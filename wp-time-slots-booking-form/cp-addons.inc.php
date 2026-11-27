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
/* Page-specific styles: Add-Ons Directory Grid & Commercial Cards */
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
    .wpts-card-header.is-free {
        background: linear-gradient(90deg, #f0fdfa 0%, #fcfcfd 100%);
        border-bottom-color: var(--wpts-primary-border);
    }
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

        <?php $this->render_admin_subnav('addons'); ?>
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