<?php
namespace W3speedster;
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class w3speedster_admin extends w3speedster{
	
    function launch(){
		
		if(!empty($_POST['import_text']) && isset( $_POST['_wpnonce'] ) && $this->checkSecurityKey( $_POST['_wpnonce'], 'w3_settings' )){
			$this->importData($_POST['import_text']);
		}
		
		if(!empty($this->add_settings['wp_get']['page']) && $this->add_settings['wp_get']['page'] == 'w3_speedster' && isset( $_POST['_wpnonce'] ) && $this->checkSecurityKey( $_POST['_wpnonce'], 'w3_settings' )){
			$this->w3SaveOptions();
		}
		
		if(!empty($this->add_settings['wp_get']['page']) && $this->add_settings['wp_get']['page'] == 'w3_speedster'){
			$this->enqueueScripts();
		}
		if(!empty($this->add_settings['wp_get']['w3_reset_preload_css'])){
			w3UpdateOption('w3speedup_preload_css','','no');
		}
		if(!empty($this->add_settings['wp_get']['w3_critical_css_data'])){
			print_r(w3GetOption('w3-critical-deleted'));
		}
		if(!empty($this->add_settings['wp_get']['reset'])){
			$this->resetImageOptCount();
		}
		if(!empty($this->add_settings['wp_get']['delete_ac'])){
			$this->removeAdvanceCacheFile();
		}
		if(!empty($this->add_settings['wp_get']['reset_css_que'])){
			w3UpdateOption('w3speedup_preload_css','', 'no');
		}
	}
	
	function w3CheckLicenseKey(){
		$res= $this->w3speedsterValidateLicenseKey();
		$response = json_decode($res);
		if(!empty($response[0]) && $response[0] == 'fail' && strpos($response[1],'could not verify-1') !== false){
			w3UpdateOption('w3_key_log',$this->w3JsonEncode($response));
			$settings = w3GetOption( 'w3_speedup_option', true );
			$settings['is_activated'] = '';
			w3UpdateOption( 'w3_speedup_option', $settings,'no' );	
		}
	}
	
	function w3SaveOptions(){
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
			$this->checkHtmlCacheSettings();
			w3UpdateOption( 'w3_speedup_option', $array,'no' );		
			$this->settings = w3GetOption( 'w3_speedup_option', true );
			
		}
	}
    
    function w3SpeedsterCachePurgeCallback() {
		if ( !isset( $this->add_settings['wp_get']['_wpnonce'] ) || !$this->checkSecurityKey( $this->add_settings['wp_get']['_wpnonce'],'purge_cache') ) {
			if(!empty($this->add_settings['wp_get']['resource_url'])){
				$url = str_replace(array($this->add_settings['home_url'],$this->add_settings['image_home_url']),'',$this->add_settings['wp_get']['resource_url']);
				if(is_file($this->add_settings['document_root'].'/'.ltrim($url,'/'))){
					echo 'Request not valid'; exit;
				}
			}else{
				echo 'Request not valid'; exit;
			}
		}
        $w3speedster_init = new w3speedster();
        $response =round( (int)$w3speedster_init->w3RemoveCacheFilesHourlyEventCallback(),2);
        echo esc_html($response);
        exit;
    }
	
	function w3SpeedsterHtmlCachePurgeCallback(){
	   
		if ( isset( $this->add_settings['wp_get']['_wpnonce'] ) && $this->checkSecurityKey( $this->add_settings['wp_get']['_wpnonce'],'purge_html_cache') ) {
			 if (function_exists('exec')) {
				exec('rm -rf ' . $this->w3GetCachePath().'/html', $output, $retval);
					echo 'Cache Delete Successfully';
			}
		}
	
        exit;
	}
	
}