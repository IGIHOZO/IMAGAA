<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
    $tmp_name = $_FILES['image']['tmp_name'];

    $api_credentials = array(
        'key' => 'acc_389d46b2f1e1cfc',
        'secret' => '0985ca43333a2b9460967abb75e31eda'
    );

    $ch = curl_init();

    // Prepare the file for upload
    $cfile = new CURLFile($tmp_name, $_FILES['image']['type'], $_FILES['image']['name']);

    curl_setopt($ch, CURLOPT_URL, 'https://api.imagga.com/v2/tags');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, TRUE); // Include headers in output
    curl_setopt($ch, CURLOPT_USERPWD, $api_credentials['key'] . ':' . $api_credentials['secret']);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array('image' => $cfile));

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $header_size);
    $body = substr($response, $header_size);
    curl_close($ch);

    if ($http_code == 200) {
        $json_response = json_decode($body, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            if (isset($json_response['result']) && isset($json_response['result']['tags'])) {
                $tags = $json_response['result']['tags'];

                // Filter tags with confidence of 50 or higher
                $filtered_tags = array_filter($tags, function($tag) {
                    return $tag['confidence'] >= 50;
                });

                // Format tags
                $formatted_tags = array_map(function($tag) {
                    return array(
                        "tag" => $tag['tag']['en'],
                        "confidence" => $tag['confidence']
                    );
                }, $filtered_tags);

                // Sort tags by confidence value in descending order
                usort($formatted_tags, function($a, $b) {
                    return $b['confidence'] <=> $a['confidence'];
                });

                // Extract all tags
                $tag_list = array_column($formatted_tags, 'tag');

                if (empty($tag_list)) {
                    echo '<div class="col-12"><p>No Matching found.</p></div>';
                    exit;
                }

                // Connect to database

                // $servername = "localhost";
                // $username = "root";
                // $password = "Igihozo!#07";
                // $dbname = "test";

                $servername = "localhost";
                $username = "mpjusdko";
                $password = "z0HpWFx1%@48";
                $dbname = "mpjusdko_seveeen_web";


                $conn = new mysqli($servername, $username, $password, $dbname);

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Build SQL query with all tags
                $searchConditions = array();
                foreach ($tag_list as $tag) {
                    $escapedTag = $conn->real_escape_string($tag); // Escape special characters
                    $searchConditions[] = "p.product_name LIKE '%$escapedTag%'";
                    $searchConditions[] = "p.product_description LIKE '%$escapedTag%'";
                    $searchConditions[] = "c.category_name LIKE '%$escapedTag%'";
                    $searchConditions[] = "c.category_description LIKE '%$escapedTag%'";
                }
                $searchSql = implode(' OR ', $searchConditions);

                if (empty($searchSql)) {
                    echo '<div class="col-12"><p>No valid search conditions generated.</p></div>';
                    exit;
                }

                $sql = "SELECT p.*, c.category_name FROM products p 
                        JOIN product_categories c ON p.category_id = c.category_id 
                        WHERE $searchSql";

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
                $conn->close();
            } else {
                echo "No tags found in the response.";
            }
        } else {
            echo "Error parsing JSON response.";
        }
    } else {
        echo "Error: API request failed with HTTP status code " . $http_code;
        echo "<pre>$body</pre>"; // Print response body for debugging
    }
} else {
    echo "No file uploaded or there was an error.";
}
?>
