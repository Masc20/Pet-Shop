<?php require_once 'views/layout/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-2">
            <?php require_once 'views/layout/admin_sidebar.php'; ?>
        </div>
        
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="fw-bold">Manage Pets</h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPetModal">
                    <i class="fas fa-plus"></i> Add New Pet
                </button>
            </div>

            <!-- Search and Filter Form for Admin Pets -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <form method="GET" action="<?php echo BASE_URL; ?>/admin/pets">
                        <div class="row g-3">
                            <div class="col-md">
                                <input type="text" name="q" class="form-control" placeholder="Search pets..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
                            </div>
                            <div class="col-md">
                                <select name="type" class="form-select">
                                    <option value="">All Type</option>
                                    <option value="dogs" <?php echo (isset($_GET['type']) && $_GET['type'] === 'dogs') ? 'selected' : ''; ?>>Dogs</option>
                                    <option value="cats" <?php echo (isset($_GET['type']) && $_GET['type'] === 'cats') ? 'selected' : ''; ?>>Cats</option>
                                </select>
                            </div>
                            <div class="col-md">
                                <select name="gender" class="form-select">
                                    <option value="">All Genders</option>
                                    <option value="male" <?php echo (isset($_GET['gender']) && $_GET['gender'] === 'male') ? 'selected' : ''; ?>>Male</option>
                                    <option value="female" <?php echo (isset($_GET['gender']) && $_GET['gender'] === 'female') ? 'selected' : ''; ?>>Female</option>
                                </select>
                            </div>
                             <div class="col-md">
                                <input type="text" name="breed" class="form-control" placeholder="Filter by Breed" value="<?php echo htmlspecialchars($_GET['breed'] ?? ''); ?>">
                            </div>
                            <div class="col-md">
                                <input type="number" name="min_age" class="form-control" placeholder="Min Age" min="0" value="<?php echo htmlspecialchars($_GET['min_age'] ?? ''); ?>">
                           </div>
                            <div class="col-md">
                                <input type="number" name="max_age" class="form-control" placeholder="Max Age" min="0" value="<?php echo htmlspecialchars($_GET['max_age'] ?? ''); ?>">
                           </div>
                            <div class="col-md-auto">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                                <?php if (isset($_GET['q']) || isset($_GET['type']) || isset($_GET['gender']) || isset($_GET['breed']) || isset($_GET['min_age']) || isset($_GET['max_age'])): ?>
                                     <a href="<?php echo BASE_URL; ?>/admin/pets" class="btn btn-outline-secondary">Clear Filters</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
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
                                            <a href="?sort=name&order=asc<?php echo isset($_GET['q']) ? '&q=' . urlencode($_GET['q']) : ''; ?><?php echo isset($_GET['type']) ? '&type=' . urlencode($_GET['type']) : ''; ?><?php echo isset($_GET['gender']) ? '&gender=' . urlencode($_GET['gender']) : ''; ?><?php echo isset($_GET['breed']) ? '&breed=' . urlencode($_GET['breed']) : ''; ?>" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Sort by name (A to Z)">
                                                <i class="fas fa-sort-alpha-down"></i>
                                            </a>
                                            <a href="?sort=name&order=desc<?php echo isset($_GET['q']) ? '&q=' . urlencode($_GET['q']) : ''; ?><?php echo isset($_GET['type']) ? '&type=' . urlencode($_GET['type']) : ''; ?><?php echo isset($_GET['gender']) ? '&gender=' . urlencode($_GET['gender']) : ''; ?><?php echo isset($_GET['breed']) ? '&breed=' . urlencode($_GET['breed']) : ''; ?>" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Sort by name (Z to A)">
                                                <i class="fas fa-sort-alpha-up"></i>
                                            </a>
                                        </div>
                                    </th>
                                    <th>
                                        Type
                                    </th>
                                    <th>
                                        Gender
                                    </th>
                                    <th>
                                        Age
                                        <div class="btn-group btn-group-sm ms-2">
                                            <a href="?sort=age&order=asc<?php echo isset($_GET['q']) ? '&q=' . urlencode($_GET['q']) : ''; ?><?php echo isset($_GET['type']) ? '&type=' . urlencode($_GET['type']) : ''; ?><?php echo isset($_GET['gender']) ? '&gender=' . urlencode($_GET['gender']) : ''; ?><?php echo isset($_GET['breed']) ? '&breed=' . urlencode($_GET['breed']) : ''; ?>" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Sort by age (youngest to oldest)">
                                                <i class="fas fa-sort-numeric-down"></i>
                                            </a>
                                            <a href="?sort=age&order=desc<?php echo isset($_GET['q']) ? '&q=' . urlencode($_GET['q']) : ''; ?><?php echo isset($_GET['type']) ? '&type=' . urlencode($_GET['type']) : ''; ?><?php echo isset($_GET['gender']) ? '&gender=' . urlencode($_GET['gender']) : ''; ?><?php echo isset($_GET['breed']) ? '&breed=' . urlencode($_GET['breed']) : ''; ?>" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Sort by age (oldest to youngest)">
                                                <i class="fas fa-sort-numeric-up"></i>
                                            </a>
                                        </div>
                                    </th>
                                    <th>
                                        Birthday
                                    </th>
                                    <th>
                                        Breed
                                        <div class="btn-group btn-group-sm ms-2">
                                            <a href="?sort=breed&order=asc<?php echo isset($_GET['q']) ? '&q=' . urlencode($_GET['q']) : ''; ?><?php echo isset($_GET['type']) ? '&type=' . urlencode($_GET['type']) : ''; ?><?php echo isset($_GET['gender']) ? '&gender=' . urlencode($_GET['gender']) : ''; ?><?php echo isset($_GET['breed']) ? '&breed=' . urlencode($_GET['breed']) : ''; ?>" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Sort by breed (A to Z)">
                                                <i class="fas fa-sort-alpha-down"></i>
                                            </a>
                                            <a href="?sort=breed&order=desc<?php echo isset($_GET['q']) ? '&q=' . urlencode($_GET['q']) : ''; ?><?php echo isset($_GET['type']) ? '&type=' . urlencode($_GET['type']) : ''; ?><?php echo isset($_GET['gender']) ? '&gender=' . urlencode($_GET['gender']) : ''; ?><?php echo isset($_GET['breed']) ? '&breed=' . urlencode($_GET['breed']) : ''; ?>" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Sort by breed (Z to A)">
                                                <i class="fas fa-sort-alpha-up"></i>
                                            </a>
                                        </div>
                                    </th>
                                    <th>
                                        Price
                                        <div class="btn-group btn-group-sm ms-2">
                                            <a href="?sort=price&order=asc<?php echo isset($_GET['q']) ? '&q=' . urlencode($_GET['q']) : ''; ?><?php echo isset($_GET['type']) ? '&type=' . urlencode($_GET['type']) : ''; ?><?php echo isset($_GET['gender']) ? '&gender=' . urlencode($_GET['gender']) : ''; ?><?php echo isset($_GET['breed']) ? '&breed=' . urlencode($_GET['breed']) : ''; ?>" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Sort by price (lowest to highest)">
                                                <i class="fas fa-sort-numeric-down"></i>
                                            </a>
                                            <a href="?sort=price&order=desc<?php echo isset($_GET['q']) ? '&q=' . urlencode($_GET['q']) : ''; ?><?php echo isset($_GET['type']) ? '&type=' . urlencode($_GET['type']) : ''; ?><?php echo isset($_GET['gender']) ? '&gender=' . urlencode($_GET['gender']) : ''; ?><?php echo isset($_GET['breed']) ? '&breed=' . urlencode($_GET['breed']) : ''; ?>" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Sort by price (highest to lowest)">
                                                <i class="fas fa-sort-numeric-up"></i>
                                            </a>
                                        </div>
                                    </th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($pets)): ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <i class="fas fa-paw fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No pets found</h5>
                                            <p class="text-muted">Try adjusting your search or filters.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                <?php foreach ($pets as $pet): ?>
                                <tr>
                                        <td data-label="Image">
                                            <img src="<?php echo BASE_URL . htmlspecialchars($pet['pet_image']); ?>" 
                                                 alt="<?php echo $pet['name']; ?>" 
                                                 class="rounded" 
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        </td>
                                        <td data-label="Name">
                                            <div class="d-flex align-items-center">
                                                <?php echo $pet['name']; ?>
                                            </div>
                                    </td>
                                        <td data-label="Type"><?php echo ucfirst($pet['type']); ?></td>
                                        <td data-label="Gender"><?php echo ucfirst($pet['gender'])?></td>
                                        <td data-label="Age"><?php echo $pet['age']; ?> years</td>
                                        <td data-label="Birthday"><?php echo date('M d, Y', strtotime($pet['birthday'])); ?></td>
                                        <td data-label="Breed"><?php echo $pet['breed']; ?></td>
                                        <td data-label="Price">₱<?php echo number_format($pet['price'], 2); ?></td>
                                        <td data-label="Status">
                                        <?php if ($pet['is_adopted']): ?>
                                            <span class="badge bg-success">Adopted</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Available</span>
                                        <?php endif; ?>
                                    </td>
                                        <td data-label="Actions">
                                            <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-primary" onclick="editPet(<?php echo htmlspecialchars(json_encode($pet)); ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete(<?php echo htmlspecialchars(json_encode($pet)); ?>)">
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
                </div>
            </div>

            <?php if ($totalPages > 1): ?>
            <nav aria-label="Admin pet pagination">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php echo ($currentPage <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="<?php echo BASE_URL; ?>/admin/pets?page=<?php echo $currentPage - 1; ?>">Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo ($i == $currentPage) ? 'active' : ''; ?>"><a class="page-link" href="<?php echo BASE_URL; ?>/admin/pets?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                    <?php endfor; ?>
                    <li class="page-item <?php echo ($currentPage >= $totalPages) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="<?php echo BASE_URL; ?>/admin/pets?page=<?php echo $currentPage + 1; ?>">Next</a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deletePetModal" tabindex="-1" aria-labelledby="deletePetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deletePetModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete <strong id="deletePetName"></strong>? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="deletePetId">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Pet Modal -->
<div class="modal fade" id="addPetModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Pet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="mb-3">
                        <label class="form-label">Pet Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Pet Image</label>
                        <input type="file" class="form-control" name="pet_image" accept="image/*" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type</label>
                            <select class="form-control" name="type" required>
                                <option value="dogs">Dog</option>
                                <option value="cats">Cat</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Gender</label>
                            <select class="form-control" name="gender" required>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Birthday</label>
                            <input type="date" class="form-control" name="birthday" id="add_birthday" required onchange="calculateAge(this.value, 'add_age')">
                            <input type="hidden" name="age" id="add_age">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Breed</label>
                            <input type="text" class="form-control" name="breed" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                        <label class="form-label">Price (₱)</label>
                        <input type="number" class="form-control" name="price" min="0" step="0.01" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3" placeholder="Enter pet's description, personality, and any special needs..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Pet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Pet Modal -->
<div class="modal fade" id="editPetModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Pet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data" action="<?php echo BASE_URL; ?>/admin/pets">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="edit_pet_id">
                    <input type="hidden" name="current_pet_image" id="edit_current_pet_image">
                    
                    <div class="mb-3">
                        <label class="form-label">Pet Name</label>
                        <input type="text" class="form-control" name="name" id="edit_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Pet Image</label>
                        <input type="file" class="form-control" name="pet_image" id="edit_pet_image" accept="image/*">
                        <small class="text-muted">Leave empty to keep current image</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type</label>
                            <select class="form-control" name="type" id="edit_type" required>
                                <option value="dogs">Dog</option>
                                <option value="cats">Cat</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Gender</label>
                            <select class="form-control" name="gender" id="edit_gender" required>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Birthday</label>
                            <input type="date" class="form-control" name="birthday" id="edit_birthday" required onchange="calculateAge(this.value, 'edit_age')">
                            <input type="hidden" name="age" id="edit_age">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Breed</label>
                            <input type="text" class="form-control" name="breed" id="edit_breed" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                        <label class="form-label">Price (₱)</label>
                        <input type="number" class="form-control" name="price" id="edit_price" min="0" step="0.01" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="3" placeholder="Enter pet's description, personality, and any special needs..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Pet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function calculateAge(birthday, ageFieldId) {
    const today = new Date();
    const birthDate = new Date(birthday);
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    
    document.getElementById(ageFieldId).value = age;
}

function editPet(pet) {
    // Set form values
    document.getElementById('edit_pet_id').value = pet.id;
    document.getElementById('edit_name').value = pet.name;
    document.getElementById('edit_type').value = pet.type;
    document.getElementById('edit_gender').value = pet.gender;
    document.getElementById('edit_birthday').value = pet.birthday;
    calculateAge(pet.birthday, 'edit_age');
    document.getElementById('edit_breed').value = pet.breed;
    document.getElementById('edit_price').value = pet.price;
    document.getElementById('edit_description').value = pet.description;
    document.getElementById('edit_current_pet_image').value = pet.pet_image;
    
    // Show the modal
    const editModal = new bootstrap.Modal(document.getElementById('editPetModal'));
    editModal.show();
}

// Calculate age for all pets in the table when page loads
document.addEventListener('DOMContentLoaded', function() {
    const petRows = document.querySelectorAll('tbody tr');
    petRows.forEach(row => {
        const birthdayCell = row.querySelector('td[data-label="Birthday"]');
        const ageCell = row.querySelector('td[data-label="Age"]');
        if (birthdayCell && ageCell) {
            const birthday = birthdayCell.getAttribute('data-birthday');
            if (birthday) {
                const today = new Date();
                const birthDate = new Date(birthday);
                let age = today.getFullYear() - birthDate.getFullYear();
                const monthDiff = today.getMonth() - birthDate.getMonth();
                
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }
                
                ageCell.textContent = age + ' years';
            }
        }
    });
});

function confirmDelete(pet) {
    // Set the pet details in the modal
    document.getElementById('deletePetName').textContent = pet.name;
    document.getElementById('deletePetId').value = pet.id;
    
    // Show the modal
    const deleteModal = new bootstrap.Modal(document.getElementById('deletePetModal'));
    deleteModal.show();
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

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
</style>

<?php require_once 'views/layout/footer.php'; ?>
