<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WooCommerce PIM - Users</title>
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
                    <li>
                        <a href="settings.php">
                            <span class="icon-settings"></span>
                            <span class="nav-text">Settings</span>
                        </a>
                    </li>
                    <li class="active">
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
                    <h2>Users</h2>
                    <p class="breadcrumb">Home > Users</p>
                </div>
                
                <div class="header-actions">
                    <div class="search-box">
                        <input type="text" id="userSearch" placeholder="Search users...">
                        <button class="search-btn">
                            <span class="icon-search"></span>
                        </button>
                    </div>
                    
                    <div class="actions">
                        <button class="action-btn primary" id="inviteUserBtn">
                            <span class="icon-plus"></span>
                            <span class="action-text">Invite User</span>
                        </button>
                    </div>
                </div>
            </header>
            
            <div class="users-content">
                <div class="users-layout">
                    <!-- Users List -->
                    <section class="users-list-section">
                        <div class="card">
                            <div class="card-header">
                                <h3>Users</h3>
                                <div class="card-actions">
                                    <select id="userRoleFilter" class="form-control">
                                        <option value="">All Roles</option>
                                        <option value="admin">Administrator</option>
                                        <option value="editor">Editor</option>
                                        <option value="viewer">Viewer</option>
                                    </select>
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="data-table" id="usersTable">
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                            <th>Last Login</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="usersTableBody">
                                        <!-- Will be populated by JavaScript -->
                                        <tr>
                                            <td colspan="6" class="text-center">Loading users...</td>
                                        </tr>
                                    </tbody>
                                </table>
                                
                                <div class="pagination-container">
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
                        </div>
                    </section>
                    
                    <!-- Roles Management -->
                    <section class="roles-section">
                        <div class="card">
                            <div class="card-header">
                                <h3>Roles & Permissions</h3>
                                <div class="card-actions">
                                    <button class="btn" id="newRoleBtn">
                                        <span class="icon-plus"></span> New Role
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="roles-list" id="rolesList">
                                    <!-- Will be populated by JavaScript -->
                                    <div class="loading">Loading roles...</div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            
            <!-- Invite User Modal -->
            <div class="modal" id="inviteUserModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Invite User</h3>
                        <button class="close-btn" id="closeInviteUserModal">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="inviteEmail">Email Address</label>
                            <input type="email" id="inviteEmail" class="form-control" placeholder="Enter email address">
                        </div>
                        
                        <div class="form-group">
                            <label for="inviteFirstName">First Name</label>
                            <input type="text" id="inviteFirstName" class="form-control" placeholder="Enter first name">
                        </div>
                        
                        <div class="form-group">
                            <label for="inviteLastName">Last Name</label>
                            <input type="text" id="inviteLastName" class="form-control" placeholder="Enter last name">
                        </div>
                        
                        <div class="form-group">
                            <label for="inviteRole">Role</label>
                            <select id="inviteRole" class="form-control">
                                <option value="">Select a role</option>
                                <option value="admin">Administrator</option>
                                <option value="editor">Editor</option>
                                <option value="viewer">Viewer</option>
                            </select>
                        </div>
                        
                        <div class="modal-actions">
                            <button class="btn" id="cancelInviteBtn">Cancel</button>
                            <button class="btn primary" id="sendInviteBtn">Send Invitation</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Edit User Modal -->
            <div class="modal" id="editUserModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Edit User</h3>
                        <button class="close-btn" id="closeEditUserModal">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="editFirstName">First Name</label>
                            <input type="text" id="editFirstName" class="form-control" placeholder="Enter first name">
                        </div>
                        
                        <div class="form-group">
                            <label for="editLastName">Last Name</label>
                            <input type="text" id="editLastName" class="form-control" placeholder="Enter last name">
                        </div>
                        
                        <div class="form-group">
                            <label for="editEmail">Email Address</label>
                            <input type="email" id="editEmail" class="form-control" placeholder="Enter email address">
                        </div>
                        
                        <div class="form-group">
                            <label for="editRole">Role</label>
                            <select id="editRole" class="form-control">
                                <option value="">Select a role</option>
                                <option value="admin">Administrator</option>
                                <option value="editor">Editor</option>
                                <option value="viewer">Viewer</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="editStatus">Status</label>
                            <select id="editStatus" class="form-control">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <div class="checkbox">
                                <input type="checkbox" id="editResetPassword">
                                <label for="editResetPassword">Send password reset email</label>
                            </div>
                        </div>
                        
                        <div class="modal-actions">
                            <button class="btn" id="cancelEditBtn">Cancel</button>
                            <button class="btn primary" id="saveUserBtn">Save Changes</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Delete User Modal -->
            <div class="modal" id="deleteUserModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Delete User</h3>
                        <button class="close-btn" id="closeDeleteUserModal">×</button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete the user <strong id="deleteUserName"></strong>? This action cannot be undone.</p>
                        <div class="modal-actions">
                            <button class="btn" id="cancelDeleteUserBtn">Cancel</button>
                            <button class="btn danger" id="confirmDeleteUserBtn">Delete User</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Create/Edit Role Modal -->
            <div class="modal" id="roleModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 id="roleModalTitle">New Role</h3>
                        <button class="close-btn" id="closeRoleModal">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="roleName">Role Name</label>
                            <input type="text" id="roleName" class="form-control" placeholder="Enter role name">
                        </div>
                        
                        <div class="form-group">
                            <label for="roleDescription">Description</label>
                            <textarea id="roleDescription" class="form-control" rows="2" placeholder="Enter role description"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>Permissions</label>
                            <div class="permissions-grid">
                                <div class="permission-section">
                                    <h4>Products</h4>
                                    <div class="permission-items">
                                        <div class="permission-item">
                                            <div class="checkbox">
                                                <input type="checkbox" id="permProductView" checked>
                                                <label for="permProductView">View Products</label>
                                            </div>
                                        </div>
                                        <div class="permission-item">
                                            <div class="checkbox">
                                                <input type="checkbox" id="permProductCreate">
                                                <label for="permProductCreate">Create Products</label>
                                            </div>
                                        </div>
                                        <div class="permission-item">
                                            <div class="checkbox">
                                                <input type="checkbox" id="permProductEdit">
                                                <label for="permProductEdit">Edit Products</label>
                                            </div>
                                        </div>
                                        <div class="permission-item">
                                            <div class="checkbox">
                                                <input type="checkbox" id="permProductDelete">
                                                <label for="permProductDelete">Delete Products</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="permission-section">
                                    <h4>Categories</h4>
                                    <div class="permission-items">
                                        <div class="permission-item">
                                            <div class="checkbox">
                                                <input type="checkbox" id="permCategoryView" checked>
                                                <label for="permCategoryView">View Categories</label>
                                            </div>
                                        </div>
                                        <div class="permission-item">
                                            <div class="checkbox">
                                                <input type="checkbox" id="permCategoryCreate">
                                                <label for="permCategoryCreate">Create Categories</label>
                                            </div>
                                        </div>
                                        <div class="permission-item">
                                            <div class="checkbox">
                                                <input type="checkbox" id="permCategoryEdit">
                                                <label for="permCategoryEdit">Edit Categories</label>
                                            </div>
                                        </div>
                                        <div class="permission-item">
                                            <div class="checkbox">
                                                <input type="checkbox" id="permCategoryDelete">
                                                <label for="permCategoryDelete">Delete Categories</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="permission-section">
                                    <h4>Attributes</h4>
                                    <div class="permission-items">
                                        <div class="permission-item">
                                            <div class="checkbox">
                                                <input type="checkbox" id="permAttributeView" checked>
                                                <label for="permAttributeView">View Attributes</label>
                                            </div>
                                        </div>
                                        <div class="permission-item">
                                            <div class="checkbox">
                                                <input type="checkbox" id="permAttributeCreate">
                                                <label for="permAttributeCreate">Create Attributes</label>
                                            </div>
                                        </div>
                                        <div class="permission-item">
                                            <div class="checkbox">
                                                <input type="checkbox" id="permAttributeEdit">
                                                <label for="permAttributeEdit">Edit Attributes</label>
                                            </div>
                                        </div>
                                        <div class="permission-item">
                                            <div class="checkbox">
                                                <input type="checkbox" id="permAttributeDelete">
                                                <label for="permAttributeDelete">Delete Attributes</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="permission-section">
                                    <h4>Users</h4>
                                    <div class="permission-items">
                                        <div class="permission-item">
                                            <div class="checkbox">
                                                <input type="checkbox" id="permUserView">
                                                <label for="permUserView">View Users</label>
                                            </div>
                                        </div>
                                        <div class="permission-item">
                                            <div class="checkbox">
                                                <input type="checkbox" id="permUserCreate">
                                                <label for="permUserCreate">Create Users</label>
                                            </div>
                                        </div>
                                        <div class="permission-item">
                                            <div class="checkbox">
                                                <input type="checkbox" id="permUserEdit">
                                                <label for="permUserEdit">Edit Users</label>
                                            </div>
                                        </div>
                                        <div class="permission-item">
                                            <div class="checkbox">
                                                <input type="checkbox" id="permUserDelete">
                                                <label for="permUserDelete">Delete Users</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="permission-section">
                                    <h4>Sync</h4>
                                    <div class="permission-items">
                                        <div class="permission-item">
                                            <div class="checkbox">
                                                <input type="checkbox" id="permSyncView" checked>
                                                <label for="permSyncView">View Sync</label>
                                            </div>
                                        </div>
                                        <div class="permission-item">
                                            <div class="checkbox">
                                                <input type="checkbox" id="permSyncExecute">
                                                <label for="permSyncExecute">Execute Sync</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="permission-section">
                                    <h4>Settings</h4>
                                    <div class="permission-items">
                                        <div class="permission-item">
                                            <div class="checkbox">
                                                <input type="checkbox" id="permSettingsView">
                                                <label for="permSettingsView">View Settings</label>
                                            </div>
                                        </div>
                                        <div class="permission-item">
                                            <div class="checkbox">
                                                <input type="checkbox" id="permSettingsEdit">
                                                <label for="permSettingsEdit">Edit Settings</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-actions">
                            <button class="btn" id="cancelRoleBtn">Cancel</button>
                            <button class="btn primary" id="saveRoleBtn">Save Role</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Delete Role Modal -->
            <div class="modal" id="deleteRoleModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Delete Role</h3>
                        <button class="close-btn" id="closeDeleteRoleModal">×</button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete the role <strong id="deleteRoleName"></strong>?</p>
                        <div class="warning-message" id="deleteRoleWarning" style="display: none;">
                            <span class="icon-warning"></span>
                            <span id="deleteRoleWarningText"></span>
                        </div>
                        <div class="modal-actions">
                            <button class="btn" id="cancelDeleteRoleBtn">Cancel</button>
                            <button class="btn danger" id="confirmDeleteRoleBtn">Delete Role</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script src="assets/js/users.js"></script>
</body>
</html>