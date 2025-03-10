<?php
/**
 * Attributes API Endpoint
 * Handles attribute data retrieval and management
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
            // Handle GET request (retrieve attributes)
            handleGetRequest($db, $logger);
            break;
            
        case 'POST':
            // Handle POST request (create attribute)
            handlePostRequest($db, $logger);
            break;
            
        case 'PUT':
            // Handle PUT request (update attribute)
            handlePutRequest($db, $logger);
            break;
            
        case 'DELETE':
            // Handle DELETE request (delete attribute)
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
        $logger->error('Attributes API error: ' . $e->getMessage());
    }
    
    // Return error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}

/**
 * Handle GET request to retrieve attributes
 * 
 * @param PDO $db Database connection
 * @param Logger $logger Logger instance
 */
function handleGetRequest($db, $logger) {
    // Check if a specific attribute ID is requested
    $attributeId = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($attributeId > 0) {
        // Get specific attribute
        $attribute = getAttribute($db, $attributeId);
        
        if (!$attribute) {
            // Attribute not found
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Attribute not found'
            ]);
            return;
        }
        
        // Get attribute values
        $attribute['values'] = getAttributeValues($db, $attributeId);
        
        // Return the attribute
        echo json_encode([
            'success' => true,
            'data' => $attribute
        ]);
    } else {
        // Get all attributes
        $attributes = getAllAttributes($db);
        
        // Return attributes
        echo json_encode([
            'success' => true,
            'data' => $attributes
        ]);
    }
}

/**
 * Handle POST request to create a new attribute
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
    if (empty($data['attribute_name']) || empty($data['attribute_label'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Attribute name and label are required'
        ]);
        return;
    }
    
    // Set default attribute type if not provided
    if (empty($data['attribute_type'])) {
        $data['attribute_type'] = 'text';
    }
    
    try {
        // Begin transaction
        $db->beginTransaction();
        
        // Create new attribute
        $attributeId = createAttribute($db, $data);
        
        // Process attribute values if provided
        if (isset($data['values']) && is_array($data['values']) && !empty($data['values'])) {
            foreach ($data['values'] as $value) {
                addAttributeValue($db, $attributeId, $value);
            }
        }
        
        // Commit transaction
        $db->commit();
        
        // Return success response
        echo json_encode([
            'success' => true,
            'message' => 'Attribute created successfully',
            'data' => [
                'attribute_id' => $attributeId,
                'attribute_name' => $data['attribute_name'],
                'attribute_label' => $data['attribute_label']
            ]
        ]);
    } catch (Exception $e) {
        // Rollback transaction
        $db->rollBack();
        
        // Log error
        $logger->error('Error creating attribute: ' . $e->getMessage());
        
        // Return error response
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error creating attribute: ' . $e->getMessage()
        ]);
    }
}

/**
 * Handle PUT request to update an existing attribute
 * 
 * @param PDO $db Database connection
 * @param Logger $logger Logger instance
 */
function handlePutRequest($db, $logger) {
    // Get attribute ID from query string
    $attributeId = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($attributeId <= 0) {
        // Invalid attribute ID
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid attribute ID'
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
    
    // Check if attribute exists
    $existingAttribute = getAttribute($db, $attributeId);
    
    if (!$existingAttribute) {
        // Attribute not found
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Attribute not found'
        ]);
        return;
    }
    
    try {
        // Begin transaction
        $db->beginTransaction();
        
        // Update attribute
        updateAttribute($db, $attributeId, $data);
        
        // Process attribute values if provided
        if (isset($data['values'])) {
            // Delete existing values if we're replacing them
            if (isset($data['replace_values']) && $data['replace_values']) {
                // Delete existing values (not used in product attribute values)
                $stmt = $db->prepare("DELETE FROM attribute_values WHERE attribute_id = ?");
                $stmt->execute([$attributeId]);
            }
            
            // Add new values
            if (is_array($data['values']) && !empty($data['values'])) {
                foreach ($data['values'] as $value) {
                    addAttributeValue($db, $attributeId, $value);
                }
            }
        }
        
        // Commit transaction
        $db->commit();
        
        // Return success response
        echo json_encode([
            'success' => true,
            'message' => 'Attribute updated successfully'
        ]);
    } catch (Exception $e) {
        // Rollback transaction
        $db->rollBack();
        
        // Log error
        $logger->error('Error updating attribute: ' . $e->getMessage());
        
        // Return error response
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error updating attribute: ' . $e->getMessage()
        ]);
    }
}

/**
 * Handle DELETE request to delete an attribute
 * 
 * @param PDO $db Database connection
 * @param Logger $logger Logger instance
 */
function handleDeleteRequest($db, $logger) {
    // Get attribute ID from query string
    $attributeId = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($attributeId <= 0) {
        // Invalid attribute ID
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid attribute ID'
        ]);
        return;
    }
    
    // Check if attribute exists
    $existingAttribute = getAttribute($db, $attributeId);
    
    if (!$existingAttribute) {
        // Attribute not found
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Attribute not found'
        ]);
        return;
    }
    
    try {
        // Begin transaction
        $db->beginTransaction();
        
        // Check if attribute is used in products
        $stmt = $db->prepare("
            SELECT COUNT(*) FROM product_attribute_values 
            WHERE attribute_id = ?
        ");
        $stmt->execute([$attributeId]);
        $useCount = $stmt->fetchColumn();
        
        if ($useCount > 0) {
            throw new Exception('Cannot delete attribute that is used in products. Please remove the attribute from all products first.');
        }
        
        // Delete attribute values
        $stmt = $db->prepare("DELETE FROM attribute_values WHERE attribute_id = ?");
        $stmt->execute([$attributeId]);
        
        // Delete attribute
        $stmt = $db->prepare("DELETE FROM product_attributes WHERE attribute_id = ?");
        $stmt->execute([$attributeId]);
        
        // Commit transaction
        $db->commit();
        
        // Return success response
        echo json_encode([
            'success' => true,
            'message' => 'Attribute deleted successfully'
        ]);
    } catch (Exception $e) {
        // Rollback transaction
        $db->rollBack();
        
        // Log error
        $logger->error('Error deleting attribute: ' . $e->getMessage());
        
        // Return error response
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error deleting attribute: ' . $e->getMessage()
        ]);
    }
}

/**
 * Get a specific attribute
 * 
 * @param PDO $db Database connection
 * @param int $attributeId Attribute ID
 * @return array|null Attribute data or null if not found
 */
function getAttribute($db, $attributeId) {
    $stmt = $db->prepare("
        SELECT 
            attribute_id, attribute_name, attribute_label, attribute_type,
            is_visible, is_variation, created_at
        FROM product_attributes
        WHERE attribute_id = ?
    ");
    $stmt->execute([$attributeId]);
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Get all attributes
 * 
 * @param PDO $db Database connection
 * @return array Attributes data
 */
function getAllAttributes($db) {
    $stmt = $db->prepare("
        SELECT 
            attribute_id, attribute_name, attribute_label, attribute_type,
            is_visible, is_variation, created_at
        FROM product_attributes
        ORDER BY attribute_label ASC
    ");
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get attribute values for a specific attribute
 * 
 * @param PDO $db Database connection
 * @param int $attributeId Attribute ID
 * @return array Attribute values
 */
function getAttributeValues($db, $attributeId) {
    $stmt = $db->prepare("
        SELECT 
            value_id, value
        FROM attribute_values
        WHERE attribute_id = ?
        ORDER BY value
    ");
    $stmt->execute([$attributeId]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Create a new attribute
 * 
 * @param PDO $db Database connection
 * @param array $data Attribute data
 * @return int New attribute ID
 */
function createAttribute($db, $data) {
    // Convert label to name if name is not provided
    if (empty($data['attribute_name']) && !empty($data['attribute_label'])) {
        $data['attribute_name'] = strtolower(str_replace(' ', '_', $data['attribute_label']));
    }
    
    $stmt = $db->prepare("
        INSERT INTO product_attributes (
            attribute_name, attribute_label, attribute_type, is_visible, is_variation, created_at
        ) VALUES (
            :attribute_name, :attribute_label, :attribute_type, :is_visible, :is_variation, NOW()
        )
    ");
    
    $isVisible = isset($data['is_visible']) ? (bool)$data['is_visible'] : true;
    $isVariation = isset($data['is_variation']) ? (bool)$data['is_variation'] : false;
    
    $stmt->bindParam(':attribute_name', $data['attribute_name']);
    $stmt->bindParam(':attribute_label', $data['attribute_label']);
    $stmt->bindParam(':attribute_type', $data['attribute_type']);
    $stmt->bindParam(':is_visible', $isVisible, PDO::PARAM_BOOL);
    $stmt->bindParam(':is_variation', $isVariation, PDO::PARAM_BOOL);
    
    $stmt->execute();
    
    return $db->lastInsertId();
}

/**
 * Update an existing attribute
 * 
 * @param PDO $db Database connection
 * @param int $attributeId Attribute ID
 * @param array $data Attribute data
 */
function updateAttribute($db, $attributeId, $data) {
    // Build update query with only provided fields
    $updateFields = [];
    $params = [':attribute_id' => $attributeId];
    
    // Add fields to update if provided
    if (isset($data['attribute_name'])) {
        $updateFields[] = "attribute_name = :attribute_name";
        $params[':attribute_name'] = $data['attribute_name'];
    }
    
    if (isset($data['attribute_label'])) {
        $updateFields[] = "attribute_label = :attribute_label";
        $params[':attribute_label'] = $data['attribute_label'];
    }
    
    if (isset($data['attribute_type'])) {
        $updateFields[] = "attribute_type = :attribute_type";
        $params[':attribute_type'] = $data['attribute_type'];
    }
    
    if (isset($data['is_visible'])) {
        $updateFields[] = "is_visible = :is_visible";
        $params[':is_visible'] = (bool)$data['is_visible'];
    }
    
    if (isset($data['is_variation'])) {
        $updateFields[] = "is_variation = :is_variation";
        $params[':is_variation'] = (bool)$data['is_variation'];
    }
    
    // If no fields to update, return
    if (empty($updateFields)) {
        return;
    }
    
    // Build and execute the query
    $query = "UPDATE product_attributes SET " . implode(", ", $updateFields) . " WHERE attribute_id = :attribute_id";
    $stmt = $db->prepare($query);
    $stmt->execute($params);
}

/**
 * Add an attribute value
 * 
 * @param PDO $db Database connection
 * @param int $attributeId Attribute ID
 * @param string $value Attribute value
 * @return int New value ID
 */
function addAttributeValue($db, $attributeId, $value) {
    // Check if value already exists
    $stmt = $db->prepare("
        SELECT value_id FROM attribute_values
        WHERE attribute_id = ? AND value = ?
    ");
    $stmt->execute([$attributeId, $value]);
    $existingValue = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingValue) {
        return $existingValue['value_id'];
    }
    
    // Insert new value
    $stmt = $db->prepare("
        INSERT INTO attribute_values (attribute_id, value)
        VALUES (?, ?)
    ");
    $stmt->execute([$attributeId, $value]);
    
    return $db->lastInsertId();
}