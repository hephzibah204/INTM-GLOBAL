<?php 
$page_title = "Contact Us | INTM Global";
require_once __DIR__ . '/includes/header.php'; 
$content = get_page_content('contact');
?>

<div class="page-hero">
  <div class="section-label">Get in Touch</div>
  <h1>Contact <em>Us</em></h1>
  <p>Have questions about our programs, memberships, or collaborations? Reach out to our team.</p>
</div>

<section id="contact-section">
  <div class="contact-grid" style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 60px; padding: 80px 5%;">
    <div class="contact-info reveal">
      <h2>Contact <em>Details</em></h2>
      <p style="margin-bottom: 30px; color: var(--mid);">Our administrative team is available Monday – Friday, 9:00 AM – 5:00 PM (WAT).</p>
      
      <div style="display: flex; flex-direction: column; gap: 24px;">
        <div>
          <h4 style="margin-bottom: 4px; color: var(--sage);">Email</h4>
          <p><a href="mailto:<?php echo $content['contact_email'] ?? 'info@intmglobal.org'; ?>" style="color: inherit;"><?php echo $content['contact_email'] ?? 'info@intmglobal.org'; ?></a></p>
        </div>
        <div>
          <h4 style="margin-bottom: 4px; color: var(--sage);">Phone</h4>
          <p><a href="tel:<?php echo $content['contact_phone'] ?? '08036210735'; ?>" style="color: inherit;"><?php echo $content['contact_phone'] ?? '08036210735'; ?></a></p>
        </div>
        <div>
          <h4 style="margin-bottom: 4px; color: var(--sage);">Address</h4>
          <p><?php echo $content['contact_address'] ?? 'Plot 6, Block B, Akinyode Family Layout, Unity Estate Off Akala Expressway, Zionist Estate, New Garage, Orita Challenge, Ibadan , Oyo State'; ?></p>
        </div>
      </div>
    </div>

    <div class="contact-form-container reveal">
      <div class="form-card" style="box-shadow: 0 20px 40px rgba(0,0,0,0.05); padding: 40px; border-radius: 12px; background: white; border: 1px solid var(--border);">
        <form id="contactForm">
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
              <label for="name">Your Name</label>
              <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
              <label for="email">Email Address</label>
              <input type="email" id="email" name="email" required>
            </div>
          </div>
          <div class="form-group">
            <label for="subject">Subject</label>
            <input type="text" id="subject" name="subject" required>
          </div>
          <div class="form-group">
            <label for="message">Message</label>
            <textarea id="message" name="message" style="width: 100%; padding: 12px; border: 1.5px solid var(--border); border-radius: 6px; background: var(--cream); min-height: 150px; font-family: inherit;" required></textarea>
          </div>
          <button type="submit" class="btn-primary" style="width: 100%; border: none; cursor: pointer; margin-top: 10px;">Send Message</button>
        </form>
        <div id="form-message" style="margin-top: 20px; display: none; padding: 15px; border-radius: 6px; text-align: center;"></div>
      </div>
    </div>
  </div>
</section>

<script>
document.getElementById('contactForm').onsubmit = async (e) => {
  e.preventDefault();
  const form = e.target;
  const btn = form.querySelector('button');
  const msg = document.getElementById('form-message');
  
  btn.textContent = 'Sending...';
  btn.disabled = true;

  try {
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    const res = await fetch('./api/submit_contact.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    });

    const resData = await res.json();
    msg.style.display = 'block';
    if (res.ok) {
      msg.textContent = 'Message sent successfully! Our team will get back to you shortly.';
      msg.style.background = 'rgba(90,122,94,0.1)';
      msg.style.color = 'var(--sage-dark)';
      form.reset();
    } else {
      msg.textContent = resData.error || 'Failed to send message.';
      msg.style.background = 'rgba(181,135,74,0.1)';
      msg.style.color = 'var(--earth)';
    }
  } catch (err) {
    msg.style.display = 'block';
    msg.textContent = 'Connection error. Please try again.';
  } finally {
    btn.textContent = 'Send Message';
    btn.disabled = false;
  }
};
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
