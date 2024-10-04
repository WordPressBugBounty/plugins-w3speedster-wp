<?php
namespace W3speedster;
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class w3speedster_admin extends w3speedster{
    function launch(){
		
		if(!empty($_POST['import_text']) && isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'w3_settings' )){
			$import_text = (array)json_decode(stripcslashes($_POST['import_text']));
			if($import_text !== null){
				w3UpdateOption( 'w3_speedup_option',  $import_text, 'no');
				add_action( 'admin_notices', array($this,'w3AdminNoticeImportSuccess') );
			}else{
				add_action( 'admin_notices', array($this,'w3AdminNoticeImportFail') );
			}
		}
		
		if(!empty($this->add_settings['wp_get']['page']) && $this->add_settings['wp_get']['page'] == 'w3_speedster' && isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'w3_settings' )){
			$this->w3SaveOptions();
		}
		
		if(!empty($this->add_settings['wp_get']['page']) && $this->add_settings['wp_get']['page'] == 'w3_speedster'){
			add_action('admin_enqueue_scripts', array($this,'w3EnqueueAdminScripts') );
			add_action('admin_head',array($this,'w3EnqueueAdminHead'));
		}
		if(!empty($this->add_settings['wp_get']['w3_reset_preload_css'])){
			w3UpdateOption('w3speedup_preload_css','','no');
			add_action( 'admin_notices', array($this,'w3AdminNoticeImportSuccess') );
		}
		if(!empty($this->add_settings['wp_get']['w3_critical_css_data'])){
			print_r(w3GetOption('w3-critical-deleted'));
		}
		if(!empty($this->add_settings['wp_get']['restart'])){
			w3UpdateOption('w3speedup_img_opt_status',0,'no');
		}
		if(!empty($this->add_settings['wp_get']['reset'])){
			w3UpdateOption('w3speedup_opt_offset',0,'no');
            $redirect_url = remove_query_arg('reset');
			wp_redirect($redirect_url);
			exit;
		}
		if(!empty($this->add_settings['wp_get']['delete_ac'])){
			$advanced_cache_file = WP_CONTENT_DIR . '/advanced-cache.php';
			if (file_exists($advanced_cache_file)){
				wp_delete_file($advanced_cache_file);
			}
            $redirect_url = remove_query_arg('delete_ac');
			wp_redirect($redirect_url);
			exit;
		}
		if(!empty($this->add_settings['wp_get']['reset_css_que'])){
			w3UpdateOption('w3speedup_preload_css','', 'no');
		}
	}
	function w3AdminNoticeImportSuccess() {
		?>
		<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e( 'Data imported successfully!', 'w3speedster' ); ?></p>
		</div>
		<?php
	}
	function w3AdminNoticeImportFail(){
		?>
		<div class="error notice-error is-dismissible">
			<p><?php esc_html_e( 'Data import failed', 'w3speedster' ); ?></p>
		</div>
		<?php
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
		var cm_settings = <?php echo wp_json_encode($cm_settings);?>
		</script>
		<?php
	}
	
	function w3EnqueueAdminScripts(){

		wp_enqueue_script('wp-theme-plugin-editor');
		wp_enqueue_style('wp-codemirror');
	}
	
	function w3CheckLicenseKey(){
		$response = $this->w3speedsterValidateLicenseKey();
		if(!empty($response[0]) && $response[0] == 'fail' && strpos($response[1],'could not verify-1') !== false){
			w3UpdateOption('w3_key_log',wp_json_encode($response));
			$settings = w3GetOption( 'w3_speedup_option', true );
			$settings['is_activated'] = '';
			w3UpdateOption( 'w3_speedup_option', $settings,'no' );	
		}
	}
	
	function w3SaveOptions(){
		global $advanced_cache_exist;
		if(isset($_POST['ws_action']) && $_POST['ws_action'] == 'cache'){
			unset($_POST['ws_action'], $_POST['temp_input']);
			$keys_to_check = array('preload_resources', 'exclude_lazy_load', 'exclude_pages_from_optimization', 'exclude_css', 'force_lazyload_css', 'load_style_tag_in_head','exclude_page_from_load_combined_css','exclude_javascript','force_lazy_load_inner_javascript','exclude_inner_javascript','exclude_page_from_load_combined_js','load_script_tag_in_url','exclude_url_html_cache','exclude_url_exclusions_html_cache');
			foreach($_POST as $key=>$value){
				

				if (in_array($key, $keys_to_check)) {
					$array[$key] = implode("\r\n", $value);
				}else{
					$array[$key] = $value;
				}
			}
			if(empty($array['license_key'])){
				$array['is_activated'] = '';
			}
			$advanced_cache_file = WP_CONTENT_DIR . '/advanced-cache.php';
			if(isset($_POST['html_caching']) && file_exists($advanced_cache_file) && strpos($this->w3speedsterGetContents($advanced_cache_file),'Added By W3speedster Pro') == 0 && $_POST['html_caching'] == 'on' && $_POST['by_serve_cache_file'] == 'advanceCache'){
				unset($array['html_caching']);
				$advanced_cache_exist = 1;
			}
			
			if(empty($array['html_caching'])){
				$this->w3SpeedsterRemoveHtmlCacheCode();
			}
			
			w3UpdateOption( 'w3_speedup_option', $array,'no' );		
			$this->settings = w3GetOption( 'w3_speedup_option', true );
			if(!empty($array['html_caching'])){
				
				$serveByAdvancedCache = $this->settings['by_serve_cache_file'];
				// @codingStandardsIgnoreLine
				if(is_file($this->add_settings['wp_document_root'] . "/.htaccess") && is_writable($this->add_settings['wp_document_root'] . "/.htaccess")){
					
					$htaccessPath = $this->add_settings['wp_document_root'] . "/.htaccess";
					$htaccessContent = $this->w3speedsterGetContents($htaccessPath);
					
					if($serveByAdvancedCache == 'htaccess'){
						$enableCachingGetParaRule = isset($this->settings['enable_caching_get_para']) ? '#' : '';
						$loggedinCachingRule = isset($this->settings['enable_loggedin_user_caching']) ? '#' : '';
						$msgPara = ($enableCachingGetParaRule) ? 'true' : 'false';
						$msgloggedIn = ($loggedinCachingRule) ? 'true' : 'false';
						$cachePath =  ($array['cache_path']) ? $array['cache_path'] :$this->add_settings['wp_content_path'] . '/cache/w3-cache';
						
						if( (strpos($htaccessContent,'checkQuery = '.$msgPara) == false) ||
							(strpos($htaccessContent,'checkLoggedIn = '.$msgloggedIn) == false) ||
							(strpos($htaccessContent,'# DefaultPath = '.$cachePath) == false) 
						){
							$htaccess = preg_replace("/#\s?BEGIN\s?W3HTMLCACHE.*?#\s?END\s?W3HTMLCACHE/s", "", $htaccessContent);
							$data = $this->w3speedsterGetHtaccessData($array['cache_path']);
							$htaccess = $data. PHP_EOL .$htaccess;
							$this->w3speedsterPutContents($htaccessPath, $htaccess);
						}
					}
					if($serveByAdvancedCache == 'advanceCache'){
						$htaccess = preg_replace("/#\s?BEGIN\s?W3HTMLCACHE.*?#\s?END\s?W3HTMLCACHE/s", "", $htaccessContent);
						$this->w3speedsterPutContents($htaccessPath, $htaccess);
					}
					
				}
				
					//$file_content = $this->w3SpeedsterGetDataAdvancedCacheFile();
					
					$previousCacheFile = $this->w3speedsterGetContents($advanced_cache_file);
					
					$cachePath =  ($array['cache_path'] ? $array['cache_path'] :$this->add_settings['wp_content_path'] . '/cache').'/w3-cache';
					$loggedinCaching = isset($array['enable_loggedin_user_caching']) ? 1 : 0;
					$enableCachingGetPara = isset($this->settings['enable_caching_get_para']) ? 1 : 0;
					$seprateMobileCaching = isset($this->settings['html_caching_for_mobile']) ? 1 : 0;
				
					if(file_exists($advanced_cache_file) && strpos($this->w3speedsterGetContents($advanced_cache_file),'Added By W3speedster Pro') != 0){
						
						if( (strpos($previousCacheFile,$cachePath) ==  false ) ||
							(strpos($previousCacheFile,$loggedinCaching.' == 0 &&  $userLoggedin') == false) ||
							(strpos($previousCacheFile,'< '.$array['html_caching_expiry_time'])) == false ||
							(strpos($previousCacheFile,$enableCachingGetPara.' == 0 &&  $queryPara') == false) ||
							(strpos($previousCacheFile,$seprateMobileCaching.' == 1') == false) ||
							(strpos($previousCacheFile,'"'.$serveByAdvancedCache.'" == "htaccess"') == false)
						){
							$file_content = $this->w3SpeedsterGetDataAdvancedCacheFile($array['cache_path']);
							wp_delete_file( $advanced_cache_file);
							$this->w3speedsterPutContents($advanced_cache_file, $file_content);
						}
					}
					
					if(!file_exists($advanced_cache_file)){
						$file_content = $this->w3SpeedsterGetDataAdvancedCacheFile($array['cache_path']);
						$this->w3speedsterPutContents($advanced_cache_file, $file_content);
					}	
			
			}
			$this->w3ModifyHtaccess();
			
		}
	}
    function getCurlUrl($url){
      return parent::getCurlUrl($url);
    }

    function w3SpeedsterCachePurgeCallback() {
		if ( !isset( $this->add_settings['wp_get']['_wpnonce'] ) || !wp_verify_nonce( $this->add_settings['wp_get']['_wpnonce'],'purge_cache') ) {
			if(!empty($this->add_settings['wp_get']['resource_url'])){
				$url = str_replace(array($this->add_settings['wp_home_url'],$this->add_settings['image_home_url']),'',$this->add_settings['wp_get']['resource_url']);
				if(is_file($this->add_settings['wp_document_root'].'/'.ltrim($url,'/'))){
					echo 'Request not valid'; exit;
				}
			}else{
				echo 'Request not valid'; exit;
			}
		}
        $w3speedster_init = new w3speedster();
        $response =round( (int)$w3speedster_init->w3RemoveCacheFilesHourlyEventCallback(),2);
        //$response = round( (int)get_option('w3_speedup_filesize') / 1024/1024 , 2);
        echo esc_html($response);
        wp_die();
    }
	
	function w3SpeedsterHtmlCachePurgeCallback(){
	   
		if ( isset( $this->add_settings['wp_get']['_wpnonce'] ) && wp_verify_nonce( $this->add_settings['wp_get']['_wpnonce'],'purge_html_cache') ) {
			 if (function_exists('exec')) {
            exec('rm -r ' . $this->w3GetCachePath().'/html', $output, $retval);
            	echo 'Cache Delete Successfully';
        }
		}
	
        wp_die();
	}
	function w3SpeedsterCriticalCachePurgeCallback() {
		if ( !isset( $this->add_settings['wp_get']['_wpnonce'] ) || !wp_verify_nonce( $this->add_settings['wp_get']['_wpnonce'],'purge_critical_css') ) {
			return 'Request not valid';
		}
		
        $w3speedster_init = new w3speedster();
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
			echo esc_html(round( (int)w3GetOption('w3_speedup_filesize') / 1024/1024 , 2));
		}else{
			$response =round( (int)$w3speedster_init->w3RemoveCriticalCssCacheFiles(),2);
			//$response = round( (int)get_option('w3_speedup_filesize') / 1024/1024 , 2);
			echo esc_html($response);
		}
		// @codingStandardsIgnoreLine
		w3UpdateOption('w3-critical-deleted',array_merge($_REQUEST,array('user_id'=>get_current_user_id(),'timestamp'=>date('Y-m-d h:i:sa'))));
        wp_die();
    }
     
	
	function w3ModifyHtaccess(){
			$path = $this->add_settings['wp_document_root'].'/';
			if(!file_exists($path.".htaccess")){
				if(isset($_SERVER["SERVER_SOFTWARE"]) && $_SERVER["SERVER_SOFTWARE"] && (preg_match("/iis/i", $_SERVER["SERVER_SOFTWARE"]) || preg_match("/nginx/i", $_SERVER["SERVER_SOFTWARE"]))){
					//
				}else{
					return array("<label>.htaccess was not found</label>", "w3speedster");
				}
			}

			
			if(!WP_CACHE){
				if($wp_config = $this->w3speedsterGetContents(ABSPATH."wp-config.php")){
					$wp_config = str_replace("\$table_prefix", "define('WP_CACHE', true);\n\$table_prefix", $wp_config);

					if(!$this->w3speedsterPutContents(ABSPATH."wp-config.php", $wp_config)){
						return array("define('WP_CACHE', true); is needed to be added into wp-config.php", "w3speedster");
					}
				}else{
					return array("define('WP_CACHE', true); is needed to be added into wp-config.php", "w3speedster");
				}
			}
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
				if(empty($webp_disable_htaccess) && $this->add_settings['image_home_url'] == $this->add_settings['wp_site_url']){
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
					$this->w3speedsterGetContents($path.".htaccess", $htaccess);
				}
			}else{
				return array(__("Options have been saved", 'w3speedster'), "updated");
			}
			return array(__("Options have been saved", 'w3speedster'), "updated");

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
			$wp_content_arr = explode('/',trim($this->add_settings['wp_content_path'],'/'));
			$wp_content = array_pop($wp_content_arr);
			$wp_content_webp = $wp_content."/w3-webp/";
			$basename = $wp_content_webp."$1w3.webp";
			/* 
				This part for sub-directory installation
				WordPress Address (URL): site_url() 
				Site Address (URL): home_url()
			*/
			if(preg_match("/https?\:\/\/[^\/]+\/(.+)/", site_url(), $siteurl_base_name)){
				if(preg_match("/https?\:\/\/[^\/]+\/(.+)/", home_url(), $homeurl_base_name)){
					/*
						site_url() return http://example.com/sub-directory
						home_url() returns http://example.com/sub-directory
					*/

					$homeurl_base_name[1] = trim($homeurl_base_name[1], "/");
					$siteurl_base_name[1] = trim($siteurl_base_name[1], "/");

					if($homeurl_base_name[1] == $siteurl_base_name[1]){
						if(preg_match("/".preg_quote($homeurl_base_name[1], "/")."$/", trim(ABSPATH, "/"))){
							$basename = $homeurl_base_name[1]."/".$basename;
						}
					}
				}else{
					/*
						site_url() return http://example.com/sub-directory
						home_url() returns http://example.com/
					*/
					$siteurl_base_name[1] = trim($siteurl_base_name[1], "/");
					$basename = $siteurl_base_name[1]."/".$basename;
				}
			}

			if(ABSPATH == "//"){
				$RewriteCond = "RewriteCond %{DOCUMENT_ROOT}/".$basename." -f"."\n";
			}else{
				// to escape spaces
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
		
		function createimageinstantly($imges=array()){
		$x=$y=300;
		
		$uploads = wp_upload_dir();
		
	
		//header('Content-Type: image/png');
		//$targetFolder = '/gw/media/uploads/processed/';
		//$targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
		$targetPath = $uploads['basedir'];		
		
		if(!empty($imges)){
			$height_array = array();
			$max_width = 0;
			$images_detail = array();
			foreach($imges as $key=>$img){
				$size = getimagesize($img);
				//$size2 = getimagesize($img2);
				//$size3 = getimagesize($img3);			
				//$height_array = array($size1[1], $size2[1] ,$size3[1]);				
				//$max_width = ($size1[0]+$size2[0]+$size3[0])+60 ;	
				$size['src'] = $img ;				
				$height_array[] = $size[1];				
				$max_width = $max_width+$size[0]+20 ;
				$images_detail[$key] = 	$size ;
			}
			$max_height = max($height_array);
			
			
			$outputImage = imagecreatetruecolor( $max_width, $max_height);

			// set background to white
			$white = imagecolorallocate($outputImage, 0, 0, 0);
			//imagefill($outputImage, 0, 0, $white);
			imagecolortransparent($outputImage, $white);
			
			/*
			$first = imagecreatefrompng($img1);
			$second = imagecreatefrompng($img2);
			$third = imagecreatefrompng($img3);

			//imagecopyresized ( resource $dst_image , resource $src_image , int $dst_x , int $dst_y , int $src_x , int $src_y , int $dst_w , int $dst_h , int $src_w , int $src_h )
			
			
			imagecopyresized($outputImage,$first,0,0,0,0, $size1[0], $size1[1],$size1[0], $size1[1]);
			
			imagecopyresized($outputImage,$second,($size1[0]+20),0,0,0, $size2[0], $size2[1], $size2[0], $size2[1]);
			
			imagecopyresized($outputImage,$third,($size1[0]+$size2[1]+40),0,0,0, $size3[0], $size3[1],$size3[0], $size3[1]); */
			
						
			$new_coordinates = 0;
			$new_images_detail = array();
			foreach($images_detail as $key=>$img){					
				$new_image = imagecreatefrompng($img['src']);
				imagecopyresized($outputImage,$new_image,$new_coordinates,0,0,0, $img[0], $img[1],$img[0], $img[1]);
				$new_coordinates = $new_coordinates+$img[0]+20;					
			}			
			
			// Add the text
			//imagettftext ( resource $image , float $size , float $angle , int $x , int $y , int $color , string $fontfile , string $text )
			//$white = imagecolorallocate($im, 255, 255, 255);
			$text = 'School Name Here';
			$font = 'OldeEnglish.ttf';
			//imagettftext($outputImage, 32, 0, 150, 150, $white, $font, $text);
			
			$wp_upload_dir = wp_upload_dir();
			
			$image_name = 'combine_image_'.round(microtime(true)).'.png';
			$filename = $wp_upload_dir['path'].'/'.$image_name;
			imagepng($outputImage, $filename);			
			
			// create attachment post			
			$filetype = wp_check_filetype( basename( $image_name ), null );

			// Prepare an array of post data for the attachment.
			$attachment = array(
				'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ), 
				'post_mime_type' => $filetype['type'],
				'post_title'     => sanitize_title(preg_replace( '/\.[^.]+$/', '', basename( $filename ) )),
				'post_content'   => '',
				'post_status'    => 'inherit'
			);
			
			
			$attach_id = wp_insert_attachment( $attachment, $filename, 0 );
			// Include image.php
			require_once(ABSPATH . 'wp-admin/includes/image.php');

			// Define attachment metadata
			$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );

			// Assign metadata to attachment
			wp_update_attachment_metadata( $attach_id, $attach_data );
			
			w3UpdateOption( 'w3_speedup_combine_image_id', $attach_id, 'no' );
			
			imagedestroy($outputImage);
		}
	}

	function getWsOptimizeImage($image_url, $image_width){
		$w3_speedster_img = new w3speedster_optimize_image(); 
		$result = $w3_speedster_img->w3OptimizeAttachment($image_url, $image_width, false,'',true);
		return $result['img'] == 1 ? 'success' : 'failed' ;
	}
	
	function notify($message = array()){
			if(isset($message[0]) && $message[0]){
				if(function_exists("add_settings_error")){
					add_settings_error('wpfc-notice', esc_attr( 'settings_updated' ), $message[0], $message[1]);
				}
			}
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
			'html'          => '<div class="loader-sec"><div class="loader"></div></div><a href="#" data-id="' . $post->ID  . '" class="optimize_media_image button-secondary button-large" title="' . esc_attr( __( 'Optimize image', 'w3speedster' ) ) . '">' . __( 'Optimize Image', 'w3speedster' ) . '<i class="dashicons dashicons-saved" style="vertical-align: sub;"></i></a>',
			'show_in_modal' => true,
			'show_in_edit'  => false,
		);

		return $form_fields;
	}
	
	

	function fnW3OptimizeMediaImageCallback (){
		if(isset($_POST['id']) && !empty($_POST['id'])){
			
			$attach_id = $_POST['id'];	
			require_once(W3SPEEDSTER_PLUGIN_DIR . 'includes/class_image.php');
			$w3speedster_image = new w3speedster_optimize_image();
			$result = $w3speedster_image->w3OptimizeAttachmentId($attach_id);
		}
		
		
		echo wp_json_encode(array(
                'summary' => $result,
                'status' => '200'
            ));
		exit;
	}
	
	function w3SpeedsterRemoveHtmlCacheCode(){
			$htaccessPath = $this->add_settings['wp_document_root'] . "/.htaccess";
			$htaccessContent = $this->w3speedsterGetContents($htaccessPath);
			
			// @codingStandardsIgnoreLine
			if(is_file($this->add_settings['wp_document_root'] . "/.htaccess") && is_writable($this->add_settings['wp_document_root'] . "/.htaccess") && strpos($htaccessContent,'# BEGIN W3HTMLCACHE') !== false && strpos($htaccessContent,'# END W3HTMLCACHE') !== false){
				$htaccess = preg_replace("/#\s?BEGIN\s?W3HTMLCACHE.*?#\s?END\s?W3HTMLCACHE/s", "", $htaccessContent);
				$this->w3speedsterPutContents($htaccessPath, $htaccess);
			}
			$advanced_cache_file = WP_CONTENT_DIR . '/advanced-cache.php';
			if(file_exists($advanced_cache_file) && strpos($this->w3speedsterGetContents($advanced_cache_file),'Added By W3speedster Pro') !== false){
				wp_delete_file($advanced_cache_file);
			}
	}

	

}