<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WooCommerce PIM - Products</title>
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
                    <li class="active">
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
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <header class="page-header">
                <div class="header-title">
                    <h2>Products</h2>
                    <p class="breadcrumb">Home > Products</p>
                </div>
                
                <div class="header-actions">
                    <div class="search-box">
                        <input type="text" id="productSearch" placeholder="Search products...">
                        <button class="search-btn">
                            <span class="icon-search"></span>
                        </button>
                    </div>
                    
                    <div class="actions">
                        <button class="action-btn" id="bulkEditBtn">
                            <span class="icon-edit"></span>
                            <span class="action-text">Bulk Edit</span>
                        </button>
                        
                        <button class="action-btn" id="exportBtn">
                            <span class="icon-export"></span>
                            <span class="action-text">Export</span>
                        </button>
                        
                        <button class="action-btn primary" id="newProductBtn">
                            <span class="icon-plus"></span>
                            <span class="action-text">New Product</span>
                        </button>
                    </div>
                </div>
            </header>
            
            <div class="products-content">
                <!-- Product Filters -->
                <section class="filters-section">
                    <div class="card">
                        <div class="card-header">
                            <h3>Filters</h3>
                            <div class="card-actions">
                                <button class="btn" id="clearFiltersBtn">Clear Filters</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="filters-container">
                                <div class="filter-group">
                                    <label>Status</label>
                                    <div class="checkbox-group">
                                        <div class="checkbox">
                                            <input type="checkbox" id="statusPublished" checked>
                                            <label for="statusPublished">Published</label>
                                        </div>
                                        <div class="checkbox">
                                            <input type="checkbox" id="statusDraft" checked>
                                            <label for="statusDraft">Draft</label>
                                        </div>
                                        <div class="checkbox">
                                            <input type="checkbox" id="statusArchived">
                                            <label for="statusArchived">Archived</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="filter-group">
                                    <label>Type</label>
                                    <div class="checkbox-group">
                                        <div class="checkbox">
                                            <input type="checkbox" id="typeSimple" checked>
                                            <label for="typeSimple">Simple</label>
                                        </div>
                                        <div class="checkbox">
                                            <input type="checkbox" id="typeVariable" checked>
                                            <label for="typeVariable">Variable</label>
                                        </div>
                                        <div class="checkbox">
                                            <input type="checkbox" id="typeGrouped" checked>
                                            <label for="typeGrouped">Grouped</label>
                                        </div>
                                        <div class="checkbox">
                                            <input type="checkbox" id="typeExternal" checked>
                                            <label for="typeExternal">External</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="filter-group">
                                    <label>Quality Score</label>
                                    <div class="range-slider">
                                        <div class="range-labels">
                                            <span>0%</span>
                                            <span>100%</span>
                                        </div>
                                        <input type="range" id="qualityScoreMin" min="0" max="100" value="0">
                                        <input type="range" id="qualityScoreMax" min="0" max="100" value="100">
                                        <div class="range-values">
                                            <span id="qualityScoreMinValue">0%</span>
                                            <span id="qualityScoreMaxValue">100%</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="filter-group">
                                    <label>Category</label>
                                    <select id="categoryFilter" class="form-control">
                                        <option value="">All Categories</option>
                                        <!-- Will be populated by JavaScript -->
                                    </select>
                                </div>
                                
                                <div class="filter-group">
                                    <label>Stock Status</label>
                                    <div class="checkbox-group">
                                        <div class="checkbox">
                                            <input type="checkbox" id="stockInStock" checked>
                                            <label for="stockInStock">In Stock</label>
                                        </div>
                                        <div class="checkbox">
                                            <input type="checkbox" id="stockOutOfStock" checked>
                                            <label for="stockOutOfStock">Out of Stock</label>
                                        </div>
                                        <div class="checkbox">
                                            <input type="checkbox" id="stockBackOrder" checked>
                                            <label for="stockBackOrder">On Backorder</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="filter-actions">
                                <button class="btn primary" id="applyFiltersBtn">Apply Filters</button>
                            </div>
                        </div>
                    </div>
                </section>
                
                <!-- Products Table -->
                <section class="products-table-section">
                    <div class="card">
                        <div class="card-header">
                            <div class="products-count">
                                <h3>Products <span id="productsCount">(0)</span></h3>
                            </div>
                            <div class="card-actions">
                                <div class="pagination">
                                    <button class="pagination-btn" id="prevPageBtn" disabled>
                                        <span class="icon-arrow-left"></span>
                                    </button>
                                    <span class="pagination-info" id="paginationInfo">Page 1 of 1</span>
                                    <button class="pagination-btn" id="nextPageBtn" disabled>
                                        <span class="icon-arrow-right"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="data-table" id="productsTable">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="checkbox">
                                                <input type="checkbox" id="selectAllProducts">
                                                <label for="selectAllProducts"></label>
                                            </div>
                                        </th>
                                        <th>Product</th>
                                        <th>SKU</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Quality</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="productsTableBody">
                                    <!-- Will be populated by JavaScript -->
                                    <tr>
                                        <td colspan="9" class="text-center">Loading products...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
            
            <!-- Bulk Edit Modal -->
            <div class="modal" id="bulkEditModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Bulk Edit Products</h3>
                        <button class="close-btn" id="closeBulkEditModal">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="bulk-edit-container">
                            <div class="products-selected">
                                <p><span id="selectedProductsCount">0</span> products selected</p>
                            </div>
                            
                            <div class="bulk-edit-fields">
                                <div class="form-group">
                                    <label>Status</label>
                                    <div class="select-wrapper">
                                        <select id="bulkStatus" class="form-control">
                                            <option value="">- No Change -</option>
                                            <option value="published">Published</option>
                                            <option value="draft">Draft</option>
                                            <option value="archived">Archived</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label>Category</label>
                                    <div class="select-wrapper">
                                        <select id="bulkCategory" class="form-control">
                                            <option value="">- No Change -</option>
                                            <!-- Will be populated by JavaScript -->
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label>Stock Status</label>
                                    <div class="select-wrapper">
                                        <select id="bulkStockStatus" class="form-control">
                                            <option value="">- No Change -</option>
                                            <option value="instock">In Stock</option>
                                            <option value="outofstock">Out of Stock</option>
                                            <option value="onbackorder">On Backorder</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label>Price Adjustment</label>
                                    <div class="price-adjustment">
                                        <div class="select-wrapper">
                                            <select id="bulkPriceAction" class="form-control">
                                                <option value="">- No Change -</option>
                                                <option value="increase_percentage">Increase by Percentage</option>
                                                <option value="increase_fixed">Increase by Fixed Amount</option>
                                                <option value="decrease_percentage">Decrease by Percentage</option>
                                                <option value="decrease_fixed">Decrease by Fixed Amount</option>
                                                <option value="set">Set to Value</option>
                                            </select>
                                        </div>
                                        <div class="input-wrapper">
                                            <input type="number" id="bulkPriceValue" class="form-control" placeholder="Value" step="0.01" min="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="modal-actions">
                                <button class="btn" id="cancelBulkEditBtn">Cancel</button>
                                <button class="btn primary" id="applyBulkEditBtn">Apply Changes</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Delete Confirmation Modal -->
            <div class="modal" id="deleteModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Delete Product</h3>
                        <button class="close-btn" id="closeDeleteModal">×</button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this product? This action cannot be undone.</p>
                        <div class="modal-actions">
                            <button class="btn" id="cancelDeleteBtn">Cancel</button>
                            <button class="btn danger" id="confirmDeleteBtn">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script src="assets/js/products.js"></script>
</body>
</html>