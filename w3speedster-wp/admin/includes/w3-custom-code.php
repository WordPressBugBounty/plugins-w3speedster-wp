<section id="w3_custom_code" class="tab-pane fade">
	<div class="header w3d-flex gap20">
		<div class="heading_container">
			<h4 class="w3heading">
				<?php $admin->translate('Custom Code'); ?>
			</h4>
			<span class="info"><a
					href="https://w3speedster.com/w3speedster-documentation/"><?php $admin->translate('More info'); ?>?
				</a></span>
		</div>
		<div class="icon_container"> <img
				src="<?php echo W3SPEEDSTER_URL; ?>assets/images/custom-code-icon1.webp"></div>
	</div>
	<hr>
	<div class="css_box" id="css-box">
		<label><?php $admin->translate('Custom CSS to Load on Page Load'); ?> <span
				class="info"></span><span
				class="info-display"><?php $admin->translate('Enter custom css which works only when css optimization is applied'); ?></span></label>
		<div class="fullview">
			<textarea name="custom_css" rows="10" title="Custom css to load with preload css"
				placeholder="<?php $admin->translate('Please Enter css without the style tag.'); ?>"><?php if (!empty($result['custom_css']))
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
		<label><?php $admin->translate('Custom Javascript to Load on Page Load'); ?> <span
				class="info"></span><span
				class="info-display"><?php $admin->translate('Enter javascript code which needs to be loaded before page load.'); ?></span></label>
		<div class="fullview">
			<textarea name="custom_javascript" rows="10" title="Custom "
				placeholder="<?php $admin->translate('Please javascript without script tag'); ?>"><?php if (!empty($result['custom_javascript']))
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
				<label for="load-as-file"><?php $admin->translate('Load as file'); ?> &nbsp;</label>
				<input type="checkbox" name="custom_javascript_file" <?php if (!empty($result['custom_javascript_file']) && $result['custom_javascript_file'] == "on")
					echo "checked"; ?> id="load-as-file">
			</div>
			&nbsp;
			<div class="w3d-flex ">
				<label for="defer"><?php $admin->translate('Defer'); ?> &nbsp;</label>
				<input type="checkbox" name="custom_javascript_defer" <?php if (!empty($result['custom_javascript_defer']) && $result['custom_javascript_defer'] == "on")
					echo "checked"; ?> id="defer">
			</div>
		</div>
	</div>
	<hr>
	<div class="js_box" id="custom-js-box<?php echo $tab == 'w3_custom_code' ? ' active in' : ''; ?>">
		<label><?php $admin->translate('Custom Javascript to Load After Page Load'); ?> <span
				class="info"></span><span
				class="info-display"><?php $admin->translate('Enter javascript which loads after page load load.'); ?></span></label>
		<div class="fullview">
			<textarea name="custom_js" rows="10" title="Custom "
				placeholder="<?php $admin->translate('Please Enter Js without the script tag'); ?>"><?php if (!empty($result['custom_js']))
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
		<input type="button" value="<?php $admin->translate('Save Changes'); ?>"
			class="btn hook_submit">
		<div class="in-progress w3d-flex save-changes-loader" style="display:none">
			<img src="<?php echo W3SPEEDSTER_URL; ?>assets/images/loader-gif.gif" alt="loader"
				class="loader-img">
		</div>
	</div>
</section>
