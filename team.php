<?php 
$page_title = "Our Team | INTM Global";
require_once __DIR__ . '/includes/header.php'; 
?>

<div class="page-hero">
  <div class="section-label">Leadership & Faculty</div>
  <h1>Our <em>Team</em></h1>
  <p>Meet the dedicated professionals and educators driving excellence in nutritional therapy management.</p>
</div>

<section id="team" style="padding: 80px 5%;">
  <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 40px;">
    <!-- Team Member 1 -->
    <div class="team-card reveal" style="text-align: center;">
      <div style="width: 200px; height: 200px; border-radius: 50%; background: var(--warm-white); margin: 0 auto 24px; display: flex; align-items: center; justify-content: center; font-size: 80px; overflow: hidden; border: 1px solid var(--border);">👨‍🔬</div>
      <h3>Prof. John Doe</h3>
      <p style="color: var(--sage); font-weight: 600; font-size: 14px; margin-bottom: 12px;">Executive Director</p>
      <p style="color: var(--mid); font-size: 14px; line-height: 1.6;">Lead researcher in clinical nutrition with over 20 years of experience in public health policy.</p>
    </div>
    
    <!-- Team Member 2 -->
    <div class="team-card reveal" style="text-align: center;">
      <div style="width: 200px; height: 200px; border-radius: 50%; background: var(--warm-white); margin: 0 auto 24px; display: flex; align-items: center; justify-content: center; font-size: 80px; overflow: hidden; border: 1px solid var(--border);">👩‍🏫</div>
      <h3>Dr. Jane Smith</h3>
      <p style="color: var(--sage); font-weight: 600; font-size: 14px; margin-bottom: 12px;">Director of Education</p>
      <p style="color: var(--mid); font-size: 14px; line-height: 1.6;">Specialist in paediatric nutrition and curriculum development for professional certification.</p>
    </div>

    <!-- Team Member 3 -->
    <div class="team-card reveal" style="text-align: center;">
      <div style="width: 200px; height: 200px; border-radius: 50%; background: var(--warm-white); margin: 0 auto 24px; display: flex; align-items: center; justify-content: center; font-size: 80px; overflow: hidden; border: 1px solid var(--border);">👨‍💼</div>
      <h3>Mr. James Brown</h3>
      <p style="color: var(--sage); font-weight: 600; font-size: 14px; margin-bottom: 12px;">Head of Partnerships</p>
      <p style="color: var(--mid); font-size: 14px; line-height: 1.6;">Managing strategic collaborations with international health bodies and academic institutions.</p>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
