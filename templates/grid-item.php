<?php
/**
 * The template used for displaying post content inside a grid.
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'card mb-4' ); ?>>

	<?php if ( has_post_thumbnail() ) : ?>
	<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true">
		<?php the_post_thumbnail( 'large', array( 'alt' => get_the_title(), 'class' => 'card-img-top' ) ); ?>
	</a>
	<?php endif; ?>

	<div class="card-body">

		<?php the_title( '<h5 class="entry-title card-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h5>' ); ?>

		<div class="card-text">

			<div class="entry-summary">
				<?php the_excerpt(); ?>
			</div>

			<p class="more">
				<a href="<?php the_permalink(); ?>" class="more-link btn btn-primary"><?php esc_html_e( 'Read More' ); ?></a>
			</p>

		</div><!-- .card-text -->

	</div>

</article><!-- #post-## -->
