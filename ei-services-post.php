<?php
/*
 * EI-Services custom post type
 */
 if ( ! class_exists( 'EI_Services_Post_Type' ) ) :

	class EI_Services_Post_Type {
		
		function __construct() {

			// Runs when the plugin is activated
			register_activation_hook( __FILE__, array( &$this, 'ei_services_plugin_activation' ) );
			
			// EI Services plugin settings menu
			add_action( 'admin_menu', array( $this, 'ei_services_setting_admin_menu' ) );

			// Adds the ei_services post type and taxonomies
			add_action( 'init', array( &$this, 'ei_services_init' ) );
            
            // Adds ei_services post meta boxes
            add_action( 'add_meta_boxes', array( &$this, 'ei_services_init_add_metaboxes' ) );
            
            //Save ei_services post meta-box values
            add_action('save_post', array( &$this, 'save_services_meta_values' ));	
            
            // Thumbnail support for ei-services posts			
			add_image_size( 'services-listing', 400, 300, true );	
			add_image_size('services-detail-thumb', 700, 550, true);	
			
			//Add frontend scripts
			add_action( 'wp_enqueue_scripts', array( &$this, 'plugin_frontend_scripts' ), 0 );
			
			//Add admin scripts
			add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_admin_styles_scripts'), 0 );
				
		}
		
		/*
		 * Plugin admin scripts
		 */
		 
		function enqueue_admin_styles_scripts() {
			wp_enqueue_script('jquery');		        		        		        		        
			wp_enqueue_style('ei-services-admin-style', EIS_PLUGIN_URL.'css/ei-services-options.css', array(), EIS_PLUGIN_VERSION);
		}
		
		/*
		 * Plugin frontend scripts
		 */
		 
		function plugin_frontend_scripts() {
			
			wp_enqueue_script('jquery');
			wp_enqueue_script( 'ei-services-bootstrap-js', EIS_PLUGIN_URL .'js/bootstrap.min.js', false, EIS_PLUGIN_VERSION, true );
			//Infinite scroll for services
			wp_enqueue_script( 'ei-services-infinitescroll-js', EIS_PLUGIN_URL .'js/services-infinitescroll.js', false, EIS_PLUGIN_VERSION, true );
			wp_localize_script( 'ei-services-infinitescroll-js', 'eis_infinitescroll', 
				array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'services_order' => get_option( 'services_tmporder' ),
					'posts_per_page' => get_option( 'services_num' ),
					'loadFunc' => get_option( 'services_loadmore' )
			) );
								
			wp_enqueue_style('ei-services-bootstrap-css', EIS_PLUGIN_URL.'css/bootstrap.css', array(), EIS_PLUGIN_VERSION);
			wp_enqueue_style('ei-services-font-awesome', EIS_PLUGIN_URL.'css/font-awesome.min.css', array(), EIS_PLUGIN_VERSION);
			wp_enqueue_style('ei-services-style', EIS_PLUGIN_URL.'css/ei_services.css', array(), EIS_PLUGIN_VERSION);
		}


		/*
		 * Flushes rewrite rules on plugin activation
		 */

		function ei_services_plugin_activation() {
			$this->portfolio_init();
			flush_rewrite_rules();
		}

		function ei_services_init() {
						
			/*
			 * Register a ei-services post type
			 */
			$labels = array(
				'name' => __( 'Services', 'ei-services' ),
				'singular_name' => __( 'Service', 'ei-services' ),
				'add_new' => __( 'Add New Service', 'ei-services' ),
				'add_new_item' => __( 'Add New Service', 'ei-services' ),
				'edit_item' => __( 'Edit Service', 'ei-services' ),
				'new_item' => __( 'Add New Service', 'ei-services' ),
				'view_item' => __( 'View Item', 'ei-services' ),
				'search_items' => __( 'Search Services', 'ei-services' ),
				'not_found' => __( 'No services found', 'ei-services' ),
				'not_found_in_trash' => __( 'No services found in trash', 'ei-services' )
			);

			$args = array(
				'labels' => $labels,
				'public' => true,
				'publicly_queryable' => true,
				'show_ui' => true, 
        		'query_var' => true,
				'supports' => array( 'title', 'editor','thumbnail'),
				'capability_type' => 'post',
				'rewrite' => array("slug" => "ei-services"),
                'menu_icon' => 'dashicons-format-gallery',
				'menu_position' => 20,
				'has_archive' => true
			);

			register_post_type( 'ei-services', $args );
            
			/*
			 * Register a taxonomy for ei-services tags
			 */

			$taxonomy_ei_services_tag_labels = array(
				'name' => __( 'Services Tags', 'ei-services' ),
				'singular_name' => __( 'Service Tag', 'ei-services' ),
				'search_items' => __( 'Search Services Tags', 'ei-services' ),
				'popular_items' => __( 'Popular Services Tags', 'ei-services' ),
				'all_items' => __( 'All Services Tags', 'ei-services' ),
				'parent_item' => __( 'Parent Service Tag', 'ei-services' ),
				'parent_item_colon' => __( 'Parent Service Tag:', 'ei-services' ),
				'edit_item' => __( 'Edit Service Tag', 'ei-services' ),
				'update_item' => __( 'Update Service Tag', 'ei-services' ),
				'add_new_item' => __( 'Add New Service Tag', 'ei-services' ),
				'new_item_name' => __( 'New Service Tag Name', 'ei-services' ),
				'separate_items_with_commas' => __( 'Separate service tags with commas', 'ei-services' ),
				'add_or_remove_items' => __( 'Add or remove service tags', 'ei-services' ),
				'choose_from_most_used' => __( 'Choose from the most used service tags', 'ei-services' ),
				'menu_name' => __( 'Services Tags', 'ei-services' )
			);

			$taxonomy_ei_services_tag_args = array(
				'labels' => $taxonomy_ei_services_tag_labels,
				'public' => true,
				'show_in_nav_menus' => true,
				'show_ui' => true,
				'show_tagcloud' => true,
				'hierarchical' => false,
				'rewrite' => array( 'slug' => 'ei_services_tag' ),
				'show_admin_column' => true,
				'query_var' => true
			);

			register_taxonomy( 'ei_services_tag', array( 'ei-services' ), $taxonomy_ei_services_tag_args );
			
			register_taxonomy_for_object_type( 'ei_services_tag', 'ei-services' );

		    /*
			 * Register a taxonomy for ei-services
			 */

			$taxonomy_ei_services_category_labels = array(
				'name' => __( 'Services Categories', 'ei-services' ),
				'singular_name' => __( 'Services Category', 'ei-services' ),
				'search_items' => __( 'Search Services Categories', 'ei-services' ),
				'popular_items' => __( 'Popular Services Categories', 'ei-services' ),
				'all_items' => __( 'All Services Categories', 'ei-services' ),
				'parent_item' => __( 'Parent Services Category', 'ei-services' ),
				'parent_item_colon' => __( 'Parent Services Category:', 'ei-services' ),
				'edit_item' => __( 'Edit Services Category', 'ei-services' ),
				'update_item' => __( 'Update Services Category', 'ei-services' ),
				'add_new_item' => __( 'Add New Services Category', 'ei-services' ),
				'new_item_name' => __( 'New Services Category Name', 'ei-services' ),
				'separate_items_with_commas' => __( 'Separate Services categories with commas', 'ei-services' ),
				'add_or_remove_items' => __( 'Add or remove Services categories', 'ei-services' ),
				'choose_from_most_used' => __( 'Choose from the most used Services categories', 'ei-services' ),
				'menu_name' => __( 'Services Categories', 'ei-services' ),
			);

			$taxonomy_ei_services_category_args = array(
				'labels' => $taxonomy_ei_services_category_labels,
				'public' => true,
				'show_in_nav_menus' => true,
				'show_ui' => true,
				'show_admin_column' => true,
				'show_tagcloud' => true,
				'hierarchical' => true,
				'rewrite' => array('slug' => 'ei_services_cat' ),
				'query_var' => true,
			);

			register_taxonomy( 'ei_services_cat', array( 'ei-services' ), $taxonomy_ei_services_category_args );
			
			register_taxonomy_for_object_type( 'ei_services_cat', 'ei-services' );
	
        }
        
        /*
		 * Added setting page for Services
		 */
		
		function ei_services_setting_admin_menu() {
							
			add_submenu_page( 'edit.php?post_type=ei-services', __( 'Services Settings', 'ei-services' ), __( 'Services Settings', 'ei-services' ), 'manage_options', 'services-settings', array( $this, 'ei_services_settings_page' ) );
		
		}
		
		function ei_services_settings_page() {
			
			if(isset($_REQUEST['update_services_settings']))
			{ 
				if ( !isset($_POST['ei_services_nonce']) || !wp_verify_nonce($_POST['ei_services_nonce'],'services_general_setting') )
				{
				    _e('Sorry, your nonce did not verify.', 'ei-services');
				   exit;
				}
				
				else
				{
					//Updating shortcode options				  	
					$services_title= !empty($_POST['ei_services_title']) ? $_POST['ei_services_title'] : 'Our Services';
				  	update_option('services_title',$services_title);
				  	
				  	$num_of_services= !empty($_POST['number_of_services']) ? $_POST['number_of_services'] : '6';
				  	update_option('services_post_count',$num_of_services);
				  	
				  	$services_orderby= !empty($_POST['services_order_by']) ? $_POST['services_order_by'] : 'none';
				  	update_option('services_orderby',$services_orderby);
				  	
				  	$services_display_order= !empty($_POST['services_order']) ? $_POST['services_order'] : 'DESC';
				  	update_option('services_order',$services_display_order);
				  	
				  	//Updating general template settings
				  	$services_page= !empty($_POST['services-page']) ? $_POST['services-page'] : '';
				  	update_option('services_page',$services_page);
				  	
				  	$services_title_tmp= !empty($_POST['ei_services_title_tmp']) ? $_POST['ei_services_title_tmp'] : 'Our Services';
				  	update_option('services_title_tmp',$services_title_tmp);
				  	
				  	$services_loadmore= !empty($_POST['services_load_more']) ? $_POST['services_load_more'] : 'load_btn';
				  	update_option('services_loadmore',$services_loadmore);
				  	
				  	$services_num= !empty($_POST['services_number']) ? $_POST['services_number'] : '8';
				  	update_option('services_num',$services_num);
				  	
				  	$services_tmp_order= !empty($_POST['services_tmp_order']) ? $_POST['services_tmp_order'] : 'DESC';
				  	update_option('services_tmporder',$services_tmp_order);
				    
				}
			}
			
			?>
			
			<form id="services-setting" method="post" action="" enctype="multipart/form-data" >
				<div class="services_general_settings">
					<div class="eis_settings_title">
						<h1><?php _e( 'Services General Settings', 'ei-services' ); ?></h1>
					</div>
					<div class="general_setup">
						<div class="shortcode_options">
							<!--Services shortcode settings-->
							<div class="eis_full_section">
								<h2><?php _e( 'Generate Services Custom Shortcode Settings', 'ei-services' ); ?></h2>
								<p><?php _e( 'Set given options according to your need and get custom shortcode based on your settings.', 'ei-services' ); ?></p>
							</div>
							<table>
								<tr>
									<?php
									$ei_services_title = get_option('services_title');
									$ei_services_title = !empty($ei_services_title) ? $ei_services_title : 'Our Services';
									?>
									<td class="eis_options"><h4><?php _e( 'Set your services section title', 'ei-services' ); ?></h4></td>
									<td><input type="text" id="ei_form_control" name="ei_services_title" value="<?php _e( $ei_services_title, 'ei-services' ); ?>" /></label></td>
								</tr>
								<tr>
									<?php
									$number_of_services = get_option('services_post_count');
									$number_of_services = !empty($number_of_services) ? $number_of_services : '6';
									?>
									<td><h4><?php _e( 'Set number of services to display', 'ei-services' ); ?></h4></td>
									<td><input type="number" id="ei_form_control" name="number_of_services" value="<?php _e( $number_of_services, 'ei-services' ); ?>" /></label></td>
								</tr>
								<tr>
									<?php
									$services_order_by = get_option('services_orderby');
									$orderby_default='checked';
									if( !empty($services_order_by) ){
										$orderby_default='';
									}						
									?>
									<td class="eis_options"><h4><?php _e( '"Order by" for services sorting', 'ei-services' ); ?></h4></td>
									<td>
										<input type="radio" name="services_order_by" value="none" <?php if (isset ($services_order_by ) ) checked($services_order_by, 'none' ); ?> <?php echo $orderby_default;?>/>
										<label>Default</label>
										
										<input type="radio" name="services_order_by" value="title" <?php if (isset ($services_order_by ) ) checked($services_order_by, 'title' ); ?> />      
										<label>Title</label>
										
										<input type="radio" name="services_order_by" value="date" <?php if (isset ($services_order_by ) ) checked($services_order_by, 'date' ); ?> />      
										<label>Date</label>
									</td>
								</tr>
								<tr>
									<?php
									$services_order = get_option('services_order');
									$order_default='checked';
									if( !empty($services_order) ){
										$order_default='';
									}						
									?>
									<td class="eis_options"><h4><?php _e( 'Set order of services to display', 'ei-services' ); ?></h4></td>
									<td>
										<input type="radio" name="services_order" value="ASC" <?php if (isset ($services_order ) ) checked($services_order, 'ASC' ); ?> <?php echo $order_default;?>/>
										<label>Ascending</label>
										
										<input type="radio" name="services_order" value="DESC" <?php if (isset ($services_order ) ) checked($services_order, 'DESC' ); ?> />      
										<label>Descending</label>
									</td>
								</tr>
								<tr>
									<td colspan="3">
									<p><strong><?php _e('Note:','ei-services'); ?></strong></p>
										<p><?php _e('To generate shortcode to use it in your page please set options given above.','ei-services'); ?></p>
										<?php if( !empty($number_of_services) || !empty($services_order_by) || !empty($services_order) ){ 
											if( !empty($number_of_services) ){
												$count = 'count="'.$number_of_services.'"';
											}
											if( !empty($services_order_by) ){
												$orderby = 'orderby="'.$services_order_by.'"';
											}
											if( !empty($services_order) ){
												$order = 'order="'.$services_order.'"';
											} ?>
											<p><?php _e('Your shortcode is given below:','ei-services'); ?></p>
											<p class="custom_shortcode"><?php _e("[ei_services $count $orderby $order]","ei-services"); ?></p>
										<?php } ?>
									</td>
								</tr>
							</table>
							
							<!--Services general settings-->
							<div class="eis_full_section">
								<h2><?php _e( 'Services display Settings', 'ei-services' ); ?></h2>
								<p><?php _e( 'Set given options according to your need to display services in frontend.', 'ei-services' ); ?></p>
							</div>
							<table>
								<tr>
									<td><h4><?php _e('Services Page', 'ei-services'); ?></h4></td>
									<td>
										<select name="services-page">
											  <option value=""><?php _e('-- select --','ei-services') ?></option>
												<?php
												 $pages = get_pages('sort_column=post_title&hierarchical=0');
												 $ei_services_page_id =get_option('services_page');
												 foreach ( $pages as $page ) {
												   echo '<option value="'.$page->ID.'"'.selected($ei_services_page_id, $page->ID, false).'>'.$page->post_title.'</option>';
												}?>										
										</select>
									</td>
								</tr>
								<tr>
									<?php
									$ei_services_title_tmp = get_option('services_title_tmp');
									$ei_services_title_tmp = !empty($ei_services_title_tmp) ? $ei_services_title_tmp : 'Our Services';
									?>
									<td class="eis_options"><h4><?php _e( 'Set services page title', 'ei-services' ); ?></h4></td>
									<td><input type="text" id="ei_form_control" name="ei_services_title_tmp" value="<?php _e( $ei_services_title_tmp, 'ei-services' ); ?>" /></label></td>
								</tr>
								<tr>
									<?php
									$services_load_more = get_option('services_loadmore');
									$load_default='checked';
									if( !empty($services_load_more) ){
										$load_default='';
									}						
									?>
									<td class="eis_options"><h4><?php _e( 'Load more services setting', 'ei-services' ); ?></h4></td>
									<td>
										<input type="radio" name="services_load_more" value="scroll" <?php if (isset ($services_load_more ) ) checked($services_load_more, 'scroll' ); ?> <?php echo $load_default;?>/>      
										<label>On Scroll</label>
										
										<input type="radio" name="services_load_more" value="load_btn" <?php if (isset ($services_load_more ) ) checked($services_load_more, 'load_btn' ); ?> />      
										<label>Load More Button</label>
									</td>
								</tr>
								<tr>
									<?php
									$services_number = get_option('services_num');
									$services_number = !empty($services_number) ? $services_number : '8';
									?>
									<td><h4><?php _e( 'Set number of services to display', 'ei-services' ); ?></h4></td>
									<td><input type="number" id="ei_form_control" name="services_number" value="<?php _e( $services_number, 'ei-services' ); ?>" /></label></td>
								</tr>
								<tr>
									<?php
									$services_tmp_order = get_option('services_tmporder');
									$order_tmp_default = 'checked';
									if( !empty($services_tmp_order) ){
										$order_tmp_default='';
									}						
									?>
									<td class="eis_options"><h4><?php _e( 'Set order of services to display', 'ei-services' ); ?></h4></td>
									<td>
										<input type="radio" name="services_tmp_order" value="ASC" <?php if (isset ($services_tmp_order ) ) checked($services_tmp_order, 'ASC' ); ?> <?php echo $order_tmp_default;?>/>
										<label>Ascending</label>
										
										<input type="radio" name="services_tmp_order" value="DESC" <?php if (isset ($services_tmp_order ) ) checked($services_tmp_order, 'DESC' ); ?> />      
										<label>Descending</label>
									</td>
								</tr>
							</table>
							<div class="eis_full_section">
								<?php wp_nonce_field( 'services_general_setting', 'ei_services_nonce' ); ?>
								<p class="submit">
									<input id="eis-submit" class="button-primary" type="submit" name="update_services_settings" value="<?php _e( 'Save Settings', 'ei-services' ) ?>" />
								</p> 
							</div>
						</div>
					</div>
				</div>
			</form>
			
		<?php	
		}
         
         
        /*
		 * Adding meta-box for ei-services		
		 */   
          
        function ei_services_init_add_metaboxes() {
            
            add_meta_box("add_services_desc", "Services Description", array( &$this, 'add_ei_services_metaboxes' ), "ei-services", "normal", "low");
           
        }
                     
        function add_ei_services_metaboxes() {
            global $post;
            $custom = get_post_custom($post->ID);
            $short_desc = !empty( $custom["short_description"][0] ) ? $custom["short_description"][0] : '';
            wp_nonce_field('services-nonce', 'services_nonce');
            ?>
            
            <label><?php _e('Description:', 'ei-services');?></label>
            <textarea cols="90" rows="5" name="short_description"><?php echo $short_desc; ?></textarea></p>
            <small><b>Note: </b>Word limit for description is 30.</small>
            			
		<?php }                    
                   
        /*
		 * Save meta-box values for ei-services		
		 */ 
		 
        function save_services_meta_values( $post_id ){
        	
			global $post;
						
			if( isset($_POST['services_nonce']) ){
				if(!wp_verify_nonce( $_POST['services_nonce'], 'services-nonce' ))
					return;
				
				update_post_meta($post->ID, "short_description", $_POST["short_description"]);
				
			}
					 
        }                   

	}
	new EI_Services_Post_Type;
	
endif;
?>
