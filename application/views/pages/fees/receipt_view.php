<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

    <!-- Action Toolbar (Hidden on Print) -->
    <div class="print:hidden flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div class="flex items-center gap-2">
        <a href="<?php echo site_url('fees/receipts'); ?>" class="p-1 rounded-lg hover:bg-surface-container-high text-on-surface-variant transition-colors">
          <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
        <h2 class="font-headline-md text-headline-md text-on-surface">Fee Payment Receipt</h2>
      </div>
      <div class="flex items-center gap-2 flex-wrap shrink-0">
        <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer">
          <span class="material-symbols-outlined text-[18px]">print</span>Print Receipt
        </button>
      </div>
    </div>

    <!-- OFFICIAL RECEIPT SHEET -->
    <div class="receipt-sheet bg-surface-container-lowest border border-outline-variant/60 rounded-2xl p-8 max-w-2xl mx-auto elevation-2 print:border-none print:shadow-none print:p-0 print:m-0 space-y-6">
      
      <!-- 1. School Header -->
      <div class="text-center pb-5 border-b-2 border-outline-variant/80">
        <div class="flex items-center justify-center gap-2.5 mb-1">
          <span class="material-symbols-outlined text-primary text-[36px]">school</span>
          <h1 class="text-2xl font-extrabold uppercase tracking-wide text-on-surface">Model School</h1>
        </div>
        <p class="text-body-md text-on-surface-variant font-medium">Affiliated to CBSE • Institutional Campus, City Area • Phone: +91 98765 43210</p>
        <div class="mt-2 inline-block px-3.5 py-0.5 rounded-full bg-primary-fixed text-primary font-bold text-[12px] uppercase tracking-wider">
          Official Fee Payment Receipt
        </div>
      </div>

      <!-- 2. Receipt & Student Meta -->
      <div class="p-4 rounded-xl bg-surface-container-low border border-outline-variant/50 grid grid-cols-2 gap-4 text-body-md">
        <div>
          <span class="text-[11px] text-on-surface-variant uppercase font-semibold block">Receipt Number</span>
          <strong class="text-primary font-mono text-title-md"><?php echo html_escape($receipt->receipt_no); ?></strong>
          <span class="text-[12px] text-on-surface-variant block">Date: <strong><?php echo date('d M Y', strtotime($receipt->payment_date)); ?></strong></span>
        </div>
        <div>
          <span class="text-[11px] text-on-surface-variant uppercase font-semibold block">Student Details</span>
          <strong class="text-on-surface text-[15px]"><?php echo html_escape($receipt->first_name . ' ' . $receipt->last_name); ?></strong>
          <span class="text-[12px] text-on-surface-variant block font-mono">Adm #: <?php echo html_escape($receipt->admission_number); ?> | Class: <?php echo html_escape($receipt->class_name . ' ' . $receipt->section_name); ?></span>
        </div>
        <div>
          <span class="text-[11px] text-on-surface-variant uppercase font-semibold block">Parent / Guardian</span>
          <span class="text-on-surface font-medium"><?php echo html_escape($receipt->guardian_name ?: 'Parent'); ?></span>
          <span class="text-[12px] text-on-surface-variant block font-mono"><?php echo html_escape($receipt->guardian_phone ?: ''); ?></span>
        </div>
        <div>
          <span class="text-[11px] text-on-surface-variant uppercase font-semibold block">Invoice / Fee Reference</span>
          <span class="font-mono text-on-surface font-semibold"><?php echo html_escape($receipt->invoice_no); ?></span>
          <span class="text-[12px] text-on-surface-variant block">Due Date: <?php echo date('d M Y', strtotime($receipt->due_date)); ?></span>
        </div>
      </div>

      <!-- 3. Payment Line Items Table -->
      <div class="rounded-xl border border-outline-variant/60 overflow-hidden">
        <table class="w-full data-table border-collapse text-body-md">
          <thead>
            <tr class="border-b border-outline-variant/60 bg-surface-container-low">
              <th class="text-left px-4 py-2.5 text-label-md font-semibold text-on-surface-variant uppercase">Particulars</th>
              <th class="text-right px-4 py-2.5 text-label-md font-semibold text-on-surface-variant uppercase">Original</th>
              <th class="text-right px-4 py-2.5 text-label-md font-semibold text-on-surface-variant uppercase">Discount</th>
              <th class="text-right px-4 py-2.5 text-label-md font-semibold text-on-surface uppercase">Amount Paid</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/40">
            <tr>
              <td class="px-4 py-3 font-bold text-on-surface">
                <?php echo html_escape($receipt->category_name); ?>
                <span class="text-[11px] text-on-surface-variant block font-normal"><?php echo html_escape($receipt->remarks ?: 'Term Fee installment payment'); ?></span>
              </td>
              <td class="px-4 py-3 text-right font-mono text-on-surface-variant">₹<?php echo number_format($receipt->original_amount, 2); ?></td>
              <td class="px-4 py-3 text-right font-mono text-on-surface-variant">
                <?php echo ($receipt->discount_amount > 0) ? '₹' . number_format($receipt->discount_amount, 2) : '—'; ?>
              </td>
              <td class="px-4 py-3 text-right font-mono font-bold text-secondary text-base">₹<?php echo number_format($receipt->amount_paid, 2); ?></td>
            </tr>
          </tbody>
          <tfoot>
            <tr class="border-t-2 border-outline-variant bg-surface-container-low font-bold">
              <td colspan="3" class="px-4 py-3 text-right uppercase text-on-surface">TOTAL PAID NOW:</td>
              <td class="px-4 py-3 text-right font-mono text-secondary text-lg">₹<?php echo number_format($receipt->amount_paid, 2); ?></td>
            </tr>
          </tfoot>
        </table>
      </div>

      <!-- 4. Outstanding Balance & Payment Meta Grid -->
      <div class="grid grid-cols-2 gap-4 text-body-md">
        <div class="p-3.5 rounded-xl bg-surface-container-low border border-outline-variant/50 space-y-1.5">
          <div class="flex justify-between"><span class="text-on-surface-variant">Payment Mode:</span><strong class="text-on-surface"><?php echo html_escape($receipt->payment_mode); ?></strong></div>
          <?php if ($receipt->transaction_reference): ?>
            <div class="flex justify-between"><span class="text-on-surface-variant">Txn / Ref #:</span><span class="font-mono text-on-surface"><?php echo html_escape($receipt->transaction_reference); ?></span></div>
          <?php endif; ?>
          <div class="flex justify-between"><span class="text-on-surface-variant">Collected By:</span><span class="text-on-surface"><?php echo html_escape($receipt->collected_by_name ?: 'Accounts Desk'); ?></span></div>
        </div>

        <div class="p-3.5 rounded-xl bg-surface-container-low border border-outline-variant/50 space-y-1.5">
          <div class="flex justify-between"><span class="text-on-surface-variant">Cumulative Paid:</span><span class="font-mono text-secondary font-bold">₹<?php echo number_format($receipt->total_paid_to_date, 2); ?></span></div>
          <div class="flex justify-between"><span class="text-on-surface-variant">Remaining Due:</span><strong class="font-mono <?php echo ($receipt->remaining_due > 0) ? 'text-error' : 'text-secondary'; ?> font-bold">₹<?php echo number_format($receipt->remaining_due, 2); ?></strong></div>
          <div class="flex justify-between"><span class="text-on-surface-variant">Status:</span><span class="font-semibold text-on-surface"><?php echo ($receipt->remaining_due == 0) ? 'Fully Cleared' : 'Partially Paid'; ?></span></div>
        </div>
      </div>

      <!-- 5. Footer & Signature -->
      <div class="pt-4 space-y-4">
        <p class="text-[12px] text-on-surface-variant italic text-center">
          "<?php echo html_escape($settings->receipt_footer ?: 'Thank you for your fee payment. This is a computer generated official fee receipt.'); ?>"
        </p>

        <div class="grid grid-cols-2 gap-8 pt-8 text-center text-body-md">
          <div>
            <div class="border-t border-outline-variant/80 pt-2 font-medium text-on-surface">Payer's Signature</div>
            <span class="text-[11px] text-on-surface-variant">Parent / Guardian</span>
          </div>
          <div>
            <div class="border-t border-outline-variant/80 pt-2 font-medium text-on-surface">
              <?php echo html_escape($settings->authorized_signature_title ?: 'Accounts Officer'); ?>
            </div>
            <span class="text-[11px] text-on-surface-variant">Cashier / Authorized Signature</span>
          </div>
        </div>
      </div>
    </div>
