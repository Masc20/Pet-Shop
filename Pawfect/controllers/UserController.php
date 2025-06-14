<?php
require_once 'models/User.php';
require_once 'models/Order.php';
require_once 'models/Pet.php';
require_once 'models/Product.php';

class UserController extends Controller
{
    private $userModel;
    private $orderModel;
    private $productModel;
    private $petModel;

    public function __construct() {
        $this->userModel = new User();
        $this->orderModel = new Order();
        $this->productModel = new Product();
        $this->petModel = new Pet();
    }

    public function profile()
    {
        if (!isLoggedIn()) {
            $this->redirect('/login');
            return;
        }

        $userModel = new User();
        $orderModel = new Order();
        $petModel = new Pet();

        // Handle cancel order action
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel_order') {
            $orderId = $_POST['order_id'] ?? null;

            if (!$orderId) {
                $_SESSION['error'] = 'Invalid cancellation request';
            } else {
                // Check if the order belongs to the user and can be cancelled
                if (!$orderModel->canBeCancelled($orderId, $_SESSION['user_id'])) {
                    $_SESSION['error'] = 'This order cannot be cancelled';
                } else {
                    // Attempt to cancel the order
                    if ($orderModel->cancelOrder($orderId, $_SESSION['user_id'])) {
                        $_SESSION['success'] = 'Order cancelled successfully';
                    } else {
                        $_SESSION['error'] = 'Failed to cancel order';
                    }
                }
            }
            $this->redirect('/profile');
            return;
        }

        $user = $userModel->getById($_SESSION['user_id']);

        // Pagination settings for active orders
        $limit = 5; // Number of orders per page
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        // Get paginated active orders for the user
        $orders = $orderModel->getUserOrders($_SESSION['user_id'], $limit, $offset, ['pending', 'processing', 'shipped', 'delivered']);
        $totalOrders = $orderModel->getUserOrdersCount($_SESSION['user_id'], ['pending', 'processing', 'shipped', 'delivered']);
        $totalPages = ceil($totalOrders / $limit);

        // Debug: Log orders data
        error_log("User ID: " . $_SESSION['user_id']);
        error_log("Total Orders: " . $totalOrders);
        error_log("Orders Data: " . print_r($orders, true));

        // Pagination settings for cancelled orders
        $cancelledPage = isset($_GET['cancelled_page']) ? (int)$_GET['cancelled_page'] : 1;
        $cancelledOffset = ($cancelledPage - 1) * $limit;

        // Get paginated cancelled orders for the user
        $cancelledOrders = $orderModel->getUserOrders($_SESSION['user_id'], $limit, $cancelledOffset, ['cancelled']);
        $totalCancelledOrders = $orderModel->getUserOrdersCount($_SESSION['user_id'], ['cancelled']);
        $cancelledTotalPages = ceil($totalCancelledOrders / $limit);

        // Debug: Log cancelled orders data
        error_log("Total Cancelled Orders: " . $totalCancelledOrders);
        error_log("Cancelled Orders Data: " . print_r($cancelledOrders, true));

        // Fetch user's delivery addresses
        global $pdo;
        $stmt = $pdo->prepare('SELECT * FROM delivery_addresses WHERE user_id = ? ORDER BY id LIMIT 1');
        $stmt->execute([$_SESSION['user_id']]);
        $deliveryAddress = $stmt->fetch() ?: []; // Fetch one address, default to empty array if none found

        // Get adopted pets
        $adoptedPets = $petModel->getAdoptedPetsByUser($_SESSION['user_id']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone'],
                'address' => $_POST['address']
            ];

            // Handle avatar upload
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../uploads/avatars/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $filename = uniqid() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '', basename($_FILES['avatar']['name']));
                $targetFile = $uploadDir . $filename;
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetFile)) {
                    $data['avatar'] = '/uploads/avatars/' . $filename;
                    
                    // Delete old avatar if it exists
                    if ($user['avatar'] && file_exists(__DIR__ . '/..' . $user['avatar'])) {
                        unlink(__DIR__ . '/..' . $user['avatar']);
                    }
                }
            } else {
                // Keep existing avatar if no new one is uploaded
                $data['avatar'] = $user['avatar'];
            }

            if ($userModel->update($_SESSION['user_id'], $data)) {
                setFlashMessage('success', 'Profile updated successfully!');
                $_SESSION['user_name'] = $data['first_name'] . ' ' . $data['last_name'];
            } else {
                setFlashMessage('error', 'Failed to update profile');
            }
            $this->redirect('/profile');
        }

        $this->view('user/profile', [
            'user' => $user,
            'orders' => $orders,
            'cancelledOrders' => $cancelledOrders,
            'delivery_address' => $deliveryAddress,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'cancelledCurrentPage' => $cancelledPage,
            'cancelledTotalPages' => $cancelledTotalPages,
            'adoptedPets' => $adoptedPets,
            'pageTitle' => 'Profile'
        ]);
    }

    public function updateAddress()
    {
        if (!isLoggedIn()) {
            $this->redirect('/login');
            return;
        }
        $userId = $_SESSION['user_id'];
        $city = $_POST['city'] ?? '';
        $barangay = $_POST['barangay'] ?? '';
        $street = $_POST['street'] ?? '';
        $zipcode = $_POST['zipcode'] ?? '';
        if ($city && $barangay && $street && $zipcode) {
            global $pdo;
            // Check if address exists
            $stmt = $pdo->prepare('SELECT id FROM delivery_addresses WHERE user_id = ?');
            $stmt->execute([$userId]);
            if ($stmt->fetch()) {
                // Update
                $stmt = $pdo->prepare('UPDATE delivery_addresses SET city=?, barangay=?, street=?, zipcode=? WHERE user_id=?');
                if ($stmt->execute([$city, $barangay, $street, $zipcode, $userId])) {
                    setFlashMessage('success', 'Delivery address updated successfully!');
                } else {
                    setFlashMessage('error', 'Failed to update delivery address');
                }
            } else {
                // Insert
                $stmt = $pdo->prepare('INSERT INTO delivery_addresses (user_id, city, barangay, street, zipcode) VALUES (?, ?, ?, ?, ?)');
                if ($stmt->execute([$userId, $city, $barangay, $street, $zipcode])) {
                    setFlashMessage('success', 'Delivery address added successfully!');
                } else {
                    setFlashMessage('error', 'Failed to add delivery address');
                }
            }
        } else {
            setFlashMessage('error', 'Please fill in all address fields');
        }
        $this->redirect('/profile');
    }

    public function products() {
        if (!isLoggedIn()) {
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
                        'description' => $_POST['description'],
                        'created_by' => $_SESSION['user_id']
                    ];
                    
                    if ($productModel->create($data)) {
                        setFlashMessage('success', 'Product created successfully!');
                    } else {
                        setFlashMessage('error', 'Failed to create product. Please try again.');
                    }
                    break;
                    
                case 'update':
                    $imagePath = $_POST['current_product_image'] ?? null;
                    
                    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                        $uploadDir = __DIR__ . '/../uploads/products/';
                        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                        $filename = uniqid() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '', basename($_FILES['product_image']['name']));
                        $targetFile = $uploadDir . $filename;
                        
                        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $targetFile)) {
                            $imagePath = '/uploads/products/' . $filename;
                            
                            // Delete old image if it exists and is not the default image
                            if (!empty($_POST['current_product_image']) && 
                                $_POST['current_product_image'] !== '/assets/images/default-product.png' && 
                                file_exists(__DIR__ . '/..' . $_POST['current_product_image'])) {
                                unlink(__DIR__ . '/..' . $_POST['current_product_image']);
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
                    if ($productModel->delete($_POST['id'], $_SESSION['user_id'])) {
                        setFlashMessage('success', 'Product deleted successfully!');
                    } else {
                        setFlashMessage('error', 'Failed to delete product. Please try again.');
                    }
                    break;
            }
            
            $this->redirect('/user/products');
            return;
        }
        
        // Get search and filter parameters
        $search = $_GET['search'] ?? '';
        $type = $_GET['type'] ?? '';
        $stockStatus = $_GET['stock_status'] ?? '';
        $sortBy = $_GET['sort'] ?? 'id';
        $sortOrder = $_GET['order'] ?? 'DESC';
        
        // Pagination settings
        $limit = 10;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;
        
        // Get user's products with search, filter, pagination, and sorting
        $products = $productModel->getUserProducts(
            $_SESSION['user_id'],
            $limit,
            $offset,
            $search,
            $type,
            $stockStatus,
            $sortBy,
            $sortOrder
        );
        
        $totalProducts = $productModel->getUserProductsCount(
            $_SESSION['user_id'],
            $search,
            $type,
            $stockStatus
        );
        
        $totalPages = ceil($totalProducts / $limit);
        
        $this->view('user/products', [
            'products' => $products,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'pageTitle' => 'My Products'
        ]);
    }

    public function dashboard() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }

        $userId = $_SESSION['user_id'];
        $totalProducts = $this->productModel->getUserProductsCount($userId);
        $totalOrders = $this->orderModel->getUserOrdersCount($userId);
        $lowStockCount = $this->productModel->getLowStockProductsCount($userId);
        $totalRevenue = $this->orderModel->getUserTotalRevenue($userId);
        $productTypeData = $this->productModel->getProductTypeDistribution($userId);
        $monthlySales = $this->orderModel->getMonthlySales($userId, 6);
        $recentOrders = $this->orderModel->getRecentOrders($userId, 5);
        $lowStockProducts = $this->productModel->getLowStockProducts($userId, 5);
        
        require_once 'views/user/dashboard.php';
    }

    public function orders() {
        if (!isLoggedIn()) {
            redirect('login');
        }
        
        $orderModel = new Order();
        $page = $_GET['page'] ?? 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $query = $_GET['search'] ?? null;
        $status = $_GET['status'] ?? null;
        $sortBy = $_GET['sort'] ?? 'order_date';
        $sortOrder = $_GET['order'] ?? 'DESC';

        // Get paginated orders for the seller's products
        $orders = $orderModel->getSellerOrders(
            $_SESSION['user_id'], 
            $limit, 
            $offset, 
            $status ? [$status] : null,
            $sortBy,
            $sortOrder
        );

        // Get total count for pagination
        $totalOrders = $orderModel->getSellerOrdersCount(
            $_SESSION['user_id'],
            $status ? [$status] : null
        );
        $totalPages = ceil($totalOrders / $limit);

        $this->view('user/orders', [
            'orders' => $orders,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'searchQuery' => $query,
            'filterStatus' => $status,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
            'pageTitle' => 'My Product Orders'
        ]);
    }
}
