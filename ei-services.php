<?php
/*
Plugin Name: EI Services
Description: EI Services is a simple WordPress plugin for displaying your services.
Version: 1.0
Text Domain: ei-services
Author: E-I
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'EIS_PLUGIN_URL', plugin_dir_url( __FILE__) );
define( 'EIS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'EIS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'EIS_PLUGIN_VERSION', '1.0' );

/* ---------------------------------------------------------------------------
 * Load the plugin required files
 * --------------------------------------------------------------------------- */
add_action( 'plugins_loaded','ei_services_plugin_load_function' );

if ( ! function_exists( 'ei_services_plugin_load_function' ) ) :
	function ei_services_plugin_load_function(){
		
		// Add required Files for EI Services Plugin
		require_once( 'cf-form-post.php' );
		require_once( 'cf-form-shortcode.php' );
	   
	}
endif;

/* ---------------------------------------------------------------------------
 * Activate EI-Services Plugin
 * --------------------------------------------------------------------------- */

register_activation_hook(__FILE__,'ei_services_plugin_enabled');

if ( ! function_exists( 'ei_services_plugin_enabled' ) ) :
	function ei_services_plugin_enabled() {	
		// Clear any cached data
		wp_cache_flush();
	}
endif;

/* ---------------------------------------------------------------------------
 * Deactivate EI-Services Plugin
 * --------------------------------------------------------------------------- */

if ( function_exists('register_deactivation_hook') )
	register_deactivation_hook(__FILE__,'ei_services_plugin_deactivated'); 

if ( ! function_exists( 'ei_services_plugin_deactivated' ) ) :
	function ei_services_plugin_deactivated() { 
		
		// Clear any cached data
		wp_cache_flush();
		
	}
endif;

/* ---------------------------------------------------------------------------
 * Uninstall EI-Services Plugin
 * --------------------------------------------------------------------------- */

if ( function_exists('register_uninstall_hook') )
	register_uninstall_hook(__FILE__,'ei_services_plugin_droped'); 

if ( ! function_exists( 'ei_services_plugin_droped' ) ) :
	function ei_services_plugin_droped() { 
		
		// Clear any cached data
		wp_cache_flush();
		
	}
endif;
?>
