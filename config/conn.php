<?php
$conn = new mysqli(
    'localhost', // Database host
    'root', // Database username
    '', // Database password
    'petshop_db', // Database name
    3306 // Database port
);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}