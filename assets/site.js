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

  const navDetails = Array.from(document.querySelectorAll('.nav-dropdown details'));
  if (navDetails.length) {
    navDetails.forEach((d) => d.removeAttribute('open'));

    navDetails.forEach((d) => {
      d.addEventListener('toggle', () => {
        if (!d.open) return;
        navDetails.forEach((other) => {
          if (other !== d) other.removeAttribute('open');
        });
      });

      const links = d.querySelectorAll('a');
      links.forEach((a) => {
        a.addEventListener('click', () => d.removeAttribute('open'));
      });
    });

    document.addEventListener('click', (e) => {
      if (e.target.closest('.nav-dropdown')) return;
      navDetails.forEach((d) => d.removeAttribute('open'));
    });

    document.addEventListener('keydown', (e) => {
      if (e.key !== 'Escape') return;
      navDetails.forEach((d) => d.removeAttribute('open'));
    });
  }

  const setBoxMessage = (box, ok, text) => {
    if (!box) return;
    box.style.display = 'block';
    box.textContent = text;
    box.style.background = ok ? 'rgba(90,122,94,0.1)' : 'rgba(181,135,74,0.1)';
    box.style.color = ok ? 'var(--sage-dark)' : 'var(--earth)';
    box.style.borderColor = ok ? 'var(--sage)' : 'var(--earth)';
  };

  const postJson = async (url, data) => {
    const res = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data),
    });
    let json = null;
    try {
      json = await res.json();
    } catch (_) {
      json = null;
    }
    return { res, json };
  };

  const getUrlParam = (key) => {
    try {
      return new URL(window.location.href).searchParams.get(key);
    } catch (_) {
      return null;
    }
  };

  const membershipModal = document.getElementById('membershipModal');
  const tierInput = document.getElementById('tier');
  const tierText = document.getElementById('selected-tier-text');

  const openMembership = (tier) => {
    if (!membershipModal) return;
    if (tierInput) tierInput.value = tier || '';
    if (tierText && tier) tierText.innerHTML = `Applying for: <strong>${tier}</strong>`;
    membershipModal.classList.add('active');
  };
  const closeMembership = () => {
    if (!membershipModal) return;
    membershipModal.classList.remove('active');
  };

  window.openModal = openMembership;
  window.closeModal = closeMembership;

  document.addEventListener('click', (e) => {
    const openBtn = e.target.closest('[data-open-membership]');
    if (openBtn) {
      const tier = openBtn.getAttribute('data-open-membership') || '';
      openMembership(tier);
      return;
    }

    const closeBtn = e.target.closest('[data-close-membership]');
    if (closeBtn) {
      closeMembership();
      return;
    }

    if (membershipModal && e.target === membershipModal) {
      closeMembership();
    }
  });

  const membershipForm = document.getElementById('membershipForm');
  if (membershipForm) {
    const tierParam = getUrlParam('tier');
    if (tierParam && tierInput) tierInput.value = tierParam;

    membershipForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const btn = membershipForm.querySelector('button[type="submit"]');
      const msgBox =
        membershipForm.closest('.modal-content')?.querySelector('#form-message') ||
        document.getElementById('form-message');

      if (btn) {
        btn.disabled = true;
        btn.textContent = 'Submitting...';
      }

      try {
        const formData = new FormData(membershipForm);
        const res = await fetch('./api/submit_membership.php', { method: 'POST', body: formData });
        const data = await res.json().catch(() => ({}));
        if (res.ok) {
          setBoxMessage(msgBox, true, 'Application received! We will review your submission and contact you shortly.');
          membershipForm.reset();
          setTimeout(closeMembership, 900);
        } else {
          setBoxMessage(msgBox, false, data.error || 'Submission failed.');
        }
      } catch (_) {
        setBoxMessage(msgBox, false, 'Connection error. Please try again.');
      } finally {
        if (btn) {
          btn.disabled = false;
          btn.textContent = 'Submit Application';
        }
      }
    });
  }

  const contactForm = document.getElementById('contactForm');
  if (contactForm) {
    contactForm.addEventListener('submit', async (e) => {
      e.preventDefault();

      const btn = contactForm.querySelector('button[type="submit"]');
      const msgBox = document.getElementById('form-message');

      if (btn) {
        btn.disabled = true;
        btn.textContent = 'Sending...';
      }

      try {
        const formData = new FormData(contactForm);
        const payload = Object.fromEntries(formData.entries());
        const { res, json } = await postJson('./api/submit_contact.php', payload);
        if (res.ok) {
          setBoxMessage(msgBox, true, 'Message sent successfully! Our team will get back to you shortly.');
          contactForm.reset();
        } else {
          setBoxMessage(msgBox, false, (json && json.error) || 'Failed to send message.');
        }
      } catch (_) {
        setBoxMessage(msgBox, false, 'Connection error. Please try again.');
      } finally {
        if (btn) {
          btn.disabled = false;
          btn.textContent = 'Send Message';
        }
      }
    });
  }

  const collabForm = document.getElementById('collabForm') || document.getElementById('collaborationForm');
  if (collabForm) {
    collabForm.addEventListener('submit', async (e) => {
      e.preventDefault();

      const btn = collabForm.querySelector('button[type="submit"]');
      const msgBox = document.getElementById('form-message');

      if (btn) {
        btn.disabled = true;
        btn.textContent = 'Submitting...';
      }

      try {
        const formData = new FormData(collabForm);
        const payload = Object.fromEntries(formData.entries());
        const { res, json } = await postJson('./api/submit_collaboration.php', payload);
        if (res.ok) {
          setBoxMessage(msgBox, true, 'Proposal received! Our team will review and contact you shortly.');
          collabForm.reset();
        } else {
          setBoxMessage(msgBox, false, (json && json.error) || 'Submission failed.');
        }
      } catch (_) {
        setBoxMessage(msgBox, false, 'Connection error. Please try again.');
      } finally {
        if (btn) {
          btn.disabled = false;
          btn.textContent = 'Submit Proposal';
        }
      }
    });
  }

  const workshopForm = document.getElementById('workshopForm');
  if (workshopForm) {
    const selected = getUrlParam('event');
    if (selected) {
      const select = workshopForm.querySelector('select[name="workshop"]');
      if (select) {
        Array.from(select.options).some((opt) => {
          if (String(opt.value).toLowerCase().includes(String(selected).toLowerCase())) {
            opt.selected = true;
            return true;
          }
          return false;
        });
      }
    }

    workshopForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const btn = workshopForm.querySelector('button[type="submit"]');
      const msgBox = document.getElementById('form-message');

      if (btn) {
        btn.disabled = true;
        btn.textContent = 'Registering...';
      }

      try {
        const formData = new FormData(workshopForm);
        const payload = Object.fromEntries(formData.entries());
        const { res, json } = await postJson('./api/submit_workshop.php', payload);
        if (res.ok) {
          setBoxMessage(msgBox, true, 'Registration successful! We will contact you shortly with further details.');
          workshopForm.reset();
        } else {
          setBoxMessage(msgBox, false, (json && json.error) || 'Registration failed.');
        }
      } catch (_) {
        setBoxMessage(msgBox, false, 'Connection error. Please try again.');
      } finally {
        if (btn) {
          btn.disabled = false;
          btn.textContent = 'Complete Registration';
        }
      }
    });
  }

  const trainerSchemeButtons = Array.from(document.querySelectorAll('[data-trainer-scheme]'));
  const trainerForms = Array.from(document.querySelectorAll('form[data-trainer-form]'));

  const findTrainerFormByTarget = (target) => {
    if (!target) return null;
    const el = document.querySelector(target);
    if (!el) return null;
    return el.querySelector('form[data-trainer-form]');
  };

  if (trainerSchemeButtons.length) {
    trainerSchemeButtons.forEach((btn) => {
      btn.addEventListener('click', () => {
        const scheme = btn.getAttribute('data-trainer-scheme') || '';
        const target = btn.getAttribute('data-trainer-target') || '';
        const form = findTrainerFormByTarget(target);
        if (form) {
          const schemeInput = form.querySelector('input[name="scheme"]');
          if (schemeInput) schemeInput.value = scheme;
        }
        if (target) {
          const t = document.querySelector(target);
          if (t) t.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      });
    });
  }

  if (trainerForms.length) {
    const schemeParam = getUrlParam('scheme');
    if (schemeParam) {
      trainerForms.forEach((f) => {
        const schemeInput = f.querySelector('input[name="scheme"]');
        if (schemeInput) schemeInput.value = schemeParam;
      });
    }

    trainerForms.forEach((form) => {
      form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = form.querySelector('button[type="submit"]');
        const msgBox = form.querySelector('.trainer-form-message');

        if (btn) {
          btn.disabled = true;
          btn.textContent = 'Submitting...';
        }

        try {
          const formData = new FormData(form);
          const res = await fetch('./api/submit_trainer.php', { method: 'POST', body: formData });
          const data = await res.json().catch(() => ({}));
          if (res.ok) {
            setBoxMessage(msgBox, true, 'Application received! We will review your submission and contact you shortly.');
            const schemeValue = formData.get('scheme');
            form.reset();
            const schemeInput = form.querySelector('input[name="scheme"]');
            if (schemeInput && schemeValue) schemeInput.value = String(schemeValue);
          } else {
            setBoxMessage(msgBox, false, data.error || 'Submission failed.');
          }
        } catch (_) {
          setBoxMessage(msgBox, false, 'Connection error. Please try again.');
        } finally {
          if (btn) {
            btn.disabled = false;
            btn.textContent = 'Submit Application';
          }
        }
      });
    });
  }

  const coursesSection = document.getElementById('courses');
  if (coursesSection && coursesSection.querySelector('.curricula-layout')) {
    const filterButtons = Array.from(coursesSection.querySelectorAll('[data-course-filter]'));
    const courseSections = Array.from(coursesSection.querySelectorAll('.course-section'));
    const sidebarLinks = Array.from(coursesSection.querySelectorAll('.course-nav-item a'));

    const applyCourseFilter = (cat) => {
      courseSections.forEach((sec) => {
        if (cat === 'all') {
          sec.style.display = '';
          return;
        }
        const cats = (sec.getAttribute('data-category') || '').split(/\s+/).filter(Boolean);
        sec.style.display = cats.includes(cat) ? '' : 'none';
      });
    };

    if (filterButtons.length) {
      filterButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
          filterButtons.forEach((b) => b.classList.remove('active'));
          btn.classList.add('active');
          applyCourseFilter(btn.getAttribute('data-course-filter') || 'all');
        });
      });
    }

    if (sidebarLinks.length && courseSections.length && 'IntersectionObserver' in window) {
      const observer = new IntersectionObserver(
        (entries) => {
          entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            const id = entry.target.id;
            sidebarLinks.forEach((a) => {
              a.classList.toggle('active', a.getAttribute('href') === `#${id}`);
            });
          });
        },
        { threshold: 0.25, rootMargin: '-160px 0px -60% 0px' }
      );
      courseSections.forEach((s) => observer.observe(s));
    }
  }

  const tabButtons = document.querySelectorAll('[data-tab]');
  const certificateTab = document.getElementById('certificate-tab');
  const membershipTab = document.getElementById('membership-tab');
  const verifyResult = document.getElementById('verify-result');
  const certForm = document.getElementById('certForm');
  const memberVerifyForm = document.getElementById('memberVerifyForm');

  if (tabButtons.length && certificateTab && membershipTab && verifyResult) {
    const switchTab = (activeBtn, tabId) => {
      tabButtons.forEach((b) => {
        b.classList.remove('active');
        b.style.borderBottomColor = 'transparent';
        b.style.color = 'var(--soft)';
      });
      activeBtn.classList.add('active');
      activeBtn.style.borderBottomColor = 'var(--sage)';
      activeBtn.style.color = 'var(--sage-dark)';

      certificateTab.style.display = tabId === 'certificate' ? 'block' : 'none';
      membershipTab.style.display = tabId === 'membership' ? 'block' : 'none';
      verifyResult.style.display = 'none';
    };

    tabButtons.forEach((btn) => {
      btn.addEventListener('click', () => {
        const tabId = btn.getAttribute('data-tab');
        if (tabId) switchTab(btn, tabId);
      });
    });
  }

  const handleVerify = async (form, type) => {
    if (!form || !verifyResult) return;
    const id = form.querySelector('[name="credential_id"]')?.value || '';
    verifyResult.style.display = 'block';
    verifyResult.innerHTML = '<div class="verify-result-header"><div class="verify-result-title">Verification Result</div><div class="verify-status-pill warn">Checking…</div></div><div class="verify-result-body"><p class="verify-message">Verifying credential, please wait…</p></div>';

    try {
      const { res, json } = await postJson('./api/verify.php', { credential_id: id, type });
      const valid = Boolean(json && json.valid);
      const cred = (json && json.credential) || null;
      const msg = (json && json.message) || (res.ok ? 'Verified.' : 'Verification failed.');

      const pillClass = valid ? 'ok' : (cred && cred.status ? 'warn' : 'bad');
      const pillText = valid ? 'Verified' : (cred && cred.status ? cred.status : 'Not Found');
      const titleText = valid ? 'Credential Verified' : 'Verification Result';

      const field = (label, value) => {
        const safe = value && String(value).trim() ? String(value) : '—';
        return `<div class="verify-field"><div class="verify-label">${label}</div><div class="verify-value">${safe}</div></div>`;
      };

      const fields = [];
      fields.push(field('Name', cred && cred.name));
      fields.push(field('Credential ID', cred && cred.credential_id));
      fields.push(field('Credential Type', cred && cred.type));
      fields.push(field('Status', cred && cred.status));
      fields.push(field('Date Issued', cred && cred.issued_date));
      fields.push(field('Qualification', cred && cred.qualification));
      fields.push(field('Grade', cred && cred.grade));

      verifyResult.innerHTML = `
        <div class="verify-result-header">
          <div class="verify-result-title">${titleText}</div>
          <div class="verify-status-pill ${pillClass}">${pillText}</div>
        </div>
        <div class="verify-result-body">
          <p class="verify-message">${msg}</p>
          <div class="verify-grid">${fields.join('')}</div>
        </div>
        <div class="verify-result-actions">
          <button type="button" class="btn-soft" id="verifyPrintBtn">Print</button>
          <a class="btn-soft" href="./contact.html">Contact Registry</a>
        </div>
      `;

      const printBtn = document.getElementById('verifyPrintBtn');
      if (printBtn) printBtn.addEventListener('click', () => window.print());

      if (valid) {
        const layer = document.createElement('div');
        layer.className = 'balloon-layer';
        const colors = ['#e53935', '#43a047', '#1e88e5', '#fdd835', '#8e24aa', '#fb8c00'];
        const count = 14;
        for (let i = 0; i < count; i++) {
          const b = document.createElement('div');
          b.className = 'balloon';
          b.style.left = `${Math.round(Math.random() * 100)}%`;
          b.style.animationDelay = `${Math.random() * 0.5}s`;
          b.style.background = colors[i % colors.length];
          layer.appendChild(b);
        }
        document.body.appendChild(layer);
        window.setTimeout(() => layer.remove(), 3200);
      }
    } catch (_) {
      verifyResult.innerHTML = '<div class="verify-result-header"><div class="verify-result-title">Verification Result</div><div class="verify-status-pill bad">Error</div></div><div class="verify-result-body"><p class="verify-message">An error occurred during verification.</p></div>';
    }
  };

  if (certForm) {
    certForm.addEventListener('submit', (e) => {
      e.preventDefault();
      handleVerify(certForm, 'Certificate');
    });
  }

  if (memberVerifyForm) {
    memberVerifyForm.addEventListener('submit', (e) => {
      e.preventDefault();
      handleVerify(memberVerifyForm, 'Membership');
    });
  }
})();
