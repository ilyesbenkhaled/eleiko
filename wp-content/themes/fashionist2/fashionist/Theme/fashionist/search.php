<?php
/**
 * The template for displaying search results pages.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
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
<!-- Page content -->
<div class="container">
    <div class="row">
        <div class="col-md-9 col-sm-12 col-xs-12 content search-content">
            
            <?php

            if ( have_posts() ) { ?>
                <div class="col-sm-12"><h1 class="page-title"><?php printf( esc_html__('Search Results for: %s', 'fashionist' ), '<span>' . get_search_query() . '</span>' ); ?></h1></div>
                <?php // Start the loop.
                $counter = 1;
                while ( have_posts() ) : the_post(); ?>
                    <?php
                        $image_id = get_post_thumbnail_id();
                        $image_url = 0;
                        $image_url = wp_get_attachment_image_src($image_id,'full');
                        if($image_url != 0){
                            $image_url = $image_url[0];
                        }else{
                            //$image_url = get_template_directory_uri().'/img/dummy.png' ;
                        }
                                                                
                    ?>
                    <div class="row blog-post">
                        <?php
                        if($image_url){
                        ?>
                        <div class="col-md-3">
                            <div>
                                <img src="<?php echo esc_url($image_url,'fashionist'); ?>" alt="" class="img-responsive" />
                            </div>  
                        </div>
                        <div class="col-md-9">
                        <?php
                        }
                        else{
                        ?>
                        <div class="col-md-12">
                        <?php
                        }
                        ?>    
                            <h2 class="blog-post-title"><?php the_title(); ?></h2>
                            <p class="blog-post-short"><?php echo fashionist_excerpt(250); ?></p>
                            <a href="<?php the_permalink(); ?>" class="blog-readmore"><?php echo esc_html__('Read More','fashionist'); ?></a>
                        </div>
                    </div>              
                <?php $counter++;    
                endwhile;

                echo fashionist_pagination();

            } else { ?>
                <h1><?php esc_html_e('Oops your result not found try another keyword !','fashionist'); ?></h1>
            <?php }
            ?>
            <?php //echo fashionist_pagination(); ?>  
        </div> <!-- end of blog_content -->
                                     
        <aside class="col-xs-12 col-sm-12 col-md-3 sidebar">
             <?php get_sidebar(); ?>            
        </aside>
    </div>
</div>
<!--/ content -->
    
<?php get_footer(); ?>
