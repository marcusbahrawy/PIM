<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WooCommerce PIM - Edit Product</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="theme-light">
    <div class="app-container">
        <!-- Sidebar - Same as dashboard -->
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
                    <h2 id="productTitle">Edit Product</h2>
                    <p class="breadcrumb">Home > Products > <span id="productBreadcrumb">Edit</span></p>
                </div>
                
                <div class="header-actions">
                    <div class="actions">
                        <button class="action-btn" id="previewBtn">
                            <span class="icon-preview"></span>
                            <span class="action-text">Preview</span>
                        </button>
                        
                        <button class="action-btn" id="cancelBtn">
                            <span class="icon-cancel"></span>
                            <span class="action-text">Cancel</span>
                        </button>
                        
                        <button class="action-btn primary" id="saveBtn">
                            <span class="icon-save"></span>
                            <span class="action-text">Save Changes</span>
                        </button>
                    </div>
                </div>
            </header>
            
            <div class="product-edit-container">
                <div class="product-edit-sidebar">
                    <div class="card">
                        <div class="card-header">
                            <h3>Product Score</h3>
                        </div>
                        <div class="card-body">
                            <div class="score-display" id="scoreDisplay">
                                <div class="score-circle">
                                    <svg viewBox="0 0 36 36" class="score-chart">
                                        <path class="score-circle-bg"
                                            d="M18 2.0845
                                            a 15.9155 15.9155 0 0 1 0 31.831
                                            a 15.9155 15.9155 0 0 1 0 -31.831"
                                        />
                                        <path class="score-circle-fill" id="scoreCircleFill"
                                            stroke-dasharray="0, 100"
                                            d="M18 2.0845
                                            a 15.9155 15.9155 0 0 1 0 31.831
                                            a 15.9155 15.9155 0 0 1 0 -31.831"
                                        />
                                        <text x="18" y="20.35" class="score-text" id="scoreText">0%</text>
                                    </svg>
                                </div>
                                <div class="score-legend">
                                    <div class="score-item">
                                        <span class="score-dot green"></span>
                                        <span class="score-label">80-100%: Excellent</span>
                                    </div>
                                    <div class="score-item">
                                        <span class="score-dot yellow"></span>
                                        <span class="score-label">50-79%: Needs Improvement</span>
                                    </div>
                                    <div class="score-item">
                                        <span class="score-dot red"></span>
                                        <span class="score-label">0-49%: Poor</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3>Improvement Tips</h3>
                        </div>
                        <div class="card-body">
                            <div class="improvement-tips" id="improvementTips">
                                <!-- Will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3>Product Status</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="productStatus">Status</label>
                                <select id="productStatus" class="form-control">
                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                    <option value="archived">Archived</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="productVisibility">Visibility</label>
                                <select id="productVisibility" class="form-control">
                                    <option value="visible">Visible</option>
                                    <option value="catalog">Catalog</option>
                                    <option value="search">Search</option>
                                    <option value="hidden">Hidden</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Last Updated</label>
                                <div id="lastUpdated" class="static-text">-</div>
                            </div>
                            
                            <div class="form-group">
                                <label>Last Synced</label>
                                <div id="lastSynced" class="static-text">-</div>
                            </div>
                            
                            <button class="btn primary mt-2 full-width" id="syncProductBtn">
                                <span class="icon-sync"></span> Sync with WooCommerce
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="product-edit-main">
                    <div class="product-tabs">
                        <div class="tab-nav">
                            <button class="tab-btn active" data-tab="basic">Basic Information</button>
                            <button class="tab-btn" data-tab="description">Description</button>
                            <button class="tab-btn" data-tab="images">Images</button>
                            <button class="tab-btn" data-tab="attributes">Attributes</button>
                            <button class="tab-btn" data-tab="categories">Categories</button>
                            <button class="tab-btn" data-tab="seo">SEO</button>
                            <button class="tab-btn" data-tab="advanced">Advanced</button>
                        </div>
                        
                        <div class="tab-content">
                            <!-- Basic Information Tab -->
                            <div class="tab-pane active" id="basicTab">
                                <div class="card">
                                    <div class="card-header">
                                        <h3>Basic Information</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-row">
                                            <div class="form-group col-md-8">
                                                <label for="productName">Product Name</label>
                                                <input type="text" id="productName" class="form-control" placeholder="Enter product name">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="productSKU">SKU</label>
                                                <input type="text" id="productSKU" class="form-control" placeholder="Enter SKU">
                                            </div>
                                        </div>
                                        
                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label for="productType">Product Type</label>
                                                <select id="productType" class="form-control">
                                                    <option value="simple">Simple Product</option>
                                                    <option value="variable">Variable Product</option>
                                                    <option value="grouped">Grouped Product</option>
                                                    <option value="external">External/Affiliate Product</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="regularPrice">Regular Price</label>
                                                <input type="text" id="regularPrice" class="form-control" placeholder="0.00">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="salePrice">Sale Price</label>
                                                <input type="text" id="salePrice" class="form-control" placeholder="0.00">
                                            </div>
                                        </div>
                                        
                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label for="stockQty">Stock Quantity</label>
                                                <input type="number" id="stockQty" class="form-control" placeholder="0">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="stockStatus">Stock Status</label>
                                                <select id="stockStatus" class="form-control">
                                                    <option value="instock">In stock</option>
                                                    <option value="outofstock">Out of stock</option>
                                                    <option value="onbackorder">On backorder</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="manageStock">Manage Stock</label>
                                                <div class="toggle-switch">
                                                    <input type="checkbox" id="manageStock" class="toggle-input">
                                                    <label for="manageStock" class="toggle-label"></label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label for="weight">Weight (kg)</label>
                                                <input type="text" id="weight" class="form-control" placeholder="0.00">
                                            </div>
                                            <div class="form-group col-md-8">
                                                <label>Dimensions (cm)</label>
                                                <div class="input-group">
                                                    <input type="text" id="length" class="form-control" placeholder="Length">
                                                    <input type="text" id="width" class="form-control" placeholder="Width">
                                                    <input type="text" id="height" class="form-control" placeholder="Height">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Description Tab -->
                            <div class="tab-pane" id="descriptionTab">
                                <div class="card">
                                    <div class="card-header">
                                        <h3>Product Description</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="shortDescription">Short Description</label>
                                            <textarea id="shortDescription" class="form-control" rows="3" placeholder="Enter a short summary of the product..."></textarea>
                                            <small class="form-text text-muted">
                                                This short description appears in product listings. Keep it concise.
                                            </small>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="fullDescription">Full Description</label>
                                            <textarea id="fullDescription" class="wysiwyg-editor" rows="10" placeholder="Enter detailed product description..."></textarea>
                                            <small class="form-text text-muted">
                                                The full description appears on the product page. Use formatting to highlight key features.
                                            </small>
                                        </div>
                                        
                                        <div class="description-tips">
                                            <h4>Writing Tips</h4>
                                            <ul>
                                                <li>Use bullet points to highlight key features</li>
                                                <li>Add formatted headings for different sections</li>
                                                <li>Include product specifications and benefits</li>
                                                <li>Aim for at least 300 words for better SEO</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Images Tab -->
                            <div class="tab-pane" id="imagesTab">
                                <div class="card">
                                    <div class="card-header">
                                        <h3>Product Images</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="image-uploader">
                                            <div class="featured-image">
                                                <h4>Featured Image</h4>
                                                <div class="upload-area" id="featuredImageUpload">
                                                    <div class="upload-placeholder">
                                                        <span class="icon-image"></span>
                                                        <p>Drag & drop your image here or click to browse</p>
                                                    </div>
                                                    <div class="upload-preview" id="featuredImagePreview" style="display: none;">
                                                        <img src="" alt="Featured Image Preview">
                                                        <div class="image-actions">
                                                            <button class="btn btn-sm danger" id="removeFeaturedImage">Remove</button>
                                                        </div>
                                                    </div>
                                                    <input type="file" id="featuredImageInput" accept="image/*" style="display: none;">
                                                </div>
                                            </div>
                                            
                                            <div class="gallery-images">
                                                <h4>Gallery Images</h4>
                                                <div class="upload-area" id="galleryImagesUpload">
                                                    <div class="upload-placeholder">
                                                        <span class="icon-images"></span>
                                                        <p>Drag & drop your images here or click to browse</p>
                                                        <small>You can upload multiple images at once</small>
                                                    </div>
                                                    <input type="file" id="galleryImagesInput" accept="image/*" multiple style="display: none;">
                                                </div>
                                                
                                                <div class="gallery-preview" id="galleryPreview">
                                                    <!-- Will be populated by JavaScript -->
                                                </div>
                                            </div>
                                            
                                            <div class="image-seo">
                                                <h4>Image SEO</h4>
                                                <p>Add descriptive titles and alt text to improve SEO and accessibility.</p>
                                                
                                                <div class="image-seo-table">
                                                    <table class="data-table">
                                                        <thead>
                                                            <tr>
                                                                <th>Image</th>
                                                                <th>Title</th>
                                                                <th>Alt Text</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="imageSeoTableBody">
                                                            <!-- Will be populated by JavaScript -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Attributes Tab -->
                            <div class="tab-pane" id="attributesTab">
                                <div class="card">
                                    <div class="card-header">
                                        <h3>Product Attributes</h3>
                                        <div class="card-actions">
                                            <button class="btn" id="addAttributeBtn">
                                                <span class="icon-plus"></span> Add Attribute
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="attributes-container" id="attributesContainer">
                                            <!-- Will be populated by JavaScript -->
                                            <div class="empty-state" id="attributesEmptyState">
                                                <span class="icon-attribute"></span>
                                                <p>No attributes added yet. Click "Add Attribute" to begin.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card mt-3" id="variationsCard" style="display: none;">
                                    <div class="card-header">
                                        <h3>Product Variations</h3>
                                        <div class="card-actions">
                                            <button class="btn" id="addVariationBtn">
                                                <span class="icon-plus"></span> Add Variation
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="variations-container" id="variationsContainer">
                                            <!-- Will be populated by JavaScript -->
                                            <div class="empty-state" id="variationsEmptyState">
                                                <span class="icon-variation"></span>
                                                <p>No variations added yet. Click "Add Variation" to begin.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Categories Tab -->
                            <div class="tab-pane" id="categoriesTab">
                                <div class="card">
                                    <div class="card-header">
                                        <h3>Product Categories</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="categories-selector">
                                            <div class="form-group">
                                                <label>Select Categories</label>
                                                <p class="form-text text-muted">
                                                    Assign this product to categories to help customers find it.
                                                </p>
                                                
                                                <div class="categories-tree" id="categoriesTree">
                                                    <!-- Will be populated by JavaScript -->
                                                    <div class="loading">Loading categories...</div>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group mt-3">
                                                <label>Primary Category</label>
                                                <select id="primaryCategory" class="form-control">
                                                    <option value="">Select a primary category</option>
                                                    <!-- Will be populated by JavaScript -->
                                                </select>
                                                <small class="form-text text-muted">
                                                    The primary category is used for breadcrumbs and URL structure.
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- SEO Tab -->
                            <div class="tab-pane" id="seoTab">
                                <div class="card">
                                    <div class="card-header">
                                        <h3>SEO Settings</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="metaTitle">Meta Title</label>
                                            <input type="text" id="metaTitle" class="form-control" placeholder="Enter meta title">
                                            <div class="char-counter">
                                                <span id="metaTitleCount">0</span>/70 characters
                                            </div>
                                            <small class="form-text text-muted">
                                                The title displayed in search engine results. Optimal length: 50-60 characters.
                                            </small>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="metaDescription">Meta Description</label>
                                            <textarea id="metaDescription" class="form-control" rows="3" placeholder="Enter meta description"></textarea>
                                            <div class="char-counter">
                                                <span id="metaDescCount">0</span>/160 characters
                                            </div>
                                            <small class="form-text text-muted">
                                                The description displayed in search engine results. Optimal length: 120-160 characters.
                                            </small>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="focusKeyword">Focus Keyword</label>
                                            <input type="text" id="focusKeyword" class="form-control" placeholder="Enter primary keyword">
                                            <small class="form-text text-muted">
                                                The main keyword you want this product to rank for in search engines.
                                            </small>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="metaKeywords">Meta Keywords</label>
                                            <input type="text" id="metaKeywords" class="form-control" placeholder="keyword1, keyword2, keyword3">
                                            <small class="form-text text-muted">
                                                Optional keywords separated by commas. Less important for SEO nowadays.
                                            </small>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="canonicalUrl">Canonical URL</label>
                                            <input type="text" id="canonicalUrl" class="form-control" placeholder="https://example.com/product-url">
                                            <small class="form-text text-muted">
                                                If this product appears on multiple URLs, set the canonical URL to avoid duplicate content.
                                            </small>
                                        </div>
                                        
                                        <div class="seo-preview">
                                            <h4>Search Result Preview</h4>
                                            <div class="seo-preview-box">
                                                <div class="seo-preview-title" id="seoPreviewTitle">Product Title</div>
                                                <div class="seo-preview-url" id="seoPreviewUrl">example.com/product</div>
                                                <div class="seo-preview-description" id="seoPreviewDescription">
                                                    This is how your product will appear in search engine results.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Advanced Tab -->
                            <div class="tab-pane" id="advancedTab">
                                <div class="card">
                                    <div class="card-header">
                                        <h3>Advanced Settings</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="purchaseNote">Purchase Note</label>
                                                <textarea id="purchaseNote" class="form-control" rows="3" placeholder="Enter purchase note"></textarea>
                                                <small class="form-text text-muted">
                                                    This note will be shown to customers after they purchase the product.
                                                </small>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="menuOrder">Menu Order</label>
                                                <input type="number" id="menuOrder" class="form-control" placeholder="0">
                                                <small class="form-text text-muted">
                                                    Custom ordering position. Lower numbers appear first.
                                                </small>
                                            </div>
                                        </div>
                                        
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="taxStatus">Tax Status</label>
                                                <select id="taxStatus" class="form-control">
                                                    <option value="taxable">Taxable</option>
                                                    <option value="shipping">Shipping only</option>
                                                    <option value="none">None</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="taxClass">Tax Class</label>
                                                <select id="taxClass" class="form-control">
                                                    <option value="">Standard</option>
                                                    <option value="reduced-rate">Reduced Rate</option>
                                                    <option value="zero-rate">Zero Rate</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="shippingClass">Shipping Class</label>
                                                <select id="shippingClass" class="form-control">
                                                    <option value="">No shipping class</option>
                                                    <!-- Will be populated by JavaScript -->
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Sold Individually</label>
                                                <div class="toggle-switch">
                                                    <input type="checkbox" id="soldIndividually" class="toggle-input">
                                                    <label for="soldIndividually" class="toggle-label"></label>
                                                </div>
                                                <small class="form-text text-muted">
                                                    Limit purchases to 1 item per order
                                                </small>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Custom Product Attributes</label>
                                            <div class="custom-attributes" id="customAttributes">
                                                <!-- Will be populated by JavaScript -->
                                            </div>
                                            <button class="btn mt-2" id="addCustomAttributeBtn">
                                                <span class="icon-plus"></span> Add Custom Attribute
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Attribute Modal -->
    <div class="modal" id="attributeModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add Attribute</h3>
                <button class="close-btn" id="closeAttributeModal">×</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="attributeName">Attribute Name</label>
                    <select id="attributeSelect" class="form-control">
                        <option value="">Select an attribute</option>
                        <!-- Will be populated by JavaScript -->
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="attributeValues">Values</label>
                    <textarea id="attributeValues" class="form-control" rows="3" placeholder="Enter values separated by | or press Enter after each value"></textarea>
                </div>
                
                <div class="form-group">
                    <div class="checkbox">
                        <input type="checkbox" id="visibleOnProduct" checked>
                        <label for="visibleOnProduct">Visible on product page</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="checkbox">
                        <input type="checkbox" id="usedForVariations">
                        <label for="usedForVariations">Used for variations</label>
                    </div>
                </div>
                
                <div class="modal-actions">
                    <button class="btn" id="cancelAttributeBtn">Cancel</button>
                    <button class="btn primary" id="saveAttributeBtn">Add Attribute</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="assets/js/tinymce/tinymce.min.js"></script>
    <script src="assets/js/product-edit.js"></script>
</body>
</html>