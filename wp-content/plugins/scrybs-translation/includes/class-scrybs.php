<?php
/**
 * @link       https://scrybs.com/
 * @since      1.9.2
 *
 * @package    Scrybs
 * @subpackage Scrybs/includes 
 * @author     Scrybs <info@scrybs.com>
 */
class Scrybs {

	protected $loader;

	protected $plugin_name;

	protected $version;
	
	private $wpdb;
	private $settings;
	private $active_languages = array();

	public function __construct() {
		global $scrybs, $scrybs_settings, $wpdb;
		
		$this->plugin_name = 'scrybs';
		$this->version = SCRYBS_VERSION;
		$this->scrybs = $scrybs;
		$this->wpdb = $wpdb;
				
		$scrybs_settings = get_option('scrybs');
		if(!empty($scrybs_settings)){
		$this->settings = &$scrybs_settings;
		}
		
		if(!empty($scrybs_settings)){
			$this->apikey 		= $scrybs_settings[ 'api_key' ];
			$this->websitekey 	= $scrybs_settings[ 'websitekey' ];
					
			$this->l_source 	= $scrybs_settings[ 'source' ];
			$this->l_target 	= $scrybs_settings[ 'languages' ];
		}

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	private function load_dependencies() {
	
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-scrybs-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-scrybs-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-scrybs-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-scrybs-public.php';

		$this->loader = new Scrybs_Loader();

	}

	private function set_locale() {
		$plugin_i18n = new Scrybs_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	private function define_admin_hooks() {

		$plugin_admin = new Scrybs_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'scrybs_enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'scrybs_enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_scrybs_admin_menu' );
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_name . '.php' );
		$this->loader->add_filter( 'plugin_action_links_' . $plugin_basename, $plugin_admin, 'add_action_links' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'scrybs_options_update' );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'scrybs_admin_notices_action' );
		$this->loader->add_filter( 'comment_post_redirect', $plugin_admin, 'wpse_58613_comment_redirect' );
		$this->loader->add_action( 'admin_footer', $plugin_admin, 'scrybs_js_actions' );
		$this->loader->add_action( 'wp_ajax_scrybs_action', $plugin_admin, 'scrybs_action');
	}

	private function define_public_hooks() {

		$plugin_public = new Scrybs_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'scrybs_enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'scrybs_enqueue_scripts' );
		
		if(!empty($this->apikey)){
			$this->loader->add_action('wp_loaded', $plugin_public, 'init_function',11); // changed: init
			$this->loader->add_action('wp_head', $plugin_public, 'add_meta_generator');
			$this->loader->add_action('wp_head', $plugin_public, 'add_alternate');
		}
	}
	
	public function run() {
		$this->loader->run();
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_loader() {
		return $this->loader;
	}
	
	public function get_settings() {
		return $this->settings;
	}
	
	public function filter_get_setting($value, $key) {
		return $this->get_setting($key, $value);
	}
	
	public function get_setting( $key, $default = false ) {
		return scrybs_get_setting_filter( $default, $key );
	}
		
	public function get_available_languages( $display_code = null ) {
		$languages = array('af', 'sq', 'ar', 'hy', 'eu', 'be', 'bs', 'bg', 'ca', 'zh-CN', 'zh-TW', 'hr', 'cs', 'da', 'nl', 'en', 'eo', 'et', 'fi', 'fr', 'ka', 'de', 'el', 'ht', 'hi', 'hu', 'is', 'id', 'ga', 'it', 'ja', 'kk', 'ko', 'lv', 'lt', 'mk', 'ms', 'mt', 'mn', 'ne', 'no', 'fa', 'pl', 'pt-pt', 'pt-br', 'ro', 'ru', 'sr', 'sk', 'sl', 'so', 'es', 'sw', 'sv', 'ta', 'th', 'tr', 'uk', 'ur', 'uz', 'vi', 'cy', 'zu');
		
		$langs = array();
		foreach($languages as $langcode){
			if($display_code){
				$name = $this->get_display_language_name( $langcode, $display_code );
			}else{
				$name = $this->get_display_language_name( $langcode, $langcode );
			}
			if($name != ''){
				$langs[$langcode] = $name;
			}
		}	
        return $langs;
    }
    
    public function detect_wp_current_language(){
    	$locale = get_locale();
    	if(!isset($locale)){
	    	$locale = 'en_US';
    	}
    	$query  = $this->wpdb->prepare(
						"SELECT code
	                     FROM {$this->wpdb->prefix}scrybs_languages
	                     WHERE default_locale=%s;",
						$locale
		);
		$code = $this->wpdb->get_var( $query );	
	    return $code;
    }
		
	public function get_source_language( $display_code = null ) {
		$sourcelanguage = array();
		if(isset( $this->settings[ 'source' ] )){
			$sourcelanguage['code'] = $this->settings['source'];
		}else{
			return false;
		}
		$sourcelanguage['display_name'] = $this->get_display_language_name( $this->settings['source'], $display_code );
		return $sourcelanguage;
	}
	
	public function get_active_languages( $display_code = null ) {
		$languages = explode(',', $this->settings['languages']);
			
		$langs = array();
		foreach($languages as $langcode){
			$langs[$langcode] = $this->get_display_language_name( $langcode, $display_code );
		}	
	    return $langs;
    }
	
	public function get_display_language_name(  $lang_code, $display_code = null ) {
		if(!isset($display_code)){
			$display_code = 'en';
		}
		$query  = $this->wpdb->prepare(
						"  SELECT name
	                       FROM {$this->wpdb->prefix}scrybs_languages_translations
	                       WHERE language_code=%s
	                        AND display_language_code=%s;",
						$lang_code,
						$display_code
		);
		$name   = $this->wpdb->get_var( $query );

		return $name;
	}

	public function get_version() {
		return $this->version;
	}
		
}
