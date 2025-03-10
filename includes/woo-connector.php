<?php
/**
 * WooCommerce Connector
 * 
 * Handles all communication between the PIM system and WooCommerce
 */
class WooConnector {
    private $api_url;
    private $consumer_key;
    private $consumer_secret;
    private $db;
    private $logger;

    /**
     * Constructor
     * 
     * @param PDO $db Database connection
     * @param Logger $logger Logger instance
     */
    public function __construct($db, $logger) {
        $this->db = $db;
        $this->logger = $logger;

        // Load API credentials from settings
        $this->loadCredentials();
    }

    /**
     * Load WooCommerce API credentials from database
     */
    private function loadCredentials() {
        $stmt = $this->db->prepare("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('woocommerce_api_url', 'woocommerce_consumer_key', 'woocommerce_consumer_secret')");
        $stmt->execute();
        
        $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        $this->api_url = $settings['woocommerce_api_url'] ?? '';
        $this->consumer_key = $settings['woocommerce_consumer_key'] ?? '';
        $this->consumer_secret = $settings['woocommerce_consumer_secret'] ?? '';
    }

    /**
     * Test API connection
     * 
     * @return bool Connection status
     */
    public function testConnection() {
        try {
            $response = $this->makeRequest('GET', 'products', ['per_page' => 1]);
            return isset($response) && !isset($response->error);
        } catch (Exception $e) {
            $this->logger->error('WooCommerce API connection test failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Import products from WooCommerce
     * 
     * @param int $page Page number
     * @param int $per_page Items per page
     * @param int $job_id Sync job ID
     * @return array Import results
     */
    public function importProducts($page = 1, $per_page = 10, $job_id = null) {
        $results = [
            'total' => 0,
            'imported' => 0,
            'updated' => 0,
            'failed' => 0,
            'errors' => []
        ];

        try {
            // Create a new sync job if not provided
            if ($job_id === null) {
                $job_id = $this->createSyncJob('import');
            }

            // Update job status
            $this->updateSyncJob($job_id, 'in_progress');

            // Fetch products from WooCommerce
            $params = [
                'page' => $page,
                'per_page' => $per_page,
                'status' => 'publish,draft',
                'orderby' => 'id',
                'order' => 'asc'
            ];

            $products = $this->makeRequest('GET', 'products', $params);
            
            if (!is_array($products)) {
                throw new Exception('Invalid response from WooCommerce API');
            }

            $results['total'] = count($products);

            // Get total from headers if available
            $totalProducts = $this->getHeaderTotal();
            if ($totalProducts) {
                $this->updateSyncJobTotal($job_id, $totalProducts);
            }

            // Process each product
            foreach ($products as $wooProduct) {
                try {
                    $result = $this->processProduct($wooProduct, $job_id);
                    
                    if ($result === 'imported') {
                        $results['imported']++;
                    } elseif ($result === 'updated') {
                        $results['updated']++;
                    }
                } catch (Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = "Product ID {$wooProduct->id}: " . $e->getMessage();
                    
                    // Log the error in sync_job_items
                    $this->logSyncItemError($job_id, $wooProduct->id, $e->getMessage());
                }
            }

            // Update job progress
            $this->updateSyncJobProgress($job_id, $results['imported'] + $results['updated'], $results['failed']);

            // If this is the last page or no products returned, complete the job
            if (count($products) < $per_page || !$totalProducts || ($page * $per_page) >= $totalProducts) {
                $this->completeSyncJob($job_id);
            }

            return $results;
        } catch (Exception $e) {
            $this->logger->error('Import failed: ' . $e->getMessage());
            $this->failSyncJob($job_id, $e->getMessage());
            
            $results['failed'] = $results['total'];
            $results['errors'][] = $e->getMessage();
            
            return $results;
        }
    }

    /**
     * Process a single product from WooCommerce
     * 
     * @param object $wooProduct WooCommerce product object
     * @param int $job_id Sync job ID
     * @return string Result status ('imported', 'updated', or 'failed')
     */
    private function processProduct($wooProduct, $job_id) {
        // Begin transaction
        $this->db->beginTransaction();
        
        try {
            // Check if product already exists
            $stmt = $this->db->prepare("SELECT product_id FROM products WHERE woo_product_id = ?");
            $stmt->execute([$wooProduct->id]);
            $existingProduct = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existingProduct) {
                // Update existing product
                $productId = $existingProduct['product_id'];
                $this->updateProduct($productId, $wooProduct);
                $result = 'updated';
            } else {
                // Create new product
                $productId = $this->createProduct($wooProduct);
                $result = 'imported';
            }
            
            // Process categories
            $this->processCategories($productId, $wooProduct->categories ?? []);
            
            // Process attributes
            $this->processAttributes($productId, $wooProduct->attributes ?? []);
            
            // Process images
            $this->processImages($productId, $wooProduct->images ?? []);
            
            // Process variations if any
            if (isset($wooProduct->variations) && !empty($wooProduct->variations)) {
                $this->processVariations($productId, $wooProduct->variations, $wooProduct->id);
            }
            
            // Create SEO record
            $this->processSeoData($productId, $wooProduct);
            
            // Calculate rating score
            $this->calculateRatingScore($productId);
            
            // Add to sync job items
            $this->addSyncJobItem($job_id, $productId, 'succeeded');
            
            // Commit transaction
            $this->db->commit();
            
            return $result;
        } catch (Exception $e) {
            // Rollback transaction
            $this->db->rollBack();
            
            // Add to sync job items with error
            if (isset($productId)) {
                $this->addSyncJobItem($job_id, $productId, 'failed', $e->getMessage());
            }
            
            throw $e;
        }
    }

    /**
     * Create a new product record
     * 
     * @param object $wooProduct WooCommerce product object
     * @return int New product ID
     */
    private function createProduct($wooProduct) {
        $stmt = $this->db->prepare("
            INSERT INTO products (
                woo_product_id, name, sku, type, status, description, short_description,
                regular_price, sale_price, tax_status, tax_class, stock_quantity,
                stock_status, weight, dimensions, shipping_class, visibility,
                meta_data, created_at, updated_at, last_synced
            ) VALUES (
                :woo_id, :name, :sku, :type, :status, :description, :short_description,
                :regular_price, :sale_price, :tax_status, :tax_class, :stock_quantity,
                :stock_status, :weight, :dimensions, :shipping_class, :visibility,
                :meta_data, :created_at, :updated_at, NOW()
            )
        ");
        
        // Convert WooCommerce statuses to PIM statuses
        $status = 'draft';
        if ($wooProduct->status === 'publish') {
            $status = 'published';
        } elseif ($wooProduct->status === 'trash') {
            $status = 'archived';
        }
        
        // Format dimensions as JSON
        $dimensions = json_encode([
            'length' => $wooProduct->dimensions->length ?? '',
            'width' => $wooProduct->dimensions->width ?? '',
            'height' => $wooProduct->dimensions->height ?? ''
        ]);
        
        // Sanitize and prepare meta data
        $metaData = [];
        if (isset($wooProduct->meta_data) && is_array($wooProduct->meta_data)) {
            foreach ($wooProduct->meta_data as $meta) {
                $metaData[$meta->key] = $meta->value;
            }
        }
        
        $stmt->bindParam(':woo_id', $wooProduct->id);
        $stmt->bindParam(':name', $wooProduct->name);
        $stmt->bindParam(':sku', $wooProduct->sku);
        $stmt->bindParam(':type', $wooProduct->type);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':description', $wooProduct->description);
        $stmt->bindParam(':short_description', $wooProduct->short_description);
        $stmt->bindParam(':regular_price', $wooProduct->regular_price);
        $stmt->bindParam(':sale_price', $wooProduct->sale_price);
        $stmt->bindParam(':tax_status', $wooProduct->tax_status);
        $stmt->bindParam(':tax_class', $wooProduct->tax_class);
        $stmt->bindParam(':stock_quantity', $wooProduct->stock_quantity);
        $stmt->bindParam(':stock_status', $wooProduct->stock_status);
        $stmt->bindParam(':weight', $wooProduct->weight);
        $stmt->bindParam(':dimensions', $dimensions);
        $stmt->bindParam(':shipping_class', $wooProduct->shipping_class);
        $stmt->bindParam(':visibility', $wooProduct->catalog_visibility);
        $stmt->bindParam(':meta_data', json_encode($metaData));
        $stmt->bindParam(':created_at', $wooProduct->date_created);
        $stmt->bindParam(':updated_at', $wooProduct->date_modified);
        
        $stmt->execute();
        
        return $this->db->lastInsertId();
    }

    /**
     * Update an existing product record
     * 
     * @param int $productId PIM product ID
     * @param object $wooProduct WooCommerce product object
     */
    private function updateProduct($productId, $wooProduct) {
        // Implementation similar to createProduct but with UPDATE query
        // ...
    }

    /**
     * Process product categories
     * 
     * @param int $productId PIM product ID
     * @param array $categories WooCommerce categories array
     */
    private function processCategories($productId, $categories) {
        // Delete existing category relationships
        $stmt = $this->db->prepare("DELETE FROM product_categories WHERE product_id = ?");
        $stmt->execute([$productId]);
        
        // No categories to process
        if (empty($categories)) {
            return;
        }
        
        foreach ($categories as $category) {
            // Check if category exists in our database
            $stmt = $this->db->prepare("SELECT category_id FROM categories WHERE woo_category_id = ?");
            $stmt->execute([$category->id]);
            $existingCategory = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $categoryId = null;
            
            if ($existingCategory) {
                $categoryId = $existingCategory['category_id'];
                
                // Update category details
                $updateStmt = $this->db->prepare("
                    UPDATE categories 
                    SET name = ?, slug = ?, description = ?
                    WHERE category_id = ?
                ");
                $updateStmt->execute([
                    $category->name,
                    $category->slug,
                    $category->description ?? '',
                    $categoryId
                ]);
            } else {
                // Create new category
                $insertStmt = $this->db->prepare("
                    INSERT INTO categories (woo_category_id, name, slug, description)
                    VALUES (?, ?, ?, ?)
                ");
                $insertStmt->execute([
                    $category->id,
                    $category->name,
                    $category->slug,
                    $category->description ?? ''
                ]);
                
                $categoryId = $this->db->lastInsertId();
            }
            
            // Create product-category relationship
            $relStmt = $this->db->prepare("
                INSERT INTO product_categories (product_id, category_id)
                VALUES (?, ?)
            ");
            $relStmt->execute([$productId, $categoryId]);
        }
    }

    /**
     * Process product attributes
     * 
     * @param int $productId PIM product ID
     * @param array $attributes WooCommerce attributes array
     */
    private function processAttributes($productId, $attributes) {
        // Implementation to handle attributes
        // ...
    }

    /**
     * Process product images
     * 
     * @param int $productId PIM product ID
     * @param array $images WooCommerce images array
     */
    private function processImages($productId, $images) {
        // Implementation to handle images
        // ...
    }

    /**
     * Process product variations
     * 
     * @param int $productId PIM product ID
     * @param array $variationIds WooCommerce variation IDs
     * @param int $wooProductId WooCommerce parent product ID
     */
    private function processVariations($productId, $variationIds, $wooProductId) {
        // Implementation to handle variations
        // ...
    }

    /**
     * Process SEO data for a product
     * 
     * @param int $productId PIM product ID
     * @param object $wooProduct WooCommerce product object
     */
    private function processSeoData($productId, $wooProduct) {
        // Extract SEO data from product meta or Yoast SEO metadata
        $metaTitle = '';
        $metaDescription = '';
        $metaKeywords = '';
        $focusKeyword = '';
        $canonicalUrl = '';
        
        // Check for Yoast SEO metadata
        if (isset($wooProduct->meta_data) && is_array($wooProduct->meta_data)) {
            foreach ($wooProduct->meta_data as $meta) {
                if ($meta->key === '_yoast_wpseo_title') {
                    $metaTitle = $meta->value;
                } elseif ($meta->key === '_yoast_wpseo_keywordsynonyms') {
                    $metaKeywords = $meta->value;
                }
            }
        }
        
        // Check if SEO record already exists
        $stmt = $this->db->prepare("SELECT seo_id FROM product_seo WHERE product_id = ?");
        $stmt->execute([$productId]);
        $existingSeo = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingSeo) {
            // Update existing SEO record
            $updateStmt = $this->db->prepare("
                UPDATE product_seo
                SET meta_title = ?, meta_description = ?, meta_keywords = ?,
                    focus_keyword = ?, canonical_url = ?, updated_at = NOW()
                WHERE product_id = ?
            ");
            $updateStmt->execute([
                $metaTitle,
                $metaDescription,
                $metaKeywords,
                $focusKeyword,
                $canonicalUrl,
                $productId
            ]);
        } else {
            // Create new SEO record
            $insertStmt = $this->db->prepare("
                INSERT INTO product_seo (
                    product_id, meta_title, meta_description, meta_keywords,
                    focus_keyword, canonical_url
                ) VALUES (?, ?, ?, ?, ?, ?)
            ");
            $insertStmt->execute([
                $productId,
                $metaTitle,
                $metaDescription,
                $metaKeywords,
                $focusKeyword,
                $canonicalUrl
            ]);
        }
    }

    /**
     * Calculate and update product rating score
     * 
     * @param int $productId PIM product ID
     */
    private function calculateRatingScore($productId) {
        // Get all rating criteria
        $stmt = $this->db->prepare("
            SELECT criteria_id, criteria_name, weight
            FROM rating_criteria
            WHERE is_active = 1
        ");
        $stmt->execute();
        $criteria = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $totalScore = 0;
        $totalWeight = 0;
        
        // Get product data
        $stmt = $this->db->prepare("
            SELECT p.*, ps.meta_title, ps.meta_description, COUNT(pi.image_id) AS image_count
            FROM products p
            LEFT JOIN product_seo ps ON p.product_id = ps.product_id
            LEFT JOIN product_images pi ON p.product_id = pi.product_id
            WHERE p.product_id = ?
            GROUP BY p.product_id
        ");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$product) {
            return;
        }
        
        // Delete existing rating details
        $deleteStmt = $this->db->prepare("DELETE FROM product_ratings WHERE product_id = ?");
        $deleteStmt->execute([$productId]);
        
        // Evaluate each criteria and insert new ratings
        foreach ($criteria as $criterion) {
            $score = 0;
            $suggestions = [];
            
            switch ($criterion['criteria_name']) {
                case 'Basic Information':
                    // Check for basic product info
                    $score = $this->evaluateBasicInfo($product, $suggestions);
                    break;
                    
                case 'Description':
                    // Check description quality
                    $score = $this->evaluateDescription($product, $suggestions);
                    break;
                    
                case 'Images':
                    // Check image quantity and quality
                    $score = $this->evaluateImages($product, $productId, $suggestions);
                    break;
                    
                case 'SEO Elements':
                    // Check SEO elements
                    $score = $this->evaluateSeo($product, $suggestions);
                    break;
                    
                case 'Attributes':
                    // Check product attributes
                    $score = $this->evaluateAttributes($productId, $suggestions);
                    break;
                    
                case 'Categories':
                    // Check category assignment
                    $score = $this->evaluateCategories($productId, $suggestions);
                    break;
            }
            
            // Insert rating detail
            $insertStmt = $this->db->prepare("
                INSERT INTO product_ratings (
                    product_id, criteria_id, score, suggestions, evaluated_at
                ) VALUES (?, ?, ?, ?, NOW())
            ");
            $insertStmt->execute([
                $productId,
                $criterion['criteria_id'],
                $score,
                !empty($suggestions) ? implode('; ', $suggestions) : null
            ]);
            
            $totalScore += ($score * $criterion['weight']);
            $totalWeight += $criterion['weight'];
        }
        
        // Calculate final weighted score (0-100%)
        $finalScore = ($totalWeight > 0) ? ($totalScore / $totalWeight) : 0;
        
        // Update product's overall rating score
        $updateStmt = $this->db->prepare("
            UPDATE products
            SET rating_score = ?
            WHERE product_id = ?
        ");
        $updateStmt->execute([$finalScore, $productId]);
    }
    
    /**
     * Evaluate basic product information
     * 
     * @param array $product Product data
     * @param array &$suggestions Suggestions array (by reference)
     * @return float Score (0-100)
     */
    private function evaluateBasicInfo($product, &$suggestions) {
        $score = 100;
        
        // Check essential fields
        if (empty($product['name'])) {
            $score -= 30;
            $suggestions[] = 'Product name is missing';
        }
        
        if (empty($product['sku'])) {
            $score -= 20;
            $suggestions[] = 'SKU is missing';
        }
        
        if (empty($product['regular_price'])) {
            $score -= 20;
            $suggestions[] = 'Regular price is missing';
        }
        
        if ($product['stock_quantity'] === null) {
            $score -= 10;
            $suggestions[] = 'Stock quantity is not set';
        }
        
        if (empty($product['weight']) && $product['type'] !== 'digital') {
            $score -= 10;
            $suggestions[] = 'Product weight is missing';
        }
        
        return max(0, min(100, $score));
    }
    
    /**
     * Evaluate product description
     * 
     * @param array $product Product data
     * @param array &$suggestions Suggestions array (by reference)
     * @return float Score (0-100)
     */
    private function evaluateDescription($product, &$suggestions) {
        $score = 0;
        
        // Check description exists
        if (!empty($product['description'])) {
            $descLength = strlen(strip_tags($product['description']));
            
            // Evaluate description length
            if ($descLength > 500) {
                $score += 60;
            } elseif ($descLength > 300) {
                $score += 40;
                $suggestions[] = 'Description could be more detailed (recommended: 500+ characters)';
            } elseif ($descLength > 100) {
                $score += 20;
                $suggestions[] = 'Description is too short (recommended: 500+ characters)';
            } else {
                $suggestions[] = 'Description is too short (recommended: 500+ characters)';
            }
            
            // Check for HTML formatting
            if (strpos($product['description'], '<') !== false) {
                $score += 20;
            } else {
                $suggestions[] = 'Description lacks formatting (use bullet points, headings, etc.)';
            }
            
            // Check for bullet points
            if (strpos($product['description'], '<li>') !== false) {
                $score += 20;
            } else {
                $suggestions[] = 'Add bullet points to highlight key features';
            }
        } else {
            $suggestions[] = 'Product description is missing';
        }
        
        // Check short description exists
        if (empty($product['short_description'])) {
            $suggestions[] = 'Short description is missing';
        } else {
            $shortDescLength = strlen(strip_tags($product['short_description']));
            if ($shortDescLength > 50) {
                $score += 20;
            } else {
                $suggestions[] = 'Short description is too brief (recommended: 50+ characters)';
            }
        }
        
        return max(0, min(100, $score));
    }
    
    /**
     * Evaluate product images
     * 
     * @param array $product Product data
     * @param int $productId Product ID
     * @param array &$suggestions Suggestions array (by reference)
     * @return float Score (0-100)
     */
    private function evaluateImages($product, $productId, &$suggestions) {
        // Count images
        $imageCount = $product['image_count'] ?? 0;
        
        // Check alt text on images
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total, SUM(CASE WHEN alt_text != '' AND alt_text IS NOT NULL THEN 1 ELSE 0 END) as with_alt
            FROM product_images
            WHERE product_id = ?
        ");
        $stmt->execute([$productId]);
        $imageData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $score = 0;
        
        // Score based on number of images
        if ($imageCount >= 5) {
            $score += 50;
        } elseif ($imageCount >= 3) {
            $score += 30;
            $suggestions[] = 'Add more product images (recommended: 5+)';
        } elseif ($imageCount >= 1) {
            $score += 15;
            $suggestions[] = 'Add more product images (recommended: 5+)';
        } else {
            $suggestions[] = 'Product has no images';
        }
        
        // Score based on alt text
        if ($imageData['total'] > 0) {
            $altTextPercentage = ($imageData['with_alt'] / $imageData['total']) * 100;
            
            if ($altTextPercentage >= 90) {
                $score += 50;
            } elseif ($altTextPercentage >= 50) {
                $score += 25;
                $suggestions[] = 'Add alt text to all product images';
            } else {
                $suggestions[] = 'Most images are missing alt text';
            }
        }
        
        return $score;
    }
    
    /**
     * Evaluate SEO elements
     * 
     * @param array $product Product data
     * @param array &$suggestions Suggestions array (by reference)
     * @return float Score (0-100)
     */
    private function evaluateSeo($product, &$suggestions) {
        $score = 0;
        
        // Check meta title
        if (!empty($product['meta_title'])) {
            $titleLength = strlen($product['meta_title']);
            if ($titleLength >= 40 && $titleLength <= 70) {
                $score += 25;
            } else {
                $score += 10;
                $suggestions[] = 'Meta title length should be between 40-70 characters';
            }
        } else {
            $suggestions[] = 'Meta title is missing';
        }
        
        // Check meta description
        if (!empty($product['meta_description'])) {
            $descLength = strlen($product['meta_description']);
            if ($descLength >= 120 && $descLength <= 160) {
                $score += 25;
            } else {
                $score += 10;
                $suggestions[] = 'Meta description length should be between 120-160 characters';
            }
        } else {
            $suggestions[] = 'Meta description is missing';
        }
        
        // Check focus keyword
        if (!empty($product['focus_keyword'])) {
            $score += 25;
            
            // Check if focus keyword appears in title and description
            $keywordInTitle = stripos($product['meta_title'] ?? '', $product['focus_keyword']) !== false;
            $keywordInDesc = stripos($product['meta_description'] ?? '', $product['focus_keyword']) !== false;
            
            if ($keywordInTitle && $keywordInDesc) {
                $score += 25;
            } elseif ($keywordInTitle || $keywordInDesc) {
                $score += 15;
                $suggestions[] = 'Include focus keyword in both meta title and description';
            } else {
                $suggestions[] = 'Include focus keyword in meta title and description';
            }
        } else {
            $suggestions[] = 'Focus keyword is missing';
        }
        
        return max(0, min(100, $score));
    }
    
    /**
     * Evaluate product attributes
     * 
     * @param int $productId Product ID
     * @param array &$suggestions Suggestions array (by reference)
     * @return float Score (0-100)
     */
    private function evaluateAttributes($productId, &$suggestions) {
        // Count attributes with values
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as attribute_count
            FROM product_attribute_values
            WHERE product_id = ?
        ");
        $stmt->execute([$productId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $attributeCount = $result['attribute_count'] ?? 0;
        
        if ($attributeCount >= 5) {
            return 100;
        } elseif ($attributeCount >= 3) {
            $suggestions[] = 'Add more product attributes (recommended: 5+)';
            return 70;
        } elseif ($attributeCount >= 1) {
            $suggestions[] = 'Add more product attributes (recommended: 5+)';
            return 40;
        } else {
            $suggestions[] = 'No product attributes defined';
            return 0;
        }
    }
    
    /**
     * Evaluate product categories
     * 
     * @param int $productId Product ID
     * @param array &$suggestions Suggestions array (by reference)
     * @return float Score (0-100)
     */
    private function evaluateCategories($productId, &$suggestions) {
        // Count category assignments
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as category_count
            FROM product_categories
            WHERE product_id = ?
        ");
        $stmt->execute([$productId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $categoryCount = $result['category_count'] ?? 0;
        
        if ($categoryCount > 0) {
            return 100;
        } else {
            $suggestions[] = 'Product is not assigned to any category';
            return 0;
        }
    }

    /**
     * Export products from PIM to WooCommerce
     * 
     * @param array $productIds Array of product IDs to export
     * @param int $userId User ID performing the export
     * @return array Export results
     */
    public function exportProducts($productIds, $userId = null) {
        $results = [
            'total' => count($productIds),
            'exported' => 0,
            'failed' => 0,
            'errors' => []
        ];
        
        // Create a sync job
        $jobId = $this->createSyncJob('export', $userId, count($productIds));
        
        // Update job status
        $this->updateSyncJob($jobId, 'in_progress');
        
        foreach ($productIds as $productId) {
            try {
                // Get product data
                $product = $this->getProductData($productId);
                
                if (!$product) {
                    throw new Exception("Product not found");
                }
                
                // Convert to WooCommerce format
                $wooProduct = $this->convertToWooFormat($product);
                
                // Send to WooCommerce
                if ($product['woo_product_id']) {
                    // Update existing WooCommerce product
                    $response = $this->makeRequest('PUT', "products/{$product['woo_product_id']}", [], $wooProduct);
                } else {
                    // Create new WooCommerce product
                    $response = $this->makeRequest('POST', 'products', [], $wooProduct);
                    
                    // Update woo_product_id in our database
                    if (isset($response->id)) {
                        $updateStmt = $this->db->prepare("
                            UPDATE products SET woo_product_id = ?, last_synced = NOW()
                            WHERE product_id = ?
                        ");
                        $updateStmt->execute([$response->id, $productId]);
                    }
                }
                
                // Add success to sync job items
                $this->addSyncJobItem($jobId, $productId, 'succeeded');
                
                $results['exported']++;
            } catch (Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Product ID {$productId}: " . $e->getMessage();
                
                // Add failure to sync job items
                $this->addSyncJobItem($jobId, $productId, 'failed', $e->getMessage());
            }
            
            // Update job progress
            $this->updateSyncJobProgress($jobId, $results['exported'], $results['failed']);
        }
        
        // Complete the job
        $this->completeSyncJob($jobId);
        
        return $results;
    }

    /**
     * Get product data including related entities
     * 
     * @param int $productId Product ID
     * @return array|null Product data or null if not found
     */
    private function getProductData($productId) {
        // Implementation to get complete product data
        // ...
    }

    /**
     * Convert PIM product data to WooCommerce format
     * 
     * @param array $product PIM product data
     * @return array WooCommerce formatted product data
     */
    private function convertToWooFormat($product) {
        // Implementation to convert product format
        // ...
    }

    /**
     * Make a request to the WooCommerce API
     * 
     * @param string $method HTTP method (GET, POST, PUT, DELETE)
     * @param string $endpoint API endpoint
     * @param array $params Query parameters
     * @param array $data Post data (for POST/PUT requests)
     * @return mixed Response data
     */
    private function makeRequest($method, $endpoint, $params = [], $data = null) {
        if (empty($this->api_url) || empty($this->consumer_key) || empty($this->consumer_secret)) {
            throw new Exception('WooCommerce API credentials not configured');
        }
        
        // Build URL
        $url = rtrim($this->api_url, '/') . '/wp-json/wc/v3/' . ltrim($endpoint, '/');
        
        // Add authentication
        $params['consumer_key'] = $this->consumer_key;
        $params['consumer_secret'] = $this->consumer_secret;
        
        // Add params to URL
        if (!empty($params)) {
            $url .= (strpos($url, '?') === false) ? '?' : '&';
            $url .= http_build_query($params);
        }
        
        // Initialize cURL
        $ch = curl_init();
        
        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HEADER, true);
        
        // Set appropriate HTTP method
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
        } elseif ($method !== 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }
        
        // Set data for POST/PUT requests
        if ($data && ($method === 'POST' || $method === 'PUT')) {
            $json_data = json_encode($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($json_data)
            ]);
        }
        
        // Execute request
        $response = curl_exec($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        // Extract headers and body
        $headers = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);
        
        // Close cURL
        curl_close($ch);
        
        // Store headers for later use (e.g., pagination)
        $this->lastHeaders = $this->parseHeaders($headers);
        
        // Handle response
        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($body);
        } else {
            $error = json_decode($body);
            throw new Exception(isset($error->message) ? $error->message : "API error: HTTP $httpCode");
        }
    }

    /**
     * Parse HTTP headers
     * 
     * @param string $headerString Raw header string
     * @return array Parsed headers
     */
    private function parseHeaders($headerString) {
        $headers = [];
        $headerLines = explode("\r\n", $headerString);
        
        foreach ($headerLines as $line) {
            if (strpos($line, ':') !== false) {
                list($key, $value) = explode(':', $line, 2);
                $headers[trim($key)] = trim($value);
            }
        }
        
        return $headers;
    }

    /**
     * Get total items from API headers
     * 
     * @return int|null Total items or null if not available
     */
    private function getHeaderTotal() {
        return isset($this->lastHeaders['X-WP-Total']) ? intval($this->lastHeaders['X-WP-Total']) : null;
    }

    /**
     * Create a new sync job
     * 
     * @param string $type Job type ('import', 'export', 'full_sync')
     * @param int $userId User ID (optional)
     * @param int $totalItems Total items to process (optional)
     * @return int New job ID
     */
    private function createSyncJob($type, $userId = null, $totalItems = 0) {
        $stmt = $this->db->prepare("
            INSERT INTO sync_jobs (job_type, status, items_total, user_id, created_at)
            VALUES (?, 'pending', ?, ?, NOW())
        ");
        $stmt->execute([$type, $totalItems, $userId]);
        
        return $this->db->lastInsertId();
    }

    /**
     * Update sync job status
     * 
     * @param int $jobId Job ID
     * @param string $status New status
     */
    private function updateSyncJob($jobId, $status) {
        $stmt = $this->db->prepare("
            UPDATE sync_jobs
            SET status = ?, started_at = CASE WHEN ? = 'in_progress' AND started_at IS NULL THEN NOW() ELSE started_at END
            WHERE job_id = ?
        ");
        $stmt->execute([$status, $status, $jobId]);
    }

    /**
     * Update sync job total items
     * 
     * @param int $jobId Job ID
     * @param int $total Total items
     */
    private function updateSyncJobTotal($jobId, $total) {
        $stmt = $this->db->prepare("
            UPDATE sync_jobs
            SET items_total = ?
            WHERE job_id = ?
        ");
        $stmt->execute([$total, $jobId]);
    }

    /**
     * Update sync job progress
     * 
     * @param int $jobId Job ID
     * @param int $succeeded Succeeded items count
     * @param int $failed Failed items count
     */
    private function updateSyncJobProgress($jobId, $succeeded, $failed) {
        $stmt = $this->db->prepare("
            UPDATE sync_jobs
            SET items_processed = ?, items_succeeded = ?, items_failed = ?
            WHERE job_id = ?
        ");
        $stmt->execute([$succeeded + $failed, $succeeded, $failed, $jobId]);
    }

    /**
     * Complete a sync job
     * 
     * @param int $jobId Job ID
     */
    private function completeSyncJob($jobId) {
        $stmt = $this->db->prepare("
            UPDATE sync_jobs
            SET status = 'completed', completed_at = NOW()
            WHERE job_id = ?
        ");
        $stmt->execute([$jobId]);
    }

    /**
     * Mark a sync job as failed
     * 
     * @param int $jobId Job ID
     * @param string $error Error message
     */
    private function failSyncJob($jobId, $error) {
        $stmt = $this->db->prepare("
            UPDATE sync_jobs
            SET status = 'failed', completed_at = NOW(), log = ?
            WHERE job_id = ?
        ");
        $stmt->execute([$error, $jobId]);
    }

    /**
     * Add an item to a sync job
     * 
     * @param int $jobId Job ID
     * @param int $productId Product ID
     * @param string $status Item status
     * @param string $error Error message (optional)
     */
    private function addSyncJobItem($jobId, $productId, $status, $error = null) {
        $stmt = $this->db->prepare("
            INSERT INTO sync_job_items (job_id, product_id, status, error_message)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$jobId, $productId, $status, $error]);
    }

    /**
     * Log an error for a sync item
     * 
     * @param int $jobId Job ID
     * @param int $wooProductId WooCommerce product ID
     * @param string $error Error message
     */
    private function logSyncItemError($jobId, $wooProductId, $error) {
        // Get product ID if it exists
        $stmt = $this->db->prepare("SELECT product_id FROM products WHERE woo_product_id = ?");
        $stmt->execute([$wooProductId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($product) {
            $this->addSyncJobItem($jobId, $product['product_id'], 'failed', $error);
        }
    }
} === '_yoast_wpseo_metadesc') {
                    $metaDescription = $meta->value;
                } elseif ($meta->key === '_yoast_wpseo_focuskw') {
                    $focusKeyword = $meta->value;
                } elseif ($meta->key === '_yoast_wpseo_canonical') {
                    $canonicalUrl = $meta->value;
                } elseif ($meta->key