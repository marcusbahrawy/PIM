<?php
/**
 * Logger Class for WooCommerce PIM
 * 
 * Handles application logging
 */

class Logger {
    private $logFile;
    private $logLevel;
    private $logLevels = [
        'debug' => 0,
        'info' => 1,
        'notice' => 2,
        'warning' => 3,
        'error' => 4,
        'critical' => 5,
        'alert' => 6,
        'emergency' => 7
    ];
    
    /**
     * Constructor
     * 
     * @param string $logFile Path to log file
     * @param string $logLevel Minimum log level to record
     */
    public function __construct($logFile = null, $logLevel = 'info') {
        // If no log file specified, create one in logs directory
        if ($logFile === null) {
            $logDir = __DIR__ . '/../logs';
            
            // Create logs directory if it doesn't exist
            if (!is_dir($logDir)) {
                mkdir($logDir, 0755, true);
            }
            
            $this->logFile = $logDir . '/app_' . date('Y-m-d') . '.log';
        } else {
            $this->logFile = $logFile;
        }
        
        $this->logLevel = strtolower($logLevel);
        
        // Make sure the log file is writable
        if (!is_writable(dirname($this->logFile))) {
            throw new Exception("Log directory is not writable: " . dirname($this->logFile));
        }
    }
    
    /**
     * Write to log
     * 
     * @param string $level Log level
     * @param string $message Log message
     * @param array $context Additional context data
     * @return bool Success/failure
     */
    public function log($level, $message, array $context = []) {
        $level = strtolower($level);
        
        // Check if this log level should be recorded
        if (!isset($this->logLevels[$level]) || $this->logLevels[$level] < $this->logLevels[$this->logLevel]) {
            return false;
        }
        
        // Format the log entry
        $timestamp = date('Y-m-d H:i:s');
        $levelUpper = strtoupper($level);
        
        // Replace placeholders in the message
        $message = $this->interpolate($message, $context);
        
        // Add context as JSON if not empty
        $contextString = '';
        if (!empty($context)) {
            $contextString = ' ' . json_encode($context);
        }
        
        // Format the log line
        $logLine = "[$timestamp] [$levelUpper] $message$contextString" . PHP_EOL;
        
        // Write to log file
        file_put_contents($this->logFile, $logLine, FILE_APPEND);
        
        return true;
    }
    
    /**
     * Replace placeholders in the message
     * 
     * @param string $message Message with placeholders
     * @param array $context Data to replace placeholders
     * @return string Interpolated message
     */
    private function interpolate($message, array $context = []) {
        // Build a replacement array with braces around the context keys
        $replace = [];
        foreach ($context as $key => $val) {
            // Skip non-scalar values
            if (!is_scalar($val) && !is_null($val)) {
                continue;
            }
            
            $replace['{' . $key . '}'] = $val;
        }
        
        // Replace placeholders with values
        return strtr($message, $replace);
    }
    
    /**
     * Debug level log
     * 
     * @param string $message Log message
     * @param array $context Additional context data
     */
    public function debug($message, array $context = []) {
        $this->log('debug', $message, $context);
    }
    
    /**
     * Info level log
     * 
     * @param string $message Log message
     * @param array $context Additional context data
     */
    public function info($message, array $context = []) {
        $this->log('info', $message, $context);
    }
    
    /**
     * Notice level log
     * 
     * @param string $message Log message
     * @param array $context Additional context data
     */
    public function notice($message, array $context = []) {
        $this->log('notice', $message, $context);
    }
    
    /**
     * Warning level log
     * 
     * @param string $message Log message
     * @param array $context Additional context data
     */
    public function warning($message, array $context = []) {
        $this->log('warning', $message, $context);
    }
    
    /**
     * Error level log
     * 
     * @param string $message Log message
     * @param array $context Additional context data
     */
    public function error($message, array $context = []) {
        $this->log('error', $message, $context);
    }
    
    /**
     * Critical level log
     * 
     * @param string $message Log message
     * @param array $context Additional context data
     */
    public function critical($message, array $context = []) {
        $this->log('critical', $message, $context);
    }
    
    /**
     * Alert level log
     * 
     * @param string $message Log message
     * @param array $context Additional context data
     */
    public function alert($message, array $context = []) {
        $this->log('alert', $message, $context);
    }
    
    /**
     * Emergency level log
     * 
     * @param string $message Log message
     * @param array $context Additional context data
     */
    public function emergency($message, array $context = []) {
        $this->log('emergency', $message, $context);
    }
}