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
/* Page-specific styles: Embedded #fbuilder Form inside Admin Dashboard */
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
    .wpts-admin-wrap #fbuilder input[type="text"],
    .wpts-admin-wrap #fbuilder input[type="email"],
    .wpts-admin-wrap #fbuilder input[type="number"],
    .wpts-admin-wrap #fbuilder input[type="password"],
    .wpts-admin-wrap #fbuilder select,
    .wpts-admin-wrap #fbuilder textarea {
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
    .wpts-admin-wrap #fbuilder select {
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
    .wpts-admin-wrap #fbuilder input:focus,
    .wpts-admin-wrap #fbuilder select:focus,
    .wpts-admin-wrap #fbuilder textarea:focus {
        border-color: var(--wpts-primary) !important;
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.14) !important;
        outline: none !important;
    }
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

<div class="wrap wpts-admin-wrap wpts-wrap-xs">

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

    <div class="wpts-alert wpts-alert-info" style="margin-top:0;">
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