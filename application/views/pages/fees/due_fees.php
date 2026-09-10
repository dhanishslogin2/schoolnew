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
        <h2 class="font-headline-md text-headline-md text-on-surface">Outstanding & Due Fees</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Track overdue student balances, aging analysis, and trigger payment reminders.</p>
      </div>
      <div class="flex items-center gap-2 flex-wrap shrink-0">
        <a href="<?php echo site_url('fees/reminders'); ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md hover:bg-surface-container-high transition-colors">
          <span class="material-symbols-outlined text-[18px]">notifications_active</span>Fee Reminders
        </a>
      </div>
    </div>

    <!-- Quick Status Tabs -->
    <div class="flex gap-2 border-b border-outline-variant/60 mb-6 overflow-x-auto">
      <?php
        $curStatus = $filters['status'] ?? '';
        $tabs = [
          ''               => 'All Outstanding',
          'Due Today'      => 'Due Today',
          'Upcoming'       => 'Upcoming Dues',
          'Overdue'        => 'Overdue Fees',
          'Partially Paid' => 'Partially Paid',
        ];
      ?>
      <?php foreach ($tabs as $key => $label): ?>
        <?php $isActive = ($curStatus === $key); ?>
        <a href="<?php echo site_url('fees/due_fees?' . http_build_query(array_merge($_GET, array('status' => $key)))); ?>" class="px-4 py-2.5 text-body-md font-medium border-b-2 <?php echo $isActive ? 'border-secondary text-primary font-semibold' : 'border-transparent text-on-surface-variant hover:text-on-surface'; ?> transition-colors whitespace-nowrap">
          <?php echo $label; ?>
        </a>
      <?php endforeach; ?>
    </div>

    <!-- Filter Bar -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4 mb-6">
      <form method="get" action="<?php echo site_url('fees/due_fees'); ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <input type="hidden" name="academic_year_id" value="<?php echo html_escape($filters['academic_year_id'] ?? ''); ?>"/>
        <input type="hidden" name="status" value="<?php echo html_escape($filters['status'] ?? ''); ?>"/>

        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Class</label>
          <select name="class_id" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
            <option value="">All Classes</option>
            <?php foreach ($classes as $c): ?>
              <option value="<?php echo $c->class_id; ?>" <?php echo ($filters['class_id'] == $c->class_id) ? 'selected' : ''; ?>>
                <?php echo html_escape($c->class_name); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Fee Category</label>
          <select name="fee_head_id" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?php echo $cat->fee_head_id; ?>" <?php echo ($filters['fee_head_id'] == $cat->fee_head_id) ? 'selected' : ''; ?>>
                <?php echo html_escape($cat->head_name); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="lg:col-span-2">
          <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Search Student</label>
          <div class="flex items-center gap-2">
            <input type="text" name="search" value="<?php echo html_escape($filters['search'] ?? ''); ?>" placeholder="Admission number or student name..." class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
            <button type="submit" class="px-3.5 py-2 rounded-lg bg-primary text-on-primary text-label-md font-semibold hover:bg-primary/90 transition-colors shrink-0">Filter</button>
          </div>
        </div>
      </form>
    </div>

    <!-- Due Fees Table -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden mb-6">
      <div class="p-4 border-b border-outline-variant/50 flex items-center justify-between">
        <?php $unique_due_students = count(array_unique(array_column($due_fees, 'student_id'))); ?>
        <span class="text-body-md font-semibold text-on-surface">Outstanding Due Records (<?php echo count($due_fees); ?> records across <?php echo $unique_due_students; ?> students)</span>
      </div>

      <div class="table-scroll overflow-x-auto">
        <table class="w-full data-table zebra border-collapse text-body-md">
          <thead>
            <tr class="border-b border-outline-variant/60 bg-surface-container-low/50">
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Student</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Class & Division</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Fee Category</th>
              <th class="text-right px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Total Fee</th>
              <th class="text-right px-4 py-3 text-label-md font-semibold text-secondary uppercase whitespace-nowrap">Paid</th>
              <th class="text-right px-4 py-3 text-label-md font-semibold text-error uppercase whitespace-nowrap">Due Balance</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Due Date</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Aging</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/40">
            <?php if (empty($due_fees)): ?>
              <tr><td colspan="9" class="px-4 py-8 text-center text-on-surface-variant">No pending or overdue fees matching the current filter.</td></tr>
            <?php else: ?>
              <?php foreach ($due_fees as $df): ?>
                <?php
                  $isOverdue = ($df->days_overdue > 0);
                  $agingClass = $isOverdue ? 'text-error font-bold' : 'text-on-surface-variant';
                ?>
                <tr class="hover:bg-surface-container-low transition-colors">
                  <td class="px-4 py-3 whitespace-nowrap font-bold text-on-surface">
                    <a href="<?php echo site_url('students/profile/' . $df->student_id); ?>" class="hover:text-primary hover:underline">
                      <?php echo html_escape($df->first_name . ' ' . $df->last_name); ?>
                    </a>
                    <span class="text-[11px] text-on-surface-variant block font-mono font-normal"><?php echo html_escape($df->admission_number); ?></span>
                  </td>
                  <td class="px-4 py-3 whitespace-nowrap text-on-surface-variant">
                    <?php echo html_escape($df->class_name . ' ' . $df->section_name); ?>
                  </td>
                  <td class="px-4 py-3 whitespace-nowrap font-medium text-on-surface">
                    <?php echo html_escape($df->category_name); ?>
                  </td>
                  <td class="px-4 py-3 text-right font-mono text-on-surface-variant whitespace-nowrap">
                    ₹<?php echo number_format($df->final_amount, 2); ?>
                  </td>
                  <td class="px-4 py-3 text-right font-mono font-bold text-secondary whitespace-nowrap">
                    ₹<?php echo number_format($df->paid_amount, 2); ?>
                  </td>
                  <td class="px-4 py-3 text-right font-mono font-bold text-error whitespace-nowrap text-base">
                    ₹<?php echo number_format($df->due_amount, 2); ?>
                  </td>
                  <td class="px-4 py-3 text-center font-mono text-[12px] whitespace-nowrap">
                    <?php echo date('d M Y', strtotime($df->due_date)); ?>
                  </td>
                  <td class="px-4 py-3 text-center whitespace-nowrap font-mono text-[12px] <?php echo $agingClass; ?>">
                    <?php echo $isOverdue ? ($df->days_overdue . ' days overdue') : 'On Schedule'; ?>
                  </td>
                  <td class="px-4 py-3 text-center whitespace-nowrap">
                    <div class="flex items-center justify-center gap-1.5">
                      <a href="<?php echo site_url('fees/collection?student_id=' . $df->student_id); ?>" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-secondary text-on-secondary hover:bg-on-secondary-fixed-variant transition-colors text-[12px] font-semibold">
                        <span class="material-symbols-outlined text-[16px]">add_card</span>Collect
                      </a>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
