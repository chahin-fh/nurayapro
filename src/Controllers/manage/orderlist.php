<?php
include '../cnx.php';
$result = mysqli_query($cnx , "SELECT * from orders");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Order List</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      padding: 40px 20px;
      margin-left: 0;
    }

    .container {
      max-width: 1000px;
      margin: 0 auto;
    }

    .header {
      text-align: center;
      margin-bottom: 40px;
      color: white;
    }

    .header h1 {
      font-size: 2.5em;
      margin-bottom: 10px;
      text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }

    .header p {
      font-size: 1.1em;
      opacity: 0.9;
    }

    .orders-grid {
      display: grid;
      gap: 30px;
    }

    .order-card {
      background: white;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 10px 40px rgba(0,0,0,0.15);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      border: 1px solid rgba(255,255,255,0.2);
    }

    .order-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 20px 60px rgba(0,0,0,0.25);
    }

    .order-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 20px 25px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .order-id {
      font-size: 0.9em;
      opacity: 0.9;
    }

    .order-id strong {
      font-size: 1.3em;
      display: block;
    }

    .order-status {
      background: rgba(255,255,255,0.25);
      padding: 8px 16px;
      border-radius: 20px;
      font-size: 0.85em;
      font-weight: 600;
    }

    .order-content {
      padding: 25px;
    }

    .client-section,
    .items-section {
      margin-bottom: 25px;
    }

    .section-title {
      font-size: 1.2em;
      font-weight: 700;
      color: #333;
      margin-bottom: 15px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .section-title i {
      color: #667eea;
      font-size: 1.3em;
    }

    .client-info {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
    }

    .info-item {
      background: #f8f9fa;
      padding: 12px 15px;
      border-radius: 10px;
      border-left: 4px solid #667eea;
    }

    .info-label {
      font-size: 0.85em;
      color: #666;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 5px;
    }

    .info-value {
      font-size: 1em;
      color: #333;
      font-weight: 500;
    }

    .items-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 15px;
    }

    .product-item {
      background: #f8f9fa;
      border-radius: 12px;
      overflow: hidden;
      border: 1px solid #e0e0e0;
      transition: all 0.3s ease;
    }

    .product-item:hover {
      border-color: #667eea;
      box-shadow: 0 8px 20px rgba(102, 126, 234, 0.15);
      transform: translateY(-3px);
    }

    .product-image {
      width: 100%;
      height: 180px;
      object-fit: cover;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .product-info {
      padding: 12px;
    }

    .product-name {
      font-weight: 600;
      color: #333;
      font-size: 0.95em;
      margin-bottom: 8px;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      line-clamp: 2;
      overflow: hidden;
    }

    .product-details {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .product-price {
      color: #667eea;
      font-weight: 700;
      font-size: 1em;
    }

    .product-qty {
      background: #667eea;
      color: white;
      padding: 4px 10px;
      border-radius: 6px;
      font-size: 0.85em;
      font-weight: 600;
    }

    .order-footer {
      border-top: 1px solid #e0e0e0;
      padding: 20px 25px;
      display: flex;
      justify-content: flex-end;
      gap: 10px;
    }

    .btn {
      padding: 12px 24px;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 0.95em;
    }

    .btn-primary {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
    }

    .btn-secondary {
      background: #e74c3c;
      color: white;
      box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
    }

    .btn-secondary:hover {
      background: #c0392b;
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(231, 76, 60, 0.5);
    }

    .empty-state {
      text-align: center;
      padding: 60px 20px;
      color: white;
    }

    .empty-state i {
      font-size: 4em;
      margin-bottom: 20px;
      opacity: 0.8;
    }

    .empty-state p {
      font-size: 1.2em;
    }

    @media (max-width: 768px) {
      .header h1 {
        font-size: 2em;
      }

      .order-header {
        flex-direction: column;
        gap: 10px;
        text-align: center;
      }

      .items-grid {
        grid-template-columns: 1fr;
      }

      .order-footer {
        flex-direction: column;
      }

      .btn {
        justify-content: center;
      }
    }
  </style>
</head>
<body>
<?php include '../adminsidebar.php';?>
  
  <div class="container">
    <div class="header">
      <h1><i class="fas fa-shopping-bag"></i> Order Management</h1>
      <p>View and manage customer orders</p>
    </div>

    <div class="orders-grid">
      <?php 
      $order_count = mysqli_num_rows($result);
      if($order_count == 0) {
      ?>
        <div class="empty-state">
          <i class="fas fa-inbox"></i>
          <p>No orders yet</p>
        </div>
      <?php
      } else {
        while($t_o = mysqli_fetch_assoc($result)) :
          $idl = $t_o['list_ids'];
          $parts_id = explode('/', $idl); 
          $q = $t_o['list_qua'];
          $parts_q = explode('/', $q);
      ?>
        <div class="order-card">
          <div class="order-header">
            <div class="order-id">
              Order <strong>#<?php echo str_pad($t_o['id'], 5, '0', STR_PAD_LEFT); ?></strong>
            </div>
            <div class="order-status">
              <i class="fas fa-clock"></i> Pending
            </div>
          </div>

          <div class="order-content">
            <!-- Client Section -->
            <div class="client-section">
              <div class="section-title">
                <i class="fas fa-user-circle"></i> Customer Information
              </div>
              <div class="client-info">
                <div class="info-item">
                  <div class="info-label"><i class="fas fa-user"></i> Name</div>
                  <div class="info-value"><?php echo htmlspecialchars($t_o['Fname'] . " " . $t_o['Lname']); ?></div>
                </div>
                <div class="info-item">
                  <div class="info-label"><i class="fas fa-phone"></i> Phone</div>
                  <div class="info-value"><?php echo htmlspecialchars($t_o['tel']); ?></div>
                </div>
                <div class="info-item">
                  <div class="info-label"><i class="fas fa-city"></i> City</div>
                  <div class="info-value"><?php echo htmlspecialchars($t_o['city']); ?></div>
                </div>
                <div class="info-item">
                  <div class="info-label"><i class="fas fa-map-marker-alt"></i> Address</div>
                  <div class="info-value"><?php echo htmlspecialchars($t_o['addr']); ?></div>
                </div>
              </div>
            </div>

            <!-- Items Section -->
            <div class="items-section">
              <div class="section-title">
                <i class="fas fa-box"></i> Order Items (<?php echo count(array_filter($parts_id)); ?>)
              </div>
              <div class="items-grid">
                <?php
                foreach($parts_id as $i => $id):
                  if(empty($id)) continue;
                  $result_o = mysqli_query($cnx , "SELECT name, price, image_url from products where product_id = '$id'");
                  while($t_p = mysqli_fetch_assoc($result_o)) : 
                ?>
                  <div class="product-item">
                    <img src="<?php echo htmlspecialchars($t_p['image_url']); ?>" alt="<?php echo htmlspecialchars($t_p['name']); ?>" class="product-image" onerror="this.src='https://via.placeholder.com/220x180?text=No+Image'">
                    <div class="product-info">
                      <div class="product-name"><?php echo htmlspecialchars($t_p['name']); ?></div>
                      <div class="product-details">
                        <span class="product-price">$<?php echo number_format($t_p['price'], 2); ?></span>
                        <span class="product-qty">× <?php echo intval($parts_q[$i]); ?></span>
                      </div>
                    </div>
                  </div>
                <?php 
                  endwhile;
                endforeach;
                ?>
              </div>
            </div>
          </div>

          <div class="order-footer">
            <a href="orderdone.php?id=<?php echo $t_o["id"]; ?>" class="btn btn-primary">
              <i class="fas fa-check-circle"></i> Mark as Done
            </a>
          </div>
        </div>
      <?php 
        endwhile;
      }
      ?>
    </div>
  </div>

</body>
</html>
