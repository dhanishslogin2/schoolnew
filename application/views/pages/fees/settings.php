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
        <h2 class="font-headline-md text-headline-md text-on-surface">Finance & Billing Settings</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Configure currency, receipt numbering, partial payment rules, and reminder templates.</p>
      </div>
    </div>

    <?php echo form_open('fees/settings'); ?>
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        
        <!-- DIVISION 1: Currency & Payment Rules -->
        <div class="p-6 rounded-2xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 space-y-5">
          <div class="flex items-center gap-2.5 pb-3 border-b border-outline-variant/50">
            <span class="material-symbols-outlined text-primary text-[24px]">payments</span>
            <h3 class="font-headline-md text-title-lg font-bold text-on-surface">Currency & Payment Rules</h3>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Currency Symbol</label>
              <input type="text" name="currency_symbol" value="<?php echo html_escape($settings->currency_symbol); ?>" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-bold focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
            </div>
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Currency Code</label>
              <input type="text" name="currency_code" value="<?php echo html_escape($settings->currency_code); ?>" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-mono focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Grace Period (Days)</label>
              <input type="number" min="0" max="90" name="grace_period_days" value="<?php echo (int)$settings->grace_period_days; ?>" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-mono focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
            </div>
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Receipt Prefix</label>
              <input type="text" name="receipt_prefix" value="<?php echo html_escape($settings->receipt_prefix); ?>" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-mono focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
            </div>
          </div>

          <div class="space-y-3 pt-2">
            <label class="flex items-center justify-between p-3.5 rounded-xl bg-surface-container-low border border-outline-variant/40 cursor-pointer">
              <div>
                <div class="font-semibold text-on-surface text-body-md">Allow Partial Fee Payments</div>
                <div class="text-[12px] text-on-surface-variant">Permits collecting fees in installments while tracking outstanding balances.</div>
              </div>
              <input type="checkbox" name="allow_partial_payments" value="1" <?php echo ($settings->allow_partial_payments) ? 'checked' : ''; ?> class="w-5 h-5 rounded text-secondary focus:ring-secondary"/>
            </label>

            <label class="flex items-center justify-between p-3.5 rounded-xl bg-surface-container-low border border-outline-variant/40 cursor-pointer">
              <div>
                <div class="font-semibold text-on-surface text-body-md">Discount Approval Required</div>
                <div class="text-[12px] text-on-surface-variant">Require administrator/principal authorization before applying fee waivers.</div>
              </div>
              <input type="checkbox" name="discount_approval_required" value="1" <?php echo ($settings->discount_approval_required) ? 'checked' : ''; ?> class="w-5 h-5 rounded text-primary focus:ring-primary"/>
            </label>
          </div>
        </div>

        <!-- DIVISION 2: Receipt Branding & Signatures -->
        <div class="p-6 rounded-2xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 space-y-5">
          <div class="flex items-center gap-2.5 pb-3 border-b border-outline-variant/50">
            <span class="material-symbols-outlined text-primary text-[24px]">receipt_long</span>
            <h3 class="font-headline-md text-title-lg font-bold text-on-surface">Receipt Branding & Signatures</h3>
          </div>

          <div>
            <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Authorized Signature Title</label>
            <input type="text" name="authorized_signature_title" value="<?php echo html_escape($settings->authorized_signature_title); ?>" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
          </div>

          <div>
            <label class="block font-label-md text-label-md text-on-surface mb-1.5 font-medium">Receipt Footer Note</label>
            <textarea name="receipt_footer" rows="3" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"><?php echo html_escape($settings->receipt_footer); ?></textarea>
          </div>
        </div>
      </div>

      <!-- DIVISION 3: Reminder Templates -->
      <div class="p-6 rounded-2xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 space-y-5 mb-6">
        <div class="flex items-center gap-2.5 pb-3 border-b border-outline-variant/50">
          <span class="material-symbols-outlined text-primary text-[24px]">notifications</span>
          <h3 class="font-headline-md text-title-lg font-bold text-on-surface">Automated Reminder Templates</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Upcoming Due Template</label>
            <textarea name="reminder_template_upcoming" rows="4" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"><?php echo html_escape($settings->reminder_template_upcoming); ?></textarea>
          </div>
          <div>
            <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Overdue Notice Template</label>
            <textarea name="reminder_template_overdue" rows="4" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"><?php echo html_escape($settings->reminder_template_overdue); ?></textarea>
          </div>
          <div>
            <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Payment Received Template</label>
            <textarea name="reminder_template_payment" rows="4" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"><?php echo html_escape($settings->reminder_template_payment); ?></textarea>
          </div>
        </div>
      </div>

      <!-- Save Button -->
      <div class="flex items-center justify-end p-4 rounded-xl bg-surface-container-lowest border border-outline-variant/50 elevation-1 mb-6">
        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer">
          <span class="material-symbols-outlined text-[18px]">save</span>Save Finance Settings
        </button>
      </div>
    <?php echo form_close(); ?>

    <!-- Audit History Logs -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden mb-6">
      <div class="p-4 border-b border-outline-variant/50 flex items-center justify-between">
        <h3 class="font-headline-md text-title-md font-bold text-on-surface flex items-center gap-2">
          <span class="material-symbols-outlined text-primary text-[20px]">history</span>Financial Audit Trail
        </h3>
      </div>

      <div class="table-scroll overflow-x-auto">
        <table class="w-full data-table zebra border-collapse text-body-md">
          <thead>
            <tr class="border-b border-outline-variant/60 bg-surface-container-low/50">
              <th class="text-left px-4 py-2.5 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Timestamp</th>
              <th class="text-left px-4 py-2.5 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">User</th>
              <th class="text-left px-4 py-2.5 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Action</th>
              <th class="text-left px-4 py-2.5 text-label-md font-semibold text-on-surface-variant uppercase">Details</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/30">
            <?php if (empty($audit_logs)): ?>
              <tr><td colspan="4" class="px-4 py-4 text-center text-on-surface-variant">No audit logs recorded yet.</td></tr>
            <?php else: ?>
              <?php foreach ($audit_logs as $l): ?>
                <tr>
                  <td class="px-4 py-2.5 font-mono text-[12px] text-on-surface-variant whitespace-nowrap"><?php echo date('d M Y, h:i A', strtotime($l->created_at)); ?></td>
                  <td class="px-4 py-2.5 font-medium text-on-surface whitespace-nowrap"><?php echo html_escape($l->user_name ?: 'System'); ?></td>
                  <td class="px-4 py-2.5 whitespace-nowrap">
                    <span class="px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-surface-container-high text-primary"><?php echo html_escape($l->action); ?></span>
                  </td>
                  <td class="px-4 py-2.5 text-on-surface-variant"><?php echo html_escape($l->details); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
