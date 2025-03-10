<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WooCommerce PIM - Dashboard</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <header class="page-header">
                <div class="header-title">
                    <h2>Dashboard</h2>
                    <p class="breadcrumb">Home > Dashboard</p>
                </div>
                
                <div class="header-actions">
                    <div class="search-box">
                        <input type="text" placeholder="Search...">
                        <button class="search-btn">
                            <span class="icon-search"></span>
                        </button>
                    </div>
                    
                    <div class="notifications">
                        <button class="notification-btn">
                            <span class="icon-notification"></span>
                            <span class="notification-count">3</span>
                        </button>
                    </div>
                    
                    <div class="actions">
                        <button class="action-btn" id="syncBtn">
                            <span class="icon-sync"></span>
                            <span class="action-text">Sync Now</span>
                        </button>
                        
                        <button class="action-btn primary" id="newProductBtn">
                            <span class="icon-plus"></span>
                            <span class="action-text">New Product</span>
                        </button>
                    </div>
                </div>
            </header>
            
            <div class="dashboard-content">
                <!-- Stats Cards -->
                <section class="stats-cards">
                    <div class="card stat-card">
                        <div class="stat-icon total">
                            <span class="icon-products"></span>
                        </div>
                        <div class="stat-info">
                            <h3>Total Products</h3>
                            <p class="stat-value" id="totalProducts">0</p>
                        </div>
                    </div>
                    
                    <div class="card stat-card">
                        <div class="stat-icon published">
                            <span class="icon-published"></span>
                        </div>
                        <div class="stat-info">
                            <h3>Published</h3>
                            <p class="stat-value" id="publishedProducts">0</p>
                        </div>
                    </div>
                    
                    <div class="card stat-card">
                        <div class="stat-icon draft">
                            <span class="icon-draft"></span>
                        </div>
                        <div class="stat-info">
                            <h3>Drafts</h3>
                            <p class="stat-value" id="draftProducts">0</p>
                        </div>
                    </div>
                    
                    <div class="card stat-card">
                        <div class="stat-icon score">
                            <span class="icon-score"></span>
                        </div>
                        <div class="stat-info">
                            <h3>Avg. Rating Score</h3>
                            <p class="stat-value" id="averageScore">0%</p>
                        </div>
                    </div>
                </section>
                
                <!-- Rating Distribution Chart -->
                <section class="dashboard-row">
                    <div class="card chart-card">
                        <div class="card-header">
                            <h3>Product Rating Distribution</h3>
                            <div class="card-actions">
                                <button class="card-action-btn">
                                    <span class="icon-refresh"></span>
                                </button>
                                <button class="card-action-btn">
                                    <span class="icon-more"></span>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="ratingChart"></canvas>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h3>Products Needing Attention</h3>
                            <div class="card-actions">
                                <button class="card-action-btn">
                                    <span class="icon-refresh"></span>
                                </button>
                                <button class="card-action-btn">
                                    <span class="icon-more"></span>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Score</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="lowScoreProducts">
                                    <!-- Will be populated by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
                
                <!-- Recent Products and Activity -->
                <section class="dashboard-row">
                    <div class="card">
                        <div class="card-header">
                            <h3>Recent Products</h3>
                            <div class="card-actions">
                                <a href="products.php" class="view-all">View All</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Status</th>
                                        <th>Rating</th>
                                        <th>Last Updated</th>
                                    </tr>
                                </thead>
                                <tbody id="recentProducts">
                                    <!-- Will be populated by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h3>Recent Activity</h3>
                        </div>
                        <div class="card-body">
                            <ul class="activity-list" id="recentActivity">
                                <!-- Will be populated by JavaScript -->
                            </ul>
                        </div>
                    </div>
                </section>
                
                <!-- Sync Status -->
                <section class="dashboard-row">
                    <div class="card">
                        <div class="card-header">
                            <h3>Sync Status</h3>
                            <div class="card-actions">
                                <button class="card-action-btn primary" id="syncNowBtn">
                                    <span class="icon-sync"></span>
                                    <span>Sync Now</span>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="sync-info">
                                <div class="sync-detail">
                                    <span class="sync-label">Last Sync:</span>
                                    <span class="sync-value" id="lastSync">Never</span>
                                </div>
                                <div class="sync-detail">
                                    <span class="sync-label">Pending Jobs:</span>
                                    <span class="sync-value" id="pendingJobs">0</span>
                                </div>
                            </div>
                            
                            <table class="data-table mt-2">
                                <thead>
                                    <tr>
                                        <th>Job Type</th>
                                        <th>Status</th>
                                        <th>Progress</th>
                                        <th>Started</th>
                                        <th>Completed</th>
                                    </tr>
                                </thead>
                                <tbody id="recentJobs">
                                    <!-- Will be populated by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
    
    <!-- Modal for Sync Options -->
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
                        <p>Pull products from your WooCommerce store into PIM.</p>
                        <button class="btn primary" id="importBtn">Import Products</button>
                    </div>
                    
                    <div class="sync-option">
                        <h4>Export to WooCommerce</h4>
                        <p>Push product changes from PIM to your WooCommerce store.</p>
                        <button class="btn primary" id="exportBtn">Export All Products</button>
                    </div>
                    
                    <div class="sync-option">
                        <h4>Full Sync</h4>
                        <p>Perform a full two-way synchronization.</p>
                        <button class="btn primary" id="fullSyncBtn">Full Sync</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="assets/js/dashboard.js"></script>
</body>
</html>
            
            <nav class="sidebar-nav">
                <ul>
                    <li class="active">
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