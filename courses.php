<?php 
$page_title = "Courses & Training | INTM Global";
require_once __DIR__ . '/includes/header.php'; 
?>

<div class="page-hero">
  <h1>Courses &amp; <em>Training</em></h1>
  <p>Programs designed to build practical skills, recognised credentials, and clinical confidence across foundational and advanced nutritional therapy.</p>
</div>

<section id="courses" style="padding: 60px 5%;">
  <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:40px;" class="reveal">
    <div>
      <div class="section-label">Our Curriculum</div>
      <h2>Programs Designed for <em>Real Impact</em></h2>
    </div>
    <a href="./contact.php" class="btn-secondary">Request Prospectus →</a>
  </div>
  <div class="course-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px;">
    <div class="course-card reveal" style="border:1px solid var(--border); border-radius:12px; overflow:hidden;">
      <div style="height:180px; background:var(--sage); display:flex; align-items:center; justify-content:center; font-size:40px;">🥗</div>
      <div style="padding:24px;">
        <div style="font-size:12px; color:var(--sage); font-weight:600; margin-bottom:8px;">Certificate · Beginner</div>
        <h3>Foundations of Nutritional Therapy</h3>
        <p style="color:var(--mid); margin-bottom:20px; font-size:14px;">An essential introduction to nutritional science and therapeutic diet planning.</p>
        <span style="font-size:13px; color:var(--soft);">Duration: 12 weeks</span>
      </div>
    </div>
    <div class="course-card reveal" style="border:1px solid var(--border); border-radius:12px; overflow:hidden;">
      <div style="height:180px; background:var(--earth); display:flex; align-items:center; justify-content:center; font-size:40px;">🩺</div>
      <div style="padding:24px;">
        <div style="font-size:12px; color:var(--earth); font-weight:600; margin-bottom:8px;">Diploma · Intermediate</div>
        <h3>Clinical Nutritional Therapy</h3>
        <p style="color:var(--mid); margin-bottom:20px; font-size:14px;">Advanced clinical assessment and management of chronic diseases through nutrition.</p>
        <span style="font-size:13px; color:var(--soft);">Duration: 6 months</span>
      </div>
    </div>
    <div class="course-card reveal" style="border:1px solid var(--border); border-radius:12px; overflow:hidden;">
      <div style="height:180px; background:var(--teal); display:flex; align-items:center; justify-content:center; font-size:40px;">🧬</div>
      <div style="padding:24px;">
        <div style="font-size:12px; color:var(--teal); font-weight:600; margin-bottom:8px;">Certificate · Advanced</div>
        <h3>Nutrigenomics</h3>
        <p style="color:var(--mid); margin-bottom:20px; font-size:14px;">Explore the intersection of genetics and personalised nutrition protocols.</p>
        <span style="font-size:13px; color:var(--soft);">Duration: 10 weeks</span>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
