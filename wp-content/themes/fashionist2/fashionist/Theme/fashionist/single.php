<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Fashionist
 */

$blogSingle = FashionistOptions::get( 'blog_single' );

if(isset($_GET['style'])){
	$blogSingle = $_GET['style'];
}
get_header(); ?>

<div class="sub-nav hidden-xs hidden-sm hidden-md">
    <div class="container">
        <div class="row">
            <?php
			$class = 'col-xs-12 col-md-offset-1 col-md-10';
			if($blogSingle == 2 ) { $class = 'col-md-9'; }
			?>
			<div class="<?php echo esc_attr( $class ); ?>">
            	<span class="sub-nav-span"><?php fashionist_breadcrumb(); ?></span>
            </div>
        </div>
    </div>
</div>

<!-- content -->
<div class="container">
	<div class="row">
		<?php
			$class = 'col-xs-12 col-md-offset-1 col-md-10 content';
			if($blogSingle == 2 ) { $class = 'col-md-9 content'; }
		?>
		<div class="<?php echo esc_attr( $class ); ?>">
			
				<div class="blog-post">
					<?php while ( have_posts() ) { the_post(); 
						$image_id = get_post_thumbnail_id();
						$image_url = wp_get_attachment_image_src($image_id,'full');
						$image_url = $image_url[0];
						$comments_count = wp_count_comments($post->ID);
					    $temp_comm = '0 comment';
					    if($comments_count->total_comments == 1 || $comments_count->total_comments == 0){
					        $temp_comm = $comments_count->total_comments.' comment';    
					    }else{
					        $temp_comm = $comments_count->total_comments.' comments';
					    }
					?>

					 <div class="blog-post-img" <?php if(!$image_url){ echo 'style="background:#c3c3c3"'; ;} ?>>
                            <?php if($image_url){ ?>
                                <img src="<?php echo esc_url($image_url); ?>" alt="" />
                            <?php } ?>    
						<div class="text fixed hidden-xs">
							<span class="title"><?php the_title(); ?></span>
							<div class="separator"></div>
							<span class="tagged"><i><?php echo esc_html__('tagged in','fashionist'); ?></i> <b><?php the_tags(); /*$tags = get_tags();  foreach ( $tags as $tag ) { echo esc_attr($tag->name).', ';*/  ?></b></span>
						</div>
					</div>
					<div class="blog-post-details"><span><span class="icon-user"></span> by <?php echo get_the_author(); ?></span> <span><span class="icon-bookmark"></span> in <?php echo get_the_category_list(', ',',',$post->ID); ?></span> <span><span class="icon-comment"></span> <?php echo esc_html($temp_comm); ?></span>
					</div>

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
				
				<div class="share">
					<span class="text"><?php echo esc_html__('Share','fashionist'); ?></span>
					<a href="http://www.facebook.com/sharer.php?u=<?php echo get_the_permalink(); ?>" target="_blank"><span class="icon-facebook"></span></a>
					<a href="https://twitter.com/share?url=<?php echo get_the_permalink(); ?>&amp;text=<?php echo urlencode(get_the_title()); ?>"><span class="icon-twitter"></span></a>
					<a href="https://plus.google.com/share?url=<?php echo get_the_permalink(); ?>" target="_blank"><span class="icon-google-plus"></span></a>
					<a href="#"><span class="icon-pinterest"></span></a>
					<a href="#"><span class="icon-instagram-square"></span></a>
				</div>

				<?php
					$args_user = array('size'=>112,'force_default'=>true);
					$get_avatar_url = get_avatar_url( get_the_author_meta( 'ID' ), $args_user );
				?>											
				<?php if(get_the_author_meta('user_description')){ ?>
					<div class="author">
						<div class="row">
							<div class="col-md-2 hidden-xs hidden-sm">
								<img class="author-img" src="<?php echo esc_url($get_avatar_url,'fashionist'); ?>" alt="" />
							</div>
							<?php $author_id = get_the_author_meta('ID'); ?>	
							<div class="col-xs-12 col-md-10">
								<div class="author-info">
									<span class="author-name"><?php echo get_the_author_meta('first_name').' '.get_the_author_meta('last_name'); ?></span>
									<span class="author-job"><?php if(fashionist_checkPlugin('advanced-custom-fields-pro/acf.php') ){  the_field('designation', 'user_'.$author_id); } ?></span>
								</div>
								<p class="author-about"><?php echo get_the_author_meta('user_description'); ?></p>
							</div>
						</div>
					</div>
				<?php } ?>

				<div class="comment">
				<?php
					// If comments are open or we have at least one comment, load up the comment template.
					if ( comments_open() || get_comments_number() ) {
						comments_template();
					}
				?>					
				</div>	
				<?php } // End of the loop. ?>							
				
			</div> <!-- end of blog_content -->
			<?php  if($blogSingle == 2 ){ ?>
				<aside class="col-md-3 sidebar">
					<?php get_sidebar(); ?>
				</aside>
			<?php } ?>

		</div> <!-- end of col-md-9 -->
	</div>


<?php get_footer(); ?>