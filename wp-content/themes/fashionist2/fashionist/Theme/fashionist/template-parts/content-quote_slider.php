<?php
/**
 * Template part for displaying quote posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Fashionist
 */

// WP_Query arguments
$args = array (
	'post_type'              => array( 'post' ),
	'posts_per_page'         => '10',
	'tax_query' => array( 
                        array(
                            'taxonomy' => 'post_format',
                            'field' => 'slug',
                            'terms' => array('post-format-quote'),
                            'operator' => 'IN'
                        )
                    )
);

// The Query
$query = new WP_Query( $args );

// The Loop
if ( $query->have_posts() ) {
	echo '<ul class="quotation-blog-post owlCarousel text-center margin-bottom-70px">';
	while ( $query->have_posts() ) {
		$query->the_post();
		?>
		<li class="item single-quotation">
			<a href="<?php the_permalink(); ?>">
				<h3 class="post_title"><?php the_title(); ?></h3>
			</a>
		
			<p><?php echo wp_trim_words( get_the_content(), 70, '' ); ?></p>
			
			<p class="text-left date"><?php echo get_the_date(); ?> |<span class="author"><?php the_author_posts_link(); ?></span></p>
		</li>
		<?php
	}
	echo '</ul>';
} 
// Restore original Post Data
wp_reset_postdata();