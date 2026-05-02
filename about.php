<?php 
$page_title = "About Us | INTM Global";
require_once __DIR__ . '/includes/header.php'; 
$content = get_page_content('about');
?>

<div class="page-hero">
  <div class="section-label">Our Story</div>
  <h1>About the <em>Institute</em></h1>
  <p><?php echo isset($content['about_intro']) ? $content['about_intro'] : 'The Institute of Nutritional Therapy Management (INTM Global) is a premier professional body dedicated to advancing the field of nutritional science.'; ?></p>
</div>

<section id="mission-vision" style="padding: 80px 5%; background: white;">
  <div style="max-width: 1000px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: 80px;">
    <div class="reveal">
      <h2 style="margin-bottom: 20px;">Our <em>Mission</em></h2>
      <p style="color: var(--mid); line-height: 1.8; font-size: 18px;">To provide world-class nutritional education and research that empowers health professionals to deliver evidence-based therapeutic interventions, improving health outcomes across Africa.</p>
    </div>
    <div class="reveal">
      <h2 style="margin-bottom: 20px;">Our <em>Vision</em></h2>
      <p style="color: var(--mid); line-height: 1.8; font-size: 18px;">To be the leading authority in nutritional therapy management, recognised globally for our contribution to science, professional standards, and community health.</p>
    </div>
  </div>
</section>

<section id="values" style="padding: 80px 5%; background: var(--warm-white);">
  <div style="text-align: center; margin-bottom: 60px;" class="reveal">
    <div class="section-label">Core Values</div>
    <h2>What We <em>Stand For</em></h2>
  </div>
  <div class="pillars-grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
    <div class="pillar-card reveal">
      <div class="pillar-icon">⚖️</div>
      <h3>Integrity</h3>
      <p>Upholding the highest ethical standards in education, research, and professional practice.</p>
    </div>
    <div class="pillar-card reveal">
      <div class="pillar-icon">🔬</div>
      <h3>Evidence</h3>
      <p>Ensuring every recommendation and curriculum is backed by rigorous nutritional science.</p>
    </div>
    <div class="pillar-card reveal">
      <div class="pillar-icon">🌍</div>
      <h3>Impact</h3>
      <p>Focusing our efforts on practical interventions that solve real-world health challenges.</p>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
