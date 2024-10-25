<?php
namespace W3speedster;

checkDirectCall();

class w3core{
	var $add_settings;
    var $settings;
    var $html = "";
	
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
        if (!empty($this->add_settings['enable_cdn']) && empty($this->add_settings['exclude_cdn']) || !in_array($ext, $this->add_settings['exclude_cdn'])) {
            $enable_cdn = 1;
        }
        return $enable_cdn;
    }
	private function isSpecialContentType()
    {
        if ($this->w3Endswith($this->add_settings['full_url'], '.xml') || $this->w3Endswith($this->add_settings['full_url'], '.xsl')) {
            return true;
        }

        return false;
    }
	function w3DebugTime($process)
    {
        if (!empty($this->add_settings['wp_get']['w3_debug'])) {
            $starttime = !empty($this->add_settings['starttime']) ? $this->add_settings['starttime'] : $this->microtime_float();
            $endtime = $this->microtime_float();
            $this->html .= $process . '-' . ($endtime - $starttime).'-ram-'.(memory_get_usage()/1024/1024).'-cpu-'.$this->w3JsonEncode(sys_getloadavg()) . "\n";
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
        $preload_css_new = $preload_css = $this->w3GetOption('w3speedup_preload_css');
		if (!empty($preload_css) && is_array($preload_css) && count($preload_css) > 0) {
            foreach ($preload_css as $key1 => $url) {
                if (strpos($key1, home_url()) !== false) {
                    unset($preload_css_new[$key1]);
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
                    continue;
                }
                $running_url = str_replace($this->add_settings['document_root'],' ',$url[2]);
				$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
				w3UpdateOption('w3speedup_critical_running_url',urldecode($key));
                $response = $this->w3CreatePreloadCss($key, $url[0], $url[2]);

                if (!empty($response) && $response === "exists") {
                    unset($preload_css_new[$key1]);
                    continue;
                }
                if (!empty($response) && $response === "hold") {
                    $this->w3Echo('rocket5' . $response);
                    break;
                }
                if ($response || $preload_css[$key1][1] == 1) {
                    $this->w3Echo('rocket4' . $response);
                    unset($preload_css_new[$key1]);
                } else {
                    $this->w3Echo('rocket6');
                    $preload_css_new[$key1][1] = 1;
                }
                break;
            }
            w3UpdateOption('w3speedup_preload_css', $preload_css_new, 'no');
			return $response;
        }elseif(!empty($_REQUEST['page']) && $_REQUEST['page'] == 'admin' && empty($this->w3GetOption('w3speedup_preload_css_total'))){
			$this->browsePost();
		}
    }
	function browsePost(){
		$options = array(
					'url' => home_url()
				 );
		$this->w3RemoteGet($this->add_settings['w3_api_url'] . '/css/browse.php',$options);
	}
	function w3GetHtmlCachePath($url)
    {
		$path = $this->add_settings['root_cache_path'] . '/html/' . trim(str_replace($this->add_settings['site_url'], '', $url), '/') . '/index.html';
		if (file_exists($path)) {
			return $path;
		}
		return false;
    }
	function w3CssCompressInit($minify)
    {
        $minify = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $minify);
        $minify = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), ' ', $minify);
        return $minify;
    }
	function w3CreatePreloadCss($url, $filename, $css_path)
    {
        $this->w3Echo('rocket2' . $filename . $url); 
        $this->w3Echo('rocket3' . $css_path);
        if (file_exists($css_path . '/' . $filename)) {
            $this->w3Echo('rocket9');
            return 'exists';
        }
        $nonce = $this->createSecureKey('purge_critical_css');
        if ($this->add_settings['enable_cdn']) {
            $css_urls = $this->add_settings['home_url'] . ',' . $this->add_settings['image_home_url'];
        } else {
            $css_urls = $this->add_settings['home_url'];
        }
		list($preload_total,$preload_created) = $this->w3CriticalCssDetails();
		$responseCssPath = $this->add_settings['critical_css_path'].'/responses';
		$this->w3CheckIfFolderExists($responseCssPath);
		$responseCssFile = $responseCssPath.'/'.base64_encode($url).'.json';
		if(file_exists($responseCssFile)){
			$content = file_get_contents($responseCssFile);
			$contentJson = (array)json_decode($content);
			if(!empty($contentJson['url']) && !empty($contentJson['w3_css']) && !empty($contentJson['_wpnonce']) && $this->checkSecurityKey('purge_critical_css',$contentJson['_wpnonce'])){
				$this->saveCriticalCss($contentJson['w3_css'],$url,$css_path,$filename);
			}
			unlink($responseCssFile);
			return 'exists';
		}
        $options = array(
                'url' => $url,
                'key' => $this->settings['main_license_key'],
                '_wpnonce' => $nonce,
                'filename' => $filename,
                'css_url' => $css_urls,
                'path' => $css_path,
				'response_url'=> W3SPEEDSTER_URL.'includes/css-response.php',
				'response_path'=> $responseCssPath,
				'auto' => ($preload_created ? $preload_created : 1)
				);
        
		if(function_exists('w3ModifyCriticalCssAPIOptions')){
			$options = w3ModifyCriticalCssAPIOptions($options);
		}
        $response = $this->w3RemoteGet($this->add_settings['w3_api_url'] . '/css/', $options);
        $this->w3Echo('<pre>');
        $this->w3PrintR($options);
        $this->w3Echo($response);
        if (!empty($response)) {
            $this->w3Echo('rocket3' . $css_path . '/' . $filename);
            $response_arr = (array) json_decode($response);
            if (!empty($response_arr['result']) && $response_arr['result'] == 'success' && !empty($response_arr['w3_css'])){
				$this->saveCriticalCss($response_arr['w3_css'],$url,$css_path,$filename);
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
    }
	function saveCriticalCss($w3Css,$url,$css_path,$filename){
		if(function_exists('w3speedup_customize_critical_css')){
			$w3Css = w3speedup_customize_critical_css($w3Css);
		}
		if(!empty($this->settings['hook_customize_critical_css'])){
			$code = str_replace(array('$critical_css'),array('$args[0]'),$this->settings['hook_customize_critical_css']);
			$w3Css = $this->hookCallbackFunction($code,$w3Css);
		}
		$this->w3CreateFile($css_path . '/' . $filename, $w3Css);
		$preload_css = $this->w3GetOption('w3speedup_preload_css');
		unset($preload_css[base64_encode($response_arr['url'])]);
		$file = $this->w3GetFullUrlCachePath($url) . '/main_css.json';
		if (file_exists($file)) {
			wp_delete_file($file);
		}
		$this->w3DeleteHtmlCacheAfterPreloadCss($url);
		return true;
	}
	function w3PutPreloadCss()
    {
        if (!isset($_POST['_wpnonce']) || $_POST['_wpnonce'] != $this->w3GetOption('purge_critical_css')) {
            echo 'Request not valid';
            exit;
        }
        if (!empty($_POST['url']) && !empty($_POST['filename']) && !empty($_POST['w3_css'])) {
             if (!empty($_POST['result']) && $_POST['result'] == 'success') {
				$url = $_POST['url'];
				$w3Css = $_POST['w3_css'];
				$path = $_POST['path'];
				$filename = $_POST['filename'];
				if(function_exists('w3speedup_customize_critical_css')){
					$w3Css = w3speedup_customize_critical_css($w3Css);
				}
				if(!empty($this->settings['hook_customize_critical_css'])){
					$code = str_replace(array('$critical_css'),array('$args[0]'),$this->settings['hook_customize_critical_css']);
					$w3Css = $this->hookCallbackFunction($code,$w3Css);
				}
                $this->w3CreateFile($path . '/' . $filename, $w3Css);
                $preload_css = $this->w3GetOption('w3speedup_preload_css');
                unset($preload_css[base64_encode($url)]);
				$file = $this->w3GetFullUrlCachePath($url) . '/main_css.json';
                if (file_exists($file)) {
                    wp_delete_file($file);
                }
                $this->w3DeleteHtmlCacheAfterPreloadCss($url);
                echo 'saved';
				exit;
            }
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
                $this->w3CreateFile($file, $this->w3JsonEncode($insert_content_head));
            }
        }
    }

    function w3InsertContentHead($content, $pos,$link='')
    {
        global $insert_content_head;
        $insert_content_head[] = array($content, $pos,$link);
        if ($pos == 1) {
            $this->html = preg_replace('/<style/', $content . '<style', $this->html, 1, $count);
        } elseif ($pos == 2) {
			if(!empty($link)){
				$this->html = str_replace($link, $content . $link, $this->html);
			}else{
				$this->html = preg_replace('/(<link.*>)/', $content . "$1", $this->html, 1);
			}
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
            $this->html = $this->rightReplace($this->html, '<link ', $content . '<link ');
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
	function insertString($originalString, $stringToInsert, $position) {
		return substr($originalString, 0, $position) . $stringToInsert . substr($originalString, $position);
	}
    
    function w3StrReplaceSetImg($str, $rep)
    {
        global $str_replace_str_img, $str_replace_rep_img;
        $str_replace_str_img[] = $str;
        $str_replace_rep_img[] = $rep;
    }
	function w3StrReplaceSetJs($str, $rep)
    {
        global $str_replace_str_js, $str_replace_rep_js;
        $str_replace_str_js[] = $str;
        $str_replace_rep_js[] = $rep;
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
		$this->w3DebugTime('start json merge');
		$str_replace_str_array = array_merge($str_replace_str_img, $str_replace_str_css, $str_replace_str_js);
		$str_replace_rep_array = array_merge($str_replace_rep_img, $str_replace_rep_css, $str_replace_rep_js);
		$this->w3DebugTime('end json merge');
		$this->html = str_replace($str_replace_str_array, $str_replace_rep_array, $this->html);
	}
	function w3GetFullUrlCachePath($full_url = '')
    {
        $cache_path = $this->w3CheckFullUrlCachePath($full_url);
        $this->w3CheckIfFolderExists($cache_path);
        return $cache_path;
    }
	function w3GetFullUrlCache($full_url = '')
    {
        $cache_path = str_replace($this->add_settings['document_root'],$this->add_settings['image_home_url'],$this->w3CheckFullUrlCachePath($full_url));
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
            $this->w3Mkdir($realpath, 0755, true);
        } catch (Exception $e) {
            echo 'Message: ' . esc_html($e->getMessage());
        }
        return $path;
    }
	
	function optimizeImage($width, $url, $is_webp = false){
		$key = $this->settings['main_license_key'];
        $key_activated = $this->settings['is_activated'];
        if (empty($key) || empty($key_activated)) {
            return "License key not activated.";
        }
		$apiUrl = $this->add_settings['w3_api_url'] . '/basic1.php';
        $width = $width < 1920 ? $width : 1920;
		$options = array(
						'key' => $key,
						'width' => $width,
						'url' => urlencode($url)
						);
		if ($is_webp) {
			$q = !empty($this->settings['webp_quality']) ? $this->settings['webp_quality'] : '';
			$option = array('webp' => 1,'q' => $q);
        } else {
			$q = !empty($this->settings['img_quality']) ? $this->settings['img_quality'] : '';
			$option = array('webp' => 1,'q' => $q);
        }
		$options = array_merge($options,$option);
		return $this->w3RemoteGet($apiUrl,array_merge($options,$option));
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
                            $this->w3DeleteFile($dir . "/" . $object);
                        }
                    }
                }
                if (is_array($objects))
                    reset($objects);
                $this->w3DeleteFile($dir);
            }
			return rmdir($dir);
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
                        $this->w3DeleteFile($dir . "/" . $object);
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
                        $this->w3DeleteFile($dir . "/" . $object);
                    }
                }
            }
            reset($objects);
            return rmdir($dir);
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
        w3UpdateOption('critical_css_delete_time', gmdate('d:m:Y::h:i:sa') . $this->w3JsonEncode($_REQUEST), 'no');
        $this->w3Rmdir($this->add_settings['critical_css_path']);
        $this->w3DeleteServerCache();
        w3UpdateOption('w3speedup_preload_css', '', 'no');
        w3UpdateOption('w3speedup_preload_css_total', 0, 'no');
        return true;

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
        w3UpdateOption('w3_rand_key', $this->w3Rand(), false);
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
	function w3PreloadResources()
    {
        $preload_html = '';
        $file = $this->w3GetFullUrlCachePath() . '/preload_css.json';
        if (!file_exists($file) && !empty($this->add_settings['preload_resources'])) {
            $this->w3CreateFile($file, $this->w3JsonEncode($this->add_settings['preload_resources']));
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
	function checkIgnoreCriticalCss(){
		if(isset($this->add_settings['ignoreCriticalCss'])){
			return $this->add_settings['ignoreCriticalCss'];
		}
		$ignore_critical_css = 0;
		if(!empty($this->add_settings['w3UserLoggedIn']) || is_404()){
			$ignore_critical_css = 1;
		}
		if(function_exists('w3_no_critical_css')){
			$ignore_critical_css = w3_no_critical_css($this->add_settings['full_url']);
		}
				
		if(!empty($this->settings['hook_no_critical_css'])){
			$url = $this->add_settings['full_url'];
			$code = str_replace(array('$ignore_critical_css','$url'),array('$args[0]','$args[1]'),$this->settings['hook_no_critical_css']);	
			$ignore_critical_css = $this->hookCallbackFunction($code,$ignore_critical_css,$url);
		}
		$this->add_settings['ignoreCriticalCss'] = $ignore_critical_css;
	}
	function w3AddPageCriticalCss(){
		if(!empty($this->settings['optimization_on'])){
			$preload_css = $this->w3GetOption('w3speedup_preload_css');
			$preload_css = (empty($preload_css) || !is_array($preload_css)) ? array() : $preload_css;
			if(is_array($preload_css) && count($preload_css) > 50){
				return;
			}
			if(!is_array($preload_css) || (is_array($preload_css) && !array_key_exists(base64_encode($this->add_settings['full_url_without_param']),$preload_css)) || (!empty($preload_css[$this->add_settings['full_url_without_param']]) && $preload_css[$this->add_settings['full_url_without_param']][0] != $this->add_settings['critical_css']) ){
				$preload_css[base64_encode($this->add_settings['full_url_without_param'])] = array($this->add_settings['critical_css'],2,$this->w3PreloadCssPath());
				w3UpdateOption('w3speedup_preload_css',$preload_css,'no');
				w3UpdateOption('w3speedup_preload_css_total',(int)$this->w3GetOption('w3speedup_preload_css_total')+1,'no');
				if(!empty($this->settings['enable_background_critical_css'])){
					$this->w3ScheduleEvent('w3speedup_preload_css_min','w3speedster_every_minute');
				}
				return serialize($this->w3GetOption('w3speedup_preload_css'));
			}
		}
	}
	function w3CustomJsEnqueue(){
		if(!empty($this->settings['custom_js'])){
			$custom_js = stripslashes($this->settings['custom_js']);
		}else{
			$custom_js = 'console.log("js loaded");';
		}
		$js_file_name1 = 'custom_js_after_load.js';
		if(!file_exists($this->w3GetCachePath('js').'/'.$js_file_name1)){
			$this->w3CreateFile($this->w3GetCachePath('js').'/'.$js_file_name1, $custom_js);
		}
		$this->html = $this->w3StrReplaceLast('</body>','<script src="'.$this->w3GetCacheUrl('js').'/'.$js_file_name1.'"></script></body>',$this->html);
	}
	function loadStyleTagInHead($style_tags){
		
		$counter = 0;
		$load_style_tag_in_head_arr = array();
		$load_style_tag_in_head	= !empty($this->settings['load_style_tag_in_head']) ? explode("\r\n", $this->settings['load_style_tag_in_head']) : array();
		foreach($load_style_tag_in_head as $ex_css){
			$ex_css_arr = explode(' ',$ex_css);
			$load_style_tag_in_head_arr[$counter][0] = $ex_css_arr[0];
			if(!empty($ex_css_arr[1])){
				$load_style_tag_in_head_arr[$counter][1] = $ex_css_arr[1];
			}
			$counter++;
		}
		$styleArr = array();
		$styleRep = array();
		//$stylesContentFile = array();
		$stylesContent = '';
		
		foreach($style_tags as $style_tag){
			$load_in_head = 0;
			$file_name = $this->w3GetOption('w3_rand_key');
			foreach($load_style_tag_in_head_arr as $ex_css){
				if(!empty($ex_css[0]) && !empty($style_tag) && strpos($style_tag, $ex_css[0]) !== false){
					$styleArr[] = $style_tag;
					
					if(!empty($ex_css[1])){
						$file_name = $ex_css[0];
						$stylesContentFile = $this->w3ParseScript('style',$style_tag);
						$link = $this->w3LoadStyleInFile($file_name,$stylesContentFile);
						$styleRep[] = $link;
					}else{
						$stylesContent .= $this->w3ParseScript('style',$style_tag);
						$styleRep[] = '';
					}
					break;
				}
			}	
		}
		if(count($styleArr) > 0 && count($styleRep) > 0){
			$this->html = str_replace($styleArr,$styleRep,$this->html);
		}
		if(empty($stylesContent)){
			return;
		}
		$this->html = str_replace('</head>','<style>'.$this->w3CssCompressInit($stylesContent).'</style></head>',$this->html);
	}
	function w3LoadStyleInFile($file_name,$stylesContentFile){
		$file_name_cache = md5($file_name).'.css';
		if(!file_exists($this->w3CheckFullUrlCachePath().'/'.$file_name_cache)){
			$this->w3CreateFile($this->w3CheckFullUrlCachePath().'/'.$file_name_cache,$this->w3CssCompressInit($stylesContentFile));
		}
		$defer = 'href=';
		if(!$this->checkIgnoreCriticalCss() && !empty($this->settings['load_critical_css']) && !empty($this->add_settings['critical_css']) && file_exists($this->w3PreloadCssPath().'/'.$this->add_settings['critical_css'])){
			$defer = 'data-css="1" href=';
		}
		return '<link rel="stylesheet" '.$defer.'"'.$this->w3GetFullUrlCache().'/'.$file_name_cache.'">';
	}

	function createBlankDataImage($width, $height) {

		$image = '%3Csvg%20xmlns=\'http://www.w3.org/2000/svg\'%20width=\''.$width.'\'%20height=\''.$height.'\'%3E%3Crect%20width=\'100%25\'%20height=\'100%25\'%20opacity=\'0\'/%3E%3C/svg%3E';
		$dataURI = 'data:image/svg+xml,' . $image;
		$this->w3CreateFile($this->add_settings['root_cache_path'].'/images/blank-'.$width.'x'.$height.'.txt',$dataURI);
	}
	
	function createSVGImageFile($filename, $content) {
		$this->w3CreateFile($filename,$content);
	}
	
	function w3LoadGoogleFonts(){
		$google_font = array();
        if(!empty($this->add_settings['fonts_api_links'])){
            $all_links = '';
            foreach($this->add_settings['fonts_api_links'] as $key => $links){
                $all_links .= !empty($links) && is_array($links) ? $key.':'.implode(',',$links).'|' : $key.'|';
            }
            $google_font[] = $this->add_settings['secure']."fonts.googleapis.com/css?display=swap&family=".urlencode(trim($all_links,'|'));
        }
		if(!empty($this->add_settings['fonts_api_links_css2'])){
			$all_links = 'https://fonts.googleapis.com/css2?';
			foreach($this->add_settings['fonts_api_links_css2'] as $font){
				$all_links .= $font.'&';
			}
			$all_links .= 'display=swap';
			$google_font[] = $all_links;
		}
		return '<script>var w3GoogleFont='.$this->w3JsonEncode($google_font).';</script>';
	}
	function lazyLoadIframe($iframe_links){
		if(!empty($this->settings['lazy_load_iframe'])){
            foreach($iframe_links as $img){
				if(strpos($img,'\\') !== false){
					continue;
				}
                if($this->checkImageExcluded($img)){
                    continue;
                }
                $img_obj = $this->w3ParseLink('iframe',$img);
				$iframe_html = '';
                if(empty($img_obj['src'])){
					continue;
				}
				if(strpos($img_obj['src'],'youtu') !== false){
                    preg_match("#([\/|\?|&]vi?[\/|=]|youtu\.be\/|embed\/)([a-zA-Z0-9_-]+)#", $img_obj['src'], $matches);
                    if(empty($img_obj['style'])){
                        $img_obj['style'] = '';
                    }
                    $img_obj['style'] .= 'background-image:url(https://i.ytimg.com/vi/'.trim(end($matches)).'/sddefault.jpg);background-size:contain;';
					//$iframe_html = '<img width="68" height="48" class="iframe-img" src="/wp-content/uploads/yt-png2.png"/>';
                }
				$img_obj['data-src'] = $img_obj['src'];
                $img_obj['src'] = 'about:blank';
                $img_obj['data-class'] = 'LazyLoad';
				
				$iframelazy =0;
				if(!empty($this->settings['hook_iframe_to_iframelazy'])){
					$code = str_replace('$iframelazy','$args[0]',$this->settings['hook_iframe_to_iframelazy']);
					$iframelazy = $this->hookCallbackFunction($code,$iframelazy);
				}
				if((function_exists('w3_change_iframe_to_iframelazy') && w3_change_iframe_to_iframelazy()) || $iframelazy){
					$this->w3StrReplaceSetImg($img,$this->w3ImplodeLinkArray('iframelazy',$img_obj).$iframe_html);
				}else{
					$this->w3StrReplaceSetImg($img,$this->w3ImplodeLinkArray('iframe',$img_obj).$iframe_html);
				}
            }
	    }
	}
	function lazyLoadVideo($video_links){
		if(!empty($this->settings['lazy_load_video'])){
            if(strpos($this->add_settings['upload_base_url'],$this->add_settings['site_url']) !== false){
				$v_src = $this->add_settings['image_home_url'].str_replace($this->add_settings['site_url'],'',$this->add_settings['upload_base_url']).'/blank.mp4';
			}else{
				$v_src = $this->add_settings['upload_base_url'].'/blank.mp4';
			}
            foreach($video_links as $img){
				if(strpos($img,'\\') !== false){
					continue;
				}
                if($this->checkImageExcluded($img)){
                    continue;
                }
				$img_new = $img;
				if(strpos($img,'poster=') !== false){
					$img_new = str_replace('poster=','data-poster=',$img_new);
				}
                $img_new = str_replace('src=','src="'.$v_src.'" data-src=',$img_new);
				$img_new = str_replace('<video ','<video data-class="LazyLoad" ',$img_new);
				$videolazy = 0;
				if(!empty($this->settings['hook_video_to_videolazy'])){
					$code = str_replace('$videolazy','$args[0]',$this->settings['hook_video_to_videolazy']);
					$videolazy = $this->hookCallbackFunction($code,$videolazy);
				}
				if(function_exists('w3_change_video_to_videolazy') && w3_change_video_to_videolazy() || $videolazy){
					$img_new= str_replace(array('<video','</video>'),array('<videolazy','</videolazy>'),$img_new);
				}
                $this->w3StrReplaceSetImg($img,$img_new);
            }
        }
	}
	function lazyLoadAudio($audio_links){
		if(!empty($this->settings['lazy_load_audio'])){
            if(strpos($this->add_settings['upload_base_url'],$this->add_settings['site_url']) !== false){
				$v_src = $this->add_settings['image_home_url'].str_replace($this->add_settings['site_url'],'',$this->add_settings['upload_base_url']).'/blank.mp3';
			}else{
				$v_src = $this->add_settings['upload_base_url'].'/blank.mp3';
			}
            foreach($audio_links as $img){
				if(strpos($img,'\\') !== false){
					continue;
				}
                if($this->checkImageExcluded($img)){
                    continue;
                }
				
                $img_new = str_replace('src=','data-class="LazyLoad" src="'.$v_src.'" data-src=',$img);
                $this->w3StrReplaceSetImg($img,$img_new);
            }
        }
	}
	function lazyLoadImg($picture_links,$img_links){
		if(empty($this->settings['lazy_load'])){
			return ;
		}
		if(!empty($picture_links)){
			$exclude_cdn_arr = !empty($this->add_settings['exclude_cdn']) ? $this->add_settings['exclude_cdn'] : array();
			foreach($picture_links as $img){
				$blank_image_url = ($this->add_settings['enable_cdn'] && !in_array('.png',$exclude_cdn_arr)) ? str_replace($this->add_settings['site_url'],$this->add_settings['image_home_url'],$this->add_settings['upload_base_url']) : $this->add_settings['upload_base_url'];
				$imgnn = $img;
				if($this->checkImageExcluded($img)){
                    continue;
                }
				$imgTag = $this->w3GetTagsData($img, '<img', '>');
				if(is_array($imgTag) && count($imgTag) > 0){
					$imgArr = $this->w3ParseLink('img',str_replace($this->add_settings['image_home_url'],$this->add_settings['site_url'],$imgTag[0]));
					if(!$this->w3IsExternal($imgArr['src'])){
						list($img_root_path,$img_root_url) = $this->getImgRootPath($imgArr['src']);
						$imgsrc_filepath = $this->getResourceRootPath($imgArr['src'],$img_root_url);
						$img_size = $this->w3GetImageSize($imgTag,$img_root_path.$imgsrc_filepath);
						$blank_image_url = $this->getBlankImageUrl($img_size);
					}else{
						$blank_image_url = $this->getBlankImageUrl(array($imgArr['width'],$imgArr['height']));
					}
					
					$imgTagN = str_replace(array(' src=',' srcset="'),array(' src="'.$blank_image_url.'" data-src=',' data-srcset="'),$imgTag[0]); 
					$imgnn = str_replace(array('<picture ','<picture>', $imgTag[0], ' srcset="'),array('<picture data-class="LazyLoad" ','<picture data-class="LazyLoad">',$imgTagN,' data-srcset="'),$imgnn);
					$this->w3StrReplaceSetImg($img,$imgnn);
				}
				
				
				
			}
		}
		if(!empty($img_links)){
			$exclude_cdn_arr = !empty($this->add_settings['exclude_cdn']) ? $this->add_settings['exclude_cdn'] : array();
			$webp_enable = $this->add_settings['webp_enable'];
			$webp_enable_instance = $this->add_settings['webp_enable_instance'];
			$webp_enable_instance_replace = $this->add_settings['webp_enable_instance_replace'];
			foreach($img_links as $img){
				$imgnn = $img;
				$imgnn_arr = $this->w3ParseLink('img',str_replace($this->add_settings['image_home_url'],$this->add_settings['site_url'],$imgnn));
				if(empty($imgnn_arr['src'])){
					continue;
				}
				if(strpos($imgnn_arr['src'],'\\') !== false){
					continue;
				}
				$imgnn_arr['src'] = urldecode(trim($imgnn_arr['src']));
				if(strpos($imgnn_arr['src'],'?') !== false){
					$imgnn_arr['src'] = $this->removeQueryParams($imgnn_arr['src']);
				}
				if(!$this->w3IsExternal($imgnn_arr['src'])){
					list($img_root_path,$img_root_url) = $this->getImgRootPath($imgnn_arr['src']);
					$w3_img_ext = '.'.pathinfo($imgnn_arr['src'], PATHINFO_EXTENSION);
					$imgsrc_filepath = $this->getResourceRootPath($imgnn_arr['src'],$img_root_url);
					$imgsrc_webpfilepath = str_replace($this->add_settings['upload_path'],$this->add_settings['webp_path'],$img_root_path).$imgsrc_filepath.'w3.webp';
					if($this->add_settings['enable_cdn']){
						$image_home_url = $this->add_settings['image_home_url'];
						foreach($exclude_cdn_arr as $cdn){
							if(strpos($img,$cdn) !== false){
								$image_home_url = $this->add_settings['site_url'];
								break;
							}
						}
						$imgnn = str_replace($this->add_settings['site_url'],$image_home_url,$imgnn);
					}else{
						$image_home_url = $this->add_settings['site_url'];
					}
					$imgnn = trim(preg_replace('/\s+/', ' ', $imgnn));
					$img_size = $this->w3GetImageSize($img,$img_root_path.$imgsrc_filepath);
					if(!empty($img_size[0]) && !empty($img_size[1])){
						$this->getImageAttributes($imgnn,$imgnn_arr,$img_size);
					}
					if(strpos($img, ' srcset=') === false && !empty($this->settings['resp_bg_img'])){
						if(!empty($img_size[0]) && $img_size[0] > 600){
							$w3_thumbnail = rtrim(str_replace($w3_img_ext.'$','-595xh'.$w3_img_ext.'$',$imgsrc_filepath.'$'),'$');
							if(in_array($w3_img_ext, $webp_enable) && !file_exists($this->add_settings['document_root'].$w3_thumbnail) && !empty($this->settings['opt_img_on_the_go'])){
								$response = $this->w3OptimizeAttachmentUrl($img_root_path.$imgsrc_filepath);
							}
							if(file_exists($img_root_path.$w3_thumbnail)){
								$w3_thumbnail = str_replace(' ','%20',$w3_thumbnail);
								$imgnn_arr['src'] = str_replace(' ','%20',$imgnn_arr['src']);
								$imgnn = str_replace(' src=',' data-mob-src="'.$img_root_url.$w3_thumbnail.'" src=',$imgnn);
							}
						}
					}
					if(count($webp_enable) > 0 && in_array($w3_img_ext, $webp_enable)){
						if(!empty($this->settings['opt_img_on_the_go']) && !file_exists($imgsrc_webpfilepath) && file_exists($img_root_path.$imgsrc_filepath)){
							$this->w3OptimizeAttachmentUrl($img_root_path.$imgsrc_filepath);
						}
						if(file_exists($imgsrc_webpfilepath) && (!empty($this->add_settings['disable_htaccess_webp']) || !file_exists($this->add_settings['document_root']."/.htaccess") || $this->add_settings['image_home_url'] != $this->add_settings['site_url'] ) ){
							$imgnn = str_replace($webp_enable_instance,$webp_enable_instance_replace,$imgnn);
						}
					}
				}
				
				if($this->checkImageExcluded($img)){
					if(function_exists('w3speedup_customize_image')){
						$imgnn = w3speedup_customize_image($imgnn,$img,$imgnn_arr);
					}
					if(!empty($this->settings['hook_customize_image'])){
						
						$code = str_replace(array('$imgnn','$img','$imgnn_arr'),array('$args[0]','$args[1]','$args[2]'),$this->settings['hook_customize_image']);
						$imgnn = $this->hookCallbackFunction($code,$imgnn,$img,$imgnn_arr);
					}
					if($img != $imgnn){
						$this->w3StrReplaceSetImg($img,$imgnn);
					}
					continue;
				}
				$blank_image_url = $this->getBlankImageUrl($img_size); 
				if(strpos($blank_image_url,'/blank') === false && strpos($blank_image_url,'data:image') === false){
					$blank_image_url .= '/blank.png';
				}
                $imgnn = str_replace(' src=',' data-class="LazyLoad" src="'. $blank_image_url .'" data-src=',$imgnn);
				if(strpos($imgnn, ' srcset=') !== false){
					$imgnn = str_replace(' srcset=',' data-srcset=',$imgnn);
				}
				
				if(function_exists('w3speedup_customize_image')){
					$imgnn = w3speedup_customize_image($imgnn,$img,$imgnn_arr);
				}
				if(!empty($this->settings['hook_customize_image'])){
					$code = str_replace(array('$imgnn','$img','$imgnn_arr'),array('$args[0]','$args[1]','$args[2]'),$this->settings['hook_customize_image']);
					$imgnn = $this->hookCallbackFunction($code,$imgnn,$img,$imgnn_arr);
				}
                $this->w3StrReplaceSetImg($img,$imgnn);
            }
		}
	}
	function removeQueryParams($url) {
		$parsedUrl = parse_url($url);
		$cleanUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . (isset($parsedUrl['path']) ? $parsedUrl['path'] : '');
		return $cleanUrl;
	}
	function getImageAttributes($imgnn,$imgnn_arr,$img_size){
		if(empty($imgnn_arr['width']) || $imgnn_arr['width'] == 'auto' || $imgnn_arr['width'] == '100%'){
			$imgnn = str_replace(array(' width="auto"',' src='),array('',' width="'.$img_size[0].'" src='),$imgnn);
		}
		if(empty($imgnn_arr['height']) || $imgnn_arr['height'] == 'auto' || $imgnn_arr['height'] == '100%'){
			$imgnn = str_replace(array(' height="auto"',' src='),array('',' height="'.$img_size[1].'" src='),$imgnn);
		}
		if(!empty($this->settings['aspect_ratio_img'])){
			if(strpos($imgnn,'style=') !== false){
				$imgnn = str_replace(array(' style="'," style='"),array(' style="aspect-ratio:'.$img_size[0].'/'.$img_size[1].';'," style='aspect-ratio:".$img_size[0]."/".$img_size[1].";"),$imgnn);
			}else{
				$imgnn = str_replace(' src=',' style="aspect-ratio:'.$img_size[0].'/'.$img_size[1].'" src=',$imgnn);
			}
		}
		return $imgnn;
	}
	function w3debug($text){
		if(!empty($_REQUEST['tester'])){
			$this->html .= $text;
		}
	}
	function w3GetImageSize($img,$path){
		$img_size = array();
		$w3_img_ext = '.'.pathinfo($path, PATHINFO_EXTENSION);
		if($w3_img_ext == '.svg'){
			list($img_size[0], $img_size[1],$alt) = $this->getSvgAttributes($img);
		}else{
			$img_size = strlen($path) < 4097 && file_exists($path) ? @getimagesize($path) : array();
		}
		return $img_size;
	}
	function getImgRootPath($src){
		if(strpos($src,$this->add_settings['theme_root']) !== false){
			$img_root_path = rtrim($this->add_settings['theme_base_dir'],'/');
			$img_root_url = rtrim($this->add_settings['theme_base_url'],'/');
		}else{
			$img_root_path = $this->add_settings['upload_base_dir'];
			$img_root_url = $this->add_settings['upload_base_url'];
		}
		return array($img_root_path,$img_root_url);
	}
	function getResourceRootPath($src,$img_root_url){
		if($this->add_settings['isMultisiteSubDomain']){
			$src = str_replace($this->add_settings['site_url'],$this->add_settings['network_site_url'],$src);
		}
		return str_replace($img_root_url,'',$src);
	}
	function getBlankImageUrl($img_size){
		if(!empty($img_size[0]) && !empty($img_size[1])){
			$blank_image = '/blank-'.(int)$img_size[0].'x'.(int)$img_size[1].'.txt';
			if(!file_exists($this->add_settings['root_cache_path'].'/images'.$blank_image)){
				$this->createBlankDataImage((int)$img_size[0],(int)$img_size[1]);
			}
			$blank_image_url = $this->w3speedsterGetContents($this->add_settings['root_cache_path'].'/images'.$blank_image);		
		}else{
			$blank_image_url = $this->add_settings['blank_image_url'];
		}
		return $blank_image_url;
	}
	function getSvgXml($data){
		if(strpos($data,'<svg') !== false){
			return simplexml_load_string($data);
		}elseif(file_exists($data)){
			return simplexml_load_file($data);
		}else{
			return array();
		}
	}
	function getSvgAttributes($content){
		$svg = $this->getSvgXml(html_entity_decode($content));
		if(!empty($svg['width'])){
			if(strpos($svg['width'],'em') !== false){
				$width = (int)$svg['width'] * 16;
			}else{
				$width = (int)$svg['width'];
			}
		}else{
			$width = 'auto';
		}
		if(!empty($svg['height'])){
			if(strpos($svg['height'],'em') !== false){
				$height = (int)$svg['height'] * 16;
			}else{
				$height = (int)$svg['height'];
			}
		}
		else{
			$height = 'auto';
		}
		return array($width,$height,$this->getSvgTitle($svg));
	}
	function getSvgTitle($svg){
		return (!empty($svg->title) ? (string)$svg->title : '');
	}
	function checkImageExcluded($img){
		$exclude_image = 0;
		if($this->settings['lazy_load']){
			foreach( $this->add_settings['excludedImg'] as $ex_img ){
				if(!empty($ex_img) && strpos($img,$ex_img)!==false){
					$exclude_image = 1;
				}
			}
			if(!empty($imgnn_arr['data-class']) && strpos($imgnn_arr['data-class'],'LazyLoad') !== false){
				$exclude_image = 1;
			}
		}else{
			$exclude_image = 1;
		}
		
		if(!empty($this->settings['hook_exclude_image_to_lazyload'])){
			$code = str_replace(array('$exclude_image','$img','$imgnn_arr'),array('$args[0]','$args[1]','$args[2]'),$this->settings['hook_exclude_image_to_lazyload']);
			$exclude_image = $this->hookCallbackFunction($code,$exclude_image,$img, $imgnn_arr);
		}
		if(function_exists('w3speedup_image_exclude_lazyload')){
			$exclude_image = w3speedup_image_exclude_lazyload($exclude_image,$img, $imgnn_arr);
		}
		return $exclude_image;
	}
	function convertSVGsToFile($svgs){
		$convertedSvg = [];
		foreach($svgs as $svg){
			$path = $this->add_settings['root_cache_path'].'/images/';
			$filename = md5($svg).'.svg';
			if(!in_array($filename,$convertedSvg)){
				$convertedSvg[] = $filename;
			}else{
				continue;
			}
			if($this->checkImageExcluded($svg)){
				continue;
			}
			if(!file_exists($path.$filename)){
				 $filePath = $this->createSVGImageFile($path.$filename,$svg);
			}
			if(file_exists($path.$filename)){
				$newSvgArr = array();
				$newSvg = $svg;
				$newSvgArr['data-src'] = $this->add_settings['cache_url'].'/images/'.$filename;
				list($newSvgArr['width'],$newSvgArr['height'],$newSvgArr['alt']) = $this->getSvgAttributes($svg);
				$newSvgArr['src'] = $this->getBlankImageUrl(array($newSvgArr['width'],$newSvgArr['height'])); 
				$newSvgArr['data-class'] = 'LazyLoad';
				$this->w3StrReplaceSetImg($svg,$this->w3ImplodeLinkArray('img',$newSvgArr));
			}
			
		}		
	}
    function lazyload($all_links){
		$this->lazyLoadIframe($all_links['iframe']);
        $this->lazyLoadVideo($all_links['video']);
		$this->lazyLoadAudio($all_links['audio']);
		$this->lazyLoadImg($all_links['picture'],$all_links['img']);
        if(!empty($all_links['svg'])){
			$this->convertSVGsToFile($all_links['svg']);
		}
        $this->html = $this->w3ConvertArrRelativeToAbsolute($this->html, $this->add_settings['home_url'].'/index.php',$all_links['url']);
    }
	function lazyLoadBackgroundImage(){
		$elements = array('<div ', '<section ', '<iframelazy ', '<iframe ');
		$Repelements = array('<div data-BgLz="1" ', '<section data-BgLz="1" ', '<iframelazy data-BgLz="1" ', '<iframe data-BgLz="1" ');
		$this->html = str_replace($elements,$Repelements,$this->html);
	}
	function W3SpeedsterCoreWebVitalsScript() {
		$script_content = '<script>
			(function () {
				var adminAjax = \''.esc_url( admin_url( 'admin-ajax.php' ) ).'\';
				var device = /Mobi|Android/i.test(navigator.userAgent) ? "Mobile" : "Desktop" ;
				var script = document.createElement(\'script\');
				script.src = \'' . esc_url( plugins_url( 'assets/js/web-vitals.iife.js', dirname(__FILE__) ) ) . '\';
				script.onload = function () {
					webVitals.onCLS(handleVitalsCLS);
					webVitals.onFID(handleVitalsFID);
					webVitals.onLCP(handleVitalsLCP);
					webVitals.onINP(handleVitalsINP);
				};
				document.head.appendChild(script);
			
	
				function handleVitalsFID(metric) {
					console.log(metric);
				   if(metric.rating != \'good\'){
						var metricString = JSON.stringify(metric);
						var metricObject = JSON.parse(metricString);
						var index = 0;
						metric.entries.forEach(() => {
							 metricObject.entries[index].targetElement = metric.entries[index].target.className;
							index++;
						});
						var lastString = JSON.stringify(metricObject);
						w3Ajax(lastString,\'LCP\');
					}
				}
		
				function handleVitalsCLS(metric) {
				   console.log(metric);
				   if(metric.rating != \'good\'){
					var metricString = JSON.stringify(metric);
					var metricObject = JSON.parse(metricString);
					metric.entries.forEach((e,i) => {
						e.sources.forEach((j,k) => {
							metricObject.entries[i].sources[k].targetElement = j.node["className"];
						});
					});
					var lastString = JSON.stringify(metricObject);
					w3Ajax(lastString,\'CLS\');
				  }
				}
		
				function handleVitalsLCP(metric) {
					console.log(metric);
					if(metric.rating != \'good\'){
						var metricString = JSON.stringify(metric); // Serialize the metric object
						var metricObject = JSON.parse(metricString);
						var index = 0;
						metric.entries.forEach(() => {
							 metricObject.entries[index].targetElement = metric.entries[index].element.className;
							index++;
						});
						var lastString = JSON.stringify(metricObject);
						w3Ajax(lastString,\'LCP\');
					}
				} 
				function w3Ajax(lastString, issueType) {
					var xhr = new XMLHttpRequest();
					var url = adminAjax;  // Assuming `adminAjax` is defined elsewhere

					xhr.open(\'POST\', url, true);
					xhr.setRequestHeader(\'Content-Type\', \'application/x-www-form-urlencoded\');

					// Create the data string in the URL-encoded format
					var data = \'action=w3speedsterPutData\' +
							   \'&data=\' + encodeURIComponent(lastString) +
							   \'&url=\' + encodeURIComponent(window.location.href) +
							   \'&issueType=\' + encodeURIComponent(issueType) +
							   \'&deviceType=\' + encodeURIComponent(device);  // Assuming `device` is defined elsewhere

					xhr.onreadystatechange = function() {
						if (xhr.readyState === XMLHttpRequest.DONE) {
							if (xhr.status === 200) {
								console.log(\'data inserted\');
							} else {
								console.log(xhr.statusText);
							}
						}
					};

					xhr.onerror = function() {
						console.log(xhr.statusText);
					};

					xhr.send(data);
				}
				function handleVitalsINP(metric) {
					console.log(metric);
					if(metric.rating != \'good\'){
						var metricString = JSON.stringify(metric); 
						var metricObject = JSON.parse(metricString);
						var index = 0;
						metric.entries.forEach(() => {
							 metricObject.entries[index].targetElement = metric.entries[index].target.className;
							index++;
						});
						var lastString = JSON.stringify(metricObject);
						w3Ajax(lastString,\'LCP\');
					}
				}
			})();
		</script>';
		$webVitalspath = $this->w3GetCachePath('all-js').'/webvital.js';
        if(!is_file($webVitalspath)){
            $this->w3CreateFile($webVitalspath,$this->w3CompressJs($script_content));
        }
		return $this->w3speedsterGetContents($webVitalspath);;
	}
	function w3CriticalCssDetails(){
		$preload_total = (int)$this->w3GetOption('w3speedup_preload_css_total');
		$que = count((array)$this->w3GetOption('w3speedup_preload_css'));
		$preload_created = $preload_total - ($que > 0 ? $que : 0);	
		$preload_created = $preload_created < 0 ? 0 : $preload_created;
		return array($preload_total,$preload_created);
	}
	function w3Wget($url){
        $command = "wget -qO- --timeout=10 \"".$url."\"";
        $output = [];
        $return_var = 0;
        exec($command, $output, $return_var);
        if ($return_var === 0) {
            $response = implode("\n", $output);
            return $response;
        }else{
            return false;
        }
    }
}