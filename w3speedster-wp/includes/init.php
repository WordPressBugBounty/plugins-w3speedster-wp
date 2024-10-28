<?php
namespace W3speedster;

checkDirectCall();

class w3speedster extends w3core
{
    public function __construct()
    {
        if (!empty($this->add_settings['wp_get']['delete-wnw-cache'])) {
            add_action('admin_init', array($this, 'w3RemoveCacheFilesHourlyEventCallback'));
            add_action('admin_init', array($this, 'w3RemoveCacheRedirect'));
        }
        if (!empty($_POST['w3speedster-use-recommended-settings'])) {

            $arr = (array) json_decode('{"license_key":"","w3_api_url":"","is_activated":"","optimization_on":"on","cdn":"","exclude_cdn":"","lbc":"on","gzip":"on","remquery":"on","lazy_load":"on","lazy_load_iframe":"on","lazy_load_video":"on","lazy_load_px":"200","webp_jpg":"on","webp_png":"on","webp_quality":"90","img_quality":"90","exclude_lazy_load":"base64\r\nlogo\r\nrev-slidebg\r\nno-lazy\r\nfacebook\r\ngoogletagmanager","exclude_pages_from_optimization":"wp-login.php\r\n\/cart\/\r\n\/checkout\/","cache_path":"","css":"on","load_critical_css":"on","exclude_css":"","force_lazyload_css":"","load_combined_css":"after_page_load","internal_css_delay_load":"10","google_fonts_delay_load":".2","exclude_page_from_load_combined_css":"","custom_css":"","js":"on","exclude_javascript":"","custom_javascript":"","exclude_inner_javascript":"","force_lazy_load_inner_javascript":"googletagmanager\r\nconnect.facebook.net\r\nstatic.hotjar.com\r\njs.driftt.com","load_combined_js":"on_page_load","internal_js_delay_load":"10","exclude_page_from_load_combined_js":"","custom_js":""}');
            w3UpdateOption('w3_speedup_option', $arr);
        }
        $this->settings = $this->w3GetOption('w3_speedup_option', true);

        if ($this->settings == 1) {
            add_action('admin_notices', array($this, 'w3RecommendedSettings'));
        }
		
        $this->settings = !empty($this->settings) && is_array($this->settings) ? $this->settings : array();
		$this->add_settings = array();
        $this->add_settings['HTTP_USER_AGENT'] = !empty($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : '';
		$this->add_settings['wp_get'] = $_GET;
        $this->add_settings['home_url'] = rtrim(home_url(), '/');
        $site_url = explode('/', rtrim(content_url(), '/'));
        array_pop($site_url);
        $this->add_settings['site_url'] = rtrim(implode('/', $site_url),'/');
		if (strpos($this->add_settings['home_url'], '?') !== false) {
            $home_url_arr = explode('?', $this->add_settings['home_url']);
            $this->add_settings['home_url'] = $home_url_arr[0];
        }
		$this->add_settings['network_site_url'] = rtrim(network_site_url(),'/');
		$this->add_settings['is_multisite'] = function_exists('is_multisite') && is_multisite() /*&& $this->add_settings['network_site_url'] != $this->add_settings['site_url']*/;
		$this->add_settings['is_multisite_networkadmin'] = $this->add_settings['is_multisite'] && function_exists('is_network_admin') && is_network_admin();
		$this->add_settings['isMultisiteSubDomain'] = $this->add_settings['is_multisite'] && $this->add_settings['site_url'] != $this->add_settings['network_site_url'];
		$this->add_settings['site_url_arr'] = wp_parse_url($this->add_settings['site_url']);
        $this->add_settings['secure'] = (isset($this->add_settings['home_url']) && strpos($this->add_settings['home_url'], 'https') !== false) ? 'https://' : 'http://';
        $home_url_arr = wp_parse_url($this->add_settings['home_url']);
		$this->settings['main_license_key'] = !empty($this->settings['license_key']) ? $this->settings['license_key'] : 'w3demo-' . $home_url_arr['host'];
        $this->add_settings['image_home_url'] = !empty($this->settings['cdn']) ? rtrim($this->settings['cdn'], '/') : $this->add_settings['site_url'];
        $this->add_settings['enable_cdn'] = $this->add_settings['site_url'] != $this->add_settings['image_home_url'] ? 1 : 0;
        $this->add_settings['w3_api_url'] = !empty($this->settings['w3_api_url']) ? rtrim($this->settings['w3_api_url'],'/') : 'https://cloud.w3speedster.com/optimize';
        //$sitename = 'home';
        $this->add_settings['content_path'] = WP_CONTENT_DIR;
        $wp_content_arr = explode('/', $this->add_settings['content_path']);
        array_pop($wp_content_arr);
        $this->add_settings['document_root'] = rtrim(implode('/', $wp_content_arr), '/');
        $this->add_settings['full_url'] = !empty($_SERVER['HTTP_HOST']) ? $this->add_settings['secure'] . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] : $this->add_settings['home_url'] . $_SERVER['REQUEST_URI'];

        $full_url_array = explode('?', $this->add_settings['full_url']);
        $this->add_settings['full_url_without_param'] = $full_url_array[0];
        $this->add_settings['wp_cache_path'] = (!empty($this->settings['cache_path']) ? $this->settings['cache_path'] : $this->add_settings['content_path'] . '/cache');
        $this->add_settings['root_cache_path'] = $this->add_settings['wp_cache_path'] . '/w3-cache';
        $this->add_settings['critical_css_path'] = (!empty($this->settings['cache_path']) ? $this->settings['cache_path'] : $this->add_settings['content_path']) . '/critical-css';
        $this->add_settings['cache_path'] = str_replace($this->add_settings['document_root'], '', $this->add_settings['root_cache_path']);
        $this->add_settings['cache_url'] = str_replace($this->add_settings['document_root'], $this->add_settings['site_url'], $this->add_settings['root_cache_path']);
        $this->add_settings['upload_path'] = str_replace($this->add_settings['document_root'], '', $this->add_settings['content_path']);
        list($upload_dir['baseurl'],$upload_dir['basedir']) = $this->w3GetUploadsBasepath();
		$upload_base_url = wp_parse_url($upload_dir['baseurl']);
        //$this->add_settings['upload_base_url'] = strpos($upload_dir['baseurl'], $this->add_settings['site_url']) !== false ? $upload_dir['baseurl'] : $this->add_settings['site_url'] . $upload_base_url['path'];
		$this->add_settings['upload_base_url'] = $upload_dir['baseurl'];
        $this->add_settings['upload_base_dir'] = $upload_dir['basedir'];
        $this->add_settings['theme_base_url'] = function_exists('get_theme_root_uri') ? get_theme_root_uri() : '';
		if($this->add_settings['isMultisiteSubDomain']){
			$this->add_settings['theme_base_url'] = str_replace($this->add_settings['site_url'],$this->add_settings['network_site_url'],$this->add_settings['theme_base_url']);
		}
		$theme_root_array = explode('/',$this->add_settings['theme_base_url']);
		$this->add_settings['theme_root'] = array_pop($theme_root_array);
        $this->add_settings['theme_base_dir'] = function_exists('get_theme_root') ? get_theme_root() . '/' : '';
		$this->add_settings['webp_path'] = $this->add_settings['upload_path'] . '/w3-webp';
        $this->add_settings['is_mobile'] = function_exists('wp_is_mobile') ? wp_is_mobile() : 0;
        $this->add_settings['load_ext_js_before_internal_js'] = !empty($this->settings['load_external_before_internal']) ? explode("\r\n", $this->settings['load_external_before_internal']) : array();
        $this->add_settings['load_js_for_mobile_only'] = !empty($this->settings['load_js_for_mobile_only']) ? $this->settings['load_js_for_mobile_only'] : '';
        $this->add_settings['w3_rand_key'] = $this->w3GetOption('w3_rand_key');
		$this->add_settings['excludedImg'] = !empty($this->settings['exclude_lazy_load']) ? explode("\r\n",stripslashes($this->settings['exclude_lazy_load'])) : array();
		$this->add_settings['excludedImg'] = array_merge($this->add_settings['excludedImg'],array('about:blank','gform_ajax'));
        if (!empty($this->add_settings['is_mobile']) && !empty($this->add_settings['load_js_for_mobile_only'])) {
            $this->settings['load_combined_js'] = 'after_page_load';
        }
        if (!empty($this->settings['separate_cache_for_mobile']) && $this->add_settings['is_mobile']) {
            $this->add_settings['css_ext'] = 'mob.css';
            $this->add_settings['js_ext'] = 'mob.js';
            $this->add_settings['preload_css'] = !empty($this->settings['preload_css_mobile']) ? explode("\r\n", $this->settings['preload_css_mobile']) : array();
        } else {
            $this->add_settings['css_ext'] = '.css';
            $this->add_settings['js_ext'] = '.js';
            $this->add_settings['preload_css'] = !empty($this->settings['preload_css']) ? explode("\r\n", $this->settings['preload_css']) : array();
        }
        $this->add_settings['preload_css_url'] = array();
        $this->add_settings['headers'] = function_exists('getallheaders') ? getallheaders() : array();
        $this->add_settings['main_css_url'] = array();
        $this->add_settings['lazy_load_js'] = array();
        $this->add_settings['exclude_cdn'] = !empty($this->settings['exclude_cdn']) ? explode(',', str_replace(' ', '', $this->settings['exclude_cdn'])) : array();
        $this->add_settings['exclude_cdn_path'] = !empty($this->settings['exclude_cdn_path']) ? explode(',', str_replace(' ', '', $this->settings['exclude_cdn_path'])) : '';
        $this->add_settings['webp_enable'] = array();
        $this->add_settings['webp_enable_instance'] = array($this->add_settings['upload_path']);
        $this->add_settings['webp_enable_instance_replace'] = array($this->add_settings['webp_path']);
        $this->settings['webp_png'] = isset($this->settings['webp_png']) ? $this->settings['webp_png'] : '';
        $this->settings['webp_jpg'] = !empty($this->settings['webp_jpg']) ? $this->settings['webp_jpg'] : '';
        if (!empty($this->settings['webp_jpg'])) {
            $this->add_settings['webp_enable'] = array_merge($this->add_settings['webp_enable'], array('.jpg', '.jpeg'));
            $this->add_settings['webp_enable_instance'] = array_merge($this->add_settings['webp_enable_instance'], array('.jpg?', '.jpeg?', '.jpg ', '.jpeg ', '.jpg"', '.jpeg"', ".jpg'", ".jpeg'", ".jpeg&", ".jpg&"));
            $this->add_settings['webp_enable_instance_replace'] = array_merge($this->add_settings['webp_enable_instance_replace'], array('.jpgw3.webp?', '.jpegw3.webp?', '.jpgw3.webp ', '.jpegw3.webp ', '.jpgw3.webp"', '.jpegw3.webp"', ".jpgw3.webp'", ".jpegw3.webp'", ".jpegw3.webp&", ".jpgw3.webp&"));
        }
        if (!empty($this->settings['webp_png'])) {
            $this->add_settings['webp_enable'] = array_merge($this->add_settings['webp_enable'], array('.png'));
            $this->add_settings['webp_enable_instance'] = array_merge($this->add_settings['webp_enable_instance'], array('.png?', '.png ', '.png"', ".png'", ".png&"));
            $this->add_settings['webp_enable_instance_replace'] = array_merge($this->add_settings['webp_enable_instance_replace'], array('.pngw3.webp?', '.pngw3.webp ', '.pngw3.webp"', ".pngw3.webp'", ".pngw3.webp&"));
        }
        $this->add_settings['htaccess'] = 0;

        if (file_exists($this->add_settings['document_root'] . "/.htaccess")) {
            $htaccess = $this->w3speedsterGetContents($this->add_settings['document_root'] . "/.htaccess");
            if (strpos($htaccess, 'W3WEBP') !== false) {
                $this->add_settings['htaccess'] = 1;
            }
        }
        $this->add_settings['critical_css'] = '';
        $this->add_settings['starttime'] = $this->microtime_float();
        if (!empty($this->add_settings['wp_get']['optimize_image'])) {
            add_action('admin_init', array($this, 'w3_optimize_image'));
        }
        if (!empty($this->settings['remquery'])) {
            add_filter('style_loader_src', array($this, 'w3RemoveVerCssJs'), 9999, 2);
            add_filter('script_loader_src', array($this, 'w3RemoveVerCssJs'), 9999, 2);
        }

        if (!empty($this->settings['image_home_url'])) {
            $this->settings['image_home_url'] = rtrim($this->settings['image_home_url']);
        }
        if (!empty($this->settings['lazy_load'])) {
            add_filter('wp_lazy_loading_enabled', '__return_false');
        }
        $this->add_settings['w3UserLoggedIn'] = $this->w3UserLoggedIn();
        $this->add_settings['fonts_api_links'] = array();
        $this->add_settings['fonts_api_links_css2'] = array();
        $this->add_settings['preload_resources'] = array();
        $this->settings['js_is_excluded'] = 0;
        $preventHtaccess = 0;
        if (!empty($this->settings['hook_prevent_generation_htaccess'])) {
			$code = str_replace('$preventHtaccess','$args[0]',$this->settings['hook_prevent_generation_htaccess']);
            $preventHtaccess = $this->hookCallbackFunction($code,$preventHtaccess);
		}
		$critical_css_file = $this->w3GetFullUrlCachePath().'/critical_css.json';
		if(file_exists($critical_css_file)){
			$this->add_settings['critical_css'] = $this->w3speedsterGetContents($critical_css_file);
		}
		$this->add_settings['w3-wget'] = (int)$this->w3GetOption('w3-wget');
		$this->add_settings['wptouch'] = false;
        $exclude_cdn_arr = !empty($this->add_settings['exclude_cdn']) ? $this->add_settings['exclude_cdn'] : array();
		$this->add_settings['blank_image_url'] = (($this->add_settings['enable_cdn'] && !in_array('.png',$exclude_cdn_arr)) ? str_replace($this->add_settings['site_url'],$this->add_settings['image_home_url'],$this->add_settings['upload_base_url']) : $this->add_settings['upload_base_url']).'/blank.png';
        if (is_admin() && !function_exists('w3_prevent_htaccess_generation') && $preventHtaccess == 0) {
            if (!file_exists($this->add_settings['document_root'] . $this->add_settings['webp_path'] . '/.htaccess')) {
                $this->w3CreateFile($this->add_settings['document_root'] . $this->add_settings['webp_path'] . '/.htaccess', '<IfModule mod_cgid.c>' . "\n" . 'Options -Indexes' . "\n" . '</IfModule>');
            }
            if (!file_exists($this->add_settings['root_cache_path'] . '/.htaccess')) {
                $this->w3CreateFile($this->add_settings['root_cache_path'] . '/.htaccess', '<IfModule mod_cgid.c>' . "\n" . 'Options -Indexes' . "\n" . '</IfModule>' . "\n" . '<IfModule mod_rewrite.c>' . "\n" . 'RewriteEngine On' . "\n" . 'RewriteCond %{REQUEST_FILENAME} !-f' . "\n" . 'RewriteRule ^(.*)$ '.str_replace($this->add_settings['document_root'],'',W3SPEEDSTER_DIR).'/check.php?path=%{REQUEST_URI}&url=%{HTTP_REFERER}'.' [L]' . "\n" . '</IfModule>');
				
            }
            if (!file_exists($this->add_settings['critical_css_path'] . '/.htaccess')) {
                $this->w3CreateFile($this->add_settings['critical_css_path'] . '/.htaccess', '<IfModule mod_cgid.c>' . "\n" . 'Options -Indexes' . "\n" . '</IfModule>');
            }
        }
		$this->add_settings['advanced_cache_exist'] = 0;
    }
	function w3GetUploadsBasepath(){
		$upload_dir = wp_upload_dir();
		$base_url = $upload_dir['baseurl'];
		$base_dir = $upload_dir['basedir'];
		if($this->add_settings['isMultisiteSubDomain']){
			$base_url = str_replace($this->add_settings['site_url'], $this->add_settings['network_site_url'],$base_url);
		}
		if (is_multisite() && $this->add_settings['isMultisiteSubDomain'] && strpos($base_url,'sites') !== false) {
			$site_path_parts = explode('/', trim($base_url, '/'));
			$base_url = str_replace("/" . implode('/', array_splice($site_path_parts, -2)), '', $base_url);

			$site_path = str_replace(ABSPATH, '', $base_dir);
			$site_path_parts = explode(DIRECTORY_SEPARATOR, trim($site_path, DIRECTORY_SEPARATOR));
			$base_dir = str_replace(DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, array_slice($site_path_parts, 2)), '', $base_dir);
		}
		return array($base_url,$base_dir);
	}
    
    function w3SaveIndividualSetting($key, $value)
    {
        $settings = $this->w3GetOption('w3_speedup_option', true);
        if (array_key_exists($key, $settings)) {
            $settings[$key] = $value;
            w3UpdateOption('w3_speedup_option', $settings);
            return true;
        }
        return false;
    }
    public function w3HeaderCheck()
    {
        return is_admin()
            || $this->isSpecialContentType()
            || $this->isSpecialRoute()
            || $_SERVER['REQUEST_METHOD'] === 'POST'
            || $_SERVER['REQUEST_METHOD'] === 'PUT'
            || $_SERVER['REQUEST_METHOD'] === 'DELETE';
    }
    public function w3UserLoggedIn()
    {
        if (function_exists('is_user_logged_in')) {
            if (is_user_logged_in()) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }
    

    private function isSpecialRoute()
    {
        $current_url = $this->add_settings['full_url'];

        if (preg_match('/(.*\/wp\/v2\/.*)/', $current_url)) {
            return true;
        }

        if (preg_match('/(.*wp-login.*)/', $current_url)) {
            return true;
        }

        if (preg_match('/(.*wp-admin.*)/', $current_url)) {
            return true;
        }

        return false;
    }

    function w3RecommendedSettings()
    {
        echo '<div class="notice notice-info" id="w3speedster-setup-wizard-notice">';
        printf(
            '<p id="w3speedster-heading"><strong>%s</strong></p>',
            $this->translate_('W3speedster Setup')
        );
        echo '<p><form method="post">';
        submit_button(
            $this->translate_('Use Recommended Settings'),
            'primary',
            'w3speedster-use-recommended-settings',
            false,
            array(
                'id' => 'w3speedster-sw-use-recommended-settings',
                'enabled' => 'enabled',
            )
        );
        echo '</form></p></div>';
    }
    function w3RemoveVerCssJs($src, $handle)
    {
        $src = remove_query_arg(array('ver', 'v'), $src);
        return $src;
    }
    
    function w3speedsterActivateLicenseKey()
    {
        echo wp_kses_post($this->w3speedsterValidateLicenseKey());
        exit;
    }
    function w3speedsterValidateLicenseKey($key = '',$wget=0)
    {
        $key = !empty($this->add_settings['wp_get']['key']) ? $this->add_settings['wp_get']['key'] : $key;
        if (!empty($key)) {
            $options = array(
                        'license_id' => $key,
                        'domain' => base64_encode($this->add_settings['home_url'])
                    );
           
            $response = $this->w3RemoteGet($this->add_settings['w3_api_url'] . '/get_license_detail.php', $options);
            if (!empty($response)) {
                $res_arr = json_decode($response);
                if ($res_arr[0] == 'success') {
                    return wp_json_encode(array('success', 'verified', $res_arr[1]));
                } else {
                    return wp_json_encode(array('fail', 'could not verify-1' . $response));
                }
            }else {
                return wp_json_encode(array('fail', 'could not verify-3'));
            }
        }
    }
    
	function w3DeleteHtmlCacheAfterPreloadCss($url)
    {
        if ($path = $this->w3GetHtmlCachePath($url)) {
            $this->w3DeleteFile($path);
        }

    }
	function isPluginActiveForNetwork( $plugin ) {
		if ( !is_multisite() )
			return false;

		$plugins = get_site_option( 'active_sitewide_plugins');
		if ( isset($plugins[$plugin]) )
			return true;

		return false;
	}
	function isPluginActive( $plugin ) {
		return in_array( $plugin, (array) get_option( 'active_plugins', array() ) ) || $this->isPluginActiveForNetwork( $plugin );
	}
    function w3GetCurlErrorMsg($response){
		return $response->get_error_message();
	}
    function w3PreloadCssPath($url = '')
    {
			 
        $url = empty($url) ? $this->add_settings['full_url_without_param'] : $url;
        if (!empty($this->add_settings['preload_css_url'][$url])) {
            return $this->add_settings['preload_css_url'][$url];
        }
        if (rtrim($url, '/') == rtrim($this->add_settings['home_url'], '/')) {

        } else {
            global $wp_post_types;

			if (function_exists("w3_create_separate_critical_css_of_post_type")) {
                $separate_post_css = w3_create_separate_critical_css_of_post_type();
            } else {
                $separate_post_css = array('page');
            }
			
			if (!empty($this->settings['hook_sep_critical_post_type'])) {
				$code = str_replace('$separate_post_css','$args[0]',$this->settings['hook_sep_critical_post_type']);
                $separate_post_css = $this->hookCallbackFunction($code,$separate_post_css);
            }
			
            if (function_exists("w3_create_separate_critical_css_of_category")) {
                $separate_cat_css = w3_create_separate_critical_css_of_category();
            } else {
                $separate_cat_css = array();
            }

            if (!empty($this->settings['hook_sep_critical_cat'])) {
				$code = str_replace('$separate_cat_css','$args[0]',$this->settings['hook_sep_critical_cat']);
                $separate_cat_css = $this->hookCallbackFunction($code,$separate_cat_css);
            }

            $url_path_arr = explode('/', rtrim($url, '/'));
            $url_path = array_pop($url_path_arr);

            if (!is_page() && (is_single() || is_singular())) {
                global $post;
                if (!in_array($post->post_type, $separate_post_css)) {
                    $url = rtrim($this->add_settings['home_url'], '/') . '/post/' . $post->post_type;
                }
            }
            if (is_404()) {
                $url = rtrim($this->add_settings['home_url'], '/') . '/' . 'w3404';
            }
            if (is_search() || is_page('search')) {
                $url = rtrim($this->add_settings['home_url'], '/') . '/' . 'w3search';
            }
			if (is_archive() || is_category()) {
                $cat = get_queried_object();
				$catname = '';
                if ($cat != null) {
					if(!empty($cat->name)){
						$catname = $cat->name;
					}
					if(!empty($cat->taxonomy)){
						$catname = $cat->taxonomy;
					}
                }
                if (empty($separate_cat_css) || (is_array($separate_cat_css) && count($separate_cat_css) > 0 && !empty($catname) && !in_array($catname, $separate_cat_css))) {
                    $url = rtrim($this->add_settings['home_url'], '/') . '/' . 'archive/' . $catname;
                }
            }
			if (is_author()) {
                $url = rtrim($this->add_settings['home_url'], '/') . '/' . 'author';
            }
        }
        global $page;
        if ($page > 1 || is_paged()) {
			$url_arr = explode('/page/', $url);
			if(count($url_arr) == 1){
				$url_arr = explode('/', trim($url,'/'));
				array_pop($url_arr);
				$url = implode('/',$url_arr);
			}else{
				$url = $url_arr[0];
			}
        }
		if(function_exists('w3_customize_critical_css_path')){
			$url = w3_customize_critical_css_path($url);
		}
        $full_url = str_replace($this->add_settings['secure'], '', rtrim($url, '/'));
        $path = urldecode($this->w3GetCriticalCachePath($full_url));
        $this->add_settings['preload_css_url'][$url] = $path;
        return $path;
    }

    function w3GetCacheUrl($path = '')
    {
        $current_blog = '';
        if (w3CheckMultisite()) {
            $current_blog = '/' . get_current_blog_id();
        }
        $cache_url = $this->add_settings['cache_url'] . $current_blog . (!empty($path) ? '/' . ltrim($path, '/') : '');
        return $cache_url;
    }
	
    function w3GetCachePath($path = '')
    {
        $current_blog = '';
        if (w3CheckMultisite()) {
            $current_blog = '/' . get_current_blog_id();
        }
        $cache_path = $this->add_settings['root_cache_path'] . $current_blog . (!empty($path) ? '/' . $path : '');
        $this->w3CheckIfFolderExists($cache_path);
        return $cache_path;
    }
	
    function w3GetCriticalCachePath($path = '')
    {
        $current_blog = '';
        if (w3CheckMultisite()) {
            $current_blog = '/' . get_current_blog_id();
        }
        $cache_path = $this->add_settings['critical_css_path'] . $current_blog . (!empty($path) ? '/' . $path : '');
        $this->w3CheckIfFolderExists($cache_path);
        return $cache_path;
    }
	function w3Mkdir($path,$permission,$recusive){
		return wp_mkdir_p($path,$permission,$recusive);
	}

    function w3RemoteGet($url,$params=array(),$method = 0){
        
		if($this->add_settings['w3-wget'] == 3 || $method == 3){
            $query = '';
            if(!empty($params) && is_array($params) && count($params) > 0){
                $query = strpos($url,'?') !== false ? '&' : '?';
                foreach($params as $key=>$value){
                    $query .= $key.'='.$value.'&';
                }
                $query = rtrim($query,'&');
            }
			$response = $this->w3Wget($url.$query);
            if(!empty($response)){
                if(!$this->add_settings['w3-wget']){
                    w3UpdateOption('w3-wget',1);
                }
				return $response;
            }else{
                return false;
            }
        }

        if(empty($this->add_settings['w3-wget']) || $this->add_settings['w3-wget'] == 1){
            $options = array(
                'method' => 'GET',
                'timeout' => 10,
                'redirection' => 5,
                'sslverify' => false,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => array(),
                'cookies' => array()
            );
            if(!empty($params)){
                $options = array_merge($options,array('body'=>$params));
            }
			
			if (function_exists('wp_remote_get')) {
                $response = wp_remote_get($url, $options);
				//echo 'rocket'.$url.$this->add_settings['w3-wget']; print_R($options); 
				//echo $url;
				//print_r($response['response']['code']);echo $response['body']; exit;
				if (!is_wp_error($response) && (int)$response['response']['code'] === 200 && !empty($response['body'])) {
                    return $response['body'];
                } else {
                    return $this->w3RemoteGet($url,$params,2);
                }
            }else{
				return $this->w3RemoteGet($url,$params,2);
			}
        }
        if($this->add_settings['w3-wget'] == 2 || $method == 2) {
            $response = $this->w3speedsterGetContents($url);
            if(!empty($response)){
                if(!$this->add_settings['w3-wget']){
                    w3UpdateOption('w3-wget',3);
                }
                return $response;
            }else{
                return $this->w3RemoteGet($url,$params,3);
            }
        }
    }

    function w3DeleteFile($file){
		return wp_delete_file($file);
	}

    function w3DeleteServerCache()
    {
        $options = array(
					'url' => $this->add_settings['home_url'],
					'key' => $this->settings['main_license_key']
					);

        $response = $this->w3RemoteGet($this->add_settings['w3_api_url'] . '/css/delete-css.php', $options);
        if (!empty($response)) {
            return true;
        } else {
            return false;
        }
    }
    function w3RemoveCacheRedirect()
    {
        header("Location:" . add_query_arg(array('delete_wp_speedup_cache' => 1), remove_query_arg('delete-wnw-cache', false)));
        exit;
    }

    function w3Rand(){
		return wp_rand(100, 1000);
	}

    function w3CheckIfPageExcluded($exclude_setting)
    {

        $e_p_from_optimization = !empty($exclude_setting) ? explode("\r\n", $exclude_setting) : array();

        if (!empty($e_p_from_optimization)) {
            foreach ($e_p_from_optimization as $e_page) {
                if (empty($e_page)) {
                    continue;
                }
                if (empty($this->add_settings['wp_get']['testing']) && (is_home() || is_front_page()) && $this->add_settings['home_url'] == $e_page) {
                    return true;
                } else if ($this->add_settings['home_url'] != $e_page) {
                    if (strpos($this->add_settings['full_url'], $e_page) !== false) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
    public function w3IsPluginActive($plugin)
    {
        return in_array($plugin, (array) get_option('active_plugins', array())) || $this->w3IsPluginActiveForNetwork($plugin);
    }

    public function w3IsPluginActiveForNetwork($plugin)
    {
        if (!is_multisite())
            return false;

        $plugins = get_site_option('active_sitewide_plugins');
        if (isset($plugins[$plugin]))
            return true;

        return false;
    }
    function w3CheckSuperCache($path, $htaccess)
    {
        if ($this->w3IsPluginActive('wp-super-cache/wp-cache.php')) {
            return array("WP Super Cache needs to be deactive", "error");
        } else {
            $this->w3DeleteFile($path . "wp-content/wp-cache-config.php");

            $message = "";

            if (file_exists($path . "wp-content/wp-cache-config.php")) {
                $message .= "<br>- be sure that you removed /wp-content/wp-cache-config.php";
            }

            if (preg_match("/supercache/", $htaccess)) {
                $message .= "<br>- be sure that you removed the rules of super cache from the .htaccess";
            }

            return $message ? array("WP Super Cache cannot remove its own remnants so please follow the steps below" . $message, "error") : "";
        }

        return "";
    }
	function w3JsonEncode($array){
		return wp_json_encode($array);
	}

    
	
	function hookCallbackFunction($code,...$args){
		
		
		
		if(!empty($code)){
			$code = stripcslashes($code);
			// @codingStandardsIgnoreLine
			return $args[0];
		}
	}
	
	function w3SpeedsterGetDataAdvancedCacheFile(){
        $cachePath =  ($cachePath) ? $cachePath :str_replace('\\', '/',$this->add_settings['root_cache_path'] . '/html');
		
        $data = '<?php
        /**
         * Advanced Cache PHP file for WordPress
         * Added By W3speedster Pro-'.W3SPEEDSTER_PLUGIN_VERSION.'
         
         */
		$expiryTime = '.($this->settings['html_caching_expiry_time'] ? $this->settings['html_caching_expiry_time'] : 3600).';
		$loggedinCaching = '.(!empty($this->settings['enable_loggedin_user_caching']) ? 1 : 0).';
		$enableCachingGetPara = '.(!empty($this->settings['enable_caching_get_para']) ? 1 : 0).';
		$seprateMobileCaching  = '.(!empty($this->settings['html_caching_for_mobile']) ? 1 : 0).';
		$serveByAdvancedCache  = \''.(!empty($this->settings['by_serve_cache_file']) ? $this->settings['by_serve_cache_file'] : '').'\';
		
		if($serveByAdvancedCache == "htaccess"){
			return;
		}
        if ( ! defined( "ABSPATH" ) ) {
            exit;
        }
		if (!empty($_SERVER["QUERY_STRING"])) {
		
			if (strpos($_SERVER["QUERY_STRING"],"orgurl") !== false) {
				return;
			} 
		}
		if (!empty($_POST)) {
			return;
		}
		if (isAjaxRequest()) {
			return;
		}
		$queryPara = 0;
		if (!empty($_SERVER["QUERY_STRING"])) {
			$queryPara = 1;
			$query = $_SERVER["QUERY_STRING"];
		} 
		
		$userLoggedin = 0;
		 foreach ($_COOKIE as $name => $value) {
            if(preg_match("/wordpress_logged_in/i", $name)){
                $userLoggedin = 1;
            }
           
        }
        
        // Define the cache directory
        $path = $_SERVER["REQUEST_URI"];
        $parsed_url = parse_url($path);
        $path1 = $parsed_url["path"];
		if (!$enableCachingGetPara &&  $queryPara == 1) {
			return;
		}elseif(!empty($query)){
			$path1 .= $query."/";
		}
		if($seprateMobileCaching){
			$cacheDirMobile = "'.$cachePath.'/$path1/w3mob";
			$cacheDirDesktop = "'.$cachePath.'/$path1";
			$userAgent = $_SERVER["HTTP_USER_AGENT"];
			$isMobile = w3speedsterIsMobileDevice($userAgent);
			$type = $isMobile ? "/w3mob/" : "";
			$cacheDir = $isMobile ? $cacheDirMobile : $cacheDirDesktop;
		}else{
			$cacheDir = "'.$cachePath.'/".$path1;
		}
        
        
        
			
		if ($loggedinCaching == 0 &&  $userLoggedin == 1) {
			return;
		}
		
		
		// Define the cache filename
        $cacheFile = $cacheDir . "/index.html";
        // Check if the cache file exists and is not expired
        if (file_exists($cacheFile) && time() - filemtime($cacheFile) < $expiryTime) { // Adjust the expiration time as needed (3600 seconds = 1 hour)
            // Serve the cached HTML
            readfile($cacheFile);
            exit;
        }
		function isAjaxRequest() {
			return isset($_SERVER[\'HTTP_X_REQUESTED_WITH\']) && strtolower($_SERVER[\'HTTP_X_REQUESTED_WITH\']) === \'xmlhttprequest\';
		}
        function w3speedsterIsMobileDevice($userAgent) {
			// Regular expression to identify common mobile user agents
				$pattern = "/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i";
				return preg_match($pattern, $userAgent);
		}';
        return $data;
    }
	
	function w3speedsterIsMobileDevice($user_agent) {   
		// Regular expression to identify common mobile user agents
		$pattern = "/(\bCrMo\b|CriOS|Android.*Chrome\/[.0-9]*\s(Mobile)?|\bDolfin\b|Opera.*Mini|Opera.*Mobi|Android.*Opera|Mobile.*OPR\/[0-9.]+|Coast\/[0-9.]+|Skyfire|Mobile\sSafari\/[.0-9]*\sEdge|IEMobile|MSIEMobile|fennec|firefox.*maemo|(Mobile|Tablet).*Firefox|Firefox.*Mobile|FxiOS|bolt|teashark|Blazer|Version.*Mobile.*Safari|Safari.*Mobile|MobileSafari|Tizen|UC.*Browser|UCWEB|baiduboxapp|baidubrowser|DiigoBrowser|Puffin|\bMercury\b|Obigo|NF-Browser|NokiaBrowser|OviBrowser|OneBrowser|TwonkyBeamBrowser|SEMC.*Browser|FlyFlow|Minimo|NetFront|Novarra-Vision|MQQBrowser|MicroMessenger|Android.*PaleMoon|Mobile.*PaleMoon|Android|blackberry|\bBB10\b|rim\stablet\sos|PalmOS|avantgo|blazer|elaine|hiptop|palm|plucker|xiino|Symbian|SymbOS|Series60|Series40|SYB-[0-9]+|\bS60\b|Windows\sCE.*(PPC|Smartphone|Mobile|[0-9]{3}x[0-9]{3})|Window\sMobile|Windows\sPhone\s[0-9.]+|WCE;|Windows\sPhone\s10.0|Windows\sPhone\s8.1|Windows\sPhone\s8.0|Windows\sPhone\sOS|XBLWP7|ZuneWP7|Windows\sNT\s6\.[23]\;\sARM\;|\biPhone.*Mobile|\biPod|\biPad|Apple-iPhone7C2|MeeGo|Maemo|J2ME\/|\bMIDP\b|\bCLDC\b|webOS|hpwOS|\bBada\b|BREW)/i";
		return preg_match($pattern, $user_agent);
    }
	function w3SpeedsterCreateHTMLCacheFile($html){
		$path = $this->add_settings['full_url'];
		$parsed_url = wp_parse_url($path);
		$userAgent = $this->add_settings['HTTP_USER_AGENT'];
		$isMobile = $this->w3speedsterIsMobileDevice($userAgent);
		$mob_msg = '';
		if(!empty($this->settings['html_caching_for_mobile']) && $isMobile){
			$mob_msg = 'Mobile';
		}
		/*if (!empty($parsed_url['query'])) {
			if (strpos($parsed_url['query'],"orgurl=") !== false) {
				return $html;
			} 
		}*/
		$path = "$_SERVER[REQUEST_URI]";
		$currenturl = rtrim($this->add_settings['full_url'],'/');
		$this->set_cache_file_path();
		if($msg = $this->w3NoHtmlCache($html)){
			$html = preg_replace('/<\!--W3_PAGE_TYPE_[a-z]+-->/i', '', $html);
			return $html.(strlen($msg) > 1 ? $msg : '');
		}else{
			$html = preg_replace('/<\!--W3_PAGE_TYPE_[a-z]+-->/i', '', $html);
		}
		/*if(defined('WP_DEBUG') && WP_DEBUG){
				return $html;
		}*/
		
		$path1 = $parsed_url['path'];	
		$enableCachingGetPara = isset($this->settings['enable_caching_get_para']) ? 1 : 0;
		if($enableCachingGetPara == 0 && !empty($parsed_url['query'])){
			return $html;
		}
		
		
		if(!empty($this->settings['minify_html_cache'])){
			$html = preg_replace("/<\/html>\s+/", "</html>", $html);
			$html = str_replace("\r", "", $html);
			$html = preg_replace("/^\s+/m", "", ((string) $html));
		}
		$fileName = $this->cacheFilePath;
		$endtime = $this->microtime_float();
		$current_time = date("Y-m-d H:i:s");
		if(!file_exists($fileName) || (file_exists($fileName) && (time() - filemtime($fileName)) > $this->settings['html_caching_expiry_time'])){
			$this->w3CreateFile($fileName, $html.'<!--'.$mob_msg.' Cache Created By W3speedster Pro at '.$current_time.' in '.number_format($endtime - $this->add_settings['starttime'],2).' secs-->');
		}
		return $html.'<!--'.$mob_msg.' Cache Created By W3speedster Pro at '.$current_time.' in '.number_format($endtime - $this->add_settings['starttime'],2).' secs-->';
		
	}
	public function w3speedsterGetHtaccessData(){
		$mobile = "";
		$loggedInUser = "";
		$ifIsNotSecure = "";
		$trailing_slash_rule = "";
		$consent_cookie = "";

		$language_negotiation_type = apply_filters('wpml_setting', false, 'language_negotiation_type');
		if(($language_negotiation_type == 2) && $this->w3isPluginActive('sitepress-multilingual-cms/sitepress.php')){
			$cache_path = '/cache/w3-cache/html/%{HTTP_HOST}/';
			$disable_condition = true;
		}else{
			$cache_path = '/cache/w3-cache/html/';
			$disable_condition = false;
		}

		if(isset($_POST["html_caching_for_mobile"]) && $_POST["html_caching_for_mobile"] == "on"){
			$mobile = "RewriteCond %{HTTP_USER_AGENT} !^.*(".$this->getMobileUserAgents().").*$ [NC]"."\n";

			if(isset($_SERVER['HTTP_CLOUDFRONT_IS_MOBILE_VIEWER'])){
				$mobile = $mobile."RewriteCond %{HTTP_CLOUDFRONT_IS_MOBILE_VIEWER} false [NC]"."\n";
				$mobile = $mobile."RewriteCond %{HTTP_CLOUDFRONT_IS_TABLET_VIEWER} false [NC]"."\n";
			}
		}

		if(empty($_POST["enable_loggedin_user_caching"])){
			$loggedInUser = "RewriteCond %{HTTP:Cookie} !wordpress_logged_in"."\n";
		}

		if(!preg_match("/^https/i", get_option("home"))){
			$ifIsNotSecure = "RewriteCond %{HTTPS} !=on";
		}

		if($this->is_trailing_slash()){
			$trailing_slash_rule = "RewriteCond %{REQUEST_URI} \/$"."\n";
		}else{
			$trailing_slash_rule = "RewriteCond %{REQUEST_URI} ![^\/]+\/$"."\n";
		}

		$data = "# BEGIN W3HTMLCACHE"."\n".
				"<IfModule mod_rewrite.c>"."\n".
				"RewriteEngine On"."\n".
				"RewriteBase /"."\n".
				$this->ruleForWpContent()."\n".
				$this->prefixRedirect().
				$this->excludeRules()."\n".
				$this->excludeAdminCookie()."\n".
				$this->http_condition_rule()."\n".
				"RewriteCond %{HTTP_USER_AGENT} !(".$this->get_excluded_useragent().")"."\n".
				"RewriteCond %{HTTP_USER_AGENT} !(W3\sCache\sPreload(\siPhone\sMobile)?\s*Bot)"."\n".
				"RewriteCond %{REQUEST_METHOD} !POST"."\n".
				"RewriteCond %{HTTP:X-Requested-With} !^XMLHttpRequest$ [NC]"."\n".
				$ifIsNotSecure."\n".
				"RewriteCond %{REQUEST_URI} !(\/){2}$"."\n".
				$trailing_slash_rule.
				$this->query_string_rule().
				$loggedInUser.
				$consent_cookie.
				"RewriteCond %{HTTP:Cookie} !comment_author_"."\n".
				//"RewriteCond %{HTTP:Cookie} !woocommerce_items_in_cart"."\n".
				"RewriteCond %{HTTP:Cookie} !safirmobilswitcher=mobil"."\n".
				'RewriteCond %{HTTP:Profile} !^[a-z0-9\"]+ [NC]'."\n".$mobile;
		

		if(ABSPATH == "//"){
			$data = $data."RewriteCond %{DOCUMENT_ROOT}/".W3SPEEDSTER_WP_CONTENT_BASENAME.$cache_path."$1/%{QUERY_STRING}/index.html -f"."\n";
		}else{
			//WARNING: If you change the following lines, you need to update webp as well
			$data = $data."RewriteCond %{DOCUMENT_ROOT}/".W3SPEEDSTER_WP_CONTENT_BASENAME.$cache_path."$1/%{QUERY_STRING}/index.html -f [or]"."\n";
			// to escape spaces
			$tmp_W3SPEEDSTER_WP_CONTENT_DIR = str_replace(" ", "\ ", W3SPEEDSTER_WP_CONTENT_DIR);

			$data = $data."RewriteCond ".$tmp_W3SPEEDSTER_WP_CONTENT_DIR.$cache_path.$this->getRewriteBase(true)."$1/%{QUERY_STRING}/index.html -f"."\n";
		}

		$data = $data.'RewriteRule ^(.*) "/'.$this->getRewriteBase().W3SPEEDSTER_WP_CONTENT_BASENAME.$cache_path.$this->getRewriteBase(true).'$1/%{QUERY_STRING}/index.html" [L]'."\n";
		
		if(!empty($this->settings['html_caching_for_mobile'])){
			if($this->w3isPluginActive('wptouch/wptouch.php') || $this->w3isPluginActive('wptouch-pro/wptouch-pro.php')){
				$this->set_wptouch(true);
			}else{
				$this->set_wptouch(false);
			}

			$data = $data."\n\n\n".$this->update_htaccess_mob($data);
		}

		$data = $data."</IfModule>"."\n".
				"<FilesMatch \"index\.(html|htm)$\">"."\n".
				"AddDefaultCharset UTF-8"."\n".
				"<ifModule mod_headers.c>"."\n".
				"FileETag None"."\n".
				"Header unset ETag"."\n".
				"Header set Cache-Control \"max-age=0, no-cache, no-store, must-revalidate\""."\n".
				"Header set Pragma \"no-cache\""."\n".
				"Header set Expires \"Mon, 29 Oct 1923 20:30:00 GMT\""."\n".
				"</ifModule>"."\n".
				"</FilesMatch>"."\n".
				"# END W3HTMLCACHE"."\n";

		if(is_multisite()){
			return "";
		}else{
			return preg_replace("/\n+/","\n", $data);
		}
	}
	function w3SpeedsterCheckCacheTrue(){
		if(null !== WP_CACHE && !WP_CACHE){
			if($wp_config = @file_get_contents(ABSPATH."wp-config.php")){
				$wp_config = preg_replace("/define\(\s*['|\"]WP_CACHE['|\"]\s*,\s*false\s*\);.*[\r\n|\r|\n]+/", "", $wp_config);
				if(!@file_put_contents(ABSPATH."wp-config.php", $wp_config)){
					return array("wp-config is not writable. define('WP_CACHE', true); is needed to be added into wp-config.php", "error");
				}
			}	
		}
        if(!WP_CACHE){
            if($wp_config = @file_get_contents(ABSPATH."wp-config.php")){
                $wp_config = str_replace("\$table_prefix", "define('WP_CACHE', true);\n\$table_prefix", $wp_config);
				// @codingStandardsIgnoreLine
                if(!@file_put_contents(ABSPATH."wp-config.php", $wp_config)){
                    return array("define('WP_CACHE', true); is needed to be added into wp-config.php", "error");
                }
            }else{
                return array("define('WP_CACHE', true); is needed to be added into wp-config.php", "error");
            }
        }
    }
	public function query_string_rule(){
		$enableCachingGetParaRule = isset($this->settings['enable_caching_get_para']) ? 1 : '';
		if(!$enableCachingGetParaRule){
			return "RewriteCond %{QUERY_STRING} !.+"."\n";
		}else{
			return "RewriteCond %{QUERY_STRING} ^(.*)$"."\n";
		}
	}
	public function is_subdirectory_install(){
		if(strlen(site_url()) > strlen(home_url())){
			return true;
		}
		return false;
	}
	public function getRewriteBase($sub = ""){
		if($sub && $this->is_subdirectory_install()){
			$trimedProtocol = preg_replace("/http:\/\/|https:\/\//", "", trim(home_url(), "/"));
			$path = strstr($trimedProtocol, '/');

			if($path){
				return trim($path, "/")."/";
			}else{
				return "";
			}
		}
		
		$url = rtrim(site_url(), "/");
		preg_match("/https?:\/\/[^\/]+(.*)/", $url, $out);

		if(isset($out[1]) && $out[1]){
			$out[1] = trim($out[1], "/");

			if(preg_match("/\/".preg_quote($out[1], "/")."\//", W3SPEEDSTER_WP_CONTENT_DIR)){
				return $out[1]."/";
			}else{
				return "";
			}
		}else{
			return "";
		}
	}
	public function set_wptouch($status){
		$this->add_settings['wptouch'] = $status;
	}

	public function update_htaccess_mob($data){
		preg_match("/RewriteEngine\sOn(.+)/is", $data, $out);
		$htaccess = "\n##### mobile #####\n";
		$htaccess .= $out[0];

		if($this->add_settings['wptouch']){
			$wptouch_rule = "RewriteCond %{HTTP:Cookie} !wptouch-pro-view=desktop";
			$htaccess = str_replace("RewriteCond %{HTTP:Profile}", $wptouch_rule."\n"."RewriteCond %{HTTP:Profile}", $htaccess);
		}

		$htaccess = str_replace("RewriteCond %{HTTP:Cookie} !safirmobilswitcher=mobil", "RewriteCond %{HTTP:Cookie} !safirmobilswitcher=masaustu", $htaccess);
		$htaccess = str_replace("RewriteCond %{HTTP_USER_AGENT} !^.*", "RewriteCond %{HTTP_USER_AGENT} ^.*", $htaccess);
		$htaccess = preg_replace("/\/index.html/", "/w3mob/index.html", $htaccess);

		//$htaccess = preg_replace("/(\/cache\/)[^\/]+(\/.{1}1\/index\.html)/","$1".$this->get_folder_name()."$2", $htaccess);
		$htaccess .= "\n##### mobile #####\n";

		return $htaccess;
	}

	public function is_trailing_slash(){
		// no need to check if Custom Permalinks plugin is active (https://tr.wordpress.org/plugins/custom-permalinks/)
		if($this->w3isPluginActive("custom-permalinks/custom-permalinks.php")){
			return false;
		}

		if($permalink_structure = get_option('permalink_structure')){
			if(preg_match("/\/$/", $permalink_structure)){
				return true;
			}
		}

		return false;
	}
	protected function get_excluded_useragent(){
		return "facebookexternalhit|Twitterbot|LinkedInBot|WhatsApp|Mediatoolkitbot";
	}
	public function http_condition_rule(){
		$http_host = preg_replace("/(http(s?)\:)?\/\/(www\d*\.)?/i", "", trim(home_url(), "/"));

		if(preg_match("/\//", $http_host)){
			$http_host = strstr($http_host, '/', true);
		}

		if(preg_match("/www\./", home_url())){
			$http_host = "www.".$http_host;
		}

		return "RewriteCond %{HTTP_HOST} ^".$http_host;
	}
	public function excludeAdminCookie(){
		$rules = "";
		$users_groups = array_chunk(get_users(array("role" => "administrator", "fields" => array("user_login"))), 5);

		foreach ($users_groups as $group_key => $group) {
			$tmp_users = "";
			$tmp_rule = "";

			foreach ($group as $key => $value) {
				if($tmp_users){
					$tmp_users = $tmp_users."|".sanitize_user(wp_unslash($value->user_login), true);
				}else{
					$tmp_users = sanitize_user(wp_unslash($value->user_login), true);
				}

				// to replace spaces with \s
				$tmp_users = preg_replace("/\s/", "\s", $tmp_users);

				if(!next($group)){
					$tmp_rule = "RewriteCond %{HTTP:Cookie} !wordpress_logged_in_[^\=]+\=".$tmp_users;
				}
			}

			if($rules){
				$rules = $rules."\n".$tmp_rule;
			}else{
				$rules = $tmp_rule;
			}
		}

		return "# Start_W3SPEEDSTER_Exclude_Admin_Cookie\n".$rules."\n# End_W3SPEEDSTER_Exclude_Admin_Cookie\n";
	}
	public function excludeRules(){
		$htaccess_page_rules = "";
		$htaccess_page_useragent = "";
		$htaccess_page_cookie = "";

		if($rules_json = get_option("W3speedsterCacheExclude")){
			if($rules_json != "null"){
				$rules_std = json_decode($rules_json);

				foreach ($rules_std as $key => $value) {
					$value->type = isset($value->type) ? $value->type : "page";

					// escape the chars
					$value->content = str_replace("?", "\?", $value->content);

					if($value->type == "page"){
						if($value->prefix == "startwith"){
							$htaccess_page_rules = $htaccess_page_rules."RewriteCond %{REQUEST_URI} !^/".$value->content." [NC]\n";
						}

						if($value->prefix == "contain"){
							$htaccess_page_rules = $htaccess_page_rules."RewriteCond %{REQUEST_URI} !".$value->content." [NC]\n";
						}

						if($value->prefix == "exact"){
							$htaccess_page_rules = $htaccess_page_rules."RewriteCond %{REQUEST_URI} !\/".$value->content." [NC]\n";
						}
					}else if($value->type == "useragent"){
						$htaccess_page_useragent = $htaccess_page_useragent."RewriteCond %{HTTP_USER_AGENT} !".$value->content." [NC]\n";
					}else if($value->type == "cookie"){
						$htaccess_page_cookie = $htaccess_page_cookie."RewriteCond %{HTTP:Cookie} !".$value->content." [NC]\n";
					}
				}
			}
		}

		return "# Start W3 Exclude\n".$htaccess_page_rules.$htaccess_page_useragent.$htaccess_page_cookie."# End W3 Exclude\n";
	}
	public function prefixRedirect(){
		$forceTo = "";
		
		if(defined("W3SPEEDSTER_DISABLE_REDIRECTION") && W3SPEEDSTER_DISABLE_REDIRECTION){
			return $forceTo;
		}

		if(preg_match("/^https:\/\//", home_url())){
			if(preg_match("/^https:\/\/www\./", home_url())){
				$forceTo = "\nRewriteCond %{HTTPS} =on"."\n".
						   "RewriteCond %{HTTP_HOST} ^www.".str_replace("www.", "", $_SERVER["HTTP_HOST"])."\n";
			}else{
				$forceTo = "\nRewriteCond %{HTTPS} =on"."\n".
						   "RewriteCond %{HTTP_HOST} ^".str_replace("www.", "", $_SERVER["HTTP_HOST"])."\n";
			}
		}else{
			if(preg_match("/^http:\/\/www\./", home_url())){
				$forceTo = "\nRewriteCond %{HTTP_HOST} ^".str_replace("www.", "", $_SERVER["HTTP_HOST"])."\n".
						   "RewriteRule ^(.*)$ ".preg_quote(home_url(), "/")."\/$1 [R=301,L]"."\n";
			}else{
				$forceTo = "\nRewriteCond %{HTTP_HOST} ^www.".str_replace("www.", "", $_SERVER["HTTP_HOST"])." [NC]"."\n".
						   "RewriteRule ^(.*)$ ".preg_quote(home_url(), "/")."\/$1 [R=301,L]"."\n";
			}
		}
		return $forceTo;
	}
	public function ruleForWpContent(){
		return "";
		$newContentPath = str_replace(home_url(), "", content_url());
		if(!preg_match("/wp-content/", $newContentPath)){
			$newContentPath = trim($newContentPath, "/");
			return "RewriteRule ^".$newContentPath."/cache/(.*) ".W3SPEEDSTER_WP_CONTENT_DIR."/cache/$1 [L]"."\n";
		}
		return "";
	}
	protected function getMobileUserAgents(){
		return implode("|", $this->get_mobile_browsers())."|".implode("|", $this->get_operating_systems());
	}
	public function get_operating_systems(){
		$operating_systems  = array(
								'Android',
								'blackberry|\bBB10\b|rim\stablet\sos',
								'PalmOS|avantgo|blazer|elaine|hiptop|palm|plucker|xiino',
								'Symbian|SymbOS|Series60|Series40|SYB-[0-9]+|\bS60\b',
								'Windows\sCE.*(PPC|Smartphone|Mobile|[0-9]{3}x[0-9]{3})|Window\sMobile|Windows\sPhone\s[0-9.]+|WCE;',
								'Windows\sPhone\s10.0|Windows\sPhone\s8.1|Windows\sPhone\s8.0|Windows\sPhone\sOS|XBLWP7|ZuneWP7|Windows\sNT\s6\.[23]\;\sARM\;',
								'\biPhone.*Mobile|\biPod|\biPad',
								'Apple-iPhone7C2',
								'MeeGo',
								'Maemo',
								'J2ME\/|\bMIDP\b|\bCLDC\b', // '|Java/' produces bug #135
								'webOS|hpwOS',
								'\bBada\b',
								'BREW'
							);
		return $operating_systems;
	}
	public function get_mobile_browsers(){
		$mobile_browsers  = array(
							'\bCrMo\b|CriOS|Android.*Chrome\/[.0-9]*\s(Mobile)?',
							'\bDolfin\b',
							'Opera.*Mini|Opera.*Mobi|Android.*Opera|Mobile.*OPR\/[0-9.]+|Coast\/[0-9.]+',
							'Skyfire',
							'Mobile\sSafari\/[.0-9]*\sEdge',
							'IEMobile|MSIEMobile', // |Trident/[.0-9]+
							'fennec|firefox.*maemo|(Mobile|Tablet).*Firefox|Firefox.*Mobile|FxiOS',
							'bolt',
							'teashark',
							'Blazer',
							'Version.*Mobile.*Safari|Safari.*Mobile|MobileSafari',
							'Tizen',
							'UC.*Browser|UCWEB',
							'baiduboxapp',
							'baidubrowser',
							'DiigoBrowser',
							'Puffin',
							'\bMercury\b',
							'Obigo',
							'NF-Browser',
							'NokiaBrowser|OviBrowser|OneBrowser|TwonkyBeamBrowser|SEMC.*Browser|FlyFlow|Minimo|NetFront|Novarra-Vision|MQQBrowser|MicroMessenger',
							'Android.*PaleMoon|Mobile.*PaleMoon'
							);
		return $mobile_browsers;
	}
	function w3speedsterGetContents($path){
		if (!function_exists('WP_Filesystem')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }
        global $wp_filesystem;
        if (WP_Filesystem()) {
            $content = $wp_filesystem->get_contents($path);
        }else{
			// @codingStandardsIgnoreLine
			$content = file_get_contents($path);
		}
		return $content;
	}
	
	function w3speedsterPutContents($path,$content){
		
		
		if (!function_exists('WP_Filesystem')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }
        global $wp_filesystem;
		if (WP_Filesystem()) {
			$file = $wp_filesystem->put_contents($path, $content);
		}else{
			// @codingStandardsIgnoreLine
			$file = file_put_contents($path,$content);
		}
		return $file;
	}
	
	function w3ScheduleEvent($event, $time){
		if (!wp_next_scheduled($event)) {
			return wp_schedule_event(time(), $time, $event);
		}
		return false;
	}
	function w3UnScheduleEvent($event){
		if (wp_next_scheduled($event)) {
			wp_clear_scheduled_hook($event);
		}
	}
	function getAjaxUrl(){
		return admin_url('admin-ajax.php');
	}
	function getImageOptimizationDetails(){
		global $wpdb;
		$img_to_opt = 1;
		if (w3CheckMultisite()) {
			$blogs = get_sites();
			foreach ($blogs as $b) {
				$table_name = $wpdb->base_prefix . $b->blog_id . '_posts';
				if ($wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($table_name))) == $table_name) {
					$img_to_opt += $wpdb->get_var(
						$wpdb->prepare("SELECT COUNT(ID) FROM `$table_name` WHERE post_type = %s", array('attachment'))
					);
				}
			}
		} else {
			$img_to_opt = $wpdb->get_var("SELECT count(ID) FROM {$wpdb->prefix}posts WHERE post_type='attachment'");
		}
		$opt_offset = $this->w3GetOption('w3speedup_opt_offset');
		$img_remaining = (int) $img_to_opt - (int) $opt_offset;
		return array($img_to_opt,$img_remaining);
	}
	function scheduleImageOptimizationCron($img_remaining){
		if (!empty($this->settings['enable_background_optimization']) && $img_remaining > 0) {
			$this->w3ScheduleEvent('w3speedster_image_optimization', 'w3speedster_every_minute');
		} else {
			$this->w3UnScheduleEvent('w3speedster_image_optimization');
		}
	}
	function esc_url($text){
		return esc_url($text);
	}
	function translate($text){
		_e($text,'w3speedster-wp');
	}
	function esc_attr($text){
		return esc_attr($text);
	}
	function translate_($text){
		return __($text,'w3speedster-wp');
	}
	function createSecureKey($option){
		return wp_create_nonce($option);
	}
	function checkSecurityKey($key,$option){
		return wp_verify_nonce($key,$option);
	}
	function importData($data){
		$import_text = (array)json_decode(stripcslashes($data));
		if($import_text !== null){
			w3UpdateOption( 'w3_speedup_option',  $import_text, 'no');
			add_action( 'admin_notices', array($this,'w3AdminNoticeImportSuccess') );
		}else{
			add_action( 'admin_notices', array($this,'w3AdminNoticeImportFail') );
		}
	}
	function w3AdminNoticeImportSuccess() {
		?>
		<div class="notice notice-success is-dismissible">
			<p><?php _e( 'Data imported successfully!' ); ?></p>
		</div>
		<?php
	}
	function w3AdminNoticeImportFail(){
		?>
		<div class="error notice-error is-dismissible">
			<p><?php _e( 'Data import failed' ); ?></p>
		</div>
		<?php
	}
	function resetImageOptCount(){
		w3UpdateOption('w3speedup_opt_offset',0,'no');
		$redirect_url = remove_query_arg('reset');
		wp_redirect($redirect_url);
		exit;
	}
	function removeAdvanceCacheFile(){
		$advancedCacheFile = WP_CONTENT_DIR . '/advanced-cache.php';
		if (file_exists($advancedCacheFile)){
			$this->w3DeleteFile($advancedCacheFile);
		}
	}
	function removeAdvanceCacheFileAndRedirect(){
		$this->removeAdvanceCacheFile();
		$this->removeAdvanceCacheRedirect();
		
	}
	function removeAdvanceCacheRedirect(){
		$redirect_url = remove_query_arg('delete_ac');
		wp_redirect($redirect_url);
		exit;
	}
	function w3EnqueueAdminHead(){
		if(function_exists('wp_enqueue_code_editor')){
			$cm_settings['codeJs'] = wp_enqueue_code_editor(array('type' => 'text/javascript'));
			$cm_settings['codeCss'] = wp_enqueue_code_editor(array('type' => 'text/css'));
		}else{
			$cm_settings = array();
		}
		?>
		<script>
		var cm_settings = <?php echo $this->w3JsonEncode($cm_settings);?>
		</script>
		<?php
	}
	function checkHtmlCacheSettings(){
		$this->w3ModifyHtaccess();
		if(isset($_POST['html_caching']) && $_POST['html_caching'] == 'on' && $_POST['by_serve_cache_file'] == 'advanceCache'){
			$advancedCacheFile = WP_CONTENT_DIR . '/advanced-cache.php';
			$this->w3SpeedsterRemoveHtmlCacheCode();
			$this->w3SpeedsterCheckCacheTrue();
			if(file_exists($advancedCacheFile) && strpos($this->w3speedsterGetContents($advancedCacheFile),'Added By W3speedster Pro') === false){
				$this->add_settings['advanced_cache_exist'] = 1;
			}elseif(!file_exists($advancedCacheFile)){
				$this->createAdvanceCacheFile($advancedCacheFile);
			}
			
		}elseif(isset($_POST['html_caching']) && $_POST['html_caching'] == 'on' && $_POST['by_serve_cache_file'] == 'htaccess'){
			$htaccessPath = $this->add_settings['document_root'] . "/.htaccess";
			if($htaccessContent = $this->w3speedsterGetContents($htaccessPath)){
				$this->removeAdvanceCacheFile();
				$htaccess = preg_replace("/#\s?BEGIN\s?W3HTMLCACHE.*?#\s?END\s?W3HTMLCACHE/s", "", $htaccessContent);
				$data = $this->w3speedsterGetHtaccessData($array['cache_path']);
				$htaccess = $data. PHP_EOL .$htaccess;
				$this->w3speedsterPutContents($htaccessPath, $htaccess);
			}else{
				return array("htaccess not writable", "w3speedster");
			}
		}elseif(empty($_POST['html_caching'])){
			$this->removeAdvanceCacheFile();
			$this->w3SpeedsterRemoveHtmlCacheCode();
		}
		
	}
	function createAdvanceCacheFile($advancedCacheFile){
		$file_content = $this->w3SpeedsterGetDataAdvancedCacheFile();
		$this->w3speedsterPutContents($advancedCacheFile, $file_content);
	}
	function checkAdvCacheFileExists(){
		$advancedCacheFile = WP_CONTENT_DIR . '/advanced-cache.php';
		// Check if the advanced-cache.php file exists and not by w3speedster
		// @codingStandardsIgnoreLine
		if (file_exists($advancedCacheFile) && strpos(file_get_contents($advancedCacheFile), 'Added By W3speedster Pro') === false && $this->add_settings['advanced_cache_exist'] == 1) {
			echo '<div class="advance-cache-exist-error">' . $this->translate_('The advanced-cache.php file already exists. Please delete this file and remove the plugin that created it.') . '</div>';
			echo '<button type="button" class="btn force-delete-ac"><a href="' . $this->esc_url($_SERVER['REQUEST_URI']) . '&delete_ac=1">' . $this->translate_('Force Delete File') . '</a></button>';
		}
	}
	function enqueueScripts(){
		add_action('admin_enqueue_scripts', array($this,'w3EnqueueAdminScripts') );
		add_action('admin_head',array($this,'w3EnqueueAdminHead'));
	}
	function w3EnqueueAdminScripts(){
		wp_enqueue_style('wp-codemirror');
		wp_enqueue_style('w3-fonts','https://fonts.googleapis.com/css?family=Open+Sans:400,600,700');
		wp_enqueue_style('w3-font-awesome',W3SPEEDSTER_URL.'assets/css/font-awesome.min.css');
		wp_enqueue_style('w3-bootstrap',W3SPEEDSTER_URL.'assets/css/bootstrap.min.css');
		wp_enqueue_style('w3-datatables',W3SPEEDSTER_URL.'assets/css/jquery.dataTables.min.css');
		wp_enqueue_style('w3-admin-main',W3SPEEDSTER_URL.'assets/css/admin.css');
		wp_enqueue_style('w3-jquery-ui',W3SPEEDSTER_URL.'assets/css/jquery-ui.css');
		wp_enqueue_style('w3-select2',W3SPEEDSTER_URL.'assets/css/select2.min.css');
		
		wp_enqueue_script('w3-datatables',W3SPEEDSTER_URL.'assets/js/jquery.dataTables.min.js');
		wp_enqueue_script('w3-prefixfree',W3SPEEDSTER_URL.'assets/js/prefixfree.min.js');
		wp_enqueue_script('w3-bootstrap',W3SPEEDSTER_URL.'assets/js/bootstrap.min.js');
		wp_enqueue_script('w3-jquery-ui',W3SPEEDSTER_URL.'assets/js/jquery-ui.js');
		wp_enqueue_script('w3-select2',W3SPEEDSTER_URL.'assets/js/select2.min.js');
		wp_enqueue_script('w3-admin-core', W3SPEEDSTER_URL.'assets/js/w3-admin-core.js','',wp_rand(100,1000));
		wp_enqueue_script('wp-theme-plugin-editor');
	}
	function w3SpeedsterCriticalCachePurgeCallback() {
		if ( !isset( $this->add_settings['wp_get']['_wpnonce'] ) || !$this->checkSecurityKey( $this->add_settings['wp_get']['_wpnonce'],'purge_critical_css') ) {
			return 'Request not valid';
		}
		
        $data_id = !empty($this->add_settings['wp_get']['data_id']) ? $this->add_settings['wp_get']['data_id'] : '';
		$data_type = !empty($this->add_settings['wp_get']['data_type']) ? $this->add_settings['wp_get']['data_type'] : '';
        if(!empty($data_id) && !empty($data_type)){
			if($data_type == 'category'){
				$url = get_term_link($data_id);
			}else{
				$url = get_permalink($data_id);
			}
			$path = $this->w3PreloadCssPath($url);
			$this->w3Rmfiles($path);
			echo esc_html(round( (int)$this->w3GetOption('w3_speedup_filesize') / 1024/1024 , 2));
		}else{
			$response =round( (int)$this->w3RemoveCriticalCssCacheFiles(),2);
			echo esc_html($response);
		}
		// @codingStandardsIgnoreLine
		w3UpdateOption('w3-critical-deleted',array_merge($_REQUEST,array('user_id'=>get_current_user_id(),'timestamp'=>date('Y-m-d h:i:sa'))));
        exit;
    }
     
	
	function w3ModifyHtaccess(){
		$path = $this->add_settings['document_root'].'/';
		if(!file_exists($path.".htaccess")){
			if(isset($_SERVER["SERVER_SOFTWARE"]) && $_SERVER["SERVER_SOFTWARE"] && (preg_match("/iis/i", $_SERVER["SERVER_SOFTWARE"]) || preg_match("/nginx/i", $_SERVER["SERVER_SOFTWARE"]))){
				//
			}else{
				return array("<label>.htaccess was not found</label>", "w3speedster");
			}
		}
		/*if(!WP_CACHE){
			if($wp_config = $this->w3speedsterGetContents(ABSPATH."wp-config.php")){
				$wp_config = str_replace("\$table_prefix", "define('WP_CACHE', true);\n\$table_prefix", $wp_config);

				if(!$this->w3speedsterPutContents(ABSPATH."wp-config.php", $wp_config)){
					return array("define('WP_CACHE', true); is needed to be added into wp-config.php", "w3speedster");
				}
			}else{
				return array("define('WP_CACHE', true); is needed to be added into wp-config.php", "w3speedster");
			}
		}*/
		$htaccess = $this->w3speedsterGetContents($path.".htaccess");

		// if(defined('DONOTCACHEPAGE')){
		// 	return array("DONOTCACHEPAGE <label>constant is defined as TRUE. It must be FALSE</label>", "error");
		// }else 
		

		if(!get_option('permalink_structure')){
			return array("You have to set <strong><u><a href='".admin_url()."options-permalink.php"."'>permalinks</a></u></strong>", "w3speedster");
			
		}
		// @codingStandardsIgnoreLine
		else if(is_writable($path.".htaccess")){
			$change_in_htaccess = 0;
			if(!empty($this->settings['lbc'])){
				if(strpos($htaccess,'# BEGIN W3LBC') === false || strpos($htaccess,'# END W3LBC') === false){
					$htaccess = $this->w3InsertLbcRule($htaccess)."\n";
					$change_in_htaccess = 1;
				}
			}elseif(strpos($htaccess,'# BEGIN W3LBC') !== false || strpos($htaccess,'# END W3LBC') !== false){
				$htaccess = preg_replace("/#\s?BEGIN\s?W3LBC.*?#\s?END\s?W3LBC/s", "", $htaccess);
				$change_in_htaccess = 1;
			}
			if(!empty($this->settings['gzip'])){
				if(strpos($htaccess,'# BEGIN W3Gzip') === false || strpos($htaccess,'# END W3Gzip') === false){
					$htaccess = $this->w3InsertGzipRule($htaccess);
					$change_in_htaccess = 1;
				}
			}elseif(strpos($htaccess,'# BEGIN W3Gzip') !== false || strpos($htaccess,'# END W3Gzip') !== false){
				$htaccess = preg_replace("/\s*\#\s?BEGIN\s?W3Gzip.*?#\s?END\s?W3Gzip\s*/s", "", $htaccess);
				$change_in_htaccess = 1;
			}
			$webp_disable_htaccess = function_exists('w3_disable_htaccess_wepb') ? w3_disable_htaccess_wepb() : 0;
			
			if(!empty($this->settings['hook_disable_htaccess_webp'] )){
				$disable_htaccess_webp = isset($this->add_settings['disable_htaccess_webp']) ? $this->add_settings['disable_htaccess_webp']: 0;
				$code = str_replace(array('$disable_htaccess_webp'),array('$args[0]'),$this->settings['hook_disable_htaccess_webp']);
				$this->add_settings['disable_htaccess_webp'] = $this->hookCallbackFunction($code,$disable_htaccess_webp);
			}
			if(empty($webp_disable_htaccess) && $this->add_settings['image_home_url'] == $this->add_settings['site_url']){
				if(!empty($this->settings['webp_png']) || !empty($this->settings['webp_jpg'])){
					if(strpos($htaccess,'# BEGIN W3WEBP') === false || strpos($htaccess,'# END W3WEBP') === false){
						$htaccess = $this->w3InsertWebp($htaccess)."\n";
						$change_in_htaccess = 1;
					}
				}elseif(strpos($htaccess,'# BEGIN W3WEBP') !== false || strpos($htaccess,'# END W3WEBP') !== false){
					$htaccess = preg_replace("/#\s?BEGIN\s?W3WEBP.*?#\s?END\s?W3WEBP/s", "", $htaccess);
					$change_in_htaccess = 1;
				}
			}elseif(strpos($htaccess,'# BEGIN W3WEBP') !== false || strpos($htaccess,'# END W3WEBP') !== false){
				$htaccess = preg_replace("/#\s?BEGIN\s?W3WEBP.*?#\s?END\s?W3WEBP/s", "", $htaccess);
				$change_in_htaccess = 1;
			}
			if(strpos($htaccess,'# BEGIN W3404') === false || strpos($htaccess,'# END W3404') === false){
				$htaccess = $this->w3Insert_404RedirectToFile($htaccess);
				$change_in_htaccess = 1;
			}
			if($change_in_htaccess){
				$this->w3speedsterPutContents($path.".htaccess", $htaccess);
			}
		}else{
			return array($this->translate_("Options have been saved"), "updated");
		}
		return array($this->translate_("Options have been saved"), "updated");

	}
	function w3Insert_404RedirectToFile($htaccess){
		$data = "\n"."# BEGIN W3404"."\n".
				"<IfModule mod_rewrite.c>"."\n".
				"RewriteEngine On"."\n".
				"RewriteBase /"."\n".
				"RewriteCond %{REQUEST_FILENAME} !-f"."\n".
				"RewriteRule (.*)/w3-cache/(css|js)/(\d)*(.*)[mob]*\.(css|js) $4.$5 [L]"."\n".
				"</IfModule>"."\n";
		$data = $data."# END W3404"."\n";
		$htaccess = preg_replace("/\s*\#\s?BEGIN\s?W3404.*?#\s?END\s?W3404\s*/s", "", $htaccess);
		return $data.$htaccess;
	}
	function w3InsertRewriteRule($htaccess){
		if(!empty($this->settings['html_cache'])){
			$htaccess = preg_replace("/#\s?BEGIN\s?W3Cache.*?#\s?END\s?W3Cache/s", "", $htaccess);
			$htaccess = $this->w3GetHtaccess().$htaccess;
		}else{
			$htaccess = preg_replace("/#\s?BEGIN\s?W3Cache.*?#\s?END\s?W3Cache/s", "", $htaccess);
			$this->deleteCache();
		}

		return $htaccess;
	}
	function w3InsertGzipRule($htaccess){
		$data = "\n"."# BEGIN W3Gzip"."\n".
				"<IfModule mod_deflate.c>"."\n".
				"AddType x-font/woff .woff"."\n".
				"AddType x-font/ttf .ttf"."\n".
				"AddOutputFilterByType DEFLATE image/svg+xml"."\n".
				"AddOutputFilterByType DEFLATE text/plain"."\n".
				"AddOutputFilterByType DEFLATE text/html"."\n".
				"AddOutputFilterByType DEFLATE text/xml"."\n".
				"AddOutputFilterByType DEFLATE text/css"."\n".
				"AddOutputFilterByType DEFLATE text/javascript"."\n".
				"AddOutputFilterByType DEFLATE application/xml"."\n".
				"AddOutputFilterByType DEFLATE application/xhtml+xml"."\n".
				"AddOutputFilterByType DEFLATE application/rss+xml"."\n".
				"AddOutputFilterByType DEFLATE application/javascript"."\n".
				"AddOutputFilterByType DEFLATE application/x-javascript"."\n".
				"AddOutputFilterByType DEFLATE application/x-font-ttf"."\n".
				"AddOutputFilterByType DEFLATE x-font/ttf"."\n".
				"AddOutputFilterByType DEFLATE application/vnd.ms-fontobject"."\n".
				"AddOutputFilterByType DEFLATE font/opentype font/ttf font/eot font/otf"."\n".
				"</IfModule>"."\n";

		$data = $data."# END W3Gzip"."\n";

		$htaccess = preg_replace("/\s*\#\s?BEGIN\s?W3Gzip.*?#\s?END\s?W3Gzip\s*/s", "", $htaccess);
		return $data.$htaccess;
	}
	function w3InsertLbcRule($htaccess){
		$data = "\n"."# BEGIN W3LBC"."\n".
			'<FilesMatch "\.(webm|ogg|mp4|ico|pdf|flv|jpg|jpeg|png|gif|webp|js|css|swf|x-html|css|xml|js|woff|woff2|otf|ttf|svg|eot)(\.gz)?$">'."\n".
			'<IfModule mod_expires.c>'."\n".
			'AddType application/font-woff2 .woff2'."\n".
			'AddType application/x-font-opentype .otf'."\n".
			'ExpiresActive On'."\n".
			'ExpiresDefault A0'."\n".
			'ExpiresByType video/webm A10368000'."\n".
			'ExpiresByType video/ogg A10368000'."\n".
			'ExpiresByType video/mp4 A10368000'."\n".
			'ExpiresByType image/webp A10368000'."\n".
			'ExpiresByType image/gif A10368000'."\n".
			'ExpiresByType image/png A10368000'."\n".
			'ExpiresByType image/jpg A10368000'."\n".
			'ExpiresByType image/jpeg A10368000'."\n".
			'ExpiresByType image/ico A10368000'."\n".
			'ExpiresByType image/svg+xml A10368000'."\n".
			'ExpiresByType text/css A10368000'."\n".
			'ExpiresByType text/javascript A10368000'."\n".
			'ExpiresByType application/javascript A10368000'."\n".
			'ExpiresByType application/x-javascript A10368000'."\n".
			'ExpiresByType application/font-woff2 A10368000'."\n".
			'ExpiresByType application/x-font-opentype A10368000'."\n".
			'ExpiresByType application/x-font-truetype A10368000'."\n".
			'</IfModule>'."\n".
			'<IfModule mod_headers.c>'."\n".
			'Header set Expires "max-age=A10368000, public"'."\n".
			'Header unset ETag'."\n".
			'Header set Connection keep-alive'."\n".
			'FileETag None'."\n".
			'</IfModule>'."\n".
			'</FilesMatch>'."\n".
			"# END W3LBC"."\n";

		$htaccess = preg_replace("/#\s?BEGIN\s?W3LBC.*?#\s?END\s?W3LBC/s", "", $htaccess);
		$htaccess = $data.$htaccess;
		return $htaccess;
	}
	function w3InsertWebp($htaccess){
		$wp_content_arr = explode('/',trim($this->add_settings['content_path'],'/'));
		$wp_content = array_pop($wp_content_arr);
		$wp_content_webp = $wp_content."/w3-webp/";
		$basename = $wp_content_webp."$1w3.webp";
		if(preg_match("/https?\:\/\/[^\/]+\/(.+)/", site_url(), $siteurl_base_name)){
			if(preg_match("/https?\:\/\/[^\/]+\/(.+)/", home_url(), $homeurl_base_name)){
				$homeurl_base_name[1] = trim($homeurl_base_name[1], "/");
				$siteurl_base_name[1] = trim($siteurl_base_name[1], "/");

				if($homeurl_base_name[1] == $siteurl_base_name[1]){
					if(preg_match("/".preg_quote($homeurl_base_name[1], "/")."$/", trim(ABSPATH, "/"))){
						$basename = $homeurl_base_name[1]."/".$basename;
					}
				}
			}else{
				$siteurl_base_name[1] = trim($siteurl_base_name[1], "/");
				$basename = $siteurl_base_name[1]."/".$basename;
			}
		}

		if(ABSPATH == "//"){
			$RewriteCond = "RewriteCond %{DOCUMENT_ROOT}/".$basename." -f"."\n";
		}else{
			$tmp_ABSPATH = str_replace(" ", "\ ", ABSPATH);

			$RewriteCond = "RewriteCond %{DOCUMENT_ROOT}/".$basename." -f [or]"."\n";
			$RewriteCond = $RewriteCond."RewriteCond ".$tmp_ABSPATH.$wp_content_webp."$1w3.webp -f"."\n";
		}
		
		$data = "\n"."# BEGIN W3WEBP"."\n".
				"<IfModule mod_rewrite.c>"."\n".
				"RewriteEngine On"."\n".
				"RewriteCond %{HTTP_ACCEPT} image/webp"."\n".
				"RewriteCond %{REQUEST_URI} \.(jpe?g|png)"."\n".
				$RewriteCond.
				"RewriteRule ^".$wp_content."/(.*) /".$basename." [L]"."\n".
				"</IfModule>"."\n".
				"<IfModule mod_headers.c>"."\n".
				"Header append Vary Accept env=REDIRECT_accept"."\n".
				"</IfModule>"."\n".
				"AddType image/webp .webp"."\n".
				"# END W3WEBP"."\n";
		$htaccess = preg_replace("/#\s?BEGIN\s?W3WEBP.*?#\s?END\s?W3WEBP/s", "", $htaccess);
		$htaccess = $data.$htaccess;
		return $htaccess;
	}
	function addButtonToEditMediaModalFieldsArea1( $form_fields, $post ) {
		//print_r($form_fields);	
		
			$image_url = wp_get_attachment_url($post->ID );
			
			$theme_root_array = explode('/',$this->add_settings['theme_base_url']);
			$theme_root = array_pop($theme_root_array);
			$upload_dir = wp_upload_dir();
			$webp_jpg = !empty($this->settings['webp_jpg']) ? 1 : 0;
			$webp_png = !empty($this->settings['webp_png']) ? 1 : 0;
			$optimize_image = !empty($this->settings['opt_jpg_png']) ? 1 : 0;
			$type = explode('.',$image_url);
			$type = array_reverse($type);
			if(strpos($image_url,$theme_root) !== false){
				$img_root_path = rtrim($this->add_settings['theme_base_dir'],'/');
				$img_root_url = rtrim($this->add_settings['theme_base_url'],'/');
			}else{
				$img_root_path = $this->add_settings['upload_base_dir'];
				$img_root_url = $this->add_settings['upload_base_url'];
				
			}
			$image_url_path = str_replace($img_root_url,$img_root_path,$image_url); 
			$webp_path = str_replace($this->add_settings['upload_path'],$this->add_settings['webp_path'],$image_url_path);
			
			$optimize_message = '';
			if(is_file($webp_path.'w3.webp')){
				$optimize_message = 1;
			}
			
		
		
		$form_fields['optimize_image'] = array(
			'label'         =>'',
			'input'         => 'html',
			'html'          => '<div class="loader-sec"><div class="loader"></div></div><a href="#" data-id="' . $post->ID  . '" class="optimize_media_image button-secondary button-large" title="' . $this->translate_( 'Optimize image' ) . '">' . $this->translate_( 'Optimize Image' ) . '<i class="dashicons dashicons-saved" style="vertical-align: sub;"></i></a>',
			'show_in_modal' => true,
			'show_in_edit'  => false,
		);

		return $form_fields;
	}
	function fnW3OptimizeMediaImageCallback (){
		if(isset($_POST['id']) && !empty($_POST['id'])){
			
			$attach_id = $_POST['id'];	
			require_once(W3SPEEDSTER_DIR . 'includes/image-optimize.php');
			$w3speedster_image = new w3speedster_optimize_image();
			$result = $w3speedster_image->w3OptimizeAttachmentId($attach_id);
		}
		
		
		echo $this->w3JsonEncode(array(
                'summary' => $result,
                'status' => '200'
            ));
		exit;
	}
	function w3SpeedsterRemoveHtmlCacheCode(){
			$htaccessPath = $this->add_settings['document_root'] . "/.htaccess";
			$htaccessContent = $this->w3speedsterGetContents($htaccessPath);
			
			// @codingStandardsIgnoreLine
			if(is_file($this->add_settings['document_root'] . "/.htaccess") && is_writable($this->add_settings['document_root'] . "/.htaccess") && strpos($htaccessContent,'# BEGIN W3HTMLCACHE') !== false && strpos($htaccessContent,'# END W3HTMLCACHE') !== false){
				$htaccess = preg_replace("/#\s?BEGIN\s?W3HTMLCACHE.*?#\s?END\s?W3HTMLCACHE/s", "", $htaccessContent);
				$this->w3speedsterPutContents($htaccessPath, $htaccess);
			}
	}
	function w3GetOption($option){
		return w3GetOption($option);
	}
	function w3UpdateOption($option){
		return w3GetOption($option);
	}
}