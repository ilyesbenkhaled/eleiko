<?php
/**
 * The template for displaying archive pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Fashionist
 */
get_header(); ?>
<div class="sub-nav hidden-xs hidden-sm hidden-md">
    <div class="container">
        <div class="row">
            <span class="sub-nav-span"><?php fashionist_breadcrumb(); ?></span>
        </div>
    </div>
</div>

<!-- Page content -->
<div class="container">
    <div class="row">
        <div class="col-md-9 content" id="blog-archive">
            <?php
            if ( have_posts() ) { while ( have_posts() ) : the_post();?>    
                <div class="col-xs-12 col-md-6">
                    <?php
                        $image_id = get_post_thumbnail_id();
                        $image_url = wp_get_attachment_image_src($image_id,'home_blog');
                        $image_url = $image_url[0];
                    ?>
                    <div class="article" style="background: rgba(155, 155, 145, 0.88) url('<?php echo esc_url($image_url); ?>');">
                        <div class="details">
                            <div class="triangle-separator"></div>
                            <p class="short-description"><?php echo fashionist_excerpt(25); ?></p>
                            <a href="<?php the_permalink(); ?>" class="readmore"><?php echo esc_html__('Read the story','fashionist'); ?></a>
                        </div>
                    
                        <a href="<?php the_permalink(); ?>"><span class="title"><?php the_title(); ?></span></a>
                        <div class="separator"></div>
                        <span class="tagged">
                            <i><?php echo esc_html__('tagged in','fashionist'); ?></i>
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
                        </span>
                    </div>
                </div>            
            <?php  endwhile; }  ?>                                
            
            <?php echo fashionist_pagination(); ?>               
                
        </div>
        
        <aside class="col-xs-12 col-sm-12 col-md-3 sidebar">          
            <?php get_sidebar(); ?>                          
        </aside>
    </div>
</div>

    
<?php get_footer(); ?>