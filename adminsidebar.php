<link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
  .admin-sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 260px;
    height: 100vh;
    background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
    padding: 30px 0;
    box-shadow: 4px 0 20px rgba(0,0,0,0.3);
    z-index: 1000;
  }

  .admin-sidebar .logo {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 40px;
    gap: 10px;
    color: white;
    font-weight: 700;
    font-size: 1.3em;
  }

  .admin-sidebar .logo i {
    font-size: 1.5em;
    color: #667eea;
  }

  .admin-sidebar ul {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .admin-sidebar li {
    margin: 0;
  }

  .admin-sidebar a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 15px 25px;
    color: #cbd5e1;
    text-decoration: none;
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
    font-weight: 500;
  }

  .admin-sidebar a:hover {
    background: rgba(102, 126, 234, 0.1);
    color: #667eea;
    border-left-color: #667eea;
    padding-left: 30px;
  }

  .admin-sidebar a i {
    font-size: 1.2em;
    min-width: 24px;
  }

  body {
    margin-left: 260px;
  }

  @media (max-width: 768px) {
    .admin-sidebar {
      width: 0;
      overflow: hidden;
      transition: width 0.3s ease;
    }

    body {
      margin-left: 0;
    }
  }
</style>

<aside class="admin-sidebar">
  <div class="logo">
    <i class="fas fa-crown"></i>
    <span>Nuraya Admin</span>
  </div>
  <ul>
    <li>
      <a href="../manage/index.php" title="Manage Products">
        <i class="fas fa-box"></i>
        <span>Manage Products</span>
      </a>
    </li>
    <li>
      <a href="../uploads/index.php" title="Insert Products">
        <i class="fas fa-cloud-upload-alt"></i>
        <span>Insert Products</span>
      </a>
    </li>
    <li>
      <a href="../manage-orders" title="Show Orders">
        <i class="fas fa-shopping-bag"></i>
        <span>Show Orders</span>
      </a>
    </li>
    <li>
      <a href="../logout.php" title="Logout">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
      </a>
    </li>
  </ul>
</aside>


