<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

    <!-- Flash Messages -->
    <?php if ($this->session->flashdata('success')): ?>
      <div class="mb-4 p-3.5 rounded-xl bg-secondary-container text-on-secondary-container text-body-md font-medium flex items-center gap-2 border border-secondary/20">
        <span class="material-symbols-outlined text-[20px] text-secondary">check_circle</span>
        <?php echo html_escape($this->session->flashdata('success')); ?>
      </div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('warning')): ?>
      <div class="mb-4 p-3.5 rounded-xl bg-amber-50 text-amber-800 text-body-md font-medium flex items-center gap-2 border border-amber-200">
        <span class="material-symbols-outlined text-[20px] text-amber-600">warning</span>
        <?php echo html_escape($this->session->flashdata('warning')); ?>
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
        <h2 class="font-headline-md text-headline-md text-on-surface">Fee Structure Management</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Configure class-wise fee schedules, amounts, and payment due dates.</p>
      </div>
      <div class="flex items-center gap-2 flex-wrap shrink-0">
        <button type="button" onclick="openStructureModal()" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer">
          <span class="material-symbols-outlined text-[18px]">add_circle</span>Add Fee Structure
        </button>
      </div>
    </div>

    <!-- Filter Bar -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4 mb-6">
      <form method="get" action="<?php echo site_url('fees/structures'); ?>" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Class</label>
          <select name="class_id" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
            <option value="">All Classes</option>
            <?php foreach ($classes as $c): ?>
              <option value="<?php echo $c->class_id; ?>" <?php echo ($filters['class_id'] == $c->class_id) ? 'selected' : ''; ?>>
                <?php echo html_escape($c->class_name); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Fee Category</label>
          <select name="fee_head_id" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?php echo $cat->fee_head_id; ?>" <?php echo ($filters['fee_head_id'] == $cat->fee_head_id) ? 'selected' : ''; ?>>
                <?php echo html_escape($cat->head_name); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Academic Year</label>
          <select name="academic_year_id" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
            <option value="">All Academic Years</option>
            <?php foreach ($academic_years as $ay): ?>
              <option value="<?php echo $ay->academic_year_id; ?>" <?php echo ($filters['academic_year_id'] == $ay->academic_year_id) ? 'selected' : ''; ?>>
                <?php echo html_escape($ay->year_name); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </form>
    </div>

    <!-- Fee Structures Table -->
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden mb-6">
      <div class="p-4 border-b border-outline-variant/50 flex items-center justify-between">
        <span class="text-body-md font-semibold text-on-surface">Configured Fee Structures (<?php echo count($structures); ?>)</span>
      </div>

      <div class="table-scroll overflow-x-auto">
        <table class="w-full data-table zebra border-collapse text-body-md">
          <thead>
            <tr class="border-b border-outline-variant/60 bg-surface-container-low/50">
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Class & Division</th>
              <th class="text-left px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Fee Category</th>
              <th class="text-right px-4 py-3 text-label-md font-semibold text-secondary uppercase whitespace-nowrap">Amount</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Frequency</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Due Date</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Session / Group</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Status</th>
              <th class="text-center px-4 py-3 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/40">
            <?php if (empty($structures)): ?>
              <tr><td colspan="8" class="px-4 py-8 text-center text-on-surface-variant">No fee structures found for the selected filters.</td></tr>
            <?php else: ?>
              <?php foreach ($structures as $s): ?>
                <tr class="hover:bg-surface-container-low transition-colors">
                  <td class="px-4 py-3 whitespace-nowrap font-bold text-on-surface">
                    <?php echo html_escape($s->class_name ?: 'General'); ?>
                    <?php if (!empty($s->division_name)): ?>
                      <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-secondary/10 text-secondary border border-secondary/20 ml-1.5">
                        Div <?php echo html_escape($s->division_name); ?>
                      </span>
                    <?php endif; ?>
                  </td>
                  <td class="px-4 py-3 whitespace-nowrap">
                    <span class="font-semibold text-on-surface"><?php echo html_escape($s->category_name); ?></span>
                    <?php if ($s->category_code): ?>
                      <span class="text-[11px] font-mono text-on-surface-variant block"><?php echo html_escape($s->category_code); ?></span>
                    <?php endif; ?>
                  </td>
                  <td class="px-4 py-3 text-right font-mono font-bold text-secondary whitespace-nowrap text-base">
                    ₹<?php echo number_format($s->amount, 2); ?>
                  </td>
                  <td class="px-4 py-3 text-center whitespace-nowrap">
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-surface-container-high text-on-surface">
                      <?php echo html_escape($s->frequency); ?>
                    </span>
                  </td>
                  <td class="px-4 py-3 text-center font-mono text-[13px] text-on-surface whitespace-nowrap font-medium">
                    <?php echo date('d M Y', strtotime($s->due_date)); ?>
                  </td>
                  <td class="px-4 py-3 text-center whitespace-nowrap">
                    <span class="font-medium text-on-surface text-[13px] block">
                      <?php echo html_escape($s->group_name ?: '—'); ?>
                    </span>
                    <span class="text-[11px] font-mono text-on-surface-variant block">
                      <?php echo html_escape($s->year_name ?: ''); ?>
                    </span>
                  </td>
                  <td class="px-4 py-3 text-center whitespace-nowrap">
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold <?php echo ($s->status == 1) ? 'bg-secondary-container text-on-secondary-container' : 'bg-surface-container-high text-on-surface-variant'; ?>">
                      <?php echo ($s->status == 1) ? 'Active' : 'Inactive'; ?>
                    </span>
                  </td>
                  <td class="px-4 py-3 text-center whitespace-nowrap">
                    <div class="flex items-center justify-center gap-1">
                      <!-- Edit -->
                      <button type="button" onclick='editStructureModal(<?php echo json_encode($s); ?>)' class="p-1.5 rounded-lg hover:bg-surface-container-high text-on-surface-variant transition-colors cursor-pointer" title="Edit">
                        <span class="material-symbols-outlined text-[18px]">edit</span>
                      </button>

                      <!-- Delete -->
                      <?php echo form_open('fees/structures', array('class' => 'inline', 'onsubmit' => 'return confirm("Delete this fee structure?");')); ?>
                        <input type="hidden" name="action" value="delete"/>
                        <input type="hidden" name="fee_structure_id" value="<?php echo $s->fee_structure_id; ?>"/>
                        <button type="submit" class="p-1.5 rounded-lg hover:bg-error-container text-error transition-colors cursor-pointer" title="Delete">
                          <span class="material-symbols-outlined text-[18px]">delete</span>
                        </button>
                      <?php echo form_close(); ?>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- CREATE / EDIT STRUCTURE MODAL -->
    <div id="structure-modal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm hidden items-center justify-center p-4">
      <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant max-w-lg w-full p-6 elevation-3 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-outline-variant/50">
          <h3 id="modal-struct-title" class="font-headline-md text-title-lg text-on-surface font-semibold flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-[22px]">tune</span>Add Fee Structure
          </h3>
          <button onclick="closeStructureModal()" class="p-1 rounded-lg hover:bg-surface-container-high text-on-surface-variant cursor-pointer">
            <span class="material-symbols-outlined text-[20px]">close</span>
          </button>
        </div>

        <?php echo form_open('fees/structures', array('class' => 'space-y-4')); ?>
          <input type="hidden" name="fee_structure_id" id="modal-struct-id" value="0"/>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Academic Year *</label>
              <select name="academic_year_id" id="modal-struct-year" required class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <?php foreach ($academic_years as $ay): ?>
                  <option value="<?php echo $ay->academic_year_id; ?>"><?php echo html_escape($ay->year_name); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Session / Group *</label>
              <select name="academic_group_id" id="modal-struct-group" onchange="onStructGroupChange(this.value)" required class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <option value="">-- Choose Session / Group --</option>
                <?php foreach ($academic_groups as $ag): ?>
                  <option value="<?php echo $ag->academic_group_id; ?>"><?php echo html_escape($ag->group_name); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Class *</label>
              <select name="class_id" id="modal-struct-class" onchange="onStructClassChange(this.value)" required class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary" disabled>
                <option value="">-- Choose Session First --</option>
              </select>
            </div>
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Division *</label>
              <select name="division_id" id="modal-struct-division" required class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary" disabled>
                <option value="">-- Choose Class First --</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Fee Category *</label>
            <select name="fee_head_id" id="modal-struct-category" required class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
              <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat->fee_head_id; ?>"><?php echo html_escape($cat->head_name); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Fee Amount (₹) *</label>
              <input type="number" step="0.5" min="1" name="amount" id="modal-struct-amount" required placeholder="0.00" class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-mono font-bold focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
            </div>
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Frequency *</label>
              <select name="frequency" id="modal-struct-frequency" required class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <option value="One Time">One Time</option>
                <option value="Monthly">Monthly</option>
                <option value="Quarterly">Quarterly</option>
                <option value="Half Yearly">Half Yearly</option>
                <option value="Yearly" selected>Yearly</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block font-label-md text-label-md text-on-surface mb-1 font-medium">Payment Due Date *</label>
            <input type="date" name="due_date" id="modal-struct-duedate" required class="w-full px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
          </div>

          <label class="flex items-center gap-2 text-body-md text-on-surface cursor-pointer">
            <input type="checkbox" name="status" id="modal-struct-status" value="1" checked class="w-4 h-4 rounded text-secondary focus:ring-secondary"/>
            <span>Active Fee Structure</span>
          </label>

          <div class="flex items-center justify-end gap-2 pt-4 border-t border-outline-variant/50">
            <button type="button" onclick="closeStructureModal()" class="px-4 py-2 rounded-lg bg-surface-container-high text-on-surface text-label-md font-medium hover:bg-surface-container-highest cursor-pointer">
              Cancel
            </button>
            <button type="submit" class="px-5 py-2 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold hover:bg-on-secondary-fixed-variant cursor-pointer">
              Save Structure
            </button>
          </div>
        <?php echo form_close(); ?>
      </div>
    </div>

    <script>
      var ajaxClassesUrl = "<?php echo site_url('fees/ajax_get_classes_by_group'); ?>";
      var ajaxDivisionsUrl = "<?php echo site_url('fees/ajax_get_divisions_for_structure'); ?>";

      function onStructGroupChange(groupId, selectedClassId, callback) {
        var yearId = document.getElementById('modal-struct-year').value;
        var classSelect = document.getElementById('modal-struct-class');
        var divSelect = document.getElementById('modal-struct-division');
        var isEdit = document.getElementById('modal-struct-id').value !== '0';

        divSelect.innerHTML = '<option value="">-- Choose Class First --</option>';
        divSelect.disabled = true;

        if (!groupId) {
          classSelect.innerHTML = '<option value="">-- Choose Session First --</option>';
          classSelect.disabled = true;
          return;
        }

        classSelect.disabled = true;
        classSelect.innerHTML = '<option value="">Loading classes...</option>';

        fetch(ajaxClassesUrl + '?academic_group_id=' + encodeURIComponent(groupId) + '&academic_year_id=' + encodeURIComponent(yearId))
          .then(function(res) { return res.json(); })
          .then(function(data) {
            classSelect.innerHTML = '<option value="">-- Choose Class --</option>';
            if (!isEdit) {
              classSelect.innerHTML += '<option value="all">All Classes</option>';
            }
            if (data && data.classes && data.classes.length > 0) {
              data.classes.forEach(function(cls) {
                var opt = document.createElement('option');
                opt.value = cls.class_id;
                opt.textContent = cls.class_name;
                classSelect.appendChild(opt);
              });
            }
            classSelect.disabled = false;
            if (selectedClassId) {
              classSelect.value = selectedClassId;
            }
            if (typeof callback === 'function') {
              callback();
            }
          })
          .catch(function(err) {
            console.error('Error fetching classes:', err);
            classSelect.innerHTML = '<option value="">-- Error loading classes --</option>';
          });
      }

      function onStructClassChange(classVal, selectedDivId) {
        var groupVal = document.getElementById('modal-struct-group').value;
        var yearId = document.getElementById('modal-struct-year').value;
        var divSelect = document.getElementById('modal-struct-division');
        var isEdit = document.getElementById('modal-struct-id').value !== '0';

        if (!classVal || !groupVal) {
          divSelect.innerHTML = '<option value="">-- Choose Class First --</option>';
          divSelect.disabled = true;
          return;
        }

        divSelect.disabled = true;
        divSelect.innerHTML = '<option value="">Loading divisions...</option>';

        var url = ajaxDivisionsUrl + '?academic_group_id=' + encodeURIComponent(groupVal) + '&class_id=' + encodeURIComponent(classVal) + '&academic_year_id=' + encodeURIComponent(yearId);
        fetch(url)
          .then(function(res) { return res.json(); })
          .then(function(data) {
            divSelect.innerHTML = '<option value="">-- Choose Division --</option>';
            if (!isEdit) {
              divSelect.innerHTML += '<option value="all">All Divisions</option>';
            }
            if (data && data.divisions && data.divisions.length > 0) {
              data.divisions.forEach(function(d) {
                var opt = document.createElement('option');
                opt.value = d.division_id;
                opt.textContent = d.division_name;
                divSelect.appendChild(opt);
              });
            }
            divSelect.disabled = false;
            if (selectedDivId) {
              divSelect.value = selectedDivId;
            }
          })
          .catch(function(err) {
            console.error('Error fetching divisions:', err);
            divSelect.innerHTML = '<option value="">-- Error loading divisions --</option>';
          });
      }

      function openStructureModal() {
        document.getElementById('modal-struct-id').value = '0';
        document.getElementById('modal-struct-title').innerHTML = '<span class="material-symbols-outlined text-primary text-[22px]">tune</span>Add Fee Structure';
        document.getElementById('modal-struct-group').value = '';
        document.getElementById('modal-struct-group').disabled = false;
        document.getElementById('modal-struct-class').innerHTML = '<option value="">-- Choose Session First --</option>';
        document.getElementById('modal-struct-class').disabled = true;
        document.getElementById('modal-struct-division').innerHTML = '<option value="">-- Choose Class First --</option>';
        document.getElementById('modal-struct-division').disabled = true;
        document.getElementById('modal-struct-amount').value = '';
        document.getElementById('modal-struct-frequency').value = 'Yearly';
        document.getElementById('modal-struct-duedate').value = '2026-09-15';
        document.getElementById('modal-struct-status').checked = true;

        var modal = document.getElementById('structure-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
      }

      function editStructureModal(item) {
        document.getElementById('modal-struct-id').value = item.fee_structure_id;
        document.getElementById('modal-struct-title').innerHTML = '<span class="material-symbols-outlined text-primary text-[22px]">edit</span>Edit Fee Structure';
        document.getElementById('modal-struct-year').value = item.academic_year_id;
        document.getElementById('modal-struct-category').value = item.fee_head_id;
        document.getElementById('modal-struct-amount').value = item.amount;
        document.getElementById('modal-struct-frequency').value = item.frequency || 'Yearly';
        document.getElementById('modal-struct-duedate').value = item.due_date;
        document.getElementById('modal-struct-status').checked = (item.status == 1);

        if (item.academic_group_id) {
          document.getElementById('modal-struct-group').value = item.academic_group_id;
          onStructGroupChange(item.academic_group_id, item.class_id, function() {
            onStructClassChange(item.class_id, item.division_id || '');
          });
        } else {
          document.getElementById('modal-struct-group').value = '';
          document.getElementById('modal-struct-class').innerHTML = '<option value="">-- Choose Session First --</option>';
          document.getElementById('modal-struct-class').disabled = true;
          document.getElementById('modal-struct-division').innerHTML = '<option value="">-- Choose Class First --</option>';
          document.getElementById('modal-struct-division').disabled = true;
        }

        var modal = document.getElementById('structure-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
      }

      function closeStructureModal() {
        var modal = document.getElementById('structure-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
      }
    </script>
