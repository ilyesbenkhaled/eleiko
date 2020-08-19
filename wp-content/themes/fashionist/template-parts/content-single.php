<?php
/**
 * Template part for displaying single posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Fashionist
 */

$categories = get_the_category();
$separator = ', ';
$output = '';
if ( ! empty( $categories ) ) {
    foreach( $categories as $category ) {
        $output .= '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" alt="' . esc_attr( sprintf( esc_html__( 'View all posts in %s', 'fashionist' ), $category->name ) ) . '">' . esc_html( $category->name ) . '</a>' . $separator;
    }
    $cat_list = trim( $output, $separator );
}

?>
<article class="single_blog_post">
    <a href="<?php the_permalink(); ?>"><img class="img-responsive" src="<?php the_post_thumbnail_url( 'blog_full' ); ?>" alt="" /></a>
    <a href="<?php the_permalink(); ?>"><h3 class="post_title"><?php the_title(); ?></h3></a>
    
    <h4><span class="date">
    	<a href="<?php echo get_month_link(get_the_time('Y'), get_the_time('m')); ?>"><?php echo esc_html( get_the_date() ); ?></a></span> | <span class="author"><?php the_author_posts_link(); ?></span> | in <span class="category"><?php echo wp_kses_post( $cat_list ); ?></span>
    </h4>

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

	<div class="post_metas">
		<div class="post_tags">
			<span><?php esc_html_e( 'Tags', 'fashionist' ); ?></span>
			<?php the_tags( '<ul><li>', '</li><li>', '</li></ul>' ); ?>			
		</div> <!-- end of post_tags -->
		<div class="social_shares">
			<span><?php esc_html_e( 'Share', 'fashionist' ); ?></span>
			<ul>
				<li><a href="https://www.facebook.com/sharer/sharer.php?u=<?php the_permalink(); ?>"><i class="fa fa-facebook"></i></a></li>
				<li><a href="https://twitter.com/home?status=<?php the_permalink(); ?>"><i class="fa fa-twitter"></i></a></li>				
				<li><a href="https://plus.google.com/share?url=<?php the_permalink(); ?>"><i class="fa fa-google-plus "></i></a></li>
				<li><a href="https://pinterest.com/pin/create/button/?url=<?php the_permalink(); ?>&media=<?php the_post_thumbnail_url( 'blog_full' ); ?>&description=<?php echo urlencode(get_the_title()); ?>"><i class="fa fa-pinterest-p "></i></a></li>
			</ul>
		</div> <!-- end of social_shares -->
	</div> <!-- end of post meta -->

    <div class="clearfix"></div>
</article> <!-- end of standard_blog_post -->