<?php 
$page_title = "Verify Credentials | INTM Global";
require_once __DIR__ . '/includes/header.php'; 
?>

<div class="page-hero">
  <div class="section-label">Integrity & Standards</div>
  <h1>Verify <em>Credentials</em></h1>
  <p>Our public registry allows you to verify the authenticity of INTM certificates and professional membership status.</p>
</div>

<section id="verify-tabs" style="padding: 60px 5%;">
  <div class="tabs-container" style="max-width: 800px; margin: 0 auto;">
    <div class="tab-buttons" style="display: flex; gap: 10px; border-bottom: 1.5px solid var(--border); margin-bottom: 30px;">
      <button class="tab-btn active" data-tab="certificate" onclick="switchTab(this, 'certificate')" style="padding: 12px 24px; background: none; border: none; font-family: inherit; font-weight: 500; cursor: pointer; border-bottom: 3px solid transparent;">Certificate Verification</button>
      <button class="tab-btn" data-tab="membership" onclick="switchTab(this, 'membership')" style="padding: 12px 24px; background: none; border: none; font-family: inherit; font-weight: 500; cursor: pointer; border-bottom: 3px solid transparent;">Membership Status</button>
    </div>

    <div class="tab-content active" id="certificate-tab" style="display: block;">
      <div class="form-card" style="background: white; padding: 30px; border-radius: 12px; border: 1px solid var(--border);">
        <h3>Verify a <em>Certificate</em></h3>
        <p style="margin-bottom: 24px; color: var(--mid); font-size: 14px;">Enter the unique Certificate ID found at the bottom of the issued document.</p>
        <form id="certForm">
          <div class="form-group">
            <label>Certificate ID</label>
            <input type="text" name="credential_id" placeholder="e.g. INTM-CERT-2025-001" required>
          </div>
          <button type="submit" class="btn-primary" style="width: 100%; border: none; cursor: pointer;">Verify Certificate</button>
        </form>
      </div>
    </div>

    <div class="tab-content" id="membership-tab" style="display: none;">
      <div class="form-card" style="background: white; padding: 30px; border-radius: 12px; border: 1px solid var(--border);">
        <h3>Verify <em>Membership</em></h3>
        <p style="margin-bottom: 24px; color: var(--mid); font-size: 14px;">Verify the professional standing of an INTM member using their Member ID.</p>
        <form id="memberVerifyForm">
          <div class="form-group">
            <label>Member ID</label>
            <input type="text" name="credential_id" placeholder="e.g. INTM-MEM-12345" required>
          </div>
          <button type="submit" class="btn-primary" style="width: 100%; border: none; cursor: pointer;">Check Membership Status</button>
        </form>
      </div>
    </div>

    <div id="verify-result" style="margin-top: 30px; display: none; padding: 24px; border-radius: 12px; text-align: center; border: 1px solid var(--border);">
    </div>
  </div>
</section>

<script>
function switchTab(btn, tabId) {
  document.querySelectorAll('.tab-btn').forEach(b => {
    b.classList.remove('active');
    b.style.borderBottomColor = 'transparent';
    b.style.color = 'var(--soft)';
  });
  btn.classList.add('active');
  btn.style.borderBottomColor = 'var(--sage)';
  btn.style.color = 'var(--sage-dark)';

  document.querySelectorAll('.tab-content').forEach(c => c.style.display = 'none');
  document.getElementById(tabId + '-tab').style.display = 'block';
  document.getElementById('verify-result').style.display = 'none';
}

document.querySelectorAll('form').forEach(form => {
  form.onsubmit = async (e) => {
    e.preventDefault();
    const resultBox = document.getElementById('verify-result');
    const type = form.id === 'certForm' ? 'Certificate' : 'Membership';
    const id = form.querySelector('[name="credential_id"]').value;
    
    resultBox.style.display = 'block';
    resultBox.innerHTML = 'Verifying...';
    resultBox.style.background = 'var(--cream)';

    try {
      const res = await fetch('./api/verify.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ credential_id: id, type: type })
      });
      const data = await res.json();
      
      if (data.valid) {
        resultBox.style.background = 'rgba(90,122,94,0.1)';
        resultBox.style.borderColor = 'var(--sage)';
        resultBox.innerHTML = `<h4 style="color:var(--sage-dark)">✅ Valid Credential</h4><p>${data.message}</p>`;
      } else {
        resultBox.style.background = 'rgba(181,135,74,0.1)';
        resultBox.style.borderColor = 'var(--earth)';
        resultBox.innerHTML = `<h4 style="color:var(--earth)">❌ Verification Failed</h4><p>${data.message}</p>`;
      }
    } catch (err) {
      resultBox.innerHTML = 'An error occurred during verification.';
    }
  };
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
