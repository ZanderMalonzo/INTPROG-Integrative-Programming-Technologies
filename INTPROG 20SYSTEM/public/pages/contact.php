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
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact - Café Java</title>
  <link rel="stylesheet" href="<?php echo $basePath; ?>/assets/css/navbar.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="<?php echo $basePath; ?>/assets/css/landingpage.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="<?php echo $basePath; ?>/assets/css/contact.css?v=<?php echo time(); ?>">
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
          <li><a href="menu.php" class="<?php echo $currentPage === 'menu.php' ? 'active' : '' ?>">MENU</a></li>
<li><a href="contact.php" class="<?php echo $currentPage === 'contact.php' ? 'active' : '' ?>">CONTACT</a></li>

          <li class="dropdown">
            <a href="#" class="profile">
              <img src="<?php echo $basePath; ?>/uploads/<?php echo htmlspecialchars($profile_pic); ?>?v=<?php echo time(); ?>"
                alt="Profile Picture">
            </a>

            <div class="profile-tab dropdown-menu">
              <div class="profile-header">
                <img src="<?php echo $basePath; ?>/uploads/<?php echo htmlspecialchars($profile_pic); ?>?v=<?php echo time(); ?>"
                  alt="Profile Picture">
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
<section class="contact-hero">
      <h1>Contact Us</h1>
      <p>Have a question or feedback? We're here to help. Reach out and let's connect over a great cup of coffee.</p>
</section>

<section class="contact-content">
      <div class="contact-container">
        <div class="contact-info">
          <h2>Visit Us</h2>
          <p><strong>Address:</strong> Muntinlupa City, Philippines</p>
          <p><strong>Phone:</strong> +63 912 345 6789</p>
          <p><strong>Email:</strong> cafejava@gmail.com</p>
          <p><strong>Hours:</strong> Mon-Fri 7AM-8PM, Sat-Sun 8AM-9PM</p>
          <p>We pride ourselves on serving the freshest brews and warmest hospitality. Stop by for a chat or to enjoy
            our cozy atmosphere!</p>
        </div>

        <div class="contact-form">
          <h2>Send a Message</h2>
          <form action="contact_process.php" method="POST">
            <div class="form-group">
              <label for="name">Name</label>
              <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
              <label for="email">Email</label>
              <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
              <label for="message">Message</label>
              <textarea id="message" name="message" rows="5" required></textarea>
            </div>
            <button type="submit" class="submit-btn">Send</button>
          </form>
        </div>
      </div>
    </section>
    
    <section class="social-media">
      <h2>Follow Us</h2>
      <p>Stay connected and get the latest updates on our menu and events.</p>
      <div class="social-links">
        <a href="#" aria-label="Facebook"><i><img src="<?php echo $basePath; ?>/assets/images/icons8-fb-50.png" alt=""></i></a>
        <a href="#" aria-label="Instagram"><i><img src="<?php echo $basePath; ?>/assets/images/icons8-instagram-50.png" alt=""></i></a>
        <a href="#" aria-label="Twitter"><i><img src="<?php echo $basePath; ?>/assets/images/icons8-twitter-50.png" alt=""></i></a>
      </div>
    </section>
    

    

    

    <section class="faq-section">
      <h2>Frequently Asked Questions</h2>
      <div class="faq-item">
        <h3>What types of coffee do you offer?</h3>
        <p>We offer a wide range of coffees, including espresso, cappuccino, latte, and specialty blends sourced from
          around the world.</p>
      </div>
      <div class="faq-item">
        <h3>Do you have vegetarian options?</h3>
        <p>Yes! Our menu includes various vegetarian and vegan options, from salads to pastries.</p>
      </div>
      <div class="faq-item">
        <h3>Can I book a table for a group?</h3>
        <p>Absolutely! Contact us in advance to reserve a table for groups. We recommend calling ahead for larger
          parties.</p>
      </div>
      <div class="faq-item">
        <h3>Do you offer delivery?</h3>
        <p>Yes, we partner with local delivery services. Check our menu page for more details or call us to place an
          order.</p>
      </div>
      <div class="faq-item">
        <h3>Are pets allowed?</h3>
        <p>We welcome well-behaved pets in our outdoor seating area. Please keep them leashed and clean up after them.
        </p>
      </div>
    </section>


  <script src="<?php echo $basePath; ?>/assets/js/contact_faq.js?v=<?php echo time(); ?>"></script>
</body>

</html>