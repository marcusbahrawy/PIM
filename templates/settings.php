<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WooCommerce PIM - Settings</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="theme-light">
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h1 class="logo">WooCommerce PIM</h1>
                <button class="menu-toggle" id="sidebarToggle">
                    <span class="icon-menu"></span>
                </button>
            </div>
            
            <nav class="sidebar-nav">
                <ul>
                    <li>
                        <a href="dashboard.php">
                            <span class="icon-dashboard"></span>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="products.php">
                            <span class="icon-product"></span>
                            <span class="nav-text">Products</span>
                        </a>
                    </li>
                    <li>
                        <a href="categories.php">
                            <span class="icon-category"></span>
                            <span class="nav-text">Categories</span>
                        </a>
                    </li>
                    <li>
                        <a href="attributes.php">
                            <span class="icon-attribute"></span>
                            <span class="nav-text">Attributes</span>
                        </a>
                    </li>
                    <li>
                        <a href="sync.php">
                            <span class="icon-sync"></span>
                            <span class="nav-text">Sync</span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="settings.php">
                            <span class="icon-settings"></span>
                            <span class="nav-text">Settings</span>
                        </a>
                    </li>
                    <li>
                        <a href="users.php">
                            <span class="icon-users"></span>
                            <span class="nav-text">Users</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <div class="sidebar-footer">
                <div class="theme-switch">
                    <label class="switch">
                        <input type="checkbox" id="themeToggle">
                        <span class="slider round"></span>
                    </label>
                    <span class="theme-text">Dark Mode</span>
                </div>
                
                <div class="user-info">
                    <div class="user-avatar">
                        <img src="assets/images/avatar.png" alt="User Avatar">
                    </div>
                    <div class="user-details">
                        <span class="user-name">John Doe</span>
                        <span class="user-role">Administrator</span>
                    </div>
                </div>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <header class="page-header">
                <div class="header-title">
                    <h2>Settings</h2>
                    <p class="breadcrumb">Home > Settings</p>
                </div>
                
                <div class="header-actions">
                    <button class="action-btn primary" id="saveSettingsBtn">
                        <span class="icon-save"></span>
                        <span class="action-text">Save Settings</span>
                    </button>
                </div>
            </header>
            
            <div class="settings-content">
                <div class="settings-tabs">
                    <div class="tab-nav">
                        <button class="tab-btn active" data-tab="general">General Settings</button>
                        <button class="tab-btn" data-tab="woocommerce">WooCommerce Integration</button>
                        <button class="tab-btn" data-tab="products">Product Settings</button>
                        <button class="tab-btn" data-tab="rating">Rating System</button>
                        <button class="tab-btn" data-tab="backups">Backups</button>
                        <button class="tab-btn" data-tab="logs">Logs</button>
                    </div>
                    
                    <div class="tab-content">
                        <!-- General Settings Tab -->
                        <div class="tab-pane active" id="generalTab">
                            <div class="card">
                                <div class="card-header">
                                    <h3>Application Settings</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="siteName">Site Name</label>
                                        <input type="text" id="siteName" class="form-control" value="WooCommerce PIM">
                                        <small class="form-text">The name of your PIM instance.</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="language">Language</label>
                                        <select id="language" class="form-control">
                                            <option value="en">English</option>
                                            <option value="fr">French</option>
                                            <option value="de">German</option>
                                            <option value="es">Spanish</option>
                                            <option value="it">Italian</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="timezone">Timezone</label>
                                        <select id="timezone" class="form-control">
                                            <option value="UTC">UTC</option>
                                            <option value="America/New_York">Eastern Time (US & Canada)</option>
                                            <option value="America/Chicago">Central Time (US & Canada)</option>
                                            <option value="America/Denver">Mountain Time (US & Canada)</option>
                                            <option value="America/Los_Angeles">Pacific Time (US & Canada)</option>
                                            <option value="Europe/London">London</option>
                                            <option value="Europe/Paris">Paris</option>
                                            <option value="Europe/Berlin">Berlin</option>
                                            <option value="Asia/Tokyo">Tokyo</option>
                                            <option value="Australia/Sydney">Sydney</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="dateFormat">Date Format</label>
                                        <select id="dateFormat" class="form-control">
                                            <option value="Y-m-d">2023-11-25 (YYYY-MM-DD)</option>
                                            <option value="m/d/Y">11/25/2023 (MM/DD/YYYY)</option>
                                            <option value="d/m/Y">25/11/2023 (DD/MM/YYYY)</option>
                                            <option value="M j, Y">Nov 25, 2023</option>
                                            <option value="j F Y">25 November 2023</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="itemsPerPage">Items Per Page</label>
                                        <select id="itemsPerPage" class="form-control">
                                            <option value="10">10</option>
                                            <option value="20" selected>20</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                        <small class="form-text">Number of items to display per page in listings.</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h3>Theme & Appearance</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Default Theme</label>
                                        <div class="theme-options">
                                            <div class="theme-option">
                                                <input type="radio" id="themeLight" name="defaultTheme" value="light" checked>
                                                <label for="themeLight" class="theme-preview light">
                                                    <div class="theme-preview-header"></div>
                                                    <div class="theme-preview-body"></div>
                                                    <span class="theme-name">Light</span>
                                                </label>
                                            </div>
                                            <div class="theme-option">
                                                <input type="radio" id="themeDark" name="defaultTheme" value="dark">
                                                <label for="themeDark" class="theme-preview dark">
                                                    <div class="theme-preview-header"></div>
                                                    <div class="theme-preview-body"></div>
                                                    <span class="theme-name">Dark</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Primary Color</label>
                                        <div class="color-picker">
                                            <input type="color" id="primaryColor" value="#4a6cf7">
                                            <input type="text" class="form-control color-value" id="primaryColorValue" value="#4a6cf7">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <div class="checkbox">
                                            <input type="checkbox" id="collapseSidebar">
                                            <label for="collapseSidebar">Collapse sidebar by default</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- WooCommerce Integration Tab -->
                        <div class="tab-pane" id="woocommerceTab">
                            <div class="card">
                                <div class="card-header">
                                    <h3>WooCommerce API Settings</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="woocommerceUrl">WooCommerce Store URL</label>
                                        <input type="text" id="woocommerceUrl" class="form-control" placeholder="https://example.com">
                                        <small class="form-text">Enter the URL of your WooCommerce store.</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="consumerKey">Consumer Key</label>
                                        <input type="text" id="consumerKey" class="form-control" placeholder="ck_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
                                        <small class="form-text">Enter your WooCommerce REST API Consumer Key.</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="consumerSecret">Consumer Secret</label>
                                        <input type="password" id="consumerSecret" class="form-control" placeholder="cs_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
                                        <small class="form-text">Enter your WooCommerce REST API Consumer Secret.</small>
                                    </div>
                                    
                                    <div class="form-actions">
                                        <button class="btn" id="testApiConnectionBtn">Test Connection</button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h3>Synchronization Settings</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="syncFrequency">Automatic Sync Frequency</label>
                                        <select id="syncFrequency" class="form-control">
                                            <option value="manual">Manual Only</option>
                                            <option value="hourly">Hourly</option>
                                            <option value="twice_daily">Twice Daily</option>
                                            <option value="daily">Daily</option>
                                            <option value="weekly">Weekly</option>
                                        </select>
                                        <small class="form-text">How often should automatic synchronization be performed?</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="syncTime">Sync Time</label>
                                        <input type="time" id="syncTime" class="form-control" value="00:00">
                                        <small class="form-text">For daily and weekly syncs, specify the time to run the sync.</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="syncDay">Sync Day</label>
                                        <select id="syncDay" class="form-control">
                                            <option value="1">Monday</option>
                                            <option value="2">Tuesday</option>
                                            <option value="3">Wednesday</option>
                                            <option value="4">Thursday</option>
                                            <option value="5">Friday</option>
                                            <option value="6">Saturday</option>
                                            <option value="0">Sunday</option>
                                        </select>
                                        <small class="form-text">For weekly syncs, specify the day to run the sync.</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <div class="checkbox">
                                            <input type="checkbox" id="syncNotifications" checked>
                                            <label for="syncNotifications">Send email notifications on sync completion</label>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <div class="checkbox">
                                            <input type="checkbox" id="syncErrorNotifications" checked>
                                            <label for="syncErrorNotifications">Send email notifications on sync errors</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Product Settings Tab -->
                        <div class="tab-pane" id="productsTab">
                            <div class="card">
                                <div class="card-header">
                                    <h3>Product Settings</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="defaultProductStatus">Default Product Status</label>
                                        <select id="defaultProductStatus" class="form-control">
                                            <option value="draft">Draft</option>
                                            <option value="published">Published</option>
                                        </select>
                                        <small class="form-text">Status for newly created products.</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="defaultProductVisibility">Default Product Visibility</label>
                                        <select id="defaultProductVisibility" class="form-control">
                                            <option value="visible">Visible</option>
                                            <option value="catalog">Catalog</option>
                                            <option value="search">Search</option>
                                            <option value="hidden">Hidden</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="requiredAttributes">Required Attributes</label>
                                        <select id="requiredAttributes" class="form-control" multiple>
                                            <!-- Will be populated by JavaScript -->
                                        </select>
                                        <small class="form-text">Select attributes that are required for all products.</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <div class="checkbox">
                                            <input type="checkbox" id="manageStock" checked>
                                            <label for="manageStock">Enable stock management by default</label>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="defaultTaxStatus">Default Tax Status</label>
                                        <select id="defaultTaxStatus" class="form-control">
                                            <option value="taxable">Taxable</option>
                                            <option value="shipping">Shipping only</option>
                                            <option value="none">None</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="defaultTaxClass">Default Tax Class</label>
                                        <select id="defaultTaxClass" class="form-control">
                                            <option value="">Standard</option>
                                            <option value="reduced-rate">Reduced Rate</option>
                                            <option value="zero-rate">Zero Rate</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h3>Media Settings</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="imageQuality">Image Quality</label>
                                        <select id="imageQuality" class="form-control">
                                            <option value="high">High (minimal compression)</option>
                                            <option value="medium" selected>Medium (balanced)</option>
                                            <option value="low">Low (maximum compression)</option>
                                        </select>
                                        <small class="form-text">Quality level for uploaded images.</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Maximum Image Dimensions</label>
                                        <div class="input-group">
                                            <input type="number" id="maxImageWidth" class="form-control" value="1200" min="0">
                                            <span class="input-group-text">×</span>
                                            <input type="number" id="maxImageHeight" class="form-control" value="1200" min="0">
                                            <span class="input-group-text">px</span>
                                        </div>
                                        <small class="form-text">Images will be resized if they exceed these dimensions.</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <div class="checkbox">
                                            <input type="checkbox" id="generateAltText" checked>
                                            <label for="generateAltText">Automatically generate alt text for images</label>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <div class="checkbox">
                                            <input type="checkbox" id="keepOriginalImages" checked>
                                            <label for="keepOriginalImages">Keep original images when optimizing</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Rating System Tab -->
                        <div class="tab-pane" id="ratingTab">
                            <div class="card">
                                <div class="card-header">
                                    <h3>Product Rating System</h3>
                                </div>
                                <div class="card-body">
                                    <p class="info-text">The Product Rating System evaluates products on a scale of 0-100% based on completeness and quality. Adjust the weights below to customize the importance of each criterion.</p>
                                    
                                    <div class="rating-criteria">
                                        <div class="criteria-item">
                                            <div class="criteria-header">
                                                <h4>Basic Information</h4>
                                                <div class="criteria-weight">
                                                    <input type="number" id="weightBasicInfo" class="form-control" value="1.0" min="0.1" max="5.0" step="0.1">
                                                </div>
                                            </div>
                                            <div class="criteria-desc">Presence of name, SKU, price, etc.</div>
                                        </div>
                                        
                                        <div class="criteria-item">
                                            <div class="criteria-header">
                                                <h4>Description</h4>
                                                <div class="criteria-weight">
                                                    <input type="number" id="weightDescription" class="form-control" value="1.5" min="0.1" max="5.0" step="0.1">
                                                </div>
                                            </div>
                                            <div class="criteria-desc">Quality and completeness of product description</div>
                                        </div>
                                        
                                        <div class="criteria-item">
                                            <div class="criteria-header">
                                                <h4>Images</h4>
                                                <div class="criteria-weight">
                                                    <input type="number" id="weightImages" class="form-control" value="2.0" min="0.1" max="5.0" step="0.1">
                                                </div>
                                            </div>
                                            <div class="criteria-desc">Number and quality of product images with alt text</div>
                                        </div>
                                        
                                        <div class="criteria-item">
                                            <div class="criteria-header">
                                                <h4>SEO Elements</h4>
                                                <div class="criteria-weight">
                                                    <input type="number" id="weightSeo" class="form-control" value="1.8" min="0.1" max="5.0" step="0.1">
                                                </div>
                                            </div>
                                            <div class="criteria-desc">Presence of meta titles, descriptions, and keywords</div>
                                        </div>
                                        
                                        <div class="criteria-item">
                                            <div class="criteria-header">
                                                <h4>Attributes</h4>
                                                <div class="criteria-weight">
                                                    <input type="number" id="weightAttributes" class="form-control" value="0.8" min="0.1" max="5.0" step="0.1">
                                                </div>
                                            </div>
                                            <div class="criteria-desc">Proper assignment of product attributes</div>
                                        </div>
                                        
                                        <div class="criteria-item">
                                            <div class="criteria-header">
                                                <h4>Categories</h4>
                                                <div class="criteria-weight">
                                                    <input type="number" id="weightCategories" class="form-control" value="0.7" min="0.1" max="5.0" step="0.1">
                                                </div>
                                            </div>
                                            <div class="criteria-desc">Proper category assignment</div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group mt-3">
                                        <label>Score Thresholds</label>
                                        <div class="range-slider">
                                            <div class="range-labels">
                                                <span>0%</span>
                                                <span>100%</span>
                                            </div>
                                            <div class="range-track">
                                                <div class="range-section red" style="width: 50%;"></div>
                                                <div class="range-section yellow" style="width: 30%;"></div>
                                                <div class="range-section green" style="width: 20%;"></div>
                                                <input type="range" id="yellowThreshold" min="1" max="99" value="50">
                                                <input type="range" id="greenThreshold" min="1" max="99" value="80">
                                            </div>
                                            <div class="range-values">
                                                <span class="red-label">Poor: 0-<span id="yellowThresholdValue">50</span>%</span>
                                                <span class="yellow-label">Needs Improvement: <span id="yellowThresholdValue2">50</span>-<span id="greenThresholdValue">80</span>%</span>
                                                <span class="green-label">Excellent: <span id="greenThresholdValue2">80</span>-100%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Backups Tab -->
                        <div class="tab-pane" id="backupsTab">
                            <div class="card">
                                <div class="card-header">
                                    <h3>Database Backups</h3>
                                    <div class="card-actions">
                                        <button class="btn primary" id="createBackupBtn">
                                            <span class="icon-backup"></span> Create Backup
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="automaticBackups">Automatic Backups</label>
                                        <select id="automaticBackups" class="form-control">
                                            <option value="disabled">Disabled</option>
                                            <option value="daily">Daily</option>
                                            <option value="weekly" selected>Weekly</option>
                                            <option value="monthly">Monthly</option>
                                        </select>
                                        <small class="form-text">How often should automatic backups be created?</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="backupRetention">Backup Retention</label>
                                        <select id="backupRetention" class="form-control">
                                            <option value="3">3 backups</option>
                                            <option value="5" selected>5 backups</option>
                                            <option value="10">10 backups</option>
                                            <option value="30">30 backups</option>
                                            <option value="0">Keep all backups</option>
                                        </select>
                                        <small class="form-text">How many backups should be kept before deleting older ones?</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Recent Backups</label>
                                        <table class="data-table" id="backupsTable">
                                            <thead>
                                                <tr>
                                                    <th>Date & Time</th>
                                                    <th>Size</th>
                                                    <th>Type</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="backupsTableBody">
                                                <!-- Will be populated by JavaScript -->
                                                <tr>
                                                    <td colspan="4" class="text-center">No backups found</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Logs Tab -->
                        <div class="tab-pane" id="logsTab">
                            <div class="card">
                                <div class="card-header">
                                    <h3>Application Logs</h3>
                                    <div class="card-actions">
                                        <button class="btn" id="clearLogsBtn">
                                            <span class="icon-trash"></span> Clear Logs
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="logLevel">Log Level</label>
                                        <select id="logLevel" class="form-control">
                                            <option value="debug">Debug (Most Verbose)</option>
                                            <option value="info" selected>Info</option>
                                            <option value="notice">Notice</option>
                                            <option value="warning">Warning</option>
                                            <option value="error">Error</option>
                                            <option value="critical">Critical</option>
                                            <option value="alert">Alert</option>
                                            <option value="emergency">Emergency</option>
                                        </select>
                                        <small class="form-text">Only events at this level or higher will be logged.</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="logRetention">Log Retention</label>
                                        <select id="logRetention" class="form-control">
                                            <option value="7">7 days</option>
                                            <option value="14" selected>14 days</option>
                                            <option value="30">30 days</option>
                                            <option value="90">90 days</option>
                                            <option value="0">Keep all logs</option>
                                        </select>
                                        <small class="form-text">How long should logs be kept before being automatically deleted?</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Recent Logs</label>
                                        <div class="log-filter">
                                            <div class="filter-group">
                                                <label for="logFilterLevel">Filter by Level:</label>
                                                <select id="logFilterLevel" class="form-control">
                                                    <option value="all">All Levels</option>
                                                    <option value="error">Error & Above</option>
                                                    <option value="warning">Warning & Above</option>
                                                    <option value="info">Info & Above</option>
                                                    <option value="debug">Debug</option>
                                                </select>
                                            </div>
                                            <div class="filter-group">
                                                <label for="logFilterDate">Date Range:</label>
                                                <select id="logFilterDate" class="form-control">
                                                    <option value="today">Today</option>
                                                    <option value="yesterday">Yesterday</option>
                                                    <option value="week">Past Week</option>
                                                    <option value="month">Past Month</option>
                                                    <option value="all" selected>All Time</option>
                                                </select>
                                            </div>
                                            <button class="btn" id="applyLogFiltersBtn">Apply Filters</button>
                                        </div>
                                        
                                        <div class="logs-container" id="logsContainer">
                                            <!-- Will be populated by JavaScript -->
                                            <div class="logs-empty">No logs found matching your criteria.</div>
                                        </div>
                                        
                                        <div class="logs-pagination">
                                            <button class="btn btn-sm" id="prevLogsBtn" disabled>
                                                <span class="icon-arrow-left"></span> Previous
                                            </button>
                                            <span class="pagination-info">Page 1 of 1</span>
                                            <button class="btn btn-sm" id="nextLogsBtn" disabled>
                                                Next <span class="icon-arrow-right"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Save Changes Notification -->
            <div class="notification" id="saveNotification">
                <div class="notification-content">
                    <span class="icon-check"></span>
                    <span class="notification-text">Settings saved successfully!</span>
                </div>
            </div>
            
            <!-- Create Backup Modal -->
            <div class="modal" id="createBackupModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Create Backup</h3>
                        <button class="close-btn" id="closeCreateBackupModal">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="backupType">Backup Type</label>
                            <select id="backupType" class="form-control">
                                <option value="full">Full Backup (Database & Media)</option>
                                <option value="database">Database Only</option>
                                <option value="media">Media Only</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="backupDescription">Description (Optional)</label>
                            <input type="text" id="backupDescription" class="form-control" placeholder="Enter a description for this backup">
                        </div>
                        
                        <div class="modal-actions">
                            <button class="btn" id="cancelBackupBtn">Cancel</button>
                            <button class="btn primary" id="startBackupBtn">Start Backup</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Restore Backup Modal -->
            <div class="modal" id="restoreBackupModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Restore Backup</h3>
                        <button class="close-btn" id="closeRestoreBackupModal">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="warning-message">
                            <span class="icon-warning"></span>
                            <p>Warning: Restoring a backup will overwrite all current data. This action cannot be undone.</p>
                        </div>
                        
                        <div class="backup-details" id="restoreBackupDetails">
                            <!-- Will be populated by JavaScript -->
                        </div>
                        
                        <div class="form-group">
                            <div class="checkbox">
                                <input type="checkbox" id="confirmRestore">
                                <label for="confirmRestore">I understand that this will overwrite all current data.</label>
                            </div>
                        </div>
                        
                        <div class="modal-actions">
                            <button class="btn" id="cancelRestoreBtn">Cancel</button>
                            <button class="btn danger" id="confirmRestoreBtn" disabled>Restore Backup</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Clear Logs Confirmation Modal -->
            <div class="modal" id="clearLogsModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Clear Logs</h3>
                        <button class="close-btn" id="closeClearLogsModal">×</button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to clear all logs? This action cannot be undone.</p>
                        <div class="modal-actions">
                            <button class="btn" id="cancelClearLogsBtn">Cancel</button>
                            <button class="btn danger" id="confirmClearLogsBtn">Clear Logs</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script src="assets/js/settings.js"></script>
</body>
</html>