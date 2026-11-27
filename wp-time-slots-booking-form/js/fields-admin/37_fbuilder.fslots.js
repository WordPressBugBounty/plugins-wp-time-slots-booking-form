$.fbuilder.typeList.push({
	id: 'fslots',
	name: 'Slots booking',
	control_category: 1
});

$.fbuilder.controls['fslots'] = function () {
	this.init();
};

$.extend(
	$.fbuilder.controls['fslots'].prototype,
	$.fbuilder.controls['ffields'].prototype,
	{
		title: 'Slots booking',
		ftype: 'fslots',
		slotsavailInit: {
			d1: [{ h1: 8, m1: 0, c: 1, p: 0, min: 8 * 60 }],
			d2: [{ h1: 8, m1: 0, c: 1, p: 0, min: 8 * 60 }],
			d3: [{ h1: 8, m1: 0, c: 1, p: 0, min: 8 * 60 }],
			d4: [{ h1: 8, m1: 0, c: 1, p: 0, min: 8 * 60 }],
			d5: [{ h1: 8, m1: 0, c: 1, p: 0, min: 8 * 60 }]
		},
		slots_special: [],
		min_quantity: 1,
		max_quantity: 5,
		quantity1: 1,
		quantity1start: 0,
		label1: '',
		quantity2: 1,
		label2: '',
		quantity3: 1,
		label3: '',
		quantity4: 1,
		label4: '',
		quantity5: 1,
		label5: '',
		prices: [{ name: 'default', price1: [], price2: [], price3: [], price4: [], price5: [] }],
		dateFormat: 'mm/dd/yy',
		showDropdown: false,
		showTotalCost: false,
		usedSlotsCheckbox: false,
		flatPrice: false,
		showTotalCostFormat: '$ {0}',
		dropdownRange: '-10:+10',
		working_dates: [true, true, true, true, true, true, true],
		numberOfMonths: 1,
		firstDay: 0,
		minDate: '0',
		maxDate: '',
		defaultDate: '',
		invalidDates: '',
		tmpinvalidDates: [],
		required: true,
		militaryTime: 1,

		formattime: function (t) {
			var h = Math.floor(t / 60);
			var m = t % 60;
			var suffix = '';
			if (parseInt(this.militaryTime, 10) === 0) {
				if (h > 12) {
					h = h - 12;
					suffix = ' PM';
				} else if (h === 12) {
					suffix = ' PM';
				} else {
					if (h === 0) h = 12;
					suffix = ' AM';
				}
			}
			return ((h < 10 && parseInt(this.militaryTime, 10) !== 0) ? '0' : '') + h + ':' + (m < 10 ? '0' : '') + m + suffix;
		},

		_getDayNames: function () {
			var defaults = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
			try {
				var e = $('#field' + this.form_identifier + '-' + this.index + ' .sbfieldCalendar' + this.name);
				if (e.length && e.hasClass('hasDatepicker')) {
					var names = e.datepicker('option', 'dayNames');
					if ($.isArray(names) && names.length === 7) return names;
				}
			} catch (err) {}
			return defaults;
		},

		_syncPriceDropdowns: function () {
			var opts = '';
			for (var m = 0; m < this.prices.length; m++) {
				opts += '<option value="' + m + '">' + $.fbuilder.htmlEncode(this.prices[m].name || ('Tier ' + (m + 1))) + '</option>';
			}
			$('#tabs-2 .p1, #tabs-2 .p2').each(function () {
				var prev = $(this).val();
				$(this).html(opts);
				if (prev !== null && $(this).find('option[value="' + prev + '"]').length) {
					$(this).val(prev);
				}
			});
		},

		showSlotsPrices: function () {
			var maxQty = Math.max(1, parseInt(this.max_quantity, 10) || 1);
			var cols = [
				{ key: 'price1', label: (this.quantity1 > 1 ? (this.label1 || 'Qty 1') : 'Price') }
			];
			if (this.quantity1 > 1 && this.quantity2 > 1) cols.push({ key: 'price2', label: this.label2 || 'Qty 2' });
			if (this.quantity1 > 1 && this.quantity2 > 1 && this.quantity3 > 1) cols.push({ key: 'price3', label: this.label3 || 'Qty 3' });
			if (this.quantity1 > 1 && this.quantity2 > 1 && this.quantity3 > 1 && this.quantity4 > 1) cols.push({ key: 'price4', label: this.label4 || 'Qty 4' });
			if (this.quantity1 > 1 && this.quantity2 > 1 && this.quantity3 > 1 && this.quantity4 > 1 && this.quantity5 > 1) cols.push({ key: 'price5', label: this.label5 || 'Qty 5' });

			var str = '';
			for (var i = 0; i < this.prices.length; i++) {
				this.prices[i].price1 = this.prices[i].price1 || [];
				this.prices[i].price2 = this.prices[i].price2 || [];
				this.prices[i].price3 = this.prices[i].price3 || [];
				this.prices[i].price4 = this.prices[i].price4 || [];
				this.prices[i].price5 = this.prices[i].price5 || [];

				for (var k = 0; k < maxQty; k++) {
					if (typeof this.prices[i].price1[k] === 'undefined') this.prices[i].price1[k] = 0;
					if (typeof this.prices[i].price2[k] === 'undefined') this.prices[i].price2[k] = 0;
					if (typeof this.prices[i].price3[k] === 'undefined') this.prices[i].price3[k] = 0;
					if (typeof this.prices[i].price4[k] === 'undefined') this.prices[i].price4[k] = 0;
					if (typeof this.prices[i].price5[k] === 'undefined') this.prices[i].price5[k] = 0;
				}

				str += '<div class="slotspricediv wpts-price-tier ' + ((i % 2) ? 'even' : 'odd') + '">';
				str += '<div class="wpts-price-tier-header">';
				if (i === 0) {
					str += '<span class="wpts-price-tier-badge">Default Tier</span>';
				} else {
					str += '<input type="text" class="nameSlotsprice" i="' + i + '" value="' + $.fbuilder.htmlEncode(this.prices[i].name) + '" placeholder="Price tier name" />';
					str += '<button type="button" i="' + i + '" class="deleteSlotsprice wpts-btn-danger-xs" title="Delete price tier">Delete</button>';
				}
				str += '</div>';

				str += '<div class="wpts-price-table-wrap"><table class="wpts-price-table"><thead><tr><th>Slot #</th>';
				for (var cIdx = 0; cIdx < cols.length; cIdx++) {
					str += '<th>' + $.fbuilder.htmlEncode(cols[cIdx].label) + '</th>';
				}
				str += '</tr></thead><tbody>';

				for (var j = 0; j < maxQty; j++) {
					str += '<tr><td class="wpts-slot-num-cell">#' + (j + 1) + '</td>';
					for (var cI = 0; cI < cols.length; cI++) {
						var pKey = cols[cI].key;
						var pVal = this.prices[i][pKey][j];
						str += '<td><input class="large ' + pKey + '" previousvalue="' + $.fbuilder.htmlEncode(pVal) + '" i="' + i + '" j="' + j + '" type="text" value="' + $.fbuilder.htmlEncode(pVal) + '" /></td>';
					}
					str += '</tr>';
				}

				str += '</tbody></table></div></div>';
			}

			str += '<div class="wpts-price-actions">' +
				'<button type="button" class="addSlotsprice wpts-btn-secondary-sm">+ Add Special Price Tier</button>' +
			'</div>';

			this._syncPriceDropdowns();
			$('#slots_prices').html(str);
			return str;
		},

		showQuantity: function (id, value) {
			var str = '<option value="1" ' + (parseInt(value, 10) === 1 ? 'selected' : '') + '>Disabled</option>';
			for (var j = 2; j <= 500; j++) {
				str += '<option value="' + j + '" ' + (parseInt(value, 10) === j ? 'selected' : '') + '>Max selection: ' + j + '</option>';
			}
			$('#' + id).html(str);
			return str;
		},

		showStartQuantity: function (id, value, maxvalue) {
			var str = '';
			var limit = parseInt(maxvalue, 10) || 1;
			for (var j = 0; j <= limit; j++) {
				str += '<option value="' + j + '" ' + (parseInt(value, 10) === j ? 'selected' : '') + '>' + j + '</option>';
			}
			$('#' + id).html(str);
			return str;
		},

		display: function () {
			var modeBadge = (parseInt(this.militaryTime, 10) === 0) ? '12h AM/PM' : '24h';
			var slotLimitBadge = (parseInt(this.min_quantity, 10) === parseInt(this.max_quantity, 10))
				? (this.max_quantity + ' slot' + (parseInt(this.max_quantity, 10) > 1 ? 's' : ''))
				: (this.min_quantity + '–' + this.max_quantity + ' slots');

			return '<div class="fields ' + this.name + ' fslots" id="field' + this.form_identifier + '-' + this.index + '">' +
				'<div class="arrow ui-icon ui-icon-play"></div>' +
				'<div title="Duplicate" class="copy ui-icon ui-icon-copy"></div>' +
				'<div title="Delete" class="remove ui-icon ui-icon-trash"></div>' +
				'<div class="wpts-fslots-card-top">' +
					'<label>' + $.fbuilder.htmlEncode(this.title) + (this.required ? '<span class="wpts-req-star">*</span>' : '') + '</label>' +
					'<div class="wpts-fslots-meta-pills">' +
						'<span class="wpts-meta-pill">' + modeBadge + '</span>' +
						'<span class="wpts-meta-pill">' + $.fbuilder.htmlEncode(slotLimitBadge) + '</span>' +
					'</div>' +
				'</div>' +
				'<div class="dfield wpts-fslots-preview-grid">' +
					'<div class="sbfieldCalendar' + this.name + ' wpts-fslots-cal-wrap"></div>' +
					'<div class="slotsCalendar' + this.name + ' wpts-fslots-slots-wrap"></div>' +
					'<span class="uh">' + $.fbuilder.htmlEncode(this.userhelp) + '</span>' +
				'</div>' +
				'<div class="clearer"></div>' +
			'</div>';
		},

		showSlots: function () {
			function isOpen(h, m, s) {
				for (var i = 0; i < s.length; i++) {
					if ((h * 60 + m >= s[i].h1 * 60 + s[i].m1) && (h * 60 + m <= s[i].h2 * 60 + s[i].m2)) {
						return 'sopen';
					}
				}
				return '';
			}
			var slots = '';
			for (var i = 0; i < 24; i++) {
				slots += '<div><div>' + i + '</div>';
				for (var j = 0; j < 60; j = j + 5) {
					slots += '<div id="d' + i + '-' + j + '" class="slot ' + isOpen(i, j, this.slots) + '"></div>';
				}
				slots += '</div>';
			}
			return slots + this.slots.length;
		},

		editItemEvents: function () {
			var me = this;
			if (typeof me.slotsavail === 'undefined') {
				me.slotsavail = $.extend(true, {}, me.slotsavailInit);
			}

			$('.dateSpecial').datepicker({
				dateFormat: me.dateFormat,
				firstDay: parseInt(me.firstDay, 10) || 0,
				onSelect: function () {
					// Keep date input ready for clicking Add without collapsing settings
				}
			});

			function msortadd(arr, newObj) {
				if (!arr) arr = [];
				var inserted = false;
				if (arr.length === 0) {
					arr[0] = $.extend({}, newObj);
					inserted = true;
				}
				for (var i = 0; i < arr.length && !inserted; i++) {
					if (newObj.min === arr[i].min) {
						// Update existing slot at the same time instead of silently dropping
						arr[i] = $.extend({}, newObj);
						inserted = true;
					} else if (i === 0 && newObj.min < arr[i].min) {
						arr.splice(i, 0, $.extend({}, newObj));
						inserted = true;
					} else if (newObj.min > arr[i].min && (arr.length === i + 1 || newObj.min < arr[i + 1].min)) {
						arr.splice(i + 1, 0, $.extend({}, newObj));
						inserted = true;
					}
				}
				return arr;
			}

			function msortSpecial(arr, d) {
				if (!arr) arr = [];
				var inserted = false;
				if (arr.length === 0) {
					arr[0] = { type: d, slots: [] };
					inserted = true;
				}
				for (var i = 0; i < arr.length && !inserted; i++) {
					if (d === arr[i].type) {
						inserted = true;
					} else if (i === 0 && d < arr[i].type) {
						arr.splice(i, 0, { type: d, slots: [] });
						inserted = true;
					} else if (d > arr[i].type && (arr.length === i + 1 || d < arr[i + 1].type)) {
						arr.splice(i + 1, 0, { type: d, slots: [] });
						inserted = true;
					}
				}
				return arr;
			}

			function updateDayCountBadge(dayKey, arr) {
				var count = arr ? arr.length : 0;
				var $badge = $('#tabs-2 .wpts-day-count[data-day="' + dayKey + '"]');
				if ($badge.length) {
					$badge.text(count + (count === 1 ? ' slot' : ' slots'));
					$badge.toggleClass('wpts-day-count-empty', count === 0);
				}
			}

			function show_day_slots(i, arr) {
				var $container = $('#day_slots' + i + ' .onlySlots');
				if (!$container.length) return;

				updateDayCountBadge(i, arr);

				if (!arr || arr.length === 0) {
					$container.html('<div class="wpts-no-slots-msg">No time slots configured for this day yet.</div>');
					return;
				}

				var str = '<div class="wpts-slot-chips-grid">';
				for (var j = 0; j < arr.length; j++) {
					var ind = parseInt(arr[j].p, 10) || 0;
					var tierName = (ind > 0 && me.prices[ind] && me.prices[ind].name) ? me.prices[ind].name : '';
					var timeStr = me.formattime(parseInt(arr[j].h1, 10) * 60 + parseInt(arr[j].m1, 10));
					str += '<div class="s wpts-slot-chip" data-slot-index="' + j + '">' +
						'<label class="wpts-slot-chip-check" title="Select for bulk delete">' +
							'<input type="checkbox" />' +
							'<span class="wpts-slot-chip-time">' + $.fbuilder.htmlEncode(timeStr) + '</span>' +
						'</label>' +
						'<span class="wpts-slot-chip-cap" title="Capacity">' + $.fbuilder.htmlEncode(arr[j].c) + '</span>' +
						(tierName ? '<span class="wpts-slot-chip-tier" title="Price tier">' + $.fbuilder.htmlEncode(tierName) + '</span>' : '') +
						'<button type="button" class="wpts-slot-chip-del" data-slot-index="' + j + '" title="Remove slot">&times;</button>' +
					'</div>';
				}
				str += '</div>';
				$container.html(str);
			}

			function show_day_slots_form(i, arr, ind) {
				var $s = $('#day_slots' + i);
				if (!$s.length) return;

				var priceOpts = '';
				for (var m = 0; m < me.prices.length; m++) {
					priceOpts += '<option value="' + m + '">' + $.fbuilder.htmlEncode(me.prices[m].name || ('Tier ' + (m + 1))) + '</option>';
				}

				var hourOpts = '';
				for (var h = 0; h < 24; h++) {
					hourOpts += '<option ' + (h === 8 ? 'selected' : '') + ' value="' + h + '">' + (h < 10 ? '0' : '') + h + '</option>';
				}

				var minOptsSingle = '';
				for (var ms = 0; ms < 60; ms++) {
					minOptsSingle += '<option ' + (ms === 0 ? 'selected' : '') + ' value="' + ms + '">' + (ms < 10 ? '0' : '') + ms + '</option>';
				}

				var minOptsBatch = '';
				for (var mb = 0; mb < 60; mb += 5) {
					minOptsBatch += '<option ' + (mb === 0 ? 'selected' : '') + ' value="' + mb + '">' + (mb < 10 ? '0' : '') + mb + '</option>';
				}

				var stepOpts = '<option value="1">1 min</option>' +
					'<option value="2">2 min</option>' +
					'<option value="3">3 min</option>' +
					'<option value="4">4 min</option>';
				for (var st = 5; st <= 90; st += 5) {
					stepOpts += '<option ' + (st === 30 ? 'selected' : '') + ' value="' + st + '">' + st + ' min</option>';
					if (st === 10) stepOpts += '<option value="12">12 min</option>';
				}
				stepOpts += '<option value="120">2 hours</option>' +
					'<option value="180">3 hours</option>' +
					'<option value="240">4 hours</option>' +
					'<option value="300">5 hours</option>' +
					'<option value="360">6 hours</option>' +
					'<option value="420">7 hours</option>' +
					'<option value="480">8 hours</option>';

				var endHourOpts = '';
				for (var eh = 0; eh < 24; eh++) {
					endHourOpts += '<option ' + (eh === 17 ? 'selected' : '') + ' value="' + eh + '">' + (eh < 10 ? '0' : '') + eh + '</option>';
				}

				var countOpts = '';
				for (var c = 1; c < 100; c++) {
					countOpts += '<option ' + (c === 4 ? 'selected' : '') + ' value="' + c + '">' + c + '</option>';
				}

				var isWeeklyDay = (String(i).length === 1);

				var str = '<div class="add_slots_f" i="' + i + '" ind="' + ind + '" data-gen-mode="range">' +
					'<div class="wpts-slot-gen-box">' +
						'<div class="wpts-slot-gen-hdr">' +
							'<span class="wpts-slot-gen-title">Generate multiple slots</span>' +
							'<div class="wpts-gen-mode-tabs">' +
								'<button type="button" class="wpts-gen-mode-btn is-active" data-mode="range">End time</button>' +
								'<button type="button" class="wpts-gen-mode-btn" data-mode="count">Count</button>' +
							'</div>' +
						'</div>' +
						'<div class="wpts-slot-gen-row">' +
							'<div class="wpts-mini-field"><span class="wpts-mini-lbl">Start</span><div class="wpts-time-pair"><select class="h2">' + hourOpts + '</select><span class="wpts-time-sep">:</span><select class="m2">' + minOptsBatch + '</select></div></div>' +
							'<div class="wpts-mini-field wpts-gen-end-wrap"><span class="wpts-mini-lbl">End</span><div class="wpts-time-pair"><select class="eh2">' + endHourOpts + '</select><span class="wpts-time-sep">:</span><select class="em2">' + minOptsBatch + '</select></div></div>' +
							'<div class="wpts-mini-field wpts-mini-cap wpts-gen-count-wrap" style="display:none;"><span class="wpts-mini-lbl">Count</span><select class="n2">' + countOpts + '</select></div>' +
						'</div>' +
						'<div class="wpts-slot-gen-row wpts-slot-gen-row-bottom">' +
							'<div class="wpts-mini-field"><span class="wpts-mini-lbl">Every</span><select class="s2">' + stepOpts + '</select></div>' +
							'<div class="wpts-mini-field wpts-mini-cap"><span class="wpts-mini-lbl">Capacity</span><input type="text" class="c2" value="1" /></div>' +
							'<div class="wpts-mini-field wpts-mini-price"><span class="wpts-mini-lbl">Price</span><select class="p2">' + priceOpts + '</select></div>' +
							'<div class="wpts-mini-action"><button type="button" class="add2 wpts-btn-primary-xs">+ Generate <span class="wpts-gen-preview-count">(19)</span></button></div>' +
						'</div>' +
					'</div>' +
					'<div class="wpts-slot-gen-box">' +
						'<div class="wpts-slot-gen-title">Add single time slot</div>' +
						'<div class="wpts-slot-gen-row">' +
							'<div class="wpts-mini-field"><span class="wpts-mini-lbl">Time</span><div class="wpts-time-pair"><select class="h1">' + hourOpts + '</select><span class="wpts-time-sep">:</span><select class="m1">' + minOptsSingle + '</select></div></div>' +
							'<div class="wpts-mini-field wpts-mini-cap"><span class="wpts-mini-lbl">Capacity</span><input type="text" class="c1" value="1" /></div>' +
							'<div class="wpts-mini-field wpts-mini-price"><span class="wpts-mini-lbl">Price</span><select class="p1">' + priceOpts + '</select></div>' +
							'<div class="wpts-mini-action"><button type="button" class="add1 wpts-btn-primary-xs">+ Add</button></div>' +
						'</div>' +
					'</div>' +
				'</div>' +
				'<div class="onlySlots" i="' + i + '" ind="' + ind + '"></div>' +
				'<div class="wpts-slot-bulk-bar">' +
					'<div class="wpts-slot-bulk-left">' +
						'<button type="button" class="wpts-btn-ghost-xs wpts-select-all-slots">Select all</button>' +
						'<button type="button" class="deletemarked wpts-btn-danger-xs">Delete marked</button>' +
					'</div>' +
					'<div class="wpts-slot-bulk-right">' +
						(isWeeklyDay ? '<button type="button" class="wpts-apply-all-days wpts-btn-soft-xs" title="Replace all other enabled working days with this day\'s slots">Apply to working days</button>' : '') +
						'<button type="button" class="copySlots wpts-btn-ghost-xs" title="Copy this day\'s slots">Copy</button>' +
						'<button type="button" class="pasteSlots wpts-btn-ghost-xs" title="Paste copied slots into this day">Paste</button>' +
					'</div>' +
				'</div>';

				$s.html(str);
				show_day_slots(i, arr);
			}

			function showSpecialDates() {
				var str = '';
				if (!me.slots_special || me.slots_special.length === 0) {
					$('#special_dates').html('<div class="wpts-no-special-msg">No special override dates added.</div>');
					return;
				}
				for (var i = 0; i < me.slots_special.length; i++) {
					var dateKey = me.slots_special[i].type;
					var count = me.slots_special[i].slots ? me.slots_special[i].slots.length : 0;
					str += '<div class="wpts-day-item wpts-special-day-item">' +
						'<div class="day_header">' +
							'<div class="wpts-day-header-left">' +
								'<span class="wpts-special-date-label">' + $.fbuilder.htmlEncode(dateKey) + '</span>' +
							'</div>' +
							'<div class="wpts-day-header-right">' +
								'<span class="wpts-day-count ' + (count === 0 ? 'wpts-day-count-empty' : '') + '" data-day="' + $.fbuilder.htmlEncode(dateKey) + '">' + count + (count === 1 ? ' slot' : ' slots') + '</span>' +
								'<a href="#" class="openSpecial wpts-day-toggle" n="' + $.fbuilder.htmlEncode(dateKey) + '">Edit</a>' +
								'<a href="#" class="deleteSpecial wpts-special-del-btn" ind="' + i + '" title="Remove special date">&times;</a>' +
							'</div>' +
						'</div>' +
						'<div id="day_slots' + $.fbuilder.htmlEncode(dateKey) + '" i="' + $.fbuilder.htmlEncode(dateKey) + '" class="day_slots" ind="' + i + '"></div>' +
					'</div>';
				}
				$('#special_dates').html(str);
				for (var k = 0; k < me.slots_special.length; k++) {
					show_day_slots_form(me.slots_special[k].type, me.slots_special[k].slots, k);
				}
			}

			try {
				for (var dIdx = 0; dIdx < 7; dIdx++) {
					show_day_slots_form(dIdx, me.slotsavail['d' + dIdx], 0);
				}
			} catch (e) {}
			showSpecialDates();

			var evt = [
				{ s: '#sMin_quantity', e: 'change', l: 'min_quantity' },
				{
					s: '#sMax_quantity',
					e: 'change',
					l: 'max_quantity',
					f: function (el) {
						var v = (parseInt(el.val(), 10) >= 1);
						$('.max_quantity_scope')[v ? 'show' : 'hide']();
						me.max_quantity = el.val();
						me.showSlotsPrices();
						return el.val();
					}
				},
				{
					s: '#sQuantity1',
					e: 'change',
					l: 'quantity1',
					f: function (el) {
						var v = (parseInt(el.val(), 10) > 1);
						$('.quantity1_scope')[v ? 'show' : 'hide']();
						me.quantity1 = el.val();
						me.showStartQuantity('sQuantity1start', me.quantity1start, me.quantity1);
						me.showSlotsPrices();
						return el.val();
					}
				},
				{
					s: '#sQuantity1start',
					e: 'change',
					l: 'quantity1start',
					f: function (el) {
						me.quantity1start = el.val();
						me.showSlotsPrices();
						return el.val();
					}
				},
				{
					s: '#sLabel1',
					e: 'input keyup',
					l: 'label1',
					f: function (el) {
						me.label1 = el.val();
						me.showSlotsPrices();
						return el.val();
					}
				},
				{
					s: '#sQuantity2',
					e: 'change',
					l: 'quantity2',
					f: function (el) {
						var v = (parseInt(el.val(), 10) > 1);
						$('.quantity2_scope')[v ? 'show' : 'hide']();
						me.quantity2 = el.val();
						me.showSlotsPrices();
						return el.val();
					}
				},
				{
					s: '#sLabel2',
					e: 'input keyup',
					l: 'label2',
					f: function (el) {
						me.label2 = el.val();
						me.showSlotsPrices();
						return el.val();
					}
				},
				{
					s: '#sQuantity3',
					e: 'change',
					l: 'quantity3',
					f: function (el) {
						var v = (parseInt(el.val(), 10) > 1);
						$('.quantity3_scope')[v ? 'show' : 'hide']();
						me.quantity3 = el.val();
						me.showSlotsPrices();
						return el.val();
					}
				},
				{
					s: '#sLabel3',
					e: 'input keyup',
					l: 'label3',
					f: function (el) {
						me.label3 = el.val();
						me.showSlotsPrices();
						return el.val();
					}
				},
				{
					s: '#sQuantity4',
					e: 'change',
					l: 'quantity4',
					f: function (el) {
						var v = (parseInt(el.val(), 10) > 1);
						$('.quantity4_scope')[v ? 'show' : 'hide']();
						me.quantity4 = el.val();
						me.showSlotsPrices();
						return el.val();
					}
				},
				{
					s: '#sLabel4',
					e: 'input keyup',
					l: 'label4',
					f: function (el) {
						me.label4 = el.val();
						me.showSlotsPrices();
						return el.val();
					}
				},
				{
					s: '#sQuantity5',
					e: 'change',
					l: 'quantity5',
					f: function (el) {
						var v = (parseInt(el.val(), 10) > 1);
						$('.quantity5_scope')[v ? 'show' : 'hide']();
						me.quantity5 = el.val();
						me.showSlotsPrices();
						return el.val();
					}
				},
				{
					s: '#sLabel5',
					e: 'input keyup',
					l: 'label5',
					f: function (el) {
						me.label5 = el.val();
						me.showSlotsPrices();
						return el.val();
					}
				},
				{
					s: '#sUsedSlotsCheckbox',
					e: 'change',
					l: 'usedSlotsCheckbox',
					f: function (el) {
						return el.is(':checked');
					}
				},
				{ s: '#sDateFormat', e: 'change', l: 'dateFormat' },
				{ s: '#sNumberOfMonths', e: 'change', l: 'numberOfMonths' },
				{ s: '#sFirstDay', e: 'change', l: 'firstDay' },
				{
					s: '#sMilitaryTime',
					e: 'change',
					l: 'militaryTime',
					f: function (el) {
						me.militaryTime = parseInt(el.val(), 10);
						for (var d = 0; d < 7; d++) {
							show_day_slots(d, me.slotsavail['d' + d]);
						}
						for (var sp = 0; sp < me.slots_special.length; sp++) {
							show_day_slots(me.slots_special[sp].type, me.slots_special[sp].slots);
						}
						var $tPill = $('#tabs-2 .wpts-time-mode-pill');
						if ($tPill.length) {
							$tPill.text(me.militaryTime === 0 ? '12h AM/PM' : '24h');
						}
						return me.militaryTime;
					}
				},
				{ s: '#sMinDate', e: 'change', l: 'minDate' },
				{ s: '#sMaxDate', e: 'change', l: 'maxDate' },
				{ s: '#sInvalidDates', e: 'change', l: 'invalidDates' },
				{ s: '#sDefaultDate', e: 'change', l: 'defaultDate' },
				{ s: '#sDropdownRange', e: 'input keyup', l: 'dropdownRange' },
				{ s: '#sShowTotalCostFormat', e: 'input keyup', l: 'showTotalCostFormat' },
				{
					s: '#sShowTotalCost',
					e: 'change',
					l: 'showTotalCost',
					f: function (el) {
						var v = el.is(':checked');
						$('#divTotalCostFormat')[v ? 'show' : 'hide']();
						$('#wpts_pricing_matrix_wrap')[v ? 'show' : 'hide']();
						var $pill = $('#tabs-2 .wpts-pricing-mode-pill');
						if ($pill.length) {
							$pill.text(v ? 'Pricing On' : 'Free');
						}
						return v;
					}
				},
				{
					s: '#sShowDropdown',
					e: 'change',
					l: 'showDropdown',
					f: function (el) {
						var v = el.is(':checked');
						$('#divdropdownRange')[v ? 'show' : 'hide']();
						return v;
					}
				},
				{
					s: '#sFlatPrice',
					e: 'change',
					l: 'flatPrice',
					f: function (el) {
						return el.is(':checked');
					}
				}
			];

			// Delegated price cell editing (price1..price5)
			var $tabs2 = $('#tabs-2');
			$tabs2.off('.wptsSlots');

			for (var pNum = 1; pNum <= 5; pNum++) {
				(function (priceKey) {
					$tabs2.on('keyup.wptsSlots change.wptsSlots', 'input.' + priceKey, function () {
						var i = parseInt($(this).attr('i'), 10),
							j = parseInt($(this).attr('j'), 10),
							v = $(this).val(),
							pv = $(this).attr('previousvalue'),
							maxQty = parseInt(me.max_quantity, 10) || 1;

						for (var k = j; k < maxQty; k++) {
							var $cell = $tabs2.find('input.' + priceKey + '[i="' + i + '"][j="' + k + '"]');
							if ($cell.attr('previousvalue') === pv) {
								$cell.val(v);
								me.prices[i][priceKey][k] = v;
							}
						}
						me.prices[i][priceKey][j] = v;
						$.fbuilder.reloadItems({ field: me });
					});
				})('price' + pNum);
			}

			$tabs2.on('click.wptsSlots', '.addSlotsprice', function (e) {
				e.preventDefault();
				me.prices.push({
					name: 'special ' + me.prices.length,
					price1: [],
					price2: [],
					price3: [],
					price4: [],
					price5: []
				});
				me.showSlotsPrices();
				$.fbuilder.reloadItems({ field: me });
			});

			$tabs2.on('click.wptsSlots', '.deleteSlotsprice', function (e) {
				e.preventDefault();
				var delIdx = parseInt($(this).attr('i'), 10);
				if (isNaN(delIdx) || delIdx <= 0) return;
				me.prices.splice(delIdx, 1);

				// Keep slot price references valid after tier removal
				var fixSlotPrices = function (arr) {
					if (!arr) return;
					for (var s = 0; s < arr.length; s++) {
						var pVal = parseInt(arr[s].p, 10) || 0;
						if (pVal === delIdx) arr[s].p = 0;
						else if (pVal > delIdx) arr[s].p = pVal - 1;
					}
				};
				for (var d = 0; d < 7; d++) {
					fixSlotPrices(me.slotsavail['d' + d]);
					show_day_slots(d, me.slotsavail['d' + d]);
				}
				for (var sp = 0; sp < me.slots_special.length; sp++) {
					fixSlotPrices(me.slots_special[sp].slots);
					show_day_slots(me.slots_special[sp].type, me.slots_special[sp].slots);
				}

				me.showSlotsPrices();
				$.fbuilder.reloadItems({ field: me });
			});

			$tabs2.on('input.wptsSlots keyup.wptsSlots', 'input.nameSlotsprice', function () {
				var idx = parseInt($(this).attr('i'), 10);
				if (!me.prices[idx]) return;
				me.prices[idx].name = $(this).val();
				me._syncPriceDropdowns();
				$.fbuilder.reloadItems({ field: me });
			});

			// Working day enable/disable checkbox
			$tabs2.on('change.wptsSlots', '.sb_working_dates .day_header input[type="checkbox"]', function () {
				var dayIndex = parseInt($(this).val(), 10);
				var isChecked = $(this).is(':checked');
				me.working_dates[dayIndex] = isChecked;
				$(this).closest('.wpts-day-item').toggleClass('wpts-day-disabled', !isChecked);
				$.fbuilder.reloadItems({ field: me });
			});

			// Day accordion open/close
			$tabs2.on('click.wptsSlots', '.sb_working_dates .openSpecial', function (e) {
				e.preventDefault();
				var dayKey = $(this).attr('n');
				var $drawer = $('#day_slots' + dayKey);
				var $header = $(this).closest('.day_header');
				var willOpen = !$drawer.is(':visible');

				$tabs2.find('.sb_working_dates .day_slots').hide();
				$tabs2.find('.sb_working_dates .day_header').removeClass('open');
				$tabs2.find('.sb_working_dates .openSpecial').text('Edit');

				if (willOpen) {
					$drawer.show();
					$header.addClass('open');
					$(this).text('Close');
				}
			});

			// Add Special Date
			$tabs2.on('click.wptsSlots', '.sb_working_dates .addSpecial', function (e) {
				e.preventDefault();
				var rawVal = $.trim($('.dateSpecial').val());
				if (rawVal === '') return;
				try {
					var d = $.datepicker.formatDate('yy-mm-dd', $.datepicker.parseDate(me.dateFormat, rawVal));
					me.slots_special = msortSpecial(me.slots_special, d);
					$('.dateSpecial').val('');
					showSpecialDates();
					// Auto-open the newly added special date drawer
					$tabs2.find('.sb_working_dates .openSpecial[n="' + d + '"]').trigger('click');
					$.fbuilder.reloadItems({ field: me });
				} catch (err) {}
			});

			// Delete Special Date
			$tabs2.on('click.wptsSlots', '.sb_working_dates .deleteSpecial', function (e) {
				e.preventDefault();
				var ind = parseInt($(this).attr('ind'), 10);
				if (isNaN(ind)) return;
				me.slots_special.splice(ind, 1);
				showSpecialDates();
				$.fbuilder.reloadItems({ field: me });
			});

			// Add single slot
			$tabs2.on('click.wptsSlots', '.sb_working_dates .add_slots_f .add1', function (e) {
				e.preventDefault();
				var $f = $(this).closest('.add_slots_f');
				var d = String($f.attr('i'));
				var h1 = parseInt($f.find('.h1').val(), 10) || 0;
				var m1 = parseInt($f.find('.m1').val(), 10) || 0;
				var c1 = Math.max(1, parseInt($f.find('.c1').val(), 10) || 1);
				var p1 = parseInt($f.find('.p1').val(), 10) || 0;
				var newObj = { h1: h1, m1: m1, c: c1, p: p1, min: h1 * 60 + m1 };

				if (d.length === 1) {
					me.slotsavail['d' + d] = msortadd(me.slotsavail['d' + d], newObj);
					show_day_slots(d, me.slotsavail['d' + d]);
				} else {
					var ind = parseInt($f.attr('ind'), 10);
					me.slots_special[ind].slots = msortadd(me.slots_special[ind].slots, newObj);
					show_day_slots(d, me.slots_special[ind].slots);
				}
				$.fbuilder.reloadItems({ field: me });
			});

			function computeBatchSlotCount($f) {
				var mode = $f.attr('data-gen-mode') || 'range';
				var step = Math.max(1, parseInt($f.find('.s2').val(), 10) || 30);
				var hStart = parseInt($f.find('.h2').val(), 10) || 0;
				var mStart = parseInt($f.find('.m2').val(), 10) || 0;
				var initialMin = hStart * 60 + mStart;

				if (mode === 'count') {
					var n = parseInt($f.find('.n2').val(), 10) || 1;
					var validCount = 0;
					for (var k = 0; k < n; k++) {
						if (initialMin + step * k >= 24 * 60) break;
						validCount++;
					}
					return validCount;
				}

				var hEnd = parseInt($f.find('.eh2').val(), 10) || 0;
				var mEnd = parseInt($f.find('.em2').val(), 10) || 0;
				var endMin = hEnd * 60 + mEnd;
				if (endMin < initialMin) return 0;
				return Math.floor((endMin - initialMin) / step) + 1;
			}

			function updateBatchPreview($f) {
				var cnt = computeBatchSlotCount($f);
				$f.find('.wpts-gen-preview-count').text('(' + cnt + ')');
			}

			// Toggle between End-time range mode and Count mode
			$tabs2.on('click.wptsSlots', '.sb_working_dates .wpts-gen-mode-btn', function (e) {
				e.preventDefault();
				var mode = $(this).attr('data-mode');
				var $f = $(this).closest('.add_slots_f');
				$f.attr('data-gen-mode', mode);
				$f.find('.wpts-gen-mode-btn').removeClass('is-active');
				$(this).addClass('is-active');
				$f.find('.wpts-gen-end-wrap')[mode === 'range' ? 'show' : 'hide']();
				$f.find('.wpts-gen-count-wrap')[mode === 'count' ? 'show' : 'hide']();
				updateBatchPreview($f);
			});

			// Live update slot count preview when range/interval changes
			$tabs2.on('change.wptsSlots', '.sb_working_dates .add_slots_f .h2, .sb_working_dates .add_slots_f .m2, .sb_working_dates .add_slots_f .eh2, .sb_working_dates .add_slots_f .em2, .sb_working_dates .add_slots_f .s2, .sb_working_dates .add_slots_f .n2', function () {
				updateBatchPreview($(this).closest('.add_slots_f'));
			});

			// Generate multiple slots (supports Start->End range and Count modes)
			$tabs2.on('click.wptsSlots', '.sb_working_dates .add_slots_f .add2', function (e) {
				e.preventDefault();
				var $f = $(this).closest('.add_slots_f');
				var step = Math.max(1, parseInt($f.find('.s2').val(), 10) || 30);
				var hStart = parseInt($f.find('.h2').val(), 10) || 0;
				var mStart = parseInt($f.find('.m2').val(), 10) || 0;
				var c2 = Math.max(1, parseInt($f.find('.c2').val(), 10) || 1);
				var p2 = parseInt($f.find('.p2').val(), 10) || 0;
				var initialMin = hStart * 60 + mStart;
				var dayofweek = String($f.attr('i'));
				var countToGen = computeBatchSlotCount($f);
				if (countToGen <= 0) return;

				// Safety cap at 200 slots per click
				countToGen = Math.min(countToGen, 200);

				for (var i = 0; i < countToGen; i++) {
					var totalMin = initialMin + step * i;
					if (totalMin >= 24 * 60) break; // Never create invalid times past 23:59
					var h = Math.floor(totalMin / 60);
					var m = totalMin % 60;
					var newObj = { h1: h, m1: m, c: c2, p: p2, min: totalMin };
					if (dayofweek.length === 1) {
						me.slotsavail['d' + dayofweek] = msortadd(me.slotsavail['d' + dayofweek], newObj);
					} else {
						var ind = parseInt($f.attr('ind'), 10);
						me.slots_special[ind].slots = msortadd(me.slots_special[ind].slots, newObj);
					}
				}

				if (dayofweek.length === 1) {
					show_day_slots(dayofweek, me.slotsavail['d' + dayofweek]);
				} else {
					var indSp = parseInt($f.attr('ind'), 10);
					show_day_slots(dayofweek, me.slots_special[indSp].slots);
				}
				$.fbuilder.reloadItems({ field: me });
			});

			// One-click Apply to all enabled working days (replaces slots on other checked days)
			$tabs2.on('click.wptsSlots', '.sb_working_dates .day_slots .wpts-apply-all-days', function (e) {
				e.preventDefault();
				var $btn = $(this);
				var $drawer = $btn.closest('.day_slots');
				var srcDay = String($drawer.attr('i'));
				if (srcDay.length !== 1) return;

				var srcSlots = me.slotsavail['d' + srcDay] || [];
				var appliedCount = 0;
				for (var d = 0; d < 7; d++) {
					if (String(d) === srcDay) continue;
					if (me.working_dates[d]) {
						me.slotsavail['d' + d] = $.map(srcSlots, function (item) {
							return $.extend({}, item);
						});
						show_day_slots(d, me.slotsavail['d' + d]);
						appliedCount++;
					}
				}

				$.fbuilder.reloadItems({ field: me });

				var origLabel = $btn.attr('data-orig-label') || $btn.text();
				$btn.attr('data-orig-label', origLabel);
				$btn.text(appliedCount > 0 ? ('✓ Applied to ' + appliedCount + (appliedCount === 1 ? ' day' : ' days')) : 'No other working days').addClass('wpts-btn-copied');
				setTimeout(function () {
					$btn.text(origLabel).removeClass('wpts-btn-copied');
				}, 1800);
			});

			// Keep dateFormat aligned when Step 2's Date Format select changes
			$(document).off('wptsDateFormatChanged.wptsSlotsGlobal').on('wptsDateFormatChanged.wptsSlotsGlobal', function (e, newFmt) {
				if (newFmt && typeof newFmt === 'string') {
					me.dateFormat = newFmt;
					$.fbuilder.reloadItems({ field: me });
				}
			});

			// Quick delete single slot chip
			$tabs2.on('click.wptsSlots', '.sb_working_dates .wpts-slot-chip-del', function (e) {
				e.preventDefault();
				e.stopPropagation();
				var slotIdx = parseInt($(this).attr('data-slot-index'), 10);
				var $drawer = $(this).closest('.day_slots');
				var dayofweek = String($drawer.attr('i'));

				if (dayofweek.length === 1) {
					me.slotsavail['d' + dayofweek].splice(slotIdx, 1);
					show_day_slots(dayofweek, me.slotsavail['d' + dayofweek]);
				} else {
					var ind = parseInt($drawer.attr('ind'), 10);
					me.slots_special[ind].slots.splice(slotIdx, 1);
					show_day_slots(dayofweek, me.slots_special[ind].slots);
				}
				$.fbuilder.reloadItems({ field: me });
			});

			// Select / Deselect all slots in a day
			$tabs2.on('click.wptsSlots', '.sb_working_dates .wpts-select-all-slots', function (e) {
				e.preventDefault();
				var $drawer = $(this).closest('.day_slots');
				var $checks = $drawer.find('.onlySlots input[type="checkbox"]');
				var allChecked = $checks.length > 0 && $checks.filter(':checked').length === $checks.length;
				$checks.prop('checked', !allChecked);
				$(this).text(allChecked ? 'Select all' : 'Unselect all');
			});

			// Copy slots (deep-cloned so later edits don't mutate clipboard)
			$tabs2.on('click.wptsSlots', '.sb_working_dates .day_slots .copySlots', function (e) {
				e.preventDefault();
				var $btn = $(this);
				var $drawer = $btn.closest('.day_slots');
				var dayofweek = String($drawer.attr('i'));
				var src = (dayofweek.length === 1)
					? (me.slotsavail['d' + dayofweek] || [])
					: (me.slots_special[parseInt($drawer.attr('ind'), 10)].slots || []);

				me.tmpCopy = $.map(src, function (item) {
					return $.extend({}, item);
				});

				$btn.text('Copied (' + me.tmpCopy.length + ')').addClass('wpts-btn-copied');
				setTimeout(function () {
					$btn.text('Copy').removeClass('wpts-btn-copied');
				}, 1300);
			});

			// Paste slots
			$tabs2.on('click.wptsSlots', '.sb_working_dates .day_slots .pasteSlots', function (e) {
				e.preventDefault();
				me.tmpCopy = me.tmpCopy || [];
				if (me.tmpCopy.length === 0) return;

				var $drawer = $(this).closest('.day_slots');
				var dayofweek = String($drawer.attr('i'));
				if (dayofweek.length === 1) {
					for (var i = 0; i < me.tmpCopy.length; i++) {
						me.slotsavail['d' + dayofweek] = msortadd(me.slotsavail['d' + dayofweek], $.extend({}, me.tmpCopy[i]));
					}
					show_day_slots(dayofweek, me.slotsavail['d' + dayofweek]);
				} else {
					var ind = parseInt($drawer.attr('ind'), 10);
					for (var j = 0; j < me.tmpCopy.length; j++) {
						me.slots_special[ind].slots = msortadd(me.slots_special[ind].slots, $.extend({}, me.tmpCopy[j]));
					}
					show_day_slots(dayofweek, me.slots_special[ind].slots);
				}
				$.fbuilder.reloadItems({ field: me });
			});

			// Delete marked slots
			$tabs2.on('click.wptsSlots', '.sb_working_dates .day_slots .deletemarked', function (e) {
				e.preventDefault();
				var $drawer = $(this).closest('.day_slots');
				var dayofweek = String($drawer.attr('i'));
				var toDeleted = [];
				$drawer.find('.onlySlots input[type="checkbox"]').each(function (index) {
					if ($(this).is(':checked')) {
						toDeleted.push(index);
					}
				});
				if (toDeleted.length === 0) return;

				if (dayofweek.length === 1) {
					for (var i = toDeleted.length - 1; i >= 0; i--) {
						me.slotsavail['d' + dayofweek].splice(toDeleted[i], 1);
					}
					show_day_slots(dayofweek, me.slotsavail['d' + dayofweek]);
				} else {
					var ind = parseInt($drawer.attr('ind'), 10);
					for (var j = toDeleted.length - 1; j >= 0; j--) {
						me.slots_special[ind].slots.splice(toDeleted[j], 1);
					}
					show_day_slots(dayofweek, me.slots_special[ind].slots);
				}
				$.fbuilder.reloadItems({ field: me });
			});

			// Section collapse toggles inside fslots settings
			$tabs2.on('click.wptsSlots', '.wpts-fslots-section-toggle', function (e) {
				e.preventDefault();
				var $sec = $(this).closest('.wpts-fslots-section');
				$sec.toggleClass('wpts-section-collapsed');
			});

			$.fbuilder.controls['ffields'].prototype.editItemEvents.call(this, evt);
		},

		after_show: function () {
			function closeOtherDatepicker() {
				$('#ui-datepicker-div').css('display', 'none');
			}
			setTimeout(closeOtherDatepicker, 100);

			var me = this,
				e = $('#field' + me.form_identifier + '-' + me.index + ' .sbfieldCalendar' + me.name);

			function onChangeDate(d) {
				var parsed;
				try {
					parsed = $.datepicker.parseDate(me.dateFormat, d);
				} catch (err) {
					return;
				}
				var day = $.datepicker.formatDate('yy-mm-dd', parsed);
				var ind = -1;
				for (var i = 0; ind === -1 && i < me.slots_special.length; i++) {
					if (me.slots_special[i].type === day) ind = i;
				}
				var arr = (ind === -1)
					? me.slotsavail['d' + parsed.getDay()]
					: me.slots_special[ind].slots;

				var str = '';
				if (arr && arr.length > 0) {
					for (var j = 0; j < arr.length; j++) {
						var capNum = parseInt(arr[j].c, 10) || 1;
						str += '<div class="wpts-preview-slot-pill">' +
							'<span>' + $.fbuilder.htmlEncode(me.formattime(parseInt(arr[j].h1, 10) * 60 + parseInt(arr[j].m1, 10))) + '</span>' +
							(capNum > 1 ? '<small class="wpts-preview-slot-cap">&times;' + capNum + '</small>' : '') +
						'</div>';
					}
				} else {
					str = '<span class="wpts-preview-no-slots">No available slots for this date</span>';
				}

				var $s = $('#field' + me.form_identifier + '-' + me.index + ' .slotsCalendar' + me.name);
				$s.html(
					'<div class="slots wpts-preview-slots-box">' +
						'<div class="wpts-preview-slots-hdr">' +
							'<strong>' + $.fbuilder.htmlEncode(d) + '</strong>' +
							'<span>' + (arr ? arr.length : 0) + ' available</span>' +
						'</div>' +
						'<div class="wpts-preview-slots-list">' + str + '</div>' +
					'</div>'
				);
			}

			if (typeof me.slotsavail === 'undefined') {
				me.slotsavail = $.extend(true, {}, me.slotsavailInit);
			}

			for (var i = 0; i < me.prices.length; i++) {
				me.prices[i].price1 = me.prices[i].price1 || [];
				me.prices[i].price2 = me.prices[i].price2 || [];
				me.prices[i].price3 = me.prices[i].price3 || [];
				me.prices[i].price4 = me.prices[i].price4 || [];
				me.prices[i].price5 = me.prices[i].price5 || [];
				for (var j = 0; j < me.prices[i].price1.length; j++) {
					if (me.prices[i].price3.length <= j) me.prices[i].price3[j] = 0;
					if (me.prices[i].price4.length <= j) me.prices[i].price4[j] = 0;
					if (me.prices[i].price5.length <= j) me.prices[i].price5[j] = 0;
				}
			}

			var minDateStr = (me.minDate !== null && typeof me.minDate !== 'undefined') ? String(me.minDate) : '';
			me.minDateTmp = minDateStr;
			if (minDateStr.toLowerCase().indexOf('h') !== -1) {
				var h = parseFloat(minDateStr.toLowerCase().replace(/h/gi, '')) || 0;
				me.minDateTmp = new Date((new Date()).getTime() + h * 60 * 60 * 1000);
			} else if (minDateStr !== '' && minDateStr == parseInt(minDateStr, 10)) {
				me.minDateTmp = new Date((new Date()).getTime() + parseInt(minDateStr, 10) * 24 * 60 * 60 * 1000);
			}

			this.tmpinvalidDates = String(this.invalidDates || '').replace(/\s+/g, '');
			if (!/^\s*$/.test(this.tmpinvalidDates)) {
				var dateRegExp = /^\d{1,2}\/\d{1,2}\/\d{4}$/,
					counter = 0,
					dates = this.tmpinvalidDates.split(',');
				this.tmpinvalidDates = [];
				for (var dI = 0, dH = dates.length; dI < dH; dI++) {
					var range = dates[dI].split('-');
					if (range.length === 2 && dateRegExp.test(range[0]) && dateRegExp.test(range[1])) {
						var fromD = new Date(range[0]),
							toD = new Date(range[1]);
						var rangeGuard = 0;
						while (fromD <= toD && rangeGuard < 1000) {
							this.tmpinvalidDates[counter++] = fromD;
							var tmp = new Date(fromD.valueOf());
							tmp.setDate(tmp.getDate() + 1);
							fromD = tmp;
							rangeGuard++;
						}
					} else {
						for (var rJ = 0, rK = range.length; rJ < rK; rJ++) {
							if (dateRegExp.test(range[rJ])) {
								this.tmpinvalidDates[counter++] = new Date(range[rJ]);
							}
						}
					}
				}
			} else {
				this.tmpinvalidDates = [];
			}

			me.special_days = [];
			for (var spI = 0; spI < me.slots_special.length; spI++) {
				me.special_days.push(me.slots_special[spI].type);
			}

			e.datepicker({
				numberOfMonths: parseInt(me.numberOfMonths, 10) || 1,
				firstDay: parseInt(me.firstDay, 10) || 0,
				minDate: me.minDateTmp,
				maxDate: me.maxDate,
				dateFormat: me.dateFormat,
				defaultDate: me.defaultDate,
				changeMonth: !!me.showDropdown,
				changeYear: !!me.showDropdown,
				yearRange: me.showDropdown ? me.dropdownRange : '',
				onSelect: function (d) {
					onChangeDate(d);
				},
				beforeShowDay: function (d) {
					var day = $.datepicker.formatDate('yy-mm-dd', d);
					var c = [day];
					var isSpecial = ($.inArray(day, me.special_days) !== -1);
					if (isSpecial) c.push('specialDates');
					if (!me.working_dates[d.getDay()] && !isSpecial) {
						c.push('nonworking');
					}
					for (var k = 0, l = me.tmpinvalidDates.length; k < l; k++) {
						if (d.getTime() === me.tmpinvalidDates[k].getTime()) {
							c.push('nonworking invalidDate');
						}
					}
					return [true, c.join(' ')];
				}
			});

			me.tmpinvalidDatestime = [];
			for (var invI = 0; invI < me.tmpinvalidDates.length; invI++) {
				me.tmpinvalidDatestime[invI] = me.tmpinvalidDates[invI].getTime();
			}

			function isDateSelectable(date) {
				if (!date) return true;
				var currentdate = date.getTime();
				if ($.inArray(currentdate, me.tmpinvalidDatestime) > -1) return false;
				if (!me.working_dates[date.getDay()]) return false;
				return true;
			}

			var sum = 0;
			for (var wI = 0; wI < 7; wI++) {
				sum += (me.working_dates[wI] ? 1 : 0);
			}
			if (sum > 0) {
				var nextdateAvailable = e.datepicker('getDate') || new Date();
				var safety = 0;
				while (!isDateSelectable(nextdateAvailable) && safety < 730) {
					nextdateAvailable.setDate(nextdateAvailable.getDate() + 1);
					safety++;
				}
				e.datepicker('setDate', nextdateAvailable);
				me.getD = nextdateAvailable;
				onChangeDate($.datepicker.formatDate(me.dateFormat, nextdateAvailable));
			}
		},

		showSpecialDataInstance: function () {
			if (typeof this.slotsavail === 'undefined') {
				this.slotsavail = $.extend(true, {}, this.slotsavailInit);
			}
			var dayNames = this._getDayNames();
			var str = '';

			// SECTION 1: Weekly Schedule & Special Dates
			str += '<div class="wpts-fslots-section">' +
				'<div class="wpts-fslots-section-header wpts-fslots-section-toggle">' +
					'<span class="wpts-fslots-section-title">Weekly Schedule &amp; Special Dates</span>' +
					'<span class="wpts-fslots-chevron" aria-hidden="true">&#9662;</span>' +
				'</div>' +
				'<div class="wpts-fslots-section-body">' +
					'<div class="sb_working_dates">';

			for (var i = 0; i < 7; i++) {
				var isWorking = !!this.working_dates[i];
				var dayArr = this.slotsavail['d' + i] || [];
				var cnt = dayArr.length;
				str += '<div class="wpts-day-item ' + (isWorking ? '' : 'wpts-day-disabled') + '">' +
					'<div class="day_header">' +
						'<label class="wpts-day-header-left" for="sWD' + i + '">' +
							'<input name="sWD' + i + '" id="sWD' + i + '" value="' + i + '" type="checkbox" ' + (isWorking ? 'checked' : '') + ' />' +
							'<span>' + $.fbuilder.htmlEncode(dayNames[i]) + '</span>' +
						'</label>' +
						'<div class="wpts-day-header-right">' +
							'<span class="wpts-day-count ' + (cnt === 0 ? 'wpts-day-count-empty' : '') + '" data-day="' + i + '">' + cnt + (cnt === 1 ? ' slot' : ' slots') + '</span>' +
							'<a class="openSpecial wpts-day-toggle" href="#" n="' + i + '">Edit</a>' +
						'</div>' +
					'</div>' +
					'<div id="day_slots' + i + '" i="' + i + '" class="day_slots"></div>' +
				'</div>';
			}

			str += '<div class="wpts-special-dates-box">' +
					'<div class="wpts-special-dates-title">Special / Override Dates</div>' +
					'<div class="wpts-special-add-row">' +
						'<input class="dateSpecial" type="text" value="" placeholder="Pick a date..." />' +
						'<button type="button" class="addSpecial wpts-btn-primary-xs">+ Add Date</button>' +
					'</div>' +
					'<div id="special_dates"></div>' +
				'</div>' +
			'</div>' + // .sb_working_dates
			'</div></div>'; // .wpts-fslots-section

			// SECTION 2: Booking Limits, Quantities & Pricing (collapsed by default)
			str += '<div class="wpts-fslots-section wpts-section-collapsed">' +
				'<div class="wpts-fslots-section-header wpts-fslots-section-toggle">' +
					'<span class="wpts-fslots-section-title">Booking Limits &amp; Pricing <a class="helpfbuilder" text="Configure how many time slots a customer can select, optional quantity selectors (e.g. Adults / Children), and slot pricing tiers (numbers only, without currency symbols).">?</a></span>' +
					'<span class="wpts-fslots-header-right"><span class="wpts-fslots-section-meta wpts-pricing-mode-pill">' + (this.showTotalCost ? 'Pricing On' : 'Free') + '</span><span class="wpts-fslots-chevron" aria-hidden="true">&#9662;</span></span>' +
				'</div>' +
				'<div class="wpts-fslots-section-body">' +
					'<div class="prices">' +
						'<div class="wpts-setting-row-2">' +
							'<div class="wpts-setting-group">' +
								'<label for="sMin_quantity">Min slots</label>' +
								'<select class="large" id="sMin_quantity">';
			for (var j = 1; j <= 99; j++) {
				str += '<option value="' + j + '" ' + (parseInt(this.min_quantity, 10) === j ? 'selected' : '') + '>' + j + '</option>';
			}
			str += '</select></div>' +
							'<div class="wpts-setting-group">' +
								'<label for="sMax_quantity">Max slots</label>' +
								'<select class="large" id="sMax_quantity">';
			for (var j2 = 1; j2 <= 99; j2++) {
				str += '<option value="' + j2 + '" ' + (parseInt(this.max_quantity, 10) === j2 ? 'selected' : '') + '>' + j2 + '</option>';
			}
			str += '</select></div></div>';

			// Quantities 1-5
			str += '<div class="max_quantity_scope wpts-qty-stack">' +
				'<div class="wpts-setting-group">' +
					'<label for="sQuantity1">Quantity selector #1</label>' +
					'<select class="large" id="sQuantity1">' + this.showQuantity('sQuantity1', this.quantity1) + '</select>' +
				'</div>' +
				'<div class="quantity1_scope wpts-qty-sub" style="display:' + (parseInt(this.quantity1, 10) > 1 ? 'flex' : 'none') + '">' +
					'<div class="wpts-setting-row-2">' +
						'<div class="wpts-setting-group"><label for="sQuantity1start">Start at</label><select class="large" id="sQuantity1start">' + this.showStartQuantity('sQuantity1start', this.quantity1start, this.quantity1) + '</select></div>' +
						'<div class="wpts-setting-group"><label for="sLabel1">Label #1</label><input id="sLabel1" type="text" value="' + $.fbuilder.htmlEncode(this.label1) + '" placeholder="e.g. Adults" /></div>' +
					'</div>' +
					'<div class="wpts-setting-group"><label for="sQuantity2">Quantity selector #2</label><select class="large" id="sQuantity2">' + this.showQuantity('sQuantity2', this.quantity2) + '</select></div>' +
					'<div class="quantity2_scope wpts-qty-sub" style="display:' + (parseInt(this.quantity2, 10) > 1 ? 'flex' : 'none') + '">' +
						'<div class="wpts-setting-group"><label for="sLabel2">Label #2</label><input id="sLabel2" type="text" value="' + $.fbuilder.htmlEncode(this.label2) + '" placeholder="e.g. Children" /></div>' +
						'<div class="wpts-setting-group"><label for="sQuantity3">Quantity selector #3</label><select class="large" id="sQuantity3">' + this.showQuantity('sQuantity3', this.quantity3) + '</select></div>' +
						'<div class="quantity3_scope wpts-qty-sub" style="display:' + (parseInt(this.quantity3, 10) > 1 ? 'flex' : 'none') + '">' +
							'<div class="wpts-setting-group"><label for="sLabel3">Label #3</label><input id="sLabel3" type="text" value="' + $.fbuilder.htmlEncode(this.label3) + '" placeholder="Label #3" /></div>' +
							'<div class="wpts-setting-group"><label for="sQuantity4">Quantity selector #4</label><select class="large" id="sQuantity4">' + this.showQuantity('sQuantity4', this.quantity4) + '</select></div>' +
							'<div class="quantity4_scope wpts-qty-sub" style="display:' + (parseInt(this.quantity4, 10) > 1 ? 'flex' : 'none') + '">' +
								'<div class="wpts-setting-group"><label for="sLabel4">Label #4</label><input id="sLabel4" type="text" value="' + $.fbuilder.htmlEncode(this.label4) + '" placeholder="Label #4" /></div>' +
								'<div class="wpts-setting-group"><label for="sQuantity5">Quantity selector #5</label><select class="large" id="sQuantity5">' + this.showQuantity('sQuantity5', this.quantity5) + '</select></div>' +
								'<div class="quantity5_scope wpts-qty-sub" style="display:' + (parseInt(this.quantity5, 10) > 1 ? 'flex' : 'none') + '">' +
									'<div class="wpts-setting-group"><label for="sLabel5">Label #5</label><input id="sLabel5" type="text" value="' + $.fbuilder.htmlEncode(this.label5) + '" placeholder="Label #5" /></div>' +
								'</div>' +
							'</div>' +
						'</div>' +
					'</div>' +
				'</div>' +
			'</div>';

			str += '<label class="wpts-checkbox-row wpts-checkbox-card">' +
				'<input type="checkbox" name="sShowTotalCost" id="sShowTotalCost" ' + (this.showTotalCost ? 'checked' : '') + ' />' +
				'<span>Enable pricing &amp; show total cost below slots</span>' +
			'</label>';

			str += '<div id="divTotalCostFormat" class="wpts-nested-input" style="display:' + (this.showTotalCost ? 'block' : 'none') + '">' +
				'<label for="sShowTotalCostFormat">Total cost format <a class="helpfbuilder" text="Keep {0} where the calculated price should appear. Example: $ {0} or {0} EUR">?</a></label>' +
				'<input type="text" class="large" name="sShowTotalCostFormat" id="sShowTotalCostFormat" value="' + $.fbuilder.htmlEncode(this.showTotalCostFormat) + '" />' +
			'</div>';

			str += '<div id="wpts_pricing_matrix_wrap" class="wpts-pricing-matrix-wrap" style="display:' + (this.showTotalCost ? 'flex' : 'none') + '">' +
				'<label class="wpts-checkbox-row wpts-checkbox-card">' +
					'<input type="checkbox" name="sFlatPrice" id="sFlatPrice" ' + (this.flatPrice ? 'checked' : '') + ' />' +
					'<span>Flat price per slot (quantity does not multiply price)</span>' +
				'</label>' +
				'<div id="slots_prices">' + this.showSlotsPrices() + '</div>' +
			'</div>';
			str += '</div></div></div>'; // .prices, .wpts-fslots-section-body, .wpts-fslots-section

			// SECTION 3: Calendar Display & Date Rules
			var sfirst = '';
			for (var f = 0; f < 7; f++) {
				sfirst += '<option value="' + f + '" ' + (f === parseInt(this.firstDay, 10) ? 'selected' : '') + '>' + $.fbuilder.htmlEncode(dayNames[f]) + '</option>';
			}
			var snumberOfM = '';
			for (var nm = 1; nm <= 12; nm++) {
				snumberOfM += '<option value="' + nm + '" ' + (nm === parseInt(this.numberOfMonths, 10) ? 'selected' : '') + '>' + nm + '</option>';
			}

			str += '<div class="wpts-fslots-section wpts-section-collapsed">' +
				'<div class="wpts-fslots-section-header wpts-fslots-section-toggle">' +
					'<span class="wpts-fslots-section-title">Calendar Display &amp; Date Rules</span>' +
					'<span class="wpts-fslots-header-right"><span class="wpts-fslots-section-meta wpts-time-mode-pill">' + (parseInt(this.militaryTime, 10) === 0 ? '12h AM/PM' : '24h') + '</span><span class="wpts-fslots-chevron" aria-hidden="true">&#9662;</span></span>' +
				'</div>' +
				'<div class="wpts-fslots-section-body">' +
					'<div class="wpts-setting-row-2">' +
						'<div class="wpts-setting-group"><label for="sFirstDay">Week starts on</label><select class="large" name="sFirstDay" id="sFirstDay">' + sfirst + '</select></div>' +
						'<div class="wpts-setting-group"><label for="sNumberOfMonths">Months visible</label><select class="large" name="sNumberOfMonths" id="sNumberOfMonths">' + snumberOfM + '</select></div>' +
					'</div>' +
					'<div class="wpts-setting-group">' +
						'<label for="sMilitaryTime">Time format</label>' +
						'<select class="large" name="sMilitaryTime" id="sMilitaryTime">' +
							'<option value="1" ' + (parseInt(this.militaryTime, 10) === 1 ? 'selected' : '') + '>24-hour (e.g. 14:00)</option>' +
							'<option value="0" ' + (parseInt(this.militaryTime, 10) !== 1 ? 'selected' : '') + '>12-hour AM/PM (e.g. 2:00 PM)</option>' +
						'</select>' +
					'</div>' +
					'<div class="wpts-setting-row-2">' +
						'<div class="wpts-setting-group">' +
							'<label for="sMinDate">Min date <a class="helpfbuilder" text="Leave empty for no min date.\n\nUse a fixed date matching the Date Format, a number of days from today (e.g. 0 = today, 2 = in 2 days), hours with &quot;h&quot; (e.g. 6h), or relative periods like &quot;+1m +7d&quot;.">?</a></label>' +
							'<input class="large" name="sMinDate" id="sMinDate" value="' + $.fbuilder.htmlEncode(this.minDate) + '" placeholder="e.g. 0 or 6h" />' +
						'</div>' +
						'<div class="wpts-setting-group">' +
							'<label for="sMaxDate">Max date <a class="helpfbuilder" text="Leave empty for no max date.\n\nUse a fixed date, a number of days from today (e.g. 30), or relative periods like &quot;+2m&quot;.">?</a></label>' +
							'<input class="large" name="sMaxDate" id="sMaxDate" value="' + $.fbuilder.htmlEncode(this.maxDate) + '" placeholder="e.g. +3m" />' +
						'</div>' +
					'</div>' +
					'<div class="wpts-setting-group">' +
						'<label for="sDefaultDate">Default date <a class="helpfbuilder" text="Leave empty for today.\n\nUse a fixed date, a number of days from today (e.g. 2 or -1), or relative periods like &quot;+1m +7d&quot;.">?</a></label>' +
						'<input class="large" name="sDefaultDate" id="sDefaultDate" value="' + $.fbuilder.htmlEncode(this.defaultDate) + '" placeholder="Optional initial date" />' +
					'</div>' +
					'<div class="wpts-setting-group">' +
						'<label for="sInvalidDates">Blocked / Invalid dates <a class="helpfbuilder" text="Enter dates in mm/dd/yyyy format separated by commas (e.g. 12/25/2026, 01/01/2027) or hyphenated ranges (e.g. 12/20/2026-12/28/2026).">?</a></label>' +
						'<input class="large" name="sInvalidDates" id="sInvalidDates" value="' + $.fbuilder.htmlEncode(this.invalidDates) + '" placeholder="mm/dd/yyyy, mm/dd/yyyy-mm/dd/yyyy" />' +
					'</div>' +
					'<div class="wpts-setting-group wpts-checkbox-stack">' +
						'<label class="wpts-checkbox-row"><input type="checkbox" name="sShowDropdown" id="sShowDropdown" ' + (this.showDropdown ? 'checked' : '') + ' /><span>Show month &amp; year dropdown selectors</span></label>' +
						'<div id="divdropdownRange" class="wpts-nested-input" style="display:' + (this.showDropdown ? 'block' : 'none') + '">' +
							'<label for="sDropdownRange">Year range <a class="helpfbuilder" text="Relative to current year (e.g. &quot;-10:+10&quot;) or absolute years (e.g. &quot;2025:2030&quot;).">?</a></label>' +
							'<input type="text" class="large" name="sDropdownRange" id="sDropdownRange" value="' + $.fbuilder.htmlEncode(this.dropdownRange) + '" />' +
						'</div>' +
						'<label class="wpts-checkbox-row"><input type="checkbox" name="sUsedSlotsCheckbox" id="sUsedSlotsCheckbox" ' + (this.usedSlotsCheckbox ? 'checked' : '') + ' /><span>Show already booked (used) slots</span></label>' +
					'</div>' +
				'</div>' +
			'</div>';

			return str;
		}
	}
);
