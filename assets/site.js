(function () {
  const nav = document.getElementById('navbar');
  if (nav) {
    const onScroll = () => {
      nav.classList.toggle('scrolled', window.scrollY > 20);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  const reveals = document.querySelectorAll('.reveal');
  if (reveals.length) {
    if ('IntersectionObserver' in window) {
      const observer = new IntersectionObserver(
        (entries) => {
          entries.forEach((e, i) => {
            if (e.isIntersecting) {
              setTimeout(() => e.target.classList.add('visible'), i * 60);
              observer.unobserve(e.target);
            }
          });
        },
        { threshold: 0.08 }
      );
      reveals.forEach((r) => observer.observe(r));
    } else {
      reveals.forEach((r) => r.classList.add('visible'));
    }
  }

  window.toggleMenu = function toggleMenu() {
    const menu = document.getElementById('mobileMenu');
    if (menu) menu.classList.toggle('open');
  };

  const membershipForm = document.getElementById('membershipForm');
  if (membershipForm) {
    const tierSelect = document.getElementById('tier');
    if (tierSelect) {
      const url = new URL(window.location.href);
      const tierParam = url.searchParams.get('tier');
      if (tierParam) tierSelect.value = tierParam;
    }

    membershipForm.addEventListener('submit', (e) => {
      e.preventDefault();

      const nameEl = document.getElementById('name');
      const emailEl = document.getElementById('email');
      const phoneEl = document.getElementById('phone');
      const tierEl = document.getElementById('tier');

      const name = nameEl ? nameEl.value : '';
      const email = emailEl ? emailEl.value : '';
      const phone = phoneEl ? phoneEl.value : '';
      const tier = tierEl ? tierEl.value : '';

      const subject = encodeURIComponent(`Membership Application: ${tier} - ${name}`);
      const body = encodeURIComponent(
        `Hello INTM Global,\n\nI am interested in joining as a ${tier}.\n\nDetails:\nName: ${name}\nEmail: ${email}\nPhone: ${phone}\n\nPlease let me know the next steps.\n\nBest regards,\n${name}`
      );

      window.location.href = `mailto:direct@intmglobal.org?subject=${subject}&body=${body}`;
    });
  }
})();
