<?php require_once 'views/layout/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <?php require_once 'views/layout/user_sidebar.php'; ?>
        </div>
        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                <h2>My Products</h2>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="fas fa-plus"></i> Add New Product
                </button>
            </div>

            <!-- Search and Filter Section -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="type">
                                <option value="">All Categories</option>
                                <option value="foods" <?php echo (isset($_GET['type']) && $_GET['type'] === 'foods') ? 'selected' : ''; ?>>Pet Foods</option>
                                <option value="accessories" <?php echo (isset($_GET['type']) && $_GET['type'] === 'accessories') ? 'selected' : ''; ?>>Accessories</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="stock_status">
                                <option value="">All Stock Status</option>
                                <option value="in_stock" <?php echo (isset($_GET['stock_status']) && $_GET['stock_status'] === 'in_stock') ? 'selected' : ''; ?>>In Stock</option>
                                <option value="low_stock" <?php echo (isset($_GET['stock_status']) && $_GET['stock_status'] === 'low_stock') ? 'selected' : ''; ?>>Low Stock</option>
                                <option value="out_of_stock" <?php echo (isset($_GET['stock_status']) && $_GET['stock_status'] === 'out_of_stock') ? 'selected' : ''; ?>>Out of Stock</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <?php if (empty($products)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No products found</h5>
                            <p class="text-muted">Start by adding your first product!</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Image</th>
                                        <th>
                                            <div class="d-flex align-items-center">
                                                Name
                                                <div class="ms-2">
                                                    <a href="?sort=name&order=asc<?php 
                                                        echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; 
                                                        echo isset($_GET['type']) ? '&type=' . urlencode($_GET['type']) : ''; 
                                                        echo isset($_GET['stock_status']) ? '&stock_status=' . urlencode($_GET['stock_status']) : ''; 
                                                    ?>" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Sort by name (A to Z)">
                                                        <i class="fas fa-sort-alpha-up"></i>
                                                    </a>
                                                    <a href="?sort=name&order=desc<?php 
                                                        echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; 
                                                        echo isset($_GET['type']) ? '&type=' . urlencode($_GET['type']) : ''; 
                                                        echo isset($_GET['stock_status']) ? '&stock_status=' . urlencode($_GET['stock_status']) : ''; 
                                                    ?>" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Sort by name (Z to A)">
                                                        <i class="fas fa-sort-alpha-down"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </th>
                                        <th>Type</th>
                                        <th>
                                            <div class="d-flex align-items-center">
                                                Price
                                                <div class="ms-2">
                                                    <a href="?sort=price&order=asc<?php 
                                                        echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; 
                                                        echo isset($_GET['type']) ? '&type=' . urlencode($_GET['type']) : ''; 
                                                        echo isset($_GET['stock_status']) ? '&stock_status=' . urlencode($_GET['stock_status']) : ''; 
                                                    ?>" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Sort by price (lowest to highest)">
                                                        <i class="fas fa-sort-numeric-up"></i>
                                                    </a>
                                                    <a href="?sort=price&order=desc<?php 
                                                        echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; 
                                                        echo isset($_GET['type']) ? '&type=' . urlencode($_GET['type']) : ''; 
                                                        echo isset($_GET['stock_status']) ? '&stock_status=' . urlencode($_GET['stock_status']) : ''; 
                                                    ?>" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Sort by price (highest to lowest)">
                                                        <i class="fas fa-sort-numeric-down"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="d-flex align-items-center">
                                                Stock
                                                <div class="ms-2">
                                                    <a href="?sort=stock&order=asc<?php 
                                                        echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; 
                                                        echo isset($_GET['type']) ? '&type=' . urlencode($_GET['type']) : ''; 
                                                        echo isset($_GET['stock_status']) ? '&stock_status=' . urlencode($_GET['stock_status']) : ''; 
                                                    ?>" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Sort by price (lowest to highest)">
                                                        <i class="fas fa-sort-numeric-up"></i>
                                                    </a>
                                                    <a href="?sort=stock&order=desc<?php 
                                                        echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; 
                                                        echo isset($_GET['type']) ? '&type=' . urlencode($_GET['type']) : ''; 
                                                        echo isset($_GET['stock_status']) ? '&stock_status=' . urlencode($_GET['stock_status']) : ''; 
                                                    ?>" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Sort by price (highest to lowest)">
                                                        <i class="fas fa-sort-numeric-down"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $product): ?>
                                        <tr class="<?php echo $product['stock_quantity'] < 5 ? 'table-warning' : ''; ?>">
                                            <td style="width: 80px;">
                                                <img src="<?php echo BASE_URL . $product['product_image']; ?>" 
                                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                                     class="rounded" 
                                                     style="width: 60px; height: 60px; object-fit: cover;">
                                            </td>
                                            <td>
                                                <div class="fw-bold"><?php echo htmlspecialchars($product['name']); ?></div>
                                                <small class="text-muted"><?php echo substr(htmlspecialchars($product['description']), 0, 50) . '...'; ?></small>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $product['type'] === 'foods' ? 'success' : 'info'; ?>">
                                                    <?php echo ucfirst($product['type']); ?>
                                                </span>
                                            </td>
                                            <td class="fw-bold">₱<?php echo number_format($product['price'], 2); ?></td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo $product['stock_quantity'] === 0 ? 'danger' : 
                                                        ($product['stock_quantity'] <= 5 ? 'warning' : 'success'); 
                                                ?>">
                                                    <?php echo $product['stock_quantity']; ?>
                                                    <?php if ($product['stock_quantity'] <= 5 && $product['stock_quantity'] > 0): ?>
                                                        (Low Stock)
                                                    <?php endif; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-primary" 
                                                            onclick="editProduct(<?php echo htmlspecialchars(json_encode([
                                                                'id' => $product['id'],
                                                                'name' => $product['name'],
                                                                'type' => $product['type'],
                                                                'price' => $product['price'],
                                                                'stock_quantity' => $product['stock_quantity'],
                                                                'description' => $product['description'],
                                                                'product_image' => $product['product_image']
                                                            ])); ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-danger" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#deleteProductModal"
                                                            data-product-id="<?php echo $product['id']; ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <nav class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?><?php 
                                                echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; 
                                                echo isset($_GET['type']) ? '&type=' . urlencode($_GET['type']) : ''; 
                                                echo isset($_GET['stock_status']) ? '&stock_status=' . urlencode($_GET['stock_status']) : ''; 
                                            ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select name="type" class="form-select" required>
                                <option value="foods">Foods</option>
                                <option value="accessories">Accessories</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Price (₱)</label>
                            <input type="number" name="price" class="form-control" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Stock Quantity</label>
                            <input type="number" name="stock_quantity" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Product Image</label>
                        <input type="file" name="product_image" class="form-control" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo BASE_URL; ?>/user/products" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="edit_id">
                    <input type="hidden" name="current_product_image" id="edit_current_image_path">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select name="type" id="edit_type" class="form-select" required>
                                <option value="foods">Pet Foods</option>
                                <option value="accessories">Accessories</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Price (₱)</label>
                            <input type="number" name="price" id="edit_price" class="form-control" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Stock Quantity</label>
                            <input type="number" name="stock_quantity" id="edit_stock_quantity" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="3" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Product Image</label>
                        <input type="file" name="product_image" class="form-control" accept="image/*">
                        <small class="text-muted">Leave empty to keep the current image</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Product Modal -->
<div class="modal fade" id="deleteProductModal" tabindex="-1" aria-labelledby="deleteProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteProductModalLabel">Delete Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this product?</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="<?php echo BASE_URL; ?>/user/delete-product" method="POST" class="d-inline">
                    <input type="hidden" name="action" value="delete_product">
                    <input type="hidden" name="product_id" id="delete_product_id">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.modal {
    backdrop-filter: blur(5px);
}

.modal-content {
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
}

.btn-outline-primary {
    color: #FF8C00;
    border-color: #FF8C00;
}

.btn-outline-primary:hover {
    background-color: #FF8C00;
    border-color: #FF8C00;
    color: white;
}

.btn-outline-danger {
    color: #dc3545;
    border-color: #dc3545;
}

.btn-outline-danger:hover {
    background-color: #dc3545;
    border-color: #dc3545;
    color: white;
}

/* Sorting styles */
.fa-sort-up, .fa-sort-down {
    color: #FF8C00;
    font-size: 0.8em;
    transition: color 0.2s ease;
}

.fa-sort-up:hover, .fa-sort-down:hover {
    color: #e67e00;
}

.sort-active {
    color: #e67e00;
    font-weight: bold;
}

.sort-header {
    cursor: pointer;
    user-select: none;
}

.sort-header:hover {
    background-color: rgba(255, 140, 0, 0.1);
}

/* Alert animations */
.alert {
    animation: slideIn 0.5s ease-out;
}

@keyframes slideIn {
    from {
        transform: translateY(-100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* Table hover effect */
.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.02);
    transition: background-color 0.2s ease;
}

/* Badge animations */
.badge {
    transition: all 0.3s ease;
}

.badge:hover {
    transform: scale(1.05);
}

/* Button hover effects */
.btn-group .btn {
    transition: all 0.2s ease;
}

.btn-group .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Toast animations */
.toast {
    animation: slideInRight 0.5s ease-out;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.toast.showing {
    animation: slideInRight 0.5s ease-out;
}

.toast.hide {
    animation: slideOutRight 0.5s ease-out;
}

@keyframes slideOutRight {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}
</style>

<script>
function editProduct(product) {
    // Set form values
    document.getElementById('edit_id').value = product.id;
    document.getElementById('edit_name').value = product.name;
    document.getElementById('edit_type').value = product.type;
    document.getElementById('edit_price').value = product.price;
    document.getElementById('edit_stock_quantity').value = product.stock_quantity;
    document.getElementById('edit_description').value = product.description;
    document.getElementById('edit_current_image_path').value = product.product_image;
    
    // Show the modal using Bootstrap's API
    const editModal = new bootstrap.Modal(document.getElementById('editProductModal'));
    editModal.show();
}

function confirmDelete(product) {
    document.getElementById('delete_product_id').value = product.id;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteProductModal'));
    deleteModal.show();
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

<?php require_once 'views/layout/footer.php'; ?> 