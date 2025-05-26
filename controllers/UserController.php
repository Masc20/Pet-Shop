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
        
        // Fetch user's delivery addresses
        global $pdo;
        $stmt = $pdo->prepare('SELECT * FROM delivery_addresses WHERE user_id = ? ORDER BY id LIMIT 1');
        $stmt->execute([$_SESSION['user_id']]);
        $deliveryAddress = $stmt->fetch() ?: []; // Fetch one address, default to empty array if none found
        
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
                }
            }
            
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
            'orders' => $orders,
            'delivery_address' => $deliveryAddress
        ]);
    }

    public function updateAddress() {
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
                $stmt->execute([$city, $barangay, $street, $zipcode, $userId]);
            } else {
                // Insert
                $stmt = $pdo->prepare('INSERT INTO delivery_addresses (user_id, city, barangay, street, zipcode) VALUES (?, ?, ?, ?, ?)');
                $stmt->execute([$userId, $city, $barangay, $street, $zipcode]);
            }
        }
        $this->redirect('/profile');
    }
}
?>
