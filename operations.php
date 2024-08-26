<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// $servername = "localhost";
// $username = "root";
// $password = "Igihozo!#07";
// $dbname = "test";

$servername = "localhost";
$username = "mpjusdko";
$password = "z0HpWFx1%@48";
$dbname = "mpjusdko_seveeen_web";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch categories from the database
$categories = [];
$sql = "SELECT category_id, category_name FROM product_categories";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Initialize variables
    $imagePath = NULL;

    // Handle file upload for product image
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/products/';
        // Create the directory if it does not exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $fileExtension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $newFileName = 'product_' . uniqid() . '.' . $fileExtension;
        $uploadFile = $uploadDir . $newFileName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            $imagePath = $uploadFile;
        } else {
            echo '<div class="alert alert-danger">Product image upload failed.</div>';
            exit();
        }
    }

    // Register Product
    if (isset($_POST['product_name'])) {
        $productName = $_POST['product_name'];
        $productDescription = $_POST['product_description'];
        $categoryId = $_POST['category_id'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];

        $stmt = $conn->prepare("INSERT INTO products (product_name, product_description, category_id, price, stock, image_path) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisds", $productName, $productDescription, $categoryId, $price, $stock, $imagePath);

        if ($stmt->execute()) {
            echo '<div class="alert alert-success">Product registered successfully.</div>';
        } else {
            echo '<div class="alert alert-danger">Error: ' . $stmt->error . '</div>';
        }

        $stmt->close();
    }
}

$conn->close();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Database connection

    // $servername = "localhost";
    // $username = "root";
    // $password = "Igihozo!#07";
    // $dbname = "test";
    
    $servername = "localhost";
    $username = "mpjusdko";
    $password = "z0HpWFx1%@48";
    $dbname = "mpjusdko_seveeen_web";
    
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get form data
    $category_name = $_POST['category_name'];
    $category_description = $_POST['category_description'];
    
    // Handle file upload
    $category_icon = null;
    if (!empty($_FILES['category_icon']['name'])) {
        $target_dir = "uploads/categories/";
        $target_file = $target_dir . basename($_FILES["category_icon"]["name"]);
        if (move_uploaded_file($_FILES["category_icon"]["tmp_name"], $target_file)) {
            $category_icon = basename($_FILES["category_icon"]["name"]);
        } else {
            echo '<div class="alert alert-danger">Failed to upload image.</div>';
            exit();
        }
    }

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO product_categories (category_name, category_description, category_icon) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $category_name, $category_description, $category_icon);

    // Execute
    if ($stmt->execute()) {
        echo '<div class="alert alert-success">Category registered successfully.</div>';
    } else {
        echo '<div class="alert alert-danger">Error: ' . $stmt->error . '</div>';
    }

    // Close connection
    $stmt->close();
    $conn->close();
}
?>