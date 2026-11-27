<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wpdb;

$this->setId(isset($_GET["ahbcalendar"]) ? intval($_GET["ahbcalendar"]) : 0);

$current_user = wp_get_current_user();
$current_user_access = current_user_can('manage_options');

if ( !is_admin() || (!$current_user_access && !@in_array($current_user->ID, unserialize($this->get_option("cp_user_access","")))))
{
    echo 'Direct access not allowed.';
    exit;
}

if ( !is_admin() )
{
    echo 'Direct access not allowed.';
    exit;
}

$myrows = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix.$this->table_items );
if (!$this->getId() && count($myrows)) $this->setId($myrows[0]->id);

// Load template edit_style.css
$css = "";
$raw_form_str = str_replace("\r"," ",str_replace("\n"," ",$this->cleanJSON($this->translate_json($this->get_option('form_structure', CP_TSLOTSBOOK_DEFAULT_form_structure)))));
$form_data = json_decode( $raw_form_str );
if ( is_null( $form_data ) ) {
    $json = new JSON;
    $form_data = $json->unserialize( $raw_form_str );
}
$form_template = '';
if ( !is_null( $form_data ) ) {
    if ( isset( $form_data[ 1 ] ) && isset( $form_data[ 1 ][ 0 ] ) && isset( $form_data[ 1 ][ 0 ]->formtemplate ) )
    {
        $templatelist = $this->available_templates();
        $form_template = substr(md5($form_data[ 1 ][ 0 ]->formtemplate),0,10);
        if ( isset( $templatelist[ $form_data[ 1 ][ 0 ]->formtemplate ] ) )
            $file = $templatelist[ $form_data[ 1 ][ 0 ]->formtemplate ][ 'file' ];
        else 
            $file = plugins_url( 'templates/edit_style.css', __FILE__ );
        $path = parse_url($file, PHP_URL_PATH);
        $desiredPart = substr($path, strpos($path, 'templates/'));
        $tpls_dir = dir( plugin_dir_path( __FILE__ ) );          
        $ecssname = dirname($tpls_dir->path.$desiredPart)."/edit_style.css";
        if (file_exists($ecssname))
            $css = file_get_contents($ecssname);
    }      
}

$message = '';
if ( isset( $_POST['csseditionon'] ) ) 
{
    $this->verify_nonce(sanitize_text_field($_POST["anonce"]), 'cpappb_actions_csseditor');
    if (!empty($_POST["savecss"])) 
    {
        update_option('WPTS_CUSTOM_CSS_'.$this->getId().$form_template, sanitize_text_field($_POST["customcsscontents"]));
        $message = __('Styles saved','wp-time-slots-booking-form'); 
    } 
    else if (!empty($_POST["resetcss"])) 
    {
        update_option('WPTS_CUSTOM_CSS_'.$this->getId().$form_template, '');
        $message = __('Styles restored to original','wp-time-slots-booking-form'); 
    }
}

$nonce = wp_create_nonce( 'cpappb_actions_csseditor' );
$active_form_name = $this->item ? $this->get_option("form_name", "Calendar") : '';
?>

<style type="text/css">
/* Page-specific styles: Visual Styles Editor Stage & Live Inspector Modal */
    .ahbadminoptions { display: none !important; }
    .wpts-studio-bar {
        padding: 16px 24px;
        background: #fcfcfd;
        border-bottom: 1px solid var(--wpts-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
    }
    .wpts-studio-group {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .wpts-studio-group label {
        font-size: 13px;
        font-weight: 600;
        color: var(--wpts-text);
    }
    .wpts-studio-bar select {
        font-weight: 600;
        min-width: 220px;
    }
    .wpts-studio-hint {
        padding: 12px 24px;
        background: var(--wpts-accent-light);
        border-bottom: 1px solid var(--wpts-accent-border);
        color: #0369a1;
        font-size: 13px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }
    #ahbcustomizationarea {
        padding: 32px 28px;
        background-color: #f8fafc;
        background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
        background-size: 20px 20px;
        min-height: 360px;
    }
    .wpts-stage-inner {
        background: #ffffff;
        border: 1px solid var(--wpts-border);
        border-radius: var(--wpts-radius);
        padding: 28px;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
        max-width: 960px;
        margin: 0 auto;
    }
    .cp_csseditor {
        outline: 2px dashed var(--wpts-accent) !important;
        outline-offset: 2px;
        cursor: pointer !important;
        transition: outline 0.1s ease;
    }
    .cp_csseditor .cp_csseditor {
        outline: 2px dashed var(--wpts-primary) !important;
    }
    .cp_csseditor .cp_csseditor .cp_csseditor {
        outline: 2px solid #f59e0b !important;
    }
    #modalBackground {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(15, 23, 42, 0.55);
        backdrop-filter: blur(4px);
        z-index: 9999998;
    }
    #myModal {
        display: none;
        position: fixed;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        width: 92%;
        max-width: 580px;
        max-height: 82vh;
        padding: 0;
        background-color: #ffffff;
        border: 1px solid var(--wpts-border);
        border-radius: var(--wpts-radius);
        box-shadow: 0 20px 30px -10px rgba(15, 23, 42, 0.28);
        z-index: 9999999;
        overflow-y: auto;
    }
    .wpts-modal-topbar {
        position: sticky;
        top: 0;
        z-index: 5;
        background: #fcfcfd;
        border-bottom: 1px solid var(--wpts-border);
        padding: 14px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .wpts-modal-topbar strong {
        font-size: 14px;
        color: var(--wpts-text);
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .close-button {
        background: var(--wpts-bg);
        border: 1px solid var(--wpts-border);
        border-radius: 6px;
        padding: 4px 10px;
        color: var(--wpts-text-muted);
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .close-button:hover,
    .close-button:focus {
        background: var(--wpts-danger-bg);
        border-color: #fecaca;
        color: var(--wpts-danger);
        text-decoration: none;
    }
    #editor {
        padding: 20px;
    }
    #editor .rule-container {
        margin-bottom: 18px;
    }
    #editor .rule-container h3 {
        margin: 0 0 12px;
        font-size: 13.5px;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        color: var(--wpts-primary-hover);
        background: var(--wpts-primary-light);
        border: 1px solid var(--wpts-primary-border);
        padding: 8px 12px;
        border-radius: 6px;
    }
    #editor table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid var(--wpts-border);
        border-radius: 8px;
        overflow: hidden;
    }
    #editor table td {
        padding: 8px 10px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
        vertical-align: middle;
    }
    #editor input.inputText,
    #editor input.colorValue {
        padding: 5px 8px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 12.5px;
        color: var(--wpts-text);
        width: 140px;
    }
    #editor input.colorPicker,
    #editor input.newValue[type="color"] {
        width: 34px;
        height: 30px;
        padding: 1px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        vertical-align: middle;
        margin-right: 6px;
        cursor: pointer;
        background: #ffffff;
    }
    #editor select.newProperty,
    #editor select.newValue {
        padding: 5px 28px 5px 8px !important;
        background-position: right 6px center;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 12.5px;
        margin-right: 6px;
        min-height: 32px;
    }
    #editor button.delProperty {
        background: var(--wpts-danger-bg);
        color: #dc2626;
        border: 1px solid #fecaca;
        border-radius: 6px;
        padding: 4px 10px;
        font-size: 11.5px;
        font-weight: 600;
        cursor: pointer;
    }
    #editor button.delProperty:hover {
        background: var(--wpts-danger);
        color: #ffffff;
    }
    #editor button.addProperty {
        background: var(--wpts-primary);
        color: #ffffff;
        border: none;
        border-radius: 6px;
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
    }
    #editor button.addProperty:hover {
        background: var(--wpts-primary-hover);
    }
    .divbuttonCode {
        position: relative;
        border-radius: 8px;
        margin-top: 14px;
        background-color: #0f172a;
        color: #e2e8f0;
        padding: 12px 14px;
        overflow: hidden;
    }
    .divbuttonCode pre.preCode {
        margin: 0 !important;
        padding-right: 110px;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 12px;
        line-height: 1.5;
        white-space: pre-wrap;
        word-break: break-all;
        color: #38bdf8;
    }
    .buttonCode {
        position: absolute;
        right: 10px;
        top: 10px;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 6px;
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        color: #ffffff;
        transition: all 0.15s ease;
    }
    .buttonCode:hover {
        background: var(--wpts-primary);
        border-color: var(--wpts-primary);
    }
</style>

<div class="wrap wpts-admin-wrap">

    <?php if (!empty($message)) : ?>
    <div id="setting-error-settings_updated" class="wpts-toast">
        <span>✅ <strong><?php echo esc_html($message); ?></strong></span>
        <button type="button" class="wpts-btn wpts-btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="this.parentElement.style.display='none'">Dismiss</button>
    </div>
    <?php endif; ?>

    <!-- Top Hero & Navigation Hub -->
    <div class="wpts-hero-shell">
        <div class="wpts-hero-top">
            <div class="wpts-brand-group">
                <div class="wpts-brand-icon">🎨</div>
                <div class="wpts-title-group">
                    <h1>
                        <?php esc_html_e('Visual Styles Editor', 'wp-time-slots-booking-form'); ?>
                        <?php if ($active_form_name) : ?>
                            <span class="wpts-badge"><?php echo esc_html($active_form_name); ?> (#<?php echo intval($this->item); ?>)</span>
                        <?php endif; ?>
                    </h1>
                    <p class="wpts-subtitle"><?php esc_html_e('Click any element on your live booking form preview below to customize its colors, typography, borders, and spacing.', 'wp-time-slots-booking-form'); ?></p>
                </div>
            </div>

            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                <?php if ($this->item) : ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page='.$this->menu_parameter.'&cal='.intval($this->item))); ?>" class="wpts-btn wpts-btn-secondary">
                    ⚙️ <?php esc_html_e('Edit Calendar Settings', 'wp-time-slots-booking-form'); ?>
                </a>
                <?php endif; ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page='.$this->menu_parameter)); ?>" class="wpts-btn wpts-btn-secondary">
                    &larr; <?php esc_html_e('Return to Calendars List', 'wp-time-slots-booking-form'); ?>
                </a>
            </div>
        </div>

        <?php $this->render_admin_subnav('csseditor'); ?>
    </div>

    <!-- Main Visual Studio Card -->
    <div class="wpts-card">
        <!-- Studio Control Bar -->
        <div class="wpts-studio-bar">
            <form action="admin.php" method="get" class="wpts-studio-group" style="margin:0;">
                <input type="hidden" name="page" value="<?php echo esc_attr($this->menu_parameter); ?>_csseditor_page" />           
                <input type="hidden" name="anonce" value="<?php echo esc_attr($nonce); ?>" />        
                <label for="ahbcalendar">🗓️ <?php esc_html_e('Calendar:', 'wp-time-slots-booking-form'); ?></label>
                <select id="ahbcalendar" name="ahbcalendar">        
                    <?php
                    $saved_id = $this->getId();
                    foreach ($myrows as $item)
                    {        
                        $this->setId($item->id);
                        if ($current_user_access || @in_array($current_user->ID, unserialize($this->get_option("cp_user_access",""))))
                            echo '<option value="'.intval($item->id).'"'.(intval($item->id)==intval($saved_id)?" selected":"").'>#'.intval($item->id).' — '.esc_html($item->form_name).'</option>';
                    }    
                    $this->setId($saved_id);
                    ?>
                </select>          
                <button type="submit" name="<?php echo esc_attr($this->prefix); ?>_load" value="1" class="wpts-btn wpts-btn-secondary">
                    🔄 <?php esc_html_e('Load Form', 'wp-time-slots-booking-form'); ?>
                </button>
            </form>

            <form action="" name="saverestoreform" method="post" onsubmit="return confirmoption();" class="wpts-studio-group" style="margin:0;">
                <input type="hidden" name="page" value="<?php echo esc_attr($this->menu_parameter); ?>_csseditor_page" />  
                <input type="hidden" name="csseditionon" value="1" />          
                <input type="hidden" name="anonce" value="<?php echo esc_attr($nonce); ?>" />        
                <input type="hidden" name="customcsscontents" id="customcsscontents" value="<?php echo esc_attr(get_option('WPTS_CUSTOM_CSS_'.$this->getId().$form_template,"")); ?>" />        

                <input type="submit" name="resetcss" class="wpts-btn wpts-btn-danger-soft" value="↺ <?php esc_attr_e('Restore Original Styles', 'wp-time-slots-booking-form'); ?>" />
                <input type="submit" name="savecss" class="wpts-btn wpts-btn-primary" value="💾 <?php esc_attr_e('Save Edited Styles', 'wp-time-slots-booking-form'); ?>" />
            </form>
        </div>

        <!-- Studio Hint Strip -->
        <div class="wpts-studio-hint">
            <span>👆 <strong><?php esc_html_e('Interactive Inspector:', 'wp-time-slots-booking-form'); ?></strong> <?php esc_html_e('Hover over any calendar header, day cell, time-slot button, or input field below and click it to open the live CSS property editor.', 'wp-time-slots-booking-form'); ?></span>
            <span style="font-weight:600;">* <?php esc_html_e('Remember to click "Save Edited Styles" when finished.', 'wp-time-slots-booking-form'); ?></span>
        </div>

        <!-- Live Form Stage -->
        <div id="ahbcustomizationarea">
            <div id="modalBackground"></div>
            <div id="myModal">
                <div class="wpts-modal-topbar">
                    <strong>🖌️ <?php esc_html_e('Element Style Inspector', 'wp-time-slots-booking-form'); ?></strong>
                    <button type="button" class="close-button">✕ <?php esc_html_e('Close', 'wp-time-slots-booking-form'); ?></button>
                </div>
                <div id="editor"></div>	
            </div>

            <div class="wpts-stage-inner">
                <?php echo $this->clean_sanitize($this->filter_content(array('id' => $this->item))); ?>
            </div>
        </div>
    </div>

    <!-- Pre-defined Templates Tip Card -->
    <div class="wpts-card">
        <div class="wpts-card-body" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:24px;padding:24px;">
            <div style="flex:1;min-width:280px;">
                <h3 style="margin:0 0 8px;font-size:16px;font-weight:700;color:var(--wpts-text);">
                    💡 <?php esc_html_e('Remember that you can also select pre-defined style templates', 'wp-time-slots-booking-form'); ?>
                </h3>
                <p style="margin:0 0 14px;font-size:13.5px;color:var(--wpts-text-muted);line-height:1.55;">
                    <?php esc_html_e('In the Form Builder under the "Form Settings" tab, you can switch between built-in design templates before fine-tuning individual elements here in the Visual Styles Editor.', 'wp-time-slots-booking-form'); ?>
                </p>
                <?php if ($this->item) : ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page='.$this->menu_parameter.'&cal='.intval($this->item))); ?>" class="wpts-btn wpts-btn-soft">
                    🎨 <?php esc_html_e('Open Form Builder Settings', 'wp-time-slots-booking-form'); ?> &rarr;
                </a>
                <?php endif; ?>
            </div>
            <div>
                <a href="<?php echo esc_url(plugins_url('/images/selecting-templates.png', __FILE__)); ?>" target="_blank" rel="noopener noreferrer">
                    <img src="<?php echo esc_url(plugins_url('/images/selecting-templates.png', __FILE__)); ?>" alt="Selecting Form Templates" style="max-width:100%;width:380px;border:1px solid var(--wpts-border);border-radius:var(--wpts-radius-sm);padding:8px;background-color:#ffffff;box-shadow:var(--wpts-shadow);display:block;" />
                </a>
            </div>
        </div>
    </div>

</div>

<script>
function confirmoption() {
    var form = document.saverestoreform;
    if (form.resetcss && form.resetcss === document.activeElement) {
        return confirm('<?php echo esc_js(__('Are you sure you want to restore original styles?', 'wp-time-slots-booking-form')); ?>');
    }
    return true;
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        jQuery('#modalBackground, #myModal').fadeOut();
        jQuery('.cp_csseditor').removeClass('cp_csseditor');
    }
});

jQuery(document).on("showHideDepEvent", function(){
    (function($) {   
        function clickFirstSlot(){
            if ($(".availableslot a").length > 0)
            {
                $(".cp_csseditor").removeClass("cp_csseditor");
                $(".availableslot a").click();
            }    
            else
                setTimeout(clickFirstSlot, 100); 
        }
        clickFirstSlot(); 
        $.datepicker._attachHandlers = function() {
            return false;
        };
            
        let cssString = `
<?php echo esc_html($css); ?>
        `;
        if ($("#customcsscontents").val() != "")
            cssString = $("#customcsscontents").val();
        const rulesArray = parseCssString(cssString);
        generateModifiedCss(rulesArray);
        updateRulesArray(rulesArray);
        
        $(document).on('input', ".colorValue", function() {
            $(this).parent().find('.colorPicker').val($(this).val());
            updateRulesArray(rulesArray);
            generateModifiedCss(rulesArray);               
        });
        $(document).on('input', ".colorPicker", function() {
            $(this).parent().find('.colorValue').val($(this).val());
            updateRulesArray(rulesArray);
            generateModifiedCss(rulesArray); 
        });
        $(document).on('input', ".inputText", function() { 
            updateRulesArray(rulesArray);
            generateModifiedCss(rulesArray); 
        });
        $('#rangeInput').on('input', function() {
            var value = $(this).val();
            $('#textInput').val(value);
        });
        $('#textInput').on('input', function() {
            var value = $(this).val();
            $('#rangeInput').val(value);
        });

        for (let i = 0; i < rulesArray.length; i++) {
            $(document).on('mouseover', rulesArray[i].selector, function() {
                $(".cp_csseditor").each(function(){
                    if ($(this).find(rulesArray[i].selector).length == 0)
                        $(this).removeClass(".cp_csseditor");     
                });
                $(rulesArray[i].selector).addClass("cp_csseditor");
            });
            $(document).on('mouseout', rulesArray[i].selector, function() {                        
                if (!$('#myModal').is(':visible'))
                    $(rulesArray[i].selector).removeClass("cp_csseditor");
            });
            $(document).off('click', "#fieldlist_1 .pbSubmit");
            
            $(document).on('click', rulesArray[i].selector, function(event) {
                event.stopImmediatePropagation();
                event.stopPropagation();
                $("#myModal").attr('class', '').addClass(genName(rulesArray[i].selector, "_m"));
                generateCssEditor(rulesArray, i);
                $('#modalBackground, #myModal'+"."+genName(rulesArray[i].selector, "_m")).fadeIn(150);
                event.stopImmediatePropagation();
                return false;
            });
        }
        
        function genName(s, p) {
            return s.replace(/[():.#\s]/g, '_') + p;
        }

        function parseCssString(cssString) {
            const rulesArray = [];
            const mergeCss = [{s:"border",e:"width"},{s:"border",e:"color"},{s:"border",e:"style"}];
            const styleElement = document.createElement('style');
            styleElement.appendChild(document.createTextNode(cssString));
            document.head.appendChild(styleElement);
        
            Array.from(styleElement.sheet.cssRules).forEach(rule => {
                if (rule.selectorText) {
                    const ruleInfo = {
                        selector: rule.selectorText.replace(':hover', ""),
                        pseudoClass: "",
                        style: {}
                    };
                    Array.from(rule.style).forEach(property => {
                        const computedValue = rule.style.getPropertyValue(property).trim();
                        if (property === 'background-color' && !rule.cssText.includes('background-color')) {
                            property = 'background';
                        }
                        for (var i = 0; i < mergeCss.length; i++)
                            if (rule.cssText.includes(mergeCss[i].s+"-"+mergeCss[i].e) && property.startsWith(mergeCss[i].s+"-") && property.includes("-"+mergeCss[i].e))
                                property = mergeCss[i].s+"-"+mergeCss[i].e;
                        if (computedValue.startsWith('rgb')) {
                            ruleInfo.style[property] = rgbToHex(computedValue);
                        } else if (computedValue !== 'initial') {
                            ruleInfo.style[property] = computedValue;
                        }
                    });   
                    
                    if (rule.selectorText.includes(':hover')) {
                        rule.selectorText = rule.selectorText.replace(':hover', "");
                        ruleInfo.pseudoClass = ':hover';
                    }
                    if (Object.keys(ruleInfo.style).length > 0) {
                        rulesArray.push(ruleInfo);
                    }
                }
            });
        
            styleElement.remove();
            return rulesArray;
        }
        
        function rgbToHex(rgb) {
            const result = rgb.match(/\d+/g);
            if (!result) return rgb;
            return `#${((1 << 24) + (result[0] << 16) + (result[1] << 8) + +result[2]).toString(16).slice(1)}`;
        }
        
        function generateCssEditor(parsedCssRules, visible) {
            const $editor = $('#editor');
            $editor.empty();
            const cssPropertyValues = {
                'Select property': [],
                'background': ['none', 'red', 'blue', 'green', 'yellow', 'orange', 'black', 'white', '#ffffff'],
                'background-color': ['none', 'red', 'blue', 'green', 'yellow', 'orange', 'black', 'white', '#ffffff'],
                'color': ['red', 'blue', 'green', 'yellow', 'orange', 'black', 'white', '#ffffff'],
                'font-size': ['12px', '14px', '16px', '18px', '20px', '24px'],
                'font-weight': ['normal', 'bold', 'bolder', 'lighter', '100', '200', '300', '400', '500'],
                'font-family': ['auto', 'cursive', 'emoji', 'none', 'sans-serif', 'serif', 'ui-monospace'],
                'line-height': ['normal','12px', '14px', '16px', '18px', '20px', '24px'],
                'display': ['block', 'inline', 'inline-block', 'flex', 'grid', 'none'],
                'text-align': ['left', 'right', 'center', 'justify'],
                'border': ['1px solid black', '2px dashed red', '3px dotted blue'],
                'margin': ['0', '5px', '10px', '20px'],
                'padding': ['0', '5px', '10px', '20px'],
                'width': ['auto', '100%', '50%', '200px'],
                'height': ['auto', '100%', '50%', '200px'],
                'z-index': ['0', '1', '2', '100'],                
                'text-shadow': ['none', 'red', 'blue', 'green', 'yellow', 'orange', 'black', 'white'],
                'box-shadow': ['none', 'red', 'blue', 'green', 'yellow', 'orange', 'black', 'white'],
                'border-radius': ['inherit','2px', '4px', '6px', '10%', '20%', '50%'],
                'opacity': ['0', '0.5', '1'],
            };

            parsedCssRules.forEach((rule, index) => {
                const $ruleContainer = $('<div>').addClass('rule-container').attr('data-index', index);
                if (visible != index)
                    $ruleContainer.hide();
                if (rule.pseudoClass != "" && visible == index - 1)
                    $ruleContainer.show();
                $ruleContainer.append(`<h3>${rule.selector}${rule.pseudoClass}</h3>`);
                
                const $table = $('<table cellpadding="5" cellspacing="0" width="100%">');
        
                Object.entries(rule.style).forEach(([property, value]) => {
                    const $tr = $('<tr>');
                    let $label = $('<td style="font-weight:600;color:#334155;">').text(property);
                    let $input, $input1, $inputCell; 
                    if (property.includes('-color') || property === 'color' || property === 'background') {
                        $input = $('<input class="colorPicker">').attr({
                            type: 'color',
                            value: value || '#ffffff'
                        });
                        $input1 = $('<input class="colorValue">').attr({
                            type: 'text',
                            value: value || '#ffffff'
                        });                        
                        $inputCell = $('<td nowrap>').append($input).append($input1);
                    } else if (property.includes('--comment')) {
                        $label = $('<td>').html("<b>*</b>Note");                       
                        $inputCell = $('<td nowrap>').append(value.replace(/-/g, "<b>"));
                    } else {
                        $input = $('<input class="inputText">').attr({
                            type: 'text',
                            value: value
                        });
                        $inputCell = $('<td style="white-space: nowrap;">').append($input);
                    }
                    let $delProperty = $('<td style="text-align:right;">').append($('<button type="button" class="delProperty">Delete</button>'));
        
                    $tr.append($label).append($inputCell).append($delProperty);
                    $table.append($tr);
                });

                const $addPropertyRow = $('<tr style="background-color:#f8fafc;">');
                const $addPropertyLabel = $('<td style="font-weight:600;">').append('<label for="propertySelect">New Property:</label>');
                const $propertySelect = $('<select class="newProperty">');
                
                Object.keys(cssPropertyValues).forEach(prop => {
                    $propertySelect.append(`<option value="${prop}">${prop}</option>`);
                });
                
                const $addPropertyValue = $('<select class="newValue"></select>');
                const $addButton = $('<button type="button" class="addProperty">+ Add</button>');
                $addPropertyRow.append($addPropertyLabel).append($('<td>').append($propertySelect).append($addPropertyValue)).append($('<td style="text-align:right;">').append($addButton));
                $table.append($addPropertyRow);
                
                $propertySelect.on('change', function() {
                    const selectedProperty = $(this).val();
                    if (selectedProperty.includes('background') || selectedProperty.includes('color')) {
                        const $newColorInput = $('<input class="newValue">').attr({
                            type: 'color',
                            value: '#ffffff'
                        });
                        $(this).parent().find(".newValue").replaceWith($newColorInput);
                    } else {
                        const $newSelect = $('<select class="newValue"></select>');
                        (cssPropertyValues[selectedProperty] || []).forEach(value => {
                            $newSelect.append(`<option value="${value}">${value}</option>`);
                        });
                        $(this).parent().find(".newValue").replaceWith($newSelect);
                    }
                }); 

                $addButton.on('click', function() {
                    const propertyName = $addPropertyRow.find('.newProperty').val();
                    const propertyValue = $addPropertyRow.find('.newValue').val();
                    if (propertyName && propertyName !== 'Select property' && propertyValue) { 
                        let index = $(this).parents(".rule-container").attr('data-index');                       
                        parsedCssRules[index].style[propertyName] = propertyValue;
                        if (parsedCssRules[index].pseudoClass == ':hover') index--;
                        generateCssEditor(parsedCssRules, index);                        
                        generateModifiedCss(parsedCssRules);
                    }
                });            
                $ruleContainer.append($table);
                const $styleElement = $('<pre class="preCode">');
                const selector = rule.selector.replace(':hover', "");
                const pseudoClass = rule.pseudoClass;
                const styles = Object.entries(rule.style)
                    .filter(([property, value]) => !property.includes('--comment'))
                    .map(([property, value]) => `${property}: ${value};`)
                    .join(' ');
        
                $styleElement.append(`${selector}${pseudoClass} { ${styles} }\n`);
                const $copyB = $('<div class="buttonCode">').html("Copy CSS rule");
                $ruleContainer.append($("<div class='divbuttonCode'>").append($styleElement).append($copyB)); 
                $editor.append($ruleContainer);
            });
        }
        
        function updateRulesArray(rulesArray) {
            $('.rule-container').each(function() {
                const $container = $(this);
                const index = $container.data('index');
                const selector = $container.find('h3').text();
                const updatedStyle = {};
            
                $container.find('input').each(function() {
                    const property = $(this).parents('tr').find('td').first().text();
                    const value = $(this).val();
                    if (property && property != "--comment" && property != "New Property:")
                        updatedStyle[property] = value;
                });
            
                rulesArray[index] = {
                    selector: selector.replace(':hover', ""),
                    pseudoClass: rulesArray[index].pseudoClass,
                    style: updatedStyle
                };
            });
        }  

        function generateModifiedCss(rulesArray) {
            let $styleElement = $('#dynamicStyles');
        
            if ($styleElement.length === 0) {
                $styleElement = $('<style>', { id: 'dynamicStyles' });
                $('body').append($styleElement);
            }
            $styleElement.empty();
            let $c = "";
            rulesArray.forEach((rule, index) => {
                const selector = rule.selector.replace(':hover', "") + rule.pseudoClass;
                let styles = Object.entries(rule.style)
                    .filter(([property, value]) => !property.includes('--comment'))
                    .map(([property, value]) => `${property}: ${value};`)
                    .join(' ');
                $c += `${selector} { ${styles} }\n`;
                
                $('.rule-container[data-index="'+index+'"] pre').text(`${selector} { ${styles} }\n`);
            });
            $styleElement.append($c);
            $("#customcsscontents").val($c);
        }
       
        $(document).on('click', ".buttonCode", function(event) {
            const preText = $(this).siblings('.preCode').text() || $('.preCode:visible').text();
            if (typeof window.wptsCopyText === 'function') {
                window.wptsCopyText(preText, this, '✓ Copied!');
            }
        });

        $(document).on('click', ".delProperty", function(event) { 
            const propertyName = $(this).parents("tr").find("td").first().text();
            let index = $(this).parents(".rule-container").attr('data-index');
            let r = rulesArray[index];
            if (r.style.hasOwnProperty(propertyName)) {
                delete r.style[propertyName];
                if (r.pseudoClass == ':hover') index--;
                generateCssEditor(rulesArray, index);                        
                generateModifiedCss(rulesArray);
            }
        });  

        $(document).on('click', ".close-button, #modalBackground", function(event) {
            $('#modalBackground, #myModal').fadeOut(150);
            $('.cp_csseditor').removeClass('cp_csseditor');
        });
        
    })(jQuery);
});         
</script>