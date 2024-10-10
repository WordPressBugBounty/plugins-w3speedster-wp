<?php
namespace W3speedster;
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class w3speedster_js extends w3speedster_css{

    
	function w3ModifyFileCacheJs($string, $path){
		$src_array = explode('/',$path);
		$count = count($src_array);
		unset($src_array[$count-1]);
		if(!empty($this->settings['load_combined_js'])){
			/*if((stripos($string,'holdready') !== false || stripos($string,'S.holdReady') !== false) && empty($this->add_settings['holdready'])){
				$string .= ';if(typeof($) == "undefined"){$ = jQuery;}else{var $ = jQuery;}';
				$this->add_settings['holdready'] = 1;
			}*/
			$exclude_from_w3_changes = 0;
			if(function_exists('w3speedup_exclude_internal_js_w3_changes')){
				$exclude_from_w3_changes = w3speedup_exclude_internal_js_w3_changes($path,$string);
			}
			
			if(!empty($this->settings['hook_exclude_internal_js_w3_changes'])){
				$code = str_replace(array('$exclude_from_w3_changes','$path','$string'),array('$args[0]','$args[1]','$args[2]'),$this->settings['hook_exclude_internal_js_w3_changes']);
				$exclude_from_w3_changes = $this->hookCallbackFunction($code,$exclude_from_w3_changes,$path,$string);
			}
			if(stripos($string,'holdready') === false && !$exclude_from_w3_changes){
				$string = $this->w3_changes_in_js($string,$path);
			}
		}
		if(function_exists('w3speedup_internal_js_customize')){
			$string = w3speedup_internal_js_customize($string,$path);
		}
		
		if(!empty($this->settings['hook_internal_js_customize'])){
		   $code = $code = str_replace(array('$string','$path'),array('$args[0]','$args[1]'),$this->settings['hook_internal_js_customize']);
           $string = $this->hookCallbackFunction($code,$string,$path);
        }
		return $string;
	}
	function unlazyLoadBackgroundImageJavascript($string){
		if(strpos($string,'data-BgLz="1" ') !== false){
			$string = str_replace('data-BgLz="1" ','',$string);
		}
		return $string;
	}
	function w3CreateFileCacheJsUrl($path){
		$file_name = w3GetOption('w3_rand_key');
        $cache_file_path = $this->w3GetCachePath('js').'/'.$file_name.'/'.ltrim($path,'/');
	    //$cache_file_path = $this->w3GetCachePath('js').'/'.md5($this->add_settings['w3_rand_key'].$path).'.js';
        if( !file_exists($cache_file_path) ){
			$path1 = explode('/',$path);
			array_pop($path1);
			$path1 = implode('/',$path1);
            $this->w3CheckIfFolderExists($this->w3GetCachePath('js'.'/'.$file_name.'/'.ltrim($path1,'/')));
            //$this->w3CheckIfFolderExists($this->w3GetCachePath('js'));
			$string = $this->w3speedsterGetContents($path);
            $string = $this->w3ModifyFileCacheJs($string, $path);
            $this->w3CreateFile($cache_file_path, $string );
        }
		if(!file_exists($cache_file_path)){
			return $path;
		}else{
			return str_replace($this->add_settings['document_root'],'',$cache_file_path);
		}
	}
	function w3_changes_in_js($string,$path=''){
		//$string = preg_replace('/"(\s*)use strict(\s*)"(\s*);/', '', $string, 1);
		$string = str_replace('document.readyState','document.w3readyState',$string);
		$string = preg_replace('/([\s\;\:\}\,\)\(\{])([a-zA-Z]+)([.]addEventListener\s*\(\s*[\'|"]\s*DOMContentLoaded\s*[\'|"]\s*,)/', "$1$2.addEventListener('w3-DOMContentLoaded',", $string);
		$string = preg_replace('/([\s\;\:\}\,\)\(\{])([a-zA-Z]+)([.]addEventListener\s*\(\s*[\'|"]\s*load\s*[\'|"]\s*,)/', "$1$2.addEventListener('w3-load',", $string);
		if(strpos($string,'DOMContentLoaded') === false){
			$string = preg_replace('/(jQuery|\$)\(\s?function\s?\(\s?((?!jQuery)[\S|\$]{1,6})\s?\)\s?{/', 'jQuery(document).on(\'w3-DOMContentLoad\',function(){const $2=jQuery;', $string);
			$string = preg_replace('/(jQuery|\$)\(\s?function\s?\(\s?(jQuery|[\s]{0,4})\s?\)\s?{/', 'jQuery(document).on(\'w3-DOMContentLoad\',function(){', $string);
			$string = preg_replace('/(jQuery|\$)(\s?\(\s?)([\(\s]?\(\s?\(\s?\)=>)/', 'document.addEventListener(\'w3-DOMContentLoaded\',$3', $string);
			$string = preg_replace('/\s{1}(\S{1,6})\(document\)\s?\.on\s?\([\'|\"]\s?ready\s?[\'|\"]\s?,(\s?function\s?\(\s?((?!jQuery)[\S|\$]{1,6})\s?\)\s?{)/', ' jQuery(document).on(\'w3-DOMContentLoad\',function(){const $3=jQuery;', $string);
			$string = preg_replace('/(jQuery|\$)(\s?\(\s?document\)\s?)\.ready\(function\s?\((\s?(?!jQuery)[\S|\$]{1,6})\s?\)\s?{/', 'jQuery(document).on(\'w3-DOMContentLoad\',function(){const $3=jQuery;', $string);
			$string = preg_replace('/(jQuery|\$)(\s?\(\s?document\)\s?)\.ready\(function\s?\((\s?(jQuery|[\s]{0,4}))\s?\)\s?{/', 'jQuery(document).on(\'w3-DOMContentLoad\',function(){', $string);
		}
		$string = preg_replace('/(\(document\)\s*\.on\s*\([\'|\"]\s*)load(\s*[\'|\"]\s*,\s*\(?function\s*\(\s*\S*\s*\))/', "$1w3-load$2", $string);
		$string = preg_replace('/(\(window\)\s*\.on\s*\([\'|\"]\s*)load(\s*[\'|\"]\s*,\s*\(?function\s*\(\s*\S*\s*\))/', "$1w3-load$2", $string);
		if(!empty($this->settings['enable_inp']) && (!function_exists('w3NoChangesForInp') || (function_exists('w3NoChangesForInp') && !w3NoChangesForInp($string, $path)))){
			$string = preg_replace('/(\.on\s*\(\s*)[\'|\"]\s*click\s*[\'|\"](\s*\,\s*)/', '$1w3elem$2', $string);
			$string = preg_replace('/(\.)click\s*\((\s*function)/', '$1on(w3elem,$2', $string);
			$string = preg_replace('/(\.)addEventListener\(\s*[\'|\"]\s*click\s*[\'|\"]/','.addEventListener(w3elem',$string);
		}
		return $string;
	}
    function w3CreateFileCacheJs($path){
		$file_name = w3GetOption('w3_rand_key');
        $cache_file_path = $this->w3GetCachePath('js').'/'.$file_name.'/'.ltrim($path,'/');
	    //$cache_file_path = $this->w3GetCachePath('js').'/'.md5($this->add_settings['w3_rand_key'].$path).'.js';
        if( !file_exists($cache_file_path) ){
			$path1 = explode('/',$path);
			array_pop($path1);
			$path1 = implode('/',$path1);
            $string = $this->w3speedsterGetContents($this->add_settings['document_root'].$path);
            $string = $this->w3ModifyFileCacheJs($string, $path);
            $this->w3CreateFile($cache_file_path, $string );
        }
	    return str_replace($this->add_settings['document_root'],'',$cache_file_path);
    }
    
    function w3CompressJs($string){
        include_once W3SPEEDSTER_DIR.'includes/jsmin.php';
        $string = \W3jsMin::minify($string);
        return $string;
    }
    
    function minify($script_links){
		if(!empty($this->settings['exclude_page_from_load_combined_js']) && $this->w3CheckIfPageExcluded($this->settings['exclude_page_from_load_combined_js'])){
			return ;
        }
		
		if(!empty($script_links) && !empty($this->settings['js'])){
			$lazy_load_js = !empty($this->settings['load_combined_js']) && $this->settings['load_combined_js'] == 'after_page_load' ? 1 : 0;
			$force_innerjs_to_lazy_load  = !empty($this->settings['force_lazy_load_inner_javascript']) ? explode("\r\n", $this->settings['force_lazy_load_inner_javascript']) : array();
            $exclude_js_arr_split  = !empty($this->settings['exclude_javascript']) ? explode("\r\n", $this->settings['exclude_javascript']) : array();
			foreach($exclude_js_arr_split as $key => $value){
				if(strpos($value,' defer') !== false){
					$exclude_js_arr[$key]['string'] = str_replace(' defer','',$value);
					$exclude_js_arr[$key]['defer'] = 1;
				}elseif(strpos($value,' full') !== false){
					$exclude_js_arr[$key]['string'] = str_replace(' full','',$value);
					$exclude_js_arr[$key]['full'] = 1;
				}else{	
					$exclude_js_arr[$key]['string'] = $value;
					$exclude_js_arr[$key]['defer'] = 0;
				}
			}
            $exclude_inner_js= !empty($this->settings['exclude_inner_javascript']) ? explode("\r\n", stripslashes($this->settings['exclude_inner_javascript'])) : array();
            $load_ext_js_before_internal_js = !empty($this->settings['load_external_before_internal']) ? explode("\r\n", $this->settings['load_external_before_internal']) : array();
            $all_js='';
            $included_js = array();
            $final_merge_js = array();
            $js_file_name = '';
            $enable_cdn = 0;
            if($this->add_settings['image_home_url'] != $this->add_settings['site_url']){
				$ext = '.js';
				if(empty($this->add_settings['exclude_cdn']) || !in_array($ext,$this->add_settings['exclude_cdn'])){
					$enable_cdn = 1;
				}
			}
			
			$final_merge_has_js = array();
			
			for($si=0; $si < count($script_links); $si++){
                $script = $script_links[$si];
				$script_obj = !empty($this->add_settings['script_obj'][$si]) ? $this->add_settings['script_obj'][$si] : $this->w3ParseLink('script',str_replace($this->add_settings['image_home_url'],$this->add_settings['site_url'],$script_links[$si]));
				$script_text = '';
				if(!array_key_exists('src',$script_obj)){
                    $script_text = $this->w3ParseScript('script',$script);
                }else{
					$script_obj['src'] = trim($script_obj['src']);
				}
				if(!empty($script_obj['type']) && strtolower($script_obj['type']) != 'application/javascript' && strtolower($script_obj['type']) != 'module' && strtolower($script_obj['type']) != 'text/javascript' && strtolower($script_obj['type']) != 'text/jsx;harmony=true'){
                    continue;
                }
				if(function_exists('w3speedup_customize_script_object')){
					$script_obj = w3speedup_customize_script_object($script_obj, $script);
				}
				if(!empty($this->settings['hook_customize_script_object'])){
					$code = str_replace(array('$script_obj','$script'),array('$args[0]','$args[1]'),$this->settings['hook_customize_script_object']);
					$script_obj = $this->hookCallbackFunction($code,$script_obj, $script);
				}
				
				//echo 'rocket <pre>';print_r($script_obj);exit;
                if(!empty($script_obj['src'])){
					
					//echo $script_obj['src'];
                    $url_array = $this->w3ParseUrl($script_obj['src']);
					$url_array['path'] = '/'.ltrim($url_array['path'],'/');
                    $exclude_js = 0;
					$enable_cdn_path = 0;
                    if(!empty($exclude_js_arr) && is_array($exclude_js_arr)){
						foreach($exclude_js_arr as $ex_js){
							if(!empty($ex_js['string']) && strpos($script,$ex_js['string']) !== false){
								if(!empty($ex_js['defer'])){
									$exclude_js = 2;
								}elseif(!empty($ex_js['full'])){
									$exclude_js = 3;
								}else{
									$exclude_js = 1;
								}
							}
						}
					}
					if(function_exists('w3speedup_exclude_javascript_filter')){
						$exclude_js = w3speedup_exclude_javascript_filter($exclude_js,$script_obj,$script,$this->html);
					}
					if(!empty($this->settings['hook_external_javascript_filter'])){
						$code = str_replace(array('$exclude_js','$script_obj','$script','$html'),array('$args[0]','$args[1]','$args[2]','$args[3]'),$this->settings['hook_external_javascript_filter']);
						$exclude_js = $this->hookCallbackFunction($code,$exclude_js,$script_obj,$script,$this->html);
					}
					
					if($exclude_js){
						$this->settings['js_is_excluded'] = 1;
					}
					if($this->w3CheckEnableCdnPath($script_obj['src'])){
						$enable_cdn_path = 1;
					}
					if(strpos($url_array['path'],'./') !== false || strpos($url_array['path'],'../') !== false){
                    	$url_array['path'] = $this->removeDotPathSegments($url_array['path']);
                    }
					if(!$this->w3IsExternal($script_obj['src']) && $this->w3Endswith($url_array['path'], '.js') && $exclude_js != 1 && $exclude_js != 3){
                        $old_path = $url_array['path'];
                        if(file_exists($this->add_settings['document_root'].$url_array['path'])){
							$url_array['path'] = $this->w3CreateFileCacheJs($url_array['path']);
						}else{
							$url_array['path'] = $this->w3CreateFileCacheJsUrl($script_obj['src']);
						}
                        $script_obj['src'] = $this->add_settings['site_url'].$url_array['path'];
                    }
					if($exclude_js){
                        if( $exclude_js == 3 || $exclude_js == 1){
							$script_obj['src'] = $enable_cdn && $enable_cdn_path ? str_replace($this->add_settings['site_url'],$this->add_settings['image_home_url'] ,$script_obj['src']) : $script_obj['src'];
							$this->add_settings['preload_resources']['all'][] = $script_obj['src'];
							$this->w3StrReplaceSet($script,$this->w3ImplodeLinkArray('script',$script_obj));
							continue;
						}
						if( $exclude_js == 2){
                            $script_obj['defer'] = 'defer';
						}
						if(file_exists($this->add_settings['document_root'].$url_array['path']) && strpos( $this->w3speedsterGetContents($this->add_settings['document_root'].$url_array['path']),'jQuery requires a window with a document') !== false){
							$this->add_settings['jquery_excluded'] = $this->add_settings['document_root'].$url_array['path'];
						}
						$script_obj['src'] = $enable_cdn && $enable_cdn_path ? str_replace($this->add_settings['site_url'],$this->add_settings['image_home_url'] ,$script_obj['src']) : $script_obj['src'];
						$this->w3StrReplaceSet($script,$this->w3ImplodeLinkArray('script',$script_obj));
                        continue;
                    }
                    $exclude_js_bool=0;
					if(!empty($force_innerjs_to_lazy_load)){
                        foreach($force_innerjs_to_lazy_load as $js){
                            if( !empty($js) && strpos($script,$js) !== false){
                                $exclude_js_bool=1;
                                break;
                            }
                        }
                    }
					
                    $val = $script_obj['src'];
                    if(!empty($val) && !$this->w3IsExternal($val) && strpos($script, '.js') && empty($exclude_js_bool)){
						if(!empty($script_obj['type']) && $script_obj['type'] != 'text/javascript'){
							$script_obj['data-w3-type']= $script_obj['type'];
						}
						$script_obj['type'] = 'lazyJs';
						$script_obj['src'] = $enable_cdn && $enable_cdn_path ? str_replace($this->add_settings['site_url'],$this->add_settings['image_home_url'] ,$this->add_settings['site_url'].$url_array['path']) : $this->add_settings['site_url'].$url_array['path'];
						$this->w3StrReplaceSet($script,$this->w3ImplodeLinkArray('script',$script_obj));
					}elseif($this->w3IsExternal($val) && empty($exclude_js_bool) ){
						if(!empty($script_obj['type']) && $script_obj['type'] != 'text/javascript'){
							$script_obj['data-w3-type']= $script_obj['type'];
						}
						$script_obj['type'] = 'lazyJs';
						$this->w3StrReplaceSet($script,$this->w3ImplodeLinkArray('script',$script_obj));
					}elseif($exclude_js_bool){
						if(!empty($script_obj['type']) && $script_obj['type'] != 'text/javascript'){
							$script_obj['data-w3-type']= $script_obj['type'];
						}
						$script_obj['src'] = $enable_cdn && $enable_cdn_path ? str_replace($this->add_settings['site_url'],$this->add_settings['image_home_url'] ,$script_obj['src']) : $script_obj['src'];
						$script_obj['type'] = 'lazyExJs';
						if(function_exists('w3_external_javascript_customize')){
							$script_obj = w3_external_javascript_customize($script_obj, $script);
						}
						
						if(!empty($this->settings['hook_external_javascript_customize'])){
							$code = str_replace(array('$script_obj','$script'),array('$args[0]','$args[1]'),$this->settings['hook_external_javascript_customize']);
							$script_obj = $this->hookCallbackFunction($code,$script_obj, $script);
						}
						$this->w3StrReplaceSet($script,$this->w3ImplodeLinkArray('script',$script_obj));
                    }
                }else{
                    
                    $inner_js = $script_text;
                    $lazy_loadjs = 0;
                    $exclude_js_bool = 0;
					$force_js_bool = 0;
                    $exclude_js_bool = $this->w3CheckJsIfExcluded($inner_js, $exclude_inner_js);
					if(function_exists('w3speedup_inner_js_customize')){
						$script_text = w3speedup_inner_js_customize($script_text);
					}
					
					if(!empty($this->settings['hook_inner_js_customize'])){
						$code = str_replace('$script_text','$args[0]',$this->settings['hook_inner_js_customize']);
						$script_text = $this->hookCallbackFunction($code,$script_text);
					}
					if($tag = $this->loadScriptTagInUrl($script_text,$si)){
						$this->w3StrReplaceSet($script,$tag);
						continue;
					}
					if(!empty($force_innerjs_to_lazy_load)){
                        foreach($force_innerjs_to_lazy_load as $js){
                            if(strpos($script_text,$js) !== false){
                                $exclude_js_bool=0;
								$force_js_bool = 1;
                                break;
                            }
                        }
                    }
                    if(!empty($exclude_js_bool) && $exclude_js_bool != 2){
						$script_text = $this->unlazyLoadBackgroundImageJavascript($script_text);
						if(function_exists('w3speedup_inner_js_customize')){
							$this->w3StrReplaceSet($script,'<script>'.$script_text.'</script>');
						}
						$this->w3StrReplaceSet($script,'<script>'.$script_text.'</script>');
					}else{
						if(!empty($script_obj['type']) && $script_obj['type'] != 'text/javascript'){
							$script_obj['data-w3-type']= $script_obj['type'];
						}
						if($exclude_js_bool == 2){
							$script_modified = '<script type="lazyJs" ';
						}elseif($force_js_bool){
    						$script_modified = '<script type="lazyExJs" ';
    					}else{
    						$script_modified = '<script type="lazyJs" ';
    					}
    					foreach($script_obj as $key => $value){
                            if($key != 'type' && $key != 'html'){
                                $script_modified .= $key.'="'.$value.'" ';
                            }
                        }
						if(!empty($this->settings['load_combined_js']) && $this->settings['load_combined_js'] == 'after_page_load' /*&& !empty($force_js_bool)*/){
							$script_text = $this->w3_changes_in_js($script_text);
						}
						$script_text = $this->unlazyLoadBackgroundImageJavascript($script_text);
						$script_modified = $script_modified.'>'.$script_text.'</script>';
						
						$this->w3StrReplaceSet($script,$script_modified);
						
						
					}
                }
				if($si == count($script_links)-1 && !empty($final_merge_has_js)){
					if(!empty($final_merge_js) && count($final_merge_js) > 0){
						$cache_js_url = $this->w3CreateJsCombinedCacheFile($final_merge_js, $enable_cdn && $enable_cdn_path);
						$this->w3ReplaceJsFilesWithCombinedFiles($final_merge_has_js, $cache_js_url);
						$final_merge_js = array();
					}
				}
            }
			if(!empty($this->settings['custom_javascript'])){
			   if(!empty($this->settings['custom_javascript_file'])){    
					$custom_js_path = $this->w3GetCachePath('all-js').'/wnw-custom-js.js';
					if(!is_file($custom_js_path)){
						$this->w3CreateFile($custom_js_path, stripslashes($this->settings['custom_javascript']));
					}
					$custom_js_url = $this->w3GetCacheUrl('all-js').'/wnw-custom-js.js';
					$custom_js_url = $enable_cdn && $enable_cdn_path ? str_replace($this->add_settings['site_url'],$this->add_settings['image_home_url'] ,$custom_js_url) : $custom_js_url;
					$position = strrpos($this->html,'</body>');
					$rand = wp_rand(10,1000);
					$this->html = substr_replace( $this->html, '<script '.(!empty($this->settings['custom_javascript_defer']) ? 'defer="defer"' : '').' id="wnw-custom-js" src="'.$custom_js_url.'?ver='.$rand.'"></script>', $position, 0 );
				}else{
					$position = strrpos($this->html,'</body>');
					$this->html = substr_replace( $this->html, '<script>'.stripslashes($this->settings['custom_javascript']).'</script>', $position, 0 ); 
				}
			}
		}
        
        
    }
	
	function w3CheckJsIfExcluded($inner_js, $exclude_inner_js){
		$exclude_js_bool=0;
		if(strpos($inner_js,'moment.') === false && strpos($inner_js,'wp.') === false && strpos($inner_js,'.noConflict') === false && strpos($inner_js,'wp.i18n') === false){
			$exclude_js_bool=2;
		}
		if(strpos($inner_js,'DOMContentLoaded') !== false || strpos($inner_js,'jQuery(') !== false || strpos($inner_js,'$(') !== false || strpos($inner_js,'jQuery.') !== false || strpos($inner_js,'$.') !== false){
			$exclude_js_bool=2;
		}
		
		if(!empty($exclude_inner_js)){
			foreach($exclude_inner_js as $js){
				if(!empty($js) && strpos($inner_js,$js) !== false){
					return 1;
					break;
				}
			}
		}
		if(function_exists('w3_inner_js_excluded')){
			$exclude_js_bool = w3_inner_js_excluded($exclude_js_bool,$inner_js);
		}
		
		
		if(!empty($this->settings['hook_inner_js_exclude'])){
			$code = str_replace(array('$exclude_js_bool','$inner_js'),array('$args[0]','$args[1]'),$this->settings['hook_inner_js_exclude']);
			$exclude_js_bool = $this->hookCallbackFunction($code,$exclude_js_bool,$inner_js);
		}
		return $exclude_js_bool;
	}
	
	
	function w3ReplaceJsFilesWithCombinedFiles($final_merge_has_js,$cache_js_url){
		if(!empty($final_merge_has_js)){
			$lazy_load_js = !empty($this->settings['load_combined_js']) && $this->settings['load_combined_js'] == 'after_page_load' ? 1 : 0;
			for($ii = 0; $ii < count($final_merge_has_js); $ii++){
				if($ii == count($final_merge_has_js) -1 ){
					$this->w3StrReplaceSet($final_merge_has_js[$ii],'<script type="lazyJs" src="'.$cache_js_url.'"></script>');
				}else{
					$this->w3StrReplaceSet($final_merge_has_js[$ii],'');
				}
			}
		}
	}
	
	function w3CreateJsCombinedCacheFile($final_merge_js, $enable_cdn){
		$file_name = is_array($final_merge_js) ? $this->add_settings['w3_rand_key'].'-'.implode('-', $final_merge_js) : '';
		if(!empty($file_name)){
			$js_file_name = md5($file_name).$this->add_settings['js_ext'];
			if(!is_file($this->w3GetCachePath('all-js').'/'.$js_file_name)){
				$all_js = '';
				foreach($final_merge_js as $key => $script_path){
					$all_js .= $this->w3speedsterGetContents($this->add_settings['document_root'].$script_path).";\n";
				}
				$this->w3CreateFile($this->w3GetCachePath('all-js').'/'.$js_file_name, $all_js);
			}
			$main_js_url = $this->w3GetCacheUrl('all-js').'/'.$js_file_name;
			$main_js_url = $enable_cdn ? str_replace($this->add_settings['site_url'],$this->add_settings['image_home_url'] ,$main_js_url) : $main_js_url;
			return $main_js_url;
		}
	}
    function w3LazyLoadJavascript(){
		$enable_cdn = 0;
		if($this->add_settings['image_home_url'] != $this->add_settings['site_url'] ){
			$enable_cdn = 1;
		}
		$exclude_cdn_arr = !empty($this->add_settings['exclude_cdn']) ? $this->add_settings['exclude_cdn'] : array();
		$lazy_load_by_px = !empty($this->settings['lazy_load_px']) ? (int)$this->settings['lazy_load_px'] : 200;
        $script = 'var w3elem;setTimeout(function(){w3elem = window.innerWidth<768?\'touchstart\':\'click\';});var w3LazyloadByPx='.$lazy_load_by_px.', blankImageWebpUrl = "'. (($enable_cdn && !in_array('.png',$exclude_cdn_arr)) ? str_replace($this->add_settings['site_url'],$this->add_settings['image_home_url'],$this->add_settings['upload_base_url']): $this->add_settings['upload_base_url']).'/blank.pngw3.webp", w3UploadPath="'.$this->add_settings['upload_path'].'", w3WebpPath="'.$this->add_settings['webp_path'].'", w3LazyloadJs = '.(!empty($this->settings['load_combined_js']) && $this->settings['load_combined_js'] == 'after_page_load' ? 1 : 0).', w3JsIsExcluded = '.(!empty($this->settings['js_is_excluded']) ? 1 : 0).', w3Inp = '.(!empty($this->settings['enable_inp']) ? 1 : 0).',';
		if((!empty($this->settings['exclude_page_from_load_combined_js']) && $this->w3CheckIfPageExcluded($this->settings['exclude_page_from_load_combined_js'])) || empty($this->settings['js']) ){
			$script.='w3ExcludedJs=1;';
        }else{
			$script.='w3ExcludedJs=0;'; 
		}
		return $script.$this->w3speedsterGetContents(W3SPEEDSTER_DIR.'assets/js/script-load.min.js');
	}
	function w3LazyLoadImages(){
		$inner_script_optimizer = $this->w3speedsterGetContents(W3SPEEDSTER_DIR.'assets/js/img-lazyload.js');
        $custom_js_path = $this->w3GetCachePath('all-js').'/wnw-custom-inner-js.js';
        if(!is_file($custom_js_path)){
            $this->w3CreateFile($custom_js_path,$this->w3CompressJs($inner_script_optimizer));
        }
        return $this->w3speedsterGetContents($custom_js_path);
    
    }
	function loadScriptTagInUrl($script_tag,$i){
		$load_script_tag_in_url= !empty($this->settings['load_script_tag_in_url']) ? explode("\r\n", $this->settings['load_script_tag_in_url']) : array();
		$scriptContentFile = array();
		$file_name = w3GetOption('w3_rand_key').$i;
		foreach($load_script_tag_in_url as $ex_script){
			if(!empty($ex_script) && !empty($script_tag) && strpos($script_tag, $ex_script) !== false){
				$file_name .= $ex_script;
				$scriptContentFile = $script_tag;
				break;
			}
		}
		if(!empty($scriptContentFile)){
			$file_name_cache = md5($file_name).'.js';
			if(!file_exists($this->w3CheckFullUrlCachePath().'/'.$file_name_cache)){
				$this->w3CreateFile($this->w3CheckFullUrlCachePath().'/'.$file_name_cache,$scriptContentFile);
			}
			$defer = 'type="lazyJs" src=';
			return '<script '.$defer.'"'.$this->w3GetFullUrlCache().'/'.$file_name_cache.'"></script>';
		}
		return false;
	}
}