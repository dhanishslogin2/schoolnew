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

    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h2 class="font-headline-md text-headline-md text-on-surface">Attendance History</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Audit log and search past attendance records across daily and period sessions.</p>
      </div>
      <div class="flex items-center gap-2 flex-wrap shrink-0">
        <a href="<?php echo site_url('attendance/reports'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md hover:bg-surface-container-high transition-colors">
          <span class="material-symbols-outlined text-[18px]">bar_chart</span>Attendance Reports
        </a>
      </div>
    </div>

    <!-- Multi-Filter Bar -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4 mb-6">
      <form method="get" action="<?php echo site_url('attendance/history'); ?>" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <!-- Search input -->
          <div>
            <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Search</label>
            <div class="relative">
              <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[18px]">search</span>
              <input type="text" name="search" value="<?php echo html_escape($filters['search']); ?>" placeholder="Student name, admission #..." class="w-full pl-9 pr-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
            </div>
          </div>

          <!-- Academic Year -->
          <div>
            <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Academic Year</label>
            <select name="academic_year_id" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
              <option value="">All Years</option>
              <?php foreach ($years as $y): ?>
                <option value="<?php echo $y->academic_year_id; ?>" <?php echo ($filters['academic_year_id'] == $y->academic_year_id) ? 'selected' : ''; ?>>
                  <?php echo html_escape($y->year_name); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Class -->
          <div>
            <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Class</label>
            <select name="class_id" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
              <option value="">All Classes</option>
              <?php foreach ($classes as $cls): ?>
                <option value="<?php echo $cls->class_id; ?>" <?php echo ($filters['class_id'] == $cls->class_id) ? 'selected' : ''; ?>>
                  <?php echo html_escape($cls->class_name); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Section -->
          <div>
            <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Section</label>
            <select name="section_id" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
              <option value="">All Sections</option>
              <?php foreach ($sections as $sec): ?>
                <option value="<?php echo $sec->section_id; ?>" <?php echo ($filters['section_id'] == $sec->section_id) ? 'selected' : ''; ?>>
                  <?php echo html_escape($sec->class_name . ' ' . $sec->section_name); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-2 border-t border-outline-variant/40">
          <!-- Attendance Type -->
          <div>
            <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Attendance Type</label>
            <select name="attendance_type" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
              <option value="">All Types (Daily & Period-wise)</option>
              <option value="Daily" <?php echo ($filters['attendance_type'] === 'Daily') ? 'selected' : ''; ?>>Daily Only</option>
              <option value="Period-wise" <?php echo ($filters['attendance_type'] === 'Period-wise') ? 'selected' : ''; ?>>Period-wise Only</option>
            </select>
          </div>

          <!-- Status -->
          <div>
            <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Status</label>
            <select name="attendance_status" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
              <option value="">All Statuses</option>
              <option value="Present" <?php echo ($filters['attendance_status'] === 'Present') ? 'selected' : ''; ?>>Present</option>
              <option value="Absent" <?php echo ($filters['attendance_status'] === 'Absent') ? 'selected' : ''; ?>>Absent</option>
              <option value="Late" <?php echo ($filters['attendance_status'] === 'Late') ? 'selected' : ''; ?>>Late</option>
              <option value="Excused" <?php echo ($filters['attendance_status'] === 'Excused') ? 'selected' : ''; ?>>Excused / Leave</option>
            </select>
          </div>

          <!-- From Date -->
          <div>
            <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">From Date</label>
            <input type="date" name="from_date" value="<?php echo html_escape($filters['from_date']); ?>" min="<?php echo html_escape($current_year->start_date ?? ''); ?>" max="<?php echo html_escape($current_year->end_date ?? ''); ?>" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
          </div>

          <!-- To Date & Submit -->
          <div>
            <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">To Date</label>
            <div class="flex items-center gap-2">
              <input type="date" name="to_date" value="<?php echo html_escape($filters['to_date']); ?>" min="<?php echo html_escape($current_year->start_date ?? ''); ?>" max="<?php echo html_escape($current_year->end_date ?? ''); ?>" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
              <button type="submit" class="px-4 py-2 rounded-lg bg-primary text-on-primary text-label-md font-semibold hover:bg-primary/90 transition-colors shrink-0 cursor-pointer">
                Filter
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>

    <!-- Results Table -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden mb-6">
      <div class="p-4 border-b border-outline-variant/50 flex items-center justify-between">
        <span class="text-body-md font-medium text-on-surface">Showing <strong><?php echo count($records); ?></strong> of <strong><?php echo $total_count; ?></strong> records</span>
      </div>

      <div class="table-scroll overflow-x-auto">
        <table class="w-full data-table zebra border-collapse">
          <thead>
            <tr class="border-b border-outline-variant/60 bg-surface-container-low/50">
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Date</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Student</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Class & Section</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Type / Period</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Status</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Remarks</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Marked By</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/40">
            <?php if (empty($records)): ?>
              <tr><td colspan="8" class="px-4 py-8 text-center text-on-surface-variant text-body-md">No attendance history records match your search criteria.</td></tr>
            <?php else: ?>
              <?php foreach ($records as $r): ?>
                <?php
                  $fullName = trim($r->first_name . ' ' . $r->last_name);
                  $badgeClass = 'bg-surface-container-high text-on-surface-variant';
                  if ($r->attendance_status === 'Present') $badgeClass = 'bg-secondary-container text-on-secondary-container';
                  elseif ($r->attendance_status === 'Absent') $badgeClass = 'bg-error-container text-on-error-container';
                  elseif ($r->attendance_status === 'Late') $badgeClass = 'bg-amber-100 text-amber-900';
                  elseif (in_array($r->attendance_status, array('Excused', 'Leave'))) $badgeClass = 'bg-primary-fixed text-on-primary-fixed';
                ?>
                <tr class="hover:bg-surface-container-low transition-colors">
                  <!-- Date -->
                  <td class="px-4 py-3 font-mono font-medium text-on-surface whitespace-nowrap">
                    <?php echo date('d M Y', strtotime($r->attendance_date)); ?>
                  </td>

                  <!-- Student -->
                  <td class="px-4 py-3 whitespace-nowrap">
                    <div class="font-semibold text-on-surface"><?php echo html_escape($fullName); ?></div>
                    <div class="text-[12px] text-on-surface-variant"><?php echo html_escape($r->admission_number); ?><?php if ($r->roll_number): ?> · Roll #<?php echo html_escape($r->roll_number); ?><?php endif; ?></div>
                  </td>

                  <!-- Class & Section -->
                  <td class="px-4 py-3 text-body-md text-on-surface-variant whitespace-nowrap">
                    <?php echo html_escape($r->class_name . ' ' . $r->section_name); ?>
                  </td>

                  <!-- Type / Period -->
                  <td class="px-4 py-3 whitespace-nowrap text-body-md">
                    <span class="inline-flex items-center gap-1 font-medium text-on-surface">
                      <?php if ($r->attendance_type === 'Period-wise'): ?>
                        <span class="material-symbols-outlined text-[16px] text-primary">schedule</span>
                        <?php echo html_escape($r->period_name ?: 'Period ' . $r->period_number); ?>
                      <?php else: ?>
                        <span class="material-symbols-outlined text-[16px] text-secondary">today</span>Daily
                      <?php endif; ?>
                    </span>
                  </td>

                  <!-- Status -->
                  <td class="px-4 py-3 text-center whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[12px] font-semibold <?php echo $badgeClass; ?>">
                      <?php echo html_escape($r->attendance_status); ?>
                    </span>
                  </td>

                  <!-- Remarks -->
                  <td class="px-4 py-3 text-body-md text-on-surface-variant max-w-[200px] truncate">
                    <?php echo html_escape($r->remarks ?: '—'); ?>
                  </td>

                  <!-- Marked By -->
                  <td class="px-4 py-3 text-body-md text-on-surface-variant whitespace-nowrap">
                    <?php echo html_escape($r->marked_by_name ?: 'System'); ?>
                  </td>

                  <!-- Actions (Edit Record Modal) -->
                  <td class="px-4 py-3 text-center whitespace-nowrap">
                    <button type="button" onclick='openEditRecordModal(<?php echo json_encode($r); ?>)' class="p-1.5 rounded-lg hover:bg-surface-container-high text-primary transition-colors cursor-pointer" title="Edit Record">
                      <span class="material-symbols-outlined text-[18px]">edit</span>
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <?php if ($total_count > $limit): ?>
        <div class="flex items-center justify-between px-4 py-3 border-t border-outline-variant/50 text-body-md text-on-surface-variant">
          <span>Page <?php echo $page; ?> of <?php echo ceil($total_count / $limit); ?></span>
          <div class="flex items-center gap-1">
            <?php if ($page > 1): ?>
              <a href="?<?php echo http_build_query(array_merge($_GET, array('page' => $page - 1))); ?>" class="px-3 py-1.5 rounded-lg border border-outline-variant hover:bg-surface-container-high">Previous</a>
            <?php endif; ?>
            <?php if ($page * $limit < $total_count): ?>
              <a href="?<?php echo http_build_query(array_merge($_GET, array('page' => $page + 1))); ?>" class="px-3 py-1.5 rounded-lg border border-outline-variant hover:bg-surface-container-high">Next</a>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <!-- EDIT RECORD MODAL -->
    <div id="edit-record-modal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm hidden items-center justify-center p-4">
      <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant max-w-md w-full p-6 elevation-3 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-outline-variant/50">
          <h3 class="font-headline-md text-title-lg text-on-surface font-semibold flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-[22px]">edit</span>Edit Attendance Record
          </h3>
          <button onclick="closeEditRecordModal()" class="p-1 rounded-lg hover:bg-surface-container-high text-on-surface-variant cursor-pointer">
            <span class="material-symbols-outlined text-[20px]">close</span>
          </button>
        </div>

        <?php echo form_open('attendance/history?' . $_SERVER['QUERY_STRING']); ?>
          <input type="hidden" name="action" value="edit_record"/>
          <input type="hidden" name="attendance_id" id="edit-att-id"/>

          <div class="space-y-3.5">
            <div>
              <div class="text-[12px] text-on-surface-variant">Student</div>
              <div class="font-semibold text-on-surface text-body-md" id="edit-att-student">Student Name</div>
            </div>

            <div class="grid grid-cols-2 gap-3 text-[13px]">
              <div>
                <span class="text-on-surface-variant">Date: </span>
                <strong id="edit-att-date" class="text-on-surface font-mono">Date</strong>
              </div>
              <div>
                <span class="text-on-surface-variant">Type: </span>
                <strong id="edit-att-type" class="text-on-surface">Daily</strong>
              </div>
            </div>

            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Attendance Status *</label>
              <select name="attendance_status" id="edit-att-status" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <option value="Present">Present</option>
                <option value="Absent">Absent</option>
                <option value="Late">Late</option>
                <option value="Excused">Excused</option>
              </select>
            </div>

            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Remarks / Reason</label>
              <input type="text" name="remarks" id="edit-att-remarks" placeholder="Notes..." class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
            </div>
          </div>

          <div class="flex items-center justify-end gap-2 pt-4 border-t border-outline-variant/50 mt-5">
            <button type="button" onclick="closeEditRecordModal()" class="px-4 py-2 rounded-lg border border-outline-variant text-on-surface-variant hover:bg-surface-container-high text-label-md cursor-pointer">Cancel</button>
            <button type="submit" class="px-5 py-2 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold hover:bg-on-secondary-fixed-variant transition-colors cursor-pointer">Update Record</button>
          </div>
        <?php echo form_close(); ?>
      </div>
    </div>

    <!-- Modal Script -->
    <script>
      function openEditRecordModal(record) {
        document.getElementById('edit-att-id').value = record.attendance_id;
        document.getElementById('edit-att-student').textContent = record.first_name + ' ' + record.last_name + ' (' + record.admission_number + ')';
        document.getElementById('edit-att-date').textContent = record.attendance_date;
        document.getElementById('edit-att-type').textContent = record.attendance_type + (record.period_name ? ' (' + record.period_name + ')' : '');
        document.getElementById('edit-att-status').value = (record.attendance_status === 'Leave') ? 'Excused' : record.attendance_status;
        document.getElementById('edit-att-remarks').value = record.remarks || '';

        var modal = document.getElementById('edit-record-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
      }

      function closeEditRecordModal() {
        var modal = document.getElementById('edit-record-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
      }
    </script>
