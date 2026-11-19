	$.fbuilder.typeList.push({
		id: 'fMedia',
		name: 'Media',
		control_category: 3
	});

	$.fbuilder.controls['fMedia'] = function () {};
	$.extend(
		$.fbuilder.controls['fMedia'].prototype,
		$.fbuilder.controls['ffields'].prototype,
		{
			ftype: 'fMedia',
			sMediaType: 'image',
			display: function () {
				return '- available only in commercial version of plugin -';
			},
			editItemEvents: function () {
				$.fbuilder.controls['ffields'].prototype.editItemEvents.call(this);
			}
		}
	);
