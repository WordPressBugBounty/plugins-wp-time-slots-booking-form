<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wpdb;

$current_user = wp_get_current_user();
$current_user_access = current_user_can('manage_options');

if ( !is_admin() || (!$current_user_access && !@in_array($current_user->ID, unserialize($this->get_option("cp_user_access","")))))
{
    echo 'Direct access not allowed.';
    exit;
}

$this->item = intval($_GET["cal"]);

$message = '';
$booking_added = ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset( $_POST[$this->prefix.'_pform_process'] ) );

$nonce = wp_create_nonce( 'cpappb_actions_admin' );

$form_name = $this->get_option('form_name', 'Calendar');
$all_forms = $wpdb->get_results( "SELECT id, form_name, cp_user_access FROM " . $wpdb->prefix . $this->table_items );
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
        max-width: 1080px;
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
    .wpts-toast a {
        color: #047857;
        font-weight: 700;
        text-decoration: underline;
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
    .wpts-badge {
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

    /* Buttons */
    .wpts-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 8px 14px;
        border-radius: var(--wpts-radius-sm);
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.15s ease;
        border: 1px solid transparent;
        line-height: 1.2;
        white-space: nowrap;
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

    /* Info Callout */
    .wpts-alert-info {
        background: var(--wpts-accent-light);
        border: 1px solid var(--wpts-accent-border);
        color: #0369a1;
        padding: 13px 18px;
        border-radius: var(--wpts-radius-sm);
        font-size: 13px;
        line-height: 1.5;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Card Wrapper for the Form */
    .wpts-card {
        background: var(--wpts-surface);
        border: 1px solid var(--wpts-border);
        border-radius: var(--wpts-radius);
        box-shadow: var(--wpts-shadow);
        overflow: hidden;
    }
    .wpts-card-header {
        padding: 16px 24px;
        border-bottom: 1px solid var(--wpts-border);
        background: #fcfcfd;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .wpts-card-title {
        margin: 0;
        font-size: 15.5px;
        font-weight: 700;
        color: var(--wpts-text);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .wpts-card-body {
        padding: 24px;
    }

    /* Administrator Options Box (.ahbadminoptions) */
    .wpts-admin-wrap fieldset.ahbadminoptions {
        background: var(--wpts-bg) !important;
        border: 1px solid var(--wpts-border) !important;
        border-left: 4px solid var(--wpts-primary) !important;
        border-radius: var(--wpts-radius-sm) !important;
        padding: 16px 20px !important;
        margin: 0 0 24px 0 !important;
    }
    .wpts-admin-wrap fieldset.ahbadminoptions legend {
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--wpts-primary-hover);
        padding: 0 6px;
    }

    /* Modernizing the Embedded #fbuilder Form inside Admin */
    .wpts-admin-wrap #fbuilder {
        max-width: 100%;
        font-family: inherit;
        color: var(--wpts-text);
    }
    .wpts-admin-wrap #fbuilder .fform h1,
    .wpts-admin-wrap #fbuilder .fform h2 {
        font-size: 18px;
        font-weight: 700;
        margin: 0 0 6px;
        color: var(--wpts-text);
    }
    .wpts-admin-wrap #fbuilder .fields {
        margin-bottom: 18px;
        padding: 4px 0;
    }
    .wpts-admin-wrap #fbuilder .fields > label {
        font-size: 13px;
        font-weight: 600;
        color: var(--wpts-text);
        margin-bottom: 6px;
        display: block;
    }
    .wpts-admin-wrap input[type="text"],
    .wpts-admin-wrap input[type="email"],
    .wpts-admin-wrap input[type="number"],
    .wpts-admin-wrap input[type="password"],
    .wpts-admin-wrap select,
    .wpts-admin-wrap textarea {
        padding: 8px 12px;
        font-size: 13.5px;
        line-height: 1.4;
        color: var(--wpts-text);
        background-color: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: var(--wpts-radius-sm);
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
        box-shadow: none;
        min-height: 38px;
    }
    .wpts-admin-wrap select {
        padding-right: 32px !important;
        background-position: right 8px center;
    }
    .wpts-admin-wrap #fbuilder input[type="text"],
    .wpts-admin-wrap #fbuilder input[type="email"],
    .wpts-admin-wrap #fbuilder input[type="number"],
    .wpts-admin-wrap #fbuilder input[type="password"],
    .wpts-admin-wrap #fbuilder textarea {
        width: 100% !important;
        max-width: 620px;
    }
    .wpts-admin-wrap input:focus,
    .wpts-admin-wrap select:focus,
    .wpts-admin-wrap textarea:focus {
        border-color: var(--wpts-primary) !important;
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.14) !important;
        outline: none !important;
    }

    /* Submit Button (No display !important so JS hides fallback duplicate) */
    .wpts-admin-wrap .pbSubmit,
    .wpts-admin-wrap .cp_subbtn {
        padding: 10px 22px !important;
        background: var(--wpts-primary) !important;
        color: #ffffff !important;
        border: none !important;
        border-radius: var(--wpts-radius-sm) !important;
        font-size: 13.5px !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        box-shadow: 0 1px 2px rgba(13, 148, 136, 0.25) !important;
        transition: all 0.15s ease !important;
        margin-top: 8px !important;
    }
    .wpts-admin-wrap .pbSubmit:hover,
    .wpts-admin-wrap .cp_subbtn:hover {
        background: var(--wpts-primary-hover) !important;
        transform: translateY(-1px);
    }
</style>

<div class="wrap wpts-admin-wrap">

    <?php if ( $booking_added ) : ?>
    <div id="setting-error-settings_updated" class="wpts-toast">
        <span>
            ✅ <strong><?php esc_html_e('Booking added.', 'wp-time-slots-booking-form'); ?></strong>
            <?php esc_html_e('It appears now in the', 'wp-time-slots-booking-form'); ?>
            <a href="?page=<?php echo esc_attr($this->menu_parameter); ?>&cal=<?php echo intval($this->item); ?>&list=1"><?php esc_html_e('bookings list', 'wp-time-slots-booking-form'); ?></a>.
        </span>
        <button type="button" class="wpts-btn wpts-btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="this.parentElement.style.display='none'">Dismiss</button>
    </div>
    <?php endif; ?>

    <?php if ( $message ) : ?>
    <div id="setting-error-settings_updated" class="wpts-toast">
        <span>✅ <strong><?php echo esc_html($message); ?></strong></span>
        <button type="button" class="wpts-btn wpts-btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="this.parentElement.style.display='none'">Dismiss</button>
    </div>
    <?php endif; ?>

    <!-- Top Header -->
    <div class="wpts-header">
        <div class="wpts-header-left">
            <a href="<?php echo esc_url(admin_url('admin.php?page='.$this->menu_parameter)); ?>" class="wpts-back-btn">
                &larr; <?php esc_html_e('Return to the calendars list', 'wp-time-slots-booking-form'); ?>
            </a>
            <div class="wpts-title-group">
                <h1>
                    ➕ <?php esc_html_e('Add Booking', 'wp-time-slots-booking-form'); ?>
                    <span class="wpts-badge"><?php echo esc_html($form_name); ?> (#<?php echo intval($this->item); ?>)</span>
                </h1>
                <p class="wpts-subtitle"><?php echo esc_html($this->plugin_name); ?> &mdash; <?php esc_html_e('Manually schedule a new appointment or time-slot reservation', 'wp-time-slots-booking-form'); ?></p>
            </div>
        </div>

        <div class="wpts-header-actions">
            <a href="<?php echo esc_url(admin_url('admin.php?page='.$this->menu_parameter.'&cal='.$this->item.'&list=1')); ?>" class="wpts-btn wpts-btn-accent">
                📋 <?php esc_html_e('Booking Orders', 'wp-time-slots-booking-form'); ?>
            </a>            
            <a href="<?php echo esc_url(admin_url('admin.php?page='.$this->menu_parameter.'&cal='.$this->item.'&schedule=1&calendarview=1')); ?>" class="wpts-btn wpts-btn-secondary">
                📅 <?php esc_html_e('Schedule View', 'wp-time-slots-booking-form'); ?>
            </a>
            <?php if ($current_user_access) : ?>
            <a href="<?php echo esc_url(admin_url('admin.php?page='.$this->menu_parameter.'&cal='.$this->item)); ?>" class="wpts-btn wpts-btn-secondary">
                ⚙️ <?php esc_html_e('Edit Calendar', 'wp-time-slots-booking-form'); ?>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="wpts-alert-info">
        <span>ℹ️</span>
        <div>
            <?php esc_html_e('This page is for adding bookings from the administration area. The captcha and payment process are disabled in order to allow the website manager easily adding bookings.', 'wp-time-slots-booking-form'); ?>
        </div>
    </div>

    <!-- Main Booking Form Card -->
    <div class="wpts-card">
        <div class="wpts-card-header">
            <h3 class="wpts-card-title">🗓️ <?php esc_html_e('Select Time Slots & Customer Details', 'wp-time-slots-booking-form'); ?></h3>
            <?php if ( is_array($all_forms) && count($all_forms) > 1 ) : ?>
            <div style="display:flex;align-items:center;gap:8px;font-size:12.5px;">
                <label for="wpts_switch_cal" style="font-weight:600;color:var(--wpts-text-muted);"><?php esc_html_e('Switch Calendar:', 'wp-time-slots-booking-form'); ?></label>
                <select id="wpts_switch_cal" onchange="window.location.href='admin.php?page=<?php echo esc_js($this->menu_parameter); ?>&addbk=1&cal='+this.value;">
                    <?php foreach ( $all_forms as $f ) : 
                        $u_access = @unserialize($f->cp_user_access);
                        if ( $current_user_access || (is_array($u_access) && in_array($current_user->ID, $u_access)) ) :
                    ?>
                        <option value="<?php echo intval( $f->id ); ?>" <?php selected( intval( $f->id ), intval( $this->item ) ); ?>>
                            #<?php echo intval( $f->id ); ?> — <?php echo esc_html( $f->form_name ); ?>
                        </option>
                    <?php 
                        endif;
                    endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
        </div>
        <div class="wpts-card-body">
            <script>var wptimesbk_in_admin=true;</script>
            <?php echo $this->clean_sanitize($this->filter_content(array('id' => $this->item))); ?>
        </div>
    </div>

</div>