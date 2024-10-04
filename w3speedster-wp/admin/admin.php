<?php
require_once (W3SPEEDSTER_PLUGIN_DIR . 'admin/class_admin.php');
require_once (W3SPEEDSTER_PLUGIN_DIR . 'includes/class_image.php');
$w3_speedster_admin = new W3Speedster\w3speedster_admin();
$result = $w3_speedster_admin->settings;
global $advanced_cache_exist;

$img_to_opt = 1;
if (w3CheckMultisite()) {

	$blogs = get_sites();
	foreach ($blogs as $b) {
		$img_to_opt += $wpdb->get_var(
			$wpdb->prepare("SELECT COUNT(ID) FROM %s%s_posts WHERE post_type = %s",array($wpdb->base_prefix,$blog_id,'attachment'))
		);
	}
} else {
	$img_to_opt = $wpdb->get_var("SELECT count(ID) FROM {$wpdb->prefix}posts WHERE post_type='attachment'");
}
$opt_offset = w3GetOption('w3speedup_opt_offset');
$img_remaining = (int) $img_to_opt - (int) $opt_offset;
if (!empty ($result['enable_background_optimization']) && $img_remaining > 0) {
	if (!wp_next_scheduled('w3speedster_image_optimization')) {
		wp_schedule_event(time(), 'w3speedster_every_minute', 'w3speedster_image_optimization');
	}
} else {
	if (wp_next_scheduled('w3speedster_image_optimization')) {
		wp_clear_scheduled_hook('w3speedster_image_optimization');
	}
}

$preload_total = (int)w3GetOption('w3speedup_preload_css_total');
$que = count((array)w3GetOption('w3speedup_preload_css'));
$preload_created = $preload_total - ($que > 0 ? $que : 0);	
$preload_created = $preload_created < 0 ? 0 : $preload_created;
?>
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700">
<link rel="stylesheet" href="<?php echo esc_attr(W3SPEEDSTER_PLUGIN_URL); ?>assets/css/font-awesome.min.css">
<link rel="stylesheet" href="<?php echo esc_attr(W3SPEEDSTER_PLUGIN_URL); ?>assets/css/bootstrap.min.css">
<link rel="stylesheet" href="<?php echo esc_attr(W3SPEEDSTER_PLUGIN_URL); ?>assets/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="<?php echo esc_attr(W3SPEEDSTER_PLUGIN_URL); ?>assets/css/admin.css?ver=<?php echo esc_attr(wp_rand(10,1000)); ?>">
<link rel="stylesheet" href="<?php echo esc_attr(W3SPEEDSTER_PLUGIN_URL); ?>assets/css/jquery-ui.css">
<link rel="stylesheet" href="<?php echo esc_attr(W3SPEEDSTER_PLUGIN_URL); ?>assets/css/select2.min.css">
<script src="<?php echo esc_attr(W3SPEEDSTER_PLUGIN_URL); ?>assets/js/jquery.dataTables.min.js"></script>
<script src="<?php echo esc_attr(W3SPEEDSTER_PLUGIN_URL); ?>assets/js/prefixfree.min.js"></script>
<script src="<?php echo esc_attr(W3SPEEDSTER_PLUGIN_URL); ?>assets/js/bootstrap.min.js"></script>
<script src="<?php echo esc_attr(W3SPEEDSTER_PLUGIN_URL); ?>assets/js/jquery-ui.js"></script>
<script src="<?php echo esc_attr(W3SPEEDSTER_PLUGIN_URL); ?>assets/js/select2.min.js"></script>

<main class="admin-speedster">
	<div class="top_panel_container">
		<div class="top_panel d-none">
			<div class="logo_container">
				<img class="logo" src="<?php echo esc_url(W3SPEEDSTER_PLUGIN_URL); ?>assets/images/w3-logo.png">
			</div>

			<div class="support_section">
				<div class="right_section">
					<div class="doc w3d-flex gap10">
						<p class="m-0"><i class="fa fa-file-text" aria-hidden="true"></i></p>
						<p class="m-0 text-center text-white"><?php esc_html_e('Need help or have question', 'w3speedster'); ?><br><a
								href="https://w3speedster.com/w3speedster-documentation/" target="_blank"><?php esc_html_e('Check our documentation', 'w3speedster'); ?></a>
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
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512"><path fill="#fff" d="M246.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-128-128c-9.2-9.2-22.9-11.9-34.9-6.9S63.9 115 63.9 128v256c0 12.9 7.8 24.6 19.8 29.6s25.7 2.2 34.9-6.9l128-128z"/></svg>
			</button>
			<script>
			document.addEventListener('DOMContentLoaded', function() {
			var tabBtn = document.querySelector('.mobile_toggle button');
			var tabPanel = document.querySelector('.tab-panel');
			tabBtn.addEventListener('click', function() {
				tabPanel.classList.toggle('menu-open');
			});
			});
			</script>
		</div>
		<div class="logo_container">
				<img class="logo" src="<?php echo esc_url(W3SPEEDSTER_PLUGIN_URL); ?>assets/images/w3-logo.png">
			</div>
			<ul class="nav nav-tabs w3speedsternav">
				<?php if (empty ($result['manage_site_separately'])) { ?>
				<li class="w3_html_cache"><a data-toggle="tab" data-section="htmlCache" href="avascript:void(0)">
					<?php esc_html_e('HTML Cache', 'w3speedster'); ?>
				</a></li>
				<?php } ?>
				<li class="w3_general"><a data-toggle="tab" data-section="general" href="javascript:void(0)">
						<?php esc_html_e('General', 'w3speedster'); ?>
					</a></li>
				<?php if (empty ($result['manage_site_separately'])) { ?>
					<li class="w3_css"><a data-toggle="tab" data-section="css"  href="javascript:void(0)">
							<?php esc_html_e('Css', 'w3speedster'); ?>
						</a></li>
					<li class="w3_js"><a data-toggle="tab"  data-section="js" href="javascript:void(0)">
							<?php esc_html_e('Javascript', 'w3speedster'); ?>
						</a></li>
					<li class="w3_exclusions"><a data-toggle="tab" data-section="exclusions"  href="javascript:void(0)">
							<?php esc_html_e('Exclusions', 'w3speedster'); ?>
						</a></li>
					<li class="w3_custom_code"><a data-toggle="tab" data-section="w3_custom_code"  href="javascript:void(0)">
							<?php esc_html_e('Custom Code', 'w3speedster'); ?>
						</a></li>
					<li class="w3_cache"><a data-toggle="tab" data-section="cache"  href="javascript:void(0)">
							<?php esc_html_e('Clear Cache', 'w3speedster'); ?>
						</a></li>
					<li class="w3_opt_img"><a data-toggle="tab" data-section="opt_img"  href="javascript:void(0)">
							<?php esc_html_e('Image Optimization', 'w3speedster'); ?>
						</a></li>
					<li class="w3_hooks"><a data-toggle="tab" data-section="hooks"  href="javascript:void(0)">
							<?php esc_html_e('Hooks', 'w3speedster'); ?>
						</a></li>
					<li class="w3_webvitals_log"><a data-toggle="tab" data-section="webvitalslogs"  href="javascript:void(0)">
							<?php esc_html_e('Web Vitals Logs', 'w3speedster'); ?>
						</a></li>
					<li class="w3_import"><a data-toggle="tab" data-section="import"  href="javascript:void(0)">
							<?php esc_html_e('Import/Export', 'w3speedster'); ?>
						</a></li>
				<?php } ?>
			</ul>

			<div class="support_section">
				<a class="doc btn" href="https://w3speedster.com/w3speedster-documentation/" target="_blank"><?php esc_html_e('Documentation', 'w3speedster'); ?> <i class="fa fa-long-arrow-right" aria-hidden="true"></i></a>
				<a class="contact btn" href="https://w3speedster.com/contact-us/"><?php esc_html_e('Contact Us', 'w3speedster'); ?> <i class="fa fa-long-arrow-right" aria-hidden="true"></i></a>
			</div>
		</div>

		<form method="post" class="main-form">
			<div class="tab-content col-md-10">
				<section id="general" class="tab-pane fade in active">
					<div class="header w3d-flex gap20">
						<div class="heading_container">
							<h4 class="heading">
								<?php esc_html_e('General Setting', 'w3speedster'); ?>
							</h4>
							<h4 class="sub_heading">
								<?php esc_html_e('Optimization Level', 'w3speedster'); ?>
							</h4> <span class="info"><a
									href="https://w3speedster.com/w3speedster-documentation/#general_setting"><?php esc_html_e('More info', 'w3speedster'); ?>?
								</a></span>
						</div>
						<div class="icon_container">
							<img src="<?php echo esc_url(W3SPEEDSTER_PLUGIN_URL); ?>assets/images/general-setting-icon.webp">
						</div>
					</div>
					<hr>
					<div class="license_key w3d-flex gap20">
						<label for="">
							<?php esc_html_e('License Key', 'w3speedster'); ?><span class="info"></span><span
								class="info-display">
								<?php esc_html_e('Activate key to get updates and access to all features of the plugin.', 'w3speedster'); ?>
							</span>
						</label>
						<div class="key w3d-flex">
							<input type="text" name="license_key" placeholder="<?php esc_html_e('Key', 'w3speedster'); ?>"
								value="<?php echo !empty ($result['license_key']) ? esc_attr($result['license_key']) : ''; ?>"
								style="">
							<input type="hidden" name="w3_api_url"
								value="<?php echo !empty ($result['w3_api_url']) ? esc_attr($result['w3_api_url']) : ''; ?>">
							<input type="hidden" name="is_activated"
								value="<?php echo !empty ($result['is_activated']) ? esc_attr($result['is_activated']) : ''; ?>">
							<input type="hidden" name="_wpnonce" value="<?php echo esc_attr(wp_create_nonce('w3_settings')); ?>" >
							<input type="hidden" name="ws_action" value="cache">
							<?php if (!empty ($result['license_key']) && !empty ($result['is_activated'])) {
								?>
								<i class="fa fa-check-circle-o" aria-hidden="true"></i>
								<?php
							} else { ?>
								<button class="activate-key btn" type="button">
									<?php esc_html_e('Activate', 'w3speedster'); ?>
								</button>
							<?php }
							?>
						</div>
					</div>
					<?php
					if (function_exists('is_multisite') && is_multisite() && is_network_admin()) { ?>
						<tr>
							<th scope="row">
								<?php esc_html_e('Manage each site separately', 'w3speedster'); ?><span class="info"></span><span
									class="info-display">
									<?php esc_html_e('Enable this option to enter separate settings for each site. Plugin page will then be available in the backend of every site.', 'w3speedster'); ?>
								</span>
							</th>
							<td>
								<input type="checkbox" name="manage_site_separately" <?php if (!empty ($result['manage_site_separately']))
									echo "checked"; ?>>
							</td>
						</tr>
					<?php }
					$hidden_class = '';
					if (!empty ($result['manage_site_separately'])) {
						$hidden_class = 'tr-hidden';
					} ?>
					<hr>
					<div class="way_to_psi ">
					<details>
					<summary><h4 class="heading text-skyblue"><?php esc_html_e('Way to 90+ in Google PSI', 'w3speedster'); ?></h4></summary>	
						<div class="w3d-flex gap20 <?php echo esc_attr($hidden_class); ?>">
							<label><?php esc_html_e('Basic Settings', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('Enable This for Basic Settings', 'w3speedster'); ?>
									</span></label>
							<div class="input_box">
								<label class="switch" for="main-basic-settings">
									<input type="checkbox" name="main-basic-setting" id="main-basic-settings" <?php if (!empty ($result['main-basic-setting'])) echo "checked"; ?> data-class="basic-set" class="basic-set-checkbox">
									<div class="checked"></div>
								</label>
							</div>
						</div>
						<div class="w3d-flex gap20 <?php echo esc_attr($hidden_class); ?>">
							<label><?php esc_html_e('Optimize images and convert images to webp', 'w3speedster'); ?><span class="info"></span><span
									class="info-display"><?php esc_html_e('This will optimize and convert image to webp', 'w3speedster'); ?></span></label>
							<div class="input_box w3d-flex">
								<label class="switch" for="optimize-images-and-convert-images-to-webp">
									<input type="checkbox" name="main-opt-img" id="optimize-images-and-convert-images-to-webp" <?php if (!empty ($result['main-opt-img'])) echo "checked"; ?> data-class="main-opt-img" class="basic-set-checkbox">
									<div class="checked"></div>
								</label>&nbsp;&nbsp;&nbsp;
								<?php if($img_remaining > 0  && !empty ($result['main-opt-img'])){ ?>
								<div class="in-progress w3d-flex">
							<img src="<?php echo esc_url(W3SPEEDSTER_PLUGIN_URL); ?>assets/images/loader-gif.gif" alt="loader" class="loader-img">
							<small class="extra-small m-0">&nbsp;<em>&nbsp;<?php esc_html_e('Image optimization in progress...', 'w3speedster'); ?></em></small>
							</div>
								<?php } ?>
							</div>
						</div>
						<div class="w3d-flex gap20 <?php echo esc_attr($hidden_class); ?>">
							<label><?php esc_html_e('Lazyload Resources', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('This will enable lazy loading of resources.', 'w3speedster'); ?></span></label>
							<div class="input_box">
								<label class="switch" for="lazyload-images">
									<input type="checkbox" name="main-lazy-image" id="lazyload-images" <?php if (!empty ($result['main-lazy-image'])) echo "checked"; ?> data-class="lazy-reso" class="basic-set-checkbox">
									<div class="checked"></div>
								</label>
							</div>
						</div>
						<div class="w3d-flex gap20 <?php echo esc_attr($hidden_class); ?>">
							<label><?php esc_html_e('Responsive images', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('Load smaller images on mobile to reduce load time.', 'w3speedster'); ?></span></label>
							<div class="input_box">
								<label class="switch" for="responsive-images">
									<input type="checkbox" name="main-resp-img" id="responsive-images" <?php if (!empty ($result['main-resp-img'])) echo "checked"; ?> data-class="resp-img"
										class="basic-set-checkbox">
									<div class="checked"></div>
								</label>
							</div>
						</div>
						<div class="w3d-flex gap20 <?php echo esc_attr($hidden_class); ?>">
							<label><?php esc_html_e('Optimize css', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('It will turn on css optimization and generate critical css.', 'w3speedster'); ?></span></label>
							<div class="input_box w3d-flex">
								<label class="switch" for="optimize-css">
									<input type="checkbox" name="main-opt-css" id="optimize-css" <?php if (!empty ($result['main-opt-css'])) echo "checked"; ?> data-class="opt-css" class="basic-set-checkbox">
									<div class="checked"></div>
								</label>&nbsp;&nbsp;&nbsp;
								<?php if($preload_total != $preload_created && !empty ($result['main-opt-css'])){ ?>
							<div class="in-progress w3d-flex">
							<img src="<?php echo esc_url(W3SPEEDSTER_PLUGIN_URL); ?>assets/images/loader-gif.gif" alt="loader" class="loader-img">
							<small class="extra-small m-0">&nbsp;<em>&nbsp;<?php esc_html_e('Critical css is generating...', 'w3speedster'); ?></em></small>
							</div>
								<?php } ?>
							</div>
						</div>
						<div class="w3d-flex gap20 <?php echo esc_attr($hidden_class); ?>">
							<label><?php esc_html_e('Lazyload javascript', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('It will turn on javascript optimization and lazyload them.', 'w3speedster'); ?></span></label>
							<div class="input_box">
								<label class="switch" for="lazyload-javascript">
									<input type="checkbox" name="main-lazy-js" id="lazyload-javascript" <?php if (!empty ($result['main-lazy-js'])) echo "checked"; ?> data-class="opt-js" class="basic-set-checkbox">
									<div class="checked"></div>
								</label>
							</div>
						</div>
					</div>
					</details>
					<hr>
					<div class="turn_on_optimization <?php echo esc_attr($hidden_class); ?>">
						<div class="w3d-flex gap20">
							<label><?php esc_html_e('Turn ON optimization', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('Site will start to optimize. All optimization settings will be applied.', 'w3speedster'); ?></span></label>
							<div class="input_box">
								<label class="switch" for="turn-on-optimization">
									<input type="checkbox" name="optimization_on" <?php if (!empty ($result['optimization_on']) && $result['optimization_on'] == "on") echo "checked"; ?> id="turn-on-optimization" class="basic-set">
									<div class="checked"></div>
								</label>
							</div>
						</div>
						<div class="w3d-flex gap20 <?php echo esc_attr($hidden_class); ?>">
							<label><?php esc_html_e('Optimize Pages with Query Parameters', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('It will optimize pages with query parameters. Recommended only for servers with high performance.', 'w3speedster'); ?></span></label>
							<div class="input_box">
								<label class="switch" for="optimize-pages-with-query-parameters">
									<input type="checkbox" name="optimize_query_parameters" id="optimize-pages-with-query-parameters" <?php if (!empty ($result['optimize_query_parameters'])) echo "checked"; ?> class="basic-set">
									<div class="checked"></div>
								</label>
							</div>
						</div>
						<div class="w3d-flex gap20 <?php echo esc_attr($hidden_class); ?>">
							<label><?php esc_html_e('Optimize pages when User Logged In', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('It will optimize pages when users are logged in. Recommended only for servers with high performance', 'w3speedster'); ?></span></label>
							<div class="input_box">
								<label class="switch" for="optimize-pages-when-user-logged-in">
									<input type="checkbox" name="optimize_user_logged_in" id="optimize-pages-when-user-logged-in" <?php if (!empty ($result['optimize_user_logged_in'])) echo "checked"; ?>>
									<div class="checked"></div>
								</label>
							</div>
						</div>
						<div class="w3d-flex gap20 <?php echo esc_attr($hidden_class); ?>">
							<label><?php esc_html_e('Separate javascript and css cache for mobile', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('It will create separate javascript and css cache for mobile', 'w3speedster'); ?></span></label>
							<div class="input_box">
								<label class="switch" for="separate-javascript-and-css-cache-for-mobile">
									<input type="checkbox" name="separate_cache_for_mobile" id="separate-javascript-and-css-cache-for-mobile" <?php if (!empty ($result['separate_cache_for_mobile'])) echo "checked"; ?>>
									<div class="checked"></div>
								</label>
							</div>
						</div>
						<div class="w3d-flex gap20 <?php echo esc_attr($hidden_class); ?>">
							<label><?php esc_html_e('CDN url', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('Enter CDN url with http or https', 'w3speedster'); ?></span></label>
							<div class="input_box">
								<label for="cdn-url">
									<input type="text" name="cdn" id="cdn-url" placeholder="<?php esc_html_e('Please Enter CDN url here', 'w3speedster'); ?>" value="<?php if (!empty ($result['cdn'])) echo esc_attr($result['cdn']); ?>">
							</div>
						</div>
						<div class="w3d-flex gap20 <?php echo esc_attr($hidden_class); ?>">
							<label><?php esc_html_e('Exclude file extensions from cdn', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('Enter extension separated by comma which are to excluded from CDN. For eg. (.woff, .eot)', 'w3speedster'); ?></span></label>
							<div class="input_box">
								<label for="exclude-file-extensions-from-cdn">
									<input type="text" name="exclude_cdn" id="exclude-file-extensions-from-cdn" placeholder="<?php esc_html_e('Please Enter extensions separated by comma ie .jpg, .woff', 'w3speedster'); ?>" value="<?php if (!empty ($result['exclude_cdn'])) echo  esc_attr($result['exclude_cdn']); ?>">
							</div>
						</div>
						<div class="w3d-flex gap20 <?php echo esc_attr($hidden_class); ?>">
							<label><?php esc_html_e('Exclude path from cdn', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('Enter path separated by comma which are to excluded from CDN. For eg. (/wp-includes/)', 'w3speedster'); ?></span></label>
							<div class="input_box">
								<label for="exclude-path-from-cdn">
									<input type="text" name="exclude_cdn_path" id="exclude-path-from-cdn"
										placeholder="<?php esc_html_e('Please Enter extensions separated by comma', 'w3speedster'); ?>"
										value="<?php if (!empty ($result['exclude_cdn_path']))
											echo  esc_attr($result['exclude_cdn_path']); ?>">
							</div>
						</div>
						<div class="w3d-flex gap20 <?php echo esc_attr($hidden_class); ?>">
							<label><?php esc_html_e('Enable leverage browsing cache', 'w3speedster'); ?><span class="info"></span><span
									class="info-display"><?php esc_html_e('Enable to turn on leverage browsing cache.', 'w3speedster'); ?></span></label>
							<div class="input_box">
								<label class="switch" for="enable-leverage-browsing-cache">
									<input type="checkbox" name="lbc" id="enable-leverage-browsing-cache" <?php if (!empty ($result['lbc']) && $result['lbc'] == "on")echo "checked"; ?>
										class="basic-set">
									<div class="checked"></div>
								</label>
							</div>
						</div>
						<div class="w3d-flex gap20 <?php echo esc_attr($hidden_class); ?>">
							<label><?php esc_html_e('Enable Gzip compression', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('Enable to turn on Gzip compresssion.', 'w3speedster'); ?></span></label>
							<div class="input_box">
								<label class="switch" for="enable-gzip-compression">
									<input type="checkbox" name="gzip" <?php if (!empty ($result['gzip']) && $result['gzip'] == "on")
										echo "checked"; ?> id="enable-gzip-compression"
										class="basic-set">
									<div class="checked"></div>
								</label>
							</div>
						</div>
						<div class="w3d-flex gap20 <?php echo esc_attr($hidden_class); ?>">
							<label><?php esc_html_e('Remove query parameters', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('Enable to remove query parameters from resources.', 'w3speedster'); ?></span></label>
							<div class="input_box">
								<label class="switch" for="remove-query-parameters">
									<input type="checkbox" name="remquery" <?php if (!empty ($result['remquery']) && $result['remquery'] == "on")
										echo "checked"; ?> id="remove-query-parameters"
										class="basic-set">
									<div class="checked"></div>
								</label>
							</div>
						</div>
						
						<div class="w3d-flex gap20 <?php echo esc_attr($hidden_class); ?>">
							<label><?php esc_html_e('Fix INP Issues', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('Enable to fix Interactive next paint issues appearing in googe page speed assessment test and/or google search console.', 'w3speedster'); ?></span></label>
							<div class="input_box">
								<label class="switch">
									<input type="checkbox" name="enable_inp" <?php if (!empty ($result['enable_inp']) && $result['enable_inp'] == "on") echo "checked"; ?> id="enable-inp">
									<div class="checked"></div>
								</label>
							</div>
						</div>
					</div>

					<hr>
					<div class="cdn_resources <?php echo esc_attr($hidden_class); ?>">
						<div class="w3d-flex gap20 align-item-baseline">
							<label for="cache_path"><?php esc_html_e('Cache Path', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('Enter path where cache can be stored. Leave empty for default path', 'w3speedster'); ?></span></label>
							<div class="input_box">
								<div class="cdn_input_box">
									<input type="text" name="cache_path"
										placeholder="<?php esc_html_e('Please Enter full cache path', 'w3speedster'); ?>"
										value="<?php echo !empty ($result['cache_path']) ? esc_attr($result['cache_path']) : ''; ?>"
										id="cache_path" placeholder="<?php esc_html_e('Please Enter full cache path', 'w3speedster'); ?>">
									<small class="d-block"><?php esc_html_e('Default cache path:', 'w3speedster'); ?>
										<?php echo esc_html($w3_speedster_admin->add_settings['wp_content_path'] . '/cache'); ?>
									</small>
								</div>
							</div>

						</div>
					</div>
					<hr>

					<div class="save-changes w3d-flex gap10">
						<input type="button" value="<?php esc_html_e('Save Changes', 'w3speedster'); ?>" class="btn hook_submit">
						<div class="in-progress w3d-flex save-changes-loader" style="display:none">
							<img src="<?php echo esc_url(W3SPEEDSTER_PLUGIN_URL); ?>assets/images/loader-gif.gif"
								alt="loader" class="loader-img">
						</div>
					</div>

					<script>
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
								url: "<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>",
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

					</script>
				</section>
                
				<section id="css" class="tab-pane fade">
					<div class="header w3d-flex gap20">
						<div class="heading_container">
							<h4 class="heading">
								<?php esc_html_e('CSS Optimization', 'w3speedster'); ?>
							</h4>
							<span class="info"><a
									href="https://w3speedster.com/w3speedster-documentation/#css_optimization"><?php esc_html_e('More info', 'w3speedster'); ?>?
								</a></span>
						</div>
						<div class="icon_container">
							<img src="<?php echo esc_url(W3SPEEDSTER_PLUGIN_URL); ?>assets/images/css-icon.webp">
						</div>
					</div>
					<hr>
					<div class="css_box">
						<div class="w3d-flex gap20 ">
							<label><?php esc_html_e('Enable CSS Optimization', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('Turn on to optimize css', 'w3speedster'); ?></span></label>
							<div class="input_box">
								<label class="switch" for="enable-css-minification">
									<input type="checkbox" name="css" <?php if (!empty ($result['css']) && $result['css'] == "on")
										echo "checked"; ?> id="enable-css-minification"
										class="opt-css">
									<div class="checked"></div>
								</label>
							</div>
						</div>
						<div class="w3d-flex gap20 ">
							<label><?php esc_html_e('Combine Google fonts', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('Turn on to combine all google fonts', 'w3speedster'); ?></span></label>
							<div class="input_box">
								<label class="switch" for="combine-google-fonts">
									<input type="checkbox" name="google_fonts" <?php if (!empty ($result['google_fonts']) && $result['google_fonts'] == "on") echo "checked"; ?> id="combine-google-fonts"
										class="opt-css">
									<div class="checked"></div>
								</label>
							</div>
						</div>
					</div>
					<hr>
					<div class="css_box">
						<div class="w3d-flex gap20 ">
							<label><?php esc_html_e('Load Critical CSS', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('Preload generated crictical css', 'w3speedster'); ?></span></label>
							<div class="input_box">
								<label class="switch" for="load-critical-css">
									<input type="checkbox" name="load_critical_css" <?php if (!empty ($result['load_critical_css']) && $result['load_critical_css'] == "on")
										echo "checked"; ?> id="load-critical-css" class="opt-css">
									<div class="checked"></div>
								</label>
							</div>
						</div>
						<div class="w3d-flex gap20 critical-in-style" <?php if (empty ($result['load_critical_css'])) { echo 'style="display:none"'; }?>>
							<label><?php esc_html_e('Load Critical CSS in Style Tag', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('Preload generated crictical css in style tag', 'w3speedster'); ?></span></label>
							<div class="input_box">
								<label class="switch" for="load-critical-css-in-style-tag">
									<input type="checkbox" name="load_critical_css_style_tag" <?php if (!empty ($result['load_critical_css_style_tag']) && $result['load_critical_css_style_tag'] == "on")
										echo "checked"; ?>
									id="load-critical-css-in-style-tag" class="opt-css">
									<div class="checked"></div>
								</label>
							</div>
						</div>
						<?php if (!empty ($result['load_critical_css']) && $result['load_critical_css'] == "on") { ?>
						<div class="d-block ">
							<div class="control_box w3d-flex gap20">
								<label for=""><?php esc_html_e('Start generating critical css', 'w3speedster'); ?> <br>
									<?php if (empty ($result['license_key']) || empty ($result['is_activated'])) { ?>
										<small class="text-danger"><?php esc_html_e('The Critical CSS for only the homepage will be generated.', 'w3speedster'); ?></small>
									<?php } ?>
								</label>
								<p class="w3d-flex go_pro gap20"><input type="button" id="create_critical_css"
										value="<?php esc_html_e('CREATE CRITICAL CSS', 'w3speedster'); ?>" class="btn gen-critical">
									<?php if (empty ($result['license_key']) || empty ($result['is_activated'])) { ?>
										<a href="https://w3speedster.com/" class="text-success"><strong><u><?php esc_html_e('GO PRO', 'w3speedster'); ?></u></strong></a>
									</p>
								<?php } ?>
							</div>
							<div class="result_box">
								<div class="progress-container">
									<div class="progress progress-bar bg-success critical-progress-bar"
										style="width:<?php  echo $preload_created > 0 ? number_format(($preload_created / $preload_total * 100), 1) : 1; ?>%">
										<?php
										$percent = $preload_created > 0 ? number_format((($preload_created / $preload_total * 100)), 1) : 1;
										echo '<span class="progress-percent">' . esc_html($percent) . '%</span>'; ?>
									</div>
								</div>
								<span class="preload_created_css">
									<?php echo esc_html($preload_created); ?>
								</span> <?php echo esc_attr__('created of', 'w3speedster')?> <span class="preload_total_css">
									<?php echo esc_html($preload_total); ?>
								</span> <?php echo esc_attr__('pages crawled', 'w3speedster')?></td>
								<?php $critical_css_error = w3GetOption('w3speedup_critical_css_error');?>
								<textarea disabled rows="1" cols="100"
									class="preload_error_css"><?php echo (empty ($result['load_critical_css'])) ? esc_attr__('*Please enable load critical css and save to start generating critical css', 'w3speedster') : esc_attr($critical_css_error); ?></textarea>

							</div>
						</div>
						<?php } ;?>
						<script>
						jQuery(document).ready(function(){
								jQuery('#create_critical_css').click(function(){
									jQuery(this).prop('disabled',true);
									jQuery('.critical-css-bar').show();
									create_critical_css();
								});
								jQuery('#load-critical-css').on('change',function(){
									if(jQuery(this).is(':checked')){
										jQuery('.critical-in-style').show();             
									}else{
									jQuery('.critical-in-style').hide();
									}
								});
							});
							function create_critical_css(){
								//jQuery('.preload_error_css').html('');
								jQuery('.critical-progress-bar').addClass('progress-bar-animated progress-bar-striped');
								jQuery.ajax({
									url: "<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>",
									data: {
										'action': 'w3speedster_preload_css',
										'page' : 'admin' 
									},
									success:function(data) {
										data = jQuery.parseJSON( data );
										console.log(data);
										if(data[0] == 'success' || (data[0] == 'error' && (data[1] == 'process-already-running' || data[1].indexOf('no stylesheets found') > -1))){
											var textArea = jQuery('textarea.preload_error_css');
											if(textArea.attr('data-running') != data[4]){
												textArea.attr('data-running',data[4]);
												jQuery('textarea.preload_error_css').html('Critical CSS is currently running for ' + data[4]);
											}
											
											var timeOut = 10000;
											jQuery('.preload_total_css').html(data[2]);
											jQuery('.preload_created_css').html(data[3]);
											percent = data[2] > 0 && data[3] > 0 ? parseFloat(data[3])/parseFloat(data[2])*100 : 1;
											jQuery('.critical-progress-bar').css('width',percent.toFixed(1)+'%');
											jQuery('.progress-percent').html(percent.toFixed(1)+'%');
											if(data[2] > data[3] || data[3] == 0){
												console.log("next scheduled");
												setTimeout(create_critical_css,timeOut);
											}else{
												setTimeout(create_critical_css,20000);
												jQuery('.critical-css-bar').hide();
												jQuery('.preload_error_css').html('');
											}
										}else{
											//jQuery('.preload_error_css').html(data[1]);
											setTimeout(create_critical_css,20000);
											jQuery('#create_critical_css').prop('disabled',true);
											jQuery('.critical-css-bar').hide();
											jQuery('.critical-progress-bar').addClass('progress-bar-animated progress-bar-striped');
										}
									},
									error: function(errorThrown){
										jQuery('.critical-progress-bar').addClass('progress-bar-animated progress-bar-striped');
										console.log(errorThrown);
									}
								});
							}
						</script>
					</div>
					<hr>
					<div class="css_box cdn_resources">
						<div class="w3d-flex gap20 align-item-baseline">
							<label><?php esc_html_e('Load Style Tag in Head to Avoid CLS', 'w3speedster'); ?> <span class="info"></span><span
									class="info-display"><?php esc_html_e('Enter matching text of style tag, which are to be loaded in the head. Each style tag to be entered in a new line', 'w3speedster'); ?></span></label>
							<div class="input_box">
								<div class="single-row">
										<?php        
										if(array_key_exists('load_style_tag_in_head', $result)){
										foreach (explode("\r\n", $result['load_style_tag_in_head']) as $row) {
											if (!empty(trim($row))) {
												?>
												<div class="cdn_input_box minus w3d-flex">
													<input type="text" name="load_style_tag_in_head[]" value="<?php echo  esc_attr(trim($row)); ?>"
														placeholder="<?php esc_html_e('Please Enter style tag text', 'w3speedster'); ?>"><button type="button"
														class="text-white rem-row bg-danger"><i
															class="fa fa-times"></i></button>
												</div>
												<?php
											}
										}} ?>
									</div>
									<div class="cdn_input_box plus">
										<button type="button"
											data-name="load_style_tag_in_head" data-placeholder="<?php esc_html_e('Please Enter style tag text', 'w3speedster'); ?>" class="btn small text-white bg-success add_more_row"><?php esc_html_e('Add Rule', 'w3speedster'); ?></button>
									</div>
							</div>
						</div>
					</div>
					<hr>
					<div class="save-changes w3d-flex gap10">
						<input type="button" value="<?php esc_html_e('Save Changes', 'w3speedster'); ?>" class="btn hook_submit">
						<div class="in-progress w3d-flex save-changes-loader" style="display:none">
							<img src="<?php echo esc_url(W3SPEEDSTER_PLUGIN_URL); ?>assets/images/loader-gif.gif"
								alt="loader" class="loader-img">
						</div>
					</div>

				</section>
				<section id="js" class="tab-pane fade white-bg-speedster">
					<div class="header w3d-flex gap20">
						<div class="heading_container">
							<h4 class="heading">
								<?php esc_html_e('Javascript Optimization', 'w3speedster'); ?>
							</h4>
							<span class="info"><a
									href="https://w3speedster.com/w3speedster-documentation/#javascript_optimization"><?php esc_html_e('More info', 'w3speedster'); ?>?
								</a></span>
						</div>
						<div class="icon_container"><img
								src="<?php echo esc_url(W3SPEEDSTER_PLUGIN_URL); ?>assets/images/js-icon.webp"></div>
					</div>
					<hr>

					<div class="js_box">
						<div class="w3d-flex gap20 ">
							<label><?php esc_html_e('Enable Javascript Optimization', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('Turn on to optimize javascript', 'w3speedster'); ?></span></label>
							<div class="input_box">
								<label class="switch" for="enable-js-minification">
									<input type="checkbox" name="js" <?php if (!empty ($result['js']) && $result['js'] == "on")
										echo "checked"; ?> id="enable-js-minification"
										class="opt-js">
									<div class="checked"></div>
								</label>
							</div>
						</div>

						<div class="w3d-flex gap20 ">
							<label><?php esc_html_e('Lazyload Javascript', 'w3speedster'); ?> <span class="info"></span><span class="info-display"><?php esc_html_e('Choose when to load javascript', 'w3speedster'); ?></span></label>
							<select name="load_combined_js" class="opt-js-select">
								<option value="after_page_load" <?php echo !empty ($result['load_combined_js']) && $result['load_combined_js'] == 'after_page_load' ? 'selected' : ''; ?>>
									<?php esc_html_e('Yes', 'w3speedster'); ?>
								</option>
								<option value="on_page_load" <?php echo !empty ($result['load_combined_js']) && $result['load_combined_js'] == 'on_page_load' ? 'selected' : ''; ?>>
									<?php esc_html_e('No', 'w3speedster'); ?>
								</option>
								
							</select>
						</div>
					</div>
					<hr>
					<div class="js_box cdn_resources">
					<div class="w3d-flex gap20 align-item-baseline">
						<label><?php esc_html_e('Load Javascript Inline Script as URL', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('Enter matching text of inline script url, which needs to be excluded from deferring of javascript. Each exclusion to be entered in a new line.', 'w3speedster'); ?></span></label>
						<div class="input_box">
								<div class="single-row">
									<?php
                                    if(array_key_exists('load_script_tag_in_url', $result)){
									foreach (explode("\r\n", $result['load_script_tag_in_url']) as $row) {
										if (!empty(trim($row))) {
											?>
											<div class="cdn_input_box minus w3d-flex">
												<input type="text" name="load_script_tag_in_url[]" value="<?php echo  esc_attr(trim($row)); ?>"
													placeholder="<?php esc_html_e('Please Enter matching text of the inline javascript here', 'w3speedster'); ?>"><button type="button"
													class="text-white rem-row bg-danger"><i
														class="fa fa-times"></i></button>
											</div>
											<?php
										}
									}} ?>
								</div>
								<div class="cdn_input_box plus">
									<button type="button"
										data-name="load_script_tag_in_url" data-placeholder="<?php esc_html_e('Please Enter matching text of the inline javascript here', 'w3speedster'); ?>" class="btn small text-white bg-success add_more_row"><?php esc_html_e('Add Rule', 'w3speedster'); ?></button>
								</div>

						</div>
						</div>
					</div>
					<hr>
					<div class="save-changes w3d-flex gap10">
						<input type="button" value="<?php esc_html_e('Save Changes', 'w3speedster'); ?>" class="btn hook_submit">
						<div class="in-progress w3d-flex save-changes-loader" style="display:none">
							<img src="<?php echo esc_url(W3SPEEDSTER_PLUGIN_URL); ?>assets/images/loader-gif.gif"
								alt="loader" class="loader-img">
						</div>
					</div>

				</section>
				<section id="exclusions" class="tab-pane fade">
					<div class="header w3d-flex gap20">
						<div class="heading_container">
							<h4 class="heading">
								<?php esc_html_e('Exclusions', 'w3speedster'); ?>
							</h4>
							<span class="info"><a href="https://w3speedster.com/w3speedster-documentation/"><?php esc_html_e('More info', 'w3speedster'); ?>?
								</a></span>
						</div>
						<div class="icon_container"> <img
								src="<?php echo esc_url(W3SPEEDSTER_PLUGIN_URL); ?>assets/images/exclusions-icon1.webp"></div>
					</div>
										<hr>
					<div class="cdn_resources <?php echo esc_attr($hidden_class); ?>">
						<div class="w3d-flex gap20 align-item-baseline">
							<label for="Preload Resources"><?php esc_html_e('Preload Resources', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('Enter url of the Resources, which are to be preloaded..', 'w3speedster'); ?></span></label>
							<div class="input_box">
								<div class="single-row">
									<?php
									//$result['preload_resources'] = 'hello';
                                    if(array_key_exists('preload_resources', $result)){
									foreach (explode("\r\n", $result['preload_resources']) as $row) {
										if (!empty(trim($row))) {
											?>
											<div class="cdn_input_box minus w3d-flex">
												<input type="text" name="preload_resources[]" value="<?php echo  esc_attr(rtrim($row)); ?>"
													placeholder="<?php esc_html_e('Please Enter Resource Url', 'w3speedster'); ?>"><button type="button"
													class="text-white rem-row bg-danger"><i
														class="fa fa-times"></i></button>
											</div>
											<?php
										}
									}} ?>
								</div>
								<div class="cdn_input_box plus">
									<button type="button"
										data-name="preload_resources" data-placeholder="<?php esc_html_e('Please Enter Resource Url', 'w3speedster'); ?>" class="btn small text-white bg-success add_more_row"><?php esc_html_e('Add Rule', 'w3speedster'); ?></button>
								</div>

							</div>

						</div>
					</div>
					<hr>
					<div class="cdn_resources <?php echo esc_attr($hidden_class); ?>">
						<div class="w3d-flex gap20 align-item-baseline">
							<label for="Exclude Images from Lazy Loading"><?php esc_html_e('Exclude Images from Lazy Loading', 'w3speedster'); ?><span
									class="info"></span><span class="info-display"><?php esc_html_e('Enter any matching text of image tag to exclude from lazy loading. For more than one exclusion, enter in a new line. For eg. (class / Id / url / alt). Images will still continue to be optimized and rendered in webp if respective settings are turned on.', 'w3speedster'); ?></span></label>
							<div class="input_box">
								<div class="single-row">
									<?php
									//$result['exclude_lazy_load'] = 'hello';
                                    if(array_key_exists('exclude_lazy_load', $result)){
									foreach (explode("\r\n", $result['exclude_lazy_load']) as $row) {
										if (!empty(trim($row))) {
											?>
											<div class="cdn_input_box minus w3d-flex">
												<input type="text" name="exclude_lazy_load[]" value="<?php echo  esc_attr(trim($row)); ?>"
													placeholder="<?php esc_html_e('Please Enter matching text of the image here', 'w3speedster'); ?>"><button
													type="button" class="text-white rem-row bg-danger"><i
														class="fa fa-times"></i></button>
											</div>
											<?php
										}
									}} ?>
									
								</div>
								<div class="cdn_input_box plus">
									<button type="button" data-name="exclude_lazy_load" data-placeholder="<?php esc_html_e('Please Enter matching text of the image here', 'w3speedster'); ?>" class="btn small text-white bg-success add_more_row"><?php esc_html_e('Add Rule', 'w3speedster'); ?></button>
								</div>

							</div>

						</div>
					</div>
					<hr>
					<div class="cdn_resources <?php echo esc_attr($hidden_class); ?>">
						<div class="w3d-flex gap20 align-item-baseline">
							<label for="Exclude Pages From Optimization"><?php esc_html_e('Exclude Pages From Optimization', 'w3speedster'); ?><span
									class="info"></span><span class="info-display"><?php esc_html_e('Enter slug of the url to exclude from optimization. For  eg. (/blog/). For home page, enter home url.', 'w3speedster'); ?></span></label>
							<div class="input_box">
								<div class="single-row">
									<?php
									//$result['exclude_pages_from_optimization'] = 'hello';
                                    if(array_key_exists('exclude_pages_from_optimization', $result)){
									foreach (explode("\r\n", $result['exclude_pages_from_optimization']) as $row) {
										if (!empty(trim($row))) {
											?>
											<div class="cdn_input_box minus w3d-flex">
												<input type="text" name="exclude_pages_from_optimization[]"
													value="<?php echo  esc_attr(trim($row)); ?>" placeholder="<?php esc_html_e('Please Enter Page Url', 'w3speedster'); ?>"><button
													type="button" class="text-white rem-row bg-danger"><i
														class="fa fa-times"></i></button>
											</div>
										<?php
										}
									}}
									?>
									
								</div>
								<div class="cdn_input_box plus">
									<button type="button" data-name="exclude_pages_from_optimization" data-placeholder="<?php esc_html_e('Please Enter Page Url', 'w3speedster'); ?>" class="btn small text-white bg-success add_more_row"><?php esc_html_e('Add Rule', 'w3speedster'); ?></button>
								</div>

							</div>

						</div>
					</div>
					<hr>
					<div class="css_box cdn_resources ">
						<div class="w3d-flex gap20 align-item-baseline">
						<label><?php esc_html_e('Exclude Link Tag CSS from Optimization', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('Enter matching text of css link url, which are to be excluded from css optimization. Each Exclusion to be entered in a new line', 'w3speedster'); ?></span></label>
						<div class="input_box">
							<div class="single-row">
									<?php        
                                    if(array_key_exists('exclude_css', $result)){
									foreach (explode("\r\n", $result['exclude_css']) as $row) {
										if (!empty(trim($row))) {
											?>
											<div class="cdn_input_box minus w3d-flex">
												<input type="text" name="exclude_css[]" value="<?php echo  esc_attr(trim($row)); ?>"
													placeholder="<?php esc_html_e('Please Enter part of link tag css here', 'w3speedster'); ?>"><button type="button"
													class="text-white rem-row bg-danger"><i
														class="fa fa-times"></i></button>
											</div>
											<?php
										}
									}} ?>
								</div>
								<div class="cdn_input_box plus">
									<button type="button"
										data-name="exclude_css" data-placeholder="<?php esc_html_e('Please Enter part of link tag css here', 'w3speedster'); ?>" class="btn small text-white bg-success add_more_row"><?php esc_html_e('Add Rule', 'w3speedster'); ?></button>
								</div>
						</div>
						</div>

					</div>
					<hr>

					<div class="css_box cdn_resources">
					<div class="w3d-flex gap20 align-item-baseline">
						<label><?php esc_html_e('Force Lazy Load Link Tag CSS', 'w3speedster'); ?> <span class="info"></span><span class="info-display"><?php esc_html_e('Enter matching text of css link url, which are forced to be lazyloaded. Each Exclusion to be entered in a new line', 'w3speedster'); ?></span></label>
						<div class="input_box">
							<div class="single-row">
									<?php        
                                    if(array_key_exists('force_lazyload_css', $result)){
									foreach (explode("\r\n", $result['force_lazyload_css']) as $row) {
										if (!empty(trim($row))) {
											?>
											<div class="cdn_input_box minus w3d-flex">
												<input type="text" name="force_lazyload_css[]" value="<?php echo  esc_attr(trim($row)); ?>"
													placeholder="<?php esc_html_e('Please Enter part of link tag css here', 'w3speedster'); ?>"><button type="button"
													class="text-white rem-row bg-danger"><i
														class="fa fa-times"></i></button>
											</div>
											<?php
										}
									}} ?>
								</div>
								<div class="cdn_input_box plus">
									<button type="button"
										data-name="force_lazyload_css" data-placeholder="<?php esc_html_e('Please Enter part of link tag css here', 'w3speedster'); ?>" class="btn small text-white bg-success add_more_row"><?php esc_html_e('Add Rule', 'w3speedster'); ?></button>
								</div>
						</div>
						</div>
					</div>
					<hr>
					<div class="css_box cdn_resources">
					<div class="w3d-flex gap20 align-item-baseline">
						<label><?php esc_html_e('Exclude Pages from CSS Optimization', 'w3speedster'); ?> <span class="info"></span><span class="info-display"><?php esc_html_e('Enter slug of the page to exclude from css optimization', 'w3speedster'); ?></span></label>
						<div class="input_box">
							<div class="single-row">
									<?php        
                                    if(array_key_exists('exclude_page_from_load_combined_css', $result)){
									foreach (explode("\r\n", $result['exclude_page_from_load_combined_css']) as $row) {
										if (!empty(trim($row))) {
											?>
											<div class="cdn_input_box minus w3d-flex">
												<input type="text" name="exclude_page_from_load_combined_css[]" value="<?php echo  esc_attr(trim($row)); ?>"
													placeholder="<?php esc_html_e('Please Enter Page Url', 'w3speedster'); ?>"><button type="button"
													class="text-white rem-row bg-danger"><i
														class="fa fa-times"></i></button>
											</div>
											<?php
										}
									}} ?>
								</div>
								<div class="cdn_input_box plus">
									<button type="button"
										data-name="exclude_page_from_load_combined_css" data-placeholder="<?php esc_html_e('Please Enter Page Url', 'w3speedster'); ?>" class="btn small text-white bg-success add_more_row"><?php esc_html_e('Add Rule', 'w3speedster'); ?></button>
								</div>
						</div>
						</div>
					</div>
					<hr>
					<div class="js_box cdn_resources">
					<div class="w3d-flex gap20 align-item-baseline">
						<label><?php esc_html_e('Force Lazy Load Javascript', 'w3speedster'); ?> <span class="info"></span><span class="info-display"><?php esc_html_e('Enter matching text of inline javascript which needs to be forced to lazyload. Each lazyload javascript to be entered in a new line ', 'w3speedster'); ?></span></label>
						<div class="input_box">
								<div class="single-row">
									<?php
                                    if(array_key_exists('force_lazy_load_inner_javascript', $result)){
									foreach (explode("\r\n", $result['force_lazy_load_inner_javascript']) as $row) {
										if (!empty(trim($row))) {
											?>
											<div class="cdn_input_box minus w3d-flex">
												<input type="text" name="force_lazy_load_inner_javascript[]" value="<?php echo  esc_attr(trim($row)); ?>"
													placeholder="<?php esc_html_e('Please Enter matching text of the inline javascript here', 'w3speedster'); ?>"><button type="button"
													class="text-white rem-row bg-danger"><i
														class="fa fa-times"></i></button>
											</div>
											<?php
										}
									}} ?>
								</div>
								<div class="cdn_input_box plus">
									<button type="button"
										data-name="force_lazy_load_inner_javascript" data-placeholder="<?php esc_html_e('Please Enter matching text of the inline javascript here', 'w3speedster'); ?>" class="btn small text-white bg-success add_more_row"><?php esc_html_e('Add Rule', 'w3speedster'); ?></button>
								</div>

						</div>
						</div>
					</div>
					<hr>
					<div class="js_box cdn_resources">
					<div class="w3d-flex gap20 align-item-baseline">
						<label><?php esc_html_e('Exclude Javascript Tags from Lazyload', 'w3speedster'); ?> <span class="info"></span><span
								class="info-display"><?php esc_html_e('Enter matching text of javascript url, which are to be excluded from javascript optimization. Each exclusion to be entered in new line.', 'w3speedster'); ?></span></label>
						<div class="input_box">
								<div class="single-row">
									<?php
                                    if(array_key_exists('exclude_javascript', $result)){
									foreach (explode("\r\n", $result['exclude_javascript']) as $row) {
										if (!empty(trim($row))) {
											?>
											<div class="cdn_input_box minus w3d-flex">
												<input type="text" name="exclude_javascript[]" value="<?php echo  esc_attr(trim($row)); ?>"
													placeholder="<?php esc_html_e('Please Enter matching text of the javascript here', 'w3speedster'); ?>"><button type="button"
													class="text-white rem-row bg-danger"><i
														class="fa fa-times"></i></button>
											</div>
											<?php
										}
									}} ?>
								</div>
								<div class="cdn_input_box plus">
									<button type="button"
										data-name="exclude_javascript" data-placeholder="<?php esc_html_e('Please Enter matching text of the javascript here', 'w3speedster'); ?>" class="btn small text-white bg-success add_more_row"><?php esc_html_e('Add Rule', 'w3speedster'); ?></button>
								</div>

						</div>
						</div>
					</div>
					<hr>
					<div class="js_box cdn_resources">
					<div class="w3d-flex gap20 align-item-baseline">
						<label><?php esc_html_e('Exclude Inline Javascript from Lazyload', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('Enter matching text of inline script url, which needs to be excluded from deferring of javascript. Each exclusion to be entered in a new line.', 'w3speedster'); ?></span></label>
						<div class="input_box">
								<div class="single-row">
									<?php
                                    if(array_key_exists('exclude_inner_javascript', $result)){
									foreach (explode("\r\n", $result['exclude_inner_javascript']) as $row) {
										if (!empty(trim($row))) {
											?>
											<div class="cdn_input_box minus w3d-flex">
												<input type="text" name="exclude_inner_javascript[]" value="<?php echo  esc_attr(trim($row)); ?>"
													placeholder="<?php esc_html_e('Please Enter matching text of the inline javascript here', 'w3speedster'); ?>"><button type="button"
													class="text-white rem-row bg-danger"><i
														class="fa fa-times"></i></button>
											</div>
											<?php
										}
									}} ?>
								</div>
								<div class="cdn_input_box plus">
									<button type="button"
										data-name="exclude_inner_javascript" data-placeholder="<?php esc_html_e('Please Enter matching text of the inline javascript here', 'w3speedster'); ?>" class="btn small text-white bg-success add_more_row"><?php esc_html_e('Add Rule', 'w3speedster'); ?></button>
								</div>

						</div>
						</div>
					</div>
					<hr>
					<div class="js_box cdn_resources">
					<div class="w3d-flex gap20 align-item-baseline">
						<label><?php esc_html_e('Exclude Pages from Javascript Optimization', 'w3speedster'); ?> <span class="info"></span><span
								class="info-display"><?php esc_html_e('Enter slug of the page to exclude from javascript optimization', 'w3speedster'); ?></span></label>
						<div class="input_box">
								<div class="single-row">
									<?php
                                    if(array_key_exists('exclude_page_from_load_combined_js', $result)){
									foreach (explode("\r\n", $result['exclude_page_from_load_combined_js']) as $row) {
										if (!empty(trim($row))) {
											?>
											<div class="cdn_input_box minus w3d-flex">
												<input type="text" name="exclude_page_from_load_combined_js[]" value="<?php echo  esc_attr(trim($row)); ?>"
													placeholder="<?php esc_html_e('Please Enter Js Page Url', 'w3speedster'); ?>"><button type="button"
													class="text-white rem-row bg-danger"><i
														class="fa fa-times"></i></button>
											</div>
											<?php
										}
									}} ?>
								</div>
								<div class="cdn_input_box plus">
									<button type="button"
										data-name="exclude_page_from_load_combined_js" data-placeholder="<?php esc_html_e('Please Enter Js Page Url', 'w3speedster'); ?>" class="btn small text-white bg-success add_more_row"><?php esc_html_e('Add Rule', 'w3speedster'); ?></button>
								</div>

							</div>
							</div>
					</div>
					<hr>
					<div class="single-hook_btn">
					<div class="save-changes w3d-flex gap10">
						<input type="button" value="Save Changes" class="btn hook_submit">
						<div class="in-progress w3d-flex save-changes-loader" style="display:none">
							<img src="<?php echo esc_url(W3SPEEDSTER_PLUGIN_URL); ?>assets/images/loader-gif.gif"
								alt="loader" class="loader-img">
						</div>
					</div>
				</section>
				<section id="w3_custom_code" class="tab-pane fade">
					<div class="header w3d-flex gap20">
						<div class="heading_container">
							<h4 class="heading">
								<?php esc_html_e('Custom Code', 'w3speedster'); ?>
							</h4>
							<span class="info"><a href="https://w3speedster.com/w3speedster-documentation/"><?php esc_html_e('More info', 'w3speedster'); ?>?
								</a></span>
						</div>
						<div class="icon_container"> <img
								src="<?php echo esc_url(W3SPEEDSTER_PLUGIN_URL); ?>assets/images/custom-code-icon1.webp"></div>
					</div>
					<hr>
					<div class="css_box" id="css-box">
						<label><?php esc_html_e('Custom CSS to Load on Page Load', 'w3speedster'); ?> <span class="info"></span><span class="info-display"><?php esc_html_e('Enter custom css which works only when css optimization is applied', 'w3speedster'); ?></span></label>
						<div class="fullview">
						<textarea name="custom_css" rows="10" title="Custom css to load with preload css"
							placeholder="<?php esc_html_e('Please Enter css without the style tag.', 'w3speedster'); ?>"><?php if (!empty ($result['custom_css']))
								echo esc_html(stripslashes($result['custom_css'])); ?></textarea>
						<button id ="btn" type="button" data-id="css-box" class="expend-textarea" title="Resize editor">
						<svg class="maximize" width="25" height="25" viewBox="0 0 26 26" xmlns="http://www.w3.org/2000/svg" transform="scale(1 -1)"><g data-name="Group 710"><path data-name="Path 1492" d="M24 26h-5v-2h5v-5h2v5a2.006 2.006 0 0 1-2 2m-4-4H8a1 1 0 0 1 0-2h12V6H6v11a1 1 0 0 1-2 0V6a2.006 2.006 0 0 1 2-2h14a2.006 2.006 0 0 1 2 2v14a2.006 2.006 0 0 1-2 2M2 2v5H0V2a2.006 2.006 0 0 1 2-2h5v2Z"/></g></svg>
						<svg class="minimize" width="25" height="25" viewBox="5 5 26 26" xmlns="http://www.w3.org/2000/svg"><path d="M28 8H14a2 2 0 0 0-2 2v2h2v-2h14v10h-2v2h2a2 2 0 0 0 2-2V10a2 2 0 0 0-2-2" class="clr-i-outline clr-i-outline-path-1"/><path d="M22 14H8a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V16a2 2 0 0 0-2-2M8 26V16h14v10Z" class="clr-i-outline clr-i-outline-path-2"/><path fill="none" d="M0 0h36v36H0z"/></svg>
						</button></div>
					</div>
					<hr>
					<div class="js_box" id="js-box">
						<label><?php esc_html_e('Custom Javascript to Load on Page Load', 'w3speedster'); ?> <span class="info"></span><span class="info-display"><?php esc_html_e('Enter javascript code which needs to be loaded before page load.', 'w3speedster'); ?></span></label>
						<div class="fullview">
						<textarea name="custom_javascript" rows="10" title="Custom "
							placeholder="<?php esc_html_e('Please javascript without script tag', 'w3speedster'); ?>"><?php if (!empty ($result['custom_javascript'])) echo esc_html(stripslashes($result['custom_javascript'])); ?></textarea>
						<button id ="btn" type="button" data-id="js-box" class="expend-textarea" title="Resize editor">
						<svg class="maximize" width="25" height="25" viewBox="0 0 26 26" xmlns="http://www.w3.org/2000/svg" transform="scale(1 -1)"><g data-name="Group 710"><path data-name="Path 1492" d="M24 26h-5v-2h5v-5h2v5a2.006 2.006 0 0 1-2 2m-4-4H8a1 1 0 0 1 0-2h12V6H6v11a1 1 0 0 1-2 0V6a2.006 2.006 0 0 1 2-2h14a2.006 2.006 0 0 1 2 2v14a2.006 2.006 0 0 1-2 2M2 2v5H0V2a2.006 2.006 0 0 1 2-2h5v2Z"/></g></svg>
						<svg class="minimize" width="25" height="25" viewBox="5 5 26 26" xmlns="http://www.w3.org/2000/svg"><path d="M28 8H14a2 2 0 0 0-2 2v2h2v-2h14v10h-2v2h2a2 2 0 0 0 2-2V10a2 2 0 0 0-2-2" class="clr-i-outline clr-i-outline-path-1"/><path d="M22 14H8a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V16a2 2 0 0 0-2-2M8 26V16h14v10Z" class="clr-i-outline clr-i-outline-path-2"/><path fill="none" d="M0 0h36v36H0z"/></svg>
						</button></div>
						<div class="w3d-flex gap20">
							<div class="w3d-flex ">
								<label for="load-as-file"><?php esc_html_e('Load as file', 'w3speedster'); ?> &nbsp;</label>
								<input type="checkbox" name="custom_javascript_file" <?php if (!empty ($result['custom_javascript_file']) && $result['custom_javascript_file'] == "on")
									echo "checked"; ?> id="load-as-file">
							</div>
							&nbsp;
							<div class="w3d-flex ">
								<label for="defer"><?php esc_html_e('Defer', 'w3speedster'); ?> &nbsp;</label>
								<input type="checkbox" name="custom_javascript_defer" <?php if (!empty ($result['custom_javascript_defer']) && $result['custom_javascript_defer'] == "on")
									echo "checked"; ?> id="defer">
							</div>
						</div>
					</div>
					<hr>
					<div class="js_box" id="custom-js-box">
						<label><?php esc_html_e('Custom Javascript to Load After Page Load', 'w3speedster'); ?> <span class="info"></span><span class="info-display"><?php esc_html_e('Enter javascript which loads after page load load.', 'w3speedster'); ?></span></label>
						<div class="fullview">
						<textarea name="custom_js" rows="10" title="Custom "
							placeholder="<?php esc_html_e('Please Enter Js without the script tag', 'w3speedster'); ?>"><?php if (!empty ($result['custom_js']))
								echo esc_html(stripslashes($result['custom_js'])); ?></textarea>
						<button id ="btn" type="button" data-id="custom-js-box" class="expend-textarea" title="Resize editor">
						<svg class="maximize" width="25" height="25" viewBox="0 0 26 26" xmlns="http://www.w3.org/2000/svg" transform="scale(1 -1)"><g data-name="Group 710"><path data-name="Path 1492" d="M24 26h-5v-2h5v-5h2v5a2.006 2.006 0 0 1-2 2m-4-4H8a1 1 0 0 1 0-2h12V6H6v11a1 1 0 0 1-2 0V6a2.006 2.006 0 0 1 2-2h14a2.006 2.006 0 0 1 2 2v14a2.006 2.006 0 0 1-2 2M2 2v5H0V2a2.006 2.006 0 0 1 2-2h5v2Z"/></g></svg>
						<svg class="minimize" width="25" height="25" viewBox="5 5 26 26" xmlns="http://www.w3.org/2000/svg"><path d="M28 8H14a2 2 0 0 0-2 2v2h2v-2h14v10h-2v2h2a2 2 0 0 0 2-2V10a2 2 0 0 0-2-2" class="clr-i-outline clr-i-outline-path-1"/><path d="M22 14H8a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V16a2 2 0 0 0-2-2M8 26V16h14v10Z" class="clr-i-outline clr-i-outline-path-2"/><path fill="none" d="M0 0h36v36H0z"/></svg>
						</button></div>
					</div>
					<hr>
					<div class="save-changes w3d-flex gap10">
						<input type="button" value="<?php esc_html_e('Save Changes', 'w3speedster'); ?>" class="btn hook_submit">
						<div class="in-progress w3d-flex save-changes-loader" style="display:none">
							<img src="<?php echo esc_url(W3SPEEDSTER_PLUGIN_URL); ?>assets/images/loader-gif.gif"
								alt="loader" class="loader-img">
						</div>
					</div>
				</section>
				<section id="cache" class="tab-pane fade">
					<div class="header w3d-flex gap20">
						<div class="heading_container">
							<h4 class="heading">
								<?php esc_html_e('Cache', 'w3speedster'); ?>
							</h4>
							<span class="info"><a href="https://w3speedster.com/w3speedster-documentation/#Cache"><?php esc_html_e('More info', 'w3speedster'); ?>?
								</a></span>
						</div>
						<div class="icon_container"> <img
								src="<?php echo esc_url(W3SPEEDSTER_PLUGIN_URL); ?>assets/images/caches-icon.webp"></div>
					</div>
					<hr>
					<div class="caches_box">
						<div class="w3d-flex gap20 ">
							<label><?php esc_html_e('Delete HTML cache', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('Delete HTML cache when you do any changes', 'w3speedster'); ?></span></label>
							<button class="btn" type="button" id="del_html_cache">
								<?php esc_html_e('Delete Now', 'w3speedster'); ?>
							</button>
							<div class="in-progress w3d-flex delete_html_cache" style="display:none">
								<img src="<?php echo esc_url(W3SPEEDSTER_PLUGIN_URL); ?>assets/images/loader-gif.gif" alt="loader" class="loader-img">
							<small class="extra-small m-0">&nbsp;<em>&nbsp;<?php esc_html_e('Deleting HTML Cache...', 'w3speedster'); ?></em></small>
							</div>
						</div>
						<div class="w3d-flex gap20 ">
							<label><?php esc_html_e('Delete JS/CSS Cache', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('Delete javascript and css combined and minified files', 'w3speedster'); ?></span></label>
							<button class="btn" type="button" id="del_js_css_cache">
								<?php esc_html_e('Delete Now', 'w3speedster'); ?>
							</button>
							<div class="in-progress w3d-flex delete_css_js_cache" style="display:none">
								<img src="<?php echo esc_url(W3SPEEDSTER_PLUGIN_URL); ?>assets/images/loader-gif.gif" alt="loader" class="loader-img">
							<small class="extra-small m-0">&nbsp;<em>&nbsp;<?php esc_html_e('Deleting JS/Css Cache...', 'w3speedster'); ?></em></small>
							</div>
						</div>
						<div class="w3d-flex gap20 ">
							<label><?php esc_html_e('Delete critical css cache', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('Delete critical css cache only when you have made any changes to style. This may take considerable amount of time to regenerate depending upon the pages on the site', 'w3speedster'); ?></span></label>
							<button class="btn" type="button" id="del_critical_css_cache">
								<?php esc_html_e('Delete Now', 'w3speedster'); ?>
							</button>
							<div class="in-progress w3d-flex delete_critical_css_cache" style="display:none">
								<img src="<?php echo esc_url(W3SPEEDSTER_PLUGIN_URL); ?>assets/images/loader-gif.gif" alt="loader" class="loader-img">
							<small class="extra-small m-0">&nbsp;<em>&nbsp;<?php esc_html_e('Deleting Critical Css Cache...', 'w3speedster'); ?></em></small>
							</div>
						</div>
					</div>
				</section>
				<section id="hooks" class="tab-pane fade">
				<div class="header w3d-flex gap20">
					<div class="heading_container">
						<h4 class="heading"><?php esc_html_e('Plugin Hooks', 'w3speedster'); ?></h4>
						<span class="info"><a href="https://w3speedster.com/w3speedster-documentation/#plugin-hooks"><?php esc_html_e('More info', 'w3speedster'); ?>?</a></span>
					</div>
					<div class="icon_container"> <img src="https://speedwp.webplus.me/wp-content/plugins/w3speedster-wp/assets/images/php-hook.webp"></div>
				</div>
				<div class="search_hooks">
					<input class="pl_search_field" autocomplete="off" name="temp_input" type="search" placeholder="<?php esc_html_e('Search...', 'w3speedster'); ?>"/>
					<button type="button" class="clear_field" style="display:none">
					<svg width="25" height="25" viewBox="0 0 36 36" xmlns="http://www.w3.org/2000/svg"><path d="m19.41 18 8.29-8.29a1 1 0 0 0-1.41-1.41L18 16.59l-8.29-8.3a1 1 0 0 0-1.42 1.42l8.3 8.29-8.3 8.29A1 1 0 1 0 9.7 27.7l8.3-8.29 8.29 8.29a1 1 0 0 0 1.41-1.41Z"/></svg>
					</button>
					<p></p>
					<div class="entry_search_contaner" style="display:none"></div>
				</div>
			<div class="usedhooks">
					<h5 style="margin-top:22px;margin-bottom: 5px;"><strong><?php esc_html_e('Used Hooks', 'w3speedster'); ?></strong></h5>
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

						foreach($hooks as $key => $hook){
							if(isset($result[$key]) && !empty($result[$key])){
								echo '<button type="button" data-label="'.esc_attr($hook).'" data-filter="'.esc_attr(str_replace(' ', '', strtolower($hook))).'" class="used_hook_btn btn">'.esc_html($hook).'</button>';
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
					<label><span class="main-label"><?php esc_html_e('W3speedster Pre Start Optimization', 'w3speedster'); ?></span><span class="info"></span>
						<span class="info-display">
							<p><strong><?php esc_html_e('Function', 'w3speedster'); ?>:</strong> <?php esc_html_e('w3SpeedsterPreStartOptimization', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Description', 'w3speedster'); ?>:</strong> <?php esc_html_e('Modify page content pre optimization.', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Parameter:', 'w3speedster'); ?></strong> <?php esc_html_e('$html = Content visible in pages view source.', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Return:', 'w3speedster'); ?></strong><?php esc_html_e(' 1 – Reflect the changes done in html of the page.', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Example:', 'w3speedster'); ?></strong><br>
<pre>
function w3SpeedsterPreStartOptimization($html){
$html = str_replace('Existing content','Changed content',$html);
return $html;
}
</pre>
							</p>
						</span>
					</label>
					<code > function w3SpeedsterPreStartOptimization($html){</code>
					<textarea rows="5" cols="100" id="hook_pre_start_opt" name="hook_pre_start_opt"
						class="hook_before_start"><?php if (!empty ($result['hook_pre_start_opt']))
							echo esc_html(stripslashes($result['hook_pre_start_opt'])); ?></textarea>
					<code > return $html; <br> }</code>
					</div>
					
					<div class="single-hook">
					<label><span class="main-label"><?php esc_html_e('W3speedster Before Start Optimization', 'w3speedster'); ?></span><span class="info"></span><span
							class="info-display">
							<p><?php esc_html_e('Function:', 'w3speedster'); ?> <?php esc_html_e('w3SpeedsterBeforeStartOptimization', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Description:', 'w3speedster'); ?></strong><?php esc_html_e(' W3Speedster allows you to make changes to the HTML on your site before actually starting the optimization. For instance replace or add in html.', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Parameter:', 'w3speedster'); ?></strong><?php esc_html_e(' $html – full html of the page.', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Return:', 'w3speedster'); ?></strong><?php esc_html_e(' 1 – Reflect the changes done in html of the page.', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Example:', 'w3speedster'); ?></strong><br>
<pre>
function w3SpeedsterBeforeStartOptimization($html){
$html = str_replace(array(""),array(""), $html);
return $html;
}
</pre>
							</p>
						</span></label>
					<code > function w3SpeedsterBeforeStartOptimization($html){</code>
					<textarea rows="5" cols="100" id="hook_before_start_opt" name="hook_before_start_opt"
						class="hook_before_start"><?php if (!empty ($result['hook_before_start_opt']))
							echo esc_html(stripslashes($result['hook_before_start_opt'])); ?></textarea>
					<code > return $html;<br> }</code>
					</div>
					
					<div class="single-hook">
					<label><span class="main-label"><?php esc_html_e('W3speedster After Optimization', 'w3speedster'); ?></span>
						<span class="info"></span>
						<span class="info-display">
							<p><?php esc_html_e('Function:', 'w3speedster'); ?> <?php esc_html_e('w3SpeedsterAfterOptimization', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Description:', 'w3speedster'); ?></strong><?php esc_html_e(' W3Speedster allows you to make changes to the HTML on your site after the page is optimized by the plugin. For instance replace or add in html.', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Parameter:', 'w3speedster'); ?></strong><?php esc_html_e('$html – full html of the page.', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Return:', 'w3speedster'); ?></strong><?php esc_html_e(' 1 – Reflect the changes done in html of the page.', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Example:', 'w3speedster'); ?></strong><br>
<pre>
function w3SpeedsterAfterOptimization($html){
$html = str_replace(array(image.png''),array(image-100x100.png''), $html);
return $html;
} 
</pre>
							</p>
						</span>
					</label>
					<code > function w3SpeedsterAfterOptimization($html){</code>
					<textarea rows="5" cols="100" id="hook_after_opt" name="hook_after_opt"
						class="hook_before_start"><?php if (!empty ($result['hook_after_opt']))
							echo esc_html(stripslashes($result['hook_after_opt'])); ?></textarea>
					<code > return $html; <br>}</code>
					</div>
					
					<div class="single-hook">
					<label><span class="main-label"><?php esc_html_e('W3speedster Inner JS Customize', 'w3speedster'); ?></span>
					<span class="info"></span>
					<span class="info-display">
						<p><?php esc_html_e('Function:', 'w3speedster'); ?> <?php esc_html_e('w3SpeedsterInnerJsCustomize', 'w3speedster'); ?></p>
						<p><strong><?php esc_html_e('Description:', 'w3speedster'); ?></strong><?php esc_html_e(' If you want to make changes in your inline JavaScript, W3Speedster allows you to make changes in Inline JavaScript (for instance making changes in inline script you have to enter the unique text from the script to identify the script).', 'w3speedster'); ?></p>
						<p><strong><?php esc_html_e('Parameter:', 'w3speedster'); ?></strong><?php esc_html_e('$script_text- The content of the script.', 'w3speedster'); ?></p>
						<p><strong><?php esc_html_e('Return:', 'w3speedster'); ?></strong><?php esc_html_e(' $script_text – Content of the script after changes.', 'w3speedster'); ?></p>
						<p><strong><?php esc_html_e('Example:', 'w3speedster'); ?></strong><br>
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
					<code > function w3SpeedsterInnerJsCustomize($script_text){</code>
					<textarea rows="5" cols="100" id="hook_inner_js_customize" name="hook_inner_js_customize"
						class="hook_before_start"><?php if (!empty ($result['hook_inner_js_customize']))
							echo esc_html(stripslashes($result['hook_inner_js_customize'])); ?></textarea>
					<code > return $script_text;<br> }</code>
					</div>
					
					<div class="single-hook">
					<label><span class="main-label"><?php esc_html_e('W3speedster Inner JS Exclude', 'w3speedster'); ?></span><span class="info"></span>
						<span class="info-display">
							<p><?php esc_html_e('Function:', 'w3speedster'); ?> <?php esc_html_e('w3SpeedsterInnerJsExclude', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Description:', 'w3speedster'); ?></strong><?php esc_html_e(' Exclude the script tag from lazy loading, which is present in the pages view source. ', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Parameter:', 'w3speedster'); ?></strong> <br><?php esc_html_e('$inner_js = The script tag s content is visible in the page s view source <br> $exclude_js_bool = 0(default) || 1 ', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Return:', 'w3speedster'); ?></strong> <?php esc_html_e('1', 'w3speedster'); ?> </p>
							<p><strong><?php esc_html_e('Example:', 'w3speedster'); ?></strong><br>
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
					<code >function w3SpeedsterInnerJsExclude($exclude_js_bool,$inner_js){</code>
					<textarea rows="5" cols="100" id="hook_inner_js_exclude" name="hook_inner_js_exclude"
						class="hook_before_start"><?php if (!empty ($result['hook_inner_js_exclude']))
							echo esc_html(stripslashes($result['hook_inner_js_exclude'])); ?></textarea>
					<code > return $exclude_js_bool; <br>}</code>
					</div>
					
					<div class="single-hook">
					<label><span class="main-label"><?php esc_html_e('W3speedster Internal JS Customize', 'w3speedster'); ?></span><span class="info"></span>
						<span class="info-display">
							<p><?php esc_html_e('Function:', 'w3speedster'); ?> <?php esc_html_e('w3SpeedsterInternalJsCustomize', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Description:', 'w3speedster'); ?></strong><?php esc_html_e(' If you wish to make changes in JavaScript files, W3Speedster allows you to make changes in JavaScript Files.', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Parameter:', 'w3speedster'); ?></strong>
								<?php esc_html_e('$path- Path of the JS file.', 'w3speedster'); ?><br>
								<?php esc_html_e('$string – javascript you want to make changes in.', 'w3speedster'); ?>
							</p>
							<p><strong><?php esc_html_e('Return:', 'w3speedster'); ?></strong><?php esc_html_e(' $string– make changes in the internal JS file.', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Example:', 'w3speedster'); ?></strong><br>
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
					<code >function w3SpeedsterInternalJsCustomize($string,$path){</code>
					<textarea rows="5" cols="100" id="hook_internal_js_customize" name="hook_internal_js_customize"
						class="hook_before_start"><?php if (!empty ($result['hook_internal_js_customize']))
							echo esc_html(stripslashes($result['hook_internal_js_customize'])); ?></textarea>
					<code > return $string; <br>}</code>
					</div>
					
					<div class="single-hook">
					<label><span class="main-label"><?php esc_html_e('W3speedster Internal Css Customize', 'w3speedster'); ?></span>
					<span class="info"></span>
					<span class="info-display">
						<p><?php esc_html_e('Function:', 'w3speedster'); ?> <?php esc_html_e('w3SpeedsterInternalCssCustomize', 'w3speedster'); ?></p>
						<p><strong><?php esc_html_e('Description:', 'w3speedster'); ?></strong><?php esc_html_e(' If you want to make changes in your CSS file, W3Speedster allows you to make changes in stylesheet files.', 'w3speedster'); ?></p>
						<p><strong><?php esc_html_e('Parameter:', 'w3speedster'); ?></strong>
							<?php esc_html_e('$css- Css content of the file.', 'w3speedster'); ?><br>
							<?php esc_html_e('$path- path of css file.', 'w3speedster'); ?>
						</p>
						<p><strong><?php esc_html_e('Return:', 'w3speedster'); ?></strong><?php esc_html_e(' $css – make the required changes in CSS files.', 'w3speedster'); ?></p>
						<p><strong><?php esc_html_e('Example:', 'w3speedster'); ?></strong><br>
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
					<code >function w3SpeedsterInternalCssCustomize($css,$path){</code>
					<textarea rows="5" cols="100" id="hook_internal_css_customize" name="hook_internal_css_customize"
						class="hook_before_start"><?php if (!empty ($result['hook_internal_css_customize']))
							echo esc_html(stripslashes($result['hook_internal_css_customize'])); ?></textarea>
					<code > return $css; <br>}</code>
					</div>
					
					<div class="single-hook">
					<label><span class="main-label"><?php esc_html_e('W3speedster Internal Css Minify', 'w3speedster'); ?></span><span class="info"></span>	<span class="info-display">
							<p><?php esc_html_e('Function:', 'w3speedster'); ?> <?php esc_html_e('W3speedup_internal_css_minify', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Description:', 'w3speedster'); ?></strong><?php esc_html_e(' If you don’t want to minify, W3Speedster allows you to exclude stylesheet files from minify.', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Parameter:', 'w3speedster'); ?></strong>
								<?php esc_html_e('$path- path of css file.<br>$css- Css content of the file. ', 'w3speedster'); ?><br>
								<?php esc_html_e('$css_minify- 0 || 1 (default)', 'w3speedster'); ?> 
							</p>
							<p><strong><?php esc_html_e('Return:', 'w3speedster'); ?></strong><?php esc_html_e(' 1 – it will exclude the entered css file from minification.', 'w3speedster'); ?><br><?php esc_html_e(' 0 – it will not exclude the entered css file from minification.', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Example:', 'w3speedster'); ?></strong><br>
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
					<code >function w3SpeedsterInternalCssMinify($path,$css,$css_minify){</code>
					<textarea rows="5" cols="100" id="hook_internal_css_minify" name="hook_internal_css_minify"
						class="hook_before_start"><?php if (!empty ($result['hook_internal_css_minify']))
							echo esc_html(stripslashes($result['hook_internal_css_minify'])); ?></textarea>
					<code > return $css_minify; <br>}</code>
					</div>
					
					<div class="single-hook">
					<label><span class="main-label"><?php esc_html_e('W3speedster No Critical Css', 'w3speedster'); ?></span>
						<span class="info"></span>
						<span class="info-display">
							<p><?php esc_html_e('Function:', 'w3speedster'); ?> <?php esc_html_e('w3SpeedsterNoCriticalCss', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Description:', 'w3speedster'); ?></strong><?php esc_html_e(' W3Speedster allows you to exclude the pages from the Critical CSS (like search pages).', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Parameter:', 'w3speedster'); ?></strong>
								<?php esc_html_e('$url- Stores the url of the page. ', 'w3speedster'); ?><br>
								<?php esc_html_e('$ignore_critical_css- 0 (default) || 1 ', 'w3speedster'); ?>
							</p>
							<p><strong><?php esc_html_e('Return:', 'w3speedster'); ?></strong><?php esc_html_e(' 1 – it will exclude the page you do not wish to create critical CSS.', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Example:', 'w3speedster'); ?></strong><br>
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
					<code >function w3SpeedsterNoCriticalCss($url,$ignore_critical_css){</code>
					<textarea rows="5" cols="100" id="hook_no_critical_css" name="hook_no_critical_css"
						class="hook_before_start"><?php if (!empty ($result['hook_no_critical_css']))
							echo esc_html(stripslashes($result['hook_no_critical_css'])); ?></textarea>
					<code > return $ignore_critical_css; <br>}</code>
					</div>
					
					<div class="single-hook">
					<label><span class="main-label"><?php esc_html_e('W3speedster Customize Critical Css', 'w3speedster'); ?></span><span class="info"></span>
					<span class="info-display">
						<p><?php esc_html_e('Function:', 'w3speedster'); ?> <?php esc_html_e('w3SpeedsterCustomizeCriticalCss', 'w3speedster'); ?></p>
						<p><strong><?php esc_html_e('Description:', 'w3speedster'); ?></strong><?php esc_html_e(' If you wish to make any changes in Critical CSS, W3Speedster allows you to make changes in generated Critical CSS. For instance if you want to replace/ remove any string/URL from critical CSS (like @font-face { font-family:”Courgette”; to @font-face { ).', 'w3speedster'); ?></p>
						<p><strong><?php esc_html_e('Parameter:', 'w3speedster'); ?></strong>
						<?php esc_html_e('$critical_css- Critical Css of the page.', 'w3speedster'); ?>
						</p>
						<p><strong><?php esc_html_e('Return:', 'w3speedster'); ?></strong><?php esc_html_e('$critical_css – Reflect the changes made in critical css.', 'w3speedster'); ?></p>
						<p><strong><?php esc_html_e('Example:', 'w3speedster'); ?></strong><br>
<pre>	
function w3SpeedsterCustomizeCriticalCss($critical_css){
	$critical_css = str_replace('@font-face { font-family:"Courgette";', ' ',$critical_css);
	return $critical_css;
}
</pre>
						</p>
					</span>
				</label>
					<code >function w3SpeedsterCustomizeCriticalCss($critical_css){</code>
					<textarea rows="5" cols="100" id="hook_customize_critical_css" name="hook_customize_critical_css"
						class="hook_before_start"><?php if (!empty ($result['hook_customize_critical_css']))
							echo esc_html(stripslashes($result['hook_customize_critical_css'])); ?></textarea>
					<code > return $critical_css; <br>}</code>
					</div>
					
					<div class="single-hook">
					<label><span class="main-label"><?php esc_html_e('W3speedster Disable Htaccess Webp', 'w3speedster'); ?></span><span class="info"></span><span class="info-display">
							<p><?php esc_html_e('Function:', 'w3speedster'); ?> <?php esc_html_e('w3SpeedsterDisableHtaccessWebp', 'w3speedster'); ?>.</p>
							<p><strong><?php esc_html_e('Description:', 'w3speedster'); ?></strong><?php esc_html_e(' Our plugin converts .jpg/.png format to WebP format without changing the URL. it disable webp to render from HTACCESS.', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Parameter:', 'w3speedster'); ?></strong><?php esc_html_e('$disable_htaccess_webp- 0(default) || 1', 'w3speedster'); ?> 
							</p>
							<p><strong><?php esc_html_e('Return:', 'w3speedster'); ?></strong><?php esc_html_e('1 – It will add w3.webp at the end of the url for instance (xyz.jpgw3.webp).', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Example:', 'w3speedster'); ?></strong><br>
<pre>	
function w3SpeedsterDisableHtaccessWebp($disable_htaccess_webp){
	$disable_htaccess_webp = 1
return $disable_htaccess_webp;
}
</pre>
							</p>
						</span>
					</label>
					<code >function w3SpeedsterDisableHtaccessWebp($disable_htaccess_webp){</code>
					<textarea rows="5" cols="100" id="hook_disable_htaccess_webp" name="hook_disable_htaccess_webp"
						class="hook_before_start"><?php if (!empty ($result['hook_disable_htaccess_webp']))
							echo esc_html(stripslashes($result['hook_disable_htaccess_webp'])); ?></textarea>
					<code > return $disable_htaccess_webp; <br>}</code>
					</div>
					
					<div class="single-hook">
					<label><span class="main-label"><?php esc_html_e('W3speedster Customize Add Settings', 'w3speedster'); ?></span><span class="info"></span>	<span class="info-display">
							<p><?php esc_html_e('Function:', 'w3speedster'); ?> <?php esc_html_e('w3SpeedsterCustomizeAddSettings', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Description:', 'w3speedster'); ?></strong><?php esc_html_e(' If you wish to change in variables and paths (URL), W3Speedster allows you to make changes in variables and paths with the help of this plugin function.', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Parameter:', 'w3speedster'); ?></strong><?php esc_html_e('$add_settings- settings of the plugin.', 'w3speedster'); ?> 
							</p>
							<p><strong><?php esc_html_e('Return:', 'w3speedster'); ?></strong><?php esc_html_e('$add_settings – reflect the changes made in variable and path.', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Example:', 'w3speedster'); ?></strong><br>
<pre>	
function w3SpeedsterCustomizeAddSettings($add_settings){
$add_settings = str_replace(array(“mob.css”),array(“mobile.css”), $add_settings);
	return $add_settings;
}
</pre>
							</p>
						</span>
					</label>
					<code >function w3SpeedsterCustomizeAddSettings($add_settings){</code>
					<textarea rows="5" cols="100" id="hook_customize_add_settings" name="hook_customize_add_settings"
						class="hook_before_start"><?php if (!empty ($result['hook_customize_add_settings']))
							echo esc_html(stripslashes($result['hook_customize_add_settings'])); ?></textarea>
					<code > return $add_settings; <br>}</code>
					</div>
					
					<div class="single-hook">
					<label><span class="main-label"><?php esc_html_e('W3speedster Customize Main Settings', 'w3speedster'); ?></span><span class="info"></span>
						<span class="info-display">
							<p><?php esc_html_e('Function:', 'w3speedster'); ?> <?php esc_html_e('w3SpeedsterCustomizeMainSettings', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Description:', 'w3speedster'); ?></strong><?php esc_html_e(' Customize plugin main settings.', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Parameter:', 'w3speedster'); ?></strong><?php esc_html_e(' $settings- Plugin main settings array (like: exclude css, cache path etc ) ', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Return:', 'w3speedster'); ?></strong><?php esc_html_e(' $settings', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Example:', 'w3speedster'); ?></strong><br>
<pre>
function w3SpeedsterCustomizeMainSettings($settings){
	$settings['setting_name'] = value;
return $settings;
}
</pre>
							</p>
						</span>
					</label>
					<code >function w3SpeedsterCustomizeMainSettings($settings){</code>
					<textarea rows="5" cols="100" id="hook_customize_main_settings" name="hook_customize_main_settings"
						class="hook_before_start"><?php if (!empty ($result['hook_customize_main_settings']))
							echo esc_html(stripslashes($result['hook_customize_main_settings'])); ?></textarea>
					<code > return $settings; <br>}</code>
					</div>
					
					<div class="single-hook">
					<label><span class="main-label"><?php esc_html_e('W3speedster Seprate Critical Css For Post Type', 'w3speedster'); ?></span><span class="info"></span>
						<span class="info-display">
							<p><?php esc_html_e('Function:', 'w3speedster'); ?> <?php esc_html_e('w3SpeedsterCreateSeprateCssOfPostType', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Description:', 'w3speedster'); ?></strong><?php esc_html_e(' By default our plugin creates a single critical css for post but If you wish to generate separate critical CSS for post. W3Speedster allows you to create critical CSS separately post-wise.', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Parameter:', 'w3speedster'); ?></strong><?php esc_html_e('$separate_post_css- Array of post types. ', 'w3speedster'); ?>
							</p>
							<p><strong><?php esc_html_e('Return:', 'w3speedster'); ?></strong><?php esc_html_e('$separate_post_css – create separate critical css for each post and page.', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Example:', 'w3speedster'); ?></strong><br>
<pre>	
function w3SpeedsterCreateSeprateCssOfPostType($separate_post_css){
	$separate_post_css = array('page','post','product');
    return $separate_post_css;
}
</pre>
							</p>
						</span>
					</label>
					<code >function w3SpeedsterCreateSeprateCssOfPostType($separate_post_css){</code>
					<textarea rows="5" cols="100" id="hook_sep_critical_post_type" name="hook_sep_critical_post_type"
						class="hook_before_start"><?php if (!empty ($result['hook_sep_critical_post_type']))
							echo esc_html(stripslashes($result['hook_sep_critical_post_type'])); ?></textarea>
					<code > return $separate_post_css; <br>}</code>
					</div>
					
					<div class="single-hook">
					<label><span class="main-label"><?php esc_html_e('W3speedster Seprate Critical Css For Category', 'w3speedster'); ?></span><span class="info"></span>
					<span class="info-display">
							<p><?php esc_html_e('Function:', 'w3speedster'); ?> <?php esc_html_e('W3speedsterCriticalCssOfCategory', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Description:', 'w3speedster'); ?></strong> <?php esc_html_e('W3Speedster Create seprate critical css for  categories pages.', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Parameter:', 'w3speedster'); ?></strong><?php esc_html_e('$separate_cat_css- Array of Category.', 'w3speedster'); ?> 
							</p>
							<p><strong><?php esc_html_e('Return:', 'w3speedster'); ?></strong>
							<?php esc_html_e('$separate_cat_css – create separate critical css for each category and tag.', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Example:', 'w3speedster'); ?></strong><br>
<pre>	
function W3speedsterCriticalCssOfCategory($separate_cat_css){
	$separate_cat_css = array('category','tag','custom-category');
   return $separate_cat_css;
}
</pre>
							</p>
						</span>
					</label>
					<code >function W3speedsterCriticalCssOfCategory($separate_cat_css){</code>
					<textarea rows="5" cols="100" id="hook_sep_critical_cat" name="hook_sep_critical_cat"
						class="hook_before_start"><?php if (!empty ($result['hook_sep_critical_cat']))
							echo esc_html(stripslashes($result['hook_sep_critical_cat'])); ?></textarea>
					<code > return $separate_cat_css; <br>}</code>
					</div>
					
					<div class="single-hook">
					<label><span class="main-label"><?php esc_html_e('W3speedster Change Video To Videolazy', 'w3speedster'); ?></span><span class="info"></span>
						<span class="info-display">
							<p><?php esc_html_e('Function:', 'w3speedster'); ?> <?php esc_html_e('w3SpeedsterVideoToVideoLazy', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Description:', 'w3speedster'); ?></strong> <?php esc_html_e('Change video tag to videolazy tag', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Parameter:', 'w3speedster'); ?></strong> <?php esc_html_e('$videolazy- 0(default) || 1', 'w3speedster'); ?> </p>
							<p><strong><?php esc_html_e('Return:', 'w3speedster'); ?></strong><?php esc_html_e(' 1 - Change video tag to videolazy tag.', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Example:', 'w3speedster'); ?></strong><br>
<pre>
function w3SpeedsterVideoToVideoLazy($videolazy){
	$videolazy= 1;
	return $videolazy;
}
</pre>
							</p>
						</span>
					</label>
					<code >function w3SpeedsterVideoToVideoLazy($videolazy){</code>
					<textarea rows="5" cols="100" id="hook_video_to_videolazy" name="hook_video_to_videolazy"
						class="hook_before_start"><?php if (!empty ($result['hook_video_to_videolazy']))
							echo esc_html(stripslashes($result['hook_video_to_videolazy'])); ?></textarea>
					<code > return $videolazy; <br>}</code>
					</div>
					
					<div class="single-hook">
					<label><span class="main-label"><?php esc_html_e('W3speedster Change Iframe To Iframlazy', 'w3speedster'); ?></span><span class="info"></span>
						<span class="info-display">
							<p><?php esc_html_e('Function:', 'w3speedster'); ?> <?php esc_html_e('w3SpeedsterIframetoIframelazy', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Description:', 'w3speedster'); ?></strong><?php esc_html_e(' Change iframe tag to iframlazy tag.', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Parameter:', 'w3speedster'); ?></strong><?php esc_html_e(' $iframelazy- 0(default) || 1', 'w3speedster'); ?> </p>
							<p><strong><?php esc_html_e('Return:', 'w3speedster'); ?></strong><?php esc_html_e(' 1 - Change iframe tag to iframlazy tag.', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Example:', 'w3speedster'); ?></strong><br>
<pre>
function w3SpeedsterIframetoIframelazy($iframelazy){
	$iframelazy = 1;
	return $iframelazy;
}
</pre>
							</p>
						</span>
					</label>
					<code >function w3SpeedsterIframetoIframelazy($iframelazy){</code>
					<textarea rows="5" cols="100" id="hook_iframe_to_iframelazy" name="hook_iframe_to_iframelazy"
						class="hook_before_start"><?php if (!empty ($result['hook_iframe_to_iframelazy']))
							echo esc_html(stripslashes($result['hook_iframe_to_iframelazy'])); ?></textarea>
					<code > return $iframelazy; <br>}</code>
					</div>
					
					<div class="single-hook">
					<label><span class="main-label"><?php esc_html_e('W3speedster Exclude Image To Lazyload', 'w3speedster'); ?></span><span class="info"></span>
					<span class="info-display">
						<p><?php esc_html_e('Function:', 'w3speedster'); ?> <?php esc_html_e('w3SpeedsterExcludeImageToLazyload', 'w3speedster'); ?></p>
						<p><strong><?php esc_html_e('Description:', 'w3speedster'); ?></strong>  <?php esc_html_e('W3Speedster allows you to exclude the images from optimization dynamically which you don’t want to lazyload.', 'w3speedster'); ?></p>
						<p><strong><?php esc_html_e('Parameter:', 'w3speedster'); ?></strong><?php esc_html_e('$exclude_image = 0(default) || 1 <br>$img = Image tag with all attributes<br>$imgnn_arr = Image tag ', 'w3speedster'); ?></p>
						<p><strong><?php esc_html_e('Return:', 'w3speedster'); ?></strong>
						<?php esc_html_e('1 – it will lazy load the image.', 'w3speedster'); ?><br>
						<?php esc_html_e('0 – it will not lazy load the image.', 'w3speedster'); ?></p>
						<p><strong><?php esc_html_e('Example:', 'w3speedster'); ?></strong><br>
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
					<code >function w3SpeedsterExcludeImageToLazyload($exclude_image,$img, $imgnn_arr){</code>
					<textarea rows="5" cols="100" id="hook_exclude_image_to_lazyload" name="hook_exclude_image_to_lazyload"
						class="hook_before_start"><?php if (!empty ($result['hook_exclude_image_to_lazyload']))
							echo esc_html(stripslashes($result['hook_exclude_image_to_lazyload'])); ?></textarea>
					<code > return $exclude_image; <br>}</code>
					</div>
					
					<div class="single-hook">
					<label><span class="main-label"><?php esc_html_e('W3speedster Customize Image', 'w3speedster'); ?></span><span class="info"></span>
						<span class="info-display">
							<p><?php esc_html_e('Function:', 'w3speedster'); ?> <?php esc_html_e('w3SpeedsterCustomizeImage', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Description:', 'w3speedster'); ?></strong><?php esc_html_e(' Customize image tags.', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Parameter:', 'w3speedster'); ?></strong><?php esc_html_e(' $img = Image tag with all attributes <br>$imgnn = Modified image tag by plugin <br>$imgnn_arr = Image tag attributes array', 'w3speedster'); ?> </p>
							<p><strong><?php esc_html_e('Return:', 'w3speedster'); ?></strong><?php esc_html_e(' $imgnn- Customized image tags ', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Example:', 'w3speedster'); ?></strong><br>
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
					<code >function w3SpeedsterCustomizeImage($imgnn,$img,$imgnn_arr){</code>
					<textarea rows="5" cols="100" id="hook_customize_image" name="hook_customize_image"
						class="hook_before_start"><?php if (!empty ($result['hook_customize_image']))
							echo esc_html(stripslashes($result['hook_customize_image'])); ?></textarea>
					<code > return $imgnn; <br>}</code>
					</div>
					
					<div class="single-hook">
					<label><span class="main-label"><?php esc_html_e('W3speedster Prevent Htaccess Generation', 'w3speedster'); ?></span><span class="info"></span>
						<span class="info-display">
							<p><?php esc_html_e('Function:', 'w3speedster'); ?> <?php esc_html_e('w3SpeedsterPreventHtaccessGeneration', 'w3speedster'); ?>.</p>
							<p><strong><?php esc_html_e('Description:', 'w3speedster'); ?></strong><?php esc_html_e('  Our plugin converts .jpg/.png format to WebP format without changing the URL. it disable webp to render from HTACCESS.', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Parameter:', 'w3speedster'); ?></strong><?php esc_html_e('$preventHtaccess = 0(default) || 1 ', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Return:', 'w3speedster'); ?></strong><?php esc_html_e(' 1 – It will add w3.webp at the end of the url for instance (xyz.jpgw3.webp).', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Example:', 'w3speedster'); ?></strong><br>
<pre>	
function w3SpeedsterPreventHtaccessGeneration($preventHtaccess){
	$preventHtaccess = 1;
   return $preventHtaccess;
}
</pre>
							</p>
						</span>
					</label>
					<code >function w3SpeedsterPreventHtaccessGeneration($preventHtaccess){</code>
					<textarea rows="5" cols="100" id="hook_prevent_generation_htaccess" name="hook_prevent_generation_htaccess"
						class="hook_before_start"><?php if (!empty ($result['hook_prevent_generation_htaccess']))
							echo esc_html(stripslashes($result['hook_prevent_generation_htaccess'])); ?></textarea>
					<code > return $preventHtaccess; <br>}</code>
					</div>
					
					<div class="single-hook">
					<label><span class="main-label"><?php esc_html_e('W3speedster Exclude CSS Filter', 'w3speedster'); ?></span><span class="info"></span>
						<span class="info-display">
						<p><?php esc_html_e('Function:', 'w3speedster'); ?> <?php esc_html_e('W3SpeedsterExcludeCssFilter', 'w3speedster'); ?></p>
						<p><strong><?php esc_html_e('Description:', 'w3speedster'); ?></strong><?php esc_html_e(' If you want to dynamically exclude a CSS file from optimization, W3Speedster allows you to exclude it from optimization (like style.css).', 'w3speedster'); ?></p>
						<p><strong><?php esc_html_e('Parameter:', 'w3speedster'); ?></strong><?php esc_html_e(' $exclude_css – 0(default) || 1', 'w3speedster'); ?><br>
							<?php esc_html_e('$css_obj – link tag in object format.', 'w3speedster'); ?><br>
							<?php esc_html_e('$css – Content of the CSS file you want to make changes in.', 'w3speedster'); ?><br>
							<?php esc_html_e('$html – content of the webpage.', 'w3speedster'); ?></p>
						<p><strong><?php esc_html_e('Return:', 'w3speedster'); ?></strong><?php esc_html_e(' $exclude_css – exclude CSS from optimization.', 'w3speedster'); ?></p>
						<p><strong><?php esc_html_e('Example:', 'w3speedster'); ?></strong><br>
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
						class="hook_before_start"><?php if (!empty ($result['hook_exclude_css_filter']))
							echo esc_html(stripslashes($result['hook_exclude_css_filter'])); ?></textarea>
					<code > return $exclude_css; <br>}</code>
					</div>
					<hr>
					<div class="single-hook">
					<label><span class="main-label"><?php esc_html_e('W3speedster Customize Force Lazyload Css', 'w3speedster'); ?></span><span class="info"></span>
						<span class="info-display">
							<p><?php esc_html_e('Function:', 'w3speedster'); ?><?php esc_html_e('w3SpeedsterCustomizeForceLazyCss', 'w3speedster'); ?>.</p>
							<p><strong><?php esc_html_e('Description:', 'w3speedster'); ?></strong> <?php esc_html_e(' If you wish to Force Lazyload CSS files dynamically for a specific page or pages, you can do so with the W3Speedster, it allows you to dynamically force lazyload stylesheet files (for instance font file like awesome, dashicons and css files).', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Parameter:', 'w3speedster'); ?></strong><?php esc_html_e(' $force_lazyload_css – Array containing text to force lazyload which you have mentioned in the plugin configuration.', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Return:', 'w3speedster'); ?></strong><?php esc_html_e(' $force_lazyload_css – Array containing text to force lazyload.', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Example:', 'w3speedster'); ?></strong><br>
<pre>	
function w3SpeedsterCustomizeForceLazyCss($force_lazyload_css){
   array_push($force_lazyload_css ,'/fire-css');
   return $force_lazyload_css;
}
</pre>
							</p>
						</span>
					</label>
					<code >function w3SpeedsterCustomizeForceLazyCss($force_lazyload_css){</code>
					<textarea rows="5" cols="100" id="hook_customize_force_lazy_css" name="hook_customize_force_lazy_css"
						class="hook_before_start"><?php if (!empty ($result['hook_customize_force_lazy_css']))
							echo esc_html(stripslashes($result['hook_customize_force_lazy_css'])); ?></textarea>
					<code > return $force_lazyload_css; <br>}</code>
					</div>
					
					<div class="single-hook">
					<label><span class="main-label"><?php esc_html_e('W3speedster External Javascript Customize', 'w3speedster'); ?></span><span class="info"></span>
					<span class="info-display">
						<p><?php esc_html_e('Function:', 'w3speedster'); ?> <?php esc_html_e('W3SpeedsterExternalJavascriptCustomize', 'w3speedster'); ?></p>
						<p><strong> Description:</strong><?php esc_html_e(' If you want to make changes in your external JavaScript tags, W3Speedster allows you to make changes in external JavaScript tags.', 'w3speedster'); ?></p>
						<p><strong><?php esc_html_e('Parameter:', 'w3speedster'); ?></strong> <?php esc_html_e('$script_obj – Script in object format.', 'w3speedster'); ?><br>
				<?php esc_html_e('$script – Content of the JS file you want to make changes in', 'w3speedster'); ?></p>
						<p><strong><?php esc_html_e('Return:', 'w3speedster'); ?></strong><?php esc_html_e(' $script_obj – Make changes in Js files from an external source.', 'w3speedster'); ?></p>
						<p><strong><?php esc_html_e('Example:', 'w3speedster'); ?></strong><br>
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
					<code >function W3SpeedsterExternalJavascriptCustomize($script_obj, $script){</code>
					<textarea rows="5" cols="100" id="hook_external_javascript_customize" name="hook_external_javascript_customize"
						class="hook_before_start"><?php if (!empty ($result['hook_external_javascript_customize']))
							echo esc_html(stripslashes($result['hook_external_javascript_customize'])); ?></textarea>
					<code > return $script_obj; <br>}</code>
					</div>
					
					<div class="single-hook">
					<label><span class="main-label"><?php esc_html_e('W3speedster External Javascript Filter', 'w3speedster'); ?></span><span class="info"></span>
						<span class="info-display">
						<p><?php esc_html_e('Function:', 'w3speedster'); ?> <?php esc_html_e('W3SpeedsterExternalJavascriptFilter', 'w3speedster'); ?></p>
						<p><strong><?php esc_html_e('Description:', 'w3speedster'); ?></strong><?php esc_html_e(' If you want to dynamically exclude a JavaScript file or inline script from optimization, W3Speedster allows you to exclude it from optimization (like revslider).', 'w3speedster'); ?></p>
						<p><strong><?php esc_html_e('Parameter:', 'w3speedster'); ?></strong><?php esc_html_e(' $exclude_js – 0(default) || 1', 'w3speedster'); ?><br>
							<?php esc_html_e('$script_obj – Script in object format.', 'w3speedster'); ?><br>
							<?php esc_html_e('$script – Content of the JS file you want to make changes in.', 'w3speedster'); ?><br>
							<?php esc_html_e('$html – content of the webpage.', 'w3speedster'); ?></p>
						<p><strong><?php esc_html_e('Return:', 'w3speedster'); ?></strong> <?php esc_html_e('$exclude_js – exclude JS from optimization.', 'w3speedster'); ?></p>
						<p><strong><?php esc_html_e('Example:', 'w3speedster'); ?></strong><br>
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
					<code >function W3SpeedsterExternalJavascriptFilter($exclude_js,$script_obj,$script,$html){</code>
					<textarea rows="5" cols="100" id="hook_external_javascript_filter" name="hook_external_javascript_filter"
						class="hook_before_start"><?php if (!empty ($result['hook_external_javascript_filter']))
							echo esc_html(stripslashes($result['hook_external_javascript_filter'])); ?></textarea>
					<code > return $exclude_js; <br>}</code>
					</div>
					
					<div class="single-hook">
					<label><span class="main-label"><?php esc_html_e('W3speedster Customize Script Object', 'w3speedster'); ?></span><span class="info"></span>
						<span class="info-display">
							<p><?php esc_html_e('Function:', 'w3speedster'); ?> <?php esc_html_e('W3SpeedsterCustomizeScriptObject', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Description:', 'w3speedster'); ?></strong><?php esc_html_e(' W3Speedster allows you to customize script objects while minifying and combining scripts.', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Parameter:', 'w3speedster'); ?></strong><?php esc_html_e('$script_obj- Script in object format.', 'w3speedster'); ?><br>
					<?php esc_html_e('$script- Content of the JS file you want to make changes in.', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Return:', 'w3speedster'); ?></strong><?php esc_html_e(' $script_obj– Make changes in Js files.', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Example:', 'w3speedster'); ?></strong><br>
<pre>
function W3SpeedsterCustomizeScriptObject($script_obj, $script){
// your code
return $script_obj;
}
</pre>
							</p>
						</span>
					</label>
					<code >function W3SpeedsterCustomizeScriptObject($script_obj, $script){</code>
					<textarea rows="5" cols="100" id="hook_customize_script_object" name="hook_customize_script_object"
						class="hook_before_start"><?php if (!empty ($result['hook_customize_script_object']))
							echo esc_html(stripslashes($result['hook_customize_script_object'])); ?></textarea>
					<code > return $script_obj; <br>}</code>
					</div>
					
					<div class="single-hook">
					<label><span class="main-label"><?php esc_html_e('W3speedster Exclude Internal Js W3 Changes', 'w3speedster'); ?></span><span class="info"></span>
						<span class="info-display">
							<p><?php esc_html_e('Function:', 'w3speedster'); ?> <?php esc_html_e('W3SpeedsterExcludeInternalJsW3Changes', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Description:', 'w3speedster'); ?></strong><?php esc_html_e(' Our plugin makes changes in JavaScript files for optimization, if you do not want to make any changes in JavaScript file, W3Speedster allows you to exclude JavaScript files from the plugin to make any changes.', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Parameter:', 'w3speedster'); ?></strong>
							<?php esc_html_e('$path- path of your script tags url ', 'w3speedster'); ?><br>
							<?php esc_html_e('$string – JavaScript files content.', 'w3speedster'); ?><br>
							<?php esc_html_e('$exclude_from_w3_changes = 0(default) || 1', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Return:', 'w3speedster'); ?></strong><?php esc_html_e(' 1 – Exclude the JS file from making any changes.', 'w3speedster'); ?>
							<?php esc_html_e('0 – It will not exclude the JS file from making any changes.', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Example:', 'w3speedster'); ?></strong><br>
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
					<code >function W3SpeedsterExcludeInternalJsW3Changes($exclude_from_w3_changes,$path,$string){</code>
					<textarea rows="5" cols="100" id="hook_exclude_internal_js_w3_changes" name="hook_exclude_internal_js_w3_changes"
						class="hook_before_start"><?php if (!empty ($result['hook_exclude_internal_js_w3_changes']))
							echo esc_html(stripslashes($result['hook_exclude_internal_js_w3_changes'])); ?></textarea>
					<code > return $exclude_from_w3_changes; <br>}</code>
					</div>
					
					<div class="single-hook">
					<label><span class="main-label"><?php esc_html_e('W3speedster Exclude Page Optimization', 'w3speedster'); ?></span><span class="info"></span>
						<span class="info-display">
							<p><?php esc_html_e('Function:', 'w3speedster'); ?> <?php esc_html_e('W3SpeedsterExcludePageOptimization', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Description:', 'w3speedster'); ?></strong><?php esc_html_e(' W3Speedster allows you to exclude the pages from the Optimization. if you wish to exclude your pages from optimization. (like cart/login pages).', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Parameter:', 'w3speedster'); ?></strong><?php esc_html_e('$html = Page viewsources content.<br>$exclude_page_optimization = 0(default) || 1', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Return:', 'w3speedster'); ?></strong><?php esc_html_e(' 1 – it will exclude the page from optimization.', 'w3speedster'); ?>
<?php esc_html_e('0 – it will not exclude the page from optimization.', 'w3speedster'); ?></p>
		<p><strong><?php esc_html_e('Example:', 'w3speedster'); ?></strong><br>
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
					<code >function W3SpeedsterExcludePageOptimization($html,$exclude_page_optimization){</code>
					<textarea rows="5" cols="100" id="hook_exclude_page_optimization" name="hook_exclude_page_optimization"
						class="hook_before_start"><?php if (!empty ($result['hook_exclude_page_optimization']))
							echo esc_html(stripslashes($result['hook_exclude_page_optimization'])); ?></textarea>
					<code > return $exclude_page_optimization; <br>}</code>
					</div>
					
					<div class="single-hook">
					<label><span class="main-label"><?php esc_html_e('W3speedster Customize Critical Css File Name', 'w3speedster'); ?></span><span class="info"></span>
						<span class="info-display">
							<p><?php esc_html_e('Function:', 'w3speedster'); ?> <?php esc_html_e('W3SpeedsterCustomizeCriticalCssFileName', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Description:', 'w3speedster'); ?></strong><?php esc_html_e(' If you wish to make any changes in Critical CSS filename, W3Speedster allows you to change in critical CSS file names. W3Speedster creates file names for critical CSS files but if you wish to change the name according to your preference this function will help.', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Parameter:', 'w3speedster'); ?></strong><?php esc_html_e('$file_name – File name of the critical css.', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Return:', 'w3speedster'); ?></strong><?php esc_html_e(' $file_name – New name of the critical css file.', 'w3speedster'); ?></p>
							<p><strong><?php esc_html_e('Example:', 'w3speedster'); ?></strong><br>
<pre>
function W3SpeedsterCustomizeCriticalCssFileName($file_name){
$file_name = str_replace(' ',' ',$file_name);
	return $file_name;
}
</pre>
							</p>
						</span>
					</label>
					<code >function W3SpeedsterCustomizeCriticalCssFileName($file_name){</code>
					<textarea rows="5" cols="100" id="hook_customize_critical_css_filename" name="hook_customize_critical_css_filename"
						class="hook_before_start"><?php if (!empty ($result['hook_customize_critical_css_filename']))
							echo esc_html(stripslashes($result['hook_customize_critical_css_filename'])); ?></textarea>
					<code > return $file_name; <br>}</code>
					</div>
					</div>
					<hr>
					<div class="single-hook_btn">
					<div class="save-changes w3d-flex gap10">
						<input type="button" value="Save Changes" class="btn hook_submit">
						<div class="in-progress w3d-flex save-changes-loader" style="display:none">
							<img src="<?php echo esc_url(W3SPEEDSTER_PLUGIN_URL); ?>assets/images/loader-gif.gif"
								alt="loader" class="loader-img">
						</div>
					</div>

				</section>
<section id="webvitalslogs" class="tab-pane fade">
				<div class="header w3d-flex gap20">
				<div class="heading_container">
					<h4 class="heading"><?php esc_html_e('Debug Logs', 'w3speedster'); ?>
					</h4>
					<span class="info"><a href="https://w3speedster.com/w3speedster-documentation/"><?php esc_html_e('More info', 'w3speedster'); ?>?
						</a></span>
				</div>
				<div class="icon_container"> <img
						src="<?php echo esc_url(W3SPEEDSTER_PLUGIN_URL); ?>assets/images/logs-icon.webp"></div>
			</div>
			<hr>
				
				<div class="w3d-flex gap20 <?php echo esc_attr($hidden_class); ?>">
					<label><?php esc_html_e('Enable Core Web Vitals Logs', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('Enable to Log Core Web Vitals Logs.', 'w3speedster'); ?></span></label>
					<div class="input_box">
						<label class="switch" for="enable-webvitals-log">
							<input type="checkbox" name="webvitals_logs" <?php if (!empty ($result['webvitals_logs']) && $result['webvitals_logs'] == "on") echo "checked"; ?> id="enable-webvitals-log" class="basic-set">
							<div class="checked"></div>
						</label>
					</div>
				</div>
					<?php if(empty($result['webvitals_logs'])){
						echo '<p class="alert_message">Enable Debug Log options for Logging</p>';
					}else{
						?>
				
				<div class="w3d-flex gap20 filter-row">
					<div class="show_log w3d-flex gap10">
					<label for="show_log_entry"><?php esc_html_e('Show', 'w3speedster'); ?></label>
						<select name="temp_input" id="show_log_entry" class="show_log_entry">
							<option value="10"><?php esc_html_e('10', 'w3speedster'); ?></option>
							<option value="20"><?php esc_html_e('20', 'w3speedster'); ?></option>
							<option value="30"><?php esc_html_e('30', 'w3speedster'); ?></option>
							<option value="40"><?php esc_html_e('40', 'w3speedster'); ?></option>
							<option value="50"><?php esc_html_e('50', 'w3speedster'); ?></option>
						</select>
					</div>
					<div class="delete-log-data w3d-flex gap10">
					<label for="log_delete_time">Delete Logs</label>
						<select class="log_select" id="log_delete_time" name="temp_input">
							<option value=""><?php esc_html_e('Select Log Time', 'w3speedster'); ?></option>
							<option value="last7days"><?php esc_html_e('Keep last 7 Days', 'w3speedster'); ?></option>
							<option value="lastMonth"><?php esc_html_e('Keep last 30 Days', 'w3speedster'); ?></option>
							<option value="last3months"><?php esc_html_e('Keep last 90 Days', 'w3speedster'); ?></option>
							<option value="last6months"><?php esc_html_e('Keep last 180 Days', 'w3speedster'); ?></option>
							<!-- <option value="lastYear">All</option> -->
							<option value="all"><?php esc_html_e('All', 'w3speedster'); ?></option>
						</select>
						<button type="button" class="btn btn-log-delete"><?php esc_html_e('Delete', 'w3speedster'); ?></button>
					</div>

				</div>
				<div class="w3d-flex gap10 filter-row">
					<div class="filter_by_issue w3d-flex gap10">
					<label for="filter_by_issue"><?php esc_html_e('Issue Type', 'w3speedster'); ?></label>
					<select name="temp_input" class="filter_by_issuetype">
						<option value=""><?php esc_html_e('All', 'w3speedster'); ?></option>
						<option value="CLS"><?php esc_html_e('CLS', 'w3speedster'); ?></option>
						<option value="FID"><?php esc_html_e('FID', 'w3speedster'); ?></option>
						<option value="INP"><?php esc_html_e('INP', 'w3speedster'); ?></option>
						<option value="LCP"><?php esc_html_e('LCP', 'w3speedster'); ?></option>
					</select>
					</div>
					<div class="filter_by_device w3d-flex gap10">
					<label for="filter_by_device"><?php esc_html_e('Device', 'w3speedster'); ?></label>
					<select name="temp_input" class="filter_by_deviceType">
						<option value=""><?php esc_html_e('All', 'w3speedster'); ?></option>
						<option value="Mobile"><?php esc_html_e('Mobile', 'w3speedster'); ?></option>
						<option value="Desktop"><?php esc_html_e('Desktop', 'w3speedster'); ?></option>
					</select>
					</div>
					<div class="filter_by_url ">
					<select class="url-select-multiple"  id="filter_by_url" class="filter_by_url_input" name="temp_input[]" multiple="multiple">
					<input type="text" class="custom_select_inp" placeholder="<?php esc_html_e('https://...', 'w3speedster'); ?>">
					<button type="button" class="btn_clear_url_inp" style="display:none">+</button>
						<div id="custom_select_url"></div>
					</select>
					</div>
					<div class="filter_by_date w3d-flex gap10">
					<label for="start_date"><?php esc_html_e('From', 'w3speedster'); ?></label>
					<input type="text" name="temp_input" class="start_date">
					<label for="end_date"><?php esc_html_e('To', 'w3speedster'); ?></label>
					<input type="text" name="temp_input" class="end_date">
					</div>
					<button type="button" class="btn btn-apply-filter"><?php esc_html_e('Apply Filters', 'w3speedster'); ?></button>
					<button type="button" class="btn btn-rem-filter"><?php esc_html_e('Clear', 'w3speedster'); ?></button>
				</div>
				<div popover="auto" id="more_info">
					<button type="button" popovertarget="more_info" popovertargetaction="hide" title="Close" class="close-popover">+</button>
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
							<h4 class="heading"><?php esc_html_e('HTML Caches', 'w3speedster'); ?>
							</h4>
							<span class="info"><a
									href="https://w3speedster.com/w3speedster-documentation/"><?php esc_html_e('More info', 'w3speedster'); ?>?
								</a></span>
						</div>
						<div class="icon_container"> <img
								src="<?php echo esc_url(W3SPEEDSTER_PLUGIN_URL); ?>assets/images/html_caches-icon1.webp"></div>
					</div>
					<hr>
					<?php
					
					$advanced_cache_file = WP_CONTENT_DIR . '/advanced-cache.php';
					// Check if the advanced-cache.php file exists and not by w3speedster
					// @codingStandardsIgnoreLine
					if (file_exists($advanced_cache_file) && strpos(file_get_contents($advanced_cache_file), 'Added By W3speedster Pro') == 0 && $advanced_cache_exist == 1) {
						echo '<div class="advance-cache-exist-error">'. esc_html__('The advanced-cache.php file already exists. Please delete this file and remove the plugin that created it.', 'w3speedster').'</div>';
						echo '<button type="button" class="btn force-delete-ac"><a href="'.esc_url($_SERVER['REQUEST_URI']).'&delete_ac=1">' . esc_html__('Force Delete File', 'w3speedster') . '</a></button>';
					}  
						?>

						<div class="html-cache-main">
							<div class="w3d-flex gap20 <?php echo esc_attr($hidden_class); ?>">
								<label><?php esc_html_e('Enable HTML Caching', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('Enable to on html caching', 'w3speedster'); ?></span></label>
								<div class="input_box">
									<label class="switch" for="enable-html-caching">
										<input type="checkbox" name="html_caching" <?php if (!empty($result['html_caching']) && $result['html_caching'] == "on")
											echo "checked"; ?> id="enable-html-caching"
											class="basic-set">
										<div class="checked"></div>
									</label>
								</div>
							</div>
							<div class="w3d-flex gap20 <?php echo esc_attr($hidden_class); ?>">
								<label><?php esc_html_e('Enable caching for logged in user', 'w3speedster'); ?><span class="info"></span><span
										class="info-display"><?php esc_html_e('Enable caching for logged in user', 'w3speedster'); ?></span></label>
								<div class="input_box">
									<label class="switch" for="enable-caching-loggedin-user">
										<input type="checkbox" name="enable_loggedin_user_caching" <?php if (!empty($result['enable_loggedin_user_caching']) && $result['enable_loggedin_user_caching'] == "on")
											echo "checked"; ?>
											id="enable-caching-loggedin-user" class="basic-set">
										<div class="checked"></div>
									</label>
								</div>
							</div>
							<div class="w3d-flex gap20 <?php echo esc_attr($hidden_class); ?>">
								<label><?php esc_html_e('Serve html cache file by', 'w3speedster'); ?><span class="info"></span><span
										class="info-display"><?php esc_html_e('Check method for serve cache html file', 'w3speedster'); ?></span></label>
								<div class="input_box w3d-flex gap10">
								<label class="switch" for="htaccess">
									<input value="htaccess" type="radio" name="by_serve_cache_file" <?php if (empty($result['by_serve_cache_file']) || $result['by_serve_cache_file'] == "htaccess") echo "checked"; ?>
										id="htaccess" class="basic-set">
									<div class="checked"></div>
								</label>
								<span><?php esc_html_e('Htaccess', 'w3speedster'); ?></span>
								</div>
								<div class="input_box w3d-flex gap10">
									<label class="switch" for="advanceCache">
										<input value="advanceCache" type="radio" name="by_serve_cache_file" <?php if (!empty($result['by_serve_cache_file']) && $result['by_serve_cache_file'] == "advanceCache") echo "checked"; ?>
											id="advanceCache" class="basic-set">
										<div class="checked"></div>
									</label>
								<span><?php esc_html_e('Advanced Cache File', 'w3speedster'); ?></span>
								</div>

							</div>
							<div class="w3d-flex gap20 <?php echo esc_attr($hidden_class); ?>">
								<label><?php esc_html_e('Enable caching page with GET parameters', 'w3speedster'); ?><span class="info"></span><span
										class="info-display"><?php esc_html_e('Enable caching page with GET parameters', 'w3speedster'); ?></span></label>
								<div class="input_box">
									<label class="switch" for="enable-caching-page-get-para">
										<input type="checkbox" name="enable_caching_get_para" <?php if (!empty($result['enable_caching_get_para']) && $result['enable_caching_get_para'] == "on")
											echo "checked"; ?>
											id="enable-caching-page-get-para" class="basic-set">
										<div class="checked"></div>
									</label>
								</div>
							</div>
							<div class="w3d-flex gap20 <?php echo esc_attr($hidden_class); ?>">
								<label><?php esc_html_e('Minify HTML', 'w3speedster'); ?><span class="info"></span><span
										class="info-display"><?php esc_html_e('BY minify html You can decrease the size of page', 'w3speedster'); ?></span></label>
								<div class="input_box">
									<label class="switch" for="minify_html_cache">
										<input type="checkbox" name="minify_html_cache" <?php if (!empty($result['minify_html_cache']) && $result['minify_html_cache'] == "on") echo "checked"; ?> id="minify_html_cache" class="basic-set">
										<div class="checked"></div>
									</label>
								</div>
							</div>
							<div class="w3d-flex gap20 <?php echo esc_attr($hidden_class); ?>">
								<label><?php esc_html_e('Cache Expiry Time', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('Input an time for cache expiry default time is 3600(1 hour)', 'w3speedster'); ?></span></label>
								<div class="input_box">
									<label class="html-cache-expiry w3d-flex" for="html-cache-expiry-time">
										<input type="text" name="html_caching_expiry_time"
											value="<?php echo (!empty($result['html_caching_expiry_time']) ? esc_attr($result['html_caching_expiry_time']) : 3600) ?>"
											id="html-cache-expiry-time" class="basic-set" style="max-width:80px;"><small>&nbsp; <?php esc_html_e('*Time delay in seconds', 'w3speedster'); ?></small>
										<div class="checked"></div>
									</label>
								</div>
							</div>
							<div class="w3d-flex gap20 <?php echo esc_attr($hidden_class); ?>">
								<label><?php esc_html_e('Separate Cache For Mobile', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('Enable to create separate cache file for mobile', 'w3speedster'); ?></span></label>
								<div class="input_box">
									<label class="switch" for="enable-html-caching-for-mobile">
										<input type="checkbox" name="html_caching_for_mobile" <?php if (!empty($result['html_caching_for_mobile']) && $result['html_caching_for_mobile'] == "on")
											echo "checked"; ?>
											id="enable-html-caching-for-mobile" class="basic-set">
										<div class="checked"></div>
									</label>
								</div>
							</div>
							<div class="w3d-flex gap20 <?php echo esc_attr($hidden_class); ?>">
								<label><?php esc_html_e('Preload Caching', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('Enable to create preload caching', 'w3speedster'); ?></span></label>
								<div class="input_box">
									<label class="switch" for="enable-preload-caching">
										<input type="checkbox" name="preload_caching" <?php if (!empty($result['preload_caching']) && $result['preload_caching'] == "on") echo "checked"; ?>
											id="enable-preload-caching" class="basic-set">
										<div class="checked"></div>
									</label>
								</div>
							</div>
							<div class="w3d-flex gap20 <?php echo esc_attr($hidden_class); ?>">
							<label><?php esc_html_e('Preload page caching per minute', 'w3speedster'); ?> <span class="info"></span><span class="info-display"><?php esc_html_e('how many pages preload per minute', 'w3speedster'); ?></span></label>
							<div class="input_box">
								<label for="pmin-url">
									<input type="number" name="preload_per_min" id="preload_per_min" min="1" max="12" 
										value="<?php echo (!empty ($result['preload_per_min']))? esc_attr($result['preload_per_min']) : 12; ?>">
							</div>
						</div>
							
							<hr>
							<div class="w3d-flex gap20 html-cache-row">
								<label for="Preload Resources"><?php esc_html_e('URI/URL Exclusions', 'w3speedster'); ?><span class="info"></span><span
										class="info-display"><?php esc_html_e('Dont cache the url which match rule', 'w3speedster'); ?></span></label>
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
															value="<?php echo esc_attr(trim($row)); ?>"
															placeholder="<?php esc_html_e('Please Enter Url/String', 'w3speedster'); ?>"><button type="button"
															class="text-white rem-row bg-danger"><i class="fa fa-times"></i></button>
													</div>
													<?php
												}
											}
										} ?>
									</div>
									<div class="cdn_input_box plus">
										<button type="button" data-name="exclude_url_exclusions_html_cache"
											data-placeholder="<?php esc_html_e('Please Enter Url/String', 'w3speedster'); ?>"
											class="btn small text-white bg-success add_more_row"><?php esc_html_e('Add Rule', 'w3speedster'); ?></button>
									</div>

								</div>

							</div>
							<hr>
							<div class="save-changes w3d-flex gap10">
								<input type="button" value="Save Changes" class="btn hook_submit">
								<div class="in-progress w3d-flex save-changes-loader" style="display:none">
									<img src="<?php echo esc_url(W3SPEEDSTER_PLUGIN_URL); ?>assets/images/loader-gif.gif"
										alt="loader" class="loader-img">
								</div>
							</div>
						</div>
						
				</section>
		<section id="opt_img" class="tab-pane fade">
			<div class="header w3d-flex gap20">
				<div class="heading_container">
					<h4 class="heading">
						<?php esc_html_e('Image Optimization', 'w3speedster'); ?>
					</h4>
					<span class="info"><a href="https://w3speedster.com/w3speedster-documentation/#img_optimization"><?php esc_html_e('More info', 'w3speedster'); ?>?
						</a></span>
				</div>
				<div class="icon_container"> <img
						src="<?php echo esc_url(W3SPEEDSTER_PLUGIN_URL); ?>assets/images/image-icon.webp"></div>
			</div>
			<hr>
			<div class="w3d-flex gap20 <?php echo esc_attr($hidden_class); ?>">
				<label><?php esc_html_e('Optimize JPG/PNG Images', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('Enable to optimize jpg and png images.', 'w3speedster'); ?></span></label>
				<div class="input_box w3d-flex gap10">
					<label class="switch" for="optimize-jpg-png-images">
						<input type="checkbox" name="opt_jpg_png" <?php if (!empty ($result['opt_jpg_png']) && $result['opt_jpg_png'] == "on") echo "checked"; ?> id="optimize-jpg-png-images"
							class="main-opt-img">
						<div class="checked"></div>
					</label>
				</div>
			
			</div>
			<div class="w3d-flex gap20 <?php echo esc_attr($hidden_class); ?>">
				<label><?php esc_html_e('JPG PNG Image Quality', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('90 ecommended', 'w3speedster'); ?></span></label>
				<div class="input_box">
					<label for="webp-image-quality">
						<input type="text" name="img_quality"
							value="<?php echo !empty ($result['img_quality']) ? esc_attr($result['img_quality']) : 90; ?>"
							id="webp-image-quality" value="90%" style="max-width:70px;text-align:center">
				</div>
			</div>
			<div class="w3d-flex gap20 <?php echo esc_attr($hidden_class); ?>">
				<label><?php esc_html_e('Convert to Webp', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('This will convert and render images in webp. Need to start image optimization in image optimization tab', 'w3speedster'); ?></span></label>
				<div class="w3d-flex">
					<label for="jpg"><?php esc_html_e('JPG', 'w3speedster'); ?>&nbsp;</label>
					<input type="checkbox" name="webp_jpg" <?php if (!empty ($result['webp_jpg']) && $result['webp_jpg'] == "on")
						echo "checked"; ?> id="jpg" class="main-opt-img">
				</div>
				<div class="w3d-flex">
					<label for="png"><?php esc_html_e('PNG', 'w3speedster'); ?>&nbsp;</label>
					<input type="checkbox" name="webp_png" <?php if (!empty ($result['webp_png']) && $result['webp_png'] == "on")
						echo "checked"; ?> id="png" class="main-opt-img">
				</div>
			</div>
			<div class="w3d-flex gap20 <?php echo esc_attr($hidden_class); ?>">
				<label><?php esc_html_e('Webp Image Quality', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('90 recommended', 'w3speedster'); ?></span></label>
				<div class="input_box">
					<label for="webp-image-quality">
						<input type="text" name="webp_quality"
							value="<?php echo !empty ($result['webp_quality']) ? esc_attr($result['webp_quality']) : 90; ?>"
							id="webp-image-quality" value="90%" style="max-width:70px;text-align:center">
				</div>
			</div>
						
			<div class="w3d-flex gap20 <?php echo esc_attr($hidden_class); ?>">
				<label><?php esc_html_e('Enable Lazy Load', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('This will enable lazy loading of resources.', 'w3speedster'); ?></span></label>
				<div class="w3d-flex">
					<label for="image"><?php esc_html_e('Image', 'w3speedster'); ?>&nbsp;</label>
					<input type="checkbox" name="lazy_load" <?php if (!empty ($result['lazy_load']) && $result['lazy_load'] == "on")
						echo "checked"; ?> id="image" class="lazy-reso">
				</div>
				<div class="w3d-flex">
					<label for="iframe"><?php esc_html_e('Iframe', 'w3speedster'); ?>&nbsp;</label>
					<input type="checkbox" name="lazy_load_iframe" <?php if (!empty ($result['lazy_load_iframe']) && $result['lazy_load_iframe'] == "on")
						echo "checked"; ?> id="iframe" class="lazy-reso">
				</div>
				<div class="w3d-flex">
					<label for="video"><?php esc_html_e('Video', 'w3speedster'); ?>&nbsp;</label>
					<input type="checkbox" name="lazy_load_video" <?php if (!empty ($result['lazy_load_video']) && $result['lazy_load_video'] == "on")
						echo "checked"; ?> id="video" class="lazy-reso">
				</div>
				<div class="w3d-flex">
					<label for="audio"><?php esc_html_e('Audio', 'w3speedster'); ?>&nbsp;</label>
					<input type="checkbox" name="lazy_load_audio" <?php if (!empty ($result['lazy_load_audio']) && $result['lazy_load_audio'] == "on")
						echo "checked"; ?> id="audio" class="lazy-reso">
				</div>
			</div>
						
			<div class="w3d-flex gap20 <?php echo esc_attr($hidden_class); ?>">
				<label><?php esc_html_e('Pixels To load Resources Below the Viewport', 'w3speedster'); ?><span
						class="info"></span><span class="info-display"><?php esc_html_e('Enter pixels to start loading of resources like images, video, iframes, background images, audio which are below the viewport. For eg. 200', 'w3speedster'); ?></span></label>
				<div class="input_box">
					<label for="lazy-px">
						<input type="text" name="lazy_load_px"
							value="<?php echo !empty ($result['lazy_load_px']) ? esc_attr($result['lazy_load_px']) : 200; ?>"
							id="lazy-px" placeholder="<?php esc_html_e('200px', 'w3speedster'); ?>" style="max-width:70px;text-align:center">
					</label>
				</div>
			</div>
				<div class="w3d-flex gap20 <?php echo esc_attr($hidden_class); ?>">
					<label><?php esc_html_e('Load SVG Inline Tag as URL', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('Load SVG inline tag as url to avoid large DOM elements', 'w3speedster'); ?></span></label>
					<div class="input_box">
						<label class="switch" for="load-inline-svg-tag-url">
							<input type="checkbox" name="inlineToUrlSVG" <?php if (!empty ($result['inlineToUrlSVG']) && $result['inlineToUrlSVG'] == "on"){ echo "checked"; } ?> id="load-inline-svg-tag-url">
							<div class="checked"></div>
						</label>
					</div>
				</div>
				<div class="w3d-flex gap20 <?php echo esc_attr($hidden_class); ?>">
					<label><?php esc_html_e('Optimize Images via wp-cron', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('Optimize images via wp-cron.', 'w3speedster'); ?></span></label>
					<div class="input_box">
						<label class="switch" for="optimize-images-via-wp-cron">
							<input type="checkbox" name="enable_background_optimization" <?php if (!empty ($result['enable_background_optimization']) && $result['enable_background_optimization'] == "on")
								echo "checked"; ?>
								id="optimize-images-via-wp-cron" class="main-opt-img">
							<div class="checked"></div>
						</label>
					</div>
				</div>
				<div class="w3d-flex gap20 <?php echo esc_attr($hidden_class); ?>">
					<label><?php esc_html_e('Optimize Images on the go', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('Automatically optimize images when site pages are crawled. Recommended to turn off after initial first crawl of all pages.', 'w3speedster'); ?></span></label>
					<div class="input_box">
						<label class="switch" for="optimize-images-on-the-go">
							<input type="checkbox" name="opt_img_on_the_go" <?php if (!empty ($result['opt_img_on_the_go']) && $result['opt_img_on_the_go'] == "on")
								echo "checked"; ?> id="optimize-images-on-the-go" class="main-opt-img">
							<div class="checked"></div>
						</label>
					</div>
				</div>
				<div class="w3d-flex gap20 <?php echo esc_attr($hidden_class); ?>">
					<label><?php esc_html_e('Automatically Optimize Images on Upload', 'w3speedster'); ?><span class="info"></span><span
							class="info-display"><?php esc_html_e('Automatically optimize new images on upload. Turn off if upload of images is taking more than expected.', 'w3speedster'); ?></span></label>
					<div class="input_box">
						<label class="switch" for="automatically-optimize-images-on-upload">
							<input type="checkbox" name="opt_upload" <?php if (!empty ($result['opt_upload']) && $result['opt_upload'] == "on")
								echo "checked"; ?>
								id="automatically-optimize-images-on-upload">
							<div class="checked"></div>
						</label>
					</div>
				</div>
				<div class="w3d-flex gap20 <?php echo esc_attr($hidden_class); ?>">
					<label><?php esc_html_e('Responsive Images', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('Load smaller images on mobile to reduce load time', 'w3speedster'); ?></span></label>
					<div class="input_box">
						<label class="switch" for="resp-imgs">
							<input type="checkbox" name="resp_bg_img" <?php if (!empty ($result['resp_bg_img']) && $result['resp_bg_img'] == "on") echo "checked"; ?> id="resp-imgs"
								class="resp-img">
							<div class="checked"></div>
						</label>
					</div>
				</div>
				<div class="w3d-flex gap20 <?php echo esc_attr($hidden_class); ?>">
					<label><?php esc_html_e('Insert Aspect Ratio in Img Tag', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('Insert aspect ratio in Img tag inline style.', 'w3speedster'); ?></span></label>
					<div class="input_box">
						<label class="switch" for="insert-aspect-ratio">
							<input type="checkbox" name="aspect_ratio_img" <?php if (!empty ($result['aspect_ratio_img']) && $result['aspect_ratio_img'] == "on")
								echo "checked"; ?> id="insert-aspect-ratio">
							<div class="checked"></div>
						</label>
					</div>
				</div>

			&nbsp;
			<h4>
				<strong><?php echo ($img_remaining <= 0) ? esc_html__('Great Work!, all images are optimized', 'w3speedster') : esc_html__('Images to be optimized', 'w3speedster') . ' - <span class="progress-number">' . esc_html($img_remaining) . '</span>'; ?></strong>
			</h4>
			<div class="progress-container">
				<div class="progress progress-bar progress-bar-striped bg-success progress-bar-animated"
					style="<?php echo 'width:' . number_format((100 - ($img_remaining / $img_to_opt * 100)), 1) . '%' ?>">
					<?php echo '<span class="progress-percent">' . number_format((100 - ($img_remaining / $img_to_opt * 100)), 1) . '%</span>'; ?>
				</div>
			</div>
			<?php
			if (empty ($result['license_key']) || empty ($result['is_activated'])) {
				echo '<span class="non_licensed"><strong class="text-danger">* Starting 500 images will be optimized </strong><br><br><a href="https://w3speedster.com/" class="text-success"><strong>*<u>GO PRO</u> </strong></a> </span><br></br>';
			}
			?>
			<button class="start_image_optimization btn <?php echo ($img_remaining <= 0) ? 'restart' : ''; ?>"
				type="button" <?php if(empty ($result['opt_jpg_png']) && empty($result['webp_png']) && empty($result['webp_jpg'])) echo "disabled";?>>
				<?php echo ($img_remaining <= 0) ? esc_html__('Start image optimization again', 'w3speedster') : esc_html__('Start image optimization', 'w3speedster'); ?>
			</button>
			<button class="reset_image_optimization btn" type="button">
				<?php echo esc_html__('Reset', 'w3speedster');?>
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
						url: "<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>",
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
									jQuery('.progress-container .progress').css('width', percent.toFixed(1) + "%");
									jQuery('.progress-container .progress .progress-percent').html(percent.toFixed(1) + "%");
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
						<input type="button" value="<?php esc_html_e('Save Changes', 'w3speedster'); ?>" class="btn hook_submit gen">
						<div class="in-progress w3d-flex save-changes-loader" style="display:none">
							<img src="<?php echo esc_url(W3SPEEDSTER_PLUGIN_URL); ?>assets/images/loader-gif.gif"
								alt="loader" class="loader-img">
						</div>
			</div>
		</section>
		</form>
		<section id="import" class="tab-pane fade">
			<div class="header w3d-flex gap20">
				<div class="heading_container">
					<h4 class="heading">
						<?php esc_html_e('Import / Export', 'w3speedster'); ?>
					</h4>
					<span class="info"><a href="https://w3speedster.com/w3speedster-documentation/"><?php esc_html_e('More info', 'w3speedster'); ?>?
						</a></span>
				</div>
				<div class="icon_container"> <img
						src="<?php echo esc_url(W3SPEEDSTER_PLUGIN_URL); ?>assets/images/import-export-icon.webp"></div>
			</div>
			<hr>
			<form id="import_form" method="post">
				<div class="import_form">
					<label><?php esc_html_e('Import Settings', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('Enter exported json code from W3speedster plugin import/export page', 'w3speedster'); ?></span></label>
					<textarea id="import_text" name="import_text" rows="10" cols="16"
						placeholder="<?php esc_html_e('Enter json code', 'w3speedster'); ?>"></textarea>
						<input type="hidden" name="_wpnonce" value="<?php echo esc_attr(wp_create_nonce('w3_settings')); ?>" >
					<button id="import_button" class="btn" type="button"><?php esc_html_e('Import', 'w3speedster'); ?></button>
				</div>
			</form>
			<?php
			$export_setting = $result;
			$export_setting['license_key'] = '';
			$export_setting['is_activated'] = '';
			?>

			<hr>
			<div class="import_form">
				<label><?php esc_html_e('Export Settings', 'w3speedster'); ?><span class="info"></span><span class="info-display"><?php esc_html_e('Copy the code and save it in a file for future use', 'w3speedster'); ?></span></label>
				<textarea rows="10"
					cols="16"><?php if (!empty ($export_setting))
						echo wp_json_encode($export_setting); ?></textarea>
			</div>
		</section>


	</div>

	</div>

</main>


<script>
	var custom_css_cd = 0;
	var custom_js_cd = 0;
	function IsJsonString(str) {
		try {
			JSON.parse(str);
		} catch (e) {
			return false;
		}
		return true;
	}
	jQuery(document).ready(function () {
		
		jQuery('button.reset_image_optimization.btn').click(function() {
        if (confirm('Are you sure you want to reset image optimization?')) {
            var currentUrl = window.location.href;
            var newUrl = currentUrl + (currentUrl.indexOf('?') === -1 ? '?' : '&') + 'reset=1';
			
            window.location.href = newUrl;
        }
		});
		jQuery('.expend-textarea').click(function(){
			var id = jQuery(this).attr('data-id');
			event.preventDefault();
			jQuery("#"+id).toggleClass("fullscreen");
		})

		jQuery('#import_button').click(function () {
			var text = jQuery("#import_text").val();
			if (!IsJsonString(text)) {
				alert("Data is courrpted, please check and enter again.");
			}
			jQuery('#import_form').submit();
		});
		
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
		
		if (window.location.href.includes('w3_custom_code')) {
			jQuery('.w3_js').click();
		}
		if (window.location.href.includes('w3_custom_code')) {
			jQuery('.w3_css').click();
		}
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
			//alert(index);

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
		jQuery('#'+newTab).addClass('active in');
	});
	var hash = window.location.href.match(/[?&]tab=([^&]+)/);
	if (hash && hash[1] && hash[1].length > 0) {
		jQuery('.tab-pane').removeClass('active in');
		jQuery('#'+hash[1]).addClass('active in');
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
			url: "<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>",
			type: 'POST',
			data: {
				'action': 'hookBeforeStartOptimization',
				'script': JSON.stringify(script),
				'_wpnonce':'<?php echo esc_attr(wp_create_nonce("hook_callback"));?>'
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
	
	jQuery('.hook_submit').on('click',function(){
        jQuery('.save-changes-loader').show();
		var  script = [];
		jQuery('.hook_before_start').each(function(){
			var id = '#'+jQuery(this).attr('id')
			var editorValue = jQuery(id).next(".CodeMirror").find(".CodeMirror-code").text();
			if(editorValue.length > 1){
				script.push({hookKey: id, value: editorValue});
			}
		});
		checkHookData(script)
		
	});
	
    jQuery('.error_hooks_close').click(function () {
			jQuery(this).parent('.error-hook-main').hide();
	});

	jQuery('.add_more_row').click(function () {
	var inputName = jQuery(this).attr('data-name');
	var placeholder = jQuery(this).attr('data-placeholder');
	var html = '<div class="cdn_input_box minus w3d-flex"><input placeholder="'+ placeholder +'" type="text" name="' + inputName + '[]""><button type="button" class="text-white rem-row bg-danger"><i class="fa fa-times"></i></button></div>';
	jQuery(this).closest('.input_box').find('.single-row').append(html);

	});
	jQuery('.input_box').on('click', '.rem-row', function () {
		jQuery(this).closest('.cdn_input_box.minus.w3d-flex').remove();
	});
	 // For Hooks functionality
	 
	function get_all_hooks(){
				var search_elementItems = '';
				//jQuery('.entry_search_container').show();
				jQuery('.single-hook').each(function(){
					var searchLabel = jQuery(this).find('span.main-label').html();
					var customClass = searchLabel.toLowerCase().replace(/\s+/g, '');
					jQuery(this).addClass('filter-'+customClass)
					var top = jQuery(this).position().top;
					search_elementItems += '<li><a class="scroll_element_item" data-label="'+searchLabel+'" data-filter="'+customClass+'" data-top="'+top+'" href="javascript:void(0);">'+ jQuery( this ).find('span.main-label').html()+ '</a></li>';
				})
				search_elementItems = '<ul>'+search_elementItems+'</ul>';
				jQuery(".entry_search_contaner").html( search_elementItems );
				jQuery('.all_hooks').removeClass('single_selected');
	}
	
		
		jQuery('.pl_search_field').on("focus", function(){
			jQuery('.entry_search_contaner').show();
			var searchTerm = jQuery(this).val();
			if(searchTerm.length == 0){
				get_all_hooks();
			}
			
		});
		jQuery('.pl_search_field').focusout(function(){
			var searchTerm  = jQuery(this).val();
				setTimeout(function(){
					jQuery('.entry_search_contaner').hide();
				},300)
				
			
		});
		jQuery('.pl_search_field').on("keyup", function(){
			var search_elementItems = '';
			var searchTerm      = jQuery(this).val();
			var	entrySearch_sec   = jQuery(".entry_search_contaner");
				
			jQuery('.entry_search_container').show();
			
			if(searchTerm.length > 0){
				jQuery('.clear_field').show();
				jQuery('.all_hooks').removeClass('single_selected'); 
				var element_heading = jQuery('.single-hook') ;
				element_heading.each(function( index ) {
				
					var ele_str = jQuery( this ).text();
					if(ele_str.toLowerCase().indexOf(searchTerm.toLowerCase()) != -1){
						jQuery(this).show();
						jQuery(this).addClass('active');
						var searchLabel = jQuery( this ).find('span.main-label').html();
						var customClass = searchLabel.toLowerCase().replace(/\s+/g, '');
						jQuery(this).addClass('filter-'+customClass)
						if(jQuery( this ).parents('a').length > 0){
							search_elementItems += '<li><a href="'+jQuery( this ).parents('a').attr('href')+'">'+ jQuery( this ).text()+ '</a></li>';
						}else{	
							var top = jQuery(this).position().top;
							search_elementItems += '<li><a class="scroll_element_item" data-label="'+searchLabel+'"data-filter="'+customClass+'" data-top="'+top+'" href="javascript:void(0);">'+ jQuery( this ).find('span.main-label').html()+ '</a></li>';
						}
					}else{
						jQuery(this).hide();
						jQuery(this).removeClass('active');
						
					}
				});				
				
				if( null == search_elementItems || "" == search_elementItems ) {
					
					search_elementItems = '<li>No matching.</li>';
				}
				search_elementItems = '<ul>'+search_elementItems+'</ul>';
				jQuery(".entry_search_contaner").html( search_elementItems );
				
			}else{
				jQuery('.single-hook').show();
				jQuery('.single-hook').removeClass('active');
				get_all_hooks();
				jQuery('.clear_field').hide();
			}
			
		});	
		
		function scrollElem(dataFilter){
			jQuery('.single-hook').hide();
			jQuery('.single-hook.filter-'+dataFilter).show();
			jQuery('.all_hooks').addClass('single_selected');
			jQuery('.clear_field').show();
			jQuery('.entry_search_contaner').html('');
			return;
		}

		jQuery( "body" ).delegate( ".scroll_element_item", "click", function() {
			var top = jQuery(this).attr('data-top');
			var dataFilter = jQuery(this).attr('data-filter');
			scrollElem(dataFilter);
			jQuery('.pl_search_field').val(jQuery(this).attr('data-label'));
		});
		
		jQuery( "body" ).delegate( ".used_hook_btn", "click", function() {
			var dataFilter = jQuery(this).attr('data-filter');
			scrollElem(dataFilter);
			jQuery('.pl_search_field').val(jQuery(this).attr('data-label'));
		});
		jQuery('body').click(function(e) {
			var container = jQuery(".menu-header-search");
			// If the target of the click isn't the container
			if(!container.is(e.target) && container.has(e.target).length === 0){
				jQuery('.entry_search_container').hide();
			}
		});
		
		get_all_hooks();
		
		jQuery('button.clear_field').click(function(){
			jQuery('.pl_search_field').val('');
			jQuery(this).hide();
			jQuery('.single-hook').show();
			jQuery('.all_hooks').removeClass('single_selected');
		});
		
		// End
		// For Logs Functionality
		
		function w3SpeedsterAjaxLoadLog(limit,issueType,urls,startDate,endDate,deviceType,paged,refBy){
			jQuery('.log-data-table').addClass('loading');
			jQuery.ajax({
				url: "<?php echo esc_url(admin_url('admin-ajax.php')); ?>",
				method: 'POST',
				data: {
					'action': 'w3SpeedsterGetLogData',
					'getBy'	:  'ajax',
					'limit' : limit,
					'issuetype' : issueType,
					'url' : urls,
					'start_date' : startDate,
					'end_date' : endDate,
					'paged' : paged,
					'deviceType' : deviceType,
				},
				success: function(data){
					jQuery('.log-data-table').html(data);
					jQuery('.log-data-table').removeClass('loading');
					
				},error: function (errorThrown){
					console.log(errorThrown);
				}
			})
		}
		jQuery('.btn-log-delete').on('click',function(){
			var timeValue = jQuery('.log_select').val();
			jQuery('.log-data-table').addClass('loading');
			jQuery.ajax({
				url: "<?php echo esc_url(admin_url('admin-ajax.php')); ?>",
				method: 'POST',
				data: {
					'action' : 'w3SpeedsterDeleteLogData',
					'time_interval' :  timeValue,
				},
				success: function(data){
					jQuery('.log-data-table').html(data);
					jQuery('.log-data-table').removeClass('loading');
					
				},error: function (errorThrown){
					console.log(errorThrown);
				}
			})
		})
		
		function filterClearDefaultValue(){
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
		
		function getLogData(page = ''){
			var limit = jQuery('.show_log_entry').val();
			var issueType = jQuery('.filter_by_issuetype').val();
			var url = jQuery('#filter_by_url').val();
			var startDate = jQuery('.start_date').val();
			var endDate	= jQuery('.end_date').val();
			var deviceType = jQuery('.filter_by_deviceType').val();
			var  paged = '';
			if(page > 0){
				paged = page;
			}else{
				paged = jQuery('.p-num.active').attr('data-page');
			}
			
			w3SpeedsterAjaxLoadLog(limit,issueType,url,startDate,endDate,deviceType,paged,'');
		}

		jQuery(document).on('click', '.pagination .p-num',function(){
			jQuery('.p-num').removeClass('active');
			jQuery(this).addClass('active')
			getLogData();
		});
		jQuery(document).on('click', '.pagination .page-next',function(){
			jQuery('.p-num').removeClass('active');
			var page = jQuery(this).attr('data-page');
			getLogData((parseInt(page) + 1));
		});
		
		jQuery(document).on('click', '.pagination .page-next-last',function(){
			jQuery('.p-num').removeClass('active');
			var page = jQuery(this).attr('data-page');
			console.log(page,'rocket')
			getLogData(parseInt(page));
		});
		jQuery(document).on('click', '.pagination .page-prev',function(){
			jQuery('.p-num').removeClass('active');
			var page = jQuery(this).attr('data-page');
			var updatedPage = (parseInt(page) - 1);
			if(updatedPage > 1){
				updatedPage = (parseInt(page) - 1);
			}else{
				updatedPage = 1;
			}
			getLogData(updatedPage);
		});


		jQuery(document).on('click', '.btn-log-refresh, .btn-apply-filter', function(){
			getLogData();
		});
		
		jQuery('.btn-rem-filter').click(function(){
			var limit = jQuery('.show_log_entry').val();
			filterClearDefaultValue();
			w3SpeedsterAjaxLoadLog(limit,'','','','','',1,'refresh');
		});
		
		jQuery('.show_log_entry').on('change',function(){
			getLogData();
		})
		
		jQuery('#enable-webvitals-log').on('change',function(){
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
		
		
		jQuery(document).on('keyup', '.custom_select_inp', function() {
		var text = jQuery(this).val();
		if(text.length > 0){
			jQuery('.btn_clear_url_inp').show();
		}
		if(text.length > 2){
			jQuery('#custom_select_url').show();
			jQuery.ajax({
				url: "<?php echo esc_url(admin_url('admin-ajax.php')); ?>",
				method: 'POST',
				data: {
					'action': 'w3SpeddsterShowUrlSuggestions',
					's_text': text,
				},
				success: function(response) {
					console.log('resp--',response.length,'---',response)
					var responseData = JSON.parse(response);
					if(responseData.length == 0){
						jQuery('#custom_select_url').html('No Url Found');
					}else{
						
						var selectedValues = jQuery('#filter_by_url').val();
						
						var createdOptions =[];
						jQuery('#filter_by_url').find('option').each(function(){
							createdOptions.push(jQuery(this).val());
						});
						
						var createdOptionsWithCheckobx = [];
						jQuery('.single-url .url').each(function(){
							createdOptionsWithCheckobx.push(jQuery(this).html());
						})
						var options = '';
						
							var optionsWithCheckbox = '<ul class="option_checkobx">';
						
						
						jQuery.each(responseData, function(index, value) {
							var checkedUrl = '';
							if(jQuery.inArray(value,selectedValues) != -1){
								checkedUrl = 'checked';
							}
							if(jQuery.inArray(value,createdOptions) == -1){
								options += '<option value="'+value+'">'+value+'</option>';
								
							}
								optionsWithCheckbox += '<div class="single-url"><div class="url">'+value+'</div><input type="checkbox" name="temp_input" class="url_checkbox" value="" '+checkedUrl+'></div>';
						});
						
							optionsWithCheckbox += '</ul>';
						
						if(options.length > 0){
							jQuery('#filter_by_url').append(options);
						}
						jQuery('#custom_select_url').html(optionsWithCheckbox);
					}
				},
				error: function(errorThrown) {
					console.log(errorThrown);
				}
			});
		}else if(text.length == 0){
			jQuery('#custom_select_url').hide();
			jQuery('.btn_clear_url_inp').hide();
		}
	});

	jQuery('.btn_clear_url_inp').on('click',function(){
		jQuery('.custom_select_inp').val('');
		jQuery(this).hide();
	})
	jQuery(document).on('change', '.url_checkbox', function() {
		var selectedUrls = jQuery('#filter_by_url').val();
		var url = jQuery(this).parent('.single-url').find('.url').html();
		if(jQuery(this).is(":checked")) {
			if(jQuery.inArray(url,selectedUrls) == -1){
				selectedUrls.push(url); 
			}
		}else {
			selectedUrls = selectedUrls.filter(function(item) {
			return item !== url;
			});
		}
		jQuery('#filter_by_url').val(selectedUrls);
		jQuery('#filter_by_url').trigger('change');

		if(selectedUrls.length > 0){
			jQuery('span.select2.select2-container.select2-container--default').show();
		}else{
			jQuery('span.select2.select2-container.select2-container--default').hide();
		}
		
	});

	jQuery("#custom_select_url").on("click", function(event){
		event.stopPropagation();
	});

	jQuery(document).on("click", function(event){
		if (!jQuery(event.target).closest("#custom_select_url").length) {
			jQuery("#custom_select_url").hide();
		}
	});
	});
	jQuery(document).ready(function($) {
		var $textareas = jQuery('.hook_before_start');
		var customEditorSettings = wp.codeEditor.defaultSettings ? _.clone(wp.codeEditor.defaultSettings) : {};
		
		// Set the mode to PHP
		customEditorSettings.codemirror.mode = "text/x-php";
		customEditorSettings.codemirror.lineNumbers = false;
		customEditorSettings.codemirror.autoRefresh = true; 
		
		$textareas.each(function() {
			var textareaId = jQuery(this).attr('id');
			var editor = wp.codeEditor.initialize(textareaId, customEditorSettings);
			
		});
	});



// Add class for fixed menu in mobile

	document.addEventListener('scroll', function () {
    const scrollPosition = window.scrollY || window.pageYOffset;
    const images = document.querySelectorAll('.admin-speedster .tab-panel')[0];
	if(images.length > 0){
    images.forEach(image => {
        if (scrollPosition > 50) {
            image.classList.add('fixed');
        } else {
            image.classList.remove('fixed');
        }
    });
	}
});
</script>