<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !is_admin() ) 
{
    echo 'Direct access not allowed.';
    exit;
}

$nonce = wp_create_nonce( 'cptslotsb_actions_adminsettings' );

$cpid = 'CP_WPTSLOTSBK';

$gotab   = '';
$message = '';
if (isset($_POST["gotab"]))
{
    $gotab = sanitize_text_field($_POST["gotab"]);
    if ($gotab == '')
        $message = __('Email report settings updated.', 'wp-time-slots-booking-form');
    else if ($gotab == 'fixarea')
        $message = __('Troubleshoot settings updated.', 'wp-time-slots-booking-form');
    else if ($gotab == 'css')
        $message = __('Custom CSS updated.', 'wp-time-slots-booking-form');
    else if ($gotab == 'js')
        $message = __('Custom JavaScript updated.', 'wp-time-slots-booking-form');
    else if ($gotab == 'gdprarea') {
        $gdpr_action = isset($_POST["wpts_gdpr_action"]) ? sanitize_text_field($_POST["wpts_gdpr_action"]) : '';
        if ($gdpr_action === 'purge_ips_anonymize')
            $message = __('All stored IP addresses have been anonymized.', 'wp-time-slots-booking-form');
        else if ($gdpr_action === 'purge_ips_clear')
            $message = __('All stored IP addresses have been cleared.', 'wp-time-slots-booking-form');
        else if ($gdpr_action === 'run_retention_now')
            $message = __('Data retention cleanup completed.', 'wp-time-slots-booking-form');
        else if ($gdpr_action === 'purge_legacy_ical') {
            $purged_count = isset($this->last_purged_ical_count) ? intval($this->last_purged_ical_count) : 0;
            $message = sprintf(
                /* translators: %d: number of removed legacy iCal files */
                __('Legacy public iCal cleanup completed (%d file(s) removed).', 'wp-time-slots-booking-form'),
                $purged_count
            );
        }
        else
            $message = __('GDPR & Privacy settings updated.', 'wp-time-slots-booking-form');
    }
} 
else if (isset($_GET["gotab"]))
{
    $gotab = sanitize_text_field($_GET["gotab"]);
}
?>

<style type="text/css">
/* Page-specific styles: General Settings Pill Tabs & CSS Snippets Grid */
    .ahb-tab-wrapper {
        display: flex;
        align-items: center;
        gap: 6px;
        background: var(--wpts-surface);
        border: 1px solid var(--wpts-border) !important;
        padding: 8px !important;
        border-radius: var(--wpts-radius);
        margin: 0 0 24px 0 !important;
        box-shadow: var(--wpts-shadow);
        flex-wrap: wrap;
    }
    .ahb-tab-wrapper .nav-tab {
        float: none !important;
        margin: 0 !important;
        padding: 9px 16px !important;
        border: none !important;
        border-radius: var(--wpts-radius-sm) !important;
        background: transparent !important;
        color: var(--wpts-text-muted) !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        line-height: 1.3 !important;
        transition: all 0.15s ease;
        text-decoration: none;
    }
    .ahb-tab-wrapper .nav-tab:hover {
        background: var(--wpts-bg) !important;
        color: var(--wpts-text) !important;
    }
    .ahb-tab-wrapper .nav-tab.nav-tab-active {
        background: var(--wpts-primary) !important;
        color: #ffffff !important;
        box-shadow: 0 2px 4px rgba(13, 148, 136, 0.22);
    }
    .ahb-tab {
        display: none;
    }
    .ahb-tab.tab-active {
        display: block;
        animation: wptsFadeIn 0.2s ease-out;
    }
    .wpts-snippets-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 16px;
    }
    .wpts-snippet-card {
        border: 1px solid var(--wpts-border);
        border-radius: var(--wpts-radius-sm);
        background: var(--wpts-bg);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    .wpts-snippet-header {
        padding: 10px 14px;
        background: #ffffff;
        border-bottom: 1px solid var(--wpts-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        font-size: 13px;
        font-weight: 600;
        color: var(--wpts-text);
    }
    .wpts-snippet-code {
        margin: 0;
        padding: 14px 16px;
        background: #0f172a;
        color: #e2e8f0;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 12.5px;
        line-height: 1.55;
        overflow-x: auto;
        flex: 1;
        white-space: pre;
    }
</style>

<script type="text/javascript">
jQuery(function(){
    var $ = jQuery,
        flag_css_editor = true,
        flag_js_editor = true;
    <?php 
    if ($gotab == 'css' || $gotab == 'js') 
    { 
        if (function_exists('wp_enqueue_code_editor'))
        {
            $settings_js = wp_enqueue_code_editor(array('type' => 'application/javascript'));
            $settings_css = wp_enqueue_code_editor(array('type' => 'text/css'));

            if (!(false === $settings_js && false === $settings_css))
            {
                if ($gotab == 'css') print sprintf('{flag_css_editor = false; if(document.getElementById("ahb_styles_container")) wp.codeEditor.initialize( "ahb_styles_container", %s );}', wp_json_encode( $settings_css ));
                if ($gotab == 'js') print sprintf('{flag_js_editor = false; if(document.getElementById("ahb_javascript_container")) wp.codeEditor.initialize( "ahb_javascript_container", %s );}', wp_json_encode( $settings_js ));
            }
        }      
    }    
    ?>    
    
    $('.ahb-tab-wrapper .nav-tab').click(
        function(){
            $('.ahb-tab-wrapper .nav-tab.nav-tab-active').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');

            var tab = $(this).data('tab');
            $('.ahb-tab.tab-active').removeClass('tab-active');
            $('.ahb-tab[data-tab="'+tab+'"]').addClass('tab-active');

            <?php
            if (function_exists('wp_enqueue_code_editor'))
            {
                $settings_js = wp_enqueue_code_editor(array('type' => 'application/javascript'));
                $settings_css = wp_enqueue_code_editor(array('type' => 'text/css'));

                if (!(false === $settings_js && false === $settings_css))
                {
                    print sprintf('if(tab == 3 && flag_css_editor){flag_css_editor = false; if(document.getElementById("ahb_styles_container")) wp.codeEditor.initialize( "ahb_styles_container", %s );}', wp_json_encode( $settings_css ));
                    print sprintf('if(tab == 4 && flag_js_editor){flag_js_editor = false; if(document.getElementById("ahb_javascript_container")) wp.codeEditor.initialize( "ahb_javascript_container", %s );}', wp_json_encode( $settings_js ));
                }
            }
            ?>
        }
    );
});

function wptsCopyCssSnippet(btn, elementId) {
    if (typeof window.wptsCopyElementText === 'function') {
        window.wptsCopyElementText(elementId, btn, '✓ Copied!');
    }
}
</script>

<div class="wrap wpts-admin-wrap wpts-wrap-md">

    <?php if (!empty($message)) : ?>
    <div id="setting-error-settings_updated" class="wpts-toast">
        <span>✅ <strong><?php echo esc_html($message); ?></strong></span>
        <button type="button" class="wpts-mini-btn" onclick="this.parentElement.style.display='none'">Dismiss</button>
    </div>
    <?php endif; ?>

    <!-- Top Hero & Sub-Navigation Hub -->
    <div class="wpts-hero-shell">
        <div class="wpts-hero-top">
            <div class="wpts-brand-group">
                <div class="wpts-brand-icon">⚙️</div>
                <div class="wpts-title-group">
                    <h1><?php echo esc_html($this->plugin_name); ?> &mdash; <?php esc_html_e('General Settings', 'wp-time-slots-booking-form'); ?></h1>
                    <p class="wpts-subtitle"><?php esc_html_e('Configure global CSV email reports across all calendars, script loading & encoding options, and custom styling.', 'wp-time-slots-booking-form'); ?></p>
                </div>
            </div>

            <a href="<?php echo esc_url(admin_url('admin.php?page='.$this->menu_parameter)); ?>" class="wpts-btn wpts-btn-secondary">
                &larr; <?php esc_html_e('Return to Calendars List', 'wp-time-slots-booking-form'); ?>
            </a>
        </div>

        <?php $this->render_admin_subnav('settings'); ?>
    </div>

    <!-- Settings Tabs Navigation -->
    <nav class="nav-tab-wrapper ahb-tab-wrapper">
        <a href="javascript:void(0);" class="nav-tab<?php if ($gotab == '') echo ' nav-tab-active'; ?>" data-tab="1">
            📊 <?php esc_html_e('Email Report Settings', 'wp-time-slots-booking-form'); ?>
        </a>
        <a href="javascript:void(0);" class="nav-tab<?php if ($gotab == 'fixarea') echo ' nav-tab-active'; ?>" data-tab="2">
            🛠️ <?php esc_html_e('Troubleshoot Area', 'wp-time-slots-booking-form'); ?>
        </a>
        <a href="javascript:void(0);" class="nav-tab<?php if ($gotab == 'gdprarea') echo ' nav-tab-active'; ?>" data-tab="5">
            🔒 <?php esc_html_e('GDPR & Privacy', 'wp-time-slots-booking-form'); ?>
        </a>
        <a href="javascript:void(0);" class="nav-tab<?php if ($gotab == 'css') echo ' nav-tab-active'; ?>" data-tab="3">
            🎨 <?php esc_html_e('Edit Styles (CSS)', 'wp-time-slots-booking-form'); ?>
        </a>
        <a href="javascript:void(0);" class="nav-tab<?php if ($gotab == 'js') echo ' nav-tab-active'; ?>" data-tab="4">
            📜 <?php esc_html_e('Edit Scripts (JS)', 'wp-time-slots-booking-form'); ?>
        </a>
    </nav>

    <!-- ==========================================
         TAB 1: AUTOMATIC EMAIL REPORTS
         ========================================== -->
    <div class="ahb-tab<?php if ($gotab == '') echo ' tab-active'; ?>" data-tab="1">
        <div class="wpts-card">
            <div class="wpts-card-header">
                <div>
                    <h3 class="wpts-card-title">📊 <?php esc_html_e('Automatic Email Reports for ALL Forms (CSV Attachment)', 'wp-time-slots-booking-form'); ?></h3>
                    <p class="wpts-card-desc"><?php esc_html_e('Schedule periodic CSV digest reports across all booking calendars. For form-specific reports, configure the Reports tab inside each calendar.', 'wp-time-slots-booking-form'); ?></p>
                </div>
            </div>
            <div class="wpts-card-body">
                <form name="updatereportsettings" action="" method="post">
                    <input name="nonce" type="hidden" value="<?php echo esc_attr($nonce); ?>" />
                    <input name="<?php echo esc_attr($cpid); ?>_post_edition" type="hidden" value="1" />
                    <input name="gotab" type="hidden" value="" />

                    <div class="wpts-grid wpts-cols-3">
                        <div class="wpts-field">
                            <label><?php esc_html_e('Enable Reports?', 'wp-time-slots-booking-form'); ?></label>
                            <?php $option = get_option('cp_cptslotsb_rep_enable', 'no'); ?>
                            <select name="cp_cptslotsb_rep_enable">
                                <option value="no"<?php if ($option == 'no' || $option == '') echo ' selected'; ?>><?php esc_html_e('No', 'wp-time-slots-booking-form'); ?></option>
                                <option value="yes"<?php if ($option == 'yes') echo ' selected'; ?>><?php esc_html_e('Yes', 'wp-time-slots-booking-form'); ?></option>
                            </select>
                        </div>

                        <div class="wpts-field">
                            <label><?php esc_html_e('Send report every (days):', 'wp-time-slots-booking-form'); ?></label>
                            <input type="text" name="cp_cptslotsb_rep_days" size="1" value="<?php echo esc_attr(get_option('cp_cptslotsb_rep_days', '1')); ?>" />
                        </div>

                        <div class="wpts-field">
                            <label><?php esc_html_e('Send after this hour (server time):', 'wp-time-slots-booking-form'); ?></label>
                            <select name="cp_cptslotsb_rep_hour">
                                <?php
                                $hour = get_option('cp_cptslotsb_rep_hour', '0');
                                for ($k = 0; $k < 24; $k++)
                                    echo '<option value="'.esc_attr($k).'"'.($hour == $k ? ' selected' : '').'>'.($k < 10 ? '0' : '').esc_html($k).':00</option>';
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="wpts-grid wpts-cols-2">
                        <div class="wpts-field">
                            <label><?php esc_html_e('Send email from:', 'wp-time-slots-booking-form'); ?></label>
                            <input type="text" name="cp_cptslotsb_fp_from_email" size="70" value="<?php echo esc_attr(get_option('cp_cptslotsb_fp_from_email', get_the_author_meta('user_email', get_current_user_id()) )); ?>" />
                        </div>
                        <div class="wpts-field">
                            <label><?php esc_html_e('Send to email(s):', 'wp-time-slots-booking-form'); ?></label>
                            <input type="text" name="cp_cptslotsb_rep_emails" size="70" value="<?php echo esc_attr(get_option('cp_cptslotsb_rep_emails', '')); ?>" placeholder="admin@example.com, manager@example.com" />
                        </div>
                    </div>

                    <div class="wpts-grid wpts-cols-2">
                        <div class="wpts-field">
                            <label><?php esc_html_e('Email subject:', 'wp-time-slots-booking-form'); ?></label>
                            <input type="text" name="cp_cptslotsb_rep_subject" size="70" value="<?php echo esc_attr(get_option('cp_cptslotsb_rep_subject', 'Submissions report...')); ?>" />
                        </div>
                        <div class="wpts-field">
                            <label><?php esc_html_e('Email format?', 'wp-time-slots-booking-form'); ?></label>
                            <?php $option = get_option('cp_cptslotsb_rep_emailformat', 'text'); ?>
                            <select name="cp_cptslotsb_rep_emailformat">
                                <option value="text"<?php if ($option != 'html') echo ' selected'; ?>><?php esc_html_e('Plain Text (default)', 'wp-time-slots-booking-form'); ?></option>
                                <option value="html"<?php if ($option == 'html') echo ' selected'; ?>><?php esc_html_e('HTML (use html in the textarea below)', 'wp-time-slots-booking-form'); ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="wpts-field" style="margin-bottom:20px;">
                        <label><?php esc_html_e('Email Text (CSV file will be attached):', 'wp-time-slots-booking-form'); ?></label>
                        <textarea name="cp_cptslotsb_rep_message" rows="3" cols="80"><?php echo esc_textarea(get_option('cp_cptslotsb_rep_message', 'Attached you will find the data from the form submissions.')); ?></textarea>
                    </div>

                    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;padding-top:16px;border-top:1px solid var(--wpts-border);">
                        <span class="wpts-help"><?php esc_html_e('Note: For setting up a report only for a specific form, use the Reports tab when editing that form\'s settings.', 'wp-time-slots-booking-form'); ?></span>
                        <button type="submit" class="wpts-btn wpts-btn-primary">
                            💾 <?php esc_html_e('Update Report Settings', 'wp-time-slots-booking-form'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ==========================================
         TAB 2: TROUBLESHOOT AREA
         ========================================== -->
    <div class="ahb-tab<?php if ($gotab == 'fixarea') echo ' tab-active'; ?>" data-tab="2">
        <div class="wpts-card">
            <div class="wpts-card-header">
                <div>
                    <h3 class="wpts-card-title">🛠️ <?php esc_html_e('Troubleshoot Area', 'wp-time-slots-booking-form'); ?></h3>
                    <p class="wpts-card-desc"><?php esc_html_e('Important: Use this area only if you are experiencing conflicts with third-party plugins, theme scripts, or character encoding.', 'wp-time-slots-booking-form'); ?></p>
                </div>
            </div>
            <div class="wpts-card-body">
                <form method="post" action="" name="cpformconf2">
                    <input name="nonce" type="hidden" value="<?php echo esc_attr($nonce); ?>" />
                    <input name="<?php echo esc_attr($cpid); ?>_post_edition" type="hidden" value="1" />
                    <input name="gotab" type="hidden" value="fixarea" />

                    <div class="wpts-grid wpts-cols-2">
                        <div class="wpts-field">
                            <label for="ccscriptload"><?php esc_html_e('Script load method', 'wp-time-slots-booking-form'); ?></label>
                            <select id="ccscriptload" name="ccscriptload">
                                <option value="0" <?php if (get_option('cp_tslotsb_LOAD_SCRIPTS',"1") == "1") echo 'selected'; ?>><?php esc_html_e('Classic (Recommended)', 'wp-time-slots-booking-form'); ?></option>
                                <option value="1" <?php if (get_option('cp_tslotsb_LOAD_SCRIPTS',"1") != "1") echo 'selected'; ?>><?php esc_html_e('Direct', 'wp-time-slots-booking-form'); ?></option>
                            </select>
                            <span class="wpts-help"><?php esc_html_e("Change the script load method if the form doesn't appear in the public website.", 'wp-time-slots-booking-form'); ?></span>
                        </div>

                        <div class="wpts-field">
                            <label for="cccharsets"><?php esc_html_e('Character encoding', 'wp-time-slots-booking-form'); ?></label>
                            <select id="cccharsets" name="cccharsets">
                                <option value=""><?php esc_html_e('Keep current charset (Recommended)', 'wp-time-slots-booking-form'); ?></option>
                                <option value="utf8_general_ci">UTF-8 (<?php esc_html_e('try this first', 'wp-time-slots-booking-form'); ?>)</option>
                                <option value="latin1_swedish_ci">latin1_swedish_ci</option>
                            </select>
                            <span class="wpts-help"><?php esc_html_e('Update the charset if you are getting problems displaying special/non-latin characters. After updating you need to edit the special characters again.', 'wp-time-slots-booking-form'); ?></span>
                        </div>
                    </div>

                    <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--wpts-border);display:flex;justify-content:flex-end;">
                        <button type="submit" class="wpts-btn wpts-btn-primary">
                            💾 <?php esc_html_e('Update Changes', 'wp-time-slots-booking-form'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ==========================================
         TAB 3: EDIT STYLES (CSS)
         ========================================== -->
    <div class="ahb-tab<?php if ($gotab == 'css') echo ' tab-active'; ?>" data-tab="3">
        <div class="wpts-card">
            <div class="wpts-card-header">
                <div>
                    <h3 class="wpts-card-title">🎨 <?php esc_html_e('Edit Styles & Custom CSS Guide', 'wp-time-slots-booking-form'); ?></h3>
                    <p class="wpts-card-desc"><?php esc_html_e('Add custom CSS safely through the WordPress Customizer or the built-in Visual Styles Editor.', 'wp-time-slots-booking-form'); ?></p>
                </div>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <a href="<?php echo esc_url(admin_url('admin.php?page='.$this->menu_parameter.'_csseditor_page')); ?>" class="wpts-btn wpts-btn-primary">
                        🖌️ <?php esc_html_e('Open Visual Styles Editor', 'wp-time-slots-booking-form'); ?>
                    </a>
                    <a href="customize.php" class="wpts-btn wpts-btn-soft">
                        ⚙️ <?php esc_html_e('Open WP Theme Customizer', 'wp-time-slots-booking-form'); ?>
                    </a>
                </div>
            </div>
            <div class="wpts-card-body">
                <div class="wpts-alert wpts-alert-info">
                    <span>💡</span>
                    <div>
                        <strong><?php esc_html_e('Important Note:', 'wp-time-slots-booking-form'); ?></strong>
                        <?php esc_html_e("Inserting raw CSS styles directly in this legacy tab is no longer used. Please insert your custom styles in your theme's Additional CSS panel:", 'wp-time-slots-booking-form'); ?>
                        <a href="customize.php" style="font-weight:700;color:#0369a1;text-decoration:underline;"><?php esc_html_e('Appearance → Customize → Additional CSS', 'wp-time-slots-booking-form'); ?></a>,
                        <?php esc_html_e('or use the dedicated', 'wp-time-slots-booking-form'); ?>
                        <a href="<?php echo esc_url(admin_url('admin.php?page='.$this->menu_parameter.'_csseditor_page')); ?>" style="font-weight:700;color:#0369a1;text-decoration:underline;"><?php esc_html_e('Styles Editor', 'wp-time-slots-booking-form'); ?></a>.
                    </div>
                </div>

                <div style="text-align:center;margin:20px 0;">
                    <a href="customize.php" style="display:inline-block;">
                        <img src="<?php echo esc_url(plugins_url('/images/edit-styles.png', __FILE__)); ?>" alt="Additional CSS in WordPress Customizer" style="max-width:100%;width:760px;padding:12px;background:var(--wpts-bg);border:1px solid var(--wpts-border);border-radius:var(--wpts-radius-sm);box-shadow:var(--wpts-shadow);" />
                    </a>
                </div>
            </div>
        </div>

        <!-- Sample CSS Snippets Card -->
        <div class="wpts-card">
            <div class="wpts-card-header">
                <div>
                    <h3 class="wpts-card-title">✨ <?php esc_html_e('Sample Styles', 'wp-time-slots-booking-form'); ?></h3>
                    <p class="wpts-card-desc"><?php esc_html_e('Click "Copy CSS" on any snippet below to paste it into the WordPress Customizer or Styles Editor.', 'wp-time-slots-booking-form'); ?></p>
                </div>
                <a href="https://wptimeslot.dwbooster.com/faq#design" target="_blank" rel="noopener noreferrer" class="wpts-btn wpts-btn-soft">
                    📖 <?php esc_html_e('More Styles in Design FAQ', 'wp-time-slots-booking-form'); ?> &#8599;
                </a>
            </div>
            <div class="wpts-card-body">
                <div class="wpts-snippets-grid">

                    <div class="wpts-snippet-card">
                        <div class="wpts-snippet-header">
                            <span><?php esc_html_e('Make the calendar 100% width / responsive', 'wp-time-slots-booking-form'); ?></span>
                            <button type="button" class="wpts-mini-btn" onclick="wptsCopyCssSnippet(this, 'wpts_css_1')"><?php esc_html_e('Copy CSS', 'wp-time-slots-booking-form'); ?></button>
                        </div>
                        <pre class="wpts-snippet-code" id="wpts_css_1">#fbuilder .ui-datepicker-inline {
    max-width: none !important;
}</pre>
                    </div>

                    <div class="wpts-snippet-card">
                        <div class="wpts-snippet-header">
                            <span><?php esc_html_e('Make the send button change color on hover', 'wp-time-slots-booking-form'); ?></span>
                            <button type="button" class="wpts-mini-btn" onclick="wptsCopyCssSnippet(this, 'wpts_css_2')"><?php esc_html_e('Copy CSS', 'wp-time-slots-booking-form'); ?></button>
                        </div>
                        <pre class="wpts-snippet-code" id="wpts_css_2">.pbSubmit:hover {
    background-color: #4CAF50;
    color: white;
}</pre>
                    </div>

                    <div class="wpts-snippet-card">
                        <div class="wpts-snippet-header">
                            <span><?php esc_html_e('Change the color of all form field labels', 'wp-time-slots-booking-form'); ?></span>
                            <button type="button" class="wpts-mini-btn" onclick="wptsCopyCssSnippet(this, 'wpts_css_3')"><?php esc_html_e('Copy CSS', 'wp-time-slots-booking-form'); ?></button>
                        </div>
                        <pre class="wpts-snippet-code" id="wpts_css_3">#fbuilder, #fbuilder label, #fbuilder span {
    color: #0000ff;
}</pre>
                    </div>

                    <div class="wpts-snippet-card">
                        <div class="wpts-snippet-header">
                            <span><?php esc_html_e('Change color of fonts inside all fields', 'wp-time-slots-booking-form'); ?></span>
                            <button type="button" class="wpts-mini-btn" onclick="wptsCopyCssSnippet(this, 'wpts_css_4')"><?php esc_html_e('Copy CSS', 'wp-time-slots-booking-form'); ?></button>
                        </div>
                        <pre class="wpts-snippet-code" id="wpts_css_4">#fbuilder input[type=text], 
#fbuilder textarea, 
#fbuilder select { 
    color: #0000ff; 
}</pre>
                    </div>

                    <div class="wpts-snippet-card">
                        <div class="wpts-snippet-header">
                            <span><?php esc_html_e('Add text before the selected date-time', 'wp-time-slots-booking-form'); ?></span>
                            <button type="button" class="wpts-mini-btn" onclick="wptsCopyCssSnippet(this, 'wpts_css_5')"><?php esc_html_e('Copy CSS', 'wp-time-slots-booking-form'); ?></button>
                        </div>
                        <pre class="wpts-snippet-code" id="wpts_css_5">.sb_item:before {
    content: "Selected date: ";
}</pre>
                    </div>

                    <div class="wpts-snippet-card">
                        <div class="wpts-snippet-header">
                            <span><?php esc_html_e('Change the calendar header color', 'wp-time-slots-booking-form'); ?></span>
                            <button type="button" class="wpts-mini-btn" onclick="wptsCopyCssSnippet(this, 'wpts_css_6')"><?php esc_html_e('Copy CSS', 'wp-time-slots-booking-form'); ?></button>
                        </div>
                        <pre class="wpts-snippet-code" id="wpts_css_6">#fbuilder .ui-datepicker-header {
    background: #6cc72b;
    color: #444444;
    text-shadow: none;
}</pre>
                    </div>

                    <div class="wpts-snippet-card">
                        <div class="wpts-snippet-header">
                            <span><?php esc_html_e('Change styles of the selected date-times', 'wp-time-slots-booking-form'); ?></span>
                            <button type="button" class="wpts-mini-btn" onclick="wptsCopyCssSnippet(this, 'wpts_css_7')"><?php esc_html_e('Copy CSS', 'wp-time-slots-booking-form'); ?></button>
                        </div>
                        <pre class="wpts-snippet-code" id="wpts_css_7">.sb_item {
    color: red;
}</pre>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- ==========================================
         TAB 4: EDIT SCRIPTS (JS)
         ========================================== -->
    <div class="ahb-tab<?php if ($gotab == 'js') echo ' tab-active'; ?>" data-tab="4">
        <div class="wpts-card">
            <div class="wpts-card-header">
                <div>
                    <h3 class="wpts-card-title">📜 <?php esc_html_e('Edit Scripts (JavaScript)', 'wp-time-slots-booking-form'); ?></h3>
                    <p class="wpts-card-desc"><?php esc_html_e('Security & best practices for adding custom JavaScript to your booking forms.', 'wp-time-slots-booking-form'); ?></p>
                </div>
            </div>
            <div class="wpts-card-body">
                <div class="wpts-alert wpts-alert-warning" style="margin-bottom:0;">
                    <span>⚠️</span>
                    <div>
                        <strong><?php esc_html_e('Important Note:', 'wp-time-slots-booking-form'); ?></strong>
                        <?php esc_html_e("Inserting raw JavaScript directly inside the plugin settings is no longer allowed for security reasons. Please enqueue or insert your custom scripts via your child theme or a dedicated code-snippets plugin.", 'wp-time-slots-booking-form'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ==========================================
         TAB 5: GDPR & PRIVACY CONTROLS
         ========================================== -->
    <div class="ahb-tab<?php if ($gotab == 'gdprarea') echo ' tab-active'; ?>" data-tab="5">
        <?php
        $gdpr_ip_mode            = CP_TimeSlotsBooking_GDPR::get_setting('ip_mode', 'anonymize');
        $gdpr_retention_days     = intval(CP_TimeSlotsBooking_GDPR::get_setting('retention_days', 0));
        $gdpr_retention_action   = CP_TimeSlotsBooking_GDPR::get_setting('retention_action', 'anonymize');
        $gdpr_erasure_action     = CP_TimeSlotsBooking_GDPR::get_setting('erasure_action', 'anonymize');
        $gdpr_delete_uploads     = CP_TimeSlotsBooking_GDPR::get_setting('delete_uploads', '1');
        $gdpr_ai_mode            = CP_TimeSlotsBooking_GDPR::get_setting('ai_gdpr_mode', 'redact');
        $gdpr_captcha_cookieless = CP_TimeSlotsBooking_GDPR::get_setting('captcha_cookieless', '1');
        ?>
        <div class="wpts-card">
            <div class="wpts-card-header">
                <div>
                    <h3 class="wpts-card-title">🔒 <?php esc_html_e('GDPR & Privacy Controls', 'wp-time-slots-booking-form'); ?></h3>
                    <p class="wpts-card-desc"><?php esc_html_e('Manage IP anonymization, automated data retention, WordPress personal data export/erasure, cookie-less CAPTCHA, and external AI privacy.', 'wp-time-slots-booking-form'); ?></p>
                </div>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <a href="<?php echo esc_url(admin_url('export-personal-data.php')); ?>" class="wpts-btn wpts-btn-secondary">
                        📤 <?php esc_html_e('WP Data Export', 'wp-time-slots-booking-form'); ?>
                    </a>
                    <a href="<?php echo esc_url(admin_url('erase-personal-data.php')); ?>" class="wpts-btn wpts-btn-secondary">
                        🧹 <?php esc_html_e('WP Data Erasure', 'wp-time-slots-booking-form'); ?>
                    </a>
                    <a href="<?php echo esc_url(admin_url('options-privacy.php')); ?>" class="wpts-btn wpts-btn-soft">
                        📄 <?php esc_html_e('WP Privacy Guide', 'wp-time-slots-booking-form'); ?>
                    </a>
                </div>
            </div>

            <div class="wpts-card-body">
                <form method="post" action="" name="cpformconf_gdpr">
                    <input name="nonce" type="hidden" value="<?php echo esc_attr($nonce); ?>" />
                    <input name="<?php echo esc_attr($cpid); ?>_post_edition" type="hidden" value="1" />
                    <input name="gotab" type="hidden" value="gdprarea" />

                    <div class="wpts-section-divider">
                        <h4><?php esc_html_e('Data Minimization & IP Handling', 'wp-time-slots-booking-form'); ?></h4>
                    </div>
                    <div class="wpts-grid wpts-cols-2">
                        <div class="wpts-field">
                            <label for="gdpr_ip_mode"><?php esc_html_e('Visitor IP Address Storage', 'wp-time-slots-booking-form'); ?></label>
                            <select id="gdpr_ip_mode" name="gdpr_ip_mode">
                                <option value="anonymize" <?php selected($gdpr_ip_mode, 'anonymize'); ?>><?php esc_html_e('Anonymize IP (Recommended — masks last octet / IPv6 suffix)', 'wp-time-slots-booking-form'); ?></option>
                                <option value="none" <?php selected($gdpr_ip_mode, 'none'); ?>><?php esc_html_e('Do Not Store IP Addresses', 'wp-time-slots-booking-form'); ?></option>
                                <option value="full" <?php selected($gdpr_ip_mode, 'full'); ?>><?php esc_html_e('Store Full IP Address (Legacy)', 'wp-time-slots-booking-form'); ?></option>
                            </select>
                            <span class="wpts-help"><?php esc_html_e('Uses WordPress wp_privacy_anonymize_ip() before saving bookings or sending admin notifications.', 'wp-time-slots-booking-form'); ?></span>
                        </div>

                        <div class="wpts-field">
                            <label for="gdpr_ai_mode"><?php esc_html_e('AI Reply Assistant Privacy (Pollinations.ai)', 'wp-time-slots-booking-form'); ?></label>
                            <select id="gdpr_ai_mode" name="gdpr_ai_mode">
                                <option value="redact" <?php selected($gdpr_ai_mode, 'redact'); ?>><?php esc_html_e('Redact emails, phones & IPs before sending (Recommended)', 'wp-time-slots-booking-form'); ?></option>
                                <option value="block" <?php selected($gdpr_ai_mode, 'block'); ?>><?php esc_html_e('Block external AI transmission completely', 'wp-time-slots-booking-form'); ?></option>
                                <option value="allow" <?php selected($gdpr_ai_mode, 'allow'); ?>><?php esc_html_e('Send full booking message without redaction', 'wp-time-slots-booking-form'); ?></option>
                            </select>
                            <span class="wpts-help"><?php esc_html_e('Prevents customer PII from being transmitted in third-party AI API requests.', 'wp-time-slots-booking-form'); ?></span>
                        </div>
                    </div>

                    <div class="wpts-section-divider">
                        <h4><?php esc_html_e('Automated Data Retention & Right to Erasure', 'wp-time-slots-booking-form'); ?></h4>
                    </div>
                    <div class="wpts-grid wpts-cols-3">
                        <div class="wpts-field">
                            <label for="gdpr_retention_days"><?php esc_html_e('Auto-Cleanup After (Days)', 'wp-time-slots-booking-form'); ?></label>
                            <input type="number" min="0" id="gdpr_retention_days" name="gdpr_retention_days" value="<?php echo esc_attr($gdpr_retention_days); ?>" />
                            <span class="wpts-help"><?php esc_html_e('Set to 0 to disable. Bookings with upcoming future appointment dates are never purged early.', 'wp-time-slots-booking-form'); ?></span>
                        </div>

                        <div class="wpts-field">
                            <label for="gdpr_retention_action"><?php esc_html_e('Retention Cleanup Action', 'wp-time-slots-booking-form'); ?></label>
                            <select id="gdpr_retention_action" name="gdpr_retention_action">
                                <option value="anonymize" <?php selected($gdpr_retention_action, 'anonymize'); ?>><?php esc_html_e('Anonymize PII (Keep slot & stats)', 'wp-time-slots-booking-form'); ?></option>
                                <option value="delete" <?php selected($gdpr_retention_action, 'delete'); ?>><?php esc_html_e('Permanently Delete Records', 'wp-time-slots-booking-form'); ?></option>
                            </select>
                        </div>

                        <div class="wpts-field">
                            <label for="gdpr_erasure_action"><?php esc_html_e('WP Personal Data Eraser Behavior', 'wp-time-slots-booking-form'); ?></label>
                            <select id="gdpr_erasure_action" name="gdpr_erasure_action">
                                <option value="anonymize" <?php selected($gdpr_erasure_action, 'anonymize'); ?>><?php esc_html_e('Anonymize PII (Preserve slot availability)', 'wp-time-slots-booking-form'); ?></option>
                                <option value="delete" <?php selected($gdpr_erasure_action, 'delete'); ?>><?php esc_html_e('Permanently Delete Booking Rows', 'wp-time-slots-booking-form'); ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="wpts-field" style="margin-bottom:18px;">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:500;">
                            <input type="checkbox" name="gdpr_delete_uploads" value="1" <?php checked($gdpr_delete_uploads, '1'); ?> />
                            <?php esc_html_e('Also delete customer-uploaded files from wp-content/uploads when erasing or anonymizing bookings', 'wp-time-slots-booking-form'); ?>
                        </label>
                    </div>

                    <div class="wpts-section-divider">
                        <h4><?php esc_html_e('CAPTCHA Privacy & Consent Note', 'wp-time-slots-booking-form'); ?></h4>
                    </div>
                    <div class="wpts-grid wpts-cols-2">
                        <div class="wpts-field">
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                                <input type="checkbox" name="gdpr_captcha_cookieless" value="1" <?php checked($gdpr_captcha_cookieless, '1'); ?> />
                                <?php esc_html_e('Enable Cookie-less CAPTCHA Mode (Transient-based)', 'wp-time-slots-booking-form'); ?>
                            </label>
                            <span class="wpts-help"><?php esc_html_e('Validates CAPTCHA submissions using short-lived hashed WordPress transients and per-load client tokens—no PHPSESSID or rand_code cookies are set, and full-page caching stays intact.', 'wp-time-slots-booking-form'); ?></span>
                        </div>

                        <div class="wpts-field">
                            <label><?php esc_html_e('GDPR Consent Checkboxes', 'wp-time-slots-booking-form'); ?></label>
                            <span class="wpts-help"><?php esc_html_e('Add optional or required acceptance/GDPR checkboxes directly inside each booking form using the Form Builder. Their submitted values are automatically included in WordPress Personal Data Exports and erased/anonymized upon GDPR erasure requests.', 'wp-time-slots-booking-form'); ?></span>
                        </div>
                    </div>

                    <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--wpts-border);display:flex;justify-content:flex-end;">
                        <button type="submit" class="wpts-btn wpts-btn-primary">
                            💾 <?php esc_html_e('Save Privacy Settings', 'wp-time-slots-booking-form'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- One-Click Privacy Maintenance Card -->
        <div class="wpts-card">
            <div class="wpts-card-header">
                <div>
                    <h3 class="wpts-card-title">🛠️ <?php esc_html_e('Privacy Maintenance Utilities', 'wp-time-slots-booking-form'); ?></h3>
                    <p class="wpts-card-desc"><?php esc_html_e('Apply privacy rules retroactively to existing bookings in the database.', 'wp-time-slots-booking-form'); ?></p>
                </div>
            </div>
            <div class="wpts-card-body" style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;">
                <form method="post" action="" onsubmit="return confirm('<?php echo esc_js(__('Anonymize all stored IP addresses in existing bookings?', 'wp-time-slots-booking-form')); ?>');">
                    <input name="nonce" type="hidden" value="<?php echo esc_attr($nonce); ?>" />
                    <input name="<?php echo esc_attr($cpid); ?>_post_edition" type="hidden" value="1" />
                    <input name="gotab" type="hidden" value="gdprarea" />
                    <input name="wpts_gdpr_action" type="hidden" value="purge_ips_anonymize" />
                    <button type="submit" class="wpts-btn wpts-btn-secondary">🛡️ <?php esc_html_e('Anonymize All Stored IPs', 'wp-time-slots-booking-form'); ?></button>
                </form>

                <form method="post" action="" onsubmit="return confirm('<?php echo esc_js(__('Permanently clear all IP addresses from existing bookings?', 'wp-time-slots-booking-form')); ?>');">
                    <input name="nonce" type="hidden" value="<?php echo esc_attr($nonce); ?>" />
                    <input name="<?php echo esc_attr($cpid); ?>_post_edition" type="hidden" value="1" />
                    <input name="gotab" type="hidden" value="gdprarea" />
                    <input name="wpts_gdpr_action" type="hidden" value="purge_ips_clear" />
                    <button type="submit" class="wpts-btn wpts-btn-secondary">🗑️ <?php esc_html_e('Clear All Stored IPs', 'wp-time-slots-booking-form'); ?></button>
                </form>

                <form method="post" action="" onsubmit="return confirm('<?php echo esc_js(__('Run the configured retention cleanup right now?', 'wp-time-slots-booking-form')); ?>');">
                    <input name="nonce" type="hidden" value="<?php echo esc_attr($nonce); ?>" />
                    <input name="<?php echo esc_attr($cpid); ?>_post_edition" type="hidden" value="1" />
                    <input name="gotab" type="hidden" value="gdprarea" />
                    <input name="wpts_gdpr_action" type="hidden" value="run_retention_now" />
                    <button type="submit" class="wpts-btn wpts-btn-soft">⏱️ <?php esc_html_e('Run Retention Cleanup Now', 'wp-time-slots-booking-form'); ?></button>
                </form>

                <form method="post" action="" onsubmit="return confirm('<?php echo esc_js(__('Remove any legacy Appointment_*.ics files from wp-content/uploads and stale temporary iCal files?', 'wp-time-slots-booking-form')); ?>');">
                    <input name="nonce" type="hidden" value="<?php echo esc_attr($nonce); ?>" />
                    <input name="<?php echo esc_attr($cpid); ?>_post_edition" type="hidden" value="1" />
                    <input name="gotab" type="hidden" value="gdprarea" />
                    <input name="wpts_gdpr_action" type="hidden" value="purge_legacy_ical" />
                    <button type="submit" class="wpts-btn wpts-btn-secondary">📅 <?php esc_html_e('Purge Legacy Public iCal Files', 'wp-time-slots-booking-form'); ?></button>
                </form>
            </div>
        </div>
    </div>

</div>