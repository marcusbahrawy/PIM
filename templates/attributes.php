<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WooCommerce PIM - Attributes</title>
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
                    <li class="active">
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
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <header class="page-header">
                <div class="header-title">
                    <h2>Attributes</h2>
                    <p class="breadcrumb">Home > Attributes</p>
                </div>
                
                <div class="header-actions">
                    <div class="search-box">
                        <input type="text" id="attributeSearch" placeholder="Search attributes...">
                        <button class="search-btn">
                            <span class="icon-search"></span>
                        </button>
                    </div>
                    
                    <div class="actions">
                        <button class="action-btn" id="syncAttributesBtn">
                            <span class="icon-sync"></span>
                            <span class="action-text">Sync Attributes</span>
                        </button>
                        
                        <button class="action-btn primary" id="newAttributeBtn">
                            <span class="icon-plus"></span>
                            <span class="action-text">New Attribute</span>
                        </button>
                    </div>
                </div>
            </header>
            
            <div class="attributes-content">
                <div class="attributes-layout">
                    <!-- Attributes List -->
                    <section class="attributes-list-section">
                        <div class="card">
                            <div class="card-header">
                                <h3>Attributes</h3>
                                <div class="card-actions">
                                    <div class="view-toggle">
                                        <button class="view-toggle-btn active" id="listViewBtn">
                                            <span class="icon-list"></span>
                                        </button>
                                        <button class="view-toggle-btn" id="gridViewBtn">
                                            <span class="icon-grid"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="attributes-container" id="attributesContainer">
                                    <!-- Will be populated by JavaScript -->
                                    <div class="loading">Loading attributes...</div>
                                </div>
                            </div>
                        </div>
                    </section>
                    
                    <!-- Attribute Details/Editor -->
                    <section class="attribute-details-section">
                        <div class="card">
                            <div class="card-header">
                                <h3 id="attributeDetailTitle">Attribute Details</h3>
                                <div class="card-actions">
                                    <button class="card-action-btn danger" id="deleteAttributeBtn" style="display: none;">
                                        <span class="icon-trash"></span>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="attribute-form" id="attributeForm" style="display: none;">
                                    <div class="form-group">
                                        <label for="attributeLabel">Attribute Name (Label)</label>
                                        <input type="text" id="attributeLabel" class="form-control" placeholder="Enter attribute name (e.g., Color, Size)">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="attributeName">Attribute Slug</label>
                                        <input type="text" id="attributeName" class="form-control" placeholder="Enter attribute slug (e.g., color, size)">
                                        <small class="form-text">The "slug" is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and underscores.</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="attributeType">Type</label>
                                        <select id="attributeType" class="form-control">
                                            <option value="text">Text</option>
                                            <option value="select">Select (Dropdown)</option>
                                            <option value="checkbox">Checkbox</option>
                                            <option value="number">Number</option>
                                            <option value="date">Date</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <div class="checkbox">
                                            <input type="checkbox" id="attributeVisible" checked>
                                            <label for="attributeVisible">Visible on product page</label>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <div class="checkbox">
                                            <input type="checkbox" id="attributeVariation">
                                            <label for="attributeVariation">Used for variations</label>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="attributeValues">Values</label>
                                        <div class="attribute-values-container">
                                            <div class="attribute-values-input">
                                                <textarea id="attributeValues" class="form-control" rows="5" placeholder="Enter values, one per line or separated by | character"></textarea>
                                            </div>
                                            <div class="attribute-values-list" id="attributeValuesList">
                                                <!-- Will be populated by JavaScript -->
                                            </div>
                                        </div>
                                        <small class="form-text">These are the predefined values for this attribute. You can add, edit, or remove them.</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Usage</label>
                                        <div class="static-text" id="attributeUsage">Used in 0 products</div>
                                    </div>
                                    
                                    <div class="form-actions">
                                        <button class="btn" id="cancelAttributeBtn">Cancel</button>
                                        <button class="btn primary" id="saveAttributeBtn">Save Attribute</button>
                                    </div>
                                </div>
                                
                                <div class="attribute-placeholder" id="attributePlaceholder">
                                    <span class="icon-attribute"></span>
                                    <p>Select an attribute to view details or click "New Attribute" to create one.</p>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            
            <!-- Delete Confirmation Modal -->
            <div class="modal" id="deleteAttributeModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Delete Attribute</h3>
                        <button class="close-btn" id="closeDeleteAttributeModal">×</button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this attribute? This action cannot be undone.</p>
                        <div class="delete-warnings" id="deleteAttributeWarnings" style="display: none;">
                            <div class="warning-message">
                                <span class="icon-warning"></span>
                                <span id="deleteAttributeWarningText"></span>
                            </div>
                        </div>
                        <div class="modal-actions">
                            <button class="btn" id="cancelDeleteAttributeBtn">Cancel</button>
                            <button class="btn danger" id="confirmDeleteAttributeBtn">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sync Attributes Modal -->
            <div class="modal" id="syncAttributesModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Sync Attributes with WooCommerce</h3>
                        <button class="close-btn" id="closeSyncAttributesModal">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="sync-options">
                            <div class="sync-option">
                                <h4>Import from WooCommerce</h4>
                                <p>Pull attributes from your WooCommerce store into PIM.</p>
                                <button class="btn primary" id="importAttributesBtn">Import Attributes</button>
                            </div>
                            
                            <div class="sync-option">
                                <h4>Export to WooCommerce</h4>
                                <p>Push attribute changes from PIM to your WooCommerce store.</p>
                                <button class="btn primary" id="exportAttributesBtn">Export Attributes</button>
                            </div>
                            
                            <div class="sync-option">
                                <h4>Full Sync</h4>
                                <p>Perform a full two-way synchronization of attributes.</p>
                                <button class="btn primary" id="fullSyncAttributesBtn">Full Sync</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script src="assets/js/attributes.js"></script>
</body>
</html>