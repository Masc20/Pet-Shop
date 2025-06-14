<?php
require_once 'models/User.php';
require_once 'models/Pet.php';
require_once 'models/Product.php';
require_once 'models/Order.php';
require_once 'models/PetOrder.php';
require_once 'models/Category.php';

class AdminController extends Controller {
    public function __construct() {
        if (!isAdmin()) {
            $this->redirect('/');
            exit;
        }
    }
    
    public function dashboard() {
        $petModel = new Pet();
        $productModel = new Product();
        $orderModel = new Order();
        $userModel = new User();
        
        $petStats = $petModel->getStats();
        $productStats = $productModel->getStats();
        $orderStats = $orderModel->getStats();
        $users = $userModel->getAll();
        
        // Get date range from GET parameters
        $startDate = $_GET['startDate'] ?? null;
        $endDate = $_GET['endDate'] ?? null;
        
        // Get analytics data for charts
        $monthlyRevenue = $orderModel->getMonthlyRevenue($startDate, $endDate);
        $productSales = $productModel->getProductSales($startDate, $endDate);
        $petAdoptions = $petModel->getMonthlyAdoptions($startDate, $endDate);
        $topProducts = $productModel->getProductSales($startDate, $endDate);
        $lowStockProducts = $productModel->getTopOutOfStockProducts();
        
        // Get distribution data for pie charts
        $productTypeDistribution = $productModel->getProductTypeDistribution();
        $petTypeDistribution = $petModel->getPetTypeDistribution();
        
        

        $this->view('admin/dashboard', [
            'petStats' => $petStats,
            'productStats' => $productStats,
            'orderStats' => $orderStats,
            'users' => $users,
            'monthlyRevenue' => $monthlyRevenue,
            'productSales' => $productSales,
            'petAdoptions' => $petAdoptions,
            'topProducts' => $topProducts,
            'lowStockProducts' => $lowStockProducts,
            'productTypeDistribution' => $productTypeDistribution,
            'petTypeDistribution' => $petTypeDistribution
        ]);
    }
    
    public function filter() {
        if (!isAdmin()) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $startDate = $_GET['startDate'] ?? null;
        $endDate = $_GET['endDate'] ?? null;

        $petModel = new Pet();
        $productModel = new Product();
        $orderModel = new Order();

        // Get filtered data for all charts
        $monthlyRevenue = $orderModel->getMonthlyRevenue($startDate, $endDate);
        $petAdoptions = $petModel->getMonthlyAdoptions($startDate, $endDate);
        $topProducts = $productModel->getProductSales($startDate, $endDate);
        $lowStockProducts = $productModel->getTopOutOfStockProducts();
        $productTypeDistribution = $productModel->getProductTypeDistribution();
        $petTypeDistribution = $petModel->getPetTypeDistribution();

        echo json_encode([
            'monthlyRevenue' => $monthlyRevenue,
            'petAdoptions' => $petAdoptions,
            'topProducts' => $topProducts,
            'lowStockProducts' => $lowStockProducts,
            'productTypeDistribution' => $productTypeDistribution,
            'petTypeDistribution' => $petTypeDistribution
        ]);
    }
    
    public function pets() {
        $petModel = new Pet();
        
        // Pagination settings for admin
        $limit = 10; // Number of rows per page
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;
        
        // Get search and filter parameters for admin pets
        $query = $_GET['q'] ?? '';
        $type = $_GET['type'] ?? null;
        $gender = $_GET['gender'] ?? null;
        $breed = $_GET['breed'] ?? null;
        $minAge = isset($_GET['min_age']) && $_GET['min_age'] !== '' ? (int)$_GET['min_age'] : null;
        $maxAge = isset($_GET['max_age']) && $_GET['max_age'] !== '' ? (int)$_GET['max_age'] : null;
        $sortBy = $_GET['sort'] ?? 'created_at';
        $sortOrder = $_GET['order'] ?? 'DESC';

        error_log("AdminController pets - Parameters: query=$query, type=$type, gender=$gender, breed=$breed, minAge=$minAge, maxAge=$maxAge");

        // Get paginated pets and total count based on search and filters
        $pets = $petModel->getAdminPaginated($limit, $offset, $query, $type, $gender, $breed, $minAge, $maxAge, $sortBy, $sortOrder);
        $totalPets = $petModel->getAdminTotalCount($query, $type, $gender, $breed, $minAge, $maxAge);

        error_log("AdminController pets - Total pets: $totalPets");

        $totalPages = ceil($totalPets / $limit);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'];
            
            if ($action === 'create') {
                $imagePath = null;
                if (isset($_FILES['pet_image']) && $_FILES['pet_image']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = __DIR__ . '/../uploads/pets/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                    $filename = uniqid() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '', basename($_FILES['pet_image']['name']));
                    $targetFile = $uploadDir . $filename;
                    if (move_uploaded_file($_FILES['pet_image']['tmp_name'], $targetFile)) {
                        $imagePath = '/uploads/pets/' . $filename;
                    }
                }
                $data = [
                    'name' => $_POST['name'],
                    'pet_image' => $imagePath,
                    'type' => $_POST['type'],
                    'gender' => $_POST['gender'],
                    'age' => $_POST['age'],
                    'birthday' => $_POST['birthday'],
                    'breed' => $_POST['breed'],
                    'description' => $_POST['description'],
                    'price' => $_POST['price']
                ];
                $petModel->create($data);
            } elseif ($action === 'update') {
                $imagePath = $_POST['current_pet_image'];
                if (isset($_FILES['pet_image']) && $_FILES['pet_image']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = __DIR__ . '/../uploads/pets/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                    $filename = uniqid() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '', basename($_FILES['pet_image']['name']));
                    $targetFile = $uploadDir . $filename;
                    if (move_uploaded_file($_FILES['pet_image']['tmp_name'], $targetFile)) {
                        $imagePath = '/uploads/pets/' . $filename;
                        // Delete old image if it exists and is not the default image
                        if ($_POST['current_pet_image'] && $_POST['current_pet_image'] !== '/uploads/pets/default.jpg') {
                            $oldImagePath = __DIR__ . '/..' . $_POST['current_pet_image'];
                            if (file_exists($oldImagePath)) {
                                unlink($oldImagePath);
                            }
                        }
                    }
                }
                $data = [
                    'name' => $_POST['name'],
                    'pet_image' => $imagePath,
                    'type' => $_POST['type'],
                    'gender' => $_POST['gender'],
                    'age' => $_POST['age'],
                    'birthday' => $_POST['birthday'],
                    'breed' => $_POST['breed'],
                    'description' => $_POST['description'],
                    'price' => $_POST['price']
                ];
                $petModel->update($_POST['id'], $data);
            } elseif ($action === 'delete') {
                $petModel->delete($_POST['id']);
            }
            
            // Redirect to the current page after action
            $this->redirect('/admin/pets?page=' . $page);
        }
        
        $this->view('admin/pets', [
            'pets' => $pets,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'searchQuery' => $query,
            'filterType' => $type,
            'filterGender' => $gender,
            'filterBreed' => $breed,
            'filterMinAge' => $minAge,
            'filterMaxAge' => $maxAge,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
            'pageTitle' => 'Manage Pets'
        ]);
    }
    
    public function products() {
        if (!isAdmin()) {
            $this->redirect('/login');
            return;
        }

        $productModel = new Product();
        
        // Handle product actions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            
            switch ($action) {
                case 'create':
                    $imagePath = null;
                    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                        $uploadDir = __DIR__ . '/../uploads/products/';
                        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                        $filename = uniqid() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '', basename($_FILES['product_image']['name']));
                        $targetFile = $uploadDir . $filename;
                        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $targetFile)) {
                            $imagePath = '/uploads/products/' . $filename;
                        }
                    }
                    
                    $data = [
                        'name' => $_POST['name'],
                        'product_image' => $imagePath,
                        'stock_quantity' => $_POST['stock_quantity'],
                        'type' => $_POST['type'],
                        'price' => $_POST['price'],
                        'description' => $_POST['description']
                    ];
                    
                    if ($productModel->create($data)) {
                        setFlashMessage('success', 'Product created successfully!');
                    } else {
                        setFlashMessage('error', 'Failed to create product. Please try again.');
                    }
                    break;
                    
                case 'update':
                    // Get the current product to preserve its image
                    $currentProduct = $productModel->getById($_POST['id']);
                    $imagePath = $currentProduct['product_image']; // Keep existing image by default
                    
                    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                        $uploadDir = __DIR__ . '/../uploads/products/';
                        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                        $filename = uniqid() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '', basename($_FILES['product_image']['name']));
                        $targetFile = $uploadDir . $filename;
                        
                        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $targetFile)) {
                            $imagePath = '/uploads/products/' . $filename;
                            
                            // Delete old image if it exists and is not the default image
                            if (!empty($currentProduct['product_image']) && 
                                $currentProduct['product_image'] !== '/assets/images/default-product.png' && 
                                file_exists(__DIR__ . '/..' . $currentProduct['product_image'])) {
                                unlink(__DIR__ . '/..' . $currentProduct['product_image']);
                            }
                        }
                    }
                    
                    $data = [
                        'name' => $_POST['name'],
                        'product_image' => $imagePath,
                        'stock_quantity' => $_POST['stock_quantity'],
                        'type' => $_POST['type'],
                        'price' => $_POST['price'],
                        'description' => $_POST['description']
                    ];
                    
                    if ($productModel->update($_POST['id'], $data)) {
                        setFlashMessage('success', 'Product updated successfully!');
                    } else {
                        setFlashMessage('error', 'Failed to update product. Please try again.');
                    }
                    break;
                    
                case 'delete':
                    if ($productModel->delete($_POST['id'])) {
                        setFlashMessage('success', 'Product deleted successfully!');
                    } else {
                        setFlashMessage('error', 'Failed to delete product. Please try again.');
                    }
                    break;
            }
            
            $this->redirect('/admin/pawducts');
            return;
        }
        
        // Get search and filter parameters
        $query = $_GET['q'] ?? '';
        $type = $_GET['type'] ?? '';
        $minPrice = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (float)$_GET['min_price'] : null;
        $maxPrice = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (float)$_GET['max_price'] : null;
        $stockStatus = $_GET['stock_status'] ?? '';
        $sortBy = $_GET['sort'] ?? 'id';
        $sortOrder = $_GET['order'] ?? 'DESC';
        
        // Pagination settings
        $limit = 10;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;
        
        // Get products with search, filter, pagination, and sorting
        $products = $productModel->getPaginated(
            $limit,
            $offset,
            null, // category_id is not used
            $minPrice,
            $maxPrice,
            $query,
            $sortBy,
            $sortOrder
        );
        
        $totalProducts = $productModel->getTotalCount($type, $minPrice, $maxPrice, $query, $stockStatus);
        $totalPages = ceil($totalProducts / $limit);
        
        $this->view('admin/products', [
            'products' => $products,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'searchQuery' => $query,
            'filterType' => $type,
            'filterMinPrice' => $minPrice,
            'filterMaxPrice' => $maxPrice,
            'filterStockStatus' => $stockStatus,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
            'pageTitle' => 'Manage Pawducts'
        ]);
    }
    
    public function orders() {
        if (!isAdmin()) {
            redirect('login');
        }

        $page = $_GET['page'] ?? 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $query = $_GET['q'] ?? null;
        $status = $_GET['status'] ?? null;
        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;
        $sortBy = $_GET['sort'] ?? 'order_date';
        $sortOrder = $_GET['order'] ?? 'DESC';

        $orderModel = new Order();
        $orders = $orderModel->getAdminPaginated($limit, $offset, $query, $status, $startDate, $endDate, $sortBy, $sortOrder);
        $totalOrders = $orderModel->getAdminTotalCount($query, $status, $startDate, $endDate);
        $totalPages = ceil($totalOrders / $limit);

        $this->view('admin/orders', [
            'orders' => $orders,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'query' => $query,
            'status' => $status,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder
        ]);
    }
    
    public function users() {
        $userModel = new User();
        
        // Pagination settings for admin
        $limit = 10; // Number of rows per page
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        // Get search and filter parameters for admin users
        $query = $_GET['q'] ?? '';
        $role = $_GET['role'] ?? null;

        // Get paginated users and total count for admin view
        // This assumes getAdminPaginated and getAdminTotalCount in User.php
        // are updated to handle search query and role filter.
        $users = $userModel->getAdminPaginated($limit, $offset, $query, $role); // Add new parameters
        $totalUsers = $userModel->getAdminTotalCount($query, $role); // Add new parameters

        $totalPages = ceil($totalUsers / $limit);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'];
            $userId = $_POST['user_id'];
            
            if ($action === 'ban') {
                $userModel->banUser($userId);
            } elseif ($action === 'unban') {
                $userModel->unbanUser($userId);
            } elseif ($action === 'role') {
                $userModel->updateRole($userId, $_POST['role']);
            }
            
            // Redirect to the current page after action
            $this->redirect('/admin/users?page=' . $page);
        }
        
        $this->view('admin/users', [
            'users' => $users,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'searchQuery' => $query, // Pass search query to view
            'filterRole' => $role, // Pass role filter to view
            'pageTitle' => 'Manage Users'
        ]);
    }
    
    public function settings() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle logo upload
            $logoPath = getSetting('site_logo'); // Keep existing logo by default
            if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../public/uploads/logo/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                
                // Generate unique filename
                $filename = uniqid() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '', basename($_FILES['site_logo']['name']));
                $targetFile = $uploadDir . $filename;
                
                // Check if image file is a actual image
                $check = getimagesize($_FILES['site_logo']['tmp_name']);
                if ($check !== false) {
                    if (move_uploaded_file($_FILES['site_logo']['tmp_name'], $targetFile)) {
                        $logoPath = '/public/uploads/logo/' . $filename;
                    }
                }
            }
            
            // Update settings
            setSetting('brand_name', $_POST['brand_name']);
            setSetting('site_logo', $logoPath);
            setSetting('primary_color', $_POST['primary_color']);
            setSetting('secondary_color', $_POST['secondary_color']);
            
            setFlashMessage('success', 'Settings updated successfully!');
            $this->redirect('/admin/settings');
        }
        
        $this->view('admin/settings', [
            'pageTitle' => 'Settings'
        ]);
    }

    public function adoptionOrders() {
        $petOrderModel = new PetOrder();
        $limit = 10;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;
        $query = $_GET['q'] ?? '';
        $status = $_GET['status'] ?? null;

        // Get paginated pet orders and total count
        $orders = $petOrderModel->getAdminPaginated($limit, $offset, $query, $status);
        $totalOrders = $petOrderModel->getAdminTotalCount($query, $status);
        $totalPages = ceil($totalOrders / $limit);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = $_POST['order_id'];
            $newStatus = $_POST['status'];
            $petOrderModel->updateStatus($orderId, $newStatus);
            $_SESSION['success'] = 'Pet order status updated!';
            $this->redirect('/admin/pet-orders?page=' . $page);
        }

        $this->view('admin/pet_orders', [
            'orders' => $orders,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'searchQuery' => $query,
            'filterStatus' => $status,
            'pageTitle' => 'Manage Pet Orders'
        ]);
    }

    public function updatePetOrderStatus() {
        if (!isAdmin()) {
            setFlashMessage('error', 'You do not have permission to update pet order status.');
            $this->redirect('/admin/pet-orders');
        }

        $orderId = $_POST['order_id'] ?? null;
        $status = $_POST['status'] ?? '';
        $adminNotes = $_POST['admin_notes'] ?? null;

        if (!$orderId || !in_array($status, ['pending', 'approved', 'rejected', 'cancelled'])) {
            setFlashMessage('error', 'Invalid request parameters.');
            $this->redirect('/admin/pet-orders');
        }

        $petOrderModel = new PetOrder();

        try {
            // Update the order status
            $success = $petOrderModel->updateStatus($orderId, $status, $adminNotes);
            setFlashMessage('success', 'Pet order status updated successfully.');
        } catch (Exception $e) {
            error_log("Error in updatePetOrderStatus: " . $e->getMessage());
            setFlashMessage('error', 'Failed to update pet order status: ' . $e->getMessage());
        }

        $this->redirect('/admin/pet-orders');
    }

    public function updateOrderStatus() {
        if (!isAdmin()) {
            setFlashMessage('error', 'You do not have permission to update order status.');
            $this->redirect('/admin/orders');
        }

        $orderId = $_POST['order_id'] ?? null;
        $status = $_POST['status'] ?? '';
        $adminNotes = $_POST['admin_notes'] ?? null;

        if (!$orderId || !in_array($status, ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])) {
            setFlashMessage('error', 'Invalid request parameters.');
            $this->redirect('/admin/orders');
        }

        $orderModel = new Order();

        try {
            // Update the order status
            $success = $orderModel->updateStatus($orderId, $status, $adminNotes);
            setFlashMessage('success', 'Order status updated successfully.');
        } catch (Exception $e) {
            error_log("Error in updateOrderStatus: " . $e->getMessage());
            setFlashMessage('error', 'Failed to update order status: ' . $e->getMessage());
        }

        $this->redirect('/admin/orders');
    }
}
?>
