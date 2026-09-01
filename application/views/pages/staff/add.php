<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

    <div class="flex items-center justify-between gap-4 mb-6">
      <div>
        <h2 class="font-headline-md text-headline-md text-on-surface">Staff Registration</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Register a new faculty member or administrative staff employee.</p>
      </div>
      <a href="<?php echo site_url('staff'); ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md hover:bg-surface-container-high transition-colors">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>Back to All Staff
      </a>
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

    <div id="doc-validation-alert" class="hidden p-4 mb-5 rounded-xl bg-error-container/40 border border-error text-error text-body-md flex items-center gap-3">
      <span class="material-symbols-outlined text-[22px]">error</span>
      <div id="doc-validation-msg"></div>
    </div>

    <div class="elevation-1 rounded-2xl bg-surface-container-lowest border border-outline-variant/50 p-6 max-w-4xl">
      <?php echo form_open_multipart('staff/register', array('class' => 'space-y-6', 'id' => 'add_staff_form')); ?>
        
        <!-- SECTION 1: Personal Details -->
        <div>
          <h3 class="font-headline-md text-headline-md text-on-surface mb-3 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-[20px]">person</span>1. Personal Details
          </h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-body-md">
            <div>
              <label class="block text-label-md text-on-surface mb-1">Full Name *</label>
              <input type="text" name="full_name" required value="<?php echo set_value('full_name'); ?>" placeholder="e.g. Ramesh Kumar" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
            </div>
            <div>
              <label class="block text-label-md text-on-surface mb-1">Gender *</label>
              <select name="gender" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest">
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
              </select>
            </div>
            <div>
              <label class="block text-label-md text-on-surface mb-1">Date of Birth</label>
              <input type="date" name="date_of_birth" value="<?php echo set_value('date_of_birth', '1988-06-15'); ?>" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
            </div>
            <div>
              <label class="block text-label-md text-on-surface mb-1">Blood Group</label>
              <select name="blood_group" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest">
                <option value="">Select Blood Group</option>
                <option value="A+">A+</option><option value="A-">A-</option><option value="B+">B+</option><option value="B-">B-</option><option value="O+">O+</option><option value="O-">O-</option><option value="AB+">AB+</option><option value="AB-">AB-</option>
              </select>
            </div>
          </div>
        </div>

        <!-- SECTION 2: Contact Information -->
        <div class="pt-4 border-t border-outline-variant/40">
          <h3 class="font-headline-md text-headline-md text-on-surface mb-3 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-[20px]">call</span>2. Contact Details
          </h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-body-md">
            <div>
              <label class="block text-label-md text-on-surface mb-1">Primary Phone *</label>
              <input type="text" name="phone" required value="<?php echo set_value('phone'); ?>" placeholder="+91 98470 11223" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
            </div>
            <div>
              <label class="block text-label-md text-on-surface mb-1">Alternate Phone</label>
              <input type="text" name="alternate_phone" value="<?php echo set_value('alternate_phone'); ?>" placeholder="+91 94470 99887" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
            </div>
            <div>
              <label class="block text-label-md text-on-surface mb-1">Email Address *</label>
              <input type="email" name="email" required value="<?php echo set_value('email'); ?>" placeholder="staff.name@gmail.com" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
            </div>
            <div class="sm:col-span-3">
              <label class="block text-label-md text-on-surface mb-1">Full Residential Address</label>
              <input type="text" name="address" value="<?php echo set_value('address'); ?>" placeholder="House Name, Street, City, District, PIN" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
            </div>
          </div>
        </div>

        <!-- SECTION 3: Employment Information -->
        <div class="pt-4 border-t border-outline-variant/40">
          <h3 class="font-headline-md text-headline-md text-on-surface mb-3 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-[20px]">badge</span>3. Employment Details
          </h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-body-md">
            <div>
              <label class="block text-label-md text-on-surface mb-1">Employee ID / Code *</label>
              <input type="text" name="employee_code" required value="<?php echo set_value('employee_code', 'EMP' . rand(1015, 1999)); ?>" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest font-mono text-primary font-medium"/>
            </div>
            <div>
              <label class="block text-label-md text-on-surface mb-1">Staff Type *</label>
              <select name="staff_type" id="staff_type_select" onchange="toggleTeacherFields(this.value)" required class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest font-medium">
                <option value="teacher">Teacher (Teaching Faculty)</option>
                <option value="non_teaching">Non-Teaching Staff</option>
              </select>
            </div>
            <div>
              <label class="block text-label-md text-on-surface mb-1">Department *</label>
              <select name="department_id" required class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest">
                <?php foreach ($departments as $dept): ?>
                  <option value="<?php echo $dept->department_id; ?>"><?php echo html_escape($dept->department_name); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label class="block text-label-md text-on-surface mb-1">Designation *</label>
              <select name="designation_id" required class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest">
                <?php foreach ($designations as $desig): ?>
                  <option value="<?php echo $desig->designation_id; ?>"><?php echo html_escape($desig->designation_name); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label class="block text-label-md text-on-surface mb-1">Joining Date *</label>
              <input type="date" name="joining_date" required value="<?php echo date('Y-m-d'); ?>" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
            </div>
            <div>
              <label class="block text-label-md text-on-surface mb-1">Monthly Salary (₹)</label>
              <input type="number" step="100" name="salary" value="<?php echo set_value('salary', '40000'); ?>" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
            </div>
          </div>
        </div>

        <!-- SECTION 4: Professional & Teacher Specific Fields -->
        <div class="pt-4 border-t border-outline-variant/40">
          <h3 class="font-headline-md text-headline-md text-on-surface mb-3 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-[20px]">school</span>4. Professional Details
          </h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-body-md">
            <div>
              <label class="block text-label-md text-on-surface mb-1">Qualification</label>
              <input type="text" name="qualification" value="<?php echo set_value('qualification'); ?>" placeholder="e.g. M.Sc, B.Ed" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
            </div>
            <div>
              <label class="block text-label-md text-on-surface mb-1">Experience</label>
              <input type="text" name="experience" value="<?php echo set_value('experience'); ?>" placeholder="e.g. 5 Years" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
            </div>
            <div id="teacher_specialization_box">
              <label class="block text-label-md text-on-surface mb-1">Subject Specialization</label>
              <input type="text" name="specialization" value="<?php echo set_value('specialization'); ?>" placeholder="e.g. Mathematics, Science" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
            </div>
          </div>
        </div>

        <!-- SECTION 5: Staff Documents (Dynamic & Mandatory) -->
        <div class="pt-4 border-t border-outline-variant/40">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
            <h3 class="font-headline-md text-headline-md text-on-surface flex items-center gap-2">
              <span class="material-symbols-outlined text-primary text-[20px]">folder_shared</span>5. Staff Documents
            </h3>
            <span class="text-label-md text-error font-medium flex items-center gap-1">
              <span class="material-symbols-outlined text-[16px]">priority_high</span>All configured documents are mandatory
            </span>
          </div>

          <?php if (empty($document_types)): ?>
            <div class="p-4 rounded-xl bg-surface-container-low border border-outline-variant/60 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
              <div>
                <div class="text-body-md font-medium text-on-surface">No staff documents configured.</div>
                <div class="text-[12px] text-on-surface-variant">Super Admin can add required staff documents from Settings → Staff Document.</div>
              </div>
              <?php if (!empty($is_super_admin)): ?>
                <a href="<?php echo site_url('settings/staff_documents'); ?>" target="_blank" class="px-3.5 py-2 rounded-lg bg-secondary text-on-secondary text-label-md hover:bg-on-secondary-fixed-variant transition-colors shrink-0 inline-flex items-center gap-1">
                  <span class="material-symbols-outlined text-[16px]">add</span>+ Add Document
                </a>
              <?php endif; ?>
            </div>
          <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-body-md">
              <?php foreach ($document_types as $dt): ?>
                <div class="p-4 rounded-xl bg-surface-container-low border border-outline-variant/60 hover:border-outline transition-colors">
                  <div class="flex items-center justify-between mb-1">
                    <label class="font-semibold text-on-surface text-label-md flex items-center gap-1">
                      <?php echo html_escape($dt->document_name); ?> <span class="text-error font-bold">*</span>
                    </label>
                    <span class="text-[10px] uppercase font-bold tracking-wider text-error bg-error-container/60 px-2 py-0.5 rounded-full">Required</span>
                  </div>
                  <?php if (!empty($dt->description)): ?>
                    <p class="text-[12px] text-on-surface-variant mb-2.5"><?php echo html_escape($dt->description); ?></p>
                  <?php else: ?>
                    <p class="text-[12px] text-on-surface-variant mb-2.5">Upload document copy (PDF, JPG, PNG, DOC)</p>
                  <?php endif; ?>
                  <input type="file" 
                    name="staff_doc_file[<?php echo $dt->id; ?>]" 
                    id="doc_input_<?php echo $dt->id; ?>"
                    data-doc-name="<?php echo html_escape($dt->document_name); ?>"
                    required 
                    class="staff-required-doc w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md text-on-surface file:mr-3 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-secondary/15 file:text-secondary hover:file:bg-secondary/25 cursor-pointer transition-colors"/>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>

        <!-- Form Actions -->
        <div class="pt-6 border-t border-outline-variant/40 flex items-center justify-end gap-3">
          <a href="<?php echo site_url('staff'); ?>" class="px-5 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant hover:bg-surface-container-high transition-colors">Cancel</a>
          <button type="submit" class="px-6 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer inline-flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[18px]">check</span>Register Staff Member
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

      // Client-side mandatory document verification
      document.getElementById('add_staff_form').addEventListener('submit', function(e) {
        var docInputs = document.querySelectorAll('.staff-required-doc');
        var missingDocs = [];

        docInputs.forEach(function(input) {
          if (!input.files || input.files.length === 0) {
            missingDocs.push(input.getAttribute('data-doc-name') || 'Document');
            input.classList.add('border-error');
          } else {
            input.classList.remove('border-error');
          }
        });

        if (missingDocs.length > 0) {
          e.preventDefault();
          var alertBox = document.getElementById('doc-validation-alert');
          var alertMsg = document.getElementById('doc-validation-msg');
          
          var msgHtml = '<strong>Please upload all required staff documents before registering:</strong><ul class="list-disc list-inside mt-1">';
          missingDocs.forEach(function(docName) {
            msgHtml += '<li>' + docName + ' is required.</li>';
          });
          msgHtml += '</ul>';

          alertMsg.innerHTML = msgHtml;
          alertBox.classList.remove('hidden');
          alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      });
    </script>
