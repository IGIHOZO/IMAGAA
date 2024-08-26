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
} else {
    $categories = []; // No categories found
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Product</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <style>
        .wrapper { display: flex; flex-direction: column; min-height: 100vh; }
        .content { flex: 1; padding: 20px; }
        .form-container { max-width: 800px; margin: auto; background: #f8f9fa; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); padding: 20px; }
        footer { background: #f1f1f1; padding: 10px; text-align: center; border-top: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Navigation Bar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
            <a class="navbar-brand" href="index.php">E-Commerce</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item"><a class="nav-link" href="register_category.php">Register Category</a></li>
                    <li class="nav-item"><a class="nav-link" href="register_product.php">Register Product</a></li>
                </ul>
                <div id="tags-container" class="mt-3"></div>
            </div>
        </nav>

        <!-- Content -->
        <div class="content">
            <div class="container">
                <div class="form-container">
                    <h2 class="mb-4">Register Product</h2>
                    <form id="productForm" enctype="multipart/form-data" method="POST">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="product_name">Product Name:</label>
                                <input type="text" class="form-control" id="product_name" name="product_name" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="price">Price:</label>
                                <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="product_description">Product Description:</label>
                                <textarea class="form-control" id="product_description" name="product_description"></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="category_id">Category:</label>
                                <select class="form-control select2" id="category_id" name="category_id" required>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo htmlspecialchars($category['category_id']); ?>">
                                            <?php echo htmlspecialchars($category['category_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="stock">Stock:</label>
                                <input type="number" class="form-control" id="stock" name="stock" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="image">Product Image:</label>
                                <input type="file" class="form-control-file" id="image" name="image" accept="image/*">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Register Product</button>
                    </form>
                    <div id="responseMessage" class="mt-3"></div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer>&copy; 2024 E-Commerce. All rights reserved.</footer>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                placeholder: "Select a category",
                allowClear: true,
                width: 'resolve'
            });

            // Form submission handling
            $('#productForm').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission

                var formData = new FormData(this);

                $.ajax({
                    url: 'operations.php', // URL for handling product registration
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#responseMessage').html(response);
                        
                        // Clear the form fields
                        $('#productForm')[0].reset();
                        $('.select2').val(null).trigger('change'); // Reset Select2
                    },
                    error: function() {
                        $('#responseMessage').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
                    }
                });
            });
        });
    </script>
</body>
</html>
