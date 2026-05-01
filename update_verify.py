import os
import re

with open('verify.html', 'r', encoding='utf-8') as f:
    content = f.read()

verification_html = """<!-- VERIFICATION SECTION -->
<section id="verification" style="min-height: 80vh; padding: 160px 5% 80px; display: flex; align-items: center; justify-content: center; background: var(--warm-white);">
  <div style="background: white; border-radius: 12px; padding: 48px; width: 100%; max-width: 540px; box-shadow: 0 24px 48px var(--shadow);">
    <div style="text-align: center; margin-bottom: 32px;">
      <h2 style="margin-bottom: 8px;">Verify <em>Credentials</em></h2>
      <p style="color: var(--mid); font-size: 15px;">Enter your details to verify your Certificate or Membership status with INTM Global.</p>
    </div>

    <!-- TABS -->
    <div style="display: flex; gap: 10px; margin-bottom: 32px; border-bottom: 2px solid var(--border);">
      <button onclick="switchTab('cert')" id="tab-cert" style="flex: 1; padding: 12px; background: none; border: none; border-bottom: 3px solid var(--sage); color: var(--sage-dark); font-weight: 600; font-family: 'DM Sans', sans-serif; cursor: pointer; transition: 0.3s; font-size: 15px;">Certificate</button>
      <button onclick="switchTab('mem')" id="tab-mem" style="flex: 1; padding: 12px; background: none; border: none; border-bottom: 3px solid transparent; color: var(--soft); font-weight: 600; font-family: 'DM Sans', sans-serif; cursor: pointer; transition: 0.3s; font-size: 15px;">Membership</button>
    </div>

    <!-- CERTIFICATE FORM -->
    <form id="form-cert" onsubmit="verifyCredential(event, 'Certificate')">
      <div class="form-group">
        <label for="cert-name">Full Name</label>
        <input type="text" id="cert-name" required placeholder="e.g. Jane Doe">
      </div>
      <div class="form-group">
        <label for="cert-id">Certificate ID</label>
        <input type="text" id="cert-id" required placeholder="e.g. INTM-CERT-2025-XXXX">
      </div>
      <button type="submit" class="btn-primary" style="width: 100%; margin-top: 16px; border: none; cursor: pointer;">Verify Certificate</button>
    </form>

    <!-- MEMBERSHIP FORM -->
    <form id="form-mem" onsubmit="verifyCredential(event, 'Membership')" style="display: none;">
      <div class="form-group">
        <label for="mem-name">Full Name</label>
        <input type="text" id="mem-name" required placeholder="e.g. Dr. John Smith">
      </div>
      <div class="form-group">
        <label for="mem-id">Membership ID</label>
        <input type="text" id="mem-id" required placeholder="e.g. INTM-MEM-XXXX">
      </div>
      <button type="submit" class="btn-primary" style="width: 100%; margin-top: 16px; border: none; cursor: pointer;">Verify Membership</button>
    </form>

    <!-- RESULT AREA -->
    <div id="verify-result" style="display: none; margin-top: 24px; padding: 16px; border-radius: 8px; text-align: center; font-size: 14px; font-weight: 500;">
    </div>
  </div>
</section>

<script>
  function switchTab(tab) {
    document.getElementById('form-cert').style.display = tab === 'cert' ? 'block' : 'none';
    document.getElementById('form-mem').style.display = tab === 'mem' ? 'block' : 'none';
    
    document.getElementById('tab-cert').style.borderColor = tab === 'cert' ? 'var(--sage)' : 'transparent';
    document.getElementById('tab-cert').style.color = tab === 'cert' ? 'var(--sage-dark)' : 'var(--soft)';
    
    document.getElementById('tab-mem').style.borderColor = tab === 'mem' ? 'var(--sage)' : 'transparent';
    document.getElementById('tab-mem').style.color = tab === 'mem' ? 'var(--sage-dark)' : 'var(--soft)';
    
    document.getElementById('verify-result').style.display = 'none';
  }

  function verifyCredential(e, type) {
    e.preventDefault();
    const resultBox = document.getElementById('verify-result');
    resultBox.style.display = 'block';
    
    resultBox.style.background = 'var(--cream)';
    resultBox.style.color = 'var(--mid)';
    resultBox.innerHTML = 'Checking database...';
    
    setTimeout(() => {
      resultBox.style.background = 'rgba(181,135,74,0.1)';
      resultBox.style.color = '#8a5f1e';
      resultBox.innerHTML = `⚠️ Our digital verification portal is currently undergoing maintenance. <br><br>While we connect our Supabase database, please email <b>direct@intmglobal.org</b> with your ${type} ID to verify its authenticity.`;
    }, 1500);
  }
</script>"""

new_content = re.sub(r'<!-- HOME / HERO -->.*?<!-- FOOTER -->', verification_html + '\n<!-- FOOTER -->', content, flags=re.DOTALL)

with open('verify.html', 'w', encoding='utf-8') as f:
    f.write(new_content)
print("Updated verify.html")
