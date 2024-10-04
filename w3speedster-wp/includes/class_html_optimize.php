<?php
namespace W3speedster;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class w3speed_html_optimize extends w3speedster_js{
	
	function w3Speedster($html){
		$this->html = $html;
		$this->w3DebugTime('start optimization');
		if(function_exists('w3speedup_pre_start_optimization')){
            $this->html = w3speedup_pre_start_optimization($this->html);
        }
		if(!empty($this->settings['hook_pre_start_opt'])){
			$code = str_replace('$html','$args[0]',$this->settings['hook_pre_start_opt']);
            $this->html = $this->hookCallbackFunction($code,$html);
        }
        $upload_dir = wp_upload_dir();
		if(!file_exists($upload_dir['basedir'].'/w3test.html') && !empty($this->html)){
			$this->w3speedsterPutContents($upload_dir['basedir'].'/w3test.html',$this->html);
		}
		
        if($this->w3NoOptimization()){
			if(isset($this->settings['html_caching']) && $this->settings['html_caching'] == "on"){
				$this->html = $this->w3SpeedsterCreateHTMLCacheFile($this->html);
			}
            return $this->html;
        }
		
		if(!empty($this->settings['hook_customize_add_settings'])){
			$add_settings = $this->add_settings;
			$code = str_replace('$add_settings','$args[0]',$this->settings['hook_customize_add_settings']);
			$this->add_settings = $this->hookCallbackFunction($code,$this->add_settings);
		}
		if(function_exists('w3speedup_customize_add_settings')){
			$this->add_settings = w3speedup_customize_add_settings($this->add_settings);
		}
		
		if(function_exists('w3speedup_customize_main_settings')){
			$this->settings = w3speedup_customize_main_settings($this->settings);
		}
		
		if(!empty($this->settings['hook_customize_main_settings'])){
			$settings = $this->settings;
			$code = str_replace('$settings','$args[0]',$this->settings['hook_customize_main_settings']);
			$this->add_settings = $this->hookCallbackFunction($code,$this->add_settings);
		}
		$this->add_settings['disable_htaccess_webp'] = function_exists('w3_disable_htaccess_wepb') ? w3_disable_htaccess_wepb() : 0;

		if(!empty($this->settings['hook_disable_htaccess_webp'])){
			$disable_htaccess_webp = $this->add_settings['disable_htaccess_webp'];
			$code = str_replace(array('$disable_htaccess_webp'),array('$args[0]'),$this->settings['hook_disable_htaccess_webp']);
			$this->add_settings['disable_htaccess_webp'] = $this->hookCallbackFunction($code,$disable_htaccess_webp);
		}
		if(!empty($this->settings['js'])){
			$this-> w3CustomJsEnqueue();
		}
        //$this->html = str_replace(array('<script type="text/javascript"',"<script type='text/javascript'",'<style type="text/css"',"<style type='text/css'"),array('<script','<script','<style','<style'),$this->html);
        if(function_exists('w3speedup_before_start_optimization')){
            $this->html = w3speedup_before_start_optimization($this->html);
        }
		if(!empty($this->settings['hook_before_start_opt'])){
			$code = str_replace('$html','$args[0]',$this->settings['hook_before_start_opt']);
            $this->html = $this->hookCallbackFunction($code,$this->html);
        }
        
        $js_json_exists = 0;
        /*if(file_exists($file = $this->w3GetFullUrlCachePath().'/js.json')){
            $rep_js = json_decode(file_get_contents($file));
            if(is_array($rep_js[0]) && is_array($rep_js[1])){
                $js_json_exists = 1;
                if(file_exists($file = $this->w3GetFullUrlCachePath().'/main_js.json')){
                    global $internal_js;
                    $internal_js = json_decode(file_get_contents($file));
                }
            }
        }*/
        $img_json_exists = 0;
        if(file_exists($file = $this->w3CheckFullUrlCachePath().'/img.json')){
            $rep_img = json_decode($this->w3speedsterGetContents($file));
            if(is_array($rep_img[0]) && is_array($rep_img[1])){
                $img_json_exists = 1;
            }
        }
        $rep_main_css = array();
        $css_json_exists = 0;
        if(file_exists($file = $this->w3CheckFullUrlCachePath().'/main_css.json')){
            $rep_main_css = json_decode($this->w3speedsterGetContents($file));
        }
		if(file_exists($file = $this->w3CheckFullUrlCachePath().'/css.json')){
            $rep_css = json_decode($this->w3speedsterGetContents($file));
            if(is_array($rep_css[0]) && is_array($rep_css[1])){
                $css_json_exists = 1;
            }
		}
        if(file_exists($file = $this->w3CheckFullUrlCachePath().'/content_head.json') && $css_json_exists){
            $rep_content_head = json_decode($this->w3speedsterGetContents($file));
            if(is_array($rep_content_head) && count($rep_content_head) > 0){
                $content_head_exists = 1;
            }else{
                $content_head_exists = 0;
            }
        }
		if($img_json_exists && $css_json_exists){
			$this->w3DebugTime('before create all links');
            $all_links = $this->w3SetAllLinks($this->html,array('script','link'));
			$this->w3DebugTime('after create all links');
            $this->minify($all_links['script']);
            $this->w3DebugTime('minify script');
            if(is_array($rep_content_head) && count($rep_content_head) > 0){
				for($i = 0; $i < count($rep_content_head); $i++){
					$this->w3InsertContentHead($rep_content_head[$i][0],$rep_content_head[$i][1]);
				}
			}
			$this->w3DebugTime('after replace json data');
            $this->w3StrReplaceBulk();
            $this->w3StrReplaceBulkJson(array_merge($rep_css[0],$rep_img[0]),array_merge($rep_css[1],$rep_img[1]));
        }else{
			$this->w3DebugTime('before create all links');
            $lazyload = array('script','link','img','url','picture');
			if(!empty($this->settings['inlineToUrlSVG'])){
				$lazyload[] = 'svg';
			}
			if(!empty($this->settings['lazy_load_iframe'])){
				$lazyload[] = 'iframe';
			}
			if(!empty($this->settings['lazy_load_video'])){
				$lazyload[] = 'video';
			}
			if(!empty($this->settings['lazy_load_audio'])){
				$lazyload[] = 'audio';
			}
            $all_links = $this->w3SetAllLinks($this->html,$lazyload);
			$this->w3DebugTime('after create all links');
            if(!empty($all_links['script'])){
				$this->minify($all_links['script']);
			}
			$this->w3DebugTime('minify script');
			$this->lazyload(array('iframe'=>$all_links['iframe'],'video'=>$all_links['video'],'audio'=>$all_links['audio'],'img'=>$all_links['img'],'picture'=>$all_links['picture'],'url'=>$all_links['url'], 'svg'=>$all_links['svg'] ) );
			$this->w3DebugTime('lazyload images');
            $this->minifyCss($all_links['link']);
			$this->w3DebugTime('minify css');
            if(!empty($this->settings['load_style_tag_in_head'])){
				$this->loadStyleTagInHead($all_links['style']);
			}
			$this->w3StrReplaceBulk();
            //$this->w3StrReplaceBulkImg();
            //$this->w3StrReplaceBulkCss();
            $this->w3DebugTime('replace json');
			$this->w3InsertContentHead('<script>'.$this->w3LazyLoadJavascript().'</script>',3);
			$this->w3InsertContentHead($this->w3LoadGoogleFonts(),3);
			$this->w3InsertContentHeadInJson();
			$this->w3DebugTime('after javascript insertion');
		}
		
		if(!$this->checkIgnoreCriticalCss()){
			if(!empty($this->add_settings['wp_get']['w3_get_css_post_type'])){
				$this->html .= 'rocket22'.$this->w3PreloadCssPath().'--'.$this->add_settings['critical_css'].'--'.file_exists($this->w3PreloadCssPath().'/'.$this->add_settings['critical_css']);
			}
			if(!empty($this->settings['load_critical_css'])){
				if(!file_exists($this->w3PreloadCssPath().'/'.$this->add_settings['critical_css'])){
					$this->w3AddPageCriticalCss();
				}else{
					$critical_css = $this->w3speedsterGetContents($this->w3PreloadCssPath().'/'.$this->add_settings['critical_css']);
					if(!empty($critical_css)){
						$this->w3InsertContentHead('{{main_w3_critical_css}}',3);
						if(function_exists('w3speedup_customize_critical_css')){
							$critical_css = w3speedup_customize_critical_css($critical_css);
						}
						
						if(!empty($this->settings['hook_customize_critical_css'])){
							$code = str_replace(array('$critical_css'),array('$args[0]'),$this->settings['hook_customize_critical_css']);
							$critical_css = $this->hookCallbackFunction($code,$critical_css);
						}
						
						if(!empty($this->settings['load_critical_css_style_tag'])){
							$this->html = str_replace(array('data-css="1" ','{{main_w3_critical_css}}'),array('data-','<style id="w3speedster-critical-css">'.$critical_css.'</style>'),$this->html);
							$this->add_settings['preload_resources']['critical_css'] = 1;
						}else{
							$enableCdnCss = 0;
							if($this->w3CheckEnableCdnExt('.css')){
								$upload_dir['baseurl'] = str_replace($this->add_settings['wp_site_url'],$this->add_settings['image_home_url'],$upload_dir['baseurl']);
								$enableCdnCss = 1;
							}
							$critical_css_url = str_replace($this->add_settings['wp_document_root'],($enableCdnCss ? $this->add_settings['image_home_url'] :$this->add_settings['wp_site_url']),$this->w3PreloadCssPath().'/'.$this->add_settings['critical_css']);
							$this->html = str_replace(array('data-css="1" ','{{main_w3_critical_css}}'),array('data-','<link rel="stylesheet" href="'.$critical_css_url.'"/>'),$this->html);
							$this->add_settings['preload_resources']['critical_css'] = $critical_css_url;
						}
					}else{
						$this->w3AddPageCriticalCss();
					}
				}
			}
		}
		$preload_html = $this->w3PreloadResources();
		$this->w3InsertContentHead($preload_html,3);
		if(!empty($this->settings['webvitals_logs'])){
		$this->w3InsertContentHead($this->W3SpeedsterCoreWebVitalsScript(),4);
		}
        $position = strrpos($this->html,'</body>');
		$this->html = substr_replace( $this->html, '<script>'.$this->w3LazyLoadImages().'</script>', $position, 0 );
		$this->w3DebugTime('w3 script');
		
        if(function_exists('w3speedup_after_optimization')){
            $this->html = w3speedup_after_optimization($this->html);
        }
		if(!empty($this->settings['hook_after_opt'])){
			$code = str_replace('$html','$args[0]',$this->settings['hook_after_opt']);
            $this->html = $this->hookCallbackFunction($code,$this->html);
        }
		
		if(isset($this->settings['html_caching']) && $this->settings['html_caching'] == "on"){
			$this->html = $this->w3SpeedsterCreateHTMLCacheFile($this->html);
		}
		
		$this->w3DebugTime('before final output');
        return $this->html;
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
			$preload_css = w3GetOption('w3speedup_preload_css');
			$preload_css = (empty($preload_css) || !is_array($preload_css)) ? array() : $preload_css;
			if(is_array($preload_css) && count($preload_css) > 50){
				return;
			}
			if(!is_array($preload_css) || (is_array($preload_css) && !array_key_exists(base64_encode($this->add_settings['full_url_without_param']),$preload_css)) || (!empty($preload_css[$this->add_settings['full_url_without_param']]) && $preload_css[$this->add_settings['full_url_without_param']][0] != $this->add_settings['critical_css']) ){
				$preload_css[base64_encode($this->add_settings['full_url_without_param'])] = array($this->add_settings['critical_css'],2,$this->w3PreloadCssPath());
				w3UpdateOption('w3speedup_preload_css',$preload_css,'no');
				w3UpdateOption('w3speedup_preload_css_total',(int)w3GetOption('w3speedup_preload_css_total')+1,'no');
				if (!wp_next_scheduled('w3speedup_preload_css_min')) {
					wp_schedule_event(time(), 'w3speedster_every_minute', 'w3speedup_preload_css_min');
				}
				return serialize(w3GetOption('w3speedup_preload_css'));
			}
		}
	}
	public function w3HeaderCheck() {
        return is_admin()
			|| $this->isSpecialContentType()
	    	|| $this->isSpecialRoute()
	    	|| $_SERVER['REQUEST_METHOD'] === 'POST'
	    	|| $_SERVER['REQUEST_METHOD'] === 'PUT'
			|| $_SERVER['REQUEST_METHOD'] === 'DELETE'
			|| is_404();
	}

   private function isSpecialContentType() {
		if($this->w3Endswith($this->add_settings['full_url'],'.xml') || $this->w3Endswith($this->add_settings['full_url'],'.xsl')){
        	return true;
        }

		return false;
    }

    private function isSpecialRoute() {
		$current_url = $this->add_settings['full_url'];

		if( preg_match('/(.*\/wp\/v2\/.*)/', $current_url) ) {
			return true;
		}

		if( preg_match('/(.*wp-login.*)/', $current_url) ) {
			return true;
		}

		if( preg_match('/(.*wp-admin.*)/', $current_url) ) {
			return true;
		}

		return false;
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
    function w3NoOptimization(){
        if(!empty($this->add_settings['wp_get']['orgurl']) || strpos($this->html,'<body') === false){
            return true;
        }
        if (function_exists( 'is_amp_endpoint' ) && is_amp_endpoint()) {
            return true;
        }
		if($this->w3HeaderCheck()){
			return true;
		}
        if(empty($this->settings['optimization_on']) && empty($this->add_settings['wp_get']['w3_get_css_post_type']) && empty($this->add_settings['wp_get']['tester']) && empty($this->add_settings['wp_get']['testing'])){
             return true;
        }
		if(function_exists('w3speedup_exclude_page_optimization')){
            if(w3speedup_exclude_page_optimization($this->html)){
				return true;
			}
        }
		
		
		if(!empty($this->settings['hook_exclude_page_optimization'])){
			$exclude_page_optimization = 0;
			$code = str_replace(array('$exclude_page_optimization','$html'),array('$args[0]','$args[1]'),$this->settings['hook_exclude_page_optimization']);
			$exclude_page_optimization = $this->hookCallbackFunction($code,$exclude_page_optimization,$this->html);
			if($exclude_page_optimization){
				return true;
			}
		}
		
		if(empty($this->settings['optimize_user_logged_in']) && function_exists('is_user_logged_in') && is_user_logged_in()){
			return true;
		}
		if(empty($this->settings['optimize_query_parameters']) && $this->add_settings['full_url'] != $this->add_settings['full_url_without_param'] && empty($this->add_settings['wp_get']['tester'])){
			return true;
		}
        if(!empty($this->settings['exclude_pages_from_optimization']) && $this->w3CheckIfPageExcluded($this->settings['exclude_pages_from_optimization'])){
            return true;
        }
        global $current_user;
        if((empty($this->add_settings['wp_get']['testing']) && is_404()) || strpos($this->html,'<title>Page Not Found') !== false || (!empty($current_user) && current_user_can('edit_others_pages')) ){
            return true;
        }
        return false;
    }
    
    function w3StartOptimizationCallback(){
        ob_start(array($this,"w3Speedster") );
		//add_action( 'shutdown', array($this,'w3ObEndFlush'));
        //register_shutdown_function(array($this,'w3ObEndFlush') );
    }
    
    function w3ObEndFlush() {
    
        if (ob_get_level() != 0) {
    
            ob_end_flush();
    
         }
    
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
			$file_name = get_option('w3_rand_key');
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
		return '<script>var w3_googlefont='.wp_json_encode($google_font).';</script>';
	}
    function lazyload($all_links){
		$upload_dir = wp_upload_dir();
        if(!empty($this->settings['lazy_load_iframe'])){
            $iframe_links = $all_links['iframe'];
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
        if(!empty($this->settings['lazy_load_video'])){
            $iframe_links = $all_links['video'];
			if(strpos($this->add_settings['upload_base_url'],$this->add_settings['wp_site_url']) !== false){
				$v_src = $this->add_settings['image_home_url'].str_replace($this->add_settings['wp_site_url'],'',$this->add_settings['upload_base_url']).'/blank.mp4';
			}else{
				$v_src = $this->add_settings['upload_base_url'].'/blank.mp4';
			}
            foreach($iframe_links as $img){
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
		if(!empty($this->settings['lazy_load_audio'])){
            $iframe_links = $all_links['audio'];
			if(strpos($this->add_settings['upload_base_url'],$this->add_settings['wp_site_url']) !== false){
				$v_src = $this->add_settings['image_home_url'].str_replace($this->add_settings['wp_site_url'],'',$this->add_settings['upload_base_url']).'/blank.mp3';
			}else{
				$v_src = $this->add_settings['upload_base_url'].'/blank.mp3';
			}
            foreach($iframe_links as $img){
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
        $picture_links = $all_links['picture'];
        if(!empty($picture_links)){
			$exclude_cdn_arr = !empty($this->add_settings['exclude_cdn']) ? $this->add_settings['exclude_cdn'] : array();
			foreach($picture_links as $img){
				$blank_image_url = ($this->add_settings['enable_cdn'] && !in_array('.png',$exclude_cdn_arr)) ? str_replace($this->add_settings['wp_site_url'],$this->add_settings['image_home_url'],$this->add_settings['upload_base_url']) : $this->add_settings['upload_base_url'];
				$imgnn = $img;
				if($this->checkImageExcluded($img)){
                    continue;
                }
				$imgTag = $this->w3GetTagsData($img, '<img', '>');
				if(is_array($imgTag) && count($imgTag) > 0){
					$imgArr = $this->w3ParseLink('img',str_replace($this->add_settings['image_home_url'],$this->add_settings['wp_site_url'],$imgTag[0]));
					if(!$this->w3IsExternal($imgArr['src'])){
						list($img_root_path,$img_root_url) = $this->getImgRootPath($imgArr['src']);
						$imgsrc_filepath = $this->getResourceRootPath($imgArr['src'],$img_root_url);
						$img_size = $this->w3GetImageSize($imgTag,$img_root_path.$imgsrc_filepath);
						$blank_image_url = $this->getBlankImageUrl($img_size);
					}else{
						$blank_image_url = $this->getBlankImageUrl(array($imgArr['width'],$imgArr['height']));
					}
					
					$imgTagN = str_replace(array(' src=',' srcset="'),array(' src="'.$blank_image_url.'" data-src=',' data-srcset="'),$imgTag[0]); 
					$imgnn = str_replace(array('<picture ', $imgTag[0], ' srcset="'),array('<picture data-class="LazyLoad" ',$imgTagN,' data-srcset="'),$imgnn);
					$this->w3StrReplaceSetImg($img,$imgnn);
				}
				
				
				
			}
		}
		$img_links = $all_links['img'];
        if(!empty($all_links['img'])){
			$exclude_cdn_arr = !empty($this->add_settings['exclude_cdn']) ? $this->add_settings['exclude_cdn'] : array();
			$webp_enable = $this->add_settings['webp_enable'];
			$webp_enable_instance = $this->add_settings['webp_enable_instance'];
			$webp_enable_instance_replace = $this->add_settings['webp_enable_instance_replace'];
			foreach($img_links as $img){
				$imgnn = $img;
				$imgnn_arr = $this->w3ParseLink('img',str_replace($this->add_settings['image_home_url'],$this->add_settings['wp_site_url'],$imgnn));
				if(empty($imgnn_arr['src'])){
					continue;
				}
				if(strpos($imgnn_arr['src'],'\\') !== false){
					continue;
				}
				$imgnn_arr['src'] = urldecode($imgnn_arr['src']);
				if(!$this->w3IsExternal($imgnn_arr['src'])){
					list($img_root_path,$img_root_url) = $this->getImgRootPath($imgnn_arr['src']);
					$w3_img_ext = '.'.pathinfo($imgnn_arr['src'], PATHINFO_EXTENSION);
					$imgsrc_filepath = $this->getResourceRootPath($imgnn_arr['src'],$img_root_url);
					$imgsrc_webpfilepath = str_replace($this->add_settings['upload_path'],$this->add_settings['webp_path'],$img_root_path).$imgsrc_filepath.'w3.webp';
					if($this->add_settings['enable_cdn']){
						$image_home_url = $this->add_settings['image_home_url'];
						foreach($exclude_cdn_arr as $cdn){
							if(strpos($img,$cdn) !== false){
								$image_home_url = $this->add_settings['wp_site_url'];
								break;
							}
						}
						$imgnn = str_replace($this->add_settings['wp_site_url'],$image_home_url,$imgnn);
					}else{
						$image_home_url = $this->add_settings['wp_site_url'];
					}
					$imgnn = trim(preg_replace('/\s+/', ' ', $imgnn));
					$img_size = $this->w3GetImageSize($img,$img_root_path.$imgsrc_filepath);
					if(!empty($img_size[0]) && !empty($img_size[1])){
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
					}
					if(strpos($img, ' srcset=') === false && !empty($this->settings['resp_bg_img'])){
						if(!empty($img_size[0]) && $img_size[0] > 600){
							$w3_thumbnail = rtrim(str_replace($w3_img_ext.'$','-595xh'.$w3_img_ext.'$',$imgsrc_filepath.'$'),'$');
							if(in_array($w3_img_ext, $webp_enable) && !file_exists($this->add_settings['wp_document_root'].$w3_thumbnail) && !empty($this->settings['opt_img_on_the_go'])){
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
						if(file_exists($imgsrc_webpfilepath) && (!empty($this->add_settings['disable_htaccess_webp']) || !file_exists($this->add_settings['wp_document_root']."/.htaccess") || $this->add_settings['image_home_url'] != $this->add_settings['wp_site_url'] ) ){
							
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
		if(!empty($all_links['svg'])){
			$this->convertSVGsToFile($all_links['svg']);
		}
        $this->html = $this->w3ConvertArrRelativeToAbsolute($this->html, $this->add_settings['wp_home_url'].'/index.php',$all_links['url']);
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
		$img_url_arr = wp_parse_url($src);
		return str_replace($img_root_url,'',$this->add_settings['home_url'].$img_url_arr['path']);
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
		}else{
			return simplexml_load_file($data);
		}
	}
	function getSvgAttributes($content){
		$svg = $this->getSvgXml($content);
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
	function calculateHCF($num1, $num2) {
		while ($num2 != 0) {
			$temp = $num2;
			$num2 = $num1 % $num2;
			$num1 = $temp;
		}
		return $num1;
	}
	function w3IncrementPrioritizedImg($attach_id=''){
		$opt_priority = w3GetOption('w3speedup_opt_priortize');
		if(empty($opt_priority) || !is_array($opt_priority)){
			$opt_priority = array();
		}
		if(is_array($opt_priority) && count($opt_priority) > 50){
			return true;
		}
		if(empty($opt_priority) || !in_array($attach_id,$opt_priority)){
			$opt_priority[] = $attach_id;
		}
		w3UpdateOption('w3speedup_opt_priortize',$opt_priority,'no');
		return true;
	}
	function w3OptimizeAttachmentUrl($path){
		global $wpdb;
		if(strpos($path,'/themes/') !== false || strpos($path,'/plugins/') !== false){
			return $this->w3IncrementPrioritizedImg($path);
		}
		//$query = "SELECT ID FROM {$wpdb->prefix}posts WHERE post_type='attachment' AND guid like '%".$path."' limit 0,1";
		$attach_id = $wpdb->get_var($wpdb->prepare(
			"SELECT ID FROM {$wpdb->prefix}posts WHERE post_type = %s AND guid LIKE %s LIMIT 0, 1",
			'attachment',
			'%' . $wpdb->esc_like($path) . '%'
		));
		if(!empty($attach_id)){
			return $this->w3IncrementPrioritizedImg($attach_id);
		}else{
			$path_arr = explode('/',$path);
			$img = array_pop($path_arr);
			$attach_id = $wpdb->get_var($wpdb->prepare(
				"SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = '_wp_attachment_metadata' AND meta_value LIKE %s",
				'%' . $wpdb->esc_like( $img ) . '%'
			));
			if(!empty($attach_id)){
				return $this->w3IncrementPrioritizedImg($attach_id);
			}else{
				return $this->w3IncrementPrioritizedImg($path);
			}
		}
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

	function w3SpeedsterCheckCacheTrue(){
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
	
	function w3SpeedsterCreateHTMLCacheFile($html){
			$path = $this->add_settings['full_url'];
			$parsed_url = wp_parse_url($path);
			if (!empty($parsed_url['query'])) {
				if (strpos($parsed_url['query'],"orgurl=") !== false) {
					return $html;
				} 
			}
			$type = '';
			$mob_msg = '';
			$userAgent = $_SERVER["HTTP_USER_AGENT"];
			$isMobile = $this->w3speedsterIsMobileDevice($userAgent);
            $this->w3SpeedsterCheckCacheTrue();
            $file_content = $this->w3SpeedsterGetDataAdvancedCacheFile();
            $advanced_cache_file = WP_CONTENT_DIR . '/advanced-cache.php';
            $path = "$_SERVER[REQUEST_URI]";
			$currenturl = rtrim($this->add_settings['full_url'],'/');
			
			
			/*if(defined('WP_DEBUG') && WP_DEBUG){
					return $html;
			}*/
			$uri_exclusions = isset($this->settings['exclude_url_exclusions_html_cache']) ? explode("\r\n", $this->settings['exclude_url_exclusions_html_cache']) : array();
			$uri_exclusions = array_merge($uri_exclusions,array('login','/admin','/wp-admin','/wp-login','json','sitemap'));
			if(!empty($uri_exclusions)){
				foreach ($uri_exclusions as $element) {
					if (strpos($currenturl,$element) != false) {
						return $html;
					}
				}
			}
			$path1 = $parsed_url['path'];	
			$enableCachingGetPara = isset($this->settings['enable_caching_get_para']) ? 1 : 0;
			if($enableCachingGetPara == 0 && !empty($parsed_url['query'])){
				return $html;
			}elseif(!empty($enableCachingGetPara) && !empty($parsed_url['query'])){
				$path1 .= $parsed_url['query'].'/' ;
			}
			
			if(!empty($this->settings['html_caching_for_mobile']) && $isMobile){
				$type = '/w3mob';
				$mob_msg = 'Mobile';
				
			}
			if(!empty($this->settings['minify_html_cache'])){
				$html = preg_replace("/<\/html>\s+/", "</html>", $html);
				$html = str_replace("\r", "", $html);
				$html = preg_replace("/^\s+/m", "", ((string) $html));
			}
			if(strpos($html,'<html') !== false ){
				$fileName = $this->add_settings['root_cache_path'].'/html/'.$path1.''.$type.'/index.html';
				$endtime = $this->microtime_float();
				$current_time = date("Y-m-d H:i:s");
				if(!file_exists($fileName) || (file_exists($fileName) && (time() - filemtime($fileName)) > $this->settings['html_caching_expiry_time'])){
					$this->w3CreateFile($fileName, $html.'<!--'.$mob_msg.' Cache Created By W3speedster Pro at '.$current_time.' in '.number_format($endtime - $this->add_settings['starttime'],2).' secs-->');
				}
				return $html.'<!--'.$mob_msg.' Cache Created By W3speedster Pro at '.$current_time.' in '.number_format($endtime - $this->add_settings['starttime'],2).' secs-->';
			}
			return $html;
			
	}
	function w3speedsterPreloadCache($urls) {
		$processed_urls = get_transient('processed_urls') ?: [];

		foreach ($urls as $url) {

			// Determine cache path
			$cachePath = isset($this->add_settings['root_cache_path']) ? $this->add_settings['root_cache_path'] : str_replace('\\', '/', $this->add_settings['wp_content_path'] . '/cache/w3-cache');
			
			// Extract path components
			$pathComponents = explode("/", $url);
			array_pop($pathComponents);
			$path = implode("/", $pathComponents);
			$cachePath = $cachePath.'/html';
			// Build cache URLs
			$cacheUrl = $cachePath . $path . '/index.html';
			$cacheUrlMob = $cachePath . $path . '/mob/index.html';
			
			// Replace site URL in cache URLs
			$site_url = get_site_url();
			$cacheUrl = str_replace($site_url, "", $cacheUrl);
			$cacheUrlMob = str_replace($site_url, "", $cacheUrlMob);
			
			// Get cache expiry time
			$expiryTime = !empty($this->settings['html_caching_expiry_time']) ? $this->settings['html_caching_expiry_time'] : 43200;

			// Check cache file modification times
			$time1 = file_exists($cacheUrl) ? filemtime($cacheUrl) : 0;
			$time2 = file_exists($cacheUrlMob) ? filemtime($cacheUrlMob) : 0;
			
			if (time() - $time1 > $expiryTime) {
				if (file_exists($cacheUrl)) {
					
					wp_delete_file($cacheUrl);
				}
			}
			if (time() - $time2 > $expiryTime) {
				if (file_exists($cacheUrlMob)) {
					wp_delete_file($cacheUrlMob);
				}
			}

			// Preload the URL
			wp_remote_get($url);
			$processed_urls[] = $url;
		}

		// Store processed URLs in a transient
		set_transient('processed_urls', $processed_urls, DAY_IN_SECONDS);
	}

	function w3speedsterGetSitemapUrl($sitemap_url) {
		$urls = [];
		$response = wp_remote_get($sitemap_url);

		if (is_wp_error($response)) {
			return $urls;
		}

		$body = wp_remote_retrieve_body($response);
		$xml = simplexml_load_string($body);

		if ($xml === false) {
			echo 'Failed loading XML';
			return $urls;
		}

		if (isset($xml->sitemap)) {
			foreach ($xml->sitemap as $sitemap) {
				$sitemap_loc = (string) $sitemap->loc;
				$urls = array_merge($urls, $this->w3speedsterGetSitemapUrl($sitemap_loc));
			}
		} else {
			foreach ($xml->url as $url) {
				$urls[] = (string) $url->loc;
			}
		}

		return $urls;
	}

	function w3speedsterSetPreloadCache(){
	    if(!isset($this->settings['preload_caching']) || $this->settings['preload_caching']){
	        return true;
	    }
		$sitemap_url = site_url() . '/sitemap.xml';
		if(empty($sitemap_url)){
			return true;
		}
		$urls = $this->w3speedsterGetSitemapUrl($sitemap_url);
		$pages_per_minute = !empty($this->settings['preload_per_min']) ? $this->settings['preload_per_min'] : 4;
		//$pages_per_minute = 4;

		$transient_key = 'preload_cache_offset';
		$offset = get_transient($transient_key) ?: 0;
		$urls_to_preload = array_slice($urls, $offset, $pages_per_minute);

		$this->w3speedsterPreloadCache($urls_to_preload);

		$new_offset = $offset + count($urls_to_preload);
		if ($new_offset >= count($urls)) {
			$new_offset = 0;
		}

		set_transient($transient_key, $new_offset, MINUTE_IN_SECONDS);
	}
}