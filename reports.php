<?php 
$page_title = "Annual Reports | INTM Global";
require_once __DIR__ . '/includes/header.php'; 
?>

<div class="page-hero">
  <div class="section-label">Transparency</div>
  <h1>Annual <em>Reports</em></h1>
  <p>Our commitment to transparency and accountability. Review our progress, impact, and financial highlights from recent years.</p>
</div>

<section id="reports" style="padding: 80px 5%;">
  <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px;">
    <!-- Report 2024 -->
    <div class="reveal" style="padding: 40px; background: white; border: 1px solid var(--border); border-radius: 12px; text-align: center;">
      <div style="font-size: 50px; margin-bottom: 20px;">📊</div>
      <h3>2024 Annual Report</h3>
      <p style="color: var(--soft); margin-bottom: 24px; font-size: 14px;">Impact metrics, research highlights, and institutional growth.</p>
      <a href="#" class="btn-outline" style="font-size: 14px;">Download PDF</a>
    </div>

    <!-- Report 2023 -->
    <div class="reveal" style="padding: 40px; background: white; border: 1px solid var(--border); border-radius: 12px; text-align: center;">
      <div style="font-size: 50px; margin-bottom: 20px;">📈</div>
      <h3>2023 Annual Report</h3>
      <p style="color: var(--soft); margin-bottom: 24px; font-size: 14px;">A year of expansion and new professional certifications.</p>
      <a href="#" class="btn-outline" style="font-size: 14px;">Download PDF</a>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
