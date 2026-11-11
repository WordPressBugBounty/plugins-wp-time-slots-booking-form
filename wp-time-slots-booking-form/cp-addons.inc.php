<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !is_admin() ) 
{
    echo 'Direct access not allowed.';
    exit;
}

global $wpdb, $cptslotsb_addons_active_list, $cptslotsb_addons_objs_list;

$message = "";

if ( isset( $_GET['b'] ) && $_GET['b'] == 1 )
{
    $this->verify_nonce( $_GET["anonce"], 'cptslotsb_actions_list' );
    // Save the option for active addons
    delete_option( 'cptslotsb_addons_active_list' );
    if ( !empty( $_GET['cptslotsb_addons_active_list'] ) && is_array( $_GET['cptslotsb_addons_active_list'] ) ) 
    {
        $tags = array_map( 'sanitize_text_field', $_GET['cptslotsb_addons_active_list'] );
        update_option( 'cptslotsb_addons_active_list', $tags );
    }	
    
    // Get the list of active addons
    $cptslotsb_addons_active_list = get_option( 'cptslotsb_addons_active_list', array() );
    $message = __( "Add-Ons settings updated", "wp-time-slots-booking-form" );
}

$nonce = wp_create_nonce( 'cptslotsb_actions_list' );
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

    /* Top Hero & Sub-Navigation Hub */
    .wpts-hero-shell {
        background: var(--wpts-surface);
        border: 1px solid var(--wpts-border);
        border-radius: var(--wpts-radius);
        box-shadow: var(--wpts-shadow);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .wpts-hero-top {
        padding: 20px 26px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        background: linear-gradient(135deg, #f0fdfa 0%, #ffffff 65%);
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
        padding: 9px 16px;
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

    /* Section Group Banner */
    .wpts-group-banner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin: 28px 0 16px;
        padding-bottom: 10px;
        border-bottom: 2px solid var(--wpts-border);
    }
    .wpts-group-banner:first-of-type {
        margin-top: 0;
    }
    .wpts-group-banner h2 {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        color: var(--wpts-text);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Category Cards */
    .wpts-card {
        background: var(--wpts-surface);
        border: 1px solid var(--wpts-border);
        border-radius: var(--wpts-radius);
        box-shadow: var(--wpts-shadow);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .wpts-card-header {
        padding: 15px 24px;
        border-bottom: 1px solid var(--wpts-border);
        background: #fcfcfd;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .wpts-card-header.is-free {
        background: linear-gradient(90deg, #f0fdfa 0%, #fcfcfd 100%);
        border-bottom-color: var(--wpts-primary-border);
    }
    .wpts-card-title {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        color: var(--wpts-text);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .wpts-card-body {
        padding: 20px 24px;
    }

    /* Add-Ons Grid & Items */
    .wpts-addons-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 14px;
    }
    .wpts-addon-item {
        border: 1px solid var(--wpts-border);
        border-radius: var(--wpts-radius-sm);
        padding: 14px 16px;
        background: var(--wpts-bg);
        transition: all 0.15s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .wpts-addon-item:hover {
        border-color: #cbd5e1;
        background: #ffffff;
    }
    .wpts-addon-item.is-active {
        background: var(--wpts-primary-light);
        border-color: var(--wpts-primary);
        box-shadow: 0 1px 3px rgba(13, 148, 136, 0.12);
    }
    .wpts-addon-item.is-commercial {
        background: #fafaf9;
        border-style: dashed;
    }
    .wpts-addon-item.is-commercial:hover {
        border-color: var(--wpts-accent);
        background: var(--wpts-accent-light);
    }
    .wpts-addon-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 6px;
    }
    .wpts-addon-label {
        font-weight: 600;
        font-size: 13.5px;
        color: var(--wpts-text);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        line-height: 1.35;
        text-decoration: none;
    }
    .wpts-addon-item.is-commercial .wpts-addon-label {
        color: #334155;
    }
    .wpts-addon-desc {
        margin: 0 0 0 24px;
        font-size: 12.5px;
        color: var(--wpts-text-muted);
        line-height: 1.45;
    }
    .wpts-addon-badges {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        flex-shrink: 0;
    }
    .wpts-pro-pill {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        background: var(--wpts-accent-light);
        color: var(--wpts-accent);
        border: 1px solid var(--wpts-accent-border);
        padding: 2px 7px;
        border-radius: 999px;
        text-decoration: none;
    }
    .wpts-help-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #ffffff;
        border: 1px solid var(--wpts-border);
        color: var(--wpts-text-muted);
        font-size: 11px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.15s ease;
    }
    .wpts-help-pill:hover {
        background: var(--wpts-primary);
        border-color: var(--wpts-primary);
        color: #ffffff;
    }

    /* Sticky Bottom Save Bar */
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
</style>

<script type="text/javascript">
function cp_activateAddons()
{
    var cptslotsb_addons = document.getElementsByName("cptslotsb_addons"),
        cptslotsb_addons_active_list = [];
    for( var i = 0, h = cptslotsb_addons.length; i < h; i++ )
    {
        if( cptslotsb_addons[ i ].checked ) cptslotsb_addons_active_list.push( 'cptslotsb_addons_active_list[]='+encodeURIComponent( cptslotsb_addons[ i ].value ) );
    }	
    document.location = 'admin.php?page=<?php echo esc_js($this->menu_parameter); ?>_addons&anonce=<?php echo esc_js($nonce); ?>&b=1&r='+Math.random()+( ( cptslotsb_addons_active_list.length ) ? '&'+cptslotsb_addons_active_list.join( '&' ) : '' )+'#addons-section';
}

function wptsToggleAddonCard(checkbox)
{
    var card = checkbox.closest('.wpts-addon-item');
    if (card) {
        if (checkbox.checked) card.classList.add('is-active');
        else card.classList.remove('is-active');
    }
}
</script>

<a id="top"></a>

<div class="wrap wpts-admin-wrap">

    <?php if ($message) : ?>
    <div id="setting-error-settings_updated" class="wpts-toast">
        <span>✅ <strong><?php echo esc_html($message); ?></strong></span>
        <button type="button" class="wpts-btn wpts-btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="this.parentElement.style.display='none'">Dismiss</button>
    </div>
    <?php endif; ?>

    <!-- Top Hero & Navigation Hub -->
    <div class="wpts-hero-shell">
        <div class="wpts-hero-top">
            <div class="wpts-brand-group">
                <div class="wpts-brand-icon">🧩</div>
                <div class="wpts-title-group">
                    <h1>
                        <?php echo esc_html($this->plugin_name); ?> &mdash; <?php esc_html_e('Add-Ons & Integrations', 'wp-time-slots-booking-form'); ?>
                        <span class="wpts-badge"><?php echo count($cptslotsb_addons_active_list); ?> <?php esc_html_e('Active', 'wp-time-slots-booking-form'); ?></span>
                    </h1>
                    <p class="wpts-subtitle"><?php esc_html_e('Extend your booking calendars with payment gateways, iCal sync, SMS reminders, CRM integrations, and custom workflows.', 'wp-time-slots-booking-form'); ?></p>
                </div>
            </div>

            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <button type="button" class="wpts-btn wpts-btn-primary" onclick="cp_activateAddons();">
                    💾 <?php esc_html_e('Activate / Deactivate Marked Add-Ons', 'wp-time-slots-booking-form'); ?>
                </button>
                <button type="button" class="wpts-btn wpts-btn-soft" onclick="document.location='?page=cp_timeslotsbooking_upgrade';">
                    🚀 <?php esc_html_e('Get The Full List of Add-Ons', 'wp-time-slots-booking-form'); ?>
                </button>
            </div>
        </div>

        <div class="wpts-subnav">
            <div class="wpts-nav-links">
                <a href="<?php echo esc_url(admin_url('admin.php?page='.$this->menu_parameter)); ?>" class="wpts-nav-link">
                    🗓️ <?php esc_html_e('Calendars List', 'wp-time-slots-booking-form'); ?>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page='.$this->menu_parameter.'&cal=0&list=1')); ?>" class="wpts-nav-link">
                    📋 <?php esc_html_e('All Booking Orders', 'wp-time-slots-booking-form'); ?>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page='.$this->menu_parameter.'_settings')); ?>" class="wpts-nav-link">
                    ⚙️ <?php esc_html_e('General Settings', 'wp-time-slots-booking-form'); ?>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page='.$this->menu_parameter.'_addons')); ?>" class="wpts-nav-link active">
                    🧩 <?php esc_html_e('Add-Ons', 'wp-time-slots-booking-form'); ?>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page='.$this->menu_parameter.'_csseditor_page')); ?>" class="wpts-nav-link">
                    🎨 <?php esc_html_e('Styles Editor', 'wp-time-slots-booking-form'); ?>
                </a>
            </div>
        </div>
    </div>

    <a id="addons-section"></a>

    <!-- Add-Ons Directory -->
    <div class="wpts-group-banner">
        <h2>✅ <?php esc_html_e('Available & Active Add-Ons', 'wp-time-slots-booking-form'); ?></h2>
    </div>

    <?php
    $printedmsg = false;
    $newlist = array();
    if (is_array($cptslotsb_addons_objs_list)) {
        foreach( $cptslotsb_addons_objs_list as $key => $obj )
        {
            if (!isset($newlist[$obj->category]))
                $newlist[$obj->category] = array();
            $newlist[$obj->category][] = array( $key, $obj );
        }
    }

    ksort($newlist);

    foreach ($newlist as $category => $category_addons)
    {
        for ($i = 0; $i < count($category_addons) - 1; $i++) {
            for ($j = $i + 1; $j < count($category_addons); $j++) {
                if ($category_addons[$i][1]->get_addon_name() > $category_addons[$j][1]->get_addon_name())
                {
                    $tmp = $category_addons[$i];
                    $category_addons[$i] = $category_addons[$j];
                    $category_addons[$j] = $tmp;
                }
            }
        }

        $is_commercial = ($category[0] != ' ');
    ?>

        <?php if (!$printedmsg && $is_commercial) : $printedmsg = true; ?>
            <div class="wpts-group-banner">
                <h2>⚡ <?php esc_html_e('The following Add-Ons are included in the commercial versions of the plugin:', 'wp-time-slots-booking-form'); ?></h2>
                <a href="?page=cp_timeslotsbooking_upgrade" class="wpts-btn wpts-btn-soft">
                    🚀 <?php esc_html_e('Upgrade to Unlock All Add-Ons', 'wp-time-slots-booking-form'); ?>
                </a>
            </div>
        <?php endif; ?>

        <div class="wpts-card">
            <div class="wpts-card-header <?php echo !$is_commercial ? 'is-free' : ''; ?>">
                <h3 class="wpts-card-title">
                    <?php echo !$is_commercial ? '🟢' : '🔒'; ?>
                    <?php echo esc_html(trim($category)); ?>
                </h3>
                <?php if ($is_commercial) : ?>
                    <a href="https://wptimeslot.dwbooster.com/download" target="_blank" rel="noopener noreferrer" class="wpts-pro-pill">
                        <?php esc_html_e('Included in Pro — Upgrade', 'wp-time-slots-booking-form'); ?> &#8599;
                    </a>
                <?php endif; ?>
            </div>

            <div class="wpts-card-body">
                <div class="wpts-addons-grid">
                    <?php
                    for ($i = 0; $i < count($category_addons); $i++)
                    {
                        $obj = $category_addons[$i];
                        $addon_key  = $obj[0];
                        $addon_inst = $obj[1];
                        $is_active  = $addon_inst->addon_is_active();
                        $help_url   = !empty($addon_inst->help) ? $addon_inst->help : '';
                        $comm_link  = $help_url ? $help_url : 'https://wptimeslot.dwbooster.com/download';
                        $card_class = 'wpts-addon-item' . ($is_active ? ' is-active' : '') . ($is_commercial ? ' is-commercial' : '');
                    ?>
                        <div class="<?php echo esc_attr($card_class); ?>">
                            <div>
                                <div class="wpts-addon-top">
                                    <?php if ($is_commercial) : ?>
                                        <a class="wpts-addon-label" target="_blank" rel="noopener noreferrer" href="<?php echo esc_url($comm_link); ?>">
                                            <input disabled type="checkbox" id="<?php echo esc_attr($addon_key); ?>" name="cptslotsb_addons" value="<?php echo esc_attr($addon_key); ?>" <?php checked($is_active); ?> />
                                            <span><?php echo esc_html($addon_inst->get_addon_name()); ?></span>
                                        </a>
                                    <?php else : ?>
                                        <label class="wpts-addon-label" for="<?php echo esc_attr($addon_key); ?>">
                                            <input type="checkbox" id="<?php echo esc_attr($addon_key); ?>" name="cptslotsb_addons" value="<?php echo esc_attr($addon_key); ?>" <?php checked($is_active); ?> onchange="wptsToggleAddonCard(this);" />
                                            <span><?php echo esc_html($addon_inst->get_addon_name()); ?></span>
                                        </label>
                                    <?php endif; ?>

                                    <div class="wpts-addon-badges">
                                        <?php if ($is_commercial) : ?>
                                            <a class="wpts-pro-pill" target="_blank" rel="noopener noreferrer" href="<?php echo esc_url($comm_link); ?>">PRO</a>
                                        <?php endif; ?>
                                        <?php if ($help_url) : ?>
                                            <a class="wpts-help-pill" target="_blank" rel="noopener noreferrer" href="<?php echo esc_url($help_url); ?>" title="<?php esc_attr_e('View Add-On Documentation', 'wp-time-slots-booking-form'); ?>">?</a>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="wpts-addon-desc">
                                    <?php echo esc_html($addon_inst->get_addon_description()); ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

    <?php } ?>

    <!-- Sticky Bottom Action Bar -->
    <div class="wpts-sticky-footer">
        <div style="display:flex;align-items:center;gap:12px;">
            <a href="#top" class="wpts-btn wpts-btn-secondary">&uarr; <?php esc_html_e('Back to Top', 'wp-time-slots-booking-form'); ?></a>
            <a href="<?php echo esc_url(admin_url('admin.php?page='.$this->menu_parameter)); ?>" class="wpts-btn wpts-btn-secondary">
                &larr; <?php esc_html_e('Return to Calendars List', 'wp-time-slots-booking-form'); ?>
            </a>
        </div>
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
            <button type="button" class="wpts-btn wpts-btn-soft" onclick="document.location='?page=cp_timeslotsbooking_upgrade';">
                🚀 <?php esc_html_e('Get The Full List of Add-Ons', 'wp-time-slots-booking-form'); ?>
            </button>
            <button type="button" class="wpts-btn wpts-btn-primary" onclick="cp_activateAddons();">
                💾 <?php esc_html_e('Activate / Deactivate Marked Add-Ons', 'wp-time-slots-booking-form'); ?>
            </button>
        </div>
    </div>

</div>