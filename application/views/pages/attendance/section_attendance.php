<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

    <!-- Flash Messages -->
    <?php if ($this->session->flashdata('success')): ?>
      <div class="mb-4 p-3.5 rounded-xl bg-secondary-container text-on-secondary-container text-body-md font-medium flex items-center gap-2 border border-secondary/20">
        <span class="material-symbols-outlined text-[20px] text-secondary">check_circle</span>
        <?php echo html_escape($this->session->flashdata('success')); ?>
      </div>
    <?php endif; ?>

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h2 class="font-headline-md text-headline-md text-on-surface">Section Attendance</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Detailed attendance records and performance metrics for an individual section.</p>
      </div>
      <div class="flex items-center gap-2 flex-wrap shrink-0">
        <a href="<?php echo site_url('attendance/daily?class_id=' . $class_id . '&section_id=' . $section_id . '&date=' . $date); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold hover:bg-on-secondary-fixed-variant transition-colors shadow-sm">
          <span class="material-symbols-outlined text-[18px]">edit</span>Edit Daily Attendance
        </a>
      </div>
    </div>

    <!-- Filter Bar -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4 mb-6">
      <form method="get" action="<?php echo site_url('attendance/section_attendance'); ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Academic Year</label>
          <select name="academic_year_id" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
            <?php foreach ($years as $y): ?>
              <option value="<?php echo $y->academic_year_id; ?>" <?php echo ($year_id == $y->academic_year_id) ? 'selected' : ''; ?>>
                <?php echo html_escape($y->year_name); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Date</label>
          <input type="date" name="date" value="<?php echo html_escape($date); ?>" min="<?php echo html_escape($current_year->start_date ?? ''); ?>" max="<?php echo html_escape($current_year->end_date ?? ''); ?>" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
        </div>

        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Class</label>
          <select name="class_id" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
            <?php foreach ($classes as $cls): ?>
              <option value="<?php echo $cls->class_id; ?>" <?php echo ($class_id == $cls->class_id) ? 'selected' : ''; ?>>
                <?php echo html_escape($cls->class_name); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Section</label>
          <select name="section_id" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
            <?php foreach ($sections as $sec): ?>
              <option value="<?php echo $sec->section_id; ?>" <?php echo ($section_id == $sec->section_id) ? 'selected' : ''; ?>>
                <?php echo html_escape($sec->class_name . ' ' . $sec->section_name); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </form>
    </div>

    <!-- Attendance Summary Cards at Top -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
      <div class="p-4 rounded-xl bg-surface-container-lowest border border-outline-variant/50 elevation-1">
        <div class="text-[12px] font-medium text-on-surface-variant uppercase">Total Enrolled</div>
        <div class="text-2xl font-bold text-on-surface mt-1"><?php echo $stats->total_students; ?></div>
      </div>
      <div class="p-4 rounded-xl bg-surface-container-lowest border border-outline-variant/50 elevation-1">
        <div class="text-[12px] font-medium text-secondary uppercase">Present</div>
        <div class="text-2xl font-bold text-secondary mt-1"><?php echo $stats->present; ?></div>
      </div>
      <div class="p-4 rounded-xl bg-surface-container-lowest border border-outline-variant/50 elevation-1">
        <div class="text-[12px] font-medium text-error uppercase">Absent</div>
        <div class="text-2xl font-bold text-error mt-1"><?php echo $stats->absent; ?></div>
      </div>
      <div class="p-4 rounded-xl bg-surface-container-lowest border border-outline-variant/50 elevation-1">
        <div class="text-[12px] font-medium text-amber-600 uppercase">Late</div>
        <div class="text-2xl font-bold text-amber-600 mt-1"><?php echo $stats->late; ?></div>
      </div>
      <div class="p-4 rounded-xl bg-surface-container-lowest border border-outline-variant/50 elevation-1">
        <div class="text-[12px] font-medium text-primary uppercase">Excused</div>
        <div class="text-2xl font-bold text-primary mt-1"><?php echo $stats->excused; ?></div>
      </div>
      <div class="p-4 rounded-xl bg-secondary-container/20 border border-secondary/30 elevation-1">
        <div class="text-[12px] font-medium text-secondary uppercase">Attendance Rate</div>
        <div class="text-2xl font-bold text-secondary mt-1"><?php echo $stats->percentage; ?>%</div>
      </div>
    </div>

    <!-- Student Attendance List Table -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden mb-6">
      <div class="table-scroll overflow-x-auto">
        <table class="w-full data-table zebra border-collapse">
          <thead>
            <tr class="border-b border-outline-variant/60 bg-surface-container-low/50">
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase w-16">Roll #</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase">Admission #</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase">Student</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase">Status</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase">Remarks</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/40">
            <?php if (empty($students)): ?>
              <tr><td colspan="5" class="px-4 py-8 text-center text-on-surface-variant text-body-md">No students found.</td></tr>
            <?php else: ?>
              <?php foreach ($students as $st): ?>
                <?php
                  $fullName = trim($st->first_name . ' ' . $st->last_name);
                  $status = $st->attendance_status ?: 'Not Marked';
                  $badgeClass = 'bg-surface-container-high text-on-surface-variant';
                  if ($status === 'Present') $badgeClass = 'bg-secondary-container text-on-secondary-container';
                  elseif ($status === 'Absent') $badgeClass = 'bg-error-container text-on-error-container';
                  elseif ($status === 'Late') $badgeClass = 'bg-amber-100 text-amber-900';
                  elseif (in_array($status, array('Excused', 'Leave'))) $badgeClass = 'bg-primary-fixed text-on-primary-fixed';
                ?>
                <tr class="hover:bg-surface-container-low transition-colors">
                  <td class="px-4 py-3 text-center font-mono font-bold text-primary whitespace-nowrap">
                    <?php echo html_escape($st->roll_number ?: '—'); ?>
                  </td>
                  <td class="px-4 py-3 font-medium text-on-surface-variant whitespace-nowrap">
                    <?php echo html_escape($st->admission_number); ?>
                  </td>
                  <td class="px-4 py-3 font-semibold text-on-surface whitespace-nowrap">
                    <a href="<?php echo site_url('students/profile/' . $st->student_id); ?>" class="hover:text-primary hover:underline">
                      <?php echo html_escape($fullName); ?>
                    </a>
                  </td>
                  <td class="px-4 py-3 text-center whitespace-nowrap">
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[12px] font-semibold <?php echo $badgeClass; ?>">
                      <?php echo html_escape($status); ?>
                    </span>
                  </td>
                  <td class="px-4 py-3 text-body-md text-on-surface-variant whitespace-nowrap">
                    <?php echo html_escape($st->remarks ?: '—'); ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
