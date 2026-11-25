<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wpdb, $cptslotsb_addons_objs_list, $cptslotsb_addons_active_list;

$current_user = wp_get_current_user();
$current_user_access = current_user_can('edit_pages');
$current_user_can_admin = current_user_can('manage_options');

if ( !is_admin() || (!$current_user_access && !@in_array($current_user->ID, unserialize($this->get_option("cp_user_access","")))))
{
    echo 'Direct access not allowed.';
    exit;
}

$this->item = intval($_GET["cal"]);
    
$this->option_buffered_item = false;
$this->option_buffered_id = -1;

if (!defined('CP_TSLOTSBOOK_DEFAULT_fp_from_email'))
    define('CP_TSLOTSBOOK_DEFAULT_fp_from_email', get_the_author_meta('user_email', get_current_user_id()) );
if (!defined('CP_TSLOTSBOOK_DEFAULT_fp_destination_emails'))
    define('CP_TSLOTSBOOK_DEFAULT_fp_destination_emails', CP_TSLOTSBOOK_DEFAULT_fp_from_email);

$settings_saved = ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset( $_POST[$this->prefix.'_post_options'] ) );
$nonce          = wp_create_nonce( 'cptslotsb_actions_admin' );
$form_name      = $this->get_option('form_name', 'Calendar');
$shortcode_str  = '[' . $this->shorttag . ' id="' . intval($this->item) . '"]';
?>

<style type="text/css">
    :root {
        --wpts-primary: #0d9488;
        --wpts-primary-hover: #0f766e;
        --wpts-primary-light: #f0fdfa;
        --wpts-primary-border: #99f6e4;
        --wpts-accent: #0284c7;
        --wpts-accent-light: #f0f9ff;
        --wpts-accent-border: #bae6fd;
        --wpts-success: #10b981;
        --wpts-success-bg: #ecfdf5;
        --wpts-warning: #f59e0b;
        --wpts-warning-bg: #fffbeb;
        --wpts-danger: #ef4444;
        --wpts-danger-bg: #fef2f2;
        --wpts-surface: #ffffff;
        --wpts-bg: #f8fafc;
        --wpts-border: #e2e8f0;
        --wpts-text: #0f172a;
        --wpts-text-muted: #64748b;
        --wpts-radius: 14px;
        --wpts-radius-sm: 8px;
        --wpts-shadow: 0 1px 3px 0 rgba(15, 23, 42, 0.05), 0 1px 2px -1px rgba(15, 23, 42, 0.05);
    }

    .wpts-admin-wrap {
        max-width: 1320px;
        margin: 20px auto 40px;
        padding-right: 20px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Inter, Oxygen-Sans, Ubuntu, Cantarell, sans-serif;
        color: var(--wpts-text);
        box-sizing: border-box;
    }
    .wpts-admin-wrap *, .wpts-admin-wrap *::before, .wpts-admin-wrap *::after {
        box-sizing: border-box;
    }

    /* Toast Notification */
    .wpts-toast {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--wpts-success-bg);
        border: 1px solid #a7f3d0;
        border-left: 4px solid var(--wpts-success);
        color: #065f46;
        padding: 14px 20px;
        border-radius: var(--wpts-radius-sm);
        margin-bottom: 20px;
        font-size: 13.5px;
        font-weight: 500;
        box-shadow: var(--wpts-shadow);
        animation: wptsSlideDown 0.25s ease-out;
    }
    @keyframes wptsSlideDown {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Top Header Card */
    .wpts-header {
        background: var(--wpts-surface);
        border: 1px solid var(--wpts-border);
        border-radius: var(--wpts-radius);
        padding: 20px 24px;
        margin-bottom: 20px;
        box-shadow: var(--wpts-shadow);
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        background: linear-gradient(135deg, #f0fdfa 0%, #ffffff 65%);
    }
    .wpts-header-left {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }
    .wpts-back-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        background: #ffffff;
        border: 1px solid var(--wpts-border);
        border-radius: var(--wpts-radius-sm);
        color: var(--wpts-text);
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .wpts-back-btn:hover {
        background: var(--wpts-primary-light);
        border-color: var(--wpts-primary-border);
        color: var(--wpts-primary-hover);
    }
    .wpts-title-group h1 {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        color: var(--wpts-text);
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 0;
        line-height: 1.3;
    }
    .wpts-id-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--wpts-primary-light);
        color: var(--wpts-primary-hover);
        font-size: 12px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 999px;
        border: 1px solid var(--wpts-primary-border);
    }
    .wpts-subtitle {
        margin: 4px 0 0;
        font-size: 13px;
        color: var(--wpts-text-muted);
    }
    .wpts-header-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .wpts-shortcode-pill {
        display: inline-flex;
        align-items: center;
        background: #ffffff;
        border: 1px dashed #94a3b8;
        border-radius: var(--wpts-radius-sm);
        padding: 6px 10px;
        font-size: 12px;
        color: var(--wpts-text-muted);
        gap: 8px;
    }
    .wpts-shortcode-pill code {
        background: transparent;
        color: var(--wpts-text);
        font-weight: 600;
        padding: 0;
    }
    .wpts-copy-btn {
        background: var(--wpts-primary-light);
        border: 1px solid var(--wpts-primary-border);
        border-radius: 6px;
        padding: 3px 8px;
        font-size: 11px;
        font-weight: 600;
        color: var(--wpts-primary-hover);
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .wpts-copy-btn:hover {
        background: var(--wpts-primary);
        color: #ffffff;
        border-color: var(--wpts-primary);
    }

    /* Buttons */
    .wpts-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 9px 15px;
        border-radius: var(--wpts-radius-sm);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.15s ease;
        border: 1px solid transparent;
        line-height: 1.2;
        white-space: nowrap;
    }
    .wpts-btn-primary {
        background: var(--wpts-primary);
        color: #ffffff;
        box-shadow: 0 1px 2px rgba(13, 148, 136, 0.25);
    }
    .wpts-btn-primary:hover {
        background: var(--wpts-primary-hover);
        color: #ffffff;
        transform: translateY(-1px);
    }
    .wpts-btn-accent {
        background: var(--wpts-accent-light);
        color: var(--wpts-accent);
        border-color: var(--wpts-accent-border);
    }
    .wpts-btn-accent:hover {
        background: var(--wpts-accent);
        color: #ffffff;
        border-color: var(--wpts-accent);
    }
    .wpts-btn-soft {
        background: var(--wpts-primary-light);
        border-color: var(--wpts-primary-border);
        color: var(--wpts-primary-hover);
    }
    .wpts-btn-soft:hover {
        background: var(--wpts-primary);
        color: #ffffff;
        border-color: var(--wpts-primary);
    }
    .wpts-btn-secondary {
        background: #ffffff;
        border-color: var(--wpts-border);
        color: var(--wpts-text);
    }
    .wpts-btn-secondary:hover {
        background: var(--wpts-bg);
        border-color: #cbd5e1;
        color: var(--wpts-primary);
    }

    /* Navigation Tabs */
    .wpts-tabs-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        background: var(--wpts-surface);
        border: 1px solid var(--wpts-border);
        padding: 8px;
        border-radius: var(--wpts-radius);
        margin-bottom: 24px;
        box-shadow: var(--wpts-shadow);
        flex-wrap: wrap;
    }
    .wpts-tabs-list {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }
    .wpts-tab-trigger {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 14px;
        border-radius: var(--wpts-radius-sm);
        border: none;
        background: transparent;
        color: var(--wpts-text-muted);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .wpts-tab-trigger:hover {
        color: var(--wpts-text);
        background: var(--wpts-bg);
    }
    .wpts-tab-trigger.ahb-step-active {
        background: var(--wpts-primary);
        color: #ffffff;
        box-shadow: 0 2px 4px rgba(13, 148, 136, 0.22);
    }
    .wpts-view-mode-btn {
        font-size: 12px;
        color: var(--wpts-text-muted);
        background: var(--wpts-bg);
        border: 1px solid var(--wpts-border);
        padding: 7px 12px;
        border-radius: var(--wpts-radius-sm);
        cursor: pointer;
        font-weight: 600;
    }
    .wpts-view-mode-btn:hover {
        color: var(--wpts-primary);
        border-color: #cbd5e1;
    }

    /* Tab Panels & Cards (including Add-On .postbox elements) */
    .ahb-adintsection {
        display: none;
    }
    .ahb-adintsection.ahb-adintsection-active,
    .wpts-show-all .ahb-adintsection {
        display: block;
        animation: wptsFadeIn 0.2s ease-out;
    }
    @keyframes wptsFadeIn {
        from { opacity: 0; transform: translateY(4px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .wpts-card,
    .wpts-admin-wrap .postbox {
        background: var(--wpts-surface);
        border: 1px solid var(--wpts-border);
        border-radius: var(--wpts-radius);
        box-shadow: var(--wpts-shadow);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .wpts-card-header,
    .wpts-admin-wrap .postbox > .hndle {
        padding: 18px 24px !important;
        border-bottom: 1px solid var(--wpts-border);
        background: #fcfcfd;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin: 0 !important;
        font-size: 16px !important;
        font-weight: 700 !important;
        color: var(--wpts-text);
        cursor: default;
    }
    .wpts-card-title {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        color: var(--wpts-text);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .wpts-card-desc {
        margin: 3px 0 0;
        font-size: 13px;
        color: var(--wpts-text-muted);
        font-weight: 400;
    }
    .wpts-card-body,
    .wpts-admin-wrap .postbox > .inside {
        padding: 24px !important;
        margin: 0 !important;
    }

    /* Section Dividers */
    .wpts-section-divider {
        margin: 26px 0 18px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--wpts-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .wpts-section-divider:first-child {
        margin-top: 0;
    }
    .wpts-section-divider h4 {
        margin: 0;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--wpts-primary-hover);
    }

    /* Form Grid & Controls */
    .wpts-grid {
        display: grid;
        gap: 20px;
        margin-bottom: 20px;
    }
    .wpts-grid:last-child {
        margin-bottom: 0;
    }
    .wpts-cols-2 { grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); }
    .wpts-cols-3 { grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); }
    .wpts-cols-4 { grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); }

    .wpts-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .wpts-field label,
    .wpts-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--wpts-text);
        display: block;
    }
    .wpts-help {
        font-size: 12px;
        color: var(--wpts-text-muted);
        line-height: 1.45;
        margin-top: 2px;
    }

    .wpts-admin-wrap input[type="text"],
    .wpts-admin-wrap input[type="email"],
    .wpts-admin-wrap input[type="number"],
    .wpts-admin-wrap select,
    .wpts-admin-wrap textarea {
        width: 100%;
        max-width: 100%;
        padding: 8px 12px;
        font-size: 13.5px;
        line-height: 1.4;
        color: var(--wpts-text);
        background-color: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: var(--wpts-radius-sm);
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
        box-shadow: none;
        height: auto;
        min-height: 38px;
    }
    .wpts-admin-wrap select:not([multiple]) {
        padding-right: 32px !important;
        background-position: right 8px center;
    }
    .wpts-admin-wrap select[multiple] {
        min-height: 135px;
        padding: 6px;
    }
    .wpts-admin-wrap input[type="text"]:focus,
    .wpts-admin-wrap input[type="email"]:focus,
    .wpts-admin-wrap input[type="number"]:focus,
    .wpts-admin-wrap select:focus,
    .wpts-admin-wrap textarea:focus {
        border-color: var(--wpts-primary);
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.14);
        outline: none;
    }
    .wpts-admin-wrap input[type="color"] {
        height: 38px;
        width: 90px;
        padding: 2px;
        border: 1px solid #cbd5e1;
        border-radius: var(--wpts-radius-sm);
        background: #ffffff;
        cursor: pointer;
    }

    /* Callouts & Subboxes */
    .wpts-alert {
        padding: 14px 18px;
        border-radius: var(--wpts-radius-sm);
        font-size: 13px;
        line-height: 1.5;
        margin: 16px 0;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }
    .wpts-alert-info {
        background: var(--wpts-accent-light);
        border: 1px solid var(--wpts-accent-border);
        color: #0369a1;
    }
    .wpts-subbox {
        background: var(--wpts-bg);
        border: 1px solid var(--wpts-border);
        border-radius: var(--wpts-radius-sm);
        padding: 18px;
        margin-top: 14px;
    }

    /* Form Builder Modernization (harmonized with css/style.css) */
    .wpts-builder-wrap {
        padding: 18px !important;
    }
    .wpts-builder-wrap .form-builder {
        width: 100% !important;
        min-width: 0 !important;
    }
    .wpts-admin-wrap .form-builder input[type="text"],
    .wpts-admin-wrap .form-builder input[type="email"],
    .wpts-admin-wrap .form-builder input[type="number"],
    .wpts-admin-wrap .form-builder select,
    .wpts-admin-wrap .form-builder textarea {
        min-height: 0;
        font-size: 12.5px;
    }

    /* Pro Upgrade Banner inside Builder */
    .wpts-pro-banner {
        background: linear-gradient(135deg, #f0fdfa 0%, #f8fafc 100%);
        border: 1px solid var(--wpts-primary-border);
        border-left: 4px solid var(--wpts-primary);
        border-radius: var(--wpts-radius);
        padding: 20px 24px;
        margin-top: 20px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
    }
    .wpts-pro-banner h4 {
        margin: 0 0 6px;
        font-size: 15px;
        color: var(--wpts-text);
    }
    .wpts-pro-banner ul {
        margin: 8px 0 0;
        padding-left: 18px;
        font-size: 13px;
        color: var(--wpts-text-muted);
    }
    .wpts-pro-banner li {
        margin-bottom: 4px;
    }

    /* Step Navigation Bar inside Cards */
    .wpts-step-nav {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 18px;
        margin-top: 20px;
        border-top: 1px solid var(--wpts-border);
    }

    /* Sticky Bottom Action Bar */
    .wpts-sticky-footer {
        position: sticky;
        bottom: 16px;
        z-index: 90;
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(8px);
        border: 1px solid var(--wpts-border);
        border-radius: var(--wpts-radius);
        padding: 14px 24px;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.12);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 28px;
    }
    .wpts-footer-links {
        display: flex;
        align-items: center;
        gap: 14px;
        font-size: 13px;
        color: var(--wpts-text-muted);
    }
    .wpts-footer-links a {
        color: var(--wpts-primary);
        text-decoration: none;
        font-weight: 600;
    }
    .wpts-footer-links a:hover {
        text-decoration: underline;
    }
</style>

<div class="wrap wpts-admin-wrap" id="wptsAdminApp">

    <?php if ( $settings_saved ) : ?>
    <div id="setting-error-settings_updated" class="wpts-toast">
        <span>✅ <strong><?php esc_html_e('Settings saved!', 'wp-time-slots-booking-form'); ?></strong> <?php esc_html_e('Your booking calendar settings have been updated.', 'wp-time-slots-booking-form'); ?></span>
        <button type="button" class="wpts-copy-btn" onclick="this.parentElement.style.display='none'">Dismiss</button>
    </div>
    <?php endif; ?>

    <form method="post" action="" name="cpformconf" id="cpformconf"> 
        <input name="anonce" type="hidden" value="<?php echo esc_attr($nonce); ?>" />
        <input name="<?php echo esc_attr($this->prefix); ?>_post_options" type="hidden" value="1" />
        <input name="<?php echo esc_attr($this->prefix); ?>_id" type="hidden" value="<?php echo esc_attr($this->item); ?>" />
        <input type="hidden" name="templates" id="templates" value="<?php echo esc_attr( json_encode( $this->available_templates() ) ); ?>" />

        <!-- Top Header -->
        <div id="topadminsection" class="wpts-header">
            <div class="wpts-header-left">
                <a href="<?php echo esc_url(admin_url('admin.php?page='.$this->menu_parameter)); ?>" class="wpts-back-btn">
                    &larr; <?php esc_html_e('Calendars List', 'wp-time-slots-booking-form'); ?>
                </a>
                <div class="wpts-title-group">
                    <h1>
                        🗓️ <?php echo esc_html($form_name); ?>
                        <span class="wpts-id-badge">ID #<?php echo intval($this->item); ?></span>
                    </h1>
                    <p class="wpts-subtitle"><?php echo esc_html($this->plugin_name); ?> &mdash; <?php esc_html_e('Configure Time Slots, Form Fields, Notification Emails & Booking Rules', 'wp-time-slots-booking-form'); ?></p>
                </div>
            </div>

            <div class="wpts-header-actions">
                <div class="wpts-shortcode-pill" title="Click Copy to paste this shortcode on any page or post">
                    <code><?php echo esc_html($shortcode_str); ?></code>
                    <button type="button" class="wpts-copy-btn" onclick="wptsCopyShortcode(this, '<?php echo esc_js($shortcode_str); ?>')"><?php esc_html_e('Copy', 'wp-time-slots-booking-form'); ?></button>
                </div>
                <a href="<?php echo esc_url(admin_url('admin.php?page='.$this->menu_parameter.'&cal='.$this->item.'&schedule=1&calendarview=1')); ?>" class="wpts-btn wpts-btn-accent">
                    📅 <?php esc_html_e('Schedule', 'wp-time-slots-booking-form'); ?>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page='.$this->menu_parameter.'&cal='.$this->item.'&list=1')); ?>" class="wpts-btn wpts-btn-secondary">
                    📋 <?php esc_html_e('Booking Orders', 'wp-time-slots-booking-form'); ?>
                </a>
                <button type="submit" name="savekeep" class="wpts-btn wpts-btn-primary">
                    💾 <?php esc_html_e('Save Changes', 'wp-time-slots-booking-form'); ?>
                </button>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="wpts-tabs-bar">
            <div class="wpts-tabs-list" role="tablist">
                <button type="button" class="wpts-tab-trigger ahb-step ahb-step-active" data-step="1">
                    🎨 1. <?php esc_html_e('Editor & Texts', 'wp-time-slots-booking-form'); ?>
                </button>
                <button type="button" class="wpts-tab-trigger ahb-step" data-step="2">
                    ⚙️ 2. <?php esc_html_e('General Settings', 'wp-time-slots-booking-form'); ?>
                </button>
                <button type="button" class="wpts-tab-trigger ahb-step" data-step="3">
                    ✉️ 3. <?php esc_html_e('Notification Emails', 'wp-time-slots-booking-form'); ?>
                </button>
                <button type="button" class="wpts-tab-trigger ahb-step" data-step="4">
                    🛡️ 4. <?php esc_html_e('Antispam', 'wp-time-slots-booking-form'); ?>
                </button>
                <button type="button" class="wpts-tab-trigger ahb-step" data-step="5">
                    📈 5. <?php esc_html_e('Reports', 'wp-time-slots-booking-form'); ?>
                </button>
                <button type="button" class="wpts-tab-trigger ahb-step" data-step="6">
                    🧩 6. <?php esc_html_e('Add-Ons', 'wp-time-slots-booking-form'); ?>
                </button>
            </div>
            <button type="button" class="wpts-view-mode-btn" id="wptsToggleAllBtn" onclick="wptsToggleAllSections()">
                <?php esc_html_e('Show All Sections', 'wp-time-slots-booking-form'); ?>
            </button>
        </div>

        <div class="ahb-adintsection-container">

            <!-- ==========================================
                 STEP 1: VISUAL EDITOR, LABELS & VALIDATION
                 ========================================== -->
            <div class="ahb-adintsection ahb-adintsection-active" data-step="1">
                <div class="wpts-card">
                    <div class="wpts-card-header">
                        <div>
                            <h3 class="wpts-card-title">🎨 <?php esc_html_e('Time Slots & Visual Form Builder', 'wp-time-slots-booking-form'); ?></h3>
                            <p class="wpts-card-desc"><?php esc_html_e('Click the calendar field on the right to configure available time slots, capacity, prices, and special dates.', 'wp-time-slots-booking-form'); ?></p>
                        </div>
                    </div>
                    <div class="wpts-card-body wpts-builder-wrap">
                        <input type="hidden" name="form_structure_control" id="form_structure_control" value="&quot;&quot;&quot;&quot;&quot;&quot;" />
                        <input type="hidden" name="form_structure" id="form_structure" size="180" value="<?php echo $this->clean_sanitize(str_replace('"','&quot;',str_replace("\r","",str_replace("\n","",esc_attr($this->cleanJSON($this->get_option('form_structure', CP_TSLOTSBOOK_DEFAULT_form_structure))))))); ?>" />

                        <script type="text/javascript">
                        $easyFormQuery = jQuery.noConflict();
                        $easyFormQuery(document).ready(function() {
                            var f = $easyFormQuery("#fbuilder").fbuilder();
                            window['cff_form'] = f;
                            f.fBuild.loadData("form_structure", "templates");

                            $easyFormQuery("#saveForm").on("click", function() {
                                f.fBuild.saveData("form_structure");
                            });

                            $easyFormQuery("#tabs-1").on("click", ".itemForm", function() {
                                var ftype = $easyFormQuery(this).attr("data-ftype") || $easyFormQuery(this).attr("id");
                                if (ftype) {
                                    f.fBuild.addItem(ftype);
                                }
                            });
                        });
                        </script>

                        <div class="form-builder wpts-modern-builder">
                            <div class="ctrlsColumn">
                                <div id="tabs">
                                    <ul>
                                        <li><a href="#tabs-1"><?php esc_html_e('Add a Field','wp-time-slots-booking-form'); ?></a></li>
                                        <li><a href="#tabs-2"><?php esc_html_e('Field Settings','wp-time-slots-booking-form'); ?></a></li>
                                        <li><a href="#tabs-3"><?php esc_html_e('Form Settings','wp-time-slots-booking-form'); ?></a></li>
                                    </ul>
                                    <div id="tabs-1"></div>
                                    <div id="tabs-2"></div>
                                    <div id="tabs-3"></div>
                                </div>
                            </div>
                            <div class="dashboardColumn">
                                <div class="wpts-canvas-toolbar">
                                    <span class="wpts-canvas-toolbar-title"><?php esc_html_e('Live Form Preview', 'wp-time-slots-booking-form'); ?></span>
                                    <span class="wpts-canvas-toolbar-hint"><?php esc_html_e('Drag fields to add or reorder · Click any field to edit', 'wp-time-slots-booking-form'); ?></span>
                                </div>
                                <div class="padding10 wpts-canvas-body">
                                    <div class="form-builder-error-messages"></div>
                                    <div id="fbuilder">
                                        <div id="formheader"></div>
                                        <div id="fieldlist"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="wpts-pro-banner">
                            <div style="flex:1;min-width:280px;">
                                <h4>⚡ <?php esc_html_e('Unlock the Full Visual Form Builder & Payment Integrations', 'wp-time-slots-booking-form'); ?></h4>
                                <div style="font-size:13px;color:var(--wpts-text-muted);">
                                    <?php esc_html_e('In this version the form builder supports calendar, text, email, and acceptance checkbox fields. Upgrade to unlock:', 'wp-time-slots-booking-form'); ?>
                                </div>
                                <ul>
                                    <li><strong>Conditional Logic:</strong> Hide/show fields dynamically based on previous selections.</li>
                                    <li><strong>File Uploads, Dropdowns, Checkboxes, Radio Buttons</strong> &amp; <strong>Multi-page</strong> forms.</li>
                                    <li><strong>Payments Integration:</strong> PayPal Standard, PayPal Pro, Stripe, Authorize.net, Skrill, Mollie / iDeal, TargetPay, SagePay &amp; RedSys TPV.</li>
                                    <li><strong>Full Set of Add-Ons:</strong> iCal sync, SMS notifications, reminders, cancellation links, reCAPTCHA, MailChimp &amp; more.</li>
                                </ul>
                            </div>
                            <div>
                                <button type="button" class="wpts-btn wpts-btn-primary" onclick="window.open('<?php echo esc_js($this->plugin_download_URL); ?>?src=activatebtn');">
                                    🚀 <?php esc_html_e('Activate Full Form Builder', 'wp-time-slots-booking-form'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Button Labels Card -->
                <div class="wpts-card">
                    <div class="wpts-card-header">
                        <div>
                            <h3 class="wpts-card-title">🔘 <?php esc_html_e('Button Labels', 'wp-time-slots-booking-form'); ?></h3>
                            <p class="wpts-card-desc"><?php esc_html_e('Customize the Submit, Previous, Next, and pagination labels displayed on the booking form.', 'wp-time-slots-booking-form'); ?></p>
                        </div>
                    </div>
                    <div class="wpts-card-body">
                        <div class="wpts-grid wpts-cols-4">
                            <div class="wpts-field">
                                <label><?php esc_html_e('Submit button label (text)','wp-time-slots-booking-form'); ?>:</label>
                                <input type="text" name="vs_text_submitbtn" size="40" value="<?php $label = ($this->get_option('vs_text_submitbtn', 'Submit')); echo esc_attr($label==''?'Submit':$label); ?>" />
                            </div>
                            <div class="wpts-field">
                                <label><?php esc_html_e('Previous page button label (text)','wp-time-slots-booking-form'); ?>:</label>
                                <input type="text" name="vs_text_previousbtn" size="40" value="<?php $label = ($this->get_option('vs_text_previousbtn', 'Previous')); echo esc_attr($label==''?'Previous':$label); ?>" />
                            </div>
                            <div class="wpts-field">
                                <label><?php esc_html_e('Next page button label (text)','wp-time-slots-booking-form'); ?>:</label>
                                <input type="text" name="vs_text_nextbtn" size="40" value="<?php $label = ($this->get_option('vs_text_nextbtn', 'Next')); echo esc_attr($label==''?'Next':$label); ?>" />
                            </div>
                            <div class="wpts-field">
                                <label><?php esc_html_e('Page {0} of {0} (text)','wp-time-slots-booking-form'); ?>:</label>
                                <input type="text" name="vs_text_pageof" size="40" value="<?php $label = ($this->get_option('vs_text_pageof', 'Page {0} of {0}')); echo esc_attr($label==''?'Page {0} of {0}':$label); ?>" />
                            </div>
                        </div>

                        <div class="wpts-alert wpts-alert-info" style="margin-bottom:0;">
                            <span>💡</span>
                            <div>
                                The <code>class="pbSubmit"</code> can be used to modify the button styles in the <a href="?page=cp_timeslotsbooking_settings&gotab=css" style="font-weight:700;color:#0369a1;">CSS Customization Area</a>. For general CSS styling examples, <a href="https://wptimeslot.dwbooster.com/faq/" target="_blank" style="font-weight:700;color:#0369a1;">check the FAQ</a>.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Validation Error Messages Card -->
                <div class="wpts-card">
                    <div class="wpts-card-header">
                        <div>
                            <h3 class="wpts-card-title">⚠️ <?php esc_html_e('Error Messages for Validation Rules', 'wp-time-slots-booking-form'); ?></h3>
                            <p class="wpts-card-desc"><?php esc_html_e('Customize the validation messages shown when required fields or slot limits are not met.', 'wp-time-slots-booking-form'); ?></p>
                        </div>
                    </div>
                    <div class="wpts-card-body">
                        <div class="wpts-grid wpts-cols-3">
                            <div class="wpts-field">
                                <label><?php esc_html_e('"is required" text','wp-time-slots-booking-form'); ?>:</label>
                                <input type="text" name="vs_text_is_required" size="40" value="<?php echo esc_attr($this->get_option('vs_text_is_required', CP_TSLOTSBOOK_DEFAULT_vs_text_is_required)); ?>" />
                            </div>
                            <div class="wpts-field">
                                <label><?php esc_html_e('"is email" text','wp-time-slots-booking-form'); ?>:</label>
                                <input type="text" name="vs_text_is_email" size="40" value="<?php echo esc_attr($this->get_option('vs_text_is_email', CP_TSLOTSBOOK_DEFAULT_vs_text_is_email)); ?>" />
                            </div>
                            <div class="wpts-field">
                                <label><?php esc_html_e('"is valid captcha" text','wp-time-slots-booking-form'); ?>:</label>
                                <input type="text" name="cv_text_enter_valid_captcha" size="40" value="<?php echo esc_attr($this->get_option('cv_text_enter_valid_captcha', CP_TSLOTSBOOK_DEFAULT_cv_text_enter_valid_captcha)); ?>" />
                            </div>
                            <div class="wpts-field">
                                <label><?php esc_html_e('"is valid date (mm/dd/yyyy)" text','wp-time-slots-booking-form'); ?>:</label>
                                <input type="text" name="vs_text_datemmddyyyy" size="40" value="<?php echo esc_attr($this->get_option('vs_text_datemmddyyyy', CP_TSLOTSBOOK_DEFAULT_vs_text_datemmddyyyy)); ?>" />
                            </div>
                            <div class="wpts-field">
                                <label><?php esc_html_e('"is valid date (dd/mm/yyyy)" text','wp-time-slots-booking-form'); ?>:</label>
                                <input type="text" name="vs_text_dateddmmyyyy" size="40" value="<?php echo esc_attr($this->get_option('vs_text_dateddmmyyyy', CP_TSLOTSBOOK_DEFAULT_vs_text_dateddmmyyyy)); ?>" />
                            </div>
                            <div class="wpts-field">
                                <label><?php esc_html_e('"is number" text','wp-time-slots-booking-form'); ?>:</label>
                                <input type="text" name="vs_text_number" size="40" value="<?php echo esc_attr($this->get_option('vs_text_number', CP_TSLOTSBOOK_DEFAULT_vs_text_number)); ?>" />
                            </div>
                            <div class="wpts-field">
                                <label><?php esc_html_e('"only digits" text','wp-time-slots-booking-form'); ?>:</label>
                                <input type="text" name="vs_text_digits" size="40" value="<?php echo esc_attr($this->get_option('vs_text_digits', CP_TSLOTSBOOK_DEFAULT_vs_text_digits)); ?>" />
                            </div>
                            <div class="wpts-field">
                                <label><?php esc_html_e('"under maximum" text','wp-time-slots-booking-form'); ?>:</label>
                                <input type="text" name="vs_text_max" size="40" value="<?php echo esc_attr($this->get_option('vs_text_max', CP_TSLOTSBOOK_DEFAULT_vs_text_max)); ?>" />
                            </div>
                            <div class="wpts-field">
                                <label><?php esc_html_e('"over minimum" text','wp-time-slots-booking-form'); ?>:</label>
                                <input type="text" name="vs_text_min" size="40" value="<?php echo esc_attr($this->get_option('vs_text_min', CP_TSLOTSBOOK_DEFAULT_vs_text_min)); ?>" />
                            </div>
                            <div class="wpts-field">
                                <label><?php esc_html_e('"Max appointments allowed message" text','wp-time-slots-booking-form'); ?>:</label>
                                <input type="text" name="vs_text_maxapp" size="40" value="<?php echo esc_attr($this->get_option('vs_text_maxapp', CP_TSLOTSBOOK_DEFAULT_vs_text_maxapp)); ?>" />
                            </div>
                        </div>

                        <div class="wpts-step-nav">
                            <span></span>
                            <button type="button" class="wpts-btn wpts-btn-soft" onclick="ahbGoToStep(2);">
                                <?php esc_html_e('Next Step: General Settings', 'wp-time-slots-booking-form'); ?> &rarr;
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 STEP 2: GENERAL SETTINGS
                 ========================================== -->
            <div class="ahb-adintsection" data-step="2">
                <div class="wpts-card">
                    <div class="wpts-card-header">
                        <div>
                            <h3 class="wpts-card-title">⚙️ <?php esc_html_e('General Booking & Calendar Settings', 'wp-time-slots-booking-form'); ?></h3>
                            <p class="wpts-card-desc"><?php esc_html_e('Configure the confirmation redirect URL, default booking statuses, date format, language, and payment labels.', 'wp-time-slots-booking-form'); ?></p>
                        </div>
                    </div>
                    <div class="wpts-card-body">

                        <div class="wpts-section-divider">
                            <h4><?php esc_html_e('Confirmation / Thank You Page', 'wp-time-slots-booking-form'); ?></h4>
                        </div>
                        <div class="wpts-field" style="margin-bottom:20px;">
                            <label><?php esc_html_e('Confirmation / Thank you page URL','wp-time-slots-booking-form'); ?></label>
                            <input type="text" name="fp_return_page" size="70" value="<?php echo esc_attr($this->get_option('fp_return_page', CP_TSLOTSBOOK_DEFAULT_fp_return_page)); ?>" />
                            <span class="wpts-help"><?php esc_html_e('Address / URL of the page where the user will be redirected after submitting the booking form.', 'wp-time-slots-booking-form'); ?></span>
                        </div>

                        <div class="wpts-section-divider">
                            <h4><?php esc_html_e('Booking Status & Global Calendar Settings', 'wp-time-slots-booking-form'); ?></h4>
                        </div>
                        <div class="wpts-grid wpts-cols-2">
                            <div class="wpts-field">
                                <label><?php esc_html_e('Default status of new bookings','wp-time-slots-booking-form'); ?></label>
                                <?php $this->render_status_box('defaultstatus',$this->get_option('defaultstatus', '')); ?>
                                <span class="wpts-help"><?php esc_html_e('Only "Approved" items are taken into account for availability verification.','wp-time-slots-booking-form'); ?></span>
                            </div>
                            <div class="wpts-field">
                                <label><?php esc_html_e('Default status of paid bookings','wp-time-slots-booking-form'); ?></label>
                                <?php $this->render_status_box('defaultpaidstatus',$this->get_option('defaultpaidstatus', '')); ?>
                                <span class="wpts-help"><?php esc_html_e('If a payment add-on is enabled, the booking will be changed to this status after payment.','wp-time-slots-booking-form'); ?></span>
                            </div>
                        </div>

                        <div class="wpts-grid wpts-cols-2">
                            <div class="wpts-field">
                                <label><?php esc_html_e('Date Format','wp-time-slots-booking-form'); ?></label>
                                <?php $v = $this->get_option('date_format','mm/dd/yy'); ?>
                                <select name="date_format">
                                    <option <?php if ($v == '' || $v == 'mm/dd/yy') echo 'selected'; ?> value="mm/dd/yy"><?php esc_html_e("Default",'wp-time-slots-booking-form'); ?> - mm/dd/yyyy</option>
                                    <option <?php if ($v == 'dd/mm/yy') echo 'selected'; ?> value="dd/mm/yy">dd/mm/yyyy</option>
                                    <option <?php if ($v == 'mm.dd.yy') echo 'selected'; ?> value="mm.dd.yy">mm.dd.yyyy</option>
                                    <option <?php if ($v == 'dd.mm.yy') echo 'selected'; ?> value="dd.mm.yy">dd.mm.yyyy</option>
                                    <option <?php if ($v == 'yy-mm-dd') echo 'selected'; ?> value="yy-mm-dd">ISO 8601 - yyyy-mm-dd</option>
                                    <option <?php if ($v == 'd M, y') echo 'selected'; ?> value="d M, y"><?php esc_html_e("Short",'wp-time-slots-booking-form'); ?> - d M, yy</option>
                                    <option <?php if ($v == 'd MM, y') echo 'selected'; ?> value="d MM, y"><?php esc_html_e("Medium",'wp-time-slots-booking-form'); ?> - d MM, yy</option>
                                    <option <?php if ($v == 'DD, d MM, yy') echo 'selected'; ?> value="DD, d MM, yy"><?php esc_html_e("Full",'wp-time-slots-booking-form'); ?> - DD, d MM, yyyy</option>
                                </select>
                            </div>
                            <div class="wpts-field">
                                <label for="calendar_language"><?php esc_html_e('Calendar Language','wp-time-slots-booking-form'); ?></label>
                                <?php $v = $this->get_option('calendar_language',''); ?>            
                                <select name="calendar_language" id="calendar_language">
                                    <option <?php if ($v == '') echo 'selected'; ?> value=""> - auto-detect - </option>
                                    <option <?php if ($v == 'af') echo 'selected'; ?> value="af">Afrikaans</option>
                                    <option <?php if ($v == 'sq') echo 'selected'; ?> value="sq">Albanian</option>
                                    <option <?php if ($v == 'ar') echo 'selected'; ?> value="ar">Arabic</option>
                                    <option <?php if ($v == 'ar_DZ') echo 'selected'; ?> value="ar_DZ">Arabic (Algeria)</option>
                                    <option <?php if ($v == 'hy_AM') echo 'selected'; ?> value="hy_AM">Armenian</option>
                                    <option <?php if ($v == 'az') echo 'selected'; ?> value="az">Azerbaijani</option>
                                    <option <?php if ($v == 'eu') echo 'selected'; ?> value="eu">Basque</option>
                                    <option <?php if ($v == 'bs_BA') echo 'selected'; ?> value="bs_BA">Bosnian</option>
                                    <option <?php if ($v == 'bg_BG') echo 'selected'; ?> value="bg_BG">Bulgarian</option>
                                    <option <?php if ($v == 'be_BY') echo 'selected'; ?> value="be_BY">Byelorussian (Belarusian)</option>
                                    <option <?php if ($v == 'km') echo 'selected'; ?> value="km">Cambodian</option>
                                    <option <?php if ($v == 'ca') echo 'selected'; ?> value="ca">Catalan</option>
                                    <option <?php if ($v == 'zh_HK') echo 'selected'; ?> value="zh_HK">Chinese (Hong Kong SAR)</option>
                                    <option <?php if ($v == 'zh_CN') echo 'selected'; ?> value="zh_CN">Chinese (PRC)</option>
                                    <option <?php if ($v == 'zh_TW') echo 'selected'; ?> value="zh_TW">Chinese (Taiwan)</option>
                                    <option <?php if ($v == 'hr') echo 'selected'; ?> value="hr">Croatian</option>
                                    <option <?php if ($v == 'cs_CZ') echo 'selected'; ?> value="cs_CZ">Czech</option>
                                    <option <?php if ($v == 'da_DK') echo 'selected'; ?> value="da_DK">Danish</option>
                                    <option <?php if ($v == 'nl_NL') echo 'selected'; ?> value="nl_NL">Dutch</option>
                                    <option <?php if ($v == 'nl_BE') echo 'selected'; ?> value="nl_BE">Dutch - Belgium</option>
                                    <option <?php if ($v == 'en_AU') echo 'selected'; ?> value="en_AU">English (Australia)</option>
                                    <option <?php if ($v == 'en_NZ') echo 'selected'; ?> value="en_NZ">English (New Zealand)</option>
                                    <option <?php if ($v == 'en_GB') echo 'selected'; ?> value="en_GB">English (United Kingdom)</option>
                                    <option <?php if ($v == 'eo_EO') echo 'selected'; ?> value="eo">Esperanto</option>
                                    <option <?php if ($v == 'et') echo 'selected'; ?> value="et">Estonian</option>
                                    <option <?php if ($v == 'fo') echo 'selected'; ?> value="fo">Faeroese</option>
                                    <option <?php if ($v == 'fa_IR') echo 'selected'; ?> value="fa_IR">Farsi</option>
                                    <option <?php if ($v == 'fi') echo 'selected'; ?> value="fi">Finnish</option>
                                    <option <?php if ($v == 'fr_FR') echo 'selected'; ?> value="fr_FR">French</option>
                                    <option <?php if ($v == 'fr_CA') echo 'selected'; ?> value="fr_CA">French (Canada)</option>
                                    <option <?php if ($v == 'fr_CH') echo 'selected'; ?> value="fr_CH">French (Switzerland)</option>
                                    <option <?php if ($v == 'gl_ES') echo 'selected'; ?> value="gl_ES">Galician</option>
                                    <option <?php if ($v == 'ka_GE') echo 'selected'; ?> value="ka_GE">Georgian</option>
                                    <option <?php if ($v == 'de_DE') echo 'selected'; ?> value="de_DE">German</option>
                                    <option <?php if ($v == 'el') echo 'selected'; ?> value="el">Greek</option>
                                    <option <?php if ($v == 'he_IL') echo 'selected'; ?> value="he_IL">Hebrew</option>
                                    <option <?php if ($v == 'hi_IN') echo 'selected'; ?> value="hi_IN">Hindi</option>
                                    <option <?php if ($v == 'hu_HU') echo 'selected'; ?> value="hu_HU">Hungarian</option>
                                    <option <?php if ($v == 'is') echo 'selected'; ?> value="is">Icelandic</option>
                                    <option <?php if ($v == 'id_ID') echo 'selected'; ?> value="id_ID">Indonesian</option>
                                    <option <?php if ($v == 'it_IT') echo 'selected'; ?> value="it_IT">Italian</option>
                                    <option <?php if ($v == 'it_CH') echo 'selected'; ?> value="it_CH">Italian (Switzerland)</option>
                                    <option <?php if ($v == 'ja') echo 'selected'; ?> value="ja">Japanese</option>
                                    <option <?php if ($v == 'kk') echo 'selected'; ?> value="kk">Kazakh</option>
                                    <option <?php if ($v == 'ky') echo 'selected'; ?> value="ky">Kirghiz</option>
                                    <option <?php if ($v == 'ko_KR') echo 'selected'; ?> value="ko_KR">Korean</option>
                                    <option <?php if ($v == 'lv') echo 'selected'; ?> value="lv">Latvian (Lettish)</option>
                                    <option <?php if ($v == 'lt_LT') echo 'selected'; ?> value="lt_LT">Lithuanian</option>
                                    <option <?php if ($v == 'lb') echo 'selected'; ?> value="lb">Luxembourgish</option>
                                    <option <?php if ($v == 'mk_MK') echo 'selected'; ?> value="mk_MK">Macedonian</option>
                                    <option <?php if ($v == 'ms_MY') echo 'selected'; ?> value="ms_MY">Malay</option>
                                    <option <?php if ($v == 'ml_IN') echo 'selected'; ?> value="ml_IN">Malayalam</option>
                                    <option <?php if ($v == 'no') echo 'selected'; ?> value="no">Norwegian</option>
                                    <option <?php if ($v == 'nb_NO') echo 'selected'; ?> value="nb_NO">Norwegian (Bokm&aring;l)</option>
                                    <option <?php if ($v == 'nn') echo 'selected'; ?> value="nn">Norwegian Nynorsk</option>
                                    <option <?php if ($v == 'pl_PL') echo 'selected'; ?> value="pl_PL">Polish</option>
                                    <option <?php if ($v == 'pt_PT') echo 'selected'; ?> value="pt_PT">Portuguese</option>
                                    <option <?php if ($v == 'pt_BR') echo 'selected'; ?> value="pt_BR">Portuguese (Brazil)</option>
                                    <option <?php if ($v == 'rm') echo 'selected'; ?> value="rm">Rhaeto-Romance</option>
                                    <option <?php if ($v == 'ro_RO') echo 'selected'; ?> value="ro_RO">Romanian</option>
                                    <option <?php if ($v == 'ru_RU') echo 'selected'; ?> value="ru_RU">Russian</option>
                                    <option <?php if ($v == 'sr_SR') echo 'selected'; ?> value="sr_SR">Serbian</option>
                                    <option <?php if ($v == 'sk_SK') echo 'selected'; ?> value="sk_SK">Slovak</option>
                                    <option <?php if ($v == 'sl_SI') echo 'selected'; ?> value="sl_SI">Slovenian</option>
                                    <option <?php if ($v == 'es_ES') echo 'selected'; ?> value="es_ES">Spanish</option>
                                    <option <?php if ($v == 'sv_SE') echo 'selected'; ?> value="sv_SE">Swedish</option>
                                    <option <?php if ($v == 'tj') echo 'selected'; ?> value="tj">Tajikistan</option>
                                    <option <?php if ($v == 'ta') echo 'selected'; ?> value="ta">Tamil</option>
                                    <option <?php if ($v == 'th') echo 'selected'; ?> value="th">Thai</option>
                                    <option <?php if ($v == 'tr_TR') echo 'selected'; ?> value="tr_TR">Turkish</option>
                                    <option <?php if ($v == 'uk') echo 'selected'; ?> value="uk">Ukrainian</option>
                                    <option <?php if ($v == 'vi') echo 'selected'; ?> value="vi">Vietnamese</option>
                                    <option <?php if ($v == 'cy_GB') echo 'selected'; ?> value="cy_GB">Welsh/UK</option>
                                </select>
                            </div>
                        </div>

                        <div class="wpts-section-divider">
                            <h4><?php esc_html_e('Payment Integration Settings', 'wp-time-slots-booking-form'); ?></h4>
                        </div>
                        <div class="wpts-grid wpts-cols-2">
                            <div class="wpts-field">
                                <label><?php esc_html_e('Product name at payment page','wp-time-slots-booking-form'); ?></label>
                                <input type="text" name="product_name" size="40" value="<?php echo esc_attr($this->get_option('product_name', 'Booking')); ?>" />
                            </div>
                            <div class="wpts-field">
                                <label><?php esc_html_e('Label of "Pay Later" option (if enabled)','wp-time-slots-booking-form'); ?></label>
                                <input type="text" name="pay_later_label" size="40" value="<?php echo esc_attr($this->get_option('pay_later_label', 'Pay later')); ?>" />
                            </div>
                        </div>
                        <span class="wpts-help">* <?php esc_html_e('Note: To enable a payment method, enable the related add-on first.', 'wp-time-slots-booking-form'); ?></span>

                        <div <?php if (!$current_user_can_admin) echo 'style="display:none"'; ?>>
                            <div class="wpts-section-divider">
                                <h4><?php esc_html_e('Users with Access to the Messages List', 'wp-time-slots-booking-form'); ?></h4>
                            </div>
                            <div class="wpts-grid wpts-cols-2">
                                <div class="wpts-field">
                                    <label><?php esc_html_e('Select users with access (CTRL/CMD+click for multiple selection)','wp-time-slots-booking-form'); ?>:</label>
                                    <?php 
                                    $users = $wpdb->get_results( "SELECT user_login,ID FROM ".$wpdb->users." ORDER BY ID DESC LIMIT 0,500" );                                                                     
                                    $options = unserialize($this->get_option('cp_user_access', array())); 
                                    if (!is_array($options)) $options = array();
                                    ?>           
                                    <select name="cp_user_access[]" multiple="multiple" size="5">
                                        <?php foreach ($users as $user) { ?>
                                            <option value="<?php echo esc_attr($user->ID); ?>"<?php if ( in_array ($user->ID, $options) ) echo ' selected'; ?>><?php echo esc_html($user->user_login); ?></option>
                                        <?php } ?>           
                                    </select>
                                </div>
                                <div class="wpts-field">
                                    <label><?php esc_html_e('Allow selected users access also to the calendar settings?','wp-time-slots-booking-form'); ?></label>
                                    <?php $option = $this->get_option('cp_user_access_settings', ''); ?>
                                    <select name="cp_user_access_settings">
                                        <option value="true"<?php if ($option == 'true') echo ' selected'; ?>><?php esc_html_e('Yes','wp-time-slots-booking-form'); ?></option>
                                        <option value=""<?php if ($option == '') echo ' selected'; ?>><?php esc_html_e('No','wp-time-slots-booking-form'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="wpts-step-nav">
                            <button type="button" class="wpts-btn wpts-btn-secondary" onclick="ahbGoToStep(1);">
                                &larr; <?php esc_html_e('Previous: Editor & Texts', 'wp-time-slots-booking-form'); ?>
                            </button>
                            <button type="button" class="wpts-btn wpts-btn-soft" onclick="ahbGoToStep(3);">
                                <?php esc_html_e('Next Step: Notification Emails', 'wp-time-slots-booking-form'); ?> &rarr;
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 STEP 3: NOTIFICATION EMAILS
                 ========================================== -->
            <div class="ahb-adintsection" data-step="3">
                <div class="wpts-card">
                    <div class="wpts-card-header">
                        <div>
                            <h3 class="wpts-card-title">📩 <?php esc_html_e('Form Processing & Notification Email Settings', 'wp-time-slots-booking-form'); ?></h3>
                            <p class="wpts-card-desc"><?php esc_html_e('Configure administrator booking notifications, sender headers, and email deliverability testing.', 'wp-time-slots-booking-form'); ?></p>
                        </div>
                    </div>
                    <div class="wpts-card-body">
                        <div class="wpts-grid wpts-cols-2">
                            <div class="wpts-field">
                                <label><?php esc_html_e('Send email "From"','wp-time-slots-booking-form'); ?></label>
                                <?php $option = $this->get_option('fp_emailfrommethod', "fixed"); ?>
                                <select name="fp_emailfrommethod">
                                    <option value="fixed"<?php if ($option == 'fixed') echo ' selected'; ?>><?php esc_html_e('From fixed email address indicated below - Recommended option','wp-time-slots-booking-form'); ?></option>
                                    <option value="customer"<?php if ($option == 'customer') echo ' selected'; ?>><?php esc_html_e('From the email address indicated by the customer','wp-time-slots-booking-form'); ?></option>
                                </select>
                                <span class="wpts-help">* <?php esc_html_e('If you select "from fixed..." the customer email address will appear in the "to" address when you hit "reply", which is recommended to avoid mail server restrictions.','wp-time-slots-booking-form'); ?></span>
                            </div>

                            <div class="wpts-field">
                                <label><?php esc_html_e('"From" email (for fixed "from" addresses)','wp-time-slots-booking-form'); ?></label>
                                <input type="text" name="fp_from_email" size="40" value="<?php echo esc_attr($this->get_option('fp_from_email', CP_TSLOTSBOOK_DEFAULT_fp_from_email)); ?>" />
                            </div>
                        </div>

                        <div class="wpts-grid wpts-cols-2">
                            <div class="wpts-field">
                                <label><?php esc_html_e('Send email "To"','wp-time-slots-booking-form'); ?></label>
                                <?php $option = $this->get_option('fp_emailtomethod', "fixed"); ?>
                                <select name="fp_emailtomethod" onchange="update_cptslotsb_option();">
                                    <option value="fixed"<?php if ($option == 'fixed') echo ' selected'; ?>><?php esc_html_e('To the fixed email(s) address(es) indicated below - Recommended option','wp-time-slots-booking-form'); ?></option>
                                    <option value="customer"<?php if ($option == 'customer') echo ' selected'; ?>><?php esc_html_e('To the email address selected in a form field (ex: captcha image enabled is recommended)','wp-time-slots-booking-form'); ?></option>
                                </select>
                                <span class="wpts-help">* <?php esc_html_e('If you select "To email in form field", add a dropdown, radio button, or checkbox that contains the destination email address in its internal value.','wp-time-slots-booking-form'); ?></span>
                            </div>

                            <div class="wpts-field" id="cptslotsb_destemails" <?php if ($option == 'customer') echo ' style="display:none;"'; ?>>
                                <label><?php esc_html_e('Destination emails (comma separated)','wp-time-slots-booking-form'); ?></label>
                                <input type="text" name="fp_destination_emails" size="40" value="<?php echo esc_attr($this->get_option('fp_destination_emails', CP_TSLOTSBOOK_DEFAULT_fp_destination_emails)); ?>" />
                            </div>

                            <div class="wpts-field" id="cptslotsb_dropemails" <?php if ($option != 'customer') echo ' style="display:none;"'; ?>>
                                <label><?php esc_html_e('Field that contains the destination email(s)','wp-time-slots-booking-form'); ?></label>
                                <select id="fp_destination_emails_field" name="fp_destination_emails_field" def="<?php echo esc_attr($this->get_option('fp_destination_emails_field', '')); ?>"></select>
                            </div>
                        </div>

                        <!-- Email Deliverability Diagnostic -->
                        <div class="wpts-subbox">
                            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                                <div>
                                    <strong style="font-size:13.5px;display:block;"><?php esc_html_e('🩺 Email Deliverability Diagnostic & Test', 'wp-time-slots-booking-form'); ?></strong>
                                    <span class="wpts-help"><?php esc_html_e('Check SMTP status, domain alignment, SPF/DMARC/MX records, and send a test email.', 'wp-time-slots-booking-form'); ?></span>
                                </div>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <span id="diag-loader" class="spinner" style="float:none;margin:0;"></span>
                                    <button id="run-diagnostic-btn" type="button" class="wpts-btn wpts-btn-soft">
                                        🔍 <?php esc_html_e('Run Email Diagnostic & Send Test', 'wp-time-slots-booking-form'); ?>
                                    </button>
                                </div>
                            </div>
                            <div id="diagnostic-results-container" style="margin-top: 16px; display: none;"></div>
                        </div>

                        <script type="text/javascript">
                        jQuery(document).ready(function($) {
                            $('#run-diagnostic-btn').on('click', function(e) {
                                e.preventDefault();
                                
                                var fromEmail = $('input[name="fp_from_email"]').val();
                                var toEmail = $('input[name="fp_destination_emails"]').val();
                                var $results = $('#diagnostic-results-container');
                                var $loader = $('#diag-loader');

                                if(!toEmail) { alert('Please enter a destination email.'); return; }

                                $loader.addClass('is-active');
                                $results.fadeOut();

                                $.ajax({
                                    url: ajaxurl,
                                    type: 'POST',
                                    data: {
                                        action: 'wpts_run_email_diagnostic',
                                        nonce: '<?php echo wp_create_nonce("email_diag_nonce"); ?>',
                                        from_email: fromEmail,
                                        to_email: toEmail
                                    },
                                    success: function(response) {
                                        $loader.removeClass('is-active');
                                        if(response.success) {
                                            $results.html(response.data.html).fadeIn();
                                        } else {
                                            $results.html('<div class="notice notice-error"><p>' + response.data + '</p></div>').fadeIn();
                                        }
                                    }
                                });
                            });
                        });
                        </script>

                        <div class="wpts-grid wpts-cols-3" style="margin-top:20px;">
                            <div class="wpts-field">
                                <label><?php esc_html_e('Email subject','wp-time-slots-booking-form'); ?></label>
                                <input type="text" name="fp_subject" size="70" value="<?php echo esc_attr($this->get_option('fp_subject', CP_TSLOTSBOOK_DEFAULT_fp_subject)); ?>" />
                            </div>
                            <div class="wpts-field">
                                <label><?php esc_html_e('Include additional information?','wp-time-slots-booking-form'); ?></label>
                                <?php $option = $this->get_option('fp_inc_additional_info', CP_TSLOTSBOOK_DEFAULT_fp_inc_additional_info); ?>
                                <select name="fp_inc_additional_info">
                                    <option value="true"<?php if ($option == 'true') echo ' selected'; ?>><?php esc_html_e('Yes','wp-time-slots-booking-form'); ?></option>
                                    <option value="false"<?php if ($option == 'false') echo ' selected'; ?>><?php esc_html_e('No','wp-time-slots-booking-form'); ?></option>
                                </select>
                            </div>
                            <div class="wpts-field">
                                <label><?php esc_html_e('Email format?','wp-time-slots-booking-form'); ?></label>
                                <?php $option = $this->get_option('fp_emailformat', CP_TSLOTSBOOK_DEFAULT_email_format); ?>
                                <select name="fp_emailformat">
                                    <option value="text"<?php if ($option != 'html') echo ' selected'; ?>><?php esc_html_e('Plain Text (default)','wp-time-slots-booking-form'); ?></option>
                                    <option value="html"<?php if ($option == 'html') echo ' selected'; ?>><?php esc_html_e('HTML (use html in the textarea below)','wp-time-slots-booking-form'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="wpts-field">
                            <label><?php esc_html_e('Message','wp-time-slots-booking-form'); ?></label>
                            <textarea name="fp_message" rows="6" cols="80"><?php echo esc_textarea($this->get_option('fp_message', CP_TSLOTSBOOK_DEFAULT_fp_message)); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Email Copy to User Card -->
                <div class="wpts-card">
                    <div class="wpts-card-header">
                        <div>
                            <h3 class="wpts-card-title">🤝 <?php esc_html_e('Email Copy to User', 'wp-time-slots-booking-form'); ?></h3>
                            <p class="wpts-card-desc"><?php esc_html_e('Configure the automatic confirmation email sent to the customer upon booking.', 'wp-time-slots-booking-form'); ?></p>
                        </div>
                    </div>
                    <div class="wpts-card-body">
                        <div class="wpts-grid wpts-cols-3">
                            <div class="wpts-field">
                                <label><?php esc_html_e('Send confirmation/thank you message to user?','wp-time-slots-booking-form'); ?></label>
                                <?php $option = $this->get_option('cu_enable_copy_to_user', CP_TSLOTSBOOK_DEFAULT_cu_enable_copy_to_user); ?>
                                <select name="cu_enable_copy_to_user">
                                    <option value="true"<?php if ($option == 'true') echo ' selected'; ?>><?php esc_html_e('Yes','wp-time-slots-booking-form'); ?></option>
                                    <option value="false"<?php if ($option == 'false') echo ' selected'; ?>><?php esc_html_e('No','wp-time-slots-booking-form'); ?></option>
                                </select>
                            </div>
                            <div class="wpts-field">
                                <label for="cu_user_email_field"><?php esc_html_e('Email field on the form','wp-time-slots-booking-form'); ?></label>
                                <select id="cu_user_email_field" name="cu_user_email_field" def="<?php echo esc_attr($this->get_option('cu_user_email_field', CP_TSLOTSBOOK_DEFAULT_cu_user_email_field)); ?>"></select>
                            </div>
                            <div class="wpts-field">
                                <label><?php esc_html_e('Email format?','wp-time-slots-booking-form'); ?></label>
                                <?php $option = $this->get_option('cu_emailformat', CP_TSLOTSBOOK_DEFAULT_email_format); ?>
                                <select name="cu_emailformat">
                                    <option value="text"<?php if ($option != 'html') echo ' selected'; ?>><?php esc_html_e('Plain Text (default)','wp-time-slots-booking-form'); ?></option>
                                    <option value="html"<?php if ($option == 'html') echo ' selected'; ?>><?php esc_html_e('HTML (use html in the textarea below)','wp-time-slots-booking-form'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="wpts-grid">
                            <div class="wpts-field">
                                <label><?php esc_html_e('Email subject','wp-time-slots-booking-form'); ?></label>
                                <input type="text" name="cu_subject" size="70" value="<?php echo esc_attr($this->get_option('cu_subject', CP_TSLOTSBOOK_DEFAULT_cu_subject)); ?>" />
                            </div>
                            <div class="wpts-field">
                                <label><?php esc_html_e('Message','wp-time-slots-booking-form'); ?></label>
                                <textarea name="cu_message" rows="6" cols="80"><?php echo esc_textarea($this->get_option('cu_message', CP_TSLOTSBOOK_DEFAULT_cu_message)); ?></textarea>
                            </div>
                        </div>

                        <div class="wpts-step-nav">
                            <button type="button" class="wpts-btn wpts-btn-secondary" onclick="ahbGoToStep(2);">
                                &larr; <?php esc_html_e('Previous: General Settings', 'wp-time-slots-booking-form'); ?>
                            </button>
                            <button type="button" class="wpts-btn wpts-btn-soft" onclick="ahbGoToStep(4);">
                                <?php esc_html_e('Next Step: Antispam', 'wp-time-slots-booking-form'); ?> &rarr;
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 STEP 4: ANTISPAM & CAPTCHA
                 ========================================== -->
            <div class="ahb-adintsection" data-step="4">
                <div class="wpts-card">
                    <div class="wpts-card-header">
                        <div>
                            <h3 class="wpts-card-title">🛡️ <?php esc_html_e('Captcha Verification', 'wp-time-slots-booking-form'); ?></h3>
                            <p class="wpts-card-desc"><?php esc_html_e('Protect your time-slot booking form from automated spam bots.', 'wp-time-slots-booking-form'); ?></p>
                        </div>
                        <div style="min-width:200px;">
                            <label class="wpts-label" style="margin-bottom:4px;"><?php esc_html_e('Use Captcha Verification?','wp-time-slots-booking-form'); ?></label>
                            <?php $option = $this->get_option('cv_enable_captcha', CP_TSLOTSBOOK_DEFAULT_cv_enable_captcha); ?>
                            <select name="cv_enable_captcha">
                                <option value="true"<?php if ($option == 'true') echo ' selected'; ?>><?php esc_html_e('Yes','wp-time-slots-booking-form'); ?></option>
                                <option value="false"<?php if ($option == 'false') echo ' selected'; ?>><?php esc_html_e('No','wp-time-slots-booking-form'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="wpts-card-body">
                        <div class="wpts-grid wpts-cols-2" style="align-items: start;">
                            <div>
                                <div class="wpts-grid wpts-cols-3">
                                    <div class="wpts-field">
                                        <label><?php esc_html_e('Width','wp-time-slots-booking-form'); ?>:</label>
                                        <input type="text" name="cv_width" size="10" value="<?php echo esc_attr($this->get_option('cv_width', CP_TSLOTSBOOK_DEFAULT_cv_width)); ?>" onblur="generateCaptcha();" />
                                    </div>
                                    <div class="wpts-field">
                                        <label><?php esc_html_e('Height','wp-time-slots-booking-form'); ?>:</label>
                                        <input type="text" name="cv_height" size="10" value="<?php echo esc_attr($this->get_option('cv_height', CP_TSLOTSBOOK_DEFAULT_cv_height)); ?>" onblur="generateCaptcha();" />
                                    </div>
                                    <div class="wpts-field">
                                        <label><?php esc_html_e('Chars','wp-time-slots-booking-form'); ?>:</label>
                                        <input type="text" name="cv_chars" size="10" value="<?php echo esc_attr($this->get_option('cv_chars', CP_TSLOTSBOOK_DEFAULT_cv_chars)); ?>" onblur="generateCaptcha();" />
                                    </div>
                                </div>

                                <div class="wpts-grid wpts-cols-2">
                                    <div class="wpts-field">
                                        <label><?php esc_html_e('Min font size','wp-time-slots-booking-form'); ?>:</label>
                                        <input type="text" name="cv_min_font_size" size="10" value="<?php echo esc_attr($this->get_option('cv_min_font_size', CP_TSLOTSBOOK_DEFAULT_cv_min_font_size)); ?>" onblur="generateCaptcha();" />
                                    </div>
                                    <div class="wpts-field">
                                        <label><?php esc_html_e('Max font size','wp-time-slots-booking-form'); ?>:</label>
                                        <input type="text" name="cv_max_font_size" size="10" value="<?php echo esc_attr($this->get_option('cv_max_font_size', CP_TSLOTSBOOK_DEFAULT_cv_max_font_size)); ?>" onblur="generateCaptcha();" />
                                    </div>
                                </div>

                                <div class="wpts-grid wpts-cols-2">
                                    <div class="wpts-field">
                                        <label><?php esc_html_e('Noise','wp-time-slots-booking-form'); ?>:</label>
                                        <input type="text" name="cv_noise" size="10" value="<?php echo esc_attr($this->get_option('cv_noise', CP_TSLOTSBOOK_DEFAULT_cv_noise)); ?>" onblur="generateCaptcha();" />
                                    </div>
                                    <div class="wpts-field">
                                        <label><?php esc_html_e('Noise Length','wp-time-slots-booking-form'); ?>:</label>
                                        <input type="text" name="cv_noise_length" size="10" value="<?php echo esc_attr($this->get_option('cv_noise_length', CP_TSLOTSBOOK_DEFAULT_cv_noise_length)); ?>" onblur="generateCaptcha();" />
                                    </div>
                                </div>

                                <div class="wpts-grid wpts-cols-3">
                                    <div class="wpts-field">
                                        <label><?php esc_html_e('Background','wp-time-slots-booking-form'); ?>:</label>
                                        <input type="color" name="cv_background" size="10" value="#<?php echo esc_attr(ltrim($this->get_option('cv_background', CP_TSLOTSBOOK_DEFAULT_cv_background),'#')); ?>" onchange="generateCaptcha();" onblur="generateCaptcha();" />
                                    </div>
                                    <div class="wpts-field">
                                        <label><?php esc_html_e('Border','wp-time-slots-booking-form'); ?>:</label>
                                        <input type="color" name="cv_border" size="10" value="#<?php echo esc_attr(ltrim($this->get_option('cv_border', CP_TSLOTSBOOK_DEFAULT_cv_border),'#')); ?>" onchange="generateCaptcha();" onblur="generateCaptcha();" />
                                    </div>
                                    <div class="wpts-field">
                                        <label><?php esc_html_e('Font','wp-time-slots-booking-form'); ?>:</label>
                                        <select name="cv_font" onchange="generateCaptcha();">
                                            <option value="font-1.ttf"<?php if ("font-1.ttf" == $this->get_option('cv_font', CP_TSLOTSBOOK_DEFAULT_cv_font)) echo " selected"; ?>>Font 1</option>
                                            <option value="font-2.ttf"<?php if ("font-2.ttf" == $this->get_option('cv_font', CP_TSLOTSBOOK_DEFAULT_cv_font)) echo " selected"; ?>>Font 2</option>
                                            <option value="font-3.ttf"<?php if ("font-3.ttf" == $this->get_option('cv_font', CP_TSLOTSBOOK_DEFAULT_cv_font)) echo " selected"; ?>>Font 3</option>
                                            <option value="font-4.ttf"<?php if ("font-4.ttf" == $this->get_option('cv_font', CP_TSLOTSBOOK_DEFAULT_cv_font)) echo " selected"; ?>>Font 4</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Live Captcha Preview -->
                            <div class="wpts-subbox" style="margin-top:0;text-align:center;padding:28px;">
                                <label class="wpts-label" style="margin-bottom:14px;"><?php esc_html_e('Preview','wp-time-slots-booking-form'); ?>:</label>
                                <div style="display:inline-block;padding:16px;background:#ffffff;border:1px solid var(--wpts-border);border-radius:var(--wpts-radius-sm);box-shadow:var(--wpts-shadow);">
                                    <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==" id="captchaimg" alt="security code" border="0" style="display:block;" />
                                </div>
                                <div style="margin-top:14px;">
                                    <button type="button" class="wpts-btn wpts-btn-secondary" onclick="generateCaptcha();">
                                        🔄 <?php esc_html_e('Refresh Preview', 'wp-time-slots-booking-form'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="wpts-step-nav">
                            <button type="button" class="wpts-btn wpts-btn-secondary" onclick="ahbGoToStep(3);">
                                &larr; <?php esc_html_e('Previous: Notification Emails', 'wp-time-slots-booking-form'); ?>
                            </button>
                            <button type="button" class="wpts-btn wpts-btn-soft" onclick="ahbGoToStep(5);">
                                <?php esc_html_e('Next Step: Reports', 'wp-time-slots-booking-form'); ?> &rarr;
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 STEP 5: AUTOMATIC REPORTS
                 ========================================== -->
            <div class="ahb-adintsection" data-step="5">
                <div class="wpts-card">
                    <div class="wpts-card-header">
                        <div>
                            <h3 class="wpts-card-title">📈 <?php esc_html_e('Automatic Reports: Send Submissions in CSV Format via Email', 'wp-time-slots-booking-form'); ?></h3>
                            <p class="wpts-card-desc"><?php esc_html_e('Receive scheduled CSV summaries of new bookings for this calendar.', 'wp-time-slots-booking-form'); ?></p>
                        </div>
                    </div>
                    <div class="wpts-card-body">
                        <div class="wpts-grid wpts-cols-3">
                            <div class="wpts-field">
                                <label><?php esc_html_e('Enable Reports?','wp-time-slots-booking-form'); ?></label>
                                <?php $option = $this->get_option('rep_enable', 'no'); ?>
                                <select name="rep_enable">
                                    <option value="no"<?php if ($option == 'no' || $option == '') echo ' selected'; ?>><?php esc_html_e('No','wp-time-slots-booking-form'); ?></option>
                                    <option value="yes"<?php if ($option == 'yes') echo ' selected'; ?>><?php esc_html_e('Yes','wp-time-slots-booking-form'); ?></option>
                                </select>
                            </div>
                            <div class="wpts-field">
                                <label><?php esc_html_e('Send report every (days)','wp-time-slots-booking-form'); ?></label>
                                <input type="text" name="rep_days" size="4" value="<?php echo esc_attr($this->get_option('rep_days', '1')); ?>" />
                                <span class="wpts-help"><?php esc_html_e('Put a 0 to send the report immediately after each submission.','wp-time-slots-booking-form'); ?></span>
                            </div>
                            <div class="wpts-field">
                                <label><?php esc_html_e('Send report after this hour (server time)','wp-time-slots-booking-form'); ?></label>
                                <select name="rep_hour">
                                    <?php
                                    $hour = $this->get_option('rep_hour', '0');
                                    for ($k=0;$k<24;$k++)
                                        echo '<option value="'.esc_attr($k).'"'.($hour==$k?' selected':'').'>'.($k<10?'0':'').esc_html($k).':00</option>';
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="wpts-grid wpts-cols-3">
                            <div class="wpts-field">
                                <label><?php esc_html_e('Send the report to the following email addresses (comma separated)','wp-time-slots-booking-form'); ?></label>
                                <input type="text" name="rep_emails" size="70" value="<?php echo esc_attr($this->get_option('rep_emails', '')); ?>" />
                            </div>
                            <div class="wpts-field">
                                <label><?php esc_html_e('Email subject','wp-time-slots-booking-form'); ?></label>
                                <input type="text" name="rep_subject" size="70" value="<?php echo esc_attr($this->get_option('rep_subject', 'Submissions report...')); ?>" />
                            </div>
                            <div class="wpts-field">
                                <label><?php esc_html_e('Email format?','wp-time-slots-booking-form'); ?></label>
                                <?php $option = $this->get_option('rep_emailformat', 'text'); ?>
                                <select name="rep_emailformat">
                                    <option value="text"<?php if ($option != 'html') echo ' selected'; ?>><?php esc_html_e('Plain Text (default)','wp-time-slots-booking-form'); ?></option>
                                    <option value="html"<?php if ($option == 'html') echo ' selected'; ?>><?php esc_html_e('HTML (use html in the textarea below)','wp-time-slots-booking-form'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="wpts-field">
                            <label><?php esc_html_e('Email Text (CSV file will be attached with the submissions)','wp-time-slots-booking-form'); ?></label>
                            <textarea name="rep_message" rows="3" cols="80"><?php echo esc_textarea($this->get_option('rep_message', 'Attached you will find the data from the form submissions.')); ?></textarea>
                        </div>

                        <div class="wpts-step-nav">
                            <button type="button" class="wpts-btn wpts-btn-secondary" onclick="ahbGoToStep(4);">
                                &larr; <?php esc_html_e('Previous: Antispam', 'wp-time-slots-booking-form'); ?>
                            </button>
                            <button type="button" class="wpts-btn wpts-btn-soft" onclick="ahbGoToStep(6);">
                                <?php esc_html_e('Next Step: Add-Ons', 'wp-time-slots-booking-form'); ?> &rarr;
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 STEP 6: ADD-ONS SETTINGS
                 ========================================== -->
            <div class="ahb-adintsection" data-step="6">
                <?php
                $printed = false;
                if ( is_array($cptslotsb_addons_active_list) && count( $cptslotsb_addons_active_list ) )
                {	
                    ob_start();
                    foreach( $cptslotsb_addons_active_list as $addon_id ) {
                        if( isset( $cptslotsb_addons_objs_list[ $addon_id ] ) ) {
                            print $cptslotsb_addons_objs_list[ $addon_id ]->get_addon_form_settings( $this->item );
                        }
                    }
                    $addon_output = ob_get_clean();
                    if (trim($addon_output) !== '') {
                        $printed = true;
                        echo '<div class="wpts-section-divider" style="margin-top:0;"><h4>🧩 ' . esc_html__( 'Active Add-Ons Settings', 'wp-time-slots-booking-form' ) . '</h4></div>';
                        echo $addon_output; // phpcs:ignore WordPress.Security.EscapeOutput
                    }
                }
                
                if (!$printed) :
                ?>
                <div class="wpts-card">
                    <div class="wpts-card-header">
                        <div>
                            <h3 class="wpts-card-title">🧩 <?php esc_html_e('Add-Ons Settings', 'wp-time-slots-booking-form'); ?></h3>
                            <p class="wpts-card-desc"><?php esc_html_e('Extend your booking calendar with payment gateways, iCal sync, SMS reminders, and third-party integrations.', 'wp-time-slots-booking-form'); ?></p>
                        </div>
                        <a href="?page=cp_timeslotsbooking_addons" target="_blank" class="wpts-btn wpts-btn-primary">
                            🧩 <?php esc_html_e('Manage Add-Ons', 'wp-time-slots-booking-form'); ?> &#8599;
                        </a>
                    </div>
                    <div class="wpts-card-body">
                        <p style="margin:0 0 8px;font-size:13.5px;">
                            <?php esc_html_e("You can optionally",'wp-time-slots-booking-form'); ?>
                            <a target="_blank" href="?page=cp_timeslotsbooking_addons" style="color:var(--wpts-primary);font-weight:700;"><?php esc_html_e("activate add-ons in the Add-Ons section",'wp-time-slots-booking-form'); ?></a>
                            <?php esc_html_e("to unlock new features for your booking forms.",'wp-time-slots-booking-form'); ?>
                        </p>
                        <p style="margin:0;font-size:13.5px;color:var(--wpts-text-muted);">
                            <?php esc_html_e("If you don't want to enable add-ons right now, simply",'wp-time-slots-booking-form'); ?>
                            <strong style="color:var(--wpts-text);"><?php esc_html_e("continue saving these settings and publishing the booking form",'wp-time-slots-booking-form'); ?></strong>.
                        </p>
                    </div>
                </div>
                <?php endif; ?>

                <div class="wpts-step-nav">
                    <button type="button" class="wpts-btn wpts-btn-secondary" onclick="ahbGoToStep(5);">
                        &larr; <?php esc_html_e('Previous: Reports', 'wp-time-slots-booking-form'); ?>
                    </button>
                    <span></span>
                </div>
            </div>

        </div>

        <!-- Sticky Bottom Save Bar -->
        <div class="wpts-sticky-footer">
            <div class="wpts-footer-links">
                <span><?php echo esc_html($this->plugin_name); ?></span>
                <span>&bull;</span>
                <a href="https://wordpress.dwbooster.com/contact-us" target="_blank"><?php esc_html_e('Request Custom Modifications','wp-time-slots-booking-form'); ?></a>
                <span>&bull;</span>
                <a href="https://wordpress.org/support/plugin/wp-time-slots-booking-form#new-post" target="_blank"><?php esc_html_e('Free Support','wp-time-slots-booking-form'); ?></a>
                <span>&bull;</span>
                <a href="<?php echo esc_url($this->plugin_URL); ?>" target="_blank"><?php esc_html_e('Help','wp-time-slots-booking-form'); ?></a>
            </div>
            <div class="wpts-header-actions">
                <a href="<?php echo esc_url(admin_url('admin.php?page='.$this->menu_parameter)); ?>" class="wpts-btn wpts-btn-secondary">
                    <?php esc_html_e('Return to Calendars List','wp-time-slots-booking-form'); ?>
                </a>
                <input type="submit" name="savepublish" value="🚀 <?php esc_attr_e('Save & Publish','wp-time-slots-booking-form'); ?>" class="wpts-btn wpts-btn-soft" />
                <input type="submit" name="savereturn" value="☑️ <?php esc_attr_e('Save & Return','wp-time-slots-booking-form'); ?>" class="wpts-btn wpts-btn-secondary" />
                <input type="submit" name="savekeep" value="💾 <?php esc_attr_e('Save Changes','wp-time-slots-booking-form'); ?>" class="wpts-btn wpts-btn-primary" />
            </div>
        </div>

    </form>
</div>

<script type="text/javascript">
function update_cptslotsb_option() 
{
    var seloption = document.cpformconf.fp_emailtomethod.selectedIndex;
    if (seloption == 0)
    {
        document.getElementById("cptslotsb_destemails").style.display = '';
        document.getElementById("cptslotsb_dropemails").style.display = 'none';
    }
    else
    {
        document.getElementById("cptslotsb_destemails").style.display = 'none';
        document.getElementById("cptslotsb_dropemails").style.display = '';
    }    
}
update_cptslotsb_option();

var randcaptcha = 1;
function generateCaptcha()
{            
    var d = new Date();
    var f = document.cpformconf;
    if (!f || !f.cv_background) return;
    var qs = "&width="+f.cv_width.value;
    qs += "&height="+f.cv_height.value;
    qs += "&letter_count="+f.cv_chars.value;
    qs += "&min_size="+f.cv_min_font_size.value;
    qs += "&max_size="+f.cv_max_font_size.value;
    qs += "&noise="+f.cv_noise.value;
    qs += "&noiselength="+f.cv_noise_length.value;
    qs += "&bcolor="+f.cv_background.value.replace('#','');
    qs += "&border="+f.cv_border.value.replace('#','');
    qs += "&font="+f.cv_font.options[f.cv_font.selectedIndex].value;
    qs += "&r="+(randcaptcha++);
    
    document.getElementById("captchaimg").src = <?php echo wp_json_encode( esc_url_raw( $this->get_site_url(true).'/?'.$this->prefix.'_captcha=captcha&inAdmin=1&preview=1' ) ); ?> + qs;
}
generateCaptcha();

function wptsCopyShortcode(btn, text) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function() {
            var orig = btn.textContent;
            btn.textContent = '✓ Copied!';
            setTimeout(function() { btn.textContent = orig; }, 1800);
        });
    } else {
        var temp = document.createElement('input');
        document.body.appendChild(temp);
        temp.value = text;
        temp.select();
        document.execCommand('copy');
        document.body.removeChild(temp);
        btn.textContent = '✓ Copied!';
    }
}

jQuery(function(){
    var $ = jQuery;
    var appWrap = document.getElementById('wptsAdminApp');
    var toggleBtn = document.getElementById('wptsToggleAllBtn');
    var storageKey = 'wpts_active_step_<?php echo intval($this->item); ?>';

    window['ahbGoToStep'] = function(s, skipScroll){
        if (appWrap) appWrap.classList.remove('wpts-show-all');
        if (toggleBtn) toggleBtn.textContent = '<?php echo esc_js(__('Show All Sections', 'wp-time-slots-booking-form')); ?>';

        $('.ahb-step.ahb-step-active').removeClass('ahb-step-active');
        $('.ahb-step[data-step="'+s+'"]').addClass('ahb-step-active');
        $('.ahb-adintsection.ahb-adintsection-active').removeClass('ahb-adintsection-active');
        $('.ahb-adintsection[data-step="'+s+'"]').addClass('ahb-adintsection-active');

        try { sessionStorage.setItem(storageKey, s); } catch(e) {}

        if (!skipScroll && $("#topadminsection").length) {
            $(window).scrollTop( $("#topadminsection").offset().top - 20 );
        }
    };

    $(document).on('click', '.ahb-step', function(){
        var s = $(this).data('step');
        ahbGoToStep(s, true);
    });

    window.wptsToggleAllSections = function() {
        var isShowingAll = appWrap.classList.toggle('wpts-show-all');
        if (toggleBtn) {
            toggleBtn.textContent = isShowingAll
                ? '<?php echo esc_js(__('Use Tabbed View', 'wp-time-slots-booking-form')); ?>'
                : '<?php echo esc_js(__('Show All Sections', 'wp-time-slots-booking-form')); ?>';
        }
    };

    try {
        var savedStep = sessionStorage.getItem(storageKey);
        if (savedStep && $('.ahb-adintsection[data-step="'+savedStep+'"]').length) {
            ahbGoToStep(savedStep, true);
        }
    } catch(e) {}

    /* Auto-save Form Builder structure before main form submit */
    $('#cpformconf').on('submit', function() {
        if (window['cff_form'] && window['cff_form'].fBuild) {
            try { window['cff_form'].fBuild.saveData("form_structure"); } catch(e) {}
        }
    });

    if ($("#admin-tabs").length && $.fn.tabs) {
        $("#admin-tabs").tabs();
    }
});
</script>