<section id="exclusions" class="tab-pane fade<?php echo $tab == 'exclusions' ? ' active in' : ''; ?>">
	<div class="header w3d-flex gap20">
		<div class="heading_container">
			<h4 class="w3heading">
				<?php $admin->translate('Exclusions'); ?>
			</h4>
			<span class="info"><a
					href="https://w3speedster.com/w3speedster-documentation/"><?php $admin->translate('More info'); ?>?
				</a></span>
		</div>
		<div class="icon_container"> 
			<img src="<?php echo W3SPEEDSTER_URL; ?>assets/images/exclusions-icon1.webp">
		</div>
	</div>
	<hr>
	<div class="way_to_psi">
	<details>
		<summary>
			<h4 class="w3heading w3text-skyblue"><?php $admin->translate('Resources Exclusions'); ?></h4>
		</summary>
		<div class="cdn_resources <?php echo $hidden_class; ?>">
			<div class="w3d-flex gap20 w3align-item-baseline">
				<label
					for="Preload Resources"><?php $admin->translate('Preload Resources'); ?><span
						class="info"></span><span
						class="info-display"><?php $admin->translate('Enter url of the Resources, which are to be preloaded..'); ?></span></label>
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
											placeholder="<?php $admin->translate('Please Enter Resource Url'); ?>"><button
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
							data-placeholder="<?php $admin->translate('Please Enter Resource Url'); ?>"
							class="btn small w3text-white w3bg-success add_more_row"><?php $admin->translate('Add Rule'); ?></button>
					</div>

				</div>

			</div>
		</div>

		<!-- <hr> -->
		<div class="cdn_resources <?php echo $hidden_class; ?>">
			<div class="w3d-flex gap20 w3align-item-baseline">
				<label
					for="Exclude Resources from Lazy Loading"><?php $admin->translate('Exclude Images from Lazy Loading'); ?><span
						class="info"></span><span
						class="info-display"><?php $admin->translate('Enter any matching text of image/iframe/video/audio tag to exclude from lazy loading. For more than one exclusion, click on add rule. For eg. (class / Id / url / alt).'); ?></span></label>
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
											placeholder="<?php $admin->translate('Please Enter matching text of the image here'); ?>"><button
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
							data-placeholder="<?php $admin->translate('Please Enter matching text of the image here'); ?>"
							class="btn small w3text-white w3bg-success add_more_row"><?php $admin->translate('Add Rule'); ?></button>
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
			<h4 class="w3heading w3text-skyblue"><?php $admin->translate('CSS Exclusions'); ?></h4>
		</summary>

	<div class="css_box cdn_resources ">
		<div class="w3d-flex gap20 w3align-item-baseline">
			<label><?php $admin->translate('Exclude Link Tag CSS from Optimization'); ?><span
					class="info"></span><span
					class="info-display"><?php $admin->translate('Enter matching text of css link url, which are to be excluded from css optimization. For each Exclusion, click on add rule'); ?></span></label>
			<div class="input_box">
				<div class="single-row">
					<?php
					if (array_key_exists('exclude_css', $result)) {
						foreach (explode("\r\n", $result['exclude_css']) as $row) {
							if (!empty(trim($row))) {
								?>
								<div class="cdn_input_box minus w3d-flex">
									<input type="text" name="exclude_css[]" value="<?php echo $admin->esc_attr(trim($row)); ?>"
										placeholder="<?php $admin->translate('Please Enter part of link tag css here'); ?>"><button
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
						data-placeholder="<?php $admin->translate('Please Enter part of link tag css here'); ?>"
						class="btn small w3text-white w3bg-success add_more_row"><?php $admin->translate('Add Rule'); ?></button>
				</div>
			</div>
		</div>

	</div>
	<!-- <hr> -->

	<div class="css_box cdn_resources">
		<div class="w3d-flex gap20 w3align-item-baseline">
			<label><?php $admin->translate('Force Lazy Load Link Tag CSS'); ?> <span
					class="info"></span><span
					class="info-display"><?php $admin->translate('Enter matching text of css link url, which are forced to be lazyloaded and will load on user interaction. For each Exclusion, click on add rule.'); ?></span></label>
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
										placeholder="<?php $admin->translate('Please Enter part of link tag css here'); ?>"><button
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
						data-placeholder="<?php $admin->translate('Please Enter part of link tag css here'); ?>"
						class="btn small w3text-white w3bg-success add_more_row"><?php $admin->translate('Add Rule'); ?></button>
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
			<h4 class="w3heading w3text-skyblue"><?php $admin->translate('JS Exclusions'); ?></h4>
		</summary>
		<div class="js_box cdn_resources">
			<div class="w3d-flex gap20 w3align-item-baseline">
				<label><?php $admin->translate('Force Lazy Load Javascript'); ?> <span
						class="info"></span><span
						class="info-display"><?php $admin->translate('Enter matching text of inline javascript which needs to be forced to lazyload. For each Exclusion, click on add rule.'); ?></span></label>
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
											placeholder="<?php $admin->translate('Please Enter matching text of the inline javascript here'); ?>"><button
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
							data-placeholder="<?php $admin->translate('Please Enter matching text of the inline javascript here'); ?>"
							class="btn small w3text-white w3bg-success add_more_row"><?php $admin->translate('Add Rule'); ?></button>
					</div>

				</div>
			</div>
		</div>
		<!-- <hr> -->
		<div class="js_box cdn_resources">
			<div class="w3d-flex gap20 w3align-item-baseline">
				<label><?php $admin->translate('Exclude Javascript Tags from Lazyload'); ?> <span
						class="info"></span><span
						class="info-display"><?php $admin->translate('Enter matching text of javascript url, which are to be excluded from javascript optimization. For each Exclusion, click on add rule.'); ?></span></label>
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
											placeholder="<?php $admin->translate('Please Enter matching text of the javascript here'); ?>"><button
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
							data-placeholder="<?php $admin->translate('Please Enter matching text of the javascript here'); ?>"
							class="btn small w3text-white w3bg-success add_more_row"><?php $admin->translate('Add Rule'); ?></button>
					</div>

				</div>
			</div>
		</div>
		<!-- <hr> -->
		<div class="js_box cdn_resources">
			<div class="w3d-flex gap20 w3align-item-baseline">
				<label><?php $admin->translate('Exclude Inline Javascript from Lazyload'); ?><span
						class="info"></span><span
						class="info-display"><?php $admin->translate('Enter matching text of inline script url, which needs to be excluded from deferring of javascript. For each Exclusion, click on add rule.'); ?></span></label>
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
											placeholder="<?php $admin->translate('Please Enter matching text of the inline javascript here'); ?>"><button
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
							data-placeholder="<?php $admin->translate('Please Enter matching text of the inline javascript here'); ?>"
							class="btn small w3text-white w3bg-success add_more_row"><?php $admin->translate('Add Rule'); ?></button>
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
			<h4 class="w3heading w3text-skyblue"><?php $admin->translate('Pages Exclusions'); ?></h4>
		</summary>
		<div class="cdn_resources <?php echo $hidden_class; ?>">
			<div class="w3d-flex gap20 html-cache-row">
				<label for="Preload Resources"><?php $admin->translate('Exclude pages from HTML caching'); ?><span
						class="info"></span><span
						class="info-display"><?php $admin->translate('Dont cache the url which match rule'); ?></span></label>
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
											placeholder="<?php $admin->translate('Please Enter Url/String'); ?>"><button
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
							data-placeholder="<?php $admin->translate('Please Enter Url/String'); ?>"
							class="btn small w3text-white w3bg-success add_more_row"><?php $admin->translate('Add Rule'); ?></button>
					</div>

				</div>

			</div>
			<div class="w3d-flex gap20 w3align-item-baseline">
				<label
					for="Exclude Pages From Optimization"><?php $admin->translate('Exclude Pages From Optimization'); ?><span
						class="info"></span><span
						class="info-display"><?php $admin->translate('Enter slug of the url to exclude from optimization. For  eg. (/blog/). For home page, enter home url. For each Exclusion, click on add rule'); ?></span></label>
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
											placeholder="<?php $admin->translate('Please Enter Page Url'); ?>"><button
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
							data-placeholder="<?php $admin->translate('Please Enter Page Url'); ?>"
							class="btn small w3text-white w3bg-success add_more_row"><?php $admin->translate('Add Rule'); ?></button>
					</div>

				</div>

			</div>
		</div>
		<div class="css_box cdn_resources">
		<div class="w3d-flex gap20 w3align-item-baseline">
			<label><?php $admin->translate('Exclude Pages from CSS Optimization'); ?> <span
					class="info"></span><span
					class="info-display"><?php $admin->translate('Enter slug of the page to exclude from css optimization'); ?></span></label>
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
										placeholder="<?php $admin->translate('Please Enter Page Url'); ?>"><button
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
						data-placeholder="<?php $admin->translate('Please Enter Page Url'); ?>"
						class="btn small w3text-white w3bg-success add_more_row"><?php $admin->translate('Add Rule'); ?></button>
				</div>
			</div>
		</div>
		</div>
		<div class="js_box cdn_resources">
			<div class="w3d-flex gap20 w3align-item-baseline">
				<label><?php $admin->translate('Exclude Pages from Javascript Optimization'); ?> <span
						class="info"></span><span
						class="info-display"><?php $admin->translate('Enter slug of the page to exclude from javascript optimization'); ?></span></label>
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
											placeholder="<?php $admin->translate('Please Enter Js Page Url'); ?>"><button
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
							data-placeholder="<?php $admin->translate('Please Enter Js Page Url'); ?>"
							class="btn small w3text-white w3bg-success add_more_row"><?php $admin->translate('Add Rule'); ?></button>
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
