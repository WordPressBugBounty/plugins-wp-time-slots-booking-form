	$.fbuilder['typeList'] = [];
	$.fbuilder['categoryList'] = [];
	$.fbuilder['controls'] = {};

	$.fbuilder['parseJSONSafe'] = function (str) {
		if (typeof str !== 'string' || !str.length) return null;
		try {
			return JSON.parse(str);
		} catch (e) {
			if (typeof $.parseJSON === 'function') {
				try {
					return $.parseJSON(str);
				} catch (e2) {
					return null;
				}
			}
			return null;
		}
	};

	$.fbuilder['preview'] = function (e) {
		var f = $(e.form);
		f.attr('target', 'formpopup').attr(
			'onsubmit',
			function (f) {
				var w = screen.width * 0.8,
					h = screen.height * 0.7,
					l = screen.width / 2 - w / 2,
					t = screen.height / 2 - h / 2,
					new_window = window.open('', 'formpopup', 'resizeable,scrollbars,width=' + w + ',height=' + h + ',left=' + l + ',top=' + t);

				$(f).removeAttr('onsubmit');
				if (new_window) new_window.focus();
			}
		);
		$('<input type="hidden" name="preview" value="1" />').appendTo(f);

		f[0].submit();
		f.attr('target', '_self').find('input[name="preview"]').remove();
	};

	$.fbuilder['htmlEncode'] = function (value) {
		if (value === null || typeof value === 'undefined') return '';
		value = $('<div/>').text(String(value)).html();
		value = value.replace(/"/g, '&quot;');
		return value;
	};

	$.fbuilder['escape_symbol'] = function (value) {
		return String(value).replace(/([\^\$\-\.\,\[\]\(\)\/\\\*\?\+\!\{\}])/g, '\\$1');
	};

	$.fbuilder['parseVal'] = function (value, thousandSeparator, decimalSymbol) {
		if (value === '') return 0;
		value += '';

		thousandSeparator = new RegExp($.fbuilder.escape_symbol((typeof thousandSeparator === 'undefined') ? ',' : thousandSeparator), 'g');
		decimalSymbol = new RegExp($.fbuilder.escape_symbol((typeof decimalSymbol === 'undefined') ? '.' : decimalSymbol), 'g');

		var t = value.replace(thousandSeparator, '').replace(decimalSymbol, '.').replace(/\s/g, ''),
			p = /[+-]?((\d+(\.\d+)?)|(\.\d+))/.exec(t);

		return (p) ? p[0] * 1 : '"' + value.replace(/'/g, "\\'").replace(/\$/g, '') + '"';
	};

	$.fbuilder['showErrorMssg'] = function (str) {
		$('.form-builder-error-messages').html('<div class="error-text">' + str + '</div>');
	};

	$.fbuilder['getFieldIcon'] = function (typeId) {
		var icons = {
			fslots: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><circle cx="12" cy="15.5" r="2.5"/></svg>',
			ftext: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="6" width="18" height="12" rx="2"/><line x1="7" y1="12" x2="13" y2="12"/></svg>',
			femail: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><polyline points="3 7 12 13 21 7"/></svg>',
			femailds: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><polyline points="3 7 12 13 21 7"/></svg>',
			fnumber: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="9" x2="20" y2="9"/><line x1="4" y1="15" x2="20" y2="15"/><line x1="10" y1="3" x2="8" y2="21"/><line x1="16" y1="3" x2="14" y2="21"/></svg>',
			ftextarea: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"/><line x1="7" y1="9" x2="17" y2="9"/><line x1="7" y1="13" x2="15" y2="13"/><line x1="7" y1="17" x2="12" y2="17"/></svg>',
			fdate: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
			fcheck: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="3"/><polyline points="8 12.5 11 15.5 16.5 9"/></svg>',
			fradio: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3.5" fill="currentColor"/></svg>',
			fdropdown: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><polyline points="8 10 12 14 16 10"/></svg>',
			fPhone: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>',
			ffile: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>',
			fpassword: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>',
			fCommentArea: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>',
			fSectionBreak: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="6" y1="7" x2="18" y2="7"/><line x1="6" y1="17" x2="14" y2="17"/></svg>',
			fPageBreak: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12h16"/><path d="M6 4h12v4H6z"/><path d="M6 16h12v4H6z"/></svg>',
			facceptance: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>',
			fCalculated: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2"/><line x1="8" y1="6" x2="16" y2="6"/><line x1="8" y1="12" x2="10" y2="12"/><line x1="14" y1="12" x2="16" y2="12"/><line x1="8" y1="16" x2="10" y2="16"/><line x1="14" y1="16" x2="16" y2="16"/></svg>'
		};
		return icons[typeId] || '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><line x1="7" y1="12" x2="17" y2="12"/></svg>';
	};

	// Non-blocking inline help popover (replaces raw browser alert())
	$.fbuilder['showHelpPopover'] = function ($trigger) {
		$('.wpts-help-popover').remove();
		var text = $trigger.attr('text') || '';
		if (!text) return;

		var $pop = $('<div class="wpts-help-popover" role="tooltip"></div>');
		$pop.append($('<div class="wpts-help-popover-body"></div>').text(text).html(function (i, h) {
			return h.replace(/\n/g, '<br />');
		}));
		var $close = $('<button type="button" class="wpts-help-popover-close" aria-label="Close">&times;</button>');
		$close.on('click', function (e) {
			e.stopPropagation();
			$pop.remove();
		});
		$pop.append($close);
		$('body').append($pop);

		var offset = $trigger.offset();
		var popW = $pop.outerWidth();
		var left = Math.max(12, Math.min(offset.left - popW / 2 + 8, $(window).width() - popW - 12));
		var top = offset.top + $trigger.outerHeight() + 8;
		$pop.css({ top: top + 'px', left: left + 'px' });

		$(document).off('click.wptsHelp keydown.wptsHelp').on('click.wptsHelp', function (e) {
			if (!$(e.target).closest('.wpts-help-popover, .helpfbuilder').length) {
				$pop.remove();
				$(document).off('click.wptsHelp keydown.wptsHelp');
			}
		}).on('keydown.wptsHelp', function (e) {
			if (e.key === 'Escape') {
				$pop.remove();
				$(document).off('click.wptsHelp keydown.wptsHelp');
			}
		});
	};

	// fbuilder plugin
	$.fn.fbuilder = function (options) {
		var opt = $.extend({},
			{
				pub: false,
				identifier: '',
				title: ''
			},
			options, true),
			typeList = $.fbuilder.typeList,
			categoryList = $.fbuilder.categoryList,
			isDraggingPalette = false,
			lastAddedName = null;

		$.fbuilder['getNameByIdFromType'] = function (id) {
			for (var i = 0, h = typeList.length; i < h; i++) {
				if (typeList[i].id === id) {
					return typeList[i].name;
				}
			}
			return '';
		};

		// Reset category typeLists in case fbuilder is initialized more than once
		for (var catKey in categoryList) {
			if (categoryList.hasOwnProperty(catKey)) {
				categoryList[catKey].typeList = [];
			}
		}

		for (var i = 0, h = typeList.length; i < h; i++) {
			var category_id = typeList[i].control_category;

			if (typeof categoryList[category_id] === 'undefined') {
				categoryList[category_id] = { title: '', description: '', typeList: [] };
			} else if (typeof categoryList[category_id]['typeList'] === 'undefined') {
				categoryList[category_id]['typeList'] = [];
			}

			categoryList[category_id].typeList.push(i);
		}

		var $tabs1 = $('#tabs-1');
		$tabs1.empty();
		$tabs1.append(
			'<div class="wpts-palette-hint">' +
				'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 9l-3 3 3 3"/><path d="M9 5l3-3 3 3"/><path d="M15 19l-3 3-3-3"/><path d="M19 9l3 3-3 3"/><line x1="2" y1="12" x2="22" y2="12"/><line x1="12" y1="2" x2="12" y2="22"/></svg>' +
				'<span>Drag any field onto the form, or click to add it.</span>' +
			'</div>'
		);

		for (var cIdx in categoryList) {
			if (!categoryList.hasOwnProperty(cIdx)) continue;
			var cat = categoryList[cIdx];
			var hasTitle = !!(cat.title && !/^\s*$/.test(cat.title));
			var hasDesc = !!(typeof cat['description'] !== 'undefined' && !/^\s*$/.test(cat['description']));
			var hasTypes = !!(typeof cat['typeList'] !== 'undefined' && cat.typeList.length > 0);
			if (!hasTitle && !hasDesc && !hasTypes) continue;
			var $catWrap = $('<div class="wpts-palette-category"></div>');
			if (cat.title) {
				$catWrap.append('<div class="wpts-palette-category-title">' + $.fbuilder.htmlEncode(cat.title) + '</div>');
			}
			if (typeof cat['description'] !== 'undefined' && !/^\s*$/.test(cat['description'])) {
				$catWrap.append('<div class="category-description">' + cat.description + '</div>');
			}

			if (typeof cat['typeList'] !== 'undefined' && cat.typeList.length > 0) {
				var $availGrid = $('<div class="wpts-palette-grid"></div>');
				var $lockedGrid = $('<div class="wpts-palette-grid"></div>');
				var availCount = 0;
				var lockedCount = 0;

				for (var j = 0, k = cat.typeList.length; j < k; j++) {
					var index = cat.typeList[j];
					var typeObj = typeList[index];
					var isLocked = ((j !== 0 && j !== 1 && j !== 2 && j !== 3) || Number(cIdx) > 1);
					var iconHtml = $.fbuilder.getFieldIcon(typeObj.id);
					var $btn;

					if (isLocked) {
						$btn = $(
							'<div class="button n wpts-palette-item wpts-palette-item-locked" id="' + $.fbuilder.htmlEncode(typeObj.id) + '" data-ftype="' + $.fbuilder.htmlEncode(typeObj.id) + '" title="Available in Pro version">' +
								'<span class="wpts-item-icon">' + iconHtml + '</span>' +
								'<span class="wpts-item-label">' + $.fbuilder.htmlEncode(typeObj.name) + '</span>' +
								'<span class="wpts-pro-badge">PRO</span>' +
							'</div>'
						);
						$lockedGrid.append($btn);
						lockedCount++;
					} else {
						$btn = $(
							'<div class="button itemForm wpts-palette-item" id="' + $.fbuilder.htmlEncode(typeObj.id) + '" data-ftype="' + $.fbuilder.htmlEncode(typeObj.id) + '" title="Drag onto canvas or click to add ' + $.fbuilder.htmlEncode(typeObj.name) + '">' +
								'<span class="wpts-item-icon">' + iconHtml + '</span>' +
								'<span class="wpts-item-label">' + $.fbuilder.htmlEncode(typeObj.name) + '</span>' +
								'<span class="wpts-item-grip" aria-hidden="true">' +
									'<svg viewBox="0 0 16 16" fill="currentColor"><circle cx="5.5" cy="3.5" r="1.2"/><circle cx="10.5" cy="3.5" r="1.2"/><circle cx="5.5" cy="8" r="1.2"/><circle cx="10.5" cy="8" r="1.2"/><circle cx="5.5" cy="12.5" r="1.2"/><circle cx="10.5" cy="12.5" r="1.2"/></svg>' +
								'</span>' +
							'</div>'
						);
						$availGrid.append($btn);
						availCount++;
					}
				}

				if (availCount > 0) {
					$catWrap.append($availGrid);
				}
				if (lockedCount > 0) {
					var $proDrawer = $(
						'<div class="wpts-pro-palette-drawer wpts-section-collapsed">' +
							'<button type="button" class="wpts-pro-palette-toggle" aria-expanded="false">' +
								'<span>Pro Fields <span class="wpts-pro-count-pill">' + lockedCount + '</span></span>' +
								'<span class="wpts-fslots-chevron" aria-hidden="true">&#9662;</span>' +
							'</button>' +
							'<div class="wpts-pro-palette-body"></div>' +
						'</div>'
					);
					$proDrawer.find('.wpts-pro-palette-body').append($lockedGrid);
					$catWrap.append($proDrawer);
				}
			}
			$tabs1.append($catWrap);
		}
		$tabs1.append('<div class="clearer"></div>');
		$tabs1.off('click.wptsProDrawer').on('click.wptsProDrawer', '.wpts-pro-palette-toggle', function (evt) {
			evt.preventDefault();
			var $drawer = $(this).closest('.wpts-pro-palette-drawer');
			var collapsed = $drawer.toggleClass('wpts-section-collapsed').hasClass('wpts-section-collapsed');
			$(this).attr('aria-expanded', collapsed ? 'false' : 'true');
		});

		// Create items state
		var items = [],
			fieldsIndex = {},
			calculatedFields = {},
			selected = -3;

		var renderEmptySettings = function () {
			$('#tabs-2').html(
				'<div class="wpts-empty-settings">' +
					'<div class="wpts-empty-settings-icon">' +
						'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>' +
					'</div>' +
					'<h4>No Field Selected</h4>' +
					'<p>Click on any field in the form preview on the right to edit its label, validation, and options.</p>' +
				'</div>'
			);
		};

		$.fbuilder['editItem'] = function (id) {
			id = parseInt(id, 10);
			if (isNaN(id) || !items[id]) return;
			selected = id;
			try {
				$('#tabs-2').html(items[id].showAllSettings());
			} catch (e) {}
			items[id].editItemEvents();
		};

		$.fbuilder['removeItem'] = function (index) {
			index = parseInt(index, 10);
			if (isNaN(index) || !items[index]) return;
			if (typeof items[index]['remove'] !== 'undefined') items[index]['remove']();
			items.splice(index, 1);
			for (var i = 0, h = items.length; i < h; i++) {
				items[i].index = i;
			}
			selected = -2;
			$('#tabs').tabs('option', 'active', 0);
			$.fbuilder.reloadItems();
		};

		$.fbuilder['duplicateItem'] = function (index) {
			index = parseInt(index, 10);
			if (isNaN(index) || !items[index]) return;
			var n = 0;
			for (var i = 0, h = items.length; i < h; i++) {
				var n1 = parseInt(items[i].name.replace(/fieldname/g, ''), 10);
				if (!isNaN(n1) && n1 > n) n = n1;
			}

			var newName = 'fieldname' + (n + 1);
			if ($('#field' + opt.identifier + '-' + items[index].index).parents('.fields').length) {
				var parentIdx = $('#field' + opt.identifier + '-' + items[index].index).parents('.fields').attr('id').replace('field' + opt.identifier + '-', '');
				if (items[parentIdx] && typeof items[parentIdx]['duplicateItem'] !== 'undefined') {
					items[parentIdx]['duplicateItem'](items[index].name, newName);
				}
			}

			var cloned = $.extend(true, {}, items[index], { name: newName });
			items.splice(index + 1, 0, cloned);
			for (var j = index + 1, l = items.length; j < l; j++) {
				items[j].index = j;
			}
			selected = index + 1;
			lastAddedName = newName;
			$.fbuilder.reloadItems();
		};

		$.fbuilder['editForm'] = function () {
			$('#tabs-3').html(theForm.showAllSettings());
			selected = -1;

			$('#fTitle').on('input keyup', function () {
				theForm.title = $(this).val();
				$.fbuilder.reloadItems();
			});

			$('#fEvalEquations').on('change', function () {
				theForm.evalequations = $(this).is(':checked') ? 1 : 0;
				$.fbuilder.reloadItems();
			});

			$('#fAutofocus').on('change', function () {
				theForm.autofocus = $(this).is(':checked');
				$.fbuilder.reloadItems();
			});

			$('#fAutocomplete').on('change', function () {
				theForm.autocomplete = $(this).is(':checked') ? 1 : 0;
				$.fbuilder.reloadItems();
			});

			$('#fDescription').on('input keyup', function () {
				theForm.description = $(this).val();
				$.fbuilder.reloadItems();
			});

			$('#fLayout').on('change', function () {
				theForm.formlayout = $(this).val();
				$.fbuilder.reloadItems();
			});

			$('#fTemplate').on('change', function () {
				theForm.formtemplate = $(this).val();
				var template = $.fbuilder.showSettings.formTemplateDic[theForm.formtemplate],
					thumbnail = '',
					description = '';

				if (typeof template !== 'undefined') {
					if (typeof template['thumbnail'] !== 'undefined') {
						thumbnail = '<img src="' + template['thumbnail'] + '" alt="">';
					}
					if (typeof template['description'] !== 'undefined') {
						description = template['description'];
					}
				}
				$('#fTemplateThumbnail').html(thumbnail);
				$('#fTemplateDescription').html(description);
				$.fbuilder.reloadItems();
			});
		};

		$.fbuilder['reloadItems'] = function () {
			var $fieldlist = $('#fieldlist' + opt.identifier);
			var $formheader = $('#formheader' + opt.identifier);

			for (var i = 0, h = $.fbuilder.showSettings.formlayoutList.length; i < h; i++) {
				$fieldlist.removeClass($.fbuilder.showSettings.formlayoutList[i].id);
			}
			$fieldlist.addClass(theForm.formlayout);
			$formheader.html(theForm.display());
			$fieldlist.empty();

			if (parseInt(selected, 10) === -1) {
				$('.fform').addClass('ui-selected');
			} else {
				$('.fform').removeClass('ui-selected');
			}

			fieldsIndex = {};
			calculatedFields = {};

			if (items.length === 0) {
				$fieldlist.append(
					'<div class="wpts-empty-canvas">' +
						'<div class="wpts-empty-canvas-icon">' +
							'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>' +
						'</div>' +
						'<strong>Your form is empty</strong>' +
						'<span>Drag fields here from the left panel, or click a field to add it.</span>' +
					'</div>'
				);
			}

			for (var idx = 0, len = items.length; idx < len; idx++) {
				items[idx].index = idx;
				fieldsIndex[items[idx].name] = idx;
				if (items[idx].ftype === 'fCalculated') {
					calculatedFields[items[idx].name] = idx;
				}
				$fieldlist.append(items[idx].display());

				var $fieldEl = $('#field' + opt.identifier + '-' + idx);
				if (idx === selected) {
					$fieldEl.addClass('ui-selected');
					if ($('#tabs').tabs('option', 'active') !== 1) {
						$.fbuilder['editItem'](idx);
					}
				} else {
					$fieldEl.removeClass('ui-selected');
				}

				if (lastAddedName && items[idx].name === lastAddedName) {
					$fieldEl.addClass('wpts-field-just-added');
				}

				$fieldEl.find('.remove').on('click', function (evt) {
					evt.stopPropagation();
					var fieldIdx = $(this).closest('.fields').attr('id').replace('field' + opt.identifier + '-', '');
					$.fbuilder['removeItem'](fieldIdx);
				});

				$fieldEl.find('.copy').on('click', function (evt) {
					evt.stopPropagation();
					var fieldIdx = $(this).closest('.fields').attr('id').replace('field' + opt.identifier + '-', '');
					$.fbuilder['duplicateItem'](fieldIdx);
				});
			}

			for (var k = 0, kLen = items.length; k < kLen; k++) {
				if (typeof items[k].after_show !== 'undefined') items[k].after_show();
			}

			if (lastAddedName) {
				var $newlyAdded = $fieldlist.find('.wpts-field-just-added');
				if ($newlyAdded.length && $newlyAdded[0].scrollIntoView) {
					try {
						$newlyAdded[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
					} catch (e) {}
				}
				setTimeout(function () {
					$newlyAdded.removeClass('wpts-field-just-added');
				}, 900);
				lastAddedName = null;
			}

			if (items.length > 0) {
				$fieldlist.find('.fields')
					.on('mouseenter', function (evt) {
						$(this).addClass('ui-over');
						evt.stopPropagation();
					})
					.on('mouseleave', function (evt) {
						$(this).removeClass('ui-over');
						evt.stopPropagation();
					})
					.on('click', function (evt) {
						evt.stopPropagation();
						var fieldIdx = parseInt($(this).attr('id').replace('field' + opt.identifier + '-', ''), 10);
						if (selected !== fieldIdx || !$('#tabs-2 .wpts-settings-panel').length) {
							$.fbuilder['editItem'](fieldIdx);
						}
						$('.fform').removeClass('ui-selected');
						$fieldlist.find('.ui-selected').removeClass('ui-selected');
						$(this).addClass('ui-selected');
						$('#tabs').tabs('option', 'active', 1);
					});

				$fieldlist.find('.field').on('focus', function () {
					$(this).blur();
				});
			}

			$formheader.find('.fform')
				.on('mouseenter', function () {
					$(this).addClass('ui-over');
				})
				.on('mouseleave', function () {
					$(this).removeClass('ui-over');
				})
				.on('click', function (evt) {
					evt.stopPropagation();
					$('#tabs').tabs('option', 'active', 2);
					$.fbuilder.editForm();
					$fieldlist.find('.ui-selected').removeClass('ui-selected');
					$(this).addClass('ui-selected');
				});

			ffunct.saveData('form_structure');

			// Refresh external admin dropdowns synced with form fields
			if ($('#cu_user_email_field').length && typeof $('#cu_user_email_field').attr('def') !== 'undefined') {
				var emailStr = '',
					cu_user_email_field = $('#cu_user_email_field').attr('def').split(',');

				for (var eIdx = 0, eLen = items.length; eIdx < eLen; eIdx++) {
					var eItem = items[eIdx];
					if (eItem.ftype === 'femail' || eItem.ftype === 'femailds') {
						emailStr += '<option value="' + $.fbuilder.htmlEncode(eItem.name) + '" ' + (($.inArray(eItem.name, cu_user_email_field) !== -1) ? 'selected' : '') + '>' + $.fbuilder.htmlEncode(eItem.name) + ' (' + $.fbuilder.htmlEncode(eItem.title) + ')' + '</option>';
					}
				}
				$('#cu_user_email_field').html(emailStr);
			}

			if ($('[name="paypal_recurrent_field"]').length) {
				var recStr = '',
					paypal_recurrent_field = $('[name="paypal_recurrent_field"]').attr('def');

				for (var rIdx = 0, rLen = items.length; rIdx < rLen; rIdx++) {
					var rItem = items[rIdx];
					if (rItem.ftype === 'fradio' || rItem.ftype === 'fdropdown') {
						recStr += '<option value="' + $.fbuilder.htmlEncode(rItem.name) + '" ' + ((rItem.name === paypal_recurrent_field) ? 'selected' : '') + '>' + $.fbuilder.htmlEncode(rItem.name) + ' (' + $.fbuilder.htmlEncode(rItem.title) + ')' + '</option>';
					}
				}
				$('[name="paypal_recurrent_field"]').html(recStr);
			}

			if ($('#fp_destination_emails_field').length > 0 && $('#fp_destination_emails_field').is('select')) {
				var destStr = '',
					destDef = $('#fp_destination_emails_field').attr('def');
				for (var dIdx = 0, dLen = items.length; dIdx < dLen; dIdx++) {
					var dItem = items[dIdx];
					destStr += '<option value="' + $.fbuilder.htmlEncode(dItem.name) + '" ' + ((dItem.name === destDef) ? 'selected' : '') + '>' + $.fbuilder.htmlEncode(dItem.name) + '(' + $.fbuilder.htmlEncode(dItem.title) + ')</option>';
				}
				$('#fp_destination_emails_field').html(destStr);
			}

			if ($('#request_cost').length > 0 && $('#request_cost').is('select')) {
				var costStr = '',
					request_cost = $('#request_cost').attr('def');
				for (var cI = 0, cL = items.length; cI < cL; cI++) {
					var cItem = items[cI];
					costStr += '<option value="' + $.fbuilder.htmlEncode(cItem.name) + '" ' + ((cItem.name === request_cost) ? 'selected' : '') + '>' + $.fbuilder.htmlEncode(cItem.name) + '(' + $.fbuilder.htmlEncode(cItem.title) + ')</option>';
				}
				$('#request_cost').html(costStr);
			}

			if ($('#paypal_price_field').length > 0) {
				var paypal_price_field = $('#paypal_price_field').attr('def'),
					priceStr = '<option value="" ' + (('' === paypal_price_field) ? 'selected' : '') + '> ---- No ---- </option>';

				for (var pI = 0, pL = items.length; pI < pL; pI++) {
					var pItem = items[pI];
					priceStr += '<option value="' + $.fbuilder.htmlEncode(pItem.name) + '" ' + ((pItem.name === paypal_price_field) ? 'selected' : '') + '>' + $.fbuilder.htmlEncode(pItem.title) + '</option>';
				}
				$('#paypal_price_field').html(priceStr);
			}
		};

		var fform = function () {};
		$.extend(fform.prototype, {
			title: 'Untitled Form',
			description: "This is my form. Please fill it out. It's awesome!",
			formlayout: 'top_aligned',
			formtemplate: '',
			evalequations: 1,
			autocomplete: 1,
			autofocus: false,
			display: function () {
				var hasHeaderContent = (this.title && this.title.trim() !== '') || (this.description && this.description.trim() !== '');
				return '<div class="fform" id="field">' +
					'<div class="arrow ui-icon ui-icon-play"></div>' +
					'<span class="wpts-form-edit-badge">Form Header &middot; Click to edit</span>' +
					(hasHeaderContent
						? '<h1>' + $.fbuilder.htmlEncode(this.title) + '</h1><span>' + $.fbuilder.htmlEncode(this.description) + '</span>'
						: '<span class="wpts-form-header-placeholder">Untitled Form &mdash; click to configure title, layout, or template</span>') +
				'</div>';
			},

			showAllSettings: function () {
				var layout = '',
					template = '<option value="">Use default template</option>',
					thumbnail = '',
					description = '',
					selAttr = '';

				for (var i = 0; i < $.fbuilder.showSettings.formlayoutList.length; i++) {
					layout += '<option value="' + $.fbuilder.showSettings.formlayoutList[i].id + '" ' + (($.fbuilder.showSettings.formlayoutList[i].id === this.formlayout) ? 'selected' : '') + '>' + $.fbuilder.showSettings.formlayoutList[i].name + '</option>';
				}

				for (var key in $.fbuilder.showSettings.formTemplateDic) {
					if (!$.fbuilder.showSettings.formTemplateDic.hasOwnProperty(key)) continue;
					if (/^\s*$/.test(key)) break;
					selAttr = '';
					if ($.fbuilder.showSettings.formTemplateDic[key].prefix === this.formtemplate) {
						selAttr = 'selected';
						if (typeof $.fbuilder.showSettings.formTemplateDic[key].thumbnail !== 'undefined') {
							thumbnail = '<img src="' + $.fbuilder.showSettings.formTemplateDic[key].thumbnail + '" alt="">';
						}
						if (typeof $.fbuilder.showSettings.formTemplateDic[key].description !== 'undefined') {
							description = $.fbuilder.showSettings.formTemplateDic[key].description;
						}
					}
					template += '<option value="' + $.fbuilder.htmlEncode($.fbuilder.showSettings.formTemplateDic[key].prefix) + '" ' + selAttr + '>' + $.fbuilder.htmlEncode($.fbuilder.showSettings.formTemplateDic[key].title) + '</option>';
				}

				return '<div class="wpts-settings-panel">' +
					'<div class="wpts-setting-group">' +
						'<label for="fTitle">Form Name</label>' +
						'<input class="large" name="fTitle" id="fTitle" value="' + $.fbuilder.htmlEncode(this.title) + '" placeholder="Optional form heading" />' +
					'</div>' +
					'<div class="wpts-setting-group">' +
						'<label for="fDescription">Description</label>' +
						'<textarea class="large" name="fDescription" id="fDescription" rows="3" placeholder="Optional description shown below the title">' + $.fbuilder.htmlEncode(this.description) + '</textarea>' +
					'</div>' +
					'<div class="wpts-setting-group">' +
						'<label for="fLayout">Label Placement</label>' +
						'<select name="fLayout" id="fLayout" class="large">' + layout + '</select>' +
					'</div>' +
					'<div class="wpts-setting-group wpts-checkbox-stack">' +
						'<label class="wpts-checkbox-row"><input type="checkbox" name="fAutofocus" id="fAutofocus" ' + (this.autofocus ? 'checked' : '') + ' /><span>Auto-focus booking form when the page is loaded</span></label>' +
						'<label class="wpts-checkbox-row"><input type="checkbox" name="fAutocomplete" id="fAutocomplete" ' + (this.autocomplete ? 'checked' : '') + ' /><span>Enable browser autocompletion</span></label>' +
						((typeof $.fbuilder.controls['fCalculated'] !== 'undefined')
							? '<label class="wpts-checkbox-row"><input type="checkbox" name="fEvalEquations" id="fEvalEquations" ' + (this.evalequations ? 'checked' : '') + ' /><span>Evaluate calculated field equations dynamically</span></label>'
							: '') +
					'</div>' +
					'<div class="wpts-setting-group">' +
						'<label for="fTemplate">Form Template</label>' +
						'<select name="fTemplate" id="fTemplate" class="large">' + template + '</select>' +
						'<div class="wpts-template-preview">' +
							'<span id="fTemplateThumbnail">' + thumbnail + '</span>' +
							'<span id="fTemplateDescription">' + description + '</span>' +
						'</div>' +
					'</div>' +
				'</div>';
			}
		});

		var theForm = new fform();

		/* Keep active field settings open when clicking empty canvas whitespace */
		$('#fbuilder').on('click', function (evt) {
			evt.stopPropagation();
		});

		var targetListSelector = '#fieldlist' + opt.identifier;
		var targetListId = 'fieldlist' + opt.identifier;

		// Sortable canvas: handles BOTH reordering existing fields AND receiving dragged palette fields at exact drop index
		$(targetListSelector).sortable({
			connectWith: '.ui-sortable',
			items: '> .fields',
			placeholder: 'wpts-sortable-placeholder',
			tolerance: 'pointer',
			cursor: 'grabbing',
			opacity: 0.92,
			distance: 5,
			refreshPositions: true,
			cancel: '.remove, .copy, .wpts-field-action, .ui-datepicker-prev, .ui-datepicker-next, .ui-datepicker-calendar a',
			scroll: true,
			scrollSensitivity: 60,
			scrollSpeed: 15,
			start: function (event, ui) {
				$(targetListSelector).addClass('wpts-sorting');
			},
			over: function () {
				$(targetListSelector).find('.wpts-empty-canvas').hide();
			},
			out: function () {
				if (items.length === 0) {
					$(targetListSelector).find('.wpts-empty-canvas').show();
				}
			},
			stop: function () {
				$(targetListSelector).removeClass('wpts-sorting');
			},
			update: function (event, ui) {
				// Case 1: New field dragged from #tabs-1 palette
				if (ui.item.hasClass('itemForm') || ui.item.attr('data-ftype')) {
					var ftype = ui.item.attr('data-ftype') || ui.item.attr('id');
					var dropPos = ui.item.prevAll('.fields').length;
					ui.item.remove();
					if (ftype && typeof $.fbuilder.controls[ftype] === 'function') {
						ffunct.addItem(ftype, dropPos);
					}
					return;
				}

				// Case 2: Reordering an existing field on the canvas
				var fromIndex = -1;
				for (var i = 0, h = items.length; i < h; i++) {
					if (ui.item.hasClass(items[i].name)) {
						fromIndex = i;
						break;
					}
				}
				if (fromIndex === -1) return;

				if (ui.item.parent().attr('id') === targetListId) {
					var selectedName = (selected >= 0 && items[selected]) ? items[selected].name : null;
					var end_pos = ui.item.prevAll('.fields').length;
					var moved = items.splice(fromIndex, 1)[0];
					items.splice(end_pos, 0, moved);

					// Keep selection tracking the same field after reorder
					if (selectedName) {
						for (var sIdx = 0; sIdx < items.length; sIdx++) {
							if (items[sIdx].name === selectedName) {
								selected = sIdx;
								break;
							}
						}
					}
					$.fbuilder.reloadItems();
				} else {
					// Moved into a nested container
					items = items.concat(items.splice(fromIndex, 1));
				}
			}
		});

		// Enable smooth Drag & Drop from the Add a Field palette into the sortable canvas
		if (typeof $.fn.draggable === 'function') {
			$('#tabs-1 .itemForm').draggable({
				connectToSortable: targetListSelector,
				helper: 'clone',
				appendTo: 'body',
				zIndex: 100000,
				revert: 'invalid',
				revertDuration: 180,
				distance: 5,
				cursor: 'grabbing',
				cursorAt: { left: 24, top: 18 },
				scroll: true,
				start: function (event, ui) {
					isDraggingPalette = true;
					ui.helper
						.removeAttr('id')
						.addClass('wpts-drag-helper')
						.css({ width: '190px', height: 'auto' });
					$(targetListSelector).addClass('wpts-drop-active');
					$('#fbuilder').addClass('wpts-canvas-drop-active');
				},
				stop: function () {
					$(targetListSelector).removeClass('wpts-drop-active');
					$('#fbuilder').removeClass('wpts-canvas-drop-active');
					setTimeout(function () {
						isDraggingPalette = false;
					}, 150);
				}
			});
		}

		$('#tabs').tabs({
			activate: function (event, ui) {
				switch ($(this).tabs('option', 'active')) {
					case 0:
						$('.fform').removeClass('ui-selected');
						break;
					case 1:
						$('.fform').removeClass('ui-selected');
						if (selected < 0) {
							renderEmptySettings();
						}
						break;
					case 2:
						$('.fields').removeClass('ui-selected');
						$('.fform').addClass('ui-selected');
						$.fbuilder.editForm();
						break;
				}
			}
		});

		var ffunct = {
			getFieldsIndex: function () {
				return fieldsIndex;
			},
			getCalculatedFields: function () {
				return calculatedFields;
			},
			getItems: function () {
				return items;
			},
			addItem: function (id, position) {
				// Prevent accidental click-triggered duplicate right after dragging from palette
				if (isDraggingPalette && typeof position === 'undefined') {
					return null;
				}
				if (typeof $.fbuilder.controls[id] !== 'function') {
					return null;
				}

				var obj = new $.fbuilder.controls[id](),
					fBuild = this,
					n = 0;

				obj.init();
				for (var i = 0, h = items.length; i < h; i++) {
					var n1 = parseInt(items[i].name.replace(/fieldname/g, ''), 10);
					if (!isNaN(n1) && n1 > n) {
						n = n1;
					}
				}
				n++;
				obj.fBuild = fBuild;
				$.extend(obj, { name: 'fieldname' + n });

				if (typeof position === 'number' && !isNaN(position)) {
					var insertAt = Math.max(0, Math.min(position, items.length));
					items.splice(insertAt, 0, obj);
					selected = insertAt;
				} else if (selected >= 0 && selected < items.length) {
					items.splice(selected + 1, 0, obj);
					if (typeof items[selected]['addItem'] !== 'undefined') {
						items[selected]['addItem'](obj.name);
					} else {
						selected++;
					}
				} else {
					selected = items.length;
					items[selected] = obj;
				}

				lastAddedName = obj.name;
				$.fbuilder.reloadItems();
				return obj;
			},
			saveData: function (f) {
				var itemsStringified = $.stringifyXX(items),
					theFormStringified = $.stringifyXX(theForm),
					errorTxt = 'The entered data includes invalid characters. Please, if you are copying and pasting from another platform, be sure the data does not include invalid characters.',
					str;

				if (typeof global_varible_save_data !== 'undefined') {
					$('.form-builder-error-messages').html('');
				}

				try {
					if ($.fbuilder.parseJSONSafe(itemsStringified) !== null && $.fbuilder.parseJSONSafe(theFormStringified) !== null) {
						str = '[' + itemsStringified + ',[' + theFormStringified + ']]';
						$('#' + f).val(str);
						$('#form_structure_crc').val(str.length);
					} else {
						$.fbuilder['showErrorMssg'](errorTxt);
					}
				} catch (err) {
					$.fbuilder['showErrorMssg'](errorTxt);
				}

				global_varible_save_data = true;
			},
			loadData: function (form_structure, available_templates) {
				var structure = $.fbuilder.parseJSONSafe($('#' + form_structure).val()),
					templates = (typeof available_templates === 'undefined') ? null : $.fbuilder.parseJSONSafe($('#' + available_templates).val()),
					fBuild = this;

				if (structure) {
					if (structure.length === 2) {
						items = [];
						for (var i = 0; i < structure[0].length; i++) {
							var ftype = structure[0][i].ftype;
							if (typeof $.fbuilder.controls[ftype] === 'function') {
								var obj = new $.fbuilder.controls[ftype]();
								obj = $.extend(true, {}, obj, structure[0][i]);
								obj.name = obj.name + opt.identifier;
								obj.form_identifier = opt.identifier;
								obj.fBuild = fBuild;
								items[items.length] = obj;
							}
						}
						theForm = new fform();
						theForm = $.extend(theForm, structure[1][0]);
						$.fbuilder.reloadItems();

						// Auto-select the Time Slots calendar field on load so schedule controls are immediately visible
						if (items.length > 0) {
							var initialIdx = 0;
							for (var sIdx = 0; sIdx < items.length; sIdx++) {
								if (items[sIdx].ftype === 'fslots') {
									initialIdx = sIdx;
									break;
								}
							}
							selected = initialIdx;
							$.fbuilder.editItem(initialIdx);
							$('#fieldlist' + opt.identifier + ' .ui-selected').removeClass('ui-selected');
							$('#field' + opt.identifier + '-' + initialIdx).addClass('ui-selected');
							if ($('#tabs').hasClass('ui-tabs')) {
								$('#tabs').tabs('option', 'active', 1);
							}
						}
					}
				}

				if (templates) {
					$.fbuilder.showSettings.formTemplateDic = templates;
				}
			},
			removeItem: $.fbuilder['removeItem'],
			editItem: $.fbuilder['editItem']
		};

		this.fBuild = ffunct;
		return this;
	};

	$.fbuilder['showSettings'] = {
		sizeList: [
			{ id: 'small', name: 'Small' },
			{ id: 'medium', name: 'Medium' },
			{ id: 'large', name: 'Large' }
		],
		layoutList: [
			{ id: 'one_column', name: 'One Column' },
			{ id: 'two_column', name: 'Two Column' },
			{ id: 'three_column', name: 'Three Column' },
			{ id: 'side_by_side', name: 'Side by Side' }
		],
		formlayoutList: [
			{ id: 'top_aligned', name: 'Top Aligned' },
			{ id: 'left_aligned', name: 'Left Aligned' },
			{ id: 'right_aligned', name: 'Right Aligned' },
			{ id: 'center_aligned', name: 'Center Aligned' }
		],
		formTemplateDic: {},
		showFieldType: function (v) {
			return '<div class="wpts-field-type-banner">' +
				'<span class="wpts-field-type-icon">' + $.fbuilder.getFieldIcon(v) + '</span>' +
				'<div class="wpts-field-type-meta">' +
					'<span class="wpts-field-type-kicker">Field Type</span>' +
					'<strong class="wpts-field-type-name">' + $.fbuilder.htmlEncode($.fbuilder['getNameByIdFromType'](v) || v) + '</strong>' +
				'</div>' +
			'</div>';
		},
		showTitle: function (v) {
			return '<div class="wpts-setting-group">' +
				'<label for="sTitle">Field Label</label>' +
				'<textarea class="large" name="sTitle" id="sTitle" rows="2">' + $.fbuilder.htmlEncode(v) + '</textarea>' +
			'</div>';
		},
		showShortLabel: function (v) {
			return '<div class="wpts-setting-group">' +
				'<label for="sShortlabel">Short label (optional) <a class="helpfbuilder" text="The short label is used as the column title when exporting form data to CSV files.\n\nIf left empty, the field label will be used instead.">?</a></label>' +
				'<input class="large" name="sShortlabel" id="sShortlabel" value="' + $.fbuilder.htmlEncode(v) + '" placeholder="CSV column header" />' +
			'</div>';
		},
		showName: function (v) {
			return '<div class="wpts-setting-group">' +
				'<label for="sNametag">Field tag for emails &amp; messages</label>' +
				'<div class="wpts-tag-copy-row">' +
					'<input readonly="readonly" class="large wpts-code-input" name="sNametag" id="sNametag" value="&lt;%' + $.fbuilder.htmlEncode(v) + '%&gt;" />' +
					'<button type="button" class="wpts-copy-tag-btn" data-copy-target="#sNametag" title="Copy tag">Copy</button>' +
				'</div>' +
				'<input style="display:none" readonly="readonly" class="large" name="sName" id="sName" value="' + $.fbuilder.htmlEncode(v) + '" />' +
			'</div>';
		},
		showPredefined: function (v, c) {
			return '<div class="wpts-setting-group">' +
				'<label for="sPredefined">Predefined Value</label>' +
				'<textarea class="large" name="sPredefined" id="sPredefined" rows="2">' + $.fbuilder.htmlEncode(v) + '</textarea>' +
				'<label class="wpts-checkbox-row"><input type="checkbox" name="sPredefinedClick" id="sPredefinedClick" ' + (c ? 'checked' : '') + ' value="1" /><span>Use predefined value as placeholder</span></label>' +
			'</div>';
		},
		showEqualTo: function (v, name) {
			return '<div class="wpts-setting-group">' +
				'<label for="sEqualTo">Equal to <a class="helpfbuilder" text="Use this setting to create a confirmation field (such as Confirm Email or Confirm Password).\n\nSet this ONLY on the confirmation field, not on the original field.">?</a></label>' +
				'<select class="equalTo large" name="sEqualTo" id="sEqualTo" dvalue="' + $.fbuilder.htmlEncode(v) + '" dname="' + $.fbuilder.htmlEncode(name) + '"></select>' +
			'</div>';
		},
		showRequired: function (v) {
			return '<div class="wpts-setting-group">' +
				'<label class="wpts-checkbox-row wpts-checkbox-card"><input type="checkbox" name="sRequired" id="sRequired" ' + (v ? 'checked' : '') + ' /><span>Required field</span></label>' +
			'</div>';
		},
		showSize: function (v) {
			var str = '';
			for (var i = 0; i < this.sizeList.length; i++) {
				str += '<option value="' + this.sizeList[i].id + '" ' + ((this.sizeList[i].id === v) ? 'selected' : '') + '>' + this.sizeList[i].name + '</option>';
			}
			return '<div class="wpts-setting-group">' +
				'<label for="sSize">Field Size</label>' +
				'<select class="large" name="sSize" id="sSize">' + str + '</select>' +
			'</div>';
		},
		showLayout: function (v) {
			var str = '';
			for (var i = 0; i < this.layoutList.length; i++) {
				str += '<option value="' + this.layoutList[i].id + '" ' + ((this.layoutList[i].id === v) ? 'selected' : '') + '>' + this.layoutList[i].name + '</option>';
			}
			return '<div class="wpts-setting-group">' +
				'<label for="sLayout">Field Layout</label>' +
				'<select class="large" name="sLayout" id="sLayout">' + str + '</select>' +
			'</div>';
		},
		showUserhelp: function (v, c) {
			return '<div class="wpts-setting-group">' +
				'<label for="sUserhelp">Instructions for User</label>' +
				'<textarea class="large" name="sUserhelp" id="sUserhelp" rows="2">' + $.fbuilder.htmlEncode(v) + '</textarea>' +
				'<label class="wpts-checkbox-row"><input type="checkbox" name="sUserhelpTooltip" id="sUserhelpTooltip" ' + (c ? 'checked' : '') + ' value="1" /><span>Show as floating tooltip</span></label>' +
			'</div>';
		},
		showCsslayout: function (v) {
			return '<div class="wpts-setting-group">' +
				'<label for="sCsslayout">Additional CSS Class</label>' +
				'<input class="large" name="sCsslayout" id="sCsslayout" value="' + $.fbuilder.htmlEncode(v) + '" placeholder="e.g. column2" />' +
			'</div>';
		}
	};

	$.fbuilder.controls['ffields'] = function () {};
	$.extend($.fbuilder.controls['ffields'].prototype, {
		form_identifier: '',
		name: '',
		shortlabel: '',
		index: -1,
		ftype: '',
		userhelp: '',
		userhelpTooltip: false,
		csslayout: '',
		init: function () {},
		editItemEvents: function (e) {
			if (typeof e !== 'undefined' && typeof e.length !== 'undefined') {
				for (var i = 0, h = e.length; i < h; i++) {
					/**
					 * s -> selector
					 * e -> event name
					 * l -> element
					 * f -> function to apply the value
					 */
					$(e[i].s).on(e[i].e, { obj: this, i: e[i] }, function (evt) {
						var v = $(this).val();
						if (typeof evt.data.i['f'] !== 'undefined') v = evt.data.i.f($(this));
						evt.data.obj[evt.data.i.l] = v;
						$.fbuilder.reloadItems({ field: evt.data.obj });
					});
				}
			}

			$('#sTitle').on('input keyup', { obj: this }, function (evt) {
				var str = $(this).val();
				evt.data.obj.title = str.replace(/\n/g, '<br />');
				$.fbuilder.reloadItems();
			});

			$('#sShortlabel').on('input keyup', { obj: this }, function (evt) {
				evt.data.obj.shortlabel = $(this).val();
				$.fbuilder.reloadItems();
			});

			$('#sPredefined').on('input keyup', { obj: this }, function (evt) {
				evt.data.obj.predefined = $(this).val();
				$.fbuilder.reloadItems();
			});

			$('#sPredefinedClick').on('change', { obj: this }, function (evt) {
				evt.data.obj.predefinedClick = $(this).is(':checked');
				$.fbuilder.reloadItems();
			});

			$('#sRequired').on('change', { obj: this }, function (evt) {
				evt.data.obj.required = $(this).is(':checked');
				$.fbuilder.reloadItems();
			});

			$('#sUserhelp').on('input keyup', { obj: this }, function (evt) {
				evt.data.obj.userhelp = $(this).val();
				$.fbuilder.reloadItems();
			});

			$('#sUserhelpTooltip').on('change', { obj: this }, function (evt) {
				evt.data.obj.userhelpTooltip = $(this).is(':checked');
				$.fbuilder.reloadItems();
			});

			$('#sCsslayout').on('input keyup', { obj: this }, function (evt) {
				evt.data.obj.csslayout = $(this).val();
				$.fbuilder.reloadItems();
			});

			$('.wpts-copy-tag-btn').on('click', function (evt) {
				evt.preventDefault();
				var $btn = $(this);
				var $input = $($btn.attr('data-copy-target'));
				var val = $input.val();
				if (navigator.clipboard && navigator.clipboard.writeText) {
					navigator.clipboard.writeText(val);
				} else {
					$input[0].select();
					try { document.execCommand('copy'); } catch (err) {}
				}
				$btn.text('Copied!').addClass('wpts-copied');
				setTimeout(function () {
					$btn.text('Copy').removeClass('wpts-copied');
				}, 1400);
			});

			$('.helpfbuilder').on('click', function (evt) {
				evt.preventDefault();
				evt.stopPropagation();
				$.fbuilder.showHelpPopover($(this));
			});

			$('#tabs-2').off('click.wptsAdvDrawer').on('click.wptsAdvDrawer', '.wpts-advanced-drawer-toggle', function (evt) {
				evt.preventDefault();
				var $drawer = $(this).closest('.wpts-advanced-drawer');
				var collapsed = $drawer.toggleClass('wpts-section-collapsed').hasClass('wpts-section-collapsed');
				$(this).attr('aria-expanded', collapsed ? 'false' : 'true');
			});
		},

		showSpecialData: function () {
			if (typeof this.showSpecialDataInstance !== 'undefined') {
				return this.showSpecialDataInstance();
			}
			return '';
		},

		showEqualTo: function () {
			if (typeof this.equalTo !== 'undefined') {
				return $.fbuilder.showSettings.showEqualTo(this.equalTo, this.name);
			}
			return '';
		},

		showPredefined: function () {
			if (typeof this.predefined !== 'undefined') {
				return $.fbuilder.showSettings.showPredefined(this.predefined, this.predefinedClick);
			}
			return '';
		},

		showRequired: function () {
			if (typeof this.required !== 'undefined') {
				return $.fbuilder.showSettings.showRequired(this.required);
			}
			return '';
		},

		showSize: function () {
			if (typeof this.size !== 'undefined') {
				return $.fbuilder.showSettings.showSize(this.size);
			}
			return '';
		},

		showLayout: function () {
			if (typeof this.layout !== 'undefined') {
				return $.fbuilder.showSettings.showLayout(this.layout);
			}
			return '';
		},

		showRange: function () {
			if (typeof this.min !== 'undefined') {
				return this.showRangeIntance();
			}
			return '';
		},

		showFormat: function () {
			if (typeof this.dformat !== 'undefined') {
				try {
					return this.showFormatIntance();
				} catch (e) {
					return '';
				}
			}
			return '';
		},

		showChoice: function () {
			if (typeof this.choices !== 'undefined') {
				return this.showChoiceIntance();
			}
			return '';
		},

		showUserhelp: function () {
			return $.fbuilder.showSettings.showUserhelp(this.userhelp, this.userhelpTooltip);
		},

		showCsslayout: function () {
			return $.fbuilder.showSettings.showCsslayout(this.csslayout);
		},

		showAllSettings: function () {
			var advancedHtml =
				this.showShortLabel() +
				this.showName() +
				this.showSize() +
				this.showLayout() +
				this.showEqualTo() +
				this.showPredefined() +
				this.showUserhelp() +
				this.showCsslayout();

			var advancedDrawer = '';
			if (advancedHtml && advancedHtml.trim() !== '') {
				advancedDrawer =
					'<div class="wpts-advanced-drawer wpts-section-collapsed">' +
						'<button type="button" class="wpts-advanced-drawer-toggle" aria-expanded="false">' +
							'<span>Advanced Field Options</span>' +
							'<span class="wpts-fslots-chevron" aria-hidden="true">&#9662;</span>' +
						'</button>' +
						'<div class="wpts-advanced-drawer-body">' + advancedHtml + '</div>' +
					'</div>';
			}

			return '<div class="wpts-settings-panel">' +
				this.showFieldType() +
				this.showTitle() +
				this.showRequired() +
				this.showFormat() +
				this.showRange() +
				this.showChoice() +
				this.showSpecialData() +
				advancedDrawer +
			'</div>';
		},

		showFieldType: function () {
			return $.fbuilder.showSettings.showFieldType(this.ftype);
		},

		showTitle: function () {
			return $.fbuilder.showSettings.showTitle(this.title);
		},

		showName: function () {
			return $.fbuilder.showSettings.showName(this.name);
		},

		showShortLabel: function () {
			return $.fbuilder.showSettings.showShortLabel(this.shortlabel);
		},

		display: function () {
			return 'Not available yet';
		},

		show: function () {
			return 'Not available yet';
		}
	});
