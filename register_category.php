<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Category</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        .wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .content {
            flex: 1;
            padding: 20px;
        }
        .form-container {
            max-width: 800px;
            margin: auto;
            background: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 20px;
        }
        footer {
            background: #f1f1f1;
            padding: 10px;
            text-align: center;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="wrapper">
<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <a class="navbar-brand" href="index.php">E-Commerce</a>

    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="register_category.php">Register Category</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="register_product.php">Register Product</a>
            </li>
        </ul>


        <!-- Container for displaying image tags -->
        <div id="tags-container" class="mt-3"></div>
    </div>
</nav>

        <!-- Content -->
        <div class="content">
            <div class="container">
                <div class="form-container">
                    <h2 class="mb-4">Register Category</h2>
                    <form id="categoryForm" enctype="multipart/form-data">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="category_name">Category Name:</label>
                                <input type="text" class="form-control" id="category_name" name="category_name" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="category_icon">Category Icon (Image):</label>
                                <input type="file" class="form-control-file" id="category_icon" name="category_icon">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="category_description">Category Description:</label>
                                <textarea class="form-control" id="category_description" name="category_description"></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Register Category</button>
                    </form>
                    <div id="responseMessage" class="mt-3"></div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer>
            &copy; 2024 E-Commerce. All rights reserved.
        </footer>
    </div>

    <script>
        $(document).ready(function() {
            $('#categoryForm').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    url: 'operations.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#responseMessage').html(response);
                        
                        // Clear form fields or reload the page
                        // Option 1: Clear form fields
                        $('#categoryForm')[0].reset();

                        // Option 2: Reload the page
                        // location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", status, error);
                        $('#responseMessage').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
                    }
                });
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