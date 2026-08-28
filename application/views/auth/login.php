<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Login - School Management</title>
<script src="<?php echo base_url('assets/vendor/tailwind.min.js'); ?>"></script>
<link href="<?php echo base_url('assets/fonts/material-symbols.css'); ?>" rel="stylesheet"/>
<link href="<?php echo base_url('assets/fonts/inter.css'); ?>" rel="stylesheet"/>
<link href="<?php echo base_url('assets/app.css'); ?>" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "primary-fixed": "#d8e3fb", "surface-container-high": "#e6e8ea", "surface-dim": "#d8dadc",
                        "tertiary-container": "#422000", "on-secondary-fixed": "#002114", "on-tertiary-fixed-variant": "#6e3900",
                        "tertiary": "#240f00", "on-surface": "#191c1e", "on-secondary": "#ffffff", "background": "#f7f9fb",
                        "on-tertiary-fixed": "#2f1500", "on-surface-variant": "#45474c", "on-primary-fixed-variant": "#3c475a",
                        "on-background": "#191c1e", "on-primary-container": "#8590a6", "surface-tint": "#545f73",
                        "surface-container-lowest": "#ffffff", "surface-container-highest": "#e0e3e5", "surface-container-low": "#f2f4f6",
                        "on-error-container": "#93000a", "on-secondary-fixed-variant": "#005137", "surface": "#f7f9fb",
                        "on-tertiary": "#ffffff", "primary-fixed-dim": "#bcc7de", "on-primary": "#ffffff",
                        "on-secondary-container": "#00714e", "on-tertiary-container": "#d97705", "outline": "#75777d",
                        "secondary": "#006c4a", "inverse-primary": "#bcc7de", "surface-variant": "#e0e3e5",
                        "secondary-container": "#82f5c1", "primary": "#091426", "outline-variant": "#c5c6cd",
                        "surface-bright": "#f7f9fb", "tertiary-fixed-dim": "#ffb77d", "secondary-fixed": "#85f8c4",
                        "error-container": "#ffdad6", "inverse-on-surface": "#eff1f3", "tertiary-fixed": "#ffdcc3",
                        "on-primary-fixed": "#111c2d", "primary-container": "#1e293b", "error": "#ba1a1a",
                        "secondary-fixed-dim": "#68dba9", "on-error": "#ffffff", "inverse-surface": "#2d3133",
                        "surface-container": "#eceef0"
                    },
                    "borderRadius": { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" },
                    "spacing": { "md": "16px", "grid-gutter": "20px", "xs": "4px", "grid-margin": "24px", "sm": "12px", "lg": "24px", "xl": "32px", "base": "8px" },
                    "fontFamily": {
                        "body-lg": ["Inter"], "headline-md": ["Inter"], "body-md": ["Inter"], "label-md": ["Inter"],
                        "headline-xl": ["Inter"], "headline-lg": ["Inter"], "data-tabular": ["Inter"], "headline-lg-mobile": ["Inter"]
                    },
                    "fontSize": {
                        "body-lg": ["16px", { "lineHeight": "24px", "fontWeight": "400" }],
                        "headline-md": ["20px", { "lineHeight": "28px", "fontWeight": "600" }],
                        "body-md": ["14px", { "lineHeight": "20px", "fontWeight": "400" }],
                        "label-md": ["12px", { "lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "600" }],
                        "headline-xl": ["36px", { "lineHeight": "44px", "letterSpacing": "-0.02em", "fontWeight": "700" }],
                        "headline-lg": ["28px", { "lineHeight": "36px", "letterSpacing": "-0.01em", "fontWeight": "600" }],
                        "data-tabular": ["14px", { "lineHeight": "20px", "fontWeight": "500" }],
                        "headline-lg-mobile": ["24px", { "lineHeight": "32px", "fontWeight": "600" }]
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-background text-on-background min-h-screen flex items-center justify-center login-bg p-4 font-body-lg" data-page="login">
<?php if ($this->session->flashdata('error')): ?>
<div data-testid="login-error" class="fixed top-4 left-1/2 -translate-x-1/2 z-50 bg-error-container text-on-error-container px-4 py-2.5 rounded-lg shadow-sm text-body-md font-body-md">
  <?php echo html_escape($this->session->flashdata('error')); ?>
</div>
<?php endif; ?>
<div class="glass-card w-full max-w-md rounded-xl shadow-[0_10px_25px_-5px_rgba(0,0,0,0.1),_0_8px_10px_-6px_rgba(0,0,0,0.1)] border border-outline-variant/30 overflow-hidden relative z-10">
<div class="h-2 w-full bg-gradient-to-r from-secondary to-primary"></div>
<div class="p-8">
<div class="text-center mb-8">
<div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary-fixed mb-4">
<span class="material-symbols-outlined text-primary text-3xl" style="font-variation-settings: 'FILL' 1;">school</span>
</div>
<h1 class="font-headline-lg text-headline-lg text-primary">School</h1>
<p class="font-body-md text-body-md text-on-surface-variant mt-2">Sign in to access your administrative dashboard.</p>
</div>
<?php echo form_open('auth/login', array('id' => 'login-form', 'class' => 'space-y-6', 'data-testid' => 'login-form')); ?>
<div>
<label class="block font-label-md text-label-md text-on-surface mb-2" for="email">Email Address or Username</label>
<div class="relative">
<div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
<span class="material-symbols-outlined text-on-surface-variant/70 text-sm">person</span>
</div>
<input data-testid="login-email" class="block w-full pl-10 pr-3 py-2.5 border border-outline-variant rounded-lg bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary transition-colors placeholder-on-surface-variant/50" id="email" name="email" placeholder="Email address or username" required="" type="text" value="<?php echo set_value('email'); ?>"/>
</div>
</div>
<div>
<label class="block font-label-md text-label-md text-on-surface mb-2" for="password">Password</label>
<div class="relative">
<div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
<span class="material-symbols-outlined text-on-surface-variant/70 text-sm">lock</span>
</div>
<input data-testid="login-password" class="block w-full pl-10 pr-3 py-2.5 border border-outline-variant rounded-lg bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:ring-2 focus:ring-primary/10 focus:border-primary transition-colors placeholder-on-surface-variant/50" id="password" name="password" placeholder="••••••••" required="" type="password"/>
</div>
</div>
<div class="flex items-center justify-between">
<div class="flex items-center">
<input class="h-4 w-4 rounded border-outline-variant text-secondary focus:ring-secondary/50 bg-surface-container-lowest cursor-pointer" id="remember-me" name="remember-me" type="checkbox"/>
<label class="ml-2 block font-body-md text-body-md text-on-surface-variant cursor-pointer" for="remember-me">
                            Remember me
                        </label>
</div>
<div class="text-sm">
<a class="font-label-md text-label-md text-secondary hover:text-on-secondary-fixed-variant transition-colors" href="#">
                            Forgot password?
                        </a>
</div>
</div>
<div>
<button data-testid="login-submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm font-label-md text-label-md text-on-secondary bg-secondary hover:bg-on-secondary-fixed-variant focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-secondary transition-colors duration-200 active:scale-[0.98]" type="submit">
                        Sign In
                    </button>
</div>
<?php echo form_close(); ?>
</div>
<div class="bg-surface-container-low px-8 py-4 border-t border-outline-variant/30 text-center">
<p class="font-body-md text-body-md text-on-surface-variant/80 text-sm">
                Need help? <a class="text-secondary hover:underline font-medium" href="#">Contact IT Support</a>
</p>
</div>
</div>
</body></html>
