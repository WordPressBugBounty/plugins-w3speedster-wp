<?php
namespace W3speedster;

if (!defined('ABSPATH')) {
    exit;
}

class w3speedster
{
    var $add_settings;
    var $settings;
    var $html = "";
    public function __construct()
    {
        if (!empty($this->add_settings['wp_get']['delete-wnw-cache'])) {
            add_action('admin_init', array($this, 'w3RemoveCacheFilesHourlyEventCallback'));
            add_action('admin_init', array($this, 'w3RemoveCacheRedirect'));
        }
        if (!empty($_POST['w3speedster-use-recommended-settings'])) {

            $arr = (array) json_decode('{"license_key":"","w3_api_url":"","is_activated":"","optimization_on":"on","cdn":"","exclude_cdn":"","lbc":"on","gzip":"on","remquery":"on","lazy_load":"on","lazy_load_iframe":"on","lazy_load_video":"on","lazy_load_px":"200","webp_jpg":"on","webp_png":"on","webp_quality":"90","img_quality":"90","exclude_lazy_load":"base64\r\nlogo\r\nrev-slidebg\r\nno-lazy\r\nfacebook\r\ngoogletagmanager","exclude_pages_from_optimization":"wp-login.php\r\n\/cart\/\r\n\/checkout\/","cache_path":"","css":"on","load_critical_css":"on","exclude_css":"","force_lazyload_css":"","load_combined_css":"after_page_load","internal_css_delay_load":"10","google_fonts_delay_load":".2","exclude_page_from_load_combined_css":"","custom_css":"","js":"on","exclude_javascript":"","custom_javascript":"","exclude_inner_javascript":"google-analytics\r\nhbspt\r\n\/* <![CDATA[ *\/","force_lazy_load_inner_javascript":"googletagmanager\r\nconnect.facebook.net\r\nstatic.hotjar.com\r\njs.driftt.com","load_combined_js":"on_page_load","internal_js_delay_load":"10","exclude_page_from_load_combined_js":"","custom_js":""}');
            w3UpdateOption('w3_speedup_option', $arr);
        }
        $this->settings = w3GetOption('w3_speedup_option', true);


        if ($this->settings == 1) {
            add_action('admin_notices', array($this, 'w3RecommendedSettings'));
        }

        $this->settings = !empty($this->settings) && is_array($this->settings) ? $this->settings : array();
        $this->add_settings = array();
        $this->add_settings['wp_get'] = $_GET;
        $this->add_settings['wp_home_url'] = rtrim(home_url(), '/');
        $site_url = explode('/', rtrim(content_url(), '/'));
        array_pop($site_url);
        $this->add_settings['wp_site_url'] = implode('/', $site_url);
        if (strpos($this->add_settings['wp_home_url'], '?') !== false) {
            $home_url_arr = explode('?', $this->add_settings['wp_home_url']);
            $this->add_settings['wp_home_url'] = $home_url_arr[0];
        }
        $this->add_settings['site_url_arr'] = wp_parse_url($this->add_settings['wp_site_url']);
        $this->add_settings['secure'] = (isset($this->add_settings['wp_home_url']) && strpos($this->add_settings['wp_home_url'], 'https') !== false) ? 'https://' : 'http://';
        $this->add_settings['home_url'] = !empty($_SERVER['HTTP_HOST']) ? $this->add_settings['secure'] . $_SERVER['HTTP_HOST'] : $this->add_settings['wp_home_url'];
        $home_url_arr = wp_parse_url($this->add_settings['home_url']);
		$this->settings['main_license_key'] = $this->settings['license_key'] ? $this->settings['license_key'] : 'w3demo-' . $home_url_arr['host'];
        $this->add_settings['image_home_url'] = !empty($this->settings['cdn']) ? rtrim($this->settings['cdn'], '/') : $this->add_settings['wp_site_url'];
        $this->add_settings['enable_cdn'] = $this->add_settings['wp_site_url'] != $this->add_settings['image_home_url'] ? 1 : 0;
        $this->add_settings['w3_api_url'] = !empty($this->settings['w3_api_url']) ? $this->settings['w3_api_url'] : 'https://cloud.w3speedster.com/optimize/';
        //$sitename = 'home';
        $this->add_settings['wp_content_path'] = WP_CONTENT_DIR;
        $wp_content_arr = explode('/', $this->add_settings['wp_content_path']);
        array_pop($wp_content_arr);
        $this->add_settings['wp_document_root'] = rtrim(implode('/', $wp_content_arr), '/');
        $this->add_settings['document_root'] = $_SERVER['DOCUMENT_ROOT'];
        $this->add_settings['full_url'] = !empty($_SERVER['HTTP_HOST']) ? $this->add_settings['secure'] . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] : $this->add_settings['home_url'] . $_SERVER['REQUEST_URI'];

        $full_url_array = explode('?', $this->add_settings['full_url']);
        $this->add_settings['full_url_without_param'] = $full_url_array[0];
        $this->add_settings['wp_cache_path'] = (!empty($this->settings['cache_path']) ? $this->settings['cache_path'] : $this->add_settings['wp_content_path'] . '/cache');
        $this->add_settings['root_cache_path'] = $this->add_settings['wp_cache_path'] . '/w3-cache';
        $this->add_settings['critical_css_path'] = ($this->settings['cache_path'] ? $this->settings['cache_path'] : $this->add_settings['wp_content_path']) . '/critical-css';
        $this->add_settings['cache_path'] = str_replace($this->add_settings['wp_document_root'], '', $this->add_settings['root_cache_path']);
        $this->add_settings['cache_url'] = str_replace($this->add_settings['wp_document_root'], $this->add_settings['wp_site_url'], $this->add_settings['root_cache_path']);
        $this->add_settings['upload_path'] = str_replace($this->add_settings['wp_document_root'], '', $this->add_settings['wp_content_path']);
        $upload_dir = wp_upload_dir();
        $upload_base_url = wp_parse_url($upload_dir['baseurl']);
        $this->add_settings['upload_base_url'] = strpos($upload_dir['baseurl'], $this->add_settings['wp_site_url']) !== false ? $upload_dir['baseurl'] : $this->add_settings['wp_site_url'] . $upload_base_url['path'];
        $this->add_settings['upload_base_dir'] = $upload_dir['basedir'];
        $this->add_settings['theme_base_url'] = function_exists('get_theme_root_uri') ? get_theme_root_uri() : '';
		$theme_root_array = explode('/',$this->add_settings['theme_base_url']);
		$this->add_settings['theme_root'] = array_pop($theme_root_array);
        $this->add_settings['theme_base_dir'] = function_exists('get_theme_root') ? get_theme_root() . '/' : '';
		$this->add_settings['webp_path'] = $this->add_settings['upload_path'] . '/w3-webp';
        $useragent = @$_SERVER['HTTP_USER_AGENT'];
        $this->add_settings['is_mobile'] = function_exists('wp_is_mobile') ? wp_is_mobile() : 0;
        $this->add_settings['load_ext_js_before_internal_js'] = !empty($this->settings['load_external_before_internal']) ? explode("\r\n", $this->settings['load_external_before_internal']) : array();
        $this->add_settings['load_js_for_mobile_only'] = !empty($this->settings['load_js_for_mobile_only']) ? $this->settings['load_js_for_mobile_only'] : '';
        $this->add_settings['w3_rand_key'] = w3GetOption('w3_rand_key');
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
        $this->add_settings['exclude_cdn'] = !empty($this->settings['exclude_cdn']) ? explode(',', str_replace(' ', '', $this->settings['exclude_cdn'])) : '';
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

        if (file_exists($this->add_settings['wp_document_root'] . "/.htaccess")) {
            $htaccess = $this->w3speedsterGetContents($this->add_settings['wp_document_root'] . "/.htaccess");
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
		$this->add_settings['wptouch'] = false;
        $exclude_cdn_arr = !empty($this->add_settings['exclude_cdn']) ? $this->add_settings['exclude_cdn'] : array();
		$this->add_settings['blank_image_url'] = (($this->add_settings['enable_cdn'] && !in_array('.png',$exclude_cdn_arr)) ? str_replace($this->add_settings['wp_site_url'],$this->add_settings['image_home_url'],$this->add_settings['upload_base_url']) : $this->add_settings['upload_base_url']).'/blank.png';
        if (is_admin() && !function_exists('w3_prevent_htaccess_generation') && $preventHtaccess == 0) {
            if (!file_exists($this->add_settings['wp_document_root'] . $this->add_settings['webp_path'] . '/.htaccess')) {
                $this->w3CreateFile($this->add_settings['wp_document_root'] . $this->add_settings['webp_path'] . '/.htaccess', '<IfModule mod_cgid.c>' . "\n" . 'Options -Indexes' . "\n" . '</IfModule>');
            }
            if (!file_exists($this->add_settings['root_cache_path'] . '/.htaccess')) {
                $this->w3CreateFile($this->add_settings['root_cache_path'] . '/.htaccess', '<IfModule mod_cgid.c>' . "\n" . 'Options -Indexes' . "\n" . '</IfModule>' . "\n" . '<IfModule mod_rewrite.c>' . "\n" . 'RewriteEngine On' . "\n" . 'RewriteCond %{REQUEST_FILENAME} !-f' . "\n" . 'RewriteRule ^(.*)$ '.str_replace($this->add_settings['wp_document_root'],'',W3SPEEDSTER_PLUGIN_DIR).'/check.php?path=%{REQUEST_URI}&url=%{HTTP_REFERER}'.' [L]' . "\n" . '</IfModule>');
				
            }
            if (!file_exists($this->add_settings['critical_css_path'] . '/.htaccess')) {
                $this->w3CreateFile($this->add_settings['critical_css_path'] . '/.htaccess', '<IfModule mod_cgid.c>' . "\n" . 'Options -Indexes' . "\n" . '</IfModule>');
            }
        }
    }
    public function w3CheckEnableCdnPath($url)
    {
        $enable_cdn = 1;
        if (!empty($this->add_settings['exclude_cdn_path'])) {
            foreach ($this->add_settings['exclude_cdn_path'] as $path) {
                if (strpos($url, $path) !== false) {
                    $enable_cdn = 0;
                    break;
                }
            }
        }
        return $enable_cdn;
    }
    public function w3CheckEnableCdnExt($ext)
    {
        $enable_cdn = 0;
        if (empty($this->add_settings['exclude_cdn']) || !in_array($ext, $this->add_settings['exclude_cdn'])) {
            $enable_cdn = 1;
        }
        return $enable_cdn;
    }
    function w3SaveIndividualSetting($key, $value)
    {
        $settings = w3GetOption('w3_speedup_option', true);
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
    private function isSpecialContentType()
    {
        if ($this->w3Endswith($this->add_settings['full_url'], '.xml') || $this->w3Endswith($this->add_settings['full_url'], '.xsl')) {
            return true;
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
            esc_html__('W3speedster Setup', 'w3speedster')
        );
        echo '<p><form method="post">';
        submit_button(
            __('Use Recommended Settings', 'w3speedster'),
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
    function w3DebugTime($process)
    {
        if (!empty($this->add_settings['wp_get']['w3_debug'])) {
            $starttime = !empty($this->add_settings['starttime']) ? $this->add_settings['starttime'] : $this->microtime_float();
            $endtime = $this->microtime_float();
            $this->html .= $process . '-' . ($endtime - $starttime)/*.'-ram-'.(memory_get_usage()/1024/1024).'-cpu-'.wp_json_encode(sys_getloadavg())*/ . "\n";
        }
    }
    function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float) $sec);
    }
    function w3StrReplaceLast($search, $replace, $str)
    {
        if (($pos = strrpos($str, $search)) !== false) {
            $search_length = strlen($search);
            $str = substr_replace($str, $replace, $pos, $search_length);
        }
        return $str;
    }
    function w3speedsterActivateLicenseKey()
    {
        echo wp_kses_post($this->w3speedsterValidateLicenseKey());
        exit;
    }
    function w3speedsterValidateLicenseKey($key = '')
    {
        $key = !empty($this->add_settings['wp_get']['key']) ? $this->add_settings['wp_get']['key'] : $key;
        if (!empty($key)) {
            $options = array(
                'method' => 'GET',
                'timeout' => 10,
                'redirection' => 5,
                'sslverify' => false,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => array(),
                'body' => array(
                    'license_id' => $key,
                    'domain' => base64_encode($this->add_settings['wp_home_url'])
                )
            );
            $response = wp_remote_post($this->add_settings['w3_api_url'] . 'get_license_detail.php', $options);
            if (!is_wp_error($response) && !empty($response["body"])) {
                $res_arr = json_decode($response["body"]);
                if ($res_arr[0] == 'success') {
                    return wp_json_encode(array('success', 'verified', $res_arr[1]));
                } else {
                    return wp_json_encode(array('fail', 'could not verify-1' . $response["body"]));
                }
            } else {
                if ($this->add_settings['w3_api_url'] != 'https://cloud1.w3speedster.com/optimize/') {
                    $this->w3SaveIndividualSetting('w3_api_url', 'https://cloud1.w3speedster.com/optimize/');
                    $this->add_settings['w3_api_url'] = 'https://cloud1.w3speedster.com/optimize/';
                    $this->w3speedsterValidateLicenseKey($key);
                } else {
                    return wp_json_encode(array('fail', 'could not verify-2'));
                }
            }
        } else {
            return wp_json_encode(array('fail', 'could not verify-3'));
        }
    }
    function w3ParseUrl($src)
    {
        if (!empty($this->add_settings['site_url_arr']['path'])) {
            if (strpos($src, $this->add_settings['site_url_arr']['host']) !== false) {
                $src = str_replace($this->add_settings['site_url_arr']['host'] . $this->add_settings['site_url_arr']['path'], $this->add_settings['site_url_arr']['host'], $src);
            } else {
                $src = str_replace($this->add_settings['site_url_arr']['path'], '', $src);
            }
        }

        if (substr_count($src, '//') > 0) {
            $src = substr($src, 0, 7) . str_replace('//', '/', substr($src, 7));
        }
        $src_arr = wp_parse_url($src);
        return $src_arr;
    }
    function getHomePath()
    {
        $home = set_url_scheme(get_option('home'), 'http');
        $siteurl = set_url_scheme(get_option('siteurl'), 'http');
        if (!empty($home) && 0 !== strcasecmp($home, $siteurl)) {
            $wp_path_rel_to_home = str_ireplace($home, '', $siteurl); /* $siteurl - $home */
            $pos = strripos(str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']), trailingslashit($wp_path_rel_to_home));
            $home_path = substr($_SERVER['SCRIPT_FILENAME'], 0, $pos);
            $home_path = trailingslashit($home_path);
        } else {
            $home_path = ABSPATH;
        }

        return str_replace('\\', '/', $home_path);
    }
    function w3IsExternal($url)
    {
        $components = wp_parse_url($url);
        return !empty($components['host']) && strcasecmp($components['host'], $_SERVER['HTTP_HOST']);
    }

    function w3Endswith($string, $test)
    {
        $str_arr = explode('?', $string);
        $string = $str_arr[0];
        $ext = '.' . pathinfo($str_arr[0], PATHINFO_EXTENSION);
        if ($ext == $test)
            return true;
        else
            return false;
    }

    function w3Echo($text)
    {
        if (!empty($this->add_settings['wp_get']['w3_preload_css'])) {
            echo esc_html($text);
        }
    }
    function w3PrintR($text)
    {
        if (!empty($this->add_settings['wp_get']['w3_preload_css'])) {
            print_r($text);
        }
    }
    function w3GeneratePreloadCss()
    {
        if (empty($this->settings['optimization_on'])) {
            return;
        }
        if (!empty($this->add_settings['wp_get']['url'])) {
            $key_url = $this->add_settings['wp_get']['url'];
        }
        $preload_css_new = $preload_css = w3GetOption('w3speedup_preload_css');
        if (!empty($preload_css)) {
            foreach ($preload_css as $key1 => $url) {
                if (strpos($key1, home_url()) !== false) {
                    unset($preload_css_new[$key1]);
                    w3UpdateOption('w3speedup_preload_css_total', (int) w3GetOption('w3speedup_preload_css_total') - 1, 'no');
                    continue;
                }
                $key = base64_decode($key1);
                if (!empty($key_url) && !empty($preload_css[base64_encode($key_url)])) {
                    $key = $key_url;
                    $url = $preload_css[base64_encode($key_url)];
                    $key_url = '';
                }
                $this->w3Echo('rocket1' . $key . $url[0] . $url[1]);
                if (empty($url[2])) {
                    $this->w3Echo('rocket2-deleted');
                    unset($preload_css_new[$key1]);
                    w3UpdateOption('w3speedup_preload_css_total', (int) w3GetOption('w3speedup_preload_css_total') - 1, 'no');
                    continue;
                }
                $running_url = str_replace($this->add_settings['wp_document_root'],' ',$url[2]);
				$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
				w3UpdateOption('w3speedup_critical_running_url',urldecode($key));
                $response = $this->w3CreatePreloadCss($key, $url[0], $url[2]);

                if (!empty($response) && $response === "exists") {
                    unset($preload_css_new[$key1]);
                    w3UpdateOption('w3speedup_preload_css_total', (int) w3GetOption('w3speedup_preload_css_total') - 1, 'no');
                    continue;
                }
                if (!empty($response) && $response === "hold") {
                    $this->w3Echo('rocket5' . $response);
                    break;
                }
                if ($response || $preload_css[$key1][1] == 1) {
                    $this->w3Echo('rocket4' . $response);
                    if($response){
						w3UpdateOption('w3speedup_preload_css_created', (int) w3GetOption('w3speedup_preload_css_created') + 1, 'no');
					}else{
						w3UpdateOption('w3speedup_preload_css_total', (int) w3GetOption('w3speedup_preload_css_total') - 1, 'no');
					}
                    unset($preload_css_new[$key1]);
                } else {
                    $this->w3Echo('rocket6');
                    $preload_css_new[$key1][1] = 1;
                }
                break;
            }
            w3UpdateOption('w3speedup_preload_css', $preload_css_new, 'no');
			return $response;
        }elseif(!empty($_REQUEST['page']) && $_REQUEST['page'] == 'admin' && empty(w3GetOption('w3speedup_preload_css_total'))){
			$options = array(
            'method' => 'GET',
            'timeout' => 10,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'sslverify' => false,
            'headers' => array(),
            'body' => array(
                'url' => home_url()
            )
        );
			wp_remote_post($this->add_settings['w3_api_url'] . '/css/browse.php',$options);
			$this->w3GeneratePreloadCss();
		}
    }

    function w3GetHtmlCachePath($url)
    {
		$path = $this->add_settings['wp_content_path'] . '/cache/html/' . trim(str_replace($this->add_settings['wp_site_url'], '', $url), '/') . '/index.html';
		if (file_exists($path)) {
			return $path;
		}
		return false;
    }

    function w3DeleteHtmlCacheAfterPreloadCss($url)
    {
        if ($path = $this->w3GetHtmlCachePath($url)) {
            wp_delete_file($path);
        }

    }
    function w3CssCompressInit($minify)
    {
        $minify = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $minify);
        $minify = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), ' ', $minify);
        return $minify;
    }
    function w3CreatePreloadCss($url, $filename, $css_path)
    {
        if (!empty($this->add_settings['wp_get']['key']) && $this->settings['main_license_key'] == $this->add_settings['wp_get']['key']) {
            $this->w3RemoveCriticalCssCacheFiles();
        }
        $this->w3Echo('rocket2' . $filename . $url); 
        $this->w3Echo('rocket3' . $css_path);
        if (file_exists($css_path . '/' . $filename)) {
            $this->w3Echo('rocket9');
            return 'exists';
        }
        $nonce = wp_create_nonce("purge_critical_css");
        w3UpdateOption('purge_critical_css', $nonce);
        if ($this->add_settings['enable_cdn']) {
            $css_urls = $this->add_settings['home_url'] . ',' . $this->add_settings['image_home_url'];
        } else {
            $css_urls = $this->add_settings['home_url'];
        }

        $url_html = '';
        $options = array(
            'method' => 'POST',
            'timeout' => 30,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'sslverify' => false,
            'headers' => array(),
            'body' => array(
                'url' => $url,
                'key' => $this->settings['main_license_key'],
                '_wpnonce' => $nonce,
                'filename' => $filename,
                'css_url' => $css_urls,
                'path' => $css_path,
				'auto' => (int) w3GetOption('w3speedup_preload_css_total'),
                'html' => $url_html
            )
        );
		if(function_exists('w3ModifyCriticalCssAPIOptions')){
			$options = w3ModifyCriticalCssAPIOptions($options);
		}
        $options1 = $options;
        $response = wp_remote_post($this->add_settings['w3_api_url'] . '/css', $options);
        $options1['body']['html'] = '';
        $this->w3Echo('<pre>');
        $this->w3PrintR($options1);
        if (!is_wp_error($response)) {
            $this->w3Echo('rocket3' . $css_path . '/' . $filename);
            $this->w3Echo($response['body']);
            if (!empty($response['body'])) {
                $response_arr = (array) json_decode($response['body']);
                if (!empty($response_arr['result']) && $response_arr['result'] == 'success') {
					if(function_exists('w3speedup_customize_critical_css')){
						$response_arr['w3_css'] = w3speedup_customize_critical_css($response_arr['w3_css']);
					}
					if(!empty($this->settings['hook_customize_critical_css'])){
						$code = str_replace(array('$critical_css'),array('$args[0]'),$this->settings['hook_customize_critical_css']);
						$response_arr['w3_css'] = $this->hookCallbackFunction($code,$response_arr['w3_css']);
					}
                    $this->w3CreateFile($css_path . '/' . $filename, $response_arr['w3_css']);
                    $preload_css = w3GetOption('w3speedup_preload_css');
                    unset($preload_css[base64_encode($response_arr['url'])]);
                    //w3UpdateOption('w3speedup_preload_css', $preload_css, 'no');
                    //w3UpdateOption('w3speedup_preload_css_created', (int) w3GetOption('w3speedup_preload_css_created') + 1, 'no');
                    if (file_exists($file = $this->w3GetFullUrlCachePath($url) . '/main_css.json')) {
                        wp_delete_file($file);
                    }
                    $this->w3DeleteHtmlCacheAfterPreloadCss($url);
                    return true;
                } elseif (!empty($response_arr['error'])) {
                    if ($response_arr['error'] == 'process already running') {
                        return 'hold';
                    } else {
                        $this->w3Echo('rocket-error' . $response_arr['error']);
                        w3UpdateOption('w3speedup_critical_css_error', $response_arr['error'], 'no');
                        return false;
                    }
                }
                $this->w3Echo('rocket7');
                return false;
            } else {
                $this->w3Echo('rocket8');
                return false;
            }
        } else {
            echo esc_html($response->get_error_message());
            return false;
        }

    }

    function w3PreloadCssPath($url = '')
    {
			 
        $url = empty($url) ? $this->add_settings['full_url_without_param'] : $url;
        if (!empty($this->add_settings['preload_css_url'][$url])) {
            return $this->add_settings['preload_css_url'][$url];
        }
        if (rtrim($url, '/') == rtrim($this->add_settings['wp_home_url'], '/')) {

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
                    $url = rtrim($this->add_settings['wp_home_url'], '/') . '/post/' . $post->post_type;
                }
            }
            if (is_404()) {
                $url = rtrim($this->add_settings['wp_home_url'], '/') . '/' . 'w3404';
            }
            if (is_search() || is_page('search')) {
                $url = rtrim($this->add_settings['wp_home_url'], '/') . '/' . 'w3search';
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
                    $url = rtrim($this->add_settings['wp_home_url'], '/') . '/' . 'archive/' . $catname;
                }
            }
			if (is_author()) {
                $url = rtrim($this->add_settings['wp_home_url'], '/') . '/' . 'author';
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

    function w3PutPreloadCss()
    {
        if (!isset($this->add_settings['wp_get']['_wpnonce']) || $this->add_settings['wp_get']['_wpnonce'] != w3GetOption('purge_critical_css')) {
            echo 'Request not valid';
            exit;
        }
        if (!empty($this->add_settings['wp_get']['url']) && !empty($this->add_settings['wp_get']['filename']) && !empty($this->add_settings['wp_get']['w3_css'])) {
            $url = $this->add_settings['wp_get']['url'];
            $preload_css = w3GetOption('w3speedup_preload_css');
            echo $path = !empty($preload_css[$this->add_settings['wp_get']['filename']][2]) ? esc_html($preload_css[$this->add_settings['wp_get']['filename']][2]) : esc_html($this->add_settings['wp_get']['path']);
            $this->w3CreateFile($path . '/' . $this->add_settings['wp_get']['filename'], stripslashes($this->add_settings['wp_get']['w3_css']));
            unset($preload_css[base64_encode($this->add_settings['wp_get']['url'])]);
            w3UpdateOption('w3speedup_preload_css', $preload_css, 'no');
            if (file_exists($file = $this->w3GetFullUrlCachePath($url) . '/main_css.json')) {
                wp_delete_file($file);
            }
            $this->w3DeleteHtmlCacheAfterPreloadCss($url);
            echo 'saved';
        }
        echo false;
        exit;
    }
	function w3_put_preload_css(){
		if ( !isset( $_REQUEST['_wpnonce'] ) || $_REQUEST['_wpnonce'] != w3_get_option('purge_critical_css')) {
			echo 'Request not valid'; exit;
		}
		if(!empty($_REQUEST['url']) && !empty($_REQUEST['filename']) && !empty($_REQUEST['w3_css'])){
			$url = $_REQUEST['url'];
			$preload_css = w3_get_option('w3speedup_preload_css');
			// @codingStandardsIgnoreLine
			echo $path = !empty($preload_css[$_REQUEST['filename']][2]) ? $preload_css[$_REQUEST['filename']][2] : $_REQUEST['path'];
			$this->w3_create_file($path.'/'.$_REQUEST['filename'], stripslashes($_REQUEST['w3_css']));
			unset($preload_css[base64_encode($_REQUEST['url'])]);
			w3_update_option('w3speedup_preload_css',$preload_css,'no');
			if(file_exists($file = $this->w3_get_full_url_cache_path($url).'/main_css.json')){
				wp_delete_file($file);
			}
			$this->w3_delete_html_cache_after_preload_css($url);
			echo 'saved';
		}
		echo false;
		exit;
	}
    function w3CreateFile($path, $text = '//'){
		$path_arr = explode('/', $path);
        $filename = array_pop($path_arr);
		$realpath = urldecode(implode('/', $path_arr));
		if(is_link($realpath) || strpos($realpath,'/./') !== false || strpos($realpath,'/../') !== false) {
			$realpath = realpath($realpath);
		}
		$this->w3CheckIfFolderExists($realpath);
		$realFullPath = $realpath . '/' . $filename;
        $file = $this->w3speedsterPutContents($realFullPath, $text);
        if ($file) {
            // @codingStandardsIgnoreLine
            chmod($realFullPath, 0644);
            return true;
        } else {
            return false;
        }
    }
    function w3ParseScript($tag, $link)
    {
        $data_exists = strpos($link, '>');
        if (!empty($data_exists)) {
            $end_tag_pointer = strpos($link, '</'.$tag.'>', $data_exists);
            $link_arr = substr($link, $data_exists + 1, $end_tag_pointer - $data_exists - 1);
        }
        return $link_arr;
    }
    function w3ParseLink($tag, $link)
    {
        $xmlDoc = new \DOMDocument();
        if (empty($link) || @$xmlDoc->loadHTML($link) === false) {
            return array();
        }
        $tag_html = $xmlDoc->getElementsByTagName($tag);
        $link_arr = array();
        if (!empty($tag_html[0])) {
            foreach ($tag_html[0]->attributes as $attr) {
                $link_arr[$attr->nodeName] = iconv('ISO-8859-1', 'UTF-8',$attr->nodeValue);
			}
        }
        if (strpos($link, '><') === false) {
            $link_arr['html'] = $this->w3ParseScript($tag, $link);
        }
        return $link_arr;
    }

    function w3ImplodeLinkArray($tag, $array){
        if (empty($array)) {
			return '';
		}
		$link = '<' . $tag . ' ';
        $html = '';
        if (!empty($array['html'])) {
            $html = $array['html'];
            unset($array['html']);
        }
        foreach ($array as $key => $arr) {
            if ($key != 'html') {
                $link .= $key . "=\"" . str_replace('"', "'", $arr) . "\" ";
            }
        }
		$link = trim($link);
        if ($tag == 'script') {
            $link .= '>' . $html . '</script>';
        } elseif ($tag == 'iframe') {
            $link .= '>' . $html . '</iframe>';
        } elseif ($tag == 'iframelazy') {
            $link .= '>' . $html . '</iframelazy>';
        } else {
            $link .= '>';
        }
        return $link;
    }
    function w3InsertContentHeadInJson()
    {
        global $insert_content_head;
        if ($this->add_settings['full_url'] == $this->add_settings['full_url_without_param']) {
            $file = $this->w3GetFullUrlCachePath() . '/content_head.json';
            if (!$this->add_settings['w3UserLoggedIn']) {
                $this->w3CreateFile($file, wp_json_encode($insert_content_head));
            }
        }
    }

    function w3InsertContentHead($content, $pos)
    {
        global $insert_content_head;
        $insert_content_head[] = array($content, $pos);
        if ($pos == 1) {

            $this->html = preg_replace('/<style/', $content . '<style', $this->html, 1, $count);

        } elseif ($pos == 2) {

            $this->html = preg_replace('/<link(.*)href="([^"]*)"(.*)>/', $content . '<link$1href="$2"$3>', $this->html, 1);

        } elseif ($pos == 3) {
            $this->html = preg_replace('/<head([^<]*)>/', '<head$1>' . $content, $this->html, 1, $count);
            if (empty($count)) {
                $this->html = preg_replace('/<html([^<]*)>/', '<html$1>' . $content, $this->html, 1, $count);
            }
        } elseif ($pos == 4) {
            $this->html = preg_replace('/<\/head(\s*)>/', $content . '</head$1>', $this->html, 1, $count);
            if (empty($count)) {
                $this->html = preg_replace('/<body([^<]*)>/', $content . '<body$1>', $this->html, 1, $count);
            }
        } elseif ($pos == 5) {
            $this->html = preg_replace($content, '', $this->html, 1, $count);
        } elseif ($pos == 6) {
            $this->html = $this->rightReplace($this->html, '<meta ', $content . '<meta ');
        } else {
            $this->html = preg_replace('/<script/', $content . '<script', $this->html, 1, $count);
        }
    }

    function rightReplace($string, $search, $replace)
    {
        $offset = strrpos($string, $search);
        if ($offset !== false) {
            $length = strlen($search);
            $string = substr_replace($string, $replace, $offset, $length);
        }
        return $string;
    }

    function w3MainCssUrlToJson()
    {
        global $main_css_url;
        if (empty($main_css_url)) {
            $main_css_url = array();
        }
        if ($this->add_settings['full_url'] == $this->add_settings['full_url_without_param']) {
            $file = $this->w3GetFullUrlCachePath() . '/main_css.json';
            if (!$this->add_settings['w3UserLoggedIn']) {
                $this->w3CreateFile($file, wp_json_encode($main_css_url));
            }
        }
    }
    function w3InternalJsToJson()
    {
        global $internal_js;
        if (empty($internal_js)) {
            $internal_js = array();
        }
        if ($this->add_settings['full_url'] == $this->add_settings['full_url_without_param']) {
            $file = $this->w3GetFullUrlCachePath() . '/main_js.json';
            if (!$this->add_settings['w3UserLoggedIn']) {
                $this->w3CreateFile($file, wp_json_encode($internal_js));
            }
        }
    }
    function w3StrReplaceSet($str, $rep)
    {
        global $str_replace_str_array, $str_replace_rep_array;
        $str_replace_str_array[] = $str;
        $str_replace_rep_array[] = $rep;
        //echo '<pre>'; print_r($this->str_replace_str_array);
    }

    function w3StrReplaceSetImg($str, $rep)
    {
        global $str_replace_str_img, $str_replace_rep_img;
        $str_replace_str_img[] = $str;
        $str_replace_rep_img[] = $rep;
    }
    function w3StrReplaceBulkImg()
    {
        global $str_replace_str_img, $str_replace_rep_img;
        if (!$this->add_settings['w3UserLoggedIn'] && $this->add_settings['full_url'] == $this->add_settings['full_url_without_param']) {
            $this->w3CreateFile($this->w3GetFullUrlCachePath() . '/img.json', wp_json_encode(array($str_replace_str_img, $str_replace_rep_img)));
        }
        $this->html = str_replace($str_replace_str_img, $str_replace_rep_img, $this->html);
    }

    function w3StrReplaceSetJs($str, $rep)
    {
        global $str_replace_str_js, $str_replace_rep_js;
        $str_replace_str_js[] = $str;
        $str_replace_rep_js[] = $rep;
    }
    function w3StrReplaceBulkJs($html)
    {
        global $str_replace_str_js, $str_replace_rep_js;
        if (!$this->add_settings['w3UserLoggedIn'] && $this->add_settings['full_url'] == $this->add_settings['full_url_without_param']) {
            $this->w3CreateFile($this->w3GetFullUrlCachePath() . '/js.json', wp_json_encode(array($str_replace_str_js, $str_replace_rep_js)));
        }
        $html = str_replace($str_replace_str_js, $str_replace_rep_js, $html);
        return $html;
    }

    function w3StrReplaceBulkJson($str = array(), $rep = array())
    {
        if (!empty($rep['php'])) {
            $rep['php'] = '<style>' . $this->w3speedsterGetContents($rep['php']) . '</style>';
        }
        $this->html = str_replace($str, $rep, $this->html);
    }

    function w3StrReplaceSetCss($str, $rep, $key = '')
    {
        global $str_replace_str_css, $str_replace_rep_css;
        if ($key) {
            $str_replace_str_css[$key] = $str;
            $str_replace_rep_css[$key] = $rep;
        } else {
            $str_replace_str_css[] = $str;
            $str_replace_rep_css[] = $rep;
        }
    }
    function w3StrReplaceBulkCss()
    {
        global $str_replace_str_css, $str_replace_rep_css;
        if (!$this->add_settings['w3UserLoggedIn'] && $this->add_settings['full_url'] == $this->add_settings['full_url_without_param']) {
            $this->w3CreateFile($this->w3GetFullUrlCachePath() . '/css.json', wp_json_encode(array($str_replace_str_css, $str_replace_rep_css)));
        }
        if (!empty($str_replace_rep_css['php'])) {
            $str_replace_rep_css['php'] = '<style>' . $this->w3speedsterGetContents($str_replace_rep_css['php']) . '</style>';
        }
        $this->html = str_replace($str_replace_str_css, $str_replace_rep_css, $this->html);
    }

    function w3StrReplaceBulk()
    {
        global $str_replace_str_array, $str_replace_rep_array;
        global $str_replace_str_css, $str_replace_rep_css;
        global $str_replace_str_js, $str_replace_rep_js;
        global $str_replace_str_img, $str_replace_rep_img;
        if (!is_array($str_replace_str_array) && !is_array($str_replace_rep_array)) {
            $str_replace_str_array = array();
            $str_replace_rep_array = array();
        }
        if (!is_array($str_replace_str_css) && !is_array($str_replace_rep_css)) {
            $str_replace_str_css = array();
            $str_replace_rep_css = array();
        }
        if (!is_array($str_replace_str_js) && !is_array($str_replace_rep_js)) {
            $str_replace_str_js = array();
            $str_replace_rep_js = array();
        }
        if (!is_array($str_replace_str_img) && !is_array($str_replace_rep_img)) {
            $str_replace_str_img = array();
            $str_replace_rep_img = array();
        }
        $this->html = str_replace(array_merge($str_replace_str_array, $str_replace_str_css, $str_replace_str_js, $str_replace_str_img), array_merge($str_replace_rep_array, $str_replace_rep_css, $str_replace_rep_js, $str_replace_rep_img), $this->html);
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

    function w3GetFullUrlCachePath($full_url = '')
    {
        $cache_path = $this->w3CheckFullUrlCachePath($full_url);
        $this->w3CheckIfFolderExists($cache_path);
        return $cache_path;
    }
	function w3GetFullUrlCache($full_url = '')
    {
        $cache_path = str_replace($this->add_settings['wp_document_root'],$this->add_settings['image_home_url'],$this->w3CheckFullUrlCachePath($full_url));
        return $cache_path;
    }
    function w3CheckFullUrlCachePath($full_url = '')
    {
        $full_url = !empty($full_url) ? $full_url : $this->add_settings['full_url'];
        $url_array = wp_parse_url($full_url);
        $query = !empty($url_array['query']) ? '/?' . $url_array['query'] : '';
        $full_url_arr = explode('/', trim($url_array['path'], '/') . $query);
        $cache_path = $this->w3GetCachePath('all');
		if(is_array($full_url_arr) && count($full_url_arr) > 0)
			foreach ($full_url_arr as $path) {
				$cache_path .= '/' . md5($path);
			}
        if (!empty($this->settings['separate_cache_for_mobile']) && !empty($this->add_settings['is_mobile'])) {
            $cache_path .= '/mob';
        }
        return $cache_path;
    }
    function w3CheckIfFolderExists($path)
    {
        $realpath = urldecode($path);
        if (is_dir($realpath)) {
            return $path;
        }
        try {
            wp_mkdir_p($realpath, 0755, true);
        } catch (Exception $e) {
            echo 'Message: ' . esc_html($e->getMessage());
        }
        return $path;
    }

    // function getCurlUrl($url){
    //     if(!function_exists('curl_init')){
    // 		return file_get_contents($url);
    // 	}
    // 	$curl = curl_init();

    // 	curl_setopt_array($curl, array(
    // 	  CURLOPT_URL => $url,
    // 	  CURLOPT_RETURNTRANSFER => true,
    // 	  CURLOPT_ENCODING => "",
    // 	  CURLOPT_MAXREDIRS => 10,
    // 	  CURLOPT_TIMEOUT => 0,
    // 	  CURLOPT_FOLLOWLOCATION => true,
    // 	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    // 	  CURLOPT_CUSTOMREQUEST => "GET",
    // 	));

    // 	$response = curl_exec($curl);

    // 	curl_close($curl);
    // 	return $response; 
    // }

    function getCurlUrl($url)
    {
        // Check if the wp_remote_get() function is available
        $options = array(
            'method' => 'GET',
            'timeout' => 40,
            'redirection' => 5,
            'httpversion' => '1.0',
            'sslverify' => false,
            'blocking' => true,
            'headers' => array(),
            'cookies' => array()
        );
        if (function_exists('wp_remote_get')) {
            // Use wp_remote_get() function to fetch the URL
            $response = wp_remote_get($url, $options);

            // Check if the request was successful
            if (!is_wp_error($response) && $response['response']['code'] === 200) {
                // Return the response body
                return wp_remote_retrieve_body($response);
            } else {
                // Return an empty string or handle the error as needed
                return '';
            }
        } else {
           
                return $this->w3speedsterGetContents($url);
        }
    }

    function optimizeImage($width, $url, $is_webp = false)
    {
        $key = $this->settings['main_license_key'];
        $key_activated = $this->settings['is_activated'];
        if (empty($key) || empty($key_activated)) {
            return "License key not activated.";
        }
        $width = $width < 1920 ? $width : 1920;
        if ($is_webp) {
            $q = !empty($this->settings['webp_quality']) ? $this->settings['webp_quality'] : '';
            return $this->getCurlUrl($this->add_settings['w3_api_url'] . 'basic1.php?key=' . $key . '&width=' . $width . '&q=' . $q . '&url=' . urlencode($url) . '&webp=1');
        } else {
            $q = !empty($this->settings['img_quality']) ? $this->settings['img_quality'] : '';
            return $this->getCurlUrl($this->add_settings['w3_api_url'] . 'basic1.php?key=' . $key . '&width=' . $width . '&q=' . $q . '&url=' . urlencode($url));
        }
    }

    function w3CombineGoogleFonts($full_css_url)
    {
        if (empty($this->settings['google_fonts'])) {
            return false;
        }

        $url_arr = wp_parse_url(str_replace('#038;', '&', $full_css_url));
        if (strpos($url_arr['path'], 'css2') !== false) {
            $query_arr = explode('&', $url_arr['query']);
            if (!empty($query_arr) && count($query_arr) > 0) {
                foreach ($query_arr as $family) {
                    if (strpos($family, 'family') !== false) {
                        $this->add_settings['fonts_api_links_css2'][] = $family;
                    }
                }
                return true;
            }
            return false;

        } elseif (!empty($url_arr['query'])) {
            parse_str($url_arr['query'], $get_array);
            if (!empty($get_array['family'])) {
                $font_array = explode('|', $get_array['family']);
                foreach ($font_array as $font) {

                    if (!empty($font)) {
                        $font_split = explode(':', $font);

                        if (empty($font_split[0])) {
                            continue;
                        }
                        if (empty($this->add_settings['fonts_api_links'][$font_split[0]]) || !is_array($this->add_settings['fonts_api_links'][$font_split[0]])) {
                            $this->add_settings['fonts_api_links'][$font_split[0]] = array();
                        }
                        $this->add_settings['fonts_api_links'][$font_split[0]] = !empty($font_split[1]) ? array_merge($this->add_settings['fonts_api_links'][$font_split[0]], explode(',', $font_split[1])) : $this->add_settings['fonts_api_links'][$font_split[0]];
                    }
                }
                return true;
            }
            return false;
        }
        return false;
    }

    function w3GetTagsDataHtml($data, $start_tag, $end_tag)
    {
        $data_exists = 0;
        $i = 0;
        $tag_char_len = strlen($start_tag);
        $end_tag_char_len = strlen($end_tag);
        $script_array = array();
        while ($data_exists != -1 && $i < 5000) {
            $data_exists = strpos($data, $start_tag, $data_exists);
            if ($data_exists !== false) {
                $end_tag_pointer = strpos($data, $end_tag, $data_exists);
                $script_array[] = substr($data, $data_exists, $end_tag_pointer - $data_exists + $end_tag_char_len);
                $data_exists = $end_tag_pointer;
            } else {
                $data_exists = -1;
            }
            $i++;
        }
        return $script_array;
    }
    function w3GetTagsData($data, $start_tag, $end_tag)
    {
        $data_exists = 0;
        $i = 0;
        $tag_char_len = strlen($start_tag);
        $end_tag_char_len = strlen($end_tag);
        $script_array = array();
        while ($data_exists != -1 && $i < 5000) {
            $data_exists = strpos($data, $start_tag, $data_exists);
            if ($data_exists !== false) {
                $end_tag_pointer = strpos($data, $end_tag, $data_exists);
                $script_array[] = substr($data, $data_exists, $end_tag_pointer - $data_exists + $end_tag_char_len);
                $data_exists = $end_tag_pointer;
            } else {
                $data_exists = -1;
            }
            $i++;
        }
        return $script_array;
    }

    private function w3CacheRmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = @scandir($dir);
            if (is_array($objects) && count($objects) > 1) {
                foreach ($objects as $object) {
                    if ($object != "." && $object != "..") {
                        if (filetype($dir . "/" . $object) == "dir" && $object != 'critical-css') {
                            $this->w3CacheRmdir($dir . "/" . $object);
                        } else {
                            wp_delete_file($dir . "/" . $object);
                        }
                    }
                }
                if (is_array($objects))
                    reset($objects);
                wp_delete_file($dir);
            }
        }
    }
    function w3Rmfiles($dir)
    {
        //echo $dir; exit;
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) != "dir") {
                        wp_delete_file($dir . "/" . $object);
                    }
                }
            }
            reset($objects);
        }
    }
    private function w3Rmdir($dir)
    {
        //echo $dir; exit;
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir") {
                        $this->w3Rmdir($dir . "/" . $object);
                    } else {
                        wp_delete_file($dir . "/" . $object);
                    }
                }
            }
            reset($objects);
            wp_delete_file($dir);
        }
    }

    function w3RemoveCacheFilesHourlyEventCallback()
    {
        $this->w3CreateRandomKey();
        if (function_exists('exec')) {
            exec('rm -r ' . $this->w3GetCachePath(), $output, $retval);
        }
        $this->w3CacheRmdir($this->w3GetCachePath());
        return $this->w3CacheSizeCallback();

    }
    function w3RemoveCriticalCssCacheFiles()
    {
        w3UpdateOption('critical_css_delete_time', gmdate('d:m:Y::h:i:sa') . wp_json_encode($_REQUEST), 'no');
        $this->w3Rmdir($this->w3GetCriticalCachePath());
        $this->w3DeleteServerCache();
        w3UpdateOption('w3speedup_preload_css', '', 'no');
        w3UpdateOption('w3speedup_preload_css_total', 0, 'no');
        return true;

    }
    function w3DeleteServerCache()
    {
        $options = array(
            'method' => 'POST',
            'timeout' => 10,
            'redirection' => 5,
            'httpversion' => '1.0',
            'sslverify' => false,
            'blocking' => true,
            'headers' => array(),
            'body' => array(
                'url' => $this->add_settings['wp_home_url'],
                'key' => $this->settings['main_license_key'],
                '_wpnonce' => $nonce
            ),
            'cookies' => array()
        );

        $response = wp_remote_post($this->add_settings['w3_api_url'] . '/css/delete-css.php', $options);
        if (!is_wp_error($response)) {
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

    function w3OptimizeImage()
    {
        $image_url = $this->add_settings['wp_get']['url'];
        $image_width = !empty($this->add_settings['wp_get']['width']) ? $this->add_settings['wp_get']['width'] : '';
        $url_array = wp_parse_url($image_url);
        $image_size = !empty($image_width) ? array($image_width) : getimagesize($document_root . $url_array['path']);
        $optmize_image = optimize_image($image_size[0], $image_url);
        $optimize_image_size = @imagecreatefromstring($optmize_image);
        if (empty($optimize_image_size)) {
            echo 'invalid image';
            exit;
        } else {
            $image_type = array('gif', 'jpg', 'png', 'jpeg');
            $type = explode('.', $image_url);
            $type = array_reverse($type);
            if (in_array($type[0], $image_type)) {
				// @codingStandardsIgnoreLine
                rename($document_root . $url_array['path'], $document_root . $url_array['path'] . 'org.' . $type[0]);
                // file_put_contents($document_root.$url_array['path'],$optmize_image);
                $file = $this->w3speedsterPutContents($document_root . $url_array['path'], $optmize_image);
				// @codingStandardsIgnoreLine
				chmod($document_root . $url_array['path'], 0775);
                echo esc_url($document_root . $url_array['path']);
            }
        }
        exit;
    }

    function w3SetAllLinks($data, $resources = array())
    {
        $resource_arr = array();
        $comment_tag = $this->w3GetTagsData($data, '<!--', '-->');
        $new_comment_tag = array();
        foreach ($comment_tag as $key => $comment) {
            if (strpos($comment, '<script>') !== false || strpos($comment, '</script>') !== false || strpos($comment, '<link') !== false) {
                $new_comment_tag[] = $comment;
            }
        }
        $noscript_tag = $this->w3GetTagsData($data, '<noscript>', '</noscript>');
        $data = str_replace(array_merge($new_comment_tag, $noscript_tag), '', $data);
        $scripts = $this->w3GetTagsData($data, '<script', '</script>');
        $data = str_replace($scripts, '', $data);

        $data = str_replace($comment_tag, '', $data);
        if (!empty($this->settings['js']) && in_array('script', $resources)) {
            $resource_arr['script'] = $scripts;
        }else{
			$resource_arr['script'] = array();
		}

        if (in_array('picture', $resources)) {
            $resource_arr['picture'] = $this->w3GetTagsData($data, '<picture', '</picture>');
        }else{
			$resource_arr['picture'] = array();
		}
		if (in_array('img', $resources)) {
            $resource_arr['img'] = $this->w3GetTagsData($data, '<img', '>');
        }else{
			$resource_arr['img'] = array();
		}
		if (in_array('svg', $resources)) {
			$resource_arr['svg'] = $this->w3GetTagsData($data, '<svg', '</svg>');			
        }else{
			$resource_arr['svg'] = array();
		}
        if (!empty($this->settings['css']) && in_array('link', $resources)) {
            $resource_arr['link'] = $this->w3GetTagsData($data, '<link', '>');
            $resource_arr['style'] = $this->w3GetTagsData($data, '<style', '</style>');
		}else{
			$resource_arr['link'] = array();
			$resource_arr['style'] = array();
		}

        if (in_array('iframe', $resources)) {
            $resource_arr['iframe'] = $this->w3GetTagsData($data, '<iframe', '</iframe>');
        } else {
            $resource_arr['iframe'] = array();
        }
        if (in_array('video', $resources)) {
            $resource_arr['video'] = $this->w3GetTagsData($data, '<video', '</video>');
        } else {
            $resource_arr['video'] = array();
        }
        if (in_array('audio', $resources)) {
            $resource_arr['audio'] = $this->w3GetTagsData($data, '<audio', '</audio>');
        } else {
            $resource_arr['audio'] = array();
        }
        if (in_array('url', $resources)) {
            $resource_arr['url'] = $this->w3GetTagsData($data, 'url(', ')');
        }else{
			$resource_arr['url'] = array();
		}
        return $resource_arr;
    }

    function w3GetCacheFileSize()
    {
        $dir = $this->w3GetCachePath();
        $size = 0;
        foreach (glob(rtrim($dir, '/') . '/*', GLOB_NOSORT) as $each) {
            $size += file_exists($each) ? filesize($each) : $this->w3Foldersize($each);
        }
        return ($size / 1024) / 1024;
    }

    function w3Foldersize($path)
    {
        $total_size = 0;
        if (is_dir($path)) {
            $files = scandir($path);
            $cleanPath = rtrim($path, '/') . '/';
            foreach ($files as $t) {
                if ($t <> "." && $t <> "..") {
                    $currentFile = $cleanPath . $t;
                    if (is_dir($currentFile)) {
                        $size = $this->w3Foldersize($currentFile);
                        $total_size += $size;
                    } else {
                        $size = filesize($currentFile);
                        $total_size += $size;
                    }
                }
            }
        }
        return $total_size;
    }
    function w3CacheSizeCallback()
    {
        $filesize = $this->w3GetCacheFileSize();
        w3UpdateOption('w3_speedup_filesize', $filesize, true);
        return $filesize;
    }
    function w3CreateRandomKey()
    {
        w3UpdateOption('w3_rand_key', wp_rand(10, 1000), false);
    }

    function w3GetPointerToInjectFiles($html)
    {
        global $appendonstyle;
        if (!empty($appendonstyle)) {
            return $appendonstyle;
        }

        $start_body_pointer = strpos($html, '<body');

        $start_body_pointer = $start_body_pointer ? $start_body_pointer : strpos($html, '</head');

        $head_html = substr($html, 0, $start_body_pointer);
        $comment_tag = $this->w3GetTagsData($head_html, '<!--', '-->');
        foreach ($comment_tag as $comment) {
            $head_html = str_replace($comment, '', $head_html);
        }


        if (strpos($head_html, '<style') !== false) {

            $appendonstyle = 1;

        } elseif (strpos($head_html, '<link') !== false) {

            $appendonstyle = 2;

        } else {

            $appendonstyle = 3;

        }
        return $appendonstyle;
    }

    function w3CheckIfPageExcluded($exclude_setting)
    {

        $e_p_from_optimization = !empty($exclude_setting) ? explode("\r\n", $exclude_setting) : array();

        if (!empty($e_p_from_optimization)) {
            foreach ($e_p_from_optimization as $e_page) {
                if (empty($e_page)) {
                    continue;
                }
                if (empty($this->add_settings['wp_get']['testing']) && (is_home() || is_front_page()) && $this->add_settings['wp_home_url'] == $e_page) {
                    return true;
                } else if ($this->add_settings['wp_home_url'] != $e_page) {
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
            wp_delete_file($path . "wp-content/wp-cache-config.php");

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
    function w3PreloadResources()
    {
        $preload_html = '';
        $file = $this->w3GetFullUrlCachePath() . '/preload_css.json';
        if (!file_exists($file) && !empty($this->add_settings['preload_resources'])) {
            $this->w3CreateFile($file, wp_json_encode($this->add_settings['preload_resources']));
        }
        if (file_exists($file)) {
            $preload_json = (array) json_decode($this->w3speedsterGetContents($file));
            $this->add_settings['preload_resources']['css'] = !empty($preload_json['css']) ? $preload_json['css'] : array();
            $this->add_settings['preload_resources']['all'] = !empty($preload_json['all']) ? $preload_json['all'] : array();
        }
        $preload_resources = !empty($this->settings['preload_resources']) ? explode("\r\n", $this->settings['preload_resources']) : array();
        if (is_array($this->add_settings['preload_resources']['all']) && count($this->add_settings['preload_resources']['all']) > 0) {
            $preload_resources = array_merge($preload_resources, $this->add_settings['preload_resources']['all']);
        }

        if (!empty($this->add_settings['preload_resources']['critical_css'])) {
            $preload_resources = $this->add_settings['preload_resources']['critical_css'] != 1 ? array_merge($preload_resources, array($this->add_settings['preload_resources']['critical_css'])) : $preload_resources;
        } elseif (!empty($this->add_settings['preload_resources']['css'])) {
            $preload_resources = array_merge($preload_resources, $this->add_settings['preload_resources']['css']);
        }
        if (!empty($preload_resources)) {
            foreach ($preload_resources as $link) {
                $link_arr = explode('?', $link);
                $extension = explode(".", $link_arr[0]);
                $extension = end($extension);
                if (empty($extension)) {
                    continue;
                }
				$crossorigin = $this->w3IsExternal($link) ? 'crossorigin' : '';
                if (in_array($extension, array('jpeg', 'jpg', 'png', 'gif', 'webp', 'tiff', 'psd', 'raw', 'bmp', 'heif', 'indd'))) {
                    $preload_html .= '<link rel="preload" href="' . trim($link) . '" as="image"/>';
                }
                if (in_array(strtolower($extension), array('otf', 'ttf', 'woff', 'woff2', 'gtf', 'mmm', 'pea', 'tpf', 'ttc', 'wtf'))) {
                    $preload_html .= '<link rel="preload" href="' . trim($link) . '" as="font" type="font/' . $extension . '" crossorigin>';
                }

                if (in_array($extension, array('mp4', 'webm'))) {
                    $preload_html .= '<link rel="preload" href="' . trim($link) . '" as="video" type="video/' . $extension . '">';
                }
                if ($extension == 'css') {
                    $preload_html .= '<link rel="preload" href="' . trim($link) . '" as="style" '.$crossorigin.'>';
                }
                if ($extension == 'js') {
                    $preload_html .= '<link rel="preload" href="' . trim($link) . '" as="script" '.$crossorigin.'>';
                }
            }
        }
        return $preload_html;
    }

    function removeDotPathSegments($path)
    {
        if (strpos($path, '.') === false) {
            return $path;
        }

        $inputBuffer = $path;
        $outputStack = [];

        while ($inputBuffer != '') {
            if (strpos($inputBuffer, "./") === 0) {
                $inputBuffer = substr($inputBuffer, 2);
                continue;
            }
            if (strpos($inputBuffer, "../") === 0) {
                $inputBuffer = substr($inputBuffer, 3);
                continue;
            }

            if ($inputBuffer === "/.") {
                $outputStack[] = '/';
                break;
            }
            if (substr($inputBuffer, 0, 3) === "/./") {
                $inputBuffer = substr($inputBuffer, 2);
                continue;
            }

            if ($inputBuffer === "/..") {
                array_pop($outputStack);
                $outputStack[] = '/';
                break;
            }
            if (substr($inputBuffer, 0, 4) === "/../") {
                array_pop($outputStack);
                $inputBuffer = substr($inputBuffer, 3);
                continue;
            }

            if ($inputBuffer === '.' || $inputBuffer === '..') {
                break;
            }

            if (($slashPos = stripos($inputBuffer, '/', 1)) === false) {
                $outputStack[] = $inputBuffer;
                break;
            } else {
                $outputStack[] = substr($inputBuffer, 0, $slashPos);
                $inputBuffer = substr($inputBuffer, $slashPos);
            }
        }

        return implode($outputStack);
    }
	
	function hookCallbackFunction($code,...$args){
		
		
		
		if(!empty($code)){
			$code = stripcslashes($code);
			// @codingStandardsIgnoreLine
			eval("$code");
			
			
			return $args[0];
		}
	}
	
	function w3SpeedsterGetDataAdvancedCacheFile($cachePath = ''){
        $cachePath =  ($cachePath) ? $cachePath :str_replace('\\', '/',$this->add_settings['wp_content_path'] . '/cache/w3-cache/html');
		
        $data = '<?php
        /**
         * Advanced Cache PHP file for WordPress
         * Added By W3speedster Pro
         
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
		if ($enableCachingGetPara &&  $queryPara == 1) {
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
			$cacheDir = "'.$cachePath.'".$path1;
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
		
		//RewriteRule !/  "/wp-content/cache/all/index.html" [L]


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

		if($this->wptouch){
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
}