<?php
namespace W3speedster;

checkDirectCall();

class w3speed_html_optimize extends w3speedster_js{
	public $current_page_content_type = '';
	public $current_page_type = '';
	public $cacheFilePath = "";
	public $cacheMsg = "";
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
			if(!empty($this->settings['html_caching'])){
				$this->html = $this->w3SpeedsterCreateHTMLCacheFile($this->html);
			}
            return $this->html;
        }
		
		if(!empty($this->settings['hook_customize_addSettings'])){
			$addSettings = $this->addSettings;
			$code = str_replace('$addSettings','$args[0]',$this->settings['hook_customize_addSettings']);
			$this->addSettings = $this->hookCallbackFunction($code,$this->addSettings);
		}
		if(function_exists('w3speedup_customize_addSettings')){
			$this->addSettings = w3speedup_customize_addSettings($this->addSettings);
		}
		
		if(function_exists('w3speedup_customize_main_settings')){
			$this->settings = w3speedup_customize_main_settings($this->settings);
		}
		
		if(!empty($this->settings['hook_customize_main_settings'])){
			$settings = $this->settings;
			$code = str_replace('$settings','$args[0]',$this->settings['hook_customize_main_settings']);
			$this->addSettings = $this->hookCallbackFunction($code,$this->addSettings);
		}
		$this->addSettings['disable_htaccess_webp'] = function_exists('w3_disable_htaccess_wepb') ? w3_disable_htaccess_wepb() : 0;

		if(!empty($this->settings['hook_disable_htaccess_webp'])){
			$disable_htaccess_webp = $this->addSettings['disable_htaccess_webp'];
			$code = str_replace(array('$disable_htaccess_webp'),array('$args[0]'),$this->settings['hook_disable_htaccess_webp']);
			$this->addSettings['disable_htaccess_webp'] = $this->hookCallbackFunction($code,$disable_htaccess_webp);
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
		if(!empty($this->settings['lazy_load'])){
			$this->lazyLoadBackgroundImage();
		}
		if($img_json_exists && $css_json_exists){
			$this->w3DebugTime('before create all links');
            $all_links = $this->w3SetAllLinks($this->html,array('script','link'));
			$this->w3DebugTime('after create all links');
            $this->minify($all_links['script']);
            $this->w3DebugTime('minify script');
            if(is_array($rep_content_head) && count($rep_content_head) > 0){
				for($i = 0; $i < count($rep_content_head); $i++){
					$this->w3InsertContentHead($rep_content_head[$i][0],$rep_content_head[$i][1],$rep_content_head[$i][2]);
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
			$this->w3DebugTime('parse all links');
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
			$insertLink = $this->getW3contentsInsertLink($all_links);
			$google_fonts = $this->w3LoadGoogleFonts();
			$this->w3InsertContentHead($google_fonts,2,$insertLink);
			$this->w3InsertContentHead('<script>'.$this->w3LazyLoadJavascript().'</script>',2,$insertLink);
			$this->w3StrReplaceBulk();
            $this->w3DebugTime('replace json');
			$this->w3InsertContentHeadInJson();
			$this->w3DebugTime('after javascript insertion');
		}
		$criticalCssInsertion = '';
		$criticalReplace = [];
		if(!$this->checkIgnoreCriticalCss()){
			if(!empty($this->addSettings['w3_get']['w3_get_css_post_type'])){
				$this->html .= 'rocket22'.W3SPEEDSTER_PLUGIN_VERSION.str_replace($this->addSettings['documentRoot'],'',$this->w3PreloadCssPath()).'--'.$this->addSettings['critical_css'].'--'.file_exists($this->w3PreloadCssPath().'/'.$this->addSettings['critical_css']);
			}
			if(!empty($this->settings['load_critical_css'])){
				if(!file_exists($this->w3PreloadCssPath().'/'.$this->addSettings['critical_css']) || filesize($this->w3PreloadCssPath().'/'.$this->addSettings['critical_css']) < 10){
					@unlink($this->w3PreloadCssPath().'/'.$this->addSettings['critical_css']);
					$this->w3AddPageCriticalCss();
				}else{
					$critical_css = $this->w3speedsterGetContents($this->w3PreloadCssPath().'/'.$this->addSettings['critical_css']);
					if(!empty($critical_css)){
						//$this->w3InsertContentHead('{{main_w3_critical_css}}',2,$insertLink);
						$criticalCssInsertion = 1;
						if(function_exists('w3speedup_customize_critical_css')){
							$critical_css = w3speedup_customize_critical_css($critical_css);
						}
						if(!empty($this->settings['hook_customize_critical_css'])){
							$code = str_replace(array('$critical_css'),array('$args[0]'),$this->settings['hook_customize_critical_css']);
							$critical_css = $this->hookCallbackFunction($code,$critical_css);
						}
						if(!empty($this->settings['load_critical_css_style_tag'])){
							$criticalReplace[0] = array('data-css="1" ','{{main_w3_critical_css}}');
							$criticalReplace[1] = array('data-','<style id="w3speedster-critical-css">'.$critical_css.'</style>');
							$this->addSettings['preload_resources']['critical_css'] = 1;
						}else{
							$enableCdnCss = 0;
							if($this->w3CheckEnableCdnExt('.css')){
								$upload_dir['baseurl'] = str_replace($this->addSettings['siteUrl'],$this->addSettings['imageHomeUrl'],$upload_dir['baseurl']);
								$enableCdnCss = 1;
							}
							$critical_css_url = str_replace($this->addSettings['documentRoot'],($enableCdnCss ? $this->addSettings['imageHomeUrl'] :$this->addSettings['siteUrl']),$this->w3PreloadCssPath().'/'.$this->addSettings['critical_css']);
							$criticalReplace[0] = array('data-css="1" ','{{main_w3_critical_css}}');
							$criticalReplace[1] = array('data-','<link rel="stylesheet" href="'.$critical_css_url.'"/>');
							$this->addSettings['preload_resources']['critical_css'] = $critical_css_url;
						}
					}else{
						$this->w3AddPageCriticalCss();
					}
				}
			}
		}
		$preload_html = $this->w3PreloadResources();
		$this->w3InsertContentHead("\n".$preload_html,2,$google_fonts);
		if($criticalCssInsertion){
			$this->w3InsertContentHead("\n".'{{main_w3_critical_css}}',2,$google_fonts);
			$this->html = str_replace($criticalReplace[0],$criticalReplace[1],$this->html);
		}
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
		if($this->w3Endswith($this->addSettings['full_url'],'.xml') || $this->w3Endswith($this->addSettings['full_url'],'.xsl')){
        	return true;
        }

		return false;
    }

    private function isSpecialRoute() {
		$current_url = $this->addSettings['full_url'];

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
	
	
	public function set_current_page_content_type($html){
		$content_type = false;
		if(function_exists("headers_list")){
			$headers = headers_list();
			foreach($headers as $header){
				if(preg_match("/Content-Type\:/i", $header)){
					$content_type = preg_replace("/Content-Type\:\s(.+)/i", "$1", $header);
				}
			}
		}

		if(preg_match("/xml/i", $content_type)){
			$this->current_page_content_type = "xml";
		}else if(preg_match("/json/i", $content_type)){
			$this->current_page_content_type = "json";
		}else{
			$this->current_page_content_type = "html";
		}
	}
	public function detect_current_page_type(){
		if(preg_match("/\?/", $_SERVER["REQUEST_URI"])){
			return true;
		}
		
		if(preg_match("/^\/wp-json/", $_SERVER["REQUEST_URI"])){
			return true;
		}

		if(is_front_page()){
			echo "<!--W3_PAGE_TYPE_homepage-->";
		}else if(is_category()){
			echo "<!--W3_PAGE_TYPE_category-->";
		}else if(is_tag()){
			echo "<!--W3_PAGE_TYPE_tag-->";
		}else if(is_singular('post')){
			echo "<!--W3_PAGE_TYPE_post-->";
		}else if(is_page()){
			echo "<!--W3_PAGE_TYPE_page-->";
		}else if(is_attachment()){
			echo "<!--W3_PAGE_TYPE_attachment-->";
		}else if(is_archive()){
			echo "<!--W3_PAGE_TYPE_archive-->";
		}
	}
	public function set_current_page_type($html){
		preg_match('/<\!--W3_PAGE_TYPE_([a-z]+)-->/i', $html, $out);

		$this->current_page_type = isset($out[1]) ? $out[1] : false;
	}
	public function isCommenter(){
		$commenter = function_exists('wp_get_current_commenter') ? wp_get_current_commenter() : array();
		return isset($commenter["comment_author_email"]) && $commenter["comment_author_email"] ? true : false;
	}
	public function is_json(){
		return $this->current_page_content_type == "json" ? true : false;
	}
	public function isPasswordProtected($html){
		if(preg_match("/action\=[\'\"].+postpass.*[\'\"]/", $html)){
			return true;
		}

		foreach($_COOKIE as $key => $value){
			if(preg_match("/wp\-postpass\_/", $key)){
				return true;
			}
		}

		return false;
	}
	public function isWpLogin($html){
		
		if($GLOBALS["pagenow"] == "wp-login.php"){
			return true;
		}

		return false;
	}
	public function hasContactForm7WithCaptcha($html){
		if(is_single() || is_page()){
			if(preg_match("/<input[^\>]+_wpcf7_captcha[^\>]+>/i", $html)){
				return true;
			}
		}
		
		return false;
	}
	public function last_error($html = false){
		if(function_exists("http_response_code") && (http_response_code() === 404)){
			return true;
		}

		if(is_404()){
			return true;
		}

		if(preg_match("/<body id\=\"error-page\">\s*<p>[^\>]+<\/p>\s*<\/body>/i", $html)){
			return true;
		}
	}
	public function ignored($html){
		$list = array(
					"\/wp\-comments\-post\.php",
					"\/wp\-login\.php",
					"\/robots\.txt",
					"\/wp\-cron\.php",
					"\/wp\-content",
					"\/wp\-admin",
					"\/wp\-includes",
					"\/index\.php",
					"\/xmlrpc\.php",
					"\/wp\-api\/",
					"leaflet\-geojson\.php",
					"\/clientarea\.php"
				);
		if($this->isPluginActive('woocommerce/woocommerce.php')){
			if($this->current_page_type != "homepage"){
				global $post;

				if(isset($post->ID) && $post->ID){
					if(function_exists("wc_get_page_id")){
						$woocommerce_ids = array();

						//wc_get_page_id('product')
						//wc_get_page_id('product-category')
						
						array_push($woocommerce_ids, wc_get_page_id('cart'), wc_get_page_id('checkout'), wc_get_page_id('receipt'), wc_get_page_id('confirmation'), wc_get_page_id('myaccount'));

						if (in_array($post->ID, $woocommerce_ids)) {
							return true;
						}
					}
				}

				//"\/product"
				//"\/product-category"

				array_push($list, "\/cart\/?$", "\/checkout", "\/receipt", "\/confirmation", "\/wc-api\/");
			}
		}

		if($this->isPluginActive('wp-easycart/wpeasycart.php')){
			array_push($list, "\/cart");
		}

		if($this->isPluginActive('easy-digital-downloads/easy-digital-downloads.php')){
			array_push($list, "\/cart", "\/checkout");
		}

		if(preg_match("/".implode("|", $list)."/i", $_SERVER["REQUEST_URI"])){
			return true;
		}

		return false;
	}
	public function checkHtml($buffer){
		if(!$this->is_html()){
			return false;
		}

		if(preg_match('/<html[^\>]*>/si', $buffer) && preg_match('/<body[^\>]*>/si', $buffer)){
			return false;
		}
		// if(strlen($buffer) > 10){
		// 	return false;
		// }

		return true;
	}
	public function is_html(){
		return $this->current_page_content_type == "html" ? true : false;
	}
	public function w3NoHtmlCache($html){
		$this->set_current_page_type($html);
		$this->set_current_page_content_type($html);

		//$html = $this->checkShortCode($html);

		// for Wordfence: not to cache 503 pages
		if(defined('DONOTCACHEPAGE') && $this->isPluginActive('wordfence/wordfence.php')){
			if(function_exists("http_response_code") && http_response_code() == 503){
				return "<!-- DONOTCACHEPAGE is defined as TRUE -->";
			}
		}

		/*if($this->exclude_page($html)){
			$html = preg_replace('/<\!--W3_PAGE_TYPE_[a-z]+-->/i', '', $html);	
			return $html;
		}*/

		$uri_exclusions = isset($this->settings['exclude_url_exclusions_html_cache']) ? explode("\r\n", $this->settings['exclude_url_exclusions_html_cache']) : array();
		$uri_exclusions = array_merge($uri_exclusions,array('login','/admin','/wp-admin','/wp-login','json','sitemap'));
		if(!empty($uri_exclusions)){
			foreach ($uri_exclusions as $element) {
				if (strpos($this->addSettings['full_url'],$element) != false) {
					return true;
				}
			}
		}
		if($this->is_json() && (!defined('W3_CACHE_JSON') || (defined('W3_CACHE_JSON') && W3_CACHE_JSON !== true))){
			return true;
		}else if(preg_match("/Mediapartners-Google|Google\sWireless\sTranscoder/i", $this->addSettings['HTTP_USER_AGENT'])){
			return true;
		}else if (is_user_logged_in() || $this->isCommenter()){
			return true;
		}else if($this->isPasswordProtected($html)){
			return "<!-- Password protected content has been detected -->";
		}else if($this->isWpLogin($html)){
			return "<!-- wp-login.php -->";
		}else if($this->hasContactForm7WithCaptcha($html)){
			return "<!-- This page was not cached because ContactForm7's captcha -->";
		}else if($this->last_error($html)){
			return true;
		}else if($this->ignored($html)){
			return true;
		}else if(isset($_GET["preview"])){
			return "<!-- not cached -->";
		}else if($this->checkHtml($html)){
			return "<!-- html is corrupted -->";
		}else if((function_exists("http_response_code")) && (http_response_code() == 301 || http_response_code() == 302)){
			return true;
		}else if(!$this->cacheFilePath){
			return "<!-- permalink_structure ends with slash (/) but REQUEST_URI does not end with slash (/) -->";
		}else{
			return false;
			
		}
		return false;
	}
    function w3NoOptimization(){
        if(!empty($this->addSettings['w3_get']['orgurl']) || strpos($this->html,'<body') === false){
            return true;
        }
        if (function_exists( 'is_amp_endpoint' ) && is_amp_endpoint()) {
            return true;
        }
		if($this->w3HeaderCheck()){
			return true;
		}
        if(empty($this->settings['optimization_on']) && empty($this->addSettings['w3_get']['w3_get_css_post_type']) && empty($this->addSettings['w3_get']['tester']) && empty($this->addSettings['w3_get']['testing'])){
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
		if(empty($this->settings['optimize_query_parameters']) && $this->addSettings['full_url'] != $this->addSettings['full_url_without_param'] && empty($this->addSettings['w3_get']['tester'])){
			return true;
		}
        if(!empty($this->settings['exclude_pages_from_optimization']) && $this->w3CheckIfPageExcluded($this->settings['exclude_pages_from_optimization'])){
            return true;
        }
        global $current_user;
        if((empty($this->addSettings['w3_get']['testing']) && is_404()) || strpos($this->html,'<title>Page Not Found') !== false || (!empty($current_user) && current_user_can('edit_others_pages')) ){
            return true;
        }
        return false;
    }
    
    function w3StartOptimizationCallback(){
		if(!empty($this->settings['html_caching'])){
			add_action('wp', array($this, "detect_current_page_type"));
			add_action('get_footer', array($this, "detect_current_page_type"));
			//add_action('get_footer', array($this, "wp_print_scripts_action"));
		}
        ob_start(array($this,"w3Speedster") );
		//add_action( 'shutdown', array($this,'w3ObEndFlush'));
        //register_shutdown_function(array($this,'w3ObEndFlush') );
    }
    
    function w3ObEndFlush() {
    
        if (ob_get_level() != 0) {
    
            ob_end_flush();
    
         }
    
    }
	
	function w3IncrementPrioritizedImg($attach_id=''){
		$opt_priority = $this->w3GetOption('w3speedup_opt_priortize');
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
	
	public function set_cache_file_path(){
		$mob_msg = '';
		$userAgent = $this->addSettings['HTTP_USER_AGENT'];
		$type = "/html";
		$isMobile = $this->w3speedsterIsMobileDevice($userAgent);
		$enableCachingGetPara = isset($this->settings['enable_caching_get_para']) ? 1 : 0;
		$path = $this->addSettings['full_url'];
		$parsed_url = wp_parse_url($path);
		$cachePath = ltrim($parsed_url['path'],'/');
		if(!empty($this->settings['html_caching_for_mobile']) && $isMobile){
			$type .= '/w3mob';
			$mob_msg = 'Mobile';
		}
		if(!empty($enableCachingGetPara) && !empty($parsed_url['query'])){
			$cachePath .= $parsed_url['query'];
		}
		//$fileName = $this->addSettings['rootCachePath'].$type.$path1.'/index.html';
		if($this->isPluginActive('gtranslate/gtranslate.php')){
			if(isset($_SERVER["HTTP_X_GT_LANG"])){
				$this->cacheFilePath = $this->addSettings['rootCachePath'].$type."/".$_SERVER["HTTP_X_GT_LANG"].$cachePath;
			}else if(isset($_SERVER["REDIRECT_URL"]) && $_SERVER["REDIRECT_URL"] != "/index.php"){
				$this->cacheFilePath = $this->addSettings['rootCachePath'].$type."/".$_SERVER["REDIRECT_URL"];
			}else if(isset($_SERVER["REQUEST_URI"])){
				$this->cacheFilePath = $this->addSettings['rootCachePath'].$type."/".$cachePath;
			}
		}else{
			$this->cacheFilePath = $this->addSettings['rootCachePath'].$type."/".$cachePath;

			// for /?s=
			//$this->cacheFilePath = preg_replace("/(\/\?s\=)/", "$1/", $this->cacheFilePath);
		}
		$this->cacheFilePath .= "/index.html";

		//$this->cacheFilePath = $this->cacheFilePath ? rtrim($this->cacheFilePath, "/")."/" : "";
		//$this->cacheFilePath = preg_replace("/\/cache\/(all|wpfc-mobile-cache)\/\//", "/cache/$1/", $this->cacheFilePath);


		/*if(strlen($_SERVER["REQUEST_URI"]) > 1){ // for the sub-pages
			if(!preg_match("/\.html/i", $_SERVER["REQUEST_URI"])){
				if($this->is_trailing_slash()){
					if(!preg_match("/\/$/", $_SERVER["REQUEST_URI"])){
						if(defined('W3_CACHE_QUERYSTRING') && W3_CACHE_QUERYSTRING){
						
						}else if(preg_match("/gclid\=/i", $this->cacheFilePath)){
							
						}else if(preg_match("/fbclid\=/i", $this->cacheFilePath)){

						}else if(preg_match("/utm_(source|medium|campaign|content|term)/i", $this->cacheFilePath)){

						}else{
							$this->cacheFilePath = false;
						}
					}
				}else{
					//toDo
				}
			}
		}*/
		
		//$this->remove_url_paramters();

		// to decode path if it is not utf-8
		if($this->cacheFilePath){
			$this->cacheFilePath = urldecode($this->cacheFilePath);
		}

		// for security
		if(preg_match("/\.{2,}/", $this->cacheFilePath)){
			$this->cacheFilePath = false;
		}

		
	}
	
	function w3speedsterPreloadCache($urls) {
		$processed_urls = get_transient('processed_urls') ?: [];

		foreach ($urls as $url) {

			// Determine cache path
			$cachePath = isset($this->addSettings['rootCachePath']) ? $this->addSettings['rootCachePath'] : str_replace('\\', '/', $this->addSettings['content_path'] . '/cache/w3-cache');
			
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
					
					$this->w3DeleteFile($cacheUrl);
				}
			}
			if (time() - $time2 > $expiryTime) {
				if (file_exists($cacheUrlMob)) {
					$this->w3DeleteFile($cacheUrlMob);
				}
			}

			// Preload the URL
			$this->w3RemoteGet($url);
			$processed_urls[] = $url;
		}

		// Store processed URLs in a transient
		set_transient('processed_urls', $processed_urls, DAY_IN_SECONDS);
	}

	function w3speedsterGetSitemapUrl($sitemap_url) {
		$urls = [];
		$body = $this->w3RemoteGet($sitemap_url);

		if (empty($body)) {
			return $urls;
		}

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