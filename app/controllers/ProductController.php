<?php

namespace App\Controllers;

class ProductController
{
    public function showProductPage()
    {
        // Include the ProductPage view
        require_once '../app/views/pages/ProductPage.php';
    }


    public function showProducts()
    {
        // Include the database connection
        include_once('../app/config/conn.php');

        // Fetch products from the database
        $sql = "SELECT * FROM products";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $products = $result->fetch_all(MYSQLI_ASSOC);
            return $products;
        } else {
            return [];
        }
    }
}