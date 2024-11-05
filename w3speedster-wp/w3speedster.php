<?php 

/*

Plugin Name: W3Speedster Pro

Description: Speedup the site with good scores on google page speed test and Gtmetrix

Version: 7.29

Author: W3speedster

Author URI: https://w3speedster.com

License: GPLv2 or later

Copyright 2019-2024 W3Speedster

*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
if (!defined('W3SPEEDSTER_WP_CONTENT_BASENAME')) {
	if (!defined('W3SPEEDSTER_WP_PLUGIN_DIR')) {
		if(preg_match("/(\/trunk\/|\/w3speedster-wp\/)$/", plugin_dir_path( __FILE__ ))){
			define("W3SPEEDSTER_WP_PLUGIN_DIR", preg_replace("/(\/trunk\/|\/w3speedster-wp\/)$/", "", plugin_dir_path( __FILE__ )));
		}else if(preg_match("/\\\w3speedster-wp\/$/", plugin_dir_path( __FILE__ ))){
			//D:\hosting\LINEapp\public_html\wp-content\plugins\w3speedster-wp/
			define("W3SPEEDSTER_WP_PLUGIN_DIR", preg_replace("/\\\w3speedster-wp\/$/", "", plugin_dir_path( __FILE__ )));
		}
	}
	define("W3SPEEDSTER_WP_CONTENT_DIR", dirname(W3SPEEDSTER_WP_PLUGIN_DIR));
	define("W3SPEEDSTER_WP_CONTENT_BASENAME", basename(W3SPEEDSTER_WP_CONTENT_DIR));
}
define( 'W3SPEEDSTER_PLUGIN_VERSION', '7.29' );
define( 'W3SPEEDSTER_DIR', plugin_dir_path( __FILE__ ) );
define( 'W3SPEEDSTER_PLUGIN_FILE', __FILE__ );
define( 'W3SPEEDSTER_URL', plugin_dir_url( __FILE__ ) );

function checkDirectCall(){
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}
}
require_once(W3SPEEDSTER_DIR . 'includes/core.php');
require_once(W3SPEEDSTER_DIR . 'includes/init.php');


function W3SpeedsterMandatoryConfigAdminNotice() {
    if ( version_compare( PHP_VERSION, '5.6', '<' )){
        echo '<div class="error"><p>' . esc_html__( 'W3speedster requires PHP 5.6 (or higher) to function properly.','w3speedster-wp' ) . '</p></div>';
    }
    if ( !extension_loaded ('xml')){
        echo '<div class="error"><p>' . esc_html__( 'W3speedster requires PHP-XML module to function properly.','w3speedster-wp' ) . '</p></div>';
    }
	if(!extension_loaded('gd')){	
		echo '<div class="error"><p>' . esc_html__( 'W3speedster image optimization requires GD module to function properly.','w3speedster-wp' ) . '</p></div>';	
	}
	if(!class_exists('DOMDocument')){
		echo '<div class="error"><p>' . esc_html__( 'W3speedster requires PHP-XML module to function properly.','w3speedster-wp' ) . '</p></div>';
	}
    if ( isset( $_GET['activate'] ) ) {
        unset( $_GET['activate'] );
    }
}
//register_activation_hook( __FILE__, 'w3SpeedsterActivate'  );
function w3SpeedsterActivate(){
	$w3_speedster_admin = new W3Speedster\w3speedster(); 
	$w3_speedster_admin->w3RemoveCacheFilesHourlyEventCallback();
}
register_deactivation_hook( __FILE__, 'w3SpeedsterDeactivate' );
function w3SpeedsterDeactivate(){
	require_once(W3SPEEDSTER_DIR . 'admin/admin-init.php');
	$w3_speedster_admin = new W3Speedster\w3speedster_admin();
	$w3_speedster_admin->w3SpeedsterRemoveHtmlCacheCode();
	if ( wp_next_scheduled( 'w3_cache_size' ) ) {
        wp_clear_scheduled_hook('w3_cache_size');
    }
	if ( wp_next_scheduled( 'w3speedup_preload_css_min' ) ) {
		wp_clear_scheduled_hook('w3speedup_preload_css_min');
	}
	if ( wp_next_scheduled( 'w3speedup_image_optimization' ) ) {
		wp_clear_scheduled_hook('w3speedup_image_optimization');
	}
	if ( wp_next_scheduled( 'w3_check_key' ) ) {
        wp_clear_scheduled_hook('w3_check_key');
    }
	if (wp_next_scheduled('w3speedsterPreloadCacheCronJob')) {
        wp_clear_scheduled_hook('w3speedsterPreloadCacheCronJob');
    }
}
function w3speedsterDeactivateUnsupportedConfig() {
    deactivate_plugins( plugin_basename( W3SPEEDSTER_PLUGIN_FILE ) );
}

function w3speedsterActionLinks( $links ) {

	$links = array_merge( array(
		'<a href="' . esc_url( add_query_arg('page','w3_speedster',admin_url( '/admin.php' ) ) ) . '">' . __( 'Settings' ) . '</a>'
	), $links );

	return $links;

}
add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'w3speedsterActionLinks' );

if ( version_compare( PHP_VERSION, '5.6', '<' ) || !extension_loaded ('xml') ) {
    add_action( 'admin_notices', 'w3SpeedsterMandatoryConfigAdminNotice' );
    add_action( 'admin_init', 'w3speedsterDeactivateUnsupportedConfig' );
}
add_filter('cron_schedules', 'w3speedsterAddCustomCronIntervals');
function w3speedsterAddCustomCronIntervals($schedules) {
	$schedules['w3speedup_every_minute'] = array('interval' => 60, 'display' => __('Once every minute'));
	return $schedules;
}
function w3speedsterOptimizeImageOnUpload($attach_id){
	require_once(W3SPEEDSTER_DIR . 'includes/image-optimize.php');
	$w3_speedster_opt_img = new W3Speedster\w3speedster_optimize_image();
	 if(!empty($w3_speedster_opt_img->settings['opt_upload'])){
		return $w3_speedster_opt_img->w3speedsterOptimizeSingleImage($attach_id);
    }else{
    	return $attach_id;
    }
}
add_action( 'add_attachment','w3speedsterOptimizeImageOnUpload');
function w3speedsterChangeImageName($metadata, $attachment_id, $context){
	require_once(W3SPEEDSTER_DIR . 'includes/image-optimize.php');
	$w3_speedster_opt_img = new W3Speedster\w3speedster_optimize_image();
	return $w3_speedster_opt_img->w3speedsterChangeImageName($metadata, $attachment_id, $context);
}

add_filter('wp_generate_attachment_metadata','w3speedsterChangeImageName',10,3);
add_action( 'w3speedster_image_optimization', 'w3speedsterImageOptimizationCallback' );
add_action( 'w3speedup_preload_css_min', 'w3speedsterPreloadCssCallback' );
add_action( 'w3speedster_check_cron_needs_running', 'w3speedsterCheckCronNeedsRunningCallback' );
function w3speedsterCheckCronNeedsRunningCallback(){
	global $wpdb;
	require_once(W3SPEEDSTER_DIR . 'admin/admin-init.php');
	$w3_speedster_admin = new W3Speedster\w3speedster_admin();
	$result = $w3_speedster_admin->settings;
	global $w3_network_option;
	if(empty($result['enable_background_optimization'])){
		if ( wp_next_scheduled( 'w3speedster_image_optimization' ) ) {
			wp_clear_scheduled_hook('w3speedster_image_optimization');
		}
	}else{
		if(w3CheckMultisite() && empty($w3_network_option['manage_site_separately'])){
			$img_to_opt = 0;
			$blogs = get_sites();
			foreach ($blogs as $b) {
				$table_name = $wpdb->base_prefix.$b->blog_id.'_posts';
				if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) ) ) == $table_name ) {
					$img_to_opt += $wpdb->get_var(
					$wpdb->prepare("SELECT COUNT(ID) FROM `$table_name` WHERE post_type = %s",array('attachment'))
					);
				}
			}
		}else{
			$img_to_opt = $wpdb->get_var("SELECT count(ID) FROM {$wpdb->prefix}posts WHERE post_type='attachment'");
		}
		$opt_offset = w3GetOption('w3speedup_opt_offset');
		$img_remaining = (int)$img_to_opt-(int)$opt_offset;
		if(!empty($result['enable_background_optimization']) && $img_remaining > 0){
			if ( ! wp_next_scheduled( 'w3speedster_image_optimization' ) ) {
				wp_schedule_event( time(), 'w3speedup_every_minute', 'w3speedster_image_optimization' );
			}
		}else{
			if ( wp_next_scheduled( 'w3speedster_image_optimization' ) ) {
				wp_clear_scheduled_hook('w3speedster_image_optimization');
			}
		}
	}
	$preload_css = w3GetOption('w3speedup_preload_css');
	if(count($preload_css) > 0 && !empty($result['enable_background_critical_css'])){
		if ( ! wp_next_scheduled( 'w3speedup_preload_css_min' ) ) {
			wp_schedule_event( time(), 'w3speedup_every_minute', 'w3speedup_preload_css_min' );
		}
	}else{
		if ( wp_next_scheduled( 'w3speedup_preload_css_min' ) ) {
			wp_clear_scheduled_hook('w3speedup_preload_css_min') ;
		}
	}
}

function w3speedsterImageOptimizationCallback(){
	require_once(W3SPEEDSTER_DIR . 'includes/image-optimize.php');

    $w3_speedster_opt_img = new W3Speedster\w3speedster_optimize_image();
	
	if(!empty($_GET['attach_id'])){
		$w3_speedster_opt_img->w3OptimizeAttachmentId((int)$_GET['attach_id']);
	}else{
		$w3_speedster_opt_img->w3speedsterOptimizeImageCallback();
	}
}
add_action('init','w3GetCacheSizeCron');
function w3GetCacheSizeCron(){
	if ( ! wp_next_scheduled( 'w3_cache_size' ) ) {
        wp_schedule_event( time(), 'hourly', 'w3_cache_size' );
    }
	if ( ! wp_next_scheduled( 'w3_check_key' ) ) {
        wp_schedule_event( time(), 'daily', 'w3_check_key' );
    }
	if ( ! wp_next_scheduled( 'w3speedster_check_cron_needs_running' ) ) {
		wp_schedule_event( time(), 'twicedaily', 'w3speedster_check_cron_needs_running' );
	}
	if (! wp_next_scheduled('w3speedsterPreloadCacheCronJob')) {
        wp_schedule_event(time(), 'w3speedup_every_minute', 'w3speedsterPreloadCacheCronJob');
    }
}

if(!empty($_GET['w3_preload_css'])){
	add_action('wp_head','w3speedsterPreloadCssCallback');
}
if(!empty($_GET['w3_put_preload_css'])){
	add_action('wp_head','w3speedsterPutPreloadCssCallback');
}
function w3speedsterPutPreloadCssCallback(){
	$w3_speedster = new W3Speedster\w3speedster(); 
	$w3_speedster->w3PutPreloadCss();
	exit;	
}
add_action( 'wp_ajax_w3speedster_preload_css', 'w3speedsterPreloadCssAjaxCallback' );
function w3speedsterPreloadCssAjaxCallback(){
	w3UpdateOption('w3speedup_critical_css_error','','no');
	$response = w3speedsterPreloadCssCallback();
	$error = w3GetOption('w3speedup_critical_css_error');
	$total = (int)w3GetOption('w3speedup_preload_css_total');
	$que = w3GetOption('w3speedup_preload_css');
	$created = $total - count(is_array($que) ? $que : array());
    $runningUrl = w3GetOption('w3speedup_critical_running_url');
	if(!empty($error)){
		echo wp_json_encode(array('error',$error,$total,$created,$runningUrl));
	}else{
		echo wp_json_encode(array('success',$response,$total,$created,$runningUrl));
	}
	exit;
}
function w3CheckMultisite(){
	if(function_exists('is_multisite') && is_multisite()){
		return 1;
	}else{
		return 0;
	}
}

function w3GetOption($option){
	if(w3CheckMultisite()){
		global $w3_network_option;
		if(empty($w3_network_option)){
			$w3_network_option = get_site_option('w3_speedup_option', true);
		}
	}
	if(w3CheckMultisite() && (empty($w3_network_option['manage_site_separately']))){
		$settings = get_site_option($option, true);
	}else{
		$settings = get_option( $option, true );
	}
	return $settings;
}
function w3UpdateOption($option, $value, $autoload = null){
	if(w3CheckMultisite()){
		global $w3_network_option;
		if(empty($w3_network_option)){
			$w3_network_option = get_site_option('w3_speedup_option', true);
		}
	}
	if(w3CheckMultisite() && (empty($w3_network_option['manage_site_separately']))){
		if(update_site_option( $option,$value,$autoload)){
			return 1;
		}else{
			return 0;
		}
	}else{
		if(update_option( $option,$value,$autoload)){
			return 1;
		}else{
			return 0;
		}
	}
}
function w3speedsterPreloadCssCallback(){
	if(is_user_logged_in() && current_user_can( 'w3speedster_settings' )){
		$w3_speedster = new W3Speedster\w3speedster(); 
		$response = $w3_speedster->w3GeneratePreloadCss();
		if(!empty($response) && $response == "exists"){
			$response = w3speedsterPreloadCssCallback();
		}
		if(!empty($_GET['w3_preload_css'])){
			exit;
		}
		return $response;
	}
}
add_action( 'w3_check_key', 'w3CheckKeyCallback' );
add_action( 'w3_cache_size', 'w3CacheSizeCallback' );
add_action('w3speedsterPreloadCacheCronJob','w3speedsterPreloadCacheCronJobCallback');
function w3CheckKeyCallback(){
	require_once(W3SPEEDSTER_DIR . 'admin/admin-init.php');
	$w3_speedster_admin = new W3Speedster\w3speedster_admin(); 
    $w3_speedster_admin->w3CheckLicenseKey();
}
function w3CacheSizeCallback(){
	require_once(W3SPEEDSTER_DIR . 'admin/admin-init.php');
	$w3_speedster_admin = new W3Speedster\w3speedster_admin(); 
    $w3_speedster_admin->w3CacheSizeCallback();
}
add_action( 'wp_ajax_w3speedster_optimize_image', 'w3speedsterAddImageOptimizationSchedule' );
function w3speedsterAddImageOptimizationSchedule(){
	w3speedsterImageOptimizationCallback();
	exit;
}

  
add_action( 'after_setup_theme', 'w3speedsterAddMobileThumbnail' );
function w3speedsterAddMobileThumbnail(){
	add_image_size( 'w3speedup-mobile', 595 );
}
function w3CachePurgeActionJs() { 
	if(is_user_logged_in() && current_user_can( 'w3speedster_settings' )){
?>
    <script type="text/javascript" >
		var ajaxurl = "<?php echo esc_url(admin_url('admin-ajax.php')); ?>";
		jQuery(document).ready(function(){	
			jQuery(".w3-speedster-cache-purge-text, #del_js_css_cache").on( "click", function() {
				jQuery('.in-progress.w3d-flex.delete_css_js_cache').show();
				jQuery('#del_js_css_cache').attr('disabled',true);
				jQuery('#w3_speedster_cache_purge').show();
				jQuery('.cache_size').addClass('deleting');
				jQuery('.w3-speedster-cache').text('Deleting...');
				var data = {
							'action': 'w3_speedster_cache_purge',
							'_wpnonce':'<?php echo wp_create_nonce("purge_cache");?>'
							};
				jQuery.get(ajaxurl, data, function(response) {
					jQuery('#w3_speedster_cache_purge').hide();
					jQuery('.cache_size').removeClass('deleting');
					jQuery('.w3-speedster-cache').text('Cache Deleted!');
					jQuery('.cache_folder_size').text(response+" MB");
					jQuery('#del_js_css_cache').attr('disabled',false);
					jQuery('.in-progress.w3d-flex.delete_css_js_cache').hide();
					setTimeout(() => {
						jQuery('.w3-speedster-cache').text('W3Speedster cache');
					}, 2000);
				}).fail(function() {
					jQuery('#w3_speedster_cache_purge').hide();
					jQuery('.cache_size').removeClass('deleting');
					jQuery('.w3-speedster-cache').text('try again');
					jQuery('.cache_folder_size').text(response+" MB");
					jQuery('#del_js_css_cache').attr('disabled',false);
					jQuery('.in-progress.w3d-flex.delete_css_js_cache').hide();
					setTimeout(() => {
						jQuery('.w3-speedster-cache').text('W3Speedster cache');
					}, 2000);
				});

			});
			function confirmAction() {
				var result = confirm('<?php _e("Are you sure you want to proceed? Critical css may take long time to regenerate.",'w3speedster');?>');
				if (result) {
					return true;
				} else {
					return false;
				}
			}
			jQuery("#del_critical_css_cache,.w3-speedster-critical-cache-purge-text,.w3-speedster-critical-cache-purge-single-text").on( "click", function() {
				jQuery('.in-progress.w3d-flex.delete_critical_css_cache').show();
				if(!confirmAction()){
					return false;
				}
				jQuery('#w3_speedster_cache_purge').show();
				jQuery('.cache_size').addClass('deleting');
				jQuery('#del_critical_css_cache').attr('disabled',true);
				jQuery('.w3-speedster-cache').text('Deleting...');
				var data_id = jQuery(this).attr("data-id");
				var data_type = jQuery(this).attr("data-type");
				var data = {
							'action': 'w3_speedster_critical_cache_purge',
							'_wpnonce':'<?php echo wp_create_nonce("purge_critical_css");?>',
							'data_id':data_id,
							'data_type':data_type
							};

				jQuery.get(ajaxurl, data, function(response) {
					jQuery('#del_critical_css_cache').attr('disabled',false);
					jQuery('#w3_speedster_cache_purge').hide();
					jQuery('.cache_size').removeClass('deleting');
					jQuery('.w3-speedster-cache').text('Cache Deleted!');
					jQuery('.in-progress.w3d-flex.delete_critical_css_cache').hide();
					setTimeout(() => {
						jQuery('.w3-speedster-cache').text('W3Speedster cache');
					}, 2000);
				}).fail(function() {
					jQuery('#del_critical_css_cache').attr('disabled',false);
					jQuery('#w3_speedster_cache_purge').hide();
					jQuery('.cache_size').removeClass('deleting');
					jQuery('.w3-speedster-cache').text('try again');
					jQuery('.in-progress.w3d-flex.delete_critical_css_cache').hide();
					setTimeout(() => {
						jQuery('.w3-speedster-cache').text('W3Speedster cache');
					}, 2000);
				});

			});
			
			jQuery("#del_html_cache").on( "click", function() {
				jQuery('.in-progress.w3d-flex.delete_html_cache').show();
				jQuery('#del_html_cache').attr('disabled',true);
				var data = {
							'action': 'w3_speedster_html_cache_purge',
							'_wpnonce':'<?php echo wp_create_nonce("purge_html_cache");?>'
							};
				jQuery.get(ajaxurl, data, function(response) {
					jQuery('#del_html_cache').attr('disabled',false);
					jQuery('.in-progress.w3d-flex.delete_html_cache').hide();
				}).fail(function() {
					jQuery('#del_html_cache').attr('disabled',false);
					jQuery('.in-progress.w3d-flex.delete_html_cache').hide();
				});

			});
		});
    </script> <?php
	}
}
function w3ToolbarLinkToDeleteCache( $wp_admin_bar ) {

	$filesize = round(w3GetOption('w3_speedup_filesize',false),2);
	$clear_cache_text = '';
	$clear_cache_id = '';
	if(is_page()){
		global $post;
		$clear_cache_text = 'page';
		$clear_cache_id = $post->ID;
	}elseif(is_single()){
		global $post;
		$clear_cache_text = 'post';
		$clear_cache_id = $post->ID;
	}elseif(is_archive() || is_category()){
		$clear_cache_text = 'category';
		$clear_cache_id = get_queried_object_id();
	}
	$args = array(

		'id'    => 'w3_speedster_purge_cache',

		'title' => '<div class="w3-speedster-spinner-container">
		<div id="w3_speedster_cache_purge"></div></div>
	  <style>#w3_speedster_cache_purge {
		width: 20px;
		height: 20px;
		margin: 4px 0px 0px 0px;
		background: transparent;
		border-top: 4px solid #009688;
		border-right: 4px solid transparent;
		border-radius: 50%;
		-webkit-animation: 1s spin linear infinite;
		animation: 1s spin linear infinite;
		display:none;
	  }
	  .w3-speedster-spinner-container{
	  overflow:hidden;
	  display:inline-block;
		}
	  
	  
	  
	  -webkit-@keyframes spin {
		-webkit-from {
		  -webkit-transform: rotate(0deg);
		  -ms-transform: rotate(0deg);
		  transform: rotate(0deg);
		}
		-webkit-to {
		  -webkit-transform: rotate(360deg);
		  -ms-transform: rotate(360deg);
		  transform: rotate(360deg);
		}
	  }
	  
	  @-webkit-keyframes spin {
		from {
		  -webkit-transform: rotate(0deg);
		  transform: rotate(0deg);
		}
		to {
		  -webkit-transform: rotate(360deg);
		  transform: rotate(360deg);
		}
	  }
	  
	  @keyframes spin {
		from {
		  -webkit-transform: rotate(0deg);
		  transform: rotate(0deg);
		}
		to {
		  -webkit-transform: rotate(360deg);
		  transform: rotate(360deg);
		}
	  }
	  }</style>
	 <div class="w3-speedster-cache">W3Speedster cache</div><div class="cache_size">
	 <div class="w3-speedster-cache-purge-text" data-id="0">Delete js/css cache for all pages</div>
	 '.(!empty($clear_cache_text) ? '<div class="w3-speedster-critical-cache-purge-single-text" data-type="'.$clear_cache_text.'" data-id="'.$clear_cache_id.'">Delete critical css cache for this '.$clear_cache_text.' only</div>' : '' ).'
	 <div><span>File Size</span>&nbsp;&nbsp;&nbsp;<span class="cache_folder_size">'.$filesize.'&nbsp;MB</span></div>
	 </div><style>#wp-admin-bar-w3_speedster_purge_cache{min-width:135px;}.wp-speedster-page .cache_size{display:none;}.cache_size.deleting{display:none!important;}.cache_size{position:absolute!important;color:#fff!important;background-color:#000;background: #000;min-width: 250px;}.cache_size div{padding: 2.5px 5px !important;}.cache_size:hover, .w3-speedster-cache:hover + .cache_size{display:block;}.w3-speedster-cache + .cache_size div:hover{background-color:#23282dcf;}.w3-speedster-cache{display: inline-block;
    vertical-align: top;}</style>'.w3CachePurgeActionJs(),

		'href'  => '#',

		'meta'  => array( 'class' => 'wp-speedster-page' )

	);

	$wp_admin_bar->add_node( $args );

}


function w3speedsterRoleCaps(){
	if(!is_multisite()){
		$role = get_role('administrator');
		$role->add_cap('w3speedster_settings', true);
	}
}
add_action('init', 'w3speedsterRoleCaps', 11);
function w3SpeedsterRegisterNetworkOptionsPage() {
	add_submenu_page('settings.php','W3speedster', 'W3speedster', 'w3speedster_settings', 'w3_speedster', 'w3SpeedsterOptionsPage');
}
function w3SpeedsterRegisterSiteOptionsPage() {
	add_menu_page('W3speedster', 'W3speedster', 'manage_options', 'w3_speedster', 'w3SpeedsterOptionsPage', plugins_url('assets/images/w3speedster-icon.webp', __FILE__),81);
}
function w3SpeedsterOptionsPage(){
	global $w3admin;
	include W3SPEEDSTER_DIR . "/admin/admin.php";
}

function w3SpeedsterCachePurgeCallback(){
	require_once(W3SPEEDSTER_DIR . 'admin/admin-init.php');
	$w3_speedster_admin = new W3Speedster\w3speedster_admin();
	$w3_speedster_admin->w3SpeedsterCachePurgeCallback();
}

function w3SpeedsterHtmlCachePurgeCallback(){
	require_once(W3SPEEDSTER_DIR . 'admin/admin-init.php');
	$w3_speedster_admin = new W3Speedster\w3speedster_admin();
	$w3_speedster_admin->w3SpeedsterHtmlCachePurgeCallback();
}

function w3SpeedsterCriticalCachePurgeCallback(){
	require_once(W3SPEEDSTER_DIR . 'admin/admin-init.php');
	$w3_speedster_admin = new W3Speedster\w3speedster_admin();
	$w3_speedster_admin->w3SpeedsterCriticalCachePurgeCallback();
}

function addOptimizeImageCustomJs(){ ?>
	<style>
	.loader {
			margin: 0px auto;
			border: 5px solid #ccc;
			border-radius: 50%;
			border-top: 5px solid #3498db;
			width: 15px;
			height: 15px;
			-webkit-animation: spin 2s linear infinite;
			animation: spin 2s linear infinite;
	}
	.loader-sec {
		display: none;
		    position: relative;
		width: 15px;
		height: 15px;
		margin: 0 auto;
		left: 35px;
		top: 17px;
	}
	/* Safari */
	@-webkit-keyframes spin {
	  0% { -webkit-transform: rotate(0deg); }
	  100% { -webkit-transform: rotate(360deg); }
	}

	@keyframes spin {
	  0% { transform: rotate(0deg); }
	  100% { transform: rotate(360deg); }
	}
	.dw-operation-sec .p-digital-button {
		display: inline-block;
		padding: 10px;
	}
	.optimize_message {
		color:#2d792d;
		display:block;
		padding-bottom: 10px;
	}
	</style>
		<script>
		var ajaxurl = "<?php echo esc_url(admin_url('admin-ajax.php')); ?>";
		jQuery(document).ready(function(){
			
			//jQuery('.optimize_media_image').click(function(){
			jQuery( "body" ).delegate( ".optimize_media_image", "click", function() {
				var img_id = jQuery( this ).attr('data-id');
				jQuery('.loader-sec').show();	
				jQuery.ajax({
					 type : "POST",
					 dataType : "json",
					 url : ajaxurl,
					 data : {
						 action: "fn_w3_optimize_media_image",
						 id: img_id							
						},
					 success: function(response) {
						   //alert(response);
						   console.log('-- response11 --', response.summary);
						   //response = jQuery.trim(response);
						   
						   
						   jQuery('.loader-sec').hide();							   
						   
						   if(response.summary == true){
							   jQuery('.optimize_message').html('Image optimize successfully.');
						   }else{
							   jQuery('.optimize_message').html('Image not Optimized.');
						   }
							jQuery('.optimize_message').show();							   
						   /* setTimeout(function(){				
								jQuery('.optimize_message').hide();	
							}, 5000); */
						}
				});				
			});	
			
			
		});
	</script>
	<?php	
}

function addButtonToEditMediaModalFieldsArea1($form_fields, $post){
	require_once(W3SPEEDSTER_DIR . 'admin/admin-init.php');
	$w3_speedster_admin = new W3Speedster\w3speedster_admin();
	return $w3_speedster_admin->addButtonToEditMediaModalFieldsArea1($form_fields, $post);
}
function fnW3OptimizeMediaImageCallback(){
	require_once(W3SPEEDSTER_DIR . 'admin/admin-init.php');
	$w3_speedster_admin = new W3Speedster\w3speedster_admin();
	$w3_speedster_admin->fnW3OptimizeMediaImageCallback();
}
function w3speedsterActivateLicenseKey(){
	require_once(W3SPEEDSTER_DIR . 'admin/admin-init.php');
	$w3_speedster_admin = new W3Speedster\w3speedster_admin();
	$w3_speedster_admin->w3speedsterActivateLicenseKey();
}
add_action( 'upgrader_process_complete', 'w3UpgradeFunction',10, 2);
 
function w3UpgradeFunction( $upgrader_object, $options ) {
    $current_plugin_path_name = plugin_basename( __FILE__ );
    if ($options['action'] == 'update' && $options['type'] == 'plugin' && !empty($options['plugins']) && is_array($options['plugins'])) {
       foreach($options['plugins'] as $each_plugin) {
          if ($each_plugin==$current_plugin_path_name) {
            $w3_speedster_admin = new W3Speedster\w3speedster(); 
			$w3_speedster_admin->w3RemoveCacheFilesHourlyEventCallback();
			
          }
       }
    }
}
function w3LoadAdminCallback(){
	
	if(function_exists('is_multisite') && is_multisite()){
		add_action('network_admin_menu', 'w3SpeedsterRegisterNetworkOptionsPage' );
	}
	$options = get_site_option('w3_speedup_option', true);
	if(!is_multisite() || (function_exists('is_multisite') && is_multisite() && !empty($options['manage_site_separately']))){
		add_action('admin_menu', 'w3SpeedsterRegisterSiteOptionsPage' );
	}
	//add_action( 'admin_footer', 'w3CachePurgeActionJs' );
	add_action( 'admin_bar_menu', 'w3ToolbarLinkToDeleteCache' ,999 );
	add_action( 'wp_ajax_w3_speedster_cache_purge', 'w3SpeedsterCachePurgeCallback' );
	add_action( 'wp_ajax_w3_speedster_critical_cache_purge', 'w3SpeedsterCriticalCachePurgeCallback');
	add_action( 'wp_ajax_w3_speedster_html_cache_purge', 'w3SpeedsterHtmlCachePurgeCallback' );
	add_action('admin_footer', 'addOptimizeImageCustomJs');		
	add_filter( 'attachment_fields_to_edit', 'addButtonToEditMediaModalFieldsArea1' , 99, 2 );
	add_action( 'wp_ajax_fn_w3_optimize_media_image', 'fnW3OptimizeMediaImageCallback');
	add_action( 'wp_ajax_w3speedsterActivateLicenseKey', 'w3speedsterActivateLicenseKey' );
	add_action('admin_enqueue_scripts', 'w3SpeedsterAdminEnqueScript');
	if(!empty($_GET['page']) && $_GET['page'] == 'w3_speedster'){
		launchAdmin();
	}
}
function launchAdmin(){
	global $w3admin;
	if(empty($w3admin)){
		require_once(W3SPEEDSTER_DIR . 'admin/admin-init.php');
		$w3admin = new W3Speedster\w3speedster_admin(); 
		$w3admin->launch();
	}
	return $w3admin;
}
function w3speedsterPreloadCacheCronJobCallback(){
	require_once(W3SPEEDSTER_DIR . 'includes/html-optimize.php');
	$w3speedsterinit = new w3speedster\w3speed_html_optimize();
	$w3speedsterinit->w3speedsterSetPreloadCache();
}
function w3LoadAdmin(){
	if(current_user_can('w3speedster_settings')){
		w3LoadAdminCallback();
	}
}
if(is_admin()){
	add_action('init','w3LoadAdmin');
	
	
}else{
    if ( (defined('DOING_AJAX') && DOING_AJAX) || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
	}else{
		require_once(W3SPEEDSTER_DIR . 'includes/css-optimize.php');
		require_once(W3SPEEDSTER_DIR . 'includes/js-optimize.php');
		require_once(W3SPEEDSTER_DIR . 'includes/html-optimize.php');
		add_action( 'admin_bar_menu', 'w3ToolbarLinkToDeleteCache' ,999 );
		//add_action( 'wp_footer', 'w3CachePurgeActionJs' );
		if(!empty($_GET['testing'])){
			
			$upload_dir = wp_upload_dir();
			// @codingStandardsIgnoreLine
			$html = file_get_contents($upload_dir['basedir'].'/w3test.html');
			$w3_optimize = new W3Speedster\w3speed_html_optimize();
            // @codingStandardsIgnoreLine
			echo $w3_optimize->w3Speedster($html); exit;
		}
			add_action('after_setup_theme', 'w3PreStart',11);
	}
	
}

function w3PreStart(){
	if(function_exists('w3_change_start_optimization_hook')	){
    	add_action(w3_change_start_optimization_hook('wp'),'w3Start',11);	
    }else{
    	w3Start();
    }
}
function w3Start(){
	global $current_user;
	if(!empty($current_user) && current_user_can('edit_others_pages')){

	}else{
		$w3_optimize = new W3speedster\w3speed_html_optimize();
		$w3_optimize->w3StartOptimizationCallback();
	}
}

if(is_admin()){
	$upload_dir   = wp_upload_dir();
	if(!is_file($upload_dir['basedir'].'/blank-h.png')){
		copy(W3SPEEDSTER_DIR."assets/images/blank-h.png",$upload_dir['basedir'].'/blank-h.png');
	}
	if(!is_file($upload_dir['basedir'].'/blank-square.png')){
		copy(W3SPEEDSTER_DIR."assets/images/blank-square.png",$upload_dir['basedir'].'/blank-square.png');
	}
	if(!is_file($upload_dir['basedir'].'/blank-p.png')){
		copy(W3SPEEDSTER_DIR."assets/images/blank-p.png",$upload_dir['basedir'].'/blank-p.png');
	}
	if(!is_file($upload_dir['basedir'].'/blank-3x4.png')){
		copy(W3SPEEDSTER_DIR."assets/images/blank-3x4.png",$upload_dir['basedir'].'/blank-3x4.png');
	}
	if(!is_file($upload_dir['basedir'].'/blank-4x3.png')){
		copy(W3SPEEDSTER_DIR."assets/images/blank-4x3.png",$upload_dir['basedir'].'/blank-4x3.png');
	}
	if(!is_file($upload_dir['basedir'].'/blank.png')){
		copy(W3SPEEDSTER_DIR."assets/images/blank.png",$upload_dir['basedir'].'/blank.png');
	}
	if(!is_file($upload_dir['basedir'].'/blank.mp4')){
		copy(W3SPEEDSTER_DIR."assets/images/blank.mp4",$upload_dir['basedir'].'/blank.mp4');
	}
	if(!is_file($upload_dir['basedir'].'/blank.mp3')){
		copy(W3SPEEDSTER_DIR."assets/images/blank.mp3",$upload_dir['basedir'].'/blank.mp3');
	}
	if(!is_file($upload_dir['basedir'].'/blank.pngw3.webp')){
		copy(W3SPEEDSTER_DIR."assets/images/blank.pngw3.webp",$upload_dir['basedir'].'/blank.pngw3.webp');
	}
}
add_action('in_plugin_update_message-w3speedster/w3speedster.php','w3speedsterPluginUpdateMessage');
function w3speedsterPluginUpdateMessage(){
	echo esc_html(__('License key will be required to update the plugin. To get a key, contact')) . ' <a rel="noopener noreferrer" href="https://w3speedster.com">' . esc_html__('here') . '</a>.';
}

function createw3CoreWebvitalsTable() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'w3corewebvitals';
	
	if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        // If table does not exist, create it
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            url varchar(255) NOT NULL,
            issuetype varchar(50) NOT NULL,
            data text NOT NULL,
            deviceType varchar(255) NOT NULL,
            timestamp datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

        //echo "Table created successfully.";
    } else {
        // Check if the column 'deviceType' exists
        if ($wpdb->get_var("SHOW COLUMNS FROM $table_name LIKE 'deviceType'") != 'deviceType') {
            // If column 'deviceType' does not exist, add it
            $sql = "ALTER TABLE $table_name ADD COLUMN deviceType VARCHAR(255) NOT NULL";

            // Execute the SQL query
			// @codingStandardsIgnoreLine
            $wpdb->query($sql);

            //echo "Column 'deviceType' added successfully.";
        } else {
           // echo "Table and column 'deviceType' already exist.";
        }
    }
}
add_action( 'admin_init', 'createw3CoreWebvitalsTable' );


add_action( 'wp_ajax_nopriv_w3speedsterPutData', 'w3speedsterPutData' );
add_action( 'wp_ajax_w3speedsterPutData', 'w3speedsterPutData' );
function w3speedsterPutData(){
	global $wpdb;
    $table_name = $wpdb->prefix . 'w3corewebvitals';

$data = array(
    'url' => $_POST['url'],
    'issueType' => $_POST['issueType'],
    'data' => $_POST['data'],
	'deviceType' => $_POST['deviceType'],
    // Add more columns and their respective values as needed
);

$wpdb->insert($table_name, $data);
exit;
}

add_action( 'wp_ajax_w3SpeedsterGetLogData', 'w3SpeedsterGetLogData' );
function w3SpeedsterGetLogData(){
	global $wpdb;
	$table_name = $wpdb->prefix . 'w3corewebvitals';
	$limit = isset($_POST['limit']) ? $_POST['limit'] : 10;
	$paged = isset($_POST['paged']) ? $_POST['paged'] : 1;
	$offset = isset($_POST['paged']) ? ($paged - 1) * $limit : 0;
	$conditions = array();
	$sql = "SELECT * FROM $table_name";
	if(isset($_POST['issuetype']) && !empty($_POST['issuetype'])){
		 $conditions[] = "issuetype = '{$_POST['issuetype']}'";
	}
	if(isset($_POST['deviceType']) && !empty($_POST['deviceType'])){
		 $conditions[] = "deviceType = '{$_POST['deviceType']}'";
	}
	if(isset($_POST['url']) && !empty($_POST['url'])){
		$url_conditions = array();
		foreach ($_POST['url'] as $url) {
			// Sanitize each URL before adding to the condition
			$url_conditions[] = $wpdb->prepare("url = %s", $url);
		}
		// Combine URL conditions using OR
		$conditions[] = '(' . implode(" OR ", $url_conditions) . ')';
	}
	if(isset($_POST['start_date']) && !empty($_POST['start_date']) && isset($_POST['end_date']) && !empty($_POST['end_date'])){
		$start_date = strtotime($_POST['start_date']);
		$end_date = strtotime($_POST['end_date']) + 86400;
		$conditions[] = "UNIX_TIMESTAMP(timestamp) BETWEEN $start_date AND $end_date";
	}
	if(!empty($conditions)){
		 $sql .= " WHERE " . implode(" AND ", $conditions);
	}
	// @codingStandardsIgnoreLine
	$logResultPagination = $wpdb->get_results($sql);
	if(count($logResultPagination) < $limit){
		$offset  = 0;
	}
	$sql .= " ORDER BY id DESC LIMIT %d OFFSET %d";
	// @codingStandardsIgnoreLine
	$logResult = $wpdb->get_results($wpdb->prepare($sql, $limit, $offset));
	
	$logData = '';
	if ( $logResult ) {
	$logData .= '<table class="webvitals-table"><thead><th>ID</th><th>Url</th><th>Issue Type</th><th>Device Type</th><th>Data</th><th>Time</th></thead><tbody>';
	foreach ( $logResult as $entry ) {
		$prettyJsonString = wp_json_encode(json_decode(stripcslashes($entry->data), true),JSON_PRETTY_PRINT);
		$logData .='<tr><td class="id_'.$entry->id.'">' . $entry->id . '</td><td class="url url_'.$entry->id.'">' . urldecode($entry->url) . '</td><td class="issueType_'.$entry->id.'">' . $entry->issuetype . '</td><td class="deviceType_'.$entry->id.'">' . $entry->deviceType . '</td><td class="data_'.$entry->id.'"><div class="log-data">' . $prettyJsonString . '</div><button data-id="'.$entry->id.'" class="more_info" type="button" popovertarget="more_info" popovertargetaction="show">View More</button></td><td class="time_'.$entry->id.'">' . $entry->timestamp . '</td></tr>';
	}
	$page = ceil(count($logResultPagination)/$limit);
	$logData .= '</tbody></table><div class="pagination" data-last="'.$page.'">';
	if($paged > 1){
		$logData .= '<button type="button" class="page-prev" data-page="1"><<</button><button type="button" class="page-prev" data-page="'.$paged.'"><</button>';
	}
	for($i=1; $i <= $page;$i++){
		if($paged == 1){
			$activeClass = $i == 1 ? 'active' : '';
		}else{
			$activeClass = $i == $paged ? 'active' : '';
		}
		//if(($i <= 2 && $paged <=2) || $i > ($page-1) || $i == ($paged +1) || $i == ($paged) || $i == 1){
		if(($paged <= 2 && $i <= 5) || ($paged >= 3 && $i >= ($paged-2) && $i <= ($paged+2) )){
			$logData .= '<button type="button" class="p-num '.$activeClass.'" data-page="'.$i.'">'.$i.'</button>';
		}
	}
	if($paged < $page){
		$logData .= '<button type="button" class="page-next" data-page='.$paged.'>></button><button type="button" class="page-next-last" data-page='.$page.'>>></button>';
	}
	$logData .= '</div>';
	} else {
		$logData = '<div class="no-data-found-log">No Data Found</div>';
	}
	if(isset($_POST['getBy'])){
		// @codingStandardsIgnoreLine
		echo $logData;
		exit;
	}else{
		return $logData;
	}
}

add_action( 'wp_ajax_w3SpeedsterDeleteLogData', 'w3SpeedsterDeleteLogData' );
function w3SpeedsterDeleteLogData(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'w3corewebvitals';
    
    // Check if the user is logged in and has the necessary permissions
    if (!is_user_logged_in() || !current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized access');
    }

    // Receive the time interval from the AJAX request
    $time_interval = $_POST['time_interval']; // Assuming 'time_interval' is the key sent via AJAX

    global $wpdb;
    // Construct the SQL query based on the received time interval
    switch ($time_interval) {
        case 'last7days':
			$sql = "DELETE FROM $table_name WHERE timestamp < DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
			break;
		case 'lastMonth':
			$sql = "DELETE FROM $table_name WHERE timestamp < DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
			break;
		case 'last3months':
			$sql = "DELETE FROM $table_name WHERE timestamp < DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
			break;
		case 'last6months':
			$sql = "DELETE FROM $table_name WHERE timestamp < DATE_SUB(CURDATE(), INTERVAL 6 MONTH)";
			break;
		case 'lastYear':
			$sql = "DELETE FROM $table_name WHERE timestamp < DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
			break;
		case 'all':
			$sql = "DELETE FROM $table_name";
			break;
        // Add cases for other time intervals as needed
        default:
            wp_send_json_error('Invalid time interval');
            break;
    }
	// @codingStandardsIgnoreLine
	$result = $wpdb->query($wpdb->prepare($sql));

    if ($result !== false) {
		// @codingStandardsIgnoreLine
        echo w3SpeedsterGetLogData();
    } else {
        wp_send_json_error('Error deleting data');
    }

    // Important: Always exit after processing to prevent further execution
    exit;
}

add_action( 'wp_ajax_w3SpeddsterShowUrlSuggestions', 'w3SpeddsterShowUrlSuggestions' );
function w3SpeddsterShowUrlSuggestions(){
	global $wpdb;
	$search_term = trim($_POST['s_text']); 
	$search_query = '%' . $wpdb->esc_like( $search_term ) . '%';

	$results = $wpdb->get_results( 
    $wpdb->prepare( 
        "
        SELECT *
        FROM {$wpdb->prefix}w3corewebvitals
        WHERE url LIKE %s
		GROUP BY url
		LIMIT 10
        ",
        $search_query
    )
);
	$urlArray = array();
	$optionsWithCheckbox = '<ul class="url-list">';
	$options = '';
	foreach($results as $result){
		$options .= '<option value="'.$result->url.'">'.$result->url.'</option>';
		$optionsWithCheckbox .= '<div class="single-url"><div class="url">'.$result->url.'</div><input type="checkbox" name="temp_input" class="url_checkbox" value=""></div>';
		
		$urlArray[] =  $result->url;
	}
	$optionsWithCheckbox .= '</ul>';
	
	$response_data = array(
    'options' => $options,
    'optionsWithCheckbox' => $optionsWithCheckbox
	);
	echo wp_json_encode($urlArray);
	exit;
}

function w3SpeedsterAdminEnqueScript(){
	wp_enqueue_style(
        'w3speedster-admin-style', plugins_url('assets/css/admin-style.css', __FILE__), array(), '1.0.0');
}