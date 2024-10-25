<section id="hooks" class="tab-pane fade<?php echo $tab == 'hooks' ? ' active in' : ''; ?>">
	<div class="header w3d-flex gap20">
		<div class="heading_container">
			<h4 class="w3heading"><?php $admin->translate('Plugin Hooks'); ?></h4>
			<span class="info"><a
					href="https://w3speedster.com/w3speedster-documentation/#plugin-hooks"><?php $admin->translate('More info'); ?>?</a></span>
		</div>
		<div class="icon_container"> <img
				src="https://speedwp.webplus.me/wp-content/plugins/w3speedster-wp/assets/images/php-hook.webp">
		</div>
	</div>
	<div class="search_hooks">
		<input class="pl_search_field" autocomplete="off" name="temp_input" type="search"
			placeholder="<?php $admin->translate('Search...'); ?>" />
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
			<strong><?php $admin->translate('Used Hooks'); ?></strong></h5>
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
					class="main-label"><?php $admin->translate('W3speedster Pre Start Optimization'); ?></span><span
					class="info"></span>
				<span class="info-display">
					<p><strong><?php $admin->translate('Function'); ?>:</strong>
						<?php $admin->translate('w3SpeedsterPreStartOptimization'); ?></p>
					<p><strong><?php $admin->translate('Description'); ?>:</strong>
						<?php $admin->translate('Modify page content pre optimization.'); ?></p>
					<p><strong><?php $admin->translate('Parameter:'); ?></strong>
						<?php $admin->translate('$html = Content visible in pages view source.'); ?>
					</p>
					<p><strong><?php $admin->translate('Return:'); ?></strong><?php $admin->translate(' 1 – Reflect the changes done in html of the page.'); ?>
					</p>
					<p><strong><?php $admin->translate('Example:'); ?></strong><br>
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
					class="main-label"><?php $admin->translate('W3speedster Before Start Optimization'); ?></span><span
					class="info"></span><span class="info-display">
					<p><?php $admin->translate('Function:'); ?>
						<?php $admin->translate('w3SpeedsterBeforeStartOptimization'); ?></p>
					<p><strong><?php $admin->translate('Description:'); ?></strong><?php $admin->translate(' W3Speedster allows you to make changes to the HTML on your site before actually starting the optimization. For instance replace or add in html.'); ?>
					</p>
					<p><strong><?php $admin->translate('Parameter:'); ?></strong><?php $admin->translate(' $html – full html of the page.'); ?>
					</p>
					<p><strong><?php $admin->translate('Return:'); ?></strong><?php $admin->translate(' 1 – Reflect the changes done in html of the page.'); ?>
					</p>
					<p><strong><?php $admin->translate('Example:'); ?></strong><br>
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
					class="main-label"><?php $admin->translate('W3speedster After Optimization'); ?></span>
				<span class="info"></span>
				<span class="info-display">
					<p><?php $admin->translate('Function:'); ?>
						<?php $admin->translate('w3SpeedsterAfterOptimization'); ?></p>
					<p><strong><?php $admin->translate('Description:'); ?></strong><?php $admin->translate(' W3Speedster allows you to make changes to the HTML on your site after the page is optimized by the plugin. For instance replace or add in html.'); ?>
					</p>
					<p><strong><?php $admin->translate('Parameter:'); ?></strong><?php $admin->translate('$html – full html of the page.'); ?>
					</p>
					<p><strong><?php $admin->translate('Return:'); ?></strong><?php $admin->translate(' 1 – Reflect the changes done in html of the page.'); ?>
					</p>
					<p><strong><?php $admin->translate('Example:'); ?></strong><br>
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
					class="main-label"><?php $admin->translate('W3speedster Inner JS Customize'); ?></span>
				<span class="info"></span>
				<span class="info-display">
					<p><?php $admin->translate('Function:'); ?>
						<?php $admin->translate('w3SpeedsterInnerJsCustomize'); ?></p>
					<p><strong><?php $admin->translate('Description:'); ?></strong><?php $admin->translate(' If you want to make changes in your inline JavaScript, W3Speedster allows you to make changes in Inline JavaScript (for instance making changes in inline script you have to enter the unique text from the script to identify the script).'); ?>
					</p>
					<p><strong><?php $admin->translate('Parameter:'); ?></strong><?php $admin->translate('$script_text- The content of the script.'); ?>
					</p>
					<p><strong><?php $admin->translate('Return:'); ?></strong><?php $admin->translate(' $script_text – Content of the script after changes.'); ?>
					</p>
					<p><strong><?php $admin->translate('Example:'); ?></strong><br>
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
					class="main-label"><?php $admin->translate('W3speedster Inner JS Exclude'); ?></span><span
					class="info"></span>
				<span class="info-display">
					<p><?php $admin->translate('Function:'); ?>
						<?php $admin->translate('w3SpeedsterInnerJsExclude'); ?></p>
					<p><strong><?php $admin->translate('Description:'); ?></strong><?php $admin->translate(' Exclude the script tag from lazy loading, which is present in the pages view source. '); ?>
					</p>
					<p><strong><?php $admin->translate('Parameter:'); ?></strong>
						<br><?php $admin->translate('$inner_js = The script tag s content is visible in the page s view source <br> $exclude_js_bool = 0(default) || 1 '); ?>
					</p>
					<p><strong><?php $admin->translate('Return:'); ?></strong>
						<?php $admin->translate('1'); ?> </p>
					<p><strong><?php $admin->translate('Example:'); ?></strong><br>
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
					class="main-label"><?php $admin->translate('W3speedster Internal JS Customize'); ?></span><span
					class="info"></span>
				<span class="info-display">
					<p><?php $admin->translate('Function:'); ?>
						<?php $admin->translate('w3SpeedsterInternalJsCustomize'); ?></p>
					<p><strong><?php $admin->translate('Description:'); ?></strong><?php $admin->translate(' If you wish to make changes in JavaScript files, W3Speedster allows you to make changes in JavaScript Files.'); ?>
					</p>
					<p><strong><?php $admin->translate('Parameter:'); ?></strong>
						<?php $admin->translate('$path- Path of the JS file.'); ?><br>
						<?php $admin->translate('$string – javascript you want to make changes in.'); ?>
					</p>
					<p><strong><?php $admin->translate('Return:'); ?></strong><?php $admin->translate(' $string– make changes in the internal JS file.'); ?>
					</p>
					<p><strong><?php $admin->translate('Example:'); ?></strong><br>
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
					class="main-label"><?php $admin->translate('W3speedster Internal Css Customize'); ?></span>
				<span class="info"></span>
				<span class="info-display">
					<p><?php $admin->translate('Function:'); ?>
						<?php $admin->translate('w3SpeedsterInternalCssCustomize'); ?></p>
					<p><strong><?php $admin->translate('Description:'); ?></strong><?php $admin->translate(' If you want to make changes in your CSS file, W3Speedster allows you to make changes in stylesheet files.'); ?>
					</p>
					<p><strong><?php $admin->translate('Parameter:'); ?></strong>
						<?php $admin->translate('$css- Css content of the file.'); ?><br>
						<?php $admin->translate('$path- path of css file.'); ?>
					</p>
					<p><strong><?php $admin->translate('Return:'); ?></strong><?php $admin->translate(' $css – make the required changes in CSS files.'); ?>
					</p>
					<p><strong><?php $admin->translate('Example:'); ?></strong><br>
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
					class="main-label"><?php $admin->translate('W3speedster Internal Css Minify'); ?></span><span
					class="info"></span> <span class="info-display">
					<p><?php $admin->translate('Function:'); ?>
						<?php $admin->translate('W3speedup_internal_css_minify'); ?></p>
					<p><strong><?php $admin->translate('Description:'); ?></strong><?php $admin->translate(' If you don’t want to minify, W3Speedster allows you to exclude stylesheet files from minify.'); ?>
					</p>
					<p><strong><?php $admin->translate('Parameter:'); ?></strong>
						<?php $admin->translate('$path- path of css file.<br>$css- Css content of the file. '); ?><br>
						<?php $admin->translate('$css_minify- 0 || 1 (default)'); ?>
					</p>
					<p><strong><?php $admin->translate('Return:'); ?></strong><?php $admin->translate(' 1 – it will exclude the entered css file from minification.'); ?><br><?php $admin->translate(' 0 – it will not exclude the entered css file from minification.'); ?>
					</p>
					<p><strong><?php $admin->translate('Example:'); ?></strong><br>
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
					class="main-label"><?php $admin->translate('W3speedster No Critical Css'); ?></span>
				<span class="info"></span>
				<span class="info-display">
					<p><?php $admin->translate('Function:'); ?>
						<?php $admin->translate('w3SpeedsterNoCriticalCss'); ?></p>
					<p><strong><?php $admin->translate('Description:'); ?></strong><?php $admin->translate(' W3Speedster allows you to exclude the pages from the Critical CSS (like search pages).'); ?>
					</p>
					<p><strong><?php $admin->translate('Parameter:'); ?></strong>
						<?php $admin->translate('$url- Stores the url of the page. '); ?><br>
						<?php $admin->translate('$ignore_critical_css- 0 (default) || 1 '); ?>
					</p>
					<p><strong><?php $admin->translate('Return:'); ?></strong><?php $admin->translate(' 1 – it will exclude the page you do not wish to create critical CSS.'); ?>
					</p>
					<p><strong><?php $admin->translate('Example:'); ?></strong><br>
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
					class="main-label"><?php $admin->translate('W3speedster Customize Critical Css'); ?></span><span
					class="info"></span>
				<span class="info-display">
					<p><?php $admin->translate('Function:'); ?>
						<?php $admin->translate('w3SpeedsterCustomizeCriticalCss'); ?></p>
					<p><strong><?php $admin->translate('Description:'); ?></strong><?php $admin->translate(' If you wish to make any changes in Critical CSS, W3Speedster allows you to make changes in generated Critical CSS. For instance if you want to replace/ remove any string/URL from critical CSS (like @font-face { font-family:”Courgette”; to @font-face { ).'); ?>
					</p>
					<p><strong><?php $admin->translate('Parameter:'); ?></strong>
						<?php $admin->translate('$critical_css- Critical Css of the page.'); ?>
					</p>
					<p><strong><?php $admin->translate('Return:'); ?></strong><?php $admin->translate('$critical_css – Reflect the changes made in critical css.'); ?>
					</p>
					<p><strong><?php $admin->translate('Example:'); ?></strong><br>
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
					class="main-label"><?php $admin->translate('W3speedster Disable Htaccess Webp'); ?></span><span
					class="info"></span><span class="info-display">
					<p><?php $admin->translate('Function:'); ?>
						<?php $admin->translate('w3SpeedsterDisableHtaccessWebp'); ?>.</p>
					<p><strong><?php $admin->translate('Description:'); ?></strong><?php $admin->translate(' Our plugin converts .jpg/.png format to WebP format without changing the URL. it disable webp to render from HTACCESS.'); ?>
					</p>
					<p><strong><?php $admin->translate('Parameter:'); ?></strong><?php $admin->translate('$disable_htaccess_webp- 0(default) || 1'); ?>
					</p>
					<p><strong><?php $admin->translate('Return:'); ?></strong><?php $admin->translate('1 – It will add w3.webp at the end of the url for instance (xyz.jpgw3.webp).'); ?>
					</p>
					<p><strong><?php $admin->translate('Example:'); ?></strong><br>
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
					class="main-label"><?php $admin->translate('W3speedster Customize Add Settings'); ?></span><span
					class="info"></span> <span class="info-display">
					<p><?php $admin->translate('Function:'); ?>
						<?php $admin->translate('w3SpeedsterCustomizeAddSettings'); ?></p>
					<p><strong><?php $admin->translate('Description:'); ?></strong><?php $admin->translate(' If you wish to change in variables and paths (URL), W3Speedster allows you to make changes in variables and paths with the help of this plugin function.'); ?>
					</p>
					<p><strong><?php $admin->translate('Parameter:'); ?></strong><?php $admin->translate('$add_settings- settings of the plugin.'); ?>
					</p>
					<p><strong><?php $admin->translate('Return:'); ?></strong><?php $admin->translate('$add_settings – reflect the changes made in variable and path.'); ?>
					</p>
					<p><strong><?php $admin->translate('Example:'); ?></strong><br>
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
					class="main-label"><?php $admin->translate('W3speedster Customize Main Settings'); ?></span><span
					class="info"></span>
				<span class="info-display">
					<p><?php $admin->translate('Function:'); ?>
						<?php $admin->translate('w3SpeedsterCustomizeMainSettings'); ?></p>
					<p><strong><?php $admin->translate('Description:'); ?></strong><?php $admin->translate(' Customize plugin main settings.'); ?>
					</p>
					<p><strong><?php $admin->translate('Parameter:'); ?></strong><?php $admin->translate(' $settings- Plugin main settings array (like: exclude css, cache path etc ) '); ?>
					</p>
					<p><strong><?php $admin->translate('Return:'); ?></strong><?php $admin->translate(' $settings'); ?>
					</p>
					<p><strong><?php $admin->translate('Example:'); ?></strong><br>
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
					class="main-label"><?php $admin->translate('W3speedster Seprate Critical Css For Post Type'); ?></span><span
					class="info"></span>
				<span class="info-display">
					<p><?php $admin->translate('Function:'); ?>
						<?php $admin->translate('w3SpeedsterCreateSeprateCssOfPostType'); ?></p>
					<p><strong><?php $admin->translate('Description:'); ?></strong><?php $admin->translate(' By default our plugin creates a single critical css for post but If you wish to generate separate critical CSS for post. W3Speedster allows you to create critical CSS separately post-wise.'); ?>
					</p>
					<p><strong><?php $admin->translate('Parameter:'); ?></strong><?php $admin->translate('$separate_post_css- Array of post types. '); ?>
					</p>
					<p><strong><?php $admin->translate('Return:'); ?></strong><?php $admin->translate('$separate_post_css – create separate critical css for each post and page.'); ?>
					</p>
					<p><strong><?php $admin->translate('Example:'); ?></strong><br>
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
					class="main-label"><?php $admin->translate('W3speedster Seprate Critical Css For Category'); ?></span><span
					class="info"></span>
				<span class="info-display">
					<p><?php $admin->translate('Function:'); ?>
						<?php $admin->translate('W3speedsterCriticalCssOfCategory'); ?></p>
					<p><strong><?php $admin->translate('Description:'); ?></strong>
						<?php $admin->translate('W3Speedster Create seprate critical css for  categories pages.'); ?>
					</p>
					<p><strong><?php $admin->translate('Parameter:'); ?></strong><?php $admin->translate('$separate_cat_css- Array of Category.'); ?>
					</p>
					<p><strong><?php $admin->translate('Return:'); ?></strong>
						<?php $admin->translate('$separate_cat_css – create separate critical css for each category and tag.'); ?>
					</p>
					<p><strong><?php $admin->translate('Example:'); ?></strong><br>
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
					class="main-label"><?php $admin->translate('W3speedster Change Video To Videolazy'); ?></span><span
					class="info"></span>
				<span class="info-display">
					<p><?php $admin->translate('Function:'); ?>
						<?php $admin->translate('w3SpeedsterVideoToVideoLazy'); ?></p>
					<p><strong><?php $admin->translate('Description:'); ?></strong>
						<?php $admin->translate('Change video tag to videolazy tag'); ?></p>
					<p><strong><?php $admin->translate('Parameter:'); ?></strong>
						<?php $admin->translate('$videolazy- 0(default) || 1'); ?> </p>
					<p><strong><?php $admin->translate('Return:'); ?></strong><?php $admin->translate(' 1 - Change video tag to videolazy tag.'); ?>
					</p>
					<p><strong><?php $admin->translate('Example:'); ?></strong><br>
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
					class="main-label"><?php $admin->translate('W3speedster Change Iframe To Iframlazy'); ?></span><span
					class="info"></span>
				<span class="info-display">
					<p><?php $admin->translate('Function:'); ?>
						<?php $admin->translate('w3SpeedsterIframetoIframelazy'); ?></p>
					<p><strong><?php $admin->translate('Description:'); ?></strong><?php $admin->translate(' Change iframe tag to iframlazy tag.'); ?>
					</p>
					<p><strong><?php $admin->translate('Parameter:'); ?></strong><?php $admin->translate(' $iframelazy- 0(default) || 1'); ?>
					</p>
					<p><strong><?php $admin->translate('Return:'); ?></strong><?php $admin->translate(' 1 - Change iframe tag to iframlazy tag.'); ?>
					</p>
					<p><strong><?php $admin->translate('Example:'); ?></strong><br>
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
					class="main-label"><?php $admin->translate('W3speedster Exclude Image To Lazyload'); ?></span><span
					class="info"></span>
				<span class="info-display">
					<p><?php $admin->translate('Function:'); ?>
						<?php $admin->translate('w3SpeedsterExcludeImageToLazyload'); ?></p>
					<p><strong><?php $admin->translate('Description:'); ?></strong>
						<?php $admin->translate('W3Speedster allows you to exclude the images from optimization dynamically which you don’t want to lazyload.'); ?>
					</p>
					<p><strong><?php $admin->translate('Parameter:'); ?></strong><?php $admin->translate('$exclude_image = 0(default) || 1 <br>$img = Image tag with all attributes<br>$imgnn_arr = Image tag '); ?>
					</p>
					<p><strong><?php $admin->translate('Return:'); ?></strong>
						<?php $admin->translate('1 – it will lazy load the image.'); ?><br>
						<?php $admin->translate('0 – it will not lazy load the image.'); ?></p>
					<p><strong><?php $admin->translate('Example:'); ?></strong><br>
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
					class="main-label"><?php $admin->translate('W3speedster Customize Image'); ?></span><span
					class="info"></span>
				<span class="info-display">
					<p><?php $admin->translate('Function:'); ?>
						<?php $admin->translate('w3SpeedsterCustomizeImage'); ?></p>
					<p><strong><?php $admin->translate('Description:'); ?></strong><?php $admin->translate(' Customize image tags.'); ?>
					</p>
					<p><strong><?php $admin->translate('Parameter:'); ?></strong><?php $admin->translate(' $img = Image tag with all attributes <br>$imgnn = Modified image tag by plugin <br>$imgnn_arr = Image tag attributes array'); ?>
					</p>
					<p><strong><?php $admin->translate('Return:'); ?></strong><?php $admin->translate(' $imgnn- Customized image tags '); ?>
					</p>
					<p><strong><?php $admin->translate('Example:'); ?></strong><br>
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
					class="main-label"><?php $admin->translate('W3speedster Prevent Htaccess Generation'); ?></span><span
					class="info"></span>
				<span class="info-display">
					<p><?php $admin->translate('Function:'); ?>
						<?php $admin->translate('w3SpeedsterPreventHtaccessGeneration'); ?>.</p>
					<p><strong><?php $admin->translate('Description:'); ?></strong><?php $admin->translate('  Our plugin converts .jpg/.png format to WebP format without changing the URL. it disable webp to render from HTACCESS.'); ?>
					</p>
					<p><strong><?php $admin->translate('Parameter:'); ?></strong><?php $admin->translate('$preventHtaccess = 0(default) || 1 '); ?>
					</p>
					<p><strong><?php $admin->translate('Return:'); ?></strong><?php $admin->translate(' 1 – It will add w3.webp at the end of the url for instance (xyz.jpgw3.webp).'); ?>
					</p>
					<p><strong><?php $admin->translate('Example:'); ?></strong><br>
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
					class="main-label"><?php $admin->translate('W3speedster Exclude CSS Filter'); ?></span><span
					class="info"></span>
				<span class="info-display">
					<p><?php $admin->translate('Function:'); ?>
						<?php $admin->translate('W3SpeedsterExcludeCssFilter'); ?></p>
					<p><strong><?php $admin->translate('Description:'); ?></strong><?php $admin->translate(' If you want to dynamically exclude a CSS file from optimization, W3Speedster allows you to exclude it from optimization (like style.css).'); ?>
					</p>
					<p><strong><?php $admin->translate('Parameter:'); ?></strong><?php $admin->translate(' $exclude_css – 0(default) || 1'); ?><br>
						<?php $admin->translate('$css_obj – link tag in object format.'); ?><br>
						<?php $admin->translate('$css – Content of the CSS file you want to make changes in.'); ?><br>
						<?php $admin->translate('$html – content of the webpage.'); ?></p>
					<p><strong><?php $admin->translate('Return:'); ?></strong><?php $admin->translate(' $exclude_css – exclude CSS from optimization.'); ?>
					</p>
					<p><strong><?php $admin->translate('Example:'); ?></strong><br>
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
					class="main-label"><?php $admin->translate('W3speedster Customize Force Lazyload Css'); ?></span><span
					class="info"></span>
				<span class="info-display">
					<p><?php $admin->translate('Function:'); ?><?php $admin->translate('w3SpeedsterCustomizeForceLazyCss'); ?>.
					</p>
					<p><strong><?php $admin->translate('Description:'); ?></strong>
						<?php $admin->translate(' If you wish to Force Lazyload CSS files dynamically for a specific page or pages, you can do so with the W3Speedster, it allows you to dynamically force lazyload stylesheet files (for instance font file like awesome, dashicons and css files).'); ?>
					</p>
					<p><strong><?php $admin->translate('Parameter:'); ?></strong><?php $admin->translate(' $force_lazyload_css – Array containing text to force lazyload which you have mentioned in the plugin configuration.'); ?>
					</p>
					<p><strong><?php $admin->translate('Return:'); ?></strong><?php $admin->translate(' $force_lazyload_css – Array containing text to force lazyload.'); ?>
					</p>
					<p><strong><?php $admin->translate('Example:'); ?></strong><br>
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
					class="main-label"><?php $admin->translate('W3speedster External Javascript Customize'); ?></span><span
					class="info"></span>
				<span class="info-display">
					<p><?php $admin->translate('Function:'); ?>
						<?php $admin->translate('W3SpeedsterExternalJavascriptCustomize'); ?></p>
					<p><strong>
							Description:</strong><?php $admin->translate(' If you want to make changes in your external JavaScript tags, W3Speedster allows you to make changes in external JavaScript tags.'); ?>
					</p>
					<p><strong><?php $admin->translate('Parameter:'); ?></strong>
						<?php $admin->translate('$script_obj – Script in object format.'); ?><br>
						<?php $admin->translate('$script – Content of the JS file you want to make changes in'); ?>
					</p>
					<p><strong><?php $admin->translate('Return:'); ?></strong><?php $admin->translate(' $script_obj – Make changes in Js files from an external source.'); ?>
					</p>
					<p><strong><?php $admin->translate('Example:'); ?></strong><br>
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
					class="main-label"><?php $admin->translate('W3speedster External Javascript Filter'); ?></span><span
					class="info"></span>
				<span class="info-display">
					<p><?php $admin->translate('Function:'); ?>
						<?php $admin->translate('W3SpeedsterExternalJavascriptFilter'); ?></p>
					<p><strong><?php $admin->translate('Description:'); ?></strong><?php $admin->translate(' If you want to dynamically exclude a JavaScript file or inline script from optimization, W3Speedster allows you to exclude it from optimization (like revslider).'); ?>
					</p>
					<p><strong><?php $admin->translate('Parameter:'); ?></strong><?php $admin->translate(' $exclude_js – 0(default) || 1'); ?><br>
						<?php $admin->translate('$script_obj – Script in object format.'); ?><br>
						<?php $admin->translate('$script – Content of the JS file you want to make changes in.'); ?><br>
						<?php $admin->translate('$html – content of the webpage.'); ?></p>
					<p><strong><?php $admin->translate('Return:'); ?></strong>
						<?php $admin->translate('$exclude_js – exclude JS from optimization.'); ?>
					</p>
					<p><strong><?php $admin->translate('Example:'); ?></strong><br>
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
					class="main-label"><?php $admin->translate('W3speedster Customize Script Object'); ?></span><span
					class="info"></span>
				<span class="info-display">
					<p><?php $admin->translate('Function:'); ?>
						<?php $admin->translate('W3SpeedsterCustomizeScriptObject'); ?></p>
					<p><strong><?php $admin->translate('Description:'); ?></strong><?php $admin->translate(' W3Speedster allows you to customize script objects while minifying and combining scripts.'); ?>
					</p>
					<p><strong><?php $admin->translate('Parameter:'); ?></strong><?php $admin->translate('$script_obj- Script in object format.'); ?><br>
						<?php $admin->translate('$script- Content of the JS file you want to make changes in.'); ?>
					</p>
					<p><strong><?php $admin->translate('Return:'); ?></strong><?php $admin->translate(' $script_obj– Make changes in Js files.'); ?>
					</p>
					<p><strong><?php $admin->translate('Example:'); ?></strong><br>
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
					class="main-label"><?php $admin->translate('W3speedster Exclude Internal Js W3 Changes'); ?></span><span
					class="info"></span>
				<span class="info-display">
					<p><?php $admin->translate('Function:'); ?>
						<?php $admin->translate('W3SpeedsterExcludeInternalJsW3Changes'); ?></p>
					<p><strong><?php $admin->translate('Description:'); ?></strong><?php $admin->translate(' Our plugin makes changes in JavaScript files for optimization, if you do not want to make any changes in JavaScript file, W3Speedster allows you to exclude JavaScript files from the plugin to make any changes.'); ?>
					</p>
					<p><strong><?php $admin->translate('Parameter:'); ?></strong>
						<?php $admin->translate('$path- path of your script tags url '); ?><br>
						<?php $admin->translate('$string – JavaScript files content.'); ?><br>
						<?php $admin->translate('$exclude_from_w3_changes = 0(default) || 1'); ?>
					</p>
					<p><strong><?php $admin->translate('Return:'); ?></strong><?php $admin->translate(' 1 – Exclude the JS file from making any changes.'); ?>
						<?php $admin->translate('0 – It will not exclude the JS file from making any changes.'); ?>
					</p>
					<p><strong><?php $admin->translate('Example:'); ?></strong><br>
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
					class="main-label"><?php $admin->translate('W3speedster Exclude Page Optimization'); ?></span><span
					class="info"></span>
				<span class="info-display">
					<p><?php $admin->translate('Function:'); ?>
						<?php $admin->translate('W3SpeedsterExcludePageOptimization'); ?></p>
					<p><strong><?php $admin->translate('Description:'); ?></strong><?php $admin->translate(' W3Speedster allows you to exclude the pages from the Optimization. if you wish to exclude your pages from optimization. (like cart/login pages).'); ?>
					</p>
					<p><strong><?php $admin->translate('Parameter:'); ?></strong><?php $admin->translate('$html = Page viewsources content.<br>$exclude_page_optimization = 0(default) || 1'); ?>
					</p>
					<p><strong><?php $admin->translate('Return:'); ?></strong><?php $admin->translate(' 1 – it will exclude the page from optimization.'); ?>
						<?php $admin->translate('0 – it will not exclude the page from optimization.'); ?>
					</p>
					<p><strong><?php $admin->translate('Example:'); ?></strong><br>
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
					class="main-label"><?php $admin->translate('W3speedster Customize Critical Css File Name'); ?></span><span
					class="info"></span>
				<span class="info-display">
					<p><?php $admin->translate('Function:'); ?>
						<?php $admin->translate('W3SpeedsterCustomizeCriticalCssFileName'); ?></p>
					<p><strong><?php $admin->translate('Description:'); ?></strong><?php $admin->translate(' If you wish to make any changes in Critical CSS filename, W3Speedster allows you to change in critical CSS file names. W3Speedster creates file names for critical CSS files but if you wish to change the name according to your preference this function will help.'); ?>
					</p>
					<p><strong><?php $admin->translate('Parameter:'); ?></strong><?php $admin->translate('$file_name – File name of the critical css.'); ?>
					</p>
					<p><strong><?php $admin->translate('Return:'); ?></strong><?php $admin->translate(' $file_name – New name of the critical css file.'); ?>
					</p>
					<p><strong><?php $admin->translate('Example:'); ?></strong><br>
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
</section>
