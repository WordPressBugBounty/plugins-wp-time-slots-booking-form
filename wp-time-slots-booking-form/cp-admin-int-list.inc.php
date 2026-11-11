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
    if (!defined('CP_TSLOTSBOOK_DEFAULT_fp_from_email'))
        define('CP_TSLOTSBOOK_DEFAULT_fp_from_email', get_the_author_meta('user_email', get_current_user_id()) );
    if (!defined('CP_TSLOTSBOOK_DEFAULT_fp_destination_emails'))
        define('CP_TSLOTSBOOK_DEFAULT_fp_destination_emails', CP_TSLOTSBOOK_DEFAULT_fp_from_email);
    
    $wpdb->insert( $wpdb->prefix.$this->table_items, array( 
                                      'form_name' => stripcslashes(sanitize_text_field($_GET["name"])),

                                      'form_structure' => $this->get_option('form_structure', CP_TSLOTSBOOK_DEFAULT_form_structure),

                                      'fp_from_email' => $this->get_option('fp_from_email', CP_TSLOTSBOOK_DEFAULT_fp_from_email),
                                      'fp_destination_emails' => $this->get_option('fp_destination_emails', CP_TSLOTSBOOK_DEFAULT_fp_destination_emails),
                                      'fp_subject' => $this->get_option('fp_subject', CP_TSLOTSBOOK_DEFAULT_fp_subject),
                                      'fp_inc_additional_info' => $this->get_option('fp_inc_additional_info', CP_TSLOTSBOOK_DEFAULT_fp_inc_additional_info),
                                      'fp_return_page' => $this->get_option('fp_return_page', CP_TSLOTSBOOK_DEFAULT_fp_return_page),
                                      'fp_message' => $this->get_option('fp_message', CP_TSLOTSBOOK_DEFAULT_fp_message),
                                      'fp_emailformat' => $this->get_option('fp_emailformat', CP_TSLOTSBOOK_DEFAULT_email_format),

                                      'cu_enable_copy_to_user' => $this->get_option('cu_enable_copy_to_user', CP_TSLOTSBOOK_DEFAULT_cu_enable_copy_to_user),
                                      'cu_user_email_field' => $this->get_option('cu_user_email_field', CP_TSLOTSBOOK_DEFAULT_cu_user_email_field),
                                      'cu_subject' => $this->get_option('cu_subject', CP_TSLOTSBOOK_DEFAULT_cu_subject),
                                      'cu_message' => $this->get_option('cu_message', CP_TSLOTSBOOK_DEFAULT_cu_message),
                                      'cu_emailformat' => $this->get_option('cu_emailformat', CP_TSLOTSBOOK_DEFAULT_email_format),
                                      
                                      'vs_text_is_required' => $this->get_option('vs_text_is_required', CP_TSLOTSBOOK_DEFAULT_vs_text_is_required),
                                      'vs_text_maxapp' => $this->get_option('vs_text_maxapp', CP_TSLOTSBOOK_DEFAULT_vs_text_maxapp),
                                      'vs_text_is_email' => $this->get_option('vs_text_is_email', CP_TSLOTSBOOK_DEFAULT_vs_text_is_email),
                                      'vs_text_datemmddyyyy' => $this->get_option('vs_text_datemmddyyyy', CP_TSLOTSBOOK_DEFAULT_vs_text_datemmddyyyy),
                                      'vs_text_dateddmmyyyy' => $this->get_option('vs_text_dateddmmyyyy', CP_TSLOTSBOOK_DEFAULT_vs_text_dateddmmyyyy),
                                      'vs_text_number' => $this->get_option('vs_text_number', CP_TSLOTSBOOK_DEFAULT_vs_text_number),
                                      'vs_text_digits' => $this->get_option('vs_text_digits', CP_TSLOTSBOOK_DEFAULT_vs_text_digits),
                                      'vs_text_max' => $this->get_option('vs_text_max', CP_TSLOTSBOOK_DEFAULT_vs_text_max),
                                      'vs_text_min' => $this->get_option('vs_text_min', CP_TSLOTSBOOK_DEFAULT_vs_text_min),                                       

                                      'cv_enable_captcha' => $this->get_option('cv_enable_captcha', CP_TSLOTSBOOK_DEFAULT_cv_enable_captcha),
                                      'cv_width' => $this->get_option('cv_width', CP_TSLOTSBOOK_DEFAULT_cv_width),
                                      'cv_height' => $this->get_option('cv_height', CP_TSLOTSBOOK_DEFAULT_cv_height),
                                      'cv_chars' => $this->get_option('cv_chars', CP_TSLOTSBOOK_DEFAULT_cv_chars),
                                      'cv_font' => $this->get_option('cv_font', CP_TSLOTSBOOK_DEFAULT_cv_font),
                                      'cv_min_font_size' => $this->get_option('cv_min_font_size', CP_TSLOTSBOOK_DEFAULT_cv_min_font_size),
                                      'cv_max_font_size' => $this->get_option('cv_max_font_size', CP_TSLOTSBOOK_DEFAULT_cv_max_font_size),
                                      'cv_noise' => $this->get_option('cv_noise', CP_TSLOTSBOOK_DEFAULT_cv_noise),
                                      'cv_noise_length' => $this->get_option('cv_noise_length', CP_TSLOTSBOOK_DEFAULT_cv_noise_length),
                                      'cv_background' => $this->get_option('cv_background', CP_TSLOTSBOOK_DEFAULT_cv_background),
                                      'cv_border' => $this->get_option('cv_border', CP_TSLOTSBOOK_DEFAULT_cv_border),
                                      'cv_text_enter_valid_captcha' => $this->get_option('cv_text_enter_valid_captcha', CP_TSLOTSBOOK_DEFAULT_cv_text_enter_valid_captcha)
                                     )
                      );   
    
    echo "<script>document.location='?page=cp_timeslotsbooking&cal=".intval($wpdb->insert_id)."';</script>";
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

if (isset($_GET["confirm"]))
    $message = __('Settings updated','wp-time-slots-booking-form');

$nonce        = wp_create_nonce( 'cptslotsb_actions_list' );
$current_user = wp_get_current_user();
$myrows       = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix.$this->table_items );
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
        --wpts-shadow-md: 0 4px 6px -1px rgba(15, 23, 42, 0.07), 0 2px 4px -2px rgba(15, 23, 42, 0.06);
    }

    .wpts-admin-wrap {
        max-width: 1360px;
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

    /* Top Banner & Navigation Hub */
    .wpts-hero-shell {
        background: var(--wpts-surface);
        border: 1px solid var(--wpts-border);
        border-radius: var(--wpts-radius);
        box-shadow: var(--wpts-shadow);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .wpts-hero-top {
        padding: 22px 26px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        background: linear-gradient(135deg, #f0fdfa 0%, #ffffff 60%);
    }
    .wpts-brand-group {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .wpts-brand-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: var(--wpts-primary);
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        box-shadow: 0 4px 10px rgba(13, 148, 136, 0.25);
        flex-shrink: 0;
    }
    .wpts-title-group h1 {
        margin: 0;
        font-size: 21px;
        font-weight: 700;
        color: var(--wpts-text);
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 0;
        line-height: 1.25;
    }
    .wpts-count-pill {
        display: inline-flex;
        align-items: center;
        background: var(--wpts-primary-light);
        color: var(--wpts-primary-hover);
        border: 1px solid var(--wpts-primary-border);
        font-size: 12px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 999px;
    }
    .wpts-subtitle {
        margin: 4px 0 0;
        font-size: 13px;
        color: var(--wpts-text-muted);
    }

    /* Quick-Access Sub-Navigation Bar */
    .wpts-subnav {
        padding: 10px 26px;
        background: var(--wpts-bg);
        border-top: 1px solid var(--wpts-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .wpts-nav-links {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }
    .wpts-nav-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 13px;
        border-radius: var(--wpts-radius-sm);
        font-size: 12.5px;
        font-weight: 600;
        color: var(--wpts-text-muted);
        text-decoration: none;
        transition: all 0.15s ease;
    }
    .wpts-nav-link:hover {
        background: #ffffff;
        color: var(--wpts-primary);
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05);
    }
    .wpts-nav-link.active {
        background: #ffffff;
        color: var(--wpts-primary);
        border: 1px solid var(--wpts-border);
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05);
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
        color: var(--wpts-primary-hover);
        border-color: var(--wpts-primary-border);
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
    .wpts-btn-danger {
        background: var(--wpts-danger-bg);
        color: #dc2626;
        border-color: #fecaca;
    }
    .wpts-btn-danger:hover {
        background: var(--wpts-danger);
        color: #ffffff;
        border-color: var(--wpts-danger);
    }

    /* Cards & Add-on Postboxes */
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
        padding: 16px 24px !important;
        border-bottom: 1px solid var(--wpts-border);
        background: #fcfcfd;
        margin: 0 !important;
        font-size: 15.5px !important;
        font-weight: 700 !important;
        color: var(--wpts-text);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        cursor: default;
    }
    .wpts-card-desc {
        margin: 3px 0 0;
        font-size: 13px;
        color: var(--wpts-text-muted);
        font-weight: 400;
    }
    .wpts-card-body,
    .wpts-admin-wrap .postbox > .inside {
        padding: 22px 24px !important;
        margin: 0 !important;
    }

    /* Quick Create Bar inside Card Header */
    .wpts-create-bar {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .wpts-admin-wrap input[type="text"],
    .wpts-admin-wrap input[type="email"],
    .wpts-admin-wrap input[type="number"],
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
    .wpts-admin-wrap input[type="text"]:focus,
    .wpts-admin-wrap select:focus,
    .wpts-admin-wrap textarea:focus {
        border-color: var(--wpts-primary);
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.14);
        outline: none;
    }

    /* Calendars Table */
    .wpts-table-wrap {
        overflow-x: auto;
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
    .wpts-id-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        height: 28px;
        padding: 0 8px;
        background: var(--wpts-bg);
        border: 1px solid var(--wpts-border);
        border-radius: 6px;
        font-weight: 700;
        font-size: 12.5px;
        color: var(--wpts-text-muted);
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

    /* Action Clusters */
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

    /* Shortcode Pill */
    .wpts-shortcode-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--wpts-bg);
        border: 1px dashed #94a3b8;
        border-radius: var(--wpts-radius-sm);
        padding: 5px 10px;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 12px;
        color: var(--wpts-text);
        white-space: nowrap;
    }
    .wpts-copy-btn {
        background: #ffffff;
        border: 1px solid var(--wpts-border);
        border-radius: 6px;
        padding: 3px 8px;
        font-size: 11px;
        font-weight: 600;
        color: var(--wpts-primary);
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .wpts-copy-btn:hover {
        background: var(--wpts-primary);
        color: #ffffff;
        border-color: var(--wpts-primary);
    }

    /* Footer Bar */
    .wpts-footer-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        padding: 14px 20px;
        background: var(--wpts-surface);
        border: 1px solid var(--wpts-border);
        border-radius: var(--wpts-radius);
        font-size: 13px;
        color: var(--wpts-text-muted);
        box-shadow: var(--wpts-shadow);
    }
    .wpts-footer-bar a {
        color: var(--wpts-primary);
        font-weight: 600;
        text-decoration: none;
    }
    .wpts-footer-bar a:hover {
        text-decoration: underline;
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
    function showFeedback() {
        var orig = btn.getAttribute('data-orig') || btn.textContent;
        btn.setAttribute('data-orig', orig);
        btn.textContent = '✓ Copied';
        setTimeout(function() { btn.textContent = orig; }, 1600);
    }
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(showFeedback);
    } else {
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.left = '-9999px';
        document.body.appendChild(ta);
        ta.select();
        try { document.execCommand('copy'); showFeedback(); } catch (e) {}
        document.body.removeChild(ta);
    }
}
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
                <input type="text" name="cp_itemname" id="cp_itemname" placeholder="<?php esc_attr_e('New Calendar Name...', 'wp-time-slots-booking-form'); ?>" class="ahb-new-calendar" onkeydown="if(event.key==='Enter'){event.preventDefault();cp_addItem();}" style="min-width:220px;" />
                <button type="button" class="wpts-btn wpts-btn-primary" onclick="cp_addItem();">
                    ➕ <?php esc_html_e('Add New Calendar', 'wp-time-slots-booking-form'); ?>
                </button>
            </div>
            <?php endif; ?>
        </div>

        <div class="wpts-subnav">
            <div class="wpts-nav-links">
                <a href="<?php echo esc_url(admin_url('admin.php?page='.$this->menu_parameter)); ?>" class="wpts-nav-link active">
                    🗓️ <?php esc_html_e('Calendars List', 'wp-time-slots-booking-form'); ?>
                </a>
                <?php if ($current_user_access) : ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page='.$this->menu_parameter.'&cal=0&list=1')); ?>" class="wpts-nav-link">
                    📋 <?php esc_html_e('All Booking Orders', 'wp-time-slots-booking-form'); ?>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page='.$this->menu_parameter.'_settings')); ?>" class="wpts-nav-link">
                    ⚙️ <?php esc_html_e('General Settings', 'wp-time-slots-booking-form'); ?>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page='.$this->menu_parameter.'_addons')); ?>" class="wpts-nav-link">
                    🧩 <?php esc_html_e('Add-Ons', 'wp-time-slots-booking-form'); ?>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page='.$this->menu_parameter.'_csseditor_page')); ?>" class="wpts-nav-link" style="display: none">
                    🎨 <?php esc_html_e('Styles Editor', 'wp-time-slots-booking-form'); ?>
                </a>
                <?php endif; ?>
            </div>

            <div class="wpts-nav-links">
                <a href="https://wptimeslot.dwbooster.com/home#demos" target="_blank" rel="noopener noreferrer" class="wpts-nav-link" style="display: none">
                    🌐 <?php esc_html_e('Online Demo', 'wp-time-slots-booking-form'); ?>
                </a>
                <a href="<?php echo esc_url($this->plugin_URL); ?>" target="_blank" rel="noopener noreferrer" class="wpts-nav-link">
                    📖 <?php esc_html_e('Documentation & Help', 'wp-time-slots-booking-form'); ?>
                </a>
            </div>
        </div>
    </div>

    <!-- Calendars List Card -->
    <div class="wpts-card">
        <div class="wpts-card-header">
            <div>
                <h3 style="margin:0;font-size:16px;font-weight:700;">🗂️ <?php esc_html_e('Calendars List', 'wp-time-slots-booking-form'); ?></h3>
                <p class="wpts-card-desc"><?php esc_html_e('Configure time-slot rules, inspect your appointment schedule, manage booking orders, or embed shortcodes.', 'wp-time-slots-booking-form'); ?></p>
            </div>
        </div>

        <div class="wpts-table-wrap">
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
                                    <input type="text" name="calname_<?php echo esc_attr($item->id); ?>" id="calname_<?php echo esc_attr($item->id); ?>" value="<?php echo esc_attr($item->form_name); ?>" />
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
                                    <button class="wpts-btn wpts-btn-primary" type="button" name="calmanage_<?php echo esc_attr($item->id); ?>" onclick="cp_manageSettings(<?php echo esc_attr($item->id); ?>);">
                                        ⚙️ <?php esc_html_e('Edit', 'wp-time-slots-booking-form'); ?>
                                    </button>   
                                <?php endif; ?>

                                <?php if ($current_user_access) : ?>                               
                                    <button class="wpts-btn wpts-btn-soft" type="button" name="calpublish_<?php echo esc_attr($item->id); ?>" onclick="cp_publish(<?php echo esc_attr($item->id); ?>);">
                                        🚀 <?php esc_html_e('Publish', 'wp-time-slots-booking-form'); ?>
                                    </button>                            
                                <?php endif; ?>

                                <span class="wpts-cluster-divider"></span>

                                <button class="wpts-btn wpts-btn-accent" type="button" name="calmessages_<?php echo esc_attr($item->id); ?>" onclick="cp_viewMessages(<?php echo esc_attr($item->id); ?>);">
                                    📋 <?php esc_html_e('Booking Orders', 'wp-time-slots-booking-form'); ?>
                                </button>     
                                <button class="wpts-btn wpts-btn-accent" type="button" name="calschedule_<?php echo esc_attr($item->id); ?>" onclick="cp_viewSchedule(<?php echo esc_attr($item->id); ?>);">
                                    📅 <?php esc_html_e('Schedule', 'wp-time-slots-booking-form'); ?>
                                </button> 
                         
                                <button class="wpts-btn wpts-btn-secondary" type="button" name="caladdbk_<?php echo esc_attr($item->id); ?>" onclick="cp_addbk(<?php echo esc_attr($item->id); ?>);">
                                    ➕ <?php esc_html_e('Add Booking', 'wp-time-slots-booking-form'); ?>
                                </button>                             
                                <button class="wpts-btn wpts-btn-secondary" type="button" name="calreport_<?php echo esc_attr($item->id); ?>" onclick="cp_viewReport(<?php echo esc_attr($item->id); ?>);">
                                    📊 <?php esc_html_e('Stats', 'wp-time-slots-booking-form'); ?>
                                </button>                              

                                <?php if ($current_user_access) : ?>                             
                                    <span class="wpts-cluster-divider"></span>
                                    <button class="wpts-btn wpts-btn-secondary" type="button" name="calclone_<?php echo esc_attr($item->id); ?>" onclick="cp_cloneItem(<?php echo esc_attr($item->id); ?>);">
                                        📑 <?php esc_html_e('Clone', 'wp-time-slots-booking-form'); ?>
                                    </button>                              
                                    <button class="wpts-btn wpts-btn-danger" type="button" name="caldelete_<?php echo esc_attr($item->id); ?>" onclick="cp_deleteItem(<?php echo esc_attr($item->id); ?>, this);">
                                        🗑️ <?php esc_html_e('Delete', 'wp-time-slots-booking-form'); ?>
                                    </button>                             
                                <?php endif; ?>
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