<?php
/*
 * EI-Services custom post type
 */
 if ( ! class_exists( 'EI_Services_Post_Type' ) ) :

	class EI_Services_Post_Type {
		
		function __construct() {

			// Runs when the plugin is activated
			register_activation_hook( __FILE__, array( &$this, 'ei_services_plugin_activation' ) );

			// Adds the ei_services post type and taxonomies
			add_action( 'init', array( &$this, 'ei_services_init' ) );
            
            // Adds ei_services post meta boxes
            add_action( 'add_meta_boxes', array( &$this, 'ei_services_init_add_metaboxes' ) );
            
            //Save ei_services post meta-box values
            add_action('save_post', array( &$this, 'save_services_meta_values' ));	
            
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
	
        }
         
         
        /*
		 * Adding meta-box for ei-services		
		 */   
          
        function ei_services_init_add_metaboxes() {
            
        	add_meta_box("add_cf_shortcode", "Form Shortcode", array( &$this, 'add_form_shortcode' ), "ei-services", "side", "high");

            add_meta_box("add_services_desc", "Services Description", array( &$this, 'add_ei_services_metaboxes' ), "ei-services", "normal", "low");

            add_meta_box("add_cf_info", "Form Fields Instructions", array( &$this, 'add_ei_services_info' ), "ei-services", "side", "high");
           
        }
                     
        function add_ei_services_metaboxes() {

            global $post;
            $custom = get_post_custom($post->ID);
            $short_desc = !empty( $custom["short_description"][0] ) ? $custom["short_description"][0] : "";
            wp_nonce_field('services-nonce', 'services_nonce');
            ?>
            
            <label><?php _e('Description:', 'ei-services');?></label>
            <textarea cols="90" rows="5" name="short_description"><?php echo $short_desc; ?></textarea></p>
            <small><b>Note: </b>Word limit for description is 30.</small>
            			
		<?php }      

		function add_form_shortcode() {

            global $post;
            ?>
            
            <h4>[cf_form id="<?php echo $post->ID; ?>"]</h4>
            			
		<?php }    

		function add_ei_services_info() {

            global $post; ?>
            
            <h4>Please see below instruction to add form fields</h4>
            <p>You can add shortcodes in your custom html structure.</p>
            <p style="color:#a00;">[input type=" " name=" " class=" "]</p>
            <p style="color:#a00;">[textarea name=" " class=" "]</p>
            <p style="color:#a00;">[submit class=" " title=" "]</p>
            			
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
