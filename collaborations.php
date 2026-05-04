<?php 
$page_title = "Collaborations | INTM Global";
require_once __DIR__ . '/includes/header.php'; 
?>

<div class="page-hero">
  <h1>Strategic <em>Collaborations</em></h1>
  <p>We work with academic institutions, healthcare providers, and industry partners to amplify the impact of nutritional therapy.</p>
</div>

<section id="collaborations" style="padding: 80px 5%;">
  <div class="collab-layout" style="display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: start; padding: 60px 5%;">
    <div class="collab-content reveal">
      <div class="section-label">Partnerships</div>
      <h2>Strength in <em>Unity</em></h2>
      <p style="color: var(--mid); font-size: 16px; line-height: 1.8; margin-top: 20px;">INTM Global actively seeks partnerships that align with our mission to advance evidence-based nutrition. We collaborate on research, curriculum development, and community outreach.</p>
      
      <div class="collab-types" style="margin-top: 40px; display: flex; flex-direction: column; gap: 16px;">
        <div class="collab-type" style="display: flex; gap: 16px; align-items: flex-start; background: white; padding: 20px 24px; border-radius: 8px; border-left: 3px solid var(--sage); box-shadow: 0 2px 12px var(--shadow);">
          <div style="font-size: 24px;">🎓</div>
          <div>
            <h4 style="font-size: 15px; font-weight: 500; color: var(--charcoal); margin-bottom: 4px;">Academic Institutions</h4>
            <p style="font-size: 13px; color: var(--soft); margin: 0; line-height: 1.55;">Joint research projects, student placements, and curriculum accreditation.</p>
          </div>
        </div>
        <div class="collab-type" style="display: flex; gap: 16px; align-items: flex-start; background: white; padding: 20px 24px; border-radius: 8px; border-left: 3px solid var(--earth); box-shadow: 0 2px 12px var(--shadow);">
          <div style="font-size: 24px;">🏢</div>
          <div>
            <h4 style="font-size: 15px; font-weight: 500; color: var(--charcoal); margin-bottom: 4px;">Industry Partners</h4>
            <p style="font-size: 13px; color: var(--soft); margin: 0; line-height: 1.55;">Corporate wellness programmes and food quality standard advocacy.</p>
          </div>
        </div>
        <div class="collab-type" style="display: flex; gap: 16px; align-items: flex-start; background: white; padding: 20px 24px; border-radius: 8px; border-left: 3px solid var(--teal); box-shadow: 0 2px 12px var(--shadow);">
          <div style="font-size: 24px;">⚖️</div>
          <div>
            <h4 style="font-size: 15px; font-weight: 500; color: var(--charcoal); margin-bottom: 4px;">Government & Policy</h4>
            <p style="font-size: 13px; color: var(--soft); margin: 0; line-height: 1.55;">Technical advisory roles for nutritional guidelines and public health policy.</p>
          </div>
        </div>
      </div>

      <a href="./collaboration-form.php" class="btn-primary" style="display: inline-block; margin-top: 32px;">Become a Collaborator</a>
    </div>
    
    <div class="partner-logos reveal" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
      <div class="partner-logo-card" style="background: white; border: 1px solid var(--border); border-radius: 10px; padding: 28px 20px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px; text-align: center; min-height: 130px; transition: box-shadow .2s;">
        <img src="./Images/partners/ifsqm.png" alt="IFSQM" style="max-height: 60px; width: auto; object-fit: contain;">
        <p style="font-size: 12px; color: var(--soft); font-weight: 500; letter-spacing: .3px;">IFSQM</p>
      </div>
      <div class="partner-logo-card" style="background: white; border: 1px solid var(--border); border-radius: 10px; padding: 28px 20px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px; text-align: center; min-height: 130px; transition: box-shadow .2s;">
        <img src="./Images/partners/collegeoffoodsafety.png" alt="College of Food Safety" style="max-height: 60px; width: auto; object-fit: contain;">
        <p style="font-size: 12px; color: var(--soft); font-weight: 500; letter-spacing: .3px;">College of Food Safety</p>
      </div>
      <div class="partner-logo-card" style="background: white; border: 1px solid var(--border); border-radius: 10px; padding: 28px 20px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px; text-align: center; min-height: 130px; transition: box-shadow .2s;">
        <div style="font-size: 32px;">🏥</div>
        <p style="font-size: 12px; color: var(--soft); font-weight: 500; letter-spacing: .3px;">Healthcare Network Africa</p>
      </div>
      <div class="partner-logo-card" style="background: white; border: 1px solid var(--border); border-radius: 10px; padding: 28px 20px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px; text-align: center; min-height: 130px; transition: box-shadow .2s;">
        <div style="font-size: 32px;">🔬</div>
        <p style="font-size: 12px; color: var(--soft); font-weight: 500; letter-spacing: .3px;">NutriLab International</p>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
