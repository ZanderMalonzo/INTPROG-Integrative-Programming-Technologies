<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$basePath = '/INTPROG SYSTEM';

if (!isset($_SESSION['user_id'])) {
    header("Location: {$basePath}/public/auth/login.php");
    exit();
}

require __DIR__ . '/../../app/includes/profiletab.php';
require_once __DIR__ . '/../../app/config/db.php';

// Check if user is admin
$user_id = (int)$_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$is_admin = ($user && $user['role'] === 'admin');
$currentPage = basename($_SERVER['PHP_SELF']);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Café Java Menu</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>/assets/css/navbar.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo $basePath; ?>/assets/css/menu.css?v=<?php echo time(); ?>">
    <script>
        window.APP_BASE_URL = '<?php echo $basePath; ?>';
    </script>
    <script src="<?php echo $basePath; ?>/assets/js/profiletab.js"></script>
    <script src="<?php echo $basePath; ?>/assets/js/menu.js?v=<?php echo time(); ?>" defer></script>
    <script src="<?php echo $basePath; ?>/assets/js/addtocart.js?v=<?php echo time(); ?>" defer></script>
    <script src="<?php echo $basePath; ?>/assets/js/searchbar.js?v=<?php echo time(); ?>" defer></script>

</head>

<body>

    <header class="navbar">
        <div class="logo">
            <img src="<?php echo $basePath; ?>/assets/images/cafejavalogo.jpg" alt="Cafe Java Logo">
        </div>
        <div class="nav-right">
            <nav>
                <ul class="nav-links">
                    <li><a href="landingpage.php" class="<?php echo $currentPage === 'landingpage.php' ? 'active' : '' ?>">HOME</a></li>
                    <li><a href="menu.php" class="<?php echo $currentPage === 'menu.php' ? 'active' : '' ?>">MENU</a></li>
                    <li><a href="contact.php" class="<?php echo $currentPage === 'contact.php' ? 'active' : '' ?>">CONTACT</a></li>
                    <?php if ($is_admin): ?>
                    <li><a href="../admin/admin_dashboard.php" style="color: #c8c3a6; font-weight: bold;">ADMIN</a></li>
                    <?php endif; ?>
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
    <div class="floating-cart">
        <img src="<?php echo $basePath; ?>/assets/images/icons8-coffee-cup-30.png" alt="">
        <span class="cart-count">0</span>
    </div>


    <aside class="side-navbar">
        <nav class="side-nav">
            <ul class="side-nav-links">
                <li><a href="#" class="side-nav-link active" data-view="menu">Menu</a></li>
                <li><a href="#" class="side-nav-link" data-view="orders">Orders (History)</a></li>
            </ul>
        </nav>
    </aside>


    <section class="menu-section">
        <div class="menu-view active" id="menuView">
            <div class="category-toggle">
                <button class="tab_btn active">Coffee</button>
                <button class="tab_btn">Snacks</button>
            </div>
            <div class="search-container">
                <input type="text" id="searchInput" placeholder="Search for coffee or snacks..." />
                <button id="clearSearch">✕</button>
            </div>

        <div class="coffee active" id="coffee">
            <h1 class="menu-title">Iced Coffee</h1>
            <div class="menu-grid">
                <div class="menu-card">
                    <div class="menu-image">
                        <img src="<?php echo $basePath; ?>/assets/images/IcedCaramel.jpg" alt="Iced Caramel Macchiato">
                    </div>
                    <div class="menu-info">
                        <h3>Iced Caramel Macchiato</h3>
                        <p>₱149</p>
                    </div>
                    <div class="add-btn">+</div>
                </div>
                <div class="menu-card">
                    <div class="menu-image">
                        <img src="<?php echo $basePath; ?>/assets/images/IcedSpanishLatte.jpg" alt="Iced Spanish Latte">
                    </div>
                    <div class="menu-info">
                        <h3>Iced Spanish Latte</h3>
                        <p>₱135</p>
                    </div>
                    <div class="add-btn">+</div>
                </div>
                <div class="menu-card">
                    <div class="menu-image">
                        <img src="<?php echo $basePath; ?>/assets/images/IcedAmericano.jpg" alt="Iced Americano">
                    </div>
                    <div class="menu-info">
                        <h3>Iced Americano</h3>
                        <p>₱149</p>
                    </div>
                    <div class="add-btn">+</div>
                </div>
                <div class="menu-card">
                    <div class="menu-image">
                        <img src="<?php echo $basePath; ?>/assets/images/IcedDarkChocolateMocha.jpg" alt="Iced Dark Chocolate Mocha">
                    </div>
                    <div class="menu-info">
                        <h3>Iced Dark Chocolate Mocha</h3>
                        <p>₱149</p>
                    </div>
                    <div class="add-btn">+</div>
                </div>
                <div class="menu-card">
                    <div class="menu-image">
                        <img src="<?php echo $basePath; ?>/assets/images/cedCappuccino.jpg" alt="Iced Cappuccino">
                    </div>
                    <div class="menu-info">
                        <h3>Iced Cappuccino</h3>
                        <p>₱149</p>
                    </div>
                    <div class="add-btn">+</div>
                </div>

            </div>

            <h1 class="menu-title">Hot Coffee</h1>
            <div class="menu-grid">
                <div class="menu-card">
                    <div class="menu-image">
                        <img src="<?php echo $basePath; ?>/assets/images/Espresso.png" alt="Espresso">
                    </div>
                    <div class="menu-info">
                        <h3>Hot Espresso</h3>
                        <p>₱149</p>
                    </div>
                    <div class="add-btn">+</div>
                </div>
                <div class="menu-card">
                    <div class="menu-image">
                        <img src="<?php echo $basePath; ?>/assets/images/EspressoMacchiato.png" alt="Hot Espresso Macchiato">
                    </div>
                    <div class="menu-info">
                        <h3>Hot Espresso Macchiato</h3>
                        <p>₱149</p>
                    </div>
                    <div class="add-btn">+</div>
                </div>
                <div class="menu-card">
                    <div class="menu-image">
                        <img src="<?php echo $basePath; ?>/assets/images/HotBrewedCoffee.jpg" alt="Hot Brewed Coffee">
                    </div>
                    <div class="menu-info">
                        <h3>Hot Brewed Coffee</h3>
                        <p>₱149</p>
                    </div>
                    <div class="add-btn">+</div>
                </div>
                <div class="menu-card">
                    <div class="menu-image">
                        <img src="<?php echo $basePath; ?>/assets/images/FlatWhite.jpg" alt="Flat White">
                    </div>
                    <div class="menu-info">
                        <h3>Hot Flat White</h3>
                        <p>₱149</p>
                    </div>
                    <div class="add-btn">+</div>
                </div>
                <div class="menu-card">
                    <div class="menu-image">
                        <img src="<?php echo $basePath; ?>/assets/images/CaffeMisto.jpg" alt="Hot Caffe Misto">
                    </div>
                    <div class="menu-info">
                        <h3>Hot Cafe Misto</h3>
                        <p>₱149</p>
                    </div>
                    <div class="add-btn">+</div>
                </div>

            </div>
        </div>

        <div class="snacks">
            <h1 class="menu-title">Bake-in</h1>
            <div class="snack-grid">
                <div class="snack-card">
                    <div class="menu-image">
                        <img src="<?php echo $basePath; ?>/assets/images/Croissant.png" alt="Croissant">
                    </div>
                    <div class="menu-info">
                        <h3>Croissant</h3>
                        <p>₱69</p>
                    </div>
                    <div class="add-btn">+</div>
                </div>
                <div class="snack-card">
                    <div class="menu-image">
                        <img src="<?php echo $basePath; ?>/assets/images/CheeseDanish.png" alt="Cheese Danish">
                    </div>
                    <div class="menu-info">
                        <h3>Cheese Danish</h3>
                        <p>₱90</p>
                    </div>
                    <div class="add-btn">+</div>
                </div>
                <div class="snack-card">
                    <div class="menu-image">
                        <img src="<?php echo $basePath; ?>/assets/images/BelgianWaffle.jpg" alt="Belgian Waffle">
                    </div>
                    <div class="menu-info">
                        <h3>Belgian Waffle</h3>
                        <p>₱65</p>
                    </div>
                    <div class="add-btn">+</div>
                </div>
                <div class="snack-card">
                    <div class="menu-image">
                        <img src="<?php echo $basePath; ?>/assets/images/ChocolateDoughnut.png" alt="Chocolate Doughnut">
                    </div>
                    <div class="menu-info">
                        <h3>Chocolate Doughnut</h3>
                        <p>40</p>
                    </div>
                    <div class="add-btn">+</div>
                </div>
                <div class="snack-card">
                    <div class="menu-image">
                        <img src="<?php echo $basePath; ?>/assets/images/ChocolateCookie.jpg" alt="Chocolate Cookie">
                    </div>
                    <div class="menu-info">
                        <h3>Chocolate Cookie</h3>
                        <p>₱45</p>
                    </div>
                    <div class="add-btn">+</div>
                </div>
            </div>

            <h1 class="menu-title">Sandwiches</h1>
            <div class="snack-grid">
                <div class="snack-card">
                    <div class="menu-image">
                        <img src="<?php echo $basePath; ?>/assets/images/CheeseEggSandwich.jpg" alt="Cheese Egg Sandwich">
                    </div>
                    <div class="menu-info">
                        <h3>Cheese Egg Sandwich</h3>
                        <p>₱45</p>
                    </div>
                    <div class="add-btn">+</div>
                </div>
                <div class="snack-card">
                    <div class="menu-image">
                        <img src="<?php echo $basePath; ?>/assets/images/PattyMelt.jpg" alt="Patty Melt">
                    </div>
                    <div class="menu-info">
                        <h3>Patty Melt</h3>
                        <p>₱69</p>
                    </div>
                    <div class="add-btn">+</div>
                </div>
                <div class="snack-card">
                    <div class="menu-image">
                        <img src="<?php echo $basePath; ?>/assets/images/HamandCheeseToastie.jpg" alt="Ham and Cheese Toast">
                    </div>
                    <div class="menu-info">
                        <h3>Ham and Cheese</h3>
                        <p>₱45</p>
                    </div>
                    <div class="add-btn">+</div>
                </div>
                <div class="snack-card">
                    <div class="menu-image">
                        <img src="<?php echo $basePath; ?>/assets/images/TurkeyHam.jpg" alt="Turkey Ham">
                    </div>
                    <div class="menu-info">
                        <h3>Turkey Ham</h3>
                        <p>₱85</p>
                    </div>
                    <div class="add-btn">+</div>
                </div>
                <div class="snack-card">
                    <div class="menu-image">
                        <img src="<?php echo $basePath; ?>/assets/images/RoastedChickenPesto.jpg" alt="Roasted Chicken Pesto">
                    </div>
                    <div class="menu-info">
                        <h3>Roasted Chicken Pesto</h3>
                        <p>₱95</p>
                    </div>
                    <div class="add-btn">+</div>
                </div>
            </div>

            <h1 class="menu-title">Desserts</h1>
            <div class="dessert-grid">
                <div class="snack-card">
                    <div class="menu-image">
                        <img src="<?php echo $basePath; ?>/assets/images/NewYorkCheesecake.jpg" alt="New York Cheesecake">
                    </div>
                    <div class="menu-info">
                        <h3>New York Cheesecake</h3>
                        <p>₱130</p>
                    </div>
                    <div class="add-btn">+</div>
                </div>
                <div class="snack-card">
                    <div class="menu-image">
                        <img src="<?php echo $basePath; ?>/assets/images/BlueberryLiciousCheesecake.jpg" alt="Blueberry Cheesecake">
                    </div>
                    <div class="menu-info">
                        <h3>Blueberry Cheesecake</h3>
                        <p>₱139</p>
                    </div>
                    <div class="add-btn">+</div>
                </div>
                <div class="snack-card">
                    <div class="menu-image">
                        <img src="<?php echo $basePath; ?>/assets/images/ClassicChocolateCake.jpg" alt="Chocolate Cake">
                    </div>
                    <div class="menu-info">
                        <h3>Chocolate Cake</h3>
                        <p>₱139</p>
                    </div>
                    <div class="add-btn">+</div>
                </div>
            </div>
        </div>
        </div>

        <div class="order-history-view" id="orderHistoryView">
            <h1 class="menu-title">Order History</h1>
            <div id="ordersContainer" class="orders-container">
                <div class="loading">Loading orders...</div>
            </div>
        </div>

    </section>


    <div class="cart-modal" id="cartModal">
        <div class="cart-content">
            <button class="close-cart">×</button>
            <h2>Your Cart</h2>
            <div class="cart-items"></div>
            <div class="cart-total">
                <h3>Total: ₱<span id="cartTotal">0</span></h3>
            </div>
            <button class="checkout-btn">Checkout</button>
        </div>
    </div>


    <div id="paymentModal" class="payment-modal">
        <div class="payment-content">
            <button class="close-payment">×</button>
            <h2>Select Payment Method</h2>
            <form id="paymentForm">
                <label>
                    <input type="radio" name="payment" value="Cash on Delivery"> Cash on Delivery
                </label><br>
                <label>
                    <input type="radio" name="payment" value="GCash"> GCash
                </label><br>
                <label>
                    <input type="radio" name="payment" value="Credit Card"> Credit Card
                </label><br><br>
                <button type="submit" class="confirm-payment">Confirm Payment</button>
            </form>
        </div>
    </div>



    <div id="toast"></div>

    <script>
        // Sidebar Navigation Toggle
        const sideNavLinks = document.querySelectorAll('.side-nav-link');
        const menuView = document.getElementById('menuView');
        const orderHistoryView = document.getElementById('orderHistoryView');
        const ordersContainer = document.getElementById('ordersContainer');

        sideNavLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const view = link.getAttribute('data-view');

                // Remove active class from all links
                sideNavLinks.forEach(l => l.classList.remove('active'));
                link.classList.add('active');

                // Toggle views
                if (view === 'menu') {
                    menuView.classList.add('active');
                    orderHistoryView.classList.remove('active');
                } else if (view === 'orders') {
                    menuView.classList.remove('active');
                    orderHistoryView.classList.add('active');
                    loadOrders();
                }
            });
        });

        // Load orders function
        async function loadOrders() {
            ordersContainer.innerHTML = '<div class="loading">Loading orders...</div>';

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
                    ordersContainer.innerHTML = `
                        <div class="no-orders">
                            <h2 style="color: #c8c3a6;" >No orders yet</h2>
                            <p style="color: #c8c3a6;">Start ordering from our menu!</p>
                        </div>
                    `;
                    return;
                }

                ordersContainer.innerHTML = data.orders.map((order, index) => {
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
                ordersContainer.innerHTML = `
                    <div class="error">
                        <strong>Error:</strong> ${error.message}
                    </div>
                `;
                console.error('Error loading orders:', error);
            }
        }
    </script>

</body>

</html>