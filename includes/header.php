<?php
require_once __DIR__ . '/../db.php';
$global_content = get_page_content('contact'); // For footer/global info
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($page_title) ? $page_title : 'INTM Global | Nutritional Therapy Management'; ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./assets/styles.css">
  <style>
    /* Page Hero spacing */
    .page-hero { padding: 140px 5% 80px; background: var(--warm-white); text-align: center; }
    .page-hero h1 { font-family: 'Playfair Display', serif; font-size: clamp(2.5rem, 5vw, 4rem); margin-bottom: 20px; }
    .page-hero h1 em { color: var(--sage); font-style: italic; }
    .page-hero p { max-width: 700px; margin: 0 auto; color: var(--mid); font-size: 18px; line-height: 1.6; }
    
    /* Modal styles */
    .modal-overlay { display: none; position: fixed; inset: 0; z-index: 1000; background: rgba(44, 44, 44, 0.6); backdrop-filter: blur(4px); align-items: center; justify-content: center; }
    .modal-overlay.active { display: flex; }
    .modal-content { background: white; border-radius: 12px; padding: 40px; width: 90%; max-width: 550px; position: relative; box-shadow: 0 24px 48px rgba(0,0,0,0.1); }
    .close-btn { position: absolute; top: 20px; right: 24px; font-size: 28px; color: var(--soft); cursor: pointer; }
    
    .form-group { margin-bottom: 16px; }
    .form-group label { display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px; }
    .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 12px; border: 1.5px solid var(--border); border-radius: 6px; outline: none; background: var(--cream); font-family: inherit; }
  </style>
</head>
<body>
  <nav id="navbar">
    <a href="./index.php" class="nav-logo">
      <img src="./Images/logo/intmgloballogo.jpg" alt="INTM Logo" style="height: 52px; width: auto; object-fit: contain;">
      <div class="nav-logo-text">
        <strong>INTM</strong>
        <span>Nutritional Therapy Management</span>
      </div>
    </a>
    <ul class="nav-links">
      <li><a href="./index.php">Home</a></li>
      <li class="nav-dropdown">
        <details>
          <summary>Programs <span class="caret">▾</span></summary>
          <div class="dropdown-menu">
            <a href="./courses.php">Courses</a>
            <a href="./workshops.php">Workshops</a>
            <a href="./membership.php">Membership</a>
            <a href="./projects.php">Projects</a>
          </div>
        </details>
      </li>
      <li class="nav-dropdown">
        <details>
          <summary>Collaborations <span class="caret">▾</span></summary>
          <div class="dropdown-menu">
            <a href="./collaborations.php">Partners</a>
            <a href="./collaboration-form.php">Become a Collaborator</a>
          </div>
        </details>
      </li>
      <li class="nav-dropdown">
        <details>
          <summary>Institute <span class="caret">▾</span></summary>
          <div class="dropdown-menu">
            <a href="./about.php">About Us</a>
            <a href="./team.php">Our Team</a>
            <a href="./accreditation.php">Accreditation</a>
            <a href="./reports.php">Annual Reports</a>
            <a href="./careers.php">Careers</a>
            <a href="./verify.php">Verify Credentials</a>
            <a href="./contact.php">Contact</a>
          </div>
        </details>
      </li>
    </ul>
    <a href="./membership.php" class="nav-cta">Join INTM</a>
    <div class="hamburger" id="hamburger" onclick="toggleMenu()">
      <span></span><span></span><span></span>
    </div>
  </nav>

  <div class="mobile-menu" id="mobileMenu">
    <a href="./index.php">Home</a>
    <a href="./courses.php">Courses</a>
    <a href="./workshops.php">Workshops</a>
    <a href="./membership.php">Membership</a>
    <a href="./about.php">About Us</a>
    <a href="./contact.php">Contact</a>
  </div>

  <main>
