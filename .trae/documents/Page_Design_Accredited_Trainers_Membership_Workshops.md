# Page Design Specifications (Desktop-first)

## Global Styles (all pages)
- Layout system: Desktop-first 12-column CSS Grid for main content (max-width 1200px), with Flexbox for component alignment.
- Spacing: 8px spacing scale (8/16/24/32/48).
- Typography: Base 16px; headings scale (H1 40px, H2 28px, H3 20px).
- Colors (tokens):
  - Background: #FFFFFF
  - Surface: #F7F7F8
  - Text: #111827
  - Muted text: #6B7280
  - Primary: brand primary (reuse existing)
  - Focus ring: primary at 40% opacity
- Buttons:
  - Primary: solid primary + white text; hover darken 8%; disabled 50% opacity.
  - Secondary: white background + 1px border + primary text.
- Links: underline on hover; visited state subtle.
- Form fields: 44px height; clear error states (red border + helper text).
- Responsive behavior (minimum):
  - >=1024px: 2–3 column layouts where relevant.
  - <1024px: single-column stacking; full-width CTAs.

---

## Page: Home
### Meta Information
- Title: Home
- Description: Overview and navigation to membership, trainers, and workshops.
- Open Graph: title + description aligned with page.

### Page Structure
- Stacked sections layout (hero → key links/cards).

### Sections & Components
1. Top navigation bar
   - Logo (left), primary links (right): Membership, Accredited Trainers, Workshops/Seminars.
2. Key destinations section
   - 3-card grid linking to the three pages.
   - Each card: title, 1–2 line summary, “Learn more” link.

---

## Page: Accredited Trainers
### Meta Information
- Title: Accredited Trainers
- Description: Two accreditation schemes and application forms.
- Open Graph: title, description.

### Page Structure
- Stacked sections with an internal anchor sub-nav (sticky on desktop).

### Sections & Components
1. Header
   - H1 + short intro explaining the two schemes.
2. Scheme selector / anchor tabs
   - Tabs or pills: “Scheme 1”, “Scheme 2”, “Apply”.
3. Scheme sections (two)
   - Each scheme block:
     - H2 scheme name
     - “Who it’s for” (short paragraph)
     - “Requirements” (bullet list, kept concise)
     - Primary CTA: “Apply for this scheme” (scrolls to form with scheme preselected).
4. Scheme comparison table
   - Columns: Scheme 1 vs Scheme 2
   - Rows: Intended audience, key requirements, outcome.
5. Application form
   - Form layout: 2-column grid on desktop; single column on mobile.
   - Fields (minimal): scheme (dropdown or hidden preselect), full name, email, phone (optional), experience summary (textarea), attachments upload (optional), consent checkbox.
   - Primary submit button.
   - Inline validation and error summary at top on submit.
6. Submission confirmation state
   - Replace form area with success panel after submit: “Application received” + what happens next.

---

## Page: Membership
### Meta Information
- Title: Membership
- Description: Two membership pathways with clear next steps.
- Open Graph: title, description.

### Page Structure
- Two-pathway comparison layout (side-by-side cards on desktop).

### Sections & Components
1. Header
   - H1 + short intro.
2. Pathway cards (two)
   - Card layout: title, “Who it’s for”, 3–5 bullet highlights.
   - CTA button per card (reuse existing destination/process).
3. Pathway details section
   - Accordion or stacked detail blocks for each pathway (benefits, requirements, next steps).

---

## Page: Workshops / Seminars
### Meta Information
- Title: Workshops & Seminars (2026)
- Description: 2026 virtual workshops/seminars schedule (9am–12pm).
- Open Graph: title, description.

### Page Structure
- Schedule-first layout: filter bar (optional) + schedule list/table.

### Sections & Components
1. Header
   - H1 + subtext: “All sessions are virtual, 9am–12pm (2026).”
2. Schedule list/table
   - Table on desktop; cards on mobile.
   - Columns/fields: Event title, Date (2026), Format (Virtual), Time (9am–12pm), CTA (Register/Enquire).
3. Event detail drawer (optional)
   - Clicking an event row opens a side drawer or modal with short description and CTA.
