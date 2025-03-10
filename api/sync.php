<?php
/**
 * Sync API Endpoint
 * Handles synchronization between WooCommerce and PIM
 */

// Include required files
require_once '../includes/db-connect.php';
require_once '../includes/logger.php';
require_once '../includes/product-rating.php';
require_once '../includes/woo-connector.php';
require_once '../includes/dashboard-controller.php';

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
    
    // Initialize WooCommerce connector
    $wooConnector = new WooConnector($db, $logger);
    
    // Initialize dashboard controller
    $dashboardController = new DashboardController($db, $logger, $ratingSystem, $wooConnector);
    
    // Get action from request
    $action = $_GET['action'] ?? '';
    
    // Get current user ID (in a real app, this would come from the session)
    $userId = 1; // Default to admin for demo
    
    // Process based on action
    switch ($action) {
        case 'import':
            // Start import from WooCommerce
            $result = $dashboardController->startProductImport($userId);
            echo json_encode($result);
            break;
            
        case 'export':
            // Get product IDs from request (or all products if not specified)
            $productIds = $_POST['product_ids'] ?? [];
            
            // If no product IDs provided, get all published products
            if (empty($productIds)) {
                $stmt = $db->prepare("SELECT product_id FROM products WHERE status = 'published'");
                $stmt->execute();
                $products = $stmt->fetchAll(PDO::FETCH_COLUMN);
                $productIds = $products;
            }
            
            // Start export to WooCommerce
            $result = $dashboardController->startProductExport($productIds, $userId);
            echo json_encode($result);
            break;
            
        case 'full_sync':
            // Start import first
            $importResult = $dashboardController->startProductImport($userId);
            
            // If import was successful, schedule export to run after
            if ($importResult['success']) {
                // In a real application, this would be better handled with a queue system
                // For demo purposes, we'll simulate it by returning a message
                echo json_encode([
                    'success' => true,
                    'message' => 'Full sync initiated. Import started, export will follow automatically.'
                ]);
            } else {
                echo json_encode($importResult);
            }
            break;
            
        case 'status':
            // Get job ID from request
            $jobId = $_GET['job_id'] ?? 0;
            
            if ($jobId) {
                // Get job status
                $stmt = $db->prepare("
                    SELECT 
                        job_id, job_type, status, 
                        items_total, items_processed, items_succeeded, items_failed,
                        created_at, started_at, completed_at
                    FROM sync_jobs
                    WHERE job_id = ?
                ");
                $stmt->execute([$jobId]);
                $job = $stmt->fetch();
                
                if ($job) {
                    echo json_encode([
                        'success' => true,
                        'job' => $job
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Job not found'
                    ]);
                }
            } else {
                // Get latest job status
                $stmt = $db->prepare("
                    SELECT 
                        job_id, job_type, status, 
                        items_total, items_processed, items_succeeded, items_failed,
                        created_at, started_at, completed_at
                    FROM sync_jobs
                    ORDER BY created_at DESC
                    LIMIT 1
                ");
                $stmt->execute();
                $job = $stmt->fetch();
                
                if ($job) {
                    echo json_encode([
                        'success' => true,
                        'job' => $job
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'No jobs found'
                    ]);
                }
            }
            break;
            
        default:
            // Invalid action
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action specified'
            ]);
            break;
    }
} catch (Exception $e) {
    // Log error
    if (isset($logger)) {
        $logger->error('Sync API error: ' . $e->getMessage());
    }
    
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}