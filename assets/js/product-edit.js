/**
 * Setup image upload handlers
 */
function setupImageUploadHandlers() {
    // Featured image upload
    const featuredImageUpload = document.getElementById('featuredImageUpload');
    const featuredImageInput = document.getElementById('featuredImageInput');
    const featuredImagePreview = document.getElementById('featuredImagePreview');
    const removeFeaturedImage = document.getElementById('removeFeaturedImage');
    
    if (featuredImageUpload && featuredImageInput) {
        featuredImageUpload.addEventListener('click', function(e) {
            if (e.target !== removeFeaturedImage && e.target !== removeFeaturedImage.querySelector('span')) {
                featuredImageInput.click();
            }
        });
        
        featuredImageUpload.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('drag-over');
        });
        
        featuredImageUpload.addEventListener('dragleave', function() {
            this.classList.remove('drag-over');
        });
        
        featuredImageUpload.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            
            if (e.dataTransfer.files.length) {
                featuredImageInput.files = e.dataTransfer.files;
                handleFeaturedImageUpload(e.dataTransfer.files[0]);
            }
        });
        
        featuredImageInput.addEventListener('change', function() {
            if (this.files.length) {
                handleFeaturedImageUpload(this.files[0]);
            }
        });
    }
    
    if (removeFeaturedImage) {
        removeFeaturedImage.addEventListener('click', function(e) {
            e.stopPropagation();
            featuredImagePreview.style.display = 'none';
            featuredImageInput.value = '';
            featuredImageUpload.querySelector('.upload-placeholder').style.display = 'block';
            
            // Remove featured image from product data
            if (productData.images) {
                productData.images = productData.images.filter(img => !img.is_featured);
            }
        });
    }
    
    // Gallery images upload
    const galleryImagesUpload = document.getElementById('galleryImagesUpload');
    const galleryImagesInput = document.getElementById('galleryImagesInput');
    
    if (galleryImagesUpload && galleryImagesInput) {
        galleryImagesUpload.addEventListener('click', function() {
            galleryImagesInput.click();
        });
        
        galleryImagesUpload.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('drag-over');
        });
        
        galleryImagesUpload.addEventListener('dragleave', function() {
            this.classList.remove('drag-over');
        });
        
        galleryImagesUpload.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            
            if (e.dataTransfer.files.length) {
                galleryImagesInput.files = e.dataTransfer.files;
                handleGalleryImagesUpload(e.dataTransfer.files);
            }
        });
        
        galleryImagesInput.addEventListener('change', function() {
            if (this.files.length) {
                handleGalleryImagesUpload(this.files);
            }
        });
    }
}

/**
 * Handle featured image upload
 * 
 * @param {File} file Uploaded file
 */
function handleFeaturedImageUpload(file) {
    const featuredImagePreview = document.getElementById('featuredImagePreview');
    const imgElement = featuredImagePreview.querySelector('img');
    
    if (!featuredImagePreview || !imgElement) return;
    
    // Show loading state
    featuredImagePreview.style.display = 'block';
    featuredImageUpload.querySelector('.upload-placeholder').style.display = 'none';
    imgElement.src = 'assets/images/loading.gif';
    
    // In a real app, this would upload the file to the server
    // For demo, we'll use FileReader to show a preview
    const reader = new FileReader();
    
    reader.onload = function(e) {
        imgElement.src = e.target.result;
        
        // Add to product data
        if (!productData.images) {
            productData.images = [];
        }
        
        // Remove any existing featured image
        productData.images = productData.images.filter(img => !img.is_featured);
        
        // Add new featured image
        productData.images.push({
            image_id: 'temp_' + Date.now(), // Temporary ID
            image_url: e.target.result,
            alt_text: '',
            title: file.name,
            is_featured: true
        });
        
        // Update image SEO table
        updateImageSeoTable();
    };
    
    reader.readAsDataURL(file);
}

/**
 * Handle gallery images upload
 * 
 * @param {FileList} files Uploaded files
 */
function handleGalleryImagesUpload(files) {
    const galleryPreview = document.getElementById('galleryPreview');
    
    if (!galleryPreview) return;
    
    // In a real app, this would upload the files to the server
    // For demo, we'll use FileReader to show previews
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const reader = new FileReader();
        
        reader.onload = function(e) {
            // Create preview element
            const imagePreview = document.createElement('div');
            imagePreview.className = 'gallery-item';
            imagePreview.innerHTML = `
                <div class="gallery-image">
                    <img src="${e.target.result}" alt="${file.name}">
                </div>
                <div class="gallery-actions">
                    <button type="button" class="btn-icon remove-gallery-image">
                        <span class="icon-trash"></span>
                    </button>
                </div>
            `;
            
            // Add remove event
            const removeBtn = imagePreview.querySelector('.remove-gallery-image');
            if (removeBtn) {
                removeBtn.addEventListener('click', function() {
                    imagePreview.remove();
                    
                    // Remove from product data
                    if (productData.images) {
                        const imageIndex = productData.images.findIndex(img => 
                            img.image_url === e.target.result && !img.is_featured);
                        
                        if (imageIndex !== -1) {
                            productData.images.splice(imageIndex, 1);
                            
                            // Update image SEO table
                            updateImageSeoTable();
                        }
                    }
                });
            }
            
            galleryPreview.appendChild(imagePreview);
            
            // Add to product data
            if (!productData.images) {
                productData.images = [];
            }
            
            productData.images.push({
                image_id: 'temp_' + Date.now() + '_' + i, // Temporary ID
                image_url: e.target.result,
                alt_text: '',
                title: file.name,
                is_featured: false
            });
            
            // Update image SEO table
            updateImageSeoTable();
        };
        
        reader.readAsDataURL(file);
    }
}

/**
 * Populate images from product data
 * 
 * @param {Array} images Product images
 */
function populateImages(images) {
    if (!images || !images.length) return;
    
    // Handle featured image
    const featuredImage = images.find(img => img.is_featured);
    if (featuredImage) {
        const featuredImagePreview = document.getElementById('featuredImagePreview');
        const imgElement = featuredImagePreview.querySelector('img');
        
        if (featuredImagePreview && imgElement) {
            featuredImagePreview.style.display = 'block';
            document.querySelector('#featuredImageUpload .upload-placeholder').style.display = 'none';
            imgElement.src = featuredImage.image_url;
        }
    }
    
    // Handle gallery images
    const galleryImages = images.filter(img => !img.is_featured);
    if (galleryImages.length) {
        const galleryPreview = document.getElementById('galleryPreview');
        
        if (galleryPreview) {
            galleryImages.forEach(image => {
                const imagePreview = document.createElement('div');
                imagePreview.className = 'gallery-item';
                imagePreview.innerHTML = `
                    <div class="gallery-image">
                        <img src="${image.image_url}" alt="${image.alt_text}">
                    </div>
                    <div class="gallery-actions">
                        <button type="button" class="btn-icon remove-gallery-image">
                            <span class="icon-trash"></span>
                        </button>
                    </div>
                `;
                
                // Add remove event
                const removeBtn = imagePreview.querySelector('.remove-gallery-image');
                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        imagePreview.remove();
                        
                        // Remove from product data
                        if (productData.images) {
                            const imageIndex = productData.images.findIndex(img => 
                                img.image_id === image.image_id);
                            
                            if (imageIndex !== -1) {
                                productData.images.splice(imageIndex, 1);
                                
                                // Update image SEO table
                                updateImageSeoTable();
                            }
                        }
                    });
                }
                
                galleryPreview.appendChild(imagePreview);
            });
        }
    }
    
    // Update image SEO table
    updateImageSeoTable();
}

/**
 * Update image SEO table
 */
function updateImageSeoTable() {
    const tableBody = document.getElementById('imageSeoTableBody');
    
    if (!tableBody) return;
    
    tableBody.innerHTML = '';
    
    if (!productData.images || !productData.images.length) {
        const row = document.createElement('tr');
        row.innerHTML = '<td colspan="3" class="text-center">No images added yet</td>';
        tableBody.appendChild(row);
        return;
    }
    
    productData.images.forEach(image => {
        const row = document.createElement('tr');
        
        row.innerHTML = `
            <td>
                <div class="thumb-image">
                    <img src="${image.image_url}" alt="${image.alt_text || 'Product image'}">
                </div>
            </td>
            <td>
                <input type="text" class="form-control" value="${image.title || ''}" 
                       placeholder="Image title" data-id="${image.image_id}" data-field="title">
            </td>
            <td>
                <input type="text" class="form-control" value="${image.alt_text || ''}" 
                       placeholder="Alt text" data-id="${image.image_id}" data-field="alt_text">
            </td>
        `;
        
        // Add change event listeners to inputs
        const inputs = row.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                const imageId = this.dataset.id;
                const field = this.dataset.field;
                const value = this.value;
                
                // Update product data
                const imageIndex = productData.images.findIndex(img => img.image_id === imageId);
                
                if (imageIndex !== -1) {
                    productData.images[imageIndex][field] = value;
                }
            });
        });
        
        tableBody.appendChild(row);
    });
}

/**
 * Setup SEO preview handlers
 */
function setupSeoPreviewHandlers() {
    const metaTitleInput = document.getElementById('metaTitle');
    const metaDescInput = document.getElementById('metaDescription');
    const metaTitleCount = document.getElementById('metaTitleCount');
    const metaDescCount = document.getElementById('metaDescCount');
    
    // Update character counts
    if (metaTitleInput && metaTitleCount) {
        metaTitleInput.addEventListener('input', function() {
            metaTitleCount.textContent = this.value.length;
            updateSeoPreview();
        });
    }
    
    if (metaDescInput && metaDescCount) {
        metaDescInput.addEventListener('input', function() {
            metaDescCount.textContent = this.value.length;
            updateSeoPreview();
        });
    }
}

/**
 * Update SEO preview
 */
function updateSeoPreview() {
    const titleInput = document.getElementById('metaTitle');
    const descInput = document.getElementById('metaDescription');
    const previewTitle = document.getElementById('seoPreviewTitle');
    const previewDesc = document.getElementById('seoPreviewDescription');
    const previewUrl = document.getElementById('seoPreviewUrl');
    
    if (titleInput && previewTitle) {
        const title = titleInput.value || document.getElementById('productName').value || 'Product Title';
        previewTitle.textContent = title;
    }
    
    if (descInput && previewDesc) {
        const desc = descInput.value || document.getElementById('shortDescription').value || 'Product description goes here. Add a compelling meta description to improve click-through rates from search results.';
        previewDesc.textContent = desc;
    }
    
    if (previewUrl) {
        const productName = document.getElementById('productName').value || 'product-name';
        const slug = productName.toLowerCase().replace(/[^a-z0-9]+/g, '-');
        previewUrl.textContent = `example.com/products/${slug}`;
    }
}

/**
 * Open attribute modal
 * 
 * @param {Object} attribute Attribute data (optional, for editing)
 */
function openAttributeModal(attribute = null) {
    const modal = document.getElementById('attributeModal');
    const modalTitle = modal.querySelector('.modal-header h3');
    const saveBtn = document.getElementById('saveAttributeBtn');
    const attributeSelect = document.getElementById('attributeSelect');
    const attributeValues = document.getElementById('attributeValues');
    const visibleOnProduct = document.getElementById('visibleOnProduct');
    const usedForVariations = document.getElementById('usedForVariations');
    
    // Reset form
    attributeValues.value = '';
    visibleOnProduct.checked = true;
    usedForVariations.checked = false;
    
    // Remove any existing new attribute name field
    const nameField = document.getElementById('newAttributeName')?.parentNode;
    if (nameField) {
        nameField.remove();
    }
    
    if (attribute) {
        // Edit mode
        modalTitle.textContent = 'Edit Attribute';
        saveBtn.textContent = 'Update Attribute';
        
        // Set attribute select
        if (attribute.attribute_id) {
            attributeSelect.value = attribute.attribute_id;
        } else if (attribute.name) {
            // This is a custom attribute
            attributeSelect.value = 'new';
            
            // Add name field
            const nameFieldDiv = document.createElement('div');
            nameFieldDiv.className = 'form-group mt-2';
            nameFieldDiv.innerHTML = `
                <label for="newAttributeName">Attribute Name</label>
                <input type="text" id="newAttributeName" class="form-control" value="${attribute.name}" placeholder="Enter attribute name">
            `;
            
            attributeSelect.parentNode.appendChild(nameFieldDiv);
        }
        
        // Set values
        if (attribute.values && attribute.values.length) {
            attributeValues.value = attribute.values.join(' | ');
        }
        
        // Set visibility and variation flags
        visibleOnProduct.checked = attribute.is_visible !== false;
        usedForVariations.checked = attribute.is_variation === true;
        
        // Store attribute ID for later
        saveBtn.dataset.attributeId = attribute.attribute_id || attribute.name;
    } else {
        // Add mode
        modalTitle.textContent = 'Add Attribute';
        saveBtn.textContent = 'Add Attribute';
        
        // Reset select to first option
        attributeSelect.selectedIndex = 0;
        
        // Clear attribute ID
        delete saveBtn.dataset.attributeId;
    }
    
    // Show modal
    modal.classList.add('open');
}

/**
 * Save attribute from modal
 */
function saveAttribute() {
    const attributeSelect = document.getElementById('attributeSelect');
    const newAttributeName = document.getElementById('newAttributeName');
    const attributeValues = document.getElementById('attributeValues');
    const visibleOnProduct = document.getElementById('visibleOnProduct');
    const usedForVariations = document.getElementById('usedForVariations');
    const saveBtn = document.getElementById('saveAttributeBtn');
    
    // Validate inputs
    if (attributeSelect.value === '' && (!newAttributeName || !newAttributeName.value)) {
        alert('Please select an attribute or enter a new attribute name.');
        return;
    }
    
    if (!attributeValues.value) {
        alert('Please enter at least one attribute value.');
        return;
    }
    
    // Parse values
    let values = attributeValues.value.split(/\s*\|\s*|\s*,\s*|\s*;\s*|\s*\n\s*/);
    values = values.filter(val => val.trim() !== '').map(val => val.trim());
    
    if (values.length === 0) {
        alert('Please enter at least one attribute value.');
        return;
    }
    
    // Prepare attribute data
    const attributeData = {
        values: values,
        is_visible: visibleOnProduct.checked,
        is_variation: usedForVariations.checked
    };
    
    if (attributeSelect.value === 'new') {
        // New custom attribute
        if (!newAttributeName || !newAttributeName.value) {
            alert('Please enter a name for the new attribute.');
            return;
        }
        
        attributeData.name = newAttributeName.value;
    } else {
        // Existing attribute
        attributeData.attribute_id = attributeSelect.value;
        
        // Get attribute details
        const attributeDetails = attributes.find(a => a.attribute_id == attributeSelect.value);
        if (attributeDetails) {
            attributeData.name = attributeDetails.attribute_label;
        }
    }
    
    // Check if we're editing an existing attribute
    if (saveBtn.dataset.attributeId) {
        attributeData.attribute_id = saveBtn.dataset.attributeId;
        
        // Remove old attribute from UI
        const oldCard = document.querySelector(`.attribute-card[data-id="${saveBtn.dataset.attributeId}"]`);
        if (oldCard) {
            oldCard.remove();
        }
        
        // Remove old attribute from product data
        if (productData.attributes) {
            const index = productData.attributes.findIndex(attr => 
                attr.attribute_id == saveBtn.dataset.attributeId || 
                attr.name == saveBtn.dataset.attributeId);
            
            if (index !== -1) {
                productData.attributes.splice(index, 1);
            }
        }
    }
    
    // Add to product data
    if (!productData.attributes) {
        productData.attributes = [];
    }
    
    productData.attributes.push(attributeData);
    
    // Add to UI
    addAttributeToUI(attributeData);
    
    // Close modal
    closeModal(document.getElementById('attributeModal'));
}

/**
 * Toggle visibility of the variations card
 * 
 * @param {boolean} show Whether to show the card
 */
function toggleVariationsCard(show) {
    const variationsCard = document.getElementById('variationsCard');
    if (variationsCard) {
        variationsCard.style.display = show ? 'block' : 'none';
    }
}

/**
 * Toggle visibility of stock-related fields
 * 
 * @param {boolean} show Whether to show the fields
 */
function toggleStockFields(show) {
    const stockQty = document.getElementById('stockQty');
    const stockStatus = document.getElementById('stockStatus');
    
    if (stockQty) {
        stockQty.disabled = !show;
    }
    
    if (stockStatus) {
        stockStatus.disabled = show;
    }
}

/**
 * Update variation options based on attributes
 */
function updateVariationOptions() {
    // This function would create variation options based on variation attributes
    // For the demo, we'll just log a message
    console.log('Updating variation options');
}

/**
 * Initialize theme based on stored preference
 */
function initTheme() {
    const theme = localStorage.getItem('theme') || 'light';
    const themeToggle = document.getElementById('themeToggle');
    
    if (theme === 'dark') {
        document.body.classList.add('theme-dark');
        if (themeToggle) themeToggle.checked = true;
    } else {
        document.body.classList.remove('theme-dark');
        if (themeToggle) themeToggle.checked = false;
    }
}

/**
 * Toggle between light and dark theme
 */
function toggleTheme() {
    const isDark = document.body.classList.toggle('theme-dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
}

/**
 * Toggle sidebar collapsed state
 */
function toggleSidebar() {
    document.body.classList.toggle('sidebar-collapsed');
    
    // Store preference
    const isCollapsed = document.body.classList.contains('sidebar-collapsed');
    localStorage.setItem('sidebar-collapsed', isCollapsed ? 'true' : 'false');
}

/**
 * Toggle modal visibility
 * 
 * @param {HTMLElement} modal Modal element
 * @param {boolean} show Whether to show or hide the modal
 */
function closeModal(modal) {
    if (modal) {
        modal.classList.remove('open');
    }
}

/**
 * Switch active tab
 * 
 * @param {string} tabId Tab ID to activate
 */
function switchTab(tabId) {
    // Update tab buttons
    const tabButtons = document.querySelectorAll('.tab-btn');
    tabButtons.forEach(btn => {
        if (btn.getAttribute('data-tab') === tabId) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });
    
    // Update tab panes
    const tabPanes = document.querySelectorAll('.tab-pane');
    tabPanes.forEach(pane => {
        pane.classList.remove('active');
    });
    
    // Activate target pane
    const targetPane = document.getElementById(`${tabId}Tab`);
    if (targetPane) {
        targetPane.classList.add('active');
    }
}

/**
 * Check if there are unsaved changes
 * 
 * @returns {boolean} Whether there are unsaved changes
 */
function hasUnsavedChanges() {
    // Compare current form data with original data
    const formData = getFormData();
    
    // Simple comparison for demo purposes
    // In a real app, we would do a deep comparison
    return JSON.stringify(formData) !== JSON.stringify(originalData);
}

/**
 * Get form data from all inputs
 * 
 * @returns {Object} Form data
 */
function getFormData() {
    // Collect basic information
    const formData = {
        name: document.getElementById('productName').value,
        sku: document.getElementById('productSKU').value,
        type: document.getElementById('productType').value,
        regular_price: document.getElementById('regularPrice').value,
        sale_price: document.getElementById('salePrice').value,
        manage_stock: document.getElementById('manageStock').checked,
        stock_quantity: document.getElementById('stockQty').value,
        stock_status: document.getElementById('stockStatus').value,
        weight: document.getElementById('weight').value,
        dimensions: {
            length: document.getElementById('length').value,
            width: document.getElementById('width').value,
            height: document.getElementById('height').value
        },
        status: document.getElementById('productStatus').value,
        visibility: document.getElementById('productVisibility').value
    };
    
    // Get description content
    if (typeof tinymce !== 'undefined') {
        const editor = tinymce.get('fullDescription');
        if (editor) {
            formData.description = editor.getContent();
        } else {
            formData.description = document.getElementById('fullDescription').value;
        }
    } else {
        formData.description = document.getElementById('fullDescription').value;
    }
    
    formData.short_description = document.getElementById('shortDescription').value;
    
    // Get SEO data
    formData.seo = {
        meta_title: document.getElementById('metaTitle').value,
        meta_description: document.getElementById('metaDescription').value,
        focus_keyword: document.getElementById('focusKeyword').value,
        meta_keywords: document.getElementById('metaKeywords').value,
        canonical_url: document.getElementById('canonicalUrl').value
    };
    
    // Get advanced settings
    formData.purchase_note = document.getElementById('purchaseNote').value;
    formData.menu_order = document.getElementById('menuOrder').value;
    formData.tax_status = document.getElementById('taxStatus').value;
    formData.tax_class = document.getElementById('taxClass').value;
    formData.shipping_class = document.getElementById('shippingClass').value;
    formData.sold_individually = document.getElementById('soldIndividually').checked;
    
    // Get selected categories
    formData.categories = [];
    const categoryCheckboxes = document.querySelectorAll('.category-checkbox:checked');
    categoryCheckboxes.forEach(checkbox => {
        formData.categories.push({
            category_id: checkbox.value,
            name: checkbox.dataset.name
        });
    });
    
    // Get primary category
    const primaryCategory = document.getElementById('primaryCategory').value;
    if (primaryCategory) {
        formData.primary_category = primaryCategory;
    }
    
    // Images and attributes are already in productData
    formData.images = productData.images || [];
    formData.attributes = productData.attributes || [];
    
    return formData;
}

/**
 * Save product data
 */
function saveProduct() {
    // Get form data
    const formData = getFormData();
    
    // Validate required fields
    if (!formData.name) {
        alert('Product name is required');
        switchTab('basic');
        document.getElementById('productName').focus();
        return;
    }
    
    // Update product data
    Object.assign(productData, formData);
    
    // In a real app, this would send data to the server
    console.log('Saving product data:', productData);
    
    // Show loading indicator
    const saveBtn = document.getElementById('saveBtn');
    if (saveBtn) {
        const originalText = saveBtn.innerHTML;
        saveBtn.innerHTML = '<span class="icon-loading"></span> Saving...';
        saveBtn.disabled = true;
        
        // Simulate server request
        setTimeout(() => {
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;
            
            // Show success message
            showNotification('Product saved successfully', 'success');
            
            // Update original data to match current data
            originalData = JSON.parse(JSON.stringify(productData));
            
            // If this is a new product, redirect to edit mode
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('action') === 'new' && productData.product_id) {
                window.location.href = `product-edit.php?id=${productData.product_id}`;
            }
        }, 1000);
    }
}

/**
 * Preview product
 */
function previewProduct() {
    // In a real app, this would open a preview in a new tab
    // For demo, we'll just show an alert
    alert('Preview functionality would open a new tab with a preview of the product.');
}

/**
 * Sync product with WooCommerce
 */
function syncProduct() {
    // In a real app, this would sync with WooCommerce
    // For demo, we'll just show an alert
    const syncBtn = document.getElementById('syncProductBtn');
    if (syncBtn) {
        const originalText = syncBtn.innerHTML;
        syncBtn.innerHTML = '<span class="icon-loading"></span> Syncing...';
        syncBtn.disabled = true;
        
        // Simulate server request
        setTimeout(() => {
            syncBtn.innerHTML = originalText;
            syncBtn.disabled = false;
            
            // Show success message
            showNotification('Product synced with WooCommerce', 'success');
            
            // Update last synced time
            const lastSyncedEl = document.getElementById('lastSynced');
            if (lastSyncedEl) {
                const now = new Date();
                lastSyncedEl.textContent = now.toLocaleDateString() + ' ' + 
                                         now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            }
        }, 1500);
    }
}

/**
 * Show a notification
 * 
 * @param {string} message Notification message
 * @param {string} type Notification type (success, error, warning, info)
 */
function showNotification(message, type = 'info') {
    // This is a placeholder. In a real app, this would display a notification.
    console.log(`Notification (${type}): ${message}`);
    
    // Example implementation using alert (would be replaced with proper UI component)
    alert(`${type.toUpperCase()}: ${message}`);
}/**
 * WooCommerce PIM System
 * Product Edit JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize the page
    initProductEdit();
    
    // Set up event listeners
    setupEventListeners();
    
    // Initialize rich text editor
    initRichTextEditor();
});

let productData = {}; // Will store the product data
let originalData = {}; // Store original data to track changes
let categories = []; // Will store categories data
let attributes = []; // Will store attributes data

/**
 * Initialize product edit page
 */
function initProductEdit() {
    // Get product ID from URL
    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get('id');
    const action = urlParams.get('action'); // 'new' or undefined (edit)
    
    // Initialize theme based on stored preference
    initTheme();
    
    if (productId) {
        // Load existing product
        fetchProductData(productId)
            .then(data => {
                productData = data;
                originalData = JSON.parse(JSON.stringify(data)); // Deep copy
                populateProductForm(data);
                updateProductScore(data.rating_score);
                updateImprovementTips(data.improvement_tips);
            })
            .catch(error => {
                console.error('Error loading product data:', error);
                showNotification('Error loading product data. Please try again.', 'error');
            });
    } else if (action === 'new') {
        // Initialize new product form
        initNewProductForm();
    } else {
        // Redirect to products page if no ID and not a new product
        window.location.href = 'products.php';
    }
    
    // Load categories regardless of product ID
    fetchCategories()
        .then(data => {
            categories = data;
            populateCategoriesTree(data);
        })
        .catch(error => {
            console.error('Error loading categories:', error);
        });
    
    // Load attributes
    fetchAttributes()
        .then(data => {
            attributes = data;
            populateAttributesSelect(data);
        })
        .catch(error => {
            console.error('Error loading attributes:', error);
        });
}

/**
 * Set up event listeners for the page
 */
function setupEventListeners() {
    // Theme toggle
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        themeToggle.addEventListener('change', toggleTheme);
    }
    
    // Sidebar toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', toggleSidebar);
    }
    
    // Tab navigation
    const tabButtons = document.querySelectorAll('.tab-btn');
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tab = this.getAttribute('data-tab');
            switchTab(tab);
        });
    });
    
    // Save button
    const saveBtn = document.getElementById('saveBtn');
    if (saveBtn) {
        saveBtn.addEventListener('click', saveProduct);
    }
    
    // Cancel button
    const cancelBtn = document.getElementById('cancelBtn');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', () => {
            // Ask for confirmation if there are unsaved changes
            if (hasUnsavedChanges()) {
                if (confirm('You have unsaved changes. Are you sure you want to leave?')) {
                    window.location.href = 'products.php';
                }
            } else {
                window.location.href = 'products.php';
            }
        });
    }
    
    // Preview button
    const previewBtn = document.getElementById('previewBtn');
    if (previewBtn) {
        previewBtn.addEventListener('click', previewProduct);
    }
    
    // Sync product button
    const syncProductBtn = document.getElementById('syncProductBtn');
    if (syncProductBtn) {
        syncProductBtn.addEventListener('click', syncProduct);
    }
    
    // Product type change
    const productType = document.getElementById('productType');
    if (productType) {
        productType.addEventListener('change', function() {
            toggleVariationsCard(this.value === 'variable');
        });
    }
    
    // Manage stock toggle
    const manageStock = document.getElementById('manageStock');
    if (manageStock) {
        manageStock.addEventListener('change', function() {
            toggleStockFields(this.checked);
        });
    }
    
    // Image upload handlers
    setupImageUploadHandlers();
    
    // Attribute handlers
    setupAttributeHandlers();
    
    // SEO field handling
    setupSeoPreviewHandlers();
}

/**
 * Initialize rich text editor for description
 */
function initRichTextEditor() {
    if (typeof tinymce !== 'undefined') {
        tinymce.init({
            selector: '.wysiwyg-editor',
            height: 300,
            menubar: false,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table paste code help wordcount'
            ],
            toolbar: 'undo redo | formatselect | ' +
                'bold italic backcolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help',
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px }'
        });
    }
}

/**
 * Fetch product data from API
 * 
 * @param {number} productId Product ID
 * @returns {Promise} Promise with product data
 */
function fetchProductData(productId) {
    return fetch(`api/product.php?id=${productId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(response => {
            if (!response.success) {
                throw new Error(response.message || 'Error fetching product data');
            }
            return response.data;
        });
}

/**
 * Fetch categories from API
 * 
 * @returns {Promise} Promise with categories data
 */
function fetchCategories() {
    return fetch('api/categories.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(response => {
            if (!response.success) {
                throw new Error(response.message || 'Error fetching categories');
            }
            return response.data;
        });
}

/**
 * Fetch attributes from API
 * 
 * @returns {Promise} Promise with attributes data
 */
function fetchAttributes() {
    return fetch('api/attributes.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(response => {
            if (!response.success) {
                throw new Error(response.message || 'Error fetching attributes');
            }
            return response.data;
        });
}

/**
 * Initialize new product form
 */
function initNewProductForm() {
    document.getElementById('productTitle').textContent = 'Add New Product';
    document.getElementById('productBreadcrumb').textContent = 'New Product';
    
    // Set default values
    document.getElementById('productStatus').value = 'draft';
    document.getElementById('productVisibility').value = 'visible';
    document.getElementById('productType').value = 'simple';
    document.getElementById('stockStatus').value = 'instock';
    document.getElementById('manageStock').checked = true;
    
    // Initialize empty product data
    productData = {
        product_id: null,
        name: '',
        sku: '',
        type: 'simple',
        status: 'draft',
        visibility: 'visible',
        description: '',
        short_description: '',
        regular_price: '',
        sale_price: '',
        manage_stock: true,
        stock_quantity: 0,
        stock_status: 'instock',
        weight: '',
        dimensions: {
            length: '',
            width: '',
            height: ''
        },
        categories: [],
        attributes: [],
        images: [],
        seo: {
            meta_title: '',
            meta_description: '',
            focus_keyword: '',
            meta_keywords: '',
            canonical_url: ''
        },
        rating_score: 0,
        improvement_tips: {
            'Basic Information': {
                score: 0,
                items: ['Product name is missing', 'SKU is missing', 'Regular price is missing']
            },
            'Description': {
                score: 0,
                items: ['Product description is missing', 'Short description is missing']
            },
            'Images': {
                score: 0,
                items: ['Product has no images']
            },
            'SEO Elements': {
                score: 0,
                items: ['Meta title is missing', 'Meta description is missing', 'Focus keyword is missing']
            }
        }
    };
    
    // Copy as original data
    originalData = JSON.parse(JSON.stringify(productData));
    
    // Update score visualization
    updateProductScore(0);
    
    // Update improvement tips
    updateImprovementTips(productData.improvement_tips);
}

/**
 * Populate product form with data
 * 
 * @param {Object} data Product data
 */
function populateProductForm(data) {
    // Update page title and breadcrumb
    document.getElementById('productTitle').textContent = `Edit: ${data.name}`;
    document.getElementById('productBreadcrumb').textContent = data.name;
    
    // Basic Information
    document.getElementById('productName').value = data.name || '';
    document.getElementById('productSKU').value = data.sku || '';
    document.getElementById('productType').value = data.type || 'simple';
    document.getElementById('regularPrice').value = data.regular_price || '';
    document.getElementById('salePrice').value = data.sale_price || '';
    document.getElementById('stockQty').value = data.stock_quantity || 0;
    document.getElementById('stockStatus').value = data.stock_status || 'instock';
    document.getElementById('manageStock').checked = data.manage_stock || false;
    document.getElementById('weight').value = data.weight || '';
    
    // Dimensions
    if (data.dimensions) {
        document.getElementById('length').value = data.dimensions.length || '';
        document.getElementById('width').value = data.dimensions.width || '';
        document.getElementById('height').value = data.dimensions.height || '';
    }
    
    // Status and visibility
    document.getElementById('productStatus').value = data.status || 'draft';
    document.getElementById('productVisibility').value = data.visibility || 'visible';
    
    // Set last updated and synced times
    const lastUpdatedEl = document.getElementById('lastUpdated');
    if (lastUpdatedEl && data.updated_at) {
        const updatedDate = new Date(data.updated_at);
        lastUpdatedEl.textContent = updatedDate.toLocaleDateString() + ' ' + 
                                   updatedDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }
    
    const lastSyncedEl = document.getElementById('lastSynced');
    if (lastSyncedEl && data.last_synced) {
        const syncedDate = new Date(data.last_synced);
        lastSyncedEl.textContent = syncedDate.toLocaleDateString() + ' ' + 
                                  syncedDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    } else if (lastSyncedEl) {
        lastSyncedEl.textContent = 'Never';
    }
    
    // Description
    document.getElementById('shortDescription').value = data.short_description || '';
    
    if (typeof tinymce !== 'undefined') {
        const editor = tinymce.get('fullDescription');
        if (editor) {
            editor.setContent(data.description || '');
        } else {
            document.getElementById('fullDescription').value = data.description || '';
        }
    } else {
        document.getElementById('fullDescription').value = data.description || '';
    }
    
    // Images
    if (data.images && data.images.length > 0) {
        populateImages(data.images);
    }
    
    // SEO data
    if (data.seo) {
        document.getElementById('metaTitle').value = data.seo.meta_title || '';
        document.getElementById('metaDescription').value = data.seo.meta_description || '';
        document.getElementById('focusKeyword').value = data.seo.focus_keyword || '';
        document.getElementById('metaKeywords').value = data.seo.meta_keywords || '';
        document.getElementById('canonicalUrl').value = data.seo.canonical_url || '';
        
        // Update SEO preview
        updateSeoPreview();
    }
    
    // Advanced settings
    document.getElementById('purchaseNote').value = data.purchase_note || '';
    document.getElementById('menuOrder').value = data.menu_order || 0;
    document.getElementById('taxStatus').value = data.tax_status || 'taxable';
    document.getElementById('taxClass').value = data.tax_class || '';
    document.getElementById('shippingClass').value = data.shipping_class || '';
    document.getElementById('soldIndividually').checked = data.sold_individually || false;
    
    // Toggle visibility of related fields
    toggleVariationsCard(data.type === 'variable');
    toggleStockFields(data.manage_stock);
    
    // Attributes will be populated separately once attributes data is loaded
    if (data.attributes && data.attributes.length > 0) {
        setTimeout(() => {
            populateAttributes(data.attributes);
        }, 500); // Small delay to ensure attributes data is loaded
    }
    
    // Categories will be checked in the tree once it's populated
    if (data.categories && data.categories.length > 0) {
        setTimeout(() => {
            checkSelectedCategories(data.categories);
        }, 500); // Small delay to ensure categories tree is populated
    }
}