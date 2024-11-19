<?php
namespace W3speedster;
checkDirectCall();
class w3speedster_css extends w3speedster{
    
    function w3RemoveCssComments( $minify ){
		$minify = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $minify );
		return $minify;
	}
	function w3CssCompress( $minify ){
    	$minify = str_replace( array("\r\n", "\r", "\n", "\t",'  ','    ', '    '), ' ', $minify );
		$minify = str_replace( array(": ", ":: "), array(':','::'), $minify );
    	return $minify;
    }
	function w3RelativeToAbsolutePath($url, $string){
		
		$url_new = $url;
		$url_arr = $this->w3ParseUrl($url);
        $url = $this->addSettings['siteUrl'].$url_arr['path'];
        
        if(strpos($string,'@import "') !== false || strpos($string,"@import '") !== false){
           $string = preg_replace('/(@import\s*)[\"|\'](.*)(\.css)[\"|\']/', '$1url("$2$3")', $string);
        }
        $matches = $this->w3GetTagsData($string,'url(',')');
		return $this->w3ConvertArrRelativeToAbsolute($string, $url, $matches);
    
    }
	function w3ConvertArrRelativeToAbsolute($string, $url, $matches){
		$webp_enable = $this->addSettings['webp_enable'];
		$replaced = array();
		$replaced_new = array();
		$replace_array = explode('/',str_replace('\'','/',$url));
		array_pop($replace_array);
		$url_parent_url = implode('/',$replace_array);
		if($this->addSettings['isMultisiteSubDomain']){
			$url_parent_url = str_replace($this->addSettings['siteUrl'],$this->addSettings['network_site_url'], $url_parent_url);
		}
		$theme_root_array = explode('/',$this->addSettings['theme_base_url']);
		$theme_root = array_pop($theme_root_array);
		foreach($matches as $match){
			if(strpos($match,'{{') !== false || strpos($match,'data:') !== false || strpos($match,'chrome-extension:') !== false){
    
                continue;
    
			}
		    $org_match = $match;
    
            $match1 = str_replace(array('url(',')',"url('","')",')',"'",'"','&#039;'), '', html_entity_decode($match));
    
            $match1 = $this->esc_url($match1);
			
            if(strpos($match1,'//') > 7){
    
                $match1 = substr($match1, 0, 7).str_replace('//','/', substr($match1, 7));
    
            }
    
            if(empty($match1) || strpos(substr($match1, 0, 1),'#') !== false){
				continue;
			}
			if($this->addSettings['isMultisiteSubDomain'] && strpos($match1,$this->addSettings['siteUrl']) != false){
				$match1 = str_replace($this->addSettings['siteUrl'],$this->addSettings['network_site_url'],$match1);
			}
            if(strpos($match,'cdnjs.cloudflare.com') !== false){
				$img_arr = explode('?',$match1 );
				$ext = pathinfo($img_arr[0], PATHINFO_EXTENSION);
                if(strpos($url,'index.php') === false && $ext == 'css'){
					$response = $this->w3RemoteGet($match1);
					if(!empty($response)){
						$string = str_replace('@import '.$match.';',$response, $string);
					}
					continue;
				}
            }
			if(strpos($match,'fonts.googleapis.com') !== false){
                if(strpos($url,'index.php') !== false){
					$string = $this->w3CombineGoogleFonts($match1) ? str_replace('@import '.$match.';','', $string) : $string;
				}else{
					$match_arr = $this->w3ParseUrl($match1);
					$match_arr['scheme'] = empty($match_arr['scheme']) ? 'https' : $match_arr['scheme'];
					$match_arr['query'] = empty($match_arr['query']) ? '' : '?'.$match_arr['query'];
					$response = $this->w3RemoteGet($match_arr['scheme'].'://'.$match_arr['host'].$match_arr['path'].$match_arr['query']);
					if(!empty($response)){
						$string = str_replace('@import '.$match.';',$response, $string);
					}
				}
                continue;
			}
			
			if(strpos($match,'../fonts/fontawesome-webfont.') !== false){
                $font_text = str_replace('../','',$match1);
                $font_text = str_replace('fonts/fontawesome-webfont.','',$font_text);
                $string = str_replace($match,'url(https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/fonts/fontawesome-webfont.'.$font_text.')',$string);
                continue;
			}
			$match1 = str_replace($this->addSettings['imageHomeUrl'],$this->addSettings['siteUrl'],$match1);
			
			if($this->w3IsExternal($match1)){
                continue;
			}
			
			$match_arr = $this->w3ParseUrl($match1);
			
			if(substr($match1, 0, 1) == '/' || strpos($match1,'http') !== false){
				if($this->addSettings['isMultisiteSubDomain']){
					$match1 = file_exists($this->addSettings['documentRoot'].'/'.trim($match_arr['path'],'/')) ? $this->addSettings['network_site_url'].'/'.trim($match_arr['path'],'/') : $match1;
				}else{
					$match1 = file_exists($this->addSettings['documentRoot'].'/'.trim($match_arr['path'],'/')) ? $this->addSettings['siteUrl'].'/'.trim($match_arr['path'],'/') : $match1;
				}
				$import_match = $match1;
			}else{
				$match1 = $url_parent_url.'/'.trim($match_arr['path'],'/');
				$import_match = $url_parent_url.'/'.trim($match_arr['path'],'/');
				$match_arr = $this->w3ParseUrl($match1);
			}
			if(strpos($match1,'.css')!== false && strpos($string,'@import '.$match)!== false && $url != $this->addSettings['homeUrl'].'/index.php'){
                $string = str_replace('@import '.$match.';',$this->w3RelativeToAbsolutePath($this->removeDotPathSegments($import_match),$this->w3speedsterGetContents($this->removeDotPathSegments(str_replace($this->addSettings['siteUrl'],$this->addSettings['documentRoot'],$import_match)))), $string);
                continue;
			}
			$img_arr = explode('?',$match1 );
			$ext = '.'.pathinfo($img_arr[0], PATHINFO_EXTENSION);
			if($ext == '.'){
                continue;
			}
			if(strpos($img_arr[0],$theme_root) !== false){
				$img_root_path = rtrim($this->addSettings['theme_base_dir'],'/');
				$img_root_url = rtrim($this->addSettings['theme_base_url'],'/');
			}else{
				$img_root_path = $this->addSettings['upload_base_dir'];
				$img_root_url = $this->addSettings['upload_base_url'];
			}
			
			$webp_enable = $this->addSettings['webp_enable'];
			$webp_enable_instance = $this->addSettings['webp_enable_instance'];
			$webp_enable_instance_replace = $this->addSettings['webp_enable_instance_replace'];
			$imgsrc_filepath = strpos($this->addSettings['siteUrl'].'/'.trim($match_arr['path'],'/'),$img_root_url) !== false ? str_replace($img_root_url,'',$this->addSettings['siteUrl'].'/'.trim($match_arr['path'],'/')) : '';
			if($this->addSettings['is_mobile'] && !empty($this->settings['resp_bg_img']) && (strpos($url,'index.php') !== false || !empty($this->settings['separate_cache_for_mobile']) ) && strpos($imgsrc_filepath,$ext) !== false && file_exists(str_replace($ext,'-595xh'.$ext,$img_root_path.$imgsrc_filepath))){
				$match1 = str_replace($ext,'-595xh'.$ext,$match1);
				$imgsrc_filepath = str_replace($ext,'-595xh'.$ext,$imgsrc_filepath);
			}
			$imgsrc_webpfilepath = !empty($imgsrc_filepath) ? str_replace($this->addSettings['uploadPath'],$this->addSettings['webp_path'],$img_root_path).$imgsrc_filepath.'w3.webp' : '';
			if(in_array($ext, $webp_enable) && !empty($imgsrc_webpfilepath)){
				if(file_exists($imgsrc_webpfilepath) && (!empty($this->addSettings['disable_htaccess_webp']) || !file_exists($this->addSettings['documentRoot']."/.htaccess") || $this->addSettings['imageHomeUrl'] != $this->addSettings['siteUrl'] )){
					//$match1 = str_replace($this->addSettings['uploadPath'],$this->addSettings['webp_path'],$img_arr[0]).'w3.webp';
					$match1 = rtrim(str_replace($webp_enable_instance,$webp_enable_instance_replace,$match1.'"'),'"');
				}else{
					if(!empty($this->settings['opt_img_on_the_go'])){
						$response = $this->w3OptimizeAttachmentUrl(str_replace($this->addSettings['siteUrl'],$this->addSettings['documentRoot'],$img_arr[0]));
					}
				}
			}
			if(substr($match1, 0, 1) == '/' || substr($match1, 0, 4) == 'http'){
				if($this->addSettings['imageHomeUrl'] == $this->addSettings['siteUrl']){
					if($this->addSettings['isMultisiteSubDomain']){
						$match1 = str_replace($this->settings['network_site_url'],$this->settings['siteUrl'],$match1);
					}
					$replacement = 'url('.$match1.')';
				}
				else{
					$match_arr = $this->w3ParseUrl($match1);
					$replacement = 'url('.$this->addSettings['siteUrl'].'/'.trim($match_arr['path'],'/').')';
				}
			}else{
				if($this->addSettings['isMultisiteSubDomain']){
					$match1 = str_replace($this->settings['network_site_url'],$this->settings['siteUrl'],$match1);
				}
				$match_arr = $this->w3ParseUrl($match1);
				$replacement = 'url('.$url_parent_url.'/'.trim($match_arr['path'],'/').')';
			}
			
			if($this->addSettings['imageHomeUrl'] != $this->addSettings['siteUrl']){
				if(empty($this->addSettings['exclude_cdn']) || !in_array($ext,$this->addSettings['exclude_cdn'])){
					$replacement  = str_replace($this->addSettings['siteUrl'],$this->addSettings['imageHomeUrl'],$replacement );
				}
			}
			
			if(strpos($url,'index.php') !== false){
				$this->w3StrReplaceSetImg($org_match, $replacement);
			}else{
            	$string = str_replace($org_match, $replacement, $string);
			}
        }
		return $string;
	}
	
	function w3CreateFileCacheCss($path){
		$file_name = $this->w3GetOption('w3_rand_key');
		$new_path = $this->addSettings['css_ext'] != '.css' ? $this->rightReplace($path,'.css',$this->addSettings['css_ext']) : $path ;
        $cache_file_path = $this->w3GetCachePath('css').'/'.$file_name.'/'.ltrim($new_path,'/');
        if( !file_exists($cache_file_path) ){
			$css = $this->w3speedsterGetContents($this->addSettings['documentRoot'].$path);
			$css = str_replace(array('@charset "utf-8";','@charset "UTF-8";'),'',$css);
			
			if(function_exists('w3speedup_internal_css_customize')){
				$css = w3speedup_internal_css_customize($css,$path);
			}
			
			if(!empty($this->settings['hook_internal_css_customize'])){
				$code = str_replace(array('$css','$path'),array('$args[0]','$args[1]'),$this->settings['hook_internal_css_customize']);
				$css = $this->hookCallbackFunction($code,$css,$path);
			}
			$css = $this->w3RemoveCssComments($css);
			$minify = $this->w3RelativeToAbsolutePath($this->addSettings['homeUrl'].$path,$css);
			$css_minify = 1;
			if(function_exists('w3speedup_internal_css_minify')){
				$css_minify = w3speedup_internal_css_minify($path,$css);
			} 
			
			if(!empty($this->settings['hook_internal_css_minify'])){
				//$css_minify = $this->w3SpeedsterInternalCssMinify($css,$path,$css_minify);
				$code = str_replace(array('$css_minify','$css','$path'),array('$args[0]','$args[1]','$args[2]'),$this->settings['hook_internal_css_minify']);
				
				$css_minify = $this->hookCallbackFunction($code,$css_minify,$css,$path);
			}
			
			if($css_minify){
				$minify = $this->w3CssCompress($minify);
			}
			$this->w3CreateFile($cache_file_path, $minify);
		}
        if(!file_exists($cache_file_path)){
			return $path;
		}else{
			return str_replace($this->addSettings['documentRoot'],'',$cache_file_path);
		}
    }
    
    function minifyCss($css_links){ 
		if(!empty($this->settings['exclude_page_from_load_combined_css']) && $this->w3CheckIfPageExcluded($this->settings['exclude_page_from_load_combined_css'])){
			return $this->html;
		}
		if(!empty($css_links) && !empty($this->settings['css'])){
			$excludeCssFromMinify = !empty($this->settings['exclude_css']) ? explode("\r\n", $this->settings['exclude_css']) : array();
			$excludeCssFromMinifyArr = array();
			foreach($excludeCssFromMinify as $key => $value){
				if(strpos($value,' 1') !== false){
					$excludeCssFromMinifyArr[$key]['string'] = str_replace(' 1','',$value);
					$excludeCssFromMinifyArr[$key]['cache'] = 1;
				}else{	
					$excludeCssFromMinifyArr[$key]['string'] = $value;
					$excludeCssFromMinifyArr[$key]['cache'] = 0;
				}
			}
			$preload_css = $this->addSettings['preload_css'];
			$force_lazyload_css	= !empty($this->settings['force_lazyload_css']) ? explode("\r\n", $this->settings['force_lazyload_css']) : array();
			$force_lazyload_css = function_exists('w3_customize_force_lazyload_css') ? w3_customize_force_lazyload_css($force_lazyload_css) : $force_lazyload_css;
			
			if(!empty($this->settings['hook_customize_force_lazy_css'])){
			$code = str_replace('$force_lazyload_css','$args[0]',$this->settings['hook_customize_force_lazy_css']);
			$force_lazyload_css = $this->hookCallbackFunction($code,$force_lazyload_css);
			}
			$enable_cdn = 0;
			if($this->w3CheckEnableCdnExt('.css')){
				$enable_cdn = 1;
			}
			
			$css_links_arr = array();
			foreach($css_links as $key => $css){
				$css_obj = $this->w3ParseLink('link',str_replace($this->addSettings['imageHomeUrl'],$this->addSettings['siteUrl'],$css));
				if( !empty($css_obj['rel']) && strpos($css_obj['rel'],'stylesheet') !== false && !empty($css_obj['href']) ){
					$css_obj['rel'] = 'stylesheet';
					$css_links_arr[] = array('arr'=>$css_obj,'css'=>$css);
				}elseif(empty($css_obj['rel'])){
					$css_links_arr[] = array('arr'=>array(),'css'=>$css);
				}
			}
			foreach($css_links_arr as $key => $link_arr){
				$css = $link_arr['css'];
				$css_obj = $link_arr['arr'];
				$enable_cdn_path = 0;
				if(!empty($css_obj['rel']) && $css_obj['rel'] == 'stylesheet' && !empty($css_obj['href'])){
					if(!empty($css_obj['media']) && strtolower($css_obj['media']) == 'print'){
						continue;
					}
					
					$org_css = '';
					$media = '';
					$exclude_css = 0;
					if(!empty($excludeCssFromMinifyArr)){
						foreach($excludeCssFromMinifyArr as $ex_css){
							if(!empty($ex_css['string']) && strpos($css, $ex_css['string']) !== false){
								$exclude_css = 1;
								if(!empty($ex_css['cache'])){
									$cache_css = 1;
								}
							}
						}
					}
					if(function_exists('w3speedup_exclude_css_filter')){
						$exclude_css = w3speedup_exclude_css_filter($exclude_css,$script_obj,$script,$this->html);
					}
					
						
					if(!empty($this->settings['hook_exclude_css_filter'])){
						$code = str_replace(array('$exclude_css','$css_obj','$css','$html'),array('$args[0]','$args[1]','$args[2]','$args[3]'),$this->settings['hook_exclude_css_filter']);
						$exclude_css = $this->hookCallbackFunction($code,$exclude_css,$css_obj,$css,$this->html);
					}
					if($this->w3CheckEnableCdnPath($css_obj['href'])){
						$enable_cdn_path = 1;
					}
					if($exclude_css){
						if($this->w3Endswith($css_obj['href'], '.css')){
							if(!empty($cache_css)){
								$url_array = $this->w3ParseCssUrl($css_obj['href']);
								$org_css = $url_array['path'];
								$url_array['path'] = $this->w3CreateFileCacheCss($url_array['path']);
								$css_obj['href'] = $url_array['path'];
							}
							$css_obj['href'] = $this->w3GetCssUrl($css_obj['href'], $enable_cdn && $enable_cdn_path);
							$this->w3StrReplaceSetCss($css,'{{'.$css_obj['href'].'}}');
							$this->w3RenderCss(array($css_obj['href']),$css_links_arr, 1);
						}
						$this->addSettings['preload_resources']['all'][] = $css_obj['href'];
						continue;
					}
					$force_lazy_load = 0;
					if(!empty($force_lazyload_css)){
						foreach($force_lazyload_css as $ex_css){
							if(!empty($ex_css) && strpos($css, $ex_css) !== false){
								$force_lazy_load = 1;
							}
						}
					}
					if($force_lazy_load){
						$this->w3StrReplaceSetCss($css,str_replace(' href=',' data-href=',$css));
						continue;
					}
					if(!empty($css_obj['media']) && $css_obj['media'] != 'all' && $css_obj['media'] != 'screen'){
						$media = $css_obj['media'];
					}
					$url_array = $this->w3ParseCssUrl($css_obj['href']);
					if(strpos($url_array['path'],'./') !== false || strpos($url_array['path'],'../') !== false){
                    	$url_array['path'] = $this->removeDotPathSegments($url_array['path']);
                    }
					if(strpos($url_array['path'],'/version') !== false){
						$url_array['path'] = preg_replace('/version(\d)*\//', '', $url_array['path']);
					}
					if(strpos($this->addSettings['documentRoot'],'/pub') !== false && strpos($url_array['path'],'/pub') !== false){
						$url_array['path'] = str_replace('/pub/', '/', $url_array['path']);
					}
					if(!$this->w3IsExternal($css_obj['href'])){
						if($this->w3Endswith($css_obj['href'], '.php') || strpos($css_obj['href'], '.php?') !== false ){
							$org_css = $url_array['path'];
							$url_array['path'] = $css_obj['href'];
							$css_obj['href'] = $this->addSettings['homeUrl'].$url_array['path'];
						}elseif(!file_exists($this->addSettings['documentRoot'].$url_array['path'])){
							if($this->w3Endswith($css_obj['href'], '.css') || strpos($css_obj['href'], '.css?') !== false ){
								$this->w3StrReplaceSetCss($css,'');
								continue;
							}
						}elseif(filesize($this->addSettings['documentRoot'].$url_array['path']) > 0){
							$org_css = $url_array['path'];
							$url_array['path'] = $this->w3CreateFileCacheCss($url_array['path']);
							$css_obj['href'] = $url_array['path'];
						}else{
							if($this->w3Endswith($css_obj['href'], '.php') || strpos($css_obj['href'], '.php?') !== false || filesize($this->addSettings['documentRoot'].$url_array['path']) < 1 ){
								$this->w3StrReplaceSetCss($css,'');
							}
							continue;
						}
					}
					if(!empty($css_obj['href']) && strpos($css_obj['href'],'fonts.googleapis.com') !== false){
						$response = $this->w3CombineGoogleFonts($css_obj['href']);
						if($response){
							$this->w3StrReplaceSetCss($css,'');
						}
						$create_css_file = 0;
						continue;
					}
					
					$src = $css_obj['href'];
					if(!empty($src) && !$this->w3IsExternal($src) && $this->w3Endswith($src, '.css')){
						$filename = $this->addSettings['documentRoot'].$url_array['path'];
						if(file_exists($filename) && filesize($filename) > 0){
							$combined_css_file = $this->w3GetCssUrl($url_array['path'], $enable_cdn && $enable_cdn_path);
							$this->w3StrReplaceSetCss($css,'{{'.$combined_css_file.'}}');
							$combined_css_files[$key] = $combined_css_file;
						}
					}elseif($this->w3Endswith($src, '.css') || strpos($src, '.css?')){
						$this->w3StrReplaceSetCss($css,'{{'.$css_obj['href'].'}}');
						$combined_css_files[$key] = $css_obj['href'];
					}
				}
			}
			if(!empty($remove_css_tags)){
				foreach($remove_css_tags as $css){
					$this->w3StrReplaceSetCss($css,'');
				}
			}
			$appendonstyle = $this->w3GetPointerToInjectFiles($this->html);
			$css_defer = '';
			if(!empty($this->settings['load_critical_css'])){
				$ignore_critical_css = 0;
				if((function_exists('is_user_logged_in') && is_user_logged_in()) || $this->is_404()){
					$ignore_critical_css = 1;
				}
				if(function_exists('w3_no_critical_css')){
					 $ignore_critical_css = w3_no_critical_css($this->addSettings['full_url']);
				}
				if(!empty($this->settings['hook_no_critical_css'])){
					$url = $this->addSettings['full_url'];
					$code = str_replace(array('$ignore_critical_css','$url'),array('$args[0]','$args[1]'),$this->settings['hook_no_critical_css']);	
					$ignore_critical_css = $this->hookCallbackFunction($code,$ignore_critical_css,$url);
				}
				if(!$ignore_critical_css && is_array($combined_css_files) && count($combined_css_files) > 0){
					$file_name = 0;
					
					/*foreach($combined_css_files as $css_arr){
						$css = explode("/",$css_arr);
						$file_name .= end($css);
					}*/
					$file_name = count($combined_css_files);
					$main_css_file_name = md5($file_name).$this->addSettings['css_ext'];
					
					if(function_exists('w3speedup_customize_critical_css_filename')){
						$main_css_file_name = w3speedup_customize_critical_css_filename($main_css_file_name,$combined_css_files);
					}
					
					if(!empty($this->settings['hook_customize_critical_css_filename'])){
						$code = str_replace(array('$main_css_file_name','$combined_css_files'),array('$args[0]','$arg[1]'),$this->settings['hook_customize_critical_css_filename']);
						$main_css_file_name = $this->hookCallbackFunction($code,$main_css_file_name,$combined_css_files);
					} 
					$this->addSettings['critical_css'] = $main_css_file_name;
					if(file_exists($this->w3PreloadCssPath().'/'.md5(0).$this->addSettings['css_ext']) && !file_exists($this->w3PreloadCssPath().'/'.$this->addSettings['critical_css'])){
						// @codingStandardsIgnoreLine
						rename($this->w3PreloadCssPath().'/'.md5(0).$this->addSettings['css_ext'], $this->w3PreloadCssPath().'/'.$this->addSettings['critical_css']);
					}
					if(file_exists($this->w3PreloadCssPath().'/'.$this->addSettings['critical_css']) && !empty($this->settings['load_critical_css'])){
						/*if(function_exists('w3speedup_customize_critical_css')){
							$critical_css = file_get_contents($this->w3PreloadCssPath().'/'.$this->addSettings['critical_css']);
							$critical_css = w3speedup_customize_critical_css($critical_css);
							$this->w3CreateFile($this->w3PreloadCssPath().'/'.$this->addSettings['critical_css'], $critical_css);
						}*/
						$file = $this->w3GetFullUrlCachePath().'/critical_css.json';
						$this->w3CreateFile($file,$this->addSettings['critical_css']);
					}
				}
			}
			//}
			$all_inline_css = (!empty($this->settings['custom_css']) ? $this->w3CssCompress(stripslashes($this->settings['custom_css'])) : '');
			$this->w3InsertContentHead('<style id="w3_bg_load"></style><style>div[data-BgLz="1"],section[data-BgLz="1"],iframelazy[data-BgLz="1"], iframe[data-BgLz="1"]{background-image:none !important;}</style><style id="w3speedster-custom-css">'.$all_inline_css.'</style>',4);
			//$this->w3StrReplaceSetCss('</head>','<style id="w3speedster-custom-css">'.$all_inline_css.'</style></head>');
			$this->w3RenderCss($combined_css_files,$css_links_arr,0);
		}
		
	}
	function w3RenderCss($combined_css_files,$css_links_arr, $exclude=0){
		if(!empty($combined_css_files) && is_array($combined_css_files)){
			foreach($combined_css_files as $key=>$css){
				$this->addSettings['preload_resources']['css'][] = $css;
				if(!empty($css_links_arr[$key]['arr']) && count((array)$css_links_arr[$key]['arr']) > 0){
					$css_link = '';
					foreach((array)$css_links_arr[$key]['arr'] as $attr => $attr_value){
						if($attr != 'href' && $attr != 'data-href' && $attr != 'onload' && $attr != 'onerror' && $attr != 'type' && $attr != 'html' ){
							$css_link .= " $attr='$attr_value'";
						}
					}
				}
				$excludeCss = !$exclude ? ' data-css="1"' : '';
				$this->w3StrReplaceSetCss('{{'.$css.'}}','<link'.$excludeCss.' href="'.$css.'"'.$css_link.'>');
			}
		}
	}
	function w3ParseCssUrl($css_href){
		$url_array = $this->w3ParseUrl(str_replace($this->addSettings['homeUrl'],'',$css_href));
		$url_array['path'] = '/'.ltrim($url_array['path'],'/');
		return $url_array;
	}
	function w3GetCssUrl($css,$enable_cdn){
		if($enable_cdn){
			$css = str_replace($this->addSettings['siteUrl'],$this->addSettings['imageHomeUrl'],(strpos($css,$this->addSettings['siteUrl']) === false ? $this->addSettings['siteUrl'].'/' : '').ltrim($css,'/'));
		}elseif(strpos($css,$this->addSettings['siteUrl']) !== true){
			$css = $this->addSettings['siteUrl'].'/'.ltrim($css,'/');
		}
		return $css;
	}
	
}