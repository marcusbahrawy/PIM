/**
 * WooCommerce PIM System
 * Dashboard JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize the dashboard
    initDashboard();
    
    // Set up event listeners
    setupEventListeners();
});

/**
 * Initialize the dashboard
 */
function initDashboard() {
    // Load dashboard data from the server
    fetchDashboardData()
        .then(data => {
            updateDashboardUI(data);
            initCharts(data);
        })
        .catch(error => {
            console.error('Error loading dashboard data:', error);
            showNotification('Error loading dashboard data. Please try again.', 'error');
        });
    
    // Initialize theme based on stored preference
    initTheme();
}

/**
 * Set up event listeners for the dashboard
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
    
    // Sync modal
    const syncBtn = document.getElementById('syncBtn');
    const syncNowBtn = document.getElementById('syncNowBtn');
    const closeSyncModal = document.getElementById('closeSyncModal');
    const syncModal = document.getElementById('syncModal');
    
    if (syncBtn && syncModal) {
        syncBtn.addEventListener('click', () => toggleModal(syncModal, true));
    }
    
    if (syncNowBtn && syncModal) {
        syncNowBtn.addEventListener('click', () => toggleModal(syncModal, true));
    }
    
    if (closeSyncModal && syncModal) {
        closeSyncModal.addEventListener('click', () => toggleModal(syncModal, false));
    }
    
    // Sync actions
    const importBtn = document.getElementById('importBtn');
    const exportBtn = document.getElementById('exportBtn');
    const fullSyncBtn = document.getElementById('fullSyncBtn');
    
    if (importBtn) {
        importBtn.addEventListener('click', startImport);
    }
    
    if (exportBtn) {
        exportBtn.addEventListener('click', startExport);
    }
    
    if (fullSyncBtn) {
        fullSyncBtn.addEventListener('click', startFullSync);
    }
    
    // New product button
    const newProductBtn = document.getElementById('newProductBtn');
    if (newProductBtn) {
        newProductBtn.addEventListener('click', () => {
            window.location.href = 'product-edit.php?action=new';
        });
    }
}

/**
 * Fetch dashboard data from the server
 * 
 * @returns {Promise} Promise object with dashboard data
 */
function fetchDashboardData() {
    return fetch('api/dashboard.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        });
}

/**
 * Update dashboard UI with data
 * 
 * @param {Object} data Dashboard data
 */
function updateDashboardUI(data) {
    // Update product stats
    document.getElementById('totalProducts').textContent = data.productStats.total;
    document.getElementById('publishedProducts').textContent = data.productStats.published;
    document.getElementById('draftProducts').textContent = data.productStats.draft;
    document.getElementById('averageScore').textContent = data.productStats.averageScore + '%';
    
    // Update recent products
    updateRecentProducts(data.recentProducts);
    
    // Update low scoring products
    updateLowScoringProducts(data.lowScoringProducts);
    
    // Update sync status
    updateSyncStatus(data.syncStatus);
    
    // Update recent activity
    updateRecentActivity(data.recentActivity);
}

/**
 * Initialize charts
 * 
 * @param {Object} data Dashboard data
 */
function initCharts(data) {
    // Rating distribution chart
    const ratingCtx = document.getElementById('ratingChart').getContext('2d');
    new Chart(ratingCtx, {
        type: 'bar',
        data: {
            labels: ['0-20%', '21-40%', '41-60%', '61-80%', '81-100%'],
            datasets: [{
                label: 'Products',
                data: calculateRatingDistribution(data.productStats),
                backgroundColor: [
                    'rgba(255, 59, 48, 0.6)',
                    'rgba(255, 149, 0, 0.6)',
                    'rgba(255, 204, 0, 0.6)',
                    'rgba(52, 199, 89, 0.6)',
                    'rgba(40, 167, 69, 0.6)'
                ],
                borderColor: [
                    'rgba(255, 59, 48, 1)',
                    'rgba(255, 149, 0, 1)',
                    'rgba(255, 204, 0, 1)',
                    'rgba(52, 199, 89, 1)',
                    'rgba(40, 167, 69, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: getComputedStyle(document.documentElement).getPropertyValue('--border-color')
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

/**
 * Calculate rating distribution for chart
 * 
 * @param {Object} productStats Product statistics
 * @returns {Array} Rating distribution array
 */
function calculateRatingDistribution(productStats) {
    // This is a placeholder. In a real app, this would calculate actual distribution
    // from the server data. For demo purposes, we're generating a mock distribution.
    
    const total = productStats.total;
    if (total === 0) return [0, 0, 0, 0, 0];
    
    const lowScoring = productStats.lowScoring;
    const midLow = Math.floor(total * 0.15);
    const mid = Math.floor(total * 0.2);
    const midHigh = Math.floor(total * 0.25);
    const high = total - lowScoring - midLow - mid - midHigh;
    
    return [lowScoring, midLow, mid, midHigh, high];
}

/**
 * Update recent products table
 * 
 * @param {Array} products Recent products
 */
function updateRecentProducts(products) {
    const tableBody = document.getElementById('recentProducts');
    if (!tableBody) return;
    
    tableBody.innerHTML = '';
    
    if (products.length === 0) {
        const row = document.createElement('tr');
        row.innerHTML = '<td colspan="4" class="text-center">No products found</td>';
        tableBody.appendChild(row);
        return;
    }
    
    products.forEach(product => {
        const row = document.createElement('tr');
        
        // Format the updated date
        const updatedDate = new Date(product.updated_at);
        const formattedDate = updatedDate.toLocaleDateString() + ' ' + 
                             updatedDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        
        // Create status badge
        const statusClass = `status-${product.status}`;
        const statusText = product.status.charAt(0).toUpperCase() + product.status.slice(1);
        
        // Create rating badge
        const scoreClass = `rating-${product.score_color}`;
        
        row.innerHTML = `
            <td>
                <div class="product-cell">
                    <div class="product-name">${product.name}</div>
                    <div class="product-sku">${product.sku}</div>
                </div>
            </td>
            <td><span class="status-badge ${statusClass}">${statusText}</span></td>
            <td><span class="rating-badge ${scoreClass}">${product.rating_score}%</span></td>
            <td>${formattedDate}</td>
        `;
        
        tableBody.appendChild(row);
    });
}

/**
 * Update low scoring products table
 * 
 * @param {Array} products Low scoring products
 */
function updateLowScoringProducts(products) {
    const tableBody = document.getElementById('lowScoreProducts');
    if (!tableBody) return;
    
    tableBody.innerHTML = '';
    
    if (products.length === 0) {
        const row = document.createElement('tr');
        row.innerHTML = '<td colspan="3" class="text-center">No products found</td>';
        tableBody.appendChild(row);
        return;
    }
    
    products.forEach(product => {
        const row = document.createElement('tr');
        
        // Create rating badge
        const scoreColor = getScoreColor(product.rating_score);
        const scoreClass = `rating-${scoreColor}`;
        
        row.innerHTML = `
            <td>
                <div class="product-cell">
                    <div class="product-name">${product.name}</div>
                    <div class="product-sku">${product.sku}</div>
                </div>
            </td>
            <td><span class="rating-badge ${scoreClass}">${product.rating_score}%</span></td>
            <td>
                <a href="product-edit.php?id=${product.product_id}" class="btn btn-sm primary">
                    Improve
                </a>
            </td>
        `;
        
        tableBody.appendChild(row);
    });
}

/**
 * Update sync status section
 * 
 * @param {Object} syncStatus Sync status data
 */
function updateSyncStatus(syncStatus) {
    // Update last sync time
    const lastSyncEl = document.getElementById('lastSync');
    if (lastSyncEl) {
        if (syncStatus.lastSync) {
            const lastSync = new Date(syncStatus.lastSync);
            lastSyncEl.textContent = lastSync.toLocaleDateString() + ' ' + 
                                    lastSync.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        } else {
            lastSyncEl.textContent = 'Never';
        }
    }
    
    // Update pending jobs
    const pendingJobsEl = document.getElementById('pendingJobs');
    if (pendingJobsEl) {
        pendingJobsEl.textContent = syncStatus.pendingJobs;
    }
    
    // Update recent jobs table
    const recentJobsTable = document.getElementById('recentJobs');
    if (recentJobsTable) {
        recentJobsTable.innerHTML = '';
        
        if (syncStatus.recentJobs.length === 0) {
            const row = document.createElement('tr');
            row.innerHTML = '<td colspan="5" class="text-center">No recent sync jobs</td>';
            recentJobsTable.appendChild(row);
            return;
        }
        
        syncStatus.recentJobs.forEach(job => {
            const row = document.createElement('tr');
            
            // Format dates
            const startedAt = job.started_at ? new Date(job.started_at) : null;
            const completedAt = job.completed_at ? new Date(job.completed_at) : null;
            
            const startedText = startedAt ? 
                startedAt.toLocaleDateString() + ' ' + startedAt.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : 
                'Not started';
                
            const completedText = completedAt ? 
                completedAt.toLocaleDateString() + ' ' + completedAt.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : 
                'In progress';
            
            // Job type label
            const jobTypeLabel = formatJobType(job.job_type);
            
            // Status badge
            const statusClass = getStatusClass(job.status);
            const statusText = job.status.charAt(0).toUpperCase() + job.status.slice(1);
            
            // Progress calculation
            const progress = job.items_total > 0 ? Math.round((job.items_processed / job.items_total) * 100) : 0;
            const progressClass = getProgressClass(progress, job.status);
            
            row.innerHTML = `
                <td>${jobTypeLabel}</td>
                <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                <td>
                    <div class="progress-bar">
                        <div class="progress-fill ${progressClass}" style="width: ${progress}%"></div>
                    </div>
                    <div class="progress-text mt-1">${job.items_processed}/${job.items_total} (${progress}%)</div>
                </td>
                <td>${startedText}</td>
                <td>${completedText}</td>
            `;
            
            recentJobsTable.appendChild(row);
        });
    }
}

/**
 * Update recent activity list
 * 
 * @param {Array} activities Recent activities
 */
function updateRecentActivity(activities) {
    const activityList = document.getElementById('recentActivity');
    if (!activityList) return;
    
    activityList.innerHTML = '';
    
    if (activities.length === 0) {
        const item = document.createElement('li');
        item.textContent = 'No recent activity';
        activityList.appendChild(item);
        return;
    }
    
    activities.forEach(activity => {
        const item = document.createElement('li');
        
        // Format date
        const activityDate = new Date(activity.created_at);
        const timeAgo = formatTimeAgo(activityDate);
        
        // Icon based on entity type and action
        const iconClass = getActivityIcon(activity.entity_type, activity.action);
        
        item.innerHTML = `
            <div class="activity-icon">
                <span class="${iconClass}"></span>
            </div>
            <div class="activity-content">
                <div class="activity-text">${activity.description}</div>
                <div class="activity-time">${timeAgo}</div>
            </div>
        `;
        
        activityList.appendChild(item);
    });
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
function toggleModal(modal, show) {
    if (show) {
        modal.classList.add('open');
    } else {
        modal.classList.remove('open');
    }
}

/**
 * Start import process
 */
function startImport() {
    fetch('api/sync.php?action=import', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            toggleModal(document.getElementById('syncModal'), false);
            
            // Reload data after a short delay
            setTimeout(() => {
                fetchDashboardData()
                    .then(data => updateDashboardUI(data))
                    .catch(error => console.error('Error reloading data:', error));
            }, 1000);
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error starting import:', error);
        showNotification('Error starting import. Please try again.', 'error');
    });
}

/**
 * Start export process
 */
function startExport() {
    fetch('api/sync.php?action=export', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            toggleModal(document.getElementById('syncModal'), false);
            
            // Reload data after a short delay
            setTimeout(() => {
                fetchDashboardData()
                    .then(data => updateDashboardUI(data))
                    .catch(error => console.error('Error reloading data:', error));
            }, 1000);
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error starting export:', error);
        showNotification('Error starting export. Please try again.', 'error');
    });
}

/**
 * Start full sync process
 */
function startFullSync() {
    fetch('api/sync.php?action=full_sync', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            toggleModal(document.getElementById('syncModal'), false);
            
            // Reload data after a short delay
            setTimeout(() => {
                fetchDashboardData()
                    .then(data => updateDashboardUI(data))
                    .catch(error => console.error('Error reloading data:', error));
            }, 1000);
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error starting full sync:', error);
        showNotification('Error starting full sync. Please try again.', 'error');
    });
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
}

/**
 * Get color for a score
 * 
 * @param {number} score Score value
 * @returns {string} Color name (red, yellow, green)
 */
function getScoreColor(score) {
    if (score < 50) {
        return 'red';
    } else if (score < 80) {
        return 'yellow';
    } else {
        return 'green';
    }
}

/**
 * Get CSS class for job status
 * 
 * @param {string} status Job status
 * @returns {string} CSS class
 */
function getStatusClass(status) {
    switch (status) {
        case 'completed':
            return 'status-published';
        case 'failed':
            return 'status-archived';
        case 'in_progress':
        case 'pending':
            return 'status-draft';
        default:
            return '';
    }
}

/**
 * Get CSS class for progress bar
 * 
 * @param {number} progress Progress percentage
 * @param {string} status Job status
 * @returns {string} CSS class
 */
function getProgressClass(progress, status) {
    if (status === 'failed') {
        return 'danger';
    } else if (status === 'completed') {
        return 'success';
    } else if (progress > 0) {
        return '';
    } else {
        return 'warning';
    }
}

/**
 * Format job type for display
 * 
 * @param {string} type Job type
 * @returns {string} Formatted job type
 */
function formatJobType(type) {
    switch (type) {
        case 'import':
            return 'Import from WooCommerce';
        case 'export':
            return 'Export to WooCommerce';
        case 'full_sync':
            return 'Full Synchronization';
        default:
            return type.charAt(0).toUpperCase() + type.slice(1);
    }
}

/**
 * Get icon for activity type
 * 
 * @param {string} entityType Entity type
 * @param {string} action Action performed
 * @returns {string} Icon class
 */
function getActivityIcon(entityType, action) {
    switch (entityType) {
        case 'product':
            return 'icon-product';
        case 'user':
            return 'icon-users';
        case 'sync':
            return 'icon-sync';
        case 'category':
            return 'icon-category';
        case 'attribute':
            return 'icon-attribute';
        default:
            return 'icon-dashboard';
    }
}

/**
 * Format relative time (time ago)
 * 
 * @param {Date} date Date to format
 * @returns {string} Formatted time ago string
 */
function formatTimeAgo(date) {
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);
    
    if (diffInSeconds < 60) {
        return `${diffInSeconds} seconds ago`;
    }
    
    const diffInMinutes = Math.floor(diffInSeconds / 60);
    if (diffInMinutes < 60) {
        return `${diffInMinutes} minute${diffInMinutes > 1 ? 's' : ''} ago`;
    }
    
    const diffInHours = Math.floor(diffInMinutes / 60);
    if (diffInHours < 24) {
        return `${diffInHours} hour${diffInHours > 1 ? 's' : ''} ago`;
    }
    
    const diffInDays = Math.floor(diffInHours / 24);
    if (diffInDays < 30) {
        return `${diffInDays} day${diffInDays > 1 ? 's' : ''} ago`;
    }
    
    const diffInMonths = Math.floor(diffInDays / 30);
    if (diffInMonths < 12) {
        return `${diffInMonths} month${diffInMonths > 1 ? 's' : ''} ago`;
    }
    
    const diffInYears = Math.floor(diffInMonths / 12);
    return `${diffInYears} year${diffInYears > 1 ? 's' : ''} ago`;
}