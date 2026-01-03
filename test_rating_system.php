<!DOCTYPE html>
<html>
<head>
    <title>Rating Test</title>
</head>
<body>
    <h1>Testing Product Rating System</h1>
    
    <h2>Test 1: Check PHP Rating Calculation</h2>
    <?php
    include 'config/database.php';
    
    $product_id = 2;
    
    // Same query as in product.php
    $avg_rating_query = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews 
                         FROM reviews 
                         WHERE product_id = $product_id AND is_approved = 1";
    $avg_result = mysqli_query($cnx, $avg_rating_query);
    $rating_data = mysqli_fetch_assoc($avg_result);
    $avg_rating = $rating_data['avg_rating'] ? round($rating_data['avg_rating'], 1) : 0;
    $total_reviews = $rating_data['total_reviews'] ?? 0;
    
    echo "<p><strong>Average Rating:</strong> $avg_rating/5</p>";
    echo "<p><strong>Total Reviews:</strong> $total_reviews</p>";
    
    // Display stars
    echo "<div class='stars' style='font-size: 24px; color: #ffc107;'>";
    for ($i = 1; $i <= 5; $i++) {
        $class = $i <= $avg_rating ? 'filled' : '';
        $color = $i <= $avg_rating ? '#ffc107' : '#ddd';
        echo "<span style='color: $color;'>★</span>";
    }
    echo "</div>";
    ?>
    
    <h2>Test 2: API Response</h2>
    <div id="api-test">Loading...</div>
    
    <h2>Test 3: Reviews Loading</h2>
    <div id="reviews-test"></div>
    
    <script>
        // Test API call
        fetch('api/reviews.php?action=get&product_id=2&page=1')
            .then(response => response.json())
            .then(data => {
                const apiDiv = document.getElementById('api-test');
                if (data.success) {
                    apiDiv.innerHTML = `
                        <p style="color: green;">✓ API Working!</p>
                        <p>Average Rating: ${data.stats.avg_rating}/5</p>
                        <p>Total Reviews: ${data.stats.total_reviews}</p>
                        <p>Reviews Returned: ${data.reviews.length}</p>
                    `;
                    
                    // Display reviews
                    const reviewsDiv = document.getElementById('reviews-test');
                    if (data.reviews.length > 0) {
                        let html = '<ul>';
                        data.reviews.forEach(review => {
                            html += `<li><strong>${review.author}</strong> - ${review.rating}/5<br>${review.comment}</li>`;
                        });
                        html += '</ul>';
                        reviewsDiv.innerHTML = html;
                    } else {
                        reviewsDiv.innerHTML = '<p>No reviews found.</p>';
                    }
                } else {
                    apiDiv.innerHTML = `<p style="color: red;">✗ API Error: ${data.message}</p>`;
                }
            })
            .catch(error => {
                const apiDiv = document.getElementById('api-test');
                apiDiv.innerHTML = `<p style="color: red;">✗ Fetch Error: ${error.message}</p>`;
            });
    </script>

    <h2>Test 4: Check showTab Function</h2>
    <button onclick="testShowTab()">Test showTab Function</button>
    <div id="tab-test"></div>
    
    <script>
        function testShowTab() {
            const testDiv = document.getElementById('tab-test');
            if (typeof showTab === 'function') {
                testDiv.innerHTML = '<p style="color: green;">✓ showTab function exists</p>';
            } else {
                testDiv.innerHTML = '<p style="color: red;">✗ showTab function NOT defined</p>';
            }
        }
    </script>
</body>
</html>
