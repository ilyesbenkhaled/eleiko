<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
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
<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">

		<section class="error-404 not-found">
			<header class="page-header">
				<h1 class="errorText"><?php esc_html_e('404','fashionist'); ?></h1>
				<h1 class="page-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'fashionist' ); ?></h1>
			</header><!-- .page-header -->
		</section><!-- .error-404 -->

	</main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>
