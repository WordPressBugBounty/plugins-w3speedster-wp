<section id="css" class="tab-pane fade<?php echo $tab == 'css' ? ' active in' : ''; ?>">
	<div class="header w3d-flex gap20">
		<div class="heading_container">
			<h4 class="w3heading">
				<?php $admin->translate('CSS Optimization'); ?>
			</h4>
			<span class="info"><a
					href="https://w3speedster.com/w3speedster-documentation/#css_optimization"><?php $admin->translate('More info'); ?>?
				</a></span>
		</div>
		<div class="icon_container">
			<img src="<?php echo W3SPEEDSTER_URL; ?>assets/images/css-icon.webp">
		</div>
	</div>
	<hr>
	<div class="css_box">
		<div class="w3d-flex gap20 ">
			<label><?php $admin->translate('Enable CSS Optimization'); ?><span
					class="info"></span><span
					class="info-display"><?php $admin->translate('Turn on to optimize css'); ?></span></label>
			<div class="input_box">
				<label class="switch" for="enable-css-minification">
					<input type="checkbox" name="css" <?php if (!empty($result['css']) && $result['css'] == "on")
						echo "checked"; ?> id="enable-css-minification"
						class="opt-css">
					<div class="checked"></div>
				</label>
			</div>
		</div>
		<div class="w3d-flex gap20 ">
			<label><?php $admin->translate('Combine Google fonts'); ?><span
					class="info"></span><span
					class="info-display"><?php $admin->translate('Turn on to combine all google fonts'); ?></span></label>
			<div class="input_box">
				<label class="switch" for="combine-google-fonts">
					<input type="checkbox" name="google_fonts" <?php if (!empty($result['google_fonts']) && $result['google_fonts'] == "on")
						echo "checked"; ?>
						id="combine-google-fonts" class="opt-css">
					<div class="checked"></div>
				</label>
			</div>
		</div>
	</div>
	<hr>
	<div class="css_box">
		<div class="w3d-flex gap20 ">
			<label><?php $admin->translate('Load Critical CSS'); ?><span
					class="info"></span><span
					class="info-display"><?php $admin->translate('Preload generated crictical css'); ?></span></label>
			<div class="input_box">
				<label class="switch" for="load-critical-css">
					<input type="checkbox" name="load_critical_css" <?php if (!empty($result['load_critical_css']) && $result['load_critical_css'] == "on")
						echo "checked"; ?> id="load-critical-css" class="opt-css">
					<div class="checked"></div>
				</label>
			</div>
		</div>
		<div class="w3d-flex gap20 critical-in-style" <?php if (empty($result['load_critical_css'])) {
			echo 'style="display:none"';} ?>>
			<label><?php $admin->translate('Load Critical CSS in Style Tag'); ?><span
					class="info"></span><span
					class="info-display"><?php $admin->translate('Preload generated crictical css in style tag'); ?></span></label>
			<div class="input_box">
				<label class="switch" for="load-critical-css-in-style-tag">
					<input type="checkbox" name="load_critical_css_style_tag" <?php if (!empty($result['load_critical_css_style_tag']) && $result['load_critical_css_style_tag'] == "on")
						echo "checked"; ?>
						id="load-critical-css-in-style-tag" class="opt-css">
					<div class="checked"></div>
				</label>
			</div>
		</div>
		<div class="w3d-flex gap20 critical-in-style" <?php if (empty($result['load_critical_css'])) {
			echo 'style="display:none"';} ?>>
			<label><?php $admin->translate('Create Critical CSS via wp-cron'); ?><span
					class="info"></span><span
					class="info-display"><?php $admin->translate('Create Critical CSS via wp-cron.'); ?></span></label>
			<div class="input_box">
				<label class="switch" for="create-critical-css-via-wp-cron">
					<input type="checkbox" name="enable_background_critical_css" <?php echo (!empty($result['enable_background_critical_css']) ? "checked" : ''); ?>
						id="create-critical-css-via-wp-cron">
					<div class="checked"></div>
				</label>
			</div>
		</div>
		<?php if (!empty($result['load_critical_css']) && $result['load_critical_css'] == "on") { ?>
			<div class="w3d-block">
				<div class="control_box w3d-flex gap20">
					<label for=""><?php $admin->translate('Start generating critical css'); ?> <br>
						<?php if (empty($result['license_key']) || empty($result['is_activated'])) { ?>
							<small
								class="w3text-danger"><?php $admin->translate('Critical CSS for only homepage will be generated.'); ?></small>
						<?php } ?>
					</label>
					<p class="w3d-flex go_pro gap20"><input type="button" id="create_critical_css"
							value="<?php $admin->translate('CREATE CRITICAL CSS'); ?>"
							class="btn gen-critical">
						<?php if (empty($result['license_key']) || empty($result['is_activated'])) { ?>
							<a href="https://w3speedster.com/"
								class="w3text-success"><strong><u><?php $admin->translate('GO PRO'); ?></u></strong></a>
						</p>
					<?php } ?>
				</div>
				<div class="result_box">
					<div class="progress-container">
						<div class="progress progress-bar w3bg-success critical-progress-bar"
							style="width:<?php echo $preload_created > 0 ? number_format(($preload_created / $preload_total * 100), 1) : 1; ?>%">
							<?php
							$percent = $preload_created > 0 ? number_format((($preload_created / $preload_total * 100)), 1) : 1;
							echo '<span class="progress-percent">' . esc_html($percent) . '%</span>'; ?>
						</div>
					</div>
					<span class="preload_created_css">
						<?php echo esc_html($preload_created); ?>
					</span> <?php echo $admin->translate_('created of') ?> 
					<span class="preload_total_css">
						<?php echo esc_html($preload_total); ?>
					</span> <?php echo $admin->translate_('pages crawled') ?>
					<?php $critical_css_error = $admin->w3GetOption('w3speedup_critical_css_error'); ?>
					<textarea disabled rows="1" cols="100"
						class="preload_error_css"><?php echo (empty($result['load_critical_css'])) ? $admin->translate_('*Please enable load critical css and save to start generating critical css') : $admin->esc_attr($critical_css_error); ?>
					</textarea>

				</div>
			</div>
		<?php } ?>
	</div>
	<hr>
	<div class="css_box cdn_resources">
		<div class="w3d-flex gap20 w3align-item-baseline">
			<label><?php $admin->translate('Load Style Tag in Head to Avoid CLS'); ?> <span
					class="info"></span><span
					class="info-display"><?php $admin->translate('Enter matching text of style tag, which are to be loaded in the head. Each style tag to be entered in a new line'); ?></span></label>
			<div class="input_box">
				<div class="single-row">
					<?php
					if (array_key_exists('load_style_tag_in_head', $result)) {
						foreach (explode("\r\n", $result['load_style_tag_in_head']) as $row) {
							if (!empty(trim($row))) {
								?>
								<div class="cdn_input_box minus w3d-flex">
									<input type="text" name="load_style_tag_in_head[]"
										value="<?php echo $admin->esc_attr(trim($row)); ?>"
										placeholder="<?php $admin->translate('Please Enter style tag text'); ?>"><button
										type="button" class="w3text-white rem-row w3bg-danger"><i
											class="fa fa-times"></i></button>
								</div>
								<?php
							}
						}
					} ?>
				</div>
				<div class="cdn_input_box plus">
					<button type="button" data-name="load_style_tag_in_head"
						data-placeholder="<?php $admin->translate('Please Enter style tag text'); ?>"
						class="btn small w3text-white w3bg-success add_more_row"><?php $admin->translate('Add Rule'); ?></button>
				</div>
			</div>
		</div>
	</div>
	<hr>
	<div class="save-changes w3d-flex gap10">
		<input type="button" value="<?php $admin->translate('Save Changes'); ?>"
			class="btn hook_submit">
		<div class="in-progress w3d-flex save-changes-loader" style="display:none">
			<img src="<?php echo W3SPEEDSTER_URL; ?>assets/images/loader-gif.gif" alt="loader"
				class="loader-img">
		</div>
	</div>
</section>
