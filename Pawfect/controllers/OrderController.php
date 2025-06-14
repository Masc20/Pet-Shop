<?php
require_once 'models/Order.php';
require_once 'models/Product.php';
require_once 'models/User.php';

class OrderController extends Controller
{
    private $orderModel;
    private $productModel;

    public function __construct()
    {
        $this->orderModel = new Order();
        $this->productModel = new Product();

        // Ensure user is logged in for all order operations
        if (!isLoggedIn()) {
            setFlashMessage('error', 'Please login to access orders.');
            redirect('login');
        }
    }

    public function index()
    {
        $userId = $_SESSION['user_id'];
        $page = $_GET['page'] ?? 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $orders = $this->orderModel->getUserOrders($userId, $limit, $offset);
        $totalOrders = $this->orderModel->getUserOrdersCount($userId);
        $totalPages = ceil($totalOrders / $limit);

        $this->view('orders.index', [
            'orders' => $orders,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }

    public function show($orderId)
    {
        $userId = $_SESSION['user_id'];
        $order = $this->orderModel->getOrderWithItems($orderId, $userId);

        if (!$order) {
            setFlashMessage('error', 'Order not found.');
            redirect('orders');
        }

        $this->view('orders.show', ['order' => $order]);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('cart');
        }

        $userId = $_SESSION['user_id'];
        $shippingData = [
            'shipping_address' => $_POST['shipping_address'] ?? '',
            'shipping_city' => $_POST['shipping_city'] ?? '',
            'shipping_barangay' => $_POST['shipping_barangay'] ?? '',
            'shipping_zip' => $_POST['shipping_zip'] ?? '',
            'payment_method' => $_POST['payment_method'] ?? '',
            'notes' => $_POST['notes'] ?? ''
        ];

        // Validate required fields
        $requiredFields = ['shipping_address', 'shipping_city', 'shipping_barangay', 'shipping_zip', 'payment_method'];
        foreach ($requiredFields as $field) {
            if (empty($shippingData[$field])) {
                setFlashMessage('error', "Please provide {$field}.");
                redirect('cart/checkout');
            }
        }

        $orderId = $this->orderModel->createFromCart($userId, $shippingData);

        if (!$orderId) {
            setFlashMessage('error', 'Failed to create order. Please try again.');
            redirect('cart/checkout');
        }

        setFlashMessage('success', 'Order created successfully.');
        redirect('orders/show/' . $orderId);
    }

    public function cancel($orderId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/user/orders');
            return;
        }

        $userId = $_SESSION['user_id'];
        
        // Check if the order belongs to the user and can be cancelled
        if (!$this->orderModel->canBeCancelled($orderId, $userId)) {
            $_SESSION['error'] = 'This order cannot be cancelled';
        } else {
            // Attempt to cancel the order
            if ($this->orderModel->cancelOrder($orderId, $userId)) {
                $_SESSION['success'] = 'Order cancelled successfully';
            } else {
                $_SESSION['error'] = 'Failed to cancel order';
            }
        }
        
        redirect('/user/orders');
    }

    public function updateStatus($orderId, $status)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/user/orders');
            return;
        }

        $userId = $_SESSION['user_id'];
        
        // Validate status
        $validStatuses = ['processing', 'shipped', 'delivered'];
        if (!in_array($status, $validStatuses)) {
            $_SESSION['error'] = 'Invalid status';
            redirect('/user/orders');
            return;
        }

        // Update order status
        if ($this->orderModel->updateStatus($orderId, $status)) {
            $_SESSION['success'] = 'Order status updated successfully';
        } else {
            $_SESSION['error'] = 'Failed to update order status';
        }
        
        redirect('/user/orders');
    }

    public function adminIndex()
    {
        if (!isAdmin()) {
            setFlashMessage('error', 'You do not have permission to access this page.');
            redirect('orders');
        }

        $page = $_GET['page'] ?? 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Get filter parameters
        $query = $_GET['search'] ?? null;
        $status = $_GET['status'] ?? null;
        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;

        $orders = $this->orderModel->getAdminPaginated($limit, $offset, $query, $status, $startDate, $endDate);
        $totalOrders = $this->orderModel->getAdminTotalCount($query, $status, $startDate, $endDate);
        $totalPages = ceil($totalOrders / $limit);

        $this->view('admin/orders/index', [
            'orders' => $orders,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'filters' => [
                'search' => $query,
                'status' => $status,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ]);
    }

    public function statistics()
    {
        if (!isAdmin()) {
            setFlashMessage('error', 'You do not have permission to access this page.');
            redirect('orders');
        }

        $stats = $this->orderModel->getStats();
        $recentOrders = $this->orderModel->getRecentOrders(5);
        $salesData = $this->orderModel->getSalesData();
        $topCustomers = $this->orderModel->getTopCustomers(5);

        $this->view('admin/orders/statistics', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'salesData' => $salesData,
            'topCustomers' => $topCustomers
        ]);
    }
}
