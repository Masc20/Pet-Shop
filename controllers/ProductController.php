<?php
/**
 * Pawfect Pet Shop - Product Controller
 * Handles product listing, details, and management
 */

require_once 'models/Product.php';

class ProductController extends Controller {
    
    public function index() {
        $productModel = new Product();
        $products = $productModel->getAll();
        
        $type = $_GET['type'] ?? 'all';
        if ($type !== 'all') {
            $products = $productModel->getByType($type);
        }
        
        $this->view('products/index', [
            'products' => $products
        ]);
    }
    
    public function show($id) {
        $productModel = new Product();
        $product = $productModel->getById($id);
        
        if (!$product) {
            $this->redirect('/products');
            return;
        }
        
        $this->view('products/show', [
            'product' => $product
        ]);
    }
}
?>
