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
        <h2 class="font-headline-md text-headline-md text-on-surface">Fee Reminder History</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Audit log of dispatched fee reminder notifications, delivery statuses, and parent contacts.</p>
      </div>
      <div class="flex items-center gap-2 flex-wrap shrink-0">
        <a href="<?php echo site_url('fees/reminders'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold hover:bg-on-secondary-fixed-variant transition-colors shadow-sm">
          <span class="material-symbols-outlined text-[18px]">add_alert</span>Compose New Reminder
        </a>
      </div>
    </div>

    <!-- History Table -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden mb-6">
      <div class="p-4 border-b border-outline-variant/50 flex items-center justify-between">
        <span class="text-body-md font-semibold text-on-surface">Reminder Log Records (<?php echo count($reminders); ?>)</span>
      </div>

      <div class="table-scroll overflow-x-auto">
        <table class="w-full data-table zebra border-collapse text-body-md">
          <thead>
            <tr class="border-b border-outline-variant/60 bg-surface-container-low/50">
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Student</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Class & Division</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Parent Contact</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Reminder Type</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase">Message Preview</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Scheduled Date</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/40">
            <?php if (empty($reminders)): ?>
              <tr><td colspan="7" class="px-4 py-8 text-center text-on-surface-variant">No fee reminders logged yet.</td></tr>
            <?php else: ?>
              <?php foreach ($reminders as $rem): ?>
                <tr class="hover:bg-surface-container-low transition-colors">
                  <td class="px-4 py-3 whitespace-nowrap font-bold text-on-surface">
                    <?php echo html_escape($rem->first_name . ' ' . $rem->last_name); ?>
                    <span class="text-[11px] text-on-surface-variant block font-mono font-normal"><?php echo html_escape($rem->admission_number); ?></span>
                  </td>
                  <td class="px-4 py-3 whitespace-nowrap text-on-surface-variant">
                    <?php echo html_escape($rem->class_name . ' ' . $rem->section_name); ?>
                  </td>
                  <td class="px-4 py-3 whitespace-nowrap">
                    <span class="font-medium text-on-surface block"><?php echo html_escape($rem->parent_name ?: 'Parent'); ?></span>
                    <span class="text-[12px] text-on-surface-variant font-mono"><?php echo html_escape($rem->parent_phone ?: $rem->parent_email); ?></span>
                  </td>
                  <td class="px-4 py-3 whitespace-nowrap">
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold <?php echo ($rem->reminder_type === 'Overdue') ? 'bg-error-container text-on-error-container' : 'bg-primary-fixed text-primary'; ?>">
                      <?php echo html_escape($rem->reminder_type); ?>
                    </span>
                  </td>
                  <td class="px-4 py-3 text-on-surface text-[13px] max-w-xs truncate" title="<?php echo html_escape($rem->message); ?>">
                    "<?php echo html_escape($rem->message); ?>"
                  </td>
                  <td class="px-4 py-3 text-center font-mono text-[12px] whitespace-nowrap">
                    <?php echo date('d M Y', strtotime($rem->scheduled_date)); ?>
                  </td>
                  <td class="px-4 py-3 text-center whitespace-nowrap">
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold <?php echo ($rem->status === 'Sent') ? 'bg-secondary-container text-on-secondary-container' : 'bg-amber-100 text-amber-900'; ?>">
                      <?php echo html_escape($rem->status); ?>
                    </span>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
