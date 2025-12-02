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

// Check if user is admin
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
    <title>Point of Sale - Café Java</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>/assets/css/admin_dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo $basePath; ?>/assets/css/pos_sales.css?v=<?php echo time(); ?>">
</head>

<body>
    <div class="admin-top-logo"><img src="<?php echo $basePath; ?>/assets/images/cafejavalogo.jpg" alt="Cafe Java Logo"></div>

    <div class="admin-header">
        <div class="admin-header-content">
            <div>
                <h1>💰 Point of Sale</h1>
                <p class="subtitle">Today's Sales Report</p>
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
            <li><a href="delivery_page.php">Delivery</a></li>
            <li><a href="pos_sales.php" class="active">POS Sales</a></li>
        </ul>
    </div>

    <div class="container">

        <div class="date-selector">
            <button class="date-btn" onclick="setDate('today')" id="btnToday">Today</button>
            <button class="date-btn" onclick="setDate('yesterday')" id="btnYesterday">Yesterday</button>
            <button class="date-btn" onclick="setDate('week')" id="btnWeek">This Week</button>
            <button class="date-btn" onclick="setDate('month')" id="btnMonth">This Month</button>
            <input type="date" id="customDate" class="custom-date-input" onchange="setCustomDate()">
        </div>

        <div class="sales-stats-grid">
            <div class="sales-stat-card primary">
                <div class="stat-icon">💰</div>
                <div class="stat-content">
                    <div class="stat-label">Total Sales</div>
                    <div class="stat-value" id="totalSales">₱0.00</div>
                    <div class="stat-change" id="salesChange">-</div>
                </div>
            </div>
            <div class="sales-stat-card success">
                <div class="stat-icon">📦</div>
                <div class="stat-content">
                    <div class="stat-label">Total Orders</div>
                    <div class="stat-value" id="totalOrders">0</div>
                    <div class="stat-change" id="ordersChange">-</div>
                </div>
            </div>
            <div class="sales-stat-card warning">
                <div class="stat-icon">📊</div>
                <div class="stat-content">
                    <div class="stat-label">Average Order Value</div>
                    <div class="stat-value" id="avgOrderValue">₱0.00</div>
                    <div class="stat-change" id="avgChange">-</div>
                </div>
            </div>
            <div class="sales-stat-card info">
                <div class="stat-icon">✅</div>
                <div class="stat-content">
                    <div class="stat-label">Completed Orders</div>
                    <div class="stat-value" id="completedOrders">0</div>
                    <div class="stat-change" id="completedChange">-</div>
                </div>
            </div>
        </div>

        <div class="orders-section">
            <div class="section-header">
                <h2>Payment Method Breakdown</h2>
            </div>
            <div class="payment-breakdown" id="paymentBreakdown">
                <div class="loading">Loading...</div>
            </div>
        </div>

        <div class="orders-section">
            <div class="section-header">
                <h2>Recent Orders</h2>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <span id="lastUpdate" class="last-update-text" style="font-size: 12px; color: #999;"></span>
                    <button class="refresh-btn" onclick="loadSalesData()" id="refreshBtn">Refresh</button>
                </div>
            </div>
            <div class="orders-container" id="recentOrders">
                <div class="loading">Loading orders...</div>
            </div>
        </div>
    </div>

    <script src="<?php echo $basePath; ?>/assets/js/admin_pos_sales.js?v=<?php echo time(); ?>"></script>
    <script type="text/disabled">
        const BASE_URL = '<?php echo $basePath; ?>';
        let currentDateFilter = 'today';
        let currentDate = new Date().toISOString().split('T')[0];

        function setDate(filter) {
            currentDateFilter = filter;
            
            // Update button states
            document.querySelectorAll('.date-btn').forEach(btn => btn.classList.remove('active'));
            
            const today = new Date();
            let startDate, endDate;
            
            switch(filter) {
                case 'today':
                    startDate = endDate = today.toISOString().split('T')[0];
                    document.getElementById('btnToday').classList.add('active');
                    break;
                case 'yesterday':
                    const yesterday = new Date(today);
                    yesterday.setDate(yesterday.getDate() - 1);
                    startDate = endDate = yesterday.toISOString().split('T')[0];
                    document.getElementById('btnYesterday').classList.add('active');
                    break;
                case 'week':
                    const weekStart = new Date(today);
                    weekStart.setDate(weekStart.getDate() - weekStart.getDay());
                    startDate = weekStart.toISOString().split('T')[0];
                    endDate = today.toISOString().split('T')[0];
                    document.getElementById('btnWeek').classList.add('active');
                    break;
                case 'month':
                    const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);
                    startDate = monthStart.toISOString().split('T')[0];
                    endDate = today.toISOString().split('T')[0];
                    document.getElementById('btnMonth').classList.add('active');
                    break;
            }
            
            currentDate = endDate;
            loadSalesData(startDate, endDate);
        }

        function setCustomDate() {
            const date = document.getElementById('customDate').value;
            if (date) {
                currentDateFilter = 'custom';
                currentDate = date;
                document.querySelectorAll('.date-btn').forEach(btn => btn.classList.remove('active'));
                loadSalesData(date, date);
            }
        }

        async function loadSalesData(startDate = null, endDate = null) {
            const refreshBtn = document.getElementById('refreshBtn');
            const lastUpdate = document.getElementById('lastUpdate');
            
            // Show loading state
            if (refreshBtn) {
                refreshBtn.disabled = true;
                refreshBtn.textContent = '⏳ Loading...';
            }
            
            // Show loading in containers
            document.getElementById('paymentBreakdown').innerHTML = '<div class="loading">Loading payment data...</div>';
            document.getElementById('recentOrders').innerHTML = '<div class="loading">Loading orders...</div>';
            
            try {
                // If no dates provided, use today
                if (!startDate || !endDate) {
                    const today = new Date().toISOString().split('T')[0];
                    startDate = endDate = today;
                }
                
                console.log('Loading sales data for:', startDate, 'to', endDate);

                const response = await fetch(`${BASE_URL}/api/sales_data.php?start_date=${startDate}&end_date=${endDate}`);
                
                const responseText = await response.text();
                
                if (!response.ok) {
                    let errorData;
                    try {
                        errorData = JSON.parse(responseText);
                        throw new Error(errorData.error || errorData.message || 'Failed to load sales data');
                    } catch (e) {
                        if (e.message && e.message !== 'Unexpected token') throw e;
                        throw new Error('Failed to load sales data: ' + responseText);
                    }
                }

                if (!responseText) {
                    throw new Error('Empty response from server');
                }

                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (e) {
                    console.error('JSON parse error:', e);
                    console.error('Response text:', responseText);
                    throw new Error('Invalid JSON response from server');
                }
                
                // Debug logging
                console.log('Sales data received:', data);
                console.log('Date range:', startDate, 'to', endDate);

                // Check for errors in response
                if (data.error) {
                    throw new Error(data.error || 'Unknown error from server');
                }

                // Update main stats with null safety
                const totalSales = parseFloat(data.total_sales || 0) || 0;
                const totalOrders = parseInt(data.total_orders || 0) || 0;
                const avgOrderValue = parseFloat(data.avg_order_value || 0) || 0;
                const completedOrders = parseInt(data.completed_orders || 0) || 0;

                document.getElementById('totalSales').textContent = `₱${totalSales.toFixed(2)}`;
                document.getElementById('totalOrders').textContent = totalOrders;
                document.getElementById('avgOrderValue').textContent = `₱${avgOrderValue.toFixed(2)}`;
                document.getElementById('completedOrders').textContent = completedOrders;

                // Update changes (comparison with previous period)
                const salesChangeEl = document.getElementById('salesChange');
                if (data.sales_change !== undefined && data.sales_change !== null && !isNaN(data.sales_change)) {
                    const change = parseFloat(data.sales_change);
                    salesChangeEl.textContent = `${change >= 0 ? '+' : ''}${change.toFixed(1)}%`;
                    salesChangeEl.className = `stat-change ${change >= 0 ? 'positive' : 'negative'}`;
                } else {
                    salesChangeEl.textContent = '-';
                    salesChangeEl.className = 'stat-change';
                }
                
                // Set other change elements to '-' for now (can be implemented later)
                const ordersChangeEl = document.getElementById('ordersChange');
                const avgChangeEl = document.getElementById('avgChange');
                const completedChangeEl = document.getElementById('completedChange');
                if (ordersChangeEl) ordersChangeEl.textContent = '-';
                if (avgChangeEl) avgChangeEl.textContent = '-';
                if (completedChangeEl) completedChangeEl.textContent = '-';

                // Payment method breakdown
                if (data.payment_methods && Object.keys(data.payment_methods).length > 0) {
                    const breakdownHtml = Object.entries(data.payment_methods).map(([method, amount]) => `
                        <div class="payment-item">
                            <div class="payment-method">${method}</div>
                            <div class="payment-amount">₱${parseFloat(amount || 0).toFixed(2)}</div>
                        </div>
                    `).join('');
                    
                    document.getElementById('paymentBreakdown').innerHTML = breakdownHtml;
                } else {
                    document.getElementById('paymentBreakdown').innerHTML = '<p style="text-align: center; color: #999; padding: 20px;">No payment data available</p>';
                }

                // Recent orders
                if (data.recent_orders && data.recent_orders.length > 0) {
                    const ordersHtml = data.recent_orders.map(order => {
                        const orderDate = new Date(order.created_at);
                        const formattedDate = orderDate.toLocaleString('en-US', {
                            month: 'short',
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });

                        return `
                            <div class="sales-order-card">
                                <div class="sales-order-header">
                                    <div>
                                        <div class="sales-order-number">Order #${order.order_id}</div>
                                        <div class="sales-order-date">${formattedDate}</div>
                                    </div>
                                    <div class="sales-order-amount">₱${parseFloat(order.total_amount).toFixed(2)}</div>
                                </div>
                                <div class="sales-order-details">
                                    <span class="sales-customer">${order.full_name}</span>
                                    <span class="sales-status ${order.order_status}">${order.order_status.replace('_', ' ').toUpperCase()}</span>
                                    <span class="sales-payment">${order.payment_method}</span>
                                </div>
                            </div>
                        `;
                    }).join('');

                    document.getElementById('recentOrders').innerHTML = ordersHtml;
                } else {
                    document.getElementById('recentOrders').innerHTML = `
                        <div class="no-orders">
                            <h2>No orders found</h2>
                            <p>No orders for the selected period.</p>
                        </div>
                    `;
                }

                // Update last refresh time
                if (lastUpdate) {
                    const now = new Date();
                    lastUpdate.textContent = `Last updated: ${now.toLocaleTimeString()}`;
                }

            } catch (error) {
                console.error('Error loading sales data:', error);
                console.error('Error details:', error.stack);
                
                // Show error in UI
                showNotification('Error loading sales data: ' + error.message, 'error');
                
                // Show error in containers
                document.getElementById('paymentBreakdown').innerHTML = 
                    `<p style="text-align: center; color: #c33; padding: 20px;">Error: ${error.message}</p>`;
                document.getElementById('recentOrders').innerHTML = 
                    `<div class="error"><strong>Error:</strong> ${error.message}</div>`;
            } finally {
                // Re-enable refresh button
                if (refreshBtn) {
                    refreshBtn.disabled = false;
                    refreshBtn.textContent = '🔄 Refresh';
                }
            }
        }

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Initialize - load today's data
        document.addEventListener('DOMContentLoaded', () => {
            setDate('today');
        });

        // Auto-refresh every 10 seconds for today's view (real-time updates)
        setInterval(() => {
            if (currentDateFilter === 'today') {
                loadSalesData();
            }
        }, 10000); // Refresh every 10 seconds instead of 60

        // Listen for order completion events from delivery page
        window.addEventListener('orderDelivered', (event) => {
            // Immediately refresh if viewing today's data
            if (currentDateFilter === 'today') {
                loadSalesData();
                showNotification('Sales updated! Order #' + event.detail.order_id + ' completed.', 'success');
            }
        });

        // Also listen for storage events (if delivery page uses localStorage to notify)
        window.addEventListener('storage', (event) => {
            if (event.key === 'orderDelivered' && currentDateFilter === 'today') {
                loadSalesData();
            }
        });
    </script>
</body>

</html>

