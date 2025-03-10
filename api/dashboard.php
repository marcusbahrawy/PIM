<?php
/**
 * Dashboard API Endpoint
 * Provides dashboard data for the WooCommerce PIM system
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
    
    // Get dashboard data
    $dashboardData = $dashboardController->getDashboardData();
    
    // Return dashboard data as JSON
    echo json_encode([
        'success' => true,
        'data' => $dashboardData
    ]);
} catch (Exception $e) {
    // Log error
    if (isset($logger)) {
        $logger->error('Dashboard API error: ' . $e->getMessage());
    }
    
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while fetching dashboard data: ' . $e->getMessage()
    ]);
}