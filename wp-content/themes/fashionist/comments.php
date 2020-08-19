<?php
/**
 * The template for displaying comments.
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Fashionist
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<section class="comments">
	<div class="title">
		<?php
			$comments_number = get_comments_number();
			if ( 1 === $comments_number ) {
				/* translators: %s: post title */
				printf( _x( 'One thought on &ldquo;%s&rdquo;', 'comments title', 'fashionist' ), get_the_title() );
			} else {
				printf(
					/* translators: 1: number of comments, 2: post title */
					_nx(
						'%1$s thought on &ldquo;%2$s&rdquo;',
						'%1$s thoughts on &ldquo;%2$s&rdquo;',
						$comments_number,
						'comments title',
						'fashionist'
					),
					number_format_i18n( $comments_number ),
					get_the_title()
				);
			}
		?>
		</div>
	
	<div class="row">
		<ul class="comment">			
			<?php wp_list_comments( 'type=comment&callback=fashionist_comment' ); ?>
		</ul>	
	</div>
	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
		<nav id="comment-nav-below" class="navigation comment-navigation" role="navigation">
			<h2 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'fashionist' ); ?></h2>
			<div class="nav-links">

				<div class="nav-previous"><?php previous_comments_link(); ?></div>
				<div class="nav-next"><?php next_comments_link(); ?></div>

			</div><!-- .nav-links -->
		</nav><!-- #comment-nav-below -->
	<?php endif; // Check for comment navigation. ?>
	

	<div class="comment_form_section xs-mb-40">
	<?php
	$req = get_option( 'require_name_email' );
	$aria_req = ( $req ? " aria-required='true'" : '' );

	$comment_args = array( 

		'title_reply_before' => '<h2 class="comment_form_title"><span>',
		'title_reply_after' => '</span></h2>',
		'comment_notes_before' => '',
		'class_form' => 'comment_form',

		'fields' => apply_filters('comment_form_default_fields', 
					array(

						'author' =>
						      '<div class="inline_form col-md-4 no-padding-left">' .
						      '<label for="v_name">' . esc_html__( 'Name', 'fashionist' ) . '' .
						      ( $req ? '<sup>*</sup>' : '' ) .
						      '</label><input id="v_name" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) .
						      '" size="30"' . $aria_req . ' /></div>',						
						
						'email' =>
						      '<div class="inline_form col-md-4 no-padding-left"><label for="v_email">' . esc_html__( 'Email', 'fashionist' ) . ' ' .
						      ( $req ? '<sup>*</sup>' : '' ) .
						      '</label><input id="v_email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) .
						      '" size="30"' . $aria_req . ' /></div>',			    
						
						'url' =>
					      '<div class="inline_form col-md-4 no-padding-left "><label for="v_website">' .
					      esc_html__( 'Website', 'fashionist' ) . '</label>' .
					      '<input id="v_website" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) .
					      '" size="30" /></div>',			      		
					    
					    )
					),
				

	    'comment_field' => '<div class="clear-fix"></div><div class="text_area_form col-md-12 no-padding-left">' .

	                '<label for="v_comment">' . esc_html__( 'Your Comment' ,'fashionist') . '</label>' .

	                '<textarea id="v_comment" name="comment" rows="8" aria-required="true"></textarea>' .

	                '</div>',

	);
	
	comment_form($comment_args);
	?>
	</div>
</section>