<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../app/config/db.php';
$basePath = '/INTPROG SYSTEM';

// Clear any old session data and ensure fresh session
if (!isset($_SESSION['user_id'])) {
    header("Location: {$basePath}/public/auth/login.php");
    exit;
}

// Validate user_id is numeric and exists
$user_id = (int) $_SESSION['user_id'];
if ($user_id <= 0) {
    session_destroy();
    header("Location: {$basePath}/public/auth/login.php");
    exit;
}

// Verify user exists in database
$stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
$stmt->execute([$user_id]);
if (!$stmt->fetch()) {
    session_destroy();
    header("Location: {$basePath}/public/auth/login.php");
    exit;
}

// Get user info
$stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History - Café Java</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>/assets/css/navbar.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo $basePath; ?>/assets/css/orderhistory.css?v=<?php echo time(); ?>">
    <script src="<?php echo $basePath; ?>/assets/js/profiletab.js"></script>
</head>

<body>
    <?php require __DIR__ . '/../../app/includes/profiletab.php'; ?>

    <header class="navbar">
        <div class="logo">
            <img src="<?php echo $basePath; ?>/assets/images/cafejavalogo.jpg" alt="Cafe Java Logo">
        </div>

        <div class="nav-right">
            <nav>
                <ul class="nav-links">
                    <li><a href="menu.php" class="<?php echo $currentPage === 'menu.php' ? 'active' : '' ?>">MENU</a></li>
                    <li><a href="contact.php" class="<?php echo $currentPage === 'contact.php' ? 'active' : '' ?>">CONTACT</a></li>

                    <li class="dropdown">
                        <a href="#" class="profile">
                            <img src="<?php echo $basePath; ?>/uploads/<?php echo htmlspecialchars($profile_pic); ?>?v=<?php echo time(); ?>">
                        </a>

                        <div class="profile-tab dropdown-menu">

                            <div class="profile-header">
                                <img
                                    src="<?php echo $basePath; ?>/uploads/<?php echo htmlspecialchars($profile_pic); ?>?v=<?php echo time(); ?>">
                                <div class="profile-name">
                                    <strong><?php echo htmlspecialchars($address_data['full_name'] ?? $username); ?></strong>
                                    <span><?php echo htmlspecialchars($address_data['phone_number'] ?? ''); ?></span>
                                </div>
                            </div>

                            <hr>

                            <a href="profile.php">View Profile</a>
                            <a href="personal_info.php">Edit Profile</a>
                            <a href="<?php echo $basePath; ?>/public/auth/logout.php" class="logout">Logout</a>

                        </div>
                    </li>

                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <h1>Order History</h1>
        <div id="ordersContainer">
            <div class="loading">Loading orders...</div>
        </div>
    </div>

    <script>
        async function loadOrders() {
            const container = document.getElementById('ordersContainer');

            try {
                const response = await fetch('<?php echo $basePath; ?>/api/orders_proxy.php');

                if (!response.ok) {
                    const errorText = await response.text();
                    let errorData;
                    try {
                        errorData = JSON.parse(errorText);
                    } catch {
                        errorData = { error: errorText || 'Failed to load orders' };
                    }
                    throw new Error(errorData.error || 'Failed to load orders');
                }

                const responseText = await response.text();
                if (!responseText) {
                    throw new Error('Empty response from server');
                }

                const data = JSON.parse(responseText);

                if (data.orders && data.orders.length === 0) {
                    container.innerHTML = `
                        <div class="no-orders">
                            <h2>No orders yet</h2>
                            <p style="color: #c8c3a6" >Start ordering from our menu!</p>
                            <a href="menu.php" style="display: inline-block; margin-top: 20px; padding: 10px 20px; background: #c8c3a6; color: #1f3b2c; text-decoration: none; border-radius: 5px;">Browse Menu</a>
                        </div>
                    `;
                    return;
                }

                container.innerHTML = data.orders.map((order, index) => {
                    // Relative order number for user (1, 2, 3...)
                    const userOrderNumber = index + 1;
                    
                    const orderDate = new Date(order.created_at);
                    const formattedDate = orderDate.toLocaleString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    const itemsHtml = order.items.map(item => `
                        <div class="item">
                            <div>
                                <div class="item-name">${item.item_name}</div>
                                <div class="item-details">₱${parseFloat(item.item_price).toFixed(2)} × ${item.quantity}</div>
                            </div>
                            <div class="item-details">₱${parseFloat(item.subtotal).toFixed(2)}</div>
                        </div>
                    `).join('');

                    return `
                        <div class="order-card">
                            <div class="order-header">
                                <div>
                                    <div class="order-id">Order #${userOrderNumber}</div>
                                    <div class="order-date">${formattedDate}</div>
                                </div>
                                <span class="status ${order.order_status}">${order.order_status.toUpperCase()}</span>
                            </div>
                            <div class="order-info">
                                <div class="info-item">
                                    <span class="info-label">Payment Method</span>
                                    <span class="info-value">${order.payment_method}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Customer</span>
                                    <span class="info-value">${order.full_name}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Phone</span>
                                    <span class="info-value">${order.phone_number}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Delivery Address</span>
                                    <span class="info-value">${order.delivery_address}</span>
                                </div>
                            </div>
                            <div class="order-items">
                                <h3>Items (${order.items.length})</h3>
                                ${itemsHtml}
                            </div>
                            <div class="total-amount">
                                <div class="total-label">Total Amount</div>
                                <div class="total-value">₱${parseFloat(order.total_amount).toFixed(2)}</div>
                            </div>
                        </div>
                    `;
                }).join('');

            } catch (error) {
                container.innerHTML = `
                    <div class="error">
                        <strong>Error:</strong> ${error.message}
                    </div>
                `;
                console.error('Error loading orders:', error);
            }
        }

        // Load orders on page load
        loadOrders();
    </script>
</body>

</html>