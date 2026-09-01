<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

    <div class="flex items-center justify-between gap-4 mb-6">
      <div>
        <h2 class="font-headline-md text-headline-md text-on-surface">Edit Staff Member</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1"><?php echo html_escape($staff->full_name); ?> (<?php echo html_escape($staff->employee_code); ?>)</p>
      </div>
      <div class="flex items-center gap-2">
        <a href="<?php echo site_url('staff/profile/' . $staff_id); ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md hover:bg-surface-container-high transition-colors">
          <span class="material-symbols-outlined text-[18px]">visibility</span>View Profile
        </a>
        <a href="<?php echo site_url('staff'); ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md hover:bg-surface-container-high transition-colors">
          <span class="material-symbols-outlined text-[18px]">arrow_back</span>All Staff
        </a>
      </div>
    </div>

    <?php if (validation_errors() || !empty($doc_errors)): ?>
      <div class="p-4 mb-5 rounded-xl bg-error-container/30 border border-error/30 text-error text-body-md space-y-1">
        <?php echo validation_errors(); ?>
        <?php if (!empty($doc_errors)): ?>
          <?php foreach ($doc_errors as $err): ?>
            <div>• <?php echo html_escape($err); ?></div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <div class="elevation-1 rounded-2xl bg-surface-container-lowest border border-outline-variant/50 p-6 max-w-4xl">
      <?php echo form_open_multipart('staff/edit/' . $staff_id, array('class' => 'space-y-6', 'id' => 'edit_staff_form')); ?>
        
        <!-- SECTION 1: Personal Details -->
        <div>
          <h3 class="font-headline-md text-headline-md text-on-surface mb-3 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-[20px]">person</span>1. Personal Details
          </h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-body-md">
            <div>
              <label class="block text-label-md text-on-surface mb-1">Full Name *</label>
              <input type="text" name="full_name" required value="<?php echo html_escape($staff->full_name); ?>" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
            </div>
            <div>
              <label class="block text-label-md text-on-surface mb-1">Gender *</label>
              <select name="gender" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest">
                <option value="Male" <?php echo ($staff->gender === 'Male') ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo ($staff->gender === 'Female') ? 'selected' : ''; ?>>Female</option>
                <option value="Other" <?php echo ($staff->gender === 'Other') ? 'selected' : ''; ?>>Other</option>
              </select>
            </div>
            <div>
              <label class="block text-label-md text-on-surface mb-1">Date of Birth</label>
              <input type="date" name="date_of_birth" value="<?php echo html_escape($staff->date_of_birth); ?>" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
            </div>
            <div>
              <label class="block text-label-md text-on-surface mb-1">Blood Group</label>
              <select name="blood_group" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest">
                <option value="">Select Blood Group</option>
                <?php foreach (array('A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-') as $bg): ?>
                  <option value="<?php echo $bg; ?>" <?php echo ($staff->blood_group === $bg) ? 'selected' : ''; ?>><?php echo $bg; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>

        <!-- SECTION 2: Contact Details -->
        <div class="pt-4 border-t border-outline-variant/40">
          <h3 class="font-headline-md text-headline-md text-on-surface mb-3 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-[20px]">call</span>2. Contact Details
          </h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-body-md">
            <div>
              <label class="block text-label-md text-on-surface mb-1">Primary Phone *</label>
              <input type="text" name="phone" required value="<?php echo html_escape($staff->phone); ?>" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
            </div>
            <div>
              <label class="block text-label-md text-on-surface mb-1">Alternate Phone</label>
              <input type="text" name="alternate_phone" value="<?php echo html_escape($staff->alternate_phone); ?>" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
            </div>
            <div>
              <label class="block text-label-md text-on-surface mb-1">Email Address *</label>
              <input type="email" name="email" required value="<?php echo html_escape($staff->email); ?>" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
            </div>
            <div class="sm:col-span-3">
              <label class="block text-label-md text-on-surface mb-1">Residential Address</label>
              <input type="text" name="address" value="<?php echo html_escape($staff->address); ?>" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
            </div>
          </div>
        </div>

        <!-- SECTION 3: Employment Details -->
        <div class="pt-4 border-t border-outline-variant/40">
          <h3 class="font-headline-md text-headline-md text-on-surface mb-3 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-[20px]">badge</span>3. Employment Details
          </h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-body-md">
            <div>
              <label class="block text-label-md text-on-surface mb-1">Employee ID / Code *</label>
              <input type="text" name="employee_code" required value="<?php echo html_escape($staff->employee_code); ?>" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest font-mono text-primary font-medium"/>
            </div>
            <div>
              <label class="block text-label-md text-on-surface mb-1">Staff Type *</label>
              <select name="staff_type" id="staff_type_select" onchange="toggleTeacherFields(this.value)" required class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest font-medium">
                <option value="teacher" <?php echo ($staff->staff_type === 'teacher') ? 'selected' : ''; ?>>Teacher (Teaching Faculty)</option>
                <option value="non_teaching" <?php echo ($staff->staff_type === 'non_teaching') ? 'selected' : ''; ?>>Non-Teaching Staff</option>
              </select>
            </div>
            <div>
              <label class="block text-label-md text-on-surface mb-1">Department *</label>
              <select name="department_id" required class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest">
                <?php foreach ($departments as $dept): ?>
                  <option value="<?php echo $dept->department_id; ?>" <?php echo ($staff->department_id == $dept->department_id) ? 'selected' : ''; ?>><?php echo html_escape($dept->department_name); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label class="block text-label-md text-on-surface mb-1">Designation *</label>
              <select name="designation_id" required class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest">
                <?php foreach ($designations as $desig): ?>
                  <option value="<?php echo $desig->designation_id; ?>" <?php echo ($staff->designation_id == $desig->designation_id) ? 'selected' : ''; ?>><?php echo html_escape($desig->designation_name); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label class="block text-label-md text-on-surface mb-1">Joining Date *</label>
              <input type="date" name="joining_date" required value="<?php echo html_escape($staff->joining_date); ?>" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
            </div>
            <div>
              <label class="block text-label-md text-on-surface mb-1">Monthly Salary (₹)</label>
              <input type="number" step="100" name="salary" value="<?php echo html_escape($staff->salary); ?>" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
            </div>
            <div>
              <label class="block text-label-md text-on-surface mb-1">Employment Status</label>
              <select name="employment_status" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest">
                <?php foreach (array('Active', 'On Leave', 'Probation', 'Resigned', 'Suspended') as $es): ?>
                  <option value="<?php echo $es; ?>" <?php echo ($staff->employment_status === $es) ? 'selected' : ''; ?>><?php echo $es; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>

        <!-- SECTION 4: Professional Details -->
        <div class="pt-4 border-t border-outline-variant/40">
          <h3 class="font-headline-md text-headline-md text-on-surface mb-3 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-[20px]">school</span>4. Professional Details
          </h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-body-md">
            <div>
              <label class="block text-label-md text-on-surface mb-1">Qualification</label>
              <input type="text" name="qualification" value="<?php echo html_escape($staff->qualification); ?>" placeholder="e.g. M.Sc, B.Ed" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
            </div>
            <div>
              <label class="block text-label-md text-on-surface mb-1">Experience</label>
              <input type="text" name="experience" value="<?php echo html_escape($staff->experience); ?>" placeholder="e.g. 5 Years" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
            </div>
            <div id="teacher_specialization_box" style="<?php echo ($staff->staff_type === 'non_teaching') ? 'display:none;' : ''; ?>">
              <label class="block text-label-md text-on-surface mb-1">Subject Specialization</label>
              <input type="text" name="specialization" value="<?php echo html_escape($staff->specialization); ?>" placeholder="e.g. Mathematics, Science" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
            </div>
          </div>
        </div>

        <!-- SECTION 5: Staff Documents (Dynamic) -->
        <div class="pt-4 border-t border-outline-variant/40">
          <div class="flex items-center justify-between gap-2 mb-3">
            <h3 class="font-headline-md text-headline-md text-on-surface flex items-center gap-2">
              <span class="material-symbols-outlined text-primary text-[20px]">folder_shared</span>5. Staff Documents
            </h3>
            <span class="text-[12px] text-on-surface-variant">Manage or replace uploaded documents</span>
          </div>

          <?php if (empty($document_types)): ?>
            <div class="p-4 rounded-xl bg-surface-container-low border border-outline-variant/60 text-body-md text-on-surface-variant">
              No active staff document definitions found in Settings.
            </div>
          <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-body-md">
              <?php foreach ($document_types as $dt): ?>
                <?php 
                  $existingDoc = $existing_docs_map[$dt->id] ?? ($existing_docs_map[strtolower(trim($dt->document_name))] ?? null);
                ?>
                <div class="p-4 rounded-xl bg-surface-container-low border border-outline-variant/60 hover:border-outline transition-colors">
                  <div class="flex items-center justify-between mb-2">
                    <span class="font-semibold text-on-surface text-label-md flex items-center gap-1">
                      <?php echo html_escape($dt->document_name); ?> <span class="text-error font-bold">*</span>
                    </span>
                    <?php if ($existingDoc): ?>
                      <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-secondary bg-secondary-container/50 px-2 py-0.5 rounded-full">
                        <span class="material-symbols-outlined text-[13px]">check_circle</span> Uploaded
                      </span>
                    <?php else: ?>
                      <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-on-surface-variant bg-surface-container-high px-2 py-0.5 rounded-full">
                        Not Uploaded
                      </span>
                    <?php endif; ?>
                  </div>

                  <?php if ($existingDoc): ?>
                    <div class="text-[12px] text-on-surface-variant mb-3 bg-surface-container-lowest p-2.5 rounded-lg border border-outline-variant/40 flex items-center justify-between gap-2">
                      <div class="truncate min-w-0">
                        <span class="text-[11px] text-on-surface-variant block">Current file:</span>
                        <span class="font-medium text-on-surface text-[12px] truncate block"><?php echo html_escape($existingDoc->file_name ?: basename($existingDoc->file_path)); ?></span>
                      </div>
                      <a href="<?php echo site_url('staff/view_document/' . $existingDoc->document_id); ?>" target="_blank" class="px-2 py-1 rounded bg-surface-container-high text-on-surface text-[11px] font-medium hover:bg-surface-container-highest transition-colors shrink-0 inline-flex items-center gap-1">
                        <span class="material-symbols-outlined text-[13px]">visibility</span>View
                      </a>
                    </div>

                    <div>
                      <label class="block text-[11px] font-semibold text-on-surface-variant mb-1">Replace Document:</label>
                      <input type="file" 
                        name="staff_doc_file[<?php echo $dt->id; ?>]" 
                        class="w-full px-3 py-1.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-[12px] text-on-surface file:mr-2.5 file:py-1 file:px-2 file:rounded file:border-0 file:text-[11px] file:font-semibold file:bg-surface-container-high file:text-on-surface hover:file:bg-surface-container-highest cursor-pointer"/>
                    </div>
                  <?php else: ?>
                    <div class="text-[12px] text-on-surface-variant mb-2">
                      <?php echo html_escape($dt->description ?: 'Upload document copy'); ?>
                    </div>
                    <input type="file" 
                      name="staff_doc_file[<?php echo $dt->id; ?>]" 
                      class="w-full px-3 py-1.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md text-on-surface file:mr-3 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-secondary/15 file:text-secondary hover:file:bg-secondary/25 cursor-pointer"/>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>

        <!-- Form Actions -->
        <div class="pt-6 border-t border-outline-variant/40 flex items-center justify-end gap-3">
          <a href="<?php echo site_url('staff/profile/' . $staff_id); ?>" class="px-5 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant hover:bg-surface-container-high transition-colors">Cancel</a>
          <button type="submit" class="px-6 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer inline-flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[18px]">save</span>Save Changes
          </button>
        </div>

      <?php echo form_close(); ?>
    </div>

    <script>
      function toggleTeacherFields(type) {
        var specBox = document.getElementById('teacher_specialization_box');
        if (type === 'non_teaching') {
          specBox.style.display = 'none';
        } else {
          specBox.style.display = 'block';
        }
      }
    </script>
