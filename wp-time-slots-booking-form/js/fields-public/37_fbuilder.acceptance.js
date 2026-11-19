	$.fbuilder.controls['facceptance'] = function () {};
	$.extend(
		$.fbuilder.controls['facceptance'].prototype,
		$.fbuilder.controls['ffields'].prototype,
		{
			title: 'Accept terms and conditions',
			ftype: 'facceptance',
			value: 'I accept',
			required: true,
			url: '',
			message: '',
			show: function () {
				var me = this,
					dlg = '',
					label = me.title;

				if (!/^\s*$/.test(me.url)) {
					label = '<a href="' + $.fbuilder.htmlEncode($.trim(me.url)) + '" target="_blank" rel="noopener noreferrer">' + label + '</a>';
				} else if (!/^\s*$/.test(me.message)) {
					label = '<a href="#" class="cff-open-dlg" role="button">' + label + '</a>';
					dlg += '<div class="cff-dialog-backdrop hide" aria-hidden="true"></div>' +
						'<div class="cff-dialog hide" role="dialog" aria-modal="true">' +
							'<button type="button" class="cff-close-dlg" aria-label="Close"></button>' +
							'<div class="cff-dialog-content">' + me.message + '</div>' +
						'</div>';
				}

				return '<div class="fields ' + $.fbuilder.htmlEncode(me.csslayout) + ' cff-checkbox-field" id="field' + me.form_identifier + '-' + me.index + '">' +
					'<div class="dfield">' +
						'<div class="one_column">' +
							'<label class="wpts-pub-acceptance">' +
								'<input name="' + me.name + '" id="' + me.name + '" class="field required" value="' + $.fbuilder.htmlEncode(me.value) + '" vt="' + $.fbuilder.htmlEncode((/^\s*$/.test(me.value)) ? me.title : me.value) + '" type="checkbox" /> ' +
								'<span>' + $.fbuilder.htmlDecode(label) + (me.required ? '<span class="r">*</span>' : '') + '</span>' +
							'</label>' +
						'</div>' +
						dlg +
					'</div>' +
					'<div class="clearer"></div>' +
				'</div>';
			},
			after_show: function () {
				$(document)
					.off('click.wptsAcceptance', '.cff-open-dlg')
					.on('click.wptsAcceptance', '.cff-open-dlg', function (e) {
						e.preventDefault();
						var $wrap = $(this).closest('.fields');
						var $dlg = $wrap.find('.cff-dialog');
						var $backdrop = $wrap.find('.cff-dialog-backdrop');
						$backdrop.removeClass('hide');
						$dlg.removeClass('hide');
						$dlg.find('.cff-close-dlg').trigger('focus');
					});

				$(document)
					.off('click.wptsAcceptanceClose', '.cff-close-dlg, .cff-dialog-backdrop')
					.on('click.wptsAcceptanceClose', '.cff-close-dlg, .cff-dialog-backdrop', function (e) {
						e.preventDefault();
						var $wrap = $(this).closest('.fields');
						$wrap.find('.cff-dialog, .cff-dialog-backdrop').addClass('hide');
					});

				$(document)
					.off('keydown.wptsAcceptanceEsc')
					.on('keydown.wptsAcceptanceEsc', function (e) {
						if (e.key === 'Escape') {
							$('.cff-dialog:not(.hide), .cff-dialog-backdrop:not(.hide)').addClass('hide');
						}
					});
			},
			val: function () {
				var e = $('[id="' + this.name + '"]:checked:not(.ignore)');
				if (e.length) {
					var v = $.fbuilder.parseValStr(e[0].value);
					if (!$.isNumeric(v)) v = v.replace(/^"/, '').replace(/"$/, '');
					return v ? ($.isNumeric(v) ? v : '"' + v + '"') : 0;
				}
				return 0;
			}
		}
	);
