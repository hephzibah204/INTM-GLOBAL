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
  <div class="membership-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 28px; padding: 60px 5%;">
    <!-- Associate -->
    <div class="mem-card reveal" style="border-radius: 12px; padding: 40px 32px; border: 1.5px solid var(--border); background: white; position: relative; overflow: hidden; transition: transform .25s, box-shadow .25s;">
      <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 24px; font-weight: 600; margin-bottom: 8px;">Associate</h3>
      <div class="mem-price" style="font-family: 'Playfair Display', serif; font-size: 2.6rem; font-weight: 700; color: var(--sage-dark); line-height: 1; margin: 20px 0 4px;">₦50,000</div>
      <div class="mem-period" style="font-size: 13px; color: var(--soft); margin-bottom: 28px;">Per Year</div>
      <ul class="mem-features" style="list-style: none; margin-bottom: 32px;">
        <li style="display: flex; align-items: flex-start; gap: 10px; font-size: 14px; padding: 7px 0; border-bottom: 1px solid rgba(0,0,0,0.05); color: var(--mid);">Access to monthly webinars</li>
        <li style="display: flex; align-items: flex-start; gap: 10px; font-size: 14px; padding: 7px 0; border-bottom: 1px solid rgba(0,0,0,0.05); color: var(--mid);">Quarterly newsletter</li>
        <li style="display: flex; align-items: flex-start; gap: 10px; font-size: 14px; padding: 7px 0; border-bottom: 1px solid rgba(0,0,0,0.05); color: var(--mid);">10% discount on workshops</li>
        <li style="display: flex; align-items: flex-start; gap: 10px; font-size: 14px; padding: 7px 0; border-bottom: 1px solid rgba(0,0,0,0.05); color: var(--mid);">Online member directory</li>
      </ul>
      <button class="mem-btn mem-btn-outline" onclick="openModal('Associate')" style="display: block; text-align: center; padding: 13px; border-radius: 6px; font-size: 14px; font-weight: 500; text-decoration: none; transition: .2s; border: 1.5px solid var(--sage); color: var(--sage); background: none; width: 100%; cursor: pointer;">Apply Now</button>
    </div>
    
    <!-- Full Member -->
    <div class="mem-card featured reveal" style="border-radius: 12px; padding: 40px 32px; border: 1.5px solid var(--sage-dark); background: var(--sage-dark); color: white; position: relative; overflow: hidden; transition: transform .25s, box-shadow .25s;">
      <div class="mem-badge" style="position: absolute; top: 20px; right: 20px; background: var(--earth); color: white; font-size: 10px; letter-spacing: 1.5px; text-transform: uppercase; padding: 4px 12px; border-radius: 50px; font-weight: 500;">Most Popular</div>
      <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 24px; font-weight: 600; margin-bottom: 8px;">Full Member</h3>
      <div class="mem-price" style="font-family: 'Playfair Display', serif; font-size: 2.6rem; font-weight: 700; color: var(--earth-light); line-height: 1; margin: 20px 0 4px;">₦120,000</div>
      <div class="mem-period" style="font-size: 13px; color: rgba(255,255,255,0.6); margin-bottom: 28px;">Per Year</div>
      <ul class="mem-features" style="list-style: none; margin-bottom: 32px;">
        <li style="display: flex; align-items: flex-start; gap: 10px; font-size: 14px; padding: 7px 0; border-bottom: 1px solid rgba(255,255,255,0.08); color: rgba(255,255,255,0.8);">All Associate benefits</li>
        <li style="display: flex; align-items: flex-start; gap: 10px; font-size: 14px; padding: 7px 0; border-bottom: 1px solid rgba(255,255,255,0.08); color: rgba(255,255,255,0.8);">Voting rights in AGMs</li>
        <li style="display: flex; align-items: flex-start; gap: 10px; font-size: 14px; padding: 7px 0; border-bottom: 1px solid rgba(255,255,255,0.08); color: rgba(255,255,255,0.8);">Post-nominal title (MINTM)</li>
        <li style="display: flex; align-items: flex-start; gap: 10px; font-size: 14px; padding: 7px 0; border-bottom: 1px solid rgba(255,255,255,0.08); color: rgba(255,255,255,0.8);">Professional Indemnity access</li>
        <li style="display: flex; align-items: flex-start; gap: 10px; font-size: 14px; padding: 7px 0; border-bottom: 1px solid rgba(255,255,255,0.08); color: rgba(255,255,255,0.8);">20% discount on all courses</li>
      </ul>
      <button class="mem-btn mem-btn-solid" onclick="openModal('Full Member')" style="display: block; text-align: center; padding: 13px; border-radius: 6px; font-size: 14px; font-weight: 500; text-decoration: none; transition: .2s; background: var(--earth); color: white; border: none; width: 100%; cursor: pointer;">Apply Now</button>
    </div>

    <!-- Fellow -->
    <div class="mem-card reveal" style="border-radius: 12px; padding: 40px 32px; border: 1.5px solid var(--border); background: white; position: relative; overflow: hidden; transition: transform .25s, box-shadow .25s;">
      <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 24px; font-weight: 600; margin-bottom: 8px;">Fellow</h3>
      <div class="mem-price" style="font-family: 'Playfair Display', serif; font-size: 2.6rem; font-weight: 700; color: var(--sage-dark); line-height: 1; margin: 20px 0 4px;">₦250,000</div>
      <div class="mem-period" style="font-size: 13px; color: var(--soft); margin-bottom: 28px;">Per Year</div>
      <ul class="mem-features" style="list-style: none; margin-bottom: 32px;">
        <li style="display: flex; align-items: flex-start; gap: 10px; font-size: 14px; padding: 7px 0; border-bottom: 1px solid rgba(0,0,0,0.05); color: var(--mid);">All Full Member benefits</li>
        <li style="display: flex; align-items: flex-start; gap: 10px; font-size: 14px; padding: 7px 0; border-bottom: 1px solid rgba(0,0,0,0.05); color: var(--mid);">FINTM title designation</li>
        <li style="display: flex; align-items: flex-start; gap: 10px; font-size: 14px; padding: 7px 0; border-bottom: 1px solid rgba(0,0,0,0.05); color: var(--mid);">Research grant eligibility</li>
        <li style="display: flex; align-items: flex-start; gap: 10px; font-size: 14px; padding: 7px 0; border-bottom: 1px solid rgba(0,0,0,0.05); color: var(--mid);">Mentorship opportunities</li>
        <li style="display: flex; align-items: flex-start; gap: 10px; font-size: 14px; padding: 7px 0; border-bottom: 1px solid rgba(0,0,0,0.05); color: var(--mid);">Advisory board eligibility</li>
      </ul>
      <button class="mem-btn mem-btn-outline" onclick="openModal('Fellow')" style="display: block; text-align: center; padding: 13px; border-radius: 6px; font-size: 14px; font-weight: 500; text-decoration: none; transition: .2s; border: 1.5px solid var(--sage); color: var(--sage); background: none; width: 100%; cursor: pointer;">Apply Now</button>
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
