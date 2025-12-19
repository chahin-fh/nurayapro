<?php
include "../cnx.php";

// Handle category creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_category'])) {
    $cat_name = mysqli_real_escape_string($cnx, $_POST['cat_name']);
    $cat_description = mysqli_real_escape_string($cnx, $_POST['cat_description']);
    
    if (!empty($cat_name)) {
        $stmt = mysqli_prepare($cnx, "INSERT INTO categories (name, description) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, 'ss', $cat_name, $cat_description);
        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "Category added successfully!";
        } else {
            $error_msg = "Error adding category.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $error_msg = "Category name cannot be empty.";
    }
}

// Handle category deletion
if (isset($_GET['delete'])) {
    $cat_id = (int)$_GET['delete'];
    $stmt = mysqli_prepare($cnx, "DELETE FROM categories WHERE category_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $cat_id);
    if (mysqli_stmt_execute($stmt)) {
        $success_msg = "Category deleted successfully!";
    } else {
        $error_msg = "Error deleting category.";
    }
    mysqli_stmt_close($stmt);
}

// Fetch all categories
$cat_res = mysqli_query($cnx, "SELECT * FROM categories ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manage Categories</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <style>
    body {
      background-color: #f5f7fa;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
    }

    .container {
      max-width: 1000px;
      margin: 0 auto;
      padding: 30px 20px;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
      background: white;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .header h1 {
      margin: 0;
      color: #333;
      font-size: 28px;
    }

    .btn-add-category {
      padding: 12px 25px;
      background-color: #667eea;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 14px;
      font-weight: 600;
      transition: all 0.3s;
    }

    .btn-add-category:hover {
      background-color: #5568d3;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .alert {
      padding: 15px 20px;
      border-radius: 6px;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .alert-success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    .alert-error {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }

    .form-card {
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      margin-bottom: 30px;
      display: none;
    }

    .form-card.show {
      display: block;
    }

    .form-card h2 {
      margin-top: 0;
      color: #333;
      font-size: 22px;
      margin-bottom: 20px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #333;
    }

    .form-group input,
    .form-group textarea {
      width: 100%;
      padding: 12px;
      border: 1px solid #ddd;
      border-radius: 6px;
      font-family: inherit;
      font-size: 14px;
      box-sizing: border-box;
    }

    .form-group input:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: #667eea;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-buttons {
      display: flex;
      gap: 10px;
      margin-top: 25px;
    }

    .form-buttons button {
      padding: 12px 25px;
      border: none;
      border-radius: 6px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
    }

    .btn-submit {
      background-color: #667eea;
      color: white;
      flex: 1;
    }

    .btn-submit:hover {
      background-color: #5568d3;
    }

    .btn-cancel {
      background-color: #e0e0e0;
      color: #333;
      flex: 1;
    }

    .btn-cancel:hover {
      background-color: #d0d0d0;
    }

    .categories-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 20px;
    }

    .category-card {
      background: white;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      transition: all 0.3s;
      border-left: 4px solid #667eea;
    }

    .category-card:hover {
      box-shadow: 0 4px 16px rgba(0,0,0,0.15);
      transform: translateY(-4px);
    }

    .category-card h3 {
      margin: 0 0 10px 0;
      color: #333;
      font-size: 18px;
    }

    .category-card p {
      margin: 10px 0;
      color: #666;
      font-size: 14px;
      line-height: 1.5;
    }

    .category-card .date {
      color: #999;
      font-size: 12px;
      margin-top: 15px;
    }

    .category-card .actions {
      display: flex;
      gap: 10px;
      margin-top: 15px;
    }

    .btn-small {
      padding: 8px 12px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 12px;
      font-weight: 600;
      transition: all 0.3s;
    }

    .btn-delete {
      background-color: #ff6b6b;
      color: white;
      flex: 1;
    }

    .btn-delete:hover {
      background-color: #ff5252;
    }

    .empty-state {
      text-align: center;
      padding: 40px;
      color: #999;
    }

    .empty-state i {
      font-size: 48px;
      margin-bottom: 15px;
      opacity: 0.5;
    }

    @media (max-width: 768px) {
      .header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
      }

      .categories-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <?php include '../adminsidebar.php'; ?>

  <div class="container">
    <div class="header">
      <h1><i class="fas fa-tags"></i> Manage Categories</h1>
      <button class="btn-add-category" onclick="toggleForm()">
        <i class="fas fa-plus"></i> Add New Category
      </button>
    </div>

    <?php if (isset($success_msg)): ?>
      <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <span><?php echo $success_msg; ?></span>
      </div>
    <?php endif; ?>

    <?php if (isset($error_msg)): ?>
      <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <span><?php echo $error_msg; ?></span>
      </div>
    <?php endif; ?>

    <!-- Add Category Form -->
    <div class="form-card" id="addCategoryForm">
      <h2><i class="fas fa-plus-circle"></i> Add New Category</h2>
      <form method="post">
        <div class="form-group">
          <label for="cat_name">Category Name <span style="color: #ff6b6b;">*</span></label>
          <input type="text" id="cat_name" name="cat_name" placeholder="Enter category name (e.g., Electronics, Clothing)" required minlength="3" maxlength="50">
        </div>
        <div class="form-group">
          <label for="cat_description">Description</label>
          <textarea id="cat_description" name="cat_description" placeholder="Enter category description (optional)" rows="4" maxlength="500"></textarea>
        </div>
        <div class="form-buttons">
          <button type="submit" name="add_category" value="1" class="form-buttons btn-submit">
            <i class="fas fa-check"></i> Add Category
          </button>
          <button type="button" class="form-buttons btn-cancel" onclick="toggleForm()">
            <i class="fas fa-times"></i> Cancel
          </button>
        </div>
      </form>
    </div>

    <!-- Categories List -->
    <h2 style="color: #333; margin-top: 30px;">
      <i class="fas fa-list"></i> All Categories
    </h2>

    <?php if (mysqli_num_rows($cat_res) > 0): ?>
      <div class="categories-grid">
        <?php while($category = mysqli_fetch_assoc($cat_res)): ?>
          <div class="category-card">
            <h3><?php echo htmlspecialchars($category['name']); ?></h3>
            <p><?php echo htmlspecialchars($category['description'] ?? 'No description'); ?></p>
            <div class="date">
              <i class="fas fa-calendar-alt"></i>
              <?php echo date('M d, Y', strtotime($category['created_at'])); ?>
            </div>
            <div class="actions">
              <a href="?delete=<?php echo $category['category_id']; ?>" class="btn-small btn-delete" onclick="return confirm('Are you sure you want to delete this category?');">
                <i class="fas fa-trash"></i> Delete
              </a>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <div class="empty-state">
        <i class="fas fa-inbox"></i>
        <p>No categories found. Create your first category!</p>
      </div>
    <?php endif; ?>
  </div>

  <script>
    function toggleForm() {
      const form = document.getElementById('addCategoryForm');
      form.classList.toggle('show');
      if (form.classList.contains('show')) {
        document.getElementById('cat_name').focus();
      }
    }
  </script>
</body>
</html>
<?php mysqli_close($cnx); ?>
