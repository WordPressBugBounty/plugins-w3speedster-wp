<section id="webvitalslogs" class="tab-pane fade<?php echo $tab == 'webvitalslogs' ? ' active in' : ''; ?>">
	<div class="header w3d-flex gap20">
		<div class="heading_container">
			<h4 class="w3heading"><?php $admin->translate('Debug Logs'); ?>
			</h4>
			<span class="info"><a
					href="https://w3speedster.com/w3speedster-documentation/"><?php $admin->translate('More info'); ?>?
				</a></span>
		</div>
		<div class="icon_container"> <img
				src="<?php echo W3SPEEDSTER_URL; ?>assets/images/logs-icon.webp"></div>
	</div>
	<hr>

	<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
		<label><?php $admin->translate('Enable Core Web Vitals Logs'); ?><span
				class="info"></span><span
				class="info-display"><?php $admin->translate('Enable to Log Core Web Vitals Logs.'); ?></span></label>
		<div class="input_box">
			<label class="switch" for="enable-webvitals-log">
				<input type="checkbox" name="webvitals_logs" <?php if (!empty($result['webvitals_logs']) && $result['webvitals_logs'] == "on")
					echo "checked"; ?> id="enable-webvitals-log"
					class="basic-set">
				<div class="checked"></div>
			</label>
		</div>
	</div>
	<?php if (empty($result['webvitals_logs'])) {
		echo '<p class="alert_message">Enable Debug Log options for Logging</p>';
	} else {
		?>

		<div class="w3d-flex gap20 filter-row">
			<div class="show_log w3d-flex gap10">
				<label for="show_log_entry"><?php $admin->translate('Show'); ?></label>
				<select name="temp_input" id="show_log_entry" class="show_log_entry">
					<option value="10"><?php $admin->translate('10'); ?></option>
					<option value="20"><?php $admin->translate('20'); ?></option>
					<option value="30"><?php $admin->translate('30'); ?></option>
					<option value="40"><?php $admin->translate('40'); ?></option>
					<option value="50"><?php $admin->translate('50'); ?></option>
				</select>
			</div>
			<div class="delete-log-data w3d-flex gap10">
				<label for="log_delete_time">Delete Logs</label>
				<select class="log_select" id="log_delete_time" name="temp_input">
					<option value=""><?php $admin->translate('Select Log Time'); ?></option>
					<option value="last7days"><?php $admin->translate('Keep last 7 Days'); ?></option>
					<option value="lastMonth"><?php $admin->translate('Keep last 30 Days'); ?></option>
					<option value="last3months"><?php $admin->translate('Keep last 90 Days'); ?>
					</option>
					<option value="last6months"><?php $admin->translate('Keep last 180 Days'); ?>
					</option>
					<!-- <option value="lastYear">All</option> -->
					<option value="all"><?php $admin->translate('All'); ?></option>
				</select>
				<button type="button"
					class="btn btn-log-delete"><?php $admin->translate('Delete'); ?></button>
			</div>

		</div>
		<div class="w3d-flex gap10 filter-row">
			<div class="filter_by_issue w3d-flex gap10">
				<label for="filter_by_issue"><?php $admin->translate('Issue Type'); ?></label>
				<select name="temp_input" class="filter_by_issuetype">
					<option value=""><?php $admin->translate('All'); ?></option>
					<option value="CLS"><?php $admin->translate('CLS'); ?></option>
					<option value="FID"><?php $admin->translate('FID'); ?></option>
					<option value="INP"><?php $admin->translate('INP'); ?></option>
					<option value="LCP"><?php $admin->translate('LCP'); ?></option>
				</select>
			</div>
			<div class="filter_by_device w3d-flex gap10">
				<label for="filter_by_device"><?php $admin->translate('Device'); ?></label>
				<select name="temp_input" class="filter_by_deviceType">
					<option value=""><?php $admin->translate('All'); ?></option>
					<option value="Mobile"><?php $admin->translate('Mobile'); ?></option>
					<option value="Desktop"><?php $admin->translate('Desktop'); ?></option>
				</select>
			</div>
			<div class="filter_by_url ">
				<select class="url-select-multiple" id="filter_by_url" class="filter_by_url_input"
					name="temp_input[]" multiple="multiple">
					<input type="text" class="custom_select_inp"
						placeholder="<?php $admin->translate('https://...'); ?>">
					<button type="button" class="btn_clear_url_inp" style="display:none">+</button>
					<div id="custom_select_url"></div>
				</select>
			</div>
			<div class="filter_by_date w3d-flex gap10">
				<label for="start_date"><?php $admin->translate('From'); ?></label>
				<input type="text" name="temp_input" class="start_date">
				<label for="end_date"><?php $admin->translate('To'); ?></label>
				<input type="text" name="temp_input" class="end_date">
			</div>
			<button type="button"
				class="btn btn-apply-filter"><?php $admin->translate('Apply Filters'); ?></button>
			<button type="button"
				class="btn btn-rem-filter"><?php $admin->translate('Clear'); ?></button>
		</div>
		<div popover="auto" id="more_info">
			<button type="button" popovertarget="more_info" popovertargetaction="hide" title="Close"
				class="close-popover">+</button>
			<ul class="log-info">

			</ul>
		</div>
		<div class="log-data-table">

			<?php
			// @codingStandardsIgnoreLine
			echo w3SpeedsterGetLogData();
			?>
		</div>
		<?php
	}
	?>
</section>
