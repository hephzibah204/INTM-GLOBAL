<?php 
$page_title = "INTM Global | Nutritional Therapy Management";
require_once __DIR__ . '/includes/header.php'; 
$content = get_page_content('home');
?>

<section class="hero">
  <div class="hero-bg"></div>
  <div class="hero-content">
    <div class="section-label" style="color:var(--sage); margin-bottom:15px; background:rgba(255,255,255,0.1); border-color:rgba(255,255,255,0.2);">Evidence-Based Excellence</div>
    <h1><?php echo isset($content['home_hero_title']) ? $content['home_hero_title'] : 'Nourish Knowledge.<br><em>Transform Lives.</em>'; ?></h1>
    <p><?php echo isset($content['home_hero_sub']) ? $content['home_hero_sub'] : 'The Institute of Nutritional Therapy Management (INTM) is a leading centre for evidence-based nutritional education, professional development, and transformative research across Africa and beyond.'; ?></p>
    <div class="hero-actions">
      <a href="./courses.php" class="btn-primary">Explore Programs</a>
      <a href="./about.php" class="btn-outline">Our Mission</a>
    </div>
  </div>
</section>

<section id="stats">
  <div class="stats-grid">
    <div class="stat-item reveal">
      <div class="stat-num">5000+</div>
      <div class="stat-label">Professionals Trained</div>
    </div>
    <div class="stat-item reveal">
      <div class="stat-num">25+</div>
      <div class="stat-label">Accredited Courses</div>
    </div>
    <div class="stat-item reveal">
      <div class="stat-num">12</div>
      <div class="stat-label">African Countries</div>
    </div>
    <div class="stat-item reveal">
      <div class="stat-num">100%</div>
      <div class="stat-label">Evidence-Based</div>
    </div>
  </div>
</section>

<section id="pillars">
  <div class="pillars-header reveal">
    <div class="section-label">Our Foundation</div>
    <h2>The <em>Three Pillars</em> of INTM</h2>
  </div>
  <div class="pillars-grid">
    <div class="pillar-card reveal">
      <div class="pillar-icon">🎓</div>
      <h3>Education & Training</h3>
      <p>From certificate programs to advanced diplomas, we provide rigorous training for health practitioners at every career stage.</p>
      <a href="./courses.php" class="pillar-link">View Curriculum →</a>
    </div>
    <div class="pillar-card reveal">
      <div class="pillar-icon">🧬</div>
      <h3>Research & Innovation</h3>
      <p>We lead translational nutrition research focused on addressing the specific health challenges of African populations.</p>
      <a href="./projects.php" class="pillar-link">Our Projects →</a>
    </div>
    <div class="pillar-card reveal">
      <div class="pillar-icon">🤝</div>
      <h3>Advocacy & Membership</h3>
      <p>Building a professional community and advocating for the integration of nutritional therapy into mainstream healthcare.</p>
      <a href="./membership.php" class="pillar-link">Join the Institute →</a>
    </div>
  </div>
</section>

<div class="cta-banner reveal">
  <div class="cta-content">
    <h2>Ready to advance your <em>professional practice?</em></h2>
    <p>Join a community of dedicated health professionals committed to the science of nutritional therapy.</p>
  </div>
  <div class="cta-actions">
    <a href="./membership.php" class="btn-outline-white">Become a Member</a>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
