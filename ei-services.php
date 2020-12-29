<?php
/*
Plugin Name: EI Services
Description: EI Services is a simple WordPress plugin for displaying your services.
Version: 1.0
Text Domain: ei-services
Author: Neeraj Khatediya
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
		require_once( 'ei-services-post.php' );
		require_once( 'ei-services-shortcode.php' );
	   
		// Assign our template to page
		add_filter( 'template_include', 'view_services_template'	);
	}
endif;

/* ---------------------------------------------------------------------------
 * Activate EI-Services Plugin
 * --------------------------------------------------------------------------- */

register_activation_hook(__FILE__,'ei_services_plugin_enabled');

if ( ! function_exists( 'ei_services_plugin_enabled' ) ) :
	function ei_services_plugin_enabled() {	
			 
		//Add Default Options for EI-Services Plugin
		add_option( 'services_page','','', 'yes' );
		add_option( 'services_title','Our Services','', 'yes' );
		add_option( 'services_post_count','6','', 'yes' );
		add_option( 'services_orderby','none','', 'yes' );
		add_option( 'services_order', 'DESC', '', 'yes' );
		add_option( 'services_title_tmp', 'Our Services', '', 'yes' );
		add_option( 'services_loadmore', 'load_btn', '', 'yes' );
		add_option( 'services_num', '8', '', 'yes' );
		add_option( 'services_tmporder', 'DESC', '', 'yes' );
		
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
		
		//Delete plugin options on uninstall
		delete_option( 'services_page' );
		delete_option( 'services_title' );
		delete_option( 'services_post_count' );
		delete_option( 'services_orderby' );	
		delete_option( 'services_order' );
		delete_option( 'services_title_tmp' );
		delete_option( 'services_loadmore' );
		delete_option( 'services_num' );
		delete_option( 'services_tmporder' );
		
	}
endif;

/* ---------------------------------------------------------------------------
 * Checks if the template is assigned to the page
 * --------------------------------------------------------------------------- */
if ( ! function_exists( 'view_services_template' ) ) : 
	function view_services_template( $template ) {

		global $post,$wp_query; 
		
		if( $post->ID == get_option('services_page') ){
			$filename = 'template-services.php';
		
		 
		 
		  $plugin_file = plugin_dir_path(__FILE__) . $filename;
		  $override_file = get_stylesheet_directory() .'/'. $filename;
		  
		  if( file_exists( $override_file ) ) {
			 
			 return $override_file;
			 
		  } else {
			  
			return $plugin_file; 
			 
		  }
		 
		}
		
		return $template;
	} 
endif;

/* ---------------------------------------------------------------------------
 * Services single page redirect tempalte hook
 * --------------------------------------------------------------------------- */
add_filter( 'single_template', 'get_services_single_template' );

if ( ! function_exists( 'get_services_single_template' ) ) : 
function get_services_single_template($single_template) {
     global $post;

     if ($post->post_type == 'ei-services') {
          $single_template = plugin_dir_path(__FILE__). 'single-ei-services.php';
     }
     return $single_template;
}
endif;

/* ---------------------------------------------------------------------------
 * EI-Services infinite scroll ajax function
 * --------------------------------------------------------------------------- */
if ( ! function_exists( 'ei_services_infinite_scroll' ) ) : 
	function ei_services_infinite_scroll(){ 
		$postPerPage = (isset($_POST['postPerPage'])) ? $_POST['postPerPage'] : 3;
		$services_order = $_POST['services_order'];
		$pageNum = (isset($_POST['pageNum'])) ? $_POST['pageNum'] : 0;
		
		$args = array(
			'post_type' => 'ei-services',
			'posts_per_page' => $postPerPage,
			'order' => $services_order,
			'paged'    => $pageNum,
		);

		$more_services = new WP_Query($args);
		
		if( $more_services->have_posts() ) :
										
			while ($more_services->have_posts()) : $more_services->the_post(); ?>
			
				<div class="col-md-4 our_services">
					<div class="text-center service_thumb">
						<?php if ( has_post_thumbnail() ) { ?>
							<?php the_post_thumbnail('services-listing', ['class' => 'img-responsive']); ?>
						<?php } ?>
					</div>
					<div class="text-left service_desc">
						<a href="<?php echo get_the_permalink(); ?>">
							<div class="overlay_content">
								<?php //services short description
								$desc = get_post_meta( get_the_ID(), 'short_description', true );
								$short_desc = wp_trim_words( $desc, 30, '' ); ?>
								<p><?php echo $short_desc; ?></p>
								<h4><?php the_title(); ?></h4>
							</div>
						</a>
					</div>
				</div>
				
			<?php endwhile;
			
		endif; 
		
		wp_reset_postdata();
		
		die();
	}
endif;

add_action('wp_ajax_eis_infinite_scroll', 'ei_services_infinite_scroll');
add_action('wp_ajax_nopriv_eis_infinite_scroll', 'ei_services_infinite_scroll');
?>
