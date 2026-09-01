<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
  $fullName = trim(($student->first_name ?: '') . ' ' . ($student->last_name ?: ''));
  $nameParts = explode(' ', $fullName);
  $initials = '';
  foreach ($nameParts as $np) { if (!empty($np)) $initials .= strtoupper($np[0]); }
  if (strlen($initials) > 2) $initials = substr($initials, 0, 2);
  if (empty($initials)) $initials = 'ST';
  $hasPhoto = (!empty($student->photo) && file_exists(FCPATH . 'uploads/students/' . $student->photo));
?>

<?php echo form_open_multipart('students/edit/' . $student_id, array('id' => 'student-edit-form')); ?>
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
      <h2 class="font-headline-md text-headline-md text-on-surface">Edit Student</h2>
      <p class="text-body-md font-body-md text-on-surface-variant mt-1">Update student personal, profile image, and academic information.</p>
    </div>
    <div class="flex items-center gap-2 shrink-0">
      <a href="<?php echo site_url('students/profile/' . $student_id); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md hover:bg-surface-container-high transition-colors">
        <span class="material-symbols-outlined text-[18px]">close</span>Cancel
      </a>
    </div>
  </div>

  <?php if (validation_errors() || !empty($photo_error)): ?>
    <div class="p-4 mb-5 rounded-xl bg-error-container/30 border border-error/30 text-error text-body-md space-y-1">
      <?php echo validation_errors(); ?>
      <?php if (!empty($photo_error)): ?>
        <div>• <?php echo html_escape($photo_error); ?></div>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <div class="elevation-1 rounded-xl bg-surface-container-lowest border border-outline-variant/50 overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-outline-variant/50">
      <h3 class="font-headline-md text-headline-md text-on-surface">Student Details</h3>
      <div class="flex items-center gap-2"></div>
    </div>
    <div class="p-5">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

        <!-- Student Profile Image (3:4 Fixed Ratio) -->
        <div class="sm:col-span-2">
          <label class="block font-label-md text-label-md text-on-surface mb-1.5">
            Student Profile Image
            <span class="text-on-surface-variant font-normal text-xs">(3:4 Portrait · JPG, JPEG, PNG, WEBP · Max 3 MB)</span>
          </label>

          <input type="hidden" name="cropped_image_data" id="cropped_image_data" value=""/>
          <input type="hidden" name="remove_photo" id="remove_photo" value="0"/>

          <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 p-4 rounded-xl border border-outline-variant/60 bg-surface-container-low">
            <!-- 3:4 Aspect Ratio Preview Box (80px x 107px) -->
            <div id="photo-preview-container" class="relative w-20 h-[107px] rounded-xl overflow-hidden border border-outline-variant/80 bg-surface-container-lowest flex items-center justify-center shrink-0 shadow-xs">
              <img id="photo-preview-img" 
                   src="<?php echo $hasPhoto ? base_url('uploads/students/' . $student->photo) : ''; ?>" 
                   alt="<?php echo html_escape($fullName); ?>" 
                   class="w-full h-full object-cover <?php echo $hasPhoto ? '' : 'hidden'; ?>"/>
              
              <div id="photo-placeholder-avatar" class="w-full h-full bg-primary-fixed text-primary flex flex-col items-center justify-center font-bold <?php echo $hasPhoto ? 'hidden' : ''; ?>">
                <span class="text-xl"><?php echo html_escape($initials); ?></span>
                <span class="text-[9px] font-medium opacity-70">3:4</span>
              </div>
            </div>

            <!-- Upload Controls & Status -->
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 flex-wrap mb-1.5">
                <button type="button" id="btn-choose-photo" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-secondary text-on-secondary text-label-md hover:bg-on-secondary-fixed-variant transition-colors shadow-2xs cursor-pointer">
                  <span class="material-symbols-outlined text-[17px]">photo_camera</span>
                  <span id="photo-btn-label"><?php echo $hasPhoto ? 'Change Photo' : 'Upload Photo'; ?></span>
                </button>
                <input type="file" id="student_image_file" name="student_image" accept="image/jpeg,image/png,image/jpg,image/webp,.jpg,.jpeg,.png,.webp" class="hidden"/>
                
                <button type="button" id="photo-remove-btn" class="inline-flex items-center gap-1 px-3 py-2 rounded-lg border border-error/30 text-error hover:bg-error-container/40 text-label-md transition-colors cursor-pointer <?php echo $hasPhoto ? '' : 'hidden'; ?>">
                  <span class="material-symbols-outlined text-[16px]">delete</span>
                  <span>Remove</span>
                </button>
              </div>
              <p id="photo-file-status" class="text-xs text-on-surface-variant">
                <?php echo $hasPhoto ? 'Current photo loaded. You can change or remove it.' : 'No photo uploaded. Select an image to crop.'; ?>
              </p>
              <p class="field-error text-error text-[11px] mt-1 hidden" id="err-student_image"></p>
            </div>
          </div>
        </div>

        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5">Admission Number <span class="text-error">*</span></label>
          <input type="text" name="admission_number" required value="<?php echo html_escape(isset($student->admission_number) ? $student->admission_number : ''); ?>" placeholder="e.g. EDU2026009" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
        </div>
          
        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5">First Name <span class="text-error">*</span></label>
          <input type="text" name="first_name" required value="<?php echo html_escape(isset($student->first_name) ? $student->first_name : ''); ?>" placeholder="e.g. Aarav" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
        </div>
          
        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5">Last Name</label>
          <input type="text" name="last_name" value="<?php echo html_escape(isset($student->last_name) ? $student->last_name : ''); ?>" placeholder="e.g. Nair" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
        </div>
          
        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5">Gender</label>
          <select name="gender" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary">
            <option value="Male" <?php echo ($student->gender === 'Male') ? 'selected' : ''; ?>>Male</option>
            <option value="Female" <?php echo ($student->gender === 'Female') ? 'selected' : ''; ?>>Female</option>
            <option value="Other" <?php echo ($student->gender === 'Other') ? 'selected' : ''; ?>>Other</option>
          </select>
        </div>
          
        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5">Date of Birth</label>
          <input type="date" name="date_of_birth" value="<?php echo html_escape($student->date_of_birth); ?>" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
        </div>
          
        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5">Blood Group</label>
          <select name="blood_group" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary">
            <?php foreach (array('A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-') as $bg): ?>
              <option value="<?php echo $bg; ?>" <?php echo ($student->blood_group === $bg) ? 'selected' : ''; ?>><?php echo $bg; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
          
        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5">Class</label>
          <select name="class_id" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary">
            <?php foreach ($classes as $cls): ?>
              <option value="<?php echo $cls->class_id; ?>" <?php echo ($student->class_id == $cls->class_id) ? 'selected' : ''; ?>><?php echo html_escape($cls->class_name); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
          
        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5">Section</label>
          <select name="section_id" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary">
            <?php foreach ($sections as $sec): ?>
              <option value="<?php echo $sec->section_id; ?>" <?php echo ($student->section_id == $sec->section_id) ? 'selected' : ''; ?>><?php echo html_escape($sec->class_name . ' ' . $sec->section_name); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
          
        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5">Roll Number</label>
          <input type="text" name="roll_number" value="<?php echo html_escape(isset($student->roll_number) ? $student->roll_number : ''); ?>" placeholder="e.g. 15" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
        </div>
          
        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5">Guardian Name</label>
          <input type="text" name="guardian_name" value="<?php echo html_escape(isset($student->guardian_name) ? $student->guardian_name : ''); ?>" placeholder="e.g. Suresh Nair" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
        </div>
          
        <div>
          <label class="block font-label-md text-label-md text-on-surface mb-1.5">Guardian Phone</label>
          <input type="text" name="guardian_phone" value="<?php echo html_escape(isset($student->guardian_phone) ? $student->guardian_phone : ''); ?>" placeholder="+91 98470 11223" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
        </div>
          
        <div class="sm:col-span-2">
          <label class="block font-label-md text-label-md text-on-surface mb-1.5">Address</label>
          <input type="text" name="address" value="<?php echo html_escape(isset($student->address) ? $student->address : ''); ?>" placeholder="House name, Place, District, PIN" class="w-full px-3 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-body-md font-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary placeholder-on-surface-variant/50"/>
        </div>

      </div>

      <div class="flex justify-end gap-3 mt-6 pt-5 border-t border-outline-variant/50">
        <a href="<?php echo site_url('students/profile/' . $student_id); ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant bg-surface-container-lowest text-label-md hover:bg-surface-container-high transition-colors">Cancel</a>
        <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-lg bg-secondary text-on-secondary text-label-md hover:bg-on-secondary-fixed-variant transition-colors shadow-sm cursor-pointer font-medium">
          <span class="material-symbols-outlined text-[18px]">check</span>Update Student
        </button>
      </div>
    </div>
  </div>
<?php echo form_close(); ?>

<!-- ═══════════════════════════════════════════════════════════════════════
     IMAGE CROPPER MODAL (FIXED 3:4 PORTRAIT)
════════════════════════════════════════════════════════════════════════ -->
<div id="student-cropper-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/75 p-4 hidden backdrop-blur-sm">
  <div class="elevation-3 rounded-2xl bg-surface-container-lowest border border-outline-variant w-full max-w-xl overflow-hidden flex flex-col max-h-[92vh] shadow-2xl">
    
    <!-- Modal Header -->
    <div class="flex items-center justify-between px-6 py-4 border-b border-outline-variant/60 bg-surface-container-low">
      <div class="flex items-center gap-2">
        <span class="material-symbols-outlined text-primary text-[22px]">crop</span>
        <h3 class="font-headline-md text-headline-md text-on-surface text-base font-bold">Crop Student Profile Image</h3>
      </div>
      <button type="button" id="btn-student-cropper-close" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container-high transition-colors cursor-pointer" title="Close">
        <span class="material-symbols-outlined text-[20px]">close</span>
      </button>
    </div>

    <!-- Modal Body / Cropper Area -->
    <div class="p-4 bg-slate-950/80 flex-1 overflow-hidden flex items-center justify-center relative select-none" style="min-height: 300px; max-height: 380px;">
      <div class="w-full h-full flex items-center justify-center overflow-hidden">
        <img id="student-cropper-image-target" src="" alt="Source Image" class="max-w-full block" style="max-height: 360px;"/>
      </div>
    </div>

    <!-- Modal Controls Toolbar -->
    <div class="px-6 py-3.5 bg-surface-container-lowest border-t border-outline-variant/40 flex flex-wrap items-center justify-between gap-4">
      
      <!-- Zoom Controls with Sync -->
      <div class="flex items-center gap-2 flex-1 min-w-[200px]">
        <button type="button" id="btn-student-cropper-zoom-out" title="Zoom Out" class="p-1.5 rounded-lg border border-outline-variant text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-colors cursor-pointer shrink-0">
          <span class="material-symbols-outlined text-[18px]">zoom_out</span>
        </button>
        <input type="range" id="student-cropper-zoom-range" min="0.1" max="3" step="0.05" value="1" class="w-full h-1.5 bg-surface-container-highest rounded-lg appearance-none cursor-pointer accent-primary" title="Zoom Slider"/>
        <button type="button" id="btn-student-cropper-zoom-in" title="Zoom In" class="p-1.5 rounded-lg border border-outline-variant text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-colors cursor-pointer shrink-0">
          <span class="material-symbols-outlined text-[18px]">zoom_in</span>
        </button>
      </div>

      <!-- Rotation & Reset Controls -->
      <div class="flex items-center gap-1.5 shrink-0">
        <button type="button" id="btn-student-cropper-rotate-left" title="Rotate Left (-90°)" class="px-2.5 py-1.5 rounded-lg border border-outline-variant text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-colors text-xs font-medium cursor-pointer inline-flex items-center gap-1">
          <span class="material-symbols-outlined text-[16px]">rotate_left</span>-90°
        </button>
        <button type="button" id="btn-student-cropper-rotate-right" title="Rotate Right (+90°)" class="px-2.5 py-1.5 rounded-lg border border-outline-variant text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-colors text-xs font-medium cursor-pointer inline-flex items-center gap-1">
          <span class="material-symbols-outlined text-[16px]">rotate_right</span>+90°
        </button>
        <button type="button" id="btn-student-cropper-reset" title="Reset View" class="px-2.5 py-1.5 rounded-lg border border-outline-variant text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-colors text-xs font-medium cursor-pointer inline-flex items-center gap-1">
          <span class="material-symbols-outlined text-[16px]">restart_alt</span>Reset
        </button>
      </div>
    </div>

    <!-- Modal Footer -->
    <div class="px-6 py-3.5 border-t border-outline-variant/60 bg-surface-container-low flex items-center justify-between gap-3">
      <button type="button" id="btn-student-cropper-change-file" class="text-label-md text-primary hover:underline font-medium cursor-pointer flex items-center gap-1 text-[13px]">
        <span class="material-symbols-outlined text-[16px]">folder_open</span>Choose Another Image
      </button>
      <div class="flex items-center gap-2.5">
        <button type="button" id="btn-student-cropper-cancel" class="px-4 py-2 rounded-lg border border-outline-variant text-on-surface-variant hover:bg-surface-container-high text-label-md cursor-pointer transition-colors">Cancel</button>
        <button type="button" id="btn-student-cropper-apply" class="px-5 py-2 rounded-lg bg-secondary text-on-secondary text-label-md hover:bg-on-secondary-fixed-variant transition-colors cursor-pointer shadow-sm flex items-center gap-1.5 font-medium">
          <span class="material-symbols-outlined text-[18px]">crop</span>Apply Crop
        </button>
      </div>
    </div>

  </div>
</div>

<script>
(function ($) {
    'use strict';

    var cropperInstance = null;
    var $fileInput      = $('#student_image_file');
    var $chooseBtn      = $('#btn-choose-photo');
    var $removeBtn      = $('#photo-remove-btn');
    var $photoBtnLabel  = $('#photo-btn-label');
    var $previewImg     = $('#photo-preview-img');
    var $placeholder    = $('#photo-placeholder-avatar');
    var $fileStatus     = $('#photo-file-status');
    var $hiddenCropped  = $('#cropped_image_data');
    var $removePhotoInp = $('#remove_photo');

    var $modal          = $('#student-cropper-modal');
    var cropperImgEl    = document.getElementById('student-cropper-image-target');
    var $zoomRange      = $('#student-cropper-zoom-range');
    var $zoomInBtn      = $('#btn-student-cropper-zoom-in');
    var $zoomOutBtn     = $('#btn-student-cropper-zoom-out');
    var $rotateLeftBtn  = $('#btn-student-cropper-rotate-left');
    var $rotateRightBtn = $('#btn-student-cropper-rotate-right');
    var $resetBtn       = $('#btn-student-cropper-reset');
    var $closeBtn       = $('#btn-student-cropper-close');
    var $cancelBtn      = $('#btn-student-cropper-cancel');
    var $applyBtn       = $('#btn-student-cropper-apply');
    var $changeFileBtn  = $('#btn-student-cropper-change-file');

    $chooseBtn.on('click', function () {
        $fileInput.val('');
        $fileInput.trigger('click');
    });

    if ($changeFileBtn.length) {
        $changeFileBtn.on('click', function () {
            $fileInput.val('');
            $fileInput.trigger('click');
        });
    }

    $fileInput.on('change', function () {
        var file = this.files[0];
        if (!file) return;

        $('#err-student_image').addClass('hidden').text('');

        // Format validation
        var allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
        var allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        var ext = file.name.split('.').pop().toLowerCase();

        if (allowedExts.indexOf(ext) === -1 && allowedMimes.indexOf(file.type) === -1) {
            alert('Please select a valid JPG, JPEG, PNG, or WEBP image.');
            $('#err-student_image').text('Please select a valid JPG, JPEG, PNG, or WEBP image.').removeClass('hidden');
            this.value = '';
            return;
        }

        // Size validation: 3 MB
        if (file.size > 3 * 1024 * 1024) {
            alert('Image size must not exceed 3 MB.');
            $('#err-student_image').text('Image size must not exceed 3 MB.').removeClass('hidden');
            this.value = '';
            return;
        }

        if (window.FileReader) {
            var reader = new FileReader();
            reader.onload = function (e) {
                openStudentCropper(e.target.result, file.name);
            };
            reader.onerror = function () {
                alert('Failed to read image file. Please try again.');
            };
            reader.readAsDataURL(file);
        }
    });

    function openStudentCropper(imgSrc, originalFileName) {
        if (typeof Cropper === 'undefined') {
            alert('Cropper library is loading. Please refresh the page.');
            return;
        }

        if (cropperInstance) {
            cropperInstance.destroy();
            cropperInstance = null;
        }

        $zoomRange.val(1);
        $modal.removeClass('hidden');

        var initialized = false;
        function triggerInit() {
            if (initialized) return;
            initialized = true;
            initCropperInstance(originalFileName);
        }

        cropperImgEl.onload = triggerInit;
        cropperImgEl.src = imgSrc;

        if (cropperImgEl.complete) {
            triggerInit();
        }
    }
    window.openStudentCropperModal = openStudentCropper;

    function initCropperInstance(originalFileName) {
        if (cropperInstance) return;
        try {
            cropperInstance = new Cropper(cropperImgEl, {
                aspectRatio: 3 / 4,
                viewMode: 1,
                autoCropArea: 0.9,
                responsive: true,
                restore: false,
                guides: true,
                center: true,
                highlight: false,
                checkOrientation: true,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
                ready: function () {
                    $zoomRange.val(1);
                },
                zoom: function (e) {
                    if (e.detail && typeof e.detail.ratio === 'number') {
                        var r = Math.min(3, Math.max(0.1, parseFloat(e.detail.ratio.toFixed(2))));
                        $zoomRange.val(r);
                    }
                }
            });
        } catch (ce) {
            console.error('Cropper init error:', ce);
        }
    }

    function closeStudentCropper() {
        $modal.addClass('hidden');
        if (cropperInstance) {
            cropperInstance.destroy();
            cropperInstance = null;
        }
        cropperImgEl.src = '';
        $fileInput.val('');
    }

    $closeBtn.on('click', closeStudentCropper);
    $cancelBtn.on('click', closeStudentCropper);

    // Zoom Range Slider
    $zoomRange.on('input', function () {
        if (!cropperInstance) return;
        var val = parseFloat(this.value);
        if (!isNaN(val) && val >= 0.1 && val <= 3) {
            cropperInstance.zoomTo(val);
        }
    });

    // Zoom In & Out Buttons
    $zoomInBtn.on('click', function () {
        if (!cropperInstance) return;
        cropperInstance.zoom(0.1);
    });
    $zoomOutBtn.on('click', function () {
        if (!cropperInstance) return;
        cropperInstance.zoom(-0.1);
    });

    // Rotate Left & Right Buttons
    $rotateLeftBtn.on('click', function () {
        if (!cropperInstance) return;
        cropperInstance.rotate(-90);
    });
    $rotateRightBtn.on('click', function () {
        if (!cropperInstance) return;
        cropperInstance.rotate(90);
    });

    // Reset Button
    $resetBtn.on('click', function () {
        if (!cropperInstance) return;
        cropperInstance.reset();
        $zoomRange.val(1);
    });

    // Apply Crop Button
    $applyBtn.on('click', function () {
        if (cropperInstance) {
            var canvas = cropperInstance.getCroppedCanvas({
                width: 600,
                height: 800,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high'
            });

            if (canvas) {
                var croppedBase64 = canvas.toDataURL('image/jpeg', 0.92);
                $hiddenCropped.val(croppedBase64);
                $removePhotoInp.val('0');

                // Update preview immediately
                $previewImg.attr('src', croppedBase64).removeClass('hidden');
                $placeholder.addClass('hidden');
                $removeBtn.removeClass('hidden');
                $photoBtnLabel.text('Change Photo');
                $fileStatus.text('New cropped photo ready to save.');
                $('#err-student_image').addClass('hidden').text('');
            }
        }

        closeStudentCropper();
    });

    /* Remove student photo */
    $removeBtn.on('click', function () {
        $hiddenCropped.val('');
        $removePhotoInp.val('1');
        $fileInput.val('');
        $previewImg.attr('src', '').addClass('hidden');
        $placeholder.removeClass('hidden');
        $removeBtn.addClass('hidden');
        $photoBtnLabel.text('Upload Photo');
        $fileStatus.text('Photo marked for removal. Save changes to update.');
        $('#err-student_image').addClass('hidden').text('');
    });

})(jQuery);
</script>
