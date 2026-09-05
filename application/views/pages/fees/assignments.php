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
                <?php $cd_label = $fs->class_name . (!empty($fs->division_name) ? ' - ' . $fs->division_name : ''); ?>
                <option value="<?php echo $fs->fee_structure_id; ?>" data-amount="<?php echo $fs->amount; ?>" data-due="<?php echo $fs->due_date; ?>">
                  <?php echo html_escape($fs->category_name . ' - ' . $cd_label . ' (₹' . number_format($fs->amount, 2) . ')'); ?>
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

        <?php echo form_open('fees/assignments', array('class' => 'space-y-4', 'id' => 'bulk-assignment-form', 'onsubmit' => 'return validateBulkAssignmentForm(this);')); ?>
          <input type="hidden" name="assignment_type" value="bulk"/>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Academic Year *</label>
              <select name="academic_year_id" id="bulk-academic-year-id" onchange="onBulkAcademicYearChange()" required class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <?php foreach ($academic_years as $ay): ?>
                  <option value="<?php echo $ay->academic_year_id; ?>"><?php echo html_escape($ay->year_name); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Target Class *</label>
              <select name="class_id" id="bulk-class-id" onchange="onBulkClassChange(this.value)" required class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <option value="">-- Choose Class --</option>
                <?php foreach ($classes as $c): ?>
                  <option value="<?php echo $c->class_id; ?>"><?php echo html_escape($c->class_name); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div>
            <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Target Division *</label>
            <select name="division_id" id="bulk-division-id" onchange="onBulkDivisionChange(this.value)" required class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary" disabled>
              <option value="">-- Choose Class First --</option>
            </select>
          </div>

          <!-- Student Selection Area -->
          <div class="space-y-2">
            <div class="flex items-center justify-between">
              <label class="block font-label-md text-label-md text-on-surface font-medium">
                Students <span class="text-error">*</span>
              </label>
              <div id="bulk-student-count-badge" class="text-xs text-on-surface-variant font-medium"></div>
            </div>

            <div class="rounded-xl border border-outline-variant/60 bg-surface-container-low/40 overflow-hidden">
              <!-- Toolbar -->
              <div class="px-3.5 py-2 bg-surface-container-high/50 border-b border-outline-variant/50 flex items-center justify-between gap-3 text-body-md">
                <div class="flex items-center gap-2">
                  <button type="button" id="btn-select-all" onclick="selectAllStudents()" class="px-2.5 py-1 text-xs font-semibold rounded bg-surface-container-lowest border border-outline-variant text-on-surface hover:bg-surface-container transition-colors cursor-pointer inline-flex items-center gap-1 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    <span class="material-symbols-outlined text-[15px] text-secondary">check_box</span>Select All
                  </button>
                  <button type="button" id="btn-unselect-all" onclick="unselectAllStudents()" class="px-2.5 py-1 text-xs font-semibold rounded bg-surface-container-lowest border border-outline-variant text-on-surface hover:bg-surface-container transition-colors cursor-pointer inline-flex items-center gap-1 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    <span class="material-symbols-outlined text-[15px] text-on-surface-variant">check_box_outline_blank</span>Unselect All
                  </button>
                </div>
                <div id="bulk-selected-summary" class="text-xs font-medium text-on-surface-variant">
                  0 selected
                </div>
              </div>

              <!-- Student list rows -->
              <div id="bulk-students-container" class="max-h-60 overflow-y-auto divide-y divide-outline-variant/30 bg-surface-container-lowest">
                <div id="bulk-students-placeholder" class="p-6 text-center text-on-surface-variant text-body-md">
                  <span class="material-symbols-outlined text-[32px] text-outline mb-1 block">groups</span>
                  <span>Please select Class and Division to load students.</span>
                </div>
              </div>
            </div>
          </div>

          <div>
            <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Fee Structure to Assign *</label>
            <select name="fee_structure_id" id="bulk-fee-structure-id" onchange="onBulkFeeStructureChange()" required class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
              <option value="">-- Choose Fee Structure --</option>
              <?php foreach ($structures as $fs): ?>
                <?php $cd_label = $fs->class_name . (!empty($fs->division_name) ? ' - ' . $fs->division_name : ''); ?>
                <option value="<?php echo $fs->fee_structure_id; ?>">
                  <?php echo html_escape($fs->category_name . ' - ' . $cd_label . ' (₹' . number_format($fs->amount, 2) . ' - Due: ' . date('d M Y', strtotime($fs->due_date)) . ')'); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="p-4 rounded-xl bg-surface-container-low border border-outline-variant/40 text-body-md text-on-surface-variant flex items-center gap-3">
            <span class="material-symbols-outlined text-primary text-[22px]">info</span>
            <span>Fee invoices will be created only for the selected eligible students who have not yet received this structure.</span>
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
      var ajaxDivisionsUrl = "<?php echo site_url('fees/ajax_get_divisions'); ?>";
      var ajaxStudentsUrl = "<?php echo site_url('fees/ajax_get_students'); ?>";

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

      function onBulkAcademicYearChange() {
        var divSelect = document.getElementById('bulk-division-id');
        if (divSelect && divSelect.value) {
          loadBulkStudents(true);
        }
      }

      function onBulkClassChange(classId) {
        var divSelect = document.getElementById('bulk-division-id');
        if (!divSelect) return;

        divSelect.innerHTML = '';
        resetStudentList();

        if (!classId) {
          divSelect.innerHTML = '<option value="">-- Choose Class First --</option>';
          divSelect.disabled = true;
          return;
        }

        divSelect.disabled = true;
        divSelect.innerHTML = '<option value="">Loading divisions...</option>';

        fetch(ajaxDivisionsUrl + '/' + encodeURIComponent(classId))
          .then(function(res) { return res.json(); })
          .then(function(divisions) {
            divSelect.innerHTML = '';
            if (divisions && divisions.length > 0) {
              var defaultOpt = document.createElement('option');
              defaultOpt.value = '';
              defaultOpt.textContent = '-- Choose Division --';
              divSelect.appendChild(defaultOpt);

              divisions.forEach(function(d) {
                var opt = document.createElement('option');
                opt.value = d.division_id;
                opt.textContent = (d.class_name ? d.class_name + ' ' : '') + d.division_name;
                divSelect.appendChild(opt);
              });
              divSelect.disabled = false;
            } else {
              divSelect.innerHTML = '<option value="" disabled selected>No divisions found for this class.</option>';
              divSelect.disabled = true;
            }
          })
          .catch(function(err) {
            console.error('Error fetching divisions:', err);
            divSelect.innerHTML = '<option value="" disabled selected>Unable to load divisions.</option>';
            divSelect.disabled = false;
          });
      }

      function onBulkDivisionChange(divId) {
        if (!divId) {
          resetStudentList();
          return;
        }
        loadBulkStudents(false);
      }

      function onBulkFeeStructureChange() {
        var divSelect = document.getElementById('bulk-division-id');
        if (divSelect && divSelect.value) {
          loadBulkStudents(true);
        }
      }

      function resetStudentList() {
        var container = document.getElementById('bulk-students-container');
        var countBadge = document.getElementById('bulk-student-count-badge');
        var summary = document.getElementById('bulk-selected-summary');
        var btnSelectAll = document.getElementById('btn-select-all');
        var btnUnselectAll = document.getElementById('btn-unselect-all');

        if (container) {
          container.innerHTML = '<div id="bulk-students-placeholder" class="p-6 text-center text-on-surface-variant text-body-md">' +
            '<span class="material-symbols-outlined text-[32px] text-outline mb-1 block">groups</span>' +
            '<span>Please select Class and Division to load students.</span></div>';
        }
        if (countBadge) countBadge.textContent = '';
        if (summary) summary.textContent = '0 selected';
        if (btnSelectAll) btnSelectAll.disabled = true;
        if (btnUnselectAll) btnUnselectAll.disabled = true;
      }

      function loadBulkStudents(preserveChecked) {
        var yearSelect = document.getElementById('bulk-academic-year-id');
        var classSelect = document.getElementById('bulk-class-id');
        var divSelect = document.getElementById('bulk-division-id');
        var feeSelect = document.getElementById('bulk-fee-structure-id');
        var container = document.getElementById('bulk-students-container');
        var countBadge = document.getElementById('bulk-student-count-badge');
        var summary = document.getElementById('bulk-selected-summary');
        var btnSelectAll = document.getElementById('btn-select-all');
        var btnUnselectAll = document.getElementById('btn-unselect-all');

        if (!container || !classSelect || !divSelect) return;

        var yearId = yearSelect ? yearSelect.value : '';
        var classId = classSelect.value;
        var divId = divSelect.value;
        var feeId = feeSelect ? feeSelect.value : '';

        if (!classId || !divId) {
          resetStudentList();
          return;
        }

        // Remember previously checked students if preserving
        var previouslyCheckedMap = {};
        if (preserveChecked) {
          var checkedBoxes = container.querySelectorAll('.bulk-student-checkbox:checked');
          checkedBoxes.forEach(function(cb) {
            previouslyCheckedMap[cb.value] = true;
          });
        }

        container.innerHTML = '<div class="p-6 text-center text-on-surface-variant text-body-md flex items-center justify-center gap-2.5">' +
          '<span class="inline-block w-4 h-4 border-2 border-primary border-t-transparent rounded-full animate-spin"></span>' +
          '<span>Loading students...</span></div>';

        if (btnSelectAll) btnSelectAll.disabled = true;
        if (btnUnselectAll) btnUnselectAll.disabled = true;

        var url = ajaxStudentsUrl + '?class_id=' + encodeURIComponent(classId) +
          '&division_id=' + encodeURIComponent(divId) +
          (yearId ? '&academic_year_id=' + encodeURIComponent(yearId) : '') +
          (feeId ? '&fee_structure_id=' + encodeURIComponent(feeId) : '');

        fetch(url)
          .then(function(res) { return res.json(); })
          .then(function(data) {
            container.innerHTML = '';
            var students = (data && data.students) ? data.students : [];

            if (students.length === 0) {
              container.innerHTML = '<div class="p-6 text-center text-on-surface-variant text-body-md">' +
                '<span class="material-symbols-outlined text-[32px] text-outline mb-1 block">person_off</span>' +
                '<span>No students found in this class and division.</span></div>';
              if (countBadge) countBadge.textContent = '0 students';
              if (summary) summary.textContent = '0 selected';
              if (btnSelectAll) btnSelectAll.disabled = true;
              if (btnUnselectAll) btnUnselectAll.disabled = true;
              return;
            }

            var totalCount = students.length;
            var assignedCount = 0;
            var eligibleCount = 0;

            students.forEach(function(s) {
              if (s.already_assigned) {
                assignedCount++;
              } else {
                eligibleCount++;
              }

              var row = document.createElement('label');
              row.className = 'px-3.5 py-2.5 flex items-center justify-between gap-3 hover:bg-surface-container-low/60 transition-colors cursor-pointer ' +
                (s.already_assigned ? 'opacity-65 bg-surface-container-low/30 cursor-not-allowed' : '');

              var left = document.createElement('div');
              left.className = 'flex items-center gap-3 min-w-0';

              var cb = document.createElement('input');
              cb.type = 'checkbox';
              cb.name = 'student_ids[]';
              cb.value = s.student_id;

              if (s.already_assigned) {
                cb.disabled = true;
                cb.checked = false;
                cb.className = 'h-4 w-4 rounded border-outline-variant bg-surface-container-high text-outline cursor-not-allowed';
              } else {
                cb.className = 'h-4 w-4 rounded border-outline-variant text-secondary focus:ring-secondary/50 bg-surface-container-lowest cursor-pointer bulk-student-checkbox';
                // If preserveChecked was requested, check if it was previously checked; otherwise default to selected
                if (preserveChecked) {
                  cb.checked = !!previouslyCheckedMap[s.student_id];
                } else {
                  cb.checked = true; // by default select eligible students
                }
                cb.addEventListener('change', updateStudentSelectionState);
              }

              var info = document.createElement('div');
              info.className = 'min-w-0 leading-tight';

              var nameSpan = document.createElement('span');
              nameSpan.className = 'font-semibold text-body-md text-on-surface block truncate';
              nameSpan.textContent = s.student_name || ((s.first_name || '') + ' ' + (s.last_name || '')).trim();

              var metaSpan = document.createElement('span');
              metaSpan.className = 'text-xs text-on-surface-variant flex items-center gap-2 mt-0.5';
              var metaHtml = '';
              if (s.admission_number) {
                metaHtml += '<span>Adm: ' + escapeHtml(s.admission_number) + '</span>';
              }
              if (s.roll_number) {
                metaHtml += (metaHtml ? '<span>•</span>' : '') + '<span>Roll No: ' + escapeHtml(s.roll_number) + '</span>';
              }
              metaSpan.innerHTML = metaHtml;

              info.appendChild(nameSpan);
              info.appendChild(metaSpan);

              left.appendChild(cb);
              left.appendChild(info);
              row.appendChild(left);

              if (s.already_assigned) {
                var badge = document.createElement('span');
                badge.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-semibold bg-amber-100 text-amber-900 border border-amber-300 dark:bg-amber-950/40 dark:text-amber-200 dark:border-amber-800 shrink-0';
                badge.innerHTML = '<span class="material-symbols-outlined text-[13px]">check_circle</span> Already Assigned';
                row.appendChild(badge);
              }

              container.appendChild(row);
            });

            // Update badge text
            if (countBadge) {
              if (assignedCount > 0) {
                countBadge.textContent = totalCount + ' students (' + eligibleCount + ' eligible, ' + assignedCount + ' already assigned)';
              } else {
                countBadge.textContent = totalCount + ' student' + (totalCount === 1 ? '' : 's') + ' found';
              }
            }

            if (btnSelectAll) btnSelectAll.disabled = (eligibleCount === 0);
            if (btnUnselectAll) btnUnselectAll.disabled = (eligibleCount === 0);

            updateStudentSelectionState();
          })
          .catch(function(err) {
            console.error('Error fetching students:', err);
            container.innerHTML = '<div class="p-6 text-center text-error text-body-md">' +
              '<span class="material-symbols-outlined text-[32px] text-error mb-1 block">error</span>' +
              '<span>Unable to load students. Please try again.</span></div>';
            if (btnSelectAll) btnSelectAll.disabled = true;
            if (btnUnselectAll) btnUnselectAll.disabled = true;
          });
      }

      function selectAllStudents() {
        var checkboxes = document.querySelectorAll('.bulk-student-checkbox:not(:disabled)');
        checkboxes.forEach(function(cb) {
          cb.checked = true;
        });
        updateStudentSelectionState();
      }

      function unselectAllStudents() {
        var checkboxes = document.querySelectorAll('.bulk-student-checkbox:not(:disabled)');
        checkboxes.forEach(function(cb) {
          cb.checked = false;
        });
        updateStudentSelectionState();
      }

      function updateStudentSelectionState() {
        var eligibleBoxes = document.querySelectorAll('.bulk-student-checkbox:not(:disabled)');
        var checkedBoxes = document.querySelectorAll('.bulk-student-checkbox:checked');
        var summary = document.getElementById('bulk-selected-summary');

        if (summary) {
          if (eligibleBoxes.length > 0) {
            summary.textContent = checkedBoxes.length + ' of ' + eligibleBoxes.length + ' selected';
          } else {
            summary.textContent = '0 selected';
          }
        }
      }

      function escapeHtml(str) {
        if (!str) return '';
        return String(str)
          .replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;')
          .replace(/'/g, '&#039;');
      }

      function validateBulkAssignmentForm(form) {
        var classSelect = document.getElementById('bulk-class-id');
        if (!classSelect || !classSelect.value || classSelect.value.trim() === '') {
          alert("Please select a target class.");
          if (classSelect) classSelect.focus();
          return false;
        }

        var divSelect = document.getElementById('bulk-division-id');
        if (!divSelect || !divSelect.value || divSelect.value.trim() === '') {
          alert("Please select a target division.");
          if (divSelect && !divSelect.disabled) {
            divSelect.focus();
          }
          return false;
        }

        var feeSelect = document.getElementById('bulk-fee-structure-id');
        if (!feeSelect || !feeSelect.value || feeSelect.value.trim() === '') {
          alert("Please select a fee structure to assign.");
          if (feeSelect) feeSelect.focus();
          return false;
        }

        var checkedStudents = form.querySelectorAll('input[name="student_ids[]"]:checked');
        if (!checkedStudents || checkedStudents.length === 0) {
          alert("Please select at least one student.");
          return false;
        }

        return confirm("Are you sure you want to assign this fee structure to the " + checkedStudents.length + " selected student(s)?");
      }

      document.addEventListener('DOMContentLoaded', function() {
        var classSelect = document.getElementById('bulk-class-id');
        if (classSelect && classSelect.value) {
          onBulkClassChange(classSelect.value);
        }
      });
    </script>

