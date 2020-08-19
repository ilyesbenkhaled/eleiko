<?php
/**
* Template Name: Page With Sidebar
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

get_header(); ?>
<!-- page title -->
<div class="page_title_area">
	<div class="container">
		<div class="row">
			<div class="col-sm-8">
				<div class="page_title">
					<h1><?php the_title();?></h1>
				</div>
			</div>
			<div class="col-sm-4">
				<?php fashionist_breadcrumb(); ?>
			</div>
		</div>
	</div>
</div>
<!--/ page title -->

<div class="margin-bottom-70px">
	<div class="container">
		<div class="row">
            <div class="col-md-9 col-sm-12 col-xs-12">
				<?php 
				if ( have_posts() ) {
				    // Start the loop.
				    while ( have_posts() ) : the_post();
				    the_content();
				    endwhile;
				} 
				?>
			</div>
			<!-- sidebar -->
            <div class="col-md-3 col-sm-12 col-xs-12">
                <div class="sidebar">
                    <?php get_sidebar(); ?>
                </div> <!-- end of sidebar -->
            </div> <!-- end of col-md-3 -->
		</div>
	</div>
</div>

<?php get_footer(); ?>