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

    <!-- Header & Quick Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h2 class="font-headline-md text-headline-md text-on-surface">Fees & Finance Dashboard</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Live fee collections, outstanding balances, payment tracking, and revenue overview.</p>
      </div>
      <div class="flex items-center gap-2 flex-wrap shrink-0">
        <a href="<?php echo site_url('fees/collection'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold hover:bg-on-secondary-fixed-variant transition-colors shadow-sm">
          <span class="material-symbols-outlined text-[18px]">add_card</span>Collect Fee
        </a>
        <a href="<?php echo site_url('fees/assignments'); ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md hover:bg-surface-container-high transition-colors">
          <span class="material-symbols-outlined text-[18px]">assignment_ind</span>Assign Fee
        </a>
        <a href="<?php echo site_url('fees/due_fees'); ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md hover:bg-surface-container-high transition-colors">
          <span class="material-symbols-outlined text-[18px]">pending_actions</span>View Due Fees
        </a>
      </div>
    </div>

    <!-- 1. Primary KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <!-- Total Expected -->
      <div class="p-5 rounded-2xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-primary-fixed flex items-center justify-center text-primary shrink-0">
          <span class="material-symbols-outlined text-[26px]">account_balance</span>
        </div>
        <div>
          <span class="text-label-md font-medium text-on-surface-variant block">Total Fee Expected</span>
          <span class="text-2xl font-bold font-mono text-on-surface">₹<?php echo number_format($metrics['total_expected'], 2); ?></span>
        </div>
      </div>

      <!-- Total Collected -->
      <div class="p-5 rounded-2xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-secondary-container flex items-center justify-center text-secondary shrink-0">
          <span class="material-symbols-outlined text-[26px]">check_circle</span>
        </div>
        <div>
          <span class="text-label-md font-medium text-on-surface-variant block">Total Fee Collected</span>
          <span class="text-2xl font-bold font-mono text-secondary">₹<?php echo number_format($metrics['total_collected'], 2); ?></span>
        </div>
      </div>

      <!-- Total Pending -->
      <div class="p-5 rounded-2xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center text-amber-900 shrink-0">
          <span class="material-symbols-outlined text-[26px]">pending</span>
        </div>
        <div>
          <span class="text-label-md font-medium text-on-surface-variant block">Total Pending Dues</span>
          <span class="text-2xl font-bold font-mono text-amber-900">₹<?php echo number_format($metrics['total_pending'], 2); ?></span>
        </div>
      </div>

      <!-- Total Overdue -->
      <div class="p-5 rounded-2xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-error-container flex items-center justify-center text-error shrink-0">
          <span class="material-symbols-outlined text-[26px]">warning</span>
        </div>
        <div>
          <span class="text-label-md font-medium text-on-surface-variant block">Total Overdue Dues</span>
          <span class="text-2xl font-bold font-mono text-error">₹<?php echo number_format($metrics['total_overdue'], 2); ?></span>
        </div>
      </div>
    </div>

    <?php
      $today_date = date('Y-m-d');
      $month_start_date = date('Y-m-01');
      $ay_id = !empty($selected_year_id) ? (int)$selected_year_id : (int)($current_academic_year_id ?? 1);
    ?>
    <!-- 2. Collection Velocity & Student Metrics -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
      <!-- 1. Today's Collection -->
      <a href="<?php echo site_url('fees/payments?date_from=' . $today_date . '&date_to=' . $today_date . '&academic_year_id=' . $ay_id); ?>" 
         class="block p-4 rounded-xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 text-center hover:border-primary/50 hover:elevation-2 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-primary/40 transition-all cursor-pointer group"
         title="View Today's Fee Collections">
        <div class="text-xl font-bold font-mono text-primary group-hover:underline">₹<?php echo number_format($metrics['today_collection'], 2); ?></div>
        <div class="text-[12px] text-on-surface-variant mt-0.5 flex items-center justify-center gap-1">
          <span>Today's Collection</span>
          <span class="material-symbols-outlined text-[14px] text-primary opacity-0 group-hover:opacity-100 transition-opacity">arrow_forward</span>
        </div>
      </a>

      <!-- 2. This Month's Collection -->
      <a href="<?php echo site_url('fees/payments?date_from=' . $month_start_date . '&date_to=' . $today_date . '&academic_year_id=' . $ay_id); ?>" 
         class="block p-4 rounded-xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 text-center hover:border-secondary/50 hover:elevation-2 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-secondary/40 transition-all cursor-pointer group"
         title="View This Month's Fee Collections">
        <div class="text-xl font-bold font-mono text-secondary group-hover:underline">₹<?php echo number_format($metrics['monthly_collection'], 2); ?></div>
        <div class="text-[12px] text-on-surface-variant mt-0.5 flex items-center justify-center gap-1">
          <span>This Month's Collection</span>
          <span class="material-symbols-outlined text-[14px] text-secondary opacity-0 group-hover:opacity-100 transition-opacity">arrow_forward</span>
        </div>
      </a>

      <!-- 3. Students with Dues -->
      <a href="<?php echo site_url('fees/due_fees?academic_year_id=' . $ay_id); ?>" 
         class="block p-4 rounded-xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 text-center hover:border-amber-800/50 hover:elevation-2 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-amber-800/40 transition-all cursor-pointer group"
         title="View Students with Outstanding Dues">
        <div class="text-xl font-bold font-mono text-amber-800 group-hover:underline"><?php echo $metrics['students_with_dues']; ?> <?php echo ($metrics['students_with_dues'] == 1) ? 'Student' : 'Students'; ?></div>
        <div class="text-[12px] text-on-surface-variant mt-0.5 flex items-center justify-center gap-1">
          <span>Students with Dues</span>
          <span class="material-symbols-outlined text-[14px] text-amber-800 opacity-0 group-hover:opacity-100 transition-opacity">arrow_forward</span>
        </div>
      </a>

      <!-- 4. Fully Paid Students -->
      <a href="<?php echo site_url('fees/student_fees?payment_status=Fully Paid&academic_year_id=' . $ay_id); ?>" 
         class="block p-4 rounded-xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 text-center hover:border-emerald-700/50 hover:elevation-2 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-emerald-700/40 transition-all cursor-pointer group"
         title="View Fully Paid Students (Zero Balance)">
        <div class="text-xl font-bold font-mono text-emerald-700 group-hover:underline"><?php echo $metrics['fully_paid_students']; ?> <?php echo ($metrics['fully_paid_students'] == 1) ? 'Student' : 'Students'; ?></div>
        <div class="text-[12px] text-on-surface-variant mt-0.5 flex items-center justify-center gap-1">
          <span>Fully Paid Students</span>
          <span class="material-symbols-outlined text-[14px] text-emerald-700 opacity-0 group-hover:opacity-100 transition-opacity">arrow_forward</span>
        </div>
      </a>
    </div>

    <!-- 3. Collection Summary & Outstanding Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
      
      <!-- Collection Summary Card -->
      <div class="p-6 rounded-2xl bg-surface-container-lowest border border-outline-variant/50 elevation-1">
        <div class="flex items-center justify-between pb-3 border-b border-outline-variant/50 mb-4">
          <h3 class="font-headline-md text-title-lg font-bold text-on-surface flex items-center gap-2">
            <span class="material-symbols-outlined text-secondary text-[22px]">trending_up</span>Collection Summary
          </h3>
          <a href="<?php echo site_url('fees/reports?type=collection'); ?>" class="text-[13px] text-primary hover:underline font-medium">Full Report</a>
        </div>
        <div class="grid grid-cols-2 gap-4 text-body-md">
          <div class="p-3.5 rounded-xl bg-surface-container-low border border-outline-variant/40">
            <span class="text-[12px] text-on-surface-variant block">Daily Collection (Today)</span>
            <strong class="text-on-surface font-mono text-title-md">₹<?php echo number_format($collection_summary['daily'], 2); ?></strong>
          </div>
          <div class="p-3.5 rounded-xl bg-surface-container-low border border-outline-variant/40">
            <span class="text-[12px] text-on-surface-variant block">Weekly Collection (7 Days)</span>
            <strong class="text-on-surface font-mono text-title-md">₹<?php echo number_format($collection_summary['weekly'], 2); ?></strong>
          </div>
          <div class="p-3.5 rounded-xl bg-surface-container-low border border-outline-variant/40">
            <span class="text-[12px] text-on-surface-variant block">Monthly Collection</span>
            <strong class="text-secondary font-mono text-title-md">₹<?php echo number_format($collection_summary['monthly'], 2); ?></strong>
          </div>
          <div class="p-3.5 rounded-xl bg-surface-container-low border border-outline-variant/40">
            <span class="text-[12px] text-on-surface-variant block">Academic Year Collection</span>
            <strong class="text-primary font-mono text-title-md">₹<?php echo number_format($collection_summary['yearly'], 2); ?></strong>
          </div>
        </div>
      </div>

      <!-- Outstanding Fees Card -->
      <div class="p-6 rounded-2xl bg-surface-container-lowest border border-outline-variant/50 elevation-1">
        <div class="flex items-center justify-between pb-3 border-b border-outline-variant/50 mb-4">
          <h3 class="font-headline-md text-title-lg font-bold text-on-surface flex items-center gap-2">
            <span class="material-symbols-outlined text-amber-700 text-[22px]">error_outline</span>Outstanding Dues Summary
          </h3>
          <a href="<?php echo site_url('fees/due_fees'); ?>" class="text-[13px] text-primary hover:underline font-medium">View Due List</a>
        </div>
        <div class="grid grid-cols-2 gap-4 text-body-md">
          <div class="p-3.5 rounded-xl bg-surface-container-low border border-outline-variant/40">
            <span class="text-[12px] text-on-surface-variant block">Total Outstanding</span>
            <strong class="text-on-surface font-mono text-title-md">₹<?php echo number_format($outstanding_summary['total_outstanding'], 2); ?></strong>
          </div>
          <div class="p-3.5 rounded-xl bg-surface-container-low border border-outline-variant/40">
            <span class="text-[12px] text-on-surface-variant block">Overdue Amount</span>
            <strong class="text-error font-mono text-title-md">₹<?php echo number_format($outstanding_summary['overdue_amount'], 2); ?></strong>
          </div>
          <div class="p-3.5 rounded-xl bg-surface-container-low border border-outline-variant/40">
            <span class="text-[12px] text-on-surface-variant block">Upcoming Dues</span>
            <strong class="text-amber-800 font-mono text-title-md">₹<?php echo number_format($outstanding_summary['upcoming_dues'], 2); ?></strong>
          </div>
          <div class="p-3.5 rounded-xl bg-surface-container-low border border-outline-variant/40">
            <span class="text-[12px] text-on-surface-variant block">Students with Dues</span>
            <strong class="text-primary font-mono text-title-md"><?php echo $metrics['students_with_dues']; ?> Enrolled</strong>
          </div>
        </div>
      </div>
    </div>

    <!-- 4. Recent Payment Transactions -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden mb-6">
      <div class="p-4 border-b border-outline-variant/50 flex items-center justify-between">
        <h3 class="font-headline-md text-title-md font-bold text-on-surface flex items-center gap-2">
          <span class="material-symbols-outlined text-primary text-[20px]">receipt_long</span>Recent Fee Payments
        </h3>
        <a href="<?php echo site_url('fees/payments'); ?>" class="text-[13px] text-primary hover:underline font-medium">View All Payments</a>
      </div>

      <div class="table-scroll overflow-x-auto">
        <table class="w-full data-table zebra border-collapse text-body-md">
          <thead>
            <tr class="border-b border-outline-variant/60 bg-surface-container-low/50">
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Receipt #</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Student</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Class</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Fee Category</th>
              <th class="text-right px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Amount Paid</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Payment Mode</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Date</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/40">
            <?php if (empty($recent_payments)): ?>
              <tr><td colspan="8" class="px-4 py-8 text-center text-on-surface-variant">No fee payments recorded yet.</td></tr>
            <?php else: ?>
              <?php foreach ($recent_payments as $p): ?>
                <tr class="hover:bg-surface-container-low transition-colors">
                  <td class="px-4 py-3 font-mono font-bold text-primary whitespace-nowrap">
                    <a href="<?php echo site_url('fees/receipt/' . $p->payment_id); ?>" class="hover:underline">
                      <?php echo html_escape($p->receipt_no); ?>
                    </a>
                  </td>
                  <td class="px-4 py-3 whitespace-nowrap font-bold text-on-surface">
                    <?php echo html_escape($p->first_name . ' ' . $p->last_name); ?>
                    <span class="text-[11px] text-on-surface-variant block font-mono font-normal"><?php echo html_escape($p->admission_number); ?></span>
                  </td>
                  <td class="px-4 py-3 whitespace-nowrap text-on-surface-variant">
                    <?php echo html_escape($p->class_name . ' ' . $p->section_name); ?>
                  </td>
                  <td class="px-4 py-3 whitespace-nowrap font-medium text-on-surface">
                    <?php echo html_escape($p->category_name ?: 'General Fee'); ?>
                  </td>
                  <td class="px-4 py-3 text-right font-mono font-bold text-secondary whitespace-nowrap">
                    ₹<?php echo number_format($p->amount_paid, 2); ?>
                  </td>
                  <td class="px-4 py-3 text-center whitespace-nowrap">
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-surface-container-high text-on-surface">
                      <?php echo html_escape($p->payment_mode); ?>
                    </span>
                  </td>
                  <td class="px-4 py-3 text-center font-mono text-[12px] text-on-surface-variant whitespace-nowrap">
                    <?php echo date('d M Y', strtotime($p->payment_date)); ?>
                  </td>
                  <td class="px-4 py-3 text-center whitespace-nowrap">
                    <a href="<?php echo site_url('fees/receipt/' . $p->payment_id); ?>" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-surface-container-high text-primary hover:bg-primary-fixed transition-colors text-[12px] font-semibold">
                      <span class="material-symbols-outlined text-[16px]">receipt</span>Receipt
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
