<?php
require_once 'models/User.php';
require_once 'models/Order.php';

class UserController extends Controller {
    public function profile() {
        if (!isLoggedIn()) {
            $this->redirect('/login');
            return;
        }
        
        $userModel = new User();
        $orderModel = new Order();
        
        $user = $userModel->getById($_SESSION['user_id']);
        $orders = $orderModel->getByUser($_SESSION['user_id']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone'],
                'address' => $_POST['address']
            ];
            
            if ($userModel->update($_SESSION['user_id'], $data)) {
                $_SESSION['success'] = 'Profile updated successfully!';
                $_SESSION['user_name'] = $data['first_name'] . ' ' . $data['last_name'];
            } else {
                $_SESSION['error'] = 'Failed to update profile';
            }
            $this->redirect('/profile');
        }
        
        $this->view('user/profile', [
            'user' => $user,
            'orders' => $orders
        ]);
    }
}
?>
