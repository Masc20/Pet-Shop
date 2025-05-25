<?php require_once 'views/layout/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-2">
            <div class="card">
                <div class="card-header gradient-bg text-white">
                    <h6 class="mb-0">Admin Menu</h6>
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?php echo BASE_URL; ?>/admin" class="list-group-item list-group-item-action">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="<?php echo BASE_URL; ?>/admin/pets" class="list-group-item list-group-item-action active">
                        <i class="fas fa-paw"></i> Manage Pets
                    </a>
                    <a href="<?php echo BASE_URL; ?>/admin/products" class="list-group-item list-group-item-action">
                        <i class="fas fa-box"></i> Manage Products
                    </a>
                    <a href="<?php echo BASE_URL; ?>/admin/orders" class="list-group-item list-group-item-action">
                        <i class="fas fa-shopping-cart"></i> Manage Orders
                    </a>
                    <a href="<?php echo BASE_URL; ?>/admin/users" class="list-group-item list-group-item-action">
                        <i class="fas fa-users"></i> Manage Users
                    </a>
                    <a href="<?php echo BASE_URL; ?>/admin/settings" class="list-group-item list-group-item-action">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="fw-bold">Manage Pets</h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPetModal">
                    <i class="fas fa-plus"></i> Add New Pet
                </button>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Gender</th>
                                    <th>Age</th>
                                    <th>Breed</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pets as $pet): ?>
                                <tr>
                                    <td><?php echo $pet['id']; ?></td>
                                    <td>
                                        <img src="<?php echo $pet['pet_image']; ?>" alt="<?php echo $pet['name']; ?>" 
                                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                    </td>
                                    <td><?php echo $pet['name']; ?></td>
                                    <td><span class="badge bg-info"><?php echo ucfirst($pet['type']); ?></span></td>
                                    <td><?php echo ucfirst($pet['gender']); ?></td>
                                    <td><?php echo $pet['age']; ?> years</td>
                                    <td><?php echo $pet['breed']; ?></td>
                                    <td>
                                        <?php if ($pet['is_adopted']): ?>
                                            <span class="badge bg-success">Adopted</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Available</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="editPet(<?php echo htmlspecialchars(json_encode($pet)); ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $pet['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
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
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="mb-3">
                        <label class="form-label">Pet Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Image URL</label>
                        <input type="url" class="form-control" name="pet_image" placeholder="/placeholder.svg?height=300&width=300">
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
                            <label class="form-label">Age (years)</label>
                            <input type="number" class="form-control" name="age" min="0" max="20" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Breed</label>
                            <input type="text" class="form-control" name="breed" required>
                        </div>
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
            <form method="POST" id="editPetForm">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="edit_pet_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Pet Name</label>
                        <input type="text" class="form-control" name="name" id="edit_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Image URL</label>
                        <input type="url" class="form-control" name="pet_image" id="edit_pet_image">
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
                            <label class="form-label">Age (years)</label>
                            <input type="number" class="form-control" name="age" id="edit_age" min="0" max="20" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Breed</label>
                            <input type="text" class="form-control" name="breed" id="edit_breed" required>
                        </div>
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
function editPet(pet) {
    document.getElementById('edit_pet_id').value = pet.id;
    document.getElementById('edit_name').value = pet.name;
    document.getElementById('edit_pet_image').value = pet.pet_image;
    document.getElementById('edit_type').value = pet.type;
    document.getElementById('edit_gender').value = pet.gender;
    document.getElementById('edit_age').value = pet.age;
    document.getElementById('edit_breed').value = pet.breed;
    
    new bootstrap.Modal(document.getElementById('editPetModal')).show();
}
</script>

<?php require_once 'views/layout/footer.php'; ?>
