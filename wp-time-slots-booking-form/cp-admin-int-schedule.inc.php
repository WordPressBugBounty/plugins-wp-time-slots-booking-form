<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wpdb;

$this->item = intval($_GET["cal"]);

$current_user = wp_get_current_user();
$current_user_access = current_user_can('manage_options');

if ( !is_admin() || (!$current_user_access && !@in_array($current_user->ID, unserialize($this->get_option("cp_user_access","")))))
{
    echo 'Direct access not allowed.';
    exit;
}

if ($this->item != 0)
    $myform = $wpdb->get_results( $wpdb->prepare('SELECT * FROM '.$wpdb->prefix.$this->table_items .' WHERE id=%d' ,$this->item) );

$default_from = date("Y-m-d", strtotime("today -10 days"));
$default_to   = date("Y-m-d", strtotime("today +30 days"));

$rawfrom = (isset($_GET["dfrom"]) ? sanitize_text_field($_GET["dfrom"]) : '');
$rawto   = (isset($_GET["dto"]) ? sanitize_text_field(@$_GET["dto"]) : '');

if ($this->get_option('date_format', 'mm/dd/yy') == 'dd/mm/yy')
{
    $rawfrom = str_replace('/', '.', $rawfrom);
    $rawto   = str_replace('/', '.', $rawto);
}

$dfrom = ($rawfrom ? date("Y-m-d", strtotime($rawfrom)) : $default_from);
$dto   = ($rawto ? date("Y-m-d", strtotime($rawto)) : $default_to);

$is_calendar_view   = isset($_GET["calendarview"]);
$active_form_title  = ($this->item != 0 && !empty($myform)) ? $myform[0]->form_name : __('All Forms', 'wp-time-slots-booking-form');
$booking_orders_url = admin_url('admin.php?page=' . $this->menu_parameter . '&cal=' . intval($this->item) . '&list=1');
$has_active_filters = (!empty($_GET["dfrom"]) || !empty($_GET["dto"]) || !empty($_GET["searchfor"]) || !empty($_GET["paid"]) || (isset($_GET["status"]) && $_GET["status"] !== '-1'));
?>

<style type="text/css">
/* Page-specific styles: Schedule View Switcher, Orders Banner & Schedule List Grid */
    .wpts-view-switcher {
        display: inline-flex;
        background: var(--wpts-bg);
        border: 1px solid var(--wpts-border);
        padding: 4px;
        border-radius: var(--wpts-radius-sm);
        gap: 4px;
    }
    .wpts-view-tab {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 13px;
        border-radius: 6px;
        font-size: 12.5px;
        font-weight: 600;
        text-decoration: none;
        color: var(--wpts-text-muted);
        transition: all 0.15s ease;
    }
    .wpts-view-tab:hover {
        color: var(--wpts-text);
    }
    .wpts-view-tab.active {
        background: var(--wpts-primary);
        color: #ffffff;
        box-shadow: 0 1px 2px rgba(13, 148, 136, 0.25);
    }
    .wpts-orders-banner {
        background: var(--wpts-accent-light);
        border: 1px solid var(--wpts-accent-border);
        border-left: 4px solid var(--wpts-accent);
        border-radius: var(--wpts-radius-sm);
        padding: 14px 20px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
        font-size: 13px;
        color: #0c4a6e;
        line-height: 1.5;
    }
    .wpts-orders-banner a.wpts-inline-link {
        color: var(--wpts-accent);
        font-weight: 700;
        text-decoration: underline;
    }
    .wpts-schedule-table-wrap {
        width: 100%;
        overflow-x: auto;
    }
    #dex_printable_contents {
        min-width: 880px;
    }
    #dex_printable_contents .cpapp_break {
        display: none;
    }
    #dex_printable_contents .cpapp_no_wrap {
        display: grid;
        grid-template-columns: 125px 105px 75px 95px 220px 1fr 165px;
        align-items: center;
        border-bottom: 1px solid #f1f5f9;
        transition: background-color 0.12s ease;
    }
    #dex_printable_contents .cpapp_no_wrap:first-of-type {
        background: var(--wpts-bg);
        border-bottom: 1px solid var(--wpts-border);
    }
    #dex_printable_contents .cpapp_no_wrap:not(:first-of-type):hover {
        background: #f8fafc;
    }
    #dex_printable_contents .cpapp_no_wrap > div {
        float: none !important;
        width: auto !important;
        min-width: 0 !important;
        margin: 0 !important;
        border: none !important;
        padding: 14px 16px !important;
        font-size: 13.5px;
        line-height: 1.45;
        color: var(--wpts-text);
        word-break: break-word;
    }
    #dex_printable_contents .cptslotsb_field_header {
        font-size: 11.5px !important;
        font-weight: 700 !important;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--wpts-text-muted) !important;
        background: transparent !important;
        padding: 13px 16px !important;
    }
    #dex_printable_contents .cpapp_no_wrap:not(:first-of-type) .cptslotsb_field_0 {
        font-weight: 700;
        color: var(--wpts-text);
    }
    #dex_printable_contents .cpapp_no_wrap:not(:first-of-type) .cptslotsb_field_5 {
        font-size: 13px;
        color: #334155;
        white-space: pre-line;
    }
    #dex_printable_contents .cptslotsb_cancelled {
        color: #94a3b8 !important;
        text-decoration: line-through;
    }
    .wpts-slot-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--wpts-primary-light);
        color: var(--wpts-primary-hover);
        border: 1px solid var(--wpts-primary-border);
        padding: 3px 9px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 12.5px;
        white-space: nowrap;
    }
    .wpts-qty-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 28px;
        height: 24px;
        padding: 0 8px;
        background: var(--wpts-bg);
        border: 1px solid var(--wpts-border);
        border-radius: 6px;
        font-weight: 700;
        font-size: 12px;
        color: var(--wpts-text);
    }
    .wpts-paid-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 9px;
        border-radius: 999px;
        font-size: 11.5px;
        font-weight: 700;
        background: var(--wpts-success-bg);
        color: #065f46;
        border: 1px solid #a7f3d0;
    }
    .wpts-unpaid-badge {
        display: inline-flex;
        align-items: center;
        padding: 3px 9px;
        border-radius: 999px;
        font-size: 11.5px;
        font-weight: 600;
        background: var(--wpts-bg);
        color: var(--wpts-text-muted);
        border: 1px solid var(--wpts-border);
    }
    .wpts-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 9px;
        border-radius: 999px;
        font-size: 11.5px;
        font-weight: 700;
        background: var(--wpts-primary-light);
        color: var(--wpts-primary-hover);
        border: 1px solid var(--wpts-primary-border);
    }
    .wpts-status-pill.is-cancelled {
        background: var(--wpts-danger-bg);
        color: #dc2626;
        border-color: #fecaca;
        text-decoration: none !important;
    }
    .wpts-manage-order-link {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        margin-top: 6px;
        font-size: 11.5px;
        font-weight: 600;
        color: var(--wpts-accent);
        text-decoration: none;
    }
    .wpts-manage-order-link:hover {
        text-decoration: underline;
    }
</style>

<div class="wrap wpts-admin-wrap">

    <!-- Top SaaS Header -->
    <div class="wpts-header">
        <div class="wpts-header-left">
            <a href="<?php echo esc_url(admin_url('admin.php?page='.$this->menu_parameter)); ?>" class="wpts-back-btn">
                &larr; <?php esc_html_e('Calendars List', 'wp-time-slots-booking-form'); ?>
            </a>
            <div class="wpts-title-group">
                <h1>
                    📅 <?php esc_html_e('Schedule', 'wp-time-slots-booking-form'); ?> &mdash; <?php echo esc_html($active_form_title); ?>
                    <span class="wpts-badge">
                        <?php echo $is_calendar_view ? esc_html__('Calendar View', 'wp-time-slots-booking-form') : esc_html__('List View', 'wp-time-slots-booking-form'); ?>
                    </span>
                </h1>
                <p class="wpts-subtitle"><?php echo esc_html($this->plugin_name); ?> &mdash; <?php esc_html_e('Inspect booked time slots by day, week, month, or filterable schedule list', 'wp-time-slots-booking-form'); ?></p>
            </div>
        </div>

        <div class="wpts-header-actions">
            <!-- View Switcher Pill -->
            <div class="wpts-view-switcher">
                <a href="?page=<?php echo esc_attr($this->menu_parameter); ?>&cal=<?php echo intval($this->item); ?>&schedule=1&calendarview=1" class="wpts-view-tab <?php echo $is_calendar_view ? 'active' : ''; ?>">
                    📅 <?php esc_html_e('Calendar View', 'wp-time-slots-booking-form'); ?>
                </a>
                <a href="?page=<?php echo esc_attr($this->menu_parameter); ?>&cal=<?php echo intval($this->item); ?>&schedule=1" class="wpts-view-tab <?php echo !$is_calendar_view ? 'active' : ''; ?>">
                    📑 <?php esc_html_e('Schedule List View', 'wp-time-slots-booking-form'); ?>
                </a>
            </div>

            <!-- Primary Link to Booking Orders Page -->
            <a href="<?php echo esc_url($booking_orders_url); ?>" class="wpts-btn wpts-btn-accent-solid">
                📋 <?php esc_html_e('Manage Booking Orders', 'wp-time-slots-booking-form'); ?>
            </a>

            <?php if ($this->item > 0) : ?>
                <a href="?page=<?php echo esc_attr($this->menu_parameter); ?>&cal=<?php echo intval($this->item); ?>&addbk=1" class="wpts-btn wpts-btn-secondary">
                    ➕ <?php esc_html_e('Add Booking', 'wp-time-slots-booking-form'); ?>
                </a>
                <?php if ($current_user_access) : ?>
                <a href="?page=<?php echo esc_attr($this->menu_parameter); ?>&cal=<?php echo intval($this->item); ?>" class="wpts-btn wpts-btn-secondary">
                    ⚙️ <?php esc_html_e('Edit Calendar', 'wp-time-slots-booking-form'); ?>
                </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Booking Orders Administration Callout Banner -->
    <div class="wpts-orders-banner">
        <div>
            💡 <strong><?php esc_html_e('Need to approve, cancel, mark as paid, reply to customers, or delete bookings?', 'wp-time-slots-booking-form'); ?></strong>
            <?php esc_html_e('The Schedule view is designed for checking slot availability and daily agendas. For full booking administration, open the', 'wp-time-slots-booking-form'); ?>
            <a href="<?php echo esc_url($booking_orders_url); ?>" class="wpts-inline-link"><?php esc_html_e('Booking Orders page', 'wp-time-slots-booking-form'); ?></a>.
        </div>
        <a href="<?php echo esc_url($booking_orders_url); ?>" class="wpts-btn wpts-btn-secondary" style="border-color:var(--wpts-accent-border);color:var(--wpts-accent);">
            📋 <?php esc_html_e('Open Booking Orders', 'wp-time-slots-booking-form'); ?> &rarr;
        </a>
    </div>

    <?php if (!$is_calendar_view) : ?>

        <!-- ==========================================
             SCHEDULE LIST VIEW
             ========================================== -->
        <div class="wpts-card">
            <div class="wpts-card-body">
                <form action="admin.php" method="get" id="wpts_schedule_filter_form">
                    <input type="hidden" name="page" value="<?php echo esc_attr($this->menu_parameter); ?>" />
                    <input type="hidden" name="schedule" value="1" />

                    <div class="wpts-filter-bar">
                        <div class="wpts-filter-field">
                            <label for="dfrom"><?php esc_html_e('Slot Date From', 'wp-time-slots-booking-form'); ?></label>
                            <input autocomplete="off" type="text" id="dfrom" name="dfrom" style="width:130px;" placeholder="<?php echo esc_attr($default_from); ?>" value="<?php if (!empty($_GET["dfrom"])) echo esc_attr(@$_GET["dfrom"]); ?>" />
                        </div>

                        <div class="wpts-filter-field">
                            <label for="dto"><?php esc_html_e('Slot Date To', 'wp-time-slots-booking-form'); ?></label>
                            <input autocomplete="off" type="text" id="dto" name="dto" style="width:130px;" placeholder="<?php echo esc_attr($default_to); ?>" value="<?php if (!empty($_GET["dto"])) echo esc_attr(@$_GET["dto"]); ?>" />
                        </div>

                        <div class="wpts-filter-field" style="flex:1;min-width:160px;">
                            <label for="searchfor"><?php esc_html_e('Search for', 'wp-time-slots-booking-form'); ?></label>
                            <input type="text" id="searchfor" name="searchfor" placeholder="<?php esc_attr_e('Customer, email, notes...', 'wp-time-slots-booking-form'); ?>" value="<?php if (!empty($_GET["searchfor"])) echo esc_attr(@$_GET["searchfor"]); ?>" />
                        </div>

                        <div class="wpts-filter-field" style="min-width:130px;">
                            <label for="paid"><?php esc_html_e('Paid Status', 'wp-time-slots-booking-form'); ?></label>
                            <select id="paid" name="paid">
                                <option value=""><?php esc_html_e('All', 'wp-time-slots-booking-form'); ?></option>
                                <option value="1" <?php if (!empty($_GET["paid"]) && @$_GET["paid"]) echo ' selected'; ?>><?php esc_html_e('Paid only', 'wp-time-slots-booking-form'); ?></option>
                            </select>
                        </div>

                        <div class="wpts-filter-field" style="min-width:140px;">
                            <label for="status"><?php esc_html_e('Booking Status', 'wp-time-slots-booking-form'); ?></label>
                            <?php $this->render_status_box('status', (!isset($_GET["status"]) ? '-1' : sanitize_text_field($_GET["status"])), true); ?>
                        </div>

                        <div class="wpts-filter-field" style="min-width:170px;">
                            <label for="cal"><?php esc_html_e('Calendar / Item', 'wp-time-slots-booking-form'); ?></label>
                            <select id="cal" name="cal">
                                <option value="0"><?php esc_html_e('[All Items]', 'wp-time-slots-booking-form'); ?></option>
                                <?php
                                $myrows = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix.$this->table_items );                                                                     
                                foreach ($myrows as $item)  
                                    echo '<option value="'.esc_attr($item->id).'"'.(intval($item->id)==intval($this->item)?" selected":"").'>'.esc_html($item->form_name).'</option>'; 
                                ?>
                            </select>
                        </div>

                        <div class="wpts-filter-actions">
                            <button type="submit" name="ds" value="<?php esc_attr_e('Filter', 'wp-time-slots-booking-form'); ?>" class="wpts-btn wpts-btn-primary">
                                🔍 <?php esc_html_e('Filter', 'wp-time-slots-booking-form'); ?>
                            </button>
                            <button type="submit" name="<?php echo esc_attr($this->prefix); ?>_csv2" value="<?php esc_attr_e('Export to CSV', 'wp-time-slots-booking-form'); ?>" class="wpts-btn wpts-btn-secondary">
                                ⬇️ <?php esc_html_e('Export to CSV', 'wp-time-slots-booking-form'); ?>
                            </button>
                            <?php if ($has_active_filters) : ?>
                                <a href="?page=<?php echo esc_attr($this->menu_parameter); ?>&cal=<?php echo intval($this->item); ?>&schedule=1" class="wpts-btn wpts-btn-secondary">
                                    ✕ <?php esc_html_e('Reset', 'wp-time-slots-booking-form'); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Quick Date Range Presets -->
                    <div class="wpts-presets-row">
                        <span style="font-size:11.5px;font-weight:600;color:var(--wpts-text-muted);margin-right:4px;">⚡ <?php esc_html_e('Quick Schedule Ranges:', 'wp-time-slots-booking-form'); ?></span>
                        <button type="button" class="wpts-preset-pill" onclick="wptsSetScheduleRange(0, 0)"><?php esc_html_e('Today', 'wp-time-slots-booking-form'); ?></button>
                        <button type="button" class="wpts-preset-pill" onclick="wptsSetScheduleRange(0, 7)"><?php esc_html_e('Next 7 Days', 'wp-time-slots-booking-form'); ?></button>
                        <button type="button" class="wpts-preset-pill" onclick="wptsSetScheduleRange(0, 30)"><?php esc_html_e('Next 30 Days', 'wp-time-slots-booking-form'); ?></button>
                        <button type="button" class="wpts-preset-pill" onclick="wptsSetScheduleRange(-10, 30)"><?php esc_html_e('Default (-10d to +30d)', 'wp-time-slots-booking-form'); ?></button>
                        <span style="margin-left:auto;font-size:12px;color:var(--wpts-text-muted);">
                            <?php printf(esc_html__('Showing slots from %1$s to %2$s', 'wp-time-slots-booking-form'), '<strong>'.esc_html($dfrom).'</strong>', '<strong>'.esc_html($dto).'</strong>'); ?>
                        </span>
                    </div>
                </form>
            </div>
        </div>

        <!-- Schedule List Table Card -->
        <div class="wpts-card">
            <div class="wpts-card-header">
                <h3 class="wpts-card-title">
                    🕒 <?php esc_html_e('Scheduled Time Slots', 'wp-time-slots-booking-form'); ?>
                    <span class="wpts-badge" id="wpts_slot_count_badge" style="display:none;">0</span>
                </h3>
                <div style="display:flex;align-items:center;gap:8px;">
                    <button type="button" class="wpts-btn wpts-btn-soft" onclick="do_dexapp_print();">
                        🖨️ <?php esc_html_e('Print Schedule', 'wp-time-slots-booking-form'); ?>
                    </button>
                </div>
            </div>

            <div class="wpts-schedule-table-wrap">
                <div id="dex_printable_contents">
                    <div class="cpapp_no_wrap">
                        <div class="cptslotsb_field_0 cptslotsb_field_header"><?php esc_html_e('Date', 'wp-time-slots-booking-form'); ?></div>
                        <div class="cptslotsb_field_1 cptslotsb_field_header"><?php esc_html_e('Slot', 'wp-time-slots-booking-form'); ?></div>
                        <div class="cptslotsb_field_2 cptslotsb_field_header"><?php esc_html_e('Qty', 'wp-time-slots-booking-form'); ?></div>
                        <div class="cptslotsb_field_3 cptslotsb_field_header"><?php esc_html_e('Paid', 'wp-time-slots-booking-form'); ?></div>
                        <div class="cptslotsb_field_4 cptslotsb_field_header"><?php esc_html_e('Customer Email', 'wp-time-slots-booking-form'); ?></div>
                        <div class="cptslotsb_field_5 cptslotsb_field_header"><?php esc_html_e('Booking Details / Data', 'wp-time-slots-booking-form'); ?></div>
                        <div class="cptslotsb_field_6 cptslotsb_field_header"><?php esc_html_e('Status & Action', 'wp-time-slots-booking-form'); ?></div>
                        <div class="cpapp_break"></div>
                    </div>
                    <div class="cpapp_break"></div>
                    <?php
                    echo $this->clean_sanitize($this->filter_list( array(
                        'calendar'  => ($this->item != 0 ? $this->item : ''),
                        'fields'    => 'DATE,TIME,quantity,paid,email,data,cancelled',
                        'from'      => $dfrom,
                        'to'        => $dto,
                        'searchfor' => (isset($_GET["searchfor"]) ? sanitize_text_field($_GET["searchfor"]) : ''),
                        'paidonly'  => (!empty($_GET["paid"]) ? intval($_GET["paid"]) : ''),
                        'status'    => (!isset($_GET["status"]) ? '-1' : sanitize_text_field($_GET["status"]))
                    ) ));
                    ?>
                </div>
            </div>
        </div>

    <?php else : ?>

        <!-- ==========================================
             SCHEDULE CALENDAR VIEW (MULTIVIEW)
             ========================================== -->
        <div class="wpts-card" id="cpabc_printable_contents">
            <div class="wpts-card-header">
                <div>
                    <h3 class="wpts-card-title">📅 <?php esc_html_e('Interactive Schedule Calendar View', 'wp-time-slots-booking-form'); ?></h3>
                    <p style="margin:3px 0 0;font-size:12.5px;color:var(--wpts-text-muted);">
                        <?php esc_html_e('Switch between Day, Week, Month, and Multi-Month views. For CSV export, printing, and status filters, switch to', 'wp-time-slots-booking-form'); ?>
                        <a href="?page=<?php echo esc_attr($this->menu_parameter); ?>&cal=<?php echo intval($this->item); ?>&schedule=1" style="color:var(--wpts-primary);font-weight:600;"><?php esc_html_e('Schedule List View', 'wp-time-slots-booking-form'); ?></a>.
                    </p>
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <a href="?page=<?php echo esc_attr($this->menu_parameter); ?>&cal=<?php echo esc_attr($this->item); ?>&addbk=1" class="wpts-btn wpts-btn-soft">
                        ➕ <?php esc_html_e('Add Booking from Dashboard', 'wp-time-slots-booking-form'); ?>
                    </a>
                </div>
            </div>

            <div class="wpts-card-body">
                <script type="text/javascript">
                var pathCalendar = "<?php echo $this->get_site_url( true ); ?>/?cp_slots_action=mv&formid=<?php echo intval($this->item); ?>";
                var dc_subjects = "";var dc_locations = "";
                initMultiViewCal("cal<?php echo intval($this->item); ?>", <?php echo intval($this->item); ?>,
                {viewDay:true,
                viewWeek:true,
                viewMonth:true,
                viewNMonth:true,
                viewList:false,
                viewdefault:"week",
                numberOfMonths:12,
                showtooltip:false,
                tooltipon:0,
                shownavigate:false,
                url:"",
                target:0,
                start_weekday:0,
                language:"en-GB",
                cssStyle:"cupertino",
                edition:true,
                btoday:true,
                dialogWidth: 330,
                bnavigation:true,
                brefresh:true,
                bnew:false,
                path:pathCalendar,
                userAdd:false,
                userEdit:false,
                userDel:false,
                userEditOwner:true,
                userDelOwner:false,
                showtooltipdwm_mouseover:true,
                userOwner:0 ,cellheight:62 , palette:0, paletteDefault:"F00", paletteFull:["FFF","FCC","FC9","FF9","FFC","9F9","9FF","CFF","CCF","FCF","CCC","F66","F96","FF6","FF3","6F9","3FF","6FF","99F","F9F","BBB","F00","F90","FC6","FF0","3F3","6CC","3CF","66C","C6C","999","C00","F60","FC3","FC0","3C0","0CC","36F","63F","C3C","666","900","C60","C93","990","090","399","33F","60C","939","333","600","930","963","660","060","366","009","339","636","000","300","630","633","330","030","033","006","309","303"]});
                </script>
              
                <div id="multicalendar"><div id="cal<?php echo intval($this->item); ?>" class="multicalendar"></div></div>
                <div style="clear:both;height:10px;"></div>
            </div>
        </div>

    <?php endif; ?>

</div>

<script type="text/javascript">
function wptsFormatDateYMD(d) {
    var m = (d.getMonth() + 1);
    var day = d.getDate();
    return d.getFullYear() + '-' + (m < 10 ? '0' : '') + m + '-' + (day < 10 ? '0' : '') + day;
}

function wptsSetScheduleRange(offsetStartDays, offsetEndDays) {
    var start = new Date();
    var end   = new Date();
    start.setDate(start.getDate() + offsetStartDays);
    end.setDate(end.getDate() + offsetEndDays);
    document.getElementById('dfrom').value = wptsFormatDateYMD(start);
    document.getElementById('dto').value   = wptsFormatDateYMD(end);
    document.getElementById('wpts_schedule_filter_form').submit();
}

function do_dexapp_print()
{
    w = window.open();
    w.document.write("<style>.cpnopr, .wpts-manage-order-link, img.emoji, img.wp-smiley{display:none !important;} body{font-family:sans-serif;padding:20px;color:#0f172a;} .cpapp_break{display:none;} .cpapp_no_wrap{display:grid;grid-template-columns:110px 85px 55px 75px 180px 1fr 110px;border-bottom:1px solid #cbd5e1;align-items:start;} .cpapp_no_wrap:first-of-type{background:#f1f5f9;font-weight:bold;border-bottom:2px solid #0f172a;} .cpapp_no_wrap > div{padding:8px 10px;font-size:12px;word-break:break-word;} a{color:#0f172a;text-decoration:none;}</style>"+document.getElementById('dex_printable_contents').innerHTML);
    w.print();
    w.close();     
}

var $j = jQuery.noConflict();
$j(function() {
    $j("#dfrom").datepicker({ dateFormat: 'yy-mm-dd' });
    $j("#dto").datepicker({ dateFormat: 'yy-mm-dd' });

    // Enhance Schedule List View rows with badges & direct Booking Orders links
    var $container = $j('#dex_printable_contents');
    if ($container.length) {
        var $rows = $container.find('.cpapp_no_wrap').not(':first');
        var countBadge = document.getElementById('wpts_slot_count_badge');
        if (countBadge) {
            countBadge.textContent = $rows.length + ($rows.length === 1 ? ' <?php echo esc_js(__('Slot', 'wp-time-slots-booking-form')); ?>' : ' <?php echo esc_js(__('Slots', 'wp-time-slots-booking-form')); ?>');
            countBadge.style.display = 'inline-flex';
        }

        if ($rows.length === 0) {
            $container.append(
                '<div class="cpnopr" style="text-align:center;padding:48px 20px;color:#64748b;">' +
                    '<span style="font-size:34px;display:block;margin-bottom:8px;">🗓️</span>' +
                    '<strong style="font-size:15px;color:#0f172a;display:block;margin-bottom:4px;"><?php echo esc_js(__('No scheduled time slots found for this period', 'wp-time-slots-booking-form')); ?></strong>' +
                    '<span><?php echo esc_js(__('Try expanding the From / To date range above or clearing your search filters.', 'wp-time-slots-booking-form')); ?></span>' +
                '</div>'
            );
        } else {
            var baseOrdersUrl = '<?php echo esc_js($booking_orders_url); ?>';
            $rows.each(function() {
                var $r = $j(this);
                var $slotCell   = $r.find('.cptslotsb_field_1');
                var $qtyCell    = $r.find('.cptslotsb_field_2');
                var $paidCell   = $r.find('.cptslotsb_field_3');
                var $emailCell  = $r.find('.cptslotsb_field_4');
                var $statusCell = $r.find('.cptslotsb_field_6');

                var slotTxt = $j.trim($slotCell.text());
                if (slotTxt !== '') {
                    $slotCell.html('<span class="wpts-slot-pill"><span class="cpnopr">🕒 </span>' + $j('<div>').text(slotTxt).html() + '</span>');
                }

                var qtyTxt = $j.trim($qtyCell.text());
                if (qtyTxt !== '') {
                    $qtyCell.html('<span class="wpts-qty-pill">' + $j('<div>').text(qtyTxt).html() + '</span>');
                }

                var paidTxt = $j.trim($paidCell.text());
                if (paidTxt !== '') {
                    $paidCell.html('<span class="wpts-paid-badge"><span class="cpnopr">✓ </span>' + $j('<div>').text(paidTxt).html() + '</span>');
                } else {
                    $paidCell.html('<span class="wpts-unpaid-badge">—</span>');
                }

                var emailTxt = $j.trim($emailCell.text());
                if (emailTxt !== '' && emailTxt.indexOf('@') !== -1) {
                    var safeEmail = $j('<div>').text(emailTxt).html();
                    $emailCell.html('<a href="mailto:' + safeEmail + '" style="color:#0284c7;font-weight:600;text-decoration:none;"><span class="cpnopr">✉️ </span>' + safeEmail + '</a>');
                }

                var statusTxt = $j.trim($statusCell.text());
                var isCancelled = $statusCell.hasClass('cptslotsb_cancelled');
                var pillClass = 'wpts-status-pill' + (isCancelled ? ' is-cancelled' : '');
                var orderFilterUrl = baseOrdersUrl + (emailTxt !== '' ? '&search=' + encodeURIComponent(emailTxt) : '');
                $statusCell.html(
                    '<div><span class="' + pillClass + '">' + $j('<div>').text(statusTxt).html() + '</span></div>' +
                    '<a class="wpts-manage-order-link cpnopr" href="' + orderFilterUrl + '">📋 <?php echo esc_js(__('Manage in Orders', 'wp-time-slots-booking-form')); ?> &rarr;</a>'
                );
            });
        }
    }
});
</script>