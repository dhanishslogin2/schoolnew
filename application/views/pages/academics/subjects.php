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
        <h2 class="font-headline-md text-headline-md text-on-surface">Subjects</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1"><?php echo count($subjects); ?> academic subjects and course curriculum modules.</p>
      </div>
      <div class="flex items-center gap-2 shrink-0">
        <button onclick="openAddSubjectModal()" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer">
          <span class="material-symbols-outlined text-[18px]">add_circle</span>Add Subject
        </button>
      </div>
    </div>

    <!-- Filter Bar -->
    <div class="flex flex-col md:flex-row gap-3 mb-4 flex-wrap">
      <select onchange="window.location.href='<?php echo site_url('academics/subjects'); ?>' + (this.value ? '?class_id=' + this.value : '')" class="px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md text-on-surface-variant">
        <option value="">All Classes</option>
        <?php foreach ($classes as $cls): ?>
          <option value="<?php echo $cls->class_id; ?>" <?php echo ($this->input->get('class_id') == $cls->class_id) ? 'selected' : ''; ?>><?php echo html_escape($cls->class_name); ?></option>
        <?php endforeach; ?>
      </select>
      <a href="<?php echo site_url('academics/subjects'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined text-[18px]">restart_alt</span>Reset</a>
    </div>

    <!-- Subjects Table -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
      <div class="table-scroll overflow-x-auto">
        <table class="w-full data-table zebra border-collapse">
          <thead>
            <tr class="border-b border-outline-variant/60">
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Subject Name</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Code</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Applicable Class</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Subject Type</th>
              <th class="text-right px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/30 text-body-md">
            <?php foreach ($subjects as $sub): ?>
              <?php
                $badge = 'bg-surface-container-high text-on-surface';
                if ($sub->subject_type === 'Core') $badge = 'bg-primary-fixed/30 text-primary font-semibold';
                if ($sub->subject_type === 'Language') $badge = 'bg-secondary-container text-on-secondary-container font-semibold';
                if ($sub->subject_type === 'Elective') $badge = 'bg-purple-100 text-purple-800 font-semibold';
                if ($sub->subject_type === 'Practical') $badge = 'bg-amber-100 text-amber-900 font-semibold';
                if ($sub->subject_type === 'Other') $badge = 'bg-surface-container-highest text-on-surface-variant font-semibold';
              ?>
              <tr class='hover:bg-surface-container-low transition-colors'>
                <td class="px-4 py-3 font-semibold text-on-surface whitespace-nowrap">
                  <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">menu_book</span>
                    <div>
                      <div><?php echo html_escape($sub->subject_name); ?></div>
                      <?php if (!empty($sub->description)): ?>
                        <div class="text-[11px] text-on-surface-variant font-normal"><?php echo html_escape($sub->description); ?></div>
                      <?php endif; ?>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3 font-mono text-primary font-bold whitespace-nowrap"><?php echo html_escape($sub->subject_code); ?></td>
                <td class="px-4 py-3 text-on-surface whitespace-nowrap"><?php echo html_escape($sub->class_name ?: 'General / All Classes'); ?></td>
                <td class="px-4 py-3 whitespace-nowrap">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] <?php echo $badge; ?>"><?php echo html_escape($sub->subject_type); ?></span>
                </td>
                <td class="px-4 py-3 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1.5">
                    <button onclick="openEditSubjectModal(<?php echo $sub->subject_id; ?>, '<?php echo html_escape(addslashes($sub->subject_name)); ?>', '<?php echo html_escape(addslashes($sub->subject_code)); ?>', '<?php echo $sub->subject_type; ?>', <?php echo $sub->class_id ?: 'null'; ?>, '<?php echo html_escape(addslashes($sub->description ?: '')); ?>')" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors cursor-pointer" title="Edit"><span class="material-symbols-outlined text-[18px]">edit</span></button>
                    <a href="<?php echo site_url('academics/delete_subject/' . $sub->subject_id); ?>" onclick="return confirm('Deactivate subject?')" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-error-container/20 hover:text-error transition-colors" title="Deactivate"><span class="material-symbols-outlined text-[18px]">delete</span></a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal: Add / Edit Subject -->
    <div id="modal-subject" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 hidden">
      <div class="elevation-3 rounded-2xl bg-surface-container-lowest border border-outline-variant w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-outline-variant">
          <h3 class="font-headline-md text-headline-md text-on-surface" id="modal-subject-title">Add Subject</h3>
          <button onclick="document.getElementById('modal-subject').classList.add('hidden')" class="p-1 rounded-lg text-on-surface-variant hover:bg-surface-container-high cursor-pointer"><span class="material-symbols-outlined">close</span></button>
        </div>
        <?php echo form_open('academics/subjects', array('class' => 'p-6 space-y-4')); ?>
          <input type="hidden" name="action" id="subject_action" value="add"/>
          <input type="hidden" name="subject_id" id="modal_subject_id"/>
          <div>
            <label class="block text-label-md mb-1">Subject Name *</label>
            <input type="text" name="subject_name" id="modal_subject_name" required placeholder="e.g. Mathematics Core, Physics" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest"/>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-label-md mb-1">Subject Code</label>
              <input type="text" name="subject_code" id="modal_subject_code" placeholder="e.g. MATH10" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest font-mono uppercase"/>
            </div>
            <div>
              <label class="block text-label-md mb-1">Subject Type *</label>
              <select name="subject_type" id="modal_subject_type" required class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest">
                <option value="Core">Core</option>
                <option value="Elective">Elective</option>
                <option value="Language">Language</option>
                <option value="Practical">Practical</option>
                <option value="Other">Other</option>
              </select>
            </div>
          </div>
          <div>
            <label class="block text-label-md mb-1">Applicable Class (Optional)</label>
            <select name="class_id" id="modal_subject_class" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest">
              <option value="">General / All Classes</option>
              <?php foreach ($classes as $cls): ?>
                <option value="<?php echo $cls->class_id; ?>"><?php echo html_escape($cls->class_name); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="block text-label-md mb-1">Description / Notes</label>
            <textarea name="description" id="modal_subject_description" rows="2" placeholder="Optional curriculum notes or syllabus details..." class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest"></textarea>
          </div>
          <div class="flex justify-end gap-2 pt-4 border-t border-outline-variant">
            <button type="button" onclick="document.getElementById('modal-subject').classList.add('hidden')" class="px-4 py-2 rounded-lg border border-outline-variant text-on-surface-variant cursor-pointer">Cancel</button>
            <button type="submit" class="px-4 py-2 rounded-lg bg-secondary text-on-secondary text-label-md hover:bg-on-secondary-fixed-variant cursor-pointer">Save Subject</button>
          </div>
        <?php echo form_close(); ?>
      </div>
    </div>

    <script>
      function openAddSubjectModal() {
        document.getElementById('subject_action').value = 'add';
        document.getElementById('modal-subject-title').textContent = 'Add Subject';
        document.getElementById('modal_subject_id').value = '';
        document.getElementById('modal_subject_name').value = '';
        document.getElementById('modal_subject_code').value = '';
        document.getElementById('modal_subject_type').value = 'Core';
        document.getElementById('modal_subject_class').value = '';
        document.getElementById('modal_subject_description').value = '';
        document.getElementById('modal-subject').classList.remove('hidden');
      }
      function openEditSubjectModal(id, name, code, type, classId, desc) {
        document.getElementById('subject_action').value = 'edit';
        document.getElementById('modal-subject-title').textContent = 'Edit Subject';
        document.getElementById('modal_subject_id').value = id;
        document.getElementById('modal_subject_name').value = name;
        document.getElementById('modal_subject_code').value = code;
        document.getElementById('modal_subject_type').value = type;
        document.getElementById('modal_subject_class').value = classId ? classId : '';
        document.getElementById('modal_subject_description').value = desc || '';
        document.getElementById('modal-subject').classList.remove('hidden');
      }
    </script>
