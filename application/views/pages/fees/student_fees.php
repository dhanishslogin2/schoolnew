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
        <h2 class="font-headline-md text-headline-md text-on-surface">Student Fee Details</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Itemized student fee invoices, payment balances, concessions, and collection statuses.</p>
      </div>
      <div class="flex items-center gap-2 flex-wrap shrink-0">
        <a href="<?php echo site_url('fees/collection'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold hover:bg-on-secondary-fixed-variant transition-colors shadow-sm">
          <span class="material-symbols-outlined text-[18px]">add_card</span>Collect Payment
        </a>
      </div>
    </div>

    <!-- Filter Bar -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4 mb-6">
      <form method="get" action="<?php echo site_url('fees/student_fees'); ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <input type="hidden" name="academic_year_id" value="<?php echo html_escape($filters['academic_year_id'] ?? ''); ?>"/>

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
          <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Division</label>
          <select name="division_id" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
            <option value="">All Divisions</option>
            <?php foreach ($sections as $s): ?>
              <option value="<?php echo $s->section_id; ?>" <?php echo ($filters['section_id'] == $s->section_id) ? 'selected' : ''; ?>>
                <?php echo html_escape($s->class_name . ' ' . $s->section_name); ?>
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

        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Status</label>
          <select name="payment_status" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
            <option value="">All Statuses</option>
            <option value="Fully Paid" <?php echo ($filters['payment_status'] === 'Fully Paid') ? 'selected' : ''; ?>>Fully Paid (Zero Balance)</option>
            <option value="Paid" <?php echo ($filters['payment_status'] === 'Paid') ? 'selected' : ''; ?>>Paid</option>
            <option value="Pending" <?php echo ($filters['payment_status'] === 'Pending') ? 'selected' : ''; ?>>Pending</option>
            <option value="Partially Paid" <?php echo ($filters['payment_status'] === 'Partially Paid') ? 'selected' : ''; ?>>Partially Paid</option>
            <option value="Overdue" <?php echo ($filters['payment_status'] === 'Overdue') ? 'selected' : ''; ?>>Overdue</option>
          </select>
        </div>

        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Search</label>
          <div class="flex items-center gap-2">
            <input type="text" name="search" value="<?php echo html_escape($filters['search'] ?? ''); ?>" placeholder="Student or invoice..." class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
            <button type="submit" class="px-3.5 py-2 rounded-lg bg-primary text-on-primary text-label-md font-semibold hover:bg-primary/90 transition-colors shrink-0">Filter</button>
          </div>
        </div>
      </form>
    </div>

    <!-- Student Fees Table -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden mb-6">
      <div class="p-4 border-b border-outline-variant/50 flex items-center justify-between">
        <?php $unique_students_count = count(array_unique(array_column($fees, 'student_id'))); ?>
        <span class="text-body-md font-semibold text-on-surface">Assigned Fee Invoices (<?php echo count($fees); ?> invoices across <?php echo $unique_students_count; ?> students)</span>
      </div>

      <div class="table-scroll overflow-x-auto">
        <table class="w-full data-table zebra border-collapse text-body-md">
          <thead>
            <tr class="border-b border-outline-variant/60 bg-surface-container-low/50">
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Invoice #</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Student</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Class</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Fee Category</th>
              <th class="text-right px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Original</th>
              <th class="text-right px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Discount</th>
              <th class="text-right px-4 py-3 text-label-md font-semibold text-secondary uppercase whitespace-nowrap">Paid</th>
              <th class="text-right px-4 py-3 text-label-md font-semibold text-error uppercase whitespace-nowrap">Due</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Due Date</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface uppercase whitespace-nowrap">Status</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/40">
            <?php if (empty($fees)): ?>
              <tr><td colspan="11" class="px-4 py-8 text-center text-on-surface-variant">No student fee records found.</td></tr>
            <?php else: ?>
              <?php foreach ($fees as $f): ?>
                <?php
                  $badgeClass = 'bg-surface-container-high text-on-surface-variant';
                  if ($f->payment_status === 'Paid') $badgeClass = 'bg-secondary-container text-on-secondary-container';
                  elseif ($f->payment_status === 'Partially Paid') $badgeClass = 'bg-amber-100 text-amber-900 font-semibold';
                  elseif ($f->payment_status === 'Overdue') $badgeClass = 'bg-error-container text-on-error-container font-semibold';
                ?>
                <tr class="hover:bg-surface-container-low transition-colors">
                  <td class="px-4 py-3 font-mono font-bold text-primary whitespace-nowrap">
                    <?php echo html_escape($f->invoice_no ?: ('INV-' . $f->student_fee_id)); ?>
                  </td>
                  <td class="px-4 py-3 whitespace-nowrap font-bold text-on-surface">
                    <a href="<?php echo site_url('students/profile/' . $f->student_id); ?>" class="hover:text-primary hover:underline">
                      <?php echo html_escape($f->first_name . ' ' . $f->last_name); ?>
                    </a>
                    <span class="text-[11px] text-on-surface-variant block font-mono font-normal"><?php echo html_escape($f->admission_number); ?></span>
                  </td>
                  <td class="px-4 py-3 whitespace-nowrap text-on-surface-variant">
                    <?php echo html_escape($f->class_name . ' ' . $f->section_name); ?>
                  </td>
                  <td class="px-4 py-3 whitespace-nowrap font-medium text-on-surface">
                    <?php echo html_escape($f->category_name); ?>
                  </td>
                  <td class="px-4 py-3 text-right font-mono text-on-surface-variant whitespace-nowrap">
                    ₹<?php echo number_format($f->original_amount, 2); ?>
                  </td>
                  <td class="px-4 py-3 text-right font-mono text-on-surface-variant whitespace-nowrap">
                    <?php echo ($f->discount_amount > 0) ? '₹' . number_format($f->discount_amount, 2) : '—'; ?>
                  </td>
                  <td class="px-4 py-3 text-right font-mono font-bold text-secondary whitespace-nowrap">
                    ₹<?php echo number_format($f->paid_amount, 2); ?>
                  </td>
                  <td class="px-4 py-3 text-right font-mono font-bold <?php echo ($f->due_amount > 0) ? 'text-error' : 'text-on-surface-variant'; ?> whitespace-nowrap">
                    ₹<?php echo number_format($f->due_amount, 2); ?>
                  </td>
                  <td class="px-4 py-3 text-center font-mono text-[12px] whitespace-nowrap text-on-surface">
                    <?php echo date('d M Y', strtotime($f->due_date)); ?>
                  </td>
                  <td class="px-4 py-3 text-center whitespace-nowrap">
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold <?php echo $badgeClass; ?>">
                      <?php echo html_escape($f->payment_status); ?>
                    </span>
                  </td>
                  <td class="px-4 py-3 text-center whitespace-nowrap">
                    <?php if ($f->due_amount > 0): ?>
                      <a href="<?php echo site_url('fees/collection?student_id=' . $f->student_id); ?>" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-secondary text-on-secondary hover:bg-on-secondary-fixed-variant transition-colors text-[12px] font-semibold">
                        <span class="material-symbols-outlined text-[16px]">add_card</span>Pay
                      </a>
                    <?php else: ?>
                      <span class="text-secondary text-label-md font-semibold flex items-center justify-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">check</span>Cleared
                      </span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
