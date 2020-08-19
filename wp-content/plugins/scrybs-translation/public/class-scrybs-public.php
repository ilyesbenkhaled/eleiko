<?php
/**
 * @link       https://scrybs.com/
 * @since      1.9.2
 *
 * @package    Scrybs
 * @subpackage Scrybs/public 
 * @author     Scrybs <info@scrybs.com>
 */
class Scrybs_Public {

	private $plugin_name;
	private $version;
	private $this_lang;
	private $wp_query;
	private $activate_lang_switcher;

	public function __construct( $plugin_name, $version ) {
		global $scrybs, $scrybs_settings;
		$this->plugin_name 		= $plugin_name;
		$this->version 			= $version;
		
		if(!empty($scrybs_settings)){
			$this->apikey 		  = $scrybs_settings[ 'api_key' ];
			$this->websitekey 	  = $scrybs_settings[ 'websitekey' ];
					
			$this->l_source 	  = $scrybs_settings[ 'source' ];
			$this->l_target 	  = $scrybs_settings[ 'languages' ];
			
			$this->auto_translate = $scrybs_settings[ 'automatic_translation' ];
			$this->browser_redirect = $scrybs_settings[ 'browser_redirect' ];
			
			// TRANSLATED URL LIST
			$url_list_file = SCRYBS_PLUGIN_PATH.'/res/urllist.json';
			if(!file_exists($url_list_file)){
				$fp = fopen($url_list_file,"w"); 
			    fwrite($fp,""); 
			    fclose($fp);
			}
			$json = file_get_contents($url_list_file);
			$url_list = json_decode($json,true);
			if(!empty($url_list)){
				$this->translated_urls = $url_list;
			}else{
				$this->translated_urls = array();
			}
			
			if( get_option( 'scrybs_url_exclusion' ) ) {
				$this->excluded_urls = explode(',', get_option('scrybs_url_exclusion'));
			} else {
				$this->excluded_urls = array();
			}
			
			// LANGUAGE SWITCHER
			if(isset($scrybs_settings[ 'language_names' ])){ $this->language_names = $scrybs_settings[ 'language_names' ]; }
			if(isset($scrybs_settings[ 'icons' ])){ $this->icons = $scrybs_settings[ 'icons' ]; }
			if(isset($scrybs_settings[ 'arrow_style' ])){ $this->arrow_style = $scrybs_settings[ 'arrow_style' ]; }
			if(isset($scrybs_settings[ 'flag_style' ])){ $this->flag_style = $scrybs_settings[ 'flag_style' ]; }
			if(isset($scrybs_settings[ 'is_dropdown' ])){ $this->is_dropdown = $scrybs_settings[ 'is_dropdown' ]; }

			$in_menu     = isset($scrybs_settings[ 'in_menu' ]) && $scrybs_settings[ 'in_menu' ] == 'yes' ? TRUE : FALSE;
			$sc_in_menu  = isset($scrybs_settings[ 'sc_in_menu' ]) && $scrybs_settings[ 'sc_in_menu' ] == 'yes' ? TRUE : FALSE;
			
			$activate_lang_switcher = isset($scrybs_settings[ 'activate_lang_switcher' ]) && $scrybs_settings[ 'activate_lang_switcher' ] == 'yes' ? TRUE : FALSE;
			
			$this->activate_lang_switcher = $activate_lang_switcher;			
			$this->in_menu = $activate_lang_switcher && $in_menu ? TRUE : FALSE;			
			$this->sc_in_menu = $activate_lang_switcher && $sc_in_menu ? TRUE : FALSE;

			if($this->is_dropdown=='yes'){
				$this->btnstyle = 'sc-drop';
			}else{
				$this->btnstyle = 'sc-list';
			}
			
			if(isset($scrybs_settings[ 'en_flag' ])){ $this->en_flag = $scrybs_settings[ 'en_flag' ]; }
			if(isset($scrybs_settings[ 'pt_flag' ])){ $this->pt_flag = $scrybs_settings[ 'pt_flag' ]; }
			if(isset($scrybs_settings[ 'de_flag' ])){ $this->de_flag = $scrybs_settings[ 'de_flag' ]; }
			if(isset($scrybs_settings[ 'fr_flag' ])){ $this->fr_flag = $scrybs_settings[ 'fr_flag' ]; }
			if(isset($scrybs_settings[ 'es_flag' ])){ $this->es_flag = $scrybs_settings[ 'es_flag' ]; }
			
			$this->translator 	= $this->apikey ? new \Scrybs\Scrybs_Client($this->apikey, $this->websitekey, $this->auto_translate, $this->translated_urls):null;
			
			add_action('template_redirect', array(&$this, 'add_headers'));
			add_shortcode('scrybs_switcher', array(&$this, 'scrybs_switcher_creator'));
			add_action('widgets_init', array(&$this, 'addWidget'));
			
			if($this->in_menu){
				add_filter( 'wp_nav_menu_items', array(&$this, 'scrybs_menu_item'), 12, 2 );
			}
			$this->home_dir = $this->getHomeDirectory();
			$this->request_uri = $this->getRequestUri($this->home_dir);
			$curr = $this->getLangFromUrl($this->request_uri);
			$this->currentlang = $curr ? $curr : $this->l_source;
			
			$curr_lang_chars = strlen($this->currentlang);
			$this->urlsanslang = substr($this->request_uri, $curr_lang_chars+1);

			if( $this->isTranslatedUrl($this->urlsanslang, $this->currentlang) ){
				$this->is_translated_url = true;
				$this->is_an_excluded_url = false;
			     add_filter('status_header', array(&$this, 'fire_translated_url_header'), 10, 2 );
			}else{
				if( $this->is_excluded_url($this->urlsanslang) ){
					$this->is_an_excluded_url = true;
				}else{
					$this->is_an_excluded_url = false;
				}
				$this->is_translated_url = false;
			}			
		}
	}

	public function scrybs_enqueue_styles() {
		wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/scrybs-public.css', array(), $this->version, 'all' );
	}

	public function scrybs_enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/scrybs-public.js', array( 'jquery' ), $this->version, false );
	}
			
	public function query_vars( $public_query_vars ) {
		global $wp_query;

		$public_query_vars[] = 'lang';
		$wp_query->query_vars['lang'] = $this->this_lang;

		return $public_query_vars;
	}
	
	public function scrybs_menu_item( $items, $args ) {
		$button = $this->returnSwitcherCode();
		$items .= $button;
		return $items;
	}
	
	public function fire_translated_url_header( $status_header, $header ) {
	    if ( (int) $header == 404 )
	        return status_header( 200 );
	    return $status_header;
	}
	
	public function is_excluded_url( $requesturl ) {
	    $excluded_urls = $this->excluded_urls;
	    $folders_to_exclude = array();
	    $urls_to_exclude = array();
	    if(!empty($excluded_urls)){
		    foreach($excluded_urls as $url){
		    	$url = trim($url);
			    if (strpos($url, '*') !== false) {
			    	$url = str_replace('*', '', $url);
				    $folders_to_exclude[] = $url;
				}else{
					$urls_to_exclude[] = $url;
				}
		    }
		    // URLS
		    if(in_array($requesturl, $urls_to_exclude)){
			    return true;
		    }
		    if (strpos($url, '?replytocom=') !== false) {
			    return true;
			}
		    // FOLDERS
		    $url_folders = explode('/', $requesturl);
		    foreach($url_folders as $url_folder){
		    	$foldr = '/'.$url_folder.'/';
				if($foldr!='//'){
					if(in_array($foldr, $folders_to_exclude)){
					    return true;
				    }
				}
			}
			$dirname = dirname($requesturl).'/';
			if(in_array($dirname, $folders_to_exclude)){
				return true;
			}
		    return false;
		 }else{
			return false;
		 }
	}
			
	public function maybe_set_this_lang() {
		$cookie_lang  = $this->get_cookie_lang();
		$current_lang = $this->currentlang;
		
		if(isset($cookie_lang) && $cookie_lang != ''){
		}else{ // NEW VISITOR
			if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
				$visitor_locale = locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']);
				$visitor_lang = substr($visitor_locale, 0, 2);
			}else{
				$visitor_lang = $this->l_source;
			}
			
			$dest = explode(",",$this->l_target);
			
			if($current_lang != $visitor_lang){
				if( in_array($visitor_lang, $dest) || ($visitor_lang == $this->l_source) ){
					$code_lang = $visitor_lang;
					$this->set_language_cookie( $code_lang );
									
					$newurl = $this->translateUrl( $this->request_uri, $current_lang, $visitor_lang );
					wp_safe_redirect( $newurl, 302 );exit;			
				}else{
					$this->set_language_cookie( $current_lang );
				}
			}else{
				$this->set_language_cookie( $current_lang );
			}					
		}		
	}
	
	public function translateUrl($url, $from, $to){			
		if($url == '/'){
			$translated_url = '/'.$to.'/';
		}else{
			$source_url = $this->isTranslatedUrl($url, $from);
			if($source_url === false){
				if (strpos('/'.$from.'/', $url) !== false) {
					$source_url = str_replace('/'.$from.'/', '/', $url);
				}else{
					$source_url = $url;
				}
			}
					
			if($to != $this->l_source){
				// CHECK IF SOURCE HAS A TRANSLATION IN REQUESTED LANG			
				$translated_url = $this->isSourceTranslatedURL($source_url, $to);
				if( $translated_url !== false && $translated_url != ''){
					
					$translated_url = '/'.$to.$translated_url;
						
				}else{ // NO TRANSLATION FOR REQUESTED LANG
					
					if (strpos('/'.$from.'/', $url) !== false) {
					    $translated_url = $this->replaceRequestUrl($url, $from, $to);
					}else{
						$translated_url = '/'.$to.$url;
					}
						
				}
			}else{
				$translated_url = $source_url;
			}
		}
		
		return $translated_url;
	}
	
	/* This function was forked from Weglot */
	public function addWidget() {
		return register_widget("ScrybsWidget");
	}
	
	public function update_language_cookie($language_code) {
		$cookie_name = $this->get_cookie_name();
		$_COOKIE[ $cookie_name ] = $language_code;
	}
	
	/* This function was forked from Weglot */
	public function getRequestUri($home_dir) {
		if($home_dir) {
			return str_replace(trim($home_dir,'/'),'',$this->full_url($_SERVER));
		}else {
			return $_SERVER['REQUEST_URI'];
		}
	}
	
	/* This function was forked from Weglot */
	public function full_url($s, $use_forwarded_host=false) {
	   return $this->url_origin($s, $use_forwarded_host) . $s['REQUEST_URI'];
	}
	
	/* This function was forked from Weglot */
	public function url_origin($s, $use_forwarded_host=false) {
		$ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true:false;
		$sp = strtolower($s['SERVER_PROTOCOL']);
		$protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
		$port = $s['SERVER_PORT'];
		$port = ((!$ssl && $port=='80') || ($ssl && $port=='443')) ? '' : ':'.$port;
		$host = ($use_forwarded_host && isset($s['HTTP_X_FORWARDED_HOST'])) ? $s['HTTP_X_FORWARDED_HOST'] : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : null);
		$host = isset($host) ? $host : $s['SERVER_NAME'] . $port;
		return $protocol . '://' . $host;
	}
	
	public function get_server_host_name() {
		$host = isset( $_SERVER[ 'HTTP_HOST' ] ) ? $_SERVER[ 'HTTP_HOST' ] : null;
		$host = $host !== null
			? $host
			: ( isset( $_SERVER[ 'SERVER_NAME' ] )
				? $_SERVER[ 'SERVER_NAME' ]
				  . ( isset( $_SERVER[ 'SERVER_PORT' ] ) && ! in_array( $_SERVER[ 'SERVER_PORT' ], array( 80, 443 ) )
					? $_SERVER[ 'SERVER_PORT' ] : '' )
				: '' );
		$result = preg_replace( "@:[443]+([/]?)@", '$1', $host );
		return $result;
	}
	
	/* This function was forked from Weglot */
	public function init_function() {
				
		if($this->browser_redirect == 'yes'){
			$this->maybe_set_this_lang();
		}
			
		$dest = explode(",",$this->l_target);
		
		$areRulesOK = true;
		$rules = get_option("rewrite_rules");
		if($rules) {
			foreach($dest as $d) {
				if(!array_key_exists($d.'/?$',$rules)) {
					$areRulesOK = false;
					break;
				}
			}
		}
	
		$nb_rules = count($rules); 
		$nb_lang = count($dest);
		if(!is_int(($nb_rules-$nb_lang)/(1+$nb_lang))) {
			$areRulesOK = false;
		}	

		if(!$areRulesOK) {
			$this->updateRewriteRule();
		}
				
		$request_uri =  $this->request_uri;
		foreach($dest as $d) {
			if($request_uri == '/'.$d.'/')
				$thisL = $d;
		}
        
        $ll = strlen($thisL)+1;
        $url = (isset($thisL) && $thisL!='') ? substr($request_uri, $ll) : $request_uri;
				
		if($url=="/" && (isset($thisL) && $thisL!='') && 'page' == get_option('show_on_front') ) {
			add_action('template_redirect', array(&$this, 'kill_canonical_wg_92103'), 1);	
		}
		
		if($this->currentlang != $this->l_source){
			if($this->is_an_excluded_url === true){	
				scrybs_force_404();
			}
		}
		ob_start(array(&$this,'treatPage'));
	}
	
	/* This function was forked from Weglot */       
    public function add_alternate() {

        if ( $this->l_target != '' ) {

            $dest = explode( ',',$this->l_target );

            $full_url = ($this->currentlang != $this->l_source) ? str_replace( '/' . $this->currentlang . '/','/',$this->full_url( $_SERVER ) ) : $this->full_url( $_SERVER );
            $output = '<link rel="alternate" hreflang="' . $this->l_source . '" href="' . $full_url . '" />' . "\n";
            foreach ( $dest as $code ) {
                $output .= '<link rel="alternate" hreflang="' . $code . '" href="' . $this->replaceUrl( $full_url,$code ) . '" />' . "\n";
            }

            echo wp_kses($output, array(
                'link' => array(
                    'rel' => array(),
                    'hreflang'=>array(),
                    'href'=>array())
            ));
        }
    }
	
	public function add_meta_generator() {
		if($this->l_target!="") {
			$output = '<meta name="generator" content="Scrybs '.$this->version.'" />'."\n";
			echo $output;
		}	
	}
	
	/* This function was forked from Weglot */
	public function getHomeDirectory() {
		$opt_siteurl = trim(get_option("siteurl"),'/');
		$opt_home = trim(get_option("home"),'/');
		if($opt_siteurl!="" && $opt_home!="") {
			if( (substr($opt_home,0,7) == "http://" && strpos(substr($opt_home,7),'/') !== false) ||  (substr($opt_home,0,8) == "https://" && strpos(substr($opt_home,8),'/') !== false) ) {
				return $opt_home;
			}
		}
		return null;
	}
		
	public function rr_404_my_event() {
		$request_uri = $this->request_uri;
		$url = 	($this->currentlang!=$this->l_source) ? substr($request_uri,3) :$request_uri;
		
		if ($this->currentlang!=$this->l_source) { 
			global $wp_query;
			$wp_query->set_404();
			status_header(404); 
		}
	}
	
	/* This function was forked from Weglot */
	public function kill_canonical_wg_92103() {
		add_action('redirect_canonical', '__return_false');  
	}
	
	/* This function was forked from Weglot */
	public function updateRewriteRule() {
		add_filter( 'rewrite_rules_array', array(&$this, 'insert_rewrite_rules') );
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}
	
	/* This function was forked from Weglot */
	public function insert_rewrite_rules($rules) {
		$newrules = array();
		if($this->l_target!="") {
			$dest = explode(",",$this->l_target);
			foreach($dest as $d) {
				foreach($rules as $k => $r) {
					$newrules[$d.'/'.$k] = $r;
				}
				$newrules[$d.'/?$'] = 'index.php';
			}		
		}
		return $newrules + $rules;
	}
		
	public function add_send_headers( $headers_arr, $options ) {
	    if ( empty($headers_arr) ) {
	        return;
	    }
	    // First check if headers have already been sent and, if yes, generate friendly warning.
	    if ( headers_sent() ) {
	        $warning_message = "Scrybs tried to append its headers to the headers list, but the header list had already been sent to the client by other code. This is not a problem caused by Scrybs plugin. Please investigate which plugin or theme sends the headers earlier than what is expected.";
	        trigger_error($warning_message, E_USER_WARNING);
	        return;
	    }
	    // Clean up pre-existing headers
	    if ( array_key_exists('remove_pre_existing_headers', $options) && $options['remove_pre_existing_headers'] === true ) {
	        $current_headers = headers_list();
	        $supported_headers = array('Last-Modified');
	        foreach ($current_headers as $current_header ) {
	            foreach ($supported_headers as $supported_header) {
	                if ( strpos($current_header, $supported_header) === false ) {
	                    header_remove($supported_header);
	                }
	            }
	        }
	    }
	    // Send the headers
	    foreach ( $headers_arr as $header_name => $header_value ) {
	        if ( ! empty($header_name) && ! empty($header_value) ) {
	            header( sprintf('%s: %s', $header_name, $header_value) );
	        }
	    }
	}
	
	public function add_generate_last_modified_header( $post, $mtime, $options ) {
	    if ( $options['add_last_modified_header'] === true ) {
	        $header_last_modified_value = str_replace( '+0000', 'GMT', gmdate('r', $mtime) );
	        return $header_last_modified_value;
	    }
	}
	
	public function add_batch_generate_headers( $post, $mtime, $options ) {
	    $headers_arr = array();
	
	    // Last-Modified
	    $headers_arr['Last-Modified'] = $this->add_generate_last_modified_header( $post, $mtime, $options );
	    $headers_arr = apply_filters( 'add_headers', $headers_arr );
	
	    // Send headers
	    $this->add_send_headers( $headers_arr, $options );
	}
	
	public function add_get_supported_post_types_singular() {
	    $supported_builtin_types = array_values(get_post_types( '', 'names' ));

	    $public_custom_types = get_post_types( array('public'=>true, '_builtin'=>false) );
	    $supported_types = array_merge($supported_builtin_types, $public_custom_types);
	
	    $supported_types = apply_filters( 'add_supported_post_types_singular', $supported_types );
	
	    return $supported_types;
	}
	
	public function add_get_supported_post_types_archive() {
	    $supported_builtin_types = array('post');
	    //$public_custom_types = get_post_types( array('public'=>true, '_builtin'=>false, 'show_ui'=>true) );
	    $public_custom_types = get_post_types( array('public'=>true, '_builtin'=>false) );
	    $supported_types = array_merge($supported_builtin_types, $public_custom_types);
	
	    // Allow filtering of the supported content types.
	    $supported_types = apply_filters( 'add_supported_post_types_archive', $supported_types );
	
	    return $supported_types;
	}
	
	public function add_set_headers_for_object( $options ) {
	
	    $post = get_queried_object();
	    if ( ! is_object($post) || ! isset($post->post_type) || ! in_array( get_post_type($post), $this->add_get_supported_post_types_singular() ) ) {
	        return;
	    }
	
	    if ( post_password_required() ) {
	        return;
	    }
	
	    $post_mtime = $post->post_modified_gmt;
	    $post_mtime_unix = strtotime( $post_mtime );
	
	    // Initially set the $mtime to the post mtime timestamp
	    $mtime = $post_mtime_unix;
	
	    if ( intval($post->comment_count) > 0 ) {
	
	        $comments = get_comments( array(
	            'status' => 'approve',
	            'orderby' => 'comment_date_gmt',
	            'number' => '1',
	            'post_id' => $post->ID
	        ) );
	        if ( ! empty($comments) ) {
	            $comment = $comments[0];
	            $comment_mtime = $comment->comment_date_gmt;
	            $comment_mtime_unix = strtotime( $comment_mtime );
	            if ( $comment_mtime_unix > $post_mtime_unix ) {
	                $mtime = $comment_mtime_unix;
	            }
	        }
	    }
	
	    $this->add_batch_generate_headers( $post, $mtime, $options );
	}
	
	public function add_set_headers_for_archive( $options ) {
	
	    // Get our post object from the list of posts.
	    global $posts;
	    if ( empty($posts) ) {
	        return;
	    }
	    $post = $posts[0];
	
	    // The post object we use for the HTTP headers is the latest post.
	    $post = apply_filters( 'add_archive_post', $post );
	
	    // Valid post types: post
	    if ( ! is_object($post) || ! isset($post->post_type) || ! in_array( get_post_type($post), $this->add_get_supported_post_types_archive() ) ) {
	        return;
	    }
	
	    // Retrieve stored time of post object
	    $post_mtime = $post->post_modified_gmt;
	    $mtime = strtotime( $post_mtime );
		
	    $this->add_batch_generate_headers( $post, $mtime, $options );
	}
	
	public function add_headers(){

	    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
	        return;
	    } elseif( defined('XMLRPC_REQUEST') && XMLRPC_REQUEST ) {
	        return;
	    } elseif( defined('REST_REQUEST') && REST_REQUEST ) {
	        return;
	    } elseif ( is_admin() ) {
	        return;
	    }
	
	    // Options
	    $default_options = array(
	        'add_last_modified_header' => true,
	        'remove_pre_existing_headers' => false,
	    );
	    $options = apply_filters( 'add_options', $default_options );
		
	    if ( is_feed() ) {
	        $this->add_set_headers_for_archive( $options );
	    }
	
	    elseif ( is_singular() ) {
	        $this->add_set_headers_for_object( $options );
	    }
	    
	    elseif ( is_archive() || is_search() || is_home() ) {
	        $this->add_set_headers_for_archive( $options );
	    }
	
	}
	
	public function get_last_modified_header($url)
	{ 
		$url = $this->home_dir.$url;
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL,            $url);
		curl_setopt($ch, CURLOPT_HEADER,         true);
		curl_setopt($ch, CURLOPT_NOBODY,         true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT,        10);
		 
	    $response = curl_exec($ch);
	    preg_match("/Last-Modified:(.*)GMT/", $response, $header); 
		return strtotime(trim($header[1]));
	}
		
	public function getHTML($url) {
		$response = wp_remote_get( $url, array(
			'method' => 'GET',
			'timeout' => 45,
			'redirection' => 5,
			'blocking' => true,
			'headers' => array( "Content-type" => "text/html" ),
			'body' => null,
			'cookies' => array(),
			'sslverify' => false
			)
		);
			if ( is_wp_error( $response ) ) {
				return;
			}
		$html = wp_remote_retrieve_body( $response );
			if ( is_wp_error( $html ) ) {
				return;
		}
		return $html;
	}
	
	public function is_cached($url)
	{
		$encoded = urlencode( base64_encode( $url ) );
		$fileurl = substr($encoded, 0, 20);
		if (file_exists(SCRYBS_CACHE_CONTENT_FOLDER.''.$fileurl.'.html')) {
		    return true;
		} else {
		    return false;
		}
	}
	
	public function get_last_modified_cache_file($url)
	{
		$encoded = urlencode( base64_encode( $url ) );
		$fileurl = substr($encoded, 0, 20);
		$mtime 	 = filemtime(SCRYBS_CACHE_CONTENT_FOLDER.''.$fileurl.'.html');
		return $mtime;
	}
	
	public function cache_url_content($url)
	{
		$opt_siteurl = get_option("siteurl");
		$opt_home = get_option("home");
		if($opt_siteurl != $opt_home){
			$homedir = $opt_home;
		}else{
			$homedir = $opt_siteurl;
		}
		
		$fullurl = $homedir.$url;
		
		$html = $this->getHTML($fullurl);
		$html = HTMLminifier($html);
		$html = preg_replace("/<aside(.*)id=\"scrybs_switcher\"(.*)aside>/", "", $html);
		$html = preg_replace("/<\/html>(.*)/s", "</html>", $html);
		if($this->in_menu)
		{
			$html = preg_replace("/(?s)<ul class=\"scrybs_languages[^>]*>.*?<\/ul>/", "", $html);
			$html = preg_replace('/<li class="sc-current[^>]*>.*?<\/li>/i', '<li id="scrybs_menu"></li>', $html); 
		}
		
		$encoded = urlencode( base64_encode( $url ) );
		$fileurl = substr($encoded, 0, 20);
		$filename = SCRYBS_CACHE_CONTENT_FOLDER.''.$fileurl.'.html';
		
		if(!empty($html)){
		    if(file_put_contents($filename, $html)){
			    return $html;
			} else {
			    return false;
			}
		} else {
		    return false;
		}	
	}
	
	public function get_cache_url_content($url)
	{
		$encoded = urlencode( base64_encode( $url ) );
		$fileurl = substr($encoded, 0, 20);
		$filename = SCRYBS_CACHE_CONTENT_FOLDER.''.$fileurl.'.html';
		$html = file_get_contents($filename);
		
		if($html != '') {
		    return $html;
		} else {
		    return false;
		}
	}

    /* This function was forked from Weglot */
    public function treatPage($final)
    {
        $request_uri = $this->request_uri;
        if (! is_admin() && strpos($request_uri, 'wc-ajax') === false && strpos($request_uri, 'wp-login') === false && $this->l_source != "" && $this->l_target != "") {
            if (is_HTML($final)) {
                if ($this->currentlang != $this->l_source) {
                    
                    $url = $this->urlsanslang;
                    
                    // CHECK IF URL IS A TRANSLATED URL, REDIRECT TO TRANSLATED URL IF YES
                    $is_source_url = $this->isSourceTranslatedURL($url, $this->currentlang);
                    if ($is_source_url !== false && $is_source_url != '') {
                        $translated_url = $this->home_dir . '/' . $this->currentlang . $is_source_url;
                        wp_safe_redirect($translated_url, 301);
                        exit();
                    }
                        
                        // 404 IF ITS A TRANSLATED URL SO CACHE THE SOURCE URL CONTENT
                    if ($this->is_translated_url !== false) {
                        $source_url = $this->isTranslatedUrl($url, $this->currentlang);
                        $url = $source_url;
                        if ($this->is_cached($url) === false) {
                            $html = $this->cache_url_content($url);
                        } else {
                            $last_modified_header = $this->get_last_modified_header($url);
                            $last_modified_cache_file = $this->get_last_modified_cache_file($url);
                            if ($last_modified_header < $last_modified_cache_file) {
                                $html = $this->get_cache_url_content($url);
                            } else {
                                $html = $this->cache_url_content($url);
                            }
                        }
                    } else {
                        if (is_404()) {
                            $url_list = $this->translator->getAndUpdateUrlList();
                            if ($url_list !== false) {
                                $target_url_list = $url_list[$this->currentlang];
                                if (array_key_exists($url, $target_url_list)) {
                                    $source_url = $target_url_list[$url];
                                    $url = $source_url;
                                    if ($this->is_cached($url) === false) {
                                        $html = $this->cache_url_content($url);
                                    } else {
                                        $last_modified_header = $this->get_last_modified_header($url);
                                        $last_modified_cache_file = $this->get_last_modified_cache_file($url);
                                        if ($last_modified_header < $last_modified_cache_file) {
                                            $html = $this->get_cache_url_content($url);
                                        } else {
                                            $html = $this->cache_url_content($url);
                                        }
                                    }
                                } else {
                                    global $wp_query;
                                    $wp_query->set_404();
                                    status_header(404);
                                }
                            } else {
                                global $wp_query;
                                $wp_query->set_404();
                                status_header(404);
                            }
                        } else {
                            $html = $final;
                        }
                    }
                    
                    if (! empty($html)) {
                        $final = $this->translatePageTo($html, $this->currentlang, $url);
                    } else {
                        global $wp_query;
                        $wp_query->set_404();
                        status_header(404);
                    }            
                    
                    /* language switcher is disabled */
                    if ( $this->activate_lang_switcher === FALSE )
                        return $final;


                    if ($this->sc_in_menu && $this->in_menu === FALSE ) {
                        if (strpos($final, '{{{scrybs_switcher}}}') !== false) {
                            $button = $this->returnSwitcherCode(TRUE);
                            $final = str_replace('{{{scrybs_switcher}}}', $button, $final);
                        } 
                    }

                    if ($this->in_menu && $this->sc_in_menu === FALSE ) {
                        if (strpos($final, '<li id="scrybs_menu"></li>') !== false) {
                            $button = $this->returnSwitcherCode();
                            $final = str_replace('<li id="scrybs_menu"></li>', $button, $final);
                        }
                    }  

                    if ($this->in_menu && $this->sc_in_menu ) {
                        if (strpos($final, '<li id="scrybs_menu"></li>') !== false) {
                            $button = $this->returnSwitcherCode();
                            $final = str_replace('<li id="scrybs_menu"></li>', $button, $final);
                        }
                    }        
                    
                    if ( $this->in_menu === FALSE && $this->sc_in_menu === FALSE ) {
                        if (strpos($final, '<div id="scrybs_here"></div>') !== false) {
                            
                            $button = '<aside id="scrybs_switcher" class="' . $this->btnstyle . ' country-selector closed" notranslate>';
                            $button .= $this->returnSwitcherCode();
                            $button .= '</aside>';
                            $final = str_lreplace('<div id="scrybs_here"></div>', $button, $final);
                        }
                        
                        if (strpos($final, 'id="scrybs_switcher"') === false) {
                            
                            $button = '<aside id="scrybs_switcher" class="' . $this->btnstyle . ' country-selector sc-default sc-openup closed" notranslate>';
                            $button .= $this->returnSwitcherCode();
                            $button .= '</aside>';
                            $final = (strpos($final, '</body>') !== false) ? str_lreplace('</body>', $button . ' </body>', $final) : str_lreplace('</footer>', $button . ' </footer>', $final);
                        }
                    }
                    return $final;
                } else {
                    
                    /* language switcher is disabled */
                    if ( $this->activate_lang_switcher === FALSE )
                        return $final;


                    if ($this->sc_in_menu && $this->in_menu === FALSE ) {
                        if (strpos($final, '{{{scrybs_switcher}}}') !== false) {
                            $button = $this->returnSwitcherCode(TRUE);
                            $final = preg_replace('/<li[^>].*>{{{scrybs_switcher}}}.*?<\/li>/i', $button, $final);
                        } 
                    }
                    
                    if ( $this->in_menu === FALSE && $this->sc_in_menu === FALSE ) {
                        if (strpos($final, '<div id="scrybs_here"></div>') !== false) {
                            
                            $button = '<aside id="scrybs_switcher" class="' . $this->btnstyle . ' country-selector closed" notranslate>';
                            $button .= $this->returnSwitcherCode();
                            $button .= '</aside>';
                            $final = str_lreplace('<div id="scrybs_here"></div>', $button, $final);
                        }
                        
                        if (strpos($final, 'id="scrybs_switcher"') === false) {
                            
                            $button = '<aside id="scrybs_switcher" class="' . $this->btnstyle . ' country-selector sc-default sc-openup closed" notranslate>';
                            $button .= $this->returnSwitcherCode();
                            $button .= '</aside>';
                            $final = (strpos($final, '</body>') !== false) ? str_lreplace('</body>', $button . ' </body>', $final) : str_lreplace('</footer>', $button . ' </footer>', $final);
                        }
                    }
                    return $final;
                }
            } else {
                return $final;
            }
        } elseif ((strpos($request_uri, 'admin-ajax.php') !== false || strpos($request_uri, '?wc-ajax') !== false) && $this->l_target != "" && $this->l_source != "" && strpos($_SERVER["HTTP_REFERER"], 'admin') === false) {
            
            $lang = $this->getLangFromUrl($this->URLToRelative($_SERVER["HTTP_REFERER"]));
            if (isset($thisL) && $thisL != '') {
                if ($final[0] == '{' || ($final[0] == '[' && $final[1] == '{')) {
                    $json = json_decode($final, true);
                    if (json_last_error() == JSON_ERROR_NONE) {
                        $jsonT = $this->translateArray($json, $lang, $_SERVER["HTTP_REFERER"]);
                        return json_encode($jsonT);
                    } else {
                        return $final;
                    }
                } elseif (is_AJAX_HTML($final)) {
                    return $this->translatePageTo($final, $lang, $_SERVER["HTTP_REFERER"]);
                } else {
                    return $final;
                }
            } else {
                return $final;
            }
        } else {
            return $final;
        }
    }
	
	/* This function was forked from Weglot */
	public function translatePageTo($html, $lang, $url) {
		$results = $this->translator->translateDomFromTo($html, $this->l_source, $lang, $url);
		
		if( $results['header'] == 'failed' )
		    return $html . nl2br( sprintf( "<!-- %s -->", $results['body'] ) );
		
		$translatedPage = $results['body'];

		// REPLACE LOCALE TAGS
		$locale = get_locale_code($lang);
		$translatedPage = preg_replace('/<html (.*?)?lang=(\"|\')(\S*)(\"|\')/','<html $1lang=$2'.$locale.'$4',$translatedPage);
		$translatedPage = preg_replace('/property="og:locale" content=(\"|\')(\S*)(\"|\')/','property="og:locale" content=$1'.$locale.'$3',$translatedPage);
		$translatedPage = preg_replace('/<abbr(.*)>(.*)<\/abbr>/', ' $0 ', $translatedPage);
				
		return $translatedPage;
	}
	
	/* This function was forked from Weglot */
	function translateArray($array,$to,$url) {
		foreach($array as $key=>$val) {
			if(is_array($val)) {
				$array[$key] = $this->translateArray($val,$to,$url);
			}
			else {
				if(is_AJAX_HTML($val)) {
					$array[$key] = $this->translatePageTo($val,$to,$url);
				}
			}
		}
		return $array;
	}
	
	/* This function was forked from Weglot */
	public function URLToRelative($url) {
		
		if ((substr($url, 0, 7) == 'http://') || (substr($url, 0, 8) == 'https://')) {
			$parsed = parse_url($url);
			$path     = isset($parsed['path']) ? $parsed['path'] : ''; 
			$query    = isset($parsed['query']) ? '?' . $parsed['query'] : ''; 
			$fragment = isset($parsed['fragment']) ? '#' . $parsed['fragment'] : ''; 
			
			$home_dir = $this->home_dir;
			if($home_dir) {
				$relative = str_replace(trim($home_dir ,'/'),'',$url);
				return ($relative=="") ? '/':$relative;
			}
			else {
				return $path.$query.$fragment;
			}
		}
		return $url;
	}
	
	public function isTranslatedUrl($url, $target){
		$url_list = $this->translated_urls;
		if(array_key_exists($target, $url_list)){
			$lang_urls = $url_list[$target];
			if(array_key_exists($url, $lang_urls)){
				$source_url = $lang_urls[$url];
				return $source_url;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	public function isSourceTranslatedURL($url, $target) {
		$url_list = $this->translated_urls;
		if(array_key_exists($target, $url_list)){
			$lang_urls = $url_list[$target];
			$translated_url = array_search($url, $lang_urls);
			if($translated_url !== false){
				return $translated_url;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
		
	public function defineSwitcherUrls($source_url){
		$url_list = $this->translated_urls;
		
		foreach($url_list as $lang => $value){
			foreach($value as $translated_url => $sourceurl){
				if($sourceurl == $source_url){
					$urls[$lang] = $translated_url;
				}
			}
		}
		if(isset($urls)){
			return $urls;
		}else{
			return false;
		}
	}
	
	/**
	 * ShortCode
	 */
	public function scrybs_switcher_creator() {
		$button = $this->returnSwitcherCode();
		echo $button;
	}
	
	public function returnSwitcherCode( $sc_in_menu = FALSE ){
		$dest = explode(",",$this->l_target);
				
		$icons = $this->icons;
		if(isset($icons)){
			$icons = ' sc-'.$icons;
		}else{
			$icons = '';
		}
		
		$flstyle = $this->flag_style;
		if(isset($flstyle)){
			$flstyle = ' '.$flstyle;
		}else{
			$flstyle = '';
		}
		
		$arrowstyle = $this->arrow_style;
		if(isset($arrowstyle)){
			$arrowstyle = ' '.$arrowstyle;
		}else{
			$arrowstyle = '';
		}
		
		if(isset($this->language_names)){ $l_names = $this->language_names; }
		
		// SWITCHER URL
		$request_uri = $this->request_uri;
		if( $this->currentlang != $this->l_source ){
			$curr_lang_chars = strlen($this->currentlang);
			$url = substr($request_uri, $curr_lang_chars+1);
		}else{
			$url = $request_uri;
		}
					
		$source_url = $this->isTranslatedUrl($url, $this->currentlang);
		if($source_url !== false){
			$url = $source_url;
		}
				
		$current_code = $this->currentlang;
		if($l_names == 'full_names'){
			$current_name = get_language_name($current_code, $current_code);
		}else if($l_names == 'code_names'){
			$current_name = strtoupper($current_code);
		}else{
			$current_name ='';
		}
		
		$source_code = $this->l_source;
		if($l_names == 'full_names'){
			$source_name = get_language_name($source_code, $source_code);
		}else if($l_names == 'code_names'){
			$source_name = strtoupper($source_code);
		}else{
			$source_name ='';
		}
				
		$ul_lang = sprintf( '<ul class="scrybs_languages %s%s">', $current_code, apply_filters( 'scrybs_languages_submenu_css', '' ) ); //@TODO: Check if this makes sense in CSS
		if($current_code != $source_code){
			$href = $this->home_dir.$url;
			$ul_lang .= '<li class="sc-li '.$this->get_flag($source_code).$icons.$flstyle.'" notranslate><a href="'.$href.'">'.$source_name.'</a></li>';
		}
		
		if ( is_home() || $this->is_excluded_url($url) ){
			foreach($dest as $code) {
				if($code != $current_code && $code != $source_code){
					$pageurl = '/'.$code.'/';
					if($l_names == 'full_names'){
						$lname = get_language_name($code, $code);
					}else if($l_names == 'code_names'){
						$lname = strtoupper($code);
					}else{
						$lname ='';
					}
					$href = $this->home_dir.$pageurl;
					$ul_lang .= '<li class="sc-li '.$this->get_flag($code).$icons.$flstyle.'" notranslate><a href="'.$href.'" notranslate>'.$lname.'</a></li>';
				}
			}
		}else{
			foreach($dest as $code) {
				if($code != $current_code && $code != $source_code){
						$pageurl = $this->translateUrl( $url, $current_code, $code );
						if($l_names == 'full_names'){
							$lname = get_language_name($code, $code);
						}else if($l_names == 'code_names'){
							$lname = strtoupper($code);
						}else{
							$lname ='';
						}
						$href = $this->home_dir.$pageurl;
						$ul_lang .= '<li class="sc-li '.$this->get_flag($code).$icons.$flstyle.'" notranslate><a href="'.$href.'" notranslate>'.$lname.'</a></li>';
				}
			}
		}
		$ul_lang .= '</ul>';
		
		$scrybs_languages_mainmenu_css = sprintf( "sc-current%s", apply_filters('scrybs_languages_mainmenu_css', '') );
		
		if( $this->in_menu === FALSE && $sc_in_menu === FALSE ) {
			$button = '<div class="sc-current sc-li '.$this->get_flag($current_code).$icons.$flstyle.$arrowstyle.'"><a href="#" onclick="return false;">'.$current_name.'</a></div>';
			$button .= $ul_lang;
		}else{
			$button = sprintf( "<li class=\"%s sc-li %s\"><a href=\"#\" onclick=\"return false;\"%s>%s</a>%s</li>", $scrybs_languages_mainmenu_css, $this->get_flag($current_code).$icons.$flstyle.$arrowstyle, ' notranslate', $current_name, $ul_lang ); 
		}
		return $button;
	}
	
	public function get_flag($code){
		if(isset($this->en_flag)){ $en_flag = $this->en_flag; }
		if(isset($this->pt_flag)){ $pt_flag = $this->pt_flag; }
		if(isset($this->de_flag)){ $de_flag = $this->de_flag; }
		if(isset($this->fr_flag)){ $fr_flag = $this->fr_flag; }
		if(isset($this->es_flag)){ $es_flag = $this->es_flag; }
			
		$flcode = '';
		switch($code) {
		 case 'en':
		 	if(isset($en_flag)){
			 	$flcode = $en_flag;
		 	}else{
		 		$flcode = 'gb';
		 	}
		 break;
		 case 'es':
		 	if(isset($es_flag)){
			 	$flcode = $es_flag;
		 	}else{
		 		$flcode = 'es';
		 	}
		 break;
		 case 'fr':
		 	if(isset($fr_flag)){
			 	$flcode = $fr_flag;
		 	}else{
		 		$flcode = 'fr';
		 	}
		 break;
		 case 'pt':
		 	if(isset($pt_flag)){
			 	$flcode = $pt_flag;
		 	}else{
		 		$flcode = 'br';
		 	}
		 break;
		 case 'de':
		 	if(isset($de_flag)){
			 	$flcode = $de_flag;
		 	}else{
		 		$flcode = 'de';
		 	}
		 break;
		 case 'sq':
		 	$flcode = 'al';
		 break;
		 case 'da':
		 	$flcode = 'dk';
		 break;
		 case 'ms':
		 	$flcode = 'my';
		 break;
		 case 'vi':
		 	$flcode = 'vn';
		 break;
		 case 'sr':
		 	$flcode = 'rs';
		 break;
		 case 'fa':
		 	$flcode = 'ir';
		 break;
		 case 'bs':
		 	$flcode = 'ba';
		 break;
		 case 'eu':
		 	$flcode = 'iq';
		 break;
		 case 'sl':
		 	$flcode = 'si';
		 break;
		 case 'zu':
		 	$flcode = 'za';
		 break;
		 case 'el':
		 	$flcode = 'gr';
		 break;
		 case 'hy':
		 	$flcode = 'am';
		 break;
		 case 'hi':
		 	$flcode = 'in';
		 break;
		 case 'et':
		 	$flcode = 'ee';
		 break;
		 case 'sv':
		 	$flcode = 'se';
		 break;
		 case 'uk':
		 	$flcode = 'ua';
		 break;
		 case 'ur':
		 	$flcode = 'pk';
		 break;
		 case 'ko':
		 	$flcode = 'kr';
		 break;
		 case 'ne':
		 	$flcode = 'np';
		 break;
		 case 'ta':
		 	$flcode = 'lk';
		 break;
		 case 'ja':
		 	$flcode = 'jp';
		 break;
		 case 'ga':
		 	$flcode = 'ie';
		 break;
		 case 'af':
		 	$flcode = 'za';
		 break;
		 case 'ar':
		 	$flcode = 'sa';
		 break;
		 case 'zh-CN':
		 	$flcode = 'cn';
		 break;
		 case 'zh-TW':
		 	$flcode = 'tw';
		 break;
		 case 'pt-pt':
		 	$flcode = 'pt';
		 break;
		 case 'pt-br':
		 	$flcode = 'br';
		 break;
		}
		if($flcode == ''){
			return $code;
		}else{
			return $flcode;
		}
	}
	
	/* This function was forked from Weglot */
	public function replaceUrl($url, $l) {
		$home_dir = $this->home_dir;
		if($home_dir) {
			$parsed_url = parse_url($url);
			$scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : ''; 
			$host     = isset($parsed_url['host']) ? $parsed_url['host'] : ''; 
			$port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : ''; 
			$user     = isset($parsed_url['user']) ? $parsed_url['user'] : ''; 
			$pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : ''; 
			$pass     = ($user || $pass) ? "$pass@" : ''; 
			$path     = isset($parsed_url['path']) ? $parsed_url['path'] : '/'; 
			$query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : ''; 
			$fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : ''; 

			if($l=='') {
				return trim($home_dir, '/')."$l".$this->URLToRelative($url);
			}
			else {
				return trim($home_dir, '/')."/$l".$this->URLToRelative($url);
			}
			
		}else {
			$parsed_url = parse_url($url);
			$scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : ''; 
			$host     = isset($parsed_url['host']) ? $parsed_url['host'] : ''; 
			$port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : ''; 
			$user     = isset($parsed_url['user']) ? $parsed_url['user'] : ''; 
			$pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : ''; 
			$pass     = ($user || $pass) ? "$pass@" : ''; 
			$path     = isset($parsed_url['path']) ? $parsed_url['path'] : '/'; 
			$query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : ''; 
			$fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : ''; 
			if($l=='') { 
				return $url;
			}
			else {
				return (strlen($path)>2 && substr($path,0,4)=="/$l/") ? "$scheme$user$pass$host$port$path$query$fragment":"$scheme$user$pass$host$port/$l$path$query$fragment";
			}
		}
	}
	
	/* This function was forked from Weglot */
	public function replaceRequestUrl($url, $from, $to) {
		$url = str_replace('/'.$from.'/', '/'.$to.'/', $url);
		return $url;
	}
	
	/* This function was forked from Weglot */
	public function getLangFromUrl($request_uri) {
		$l= null;
		$dest = explode(",",$this->l_target);
		foreach($dest as $d) {
			$ch = strlen($d)+2;
			if(substr($request_uri,0,$ch) == '/'.$d.'/'){
				$l = $d;
			}
		}
		return $l;
	}
		
	/**
	 * COOKIE RELATED FUNCTIONS
	 */
	public function set_language_cookie( $lang_code ) {
		$cookie_name = $this->get_cookie_name();
		if ( ! $this->headers_sent() ) {
			if ( preg_match( '@\.(css|js|png|jpg|gif|jpeg|bmp)@i',
					basename( preg_replace( '@\?.*$@', '', $_SERVER['REQUEST_URI'] ) ) )
			     || isset( $_POST['icl_ajx_action'] ) || isset( $_POST['_ajax_nonce'] ) || defined( 'DOING_AJAX' )
			) {
				return;
			}

			$cookie_domain = $this->get_cookie_domain();
			$cookie_path   = defined( 'COOKIEPATH' ) ? COOKIEPATH : '/';
			$this->set_cookie( $cookie_name, $lang_code, time() + 86400, $cookie_path, $cookie_domain );
		}
		$_COOKIE[ $cookie_name ] = $lang_code;
	}
	
	public function set_cookie( $name, $value, $expires, $path, $domain ) {
		setcookie( $name, $value, $expires, $path, $domain );
	}

	public function get_cookie( $name ) {
		if ( isset( $_COOKIE[ $name ] ) ) {
			return $_COOKIE[ $name ];
		} else {
			return '';
		}
	}

	public function headers_sent() {

		return headers_sent();
	}
	
	public function get_cookie_domain() {

		return defined( 'COOKIE_DOMAIN' ) ? COOKIE_DOMAIN : $this->get_server_host_name();
	}
	
	public function get_cookie_lang() {
		$cookie_name  = $this->get_cookie_name();
		$cookie_value = $this->get_cookie( $cookie_name );
		$lang         = $cookie_value ? substr( $cookie_value, 0, 10 ) : "";

		return $lang;
	}
	 	 
	protected function get_cookie_name() {
		return 'scrybs_language';
	}
}