<section id="htmlCache" class="tab-pane fade<?php echo $tab == 'htmlCache' ? ' active in' : ''; ?>">
	<div class="header w3d-flex gap20">
		<div class="heading_container">
			<h4 class="w3heading"><?php $admin->translate('HTML Caches'); ?>
			</h4>
			<span class="info"><a
					href="https://w3speedster.com/w3speedster-documentation/"><?php $admin->translate('More info'); ?>?
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
			<label><?php $admin->translate('Enable HTML Caching'); ?><span class="info"></span><span
					class="info-display"><?php $admin->translate('Enable to on html caching'); ?></span></label>
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
			<label><?php $admin->translate('Enable caching for logged in user'); ?><span
					class="info"></span><span
					class="info-display"><?php $admin->translate('Enable caching for logged in user'); ?></span></label>
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
			<label><?php $admin->translate('Serve html cache file by'); ?><span
					class="info"></span><span
					class="info-display"><?php $admin->translate('Check method for serve cache html file'); ?></span></label>
			<div class="input_box w3d-flex gap10">
				<label class="switch" for="htaccess">
					<input value="htaccess" type="radio" name="by_serve_cache_file" <?php if (empty($result['by_serve_cache_file']) || $result['by_serve_cache_file'] == "htaccess")
						echo "checked"; ?> id="htaccess"
						class="basic-set">
					<div class="checked"></div>
				</label>
				<span><?php $admin->translate('Htaccess'); ?></span>
			</div>
			<div class="input_box w3d-flex gap10">
				<label class="switch" for="advanceCache">
					<input value="advanceCache" type="radio" name="by_serve_cache_file" <?php if (!empty($result['by_serve_cache_file']) && $result['by_serve_cache_file'] == "advanceCache")
						echo "checked"; ?> id="advanceCache"
						class="basic-set">
					<div class="checked"></div>
				</label>
				<span><?php $admin->translate('PHP Cache'); ?></span>
			</div>

		</div>
		<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
			<label><?php $admin->translate('Enable caching page with GET parameters'); ?><span
					class="info"></span><span
					class="info-display"><?php $admin->translate('Enable caching page with GET parameters'); ?></span></label>
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
			<label><?php $admin->translate('Minify HTML'); ?><span class="info"></span><span
					class="info-display"><?php $admin->translate('BY minify html You can decrease the size of page'); ?></span></label>
			<div class="input_box">
				<label class="switch" for="minify_html_cache">
					<input type="checkbox" name="minify_html_cache" <?php if (!empty($result['minify_html_cache']) && $result['minify_html_cache'] == "on")
						echo "checked"; ?> id="minify_html_cache" class="basic-set">
					<div class="checked"></div>
				</label>
			</div>
		</div>
		<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
			<label><?php $admin->translate('Cache Expiry Time'); ?><span class="info"></span><span
					class="info-display"><?php $admin->translate('Input an time for cache expiry default time is 3600(1 hour)'); ?></span></label>
			<div class="input_box">
				<label class="html-cache-expiry w3d-flex" for="html-cache-expiry-time">
					<input type="text" name="html_caching_expiry_time"
						value="<?php echo (!empty($result['html_caching_expiry_time']) ? $admin->esc_attr($result['html_caching_expiry_time']) : 3600) ?>"
						id="html-cache-expiry-time" class="basic-set" style="max-width:80px;"><small>&nbsp;
						<?php $admin->translate('*Time delay in seconds'); ?></small>
					<div class="checked"></div>
				</label>
			</div>
		</div>
		<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
			<label><?php $admin->translate('Separate Cache For Mobile'); ?><span
					class="info"></span><span
					class="info-display"><?php $admin->translate('Enable to create separate cache file for mobile'); ?></span></label>
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
			<label><?php $admin->translate('Preload Caching'); ?><span class="info"></span><span
					class="info-display"><?php $admin->translate('Enable to create preload caching'); ?></span></label>
			<div class="input_box">
				<label class="switch" for="enable-preload-caching">
					<input type="checkbox" name="preload_caching" <?php if (!empty($result['preload_caching']) && $result['preload_caching'] == "on")
						echo "checked"; ?> id="enable-preload-caching" class="basic-set">
					<div class="checked"></div>
				</label>
			</div>
		</div>
		<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
			<label><?php $admin->translate('Preload page caching per minute'); ?> <span
					class="info"></span><span
					class="info-display"><?php $admin->translate('how many pages preload per minute'); ?></span></label>
			<div class="input_box">
				<label for="pmin-url">
					<input type="number" name="preload_per_min" id="preload_per_min" min="1" max="12"
						value="<?php echo (!empty($result['preload_per_min'])) ? $admin->esc_attr($result['preload_per_min']) : 1; ?>">
			</div>
		</div>
		<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
			<label><?php $admin->translate('Enable leverage browsing cache'); ?><span
					class="info"></span><span
					class="info-display"><?php $admin->translate('Enable to turn on leverage browsing cache.'); ?></span></label>
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
			<label><?php $admin->translate('Enable Gzip compression'); ?><span
					class="info"></span><span
					class="info-display"><?php $admin->translate('Enable to turn on Gzip compresssion.'); ?></span></label>
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
			<label><?php $admin->translate('Remove query parameters'); ?><span
					class="info"></span><span
					class="info-display"><?php $admin->translate('Enable to remove query parameters from resources.'); ?></span></label>
			<div class="input_box">
				<label class="switch" for="remove-query-parameters">
					<input type="checkbox" name="remquery" <?php if (!empty($result['remquery']) && $result['remquery'] == "on")
						echo "checked"; ?> id="remove-query-parameters"
						class="basic-set">
					<div class="checked"></div>
				</label>
			</div>
		</div>
		<hr>
		<div class="cdn_resources <?php echo $hidden_class; ?>">
			<div class="w3d-flex gap20 w3align-item-baseline">
				<label for="cache_path"><?php $admin->translate('Cache Path'); ?><span
						class="info"></span><span
						class="info-display"><?php $admin->translate('Enter path where cache can be stored. Leave empty for default path'); ?></span></label>
				<div class="input_box">
					<div class="cdn_input_box">
						<input type="text" name="cache_path"
							placeholder="<?php $admin->translate('Please Enter full cache path'); ?>"
							value="<?php echo !empty($result['cache_path']) ? $admin->esc_attr($result['cache_path']) : ''; ?>"
							id="cache_path"
							placeholder="<?php $admin->translate('Please Enter full cache path'); ?>">
						<small
							class="w3d-block"><?php $admin->translate('Default cache path:'); ?>
							<?php echo $admin->addSettings['content_path'] . '/cache'; ?>
						</small>
					</div>
				</div>

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