<?php
/**
 * Fashionist functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Fashionist
 */

/*-------- Global Declaration for redux variable -------- */
if ( ! class_exists( 'FashionistOptions' ) ):
    class FashionistOptions {
        static public function is( $key, $compare ) {
            $value = static::get( $key );
            return $value === $compare;
        }
        static public function not( $key, $compare ) {
            $value = static::get( $key );
            return $value !== $compare;
        }
        static public function has( $key ) {
            $value = static::get( $key );
            return ! empty( $value );
        }
        static public function get( $key ) {
            global $fashionist_options;
            if ($fashionist_options == null) {
                return null;
            }
            return isset( $fashionist_options[ $key ] ) ? $fashionist_options[ $key ] : null;
        }
    }
endif; // FashionistOptions

/*-------- Check Plugin available or not -----*/

if( !function_exists( 'fashionist_checkPlugin' ) ) {
    function fashionist_checkPlugin($path = '')
    {
        if (strlen($path) == 0) return false;
        $_actived = apply_filters('active_plugins', get_option('active_plugins'));
        if (in_array(trim($path), $_actived)) return true;
        else return false;
    }
}

if(	fashionist_checkPlugin('advanced-custom-fields-pro/acf.php') ){
	require get_template_directory() . '/inc/acf-import.php';
}

if ( ! function_exists( 'fashionist_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */

function fashionist_setup() {

	/*
	 * Add Redux Framework
	 */

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary Menu', 'fashionist' ),
		'mobile' => esc_html__( 'Mobile Menu', 'fashionist' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'fashionist_custom_background_args', array(
		'default-color' => esc_html__('ffffff','fashionist'),
		'default-image' => esc_attr__('','fashionist'),
	) ) );

	$args = array(
		'width'         => 980,
		'height'        => 60,
		'default-image' => get_template_directory_uri() . '/img/dummy.png',
	);
	add_theme_support( 'custom-header', $args );

}
endif; // fashionist_setup
add_action( 'after_setup_theme', 'fashionist_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function fashionist_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'fashionist_content_width', 640 );
}
add_action( 'after_setup_theme', 'fashionist_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function fashionist_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'fashionist' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__('Show as default sidebar', 'fashionist' ),
		'before_widget' => '<div id="%1$s" class="box">',
		'after_widget'  => '</div>',
		'before_title'  => '<div class="title">',
		'after_title'   => '</div>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Single Product Sidebar', 'fashionist' ),
		'id'            => 'sidebar-2',
		'description'   => esc_html__('Showing in single product page', 'fashionist'),
		'before_widget' => '<div id="%1$s" class="sidebar_widget">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget_title">',
		'after_title'   => '</h3>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Top Footer', 'fashionist' ),
		'id'            => 'footer-top',
		'description'   => esc_html__('showing in top footer section', 'fashionist' ),
		'before_widget' => '<div id="%1$s" class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2>',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Footer Left', 'fashionist' ),
		'id'            => 'footer-left',
		'description'   => esc_html__('showing in footer left section', 'fashionist' ),
		'before_widget' => '<div id="%1$s" class="footer_left">',
		'after_widget'  => '</div>',
		'before_title'  => '<div class="logofooter">',
		'after_title'   => '</div>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Footer Right', 'fashionist' ),
		'id'            => 'footer-right',
		'description'   => esc_html__('showing in footer right section', 'fashionist' ),
		'before_widget' => '<div id="%1$s" class="col-xs-6 col-md-3 col-lg-2 footer_right">',
		'after_widget'  => '</div>',
		'before_title'  => '<span class="row-title">',
		'after_title'   => '</span>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Footer Cards', 'fashionist' ),
		'id'            => 'footer-cards',
		'description'   => esc_html__('showing in footer cards section', 'fashionist' ),
		'before_widget' => '<div id="%1$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<span class="row-title">',
		'after_title'   => '</span>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Languages for Header', 'fashionist' ),
		'id'            => 'languages',
		'description'   => esc_html__('showing in top header section', 'fashionist' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Currency for Header', 'fashionist' ),
		'id'            => 'currency',
		'description'   => esc_html__('showing in top header section', 'fashionist' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<div onclick="open_close_dropdown(\'currency-dropdown\')"><span>',
		'after_title'   => '<span class="icon-down"></span></span></div>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Woocommerce', 'fashionist' ),
		'id'            => 'woocommerce',
		'description'   => esc_html__('showing in woocommerce pages', 'fashionist' ),
		'before_widget' => '<div id="%1$s" class="box %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<div class="title">',
		'after_title'   => '</div>',
	) );
}
add_action( 'widgets_init', 'fashionist_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function fashionist_scripts() {

	/* BOOTSTRAP */
    wp_enqueue_style( 'fashionist_bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css', array(), null );

    /* FONT AWESOME */
    wp_enqueue_style( 'fashionist_iconfont', get_template_directory_uri() . '/iconfont/style.css', array(), null );

    /* LightBox CSS */
    wp_enqueue_style( 'fashionist_lightbox', get_template_directory_uri() . '/css/lightbox.css', array(), null );

    /* style CSS */
    wp_enqueue_style( 'fashionist_stylesheet', get_template_directory_uri() . '/css/style.css', array(), null );
    /* Theme CSS */
    wp_enqueue_style( 'fashionist_stylesheets', get_template_directory_uri() . '/style.css', array(), null );
    /* sss CSS */
    wp_enqueue_style( 'fashionist_sssstyle', get_template_directory_uri() . '/css/sss.css', array(), null );
    /* rs css*/
    wp_enqueue_style( 'fashionist_rsstyle', get_template_directory_uri() . '/css/rs-slider.css', array(), null );

	/* jquery ui */
    wp_enqueue_script( 'jquery-ui-core' );
    wp_enqueue_script( 'jquery-ui-widget' );
    wp_enqueue_script( 'jquery-ui-mouse' );
    wp_enqueue_script( 'jquery-ui-accordion' );
    wp_enqueue_script( 'jquery-ui-autocomplete' );
    wp_enqueue_script( 'jquery-ui-slider' );
    wp_enqueue_script( 'jquery-ui-tabs' );
    wp_enqueue_script( 'jquery-ui-sortable' );
    wp_enqueue_script( 'jquery-ui-draggable' );
    wp_enqueue_script( 'jquery-ui-droppable' );
    wp_enqueue_script( 'jquery-ui-datepicker' );
    wp_enqueue_script( 'jquery-ui-resize' );
    wp_enqueue_script( 'jquery-ui-dialog' );
    wp_enqueue_script( 'jquery-ui-button' );
    /* sss js*/
    wp_enqueue_script( 'fashionist_sssscript', get_template_directory_uri() . '/scripts/sss.js' , array(), null , true);
    /*LightBox JS*/
    wp_enqueue_script( 'fashionist_lightboxscript', get_template_directory_uri() . '/scripts/lightbox.js' , array(), null , true);
     /* Bootstrap */
    wp_enqueue_script( 'fashionist_bootstrap', get_template_directory_uri() . '/scripts/bootstrap.min.js' , array(), null , true );
    /* Fashionist js*/
    wp_enqueue_script( 'fashionist', get_template_directory_uri() . '/scripts/fashionist.js' , array(), null , true);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'fashionist_scripts' );

/* add-to-cart-variation */
add_action( 'wp_enqueue_scripts','load_scripts', 999 );
function load_scripts() {
	wp_deregister_script( 'wc-add-to-cart-variation' );
	wp_enqueue_script( 'wc-add-to-cart-variation', get_template_directory_uri() . '/scripts/add-to-cart-variation.js' , array( 'jquery', 'wp-util' ), null , true );
}
/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';
/**
 * Declare WooCommerce support
 */
add_action( 'after_setup_theme', 'fashionist_woocommerce_support' );
function fashionist_woocommerce_support() {
    add_theme_support( 'woocommerce' );
}

/* Add Images siges */
add_image_size( 'fashionist_product_list', 345 , 440, true );

/**
 * This file represents an example of the code that themes would use to register
 * the required plugins.
 *
 * It is expected that theme authors would copy and paste this code into their
 * functions.php file, and amend to suit.
 */
require_once get_template_directory() . '/inc/class-tgm-plugin-activation.php';
add_action( 'tgmpa_register', 'fashionist_register_required_plugins' );

/**
 * Register the required plugins for this theme.
 *
 * In this example, we register five plugins:
 * - one included with the TGMPA library
 * - two from an external source, one from an arbitrary source, one from a GitHub repository
 * - two from the .org repo, where one demonstrates the use of the `is_callable` argument
 *
 * The variables passed to the `tgmpa()` function should be:
 * - an array of plugin arrays;
 * - optionally a configuration array.
 * If you are not changing anything in the configuration array, you can remove the array and remove the
 * variable from the function call: `tgmpa( $plugins );`.
 * In that case, the TGMPA default settings will be used.
 *
 * This function is hooked into `tgmpa_register`, which is fired on the WP `init` action on priority 10.
 */
function fashionist_register_required_plugins() {
	/*
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	$plugins = array(

		// This is an example of how to include a plugin bundled with a theme.
		array(
			'name'               => esc_html__('Fashionist Themes Plugin','fashionist'), // The plugin name.
			'slug'               => 'fashionistthemes-plugin', // The plugin slug (typically the folder name).
			'source'             => esc_url('http://kl-webmedia.com/demo/wp/plugins/fashionistthemes-plugin.zip'), // The plugin source.
			'required'           => true, // If false, the plugin is only 'recommended' instead of required.
			'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
			'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
			'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			'external_url'       => '', // If set, overrides default API URL and points to an external URL.
			'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
		),
		array(
			'name'               => esc_html__('Slider Revolution','fashionist'), // The plugin name.
			'slug'               => 'revslider', // The plugin slug (typically the folder name).
			'source'             => esc_url('http://kl-webmedia.com/demo/wp/plugins/revslider.zip'), // The plugin source.
			'required'           => true, // If false, the plugin is only 'recommended' instead of required.
			'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
			'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
			'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			'external_url'       => '', // If set, overrides default API URL and points to an external URL.
			'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
		),
		array(
			'name'               => esc_html__('Visual Composer','fashionist'), // The plugin name.
			'slug'               => 'js_composer', // The plugin slug (typically the folder name).
			'source'             => esc_url('http://kl-webmedia.com/demo/wp/plugins/js_composer.zip'), // The plugin source.
			'required'           => true, // If false, the plugin is only 'recommended' instead of required.
			'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
			'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
			'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			'external_url'       => '', // If set, overrides default API URL and points to an external URL.
			'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
		),
		array(
			'name'               => esc_html__('Woocommerce','fashionist'), // The plugin name.
			'slug'               => 'woocommerce', // The plugin slug (typically the folder name).
			'source'             => esc_url('https://wordpress.org/plugins/woocommerce/'), // The plugin source.
			'required'           => true, // If false, the plugin is only 'recommended' instead of required.
			'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
			'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
			'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			'external_url'       => '', // If set, overrides default API URL and points to an external URL.
			'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
		),
		array(
			'name'               => esc_html__('Mega Menu','fashionist'), // The plugin name.
			'slug'               => 'megamenu', // The plugin slug (typically the folder name).
			'source'             => esc_url('https://wordpress.org/plugins/megamenu/'), // The plugin source.
			'required'           => true, // If false, the plugin is only 'recommended' instead of required.
			'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
			'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
			'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			'external_url'       => '', // If set, overrides default API URL and points to an external URL.
			'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
		),
		array(
			'name'               => esc_html__('MailChimp Newsletter','fashionist'), // The plugin name.
			'slug'               => 'mailchimp-for-wp', // The plugin slug (typically the folder name).
			'source'             => esc_url('https://wordpress.org/plugins/mailchimp-for-wp/'), // The plugin source.
			'required'           => true, // If false, the plugin is only 'recommended' instead of required.
			'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
			'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
			'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			'external_url'       => '', // If set, overrides default API URL and points to an external URL.
			'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
		),
		array(
			'name'               => esc_html__('Advanced Custom Fields','fashionist'), // The plugin name.
			'slug'               => 'advanced-custom-fields-pro', // The plugin slug (typically the folder name).
			'source'             => esc_url('http://kl-webmedia.com/demo/wp/plugins/advanced-custom-fields-pro.zip'), // The plugin source.
			'required'           => true, // If false, the plugin is only 'recommended' instead of required.
			'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
			'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
			'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			'external_url'       => '', // If set, overrides default API URL and points to an external URL.
			'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
		),
		array(
			'name'               => esc_html__('Contact Form 7','fashionist'), // The plugin name.
			'slug'               => 'contact-form-7', // The plugin slug (typically the folder name).
			'source'             => esc_url('https://wordpress.org/plugins/contact-form-7/'), // The plugin source.
			'required'           => true, // If false, the plugin is only 'recommended' instead of required.
			'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
			'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
			'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			'external_url'       => '', // If set, overrides default API URL and points to an external URL.
			'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
		),
		array(
			'name'               => esc_html__('YITH WooCommerce Ajax Product Filter','fashionist'), // The plugin name.
			'slug'               => 'yith-woocommerce-ajax-navigation', // The plugin slug (typically the folder name).
			'source'             => esc_url('https://wordpress.org/plugins/yith-woocommerce-ajax-navigation/'), // The plugin source.
			'required'           => true, // If false, the plugin is only 'recommended' instead of required.
			'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
			'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
			'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			'external_url'       => '', // If set, overrides default API URL and points to an external URL.
			'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
		),
		array(
			'name'               => esc_html__('YITH WooCommerce Wishlist','fashionist'), // The plugin name.
			'slug'               => 'yith-woocommerce-wishlist', // The plugin slug (typically the folder name).
			'source'             => esc_url('https://wordpress.org/plugins/yith-woocommerce-wishlist/'), // The plugin source.
			'required'           => true, // If false, the plugin is only 'recommended' instead of required.
			'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
			'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
			'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			'external_url'       => '', // If set, overrides default API URL and points to an external URL.
			'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
		),
		array(
			'name'               => esc_html__('AccessPress Social Login Lite','fashionist'), // The plugin name.
			'slug'               => 'accesspress-social-login-lite', // The plugin slug (typically the folder name).
			'source'             => esc_url('https://wordpress.org/plugins/accesspress-social-login-lite/'), // The plugin source.
			'required'           => true, // If false, the plugin is only 'recommended' instead of required.
			'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
			'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
			'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			'external_url'       => '', // If set, overrides default API URL and points to an external URL.
			'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
		),


	);

	/*
	 * Array of configuration settings. Amend each line as needed.
	 *
	 * TGMPA will start providing localized text strings soon. If you already have translations of our standard
	 * strings available, please help us make TGMPA even better by giving us access to these translations or by
	 * sending in a pull-request with .po file(s) with the translations.
	 *
	 * Only uncomment the strings in the config array if you want to customize the strings.
	 */
	$config = array(
		'id'           => 'fashionist',                 // Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',                      // Default absolute path to bundled plugins.
		'menu'         => 'tgmpa-install-plugins', // Menu slug.
		'has_notices'  => true,                    // Show admin notices or not.
		'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => false,                   // Automatically activate plugins after installation or not.
		'message'      => '',                      // Message to output right before the plugins table.
	);


	tgmpa( $plugins, $config );
}

function fashionist_breadcrumb( array $options = array()  ) {
    global $post;
	$separator = ">"; // Simply change the separator to what ever you need e.g. / or >

	if (!is_front_page()) {
		echo '<a href="'. esc_url(home_url('/')).'">';
		esc_html_e('Home', 'fashionist');
		echo "</a>".$separator;
		if ( is_archive() ) {
		    echo "<a class='active'>".get_the_archive_title()."</a>";
		}
		elseif ( is_category() || is_single()  ) {
			echo "";the_category('>');echo "";
			if ( is_single() ) {
				echo $separator."<a class='active'>".get_the_title()."</a>";
			}
		} elseif ( is_page() && $post->post_parent ) {
			$home = get_page(get_option('page_on_front'));
			for ($i = count($post->ancestors)-1; $i >= 0; $i--) {
				if (($home->ID) != ($post->ancestors[$i])) {
					echo '<a href="';
					echo get_permalink($post->ancestors[$i]);
					echo '">';
					echo get_the_title($post->ancestors[$i]);
					echo "</a>".$separator;
				}
			}
			echo "<a class='active'>".get_the_title()."</a>";
		} elseif (is_page()) {
			echo "<a class='active'>".get_the_title()."</a>";
		} elseif (is_404()) {
			echo "<a class='active'>".esc_html__('404','fashionist')."</a>";
		} elseif (is_search()) {
			echo "<a class='active'>".esc_html__('Search Results for: ', 'fashionist' ), '<span>' . get_search_query() . '</span>'."</a>";
		}
	} else {
		echo "<a class='active'>".bloginfo('name')."</a>";
	}

}

function fashionist_pagination() {

global $wp_query;
$big = 999999999; // need an unlikely integer
$pages = paginate_links( array(
        'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
        'format' => '?paged=%#%',
        'current' => max( 1, get_query_var('paged') ),
        'total' => $wp_query->max_num_pages,
        'type'  => 'array',
        'prev_next' => false
    ) );

    if( is_array( $pages ) ) {
        $paged = ( get_query_var('paged') == 0 ) ? 1 : get_query_var('paged');
        echo '<div class="col-xs-12 pagination">';
            if($paged > 1){
                echo '<a href="'.get_previous_posts_page_link().'"><span class="icon-left"></span></a>';
            }else{
                echo '<a href="#"><span class="icon-left"></span></a>';
            }
            echo '<div class="pages">';
            $counter = 1;
            $ext = 0;
            foreach ( $pages as $page ) {
                echo wp_kses_post( $page );
            }
            echo '</div>';
            if($paged < $wp_query->max_num_pages){
                echo '<a href="'.get_next_posts_page_link().'" class="next_page"><span class="icon-right"></span></a>';
            }else{
                echo '<a href="#" class="next_page"><span class="icon-right"></span></a>';
            }
            echo '</div>';
    }
}

function fashionist_comment($comment, $args, $depth) {

    $tag       = 'li';
    $add_below = 'div-comment';
    ?>
    <<?php echo wp_kses_post( $tag ); ?> <?php comment_class( 'media' ) ?> id="comment-<?php comment_ID() ?>">

    <a class="pull-left hidden-xs hidden-sm" href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ); ?>">
    	<?php echo get_avatar( $comment,112,array('class'=>'media-object','force_default'=>true) ); ?>
	</a>
	<div class="media-body">
		<div class="author-info">
			<span class="author-name">
				<?php printf( esc_html__( '%s' ,'fashionist'), get_comment_author_link() ); ?>
			</span>
			<span class="date">
				<?php printf( esc_html__('%1$s at %2$s','fashionist'), get_comment_date(),  get_comment_time() ); ?>
			</span>
		</div>
		<div class="comment-text">
		<?php
		if ( $comment->comment_approved == '0' ) { ?>
	        <?php esc_html_e( 'Your comment is awaiting moderation.','fashionist'); ?>
	    <?php
		}
	    else{
	    	comment_text();
	    	?>
			<!-- <a href="#comment">REPLY</a> -->
			<?php comment_reply_link( array_merge( $args, array( 'add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
	    	<?php
		}
		?>
		</div>
	</div>
<?php
}


function fashionist_move_comment_field_to_bottom( $fields ) {
$comment_field = $fields['comment'];
unset( $fields['comment'] );
$fields['comment'] = $comment_field;
return $fields;
}
add_filter( 'comment_form_fields', 'fashionist_move_comment_field_to_bottom' );

function fashionist_woocommerce_breadcrumbs() {
    return array(
            'delimiter'   => '>',
            'wrap_before' => '',
            'wrap_after'  => '',
            'before'      => '<span class="woobreadc">',
            'after'       => '</span>',
            'home'        => _x( 'Home', 'breadcrumb', 'fashionist' ),
        );
}
add_filter( 'woocommerce_breadcrumb_defaults', 'fashionist_woocommerce_breadcrumbs' );

//Removing Tabs
add_filter( 'woocommerce_product_tabs', 'fashionist_woo_remove_product_tabs', 98 );
function fashionist_woo_remove_product_tabs( $tabs ) {
    unset( $tabs['additional_information'] );  	// Remove the additional information tab
    return $tabs;
}

// Rename the woocommerce tabs
add_filter( 'woocommerce_product_tabs', 'fashionist_woo_rename_tabs', 98 );
function fashionist_woo_rename_tabs( $tabs ) {

	$tabs['description']['title'] = esc_html__( 'Product Description' ,'fashionist');		// Rename the description tab
	$tabs['reviews']['title'] = esc_html__( 'Customer Reviews' ,'fashionist');				// Rename the reviews tab

	return $tabs;
}

// Reorder the woocommerce tabs
add_filter( 'woocommerce_product_tabs', 'fashionist_woo_reorder_tabs', 98 );
function fashionist_woo_reorder_tabs( $tabs ) {

	$tabs['description']['priority'] = 5;			// Description second
	$tabs['reviews']['priority'] = 10;			// Reviews first
	return $tabs;
}

// Add Custom Tab
add_filter( 'woocommerce_product_tabs', 'fashionist_woo_shipping_info_product_tab' );
function fashionist_woo_shipping_info_product_tab( $tabs ) {
	// Adds the new tab
	$tabs['test_tab'] = array(
		'title' 	=> esc_html__( 'Shipping information', 'fashionist' ),
		'priority' 	=> 50,
		'callback' 	=> 'fashionist_woo_shipping_info_product_tab_content'
	);
	return $tabs;

}
function fashionist_woo_shipping_info_product_tab_content() {
	// The new tab content
	echo '<p>';
	esc_html_e('Mauris eleifend est et turpis. Duis id erat. Suspendisse potenti. Aliquam vulputate, pede vel vehicula accumsan, mi neque rutrum erat, eu congue orci lorem eget lorem. Vestibulum non ante. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Fusce sodales. Quisque eu urna vel enim commodo pellentesque. Praesent eu risus hendrerit ligula tempus pretium. Curabitur lorem enim, pretium nec, feugiat nec, luctus a, lacus.

			Duis cursus. Maecenas ligula eros, blandit nec, pharetra at, semper at, magna. Nullam ac lacus. Nulla facilisi. Praesent viverra justo vitae neque. Praesent blandit adipiscing velit. Suspendisse potenti. Donec mattis, pede vel pharetra blandit, magna ligula faucibus eros, id euismod lacus dolor eget odio. Nam scelerisque. Donec non libero sed nulla mattis commodo. Ut sagittis. Donec nisi lectus, feugiat porttitor, tempor ac, tempor vitae, pede. Aenean vehicula velit eu tellus interdum rutrum. Maecenas commodo. Pellentesque nec elit. Fusce in lacus. Vivamus a libero vitae lectus hendrerit hendrerit.','fashionist');
	echo '</p>';
}

//Updating use meta after registration successful registration
add_action('woocommerce_created_customer','fashionist_adding_extra_reg_fields');

function fashionist_adding_extra_reg_fields($user_id) {
	extract($_POST);
	update_user_meta($user_id, 'first_name', $first_name);
	update_user_meta($user_id, 'last_name', $last_name);
}

/////////////////////////////////////////////////////////////////
function fashionist_nav_wrap( $items ) {
	$tdir = get_template_directory_uri();
	$loginurl = get_permalink( get_option('woocommerce_myaccount_page_id') );
    if( !$loginurl ){
        $$loginurl = wp_login_url();
    }

	if ( is_active_sidebar( 'languages-2' ) ) {

		ob_start();
		dynamic_sidebar('languages-2');
		$languages_2 = ob_get_contents();
		$languages_2 = str_replace('<div class="textwidget">', '', $languages_2);
		$languages_2 = str_replace('</ul></div>', '</ul>', $languages_2);
		ob_end_clean();

		$items .= 	'<li class="mega-menu-item mega-menu-item-type-custom mega-menu-item-object-custom mega-current-menu-ancestor mega-current-menu-parent mega-menu-item-has-children mega-align-bottom-left mega-menu-flyout dropdown">'.$languages_2.'</li>';

  	}

  	if ( is_active_sidebar( 'currency-2' ) ) {
  		ob_start();
		dynamic_sidebar('currency-2');
		$currency_2 = ob_get_contents();
		$currency_2 = str_replace('<div class="textwidget">', '', $currency_2);
		$currency_2 = str_replace('</ul></div>', '</ul>', $currency_2);
		ob_end_clean();

  		$items .= 	'<li class="mega-menu-item mega-menu-item-type-custom mega-menu-item-object-custom mega-current-menu-ancestor mega-current-menu-parent mega-menu-item-has-children mega-align-bottom-left mega-menu-flyout dropdown">'.$currency_2.'</li>';
  	}

  	$items .= 	'<li class="mega-menu-item mega-align-bottom-left mega-menu-flyout"><a href="'.$loginurl.'" class="mega-menu-link">Login</a></li>';

  	ob_start();
	fashionist_ajax_cart_new();
	$fashionist_ajax_cart_new = ob_get_clean();
  	$items .= 	'<li class="mega-menu-item mega-align-bottom-left mega-menu-flyout cart">'.$fashionist_ajax_cart_new.'</li>';


    return $items;
}

add_filter( 'woocommerce_ship_to_different_address_checked', '__return_true' );

add_action('wp_logout','fashionist_go_login_page');
function fashionist_go_login_page(){
	$loginurl = get_permalink( get_option('woocommerce_myaccount_page_id') );
    if( !$loginurl ){
        $$loginurl = wp_login_url();
    }
	wp_redirect( $loginurl );
	exit();
}

//remove you may also like products
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );

if( !function_exists( 'fashionist_excerpt' ) ) {
    function fashionist_excerpt($limit) {
     $excerpt = explode(' ', get_the_excerpt(), $limit);
        array_pop($excerpt);
        $excerpt = implode(" ",$excerpt).'...';
     $excerpt = preg_replace('`[[^]]*]`','',$excerpt);
     return $excerpt;
    }
}

/*----------------- <!-- Ajax Cart --> ---------------*/
add_filter('woocommerce_add_to_cart_fragments', 'fashionist_woocommerce_header_add_to_cart_fragment');
function fashionist_woocommerce_header_add_to_cart_fragment( $fragments ) {
  if(fashionist_checkPlugin('woocommerce/woocommerce.php') ){
      global $woocommerce;
      ob_start();
      ?>
      <span class="cart-count"><?php echo wp_kses_post( $woocommerce->cart->cart_contents_count ); ?></span>
      <?php
        $fragments['span.cart-count'] = ob_get_clean();
        return $fragments;
    }
}

if( !function_exists( 'fashionist_ajax_cart' ) ) {
    function fashionist_ajax_cart(){
        global $woocommerce;
        if(fashionist_checkPlugin('woocommerce/woocommerce.php') ):?>
            <span id="open-close-cart" onclick="open_close_dropdown('shopping-cart')"><span class="icon-shoppingcart"></span> (<?php $a = ''; $a = fashionist_woocommerce_header_add_to_cart_fragment($a);  echo wp_kses_post( $a['span.cart-count'] ); ?>)</span>

            <?php
                $a = ''; $a = fashionist_woocommerce_header_add_to_cart_fragment($a);
                if( $a['span.cart-count'] != "0" ) { ?>
                    <div id="shopping-cart" class="closed"><div class="cont"><span class="title">Shopping cart (<?php $a = ''; $a = fashionist_woocommerce_header_add_to_cart_fragment($a);  echo wp_kses_post( $a['span.cart-count'] ); ?>)</span><div class="widget_shopping_cart_content"></div></div></div>
                <?php } else { ?>
                    <div class="current-product-added-to-cart animated fadeInRight">no product</div>
            <?php } ?>

        <?php endif;
    }
}
add_action( 'fashionist_ajax_cart', 'fashionist_ajax_cart', 10 );

if (! function_exists('fashionist_pagination_woocommerce') && fashionist_checkPlugin('woocommerce/woocommerce.php') ) {

    function fashionist_pagination_woocommerce() {

    global $wp_query;
    $big = 999999999; // need an unlikely integer
    $pages = paginate_links( array(
            'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
            'format' => '?paged=%#%',
            'current' => max( 1, get_query_var('paged') ),
            'total' => $wp_query->max_num_pages,
            'type'  => 'array',
            'prev_next' => false
        ) );

        if( is_array( $pages ) ) {
            $paged = ( get_query_var('paged') == 0 ) ? 1 : get_query_var('paged');
            echo '<div class="col-xs-12 pagination">';
            if($paged > 1){
                echo '<a href="'.get_previous_posts_page_link().'"><span class="icon-left"></span></a>';
            }else{
                echo '<a href="#"><span class="icon-left"></span></a>';
            }
            echo '<div class="pages">';
            $counter = 1;
            $ext = 0;
            foreach ( $pages as $page ) {
                echo wp_kses_post( $page );
            }
            echo '</div>';
            if($paged < $wp_query->max_num_pages){
                echo '<a href="'.get_next_posts_page_link().'" class="next_page"><span class="icon-right"></span></a>';
            }else{
                echo '<a href="#" class="next_page"><span class="icon-right"></span></a>';
            }
            echo '</div>';
        }
    }
}
// Remove Classes from body class
add_filter( 'body_class', 'fashionist_adjust_body_class' );
function fashionist_adjust_body_class( $classes ) {

    foreach ( $classes as $key => $value ) {
        if ( $value == 'woocommerce' || $value == 'woocommerce-page') unset( $classes[ $key ] );
    }

    return $classes;

}

function fashionist_theme_add_editor_styles() {
    add_editor_style( 'css/custom.css' );
}
add_action( 'admin_init', 'fashionist_theme_add_editor_styles' );