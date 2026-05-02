  </main>

  <footer>
    <div class="footer-grid">
      <div class="footer-brand">
        <a href="./index.php" class="nav-logo" style="text-decoration:none;">
          <img src="./Images/logo/intmgloballogo.jpg" alt="INTM Logo" style="height: 52px; width: auto; object-fit: contain;">
          <div class="nav-logo-text">
            <strong style="color:white;">INTM</strong>
            <span>Institute of Nutritional Therapy Management</span>
          </div>
        </a>
        <p>Advancing evidence-based nutritional science, professional practice, and community health across Africa and beyond.</p>
      </div>
      <div class="footer-col">
        <h4>Institute</h4>
        <ul>
          <li><a href="./about.php">About INTM</a></li>
          <li><a href="./team.php">Our Team</a></li>
          <li><a href="./accreditation.php">Accreditation</a></li>
          <li><a href="./verify.php">Verify Credentials</a></li>
          <li><a href="./contact.php">Contact</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Programs</h4>
        <ul>
          <li><a href="./courses.php">All Courses</a></li>
          <li><a href="./workshops.php">Workshops</a></li>
          <li><a href="./projects.php">Research</a></li>
          <li><a href="./membership.php">Membership</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Contact</h4>
        <ul>
          <?php 
            $email = isset($global_content['contact_email']) ? $global_content['contact_email'] : 'info@intmglobal.org';
            $phone = isset($global_content['contact_phone']) ? $global_content['contact_phone'] : '08036210735';
            $address = isset($global_content['contact_address']) ? $global_content['contact_address'] : 'Plot 6, Block B, Akinyode Family Layout, Unity Estate Off Akala Expressway, Zionist Estate, New Garage, Orita Challenge, Ibadan , Oyo State';
          ?>
          <li><a href="https://www.google.com/maps?q=<?php echo urlencode($address); ?>" target="_blank" rel="noopener">📍 <?php echo $address; ?></a></li>
          <li><a href="tel:<?php echo $phone; ?>">📞 <?php echo $phone; ?></a></li>
          <li><a href="mailto:<?php echo $email; ?>">✉️ <?php echo $email; ?></a></li>
          <li><a href="https://www.intmglobal.org" target="_blank" rel="noopener">🌐 www.intmglobal.org</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span>© <?php echo date('Y'); ?> Institute of Nutritional Therapy Management. All rights reserved.</span>
      <div class="footer-socials">
        <a href="#" title="Facebook">f</a>
        <a href="#" title="Twitter">𝕏</a>
        <a href="#" title="LinkedIn">in</a>
        <a href="#" title="Instagram">ig</a>
      </div>
    </div>
  </footer>

  <script>
    function toggleMenu() {
      const menu = document.getElementById('mobileMenu');
      const hamburger = document.getElementById('hamburger');
      menu.classList.toggle('open');
      hamburger.classList.toggle('active');
    }

    // Reveal animations
    const reveal = () => {
      const reveals = document.querySelectorAll('.reveal');
      for (let i = 0; i < reveals.length; i++) {
        const windowHeight = window.innerHeight;
        const elementTop = reveals[i].getBoundingClientRect().top;
        const elementVisible = 100;
        if (elementTop < windowHeight - elementVisible) {
          reveals[i].classList.add('visible');
        }
      }
    };
    window.addEventListener('scroll', reveal);
    window.addEventListener('DOMContentLoaded', reveal);
  </script>
</body>
</html>
