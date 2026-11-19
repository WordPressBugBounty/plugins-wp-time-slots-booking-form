	$.fbuilder.controls['femail'] = function () {};
	$.extend(
		$.fbuilder.controls['femail'].prototype,
		$.fbuilder.controls['ffields'].prototype,
		{
			title: 'Email',
			ftype: 'femail',
			predefined: '',
			predefinedClick: false,
			required: false,
			size: 'medium',
			equalTo: '',
			show: function () {
				var eqAttr = (this.equalTo !== '') ? ' equalTo="#' + $.fbuilder.htmlEncode(this.equalTo + this.form_identifier) + '"' : '';

				return '<div class="fields ' + $.fbuilder.htmlEncode(this.csslayout) + '" id="field' + this.form_identifier + '-' + this.index + '">' +
					'<label for="' + this.name + '">' + $.fbuilder.htmlEncode(this.title) + (this.required ? '<span class="r">*</span>' : '') + '</label>' +
					'<div class="dfield">' +
						'<input id="' + this.name + '" name="' + this.name + '"' + eqAttr + ' class="field email ' + $.fbuilder.htmlEncode(this.size) + (this.required ? ' required' : '') + '" type="text" inputmode="email" autocomplete="email" value="' + $.fbuilder.htmlEncode(this.predefined) + '" />' +
						'<span class="uh">' + $.fbuilder.htmlEncode(this.userhelp) + '</span>' +
					'</div>' +
					'<div class="clearer"></div>' +
				'</div>';
			},
			after_show: function () {
				$('#' + this.name).on('blur change', function () {
					var val = $(this).val();
					var trimmed = $.trim(val);
					if (val !== trimmed) {
						$(this).val(trimmed);
					}
				});
			},
			val: function () {
				var e = $('[id="' + this.name + '"]:not(.ignore)');
				if (e.length) return $.fbuilder.parseValStr($.trim(e.val()));
				return '';
			}
		}
	);
