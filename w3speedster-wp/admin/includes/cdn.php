<section id="w3-cdn" class="tab-pane fade<?php echo $tab == 'w3-cdn' ? ' active in' : ''; ?>">
	<div class="header w3d-flex gap20">
		<div class="heading_container">
			<h4 class="w3heading">
				<?php $admin->translate('CDN'); ?>
			</h4>
			<span class="info"><a
					href="https://w3speedster.com/w3speedster-documentation/"><?php $admin->translate('More info'); ?>?
				</a></span>
		</div>
		<div class="icon_container"> <img src="<?php echo W3SPEEDSTER_URL; ?>assets/images/general-setting-icon.webp"></div>
	</div>
	<hr>
	<div class="w3-cdn <?php echo $hidden_class; ?>">
		<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
			<label><?php $admin->translate('CDN url'); ?><span class="info"></span><span
					class="info-display"><?php $admin->translate('Enter CDN url with http or https'); ?></span></label>
			<div class="input_box">
				<label for="cdn-url">
					<input type="text" name="cdn" id="cdn-url"
						placeholder="<?php $admin->translate('Please Enter CDN url here'); ?>"
						value="<?php if (!empty($result['cdn']))
							echo $admin->esc_attr($result['cdn']); ?>"></label>
			</div>
		</div>
		<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
			<label><?php $admin->translate('Exclude file extensions from cdn'); ?><span
					class="info"></span><span
					class="info-display"><?php $admin->translate('Enter extension separated by comma which are to excluded from CDN. For eg. (.woff, .eot)'); ?></span></label>
			<div class="input_box">
				<label for="exclude-file-extensions-from-cdn">
					<input type="text" name="exclude_cdn" id="exclude-file-extensions-from-cdn"
						placeholder="<?php $admin->translate('Please Enter extensions separated by comma ie .jpg, .woff'); ?>"
						value="<?php if (!empty($result['exclude_cdn']))
							echo $admin->esc_attr($result['exclude_cdn']); ?>"></label>
			</div>
		</div>
		<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
			<label><?php $admin->translate('Exclude path from cdn'); ?><span
					class="info"></span><span
					class="info-display"><?php $admin->translate('Enter path separated by comma which are to excluded from CDN. For eg. (/wp-includes/)'); ?></span></label>
			<div class="input_box">
				<label for="exclude-path-from-cdn">
					<input type="text" name="exclude_cdn_path" id="exclude-path-from-cdn"
						placeholder="<?php $admin->translate('Please Enter extensions separated by comma'); ?>"
						value="<?php if (!empty($result['exclude_cdn_path']))
							echo $admin->esc_attr($result['exclude_cdn_path']); ?>"></label>
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