<section id="opt_img" class="tab-pane fade<?php echo $tab == 'opt_img' ? ' active in' : ''; ?>">
	<div class="header w3d-flex gap20">
		<div class="heading_container">
			<h4 class="w3heading">
				<?php $admin->translate('Image Optimization'); ?>
			</h4>
			<span class="info"><a
					href="https://w3speedster.com/w3speedster-documentation/#img_optimization"><?php $admin->translate('More info'); ?>?
				</a></span>
		</div>
		<div class="icon_container"> <img
				src="<?php echo W3SPEEDSTER_URL; ?>assets/images/image-icon.webp"></div>
	</div>
	<hr>
	<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
		<label><?php $admin->translate('Optimize JPG/PNG Images'); ?><span class="info"></span><span
				class="info-display"><?php $admin->translate('Enable to optimize jpg and png images.'); ?></span></label>
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
		<label><?php $admin->translate('JPG PNG Image Quality'); ?><span class="info"></span><span
				class="info-display"><?php $admin->translate('90 ecommended'); ?></span></label>
		<div class="input_box">
			<input type="text" name="img_quality"
				value="<?php echo !empty($result['img_quality']) ? $admin->esc_attr($result['img_quality']) : 90; ?>"
				id="webp-image-quality" value="90%" style="max-width:70px;text-align:center">
		</div>
	</div>
	<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
		<label><?php $admin->translate('Convert to Webp'); ?><span class="info"></span><span
				class="info-display"><?php $admin->translate('This will convert and render images in webp. Need to start image optimization in image optimization tab'); ?></span></label>
		<div class="w3d-flex">
			<label for="jpg"><?php $admin->translate('JPG'); ?>&nbsp;</label>
			<input type="checkbox" name="webp_jpg" <?php if (!empty($result['webp_jpg']) && $result['webp_jpg'] == "on")
				echo "checked"; ?> id="jpg" class="main-opt-img">
		</div>
		<div class="w3d-flex">
			<label for="png"><?php $admin->translate('PNG'); ?>&nbsp;</label>
			<input type="checkbox" name="webp_png" <?php if (!empty($result['webp_png']) && $result['webp_png'] == "on")
				echo "checked"; ?> id="png" class="main-opt-img">
		</div>
	</div>
	<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
		<label><?php $admin->translate('Webp Image Quality'); ?><span class="info"></span><span
				class="info-display"><?php $admin->translate('90 recommended'); ?></span></label>
		<div class="input_box">
			<input type="text" name="webp_quality"
				value="<?php echo !empty($result['webp_quality']) ? $admin->esc_attr($result['webp_quality']) : 90; ?>"
				id="webp-image-quality" value="90%" style="max-width:70px;text-align:center">
		</div>
	</div>

	<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
		<label><?php $admin->translate('Enable Lazy Load'); ?><span class="info"></span><span
				class="info-display"><?php $admin->translate('This will enable lazy loading of resources.'); ?></span></label>
		<div class="w3d-flex">
			<label for="image"><?php $admin->translate('Image'); ?>&nbsp;</label>
			<input type="checkbox" name="lazy_load" <?php if (!empty($result['lazy_load']) && $result['lazy_load'] == "on")
				echo "checked"; ?> id="image" class="lazy-reso">
		</div>
		<div class="w3d-flex">
			<label for="iframe"><?php $admin->translate('Iframe'); ?>&nbsp;</label>
			<input type="checkbox" name="lazy_load_iframe" <?php if (!empty($result['lazy_load_iframe']) && $result['lazy_load_iframe'] == "on")
				echo "checked"; ?> id="iframe" class="lazy-reso">
		</div>
		<div class="w3d-flex">
			<label for="video"><?php $admin->translate('Video'); ?>&nbsp;</label>
			<input type="checkbox" name="lazy_load_video" <?php if (!empty($result['lazy_load_video']) && $result['lazy_load_video'] == "on")
				echo "checked"; ?> id="video" class="lazy-reso">
		</div>
		<div class="w3d-flex">
			<label for="audio"><?php $admin->translate('Audio'); ?>&nbsp;</label>
			<input type="checkbox" name="lazy_load_audio" <?php if (!empty($result['lazy_load_audio']) && $result['lazy_load_audio'] == "on")
				echo "checked"; ?> id="audio" class="lazy-reso">
		</div>
	</div>

	<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
		<label><?php $admin->translate('Pixels To load Resources Below the Viewport'); ?><span
				class="info"></span><span
				class="info-display"><?php $admin->translate('Enter pixels to start loading of resources like images, video, iframes, background images, audio which are below the viewport. For eg. 200'); ?></span></label>
		<div class="input_box">
			<label for="lazy-px">
				<input type="text" name="lazy_load_px"
					value="<?php echo !empty($result['lazy_load_px']) ? $admin->esc_attr($result['lazy_load_px']) : 200; ?>"
					id="lazy-px" placeholder="<?php $admin->translate('200px'); ?>"
					style="max-width:70px;text-align:center">
			</label>
		</div>
	</div>
	<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
		<label><?php $admin->translate('Load SVG Inline Tag as URL'); ?><span
				class="info"></span><span
				class="info-display"><?php $admin->translate('Load SVG inline tag as url to avoid large DOM elements'); ?></span></label>
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
		<label><?php $admin->translate('Optimize Images via wp-cron'); ?><span
				class="info"></span><span
				class="info-display"><?php $admin->translate('Optimize images via wp-cron.'); ?></span></label>
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
		<label><?php $admin->translate('Optimize Images on the go'); ?><span
				class="info"></span><span
				class="info-display"><?php $admin->translate('Automatically optimize images when site pages are crawled. Recommended to turn off after initial first crawl of all pages.'); ?></span></label>
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
		<label><?php $admin->translate('Automatically Optimize Images on Upload'); ?><span
				class="info"></span><span
				class="info-display"><?php $admin->translate('Automatically optimize new images on upload. Turn off if upload of images is taking more than expected.'); ?></span></label>
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
		<label><?php $admin->translate('Responsive Images'); ?><span class="info"></span><span
				class="info-display"><?php $admin->translate('Load smaller images on mobile to reduce load time'); ?></span></label>
		<div class="input_box">
			<label class="switch" for="resp-imgs">
				<input type="checkbox" name="resp_bg_img" <?php if (!empty($result['resp_bg_img']) && $result['resp_bg_img'] == "on")
					echo "checked"; ?> id="resp-imgs" class="resp-img">
				<div class="checked"></div>
			</label>
		</div>
	</div>
	<div class="w3d-flex gap20 <?php echo $hidden_class; ?>">
		<label><?php $admin->translate('Insert Aspect Ratio in Img Tag'); ?><span
				class="info"></span><span
				class="info-display"><?php $admin->translate('Insert aspect ratio in Img tag inline style.'); ?></span></label>
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
		<strong><?php echo ($img_remaining <= 0) ? $admin->translate_('Great Work!, all images are optimized') : $admin->translate_('Images to be optimized') . ' - <span class="progress-number">' . $img_remaining . '</span>'; ?></strong>
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
		<?php echo ($img_remaining <= 0) ? $admin->translate_('Start image optimization again') : $admin->translate_('Start image optimization'); ?>
	</button>
	<button class="reset_image_optimization btn" type="button">
		<?php echo $admin->translate_('Reset'); ?>
	</button>
	<script>
		var start_optimization = 0;
		var offset = 0;
		var img_to_opt = <?php echo $img_to_opt; ?>;
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
		<input type="button" value="<?php $admin->translate('Save Changes'); ?>"
			class="btn hook_submit gen">
		<div class="in-progress w3d-flex save-changes-loader" style="display:none">
			<img src="<?php echo W3SPEEDSTER_URL; ?>assets/images/loader-gif.gif" alt="loader"
				class="loader-img">
		</div>
	</div>
</section>
				