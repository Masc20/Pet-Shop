<?php
/**
 * Pawfect Pet Shop - Cart Controller
 * Handles shopping cart functionality
 */

require_once 'models/Cart.php';
require_once 'models/Order.php';
require_once 'models/Product.php';

class CartController extends Controller {
    
    public function index() {
        if (!isLoggedIn()) {
            $this->redirect('/login');
            return;
        }
        
        $cartModel = new Cart();
        $items = $cartModel->getItems($_SESSION['user_id']);
        $total = $cartModel->getTotal($_SESSION['user_id']);
        
        $this->view('cart/index', [
            'items' => $items,
            'total' => $total
        ]);
    }
    
    public function add() {
        if (!isLoggedIn()) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Please login first']);
                return;
            }
            $this->redirect('/login');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = $_POST['product_id'];
            $quantity = $_POST['quantity'] ?? 1;
            
            $cartModel = new Cart();
            if ($cartModel->addItem($_SESSION['user_id'], $productId, $quantity)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Item added to cart']);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Failed to add item']);
            }
        }
    }
    
    public function update() {
        if (!isLoggedIn()) {
            $this->redirect('/login');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = $_POST['product_id'];
            $quantity = $_POST['quantity'];
            
            $cartModel = new Cart();
            $cartModel->updateQuantity($_SESSION['user_id'], $productId, $quantity);
            $this->redirect('/cart');
        }
    }
    
    public function remove() {
        if (!isLoggedIn()) {
            $this->redirect('/login');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = $_POST['product_id'];
            
            $cartModel = new Cart();
            $cartModel->removeItem($_SESSION['user_id'], $productId);
            $this->redirect('/cart');
        }
    }
    
    public function checkout() {
        if (!isLoggedIn()) {
            $this->redirect('/login');
            return;
        }
        
        $cartModel = new Cart();
        $orderModel = new Order();
        $productModel = new Product();
        
        $items = $cartModel->getItems($_SESSION['user_id']);
        $total = $cartModel->getTotal($_SESSION['user_id']);
        
        if (empty($items)) {
            $this->redirect('/cart');
            return;
        }
        
        // Create order
        $orderId = $orderModel->createOrder($_SESSION['user_id'], $total);
        
        // Add order items and update stock
        foreach ($items as $item) {
            $orderModel->addItem($orderId, $item['product_id'], $item['quantity'], $item['price']);
            $productModel->updateStock($item['product_id'], $item['quantity']);
        }
        
        // Clear cart
        $cartModel->clearCart($_SESSION['user_id']);
        
        $_SESSION['success'] = 'Order placed successfully!';
        $this->redirect('/profile');
    }
    
    public function count() {
        if (!isLoggedIn()) {
            header('Content-Type: application/json');
            echo json_encode(['count' => 0]);
            return;
        }
        
        $cartModel = new Cart();
        $count = $cartModel->getItemCount($_SESSION['user_id']);
        
        header('Content-Type: application/json');
        echo json_encode(['count' => $count]);
    }
}
?>
