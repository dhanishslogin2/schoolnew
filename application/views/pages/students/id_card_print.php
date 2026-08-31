<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Print Student ID Cards - <?php echo html_escape($settings->school_name ?? 'Login2'); ?></title>
  <link href="<?php echo base_url('assets/fonts/inter.css'); ?>" rel="stylesheet"/>
  <link href="<?php echo base_url('assets/fonts/material-symbols.css'); ?>" rel="stylesheet"/>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background: #f1f5f9;
      color: #0f172a;
      padding: 20px;
    }

    /* Print action bar */
    .print-bar {
      max-width: 900px;
      margin: 0 auto 20px auto;
      background: #ffffff;
      padding: 12px 20px;
      border-radius: 12px;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .print-btn {
      background: #006c4a;
      color: #ffffff;
      border: none;
      padding: 9px 20px;
      border-radius: 8px;
      font-weight: 600;
      font-size: 14px;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }
    .close-btn {
      background: #e2e8f0;
      color: #334155;
      border: none;
      padding: 9px 18px;
      border-radius: 8px;
      font-weight: 600;
      font-size: 14px;
      cursor: pointer;
      text-decoration: none;
    }

    .cards-container {
      display: flex;
      flex-wrap: wrap;
      gap: 25px;
      justify-content: center;
      max-width: 1000px;
      margin: 0 auto;
    }

    /* Standard CR80 Portrait Card: 2.125 inches x 3.375 inches (54mm x 86mm) */
    .cr80-portrait-card {
      width: 54mm;
      height: 86mm;
      background: #ffffff;
      border-radius: 3.5mm;
      overflow: hidden;
      position: relative;
      border: 1px solid #cbd5e1;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      page-break-inside: avoid;
      break-inside: avoid;
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
      font-size: 7pt;
      line-height: 1.2;
    }

    /* Lanyard Slot Hole Accent */
    .lanyard-slot {
      position: absolute;
      top: 1.8mm;
      left: 50%;
      transform: translateX(-50%);
      width: 11mm;
      height: 2mm;
      background: #ffffff;
      border: 0.3mm solid #cbd5e1;
      border-radius: 2mm;
      z-index: 20;
    }

    /* Wave Background Decorations */
    .card-waves-svg {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      pointer-events: none;
      z-index: 1;
    }

    .card-content-layer {
      position: relative;
      z-index: 10;
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: 4mm 3mm 3mm 3mm;
    }

    /* Header */
    .card-front-header {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding-top: 1.5mm;
    }
    .school-logo-img {
      max-height: 7.5mm;
      max-width: 25mm;
      object-fit: contain;
    }
    .school-sub-text {
      font-size: 5.2pt;
      font-weight: 800;
      color: #006c4a;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      margin-top: 0.4mm;
      line-height: 1;
    }

    /* Photo Frame */
    .student-photo-wrapper {
      margin: 1mm auto;
      width: 22mm;
      height: 26mm;
      border-radius: 2mm;
      border: 0.6mm solid #ea580c;
      background: #ffffff;
      box-shadow: 0 2px 5px rgba(0,0,0,0.12);
      overflow: hidden;
      position: relative;
    }
    .student-photo-img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .student-photo-fallback {
      width: 100%;
      height: 100%;
      background: #f1f5f9;
      color: #006c4a;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 11pt;
      font-weight: 800;
    }

    /* Student ID Badge */
    .id-pill-badge {
      align-self: center;
      background: #ffffff;
      border: 0.35mm solid #ea580c;
      border-radius: 3mm;
      padding: 0.3mm 2.2mm;
      font-size: 5pt;
      font-weight: 800;
      color: #ea580c;
      letter-spacing: 0.2px;
      box-shadow: 0 1px 2px rgba(0,0,0,0.05);
      margin-bottom: 0.6mm;
    }

    /* Student Name */
    .student-name-title {
      text-align: center;
      font-size: 8.5pt;
      font-weight: 800;
      color: #006c4a;
      text-transform: uppercase;
      letter-spacing: 0.3px;
      line-height: 1.1;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      margin-bottom: 0.8mm;
    }

    /* Two-column Aligned Details Table */
    .details-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 5.8pt;
      margin-bottom: 1mm;
      padding: 0 1mm;
    }
    .details-table td {
      padding: 0.3mm 0;
      vertical-align: top;
    }
    .details-table .lbl-col {
      color: #334155;
      font-weight: 600;
      width: 18mm;
      white-space: nowrap;
    }
    .details-table .sep-col {
      width: 2mm;
      text-align: center;
      color: #64748b;
      font-weight: 600;
    }
    .details-table .val-col {
      color: #0f172a;
      font-weight: 700;
      word-break: break-word;
    }
    .blood-highlight {
      color: #dc2626 !important;
      font-weight: 800 !important;
    }

    /* Back Side Styles */
    .back-contact-badge {
      font-size: 5.2pt;
      font-weight: 800;
      color: #ea580c;
      letter-spacing: 0.8px;
      text-transform: uppercase;
      margin-top: 1mm;
      text-align: center;
    }
    .back-main-phone {
      text-align: center;
      font-size: 8pt;
      font-weight: 800;
      color: #006c4a;
      letter-spacing: 0.3px;
      margin-top: 0.2mm;
      margin-bottom: 1.2mm;
    }
    .back-address-badge {
      font-size: 5.2pt;
      font-weight: 800;
      color: #006c4a;
      letter-spacing: 0.8px;
      text-transform: uppercase;
      text-align: center;
      margin-bottom: 0.4mm;
    }
    .back-address-text {
      text-align: center;
      font-size: 5.2pt;
      color: #334155;
      line-height: 1.2;
      padding: 0 1.5mm;
      margin-bottom: 1.5mm;
      font-weight: 600;
    }

    .back-info-list {
      display: flex;
      flex-direction: column;
      gap: 0.7mm;
      padding: 0 1.5mm;
      margin-bottom: 1mm;
    }
    .back-info-item {
      display: flex;
      align-items: center;
      gap: 1.2mm;
      font-size: 5.2pt;
      color: #1e293b;
      font-weight: 600;
    }
    .back-info-icon {
      color: #006c4a;
      font-size: 7pt;
      flex-shrink: 0;
    }

    .back-sig-wrapper {
      text-align: center;
      margin-top: auto;
      padding-bottom: 0.5mm;
    }
    .back-sig-img {
      height: 5.5mm;
      max-width: 22mm;
      object-fit: contain;
      margin: 0 auto;
      display: block;
    }
    .back-sig-title {
      font-size: 4.8pt;
      font-weight: 700;
      color: #475569;
      text-transform: capitalize;
      margin-top: 0.3mm;
    }
    .back-return-disclaimer {
      font-size: 5.2pt;
      font-weight: 700;
      color: #ea580c;
      text-align: center;
      line-height: 1.2;
      padding: 0 1mm 1mm 1mm;
    }

    /* Print media query */
    @media print {
      body {
        background: #ffffff !important;
        padding: 0 !important;
        margin: 0 !important;
      }
      .no-print {
        display: none !important;
      }
      .cards-container {
        display: block !important;
        gap: 0 !important;
        max-width: 100% !important;
        margin: 0 !important;
      }
      .card-pair-wrapper {
        display: flex !important;
        gap: 6mm !important;
        margin-bottom: 8mm !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
      }
      .cr80-portrait-card {
        border: 0.3mm solid #cbd5e1 !important;
        box-shadow: none !important;
      }
      @page {
        size: A4 portrait;
        margin: 8mm;
      }
    }
  </style>
</head>
<body>

  <!-- Print Action Bar -->
  <div class="print-bar no-print">
    <div>
      <h3 style="font-size: 16px; font-weight: 700; color: #006c4a;">Print Student ID Cards</h3>
      <p style="font-size: 12px; color: #64748b; margin-top: 2px;">Total: <?php echo count($students); ?> Student Card(s) · Standard CR80 Portrait (2.125" × 3.375" / 54mm × 86mm)</p>
    </div>
    <div style="display: flex; gap: 10px;">
      <button onclick="window.print()" class="print-btn">
        <span class="material-symbols-outlined" style="font-size: 18px;">print</span> Print Cards
      </button>
      <button onclick="window.close()" class="close-btn">Close</button>
    </div>
  </div>

  <div class="cards-container">
    <?php foreach ($students as $st): ?>
      <?php
        $fullName = trim($st->first_name . ' ' . ($st->middle_name ? $st->middle_name . ' ' : '') . $st->last_name);
        $nameParts = explode(' ', $fullName);
        $initials = '';
        foreach ($nameParts as $np) { if (!empty($np)) $initials .= strtoupper($np[0]); }
        $initials = substr($initials, 0, 2) ?: 'ST';
        $classDisplay = trim(($st->class_name ?: 'Grade 10'));
        $sectionDisplay = trim($st->section_name ?: 'A');
        $dobFormatted = !empty($st->date_of_birth) ? date('d-m-Y', strtotime($st->date_of_birth)) : '15-06-2012';

        $hasPhoto = !empty($st->photo) && file_exists(FCPATH . 'uploads/students/' . $st->photo);
        
        $hasLogo  = !empty($settings->school_logo) && (file_exists(FCPATH . 'uploads/id_card/' . $settings->school_logo) || file_exists(FCPATH . 'uploads/settings/' . $settings->school_logo));
        $logoPath = base_url('assets/logo.png');
        if ($hasLogo) {
          $logoPath = file_exists(FCPATH . 'uploads/id_card/' . $settings->school_logo)
            ? base_url('uploads/id_card/' . $settings->school_logo)
            : base_url('uploads/settings/' . $settings->school_logo);
        }

        $hasSig  = !empty($settings->principal_signature) && file_exists(FCPATH . 'uploads/id_card/' . $settings->principal_signature);
        $sigPath = $hasSig ? base_url('uploads/id_card/' . $settings->principal_signature) : '';
      ?>

      <div class="card-pair-wrapper" style="display: flex; gap: 20px; margin-bottom: 20px;">
        
        <?php if ($side === 'both' || $side === 'front'): ?>
          <!-- FRONT SIDE (CR80 Portrait: 54mm x 86mm) -->
          <div class="cr80-portrait-card">
            <!-- Lanyard Slot Accent -->
            <div class="lanyard-slot"></div>

            <!-- Curved Waves Background (SVG) -->
            <svg class="card-waves-svg" viewBox="0 0 204 325" fill="none" xmlns="http://www.w3.org/2000/svg">
              <!-- Top Orange Wave -->
              <path d="M0 0C55 0 135 18 204 9V38C146 47 66 28 0 42V0Z" fill="#ea580c"/>
              <!-- Top Green Wave -->
              <path d="M0 0C44 0 88 26 204 18V0H0Z" fill="#006c4a"/>
              <!-- Decorative Right Dots -->
              <circle cx="188" cy="115" r="4" fill="#ea580c"/>
              <circle cx="193" cy="125" r="4" fill="#006c4a"/>
              <circle cx="190" cy="137" r="5" fill="#fed7aa"/>
              <!-- Bottom Orange Wave -->
              <path d="M204 325C142 325 62 308 0 317V286C55 276 135 295 204 280V325Z" fill="#ea580c"/>
              <!-- Bottom Green Wave -->
              <path d="M204 325C157 325 113 296 0 307V325H204Z" fill="#006c4a"/>
            </svg>

            <!-- Card Content Layer -->
            <div class="card-content-layer">
              
              <!-- Front Header -->
              <div class="card-front-header">
                <img src="<?php echo $logoPath; ?>" alt="Login2 Logo" class="school-logo-img"/>
                <div class="school-sub-text"><?php echo html_escape($settings->card_title ?? 'PUBLIC SCHOOL'); ?></div>
              </div>

              <!-- Student Photo -->
              <div class="student-photo-wrapper">
                <?php if ($hasPhoto): ?>
                  <img src="<?php echo base_url('uploads/students/' . $st->photo); ?>" alt="<?php echo html_escape($fullName); ?>" class="student-photo-img"/>
                <?php else: ?>
                  <div class="student-photo-fallback"><?php echo html_escape($initials); ?></div>
                <?php endif; ?>
              </div>

              <!-- Student ID Pill Badge -->
              <div class="id-pill-badge">
                STUDENT ID : <?php echo html_escape($st->admission_number ?? 'EDU2026015'); ?>
              </div>

              <!-- Student Name -->
              <div class="student-name-title">
                <?php echo html_escape($fullName); ?>
              </div>

              <!-- Details Table -->
              <table class="details-table">
                <tr>
                  <td class="lbl-col">Father's Name</td>
                  <td class="sep-col">:</td>
                  <td class="val-col"><?php echo html_escape($st->guardian_name ?: 'test'); ?></td>
                </tr>
                <tr>
                  <td class="lbl-col">Class</td>
                  <td class="sep-col">:</td>
                  <td class="val-col"><?php echo html_escape($classDisplay); ?></td>
                </tr>
                <tr>
                  <td class="lbl-col">Section</td>
                  <td class="sep-col">:</td>
                  <td class="val-col"><?php echo html_escape($sectionDisplay); ?></td>
                </tr>
                <?php if (!empty($st->roll_number)): ?>
                <tr>
                  <td class="lbl-col">Roll No.</td>
                  <td class="sep-col">:</td>
                  <td class="val-col"><?php echo html_escape($st->roll_number); ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                  <td class="lbl-col">DOB</td>
                  <td class="sep-col">:</td>
                  <td class="val-col"><?php echo $dobFormatted; ?></td>
                </tr>
                <tr>
                  <td class="lbl-col">Blood Group</td>
                  <td class="sep-col">:</td>
                  <td class="val-col blood-highlight"><?php echo html_escape($st->blood_group ?: 'A+'); ?></td>
                </tr>
              </table>

            </div>
          </div>
        <?php endif; ?>

        <?php if ($side === 'both' || $side === 'back'): ?>
          <!-- BACK SIDE (CR80 Portrait: 54mm x 86mm) -->
          <div class="cr80-portrait-card">
            <!-- Lanyard Slot Accent -->
            <div class="lanyard-slot"></div>

            <!-- Waves Background -->
            <svg class="card-waves-svg" viewBox="0 0 204 325" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M0 0C55 0 135 18 204 9V38C146 47 66 28 0 42V0Z" fill="#ea580c"/>
              <path d="M0 0C44 0 88 26 204 18V0H0Z" fill="#006c4a"/>
              <circle cx="185" cy="85" r="3.5" fill="#ea580c"/>
              <circle cx="181" cy="94" r="4.5" fill="#fed7aa"/>
              <circle cx="190" cy="96" r="3.5" fill="#006c4a"/>
              <path d="M204 325C142 325 62 308 0 317V286C55 276 135 295 204 280V325Z" fill="#006c4a"/>
              <path d="M204 325C157 325 113 296 0 307V325H204Z" fill="#ea580c"/>
            </svg>

            <!-- Card Content Layer -->
            <div class="card-content-layer" style="padding-top: 6mm;">
              
              <!-- Contact Header -->
              <div class="back-contact-badge">CONTACT</div>
              <div class="back-main-phone"><?php echo html_escape($settings->phone ?? '001 123 456 789'); ?></div>

              <!-- School Address -->
              <div class="back-address-badge">SCHOOL ADDRESS</div>
              <div class="back-address-text">
                100/1 Bryant Lane<br>Manor, Orla land, New York
              </div>

              <!-- Contact Info List -->
              <div class="back-info-list">
                <div class="back-info-item">
                  <span class="material-symbols-outlined back-info-icon" style="color: #ea580c;">call</span>
                  <span><?php echo html_escape($settings->phone ?? '001 123 456 789'); ?></span>
                </div>
                <div class="back-info-item">
                  <span class="material-symbols-outlined back-info-icon">mail</span>
                  <span><?php echo html_escape($settings->email ?? 'info@login2school.com'); ?></span>
                </div>
                <div class="back-info-item">
                  <span class="material-symbols-outlined back-info-icon">language</span>
                  <span><?php echo html_escape($settings->website ?? 'www.login2school.com'); ?></span>
                </div>
                <div class="back-info-item">
                  <span class="material-symbols-outlined back-info-icon">support_agent</span>
                  <span>Emergency: <strong style="color: #0f172a;"><?php echo html_escape($settings->emergency_contact ?? '001 987 654 321'); ?></strong></span>
                </div>
              </div>

              <!-- Principal Signature Block -->
              <div class="back-sig-wrapper">
                <?php if ($hasSig): ?>
                  <img src="<?php echo $sigPath; ?>" alt="Signature" class="back-sig-img"/>
                <?php else: ?>
                  <div style="font-family: Georgia, serif; font-style: italic; font-size: 11pt; color: #1e293b; line-height: 1;">John</div>
                <?php endif; ?>
                <div class="back-sig-title">Principal Signature</div>
              </div>

              <!-- Return Notice Disclaimer (Orange) -->
              <div class="back-return-disclaimer">
                If found, please return this card<br>to the school.
              </div>

            </div>
          </div>
        <?php endif; ?>

      </div>
    <?php endforeach; ?>
  </div>

  <script>
    window.addEventListener('DOMContentLoaded', () => {
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.get('autoprint') === '1') {
        setTimeout(() => { window.print(); }, 500);
      }
    });
  </script>
</body>
</html>
