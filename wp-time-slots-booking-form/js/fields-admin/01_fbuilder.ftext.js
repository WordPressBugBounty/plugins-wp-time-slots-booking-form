	$.fbuilder.typeList.push({
		id: 'ftext',
		name: 'Single Line',
		control_category: 1
	});

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
			display: function () {
				var inputAttr = this.predefinedClick
					? 'value="" placeholder="' + $.fbuilder.htmlEncode(this.predefined) + '"'
					: 'value="' + $.fbuilder.htmlEncode(this.predefined) + '"';

				return '<div class="fields ' + this.name + '" id="field' + this.form_identifier + '-' + this.index + '">' +
					'<div class="arrow ui-icon ui-icon-play"></div>' +
					'<div title="Delete" class="remove ui-icon ui-icon-trash"></div>' +
					'<div title="Duplicate" class="copy ui-icon ui-icon-copy"></div>' +
					'<label>' + $.fbuilder.htmlEncode(this.title) + (this.required ? '<span class="wpts-req-star">*</span>' : '') + '</label>' +
					'<div class="dfield">' +
						'<input class="field disabled ' + $.fbuilder.htmlEncode(this.size) + '" type="text" ' + inputAttr + ' readonly="readonly" />' +
						'<span class="uh">' + $.fbuilder.htmlEncode(this.userhelp) + '</span>' +
					'</div>' +
					'<div class="clearer"></div>' +
				'</div>';
			},
			editItemEvents: function () {
				$('#sSize').on('change', { obj: this }, function (e) {
					e.data.obj.size = $(this).val();
					$.fbuilder.reloadItems();
				});
				$('#sMinlength').on('input change', { obj: this }, function (e) {
					e.data.obj.minlength = $(this).val();
					$.fbuilder.reloadItems();
				});
				$('#sMaxlength').on('input change', { obj: this }, function (e) {
					e.data.obj.maxlength = $(this).val();
					$.fbuilder.reloadItems();
				});
				$('#sEqualTo').on('change', { obj: this }, function (e) {
					e.data.obj.equalTo = $(this).val();
					$.fbuilder.reloadItems();
				});

				var items = this.fBuild.getItems();
				$('.equalTo').each(function () {
					var dval = $(this).attr('dvalue') || '';
					var dname = $(this).attr('dname') || '';
					var str = '<option value="" ' + (dval === '' ? 'selected' : '') + '>-- None --</option>';
					for (var i = 0; i < items.length; i++) {
						if ((items[i].ftype === 'ftext' || items[i].ftype === 'femail' || items[i].ftype === 'fpassword') && items[i].name !== dname) {
							str += '<option value="' + $.fbuilder.htmlEncode(items[i].name) + '" ' + (items[i].name === dval ? 'selected' : '') + '>' + $.fbuilder.htmlEncode(items[i].title) + ' (' + $.fbuilder.htmlEncode(items[i].name) + ')</option>';
						}
					}
					$(this).html(str);
				});

				$.fbuilder.controls['ffields'].prototype.editItemEvents.call(this);
			},
			showSpecialDataInstance: function () {
				return '<div class="wpts-setting-group wpts-setting-row-2">' +
					'<div>' +
						'<label for="sMinlength">Min characters</label>' +
						'<input type="number" min="0" class="large" name="sMinlength" id="sMinlength" value="' + $.fbuilder.htmlEncode(this.minlength) + '" placeholder="No limit" />' +
					'</div>' +
					'<div>' +
						'<label for="sMaxlength">Max characters</label>' +
						'<input type="number" min="0" class="large" name="sMaxlength" id="sMaxlength" value="' + $.fbuilder.htmlEncode(this.maxlength) + '" placeholder="No limit" />' +
					'</div>' +
				'</div>';
			}
		}
	);
