	$.fbuilder.controls['ftext'] = function () {};
	$.extend(
		$.fbuilder.controls['ftext'].prototype,
		$.fbuilder.controls['ffields'].prototype,
		{
			title: 'Untitled',
			ftype: 'ftext',
			predefined: '',
			predefinedClick: false,
			required: false,
			size: 'medium',
			minlength: '',
			maxlength: '',
			equalTo: '',
			show: function () {
				var minAttr = (this.minlength !== '' && !isNaN(parseInt(this.minlength, 10))) ? ' minlength="' + parseInt(this.minlength, 10) + '"' : '';
				var maxAttr = (this.maxlength !== '' && !isNaN(parseInt(this.maxlength, 10))) ? ' maxlength="' + parseInt(this.maxlength, 10) + '"' : '';
				var eqAttr = (this.equalTo !== '') ? ' equalTo="#' + $.fbuilder.htmlEncode(this.equalTo + this.form_identifier) + '"' : '';

				return '<div class="fields ' + $.fbuilder.htmlEncode(this.csslayout) + '" id="field' + this.form_identifier + '-' + this.index + '">' +
					'<label for="' + this.name + '">' + $.fbuilder.htmlEncode(this.title) + (this.required ? '<span class="r">*</span>' : '') + '</label>' +
					'<div class="dfield">' +
						'<input id="' + this.name + '" name="' + this.name + '"' + minAttr + maxAttr + eqAttr + ' class="field ' + $.fbuilder.htmlEncode(this.size) + (this.required ? ' required' : '') + '" type="text" value="' + $.fbuilder.htmlEncode(this.predefined) + '" />' +
						'<span class="uh">' + $.fbuilder.htmlEncode(this.userhelp) + '</span>' +
					'</div>' +
					'<div class="clearer"></div>' +
				'</div>';
			}
		}
	);
