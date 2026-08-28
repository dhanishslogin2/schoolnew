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
        <h2 class="font-headline-md text-headline-md text-on-surface">Daily Attendance</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Mark and manage day-wise attendance for class and section.</p>
      </div>
      <div class="flex items-center gap-2 flex-wrap shrink-0">
        <a href="<?php echo site_url('attendance/history?class_id=' . $class_id . '&section_id=' . $section_id); ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md hover:bg-surface-container-high transition-colors">
          <span class="material-symbols-outlined text-[18px]">history</span>Attendance History
        </a>
      </div>
    </div>

    <!-- Filter Bar -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4 mb-5">
      <form method="get" action="<?php echo site_url('attendance/daily'); ?>" id="daily-filter-form" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Academic Year *</label>
          <select name="academic_year_id" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
            <?php foreach ($years as $y): ?>
              <option value="<?php echo $y->academic_year_id; ?>" <?php echo ($year_id == $y->academic_year_id) ? 'selected' : ''; ?>>
                <?php echo html_escape($y->year_name); ?><?php echo ($y->is_active) ? ' (Active)' : ''; ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Date *</label>
          <input type="date" name="date" value="<?php echo html_escape($date); ?>" min="<?php echo html_escape($current_year->start_date ?? ''); ?>" max="<?php echo html_escape($current_year->end_date ?? ''); ?>" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
        </div>

        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Class *</label>
          <select name="class_id" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
            <option value="">-- Select Class --</option>
            <?php foreach ($classes as $cls): ?>
              <option value="<?php echo $cls->class_id; ?>" <?php echo ($class_id == $cls->class_id) ? 'selected' : ''; ?>>
                <?php echo html_escape($cls->class_name); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Section *</label>
          <select name="section_id" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
            <option value="">-- Select Section --</option>
            <?php foreach ($sections as $sec): ?>
              <option value="<?php echo $sec->section_id; ?>" <?php echo ($section_id == $sec->section_id) ? 'selected' : ''; ?>>
                <?php echo html_escape($sec->class_name . ' ' . $sec->section_name); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </form>
    </div>

    <?php if (!$class_id || !$section_id): ?>
      <!-- Prompt to Select Class & Section -->
      <div class="p-8 rounded-xl bg-surface-container-lowest border border-outline-variant/50 text-center elevation-1">
        <span class="material-symbols-outlined text-[48px] text-primary/60 mb-2">filter_alt</span>
        <h3 class="font-title-md text-title-md text-on-surface font-semibold">Select Class and Section</h3>
        <p class="text-body-md text-on-surface-variant max-w-md mx-auto mt-1">Please choose a class and section from the filters above to load the student list and mark daily attendance.</p>
      </div>
    <?php else: ?>

      <!-- Already Marked Notice / Edit Mode -->
      <?php if ($is_already_marked): ?>
        <div class="mb-5 p-4 rounded-xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 elevation-1">
          <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-[24px] text-amber-600 shrink-0">info</span>
            <div>
              <div class="font-semibold text-body-md">Attendance has already been marked for this date.</div>
              <div class="text-[13px] text-amber-800 dark:text-amber-300">You are currently in <strong>Edit Attendance</strong> mode. Modifying statuses will update the existing records without creating duplicates.</div>
            </div>
          </div>
          <span class="px-3 py-1 rounded-full text-[12px] font-semibold bg-amber-200 text-amber-900 shrink-0 self-start sm:self-center">Edit Mode</span>
        </div>
      <?php endif; ?>

      <?php echo form_open('attendance/daily', array('id' => 'attendance-sheet-form')); ?>
        <input type="hidden" name="date" value="<?php echo html_escape($date); ?>"/>
        <input type="hidden" name="academic_year_id" value="<?php echo html_escape($year_id); ?>"/>
        <input type="hidden" name="class_id" value="<?php echo html_escape($class_id); ?>"/>
        <input type="hidden" name="section_id" value="<?php echo html_escape($section_id); ?>"/>

        <!-- Control Bar & Live Counters -->
        <div class="p-4 rounded-xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 mb-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
          <!-- Bulk Action Buttons -->
          <div class="flex items-center gap-2 flex-wrap">
            <span class="text-body-md font-medium text-on-surface mr-1">Quick Mark:</span>
            <button type="button" onclick="markAllStatus('Present')" class="px-3 py-1.5 rounded-lg bg-secondary-container text-on-secondary-container hover:opacity-90 transition-opacity text-label-md font-semibold flex items-center gap-1 cursor-pointer">
              <span class="material-symbols-outlined text-[16px]">done_all</span>Mark All Present
            </button>
            <button type="button" onclick="markAllStatus('Absent')" class="px-3 py-1.5 rounded-lg bg-error-container text-on-error-container hover:opacity-90 transition-opacity text-label-md font-semibold flex items-center gap-1 cursor-pointer">
              <span class="material-symbols-outlined text-[16px]">close</span>Mark All Absent
            </button>
          </div>

          <!-- Live Summary Counters before saving -->
          <div class="flex items-center gap-3 flex-wrap text-[13px] font-medium">
            <span class="px-3 py-1 rounded-lg bg-surface-container-high text-on-surface">Total: <strong id="cnt-total"><?php echo count($students); ?></strong></span>
            <span class="px-3 py-1 rounded-lg bg-secondary-container text-on-secondary-container">Present: <strong id="cnt-present">0</strong></span>
            <span class="px-3 py-1 rounded-lg bg-error-container text-on-error-container">Absent: <strong id="cnt-absent">0</strong></span>
            <span class="px-3 py-1 rounded-lg bg-amber-100 text-amber-900">Late: <strong id="cnt-late">0</strong></span>
            <span class="px-3 py-1 rounded-lg bg-primary-fixed text-on-primary-fixed">Excused: <strong id="cnt-excused">0</strong></span>
          </div>

          <!-- Save Button -->
          <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer shrink-0">
            <span class="material-symbols-outlined text-[18px]">save</span>
            <?php echo ($is_already_marked) ? 'Update Attendance' : 'Save Attendance'; ?>
          </button>
        </div>

        <!-- Student Attendance Table -->
        <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden mb-6">
          <div class="table-scroll overflow-x-auto">
            <table class="w-full data-table zebra border-collapse">
              <thead>
                <tr class="border-b border-outline-variant/60 bg-surface-container-low/50">
                  <th class="text-center px-3 py-3 text-label-md font-semibold text-on-surface-variant uppercase w-16">Roll #</th>
                  <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase">Admission #</th>
                  <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase">Student</th>
                  <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase">Attendance Status</th>
                  <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase">Remarks / Reason</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-outline-variant/40">
                <?php if (empty($students)): ?>
                  <tr><td colspan="5" class="px-4 py-8 text-center text-on-surface-variant text-body-md">No students found in this class and section.</td></tr>
                <?php else: ?>
                  <?php foreach ($students as $st): ?>
                    <?php
                      $fullName = trim($st->first_name . ' ' . $st->last_name);
                      $nameParts = explode(' ', $fullName);
                      $initials = '';
                      foreach ($nameParts as $np) { if (!empty($np)) $initials .= strtoupper($np[0]); }
                      if (strlen($initials) > 2) $initials = substr($initials, 0, 2);

                      $curStatus = $st->attendance_status ?: 'Present';
                      if ($curStatus === 'Leave') $curStatus = 'Excused';
                    ?>
                    <tr class="hover:bg-surface-container-low transition-colors attendance-row" data-student-id="<?php echo $st->student_id; ?>">
                      <!-- Roll Number -->
                      <td class="px-3 py-3 text-center font-mono font-semibold text-on-surface whitespace-nowrap">
                        <?php echo html_escape($st->roll_number ?: '—'); ?>
                      </td>

                      <!-- Admission Number -->
                      <td class="px-4 py-3 font-medium text-on-surface-variant whitespace-nowrap">
                        <?php echo html_escape($st->admission_number); ?>
                      </td>

                      <!-- Student Name & Photo -->
                      <td class="px-4 py-3 text-on-surface whitespace-nowrap">
                        <div class="flex items-center gap-3">
                          <?php if (!empty($st->photo)): ?>
                            <img src="<?php echo base_url('uploads/students/' . $st->photo); ?>" alt="<?php echo html_escape($fullName); ?>" class="w-9 h-9 rounded-full object-cover shrink-0 border border-outline-variant/50"/>
                          <?php else: ?>
                            <div class="w-9 h-9 rounded-full bg-primary-fixed/40 text-primary font-bold flex items-center justify-center text-[12px] shrink-0">
                              <?php echo html_escape($initials); ?>
                            </div>
                          <?php endif; ?>
                          <div>
                            <div class="font-semibold text-on-surface text-body-md"><?php echo html_escape($fullName); ?></div>
                            <div class="text-[12px] text-on-surface-variant"><?php echo html_escape($st->class_name . ' ' . $st->section_name); ?></div>
                          </div>
                        </div>
                      </td>

                      <!-- Attendance Status Radio Controls -->
                      <td class="px-4 py-3 text-center whitespace-nowrap">
                        <div class="inline-flex items-center gap-1.5 p-1 rounded-xl bg-surface-container-low border border-outline-variant/40">
                          <!-- Present -->
                          <label class="cursor-pointer">
                            <input type="radio" name="attendance[<?php echo $st->student_id; ?>][status]" value="Present" class="sr-only peer att-radio" <?php echo ($curStatus === 'Present') ? 'checked' : ''; ?> onchange="updateSummaryCounters()">
                            <span class="px-3 py-1.5 rounded-lg text-[13px] font-semibold flex items-center gap-1 border border-transparent text-on-surface-variant peer-checked:bg-secondary-container peer-checked:text-on-secondary-container peer-checked:border-secondary transition-all">
                              <span class="material-symbols-outlined text-[16px]">check</span>Present
                            </span>
                          </label>

                          <!-- Absent -->
                          <label class="cursor-pointer">
                            <input type="radio" name="attendance[<?php echo $st->student_id; ?>][status]" value="Absent" class="sr-only peer att-radio" <?php echo ($curStatus === 'Absent') ? 'checked' : ''; ?> onchange="updateSummaryCounters()">
                            <span class="px-3 py-1.5 rounded-lg text-[13px] font-semibold flex items-center gap-1 border border-transparent text-on-surface-variant peer-checked:bg-error-container peer-checked:text-on-error-container peer-checked:border-error transition-all">
                              <span class="material-symbols-outlined text-[16px]">close</span>Absent
                            </span>
                          </label>

                          <!-- Late -->
                          <label class="cursor-pointer">
                            <input type="radio" name="attendance[<?php echo $st->student_id; ?>][status]" value="Late" class="sr-only peer att-radio" <?php echo ($curStatus === 'Late') ? 'checked' : ''; ?> onchange="updateSummaryCounters()">
                            <span class="px-3 py-1.5 rounded-lg text-[13px] font-semibold flex items-center gap-1 border border-transparent text-on-surface-variant peer-checked:bg-amber-100 peer-checked:text-amber-900 peer-checked:border-amber-500 transition-all">
                              <span class="material-symbols-outlined text-[16px]">schedule</span>Late
                            </span>
                          </label>

                          <!-- Excused -->
                          <label class="cursor-pointer">
                            <input type="radio" name="attendance[<?php echo $st->student_id; ?>][status]" value="Excused" class="sr-only peer att-radio" <?php echo ($curStatus === 'Excused') ? 'checked' : ''; ?> onchange="updateSummaryCounters()">
                            <span class="px-3 py-1.5 rounded-lg text-[13px] font-semibold flex items-center gap-1 border border-transparent text-on-surface-variant peer-checked:bg-primary-fixed peer-checked:text-on-primary-fixed peer-checked:border-primary transition-all">
                              <span class="material-symbols-outlined text-[16px]">event_available</span>Excused
                            </span>
                          </label>
                        </div>
                      </td>

                      <!-- Remarks Field -->
                      <td class="px-4 py-3 whitespace-nowrap min-w-[200px]">
                        <input type="text" name="attendance[<?php echo $st->student_id; ?>][remarks]" value="<?php echo html_escape($st->remarks); ?>" placeholder="Notes (e.g. Medical leave, bus delay)" class="w-full px-3 py-1.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-1 focus:ring-primary focus:border-primary placeholder-on-surface-variant/40"/>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Bottom Save Action Bar -->
        <?php if (!empty($students)): ?>
          <div class="flex items-center justify-between p-4 rounded-xl bg-surface-container-lowest border border-outline-variant/50 elevation-1">
            <span class="text-body-md text-on-surface-variant font-medium">Ready to save daily attendance for <?php echo count($students); ?> student(s).</span>
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer">
              <span class="material-symbols-outlined text-[18px]">save</span>
              <?php echo ($is_already_marked) ? 'Update Attendance' : 'Save Attendance'; ?>
            </button>
          </div>
        <?php endif; ?>
      <?php echo form_close(); ?>

    <?php endif; ?>

    <!-- Live Counter & Bulk Action Script -->
    <script>
      function updateSummaryCounters() {
        var total = document.querySelectorAll('.attendance-row').length;
        var present = document.querySelectorAll('input.att-radio[value="Present"]:checked').length;
        var absent = document.querySelectorAll('input.att-radio[value="Absent"]:checked').length;
        var late = document.querySelectorAll('input.att-radio[value="Late"]:checked').length;
        var excused = document.querySelectorAll('input.att-radio[value="Excused"]:checked').length;

        var elTot = document.getElementById('cnt-total');
        var elPres = document.getElementById('cnt-present');
        var elAbs = document.getElementById('cnt-absent');
        var elLate = document.getElementById('cnt-late');
        var elExc = document.getElementById('cnt-excused');

        if (elTot) elTot.textContent = total;
        if (elPres) elPres.textContent = present;
        if (elAbs) elAbs.textContent = absent;
        if (elLate) elLate.textContent = late;
        if (elExc) elExc.textContent = excused;
      }

      function markAllStatus(statusValue) {
        var inputs = document.querySelectorAll('input.att-radio[value="' + statusValue + '"]');
        inputs.forEach(function(inp) {
          inp.checked = true;
        });
        updateSummaryCounters();
      }

      document.addEventListener('DOMContentLoaded', function() {
        updateSummaryCounters();
      });
    </script>
