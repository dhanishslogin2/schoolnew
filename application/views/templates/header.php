<!DOCTYPE html>
<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?php echo html_escape(($title ?? 'Dashboard') . ' - Login2'); ?></title>
<script src="<?php echo base_url('assets/vendor/tailwind.min.js'); ?>"></script>
<link href="<?php echo base_url('assets/fonts/material-symbols.css'); ?>" rel="stylesheet"/>
<link href="<?php echo base_url('assets/fonts/inter.css'); ?>" rel="stylesheet"/>
<link href="<?php echo base_url('assets/vendor/datatables/jquery.dataTables.min.css'); ?>" rel="stylesheet"/>
<link href="<?php echo base_url('assets/vendor/datatables/responsive.dataTables.min.css'); ?>" rel="stylesheet"/>
<link href="<?php echo base_url('assets/app.css'); ?>" rel="stylesheet"/>
<script src="<?php echo base_url('assets/vendor/jquery.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/vendor/datatables/jquery.dataTables.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/vendor/datatables/dataTables.responsive.min.js'); ?>"></script>
<script id="tailwind-config">
tailwind.config = {
    darkMode: "class",
    theme: {
        extend: {
            colors: {
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
            borderRadius: { DEFAULT: "0.25rem", lg: "0.5rem", xl: "0.75rem", full: "9999px" },
            spacing: { md: "16px", "grid-gutter": "20px", xs: "4px", "grid-margin": "24px", sm: "12px", lg: "24px", xl: "32px", base: "8px" },
            fontFamily: {
                "body-lg": ["Inter"], "headline-md": ["Inter"], "body-md": ["Inter"], "label-md": ["Inter"],
                "headline-xl": ["Inter"], "headline-lg": ["Inter"], "data-tabular": ["Inter"], "headline-lg-mobile": ["Inter"]
            },
            fontSize: {
                "body-lg": ["16px", { lineHeight: "24px", fontWeight: "400" }],
                "headline-md": ["20px", { lineHeight: "28px", fontWeight: "600" }],
                "body-md": ["14px", { lineHeight: "20px", fontWeight: "400" }],
                "label-md": ["12px", { lineHeight: "16px", letterSpacing: "0.05em", fontWeight: "600" }],
                "headline-xl": ["36px", { lineHeight: "44px", letterSpacing: "-0.02em", fontWeight: "700" }],
                "headline-lg": ["28px", { lineHeight: "36px", letterSpacing: "-0.01em", fontWeight: "600" }],
                "data-tabular": ["14px", { lineHeight: "20px", fontWeight: "500" }],
                "headline-lg-mobile": ["24px", { lineHeight: "32px", fontWeight: "600" }]
            }
        }
    }
}
</script>

</head>
<body class="bg-background text-on-background font-body-lg" data-page="<?php echo html_escape($page_key); ?>"<?php echo $breadcrumb ? ' data-breadcrumb=\'' . $breadcrumb . '\'' : ''; ?> >
<script>
window.APP_BASE_URL = "<?php echo base_url(); ?>";
window.CSRF_TOKEN_NAME = "<?php echo $this->security->get_csrf_token_name(); ?>";
window.CSRF_HASH = "<?php echo $this->security->get_csrf_hash(); ?>";
<?php if (isset($current_user)): ?>
window.CURRENT_USER = <?php echo json_encode($current_user); ?>;
window.IS_SUPER_ADMIN = <?php echo (!empty($is_super_admin)) ? 'true' : 'false'; ?>;
window.USER_PERMISSIONS = <?php echo json_encode($effective_permissions ?? []); ?>;
window.CURRENT_ACADEMIC_YEAR_ID = <?php echo (int)($current_academic_year_id ?? 1); ?>;
window.CURRENT_ACADEMIC_YEAR = <?php echo json_encode($current_academic_year ?? null); ?>;
window.AVAILABLE_ACADEMIC_YEARS = <?php echo json_encode($available_academic_years ?? []); ?>;
window.CAN_CHANGE_ACADEMIC_YEAR = <?php echo (!empty($can_change_academic_year)) ? 'true' : 'false'; ?>;
<?php endif; ?>
</script>
<div class="flex min-h-screen">
  <div id="sidebar-root"></div>
  <div class="flex-1 min-w-0 flex flex-col">
    <div id="header-root"></div>
    <main class="flex-1 p-4 lg:p-6 max-w-[1600px] w-full mx-auto">
