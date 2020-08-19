<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Fashionist
 */

get_header(); ?>
<!-- page title -->
<div class="sub-nav hidden-xs hidden-sm hidden-md">
	<div class="container">
		<div class="row">
			<span class="sub-nav-span"><?php fashionist_breadcrumb(); ?></span>
		</div>
	</div>
</div>
<!--/ page title -->

<div class="content container">
	
	<?php 
	if ( have_posts() ) {
	    // Start the loop.
	    while ( have_posts() ) : the_post();

	    ?>

	    <div class="blog-post">

		    <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

		    <div class="entry-content-post">
				<?php the_content(); ?>
			</div>
		    
			<?php
			wp_link_pages( array(
				'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'fashionist' ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
				'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'fashionist' ) . ' </span>%',
			) );
			?>
			
		</div>	

		<?php	
	    if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
	    endwhile;
	} 
	?>

</div>

<?php get_footer(); ?>