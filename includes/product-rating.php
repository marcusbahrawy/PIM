<?php
/**
 * Product Rating System
 * 
 * Evaluates product completeness and SEO-friendliness on a 0-100% scale
 */
class ProductRatingSystem {
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
    }
    
    /**
     * Calculate rating score for a single product
     * 
     * @param int $productId Product ID
     * @return float Calculated score (0-100)
     */
    public function calculateScore($productId) {
        try {
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
            
            // Get product data with joined tables for complete evaluation
            $stmt = $this->db->prepare("
                SELECT 
                    p.*,
                    ps.meta_title, ps.meta_description, ps.meta_keywords, ps.focus_keyword,
                    COUNT(DISTINCT pi.image_id) AS image_count,
                    COUNT(DISTINCT pc.category_id) AS category_count,
                    COUNT(DISTINCT pav.attribute_id) AS attribute_count
                FROM products p
                LEFT JOIN product_seo ps ON p.product_id = ps.product_id
                LEFT JOIN product_images pi ON p.product_id = pi.product_id
                LEFT JOIN product_categories pc ON p.product_id = pc.product_id
                LEFT JOIN product_attribute_values pav ON p.product_id = pav.product_id
                WHERE p.product_id = ?
                GROUP BY p.product_id
            ");
            $stmt->execute([$productId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$product) {
                throw new Exception("Product not found");
            }
            
            // Delete existing rating details
            $deleteStmt = $this->db->prepare("DELETE FROM product_ratings WHERE product_id = ?");
            $deleteStmt->execute([$productId]);
            
            // Evaluate each criterion
            foreach ($criteria as $criterion) {
                $score = 0;
                $suggestions = [];
                
                switch ($criterion['criteria_name']) {
                    case 'Basic Information':
                        $score = $this->evaluateBasicInfo($product, $suggestions);
                        break;
                        
                    case 'Description':
                        $score = $this->evaluateDescription($product, $suggestions);
                        break;
                        
                    case 'Images':
                        $score = $this->evaluateImages($product, $productId, $suggestions);
                        break;
                        
                    case 'SEO Elements':
                        $score = $this->evaluateSeo($product, $suggestions);
                        break;
                        
                    case 'Attributes':
                        $score = $this->evaluateAttributes($product, $suggestions);
                        break;
                        
                    case 'Categories':
                        $score = $this->evaluateCategories($product, $suggestions);
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
            
            return $finalScore;
        } catch (Exception $e) {
            $this->logger->error('Error calculating product rating: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Calculate rating scores for multiple products
     * 
     * @param array $productIds Array of product IDs
     * @return array Results with scores per product
     */
    public function calculateBatchScores($productIds) {
        $results = [
            'total' => count($productIds),
            'processed' => 0,
            'failed' => 0,
            'scores' => []
        ];
        
        foreach ($productIds as $productId) {
            try {
                $score = $this->calculateScore($productId);
                $results['processed']++;
                $results['scores'][$productId] = $score;
            } catch (Exception $e) {
                $results['failed']++;
                $this->logger->error("Failed to calculate score for product ID $productId: " . $e->getMessage());
            }
        }
        
        return $results;
    }
    
    /**
     * Get score breakdown for a product
     * 
     * @param int $productId Product ID
     * @return array Score breakdown by criteria
     */
    public function getScoreBreakdown($productId) {
        $stmt = $this->db->prepare("
            SELECT 
                pr.criteria_id,
                rc.criteria_name,
                rc.weight,
                pr.score,
                pr.suggestions,
                pr.evaluated_at
            FROM product_ratings pr
            JOIN rating_criteria rc ON pr.criteria_id = rc.criteria_id
            WHERE pr.product_id = ?
            ORDER BY rc.weight DESC
        ");
        $stmt->execute([$productId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get suggestions to improve product score
     * 
     * @param int $productId Product ID
     * @return array Suggestions grouped by criteria
     */
    public function getImprovementSuggestions($productId) {
        $stmt = $this->db->prepare("
            SELECT 
                rc.criteria_name,
                pr.score,
                pr.suggestions
            FROM product_ratings pr
            JOIN rating_criteria rc ON pr.criteria_id = rc.criteria_id
            WHERE pr.product_id = ? AND pr.suggestions IS NOT NULL
            ORDER BY pr.score ASC
        ");
        $stmt->execute([$productId]);
        
        $suggestions = [];
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($results as $row) {
            if (!empty($row['suggestions'])) {
                $criteriaName = $row['criteria_name'];
                $score = $row['score'];
                $suggestionItems = explode(';', $row['suggestions']);
                
                $suggestions[$criteriaName] = [
                    'score' => $score,
                    'items' => array_map('trim', $suggestionItems)
                ];
            }
        }
        
        return $suggestions;
    }
    
    /**
     * Get color code for a score
     * 
     * @param float $score Score value
     * @return string Color code (red, yellow, green)
     */
    public function getScoreColor($score) {
        if ($score < 50) {
            return 'red';
        } elseif ($score < 80) {
            return 'yellow';
        } else {
            return 'green';
        }
    }
    
    /**
     * Get products with lowest scores
     * 
     * @param int $limit Number of products to return
     * @return array Low-scoring products
     */
    public function getLowScoringProducts($limit = 10) {
        $stmt = $this->db->prepare("
            SELECT 
                p.product_id,
                p.name,
                p.sku,
                p.rating_score
            FROM products p
            WHERE p.status != 'archived'
            ORDER BY p.rating_score ASC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
     * @param array $product Product data
     * @param array &$suggestions Suggestions array (by reference)
     * @return float Score (0-100)
     */
    private function evaluateAttributes($product, &$suggestions) {
        $attributeCount = $product['attribute_count'] ?? 0;
        
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
     * @param array $product Product data
     * @param array &$suggestions Suggestions array (by reference)
     * @return float Score (0-100)
     */
    private function evaluateCategories($product, &$suggestions) {
        $categoryCount = $product['category_count'] ?? 0;
        
        if ($categoryCount > 0) {
            return 100;
        } else {
            $suggestions[] = 'Product is not assigned to any category';
            return 0;
        }
    }
}