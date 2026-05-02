<?php 
$page_title = "Membership | INTM Global";
require_once __DIR__ . '/includes/header.php'; 
?>

<div class="page-hero">
  <div class="section-label">Professional Community</div>
  <h1>Membership <em>Tiers</em></h1>
  <p>Join the Institute of Nutritional Therapy Management and connect with a network of professionals dedicated to advancing nutritional science across Africa.</p>
</div>

<section id="tiers">
  <div class="tier-grid">
    <div class="tier-card reveal">
      <div class="tier-header">
        <h3>Associate</h3>
        <p>Entry-level membership for students and newly qualified practitioners.</p>
      </div>
      <ul class="tier-features">
        <li>Access to monthly webinars</li>
        <li>Quarterly newsletter</li>
        <li>10% discount on workshops</li>
        <li>Online member directory</li>
      </ul>
      <button class="btn-primary" onclick="openModal('Associate')">Apply Now</button>
    </div>
    
    <div class="tier-card featured reveal">
      <div class="tier-header">
        <h3>Full Member</h3>
        <p>For established practitioners with verified credentials and experience.</p>
      </div>
      <ul class="tier-features">
        <li>All Associate benefits</li>
        <li>Voting rights in AGMs</li>
        <li>Post-nominal title (MINTM)</li>
        <li>Professional Indemnity access</li>
        <li>20% discount on all courses</li>
      </ul>
      <button class="btn-primary" onclick="openModal('Full Member')">Apply Now</button>
    </div>

    <div class="tier-card reveal">
      <div class="tier-header">
        <h3>Fellow</h3>
        <p>By invitation or application for distinguished contribution to the field.</p>
      </div>
      <ul class="tier-features">
        <li>All Full Member benefits</li>
        <li>FINTM title designation</li>
        <li>Research grant eligibility</li>
        <li>Mentorship opportunities</li>
        <li>Advisory board eligibility</li>
      </ul>
      <button class="btn-primary" onclick="openModal('Fellow')">Apply Now</button>
    </div>
  </div>
</section>

<!-- Membership Application Modal -->
<div class="modal-overlay" id="membershipModal">
  <div class="modal-content">
    <span class="close-btn" onclick="closeModal()">&times;</span>
    <h3>Membership Application</h3>
    <p id="selected-tier-text">Applying for: <strong>Associate</strong></p>
    
    <form id="membershipForm" enctype="multipart/form-data">
      <input type="hidden" id="tier" name="tier">
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
        <div class="form-group">
          <label for="name">Full Name</label>
          <input type="text" id="name" name="name" required>
        </div>
        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email" required>
        </div>
      </div>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
        <div class="form-group">
          <label for="phone">Phone Number</label>
          <input type="tel" id="phone" name="phone">
        </div>
        <div class="form-group">
          <label for="role">Current Role / Organisation</label>
          <input type="text" id="role" name="role" placeholder="e.g. Clinical Nutritionist">
        </div>
      </div>
      <div class="form-group">
        <label for="qualifications">Professional Qualifications</label>
        <input type="text" id="qualifications" name="qualifications" placeholder="e.g. BSc Nutrition, RDN">
      </div>
      <div class="form-group">
        <label for="cv">Upload CV (PDF/DOCX) *</label>
        <input type="file" id="cv" name="cv" accept=".pdf,.docx,.doc" required style="padding: 8px 0; background: transparent; border: none;">
      </div>
      <button type="submit" class="btn-primary" style="width: 100%; border: none; cursor: pointer; margin-top: 10px;">Submit Application</button>
    </form>
    <div id="form-message" style="margin-top: 16px; display: none; padding: 15px; border-radius: 6px; text-align: center;"></div>
  </div>
</div>

<script>
function openModal(tier) {
  document.getElementById('tier').value = tier;
  document.getElementById('selected-tier-text').innerHTML = `Applying for: <strong>${tier}</strong>`;
  document.getElementById('membershipModal').classList.add('active');
}

function closeModal() {
  document.getElementById('membershipModal').classList.remove('active');
}

document.getElementById('membershipForm').onsubmit = async (e) => {
  e.preventDefault();
  const form = e.target;
  const btn = form.querySelector('button');
  const msg = document.getElementById('form-message');
  
  btn.textContent = 'Submitting...';
  btn.disabled = true;

  try {
    const formData = new FormData(form);
    const res = await fetch('./api/submit_membership.php', {
      method: 'POST',
      body: formData
    });

    const data = await res.json();
    msg.style.display = 'block';
    if (res.ok) {
      msg.textContent = 'Application received! We will review your CV and contact you shortly.';
      msg.style.background = 'rgba(90,122,94,0.1)';
      msg.style.color = 'var(--sage-dark)';
      form.reset();
      setTimeout(closeModal, 3000);
    } else {
      msg.textContent = data.error || 'Submission failed.';
      msg.style.background = 'rgba(181,135,74,0.1)';
      msg.style.color = 'var(--earth)';
    }
  } catch (err) {
    msg.style.display = 'block';
    msg.textContent = 'Connection error. Please try again.';
  } finally {
    btn.textContent = 'Submit Application';
    btn.disabled = false;
  }
};
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
