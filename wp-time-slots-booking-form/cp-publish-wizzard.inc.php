<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
    echo 'Direct access not allowed.';
    exit;
}

global $wpdb;
$nonce  = wp_create_nonce( 'cptslotsb_actions_wizard' );
$myrows = $wpdb->get_results( "SELECT id, form_name FROM " . $wpdb->prefix . $this->table_items );
if ( empty( $this->item ) && ! empty( $myrows ) ) {
    $this->item = intval( $myrows[0]->id );
}
?>

<style type="text/css">
/* Page-specific styles: Publish Wizard Grid, Embed Methods & Success Card */
    .wpts-wizard-grid {
        display: grid;
        grid-template-columns: 1.3fr 1fr;
        gap: 24px;
        align-items: start;
    }
    @media (max-width: 920px) {
        .wpts-wizard-grid {
            grid-template-columns: 1fr;
        }
    }
    .wpts-wizard-grid .wpts-field {
        margin-bottom: 18px;
    }
    .wpts-wizard-grid .wpts-field:last-child {
        margin-bottom: 0;
    }
    .wpts-shortcode-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        background: var(--wpts-bg);
        border: 1px dashed #94a3b8;
        border-radius: var(--wpts-radius-sm);
        padding: 12px 16px;
        margin: 6px 0;
    }
    .wpts-shortcode-box code {
        font-size: 13.5px;
        font-weight: 700;
        color: var(--wpts-text);
        background: transparent;
        padding: 0;
        word-break: break-all;
    }
    .wpts-pro-callout {
        display: none;
        background: linear-gradient(135deg, #f0fdfa 0%, #f8fafc 100%);
        border: 1px solid var(--wpts-primary-border);
        border-left: 4px solid var(--wpts-primary);
        border-radius: var(--wpts-radius-sm);
        padding: 14px 16px;
        margin-bottom: 18px;
        font-size: 13px;
        line-height: 1.5;
    }
    .wpts-method-item {
        padding: 14px 16px;
        background: var(--wpts-bg);
        border: 1px solid var(--wpts-border);
        border-radius: var(--wpts-radius-sm);
        margin-bottom: 14px;
    }
    .wpts-method-item:last-child {
        margin-bottom: 0;
    }
    .wpts-method-item h4 {
        margin: 0 0 6px;
        font-size: 13.5px;
        font-weight: 700;
        color: var(--wpts-text);
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .wpts-method-item p {
        margin: 0;
        font-size: 12.5px;
        color: var(--wpts-text-muted);
        line-height: 1.5;
    }
    .wpts-success-card {
        background: var(--wpts-surface);
        border: 1px solid var(--wpts-border);
        border-top: 4px solid var(--wpts-success);
        border-radius: var(--wpts-radius);
        padding: 32px;
        max-width: 760px;
        margin: 10px auto;
        box-shadow: var(--wpts-shadow);
    }
    .wpts-success-header {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 20px;
    }
    .wpts-success-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        background: var(--wpts-success-bg);
        color: var(--wpts-success);
        border: 1px solid #a7f3d0;
        border-radius: 50%;
        font-size: 22px;
        font-weight: 700;
        flex-shrink: 0;
    }
    .wpts-success-header h2 {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        color: var(--wpts-text);
    }
    .wpts-url-box {
        background: var(--wpts-bg);
        border: 1px solid var(--wpts-border);
        border-radius: var(--wpts-radius-sm);
        padding: 14px 18px;
        margin: 18px 0 24px;
        font-size: 13.5px;
        color: var(--wpts-text-muted);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }
    .wpts-url-box a {
        font-weight: 600;
        color: var(--wpts-primary);
        text-decoration: none;
        word-break: break-all;
    }
    .wpts-url-box a:hover {
        text-decoration: underline;
    }
    .wpts-draft-notice {
        background: var(--wpts-warning-bg);
        border: 1px solid #fde68a;
        border-radius: var(--wpts-radius-sm);
        padding: 12px 16px;
        font-size: 13px;
        color: #92400e;
        line-height: 1.5;
        margin-top: 22px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }
</style>

<div class="wrap wpts-admin-wrap wpts-wrap-sm">

    <!-- Top SaaS Header -->
    <div class="wpts-header">
        <div class="wpts-header-left">
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->menu_parameter ) ); ?>" class="wpts-back-btn">
                &larr; <?php esc_html_e( 'Return to the calendars list', 'wp-time-slots-booking-form' ); ?>
            </a>
            <div class="wpts-title-group">
                <h1>
                    🚀 <?php esc_html_e( 'Publish WP Time Slots Booking Form', 'wp-time-slots-booking-form' ); ?>
                    <?php if ( ! empty( $this->item ) ) : ?>
                        <span class="wpts-badge" id="wpts_header_badge">ID #<?php echo intval( $this->item ); ?></span>
                    <?php endif; ?>
                </h1>
                <p class="wpts-subtitle"><?php echo esc_html( $this->plugin_name ); ?> &mdash; <?php esc_html_e( 'Embed your time-slot booking calendar into a new or existing page, post, or block editor', 'wp-time-slots-booking-form' ); ?></p>
            </div>
        </div>

        <div class="wpts-header-actions">
            <?php if ( ! empty( $this->item ) ) : ?>
                <a id="wpts_edit_settings_link" href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->menu_parameter . '&cal=' . intval( $this->item ) ) ); ?>" class="wpts-btn wpts-btn-secondary">
                    ⚙️ <?php esc_html_e( 'Edit Calendar Settings', 'wp-time-slots-booking-form' ); ?>
                </a>
                <a id="wpts_orders_link" href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->menu_parameter . '&cal=' . intval( $this->item ) . '&list=1' ) ); ?>" class="wpts-btn wpts-btn-secondary">
                    📋 <?php esc_html_e( 'Booking Orders', 'wp-time-slots-booking-form' ); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <form method="post" action="?page=cp_timeslotsbooking&pwizard=1" name="regForm" id="regForm">
        <input name="cp_timeslotsbooking_do_action_loaded" type="hidden" value="wizard" />
        <input name="anonce" type="hidden" value="<?php echo esc_attr( $nonce ); ?>" />

        <?php if ( $this->get_param( 'cp_timeslotsbooking_do_action_loaded' ) == 'wizard' ) : 
            $cal_id = intval( $this->get_param( 'cptimeslotsbk_id' ) );
        ?>
            <!-- Post-Publish Success Screen -->
            <div class="wpts-success-card">
                <div class="wpts-success-header">
                    <span class="wpts-success-icon">✓</span>
                    <div>
                        <h2><?php esc_html_e( 'Great! Form successfully published', 'wp-time-slots-booking-form' ); ?></h2>
                        <p class="wpts-subtitle"><?php esc_html_e( 'Your time-slot booking calendar has been embedded and is ready to accept reservations.', 'wp-time-slots-booking-form' ); ?></p>
                    </div>
                </div>

                <div class="wpts-url-box">
                    <span><?php esc_html_e( 'The booking form was placed into the page:', 'wp-time-slots-booking-form' ); ?></span>
                    <a href="<?php echo esc_url( $this->postURL ); ?>" target="_blank" rel="noopener noreferrer">
                        <?php echo esc_html( $this->postURL ); ?> &#8599;
                    </a>
                </div>

                <p style="font-size:13.5px;font-weight:600;color:var(--wpts-text);margin:0 0 12px;"><?php esc_html_e( 'Next steps:', 'wp-time-slots-booking-form' ); ?></p>

                <div style="display:flex;flex-wrap:wrap;gap:12px;">
                    <button class="wpts-btn wpts-btn-primary" type="button" onclick="window.open('<?php echo esc_js( $this->postURL ); ?>', '_blank');">
                        🌐 <?php esc_html_e( 'View the Published Booking Form', 'wp-time-slots-booking-form' ); ?>
                    </button>
                    <button class="wpts-btn wpts-btn-secondary" type="button" onclick="window.location.href='?page=cp_timeslotsbooking&cal=<?php echo intval( $cal_id ); ?>';">
                        ⚙️ <?php esc_html_e( 'Edit booking form settings and availability', 'wp-time-slots-booking-form' ); ?>
                    </button>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->menu_parameter ) ); ?>" class="wpts-btn wpts-btn-secondary">
                        🗓️ <?php esc_html_e( 'Return to Calendars List', 'wp-time-slots-booking-form' ); ?>
                    </a>
                </div>

                <div class="wpts-draft-notice">
                    <span>💡</span>
                    <div>
                        <strong><?php esc_html_e( 'Note:', 'wp-time-slots-booking-form' ); ?></strong>
                        <?php esc_html_e( 'If the calendar was published in a new draft page or post, remember to publish it when ready.', 'wp-time-slots-booking-form' ); ?>
                    </div>
                </div>
            </div>

        <?php else : ?>

            <div class="wpts-wizard-grid">
                <!-- Left Column: Automatic Page / Post Publisher -->
                <div class="wpts-card">
                    <div class="wpts-card-header">
                        <h3 class="wpts-card-title">🪄 <?php esc_html_e( 'Automatic Page / Post Publisher', 'wp-time-slots-booking-form' ); ?></h3>
                        <p class="wpts-card-desc"><?php esc_html_e( 'Create a new page/post with your booking calendar or insert it into an existing one.', 'wp-time-slots-booking-form' ); ?></p>
                    </div>
                    <div class="wpts-card-body">

                        <div class="wpts-field">
                            <label for="cptimeslotsbk_id"><?php esc_html_e( '1. Select calendar', 'wp-time-slots-booking-form' ); ?></label>
                            <select id="cptimeslotsbk_id" name="cptimeslotsbk_id" onchange="ahb_update_shortcode(this.value);">
                                <?php
                                foreach ( $myrows as $item ) {
                                    echo '<option value="' . esc_attr( $item->id ) . '"' . selected( $item->id, $this->item, false ) . '>#' . intval( $item->id ) . ' — ' . esc_html( $item->form_name ) . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="wpts-field">
                            <label for="whereto"><?php esc_html_e( '2. Where to publish it?', 'wp-time-slots-booking-form' ); ?></label>
                            <select name="whereto" id="whereto" onchange="mvpublish_displayoption(this);">
                                <option value="0"><?php esc_html_e( 'Into a new page', 'wp-time-slots-booking-form' ); ?></option>
                                <option value="1"><?php esc_html_e( 'Into a new post', 'wp-time-slots-booking-form' ); ?></option>
                                <option value="2"><?php esc_html_e( 'Into an existing page', 'wp-time-slots-booking-form' ); ?></option>
                                <option value="3"><?php esc_html_e( 'Into an existing post', 'wp-time-slots-booking-form' ); ?></option>
                                <option value="4" style="color:#64748b;"><?php esc_html_e( 'Widget in a sidebar, header or footer - upgrade required -', 'wp-time-slots-booking-form' ); ?></option>
                            </select>
                        </div>

                        <!-- Inline Pro Notice if Widget is clicked -->
                        <div id="wpts_widget_upgrade_notice" class="wpts-pro-callout">
                            <strong style="color:var(--wpts-primary-hover);display:block;margin-bottom:4px;">⚡ <?php esc_html_e( 'Sidebar, Header & Footer Widget Available in Pro', 'wp-time-slots-booking-form' ); ?></strong>
                            <span style="color:var(--wpts-text-muted);display:block;margin-bottom:10px;">
                                <?php esc_html_e( 'The dedicated sidebar, header, and footer widget is available in commercial versions. You can publish into a page or post below, or upgrade to unlock the widget.', 'wp-time-slots-booking-form' ); ?>
                            </span>
                            <a href="?page=cp_timeslotsbooking_upgrade" class="wpts-btn wpts-btn-soft" style="padding:6px 12px;font-size:12px;">
                                🚀 <?php esc_html_e( 'Explore Pro Features', 'wp-time-slots-booking-form' ); ?>
                            </a>
                        </div>

                        <div class="wpts-field" id="posttitle_row">
                            <label for="posttitle"><?php esc_html_e( '3. Page/Post Title', 'wp-time-slots-booking-form' ); ?></label>
                            <input type="text" name="posttitle" id="posttitle" value="<?php echo esc_attr__( 'Booking Form', 'wp-time-slots-booking-form' ); ?>" />
                        </div>

                        <div class="wpts-field" id="publishstatus_row">
                            <label for="publishstatus"><?php esc_html_e( '4. Initial Status', 'wp-time-slots-booking-form' ); ?></label>
                            <select name="publishstatus" id="publishstatus">
                                <option value="draft"><?php esc_html_e( 'Draft (Preview first)', 'wp-time-slots-booking-form' ); ?></option>
                                <option value="publish"><?php esc_html_e( 'Published immediately', 'wp-time-slots-booking-form' ); ?></option>
                            </select>
                        </div>

                        <div class="wpts-field" id="select_page_row" style="display:none;">
                            <label for="publishpage"><?php esc_html_e( '3. Select existing page', 'wp-time-slots-booking-form' ); ?></label>
                            <?php
                            wp_dropdown_pages( array(
                                'name'              => 'publishpage',
                                'id'                => 'publishpage',
                                'show_option_none'  => esc_html__( '— Select a page —', 'wp-time-slots-booking-form' ),
                                'option_none_value' => '0',
                            ) );
                            ?>
                        </div>

                        <div class="wpts-field" id="select_post_row" style="display:none;">
                            <label for="publishpost"><?php esc_html_e( '3. Select existing post', 'wp-time-slots-booking-form' ); ?></label>
                            <select name="publishpost" id="publishpost">
                                <?php
                                $posts_list = get_posts( array( 'numberposts' => 100, 'post_status' => array( 'publish', 'draft' ) ) );
                                foreach ( $posts_list as $p ) {
                                    echo '<option value="' . esc_attr( $p->ID ) . '">' . esc_html( $p->post_title ) . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div style="margin-top:24px;padding-top:18px;border-top:1px solid var(--wpts-border);display:flex;justify-content:flex-end;">
                            <button type="submit" id="subbtnnow" class="wpts-btn wpts-btn-primary">
                                🚀 <?php esc_html_e( 'Publish Calendar', 'wp-time-slots-booking-form' ); ?>
                            </button>
                        </div>

                    </div>
                </div>

                <!-- Right Column: Quick Shortcode & Page Builders -->
                <div class="wpts-card">
                    <div class="wpts-card-header">
                        <h3 class="wpts-card-title">📋 <?php esc_html_e( 'Quick Shortcode & Page Builders', 'wp-time-slots-booking-form' ); ?></h3>
                        <p class="wpts-card-desc"><?php esc_html_e( 'You can also paste the shortcode or use our dedicated block in Gutenberg or Elementor.', 'wp-time-slots-booking-form' ); ?></p>
                    </div>
                    <div class="wpts-card-body">

                        <div class="wpts-field">
                            <label><?php esc_html_e( 'Quick Shortcode', 'wp-time-slots-booking-form' ); ?></label>
                            <div class="wpts-shortcode-box">
                                <code id="ahb_shortcode_text">[<?php echo esc_html( $this->shorttag ); ?> id="<?php echo intval( $this->item ); ?>"]</code>
                                <button type="button" class="wpts-btn wpts-btn-soft" style="padding:6px 12px;font-size:12px;" onclick="ahb_copy_shortcode(this);">
                                    <?php esc_html_e( 'Copy', 'wp-time-slots-booking-form' ); ?>
                                </button>
                            </div>
                            <span class="wpts-help"><?php esc_html_e( 'You can also paste this shortcode directly into any page, post, or builder (Gutenberg / Elementor).', 'wp-time-slots-booking-form' ); ?></span>
                        </div>

                        <div style="margin-top:20px;">
                            <div class="wpts-method-item">
                                <h4>🧱 <?php esc_html_e( 'Gutenberg Block Editor', 'wp-time-slots-booking-form' ); ?></h4>
                                <p><?php esc_html_e( 'Click the "+" block inserter in the WordPress Block Editor, search for "WP Time Slots Booking Form", and choose your calendar from the block settings.', 'wp-time-slots-booking-form' ); ?></p>
                            </div>

                            <div class="wpts-method-item">
                                <h4>🎨 <?php esc_html_e( 'Elementor & Page Builders', 'wp-time-slots-booking-form' ); ?></h4>
                                <p><?php esc_html_e( 'Drag the native "WP Time Slots Booking Form" Elementor widget onto your canvas, or paste the shortcode above into any Shortcode module.', 'wp-time-slots-booking-form' ); ?></p>
                            </div>

                            <div class="wpts-method-item">
                                <h4>📝 <?php esc_html_e( 'Classic WordPress Editor', 'wp-time-slots-booking-form' ); ?></h4>
                                <p>
                                    <?php esc_html_e( 'Use the dedicated insertion icon', 'wp-time-slots-booking-form' ); ?>
                                    <img hspace="4" style="vertical-align:middle;" src="<?php echo esc_url( plugins_url( '/images/cp_form.gif', __FILE__ ) ); ?>" alt="<?php echo esc_attr( $this->plugin_name ); ?>" />
                                    <?php esc_html_e( 'located in your Upload/Insert toolbar right above the post editor.', 'wp-time-slots-booking-form' ); ?>
                                </p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        <?php endif; ?>
    </form>
</div>

<script type="text/javascript">
function ahb_update_shortcode(calId) {
    var parsedId = parseInt(calId, 10);
    var codeEl = document.getElementById('ahb_shortcode_text');
    if (codeEl) {
        codeEl.textContent = '[<?php echo esc_js( $this->shorttag ); ?> id="' + parsedId + '"]';
    }
    var badgeEl = document.getElementById('wpts_header_badge');
    if (badgeEl) {
        badgeEl.textContent = 'ID #' + parsedId;
    }
    var settingsLink = document.getElementById('wpts_edit_settings_link');
    if (settingsLink) {
        settingsLink.href = '<?php echo esc_js( admin_url( 'admin.php?page=' . $this->menu_parameter . '&cal=' ) ); ?>' + parsedId;
    }
    var ordersLink = document.getElementById('wpts_orders_link');
    if (ordersLink) {
        ordersLink.href = '<?php echo esc_js( admin_url( 'admin.php?page=' . $this->menu_parameter . '&list=1&cal=' ) ); ?>' + parsedId;
    }
}

function ahb_copy_shortcode(btn) {
    if (typeof window.wptsCopyElementText === 'function') {
        window.wptsCopyElementText('ahb_shortcode_text', btn, '✓ Copied!');
    }
}

function mvpublish_displayoption(sel) {
    var val = sel.value;
    var upgradeBox = document.getElementById("wpts_widget_upgrade_notice");

    if (val === '4') {
        if (upgradeBox) upgradeBox.style.display = 'block';
        sel.value = '0';
        val = '0';
    } else if (upgradeBox) {
        upgradeBox.style.display = 'none';
    }

    document.getElementById("posttitle_row").style.display = (val === '0' || val === '1') ? '' : 'none';
    document.getElementById("publishstatus_row").style.display = (val === '0' || val === '1') ? '' : 'none';
    document.getElementById("select_page_row").style.display = (val === '2') ? '' : 'none';
    document.getElementById("select_post_row").style.display = (val === '3') ? '' : 'none';
}
</script>