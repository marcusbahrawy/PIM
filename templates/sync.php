<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WooCommerce PIM - Sync</title>
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
                    <li class="active">
                        <a href="sync.php">
                            <span class="icon-sync"></span>
                            <span class="nav-text">Sync</span>
                        </a>
                    </li>
                    <li>
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
                    <h2>Sync with WooCommerce</h2>
                    <p class="breadcrumb">Home > Sync</p>
                </div>
                
                <div class="header-actions">
                    <div class="actions">
                        <button class="action-btn" id="viewLogsBtn">
                            <span class="icon-log"></span>
                            <span class="action-text">View Logs</span>
                        </button>
                        
                        <button class="action-btn primary" id="syncNowBtn">
                            <span class="icon-sync"></span>
                            <span class="action-text">Sync Now</span>
                        </button>
                    </div>
                </div>
            </header>
            
            <div class="sync-content">
                <!-- Sync Status -->
                <section class="sync-status-section">
                    <div class="card">
                        <div class="card-header">
                            <h3>Connection Status</h3>
                        </div>
                        <div class="card-body">
                            <div class="connection-status">
                                <div id="connectionIndicator" class="connection-indicator">
                                    <div class="indicator-circle"></div>
                                    <div class="indicator-text">Checking connection...</div>
                                </div>
                                
                                <div class="connection-info">
                                    <div class="info-item">
                                        <span class="info-label">WooCommerce URL:</span>
                                        <span class="info-value" id="woocommerceUrl">-</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Last Successful Sync:</span>
                                        <span class="info-value" id="lastSuccessfulSync">-</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Sync Schedule:</span>
                                        <span class="info-value" id="syncSchedule">-</span>
                                    </div>
                                </div>
                                
                                <div class="connection-actions">
                                    <button class="btn" id="testConnectionBtn">Test Connection</button>
                                    <button class="btn" id="configureConnectionBtn">Configure</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                
                <!-- Sync Options -->
                <section class="sync-options-section">
                    <div class="sync-options-grid">
                        <!-- Products Sync -->
                        <div class="card sync-option-card">
                            <div class="card-header">
                                <h3>Products</h3>
                                <div class="card-actions">
                                    <span class="badge" id="productsCount">0</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="sync-option-content">
                                    <div class="sync-option-icon">
                                        <span class="icon-product"></span>
                                    </div>
                                    <div class="sync-option-info">
                                        <p>Synchronize product data between WooCommerce and PIM.</p>
                                    </div>
                                </div>
                                <div class="sync-option-actions">
                                    <button class="btn" id="importProductsBtn">Import</button>
                                    <button class="btn" id="exportProductsBtn">Export</button>
                                    <button class="btn primary" id="syncProductsBtn">Full Sync</button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Categories Sync -->
                        <div class="card sync-option-card">
                            <div class="card-header">
                                <h3>Categories</h3>
                                <div class="card-actions">
                                    <span class="badge" id="categoriesCount">0</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="sync-option-content">
                                    <div class="sync-option-icon">
                                        <span class="icon-category"></span>
                                    </div>
                                    <div class="sync-option-info">
                                        <p>Synchronize product categories between WooCommerce and PIM.</p>
                                    </div>
                                </div>
                                <div class="sync-option-actions">
                                    <button class="btn" id="importCategoriesBtn">Import</button>
                                    <button class="btn" id="exportCategoriesBtn">Export</button>
                                    <button class="btn primary" id="syncCategoriesBtn">Full Sync</button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Attributes Sync -->
                        <div class="card sync-option-card">
                            <div class="card-header">
                                <h3>Attributes</h3>
                                <div class="card-actions">
                                    <span class="badge" id="attributesCount">0</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="sync-option-content">
                                    <div class="sync-option-icon">
                                        <span class="icon-attribute"></span>
                                    </div>
                                    <div class="sync-option-info">
                                        <p>Synchronize product attributes between WooCommerce and PIM.</p>
                                    </div>
                                </div>
                                <div class="sync-option-actions">
                                    <button class="btn" id="importAttributesBtn">Import</button>
                                    <button class="btn" id="exportAttributesBtn">Export</button>
                                    <button class="btn primary" id="syncAttributesBtn">Full Sync</button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Images Sync -->
                        <div class="card sync-option-card">
                            <div class="card-header">
                                <h3>Media</h3>
                                <div class="card-actions">
                                    <span class="badge" id="mediaCount">0</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="sync-option-content">
                                    <div class="sync-option-icon">
                                        <span class="icon-image"></span>
                                    </div>
                                    <div class="sync-option-info">
                                        <p>Synchronize product images and media between WooCommerce and PIM.</p>
                                    </div>
                                </div>
                                <div class="sync-option-actions">
                                    <button class="btn" id="importMediaBtn">Import</button>
                                    <button class="btn" id="exportMediaBtn">Export</button>
                                    <button class="btn primary" id="syncMediaBtn">Full Sync</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                
                <!-- Recent Sync Jobs -->
                <section class="sync-jobs-section">
                    <div class="card">
                        <div class="card-header">
                            <h3>Recent Sync Jobs</h3>
                            <div class="card-actions">
                                <button class="btn" id="refreshJobsBtn">
                                    <span class="icon-refresh"></span> Refresh
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="data-table" id="syncJobsTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Progress</th>
                                        <th>Started</th>
                                        <th>Completed</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="syncJobsTableBody">
                                    <!-- Will be populated by JavaScript -->
                                    <tr>
                                        <td colspan="7" class="text-center">Loading sync jobs...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
            
            <!-- Sync Now Modal -->
            <div class="modal" id="syncModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Sync with WooCommerce</h3>
                        <button class="close-btn" id="closeSyncModal">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="sync-options">
                            <div class="sync-option">
                                <h4>Import from WooCommerce</h4>
                                <p>Pull data from your WooCommerce store into PIM.</p>
                                <div class="checkbox-group">
                                    <div class="checkbox">
                                        <input type="checkbox" id="importProducts" checked>
                                        <label for="importProducts">Products</label>
                                    </div>
                                    <div class="checkbox">
                                        <input type="checkbox" id="importCategories" checked>
                                        <label for="importCategories">Categories</label>
                                    </div>
                                    <div class="checkbox">
                                        <input type="checkbox" id="importAttributes" checked>
                                        <label for="importAttributes">Attributes</label>
                                    </div>
                                    <div class="checkbox">
                                        <input type="checkbox" id="importMedia" checked>
                                        <label for="importMedia">Media</label>
                                    </div>
                                </div>
                                <button class="btn primary mt-2" id="startImportBtn">Start Import</button>
                            </div>
                            
                            <div class="sync-option">
                                <h4>Export to WooCommerce</h4>
                                <p>Push data from PIM to your WooCommerce store.</p>
                                <div class="checkbox-group">
                                    <div class="checkbox">
                                        <input type="checkbox" id="exportProducts" checked>
                                        <label for="exportProducts">Products</label>
                                    </div>
                                    <div class="checkbox">
                                        <input type="checkbox" id="exportCategories" checked>
                                        <label for="exportCategories">Categories</label>
                                    </div>
                                    <div class="checkbox">
                                        <input type="checkbox" id="exportAttributes" checked>
                                        <label for="exportAttributes">Attributes</label>
                                    </div>
                                    <div class="checkbox">
                                        <input type="checkbox" id="exportMedia" checked>
                                        <label for="exportMedia">Media</label>
                                    </div>
                                </div>
                                <button class="btn primary mt-2" id="startExportBtn">Start Export</button>
                            </div>
                            
                            <div class="sync-option">
                                <h4>Full Sync</h4>
                                <p>Perform a full two-way synchronization.</p>
                                <div class="checkbox-group">
                                    <div class="checkbox">
                                        <input type="checkbox" id="syncProducts" checked>
                                        <label for="syncProducts">Products</label>
                                    </div>
                                    <div class="checkbox">
                                        <input type="checkbox" id="syncCategories" checked>
                                        <label for="syncCategories">Categories</label>
                                    </div>
                                    <div class="checkbox">
                                        <input type="checkbox" id="syncAttributes" checked>
                                        <label for="syncAttributes">Attributes</label>
                                    </div>
                                    <div class="checkbox">
                                        <input type="checkbox" id="syncMedia" checked>
                                        <label for="syncMedia">Media</label>
                                    </div>
                                </div>
                                <button class="btn primary mt-2" id="startFullSyncBtn">Start Full Sync</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Connection Configuration Modal -->
            <div class="modal" id="connectionModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>WooCommerce API Connection</h3>
                        <button class="close-btn" id="closeConnectionModal">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="woocommerceApiUrl">WooCommerce Store URL</label>
                            <input type="text" id="woocommerceApiUrl" class="form-control" placeholder="https://example.com">
                            <small class="form-text">Enter the URL of your WooCommerce store.</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="woocommerceConsumerKey">Consumer Key</label>
                            <input type="text" id="woocommerceConsumerKey" class="form-control" placeholder="ck_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
                            <small class="form-text">Enter your WooCommerce REST API Consumer Key.</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="woocommerceConsumerSecret">Consumer Secret</label>
                            <input type="password" id="woocommerceConsumerSecret" class="form-control" placeholder="cs_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
                            <small class="form-text">Enter your WooCommerce REST API Consumer Secret.</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="syncFrequency">Sync Frequency</label>
                            <select id="syncFrequency" class="form-control">
                                <option value="manual">Manual</option>
                                <option value="hourly">Hourly</option>
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                            </select>
                            <small class="form-text">How often should automatic synchronization be performed?</small>
                        </div>
                        
                        <div class="modal-actions">
                            <button class="btn" id="cancelConnectionBtn">Cancel</button>
                            <button class="btn primary" id="saveConnectionBtn">Save Connection</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Job Details Modal -->
            <div class="modal" id="jobDetailsModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Sync Job Details</h3>
                        <button class="close-btn" id="closeJobDetailsModal">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="job-details-container" id="jobDetailsContainer">
                            <!-- Will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script src="assets/js/sync.js"></script>
</body>
</html>