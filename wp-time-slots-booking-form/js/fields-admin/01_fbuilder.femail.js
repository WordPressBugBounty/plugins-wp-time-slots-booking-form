	$.fbuilder.typeList.push({
		id: 'femail',
		name: 'Email',
		control_category: 1
	});

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
			display: function () {
				var inputAttr = this.predefinedClick
					? 'value="" placeholder="' + $.fbuilder.htmlEncode(this.predefined) + '"'
					: 'value="' + $.fbuilder.htmlEncode(this.predefined) + '" placeholder="name@example.com"';

				return '<div class="fields ' + this.name + '" id="field' + this.form_identifier + '-' + this.index + '">' +
					'<div class="arrow ui-icon ui-icon-play"></div>' +
					'<div title="Delete" class="remove ui-icon ui-icon-trash"></div>' +
					'<div title="Duplicate" class="copy ui-icon ui-icon-copy"></div>' +
					'<label>' + $.fbuilder.htmlEncode(this.title) + (this.required ? '<span class="wpts-req-star">*</span>' : '') + '</label>' +
					'<div class="dfield">' +
						'<input class="field disabled ' + $.fbuilder.htmlEncode(this.size) + '" type="email" ' + inputAttr + ' readonly="readonly" />' +
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
				return '';
			}
		}
	);
