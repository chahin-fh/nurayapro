<?php
include "../cnx.php";
$cat_res = mysqli_query($cnx, "SELECT * FROM categories");

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = mysqli_real_escape_string($cnx, $_POST['title']);
    $description = mysqli_real_escape_string($cnx, $_POST['description']);
    $price = (float)$_POST['price'];
    $quantity = (int)$_POST['quantity'];
    $category = (int)$_POST['category'];

    $upload_dir = "uploaded/";
    $img_name = basename($_FILES["pro_images"]["name"]);
    $img_name = preg_replace("/[^A-Za-z0-9.\-_]/", '', $img_name); // Nettoyer le nom
    $pro_images = $upload_dir . time() . "_" . $img_name; // Renommer avec timestamp

    if (move_uploaded_file($_FILES["pro_images"]["tmp_name"], $pro_images)) {
        $d = date("Y-m-d H:i:s");

        $stmt = mysqli_prepare($cnx, "INSERT INTO products (name, description, price, stock_quantity, category_id, image_url, created_at, updated_at) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'ssdiisss', $title, $description, $price, $quantity, $category, $pro_images, $d, $d);
        $result_of_ins = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if ($result_of_ins) {
            echo "<script>alert('Product successfully added.');</script>";
        } else {
            echo "<script>alert('Database insertion error.');</script>";
        }
    } else {
        echo "<script>alert('File upload failed.');</script>";
    }
}
?>
<!DOC````TYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Product Upload</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <style>
    /* Center the form container and style it for readability */
    .form-container {
      max-width: 820px;
      width: 100%;
      margin: 48px auto; /* center horizontally and add top/bottom space */
      background: #fff;
      padding: 26px 28px;
      border-radius: 10px;
      box-shadow: 0 6px 22px rgba(16,24,40,0.08);
      box-sizing: border-box;
    }

    /* Keep the admin sidebar visible on larger screens; adapt the form spacing */
    @media (min-width: 1000px) {
      .form-container { margin-left: 260px; max-width: 760px; }
    }

    /* Make form full-width on small screens */
    @media (max-width: 768px) {
      .form-container { margin: 22px 18px; padding: 18px; border-radius: 8px; }
    }

    .form-container h2 { margin-top: 0; margin-bottom: 18px; font-size: 20px; color: #111827; }
    .form-group { margin-bottom: 16px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #374151; }
    input[type="text"], input[type="number"], textarea, select { width: 100%; padding: 10px 12px; border: 1px solid #e5e7eb; border-radius: 8px; }
    .btn { background:#111827;color:#fff;padding:10px 18px;border-radius:8px;border:none;cursor:pointer }
  </style>
  <script src="main.js"></script>
</head>
<body>
  <?php include '../adminsidebar.php'; ?>

  <div class="form-container">
    <h2><i class="fas fa-plus-circle"></i> Create New Listing</h2>
    <form id="productForm" method="post" enctype="multipart/form-data">
      <div class="form-group">
        <label><i class="fas fa-images"></i> Photos <span class="required">*</span></label>
        <div class="photo-upload" id="photoUploadArea">
          <i class="fas fa-camera"></i>
          <p>Drag photos here or click to upload</p>
          <p class="upload-hint">Supported formats: JPG, PNG, GIF (Max 5MB each)</p>
          <input type="file" id="photos" name="pro_images" accept="image/*" required style="display: none;">
        </div>
      </div>

      <div class="form-group">
        <label for="title">
          <i class="fas fa-heading"></i> Title
          <span class="required">*</span>
        </label>
        <input type="text" id="title" name="title" placeholder="What are you selling?" required minlength="3">
        <div class="input-hint">Minimum 3 characters</div>
      </div>

      <div class="form-group">
        <label for="price">
          <i class="fas fa-tag"></i> Price
          <span class="required">*</span>
        </label>
        <div class="price-container">
          <input type="number" id="price" name="price" placeholder="0.00" min="0.01" step="0.01" required>
        </div>
        <div class="input-hint">Enter a price greater than 0</div>
      </div>

      <div class="form-group">
        <label for="quantity">
          <i class="fas fa-box"></i> Quantity
          <span class="required">*</span>
        </label>
        <div class="quantity-container">
          <input type="number" id="quantity" name="quantity" placeholder="0" min="1" required>
        </div>
        <div class="input-hint">Enter the stock quantity</div>
      </div>

      <div class="form-group">
        <label for="category">
          <i class="fas fa-tags"></i> Category
          <span class="required">*</span>
        </label>
        <select name="category" id="category" required>
          <option value="" disabled selected>Select a category</option>
          <?php 
          $cat_res = mysqli_query($cnx, "SELECT * FROM categories");
          while($t = mysqli_fetch_assoc($cat_res)) : ?>
            <option value="<?php echo $t['category_id']; ?>"><?php echo htmlspecialchars($t['name']); ?></option>
          <?php endwhile; ?>
        </select>
        <div class="input-hint">Select a category for your product or <a href="../manage-categories" style="color: #667eea; text-decoration: none; font-weight: 600;">manage categories</a></div>
      </div>

      <div class="form-group">
        <label for="description">
          <i class="fas fa-align-left"></i> Description
          <span class="required">*</span>
        </label>
        <textarea id="description" name="description" placeholder="Include details like size, brand, color, condition, etc." required minlength="10"></textarea>
        <div class="input-hint">Minimum 10 characters</div>
      </div>

      <button type="submit" class="btn">
        <i class="fas fa-paper-plane"></i> Publish Listing
      </button>
    </form>
  </div>
</body>
</html>
<?php mysqli_close($cnx);?>