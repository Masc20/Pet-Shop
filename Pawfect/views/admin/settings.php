<?php require_once 'views/layout/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <?php require_once 'views/layout/admin_sidebar.php'; ?>
        </div>
        <div class="col-md-10 py-4">
            <h2 class="fw-bold mb-4">Website Settings</h2>
            
            <div class="card">
                <div class="card-header">
                    <h5>Customize Website</h5>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="brand_name" class="form-label">Brand Name</label>
                            <input type="text" class="form-control" id="brand_name" name="brand_name" 
                                   value="<?php echo getSetting('brand_name', 'Pawfect Pet Shop'); ?>" required>
                            <div class="form-text">This will be used throughout the website as the main brand name.</div>
                        </div>

                        <div class="mb-3">
                            <label for="site_logo" class="form-label">Site Logo</label>
                            <input type="file" class="form-control" id="site_logo" name="site_logo" accept="image/*">
                            <div class="form-text">Upload your logo image (recommended size: 200x50px)</div>
                            <?php if (getSetting('site_logo')): ?>
                                <div class="mt-2">
                                    <small>Current logo:</small><br>
                                    <img src="<?php echo BASE_URL . getSetting('site_logo'); ?>" alt="Current Logo" style="max-height: 50px;" class="mt-1">
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="primary_color" class="form-label">Primary Color</label>
                                <input type="color" class="form-control form-control-color" id="primary_color" 
                                       name="primary_color" value="<?php echo getSetting('primary_color', '#FF8C00'); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="secondary_color" class="form-label">Secondary Color</label>
                                <input type="color" class="form-control form-control-color" id="secondary_color" 
                                       name="secondary_color" value="<?php echo getSetting('secondary_color', '#FFD700'); ?>">
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Settings
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Preview Section -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Preview</h5>
                </div>
                <div class="card-body">
                    <div class="border rounded p-3 mb-4">
                        <h6 class="mb-3">Navbar Preview</h6>
                        <nav class="navbar navbar-light bg-white">
                            <div class="container-fluid">
                                <a class="navbar-brand" href="#">
                                    <?php if (getSetting('site_logo')): ?>
                                        <img src="<?php echo BASE_URL . getSetting('site_logo'); ?>" alt="Logo Preview" class="logo-img">
                                    <?php else: ?>
                                        <div class="text-muted">No logo uploaded</div>
                                    <?php endif; ?>
                                </a>
                            </div>
                        </nav>
                    </div>
                    
                    <div class="text-center">
                        <div class="d-inline-block p-3 rounded" style="background: linear-gradient(135deg, <?php echo getSetting('primary_color'); ?>, <?php echo getSetting('secondary_color'); ?>);">
                            <span class="text-white fw-bold">Color Preview</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(tooltip => {
        new bootstrap.Tooltip(tooltip);
    });
});
</script>

<?php require_once 'views/layout/footer.php'; ?>
