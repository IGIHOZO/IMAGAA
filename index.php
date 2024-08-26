<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products List</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .card-img-top {
            height: 200px;
            width: 100%;
            object-fit: cover; 
            border-bottom: 1px solid #ddd;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-body {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 0 0 15px 15px;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
        }

        .card-category {
            font-size: 1rem;
            color: #888;
            margin-bottom: 10px;
        }

        .card-text {
            font-size: 1rem;
            color: #666;
            margin-bottom: 10px;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            border-radius: 20px;
            transition: background-color 0.3s;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .pagination {
            justify-content: center;
        }

        .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
        }
    </style>
</head>
<body>
<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <a class="navbar-brand" href="index.php">Home</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="register_category.php">Register Category</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="register_product.php">Register Product</a>
            </li>
        </ul>
        <form id="image-upload-form" class="form-inline my-2 my-lg-0" enctype="multipart/form-data">
            <!-- Hidden file input -->
            <input type="file" id="image-search-input" name="image" style="display: none;" accept="image/*">

            <!-- Button to trigger the file input -->
            <button class="btn btn-outline-secondary my-2 my-sm-0" type="button" onclick="document.getElementById('image-search-input').click();">
                <img src="images/image.png" alt="Image Search" style="height: 20px; width: 20px;">
            </button>

            <input class="form-control mr-sm-2" type="search" name="search" placeholder="Search products..." aria-label="Search">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>

        <!-- Container for displaying image tags -->
        <div id="tags-container" class="mt-3"></div>
    </div>
</nav>



    <div class="container">
        <div class="row" id="responseDiv">
            <?php
            // Database connection
            // $servername = "localhost";
            // $username = "mpjusdko";
            // $password = "z0HpWFx1%@48";
            // $dbname = "mpjusdko_seveeen_web";

    $servername = "localhost";
    $username = "root";
    $password = "Igihozo!#07";
    $dbname = "test";
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Pagination setup
            $limit = 9;
            $page = isset($_GET['page']) ? $_GET['page'] : 1;
            $offset = ($page - 1) * $limit;

            // Search functionality
            $search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

            // SQL query with search functionality
            $sql = "SELECT p.*, c.category_name FROM products p 
                    JOIN product_categories c ON p.category_id = c.category_id 
                    WHERE p.product_name LIKE '%$search%' 
                    OR p.product_description LIKE '%$search%' 
                    OR c.category_name LIKE '%$search%' 
                    OR c.category_description LIKE '%$search%' 
                    ORDER BY RAND() 
                    LIMIT $limit OFFSET $offset";

            $result = $conn->query($sql);

            if ($result === FALSE) {
                echo '<div class="col-12"><p>Error fetching products: ' . $conn->error . '</p></div>';
            } elseif ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="col-md-4 mb-4">';
                    echo '<div class="card h-100">';
                    echo '<img src="' . htmlspecialchars($row["image_path"]) . '" class="card-img-top" alt="Product Image">';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">' . htmlspecialchars($row["product_name"]) . '</h5>';
                    echo '<p class="card-category">' . htmlspecialchars($row["category_name"]) . '</p>';
                    echo '<p class="card-text">' . htmlspecialchars(substr($row["product_description"], 0, 100)) . '... ';
                    echo '<a href="#" class="more-link" data-full-text="' . htmlspecialchars($row["product_description"]) . '">More</a></p>';
                    echo '<p class="card-text"><strong>Price:</strong> Rwf ' . htmlspecialchars($row["price"]) . '</p>';
                    echo '<p class="card-text"><strong>Stock:</strong> ' . htmlspecialchars($row["stock"]) . '</p>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="col-12"><p>No products found.</p></div>';
            }

            // Pagination links
            $sql = "SELECT COUNT(*) FROM products p 
                    JOIN product_categories c ON p.category_id = c.category_id 
                    WHERE p.product_name LIKE '%$search%' 
                    OR p.product_description LIKE '%$search%' 
                    OR c.category_name LIKE '%$search%' 
                    OR c.category_description LIKE '%$search%'";
            $result = $conn->query($sql);
            if ($result) {
                $totalRows = $result->fetch_row()[0];
                $totalPages = ceil($totalRows / $limit);

                echo '<div class="col-12">';
                echo '<ul class="pagination">';
                for ($i = 1; $i <= $totalPages; $i++) {
                    echo '<li class="page-item' . ($i == $page ? ' active' : '') . '">';
                    echo '<a class="page-link" href="?page=' . $i . '&search=' . urlencode($search) . '">' . $i . '</a>';
                    echo '</li>';
                }
                echo '</ul>';
                echo '</div>';
            } else {
                echo '<div class="col-12"><p>Error fetching pagination: ' . $conn->error . '</p></div>';
            }

            $conn->close();
            ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.more-link').on('click', function(e) {
                e.preventDefault();
                var fullText = $(this).data('full-text');
                $(this).parent().html(fullText);
            });
        });
    </script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#image-upload-form').on('change', function(event) {
        event.preventDefault(); // Prevent the default form submission

        var formData = new FormData(this); // Create FormData object

        $.ajax({
            url: 'image_processor.php', // URL to send the request
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                // Directly display the HTML response
                $('#responseDiv').html(response);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $('#responseDiv').html('<p>Error: ' + textStatus + ' - ' + errorThrown + '</p>');
            }
        });
    });
});
</script>


</body>
</html>
