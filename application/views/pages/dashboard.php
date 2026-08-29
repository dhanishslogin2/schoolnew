<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- =========================================================================
     Dashboard — School Management
     All figures are live from the database via Dashboard_model.
     ========================================================================= -->

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
  <div>
    <h2 class="font-headline-md text-headline-md text-on-surface">
      Welcome back, <?php echo html_escape($current_user->name ?? 'User'); ?>
    </h2>
    <p class="text-body-md font-body-md text-on-surface-variant mt-1">
      Here's what's happening across Login2 today &mdash; <?php echo date('d M Y', strtotime($today)); ?>.
    </p>
  </div>
  <div class="flex items-center gap-2 shrink-0">
    <a href="<?php echo site_url('students/add'); ?>"
       class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md hover:opacity-90 transition-colors shadow-sm">
      <span class="material-symbols-outlined text-[18px]">person_add</span>
      Add Student
    </a>
  </div>
</div>

<!-- ── Stat Cards ──────────────────────────────────────────────────────── -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">

  <!-- Total Students -->
  <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4">
    <div class="flex items-center justify-between">
      <div class="w-10 h-10 rounded-lg bg-primary-fixed text-primary flex items-center justify-center">
        <span class="material-symbols-outlined text-[20px]">group</span>
      </div>
    </div>
    <div class="mt-3 font-headline-lg text-headline-lg text-on-surface">
      <?php echo number_format($stats->total_students); ?>
    </div>
    <div class="text-body-md font-body-md text-on-surface-variant">Total Students</div>
  </div>

  <!-- Total Teachers -->
  <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4">
    <div class="flex items-center justify-between">
      <div class="w-10 h-10 rounded-lg bg-secondary-container text-on-secondary-container flex items-center justify-center">
        <span class="material-symbols-outlined text-[20px]">person</span>
      </div>
    </div>
    <div class="mt-3 font-headline-lg text-headline-lg text-on-surface">
      <?php echo number_format($stats->total_teachers); ?>
    </div>
    <div class="text-body-md font-body-md text-on-surface-variant">Total Teachers</div>
  </div>

  <!-- Total Staff -->
  <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4">
    <div class="flex items-center justify-between">
      <div class="w-10 h-10 rounded-lg bg-primary-fixed text-primary flex items-center justify-center">
        <span class="material-symbols-outlined text-[20px]">badge</span>
      </div>
    </div>
    <div class="mt-3 font-headline-lg text-headline-lg text-on-surface">
      <?php echo number_format($stats->total_staff); ?>
    </div>
    <div class="text-body-md font-body-md text-on-surface-variant">Total Staff</div>
  </div>

  <!-- Total Classes -->
  <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4">
    <div class="flex items-center justify-between">
      <div class="w-10 h-10 rounded-lg bg-tertiary-container/10 text-on-tertiary-container flex items-center justify-center">
        <span class="material-symbols-outlined text-[20px]">meeting_room</span>
      </div>
    </div>
    <div class="mt-3 font-headline-lg text-headline-lg text-on-surface">
      <?php echo number_format($stats->total_classes); ?>
    </div>
    <div class="text-body-md font-body-md text-on-surface-variant">Total Classes</div>
  </div>

  <!-- Today's Attendance -->
  <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4">
    <div class="flex items-center justify-between">
      <div class="w-10 h-10 rounded-lg bg-secondary-container text-on-secondary-container flex items-center justify-center">
        <span class="material-symbols-outlined text-[20px]">fact_check</span>
      </div>
      <?php if ($attendance->total_marked > 0): ?>
        <span class="text-[12px] font-semibold text-on-secondary-container"><?php echo $attendance->pct; ?>%</span>
      <?php endif; ?>
    </div>
    <div class="mt-3 font-headline-lg text-headline-lg text-on-surface">
      <?php echo $attendance->total_marked > 0 ? $attendance->pct . '%' : 'N/A'; ?>
    </div>
    <div class="text-body-md font-body-md text-on-surface-variant">Today's Attendance</div>
  </div>

  <!-- Fees Collected (MTD) -->
  <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4">
    <div class="flex items-center justify-between">
      <div class="w-10 h-10 rounded-lg bg-secondary-container text-on-secondary-container flex items-center justify-center">
        <span class="material-symbols-outlined text-[20px]">payments</span>
      </div>
    </div>
    <div class="mt-3 font-headline-lg text-headline-lg text-on-surface">
      <?php echo school_currency($fees->monthly_collection); ?>
    </div>
    <div class="text-body-md font-body-md text-on-surface-variant">Fees Collected (MTD)</div>
  </div>

  <!-- Pending Fees -->
  <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4">
    <div class="flex items-center justify-between">
      <div class="w-10 h-10 rounded-lg bg-error-container text-on-error-container flex items-center justify-center">
        <span class="material-symbols-outlined text-[20px]">receipt_long</span>
      </div>
    </div>
    <div class="mt-3 font-headline-lg text-headline-lg text-on-surface">
      <?php echo school_currency($fees->total_pending); ?>
    </div>
    <div class="text-body-md font-body-md text-on-surface-variant">Pending Fees</div>
  </div>

  <!-- New Admissions -->
  <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 p-4">
    <div class="flex items-center justify-between">
      <div class="w-10 h-10 rounded-lg bg-primary-fixed text-primary flex items-center justify-center">
        <span class="material-symbols-outlined text-[20px]">person_add</span>
      </div>
    </div>
    <div class="mt-3 font-headline-lg text-headline-lg text-on-surface">
      <?php echo number_format($stats->new_admissions); ?>
    </div>
    <div class="text-body-md font-body-md text-on-surface-variant">New Admissions (Month)</div>
  </div>

</div>

<!-- ── Middle Row: Attendance + Student Overview ───────────────────────── -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">

  <!-- Attendance Summary (2/3 width) -->
  <div class="lg:col-span-2 elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/50">
      <h3 class="font-headline-md text-headline-md text-on-surface">Today's Attendance Summary</h3>
      <a href="<?php echo site_url('attendance/reports'); ?>"
         class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md hover:bg-surface-container-high transition-colors">
        <span class="material-symbols-outlined text-[18px]">chevron_right</span>View Report
      </a>
    </div>
    <div class="p-5">
      <?php if ($attendance->total_marked > 0): ?>
        <div class="grid grid-cols-3 gap-4 mb-5 text-center">
          <div>
            <div class="font-headline-lg text-headline-lg text-on-secondary-container"><?php echo number_format($attendance->present); ?></div>
            <div class="text-body-md font-body-md text-on-surface-variant">Present</div>
          </div>
          <div>
            <div class="font-headline-lg text-headline-lg text-error"><?php echo number_format($attendance->absent); ?></div>
            <div class="text-body-md font-body-md text-on-surface-variant">Absent</div>
          </div>
          <div>
            <div class="font-headline-lg text-headline-lg text-on-tertiary-container"><?php echo number_format($attendance->late); ?></div>
            <div class="text-body-md font-body-md text-on-surface-variant">Late</div>
          </div>
        </div>
        <div class="w-full h-2.5 rounded-full bg-surface-container-high overflow-hidden flex">
          <div class="h-full bg-secondary" style="width:<?php echo $attendance->pct; ?>%"></div>
          <div class="h-full bg-tertiary-fixed-dim" style="width:<?php echo $attendance->late_pct; ?>%"></div>
          <div class="h-full bg-error" style="width:<?php echo $attendance->absent_pct; ?>%"></div>
        </div>
        <p class="text-body-md font-body-md text-on-surface-variant mt-2">
          <?php echo $attendance->pct; ?>% attendance across all active sections today.
        </p>
      <?php else: ?>
        <div class="text-center py-8">
          <span class="material-symbols-outlined text-[40px] text-on-surface-variant mb-2 block">event_busy</span>
          <p class="text-body-md font-body-md text-on-surface-variant">No attendance marked yet for today.</p>
          <a href="<?php echo site_url('attendance/daily'); ?>"
             class="mt-3 inline-flex items-center gap-1 text-secondary text-body-md font-body-md hover:underline">
            <span class="material-symbols-outlined text-[16px]">add</span>Mark Attendance
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Student Overview (1/3 width) -->
  <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/50">
      <h3 class="font-headline-md text-headline-md text-on-surface">Student Overview</h3>
    </div>
    <div class="p-5">
      <div class="space-y-3">
        <div class="flex items-center justify-between text-body-md font-body-md">
          <span class="text-on-surface-variant">New Admissions (this month)</span>
          <span class="font-semibold text-on-surface"><?php echo number_format($stats->new_admissions); ?></span>
        </div>
        <div class="flex items-center justify-between text-body-md font-body-md">
          <span class="text-on-surface-variant">Active Students</span>
          <span class="font-semibold text-on-surface"><?php echo number_format($stats->active_students); ?></span>
        </div>
        <div class="flex items-center justify-between text-body-md font-body-md">
          <span class="text-on-surface-variant">Boys / Girls</span>
          <span class="font-semibold text-on-surface">
            <?php echo number_format($stats->male_students); ?> / <?php echo number_format($stats->female_students); ?>
          </span>
        </div>

        <?php if (!empty($students_by_class)): ?>
        <div class="pt-2 border-t border-outline-variant/50">
          <div class="text-label-md font-label-md text-on-surface-variant uppercase mb-2">Students by Class</div>
          <?php
          // Find max for proportional bar widths
          $max_count = max(array_column((array)$students_by_class, 'student_count') ?: [1]);
          foreach ($students_by_class as $cls):
            $pct = $max_count > 0 ? round(($cls->student_count / $max_count) * 100) : 0;
          ?>
          <div class="flex items-center gap-2 mb-1.5">
            <span class="w-20 text-[12px] text-on-surface-variant truncate"><?php echo html_escape($cls->class_name); ?></span>
            <div class="flex-1 h-2 rounded-full bg-surface-container-high overflow-hidden">
              <div class="h-full bg-primary-container" style="width:<?php echo $pct; ?>%"></div>
            </div>
            <span class="text-[12px] text-on-surface-variant w-6 text-right"><?php echo $cls->student_count; ?></span>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

</div>

<!-- ── Bottom Row: Fees + Events + Notices ────────────────────────────── -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

  <!-- Fees Overview -->
  <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/50">
      <h3 class="font-headline-md text-headline-md text-on-surface">Fees Overview</h3>
      <a href="<?php echo site_url('fees'); ?>"
         class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md hover:bg-surface-container-high transition-colors">
        <span class="material-symbols-outlined text-[18px]">chevron_right</span>Open
      </a>
    </div>
    <div class="p-5">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <div class="text-body-md font-body-md text-on-surface-variant">Today's Collection</div>
          <div class="font-headline-md text-headline-md text-on-surface">
            <?php echo school_currency($fees->today_collection); ?>
          </div>
        </div>
        <div>
          <div class="text-body-md font-body-md text-on-surface-variant">Monthly Collection</div>
          <div class="font-headline-md text-headline-md text-on-surface">
            <?php echo school_currency($fees->monthly_collection); ?>
          </div>
        </div>
        <div>
          <div class="text-body-md font-body-md text-on-surface-variant">Pending Fees</div>
          <div class="font-headline-md text-headline-md text-on-tertiary-container">
            <?php echo school_currency($fees->total_pending); ?>
          </div>
        </div>
        <div>
          <div class="text-body-md font-body-md text-on-surface-variant">Overdue Fees</div>
          <div class="font-headline-md text-headline-md text-error">
            <?php echo school_currency($fees->overdue_amount); ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Upcoming Events -->
  <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/50">
      <h3 class="font-headline-md text-headline-md text-on-surface">Upcoming Events</h3>
    </div>
    <div class="p-5">
      <?php if (!empty($upcoming_events)): ?>
        <ul class="space-y-3">
          <?php foreach ($upcoming_events as $ev):
            $ev_date = new DateTime($ev->event_date);
          ?>
          <li class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-primary-fixed text-primary flex flex-col items-center justify-center text-[11px] font-semibold leading-none shrink-0">
              <span><?php echo $ev_date->format('d'); ?></span>
              <span><?php echo strtoupper($ev_date->format('M')); ?></span>
            </div>
            <div class="min-w-0">
              <div class="text-body-md font-body-md text-on-surface truncate"><?php echo html_escape($ev->title); ?></div>
              <div class="text-[12px] text-on-surface-variant">
                <?php echo html_escape($ev->audience); ?>
                <?php if (!empty($ev->venue)): ?> &middot; <?php echo html_escape($ev->venue); ?><?php endif; ?>
              </div>
            </div>
          </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <div class="text-center py-6">
          <span class="material-symbols-outlined text-[36px] text-on-surface-variant block mb-1">event</span>
          <p class="text-body-md font-body-md text-on-surface-variant">No upcoming events.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Recent Notices -->
  <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/50">
      <h3 class="font-headline-md text-headline-md text-on-surface">Recent Notices</h3>
      <a href="<?php echo site_url('communication/notices'); ?>"
         class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md hover:bg-surface-container-high transition-colors">
        <span class="material-symbols-outlined text-[18px]">chevron_right</span>View All
      </a>
    </div>
    <div class="p-5">
      <?php if (!empty($recent_notices)): ?>
        <ul class="divide-y divide-outline-variant/50">
          <?php foreach ($recent_notices as $notice): ?>
          <li class="py-3 first:pt-0 last:pb-0">
            <div class="flex items-start justify-between gap-2">
              <div>
                <div class="text-body-md font-body-md text-on-surface font-medium">
                  <?php echo html_escape($notice->title); ?>
                </div>
                <div class="text-[12px] text-on-surface-variant mt-0.5">
                  <?php echo html_escape($notice->posted_by); ?>
                </div>
              </div>
              <span class="text-[11px] text-on-surface-variant whitespace-nowrap">
                <?php echo school_timeago($notice->publish_date); ?>
              </span>
            </div>
          </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <div class="text-center py-6">
          <span class="material-symbols-outlined text-[36px] text-on-surface-variant block mb-1">notifications_none</span>
          <p class="text-body-md font-body-md text-on-surface-variant">No recent notices.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>

</div>
