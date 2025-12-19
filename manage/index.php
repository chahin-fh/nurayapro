<?php
include '../cnx.php';
$result_of_cards = mysqli_query($cnx, "SELECT * FROM products");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="style.css" />
  <title>Manage Products</title>
</head>
<body>
  <?php include '../adminsidebar.php'; ?>

  <div class="product-list">
    <?php while($t = mysqli_fetch_assoc($result_of_cards)) : ?>
    <div class="product-card-horizontal">
      <div class="product-image">
        <img src="<?php echo $t['image_url']; ?>" alt="<?php echo htmlspecialchars($t['name']); ?>" />
      </div>
      <div class="product-details">
        <div class="product-title"><?php echo htmlspecialchars($t['name']); ?></div>
        <div class="product-description"><?php echo htmlspecialchars($t['description']); ?></div>
        <div class="price-section">
          <span class="current-price"><?php echo $t['price']; ?> DT</span>
        </div>
        <div class="action-buttons">
          <a href="main.php?id=<?php echo $t['product_id']; ?>&dl=1">
            <button name="delb" class="add-to-cart">Supprimer</button>
          </a>
          <a href="main.php?id=<?php echo $t['product_id']; ?>&dl=0">
            <button name="edb" class="quote-btn">Modifier</button>
          </a>
        </div>
      </div>
    </div>
    <br><br>
    <?php endwhile; ?>
  </div>
  <?php mysqli_close($cnx); ?>
</body>
</html>
