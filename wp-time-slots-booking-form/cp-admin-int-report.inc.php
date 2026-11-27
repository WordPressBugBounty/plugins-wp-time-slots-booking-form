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

global $numberofdates;
$numberofdates = [];

if ($this->item != 0)
    $myform = $wpdb->get_results( $wpdb->prepare('SELECT * FROM '.$wpdb->prefix.$this->table_items .' WHERE id=%d', $this->item) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

$current_page = intval((empty($_GET["p"]) ? 0 : $_GET["p"]));
if (!$current_page) $current_page = 1;
$records_per_page = 50;

$date_start = '';
$date_end   = '';

$rawfrom = (isset($_GET["dfrom"]) ? sanitize_text_field($_GET["dfrom"]) : '');
$rawto   = (isset($_GET["dto"]) ? sanitize_text_field($_GET["dto"]) : '');
if ($this->get_option('date_format', 'mm/dd/yy') == 'dd/mm/yy')
{
    $rawfrom = str_replace('/','.',$rawfrom);
    $rawto   = str_replace('/','.',$rawto);
}

$cond = '';
if (!empty($_GET["search"])) $cond .= " AND (data like '%".esc_sql(sanitize_text_field($_GET["search"]))."%' OR posted_data LIKE '%".esc_sql(sanitize_text_field($_GET["search"]))."%')";
if ($rawfrom != '')
{
    $date_start = date("Y-m-d",strtotime($rawfrom));
    $cond .= " AND (`time` >= '".esc_sql( $date_start )."')";
}
if ($rawto != '')
{
    $date_end = date("Y-m-d",strtotime($rawto));
    $cond .= " AND (`time` <= '".esc_sql($date_end)." 23:59:59')";
}
if ($this->item != 0) $cond .= " AND formid=".intval($this->item);

$events = $wpdb->get_results( "SELECT ipaddr,time,notifyto,posted_data FROM ".$wpdb->prefix.$this->table_messages." WHERE 1=1 ".$cond." ORDER BY `time` DESC" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
$total_submissions = count($events);

// General initialization
$fields = array();
$fields["date"]     = array();
$fields["time"]     = array();
$fields["ip"]       = array();
$fields["notifyto"] = array();

$revenue_daily     = array();
$total_revenue     = 0;
$total_booked_apps = 0;
$unique_emails     = array();

foreach ($events as $item)
{
    if (empty($fields["date"]["k".substr($item->time,0,10)])) $fields["date"]["k".substr($item->time,0,10)] = 0;
    if (empty($fields["time"]["k".substr($item->time,11,2)])) $fields["time"]["k".substr($item->time,11,2)] = 0;
    if (empty($fields["notifyto"]["k".$item->notifyto])) $fields["notifyto"]["k".$item->notifyto] = 0;
    if (empty($fields["ip"]["k".$item->ipaddr])) $fields["ip"]["k".$item->ipaddr] = 0;    
    
    $fields["date"]["k".substr($item->time,0,10)]++;
    $fields["time"]["k".substr($item->time,11,2)]++;
    $fields["notifyto"]["k".$item->notifyto]++;
    $fields["ip"]["k".$item->ipaddr]++;

    if (!empty($item->notifyto)) {
        $unique_emails[strtolower(trim($item->notifyto))] = true;
    }

    $params = unserialize($item->posted_data);
    
    $price = 0;
    if (is_array($params)) {
        if (isset($params["final_price"])) 
            $price = floatval($params["final_price"]);
        if (isset($params["apps"]) && is_array($params["apps"]))
            $total_booked_apps += count($params["apps"]);
    }
    
    $date_key = "k".substr($item->time,0,10);
    if (empty($revenue_daily[$date_key])) $revenue_daily[$date_key] = 0;
    $revenue_daily[$date_key] += $price;
    $total_revenue += $price;

    if (is_array($params)) {
        foreach ($params as $param => $value) {
            if (!is_array($value) && strlen((string)$value) < 100)
            {
                if (empty($fields[$param]["k".$value])) $fields[$param]["k".$value] = 0;
                $fields[$param]["k".$value]++;
            }
        }
    }
}

// Line graphs & Peak Hour calculation
$hourly_messages     = '';
$max_hourly_messages = 200;
$peak_hour_val       = 0;
$peak_hour_lbl       = '—';
for ($i = 0; $i <= 23; $i++) {
    $h_key = ($i < 10 ? '0' : '') . $i;
    if (isset($fields['time']['k'.$h_key]))
    {
        $cnt = intval($fields['time']['k'.$h_key]);
        $hourly_messages .= $cnt.($i < 23 ? ',' : '');
        if ($max_hourly_messages < $cnt)
            $max_hourly_messages = $cnt;
        if ($cnt > $peak_hour_val) {
            $peak_hour_val = $cnt;
            $peak_hour_lbl = $h_key . ':00 – ' . $h_key . ':59';
        }
    }        
    else
        $hourly_messages .= '0'.($i < 23 ? ',' : '');
}

if ($date_start == '')
{
    $kkeys = array_keys($fields["date"]);
    if (count($kkeys))
        $date_start = substr(min($kkeys), 1);
    else
        $date_start = date("Y-m-d");
}

if ($date_end == '')
{
    $kkeys = array_keys($fields["date"]);    
    if (count($kkeys))
        $date_end = substr(max($kkeys), 1);
    else
        $date_end = date("Y-m-d");    
}

$daily_messages     = '';
$daily_revenue_str  = '';
$max_daily_messages = 200;
$days_span          = 0;
$date = $date_start;
while ($date <= $date_end && $days_span < 1500)
{
    if (isset($fields['date']['k'.$date]))
    {
        $daily_messages .= ','.$fields['date']['k'.$date];
        if ($max_daily_messages < intval($fields['date']['k'.$date]))
            $max_daily_messages = intval($fields['date']['k'.$date]);        
    }        
    else
        $daily_messages .= ',0';

    if (isset($revenue_daily['k'.$date]))
        $daily_revenue_str .= ','.$revenue_daily['k'.$date];
    else
        $daily_revenue_str .= ',0';

    $date = date("Y-m-d", strtotime($date." +1 day"));
    $days_span++;
}
$daily_messages    = substr($daily_messages, 1);
$daily_revenue_str = substr($daily_revenue_str, 1);
$daily_avg         = round($total_submissions / max(1, $days_span), 1);

if (!isset($_GET["field"]))
    $field_filter = 'time';
else
    $field_filter = sanitize_key($_GET["field"]);

$bar_colors = array('#0d9488', '#0284c7', '#10b981', '#f59e0b', '#6366f1', '#ec4899', '#8b5cf6', '#14b8a6', '#f97316', '#06b6d4');

if ($this->item)
{
    $form = json_decode($this->cleanJSON($this->get_option('form_structure', CP_TSLOTSBOOK_DEFAULT_form_structure)));
    $form = !empty($form[0]) ? $form[0] : array();
}
else
    $form = array();

if (!function_exists('wpts_pretty_field_label')) {
    function wpts_pretty_field_label($field_key, $plugin_obj, $form_fields) {
        $system_labels = array(
            'date'              => __('Submission Date (YYYY-MM-DD)', 'wp-time-slots-booking-form'),
            'time'              => __('Hour of Day (00h – 23h)', 'wp-time-slots-booking-form'),
            'notifyto'          => __('Customer Email Address', 'wp-time-slots-booking-form'),
            'ip'                => __('Customer IP Address', 'wp-time-slots-booking-form'),
            'referrer'          => __('Referrer Page URL', 'wp-time-slots-booking-form'),
            'final_price'       => __('Final Price', 'wp-time-slots-booking-form'),
            'final_price_short' => __('Final Price (Rounded)', 'wp-time-slots-booking-form'),
            'paid'              => __('Paid Status', 'wp-time-slots-booking-form'),
            'payment_type'      => __('Payment Method', 'wp-time-slots-booking-form'),
            'formid'            => __('Calendar ID', 'wp-time-slots-booking-form'),
            'formname'          => __('Calendar Name', 'wp-time-slots-booking-form'),
        );
        if (isset($system_labels[$field_key])) {
            return $system_labels[$field_key];
        }
        return $plugin_obj->get_form_field_label($field_key, $form_fields);
    }
}

$active_form_title  = ($this->item != 0) ? $this->get_option("form_name", "Calendar") : __('All Calendars', 'wp-time-slots-booking-form');
$has_active_filters = (!empty($_GET["search"]) || !empty($_GET["dfrom"]) || !empty($_GET["dto"]));
?>

<style type="text/css">
/* Page-specific styles: Stats KPI Cards, Chart Grids & Field Distribution Table */
    .wpts-kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .wpts-kpi-card {
        background: var(--wpts-surface);
        border: 1px solid var(--wpts-border);
        border-radius: var(--wpts-radius);
        padding: 18px 20px;
        box-shadow: var(--wpts-shadow);
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .wpts-kpi-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--wpts-text-muted);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .wpts-kpi-value {
        font-size: 24px;
        font-weight: 800;
        color: var(--wpts-text);
        line-height: 1.2;
        margin-top: 2px;
    }
    .wpts-kpi-sub {
        font-size: 12px;
        color: var(--wpts-text-muted);
        margin-top: 2px;
    }
    .wpts-charts-3col {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }
    .wpts-charts-2col {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(460px, 1fr));
        gap: 24px;
        margin-bottom: 24px;
    }
    @media (max-width: 782px) {
        .wpts-charts-3col,
        .wpts-charts-2col {
            grid-template-columns: 1fr;
        }
    }
    .wpts-chart-box-sm {
        position: relative;
        width: 100%;
        height: 215px;
    }
    .wpts-chart-box-lg {
        position: relative;
        width: 100%;
        height: 265px;
    }
    .wpts-dist-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }
    .wpts-dist-table th {
        background: var(--wpts-bg);
        color: var(--wpts-text-muted);
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 12px 16px;
        border-bottom: 1px solid var(--wpts-border);
    }
    .wpts-dist-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13.5px;
        vertical-align: middle;
    }
    .wpts-dist-table tr:last-child td {
        border-bottom: none;
    }
    .wpts-rank-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 24px;
        background: var(--wpts-bg);
        border: 1px solid var(--wpts-border);
        border-radius: 6px;
        font-size: 11.5px;
        font-weight: 700;
        color: var(--wpts-text-muted);
    }
    .wpts-progress-row {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .wpts-progress-track {
        flex: 1;
        height: 10px;
        background: #f1f5f9;
        border-radius: 999px;
        overflow: hidden;
    }
    .wpts-progress-fill {
        height: 100%;
        border-radius: 999px;
        transition: width 0.4s ease;
    }
    .wpts-progress-pct {
        min-width: 48px;
        text-align: right;
        font-size: 12.5px;
        font-weight: 700;
        color: var(--wpts-text);
    }
    #cTable th { background: var(--wpts-bg); }
    #cTable td { vertical-align: top; }
</style>

<div class="wrap wpts-admin-wrap">

    <!-- Top SaaS Header -->
    <div class="wpts-header">
        <div class="wpts-header-left">
            <a href="<?php echo esc_url(admin_url('admin.php?page='.$this->menu_parameter)); ?>" class="wpts-back-btn">
                &larr; <?php esc_html_e('Return to the calendars list', 'wp-time-slots-booking-form'); ?>
            </a>
            <div class="wpts-title-group">
                <h1>
                    📊 <?php esc_html_e('Stats', 'wp-time-slots-booking-form'); ?> &mdash; <?php echo esc_html($active_form_title); ?>
                    <span class="wpts-badge"><?php echo esc_html($date_start); ?> &rarr; <?php echo esc_html($date_end); ?></span>
                </h1>
                <p class="wpts-subtitle"><?php echo esc_html($this->plugin_name); ?> &mdash; <?php esc_html_e('Incoming submission velocity, upcoming booked slot forecasts, daily revenue, and field analytics', 'wp-time-slots-booking-form'); ?></p>
            </div>
        </div>

        <div class="wpts-header-actions">
            <?php if ($this->item > 0) : ?>
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
            <?php endif; ?>
            <button type="button" class="wpts-btn wpts-btn-soft" onclick="do_dexapp_print();">
                🖨️ <?php esc_html_e('Print Stats', 'wp-time-slots-booking-form'); ?>
            </button>
        </div>
    </div>

    <!-- Executive KPI Summary Strip -->
    <div class="wpts-kpi-grid">
        <div class="wpts-kpi-card">
            <span class="wpts-kpi-label">
                <?php esc_html_e('Total Submissions', 'wp-time-slots-booking-form'); ?>
                <span>📥</span>
            </span>
            <span class="wpts-kpi-value"><?php echo number_format_i18n($total_submissions); ?></span>
            <span class="wpts-kpi-sub"><?php printf(esc_html__('Avg %s submissions / day', 'wp-time-slots-booking-form'), esc_html($daily_avg)); ?></span>
        </div>

        <div class="wpts-kpi-card">
            <span class="wpts-kpi-label">
                <?php esc_html_e('Booked Time Slots', 'wp-time-slots-booking-form'); ?>
                <span>🗓️</span>
            </span>
            <span class="wpts-kpi-value" style="color:var(--wpts-primary);"><?php echo number_format_i18n($total_booked_apps); ?></span>
            <span class="wpts-kpi-sub"><?php esc_html_e('Total appointments in range', 'wp-time-slots-booking-form'); ?></span>
        </div>

        <div class="wpts-kpi-card">
            <span class="wpts-kpi-label">
                <?php esc_html_e('Total Revenue', 'wp-time-slots-booking-form'); ?>
                <span>💳</span>
            </span>
            <span class="wpts-kpi-value" style="color:var(--wpts-success);"><?php echo number_format($total_revenue, 2); ?></span>
            <span class="wpts-kpi-sub"><?php printf(esc_html__('From %1$s to %2$s', 'wp-time-slots-booking-form'), esc_html($date_start), esc_html($date_end)); ?></span>
        </div>

        <div class="wpts-kpi-card">
            <span class="wpts-kpi-label">
                <?php esc_html_e('Peak Booking Hour', 'wp-time-slots-booking-form'); ?>
                <span>⏰</span>
            </span>
            <span class="wpts-kpi-value" style="font-size:20px;"><?php echo esc_html($peak_hour_lbl); ?></span>
            <span class="wpts-kpi-sub">
                <?php echo ($peak_hour_val > 0) ? sprintf(esc_html__('%d bookings in this hour', 'wp-time-slots-booking-form'), intval($peak_hour_val)) : esc_html__('No hourly activity yet', 'wp-time-slots-booking-form'); ?>
            </span>
        </div>

        <div class="wpts-kpi-card">
            <span class="wpts-kpi-label">
                <?php esc_html_e('Unique Customers', 'wp-time-slots-booking-form'); ?>
                <span>👤</span>
            </span>
            <span class="wpts-kpi-value"><?php echo number_format_i18n(count($unique_emails)); ?></span>
            <span class="wpts-kpi-sub"><?php esc_html_e('Distinct customer emails', 'wp-time-slots-booking-form'); ?></span>
        </div>
    </div>

    <?php do_action("cp_apphourbooking_do_action_reportstats"); ?>

    <?php require_once dirname( __FILE__ ).'/cp-full-stats.inc.php'; ?>

    <!-- Date Range Filter & Export Card -->
    <div class="wpts-section-banner">
        <div>
            <h2>🔍 <?php esc_html_e('Custom Date Range, Revenue & Field Analytics', 'wp-time-slots-booking-form'); ?></h2>
            <p><?php esc_html_e('Filter submissions by keyword or date range to inspect daily volume, hourly distribution, revenue, and field values.', 'wp-time-slots-booking-form'); ?></p>
        </div>
    </div>

    <div class="wpts-card">
        <div class="wpts-card-body">
            <form action="admin.php" method="get" id="wpts_stats_filter_form">
                <input type="hidden" name="page" value="<?php echo esc_attr($this->menu_parameter); ?>" />
                <input type="hidden" name="report" value="1" />
                <input type="hidden" name="field" value="<?php echo esc_attr($field_filter); ?>" />

                <div class="wpts-filter-bar">
                    <div class="wpts-filter-field" style="flex:1;min-width:180px;">
                        <label for="wpts_search"><?php esc_html_e('Search for', 'wp-time-slots-booking-form'); ?></label>
                        <input type="text" id="wpts_search" name="search" placeholder="<?php esc_attr_e('Keyword, email, slot...', 'wp-time-slots-booking-form'); ?>" value="<?php echo esc_attr((!empty($_GET["search"])?sanitize_text_field($_GET["search"]):'')); ?>" />
                    </div>

                    <div class="wpts-filter-field">
                        <label for="dfrom"><?php esc_html_e('From', 'wp-time-slots-booking-form'); ?></label>
                        <input autocomplete="off" type="text" id="dfrom" name="dfrom" style="width:135px;" placeholder="YYYY-MM-DD" value="<?php echo esc_attr((!empty($_GET["dfrom"])?sanitize_text_field($_GET["dfrom"]):'')); ?>" />
                    </div>

                    <div class="wpts-filter-field">
                        <label for="dto"><?php esc_html_e('To', 'wp-time-slots-booking-form'); ?></label>
                        <input autocomplete="off" type="text" id="dto" name="dto" style="width:135px;" placeholder="YYYY-MM-DD" value="<?php echo esc_attr((!empty($_GET["dto"])?sanitize_text_field($_GET["dto"]):'')); ?>" />
                    </div>

                    <div class="wpts-filter-field" style="min-width:190px;">
                        <label for="cal"><?php esc_html_e('Calendar / Item', 'wp-time-slots-booking-form'); ?></label>
                        <select id="cal" name="cal">
                            <?php if ($current_user_access) { ?>
                                <option value="0">[<?php esc_html_e('All Items','wp-time-slots-booking-form'); ?>]</option>
                            <?php } ?>
                            <?php
                            $myrows = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix.$this->table_items );
                            $saveditem = $this->item;
                            foreach ($myrows as $item)
                            {
                                $this->setId($item->id);
                                $options = unserialize($this->get_option('cp_user_access', serialize(array()))); 
                                $this->setId($saveditem);
                                if (!is_array($options))
                                    $options = array();        
                                if ($current_user_access || @in_array($current_user->ID, $options))
                                    echo '<option value="'.intval($item->id).'"'.(intval($item->id)==intval($this->item)?" selected":"").'>'.esc_html($item->form_name).'</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="wpts-filter-actions">
                        <button type="submit" name="ds" value="<?php esc_attr_e('Filter','wp-time-slots-booking-form'); ?>" class="wpts-btn wpts-btn-primary">
                            🔍 <?php esc_html_e('Filter', 'wp-time-slots-booking-form'); ?>
                        </button>
                        <button type="submit" name="<?php echo esc_attr($this->prefix); ?>_csv3" value="<?php esc_attr_e('Export to CSV','wp-time-slots-booking-form'); ?>" class="wpts-btn wpts-btn-secondary">
                            ⬇️ <?php esc_html_e('Export to CSV', 'wp-time-slots-booking-form'); ?>
                        </button>
                        <?php if ($has_active_filters) : ?>
                            <a href="<?php echo esc_url(admin_url('admin.php?page='.$this->menu_parameter.'&cal='.$this->item.'&report=1')); ?>" class="wpts-btn wpts-btn-secondary">
                                ✕ <?php esc_html_e('Reset', 'wp-time-slots-booking-form'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Quick Date Range Presets -->
                <div class="wpts-presets-row">
                    <span style="font-size:11.5px;font-weight:600;color:var(--wpts-text-muted);margin-right:4px;">⚡ <?php esc_html_e('Quick Ranges:', 'wp-time-slots-booking-form'); ?></span>
                    <button type="button" class="wpts-preset-pill" onclick="wptsSetDateRange(7)"><?php esc_html_e('Last 7 Days', 'wp-time-slots-booking-form'); ?></button>
                    <button type="button" class="wpts-preset-pill" onclick="wptsSetDateRange(30)"><?php esc_html_e('Last 30 Days', 'wp-time-slots-booking-form'); ?></button>
                    <button type="button" class="wpts-preset-pill" onclick="wptsSetDateRange(90)"><?php esc_html_e('Last 90 Days', 'wp-time-slots-booking-form'); ?></button>
                    <button type="button" class="wpts-preset-pill" onclick="wptsSetThisMonth()"><?php esc_html_e('This Month', 'wp-time-slots-booking-form'); ?></button>
                    <button type="button" class="wpts-preset-pill" onclick="wptsSetDateRange(0)"><?php esc_html_e('All Time', 'wp-time-slots-booking-form'); ?></button>
                </div>
            </form>
        </div>
    </div>

    <!-- Filtered Charts: Submissions per Day & Submissions per Hour -->
    <div class="wpts-charts-2col">  
        <div class="wpts-card" style="margin-bottom:0;">
            <div class="wpts-card-header">
                <div>
                    <h3 class="wpts-card-title">📅 <?php esc_html_e('Submissions per day in the selected date range', 'wp-time-slots-booking-form'); ?></h3>
                    <p class="wpts-card-desc"><?php esc_html_e('Days from', 'wp-time-slots-booking-form'); ?> <?php echo esc_html($date_start); ?> <?php esc_html_e('to', 'wp-time-slots-booking-form'); ?> <?php echo esc_html($date_end); ?></p>
                </div>
            </div>
            <div class="wpts-card-body">
                <div class="wpts-chart-box-lg">
                    <canvas id="chartperday" st="<?php echo esc_attr($date_start); ?>" et="<?php echo esc_attr($date_end); ?>" label="<?php esc_attr_e('Submissions', 'wp-time-slots-booking-form'); ?>" questions='[{"color":"#0d9488","values":[<?php echo esc_html($daily_messages); ?>]}]'></canvas>
                </div>
            </div>
        </div>  

        <div class="wpts-card" style="margin-bottom:0;">
            <div class="wpts-card-header">
                <div>
                    <h3 class="wpts-card-title">⏰ <?php esc_html_e('Total submissions per hour in the selected date range', 'wp-time-slots-booking-form'); ?></h3>
                    <p class="wpts-card-desc"><?php esc_html_e('Days from', 'wp-time-slots-booking-form'); ?> <?php echo esc_html($date_start); ?> <?php esc_html_e('to', 'wp-time-slots-booking-form'); ?> <?php echo esc_html($date_end); ?></p>
                </div>
            </div>
            <div class="wpts-card-body">
                <div class="wpts-chart-box-lg">
                    <canvas id="chartperhour" st="<?php echo esc_attr($date_start); ?>" et="<?php echo esc_attr($date_end); ?>" label="<?php esc_attr_e('Submissions', 'wp-time-slots-booking-form'); ?>" questions='[{"color":"#0284c7","values":[<?php echo esc_html($hourly_messages); ?>]}]'></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Per Day Chart -->
    <div class="wpts-card">
        <div class="wpts-card-header">
            <div>
                <h3 class="wpts-card-title">
                    💳 <?php esc_html_e('Revenue per day', 'wp-time-slots-booking-form'); ?>
                    <span class="wpts-badge" style="background:var(--wpts-success-bg);color:#065f46;border-color:#a7f3d0;">
                        <?php esc_html_e('Total:', 'wp-time-slots-booking-form'); ?> <?php echo number_format($total_revenue, 2); ?>
                    </span>
                </h3>
                <p class="wpts-card-desc"><?php esc_html_e('Days from', 'wp-time-slots-booking-form'); ?> <?php echo esc_html($date_start); ?> <?php esc_html_e('to', 'wp-time-slots-booking-form'); ?> <?php echo esc_html($date_end); ?></p>
            </div>
        </div>
        <div class="wpts-card-body">
            <div class="wpts-chart-box-lg">
                <canvas id="chartrevenue" st="<?php echo esc_attr($date_start); ?>" et="<?php echo esc_attr($date_end); ?>" label="<?php esc_attr_e('Revenue', 'wp-time-slots-booking-form'); ?>" questions='[{"color":"#10b981","values":[<?php echo esc_html($daily_revenue_str); ?>]}]'></canvas>
            </div>
        </div>
    </div>

    <!-- Field Value Distribution Card -->
    <div class="wpts-card" id="dex_printable_contents">
        <div class="wpts-card-header">
            <div>
                <h3 class="wpts-card-title">
                    🎯 <?php esc_html_e('Report of values for:', 'wp-time-slots-booking-form'); ?>
                    <span style="color:var(--wpts-primary);"><?php echo esc_html(wpts_pretty_field_label(sanitize_text_field($field_filter), $this, $form)); ?></span>
                </h3>
                <p class="wpts-card-desc">
                    <?php esc_html_e('Number of times that each value appears and percentage in relation to total submissions.', 'wp-time-slots-booking-form'); ?>
                    (<?php echo esc_html($date_start); ?> &rarr; <?php echo esc_html($date_end); ?>)
                </p>
            </div>

            <form action="admin.php" name="cfm_formrep" method="get" class="cpnopr" style="display:flex;align-items:center;gap:8px;margin:0;">
                <input type="hidden" name="page" value="<?php echo esc_attr($this->menu_parameter); ?>" />
                <input type="hidden" name="cal" value="<?php echo intval($this->item); ?>" />
                <input type="hidden" name="report" value="1" />
                <input type="hidden" name="search" value="<?php echo esc_attr((!empty($_GET["search"])?sanitize_text_field($_GET["search"]):'')); ?>" />
                <input type="hidden" name="dfrom" value="<?php echo esc_attr((!empty($_GET["dfrom"])?sanitize_text_field($_GET["dfrom"]):'')); ?>" />
                <input type="hidden" name="dto" value="<?php echo esc_attr((!empty($_GET["dto"])?sanitize_text_field($_GET["dto"]):'')); ?>" />
                <label for="wpts_field_selector" style="font-size:12.5px;font-weight:600;color:var(--wpts-text-muted);">
                    <?php esc_html_e('Select field for the report:', 'wp-time-slots-booking-form'); ?>
                </label>
                <select id="wpts_field_selector" name="field" onchange="document.cfm_formrep.submit();">
                    <?php
                    foreach ($fields as $item => $value)
                        echo '<option value="'.esc_attr($item).'"'.($field_filter==$item?' selected':'').'>'.esc_html(wpts_pretty_field_label($item, $this, $form)).'</option>';
                    ?>
                </select>
            </form>
        </div>

        <div class="wpts-card-body" style="padding:0;">
            <?php
            $arr = isset($fields[$field_filter]) && is_array($fields[$field_filter]) ? $fields[$field_filter] : array();
            arsort($arr, SORT_NUMERIC);
            $total = 0;
            foreach ($arr as $item => $value)
                $total += $value;

            if (count($arr) > 0 && $total > 0) :
            ?>
            <table class="wpts-dist-table">
                <thead>
                    <tr>
                        <th style="width:65px;"><?php esc_html_e('Rank', 'wp-time-slots-booking-form'); ?></th>
                        <th><?php esc_html_e('Value', 'wp-time-slots-booking-form'); ?></th>
                        <th style="width:150px;text-align:right;"><?php esc_html_e('Occurrences', 'wp-time-slots-booking-form'); ?></th>
                        <th style="width:320px;"><?php esc_html_e('Share of Total', 'wp-time-slots-booking-form'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $count = 0;
                    foreach ($arr as $item => $value) :
                        $raw_val = substr((string)$item, 1);
                        if ($field_filter === 'time' && is_numeric($raw_val)) {
                            $display_val = sprintf('%02d:00 – %02d:59', intval($raw_val), intval($raw_val));
                        } else if (trim($raw_val) === '') {
                            $display_val = __('(Empty / Not provided)', 'wp-time-slots-booking-form');
                        } else {
                            $display_val = (strlen($raw_val) > 75 ? substr($raw_val, 0, 75).'...' : $raw_val);
                        }
                        $pct = round($value / max(1, $total) * 100, 2);
                        $bar_color = $bar_colors[$count % count($bar_colors)];
                        $count++;
                    ?>
                    <tr>
                        <td><span class="wpts-rank-pill">#<?php echo intval($count); ?></span></td>
                        <td style="font-weight:600;color:var(--wpts-text);"><?php echo esc_html($display_val); ?></td>
                        <td style="text-align:right;font-weight:600;color:var(--wpts-text-muted);">
                            <?php echo esc_html($value . ' ' . __('times', 'wp-time-slots-booking-form')); ?>
                        </td>
                        <td>
                            <div class="wpts-progress-row">
                                <div class="wpts-progress-track">
                                    <div class="wpts-progress-fill" style="width:<?php echo esc_attr(min(100, round($pct))); ?>%;background-color:<?php echo esc_attr($bar_color); ?>;"></div>
                                </div>
                                <span class="wpts-progress-pct"><?php echo esc_html($pct); ?>%</span>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else : ?>
            <div style="text-align:center;padding:42px 20px;color:var(--wpts-text-muted);">
                <span style="font-size:32px;display:block;margin-bottom:8px;">📉</span>
                <strong style="color:var(--wpts-text);display:block;"><?php esc_html_e('No values found for this date range', 'wp-time-slots-booking-form'); ?></strong>
            </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<script type="text/javascript">
function wptsFormatYMD(d) {
    var m = (d.getMonth() + 1);
    var day = d.getDate();
    return d.getFullYear() + '-' + (m < 10 ? '0' : '') + m + '-' + (day < 10 ? '0' : '') + day;
}

function wptsSetDateRange(days) {
    var dfrom = document.getElementById('dfrom');
    var dto   = document.getElementById('dto');
    if (days === 0) {
        dfrom.value = '';
        dto.value   = '';
    } else {
        var end = new Date();
        var start = new Date();
        start.setDate(end.getDate() - (days - 1));
        dfrom.value = wptsFormatYMD(start);
        dto.value   = wptsFormatYMD(end);
    }
    document.getElementById('wpts_stats_filter_form').submit();
}

function wptsSetThisMonth() {
    var now = new Date();
    var start = new Date(now.getFullYear(), now.getMonth(), 1);
    document.getElementById('dfrom').value = wptsFormatYMD(start);
    document.getElementById('dto').value   = wptsFormatYMD(now);
    document.getElementById('wpts_stats_filter_form').submit();
}

function do_dexapp_print()
{
    w = window.open();
    w.document.write("<style>.cpnopr{display:none;}body{font-family:sans-serif;padding:20px;}table{border-collapse:collapse;border:1px solid #cbd5e1;width:100%;}th{border-bottom:2px solid #0f172a;text-align:left;padding:10px;background:#f8fafc;}td{padding:10px;border-bottom:1px solid #e2e8f0;}</style>"+document.getElementById('dex_printable_contents').innerHTML);
    w.print();
    w.close();
}

var $j = jQuery.noConflict();
$j(function() {
    $j("#dfrom").datepicker({ dateFormat: 'yy-mm-dd' });
    $j("#dto").datepicker({ dateFormat: 'yy-mm-dd' });
});
</script>

<script type="text/javascript">
var $ = jQuery.noConflict();
$j(document).ready(function(){
    if (typeof Chart === 'undefined') return;

    Chart.defaults.set({
        elements: {
            line: {
                borderWidth: 2.5,
                tension: 0.35
            },
            point: {
                radius: 3,
                hoverRadius: 5
            }
        }
    });
    Chart.defaults.set({
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: '#f1f5f9' },
                ticks: { precision: 0, color: '#64748b' }
            },
            x: {
                grid: { display: false },
                ticks: { color: '#64748b', maxRotation: 0, autoSkip: true, maxTicksLimit: 8 }
            }
        },
        plugins: {
            legend: { display: false }
        }
    });

    drawChartDaily($("#chartDailyIncoming"), '#0284c7', 'rgba(2, 132, 199, 0.12)');
    drawChartDaily($("#chartDaily"), '#0d9488', 'rgba(13, 148, 136, 0.14)');
    drawChartWeekly($("#chartWeeklyIncoming"), '#0284c7', 'rgba(2, 132, 199, 0.12)');
    drawChartWeekly($("#chartWeekly"), '#0d9488', 'rgba(13, 148, 136, 0.14)');
    drawChartMonthly($("#chartMonthlyIncoming"), '#0284c7', 'rgba(2, 132, 199, 0.12)');
    drawChartMonthly($("#chartMonthly"), '#0d9488', 'rgba(13, 148, 136, 0.14)');

    function drawChartMonthly(obj, strokeCol, fillCol)
    {
        if (!obj.length || !obj.attr("x")) return;
        function getLabels(arr)
        {       
            for (var i = 0; i < arr.length; i++)
                arr[i] = $.datepicker.formatDate(((i==0 || (i==arr.length - 1))?"M yy":"M"), $.datepicker.parseDate("yymmdd", arr[i]+"01"));
            return arr;
        }
        new Chart(obj, {
            type: 'line',
            data: {
                labels: getLabels(obj.attr("x").split(',')),
                datasets: [{
                    label: obj.attr("label"),
                    data: obj.attr("y").split(','),
                    startdate: obj.attr("x").split(','),
                    borderColor: strokeCol,
                    backgroundColor: fillCol,
                    fill: true
                }]
            },         
            options: { 
                plugins: {
                    tooltip: {
                        callbacks: {
                            title: function(context) {
                                return $.datepicker.formatDate("M yy", $.datepicker.parseDate("yymmdd", context[0].dataset.startdate[context[0].parsed.x]+"01"));
                            }
                        }
                    }
                }
            }   
        });    
    }

    function drawChartWeekly(obj, strokeCol, fillCol)
    {
        if (!obj.length || !obj.attr("x")) return;
        function getLabels(arr)
        {       
            for (var i = 0; i < arr.length; i++)
                arr[i] = ((i==0 || (i==arr.length - 1)) ? arr[i].substring(0, 4) + " W" : "W") + arr[i].substring(4, 6);
            return arr;
        }
        new Chart(obj, {
            type: 'line',
            data: {
                labels: getLabels(obj.attr("x").split(',')),
                datasets: [{
                    label: obj.attr("label"),
                    data: obj.attr("y").split(','),
                    startdate: obj.attr("x").split(','),
                    borderColor: strokeCol,
                    backgroundColor: fillCol,
                    fill: true
                }]
            },         
            options: { 
                plugins: {
                    tooltip: {
                        callbacks: {
                            title: function(context) {
                                var s = context[0].dataset.startdate[context[0].parsed.x];
                                return s.substring(0, 4) + " Week #" + s.substring(4, 6);
                            }
                        }
                    }
                }
            }   
        });    
    }

    function drawChartDaily(obj, strokeCol, fillCol)
    {
        if (!obj.length || !obj.attr("x")) return;
        function getLabels(arr)
        {       
            for (var i = 0; i < arr.length; i++)
                arr[i] = $.datepicker.formatDate(((i==0 || (i==arr.length - 1))?"M dd":"dd"), $.datepicker.parseDate("yymmdd", arr[i]));
            return arr;
        }
        new Chart(obj, {
            type: 'line',
            data: {
                labels: getLabels(obj.attr("x").split(',')),
                datasets: [{
                    label: obj.attr("label"),
                    data: obj.attr("y").split(','),
                    startdate: obj.attr("x").split(','),
                    borderColor: strokeCol,
                    backgroundColor: fillCol,
                    fill: true
                }]
            },         
            options: { 
                plugins: {
                    tooltip: {
                        callbacks: {
                            title: function(context) {
                                return $.datepicker.formatDate("M d, yy", $.datepicker.parseDate("yymmdd", context[0].dataset.startdate[context[0].parsed.x]));
                            }
                        }
                    }
                }
            }   
        });    
    }           
            
    chartperday($("#chartperday"), 'rgba(13, 148, 136, 0.14)'); 
    chartperday($("#chartrevenue"), 'rgba(16, 185, 129, 0.14)');

    function chartperday(obj, fillCol)
    {           
        if (!obj.length || !obj.attr("questions")) return;
        var data = jQuery.parseJSON(obj.attr("questions"));
        var st = $.datepicker.parseDate("yy-mm-dd", obj.attr("st"));
        var et = $.datepicker.parseDate("yy-mm-dd", obj.attr("et"));
        var l = [];
        while (st <= et)
        {
            l[l.length] = $.datepicker.formatDate("yy-mm-dd", st);
            st.setDate(st.getDate() + 1);
        }        
        new Chart(obj, {
            type: 'line',
            data: {
                labels: l,
                datasets: [{
                    label: obj.attr("label"),
                    data: data[0].values,
                    borderColor: data[0].color,
                    backgroundColor: fillCol || data[0].color,
                    fill: true
                }]
            }
        });    
    }    

    chartperhour($("#chartperhour"));
    function chartperhour(obj)
    {           
        if (!obj.length || !obj.attr("questions")) return;
        var data = jQuery.parseJSON(obj.attr("questions"));
        var l = [];
        for (var i = 0; i < 24; i++)
            l[l.length] = (i < 10 ? '0' : '') + i + ':00';
        new Chart(obj, {
            type: 'bar',
            data: {
                labels: l,
                datasets: [{
                    label: obj.attr("label"),
                    data: data[0].values,
                    backgroundColor: 'rgba(2, 132, 199, 0.75)',
                    borderColor: '#0284c7',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            }
        });    
    }
});
</script>