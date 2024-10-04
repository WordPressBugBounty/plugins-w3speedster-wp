<?php
namespace W3speedster;
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class w3speedster_optimize_image extends w3speedster{
	public function __construct(){
		parent::__construct();
		include_once( ABSPATH . 'wp-admin/includes/image.php' );
	}
	
	function w3speedsterOptimizeSingleImage($attach_id){
		
		if(get_post_type($attach_id) == 'attachment'){
			$file = get_post_meta($attach_id,'_wp_attached_file',true);
			$file1 = get_attached_file($attach_id, true);
			$response = $this->w3OptimizeAttachment($this->add_settings['upload_base_url'].'/'.trim($file,'/'), 0, false,$attach_id);
			if(!empty($response['img'])	&& $response['img'] == 1){
				$metadata = wp_get_attachment_metadata($attach_id);
				if(!empty($metadata['sizes'])){
					$file = explode('/',$metadata['file']);
					array_pop($file);
					$file = implode('/',$file);
					foreach($metadata['sizes'] as $key => $sizes){
						wp_delete_file(realpath($this->add_settings['upload_base_dir'].'/'.$file.'/'.$sizes['file']));
					}
				}
			}
			
			
			$metadata = wp_generate_attachment_metadata($attach_id,get_attached_file($attach_id, true));
			
			if(!empty($metadata)){
				wp_update_attachment_metadata( $attach_id, $metadata );
				$ext = !empty($metadata['file']) ? pathinfo($metadata['file'], PATHINFO_EXTENSION) : '';
				if(!empty($metadata['sizes']) && !empty($ext) && ((!empty($this->settings['webp_jpg']) && in_array($ext,array('jpg','jpeg'))) || (!empty($this->settings['webp_png']) && $ext == 'png'))){
					$file = explode('/',$metadata['file']);
					array_pop($file);
					$file = implode('/',$file);
					$image_size = getimagesize($this->add_settings['upload_base_dir'].'/'.trim($metadata['file'],'/'));
					$response = $this->w3OptimizeAttachmentWebp($this->add_settings['upload_base_url'].'/'.trim($metadata['file'],'/'), $image_size[0], true , $this->add_settings['upload_base_url'].'/'.trim($metadata['file'],'/'),$this->add_settings['upload_base_dir'].'/'.trim($metadata['file'],'/'));
					foreach($metadata['sizes'] as $key=>$thumb){
						$response = $this->w3OptimizeAttachmentWebp($this->add_settings['upload_base_url'].'/'.$file.'/'.$thumb['file'], $thumb['width'], true , $this->add_settings['upload_base_url'].'/'.trim($metadata['file'],'/'),$this->add_settings['upload_base_dir'].'/'.$file.'/'.$thumb['file']);
					}
				}
			}
			return $response;
		}
		return false;
	}
	function w3speedsterChangeImageName($metadata, $attachment_id, $context){
		
		if(empty($metadata['file'])){
			$metadata = wp_get_attachment_metadata($attachment_id);
		}
		if(empty($metadata['file'])){
			return $metadata;
		}
		
		$file = explode('/',$metadata['file']);
		array_pop($file);
		$file = implode('/',$file);
		if(!empty($metadata['sizes']['w3speedup-mobile'])){
			$new_thumb_name = str_replace('x'.$metadata['sizes']['w3speedup-mobile']['height'],'xh',$metadata['sizes']['w3speedup-mobile']['file']);
			if(is_file($this->add_settings['upload_base_dir'].'/'.$file.'/'.$metadata['sizes']['w3speedup-mobile']['file'])){
				// @codingStandardsIgnoreLine
				rename($this->add_settings['upload_base_dir'].'/'.$file.'/'.$metadata['sizes']['w3speedup-mobile']['file'],$this->add_settings['upload_base_dir'].'/'.$file.'/'.$new_thumb_name);
			}
			$metadata['sizes']['w3speedup-mobile']['file'] = $new_thumb_name;
		}
		
		return $metadata;
	}
	function w3OptimizeAttachmentId($attach_id){
		$file_url = wp_get_attachment_url( $attach_id );
 		$filetype = wp_check_filetype( $file_url );
		if(!in_array($filetype['ext'], array('png','jpg','jpeg','webp'))){
			return true;
		}
		$response = $this->w3speedsterOptimizeSingleImage($attach_id);
		
	}
	
	
	function w3speedsterOptimizeImageCallback(){
		
		global $wpdb;
		if(!empty($this->add_settings['wp_get']['start_type']) && $this->add_settings['wp_get']['start_type'] == 2){
			w3UpdateOption('w3speedup_opt_offset',0);
		}
		if(empty($this->settings['opt_jpg_png']) && empty($this->settings['webp_jpg']) && empty($this->settings['webp_png'])){
			wp_clear_scheduled_hook('w3speedup_image_optimization');
		}
		if(!empty($this->settings['opt_img_on_the_go'])){
			
			$opt_priority = w3GetOption('w3speedup_opt_priortize');
			$opt_offset = w3GetOption('w3speedup_opt_offset');
			$attach_arr = array();
			if(!empty($opt_priority)){
				$i = 0;
				foreach($opt_priority as $key => $attach_id){
					if(strpos($attach_id,'/themes/') !== false || strpos($attach_id,'/plugins/') !== false){
						$imgUrl = str_replace($this->add_settings['wp_document_root'],$this->add_settings['wp_site_url'],$attach_id);
						$image_size = getimagesize($imgUrl);
						$this->w3OptimizeAttachment($imgUrl,0,false);
						$this->w3OptimizeAttachmentWebp($imgUrl,$image_size[0]);
					}else{
						$this->w3OptimizeAttachmentId($attach_id);
					}
					$attach_arr[] = $key; 
					unset($opt_priority[$key]);
					if(++$i > 1){
						break;
					}
				}
				w3UpdateOption('w3speedup_opt_priortize',$opt_priority);
				echo wp_json_encode(array_merge(array('offset'=>-1),$attach_arr));
				exit;
			}
		}
		$opt_offset = get_option('w3speedup_opt_offset');
		$opt_offset = !empty($opt_offset) ? $opt_offset : 0;
		$new_offset = $opt_offset;
		$upload_dir = wp_upload_dir();
		$offset_limit = !empty($this->add_settings['wp_get']['w3_limit']) && (int)$this->add_settings['wp_get']['w3_limit'] > 0 ? (int)$this->add_settings['wp_get']['w3_limit'] : 1;
		global $w3_network_option;
        if(w3CheckMultisite() && empty($w3_network_option['manage_site_separately'])){
			$current_blog = get_current_blog_id();
			$img_to_opt = 0;
			$blogs = get_sites();
			foreach( $blogs as $b ){
				$img_to_opt = $wpdb->get_var($wpdb->prepare("SELECT count(ID) FROM %s%s_posts WHERE post_type='attachment'",array($wpdb->prefix,$b->blog_id)));
				if($opt_offset < $img_to_opt){
					$attach_arr = $wpdb->get_col($wpdb->prepare("SELECT ID FROM %s%s_posts WHERE post_type='attachment' limit %d,%d",
				array( $wpdb->prefix,$b->blog_id,$opt_offset,$offset_limit )));
					switch_to_blog($b->blog_id);
					break;
				}
				$opt_offset = $opt_offset - $img_to_opt;
			} 
		}else{
			$attach_arr = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT ID FROM {$wpdb->prefix}posts WHERE post_type='attachment' LIMIT %d, %d",
					$opt_offset,
					$offset_limit
				)
			);
		}
		
		if(!empty($attach_arr) && count($attach_arr) > 0){
			foreach($attach_arr as $attach_id){
				$image_url_path = get_attached_file($attach_id, true); 
				if(file_exists($image_url_path)){
                    $image_size = getimagesize($image_url_path);
                    if($image_size[0] > 3000 || $image_size[1] > 3000){
                        //nothing
                    }else{
						$this->w3OptimizeAttachmentId($attach_id);
                    }
                }
				$new_offset++;
				if(w3CheckMultisite()){
					switch_to_blog($current_blog);
				}
				w3UpdateOption('w3speedup_opt_offset',$new_offset,'no');
                
			}
		}else{
			wp_clear_scheduled_hook('w3speedup_image_optimization');
		}
		echo wp_json_encode(array_merge(array('offset'=>$new_offset),$attach_arr));
		exit;
	}
	function w3OptimizeAttachment($image_url,$image_width=0,$thumb=false, $main_image='', $overwrite=false,$attach_id=0){
		$theme_root_array = explode('/',$this->add_settings['theme_base_url']);
		$theme_root = array_pop($theme_root_array);
		$upload_dir = wp_upload_dir();
		$webp_jpg = !empty($this->settings['webp_jpg']) ? 1 : 0;
		$webp_png = !empty($this->settings['webp_png']) ? 1 : 0;
		$optimize_image = !empty($this->settings['opt_jpg_png']) ? 1 : 0;
		$type = pathinfo($image_url, PATHINFO_EXTENSION);
		if(strpos($image_url,$theme_root) !== false){
			$img_root_path = rtrim($this->add_settings['theme_base_dir'],'/');
			$img_root_url = rtrim($this->add_settings['theme_base_url'],'/');
		}else{
			$img_root_path = $this->add_settings['upload_base_dir'];
			$img_root_url = $this->add_settings['upload_base_url'];
			
		}
		
		$image_url_path = str_replace($img_root_url,$img_root_path,$image_url);
		
		$url_array = $this->w3ParseUrl($image_url);
		$image_size = !empty($image_width) ? array($image_width) : getimagesize($image_url_path);
		$image_type = array('gif','jpg','png','jpeg','webp');
		if( $optimize_image && in_array($type,$image_type) && ($overwrite == true || (!is_file($image_url_path.'org.'.$type) && $thumb == false) || (!empty($main_image) && $thumb == true && !is_file($image_url_path.'org.'.$type) ) ) ){
			if($image_size[0] > 3000){
				$return['img'] = 3;/*copy($this->add_settings['wp_document_root'].$url_array['path'],$this->add_settings['wp_document_root'].$url_array['path'].'org.'.$type);
				$image_size[0] = 1920;
				$this->w3speedsterResizeImage( $this->add_settings['wp_document_root'].$url_array['path'].'org.'.$type, $this->add_settings['wp_document_root'].$url_array['path'], $image_size[0]);*/
				return $return;
			}
			$optmize_image = $this->optimizeImage($image_size[0],$image_url);
			if(function_exists('imagecreatefromstring')){
				$optimize_image_size = @imagecreatefromstring($optmize_image);
			}
			
			if(empty($optimize_image_size)){
				$return['img'] = 2;
			}else{
				if(!is_file($image_url_path.'org.'.$type) && !$thumb){
					// @codingStandardsIgnoreLine
					@rename($image_url_path,$image_url_path.'org.'.$type);
				}
				wp_delete_file($image_url_path);
				$this->w3CreateFile($image_url_path, $optmize_image);
				$return['img'] = 1;
			}
		}else{
			$return['img'] = 0;
		}
		
		return $return;
    }
	function w3OptimizeAttachmentWebp($image_url,$image_width=0,$thumb=false, $main_image='', $image_url_path='',$overwrite=false,$attach_id=0){
		$type = pathinfo($image_url, PATHINFO_EXTENSION);
		$theme_root_array = explode('/',$this->add_settings['theme_base_url']);
		$theme_root = array_pop($theme_root_array);
		if(strpos($image_url,$theme_root) !== false){
			$img_root_path = rtrim($this->add_settings['theme_base_dir'],'/');
			$img_root_url = rtrim($this->add_settings['theme_base_url'],'/');
		}else{
			$img_root_path = $this->add_settings['upload_base_dir'];
			$img_root_url = $this->add_settings['upload_base_url'];
			
		}
		
		$image_url_path = str_replace('\\','/',$image_url);
		$image_url_path = str_replace($img_root_url,$img_root_path,$image_url_path);
		$image_size = !empty($image_width) ? array($image_width) : getimagesize($image_url_path);
		$webp_path = strpos($image_url_path, $this->add_settings['upload_path']) !== false ? str_replace($this->add_settings['upload_path'],$this->add_settings['webp_path'],$image_url_path) : $this->add_settings['wp_document_root'].$this->add_settings['webp_path'].$image_url_path;
		//echo 'rocket'.$image_url_path; exit;
		//echo 'rocket'.$webp_path.'w3.webp'; exit;
		if(!is_file($webp_path.'w3.webp') && is_file($image_url_path)){
			$webp_path_arr = explode('/',$webp_path);
			array_pop($webp_path_arr); 
			$optmize_image = $this->optimizeImage($image_width,$image_url,1);
			$this->w3CreateFile($webp_path.'w3.webp', $optmize_image);
			// @codingStandardsIgnoreLine
			chmod($webp_path.'w3.webp', 0644);
			if(filesize($webp_path.'w3.webp') < 1024){
				wp_delete_file($webp_path.'w3.webp');
				$return['webp'] = 0;
			}else{
				$return['webp']=1;
			}
			/*if(!empty($return['webp']) && $return['webp'] == 1){
				$this->w3_create_sub_sizes();
			}*/
		}
			
		
	}
	function w3speedsterResizeImage( $file, $dest_path, $max_w) {
		$image = wp_get_image_editor( $file );
		if ( !is_resource( $image ) )
			return new WP_Error( 'error_loading_image', $image, $file );

		$size = @getimagesize( $file );
		if ( !$size )
			return new WP_Error('invalid_image', __('Could not read image size'), $file);
		list($orig_w, $orig_h, $orig_type) = $size;

		$dst_h = $orig_h*$max_w /$orig_w ;
		$dst_w = $max_w;
		

		$newimage = wp_imagecreatetruecolor( $dst_w, $dst_h );

		
		imagecopyresampled( $newimage, $image, 0, 0, 0, 0, $dst_w, $dst_h, $orig_w, $orig_h);

		if ( IMAGETYPE_PNG == $orig_type && function_exists('imageistruecolor') && !imageistruecolor( $image ) )
			imagetruecolortopalette( $newimage, false, imagecolorstotal( $image ) );

		imagedestroy( $image );

		$info = pathinfo($file);
		$dir = $info['dirname'];
		$ext = $info['extension'];
		$name = wp_basename($file, ".$ext");

		if ( !is_null($dest_path) and $_dest_path = realpath($dest_path) )
			$dir = $_dest_path;
		$destfilename = $dest_path;

		if ( IMAGETYPE_GIF == $orig_type ) {
			if ( !imagegif( $newimage, $destfilename ) )
				return new WP_Error('resize_path_invalid', __( 'Resize path invalid' ));
		} elseif ( IMAGETYPE_PNG == $orig_type ) {
			if ( !imagepng( $newimage, $destfilename ) )
				return new WP_Error('resize_path_invalid', __( 'Resize path invalid' ));
		} else {
			$destfilename = $dest_path;
			$return = imagejpeg( $newimage, $destfilename, apply_filters( 'jpeg_quality', $jpeg_quality, 'image_resize' ) );
			if ( !$return )
				return new WP_Error('resize_path_invalid', __( 'Resize path invalid' ));
		}

		imagedestroy( $newimage );

		$stat = stat( dirname( $destfilename ));
		$perms = $stat['mode'] & 0000666;
		// @codingStandardsIgnoreLine
		chmod( $destfilename, $perms );

		return $destfilename;
	}
}