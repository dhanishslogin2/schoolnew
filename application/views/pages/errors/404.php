<!DOCTYPE html>
<html class="light" lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title><?php echo html_escape($title ?? '404 - Page Not Found'); ?> - School</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
  <link href="<?php echo base_url('assets/app.css'); ?>" rel="stylesheet"/>
  <script id="tailwind-config">
  tailwind.config = {
      darkMode: "class",
      theme: {
          extend: {
              colors: {
                  "secondary": "#006c4a",
                  "primary": "#091426",
                  "background": "#f7f9fb",
                  "on-background": "#191c1e",
                  "surface-container-lowest": "#ffffff",
                  "outline-variant": "#c5c6cd"
              }
          }
      }
  }
  </script>
</head>
<body class="bg-background text-on-background min-h-screen flex items-center justify-center p-4 font-['Inter']">
  <div class="max-w-md w-full text-center bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/60 shadow-xl elevation-2">
    <div class="w-16 h-16 rounded-2xl bg-secondary/10 text-secondary flex items-center justify-center mx-auto mb-4">
      <span class="material-symbols-outlined text-[36px]">travel_explore</span>
    </div>
    <h1 class="text-3xl font-bold text-primary tracking-tight mb-2">404 - Page Not Found</h1>
    <p class="text-gray-600 text-sm mb-6 leading-relaxed">
      <?php echo html_escape($message ?? 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.'); ?>
    </p>
    <div class="flex items-center justify-center gap-3">
      <a href="<?php echo site_url('dashboard'); ?>" class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-secondary text-white text-sm font-semibold hover:bg-secondary/90 transition-all shadow-sm">
        <span class="material-symbols-outlined text-[18px]">home</span>Go to Dashboard
      </a>
      <a href="javascript:history.back()" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-outline-variant text-gray-700 bg-surface-container-lowest text-sm font-medium hover:bg-gray-100 transition-colors">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>Go Back
      </a>
    </div>
  </div>
</body>
</html>
