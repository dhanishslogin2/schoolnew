<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Include Client-Side Export Libraries -->
<script src="<?php echo base_url('assets/vendor/html2canvas.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/vendor/jspdf.umd.min.js'); ?>"></script>

<div class="space-y-5">

  <!-- Flash Messages -->
  <?php if ($this->session->flashdata('success')): ?>
    <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-2 shadow-xs">
      <span class="material-symbols-outlined text-[20px] text-emerald-600">check_circle</span>
      <span class="font-medium"><?php echo html_escape($this->session->flashdata('success')); ?></span>
    </div>
  <?php endif; ?>
  <?php if ($this->session->flashdata('error')): ?>
    <div class="p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm flex items-center gap-2 shadow-xs">
      <span class="material-symbols-outlined text-[20px] text-red-600">error</span>
      <span class="font-medium"><?php echo html_escape($this->session->flashdata('error')); ?></span>
    </div>
  <?php endif; ?>

  <!-- Top Title & Navigation Bar -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-1 no-print">
    <div class="flex items-center gap-3">
      <span class="w-10 h-10 rounded-xl bg-emerald-800 text-white flex items-center justify-center shadow-xs">
        <span class="material-symbols-outlined text-[22px]">badge</span>
      </span>
      <div>
        <h2 class="font-bold text-xl text-slate-900 leading-tight">Student ID Card</h2>
        <div class="flex items-center gap-1.5 text-xs text-slate-500 mt-0.5">
          <span>Student Management</span>
          <span class="material-symbols-outlined text-[12px]">chevron_right</span>
          <span>Student Services</span>
          <span class="material-symbols-outlined text-[12px]">chevron_right</span>
          <span class="text-slate-800 font-medium">Student ID Card</span>
        </div>
      </div>
    </div>

    <!-- Top Right Action Buttons -->
    <div class="flex items-center gap-2.5">
      <button type="button" onclick="openSettingsModal()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition-colors shadow-2xs cursor-pointer">
        <span class="material-symbols-outlined text-[18px] text-slate-500">settings</span>
        <span>ID Card Settings</span>
      </button>
      <button type="button" onclick="openHistoryModal()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition-colors shadow-2xs cursor-pointer">
        <span class="material-symbols-outlined text-[18px] text-slate-500">history</span>
        <span>History</span>
      </button>
    </div>
  </div>

  <!-- Main Content Grid -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

    <!-- =========================================================================
         LEFT COLUMN: STUDENT SELECTION (4 COLS)
         ========================================================================= -->
    <div class="lg:col-span-4 xl:col-span-4 space-y-4">
      
      <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs space-y-4">
        
        <!-- Segmented Tab: Single Student / Bulk Students -->
        <div class="grid grid-cols-2 gap-1 p-1 bg-slate-100 rounded-xl border border-slate-200">
          <button type="button" id="tab-single-btn" onclick="toggleSelectionMode('single')" class="py-2 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1.5 bg-white text-emerald-800 shadow-xs">
            <span class="material-symbols-outlined text-[17px] text-emerald-700">person</span>
            <span>Single Student</span>
          </button>
          <button type="button" id="tab-bulk-btn" onclick="toggleSelectionMode('bulk')" class="py-2 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1.5 text-slate-600 hover:text-slate-900">
            <span class="material-symbols-outlined text-[17px]">groups</span>
            <span>Bulk Students</span>
          </button>
        </div>

        <!-- Single Student Search & List Mode -->
        <div id="single-select-wrapper" class="space-y-4">
          
          <!-- Search & Filter Controls -->
          <div class="space-y-2.5">
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Search Student</label>
            
            <!-- Class Dropdown -->
            <div class="relative">
              <select id="left-class-filter" onchange="filterStudentCards()" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-300 bg-white text-slate-800 focus:ring-2 focus:ring-emerald-600/20 focus:border-emerald-600 appearance-none font-medium pr-8">
                <option value="">All Classes</option>
                <?php foreach ($classes as $c): ?>
                  <option value="<?php echo $c->class_id; ?>" <?php echo ($selected_class == $c->class_id) ? 'selected' : ''; ?>><?php echo html_escape($c->class_name); ?></option>
                <?php endforeach; ?>
              </select>
              <span class="material-symbols-outlined absolute right-3 top-3 text-slate-400 text-[18px] pointer-events-none">expand_more</span>
            </div>

            <!-- Keyword Search Input + Button -->
            <div class="flex gap-2">
              <div class="relative flex-1">
                <input type="text" id="left-search-input" onkeyup="if(event.key === 'Enter') filterStudentCards()" placeholder="Search by name, admission no., roll no..." class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-300 bg-white text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-emerald-600/20 focus:border-emerald-600"/>
              </div>
              <button type="button" onclick="filterStudentCards()" class="p-2.5 rounded-xl bg-emerald-800 text-white hover:bg-emerald-900 transition-colors shadow-2xs flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[19px]">search</span>
              </button>
            </div>
          </div>

          <!-- Student List Header -->
          <div class="flex items-center justify-between pt-1">
            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Select Student</span>
            <span id="filtered-count-badge" class="text-xs font-medium text-slate-500"><?php echo count($students); ?> found</span>
          </div>

          <!-- Student Cards List -->
          <div id="student-cards-container" class="space-y-2 max-h-[380px] overflow-y-auto pr-1">
            <?php if (empty($students)): ?>
              <div class="p-8 text-center text-slate-400 text-sm bg-slate-50 rounded-xl border border-slate-200">
                No students found matching selection.
              </div>
            <?php else: ?>
              <?php foreach ($students as $idx => $st): ?>
                <?php
                  $fullName = trim($st->first_name . ' ' . ($st->middle_name ? $st->middle_name . ' ' : '') . $st->last_name);
                  $nameParts = explode(' ', $fullName);
                  $initials = '';
                  foreach ($nameParts as $np) { if (!empty($np)) $initials .= strtoupper($np[0]); }
                  $initials = substr($initials, 0, 2) ?: 'ST';
                  $classDisplay = trim(($st->class_name ?? '') . ($st->section_name ? ' - ' . $st->section_name : ''));
                  $isSelected = ($selected_student && $selected_student->student_id == $st->student_id) || (!$selected_student && $idx === 0);
                ?>
                <div onclick="selectStudentCard(<?php echo $st->student_id; ?>, this)" class="student-select-card p-3 rounded-xl border transition-all cursor-pointer flex items-center gap-3.5 <?php echo $isSelected ? 'border-emerald-600 bg-emerald-50/40 ring-1 ring-emerald-600 shadow-2xs' : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/60'; ?>" data-student-id="<?php echo $st->student_id; ?>" data-name="<?php echo strtolower(html_escape($fullName)); ?>" data-adm="<?php echo strtolower(html_escape($st->admission_number)); ?>" data-roll="<?php echo strtolower(html_escape($st->roll_number ?? '')); ?>" data-class="<?php echo $st->class_id; ?>" data-section="<?php echo $st->section_id; ?>">
                  
                  <!-- Custom Radio Dot Indicator -->
                  <div class="radio-indicator w-4 h-4 rounded-full border-2 flex items-center justify-center shrink-0 <?php echo $isSelected ? 'border-emerald-600 bg-white' : 'border-slate-300 bg-white'; ?>">
                    <div class="radio-dot w-2 h-2 rounded-full bg-emerald-600 <?php echo $isSelected ? '' : 'hidden'; ?>"></div>
                  </div>

                  <!-- Thumbnail Avatar -->
                  <div class="w-10 h-10 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center shrink-0 overflow-hidden shadow-2xs font-bold text-xs text-emerald-800">
                    <?php if (!empty($st->photo) && file_exists(FCPATH . 'uploads/students/' . $st->photo)): ?>
                      <img src="<?php echo base_url('uploads/students/' . $st->photo); ?>" alt="<?php echo html_escape($fullName); ?>" class="w-full h-full object-cover"/>
                    <?php else: ?>
                      <span><?php echo html_escape($initials); ?></span>
                    <?php endif; ?>
                  </div>

                  <!-- Details -->
                  <div class="min-w-0 flex-1">
                    <div class="font-bold text-slate-900 text-sm truncate leading-snug"><?php echo html_escape($fullName); ?></div>
                    <div class="text-xs text-slate-500 font-mono mt-0.5 truncate">
                      <span>Adm No: <?php echo html_escape($st->admission_number); ?></span>
                    </div>
                    <div class="text-xs text-slate-600 font-medium mt-0.5 truncate">
                      <span><?php echo html_escape($classDisplay ?: 'Grade 10 - A'); ?></span>
                    </div>
                  </div>

                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>

          <!-- Pagination Bar -->
          <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-xs">
            <button type="button" id="prev-page-btn" onclick="paginateStudents(-1)" class="px-2.5 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors font-medium">
              &laquo; Prev
            </button>
            <div id="pagination-pills" class="flex items-center gap-1 font-semibold">
              <!-- Rendered via JS -->
            </div>
            <button type="button" id="next-page-btn" onclick="paginateStudents(1)" class="px-2.5 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors font-medium">
              Next &raquo;
            </button>
          </div>

        </div>

        <!-- Bulk Students Mode -->
        <div id="bulk-select-wrapper" class="space-y-4 hidden">
          <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-200 text-xs text-emerald-900">
            <span class="font-bold">Bulk Mode Active:</span> Select students using the checkboxes below to generate, print, or download batch cards.
          </div>

          <div class="flex items-center justify-between text-xs">
            <div class="flex items-center gap-2">
              <input type="checkbox" id="bulk-master-cb" onchange="toggleAllBulkCbs(this)" class="w-4 h-4 rounded text-emerald-600 border-slate-300 focus:ring-emerald-600"/>
              <label for="bulk-master-cb" class="font-bold text-slate-700 cursor-pointer">Select All</label>
            </div>
            <span class="text-slate-500 font-medium"><span id="bulk-checked-count">0</span> selected</span>
          </div>

          <div class="space-y-2 max-h-[360px] overflow-y-auto pr-1">
            <?php foreach ($students as $st): ?>
              <?php
                $fullName = trim($st->first_name . ' ' . ($st->middle_name ? $st->middle_name . ' ' : '') . $st->last_name);
                $classDisplay = trim(($st->class_name ?? '') . ($st->section_name ? ' - ' . $st->section_name : ''));
              ?>
              <div class="p-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition-colors flex items-center gap-3 text-xs">
                <input type="checkbox" value="<?php echo $st->student_id; ?>" class="bulk-cb w-4 h-4 rounded text-emerald-600 border-slate-300 focus:ring-emerald-600" onchange="updateBulkCount()"/>
                <div class="min-w-0 flex-1">
                  <div class="font-bold text-slate-900 truncate"><?php echo html_escape($fullName); ?></div>
                  <div class="text-[11px] text-slate-500 font-mono truncate"><?php echo html_escape($st->admission_number); ?> · <?php echo html_escape($classDisplay); ?></div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <div class="flex gap-2 pt-2">
            <button type="button" onclick="executeBulkPrint()" class="flex-1 py-2.5 rounded-xl bg-emerald-800 text-white font-bold text-xs hover:bg-emerald-900 transition-colors flex items-center justify-center gap-1.5 shadow-2xs">
              <span class="material-symbols-outlined text-[17px]">print</span>
              <span>Print Selected</span>
            </button>
            <button type="button" onclick="executeBulkPdf()" class="flex-1 py-2.5 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition-colors flex items-center justify-center gap-1.5 shadow-2xs">
              <span class="material-symbols-outlined text-[17px]">picture_as_pdf</span>
              <span>Bulk PDF</span>
            </button>
          </div>
        </div>

      </div>

    </div>

    <!-- =========================================================================
         RIGHT COLUMN: LIVE CR80 PORTRAIT CARD PREVIEW (FRONT & BACK SIDE-BY-SIDE)
         ========================================================================= -->
    <div class="lg:col-span-8 xl:col-span-8 space-y-4">
      
      <!-- ID Card Preview Box -->
      <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs space-y-5">
        
        <!-- Header with Front/Back Switch -->
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
          <div class="flex items-center gap-2">
            <h3 class="font-bold text-base text-slate-900">ID Card Preview</h3>
            <span id="preview-ver-pill" class="px-2 py-0.5 rounded text-[11px] font-mono bg-slate-100 text-slate-700 font-bold">v1</span>
          </div>

          <!-- Front / Back Toggle Buttons -->
          <div class="flex items-center p-1 bg-slate-100 rounded-xl border border-slate-200">
            <button type="button" id="btn-toggle-front" onclick="filterCardView('front')" class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all bg-emerald-800 text-white shadow-xs">
              Front
            </button>
            <button type="button" id="btn-toggle-back" onclick="filterCardView('back')" class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all text-slate-600 hover:text-slate-900">
              Back
            </button>
            <button type="button" id="btn-toggle-both" onclick="filterCardView('both')" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all text-slate-600 hover:text-slate-900 hidden sm:inline-block">
              Both
            </button>
          </div>
        </div>

        <!-- Preview Stage: Side-by-Side Front & Back with Dimension Line Indicators -->
        <div class="py-6 px-3 flex flex-wrap items-center justify-center gap-10 bg-slate-50/70 rounded-2xl border border-dashed border-slate-200/90 overflow-hidden">
          
          <!-- ================= FRONT SIDE CR80 (2.125" x 3.375") ================= -->
          <div id="front-card-stage-wrapper" class="flex flex-col items-center">
            
            <!-- Top Dimension Label (2.125 inch / 54 mm) -->
            <div class="w-[280px] flex items-center justify-between text-[11px] font-semibold text-slate-600 mb-2 px-1">
              <span class="text-slate-400">&bull;</span>
              <span class="border-b border-dashed border-slate-400 flex-1 mx-2 text-center pb-0.5">2.125 inch (54 mm)</span>
              <span class="text-slate-400">&bull;</span>
            </div>

            <!-- Card + Left Height Dimension Wrapper -->
            <div class="flex items-center gap-3">
              
              <!-- Left Dimension Indicator (3.375 inch / 86 mm) -->
              <div class="h-[445px] flex flex-col items-center justify-between text-[11px] font-semibold text-slate-600 py-1 select-none">
                <span class="text-slate-400">&bull;</span>
                <span class="writing-mode-vertical border-l border-dashed border-slate-400 h-full mx-1 flex items-center justify-center text-center pl-1 text-[10px]" style="writing-mode: vertical-rl; transform: rotate(180deg);">3.375 inch (86 mm)</span>
                <span class="text-slate-400">&bull;</span>
              </div>

              <!-- FRONT CARD CONTAINER -->
              <div id="portrait-cr80-front" class="relative select-none shadow-xl transition-all duration-300 bg-white" style="width: 280px; height: 445px; aspect-ratio: 2.125 / 3.375; border-radius: 18px; overflow: hidden; border: 1px solid #cbd5e1;">
                
                <!-- Lanyard Slot Accent Hole -->
                <div class="absolute top-2 left-1/2 -translate-x-1/2 w-14 h-2.5 bg-white border border-slate-300/90 rounded-full z-20 shadow-inner"></div>

                <!-- Organic Decorative Waves (SVG) -->
                <svg class="absolute inset-0 w-full h-full pointer-events-none z-1" viewBox="0 0 280 445" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <!-- Top Orange Wave -->
                  <path d="M0 0C75 0 185 24 280 12V52C200 64 90 38 0 58V0Z" fill="#ea580c"/>
                  <!-- Top Green Wave -->
                  <path d="M0 0C60 0 120 36 280 24V0H0Z" fill="#006c4a"/>
                  <!-- Decorative Accent Dots -->
                  <circle cx="258" cy="158" r="5" fill="#ea580c"/>
                  <circle cx="264" cy="172" r="5" fill="#006c4a"/>
                  <circle cx="260" cy="188" r="7" fill="#fed7aa"/>
                  <!-- Bottom Orange Wave -->
                  <path d="M280 445C195 445 85 422 0 435V392C75 378 185 404 280 384V445Z" fill="#ea580c"/>
                  <!-- Bottom Green Wave -->
                  <path d="M280 445C215 445 155 406 0 420V445H280Z" fill="#006c4a"/>
                </svg>

                <!-- Front Content Layer -->
                <div class="relative z-10 h-full flex flex-col justify-between p-4 pt-6 pb-3">
                  
                  <!-- School Header (Logo + Public School text) -->
                  <div class="flex flex-col items-center justify-center pt-1 text-center">
                    <div class="h-10 flex items-center justify-center max-w-[140px]">
                      <img id="dom-school-logo" src="<?php echo base_url('assets/logo.png'); ?>" alt="Login2 Logo" class="max-h-9 max-w-[130px] object-contain"/>
                    </div>
                    <div id="dom-school-subtitle" class="font-extrabold text-[10px] text-emerald-800 tracking-widest uppercase mt-0.5 leading-tight">
                      <?php echo html_escape($settings->card_title ?? 'PUBLIC SCHOOL'); ?>
                    </div>
                  </div>

                  <!-- Central Student Photo -->
                  <div class="my-0.5 mx-auto">
                    <div class="w-28 h-33 rounded-xl border-2 border-orange-600 p-0.5 bg-white shadow-md overflow-hidden flex items-center justify-center">
                      <img id="dom-student-photo" src="" alt="Student Photo" class="w-full h-full object-cover rounded-lg hidden"/>
                      <div id="dom-student-photo-fallback" class="w-full h-full bg-slate-100 rounded-lg flex items-center justify-center font-extrabold text-xl text-emerald-800">
                        ST
                      </div>
                    </div>
                  </div>

                  <!-- Student ID Pill Badge -->
                  <div class="mx-auto bg-white border border-orange-600 rounded-full px-3 py-0.5 shadow-2xs">
                    <span class="text-[9px] font-extrabold text-orange-600 tracking-wider uppercase">STUDENT ID : </span>
                    <span id="dom-student-adm" class="text-[9px] font-black text-orange-600 font-mono">EDU2026015</span>
                  </div>

                  <!-- Student Full Name -->
                  <div id="dom-student-name" class="text-center font-black text-[15px] text-emerald-900 tracking-wide uppercase leading-tight truncate px-1">
                    TEST NEW
                  </div>

                  <!-- Aligned Details Grid Table -->
                  <div class="px-2">
                    <table class="w-full text-[9.5px] leading-tight">
                      <tr id="row-guardian-name">
                        <td class="w-22 font-bold text-slate-700 py-0.5">Father's Name</td>
                        <td class="w-2.5 text-center text-slate-500 font-bold">:</td>
                        <td id="dom-student-father" class="font-extrabold text-slate-900 py-0.5 truncate">test</td>
                      </tr>
                      <tr>
                        <td class="font-bold text-slate-700 py-0.5">Class</td>
                        <td class="text-center text-slate-500 font-bold">:</td>
                        <td id="dom-student-class" class="font-extrabold text-slate-900 py-0.5 truncate">Grade 10</td>
                      </tr>
                      <tr>
                        <td class="font-bold text-slate-700 py-0.5">Section</td>
                        <td class="text-center text-slate-500 font-bold">:</td>
                        <td id="dom-student-section" class="font-extrabold text-slate-900 py-0.5 truncate">A</td>
                      </tr>
                      <tr id="row-roll-no">
                        <td class="font-bold text-slate-700 py-0.5">Roll No.</td>
                        <td class="text-center text-slate-500 font-bold">:</td>
                        <td id="dom-student-roll" class="font-extrabold text-slate-900 py-0.5 font-mono truncate">0125</td>
                      </tr>
                      <tr>
                        <td class="font-bold text-slate-700 py-0.5">DOB</td>
                        <td class="text-center text-slate-500 font-bold">:</td>
                        <td id="dom-student-dob" class="font-extrabold text-slate-900 py-0.5 truncate">15-06-2012</td>
                      </tr>
                      <tr id="row-blood-group">
                        <td class="font-bold text-slate-700 py-0.5">Blood Group</td>
                        <td class="text-center text-slate-500 font-bold">:</td>
                        <td id="dom-student-blood" class="font-black text-red-600 py-0.5">A+</td>
                      </tr>
                    </table>
                  </div>

                  <!-- Bottom spacer -->
                  <div class="h-1.5"></div>

                </div>

              </div>

            </div>

          </div>

          <!-- ================= BACK SIDE CR80 (2.125" x 3.375") ================= -->
          <div id="back-card-stage-wrapper" class="flex flex-col items-center">
            
            <!-- Top Dimension Label (2.125 inch / 54 mm) -->
            <div class="w-[280px] flex items-center justify-between text-[11px] font-semibold text-slate-600 mb-2 px-1">
              <span class="text-slate-400">&bull;</span>
              <span class="border-b border-dashed border-slate-400 flex-1 mx-2 text-center pb-0.5">2.125 inch (54 mm)</span>
              <span class="text-slate-400">&bull;</span>
            </div>

            <!-- Card + Right Height Dimension Wrapper -->
            <div class="flex items-center gap-3">
              
              <!-- BACK CARD CONTAINER -->
              <div id="portrait-cr80-back" class="relative select-none shadow-xl transition-all duration-300 bg-white" style="width: 280px; height: 445px; aspect-ratio: 2.125 / 3.375; border-radius: 18px; overflow: hidden; border: 1px solid #cbd5e1;">
                
                <!-- Lanyard Slot Accent Hole -->
                <div class="absolute top-2 left-1/2 -translate-x-1/2 w-14 h-2.5 bg-white border border-slate-300/90 rounded-full z-20 shadow-inner"></div>

                <!-- Organic Decorative Waves (SVG) -->
                <svg class="absolute inset-0 w-full h-full pointer-events-none z-1" viewBox="0 0 280 445" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M0 0C75 0 185 24 280 12V52C200 64 90 38 0 58V0Z" fill="#ea580c"/>
                  <path d="M0 0C60 0 120 36 280 24V0H0Z" fill="#006c4a"/>
                  <circle cx="254" cy="115" r="4.5" fill="#ea580c"/>
                  <circle cx="248" cy="128" r="6" fill="#fed7aa"/>
                  <circle cx="260" cy="130" r="4.5" fill="#006c4a"/>
                  <path d="M280 445C195 445 85 422 0 435V392C75 378 185 404 280 384V445Z" fill="#006c4a"/>
                  <path d="M280 445C215 445 155 406 0 420V445H280Z" fill="#ea580c"/>
                </svg>

                <!-- Back Content Layer -->
                <div class="relative z-10 h-full flex flex-col justify-between p-4 pt-9 pb-3 text-center">
                  
                  <!-- Contact Header & Phone -->
                  <div class="space-y-0.5">
                    <div class="text-[9.5px] font-black text-orange-600 tracking-widest uppercase">
                      CONTACT
                    </div>
                    <div class="flex items-center justify-center gap-1.5 font-black text-sm text-emerald-900 font-mono">
                      <span class="material-symbols-outlined text-[15px] text-emerald-700">call</span>
                      <span id="dom-back-main-phone"><?php echo html_escape($settings->phone ?? '001 123 456 789'); ?></span>
                    </div>
                  </div>

                  <!-- School Address Block -->
                  <div class="space-y-0.5 px-2">
                    <div class="text-[9.5px] font-black text-emerald-800 tracking-widest uppercase">
                      SCHOOL ADDRESS
                    </div>
                    <div id="dom-back-address" class="text-[10px] text-slate-700 font-semibold leading-snug px-1">
                      100/1 Bryant Lane<br>Manor, Orla land, New York
                    </div>
                  </div>

                  <!-- Detailed Contact List with Icons -->
                  <div class="px-3 space-y-1 text-left text-[10px] font-semibold text-slate-800">
                    <div class="flex items-center gap-2">
                      <span class="material-symbols-outlined text-[15px] text-orange-600 shrink-0">call</span>
                      <span id="dom-back-phone-list"><?php echo html_escape($settings->phone ?? '001 123 456 789'); ?></span>
                    </div>
                    <div class="flex items-center gap-2">
                      <span class="material-symbols-outlined text-[15px] text-emerald-700 shrink-0">mail</span>
                      <span id="dom-back-email-list" class="truncate"><?php echo html_escape($settings->email ?? 'info@login2school.com'); ?></span>
                    </div>
                    <div class="flex items-center gap-2">
                      <span class="material-symbols-outlined text-[15px] text-emerald-700 shrink-0">language</span>
                      <span id="dom-back-web-list" class="truncate"><?php echo html_escape($settings->website ?? 'www.login2school.com'); ?></span>
                    </div>
                    <div class="flex items-start gap-2 pt-0.5">
                      <span class="material-symbols-outlined text-[15px] text-emerald-700 shrink-0 mt-0.5">support_agent</span>
                      <div class="leading-tight">
                        <div class="text-[9px] text-slate-500 font-bold">Emergency Contact</div>
                        <div id="dom-back-emergency-list" class="text-slate-900 font-extrabold font-mono"><?php echo html_escape($settings->emergency_contact ?? '001 987 654 321'); ?></div>
                      </div>
                    </div>
                  </div>

                  <!-- Principal Signature Block -->
                  <div class="text-center pt-1">
                    <div class="h-8 flex items-center justify-center">
                      <img id="dom-back-sig-img" src="" alt="Signature" class="max-h-7 max-w-[110px] object-contain hidden"/>
                      <!-- Elegant signature script placeholder if none uploaded -->
                      <div id="dom-back-sig-script" class="font-serif italic text-base text-slate-800 tracking-wider">
                        John
                      </div>
                    </div>
                    <div class="text-[9px] font-bold text-slate-600">
                      Principal Signature
                    </div>
                  </div>

                  <!-- Return Notice Disclaimer (Orange) -->
                  <div id="dom-back-return-text" class="text-[9.5px] font-bold text-orange-600 leading-tight px-2 pb-1">
                    If found, please return this card<br>to the school.
                  </div>

                </div>

              </div>

              <!-- Right Dimension Indicator (3.375 inch / 86 mm) -->
              <div class="h-[445px] flex flex-col items-center justify-between text-[11px] font-semibold text-slate-600 py-1 select-none">
                <span class="text-slate-400">&bull;</span>
                <span class="writing-mode-vertical border-r border-dashed border-slate-400 h-full mx-1 flex items-center justify-center text-center pr-1 text-[10px]" style="writing-mode: vertical-rl; transform: rotate(180deg);">3.375 inch (86 mm)</span>
                <span class="text-slate-400">&bull;</span>
              </div>

            </div>

          </div>

        </div>

        <!-- Bottom Info Banner (CR80 Portrait Dimensions) -->
        <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs font-semibold flex items-center justify-center gap-2 text-center">
          <span class="material-symbols-outlined text-[18px] text-amber-700">info</span>
          <span>Card Size: 2.125 &times; 3.375 inch (CR80 Portrait)</span>
        </div>

        <!-- Action Toolbar -->
        <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
          
          <div class="flex flex-wrap items-center gap-2.5">
            <!-- Print Button (Dark Green) -->
            <button type="button" onclick="printActiveCard()" class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-emerald-800 text-white font-bold text-xs hover:bg-emerald-900 transition-colors shadow-xs cursor-pointer">
              <span class="material-symbols-outlined text-[18px]">print</span>
              <span>Print Card</span>
            </button>

            <!-- Download PDF (Navy) -->
            <button type="button" onclick="downloadActivePdf()" class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition-colors shadow-xs cursor-pointer">
              <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span>
              <span>Download PDF</span>
            </button>

            <!-- Download PNG (Outlined) -->
            <button type="button" onclick="downloadActiveImage('png')" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-50 transition-colors shadow-2xs cursor-pointer">
              <span class="material-symbols-outlined text-[18px] text-slate-500">image</span>
              <span>Download PNG</span>
            </button>

            <!-- Download JPG (Outlined) -->
            <button type="button" onclick="downloadActiveImage('jpeg')" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-50 transition-colors shadow-2xs cursor-pointer">
              <span class="material-symbols-outlined text-[18px] text-slate-500">photo</span>
              <span>Download JPG</span>
            </button>
          </div>

          <!-- Regenerate Card (Outlined Green) -->
          <button type="button" onclick="regenerateActiveCard()" title="Fetch latest student details from database and increment version" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-white border border-emerald-600 text-emerald-800 font-bold text-xs hover:bg-emerald-50 transition-colors shadow-2xs cursor-pointer">
            <span class="material-symbols-outlined text-[18px] text-emerald-700">sync</span>
            <span>Regenerate Card</span>
          </button>

        </div>

      </div>

    </div>

  </div>

</div>

<!-- =========================================================================
     MODAL 1: ID CARD SETTINGS CONFIGURATION
     ========================================================================= -->
<div id="settings-modal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
  <div class="w-full max-w-2xl bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[90vh]">
    
    <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
      <div class="flex items-center gap-2.5">
        <span class="p-2 rounded-xl bg-emerald-800 text-white flex items-center justify-center shadow-xs">
          <span class="material-symbols-outlined text-[20px]">settings</span>
        </span>
        <div>
          <h3 class="font-bold text-base text-slate-900">ID Card Configuration Settings</h3>
          <p class="text-xs text-slate-500">Configure institution branding, principal signature, and return text.</p>
        </div>
      </div>
      <button type="button" onclick="closeSettingsModal()" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-200 transition-colors">
        <span class="material-symbols-outlined text-[20px]">close</span>
      </button>
    </div>

    <?php echo form_open_multipart('students/id_card_settings_save', array('id' => 'modal-settings-form', 'class' => 'overflow-y-auto p-6 space-y-4')); ?>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">School / Institution Name</label>
          <input type="text" name="school_name" value="<?php echo html_escape($settings->school_name ?? 'Login2'); ?>" placeholder="Login2" class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300"/>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">School Motto / Subtitle</label>
          <input type="text" name="card_title" value="<?php echo html_escape($settings->card_title ?? 'PUBLIC SCHOOL'); ?>" placeholder="PUBLIC SCHOOL" class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300"/>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Phone Number</label>
          <input type="text" name="phone" value="<?php echo html_escape($settings->phone ?? '001 123 456 789'); ?>" placeholder="001 123 456 789" class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300"/>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Email Address</label>
          <input type="text" name="email" value="<?php echo html_escape($settings->email ?? 'info@login2school.com'); ?>" placeholder="info@login2school.com" class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300"/>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Website URL</label>
          <input type="text" name="website" value="<?php echo html_escape($settings->website ?? 'www.login2school.com'); ?>" placeholder="www.login2school.com" class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300"/>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Emergency Contact Number</label>
          <input type="text" name="emergency_contact" value="<?php echo html_escape($settings->emergency_contact ?? '001 987 654 321'); ?>" placeholder="001 987 654 321" class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300"/>
        </div>

        <div class="sm:col-span-2">
          <label class="block text-xs font-bold text-slate-700 mb-1">School Campus Address</label>
          <input type="text" name="school_address" value="<?php echo html_escape($settings->school_address ?? '100/1 Bryant Lane, Manor, Orla land, New York'); ?>" placeholder="100/1 Bryant Lane, Manor, Orla land, New York" class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300"/>
        </div>

        <div class="sm:col-span-2">
          <label class="block text-xs font-bold text-slate-700 mb-1">Return Notice Text</label>
          <input type="text" name="return_text" value="<?php echo html_escape($settings->return_text ?? 'If found, please return this card to the school.'); ?>" placeholder="If found, please return this card to the school." class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300"/>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">School Logo</label>
          <input type="file" name="school_logo" accept="image/*" class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-300"/>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Principal Signature</label>
          <input type="file" name="principal_signature" accept="image/*" class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-300"/>
        </div>

      </div>

      <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-2">
        <button type="button" onclick="closeSettingsModal()" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-100">Cancel</button>
        <button type="submit" class="px-5 py-2 rounded-lg bg-emerald-800 text-white text-xs font-bold hover:bg-emerald-900 shadow-xs">Save Changes</button>
      </div>
    <?php echo form_close(); ?>

  </div>
</div>

<!-- =========================================================================
     MODAL 2: ID CARD GENERATION HISTORY
     ========================================================================= -->
<div id="history-modal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
  <div class="w-full max-w-4xl bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[90vh]">
    
    <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
      <div class="flex items-center gap-2.5">
        <span class="p-2 rounded-xl bg-emerald-800 text-white flex items-center justify-center shadow-xs">
          <span class="material-symbols-outlined text-[20px]">history</span>
        </span>
        <div>
          <h3 class="font-bold text-base text-slate-900">ID Card Generation History</h3>
          <p class="text-xs text-slate-500">Audit trail of issued ID cards with direct reprint & regeneration.</p>
        </div>
      </div>
      <button type="button" onclick="closeHistoryModal()" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-200 transition-colors">
        <span class="material-symbols-outlined text-[20px]">close</span>
      </button>
    </div>

    <div class="p-6 overflow-y-auto space-y-4">
      <table id="modal-history-datatable" class="w-full text-left text-xs">
        <thead>
          <tr class="border-b border-slate-200 bg-slate-50 text-slate-700 font-bold uppercase">
            <th class="p-2.5">Card Number</th>
            <th class="p-2.5">Student</th>
            <th class="p-2.5">Class</th>
            <th class="p-2.5">Version</th>
            <th class="p-2.5">Generated Date</th>
            <th class="p-2.5">Status</th>
            <th class="p-2.5 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <!-- Populated via DataTables AJAX -->
        </tbody>
      </table>
    </div>

  </div>
</div>

<!-- Client JavaScript Logic -->
<script>
  let activeStudentId = <?php echo ($selected_student ? $selected_student->student_id : (empty($students) ? 0 : $students[0]->student_id)); ?>;
  let activeStudentData = null;
  let activeSettingsData = <?php echo json_encode($settings); ?>;
  let historyDtInstance = null;

  // Pagination state for left list
  let currentPage = 1;
  const pageSize = 5;

  document.addEventListener('DOMContentLoaded', () => {
    if (activeStudentId > 0) {
      loadCardData(activeStudentId);
    }
    initClientPagination();
  });

  // Toggle Single vs Bulk Mode
  function toggleSelectionMode(mode) {
    const singleWrap = document.getElementById('single-select-wrapper');
    const bulkWrap   = document.getElementById('bulk-select-wrapper');
    const singleBtn  = document.getElementById('tab-single-btn');
    const bulkBtn    = document.getElementById('tab-bulk-btn');

    if (mode === 'single') {
      singleWrap.classList.remove('hidden');
      bulkWrap.classList.add('hidden');
      singleBtn.className = "py-2 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1.5 bg-white text-emerald-800 shadow-xs";
      bulkBtn.className   = "py-2 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1.5 text-slate-600 hover:text-slate-900";
    } else {
      singleWrap.classList.add('hidden');
      bulkWrap.classList.remove('hidden');
      bulkBtn.className   = "py-2 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1.5 bg-white text-emerald-800 shadow-xs";
      singleBtn.className = "py-2 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1.5 text-slate-600 hover:text-slate-900";
    }
  }

  // Filter Card View (Front / Back / Both)
  function filterCardView(view) {
    const frontStage = document.getElementById('front-card-stage-wrapper');
    const backStage  = document.getElementById('back-card-stage-wrapper');
    const frontBtn   = document.getElementById('btn-toggle-front');
    const backBtn    = document.getElementById('btn-toggle-back');
    const bothBtn    = document.getElementById('btn-toggle-both');

    const activeBtnClass   = "px-4 py-1.5 rounded-lg text-xs font-bold transition-all bg-emerald-800 text-white shadow-xs";
    const inactiveBtnClass = "px-4 py-1.5 rounded-lg text-xs font-bold transition-all text-slate-600 hover:text-slate-900";

    if (view === 'front') {
      frontStage.classList.remove('hidden');
      backStage.classList.add('hidden');
      frontBtn.className = activeBtnClass;
      backBtn.className  = inactiveBtnClass;
      if (bothBtn) bothBtn.className = inactiveBtnClass;
    } else if (view === 'back') {
      frontStage.classList.add('hidden');
      backStage.classList.remove('hidden');
      frontBtn.className = inactiveBtnClass;
      backBtn.className  = activeBtnClass;
      if (bothBtn) bothBtn.className = inactiveBtnClass;
    } else {
      frontStage.classList.remove('hidden');
      backStage.classList.remove('hidden');
      frontBtn.className = inactiveBtnClass;
      backBtn.className  = inactiveBtnClass;
      if (bothBtn) bothBtn.className = activeBtnClass;
    }
  }

  // Select a student from list
  function selectStudentCard(studentId, el) {
    activeStudentId = studentId;

    // Reset and set active card style
    document.querySelectorAll('.student-select-card').forEach(card => {
      card.className = "student-select-card p-3 rounded-xl border border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/60 transition-all cursor-pointer flex items-center gap-3.5";
      const radio = card.querySelector('.radio-indicator');
      const dot = card.querySelector('.radio-dot');
      if (radio) radio.className = "radio-indicator w-4 h-4 rounded-full border-2 border-slate-300 bg-white flex items-center justify-center shrink-0";
      if (dot) dot.classList.add('hidden');
    });

    if (el) {
      el.className = "student-select-card p-3 rounded-xl border border-emerald-600 bg-emerald-50/40 ring-1 ring-emerald-600 shadow-2xs transition-all cursor-pointer flex items-center gap-3.5";
      const activeRadio = el.querySelector('.radio-indicator');
      const activeDot = el.querySelector('.radio-dot');
      if (activeRadio) activeRadio.className = "radio-indicator w-4 h-4 rounded-full border-2 border-emerald-600 bg-white flex items-center justify-center shrink-0";
      if (activeDot) activeDot.classList.remove('hidden');
    }

    loadCardData(studentId);
  }

  // Fetch Student data via AJAX
  function loadCardData(studentId) {
    $.ajax({
      url: window.APP_BASE_URL + 'students/id_card_preview_ajax',
      type: 'GET',
      data: { student_id: studentId },
      dataType: 'json',
      success: function (res) {
        if (res.status && res.student) {
          activeStudentData = res.student;
          activeSettingsData = res.settings;
          populateCardDOM(res);
        }
      }
    });
  }

  // Populate Card Preview DOM
  function populateCardDOM(data) {
    const st  = data.student;
    const set = data.settings;

    // Front Side
    $('#dom-school-subtitle').text(set.card_title || 'PUBLIC SCHOOL');
    $('#dom-student-name').text(data.full_name || 'STUDENT NAME');
    $('#dom-student-adm').text(st.admission_number || 'EDU2026015');
    $('#dom-student-father').text(st.guardian_name || 'test');
    $('#dom-student-class').text(st.class_name || 'Grade 10');
    $('#dom-student-section').text(st.section_name || 'A');
    
    if (st.roll_number) {
      $('#dom-student-roll').text(st.roll_number);
      $('#row-roll-no').removeClass('hidden');
    } else {
      $('#row-roll-no').addClass('hidden');
    }

    $('#dom-student-dob').text(data.dob_formatted || '15-06-2012');
    $('#dom-student-blood').text(st.blood_group || 'A+');

    // Photo
    if (data.has_photo && data.photo_url) {
      $('#dom-student-photo').attr('src', data.photo_url).removeClass('hidden');
      $('#dom-student-photo-fallback').addClass('hidden');
    } else {
      $('#dom-student-photo').addClass('hidden');
      $('#dom-student-photo-fallback').text(data.initials || 'ST').removeClass('hidden');
    }

    // Logo
    if (data.logo_url) {
      $('#dom-school-logo').attr('src', data.logo_url).removeClass('hidden');
    } else {
      $('#dom-school-logo').attr('src', window.APP_BASE_URL + 'assets/logo.png').removeClass('hidden');
    }

    // Version
    const ver = (st.card_history && st.card_history.card_version) ? st.card_history.card_version : 1;
    $('#preview-ver-pill').text('v' + ver);

    // Back Side
    $('#dom-back-main-phone').text(set.phone || '001 123 456 789');
    $('#dom-back-address').html(escapeAddressHtml(set.school_address || '100/1 Bryant Lane, Manor, Orla land, New York'));
    $('#dom-back-phone-list').text(set.phone || '001 123 456 789');
    $('#dom-back-email-list').text(set.email || 'info@login2school.com');
    $('#dom-back-web-list').text(set.website || 'www.login2school.com');
    $('#dom-back-emergency-list').text(set.emergency_contact || '001 987 654 321');
    $('#dom-back-return-text').html(escapeAddressHtml(set.return_text || 'If found, please return this card to the school.'));

    if (data.signature_url) {
      $('#dom-back-sig-img').attr('src', data.signature_url).removeClass('hidden');
      $('#dom-back-sig-script').addClass('hidden');
    } else {
      $('#dom-back-sig-img').addClass('hidden');
      $('#dom-back-sig-script').removeClass('hidden');
    }
  }

  function escapeAddressHtml(str) {
    if (!str) return '';
    const safe = $('<div>').text(str).html();
    return safe.replace(/,\s*/g, '<br>');
  }

  // Filter left list
  function filterStudentCards() {
    const classVal  = $('#left-class-filter').val();
    const searchVal = $('#left-search-input').val().toLowerCase().trim();

    let visibleCards = [];
    $('.student-select-card').each(function () {
      const card = $(this);
      const cardClass = card.attr('data-class');
      const cardName  = card.attr('data-name');
      const cardAdm   = card.attr('data-adm');
      const cardRoll  = card.attr('data-roll');

      let match = true;
      if (classVal && cardClass !== classVal) match = false;
      if (searchVal) {
        if (!cardName.includes(searchVal) && !cardAdm.includes(searchVal) && !cardRoll.includes(searchVal)) {
          match = false;
        }
      }

      if (match) {
        visibleCards.push(card);
      } else {
        card.addClass('hidden');
      }
    });

    $('#filtered-count-badge').text(visibleCards.length + ' found');
    currentPage = 1;
    renderPagedCards(visibleCards);
  }

  // Client-side pagination
  function initClientPagination() {
    filterStudentCards();
  }

  function renderPagedCards(cardsList) {
    const cards = cardsList || Array.from(document.querySelectorAll('.student-select-card:not(.filtered-out)'));
    const totalPages = Math.ceil(cards.length / pageSize) || 1;

    if (currentPage > totalPages) currentPage = totalPages;
    if (currentPage < 1) currentPage = 1;

    cards.forEach((card, idx) => {
      const el = card.jquery ? card[0] : card;
      const startIdx = (currentPage - 1) * pageSize;
      const endIdx   = startIdx + pageSize;
      if (idx >= startIdx && idx < endIdx) {
        el.classList.remove('hidden');
      } else {
        el.classList.add('hidden');
      }
    });

    // Render Pills
    const pillContainer = document.getElementById('pagination-pills');
    if (pillContainer) {
      let html = '';
      for (let i = 1; i <= Math.min(totalPages, 5); i++) {
        const isActive = (i === currentPage);
        html += `<button type="button" onclick="goToPage(${i})" class="w-6 h-6 rounded flex items-center justify-center ${isActive ? 'bg-emerald-800 text-white' : 'text-slate-600 hover:bg-slate-100'}">${i}</button>`;
      }
      pillContainer.innerHTML = html;
    }

    $('#prev-page-btn').prop('disabled', currentPage === 1).toggleClass('opacity-50', currentPage === 1);
    $('#next-page-btn').prop('disabled', currentPage === totalPages).toggleClass('opacity-50', currentPage === totalPages);
  }

  function goToPage(page) {
    currentPage = page;
    filterStudentCards();
  }

  function paginateStudents(direction) {
    currentPage += direction;
    filterStudentCards();
  }

  // =========================================================================
  // ACTIONS: PRINT, PDF, PNG, JPG, REGENERATE
  // =========================================================================
  function printActiveCard() {
    if (!activeStudentId) return;
    recordEvent(activeStudentId, 'Printed');
    const printUrl = window.APP_BASE_URL + 'students/id_card_print?student_id=' + activeStudentId + '&autoprint=1';
    window.open(printUrl, '_blank', 'width=900,height=750');
  }

  async function downloadActivePdf() {
    if (!activeStudentId) return;
    const { jsPDF } = window.jspdf;

    // Standard Portrait CR80 (54mm x 86mm / 2.125" x 3.375")
    const pdf = new jsPDF({
      orientation: 'portrait',
      unit: 'mm',
      format: [54, 86]
    });

    // 1. Render Front
    const frontEl = document.getElementById('portrait-cr80-front');
    const frontCanvas = await html2canvas(frontEl, { scale: 3.5, useCORS: true, logging: false });
    pdf.addImage(frontCanvas.toDataURL('image/jpeg', 0.95), 'JPEG', 0, 0, 54, 86);

    // 2. Render Back
    pdf.addPage([54, 86], 'portrait');
    const backEl = document.getElementById('portrait-cr80-back');
    const backCanvas = await html2canvas(backEl, { scale: 3.5, useCORS: true, logging: false });
    pdf.addImage(backCanvas.toDataURL('image/jpeg', 0.95), 'JPEG', 0, 0, 54, 86);

    const adm = $('#dom-student-adm').text() || 'student';
    pdf.save('ID_Card_' + adm + '.pdf');

    recordEvent(activeStudentId, 'Generated');
  }

  async function downloadActiveImage(format) {
    if (!activeStudentId) return;

    // Export both Front and Back or active face
    const frontEl = document.getElementById('portrait-cr80-front');
    const frontCanvas = await html2canvas(frontEl, { scale: 3.5, useCORS: true, logging: false });

    const mime = (format === 'png') ? 'image/png' : 'image/jpeg';
    const ext  = (format === 'png') ? 'png' : 'jpg';
    const adm = $('#dom-student-adm').text() || 'student';

    // Front image download
    const linkFront = document.createElement('a');
    linkFront.download = 'ID_Card_' + adm + '_front.' + ext;
    linkFront.href = frontCanvas.toDataURL(mime, 0.95);
    linkFront.click();

    // Back image download
    setTimeout(async () => {
      const backEl = document.getElementById('portrait-cr80-back');
      const backCanvas = await html2canvas(backEl, { scale: 3.5, useCORS: true, logging: false });
      const linkBack = document.createElement('a');
      linkBack.download = 'ID_Card_' + adm + '_back.' + ext;
      linkBack.href = backCanvas.toDataURL(mime, 0.95);
      linkBack.click();
    }, 400);

    recordEvent(activeStudentId, 'Generated');
  }

  function regenerateActiveCard() {
    if (!activeStudentId) return;

    $.ajax({
      url: window.APP_BASE_URL + 'students/id_card_regenerate_ajax',
      type: 'POST',
      data: {
        student_id: activeStudentId,
        [window.CSRF_TOKEN_NAME]: window.CSRF_HASH
      },
      dataType: 'json',
      success: function (res) {
        if (res.csrf_hash) window.CSRF_HASH = res.csrf_hash;
        if (res.status) {
          loadCardData(activeStudentId);
          if (historyDtInstance) {
            historyDtInstance.ajax.reload(null, false);
          }
        }
      }
    });
  }

  function recordEvent(studentId, action) {
    $.ajax({
      url: window.APP_BASE_URL + 'students/id_card_record_ajax',
      type: 'POST',
      data: {
        student_id: studentId,
        action: action,
        [window.CSRF_TOKEN_NAME]: window.CSRF_HASH
      },
      dataType: 'json',
      success: function (res) {
        if (res.csrf_hash) window.CSRF_HASH = res.csrf_hash;
      }
    });
  }

  // =========================================================================
  // BULK ACTIONS
  // =========================================================================
  function updateBulkCount() {
    const cnt = $('.bulk-cb:checked').length;
    $('#bulk-checked-count').text(cnt);
  }

  function toggleAllBulkCbs(master) {
    $('.bulk-cb').prop('checked', master.checked);
    updateBulkCount();
  }

  function executeBulkPrint() {
    const ids = [];
    $('.bulk-cb:checked').each(function () { ids.push($(this).val()); });

    if (ids.length === 0) {
      alert('Please check at least one student for bulk print.');
      return;
    }

    const printUrl = window.APP_BASE_URL + 'students/id_card_print?student_ids=' + ids.join(',') + '&autoprint=1';
    window.open(printUrl, '_blank', 'width=950,height=750');
  }

  function executeBulkPdf() {
    const ids = [];
    $('.bulk-cb:checked').each(function () { ids.push($(this).val()); });

    if (ids.length === 0) {
      alert('Please check at least one student for bulk PDF export.');
      return;
    }

    const printUrl = window.APP_BASE_URL + 'students/id_card_print?student_ids=' + ids.join(',');
    window.open(printUrl, '_blank', 'width=950,height=750');
  }

  // =========================================================================
  // MODALS: SETTINGS & HISTORY
  // =========================================================================
  function openSettingsModal() {
    document.getElementById('settings-modal').classList.remove('hidden');
  }

  function closeSettingsModal() {
    document.getElementById('settings-modal').classList.add('hidden');
  }

  function openHistoryModal() {
    document.getElementById('history-modal').classList.remove('hidden');
    initHistoryDataTable();
  }

  function closeHistoryModal() {
    document.getElementById('history-modal').classList.add('hidden');
  }

  function initHistoryDataTable() {
    if ($.fn.DataTable.isDataTable('#modal-history-datatable')) {
      historyDtInstance.ajax.reload(null, false);
      return;
    }

    historyDtInstance = $('#modal-history-datatable').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: window.APP_BASE_URL + 'students/id_card_history_ajax',
        type: 'POST',
        data: function (d) {
          d[window.CSRF_TOKEN_NAME] = window.CSRF_HASH;
        },
        dataSrc: function (json) {
          if (json.csrf_hash) window.CSRF_HASH = json.csrf_hash;
          return json.data;
        }
      },
      pageLength: 10,
      order: [[4, 'desc']],
      columns: [
        { data: 0, orderable: true },
        { data: 1, orderable: true },
        { data: 2, orderable: false },
        { data: 3, orderable: true },
        { data: 4, orderable: true },
        { data: 5, orderable: false },
        { data: 6, orderable: false, className: 'text-right' }
      ]
    });
  }

  function previewHistoryCard(studentId) {
    closeHistoryModal();
    const el = document.querySelector(`.student-select-card[data-student-id="${studentId}"]`);
    selectStudentCard(studentId, el);
  }

  function printSingleCard(studentId) {
    const printUrl = window.APP_BASE_URL + 'students/id_card_print?student_id=' + studentId + '&autoprint=1';
    window.open(printUrl, '_blank', 'width=900,height=750');
  }

  function downloadSinglePdf(studentId) {
    activeStudentId = studentId;
    loadCardData(studentId);
    setTimeout(() => { downloadActivePdf(); }, 400);
  }

  function downloadSingleImage(studentId, format) {
    if (typeof studentId === 'string') {
      downloadActiveImage(studentId);
      return;
    }
    activeStudentId = studentId;
    loadCardData(studentId);
    setTimeout(() => { downloadActiveImage(format || 'png'); }, 400);
  }

  function regenerateCard(studentId) {
    activeStudentId = studentId;
    regenerateActiveCard();
  }
</script>
