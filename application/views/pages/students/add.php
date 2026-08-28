<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

    <?php echo form_open('students/add', array('id' => 'student-add-form', 'data-testid' => 'student-add-form')); ?>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h2 class="font-headline-md text-headline-md text-on-surface">Student Admission</h2>
        <p class="text-body-md font-body-md text-on-surface-variant mt-1">Enter the student's personal and admission details.</p>
      </div>
      <div class="flex items-center gap-2 shrink-0">
        <a href="<?php echo site_url('students'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md text-label-md hover:bg-surface-container-high transition-colors"><span class="material-symbols-outlined text-[18px]">close</span>Cancel</a>
      </div>
    </div>
  
<div class="flex items-center gap-2 mb-6 overflow-x-auto">
  <div class="flex items-center gap-2 shrink-0"><div class="w-8 h-8 rounded-full flex items-center justify-center text-[13px] font-semibold bg-secondary text-on-secondary">1</div><span class="text-body-md font-body-md text-on-surface font-medium">Student Details</span><span class='text-outline-variant'>—</span></div>
  <div class="flex items-center gap-2 shrink-0"><div class="w-8 h-8 rounded-full flex items-center justify-center text-[13px] font-semibold bg-surface-container-high text-on-surface-variant">2</div><span class="text-body-md font-body-md text-on-surface-variant">Academic Details</span><span class='text-outline-variant'>—</span></div>
  <div class="flex items-center gap-2 shrink-0"><div class="w-8 h-8 rounded-full flex items-center justify-center text-[13px] font-semibold bg-surface-container-high text-on-surface-variant">3</div><span class="text-body-md font-body-md text-on-surface-variant">Parent / Guardian</span></div>
</div>
  
    <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
      <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/50">
        <h3 class="font-headline-md text-headline-md text-on-surface">Student Details</h3>
        <div class="flex items-center gap-2"></div>
      </div>
      <div class="p-5">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      
    <div class="">
      <label class="block font-label-md text-label-md text-on-surface mb-1.5">Admission Number <span class="text-error">*</span></label>
      <input data-testid="student-admission-no" type="text" name="admission_number" required value="EDU<?php echo date('Y') . sprintf('%03d', rand(10, 999)); ?>" placeholder="e.g. EDU2026009" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
    </div>
      
    <div class="">
      <label class="block font-label-md text-label-md text-on-surface mb-1.5">First Name <span class="text-error">*</span></label>
      <input data-testid="student-first-name" type="text" name="first_name" required placeholder="e.g. Aarav" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
    </div>
      
    <div class="">
      <label class="block font-label-md text-label-md text-on-surface mb-1.5">Last Name</label>
      <input data-testid="student-last-name" type="text" name="last_name" placeholder="e.g. Nair" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
    </div>
      
    <div class="">
      <label class="block font-label-md text-label-md text-on-surface mb-1.5">Gender</label>
      <select name="gender" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary">
        <option value="Male">Male</option>
        <option value="Female">Female</option>
        <option value="Other">Other</option>
      </select>
    </div>
      
    <div class="">
      <label class="block font-label-md text-label-md text-on-surface mb-1.5">Date of Birth</label>
      <input type="date" name="date_of_birth" value="2012-06-15" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
    </div>
      
    <div class="">
      <label class="block font-label-md text-label-md text-on-surface mb-1.5">Blood Group</label>
      <select name="blood_group" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary">
        <option value="A+">A+</option>
        <option value="A-">A-</option>
        <option value="B+">B+</option>
        <option value="B-">B-</option>
        <option value="O+">O+</option>
        <option value="O-">O-</option>
        <option value="AB+">AB+</option>
        <option value="AB-">AB-</option>
      </select>
    </div>
      
    <div class="">
      <label class="block font-label-md text-label-md text-on-surface mb-1.5">Class</label>
      <select data-testid="student-class-select" name="class_id" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary">
        <?php foreach ($classes as $cls): ?>
          <option value="<?php echo $cls->class_id; ?>"><?php echo html_escape($cls->class_name); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
      
    <div class="">
      <label class="block font-label-md text-label-md text-on-surface mb-1.5">Section</label>
      <select data-testid="student-section-select" name="section_id" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary">
        <?php foreach ($sections as $sec): ?>
          <option value="<?php echo $sec->section_id; ?>"><?php echo html_escape($sec->class_name . ' ' . $sec->section_name); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
      
    <div class="">
      <label class="block font-label-md text-label-md text-on-surface mb-1.5">Roll Number</label>
      <input type="text" name="roll_number" placeholder="e.g. 15" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
    </div>
      
    <div class="">
      <label class="block font-label-md text-label-md text-on-surface mb-1.5">Guardian Name</label>
      <input type="text" name="guardian_name" placeholder="e.g. Suresh Nair" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
    </div>
      
    <div class="">
      <label class="block font-label-md text-label-md text-on-surface mb-1.5">Guardian Phone</label>
      <input data-testid="student-guardian-phone" type="text" name="guardian_phone" placeholder="+91 98470 11223" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
    </div>
      
    <div class="sm:col-span-2">
      <label class="block font-label-md text-label-md text-on-surface mb-1.5">Address</label>
      <input type="text" name="address" placeholder="House name, Place, District, PIN" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
    </div>
    </div>
    <div class="flex justify-end gap-3 mt-6 pt-5 border-t border-outline-variant/50">
      <a href="<?php echo site_url('students'); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md text-label-md hover:bg-surface-container-high transition-colors">Cancel</a>
      <button data-testid="student-submit-btn" type="submit" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md text-label-md hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer"><span class="material-symbols-outlined text-[18px]">check</span>Save Student</button>
    </div>
  </div>
    </div>
    <?php echo form_close(); ?>

