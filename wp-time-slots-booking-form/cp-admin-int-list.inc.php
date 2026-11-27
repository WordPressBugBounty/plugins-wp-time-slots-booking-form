<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! is_admin() ) 
{
    echo 'Direct access not allowed.';
    exit;
}

$current_user_access   = current_user_can('manage_options');
$current_user_can_edit = current_user_can('edit_posts');

global $wpdb, $cptslotsb_addons_active_list, $cptslotsb_addons_objs_list;

$message = "";

if (isset($_GET['a']) && $_GET['a'] == '1' && $current_user_access)
{
    $this->verify_nonce(sanitize_text_field($_GET["anonce"]), 'cptslotsb_actions_list');
    $new_cal_name = isset($_GET["name"]) ? stripcslashes(sanitize_text_field($_GET["name"])) : '';
    $new_cal_id   = $this->create_calendar($new_cal_name);

    echo "<script>document.location='?page=cp_timeslotsbooking&cal=".intval($new_cal_id)."';</script>";
    exit;
} 
else if (isset($_GET['u']) && $_GET['u'] != '' && $current_user_access)
{
    $this->verify_nonce(sanitize_text_field($_GET["anonce"]), 'cptslotsb_actions_list');
    $wpdb->query( $wpdb->prepare('UPDATE `'.$wpdb->prefix.$this->table_items.'` SET form_name=%s WHERE id=%d', sanitize_text_field($_GET["name"]), intval($_GET['u'])) );
    $message = __('Item updated','wp-time-slots-booking-form');        
} 
else if (isset($_GET['d']) && $_GET['d'] != '' && $current_user_access)
{
    $this->verify_nonce(sanitize_text_field($_GET["anonce"]), 'cptslotsb_actions_list');
    $wpdb->query( $wpdb->prepare('DELETE FROM `'.$wpdb->prefix.$this->table_items.'` WHERE id=%d', intval($_GET['d'])) );
    $message = __('Item deleted','wp-time-slots-booking-form');
} 
else if (isset($_GET['c']) && $_GET['c'] != '' && $current_user_access)
{
    $this->verify_nonce(sanitize_text_field($_GET["anonce"]), 'cptslotsb_actions_list');
    $myrows = $wpdb->get_row( $wpdb->prepare("SELECT * FROM ".$wpdb->prefix.$this->table_items." WHERE id=%d", intval($_GET['c'])) , ARRAY_A);    
    if (!empty($myrows)) {
        unset($myrows["id"]);
        $myrows["form_name"] = 'Cloned: '.$myrows["form_name"];
        $wpdb->insert( $wpdb->prefix.$this->table_items, $myrows);
        $message = __('Item duplicated/cloned','wp-time-slots-booking-form');
    }
}

if (isset($_GET['dismiss_qw']) && $_GET['dismiss_qw'] == '1' && $current_user_access)
{
    $this->verify_nonce(sanitize_text_field($_GET["anonce"]), 'cptslotsb_actions_list');
    update_option('cptslotsb_quickstart_dismissed', '1');
}

if (isset($_GET["confirm"]))
    $message = __('Settings updated','wp-time-slots-booking-form');

$nonce        = wp_create_nonce( 'cptslotsb_actions_list' );
$current_user = wp_get_current_user();
$myrows       = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix.$this->table_items );

$show_quickstart_welcome = false;
$qw_first_cal_id = (!empty($myrows) && count($myrows) === 1) ? intval($myrows[0]->id) : 0;
if (
    $current_user_access &&
    get_option('cptslotsb_quickstart_dismissed', '') !== '1' &&
    get_option('cptslotsb_quickstart_completed', '') !== '1' &&
    count($myrows) <= 1
) {
    $total_bookings_count = intval($wpdb->get_var("SELECT COUNT(id) FROM " . $wpdb->prefix . $this->table_messages));
    if ($total_bookings_count === 0) {
        $show_quickstart_welcome = true;
    }
}
?>

<style type="text/css">
/* Page-specific styles: Calendars List Table & Quick Create Bar */
    .wpts-create-bar {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .wpts-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }
    .wpts-table th {
        background: var(--wpts-bg);
        color: var(--wpts-text-muted);
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 13px 18px;
        border-bottom: 1px solid var(--wpts-border);
    }
    .wpts-table td {
        padding: 16px 18px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: 13.5px;
    }
    .wpts-table tr:last-child td {
        border-bottom: none;
    }
    .wpts-table tbody tr:hover td {
        background: #f8fafc;
    }
    .wpts-name-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .wpts-name-group input[type="text"] {
        min-width: 190px;
        font-weight: 600;
    }
    .wpts-actions-cluster {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px;
    }
    .wpts-cluster-divider {
        width: 1px;
        height: 22px;
        background: var(--wpts-border);
        margin: 0 4px;
    }
    .wpts-list-card-body {
        overflow: visible;
    }
    .wpts-card.wpts-list-card {
        overflow: visible;
    }
    .wpts-table-wrap.wpts-allow-overflow {
        overflow: visible;
    }
    @media (max-width: 1080px) {
        .wpts-table-wrap.wpts-allow-overflow {
            overflow-x: auto;
        }
    }
</style>

<script type="text/javascript">
function cp_addItem()
{
    var calname = document.getElementById("cp_itemname").value;
    document.location = 'admin.php?page=<?php echo esc_js($this->menu_parameter); ?>&anonce=<?php echo esc_js($nonce); ?>&a=1&r='+Math.random()+'&name='+encodeURIComponent(calname);       
}

function cp_updateItem(id, btn)
{
    var calname = document.getElementById("calname_"+id).value;    
    var url = 'admin.php?page=<?php echo esc_js($this->menu_parameter); ?>&anonce=<?php echo esc_js($nonce); ?>&u='+id+'&r='+Math.random()+'&name='+encodeURIComponent(calname);
    if (typeof jQuery !== 'undefined' && btn) {
        var $btn = jQuery(btn);
        $btn.prop('disabled', true);
        jQuery.get(url, function() {
            $btn.prop('disabled', false);
            var check = jQuery('<span style="color:#10b981; margin-left:4px; font-weight:bold;">&#10003;</span>');
            $btn.after(check);
            setTimeout(function() { check.fadeOut(400, function() { jQuery(this).remove(); }); }, 1800);
        });
    } else {
        document.location = url;
    }
}

function cp_cloneItem(id)
{
    document.location = 'admin.php?page=<?php echo esc_js($this->menu_parameter); ?>&anonce=<?php echo esc_js($nonce); ?>&c='+id+'&r='+Math.random();  
} 

function cp_manageSettings(id)
{
    document.location = 'admin.php?page=<?php echo esc_js($this->menu_parameter); ?>&cal='+id+'&r='+Math.random();
}

function cp_publish(id)
{
    document.location = 'admin.php?page=cp_timeslotsbooking&pwizard=1&cal='+id+'&r='+Math.random();
}

function cp_addbk(id)
{
    document.location = 'admin.php?page=<?php echo esc_js($this->menu_parameter); ?>&addbk=1&cal='+id+'&r='+Math.random();
} 

function cp_viewMessages(id)
{
    document.location = 'admin.php?page=<?php echo esc_js($this->menu_parameter); ?>&cal='+id+'&list=1&r='+Math.random();
} 

function cp_viewSchedule(id)
{
    document.location = 'admin.php?page=<?php echo esc_js($this->menu_parameter); ?>&cal='+id+'&schedule=1&calendarview=1&r='+Math.random();
}

function cp_viewReport(id)
{
    document.location = 'admin.php?page=<?php echo esc_js($this->menu_parameter); ?>&cal='+id+'&report=1&r='+Math.random();
} 

function cp_deleteItem(id, btn)
{
    if (confirm('<?php echo esc_js(__('Are you sure that you want to delete this item?', 'wp-time-slots-booking-form')); ?>'))
    {        
        var url = 'admin.php?page=<?php echo esc_js($this->menu_parameter); ?>&anonce=<?php echo esc_js($nonce); ?>&d='+id+'&r='+Math.random();
        if (typeof jQuery !== 'undefined' && btn) {
            var $tr = jQuery(btn).closest('tr');
            $tr.css('opacity', '0.4');
            jQuery.get(url, function() {
                $tr.fadeOut(350, function() { jQuery(this).remove(); });
            });
        } else {
            document.location = url;
        }
    }
}

function wpts_copy_shortcode(text, btn)
{
    if (typeof window.wptsCopyText === 'function') {
        window.wptsCopyText(text, btn, '✓ Copied');
    }
}

function wptsToggleMoreMenu(btn, event)
{
    if (event) event.stopPropagation();
    var wrap = btn.closest('.wpts-more-wrap');
    if (!wrap) return;
    var wasOpen = wrap.classList.contains('is-open');
    document.querySelectorAll('.wpts-more-wrap.is-open').forEach(function(el) {
        el.classList.remove('is-open');
        var t = el.querySelector('.wpts-more-trigger');
        if (t) t.setAttribute('aria-expanded', 'false');
    });
    if (!wasOpen) {
        wrap.classList.add('is-open');
        btn.setAttribute('aria-expanded', 'true');
    }
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('.wpts-more-wrap')) {
        document.querySelectorAll('.wpts-more-wrap.is-open').forEach(function(el) {
            el.classList.remove('is-open');
            var t = el.querySelector('.wpts-more-trigger');
            if (t) t.setAttribute('aria-expanded', 'false');
        });
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.wpts-more-wrap.is-open').forEach(function(el) {
            el.classList.remove('is-open');
            var t = el.querySelector('.wpts-more-trigger');
            if (t) t.setAttribute('aria-expanded', 'false');
        });
    }
});
</script>

<div class="wrap wpts-admin-wrap">

    <?php if ($message && !isset($_GET['u']) && !isset($_GET['d'])) : ?>
    <div id="setting-error-settings_updated" class="wpts-toast">
        <span>✅ <strong><?php echo esc_html($message); ?></strong></span>
        <button type="button" class="wpts-copy-btn" onclick="this.parentElement.style.display='none'">Dismiss</button>
    </div>
    <?php endif; ?>

    <!-- Top Hero & Navigation Hub -->
    <div class="wpts-hero-shell">
        <div class="wpts-hero-top">
            <div class="wpts-brand-group">
                <div class="wpts-brand-icon">🗓️</div>
                <div class="wpts-title-group">
                    <h1>
                        <?php echo esc_html($this->plugin_name); ?>
                        <span class="wpts-count-pill"><?php echo count($myrows); ?> <?php echo (count($myrows) === 1 ? __('Calendar', 'wp-time-slots-booking-form') : __('Calendars', 'wp-time-slots-booking-form')); ?></span>
                    </h1>
                    <p class="wpts-subtitle"><?php esc_html_e('Manage time-slot booking calendars, appointment schedules, customer orders, and availability rules.', 'wp-time-slots-booking-form'); ?></p>
                </div>
            </div>

            <?php if ($current_user_access) : ?>
            <div class="wpts-create-bar">
                <input type="text" name="cp_itemname" id="cp_itemname" placeholder="<?php esc_attr_e('New Calendar Name...', 'wp-time-slots-booking-form'); ?>" class="ahb-new-calendar" onkeydown="if(event.key==='Enter'){event.preventDefault();cp_addItem();}" style="min-width:200px;" />
                <button type="button" class="wpts-btn wpts-btn-primary" onclick="cp_addItem();">
                    ➕ <?php esc_html_e('Add New Calendar', 'wp-time-slots-booking-form'); ?>
                </button>
                <a href="<?php echo esc_url(admin_url('admin.php?page=' . $this->menu_parameter . '&qwizard=1')); ?>" class="wpts-btn wpts-btn-soft" title="<?php esc_attr_e('Create a new booking calendar step by step', 'wp-time-slots-booking-form'); ?>">
                    ✨ <?php esc_html_e('Guided Setup', 'wp-time-slots-booking-form'); ?>
                </a>
            </div>
            <?php endif; ?>
        </div>

        <?php $this->render_admin_subnav('list'); ?>
    </div>

    <?php if ($show_quickstart_welcome) :
        $qw_launch_url = admin_url('admin.php?page=' . $this->menu_parameter . '&qwizard=1' . ($qw_first_cal_id > 0 ? '&cal=' . $qw_first_cal_id : ''));
        $qw_dismiss_url = admin_url('admin.php?page=' . $this->menu_parameter . '&dismiss_qw=1&anonce=' . $nonce);
    ?>
    <!-- First-Run Quick Start Welcome Card (non-blocking, dismissible) -->
    <div class="wpts-welcome-card">
        <div class="wpts-welcome-content">
            <span class="wpts-welcome-kicker">✨ <?php esc_html_e('Quick Start · ~2 Minutes', 'wp-time-slots-booking-form'); ?></span>
            <h2><?php esc_html_e('Set up your first booking calendar in 4 easy steps', 'wp-time-slots-booking-form'); ?></h2>
            <p><?php esc_html_e('Pick a booking preset, generate your weekly time slots with live preview, test email notifications, and publish directly to a page—no manual field digging required.', 'wp-time-slots-booking-form'); ?></p>
        </div>
        <div class="wpts-welcome-actions">
            <a href="<?php echo esc_url($qw_launch_url); ?>" class="wpts-btn wpts-btn-primary">
                ✨ <?php esc_html_e('Launch Quick Start Wizard', 'wp-time-slots-booking-form'); ?> &rarr;
            </a>
            <a href="<?php echo esc_url($qw_dismiss_url); ?>" class="wpts-btn wpts-btn-secondary">
                <?php esc_html_e('Skip — configure manually', 'wp-time-slots-booking-form'); ?>
            </a>
        </div>
    </div>
    <?php endif; ?>

    <!-- Calendars List Card -->
    <div class="wpts-card wpts-list-card">
        <div class="wpts-card-header">
            <div>
                <h3 style="margin:0;font-size:16px;font-weight:700;">🗂️ <?php esc_html_e('Calendars List', 'wp-time-slots-booking-form'); ?></h3>
                <p class="wpts-card-desc"><?php esc_html_e('Configure time-slot rules, inspect your appointment schedule, manage booking orders, or embed shortcodes.', 'wp-time-slots-booking-form'); ?></p>
            </div>
        </div>

        <div class="wpts-table-wrap wpts-allow-overflow">
            <table class="wpts-table ahb-calendars-list"> 
                <thead>
                    <tr>
                        <th style="width:65px;"><?php esc_html_e('ID', 'wp-time-slots-booking-form'); ?></th>
                        <th style="min-width:230px;"><?php esc_html_e('Form / Calendar Name', 'wp-time-slots-booking-form'); ?></th>
                        <th><?php esc_html_e('Calendar Actions & Operations', 'wp-time-slots-booking-form'); ?></th>
                        <?php if ($current_user_can_edit) : ?>
                            <th style="width:290px;"><?php esc_html_e('Shortcode for Pages & Posts', 'wp-time-slots-booking-form'); ?></th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                <?php  
                foreach ($myrows as $item) {
                    $user_access_arr = @unserialize($item->cp_user_access);
                    if ($current_user_access || (is_array($user_access_arr) && in_array($current_user->ID, $user_access_arr))) {
                        $shortcode_str = '['.$this->shorttag.' id="'.intval($item->id).'"]';
                ?>
                    <tr> 
                        <td>
                            <span class="wpts-id-badge">#<?php echo esc_html($item->id); ?></span>
                        </td>
                        <td>
                            <div class="wpts-name-group">
                                <?php if ($current_user_access) : ?>
                                    <input type="text" name="calname_<?php echo esc_attr($item->id); ?>" id="calname_<?php echo esc_attr($item->id); ?>" value="<?php echo esc_attr($item->form_name); ?>" onkeydown="if(event.key==='Enter'){event.preventDefault();cp_updateItem(<?php echo esc_attr($item->id); ?>, this.nextElementSibling);}" />
                                    <button class="wpts-btn wpts-btn-secondary" type="button" name="calupdate_<?php echo esc_attr($item->id); ?>" onclick="cp_updateItem(<?php echo esc_attr($item->id); ?>, this);" title="<?php esc_attr_e('Save New Name', 'wp-time-slots-booking-form'); ?>">
                                        <?php esc_html_e('Rename', 'wp-time-slots-booking-form'); ?>
                                    </button>
                                <?php else : ?>
                                    <strong style="font-size:14px;"><?php echo esc_html($item->form_name); ?></strong>
                                <?php endif; ?>
                            </div>
                        </td>          
                        <td>
                            <div class="wpts-actions-cluster">
                                <?php if ($current_user_access || @$item->cp_user_access_settings == 'true') : ?>
                                    <button class="wpts-btn wpts-btn-sm wpts-btn-primary" type="button" name="calmanage_<?php echo esc_attr($item->id); ?>" onclick="cp_manageSettings(<?php echo esc_attr($item->id); ?>);">
                                        ⚙️ <?php esc_html_e('Edit', 'wp-time-slots-booking-form'); ?>
                                    </button>
                                <?php endif; ?>

                                <button class="wpts-btn wpts-btn-sm wpts-btn-accent" type="button" name="calschedule_<?php echo esc_attr($item->id); ?>" onclick="cp_viewSchedule(<?php echo esc_attr($item->id); ?>);">
                                    📅 <?php esc_html_e('Schedule', 'wp-time-slots-booking-form'); ?>
                                </button>
                                <button class="wpts-btn wpts-btn-sm wpts-btn-accent" type="button" name="calmessages_<?php echo esc_attr($item->id); ?>" onclick="cp_viewMessages(<?php echo esc_attr($item->id); ?>);">
                                    📋 <?php esc_html_e('Booking Orders', 'wp-time-slots-booking-form'); ?>
                                </button>

                                <?php if ($current_user_access) : ?>
                                    <button class="wpts-btn wpts-btn-sm wpts-btn-soft" type="button" name="calpublish_<?php echo esc_attr($item->id); ?>" onclick="cp_publish(<?php echo esc_attr($item->id); ?>);">
                                        🚀 <?php esc_html_e('Publish', 'wp-time-slots-booking-form'); ?>
                                    </button>
                                <?php endif; ?>

                                <div class="wpts-more-wrap">
                                    <button type="button" class="wpts-btn wpts-btn-sm wpts-btn-secondary wpts-more-trigger" aria-haspopup="true" aria-expanded="false" onclick="wptsToggleMoreMenu(this, event);">
                                        <?php esc_html_e('More', 'wp-time-slots-booking-form'); ?> &#9662;
                                    </button>
                                    <div class="wpts-more-menu" role="menu">
                                        <button type="button" class="wpts-more-item" role="menuitem" name="caladdbk_<?php echo esc_attr($item->id); ?>" onclick="cp_addbk(<?php echo esc_attr($item->id); ?>);">
                                            <span>➕</span> <?php esc_html_e('Add Booking', 'wp-time-slots-booking-form'); ?>
                                        </button>
                                        <button type="button" class="wpts-more-item" role="menuitem" name="calreport_<?php echo esc_attr($item->id); ?>" onclick="cp_viewReport(<?php echo esc_attr($item->id); ?>);">
                                            <span>📊</span> <?php esc_html_e('Stats', 'wp-time-slots-booking-form'); ?>
                                        </button>
                                        <?php if ($current_user_access) : ?>
                                            <div class="wpts-more-sep"></div>
                                            <button type="button" class="wpts-more-item" role="menuitem" name="calclone_<?php echo esc_attr($item->id); ?>" onclick="cp_cloneItem(<?php echo esc_attr($item->id); ?>);">
                                                <span>📑</span> <?php esc_html_e('Clone', 'wp-time-slots-booking-form'); ?>
                                            </button>
                                            <button type="button" class="wpts-more-item is-danger" role="menuitem" name="caldelete_<?php echo esc_attr($item->id); ?>" onclick="cp_deleteItem(<?php echo esc_attr($item->id); ?>, this);">
                                                <span>🗑️</span> <?php esc_html_e('Delete', 'wp-time-slots-booking-form'); ?>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <?php if ($current_user_can_edit) : ?>
                        <td>
                            <div class="wpts-shortcode-pill">
                                <span><?php echo esc_html($shortcode_str); ?></span>
                                <button type="button" class="wpts-copy-btn" onclick="wpts_copy_shortcode('<?php echo esc_js($shortcode_str); ?>', this);">
                                    <?php esc_html_e('Copy', 'wp-time-slots-booking-form'); ?>
                                </button>
                            </div>
                        </td>
                        <?php endif; ?>
                    </tr>
                <?php  
                    }
                } 
                ?>   
                </tbody>
            </table> 
        </div>
    </div>

    <!-- Active Add-Ons Settings Container -->
    <div id="normal-sortables" class="meta-box-sortables"> 
        <?php if ($current_user_access) : ?> 
            <?php
            if ( is_array($cptslotsb_addons_active_list) && count( $cptslotsb_addons_active_list ) )
            {	
                foreach( $cptslotsb_addons_active_list as $addon_id ) {
                    if( isset( $cptslotsb_addons_objs_list[ $addon_id ] ) ) {
                        print $cptslotsb_addons_objs_list[ $addon_id ]->get_addon_settings();
                    }
                }
            }
            ?>  
        <?php endif; ?>
    </div> 

    <!-- Bottom Help Bar -->
    <div class="wpts-footer-bar">
        <span><strong><?php echo esc_html($this->plugin_name); ?></strong> &mdash; <?php esc_html_e('Time Slots & Appointment Booking System', 'wp-time-slots-booking-form'); ?></span>
        <div style="display:flex;gap:16px;align-items:center;">
            <a href="https://wptimeslot.dwbooster.com/contact-us" target="_blank" rel="noopener noreferrer">💬 <?php esc_html_e('Request Custom Modifications', 'wp-time-slots-booking-form'); ?></a>
            <span>&bull;</span>
            <a href="<?php echo esc_url($this->plugin_URL); ?>" target="_blank" rel="noopener noreferrer">📖 <?php esc_html_e('Help & Documentation', 'wp-time-slots-booking-form'); ?></a>
        </div>
    </div>

</div>