<?php
/**
 * Database Connection for WooCommerce PIM
 * 
 * Establishes connection to the MySQL database
 */

class Database {
    private static $instance = null;
    private $conn;
    
    /**
     * Database constructor - private to implement singleton pattern
     */
    private function __construct() {
        $host = 'localhost';
        $dbname = 'pimilleris_illeris';
        $username = 'pimilleris_illeris';
        $password = 'rRY*!K([jCHd';
        $charset = 'utf8mb4';
        
        try {
            $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->conn = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
    
    /**
     * Get database connection instance
     * 
     * @return PDO Database connection
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance->conn;
    }
    
    /**
     * Prevent cloning of the instance
     */
    private function __clone() {}
    
    /**
     * Prevent unserialization of the instance
     */
    private function __wakeup() {}
}