<?php
require_once 'models/User.php';
require_once 'models/Pet.php';
require_once 'models/Product.php';
require_once 'models/Order.php';

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
        
        $this->view('admin/dashboard', [
            'petStats' => $petStats,
            'productStats' => $productStats,
            'orderStats' => $orderStats,
            'users' => $users
        ]);
    }
    
    public function pets() {
        $petModel = new Pet();
        $pets = $petModel->getAll();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'];
            
            if ($action === 'create') {
                $data = [
                    'name' => $_POST['name'],
                    'pet_image' => $_POST['pet_image'],
                    'type' => $_POST['type'],
                    'gender' => $_POST['gender'],
                    'age' => $_POST['age'],
                    'breed' => $_POST['breed']
                ];
                $petModel->create($data);
            } elseif ($action === 'update') {
                $data = [
                    'name' => $_POST['name'],
                    'pet_image' => $_POST['pet_image'],
                    'type' => $_POST['type'],
                    'gender' => $_POST['gender'],
                    'age' => $_POST['age'],
                    'breed' => $_POST['breed']
                ];
                $petModel->update($_POST['id'], $data);
            } elseif ($action === 'delete') {
                $petModel->delete($_POST['id']);
            }
            
            $this->redirect('/admin/pets');
        }
        
        $this->view('admin/pets', [
            'pets' => $pets
        ]);
    }
    
    public function products() {
        $productModel = new Product();
        $products = $productModel->getAll();
        $archivedProducts = $productModel->getArchived();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'];
            
            if ($action === 'create') {
                $data = [
                    'name' => $_POST['name'],
                    'product_image' => $_POST['product_image'],
                    'stock_quantity' => $_POST['stock_quantity'],
                    'type' => $_POST['type'],
                    'price' => $_POST['price'],
                    'description' => $_POST['description']
                ];
                $productModel->create($data);
            } elseif ($action === 'update') {
                $data = [
                    'name' => $_POST['name'],
                    'product_image' => $_POST['product_image'],
                    'stock_quantity' => $_POST['stock_quantity'],
                    'type' => $_POST['type'],
                    'price' => $_POST['price'],
                    'description' => $_POST['description']
                ];
                $productModel->update($_POST['id'], $data);
            } elseif ($action === 'archive') {
                $productModel->archive($_POST['id']);
            } elseif ($action === 'restore') {
                $productModel->restore($_POST['id']);
            }
            
            $this->redirect('/admin/products');
        }
        
        $this->view('admin/products', [
            'products' => $products,
            'archivedProducts' => $archivedProducts
        ]);
    }
    
    public function orders() {
        $orderModel = new Order();
        $orders = $orderModel->getAll();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderModel->updateStatusByPdo($_POST['order_id'], $_POST['status']);
            $this->redirect('/admin/orders');
        }
        
        $this->view('admin/orders', [
            'orders' => $orders
        ]);
    }
    
    public function users() {
        $userModel = new User();
        $users = $userModel->getAll();
        
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
            
            $this->redirect('/admin/users');
        }
        
        $this->view('admin/users', [
            'users' => $users
        ]);
    }
    
    public function settings() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            setSetting('site_logo', $_POST['site_logo']);
            setSetting('primary_color', $_POST['primary_color']);
            setSetting('secondary_color', $_POST['secondary_color']);
            
            $_SESSION['success'] = 'Settings updated successfully!';
            $this->redirect('/admin/settings');
        }
        
        $this->view('admin/settings');
    }
}
?>
