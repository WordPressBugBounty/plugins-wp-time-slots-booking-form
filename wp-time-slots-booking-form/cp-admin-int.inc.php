<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$current_user = wp_get_current_user();
$current_user_access = current_user_can('edit_pages');
$current_user_can_admin = current_user_can('manage_options');

if ( !is_admin() || (!$current_user_access && !@in_array($current_user->ID, unserialize($this->get_option("cp_user_access","")))))
{
    echo 'Direct access not allowed.';
    exit;
}

$this->item = intval($_GET["cal"]);
    
$this->option_buffered_item = false;
$this->option_buffered_id = -1;

define('CP_TSLOTSBOOK_DEFAULT_fp_from_email', get_the_author_meta('user_email', get_current_user_id()) );
define('CP_TSLOTSBOOK_DEFAULT_fp_destination_emails', CP_TSLOTSBOOK_DEFAULT_fp_from_email);

if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset( $_POST[$this->prefix.'_post_options'] ) )
    echo "<div id='setting-error-settings_updated' class='updated'> <h2>Settings saved.</h2></div>";

$nonce = wp_create_nonce( 'cptslotsb_actions_admin' );

?>
<script type="text/javascript">
    function update_cptslotsb_option() 
    {
        var seloption = document.cpformconf.fp_emailtomethod.selectedIndex;
        if (seloption == 0)
        {
            document.getElementById("cptslotsb_destemails").style.display = '';
            document.getElementById("cptslotsb_dropemails").style.display = 'none';
        }
        else
        {
            document.getElementById("cptslotsb_destemails").style.display = 'none';
            document.getElementById("cptslotsb_dropemails").style.display = '';
        }    
    }
    
	jQuery(function(){
		var $ = jQuery;
		$(document).on('click', '.ahb-step', function(){
			var s = $(this).data('step');
			ahbGoToStep(s);
		});

		window['ahbGoToStep'] = function(s){
			$('.ahb-step.ahb-step-active').removeClass('ahb-step-active');
			$('.ahb-step[data-step="'+s+'"]').addClass('ahb-step-active');
			$('.ahb-adintsection.ahb-adintsection-active').removeClass('ahb-adintsection-active');
			$('.ahb-adintsection[data-step="'+s+'"]').addClass('ahb-adintsection-active');
            $(window).scrollTop( $("#topadminsection").offset().top );
		};
	});
   
</script>
<div class="wrap">
<h1><?php esc_html_e('Edit','wp-time-slots-booking-form'); ?> - <?php echo esc_html($this->get_option('form_name','Calendar')); ?></h1>


<form method="post" action="" name="cpformconf"> 
<input name="anonce" type="hidden" value="<?php echo esc_attr($nonce); ?>" />
<input name="<?php echo esc_attr($this->prefix); ?>_post_options" type="hidden" value="1" />
<input name="<?php echo esc_attr($this->prefix); ?>_id" type="hidden" value="<?php echo esc_attr($this->item); ?>" />
<input type="hidden" name="templates" id="templates" value="<?php echo esc_attr( json_encode( $this->available_templates() ) ); ?>" />
   
<div id="topadminsection"  class="ahb-buttons-container">
	<input type="submit" class="button button-primary ahb-save-btn" name="savereturn" value="<?php esc_html_e('Save Changes and Return','wp-time-slots-booking-form'); ?>"  />
	<a href="<?php print esc_attr(admin_url('admin.php?page='.$this->menu_parameter));?>" class="ahb-return-link">&larr;<?php esc_html_e('Return to the calendars list','wp-time-slots-booking-form'); ?></a>
	<div class="clear"></div>
</div>  
   
<div class="ahb-adintsection-container">
	<div class="ahb-breadcrumb">
		<div class="ahb-step ahb-step-active" data-step="1">
			<i>1</i>
			<label><?php esc_html_e('Editor','wp-time-slots-booking-form'); ?></label>
		</div>
		<div class="ahb-step" data-step="2">
			<i>2</i>
			<label><?php esc_html_e('General Settings','wp-time-slots-booking-form'); ?></label>
		</div>
		<div class="ahb-step" data-step="3">
			<i>3</i>
			<label><?php esc_html_e('Notification Emails','wp-time-slots-booking-form'); ?></label>
		</div>
		<div class="ahb-step" data-step="4">
			<i>4</i>
			<label><?php esc_html_e('Antispam','wp-time-slots-booking-form'); ?></label>
		</div>
		<div class="ahb-step" data-step="5">
			<i>5</i>
			<label><?php esc_html_e('Reports','wp-time-slots-booking-form'); ?></label>
		</div>
        <div class="ahb-step" data-step="6">
			<i>6</i>
			<label><?php esc_html_e('Add Ons','wp-time-slots-booking-form'); ?></label>
		</div>
	</div>

    
  
    <div class="ahb-adintsection ahb-adintsection-active" data-step="1">
       <div class="inside">   
    
         <input type="hidden" name="form_structure_control" id="form_structure_control" value="&quot;&quot;&quot;&quot;&quot;&quot;" />
         <input type="hidden" name="form_structure" id="form_structure" size="180" value="<?php echo $this->clean_sanitize(str_replace('"','&quot;',str_replace("\r","",str_replace("\n","",esc_attr($this->cleanJSON($this->get_option('form_structure', CP_TSLOTSBOOK_DEFAULT_form_structure))))))); ?>" />
         
         
<style type="text/css">
         .ui-state-active, .ui-widget-content .ui-state-active, .ui-widget-header .ui-state-active {
    border: 1px solid #2694e8;
    background: #3baae3 url(images/ui-bg_glass_50_3baae3_1x400.png) 50% 50% repeat-x;
    font-weight: bold;
    color: #ffffff;
}
</style>
            
         <script type="text/javascript">                 
           $easyFormQuery = jQuery.noConflict();
         </script> 
                 
         <script>
             
             $easyFormQuery(document).ready(function() {
                var f = $easyFormQuery("#fbuilder").fbuilder();
    		    window['cff_form'] = f;
                f.fBuild.loadData("form_structure", "templates");
                
                $easyFormQuery("#saveForm").click(function() {       
                    f.fBuild.saveData("form_structure");
                });  
                     
                $easyFormQuery(".itemForm").click(function() {
         	       f.fBuild.addItem($easyFormQuery(this).attr("id"));
         	   });  
              
               $easyFormQuery( ".itemForm" ).draggable({revert1: "invalid",helper: "clone",cursor: "move"});
         	   $easyFormQuery( "#fbuilder" ).droppable({
         	       accept: ".button",
         	       drop: function( event, ui ) {
         	           f.fBuild.addItem(ui.draggable.attr("id"));				
         	       }
         	   });
         		    
             }); 
            var randcaptcha = 1;
            function generateCaptcha()
            {            
               var d=new Date();
               var f = document.cpformconf;    
               var qs = "&width="+f.cv_width.value;
               qs += "&height="+f.cv_height.value;
               qs += "&letter_count="+f.cv_chars.value;
               qs += "&min_size="+f.cv_min_font_size.value;
               qs += "&max_size="+f.cv_max_font_size.value;
               qs += "&noise="+f.cv_noise.value;
               qs += "&noiselength="+f.cv_noise_length.value;
               qs += "&bcolor="+f.cv_background.value;
               qs += "&border="+f.cv_border.value;
               qs += "&font="+f.cv_font.options[f.cv_font.selectedIndex].value;
               qs += "&r="+(randcaptcha++);
               
               document.getElementById("captchaimg").src= "<?php echo esc_js($this->get_site_url(true).'/?'.$this->prefix).'_captcha=captcha&inAdmin=1'; ?>"+qs;
            }
    
         </script>
         
         <div style="background:#fafafa;min-width:780px;" class="form-builder">
         
             <div class="column width50">
                 <div id="tabs">
         			<ul>
         				<li><a href="#tabs-1"><?php esc_html_e('Add a Field','wp-time-slots-booking-form'); ?></a></li>
         				<li><a href="#tabs-2"><?php esc_html_e('Field Settings','wp-time-slots-booking-form'); ?></a></li>
         				<li><a href="#tabs-3"><?php esc_html_e('Form Settings','wp-time-slots-booking-form'); ?></a></li>
         			</ul>
         			<div id="tabs-1">
         			    
         			</div>
         			<div id="tabs-2"></div>
         			<div id="tabs-3"></div>
         		</div>	
             </div>
             <div class="columnr width50 padding10" id="fbuilder">
                 <div id="formheader"></div>
                 <div id="fieldlist"></div>
                 <!--<div class="button" id="saveForm">Save Form</div>-->
             </div>
             <div class="clearer"></div>
             
         </div>           
      </div>    
      
       <br />
        <div style="padding:10px;background-color:#ffffdd;border:1px dotted black;">
            <p><STRONG>In this version</STRONG> the form builder supports <STRONG>calendar, text, email and acceptance checkbox fields</STRONG>.</p>
            <p><button type="button" onclick="window.open('<?php echo esc_js($this->plugin_download_URL); ?>?src=activatebtn');" style="cursor:pointer;height:35px;color:#20A020;font-weight:bold;">Activate the FULL form builder</button>
               <p style="font-weight:bold">The full set of fields also supports:
               <ul>
                <li> - <strong>Conditional Logic</strong>: Hide/show fields based in previous selections.</li>
                <li> - File <strong>uploads</strong>, strong>Multi-page</strong> forms</li>
                <li> - <strong>Payments integration</strong> with PayPal Standard, PayPal Pro, Stripe, Authorize.net, Skrill, Mollie / iDeal, TargetPay / iDeal, SagePay, RedSys TPV and Sage Payments.</li>
                <li> - <strong><a href="?page=cp_apphourbooking_addons">Full set of addons</a></strong> (iCal sync, SMS, reminders, cancellation opts, reCaptcha, MailChimp, ...), <strong>fields</strong> and <strong>validations</strong></li>
               </ul>              
         </div>  
         
      <br />
      <!--<input type="submit" value="Save Changes" class="button-primary" />-->
      
      
      <!-- TEXT DEFINITIONS -->
      <hr style="margin-top:20px;" />
	  <h2><?php esc_html_e('Labels and Texts','wp-time-slots-booking-form'); ?></h2>
	  <hr />
      
      <h3 class='hndle' style="padding:5px;"><span><?php esc_html_e('Button Labels','wp-time-slots-booking-form'); ?></span></h3>
      <div class="inside">   
         <table class="form-table">    
            <tr valign="top">
            <td scope="row">
             <strong><?php esc_html_e('Submit button label (text)','wp-time-slots-booking-form'); ?>:</strong><br />
             <input type="text" name="vs_text_submitbtn" size="40" value="<?php $label = ($this->get_option('vs_text_submitbtn', 'Submit')); echo esc_attr($label==''?'Submit':$label); ?>" />
            </td>
            <td>
              <strong><?php esc_html_e('Page {0} of {0} (text)','wp-time-slots-booking-form'); ?>:</strong><br />
              <input type="text" name="vs_text_pageof" size="40" value="<?php $label = ($this->get_option('vs_text_pageof', 'Page {0} of {0}')); echo esc_attr($label==''?'Page {0} of {0}':$label); ?>" />
            </td>
            </tr>
            <tr valign="top">
            <td scope="row">
             <strong><?php esc_html_e('Previous page button label (text)','wp-time-slots-booking-form'); ?>:</strong><br />
             <input type="text" name="vs_text_previousbtn" size="40" value="<?php $label = ($this->get_option('vs_text_previousbtn', 'Previous')); echo esc_attr($label==''?'Previous':$label); ?>" /></td>
            <td scope="row">
             <strong><?php esc_html_e('Next page button label (text)','wp-time-slots-booking-form'); ?>:</strong><br />
             <input type="text" name="vs_text_nextbtn" size="40" value="<?php $label = ($this->get_option('vs_text_nextbtn', 'Next')); echo esc_attr($label==''?'Next':$label); ?>" /></td>
            </tr>          
            <tr valign="top">
            <td colspan="2">The  <em>class="pbSubmit"</em> can be used to modify the button styles. The styles can be applied into the <a href="?page=cp_timeslotsbooking_settings&gotab=css">CSS customization area</a></em>.  For further modifications the submit button is located at the end of the file <em>"cp-public-int.inc.php"</em>. For general CSS styles modifications to the form and samples <a href="http://wptimeslot.dwbooster.com/faq/" target="_blank">check the FAQ</a>.
            </tr>
         </table>
      </div>    
      
      <hr  size="1" />
        
      <h3 class='hndle' style="padding:5px;"><span><?php esc_html_e('Error messages for validation rules','wp-time-slots-booking-form'); ?></span></h3>
      <div class="inside">
         <table class="form-table">
            <tr valign="top">
            <td scope="row">
             <strong><?php esc_html_e('"is required" text','wp-time-slots-booking-form'); ?>:</strong><br />
             <input type="text" name="vs_text_is_required" size="40" value="<?php echo esc_attr($this->get_option('vs_text_is_required', CP_TSLOTSBOOK_DEFAULT_vs_text_is_required)); ?>" />
            </td>
            <td scope="row">
             <strong><?php esc_html_e('"is email" text','wp-time-slots-booking-form'); ?>:</strong><br />
             <input type="text" name="vs_text_is_email" size="40" value="<?php echo esc_attr($this->get_option('vs_text_is_email', CP_TSLOTSBOOK_DEFAULT_vs_text_is_email)); ?>" />
            </td>
            </tr>       
            <tr valign="top">
            <td scope="row">
             <strong><?php esc_html_e('"is valid captcha" text','wp-time-slots-booking-form'); ?>:</strong><br />
             <input type="text" name="cv_text_enter_valid_captcha" size="40" value="<?php echo esc_attr($this->get_option('cv_text_enter_valid_captcha', CP_TSLOTSBOOK_DEFAULT_cv_text_enter_valid_captcha)); ?>" />
            </td>
            <td scope="row"><strong><?php esc_html_e('"is valid date (mm/dd/yyyy)" text','wp-time-slots-booking-form'); ?>:</strong><br /><input type="text" name="vs_text_datemmddyyyy" size="40" value="<?php echo esc_attr($this->get_option('vs_text_datemmddyyyy', CP_TSLOTSBOOK_DEFAULT_vs_text_datemmddyyyy)); ?>" /></td>
            </tr>
            <tr valign="top">
            <td scope="row">
             <strong><?php esc_html_e('"is valid date (dd/mm/yyyy)" text','wp-time-slots-booking-form'); ?>:</strong><br />
             <input type="text" name="vs_text_dateddmmyyyy" size="40" value="<?php echo esc_attr($this->get_option('vs_text_dateddmmyyyy', CP_TSLOTSBOOK_DEFAULT_vs_text_dateddmmyyyy)); ?>" />
            </td>
            <td scope="row">
             <strong><?php esc_html_e('"is number" text','wp-time-slots-booking-form'); ?>:</strong><br />
             <input type="text" name="vs_text_number" size="40" value="<?php echo esc_attr($this->get_option('vs_text_number', CP_TSLOTSBOOK_DEFAULT_vs_text_number)); ?>" />
            </td>
            </tr>
            <tr valign="top">
            <td scope="row">
             <strong><?php esc_html_e('"only digits" text','wp-time-slots-booking-form'); ?>:</strong><br />
             <input type="text" name="vs_text_digits" size="40" value="<?php echo esc_attr($this->get_option('vs_text_digits', CP_TSLOTSBOOK_DEFAULT_vs_text_digits)); ?>" />
            </td>
            <td scope="row">
             <strong><?php esc_html_e('"under maximum" text','wp-time-slots-booking-form'); ?>:</strong><br />
             <input type="text" name="vs_text_max" size="40" value="<?php echo esc_attr($this->get_option('vs_text_max', CP_TSLOTSBOOK_DEFAULT_vs_text_max)); ?>" />
            </td>
            </tr>
            <tr valign="top">
            <td scope="row">
             <strong><?php esc_html_e('"over minimum" text','wp-time-slots-booking-form'); ?>:</strong><br />
             <input type="text" name="vs_text_min" size="40" value="<?php echo esc_attr($this->get_option('vs_text_min', CP_TSLOTSBOOK_DEFAULT_vs_text_min)); ?>" />
            </td>
            <td scope="row">
             <strong><?php esc_html_e('"Max appointments allowed messsage" text','wp-time-slots-booking-form'); ?>:</strong><br />
             <input type="text" name="vs_text_maxapp" size="40" value="<?php echo esc_attr($this->get_option('vs_text_maxapp', CP_TSLOTSBOOK_DEFAULT_vs_text_maxapp)); ?>" />
            </td>    
            </tr>            
            
         </table>  
      </div>    
        <hr>      
		<div class="ahb-buttons-container">
			<input type="button" value="<?php esc_html_e('Next Step - General Settings >','wp-time-slots-booking-form'); ?>" class="button" style="float:right;margin-right:10px" onclick="ahbGoToStep(2);" />
			<input type="submit" name="savepublish" value="<?php esc_html_e('Save and Publish','wp-time-slots-booking-form'); ?>" class="button button-primary" style="float:right;margin-right:10px" />
			<input type="submit" name="savereturn" value="<?php esc_html_e('Save and Return','wp-time-slots-booking-form'); ?>" class="button button-primary" style="float:right;margin-right:10px" />
			<div class="clear"></div>
		</div>           
     </div> 
     
     
      
     <div class="ahb-adintsection" data-step="2">
      
      <div class="inside">
      
         <h3 class='hndle' style="padding-top:5px;padding-bottom:5px;"><span><?php esc_html_e('Confirmation / Thank you page','wp-time-slots-booking-form'); ?></span></h3>
         <table class="form-table">
            <tr valign="top">
            <th scope="row"><?php esc_html_e('Confirmation / Thank you page','wp-time-slots-booking-form'); ?></th>
            <td><input type="text" name="fp_return_page" size="70" value="<?php echo esc_attr($this->get_option('fp_return_page', CP_TSLOTSBOOK_DEFAULT_fp_return_page)); ?>" />
            <br /><em><?php esc_html_e('Address / URL of the page where the user will be redirected after submiting the booking form','wp-time-slots-booking-form'); ?></em></td>
            </tr>
          <table> 
         <hr />
         
         <h3 class='hndle' style="padding-top:5px;padding-bottom:5px;"><span><?php esc_html_e('Booking Status','wp-time-slots-booking-form'); ?></span></h3>
         <table class="form-table">
            <tr valign="top">
            <th scope="row"><?php esc_html_e('Default status of new bookings','wp-time-slots-booking-form'); ?></th>
            <td><?php $this->render_status_box('defaultstatus',$this->get_option('defaultstatus', ''));  ?>
            <br /><em><?php esc_html_e('Only "Approved" items are taken in account for the availability verification.','wp-time-slots-booking-form'); ?></em></td>
            </tr>
            <tr valign="top">
            <th scope="row"><?php esc_html_e('Default status of paid bookings','wp-time-slots-booking-form'); ?></th>
            <td><?php $this->render_status_box('defaultpaidstatus',$this->get_option('defaultpaidstatus', ''));  ?>
            <br /><em><?php esc_html_e('If a payment add-on is enabled the booking will be changed to this status after the payment.','wp-time-slots-booking-form'); ?></em></td>
            </tr>            
          <table> 
         <hr />       
      
         <h3 class='hndle' style="padding:5px;"><span><?php esc_html_e('Global Calendar Settings','wp-time-slots-booking-form'); ?></span></h3>
       
         <table class="form-table">
            <tr valign="top">
            <th scope="row"><?php esc_html_e('Date Format','wp-time-slots-booking-form'); ?></th>
            <td><?php $v = $this->get_option('date_format','mm/dd/yy'); ?>
         	   <select name="date_format">
    		   <option <?php if ($v == '' || $v == 'mm/dd/yy') echo 'selected'; ?> value="mm/dd/yy"><?php esc_html_e("Default",'wp-time-slots-booking-form'); ?> - mm/dd/yyyy</option>
               <option <?php if ($v == 'dd/mm/yy') echo 'selected'; ?> value="dd/mm/yy">dd/mm/yyyy</option>
               <option <?php if ($v == 'mm.dd.yy') echo 'selected'; ?> value="mm.dd.yy">mm.dd.yyyy</option>
               <option <?php if ($v == 'dd.mm.yy') echo 'selected'; ?> value="dd.mm.yy">dd.mm.yyyy</option>
    		   <option <?php if ($v == 'yy-mm-dd') echo 'selected'; ?> value="yy-mm-dd">ISO 8601 - yyyy-mm-dd</option>
    		   <option <?php if ($v == 'd M, y') echo 'selected'; ?> value="d M, y"><?php esc_html_e("Short",'wp-time-slots-booking-form'); ?> - d M, yy</option>
    		   <option <?php if ($v == 'd MM, y') echo 'selected'; ?> value="d MM, y"><?php esc_html_e("Medium",'wp-time-slots-booking-form'); ?> - d MM, yy</option>
    		   <option <?php if ($v == 'DD, d MM, yy') echo 'selected'; ?> value="DD, d MM, yy"><?php esc_html_e("Full",'wp-time-slots-booking-form'); ?> - DD, d MM, yyyy</option>
        	   </select>
            </td>
            </tr>
            <tr valign="top">
            <th scope="row"><?php esc_html_e('Calendar Language','wp-time-slots-booking-form'); ?></th>
            <td><?php $v = $this->get_option('calendar_language',''); ?>            
                 <select name="calendar_language" id="calendar_language">
    <option <?php if ($v == '') echo 'selected'; ?> value=""> - auto-detect - </option>
    <option <?php if ($v == 'af') echo 'selected'; ?> value="af">Afrikaans</option>
    <option <?php if ($v == 'sq') echo 'selected'; ?> value="sq">Albanian</option>
    <option <?php if ($v == 'ar') echo 'selected'; ?> value="ar">Arabic</option>
    <option <?php if ($v == 'ar_DZ') echo 'selected'; ?> value="ar_DZ">Arabic (Algeria)</option>
    <option <?php if ($v == 'hy_AM') echo 'selected'; ?> value="hy_AM">Armenian</option>
    <option <?php if ($v == 'az') echo 'selected'; ?> value="az">Azerbaijani</option>
    <option <?php if ($v == 'eu') echo 'selected'; ?> value="eu">Basque</option>
    <option <?php if ($v == 'bs_BA') echo 'selected'; ?> value="bs_BA">Bosnian</option>
    <option <?php if ($v == 'bg_BG') echo 'selected'; ?> value="bg_BG">Bulgarian</option>
    <option <?php if ($v == 'be_BY') echo 'selected'; ?> value="be_BY">Byelorussian (Belarusian)</option>
    <option <?php if ($v == 'km') echo 'selected'; ?> value="km">Cambodian</option>
    <option <?php if ($v == 'ca') echo 'selected'; ?> value="ca">Catalan</option>
    <option <?php if ($v == 'zh_HK') echo 'selected'; ?> value="zh_HK">Chinese (Hong Kong SAR)</option>
    <option <?php if ($v == 'zh_CN') echo 'selected'; ?> value="zh_CN">Chinese (PRC)</option>
    <option <?php if ($v == 'zh_TW') echo 'selected'; ?> value="zh_TW">Chinese (Taiwan)</option>
    <option <?php if ($v == 'hr') echo 'selected'; ?> value="hr">Croatian</option>
    <option <?php if ($v == 'cs_CZ') echo 'selected'; ?> value="cs_CZ">Czech</option>
    <option <?php if ($v == 'da_DK') echo 'selected'; ?> value="da_DK">Danish</option>
    <option <?php if ($v == 'nl_NL') echo 'selected'; ?> value="nl_NL">Dutch</option>
    <option <?php if ($v == 'nl_BE') echo 'selected'; ?> value="nl_BE">Dutch - Belgium</option>
    <option <?php if ($v == 'en_AU') echo 'selected'; ?> value="en_AU">English (Australia)</option>
    <option <?php if ($v == 'en_NZ') echo 'selected'; ?> value="en_NZ">English (New Zealand)</option>
    <option <?php if ($v == 'en_GB') echo 'selected'; ?> value="en_GB">English (United Kingdom)</option>
    <option <?php if ($v == 'eo_EO') echo 'selected'; ?> value="eo">Esperanto</option>
    <option <?php if ($v == 'et') echo 'selected'; ?> value="et">Estonian</option>
    <option <?php if ($v == 'fo') echo 'selected'; ?> value="fo">Faeroese</option>
    <option <?php if ($v == 'fa_IR') echo 'selected'; ?> value="fa_IR">Farsi</option>
    <option <?php if ($v == 'fi') echo 'selected'; ?> value="fi">Finnish</option>
    <option <?php if ($v == 'fr_FR') echo 'selected'; ?> value="fr_FR">French</option>
    <option <?php if ($v == 'fr_CA') echo 'selected'; ?> value="fr_CA">French (Canada)</option>
    <option <?php if ($v == 'fr_CH') echo 'selected'; ?> value="fr_CH">French (Switzerland)</option>
    <option <?php if ($v == 'gl_ES') echo 'selected'; ?> value="gl_ES">Galician</option>
    <option <?php if ($v == 'ka_GE') echo 'selected'; ?> value="ka_GE">Georgian</option>
    <option <?php if ($v == 'de_DE') echo 'selected'; ?> value="de_DE">German</option>
    <option <?php if ($v == 'el') echo 'selected'; ?> value="el">Greek</option>
    <option <?php if ($v == 'he_IL') echo 'selected'; ?> value="he_IL">Hebrew</option>
    <option <?php if ($v == 'hi_IN') echo 'selected'; ?> value="hi_IN">Hindi</option>
    <option <?php if ($v == 'hu_HU') echo 'selected'; ?> value="hu_HU">Hungarian</option>
    <option <?php if ($v == 'is') echo 'selected'; ?> value="is">Icelandic</option>
    <option <?php if ($v == 'id_ID') echo 'selected'; ?> value="id_ID">Indonesian</option>
    <option <?php if ($v == 'it_IT') echo 'selected'; ?> value="it_IT">Italian</option>
    <option <?php if ($v == 'it_CH') echo 'selected'; ?> value="it_CH">Italian (Switzerland)</option>
    <option <?php if ($v == 'ja') echo 'selected'; ?> value="ja">Japanese</option>
    <option <?php if ($v == 'kk') echo 'selected'; ?> value="kk">Kazakh</option>
    <option <?php if ($v == 'ky') echo 'selected'; ?> value="ky">Kirghiz</option>
    <option <?php if ($v == 'ko_KR') echo 'selected'; ?> value="ko_KR">Korean</option>
    <option <?php if ($v == 'lv') echo 'selected'; ?> value="lv">Latvian (Lettish)</option>
    <option <?php if ($v == 'lt_LT') echo 'selected'; ?> value="lt_LT">Lithuanian</option>
    <option <?php if ($v == 'lb') echo 'selected'; ?> value="lb">Luxembourgish</option>
    <option <?php if ($v == 'mk_MK') echo 'selected'; ?> value="mk_MK">Macedonian</option>
    <option <?php if ($v == 'ms_MY') echo 'selected'; ?> value="ms_MY">Malay</option>
    <option <?php if ($v == 'ml_IN') echo 'selected'; ?> value="ml_IN">Malayalam</option>
    <option <?php if ($v == 'no') echo 'selected'; ?> value="no">Norwegian</option>
    <option <?php if ($v == 'nb_NO') echo 'selected'; ?> value="nb_NO">Norwegian (Bokm&aring;l)</option>
    <option <?php if ($v == 'nn') echo 'selected'; ?> value="nn">Norwegian Nynorsk</option>
    <option <?php if ($v == 'pl_PL') echo 'selected'; ?> value="pl_PL">Polish</option>
    <option <?php if ($v == 'pt_PT') echo 'selected'; ?> value="pt_PT">Portuguese</option>
    <option <?php if ($v == 'pt_BR') echo 'selected'; ?> value="pt_BR">Portuguese (Brazil)</option>
    <option <?php if ($v == 'rm') echo 'selected'; ?> value="rm">Rhaeto-Romance</option>
    <option <?php if ($v == 'ro_RO') echo 'selected'; ?> value="ro_RO">Romanian</option>
    <option <?php if ($v == 'ru_RU') echo 'selected'; ?> value="ru_RU">Russian</option>
    <option <?php if ($v == 'sr_SR') echo 'selected'; ?> value="sr_SR">Serbian</option>
    <option <?php if ($v == 'sk_SK') echo 'selected'; ?> value="sk_SK">Slovak</option>
    <option <?php if ($v == 'sl_SI') echo 'selected'; ?> value="sl_SI">Slovenian</option>
    <option <?php if ($v == 'es_ES') echo 'selected'; ?> value="es_ES">Spanish</option>
    <option <?php if ($v == 'sv_SE') echo 'selected'; ?> value="sv_SE">Swedish</option>
    <option <?php if ($v == 'tj') echo 'selected'; ?> value="tj">Tajikistan</option>
    <option <?php if ($v == 'ta') echo 'selected'; ?> value="ta">Tamil</option>
    <option <?php if ($v == 'th') echo 'selected'; ?> value="th">Thai</option>
    <option <?php if ($v == 'tr_TR') echo 'selected'; ?> value="tr_TR">Turkish</option>
    <option <?php if ($v == 'uk') echo 'selected'; ?> value="uk">Ukrainian</option>
    <option <?php if ($v == 'vi') echo 'selected'; ?> value="vi">Vietnamese</option>
    <option <?php if ($v == 'cy_GB') echo 'selected'; ?> value="cy_GB">Welsh/UK</option>
    </select>
    </td>
            </tr>        
         </table>   
      </div>
      <hr />
      <h3 class='hndle' style="padding:5px;"><span><?php esc_html_e('Payment Integration Settings','wp-time-slots-booking-form'); ?></span></h3>
      <div class="inside">
         <table class="form-table">
            <tr valign="top">
            <th scope="row"><?php esc_html_e('Product name at payment page','wp-time-slots-booking-form'); ?></th>
            <td><input type="text" name="product_name" size="40" value="<?php echo esc_attr($this->get_option('product_name', 'Booking')); ?>" /></td>
            </tr>
            <tr valign="top">
            <th scope="row"><?php esc_html_e('Label of "Pay Later" option (if enabled)','wp-time-slots-booking-form'); ?></th>
            <td><input type="text" name="pay_later_label" size="40" value="<?php echo esc_attr($this->get_option('pay_later_label', 'Pay later')); ?>" /></td>
            </tr>        
         </table>   
         <em>* <?php esc_html_e('Note: To enable a payment method enable first the related addon.','wp-time-slots-booking-form'); ?></em>
      </div>
      <hr />
      <div <?php if (!$current_user_can_admin) echo 'style="display:none"'; ?>>
      <h3 class='hndle' style="padding:5px;"><span><?php esc_html_e('Users with access to the messages list','wp-time-slots-booking-form'); ?></span></h3>
      <div class="inside">
         <table class="form-table">    
            <tr valign="top">
            <th scope="row"><?php esc_html_e('Select users with access (CTRL+click for multiple selection)','wp-time-slots-booking-form'); ?>:</th>
            <td>
              <?php 
                 $users = $wpdb->get_results( "SELECT user_login,ID FROM ".$wpdb->users." ORDER BY ID DESC LIMIT 0,500" );                                                                     
                 $options = unserialize($this->get_option('cp_user_access', array())); 
                 if (!is_array($options)) $options = array();
              ?>           
              <select name="cp_user_access[]" multiple="multiple" size="5">
                <?php foreach ($users as $user) { ?>
                 <option value="<?php echo esc_attr($user->ID); ?>"<?php if ( in_array ($user->ID, $options) ) echo ' selected'; ?>><?php echo esc_html($user->user_login); ?></option>
                <?php  } ?>           
              </select>
            </td>
           </tr>
           <tr valign="top">
            <th scope="row"><?php esc_html_e('Allow selected users access also to the calendar settings?','wp-time-slots-booking-form'); ?>:</th>
            <td>
              <?php $option = $this->get_option('cp_user_access_settings', ''); ?>
              <select name="cp_user_access_settings">
               <option value="true"<?php if ($option == 'true') echo ' selected'; ?>><?php esc_html_e('Yes','wp-time-slots-booking-form'); ?></option>
               <option value=""<?php if ($option == '') echo ' selected'; ?>><?php esc_html_e('No','wp-time-slots-booking-form'); ?></option>
              </select>
            </td>
           </tr>           
         </table>  
      </div>   
      </div>
      <hr>      
		<div class="ahb-buttons-container">
			<input type="button" value="<?php esc_html_e('Next Step - Notification Emails >','wp-time-slots-booking-form'); ?>" class="button" style="float:right;margin-right:10px" onclick="ahbGoToStep(3);" />
			<input type="submit" name="savepublish" value="<?php esc_html_e('Save and Publish','wp-time-slots-booking-form'); ?>" class="button button-primary" style="float:right;margin-right:10px" />
			<input type="submit" name="savereturn" value="<?php esc_html_e('Save and Return','wp-time-slots-booking-form'); ?>" class="button button-primary" style="float:right;margin-right:10px" />
			<div class="clear"></div>
		</div>      
     </div>
       
     <div class="ahb-adintsection" data-step="3">
      <h3 class='hndle' style="padding:5px;"><span><?php esc_html_e('Form Processing','wp-time-slots-booking-form'); ?> / <?php esc_html_e('Email Settings','wp-time-slots-booking-form'); ?></span></h3>
      <div class="inside">
         <table class="form-table">    
         
            <tr valign="top">
            <th scope="row"><?php esc_html_e('Send email "From"','wp-time-slots-booking-form'); ?> </th>
            <td>
              <?php $option = $this->get_option('fp_emailfrommethod', "fixed"); ?>
               <select name="fp_emailfrommethod">
                 <option value="fixed"<?php if ($option == 'fixed') echo ' selected'; ?>><?php esc_html_e('From fixed email address indicated below - Recommended option','wp-time-slots-booking-form'); ?></option>
                 <option value="customer"<?php if ($option == 'customer') echo ' selected'; ?>><?php esc_html_e('From the email address indicated by the customer','wp-time-slots-booking-form'); ?></option>
                </select><br />
                <span style="font-size:10px;color:#666666">
                * <?php esc_html_e("If you select \"from fixed...\" the customer email address will appear in the \"to\" address when you hit \"reply\", this is the recommended setting to avoid mail server restrictions. ",'wp-time-slots-booking-form'); ?>
                <br />
                * <?php esc_html_e("If you select \"from customer email\" then the customer email will appear also visually when you receive the email, but this isn't supported by all hosting services, so this
                option isn't recommended in most cases.",'wp-time-slots-booking-form'); ?>
                </span>
            </td>
            </tr>       
            <tr valign="top">
            <th scope="row"><?php esc_html_e('"From" email (for fixed "from" addresses)','wp-time-slots-booking-form'); ?></th>
            <td><input type="text" name="fp_from_email" size="40" value="<?php echo esc_attr($this->get_option('fp_from_email', CP_TSLOTSBOOK_DEFAULT_fp_from_email)); ?>" /></td>
            </tr>             
    
            <th scope="row"><?php esc_html_e('Send email "To"','wp-time-slots-booking-form'); ?> </th>
            <td>
              <?php $option = $this->get_option('fp_emailtomethod', "fixed"); ?>
               <select name="fp_emailtomethod" onchange="update_cptslotsb_option();">
                 <option value="fixed"<?php if ($option == 'fixed') echo ' selected'; ?>><?php esc_html_e('To the fixed email(s) address(es) indicated below - Recommended option','wp-time-slots-booking-form'); ?></option>
                 <option value="customer"<?php if ($option == 'customer') echo ' selected'; ?>><?php esc_html_e('To the email address selected in a form field (ex: captcha image enabled is recommended in this case)','wp-time-slots-booking-form'); ?></option>
                </select><br />
                <span style="font-size:10px;color:#666666">
                * <?php esc_html_e('If you select "To fixed..." enter the destination emails in the next field. ','wp-time-slots-booking-form'); ?>
                <br />
                * <?php esc_html_e('If you select "To email ...in form field" then add a field like a drop-down, radio-button or checkbox that contains the email address in the field value (not needed in the field text but in the internal value).','wp-time-slots-booking-form'); ?>
                </span>
            </td>
            </tr>        
            <tr valign="top" id="cptslotsb_destemails" <?php if ($option == 'customer') echo ' style="display:none;"'; ?>>
            <th scope="row"><?php esc_html_e('Destination emails (comma separated)','wp-time-slots-booking-form'); ?></th>
            <td><input type="text" name="fp_destination_emails" size="40" value="<?php echo esc_attr($this->get_option('fp_destination_emails', CP_TSLOTSBOOK_DEFAULT_fp_destination_emails)); ?>" /></td>
            </tr>
            <tr valign="top" id="cptslotsb_dropemails" <?php if ($option != 'customer') echo ' style="display:none;"'; ?>>
            <th scope="row"><?php esc_html_e('Field that contains the destination email(s)','wp-time-slots-booking-form'); ?></th>
            <td>
                <select id="fp_destination_emails_field" name="fp_destination_emails_field" def="<?php echo esc_attr($this->get_option('fp_destination_emails_field', '')); ?>"></select>
            </td>
            </tr>     
            
            
            <tr valign="top">
            <th scope="row"><?php esc_html_e('Email subject','wp-time-slots-booking-form'); ?></th>
            <td><input type="text" name="fp_subject" size="70" value="<?php echo esc_attr($this->get_option('fp_subject', CP_TSLOTSBOOK_DEFAULT_fp_subject)); ?>" /></td>
            </tr>
            <tr valign="top">
            <th scope="row"><?php esc_html_e('Include additional information?','wp-time-slots-booking-form'); ?></th>
            <td>
              <?php $option = $this->get_option('fp_inc_additional_info', CP_TSLOTSBOOK_DEFAULT_fp_inc_additional_info); ?>
              <select name="fp_inc_additional_info">
               <option value="true"<?php if ($option == 'true') echo ' selected'; ?>><?php esc_html_e('Yes','wp-time-slots-booking-form'); ?></option>
               <option value="false"<?php if ($option == 'false') echo ' selected'; ?>><?php esc_html_e('No','wp-time-slots-booking-form'); ?></option>
              </select>
            </td>
            </tr>        
            <tr valign="top">
            <th scope="row"><?php esc_html_e('Email format?','wp-time-slots-booking-form'); ?></th>
            <td>
              <?php $option = $this->get_option('fp_emailformat', CP_TSLOTSBOOK_DEFAULT_email_format); ?>
              <select name="fp_emailformat">
               <option value="text"<?php if ($option != 'html') echo ' selected'; ?>><?php esc_html_e('Plain Text (default)','wp-time-slots-booking-form'); ?></option>
               <option value="html"<?php if ($option == 'html') echo ' selected'; ?>><?php esc_html_e('HTML (use html in the textarea below)','wp-time-slots-booking-form'); ?></option>
              </select>
            </td>
            </tr>        
            <tr valign="top">
            <th scope="row"><?php esc_html_e('Message','wp-time-slots-booking-form'); ?></th>
            <td><textarea type="text" name="fp_message" rows="6" cols="80"><?php echo esc_textarea($this->get_option('fp_message', CP_TSLOTSBOOK_DEFAULT_fp_message)); ?></textarea></td>
            </tr>                                                               
         </table>  
      </div>    
      <hr />
      <h3 class='hndle' style="padding:5px;"><span><?php esc_html_e('Email Copy to User','wp-time-slots-booking-form'); ?></span></h3>
      <div class="inside">
         <table class="form-table">    
            <tr valign="top">
            <th scope="row"><?php esc_html_e('Send confirmation/thank you message to user?','wp-time-slots-booking-form'); ?></th>
            <td>
              <?php $option = $this->get_option('cu_enable_copy_to_user', CP_TSLOTSBOOK_DEFAULT_cu_enable_copy_to_user); ?>
              <select name="cu_enable_copy_to_user">
               <option value="true"<?php if ($option == 'true') echo ' selected'; ?>><?php esc_html_e('Yes','wp-time-slots-booking-form'); ?></option>
               <option value="false"<?php if ($option == 'false') echo ' selected'; ?>><?php esc_html_e('No','wp-time-slots-booking-form'); ?></option>
              </select>
            </td>
            </tr>
            <tr valign="top">
            <th scope="row"><?php esc_html_e('Email field on the form','wp-time-slots-booking-form'); ?></th>
            <td><select id="cu_user_email_field" name="cu_user_email_field" def="<?php echo esc_attr($this->get_option('cu_user_email_field', CP_TSLOTSBOOK_DEFAULT_cu_user_email_field)); ?>"></select></td>
            </tr>             
            <tr valign="top">
            <th scope="row"><?php esc_html_e('Email subject','wp-time-slots-booking-form'); ?></th>
            <td><input type="text" name="cu_subject" size="70" value="<?php echo esc_attr($this->get_option('cu_subject', CP_TSLOTSBOOK_DEFAULT_cu_subject)); ?>" /></td>
            </tr>
            <tr valign="top">
            <th scope="row"><?php esc_html_e('Email format?','wp-time-slots-booking-form'); ?></th>
            <td>
              <?php $option = $this->get_option('cu_emailformat', CP_TSLOTSBOOK_DEFAULT_email_format); ?>
              <select name="cu_emailformat">
               <option value="text"<?php if ($option != 'html') echo ' selected'; ?>><?php esc_html_e('Plain Text (default)','wp-time-slots-booking-form'); ?></option>
               <option value="html"<?php if ($option == 'html') echo ' selected'; ?>><?php esc_html_e('HTML (use html in the textarea below)','wp-time-slots-booking-form'); ?></option>
              </select>
            </td>
            </tr>  
            <tr valign="top">
            <th scope="row"><?php esc_html_e('Message','wp-time-slots-booking-form'); ?></th>
            <td><textarea type="text" name="cu_message" rows="6" cols="80"><?php echo esc_textarea($this->get_option('cu_message', CP_TSLOTSBOOK_DEFAULT_cu_message)); ?></textarea></td>
            </tr>        
         </table>  
      </div>    
		<hr>
		<div class="ahb-buttons-container">
			<input type="button" value="<?php esc_html_e('Next Step - Antispam >','wp-time-slots-booking-form'); ?>" class="button" style="float:right;margin-right:10px" onclick="ahbGoToStep(4);" />
			<input type="submit" name="savepublish" value="<?php esc_html_e('Save and Publish','wp-time-slots-booking-form'); ?>" class="button button-primary" style="float:right;margin-right:10px" />
			<input type="submit" name="savereturn" value="<?php esc_html_e('Save and Return','wp-time-slots-booking-form'); ?>" class="button button-primary" style="float:right;margin-right:10px" />
			<div class="clear"></div>
		</div>      
     </div>  
     
    
     <div class="ahb-adintsection" data-step="4">
      <h3 class='hndle' style="padding:5px;"><span><?php esc_html_e('Captcha Verification','wp-time-slots-booking-form'); ?></span></h3>
      <div class="inside">
         <table class="form-table">    
            <tr valign="top">
            <th scope="row"><?php esc_html_e('Use Captcha Verification?','wp-time-slots-booking-form'); ?></th>
            <td colspan="5">
              <?php $option = $this->get_option('cv_enable_captcha', CP_TSLOTSBOOK_DEFAULT_cv_enable_captcha); ?>
              <select name="cv_enable_captcha">
               <option value="true"<?php if ($option == 'true') echo ' selected'; ?>><?php esc_html_e('Yes','wp-time-slots-booking-form'); ?></option>
               <option value="false"<?php if ($option == 'false') echo ' selected'; ?>><?php esc_html_e('No','wp-time-slots-booking-form'); ?></option>
              </select>
            </td>
            </tr>
            
            <tr valign="top">
             <th scope="row"><?php esc_html_e('Width','wp-time-slots-booking-form'); ?>:</th>
             <td><input type="text" name="cv_width" size="10" value="<?php echo esc_attr($this->get_option('cv_width', CP_TSLOTSBOOK_DEFAULT_cv_width)); ?>"  onblur="generateCaptcha();"  /></td>
             <th scope="row"><?php esc_html_e('Height','wp-time-slots-booking-form'); ?>:</th>
             <td><input type="text" name="cv_height" size="10" value="<?php echo esc_attr($this->get_option('cv_height', CP_TSLOTSBOOK_DEFAULT_cv_height)); ?>" onblur="generateCaptcha();"  /></td>
             <th scope="row"><?php esc_html_e('Chars','wp-time-slots-booking-form'); ?>:</th>
             <td><input type="text" name="cv_chars" size="10" value="<?php echo esc_attr($this->get_option('cv_chars', CP_TSLOTSBOOK_DEFAULT_cv_chars)); ?>" onblur="generateCaptcha();"  /></td>
            </tr>             
    
            <tr valign="top">
             <th scope="row" valign="top"><?php esc_html_e('Min font size','wp-time-slots-booking-form'); ?>:</th>
             <td valign="top"><input type="text" name="cv_min_font_size" size="10" value="<?php echo esc_attr($this->get_option('cv_min_font_size', CP_TSLOTSBOOK_DEFAULT_cv_min_font_size)); ?>" onblur="generateCaptcha();"  /></td>
             <th scope="row" valign="top"><?php esc_html_e('Max font size','wp-time-slots-booking-form'); ?>:</th>
             <td valign="top"><input type="text" name="cv_max_font_size" size="10" value="<?php echo esc_attr($this->get_option('cv_max_font_size', CP_TSLOTSBOOK_DEFAULT_cv_max_font_size)); ?>" onblur="generateCaptcha();"  /></td>        
             <td colspan="2" rowspan="2">
               <?php esc_html_e('Preview','wp-time-slots-booking-form'); ?>:<br />
                 <br />
                <img src="<?php echo esc_url($this->get_site_url(true).'/?'.$this->prefix).'_captcha=captcha&inAdmin=1'; ?>"  id="captchaimg" alt="security code" border="0"  />            
             </td> 
            </tr>             
                    
    
            <tr valign="top">
             <th scope="row"><?php esc_html_e('Noise','wp-time-slots-booking-form'); ?>:</th>
             <td><input type="text" name="cv_noise" size="10" value="<?php echo esc_attr($this->get_option('cv_noise', CP_TSLOTSBOOK_DEFAULT_cv_noise)); ?>" onblur="generateCaptcha();" /></td>
             <th scope="row"><?php esc_html_e('Noise Length','wp-time-slots-booking-form'); ?>:</th>
             <td><input type="text" name="cv_noise_length" size="10" value="<?php echo esc_attr($this->get_option('cv_noise_length', CP_TSLOTSBOOK_DEFAULT_cv_noise_length)); ?>" onblur="generateCaptcha();" /></td>        
            </tr>          
            
    
            <tr valign="top">
             <th scope="row"><?php esc_html_e('Background','wp-time-slots-booking-form'); ?>:</th>
             <td><input type="color" name="cv_background" size="10" value="#<?php echo esc_attr($this->get_option('cv_background', CP_TSLOTSBOOK_DEFAULT_cv_background)); ?>" onblur="generateCaptcha();" /></td>
             <th scope="row"><?php esc_html_e('Border','wp-time-slots-booking-form'); ?>:</th>
             <td><input type="color" name="cv_border" size="10" value="#<?php echo esc_attr($this->get_option('cv_border', CP_TSLOTSBOOK_DEFAULT_cv_border)); ?>" onblur="generateCaptcha();" /></td>      
             <th scope="row"><?php esc_html_e('Font','wp-time-slots-booking-form'); ?>:</th>
             <td>
                <select name="cv_font" onchange="generateCaptcha();" >
                  <option value="font-1.ttf"<?php if ("font-1.ttf" == $this->get_option('cv_font', CP_TSLOTSBOOK_DEFAULT_cv_font)) echo " selected"; ?>>Font 1</option>
                  <option value="font-2.ttf"<?php if ("font-2.ttf" == $this->get_option('cv_font', CP_TSLOTSBOOK_DEFAULT_cv_font)) echo " selected"; ?>>Font 2</option>
                  <option value="font-3.ttf"<?php if ("font-3.ttf" == $this->get_option('cv_font', CP_TSLOTSBOOK_DEFAULT_cv_font)) echo " selected"; ?>>Font 3</option>
                  <option value="font-4.ttf"<?php if ("font-4.ttf" == $this->get_option('cv_font', CP_TSLOTSBOOK_DEFAULT_cv_font)) echo " selected"; ?>>Font 4</option>
                </select>            
             </td>            
            </tr>    
         </table>  
      </div> 
		<hr>
		<div class="ahb-buttons-container">
			<input type="button" value="<?php esc_html_e('Next Step - Reports >','wp-time-slots-booking-form'); ?>" class="button" style="float:right;margin-right:10px" onclick="ahbGoToStep(5);" />
			<input type="submit" name="savepublish" value="<?php esc_html_e('Save and Publish','wp-time-slots-booking-form'); ?>" class="button button-primary" style="float:right;margin-right:10px" />
			<input type="submit" name="savereturn" value="<?php esc_html_e('Save and Return','wp-time-slots-booking-form'); ?>" class="button button-primary" style="float:right;margin-right:10px" />
			<div class="clear"></div>
		</div>      
     </div>    
     
     <div class="ahb-adintsection" data-step="5">
      <h3 class='hndle' style="padding:5px;"><span><?php esc_html_e('Automatic Reports: Send submissions in CSV format via email','wp-time-slots-booking-form'); ?></span></h3>
      <div class="inside">
         <table class="form-table">    
            <tr valign="top">
            <th scope="row"><?php esc_html_e('Enable Reports?','wp-time-slots-booking-form'); ?></th>
            <td>
              <?php $option = $this->get_option('rep_enable', 'no'); ?>
              <select name="rep_enable">
               <option value="no"<?php if ($option == 'no' || $option == '') echo ' selected'; ?>><?php esc_html_e('No','wp-time-slots-booking-form'); ?></option>
               <option value="yes"<?php if ($option == 'yes') echo ' selected'; ?>><?php esc_html_e('Yes','wp-time-slots-booking-form'); ?></option>
              </select>
            </td>
            </tr>
            <tr valign="top">
            <th scope="row"><?php esc_html_e('Send report every','wp-time-slots-booking-form'); ?></th>
            <td><input type="text" name="rep_days" size="4" value="<?php echo esc_attr($this->get_option('rep_days', '1')); ?>" /> <?php esc_html_e('days (Put a 0 to send the report immediately after each submission)','wp-time-slots-booking-form'); ?></td>
            </tr>        
            <tr valign="top">
            <th scope="row"><?php esc_html_e('Send report after this hour (server time)','wp-time-slots-booking-form'); ?></th>
            <td>
              <select name="rep_hour">
               <?php
                 $hour = $this->get_option('rep_hour', '0');
                 for ($k=0;$k<24;$k++)
                     echo '<option value="'.esc_attr($k).'"'.($hour==$k?' selected':'').'>'.($k<10?'0':'').esc_html($k).'</option>';
               ?>
              </select>
            </td>
            </tr>        
            <tr valign="top">
            <th scope="row"><?php esc_html_e('Send the report to the following email addresses (comma separated)','wp-time-slots-booking-form'); ?></th>
            <td><input type="text" name="rep_emails" size="70" value="<?php echo esc_attr($this->get_option('rep_emails', '')); ?>" /></td>
            </tr>             
            <tr valign="top">
            <th scope="row"><?php esc_html_e('Email subject','wp-time-slots-booking-form'); ?></th>
            <td><input type="text" name="rep_subject" size="70" value="<?php echo esc_attr($this->get_option('rep_subject', 'Submissions report...')); ?>" /></td>
            </tr>
            <tr valign="top">
            <th scope="row"><?php esc_html_e('Email format?','wp-time-slots-booking-form'); ?></th>
            <td>
              <?php $option = $this->get_option('rep_emailformat', 'text'); ?>
              <select name="rep_emailformat">
               <option value="text"<?php if ($option != 'html') echo ' selected'; ?>><?php esc_html_e('Plain Text (default)','wp-time-slots-booking-form'); ?></option>
               <option value="html"<?php if ($option == 'html') echo ' selected'; ?>><?php esc_html_e('HTML (use html in the textarea below)','wp-time-slots-booking-form'); ?></option>
              </select>
            </td>
            </tr>  
            <tr valign="top">
            <th scope="row"><?php esc_html_e('Email Text (CSV file will be attached with the submissions)','wp-time-slots-booking-form'); ?></th>
            <td><textarea type="text" name="rep_message" rows="3" cols="80"><?php echo esc_textarea($this->get_option('rep_message', 'Attached you will find the data from the form submissions.')); ?></textarea></td>
            </tr>        
         </table>  
      </div>  
        <hr>
		<div class="ahb-buttons-container">
			<input type="button" value="<?php esc_html_e('Next Step - Add Ons >','wp-time-slots-booking-form'); ?>" class="button" style="float:right;margin-right:10px" onclick="ahbGoToStep(6);" />
			<input type="submit" name="savepublish" value="<?php esc_html_e('Save and Publish','wp-time-slots-booking-form'); ?>" class="button button-primary" style="float:right;margin-right:10px" />
			<input type="submit" name="savereturn" value="<?php esc_html_e('Save and Return','wp-time-slots-booking-form'); ?>" class="button button-primary" style="float:right;margin-right:10px" />
			<div class="clear"></div>
		</div> 
     </div>   
     

    <div class="ahb-adintsection" data-step="6">
     <?php
    	global $cptslotsb_addons_objs_list, $cptslotsb_addons_active_list;
        $printed = false;
    	if( count( $cptslotsb_addons_active_list ) )
    	{	
    		_e( '<h2>Add-Ons Settings:</h2><hr />', 'wp-time-slots-booking-form' );
            ob_start();
    		foreach( $cptslotsb_addons_active_list as $addon_id ) if( isset( $cptslotsb_addons_objs_list[ $addon_id ] ) ) print $cptslotsb_addons_objs_list[ $addon_id ]->get_addon_form_settings( $this->item );	
            $printed = ob_get_contents() != '';
            ob_end_flush();
    	}
        
        if (!$printed)
        {
           ?>
            <p><?php esc_html_e("You can optionally",'wp-time-slots-booking-form'); ?> <a target="_blank" href="?page=cp_timeslotsbooking_addons"><?php esc_html_e("activate add ons in the add ons section",'wp-time-slots-booking-form'); ?></a>.</p> 
            <p><?php esc_html_e("The add ons can be enabled to add new features.",'wp-time-slots-booking-form'); ?></p> 
            <p><?php esc_html_e("If you don't want to enable add ons now then",'wp-time-slots-booking-form'); ?> <strong><?php esc_html_e("continue saving these settings and publishing the booking form",'wp-time-slots-booking-form'); ?></strong>.</p> 
            <?php
        }        
     ?>
		<hr>
		<div class="ahb-buttons-container">
			<input type="submit" name="savepublish" value="<?php esc_html_e('Save and Publish','wp-time-slots-booking-form'); ?>" class="button button-primary" style="float:right;margin-right:10px" />
			<input type="submit" name="savereturn" value="<?php esc_html_e('Save and Return','wp-time-slots-booking-form'); ?>" class="button button-primary" style="float:right;margin-right:10px" />
			<div class="clear"></div>
		</div>      
    </div> 
   
 </div>

 <div class="ahb-buttons-container">
	<a href="<?php print esc_attr(admin_url('admin.php?page='.$this->menu_parameter));?>" class="ahb-return-link">&larr;<?php esc_html_e('Return to the calendars list','wp-time-slots-booking-form'); ?></a>
 </div>

</div> 



[<a href="https://wordpress.dwbooster.com/contact-us" target="_blank"><?php esc_html_e('Request Custom Modifications','wp-time-slots-booking-form'); ?></a>] | [<a href="https://wordpress.org/support/plugin/wp-time-slots-booking-form#new-post" target="_blank"><?php esc_html_e('Free Support','wp-time-slots-booking-form'); ?></a>] | [<a href="<?php echo esc_url($this->plugin_URL); ?>" target="_blank"><?php esc_html_e('Help','wp-time-slots-booking-form'); ?></a>]
</form>

<script type="text/javascript">generateCaptcha();</script>
 <script>
  jQuery( function() {
    jQuery( "#admin-tabs" ).tabs();
  } );
  </script>