<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

    <div class="flex items-center justify-between gap-4 mb-6 no-print">
      <div class="flex items-center gap-2">
        <a href="<?php echo site_url('students/transfers'); ?>" class="inline-flex items-center gap-1 px-3 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-label-md text-on-surface-variant hover:bg-surface-container-high"><span class="material-symbols-outlined text-[18px]">arrow_back</span>Back to Transfers</a>
      </div>
      <div class="flex items-center gap-2">
        <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-lg bg-primary text-on-primary text-label-md hover:bg-primary/90 transition-colors shadow-sm cursor-pointer"><span class="material-symbols-outlined text-[18px]">print</span>Print Certificate</button>
      </div>
    </div>

    <style>
      @media print {
        body { background: #fff !important; margin: 0; padding: 0; }
        .no-print, #sidebar-root, #header-root { display: none !important; }
        .tc-container { border: 3px double #000 !important; box-shadow: none !important; margin: 0 auto !important; width: 100% !important; padding: 25px !important; }
      }
    </style>

    <!-- Certificate Document Container -->
    <div class="tc-container elevation-2 rounded-2xl bg-surface-container-lowest border-2 border-outline-variant p-10 max-w-3xl mx-auto my-4 text-on-surface">
      
      <!-- School Header -->
      <div class="text-center border-b-2 border-primary/40 pb-6 mb-6">
        <div class="text-2xl font-bold uppercase tracking-wider text-primary">Senior Secondary School</div>
        <div class="text-xs text-on-surface-variant uppercase tracking-widest mt-0.5">Affiliated to CBSE, New Delhi · Affiliation No. 930842 · School Code 75210</div>
        <div class="text-xs text-on-surface-variant mt-1">Knowledge Park Road, Kochi, Ernakulam, Kerala — 682030 | Phone: +91 484 2900000</div>
        <div class="mt-4 inline-block px-6 py-1.5 rounded-full bg-surface-container-high font-bold text-sm uppercase tracking-wider text-on-surface border border-outline-variant">
          Transfer Certificate / School Leaving Certificate
        </div>
      </div>

      <!-- TC Metadata Top -->
      <div class="flex justify-between items-center text-sm mb-6 border-b border-outline-variant/40 pb-3">
        <div><strong class="text-on-surface">TC No:</strong> <span class="font-mono font-bold text-primary"><?php echo html_escape($transfer->tc_number); ?></span></div>
        <div><strong class="text-on-surface">Admission No:</strong> <span class="font-mono font-bold"><?php echo html_escape($transfer->admission_number); ?></span></div>
        <div><strong class="text-on-surface">Date:</strong> <span><?php echo date('d M Y', strtotime($transfer->transfer_date)); ?></span></div>
      </div>

      <!-- Certificate Particulars List -->
      <table class="w-full text-sm border-collapse mb-8">
        <tbody>
          <tr class="border-b border-outline-variant/30">
            <td class="py-2.5 w-1/3 text-on-surface-variant font-medium">1. Name of Pupil</td>
            <td class="py-2.5 font-bold uppercase text-on-surface"><?php echo html_escape($transfer->first_name . ' ' . $transfer->last_name); ?></td>
          </tr>
          <tr class="border-b border-outline-variant/30">
            <td class="py-2.5 text-on-surface-variant font-medium">2. Father's / Guardian's Name</td>
            <td class="py-2.5 font-medium text-on-surface"><?php echo html_escape($transfer->guardian_name); ?> (<?php echo html_escape($transfer->guardian_relation ?: 'Father'); ?>)</td>
          </tr>
          <tr class="border-b border-outline-variant/30">
            <td class="py-2.5 text-on-surface-variant font-medium">3. Gender</td>
            <td class="py-2.5 text-on-surface"><?php echo html_escape($transfer->gender); ?></td>
          </tr>
          <tr class="border-b border-outline-variant/30">
            <td class="py-2.5 text-on-surface-variant font-medium">4. Date of Birth (in figures & words)</td>
            <td class="py-2.5 text-on-surface font-medium"><?php echo date('d-m-Y', strtotime($transfer->date_of_birth)); ?> (<?php echo date('jS F, Y', strtotime($transfer->date_of_birth)); ?>)</td>
          </tr>
          <tr class="border-b border-outline-variant/30">
            <td class="py-2.5 text-on-surface-variant font-medium">5. Class in which pupil last studied</td>
            <td class="py-2.5 font-bold text-on-surface"><?php echo html_escape($transfer->prev_class ?: 'Grade 10'); ?></td>
          </tr>
          <tr class="border-b border-outline-variant/30">
            <td class="py-2.5 text-on-surface-variant font-medium">6. Academic Session</td>
            <td class="py-2.5 text-on-surface"><?php echo html_escape($transfer->year_name ?: '2026-2027'); ?></td>
          </tr>
          <tr class="border-b border-outline-variant/30">
            <td class="py-2.5 text-on-surface-variant font-medium">7. Whether school dues cleared</td>
            <td class="py-2.5 font-medium text-secondary"><?php echo ($transfer->dues_cleared == 1) ? 'Yes, All Dues Fully Paid' : 'Pending'; ?></td>
          </tr>
          <tr class="border-b border-outline-variant/30">
            <td class="py-2.5 text-on-surface-variant font-medium">8. Reason for leaving the school</td>
            <td class="py-2.5 font-medium text-on-surface"><?php echo html_escape($transfer->reason); ?></td>
          </tr>
          <tr class="border-b border-outline-variant/30">
            <td class="py-2.5 text-on-surface-variant font-medium">9. General Conduct</td>
            <td class="py-2.5 font-medium text-on-surface"><?php echo html_escape($transfer->conduct ?: 'Good'); ?></td>
          </tr>
          <tr class="border-b border-outline-variant/30">
            <td class="py-2.5 text-on-surface-variant font-medium">10. Any other remarks</td>
            <td class="py-2.5 text-on-surface-variant"><?php echo html_escape($transfer->remarks ?: 'Nil'); ?></td>
          </tr>
        </tbody>
      </table>

      <!-- Signatures Footer -->
      <div class="grid grid-cols-3 gap-8 pt-16 text-center text-xs text-on-surface">
        <div>
          <div class="border-t border-outline-variant pt-2 font-semibold">Prepared By (Clerk)</div>
        </div>
        <div>
          <div class="border-t border-outline-variant pt-2 font-semibold">Checked By (Admin Office)</div>
        </div>
        <div>
          <div class="border-t border-primary pt-2 font-bold text-primary">Principal's Signature & Seal</div>
        </div>
      </div>

    </div>
