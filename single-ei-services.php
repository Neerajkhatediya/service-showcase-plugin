<?php
/*
 * EI-Services single page
 */
?>
<?php get_header(); ?>
<div class="services-details">
	<div class="container">
		<div class="row">
			<?php if ( have_posts() ) :
				while ( have_posts() ) : the_post(); ?>
					<!--blogs detail page content-->
					<div class="col-md-6">
						<div class="services-detail-thumb">
							<?php if ( has_post_thumbnail() ) {
								the_post_thumbnail('services-detail-thumb', ['class' => 'img-responsive']);
							} ?>
						</div>
					</div>
					<div class="col-md-6">
						<div class="services-title">
							<h3><?php the_title(); ?></h3>
						</div>
						<div class="services-author">
							<p class="author">Posted by <span><?php the_author(); ?></span> on <span><?php echo get_the_date( 'dS F Y' ); ?></span></p>
						</div>
						<div class="services-content">
							<?php the_content(); ?>
						</div>
					</div>
			<?php endwhile;
			endif; ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>
