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
        <h2 class="font-headline-md text-headline-md text-on-surface">Sections & Divisions</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1"><?php echo count($sections); ?> class sections / divisions configured.</p>
      </div>
      <div class="flex items-center gap-2 shrink-0">
        <button onclick="openAddSectionModal()" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer">
          <span class="material-symbols-outlined text-[18px]">add_circle</span>Add Section
        </button>
      </div>
    </div>

    <!-- Filter Bar -->
    <div class="flex flex-col md:flex-row gap-3 mb-4 flex-wrap">
      <select onchange="window.location.href='<?php echo site_url('academics/sections'); ?>' + (this.value ? '?class_id=' + this.value : '')" class="px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md text-on-surface-variant">
        <option value="">All Classes</option>
        <?php foreach ($classes as $cls): ?>
          <option value="<?php echo $cls->class_id; ?>" <?php echo ($this->input->get('class_id') == $cls->class_id) ? 'selected' : ''; ?>><?php echo html_escape($cls->class_name); ?></option>
        <?php endforeach; ?>
      </select>
      <a href="<?php echo site_url('academics/sections'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined text-[18px]">restart_alt</span>Reset</a>
    </div>

    <!-- Sections Table -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
      <div class="table-scroll overflow-x-auto">
        <table class="w-full data-table zebra border-collapse">
          <thead>
            <tr class="border-b border-outline-variant/60">
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Class</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Section / Division</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Class Teacher</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Room No.</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Capacity</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Students</th>
              <th class="text-right px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/30 text-body-md">
            <?php foreach ($sections as $sec): ?>
              <tr class='hover:bg-surface-container-low transition-colors'>
                <td class="px-4 py-3 font-semibold text-on-surface whitespace-nowrap"><?php echo html_escape($sec->class_name); ?></td>
                <td class="px-4 py-3 font-bold text-primary whitespace-nowrap">
                  <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-surface-container-high text-on-surface">Section <?php echo html_escape($sec->section_name); ?></span>
                  <?php if (!empty($sec->description)): ?>
                    <div class="text-[11px] text-on-surface-variant font-normal mt-0.5"><?php echo html_escape($sec->description); ?></div>
                  <?php endif; ?>
                </td>
                <td class="px-4 py-3 text-on-surface whitespace-nowrap">
                  <?php if ($sec->class_teacher_name): ?>
                    <span class="inline-flex items-center gap-1.5 font-medium text-secondary">
                      <span class="material-symbols-outlined text-[18px]">person</span><?php echo html_escape($sec->class_teacher_name); ?>
                    </span>
                  <?php else: ?>
                    <a href="<?php echo site_url('academics/class_teachers?class_id=' . $sec->class_id); ?>" class="text-[12px] text-primary hover:underline">+ Assign Teacher</a>
                  <?php endif; ?>
                </td>
                <td class="px-4 py-3 text-on-surface whitespace-nowrap"><?php echo html_escape($sec->room_no ?: '—'); ?></td>
                <td class="px-4 py-3 text-on-surface whitespace-nowrap"><?php echo $sec->capacity; ?> seats</td>
                <td class="px-4 py-3 font-bold text-secondary whitespace-nowrap">
                  <a href="<?php echo site_url('students?section_id=' . $sec->section_id); ?>" class="hover:underline"><?php echo isset($sec->student_count) ? $sec->student_count : 0; ?> students</a>
                </td>
                <td class="px-4 py-3 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1.5">
                    <button onclick="openEditSectionModal(<?php echo $sec->section_id; ?>, <?php echo $sec->class_id; ?>, '<?php echo html_escape(addslashes($sec->section_name)); ?>', '<?php echo html_escape(addslashes($sec->room_no ?: '')); ?>', <?php echo $sec->capacity; ?>, '<?php echo html_escape(addslashes($sec->description ?: '')); ?>')" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors cursor-pointer" title="Edit"><span class="material-symbols-outlined text-[18px]">edit</span></button>
                    <a href="<?php echo site_url('academics/delete_section/' . $sec->section_id); ?>" onclick="return confirm('Deactivate section?')" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-error-container/20 hover:text-error transition-colors" title="Deactivate"><span class="material-symbols-outlined text-[18px]">delete</span></a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal: Add / Edit Section -->
    <div id="modal-section" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 hidden">
      <div class="elevation-3 rounded-2xl bg-surface-container-lowest border border-outline-variant w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-outline-variant">
          <h3 class="font-headline-md text-headline-md text-on-surface" id="modal-section-title">Add Section</h3>
          <button onclick="document.getElementById('modal-section').classList.add('hidden')" class="p-1 rounded-lg text-on-surface-variant hover:bg-surface-container-high cursor-pointer"><span class="material-symbols-outlined">close</span></button>
        </div>
        <?php echo form_open('academics/sections', array('class' => 'p-6 space-y-4')); ?>
          <input type="hidden" name="action" id="section_action" value="add"/>
          <input type="hidden" name="section_id" id="modal_section_id"/>
          <div>
            <label class="block text-label-md mb-1">Select Class *</label>
            <select name="class_id" id="modal_section_class" required class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest">
              <?php foreach ($classes as $cls): ?>
                <option value="<?php echo $cls->class_id; ?>"><?php echo html_escape($cls->class_name); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="block text-label-md mb-1">Section / Division Name *</label>
            <input type="text" name="section_name" id="modal_section_name" required placeholder="e.g. A, B, C or Rose" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-label-md mb-1">Room No.</label>
              <input type="text" name="room_no" id="modal_section_room" placeholder="e.g. 102" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
            </div>
            <div>
              <label class="block text-label-md mb-1">Max Capacity</label>
              <input type="number" name="capacity" id="modal_section_capacity" value="40" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
            </div>
          </div>
          <div>
            <label class="block text-label-md mb-1">Description / Notes</label>
            <textarea name="description" id="modal_section_description" rows="2" placeholder="Optional notes for this division..." class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest"></textarea>
          </div>
          <div class="flex justify-end gap-2 pt-4 border-t border-outline-variant">
            <button type="button" onclick="document.getElementById('modal-section').classList.add('hidden')" class="px-4 py-2 rounded-lg border border-outline-variant text-on-surface-variant cursor-pointer">Cancel</button>
            <button type="submit" class="px-4 py-2 rounded-lg bg-secondary text-on-secondary text-label-md hover:bg-on-secondary-fixed-variant cursor-pointer">Save Section</button>
          </div>
        <?php echo form_close(); ?>
      </div>
    </div>

    <script>
      function fetchNextSectionName(classId) {
        if (!classId) return;
        $.ajax({
          url: '<?php echo site_url('academics/get_next_section_ajax'); ?>',
          type: 'POST',
          data: {
            class_id: classId,
            [window.CSRF_TOKEN_NAME]: window.CSRF_HASH
          },
          dataType: 'json',
          success: function(res) {
            if (res && res.csrf_hash) window.CSRF_HASH = res.csrf_hash;
            if (document.getElementById('section_action').value === 'add' && res && res.next_section) {
              document.getElementById('modal_section_name').value = res.next_section;
            }
          }
        });
      }

      function openAddSectionModal() {
        document.getElementById('section_action').value = 'add';
        document.getElementById('modal-section-title').textContent = 'Add Section';
        document.getElementById('modal_section_id').value = '';
        document.getElementById('modal_section_name').value = 'B';
        document.getElementById('modal_section_room').value = '';
        document.getElementById('modal_section_capacity').value = '40';
        document.getElementById('modal_section_description').value = '';
        document.getElementById('modal-section').classList.remove('hidden');

        var cls = document.getElementById('modal_section_class').value;
        if (cls) {
          fetchNextSectionName(cls);
        }
      }

      document.getElementById('modal_section_class').addEventListener('change', function() {
        if (document.getElementById('section_action').value === 'add') {
          fetchNextSectionName(this.value);
        }
      });

      function openEditSectionModal(id, classId, name, room, capacity, desc) {
        document.getElementById('section_action').value = 'edit';
        document.getElementById('modal-section-title').textContent = 'Edit Section';
        document.getElementById('modal_section_id').value = id;
        document.getElementById('modal_section_class').value = classId;
        document.getElementById('modal_section_name').value = name;
        document.getElementById('modal_section_room').value = room;
        document.getElementById('modal_section_capacity').value = capacity;
        document.getElementById('modal_section_description').value = desc || '';
        document.getElementById('modal-section').classList.remove('hidden');
      }
    </script>
