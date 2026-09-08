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

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h2 class="font-headline-md text-headline-md text-on-surface">Class Teachers</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Assign primary class teachers and mentors to each academic class and division.</p>
      </div>
      <div class="flex items-center gap-2 shrink-0">
        <button onclick="openAssignModal()" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer">
          <span class="material-symbols-outlined text-[18px]">person_add</span>Assign Class Teacher
        </button>
      </div>
    </div>

    <!-- Filters Bar -->
    <div class="flex flex-col md:flex-row gap-3 mb-4 flex-wrap">
      <select onchange="applyFilter('academic_year_id', this.value)" class="px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md text-on-surface-variant">
        <option value="">All Academic Years</option>
        <?php foreach ($years as $yr): ?>
          <option value="<?php echo $yr->academic_year_id; ?>" <?php echo ($this->input->get('academic_year_id') == $yr->academic_year_id) ? 'selected' : ''; ?>><?php echo html_escape($yr->year_name); ?></option>
        <?php endforeach; ?>
      </select>
      <select onchange="applyFilter('class_id', this.value)" class="px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md text-on-surface-variant">
        <option value="">All Classes</option>
        <?php foreach ($classes as $cls): ?>
          <option value="<?php echo $cls->class_id; ?>" <?php echo ($this->input->get('class_id') == $cls->class_id) ? 'selected' : ''; ?>><?php echo html_escape($cls->class_name); ?></option>
        <?php endforeach; ?>
      </select>
      <select onchange="applyFilter('staff_id', this.value)" class="px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md text-on-surface-variant">
        <option value="">All Teachers</option>
        <?php foreach ($teachers as $t): ?>
          <option value="<?php echo $t->staff_id; ?>" <?php echo ($this->input->get('staff_id') == $t->staff_id) ? 'selected' : ''; ?>><?php echo html_escape($t->full_name); ?></option>
        <?php endforeach; ?>
      </select>
      <a href="<?php echo site_url('academics/class_teachers'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined text-[18px]">restart_alt</span>Reset</a>
    </div>

    <!-- Class Teachers Table -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
      <div class="table-scroll overflow-x-auto">
        <table class="w-full data-table zebra border-collapse">
          <thead>
            <tr class="border-b border-outline-variant/60">
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Class & Division</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Assigned Class Teacher</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Employee Code</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Contact Phone</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Academic Session</th>
              <th class="text-right px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/30 text-body-md">
            <?php if (empty($assignments)): ?>
              <tr><td colspan="6" class="px-4 py-8 text-center text-on-surface-variant">No class teacher assignments found.</td></tr>
            <?php endif; ?>
            <?php foreach ($assignments as $a): ?>
              <tr class='hover:bg-surface-container-low transition-colors'>
                <td class="px-4 py-3 font-semibold text-on-surface whitespace-nowrap">
                  <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">meeting_room</span>
                    <?php echo html_escape($a->class_name . ' - Division ' . ($a->division_name ?: $a->section_name)); ?>
                  </div>
                </td>
                <td class="px-4 py-3 font-bold text-secondary whitespace-nowrap">
                  <a href="<?php echo site_url('staff/teachers?id=' . $a->staff_id); ?>" class="hover:underline"><?php echo html_escape($a->teacher_name); ?></a>
                </td>
                <td class="px-4 py-3 font-mono text-on-surface-variant whitespace-nowrap"><?php echo html_escape($a->employee_code); ?></td>
                <td class="px-4 py-3 text-on-surface whitespace-nowrap"><?php echo html_escape($a->phone); ?></td>
                <td class="px-4 py-3 text-on-surface whitespace-nowrap"><?php echo html_escape($a->year_name ?: '2026-2027'); ?></td>
                <td class="px-4 py-3 text-right whitespace-nowrap">
                  <a href="<?php echo site_url('academics/delete_class_teacher/' . $a->class_teacher_id); ?>" onclick="return confirm('Remove this class teacher assignment?')" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-error-container/20 hover:text-error transition-colors inline-flex" title="Remove"><span class="material-symbols-outlined text-[18px]">delete</span></a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal: Assign Class Teacher -->
    <div id="modal-assign-ct" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 <?php echo (!empty($modal_open)) ? '' : 'hidden'; ?>">
      <div class="elevation-3 rounded-2xl bg-surface-container-lowest border border-outline-variant w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-outline-variant">
          <h3 class="font-headline-md text-headline-md text-on-surface">Assign Class Teacher</h3>
          <button onclick="document.getElementById('modal-assign-ct').classList.add('hidden')" class="p-1 rounded-lg text-on-surface-variant hover:bg-surface-container-high cursor-pointer"><span class="material-symbols-outlined">close</span></button>
        </div>
        <?php echo form_open('academics/class_teachers', array('class' => 'p-6 space-y-4')); ?>
          <div>
            <label class="block text-label-md mb-1">Academic Session *</label>
            <select name="academic_year_id" id="modal_ct_year" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest">
              <?php foreach ($years as $yr): ?>
                <option value="<?php echo $yr->academic_year_id; ?>" <?php echo (!empty($modal_open) && $yr->academic_year_id == $modal_year_id) ? 'selected' : ''; ?>><?php echo html_escape($yr->year_name); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="block text-label-md mb-1">Class *</label>
            <select name="class_id" id="modal_ct_class" onchange="loadDivisionsForClass(this.value)" required class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest">
              <option value="">Select Class</option>
              <?php foreach ($classes as $cls): ?>
                <option value="<?php echo $cls->class_id; ?>" <?php echo (!empty($modal_open) && $cls->class_id == $modal_class_id) ? 'selected' : ''; ?>><?php echo html_escape($cls->class_name); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="block text-label-md mb-1">Division *</label>
            <select name="division_id" id="modal_ct_section" required class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest">
              <?php if (!empty($modal_open) && !empty($modal_divisions)): ?>
                <option value="">Select Division</option>
                <?php foreach ($modal_divisions as $mdiv): ?>
                  <?php $m_id = $mdiv->division_id ?: $mdiv->section_id; ?>
                  <option value="<?php echo $m_id; ?>" <?php echo ($m_id == $modal_division_id) ? 'selected' : ''; ?>>Division <?php echo html_escape($mdiv->division_name ?: $mdiv->section_name); ?></option>
                <?php endforeach; ?>
              <?php else: ?>
                <option value="">Select Class First</option>
              <?php endif; ?>
            </select>
          </div>
          <div>
            <label class="block text-label-md mb-1">Select Teaching Faculty *</label>
            <select name="staff_id" id="modal_ct_staff" required class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest">
              <option value="">Select Teacher</option>
              <?php foreach ($teachers as $t): ?>
                <option value="<?php echo $t->staff_id; ?>"><?php echo html_escape($t->full_name . ' (' . $t->employee_code . ')'); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="flex justify-end gap-2 pt-4 border-t border-outline-variant">
            <button type="button" onclick="document.getElementById('modal-assign-ct').classList.add('hidden')" class="px-4 py-2 rounded-lg border border-outline-variant text-on-surface-variant cursor-pointer">Cancel</button>
            <button type="submit" class="px-4 py-2 rounded-lg bg-secondary text-on-secondary text-label-md hover:bg-on-secondary-fixed-variant cursor-pointer">Assign Teacher</button>
          </div>
        <?php echo form_close(); ?>
      </div>
    </div>

    <script>
      function applyFilter(key, val) {
        var url = new URL(window.location.href);
        if (val) { url.searchParams.set(key, val); } else { url.searchParams.delete(key); }
        url.searchParams.delete('open_assign');
        window.location.href = url.toString();
      }

      function openAssignModal() {
        document.getElementById('modal_ct_class').selectedIndex = 0;
        document.getElementById('modal_ct_section').innerHTML = '<option value="">Select Class First</option>';
        document.getElementById('modal_ct_staff').selectedIndex = 0;
        document.getElementById('modal-assign-ct').classList.remove('hidden');
      }

      function loadDivisionsForClass(classId, selectedDivisionId) {
        var secSelect = document.getElementById('modal_ct_section');
        secSelect.innerHTML = '<option value="">Loading divisions...</option>';
        if (!classId) {
          secSelect.innerHTML = '<option value="">Select Class First</option>';
          return Promise.resolve();
        }

        return fetch('<?php echo site_url('academics/ajax_get_divisions/'); ?>' + classId)
          .then(res => res.json())
          .then(data => {
            if (!data || data.length === 0) {
              secSelect.innerHTML = '<option value="">No divisions found</option>';
            } else {
              var opts = '<option value="">Select Division</option>';
              data.forEach(function(sec) {
                var divId = sec.division_id || sec.section_id;
                var divName = sec.division_name || sec.section_name;
                var isSelected = (selectedDivisionId && String(divId) === String(selectedDivisionId)) ? ' selected' : '';
                opts += '<option value="' + divId + '"' + isSelected + '>Division ' + divName + '</option>';
              });
              secSelect.innerHTML = opts;
              if (selectedDivisionId) {
                secSelect.value = selectedDivisionId;
              }
            }
          })
          .catch(function() {
            secSelect.innerHTML = '<option value="">Error loading divisions</option>';
          });
      }
      var loadSectionsForClass = loadDivisionsForClass;

      document.addEventListener('DOMContentLoaded', function() {
        var urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('open_assign') === '1') {
          var modal = document.getElementById('modal-assign-ct');
          if (modal) {
            modal.classList.remove('hidden');
          }
        }
      });
    </script>
