<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WooCommerce PIM - Categories</title>
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
                    <li class="active">
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
                    <h2>Categories</h2>
                    <p class="breadcrumb">Home > Categories</p>
                </div>
                
                <div class="header-actions">
                    <div class="search-box">
                        <input type="text" id="categorySearch" placeholder="Search categories...">
                        <button class="search-btn">
                            <span class="icon-search"></span>
                        </button>
                    </div>
                    
                    <div class="actions">
                        <button class="action-btn" id="syncCategoriesBtn">
                            <span class="icon-sync"></span>
                            <span class="action-text">Sync Categories</span>
                        </button>
                        
                        <button class="action-btn primary" id="newCategoryBtn">
                            <span class="icon-plus"></span>
                            <span class="action-text">New Category</span>
                        </button>
                    </div>
                </div>
            </header>
            
            <div class="categories-content">
                <div class="categories-layout">
                    <!-- Categories Tree View -->
                    <section class="categories-tree-section">
                        <div class="card">
                            <div class="card-header">
                                <h3>Category Hierarchy</h3>
                                <div class="card-actions">
                                    <button class="card-action-btn" id="expandAllBtn">
                                        <span class="icon-expand-all"></span>
                                    </button>
                                    <button class="card-action-btn" id="collapseAllBtn">
                                        <span class="icon-collapse-all"></span>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="tree-container" id="categoriesTree">
                                    <!-- Will be populated by JavaScript -->
                                    <div class="loading">Loading categories...</div>
                                </div>
                            </div>
                        </div>
                    </section>
                    
                    <!-- Category Details/Editor -->
                    <section class="category-details-section">
                        <div class="card">
                            <div class="card-header">
                                <h3 id="categoryDetailTitle">Category Details</h3>
                                <div class="card-actions">
                                    <button class="card-action-btn danger" id="deleteCategoryBtn" style="display: none;">
                                        <span class="icon-trash"></span>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="category-form" id="categoryForm" style="display: none;">
                                    <div class="form-group">
                                        <label for="categoryName">Category Name</label>
                                        <input type="text" id="categoryName" class="form-control" placeholder="Enter category name">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="categorySlug">Slug</label>
                                        <input type="text" id="categorySlug" class="form-control" placeholder="Enter category slug">
                                        <small class="form-text">The "slug" is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="categoryParent">Parent Category</label>
                                        <select id="categoryParent" class="form-control">
                                            <option value="0">None (Top Level Category)</option>
                                            <!-- Will be populated by JavaScript -->
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="categoryDescription">Description</label>
                                        <textarea id="categoryDescription" class="form-control" rows="4" placeholder="Enter category description"></textarea>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Category Image</label>
                                        <div class="image-upload" id="categoryImageUpload">
                                            <div class="upload-placeholder" id="categoryImagePlaceholder">
                                                <span class="icon-image"></span>
                                                <p>Click to upload an image</p>
                                            </div>
                                            <div class="upload-preview" id="categoryImagePreview" style="display: none;">
                                                <img src="" id="categoryImagePreviewImg" alt="Category Image">
                                                <div class="image-actions">
                                                    <button class="btn btn-sm danger" id="removeCategoryImage">Remove</button>
                                                </div>
                                            </div>
                                            <input type="file" id="categoryImageInput" accept="image/*" style="display: none;">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Product Count</label>
                                        <div class="static-text" id="categoryProductCount">0 products</div>
                                    </div>
                                    
                                    <div class="form-actions">
                                        <button class="btn" id="cancelCategoryBtn">Cancel</button>
                                        <button class="btn primary" id="saveCategoryBtn">Save Category</button>
                                    </div>
                                </div>
                                
                                <div class="category-placeholder" id="categoryPlaceholder">
                                    <span class="icon-category"></span>
                                    <p>Select a category to view details or click "New Category" to create one.</p>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            
            <!-- Delete Confirmation Modal -->
            <div class="modal" id="deleteCategoryModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Delete Category</h3>
                        <button class="close-btn" id="closeDeleteCategoryModal">×</button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this category? This action cannot be undone.</p>
                        <div class="delete-warnings" id="deleteCategoryWarnings" style="display: none;">
                            <div class="warning-message">
                                <span class="icon-warning"></span>
                                <span id="deleteCategoryWarningText"></span>
                            </div>
                        </div>
                        <div class="modal-actions">
                            <button class="btn" id="cancelDeleteCategoryBtn">Cancel</button>
                            <button class="btn danger" id="confirmDeleteCategoryBtn">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sync Categories Modal -->
            <div class="modal" id="syncCategoriesModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Sync Categories with WooCommerce</h3>
                        <button class="close-btn" id="closeSyncCategoriesModal">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="sync-options">
                            <div class="sync-option">
                                <h4>Import from WooCommerce</h4>
                                <p>Pull categories from your WooCommerce store into PIM.</p>
                                <button class="btn primary" id="importCategoriesBtn">Import Categories</button>
                            </div>
                            
                            <div class="sync-option">
                                <h4>Export to WooCommerce</h4>
                                <p>Push category changes from PIM to your WooCommerce store.</p>
                                <button class="btn primary" id="exportCategoriesBtn">Export Categories</button>
                            </div>
                            
                            <div class="sync-option">
                                <h4>Full Sync</h4>
                                <p>Perform a full two-way synchronization of categories.</p>
                                <button class="btn primary" id="fullSyncCategoriesBtn">Full Sync</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script src="assets/js/categories.js"></script>
</body>
</html>