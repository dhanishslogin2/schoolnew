<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

    <!-- Action Toolbar (Hidden during Print) -->
    <div class="print:hidden flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div class="flex items-center gap-2">
        <a href="<?php echo site_url('examinations/report_cards'); ?>" class="p-1 rounded-lg hover:bg-surface-container-high text-on-surface-variant transition-colors">
          <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
        <h2 class="font-headline-md text-headline-md text-on-surface">Academic Progress Report Card</h2>
      </div>
      <div class="flex items-center gap-2 flex-wrap shrink-0">
        <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer">
          <span class="material-symbols-outlined text-[18px]">print</span>Print Report Card
        </button>
      </div>
    </div>

    <!-- REPORT CARD SHEET (Styled for Screen & Print) -->
    <div class="report-card-container bg-surface-container-lowest border border-outline-variant/60 rounded-2xl p-8 max-w-4xl mx-auto elevation-2 print:border-none print:shadow-none print:p-0 print:m-0 space-y-6">
      
      <!-- 1. School Header -->
      <div class="text-center pb-6 border-b-2 border-outline-variant/80">
        <div class="flex items-center justify-center gap-3 mb-2">
          <span class="material-symbols-outlined text-primary text-[40px]">school</span>
          <h1 class="text-2xl sm:text-3xl font-extrabold uppercase tracking-wide text-on-surface">Login2</h1>
        </div>
        <p class="text-body-md text-on-surface-variant font-medium">Affiliated to Central Board of Secondary Education • School Code: 84210</p>
        <p class="text-[12px] text-on-surface-variant">Knowledge Park, Institutional Area, City Campus • Phone: +91 98765 43210 • Email: info@school.edu</p>
        <div class="mt-3 inline-block px-4 py-1 rounded-full bg-primary-fixed text-primary font-bold text-[13px] uppercase tracking-wider">
          <?php echo html_escape($settings->report_card_header ?: 'Official Academic Report Card'); ?>
        </div>
      </div>

      <!-- 2. Student & Exam Meta Grid -->
      <div class="p-4 rounded-xl bg-surface-container-low border border-outline-variant/50 grid grid-cols-2 sm:grid-cols-4 gap-4 text-body-md">
        <div>
          <span class="text-[11px] text-on-surface-variant uppercase font-semibold block">Student Name</span>
          <strong class="text-on-surface text-[15px]"><?php echo html_escape($result->first_name . ' ' . $result->last_name); ?></strong>
          <span class="text-[12px] text-on-surface-variant block font-mono">Adm: <?php echo html_escape($result->admission_number); ?></span>
        </div>
        <div>
          <span class="text-[11px] text-on-surface-variant uppercase font-semibold block">Class & Division</span>
          <strong class="text-on-surface"><?php echo html_escape($result->class_name . ' ' . ($result->division_name ?: '')); ?></strong>
          <span class="text-[12px] text-on-surface-variant block">Roll #: <strong class="text-primary font-mono"><?php echo html_escape($result->roll_number ?: '—'); ?></strong></span>
        </div>
        <div>
          <span class="text-[11px] text-on-surface-variant uppercase font-semibold block">Academic Session</span>
          <strong class="text-on-surface"><?php echo html_escape($result->year_name); ?></strong>
          <span class="text-[12px] text-on-surface-variant block">Exam: <strong><?php echo html_escape($result->exam_name); ?></strong></span>
        </div>
        <div>
          <span class="text-[11px] text-on-surface-variant uppercase font-semibold block">Guardian / Contact</span>
          <strong class="text-on-surface"><?php echo html_escape($result->guardian_name ?: 'Parent'); ?></strong>
          <span class="text-[12px] text-on-surface-variant block font-mono"><?php echo html_escape($result->guardian_phone ?: '—'); ?></span>
        </div>
      </div>

      <!-- 3. Subject Evaluation Table -->
      <div>
        <h3 class="font-bold text-title-md text-on-surface mb-2 uppercase tracking-wider text-[13px]">Academic Performance</h3>
        <div class="overflow-x-auto rounded-xl border border-outline-variant/60">
          <table class="w-full data-table border-collapse text-body-md">
            <thead>
              <tr class="border-b border-outline-variant/60 bg-surface-container-low">
                <th class="text-left px-4 py-2.5 text-label-md font-semibold text-on-surface-variant uppercase">Subject</th>
                <th class="text-right px-4 py-2.5 text-label-md font-semibold text-on-surface-variant uppercase">Max</th>
                <th class="text-right px-4 py-2.5 text-label-md font-semibold text-on-surface-variant uppercase">Pass</th>
                <th class="text-right px-4 py-2.5 text-label-md font-semibold text-on-surface uppercase">Marks Obtained</th>
                <th class="text-right px-4 py-2.5 text-label-md font-semibold text-on-surface uppercase">%</th>
                <th class="text-center px-4 py-2.5 text-label-md font-semibold text-on-surface uppercase">Grade</th>
                <th class="text-right px-4 py-2.5 text-label-md font-semibold text-primary uppercase">Grade Point</th>
                <th class="text-center px-4 py-2.5 text-label-md font-semibold text-on-surface uppercase">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/40">
              <?php foreach ($result->subject_marks as $sm): ?>
                <?php
                  $maxM = (float)$sm->max_marks ?: 100.00;
                  $passM = (float)$sm->passing_marks ?: 35.00;
                  $obtM = ($sm->marks_obtained !== null) ? (float)$sm->marks_obtained : 0.00;
                  $subPct = ($maxM > 0) ? round(($obtM / $maxM) * 100, 1) : 0;
                  $isSubPass = (!$sm->is_absent && $obtM >= $passM);
                ?>
                <tr>
                  <td class="px-4 py-2.5 font-bold text-on-surface"><?php echo html_escape($sm->subject_name); ?></td>
                  <td class="px-4 py-2.5 text-right font-mono text-on-surface-variant"><?php echo (int)$maxM; ?></td>
                  <td class="px-4 py-2.5 text-right font-mono text-on-surface-variant"><?php echo (int)$passM; ?></td>
                  <td class="px-4 py-2.5 text-right font-mono font-bold text-on-surface">
                    <?php if ($sm->is_absent): ?><span class="text-error">ABS</span>
                    <?php elseif ($sm->is_exempted): ?><span class="text-primary">EXM</span>
                    <?php else: ?><?php echo number_format($obtM, 1); ?><?php endif; ?>
                  </td>
                  <td class="px-4 py-2.5 text-right font-mono"><?php echo ($sm->is_absent || $sm->is_exempted) ? '—' : $subPct . '%'; ?></td>
                  <td class="px-4 py-2.5 text-center font-bold"><?php echo html_escape($sm->grade ?: '—'); ?></td>
                  <td class="px-4 py-2.5 text-right font-mono font-bold text-primary"><?php echo number_format($sm->grade_point, 1); ?></td>
                  <td class="px-4 py-2.5 text-center">
                    <span class="px-2 py-0.5 rounded text-[11px] font-bold <?php echo $isSubPass ? 'bg-secondary-container text-on-secondary-container' : 'bg-error-container text-on-error-container'; ?>">
                      <?php echo $isSubPass ? 'PASS' : 'FAIL'; ?>
                    </span>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
            <tfoot>
              <tr class="border-t-2 border-outline-variant bg-surface-container-low font-bold">
                <td class="px-4 py-3 text-on-surface">GRAND TOTAL</td>
                <td class="px-4 py-3 text-right font-mono"><?php echo (int)$result->max_marks; ?></td>
                <td class="px-4 py-3 text-right font-mono">—</td>
                <td class="px-4 py-3 text-right font-mono text-primary text-base"><?php echo number_format($result->total_marks, 1); ?></td>
                <td class="px-4 py-3 text-right font-mono text-secondary text-base"><?php echo number_format($result->percentage, 2); ?>%</td>
                <td class="px-4 py-3 text-center text-base"><?php echo html_escape($result->overall_grade); ?></td>
                <td class="px-4 py-3 text-right font-mono text-primary text-base"><?php echo number_format($result->gpa, 2); ?></td>
                <td class="px-4 py-3 text-center">
                  <span class="px-3 py-1 rounded-md text-[12px] font-bold <?php echo ($result->pass_status === 'Pass') ? 'bg-secondary text-on-secondary' : 'bg-error text-on-error'; ?>">
                    <?php echo strtoupper($result->pass_status); ?>
                  </span>
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

      <!-- 4. Performance Summary & Attendance Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- Result Summary -->
        <div class="p-4 rounded-xl bg-surface-container-low border border-outline-variant/50 space-y-2 text-body-md">
          <div class="font-bold text-on-surface text-[13px] uppercase tracking-wider border-b border-outline-variant/40 pb-1">Result Evaluation</div>
          <div class="flex justify-between">
            <span class="text-on-surface-variant">Overall Result:</span>
            <strong class="<?php echo ($result->pass_status === 'Pass') ? 'text-secondary' : 'text-error'; ?>"><?php echo $result->pass_status; ?></strong>
          </div>
          <?php if ($settings->show_rank_on_report_card): ?>
            <div class="flex justify-between">
              <span class="text-on-surface-variant">Class Merit Position:</span>
              <strong class="text-primary font-mono"><?php echo $result->class_rank ? $result->class_rank . 'th Position' : 'N/A'; ?></strong>
            </div>
          <?php endif; ?>
          <div class="flex justify-between">
            <span class="text-on-surface-variant">Cumulative GPA:</span>
            <strong class="font-mono text-on-surface"><?php echo number_format($result->gpa, 2); ?> / 10.0</strong>
          </div>
        </div>

        <!-- Attendance Summary -->
        <div class="p-4 rounded-xl bg-surface-container-low border border-outline-variant/50 space-y-2 text-body-md">
          <div class="font-bold text-on-surface text-[13px] uppercase tracking-wider border-b border-outline-variant/40 pb-1">Academic Attendance</div>
          <div class="flex justify-between">
            <span class="text-on-surface-variant">Working Days:</span>
            <strong class="font-mono text-on-surface"><?php echo $attendance->total_days ?? 0; ?> Days</strong>
          </div>
          <div class="flex justify-between">
            <span class="text-on-surface-variant">Days Present:</span>
            <strong class="font-mono text-secondary"><?php echo $attendance->present ?? 0; ?> Days</strong>
          </div>
          <div class="flex justify-between">
            <span class="text-on-surface-variant">Attendance Rate:</span>
            <strong class="font-mono text-primary"><?php echo $attendance->percentage ?? 0; ?>%</strong>
          </div>
        </div>
      </div>

      <!-- 5. Remarks -->
      <div class="p-4 rounded-xl bg-surface-container-low border border-outline-variant/50 space-y-2">
        <span class="text-[11px] text-on-surface-variant uppercase font-semibold block">Teacher Assessment & Remarks</span>
        <p class="text-body-md text-on-surface italic leading-relaxed">
          "<?php echo html_escape($result->teacher_remarks ?: ($result->pass_status === 'Pass' ? 'Demonstrated commendable academic diligence and active participation throughout the term. Keep up the good work!' : 'Needs dedicated attention and remedial support in core subject areas. Regular practice is advised.')); ?>"
        </p>
      </div>

      <!-- 6. Signatures Footer -->
      <div class="grid grid-cols-3 gap-4 pt-12 text-center text-body-md">
        <div>
          <div class="border-t border-outline-variant/80 pt-2 font-medium text-on-surface">
            <?php echo html_escape($settings->teacher_signature_title ?: 'Class Teacher'); ?>
          </div>
          <span class="text-[11px] text-on-surface-variant">Signature & Date</span>
        </div>
        <div>
          <div class="border-t border-outline-variant/80 pt-2 font-medium text-on-surface">
            Parent / Guardian
          </div>
          <span class="text-[11px] text-on-surface-variant">Signature</span>
        </div>
        <div>
          <div class="border-t border-outline-variant/80 pt-2 font-medium text-on-surface">
            <?php echo html_escape($settings->principal_signature_title ?: 'Principal'); ?>
          </div>
          <span class="text-[11px] text-on-surface-variant">Seal & Signature</span>
        </div>
      </div>
    </div>
