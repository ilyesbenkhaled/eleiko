<?php
/**
 * Get a SCRYBS setting value
 * @param mixed|false $default     Required. The value to return if the settings key does not exist.
 * @param string      $key         The settings name key to return the value of
 *
 * @return mixed The value of the requested setting, or $default
 */
function scrybs_get_setting_filter( $default, $key) {
    return scrybs_get_setting($key, $default);
}

/**
 * @param string      $key
 * @param mixed|false $default
 *
 * @return bool|mixed
 */
function scrybs_get_setting( $key, $default = false ) {
    global $scrybs_settings;
    $scrybs_settings = isset($scrybs_settings) ? $scrybs_settings : get_option('scrybs');

    return isset( $scrybs_settings[ $key ] ) ? $scrybs_settings[ $key ] : $default;
}

function _is_curl() {
	if  (in_array  ('curl', get_loaded_extensions())) {
		return true;
	}
	else {
		return false;
	}
}

/* This function was forked from Weglot */
function doRequest($url, $parameters) {
	
		if(!empty($parameters)) {
			$payload = json_encode($parameters);
			if(json_last_error() == JSON_ERROR_NONE) {
				$response = wp_remote_post( $url, array(
					'method' => 'POST',
					'timeout' => 45,
					'redirection' => 5,
					'blocking' => true,
					'headers' => array( "Content-type" => "application/json" ),
					'body' => $payload,
					'cookies' => array(),
					'sslverify' => false
					)
				);
			}else{
				return 'Json error: '.json_last_error();
			}
			
		}else {
			$response = wp_remote_get( $url, array(
				'method' => 'GET',
				'timeout' => 45,
				'redirection' => 5,
				'blocking' => true,
				'headers' => array( "Content-type" => "application/json" ),
				'body' => null,
				'cookies' => array(),
				'sslverify' => false
				)
			);
		}

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
		} else {
			return $response['body'];
		}
}

/* This function was forked from Weglot */
function isLanguageRTL($code){
	$rtls = array("ar","he","fa");
	if(in_array($code, $rtls)) {
		return true;
	}
	return false;
}

function get_locale_code($code){
	global $wpdb;
	$table_name = $wpdb->prefix . 'scrybs_languages';	
	$locale = $wpdb->get_var( "SELECT default_locale FROM ".$table_name." WHERE code ='".$code."'" );
	if(!is_null($locale)){
		return $locale;
	}else{
		return $code;
	}
}

/* This function was forked from Weglot */
function str_lreplace($search, $replace, $subject) {
	$pos = strrpos($subject, $search);
		
	if($pos !== false)
	{
		$subject = substr_replace($subject, $replace, $pos, strlen($search));
	}
	return $subject;
}

function get_language_name(  $lang_code, $display_code = null ) {
	global $wpdb;
	if(!isset($display_code)){
		$display_code = 'en';
	}
	$query  = $wpdb->prepare(
						"SELECT name
	                    FROM {$wpdb->prefix}scrybs_languages_translations
	                    WHERE language_code=%s
	                    AND display_language_code=%s",
						$lang_code,
						$display_code
	);
	$name = $wpdb->get_var( $query );

	return $name;
}

/* This function was forked from Weglot */
function is_HTML($string) {
	return ((preg_match("/<head/",$string,$m) != 0) && !(preg_match("/<xsl/",$string,$m) != 0));
}

/* This function was forked from Weglot */	
function is_AJAX_HTML($string) {
	$r = preg_match_all("/<(a|div|span|p|i|aside|input|textarea|select|h1|h2|h3|h4|meta|button|form)/",$string,$m,PREG_PATTERN_ORDER);
	if($string[0]!='{' && $r && $r>=3)
		return true;
	else
		return false;
}

function scrybs_endsWith($haystack, $needle) {
	return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}

function HTMLminifier($buffer) {
	    $search = array(
	        '/\>[^\S ]+/s',
	        '/[^\S ]+\</s',
	        '/(\s)+/s'
	    );
	    $replace = array(
	        '>',
	        '<',
	        '\\1'
	    );
	    $buffer = preg_replace($search, $replace, $buffer);
	    return $buffer;
}

function is_internal_link($url) {
  $request_host = $_SERVER['HTTP_HOST'];
  if(stristr($url, $request_host) || strpos($url,"/") == '0')
    return true;
  else
    return false;
}

function emptyDir($dir) {
	$handle=opendir($dir);	
	while (($file = readdir($handle))!==false) {	
		@unlink($dir.'/'.$file);
	}
	closedir($handle);
	return true;
}

function scrybs_force_404() {
    global $wp_query;
    status_header( 404 );
    nocache_headers();
    include( get_query_template( '404' ) );
    die();
}