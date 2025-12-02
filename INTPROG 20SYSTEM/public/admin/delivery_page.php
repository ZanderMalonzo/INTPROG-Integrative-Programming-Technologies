<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../app/config/db.php';
$basePath = '/INTPROG SYSTEM';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: {$basePath}/public/admin/admin_login.php");
    exit;
}

// Check if user is admin (delivery page is for admins/delivery personnel)
$user_id = (int)$_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT role, username, profile_pic FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || strtolower(trim($user['role'])) !== 'admin') {
    header("Location: {$basePath}/public/pages/menu.php");
    exit;
}

$username = $user['username'];
$profile_pic = $user['profile_pic'] ?? 'profile-placeholder.jpg';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Management - Café Java</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>/assets/css/admin_dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo $basePath; ?>/assets/css/delivery_page.css?v=<?php echo time(); ?>">
</head>

<body>
    <div class="admin-top-logo"><img src="<?php echo $basePath; ?>/assets/images/cafejavalogo.jpg" alt="Cafe Java Logo"></div>
    <div class="admin-header">
        <div class="admin-header-content">
            <div>
                <h1>🚚 Delivery Management</h1>
                <p class="subtitle">Track and Complete Orders</p>
            </div>
            <div class="admin-user-info">
                <img src="<?php echo $basePath; ?>/uploads/<?php echo htmlspecialchars($profile_pic); ?>?v=<?php echo time(); ?>" alt="Admin">
                <div class="user-details">
                    <span class="username"><?php echo htmlspecialchars($username); ?></span>
                    <span class="role-badge">Administrator</span>
                </div>
                <a href="<?php echo $basePath; ?>/public/auth/logout.php" style="color: #fff; text-decoration: none; margin-left: 10px; padding: 8px 15px; background: rgba(255,255,255,0.2); border-radius: 6px; font-size: 13px;">Logout</a>
            </div>
        </div>
    </div>


    <div class="admin-nav">
            <ul class="admin-nav-links">
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="delivery_page.php" class="active">Delivery</a></li>
                <li><a href="pos_sales.php">POS Sales</a></li>
            </ul>
    </div>


    <div class="container">
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-header">
                    <span class="stat-card-title">Pending Delivery</span>
                    <div class="stat-card-icon warning">📦</div>
                </div>
                <p class="stat-card-value" id="pendingDelivery">-</p>
            </div>
            <div class="stat-card">
                <div class="stat-card-header">
                    <span class="stat-card-title">Out for Delivery</span>
                    <div class="stat-card-icon primary">🚚</div>
                </div>
                <p class="stat-card-value" id="outForDelivery">-</p>
            </div>
            <div class="stat-card">
                <div class="stat-card-header">
                    <span class="stat-card-title">Completed Today</span>
                    <div class="stat-card-icon success">✅</div>
                </div>
                <p class="stat-card-value" id="completedToday">-</p>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="filter-tabs">
            <button class="filter-tab active" data-status="all">All Orders</button>
            <button class="filter-tab" data-status="pending">Pending</button>
            <button class="filter-tab" data-status="confirmed">Confirmed</button>
            <button class="filter-tab" data-status="preparing">Preparing</button>
            <button class="filter-tab" data-status="ready">Ready</button>
            <button class="filter-tab" data-status="out_for_delivery">Out for Delivery</button>
        </div>

        <!-- Orders Section -->
        <div class="orders-section">
            <div class="section-header">
                <h2>Delivery Orders</h2>
                <button class="refresh-btn" onclick="loadOrders()">Refresh</button>
            </div>
            <div class="orders-container" id="ordersContainer">
                <div class="loading">Loading orders...</div>
            </div>
        </div>
    </div>

    <script src="<?php echo $basePath; ?>/assets/js/admin_delivery.js?v=<?php echo time(); ?>"></script>
    
</body>

</html>

