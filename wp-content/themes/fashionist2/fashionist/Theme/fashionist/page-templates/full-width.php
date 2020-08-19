<?php
/**
* Template Name: Full width
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

get_header(); ?>

<div class="content container">
<?php 
if ( have_posts() ) {
    // Start the loop.
    while ( have_posts() ) : the_post();
    the_content();
    endwhile;
} 
?>
</div>

<?php get_footer(); ?>