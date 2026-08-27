<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 no-print">
      <div>
        <h2 class="font-headline-md text-headline-md text-on-surface">Student ID Cards</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Preview and print student identity cards with barcode and school credentials.</p>
      </div>
      <div class="flex items-center gap-2 shrink-0">
        <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer">
          <span class="material-symbols-outlined text-[18px]">print</span>Print All ID Cards
        </button>
      </div>
    </div>

    <!-- Filters Bar -->
    <div class="flex flex-col md:flex-row gap-3 mb-6 no-print flex-wrap">
      <select onchange="applyFilter('class_id', this.value)" class="px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md text-on-surface-variant">
        <option value="">All Classes</option>
        <?php foreach ($classes as $cls): ?>
          <option value="<?php echo $cls->class_id; ?>" <?php echo ($this->input->get('class_id') == $cls->class_id) ? 'selected' : ''; ?>><?php echo html_escape($cls->class_name); ?></option>
        <?php endforeach; ?>
      </select>
      <select onchange="applyFilter('section_id', this.value)" class="px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md text-on-surface-variant">
        <option value="">All Sections</option>
        <?php foreach ($sections as $sec): ?>
          <option value="<?php echo $sec->section_id; ?>" <?php echo ($this->input->get('section_id') == $sec->section_id) ? 'selected' : ''; ?>><?php echo html_escape($sec->class_name . ' - ' . $sec->section_name); ?></option>
        <?php endforeach; ?>
      </select>
      <a href="<?php echo site_url('students/id_cards'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined text-[18px]">restart_alt</span>Reset</a>
    </div>

    <style>
      @media print {
        body { background: #fff !important; margin: 0; padding: 10px; }
        .no-print, #sidebar-root, #header-root { display: none !important; }
        .id-card-grid { display: grid !important; grid-template-columns: repeat(2, 1fr) !important; gap: 20px !important; }
        .id-card { page-break-inside: avoid; border: 2px solid #000 !important; box-shadow: none !important; }
      }
    </style>

    <!-- ID Card Grid -->
    <div class="id-card-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
      <?php if (empty($students)): ?>
        <div class="col-span-full p-8 text-center bg-surface-container-lowest rounded-xl border border-outline-variant text-on-surface-variant">
          No students found in the selected class/section.
        </div>
      <?php endif; ?>
      <?php foreach ($students as $st): ?>
        <?php
          $nameParts = explode(' ', trim($st->first_name . ' ' . $st->last_name));
          $initials = '';
          foreach ($nameParts as $np) { if (!empty($np)) $initials .= strtoupper($np[0]); }
          if (strlen($initials) > 2) $initials = substr($initials, 0, 2);
          $classDisplay = trim(($st->class_name ?: '') . ' ' . ($st->section_name ?: ''));
        ?>
        <div class="id-card elevation-2 rounded-2xl overflow-hidden border border-outline-variant/60 bg-surface-container-lowest flex flex-col justify-between">
          <!-- Card Top Header -->
          <div class="bg-primary text-on-primary p-3.5 text-center">
            <div class="text-[13px] font-bold tracking-wider uppercase">Senior School</div>
            <div class="text-[10px] opacity-80 uppercase tracking-widest mt-0.5">Student Identity Card</div>
          </div>

          <!-- Card Body -->
          <div class="p-5 flex items-center gap-4">
            <div class="w-20 h-20 rounded-xl bg-primary-fixed text-primary flex items-center justify-center font-bold text-2xl shrink-0 shadow-inner">
              <?php echo html_escape($initials); ?>
            </div>
            <div class="min-w-0 flex-1 space-y-1 text-body-md">
              <div class="font-bold text-on-surface text-[15px] truncate"><?php echo html_escape($st->first_name . ' ' . $st->last_name); ?></div>
              <div class="text-[12px] text-on-surface font-medium">Adm No: <span class="font-mono text-primary"><?php echo html_escape($st->admission_number); ?></span></div>
              <?php if (!empty($st->roll_number)): ?>
                <div class="text-[12px] text-on-surface-variant">Roll No: <span class="font-medium text-on-surface"><?php echo html_escape($st->roll_number); ?></span></div>
              <?php endif; ?>
              <div class="text-[12px] text-on-surface-variant">Class: <span class="font-medium text-on-surface"><?php echo html_escape($classDisplay ?: 'Grade 10'); ?></span></div>
              <div class="text-[12px] text-on-surface-variant">DOB: <span class="font-medium text-on-surface"><?php echo date('d-m-Y', strtotime($st->date_of_birth)); ?></span></div>
              <div class="text-[12px] text-on-surface-variant">Emergency: <span class="font-medium text-on-surface"><?php echo html_escape($st->guardian_phone); ?></span></div>
            </div>
          </div>

          <!-- Card Footer Bar -->
          <div class="px-5 py-2.5 bg-surface-container-low border-t border-outline-variant/40 flex items-center justify-between text-[11px] text-on-surface-variant">
            <span>Valid: 2026-2027</span>
            <span class="font-semibold text-primary">Principal Signed</span>
          </div>

          <!-- Actions (Hidden in Print) -->
          <div class="flex border-t border-outline-variant/40 no-print">
            <a href="<?php echo site_url('students/profile/' . $st->student_id); ?>" class="flex-1 py-2.5 text-label-md text-on-surface-variant hover:bg-surface-container-high flex items-center justify-center gap-1"><span class="material-symbols-outlined text-[16px]">visibility</span>Profile</a>
            <div class="w-px bg-outline-variant/40"></div>
            <button onclick="window.print()" class="flex-1 py-2.5 text-label-md text-secondary hover:bg-surface-container-high flex items-center justify-center gap-1 cursor-pointer"><span class="material-symbols-outlined text-[16px]">print</span>Print</button>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <script>
      function applyFilter(key, val) {
        var url = new URL(window.location.href);
        if (val) { url.searchParams.set(key, val); } else { url.searchParams.delete(key); }
        window.location.href = url.toString();
      }
    </script>
