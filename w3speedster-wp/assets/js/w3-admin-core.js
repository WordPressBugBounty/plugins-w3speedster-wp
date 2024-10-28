jQuery(document).ready(function () {
	jQuery('#create_critical_css').click(function () {
		jQuery(this).prop('disabled', true);
		jQuery('.critical-css-bar').show();
		create_critical_css();
	});
	jQuery('#load-critical-css').on('change', function () {
		if (jQuery(this).is(':checked')) {
			jQuery('.critical-in-style').show();
		} else {
			jQuery('.critical-in-style').hide();
		}
	});
	jQuery('.activate-key').click(function () {
		var key = jQuery("[name='license_key']");
		if (key.val() == '') {
			alert("Please enter key");
			return false;
		}
		jQuery(this).prop('disabled', true);
		activateLicenseKey(key);

	});
	function activateLicenseKey(key) {

		jQuery.ajax({
			url: adminUrl,
			data: {
				'action': 'w3speedsterActivateLicenseKey',
				'key': key.val()
			},
			success: function (data) {
				// This outputs the result of the ajax request
				data = jQuery.parseJSON(data);
				if (data[1] == 'verified') {
					jQuery('[name="is_activated"]').val(data[2]);
					key.closest('form').submit();
				} else {
					alert("Invalid key");
				}
				jQuery('.activate-key').prop('disabled', false);
				console.log(data[1]);
				console.log(data);
			},
			error: function (errorThrown) {
				console.log(errorThrown);
			}
		});
	}

	jQuery('.basic-set-checkbox').on('change', function () {
		var childElement = '.' + jQuery(this).attr('data-class');

		if (jQuery(this).is(':checked')) {
			jQuery(childElement).each(function () {
				if (!jQuery(this).is(':checked')) {
					jQuery(this).prop('checked', true);

				}
			});

			if (childElement == '.opt-js') {
				jQuery('.opt-js-select').val('after_page_load');
			}
			if (childElement == '.main-opt-img') {
				jQuery('.start_image_optimization').click();
			}
			if (childElement == '.opt-css') {
				jQuery('#create_critical_css').click();
			}
		} else {

			jQuery(childElement).each(function () {
				if (jQuery(this).is(':checked')) {
					jQuery(this).prop('checked', false);

				}
			});
			if (childElement == '.opt-js') {
				jQuery('.opt-js-select').val('on_page_load');
			}
		}

		jQuery('.main-form').submit();
	});
});
function IsJsonString(str) {
	try {
		JSON.parse(str);
	} catch (e) {
		return false;
	}
	return true;
}
function create_critical_css() {
	//jQuery('.preload_error_css').html('');
	jQuery('.critical-progress-bar').addClass('progress-bar-animated progress-bar-striped');
	jQuery.ajax({
		url: adminUrl,
		data: {
			'action': 'w3speedster_preload_css',
			'page': 'admin'
		},
		success: function (data) {
			data = jQuery.parseJSON(data);
			console.log(data);
			if (data[0] == 'success' || (data[0] == 'error' && (data[1] == 'process-already-running' || data[1].indexOf('no stylesheets found') > -1))) {
				var textArea = jQuery('textarea.preload_error_css');
				if (textArea.attr('data-running') != data[4]) {
					textArea.attr('data-running', data[4]);
					jQuery('textarea.preload_error_css').html('Critical CSS is currently running for ' + data[4]);
				}

				var timeOut = 10000;
				jQuery('.preload_total_css').html(data[2]);
				jQuery('.preload_created_css').html(data[3]);
				percent = data[2] > 0 && data[3] > 0 ? parseFloat(data[3]) / parseFloat(data[2]) * 100 : 1;
				jQuery('.critical-progress-bar').css('width', percent.toFixed(1) + '%');
				jQuery('.progress-percent').html(percent.toFixed(1) + '%');
				if (data[2] > data[3] || data[3] == 0) {
					console.log("next scheduled");
					setTimeout(create_critical_css, timeOut);
				} else {
					setTimeout(create_critical_css, 20000);
					jQuery('.critical-css-bar').hide();
					jQuery('.preload_error_css').html('');
				}
			} else {
				//jQuery('.preload_error_css').html(data[1]);
				setTimeout(create_critical_css, 20000);
				jQuery('#create_critical_css').prop('disabled', true);
				jQuery('.critical-css-bar').hide();
				jQuery('.critical-progress-bar').addClass('progress-bar-animated progress-bar-striped');
			}
		},
		error: function (errorThrown) {
			jQuery('.critical-progress-bar').addClass('progress-bar-animated progress-bar-striped');
			console.log(errorThrown);
		}
	});
}

jQuery(window).load(function(){
	if (window.location.href.includes('w3_custom_code')) {
		jQuery('.w3_custom_code').click();
	}
});
jQuery(document).ready(function(){
	var tabBtn = document.querySelector('.mobile_toggle button');
	var tabPanel = document.querySelector('.tab-panel');
	tabBtn.addEventListener('click', function () {
		tabPanel.classList.toggle('menu-open');
	});
	jQuery('button.reset_image_optimization.btn').click(function () {
		if (confirm('Are you sure you want to reset image optimization?')) {
			var currentUrl = window.location.href;
			var newUrl = currentUrl + (currentUrl.indexOf('?') === -1 ? '?' : '&') + 'reset=1';

			window.location.href = newUrl;
		}
	});
	jQuery('.expend-textarea').click(function () {
		var id = jQuery(this).attr('data-id');
		event.preventDefault();
		jQuery("#" + id).toggleClass("fullscreen");
	})

	jQuery('#import_button').click(function () {
		var text = jQuery("#import_text").val();
		if (!IsJsonString(text)) {
			alert("Data is courrpted, please check and enter again.");
		}
		jQuery('#import_form').submit();
	});
	var hash = window.location.hash;
	if (hash) {
		jQuery(hash).prop("checked", "checked");
	}
	jQuery('[name="tabs"]').click(function () {
		window.location.hash = jQuery(this).attr("id");
	});
	jQuery('.add_more_image').click(function () {
		var index = jQuery(this).parents('#w3_opt_img_content').find('.image_src_field').length;

		var $html = '<tr class="image_src_field"><td style="width:70%; padding-left:0px;"><input type="text" name="optimiz_images[' + index + '][src]" placeholder="Please Enter Img Src" value=""></td><td style="padding-left:0px;"><input type="text" name="optimiz_images[' + index + '][width]" placeholder="Please Enter Image Width" value=""></td><td class="remove_image_field" style="width:5%; cursor:pointer;">X</td></tr>';

		jQuery(this).parents('.image_add_more_field').before($html);
	});

	jQuery('.add_more_combine_image').click(function () {

		var index = jQuery(this).parents('#w3_opt_img_combin_content').find('.image_src_field').length;
		var $html = '<tr class="image_src_field"><td style="width:70%; padding-left:0px;"><input type="text" name="combine_images[' + index + '][src]" placeholder="Please Enter Img Src" value=""></td><td style="padding-left:0px;"><input type="text" name="combine_images[' + index + '][position]" placeholder="Please Enter Image Width" value=""></td><td class="remove_image_field" style="width:5%; cursor:pointer;">X</td></tr>';

		jQuery(this).parents('.image_add_more_field').before($html);
	});

	//jQuery('.remove_image_field').click(function(){
	jQuery("table").delegate(".remove_image_field", "click", function () {
		jQuery(this).parents('.image_src_field').remove();
	});

	jQuery("ul.w3speedsternav li a").click(function (e) {

		e.preventDefault();
		var url = document.location.href;
		var newTab = jQuery(this).attr('data-section');
		var updatedUrl = updateQueryStringParameter(url, 'tab', newTab);
		history.pushState({}, '', updatedUrl);
		jQuery('.tab-pane').removeClass('active in');
		jQuery('#' + newTab).addClass('active in');
	});
	var hash = window.location.href.match(/[?&]tab=([^&]+)/);
	if (hash && hash[1] && hash[1].length > 0) {
		jQuery('.tab-pane').removeClass('active in');
		jQuery('#' + hash[1]).addClass('active in');
	}


	function updateQueryStringParameter(uri, key, value) {
		var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
		var separator = uri.indexOf('?') !== -1 ? "&" : "?";

		if (uri.match(re)) {
			return uri.replace(re, '$1' + key + "=" + value + '$2');
		} else {
			return uri + separator + key + "=" + value;
		}
	}



	function checkHookData(script) {
		//	var script = jQuery(this).val();
		jQuery.ajax({
			url: adminUrl,
			type: 'POST',
			data: {
				'action': 'hookBeforeStartOptimization',
				'script': JSON.stringify(script),
				'_wpnonce': secureKey
			},
			success: function (data) {
				jQuery('.CodeMirror.cm-s-default.CodeMirror-wrap').removeClass('error_textarea');
				if (data.trim().length > 1) {
					newData = jQuery.parseJSON(data)[0];
					var startIndex = data.indexOf('{"error":"') + 10;
					var endIndex = data.lastIndexOf('"}');
					console.log(data.slice(startIndex, endIndex));
					jQuery('.error-hook-main').show();
					jQuery('.error_hooks').html(data.slice(startIndex, endIndex));
					jQuery(newData).parent('.single-hook').find('.CodeMirror.cm-s-default.CodeMirror-wrap').addClass('error_textarea');

					//jQuery('.single-hook').hide();
					//jQuery(newData).parent('.single-hook').show();
					jQuery('.error_hooks').show();
					jQuery('li.w3_hooks a').click();
					jQuery('.save-changes-loader').hide();

				} else {

					jQuery('.error_hooks').hide();
					jQuery('.main-form').submit();
				}


			}.bind(this),
			error: function (errorThrown) {
				//console.log(errorThrown);
				jQuery('.CodeMirror.cm-s-default.CodeMirror-wrap').removeClass('error_textarea');
				jQuery('.error_hooks').show();
				var text = errorThrown.responseText.replace(/\\/g, '');
				var startIndex = text.indexOf('{"error":') + 10;
				var endIndex = text.lastIndexOf('in ') - 1;
				jQuery('.error-hook-main').show();
				jQuery('.error_hooks').html(text.slice(startIndex, endIndex));
				jQuery('li.w3_hooks a').click();
				jQuery('.save-changes-loader').hide();
				if (text.length > 1) {
					jQuery(this).addClass('error_textarea');
					jQuery('form.main-form input[type=submit]').prop("disabled", true);
				}
			}.bind(this)
		});

	};

	jQuery('.hook_submit').on('click', function () {
		jQuery('.save-changes-loader').show();
		/*var script = [];
		jQuery('.hook_before_start').each(function () {
			var id = '#' + jQuery(this).attr('id')
			var editorValue = jQuery(id).next(".CodeMirror").find(".CodeMirror-code").text();
			if (editorValue.length > 1) {
				script.push({ hookKey: id, value: editorValue });
			}
		});*/
		//jQuery('.main-form').submit();
		jQuery(this).closest('form').submit();
		/*checkHookData(script)*/

	});

	jQuery('.error_hooks_close').click(function () {
		jQuery(this).parent('.error-hook-main').hide();
	});

	jQuery('.add_more_row').click(function () {
		var inputName = jQuery(this).attr('data-name');
		var placeholder = jQuery(this).attr('data-placeholder');
		var html = '<div class="cdn_input_box minus w3d-flex"><input placeholder="' + placeholder + '" type="text" name="' + inputName + '[]""><button type="button" class="w3text-white rem-row w3bg-danger"><i class="fa fa-times"></i></button></div>';
		jQuery(this).closest('.input_box').find('.single-row').append(html);

	});
	jQuery('.input_box').on('click', '.rem-row', function () {
		jQuery(this).closest('.cdn_input_box.minus.w3d-flex').remove();
	});
	// For Hooks functionality

	function get_all_hooks() {
		var search_elementItems = '';
		//jQuery('.entry_search_container').show();
		jQuery('.single-hook').each(function () {
			var searchLabel = jQuery(this).find('span.main-label').html();
			var customClass = searchLabel.toLowerCase().replace(/\s+/g, '');
			jQuery(this).addClass('filter-' + customClass)
			var top = jQuery(this).position().top;
			search_elementItems += '<li><a class="scroll_element_item" data-label="' + searchLabel + '" data-filter="' + customClass + '" data-top="' + top + '" href="javascript:void(0);">' + jQuery(this).find('span.main-label').html() + '</a></li>';
		})
		search_elementItems = '<ul>' + search_elementItems + '</ul>';
		jQuery(".entry_search_contaner").html(search_elementItems);
		jQuery('.all_hooks').removeClass('single_selected');
	}


	jQuery('.pl_search_field').on("focus", function () {
		jQuery('.entry_search_contaner').show();
		var searchTerm = jQuery(this).val();
		if (searchTerm.length == 0) {
			get_all_hooks();
		}

	});
	jQuery('.pl_search_field').focusout(function () {
		var searchTerm = jQuery(this).val();
		setTimeout(function () {
			jQuery('.entry_search_contaner').hide();
		}, 300)


	});
	jQuery('.pl_search_field').on("keyup", function () {
		var search_elementItems = '';
		var searchTerm = jQuery(this).val();
		var entrySearch_sec = jQuery(".entry_search_contaner");

		jQuery('.entry_search_container').show();

		if (searchTerm.length > 0) {
			jQuery('.clear_field').show();
			jQuery('.all_hooks').removeClass('single_selected');
			var element_heading = jQuery('.single-hook');
			element_heading.each(function (index) {

				var ele_str = jQuery(this).text();
				if (ele_str.toLowerCase().indexOf(searchTerm.toLowerCase()) != -1) {
					jQuery(this).show();
					jQuery(this).addClass('active');
					var searchLabel = jQuery(this).find('span.main-label').html();
					var customClass = searchLabel.toLowerCase().replace(/\s+/g, '');
					jQuery(this).addClass('filter-' + customClass)
					if (jQuery(this).parents('a').length > 0) {
						search_elementItems += '<li><a href="' + jQuery(this).parents('a').attr('href') + '">' + jQuery(this).text() + '</a></li>';
					} else {
						var top = jQuery(this).position().top;
						search_elementItems += '<li><a class="scroll_element_item" data-label="' + searchLabel + '"data-filter="' + customClass + '" data-top="' + top + '" href="javascript:void(0);">' + jQuery(this).find('span.main-label').html() + '</a></li>';
					}
				} else {
					jQuery(this).hide();
					jQuery(this).removeClass('active');

				}
			});

			if (null == search_elementItems || "" == search_elementItems) {

				search_elementItems = '<li>No matching.</li>';
			}
			search_elementItems = '<ul>' + search_elementItems + '</ul>';
			jQuery(".entry_search_contaner").html(search_elementItems);

		} else {
			jQuery('.single-hook').show();
			jQuery('.single-hook').removeClass('active');
			get_all_hooks();
			jQuery('.clear_field').hide();
		}

	});

	function scrollElem(dataFilter) {
		jQuery('.single-hook').hide();
		jQuery('.single-hook.filter-' + dataFilter).show();
		jQuery('.all_hooks').addClass('single_selected');
		jQuery('.clear_field').show();
		jQuery('.entry_search_contaner').html('');
		return;
	}

	jQuery("body").delegate(".scroll_element_item", "click", function () {
		var top = jQuery(this).attr('data-top');
		var dataFilter = jQuery(this).attr('data-filter');
		scrollElem(dataFilter);
		jQuery('.pl_search_field').val(jQuery(this).attr('data-label'));
	});

	jQuery("body").delegate(".used_hook_btn", "click", function () {
		var dataFilter = jQuery(this).attr('data-filter');
		scrollElem(dataFilter);
		jQuery('.pl_search_field').val(jQuery(this).attr('data-label'));
	});
	jQuery('body').click(function (e) {
		var container = jQuery(".menu-header-search");
		// If the target of the click isn't the container
		if (!container.is(e.target) && container.has(e.target).length === 0) {
			jQuery('.entry_search_container').hide();
		}
	});

	get_all_hooks();

	jQuery('button.clear_field').click(function () {
		jQuery('.pl_search_field').val('');
		jQuery(this).hide();
		jQuery('.single-hook').show();
		jQuery('.all_hooks').removeClass('single_selected');
	});

	// End
	// For Logs Functionality

	function w3SpeedsterAjaxLoadLog(limit, issueType, urls, startDate, endDate, deviceType, paged, refBy) {
		jQuery('.log-data-table').addClass('loading');
		jQuery.ajax({
			url: adminUrl,
			method: 'POST',
			data: {
				'action': 'w3SpeedsterGetLogData',
				'getBy': 'ajax',
				'limit': limit,
				'issuetype': issueType,
				'url': urls,
				'start_date': startDate,
				'end_date': endDate,
				'paged': paged,
				'deviceType': deviceType,
			},
			success: function (data) {
				jQuery('.log-data-table').html(data);
				jQuery('.log-data-table').removeClass('loading');

			}, error: function (errorThrown) {
				console.log(errorThrown);
			}
		})
	}
	jQuery('.btn-log-delete').on('click', function () {
		var timeValue = jQuery('.log_select').val();
		jQuery('.log-data-table').addClass('loading');
		jQuery.ajax({
			url: adminUrl,
			method: 'POST',
			data: {
				'action': 'w3SpeedsterDeleteLogData',
				'time_interval': timeValue,
			},
			success: function (data) {
				jQuery('.log-data-table').html(data);
				jQuery('.log-data-table').removeClass('loading');

			}, error: function (errorThrown) {
				console.log(errorThrown);
			}
		})
	})

	function filterClearDefaultValue() {
		jQuery('.filter_by_issuetype').val('');
		jQuery('.filter_by_deviceType').val('');
		jQuery('#filter_by_url').val('').trigger('change');
		jQuery('.start_date').val('');
		jQuery('.end_date').val('');
		jQuery('.custom_select_inp').val('');
		jQuery('.url_checkbox').prop('checked', false);
		jQuery('span.select2.select2-container.select2-container--default').hide();
		jQuery('.btn_clear_url_inp').hide();
	}

	function getLogData(page = '') {
		var limit = jQuery('.show_log_entry').val();
		var issueType = jQuery('.filter_by_issuetype').val();
		var url = jQuery('#filter_by_url').val();
		var startDate = jQuery('.start_date').val();
		var endDate = jQuery('.end_date').val();
		var deviceType = jQuery('.filter_by_deviceType').val();
		var paged = '';
		if (page > 0) {
			paged = page;
		} else {
			paged = jQuery('.p-num.active').attr('data-page');
		}

		w3SpeedsterAjaxLoadLog(limit, issueType, url, startDate, endDate, deviceType, paged, '');
	}

	jQuery(document).on('click', '.pagination .p-num', function () {
		jQuery('.p-num').removeClass('active');
		jQuery(this).addClass('active')
		getLogData();
	});
	jQuery(document).on('click', '.pagination .page-next', function () {
		jQuery('.p-num').removeClass('active');
		var page = jQuery(this).attr('data-page');
		getLogData((parseInt(page) + 1));
	});

	jQuery(document).on('click', '.pagination .page-next-last', function () {
		jQuery('.p-num').removeClass('active');
		var page = jQuery(this).attr('data-page');
		getLogData(parseInt(page));
	});
	jQuery(document).on('click', '.pagination .page-prev', function () {
		jQuery('.p-num').removeClass('active');
		var page = jQuery(this).attr('data-page');
		var updatedPage = (parseInt(page) - 1);
		if (updatedPage > 1) {
			updatedPage = (parseInt(page) - 1);
		} else {
			updatedPage = 1;
		}
		getLogData(updatedPage);
	});


	jQuery(document).on('click', '.btn-log-refresh, .btn-apply-filter', function () {
		getLogData();
	});

	jQuery('.btn-rem-filter').click(function () {
		var limit = jQuery('.show_log_entry').val();
		filterClearDefaultValue();
		w3SpeedsterAjaxLoadLog(limit, '', '', '', '', '', 1, 'refresh');
	});

	jQuery('.show_log_entry').on('change', function () {
		getLogData();
	})

	jQuery('#enable-webvitals-log').on('change', function () {
		jQuery('.main-form').submit();
	});

	jQuery('.start_date').datepicker({
		changeMonth: true,
		changeYear: true,
		yearRange: "-100:+0"
	});
	jQuery('.start_date').show();
	jQuery('.end_date').datepicker({
		changeMonth: true,
		changeYear: true,
		yearRange: "-100:+0"
	});
	jQuery('.end_date').show();


	jQuery(document).on('click', '.more_info', function () {
		var id = jQuery(this).attr('data-id');
		console.log('data--id', id);
		console.log('this', this); // Now `this` refers to the clicked element
		var data = jQuery('.data_' + id + ' .log-data').html();
		console.log('data', data);
		var html = '<li><strong>Data:</strong><code>' + data + '</code></li>';
		//console.log('data', html);
		jQuery('.log-info').html(html);
	});


	jQuery('.url-select-multiple').select2();
	jQuery('span.select2.select2-container.select2-container--default').hide();


	jQuery(document).on('keyup', '.custom_select_inp', function () {
		var text = jQuery(this).val();
		if (text.length > 0) {
			jQuery('.btn_clear_url_inp').show();
		}
		if (text.length > 2) {
			jQuery('#custom_select_url').show();
			jQuery.ajax({
				url: adminUrl,
				method: 'POST',
				data: {
					'action': 'w3SpeddsterShowUrlSuggestions',
					's_text': text,
				},
				success: function (response) {
					console.log('resp--', response.length, '---', response)
					var responseData = JSON.parse(response);
					if (responseData.length == 0) {
						jQuery('#custom_select_url').html('No Url Found');
					} else {

						var selectedValues = jQuery('#filter_by_url').val();

						var createdOptions = [];
						jQuery('#filter_by_url').find('option').each(function () {
							createdOptions.push(jQuery(this).val());
						});

						var createdOptionsWithCheckobx = [];
						jQuery('.single-url .url').each(function () {
							createdOptionsWithCheckobx.push(jQuery(this).html());
						})
						var options = '';

						var optionsWithCheckbox = '<ul class="option_checkobx">';


						jQuery.each(responseData, function (index, value) {
							var checkedUrl = '';
							if (jQuery.inArray(value, selectedValues) != -1) {
								checkedUrl = 'checked';
							}
							if (jQuery.inArray(value, createdOptions) == -1) {
								options += '<option value="' + value + '">' + value + '</option>';

							}
							optionsWithCheckbox += '<div class="single-url"><div class="url">' + value + '</div><input type="checkbox" name="temp_input" class="url_checkbox" value="" ' + checkedUrl + '></div>';
						});

						optionsWithCheckbox += '</ul>';

						if (options.length > 0) {
							jQuery('#filter_by_url').append(options);
						}
						jQuery('#custom_select_url').html(optionsWithCheckbox);
					}
				},
				error: function (errorThrown) {
					console.log(errorThrown);
				}
			});
		} else if (text.length == 0) {
			jQuery('#custom_select_url').hide();
			jQuery('.btn_clear_url_inp').hide();
		}
	});

	jQuery('.btn_clear_url_inp').on('click', function () {
		jQuery('.custom_select_inp').val('');
		jQuery(this).hide();
	})
	jQuery(document).on('change', '.url_checkbox', function () {
		var selectedUrls = jQuery('#filter_by_url').val();
		var url = jQuery(this).parent('.single-url').find('.url').html();
		if (jQuery(this).is(":checked")) {
			if (jQuery.inArray(url, selectedUrls) == -1) {
				selectedUrls.push(url);
			}
		} else {
			selectedUrls = selectedUrls.filter(function (item) {
				return item !== url;
			});
		}
		jQuery('#filter_by_url').val(selectedUrls);
		jQuery('#filter_by_url').trigger('change');

		if (selectedUrls.length > 0) {
			jQuery('span.select2.select2-container.select2-container--default').show();
		} else {
			jQuery('span.select2.select2-container.select2-container--default').hide();
		}

	});

	jQuery("#custom_select_url").on("click", function (event) {
		event.stopPropagation();
	});

	jQuery(document).on("click", function (event) {
		if (!jQuery(event.target).closest("#custom_select_url").length) {
			jQuery("#custom_select_url").hide();
		}
	});
	document.addEventListener('scroll', function () {
		const scrollPosition = window.scrollY || window.pageYOffset;
		const images = document.querySelectorAll('.admin-speedster .tab-panel')[0];
		if (images.length > 0) {
			images.forEach(image => {
				if (scrollPosition > 50) {
					image.classList.add('fixed');
				} else {
					image.classList.remove('fixed');
				}
			});
		}
	});
});