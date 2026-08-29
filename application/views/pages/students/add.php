<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
    $sd = isset($wizard['student_details'])  ? $wizard['student_details']  : array();
    $ad = isset($wizard['academic_details']) ? $wizard['academic_details'] : array();
    $pd = isset($wizard['parent_details'])   ? $wizard['parent_details']   : array();
    $ps = isset($ad['prev_school'])          ? $ad['prev_school']          : array();

    $cs = isset($current_step) ? (int)$current_step : 1;

    function wval($arr, $key, $default = '') {
        return isset($arr[$key]) ? htmlspecialchars((string)$arr[$key], ENT_QUOTES, 'UTF-8') : $default;
    }
    function wsel($arr, $key, $value, $default = '') {
        return (isset($arr[$key]) ? (string)$arr[$key] : $default) === (string)$value ? 'selected' : '';
    }

    $academic_types = ['Achievement','Olympiad','Competition','Scholarship','Project','Certification','Award','Other Academic'];
    $extra_types    = ['Sports','Arts','Music','Dance','Club','Cultural','Competition','Other'];
    $levels         = ['School','District','State','National','International'];

    $saved_activities     = isset($ad['activities'])      ? $ad['activities']      : array();
    $saved_extracurricular = isset($ad['extracurricular']) ? $ad['extracurricular'] : array();
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
  <div>
    <h2 class="font-headline-md text-headline-md text-on-surface">Student Registration</h2>
    <p class="text-body-md font-body-md text-on-surface-variant mt-1">Complete all three steps to register a new student.</p>
  </div>
  <a href="<?php echo site_url('students/wizard_cancel'); ?>" id="wizard-cancel-btn"
     class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md hover:bg-surface-container-high transition-colors shrink-0">
    <span class="material-symbols-outlined text-[18px]">close</span>Cancel
  </a>
</div>

<!-- ═══ STEP INDICATOR ═══════════════════════════════════════════════════ -->
<div class="flex items-center gap-0 mb-6 overflow-x-auto pb-1">
  <?php for ($i = 1; $i <= 3; $i++):
    $labels = [1 => 'Student Details', 2 => 'Academic Details', 3 => 'Parent / Guardian'];
  ?>
  <?php if ($i > 1): ?>
    <div class="w-8 h-px bg-outline-variant mx-2 shrink-0"></div>
  <?php endif; ?>
  <div class="flex items-center gap-2 shrink-0">
    <div class="w-8 h-8 rounded-full flex items-center justify-center text-[13px] font-semibold transition-colors
                <?php echo ($cs > $i) ? 'bg-secondary text-on-secondary' : (($cs === $i) ? 'bg-secondary text-on-secondary' : 'bg-surface-container-high text-on-surface-variant'); ?>">
      <?php if ($cs > $i): ?>
        <span class="material-symbols-outlined text-[16px]">check</span>
      <?php else: echo $i; endif; ?>
    </div>
    <span class="text-body-md font-body-md <?php echo ($cs === $i) ? 'text-on-surface font-medium' : 'text-on-surface-variant'; ?>"><?php echo $labels[$i]; ?></span>
  </div>
  <?php endfor; ?>
</div>

<!-- ═══ ALERT BANNER ═════════════════════════════════════════════════════ -->
<div id="wizard-alert" class="hidden mb-4 px-4 py-3 rounded-lg border" role="alert"></div>

<!-- ═══════════════════════════════════════════════════════════════════════
     STEP 1 — STUDENT DETAILS
════════════════════════════════════════════════════════════════════════ -->
<?php if ($cs === 1): ?>
<div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
  <div class="px-5 py-4 border-b border-outline-variant/50">
    <h3 class="font-headline-md text-headline-md text-on-surface">Student Details</h3>
  </div>
  <div class="p-5">
    <form id="step1-form" novalidate>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5">Admission Number <span class="text-error">*</span></label>
          <input type="text" id="admission_number" name="admission_number"
                 value="<?php echo wval($sd, 'admission_number', 'EDU' . date('Y') . sprintf('%03d', rand(10,999))); ?>"
                 placeholder="e.g. EDU2026009"
                 class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
          <p class="field-error text-error text-[11px] mt-1 hidden" id="err-admission_number"></p>
        </div>
        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5">First Name <span class="text-error">*</span></label>
          <input type="text" id="first_name" name="first_name" value="<?php echo wval($sd, 'first_name'); ?>" placeholder="e.g. Aarav"
                 class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
          <p class="field-error text-error text-[11px] mt-1 hidden" id="err-first_name"></p>
        </div>
        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5">Last Name</label>
          <input type="text" name="last_name" value="<?php echo wval($sd, 'last_name'); ?>" placeholder="e.g. Nair"
                 class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
        </div>
        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5">Gender</label>
          <select name="gender" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary">
            <?php foreach (['Male','Female','Other'] as $g): ?>
              <option value="<?php echo $g; ?>" <?php echo wsel($sd,'gender',$g,'Male'); ?>><?php echo $g; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5">Date of Birth</label>
          <input type="date" name="date_of_birth" value="<?php echo wval($sd, 'date_of_birth', '2012-06-15'); ?>"
                 class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary"/>
        </div>
        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5">Blood Group</label>
          <select name="blood_group" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary">
            <?php foreach (['A+','A-','B+','B-','O+','O-','AB+','AB-'] as $bg): ?>
              <option value="<?php echo $bg; ?>" <?php echo wsel($sd,'blood_group',$bg,'A+'); ?>><?php echo $bg; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="flex justify-end gap-3 mt-6 pt-5 border-t border-outline-variant/50">
        <a href="<?php echo site_url('students/wizard_cancel'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant text-label-md hover:bg-surface-container-high transition-colors">Cancel</a>
        <button type="submit" id="btn-step1-next" class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer">
          <span class="material-symbols-outlined text-[18px]">arrow_forward</span>Next
        </button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<!-- ═══════════════════════════════════════════════════════════════════════
     STEP 2 — ACADEMIC DETAILS (Enhanced)
════════════════════════════════════════════════════════════════════════ -->
<?php if ($cs === 2): ?>
<form id="step2-form" novalidate>
<input type="hidden" id="tc_temp_path" name="tc_temp_path" value="<?php echo wval($ps, 'tc_temp_path'); ?>"/>

<!-- ── 2A: Current Academic Information ─────────────────────────────── -->
<div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden mb-4">
  <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/50">
    <h3 class="font-headline-md text-headline-md text-on-surface">Current Academic Information</h3>
    <?php if (!empty($current_academic_year)): ?>
    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-secondary-container text-on-secondary-container text-[12px] font-semibold">
      <span class="material-symbols-outlined text-[14px]">school</span><?php echo html_escape($current_academic_year->year_name); ?>
    </span>
    <?php endif; ?>
  </div>
  <div class="p-5">
    <input type="hidden" name="academic_year_id" value="<?php echo (int)$current_academic_year_id; ?>"/>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="lg:col-span-2">
        <label class="block font-label-md text-label-md text-on-surface mb-1.5">Academic Year</label>
        <div class="px-3 py-2.5 rounded-lg border border-outline-variant/40 bg-surface-container-low text-body-md text-on-surface-variant">
          <?php echo !empty($current_academic_year) ? html_escape($current_academic_year->year_name) : 'Current Year'; ?>
        </div>
      </div>
      <div>
        <label class="block font-label-md text-label-md text-on-surface mb-1.5">Class <span class="text-error">*</span></label>
        <select id="class_id" name="class_id" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary">
          <option value="">Select Class</option>
          <?php foreach ($classes as $cls): ?>
            <option value="<?php echo (int)$cls->class_id; ?>" <?php echo isset($ad['class_id']) && (int)$ad['class_id'] === (int)$cls->class_id ? 'selected' : ''; ?>><?php echo html_escape($cls->class_name); ?></option>
          <?php endforeach; ?>
        </select>
        <p class="field-error text-error text-[11px] mt-1 hidden" id="err-class_id"></p>
      </div>
      <div>
        <label class="block font-label-md text-label-md text-on-surface mb-1.5">Section</label>
        <select id="section_id" name="section_id" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary">
          <option value="">Select Section</option>
          <?php foreach ($sections as $sec): ?>
            <option value="<?php echo (int)$sec->section_id; ?>" <?php echo isset($ad['section_id']) && (int)$ad['section_id'] === (int)$sec->section_id ? 'selected' : ''; ?>><?php echo html_escape($sec->section_name ?? $sec->class_name); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="block font-label-md text-label-md text-on-surface mb-1.5">Roll Number</label>
        <input type="text" name="roll_number" value="<?php echo wval($ad, 'roll_number'); ?>" placeholder="e.g. 15"
               class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
      </div>
    </div>
  </div>
</div>

<!-- ── 2B: Previous School Information ──────────────────────────────── -->
<div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden mb-4">
  <div class="px-5 py-4 border-b border-outline-variant/50">
    <h3 class="font-headline-md text-headline-md text-on-surface">Previous School Information</h3>
  </div>
  <div class="p-5">
    <!-- No Previous School checkbox -->
    <label class="flex items-center gap-2.5 mb-5 cursor-pointer w-fit">
      <input type="checkbox" id="no_previous_school" name="no_previous_school" value="1"
             <?php echo !empty($ad['no_previous_school']) ? 'checked' : ''; ?>
             class="w-4 h-4 rounded border-outline-variant accent-secondary cursor-pointer"/>
      <span class="text-body-md font-body-md text-on-surface">No Previous School</span>
      <span class="text-body-md text-on-surface-variant">(e.g. LKG/UKG first admission)</span>
    </label>

    <!-- Previous school fields -->
    <div id="prev-school-fields" class="<?php echo !empty($ad['no_previous_school']) ? 'hidden' : ''; ?>">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="sm:col-span-2">
          <label class="block font-label-md text-label-md text-on-surface mb-1.5">Previous School Name <span class="text-error">*</span></label>
          <input type="text" id="prev_school_name" name="prev_school_name" value="<?php echo wval($ps, 'school_name'); ?>" placeholder="e.g. St. Mary's Higher Secondary School"
                 class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
          <p class="field-error text-error text-[11px] mt-1 hidden" id="err-prev_school_name"></p>
        </div>
        <div class="sm:col-span-2">
          <label class="block font-label-md text-label-md text-on-surface mb-1.5">Previous School Address</label>
          <input type="text" name="prev_school_address" value="<?php echo wval($ps, 'school_address'); ?>" placeholder="School address"
                 class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
        </div>
        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5">Board / Affiliation</label>
          <input type="text" id="prev_school_board" name="prev_school_board" value="<?php echo wval($ps, 'school_board'); ?>" placeholder="e.g. CBSE, ICSE, State Board"
                 class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
        </div>
        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5">Previous Class / Grade</label>
          <input type="text" id="prev_class" name="prev_class" value="<?php echo wval($ps, 'previous_class'); ?>" placeholder="e.g. Grade 9, Class X"
                 class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
        </div>
        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5">Previous Academic Year</label>
          <input type="text" id="prev_academic_year" name="prev_academic_year" value="<?php echo wval($ps, 'previous_academic_year'); ?>" placeholder="e.g. 2025-2026"
                 class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
        </div>
        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5">Date of Leaving</label>
          <input type="date" name="date_of_leaving" value="<?php echo wval($ps, 'date_of_leaving'); ?>"
                 class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary"/>
        </div>
        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5">Previous Percentage (%)</label>
          <input type="number" id="prev_percentage" name="prev_percentage" min="0" max="100" step="0.01"
                 value="<?php echo wval($ps, 'previous_percentage'); ?>" placeholder="e.g. 82.50"
                 class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
          <p class="field-error text-error text-[11px] mt-1 hidden" id="err-prev_percentage"></p>
        </div>
        <div class="sm:col-span-2">
          <label class="block font-label-md text-label-md text-on-surface mb-1.5">Reason for Leaving</label>
          <input type="text" name="reason_for_leaving" value="<?php echo wval($ps, 'reason_for_leaving'); ?>" placeholder="e.g. Relocated, Seeking better opportunities"
                 class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
        </div>
        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5">TC Number</label>
          <input type="text" name="tc_number" value="<?php echo wval($ps, 'tc_number'); ?>" placeholder="e.g. TC/2025/0042"
                 class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
        </div>
        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5">TC Document
            <span class="text-on-surface-variant font-normal">(PDF/JPG/PNG, max 2MB)</span>
          </label>
          <div class="flex items-center gap-2 flex-wrap">
            <label for="tc_document_file" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-outline-variant text-on-surface-variant text-label-md hover:bg-surface-container-high cursor-pointer transition-colors">
              <span class="material-symbols-outlined text-[16px]">upload_file</span>
              <span id="tc-btn-label">Choose File</span>
            </label>
            <input type="file" id="tc_document_file" name="tc_document" accept=".pdf,.jpg,.jpeg,.png" class="hidden"/>
            <?php if (!empty($ps['tc_temp_path'])): ?>
            <span id="tc-file-status" class="inline-flex items-center gap-1 text-[12px] text-on-secondary-container bg-secondary-container px-2 py-1 rounded-full">
              <span class="material-symbols-outlined text-[14px]">check_circle</span>
              <span id="tc-file-name">File uploaded</span>
              <button type="button" id="tc-file-remove" class="ml-1 text-on-surface-variant hover:text-error">
                <span class="material-symbols-outlined text-[14px]">close</span>
              </button>
            </span>
            <?php else: ?>
            <span id="tc-file-status" class="hidden inline-flex items-center gap-1 text-[12px] text-on-secondary-container bg-secondary-container px-2 py-1 rounded-full">
              <span class="material-symbols-outlined text-[14px]">check_circle</span>
              <span id="tc-file-name"></span>
              <button type="button" id="tc-file-remove" class="ml-1 text-on-surface-variant hover:text-error">
                <span class="material-symbols-outlined text-[14px]">close</span>
              </button>
            </span>
            <?php endif; ?>
            <span id="tc-upload-progress" class="hidden text-[12px] text-on-surface-variant">Uploading…</span>
          </div>
          <p class="field-error text-error text-[11px] mt-1 hidden" id="err-tc_document"></p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ── 2C: Academic Activities ───────────────────────────────────────── -->
<div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden mb-4">
  <div class="px-5 py-4 border-b border-outline-variant/50">
    <h3 class="font-headline-md text-headline-md text-on-surface">Academic Achievements &amp; Activities</h3>
    <p class="text-body-md text-on-surface-variant mt-0.5">Achievements, Olympiads, Competitions, Scholarships, Projects, Certifications, Awards</p>
  </div>
  <div class="p-5">
    <div id="academic-activities-list" class="flex flex-col gap-3 mb-4">
      <?php
      $rows = !empty($saved_activities) ? $saved_activities : [[]];
      foreach ($rows as $i => $act):
      ?>
      <div class="academic-activity-row grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-2 p-3 rounded-lg bg-surface-container-low border border-outline-variant/40 relative">
        <div>
          <label class="block font-label-md text-label-md text-on-surface-variant mb-1">Type</label>
          <select name="academic_activities[<?php echo $i; ?>][activity_type]" class="w-full px-2 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md text-sm focus:ring-2 focus:ring-primary/10 focus:border-primary">
            <?php foreach ($academic_types as $t): ?>
              <option value="<?php echo $t; ?>" <?php echo wsel($act,'activity_type',$t,'Achievement'); ?>><?php echo $t; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block font-label-md text-label-md text-on-surface-variant mb-1">Activity Name <span class="text-error">*</span></label>
          <input type="text" name="academic_activities[<?php echo $i; ?>][activity_name]" value="<?php echo wval($act,'activity_name'); ?>" placeholder="Name / Title"
                 class="w-full px-2 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md text-sm focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
        </div>
        <div>
          <label class="block font-label-md text-label-md text-on-surface-variant mb-1">Position / Result</label>
          <input type="text" name="academic_activities[<?php echo $i; ?>][position_result]" value="<?php echo wval($act,'position_result'); ?>" placeholder="e.g. 1st, Gold"
                 class="w-full px-2 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md text-sm focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
        </div>
        <div>
          <label class="block font-label-md text-label-md text-on-surface-variant mb-1">Year</label>
          <input type="number" name="academic_activities[<?php echo $i; ?>][year]" value="<?php echo wval($act,'year'); ?>" placeholder="<?php echo date('Y'); ?>" min="1990" max="<?php echo date('Y'); ?>"
                 class="w-full px-2 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md text-sm focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
        </div>
        <div class="flex flex-col gap-1">
          <label class="block font-label-md text-label-md text-on-surface-variant mb-1">Description</label>
          <div class="flex gap-1">
            <input type="text" name="academic_activities[<?php echo $i; ?>][description]" value="<?php echo wval($act,'description'); ?>" placeholder="Brief description"
                   class="flex-1 min-w-0 px-2 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md text-sm focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
            <button type="button" class="remove-activity shrink-0 w-8 h-9 flex items-center justify-center rounded-lg border border-error/30 text-error hover:bg-error-container transition-colors" title="Remove">
              <span class="material-symbols-outlined text-[16px]">remove</span>
            </button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <button type="button" id="add-academic-activity" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-secondary text-secondary text-label-md hover:bg-secondary-container transition-colors">
      <span class="material-symbols-outlined text-[16px]">add</span>Add Academic Activity
    </button>
  </div>
</div>

<!-- ── 2D: Extracurricular Activities ───────────────────────────────── -->
<div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden mb-4">
  <div class="px-5 py-4 border-b border-outline-variant/50">
    <h3 class="font-headline-md text-headline-md text-on-surface">Co-curricular &amp; Extracurricular Activities</h3>
    <p class="text-body-md text-on-surface-variant mt-0.5">Sports, Arts, Music, Dance, Clubs, Cultural Activities</p>
  </div>
  <div class="p-5">
    <div id="extracurricular-list" class="flex flex-col gap-3 mb-4">
      <?php
      $xrows = !empty($saved_extracurricular) ? $saved_extracurricular : [[]];
      foreach ($xrows as $j => $xact):
      ?>
      <div class="extra-activity-row grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-2 p-3 rounded-lg bg-surface-container-low border border-outline-variant/40 relative">
        <div>
          <label class="block font-label-md text-label-md text-on-surface-variant mb-1">Type</label>
          <select name="extracurricular[<?php echo $j; ?>][activity_type]" class="w-full px-2 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md text-sm focus:ring-2 focus:ring-primary/10 focus:border-primary">
            <?php foreach ($extra_types as $t): ?>
              <option value="<?php echo $t; ?>" <?php echo wsel($xact,'activity_type',$t,'Sports'); ?>><?php echo $t; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block font-label-md text-label-md text-on-surface-variant mb-1">Activity Name <span class="text-error">*</span></label>
          <input type="text" name="extracurricular[<?php echo $j; ?>][activity_name]" value="<?php echo wval($xact,'activity_name'); ?>" placeholder="Name"
                 class="w-full px-2 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md text-sm focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
        </div>
        <div>
          <label class="block font-label-md text-label-md text-on-surface-variant mb-1">Level</label>
          <select name="extracurricular[<?php echo $j; ?>][level]" class="w-full px-2 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md text-sm focus:ring-2 focus:ring-primary/10 focus:border-primary">
            <option value="">Select</option>
            <?php foreach ($levels as $l): ?>
              <option value="<?php echo $l; ?>" <?php echo wsel($xact,'level',$l); ?>><?php echo $l; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block font-label-md text-label-md text-on-surface-variant mb-1">Position / Achievement</label>
          <input type="text" name="extracurricular[<?php echo $j; ?>][position_result]" value="<?php echo wval($xact,'position_result'); ?>" placeholder="e.g. Winner"
                 class="w-full px-2 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md text-sm focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
        </div>
        <div>
          <label class="block font-label-md text-label-md text-on-surface-variant mb-1">Year</label>
          <input type="number" name="extracurricular[<?php echo $j; ?>][year]" value="<?php echo wval($xact,'year'); ?>" placeholder="<?php echo date('Y'); ?>" min="1990" max="<?php echo date('Y'); ?>"
                 class="w-full px-2 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md text-sm focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
        </div>
        <div class="flex flex-col gap-1">
          <label class="block font-label-md text-label-md text-on-surface-variant mb-1">Description</label>
          <div class="flex gap-1">
            <input type="text" name="extracurricular[<?php echo $j; ?>][description]" value="<?php echo wval($xact,'description'); ?>" placeholder="Optional"
                   class="flex-1 min-w-0 px-2 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md text-sm focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
            <button type="button" class="remove-extra shrink-0 w-8 h-9 flex items-center justify-center rounded-lg border border-error/30 text-error hover:bg-error-container transition-colors" title="Remove">
              <span class="material-symbols-outlined text-[16px]">remove</span>
            </button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <button type="button" id="add-extra-activity" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-secondary text-secondary text-label-md hover:bg-secondary-container transition-colors">
      <span class="material-symbols-outlined text-[16px]">add</span>Add Extracurricular Activity
    </button>
  </div>
</div>

<!-- ── Navigation ────────────────────────────────────────────────────── -->
<div class="flex justify-between gap-3 mt-2 mb-4">
  <a href="<?php echo site_url('students/add'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant text-label-md hover:bg-surface-container-high transition-colors">
    <span class="material-symbols-outlined text-[18px]">arrow_back</span>Back
  </a>
  <div class="flex gap-3">
    <a href="<?php echo site_url('students/wizard_cancel'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant text-label-md hover:bg-surface-container-high transition-colors">Cancel</a>
    <button type="submit" id="btn-step2-next" class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer">
      <span class="material-symbols-outlined text-[18px]">arrow_forward</span>Next
    </button>
  </div>
</div>
</form>
<?php endif; ?>

<!-- ═══════════════════════════════════════════════════════════════════════
     STEP 3 — PARENT / GUARDIAN
════════════════════════════════════════════════════════════════════════ -->
<?php if ($cs === 3): ?>
<div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
  <div class="px-5 py-4 border-b border-outline-variant/50">
    <h3 class="font-headline-md text-headline-md text-on-surface">Parent / Guardian</h3>
  </div>
  <div class="p-5">
    <!-- Summary of previous steps -->
    <div class="mb-5 p-4 rounded-lg bg-surface-container-low border border-outline-variant/40">
      <p class="text-label-md text-on-surface-variant mb-2 font-semibold uppercase tracking-wide">Registration Summary</p>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-1 text-body-md text-on-surface">
        <?php if (!empty($wizard['student_details'])): $s = $wizard['student_details']; ?>
        <span><span class="text-on-surface-variant">Name:</span> <?php echo html_escape(trim($s['first_name'] . ' ' . $s['last_name'])); ?></span>
        <span><span class="text-on-surface-variant">Admission No:</span> <?php echo html_escape($s['admission_number']); ?></span>
        <?php endif; ?>
        <?php if (!empty($wizard['academic_details'])): $a = $wizard['academic_details']; ?>
        <span><span class="text-on-surface-variant">Class:</span> <?php echo html_escape($a['class_id'] ?? '—'); ?></span>
        <span><span class="text-on-surface-variant">Roll No:</span> <?php echo html_escape($a['roll_number'] ?: '—'); ?></span>
        <?php if (!empty($a['prev_school']['school_name'])): ?>
        <span class="sm:col-span-2"><span class="text-on-surface-variant">Previous School:</span> <?php echo html_escape($a['prev_school']['school_name']); ?></span>
        <?php endif; ?>
        <?php $act_count = count($a['activities'] ?? []) + count($a['extracurricular'] ?? []); ?>
        <?php if ($act_count > 0): ?>
        <span class="sm:col-span-2"><span class="text-on-surface-variant">Activities:</span> <?php echo $act_count; ?> added</span>
        <?php endif; ?>
        <?php endif; ?>
      </div>
    </div>

    <form id="step3-form" novalidate>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5">Guardian Name <span class="text-error">*</span></label>
          <input type="text" id="guardian_name" name="guardian_name" value="<?php echo wval($pd, 'guardian_name'); ?>" placeholder="e.g. Suresh Nair"
                 class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
          <p class="field-error text-error text-[11px] mt-1 hidden" id="err-guardian_name"></p>
        </div>
        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5">Relation</label>
          <select name="guardian_relation" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary">
            <?php foreach (['Father','Mother','Grandfather','Grandmother','Uncle','Aunt','Guardian'] as $rel): ?>
              <option value="<?php echo $rel; ?>" <?php echo wsel($pd,'guardian_relation',$rel,'Father'); ?>><?php echo $rel; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5">Guardian Phone</label>
          <input type="text" name="guardian_phone" value="<?php echo wval($pd, 'guardian_phone'); ?>" placeholder="+91 98470 11223"
                 class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
        </div>
        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5">Guardian Email</label>
          <input type="email" name="guardian_email" value="<?php echo wval($pd, 'guardian_email'); ?>" placeholder="parent@example.com"
                 class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
        </div>
        <div class="sm:col-span-2">
          <label class="block font-label-md text-label-md text-on-surface mb-1.5">Address</label>
          <input type="text" name="address" value="<?php echo wval($pd, 'address'); ?>" placeholder="House name, Place, District, PIN"
                 class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
        </div>
      </div>
      <div class="flex justify-between gap-3 mt-6 pt-5 border-t border-outline-variant/50">
        <a href="<?php echo site_url('students/add?step=2'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant text-label-md hover:bg-surface-container-high transition-colors">
          <span class="material-symbols-outlined text-[18px]">arrow_back</span>Back
        </a>
        <div class="flex gap-3">
          <a href="<?php echo site_url('students/wizard_cancel'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant text-label-md hover:bg-surface-container-high transition-colors">Cancel</a>
          <button type="submit" id="btn-save-student" class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer">
            <span class="material-symbols-outlined text-[18px]">check</span>Save Student
          </button>
        </div>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<!-- ═══════════════════════════════════════════════════════════════════════
     WIZARD JAVASCRIPT
════════════════════════════════════════════════════════════════════════ -->
<script>
(function ($) {
    'use strict';

    var BASE_URL  = window.APP_BASE_URL || '';
    var CSRF_NAME = window.CSRF_TOKEN_NAME || 'csrf_token';
    var CSRF_HASH = window.CSRF_HASH || '';
    var CURRENT_YEAR = <?php echo date('Y'); ?>;

    function refreshCsrf(data) {
        if (data && data.csrf_token_name && data.csrf_hash) {
            CSRF_NAME = data.csrf_token_name; CSRF_HASH = data.csrf_hash;
            window.CSRF_TOKEN_NAME = CSRF_NAME; window.CSRF_HASH = CSRF_HASH;
        }
    }

    function csrfData() { return encodeURIComponent(CSRF_NAME) + '=' + encodeURIComponent(CSRF_HASH); }

    function clearErrors() { $('.field-error').addClass('hidden').text(''); $('input,select').removeClass('!border-error'); }

    function showFieldErrors(errors) {
        clearErrors();
        if (errors && typeof errors === 'object') {
            $.each(errors, function (f, msg) {
                $('#err-' + f).text(msg).removeClass('hidden');
                $('#' + f).addClass('!border-error');
            });
        }
    }

    function showAlert(type, message) {
        var $a = $('#wizard-alert');
        $a.removeClass('hidden bg-error-container border-error text-on-error-container bg-secondary-container border-secondary text-on-secondary-container');
        $a.addClass(type === 'error'
            ? 'bg-error-container border-error text-on-error-container'
            : 'bg-secondary-container border-secondary text-on-secondary-container');
        $a.text(message).removeClass('hidden');
        $('html,body').animate({ scrollTop: 0 }, 200);
    }

    function formDataWithCsrf($form) { return $form.serialize() + '&' + csrfData(); }

    /* ═══ STEP 1 ═══════════════════════════════════════════════════════════ */
    var $s1 = $('#step1-form');
    if ($s1.length) {
        $s1.on('submit', function (e) {
            e.preventDefault();
            var $btn = $('#btn-step1-next').prop('disabled', true)
                         .html('<span class="material-symbols-outlined text-[18px] animate-spin">autorenew</span>Saving…');
            clearErrors(); $('#wizard-alert').addClass('hidden');
            $.ajax({ url: BASE_URL + 'students/wizard_step1', method: 'POST', data: formDataWithCsrf($s1), dataType: 'json',
                success: function (r) {
                    refreshCsrf(r);
                    if (r.success) { window.location.href = r.redirect; }
                    else {
                        $btn.prop('disabled', false).html('<span class="material-symbols-outlined text-[18px]">arrow_forward</span>Next');
                        if (r.errors) { showFieldErrors(r.errors); showAlert('error', 'Please fix the errors below.'); }
                        else { showAlert('error', r.message || 'Validation failed.'); }
                    }
                },
                error: function () {
                    $btn.prop('disabled', false).html('<span class="material-symbols-outlined text-[18px]">arrow_forward</span>Next');
                    showAlert('error', 'Server error. Please try again.');
                }
            });
        });
    }

    /* ═══ STEP 2 ═══════════════════════════════════════════════════════════ */
    var $s2 = $('#step2-form');
    if ($s2.length) {

        /* ── No Previous School toggle ─────────────────────────────────── */
        $('#no_previous_school').on('change', function () {
            var $fields = $('#prev-school-fields');
            if (this.checked) {
                $fields.slideUp(200);
            } else {
                $fields.slideDown(200);
            }
        });

        /* ── TC File Upload (fire-and-forget before main submit) ────────── */
        $('#tc_document_file').on('change', function () {
            var file = this.files[0];
            if (!file) return;

            // Client-side pre-check
            var ext = file.name.split('.').pop().toLowerCase();
            if (['pdf','jpg','jpeg','png'].indexOf(ext) === -1) {
                showAlert('error', 'Invalid file type. Allowed: PDF, JPG, PNG.');
                this.value = '';
                return;
            }
            if (file.size > 2 * 1024 * 1024) {
                showAlert('error', 'File is too large. Maximum size is 2 MB.');
                this.value = '';
                return;
            }

            var fd = new FormData();
            fd.append('tc_document', file);
            fd.append(CSRF_NAME, CSRF_HASH);

            $('#tc-btn-label').text('Uploading…');
            $('#tc-upload-progress').removeClass('hidden');
            $('#tc-file-status').addClass('hidden');

            $.ajax({
                url: BASE_URL + 'students/wizard_tc_upload',
                method: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (r) {
                    refreshCsrf(r);
                    $('#tc-btn-label').text('Change File');
                    $('#tc-upload-progress').addClass('hidden');
                    if (r.success) {
                        $('#tc_temp_path').val(r.temp_path);
                        $('#tc-file-name').text(r.display_name);
                        $('#tc-file-status').removeClass('hidden');
                    } else {
                        showAlert('error', r.error || 'Upload failed. Please try again.');
                        $('#tc-btn-label').text('Choose File');
                    }
                },
                error: function () {
                    $('#tc-btn-label').text('Choose File');
                    $('#tc-upload-progress').addClass('hidden');
                    showAlert('error', 'Upload failed. Please check your connection.');
                }
            });
        });

        /* Remove TC file */
        $(document).on('click', '#tc-file-remove', function () {
            $('#tc_temp_path').val('');
            $('#tc-file-status').addClass('hidden');
            $('#tc-btn-label').text('Choose File');
            $('#tc_document_file').val('');
        });

        /* ── Dynamic Academic Activity Rows ────────────────────────────── */
        var ACADEMIC_TYPES = <?php echo json_encode($academic_types); ?>;

        function academicRowHtml(idx) {
            var typeOpts = ACADEMIC_TYPES.map(function (t) {
                return '<option value="' + t + '">' + t + '</option>';
            }).join('');
            return '<div class="academic-activity-row grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-2 p-3 rounded-lg bg-surface-container-low border border-outline-variant/40">'
                + '<div><label class="block font-label-md text-label-md text-on-surface-variant mb-1">Type</label>'
                + '<select name="academic_activities[' + idx + '][activity_type]" class="w-full px-2 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md text-sm focus:ring-2 focus:ring-primary/10 focus:border-primary">' + typeOpts + '</select></div>'
                + '<div><label class="block font-label-md text-label-md text-on-surface-variant mb-1">Activity Name <span class="text-error">*</span></label>'
                + '<input type="text" name="academic_activities[' + idx + '][activity_name]" placeholder="Name / Title" class="w-full px-2 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md text-sm focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/></div>'
                + '<div><label class="block font-label-md text-label-md text-on-surface-variant mb-1">Position / Result</label>'
                + '<input type="text" name="academic_activities[' + idx + '][position_result]" placeholder="e.g. 1st" class="w-full px-2 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md text-sm focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/></div>'
                + '<div><label class="block font-label-md text-label-md text-on-surface-variant mb-1">Year</label>'
                + '<input type="number" name="academic_activities[' + idx + '][year]" placeholder="' + CURRENT_YEAR + '" min="1990" max="' + CURRENT_YEAR + '" class="w-full px-2 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md text-sm focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/></div>'
                + '<div class="flex flex-col gap-1"><label class="block font-label-md text-label-md text-on-surface-variant mb-1">Description</label>'
                + '<div class="flex gap-1"><input type="text" name="academic_activities[' + idx + '][description]" placeholder="Brief description" class="flex-1 min-w-0 px-2 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md text-sm focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>'
                + '<button type="button" class="remove-activity shrink-0 w-8 h-9 flex items-center justify-center rounded-lg border border-error/30 text-error hover:bg-error-container transition-colors"><span class="material-symbols-outlined text-[16px]">remove</span></button></div></div>'
                + '</div>';
        }

        $('#add-academic-activity').on('click', function () {
            var idx = $('#academic-activities-list .academic-activity-row').length;
            $('#academic-activities-list').append(academicRowHtml(idx));
        });

        $(document).on('click', '.remove-activity', function () {
            var $list = $('#academic-activities-list');
            $(this).closest('.academic-activity-row').remove();
            // Re-index names
            $list.find('.academic-activity-row').each(function (i) {
                $(this).find('[name]').each(function () {
                    this.name = this.name.replace(/academic_activities\[\d+\]/, 'academic_activities[' + i + ']');
                });
            });
        });

        /* ── Dynamic Extracurricular Activity Rows ──────────────────────── */
        var EXTRA_TYPES = <?php echo json_encode($extra_types); ?>;
        var LEVELS      = <?php echo json_encode($levels); ?>;

        function extraRowHtml(idx) {
            var typeOpts  = EXTRA_TYPES.map(function (t) { return '<option value="' + t + '">' + t + '</option>'; }).join('');
            var levelOpts = '<option value="">Select</option>' + LEVELS.map(function (l) { return '<option value="' + l + '">' + l + '</option>'; }).join('');
            return '<div class="extra-activity-row grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-2 p-3 rounded-lg bg-surface-container-low border border-outline-variant/40">'
                + '<div><label class="block font-label-md text-label-md text-on-surface-variant mb-1">Type</label>'
                + '<select name="extracurricular[' + idx + '][activity_type]" class="w-full px-2 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md text-sm focus:ring-2 focus:ring-primary/10 focus:border-primary">' + typeOpts + '</select></div>'
                + '<div><label class="block font-label-md text-label-md text-on-surface-variant mb-1">Activity Name <span class="text-error">*</span></label>'
                + '<input type="text" name="extracurricular[' + idx + '][activity_name]" placeholder="Name" class="w-full px-2 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md text-sm focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/></div>'
                + '<div><label class="block font-label-md text-label-md text-on-surface-variant mb-1">Level</label>'
                + '<select name="extracurricular[' + idx + '][level]" class="w-full px-2 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md text-sm focus:ring-2 focus:ring-primary/10 focus:border-primary">' + levelOpts + '</select></div>'
                + '<div><label class="block font-label-md text-label-md text-on-surface-variant mb-1">Position</label>'
                + '<input type="text" name="extracurricular[' + idx + '][position_result]" placeholder="e.g. Winner" class="w-full px-2 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md text-sm focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/></div>'
                + '<div><label class="block font-label-md text-label-md text-on-surface-variant mb-1">Year</label>'
                + '<input type="number" name="extracurricular[' + idx + '][year]" placeholder="' + CURRENT_YEAR + '" min="1990" max="' + CURRENT_YEAR + '" class="w-full px-2 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md text-sm focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/></div>'
                + '<div class="flex flex-col gap-1"><label class="block font-label-md text-label-md text-on-surface-variant mb-1">Description</label>'
                + '<div class="flex gap-1"><input type="text" name="extracurricular[' + idx + '][description]" placeholder="Optional" class="flex-1 min-w-0 px-2 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md text-sm focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>'
                + '<button type="button" class="remove-extra shrink-0 w-8 h-9 flex items-center justify-center rounded-lg border border-error/30 text-error hover:bg-error-container transition-colors"><span class="material-symbols-outlined text-[16px]">remove</span></button></div></div>'
                + '</div>';
        }

        $('#add-extra-activity').on('click', function () {
            var idx = $('#extracurricular-list .extra-activity-row').length;
            $('#extracurricular-list').append(extraRowHtml(idx));
        });

        $(document).on('click', '.remove-extra', function () {
            var $list = $('#extracurricular-list');
            $(this).closest('.extra-activity-row').remove();
            $list.find('.extra-activity-row').each(function (i) {
                $(this).find('[name]').each(function () {
                    this.name = this.name.replace(/extracurricular\[\d+\]/, 'extracurricular[' + i + ']');
                });
            });
        });

        /* ── Client-side validation helpers ────────────────────────────── */
        function validateStep2() {
            var ok = true;
            clearErrors();

            if (!$('#class_id').val()) {
                $('#err-class_id').text('Please select a Class.').removeClass('hidden');
                $('#class_id').addClass('!border-error');
                ok = false;
            }

            var noPrev = $('#no_previous_school').is(':checked');
            if (!noPrev) {
                if (!$.trim($('#prev_school_name').val())) {
                    $('#err-prev_school_name').text('Previous School Name is required.').removeClass('hidden');
                    $('#prev_school_name').addClass('!border-error');
                    ok = false;
                }
                var pct = $('#prev_percentage').val();
                if (pct !== '' && (isNaN(parseFloat(pct)) || parseFloat(pct) < 0 || parseFloat(pct) > 100)) {
                    $('#err-prev_percentage').text('Percentage must be between 0 and 100.').removeClass('hidden');
                    $('#prev_percentage').addClass('!border-error');
                    ok = false;
                }
            }
            return ok;
        }

        /* ── Step 2 Submit ─────────────────────────────────────────────── */
        $s2.on('submit', function (e) {
            e.preventDefault();
            if (!validateStep2()) { showAlert('error', 'Please fix the errors below.'); return; }

            var $btn = $('#btn-step2-next').prop('disabled', true)
                         .html('<span class="material-symbols-outlined text-[18px] animate-spin">autorenew</span>Saving…');
            $('#wizard-alert').addClass('hidden');

            $.ajax({ url: BASE_URL + 'students/wizard_step2', method: 'POST', data: formDataWithCsrf($s2), dataType: 'json',
                success: function (r) {
                    refreshCsrf(r);
                    if (r.success) { window.location.href = r.redirect; }
                    else if (r.redirect) { window.location.href = r.redirect; }
                    else {
                        $btn.prop('disabled', false).html('<span class="material-symbols-outlined text-[18px]">arrow_forward</span>Next');
                        if (r.errors) { showFieldErrors(r.errors); showAlert('error', 'Please fix the errors below.'); }
                        else { showAlert('error', r.message || 'Validation failed.'); }
                    }
                },
                error: function () {
                    $btn.prop('disabled', false).html('<span class="material-symbols-outlined text-[18px]">arrow_forward</span>Next');
                    showAlert('error', 'Server error. Please try again.');
                }
            });
        });
    } // end step 2

    /* ═══ STEP 3 ═══════════════════════════════════════════════════════════ */
    var $s3      = $('#step3-form');
    var _saved3  = false;

    if ($s3.length) {
        $s3.on('submit', function (e) {
            e.preventDefault();
            if (_saved3) return;
            _saved3 = true;
            var $btn = $('#btn-save-student').prop('disabled', true)
                         .html('<span class="material-symbols-outlined text-[18px] animate-spin">autorenew</span>Saving…');
            $('#wizard-alert').addClass('hidden');
            $.ajax({ url: BASE_URL + 'students/wizard_save', method: 'POST', data: formDataWithCsrf($s3), dataType: 'json',
                success: function (r) {
                    refreshCsrf(r);
                    if (r.success) { window.location.href = r.redirect; }
                    else if (r.redirect) { window.location.href = r.redirect; }
                    else {
                        _saved3 = false;
                        $btn.prop('disabled', false).html('<span class="material-symbols-outlined text-[18px]">check</span>Save Student');
                        if (r.errors) { showFieldErrors(r.errors); showAlert('error', 'Please fix the errors below.'); }
                        else { showAlert('error', r.message || 'Failed to save. Please try again.'); }
                    }
                },
                error: function () {
                    _saved3 = false;
                    $btn.prop('disabled', false).html('<span class="material-symbols-outlined text-[18px]">check</span>Save Student');
                    showAlert('error', 'Server error. Please try again.');
                }
            });
        });
    }

}(jQuery));
</script>
