<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../app/config/db.php'; // Your database connection file
$basePath = '/INTPROG SYSTEM';

if (!isset($_SESSION['user_id'])) {
    header("Location: {$basePath}/public/auth/login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

$message = $gcash_message = $credit_message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_type = $_POST['form_type'] ?? '';

    if ($form_type === 'address') {
        // Handle address update
        $full_name = $_POST['full_name'] ?? '';
        $phone_number = $_POST['phone_number'] ?? '';
        $street = $_POST['street'] ?? '';
        $city = $_POST['city'] ?? '';
        $province = $_POST['province'] ?? '';
        $postal_code = $_POST['postal_code'] ?? '';

        // Verify user exists before inserting/updating address
        $user_check = $pdo->prepare("SELECT id FROM users WHERE id = ?");
        $user_check->execute([$user_id]);
        if (!$user_check->fetch()) {
            $message = 'Error: User not found. Please login again.';
        } else {
            $stmt = $pdo->prepare("SELECT address_id FROM addresses WHERE user_id = ?");
            $stmt->execute([$user_id]);
            if ($stmt->fetch()) {
                $stmt = $pdo->prepare("UPDATE addresses SET full_name = ?, phone_number = ?, street = ?, city = ?, province = ?, postal_code = ? WHERE user_id = ?");
                $stmt->execute([$full_name, $phone_number, $street, $city, $province, $postal_code, $user_id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO addresses (user_id, full_name, phone_number, street, city, province, postal_code) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$user_id, $full_name, $phone_number, $street, $city, $province, $postal_code]);
            }
        }
        $message = 'Address updated successfully!';
    } elseif ($form_type === 'gcash') {
        // Handle GCash update
        $gcash_number = $_POST['gcash_number'] ?? '';
        $gcash_name = $_POST['gcash_name'] ?? '';

        $stmt = $pdo->prepare("SELECT gcash_id FROM gcash_accounts WHERE user_id = ?");
        $stmt->execute([$user_id]);
        if ($stmt->fetch()) {
            $stmt = $pdo->prepare("UPDATE gcash_accounts SET gcash_name = ?, gcash_number = ? WHERE user_id = ?");
            $stmt->execute([$gcash_name, $gcash_number, $user_id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO gcash_accounts (user_id, gcash_name, gcash_number) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $gcash_name, $gcash_number]);
        }
        $gcash_message = 'GCash account updated successfully!';
    } elseif ($form_type === 'credit') {
        // Handle credit card update
        $card_number = $_POST['card_number'] ?? '';
        $expiry_date = $_POST['expiry_date'] ?? '';
        $cvv = $_POST['cvv'] ?? '';
        $card_name = $_POST['card_name'] ?? '';

        $stmt = $pdo->prepare("SELECT card_id FROM credit_cards WHERE user_id = ?");
        $stmt->execute([$user_id]);
        if ($stmt->fetch()) {
            $stmt = $pdo->prepare("UPDATE credit_cards SET card_name = ?, card_number = ?, expiry_date = ?, cvv = ? WHERE user_id = ?");
            $stmt->execute([$card_name, $card_number, $expiry_date, $cvv, $user_id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO credit_cards (user_id, card_name, card_number, expiry_date, cvv) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $card_name, $card_number, $expiry_date, $cvv]);
        }
        $credit_message = 'Credit card updated successfully!';
    } elseif ($form_type === 'profile_pic') {
        // Handle profile picture upload
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['profile_pic']['tmp_name'];
            $file_name = basename($_FILES['profile_pic']['name']);
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($file_ext, $allowed_exts)) {
                $new_file_name = $user_id . '_profile.' . $file_ext;
                $upload_dir = __DIR__ . '/../../uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                $upload_path = $upload_dir . $new_file_name;

                if (move_uploaded_file($file_tmp, $upload_path)) {
                    // ✅ Save only filename (not full path)
                    $stmt = $pdo->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
                    $stmt->execute([$new_file_name, $user_id]);

                    // ✅ Update session so landingpage.php shows the new image instantly
                    $_SESSION['profile_pic'] = $new_file_name;

                    $message = 'Profile picture updated successfully!';

                    // ✅ Reload page to show updated picture immediately
                    header("Location: {$basePath}/public/pages/personal_info.php");
                    exit;
                } else {
                    $message = 'Failed to upload image.';
                }
            } else {
                $message = 'Invalid file type. Only JPG, PNG, GIF allowed.';
            }
        }
    }
}

// Fetch existing data for pre-filling
$address = $pdo->prepare("SELECT * FROM addresses WHERE user_id = ?");
$address->execute([$user_id]);
$address_data = $address->fetch(PDO::FETCH_ASSOC);

$gcash = $pdo->prepare("SELECT * FROM gcash_accounts WHERE user_id = ?");
$gcash->execute([$user_id]);
$gcash_data = $gcash->fetch(PDO::FETCH_ASSOC);

$credit = $pdo->prepare("SELECT * FROM credit_cards WHERE user_id = ?");
$credit->execute([$user_id]);
$credit_data = $credit->fetch(PDO::FETCH_ASSOC);

$user = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$user->execute([$user_id]);
$user_data = $user->fetch(PDO::FETCH_ASSOC);
$profile_pic = $user_data['profile_pic'] ?? 'profile-placeholder.jpg';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Personal Information</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>/assets/css/personal_info.css">
        <link rel="stylesheet" href="<?php echo $basePath; ?>/assets/css/navbar.css?v=<?php echo time(); ?>">

    <style>
        /* Updated CSS (removed tab-related styles) */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Poppins", sans-serif; }
        body { background-color: #1f3b2c; color: #333; display: flex; justify-content: center; padding: 50px; }
        .profile-container { margin-top: 80px; display: flex; background: #fff; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); overflow: hidden; width: 1100px; max-width: 95%; height: auto; min-height: 650px; } /* Adjusted height to auto for scrolling */
        .profile-sidebar { background: #f0f3fa; padding: 40px 30px; width: 300px; text-align: center; }
        .profile-sidebar img { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; margin-bottom: 15px; }
        .profile-sidebar h3 { margin-bottom: 5px; color: #333; }
        .profile-sidebar p { color: #777; font-size: 14px; }
        .profile-stats { margin-top: 20px; text-align: left; }
        .profile-sidebar a { display: block; text-decoration: none; color: #418a63ff; margin-top: 20px; font-size: 14px; }
        .profile-content { flex: 1; padding: 40px 50px; overflow-y: auto; }
        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-top: 30px; }
        .form-group label { font-weight: 500; font-size: 14px; margin-bottom: 5px; display: block; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px; }
        button.update-btn { margin-top: 25px; background-color: #1f3b2c; color: #fff; border: none; padding: 12px 20px; border-radius: 8px; cursor: pointer; font-weight: 500; }
        button.update-btn:hover { background-color: #325d46ff; }
        .message { text-align: center; color: #d00; margin-bottom: 10px; }
        .section-title { margin-top: 40px; margin-bottom: 20px; color: #1f3b2c; font-size: 18px; font-weight: 600; }
    </style>
</head>
<body>
   <header class="navbar">
        <div class="logo">
            <img src="<?php echo $basePath; ?>/assets/images/cafejavalogo.jpg" alt="Cafe Java Logo">
        </div>
    </header>

<div class="profile-container">

    <!-- Left Sidebar -->
    <div class="profile-sidebar">
        <img src="<?php echo $basePath; ?>/uploads/<?php echo htmlspecialchars($profile_pic); ?>?v=<?php echo time(); ?>" alt="Profile Picture">
        <h3><?php echo htmlspecialchars($user_data['username'] ?? 'John Doe'); ?></h3>
        <p>Customer Account</p>

        <a href="profile.php">View Public Profile</a>
    </div>

    <!-- Right Form Section (No Tabs) -->
    <div class="profile-content">
        <!-- Profile Picture Upload -->
        <p class="message"><?php echo $message; ?></p>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="form_type" value="profile_pic">
            <div class="form-group">
                <label>Profile Picture</label>
                <input type="file" name="profile_pic" accept="image/*">
            </div>
            <button type="submit" class="update-btn">Upload Picture</button>
        </form>

        <!-- Address Form -->
        <form action="" method="POST">
            <input type="hidden" name="form_type" value="address">
            <div class="form-grid">
                <div class="form-group"><label>Full Name</label><input type="text" name="full_name" value="<?php echo htmlspecialchars($address_data['full_name'] ?? ''); ?>" required></div>
                <div class="form-group"><label>Phone Number</label><input type="text" name="phone_number" value="<?php echo htmlspecialchars($address_data['phone_number'] ?? ''); ?>" required></div>
                <div class="form-group"><label>Street</label><input type="text" name="street" value="<?php echo htmlspecialchars($address_data['street'] ?? ''); ?>" required></div>
                <div class="form-group"><label>City</label><input type="text" name="city" value="<?php echo htmlspecialchars($address_data['city'] ?? ''); ?>" required></div>
                <div class="form-group"><label>Province</label><input type="text" name="province" value="<?php echo htmlspecialchars($address_data['province'] ?? ''); ?>" required></div>
                <div class="form-group"><label>Postal Code</label><input type="text" name="postal_code" value="<?php echo htmlspecialchars($address_data['postal_code'] ?? ''); ?>" required></div>
            </div>
            <button type="submit" class="update-btn">Update Address</button>
        </form>

        <!-- GCash Form -->
        <div class="section-title">GCash Information</div>
        <p class="message"><?php echo $gcash_message; ?></p>
        <form action="" method="POST">
            <input type="hidden" name="form_type" value="gcash">
            <div class="form-grid">
                <div class="form-group"><label>GCash Number</label><input type="text" name="gcash_number" value="<?php echo htmlspecialchars($gcash_data['gcash_number'] ?? ''); ?>" required></div>
                <div class="form-group"><label>Full Name (as in GCash)</label><input type="text" name="gcash_name" value="<?php echo htmlspecialchars($gcash_data['gcash_name'] ?? ''); ?>" required></div>
            </div>
            <button type="submit" class="update-btn">Update GCash</button>
        </form>

        <!-- Credit Card Form -->
        <div class="section-title">Credit Card Information</div>
        <p class="message"><?php echo $credit_message; ?></p>
        <form action="" method="POST">
            <input type="hidden" name="form_type" value="credit">
            <div class="form-grid">
                <div class="form-group"><label>Card Number</label><input type="text" name="card_number" value="<?php echo htmlspecialchars($credit_data['card_number'] ?? ''); ?>" required></div>
                <div class="form-group"><label>Expiry Date</label><input type="text" name="expiry_date" value="<?php echo htmlspecialchars($credit_data['expiry_date'] ?? ''); ?>" required></div>
                <div class="form-group"><label>CVV</label><input type="text" name="cvv" value="<?php echo htmlspecialchars($credit_data['cvv'] ?? ''); ?>" required></div>
                <div class="form-group"><label>Card Name</label><input type="text" name="card_name" value="<?php echo htmlspecialchars($credit_data['card_name'] ?? ''); ?>" required></div>
            </div>
            <button type="submit" class="update-btn">Update Credit Card</button>
        </form>
    </div>
</div>

</body>
</html>
