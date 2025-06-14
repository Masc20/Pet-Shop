<?php
$page_title = 'Manage Products - ' . get_setting('site_name', 'Pawfect Pet Shop');

// Capture current filter parameters for pagination links
$filter_params = http_build_query(array_filter([
    'q' => $searchQuery
]));

?>

<?php require_once 'views/layout/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <?php require_once 'views/layout/admin_sidebar.php'; ?>
        </div>
        <!-- Main Content -->
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4 py-4">
                <h2>Manage Pawducts</h2>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="fas fa-plus"></i> Add New Pawduct
                </button>
            </div>
            
            <!-- Search and Filter Form -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <form method="GET" action="<?php echo BASE_URL; ?>/admin/pawducts">
                        <div class="row g-3 align-items-center">
                            <div class="col-md">
                                <input type="text" name="q" class="form-control" placeholder="Search pawducts..." value="<?php echo htmlspecialchars($searchQuery ?? ''); ?>">
                            </div>
                            <div class="col-md-auto">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
                                <?php if (!empty($searchQuery)): ?>
                                    <a href="<?php echo BASE_URL; ?>/admin/pawducts" class="btn btn-outline-secondary">Clear Search</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Products Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>
                                        Name
                                        <div class="btn-group btn-group-sm ms-2">
                                            <a href="?sort=name&order=asc<?php echo isset($_GET['q']) ? '&q=' . urlencode($_GET['q']) : ''; ?><?php echo isset($_GET['category_id']) ? '&category_id=' . urlencode($_GET['category_id']) : ''; ?>" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Sort by name (A to Z)">
                                                <i class="fas fa-sort-alpha-down"></i>
                                            </a>
                                            <a href="?sort=name&order=desc<?php echo isset($_GET['q']) ? '&q=' . urlencode($_GET['q']) : ''; ?><?php echo isset($_GET['category_id']) ? '&category_id=' . urlencode($_GET['category_id']) : ''; ?>" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Sort by name (Z to A)">
                                                <i class="fas fa-sort-alpha-up"></i>
                                            </a>
                                        </div>
                                    </th>
                                    <th>
                                        Price
                                        <div class="btn-group btn-group-sm ms-2">
                                            <a href="?sort=price&order=asc<?php echo isset($_GET['q']) ? '&q=' . urlencode($_GET['q']) : ''; ?><?php echo isset($_GET['category_id']) ? '&category_id=' . urlencode($_GET['category_id']) : ''; ?>" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Sort by price (lowest to highest)">
                                                <i class="fas fa-sort-numeric-down"></i>
                                            </a>
                                            <a href="?sort=price&order=desc<?php echo isset($_GET['q']) ? '&q=' . urlencode($_GET['q']) : ''; ?><?php echo isset($_GET['category_id']) ? '&category_id=' . urlencode($_GET['category_id']) : ''; ?>" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Sort by price (highest to lowest)">
                                                <i class="fas fa-sort-numeric-up"></i>
                                            </a>
                                        </div>
                                    </th>
                                    <th>
                                        Stock
                                        <div class="btn-group btn-group-sm ms-2">
                                            <a href="?sort=stock_quantity&order=asc<?php echo isset($_GET['q']) ? '&q=' . urlencode($_GET['q']) : ''; ?><?php echo isset($_GET['category_id']) ? '&category_id=' . urlencode($_GET['category_id']) : ''; ?>" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Sort by stock (lowest to highest)">
                                                <i class="fas fa-sort-numeric-down"></i>
                                            </a>
                                            <a href="?sort=stock_quantity&order=desc<?php echo isset($_GET['q']) ? '&q=' . urlencode($_GET['q']) : ''; ?><?php echo isset($_GET['category_id']) ? '&category_id=' . urlencode($_GET['category_id']) : ''; ?>" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Sort by stock (highest to lowest)">
                                                <i class="fas fa-sort-numeric-up"></i>
                                            </a>
                                        </div>
                                    </th>
                                    <th>Category</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($products)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No products found</h5>
                                            <p class="text-muted">Try adjusting your search or filters.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($products as $product): ?>
                                    <tr class="<?php echo $product['stock_quantity'] < 5 ? 'table-warning' : ''; ?>">
                                        <td data-label="Image">
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo BASE_URL. $product['product_image'] ?: '/assets/images/default-product.png'; ?>" 
                                                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                                     class="rounded me-2" 
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                                <div>
                                                    <h6 class="mb-0"><?php echo htmlspecialchars($product['name']); ?></h6>
                                                    <small class="text-muted"><?php echo htmlspecialchars($product['type']); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td data-label="Name"><?php echo htmlspecialchars($product['name']); ?></td>
                                        <td data-label="Price">₱<?php echo number_format($product['price'], 2); ?></td>
                                        <td data-label="Stock">
                                            <?php if ($product['stock_quantity'] > 5): ?>
                                                <span class="badge bg-success">
                                                    <?php echo $product['stock_quantity']; ?> in stock
                                                </span>
                                            <?php elseif ($product['stock_quantity'] > 0): ?>
                                                <span class="badge bg-warning">
                                                    <?php echo $product['stock_quantity']; ?> low on stocks
                                                </span>                                        
                                            <?php else: ?>
                                                <span class="badge bg-danger">
                                                    <?php echo $product['stock_quantity']; ?> Out of stock
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <td data-label="Category">
                                            <span class="badge bg-info"><?php echo htmlspecialchars(ucfirst($product['type']))?></span>
                                        </td>
                                        <td data-label="Actions">
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-outline-primary" onclick="editProduct(<?php echo htmlspecialchars(json_encode($product)); ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete(<?php echo htmlspecialchars(json_encode($product)); ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <nav aria-label="Admin Product pagination" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php
                                // Generate base URL for pagination links, keeping filters
                                $pagination_base_url = BASE_URL . '/admin/pawducts?';
                                $current_filters = [];
                                if (!empty($searchQuery)) $current_filters['q'] = urlencode($searchQuery);
                                $filter_string = http_build_query($current_filters);
                            ?>
                            <li class="page-item <?php echo ($currentPage <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo $pagination_base_url . $filter_string . '&page=' . ($currentPage - 1); ?>">Previous</a>
                            </li>
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?php echo ($i == $currentPage) ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?php echo $pagination_base_url . $filter_string . '&page=' . $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?php echo ($currentPage >= $totalPages) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo $pagination_base_url . $filter_string . '&page=' . ($currentPage + 1); ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Responsive table styles */
@media screen and (max-width: 768px) {
    .table-responsive {
        border: 0;
    }
    
    .table thead {
        display: none;
    }
    
    .table tr {
        display: block;
        margin-bottom: 1rem;
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        background-color: #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .table td {
        display: block;
        text-align: right;
        padding: 0.75rem;
        border-bottom: 1px solid #dee2e6;
        position: relative;
    }
    
    .table td:last-child {
        border-bottom: 0;
    }
    
    .table td::before {
        content: attr(data-label);
        float: left;
        font-weight: bold;
        text-transform: uppercase;
        font-size: 0.85rem;
        color: #6c757d;
        margin-right: 1rem;
    }
    
    /* Adjust image size for mobile */
    .table td[data-label="Image"] img {
        width: 60px !important;
        height: 60px !important;
    }
    
    /* Adjust badge styles for mobile */
    .badge {
        display: inline-block;
        padding: 0.5em 0.75em;
        font-size: 0.85rem;
        margin-top: 0.25rem;
    }
    
    /* Adjust button group for mobile */
    .btn-group {
        width: 100%;
        justify-content: flex-end;
    }
    
    .btn-group .btn {
        padding: 0.5rem 1rem;
    }
}

/* Existing pagination styles */
.pagination .page-link {
    border-radius: 8px;
    margin: 0 2px;
    border: none;
    color: #FF8C00;
}

.pagination .page-link:hover {
    background: #FF8C00;
    color: white;
}

.pagination .page-item.active .page-link {
    background: #FF8C00;
    border-color: #FF8C00;
}

.modal {
    backdrop-filter: blur(5px);
}

.modal-content {
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
}
</style>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Pawduct</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data" action="<?php echo BASE_URL; ?>/admin/pawducts">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Pawduct Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select name="type" class="form-select" required>
                                <option value="foods">Pet Foods</option>
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
                        <label class="form-label">Pawduct Image</label>
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
                <h5 class="modal-title">Edit Pawduct</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data" action="<?php echo BASE_URL; ?>/admin/pawducts">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Pawduct Name</label>
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
                        <label class="form-label">Pawduct Image</label>
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-danger mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Warning: This action cannot be undone!
                </p>
                <p class="mt-3">
                    Are you sure you want to delete "<span id="deleteProductName" class="fw-bold"></span>"?
                    All product information will be permanently deleted and cannot be retrieved.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="<?php echo BASE_URL; ?>/admin/pawducts" id="deleteProductForm">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="deleteProductId">
                    <button type="submit" class="btn btn-danger">Delete Product</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function editProduct(product) {
    document.getElementById('edit_id').value = product.id;
    document.getElementById('edit_name').value = product.name;
    document.getElementById('edit_type').value = product.type;
    document.getElementById('edit_price').value = product.price;
    document.getElementById('edit_stock_quantity').value = product.stock_quantity;
    document.getElementById('edit_description').value = product.description;
    
    new bootstrap.Modal(document.getElementById('editProductModal')).show();
}

function confirmDelete(product) {
    document.getElementById('deleteProductId').value = product.id;
    document.getElementById('deleteProductName').textContent = product.name;
    new bootstrap.Modal(document.getElementById('deleteConfirmModal')).show();
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(tooltip => {
        new bootstrap.Tooltip(tooltip);
    });
});
</script>

<?php require_once 'views/layout/footer.php'; ?>
