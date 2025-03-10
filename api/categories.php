<?php
/**
 * Categories API Endpoint
 * Handles category data retrieval and management
 */

// Include required files
require_once '../includes/db-connect.php';
require_once '../includes/logger.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Initialize components
try {
    // Get database connection
    $db = Database::getInstance();
    
    // Initialize logger
    $logger = new Logger();
    
    // Determine the request method
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch ($method) {
        case 'GET':
            // Handle GET request (retrieve categories)
            handleGetRequest($db, $logger);
            break;
            
        case 'POST':
            // Handle POST request (create category)
            handlePostRequest($db, $logger);
            break;
            
        case 'PUT':
            // Handle PUT request (update category)
            handlePutRequest($db, $logger);
            break;
            
        case 'DELETE':
            // Handle DELETE request (delete category)
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
        $logger->error('Categories API error: ' . $e->getMessage());
    }
    
    // Return error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}

/**
 * Handle GET request to retrieve categories
 * 
 * @param PDO $db Database connection
 * @param Logger $logger Logger instance
 */
function handleGetRequest($db, $logger) {
    // Check if a specific category ID is requested
    $categoryId = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($categoryId > 0) {
        // Get specific category
        $category = getCategory($db, $categoryId);
        
        if (!$category) {
            // Category not found
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Category not found'
            ]);
            return;
        }
        
        // Return the category
        echo json_encode([
            'success' => true,
            'data' => $category
        ]);
    } else {
        // Get all categories
        $categories = getAllCategories($db);
        
        // Return categories
        echo json_encode([
            'success' => true,
            'data' => $categories
        ]);
    }
}

/**
 * Handle POST request to create a new category
 * 
 * @param PDO $db Database connection
 * @param Logger $logger Logger instance
 */
function handlePostRequest($db, $logger) {
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
            'message' => 'Category name is required'
        ]);
        return;
    }
    
    // Generate slug if not provided
    if (empty($data['slug'])) {
        $data['slug'] = createSlug($data['name']);
    }
    
    try {
        // Begin transaction
        $db->beginTransaction();
        
        // Create new category
        $categoryId = createCategory($db, $data);
        
        // Commit transaction
        $db->commit();
        
        // Return success response
        echo json_encode([
            'success' => true,
            'message' => 'Category created successfully',
            'data' => [
                'category_id' => $categoryId,
                'name' => $data['name']
            ]
        ]);
    } catch (Exception $e) {
        // Rollback transaction
        $db->rollBack();
        
        // Log error
        $logger->error('Error creating category: ' . $e->getMessage());
        
        // Return error response
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error creating category: ' . $e->getMessage()
        ]);
    }
}

/**
 * Handle PUT request to update an existing category
 * 
 * @param PDO $db Database connection
 * @param Logger $logger Logger instance
 */
function handlePutRequest($db, $logger) {
    // Get category ID from query string
    $categoryId = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($categoryId <= 0) {
        // Invalid category ID
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid category ID'
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
    
    // Check if category exists
    $existingCategory = getCategory($db, $categoryId);
    
    if (!$existingCategory) {
        // Category not found
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Category not found'
        ]);
        return;
    }
    
    // Generate slug if name is changed and slug is not provided
    if (isset($data['name']) && $data['name'] !== $existingCategory['name'] && !isset($data['slug'])) {
        $data['slug'] = createSlug($data['name']);
    }
    
    try {
        // Begin transaction
        $db->beginTransaction();
        
        // Update category
        updateCategory($db, $categoryId, $data);
        
        // Commit transaction
        $db->commit();
        
        // Return success response
        echo json_encode([
            'success' => true,
            'message' => 'Category updated successfully'
        ]);
    } catch (Exception $e) {
        // Rollback transaction
        $db->rollBack();
        
        // Log error
        $logger->error('Error updating category: ' . $e->getMessage());
        
        // Return error response
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error updating category: ' . $e->getMessage()
        ]);
    }
}

/**
 * Handle DELETE request to delete a category
 * 
 * @param PDO $db Database connection
 * @param Logger $logger Logger instance
 */
function handleDeleteRequest($db, $logger) {
    // Get category ID from query string
    $categoryId = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($categoryId <= 0) {
        // Invalid category ID
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid category ID'
        ]);
        return;
    }
    
    // Check if category exists
    $existingCategory = getCategory($db, $categoryId);
    
    if (!$existingCategory) {
        // Category not found
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Category not found'
        ]);
        return;
    }
    
    try {
        // Begin transaction
        $db->beginTransaction();
        
        // Check if there are child categories
        $stmt = $db->prepare("SELECT COUNT(*) FROM categories WHERE parent_id = ?");
        $stmt->execute([$categoryId]);
        $childCount = $stmt->fetchColumn();
        
        if ($childCount > 0) {
            throw new Exception('Cannot delete category with child categories. Please delete or move the child categories first.');
        }
        
        // Check if there are associated products
        $stmt = $db->prepare("SELECT COUNT(*) FROM product_categories WHERE category_id = ?");
        $stmt->execute([$categoryId]);
        $productCount = $stmt->fetchColumn();
        
        if ($productCount > 0) {
            throw new Exception('Cannot delete category with associated products. Please remove the category from all products first.');
        }
        
        // Delete category
        $stmt = $db->prepare("DELETE FROM categories WHERE category_id = ?");
        $stmt->execute([$categoryId]);
        
        // Commit transaction
        $db->commit();
        
        // Return success response
        echo json_encode([
            'success' => true,
            'message' => 'Category deleted successfully'
        ]);
    } catch (Exception $e) {
        // Rollback transaction
        $db->rollBack();
        
        // Log error
        $logger->error('Error deleting category: ' . $e->getMessage());
        
        // Return error response
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error deleting category: ' . $e->getMessage()
        ]);
    }
}

/**
 * Get a specific category
 * 
 * @param PDO $db Database connection
 * @param int $categoryId Category ID
 * @return array|null Category data or null if not found
 */
function getCategory($db, $categoryId) {
    $stmt = $db->prepare("
        SELECT 
            c.category_id, c.woo_category_id, c.name, c.slug,
            c.parent_id, c.description, c.image_id, c.created_at, c.updated_at,
            p.name as parent_name,
            COUNT(pc.product_id) as product_count
        FROM categories c
        LEFT JOIN categories p ON c.parent_id = p.category_id
        LEFT JOIN product_categories pc ON c.category_id = pc.category_id
        WHERE c.category_id = ?
        GROUP BY c.category_id
    ");
    $stmt->execute([$categoryId]);
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Get all categories
 * 
 * @param PDO $db Database connection
 * @return array Categories data
 */
function getAllCategories($db) {
    $stmt = $db->prepare("
        SELECT 
            c.category_id, c.woo_category_id, c.name, c.slug,
            c.parent_id, c.description, c.image_id, c.created_at, c.updated_at,
            COUNT(pc.product_id) as product_count
        FROM categories c
        LEFT JOIN product_categories pc ON c.category_id = pc.category_id
        GROUP BY c.category_id
        ORDER BY c.name ASC
    ");
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Create a new category
 * 
 * @param PDO $db Database connection
 * @param array $data Category data
 * @return int New category ID
 */
function createCategory($db, $data) {
    $stmt = $db->prepare("
        INSERT INTO categories (
            name, slug, parent_id, description, image_id, created_at, updated_at
        ) VALUES (
            :name, :slug, :parent_id, :description, :image_id, NOW(), NOW()
        )
    ");
    
    $stmt->bindParam(':name', $data['name']);
    $stmt->bindParam(':slug', $data['slug']);
    $stmt->bindParam(':parent_id', $data['parent_id'], PDO::PARAM_INT);
    $stmt->bindParam(':description', $data['description']);
    $stmt->bindParam(':image_id', $data['image_id'], PDO::PARAM_INT);
    
    $stmt->execute();
    
    return $db->lastInsertId();
}

/**
 * Update an existing category
 * 
 * @param PDO $db Database connection
 * @param int $categoryId Category ID
 * @param array $data Category data
 */
function updateCategory($db, $categoryId, $data) {
    // Build update query with only provided fields
    $updateFields = [];
    $params = [':category_id' => $categoryId];
    
    // Add fields to update if provided
    if (isset($data['name'])) {
        $updateFields[] = "name = :name";
        $params[':name'] = $data['name'];
    }
    
    if (isset($data['slug'])) {
        $updateFields[] = "slug = :slug";
        $params[':slug'] = $data['slug'];
    }
    
    if (isset($data['parent_id'])) {
        $updateFields[] = "parent_id = :parent_id";
        $params[':parent_id'] = $data['parent_id'];
    }
    
    if (isset($data['description'])) {
        $updateFields[] = "description = :description";
        $params[':description'] = $data['description'];
    }
    
    if (isset($data['image_id'])) {
        $updateFields[] = "image_id = :image_id";
        $params[':image_id'] = $data['image_id'];
    }
    
    if (isset($data['woo_category_id'])) {
        $updateFields[] = "woo_category_id = :woo_category_id";
        $params[':woo_category_id'] = $data['woo_category_id'];
    }
    
    // Always update the updated_at timestamp
    $updateFields[] = "updated_at = NOW()";
    
    // If no fields to update, return
    if (empty($updateFields)) {
        return;
    }
    
    // Build and execute the query
    $query = "UPDATE categories SET " . implode(", ", $updateFields) . " WHERE category_id = :category_id";
    $stmt = $db->prepare($query);
    $stmt->execute($params);
}

/**
 * Create a slug from a string
 * 
 * @param string $string String to convert to slug
 * @return string Slug
 */
function createSlug($string) {
    // Convert to lowercase
    $slug = strtolower($string);
    
    // Replace non-alphanumeric characters with hyphens
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    
    // Remove leading and trailing hyphens
    $slug = trim($slug, '-');
    
    return $slug;
}