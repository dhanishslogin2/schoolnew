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
        <h2 class="font-headline-md text-headline-md text-on-surface">Fee Collection Counter</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Search student, review outstanding dues, apply partial/full payments, and generate instant receipts.</p>
      </div>
      <div class="flex items-center gap-2 flex-wrap shrink-0">
        <a href="<?php echo site_url('fees/payments'); ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md hover:bg-surface-container-high transition-colors">
          <span class="material-symbols-outlined text-[18px]">receipt_long</span>Payment History
        </a>
      </div>
    </div>

    <!-- Student Search Box -->
    <div class="elevation-1 rounded-2xl bg-surface-container-lowest border border-outline-variant/50 p-5 mb-6">
      <form method="get" action="<?php echo site_url('fees/collection'); ?>" class="flex flex-col sm:flex-row items-center gap-3">
        <div class="relative flex-1 w-full">
          <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
          <input data-testid="fee-search-input" type="text" name="search" value="<?php echo html_escape($search ?? ''); ?>" placeholder="Search student by Name, Admission Number, or Roll Number..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-outline-variant bg-surface-container-low text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
        </div>
        <button data-testid="fee-search-btn" type="submit" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-primary text-on-primary text-label-md font-semibold hover:bg-primary/90 transition-colors shrink-0 flex items-center justify-center gap-1.5 cursor-pointer">
          <span class="material-symbols-outlined text-[18px]">search</span>Find Student
        </button>
      </form>
    </div>

    <?php if ($student_info): ?>
      <!-- Student Overview Card -->
      <div data-testid="fee-student-card" class="p-6 rounded-2xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-outline-variant/50 mb-4">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-primary text-on-primary font-bold text-lg flex items-center justify-center shrink-0">
              <?php echo strtoupper(substr($student_info->first_name, 0, 1)); ?>
            </div>
            <div>
              <h3 class="font-headline-md text-title-lg font-bold text-on-surface">
                <?php echo html_escape($student_info->first_name . ' ' . $student_info->last_name); ?>
              </h3>
              <span class="text-body-md text-on-surface-variant font-mono">Admission #: <?php echo html_escape($student_info->admission_number); ?></span>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-full text-label-md font-semibold bg-secondary-container text-on-secondary-container">
              Active Student
            </span>
            <a href="<?php echo site_url('students/profile/' . $student_info->student_id); ?>" class="text-[13px] text-primary hover:underline font-medium">View Profile</a>
          </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-body-md">
          <div class="p-3 bg-surface-container-low rounded-xl">
            <span class="text-on-surface-variant text-[12px] block">Class & Division</span>
            <span class="font-semibold text-on-surface"><?php echo html_escape(($student_info->class_name ?? '') . ' - ' . ($student_info->section_name ?? '')); ?></span>
          </div>
          <div class="p-3 bg-surface-container-low rounded-xl">
            <span class="text-on-surface-variant text-[12px] block">Roll Number</span>
            <span class="font-semibold text-on-surface"><?php echo html_escape($student_info->roll_number ?? '—'); ?></span>
          </div>
          <div class="p-3 bg-surface-container-low rounded-xl">
            <span class="text-on-surface-variant text-[12px] block">Guardian Name</span>
            <span class="font-semibold text-on-surface"><?php echo html_escape($student_info->guardian_name ?? '—'); ?></span>
          </div>
          <div class="p-3 bg-surface-container-low rounded-xl">
            <span class="text-on-surface-variant text-[12px] block">Contact Phone</span>
            <span class="font-semibold text-on-surface"><?php echo html_escape($student_info->guardian_phone ?? '—'); ?></span>
          </div>
        </div>
      </div>

      <!-- Fees List & Collection Actions -->
      <div class="elevation-1 rounded-2xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden mb-6">
        <div class="flex items-center justify-between px-6 py-4 border-b border-outline-variant/50">
          <h3 class="font-headline-md text-title-md font-semibold text-on-surface">Assigned Fee Schedule & Invoices</h3>
          <span class="text-body-md text-on-surface-variant"><?php echo count($student_fees); ?> Fee Items</span>
        </div>

        <div class="table-scroll overflow-x-auto">
          <table class="w-full data-table border-collapse text-body-md">
            <thead>
              <tr class="border-b border-outline-variant/60 bg-surface-container-low/50">
                <th class="px-4 py-3 text-left font-semibold text-on-surface-variant uppercase text-[11px] tracking-wider">Invoice #</th>
                <th class="px-4 py-3 text-left font-semibold text-on-surface-variant uppercase text-[11px] tracking-wider">Fee Category</th>
                <th class="px-4 py-3 text-right font-semibold text-on-surface-variant uppercase text-[11px] tracking-wider">Original Fee</th>
                <th class="px-4 py-3 text-right font-semibold text-on-surface-variant uppercase text-[11px] tracking-wider">Discount</th>
                <th class="px-4 py-3 text-right font-semibold text-on-surface-variant uppercase text-[11px] tracking-wider">Paid</th>
                <th class="px-4 py-3 text-right font-semibold text-on-surface-variant uppercase text-[11px] tracking-wider">Balance Due</th>
                <th class="px-4 py-3 text-center font-semibold text-on-surface-variant uppercase text-[11px] tracking-wider">Due Date</th>
                <th class="px-4 py-3 text-center font-semibold text-on-surface-variant uppercase text-[11px] tracking-wider">Status</th>
                <th class="px-4 py-3 text-center font-semibold text-on-surface-variant uppercase text-[11px] tracking-wider">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/40">
              <?php if (empty($student_fees)): ?>
                <tr>
                  <td colspan="9" class="px-4 py-8 text-center text-on-surface-variant">No fee records found for this student.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($student_fees as $sf): ?>
                  <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-4 py-3 font-mono font-bold text-primary whitespace-nowrap"><?php echo html_escape($sf->invoice_no); ?></td>
                    <td class="px-4 py-3 whitespace-nowrap font-bold text-on-surface"><?php echo html_escape($sf->category_name); ?></td>
                    <td class="px-4 py-3 text-right font-mono whitespace-nowrap">₹<?php echo number_format($sf->original_amount, 2); ?></td>
                    <td class="px-4 py-3 text-right font-mono text-on-surface-variant whitespace-nowrap"><?php echo ($sf->discount_amount > 0) ? '₹' . number_format($sf->discount_amount, 2) : '—'; ?></td>
                    <td class="px-4 py-3 text-right font-mono font-bold text-secondary whitespace-nowrap">₹<?php echo number_format($sf->paid_amount, 2); ?></td>
                    <td class="px-4 py-3 text-right font-mono font-bold text-error whitespace-nowrap text-base">₹<?php echo number_format($sf->due_amount, 2); ?></td>
                    <td class="px-4 py-3 text-center font-mono text-[12px] whitespace-nowrap"><?php echo date('d M Y', strtotime($sf->due_date)); ?></td>
                    <td class="px-4 py-3 text-center whitespace-nowrap">
                      <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold <?php echo ($sf->payment_status === 'Paid') ? 'bg-secondary-container text-on-secondary-container' : (($sf->payment_status === 'Partially Paid') ? 'bg-amber-100 text-amber-900' : 'bg-error-container text-on-error-container'); ?>">
                        <?php echo html_escape($sf->payment_status); ?>
                      </span>
                    </td>
                    <td class="px-4 py-3 text-center whitespace-nowrap">
                      <?php if ($sf->due_amount > 0): ?>
                        <button data-testid="fee-pay-now-btn" type="button" onclick='openPaymentModal(<?php echo json_encode($sf); ?>)' class="inline-flex items-center gap-1 px-4 py-1.5 rounded-lg bg-secondary text-on-secondary hover:bg-on-secondary-fixed-variant transition-colors text-[12px] font-semibold cursor-pointer shadow-sm">
                          <span class="material-symbols-outlined text-[16px]">payments</span>Pay Now
                        </button>
                      <?php else: ?>
                        <span class="text-secondary text-label-md font-semibold flex items-center justify-center gap-1">
                          <span class="material-symbols-outlined text-[16px]">check</span>Paid
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
    <?php elseif ($search): ?>
      <div class="p-8 rounded-2xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 text-center">
        <span class="material-symbols-outlined text-[48px] text-on-surface-variant/50 mb-2">person_search</span>
        <h4 class="text-title-md font-bold text-on-surface">No Student Found</h4>
        <p class="text-body-md text-on-surface-variant mt-1">No matching student record found for "<?php echo html_escape($search); ?>". Please check the admission number or name.</p>
      </div>
    <?php endif; ?>

    <!-- PAYMENT COLLECTION & CONFIRMATION MODAL -->
    <div id="payment-modal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm hidden items-center justify-center p-4">
      <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant max-w-lg w-full p-6 elevation-3 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-outline-variant/50">
          <h3 class="font-headline-md text-title-lg text-on-surface font-semibold flex items-center gap-2">
            <span class="material-symbols-outlined text-secondary text-[24px]">payments</span>Collect Fee Payment
          </h3>
          <button onclick="closePaymentModal()" class="p-1 rounded-lg hover:bg-surface-container-high text-on-surface-variant cursor-pointer">
            <span class="material-symbols-outlined text-[20px]">close</span>
          </button>
        </div>

        <?php echo form_open('fees/collection', array('id' => 'payment-form', 'class' => 'space-y-4')); ?>
          <input type="hidden" name="student_fee_id" id="pay-student-fee-id" value="0"/>

          <!-- Summary Box -->
          <div class="p-4 rounded-xl bg-surface-container-low border border-outline-variant/50 space-y-2 text-body-md">
            <div class="flex justify-between">
              <span class="text-on-surface-variant">Fee Item:</span>
              <strong id="pay-category-name" class="text-on-surface">Tuition Fee</strong>
            </div>
            <div class="flex justify-between">
              <span class="text-on-surface-variant">Total Payable:</span>
              <span id="pay-final-amount" class="font-mono text-on-surface">₹0.00</span>
            </div>
            <div class="flex justify-between">
              <span class="text-on-surface-variant">Already Paid:</span>
              <span id="pay-already-paid" class="font-mono text-secondary">₹0.00</span>
            </div>
            <div class="flex justify-between border-t border-outline-variant/40 pt-2">
              <span class="font-semibold text-on-surface">Outstanding Due:</span>
              <strong id="pay-due-amount" class="font-mono text-error text-title-md">₹0.00</strong>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Amount to Pay (₹) *</label>
              <input data-testid="payment-amount-input" type="number" step="0.5" min="1" name="amount_to_pay" id="pay-amount-input" required class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-mono font-bold text-secondary focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
            </div>
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Payment Method *</label>
              <select data-testid="payment-mode-select" name="payment_mode" required class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <option value="Cash" selected>Cash</option>
                <option value="UPI">UPI / QR Code</option>
                <option value="Card">Credit / Debit Card</option>
                <option value="Bank Transfer">Bank Transfer / NEFT</option>
                <option value="Cheque">Cheque</option>
                <option value="Other">Other</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Transaction / Ref #</label>
              <input data-testid="payment-reference-input" type="text" name="transaction_reference" placeholder="e.g. UPI/2026/89472" class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-mono focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
            </div>
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Payment Date *</label>
              <input data-testid="payment-date-input" type="date" name="payment_date" value="<?php echo date('Y-m-d'); ?>" required class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
            </div>
          </div>

          <div>
            <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Remarks / Counter Note</label>
            <input data-testid="payment-remarks-input" type="text" name="remarks" placeholder="Optional notes for this payment..." class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
          </div>

          <div class="flex items-center justify-end gap-2 pt-4 border-t border-outline-variant/50">
            <button type="button" onclick="closePaymentModal()" class="px-4 py-2 rounded-lg bg-surface-container-high text-on-surface text-label-md font-medium hover:bg-surface-container-highest cursor-pointer">
              Cancel
            </button>
            <button data-testid="payment-submit-btn" type="submit" class="px-6 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer">
              Confirm & Collect Payment
            </button>
          </div>
        <?php echo form_close(); ?>
      </div>
    </div>

    <script>
      function openPaymentModal(item) {
        document.getElementById('pay-student-fee-id').value = item.student_fee_id;
        document.getElementById('pay-category-name').textContent = item.category_name;
        document.getElementById('pay-final-amount').textContent = '₹' + parseFloat(item.final_amount).toFixed(2);
        document.getElementById('pay-already-paid').textContent = '₹' + parseFloat(item.paid_amount).toFixed(2);
        document.getElementById('pay-due-amount').textContent = '₹' + parseFloat(item.due_amount).toFixed(2);
        document.getElementById('pay-amount-input').value = parseFloat(item.due_amount).toFixed(2);
        document.getElementById('pay-amount-input').max = parseFloat(item.due_amount).toFixed(2);

        var modal = document.getElementById('payment-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
      }

      function closePaymentModal() {
        var modal = document.getElementById('payment-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
      }
    </script>
