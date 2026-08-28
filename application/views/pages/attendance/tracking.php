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
        <h2 class="font-headline-md text-headline-md text-on-surface">Absent / Late / Excused Tracking</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Dedicated tracking monitor for students absent, late, or excused with parent contact details.</p>
      </div>
      <div class="flex items-center gap-2 flex-wrap shrink-0">
        <a href="<?php echo site_url('attendance/notifications'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold hover:bg-on-secondary-fixed-variant transition-colors shadow-sm">
          <span class="material-symbols-outlined text-[18px]">notifications</span>Parent Notifications
        </a>
      </div>
    </div>

    <!-- Quick Status Tabs -->
    <div class="flex gap-2 border-b border-outline-variant/60 mb-6 overflow-x-auto">
      <?php
        $curTab = $filters['status_filter'] ?: 'All';
        $tabs = array(
          'All'     => 'All Exceptions',
          'Absent'  => 'Absent Only',
          'Late'    => 'Late Arrivals',
          'Excused' => 'Excused / Leave'
        );
      ?>
      <?php foreach ($tabs as $k => $label): ?>
        <?php $isActive = ($curTab === $k); ?>
        <a href="<?php echo site_url('attendance/tracking?' . http_build_query(array_merge($_GET, array('status' => $k)))); ?>" class="px-4 py-2.5 text-body-md font-medium border-b-2 <?php echo $isActive ? 'border-secondary text-primary font-semibold' : 'border-transparent text-on-surface-variant hover:text-on-surface'; ?> transition-colors whitespace-nowrap">
          <?php echo $label; ?>
        </a>
      <?php endforeach; ?>
    </div>

    <!-- Filter Bar -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4 mb-6">
      <form method="get" action="<?php echo site_url('attendance/tracking'); ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <input type="hidden" name="status" value="<?php echo html_escape($filters['status_filter']); ?>"/>

        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Class</label>
          <select name="class_id" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
            <option value="">All Classes</option>
            <?php foreach ($classes as $cls): ?>
              <option value="<?php echo $cls->class_id; ?>" <?php echo ($filters['class_id'] == $cls->class_id) ? 'selected' : ''; ?>>
                <?php echo html_escape($cls->class_name); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Section</label>
          <select name="section_id" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
            <option value="">All Sections</option>
            <?php foreach ($sections as $sec): ?>
              <option value="<?php echo $sec->section_id; ?>" <?php echo ($filters['section_id'] == $sec->section_id) ? 'selected' : ''; ?>>
                <?php echo html_escape($sec->class_name . ' ' . $sec->section_name); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">From Date</label>
          <input type="date" name="from_date" value="<?php echo html_escape($filters['from_date']); ?>" min="<?php echo html_escape($current_year->start_date ?? ''); ?>" max="<?php echo html_escape($current_year->end_date ?? ''); ?>" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
        </div>

        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">To Date</label>
          <input type="date" name="to_date" value="<?php echo html_escape($filters['to_date']); ?>" min="<?php echo html_escape($current_year->start_date ?? ''); ?>" max="<?php echo html_escape($current_year->end_date ?? ''); ?>" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
        </div>
      </form>
    </div>

    <!-- Tracking Records Table -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden mb-6">
      <div class="p-4 border-b border-outline-variant/50 flex items-center justify-between">
        <span class="text-body-md font-semibold text-on-surface">Found <strong><?php echo count($records); ?></strong> incident(s) requiring tracking</span>
      </div>

      <div class="table-scroll overflow-x-auto">
        <table class="w-full data-table zebra border-collapse">
          <thead>
            <tr class="border-b border-outline-variant/60 bg-surface-container-low/50">
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Date</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Student Name</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Class & Section</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Status</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Type / Period</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Parent / Guardian</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Remarks</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Profile</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/40">
            <?php if (empty($records)): ?>
              <tr><td colspan="8" class="px-4 py-8 text-center text-on-surface-variant text-body-md">No absent, late, or excused records found for this period.</td></tr>
            <?php else: ?>
              <?php foreach ($records as $r): ?>
                <?php
                  $fullName = trim($r->first_name . ' ' . $r->last_name);
                  $badgeClass = 'bg-error-container text-on-error-container';
                  if ($r->attendance_status === 'Late') $badgeClass = 'bg-amber-100 text-amber-900';
                  elseif (in_array($r->attendance_status, array('Excused', 'Leave'))) $badgeClass = 'bg-primary-fixed text-on-primary-fixed';
                ?>
                <tr class="hover:bg-surface-container-low transition-colors">
                  <td class="px-4 py-3 font-mono font-medium text-on-surface whitespace-nowrap">
                    <?php echo date('d M Y', strtotime($r->attendance_date)); ?>
                  </td>
                  <td class="px-4 py-3 whitespace-nowrap">
                    <div class="font-semibold text-on-surface"><?php echo html_escape($fullName); ?></div>
                    <div class="text-[12px] text-on-surface-variant"><?php echo html_escape($r->admission_number); ?></div>
                  </td>
                  <td class="px-4 py-3 text-body-md text-on-surface-variant whitespace-nowrap">
                    <?php echo html_escape($r->class_name . ' ' . $r->section_name); ?>
                  </td>
                  <td class="px-4 py-3 text-center whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[12px] font-semibold <?php echo $badgeClass; ?>">
                      <?php echo html_escape($r->attendance_status); ?>
                    </span>
                  </td>
                  <td class="px-4 py-3 whitespace-nowrap text-body-md">
                    <?php echo ($r->attendance_type === 'Period-wise') ? html_escape($r->period_name ?: 'Period') : 'Daily Session'; ?>
                  </td>
                  <td class="px-4 py-3 whitespace-nowrap text-body-md">
                    <div class="font-medium text-on-surface"><?php echo html_escape($r->guardian_name ?: '—'); ?></div>
                    <?php if (!empty($r->guardian_phone)): ?>
                      <div class="text-[12px] text-primary flex items-center gap-1 font-mono">
                        <span class="material-symbols-outlined text-[14px]">call</span><?php echo html_escape($r->guardian_phone); ?>
                      </div>
                    <?php endif; ?>
                  </td>
                  <td class="px-4 py-3 text-body-md text-on-surface-variant max-w-[200px] truncate">
                    <?php echo html_escape($r->remarks ?: '—'); ?>
                  </td>
                  <td class="px-4 py-3 text-center whitespace-nowrap">
                    <a href="<?php echo site_url('students/profile/' . $r->student_id); ?>" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-surface-container-high hover:bg-primary-fixed hover:text-primary transition-colors text-[12px] font-medium">
                      <span class="material-symbols-outlined text-[15px]">person</span>Profile
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
