	$.fbuilder.typeList.push({
		id: 'facceptance',
		name: 'Accept/GDPR',
		control_category: 1
	});

	$.fbuilder.controls['facceptance'] = function () {};
	$.extend(
		$.fbuilder.controls['facceptance'].prototype,
		$.fbuilder.controls['ffields'].prototype,
		{
			title: 'Accept terms and conditions',
			ftype: 'facceptance',
			value: 'I accept',
			url: '',
			message: '',
			required: true,
			display: function () {
				var hasLink = !!(this.url && $.trim(this.url) !== '') || !!(this.message && $.trim(this.message) !== '');
				var str = '<label class="wpts-acceptance-preview">' +
					'<input class="field" disabled="disabled" type="checkbox" />' +
					'<span>' + $.fbuilder.htmlEncode(this.title) + (this.required ? '<span class="wpts-req-star">*</span>' : '') + '</span>' +
					(hasLink ? '<span class="wpts-meta-pill">Consent link</span>' : '') +
				'</label>';

				return '<div class="fields ' + this.name + '" id="field' + this.form_identifier + '-' + this.index + '" title="' + $.fbuilder.htmlEncode(this.name) + '">' +
					'<div class="arrow ui-icon ui-icon-play"></div>' +
					'<div title="Delete" class="remove ui-icon ui-icon-trash"></div>' +
					'<div title="Duplicate" class="copy ui-icon ui-icon-copy"></div>' +
					'<div class="dfield">' + str + '<span class="uh">' + $.fbuilder.htmlEncode(this.userhelp) + '</span></div>' +
					'<div class="clearer"></div>' +
				'</div>';
			},
			editItemEvents: function () {
				var me = this;
				var evt = [
					{ s: '#sValue', e: 'input change keyup', l: 'value' },
					{ s: '#sURL', e: 'input change keyup', l: 'url' },
					{ s: '#sMessage', e: 'input change keyup', l: 'message' }
				];
				$('#wptsUsePrivacyUrlBtn').on('click', function (e) {
					e.preventDefault();
					var url = (window.wptsAdminConfig && window.wptsAdminConfig.privacyPolicyUrl) ? window.wptsAdminConfig.privacyPolicyUrl : '';
					if (url) {
						$('#sURL').val(url).trigger('change');
						me.url = url;
						$.fbuilder.reloadItems({ field: me });
					}
				});
				$.fbuilder.controls['ffields'].prototype.editItemEvents.call(this, evt);
			},
			showRequired: function () {
				return '<div class="wpts-setting-group">' +
					'<label class="wpts-checkbox-row wpts-checkbox-card">' +
						'<input type="checkbox" checked disabled />' +
						'<span>Acceptance fields are always required</span>' +
					'</label>' +
				'</div>';
			},
			showUserhelp: function () {
				return '';
			},
			showValue: function () {
				return '<div class="wpts-setting-group">' +
					'<label for="sValue">Submitted value</label>' +
					'<input class="large" type="text" name="sValue" id="sValue" value="' + $.fbuilder.htmlEncode(this.value) + '" placeholder="I accept" />' +
				'</div>';
			},
			showURL: function () {
				var hasPrivacyUrl = !!(window.wptsAdminConfig && window.wptsAdminConfig.privacyPolicyUrl);
				return '<div class="wpts-setting-group">' +
					'<div class="wpts-label-action-row">' +
						'<label for="sURL">URL to Consent / Terms page</label>' +
						(hasPrivacyUrl ? '<button type="button" id="wptsUsePrivacyUrlBtn" class="wpts-link-action-btn">Use site Privacy Policy</button>' : '') +
					'</div>' +
					'<input class="large" type="text" name="sURL" id="sURL" value="' + $.fbuilder.htmlEncode(this.url) + '" placeholder="https://example.com/terms" />' +
				'</div>';
			},
			showMessage: function () {
				return '<div class="wpts-setting-group">' +
					'<label for="sMessage">Or enter Consent / Acknowledgement text</label>' +
					'<textarea class="large" name="sMessage" id="sMessage" rows="5" placeholder="Optional popup text shown when clicked...">' + $.fbuilder.htmlEncode(this.message) + '</textarea>' +
				'</div>';
			},
			showSpecialDataInstance: function () {
				var notice = '<div class="wpts-gdpr-notice">' +
					'<strong>GDPR Compliance:</strong> This control records explicit user consent before the booking can be submitted.' +
				'</div>';
				return this.showValue() + this.showURL() + this.showMessage() + notice;
			}
		}
	);
