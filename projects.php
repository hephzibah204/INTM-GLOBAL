<?php 
$page_title = "Research & Projects | INTM Global";
require_once __DIR__ . '/includes/header.php'; 
?>

<div class="page-hero">
  <h1>Research & <em>Initiatives</em></h1>
  <p>INTM leads and supports a portfolio of impactful nutrition projects across community intervention, clinical research, and public health policy.</p>
</div>

<section id="projects" style="padding: 80px 5%;">
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 40px;">
    <div class="project-card reveal" style="background: white; border: 1px solid var(--border); border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
      <div style="height: 12px; background: var(--sage);"></div>
      <div style="padding: 30px;">
        <div style="font-size: 11px; color: var(--sage); font-weight: 700; text-transform: uppercase; margin-bottom: 12px;">Active Project</div>
        <h3 style="margin-bottom: 16px;">NutriScreen Nigeria</h3>
        <p style="color: var(--mid); line-height: 1.6; font-size: 15px;">Nationwide malnutrition screening programme across 200 primary healthcare centres.</p>
      </div>
    </div>
    
    <div class="project-card reveal" style="background: white; border: 1px solid var(--border); border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
      <div style="height: 12px; background: var(--earth);"></div>
      <div style="padding: 30px;">
        <div style="font-size: 11px; color: var(--earth); font-weight: 700; text-transform: uppercase; margin-bottom: 12px;">Clinical Trial</div>
        <h3 style="margin-bottom: 16px;">Maternal Nutrition Study</h3>
        <p style="color: var(--mid); line-height: 1.6; font-size: 15px;">Examining the impact of micronutrient supplementation on birth outcomes in Lagos.</p>
      </div>
    </div>

    <div class="project-card reveal" style="background: white; border: 1px solid var(--border); border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
      <div style="height: 12px; background: var(--teal);"></div>
      <div style="padding: 30px;">
        <div style="font-size: 11px; color: var(--teal); font-weight: 700; text-transform: uppercase; margin-bottom: 12px;">Ongoing</div>
        <h3 style="margin-bottom: 16px;">Urban Nutrition Atlas</h3>
        <p style="color: var(--mid); line-height: 1.6; font-size: 15px;">Mapping food access and nutritional vulnerability across major Nigerian urban centres.</p>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
