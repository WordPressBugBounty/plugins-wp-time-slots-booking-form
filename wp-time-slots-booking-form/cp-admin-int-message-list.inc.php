<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wpdb;

$this->item = intval((isset($_GET["cal"]) ? $_GET["cal"] : 0));

$current_user = wp_get_current_user();
$current_user_access = current_user_can('manage_options');

if ( !is_admin() || (!$current_user_access && !@in_array($current_user->ID, unserialize($this->get_option("cp_user_access",serialize(array()))))))
{
    echo 'Direct access not allowed.';
    exit;
}

if (empty($_GET["p"]))
    $current_page = 0;
else
    $current_page = intval($_GET["p"]);
if (!$current_page) $current_page = 1;
$records_per_page = 50;  

$message = "";

if (isset($_GET['statusmark']) && $_GET['statusmark'] != '')
{
    $this->verify_nonce( sanitize_text_field($_GET["anonce"]), 'cptslotsb_actions_booking');
    for ($i=0; $i<=$records_per_page; $i++)
    if (isset($_GET['c'.$i]) && $_GET['c'.$i] != '')   
    {
        $this->update_status( intval($_GET['c'.$i]), sanitize_text_field($_GET['sbmi']) );        
    }
    $message = __('Marked items status updated','wp-time-slots-booking-form');
}
else if (isset($_GET['resend']) && $_GET['resend'] != '')
{
    $this->verify_nonce( sanitize_text_field($_GET["anonce"]), 'cptslotsb_actions_booking');
    if (method_exists($this, 'ready_to_go_reservation')) {
        $this->ready_to_go_reservation( intval($_GET['resend']), '', array(), true);        
    }
    $message = __('Notification emails resent for the booking','wp-time-slots-booking-form');
}
else if (isset($_GET['delmark']) && $_GET['delmark'] != '')
{
    $this->verify_nonce(sanitize_text_field($_GET["anonce"]), 'cptslotsb_actions_booking');
    for ($i=0; $i<=$records_per_page; $i++)
    if (isset($_GET['c'.$i]) && $_GET['c'.$i] != '')   
    {
        do_action( 'cptslotsb_item_deleted', intval($_GET['c'.$i]) );
        $wpdb->query( $wpdb->prepare('DELETE FROM `'.$wpdb->prefix.$this->table_messages.'` WHERE id=%d', intval($_GET['c'.$i])) );       
    }
    $message = __('Marked items deleted','wp-time-slots-booking-form');
}
else if (isset($_GET['del']) && $_GET['del'] == 'all')
{    
    $this->verify_nonce(sanitize_text_field($_GET["anonce"]), 'cptslotsb_actions_booking');
    if ($this->item == '' || $this->item == '0')
        $wpdb->query('DELETE FROM `'.$wpdb->prefix.$this->table_messages.'`');           
    else
        $wpdb->query($wpdb->prepare('DELETE FROM `'.$wpdb->prefix.$this->table_messages.'` WHERE formid=%d', $this->item));           
    $message = __('All items deleted','wp-time-slots-booking-form');  
} 
else if (isset($_GET['lu']) && $_GET['lu'] != '')
{
    $this->verify_nonce(sanitize_text_field($_GET["anonce"]), 'cptslotsb_actions_booking');
    $myrows = $wpdb->get_results( $wpdb->prepare("SELECT * FROM ".$wpdb->prefix.$this->table_messages." WHERE id=%d", intval($_GET['lu'])) );
    $params = unserialize($myrows[0]->posted_data);
    $params["paid"] = sanitize_text_field($_GET["status"]);
    $params["payment_type"] = __('Manually updated','wp-time-slots-booking-form');  
    $wpdb->query( $wpdb->prepare('UPDATE `'.$wpdb->prefix.$this->table_messages.'` SET posted_data=%s WHERE id=%d', serialize($params), intval($_GET['lu'])) );
    $message = __('Item updated','wp-time-slots-booking-form');        
}
else if (isset($_GET['ld']) && $_GET['ld'] != '')
{
    $this->verify_nonce(sanitize_text_field($_GET["anonce"]), 'cptslotsb_actions_booking');
    do_action( 'cptslotsb_item_deleted', intval($_GET['ld']) );
    $wpdb->query( $wpdb->prepare('DELETE FROM `'.$wpdb->prefix.$this->table_messages.'` WHERE id=%d', intval($_GET['ld'])) );
    $message = __('Item deleted','wp-time-slots-booking-form');
}
else if (isset($_GET['ud']) && $_GET['ud'] != '')
{
    $this->verify_nonce(sanitize_text_field($_GET["anonce"]), 'cptslotsb_actions_booking');
    if (isset($_GET["udidx"]))
        $this->update_status(sanitize_text_field($_GET['ud']), sanitize_text_field($_GET['status']), intval($_GET["udidx"]));
    else  
        $this->update_status(intval($_GET['ud']), sanitize_text_field($_GET['status']));
    $message = __('Status updated','wp-time-slots-booking-form');
}

if ($this->item != 0)
    $myform = $wpdb->get_results( $wpdb->prepare('SELECT * FROM '.$wpdb->prefix.$this->table_items .' WHERE id=%d' ,$this->item) );

$rawfrom = (isset($_GET["dfrom"]) ? sanitize_text_field($_GET["dfrom"]) : '');
$rawto   = (isset($_GET["dto"]) ? sanitize_text_field(@$_GET["dto"]) : '');
if ($this->get_option('date_format', 'mm/dd/yy') == 'dd/mm/yy')
{
    $rawfrom = str_replace('/','.',$rawfrom);
    $rawto   = str_replace('/','.',$rawto);
}                                                                                

$cond = '';
if (!empty($_GET["search"]) && $_GET["search"] != '') $cond .= " AND (data like '%".esc_sql(sanitize_text_field($_GET["search"]))."%' OR posted_data LIKE '%".esc_sql(sanitize_text_field($_GET["search"]))."%')";
if ($rawfrom != '') $cond .= " AND (`time` >= '".esc_sql( date("Y-m-d",strtotime($rawfrom)))."')";
if ($rawto != '') $cond .= " AND (`time` <= '".esc_sql(date("Y-m-d",strtotime($rawto)))." 23:59:59')";
if ($this->item != 0) $cond .= " AND formid=".intval($this->item);

$events_query = "SELECT count(id) as ck FROM ".$wpdb->prefix.$this->table_messages." WHERE 1=1 ".$cond." ORDER BY `time` DESC";
$eventscount = $wpdb->get_results( $events_query );
$total_items = isset($eventscount[0]) ? intval($eventscount[0]->ck) : 0;
$total_pages = ceil($total_items / $records_per_page);

$events_query = "SELECT * FROM ".$wpdb->prefix.$this->table_messages." WHERE 1=1 ".$cond." ORDER BY `time` DESC LIMIT ".intval(($current_page-1)*$records_per_page).",".intval($records_per_page);

/**
 * Allows modify the query of messages, passing the query as parameter
 * returns the new query
 */
$events_query = apply_filters( 'cptslotsb_messages_query', $events_query );
$events = $wpdb->get_results( $events_query );

$form_data = json_decode($this->cleanJSON($this->get_option('form_structure', CP_TSLOTSBOOK_DEFAULT_form_structure)));
$formdata  = !empty($form_data[0]) ? $form_data[0] : array();

$nonce       = wp_create_nonce( 'cptslotsb_actions_booking' );
$reply_nonce = wp_create_nonce( 'cptslotsb_reply_nonce' );
$ai_reply_enabled = ( get_option( 'cptslotsb_ai_reply_enabled', '0' ) === '1' );
$has_active_filters = (!empty($_GET["search"]) || !empty($_GET["dfrom"]) || !empty($_GET["dto"]));
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

    /* Top Header Card */
    .wpts-header {
        background: var(--wpts-surface);
        border: 1px solid var(--wpts-border);
        border-radius: var(--wpts-radius);
        padding: 20px 24px;
        margin-bottom: 20px;
        box-shadow: var(--wpts-shadow);
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        background: linear-gradient(135deg, #f0fdfa 0%, #ffffff 65%);
    }
    .wpts-header-left {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }
    .wpts-back-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        background: #ffffff;
        border: 1px solid var(--wpts-border);
        border-radius: var(--wpts-radius-sm);
        color: var(--wpts-text);
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .wpts-back-btn:hover {
        background: var(--wpts-primary-light);
        border-color: var(--wpts-primary-border);
        color: var(--wpts-primary-hover);
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
        gap: 6px;
        background: var(--wpts-primary-light);
        color: var(--wpts-primary-hover);
        font-size: 12px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 999px;
        border: 1px solid var(--wpts-primary-border);
    }
    .wpts-subtitle {
        margin: 4px 0 0;
        font-size: 13px;
        color: var(--wpts-text-muted);
    }
    .wpts-header-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
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

    /* Cards */
    .wpts-card {
        background: var(--wpts-surface);
        border: 1px solid var(--wpts-border);
        border-radius: var(--wpts-radius);
        box-shadow: var(--wpts-shadow);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .wpts-card-body {
        padding: 20px 24px;
    }

    /* Filter Bar */
    .wpts-filter-bar {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 14px;
    }
    .wpts-filter-field {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    .wpts-filter-field label {
        font-size: 12px;
        font-weight: 600;
        color: var(--wpts-text-muted);
    }
    .wpts-filter-actions {
        margin-left: auto;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    /* Inputs & Selects */
    .wpts-admin-wrap input[type="text"],
    .wpts-admin-wrap input[type="search"],
    .wpts-admin-wrap input[type="date"],
    .wpts-admin-wrap input[type="email"],
    .wpts-admin-wrap select,
    .wpts-admin-wrap textarea {
        padding: 7px 11px;
        font-size: 13px;
        line-height: 1.4;
        color: var(--wpts-text);
        background-color: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: var(--wpts-radius-sm);
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
        box-shadow: none;
        min-height: 36px;
    }
    .wpts-admin-wrap select {
        padding-right: 32px !important;
        background-position: right 8px center;
    }
    .wpts-admin-wrap input:focus,
    .wpts-admin-wrap select:focus,
    .wpts-admin-wrap textarea:focus {
        border-color: var(--wpts-primary);
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.14);
        outline: none;
    }

    /* Toolbars & Pagination */
    .wpts-toolbar {
        padding: 12px 20px;
        background: #fcfcfd;
        border-bottom: 1px solid var(--wpts-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .wpts-toolbar-footer {
        border-top: 1px solid var(--wpts-border);
        border-bottom: none;
    }
    .wpts-bulk-group {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .ahb-wp-pagination {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--wpts-text-muted);
        font-size: 13px;
    }
    .ahb-wp-pagination .displaying-num {
        font-weight: 600;
    }
    .ahb-wp-pagination .pagination-links {
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .ahb-wp-pagination .pagination-links .button {
        margin: 0;
        padding: 0 8px;
        min-width: 32px;
        min-height: 32px;
        line-height: 30px;
        text-align: center;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--wpts-border);
        border-radius: 6px;
        background: #ffffff;
        color: var(--wpts-text);
        text-decoration: none;
        font-weight: 600;
        transition: all 0.15s ease;
    }
    .ahb-wp-pagination .pagination-links .button.disabled {
        border-color: var(--wpts-border);
        color: #cbd5e1;
        background: var(--wpts-bg);
        cursor: default;
    }
    .ahb-wp-pagination .pagination-links .button:hover:not(.disabled) {
        background: var(--wpts-primary-light);
        border-color: var(--wpts-primary-border);
        color: var(--wpts-primary-hover);
    }
    .ahb-wp-pagination .paging-input {
        margin: 0 4px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .ahb-wp-pagination .paging-input input {
        width: 48px !important;
        min-height: 32px;
        text-align: center;
        padding: 2px 6px;
        font-weight: 600;
    }

    /* Orders Table */
    .wpts-table-wrap {
        overflow-x: auto;
    }
    .ahb-orders-list {
        width: 100%;
        border-collapse: collapse;
        border: none !important;
        box-shadow: none !important;
    }
    .ahb-orders-list thead th,
    .ahb-orders-list thead td {
        background: var(--wpts-bg);
        color: var(--wpts-text-muted);
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 13px 16px;
        border-bottom: 1px solid var(--wpts-border);
    }
    .ahb-orders-list tbody th.check-column,
    .ahb-orders-list tbody td {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: top;
        font-size: 13.5px;
        background: #ffffff;
    }
    .ahb-orders-list tbody tr:hover td,
    .ahb-orders-list tbody tr:hover th.check-column {
        background: #f8fafc;
    }
    .ahb-orders-list tbody tr.cptslotsb_cancelled td {
        background: #fffafa;
        color: #94a3b8;
    }
    .ahb-orders-list tbody tr.cptslotsb_cancelled .ahb-appointment-badge {
        background: #fef2f2;
        border-color: #fecaca;
        color: #b91c1c;
        text-decoration: line-through;
    }

    .wpts-id-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 26px;
        padding: 0 8px;
        background: var(--wpts-bg);
        border: 1px solid var(--wpts-border);
        border-radius: 6px;
        font-weight: 700;
        font-size: 12px;
        color: var(--wpts-text-muted);
    }
    .wpts-email-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: var(--wpts-accent-light);
        color: var(--wpts-accent);
        border: 1px solid var(--wpts-accent-border);
        padding: 4px 10px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 12.5px;
        text-decoration: none;
        word-break: break-all;
    }
    .wpts-email-pill:hover {
        background: var(--wpts-accent);
        color: #ffffff;
    }

    /* Appointment Slot Badges & Message Content */
    .ahb-appointment-header {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 8px;
    }
    .ahb-appointment-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--wpts-primary-light);
        border: 1px solid var(--wpts-primary-border);
        color: var(--wpts-primary-hover);
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 12.5px;
        font-weight: 600;
    }
    .ahb-appointment-badge .dashicons {
        font-size: 15px;
        width: 15px;
        height: 15px;
        line-height: 15px;
    }
    .wpts-details-body {
        font-size: 13px;
        line-height: 1.55;
        color: #334155;
    }
    .wpts-replied-badge {
        display: block;
        margin-top: 8px;
        padding: 8px 12px;
        background: var(--wpts-success-bg);
        border: 1px solid #a7f3d0;
        border-left: 3px solid var(--wpts-success);
        color: #065f46;
        font-size: 12.5px;
        border-radius: 6px;
    }

    /* Paid Status Badges */
    .wpts-badge-paid {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        background: var(--wpts-success-bg);
        color: #065f46;
        border: 1px solid #a7f3d0;
        border-radius: 999px;
        font-weight: 700;
        font-size: 11.5px;
    }
    .wpts-badge-unpaid {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        background: var(--wpts-bg);
        color: var(--wpts-text-muted);
        border: 1px solid var(--wpts-border);
        border-radius: 999px;
        font-weight: 600;
        font-size: 11.5px;
    }

    /* Row Actions */
    .wpts-action-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        margin-top: 8px;
    }
    .wpts-mini-action {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 9px;
        border-radius: 6px;
        font-size: 11.5px;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid var(--wpts-border);
        background: #ffffff;
        color: var(--wpts-text);
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .wpts-mini-action:hover {
        border-color: var(--wpts-primary);
        color: var(--wpts-primary);
        background: var(--wpts-primary-light);
    }
    .wpts-mini-action.reply-btn {
        background: var(--wpts-primary);
        border-color: var(--wpts-primary);
        color: #ffffff;
    }
    .wpts-mini-action.reply-btn:hover {
        background: var(--wpts-primary-hover);
        color: #ffffff;
    }
    .wpts-mini-action.delete-btn {
        color: #dc2626;
        border-color: #fecaca;
        background: var(--wpts-danger-bg);
    }
    .wpts-mini-action.delete-btn:hover {
        background: var(--wpts-danger);
        border-color: var(--wpts-danger);
        color: #ffffff;
    }

    /* Reply Modal & Explicit AI Opt-In Box */
    #cptslotsb-reply-modal {
        display: none;
        position: fixed;
        z-index: 99999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.55);
        backdrop-filter: blur(4px);
        overflow-y: auto;
        padding: 20px;
    }
    .cptslotsb-modal-content {
        background: #ffffff;
        width: 100%;
        max-width: 640px;
        margin: 3% auto;
        border-radius: var(--wpts-radius);
        border: 1px solid var(--wpts-border);
        box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.2);
        overflow: hidden;
        animation: wptsSlideDown 0.2s ease-out;
    }
    .cptslotsb-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 18px 24px;
        background: #fcfcfd;
        border-bottom: 1px solid var(--wpts-border);
    }
    .cptslotsb-modal-header h2 {
        margin: 0 !important;
        font-size: 17px;
        font-weight: 700;
        color: var(--wpts-text);
    }
    .cptslotsb-modal-close {
        background: var(--wpts-bg);
        border: 1px solid var(--wpts-border);
        border-radius: 6px;
        width: 30px;
        height: 30px;
        font-size: 18px;
        line-height: 1;
        color: var(--wpts-text-muted);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .cptslotsb-modal-close:hover {
        background: var(--wpts-danger-bg);
        color: var(--wpts-danger);
        border-color: #fecaca;
    }
    .cptslotsb-modal-body {
        padding: 22px 24px;
    }
    .cptslotsb-modal-field {
        margin-bottom: 16px;
    }
    .cptslotsb-modal-field:last-child {
        margin-bottom: 0;
    }
    .cptslotsb-modal-field label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 6px;
        color: var(--wpts-text);
    }
    .cptslotsb-modal-field input[type="email"],
    .cptslotsb-modal-field input[type="text"],
    .cptslotsb-modal-field textarea {
        width: 100%;
    }

    /* AI Opt-In Box & Switch */
    .cptslotsb-ai-box {
        background: linear-gradient(135deg, #f0fdfa 0%, #f8fafc 100%);
        padding: 16px;
        border: 1px solid var(--wpts-primary-border);
        border-left: 4px solid var(--wpts-primary);
        border-radius: var(--wpts-radius-sm);
        margin-bottom: 16px;
    }
    .cptslotsb-ai-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }
    .cptslotsb-ai-toggle-wrap {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        font-weight: 600;
        color: var(--wpts-text);
        cursor: pointer;
        user-select: none;
    }
    .cptslotsb-switch {
        position: relative;
        display: inline-block;
        width: 38px;
        height: 20px;
    }
    .cptslotsb-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .cptslotsb-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #94a3b8;
        transition: .2s;
        border-radius: 20px;
    }
    .cptslotsb-slider:before {
        position: absolute;
        content: "";
        height: 14px;
        width: 14px;
        left: 3px;
        bottom: 3px;
        background-color: #fff;
        transition: .2s;
        border-radius: 50%;
    }
    .cptslotsb-switch input:checked + .cptslotsb-slider {
        background-color: var(--wpts-primary);
    }
    .cptslotsb-switch input:checked + .cptslotsb-slider:before {
        transform: translateX(18px);
    }
    .cptslotsb-ai-notice {
        margin: 8px 0 0;
        font-size: 12px;
        line-height: 1.5;
        color: var(--wpts-text-muted);
    }
    .cptslotsb-ai-controls {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid var(--wpts-primary-border);
    }
    .cptslotsb-ai-row {
        display: flex;
        gap: 8px;
        align-items: center;
        margin-top: 6px;
    }
    .cptslotsb-ai-row input[type="text"] {
        flex: 1;
    }

    .cptslotsb-modal-footer {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
        padding: 16px 24px;
        background: #fcfcfd;
        border-top: 1px solid var(--wpts-border);
    }
    #cptslotsb-reply-status {
        margin-right: auto;
        font-size: 13px;
        font-weight: 600;
    }
</style>

<script type="text/javascript">
// AJAX Status Update
function cp_UpsItem(id) {
    var selectBox = document.getElementById("sb" + id);
    var status = selectBox.options[selectBox.selectedIndex].value;
    var url = 'admin.php?page=<?php echo esc_js($this->menu_parameter); ?>&anonce=<?php echo esc_js($nonce); ?>&cal=<?php echo intval($_GET["cal"]); ?>&list=1&ud=' + id + '&status=' + encodeURIComponent(status) + '&r=' + Math.random();
    
    selectBox.disabled = true;
    
    jQuery.get(url, function() {
        selectBox.disabled = false;
        var tr = jQuery(selectBox).closest('tr');
        var lowerStatus = status.toLowerCase();
        
        if (lowerStatus === 'cancelled' || lowerStatus === 'cancelled by customer' || lowerStatus === 'rejected') {
            tr.addClass('cptslotsb_cancelled');
        } else {
            tr.removeClass('cptslotsb_cancelled');
        }
        
        var check = jQuery('<span style="color:#10b981; margin-left:6px; font-weight:bold;">&#10003;</span>');
        jQuery(selectBox).after(check);
        check.fadeOut(2000, function() { jQuery(this).remove(); });
    });
}

// AJAX Toggle Payment
function cp_updateMessageItem(id, status, linkElement) {
    var url = 'admin.php?page=<?php echo esc_js($this->menu_parameter); ?>&anonce=<?php echo esc_js($nonce); ?>&cal=<?php echo intval($_GET["cal"]); ?>&list=1&status=' + status + '&lu=' + id + '&r=' + Math.random();
    
    var $link = jQuery(linkElement);
    var $cell = $link.closest('tr').find('td').eq(4);
    
    $link.css('opacity', '0.5');

    jQuery.get(url, function() {
        $link.css('opacity', '1');
        var newStatus = (status == '1') ? '0' : '1';
        $link.attr('onclick', 'cp_updateMessageItem(' + id + ',' + newStatus + ', this);');

        if (status == '1') {
            $cell.html('<span class="wpts-badge-paid">✓ <?php echo esc_js(__('Paid', 'wp-time-slots-booking-form')); ?></span><div style="font-size:11px; color:#64748b; margin-top:4px;"><?php echo esc_js(__('Manually updated','wp-time-slots-booking-form')); ?></div>');
        } else {
            $cell.html('<span class="wpts-badge-unpaid"><?php echo esc_js(__('Not Paid', 'wp-time-slots-booking-form')); ?></span>');
        }
    });
}

function cp_resendMessageItem(id, element) {
    if (confirm('<?php echo esc_js(__('Are you sure that you want to resend the notification emails for this item?','wp-time-slots-booking-form')); ?>')) {
        var url = 'admin.php?page=<?php echo esc_js($this->menu_parameter); ?>&anonce=<?php echo esc_js($nonce); ?>&cal=<?php echo intval($_GET["cal"]); ?>&list=1&resend='+id+'&r='+Math.random();
        var $link = jQuery(element);
        var originalText = $link.html();
        
        $link.text('<?php echo esc_js(__('Sending...','wp-time-slots-booking-form')); ?>').css({'pointer-events': 'none', 'opacity': '0.5'});
        
        jQuery.get(url, function() {
            $link.text('✓ <?php echo esc_js(__('Sent!','wp-time-slots-booking-form')); ?>').css('color', '#059669');
            setTimeout(function() {
                $link.html(originalText).css({'pointer-events': 'auto', 'opacity': '1', 'color': ''});
            }, 2000);
        });
    }
} 

function cp_deleteMessageItem(id, element) {
    if (confirm('<?php echo esc_js(__('Are you sure that you want to delete this item?','wp-time-slots-booking-form')); ?>')) {
        var url = 'admin.php?page=<?php echo esc_js($this->menu_parameter); ?>&anonce=<?php echo esc_js($nonce); ?>&cal=<?php echo intval($_GET["cal"]); ?>&list=1&ld='+id+'&r='+Math.random();
        var $tr = jQuery(element).closest('tr');
        
        $tr.css('opacity', '0.4');
        
        jQuery.get(url, function() {
            $tr.fadeOut(350, function() {
                jQuery(this).remove();
            });
        });
    }
}

function cp_deletemarked() {
    if (confirm('<?php echo esc_js(__('Are you sure that you want to delete the marked items?', 'wp-time-slots-booking-form')); ?>'))
        document.dex_table_form.submit();
}

function cp_statusmarked() {
    if (confirm('<?php echo esc_js(__('Are you sure that you want to change the status of the marked items?', 'wp-time-slots-booking-form')); ?>')) {                
        document.dex_table_form.delmark.value = '';
        document.dex_table_form.statusmark.value = '1';
        var status = document.getElementById("statusbox_markeditems").options[document.getElementById("statusbox_markeditems").selectedIndex].value;
        document.dex_table_form.sbmi.value = status;        
        document.dex_table_form.submit();
    }
}  

function cp_deleteall() {
    if (confirm('<?php echo esc_js(__('Are you sure that you want to delete ALL bookings for this form?', 'wp-time-slots-booking-form')); ?>')) {
        if (confirm('<?php echo esc_js(__('Please note that this action cannot be undone. ALL THE BOOKINGS of this form will be DELETED. Are you sure that you want to delete ALL bookings for this form?', 'wp-time-slots-booking-form')); ?>'))
            document.location = 'admin.php?page=<?php echo esc_js($this->menu_parameter); ?>&cal=<?php echo intval($_GET["cal"]); ?>&list=1&del=all&anonce=<?php echo esc_js($nonce); ?>&r='+Math.random();
    }
}

function cp_markall() {
    var ischecked = document.getElementById("cpcontrolck").checked;
    <?php for ($i=0; $i<$records_per_page; $i++) if (isset($events[$i])) { ?>
    if (document.forms.dex_table_form.c<?php echo intval($i); ?>) {
        document.forms.dex_table_form.c<?php echo intval($i); ?>.checked = ischecked;
    }
    <?php } ?>
}

function do_dexapp_print() {
    w = window.open();
    w.document.write("<style>.ahbnoprint, .check-column, .cpnopr{display:none;} table{border-collapse:collapse;width:100%;font-family:sans-serif;} th, td{border:1px solid #ddd;padding:10px;text-align:left;vertical-align:top;} th{background-color:#f4f4f4;}</style>"+document.getElementById('dex_printable_contents').innerHTML);
    w.print();
    w.close();
}
</script>

<div class="wrap wpts-admin-wrap">

    <?php if ($message && !isset($_GET['ud'])) : ?>
    <div id="setting-error-settings_updated" class="wpts-toast">
        <span>✅ <strong><?php echo esc_html($message); ?></strong></span>
        <button type="button" class="wpts-btn wpts-btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="this.parentElement.style.display='none'">Dismiss</button>
    </div>
    <?php endif; ?>

    <!-- Top Header -->
    <div class="wpts-header">
        <div class="wpts-header-left">
            <a href="<?php echo esc_url(admin_url('admin.php?page='.$this->menu_parameter)); ?>" class="wpts-back-btn">
                &larr; <?php esc_html_e('Calendars List', 'wp-time-slots-booking-form'); ?>
            </a>
            <div class="wpts-title-group">
                <h1>
                    📋 <?php esc_html_e('Booking Orders', 'wp-time-slots-booking-form'); ?> &mdash;
                    <?php if ($this->item != 0 && !empty($myform)) echo esc_html($myform[0]->form_name); else esc_html_e('All Forms', 'wp-time-slots-booking-form'); ?>
                    <span class="wpts-badge"><?php echo number_format_i18n($total_items); ?> <?php echo ($total_items === 1 ? __('Booking', 'wp-time-slots-booking-form') : __('Bookings', 'wp-time-slots-booking-form')); ?></span>
                </h1>
                <p class="wpts-subtitle"><?php echo esc_html($this->plugin_name); ?> &mdash; <?php esc_html_e('Manage booked time slots, approval statuses, payments, and customer replies', 'wp-time-slots-booking-form'); ?></p>
            </div>
        </div>

        <div class="wpts-header-actions">
            <?php if ($this->item > 0) : ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page='.$this->menu_parameter.'&cal='.$this->item.'&schedule=1&calendarview=1')); ?>" class="wpts-btn wpts-btn-accent">
                    📅 <?php esc_html_e('Schedule View', 'wp-time-slots-booking-form'); ?>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page='.$this->menu_parameter.'&cal='.$this->item.'&addbk=1')); ?>" class="wpts-btn wpts-btn-secondary">
                    ➕ <?php esc_html_e('Add Booking', 'wp-time-slots-booking-form'); ?>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page='.$this->menu_parameter.'&cal='.$this->item.'&report=1')); ?>" class="wpts-btn wpts-btn-secondary">
                    📊 <?php esc_html_e('Stats', 'wp-time-slots-booking-form'); ?>
                </a>
                <?php if ($current_user_access) : ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page='.$this->menu_parameter.'&cal='.$this->item)); ?>" class="wpts-btn wpts-btn-secondary">
                    ⚙️ <?php esc_html_e('Edit Calendar', 'wp-time-slots-booking-form'); ?>
                </a>
                <?php endif; ?>
            <?php endif; ?>
            <button type="button" class="wpts-btn wpts-btn-soft" onclick="do_dexapp_print();">
                🖨️ <?php esc_html_e('Print List', 'wp-time-slots-booking-form'); ?>
            </button>
        </div>
    </div>

    <!-- Filter & Search Card -->
    <div class="wpts-card">
        <div class="wpts-card-body">
            <form action="admin.php" method="get" class="wpts-filter-bar">
                <input type="hidden" name="page" value="<?php echo esc_attr($this->menu_parameter); ?>" />
                <input type="hidden" name="list" value="1" />
                <input type="hidden" name="anonce" value="<?php echo esc_attr($nonce); ?>" />

                <div class="wpts-filter-field" style="min-width:200px;">
                    <label for="cal"><?php esc_html_e('Calendar / Form', 'wp-time-slots-booking-form'); ?></label>
                    <select id="cal" name="cal" class="selectmh">
                        <option value="0">[<?php esc_html_e('All Items','wp-time-slots-booking-form'); ?>]</option>
                        <?php
                        $myrows = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix.$this->table_items ); 
                        foreach ($myrows as $item) {
                            echo '<option value="'.esc_attr($item->id).'"'.(intval($item->id)==intval($this->item)?" selected":"").'>'.esc_html($item->form_name).'</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="wpts-filter-field" style="flex:1;min-width:180px;">
                    <label for="wpts_search"><?php esc_html_e('Search keyword', 'wp-time-slots-booking-form'); ?></label>
                    <input type="search" id="wpts_search" name="search" placeholder="<?php esc_attr_e('Customer email, slot, details...','wp-time-slots-booking-form'); ?>" value="<?php echo esc_attr((!empty($_GET["search"])?sanitize_text_field($_GET["search"]):'')); ?>" />
                </div>

                <div class="wpts-filter-field">
                    <label for="dfrom"><?php esc_html_e('Submitted From', 'wp-time-slots-booking-form'); ?></label>
                    <input autocomplete="off" type="date" id="dfrom" name="dfrom" value="<?php echo esc_attr((!empty($_GET["dfrom"])?sanitize_text_field($_GET["dfrom"]):'')); ?>" />
                </div>

                <div class="wpts-filter-field">
                    <label for="dto"><?php esc_html_e('Submitted To', 'wp-time-slots-booking-form'); ?></label>
                    <input autocomplete="off" type="date" id="dto" name="dto" value="<?php echo esc_attr((!empty($_GET["dto"])?sanitize_text_field($_GET["dto"]):'')); ?>" />
                </div>

                <div class="wpts-filter-actions">
                    <button type="submit" name="ds" value="<?php esc_attr_e('Filter','wp-time-slots-booking-form'); ?>" class="wpts-btn wpts-btn-primary">
                        🔍 <?php esc_html_e('Filter', 'wp-time-slots-booking-form'); ?>
                    </button>
                    <button type="submit" name="<?php echo esc_attr($this->prefix); ?>_csv" value="<?php esc_attr_e('Export CSV','wp-time-slots-booking-form'); ?>" class="wpts-btn wpts-btn-secondary">
                        ⬇️ <?php esc_html_e('Export CSV', 'wp-time-slots-booking-form'); ?>
                    </button>
                    <?php if ($has_active_filters) : ?>
                        <a href="<?php echo esc_url(admin_url('admin.php?page='.$this->menu_parameter.'&cal='.$this->item.'&list=1')); ?>" class="wpts-btn wpts-btn-secondary">
                            ✕ <?php esc_html_e('Reset', 'wp-time-slots-booking-form'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Bookings Table Card -->
    <div class="wpts-card" id="dex_printable_contents">
        <form name="dex_table_form" id="dex_table_form" action="admin.php" method="get">
            <input type="hidden" name="page" value="<?php echo esc_attr($this->menu_parameter); ?>" />
            <input type="hidden" name="cal" value="<?php echo intval($_GET["cal"]); ?>" />
            <input type="hidden" name="list" value="1" />
            <input type="hidden" name="delmark" value="1" />
            <input type="hidden" name="statusmark" value="" />
            <input type="hidden" name="sbmi" value="" /> 
            <input type="hidden" name="anonce" value="<?php echo esc_attr($nonce); ?>" />

            <!-- Top Bulk Actions & Pagination Toolbar -->
            <div class="wpts-toolbar ahbnoprint">
                <div class="wpts-bulk-group">
                    <span style="font-size:12.5px;font-weight:600;color:var(--wpts-text-muted);"><?php esc_html_e('Bulk Status:', 'wp-time-slots-booking-form'); ?></span>
                    <?php $this->render_status_box('statusbox_markeditems', ''); ?>
                    <button class="wpts-btn wpts-btn-soft" type="button" name="pbutton" onclick="cp_statusmarked();">
                        ✓ <?php esc_html_e('Apply Status', 'wp-time-slots-booking-form'); ?>
                    </button>
                    <button class="wpts-btn wpts-btn-secondary" type="button" name="pbutton" onclick="cp_deletemarked();">
                        🗑️ <?php esc_html_e('Delete Selected', 'wp-time-slots-booking-form'); ?>
                    </button>
                </div>

                <div class="tablenav-pages ahb-wp-pagination<?php if($total_pages <= 1) echo ' one-page'; ?>">
                    <?php if ( $total_pages > 0 ) : ?>
                        <span class="displaying-num"><?php echo esc_html( sprintf( _n( '%s item', '%s items', $eventscount[0]->ck, 'wp-time-slots-booking-form' ), number_format_i18n( $eventscount[0]->ck ) ) ); ?></span>
                        
                        <?php 
                        $base_url = 'admin.php?page='.esc_attr($this->menu_parameter).'&cal='.intval($this->item).'&list=1&dfrom='.urlencode(sanitize_text_field((!empty($_GET["dfrom"])?sanitize_text_field($_GET["dfrom"]):''))).'&dto='.urlencode(sanitize_text_field((!empty($_GET["dto"])?sanitize_text_field($_GET["dto"]):''))).'&search='.urlencode((!empty($_GET["search"])?sanitize_text_field($_GET["search"]):''));
                        ?>
                        <span class="pagination-links">
                            <?php if ( $current_page == 1 ) : ?>
                                <span class="tablenav-pages-navspan button disabled" aria-hidden="true">&laquo;</span>
                                <span class="tablenav-pages-navspan button disabled" aria-hidden="true">&lsaquo;</span>
                            <?php else : ?>
                                <a class="first-page button" href="<?php echo esc_url($base_url . '&p=1'); ?>"><span class="screen-reader-text"><?php esc_html_e( 'First page', 'wp-time-slots-booking-form' ); ?></span><span aria-hidden="true">&laquo;</span></a>
                                <a class="prev-page button" href="<?php echo esc_url($base_url . '&p=' . ($current_page - 1)); ?>"><span class="screen-reader-text"><?php esc_html_e( 'Previous page', 'wp-time-slots-booking-form' ); ?></span><span aria-hidden="true">&lsaquo;</span></a>
                            <?php endif; ?>

                            <span class="paging-input">
                                <label for="current-page-selector" class="screen-reader-text"><?php esc_html_e( 'Current Page', 'wp-time-slots-booking-form' ); ?></label>
                                <input class="current-page" id="current-page-selector" type="text" name="p" value="<?php echo esc_attr( $current_page ); ?>" size="<?php echo intval(strlen( (string) $total_pages )); ?>" aria-describedby="table-paging" onkeydown="if(event.keyCode==13) { window.location.href='<?php echo esc_url_raw($base_url); ?>&p='+this.value; return false; }">
                                <span class="tablenav-paging-text"> <?php esc_html_e( 'of', 'wp-time-slots-booking-form' ); ?> <span class="total-pages"><?php echo esc_html( number_format_i18n( $total_pages ) ); ?></span></span>
                            </span>

                            <?php if ( $current_page == $total_pages ) : ?>
                                <span class="tablenav-pages-navspan button disabled" aria-hidden="true">&rsaquo;</span>
                                <span class="tablenav-pages-navspan button disabled" aria-hidden="true">&raquo;</span>
                            <?php else : ?>
                                <a class="next-page button" href="<?php echo esc_url($base_url . '&p=' . ($current_page + 1)); ?>"><span class="screen-reader-text"><?php esc_html_e( 'Next page', 'wp-time-slots-booking-form' ); ?></span><span aria-hidden="true">&rsaquo;</span></a>
                                <a class="last-page button" href="<?php echo esc_url($base_url . '&p=' . $total_pages); ?>"><span class="screen-reader-text"><?php esc_html_e( 'Last page', 'wp-time-slots-booking-form' ); ?></span><span aria-hidden="true">&raquo;</span></a>
                            <?php endif; ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="wpts-table-wrap ahb-orderssection-container">
                <table class="wp-list-table widefat fixed striped ahb-orders-list">
                    <thead>
                    <tr>
                        <td id="cb" class="manage-column column-cb check-column" style="width:42px;text-align:center;">
                            <input type="checkbox" name="cpcontrolck" id="cpcontrolck" value="" onclick="cp_markall();" />
                        </td>
                        <th scope="col" class="manage-column column-id" style="width:70px;"><?php esc_html_e('ID','wp-time-slots-booking-form'); ?></th>
                        <th scope="col" class="manage-column column-date" style="width:150px;"><?php esc_html_e('Submission Date','wp-time-slots-booking-form'); ?></th>
                        <th scope="col" class="manage-column column-email" style="width:210px;"><?php esc_html_e('Email','wp-time-slots-booking-form'); ?></th>
                        <th scope="col" class="manage-column column-message"><?php esc_html_e('Booked Slots & Details','wp-time-slots-booking-form'); ?></th>
                        <th scope="col" class="manage-column column-status" style="width:130px;text-align:center;"><?php esc_html_e('Paid Status','wp-time-slots-booking-form'); ?></th>
                        <th scope="col" class="manage-column column-options cpnopr" style="width:270px;"><?php esc_html_e('Status & Actions','wp-time-slots-booking-form'); ?></th>
                    </tr>
                    </thead>
                    <tbody id="the-list">
                    <?php if (empty($events)) : ?>
                    <tr>
                        <td colspan="7" style="text-align:center;padding:48px 20px;color:var(--wpts-text-muted);">
                            <span style="font-size:34px;display:block;margin-bottom:8px;">🗓️</span>
                            <strong style="font-size:15px;color:var(--wpts-text);display:block;margin-bottom:4px;"><?php esc_html_e('No booking orders found', 'wp-time-slots-booking-form'); ?></strong>
                            <span><?php esc_html_e('No bookings match your current filter criteria.', 'wp-time-slots-booking-form'); ?></span>
                        </td>
                    </tr>
                    <?php endif; ?>

                    <?php for ($i=0; $i<$records_per_page; $i++) if (isset($events[$i])) { 
                        $posted_data = unserialize($events[$i]->posted_data);	
                        $cancelled = false;
                        $status = '';
                        if (isset($posted_data["apps"]) && is_array($posted_data["apps"])) {
                            for($k=0; $k<count($posted_data["apps"]); $k++) {
                                if ($posted_data["apps"][$k]["cancelled"] != '')
                                {
                                    $cancelled = true;
                                    $status = $posted_data["apps"][$k]["cancelled"];
                                }
                            }
                        }

                        $raw_data = $events[$i]->data;
                        $blocked_indicator = isset($this->blocked_by_admin_indicator) ? $this->blocked_by_admin_indicator : 'BLOCKED@BY.ADMIN';
                        $display_email = ($events[$i]->notifyto != $blocked_indicator) ? sanitize_email($events[$i]->notifyto) : '';
                        if (empty($display_email) && $events[$i]->notifyto != $blocked_indicator && preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $raw_data, $matches)) {
                            $display_email = sanitize_email($matches[0]);
                        }
                    ?>
                    <tr class="<?php if ($cancelled && $status != 'Attended') { echo 'cptslotsb_cancelled '; } ?>" valign="top">
                        <th scope="row" class="check-column" style="text-align:center;">
                            <input type="checkbox" name="c<?php echo intval($i); ?>" value="<?php echo intval($events[$i]->id); ?>" />
                        </th>
                        <td>
                            <span class="wpts-id-pill">#<?php echo esc_html($events[$i]->id); ?></span>
                        </td>
                        <td>
                            <strong style="display:block;color:var(--wpts-text);"><?php echo esc_html($this->format_date(substr($events[$i]->time,0,16))); ?></strong>
                            <span style="font-size:12px;color:var(--wpts-text-muted);"><?php echo esc_html(date("H:i",strtotime($events[$i]->time))); ?></span>
                        </td>
                        <td>
                            <?php if ($display_email) : ?>
                                <a class="wpts-email-pill" href="mailto:<?php echo esc_attr($display_email); ?>">
                                    ✉️ <?php echo esc_html($display_email); ?>
                                </a>
                            <?php else : ?>
                                <span style="color:var(--wpts-text-muted);"><?php echo esc_html($events[$i]->notifyto); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="ahbmessage">
                            <div class="ahbmessagemw">
                                <?php 
                                $reply_parts = preg_split('/\n--- Replied on (.*?) ---\n/s', trim($events[$i]->data), -1, PREG_SPLIT_DELIM_CAPTURE);
                                $base_message = isset($reply_parts[0]) ? $reply_parts[0] : $events[$i]->data;

                                $data = str_replace("\n","<br />",str_replace('<','&lt;',$base_message));		        	        
                                if (is_array($posted_data)) {
                                    foreach ($posted_data as $item => $value) {
                                        if (strpos($item,"_url") && $value != '' && is_array($value)) {		                
                                            $data = str_replace($posted_data[str_replace("_url","",$item)],'<a href="'.esc_url($value[0]).'" target="_blank">'.esc_html($posted_data[str_replace("_url","",$item)]).'</a><br />',$data);  		                
                                        }   
                                    }
                                }
                                $data = str_replace("&lt;img ","<img ", $data);  

                                echo '<div class="ahb-appointment-header">';
                                $data = str_replace('<br /><br />', '</div><div class="wpts-details-body">',$this->filter_allowed_tags(apply_filters( 'cpappb_booking_orders_item', $data, $posted_data ))); 

                                $appts = "";
                                if (isset($posted_data["apps"]) && is_array($posted_data["apps"])) {
                                    for($k=0; $k<count($posted_data["apps"]); $k++) {
                                        $app = $posted_data["apps"][$k];
                                        $appts .= '<div class="ahb-appointment-badge">' .
                                                       '<span class="dashicons dashicons-clock"></span>' .
                                                       '<span class="ahb-time">'.$this->format_date($posted_data["apps"][$k]["date"]).' '.esc_html($posted_data["apps"][$k]["slot"]).'</span>' .
                                                       '<span class="ahb-service">'.($app["quantity1"] + $app["quantity2"] + $app["quantity3"] + $app["quantity4"] + $app["quantity5"] > 1 || $app["quantity1"] == 0 ? 
                                                          " (".(
                                                          $this->getQuantityLabel("quantity1",$app["field"],$formdata).$app["quantity1"]
                                                          .($app["quantity2"] || $app["quantity3"] || $app["quantity4"] || $app["quantity5"]?', '.$this->getQuantityLabel("quantity2",$app["field"],$formdata).$app["quantity2"]:'')
                                                          .($app["quantity3"] || $app["quantity4"] || $app["quantity5"]?', '.$this->getQuantityLabel("quantity3",$app["field"],$formdata).$app["quantity3"]:'')
                                                          .($app["quantity4"] || $app["quantity5"]?', '.$this->getQuantityLabel("quantity4",$app["field"],$formdata).$app["quantity4"]:'')
                                                          .($app["quantity5"]?', '.$this->getQuantityLabel("quantity5",$app["field"],$formdata).$app["quantity5"]:'')
                                                          ).")\n"
                                                          :
                                                          "\n").'</span>' .
                                                   '</div>';
                                    }
                                }

                                echo $appts.'</div><div style="display:none">'.$data; // phpcs:ignore WordPress.Security.EscapeOutput
                                echo '</div>';

                                if (count($reply_parts) > 1) {
                                    for ($r = 1; $r < count($reply_parts); $r += 2) {
                                        $r_date = isset($reply_parts[$r]) ? $reply_parts[$r] : '';
                                        $r_body = isset($reply_parts[$r + 1]) ? trim($reply_parts[$r + 1]) : '';
                                        echo '<div class="wpts-replied-badge">';
                                        echo '<strong>✓ ' . esc_html__('Replied on', 'wp-time-slots-booking-form') . ' ' . esc_html($r_date) . ':</strong><br />';
                                        echo nl2br(esc_html($r_body));
                                        echo '</div>';
                                    }
                                }
                                ?>
                            </div>
                        </td>
                        <td align="center" class="wpts-paid-cell">
                            <?php 
                            $is_paid = (!empty($posted_data["paid"]) && $posted_data["paid"]=='1');
                            if ($is_paid) {
                                echo '<span class="wpts-badge-paid" title="'.esc_attr__('Paid','wp-time-slots-booking-form').'">✓ '.esc_html__('Paid','wp-time-slots-booking-form').'</span>';
                                $clean_type = $this->clean_sanitize($posted_data["payment_type"]);
                                if (!empty($clean_type)) {
                                    echo '<div style="font-size: 11px; color: #64748b; margin-top: 4px;">'.esc_html($clean_type).'</div>';
                                }
                            } else {
                                echo '<span class="wpts-badge-unpaid" title="'.esc_attr__('Not Paid','wp-time-slots-booking-form').'">'.esc_html__('Not Paid','wp-time-slots-booking-form').'</span>';
                            }
                            ?>
                        </td>    
                        <td class="cpnopr">
                            <div>
                                <?php $this->render_status_box('sb'.intval($events[$i]->id), $status); ?>
                            </div>
                            <div class="wpts-action-pills">
                                <?php if ($events[$i]->notifyto != $blocked_indicator) { ?>
                                    <a href="javascript:void(0);" class="wpts-mini-action reply-btn"
                                       data-id="<?php echo intval($events[$i]->id); ?>"
                                       data-email="<?php echo esc_attr($display_email); ?>"
                                       data-message="<?php echo esc_attr(trim($raw_data)); ?>"
                                       onclick="cp_openReplyModal(this);">
                                        💬 <?php esc_html_e('Reply','wp-time-slots-booking-form'); ?>
                                    </a>
                                <?php } ?>
                                <a href="javascript:void(0);" class="wpts-mini-action" onclick="cp_updateMessageItem(<?php echo intval($events[$i]->id); ?>,<?php echo ($is_paid ? '0' : '1'); ?>, this);">
                                    💳 <?php esc_html_e('Toggle Payment','wp-time-slots-booking-form'); ?>
                                </a>
                                <a href="javascript:void(0);" class="wpts-mini-action" onclick="cp_resendMessageItem(<?php echo intval($events[$i]->id); ?>, this);">
                                    ✉️ <?php esc_html_e('Resend Email','wp-time-slots-booking-form'); ?>
                                </a>
                                <a href="javascript:void(0);" class="wpts-mini-action delete-btn" onclick="cp_deleteMessageItem(<?php echo intval($events[$i]->id); ?>, this);">
                                    🗑️ <?php esc_html_e('Delete','wp-time-slots-booking-form'); ?>
                                </a>
                            </div> 
                        </td>
                    </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>

            <!-- Bottom Bulk Actions Toolbar -->
            <div class="wpts-toolbar wpts-toolbar-footer ahbnoprint">
                <div class="wpts-bulk-group">
                    <button class="wpts-btn wpts-btn-secondary" type="button" onclick="do_dexapp_print();">
                        🖨️ <?php esc_html_e('Print List','wp-time-slots-booking-form'); ?>
                    </button>
                </div>
                <div class="wpts-bulk-group">
                    <button class="wpts-btn wpts-btn-danger" type="button" name="pbutton" onclick="cp_deleteall();">
                        ⚠️ <?php esc_html_e('Delete ALL Bookings','wp-time-slots-booking-form'); ?>
                    </button>
                </div>
            </div>
        </form>
    </div>

</div>

<script type="text/javascript">
var $j = jQuery.noConflict();
$j(function() {
    // Bind AJAX onchange listener for status dropdowns
    $j("select[id^='sb']").each(function() {
        var idAttr = $j(this).attr('id');
        if (/^sb\d+$/.test(idAttr)) {
            $j(this).on('change', function() {
                var id = idAttr.replace('sb', '');
                cp_UpsItem(id);
            });
        }
    });
});
</script>

<!-- AI-Assisted Reply Modal with Explicit Opt-In & Privacy Notice -->
<div id="cptslotsb-reply-modal" onclick="if(event.target === this) this.style.display='none';">
    <div class="cptslotsb-modal-content">
        <div class="cptslotsb-modal-header">
            <h2>💬 <?php esc_html_e('Reply to Booking / Message','wp-time-slots-booking-form'); ?></h2>
            <button type="button" class="cptslotsb-modal-close" onclick="document.getElementById('cptslotsb-reply-modal').style.display='none';">&times;</button>
        </div>

        <div class="cptslotsb-modal-body">
            <input type="hidden" id="cptslotsb-reply-msg-id" value="" />
            
            <div class="cptslotsb-modal-field">
                <label for="cptslotsb-reply-to"><?php esc_html_e('To:','wp-time-slots-booking-form'); ?></label>
                <input type="email" id="cptslotsb-reply-to" placeholder="customer@example.com" />
            </div>

            <div class="cptslotsb-modal-field">
                <label for="cptslotsb-reply-original"><?php esc_html_e('Original Submission:','wp-time-slots-booking-form'); ?></label>
                <textarea id="cptslotsb-reply-original" rows="3" style="background:var(--wpts-bg);color:var(--wpts-text-muted);" readonly></textarea>
            </div>

            <div class="cptslotsb-ai-box">
                <div class="cptslotsb-ai-header">
                    <strong style="color:var(--wpts-text);font-size:13.5px;">✨ <?php esc_html_e('AI Reply Assistant','wp-time-slots-booking-form'); ?></strong>
                    <label class="cptslotsb-ai-toggle-wrap" for="cptslotsb-ai-toggle">
                        <span id="cptslotsb-ai-toggle-label"><?php echo $ai_reply_enabled ? 'On' : 'Off'; ?></span>
                        <span class="cptslotsb-switch">
                            <input type="checkbox" id="cptslotsb-ai-toggle" <?php checked( $ai_reply_enabled ); ?> onchange="cp_toggleAiAssistant(this);" />
                            <span class="cptslotsb-slider"></span>
                        </span>
                    </label>
                </div>

                <p class="cptslotsb-ai-notice">
                    <?php esc_html_e('Privacy Notice: When enabled, clicking "Generate with AI" sends your site name, the Original Submission shown above, and your instructions to the external AI service text.pollinations.ai to generate a draft reply.','wp-time-slots-booking-form'); ?>
                </p>

                <div id="cptslotsb-ai-controls" class="cptslotsb-ai-controls" style="<?php echo $ai_reply_enabled ? '' : 'display:none;'; ?>">
                    <label for="cptslotsb-ai-instructions" style="font-weight:600;font-size:12.5px;color:var(--wpts-text);display:block;"><?php esc_html_e('AI Assistant Instructions (optional):','wp-time-slots-booking-form'); ?></label>
                    <div class="cptslotsb-ai-row">
                        <input type="text" id="cptslotsb-ai-instructions" placeholder="<?php esc_attr_e('e.g., Confirm the booking for Tuesday at 10 AM and thank them','wp-time-slots-booking-form'); ?>" <?php disabled( ! $ai_reply_enabled ); ?> />
                        <button type="button" class="wpts-btn wpts-btn-soft" id="cptslotsb-btn-ai" onclick="cp_generateAiReply();" <?php disabled( ! $ai_reply_enabled ); ?>>
                            ✨ <?php esc_html_e('Generate with AI','wp-time-slots-booking-form'); ?>
                        </button>
                    </div>
                </div>
            </div>

            <div class="cptslotsb-modal-field">
                <label for="cptslotsb-reply-subject"><?php esc_html_e('Subject:','wp-time-slots-booking-form'); ?></label>
                <input type="text" id="cptslotsb-reply-subject" value="Re: <?php echo esc_attr($this->get_option('fp_subject', (defined('CP_TSLOTSBOOK_DEFAULT_fp_subject') ? CP_TSLOTSBOOK_DEFAULT_fp_subject : 'Booking'))); ?>" />
            </div>

            <div class="cptslotsb-modal-field">
                <label for="cptslotsb-reply-body"><?php esc_html_e('Your Reply:','wp-time-slots-booking-form'); ?></label>
                <textarea id="cptslotsb-reply-body" rows="6" placeholder="<?php esc_attr_e('Write your reply here...','wp-time-slots-booking-form'); ?>"></textarea>
            </div>
        </div>

        <div class="cptslotsb-modal-footer">
            <span id="cptslotsb-reply-status"></span>
            <button type="button" class="wpts-btn wpts-btn-secondary" onclick="document.getElementById('cptslotsb-reply-modal').style.display='none';">
                <?php esc_html_e('Cancel','wp-time-slots-booking-form'); ?>
            </button>
            <button type="button" class="wpts-btn wpts-btn-primary" id="cptslotsb-btn-send" onclick="cp_sendReplyEmail();">
                🚀 <?php esc_html_e('Send Reply','wp-time-slots-booking-form'); ?>
            </button>
        </div>
    </div>
</div>

<script type="text/javascript">
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        var modal = document.getElementById('cptslotsb-reply-modal');
        if (modal && modal.style.display === 'block') {
            modal.style.display = 'none';
        }
    }
});

function cp_openReplyModal(link) {
    document.getElementById('cptslotsb-reply-msg-id').value = link.getAttribute('data-id');
    document.getElementById('cptslotsb-reply-to').value = link.getAttribute('data-email');
    document.getElementById('cptslotsb-reply-original').value = link.getAttribute('data-message');
    document.getElementById('cptslotsb-reply-body').value = '';
    document.getElementById('cptslotsb-reply-status').innerText = '';
    document.getElementById('cptslotsb-reply-modal').style.display = 'block';
}

function cp_toggleAiAssistant(checkbox) {
    var enabled = checkbox.checked;
    var controls = document.getElementById('cptslotsb-ai-controls');
    var label = document.getElementById('cptslotsb-ai-toggle-label');
    var input = document.getElementById('cptslotsb-ai-instructions');
    var btn = document.getElementById('cptslotsb-btn-ai');
    var status = document.getElementById('cptslotsb-reply-status');

    controls.style.display = enabled ? 'block' : 'none';
    input.disabled = !enabled;
    btn.disabled = !enabled;
    label.innerText = enabled ? 'On' : 'Off';

    $j.post(ajaxurl, {
        action: 'cptslotsb_toggle_ai_reply',
        nonce: '<?php echo esc_js($reply_nonce); ?>',
        cal_id: '<?php echo intval($this->item); ?>',
        enabled: enabled ? 1 : 0
    }, function(response) {
        if (!response || !response.success) {
            checkbox.checked = !enabled;
            controls.style.display = !enabled ? 'block' : 'none';
            input.disabled = enabled;
            btn.disabled = enabled;
            label.innerText = !enabled ? 'On' : 'Off';
            status.style.color = '#dc2626';
            status.innerText = (response && response.data) ? response.data : 'Could not save setting.';
        }
    }).fail(function() {
        checkbox.checked = !enabled;
        controls.style.display = !enabled ? 'block' : 'none';
        input.disabled = enabled;
        btn.disabled = enabled;
        label.innerText = !enabled ? 'On' : 'Off';
        status.style.color = '#dc2626';
        status.innerText = 'Request failed while saving AI preference.';
    });
}

function cp_generateAiReply() {
    var toggle = document.getElementById('cptslotsb-ai-toggle');
    var status = document.getElementById('cptslotsb-reply-status');

    if (!toggle || !toggle.checked) {
        status.style.color = '#dc2626';
        status.innerText = '<?php echo esc_js(__('Please enable the AI Reply Assistant first.','wp-time-slots-booking-form')); ?>';
        return;
    }

    var original = document.getElementById('cptslotsb-reply-original').value;
    var instructions = document.getElementById('cptslotsb-ai-instructions').value;
    var btn = document.getElementById('cptslotsb-btn-ai');

    btn.disabled = true;
    btn.innerText = '<?php echo esc_js(__('Generating...','wp-time-slots-booking-form')); ?>';
    status.style.color = '#0d9488';
    status.innerText = '<?php echo esc_js(__('Contacting AI...','wp-time-slots-booking-form')); ?>';

    $j.post(ajaxurl, {
        action: 'cptslotsb_generate_ai_reply',
        nonce: '<?php echo esc_js($reply_nonce); ?>',
        cal_id: '<?php echo intval($this->item); ?>',
        original_message: original,
        instructions: instructions
    }, function(response) {
        btn.disabled = false;
        btn.innerText = '✨ <?php echo esc_js(__('Generate with AI','wp-time-slots-booking-form')); ?>';
        if (response.success) {
            document.getElementById('cptslotsb-reply-body').value = response.data.reply;
            status.style.color = '#059669';
            status.innerText = '✓ <?php echo esc_js(__('Draft generated!','wp-time-slots-booking-form')); ?>';
        } else {
            status.style.color = '#dc2626';
            status.innerText = response.data || '<?php echo esc_js(__('Failed to generate reply.','wp-time-slots-booking-form')); ?>';
        }
    }).fail(function() {
        btn.disabled = false;
        btn.innerText = '✨ <?php echo esc_js(__('Generate with AI','wp-time-slots-booking-form')); ?>';
        status.style.color = '#dc2626';
        status.innerText = '<?php echo esc_js(__('Request failed.','wp-time-slots-booking-form')); ?>';
    });
}

function cp_sendReplyEmail() {
    var btn = document.getElementById('cptslotsb-btn-send');
    var status = document.getElementById('cptslotsb-reply-status');

    btn.disabled = true;
    status.style.color = '#0d9488';
    status.innerText = '<?php echo esc_js(__('Sending...','wp-time-slots-booking-form')); ?>';

    $j.post(ajaxurl, {
        action: 'cptslotsb_send_reply',
        nonce: '<?php echo esc_js($reply_nonce); ?>',
        message_id: document.getElementById('cptslotsb-reply-msg-id').value,
        to_email: document.getElementById('cptslotsb-reply-to').value,
        subject: document.getElementById('cptslotsb-reply-subject').value,
        reply_body: document.getElementById('cptslotsb-reply-body').value
    }, function(response) {
        btn.disabled = false;
        if (response.success) {
            status.style.color = '#059669';
            status.innerText = '✓ ' + response.data;
            setTimeout(function() {
                location.reload();
            }, 900);
        } else {
            status.style.color = '#dc2626';
            status.innerText = response.data || '<?php echo esc_js(__('Error sending email.','wp-time-slots-booking-form')); ?>';
        }
    }).fail(function() {
        btn.disabled = false;
        status.style.color = '#dc2626';
        status.innerText = '<?php echo esc_js(__('Server error while sending.','wp-time-slots-booking-form')); ?>';
    });
}
</script>