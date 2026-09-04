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
        <h2 class="font-headline-md text-headline-md text-on-surface">Classes & Grades</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1"><?php echo count($classes); ?> academic class grades structured under Academic Groups.</p>
      </div>
      <div class="flex items-center gap-2 shrink-0">
        <a href="<?php echo site_url('academics/academic_groups'); ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface text-label-md hover:bg-surface-container-high transition-colors shadow-sm cursor-pointer">
          <span class="material-symbols-outlined text-[18px]">category</span>Manage Groups
        </a>
        <button onclick="openAddClassModal()" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer">
          <span class="material-symbols-outlined text-[18px]">add_circle</span>Add Class
        </button>
      </div>
    </div>

    <!-- Classes Grid / Table -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
      <div class="table-scroll overflow-x-auto">
        <table class="w-full data-table zebra border-collapse">
          <thead>
            <tr class="border-b border-outline-variant/60">
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Academic Group</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Class Name</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Class Code</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Academic Session</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Capacity</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Enrolled Students</th>
              <th class="text-right px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/30 text-body-md">
            <?php foreach ($classes as $cls): ?>
              <tr class='hover:bg-surface-container-low transition-colors'>
                <td class="px-4 py-3 whitespace-nowrap">
                  <?php if (!empty($cls->group_name)): ?>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-primary/10 text-primary border border-primary/20">
                      <?php echo html_escape($cls->group_name); ?>
                    </span>
                  <?php else: ?>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-zinc-100 text-zinc-600 border border-zinc-200">
                      Unassigned
                    </span>
                  <?php endif; ?>
                </td>
                <td class="px-4 py-3 font-semibold text-on-surface whitespace-nowrap">
                  <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">school</span>
                    <div>
                      <a href="<?php echo site_url('academics/divisions?class_id=' . $cls->class_id); ?>" class="hover:underline text-on-surface font-semibold"><?php echo html_escape($cls->class_name); ?></a>
                      <?php if (!empty($cls->description)): ?>
                        <div class="text-[12px] text-on-surface-variant font-normal"><?php echo html_escape($cls->description); ?></div>
                      <?php endif; ?>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3 font-mono text-primary font-medium whitespace-nowrap"><?php echo html_escape($cls->class_code); ?></td>
                <td class="px-4 py-3 text-on-surface whitespace-nowrap"><?php echo html_escape($cls->year_name ?: '2026-2027'); ?></td>
                <td class="px-4 py-3 text-on-surface whitespace-nowrap"><?php echo $cls->capacity; ?> seats</td>
                <td class="px-4 py-3 font-bold text-secondary whitespace-nowrap">
                  <a href="<?php echo site_url('students?class_id=' . $cls->class_id); ?>" class="hover:underline"><?php echo isset($cls->student_count) ? $cls->student_count : 0; ?> students</a>
                </td>
                <td class="px-4 py-3 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1.5">
                    <a href="<?php echo site_url('academics/divisions?class_id=' . $cls->class_id); ?>" class="px-3 py-1 rounded bg-surface-container-high text-on-surface text-label-md hover:bg-surface-container-highest transition-colors inline-flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">view_list</span>Divisions</a>
                    <button onclick="openEditClassModal(<?php echo $cls->class_id; ?>, '<?php echo html_escape(addslashes($cls->class_name)); ?>', '<?php echo html_escape(addslashes($cls->class_code)); ?>', <?php echo $cls->capacity; ?>, '<?php echo html_escape(addslashes($cls->description ?: '')); ?>', <?php echo $cls->academic_year_id ?: 1; ?>, <?php echo $cls->academic_group_id ? (int)$cls->academic_group_id : 'null'; ?>)" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors cursor-pointer" title="Edit"><span class="material-symbols-outlined text-[18px]">edit</span></button>
                    <a href="<?php echo site_url('academics/delete_class/' . $cls->class_id); ?>" onclick="return confirm('Deactivate class?')" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-error-container/20 hover:text-error transition-colors" title="Deactivate"><span class="material-symbols-outlined text-[18px]">delete</span></a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal: Add / Edit Class -->
    <div id="modal-class" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 hidden">
      <div class="elevation-3 rounded-2xl bg-surface-container-lowest border border-outline-variant w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-outline-variant">
          <h3 class="font-headline-md text-headline-md text-on-surface" id="modal-class-title">Add Class</h3>
          <button onclick="document.getElementById('modal-class').classList.add('hidden')" class="p-1 rounded-lg text-on-surface-variant hover:bg-surface-container-high cursor-pointer"><span class="material-symbols-outlined">close</span></button>
        </div>
        <?php echo form_open('academics/classes', array('class' => 'p-6 space-y-4')); ?>
          <input type="hidden" name="action" id="class_action" value="add"/>
          <input type="hidden" name="class_id" id="modal_class_id"/>
          
          <div>
            <label class="block text-label-md mb-1 text-on-surface">Academic Session *</label>
            <select name="academic_year_id" id="modal_class_year" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface">
              <?php foreach ($years as $yr): ?>
                <option value="<?php echo $yr->academic_year_id; ?>"><?php echo html_escape($yr->year_name); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="block text-label-md mb-1 text-on-surface">Academic Group *</label>
            <select name="academic_group_id" id="modal_academic_group_id" required onchange="loadGroupClasses(this.value)" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface">
              <option value="">Select Academic Group</option>
              <?php if (!empty($groups)): ?>
                <?php foreach ($groups as $grp): ?>
                  <option value="<?php echo $grp->academic_group_id; ?>"><?php echo html_escape($grp->group_name); ?> (<?php echo html_escape($grp->description ?: 'Group'); ?>)</option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>

          <div>
            <label class="block text-label-md mb-1 text-on-surface">Class *</label>
            <div class="flex gap-2">
              <select id="modal_class_select" onchange="onClassOptionSelected(this.value)" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface">
                <option value="">Select an Academic Group first</option>
              </select>
              <input type="text" name="class_name" id="modal_class_name" required placeholder="Class Name" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface"/>
            </div>
            <p class="text-xs text-on-surface-variant mt-1">Options are dynamically populated from the database based on the selected group.</p>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-label-md mb-1 text-on-surface">Class Code</label>
              <input type="text" name="class_code" id="modal_class_code" placeholder="e.g. CLS-10" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest uppercase text-on-surface"/>
            </div>
            <div>
              <label class="block text-label-md mb-1 text-on-surface">Max Capacity</label>
              <input type="number" name="capacity" id="modal_class_capacity" value="40" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface"/>
            </div>
          </div>

          <div>
            <label class="block text-label-md mb-1 text-on-surface">Description / Notes</label>
            <textarea name="description" id="modal_class_description" rows="2" placeholder="Optional notes about curriculum or track..." class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface"></textarea>
          </div>

          <div class="flex justify-end gap-2 pt-4 border-t border-outline-variant">
            <button type="button" onclick="document.getElementById('modal-class').classList.add('hidden')" class="px-4 py-2 rounded-lg border border-outline-variant text-on-surface-variant cursor-pointer text-label-md">Cancel</button>
            <button type="submit" class="px-4 py-2 rounded-lg bg-secondary text-on-secondary text-label-md hover:bg-on-secondary-fixed-variant cursor-pointer shadow-sm">Save Class</button>
          </div>
        <?php echo form_close(); ?>
      </div>
    </div>

    <script>
      function loadGroupClasses(groupId, selectedClassName = '') {
        const select = document.getElementById('modal_class_select');
        const nameInput = document.getElementById('modal_class_name');
        
        if (!groupId) {
          select.innerHTML = '<option value="">Select an Academic Group first</option>';
          return;
        }

        select.innerHTML = '<option value="">Loading classes from database...</option>';

        fetch('<?php echo site_url("academics/ajax_get_group_classes"); ?>?academic_group_id=' + groupId)
          .then(res => res.json())
          .then(data => {
            select.innerHTML = '<option value="">-- Choose Standard Class --</option>';
            if (data.classes && data.classes.length > 0) {
              data.classes.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c.class_name;
                opt.textContent = c.class_name;
                if (selectedClassName && selectedClassName === c.class_name) {
                  opt.selected = true;
                }
                select.appendChild(opt);
              });
            }
          })
          .catch(err => {
            console.error('Error loading group classes:', err);
            select.innerHTML = '<option value="">Error loading class options</option>';
          });
      }

      function onClassOptionSelected(val) {
        if (val) {
          document.getElementById('modal_class_name').value = val;
          const codeInput = document.getElementById('modal_class_code');
          if (!codeInput.value || codeInput.value.startsWith('CLS-')) {
            codeInput.value = 'CLS-' + val.replace(/\s+/g, '').toUpperCase();
          }
        }
      }

      function openAddClassModal() {
        document.getElementById('class_action').value = 'add';
        document.getElementById('modal-class-title').textContent = 'Add Class';
        document.getElementById('modal_class_id').value = '';
        document.getElementById('modal_academic_group_id').value = '';
        document.getElementById('modal_class_name').value = '';
        document.getElementById('modal_class_code').value = '';
        document.getElementById('modal_class_capacity').value = '40';
        document.getElementById('modal_class_description').value = '';
        document.getElementById('modal_class_select').innerHTML = '<option value="">Select an Academic Group first</option>';
        document.getElementById('modal-class').classList.remove('hidden');
      }

      function openEditClassModal(id, name, code, capacity, desc, yearId, groupId) {
        document.getElementById('class_action').value = 'edit';
        document.getElementById('modal-class-title').textContent = 'Edit Class';
        document.getElementById('modal_class_id').value = id;
        document.getElementById('modal_class_name').value = name;
        document.getElementById('modal_class_code').value = code;
        document.getElementById('modal_class_capacity').value = capacity;
        document.getElementById('modal_class_description').value = desc || '';
        if (yearId) {
          document.getElementById('modal_class_year').value = yearId;
        }
        if (groupId) {
          document.getElementById('modal_academic_group_id').value = groupId;
          loadGroupClasses(groupId, name);
        } else {
          document.getElementById('modal_academic_group_id').value = '';
          document.getElementById('modal_class_select').innerHTML = '<option value="">Select an Academic Group first</option>';
        }
        document.getElementById('modal-class').classList.remove('hidden');
      }
    </script>
