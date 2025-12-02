<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
$basePath = '/INTPROG SYSTEM';
require_once __DIR__ . '/../../app/config/config.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: {$basePath}/public/auth/login.php");
  exit();
}

require __DIR__ . '/../../app/includes/profiletab.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Café Java</title>
  <link rel="stylesheet" href="<?php echo $basePath; ?>/assets/css/navbar.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="<?php echo $basePath; ?>/assets/css/landingpage.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="<?php echo $basePath; ?>/assets/css/footer.css?v=<?php echo time(); ?>">
  <script src="<?php echo $basePath; ?>/assets/js/profiletab.js"></script>
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
          <li><a href="contact.php">CONTACT</a></li>

          <li class="dropdown">
            <a href="#" class="profile">
              <img src="<?php echo $basePath; ?>/uploads/<?php echo htmlspecialchars($profile_pic); ?>?v=<?php echo time(); ?>">
            </a>

            <div class="profile-tab dropdown-menu">

              <div class="profile-header">
                <img src="<?php echo $basePath; ?>/uploads/<?php echo htmlspecialchars($profile_pic); ?>?v=<?php echo time(); ?>">
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


  <section class="hero">
    <div class="hero-content">
      <h1 class="userwelcome">Hi <span class="user"><?php echo htmlspecialchars($username); ?></span>, welcome to</h1>
      <h1 class="cafename">Café Java</h1>
      <p>Where Every Sip<br>Feels Like Home.</p>
      <a href="menu.php" class="btn">ORDER NOW</a>
    </div>
  </section>

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


</body>

</html>