function cp_timeslots_renderForm(id) {
    var $iframe = jQuery('iframe[name="editor-canvas"]');
    var inIframe = $iframe.length > 0;
    
    // Search the iframe if we are in the editor, otherwise search the main document
    var $context = inIframe ? $iframe.contents() : jQuery(document);

    var $formStructure = jQuery("#form_structure" + id, $context);
    var $fbuilderDiv   = jQuery("#fbuilder_" + id, $context);

    if ($formStructure.length && $fbuilderDiv.length) {      
        
        var $tempContainer;
        var $structurePlaceholder;
        var $builderPlaceholder;

        if (inIframe) {
            // 1. Create invisible placeholders (namespaced with cptsb_) to prevent conflicts
            $structurePlaceholder = jQuery('<div style="display:none;" id="cptsb_placeholder_struct_' + id + '"></div>');
            $builderPlaceholder   = jQuery('<div style="display:none;" id="cptsb_placeholder_build_' + id + '"></div>');
            
            $formStructure.before($structurePlaceholder);
            $fbuilderDiv.before($builderPlaceholder);

            // 2. Create a fake <form> in the main document. Namespaced to prevent conflict.
            $tempContainer = jQuery('<form id="cptsb_temp_form_' + id + '" style="position:fixed; top:0; left:0; width:800px; height:800px; visibility:hidden; z-index:-9999;"></form>').appendTo('body');
            
            // Move elements to the main document
            $tempContainer.append($formStructure).append($fbuilderDiv);
        }

        try {
            // 3. Initialize the form builder
            var cp_appbooking_fbuilder_myconfig = {"obj":"{\"pub\":true,\"identifier\":\"_"+id+"\",\"messages\": {}}"};
            var f = jQuery("#fbuilder_" + id).fbuilder(jQuery.parseJSON(cp_appbooking_fbuilder_myconfig.obj));
            f.fBuild.loadData("form_structure" + id);
        } catch(e) {
            console.error("Time Slots Builder Error:", e);
        }

        if (inIframe) {
            // 4. Wait half a second to allow all asynchronous calendar math/rendering to finish
            setTimeout(function() {
                // Move elements back to their exact original locations inside the iframe
                $structurePlaceholder.before($formStructure).remove();
                $builderPlaceholder.before($fbuilderDiv).remove();
                
                // Clean up our temporary form
                $tempContainer.remove();
            }, 500); 
        }

    } else {
        // If the ServerSideRender hasn't fetched the PHP HTML yet, wait and try again
        setTimeout(function() { 
            cp_timeslots_renderForm(id); 
        }, 100);
    }
}

jQuery(function() {             
    (function( blocks, element, blockEditor, components, serverSideRender ) {
        var el = element.createElement;
        var Fragment = element.Fragment;
        var useEffect = element.useEffect;
        
        // Handle backward compatibility just in case WP version is older
        var InspectorControls = blockEditor ? blockEditor.InspectorControls : window.wp.editor.InspectorControls;        
        var SelectControl = components.SelectControl;
        var PanelBody = components.PanelBody;
        var ServerSideRender = serverSideRender;

        var iconWPTSB = el('img', { width: 20, height: 20, src:'data:image/gif;base64,R0lGODlhFQAVAMQAAP//////AP8A//8AAAD//wD/AAAA/wAAAAU6gAY8gAlFiwlFigtMkA9TlhZioxhkpBtqqRxrqSR1sSd6tCp8tDuPwUibyEueymGu1HS73IzK5JvS6KjZ6+75/P///wAAACH5BAEAAB4ALAAAAAAVABUAAAWDYCd229Jko4glD5emUnQx71JN1DtCAIC8iB4kZUEYg77jsae0FJdJJbL5NDoiUuOkQc16s86v2Ogc6nSAXbnTa7vfaci6A6mL6mb8fa6n2/15a3CDPXR8f32Jh3mIiGWEhIYWjJR3jpOWlYB7mCJpI5+eahYKeKanpwoWGk6trq+vGiEAOw==' } );               
                
        /* Plugin Category */
        var categorySlug = 'cptimeslotsbk';
        var categoryExists = blocks.getCategories().some(function(cat) { return cat.slug === categorySlug; });
        if (!categoryExists) {
            blocks.getCategories().push({slug: categorySlug, title:'WP Time Slots Booking Form'});
        }

        blocks.registerBlockType( 'cptimeslots/form-rendering', {
            apiVersion: 3, 
            title: 'WP Time Slots Booking Form', 
            icon: iconWPTSB,    
            category: 'cptimeslotsbk',
            supports: {
                customClassName: false,
                className: false
            },
            attributes: {
                formId: { type: 'string' },
                instanceId: { type: 'string' }
            },           
            edit: function( props ) {             
                var attributes = props.attributes;
                var setAttributes = props.setAttributes;
                var isSelected = props.isSelected;
                var formOptions = typeof cptimeslots_forms !== 'undefined' ? cptimeslots_forms.forms : [];

                useEffect(function() {
                    if (!formOptions.length) return;
                    
                    var currentFormId = attributes.formId;
                    var currentInstanceId = attributes.instanceId;
                    var needsUpdate = false;

                    if (!currentInstanceId) {                        
                        currentInstanceId = formOptions[0].value + parseInt(Math.random() * 100000, 10);
                        needsUpdate = true;
                    }
                    if (!currentFormId) {
                        currentFormId = formOptions[0].value;
                        needsUpdate = true;
                    }

                    if (needsUpdate) {
                        setAttributes({ formId: currentFormId, instanceId: currentInstanceId });
                    }

                    if (currentInstanceId) {
                        cp_timeslots_renderForm(currentInstanceId);
                    }
                }, []); 

                if (!formOptions.length) {
                    return el("div", null, 'Please create a booking form first.');
                }
                                                       
                return el(
                    Fragment, 
                    null,
                    isSelected && el(
                        InspectorControls,
                        { key: 'cptimeslotsbk_inspector' },
                        el(
                            PanelBody,
                            { title: 'Help & Support' },
                            el('span', { style: { fontStyle: 'italic' } }, 'If you need help: '),
                            el('a', { href: 'https://wptimeslot.dwbooster.com/contact-us', target: '_blank' }, 'CLICK HERE')
                        )
                    ),			    		
                    el(SelectControl, {
                        value: attributes.formId,
                        options: formOptions,
                        onChange: function(evt) {         
                            var newInstanceId = evt + parseInt(Math.random() * 100000, 10);
                            setAttributes({ formId: evt, instanceId: newInstanceId });
                            cp_timeslots_renderForm(newInstanceId);                                   
                        }
                    }),
                    el(ServerSideRender, {
                        block: "cptimeslots/form-rendering",
                        attributes: attributes
                    })			    		
                );
            },
            save: function() {
                return null; 
            }
        });
    })(
        window.wp.blocks,
        window.wp.element,
        window.wp.blockEditor || window.wp.editor,
        window.wp.components,
        window.wp.serverSideRender
    );
});