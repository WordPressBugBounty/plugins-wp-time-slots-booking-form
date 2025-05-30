<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !is_admin() ) 
{
    echo 'Direct access not allowed.';
    exit;
}

$nonce = wp_create_nonce( 'cptslotsb_actions_adminsettings' );

$cpid = 'CP_WPTSLOTSBK';

$gotab = '';
if (isset($_POST["gotab"]))
{
    $gotab = sanitize_text_field($_POST["gotab"]);
    if ($gotab == '')
        $message = 'Email report settings updated.';
    else if ($gotab == 'fixarea')
        $message = 'Troubleshoot settings updated.';
    else if ($gotab == 'css')
        $message = 'Custom CSS updated.';
    else if ($gotab == 'js')
        $message = 'Custom javascript updated.';
} 
else 
    if (isset($_GET["gotab"]))
        $gotab = sanitize_text_field($_GET["gotab"]);
    

?>
<style>
	.ahb-tab{display:none;}
	.ahb-tab label{font-weight:600;}
	.tab-active{display:block;}
	.ahb-code-editor-container{border:1px solid #DDDDDD;margin-bottom:20px;}
    
.ahb-csssample { margin-top: 15px; margin-left:20px;  margin-right:20px;}
.ahb-csssampleheader { 
  font-weight: bold; 
  background: #dddddd;
	padding:10px 20px;-webkit-box-shadow: 0px 2px 2px 0px rgba(100, 100, 100, 0.1);-moz-box-shadow:    0px 2px 2px 0px rgba(100, 100, 100, 0.1);box-shadow:         0px 2px 2px 0px rgba(100, 100, 100, 0.1);
}
.ahb-csssamplecode {     background: #f4f4f4;
    border: 1px solid #ddd;
    border-left: 3px solid #f36d33;
    color: #666;
    page-break-inside: avoid;
    font-family: monospace;
    font-size: 15px;
    line-height: 1.6;
    margin-bottom: 1.6em;
    max-width: 100%;
    overflow: auto;
    padding: 1em 1.5em;
    display: block;
    word-wrap: break-word; 
}       
</style>
<script>
// Move to an external file
jQuery(function(){
	var $ = jQuery,
		flag_css_editor = true,
		flag_js_editor = true;
    <?php 
          if ($gotab == 'css' || $gotab == 'js') 
          { 
			if(function_exists('wp_enqueue_code_editor'))
			{
				$settings_js = wp_enqueue_code_editor(array('type' => 'application/javascript'));
				$settings_css = wp_enqueue_code_editor(array('type' => 'text/css'));

				// Bail if user disabled CodeMirror.
				if(!(false === $settings_js && false === $settings_css))
				{
					if ($gotab == 'css') print sprintf('{flag_css_editor = false; wp.codeEditor.initialize( "ahb_styles_container", %s );}',wp_json_encode( $settings_css ));

					if ($gotab == 'js') print sprintf('{flag_js_editor = false; wp.codeEditor.initialize( "ahb_javascript_container", %s );}',wp_json_encode( $settings_js ));
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
			// This function is used to load the code editor of WordPress
			if(function_exists('wp_enqueue_code_editor'))
			{
				$settings_js = wp_enqueue_code_editor(array('type' => 'application/javascript'));
				$settings_css = wp_enqueue_code_editor(array('type' => 'text/css'));

				// Bail if user disabled CodeMirror.
				if(!(false === $settings_js && false === $settings_css))
				{
					print sprintf('if(tab == 3 && flag_css_editor){flag_css_editor = false; wp.codeEditor.initialize( "ahb_styles_container", %s );}',wp_json_encode( $settings_css ));

					print sprintf('if(tab == 4 && flag_js_editor){flag_js_editor = false; wp.codeEditor.initialize( "ahb_javascript_container", %s );}',wp_json_encode( $settings_js ));
				}
			}
			?>
		}
	);
});
</script>
<h1>WP Time Slots Booking Form - <?php esc_html_e('General Settings','wp-time-slots-booking-form'); ?></h1>

<?php
    if (isset($message) && $message) echo "<div id='setting-error-settings_updated' class='updated'><h2>".esc_html($message)."</h2></div>";
?>
<nav class="nav-tab-wrapper ahb-tab-wrapper">
	<a href="javascript:void(0);" class="nav-tab<?php if ($gotab == '') echo ' nav-tab-active'; ?>" data-tab="1"><?php esc_html_e('Email Report Settings','wp-time-slots-booking-form'); ?></a>
	<a href="javascript:void(0);" class="nav-tab<?php if ($gotab == 'fixarea') echo ' nav-tab-active'; ?>"  data-tab="2"><?php esc_html_e('Troubleshoot Area','wp-time-slots-booking-form'); ?></a>
	<a href="javascript:void(0);" class="nav-tab<?php if ($gotab == 'css') echo ' nav-tab-active'; ?>"  data-tab="3"><?php esc_html_e('Edit Styles','wp-time-slots-booking-form'); ?></a>
	<a href="javascript:void(0);" class="nav-tab<?php if ($gotab == 'js') echo ' nav-tab-active'; ?>"  data-tab="4"><?php esc_html_e('Edit Scripts','wp-time-slots-booking-form'); ?></a>
</nav>

<!-- TAB 1 -->
<div class="ahb-tab<?php if ($gotab == '') echo ' tab-active'; ?>" data-tab="1">
	<h2><?php esc_html_e('Automatic Email Reports','wp-time-slots-booking-form'); ?></h2>
	<p><?php esc_html_e('Automatic email reports for','wp-time-slots-booking-form'); ?> <b><?php esc_html_e('ALL forms','wp-time-slots-booking-form'); ?></b>: <?php esc_html_e('Send submissions in CSV format via email','wp-time-slots-booking-form'); ?>.</p>    
	<form name="updatereportsettings" action="" method="post">
     <input name="nonce" type="hidden" value="<?php echo esc_attr($nonce); ?>" />
     <input name="<?php echo esc_attr($cpid); ?>_post_edition" type="hidden" value="1" />
     <input name="gotab" type="hidden" value="" />
     <table class="form-table">    
        <tr valign="top">
        <td scope="row" colspan="2"><strong><?php esc_html_e('Enable Reports?','wp-time-slots-booking-form'); ?></strong>
          <?php $option = get_option('cp_cptslotsb_rep_enable', 'no'); ?>
          <select name="cp_cptslotsb_rep_enable">
           <option value="no"<?php if ($option == 'no' || $option == '') echo ' selected'; ?>><?php esc_html_e('No','wp-time-slots-booking-form'); ?></option>
           <option value="yes"<?php if ($option == 'yes') echo ' selected'; ?>><?php esc_html_e('Yes','wp-time-slots-booking-form'); ?></option>
          </select>     
          &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
          <strong><?php esc_html_e('Send report every','wp-time-slots-booking-form'); ?>:</strong> <input type="text" name="cp_cptslotsb_rep_days" size="1" value="<?php echo esc_attr(get_option('cp_cptslotsb_rep_days', '1')); ?>" /> <?php esc_html_e('days','wp-time-slots-booking-form'); ?>
          &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
          <strong><?php esc_html_e('Send after this hour (server time)','wp-time-slots-booking-form'); ?>:</strong>
          <select name="cp_cptslotsb_rep_hour">
           <?php
             $hour = get_option('cp_cptslotsb_rep_hour', '0');
             for ($k=0;$k<24;$k++)
                 echo '<option value="'.esc_attr($k).'"'.($hour==$k?' selected':'').'>'.($k<10?'0':'').esc_html($k).'</option>';
           ?>
          </select>
        </td>
        <tr valign="top">
        <th scope="row"><?php esc_html_e('Send email from','wp-time-slots-booking-form'); ?></th>
        <td><input type="text" name="cp_cptslotsb_fp_from_email" size="70" value="<?php echo esc_attr(get_option('cp_cptslotsb_fp_from_email', get_the_author_meta('user_email', get_current_user_id()) )); ?>" /></td>
        </tr>       
        <tr valign="top">
        <th scope="row"><?php esc_html_e('Send to email(s)','wp-time-slots-booking-form'); ?></th>
        <td><input type="text" name="cp_cptslotsb_rep_emails" size="70" value="<?php echo esc_attr(get_option('cp_cptslotsb_rep_emails', '')); ?>" /></td>
        </tr>
        <tr valign="top">
        <th scope="row"><?php esc_html_e('Email subject','wp-time-slots-booking-form'); ?></th>
        <td><input type="text" name="cp_cptslotsb_rep_subject" size="70" value="<?php echo esc_attr(get_option('cp_cptslotsb_rep_subject', 'Submissions report...')); ?>" /></td>
        </tr>
        <tr valign="top">
        <th scope="row"><?php esc_html_e('Email format?','wp-time-slots-booking-form'); ?></th>
        <td>
          <?php $option = get_option('cp_cptslotsb_rep_emailformat', 'text'); ?>
          <select name="cp_cptslotsb_rep_emailformat">
           <option value="text"<?php if ($option != 'html') echo ' selected'; ?>><?php esc_html_e('Plain Text (default)','wp-time-slots-booking-form'); ?></option>
           <option value="html"<?php if ($option == 'html') echo ' selected'; ?>><?php esc_html_e('HTML (use html in the textarea below)','wp-time-slots-booking-form'); ?></option>
          </select>
        </td>
        </tr>  
        <tr valign="top">
        <th scope="row"><?php esc_html_e('Email Text (CSV file will be attached','wp-time-slots-booking-form'); ?>)</th>
        <td><textarea type="text" name="cp_cptslotsb_rep_message" rows="3" cols="80"><?php echo esc_textarea(get_option('cp_cptslotsb_rep_message', 'Attached you will find the data from the form submissions.')); ?></textarea></td>
        </tr>      
     </table>            
     <input type="submit" value="<?php esc_html_e("Update Report Settings",'wp-time-slots-booking-form'); ?>" class="button button-primary" />
     </form>
     <div class="clear"></div>
     <p><?php esc_html_e('Note: For setting up a report only for a specific form use the setting area available for that when editing each form settings','wp-time-slots-booking-form'); ?>.</p>
	
</div>

<!-- TAB 2 -->
<div class="ahb-tab<?php if ($gotab == 'fixarea') echo ' tab-active'; ?>" data-tab="2">
	<h2><?php esc_html_e('Troubleshoot Area','wp-time-slots-booking-form'); ?></h2>
	<p><b><?php esc_html_e('Important!','wp-time-slots-booking-form'); ?>:</b> <?php esc_html_e('Use this area only if you are experiencing conflicts with third party plugins, with the theme scripts or with the character encoding','wp-time-slots-booking-form'); ?>.</p>
    <form  method="post" action="" name="cpformconf2">
        <input name="nonce" type="hidden" value="<?php echo esc_attr($nonce); ?>" />
        <input name="<?php echo esc_attr($cpid); ?>_post_edition" type="hidden" value="1" />
        <input name="gotab" type="hidden" value="fixarea" />
	    <table class="form-table">
            <tbody>
	    		<tr valign="top">
	    			<th scope="row">
	    				<label><?php esc_html_e('Script load method','wp-time-slots-booking-form'); ?></label>
	    			</th>
	    			<td>
	    				<select id="ccscriptload" name="ccscriptload">
        <option value="0" <?php if (get_option('cp_tslotsb_LOAD_SCRIPTS',"1") == "1") echo 'selected'; ?>><?php esc_html_e('Classic (Recommended)','wp-time-slots-booking-form'); ?></option>
        <option value="1" <?php if (get_option('cp_tslotsb_LOAD_SCRIPTS',"1") != "1") echo 'selected'; ?>><?php esc_html_e('Direct','wp-time-slots-booking-form'); ?></option>
       </select><br>
	    				<em><?php esc_html_e('Change the script load method if the form doesn\'t appear in the public website.','wp-time-slots-booking-form'); ?></em>
	    			</td>
	    		</tr>
	    		<tr valign="top">
	    			<th scope="row">
	    				<label><?php esc_html_e('Character encoding','wp-time-slots-booking-form'); ?></label>
	    			</th>
	    			<td>
	    				<select id="cccharsets" name="cccharsets">
	    					<option value=""><?php esc_html_e('Keep current charset (Recommended)','wp-time-slots-booking-form'); ?></option>
                            <option value="utf8_general_ci">UTF-8 (<?php esc_html_e('try this first','wp-time-slots-booking-form'); ?>)</option>
                            <option value="latin1_swedish_ci">latin1_swedish_ci</option>
	    				</select><br>
	    				<em><?php esc_html_e('Update the charset if you are getting problems displaying special/non-latin characters. After updated you need to edit the special characters again.','wp-time-slots-booking-form'); ?></em>
	    			</td>
	    		</tr>
	    	</tbody>
	    </table>
	    <input type="submit" value="<?php esc_html_e('Update Changes','wp-time-slots-booking-form'); ?>" class="button button-primary" />
    </form>    
</div>

<!-- TAB 3 -->
<div class="ahb-tab<?php if ($gotab == 'css') echo ' tab-active'; ?>" data-tab="3">
	<h2><?php esc_html_e('Edit Styles','wp-time-slots-booking-form'); ?></h2>
	<h3><?php esc_html_e("Important Note: Inserting CSS styles directly in this area of the plugin isn't longer allowed. Please insert the custom styles in the theme using for example the",'wp-time-slots-booking-form'); ?> <a href="customize.php"><?php esc_html_e("following way",'wp-time-slots-booking-form'); ?></a>:</h3>
    <a href="customize.php"><img src="<?php echo plugins_url('/images/edit-styles.png', __FILE__)?>" style="width:80%;padding:10px;max-width:900px;border:3px dotted gray"></a>
    <h3><?php esc_html_e("For commonly used CSS styles please check the following FAQ section",'wp-time-slots-booking-form'); ?>: <a href="https://wptimeslot.dwbooster.com/faq#design">https://wptimeslot.dwbooster.com/faq#design</a></h3>
     
    <br /><hr /><br />
   
   <div class="ahb-statssection-container" style="background:#f6f6f6;">
	<div class="ahb-statssection-header" style="background:white;
	padding:10px 20px;-webkit-box-shadow: 0px 2px 2px 0px rgba(100, 100, 100, 0.1);-moz-box-shadow:    0px 2px 2px 0px rgba(100, 100, 100, 0.1);box-shadow:         0px 2px 2px 0px rgba(100, 100, 100, 0.1);">
    <h3><?php esc_html_e('Sample Styles','wp-time-slots-booking-form'); ?>:</h3>
	</div>
	<div class="ahb-statssection">
      
        <div class="ahb-csssample">
         <div class="ahb-csssampleheader">
           <?php esc_html_e("Make the calendar 100% width / responsive",'wp-time-slots-booking-form'); ?>:
         </div>
         <div class="ahb-csssamplecode">
           #fbuilder .ui-datepicker-inline { max-width:none !important; }       
         </div>
        </div>
        
        <div class="ahb-csssample">
         <div class="ahb-csssampleheader">
           <?php esc_html_e("Make the send button in a hover format",'wp-time-slots-booking-form'); ?>:
         </div>
         <div class="ahb-csssamplecode">
           .pbSubmit:hover {
               background-color: #4CAF50;
               color: white;
           }         
         </div>
        </div> 
        
        <div class="ahb-csssample">
         <div class="ahb-csssampleheader">
           <?php esc_html_e("Change the color of all form field labels",'wp-time-slots-booking-form'); ?>:
         </div>
         <div class="ahb-csssamplecode">
           #fbuilder, #fbuilder label, #fbuilder span { color: #00f; }     
         </div>
        </div> 

        <div class="ahb-csssample">
         <div class="ahb-csssampleheader">
           <?php esc_html_e("Change color of fonts into all fields",'wp-time-slots-booking-form'); ?>:
         </div>
         <div class="ahb-csssamplecode">
           #fbuilder input[type=text], 
           #fbuilder textarea, 
           #fbuilder select { 
             color: #00f; 
           }     
         </div>
        </div> 
        
        <div class="ahb-csssample">
         <div class="ahb-csssampleheader">
           <?php esc_html_e("Add a text before the selected date-time",'wp-time-slots-booking-form'); ?>:
         </div>
         <div class="ahb-csssamplecode">
           .sb_item:before { content: "Selected date: "}
         </div>
        </div> 
        
        <div class="ahb-csssample">
         <div class="ahb-csssampleheader">
           <?php esc_html_e("Change the calendar header color",'wp-time-slots-booking-form'); ?>:
         </div>
         <div class="ahb-csssamplecode">
           #fbuilder .ui-datepicker-header { background:#6cc72b ; color:#444; text-shadow:none; }
         </div>
        </div>          
                
        <div class="ahb-csssample">
         <div class="ahb-csssampleheader">
           <?php esc_html_e("Change styles of the selected date-times",'wp-time-slots-booking-form'); ?>:
         </div>
         <div class="ahb-csssamplecode">
           .sb_item { color: red; }
         </div>
        </div>         
        
        
        <div class="ahb-csssample">
         <div class="ahb-csssampleheader">
           <?php esc_html_e("Other styles",'wp-time-slots-booking-form'); ?>:
         </div>
         <div class="ahb-csssamplecode">
           <?php esc_html_e("For other styles check the design section in the FAQ",'wp-time-slots-booking-form'); ?>: <a href="https://wptimeslot.dwbooster.com/faq#design">https://wptimeslot.dwbooster.com/faq#design</a>     
         </div>
        </div>         
       
    </div>
   </div>  
   
</div>

<!-- TAB 4 -->
<div class="ahb-tab<?php if ($gotab == 'js') echo ' tab-active'; ?>" data-tab="4">
	<h2><?php esc_html_e('Edit Scripts','wp-time-slots-booking-form'); ?></h2>
    <h3><?php esc_html_e("Important Note: This way of inserting JavaScript in the plugin isn't longer allowed. For this version of the plugin please insert the custom scripts in your own scripts or theme.",'wp-time-slots-booking-form'); ?></h3>
</div>
