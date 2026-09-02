<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
  $fullName = ($student) ? trim($student->first_name . ' ' . $student->last_name) : 'Student Profile';
  $nameParts = explode(' ', $fullName);
  $initials = '';
  foreach ($nameParts as $np) { if (!empty($np)) $initials .= strtoupper($np[0]); }
  if (strlen($initials) > 2) $initials = substr($initials, 0, 2);
  $status = (isset($student->status) && $student->status == 1) ? 'Active' : 'Inactive / Transferred';
  $statusBadge = ($status == 'Active')
    ? '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-secondary-container text-on-secondary-container">Active</span>'
    : '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-surface-container-high text-on-surface-variant">Inactive / Transferred</span>';
  $classDisplay = (isset($student->class_name) ? $student->class_name : '') . ' ' . (isset($student->section_name) ? $student->section_name : '');
?>

  <!-- Header Card -->
  <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-5 mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-4">
      <?php if (!empty($student->photo) && file_exists(FCPATH . 'uploads/students/' . $student->photo)): ?>
        <img src="<?php echo base_url('uploads/students/' . $student->photo); ?>" alt="<?php echo html_escape($fullName); ?>" class="w-16 h-16 rounded-xl object-cover shrink-0 border border-outline-variant/60 shadow-sm"/>
      <?php else: ?>
        <div class="w-16 h-16 rounded-xl bg-primary-fixed text-primary flex items-center justify-center text-2xl font-bold shrink-0"><?php echo html_escape($initials); ?></div>
      <?php endif; ?>
      <div class="min-w-0">
        <div class="flex items-center gap-2.5 flex-wrap">
          <h2 class="font-headline-md text-headline-md text-on-surface"><?php echo html_escape($fullName); ?></h2>
          <?php echo $statusBadge; ?>
        </div>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">
          <span class="font-medium text-on-surface"><?php echo html_escape($student->admission_number); ?></span>
          <?php if (!empty($student->roll_number)): ?> · Roll No. <span class="font-medium text-on-surface"><?php echo html_escape($student->roll_number); ?></span><?php endif; ?>
          · Class <span class="font-medium text-on-surface"><?php echo html_escape($classDisplay ?: 'Not assigned'); ?></span>
          · Session <span class="font-medium text-on-surface"><?php echo html_escape(isset($student->year_name) ? $student->year_name : '2026-2027'); ?></span>
        </p>
      </div>
    </div>
    <div class="flex items-center gap-2 flex-wrap shrink-0">
      <a href="<?php echo site_url('students/edit/' . $student_id); ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined text-[18px]">edit</span>Edit</a>
      <a href="<?php echo site_url('students/id_cards?student_id=' . (int)$student->student_id); ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-secondary text-on-secondary text-label-md hover:bg-on-secondary-fixed-variant transition-colors shadow-sm"><span class="material-symbols-outlined text-[18px]">badge</span>ID Card</a>
      <?php if (!empty($student->transfer)): ?>
        <a href="<?php echo site_url('students/tc/' . $student->transfer->transfer_id); ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-tertiary text-on-tertiary text-label-md hover:opacity-90 transition-opacity"><span class="material-symbols-outlined text-[18px]">description</span>Print TC</a>
      <?php endif; ?>
      <a href="<?php echo site_url('students'); ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined text-[18px]">arrow_back</span>Back</a>
    </div>
  </div>

  <!-- Interactive Profile Tabs -->
  <div class="flex gap-2 border-b border-outline-variant/60 mb-6 overflow-x-auto" id="profile-tabs">
    <button onclick="switchTab('overview')" class="tab-btn px-4 py-2.5 text-body-md font-medium border-b-2 border-secondary text-primary cursor-pointer" data-tab="overview">Overview</button>
    <button onclick="switchTab('personal')" class="tab-btn px-4 py-2.5 text-body-md text-on-surface-variant hover:text-on-surface border-b-2 border-transparent cursor-pointer" data-tab="personal">Personal Info</button>
    <button onclick="switchTab('guardian')" class="tab-btn px-4 py-2.5 text-body-md text-on-surface-variant hover:text-on-surface border-b-2 border-transparent cursor-pointer" data-tab="guardian">Guardian & Address</button>
    <button onclick="switchTab('academic')" class="tab-btn px-4 py-2.5 text-body-md text-on-surface-variant hover:text-on-surface border-b-2 border-transparent cursor-pointer" data-tab="academic">Academic & Promotion History</button>
    <button onclick="switchTab('documents')" class="tab-btn px-4 py-2.5 text-body-md text-on-surface-variant hover:text-on-surface border-b-2 border-transparent cursor-pointer" data-tab="documents">Documents (<?php echo count($student->documents); ?>)</button>
    <button onclick="switchTab('attendance')" class="tab-btn px-4 py-2.5 text-body-md text-on-surface-variant hover:text-on-surface border-b-2 border-transparent cursor-pointer" data-tab="attendance">Attendance Summary</button>
    <button onclick="switchTab('fees')" class="tab-btn px-4 py-2.5 text-body-md text-on-surface-variant hover:text-on-surface border-b-2 border-transparent cursor-pointer" data-tab="fees">Fees & Finance</button>
    <button onclick="switchTab('transfer')" class="tab-btn px-4 py-2.5 text-body-md text-on-surface-variant hover:text-on-surface border-b-2 border-transparent cursor-pointer" data-tab="transfer">Transfer / TC</button>
  </div>

  <!-- TAB 1: OVERVIEW -->
  <div id="tab-overview" class="tab-pane space-y-5">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
      
      <!-- Left Column: Personal & Academic Quick Summary -->
      <div class="lg:col-span-2 space-y-5">
        
        <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
          <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/50">
            <h3 class="font-headline-md text-headline-md text-on-surface flex items-center gap-2">
              <span class="material-symbols-outlined text-primary text-[20px]">person</span>Personal & Admission Details
            </h3>
            <a href="<?php echo site_url('students/edit/' . $student_id); ?>" class="text-label-md text-primary hover:underline">Edit</a>
          </div>
          <div class="p-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-body-md">
              <div><div class="text-on-surface-variant text-[12px]">Admission Number</div><div class="font-medium text-on-surface"><?php echo html_escape($student->admission_number); ?></div></div>
              <div><div class="text-on-surface-variant text-[12px]">Roll Number</div><div class="font-medium text-on-surface"><?php echo html_escape($student->roll_number ?: '—'); ?></div></div>
              <div><div class="text-on-surface-variant text-[12px]">Gender</div><div class="font-medium text-on-surface"><?php echo html_escape($student->gender); ?></div></div>
              <div><div class="text-on-surface-variant text-[12px]">Date of Birth</div><div class="font-medium text-on-surface"><?php echo date('d M Y', strtotime($student->date_of_birth)); ?></div></div>
              <div><div class="text-on-surface-variant text-[12px]">Blood Group</div><div class="font-medium text-on-surface"><?php echo html_escape($student->blood_group ?: '—'); ?></div></div>
              <div><div class="text-on-surface-variant text-[12px]">Nationality</div><div class="font-medium text-on-surface"><?php echo html_escape($student->nationality ?: 'Indian'); ?></div></div>
              <div><div class="text-on-surface-variant text-[12px]">Class & Section</div><div class="font-medium text-on-surface"><?php echo html_escape($classDisplay ?: '—'); ?></div></div>
              <div><div class="text-on-surface-variant text-[12px]">Academic Session</div><div class="font-medium text-on-surface"><?php echo html_escape($student->year_name ?: '2026-2027'); ?></div></div>
              <div><div class="text-on-surface-variant text-[12px]">Admission Date</div><div class="font-medium text-on-surface"><?php echo date('d M Y', strtotime($student->created_at)); ?></div></div>
            </div>
          </div>
        </div>

        <!-- Attendance Stats Card -->
        <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
          <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/50">
            <h3 class="font-headline-md text-headline-md text-on-surface flex items-center gap-2">
              <span class="material-symbols-outlined text-secondary text-[20px]">fact_check</span>Attendance Overview
            </h3>
            <span class="text-label-md font-semibold text-secondary"><?php echo $student->attendance->percentage; ?>% Attendance</span>
          </div>
          <div class="p-5">
            <div class="grid grid-cols-3 gap-4 text-center">
              <div class="p-3.5 rounded-lg bg-surface-container-low border border-outline-variant/30">
                <div class="text-title-lg font-bold text-on-surface"><?php echo $student->attendance->total_days; ?></div>
                <div class="text-[12px] text-on-surface-variant mt-0.5">Total Working Days</div>
              </div>
              <div class="p-3.5 rounded-lg bg-secondary-container/20 border border-secondary/20">
                <div class="text-title-lg font-bold text-secondary"><?php echo $student->attendance->present; ?></div>
                <div class="text-[12px] text-on-secondary-container mt-0.5">Days Present</div>
              </div>
              <div class="p-3.5 rounded-lg bg-error-container/20 border border-error/20">
                <div class="text-title-lg font-bold text-error"><?php echo $student->attendance->absent; ?></div>
                <div class="text-[12px] text-on-error-container mt-0.5">Days Absent</div>
              </div>
            </div>
            <div class="w-full bg-surface-container-high h-2 rounded-full overflow-hidden mt-4">
              <div class="bg-secondary h-full rounded-full transition-all" style="width: <?php echo min(100, max(0, $student->attendance->percentage)); ?>%"></div>
            </div>
          </div>
        </div>

      </div>

      <!-- Right Column: Guardian, Address & Documents -->
      <div class="space-y-5">
        
        <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
          <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/50">
            <h3 class="font-headline-md text-headline-md text-on-surface flex items-center gap-2">
              <span class="material-symbols-outlined text-primary text-[20px]">family_restroom</span>Parent / Guardian
            </h3>
          </div>
          <div class="p-5 space-y-3 text-body-md">
            <div>
              <div class="text-[12px] text-on-surface-variant">Guardian Name & Relation</div>
              <div class="font-medium text-on-surface"><?php echo html_escape($student->guardian_name); ?> (<?php echo html_escape($student->guardian_relation ?: 'Father'); ?>)</div>
            </div>
            <div>
              <div class="text-[12px] text-on-surface-variant">Contact Phone</div>
              <div class="font-medium text-on-surface"><?php echo html_escape($student->guardian_phone); ?></div>
            </div>
            <div>
              <div class="text-[12px] text-on-surface-variant">Email Address</div>
              <div class="font-medium text-on-surface"><?php echo html_escape($student->guardian_email ?: '—'); ?></div>
            </div>
            <div>
              <div class="text-[12px] text-on-surface-variant">Residential Address</div>
              <div class="text-on-surface text-[13px] leading-relaxed"><?php echo html_escape($student->address ?: '—'); ?></div>
            </div>
          </div>
        </div>

        <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
          <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/50">
            <h3 class="font-headline-md text-headline-md text-on-surface flex items-center gap-2">
              <span class="material-symbols-outlined text-primary text-[20px]">folder</span>Documents
            </h3>
            <button onclick="switchTab('documents')" class="text-label-md text-primary hover:underline">Manage</button>
          </div>
          <div class="p-5">
            <?php if (!empty($student->documents)): ?>
              <ul class="divide-y divide-outline-variant/30 text-body-md">
                <?php foreach ($student->documents as $doc): ?>
                  <li class="py-2.5 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                      <span class="material-symbols-outlined text-primary text-[18px]">description</span>
                      <div>
                        <div class="font-medium text-on-surface text-[13px]"><?php echo html_escape($doc->document_name); ?></div>
                        <div class="text-[11px] text-on-surface-variant"><?php echo html_escape($doc->document_type); ?></div>
                      </div>
                    </div>
                    <span class="material-symbols-outlined text-secondary text-[18px]">verified</span>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php else: ?>
              <p class="text-body-md text-on-surface-variant text-center py-2">No documents uploaded yet.</p>
            <?php endif; ?>
          </div>
        </div>

      </div>

    </div>
  </div>

  <!-- TAB 2: PERSONAL INFO -->
  <div id="tab-personal" class="tab-pane hidden space-y-5">
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-6">
      <h3 class="font-headline-md text-headline-md text-on-surface mb-4">Complete Personal Information</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-body-md">
        <div class="space-y-4">
          <div><span class="text-on-surface-variant text-sm block">Full Name</span><span class="font-medium text-on-surface"><?php echo html_escape($student->first_name . ' ' . $student->last_name); ?></span></div>
          <div><span class="text-on-surface-variant text-sm block">First Name</span><span class="font-medium text-on-surface"><?php echo html_escape($student->first_name); ?></span></div>
          <div><span class="text-on-surface-variant text-sm block">Last Name</span><span class="font-medium text-on-surface"><?php echo html_escape($student->last_name); ?></span></div>
          <div><span class="text-on-surface-variant text-sm block">Gender</span><span class="font-medium text-on-surface"><?php echo html_escape($student->gender); ?></span></div>
          <div><span class="text-on-surface-variant text-sm block">Date of Birth</span><span class="font-medium text-on-surface"><?php echo date('d M Y', strtotime($student->date_of_birth)); ?></span></div>
        </div>
        <div class="space-y-4">
          <div><span class="text-on-surface-variant text-sm block">Blood Group</span><span class="font-medium text-on-surface"><?php echo html_escape($student->blood_group ?: '—'); ?></span></div>
          <div><span class="text-on-surface-variant text-sm block">Nationality</span><span class="font-medium text-on-surface"><?php echo html_escape($student->nationality ?: 'Indian'); ?></span></div>
          <div><span class="text-on-surface-variant text-sm block">Religion</span><span class="font-medium text-on-surface"><?php echo html_escape($student->religion ?: '—'); ?></span></div>
          <div><span class="text-on-surface-variant text-sm block">Account Status</span><span class="font-medium text-on-surface"><?php echo $status; ?></span></div>
          <div><span class="text-on-surface-variant text-sm block">Registered In System Since</span><span class="font-medium text-on-surface"><?php echo date('d M Y, h:i A', strtotime($student->created_at)); ?></span></div>
        </div>
      </div>
    </div>
  </div>

  <!-- TAB 3: GUARDIAN & ADDRESS -->
  <div id="tab-guardian" class="tab-pane hidden space-y-5">
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-6">
      <h3 class="font-headline-md text-headline-md text-on-surface mb-4">Guardian & Residential Information</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-body-md">
        <div class="space-y-4">
          <div><span class="text-on-surface-variant text-sm block">Guardian Full Name</span><span class="font-medium text-on-surface"><?php echo html_escape($student->guardian_name); ?></span></div>
          <div><span class="text-on-surface-variant text-sm block">Relationship to Student</span><span class="font-medium text-on-surface"><?php echo html_escape($student->guardian_relation ?: 'Father'); ?></span></div>
          <div><span class="text-on-surface-variant text-sm block">Primary Phone Number</span><span class="font-medium text-on-surface"><?php echo html_escape($student->guardian_phone); ?></span></div>
          <div><span class="text-on-surface-variant text-sm block">Email Address</span><span class="font-medium text-on-surface"><?php echo html_escape($student->guardian_email ?: '—'); ?></span></div>
        </div>
        <div class="space-y-4">
          <div><span class="text-on-surface-variant text-sm block">Full Residential Address</span><span class="font-medium text-on-surface leading-relaxed"><?php echo nl2br(html_escape($student->address ?: 'No address provided.')); ?></span></div>
        </div>
      </div>
    </div>
  </div>

  <!-- TAB 4: ACADEMIC & PROMOTION HISTORY -->
  <div id="tab-academic" class="tab-pane hidden space-y-5">
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-headline-md text-headline-md text-on-surface">Current Academic Enrollment</h3>
        <a href="<?php echo site_url('students/promotion'); ?>" class="text-label-md text-primary hover:underline flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">upgrade</span>Promote Student</a>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="p-4 rounded-lg bg-surface-container-low border border-outline-variant/40">
          <div class="text-[12px] text-on-surface-variant">Class</div>
          <div class="text-title-md font-semibold text-on-surface"><?php echo html_escape($student->class_name ?: '—'); ?></div>
        </div>
        <div class="p-4 rounded-lg bg-surface-container-low border border-outline-variant/40">
          <div class="text-[12px] text-on-surface-variant">Section</div>
          <div class="text-title-md font-semibold text-on-surface">Section <?php echo html_escape($student->section_name ?: '—'); ?></div>
        </div>
        <div class="p-4 rounded-lg bg-surface-container-low border border-outline-variant/40">
          <div class="text-[12px] text-on-surface-variant">Current Academic Year</div>
          <div class="text-title-md font-semibold text-on-surface"><?php echo html_escape($student->year_name ?: '2026-2027'); ?></div>
        </div>
      </div>

      <h4 class="font-title-md text-title-md text-on-surface mb-3">Promotion & Academic History</h4>
      <div class="table-scroll overflow-x-auto border border-outline-variant/40 rounded-lg">
        <table class="w-full data-table zebra border-collapse">
          <thead>
            <tr class="border-b border-outline-variant/60 bg-surface-container-low">
              <th class="text-left px-4 py-2.5 text-label-md text-on-surface-variant">Date</th>
              <th class="text-left px-4 py-2.5 text-label-md text-on-surface-variant">From Class</th>
              <th class="text-left px-4 py-2.5 text-label-md text-on-surface-variant">To Class</th>
              <th class="text-left px-4 py-2.5 text-label-md text-on-surface-variant">Action Type</th>
              <th class="text-left px-4 py-2.5 text-label-md text-on-surface-variant">Remarks</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/40">
            <?php if (empty($student->promotions)): ?>
              <tr>
                <td colspan="5" class="px-4 py-6 text-center text-on-surface-variant text-body-md">Initial registration enrollment record. No prior promotions recorded.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($student->promotions as $p): ?>
                <tr>
                  <td class="px-4 py-3 text-body-md text-on-surface whitespace-nowrap"><?php echo date('d M Y', strtotime($p->promotion_date)); ?></td>
                  <td class="px-4 py-3 text-body-md text-on-surface whitespace-nowrap"><?php echo html_escape($p->from_class . ' ' . $p->from_section . ' (' . $p->from_year . ')'); ?></td>
                  <td class="px-4 py-3 text-body-md text-on-surface whitespace-nowrap font-medium"><?php echo html_escape($p->to_class . ' ' . $p->to_section . ' (' . $p->to_year . ')'); ?></td>
                  <td class="px-4 py-3 text-body-md whitespace-nowrap">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-secondary-container text-on-secondary-container"><?php echo html_escape($p->promotion_type); ?></span>
                  </td>
                  <td class="px-4 py-3 text-body-md text-on-surface-variant"><?php echo html_escape($p->remarks ?: '—'); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- TAB 5: DOCUMENTS -->
  <div id="tab-documents" class="tab-pane hidden space-y-5">
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-6">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
          <h3 class="font-headline-md text-headline-md text-on-surface">Student Document Repository</h3>
          <p class="text-body-md text-on-surface-variant mt-0.5">Upload and manage official certificates, identity proof, and academic documents.</p>
        </div>
      </div>

      <!-- Upload Document Form -->
      <?php echo form_open_multipart('students/upload_document', array('class' => 'p-4 rounded-xl bg-surface-container-low border border-outline-variant/40 mb-6')); ?>
        <input type="hidden" name="student_id" value="<?php echo $student_id; ?>"/>
        <input type="hidden" name="redirect_to" value="<?php echo current_url(); ?>"/>
        <h4 class="font-title-md text-title-md text-on-surface mb-3 flex items-center gap-1.5"><span class="material-symbols-outlined text-[18px]">cloud_upload</span>Upload New Document</h4>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
          <div>
            <label class="block text-label-md text-on-surface mb-1">Document Type *</label>
            <select name="document_type" required class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md">
              <option value="Birth Certificate">Birth Certificate</option>
              <option value="Aadhaar Card / ID Proof">Aadhaar Card / ID Proof</option>
              <option value="Previous School TC">Previous School TC</option>
              <option value="Transfer Certificate">Transfer Certificate</option>
              <option value="Medical Fitness Certificate">Medical Fitness Certificate</option>
              <option value="Passport Photo">Passport Photo</option>
              <option value="Other">Other</option>
            </select>
          </div>
          <div>
            <label class="block text-label-md text-on-surface mb-1">Document Title / Name *</label>
            <input type="text" name="document_name" required placeholder="e.g. Birth Certificate Original" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md"/>
          </div>
          <div>
            <label class="block text-label-md text-on-surface mb-1">Select File</label>
            <input type="file" name="document_file" class="w-full px-3 py-1.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md text-on-surface-variant"/>
          </div>
        </div>
        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-secondary text-on-secondary text-label-md hover:bg-on-secondary-fixed-variant transition-colors cursor-pointer"><span class="material-symbols-outlined text-[18px]">upload</span>Upload Document</button>
      <?php echo form_close(); ?>

      <!-- Documents List Table -->
      <div class="table-scroll overflow-x-auto border border-outline-variant/40 rounded-lg">
        <table class="w-full data-table zebra border-collapse">
          <thead>
            <tr class="border-b border-outline-variant/60 bg-surface-container-low">
              <th class="text-left px-4 py-2.5 text-label-md text-on-surface-variant">Document Name</th>
              <th class="text-left px-4 py-2.5 text-label-md text-on-surface-variant">Document Type</th>
              <th class="text-left px-4 py-2.5 text-label-md text-on-surface-variant">Uploaded On</th>
              <th class="text-right px-4 py-2.5 text-label-md text-on-surface-variant">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/40">
            <?php if (empty($student->documents)): ?>
              <tr>
                <td colspan="4" class="px-4 py-6 text-center text-on-surface-variant text-body-md">No documents currently uploaded for this student.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($student->documents as $doc): ?>
                <tr>
                  <td class="px-4 py-3 text-body-md text-on-surface font-medium whitespace-nowrap">
                    <div class="flex items-center gap-2">
                      <span class="material-symbols-outlined text-primary text-[18px]">description</span>
                      <?php echo html_escape($doc->document_name); ?>
                    </div>
                  </td>
                  <td class="px-4 py-3 text-body-md text-on-surface-variant whitespace-nowrap"><?php echo html_escape($doc->document_type); ?></td>
                  <td class="px-4 py-3 text-body-md text-on-surface-variant whitespace-nowrap"><?php echo date('d M Y', strtotime($doc->created_at)); ?></td>
                  <td class="px-4 py-3 text-body-md text-right whitespace-nowrap">
                    <a href="<?php echo base_url($doc->file_path); ?>" target="_blank" class="px-2.5 py-1 rounded bg-surface-container-high text-on-surface text-label-md hover:bg-surface-container-highest transition-colors inline-flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">visibility</span>View</a>
                    <a href="<?php echo site_url('students/delete_document/' . $doc->document_id . '?redirect_to=' . urlencode(current_url())); ?>" onclick="return confirm('Remove this document record?')" class="px-2.5 py-1 rounded text-error hover:bg-error-container/20 transition-colors inline-flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">delete</span>Delete</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- TAB 6: ATTENDANCE SUMMARY -->
  <div id="tab-attendance" class="tab-pane hidden space-y-5">
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-6 space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-outline-variant/50 pb-4">
        <div>
          <h3 class="font-headline-md text-headline-md text-on-surface">Student Attendance Profile</h3>
          <p class="text-body-md text-on-surface-variant mt-0.5">Summary, monthly trend, and historical logs for <?php echo html_escape($fullName); ?>.</p>
        </div>
        <div class="flex items-center gap-2">
          <a href="<?php echo site_url('attendance/calendar?student_id=' . $student_id . '&class_id=' . $student->class_id . '&section_id=' . $student->section_id); ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold hover:bg-on-secondary-fixed-variant transition-colors shadow-sm">
            <span class="material-symbols-outlined text-[18px]">calendar_month</span>View Interactive Calendar
          </a>
        </div>
      </div>

      <!-- 1. Attendance Summary Metrics -->
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="p-4 rounded-xl bg-surface-container-low border border-outline-variant/30 text-center">
          <div class="text-2xl font-bold text-on-surface"><?php echo $student->attendance->total_days; ?></div>
          <div class="text-[12px] text-on-surface-variant mt-1">Total Academic Days</div>
        </div>
        <div class="p-4 rounded-xl bg-secondary-container/20 border border-secondary/20 text-center">
          <div class="text-2xl font-bold text-secondary"><?php echo $student->attendance->present; ?></div>
          <div class="text-[12px] text-on-secondary-container mt-1">Days Present</div>
        </div>
        <div class="p-4 rounded-xl bg-error-container/20 border border-error/20 text-center">
          <div class="text-2xl font-bold text-error"><?php echo $student->attendance->absent; ?></div>
          <div class="text-[12px] text-on-error-container mt-1">Days Absent</div>
        </div>
        <div class="p-4 rounded-xl bg-amber-100 dark:bg-amber-950/30 border border-amber-300 text-center">
          <div class="text-2xl font-bold text-amber-900 dark:text-amber-300"><?php echo isset($student->attendance->late) ? $student->attendance->late : 0; ?></div>
          <div class="text-[12px] text-on-surface-variant mt-1">Late Arrivals</div>
        </div>
        <div class="p-4 rounded-xl bg-primary-fixed/20 border border-primary/20 text-center">
          <div class="text-2xl font-bold text-primary"><?php echo isset($student->attendance->excused) ? $student->attendance->excused : 0; ?></div>
          <div class="text-[12px] text-on-surface-variant mt-1">Excused / Leave</div>
        </div>
        <div class="p-4 rounded-xl bg-secondary-container/30 border border-secondary/40 text-center">
          <div class="text-2xl font-bold text-secondary"><?php echo $student->attendance->percentage; ?>%</div>
          <div class="text-[12px] text-on-surface-variant mt-1">Overall Percentage</div>
        </div>
      </div>

      <!-- 2. Monthly Attendance Breakdown Table -->
      <div>
        <h4 class="font-title-md text-title-md font-bold text-on-surface mb-3 flex items-center gap-2">
          <span class="material-symbols-outlined text-primary text-[20px]">date_range</span>Month-wise Attendance Breakdown
        </h4>
        <div class="overflow-x-auto rounded-xl border border-outline-variant/50">
          <table class="w-full data-table zebra border-collapse text-body-md">
            <thead>
              <tr class="border-b border-outline-variant/60 bg-surface-container-low/50">
                <th class="text-left px-4 py-2.5 text-label-md font-semibold text-on-surface-variant uppercase">Month</th>
                <th class="text-right px-4 py-2.5 text-label-md font-semibold text-secondary uppercase">Present</th>
                <th class="text-right px-4 py-2.5 text-label-md font-semibold text-error uppercase">Absent</th>
                <th class="text-right px-4 py-2.5 text-label-md font-semibold text-amber-600 uppercase">Late</th>
                <th class="text-right px-4 py-2.5 text-label-md font-semibold text-primary uppercase">Excused</th>
                <th class="text-right px-4 py-2.5 text-label-md font-semibold text-on-surface uppercase">Total Days</th>
                <th class="text-right px-4 py-2.5 text-label-md font-semibold text-on-surface uppercase">Percentage</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/30">
              <?php if (empty($student->attendance->monthly)): ?>
                <tr><td colspan="7" class="px-4 py-4 text-center text-on-surface-variant">No monthly attendance logged yet.</td></tr>
              <?php else: ?>
                <?php foreach ($student->attendance->monthly as $m): ?>
                  <tr>
                    <td class="px-4 py-2.5 font-semibold text-on-surface"><?php echo html_escape($m->month_name); ?></td>
                    <td class="px-4 py-2.5 text-right font-semibold text-secondary"><?php echo $m->present_count ?: 0; ?></td>
                    <td class="px-4 py-2.5 text-right font-semibold text-error"><?php echo $m->absent_count ?: 0; ?></td>
                    <td class="px-4 py-2.5 text-right font-semibold text-amber-600"><?php echo $m->late_count ?: 0; ?></td>
                    <td class="px-4 py-2.5 text-right font-semibold text-primary"><?php echo $m->excused_count ?: 0; ?></td>
                    <td class="px-4 py-2.5 text-right font-semibold text-on-surface"><?php echo $m->total_days; ?></td>
                    <td class="px-4 py-2.5 text-right font-bold <?php echo ($m->percentage >= 90) ? 'text-secondary' : 'text-amber-600'; ?>"><?php echo $m->percentage; ?>%</td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- 3. Recent Attendance Activity -->
      <div>
        <h4 class="font-title-md text-title-md font-bold text-on-surface mb-3 flex items-center gap-2">
          <span class="material-symbols-outlined text-secondary text-[20px]">history</span>Recent Attendance Logs
        </h4>
        <div class="overflow-x-auto rounded-xl border border-outline-variant/50 max-h-[300px]">
          <table class="w-full data-table zebra border-collapse text-body-md">
            <thead>
              <tr class="border-b border-outline-variant/60 bg-surface-container-low/50">
                <th class="text-left px-4 py-2.5 text-label-md font-semibold text-on-surface-variant uppercase">Date</th>
                <th class="text-center px-4 py-2.5 text-label-md font-semibold text-on-surface-variant uppercase">Status</th>
                <th class="text-left px-4 py-2.5 text-label-md font-semibold text-on-surface-variant uppercase">Remarks</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/30">
              <?php if (empty($student->attendance->recent_records)): ?>
                <tr><td colspan="3" class="px-4 py-4 text-center text-on-surface-variant">No attendance records logged.</td></tr>
              <?php else: ?>
                <?php foreach ($student->attendance->recent_records as $rec): ?>
                  <?php
                    $badgeClass = 'bg-secondary-container text-on-secondary-container';
                    if ($rec->attendance_status === 'Absent') $badgeClass = 'bg-error-container text-on-error-container';
                    elseif ($rec->attendance_status === 'Late') $badgeClass = 'bg-amber-100 text-amber-900';
                    elseif (in_array($rec->attendance_status, array('Excused', 'Leave'))) $badgeClass = 'bg-primary-fixed text-on-primary-fixed';
                  ?>
                  <tr>
                    <td class="px-4 py-2.5 font-mono text-on-surface"><?php echo date('d M Y', strtotime($rec->attendance_date)); ?></td>
                    <td class="px-4 py-2.5 text-center">
                      <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold <?php echo $badgeClass; ?>">
                        <?php echo html_escape($rec->attendance_status); ?>
                      </span>
                    </td>
                    <td class="px-4 py-2.5 text-on-surface-variant"><?php echo html_escape($rec->remarks ?: '—'); ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- TAB: FEES & FINANCE -->
  <div id="tab-fees" class="tab-pane hidden space-y-5">
    <?php if (isset($student->fee_profile)): ?>
      <?php $fp = $student->fee_profile; ?>
      
      <!-- Fee Summary KPI Grid -->
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="p-3.5 rounded-xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 text-center">
          <span class="text-[11px] text-on-surface-variant uppercase font-semibold block">Total Assigned</span>
          <div class="text-lg font-bold font-mono text-on-surface mt-1">₹<?php echo number_format($fp->summary->total_assigned, 2); ?></div>
        </div>
        <div class="p-3.5 rounded-xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 text-center">
          <span class="text-[11px] text-on-surface-variant uppercase font-semibold block">Total Paid</span>
          <div class="text-lg font-bold font-mono text-secondary mt-1">₹<?php echo number_format($fp->summary->total_paid, 2); ?></div>
        </div>
        <div class="p-3.5 rounded-xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 text-center">
          <span class="text-[11px] text-on-surface-variant uppercase font-semibold block">Discounts</span>
          <div class="text-lg font-bold font-mono text-primary mt-1">₹<?php echo number_format($fp->summary->total_discount, 2); ?></div>
        </div>
        <div class="p-3.5 rounded-xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 text-center">
          <span class="text-[11px] text-on-surface-variant uppercase font-semibold block">Concessions</span>
          <div class="text-lg font-bold font-mono text-amber-700 mt-1">₹<?php echo number_format($fp->summary->total_concession, 2); ?></div>
        </div>
        <div class="p-3.5 rounded-xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 text-center">
          <span class="text-[11px] text-on-surface-variant uppercase font-semibold block">Total Due</span>
          <div class="text-lg font-bold font-mono text-amber-900 mt-1">₹<?php echo number_format($fp->summary->total_due, 2); ?></div>
        </div>
        <div class="p-3.5 rounded-xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 text-center">
          <span class="text-[11px] text-on-surface-variant uppercase font-semibold block">Overdue Amount</span>
          <div class="text-lg font-bold font-mono text-error mt-1">₹<?php echo number_format($fp->summary->total_overdue, 2); ?></div>
        </div>
      </div>

      <!-- Itemized Invoices Table -->
      <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/50">
          <h3 class="font-headline-md text-headline-md text-on-surface flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-[20px]">receipt_long</span>Assigned Fee Invoices
          </h3>
          <a href="<?php echo site_url('fees/collection?student_id=' . $student_id); ?>" class="inline-flex items-center gap-1 px-3.5 py-1.5 rounded-lg bg-secondary text-on-secondary text-label-md hover:bg-on-secondary-fixed-variant transition-colors shadow-sm font-semibold">
            <span class="material-symbols-outlined text-[16px]">add_card</span>Collect Payment
          </a>
        </div>
        <div class="table-scroll overflow-x-auto">
          <table class="w-full data-table zebra border-collapse text-body-md">
            <thead>
              <tr class="border-b border-outline-variant/60 bg-surface-container-low/50">
                <th class="text-left px-4 py-2.5 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Invoice #</th>
                <th class="text-left px-4 py-2.5 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Fee Particulars</th>
                <th class="text-right px-4 py-2.5 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Original</th>
                <th class="text-right px-4 py-2.5 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Discount</th>
                <th class="text-right px-4 py-2.5 text-label-md font-semibold text-secondary uppercase whitespace-nowrap">Paid</th>
                <th class="text-right px-4 py-2.5 text-label-md font-semibold text-error uppercase whitespace-nowrap">Due</th>
                <th class="text-center px-4 py-2.5 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Due Date</th>
                <th class="text-center px-4 py-2.5 text-label-md font-semibold text-on-surface uppercase whitespace-nowrap">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/40">
              <?php if (empty($fp->fees)): ?>
                <tr><td colspan="8" class="px-4 py-6 text-center text-on-surface-variant">No fee invoices assigned to this student.</td></tr>
              <?php else: ?>
                <?php foreach ($fp->fees as $f): ?>
                  <?php
                    $badgeClass = 'bg-surface-container-high text-on-surface-variant';
                    if ($f->payment_status === 'Paid') $badgeClass = 'bg-secondary-container text-on-secondary-container';
                    elseif ($f->payment_status === 'Partially Paid') $badgeClass = 'bg-amber-100 text-amber-900 font-semibold';
                    elseif ($f->payment_status === 'Overdue') $badgeClass = 'bg-error-container text-on-error-container font-semibold';
                  ?>
                  <tr>
                    <td class="px-4 py-2.5 font-mono font-bold text-primary whitespace-nowrap"><?php echo html_escape($f->invoice_no); ?></td>
                    <td class="px-4 py-2.5 font-bold text-on-surface whitespace-nowrap"><?php echo html_escape($f->category_name); ?></td>
                    <td class="px-4 py-2.5 text-right font-mono whitespace-nowrap">₹<?php echo number_format($f->original_amount, 2); ?></td>
                    <td class="px-4 py-2.5 text-right font-mono text-on-surface-variant whitespace-nowrap">
                      <?php echo ($f->discount_amount > 0) ? '₹' . number_format($f->discount_amount, 2) : '—'; ?>
                    </td>
                    <td class="px-4 py-2.5 text-right font-mono font-bold text-secondary whitespace-nowrap">₹<?php echo number_format($f->paid_amount, 2); ?></td>
                    <td class="px-4 py-2.5 text-right font-mono font-bold <?php echo ($f->due_amount > 0) ? 'text-error' : 'text-on-surface-variant'; ?> whitespace-nowrap">₹<?php echo number_format($f->due_amount, 2); ?></td>
                    <td class="px-4 py-2.5 text-center font-mono text-[12px] whitespace-nowrap"><?php echo date('d M Y', strtotime($f->due_date)); ?></td>
                    <td class="px-4 py-2.5 text-center whitespace-nowrap">
                      <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold <?php echo $badgeClass; ?>">
                        <?php echo html_escape($f->payment_status); ?>
                      </span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Payment Receipts History Table -->
      <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
        <div class="px-5 py-4 border-b border-outline-variant/50">
          <h3 class="font-headline-md text-headline-md text-on-surface flex items-center gap-2">
            <span class="material-symbols-outlined text-secondary text-[20px]">payments</span>Payment Transactions & Receipts
          </h3>
        </div>
        <div class="table-scroll overflow-x-auto">
          <table class="w-full data-table zebra border-collapse text-body-md">
            <thead>
              <tr class="border-b border-outline-variant/60 bg-surface-container-low/50">
                <th class="text-left px-4 py-2.5 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Receipt #</th>
                <th class="text-left px-4 py-2.5 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Particulars</th>
                <th class="text-right px-4 py-2.5 text-label-md font-semibold text-secondary uppercase whitespace-nowrap">Amount Paid</th>
                <th class="text-center px-4 py-2.5 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Mode</th>
                <th class="text-center px-4 py-2.5 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Date</th>
                <th class="text-center px-4 py-2.5 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Receipt</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/40">
              <?php if (empty($fp->payments)): ?>
                <tr><td colspan="6" class="px-4 py-6 text-center text-on-surface-variant">No payment receipts logged yet.</td></tr>
              <?php else: ?>
                <?php foreach ($fp->payments as $p): ?>
                  <tr>
                    <td class="px-4 py-2.5 font-mono font-bold text-primary whitespace-nowrap"><?php echo html_escape($p->receipt_no); ?></td>
                    <td class="px-4 py-2.5 font-medium text-on-surface whitespace-nowrap"><?php echo html_escape($p->category_name); ?></td>
                    <td class="px-4 py-2.5 text-right font-mono font-bold text-secondary whitespace-nowrap">₹<?php echo number_format($p->amount_paid, 2); ?></td>
                    <td class="px-4 py-2.5 text-center whitespace-nowrap">
                      <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-surface-container-high text-on-surface"><?php echo html_escape($p->payment_mode); ?></span>
                    </td>
                    <td class="px-4 py-2.5 text-center font-mono text-[12px] whitespace-nowrap"><?php echo date('d M Y', strtotime($p->payment_date)); ?></td>
                    <td class="px-4 py-2.5 text-center whitespace-nowrap">
                      <a href="<?php echo site_url('fees/receipt/' . $p->payment_id); ?>" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-surface-container-high text-primary hover:bg-primary-fixed transition-colors text-[12px] font-semibold">
                        <span class="material-symbols-outlined text-[15px]">print</span>Print
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <!-- TAB 7: TRANSFER / TC -->
  <div id="tab-transfer" class="tab-pane hidden space-y-5">
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-headline-md text-headline-md text-on-surface">Transfer / School Leaving Certificate</h3>
        <?php if (empty($student->transfer)): ?>
          <a href="<?php echo site_url('students/transfers'); ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-secondary text-on-secondary text-label-md hover:bg-on-secondary-fixed-variant transition-colors"><span class="material-symbols-outlined text-[18px]">post_add</span>Issue TC</a>
        <?php endif; ?>
      </div>

      <?php if (!empty($student->transfer)): ?>
        <div class="p-5 rounded-xl bg-surface-container-low border border-outline-variant/40 space-y-4">
          <div class="flex items-center justify-between border-b border-outline-variant/40 pb-3">
            <div>
              <div class="text-[12px] text-on-surface-variant">Transfer Certificate Number</div>
              <div class="text-title-md font-bold text-primary font-mono"><?php echo html_escape($student->transfer->tc_number); ?></div>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-label-md font-semibold bg-secondary-container text-on-secondary-container">Issued</span>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-body-md">
            <div><span class="text-on-surface-variant text-[12px] block">Date of Issue</span><span class="font-medium text-on-surface"><?php echo date('d M Y', strtotime($student->transfer->transfer_date)); ?></span></div>
            <div><span class="text-on-surface-variant text-[12px] block">Reason for Leaving</span><span class="font-medium text-on-surface"><?php echo html_escape($student->transfer->reason); ?></span></div>
            <div><span class="text-on-surface-variant text-[12px] block">Conduct & Behavior</span><span class="font-medium text-on-surface"><?php echo html_escape($student->transfer->conduct); ?></span></div>
            <div class="sm:col-span-3"><span class="text-on-surface-variant text-[12px] block">Remarks</span><span class="text-on-surface"><?php echo html_escape($student->transfer->remarks ?: 'All school dues cleared.'); ?></span></div>
          </div>
          <div class="pt-3 border-t border-outline-variant/40">
            <a href="<?php echo site_url('students/tc/' . $student->transfer->transfer_id); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-primary text-on-primary text-label-md hover:bg-primary/90 transition-colors shadow-sm"><span class="material-symbols-outlined text-[18px]">print</span>Print Official Transfer Certificate</a>
          </div>
        </div>
      <?php else: ?>
        <div class="p-6 rounded-xl bg-surface-container-low border border-outline-variant/40 text-center">
          <span class="material-symbols-outlined text-[48px] text-on-surface-variant/50 mb-2">school</span>
          <h4 class="font-title-md text-title-md text-on-surface">No Transfer Certificate Issued</h4>
          <p class="text-body-md text-on-surface-variant mt-1 mb-4">This student is currently actively enrolled or has not requested a Transfer Certificate.</p>
          <a href="<?php echo site_url('students/transfers'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-secondary text-on-secondary text-label-md hover:bg-on-secondary-fixed-variant transition-colors shadow-sm"><span class="material-symbols-outlined text-[18px]">add_circle</span>Issue Transfer Certificate</a>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Tab Switching Script -->
  <script>
    function switchTab(tabName) {
      // Hide all panes
      document.querySelectorAll('.tab-pane').forEach(function(pane) {
        pane.classList.add('hidden');
      });
      // Deactivate all buttons
      document.querySelectorAll('.tab-btn').forEach(function(btn) {
        btn.classList.remove('border-secondary', 'text-primary', 'font-medium');
        btn.classList.add('border-transparent', 'text-on-surface-variant');
      });
      // Show selected pane
      var targetPane = document.getElementById('tab-' + tabName);
      if (targetPane) {
        targetPane.classList.remove('hidden');
      }
      // Activate button
      var targetBtn = document.querySelector('.tab-btn[data-tab="' + tabName + '"]');
      if (targetBtn) {
        targetBtn.classList.remove('border-transparent', 'text-on-surface-variant');
        targetBtn.classList.add('border-secondary', 'text-primary', 'font-medium');
      }
    }
  </script>
