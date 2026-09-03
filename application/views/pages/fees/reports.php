<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h2 class="font-headline-md text-headline-md text-on-surface">Financial Reports</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Audit reports for fee collections, outstanding balances, payment methods, and student fee summaries.</p>
      </div>
      <div class="flex items-center gap-2 flex-wrap shrink-0">
        <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md hover:bg-surface-container-high transition-colors cursor-pointer">
          <span class="material-symbols-outlined text-[18px]">print</span>Print
        </button>
        <a href="<?php echo site_url('fees/reports?' . http_build_query(array_merge($_GET, array('export' => 'csv')))); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold hover:bg-on-secondary-fixed-variant transition-colors shadow-sm">
          <span class="material-symbols-outlined text-[18px]">download</span>Export CSV
        </a>
      </div>
    </div>

    <!-- Report Type Tabs -->
    <div class="flex gap-2 border-b border-outline-variant/60 mb-6 overflow-x-auto">
      <?php
        $curType = $report_type ?: 'collection';
        $report_tabs = [
          'collection' => 'Collection Report',
          'due'        => 'Outstanding / Due Report',
          'student'    => 'Student Fee Report',
        ];
      ?>
      <?php foreach ($report_tabs as $k => $label): ?>
        <?php $isActive = ($curType === $k); ?>
        <a href="<?php echo site_url('fees/reports?' . http_build_query(array_merge($_GET, array('type' => $k)))); ?>" class="px-4 py-2.5 text-body-md font-medium border-b-2 <?php echo $isActive ? 'border-secondary text-primary font-semibold' : 'border-transparent text-on-surface-variant hover:text-on-surface'; ?> transition-colors whitespace-nowrap">
          <?php echo $label; ?>
        </a>
      <?php endforeach; ?>
    </div>

    <!-- Filter Bar -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4 mb-6">
      <form method="get" action="<?php echo site_url('fees/reports'); ?>" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <input type="hidden" name="type" value="<?php echo html_escape($report_type); ?>"/>

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
          <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Date From</label>
          <input type="date" name="date_from" value="<?php echo html_escape($filters['date_from'] ?? ''); ?>" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
        </div>

        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Date To</label>
          <input type="date" name="date_to" value="<?php echo html_escape($filters['date_to'] ?? ''); ?>" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
        </div>
      </form>
    </div>

    <!-- Report Table -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden mb-6">
      <div class="p-4 border-b border-outline-variant/50 flex items-center justify-between">
        <span class="text-body-md font-semibold text-on-surface">Report Data (<?php echo count($report_data); ?> records)</span>
      </div>

      <div class="table-scroll overflow-x-auto">
        <table class="w-full data-table zebra border-collapse text-body-md">
          <thead>
            <tr class="border-b border-outline-variant/60 bg-surface-container-low/50">
              <?php if ($report_type === 'collection'): ?>
                <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Receipt #</th>
                <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Student</th>
                <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Class & Division</th>
                <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Category</th>
                <th class="text-right px-4 py-3 text-label-md font-semibold text-secondary uppercase whitespace-nowrap">Amount Paid</th>
                <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Payment Mode</th>
                <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Date</th>
              <?php elseif ($report_type === 'due'): ?>
                <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Student</th>
                <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Class & Division</th>
                <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Category</th>
                <th class="text-right px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Assigned Fee</th>
                <th class="text-right px-4 py-3 text-label-md font-semibold text-secondary uppercase whitespace-nowrap">Paid</th>
                <th class="text-right px-4 py-3 text-label-md font-semibold text-error uppercase whitespace-nowrap">Due Balance</th>
                <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Due Date</th>
                <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Days Overdue</th>
              <?php elseif ($report_type === 'student'): ?>
                <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Invoice #</th>
                <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Student</th>
                <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Class & Division</th>
                <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Category</th>
                <th class="text-right px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Original</th>
                <th class="text-right px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Discount</th>
                <th class="text-right px-4 py-3 text-label-md font-semibold text-secondary uppercase whitespace-nowrap">Paid</th>
                <th class="text-right px-4 py-3 text-label-md font-semibold text-error uppercase whitespace-nowrap">Due</th>
                <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface uppercase whitespace-nowrap">Status</th>
              <?php endif; ?>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/40">
            <?php if (empty($report_data)): ?>
              <tr><td colspan="8" class="px-4 py-8 text-center text-on-surface-variant">No report records found for the selected criteria.</td></tr>
            <?php else: ?>
              <?php foreach ($report_data as $row): ?>
                <tr class="hover:bg-surface-container-low transition-colors">
                  <?php if ($report_type === 'collection'): ?>
                    <td class="px-4 py-3 font-mono font-bold text-primary whitespace-nowrap"><?php echo html_escape($row->receipt_no); ?></td>
                    <td class="px-4 py-3 whitespace-nowrap font-bold text-on-surface"><?php echo html_escape($row->first_name . ' ' . $row->last_name); ?></td>
                    <td class="px-4 py-3 whitespace-nowrap text-on-surface-variant"><?php echo html_escape($row->class_name . ' ' . $row->section_name); ?></td>
                    <td class="px-4 py-3 whitespace-nowrap font-medium text-on-surface"><?php echo html_escape($row->category_name); ?></td>
                    <td class="px-4 py-3 text-right font-mono font-bold text-secondary whitespace-nowrap">₹<?php echo number_format($row->amount_paid, 2); ?></td>
                    <td class="px-4 py-3 text-center whitespace-nowrap"><?php echo html_escape($row->payment_mode); ?></td>
                    <td class="px-4 py-3 text-center font-mono text-[12px] whitespace-nowrap"><?php echo date('d M Y', strtotime($row->payment_date)); ?></td>
                  <?php elseif ($report_type === 'due'): ?>
                    <td class="px-4 py-3 whitespace-nowrap font-bold text-on-surface"><?php echo html_escape($row->first_name . ' ' . $row->last_name); ?></td>
                    <td class="px-4 py-3 whitespace-nowrap text-on-surface-variant"><?php echo html_escape($row->class_name . ' ' . $row->section_name); ?></td>
                    <td class="px-4 py-3 whitespace-nowrap font-medium text-on-surface"><?php echo html_escape($row->category_name); ?></td>
                    <td class="px-4 py-3 text-right font-mono text-on-surface-variant whitespace-nowrap">₹<?php echo number_format($row->final_amount, 2); ?></td>
                    <td class="px-4 py-3 text-right font-mono font-bold text-secondary whitespace-nowrap">₹<?php echo number_format($row->paid_amount, 2); ?></td>
                    <td class="px-4 py-3 text-right font-mono font-bold text-error whitespace-nowrap">₹<?php echo number_format($row->due_amount, 2); ?></td>
                    <td class="px-4 py-3 text-center font-mono text-[12px] whitespace-nowrap"><?php echo date('d M Y', strtotime($row->due_date)); ?></td>
                    <td class="px-4 py-3 text-center whitespace-nowrap font-mono text-[12px] <?php echo ($row->days_overdue > 0) ? 'text-error font-bold' : 'text-on-surface-variant'; ?>">
                      <?php echo ($row->days_overdue > 0) ? ($row->days_overdue . ' days') : '—'; ?>
                    </td>
                  <?php elseif ($report_type === 'student'): ?>
                    <td class="px-4 py-3 font-mono font-bold text-primary whitespace-nowrap"><?php echo html_escape($row->invoice_no); ?></td>
                    <td class="px-4 py-3 whitespace-nowrap font-bold text-on-surface"><?php echo html_escape($row->first_name . ' ' . $row->last_name); ?></td>
                    <td class="px-4 py-3 whitespace-nowrap text-on-surface-variant"><?php echo html_escape($row->class_name . ' ' . $row->section_name); ?></td>
                    <td class="px-4 py-3 whitespace-nowrap font-medium text-on-surface"><?php echo html_escape($row->category_name); ?></td>
                    <td class="px-4 py-3 text-right font-mono text-on-surface-variant whitespace-nowrap">₹<?php echo number_format($row->original_amount, 2); ?></td>
                    <td class="px-4 py-3 text-right font-mono text-on-surface-variant whitespace-nowrap"><?php echo ($row->discount_amount > 0) ? '₹' . number_format($row->discount_amount, 2) : '—'; ?></td>
                    <td class="px-4 py-3 text-right font-mono font-bold text-secondary whitespace-nowrap">₹<?php echo number_format($row->paid_amount, 2); ?></td>
                    <td class="px-4 py-3 text-right font-mono font-bold text-error whitespace-nowrap">₹<?php echo number_format($row->due_amount, 2); ?></td>
                    <td class="px-4 py-3 text-center whitespace-nowrap">
                      <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold <?php echo ($row->payment_status === 'Paid') ? 'bg-secondary-container text-on-secondary-container' : 'bg-error-container text-on-error-container'; ?>">
                        <?php echo html_escape($row->payment_status); ?>
                      </span>
                    </td>
                  <?php endif; ?>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
