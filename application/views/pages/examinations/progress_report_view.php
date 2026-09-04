<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

    <!-- Action Toolbar (Hidden during Print) -->
    <div class="print:hidden flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div class="flex items-center gap-2">
        <a href="<?php echo site_url('examinations/progress_reports'); ?>" class="p-1 rounded-lg hover:bg-surface-container-high text-on-surface-variant transition-colors">
          <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
        <h2 class="font-headline-md text-headline-md text-on-surface">Multi-Exam Progress Report</h2>
      </div>
      <div class="flex items-center gap-2 flex-wrap shrink-0">
        <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md font-semibold hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer">
          <span class="material-symbols-outlined text-[18px]">print</span>Print Progress Report
        </button>
      </div>
    </div>

    <!-- REPORT SHEET -->
    <div class="progress-report-container bg-surface-container-lowest border border-outline-variant/60 rounded-2xl p-8 max-w-4xl mx-auto elevation-2 print:border-none print:shadow-none print:p-0 print:m-0 space-y-6">
      
      <!-- School Header -->
      <div class="text-center pb-4 border-b-2 border-outline-variant/80">
        <div class="flex items-center justify-center gap-2 mb-1">
          <span class="material-symbols-outlined text-primary text-[36px]">trending_up</span>
          <h1 class="text-2xl font-extrabold uppercase tracking-wide text-on-surface">Comprehensive Progress Report</h1>
        </div>
        <p class="text-body-md text-on-surface-variant font-medium">Login2 • Longitudinal Academic Trend Analysis</p>
      </div>

      <!-- Student Meta -->
      <div class="p-4 rounded-xl bg-surface-container-low border border-outline-variant/50 grid grid-cols-2 sm:grid-cols-4 gap-4 text-body-md">
        <div>
          <span class="text-[11px] text-on-surface-variant uppercase font-semibold block">Student</span>
          <strong class="text-on-surface text-[15px]"><?php echo html_escape($report->student->first_name . ' ' . $report->student->last_name); ?></strong>
          <span class="text-[12px] text-on-surface-variant block font-mono">Adm: <?php echo html_escape($report->student->admission_number); ?></span>
        </div>
        <div>
          <span class="text-[11px] text-on-surface-variant uppercase font-semibold block">Class & Division</span>
          <strong class="text-on-surface"><?php echo html_escape($report->student->class_name . ' ' . ($report->student->division_name ?: '')); ?></strong>
          <span class="text-[12px] text-on-surface-variant block">Roll #: <strong class="text-primary font-mono"><?php echo html_escape($report->student->roll_number ?: '—'); ?></strong></span>
        </div>
        <div>
          <span class="text-[11px] text-on-surface-variant uppercase font-semibold block">Academic Year</span>
          <strong class="text-on-surface"><?php echo html_escape($report->student->year_name); ?></strong>
        </div>
        <div>
          <span class="text-[11px] text-on-surface-variant uppercase font-semibold block">Exams Tracked</span>
          <strong class="text-primary font-mono"><?php echo count($report->exams); ?> Examinations</strong>
        </div>
      </div>

      <!-- Examination Summary Cards -->
      <div>
        <h3 class="font-bold text-title-md text-on-surface mb-3 uppercase tracking-wider text-[13px]">Examination Score Trajectory</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
          <?php foreach ($report->exams as $ex): ?>
            <div class="p-3.5 rounded-xl bg-surface-container-low border border-outline-variant/40 text-center">
              <span class="text-[12px] font-semibold text-on-surface block truncate"><?php echo html_escape($ex['exam_name']); ?></span>
              <div class="text-xl font-bold font-mono text-secondary mt-1"><?php echo number_format($ex['percentage'], 1); ?>%</div>
              <span class="inline-block mt-1 px-2 py-0.5 rounded text-[11px] font-bold bg-surface-container-high text-on-surface">Grade <?php echo html_escape($ex['grade']); ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Comparative Subject Matrix Table -->
      <div>
        <h3 class="font-bold text-title-md text-on-surface mb-3 uppercase tracking-wider text-[13px]">Subject-Wise Trend Matrix</h3>
        <div class="overflow-x-auto rounded-xl border border-outline-variant/60">
          <table class="w-full data-table border-collapse text-body-md">
            <thead>
              <tr class="border-b border-outline-variant/60 bg-surface-container-low">
                <th class="text-left px-4 py-2.5 text-label-md font-semibold text-on-surface-variant uppercase">Subject</th>
                <?php foreach ($report->exams as $ex): ?>
                  <th class="text-right px-4 py-2.5 text-label-md font-semibold text-on-surface-variant uppercase whitespace-nowrap">
                    <?php echo html_escape($ex['exam_name']); ?>
                  </th>
                <?php endforeach; ?>
                <th class="text-center px-4 py-2.5 text-label-md font-semibold text-primary uppercase whitespace-nowrap">Overall Trend</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/40">
              <?php if (empty($report->subject_matrix)): ?>
                <tr><td colspan="<?php echo count($report->exams) + 2; ?>" class="px-4 py-6 text-center text-on-surface-variant">No comparative exam subject marks recorded yet.</td></tr>
              <?php else: ?>
                <?php foreach ($report->subject_matrix as $subName => $data): ?>
                  <?php
                    $trendClass = 'bg-primary-fixed text-primary';
                    $trendIcon = 'trending_flat';
                    if ($data['trend'] === 'Improving') {
                      $trendClass = 'bg-secondary-container text-on-secondary-container';
                      $trendIcon = 'trending_up';
                    } elseif ($data['trend'] === 'Declining') {
                      $trendClass = 'bg-error-container text-on-error-container';
                      $trendIcon = 'trending_down';
                    }
                  ?>
                  <tr>
                    <td class="px-4 py-3 font-bold text-on-surface whitespace-nowrap">
                      <?php echo html_escape($subName); ?>
                      <span class="text-[11px] text-on-surface-variant block font-mono font-normal"><?php echo html_escape($data['subject_code'] ?? ''); ?></span>
                    </td>
                    <?php foreach ($report->exams as $ex): ?>
                      <?php $em = $data['exams'][$ex['exam_id']] ?? null; ?>
                      <td class="px-4 py-3 text-right font-mono whitespace-nowrap">
                        <?php if ($em && $em['marks'] !== null): ?>
                          <span class="font-bold text-on-surface"><?php echo number_format($em['marks'], 1); ?></span>
                          <span class="text-[11px] text-on-surface-variant">(<?php echo $em['percentage']; ?>%)</span>
                        <?php else: ?>
                          <span class="text-on-surface-variant">—</span>
                        <?php endif; ?>
                      </td>
                    <?php endforeach; ?>
                    <td class="px-4 py-3 text-center whitespace-nowrap">
                      <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold <?php echo $trendClass; ?>">
                        <span class="material-symbols-outlined text-[14px]"><?php echo $trendIcon; ?></span>
                        <?php echo html_escape($data['trend']); ?>
                      </span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Qualitative Assessment -->
      <div class="p-4 rounded-xl bg-surface-container-low border border-outline-variant/50 space-y-2">
        <span class="text-[11px] text-on-surface-variant uppercase font-semibold block">Academic Coordinator Summary</span>
        <p class="text-body-md text-on-surface italic leading-relaxed">
          "The progress report tracks cumulative academic growth over the scholastic term. Consistent performance patterns and subject trend vectors assist in targeted academic counseling."
        </p>
      </div>

      <!-- Signatures Footer -->
      <div class="grid grid-cols-2 gap-8 pt-8 text-center text-body-md">
        <div>
          <div class="border-t border-outline-variant/80 pt-2 font-medium text-on-surface">Academic Counselor / Class Teacher</div>
          <span class="text-[11px] text-on-surface-variant">Signature</span>
        </div>
        <div>
          <div class="border-t border-outline-variant/80 pt-2 font-medium text-on-surface">Principal / Head of Institution</div>
          <span class="text-[11px] text-on-surface-variant">Seal & Signature</span>
        </div>
      </div>
    </div>
