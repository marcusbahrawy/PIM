<?php
/**
 * Product API Endpoint
 * Handles product data retrieval and updates
 */

// Include required files
require_once '../includes/db-connect.php';
require_once '../includes/logger.php';
require_once '../includes/product-rating.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Initialize components
try {
    // Get database connection
    $db = Database::getInstance();
    
    // Initialize logger
    $logger = new Logger();
    
    // Initialize rating system
    $ratingSystem = new ProductRatingSystem($db, $logger);
    
    // Determine the request method
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch ($method) {
        case 'GET':
            // Handle GET request (retrieve product)
            handleGetRequest($db, $logger, $ratingSystem);
            break;
            
        case 'POST':
            // Handle POST request (create product)
            handlePostRequest($db, $logger, $ratingSystem);
            break;
            
        case 'PUT':
            // Handle PUT request (update product)
            handlePutRequest($db, $logger, $ratingSystem);
            break;
            
        case 'DELETE':
            // Handle DELETE request (delete product)
            handleDeleteRequest($db, $logger);
            break;
            
        default:
            // Method not allowed
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Method not allowed'
            ]);
            break;
    }
} catch (Exception $e) {
    // Log error
    if (isset($logger)) {
        $logger->error('Product API error: ' . $e->getMessage());
    }
    
    // Return error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}

/**
 * Handle GET request to retrieve product data
 * 
 * @param PDO $db Database connection
 * @param Logger $logger Logger instance
 * @param ProductRatingSystem $ratingSystem Product rating system
 */
function handleGetRequest($db, $logger, $ratingSystem) {
    // Get product ID from query string
    $productId = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($productId <= 0) {
        // Invalid product ID
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid product ID'
        ]);
        return;
    }
    
    // Get product data
    $product = getProduct($db, $productId);
    
    if (!$product) {
        // Product not found
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Product not found'
        ]);
        return;
    }
    
    // Get product categories
    $product['categories'] = getProductCategories($db, $productId);
    
    // Get product attributes
    $product['attributes'] = getProductAttributes($db, $productId);
    
    // Get product images
    $product['images'] = getProductImages($db, $productId);
    
    // Get product SEO data
    $product['seo'] = getProductSeo($db, $productId);
    
    // Get improvement suggestions based on rating
    $product['improvement_tips'] = $ratingSystem->getImprovementSuggestions($productId);
    
    // Return success response
    echo json_encode([
        'success' => true,
        'data' => $product
    ]);
}

/**
 * Handle POST request to create a new product
 * 
 * @param PDO $db Database connection
 * @param Logger $logger Logger instance
 * @param ProductRatingSystem $ratingSystem Product rating system
 */
function handlePostRequest($db, $logger, $ratingSystem) {
    // Get JSON data from request body
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if (!$data) {
        // Invalid JSON data
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid JSON data'
        ]);
        return;
    }
    
    // Validate required fields
    if (empty($data['name'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Product name is required'
        ]);
        return;
    }
    
    try {
        // Begin transaction
        $db->beginTransaction();
        
        // Create new product
        $productId = createProduct($db, $data);
        
        // Process categories if provided
        if (isset($data['categories']) && is_array($data['categories'])) {
            updateProductCategories($db, $productId, $data['categories']);
        }
        
        // Process attributes if provided
        if (isset($data['attributes']) && is_array($data['attributes'])) {
            updateProductAttributes($db, $productId, $data['attributes']);
        }
        
        // Process images if provided
        if (isset($data['images']) && is_array($data['images'])) {
            updateProductImages($db, $productId, $data['images']);
        }
        
        // Process SEO data if provided
        if (isset($data['seo']) && is_array($data['seo'])) {
            updateProductSeo($db, $productId, $data['seo']);
        }
        
        // Calculate product rating
        $ratingSystem->calculateScore($productId);
        
        // Commit transaction
        $db->commit();
        
        // Get the created product
        $product = getProduct($db, $productId);
        
        // Return success response
        echo json_encode([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => [
                'product_id' => $productId,
                'name' => $product['name']
            ]
        ]);
    } catch (Exception $e) {
        // Rollback transaction
        $db->rollBack();
        
        // Log error
        $logger->error('Error creating product: ' . $e->getMessage());
        
        // Return error response
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error creating product: ' . $e->getMessage()
        ]);
    }
}

/**
 * Handle PUT request to update an existing product
 * 
 * @param PDO $db Database connection
 * @param Logger $logger Logger instance
 * @param ProductRatingSystem $ratingSystem Product rating system
 */
function handlePutRequest($db, $logger, $ratingSystem) {
    // Get product ID from query string
    $productId = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($productId <= 0) {
        // Invalid product ID
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid product ID'
        ]);
        return;
    }
    
    // Get JSON data from request body
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if (!$data) {
        // Invalid JSON data
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid JSON data'
        ]);
        return;
    }
    
    // Check if product exists
    $existingProduct = getProduct($db, $productId);
    
    if (!$existingProduct) {
        // Product not found
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Product not found'
        ]);
        return;
    }
    
    try {
        // Begin transaction
        $db->beginTransaction();
        
        // Update product
        updateProduct($db, $productId, $data);
        
        // Process categories if provided
        if (isset($data['categories'])) {
            updateProductCategories($db, $productId, $data['categories']);
        }
        
        // Process attributes if provided
        if (isset($data['attributes'])) {
            updateProductAttributes($db, $productId, $data['attributes']);
        }
        
        // Process images if provided
        if (isset($data['images'])) {
            updateProductImages($db, $productId, $data['images']);
        }
        
        // Process SEO data if provided
        if (isset($data['seo'])) {
            updateProductSeo($db, $productId, $data['seo']);
        }
        
        // Calculate product rating
        $ratingSystem->calculateScore($productId);
        
        // Commit transaction
        $db->commit();
        
        // Return success response
        echo json_encode([
            'success' => true,
            'message' => 'Product updated successfully'
        ]);
    } catch (Exception $e) {
        // Rollback transaction
        $db->rollBack();
        
        // Log error
        $logger->error('Error updating product: ' . $e->getMessage());
        
        // Return error response
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error updating product: ' . $e->getMessage()
        ]);
    }
}

/**
 * Handle DELETE request to delete a product
 * 
 * @param PDO $db Database connection
 * @param Logger $logger Logger instance
 */
function handleDeleteRequest($db, $logger) {
    // Get product ID from query string
    $productId = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($productId <= 0) {
        // Invalid product ID
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid product ID'
        ]);
        return;
    }
    
    // Check if product exists
    $existingProduct = getProduct($db, $productId);
    
    if (!$existingProduct) {
        // Product not found
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Product not found'
        ]);
        return;
    }
    
    try {
        // Begin transaction
        $db->beginTransaction();
        
        // Delete product related data
        deleteProductRelatedData($db, $productId);
        
        // Delete product
        $stmt = $db->prepare("DELETE FROM products WHERE product_id = ?");
        $stmt->execute([$productId]);
        
        // Commit transaction
        $db->commit();
        
        // Return success response
        echo json_encode([
            'success' => true,
            'message' => 'Product deleted successfully'
        ]);
    } catch (Exception $e) {
        // Rollback transaction
        $db->rollBack();
        
        // Log error
        $logger->error('Error deleting product: ' . $e->getMessage());
        
        // Return error response
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error deleting product: ' . $e->getMessage()
        ]);
    }
}

/**
 * Get product data
 * 
 * @param PDO $db Database connection
 * @param int $productId Product ID
 * @return array|null Product data or null if not found
 */
function getProduct($db, $productId) {
    $stmt = $db->prepare("
        SELECT * FROM products
        WHERE product_id = ?
    ");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($product) {
        // Parse JSON fields
        if (isset($product['dimensions']) && $product['dimensions']) {
            $product['dimensions'] = json_decode($product['dimensions'], true);
        } else {
            $product['dimensions'] = [
                'length' => '',
                'width' => '',
                'height' => ''
            ];
        }
        
        if (isset($product['meta_data']) && $product['meta_data']) {
            $product['meta_data'] = json_decode($product['meta_data'], true);
        } else {
            $product['meta_data'] = [];
        }
    }
    
    return $product;
}

/**
 * Get product categories
 * 
 * @param PDO $db Database connection
 * @param int $productId Product ID
 * @return array Product categories
 */
function getProductCategories($db, $productId) {
    $stmt = $db->prepare("
        SELECT c.category_id, c.name, c.slug
        FROM categories c
        JOIN product_categories pc ON c.category_id = pc.category_id
        WHERE pc.product_id = ?
    ");
    $stmt->execute([$productId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get product attributes
 * 
 * @param PDO $db Database connection
 * @param int $productId Product ID
 * @return array Product attributes
 */
function getProductAttributes($db, $productId) {
    // Get attribute metadata
    $stmt = $db->prepare("
        SELECT 
            a.attribute_id,
            a.attribute_name as name,
            a.attribute_label,
            pav.is_visible,
            pav.is_variation
        FROM product_attribute_values pav
        JOIN product_attributes a ON pav.attribute_id = a.attribute_id
        WHERE pav.product_id = ?
        GROUP BY a.attribute_id
    ");
    $stmt->execute([$productId]);
    $attributes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get attribute values for each attribute
    foreach ($attributes as &$attribute) {
        $stmt = $db->prepare("
            SELECT value
            FROM product_attribute_values
            WHERE product_id = ? AND attribute_id = ?
        ");
        $stmt->execute([$productId, $attribute['attribute_id']]);
        $values = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $attribute['values'] = $values;
    }
    
    return $attributes;
}

/**
 * Get product images
 * 
 * @param PDO $db Database connection
 * @param int $productId Product ID
 * @return array Product images
 */
function getProductImages($db, $productId) {
    $stmt = $db->prepare("
        SELECT 
            image_id,
            image_url,
            alt_text,
            title,
            position,
            is_featured
        FROM product_images
        WHERE product_id = ?
        ORDER BY is_featured DESC, position ASC
    ");
    $stmt->execute([$productId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get product SEO data
 * 
 * @param PDO $db Database connection
 * @param int $productId Product ID
 * @return array Product SEO data
 */
function getProductSeo($db, $productId) {
    $stmt = $db->prepare("
        SELECT 
            meta_title,
            meta_description,
            meta_keywords,
            focus_keyword,
            canonical_url
        FROM product_seo
        WHERE product_id = ?
    ");
    $stmt->execute([$productId]);
    $seo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$seo) {
        // Return empty SEO data if not found
        $seo = [
            'meta_title' => '',
            'meta_description' => '',
            'meta_keywords' => '',
            'focus_keyword' => '',
            'canonical_url' => ''
        ];
    }
    
    return $seo;
}

/**
 * Create a new product
 * 
 * @param PDO $db Database connection
 * @param array $data Product data
 * @return int New product ID
 */
function createProduct($db, $data) {
    // Prepare dimensions as JSON if provided
    $dimensions = json_encode([
        'length' => $data['dimensions']['length'] ?? '',
        'width' => $data['dimensions']['width'] ?? '',
        'height' => $data['dimensions']['height'] ?? ''
    ]);
    
    // Prepare meta data as JSON if provided
    $metaData = isset($data['meta_data']) ? json_encode($data['meta_data']) : null;
    
    // Create new product
    $stmt = $db->prepare("
        INSERT INTO products (
            name, sku, type, status, description, short_description,
            regular_price, sale_price, manage_stock, stock_quantity,
            stock_status, weight, dimensions, shipping_class, visibility,
            meta_data, created_at, updated_at
        ) VALUES (
            :name, :sku, :type, :status, :description, :short_description,
            :regular_price, :sale_price, :manage_stock, :stock_quantity,
            :stock_status, :weight, :dimensions, :shipping_class, :visibility,
            :meta_data, NOW(), NOW()
        )
    ");
    
    $stmt->bindParam(':name', $data['name']);
    $stmt->bindParam(':sku', $data['sku']);
    $stmt->bindParam(':type', $data['type']);
    $stmt->bindParam(':status', $data['status']);
    $stmt->bindParam(':description', $data['description']);
    $stmt->bindParam(':short_description', $data['short_description']);
    $stmt->bindParam(':regular_price', $data['regular_price']);
    $stmt->bindParam(':sale_price', $data['sale_price']);
    $stmt->bindParam(':manage_stock', $data['manage_stock'], PDO::PARAM_BOOL);
    $stmt->bindParam(':stock_quantity', $data['stock_quantity']);
    $stmt->bindParam(':stock_status', $data['stock_status']);
    $stmt->bindParam(':weight', $data['weight']);
    $stmt->bindParam(':dimensions', $dimensions);
    $stmt->bindParam(':shipping_class', $data['shipping_class']);
    $stmt->bindParam(':visibility', $data['visibility']);
    $stmt->bindParam(':meta_data', $metaData);
    
    $stmt->execute();
    
    return $db->lastInsertId();
}

/**
 * Update an existing product
 * 
 * @param PDO $db Database connection
 * @param int $productId Product ID
 * @param array $data Product data
 */
function updateProduct($db, $productId, $data) {
    // Prepare dimensions as JSON if provided
    if (isset($data['dimensions'])) {
        $dimensions = json_encode([
            'length' => $data['dimensions']['length'] ?? '',
            'width' => $data['dimensions']['width'] ?? '',
            'height' => $data['dimensions']['height'] ?? ''
        ]);
    } else {
        $dimensions = null;
    }
    
    // Prepare meta data as JSON if provided
    $metaData = isset($data['meta_data']) ? json_encode($data['meta_data']) : null;
    
    // Build update query with only provided fields
    $updateFields = [];
    $params = [':product_id' => $productId];
    
    // Add fields to update if provided
    if (isset($data['name'])) {
        $updateFields[] = "name = :name";
        $params[':name'] = $data['name'];
    }
    
    if (isset($data['sku'])) {
        $updateFields[] = "sku = :sku";
        $params[':sku'] = $data['sku'];
    }
    
    if (isset($data['type'])) {
        $updateFields[] = "type = :type";
        $params[':type'] = $data['type'];
    }
    
    if (isset($data['status'])) {
        $updateFields[] = "status = :status";
        $params[':status'] = $data['status'];
    }
    
    if (isset($data['description'])) {
        $updateFields[] = "description = :description";
        $params[':description'] = $data['description'];
    }
    
    if (isset($data['short_description'])) {
        $updateFields[] = "short_description = :short_description";
        $params[':short_description'] = $data['short_description'];
    }
    
    if (isset($data['regular_price'])) {
        $updateFields[] = "regular_price = :regular_price";
        $params[':regular_price'] = $data['regular_price'];
    }
    
    if (isset($data['sale_price'])) {
        $updateFields[] = "sale_price = :sale_price";
        $params[':sale_price'] = $data['sale_price'];
    }
    
    if (isset($data['manage_stock'])) {
        $updateFields[] = "manage_stock = :manage_stock";
        $params[':manage_stock'] = $data['manage_stock'] ? 1 : 0;
    }
    
    if (isset($data['stock_quantity'])) {
        $updateFields[] = "stock_quantity = :stock_quantity";
        $params[':stock_quantity'] = $data['stock_quantity'];
    }
    
    if (isset($data['stock_status'])) {
        $updateFields[] = "stock_status = :stock_status";
        $params[':stock_status'] = $data['stock_status'];
    }
    
    if (isset($data['weight'])) {
        $updateFields[] = "weight = :weight";
        $params[':weight'] = $data['weight'];
    }
    
    if ($dimensions !== null) {
        $updateFields[] = "dimensions = :dimensions";
        $params[':dimensions'] = $dimensions;
    }
    
    if (isset($data['shipping_class'])) {
        $updateFields[] = "shipping_class = :shipping_class";
        $params[':shipping_class'] = $data['shipping_class'];
    }
    
    if (isset($data['visibility'])) {
        $updateFields[] = "visibility = :visibility";
        $params[':visibility'] = $data['visibility'];
    }
    
    if ($metaData !== null) {
        $updateFields[] = "meta_data = :meta_data";
        $params[':meta_data'] = $metaData;
    }
    
    // Always update the updated_at timestamp
    $updateFields[] = "updated_at = NOW()";
    
    // If no fields to update, return
    if (empty($updateFields)) {
        return;
    }
    
    // Build and execute the query
    $query = "UPDATE products SET " . implode(", ", $updateFields) . " WHERE product_id = :product_id";
    $stmt = $db->prepare($query);
    $stmt->execute($params);
}

/**
 * Update product categories
 * 
 * @param PDO $db Database connection
 * @param int $productId Product ID
 * @param array $categories Categories data
 */
function updateProductCategories($db, $productId, $categories) {
    // Delete existing category relationships
    $stmt = $db->prepare("DELETE FROM product_categories WHERE product_id = ?");
    $stmt->execute([$productId]);
    
    // If no categories provided, return
    if (empty($categories)) {
        return;
    }
    
    // Insert new category relationships
    $stmt = $db->prepare("INSERT INTO product_categories (product_id, category_id) VALUES (?, ?)");
    
    foreach ($categories as $category) {
        $categoryId = is_array($category) ? $category['category_id'] : $category;
        $stmt->execute([$productId, $categoryId]);
    }
}

/**
 * Update product attributes
 * 
 * @param PDO $db Database connection
 * @param int $productId Product ID
 * @param array $attributes Attributes data
 */
function updateProductAttributes($db, $productId, $attributes) {
    // Delete existing attribute values
    $stmt = $db->prepare("DELETE FROM product_attribute_values WHERE product_id = ?");
    $stmt->execute([$productId]);
    
    // If no attributes provided, return
    if (empty($attributes)) {
        return;
    }
    
    foreach ($attributes as $attribute) {
        // Check if this is a new custom attribute or an existing one
        if (!isset($attribute['attribute_id']) && isset($attribute['name'])) {
            // Create new attribute
            $stmt = $db->prepare("
                INSERT INTO product_attributes (attribute_name, attribute_label, attribute_type)
                VALUES (?, ?, 'text')
            ");
            $stmt->execute([
                strtolower(str_replace(' ', '_', $attribute['name'])),
                $attribute['name']
            ]);
            
            $attributeId = $db->lastInsertId();
        } else {
            $attributeId = $attribute['attribute_id'];
        }
        
        // Insert attribute values
        if (isset($attribute['values']) && is_array($attribute['values'])) {
            $stmt = $db->prepare("
                INSERT INTO product_attribute_values (product_id, attribute_id, value, is_visible, is_variation)
                VALUES (?, ?, ?, ?, ?)
            ");
            
            foreach ($attribute['values'] as $value) {
                $stmt->execute([
                    $productId,
                    $attributeId,
                    $value,
                    $attribute['is_visible'] ?? true,
                    $attribute['is_variation'] ?? false
                ]);
            }
        }
    }
}

/**
 * Update product images
 * 
 * @param PDO $db Database connection
 * @param int $productId Product ID
 * @param array $images Images data
 */
function updateProductImages($db, $productId, $images) {
    // Delete existing images
    $stmt = $db->prepare("DELETE FROM product_images WHERE product_id = ?");
    $stmt->execute([$productId]);
    
    // If no images provided, return
    if (empty($images)) {
        return;
    }
    
    // Insert new images
    $stmt = $db->prepare("
        INSERT INTO product_images (
            product_id, image_url, alt_text, title, position, is_featured
        ) VALUES (
            ?, ?, ?, ?, ?, ?
        )
    ");
    
    $position = 0;
    foreach ($images as $image) {
        // Skip images without URL
        if (empty($image['image_url'])) {
            continue;
        }
        
        // In a real app, we would handle file uploads and store actual files
        // For the demo, we'll just use the provided URLs
        
        $stmt->execute([
            $productId,
            $image['image_url'],
            $image['alt_text'] ?? '',
            $image['title'] ?? '',
            $position++,
            $image['is_featured'] ?? false
        ]);
    }
}

/**
 * Update product SEO data
 * 
 * @param PDO $db Database connection
 * @param int $productId Product ID
 * @param array $seo SEO data
 */
function updateProductSeo($db, $productId, $seo) {
    // Check if SEO record exists
    $stmt = $db->prepare("SELECT seo_id FROM product_seo WHERE product_id = ?");
    $stmt->execute([$productId]);
    $exists = $stmt->fetchColumn();
    
    if ($exists) {
        // Update existing SEO record
        $stmt = $db->prepare("
            UPDATE product_seo SET
                meta_title = :meta_title,
                meta_description = :meta_description,
                meta_keywords = :meta_keywords,
                focus_keyword = :focus_keyword,
                canonical_url = :canonical_url,
                updated_at = NOW()
            WHERE product_id = :product_id
        ");
    } else {
        // Create new SEO record
        $stmt = $db->prepare("
            INSERT INTO product_seo (
                product_id, meta_title, meta_description, meta_keywords,
                focus_keyword, canonical_url
            ) VALUES (
                :product_id, :meta_title, :meta_description, :meta_keywords,
                :focus_keyword, :canonical_url
            )
        ");
    }
    
    $stmt->bindParam(':product_id', $productId);
    $stmt->bindParam(':meta_title', $seo['meta_title']);
    $stmt->bindParam(':meta_description', $seo['meta_description']);
    $stmt->bindParam(':meta_keywords', $seo['meta_keywords']);
    $stmt->bindParam(':focus_keyword', $seo['focus_keyword']);
    $stmt->bindParam(':canonical_url', $seo['canonical_url']);
    
    $stmt->execute();
}

/**
 * Delete product related data
 * 
 * @param PDO $db Database connection
 * @param int $productId Product ID
 */
function deleteProductRelatedData($db, $productId) {
    // Delete product categories
    $stmt = $db->prepare("DELETE FROM product_categories WHERE product_id = ?");
    $stmt->execute([$productId]);
    
    // Delete product attribute values
    $stmt = $db->prepare("DELETE FROM product_attribute_values WHERE product_id = ?");
    $stmt->execute([$productId]);
    
    // Delete product images
    $stmt = $db->prepare("DELETE FROM product_images WHERE product_id = ?");
    $stmt->execute([$productId]);
    
    // Delete product SEO data
    $stmt = $db->prepare("DELETE FROM product_seo WHERE product_id = ?");
    $stmt->execute([$productId]);
    
    // Delete product ratings
    $stmt = $db->prepare("DELETE FROM product_ratings WHERE product_id = ?");
    $stmt->execute([$productId]);
    
    // Delete product variations
    $stmt = $db->prepare("DELETE FROM product_variations WHERE product_id = ?");
    $stmt->execute([$productId]);
}