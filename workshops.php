<?php 
$page_title = "Workshops & Seminars | INTM Global";
require_once __DIR__ . '/includes/header.php'; 
?>

<div class="page-hero">
  <div class="section-label">Professional Development</div>
  <h1>Workshops & <em>Seminars</em></h1>
  <p>Evidence-based continuing education sessions designed for healthcare practitioners, researchers, and nutrition enthusiasts.</p>
</div>

<section id="workshops">
  <div class="workshop-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 32px; padding: 60px 5%;">
    <div class="workshop-card reveal" style="background: white; border: 1px solid var(--border); border-radius: 12px; overflow: hidden; display: flex; flex-direction: column;">
      <div style="height: 200px; background: var(--sage); display: flex; align-items: center; justify-content: center; font-size: 60px;">🧫</div>
      <div style="padding: 30px; flex-grow: 1; display: flex; flex-direction: column;">
        <div style="font-size: 14px; color: var(--sage); font-weight: 600; margin-bottom: 10px;">Clinical Seminar · Upcoming</div>
        <h3 style="margin-bottom: 15px;">Gut Microbiome & Nutritional Therapy</h3>
        <p style="color: var(--mid); margin-bottom: 24px; line-height: 1.6;">An in-depth exploration of the gut-brain axis and clinical protocols for microbiome restoration through diet.</p>
        <div style="margin-top: auto; display: flex; align-items: center; justify-content: space-between;">
          <span style="font-size: 14px; color: var(--soft);">Date: 15 June 2025</span>
          <a href="./workshop-reg.php?event=Gut Microbiome" class="btn-primary" style="padding: 8px 20px; font-size: 14px;">Register</a>
        </div>
      </div>
    </div>

    <div class="workshop-card reveal" style="background: white; border: 1px solid var(--border); border-radius: 12px; overflow: hidden; display: flex; flex-direction: column;">
      <div style="height: 200px; background: var(--earth); display: flex; align-items: center; justify-content: center; font-size: 60px;">🩸</div>
      <div style="padding: 30px; flex-grow: 1; display: flex; flex-direction: column;">
        <div style="font-size: 14px; color: var(--earth); font-weight: 600; margin-bottom: 10px;">Specialist Workshop · Upcoming</div>
        <h3 style="margin-bottom: 15px;">Nutritional Management of Diabetes</h3>
        <p style="color: var(--mid); margin-bottom: 24px; line-height: 1.6;">Evidence-based dietary interventions for Type 2 Diabetes reversal and management in sub-Saharan populations.</p>
        <div style="margin-top: auto; display: flex; align-items: center; justify-content: space-between;">
          <span style="font-size: 14px; color: var(--soft);">Date: 22 July 2025</span>
          <a href="./workshop-reg.php?event=Diabetes" class="btn-primary" style="padding: 8px 20px; font-size: 14px;">Register</a>
        </div>
      </div>
    </div>

    <div class="workshop-card reveal" style="background: white; border: 1px solid var(--border); border-radius: 12px; overflow: hidden; display: flex; flex-direction: column;">
      <div style="height: 200px; background: var(--teal); display: flex; align-items: center; justify-content: center; font-size: 60px;">🍼</div>
      <div style="padding: 30px; flex-grow: 1; display: flex; flex-direction: column;">
        <div style="font-size: 14px; color: var(--teal); font-weight: 600; margin-bottom: 10px;">Public Health · Seminar</div>
        <h3 style="margin-bottom: 15px;">Paediatric Malnutrition</h3>
        <p style="color: var(--mid); margin-bottom: 24px; line-height: 1.6;">Addressing the double burden of malnutrition in children: Strategies for community and clinical intervention.</p>
        <div style="margin-top: auto; display: flex; align-items: center; justify-content: space-between;">
          <span style="font-size: 14px; color: var(--soft);">Date: 10 Aug 2025</span>
          <a href="./workshop-reg.php?event=Paediatric" class="btn-primary" style="padding: 8px 20px; font-size: 14px;">Register</a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
