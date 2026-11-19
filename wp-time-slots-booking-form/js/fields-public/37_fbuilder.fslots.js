$.fbuilder.controls['fslots'] = function () {};
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
		cacheArr: {},
		usedSlots: {},
		dateFormat: 'mm/dd/yy',
		showDropdown: false,
		showTotalCost: false,
		showTotalCostFormat: '$ {0}',
		usedSlotsCheckbox: false,
		dropdownRange: '-10:+10',
		working_dates: [true, true, true, true, true, true, true],
		numberOfMonths: 1,
		firstDay: 0,
		minDate: '0',
		maxDate: '',
		defaultDate: '',
		invalidDates: '',
		required: true,
		formId: 0,
		extras: 0,
		size: 'medium',
		current_quantity: 1,
		invalidDatesByQuantity: {},
		pctByDay: {},
		loadOK: false,
		initialapp: '',
		initialID: 0,
		arr: {},
		arrTotal: {},
		militaryTime: 1,
		percent: [],
		notShowBookedDate: true,

		init: function () {
			// Ensure each fslots instance gets its own state containers (never shared across forms on prototype)
			this.cacheArr = {};
			this.usedSlots = {};
			this.invalidDatesByQuantity = {};
			this.pctByDay = {};
			this.arr = {};
			this.arrTotal = {};
			this.percent = [];
			this.allUsedSlots = [];
			this.tmpinvalidDatestime = [];
			this.special_days = [];
			this.loadOK = false;
			this.extras = 0;
			this.current_quantity = 1;
		},

		formattime: function (t, mt) { // mt=2 for database 09:00
			var h = Math.floor(t / 60);
			var m = t % 60;
			var suffix = '';
			if (parseInt(mt, 10) === 0) {
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
			return (((h < 10) ? ((parseInt(mt, 10) === 2) ? '0' : '') : '') + h + ':' + (m < 10 ? '0' : '') + m) + suffix;
		},

		disableSpecificDates: function (d) {
			if (!d) return false;
			var currentdate = d.getTime();
			if ($.inArray(currentdate, this.tmpinvalidDatestime) > -1) {
				return false;
			}
			var day = $.datepicker.formatDate('yy-mm-dd', d);
			if (!this.working_dates[d.getDay()] && $.inArray(day, this.special_days) === -1) {
				return false;
			}
			return true;
		},

		getAvailableSlots: function (d) {
			var me = this;
			var str = '';
			var c = 'c' + me.current_quantity;
			var dday = $.datepicker.parseDate('yy-mm-dd', d);
			var item = $.datepicker.formatDate('yy-mm-dd', dday);

			// Verify if working date or invalid date
			if (!me.working_dates[dday.getDay()] && $.inArray(item, me.special_days) === -1) {
				return str;
			}
			if ($.isArray(me.invalidDates)) {
				for (var invIdx = 0, invLen = me.invalidDates.length; invIdx < invLen; invIdx++) {
					if (dday.getTime() === me.invalidDates[invIdx].getTime()) {
						return str;
					}
				}
			}

			me.arr[d] = [];
			var day = $.datepicker.formatDate('yy-mm-dd', dday);
			var ind = -1;
			for (var spIdx = 0; ind === -1 && spIdx < me.slots_special.length; spIdx++) {
				if (me.slots_special[spIdx].type === day) {
					ind = spIdx;
				}
			}

			if (ind === -1) {
				me.slotsavail['d' + dday.getDay()] = me.slotsavail['d' + dday.getDay()] || [];
				me.arr[d] = !me.working_dates[dday.getDay()] ? [] : me.slotsavail['d' + dday.getDay()].slice();
			} else {
				me.arr[d] = me.slots_special[ind].slots.slice();
			}
			me.arrTotal[d] = me.arr[d].slice();
			for (var copyIdx = 0; copyIdx < me.arr[d].length; copyIdx++) {
				me.arr[d][copyIdx] = $.extend({}, me.arr[d][copyIdx]);
				me.arr[d][copyIdx].h1 = parseInt(me.arr[d][copyIdx].h1, 10) || 0;
				me.arr[d][copyIdx].m1 = parseInt(me.arr[d][copyIdx].m1, 10) || 0;
				me.arr[d][copyIdx].c = parseInt(me.arr[d][copyIdx].c, 10) || 0;
				me.arr[d][copyIdx].min = me.arr[d][copyIdx].h1 * 60 + me.arr[d][copyIdx].m1;
			}

			if ($.inArray(item, me.invalidDatesByQuantity[c]) === -1) {
				me.usedSlots[d] = me.usedSlots[d] || [];
				me.cacheArr[d] = me.cacheArr[d] || [];
				var used = $.merge(me.cacheArr[d].slice(), me.usedSlots[d]);
				var totalC = 0, usedC = 0;

				for (var tIdx = 0; me.arr[d] && tIdx < me.arr[d].length; tIdx++) {
					totalC += (parseInt(me.arr[d][tIdx].c, 10) || 0);
				}

				for (var uIdx = 0; uIdx < used.length; uIdx++) {
					var uMin = (parseInt(used[uIdx].h1, 10) || 0) * 60 + (parseInt(used[uIdx].m1, 10) || 0);
					var uCap = (parseInt(used[uIdx].quantity1, 10) || 0) +
						(parseInt(used[uIdx].quantity2, 10) || 0) +
						(parseInt(used[uIdx].quantity3, 10) || 0) +
						(parseInt(used[uIdx].quantity4, 10) || 0) +
						(parseInt(used[uIdx].quantity5, 10) || 0);
					used[uIdx].min = uMin;
					used[uIdx].c = uCap;
					usedC += uCap;

					for (var sIdx = 0; sIdx < me.arr[d].length; sIdx++) {
						if (
							me.arr[d][sIdx].min === uMin ||
							(typeof used[uIdx].dr !== 'undefined' && me.arr[d][sIdx].min >= uMin && me.arr[d][sIdx].min <= uMin + parseInt(used[uIdx].dr, 10))
						) {
							if (me.arr[d][sIdx].c >= uCap) {
								me.arr[d][sIdx].c -= uCap;
							} else {
								me.arr[d][sIdx].c = 0;
							}
						}
					}
				}

				me.pctByDay[day] = (totalC > 0) ? Math.min(10, Math.floor((10 * usedC) / totalC)) : 0;

				var dt = $.datepicker.parseDate('yy-mm-dd', d).getTime();
				var dmin = (me.minDate && typeof me.minDate.getMonth === 'function') ? me.minDate.getTime() : 0;
				var dmax = (me.maxDate && typeof me.maxDate.getMonth === 'function') ? me.maxDate.getTime() : 0;

				for (var j = 0; me.arr[d] && j < me.arr[d].length; j++) {
					var slotObj = me.arr[d][j];
					var slotTimeMs = dt + slotObj.min * 60 * 1000;
					if (((dmin === 0) || (slotTimeMs > dmin)) && ((dmax === 0) || (slotTimeMs < dmax))) {
						var currentSelection = '';
						for (var k = 0; k < me.usedSlots[d].length; k++) {
							if (parseInt(me.usedSlots[d][k].h1, 10) === slotObj.h1 && parseInt(me.usedSlots[d][k].m1, 10) === slotObj.m1) {
								currentSelection = 'currentSelection';
								break;
							}
						}
						var priceIdx = slotObj.p ? slotObj.p : 0;
						var formatted = me.formattime(slotObj.h1 * 60 + slotObj.m1, me.militaryTime);
						var availBadge = '<span class="ts_slot_availability"><span class="p">' + slotObj.c + '</span><span class="t">' + me.arrTotal[d][j].c + '</span></span>';

						if (slotObj.c >= me.current_quantity) {
							str += '<div class="availableslot ' + currentSelection + '">' +
								'<a href="#" role="button" aria-pressed="' + (currentSelection ? 'true' : 'false') + '" h1="' + slotObj.h1 + '" m1="' + slotObj.m1 + '" c="' + slotObj.c + '" p="' + priceIdx + '">' +
									formatted + availBadge +
								'</a>' +
							'</div>';
						} else if (slotObj.c < me.current_quantity && (me.usedSlotsCheckbox || currentSelection !== '')) {
							str += '<div class="usedslot ' + currentSelection + '">' +
								'<a href="#" role="button" aria-disabled="true" tabindex="-1" h1="' + slotObj.h1 + '" m1="' + slotObj.m1 + '" c="' + slotObj.c + '" p="' + priceIdx + '">' +
									formatted + availBadge +
								'</a>' +
							'</div>';
						}
					}
				}

				if (me.loadOK && str === '') {
					me.invalidDatesByQuantity[c] = me.invalidDatesByQuantity[c] || [];
					me.invalidDatesByQuantity[c].push(item);
				}
			}

			return str;
		},

		show: function () {
			var str1 = '<input type="hidden" class="sbquantity1" value="1" />',
				str2 = '<input type="hidden" class="sbquantity2" value="0" />',
				str3 = '<input type="hidden" class="sbquantity3" value="0" />',
				str4 = '<input type="hidden" class="sbquantity4" value="0" />',
				str5 = '<input type="hidden" class="sbquantity5" value="0" />';

			var q1 = parseInt(this.quantity1, 10) || 1;
			var q2 = parseInt(this.quantity2, 10) || 1;
			var q3 = parseInt(this.quantity3, 10) || 1;
			var q4 = parseInt(this.quantity4, 10) || 1;
			var q5 = parseInt(this.quantity5, 10) || 1;
			var q1Start = parseInt(this.quantity1start, 10) || 0;

			if (q1 > 1) {
				var o1 = '';
				for (var i = q1Start; i <= q1; i++) {
					o1 += '<option value="' + i + '" ' + (i === 1 ? 'selected' : '') + '>' + i + '</option>';
				}
				str1 = '<div id="field_q2_1" class="wpts-qty-field"><label>' + $.fbuilder.htmlEncode(this.label1) + '</label><div class="dfield"><select class="sbquantity sbquantity1 ' + $.fbuilder.htmlEncode(this.size) + '">' + o1 + '</select><span class="uh"></span></div><div class="clearer"></div></div>';
			}

			if (q1 > 1 && q2 > 1) {
				var o2 = '';
				for (var i2 = 0; i2 <= q2; i2++) {
					o2 += '<option value="' + i2 + '" ' + (i2 === 0 ? 'selected' : '') + '>' + i2 + '</option>';
				}
				str2 = '<div id="field_q2_2" class="wpts-qty-field"><label>' + $.fbuilder.htmlEncode(this.label2) + '</label><div class="dfield"><select class="sbquantity sbquantity2 ' + $.fbuilder.htmlEncode(this.size) + '">' + o2 + '</select><span class="uh"></span></div><div class="clearer"></div></div>';
			}

			if (q1 > 1 && q2 > 1 && q3 > 1) {
				var o3 = '';
				for (var i3 = 0; i3 <= q3; i3++) {
					o3 += '<option value="' + i3 + '" ' + (i3 === 0 ? 'selected' : '') + '>' + i3 + '</option>';
				}
				str3 = '<div id="field_q2_3" class="wpts-qty-field"><label>' + $.fbuilder.htmlEncode(this.label3) + '</label><div class="dfield"><select class="sbquantity sbquantity3 ' + $.fbuilder.htmlEncode(this.size) + '">' + o3 + '</select><span class="uh"></span></div><div class="clearer"></div></div>';
			}

			if (q1 > 1 && q2 > 1 && q3 > 1 && q4 > 1) {
				var o4 = '';
				for (var i4 = 0; i4 <= q4; i4++) {
					o4 += '<option value="' + i4 + '" ' + (i4 === 0 ? 'selected' : '') + '>' + i4 + '</option>';
				}
				str4 = '<div id="field_q2_4" class="wpts-qty-field"><label>' + $.fbuilder.htmlEncode(this.label4) + '</label><div class="dfield"><select class="sbquantity sbquantity4 ' + $.fbuilder.htmlEncode(this.size) + '">' + o4 + '</select><span class="uh"></span></div><div class="clearer"></div></div>';
			}

			if (q1 > 1 && q2 > 1 && q3 > 1 && q4 > 1 && q5 > 1) {
				var o5 = '';
				for (var i5 = 0; i5 <= q5; i5++) {
					o5 += '<option value="' + i5 + '" ' + (i5 === 0 ? 'selected' : '') + '>' + i5 + '</option>';
				}
				str5 = '<div id="field_q2_5" class="wpts-qty-field"><label>' + $.fbuilder.htmlEncode(this.label5) + '</label><div class="dfield"><select class="sbquantity sbquantity5 ' + $.fbuilder.htmlEncode(this.size) + '">' + o5 + '</select><span class="uh"></span></div><div class="clearer"></div></div>';
			}

			return '<div class="fields ' + $.fbuilder.htmlEncode(this.csslayout) + '" id="field' + this.form_identifier + '-' + this.index + '">' +
				str1 + str2 + str3 + str4 + str5 +
				'<label for="' + this.name + '">' + $.fbuilder.htmlEncode(this.title) + (this.required ? '<span class="r">*</span>' : '') + '</label>' +
				'<div class="dfield fslots">' +
					'<input class="field avoid_overlapping_before ' + (q1 > 1 ? 'sbquantity_min ' : '') + (this.required ? 'required ' : '') + (parseInt(this.min_quantity, 10) > 1 ? 'sbminSlots' : '') + '" id="' + this.name + '" name="' + this.name + '" type="hidden" value="" summary="usedSlots" />' +
					'<input id="' + this.name + '_services" name="' + this.name + '_services" type="hidden" value="0" />' +
					'<input id="tcost' + this.name + '" name="tcost' + this.name + '" type="hidden" value="" />' +
					'<input id="totalcost' + this.name + '" name="totalcost' + this.name + '" type="hidden" value="" />' +
					'<div class="sbfieldCalendar sbfieldCalendar' + this.name + '"></div>' +
					'<div class="slotsCalendar slotsCalendar' + this.name + '"></div>' +
					'<div class="usedSlots usedSlots' + this.name + '"></div>' +
					'<span class="uh">' + $.fbuilder.htmlEncode(this.userhelp) + '</span>' +
				'</div>' +
				'<div class="clearer"></div>' +
			'</div>';
		},

		after_show: function () {
			try { $.fn.datepicker.noConflict(); } catch (e) {}
			function closeOtherDatepicker() {
				$('#ui-datepicker-div').css('display', 'none');
			}
			setTimeout(closeOtherDatepicker, 100);

			var me = this,
				fieldSel = '#field' + me.form_identifier + '-' + me.index,
				$fieldWrap = $(fieldSel),
				e = $fieldWrap.find('.sbfieldCalendar' + me.name),
				dWrap = e.parents('.fields'),
				dd = '';

			function showInlineNotice(msg) {
				$fieldWrap.find('.wpts-pub-notice').remove();
				var $notice = $('<div class="wpts-pub-notice" role="alert"></div>').text(msg);
				$fieldWrap.find('.slotsCalendar' + me.name).after($notice);
				setTimeout(function () {
					$notice.fadeOut(250, function () { $(this).remove(); });
				}, 4000);
			}

			function renderCalendarCallback(d) {
				var day = $.datepicker.formatDate('yy-mm-dd', d);
				var c = [day, 'd' + day];
				if (!me.working_dates[d.getDay()] && $.inArray(day, me.special_days) === -1) {
					c.push('nonworking ui-datepicker-unselectable ui-state-disabled nonworkingweekday');
				}
				if ($.isArray(me.invalidDates)) {
					for (var i = 0, l = me.invalidDates.length; i < l; i++) {
						if (d.getTime() === me.invalidDates[i].getTime()) {
							c.push('nonworking ui-datepicker-unselectable ui-state-disabled invalidDate');
						}
					}
				}
				me.invalidDatesByQuantity['c' + me.current_quantity] = me.invalidDatesByQuantity['c' + me.current_quantity] || [];
				me.getAvailableSlots(day);
				if ($.inArray(day, me.invalidDatesByQuantity['c' + me.current_quantity]) > -1 && me.notShowBookedDate) {
					c.push('nonworking ui-datepicker-unselectable ui-state-disabled tmpinvalidDate nonavailableslots');
				}
				var cclass = c.join(' ');
				if (cclass.indexOf('nonworking') === -1) {
					cclass += ' ts_booked' + (typeof me.pctByDay[day] !== 'undefined' ? me.pctByDay[day] : 0);
				}
				return [true, cclass];
			}

			function onChangeDateOrQuantity(d) {
				function loadSlots(dateStr) {
					if (!me.loadOK) {
						me.formId = $fieldWrap.parents('form').find('input[type="hidden"][name$="_id"]').val();
						$.ajax({
							dataType: 'json',
							type: 'POST',
							url: document.location.href,
							cache: true,
							data: {
								cp_slots_action: 'get_slots',
								formid: me.formId,
								initialID: me.initialID,
								formfield: me.name.replace(me.form_identifier, '')
							},
							success: function (data) {
								me.cacheArr = {};
								if ($.isArray(data)) {
									for (var i = 0; i < data.length; i++) {
										var dKey = data[i].d;
										me.cacheArr[dKey] = me.cacheArr[dKey] || [];
										me.cacheArr[dKey].push(data[i]);
									}
								}
								me.cacheArr[dateStr] = me.cacheArr[dateStr] || [];
								me.loadOK = true;
								getSlots($.datepicker.formatDate('yy-mm-dd', me.getD));
							},
							error: function () {
								me.cacheArr[dateStr] = me.cacheArr[dateStr] || [];
								me.loadOK = true;
								getSlots($.datepicker.formatDate('yy-mm-dd', me.getD));
							}
						});
					} else {
						me.cacheArr[dateStr] = me.cacheArr[dateStr] || [];
						getSlots(dateStr);
					}
				}

				function getSlots(dateStr) {
					var c = 'c' + me.current_quantity;
					var nextdateAvailable = $.datepicker.parseDate('yy-mm-dd', dateStr);
					var str = me.getAvailableSlots(dateStr);
					var $s = $fieldWrap.find('.slotsCalendar' + me.name);

					if (str === '' && me.notShowBookedDate) {
						var guard = 0;
						me.invalidDatesByQuantity[c] = me.invalidDatesByQuantity[c] || [];
						while (str === '' && me.invalidDatesByQuantity[c].length < 1000 && guard < 1100) {
							guard++;
							nextdateAvailable.setDate(nextdateAvailable.getDate() + 1);
							str = me.getAvailableSlots($.datepicker.formatDate('yy-mm-dd', nextdateAvailable));
						}
						if (str !== '') {
							e.datepicker('setDate', nextdateAvailable);
							me.getD = nextdateAvailable;
							onChangeDateOrQuantity($.datepicker.formatDate('yy-mm-dd', nextdateAvailable));
							return;
						} else {
							e.datepicker('setDate', me.startdate);
							$s.html('<div class="slots wpts-slots-empty">' + $.fbuilder.htmlEncode(cp_tslotsbk_nomore_label) + '</div>');
						}
					} else {
						if (str === '') {
							$s.html('<div class="slots wpts-slots-empty" d="' + $.fbuilder.htmlEncode(dateStr) + '"><span>' + $.datepicker.formatDate(me.dateFormat, $.datepicker.parseDate('yy-mm-dd', dateStr)) + '</span><p class="wpts-no-slots-text">' + $.fbuilder.htmlEncode(cp_tslotsbk_nomore_label) + '</p></div>');
						} else {
							$s.html('<div class="slots" d="' + $.fbuilder.htmlEncode(dateStr) + '"><span>' + $.datepicker.formatDate(me.dateFormat, $.datepicker.parseDate('yy-mm-dd', dateStr)) + '</span>' + str + '</div>');
						}
					}

					var str1 = '', str2 = '';
					me.allUsedSlots = me.allUsedSlots || [];
					var j = 0;
					var total = 0, subtotal = 0;

					me.allUsedSlots.sort(function (a, b) {
						var aMin = (parseInt(a.h1, 10) || 0) * 60 + (parseInt(a.m1, 10) || 0);
						var bMin = (parseInt(b.h1, 10) || 0) * 60 + (parseInt(b.m1, 10) || 0);
						return ($.datepicker.parseDate('yy-mm-dd', a.d).getTime() + aMin * 60 * 1000) - ($.datepicker.parseDate('yy-mm-dd', b.d).getTime() + bMin * 60 * 1000);
					});

					for (var i = 0; i < me.allUsedSlots.length; i++) {
						subtotal = 0;
						var slotItem = me.allUsedSlots[i];
						var pIdx = parseInt(slotItem.p, 10) || 0;
						if (pIdx > me.prices.length - 1 || pIdx < 0) {
							pIdx = 0;
							slotItem.p = 0;
						}
						try {
							if (me.flatPrice) {
								subtotal += parseFloat(me.prices[pIdx].price1[i]) || 0;
							} else {
								subtotal += (parseFloat(me.prices[pIdx].price1[i]) || 0) * (parseInt(slotItem.quantity1, 10) || 0);
								subtotal += (parseFloat(me.prices[pIdx].price2[i]) || 0) * (parseInt(slotItem.quantity2, 10) || 0);
								subtotal += (parseFloat(me.prices[pIdx].price3[i]) || 0) * (parseInt(slotItem.quantity3, 10) || 0);
								subtotal += (parseFloat(me.prices[pIdx].price4[i]) || 0) * (parseInt(slotItem.quantity4, 10) || 0);
								subtotal += (parseFloat(me.prices[pIdx].price5[i]) || 0) * (parseInt(slotItem.quantity5, 10) || 0);
							}
						} catch (err) {}

						total += subtotal;
						var slotMins = (parseInt(slotItem.h1, 10) || 0) * 60 + (parseInt(slotItem.m1, 10) || 0);
						str1 += '<div class="sb_item" d="' + $.fbuilder.htmlEncode(slotItem.d) + '" h="' + slotItem.h1 + '" m="' + slotItem.m1 + '">' +
							'<span class="sb_item_time">' + $.datepicker.formatDate(me.dateFormat, $.datepicker.parseDate('yy-mm-dd', slotItem.d)) + ' ' + me.formattime(slotMins, me.militaryTime) + '</span> ' +
							'<a href="#" class="cancel" role="button" i="' + j + '" iall="' + i + '">[' + $.fbuilder.htmlEncode(cp_tslotsbk_cancel_label) + ']</a>' +
						'</div>';

						str2 += (str2 === '' ? '' : ';') +
							slotItem.d + ' ' +
							me.formattime(slotMins, 2) + ' ' +
							slotItem.quantity1 + ' ' +
							slotItem.quantity2 + ' ' +
							slotItem.quantity3 + ' ' +
							slotItem.quantity4 + ' ' +
							slotItem.quantity5 + ' ' +
							subtotal + ' ' +
							pIdx;

						if (slotItem.d === dateStr) j++;
					}

					me.getExtrasVisible($fieldWrap.parents('form'));
					var totalCost = me.getTotalCost(me.extras + total);

					if (str1 !== '') {
						var strq = '';
						if (me.quantity1 > 1) {
							strq += '<div><label>' + $.fbuilder.htmlEncode(me.label1) + '</label><span>' + me.allUsedSlots[0].quantity1 + '</span></div>';
						}
						if (me.quantity1 > 1 && me.quantity2 > 1) {
							strq += '<div><label>' + $.fbuilder.htmlEncode(me.label2) + '</label><span>' + me.allUsedSlots[0].quantity2 + '</span></div>';
						}
						if (me.quantity1 > 1 && me.quantity2 > 1 && me.quantity3 > 1) {
							strq += '<div><label>' + $.fbuilder.htmlEncode(me.label3) + '</label><span>' + me.allUsedSlots[0].quantity3 + '</span></div>';
						}
						if (me.quantity1 > 1 && me.quantity2 > 1 && me.quantity3 > 1 && me.quantity4 > 1) {
							strq += '<div><label>' + $.fbuilder.htmlEncode(me.label4) + '</label><span>' + me.allUsedSlots[0].quantity4 + '</span></div>';
						}
						if (me.quantity1 > 1 && me.quantity2 > 1 && me.quantity3 > 1 && me.quantity4 > 1 && me.quantity5 > 1) {
							strq += '<div><label>' + $.fbuilder.htmlEncode(me.label5) + '</label><span>' + me.allUsedSlots[0].quantity5 + '</span></div>';
						}
						str1 = '<div class="quantities">' + strq + '</div>' + str1;

						if (me.showTotalCost) {
							str1 += '<div class="totalCost"><span>' + $.fbuilder.htmlEncode(cp_tslotsbk_cost_label) + '</span><span class="n"> ' + $.fbuilder.htmlEncode(me.showTotalCostFormat.replace('{0}', totalCost)) + '</span></div>';
						}
					}

					$fieldWrap.find('.usedSlots' + me.name).html(str1);
					$fieldWrap.find('#' + me.name).val(str2).trigger('change');
					$fieldWrap.find('#tcost' + me.name).val(total);
					$fieldWrap.find('#totalcost' + me.name).val(totalCost);

					// Click handler for selecting a time slot
					$fieldWrap.find('.slots a').off('click').on('click', function (evt) {
						evt.preventDefault();
						if ($(this).parents('.cp_csseditor').length > 0) return;
						if ($(this).parents('fieldset').hasClass('ahbgutenberg_editor')) return false;
						if ($(this).parent().hasClass('usedslot')) return false;
						if ($(this).parent().hasClass('currentSelection')) return false;

						$fieldWrap.find('div.cpefb_error').remove();
						var $hidden = $fieldWrap.find('#' + me.name);
						var cRequired = false;
						if ($hidden.hasClass('required')) {
							$hidden.removeClass('required');
							cRequired = true;
						}
						if (parseInt(me.min_quantity, 10) > 1) $hidden.removeClass('sbminSlots');

						var isValidField = true;
						try {
							isValidField = $fieldWrap.parents('form').validate().element('#' + me.name);
						} catch (err) {}

						if (isValidField) {
							var maxQty = parseInt(me.max_quantity, 10) || 0;
							if (maxQty === 1 && me.allUsedSlots.length === 1) {
								me.usedSlots[me.allUsedSlots[0].d] = [];
								me.allUsedSlots = [];
								me.invalidDatesByQuantity['c' + me.current_quantity] = [];
							}
							me.allUsedSlots = me.allUsedSlots || [];
							if (maxQty === 0 || me.allUsedSlots.length < maxQty) {
								var pickedDate = $(this).parents('.slots').attr('d');
								var $fieldsEl = $(this).parents('.fields');
								me.usedSlots[pickedDate] = me.usedSlots[pickedDate] || [];
								var obj = {
									h1: parseInt($(this).attr('h1'), 10) || 0,
									m1: parseInt($(this).attr('m1'), 10) || 0,
									quantity1: parseInt($fieldsEl.find('.sbquantity1').val(), 10) || 0,
									quantity2: parseInt($fieldsEl.find('.sbquantity2').val(), 10) || 0,
									quantity3: parseInt($fieldsEl.find('.sbquantity3').val(), 10) || 0,
									quantity4: parseInt($fieldsEl.find('.sbquantity4').val(), 10) || 0,
									quantity5: parseInt($fieldsEl.find('.sbquantity5').val(), 10) || 0,
									d: pickedDate,
									p: parseInt($(this).attr('p'), 10) || 0
								};
								me.usedSlots[pickedDate].push(obj);
								me.allUsedSlots.push(obj);
								onChangeDateOrQuantity($.datepicker.formatDate('yy-mm-dd', me.getD));
							} else {
								var maxMsg = ($.validator && $.validator.messages && $.validator.messages.maxapp)
									? $.validator.messages.maxapp.replace('{0}', maxQty)
									: ('Maximum ' + maxQty + ' slots allowed.');
								showInlineNotice(maxMsg);
							}
						}

						if (cRequired) $hidden.addClass('required');
						if (parseInt(me.min_quantity, 10) > 1) $hidden.addClass('sbminSlots');
						return false;
					});

					// Click handler for removing a selected time slot
					$fieldWrap.find('.usedSlots' + me.name + ' a.cancel').off('click').on('click', function (evt) {
						evt.preventDefault();
						if ($(this).parents('.cp_csseditor').length > 0) return;
						var $item = $(this).closest('.sb_item');
						var remDate = $item.attr('d');
						var remH = parseInt($item.attr('h'), 10);
						var remM = parseInt($item.attr('m'), 10);

						me.usedSlots[remDate] = me.usedSlots[remDate] || [];
						for (var u = 0; u < me.usedSlots[remDate].length; u++) {
							if (me.usedSlots[remDate][u].d === remDate && parseInt(me.usedSlots[remDate][u].h1, 10) === remH && parseInt(me.usedSlots[remDate][u].m1, 10) === remM) {
								me.usedSlots[remDate].splice(u, 1);
								break;
							}
						}
						for (var a = 0; a < me.allUsedSlots.length; a++) {
							if (me.allUsedSlots[a].d === remDate && parseInt(me.allUsedSlots[a].h1, 10) === remH && parseInt(me.allUsedSlots[a].m1, 10) === remM) {
								me.allUsedSlots.splice(a, 1);
								break;
							}
						}

						var currentday = me.getD;
						e.datepicker('setDate', me.startdate);
						me.invalidDatesByQuantity['c' + me.current_quantity] = [];
						onChangeDateOrQuantity($.datepicker.formatDate('yy-mm-dd', me.startdate));
						e.datepicker('setDate', currentday);
						me.getD = currentday;
						onChangeDateOrQuantity($.datepicker.formatDate('yy-mm-dd', me.getD));
						return false;
					});
				}

				loadSlots(d);
				$(document).trigger('wpts_afterOnChange', { name: me.name, me: me });
			}

			e.addClass('notranslate');

			// Ensure price arrays 3..5 exist
			for (var prI = 0; prI < me.prices.length; prI++) {
				me.prices[prI].price1 = me.prices[prI].price1 || [];
				me.prices[prI].price2 = me.prices[prI].price2 || [];
				me.prices[prI].price3 = me.prices[prI].price3 || [];
				me.prices[prI].price4 = me.prices[prI].price4 || [];
				me.prices[prI].price5 = me.prices[prI].price5 || [];
				for (var prJ = 0; prJ < me.prices[prI].price1.length; prJ++) {
					if (me.prices[prI].price3.length <= prJ) me.prices[prI].price3[prJ] = 0;
					if (me.prices[prI].price4.length <= prJ) me.prices[prI].price4[prJ] = 0;
					if (me.prices[prI].price5.length <= prJ) me.prices[prI].price5[prJ] = 0;
				}
			}

			if (me.initialapp !== '') {
				me.allUsedSlots = me.allUsedSlots || [];
				var initParts = me.initialapp.split(';');
				var lastQ1 = 1, lastQ2 = 0, lastQ3 = 0, lastQ4 = 0, lastQ5 = 0;
				for (var initI = 0; initI < initParts.length; initI++) {
					if (initParts[initI] !== '') {
						var s2 = initParts[initI].split(' ');
						var tt = s2[1].split(':');
						lastQ1 = parseInt(s2[2], 10) || 0;
						lastQ2 = parseInt(s2[3], 10) || 0;
						lastQ3 = parseInt(s2[4], 10) || 0;
						lastQ4 = parseInt(s2[5], 10) || 0;
						lastQ5 = parseInt(s2[6], 10) || 0;
						dd = s2[0];
						var initP = (s2.length > 8) ? (parseInt(s2[8], 10) || 0) : (parseInt(s2[7], 10) || 0);
						me.usedSlots[dd] = me.usedSlots[dd] || [];
						var initObj = {
							h1: parseInt(tt[0], 10) || 0,
							m1: parseInt(tt[1], 10) || 0,
							quantity1: lastQ1,
							quantity2: lastQ2,
							quantity3: lastQ3,
							quantity4: lastQ4,
							quantity5: lastQ5,
							d: dd,
							p: initP
						};
						me.usedSlots[dd].push(initObj);
						me.allUsedSlots.push(initObj);
					}
					dWrap.find('.sbquantity1').val(lastQ1);
					dWrap.find('.sbquantity2').val(lastQ2);
					dWrap.find('.sbquantity3').val(lastQ3);
					dWrap.find('.sbquantity4').val(lastQ4);
					dWrap.find('.sbquantity5').val(lastQ5);
				}
			}

			if (typeof me.slotsavail === 'undefined') {
				me.slotsavail = $.extend(true, {}, me.slotsavailInit);
			}

			if (typeof wptimesbk_in_admin !== 'undefined') {
				me.minDate = '';
				me.maxDate = '';
			}

			var mindatetime = '';
			var maxdatetime = '';
			var minDateStr = (me.minDate !== null && typeof me.minDate !== 'undefined') ? String(me.minDate) : '';
			var maxDateStr = (me.maxDate !== null && typeof me.maxDate !== 'undefined') ? String(me.maxDate) : '';

			if (minDateStr.indexOf('@') !== -1) {
				var minParts = minDateStr.split('@');
				minDateStr = minParts[0];
				mindatetime = minParts[1];
			}
			me.minDate = minDateStr;
			if (minDateStr.length < 6 && minDateStr.toLowerCase().indexOf('h') !== -1) {
				var minH = parseFloat(minDateStr.toLowerCase().replace(/h/gi, '')) || 0;
				me.minDate = new Date((new Date()).getTime() + minH * 60 * 60 * 1000);
			} else if (minDateStr !== '' && minDateStr == parseInt(minDateStr, 10)) {
				me.minDate = new Date((new Date()).getTime() + parseInt(minDateStr, 10) * 24 * 60 * 60 * 1000);
			}

			if (maxDateStr.indexOf('@') !== -1) {
				var maxParts = maxDateStr.split('@');
				maxDateStr = maxParts[0];
				maxdatetime = maxParts[1];
			}
			me.maxDate = maxDateStr;
			if (maxDateStr.length < 6 && maxDateStr.toLowerCase().indexOf('h') !== -1) {
				var maxH = parseFloat(maxDateStr.toLowerCase().replace(/h/gi, '')) || 0;
				me.maxDate = new Date((new Date()).getTime() + maxH * 60 * 60 * 1000);
			} else if (maxDateStr !== '' && maxDateStr == parseInt(maxDateStr, 10)) {
				me.maxDate = new Date((new Date()).getTime() + parseInt(maxDateStr, 10) * 24 * 60 * 60 * 1000);
			}

			var rawInvalid = String(this.invalidDates || '').replace(/\s+/g, '');
			if (!/^\s*$/.test(rawInvalid)) {
				var dateRegExp = /^\d{1,2}\/\d{1,2}\/\d{4}$/,
					counter = 0,
					dates = rawInvalid.split(',');
				this.invalidDates = [];
				for (var invI = 0, invH = dates.length; invI < invH; invI++) {
					var range = dates[invI].split('-');
					if (range.length === 2 && dateRegExp.test(range[0]) && dateRegExp.test(range[1])) {
						var fromD = new Date(range[0]),
							toD = new Date(range[1]),
							rangeGuard = 0;
						while (fromD <= toD && rangeGuard < 1000) {
							this.invalidDates[counter++] = fromD;
							var tmp = new Date(fromD.valueOf());
							tmp.setDate(tmp.getDate() + 1);
							fromD = tmp;
							rangeGuard++;
						}
					} else {
						for (var invJ = 0, invK = range.length; invJ < invK; invJ++) {
							if (dateRegExp.test(range[invJ])) {
								this.invalidDates[counter++] = new Date(range[invJ]);
							}
						}
					}
				}
			} else {
				this.invalidDates = [];
			}

			if ($.validator && $.validator.messages && $.validator.messages.date_format && $.validator.messages.date_format !== '') {
				me.dateFormat = $.validator.messages.date_format;
			}

			dWrap.find('.sbquantity1,.sbquantity2,.sbquantity3,.sbquantity4,.sbquantity5').on('change', function () {
				me.allUsedSlots = [];
				me.usedSlots = {};
				me.current_quantity =
					(parseInt($fieldWrap.find('.sbquantity1').val(), 10) || 0) +
					(parseInt($fieldWrap.find('.sbquantity2').val(), 10) || 0) +
					(parseInt($fieldWrap.find('.sbquantity3').val(), 10) || 0) +
					(parseInt($fieldWrap.find('.sbquantity4').val(), 10) || 0) +
					(parseInt($fieldWrap.find('.sbquantity5').val(), 10) || 0);
				e.datepicker('setDate', me.startdate);
				me.getD = me.startdate;
				onChangeDateOrQuantity($.datepicker.formatDate('yy-mm-dd', me.startdate));
			});

			$('#' + me.name).on('change', function () {
				if ($(this).attr('reload') === 'reload') {
					$(this).attr('reload', '');
					onChangeDateOrQuantity($.datepicker.formatDate('yy-mm-dd', me.getD));
					e.datepicker('option', 'beforeShowDay', renderCalendarCallback);
				}
			});

			me.special_days = [];
			for (var sp = 0; sp < me.slots_special.length; sp++) {
				me.special_days.push(me.slots_special[sp].type);
			}

			e.datepicker({
				numberOfMonths: parseInt(me.numberOfMonths, 10) || 1,
				dateFormat: me.dateFormat,
				defaultDate: me.defaultDate,
				changeMonth: !!me.showDropdown,
				changeYear: !!me.showDropdown,
				yearRange: me.showDropdown ? me.dropdownRange : '',
				onSelect: function () {
					me.getD = e.datepicker('getDate');
					onChangeDateOrQuantity($.datepicker.formatDate('yy-mm-dd', me.getD));
					$fieldWrap.find('div.cpefb_error').remove();
				},
				beforeShowDay: renderCalendarCallback
			});

			if ($.validator && $.validator.messages && $.datepicker.regional && $.datepicker.regional[$.validator.messages.language]) {
				e.datepicker('option', $.datepicker.regional[$.validator.messages.language]);
				$.datepicker.setDefaults($.datepicker.regional[$.validator.messages.language]);
			}
			e.datepicker('option', 'firstDay', parseInt(me.firstDay, 10) || 0);
			e.datepicker('option', 'dateFormat', me.dateFormat);
			e.datepicker('option', 'minDate', me.minDate);
			e.datepicker('option', 'maxDate', me.maxDate);

			if (me.minDate !== '' && typeof me.minDate.getMonth !== 'function') {
				try {
					me.minDate = $.datepicker.parseDate(me.dateFormat, e.datepicker('option', 'minDate'));
				} catch (err) {}
			}
			if (mindatetime !== '' && me.minDate && typeof me.minDate.getFullYear === 'function') {
				var minTA = mindatetime.split(':');
				if (parseInt(minTA[0], 10) >= 0 && parseInt(minTA[0], 10) < 24 && parseInt(minTA[1], 10) >= 0 && parseInt(minTA[1], 10) < 60) {
					me.minDate = new Date(me.minDate.getFullYear(), me.minDate.getMonth(), me.minDate.getDate(), parseInt(minTA[0], 10), parseInt(minTA[1], 10));
				}
			}
			if (me.maxDate !== '' && typeof me.maxDate.getMonth !== 'function') {
				try {
					me.maxDate = $.datepicker.parseDate(me.dateFormat, e.datepicker('option', 'maxDate'));
					me.maxDate = new Date(me.maxDate.getTime() + 23 * 60 * 60 * 1000 + 59 * 60 * 1000);
				} catch (err) {}
			}
			if (maxdatetime !== '' && me.maxDate && typeof me.maxDate.getFullYear === 'function') {
				var maxTA = maxdatetime.split(':');
				if (parseInt(maxTA[0], 10) >= 0 && parseInt(maxTA[0], 10) < 24 && parseInt(maxTA[1], 10) >= 0 && parseInt(maxTA[1], 10) < 60) {
					me.maxDate = new Date(me.maxDate.getFullYear(), me.maxDate.getMonth(), me.maxDate.getDate(), parseInt(maxTA[0], 10), parseInt(maxTA[1], 10));
				}
			}

			me.tmpinvalidDatestime = [];
			if ($.isArray(me.invalidDates)) {
				for (var ti = 0; ti < me.invalidDates.length; ti++) {
					me.tmpinvalidDatestime[ti] = me.invalidDates[ti].getTime();
				}
			}

			var sum = 0;
			for (var wi = 0; wi < 7; wi++) {
				sum += (me.working_dates[wi] ? 1 : 0);
			}
			var max_slots_special = 0;
			if (sum > 0 || me.slots_special.length > 0) {
				if (sum === 0) {
					for (var msp = 0; msp < me.slots_special.length; msp++) {
						var spTime = $.datepicker.parseDate('yy-mm-dd', me.slots_special[msp].type).getTime();
						if (spTime > max_slots_special) max_slots_special = spTime;
					}
				}
				var nextdateAvailable = e.datepicker('getDate') || new Date();
				var safety = 0;
				while (!me.disableSpecificDates(nextdateAvailable) && (sum > 0 || nextdateAvailable.getTime() <= max_slots_special) && safety < 730) {
					nextdateAvailable.setDate(nextdateAvailable.getDate() + 1);
					safety++;
				}
				if (sum === 0 && nextdateAvailable.getTime() > max_slots_special) {
					e.datepicker('setDate', me.startdate);
					$fieldWrap.find('.slotsCalendar' + me.name).html('<div class="slots wpts-slots-empty">' + $.fbuilder.htmlEncode(cp_tslotsbk_nomore_label) + '</div>');
				} else {
					e.datepicker('setDate', nextdateAvailable);
					me.getD = new Date(nextdateAvailable.getTime());
					me.startdate = new Date(nextdateAvailable.getTime());
					onChangeDateOrQuantity($.datepicker.formatDate('yy-mm-dd', me.getD));
					(function ifLoadOk() {
						if (!me.loadOK) {
							setTimeout(ifLoadOk, 100);
						} else {
							e.datepicker('option', 'beforeShowDay', renderCalendarCallback);
							onChangeDateOrQuantity($.datepicker.formatDate('yy-mm-dd', me.getD));
							e.datepicker('option', 'beforeShowDay', renderCalendarCallback);
						}
					})();
				}
			}

			if (me.initialapp !== '' && dd !== '') {
				me.getD = $.datepicker.parseDate('yy-mm-dd', dd);
				e.datepicker('setDate', me.getD);
				onChangeDateOrQuantity(dd);
			}

			$fieldWrap.parents('form')
				.find('.tsb_service,.tsb_service_percent,.tsb_service_per_slot,.tsb_service_per_quantity_selection')
				.on('click change', function () {
					me.getExtrasVisible($(this).parents('form'));
				});

			$fieldWrap.parents('form').on('submit', function () {
				me.getExtrasVisible($(this));
			});

			if (typeof cp_tslotsbk_overlapping_label !== 'undefined') {
				$.extend($.validator.messages, { avoid_overlapping: $.validator.format(cp_tslotsbk_overlapping_label) });
			}

			if (!('avoid_overlapping' in $.validator.methods)) {
				var resolveSlotControl = function (element) {
					var p = element.id.split('_'),
						_index = (p.length > 1) ? '_' + p[p.length - 1] : '';
					if (typeof $.fbuilder['forms'] !== 'undefined' && typeof $.fbuilder['forms'][_index] !== 'undefined') {
						return $.fbuilder['forms'][_index].getItem(element.id);
					}
					return null;
				};

				function avoid_over_function(value, element) {
					var validator = this,
						previous = validator.previousValue(element);
					if (previous.old === value) {
						return previous.valid;
					}
					previous.old = value;
					validator.startRequest(element);

					var ctrl = resolveSlotControl(element);
					if (ctrl) {
						$.ajax({
							dataType: 'json',
							type: 'POST',
							url: document.location.href,
							data: {
								cp_slots_action: 'get_slots',
								formid: ctrl.formId,
								initialID: ctrl.initialID,
								formfield: ctrl.name.replace(ctrl.form_identifier, '')
							},
							success: function (data) {
								var overlapping = false;
								ctrl.ignoreUsedSlots = true;
								ctrl.cacheArr = {};
								if ($.isArray(data)) {
									for (var i = 0; i < data.length; i++) {
										var dKey = data[i].d;
										ctrl.cacheArr[dKey] = ctrl.cacheArr[dKey] || [];
										ctrl.cacheArr[dKey].push(data[i]);
									}
								}
								ctrl.loadOK = true;

								var arrAllDays = [];
								ctrl.allUsedSlots = ctrl.allUsedSlots || [];
								for (var a = 0; a < ctrl.allUsedSlots.length; a++) {
									if ($.inArray(ctrl.allUsedSlots[a].d, arrAllDays) === -1) {
										arrAllDays.push(ctrl.allUsedSlots[a].d);
									}
								}

								for (var k = 0; k < arrAllDays.length && !overlapping; k++) {
									var dayKey = arrAllDays[k];
									ctrl.cacheArr[dayKey] = ctrl.cacheArr[dayKey] || [];
									ctrl.usedSlots[dayKey] = ctrl.usedSlots[dayKey] || [];
									var used = $.merge(ctrl.cacheArr[dayKey].slice(), ctrl.usedSlots[dayKey]);
									ctrl.arrTotal[dayKey] = ctrl.arrTotal[dayKey] || [];

									for (var j = 0; j < ctrl.arrTotal[dayKey].length; j++) {
										ctrl.arrTotal[dayKey][j].available = parseInt(ctrl.arrTotal[dayKey][j].c, 10) || 0;
									}

									for (var u = 0; u < used.length && !overlapping; u++) {
										var uMin = (parseInt(used[u].h1, 10) || 0) * 60 + (parseInt(used[u].m1, 10) || 0);
										var uCap = (parseInt(used[u].quantity1, 10) || 0) +
											(parseInt(used[u].quantity2, 10) || 0) +
											(parseInt(used[u].quantity3, 10) || 0) +
											(parseInt(used[u].quantity4, 10) || 0) +
											(parseInt(used[u].quantity5, 10) || 0);
										used[u].min = uMin;
										used[u].c = uCap;

										for (var s = 0; s < ctrl.arrTotal[dayKey].length && !overlapping; s++) {
											if (parseInt(ctrl.arrTotal[dayKey][s].min, 10) === uMin) {
												ctrl.arrTotal[dayKey][s].available -= uCap;
												if (ctrl.arrTotal[dayKey][s].available < 0) {
													for (var x = 0; x < ctrl.usedSlots[dayKey].length && !overlapping; x++) {
														var selMin = (parseInt(ctrl.usedSlots[dayKey][x].h1, 10) || 0) * 60 + (parseInt(ctrl.usedSlots[dayKey][x].m1, 10) || 0);
														if (parseInt(ctrl.arrTotal[dayKey][s].min, 10) === selMin) {
															overlapping = true;
														}
													}
												}
											}
										}
									}
								}

								var isValid = !overlapping;
								if (isValid) {
									var submitted = validator.formSubmitted;
									validator.prepareElement(element);
									validator.formSubmitted = submitted;
									validator.successList.push(element);
									delete validator.invalid[element.name];
									validator.showErrors();
								} else {
									for (var r = 0; r < arrAllDays.length; r++) {
										var rDay = arrAllDays[r];
										for (var rj = 0; ctrl.arrTotal[rDay] && rj < ctrl.arrTotal[rDay].length; rj++) {
											ctrl.arrTotal[rDay][rj].available = parseInt(ctrl.arrTotal[rDay][rj].c, 10) || 0;
										}
									}
									ctrl.loadOK = true;
									var errors = {};
									errors[element.name] = validator.defaultMessage(element, 'avoid_overlapping');
									validator.invalid[element.name] = true;
									validator.showErrors(errors);
									element.focus();
								}
								previous.valid = isValid;
								validator.stopRequest(element, isValid);
								if (typeof cp_tslotsbk_avoid_overlapping !== 'undefined') {
									cp_tslotsbk_avoid_overlapping = Math.max(0, cp_tslotsbk_avoid_overlapping - 1);
								}
							},
							error: function () {
								previous.valid = true;
								validator.stopRequest(element, true);
								if (typeof cp_tslotsbk_avoid_overlapping !== 'undefined') {
									cp_tslotsbk_avoid_overlapping = Math.max(0, cp_tslotsbk_avoid_overlapping - 1);
								}
							}
						});
						return 'pending';
					}
					return true;
				}

				$.validator.addMethod('avoid_overlapping', avoid_over_function);

				$.validator.addMethod('sbquantity_min', function (value, element) {
					var $form = $(element).parents('form');
					var totalQty = 0;
					$form.find('.sbquantity').each(function () {
						totalQty += (parseInt($(this).val(), 10) || 0);
					});
					return totalQty > 0;
				}, cp_tslotsbk_pselect_quant);

				$.validator.addMethod('sbminSlots', function (value, element) {
					var ctrl = resolveSlotControl(element);
					if (!ctrl) return true;
					return (ctrl.allUsedSlots ? ctrl.allUsedSlots.length : 0) >= (parseInt(ctrl.min_quantity, 10) || 1);
				}, function (params, element) {
					var ctrl = resolveSlotControl(element);
					var minQ = ctrl ? (parseInt(ctrl.min_quantity, 10) || 1) : 1;
					return $.validator.messages.minapp.replace('{0}', minQ);
				});
			}
		},

		getExtrasVisible: function (f) {
			try {
				var p = f.attr('id').split('_');
				var items = $.fbuilder['forms']['_' + p[p.length - 1]].getItems();
				for (var i = 0; i < items.length; i++) {
					if (items[i].ftype === 'fslots' && ($('#' + items[i].name).parent().is(':visible') || $('#' + items[i].name).parents('.fields').hasClass('cp_active'))) {
						this.getExtras(items[i], f);
					}
				}
			} catch (e) {}
		},

		getTotalCost: function (t) {
			var totalCost = Number((Number(t) || 0).toFixed(2));
			var pctArr = this.percent || [];
			for (var i = 0; i < pctArr.length; i++) {
				totalCost += Number(((Number(t) || 0) * (pctArr[i] / 100)).toFixed(2));
			}
			return totalCost.toFixed(2);
		},

		getExtras: function (ctrl, f) {
			var v = 0;
			var isVisibleOption = function ($el) {
				return $el.parents('.fields').hasClass('cp_active') ||
					$el.is(':visible') ||
					($el.prop('tagName') === 'OPTION' && $el.parent().is(':visible'));
			};

			f.find('.tsb_service').find(':checked:not(.ignore)').each(function () {
				if (isVisibleOption($(this)) && $.isNumeric(this.value)) {
					v += parseFloat(this.value);
				}
			});

			ctrl.percent = [];
			f.find('.tsb_service_percent').find(':checked:not(.ignore)').each(function () {
				if (isVisibleOption($(this)) && $.isNumeric(this.value)) {
					ctrl.percent.push(parseFloat(this.value));
				}
			});

			ctrl.allUsedSlots = ctrl.allUsedSlots || [];
			var s = ctrl.allUsedSlots.length;
			f.find('.tsb_service_per_slot').find(':checked:not(.ignore)').each(function () {
				if (isVisibleOption($(this)) && $.isNumeric(this.value)) {
					v += parseFloat(this.value) * s;
				}
			});

			var q = (parseInt(f.find('.sbquantity1').val(), 10) || 0) +
				(parseInt(f.find('.sbquantity2').val(), 10) || 0) +
				(parseInt(f.find('.sbquantity3').val(), 10) || 0) +
				(parseInt(f.find('.sbquantity4').val(), 10) || 0) +
				(parseInt(f.find('.sbquantity5').val(), 10) || 0);
			f.find('.tsb_service_per_quantity_selection').find(':checked:not(.ignore)').each(function () {
				if (isVisibleOption($(this)) && $.isNumeric(this.value)) {
					v += parseFloat(this.value) * q;
				}
			});

			ctrl.extras = v;
			var $wrap = $('#field' + ctrl.form_identifier + '-' + ctrl.index);
			var total = parseFloat($wrap.find('#tcost' + ctrl.name).val()) || 0;
			var totalCost = ctrl.getTotalCost(ctrl.extras + total);
			$wrap.find('#totalcost' + ctrl.name).val(totalCost);
			$wrap.find('.totalCost .n').text(' ' + ctrl.showTotalCostFormat.replace('{0}', totalCost));
			$wrap.find('#' + ctrl.name).trigger('change');
		},

		val: function () {
			return 0;
		}
	}
);
