<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h2 class="font-headline-md text-headline-md text-on-surface">Teacher Workload Management</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Assign academic subjects, classes, sections, and weekly period schedules to teaching faculty.</p>
      </div>
      <div class="flex items-center gap-2 shrink-0">
        <button onclick="document.getElementById('modal-add-workload').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer">
          <span class="material-symbols-outlined text-[18px]">add_circle</span>Assign Workload
        </button>
      </div>
    </div>

    <!-- Filters Bar -->
    <div class="flex flex-col md:flex-row gap-3 mb-4 flex-wrap">
      <select onchange="applyFilter('staff_id', this.value)" class="px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md text-on-surface-variant">
        <option value="">All Teachers</option>
        <?php foreach ($teachers as $t): ?>
          <option value="<?php echo $t->staff_id; ?>" <?php echo ($this->input->get('staff_id') == $t->staff_id) ? 'selected' : ''; ?>><?php echo html_escape($t->full_name . ' (' . $t->employee_code . ')'); ?></option>
        <?php endforeach; ?>
      </select>
      <select onchange="applyFilter('class_id', this.value)" class="px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md text-on-surface-variant">
        <option value="">All Classes</option>
        <?php foreach ($classes as $cls): ?>
          <option value="<?php echo $cls->class_id; ?>" <?php echo ($this->input->get('class_id') == $cls->class_id) ? 'selected' : ''; ?>><?php echo html_escape($cls->class_name); ?></option>
        <?php endforeach; ?>
      </select>
      <select onchange="applyFilter('subject_id', this.value)" class="px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md text-on-surface-variant">
        <option value="">All Subjects</option>
        <?php foreach ($subjects as $sub): ?>
          <option value="<?php echo $sub->subject_id; ?>" <?php echo ($this->input->get('subject_id') == $sub->subject_id) ? 'selected' : ''; ?>><?php echo html_escape($sub->subject_name); ?></option>
        <?php endforeach; ?>
      </select>
      <a href="<?php echo site_url('staff/workload'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined text-[18px]">restart_alt</span>Reset</a>
    </div>

    <!-- Workloads Table -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
      <div class="table-scroll overflow-x-auto">
        <table class="w-full data-table zebra border-collapse">
          <thead>
            <tr class="border-b border-outline-variant/60">
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Teacher Name</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Employee Code</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Subject</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Class & Division</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Periods/Week</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Working Days</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Remarks</th>
              <th class="text-right px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/30 text-body-md">
            <?php if (empty($workloads)): ?>
              <tr><td colspan="8" class="px-4 py-8 text-center text-on-surface-variant">No teacher workload allocations found.</td></tr>
            <?php endif; ?>
            <?php foreach ($workloads as $wl): ?>
              <tr class='hover:bg-surface-container-low transition-colors'>
                <td class="px-4 py-3 font-semibold text-on-surface whitespace-nowrap">
                  <a href="<?php echo site_url('staff/profile/' . $wl->staff_id); ?>" class="hover:underline text-primary"><?php echo html_escape($wl->full_name); ?></a>
                </td>
                <td class="px-4 py-3 font-mono text-on-surface-variant whitespace-nowrap"><?php echo html_escape($wl->employee_code); ?></td>
                <td class="px-4 py-3 text-on-surface font-medium whitespace-nowrap"><?php echo html_escape($wl->subject_name); ?></td>
                <td class="px-4 py-3 text-on-surface whitespace-nowrap"><?php echo html_escape($wl->class_name . ' ' . $wl->section_name); ?></td>
                <td class="px-4 py-3 font-mono font-bold text-primary whitespace-nowrap"><?php echo $wl->periods; ?> periods</td>
                <td class="px-4 py-3 text-on-surface-variant whitespace-nowrap text-[13px]"><?php echo html_escape($wl->working_days); ?></td>
                <td class="px-4 py-3 text-on-surface-variant text-[13px]"><?php echo html_escape($wl->remarks ?: '—'); ?></td>
                <td class="px-4 py-3 text-right whitespace-nowrap">
                  <a href="<?php echo site_url('staff/delete_workload/' . $wl->workload_id); ?>" onclick="return confirm('Remove this workload allocation?')" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-error-container/20 hover:text-error transition-colors inline-flex"><span class="material-symbols-outlined text-[18px]">delete</span></a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="flex items-center justify-between px-4 py-3 border-t border-outline-variant/50 text-body-md font-body-md text-on-surface-variant">
        <span>Total <?php echo count($workloads); ?> workload assignment(s)</span>
      </div>
    </div>

    <!-- Modal: Assign Workload -->
    <div id="modal-add-workload" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 hidden">
      <div class="elevation-3 rounded-2xl bg-surface-container-lowest border border-outline-variant w-full max-w-lg">
        <div class="flex items-center justify-between px-6 py-4 border-b border-outline-variant">
          <h3 class="font-headline-md text-headline-md text-on-surface">Assign Teacher Workload</h3>
          <button onclick="document.getElementById('modal-add-workload').classList.add('hidden')" class="p-1 rounded-lg text-on-surface-variant hover:bg-surface-container-high"><span class="material-symbols-outlined">close</span></button>
        </div>
        <?php echo form_open('staff/workload', array('class' => 'p-6 space-y-4')); ?>
          <div>
            <label class="block text-label-md mb-1">Select Teacher *</label>
            <select name="staff_id" required class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest">
              <?php foreach ($teachers as $t): ?>
                <option value="<?php echo $t->staff_id; ?>"><?php echo html_escape($t->full_name . ' (' . $t->employee_code . ' - ' . $t->department_name . ')'); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-label-md mb-1">Class *</label>
              <select name="class_id" required class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest">
                <?php foreach ($classes as $cls): ?>
                  <option value="<?php echo $cls->class_id; ?>"><?php echo html_escape($cls->class_name); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label class="block text-label-md mb-1">Division *</label>
              <select name="division_id" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest">
                <?php foreach ($sections as $sec): ?>
                  <option value="<?php echo $sec->section_id; ?>"><?php echo html_escape($sec->class_name . ' ' . $sec->section_name); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div>
            <label class="block text-label-md mb-1">Subject *</label>
            <select name="subject_id" required class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest">
              <?php foreach ($subjects as $sub): ?>
                <option value="<?php echo $sub->subject_id; ?>"><?php echo html_escape($sub->subject_name); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-label-md mb-1">Periods / Week *</label>
              <input type="number" name="periods" min="1" max="30" value="5" required class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
            </div>
            <div>
              <label class="block text-label-md mb-1">Working Days</label>
              <input type="text" name="working_days" value="Mon,Tue,Wed,Thu,Fri" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-[13px]"/>
            </div>
          </div>
          <div>
            <label class="block text-label-md mb-1">Remarks</label>
            <input type="text" name="remarks" placeholder="Optional notes (e.g. Core theory)" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
          </div>
          <div class="flex justify-end gap-2 pt-4 border-t border-outline-variant">
            <button type="button" onclick="document.getElementById('modal-add-workload').classList.add('hidden')" class="px-4 py-2 rounded-lg border border-outline-variant text-on-surface-variant">Cancel</button>
            <button type="submit" class="px-4 py-2 rounded-lg bg-secondary text-on-secondary text-label-md hover:bg-on-secondary-fixed-variant cursor-pointer">Save Assignment</button>
          </div>
        <?php echo form_close(); ?>
      </div>
    </div>

    <script>
      function applyFilter(key, val) {
        var url = new URL(window.location.href);
        if (val) { url.searchParams.set(key, val); } else { url.searchParams.delete(key); }
        window.location.href = url.toString();
      }
    </script>
