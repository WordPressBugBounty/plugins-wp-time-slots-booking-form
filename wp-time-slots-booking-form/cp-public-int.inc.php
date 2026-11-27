<?php if ( !defined('CP_AUTH_INCLUDE') ) { echo 'Direct access not allowed.'; exit; } 

/**
  if (!is_admin()) {
      $full_url = get_permalink();  
      
      // Parse the URL to get the path
      $path = parse_url($full_url, PHP_URL_PATH);
      if ($path == "") $path = $full_url;
      
      // Add a random GET parameter to the path to prevent cache
      $cache_buster = rand(100000, 999999);
      $path .= (strpos($path, '?') === false ? '?' : '&') . 'wptsnocache=' . $cache_buster;
  }
  else $path = "";
*/

  $path = ""; 
  
?>
<form class="cpp_form no-prefetch" name="<?php echo esc_attr($this->prefix); ?>_pform<?php echo '_'.esc_attr($this->print_counter); ?>" id="<?php echo esc_attr($this->prefix); ?>_pform<?php echo '_'.esc_attr($this->print_counter); ?>" action="<?php echo esc_attr($path); ?>" method="post" enctype="multipart/form-data" onsubmit="return <?php echo esc_attr($this->prefix); ?>_pform_doValidate<?php echo '_'.esc_attr($this->print_counter); ?>(this);"><input type="hidden" name="cp_pform_psequence" value="<?php echo '_'.esc_attr($this->print_counter); ?>" /><input type="hidden" name="<?php echo esc_attr($this->prefix); ?>_pform_process" value="1" /><input type="hidden" name="<?php echo esc_attr($this->prefix); ?>_id" value="<?php echo intval($this->item); ?>" /><input type="hidden" name="cp_ref_page" value="<?php esc_attr($this->get_site_url()); ?>" /><input type="hidden" name="form_structure<?php echo '_'.esc_attr($this->print_counter); ?>" id="form_structure<?php echo '_'.esc_attr($this->print_counter); ?>" size="180" value="<?php echo $this->clean_sanitize($raw_form_str); ?>" /><input type="hidden" name="refpage<?php echo '_'.esc_attr($this->print_counter); ?>" id="refpage<?php echo '_'.esc_attr($this->print_counter); ?>" value=""><input type="hidden" name="<?php echo esc_attr($this->prefix); ?>_pform_status" value="0" /><?php
$wpts_cookieless_captcha = ( class_exists( 'CP_TimeSlotsBooking_GDPR' ) && CP_TimeSlotsBooking_GDPR::get_setting( 'captcha_cookieless', '1' ) === '1' );
if ( $wpts_cookieless_captcha ) {
    echo '<input type="hidden" name="cptslotsb_ctoken" id="cptslotsb_ctoken_' . intval( $this->print_counter ) . '" value="" />';
}
?>
<?php if (is_admin() && !defined('WPTIMESLOTSBK_ELEMENTOR_EDIT_MODE') && (empty($_GET["action"]) || @$_GET["action"] != 'edit')) { ?>
  <fieldset class="ahbadminoptions" style="background:#f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid #0d9488; border-radius: 8px; padding: 14px 18px; margin-bottom: 22px;">
   <legend style="font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #0f766e; padding: 0 6px;">⚙️ <?php _e('Administrator options','wp-time-slots-booking-form'); ?></legend>
    <div style="display:flex; flex-wrap:wrap; align-items:center; gap:20px; padding-top:4px;">
        <label style="display:inline-flex; align-items:center; gap:8px; font-size:13.5px; font-weight:600; color:#0f172a; cursor:pointer;">
            <input type="checkbox" name="sendemails_admin" value="1" vt="1" checked style="margin:0;" />
            <?php _e('Send notification emails for this booking','wp-time-slots-booking-form'); ?>
        </label>
        <div id="wptsremoveval" style="display:inline-flex; align-items:center;">
            <label style="display:inline-flex; align-items:center; gap:8px; font-size:13.5px; font-weight:600; color:#0f172a; cursor:pointer;">
                <input type="checkbox" name="ignorewptsval" value="1" vt="1" onclick="wptsingnoreval();" style="margin:0;" />
                <?php _e('Ignore validation of required fields?','wp-time-slots-booking-form'); ?>
            </label>
        </div>
        <div id="wptsremovedval" style="display:none; align-items:center; gap:6px; background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; padding:3px 10px; border-radius:6px; font-size:12.5px; font-weight:600;">
            <input type="checkbox" name="ignorewptsval" value="1" vt="1" checked disabled style="margin:0;" />
            <?php echo esc_js(__('Required field validation ignored!','wp-time-slots-booking-form')); ?>
        </div>
    </div>
    <script>
        function wptsingnoreval() {
           jQuery(".required").removeClass("required");
           jQuery("#wptsremoveval").hide();
           jQuery("#wptsremovedval").css("display", "inline-flex");
         }
    </script>
  </fieldset> 
<?php } ?>                         
<div id="fbuilder">    
    <div id="fbuilder<?php echo '_'.esc_attr($this->print_counter); ?>">
        <div id="formheader<?php echo '_'.esc_attr($this->print_counter); ?>"></div>
        <div id="fieldlist<?php echo '_'.esc_attr($this->print_counter); ?>"></div>
    </div>
</div>
<div style="display:none">
<div id="cpcaptchalayer<?php echo '_'.esc_attr($this->print_counter); ?>" class="cpcaptchalayer">
 <div class="fields" id="field-c0" style="display:none">
    <label></label>
    <div class="dfield"><!--addons-payment-options--></div>
    <div class="clearer"></div>
 </div><!--addons-payment-fields-->
<?php if (!is_admin() && $this->get_option('cv_enable_captcha', CP_TSLOTSBOOK_DEFAULT_cv_enable_captcha) != 'false' && $this->get_option('cv_enable_captcha', CP_TSLOTSBOOK_DEFAULT_cv_enable_captcha) != false) { ?>
  <?php
  $wpts_seq = '_' . intval($this->print_counter);
  $wpts_captcha_input_id = 'hdcaptcha_' . $this->prefix . '_post' . $wpts_seq;
  $wpts_captcha_base_url = $this->get_site_url().'/?'.$this->prefix.'_captcha=captcha&ps=_'.$this->print_counter.'&inAdmin=1&width='.$this->get_option('cv_width', CP_TSLOTSBOOK_DEFAULT_cv_width).'&height='.$this->get_option('cv_height', CP_TSLOTSBOOK_DEFAULT_cv_height).'&letter_count='.$this->get_option('cv_chars', CP_TSLOTSBOOK_DEFAULT_cv_chars).'&min_size='.$this->get_option('cv_min_font_size', CP_TSLOTSBOOK_DEFAULT_cv_min_font_size).'&max_size='.$this->get_option('cv_max_font_size', CP_TSLOTSBOOK_DEFAULT_cv_max_font_size).'&noise='.$this->get_option('cv_noise', CP_TSLOTSBOOK_DEFAULT_cv_noise).'&noiselength='.$this->get_option('cv_noise_length', CP_TSLOTSBOOK_DEFAULT_cv_noise_length).'&bcolor='.$this->get_option('cv_background', CP_TSLOTSBOOK_DEFAULT_cv_background).'&border='.$this->get_option('cv_border', CP_TSLOTSBOOK_DEFAULT_cv_border).'&font='.$this->get_option('cv_font', CP_TSLOTSBOOK_DEFAULT_cv_font);
  ?>
  <div class="wpts-captcha-box">
    <label class="wpts-captcha-label" for="<?php echo esc_attr($wpts_captcha_input_id); ?>">
      <svg class="wpts-captcha-shield" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
      <span><?php esc_html_e("Security Code",'wp-time-slots-booking-form'); ?></span><span class="r">*</span>
    </label>
    <div class="wpts-captcha-row">
      <div class="wpts-captcha-media">
        <?php if ( $wpts_cookieless_captcha ) : ?>
        <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==" id="captchaimg<?php echo esc_attr($wpts_seq); ?>" class="skip-lazy wpts-captcha-img<?php echo esc_attr($wpts_seq); ?>" data-seq="<?php echo esc_attr($wpts_seq); ?>" onclick="if(window.wptsRefreshCaptchaBtn)window.wptsRefreshCaptchaBtn(this);" alt="<?php esc_attr_e('Security code','wp-time-slots-booking-form'); ?>" border="0" />
        <?php else : ?>
        <img src="<?php echo esc_url( $wpts_captcha_base_url ); ?>" id="captchaimg<?php echo esc_attr($wpts_seq); ?>" class="skip-lazy wpts-captcha-img<?php echo esc_attr($wpts_seq); ?>" data-seq="<?php echo esc_attr($wpts_seq); ?>" onclick="if(window.wptsRefreshCaptchaBtn)window.wptsRefreshCaptchaBtn(this);" alt="<?php esc_attr_e('Security code','wp-time-slots-booking-form'); ?>" border="0" />
        <?php endif; ?>
        <button type="button" class="wpts-captcha-refresh" data-seq="<?php echo esc_attr($wpts_seq); ?>" title="<?php esc_attr_e('Refresh security code','wp-time-slots-booking-form'); ?>" aria-label="<?php esc_attr_e('Refresh security code','wp-time-slots-booking-form'); ?>" onclick="if(window.wptsRefreshCaptchaBtn)window.wptsRefreshCaptchaBtn(this);return false;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
        </button>
      </div>
      <div class="dfield wpts-captcha-input-wrap">
        <input type="text" class="wpts-captcha-input" size="20" name="hdcaptcha_<?php echo esc_attr($this->prefix); ?>_post" id="<?php echo esc_attr($wpts_captcha_input_id); ?>" value="" autocomplete="off" spellcheck="false" placeholder="<?php esc_attr_e('Enter the security code','wp-time-slots-booking-form'); ?>" aria-label="<?php esc_attr_e('Please enter the security code','wp-time-slots-booking-form'); ?>" />
        <div class="cpefb_error message" id="hdcaptcha_error<?php echo esc_attr($wpts_seq); ?>" generated="true" style="display:none;position: absolute; left: 0px; top: 25px;"><?php echo esc_html($this->translate_dynamic($this->get_option('cv_text_enter_valid_captcha', CP_TSLOTSBOOK_DEFAULT_cv_text_enter_valid_captcha))); ?></div>
        <span class="uh wpts-captcha-help"><?php esc_html_e("Please enter the security code",'wp-time-slots-booking-form'); ?></span>
      </div>
    </div>
  </div>
<?php } ?><!-- rcadon -->
</div>
</div>
<div id="cp_subbtn<?php echo '_'.esc_attr($this->print_counter); ?>" class="cp_subbtn"><?php echo esc_html($button_label); ?></div>
<div style="clear:both;"></div>
</form>
<?php if ( ! is_admin() && $this->get_option('cv_enable_captcha', CP_TSLOTSBOOK_DEFAULT_cv_enable_captcha) != 'false' && $this->get_option('cv_enable_captcha', CP_TSLOTSBOOK_DEFAULT_cv_enable_captcha) != false ) : ?>
<!--noptimize--><script type="text/javascript">
(function(){
    if (typeof window.wptsRefreshCaptchaBtn !== 'function') {
        window.wptsRefreshCaptchaBtn = function(el) {
            var seq = (el && el.getAttribute) ? (el.getAttribute('data-seq') || '') : '';
            if (seq && typeof window['wptsRefreshCaptcha' + seq] === 'function') {
                window['wptsRefreshCaptcha' + seq]();
                return;
            }
            var box = (el && el.closest) ? el.closest('.wpts-captcha-box') : null;
            var imgs = box
                ? box.querySelectorAll('img')
                : document.querySelectorAll('img[id="captchaimg' + seq + '"], img.wpts-captcha-img' + seq);
            for (var k = 0; imgs.length > k; k++) {
                var src = imgs[k].getAttribute('src') || '';
                if (src) {
                    src = src.replace(/([?&])t=\d+/g, '$1').replace(/[?&]$/, '');
                    imgs[k].setAttribute('src', src + (src.indexOf('?') === -1 ? '?' : '&') + 't=' + Date.now());
                }
            }
        };
    }
    <?php if ( $wpts_cookieless_captcha ) : ?>
    var baseSrc = <?php echo wp_json_encode( esc_url_raw( $wpts_captcha_base_url ) ); ?>;
    var seq = "<?php echo '_'.intval( $this->print_counter ); ?>";
    function wptsGenToken() {
        var arr = new Uint8Array(16);
        if (window.crypto && window.crypto.getRandomValues) {
            window.crypto.getRandomValues(arr);
        } else {
            for (var i = 0; 16 > i; i++) arr[i] = Math.floor(Math.random() * 256);
        }
        return Array.prototype.map.call(arr, function(b){ return ('0' + b.toString(16)).slice(-2); }).join('');
    }
    window['wptsRefreshCaptcha' + seq] = function() {
        var tok = wptsGenToken();
        var newSrc = baseSrc + '&ctoken=' + encodeURIComponent(tok) + '&t=' + Date.now();
        var inputs = document.querySelectorAll('[id="cptslotsb_ctoken' + seq + '"]');
        for (var i = 0; inputs.length > i; i++) {
            inputs[i].value = tok;
        }
        var imgs = document.querySelectorAll('img[id="captchaimg' + seq + '"], img.wpts-captcha-img' + seq);
        for (var j = 0; imgs.length > j; j++) {
            imgs[j].src = newSrc;
        }
    };
    window['wptsRefreshCaptcha' + seq]();
    <?php endif; ?>
})();
</script><!--/noptimize-->
<?php endif; ?>