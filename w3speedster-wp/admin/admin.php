<?php
$admin = $args['settings'];
$result = $admin->settings;
$tab = !empty($_GET['tab']) ? $_GET['tab'] : '';

list($img_to_opt,$img_remaining) = $admin->getImageOptimizationDetails();
$admin->scheduleImageOptimizationCron($img_remaining);
list($preload_total, $preload_created) = $admin->w3CriticalCssDetails();
?>
<script>
var adminUrl = "<?php echo $admin->getAjaxUrl(); ?>";
var secureKey = "<?php echo $admin->createSecureKey("hook_callback"); ?>";
</script>
<main class="admin-speedster">
	<div class="top_panel_container">
		<div class="top_panel d-none">
			<div class="logo_container">
				<img class="logo" src="<?php echo W3SPEEDSTER_URL; ?>assets/images/w3-logo.png">
			</div>

			<div class="support_section">
				<div class="right_section">
					<div class="doc w3d-flex gap10">
						<p class="m-0"><i class="fa fa-file-text" aria-hidden="true"></i></p>
						<p class="m-0 text-center w3text-white">
							<?php $admin->translate('Need help or have question', 'w3speedster'); ?><br><a
								href="https://w3speedster.com/w3speedster-documentation/"
								target="_blank"><?php $admin->translate('Check our documentation', 'w3speedster'); ?></a>
						</p>

					</div>

				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="tab-panel col-md-2">
			<div class="mobile_toggle d-none">
				<button type="button">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512">
						<path fill="#fff"
							d="M246.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-128-128c-9.2-9.2-22.9-11.9-34.9-6.9S63.9 115 63.9 128v256c0 12.9 7.8 24.6 19.8 29.6s25.7 2.2 34.9-6.9l128-128z" />
					</svg>
				</button>
			</div>
			<div class="logo_container">
				<img class="logo" src="<?php echo W3SPEEDSTER_URL; ?>assets/images/w3-logo.png">
			</div>
			<ul class="nav nav-tabs w3speedsternav">
				<?php if (empty($result['manage_site_separately'])) { ?>
					<li class="w3_html_cache<?php echo $tab == 'htmlCache' ? ' active' : ''; ?>"><a data-toggle="tab"
							data-section="htmlCache" href="avascript:void(0)">
							<?php $admin->translate('HTML Cache', 'w3speedster'); ?>
						</a></li>
				<?php } ?>
				<li class="w3_general<?php echo empty($tab) || $tab == 'general' ? ' active' : ''; ?>"><a
						data-toggle="tab" data-section="general" href="javascript:void(0)">
						<?php $admin->translate('General', 'w3speedster'); ?>
					</a></li>
				<?php if (empty($result['manage_site_separately'])) { ?>
					<li class="w3_opt_img<?php echo $tab == 'opt_img' ? ' active' : ''; ?>"><a data-toggle="tab"
							data-section="opt_img" href="javascript:void(0)">
							<?php $admin->translate('Image Optimization', 'w3speedster'); ?>
						</a></li>
					<li class="w3_css<?php echo $tab == 'css' ? ' active' : ''; ?>"><a data-toggle="tab" data-section="css"
							href="javascript:void(0)">
							<?php $admin->translate('Css', 'w3speedster'); ?>
						</a></li>
					<li class="w3_js<?php echo $tab == 'js' ? 'active' : ''; ?>"><a data-toggle="tab" data-section="js"
							href="javascript:void(0)">
							<?php $admin->translate('Javascript', 'w3speedster'); ?>
						</a></li>
					<li class="w3_exclusions<?php echo $tab == 'exclusions' ? ' active' : ''; ?>"><a data-toggle="tab"
							data-section="exclusions" href="javascript:void(0)">
							<?php $admin->translate('Exclusions', 'w3speedster'); ?>
						</a></li>
					<li class="w3_custom_code<?php echo $tab == 'w3_custom_code' ? ' active' : ''; ?>"><a data-toggle="tab"
							data-section="w3_custom_code" href="javascript:void(0)">
							<?php $admin->translate('Custom Code', 'w3speedster'); ?>
						</a></li>
					<li class="w3_cache<?php echo $tab == 'cache' ? ' active' : ''; ?>"><a data-toggle="tab"
							data-section="cache" href="javascript:void(0)">
							<?php $admin->translate('Clear Cache', 'w3speedster'); ?>
						</a></li>
					<li class="w3_hooks<?php echo $tab == 'hooks' ? ' active' : ''; ?>"><a data-toggle="tab"
							data-section="hooks" href="javascript:void(0)">
							<?php $admin->translate('Hooks', 'w3speedster'); ?>
						</a></li>
					<li class="w3_webvitals_log<?php echo $tab == 'webvitalslogs' ? ' active' : ''; ?>"><a data-toggle="tab"
							data-section="webvitalslogs" href="javascript:void(0)">
							<?php $admin->translate('Web Vitals Logs', 'w3speedster'); ?>
						</a></li>
					<li class="w3_import<?php echo $tab == 'import' ? ' active' : ''; ?>"><a data-toggle="tab"
							data-section="import" href="javascript:void(0)">
							<?php $admin->translate('Import/Export', 'w3speedster'); ?>
						</a></li>
				<?php } ?>
			</ul>

			<div class="support_section">
				<a class="doc btn" href="https://w3speedster.com/w3speedster-documentation/"
					target="_blank"><?php $admin->translate('Documentation', 'w3speedster'); ?> <i
						class="fa fa-long-arrow-right" aria-hidden="true"></i></a>
				<a class="contact btn"
					href="https://w3speedster.com/contact-us/"><?php $admin->translate('Contact Us', 'w3speedster'); ?> <i
						class="fa fa-long-arrow-right" aria-hidden="true"></i></a>
			</div>
		</div>

		<form method="post" class="main-form">
			<div class="tab-content col-md-10">
				<section id="general" class="tab-pane fade in active">
					<div class="header w3d-flex gap20">
						<div class="heading_container">
							<h4 class="w3heading">
								<?php $admin->translate('General Setting', 'w3speedster'); ?>
							</h4>
							<h4 class="w3_sub_heading">
								<?php $admin->translate('Optimization Level', 'w3speedster'); ?>
							</h4> <span class="info"><a
									href="https://w3speedster.com/w3speedster-documentation/#general_setting"><?php $admin->translate('More info', 'w3speedster'); ?>?
								</a></span>
						</div>
						<div class="icon_container">
							<img src="<?php echo W3SPEEDSTER_URL; ?>assets/images/general-setting-icon.webp">
						</div>
					</div>
					<hr>
					<div class="license_key w3d-flex gap20">
						<label for="">
							<?php $admin->translate('License Key', 'w3speedster'); ?><span class="info"></span><span
								class="info-display">
								<?php $admin->translate('Activate key to get updates and access to all features of the plugin.', 'w3speedster'); ?>
							</span>
						</label>
						<div class="key w3d-flex">
							<input type="text" name="license_key"
								placeholder="<?php $admin->translate('Key', 'w3speedster'); ?>"
								value="<?php echo !empty($result['license_key']) ? $admin->esc_attr($result['license_key']) : ''; ?>"
								style="">
							<input type="hidden" name="w3_api_url"
								value="<?php echo !empty($result['w3_api_url']) ? $admin->esc_attr($result['w3_api_url']) : ''; ?>">
							<input type="hidden" name="is_activated"
								value="<?php echo !empty($result['is_activated']) ? $admin->esc_attr($result['is_activated']) : ''; ?>">
							<input type="hidden" name="_wpnonce"
								value="<?php echo $admin->esc_attr(wp_create_nonce('w3_settings')); ?>">
							<input type="hidden" name="ws_action" value="cache">
							<?php if (!empty($result['license_key']) && !empty($result['is_activated'])) {
								?>
								<i class="fa fa-check-circle-o" aria-hidden="true"></i>
								<?php
							} else { ?>
								<button class="activate-key btn" type="button">
									<?php $admin->translate('Activate', 'w3speedster'); ?>
								</button>
							<?php }
							?>
						</div>
					</div>
					<?php
					if (function_exists('is_multisite') && is_multisite() && is_network_admin()) { ?>
						<div class="manage-separately w3d-flex gap20">
							<label><?php $admin->translate('Manage Each Site Separately', 'w3speedster'); ?><span
									class="info"></span><span
									class="info-display"><?php $admin->translate('Enable this option to enter separate settings for each site. Plugin page will then be available in the backend of every site.', 'w3speedster'); ?></span></label>
							<div class="input_box">
								<label class="switch" for="manage-site-separately">
									<input type="checkbox" name="manage_site_separately" <?php if (!empty($result['manage_site_separately']) && $result['manage_site_separately'] == "on")
										echo "checked"; ?> id="manage-site-separately" class="basic-set">
									<div class="checked"></div>
								</label>
							</div>
						</div>
					<?php }
					$hidden_class = '';
					if (!empty($result['manage_site_separately']) && is_network_admin()) {
						$hidden_class = 'tr-hidden';
					} ?>
					<hr>
					<div class="main <?php echo $hidden_class; ?>">

						<div class="way_to_psi">
							<details>
								<summary>
									<h4 class="w3heading w3text-skyblue">
										<?php $admin->translate('Way to 90+ in Google PSI', 'w3speedster'); ?></h4>
								</summary>
								<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
									<label><?php $admin->translate('Basic Settings', 'w3speedster'); ?><span
											class="info"></span><span
											class="info-display"><?php $admin->translate('Enable This for Basic Settings', 'w3speedster'); ?>
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
									<label><?php $admin->translate('Optimize images and convert images to webp', 'w3speedster'); ?><span
											class="info"></span><span
											class="info-display"><?php $admin->translate('This will optimize and convert image to webp', 'w3speedster'); ?></span></label>
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
													class="extra-small m-0">&nbsp;<em>&nbsp;<?php $admin->translate('Image optimization in progress...', 'w3speedster'); ?></em></small>
											</div>
										<?php } ?>
									</div>
								</div>
								<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
									<label><?php $admin->translate('Lazyload Resources', 'w3speedster'); ?><span
											class="info"></span><span
											class="info-display"><?php $admin->translate('This will enable lazy loading of resources.', 'w3speedster'); ?></span></label>
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
									<label><?php $admin->translate('Responsive images', 'w3speedster'); ?><span
											class="info"></span><span
											class="info-display"><?php $admin->translate('Load smaller images on mobile to reduce load time.', 'w3speedster'); ?></span></label>
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
									<label><?php $admin->translate('Optimize css', 'w3speedster'); ?><span
											class="info"></span><span
											class="info-display"><?php $admin->translate('It will turn on css optimization and generate critical css.', 'w3speedster'); ?></span></label>
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
													class="extra-small m-0">&nbsp;<em>&nbsp;<?php $admin->translate('Critical css is generating...', 'w3speedster'); ?></em></small>
											</div>
										<?php } ?>
									</div>
								</div>
								<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
									<label><?php $admin->translate('Lazyload javascript', 'w3speedster'); ?><span
											class="info"></span><span
											class="info-display"><?php $admin->translate('It will turn on javascript optimization and lazyload them.', 'w3speedster'); ?></span></label>
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
								<label><?php $admin->translate('Turn ON optimization', 'w3speedster'); ?><span
										class="info"></span><span
										class="info-display"><?php $admin->translate('Site will start to optimize. All optimization settings will be applied.', 'w3speedster'); ?></span></label>
								<div class="input_box">
									<label class="switch" for="turn-on-optimization">
										<input type="checkbox" name="optimization_on" <?php if (!empty($result['optimization_on']) && $result['optimization_on'] == "on")
											echo "checked"; ?> id="turn-on-optimization" class="basic-set">
										<div class="checked"></div>
									</label>
								</div>
							</div>
							<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
								<label><?php $admin->translate('Optimize Pages with Query Parameters', 'w3speedster'); ?><span
										class="info"></span><span
										class="info-display"><?php $admin->translate('It will optimize pages with query parameters. Recommended only for servers with high performance.', 'w3speedster'); ?></span></label>
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
								<label><?php $admin->translate('Optimize pages when User Logged In', 'w3speedster'); ?><span
										class="info"></span><span
										class="info-display"><?php $admin->translate('It will optimize pages when users are logged in. Recommended only for servers with high performance', 'w3speedster'); ?></span></label>
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
								<label><?php $admin->translate('Separate javascript and css cache for mobile', 'w3speedster'); ?><span
										class="info"></span><span
										class="info-display"><?php $admin->translate('It will create separate javascript and css cache for mobile', 'w3speedster'); ?></span></label>
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
								<label><?php $admin->translate('CDN url', 'w3speedster'); ?><span class="info"></span><span
										class="info-display"><?php $admin->translate('Enter CDN url with http or https', 'w3speedster'); ?></span></label>
								<div class="input_box">
									<label for="cdn-url">
										<input type="text" name="cdn" id="cdn-url"
											placeholder="<?php $admin->translate('Please Enter CDN url here', 'w3speedster'); ?>"
											value="<?php if (!empty($result['cdn']))
												echo $admin->esc_attr($result['cdn']); ?>"></label>
								</div>
							</div>
							<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
								<label><?php $admin->translate('Exclude file extensions from cdn', 'w3speedster'); ?><span
										class="info"></span><span
										class="info-display"><?php $admin->translate('Enter extension separated by comma which are to excluded from CDN. For eg. (.woff, .eot)', 'w3speedster'); ?></span></label>
								<div class="input_box">
									<label for="exclude-file-extensions-from-cdn">
										<input type="text" name="exclude_cdn" id="exclude-file-extensions-from-cdn"
											placeholder="<?php $admin->translate('Please Enter extensions separated by comma ie .jpg, .woff', 'w3speedster'); ?>"
											value="<?php if (!empty($result['exclude_cdn']))
												echo $admin->esc_attr($result['exclude_cdn']); ?>"></label>
								</div>
							</div>
							<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
								<label><?php $admin->translate('Exclude path from cdn', 'w3speedster'); ?><span
										class="info"></span><span
										class="info-display"><?php $admin->translate('Enter path separated by comma which are to excluded from CDN. For eg. (/wp-includes/)', 'w3speedster'); ?></span></label>
								<div class="input_box">
									<label for="exclude-path-from-cdn">
										<input type="text" name="exclude_cdn_path" id="exclude-path-from-cdn"
											placeholder="<?php $admin->translate('Please Enter extensions separated by comma', 'w3speedster'); ?>"
											value="<?php if (!empty($result['exclude_cdn_path']))
												echo $admin->esc_attr($result['exclude_cdn_path']); ?>"></label>
								</div>
							</div>
							<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
								<label><?php $admin->translate('Enable leverage browsing cache', 'w3speedster'); ?><span
										class="info"></span><span
										class="info-display"><?php $admin->translate('Enable to turn on leverage browsing cache.', 'w3speedster'); ?></span></label>
								<div class="input_box">
									<label class="switch" for="enable-leverage-browsing-cache">
										<input type="checkbox" name="lbc" id="enable-leverage-browsing-cache" <?php if (!empty($result['lbc']) && $result['lbc'] == "on")
											echo "checked"; ?>
											class="basic-set">
										<div class="checked"></div>
									</label>
								</div>
							</div>
							<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
								<label><?php $admin->translate('Enable Gzip compression', 'w3speedster'); ?><span
										class="info"></span><span
										class="info-display"><?php $admin->translate('Enable to turn on Gzip compresssion.', 'w3speedster'); ?></span></label>
								<div class="input_box">
									<label class="switch" for="enable-gzip-compression">
										<input type="checkbox" name="gzip" <?php if (!empty($result['gzip']) && $result['gzip'] == "on")
											echo "checked"; ?> id="enable-gzip-compression"
											class="basic-set">
										<div class="checked"></div>
									</label>
								</div>
							</div>
							<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
								<label><?php $admin->translate('Remove query parameters', 'w3speedster'); ?><span
										class="info"></span><span
										class="info-display"><?php $admin->translate('Enable to remove query parameters from resources.', 'w3speedster'); ?></span></label>
								<div class="input_box">
									<label class="switch" for="remove-query-parameters">
										<input type="checkbox" name="remquery" <?php if (!empty($result['remquery']) && $result['remquery'] == "on")
											echo "checked"; ?> id="remove-query-parameters"
											class="basic-set">
										<div class="checked"></div>
									</label>
								</div>
							</div>
							<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
								<label><?php $admin->translate('Fix INP Issues', 'w3speedster'); ?><span
										class="info"></span><span
										class="info-display"><?php $admin->translate('Enable to fix Interactive next paint issues appearing in googe page speed assessment test and/or google search console.', 'w3speedster'); ?></span></label>
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

						<hr>
						<div class="cdn_resources <?php echo $hidden_class; ?>">
							<div class="w3d-flex gap20 w3align-item-baseline">
								<label for="cache_path"><?php $admin->translate('Cache Path', 'w3speedster'); ?><span
										class="info"></span><span
										class="info-display"><?php $admin->translate('Enter path where cache can be stored. Leave empty for default path', 'w3speedster'); ?></span></label>
								<div class="input_box">
									<div class="cdn_input_box">
										<input type="text" name="cache_path"
											placeholder="<?php $admin->translate('Please Enter full cache path', 'w3speedster'); ?>"
											value="<?php echo !empty($result['cache_path']) ? $admin->esc_attr($result['cache_path']) : ''; ?>"
											id="cache_path"
											placeholder="<?php $admin->translate('Please Enter full cache path', 'w3speedster'); ?>">
										<small
											class="w3d-block"><?php $admin->translate('Default cache path:', 'w3speedster'); ?>
											<?php echo esc_html($admin->add_settings['content_path'] . '/cache'); ?>
										</small>
									</div>
								</div>

							</div>
						</div>
						<hr>
					</div>
					<div class="save-changes w3d-flex gap10">
						<input type="button" value="<?php $admin->translate('Save Changes', 'w3speedster'); ?>"
							class="btn hook_submit">
						<div class="in-progress w3d-flex save-changes-loader" style="display:none">
							<img src="<?php echo W3SPEEDSTER_URL; ?>assets/images/loader-gif.gif" alt="loader"
								class="loader-img">
						</div>
					</div>
				</section>

				<section id="css" class="tab-pane fade">
					<div class="header w3d-flex gap20">
						<div class="heading_container">
							<h4 class="w3heading">
								<?php $admin->translate('CSS Optimization', 'w3speedster'); ?>
							</h4>
							<span class="info"><a
									href="https://w3speedster.com/w3speedster-documentation/#css_optimization"><?php $admin->translate('More info', 'w3speedster'); ?>?
								</a></span>
						</div>
						<div class="icon_container">
							<img src="<?php echo W3SPEEDSTER_URL; ?>assets/images/css-icon.webp">
						</div>
					</div>
					<hr>
					<div class="css_box">
						<div class="w3d-flex gap20 ">
							<label><?php $admin->translate('Enable CSS Optimization', 'w3speedster'); ?><span
									class="info"></span><span
									class="info-display"><?php $admin->translate('Turn on to optimize css', 'w3speedster'); ?></span></label>
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
							<label><?php $admin->translate('Combine Google fonts', 'w3speedster'); ?><span
									class="info"></span><span
									class="info-display"><?php $admin->translate('Turn on to combine all google fonts', 'w3speedster'); ?></span></label>
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
							<label><?php $admin->translate('Load Critical CSS', 'w3speedster'); ?><span
									class="info"></span><span
									class="info-display"><?php $admin->translate('Preload generated crictical css', 'w3speedster'); ?></span></label>
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
							<label><?php $admin->translate('Load Critical CSS in Style Tag', 'w3speedster'); ?><span
									class="info"></span><span
									class="info-display"><?php $admin->translate('Preload generated crictical css in style tag', 'w3speedster'); ?></span></label>
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
							<label><?php $admin->translate('Create Critical CSS via wp-cron', 'w3speedster'); ?><span
									class="info"></span><span
									class="info-display"><?php $admin->translate('Create Critical CSS via wp-cron.', 'w3speedster'); ?></span></label>
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
									<label for=""><?php $admin->translate('Start generating critical css', 'w3speedster'); ?> <br>
										<?php if (empty($result['license_key']) || empty($result['is_activated'])) { ?>
											<small
												class="w3text-danger"><?php $admin->translate('Critical CSS for only homepage will be generated.', 'w3speedster'); ?></small>
										<?php } ?>
									</label>
									<p class="w3d-flex go_pro gap20"><input type="button" id="create_critical_css"
											value="<?php $admin->translate('CREATE CRITICAL CSS', 'w3speedster'); ?>"
											class="btn gen-critical">
										<?php if (empty($result['license_key']) || empty($result['is_activated'])) { ?>
											<a href="https://w3speedster.com/"
												class="w3text-success"><strong><u><?php $admin->translate('GO PRO', 'w3speedster'); ?></u></strong></a>
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
									</span> <?php echo $admin->translate_('created of', 'w3speedster') ?> <span
										class="preload_total_css">
										<?php echo esc_html($preload_total); ?>
									</span> <?php echo $admin->translate_('pages crawled', 'w3speedster') ?></td>
									<?php $critical_css_error = w3GetOption('w3speedup_critical_css_error'); ?>
									<textarea disabled rows="1" cols="100"
										class="preload_error_css"><?php echo (empty($result['load_critical_css'])) ? $admin->translate_('*Please enable load critical css and save to start generating critical css', 'w3speedster') : $admin->esc_attr($critical_css_error); ?></textarea>

								</div>
							</div>
						<?php } ?>
					</div>
					<hr>
					<div class="css_box cdn_resources">
						<div class="w3d-flex gap20 w3align-item-baseline">
							<label><?php $admin->translate('Load Style Tag in Head to Avoid CLS', 'w3speedster'); ?> <span
									class="info"></span><span
									class="info-display"><?php $admin->translate('Enter matching text of style tag, which are to be loaded in the head. Each style tag to be entered in a new line', 'w3speedster'); ?></span></label>
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
														placeholder="<?php $admin->translate('Please Enter style tag text', 'w3speedster'); ?>"><button
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
										data-placeholder="<?php $admin->translate('Please Enter style tag text', 'w3speedster'); ?>"
										class="btn small w3text-white w3bg-success add_more_row"><?php $admin->translate('Add Rule', 'w3speedster'); ?></button>
								</div>
							</div>
						</div>
					</div>
					<hr>
					<div class="save-changes w3d-flex gap10">
						<input type="button" value="<?php $admin->translate('Save Changes', 'w3speedster'); ?>"
							class="btn hook_submit">
						<div class="in-progress w3d-flex save-changes-loader" style="display:none">
							<img src="<?php echo W3SPEEDSTER_URL; ?>assets/images/loader-gif.gif" alt="loader"
								class="loader-img">
						</div>
					</div>

				</section>
				<section id="js" class="tab-pane fade white-bg-speedster">
					<div class="header w3d-flex gap20">
						<div class="heading_container">
							<h4 class="w3heading">
								<?php $admin->translate('Javascript Optimization', 'w3speedster'); ?>
							</h4>
							<span class="info"><a
									href="https://w3speedster.com/w3speedster-documentation/#javascript_optimization"><?php $admin->translate('More info', 'w3speedster'); ?>?
								</a></span>
						</div>
						<div class="icon_container"><img
								src="<?php echo W3SPEEDSTER_URL; ?>assets/images/js-icon.webp"></div>
					</div>
					<hr>

					<div class="js_box">
						<div class="w3d-flex gap20 ">
							<label><?php $admin->translate('Enable Javascript Optimization', 'w3speedster'); ?><span
									class="info"></span><span
									class="info-display"><?php $admin->translate('Turn on to optimize javascript', 'w3speedster'); ?></span></label>
							<div class="input_box">
								<label class="switch" for="enable-js-minification">
									<input type="checkbox" name="js" <?php if (!empty($result['js']) && $result['js'] == "on")
										echo "checked"; ?> id="enable-js-minification"
										class="opt-js">
									<div class="checked"></div>
								</label>
							</div>
						</div>

						<div class="w3d-flex gap20 ">
							<label><?php $admin->translate('Lazyload Javascript', 'w3speedster'); ?> <span
									class="info"></span><span
									class="info-display"><?php $admin->translate('Choose when to load javascript', 'w3speedster'); ?></span></label>
							<select name="load_combined_js" class="opt-js-select">
								<option value="after_page_load" <?php echo !empty($result['load_combined_js']) && $result['load_combined_js'] == 'after_page_load' ? 'selected' : ''; ?>>
									<?php $admin->translate('Yes', 'w3speedster'); ?>
								</option>
								<option value="on_page_load" <?php echo !empty($result['load_combined_js']) && $result['load_combined_js'] == 'on_page_load' ? 'selected' : ''; ?>>
									<?php $admin->translate('No', 'w3speedster'); ?>
								</option>

							</select>
						</div>
					</div>
					<hr>
					<div class="js_box cdn_resources">
						<div class="w3d-flex gap20 w3align-item-baseline">
							<label><?php $admin->translate('Load Javascript Inline Script as URL', 'w3speedster'); ?><span
									class="info"></span><span
									class="info-display"><?php $admin->translate('Enter matching text of inline script url which needs to be load in a url to avoid large page size, javascript execution time. Each exclusion to be entered in a new line.', 'w3speedster'); ?></span></label>
							<div class="input_box">
								<div class="single-row">
									<?php
									if (array_key_exists('load_script_tag_in_url', $result)) {
										foreach (explode("\r\n", $result['load_script_tag_in_url']) as $row) {
											if (!empty(trim($row))) {
												?>
												<div class="cdn_input_box minus w3d-flex">
													<input type="text" name="load_script_tag_in_url[]"
														value="<?php echo $admin->esc_attr(trim($row)); ?>"
														placeholder="<?php $admin->translate('Please Enter matching text of the inline javascript here', 'w3speedster'); ?>"><button
														type="button" class="w3text-white rem-row w3bg-danger"><i
															class="fa fa-times"></i></button>
												</div>
												<?php
											}
										}
									} ?>
								</div>
								<div class="cdn_input_box plus">
									<button type="button" data-name="load_script_tag_in_url"
										data-placeholder="<?php $admin->translate('Please Enter matching text of the inline javascript here', 'w3speedster'); ?>"
										class="btn small w3text-white w3bg-success add_more_row"><?php $admin->translate('Add Rule', 'w3speedster'); ?></button>
								</div>

							</div>
						</div>
					</div>
					<hr>
					<div class="save-changes w3d-flex gap10">
						<input type="button" value="<?php $admin->translate('Save Changes', 'w3speedster'); ?>"
							class="btn hook_submit">
						<div class="in-progress w3d-flex save-changes-loader" style="display:none">
							<img src="<?php echo W3SPEEDSTER_URL; ?>assets/images/loader-gif.gif" alt="loader"
								class="loader-img">
						</div>
					</div>

				</section>
				<section id="exclusions" class="tab-pane fade">
					<div class="header w3d-flex gap20">
						<div class="heading_container">
							<h4 class="w3heading">
								<?php $admin->translate('Exclusions', 'w3speedster'); ?>
							</h4>
							<span class="info"><a
									href="https://w3speedster.com/w3speedster-documentation/"><?php $admin->translate('More info', 'w3speedster'); ?>?
								</a></span>
						</div>
						<div class="icon_container"> <img
								src="<?php echo W3SPEEDSTER_URL; ?>assets/images/exclusions-icon1.webp"></div>
					</div>
					<hr>
					<div class="way_to_psi">
					<details>
						<summary>
							<h4 class="w3heading w3text-skyblue"><?php $admin->translate('Resources Exclusions', 'w3speedster'); ?></h4>
						</summary>
						<div class="cdn_resources <?php echo $hidden_class; ?>">
							<div class="w3d-flex gap20 w3align-item-baseline">
								<label
									for="Preload Resources"><?php $admin->translate('Preload Resources', 'w3speedster'); ?><span
										class="info"></span><span
										class="info-display"><?php $admin->translate('Enter url of the Resources, which are to be preloaded..', 'w3speedster'); ?></span></label>
								<div class="input_box">
									<div class="single-row">
										<?php
										//$result['preload_resources'] = 'hello';
										if (array_key_exists('preload_resources', $result)) {
											foreach (explode("\r\n", $result['preload_resources']) as $row) {
												if (!empty(trim($row))) {
													?>
													<div class="cdn_input_box minus w3d-flex">
														<input type="text" name="preload_resources[]"
															value="<?php echo $admin->esc_attr(rtrim($row)); ?>"
															placeholder="<?php $admin->translate('Please Enter Resource Url', 'w3speedster'); ?>"><button
															type="button" class="w3text-white rem-row w3bg-danger"><i
																class="fa fa-times"></i></button>
													</div>
													<?php
												}
											}
										} ?>
									</div>
									<div class="cdn_input_box plus">
										<button type="button" data-name="preload_resources"
											data-placeholder="<?php $admin->translate('Please Enter Resource Url', 'w3speedster'); ?>"
											class="btn small w3text-white w3bg-success add_more_row"><?php $admin->translate('Add Rule', 'w3speedster'); ?></button>
									</div>

								</div>

							</div>
						</div>

						<!-- <hr> -->
						<div class="cdn_resources <?php echo $hidden_class; ?>">
							<div class="w3d-flex gap20 w3align-item-baseline">
								<label
									for="Exclude Resources from Lazy Loading"><?php $admin->translate('Exclude Images from Lazy Loading', 'w3speedster'); ?><span
										class="info"></span><span
										class="info-display"><?php $admin->translate('Enter any matching text of image/iframe/video/audio tag to exclude from lazy loading. For more than one exclusion, click on add rule. For eg. (class / Id / url / alt).', 'w3speedster'); ?></span></label>
								<div class="input_box">
									<div class="single-row">
										<?php
										//$result['exclude_lazy_load'] = 'hello';
										if (array_key_exists('exclude_lazy_load', $result)) {
											foreach (explode("\r\n", $result['exclude_lazy_load']) as $row) {
												if (!empty(trim($row))) {
													?>
													<div class="cdn_input_box minus w3d-flex">
														<input type="text" name="exclude_lazy_load[]"
															value="<?php echo $admin->esc_attr(trim($row)); ?>"
															placeholder="<?php $admin->translate('Please Enter matching text of the image here', 'w3speedster'); ?>"><button
															type="button" class="w3text-white rem-row w3bg-danger"><i
																class="fa fa-times"></i></button>
													</div>
													<?php
												}
											}
										} ?>

									</div>
									<div class="cdn_input_box plus">
										<button type="button" data-name="exclude_lazy_load"
											data-placeholder="<?php $admin->translate('Please Enter matching text of the image here', 'w3speedster'); ?>"
											class="btn small w3text-white w3bg-success add_more_row"><?php $admin->translate('Add Rule', 'w3speedster'); ?></button>
									</div>

								</div>

							</div>
						</div>
						<!-- <hr> -->
						
					</details>
					</div>
					<hr>
					<div class="way_to_psi">
					<details>
						<summary>
							<h4 class="w3heading w3text-skyblue"><?php $admin->translate('CSS Exclusions', 'w3speedster'); ?></h4>
						</summary>
			
					<div class="css_box cdn_resources ">
						<div class="w3d-flex gap20 w3align-item-baseline">
							<label><?php $admin->translate('Exclude Link Tag CSS from Optimization', 'w3speedster'); ?><span
									class="info"></span><span
									class="info-display"><?php $admin->translate('Enter matching text of css link url, which are to be excluded from css optimization. For each Exclusion, click on add rule', 'w3speedster'); ?></span></label>
							<div class="input_box">
								<div class="single-row">
									<?php
									if (array_key_exists('exclude_css', $result)) {
										foreach (explode("\r\n", $result['exclude_css']) as $row) {
											if (!empty(trim($row))) {
												?>
												<div class="cdn_input_box minus w3d-flex">
													<input type="text" name="exclude_css[]" value="<?php echo $admin->esc_attr(trim($row)); ?>"
														placeholder="<?php $admin->translate('Please Enter part of link tag css here', 'w3speedster'); ?>"><button
														type="button" class="w3text-white rem-row w3bg-danger"><i
															class="fa fa-times"></i></button>
												</div>
												<?php
											}
										}
									} ?>
								</div>
								<div class="cdn_input_box plus">
									<button type="button" data-name="exclude_css"
										data-placeholder="<?php $admin->translate('Please Enter part of link tag css here', 'w3speedster'); ?>"
										class="btn small w3text-white w3bg-success add_more_row"><?php $admin->translate('Add Rule', 'w3speedster'); ?></button>
								</div>
							</div>
						</div>

					</div>
					<!-- <hr> -->

					<div class="css_box cdn_resources">
						<div class="w3d-flex gap20 w3align-item-baseline">
							<label><?php $admin->translate('Force Lazy Load Link Tag CSS', 'w3speedster'); ?> <span
									class="info"></span><span
									class="info-display"><?php $admin->translate('Enter matching text of css link url, which are forced to be lazyloaded and will load on user interaction. For each Exclusion, click on add rule.', 'w3speedster'); ?></span></label>
							<div class="input_box">
								<div class="single-row">
									<?php
									if (array_key_exists('force_lazyload_css', $result)) {
										foreach (explode("\r\n", $result['force_lazyload_css']) as $row) {
											if (!empty(trim($row))) {
												?>
												<div class="cdn_input_box minus w3d-flex">
													<input type="text" name="force_lazyload_css[]"
														value="<?php echo $admin->esc_attr(trim($row)); ?>"
														placeholder="<?php $admin->translate('Please Enter part of link tag css here', 'w3speedster'); ?>"><button
														type="button" class="w3text-white rem-row w3bg-danger"><i
															class="fa fa-times"></i></button>
												</div>
												<?php
											}
										}
									} ?>
								</div>
								<div class="cdn_input_box plus">
									<button type="button" data-name="force_lazyload_css"
										data-placeholder="<?php $admin->translate('Please Enter part of link tag css here', 'w3speedster'); ?>"
										class="btn small w3text-white w3bg-success add_more_row"><?php $admin->translate('Add Rule', 'w3speedster'); ?></button>
								</div>
							</div>
						</div>
					</div>
					<!-- <hr> -->
					
					</details>
					</div>
					<hr>
					<div class="way_to_psi">
					<details>
						<summary>
							<h4 class="w3heading w3text-skyblue"><?php $admin->translate('JS Exclusions', 'w3speedster'); ?></h4>
						</summary>
						<div class="js_box cdn_resources">
							<div class="w3d-flex gap20 w3align-item-baseline">
								<label><?php $admin->translate('Force Lazy Load Javascript', 'w3speedster'); ?> <span
										class="info"></span><span
										class="info-display"><?php $admin->translate('Enter matching text of inline javascript which needs to be forced to lazyload. For each Exclusion, click on add rule.', 'w3speedster'); ?></span></label>
								<div class="input_box">
									<div class="single-row">
										<?php
										if (array_key_exists('force_lazy_load_inner_javascript', $result)) {
											foreach (explode("\r\n", $result['force_lazy_load_inner_javascript']) as $row) {
												if (!empty(trim($row))) {
													?>
													<div class="cdn_input_box minus w3d-flex">
														<input type="text" name="force_lazy_load_inner_javascript[]"
															value="<?php echo $admin->esc_attr(trim($row)); ?>"
															placeholder="<?php $admin->translate('Please Enter matching text of the inline javascript here', 'w3speedster'); ?>"><button
															type="button" class="w3text-white rem-row w3bg-danger"><i
																class="fa fa-times"></i></button>
													</div>
													<?php
												}
											}
										} ?>
									</div>
									<div class="cdn_input_box plus">
										<button type="button" data-name="force_lazy_load_inner_javascript"
											data-placeholder="<?php $admin->translate('Please Enter matching text of the inline javascript here', 'w3speedster'); ?>"
											class="btn small w3text-white w3bg-success add_more_row"><?php $admin->translate('Add Rule', 'w3speedster'); ?></button>
									</div>

								</div>
							</div>
						</div>
						<!-- <hr> -->
						<div class="js_box cdn_resources">
							<div class="w3d-flex gap20 w3align-item-baseline">
								<label><?php $admin->translate('Exclude Javascript Tags from Lazyload', 'w3speedster'); ?> <span
										class="info"></span><span
										class="info-display"><?php $admin->translate('Enter matching text of javascript url, which are to be excluded from javascript optimization. For each Exclusion, click on add rule.', 'w3speedster'); ?></span></label>
								<div class="input_box">
									<div class="single-row">
										<?php
										if (array_key_exists('exclude_javascript', $result)) {
											foreach (explode("\r\n", $result['exclude_javascript']) as $row) {
												if (!empty(trim($row))) {
													?>
													<div class="cdn_input_box minus w3d-flex">
														<input type="text" name="exclude_javascript[]"
															value="<?php echo $admin->esc_attr(trim($row)); ?>"
															placeholder="<?php $admin->translate('Please Enter matching text of the javascript here', 'w3speedster'); ?>"><button
															type="button" class="w3text-white rem-row w3bg-danger"><i
																class="fa fa-times"></i></button>
													</div>
													<?php
												}
											}
										} ?>
									</div>
									<div class="cdn_input_box plus">
										<button type="button" data-name="exclude_javascript"
											data-placeholder="<?php $admin->translate('Please Enter matching text of the javascript here', 'w3speedster'); ?>"
											class="btn small w3text-white w3bg-success add_more_row"><?php $admin->translate('Add Rule', 'w3speedster'); ?></button>
									</div>

								</div>
							</div>
						</div>
						<!-- <hr> -->
						<div class="js_box cdn_resources">
							<div class="w3d-flex gap20 w3align-item-baseline">
								<label><?php $admin->translate('Exclude Inline Javascript from Lazyload', 'w3speedster'); ?><span
										class="info"></span><span
										class="info-display"><?php $admin->translate('Enter matching text of inline script url, which needs to be excluded from deferring of javascript. For each Exclusion, click on add rule.', 'w3speedster'); ?></span></label>
								<div class="input_box">
									<div class="single-row">
										<?php
										if (array_key_exists('exclude_inner_javascript', $result)) {
											foreach (explode("\r\n", $result['exclude_inner_javascript']) as $row) {
												if (!empty(trim($row))) {
													?>
													<div class="cdn_input_box minus w3d-flex">
														<input type="text" name="exclude_inner_javascript[]"
															value="<?php echo $admin->esc_attr(trim($row)); ?>"
															placeholder="<?php $admin->translate('Please Enter matching text of the inline javascript here', 'w3speedster'); ?>"><button
															type="button" class="w3text-white rem-row w3bg-danger"><i
																class="fa fa-times"></i></button>
													</div>
													<?php
												}
											}
										} ?>
									</div>
									<div class="cdn_input_box plus">
										<button type="button" data-name="exclude_inner_javascript"
											data-placeholder="<?php $admin->translate('Please Enter matching text of the inline javascript here', 'w3speedster'); ?>"
											class="btn small w3text-white w3bg-success add_more_row"><?php $admin->translate('Add Rule', 'w3speedster'); ?></button>
									</div>

								</div>
							</div>
						</div>
						<!-- <hr> -->
					</details>
					</div>
					<hr>
					<div class="way_to_psi">
					<details>
						<summary>
							<h4 class="w3heading w3text-skyblue"><?php $admin->translate('Pages Exclusions', 'w3speedster'); ?></h4>
						</summary>
						<div class="cdn_resources <?php echo $hidden_class; ?>">
							<div class="w3d-flex gap20 html-cache-row">
								<label for="Preload Resources"><?php $admin->translate('Exclude pages from HTML caching', 'w3speedster'); ?><span
										class="info"></span><span
										class="info-display"><?php $admin->translate('Dont cache the url which match rule', 'w3speedster'); ?></span></label>
								<div class="input_box">
									<div class="single-row">
										<?php
										//$result['preload_resources'] = 'hello';
										if (array_key_exists('exclude_url_exclusions_html_cache', $result)) {
											foreach (explode("\r\n", $result['exclude_url_exclusions_html_cache']) as $row) {
												if (!empty(trim($row))) {
													?>
													<div class="cdn_input_box minus w3d-flex">
														<input type="text" name="exclude_url_exclusions_html_cache[]"
															value="<?php echo $admin->esc_attr(trim($row)); ?>"
															placeholder="<?php $admin->translate('Please Enter Url/String', 'w3speedster'); ?>"><button
															type="button" class="w3text-white rem-row w3bg-danger"><i
																class="fa fa-times"></i></button>
													</div>
													<?php
												}
											}
										} ?>
									</div>
									<div class="cdn_input_box plus">
										<button type="button" data-name="exclude_url_exclusions_html_cache"
											data-placeholder="<?php $admin->translate('Please Enter Url/String', 'w3speedster'); ?>"
											class="btn small w3text-white w3bg-success add_more_row"><?php $admin->translate('Add Rule', 'w3speedster'); ?></button>
									</div>

								</div>

							</div>
							<div class="w3d-flex gap20 w3align-item-baseline">
								<label
									for="Exclude Pages From Optimization"><?php $admin->translate('Exclude Pages From Optimization', 'w3speedster'); ?><span
										class="info"></span><span
										class="info-display"><?php $admin->translate('Enter slug of the url to exclude from optimization. For  eg. (/blog/). For home page, enter home url. For each Exclusion, click on add rule', 'w3speedster'); ?></span></label>
								<div class="input_box">
									<div class="single-row">
										<?php
										//$result['exclude_pages_from_optimization'] = 'hello';
										if (array_key_exists('exclude_pages_from_optimization', $result)) {
											foreach (explode("\r\n", $result['exclude_pages_from_optimization']) as $row) {
												if (!empty(trim($row))) {
													?>
													<div class="cdn_input_box minus w3d-flex">
														<input type="text" name="exclude_pages_from_optimization[]"
															value="<?php echo $admin->esc_attr(trim($row)); ?>"
															placeholder="<?php $admin->translate('Please Enter Page Url', 'w3speedster'); ?>"><button
															type="button" class="w3text-white rem-row w3bg-danger"><i
																class="fa fa-times"></i></button>
													</div>
													<?php
												}
											}
										}
										?>

									</div>
									<div class="cdn_input_box plus">
										<button type="button" data-name="exclude_pages_from_optimization"
											data-placeholder="<?php $admin->translate('Please Enter Page Url', 'w3speedster'); ?>"
											class="btn small w3text-white w3bg-success add_more_row"><?php $admin->translate('Add Rule', 'w3speedster'); ?></button>
									</div>

								</div>

							</div>
						</div>
						<div class="css_box cdn_resources">
						<div class="w3d-flex gap20 w3align-item-baseline">
							<label><?php $admin->translate('Exclude Pages from CSS Optimization', 'w3speedster'); ?> <span
									class="info"></span><span
									class="info-display"><?php $admin->translate('Enter slug of the page to exclude from css optimization', 'w3speedster'); ?></span></label>
							<div class="input_box">
								<div class="single-row">
									<?php
									if (array_key_exists('exclude_page_from_load_combined_css', $result)) {
										foreach (explode("\r\n", $result['exclude_page_from_load_combined_css']) as $row) {
											if (!empty(trim($row))) {
												?>
												<div class="cdn_input_box minus w3d-flex">
													<input type="text" name="exclude_page_from_load_combined_css[]"
														value="<?php echo $admin->esc_attr(trim($row)); ?>"
														placeholder="<?php $admin->translate('Please Enter Page Url', 'w3speedster'); ?>"><button
														type="button" class="w3text-white rem-row w3bg-danger"><i
															class="fa fa-times"></i></button>
												</div>
												<?php
											}
										}
									} ?>
								</div>
								<div class="cdn_input_box plus">
									<button type="button" data-name="exclude_page_from_load_combined_css"
										data-placeholder="<?php $admin->translate('Please Enter Page Url', 'w3speedster'); ?>"
										class="btn small w3text-white w3bg-success add_more_row"><?php $admin->translate('Add Rule', 'w3speedster'); ?></button>
								</div>
							</div>
						</div>
						</div>
						<div class="js_box cdn_resources">
							<div class="w3d-flex gap20 w3align-item-baseline">
								<label><?php $admin->translate('Exclude Pages from Javascript Optimization', 'w3speedster'); ?> <span
										class="info"></span><span
										class="info-display"><?php $admin->translate('Enter slug of the page to exclude from javascript optimization', 'w3speedster'); ?></span></label>
								<div class="input_box">
									<div class="single-row">
										<?php
										if (array_key_exists('exclude_page_from_load_combined_js', $result)) {
											foreach (explode("\r\n", $result['exclude_page_from_load_combined_js']) as $row) {
												if (!empty(trim($row))) {
													?>
													<div class="cdn_input_box minus w3d-flex">
														<input type="text" name="exclude_page_from_load_combined_js[]"
															value="<?php echo $admin->esc_attr(trim($row)); ?>"
															placeholder="<?php $admin->translate('Please Enter Js Page Url', 'w3speedster'); ?>"><button
															type="button" class="w3text-white rem-row w3bg-danger"><i
																class="fa fa-times"></i></button>
													</div>
													<?php
												}
											}
										} ?>
									</div>
									<div class="cdn_input_box plus">
										<button type="button" data-name="exclude_page_from_load_combined_js"
											data-placeholder="<?php $admin->translate('Please Enter Js Page Url', 'w3speedster'); ?>"
											class="btn small w3text-white w3bg-success add_more_row"><?php $admin->translate('Add Rule', 'w3speedster'); ?></button>
									</div>

								</div>
							</div>
						</div>
					</details>
					</div>
					<hr>
					<div class="single-hook_btn">
						<div class="save-changes w3d-flex gap10">
							<input type="button" value="Save Changes" class="btn hook_submit">
							<div class="in-progress w3d-flex save-changes-loader" style="display:none">
								<img src="<?php echo W3SPEEDSTER_URL; ?>assets/images/loader-gif.gif" alt="loader"
									class="loader-img">
							</div>
						</div>
					</div>
				</section>
			<section id="w3_custom_code" class="tab-pane fade">
				<div class="header w3d-flex gap20">
					<div class="heading_container">
						<h4 class="w3heading">
							<?php $admin->translate('Custom Code', 'w3speedster'); ?>
						</h4>
						<span class="info"><a
								href="https://w3speedster.com/w3speedster-documentation/"><?php $admin->translate('More info', 'w3speedster'); ?>?
							</a></span>
					</div>
					<div class="icon_container"> <img
							src="<?php echo W3SPEEDSTER_URL; ?>assets/images/custom-code-icon1.webp"></div>
				</div>
				<hr>
				<div class="css_box" id="css-box">
					<label><?php $admin->translate('Custom CSS to Load on Page Load', 'w3speedster'); ?> <span
							class="info"></span><span
							class="info-display"><?php $admin->translate('Enter custom css which works only when css optimization is applied', 'w3speedster'); ?></span></label>
					<div class="fullview">
						<textarea name="custom_css" rows="10" title="Custom css to load with preload css"
							placeholder="<?php $admin->translate('Please Enter css without the style tag.', 'w3speedster'); ?>"><?php if (!empty($result['custom_css']))
								   echo esc_html(stripslashes($result['custom_css'])); ?></textarea>
						<button id="btn" type="button" data-id="css-box" class="expend-textarea" title="Resize editor">
							<svg class="maximize" width="25" height="25" viewBox="0 0 26 26"
								xmlns="http://www.w3.org/2000/svg" transform="scale(1 -1)">
								<g data-name="Group 710">
									<path data-name="Path 1492"
										d="M24 26h-5v-2h5v-5h2v5a2.006 2.006 0 0 1-2 2m-4-4H8a1 1 0 0 1 0-2h12V6H6v11a1 1 0 0 1-2 0V6a2.006 2.006 0 0 1 2-2h14a2.006 2.006 0 0 1 2 2v14a2.006 2.006 0 0 1-2 2M2 2v5H0V2a2.006 2.006 0 0 1 2-2h5v2Z" />
								</g>
							</svg>
							<svg class="minimize" width="25" height="25" viewBox="5 5 26 26"
								xmlns="http://www.w3.org/2000/svg">
								<path d="M28 8H14a2 2 0 0 0-2 2v2h2v-2h14v10h-2v2h2a2 2 0 0 0 2-2V10a2 2 0 0 0-2-2"
									class="clr-i-outline clr-i-outline-path-1" />
								<path
									d="M22 14H8a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V16a2 2 0 0 0-2-2M8 26V16h14v10Z"
									class="clr-i-outline clr-i-outline-path-2" />
								<path fill="none" d="M0 0h36v36H0z" />
							</svg>
						</button>
					</div>
				</div>
				<hr>
				<div class="js_box" id="js-box">
					<label><?php $admin->translate('Custom Javascript to Load on Page Load', 'w3speedster'); ?> <span
							class="info"></span><span
							class="info-display"><?php $admin->translate('Enter javascript code which needs to be loaded before page load.', 'w3speedster'); ?></span></label>
					<div class="fullview">
						<textarea name="custom_javascript" rows="10" title="Custom "
							placeholder="<?php $admin->translate('Please javascript without script tag', 'w3speedster'); ?>"><?php if (!empty($result['custom_javascript']))
								   echo esc_html(stripslashes($result['custom_javascript'])); ?></textarea>
						<button id="btn" type="button" data-id="js-box" class="expend-textarea" title="Resize editor">
							<svg class="maximize" width="25" height="25" viewBox="0 0 26 26"
								xmlns="http://www.w3.org/2000/svg" transform="scale(1 -1)">
								<g data-name="Group 710">
									<path data-name="Path 1492"
										d="M24 26h-5v-2h5v-5h2v5a2.006 2.006 0 0 1-2 2m-4-4H8a1 1 0 0 1 0-2h12V6H6v11a1 1 0 0 1-2 0V6a2.006 2.006 0 0 1 2-2h14a2.006 2.006 0 0 1 2 2v14a2.006 2.006 0 0 1-2 2M2 2v5H0V2a2.006 2.006 0 0 1 2-2h5v2Z" />
								</g>
							</svg>
							<svg class="minimize" width="25" height="25" viewBox="5 5 26 26"
								xmlns="http://www.w3.org/2000/svg">
								<path d="M28 8H14a2 2 0 0 0-2 2v2h2v-2h14v10h-2v2h2a2 2 0 0 0 2-2V10a2 2 0 0 0-2-2"
									class="clr-i-outline clr-i-outline-path-1" />
								<path
									d="M22 14H8a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V16a2 2 0 0 0-2-2M8 26V16h14v10Z"
									class="clr-i-outline clr-i-outline-path-2" />
								<path fill="none" d="M0 0h36v36H0z" />
							</svg>
						</button>
					</div>
					<div class="w3d-flex gap20">
						<div class="w3d-flex ">
							<label for="load-as-file"><?php $admin->translate('Load as file', 'w3speedster'); ?> &nbsp;</label>
							<input type="checkbox" name="custom_javascript_file" <?php if (!empty($result['custom_javascript_file']) && $result['custom_javascript_file'] == "on")
								echo "checked"; ?> id="load-as-file">
						</div>
						&nbsp;
						<div class="w3d-flex ">
							<label for="defer"><?php $admin->translate('Defer', 'w3speedster'); ?> &nbsp;</label>
							<input type="checkbox" name="custom_javascript_defer" <?php if (!empty($result['custom_javascript_defer']) && $result['custom_javascript_defer'] == "on")
								echo "checked"; ?> id="defer">
						</div>
					</div>
				</div>
				<hr>
				<div class="js_box" id="custom-js-box">
					<label><?php $admin->translate('Custom Javascript to Load After Page Load', 'w3speedster'); ?> <span
							class="info"></span><span
							class="info-display"><?php $admin->translate('Enter javascript which loads after page load load.', 'w3speedster'); ?></span></label>
					<div class="fullview">
						<textarea name="custom_js" rows="10" title="Custom "
							placeholder="<?php $admin->translate('Please Enter Js without the script tag', 'w3speedster'); ?>"><?php if (!empty($result['custom_js']))
								   echo esc_html(stripslashes($result['custom_js'])); ?></textarea>
						<button id="btn" type="button" data-id="custom-js-box" class="expend-textarea"
							title="Resize editor">
							<svg class="maximize" width="25" height="25" viewBox="0 0 26 26"
								xmlns="http://www.w3.org/2000/svg" transform="scale(1 -1)">
								<g data-name="Group 710">
									<path data-name="Path 1492"
										d="M24 26h-5v-2h5v-5h2v5a2.006 2.006 0 0 1-2 2m-4-4H8a1 1 0 0 1 0-2h12V6H6v11a1 1 0 0 1-2 0V6a2.006 2.006 0 0 1 2-2h14a2.006 2.006 0 0 1 2 2v14a2.006 2.006 0 0 1-2 2M2 2v5H0V2a2.006 2.006 0 0 1 2-2h5v2Z" />
								</g>
							</svg>
							<svg class="minimize" width="25" height="25" viewBox="5 5 26 26"
								xmlns="http://www.w3.org/2000/svg">
								<path d="M28 8H14a2 2 0 0 0-2 2v2h2v-2h14v10h-2v2h2a2 2 0 0 0 2-2V10a2 2 0 0 0-2-2"
									class="clr-i-outline clr-i-outline-path-1" />
								<path
									d="M22 14H8a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V16a2 2 0 0 0-2-2M8 26V16h14v10Z"
									class="clr-i-outline clr-i-outline-path-2" />
								<path fill="none" d="M0 0h36v36H0z" />
							</svg>
						</button>
					</div>
				</div>
				<hr>
				<div class="save-changes w3d-flex gap10">
					<input type="button" value="<?php $admin->translate('Save Changes', 'w3speedster'); ?>"
						class="btn hook_submit">
					<div class="in-progress w3d-flex save-changes-loader" style="display:none">
						<img src="<?php echo W3SPEEDSTER_URL; ?>assets/images/loader-gif.gif" alt="loader"
							class="loader-img">
					</div>
				</div>
			</section>
			<section id="cache" class="tab-pane fade">
				<div class="header w3d-flex gap20">
					<div class="heading_container">
						<h4 class="w3heading">
							<?php $admin->translate('Cache', 'w3speedster'); ?>
						</h4>
						<span class="info"><a
								href="https://w3speedster.com/w3speedster-documentation/#Cache"><?php $admin->translate('More info', 'w3speedster'); ?>?
							</a></span>
					</div>
					<div class="icon_container"> <img
							src="<?php echo W3SPEEDSTER_URL; ?>assets/images/caches-icon.webp"></div>
				</div>
				<hr>
				<div class="caches_box">
					<div class="w3d-flex gap20 ">
						<label><?php $admin->translate('Delete HTML cache', 'w3speedster'); ?><span class="info"></span><span
								class="info-display"><?php $admin->translate('Delete HTML cache when you do any changes', 'w3speedster'); ?></span></label>
						<button class="btn" type="button" id="del_html_cache">
							<?php $admin->translate('Delete Now', 'w3speedster'); ?>
						</button>
						<div class="in-progress w3d-flex delete_html_cache" style="display:none">
							<img src="<?php echo W3SPEEDSTER_URL; ?>assets/images/loader-gif.gif" alt="loader"
								class="loader-img">
							<small
								class="extra-small m-0">&nbsp;<em>&nbsp;<?php $admin->translate('Deleting HTML Cache...', 'w3speedster'); ?></em></small>
						</div>
					</div>
					<div class="w3d-flex gap20 ">
						<label><?php $admin->translate('Delete JS/CSS Cache', 'w3speedster'); ?><span class="info"></span><span
								class="info-display"><?php $admin->translate('Delete javascript and css combined and minified files', 'w3speedster'); ?></span></label>
						<button class="btn" type="button" id="del_js_css_cache">
							<?php $admin->translate('Delete Now', 'w3speedster'); ?>
						</button>
						<div class="in-progress w3d-flex delete_css_js_cache" style="display:none">
							<img src="<?php echo W3SPEEDSTER_URL; ?>assets/images/loader-gif.gif" alt="loader"
								class="loader-img">
							<small
								class="extra-small m-0">&nbsp;<em>&nbsp;<?php $admin->translate('Deleting JS/Css Cache...', 'w3speedster'); ?></em></small>
						</div>
					</div>
					<div class="w3d-flex gap20 ">
						<label><?php $admin->translate('Delete critical css cache', 'w3speedster'); ?><span
								class="info"></span><span
								class="info-display"><?php $admin->translate('Delete critical css cache only when you have made any changes to style. This may take considerable amount of time to regenerate depending upon the pages on the site', 'w3speedster'); ?></span></label>
						<button class="btn" type="button" id="del_critical_css_cache">
							<?php $admin->translate('Delete Now', 'w3speedster'); ?>
						</button>
						<div class="in-progress w3d-flex delete_critical_css_cache" style="display:none">
							<img src="<?php echo W3SPEEDSTER_URL; ?>assets/images/loader-gif.gif" alt="loader"
								class="loader-img">
							<small
								class="extra-small m-0">&nbsp;<em>&nbsp;<?php $admin->translate('Deleting Critical Css Cache...', 'w3speedster'); ?></em></small>
						</div>
					</div>
				</div>
			</section>
			<section id="hooks" class="tab-pane fade">
				<div class="header w3d-flex gap20">
					<div class="heading_container">
						<h4 class="w3heading"><?php $admin->translate('Plugin Hooks', 'w3speedster'); ?></h4>
						<span class="info"><a
								href="https://w3speedster.com/w3speedster-documentation/#plugin-hooks"><?php $admin->translate('More info', 'w3speedster'); ?>?</a></span>
					</div>
					<div class="icon_container"> <img
							src="https://speedwp.webplus.me/wp-content/plugins/w3speedster-wp/assets/images/php-hook.webp">
					</div>
				</div>
				<div class="search_hooks">
					<input class="pl_search_field" autocomplete="off" name="temp_input" type="search"
						placeholder="<?php $admin->translate('Search...', 'w3speedster'); ?>" />
					<button type="button" class="clear_field" style="display:none">
						<svg width="25" height="25" viewBox="0 0 36 36" xmlns="http://www.w3.org/2000/svg">
							<path
								d="m19.41 18 8.29-8.29a1 1 0 0 0-1.41-1.41L18 16.59l-8.29-8.3a1 1 0 0 0-1.42 1.42l8.3 8.29-8.3 8.29A1 1 0 1 0 9.7 27.7l8.3-8.29 8.29 8.29a1 1 0 0 0 1.41-1.41Z" />
						</svg>
					</button>
					<p></p>
					<div class="entry_search_contaner" style="display:none"></div>
				</div>
				<div class="usedhooks">
					<h5 style="margin-top:22px;margin-bottom: 5px;">
						<strong><?php $admin->translate('Used Hooks', 'w3speedster'); ?></strong></h5>
					<?php
					$hooks = array(
						'hook_pre_start_opt' => 'W3speedster Pre Start Optimization',
						'hook_before_start_opt' => 'W3speedster Before Start Optimization',
						'hook_after_opt' => 'W3speedster After Optimization',
						'hook_inner_js_exclude' => 'W3speedster Inner JS Exclude',
						'hook_inner_js_customize' => 'W3speedster Inner JS Customize',
						'hook_internal_js_customize' => 'W3speedster Internal JS Customize',
						'hook_internal_css_customize' => 'W3speedster Internal Css Customize',
						'hook_internal_css_minify' => 'W3speedster Internal Css Minify',
						'hook_no_critical_css' => 'W3speedster No Critical Css',
						'hook_customize_critical_css' => 'W3speedster Customize Critical Css',
						'hook_disable_htaccess_webp' => 'W3speedster Disable Htaccess Webp',
						'hook_customize_add_settings' => 'W3speedster Customize Add Settings',
						'hook_customize_main_settings' => 'W3speedster Customize Main Settings',
						'hook_sep_critical_post_type' => 'W3speedster Seprate Critical Css For Post Type',
						'hook_sep_critical_cat' => 'W3speedster Seprate Critical Css For Category',
						'hook_video_to_videolazy' => 'W3speedster Change Video To Videolazy',
						'hook_iframe_to_iframelazy' => 'W3speedster Change Iframe To Iframlazy',
						'hook_exclude_image_to_lazyload' => 'W3speedster Exclude Image To Lazyload',
						'hook_customize_image' => 'W3speedster Customize Image',
						'hook_prevent_generation_htaccess' => 'W3speedster Prevent Htaccess Generation',
						'hook_exclude_css_filter' => 'W3speedster Exclude CSS Filter',
						'hook_customize_force_lazy_css' => 'W3speedster Customize Force Lazyload Css',
						'hook_external_javascript_customize' => 'W3speedster External Javascript Customize',
						'hook_external_javascript_filter' => 'W3speedster External Javascript Filter',
						'hook_customize_script_object' => 'W3speedster Customize Script Object',
						'hook_exclude_internal_js_w3_changes' => 'W3speedster Exclude Internal Js W3 Changes',
						'hook_exclude_page_optimization' => 'W3speedster Exclude Page Optimization',
						'hook_customize_critical_css_filename' => 'W3speedster Customize Critical Css File Name',
					);

					foreach ($hooks as $key => $hook) {
						if (isset($result[$key]) && !empty($result[$key])) {
							echo '<button type="button" data-label="' . $admin->esc_attr($hook) . '" data-filter="' . $admin->esc_attr(str_replace(' ', '', strtolower($hook))) . '" class="used_hook_btn btn">' . esc_html($hook) . '</button>';
						}
					}
					?>
				</div>
				<hr class="search-line">
				<div class="w3d-flex gap10 error-hook-main">
					<h3 class="error_hooks" style="display:none">
					</h3>
					<button type="button" class="error_hooks_close" style="">
						<svg width="25" height="25" viewBox="0 0 36 36" xmlns="http://www.w3.org/2000/svg">
							<path
								d="m19.41 18 8.29-8.29a1 1 0 0 0-1.41-1.41L18 16.59l-8.29-8.3a1 1 0 0 0-1.42 1.42l8.3 8.29-8.3 8.29A1 1 0 1 0 9.7 27.7l8.3-8.29 8.29 8.29a1 1 0 0 0 1.41-1.41Z">
							</path>
						</svg>
					</button>
				</div>
				<div class="all_hooks">
					<div class="single-hook">
						<label><span
								class="main-label"><?php $admin->translate('W3speedster Pre Start Optimization', 'w3speedster'); ?></span><span
								class="info"></span>
							<span class="info-display">
								<p><strong><?php $admin->translate('Function', 'w3speedster'); ?>:</strong>
									<?php $admin->translate('w3SpeedsterPreStartOptimization', 'w3speedster'); ?></p>
								<p><strong><?php $admin->translate('Description', 'w3speedster'); ?>:</strong>
									<?php $admin->translate('Modify page content pre optimization.', 'w3speedster'); ?></p>
								<p><strong><?php $admin->translate('Parameter:', 'w3speedster'); ?></strong>
									<?php $admin->translate('$html = Content visible in pages view source.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Return:', 'w3speedster'); ?></strong><?php $admin->translate(' 1 – Reflect the changes done in html of the page.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Example:', 'w3speedster'); ?></strong><br>
								<pre>
								function w3SpeedsterPreStartOptimization($html){
								$html = str_replace('Existing content','Changed content',$html);
								return $html;
								}
								</pre>
								</p>
							</span>
						</label>
						<code> function w3SpeedsterPreStartOptimization($html){</code>
						<textarea rows="5" cols="100" id="hook_pre_start_opt" name="hook_pre_start_opt"
							class="hook_before_start"><?php if (!empty($result['hook_pre_start_opt']))
								echo esc_html(stripslashes($result['hook_pre_start_opt'])); ?></textarea>
						<code> return $html; <br> }</code>
					</div>

					<div class="single-hook">
						<label><span
								class="main-label"><?php $admin->translate('W3speedster Before Start Optimization', 'w3speedster'); ?></span><span
								class="info"></span><span class="info-display">
								<p><?php $admin->translate('Function:', 'w3speedster'); ?>
									<?php $admin->translate('w3SpeedsterBeforeStartOptimization', 'w3speedster'); ?></p>
								<p><strong><?php $admin->translate('Description:', 'w3speedster'); ?></strong><?php $admin->translate(' W3Speedster allows you to make changes to the HTML on your site before actually starting the optimization. For instance replace or add in html.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Parameter:', 'w3speedster'); ?></strong><?php $admin->translate(' $html – full html of the page.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Return:', 'w3speedster'); ?></strong><?php $admin->translate(' 1 – Reflect the changes done in html of the page.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Example:', 'w3speedster'); ?></strong><br>
								<pre>
function w3SpeedsterBeforeStartOptimization($html){
$html = str_replace(array(""),array(""), $html);
return $html;
}
</pre>
								</p>
							</span></label>
						<code> function w3SpeedsterBeforeStartOptimization($html){</code>
						<textarea rows="5" cols="100" id="hook_before_start_opt" name="hook_before_start_opt"
							class="hook_before_start"><?php if (!empty($result['hook_before_start_opt']))
								echo esc_html(stripslashes($result['hook_before_start_opt'])); ?></textarea>
						<code> return $html;<br> }</code>
					</div>

					<div class="single-hook">
						<label><span
								class="main-label"><?php $admin->translate('W3speedster After Optimization', 'w3speedster'); ?></span>
							<span class="info"></span>
							<span class="info-display">
								<p><?php $admin->translate('Function:', 'w3speedster'); ?>
									<?php $admin->translate('w3SpeedsterAfterOptimization', 'w3speedster'); ?></p>
								<p><strong><?php $admin->translate('Description:', 'w3speedster'); ?></strong><?php $admin->translate(' W3Speedster allows you to make changes to the HTML on your site after the page is optimized by the plugin. For instance replace or add in html.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Parameter:', 'w3speedster'); ?></strong><?php $admin->translate('$html – full html of the page.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Return:', 'w3speedster'); ?></strong><?php $admin->translate(' 1 – Reflect the changes done in html of the page.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Example:', 'w3speedster'); ?></strong><br>
								<pre>
function w3SpeedsterAfterOptimization($html){
$html = str_replace(array(image.png''),array(image-100x100.png''), $html);
return $html;
} 
</pre>
								</p>
							</span>
						</label>
						<code> function w3SpeedsterAfterOptimization($html){</code>
						<textarea rows="5" cols="100" id="hook_after_opt" name="hook_after_opt"
							class="hook_before_start"><?php if (!empty($result['hook_after_opt']))
								echo esc_html(stripslashes($result['hook_after_opt'])); ?></textarea>
						<code> return $html; <br>}</code>
					</div>

					<div class="single-hook">
						<label><span
								class="main-label"><?php $admin->translate('W3speedster Inner JS Customize', 'w3speedster'); ?></span>
							<span class="info"></span>
							<span class="info-display">
								<p><?php $admin->translate('Function:', 'w3speedster'); ?>
									<?php $admin->translate('w3SpeedsterInnerJsCustomize', 'w3speedster'); ?></p>
								<p><strong><?php $admin->translate('Description:', 'w3speedster'); ?></strong><?php $admin->translate(' If you want to make changes in your inline JavaScript, W3Speedster allows you to make changes in Inline JavaScript (for instance making changes in inline script you have to enter the unique text from the script to identify the script).', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Parameter:', 'w3speedster'); ?></strong><?php $admin->translate('$script_text- The content of the script.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Return:', 'w3speedster'); ?></strong><?php $admin->translate(' $script_text – Content of the script after changes.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Example:', 'w3speedster'); ?></strong><br>
								<pre>
function w3SpeedsterInnerJsCustomize($script_text){
	if(strpos($script_text'//unique word from script//') !== false){
		$script_text = str_replace(''jQuery(window) ', 'jQuery(document)',$script_text);
	}
	return $script_text;
}
</pre>
								</p>
							</span>
						</label>
						<code> function w3SpeedsterInnerJsCustomize($script_text){</code>
						<textarea rows="5" cols="100" id="hook_inner_js_customize" name="hook_inner_js_customize"
							class="hook_before_start"><?php if (!empty($result['hook_inner_js_customize']))
								echo esc_html(stripslashes($result['hook_inner_js_customize'])); ?></textarea>
						<code> return $script_text;<br> }</code>
					</div>

					<div class="single-hook">
						<label><span
								class="main-label"><?php $admin->translate('W3speedster Inner JS Exclude', 'w3speedster'); ?></span><span
								class="info"></span>
							<span class="info-display">
								<p><?php $admin->translate('Function:', 'w3speedster'); ?>
									<?php $admin->translate('w3SpeedsterInnerJsExclude', 'w3speedster'); ?></p>
								<p><strong><?php $admin->translate('Description:', 'w3speedster'); ?></strong><?php $admin->translate(' Exclude the script tag from lazy loading, which is present in the pages view source. ', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Parameter:', 'w3speedster'); ?></strong>
									<br><?php $admin->translate('$inner_js = The script tag s content is visible in the page s view source <br> $exclude_js_bool = 0(default) || 1 ', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Return:', 'w3speedster'); ?></strong>
									<?php $admin->translate('1', 'w3speedster'); ?> </p>
								<p><strong><?php $admin->translate('Example:', 'w3speedster'); ?></strong><br>
								<pre>
function w3SpeedsterInnerJsExclude($exclude_js_bool,$inner_js){
	if(strpos($inner_js,'Script text') !== false){
		$exclude_js_bool= 1;
	}
return $exclude_js_bool;
}
</pre>
								</p>
							</span>
						</label>
						<code>function w3SpeedsterInnerJsExclude($exclude_js_bool,$inner_js){</code>
						<textarea rows="5" cols="100" id="hook_inner_js_exclude" name="hook_inner_js_exclude"
							class="hook_before_start"><?php if (!empty($result['hook_inner_js_exclude']))
								echo esc_html(stripslashes($result['hook_inner_js_exclude'])); ?></textarea>
						<code> return $exclude_js_bool; <br>}</code>
					</div>

					<div class="single-hook">
						<label><span
								class="main-label"><?php $admin->translate('W3speedster Internal JS Customize', 'w3speedster'); ?></span><span
								class="info"></span>
							<span class="info-display">
								<p><?php $admin->translate('Function:', 'w3speedster'); ?>
									<?php $admin->translate('w3SpeedsterInternalJsCustomize', 'w3speedster'); ?></p>
								<p><strong><?php $admin->translate('Description:', 'w3speedster'); ?></strong><?php $admin->translate(' If you wish to make changes in JavaScript files, W3Speedster allows you to make changes in JavaScript Files.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Parameter:', 'w3speedster'); ?></strong>
									<?php $admin->translate('$path- Path of the JS file.', 'w3speedster'); ?><br>
									<?php $admin->translate('$string – javascript you want to make changes in.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Return:', 'w3speedster'); ?></strong><?php $admin->translate(' $string– make changes in the internal JS file.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Example:', 'w3speedster'); ?></strong><br>
								<pre>
function w3SpeedsterInternalJsCustomize($string,$path){
	if(strpos($path,'//js path//') !== false){
	$string = str_replace("jQuery(windw)", "jQuery(window)",$string);
	}
	return $string;
}
</pre>
								</p>
							</span>
						</label>
						<code>function w3SpeedsterInternalJsCustomize($string,$path){</code>
						<textarea rows="5" cols="100" id="hook_internal_js_customize" name="hook_internal_js_customize"
							class="hook_before_start"><?php if (!empty($result['hook_internal_js_customize']))
								echo esc_html(stripslashes($result['hook_internal_js_customize'])); ?></textarea>
						<code> return $string; <br>}</code>
					</div>

					<div class="single-hook">
						<label><span
								class="main-label"><?php $admin->translate('W3speedster Internal Css Customize', 'w3speedster'); ?></span>
							<span class="info"></span>
							<span class="info-display">
								<p><?php $admin->translate('Function:', 'w3speedster'); ?>
									<?php $admin->translate('w3SpeedsterInternalCssCustomize', 'w3speedster'); ?></p>
								<p><strong><?php $admin->translate('Description:', 'w3speedster'); ?></strong><?php $admin->translate(' If you want to make changes in your CSS file, W3Speedster allows you to make changes in stylesheet files.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Parameter:', 'w3speedster'); ?></strong>
									<?php $admin->translate('$css- Css content of the file.', 'w3speedster'); ?><br>
									<?php $admin->translate('$path- path of css file.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Return:', 'w3speedster'); ?></strong><?php $admin->translate(' $css – make the required changes in CSS files.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Example:', 'w3speedster'); ?></strong><br>
								<pre>
function w3SpeedsterInternalCssCustomize($css,$path){
	if(strpos($path,' //cssPath // ') !== false){
		$css = str_replace(' ',' ',$css);
	}
	return $css;
}
</pre>
								</p>
							</span>
						</label>
						<code>function w3SpeedsterInternalCssCustomize($css,$path){</code>
						<textarea rows="5" cols="100" id="hook_internal_css_customize"
							name="hook_internal_css_customize" class="hook_before_start"><?php if (!empty($result['hook_internal_css_customize']))
								echo esc_html(stripslashes($result['hook_internal_css_customize'])); ?></textarea>
						<code> return $css; <br>}</code>
					</div>

					<div class="single-hook">
						<label><span
								class="main-label"><?php $admin->translate('W3speedster Internal Css Minify', 'w3speedster'); ?></span><span
								class="info"></span> <span class="info-display">
								<p><?php $admin->translate('Function:', 'w3speedster'); ?>
									<?php $admin->translate('W3speedup_internal_css_minify', 'w3speedster'); ?></p>
								<p><strong><?php $admin->translate('Description:', 'w3speedster'); ?></strong><?php $admin->translate(' If you don’t want to minify, W3Speedster allows you to exclude stylesheet files from minify.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Parameter:', 'w3speedster'); ?></strong>
									<?php $admin->translate('$path- path of css file.<br>$css- Css content of the file. ', 'w3speedster'); ?><br>
									<?php $admin->translate('$css_minify- 0 || 1 (default)', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Return:', 'w3speedster'); ?></strong><?php $admin->translate(' 1 – it will exclude the entered css file from minification.', 'w3speedster'); ?><br><?php $admin->translate(' 0 – it will not exclude the entered css file from minification.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Example:', 'w3speedster'); ?></strong><br>
								<pre>
function w3SpeedsterInternalCssMinify($path,$css,$css_minify){
if(strpos($path,'//cssPath//') !== false){
	$css_minify = 0;
}
return $css_minify ;
}
</pre>
								</p>
							</span>
						</label>
						<code>function w3SpeedsterInternalCssMinify($path,$css,$css_minify){</code>
						<textarea rows="5" cols="100" id="hook_internal_css_minify" name="hook_internal_css_minify"
							class="hook_before_start"><?php if (!empty($result['hook_internal_css_minify']))
								echo esc_html(stripslashes($result['hook_internal_css_minify'])); ?></textarea>
						<code> return $css_minify; <br>}</code>
					</div>

					<div class="single-hook">
						<label><span
								class="main-label"><?php $admin->translate('W3speedster No Critical Css', 'w3speedster'); ?></span>
							<span class="info"></span>
							<span class="info-display">
								<p><?php $admin->translate('Function:', 'w3speedster'); ?>
									<?php $admin->translate('w3SpeedsterNoCriticalCss', 'w3speedster'); ?></p>
								<p><strong><?php $admin->translate('Description:', 'w3speedster'); ?></strong><?php $admin->translate(' W3Speedster allows you to exclude the pages from the Critical CSS (like search pages).', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Parameter:', 'w3speedster'); ?></strong>
									<?php $admin->translate('$url- Stores the url of the page. ', 'w3speedster'); ?><br>
									<?php $admin->translate('$ignore_critical_css- 0 (default) || 1 ', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Return:', 'w3speedster'); ?></strong><?php $admin->translate(' 1 – it will exclude the page you do not wish to create critical CSS.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Example:', 'w3speedster'); ?></strong><br>
								<pre>
function w3SpeedsterNoCriticalCss($url, $ignore_critical_css){
	if(strpos($url,'/path/') !==false) {
		$ignore_critical_css = 1;
	}	
	return $ignore_critical_css;
}
</pre>
								</p>
							</span>
						</label>
						<code>function w3SpeedsterNoCriticalCss($url,$ignore_critical_css){</code>
						<textarea rows="5" cols="100" id="hook_no_critical_css" name="hook_no_critical_css"
							class="hook_before_start"><?php if (!empty($result['hook_no_critical_css']))
								echo esc_html(stripslashes($result['hook_no_critical_css'])); ?></textarea>
						<code> return $ignore_critical_css; <br>}</code>
					</div>

					<div class="single-hook">
						<label><span
								class="main-label"><?php $admin->translate('W3speedster Customize Critical Css', 'w3speedster'); ?></span><span
								class="info"></span>
							<span class="info-display">
								<p><?php $admin->translate('Function:', 'w3speedster'); ?>
									<?php $admin->translate('w3SpeedsterCustomizeCriticalCss', 'w3speedster'); ?></p>
								<p><strong><?php $admin->translate('Description:', 'w3speedster'); ?></strong><?php $admin->translate(' If you wish to make any changes in Critical CSS, W3Speedster allows you to make changes in generated Critical CSS. For instance if you want to replace/ remove any string/URL from critical CSS (like @font-face { font-family:”Courgette”; to @font-face { ).', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Parameter:', 'w3speedster'); ?></strong>
									<?php $admin->translate('$critical_css- Critical Css of the page.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Return:', 'w3speedster'); ?></strong><?php $admin->translate('$critical_css – Reflect the changes made in critical css.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Example:', 'w3speedster'); ?></strong><br>
								<pre>
function w3SpeedsterCustomizeCriticalCss($critical_css){
	$critical_css = str_replace('@font-face { font-family:"Courgette";', ' ',$critical_css);
	return $critical_css;
}
</pre>
								</p>
							</span>
						</label>
						<code>function w3SpeedsterCustomizeCriticalCss($critical_css){</code>
						<textarea rows="5" cols="100" id="hook_customize_critical_css"
							name="hook_customize_critical_css" class="hook_before_start"><?php if (!empty($result['hook_customize_critical_css']))
								echo esc_html(stripslashes($result['hook_customize_critical_css'])); ?></textarea>
						<code> return $critical_css; <br>}</code>
					</div>

					<div class="single-hook">
						<label><span
								class="main-label"><?php $admin->translate('W3speedster Disable Htaccess Webp', 'w3speedster'); ?></span><span
								class="info"></span><span class="info-display">
								<p><?php $admin->translate('Function:', 'w3speedster'); ?>
									<?php $admin->translate('w3SpeedsterDisableHtaccessWebp', 'w3speedster'); ?>.</p>
								<p><strong><?php $admin->translate('Description:', 'w3speedster'); ?></strong><?php $admin->translate(' Our plugin converts .jpg/.png format to WebP format without changing the URL. it disable webp to render from HTACCESS.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Parameter:', 'w3speedster'); ?></strong><?php $admin->translate('$disable_htaccess_webp- 0(default) || 1', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Return:', 'w3speedster'); ?></strong><?php $admin->translate('1 – It will add w3.webp at the end of the url for instance (xyz.jpgw3.webp).', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Example:', 'w3speedster'); ?></strong><br>
								<pre>
function w3SpeedsterDisableHtaccessWebp($disable_htaccess_webp){
	$disable_htaccess_webp = 1
return $disable_htaccess_webp;
}
</pre>
								</p>
							</span>
						</label>
						<code>function w3SpeedsterDisableHtaccessWebp($disable_htaccess_webp){</code>
						<textarea rows="5" cols="100" id="hook_disable_htaccess_webp" name="hook_disable_htaccess_webp"
							class="hook_before_start"><?php if (!empty($result['hook_disable_htaccess_webp']))
								echo esc_html(stripslashes($result['hook_disable_htaccess_webp'])); ?></textarea>
						<code> return $disable_htaccess_webp; <br>}</code>
					</div>

					<div class="single-hook">
						<label><span
								class="main-label"><?php $admin->translate('W3speedster Customize Add Settings', 'w3speedster'); ?></span><span
								class="info"></span> <span class="info-display">
								<p><?php $admin->translate('Function:', 'w3speedster'); ?>
									<?php $admin->translate('w3SpeedsterCustomizeAddSettings', 'w3speedster'); ?></p>
								<p><strong><?php $admin->translate('Description:', 'w3speedster'); ?></strong><?php $admin->translate(' If you wish to change in variables and paths (URL), W3Speedster allows you to make changes in variables and paths with the help of this plugin function.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Parameter:', 'w3speedster'); ?></strong><?php $admin->translate('$add_settings- settings of the plugin.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Return:', 'w3speedster'); ?></strong><?php $admin->translate('$add_settings – reflect the changes made in variable and path.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Example:', 'w3speedster'); ?></strong><br>
								<pre>
function w3SpeedsterCustomizeAddSettings($add_settings){
$add_settings = str_replace(array(“mob.css”),array(“mobile.css”), $add_settings);
	return $add_settings;
}
</pre>
								</p>
							</span>
						</label>
						<code>function w3SpeedsterCustomizeAddSettings($add_settings){</code>
						<textarea rows="5" cols="100" id="hook_customize_add_settings"
							name="hook_customize_add_settings" class="hook_before_start"><?php if (!empty($result['hook_customize_add_settings']))
								echo esc_html(stripslashes($result['hook_customize_add_settings'])); ?></textarea>
						<code> return $add_settings; <br>}</code>
					</div>

					<div class="single-hook">
						<label><span
								class="main-label"><?php $admin->translate('W3speedster Customize Main Settings', 'w3speedster'); ?></span><span
								class="info"></span>
							<span class="info-display">
								<p><?php $admin->translate('Function:', 'w3speedster'); ?>
									<?php $admin->translate('w3SpeedsterCustomizeMainSettings', 'w3speedster'); ?></p>
								<p><strong><?php $admin->translate('Description:', 'w3speedster'); ?></strong><?php $admin->translate(' Customize plugin main settings.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Parameter:', 'w3speedster'); ?></strong><?php $admin->translate(' $settings- Plugin main settings array (like: exclude css, cache path etc ) ', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Return:', 'w3speedster'); ?></strong><?php $admin->translate(' $settings', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Example:', 'w3speedster'); ?></strong><br>
								<pre>
function w3SpeedsterCustomizeMainSettings($settings){
	$settings['setting_name'] = value;
return $settings;
}
</pre>
								</p>
							</span>
						</label>
						<code>function w3SpeedsterCustomizeMainSettings($settings){</code>
						<textarea rows="5" cols="100" id="hook_customize_main_settings"
							name="hook_customize_main_settings" class="hook_before_start"><?php if (!empty($result['hook_customize_main_settings']))
								echo esc_html(stripslashes($result['hook_customize_main_settings'])); ?></textarea>
						<code> return $settings; <br>}</code>
					</div>

					<div class="single-hook">
						<label><span
								class="main-label"><?php $admin->translate('W3speedster Seprate Critical Css For Post Type', 'w3speedster'); ?></span><span
								class="info"></span>
							<span class="info-display">
								<p><?php $admin->translate('Function:', 'w3speedster'); ?>
									<?php $admin->translate('w3SpeedsterCreateSeprateCssOfPostType', 'w3speedster'); ?></p>
								<p><strong><?php $admin->translate('Description:', 'w3speedster'); ?></strong><?php $admin->translate(' By default our plugin creates a single critical css for post but If you wish to generate separate critical CSS for post. W3Speedster allows you to create critical CSS separately post-wise.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Parameter:', 'w3speedster'); ?></strong><?php $admin->translate('$separate_post_css- Array of post types. ', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Return:', 'w3speedster'); ?></strong><?php $admin->translate('$separate_post_css – create separate critical css for each post and page.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Example:', 'w3speedster'); ?></strong><br>
								<pre>
function w3SpeedsterCreateSeprateCssOfPostType($separate_post_css){
	$separate_post_css = array('page','post','product');
	return $separate_post_css;
}
</pre>
								</p>
							</span>
						</label>
						<code>function w3SpeedsterCreateSeprateCssOfPostType($separate_post_css){</code>
						<textarea rows="5" cols="100" id="hook_sep_critical_post_type"
							name="hook_sep_critical_post_type" class="hook_before_start"><?php if (!empty($result['hook_sep_critical_post_type']))
								echo esc_html(stripslashes($result['hook_sep_critical_post_type'])); ?></textarea>
						<code> return $separate_post_css; <br>}</code>
					</div>

					<div class="single-hook">
						<label><span
								class="main-label"><?php $admin->translate('W3speedster Seprate Critical Css For Category', 'w3speedster'); ?></span><span
								class="info"></span>
							<span class="info-display">
								<p><?php $admin->translate('Function:', 'w3speedster'); ?>
									<?php $admin->translate('W3speedsterCriticalCssOfCategory', 'w3speedster'); ?></p>
								<p><strong><?php $admin->translate('Description:', 'w3speedster'); ?></strong>
									<?php $admin->translate('W3Speedster Create seprate critical css for  categories pages.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Parameter:', 'w3speedster'); ?></strong><?php $admin->translate('$separate_cat_css- Array of Category.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Return:', 'w3speedster'); ?></strong>
									<?php $admin->translate('$separate_cat_css – create separate critical css for each category and tag.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Example:', 'w3speedster'); ?></strong><br>
								<pre>
function W3speedsterCriticalCssOfCategory($separate_cat_css){
	$separate_cat_css = array('category','tag','custom-category');
   return $separate_cat_css;
}
</pre>
								</p>
							</span>
						</label>
						<code>function W3speedsterCriticalCssOfCategory($separate_cat_css){</code>
						<textarea rows="5" cols="100" id="hook_sep_critical_cat" name="hook_sep_critical_cat"
							class="hook_before_start"><?php if (!empty($result['hook_sep_critical_cat']))
								echo esc_html(stripslashes($result['hook_sep_critical_cat'])); ?></textarea>
						<code> return $separate_cat_css; <br>}</code>
					</div>

					<div class="single-hook">
						<label><span
								class="main-label"><?php $admin->translate('W3speedster Change Video To Videolazy', 'w3speedster'); ?></span><span
								class="info"></span>
							<span class="info-display">
								<p><?php $admin->translate('Function:', 'w3speedster'); ?>
									<?php $admin->translate('w3SpeedsterVideoToVideoLazy', 'w3speedster'); ?></p>
								<p><strong><?php $admin->translate('Description:', 'w3speedster'); ?></strong>
									<?php $admin->translate('Change video tag to videolazy tag', 'w3speedster'); ?></p>
								<p><strong><?php $admin->translate('Parameter:', 'w3speedster'); ?></strong>
									<?php $admin->translate('$videolazy- 0(default) || 1', 'w3speedster'); ?> </p>
								<p><strong><?php $admin->translate('Return:', 'w3speedster'); ?></strong><?php $admin->translate(' 1 - Change video tag to videolazy tag.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Example:', 'w3speedster'); ?></strong><br>
								<pre>
function w3SpeedsterVideoToVideoLazy($videolazy){
	$videolazy= 1;
	return $videolazy;
}
</pre>
								</p>
							</span>
						</label>
						<code>function w3SpeedsterVideoToVideoLazy($videolazy){</code>
						<textarea rows="5" cols="100" id="hook_video_to_videolazy" name="hook_video_to_videolazy"
							class="hook_before_start"><?php if (!empty($result['hook_video_to_videolazy']))
								echo esc_html(stripslashes($result['hook_video_to_videolazy'])); ?></textarea>
						<code> return $videolazy; <br>}</code>
					</div>

					<div class="single-hook">
						<label><span
								class="main-label"><?php $admin->translate('W3speedster Change Iframe To Iframlazy', 'w3speedster'); ?></span><span
								class="info"></span>
							<span class="info-display">
								<p><?php $admin->translate('Function:', 'w3speedster'); ?>
									<?php $admin->translate('w3SpeedsterIframetoIframelazy', 'w3speedster'); ?></p>
								<p><strong><?php $admin->translate('Description:', 'w3speedster'); ?></strong><?php $admin->translate(' Change iframe tag to iframlazy tag.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Parameter:', 'w3speedster'); ?></strong><?php $admin->translate(' $iframelazy- 0(default) || 1', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Return:', 'w3speedster'); ?></strong><?php $admin->translate(' 1 - Change iframe tag to iframlazy tag.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Example:', 'w3speedster'); ?></strong><br>
								<pre>
function w3SpeedsterIframetoIframelazy($iframelazy){
	$iframelazy = 1;
	return $iframelazy;
}
</pre>
								</p>
							</span>
						</label>
						<code>function w3SpeedsterIframetoIframelazy($iframelazy){</code>
						<textarea rows="5" cols="100" id="hook_iframe_to_iframelazy" name="hook_iframe_to_iframelazy"
							class="hook_before_start"><?php if (!empty($result['hook_iframe_to_iframelazy']))
								echo esc_html(stripslashes($result['hook_iframe_to_iframelazy'])); ?></textarea>
						<code> return $iframelazy; <br>}</code>
					</div>

					<div class="single-hook">
						<label><span
								class="main-label"><?php $admin->translate('W3speedster Exclude Image To Lazyload', 'w3speedster'); ?></span><span
								class="info"></span>
							<span class="info-display">
								<p><?php $admin->translate('Function:', 'w3speedster'); ?>
									<?php $admin->translate('w3SpeedsterExcludeImageToLazyload', 'w3speedster'); ?></p>
								<p><strong><?php $admin->translate('Description:', 'w3speedster'); ?></strong>
									<?php $admin->translate('W3Speedster allows you to exclude the images from optimization dynamically which you don’t want to lazyload.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Parameter:', 'w3speedster'); ?></strong><?php $admin->translate('$exclude_image = 0(default) || 1 <br>$img = Image tag with all attributes<br>$imgnn_arr = Image tag ', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Return:', 'w3speedster'); ?></strong>
									<?php $admin->translate('1 – it will lazy load the image.', 'w3speedster'); ?><br>
									<?php $admin->translate('0 – it will not lazy load the image.', 'w3speedster'); ?></p>
								<p><strong><?php $admin->translate('Example:', 'w3speedster'); ?></strong><br>
								<pre>
function w3SpeedsterExcludeImageToLazyload($exclude_image,$img, $imgnn_arr){
   if(!empty($img) && strpos($img,'logo.png') !== false){
	   $exclude_image = 1
   }
   return $exclude_image;
}
</pre>
								</p>
							</span>
						</label>
						<code>function w3SpeedsterExcludeImageToLazyload($exclude_image,$img, $imgnn_arr){</code>
						<textarea rows="5" cols="100" id="hook_exclude_image_to_lazyload"
							name="hook_exclude_image_to_lazyload" class="hook_before_start"><?php if (!empty($result['hook_exclude_image_to_lazyload']))
								echo esc_html(stripslashes($result['hook_exclude_image_to_lazyload'])); ?></textarea>
						<code> return $exclude_image; <br>}</code>
					</div>

					<div class="single-hook">
						<label><span
								class="main-label"><?php $admin->translate('W3speedster Customize Image', 'w3speedster'); ?></span><span
								class="info"></span>
							<span class="info-display">
								<p><?php $admin->translate('Function:', 'w3speedster'); ?>
									<?php $admin->translate('w3SpeedsterCustomizeImage', 'w3speedster'); ?></p>
								<p><strong><?php $admin->translate('Description:', 'w3speedster'); ?></strong><?php $admin->translate(' Customize image tags.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Parameter:', 'w3speedster'); ?></strong><?php $admin->translate(' $img = Image tag with all attributes <br>$imgnn = Modified image tag by plugin <br>$imgnn_arr = Image tag attributes array', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Return:', 'w3speedster'); ?></strong><?php $admin->translate(' $imgnn- Customized image tags ', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Example:', 'w3speedster'); ?></strong><br>
								<pre>
function w3SpeedsterCustomizeImage($imgnn,$img,$imgnn_arr){
	if(strpos($imgnn,'alt') != false){
		$imgnn = str_replace('alt=""','alt="value"',$imgnn);
	}
	return $imgnn;
}
</pre>
								</p>
							</span>
						</label>
						<code>function w3SpeedsterCustomizeImage($imgnn,$img,$imgnn_arr){</code>
						<textarea rows="5" cols="100" id="hook_customize_image" name="hook_customize_image"
							class="hook_before_start"><?php if (!empty($result['hook_customize_image']))
								echo esc_html(stripslashes($result['hook_customize_image'])); ?></textarea>
						<code> return $imgnn; <br>}</code>
					</div>

					<div class="single-hook">
						<label><span
								class="main-label"><?php $admin->translate('W3speedster Prevent Htaccess Generation', 'w3speedster'); ?></span><span
								class="info"></span>
							<span class="info-display">
								<p><?php $admin->translate('Function:', 'w3speedster'); ?>
									<?php $admin->translate('w3SpeedsterPreventHtaccessGeneration', 'w3speedster'); ?>.</p>
								<p><strong><?php $admin->translate('Description:', 'w3speedster'); ?></strong><?php $admin->translate('  Our plugin converts .jpg/.png format to WebP format without changing the URL. it disable webp to render from HTACCESS.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Parameter:', 'w3speedster'); ?></strong><?php $admin->translate('$preventHtaccess = 0(default) || 1 ', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Return:', 'w3speedster'); ?></strong><?php $admin->translate(' 1 – It will add w3.webp at the end of the url for instance (xyz.jpgw3.webp).', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Example:', 'w3speedster'); ?></strong><br>
								<pre>
function w3SpeedsterPreventHtaccessGeneration($preventHtaccess){
	$preventHtaccess = 1;
   return $preventHtaccess;
}
</pre>
								</p>
							</span>
						</label>
						<code>function w3SpeedsterPreventHtaccessGeneration($preventHtaccess){</code>
						<textarea rows="5" cols="100" id="hook_prevent_generation_htaccess"
							name="hook_prevent_generation_htaccess" class="hook_before_start"><?php if (!empty($result['hook_prevent_generation_htaccess']))
								echo esc_html(stripslashes($result['hook_prevent_generation_htaccess'])); ?></textarea>
						<code> return $preventHtaccess; <br>}</code>
					</div>

					<div class="single-hook">
						<label><span
								class="main-label"><?php $admin->translate('W3speedster Exclude CSS Filter', 'w3speedster'); ?></span><span
								class="info"></span>
							<span class="info-display">
								<p><?php $admin->translate('Function:', 'w3speedster'); ?>
									<?php $admin->translate('W3SpeedsterExcludeCssFilter', 'w3speedster'); ?></p>
								<p><strong><?php $admin->translate('Description:', 'w3speedster'); ?></strong><?php $admin->translate(' If you want to dynamically exclude a CSS file from optimization, W3Speedster allows you to exclude it from optimization (like style.css).', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Parameter:', 'w3speedster'); ?></strong><?php $admin->translate(' $exclude_css – 0(default) || 1', 'w3speedster'); ?><br>
									<?php $admin->translate('$css_obj – link tag in object format.', 'w3speedster'); ?><br>
									<?php $admin->translate('$css – Content of the CSS file you want to make changes in.', 'w3speedster'); ?><br>
									<?php $admin->translate('$html – content of the webpage.', 'w3speedster'); ?></p>
								<p><strong><?php $admin->translate('Return:', 'w3speedster'); ?></strong><?php $admin->translate(' $exclude_css – exclude CSS from optimization.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Example:', 'w3speedster'); ?></strong><br>
								<pre>
function W3SpeedsterExcludeCssFilter($exclude_css,$css_obj,$css,$html){
	if(wp_is_mobile()){
		if(strpos($css,'style.css') !== false){
			$exclude_css = 1 ;
		}
	}
	return $exclude_css;
}
</pre>
								</p>
							</span>
						</label>
						<code>function W3SpeedsterExcludeCssFilter($exclude_css,$css_obj,$css,$html){</code>
						<textarea rows="5" cols="100" id="hook_exclude_css_filter" name="hook_exclude_css_filter"
							class="hook_before_start"><?php if (!empty($result['hook_exclude_css_filter']))
								echo esc_html(stripslashes($result['hook_exclude_css_filter'])); ?></textarea>
						<code> return $exclude_css; <br>}</code>
					</div>
					<hr>
					<div class="single-hook">
						<label><span
								class="main-label"><?php $admin->translate('W3speedster Customize Force Lazyload Css', 'w3speedster'); ?></span><span
								class="info"></span>
							<span class="info-display">
								<p><?php $admin->translate('Function:', 'w3speedster'); ?><?php $admin->translate('w3SpeedsterCustomizeForceLazyCss', 'w3speedster'); ?>.
								</p>
								<p><strong><?php $admin->translate('Description:', 'w3speedster'); ?></strong>
									<?php $admin->translate(' If you wish to Force Lazyload CSS files dynamically for a specific page or pages, you can do so with the W3Speedster, it allows you to dynamically force lazyload stylesheet files (for instance font file like awesome, dashicons and css files).', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Parameter:', 'w3speedster'); ?></strong><?php $admin->translate(' $force_lazyload_css – Array containing text to force lazyload which you have mentioned in the plugin configuration.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Return:', 'w3speedster'); ?></strong><?php $admin->translate(' $force_lazyload_css – Array containing text to force lazyload.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Example:', 'w3speedster'); ?></strong><br>
								<pre>
function w3SpeedsterCustomizeForceLazyCss($force_lazyload_css){
   array_push($force_lazyload_css ,'/fire-css');
   return $force_lazyload_css;
}
</pre>
								</p>
							</span>
						</label>
						<code>function w3SpeedsterCustomizeForceLazyCss($force_lazyload_css){</code>
						<textarea rows="5" cols="100" id="hook_customize_force_lazy_css"
							name="hook_customize_force_lazy_css" class="hook_before_start"><?php if (!empty($result['hook_customize_force_lazy_css']))
								echo esc_html(stripslashes($result['hook_customize_force_lazy_css'])); ?></textarea>
						<code> return $force_lazyload_css; <br>}</code>
					</div>

					<div class="single-hook">
						<label><span
								class="main-label"><?php $admin->translate('W3speedster External Javascript Customize', 'w3speedster'); ?></span><span
								class="info"></span>
							<span class="info-display">
								<p><?php $admin->translate('Function:', 'w3speedster'); ?>
									<?php $admin->translate('W3SpeedsterExternalJavascriptCustomize', 'w3speedster'); ?></p>
								<p><strong>
										Description:</strong><?php $admin->translate(' If you want to make changes in your external JavaScript tags, W3Speedster allows you to make changes in external JavaScript tags.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Parameter:', 'w3speedster'); ?></strong>
									<?php $admin->translate('$script_obj – Script in object format.', 'w3speedster'); ?><br>
									<?php $admin->translate('$script – Content of the JS file you want to make changes in', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Return:', 'w3speedster'); ?></strong><?php $admin->translate(' $script_obj – Make changes in Js files from an external source.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Example:', 'w3speedster'); ?></strong><br>
								<pre>
function W3SpeedsterExternalJavascriptCustomize($script_obj, $script){
if(strpos($script,'//text//') !== false){
	$script = str_replace(' ',' ',$script)
}
return $script_obj;
}
</pre>
								</p>
							</span>
						</label>
						<code>function W3SpeedsterExternalJavascriptCustomize($script_obj, $script){</code>
						<textarea rows="5" cols="100" id="hook_external_javascript_customize"
							name="hook_external_javascript_customize" class="hook_before_start"><?php if (!empty($result['hook_external_javascript_customize']))
								echo esc_html(stripslashes($result['hook_external_javascript_customize'])); ?></textarea>
						<code> return $script_obj; <br>}</code>
					</div>

					<div class="single-hook">
						<label><span
								class="main-label"><?php $admin->translate('W3speedster External Javascript Filter', 'w3speedster'); ?></span><span
								class="info"></span>
							<span class="info-display">
								<p><?php $admin->translate('Function:', 'w3speedster'); ?>
									<?php $admin->translate('W3SpeedsterExternalJavascriptFilter', 'w3speedster'); ?></p>
								<p><strong><?php $admin->translate('Description:', 'w3speedster'); ?></strong><?php $admin->translate(' If you want to dynamically exclude a JavaScript file or inline script from optimization, W3Speedster allows you to exclude it from optimization (like revslider).', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Parameter:', 'w3speedster'); ?></strong><?php $admin->translate(' $exclude_js – 0(default) || 1', 'w3speedster'); ?><br>
									<?php $admin->translate('$script_obj – Script in object format.', 'w3speedster'); ?><br>
									<?php $admin->translate('$script – Content of the JS file you want to make changes in.', 'w3speedster'); ?><br>
									<?php $admin->translate('$html – content of the webpage.', 'w3speedster'); ?></p>
								<p><strong><?php $admin->translate('Return:', 'w3speedster'); ?></strong>
									<?php $admin->translate('$exclude_js – exclude JS from optimization.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Example:', 'w3speedster'); ?></strong><br>
								<pre>
function W3SpeedsterExternalJavascriptFilter($exclude_js,$script_obj,$script,$html){
	if(wp_is_mobile()){
		if(strpos($script,'jquery-core-js') !== false || strpos($script,'/revslider/') !== false){
			$exclude_js = 1 ;
		}
	}
	return $exclude_js;
}
</pre>
								</p>
							</span>
						</label>
						<code>function W3SpeedsterExternalJavascriptFilter($exclude_js,$script_obj,$script,$html){</code>
						<textarea rows="5" cols="100" id="hook_external_javascript_filter"
							name="hook_external_javascript_filter" class="hook_before_start"><?php if (!empty($result['hook_external_javascript_filter']))
								echo esc_html(stripslashes($result['hook_external_javascript_filter'])); ?></textarea>
						<code> return $exclude_js; <br>}</code>
					</div>

					<div class="single-hook">
						<label><span
								class="main-label"><?php $admin->translate('W3speedster Customize Script Object', 'w3speedster'); ?></span><span
								class="info"></span>
							<span class="info-display">
								<p><?php $admin->translate('Function:', 'w3speedster'); ?>
									<?php $admin->translate('W3SpeedsterCustomizeScriptObject', 'w3speedster'); ?></p>
								<p><strong><?php $admin->translate('Description:', 'w3speedster'); ?></strong><?php $admin->translate(' W3Speedster allows you to customize script objects while minifying and combining scripts.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Parameter:', 'w3speedster'); ?></strong><?php $admin->translate('$script_obj- Script in object format.', 'w3speedster'); ?><br>
									<?php $admin->translate('$script- Content of the JS file you want to make changes in.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Return:', 'w3speedster'); ?></strong><?php $admin->translate(' $script_obj– Make changes in Js files.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Example:', 'w3speedster'); ?></strong><br>
								<pre>
function W3SpeedsterCustomizeScriptObject($script_obj, $script){
// your code
return $script_obj;
}
</pre>
								</p>
							</span>
						</label>
						<code>function W3SpeedsterCustomizeScriptObject($script_obj, $script){</code>
						<textarea rows="5" cols="100" id="hook_customize_script_object"
							name="hook_customize_script_object" class="hook_before_start"><?php if (!empty($result['hook_customize_script_object']))
								echo esc_html(stripslashes($result['hook_customize_script_object'])); ?></textarea>
						<code> return $script_obj; <br>}</code>
					</div>

					<div class="single-hook">
						<label><span
								class="main-label"><?php $admin->translate('W3speedster Exclude Internal Js W3 Changes', 'w3speedster'); ?></span><span
								class="info"></span>
							<span class="info-display">
								<p><?php $admin->translate('Function:', 'w3speedster'); ?>
									<?php $admin->translate('W3SpeedsterExcludeInternalJsW3Changes', 'w3speedster'); ?></p>
								<p><strong><?php $admin->translate('Description:', 'w3speedster'); ?></strong><?php $admin->translate(' Our plugin makes changes in JavaScript files for optimization, if you do not want to make any changes in JavaScript file, W3Speedster allows you to exclude JavaScript files from the plugin to make any changes.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Parameter:', 'w3speedster'); ?></strong>
									<?php $admin->translate('$path- path of your script tags url ', 'w3speedster'); ?><br>
									<?php $admin->translate('$string – JavaScript files content.', 'w3speedster'); ?><br>
									<?php $admin->translate('$exclude_from_w3_changes = 0(default) || 1', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Return:', 'w3speedster'); ?></strong><?php $admin->translate(' 1 – Exclude the JS file from making any changes.', 'w3speedster'); ?>
									<?php $admin->translate('0 – It will not exclude the JS file from making any changes.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Example:', 'w3speedster'); ?></strong><br>
								<pre>
function W3SpeedsterExcludeInternalJsW3Changes($exclude_from_w3_changes,$string,$path){
   if(strpos($path,'//js path//') !== false){
	$exclude_from_w3_changes = 1;
   }
   return $exclude_from_w3_changes;
}
</pre>
								</p>
							</span>
						</label>
						<code>function W3SpeedsterExcludeInternalJsW3Changes($exclude_from_w3_changes,$path,$string){</code>
						<textarea rows="5" cols="100" id="hook_exclude_internal_js_w3_changes"
							name="hook_exclude_internal_js_w3_changes" class="hook_before_start"><?php if (!empty($result['hook_exclude_internal_js_w3_changes']))
								echo esc_html(stripslashes($result['hook_exclude_internal_js_w3_changes'])); ?></textarea>
						<code> return $exclude_from_w3_changes; <br>}</code>
					</div>

					<div class="single-hook">
						<label><span
								class="main-label"><?php $admin->translate('W3speedster Exclude Page Optimization', 'w3speedster'); ?></span><span
								class="info"></span>
							<span class="info-display">
								<p><?php $admin->translate('Function:', 'w3speedster'); ?>
									<?php $admin->translate('W3SpeedsterExcludePageOptimization', 'w3speedster'); ?></p>
								<p><strong><?php $admin->translate('Description:', 'w3speedster'); ?></strong><?php $admin->translate(' W3Speedster allows you to exclude the pages from the Optimization. if you wish to exclude your pages from optimization. (like cart/login pages).', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Parameter:', 'w3speedster'); ?></strong><?php $admin->translate('$html = Page viewsources content.<br>$exclude_page_optimization = 0(default) || 1', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Return:', 'w3speedster'); ?></strong><?php $admin->translate(' 1 – it will exclude the page from optimization.', 'w3speedster'); ?>
									<?php $admin->translate('0 – it will not exclude the page from optimization.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Example:', 'w3speedster'); ?></strong><br>
								<pre>
function W3SpeedsterExcludePageOptimization($html,$exclude_page_optimization){
   if(!empty($_REQUEST['//Path//'])){
	$exclude_page_optimization = 1;
   }
   return $exclude_page_optimization;
}
</pre>
								</p>
							</span>
						</label>
						<code>function W3SpeedsterExcludePageOptimization($html,$exclude_page_optimization){</code>
						<textarea rows="5" cols="100" id="hook_exclude_page_optimization"
							name="hook_exclude_page_optimization" class="hook_before_start"><?php if (!empty($result['hook_exclude_page_optimization']))
								echo esc_html(stripslashes($result['hook_exclude_page_optimization'])); ?></textarea>
						<code> return $exclude_page_optimization; <br>}</code>
					</div>

					<div class="single-hook">
						<label><span
								class="main-label"><?php $admin->translate('W3speedster Customize Critical Css File Name', 'w3speedster'); ?></span><span
								class="info"></span>
							<span class="info-display">
								<p><?php $admin->translate('Function:', 'w3speedster'); ?>
									<?php $admin->translate('W3SpeedsterCustomizeCriticalCssFileName', 'w3speedster'); ?></p>
								<p><strong><?php $admin->translate('Description:', 'w3speedster'); ?></strong><?php $admin->translate(' If you wish to make any changes in Critical CSS filename, W3Speedster allows you to change in critical CSS file names. W3Speedster creates file names for critical CSS files but if you wish to change the name according to your preference this function will help.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Parameter:', 'w3speedster'); ?></strong><?php $admin->translate('$file_name – File name of the critical css.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Return:', 'w3speedster'); ?></strong><?php $admin->translate(' $file_name – New name of the critical css file.', 'w3speedster'); ?>
								</p>
								<p><strong><?php $admin->translate('Example:', 'w3speedster'); ?></strong><br>
								<pre>
function W3SpeedsterCustomizeCriticalCssFileName($file_name){
$file_name = str_replace(' ',' ',$file_name);
	return $file_name;
}
</pre>
								</p>
							</span>
						</label>
						<code>function W3SpeedsterCustomizeCriticalCssFileName($file_name){</code>
						<textarea rows="5" cols="100" id="hook_customize_critical_css_filename"
							name="hook_customize_critical_css_filename" class="hook_before_start"><?php if (!empty($result['hook_customize_critical_css_filename']))
								echo esc_html(stripslashes($result['hook_customize_critical_css_filename'])); ?></textarea>
						<code> return $file_name; <br>}</code>
					</div>
				</div>
				<hr>
				<div class="single-hook_btn">
					<div class="save-changes w3d-flex gap10">
						<input type="button" value="Save Changes" class="btn hook_submit">
						<div class="in-progress w3d-flex save-changes-loader" style="display:none">
							<img src="<?php echo W3SPEEDSTER_URL; ?>assets/images/loader-gif.gif" alt="loader"
								class="loader-img">
						</div>
					</div>

			</section>
			<section id="webvitalslogs" class="tab-pane fade">
				<div class="header w3d-flex gap20">
					<div class="heading_container">
						<h4 class="w3heading"><?php $admin->translate('Debug Logs', 'w3speedster'); ?>
						</h4>
						<span class="info"><a
								href="https://w3speedster.com/w3speedster-documentation/"><?php $admin->translate('More info', 'w3speedster'); ?>?
							</a></span>
					</div>
					<div class="icon_container"> <img
							src="<?php echo W3SPEEDSTER_URL; ?>assets/images/logs-icon.webp"></div>
				</div>
				<hr>

				<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
					<label><?php $admin->translate('Enable Core Web Vitals Logs', 'w3speedster'); ?><span
							class="info"></span><span
							class="info-display"><?php $admin->translate('Enable to Log Core Web Vitals Logs.', 'w3speedster'); ?></span></label>
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
							<label for="show_log_entry"><?php $admin->translate('Show', 'w3speedster'); ?></label>
							<select name="temp_input" id="show_log_entry" class="show_log_entry">
								<option value="10"><?php $admin->translate('10', 'w3speedster'); ?></option>
								<option value="20"><?php $admin->translate('20', 'w3speedster'); ?></option>
								<option value="30"><?php $admin->translate('30', 'w3speedster'); ?></option>
								<option value="40"><?php $admin->translate('40', 'w3speedster'); ?></option>
								<option value="50"><?php $admin->translate('50', 'w3speedster'); ?></option>
							</select>
						</div>
						<div class="delete-log-data w3d-flex gap10">
							<label for="log_delete_time">Delete Logs</label>
							<select class="log_select" id="log_delete_time" name="temp_input">
								<option value=""><?php $admin->translate('Select Log Time', 'w3speedster'); ?></option>
								<option value="last7days"><?php $admin->translate('Keep last 7 Days', 'w3speedster'); ?></option>
								<option value="lastMonth"><?php $admin->translate('Keep last 30 Days', 'w3speedster'); ?></option>
								<option value="last3months"><?php $admin->translate('Keep last 90 Days', 'w3speedster'); ?>
								</option>
								<option value="last6months"><?php $admin->translate('Keep last 180 Days', 'w3speedster'); ?>
								</option>
								<!-- <option value="lastYear">All</option> -->
								<option value="all"><?php $admin->translate('All', 'w3speedster'); ?></option>
							</select>
							<button type="button"
								class="btn btn-log-delete"><?php $admin->translate('Delete', 'w3speedster'); ?></button>
						</div>

					</div>
					<div class="w3d-flex gap10 filter-row">
						<div class="filter_by_issue w3d-flex gap10">
							<label for="filter_by_issue"><?php $admin->translate('Issue Type', 'w3speedster'); ?></label>
							<select name="temp_input" class="filter_by_issuetype">
								<option value=""><?php $admin->translate('All', 'w3speedster'); ?></option>
								<option value="CLS"><?php $admin->translate('CLS', 'w3speedster'); ?></option>
								<option value="FID"><?php $admin->translate('FID', 'w3speedster'); ?></option>
								<option value="INP"><?php $admin->translate('INP', 'w3speedster'); ?></option>
								<option value="LCP"><?php $admin->translate('LCP', 'w3speedster'); ?></option>
							</select>
						</div>
						<div class="filter_by_device w3d-flex gap10">
							<label for="filter_by_device"><?php $admin->translate('Device', 'w3speedster'); ?></label>
							<select name="temp_input" class="filter_by_deviceType">
								<option value=""><?php $admin->translate('All', 'w3speedster'); ?></option>
								<option value="Mobile"><?php $admin->translate('Mobile', 'w3speedster'); ?></option>
								<option value="Desktop"><?php $admin->translate('Desktop', 'w3speedster'); ?></option>
							</select>
						</div>
						<div class="filter_by_url ">
							<select class="url-select-multiple" id="filter_by_url" class="filter_by_url_input"
								name="temp_input[]" multiple="multiple">
								<input type="text" class="custom_select_inp"
									placeholder="<?php $admin->translate('https://...', 'w3speedster'); ?>">
								<button type="button" class="btn_clear_url_inp" style="display:none">+</button>
								<div id="custom_select_url"></div>
							</select>
						</div>
						<div class="filter_by_date w3d-flex gap10">
							<label for="start_date"><?php $admin->translate('From', 'w3speedster'); ?></label>
							<input type="text" name="temp_input" class="start_date">
							<label for="end_date"><?php $admin->translate('To', 'w3speedster'); ?></label>
							<input type="text" name="temp_input" class="end_date">
						</div>
						<button type="button"
							class="btn btn-apply-filter"><?php $admin->translate('Apply Filters', 'w3speedster'); ?></button>
						<button type="button"
							class="btn btn-rem-filter"><?php $admin->translate('Clear', 'w3speedster'); ?></button>
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

			<section id="htmlCache" class="tab-pane fade">
				<div class="header w3d-flex gap20">
					<div class="heading_container">
						<h4 class="w3heading"><?php $admin->translate('HTML Caches', 'w3speedster'); ?>
						</h4>
						<span class="info"><a
								href="https://w3speedster.com/w3speedster-documentation/"><?php $admin->translate('More info', 'w3speedster'); ?>?
							</a></span>
					</div>
					<div class="icon_container"> <img
							src="<?php echo W3SPEEDSTER_URL; ?>assets/images/html_caches-icon1.webp"></div>
				</div>
				<hr>
				<?php
				$admin->checkAdvCacheFileExists();
				?>

				<div class="html-cache-main">
					<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
						<label><?php $admin->translate('Enable HTML Caching', 'w3speedster'); ?><span class="info"></span><span
								class="info-display"><?php $admin->translate('Enable to on html caching', 'w3speedster'); ?></span></label>
						<div class="input_box">
							<label class="switch" for="enable-html-caching">
								<input type="checkbox" name="html_caching" <?php if (!empty($result['html_caching']) && $result['html_caching'] == "on")
									echo "checked"; ?> id="enable-html-caching"
									class="basic-set">
								<div class="checked"></div>
							</label>
						</div>
					</div>
					<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
						<label><?php $admin->translate('Enable caching for logged in user', 'w3speedster'); ?><span
								class="info"></span><span
								class="info-display"><?php $admin->translate('Enable caching for logged in user', 'w3speedster'); ?></span></label>
						<div class="input_box">
							<label class="switch" for="enable-caching-loggedin-user">
								<input type="checkbox" name="enable_loggedin_user_caching" <?php if (!empty($result['enable_loggedin_user_caching']) && $result['enable_loggedin_user_caching'] == "on")
									echo "checked"; ?>
									id="enable-caching-loggedin-user" class="basic-set">
								<div class="checked"></div>
							</label>
						</div>
					</div>
					<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
						<label><?php $admin->translate('Serve html cache file by', 'w3speedster'); ?><span
								class="info"></span><span
								class="info-display"><?php $admin->translate('Check method for serve cache html file', 'w3speedster'); ?></span></label>
						<div class="input_box w3d-flex gap10">
							<label class="switch" for="htaccess">
								<input value="htaccess" type="radio" name="by_serve_cache_file" <?php if (empty($result['by_serve_cache_file']) || $result['by_serve_cache_file'] == "htaccess")
									echo "checked"; ?> id="htaccess"
									class="basic-set">
								<div class="checked"></div>
							</label>
							<span><?php $admin->translate('Htaccess', 'w3speedster'); ?></span>
						</div>
						<div class="input_box w3d-flex gap10">
							<label class="switch" for="advanceCache">
								<input value="advanceCache" type="radio" name="by_serve_cache_file" <?php if (!empty($result['by_serve_cache_file']) && $result['by_serve_cache_file'] == "advanceCache")
									echo "checked"; ?> id="advanceCache"
									class="basic-set">
								<div class="checked"></div>
							</label>
							<span><?php $admin->translate('PHP Cache', 'w3speedster'); ?></span>
						</div>

					</div>
					<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
						<label><?php $admin->translate('Enable caching page with GET parameters', 'w3speedster'); ?><span
								class="info"></span><span
								class="info-display"><?php $admin->translate('Enable caching page with GET parameters', 'w3speedster'); ?></span></label>
						<div class="input_box">
							<label class="switch" for="enable-caching-page-get-para">
								<input type="checkbox" name="enable_caching_get_para" <?php if (!empty($result['enable_caching_get_para']) && $result['enable_caching_get_para'] == "on")
									echo "checked"; ?>
									id="enable-caching-page-get-para" class="basic-set">
								<div class="checked"></div>
							</label>
						</div>
					</div>
					<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
						<label><?php $admin->translate('Minify HTML', 'w3speedster'); ?><span class="info"></span><span
								class="info-display"><?php $admin->translate('BY minify html You can decrease the size of page', 'w3speedster'); ?></span></label>
						<div class="input_box">
							<label class="switch" for="minify_html_cache">
								<input type="checkbox" name="minify_html_cache" <?php if (!empty($result['minify_html_cache']) && $result['minify_html_cache'] == "on")
									echo "checked"; ?> id="minify_html_cache" class="basic-set">
								<div class="checked"></div>
							</label>
						</div>
					</div>
					<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
						<label><?php $admin->translate('Cache Expiry Time', 'w3speedster'); ?><span class="info"></span><span
								class="info-display"><?php $admin->translate('Input an time for cache expiry default time is 3600(1 hour)', 'w3speedster'); ?></span></label>
						<div class="input_box">
							<label class="html-cache-expiry w3d-flex" for="html-cache-expiry-time">
								<input type="text" name="html_caching_expiry_time"
									value="<?php echo (!empty($result['html_caching_expiry_time']) ? $admin->esc_attr($result['html_caching_expiry_time']) : 3600) ?>"
									id="html-cache-expiry-time" class="basic-set" style="max-width:80px;"><small>&nbsp;
									<?php $admin->translate('*Time delay in seconds', 'w3speedster'); ?></small>
								<div class="checked"></div>
							</label>
						</div>
					</div>
					<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
						<label><?php $admin->translate('Separate Cache For Mobile', 'w3speedster'); ?><span
								class="info"></span><span
								class="info-display"><?php $admin->translate('Enable to create separate cache file for mobile', 'w3speedster'); ?></span></label>
						<div class="input_box">
							<label class="switch" for="enable-html-caching-for-mobile">
								<input type="checkbox" name="html_caching_for_mobile" <?php if (!empty($result['html_caching_for_mobile']) && $result['html_caching_for_mobile'] == "on")
									echo "checked"; ?>
									id="enable-html-caching-for-mobile" class="basic-set">
								<div class="checked"></div>
							</label>
						</div>
					</div>
					<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
						<label><?php $admin->translate('Preload Caching', 'w3speedster'); ?><span class="info"></span><span
								class="info-display"><?php $admin->translate('Enable to create preload caching', 'w3speedster'); ?></span></label>
						<div class="input_box">
							<label class="switch" for="enable-preload-caching">
								<input type="checkbox" name="preload_caching" <?php if (!empty($result['preload_caching']) && $result['preload_caching'] == "on")
									echo "checked"; ?> id="enable-preload-caching" class="basic-set">
								<div class="checked"></div>
							</label>
						</div>
					</div>
					<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
						<label><?php $admin->translate('Preload page caching per minute', 'w3speedster'); ?> <span
								class="info"></span><span
								class="info-display"><?php $admin->translate('how many pages preload per minute', 'w3speedster'); ?></span></label>
						<div class="input_box">
							<label for="pmin-url">
								<input type="number" name="preload_per_min" id="preload_per_min" min="1" max="12"
									value="<?php echo (!empty($result['preload_per_min'])) ? $admin->esc_attr($result['preload_per_min']) : 1; ?>">
						</div>
					</div>

					<hr>
					<div class="save-changes w3d-flex gap10">
						<input type="button" value="Save Changes" class="btn hook_submit">
						<div class="in-progress w3d-flex save-changes-loader" style="display:none">
							<img src="<?php echo W3SPEEDSTER_URL; ?>assets/images/loader-gif.gif" alt="loader"
								class="loader-img">
						</div>
					</div>
				</div>

			</section>
			<section id="opt_img" class="tab-pane fade">
				<div class="header w3d-flex gap20">
					<div class="heading_container">
						<h4 class="w3heading">
							<?php $admin->translate('Image Optimization', 'w3speedster'); ?>
						</h4>
						<span class="info"><a
								href="https://w3speedster.com/w3speedster-documentation/#img_optimization"><?php $admin->translate('More info', 'w3speedster'); ?>?
							</a></span>
					</div>
					<div class="icon_container"> <img
							src="<?php echo W3SPEEDSTER_URL; ?>assets/images/image-icon.webp"></div>
				</div>
				<hr>
				<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
					<label><?php $admin->translate('Optimize JPG/PNG Images', 'w3speedster'); ?><span class="info"></span><span
							class="info-display"><?php $admin->translate('Enable to optimize jpg and png images.', 'w3speedster'); ?></span></label>
					<div class="input_box w3d-flex gap10">
						<label class="switch" for="optimize-jpg-png-images">
							<input type="checkbox" name="opt_jpg_png" <?php if (!empty($result['opt_jpg_png']) && $result['opt_jpg_png'] == "on")
								echo "checked"; ?> id="optimize-jpg-png-images"
								class="main-opt-img">
							<div class="checked"></div>
						</label>
					</div>

				</div>
				<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
					<label><?php $admin->translate('JPG PNG Image Quality', 'w3speedster'); ?><span class="info"></span><span
							class="info-display"><?php $admin->translate('90 ecommended', 'w3speedster'); ?></span></label>
					<div class="input_box">
						<label for="webp-image-quality">
							<input type="text" name="img_quality"
								value="<?php echo !empty($result['img_quality']) ? $admin->esc_attr($result['img_quality']) : 90; ?>"
								id="webp-image-quality" value="90%" style="max-width:70px;text-align:center">
					</div>
				</div>
				<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
					<label><?php $admin->translate('Convert to Webp', 'w3speedster'); ?><span class="info"></span><span
							class="info-display"><?php $admin->translate('This will convert and render images in webp. Need to start image optimization in image optimization tab', 'w3speedster'); ?></span></label>
					<div class="w3d-flex">
						<label for="jpg"><?php $admin->translate('JPG', 'w3speedster'); ?>&nbsp;</label>
						<input type="checkbox" name="webp_jpg" <?php if (!empty($result['webp_jpg']) && $result['webp_jpg'] == "on")
							echo "checked"; ?> id="jpg" class="main-opt-img">
					</div>
					<div class="w3d-flex">
						<label for="png"><?php $admin->translate('PNG', 'w3speedster'); ?>&nbsp;</label>
						<input type="checkbox" name="webp_png" <?php if (!empty($result['webp_png']) && $result['webp_png'] == "on")
							echo "checked"; ?> id="png" class="main-opt-img">
					</div>
				</div>
				<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
					<label><?php $admin->translate('Webp Image Quality', 'w3speedster'); ?><span class="info"></span><span
							class="info-display"><?php $admin->translate('90 recommended', 'w3speedster'); ?></span></label>
					<div class="input_box">
						<label for="webp-image-quality">
							<input type="text" name="webp_quality"
								value="<?php echo !empty($result['webp_quality']) ? $admin->esc_attr($result['webp_quality']) : 90; ?>"
								id="webp-image-quality" value="90%" style="max-width:70px;text-align:center">
					</div>
				</div>

				<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
					<label><?php $admin->translate('Enable Lazy Load', 'w3speedster'); ?><span class="info"></span><span
							class="info-display"><?php $admin->translate('This will enable lazy loading of resources.', 'w3speedster'); ?></span></label>
					<div class="w3d-flex">
						<label for="image"><?php $admin->translate('Image', 'w3speedster'); ?>&nbsp;</label>
						<input type="checkbox" name="lazy_load" <?php if (!empty($result['lazy_load']) && $result['lazy_load'] == "on")
							echo "checked"; ?> id="image" class="lazy-reso">
					</div>
					<div class="w3d-flex">
						<label for="iframe"><?php $admin->translate('Iframe', 'w3speedster'); ?>&nbsp;</label>
						<input type="checkbox" name="lazy_load_iframe" <?php if (!empty($result['lazy_load_iframe']) && $result['lazy_load_iframe'] == "on")
							echo "checked"; ?> id="iframe" class="lazy-reso">
					</div>
					<div class="w3d-flex">
						<label for="video"><?php $admin->translate('Video', 'w3speedster'); ?>&nbsp;</label>
						<input type="checkbox" name="lazy_load_video" <?php if (!empty($result['lazy_load_video']) && $result['lazy_load_video'] == "on")
							echo "checked"; ?> id="video" class="lazy-reso">
					</div>
					<div class="w3d-flex">
						<label for="audio"><?php $admin->translate('Audio', 'w3speedster'); ?>&nbsp;</label>
						<input type="checkbox" name="lazy_load_audio" <?php if (!empty($result['lazy_load_audio']) && $result['lazy_load_audio'] == "on")
							echo "checked"; ?> id="audio" class="lazy-reso">
					</div>
				</div>

				<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
					<label><?php $admin->translate('Pixels To load Resources Below the Viewport', 'w3speedster'); ?><span
							class="info"></span><span
							class="info-display"><?php $admin->translate('Enter pixels to start loading of resources like images, video, iframes, background images, audio which are below the viewport. For eg. 200', 'w3speedster'); ?></span></label>
					<div class="input_box">
						<label for="lazy-px">
							<input type="text" name="lazy_load_px"
								value="<?php echo !empty($result['lazy_load_px']) ? $admin->esc_attr($result['lazy_load_px']) : 200; ?>"
								id="lazy-px" placeholder="<?php $admin->translate('200px', 'w3speedster'); ?>"
								style="max-width:70px;text-align:center">
						</label>
					</div>
				</div>
				<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
					<label><?php $admin->translate('Load SVG Inline Tag as URL', 'w3speedster'); ?><span
							class="info"></span><span
							class="info-display"><?php $admin->translate('Load SVG inline tag as url to avoid large DOM elements', 'w3speedster'); ?></span></label>
					<div class="input_box">
						<label class="switch" for="load-inline-svg-tag-url">
							<input type="checkbox" name="inlineToUrlSVG" <?php if (!empty($result['inlineToUrlSVG']) && $result['inlineToUrlSVG'] == "on") {
								echo "checked";
							} ?> id="load-inline-svg-tag-url">
							<div class="checked"></div>
						</label>
					</div>
				</div>
				<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
					<label><?php $admin->translate('Optimize Images via wp-cron', 'w3speedster'); ?><span
							class="info"></span><span
							class="info-display"><?php $admin->translate('Optimize images via wp-cron.', 'w3speedster'); ?></span></label>
					<div class="input_box">
						<label class="switch" for="optimize-images-via-wp-cron">
							<input type="checkbox" name="enable_background_optimization" <?php if (!empty($result['enable_background_optimization']) && $result['enable_background_optimization'] == "on")
								echo "checked"; ?>
								id="optimize-images-via-wp-cron" class="main-opt-img">
							<div class="checked"></div>
						</label>
					</div>
				</div>
				<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
					<label><?php $admin->translate('Optimize Images on the go', 'w3speedster'); ?><span
							class="info"></span><span
							class="info-display"><?php $admin->translate('Automatically optimize images when site pages are crawled. Recommended to turn off after initial first crawl of all pages.', 'w3speedster'); ?></span></label>
					<div class="input_box">
						<label class="switch" for="optimize-images-on-the-go">
							<input type="checkbox" name="opt_img_on_the_go" <?php if (!empty($result['opt_img_on_the_go']) && $result['opt_img_on_the_go'] == "on")
								echo "checked"; ?>
								id="optimize-images-on-the-go" class="main-opt-img">
							<div class="checked"></div>
						</label>
					</div>
				</div>
				<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
					<label><?php $admin->translate('Automatically Optimize Images on Upload', 'w3speedster'); ?><span
							class="info"></span><span
							class="info-display"><?php $admin->translate('Automatically optimize new images on upload. Turn off if upload of images is taking more than expected.', 'w3speedster'); ?></span></label>
					<div class="input_box">
						<label class="switch" for="automatically-optimize-images-on-upload">
							<input type="checkbox" name="opt_upload" <?php if (!empty($result['opt_upload']) && $result['opt_upload'] == "on")
								echo "checked"; ?>
								id="automatically-optimize-images-on-upload">
							<div class="checked"></div>
						</label>
					</div>
				</div>
				<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
					<label><?php $admin->translate('Responsive Images', 'w3speedster'); ?><span class="info"></span><span
							class="info-display"><?php $admin->translate('Load smaller images on mobile to reduce load time', 'w3speedster'); ?></span></label>
					<div class="input_box">
						<label class="switch" for="resp-imgs">
							<input type="checkbox" name="resp_bg_img" <?php if (!empty($result['resp_bg_img']) && $result['resp_bg_img'] == "on")
								echo "checked"; ?> id="resp-imgs" class="resp-img">
							<div class="checked"></div>
						</label>
					</div>
				</div>
				<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
					<label><?php $admin->translate('Insert Aspect Ratio in Img Tag', 'w3speedster'); ?><span
							class="info"></span><span
							class="info-display"><?php $admin->translate('Insert aspect ratio in Img tag inline style.', 'w3speedster'); ?></span></label>
					<div class="input_box">
						<label class="switch" for="insert-aspect-ratio">
							<input type="checkbox" name="aspect_ratio_img" <?php if (!empty($result['aspect_ratio_img']) && $result['aspect_ratio_img'] == "on")
								echo "checked"; ?>
								id="insert-aspect-ratio">
							<div class="checked"></div>
						</label>
					</div>
				</div>

				&nbsp;
				<h4>
					<strong><?php echo ($img_remaining <= 0) ? $admin->translate_('Great Work!, all images are optimized', 'w3speedster') : $admin->translate_('Images to be optimized', 'w3speedster') . ' - <span class="progress-number">' . esc_html($img_remaining) . '</span>'; ?></strong>
				</h4>
				<div class="progress-container">
					<div class="progress progress-bar progress-bar-striped w3bg-success progress-bar-animated-img"
						style="<?php echo 'width:' . number_format((100 - ($img_remaining / $img_to_opt * 100)), 1) . '%' ?>">
						<?php echo '<span class="progress-percent">' . number_format((100 - ($img_remaining / $img_to_opt * 100)), 1) . '%</span>'; ?>
					</div>
				</div>
				<?php
				if (empty($result['license_key']) || empty($result['is_activated'])) {
					echo '<span class="non_licensed"><strong class="w3text-danger">* Starting 500 images will be optimized </strong><br><br><a href="https://w3speedster.com/" class="w3text-success"><strong>*<u>GO PRO</u> </strong></a> </span><br></br>';
				}
				?>
				<button class="start_image_optimization btn <?php echo ($img_remaining <= 0) ? 'restart' : ''; ?>"
					type="button" <?php if (empty($result['opt_jpg_png']) && empty($result['webp_png']) && empty($result['webp_jpg']))
						echo "disabled"; ?>>
					<?php echo ($img_remaining <= 0) ? $admin->translate_('Start image optimization again', 'w3speedster') : $admin->translate_('Start image optimization', 'w3speedster'); ?>
				</button>
				<button class="reset_image_optimization btn" type="button">
					<?php echo $admin->translate_('Reset', 'w3speedster'); ?>
				</button>
				<script>
					var start_optimization = 0;
					var offset = 0;
					var img_to_opt = <?php echo esc_html($img_to_opt); ?>;
					jQuery('.start_image_optimization').click(function () {
						if (!start_optimization) {
							if (jQuery(this).hasClass('restart')) {
								start_optimization = 2;
							} else {
								start_optimization = 1;
							}
							jQuery(this).hide();
							do_optimization(start_optimization);
							console.log("optimization_start");
						}
					});
					function do_optimization(opt) {
						jQuery.ajax({
							url: adminUrl,
							data: {
								'action': 'w3speedster_optimize_image',
								'start_type': opt
							},
							success: function (data) {
								// This outputs the result of the ajax request
								if (data && data != 'optimization running') {
									data = jQuery.parseJSON(data);
									console.log(data, offset);
									if (data.offset == -1) {
										setTimeout(function () {
											do_optimization(1);
										}, 100);
									} else if (offset != data.offset) {
										offset = data.offset;
										percent = (offset / img_to_opt * 100);
										jQuery('.progress-container .progress-bar-animated-img').css('width', percent.toFixed(1) + "%");
										jQuery('.progress-container .progress-bar-animated-img .progress-percent').html(percent.toFixed(1) + "%");
										jQuery('.progress-number').html(img_to_opt - offset);
										setTimeout(function () {
											do_optimization(1);
										}, 100);
									}
								} else {
									setTimeout(function () {
										do_optimization(1);
									}, 100);
								}
							},
							error: function (errorThrown) {
								console.log(errorThrown);
							}
						});
					}
				</script>
				<p>&nbsp;</p>
				<hr>
				<div class="save-changes w3d-flex gap10">
					<input type="button" value="<?php $admin->translate('Save Changes', 'w3speedster'); ?>"
						class="btn hook_submit gen">
					<div class="in-progress w3d-flex save-changes-loader" style="display:none">
						<img src="<?php echo W3SPEEDSTER_URL; ?>assets/images/loader-gif.gif" alt="loader"
							class="loader-img">
					</div>
				</div>
			</section>
			<section id="import" class="tab-pane fade">
			<div class="header w3d-flex gap20">
				<div class="heading_container">
					<h4 class="w3heading">
						<?php $admin->translate('Import / Export', 'w3speedster'); ?>
					</h4>
					<span class="info"><a
							href="https://w3speedster.com/w3speedster-documentation/"><?php $admin->translate('More info', 'w3speedster'); ?>?
						</a></span>
				</div>
				<div class="icon_container"> <img
						src="<?php echo W3SPEEDSTER_URL; ?>assets/images/import-export-icon.webp"></div>
			</div>
			<hr>
			<form id="import_form" method="post">
				<div class="import_form">
					<label><?php $admin->translate('Import Settings', 'w3speedster'); ?><span class="info"></span><span
							class="info-display"><?php $admin->translate('Enter exported json code from W3speedster plugin import/export page', 'w3speedster'); ?></span></label>
					<textarea id="import_text" name="import_text" rows="10" cols="16"
						placeholder="<?php $admin->translate('Enter json code', 'w3speedster'); ?>"></textarea>
					<input type="hidden" name="_wpnonce"
						value="<?php echo $admin->esc_attr(wp_create_nonce('w3_settings')); ?>">
					<button id="import_button" class="btn"
						type="button"><?php $admin->translate('Import', 'w3speedster'); ?></button>
				</div>
			</form>
			<?php
			$export_setting = $result;
			$export_setting['license_key'] = '';
			$export_setting['is_activated'] = '';
			?>

			<hr>
			<div class="import_form">
				<label><?php $admin->translate('Export Settings', 'w3speedster'); ?><span class="info"></span><span
						class="info-display"><?php $admin->translate('Copy the code and save it in a file for future use', 'w3speedster'); ?></span></label>
				<textarea rows="10" cols="16"><?php if (!empty($export_setting))
					echo $admin->w3JsonEncode($export_setting); ?></textarea>
			</div>
		</section>
			</div>
		</form>



	</div>
</main>
<script>

	var custom_css_cd = 0;
	var custom_js_cd = 0;
	
	jQuery(document).ready(function () {
		jQuery('.w3_custom_code').click(function () {
			console.log("custom code click");
			if (!custom_css_cd) {
				custom_css_cd = 1;
				setTimeout(function () { wp.codeEditor.initialize(jQuery('[name="custom_css"]'), cm_settings.codeCss); }, 300);
			}
			if (!custom_js_cd) {
				custom_js_cd = 1;
				setTimeout(function () {
					wp.codeEditor.initialize(jQuery('[name="custom_javascript"]'), cm_settings.codeJs);
					wp.codeEditor.initialize(jQuery('[name="custom_js"]'), cm_settings.codeJs);
				}, 300);
			}
		});

		
		var $textareas = jQuery('.hook_before_start');
		var customEditorSettings = wp.codeEditor.defaultSettings ? _.clone(wp.codeEditor.defaultSettings) : {};

		customEditorSettings.codemirror.mode = "text/x-php";
		customEditorSettings.codemirror.lineNumbers = false;
		customEditorSettings.codemirror.autoRefresh = true;

		$textareas.each(function () {
			var textareaId = jQuery(this).attr('id');
			var editor = wp.codeEditor.initialize(textareaId, customEditorSettings);

		});
	});

	
</script>