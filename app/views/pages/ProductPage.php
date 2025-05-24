<?php
include_once(__DIR__ . '/../../../config/app.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Shop - Page Title</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .content {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <?php include_once(APP_ROOT . '/views/partials/navBar.php'); ?>

    <div class="container">
        <div class="content">
            <h1>Page Title</h1>
            <p>Your content goes here...</p>
        </div>
    </div>

    <script>
        console.log('Page loaded successfully');
    </script>
</body>

</html>