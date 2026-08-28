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
        <h2 class="font-headline-md text-headline-md text-on-surface">Class Attendance</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Multi-section attendance overview and comparative metrics for a selected class.</p>
      </div>
      <div class="flex items-center gap-2 flex-wrap shrink-0">
        <a href="<?php echo site_url('attendance'); ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md hover:bg-surface-container-high transition-colors">
          <span class="material-symbols-outlined text-[18px]">dashboard</span>Attendance Dashboard
        </a>
      </div>
    </div>

    <!-- Filter Bar (Academic Year, Date, Class) -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4 mb-6">
      <form method="get" action="<?php echo site_url('attendance/class_attendance'); ?>" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
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

        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Date *</label>
          <input type="date" name="date" value="<?php echo html_escape($date); ?>" min="<?php echo html_escape($current_year->start_date ?? ''); ?>" max="<?php echo html_escape($current_year->end_date ?? ''); ?>" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
        </div>

        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Class *</label>
          <select name="class_id" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
            <?php foreach ($classes as $cls): ?>
              <option value="<?php echo $cls->class_id; ?>" <?php echo ($class_id == $cls->class_id) ? 'selected' : ''; ?>>
                <?php echo html_escape($cls->class_name); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </form>
    </div>

    <!-- Section Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-6">
      <?php if (empty($sections_overview)): ?>
        <div class="col-span-full p-8 rounded-xl bg-surface-container-lowest border border-outline-variant/50 text-center elevation-1">
          <span class="material-symbols-outlined text-[48px] text-outline mb-2">school</span>
          <h4 class="font-title-md text-title-md text-on-surface font-semibold">No Sections Found</h4>
          <p class="text-body-md text-on-surface-variant mt-1">There are no active sections created under this class.</p>
        </div>
      <?php else: ?>
        <?php foreach ($sections_overview as $sec): ?>
          <div class="p-5 rounded-2xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 hover:border-primary/40 transition-all flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between pb-3 border-b border-outline-variant/40 mb-4">
                <div>
                  <h3 class="font-title-md text-title-md font-bold text-on-surface"><?php echo html_escape($sec->class_name . ' — ' . $sec->section_name); ?></h3>
                  <div class="text-[12px] text-on-surface-variant font-medium"><?php echo $sec->total_students; ?> Enrolled Students</div>
                </div>
                <div class="text-right">
                  <div class="text-xl font-bold <?php echo ($sec->percentage >= 90) ? 'text-secondary' : (($sec->percentage >= 75) ? 'text-amber-600' : 'text-error'); ?>">
                    <?php echo $sec->percentage; ?>%
                  </div>
                  <div class="text-[11px] text-on-surface-variant">Present</div>
                </div>
              </div>

              <!-- Stats Breakdown Grid -->
              <div class="grid grid-cols-4 gap-2 text-center mb-4">
                <div class="p-2.5 rounded-lg bg-secondary-container/20 border border-secondary/20">
                  <div class="font-bold text-secondary text-title-md"><?php echo $sec->present_count ?: 0; ?></div>
                  <div class="text-[11px] text-on-surface-variant">Present</div>
                </div>
                <div class="p-2.5 rounded-lg bg-error-container/20 border border-error/20">
                  <div class="font-bold text-error text-title-md"><?php echo $sec->absent_count ?: 0; ?></div>
                  <div class="text-[11px] text-on-surface-variant">Absent</div>
                </div>
                <div class="p-2.5 rounded-lg bg-amber-100 dark:bg-amber-950/30 border border-amber-300">
                  <div class="font-bold text-amber-900 dark:text-amber-300 text-title-md"><?php echo $sec->late_count ?: 0; ?></div>
                  <div class="text-[11px] text-on-surface-variant">Late</div>
                </div>
                <div class="p-2.5 rounded-lg bg-primary-fixed/30 border border-primary/20">
                  <div class="font-bold text-primary text-title-md"><?php echo $sec->excused_count ?: 0; ?></div>
                  <div class="text-[11px] text-on-surface-variant">Excused</div>
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2 pt-3 border-t border-outline-variant/40 mt-2">
              <a href="<?php echo site_url('attendance/section_attendance?class_id=' . $sec->class_id . '&section_id=' . $sec->section_id . '&date=' . $date); ?>" class="flex-1 text-center py-2 px-3 rounded-lg border border-outline-variant text-on-surface-variant hover:bg-surface-container-high text-body-md font-medium transition-colors">
                View Details
              </a>
              <a href="<?php echo site_url('attendance/daily?class_id=' . $sec->class_id . '&section_id=' . $sec->section_id . '&date=' . $date); ?>" class="flex-1 text-center py-2 px-3 rounded-lg bg-secondary text-on-secondary hover:bg-on-secondary-fixed-variant text-body-md font-semibold transition-colors shadow-sm">
                <?php echo ($sec->is_marked) ? 'Edit Daily' : 'Mark Daily'; ?>
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
