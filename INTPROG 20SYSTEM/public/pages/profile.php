<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$basePath = '/INTPROG SYSTEM';

// Redirect to login if not authenticated (prevents defaulting to user_id=1)
if (!isset($_SESSION['user_id'])) {
    header("Location: {$basePath}/public/auth/login.php"); // Adjust to your login page
    exit;
}

require_once __DIR__ . '/../../app/config/db.php';

$user_id = (int) $_SESSION['user_id']; // Cast to int for safety

// Fetch User Data
$user_stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$user_stmt->execute([$user_id]);
$user_data = $user_stmt->fetch(PDO::FETCH_ASSOC);

if (!$user_data) {
    // User not found in database - redirect to login
    session_destroy();
    header("Location: {$basePath}/public/auth/login.php");
    exit;
}

// Fetch Address
$address_stmt = $pdo->prepare("SELECT * FROM addresses WHERE user_id = ?");
$address_stmt->execute([$user_id]);
$address_data = $address_stmt->fetch(PDO::FETCH_ASSOC);

// Fetch GCash Data
$gcash_stmt = $pdo->prepare("SELECT * FROM gcash_accounts WHERE user_id = ?");
$gcash_stmt->execute([$user_id]);
$gcash_data = $gcash_stmt->fetch(PDO::FETCH_ASSOC);

// Fetch Credit Card Data
$credit_stmt = $pdo->prepare("SELECT * FROM credit_cards WHERE user_id = ?");
$credit_stmt->execute([$user_id]);
$credit_data = $credit_stmt->fetch(PDO::FETCH_ASSOC);

// Handle profile pic
$profile_pic = $user_data['profile_pic'] ?? 'profile-placeholder.jpg';

// Build address string (only include non-empty parts)
$address_parts = array_filter([
    $address_data['street'] ?? '',
    $address_data['city'] ?? '',
    $address_data['province'] ?? '',
    $address_data['postal_code'] ?? ''
]);
$full_address = implode(', ', $address_parts) ?: 'Not set';

// Mask credit card number for security (show last 4 digits)
$masked_card_number = $credit_data['card_number'] ?? '';
if ($masked_card_number) {
    $masked_card_number = '**** **** **** ' . substr($masked_card_number, -4);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile - Café Java</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>/assets/css/navbar.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo $basePath; ?>/assets/css/footer.css?v=<?php echo time(); ?>">
    <style>
        /* Copied and adapted from the editable page's styles */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Poppins", sans-serif; }
        body { background-color: #1f3b2c; color: #333; padding: 0; } /* Removed flex to allow footer below */
        .profile-container { margin: 130px auto 0; display: flex; background: #fff; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); overflow: hidden; width: 1100px; max-width: 95%; height: 650px; }
        .profile-sidebar { background: #f0f3fa; padding: 40px 30px; width: 300px; text-align: center; }
        .profile-sidebar img { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; margin-bottom: 15px; }
        .profile-sidebar h3 { margin-bottom: 5px; color: #333; }
        .profile-sidebar p { color: #777; font-size: 14px; }
        .profile-stats { margin-top: 20px; text-align: left; }
        .profile-sidebar a { display: block; text-decoration: none; color: #418a63ff; margin-top: 20px; font-size: 14px; }
        .profile-content { flex: 1; padding: 40px 50px; overflow-y: auto; }
        .tabs { display: flex; gap: 30px; border-bottom: 2px solid #eee; margin-bottom: 25px; }
        .tab { padding-bottom: 8px; cursor: pointer; color: #555; }
        .tab.active { color: #1f3b2c; border-bottom: 3px solid #1f3b2c; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .view-section { margin-top: 30px; }
        .view-section h2 { color: #1f3b2c; margin-bottom: 20px; }
        .view-section p { margin-bottom: 15px; font-size: 16px; color: #333; }
        .view-section strong { font-weight: 600; color: #1f3b2c; }
        .view-section a { display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #1f3b2c; color: #fff; text-decoration: none; border-radius: 8px; font-weight: 500; }
        .view-section a:hover { background-color: #325d46ff; }
        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-top: 30px; }
        .form-group label { font-weight: 500; font-size: 14px; margin-bottom: 5px; display: block; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px; background: #f9f9f9; color: #666; cursor: not-allowed; }
        .footer { margin-top: 0px; }
    </style>
</head>
<body>
    <header class="navbar">
        <div class="logo">
            <img src="<?php echo $basePath; ?>/assets/images/cafejavalogo.jpg" alt="Cafe Java Logo">
        </div>
        <div class="nav-right">
        <nav>
            <ul class="nav-links">
            <li><a href="menu.php">MENU</a></li>
            <li><a href="#contact">CONTACT</a></li>

            <li class="dropdown">
                <a href="#" class="profile">
                <img src="<?php echo $basePath; ?>/uploads/<?php echo htmlspecialchars($profile_pic); ?>?v=<?php echo time(); ?>">
                </a>

                <div class="profile-tab dropdown-menu">

                <div class="profile-header">
                    <img src="<?php echo $basePath; ?>/uploads/<?php echo htmlspecialchars($profile_pic); ?>?v=<?php echo time(); ?>">
                    <div class="profile-name">
                    <strong><?php echo htmlspecialchars($address_data['full_name'] ?? $user_data['username'] ?? 'User'); ?></strong>
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

    <div class="profile-container">
        <!-- Left Sidebar (copied from editable page) -->
        <div class="profile-sidebar">
            <img src="<?php echo $basePath; ?>/uploads/<?php echo htmlspecialchars($profile_pic); ?>?v=<?php echo time(); ?>" alt="Profile Picture">
            <h3><?php echo htmlspecialchars($user_data['username'] ?? 'John Doe'); ?></h3>
            <p>Customer Account</p>
            <a href="profile.php">View Public Profile</a>
        </div>

        <!-- Right Content Section with Tabs -->
        <div class="profile-content">
            <div class="tabs">
                <div class="tab active" data-tab="account">Account Settings</div>
                <div class="tab" data-tab="gcash">GCash</div>
                <div class="tab" data-tab="credit">Credit Card</div>
            </div>

            <!-- Account Settings Tab Content (Read-Only) -->
            <div id="account" class="tab-content active">
                <div class="view-section">
                    <h2>Account Settings</h2>
                    <div class="form-grid">
                        <div class="form-group"><label>Full Name</label><input type="text" value="<?php echo htmlspecialchars($address_data['full_name'] ?? 'Not set'); ?>" readonly></div>
                        <div class="form-group"><label>Phone Number</label><input type="text" value="<?php echo htmlspecialchars($address_data['phone_number'] ?? 'Not set'); ?>" readonly></div>
                        <div class="form-group"><label>Street</label><input type="text" value="<?php echo htmlspecialchars($address_data['street'] ?? 'Not set'); ?>" readonly></div>
                        <div class="form-group"><label>City</label><input type="text" value="<?php echo htmlspecialchars($address_data['city'] ?? 'Not set'); ?>" readonly></div>
                        <div class="form-group"><label>Province</label><input type="text" value="<?php echo htmlspecialchars($address_data['province'] ?? 'Not set'); ?>" readonly></div>
                        <div class="form-group"><label>Postal Code</label><input type="text" value="<?php echo htmlspecialchars($address_data['postal_code'] ?? 'Not set'); ?>" readonly></div>
                    </div>
                    <a href="personal_info.php">Edit Profile</a>
                </div>
            </div>

            <!-- GCash Tab Content (Read-Only) -->
            <div id="gcash" class="tab-content">
                <div class="view-section">
                    <h2>GCash Information</h2>
                    <div class="form-grid">
                        <div class="form-group"><label>GCash Number</label><input type="text" value="<?php echo htmlspecialchars($gcash_data['gcash_number'] ?? 'Not set'); ?>" readonly></div>
                        <div class="form-group"><label>Full Name (as in GCash)</label><input type="text" value="<?php echo htmlspecialchars($gcash_data['gcash_name'] ?? 'Not set'); ?>" readonly></div>
                    </div>
                    <a href="personal_info.php">Edit Profile</a>
                </div>
            </div>

            <!-- Credit Card Tab Content (Read-Only, Masked) -->
            <div id="credit" class="tab-content">
                <div class="view-section">
                    <h2>Credit Card Information</h2>
                    <div class="form-grid">
                        <div class="form-group"><label>Card Number</label><input type="text" value="<?php echo htmlspecialchars($masked_card_number ?: 'Not set'); ?>" readonly></div>
                        <div class="form-group"><label>Expiry Date</label><input type="text" value="<?php echo htmlspecialchars($credit_data['expiry_date'] ?? 'Not set'); ?>" readonly></div>
                        <div class="form-group"><label>CVV</label><input type="text" value="***" readonly></div> <!-- Never display CVV -->
                        <div class="form-group"><label>Card Name</label><input type="text" value="<?php echo htmlspecialchars($credit_data['card_name'] ?? 'Not set'); ?>" readonly></div>
                    </div>
                    <a href="personal_info.php">Edit Profile</a>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer" id="contact">
        <div class="footer-container">
            <div class="footer-logo">
                <img src="<?php echo $basePath; ?>/assets/images/cafejavalogo.jpg" alt="Cafe Java Logo">
                <h2>Café Java</h2>
                <p>Where Every Sip Feels Like Home.</p>
            </div>
            <div class="footer-contact">
                <h3>Contact Us</h3>
                <p>Email: cafejava@gmail.com</p>
                <p>Phone: +63 912 345 6789</p>
                <p>Location: Muntinlupa City, Philippines</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date("Y"); ?> Café Java. All rights reserved.</p>
        </div>
    </footer>

    <script src="<?php echo $basePath; ?>/assets/js/profile_view_tabs.js?v=<?php echo time(); ?>"></script>
</body>
</html>
