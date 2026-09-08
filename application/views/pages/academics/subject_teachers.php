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
        <h2 class="font-headline-md text-headline-md text-on-surface">Subject Teachers</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Assign subject specialist teachers to specific academic classes and divisions.</p>
      </div>
      <div class="flex items-center gap-2 shrink-0">
        <button onclick="openAssignSubjectTeacherModal()" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer">
          <span class="material-symbols-outlined text-[18px]">person_add</span>Assign Subject Teacher
        </button>
      </div>
    </div>

    <!-- Filters Bar -->
    <div class="flex flex-col md:flex-row gap-3 mb-4 flex-wrap">
      <select id="filter_academic_year_id" onchange="onYearFilterChange(this.value)" class="px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md text-on-surface-variant">
        <option value="">All Academic Years</option>
        <?php foreach ($years as $yr): ?>
          <option value="<?php echo $yr->academic_year_id; ?>" <?php echo ($selected_year_id == $yr->academic_year_id) ? 'selected' : ''; ?>><?php echo html_escape($yr->year_name); ?></option>
        <?php endforeach; ?>
      </select>
      <select id="filter_class_id" onchange="onClassFilterChange(this.value)" class="px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md text-on-surface-variant">
        <option value="">All Classes</option>
        <?php foreach ($classes as $cls): ?>
          <option value="<?php echo $cls->class_id; ?>" <?php echo ($selected_class_id == $cls->class_id) ? 'selected' : ''; ?>><?php echo html_escape($cls->class_name); ?></option>
        <?php endforeach; ?>
      </select>
      <select id="filter_division_id" onchange="applyFilter('division_id', this.value)" <?php echo empty($selected_class_id) ? 'disabled' : ''; ?> class="px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md text-on-surface-variant <?php echo empty($selected_class_id) ? 'opacity-60 cursor-not-allowed' : ''; ?>">
        <?php if (empty($selected_class_id)): ?>
          <option value="">Select Class First</option>
        <?php elseif (empty($filter_divisions)): ?>
          <option value="">No divisions found</option>
        <?php else: ?>
          <option value="">All Divisions</option>
          <?php foreach ($filter_divisions as $div): ?>
            <?php $d_id = $div->division_id ?: $div->section_id; ?>
            <option value="<?php echo $d_id; ?>" <?php echo ($selected_division_id == $d_id) ? 'selected' : ''; ?>>Division <?php echo html_escape($div->division_name ?: $div->section_name); ?></option>
          <?php endforeach; ?>
        <?php endif; ?>
      </select>
      <select id="filter_subject_id" onchange="applyFilter('subject_id', this.value)" <?php echo empty($selected_class_id) ? 'disabled' : ''; ?> class="px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md text-on-surface-variant <?php echo empty($selected_class_id) ? 'opacity-60 cursor-not-allowed' : ''; ?>">
        <option value="">All Subjects</option>
        <?php if (!empty($selected_class_id)): ?>
          <?php foreach ($filter_subjects as $sub): ?>
            <option value="<?php echo $sub->subject_id; ?>" <?php echo ($selected_subject_id == $sub->subject_id) ? 'selected' : ''; ?>><?php echo html_escape($sub->subject_name); ?></option>
          <?php endforeach; ?>
        <?php endif; ?>
      </select>
      <select onchange="applyFilter('staff_id', this.value)" class="px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md text-on-surface-variant">
        <option value="">All Teachers</option>
        <?php foreach ($teachers as $t): ?>
          <option value="<?php echo $t->staff_id; ?>" <?php echo ($selected_staff_id == $t->staff_id) ? 'selected' : ''; ?>><?php echo html_escape($t->full_name); ?></option>
        <?php endforeach; ?>
      </select>
      <a href="<?php echo site_url('academics/subject_teachers'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined text-[18px]">restart_alt</span>Reset</a>
    </div>

    <!-- Subject Teachers Table -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
      <div class="table-scroll overflow-x-auto">
        <table class="w-full data-table zebra border-collapse">
          <thead>
            <tr class="border-b border-outline-variant/60">
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Subject</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Class & Division</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Assigned Teacher</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Employee Code</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Academic Session</th>
              <th class="text-right px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/30 text-body-md">
            <?php if (empty($assignments)): ?>
              <tr><td colspan="6" class="px-4 py-8 text-center text-on-surface-variant">No subject teacher assignments found.</td></tr>
            <?php endif; ?>
            <?php foreach ($assignments as $a): ?>
              <tr class='hover:bg-surface-container-low transition-colors'>
                <td class="px-4 py-3 font-semibold text-on-surface whitespace-nowrap">
                  <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">menu_book</span>
                    <div>
                      <div><?php echo html_escape($a->subject_name); ?></div>
                      <div class="text-[11px] font-mono text-on-surface-variant"><?php echo html_escape($a->subject_code); ?></div>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3 text-on-surface whitespace-nowrap font-medium"><?php echo html_escape($a->class_name . ' - Division ' . ($a->division_name ?: $a->section_name)); ?></td>
                <td class="px-4 py-3 font-bold text-secondary whitespace-nowrap">
                  <a href="<?php echo site_url('staff/teachers?id=' . $a->staff_id); ?>" class="hover:underline"><?php echo html_escape($a->teacher_name); ?></a>
                </td>
                <td class="px-4 py-3 font-mono text-on-surface-variant whitespace-nowrap"><?php echo html_escape($a->employee_code); ?></td>
                <td class="px-4 py-3 text-on-surface whitespace-nowrap"><?php echo html_escape($a->year_name ?: '2026-2027'); ?></td>
                <td class="px-4 py-3 text-right whitespace-nowrap">
                  <a href="<?php echo site_url('academics/delete_subject_teacher/' . $a->subject_teacher_id); ?>" onclick="return confirm('Remove subject teacher allocation?')" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-error-container/20 hover:text-error transition-colors inline-flex" title="Remove"><span class="material-symbols-outlined text-[18px]">delete</span></a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal: Assign Subject Teacher -->
    <div id="modal-assign-st" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 hidden">
      <div class="elevation-3 rounded-2xl bg-surface-container-lowest border border-outline-variant w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-outline-variant">
          <h3 class="font-headline-md text-headline-md text-on-surface">Assign Subject Teacher</h3>
          <button onclick="document.getElementById('modal-assign-st').classList.add('hidden')" class="p-1 rounded-lg text-on-surface-variant hover:bg-surface-container-high cursor-pointer"><span class="material-symbols-outlined">close</span></button>
        </div>
        <?php echo form_open('academics/subject_teachers', array('class' => 'p-6 space-y-4')); ?>
          <div>
            <label class="block text-label-md mb-1">Academic Session *</label>
            <select name="academic_year_id" id="modal_st_year" onchange="if(document.getElementById('modal_st_class').value) loadClassDependencies(document.getElementById('modal_st_class').value)" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest">
              <?php foreach ($years as $yr): ?>
                <option value="<?php echo $yr->academic_year_id; ?>"><?php echo html_escape($yr->year_name); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="block text-label-md mb-1">Class *</label>
            <select name="class_id" id="modal_st_class" onchange="loadClassDependencies(this.value)" required class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest">
              <option value="">Select Class</option>
              <?php foreach ($classes as $cls): ?>
                <option value="<?php echo $cls->class_id; ?>"><?php echo html_escape($cls->class_name); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="block text-label-md mb-1">Division *</label>
            <select name="division_id" id="modal_st_section" required class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest">
              <option value="">Select Class First</option>
            </select>
          </div>
          <div>
            <label class="block text-label-md mb-1">Subject *</label>
            <select name="subject_id" id="modal_st_subject" required class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest">
              <option value="">Select Class First</option>
            </select>
          </div>
          <div>
            <label class="block text-label-md mb-1">Assigned Teacher *</label>
            <select name="staff_id" id="modal_st_staff" required class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest">
              <option value="">Select Teacher</option>
              <?php foreach ($teachers as $t): ?>
                <option value="<?php echo $t->staff_id; ?>"><?php echo html_escape($t->full_name . ' (' . $t->employee_code . ')'); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="flex justify-end gap-2 pt-4 border-t border-outline-variant">
            <button type="button" onclick="document.getElementById('modal-assign-st').classList.add('hidden')" class="px-4 py-2 rounded-lg border border-outline-variant text-on-surface-variant cursor-pointer">Cancel</button>
            <button type="submit" class="px-4 py-2 rounded-lg bg-secondary text-on-secondary text-label-md hover:bg-on-secondary-fixed-variant cursor-pointer">Assign Subject Teacher</button>
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

      function onYearFilterChange(yearId) {
        var url = new URL(window.location.href);
        if (yearId) {
          url.searchParams.set('academic_year_id', yearId);
        } else {
          url.searchParams.delete('academic_year_id');
        }
        // Changing academic year clears stale subject selection
        url.searchParams.delete('subject_id');
        window.location.href = url.toString();
      }

      function onClassFilterChange(classId) {
        var url = new URL(window.location.href);
        if (classId) {
          url.searchParams.set('class_id', classId);
        } else {
          url.searchParams.delete('class_id');
        }
        // Changing class clears dependent division and subject selections
        url.searchParams.delete('division_id');
        url.searchParams.delete('section_id');
        url.searchParams.delete('subject_id');
        window.location.href = url.toString();
      }

      function openAssignSubjectTeacherModal() {
        document.getElementById('modal_st_class').selectedIndex = 0;
        document.getElementById('modal_st_section').innerHTML = '<option value="">Select Class First</option>';
        document.getElementById('modal_st_subject').innerHTML = '<option value="">Select Class First</option>';
        document.getElementById('modal-assign-st').classList.remove('hidden');
      }

      function loadClassDependencies(classId) {
        var secSelect = document.getElementById('modal_st_section');
        var subSelect = document.getElementById('modal_st_subject');

        secSelect.innerHTML = '<option value="">Loading divisions...</option>';
        subSelect.innerHTML = '<option value="">Loading subjects...</option>';

        if (!classId) {
          secSelect.innerHTML = '<option value="">Select Class First</option>';
          subSelect.innerHTML = '<option value="">Select Class First</option>';
          return;
        }

        // Fetch Sections
        fetch('<?php echo site_url('academics/ajax_get_divisions/'); ?>' + classId)
          .then(res => res.json())
          .then(data => {
            if (data.length === 0) {
              secSelect.innerHTML = '<option value="">No divisions found</option>';
            } else {
              var opts = '<option value="">Select Division</option>';
              data.forEach(function(sec) {
                var divId = sec.division_id || sec.section_id;
                var divName = sec.division_name || sec.section_name;
                opts += '<option value="' + divId + '">Division ' + divName + '</option>';
              });
              secSelect.innerHTML = opts;
            }
          });

        // Fetch Subjects with Academic Year awareness
        var yearSelect = document.getElementById('modal_st_year');
        var yearParam = (yearSelect && yearSelect.value) ? '?academic_year_id=' + encodeURIComponent(yearSelect.value) : '';
        fetch('<?php echo site_url('academics/ajax_get_subjects/'); ?>' + classId + yearParam)
          .then(res => res.json())
          .then(data => {
            if (data.length === 0) {
              subSelect.innerHTML = '<option value="">No subjects found</option>';
            } else {
              var opts = '<option value="">Select Subject</option>';
              data.forEach(function(sub) {
                opts += '<option value="' + sub.subject_id + '">' + sub.subject_name + ' (' + sub.subject_code + ')</option>';
              });
              subSelect.innerHTML = opts;
            }
          });
      }
    </script>
