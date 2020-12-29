<?php
add_shortcode( 'ei_services', 'ei_services_Shortcode' );

function ei_services_Shortcode( $attr, $content = null )
{
   extract(shortcode_atts(array(
		'count' => '',
		'category' => '',
		'orderby' => 'menu_order',
		'order' => 'DESC',
	), $attr));
	    
	$args = array( 
		'post_type' => 'ei-services',
		'posts_per_page' => intval($count),
        'post_status'=> 'publish',
		'orderby' => $orderby,
		'order' => $order,	
		'ignore_sticky_posts' =>1,
	);
    
    $services = new WP_Query( $args ); ?>
    
	<div class="services_sc">
		<div class="container">
			<div class="row">
			
			<?php $sr_title = get_option('services_title'); 
			if( !empty($sr_title) ){ ?>
				<div class="col-md-12 text-center section_title">
					<h1><?php echo $sr_title; ?></h1>
				</div>
			<?php } ?>
			
			<div class="clearfix"></div>
			<?php if ($services->have_posts()) :
					
					while ($services->have_posts()) : $services->the_post(); ?>
						
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
				
				wp_reset_query(); ?>
			</div>
		</div>
	</div>
<?php } ?>
