<?php
/*
 * You can override this template by copying this template 
 * to your parent or child theme and customize it.
 */
?>
<?php get_header(); ?>
<div class="our-services">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="services_section_title">
					<div class="services_sc">
						<div class="container">
							<div class="row">
							
							<?php $sr_title = get_option('services_title_tmp'); 
							if( !empty($sr_title) ){ ?>
								<div class="col-md-12 text-center section_title">
									<h1><?php echo $sr_title; ?></h1>
								</div>
							<?php } ?>
							
							<?php  $postsPerPage = get_option('services_num');
							$order = get_option('services_tmporder');
							
							$args = array(
								'post_type' => 'ei-services',
								'posts_per_page' => $postsPerPage,
								'order' => $order
							);

							$service_loop = new WP_Query( $args );
							if( $service_loop->have_posts() ) :
															
								while ($service_loop->have_posts()) : $service_loop->the_post(); ?>	
								
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
								
								<?php endwhile; ?>
								
								<div class="more_content">
								<!--Load more content section-->
								</div>
								<div class="col-md-12 text-center">
									<h2 class="no_data"></h2>
									<?php $loadMore = get_option( 'services_loadmore' ); 
									if( $loadMore == "load_btn" ){ ?>
										<button class="btn btn-primary loadMore_btn">Load more</button>
									<?php } ?>
									<div class="loader_img">
										<img src="<?php echo EIS_PLUGIN_URL. 'images/ajax-loader-1.gif'; ?>" class="loadmore_img" />
									</div>
								</div>
							
							<?php else: 
								
								echo "<p class='text-center'>No data found.</p>";
						
							endif; 
							wp_reset_postdata(); ?>
							
						</div>
					</div>
				</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php get_footer(); ?>
