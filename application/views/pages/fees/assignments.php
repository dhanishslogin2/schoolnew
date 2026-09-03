<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

    <!-- Flash Messages -->
    <?php if ($this->session->flashdata('success')): ?>
      <div class="mb-4 p-3.5 rounded-xl bg-secondary-container text-on-secondary-container text-body-md font-medium flex items-center gap-2 border border-secondary/20">
        <span class="material-symbols-outlined text-[20px] text-secondary">check_circle</span>
        <?php echo html_escape($this->session->flashdata('success')); ?>
      </div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('error')): ?>
      <div class="mb-4 p-3.5 rounded-xl bg-error-container text-on-error-container text-body-md font-medium flex items-center gap-2 border border-error/20">
        <span class="material-symbols-outlined text-[20px] text-error">error</span>
        <?php echo html_escape($this->session->flashdata('error')); ?>
      </div>
    <?php endif; ?>

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h2 class="font-headline-md text-headline-md text-on-surface">Student Fee Assignment</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Assign fees to individual students or bulk allocate fee structures across entire classes.</p>
      </div>
      <div class="flex items-center gap-2 flex-wrap shrink-0">
        <a href="<?php echo site_url('fees/student_fees'); ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md hover:bg-surface-container-high transition-colors">
          <span class="material-symbols-outlined text-[18px]">receipt_long</span>Student Fee Directory
        </a>
      </div>
    </div>

    <!-- Assignment Mode Tabs -->
    <div class="flex gap-2 border-b border-outline-variant/60 mb-6">
      <button onclick="switchMode('individual')" id="tab-btn-individual" class="px-5 py-2.5 text-body-md font-semibold border-b-2 border-secondary text-primary cursor-pointer transition-colors">
        Individual Assignment
      </button>
      <button onclick="switchMode('bulk')" id="tab-btn-bulk" class="px-5 py-2.5 text-body-md font-medium border-b-2 border-transparent text-on-surface-variant hover:text-on-surface cursor-pointer transition-colors">
        Bulk Class Assignment
      </button>
    </div>

    <!-- 1. INDIVIDUAL ASSIGNMENT FORM -->
    <div id="mode-individual" class="max-w-2xl">
      <div class="p-6 rounded-2xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 space-y-5">
        <div class="flex items-center gap-2.5 pb-3 border-b border-outline-variant/50">
          <span class="material-symbols-outlined text-primary text-[24px]">person_add</span>
          <h3 class="font-headline-md text-title-lg font-bold text-on-surface">Individual Fee Assignment</h3>
        </div>

        <?php echo form_open('fees/assignments', array('class' => 'space-y-4')); ?>
          <input type="hidden" name="assignment_type" value="individual"/>

          <div>
            <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Select Student *</label>
            <select name="student_id" required class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
              <option value="">-- Choose Student --</option>
              <?php foreach ($students as $stu): ?>
                <option value="<?php echo $stu->student_id; ?>">
                  <?php echo html_escape($stu->first_name . ' ' . $stu->last_name . ' (' . $stu->admission_number . ') - ' . $stu->class_name . ' ' . $stu->section_name); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Select Fee Structure *</label>
            <select name="fee_structure_id" id="ind-struct-select" onchange="updateStructureAmount(this)" required class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
              <option value="">-- Choose Fee Structure --</option>
              <?php foreach ($structures as $fs): ?>
                <option value="<?php echo $fs->fee_structure_id; ?>" data-amount="<?php echo $fs->amount; ?>" data-due="<?php echo $fs->due_date; ?>">
                  <?php echo html_escape($fs->category_name . ' - ' . $fs->class_name . ' (₹' . number_format($fs->amount, 2) . ')'); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Fee Amount (₹) *</label>
              <input type="number" step="0.5" name="amount" id="ind-amount" required placeholder="0.00" class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-mono font-bold focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
            </div>
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Payment Due Date *</label>
              <input type="date" name="due_date" id="ind-duedate" required class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Discount Amount (₹)</label>
              <input type="number" step="0.5" name="discount_amount" value="0.00" class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-mono focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
            </div>
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Concession (₹)</label>
              <input type="number" step="0.5" name="concession_amount" value="0.00" class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-mono focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
            </div>
          </div>

          <div>
            <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Remarks / Special Notes</label>
            <input type="text" name="remarks" placeholder="Optional notes for this invoice..." class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
          </div>

          <div class="pt-4 border-t border-outline-variant/50 flex items-center justify-end">
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer">
              <span class="material-symbols-outlined text-[18px]">check</span>Assign Fee to Student
            </button>
          </div>
        <?php echo form_close(); ?>
      </div>
    </div>

    <!-- 2. BULK CLASS ASSIGNMENT FORM -->
    <div id="mode-bulk" class="max-w-2xl hidden">
      <div class="p-6 rounded-2xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 space-y-5">
        <div class="flex items-center gap-2.5 pb-3 border-b border-outline-variant/50">
          <span class="material-symbols-outlined text-secondary text-[24px]">group_add</span>
          <h3 class="font-headline-md text-title-lg font-bold text-on-surface">Bulk Class Fee Assignment</h3>
        </div>

        <?php echo form_open('fees/assignments', array('class' => 'space-y-4', 'onsubmit' => 'return confirm("Are you sure you want to assign this fee structure to all eligible students in the selected class/section?");')); ?>
          <input type="hidden" name="assignment_type" value="bulk"/>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Academic Year *</label>
              <select name="academic_year_id" required class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <?php foreach ($academic_years as $ay): ?>
                  <option value="<?php echo $ay->academic_year_id; ?>"><?php echo html_escape($ay->year_name); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Target Class *</label>
              <select name="class_id" required class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <option value="">-- Choose Class --</option>
                <?php foreach ($classes as $c): ?>
                  <option value="<?php echo $c->class_id; ?>"><?php echo html_escape($c->class_name); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div>
            <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Target Division (Optional)</label>
            <select name="division_id" class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
              <option value="">All Divisions in Class</option>
              <?php foreach ($sections as $sec): ?>
                <option value="<?php echo $sec->section_id; ?>"><?php echo html_escape($sec->class_name . ' ' . $sec->section_name); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Fee Structure to Assign *</label>
            <select name="fee_structure_id" required class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
              <option value="">-- Choose Fee Structure --</option>
              <?php foreach ($structures as $fs): ?>
                <option value="<?php echo $fs->fee_structure_id; ?>">
                  <?php echo html_escape($fs->category_name . ' - ' . $fs->class_name . ' (₹' . number_format($fs->amount, 2) . ' - Due: ' . date('d M Y', strtotime($fs->due_date)) . ')'); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="p-4 rounded-xl bg-surface-container-low border border-outline-variant/40 text-body-md text-on-surface-variant flex items-center gap-3">
            <span class="material-symbols-outlined text-primary text-[22px]">info</span>
            <span>Fee invoices will be created for all actively enrolled students in the selected class who have not yet received this structure.</span>
          </div>

          <div class="pt-4 border-t border-outline-variant/50 flex items-center justify-end">
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer">
              <span class="material-symbols-outlined text-[18px]">bolt</span>Execute Bulk Assignment
            </button>
          </div>
        <?php echo form_close(); ?>
      </div>
    </div>

    <script>
      function switchMode(mode) {
        var ind = document.getElementById('mode-individual');
        var blk = document.getElementById('mode-bulk');
        var btnInd = document.getElementById('tab-btn-individual');
        var btnBlk = document.getElementById('tab-btn-bulk');

        if (mode === 'individual') {
          ind.classList.remove('hidden');
          blk.classList.add('hidden');
          btnInd.classList.add('border-secondary', 'text-primary', 'font-semibold');
          btnInd.classList.remove('border-transparent', 'text-on-surface-variant');
          btnBlk.classList.remove('border-secondary', 'text-primary', 'font-semibold');
          btnBlk.classList.add('border-transparent', 'text-on-surface-variant');
        } else {
          ind.classList.add('hidden');
          blk.classList.remove('hidden');
          btnBlk.classList.add('border-secondary', 'text-primary', 'font-semibold');
          btnBlk.classList.remove('border-transparent', 'text-on-surface-variant');
          btnInd.classList.remove('border-secondary', 'text-primary', 'font-semibold');
          btnInd.classList.add('border-transparent', 'text-on-surface-variant');
        }
      }

      function updateStructureAmount(select) {
        var opt = select.options[select.selectedIndex];
        var amount = opt.getAttribute('data-amount');
        var due = opt.getAttribute('data-due');
        if (amount) {
          document.getElementById('ind-amount').value = amount;
        }
        if (due) {
          document.getElementById('ind-duedate').value = due;
        }
      }
    </script>
