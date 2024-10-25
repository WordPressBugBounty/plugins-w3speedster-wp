<section id="general" class="tab-pane fade<?php echo empty($tab) || $tab == 'general' ? ' active in' : ''; ?>">
	<div class="header w3d-flex gap20">
		<div class="heading_container">
			<h4 class="w3heading">
				<?php $admin->translate('General Setting'); ?>
			</h4>
			<h4 class="w3_sub_heading">
				<?php $admin->translate('Optimization Level'); ?>
			</h4> <span class="info"><a
					href="https://w3speedster.com/w3speedster-documentation/#general_setting"><?php $admin->translate('More info'); ?>?
				</a></span>
		</div>
		<div class="icon_container">
			<img src="<?php echo W3SPEEDSTER_URL; ?>assets/images/general-setting-icon.webp">
		</div>
	</div>
	<hr>
	<div class="license_key w3d-flex gap20">
		<label for="">
			<?php $admin->translate('License Key'); ?><span class="info"></span><span
				class="info-display">
				<?php $admin->translate('Activate key to get updates and access to all features of the plugin.'); ?>
			</span>
		</label>
		<div class="key w3d-flex">
			<input type="text" name="license_key"
				placeholder="<?php $admin->translate('Key'); ?>"
				value="<?php echo !empty($result['license_key']) ? $admin->esc_attr($result['license_key']) : ''; ?>"
				style="">
			<input type="hidden" name="w3_api_url"
				value="<?php echo !empty($result['w3_api_url']) ? $admin->esc_attr($result['w3_api_url']) : ''; ?>">
			<input type="hidden" name="is_activated"
				value="<?php echo !empty($result['is_activated']) ? $admin->esc_attr($result['is_activated']) : ''; ?>">
			<input type="hidden" name="_wpnonce"
				value="<?php echo $admin->createSecureKey('w3_settings'); ?>">
			<input type="hidden" name="ws_action" value="cache">
			<?php if (!empty($result['license_key']) && !empty($result['is_activated'])) {
				?>
				<i class="fa fa-check-circle-o" aria-hidden="true"></i>
				<?php
			} else { ?>
				<button class="activate-key btn" type="button">
					<?php $admin->translate('Activate'); ?>
				</button>
			<?php }
			?>
		</div>
	</div>
	<?php
	if($networkAdmin){ ?>
		<div class="manage-separately w3d-flex gap20">
			<label><?php $admin->translate('Manage Each Site Separately'); ?><span
					class="info"></span><span
					class="info-display"><?php $admin->translate('Enable this option to enter separate settings for each site. Plugin page will then be available in the backend of every site.'); ?></span></label>
			<div class="input_box">
				<label class="switch" for="manage-site-separately">
					<input type="checkbox" name="manage_site_separately" <?php if (!empty($result['manage_site_separately']) && $result['manage_site_separately'] == "on")
						echo "checked"; ?> id="manage-site-separately" class="basic-set">
					<div class="checked"></div>
				</label>
			</div>
		</div>
	<?php } ?>
	<hr>
	<div class="main <?php echo $hidden_class; ?>">

		<div class="way_to_psi">
			<details>
				<summary>
					<h4 class="w3heading w3text-skyblue">
						<?php $admin->translate('Way to 90+ in Google PSI'); ?></h4>
				</summary>
				<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
					<label><?php $admin->translate('Basic Settings'); ?><span
							class="info"></span><span
							class="info-display"><?php $admin->translate('Enable This for Basic Settings'); ?>
						</span></label>
					<div class="input_box">
						<label class="switch" for="main-basic-settings">
							<input type="checkbox" name="main-basic-setting" id="main-basic-settings"
								<?php if (!empty($result['main-basic-setting']))
									echo "checked"; ?>
								data-class="basic-set" class="basic-set-checkbox">
							<div class="checked"></div>
						</label>
					</div>
				</div>
				<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
					<label><?php $admin->translate('Optimize images and convert images to webp'); ?><span
							class="info"></span><span
							class="info-display"><?php $admin->translate('This will optimize and convert image to webp'); ?></span></label>
					<div class="input_box w3d-flex">
						<label class="switch" for="optimize-images-and-convert-images-to-webp">
							<input type="checkbox" name="main-opt-img"
								id="optimize-images-and-convert-images-to-webp" <?php if (!empty($result['main-opt-img']))
									echo "checked"; ?> data-class="main-opt-img"
								class="basic-set-checkbox">
							<div class="checked"></div>
						</label>&nbsp;&nbsp;&nbsp;
						<?php if ($img_remaining > 0 && !empty($result['main-opt-img'])) { ?>
							<div class="in-progress w3d-flex">
								<img src="<?php echo W3SPEEDSTER_URL; ?>assets/images/loader-gif.gif"
									alt="loader" class="loader-img">
								<small
									class="extra-small m-0">&nbsp;<em>&nbsp;<?php $admin->translate('Image optimization in progress...'); ?></em></small>
							</div>
						<?php } ?>
					</div>
				</div>
				<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
					<label><?php $admin->translate('Lazyload Resources'); ?><span
							class="info"></span><span
							class="info-display"><?php $admin->translate('This will enable lazy loading of resources.'); ?></span></label>
					<div class="input_box">
						<label class="switch" for="lazyload-images">
							<input type="checkbox" name="main-lazy-image" id="lazyload-images" <?php if (!empty($result['main-lazy-image']))
								echo "checked"; ?>
								data-class="lazy-reso" class="basic-set-checkbox">
							<div class="checked"></div>
						</label>
					</div>
				</div>
				<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
					<label><?php $admin->translate('Responsive images'); ?><span
							class="info"></span><span
							class="info-display"><?php $admin->translate('Load smaller images on mobile to reduce load time.'); ?></span></label>
					<div class="input_box">
						<label class="switch" for="responsive-images">
							<input type="checkbox" name="main-resp-img" id="responsive-images" <?php if (!empty($result['main-resp-img']))
								echo "checked"; ?>
								data-class="resp-img" class="basic-set-checkbox">
							<div class="checked"></div>
						</label>
					</div>
				</div>
				<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
					<label><?php $admin->translate('Optimize css'); ?><span
							class="info"></span><span
							class="info-display"><?php $admin->translate('It will turn on css optimization and generate critical css.'); ?></span></label>
					<div class="input_box w3d-flex">
						<label class="switch" for="optimize-css">
							<input type="checkbox" name="main-opt-css" id="optimize-css" <?php if (!empty($result['main-opt-css']))
								echo "checked"; ?>
								data-class="opt-css" class="basic-set-checkbox">
							<div class="checked"></div>
						</label>&nbsp;&nbsp;&nbsp;
						<?php if ($preload_total != $preload_created && !empty($result['main-opt-css'])) { ?>
							<div class="in-progress w3d-flex">
								<img src="<?php echo W3SPEEDSTER_URL; ?>assets/images/loader-gif.gif"
									alt="loader" class="loader-img">
								<small
									class="extra-small m-0">&nbsp;<em>&nbsp;<?php $admin->translate('Critical css is generating...'); ?></em></small>
							</div>
						<?php } ?>
					</div>
				</div>
				<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
					<label><?php $admin->translate('Lazyload javascript'); ?><span
							class="info"></span><span
							class="info-display"><?php $admin->translate('It will turn on javascript optimization and lazyload them.'); ?></span></label>
					<div class="input_box">
						<label class="switch" for="lazyload-javascript">
							<input type="checkbox" name="main-lazy-js" id="lazyload-javascript" <?php if (!empty($result['main-lazy-js']))
								echo "checked"; ?>
								data-class="opt-js" class="basic-set-checkbox">
							<div class="checked"></div>
						</label>
					</div>
				</div>
		</details>
		</div>
		<hr>
		<div class="turn_on_optimization <?php echo $hidden_class; ?>">
			<div class="w3d-flex gap20">
				<label><?php $admin->translate('Turn ON optimization'); ?><span
						class="info"></span><span
						class="info-display"><?php $admin->translate('Site will start to optimize. All optimization settings will be applied.'); ?></span></label>
				<div class="input_box">
					<label class="switch" for="turn-on-optimization">
						<input type="checkbox" name="optimization_on" <?php if (!empty($result['optimization_on']) && $result['optimization_on'] == "on")
							echo "checked"; ?> id="turn-on-optimization" class="basic-set">
						<div class="checked"></div>
					</label>
				</div>
			</div>
			<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
				<label><?php $admin->translate('Optimize Pages with Query Parameters'); ?><span
						class="info"></span><span
						class="info-display"><?php $admin->translate('It will optimize pages with query parameters. Recommended only for servers with high performance.'); ?></span></label>
				<div class="input_box">
					<label class="switch" for="optimize-pages-with-query-parameters">
						<input type="checkbox" name="optimize_query_parameters"
							id="optimize-pages-with-query-parameters" <?php if (!empty($result['optimize_query_parameters']))
								echo "checked"; ?>
							class="basic-set">
						<div class="checked"></div>
					</label>
				</div>
			</div>
			<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
				<label><?php $admin->translate('Optimize pages when User Logged In'); ?><span
						class="info"></span><span
						class="info-display"><?php $admin->translate('It will optimize pages when users are logged in. Recommended only for servers with high performance'); ?></span></label>
				<div class="input_box">
					<label class="switch" for="optimize-pages-when-user-logged-in">
						<input type="checkbox" name="optimize_user_logged_in"
							id="optimize-pages-when-user-logged-in" <?php if (!empty($result['optimize_user_logged_in']))
								echo "checked"; ?>>
						<div class="checked"></div>
					</label>
				</div>
			</div>
			<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
				<label><?php $admin->translate('Separate javascript and css cache for mobile'); ?><span
						class="info"></span><span
						class="info-display"><?php $admin->translate('It will create separate javascript and css cache for mobile'); ?></span></label>
				<div class="input_box">
					<label class="switch" for="separate-javascript-and-css-cache-for-mobile">
						<input type="checkbox" name="separate_cache_for_mobile"
							id="separate-javascript-and-css-cache-for-mobile" <?php if (!empty($result['separate_cache_for_mobile']))
								echo "checked"; ?>>
						<div class="checked"></div>
					</label>
				</div>
			</div>
			<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
				<label><?php $admin->translate('Fix INP Issues'); ?><span
						class="info"></span><span
						class="info-display"><?php $admin->translate('Enable to fix Interactive next paint issues appearing in googe page speed assessment test and/or google search console.'); ?></span></label>
				<div class="input_box">
					<label class="switch">
						<input type="checkbox" name="enable_inp" <?php if (!empty($result['enable_inp']) && $result['enable_inp'] == "on")
							echo "checked"; ?>
							id="enable-inp">
						<div class="checked"></div>
					</label>
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
