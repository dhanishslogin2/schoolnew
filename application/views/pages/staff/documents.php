<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h2 class="font-headline-md text-headline-md text-on-surface">Staff Documents</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Official repository for faculty appointment letters, qualification certificates, ID cards, and experience records.</p>
      </div>
      <div class="flex items-center gap-2 shrink-0">
        <button onclick="document.getElementById('modal-upload-doc').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer">
          <span class="material-symbols-outlined text-[18px]">upload_file</span>Upload Staff Document
        </button>
      </div>
    </div>

    <!-- Filter Bar -->
    <div class="flex flex-col md:flex-row gap-3 mb-4 flex-wrap">
      <select onchange="applyFilter('document_type', this.value)" class="px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md text-on-surface-variant">
        <option value="">All Document Types</option>
        <?php if (!empty($document_types)): ?>
          <?php foreach ($document_types as $dt): ?>
            <option value="<?php echo html_escape($dt->document_name); ?>" <?php echo ($this->input->get('document_type') === $dt->document_name) ? 'selected' : ''; ?>><?php echo html_escape($dt->document_name); ?></option>
          <?php endforeach; ?>
        <?php endif; ?>
        <option value="Other" <?php echo ($this->input->get('document_type') === 'Other') ? 'selected' : ''; ?>>Other</option>
      </select>
      <select onchange="applyFilter('department_id', this.value)" class="px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md text-on-surface-variant">
        <option value="">All Departments</option>
        <?php foreach ($departments as $dept): ?>
          <option value="<?php echo $dept->department_id; ?>" <?php echo ($this->input->get('department_id') == $dept->department_id) ? 'selected' : ''; ?>><?php echo html_escape($dept->department_name); ?></option>
        <?php endforeach; ?>
      </select>
      <a href="<?php echo site_url('staff/documents'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined text-[18px]">restart_alt</span>Reset</a>
    </div>

    <!-- Documents Table -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
      <div class="table-scroll overflow-x-auto">
        <table class="w-full data-table zebra border-collapse">
          <thead>
            <tr class="border-b border-outline-variant/60">
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Document Title</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Document Type</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Staff Member</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Employee Code</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Department</th>
              <th class="text-left px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Uploaded On</th>
              <th class="text-right px-4 py-3 text-label-md text-on-surface-variant uppercase whitespace-nowrap">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/30 text-body-md">
            <?php if (empty($documents)): ?>
              <tr><td colspan="7" class="px-4 py-8 text-center text-on-surface-variant">No staff documents found.</td></tr>
            <?php endif; ?>
            <?php foreach ($documents as $doc): ?>
              <tr class='hover:bg-surface-container-low transition-colors'>
                <td class="px-4 py-3 font-semibold text-on-surface whitespace-nowrap">
                  <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">description</span>
                    <?php echo html_escape($doc->document_name); ?>
                  </div>
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                  <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-surface-container-high text-on-surface"><?php echo html_escape($doc->document_type); ?></span>
                </td>
                <td class="px-4 py-3 text-on-surface font-medium whitespace-nowrap">
                  <a href="<?php echo site_url('staff/profile/' . $doc->staff_id); ?>" class="hover:underline text-primary"><?php echo html_escape($doc->full_name); ?></a>
                </td>
                <td class="px-4 py-3 font-mono text-on-surface-variant whitespace-nowrap"><?php echo html_escape($doc->employee_code); ?></td>
                <td class="px-4 py-3 text-on-surface whitespace-nowrap"><?php echo html_escape($doc->department_name ?: '—'); ?></td>
                <td class="px-4 py-3 text-on-surface-variant whitespace-nowrap"><?php echo date('d M Y', strtotime($doc->created_at)); ?></td>
                <td class="px-4 py-3 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1.5">
                    <a href="<?php echo site_url('staff/view_document/' . $doc->document_id); ?>" target="_blank" class="px-2.5 py-1 rounded bg-surface-container-high text-on-surface text-label-md hover:bg-surface-container-highest transition-colors inline-flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">visibility</span>View</a>
                    <a href="<?php echo site_url('staff/download_document/' . $doc->document_id); ?>" class="px-2.5 py-1 rounded bg-surface-container-high text-secondary text-label-md hover:bg-surface-container-highest transition-colors inline-flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">download</span>Download</a>
                    <a href="<?php echo site_url('staff/delete_document/' . $doc->document_id . '?redirect_to=' . urlencode(current_url())); ?>" onclick="return confirm('Delete this staff document?')" class="px-2.5 py-1 rounded text-error hover:bg-error-container/20 transition-colors inline-flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">delete</span>Delete</a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Upload Document Modal -->
    <div id="modal-upload-doc" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 hidden">
      <div class="elevation-3 rounded-2xl bg-surface-container-lowest border border-outline-variant w-full max-w-lg">
        <div class="flex items-center justify-between px-6 py-4 border-b border-outline-variant">
          <h3 class="font-headline-md text-headline-md text-on-surface">Upload Staff Document</h3>
          <button onclick="document.getElementById('modal-upload-doc').classList.add('hidden')" class="p-1 rounded-lg text-on-surface-variant hover:bg-surface-container-high cursor-pointer"><span class="material-symbols-outlined">close</span></button>
        </div>
        <?php echo form_open_multipart('staff/upload_document', array('class' => 'p-6 space-y-4')); ?>
          <input type="hidden" name="redirect_to" value="<?php echo current_url(); ?>"/>
          <div>
            <label class="block text-label-md mb-1">Select Staff Member *</label>
            <select name="staff_id" required class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-medium text-on-surface">
              <?php foreach ($staff_list as $st): ?>
                <option value="<?php echo $st->staff_id; ?>"><?php echo html_escape($st->full_name . ' (' . $st->employee_code . ' - ' . $st->department_name . ')'); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="block text-label-md mb-1">Document Type *</label>
            <select name="document_type_id" required class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-medium text-on-surface">
              <?php if (!empty($document_types)): ?>
                <?php foreach ($document_types as $dt): ?>
                  <option value="<?php echo $dt->id; ?>"><?php echo html_escape($dt->document_name); ?></option>
                <?php endforeach; ?>
              <?php endif; ?>
              <option value="0">Other Document</option>
            </select>
          </div>
          <div>
            <label class="block text-label-md mb-1">Document Title (Optional)</label>
            <input type="text" name="document_name" placeholder="Leave empty to use document type" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md"/>
          </div>
          <div>
            <label class="block text-label-md mb-1">Select File *</label>
            <input type="file" name="document_file" required class="w-full px-3 py-1.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface-variant file:mr-2.5 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-secondary/15 file:text-secondary"/>
          </div>
          <div class="flex justify-end gap-2 pt-4 border-t border-outline-variant">
            <button type="button" onclick="document.getElementById('modal-upload-doc').classList.add('hidden')" class="px-4 py-2 rounded-lg border border-outline-variant text-on-surface-variant cursor-pointer">Cancel</button>
            <button type="submit" class="px-4 py-2 rounded-lg bg-secondary text-on-secondary text-label-md hover:bg-on-secondary-fixed-variant cursor-pointer">Upload Document</button>
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
