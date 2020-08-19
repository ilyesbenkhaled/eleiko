<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<?php wc_print_notices(); ?>

<?php do_action( 'woocommerce_before_customer_login_form' ); ?>


<div class="content" id="login-page">
	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-md-6 login">
				<div class="row">
					<form method="post">
						<?php do_action( 'woocommerce_login_form_start' ); ?>
						<div class="col-xs-12">
							<span class="title"><?php esc_html_e( 'Sign in to your account', 'fashionist' ); ?></span>
						</div>
						
						<div class="col-xs-12 col-md-6">
							<label for="email"><?php esc_html_e( 'Email address', 'fashionist' ); ?> <em>*</em></label>
							<input type="text" class="your-email" name="username" id="username" value="<?php if ( ! empty( $_POST['username'] ) ) echo esc_attr( $_POST['username'] ); ?>" />
						</div>
						
						<div class="col-xs-12 col-md-6">
							<label for="pass"><?php esc_html_e( 'Password', 'fashionist' ); ?> <em>*</em></label>
							<input class="password" type="password" name="password" id="password" value="<?php if ( ! empty( $_POST['password'] ) ) echo esc_attr( $_POST['password'] ); ?>" />
						</div>
						
						<div class="col-xs-12">
							<input class="remember-pass" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <label class="checkbox-label" for="remember"><?php esc_html_e( 'Remember Me', 'fashionist' ); ?> </label>
							<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="forgot-pass"><?php esc_html_e( 'Forgot password?', 'fashionist' ); ?></a>
						</div>
						
						<div class="col-xs-12">
							<div class="buttons">
								<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
								<input type="submit" class="fashionist-btn default-bordered" name="login" value="<?php esc_attr_e( 'Sign In', 'fashionist' ); ?>" />	
								<?php if(fashionist_checkPlugin('accesspress-social-login-lite/accesspress-social-login-lite.php')) { echo do_shortcode('[apsl-login-lite]');} ?>
							</div>
						</div>
						<?php do_action( 'woocommerce_login_form_end' ); ?>
					</form>
				</div>
			</div>

			<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>

			<div class="col-xs-12 col-md-6 login">
				<div class="row">
					<form method="post">
						<?php do_action( 'woocommerce_register_form_start' ); ?>
						<div class="col-xs-12">
							<span class="title"><?php esc_html_e( 'Register account', 'fashionist' ); ?></span>
						</div>
						
						<div class="col-xs-12 col-md-6">
							<label for="first-name"><?php esc_html_e( 'First name', 'fashionist' ); ?> <em>*</em></label>
							<input type="text" name="first_name" value="<?php if ( ! empty( $_POST['first_name'] ) ) echo esc_attr( $_POST['first_name'] ); ?>" class="your-first-name" id="your-first-name">
						</div>
							
						<div class="col-xs-12 col-md-6">
							<label for="last-name"><?php esc_html_e( 'Last name', 'fashionist' ); ?> <em>*</em></label>
							<input type="text" name="last_name" value="<?php if ( ! empty( $_POST['last_name'] ) ) echo esc_attr( $_POST['last_name'] ); ?>" class="your-last-name" id="your-last-name">
						</div>
						
						<div class="col-xs-12 col-md-6">
							<label for="email1"><?php esc_html_e( 'Email address', 'fashionist' ); ?> <em>*</em></label>
							<input type="email" class="your-email" name="email" id="reg_email" value="<?php if ( ! empty( $_POST['email'] ) ) echo esc_attr( $_POST['email'] ); ?>" />		
						</div>
							
						<div class="col-xs-12 col-md-6">
							<label for="email2"><?php esc_html_e( 'password', 'fashionist' ); ?> <em>*</em></label>
							<input class="password" type="password" name="password" id="password" />
						</div>											
						<?php do_action( 'woocommerce_register_form' ); ?>
						<?php do_action( 'register_form' ); ?>		
						<div class="col-xs-12">
							<div class="buttons">
								<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
								<input type="submit" class="fashionist-btn bordered" name="register" value="<?php esc_attr_e( 'Sign Up', 'fashionist' ); ?>" />	
							</div>
						</div>
						<?php do_action( 'woocommerce_register_form_end' ); ?>	
					</form>
				</div>
			</div>

			<?php endif; ?>

		</div>
	</div>
</div>
<?php do_action( 'woocommerce_after_customer_login_form' ); ?>