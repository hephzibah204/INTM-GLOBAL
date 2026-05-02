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
  <style>
    /* Dropdown Styling */
    .nav-links { display: flex; gap: 30px; list-style: none; align-items: center; }
    .nav-dropdown { position: relative; }
    .nav-dropdown summary { cursor: pointer; list-style: none; font-weight: 500; display: flex; align-items: center; gap: 5px; color: var(--text); }
    .nav-dropdown summary::-webkit-details-marker { display: none; }
    .dropdown-menu { position: absolute; top: 100%; left: 0; background: white; border: 1px solid var(--border); border-radius: 8px; padding: 15px 0; min-width: 220px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); z-index: 100; opacity: 0; visibility: hidden; transform: translateY(10px); transition: 0.3s; }
    .nav-dropdown[open] .dropdown-menu { opacity: 1; visibility: visible; transform: translateY(0); }
    .dropdown-menu a { display: block; padding: 10px 25px; color: var(--mid); text-decoration: none; font-size: 14px; transition: 0.2s; }
    .dropdown-menu a:hover { background: var(--warm-white); color: var(--primary); }
    
    /* Mobile Responsive Header */
    @media (max-width: 1024px) {
      .nav-links, .nav-cta { display: none; }
      .hamburger { display: block; }
    }
    
    .mobile-menu { display: none; position: fixed; top: 80px; left: 0; right: 0; background: white; padding: 30px; z-index: 999; border-bottom: 1px solid var(--border); box-shadow: 0 10px 20px rgba(0,0,0,0.05); flex-direction: column; gap: 20px; overflow-y: auto; max-height: calc(100vh - 80px); }
    .mobile-menu.open { display: flex; }
    .mobile-group { margin-bottom: 20px; }
    .mobile-group h4 { font-size: 12px; text-transform: uppercase; color: var(--soft); margin-bottom: 12px; letter-spacing: 1px; }
    .mobile-group a { display: block; padding: 8px 0; font-size: 18px; color: var(--text); text-decoration: none; font-weight: 500; }
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
          <summary>Programs <span style="font-size: 10px;">▼</span></summary>
          <div class="dropdown-menu">
            <a href="./courses.php">All Courses</a>
            <a href="./workshops.php">Workshops & Seminars</a>
            <a href="./membership.php">Professional Membership</a>
            <a href="./projects.php">Research Projects</a>
          </div>
        </details>
      </li>

      <li class="nav-dropdown">
        <details>
          <summary>Collaborations <span style="font-size: 10px;">▼</span></summary>
          <div class="dropdown-menu">
            <a href="./collaborations.php">Partnerships</a>
            <a href="./collaboration-form.php">Become a Collaborator</a>
          </div>
        </details>
      </li>

      <li class="nav-dropdown">
        <details>
          <summary>Institute <span style="font-size: 10px;">▼</span></summary>
          <div class="dropdown-menu">
            <a href="./about.php">About Us</a>
            <a href="./team.php">Our Team</a>
            <a href="./accreditation.php">Accreditation</a>
            <a href="./reports.php">Annual Reports</a>
            <a href="./careers.php">Careers</a>
            <a href="./verify.php">Verify Credentials</a>
            <a href="./contact.php">Contact Us</a>
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
    <div class="mobile-group">
      <a href="./index.php">Home</a>
    </div>
    
    <div class="mobile-group">
      <h4>Programs</h4>
      <a href="./courses.php">Courses</a>
      <a href="./workshops.php">Workshops</a>
      <a href="./membership.php">Membership</a>
      <a href="./projects.php">Projects</a>
    </div>

    <div class="mobile-group">
      <h4>Collaborations</h4>
      <a href="./collaborations.php">Partnerships</a>
      <a href="./collaboration-form.php">Become a Collaborator</a>
    </div>

    <div class="mobile-group">
      <h4>Institute</h4>
      <a href="./about.php">About Us</a>
      <a href="./team.php">Our Team</a>
      <a href="./accreditation.php">Accreditation</a>
      <a href="./reports.php">Annual Reports</a>
      <a href="./careers.php">Careers</a>
      <a href="./verify.php">Verify Credentials</a>
      <a href="./contact.php">Contact</a>
    </div>

    <a href="./membership.php" class="btn-primary" style="text-align: center; margin-top: 10px;">Join the Institute</a>
  </div>

  <main>
