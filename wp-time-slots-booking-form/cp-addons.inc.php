<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !is_admin() ) 
{
    echo 'Direct access not allowed.';
    exit;
}

global $wpdb, $cptslotsb_addons_active_list, $cptslotsb_addons_objs_list;

$message = "";

if( isset( $_GET[ 'b' ] ) && $_GET[ 'b' ] == 1 )
{
    $this->verify_nonce ($_GET["anonce"], 'cptslotsb_actions_list');
	// Save the option for active addons
	delete_option( 'cptslotsb_addons_active_list' );
	if( !empty( $_GET[ 'cptslotsb_addons_active_list' ] ) && is_array( $_GET[ 'cptslotsb_addons_active_list' ] ) ) 
	{
        $tags = array_map( 'sanitize_text_field', $_GET[ 'cptslotsb_addons_active_list' ] );
		update_option( 'cptslotsb_addons_active_list', $tags );
	}	
	
	// Get the list of active addons
	$cptslotsb_addons_active_list = get_option( 'cptslotsb_addons_active_list', array() );
    $message = "Add Ons settings updated";
}

$nonce = wp_create_nonce( 'cptslotsb_actions_list' );

?>
<style>
	.clear{clear:both;}
	.ahb-addons-container {
		border: 1px solid #e6e6e6;
		padding: 20px;
		border-radius: 3px;
		-webkit-box-flex: 1;
		flex: 1;
		margin: 1em 1em 1em 0;
		min-width: 200px;
		background: white;
		position:relative;
	}
	.ahb-addons-container h2{margin:0 0 20px 0;padding:0;}
	.ahb-addon{border-bottom: 1px solid #efefef;padding: 10px 0;}
	.ahb-addon:first-child{border-top: 1px solid #efefef;}
	.ahb-addon:last-child{border-bottom: 0;}
	.ahb-addon label{font-weight:600;}
	.ahb-addon p{font-style:italic;margin:5px 0 0 0;}
	.ahb-first-button{margin-right:10px !important;}

    .ahb-buttons-container{margin:1em 1em 1em 0;}
    .ahb-return-link{float:right;}

	.ahb-disabled-addons {
		background: #f9f9f9;
	}
	.ahb-addons-container h2{margin-left:30px;}
	.ahb-disabled-addons *{
		color:#888888;
	}
	.ahb-disabled-addons input{
		pointer-events: none !important;
	}

	/** For Ribbon **/
	.ribbon {
		position: absolute;
		left: -5px; top: -5px;
		z-index: 1;
		overflow: hidden;
		width: 75px; height: 75px;
		text-align: right;
	}
	.ribbon span {
		font-size: 10px;
		font-weight: bold;
		color: #FFF;
		text-transform: uppercase;
		text-align: center;
		line-height: 20px;
		transform: rotate(-45deg);
		-webkit-transform: rotate(-45deg);
		width: 100px;
		display: block;
		background: #79A70A;
		background: linear-gradient(#2989d8 0%, #1e5799 100%);
		box-shadow: 0 3px 10px -5px rgba(0, 0, 0, 1);
		position: absolute;
		top: 19px; left: -21px;
	}
	.ribbon span::before {
		content: "";
		position: absolute; left: 0px; top: 100%;
		z-index: -1;
		border-left: 3px solid #1e5799;
		border-right: 3px solid transparent;
		border-bottom: 3px solid transparent;
		border-top: 3px solid #1e5799;
	}
	.ribbon span::after {
		content: "";
		position: absolute; right: 0px; top: 100%;
		z-index: -1;
		border-left: 3px solid transparent;
		border-right: 3px solid #1e5799;
		border-bottom: 3px solid transparent;
		border-top: 3px solid #1e5799;
	}
h2.category {
    background-color: #C3E0E5;
    border-bottom: 1px solid #336699;
    color: #224477;
    margin:0px;
    padding: 5px;
}
h2.categorycommercial {
    background-color: #dddddd;
    border-bottom: 1px solid #666666;
    color: #555555;
    margin:0px;
    padding: 5px;
}
a.addonshelp { text-decoration: none; color: #aaaaaa; }
a.addonshelp:hover { text-decoration: none; color: #0000ff; }
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
 
</script>

<a id="top"></a>

<h1>WP Time Slots Booking Form - <?php _e('Add Ons','wp-time-slots-booking-form'); ?></h1>

<?php if ($message) echo "<div id='setting-error-settings_updated' class='updated' style='margin:0px;'><h2>".esc_html($message)."</h2></div> <br />";
 ?>

<div class="ahb-buttons-container">
	<a href="<?php print esc_attr(admin_url('admin.php?page='.$this->menu_parameter));?>" class="ahb-return-link">&larr;<?php _e('Return to the calendars list','wp-time-slots-booking-form'); ?></a>
	<div class="clear"></div>
</div>


<input type="button" value="<?php esc_html_e("Activate/Deactivate Marked Add Ons",'wp-time-slots-booking-form'); ?>" onclick="cp_activateAddons();" class="button button-primary ahb-first-button" />
<input type="button" value="<?php esc_html_e("Get The Full List of Add Ons",'wp-time-slots-booking-form'); ?>" onclick="document.location='?page=cp_timeslotsbooking_upgrade';"class="button" />
<div class="clear"></div>

<!-- Add Ons -->
<h2><?php _e('Active Add Ons','wp-time-slots-booking-form'); ?></h2>

<?php
$printedmsg = false;
$newlist = array();
foreach( $cptslotsb_addons_objs_list as $key => $obj )
{
    if (!isset($newlist[$obj->category]))
        $newlist[$obj->category] = array();
    $newlist[$obj->category][] = array ($key, $obj);
}

ksort($newlist);

foreach ($newlist as $category => $cptslotsb_addons_objs_list)
{

  for ($i=0; $i<count($cptslotsb_addons_objs_list)-1; $i++)
      for ($j=$i+1; $j<count($cptslotsb_addons_objs_list); $j++)
          if ($cptslotsb_addons_objs_list[$i][1]->get_addon_name() > $cptslotsb_addons_objs_list[$j][1]->get_addon_name())
          {
              $tmp = $cptslotsb_addons_objs_list[$i];
              $cptslotsb_addons_objs_list[$i] = $cptslotsb_addons_objs_list[$j];
              $cptslotsb_addons_objs_list[$j] = $tmp;
          }

 $is_commercial = ($category[0] != ' ');
?>


<?php if (!$printedmsg && $is_commercial) { $printedmsg = true; ?><h2><?php _e('The following Add Ons are included in the commercial versions of the plugin:','appointment-hour-booking'); ?></h2><?php } ?>


<div class="ahb-addons-container">
   <?php if ($is_commercial) { ?><div class="ribbon"><span><?php esc_html_e("Upgrade",'wp-time-slots-booking-form'); ?></span></div><?php } ?>
	<h2 class="category<?php if ($is_commercial) echo 'commercial'; ?>"><?php echo esc_html($category); ?></h2>
	<div class="ahb-addons-group">

	<?php
	for ($i=0; $i<count($cptslotsb_addons_objs_list); $i++)
	{
        $obj = $cptslotsb_addons_objs_list[$i];
		print '<div class="ahb-addon" style="padding-right:20px;border:0;width:320px;min-height:90px;float:left;">';
        if ($is_commercial)
            if (!empty($obj[1]->help))
                echo '<a style="text-decoration:none;color:#888888" target="_blank" href="'.esc_attr($obj[1]->help).'">';
            else
                echo '<a style="text-decoration:none;color:#888888" target="_blank" href="https://wptimeslot.dwbooster.com/download">';
        if (!$is_commercial) print '<label>'; else print '<strong>';
        print '<input '.($is_commercial?'disabled':'').' type="checkbox" id="'.esc_attr($obj[0]).'" name="cptslotsb_addons" value="'.esc_attr($obj[0]).'" '.( ( $obj[1]->addon_is_active() ) ? 'CHECKED' : '' ).'>'.esc_html($obj[1]->get_addon_name());
        if (!$is_commercial) print '</label>'; else print '</strong>';
        if ($is_commercial)
            echo '</a>';
        if (!empty($obj[1]->help))
            echo ' &nbsp; <a class="addonshelp" target="_blank" href="'.esc_attr($obj[1]->help).'">[?]</a>';
        print '<div style="margin-left:20px;color:#336699">'.esc_html($obj[1]->get_addon_description()).'</div>';
        print '</div>';
	}
	?>
    <div class="clear"></div>
	</div>
</div>

<?php } ?>

<div class="ahb-to-top" style="margin-bottom:10px;"><a href="#top">&uarr; <?php esc_html_e("Top",'wp-time-slots-booking-form'); ?></a></div>

<input type="button" value="<?php _e('Activate/Deactivate Marked Add Ons','wp-time-slots-booking-form'); ?>" onclick="cp_activateAddons();" class="button button-primary ahb-first-button" />
<input type="button" value="<?php _e('Get The Full List of Add Ons','wp-time-slots-booking-form'); ?>" onclick="document.location='?page=cp_timeslotsbooking_upgrade';"class="button" />
<div class="clear"></div>