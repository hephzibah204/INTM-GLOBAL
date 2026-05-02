<?php 
$page_title = "Collaborations | INTM Global";
require_once __DIR__ . '/includes/header.php'; 
?>

<div class="page-hero">
  <h1>Strategic <em>Collaborations</em></h1>
  <p>We work with academic institutions, healthcare providers, and industry partners to amplify the impact of nutritional therapy.</p>
</div>

<section id="collaborations" style="padding: 80px 5%;">
  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center;">
    <div class="reveal">
      <div class="section-label">Partnerships</div>
      <h2>Strength in <em>Unity</em></h2>
      <p style="color: var(--mid); line-height: 1.8; margin-top: 20px;">INTM Global actively seeks partnerships that align with our mission to advance evidence-based nutrition. We collaborate on research, curriculum development, and community outreach.</p>
      <a href="./collaboration-form.php" class="btn-primary" style="display: inline-block; margin-top: 32px;">Become a Collaborator</a>
    </div>
    
    <div class="reveal" style="display: grid; gap: 20px;">
      <div style="padding: 40px; background: white; border: 1px solid var(--border); border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
        <img src="./Images/partners/ifsqm.png" alt="IFSQM" style="max-height: 100px; width: auto;">
      </div>
      <div style="padding: 40px; background: white; border: 1px solid var(--border); border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
        <img src="./Images/partners/collegeoffoodsafety.png" alt="College of Food Safety" style="max-height: 100px; width: auto;">
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
