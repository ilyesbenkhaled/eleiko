<?php
/**
* Template Name: Blog
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
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

<!-- Page content -->
<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-offset-1 col-md-10">

            <?php
            $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
            // WP_Query arguments
            $query_args = array (
                'post_type' => array( 'post' ),                    
                'posts_per_page' => 3,                        
                'paged' => $paged,
                'tax_query' => array( 
                                    array(
                                        'taxonomy' => 'post_format',
                                        'field' => 'slug',
                                        'terms' => array('post-format-aside', 'post-format-image', 'post-format-video', 'post-format-quote', 'post-format-link'),
                                        'operator' => 'NOT IN'
                                    )
                                )                        
            );

            // The Query                    
            query_posts ( $query_args );  

            // The Loop
            if ( have_posts() ) {
                // Start the loop.
                $counter = 1;
                while ( have_posts() ) : the_post(); ?>
                    <?php
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
                    <!-- Blog post preview -->
                    <div class="blog-post text-center">
                        <div class="blog-post-img" <?php if(!$image_url){ echo 'style="background:#c3c3c3"'; ;} ?>>
                            <?php if($image_url){ ?>
                                <img src="<?php echo esc_url($image_url); ?>" alt="" />
                            <?php } ?>    
                            <div class="text hidden-xs">
                                <a href="<?php echo get_the_permalink(); ?>"><span class="title"><?php the_title(); ?></span></a>
                                <div class="separator"></div>
                                <span class="tagged">
                                    <i><?php echo esc_html__('tagged in','fashionist'); ?></i>
                                    <b>
                                    <?php
                                    $tags = wp_get_post_tags($post->ID);                            
                                    $tagLine = '';
                                    $tagCount = 1;
                                    foreach ( $tags as $tag ) { 
                                        $tagLine .= esc_attr($tag->name,'fashionist').', ';
                                        if( $tagCount > 10 ){ 
                                            $tagLine .= '...';
                                            break; 
                                        }  
                                        $tagCount++;
                                    }
                                    echo rtrim($tagLine, ', ');
                                    ?>
                                    </b>
                                </span>
                            </div>
                        </div>
                        <a href="<?php echo get_the_permalink(); ?>"><h2 class="blog-post-title"><?php the_title(); ?></h2></a>
                        <div class="blog-post-details"><span><span class="icon-user"></span> <?php echo esc_html__('by','fashionist'); ?> <?php echo get_the_author(); ?></span> <span><span class="icon-bookmark"></span> <?php echo esc_html__('in','fashionist'); ?> <?php echo get_the_category_list(', ',',',$post->ID); ?></span> <span><span class="icon-comment"></span> <?php echo esc_html($temp_comm); ?> </span></div>
                        <p class="blog-post-short"><?php echo fashionist_excerpt(250); ?></p>
                        <a href="<?php the_permalink(); ?>" class="blog-readmore"><?php echo esc_html__('Read the story','fashionist'); ?></a>
                    </div>
                <?php $counter++;    
                endwhile;
            } 
            ?>                    
            
        </div> <!-- end of blog_content -->

        <?php echo fashionist_pagination(); ?>                               
    </div>
</div>
<!--/ content -->
	
<?php get_footer(); ?>