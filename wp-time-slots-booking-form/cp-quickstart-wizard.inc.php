<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
    echo 'Direct access not allowed.';
    exit;
}

global $wpdb;

$nonce      = wp_create_nonce( 'cptslotsb_actions_qwizard' );
$diag_nonce = wp_create_nonce( 'email_diag_nonce' );
$result     = is_array( $this->qwizard_result ) ? $this->qwizard_result : null;

$target_cal_id   = intval( $this->item );
$target_cal_name = '';
if ( $target_cal_id > 0 ) {
    $existing_row = $wpdb->get_row( $wpdb->prepare(
        "SELECT id, form_name FROM " . $wpdb->prefix . $this->table_items . " WHERE id=%d",
        $target_cal_id
    ) );
    if ( $existing_row ) {
        $target_cal_name = $existing_row->form_name;
    } else {
        $target_cal_id = 0;
    }
}

$smart          = $this->get_wp_smart_defaults();
$admin_email    = get_the_author_meta( 'user_email', get_current_user_id() );
if ( empty( $admin_email ) ) {
    $admin_email = get_option( 'admin_email', '' );
}
$privacy_url    = function_exists( 'get_privacy_policy_url' ) ? get_privacy_policy_url() : '';
$wp_pages_list  = get_pages( array( 'sort_column' => 'post_title', 'post_status' => 'publish' ) );
$templates_list = $this->available_templates();
$default_tpl    = isset( $templates_list['ahb_m4'] ) ? 'ahb_m4' : '';

$day_labels = array(
    0 => __( 'Sun', 'wp-time-slots-booking-form' ),
    1 => __( 'Mon', 'wp-time-slots-booking-form' ),
    2 => __( 'Tue', 'wp-time-slots-booking-form' ),
    3 => __( 'Wed', 'wp-time-slots-booking-form' ),
    4 => __( 'Thu', 'wp-time-slots-booking-form' ),
    5 => __( 'Fri', 'wp-time-slots-booking-form' ),
    6 => __( 'Sat', 'wp-time-slots-booking-form' ),
);
?>

<div class="wrap wpts-admin-wrap wpts-wrap-sm">

    <!-- Top Header -->
    <div class="wpts-header">
        <div class="wpts-header-left">
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->menu_parameter ) ); ?>" class="wpts-back-btn">
                &larr; <?php esc_html_e( 'Calendars List', 'wp-time-slots-booking-form' ); ?>
            </a>
            <div class="wpts-title-group">
                <h1>
                    ✨ <?php esc_html_e( 'Quick Start Wizard', 'wp-time-slots-booking-form' ); ?>
                    <?php if ( $target_cal_id > 0 && ! $result ) : ?>
                        <span class="wpts-badge"><?php printf( esc_html__( 'Configuring #%1$d — %2$s', 'wp-time-slots-booking-form' ), intval( $target_cal_id ), esc_html( $target_cal_name ) ); ?></span>
                    <?php else : ?>
                        <span class="wpts-badge"><?php esc_html_e( '2-Minute Guided Setup', 'wp-time-slots-booking-form' ); ?></span>
                    <?php endif; ?>
                </h1>
                <p class="wpts-subtitle"><?php esc_html_e( 'Pick a booking style, generate your weekly time slots, verify email delivery, and publish in four simple steps.', 'wp-time-slots-booking-form' ); ?></p>
            </div>
        </div>

        <?php if ( ! $result ) : ?>
        <div class="wpts-header-actions">
            <?php if ( $target_cal_id > 0 ) : ?>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->menu_parameter . '&cal=' . intval( $target_cal_id ) ) ); ?>" class="wpts-btn wpts-btn-secondary">
                    <?php esc_html_e( 'Skip to Full Editor', 'wp-time-slots-booking-form' ); ?> &rarr;
                </a>
            <?php else : ?>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->menu_parameter ) ); ?>" class="wpts-btn wpts-btn-secondary">
                    <?php esc_html_e( 'Cancel', 'wp-time-slots-booking-form' ); ?>
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <?php if ( $result ) : ?>

        <!-- ==========================================
             COMPLETION / SUCCESS SCREEN
             ========================================== -->
        <div class="wpts-success-card">
            <div class="wpts-success-header">
                <span class="wpts-success-icon">✓</span>
                <div>
                    <h2><?php esc_html_e( 'Your booking calendar is ready!', 'wp-time-slots-booking-form' ); ?></h2>
                    <p class="wpts-subtitle">
                        <?php printf(
                            esc_html__( '"%1$s" (#%2$d) has been configured with %3$d working day(s) and %4$d time slot(s) per day.', 'wp-time-slots-booking-form' ),
                            esc_html( $result['cal_name'] ),
                            intval( $result['cal_id'] ),
                            intval( $result['working_days_count'] ),
                            intval( $result['slots_per_day'] )
                        ); ?>
                    </p>
                </div>
            </div>

            <div class="wpts-qw-done-pills">
                <span class="wpts-badge">🗓️ <?php echo intval( $result['working_days_count'] ); ?> <?php esc_html_e( 'working days', 'wp-time-slots-booking-form' ); ?></span>
                <span class="wpts-badge">🕒 <?php echo intval( $result['slots_per_day'] ); ?> <?php esc_html_e( 'slots / day', 'wp-time-slots-booking-form' ); ?></span>
                <span class="wpts-badge">👥 <?php printf( esc_html__( 'Capacity: %d', 'wp-time-slots-booking-form' ), intval( $result['capacity'] ) ); ?></span>
                <span class="wpts-badge">💳 <?php echo esc_html( $result['price_display'] ); ?></span>
            </div>

            <?php if ( ! empty( $result['post_url'] ) ) : ?>
                <div class="wpts-url-box">
                    <span><?php esc_html_e( 'Booking form embedded on:', 'wp-time-slots-booking-form' ); ?></span>
                    <a href="<?php echo esc_url( $result['post_url'] ); ?>" target="_blank" rel="noopener noreferrer">
                        <?php echo esc_html( $result['post_url'] ); ?> &#8599;
                    </a>
                </div>
            <?php endif; ?>

            <?php if ( ! empty( $result['thankyou_created'] ) && ! empty( $result['return_url'] ) ) : ?>
                <div class="wpts-alert wpts-alert-info" style="margin:0 0 18px;">
                    <span>🎉</span>
                    <div>
                        <strong><?php esc_html_e( 'Confirmation page created:', 'wp-time-slots-booking-form' ); ?></strong>
                        <a href="<?php echo esc_url( $result['return_url'] ); ?>" target="_blank" rel="noopener noreferrer" style="color:#0369a1;font-weight:700;text-decoration:underline;"><?php echo esc_html( $result['return_url'] ); ?> &#8599;</a>
                    </div>
                </div>
            <?php endif; ?>

            <div class="wpts-field" style="margin-bottom:22px;">
                <label><?php esc_html_e( 'Shortcode (paste anywhere, or use the Gutenberg / Elementor block)', 'wp-time-slots-booking-form' ); ?></label>
                <div class="wpts-shortcode-box">
                    <code id="wpts_qw_shortcode"><?php echo esc_html( $result['shortcode'] ); ?></code>
                    <button type="button" class="wpts-btn wpts-btn-soft wpts-btn-sm" onclick="if(typeof window.wptsCopyElementText==='function'){window.wptsCopyElementText('wpts_qw_shortcode', this, '✓ Copied!');}">
                        <?php esc_html_e( 'Copy Shortcode', 'wp-time-slots-booking-form' ); ?>
                    </button>
                </div>
            </div>

            <div style="display:flex;flex-wrap:wrap;gap:10px;">
                <?php if ( ! empty( $result['post_url'] ) ) : ?>
                    <a href="<?php echo esc_url( $result['post_url'] ); ?>" target="_blank" rel="noopener noreferrer" class="wpts-btn wpts-btn-primary">
                        🌐 <?php esc_html_e( 'View Booking Page', 'wp-time-slots-booking-form' ); ?> &#8599;
                    </a>
                <?php else : ?>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->menu_parameter . '&pwizard=1&cal=' . intval( $result['cal_id'] ) ) ); ?>" class="wpts-btn wpts-btn-primary">
                        🚀 <?php esc_html_e( 'Publish to a Page', 'wp-time-slots-booking-form' ); ?>
                    </a>
                <?php endif; ?>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->menu_parameter . '&cal=' . intval( $result['cal_id'] ) ) ); ?>" class="wpts-btn wpts-btn-soft">
                    ⚙️ <?php esc_html_e( 'Fine-Tune in Full Editor', 'wp-time-slots-booking-form' ); ?>
                </a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->menu_parameter . '&cal=' . intval( $result['cal_id'] ) . '&schedule=1&calendarview=1' ) ); ?>" class="wpts-btn wpts-btn-secondary">
                    📅 <?php esc_html_e( 'Open Schedule', 'wp-time-slots-booking-form' ); ?>
                </a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->menu_parameter ) ); ?>" class="wpts-btn wpts-btn-secondary">
                    🗓️ <?php esc_html_e( 'Calendars List', 'wp-time-slots-booking-form' ); ?>
                </a>
            </div>

            <?php if ( $result['publish_mode'] === 'new_page' && $result['publish_status'] === 'draft' ) : ?>
                <div class="wpts-draft-notice">
                    <span>💡</span>
                    <div>
                        <strong><?php esc_html_e( 'Saved as Draft:', 'wp-time-slots-booking-form' ); ?></strong>
                        <?php esc_html_e( 'Preview your new booking page first, then click Publish in WordPress whenever you are ready to accept live bookings.', 'wp-time-slots-booking-form' ); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    <?php else : ?>

        <!-- ==========================================
             4-STEP PROGRESS STEPPER
             ========================================== -->
        <div class="wpts-qw-stepper" role="tablist">
            <button type="button" class="wpts-qw-step is-active" data-qw-step="1" onclick="wptsQwGoTo(1);">
                <span class="wpts-qw-step-num">1</span>
                <span class="wpts-qw-step-text">
                    <strong><?php esc_html_e( 'Booking Type', 'wp-time-slots-booking-form' ); ?></strong>
                    <small><?php esc_html_e( 'Preset & fields', 'wp-time-slots-booking-form' ); ?></small>
                </span>
            </button>
            <button type="button" class="wpts-qw-step" data-qw-step="2" onclick="wptsQwGoTo(2);">
                <span class="wpts-qw-step-num">2</span>
                <span class="wpts-qw-step-text">
                    <strong><?php esc_html_e( 'Schedule & Pricing', 'wp-time-slots-booking-form' ); ?></strong>
                    <small><?php esc_html_e( 'Hours, slots & capacity', 'wp-time-slots-booking-form' ); ?></small>
                </span>
            </button>
            <button type="button" class="wpts-qw-step" data-qw-step="3" onclick="wptsQwGoTo(3);">
                <span class="wpts-qw-step-num">3</span>
                <span class="wpts-qw-step-text">
                    <strong><?php esc_html_e( 'Emails & Redirect', 'wp-time-slots-booking-form' ); ?></strong>
                    <small><?php esc_html_e( 'Notifications & Thank You', 'wp-time-slots-booking-form' ); ?></small>
                </span>
            </button>
            <button type="button" class="wpts-qw-step" data-qw-step="4" onclick="wptsQwGoTo(4);">
                <span class="wpts-qw-step-num">4</span>
                <span class="wpts-qw-step-text">
                    <strong><?php esc_html_e( 'Review & Publish', 'wp-time-slots-booking-form' ); ?></strong>
                    <small><?php esc_html_e( 'Launch your form', 'wp-time-slots-booking-form' ); ?></small>
                </span>
            </button>
        </div>

        <form method="post" action="?page=<?php echo esc_attr( $this->menu_parameter ); ?>&qwizard=1<?php echo $target_cal_id > 0 ? '&cal=' . intval( $target_cal_id ) : ''; ?>" id="wptsQuickStartForm">
            <input type="hidden" name="cp_timeslotsbooking_do_action_loaded" value="quickstart_wizard" />
            <input type="hidden" name="anonce" value="<?php echo esc_attr( $nonce ); ?>" />
            <input type="hidden" name="qw_target_cal" value="<?php echo intval( $target_cal_id ); ?>" />
            <input type="hidden" name="qw_booking_type" id="qw_booking_type" value="appointment_free" />
            <input type="hidden" name="qw_date_format" value="<?php echo esc_attr( $smart['date_format'] ); ?>" />
            <input type="hidden" name="qw_first_day" value="<?php echo intval( $smart['first_day'] ); ?>" />

            <!-- ==========================================
                 STEP 1: BOOKING TYPE & FORM BASICS
                 ========================================== -->
            <div class="wpts-qw-panel is-active" data-qw-panel="1">
                <div class="wpts-card">
                    <div class="wpts-card-header">
                        <div>
                            <h3 class="wpts-card-title">🎯 <?php esc_html_e( 'Step 1: What are customers booking?', 'wp-time-slots-booking-form' ); ?></h3>
                            <p class="wpts-card-desc"><?php esc_html_e( 'Choose a starting preset to pre-configure capacity, pricing, and fields. You can customize every detail in the next step.', 'wp-time-slots-booking-form' ); ?></p>
                        </div>
                    </div>
                    <div class="wpts-card-body">

                        <div class="wpts-qw-preset-grid">
                            <button type="button" class="wpts-qw-preset-card is-selected" data-preset="appointment_free" onclick="wptsQwSelectPreset('appointment_free');">
                                <span class="wpts-qw-preset-icon">🩺</span>
                                <strong><?php esc_html_e( '1-on-1 Appointment (Free)', 'wp-time-slots-booking-form' ); ?></strong>
                                <span><?php esc_html_e( 'Consultations, calls, visits or interviews. 1 person per time slot, no price shown.', 'wp-time-slots-booking-form' ); ?></span>
                            </button>

                            <button type="button" class="wpts-qw-preset-card" data-preset="paid_service" onclick="wptsQwSelectPreset('paid_service');">
                                <span class="wpts-qw-preset-icon">💇</span>
                                <strong><?php esc_html_e( 'Paid Service / Session', 'wp-time-slots-booking-form' ); ?></strong>
                                <span><?php esc_html_e( 'Salons, coaching, tutoring or rentals. 1-on-1 slots with visible total cost.', 'wp-time-slots-booking-form' ); ?></span>
                            </button>

                            <button type="button" class="wpts-qw-preset-card" data-preset="group_class" onclick="wptsQwSelectPreset('group_class');">
                                <span class="wpts-qw-preset-icon">🧘</span>
                                <strong><?php esc_html_e( 'Group Class / Workshop', 'wp-time-slots-booking-form' ); ?></strong>
                                <span><?php esc_html_e( 'Fitness classes, tours or labs. Multiple seats per slot with quantity selector.', 'wp-time-slots-booking-form' ); ?></span>
                            </button>

                            <button type="button" class="wpts-qw-preset-card" data-preset="event_ticket" onclick="wptsQwSelectPreset('event_ticket');">
                                <span class="wpts-qw-preset-icon">🎟️</span>
                                <strong><?php esc_html_e( 'Event / Timed Entry Ticket', 'wp-time-slots-booking-form' ); ?></strong>
                                <span><?php esc_html_e( 'Museums, tastings or venue entry. High slot capacity and multi-ticket booking.', 'wp-time-slots-booking-form' ); ?></span>
                            </button>
                        </div>

                        <div class="wpts-section-divider">
                            <h4><?php esc_html_e( 'Calendar Name & Design', 'wp-time-slots-booking-form' ); ?></h4>
                        </div>

                        <div class="wpts-grid wpts-cols-2">
                            <div class="wpts-field">
                                <label for="qw_cal_name"><?php esc_html_e( 'Calendar / Form Name', 'wp-time-slots-booking-form' ); ?></label>
                                <input type="text" id="qw_cal_name" name="qw_cal_name" value="<?php echo esc_attr( $target_cal_id > 0 && $target_cal_name !== 'Form 1' ? $target_cal_name : __( 'Appointment Booking', 'wp-time-slots-booking-form' ) ); ?>" placeholder="<?php esc_attr_e( 'e.g. Haircut Appointments', 'wp-time-slots-booking-form' ); ?>" required />
                                <span class="wpts-help"><?php esc_html_e( 'Used in your admin dashboard and as the default page title if you publish a new page.', 'wp-time-slots-booking-form' ); ?></span>
                            </div>

                            <div class="wpts-field">
                                <label for="qw_form_template"><?php esc_html_e( 'Visual Design Template', 'wp-time-slots-booking-form' ); ?></label>
                                <select id="qw_form_template" name="qw_form_template">
                                    <option value=""><?php esc_html_e( 'Default template', 'wp-time-slots-booking-form' ); ?></option>
                                    <?php foreach ( $templates_list as $tpl_key => $tpl_info ) : ?>
                                        <option value="<?php echo esc_attr( $tpl_key ); ?>" <?php selected( $tpl_key, $default_tpl ); ?>>
                                            <?php echo esc_html( ! empty( $tpl_info['title'] ) ? $tpl_info['title'] : $tpl_key ); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="wpts-help"><?php esc_html_e( 'You can switch templates or tweak colors anytime in the Styles Editor.', 'wp-time-slots-booking-form' ); ?></span>
                            </div>
                        </div>

                        <div class="wpts-section-divider">
                            <h4><?php esc_html_e( 'Customer Details to Collect', 'wp-time-slots-booking-form' ); ?></h4>
                        </div>

                        <div class="wpts-qw-checks-row">
                            <label class="wpts-qw-check-pill is-locked">
                                <input type="checkbox" checked disabled />
                                <span>✉️ <?php esc_html_e( 'Email Address (Required)', 'wp-time-slots-booking-form' ); ?></span>
                            </label>
                            <label class="wpts-qw-check-pill">
                                <input type="checkbox" name="qw_include_name" id="qw_include_name" value="1" checked />
                                <span>👤 <?php esc_html_e( 'Full Name', 'wp-time-slots-booking-form' ); ?></span>
                            </label>
                            <label class="wpts-qw-check-pill">
                                <input type="checkbox" name="qw_include_phone" id="qw_include_phone" value="1" />
                                <span>📞 <?php esc_html_e( 'Phone Number', 'wp-time-slots-booking-form' ); ?></span>
                            </label>
                            <label class="wpts-qw-check-pill">
                                <input type="checkbox" name="qw_include_gdpr" id="qw_include_gdpr" value="1" onchange="wptsQwToggleGdprUrl();" />
                                <span>🔒 <?php esc_html_e( 'GDPR / Terms Checkbox', 'wp-time-slots-booking-form' ); ?></span>
                            </label>
                        </div>

                        <div id="qw_gdpr_url_wrap" class="wpts-field" style="display:none;margin-top:14px;">
                            <label for="qw_gdpr_url"><?php esc_html_e( 'Privacy Policy / Terms URL (optional)', 'wp-time-slots-booking-form' ); ?></label>
                            <input type="text" id="qw_gdpr_url" name="qw_gdpr_url" value="<?php echo esc_attr( $privacy_url ); ?>" placeholder="https://example.com/privacy-policy" />
                        </div>

                        <div class="wpts-qw-nav">
                            <span></span>
                            <button type="button" class="wpts-btn wpts-btn-primary" onclick="wptsQwGoTo(2);">
                                <?php esc_html_e( 'Next: Schedule & Pricing', 'wp-time-slots-booking-form' ); ?> &rarr;
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 STEP 2: WEEKLY SCHEDULE, CAPACITY & PRICING
                 ========================================== -->
            <div class="wpts-qw-panel" data-qw-panel="2">
                <div class="wpts-card">
                    <div class="wpts-card-header">
                        <div>
                            <h3 class="wpts-card-title">🗓️ <?php esc_html_e( 'Step 2: Generate Your Weekly Schedule', 'wp-time-slots-booking-form' ); ?></h3>
                            <p class="wpts-card-desc"><?php esc_html_e( 'Pick your open days and hours. We will generate all matching time slots automatically—you can still adjust individual days or special dates later.', 'wp-time-slots-booking-form' ); ?></p>
                        </div>
                    </div>
                    <div class="wpts-card-body">

                        <div class="wpts-field" style="margin-bottom:20px;">
                            <label><?php esc_html_e( 'Working / Available Days', 'wp-time-slots-booking-form' ); ?></label>
                            <div class="wpts-qw-day-pills">
                                <?php
                                $ordered_days = array();
                                for ( $offset = 0; $offset < 7; $offset++ ) {
                                    $ordered_days[] = ( intval( $smart['first_day'] ) + $offset ) % 7;
                                }
                                foreach ( $ordered_days as $day_idx ) :
                                    $is_weekday = ( $day_idx >= 1 && $day_idx <= 5 );
                                ?>
                                    <label class="wpts-qw-day-pill">
                                        <input type="checkbox" name="qw_days[]" value="<?php echo intval( $day_idx ); ?>" <?php checked( $is_weekday ); ?> onchange="wptsQwRenderPreview();" />
                                        <span><?php echo esc_html( $day_labels[ $day_idx ] ); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="wpts-grid wpts-cols-4">
                            <div class="wpts-field">
                                <label for="qw_start_time"><?php esc_html_e( 'First Slot', 'wp-time-slots-booking-form' ); ?></label>
                                <input type="time" id="qw_start_time" name="qw_start_time" value="09:00" step="300" onchange="wptsQwRenderPreview();" />
                            </div>
                            <div class="wpts-field">
                                <label for="qw_end_time"><?php esc_html_e( 'Last Slot', 'wp-time-slots-booking-form' ); ?></label>
                                <input type="time" id="qw_end_time" name="qw_end_time" value="17:00" step="300" onchange="wptsQwRenderPreview();" />
                            </div>
                            <div class="wpts-field">
                                <label for="qw_slot_duration"><?php esc_html_e( 'Slot Interval / Duration', 'wp-time-slots-booking-form' ); ?></label>
                                <select id="qw_slot_duration" name="qw_slot_duration" onchange="wptsQwRenderPreview();">
                                    <option value="15"><?php esc_html_e( 'Every 15 minutes', 'wp-time-slots-booking-form' ); ?></option>
                                    <option value="20"><?php esc_html_e( 'Every 20 minutes', 'wp-time-slots-booking-form' ); ?></option>
                                    <option value="30"><?php esc_html_e( 'Every 30 minutes', 'wp-time-slots-booking-form' ); ?></option>
                                    <option value="45"><?php esc_html_e( 'Every 45 minutes', 'wp-time-slots-booking-form' ); ?></option>
                                    <option value="60" selected><?php esc_html_e( 'Every 60 minutes (1 hour)', 'wp-time-slots-booking-form' ); ?></option>
                                    <option value="90"><?php esc_html_e( 'Every 90 minutes', 'wp-time-slots-booking-form' ); ?></option>
                                    <option value="120"><?php esc_html_e( 'Every 2 hours', 'wp-time-slots-booking-form' ); ?></option>
                                </select>
                            </div>
                            <div class="wpts-field">
                                <label for="qw_military_time"><?php esc_html_e( 'Time Display Format', 'wp-time-slots-booking-form' ); ?></label>
                                <select id="qw_military_time" name="qw_military_time" onchange="wptsQwRenderPreview();">
                                    <option value="1" <?php selected( intval( $smart['military_time'] ), 1 ); ?>><?php esc_html_e( '24-hour (14:00)', 'wp-time-slots-booking-form' ); ?></option>
                                    <option value="0" <?php selected( intval( $smart['military_time'] ), 0 ); ?>><?php esc_html_e( '12-hour (2:00 PM)', 'wp-time-slots-booking-form' ); ?></option>
                                </select>
                            </div>
                        </div>

                        <!-- Optional Midday Break -->
                        <div class="wpts-subbox" style="margin-top:16px;padding:14px 16px;">
                            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                                <label style="display:inline-flex;align-items:center;gap:8px;font-weight:600;font-size:13px;cursor:pointer;">
                                    <input type="checkbox" id="qw_break_enabled" name="qw_break_enabled" value="1" onchange="wptsQwToggleBreak();" />
                                    ☕ <?php esc_html_e( 'Exclude a midday / lunch break', 'wp-time-slots-booking-form' ); ?>
                                </label>
                                <div id="qw_break_inputs" style="display:none;align-items:center;gap:8px;flex-wrap:wrap;">
                                    <span class="wpts-help" style="margin:0;"><?php esc_html_e( 'From', 'wp-time-slots-booking-form' ); ?></span>
                                    <input type="time" id="qw_break_start" name="qw_break_start" value="13:00" step="300" onchange="wptsQwRenderPreview();" style="width:125px;min-height:34px;padding:4px 8px;border:1px solid #cbd5e1;border-radius:6px;" />
                                    <span class="wpts-help" style="margin:0;"><?php esc_html_e( 'to', 'wp-time-slots-booking-form' ); ?></span>
                                    <input type="time" id="qw_break_end" name="qw_break_end" value="14:00" step="300" onchange="wptsQwRenderPreview();" style="width:125px;min-height:34px;padding:4px 8px;border:1px solid #cbd5e1;border-radius:6px;" />
                                </div>
                            </div>
                        </div>

                        <!-- Live Slots Preview -->
                        <div class="wpts-qw-preview-box">
                            <div class="wpts-qw-preview-hdr">
                                <strong>👁️ <?php esc_html_e( 'Live Daily Time Slots Preview', 'wp-time-slots-booking-form' ); ?></strong>
                                <span id="qw_preview_badge" class="wpts-badge">9 slots / day</span>
                            </div>
                            <div id="qw_preview_chips" class="wpts-qw-preview-chips"></div>
                        </div>

                        <div class="wpts-section-divider">
                            <h4><?php esc_html_e( 'Capacity & Pricing', 'wp-time-slots-booking-form' ); ?></h4>
                        </div>

                        <div class="wpts-grid wpts-cols-3">
                            <div class="wpts-field">
                                <label for="qw_capacity"><?php esc_html_e( 'Capacity per time slot', 'wp-time-slots-booking-form' ); ?></label>
                                <input type="number" id="qw_capacity" name="qw_capacity" min="1" max="500" value="1" oninput="wptsQwSyncCapacity();" />
                                <span class="wpts-help"><?php esc_html_e( '1 for private appointments, or >1 for classes / tables / tickets.', 'wp-time-slots-booking-form' ); ?></span>
                            </div>
                            <div class="wpts-field">
                                <label for="qw_max_slots"><?php esc_html_e( 'Max slots per booking', 'wp-time-slots-booking-form' ); ?></label>
                                <input type="number" id="qw_max_slots" name="qw_max_slots" min="1" max="99" value="1" />
                                <span class="wpts-help"><?php esc_html_e( 'How many slots one customer can pick in a single submission.', 'wp-time-slots-booking-form' ); ?></span>
                            </div>
                            <div class="wpts-field" id="qw_qty_toggle_field" style="display:none;">
                                <label><?php esc_html_e( 'Multi-seat / Ticket Selector', 'wp-time-slots-booking-form' ); ?></label>
                                <label class="wpts-qw-inline-toggle">
                                    <input type="checkbox" id="qw_enable_qty" name="qw_enable_qty" value="1" onchange="wptsQwToggleQty();" />
                                    <span><?php esc_html_e( 'Let customer pick quantity', 'wp-time-slots-booking-form' ); ?></span>
                                </label>
                                <div id="qw_qty_details" style="display:none;margin-top:8px;gap:8px;grid-template-columns:1fr 90px;" class="wpts-grid">
                                    <input type="text" id="qw_qty_label" name="qw_qty_label" value="<?php esc_attr_e( 'Participants', 'wp-time-slots-booking-form' ); ?>" placeholder="<?php esc_attr_e( 'Label (e.g. Guests)', 'wp-time-slots-booking-form' ); ?>" />
                                    <input type="number" id="qw_qty_max" name="qw_qty_max" min="2" max="500" value="10" title="<?php esc_attr_e( 'Max selectable quantity', 'wp-time-slots-booking-form' ); ?>" />
                                </div>
                            </div>
                        </div>

                        <div class="wpts-subbox" style="margin-top:16px;">
                            <label style="display:inline-flex;align-items:center;gap:8px;font-weight:700;font-size:13.5px;cursor:pointer;">
                                <input type="checkbox" id="qw_enable_pricing" name="qw_enable_pricing" value="1" onchange="wptsQwTogglePricing();" />
                                💳 <?php esc_html_e( 'Display a price per time slot', 'wp-time-slots-booking-form' ); ?>
                            </label>
                            <div id="qw_pricing_fields" class="wpts-grid wpts-cols-2" style="display:none;margin-top:14px;">
                                <div class="wpts-field">
                                    <label for="qw_price"><?php esc_html_e( 'Price per slot (number only)', 'wp-time-slots-booking-form' ); ?></label>
                                    <input type="number" id="qw_price" name="qw_price" min="0" step="0.01" value="50" />
                                </div>
                                <div class="wpts-field">
                                    <label for="qw_currency_format"><?php esc_html_e( 'Currency Display Format', 'wp-time-slots-booking-form' ); ?></label>
                                    <select id="qw_currency_format" name="qw_currency_format">
                                        <option value="$ {0}">$ 50 (USD / CAD / AUD)</option>
                                        <option value="€ {0}">€ 50 (EUR prefix)</option>
                                        <option value="{0} €">50 € (EUR suffix)</option>
                                        <option value="£ {0}">£ 50 (GBP)</option>
                                        <option value="CHF {0}">CHF 50</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="wpts-qw-nav">
                            <button type="button" class="wpts-btn wpts-btn-secondary" onclick="wptsQwGoTo(1);">
                                &larr; <?php esc_html_e( 'Back: Booking Type', 'wp-time-slots-booking-form' ); ?>
                            </button>
                            <button type="button" class="wpts-btn wpts-btn-primary" onclick="wptsQwGoTo(3);">
                                <?php esc_html_e( 'Next: Emails & Redirect', 'wp-time-slots-booking-form' ); ?> &rarr;
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 STEP 3: EMAILS & CONFIRMATION PAGE
                 ========================================== -->
            <div class="wpts-qw-panel" data-qw-panel="3">
                <div class="wpts-card">
                    <div class="wpts-card-header">
                        <div>
                            <h3 class="wpts-card-title">✉️ <?php esc_html_e( 'Step 3: Notification Emails & Confirmation Page', 'wp-time-slots-booking-form' ); ?></h3>
                            <p class="wpts-card-desc"><?php esc_html_e( 'Choose where new booking alerts go, test email deliverability right now, and pick the post-booking Thank You page.', 'wp-time-slots-booking-form' ); ?></p>
                        </div>
                    </div>
                    <div class="wpts-card-body">

                        <div class="wpts-grid wpts-cols-2">
                            <div class="wpts-field">
                                <label for="qw_dest_email"><?php esc_html_e( 'Send new booking notifications to', 'wp-time-slots-booking-form' ); ?></label>
                                <input type="email" id="qw_dest_email" name="qw_dest_email" value="<?php echo esc_attr( $admin_email ); ?>" required />
                                <span class="wpts-help"><?php esc_html_e( 'Your business or admin inbox that receives every new appointment.', 'wp-time-slots-booking-form' ); ?></span>
                            </div>

                            <div class="wpts-field">
                                <label for="qw_from_email"><?php esc_html_e( '"From" email address', 'wp-time-slots-booking-form' ); ?></label>
                                <input type="email" id="qw_from_email" name="qw_from_email" value="<?php echo esc_attr( $admin_email ); ?>" required />
                                <span class="wpts-help"><?php esc_html_e( 'Use an address on your website domain for best deliverability.', 'wp-time-slots-booking-form' ); ?></span>
                            </div>
                        </div>

                        <div class="wpts-field" style="margin-bottom:18px;">
                            <label for="qw_send_user_copy"><?php esc_html_e( 'Send automatic confirmation email to the customer?', 'wp-time-slots-booking-form' ); ?></label>
                            <select id="qw_send_user_copy" name="qw_send_user_copy" style="max-width:340px;">
                                <option value="true" selected><?php esc_html_e( 'Yes — send confirmation copy to customer (Recommended)', 'wp-time-slots-booking-form' ); ?></option>
                                <option value="false"><?php esc_html_e( 'No — notify administrator only', 'wp-time-slots-booking-form' ); ?></option>
                            </select>
                        </div>

                        <!-- Inline Email Deliverability Test (reuses existing AJAX diagnostic) -->
                        <div class="wpts-subbox" style="margin-bottom:22px;">
                            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                                <div>
                                    <strong style="font-size:13.5px;display:block;">🩺 <?php esc_html_e( 'Quick Email Delivery Check (Optional)', 'wp-time-slots-booking-form' ); ?></strong>
                                    <span class="wpts-help"><?php esc_html_e( 'Verify SMTP, SPF/DMARC alignment, and send a quick test email before going live.', 'wp-time-slots-booking-form' ); ?></span>
                                </div>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <span id="qw_diag_loader" class="spinner" style="float:none;margin:0;"></span>
                                    <button type="button" id="qw_diag_btn" class="wpts-btn wpts-btn-soft wpts-btn-sm" onclick="wptsQwRunEmailTest();">
                                        🔍 <?php esc_html_e( 'Send Test Email', 'wp-time-slots-booking-form' ); ?>
                                    </button>
                                </div>
                            </div>
                            <div id="qw_diag_results" style="margin-top:14px;display:none;"></div>
                        </div>

                        <div class="wpts-section-divider">
                            <h4><?php esc_html_e( 'After Booking Confirmation Page', 'wp-time-slots-booking-form' ); ?></h4>
                        </div>

                        <div class="wpts-grid wpts-cols-2">
                            <div class="wpts-field">
                                <label for="qw_return_mode"><?php esc_html_e( 'Redirect customer after booking to', 'wp-time-slots-booking-form' ); ?></label>
                                <select id="qw_return_mode" name="qw_return_mode" onchange="wptsQwToggleReturnUrl();">
                                    <option value="auto_thankyou" selected>✨ <?php esc_html_e( 'Auto-create a "Booking Confirmed" page (Recommended)', 'wp-time-slots-booking-form' ); ?></option>
                                    <option value="home">🏠 <?php esc_html_e( 'Site Home Page', 'wp-time-slots-booking-form' ); ?></option>
                                    <?php if ( ! empty( $wp_pages_list ) ) : ?>
                                        <optgroup label="<?php esc_attr_e( 'Existing WordPress Pages', 'wp-time-slots-booking-form' ); ?>">
                                            <?php foreach ( $wp_pages_list as $wp_pg ) : ?>
                                                <option value="page_<?php echo intval( $wp_pg->ID ); ?>"><?php echo esc_html( $wp_pg->post_title ); ?></option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php endif; ?>
                                    <option value="custom">🔗 <?php esc_html_e( 'Custom URL...', 'wp-time-slots-booking-form' ); ?></option>
                                </select>
                            </div>

                            <div class="wpts-field" id="qw_custom_return_wrap" style="display:none;">
                                <label for="qw_return_url"><?php esc_html_e( 'Custom Confirmation URL', 'wp-time-slots-booking-form' ); ?></label>
                                <input type="text" id="qw_return_url" name="qw_return_url" value="<?php echo esc_attr( get_site_url() ); ?>" placeholder="https://example.com/thank-you" />
                            </div>
                        </div>

                        <div class="wpts-qw-nav">
                            <button type="button" class="wpts-btn wpts-btn-secondary" onclick="wptsQwGoTo(2);">
                                &larr; <?php esc_html_e( 'Back: Schedule & Pricing', 'wp-time-slots-booking-form' ); ?>
                            </button>
                            <button type="button" class="wpts-btn wpts-btn-primary" onclick="wptsQwGoTo(4);">
                                <?php esc_html_e( 'Next: Review & Publish', 'wp-time-slots-booking-form' ); ?> &rarr;
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 STEP 4: REVIEW & PUBLISH
                 ========================================== -->
            <div class="wpts-qw-panel" data-qw-panel="4">
                <div class="wpts-card">
                    <div class="wpts-card-header">
                        <div>
                            <h3 class="wpts-card-title">🚀 <?php esc_html_e( 'Step 4: Review & Publish Your Booking Form', 'wp-time-slots-booking-form' ); ?></h3>
                            <p class="wpts-card-desc"><?php esc_html_e( 'Verify your configuration below and choose whether to publish it onto a page right away.', 'wp-time-slots-booking-form' ); ?></p>
                        </div>
                    </div>
                    <div class="wpts-card-body">

                        <div class="wpts-qw-summary-grid" id="qw_review_summary">
                            <div class="wpts-qw-summary-item">
                                <span><?php esc_html_e( 'Calendar Name', 'wp-time-slots-booking-form' ); ?></span>
                                <strong id="qw_sum_name">—</strong>
                            </div>
                            <div class="wpts-qw-summary-item">
                                <span><?php esc_html_e( 'Schedule', 'wp-time-slots-booking-form' ); ?></span>
                                <strong id="qw_sum_schedule">—</strong>
                            </div>
                            <div class="wpts-qw-summary-item">
                                <span><?php esc_html_e( 'Capacity & Pricing', 'wp-time-slots-booking-form' ); ?></span>
                                <strong id="qw_sum_cap_price">—</strong>
                            </div>
                            <div class="wpts-qw-summary-item">
                                <span><?php esc_html_e( 'Notifications', 'wp-time-slots-booking-form' ); ?></span>
                                <strong id="qw_sum_email">—</strong>
                            </div>
                        </div>

                        <div class="wpts-section-divider">
                            <h4><?php esc_html_e( 'Where should we place your booking form?', 'wp-time-slots-booking-form' ); ?></h4>
                        </div>

                        <div class="wpts-qw-publish-options">
                            <label class="wpts-qw-radio-card is-selected" data-pub="new_page">
                                <input type="radio" name="qw_publish_mode" value="new_page" checked onchange="wptsQwSyncPublishMode();" />
                                <div>
                                    <strong>📄 <?php esc_html_e( 'Create a new WordPress page', 'wp-time-slots-booking-form' ); ?></strong>
                                    <span class="wpts-help"><?php esc_html_e( 'Automatically creates a clean page with your booking form embedded.', 'wp-time-slots-booking-form' ); ?></span>
                                </div>
                            </label>

                            <label class="wpts-qw-radio-card" data-pub="existing_page">
                                <input type="radio" name="qw_publish_mode" value="existing_page" onchange="wptsQwSyncPublishMode();" />
                                <div>
                                    <strong>📌 <?php esc_html_e( 'Add to an existing page', 'wp-time-slots-booking-form' ); ?></strong>
                                    <span class="wpts-help"><?php esc_html_e( 'Inserts the booking form shortcode into a page you already have.', 'wp-time-slots-booking-form' ); ?></span>
                                </div>
                            </label>

                            <label class="wpts-qw-radio-card" data-pub="later">
                                <input type="radio" name="qw_publish_mode" value="later" onchange="wptsQwSyncPublishMode();" />
                                <div>
                                    <strong>📋 <?php esc_html_e( 'Save calendar only — I\'ll embed it later', 'wp-time-slots-booking-form' ); ?></strong>
                                    <span class="wpts-help"><?php esc_html_e( 'Use the shortcode, Gutenberg block, or Elementor widget whenever you are ready.', 'wp-time-slots-booking-form' ); ?></span>
                                </div>
                            </label>
                        </div>

                        <div id="qw_pub_new_page_fields" class="wpts-subbox" style="margin-top:16px;">
                            <div class="wpts-grid wpts-cols-2">
                                <div class="wpts-field">
                                    <label for="qw_page_title"><?php esc_html_e( 'New Page Title', 'wp-time-slots-booking-form' ); ?></label>
                                    <input type="text" id="qw_page_title" name="qw_page_title" value="<?php esc_attr_e( 'Book an Appointment', 'wp-time-slots-booking-form' ); ?>" />
                                </div>
                                <div class="wpts-field">
                                    <label for="qw_publish_status"><?php esc_html_e( 'Page Status', 'wp-time-slots-booking-form' ); ?></label>
                                    <select id="qw_publish_status" name="qw_publish_status">
                                        <option value="draft"><?php esc_html_e( 'Draft (Preview first — Recommended)', 'wp-time-slots-booking-form' ); ?></option>
                                        <option value="publish"><?php esc_html_e( 'Published immediately', 'wp-time-slots-booking-form' ); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div id="qw_pub_existing_page_fields" class="wpts-subbox" style="display:none;margin-top:16px;">
                            <div class="wpts-field">
                                <label for="qw_existing_page"><?php esc_html_e( 'Select Existing Page', 'wp-time-slots-booking-form' ); ?></label>
                                <?php
                                wp_dropdown_pages( array(
                                    'name'              => 'qw_existing_page',
                                    'id'                => 'qw_existing_page',
                                    'show_option_none'  => esc_html__( '— Select a page —', 'wp-time-slots-booking-form' ),
                                    'option_none_value' => '0',
                                ) );
                                ?>
                            </div>
                        </div>

                        <div class="wpts-qw-nav">
                            <button type="button" class="wpts-btn wpts-btn-secondary" onclick="wptsQwGoTo(3);">
                                &larr; <?php esc_html_e( 'Back: Emails & Redirect', 'wp-time-slots-booking-form' ); ?>
                            </button>
                            <button type="submit" class="wpts-btn wpts-btn-primary" id="qw_submit_btn">
                                🚀 <?php esc_html_e( 'Finish & Create Booking Form', 'wp-time-slots-booking-form' ); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </form>

    <?php endif; ?>

</div>

<?php if ( ! $result ) : ?>
<script type="text/javascript">
(function() {
    var wptsQwPresets = {
        appointment_free: {
            calName: <?php echo wp_json_encode( __( 'Appointment Booking', 'wp-time-slots-booking-form' ) ); ?>,
            pageTitle: <?php echo wp_json_encode( __( 'Book an Appointment', 'wp-time-slots-booking-form' ) ); ?>,
            start: '09:00', end: '17:00', duration: '60',
            capacity: 1, maxSlots: 1, enableQty: false, qtyLabel: <?php echo wp_json_encode( __( 'Guests', 'wp-time-slots-booking-form' ) ); ?>,
            enablePricing: false, price: 0, includePhone: false
        },
        paid_service: {
            calName: <?php echo wp_json_encode( __( 'Service Booking', 'wp-time-slots-booking-form' ) ); ?>,
            pageTitle: <?php echo wp_json_encode( __( 'Book a Session', 'wp-time-slots-booking-form' ) ); ?>,
            start: '09:00', end: '18:00', duration: '60',
            capacity: 1, maxSlots: 1, enableQty: false, qtyLabel: <?php echo wp_json_encode( __( 'Guests', 'wp-time-slots-booking-form' ) ); ?>,
            enablePricing: true, price: 50, includePhone: true
        },
        group_class: {
            calName: <?php echo wp_json_encode( __( 'Class & Workshop Schedule', 'wp-time-slots-booking-form' ) ); ?>,
            pageTitle: <?php echo wp_json_encode( __( 'Book a Class', 'wp-time-slots-booking-form' ) ); ?>,
            start: '09:00', end: '18:00', duration: '60',
            capacity: 10, maxSlots: 1, enableQty: true, qtyLabel: <?php echo wp_json_encode( __( 'Participants', 'wp-time-slots-booking-form' ) ); ?>,
            enablePricing: false, price: 25, includePhone: false
        },
        event_ticket: {
            calName: <?php echo wp_json_encode( __( 'Timed Entry Tickets', 'wp-time-slots-booking-form' ) ); ?>,
            pageTitle: <?php echo wp_json_encode( __( 'Reserve Tickets', 'wp-time-slots-booking-form' ) ); ?>,
            start: '10:00', end: '18:00', duration: '60',
            capacity: 25, maxSlots: 3, enableQty: true, qtyLabel: <?php echo wp_json_encode( __( 'Tickets', 'wp-time-slots-booking-form' ) ); ?>,
            enablePricing: true, price: 15, includePhone: false
        }
    };

    var userEditedCalName = false;
    var userEditedPageTitle = false;

    function parseHHMM(val, fallback) {
        if (!val || val.indexOf(':') === -1) return fallback;
        var p = val.split(':');
        var h = parseInt(p[0], 10);
        var m = parseInt(p[1], 10);
        if (isNaN(h) || isNaN(m)) return fallback;
        return h * 60 + m;
    }

    function formatMins(mins, is24h) {
        var h = Math.floor(mins / 60);
        var m = mins % 60;
        var mm = (m < 10 ? '0' : '') + m;
        if (!is24h) {
            var suffix = (h >= 12) ? ' PM' : ' AM';
            var h12 = h % 12;
            if (h12 === 0) h12 = 12;
            return h12 + ':' + mm + suffix;
        }
        return (h < 10 ? '0' : '') + h + ':' + mm;
    }

    function computeSlots() {
        var start = parseHHMM(document.getElementById('qw_start_time').value, 540);
        var end   = parseHHMM(document.getElementById('qw_end_time').value, 1020);
        var step  = Math.max(5, parseInt(document.getElementById('qw_slot_duration').value, 10) || 60);
        var hasBreak = document.getElementById('qw_break_enabled').checked;
        var bStart   = parseHHMM(document.getElementById('qw_break_start').value, 780);
        var bEnd     = parseHHMM(document.getElementById('qw_break_end').value, 840);

        var out = [];
        if (end < start) return out;
        var guard = 0;
        for (var t = start; t <= end && t < 1440 && guard < 200; t += step) {
            guard++;
            if (hasBreak && bEnd > bStart && t >= bStart && t < bEnd) continue;
            out.push(t);
        }
        return out;
    }

    window.wptsQwSelectPreset = function(key) {
        var p = wptsQwPresets[key];
        if (!p) return;
        document.getElementById('qw_booking_type').value = key;
        document.querySelectorAll('.wpts-qw-preset-card').forEach(function(el) {
            el.classList.toggle('is-selected', el.getAttribute('data-preset') === key);
        });

        if (!userEditedCalName) {
            document.getElementById('qw_cal_name').value = p.calName;
        }
        if (!userEditedPageTitle) {
            document.getElementById('qw_page_title').value = p.pageTitle;
        }

        document.getElementById('qw_start_time').value = p.start;
        document.getElementById('qw_end_time').value = p.end;
        document.getElementById('qw_slot_duration').value = p.duration;
        document.getElementById('qw_capacity').value = p.capacity;
        document.getElementById('qw_max_slots').value = p.maxSlots;
        document.getElementById('qw_enable_qty').checked = !!p.enableQty;
        document.getElementById('qw_qty_label').value = p.qtyLabel;
        document.getElementById('qw_qty_max').value = Math.max(2, p.capacity);
        document.getElementById('qw_enable_pricing').checked = !!p.enablePricing;
        if (p.price > 0) document.getElementById('qw_price').value = p.price;
        document.getElementById('qw_include_phone').checked = !!p.includePhone;

        wptsQwSyncCapacity();
        wptsQwTogglePricing();
        wptsQwRenderPreview();
    };

    window.wptsQwToggleGdprUrl = function() {
        var show = document.getElementById('qw_include_gdpr').checked;
        document.getElementById('qw_gdpr_url_wrap').style.display = show ? 'flex' : 'none';
    };

    window.wptsQwToggleBreak = function() {
        var show = document.getElementById('qw_break_enabled').checked;
        document.getElementById('qw_break_inputs').style.display = show ? 'inline-flex' : 'none';
        wptsQwRenderPreview();
    };

    window.wptsQwSyncCapacity = function() {
        var cap = Math.max(1, parseInt(document.getElementById('qw_capacity').value, 10) || 1);
        var qtyField = document.getElementById('qw_qty_toggle_field');
        var qtyMax = document.getElementById('qw_qty_max');
        if (qtyField) qtyField.style.display = (cap > 1) ? 'flex' : 'none';
        if (qtyMax && cap > 1) {
            qtyMax.max = cap;
            if (parseInt(qtyMax.value, 10) > cap) qtyMax.value = cap;
        }
        wptsQwToggleQty();
    };

    window.wptsQwToggleQty = function() {
        var cap = Math.max(1, parseInt(document.getElementById('qw_capacity').value, 10) || 1);
        var checked = document.getElementById('qw_enable_qty').checked && cap > 1;
        document.getElementById('qw_qty_details').style.display = checked ? 'grid' : 'none';
    };

    window.wptsQwTogglePricing = function() {
        var checked = document.getElementById('qw_enable_pricing').checked;
        document.getElementById('qw_pricing_fields').style.display = checked ? 'grid' : 'none';
    };

    window.wptsQwToggleReturnUrl = function() {
        var mode = document.getElementById('qw_return_mode').value;
        document.getElementById('qw_custom_return_wrap').style.display = (mode === 'custom') ? 'flex' : 'none';
    };

    window.wptsQwSyncPublishMode = function() {
        var checked = document.querySelector('input[name="qw_publish_mode"]:checked');
        var mode = checked ? checked.value : 'new_page';
        document.querySelectorAll('.wpts-qw-radio-card').forEach(function(card) {
            card.classList.toggle('is-selected', card.getAttribute('data-pub') === mode);
        });
        document.getElementById('qw_pub_new_page_fields').style.display = (mode === 'new_page') ? 'block' : 'none';
        document.getElementById('qw_pub_existing_page_fields').style.display = (mode === 'existing_page') ? 'block' : 'none';
    };

    window.wptsQwRenderPreview = function() {
        var slots = computeSlots();
        var is24h = (parseInt(document.getElementById('qw_military_time').value, 10) === 1);
        var badge = document.getElementById('qw_preview_badge');
        var box   = document.getElementById('qw_preview_chips');
        if (!badge || !box) return;

        badge.textContent = slots.length + ' ' + (slots.length === 1 ? <?php echo wp_json_encode( __( 'slot / day', 'wp-time-slots-booking-form' ) ); ?> : <?php echo wp_json_encode( __( 'slots / day', 'wp-time-slots-booking-form' ) ); ?>);

        if (slots.length === 0) {
            box.innerHTML = '<span style="color:#b91c1c;font-size:12.5px;font-weight:600;"><?php echo esc_js( __( 'Last slot must be equal to or later than First slot.', 'wp-time-slots-booking-form' ) ); ?></span>';
            return;
        }
        var html = '';
        for (var i = 0; i < slots.length; i++) {
            html += '<span class="wpts-qw-preview-chip">' + formatMins(slots[i], is24h) + '</span>';
        }
        box.innerHTML = html;
    };

    window.wptsQwUpdateSummary = function() {
        var name = document.getElementById('qw_cal_name').value || <?php echo wp_json_encode( __( 'Booking Form', 'wp-time-slots-booking-form' ) ); ?>;
        var days = [];
        document.querySelectorAll('input[name="qw_days[]"]:checked').forEach(function(cb) {
            var lbl = cb.nextElementSibling ? cb.nextElementSibling.textContent : '';
            if (lbl) days.push(lbl);
        });
        var slots = computeSlots();
        var cap = Math.max(1, parseInt(document.getElementById('qw_capacity').value, 10) || 1);
        var isPaid = document.getElementById('qw_enable_pricing').checked;
        var priceStr = <?php echo wp_json_encode( __( 'Free', 'wp-time-slots-booking-form' ) ); ?>;
        if (isPaid) {
            var fmt = document.getElementById('qw_currency_format').value || '$ {0}';
            var val = document.getElementById('qw_price').value || '0';
            priceStr = fmt.replace('{0}', val);
        }
        var email = document.getElementById('qw_dest_email').value || '—';

        document.getElementById('qw_sum_name').textContent = name;
        document.getElementById('qw_sum_schedule').textContent = (days.length ? days.join(', ') : 'None') + ' · ' + slots.length + ' slots/day';
        document.getElementById('qw_sum_cap_price').textContent = <?php echo wp_json_encode( __( 'Capacity', 'wp-time-slots-booking-form' ) ); ?> + ' ' + cap + ' · ' + priceStr;
        document.getElementById('qw_sum_email').textContent = email;
    };

    window.wptsQwGoTo = function(stepNum) {
        if (stepNum === 4) {
            wptsQwUpdateSummary();
        }
        document.querySelectorAll('.wpts-qw-panel').forEach(function(panel) {
            panel.classList.toggle('is-active', parseInt(panel.getAttribute('data-qw-panel'), 10) === stepNum);
        });
        document.querySelectorAll('.wpts-qw-step').forEach(function(btn) {
            var s = parseInt(btn.getAttribute('data-qw-step'), 10);
            btn.classList.toggle('is-active', s === stepNum);
            btn.classList.toggle('is-done', s < stepNum);
        });
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    window.wptsQwRunEmailTest = function() {
        var fromEmail = document.getElementById('qw_from_email').value;
        var toEmail   = document.getElementById('qw_dest_email').value;
        var resultsEl = document.getElementById('qw_diag_results');
        var loaderEl  = document.getElementById('qw_diag_loader');
        if (!toEmail) {
            alert(<?php echo wp_json_encode( __( 'Please enter a notification email address first.', 'wp-time-slots-booking-form' ) ); ?>);
            return;
        }
        if (loaderEl) loaderEl.classList.add('is-active');
        jQuery.post(ajaxurl, {
            action: 'wpts_run_email_diagnostic',
            nonce: '<?php echo esc_js( $diag_nonce ); ?>',
            from_email: fromEmail,
            to_email: toEmail
        }, function(response) {
            if (loaderEl) loaderEl.classList.remove('is-active');
            if (response && response.success && response.data && response.data.html) {
                resultsEl.innerHTML = response.data.html;
            } else {
                var msg = (response && response.data) ? response.data : <?php echo wp_json_encode( __( 'Could not run email diagnostic.', 'wp-time-slots-booking-form' ) ); ?>;
                resultsEl.innerHTML = '<div class="wpts-alert wpts-alert-warning" style="margin:0;"><span>⚠️</span><div>' + jQuery('<div>').text(msg).html() + '</div></div>';
            }
            resultsEl.style.display = 'block';
        }).fail(function() {
            if (loaderEl) loaderEl.classList.remove('is-active');
            resultsEl.innerHTML = '<div class="wpts-alert wpts-alert-warning" style="margin:0;"><span>⚠️</span><div><?php echo esc_js( __( 'Request failed while testing email delivery.', 'wp-time-slots-booking-form' ) ); ?></div></div>';
            resultsEl.style.display = 'block';
        });
    };

    document.addEventListener('DOMContentLoaded', function() {
        var calInput = document.getElementById('qw_cal_name');
        var pgInput  = document.getElementById('qw_page_title');
        if (calInput) calInput.addEventListener('input', function() { userEditedCalName = true; });
        if (pgInput)  pgInput.addEventListener('input', function() { userEditedPageTitle = true; });

        wptsQwSyncCapacity();
        wptsQwTogglePricing();
        wptsQwRenderPreview();
        wptsQwSyncPublishMode();
    });
})();
</script>
<?php endif; ?>
