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

    <!-- Header & Action Links -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <div class="flex items-center gap-2.5">
          <h2 class="font-headline-md text-headline-md text-on-surface">Mark Attendance</h2>
          <?php if ($is_higher_sec): ?>
            <span class="px-3 py-1 rounded-full text-[12px] font-semibold bg-primary-fixed text-on-primary-fixed border border-primary/30">
              Period-wise (+1 / +2)
            </span>
          <?php else: ?>
            <span class="px-3 py-1 rounded-full text-[12px] font-semibold bg-secondary-container text-on-secondary-container border border-secondary/30">
              Daily Attendance (LKG – 10)
            </span>
          <?php endif; ?>
        </div>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">
          <?php if ($is_higher_sec): ?>
            Take period-wise attendance for Higher Secondary (+1 &amp; +2). Allowed statuses: Present, Half Day, Absent, Late Coming.
          <?php else: ?>
            Take one daily morning attendance per student for LKG through Class 10. Allowed statuses: Present, Half Day, Absent.
          <?php endif; ?>
        </p>
      </div>
      <div class="flex items-center gap-2 flex-wrap shrink-0">
        <a href="<?php echo site_url('attendance/class_attendance?class_id=' . $class_id . '&date=' . $date . '&academic_year_id=' . $year_id); ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md hover:bg-surface-container-high transition-colors">
          <span class="material-symbols-outlined text-[18px]">co_present</span>Class Attendance (View)
        </a>
        <a href="<?php echo site_url('student-attendance/view?class_id=' . $class_id . '&academic_year_id=' . $year_id); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold hover:bg-on-secondary-fixed-variant transition-colors shadow-sm">
          <span class="material-symbols-outlined text-[18px]">visibility</span>VIEW ATTENDANCE
        </a>
      </div>
    </div>

    <!-- Filter Bar (Academic Year, Date, Class, Section, and Subject/Period for +1/+2) -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4 mb-6">
      <form method="get" action="<?php echo site_url('attendance/mark_attendance'); ?>" class="grid grid-cols-1 sm:grid-cols-2 <?php echo $is_higher_sec ? 'lg:grid-cols-6' : 'lg:grid-cols-4'; ?> gap-4">
        <!-- Academic Year -->
        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Academic Year *</label>
          <select name="academic_year_id" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
            <?php foreach ($years as $y): ?>
              <option value="<?php echo $y->academic_year_id; ?>" <?php echo ($year_id == $y->academic_year_id) ? 'selected' : ''; ?>>
                <?php echo html_escape($y->year_name); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Date (Can select any past date, not restricted to today) -->
        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Date *</label>
          <input type="date" name="date" value="<?php echo html_escape($date); ?>" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
        </div>

        <!-- Class -->
        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Class *</label>
          <select name="class_id" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
            <?php foreach ($classes as $cls): ?>
              <option value="<?php echo $cls->class_id; ?>" <?php echo ($class_id == $cls->class_id) ? 'selected' : ''; ?>>
                <?php echo html_escape($cls->class_name); ?>
                <?php echo is_higher_secondary_class($cls) ? ' (+1/+2 Period)' : ' (Daily)'; ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Division / Session -->
        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Division / Session *</label>
          <select name="division_id" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
            <?php if (empty($sections)): ?>
              <option value="<?php echo $section_id ?: 0; ?>">Division A (Default)</option>
            <?php else: ?>
              <?php foreach ($sections as $sec): ?>
                <option value="<?php echo ($sec->division_id ?? $sec->section_id); ?>" <?php echo ($section_id == ($sec->division_id ?? $sec->section_id)) ? 'selected' : ''; ?>>
                  Division <?php echo html_escape($sec->division_name ?? $sec->section_name); ?>
                </option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
        </div>

        <?php if ($is_higher_sec): ?>
          <!-- Subject (+1/+2 only) -->
          <div>
            <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Subject</label>
            <select name="subject_id" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
              <option value="">-- All Subjects --</option>
              <?php foreach ($subjects as $sub): ?>
                <option value="<?php echo $sub->subject_id; ?>" <?php echo ($subject_id == $sub->subject_id) ? 'selected' : ''; ?>>
                  <?php echo html_escape($sub->subject_name); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Period (+1/+2 only) -->
          <div>
            <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Period *</label>
            <select name="period_id" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary font-semibold">
              <option value="">-- Choose Period --</option>
              <?php foreach ($periods as $p): ?>
                <option value="<?php echo $p->period_id; ?>" <?php echo ($period_id == $p->period_id) ? 'selected' : ''; ?>>
                  <?php echo html_escape($p->period_name . ' (' . date('h:i A', strtotime($p->start_time)) . ' - ' . date('h:i A', strtotime($p->end_time)) . ')'); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        <?php endif; ?>
      </form>
    </div>

    <!-- Higher Secondary Prompt if Period is not selected -->
    <?php if ($is_higher_sec && empty($period_id)): ?>
      <div class="p-8 rounded-2xl bg-surface-container-lowest border border-outline-variant/50 text-center elevation-1 mb-8">
        <span class="material-symbols-outlined text-[48px] text-primary mb-2">schedule</span>
        <h4 class="font-title-md text-title-md text-on-surface font-semibold">Select a Period to Begin</h4>
        <p class="text-body-md text-on-surface-variant mt-1 max-w-md mx-auto">
          Higher Secondary (+1 &amp; +2) requires period-wise attendance. Please select a period from the filter above to load students.
        </p>
      </div>
    <?php elseif (empty($students)): ?>
      <!-- No students found in section -->
      <div class="p-8 rounded-2xl bg-surface-container-lowest border border-outline-variant/50 text-center elevation-1 mb-8">
        <span class="material-symbols-outlined text-[48px] text-outline mb-2">group_off</span>
        <h4 class="font-title-md text-title-md text-on-surface font-semibold">No Enrolled Students Found</h4>
        <p class="text-body-md text-on-surface-variant mt-1">There are no active students enrolled in this class and division for the selected academic year.</p>
      </div>
    <?php else: ?>
      <!-- Attendance Roll Sheet -->
      <div class="elevation-1 rounded-2xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden mb-8">
        <!-- Sheet Header Bar -->
        <div class="p-5 border-b border-outline-variant/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-surface-container-low/40">
          <div>
            <div class="flex items-center gap-2.5 flex-wrap">
              <h3 class="font-title-lg text-title-lg font-bold text-on-surface">
                <?php echo html_escape($selected_class ? $selected_class->class_name : 'Class'); ?> — Division <?php echo html_escape($selected_section ? ($selected_section->division_name ?? $selected_section->section_name) : 'A'); ?>
              </h3>
              <?php if ($is_already_marked): ?>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-900 border border-amber-300 flex items-center gap-1">
                  <span class="material-symbols-outlined text-[14px]">edit</span>Editing Saved Attendance
                </span>
              <?php else: ?>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-secondary-container text-on-secondary-container border border-secondary/30 flex items-center gap-1">
                  <span class="material-symbols-outlined text-[14px]">add_circle</span>New Attendance
                </span>
              <?php endif; ?>
            </div>
            <p class="text-body-md text-on-surface-variant mt-1">
              Date: <strong class="text-on-surface"><?php echo date('d M Y', strtotime($date)); ?></strong>
              <?php if ($is_higher_sec && $period_id): ?>
                <?php
                  $cur_p = null;
                  foreach ($periods as $p) { if ((int)$p->period_id === (int)$period_id) { $cur_p = $p; break; } }
                ?>
                • Period: <strong class="text-on-surface"><?php echo html_escape($cur_p ? $cur_p->period_name : 'Period ' . $period_id); ?></strong>
              <?php endif; ?>
            </p>
          </div>

          <!-- Quick Mark Actions -->
          <div class="flex items-center gap-2 flex-wrap">
            <span class="text-label-md font-medium text-on-surface-variant mr-1">Quick Mark:</span>
            <button type="button" onclick="markAllStatus('Present')" class="px-3 py-1.5 rounded-lg bg-secondary-container text-on-secondary-container hover:opacity-90 transition-opacity text-label-md font-semibold flex items-center gap-1 cursor-pointer">
              <span class="material-symbols-outlined text-[16px]">done_all</span>All Present
            </button>
            <button type="button" onclick="markAllStatus('Half Day')" class="px-3 py-1.5 rounded-lg bg-amber-100 text-amber-900 hover:opacity-90 transition-opacity text-label-md font-semibold flex items-center gap-1 cursor-pointer">
              <span class="material-symbols-outlined text-[16px]">timelapse</span>All Half Day
            </button>
            <?php if ($is_higher_sec): ?>
              <button type="button" onclick="markAllStatus('Late Coming')" class="px-3 py-1.5 rounded-lg bg-indigo-100 text-indigo-900 hover:opacity-90 transition-opacity text-label-md font-semibold flex items-center gap-1 cursor-pointer">
                <span class="material-symbols-outlined text-[16px]">schedule</span>All Late Coming
              </button>
            <?php endif; ?>
            <button type="button" onclick="markAllStatus('Absent')" class="px-3 py-1.5 rounded-lg bg-error-container text-on-error-container hover:opacity-90 transition-opacity text-label-md font-semibold flex items-center gap-1 cursor-pointer">
              <span class="material-symbols-outlined text-[16px]">close</span>All Absent
            </button>
          </div>
        </div>

        <?php echo form_open('attendance/mark_attendance', array('id' => 'mark-attendance-form')); ?>
          <input type="hidden" name="date" value="<?php echo html_escape($date); ?>"/>
          <input type="hidden" name="academic_year_id" value="<?php echo html_escape($year_id); ?>"/>
          <input type="hidden" name="class_id" value="<?php echo html_escape($class_id); ?>"/>
          <input type="hidden" name="division_id" value="<?php echo html_escape($section_id); ?>"/>
          <?php if ($is_higher_sec): ?>
            <input type="hidden" name="period_id" value="<?php echo html_escape($period_id); ?>"/>
            <input type="hidden" name="subject_id" value="<?php echo html_escape($subject_id); ?>"/>
          <?php endif; ?>

          <!-- Students Table -->
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-surface-container-high/70 text-on-surface border-b border-outline-variant/40 text-label-md uppercase tracking-wider font-semibold text-[11px]">
                  <th class="py-3 px-4 w-12 text-center">Roll #</th>
                  <th class="py-3 px-4 min-w-[200px]">Student</th>
                  <th class="py-3 px-4 text-center">Attendance Status</th>
                  <th class="py-3 px-4 min-w-[200px]">Remarks</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-outline-variant/30">
                <?php foreach ($students as $st): ?>
                  <?php
                    $fullName  = trim($st->first_name . ' ' . $st->last_name);
                    $curStatus = $st->attendance_status ?: 'Present';
                    // Re-map historical Leave or Excused safely
                    if (in_array($curStatus, array('Leave', 'Excused'))) {
                        $curStatus = 'Present';
                    }
                    if (!$is_higher_sec && in_array($curStatus, array('Late', 'Late Coming'))) {
                        $curStatus = 'Present';
                    }
                  ?>
                  <tr class="hover:bg-surface-container-low/60 transition-colors att-student-row">
                    <!-- Roll Number -->
                    <td class="py-3.5 px-4 text-center font-mono font-semibold text-on-surface whitespace-nowrap">
                      <?php echo html_escape($st->roll_number ?: '—'); ?>
                    </td>

                    <!-- Student Name & Admission -->
                    <td class="py-3.5 px-4 whitespace-nowrap">
                      <div class="flex items-center gap-3">
                        <?php if (!empty($st->photo)): ?>
                          <img src="<?php echo base_url('uploads/students/' . $st->photo); ?>" alt="Photo" class="w-9 h-9 rounded-full object-cover border border-outline-variant/40 shrink-0"/>
                        <?php else: ?>
                          <div class="w-9 h-9 rounded-full bg-surface-container-high text-on-surface-variant flex items-center justify-center font-bold text-[12px] shrink-0 border border-outline-variant/40">
                            <?php echo strtoupper(substr($st->first_name ?: 'S', 0, 1)); ?>
                          </div>
                        <?php endif; ?>
                        <div>
                          <div class="font-semibold text-on-surface text-body-md"><?php echo html_escape($fullName); ?></div>
                          <div class="text-[11px] text-on-surface-variant font-mono">Adm: <?php echo html_escape($st->admission_number); ?></div>
                        </div>
                      </div>
                    </td>

                    <!-- Attendance Status Radio Buttons -->
                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                      <div class="inline-flex items-center gap-1.5 p-1 rounded-xl bg-surface-container-low border border-outline-variant/40">
                        <!-- Present -->
                        <label class="cursor-pointer">
                          <input type="radio" name="attendance[<?php echo $st->student_id; ?>][status]" value="Present" class="sr-only peer att-radio-input" <?php echo ($curStatus === 'Present') ? 'checked' : ''; ?>>
                          <span class="px-3 py-1.5 rounded-lg text-[13px] font-semibold flex items-center gap-1 border border-transparent text-on-surface-variant peer-checked:bg-secondary-container peer-checked:text-on-secondary-container peer-checked:border-secondary transition-all">
                            <span class="material-symbols-outlined text-[16px]">check</span>Present
                          </span>
                        </label>

                        <!-- Half Day -->
                        <label class="cursor-pointer">
                          <input type="radio" name="attendance[<?php echo $st->student_id; ?>][status]" value="Half Day" class="sr-only peer att-radio-input" <?php echo (in_array($curStatus, array('Half Day', 'Late / Half Day', 'Half-day'))) ? 'checked' : ''; ?>>
                          <span class="px-3 py-1.5 rounded-lg text-[13px] font-semibold flex items-center gap-1 border border-transparent text-on-surface-variant peer-checked:bg-amber-100 peer-checked:text-amber-900 peer-checked:border-amber-400 transition-all">
                            <span class="material-symbols-outlined text-[16px]">timelapse</span>Half Day
                          </span>
                        </label>

                        <!-- Late Coming (+1 / +2 ONLY) -->
                        <?php if ($is_higher_sec): ?>
                          <label class="cursor-pointer">
                            <input type="radio" name="attendance[<?php echo $st->student_id; ?>][status]" value="Late Coming" class="sr-only peer att-radio-input" <?php echo (in_array($curStatus, array('Late', 'Late Coming'))) ? 'checked' : ''; ?>>
                            <span class="px-3 py-1.5 rounded-lg text-[13px] font-semibold flex items-center gap-1 border border-transparent text-on-surface-variant peer-checked:bg-indigo-100 peer-checked:text-indigo-900 peer-checked:border-indigo-400 transition-all">
                              <span class="material-symbols-outlined text-[16px]">schedule</span>Late Coming
                            </span>
                          </label>
                        <?php endif; ?>

                        <!-- Absent -->
                        <label class="cursor-pointer">
                          <input type="radio" name="attendance[<?php echo $st->student_id; ?>][status]" value="Absent" class="sr-only peer att-radio-input" <?php echo ($curStatus === 'Absent') ? 'checked' : ''; ?>>
                          <span class="px-3 py-1.5 rounded-lg text-[13px] font-semibold flex items-center gap-1 border border-transparent text-on-surface-variant peer-checked:bg-error-container peer-checked:text-on-error-container peer-checked:border-error transition-all">
                            <span class="material-symbols-outlined text-[16px]">close</span>Absent
                          </span>
                        </label>
                      </div>
                    </td>

                    <!-- Remarks Input -->
                    <td class="py-3.5 px-4 whitespace-nowrap">
                      <input type="text" name="attendance[<?php echo $st->student_id; ?>][remarks]" value="<?php echo html_escape($st->remarks ?? ''); ?>" placeholder="Optional remarks..." class="w-full px-3 py-1.5 rounded-lg border border-outline-variant/60 bg-surface-container-lowest text-[13px] focus:ring-1 focus:ring-primary focus:border-primary"/>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <!-- Bottom Sticky Submission Bar -->
          <div class="p-5 border-t border-outline-variant/40 bg-surface-container-low/40 flex items-center justify-between gap-4 flex-wrap sticky bottom-0 z-10 backdrop-blur-md">
            <div class="text-body-md text-on-surface-variant font-medium">
              Total Students: <strong class="text-on-surface"><?php echo count($students); ?></strong>
            </div>
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer">
              <span class="material-symbols-outlined text-[18px]">save</span>
              <?php echo ($is_already_marked) ? 'Update Attendance' : 'Save Attendance'; ?>
            </button>
          </div>
        <?php echo form_close(); ?>
      </div>

      <script>
        function markAllStatus(status) {
          document.querySelectorAll('.att-radio-input[value="' + status + '"]').forEach(function(radio) {
            radio.checked = true;
          });
        }
      </script>
    <?php endif; ?>
