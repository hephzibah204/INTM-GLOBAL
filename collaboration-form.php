<?php 
$page_title = "Become a Collaborator | INTM Global";
require_once __DIR__ . '/includes/header.php'; 
?>

<div class="page-hero">
  <h1>Become a <em>Collaborator</em></h1>
  <p>Partner with INTM Global to drive innovation in nutritional science and community health.</p>
</div>

<section id="collab-form-section" style="padding: 60px 5%;">
  <div class="form-card" style="max-width: 700px; margin: 0 auto; background: white; padding: 40px; border-radius: 12px; border: 1px solid var(--border);">
    <form id="collabForm">
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div class="form-group">
          <label>Organisation Name</label>
          <input type="text" name="organisation" required>
        </div>
        <div class="form-group">
          <label>Contact Person</label>
          <input type="text" name="contact_name" required>
        </div>
      </div>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div class="form-group">
          <label>Email Address</label>
          <input type="email" name="email" required>
        </div>
        <div class="form-group">
          <label>Phone Number</label>
          <input type="tel" name="phone">
        </div>
      </div>
      <div class="form-group">
        <label>Collaboration Type</label>
        <select name="collab_type" required>
          <option value="">-- Select Type --</option>
          <option value="Academic">Academic / Research</option>
          <option value="Corporate">Corporate / Industry</option>
          <option value="Government">Government / Policy</option>
          <option value="Healthcare">Healthcare Provider</option>
        </select>
      </div>
      <div class="form-group">
        <label>Partnership Proposal</label>
        <textarea name="message" style="width: 100%; padding: 12px; border: 1.5px solid var(--border); border-radius: 6px; background: var(--cream); min-height: 150px; font-family: inherit;" required></textarea>
      </div>
      <button type="submit" class="btn-primary" style="width: 100%; border: none; cursor: pointer; margin-top: 10px;">Submit Proposal</button>
    </form>
    <div id="form-message" style="margin-top: 20px; display: none; padding: 15px; border-radius: 6px; text-align: center;"></div>
  </div>
</section>

<script>
document.getElementById('collabForm').onsubmit = async (e) => {
  e.preventDefault();
  const form = e.target;
  const btn = form.querySelector('button');
  const msg = document.getElementById('form-message');
  
  btn.textContent = 'Submitting...';
  btn.disabled = true;

  try {
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    const res = await fetch('./api/submit_collaboration.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    });

    const resData = await res.json();
    msg.style.display = 'block';
    if (res.ok) {
      msg.textContent = 'Proposal received! Our team will review and contact you shortly.';
      msg.style.background = 'rgba(90,122,94,0.1)';
      msg.style.color = 'var(--sage-dark)';
      form.reset();
    } else {
      msg.textContent = resData.error || 'Submission failed.';
      msg.style.background = 'rgba(181,135,74,0.1)';
      msg.style.color = 'var(--earth)';
    }
  } catch (err) {
    msg.style.display = 'block';
    msg.textContent = 'Connection error.';
  } finally {
    btn.textContent = 'Submit Proposal';
    btn.disabled = false;
  }
};
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
