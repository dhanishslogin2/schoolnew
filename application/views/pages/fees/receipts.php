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
        <h2 class="font-headline-md text-headline-md text-on-surface">Fee Receipts</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Browse, view, print, and download generated official fee payment receipts.</p>
      </div>
      <div class="flex items-center gap-2 flex-wrap shrink-0">
        <a href="<?php echo site_url('fees/collection'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold hover:bg-on-secondary-fixed-variant transition-colors shadow-sm">
          <span class="material-symbols-outlined text-[18px]">add_card</span>Collect Fee & Generate Receipt
        </a>
      </div>
    </div>

    <!-- Filter Bar -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4 mb-6">
      <form method="get" action="<?php echo site_url('fees/receipts'); ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
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
          <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Payment Mode</label>
          <select name="payment_mode" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
            <option value="">All Payment Modes</option>
            <option value="Cash" <?php echo ($filters['payment_mode'] === 'Cash') ? 'selected' : ''; ?>>Cash</option>
            <option value="UPI" <?php echo ($filters['payment_mode'] === 'UPI') ? 'selected' : ''; ?>>UPI</option>
            <option value="Card" <?php echo ($filters['payment_mode'] === 'Card') ? 'selected' : ''; ?>>Card</option>
            <option value="Bank Transfer" <?php echo ($filters['payment_mode'] === 'Bank Transfer') ? 'selected' : ''; ?>>Bank Transfer</option>
          </select>
        </div>

        <div class="lg:col-span-2">
          <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Search Receipt</label>
          <div class="flex items-center gap-2">
            <input type="text" name="search" value="<?php echo html_escape($filters['search'] ?? ''); ?>" placeholder="Receipt # (e.g. REC-2026-0001) or Student Name..." class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
            <button type="submit" class="px-3.5 py-2 rounded-lg bg-primary text-on-primary text-label-md font-semibold hover:bg-primary/90 transition-colors shrink-0">Search</button>
          </div>
        </div>
      </form>
    </div>

    <!-- Receipts Table -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden mb-6">
      <div class="p-4 border-b border-outline-variant/50 flex items-center justify-between">
        <span class="text-body-md font-semibold text-on-surface">Issued Receipts (<?php echo count($payments); ?>)</span>
      </div>

      <div class="table-scroll overflow-x-auto">
        <table class="w-full data-table zebra border-collapse text-body-md">
          <thead>
            <tr class="border-b border-outline-variant/60 bg-surface-container-low/50">
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Receipt #</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Student</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Class & Division</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Fee Category</th>
              <th class="text-right px-4 py-3 text-label-md font-semibold text-secondary uppercase whitespace-nowrap">Amount Paid</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Mode</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Issue Date</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/40">
            <?php if (empty($payments)): ?>
              <tr><td colspan="8" class="px-4 py-8 text-center text-on-surface-variant">No receipts found.</td></tr>
            <?php else: ?>
              <?php foreach ($payments as $p): ?>
                <tr class="hover:bg-surface-container-low transition-colors">
                  <td class="px-4 py-3 font-mono font-bold text-primary whitespace-nowrap">
                    <?php echo html_escape($p->receipt_no); ?>
                  </td>
                  <td class="px-4 py-3 whitespace-nowrap font-bold text-on-surface">
                    <?php echo html_escape($p->first_name . ' ' . $p->last_name); ?>
                    <span class="text-[11px] text-on-surface-variant block font-mono font-normal"><?php echo html_escape($p->admission_number); ?></span>
                  </td>
                  <td class="px-4 py-3 whitespace-nowrap text-on-surface-variant">
                    <?php echo html_escape($p->class_name . ' ' . $p->section_name); ?>
                  </td>
                  <td class="px-4 py-3 whitespace-nowrap font-medium text-on-surface">
                    <?php echo html_escape($p->category_name); ?>
                  </td>
                  <td class="px-4 py-3 text-right font-mono font-bold text-secondary whitespace-nowrap text-base">
                    ₹<?php echo number_format($p->amount_paid, 2); ?>
                  </td>
                  <td class="px-4 py-3 text-center whitespace-nowrap">
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-surface-container-high text-on-surface">
                      <?php echo html_escape($p->payment_mode); ?>
                    </span>
                  </td>
                  <td class="px-4 py-3 text-center font-mono text-[12px] text-on-surface whitespace-nowrap">
                    <?php echo date('d M Y', strtotime($p->payment_date)); ?>
                  </td>
                  <td class="px-4 py-3 text-center whitespace-nowrap">
                    <div class="flex items-center justify-center gap-1.5">
                      <a href="<?php echo site_url('fees/receipt/' . $p->payment_id); ?>" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-secondary text-on-secondary hover:bg-on-secondary-fixed-variant transition-colors text-[12px] font-semibold">
                        <span class="material-symbols-outlined text-[16px]">visibility</span>View / Print
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
