<?php
ob_start();
/**
 * @link       https://scrybs.com/
 * @since      1.9.2
 *
 * @package    Scrybs
 * @subpackage Scrybs/admin  
 * @author     Scrybs <info@scrybs.com>
 */
class Scrybs_Admin {

	private $plugin_name;
	private $version;
		
	public function __construct( $plugin_name, $version ) {
		global $scrybs, $wpdb, $locale;
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->locale = $locale;
						
		$apikey = scrybs_get_setting( 'api_key' );
		if(!empty($apikey)){
			$this->apikey = $apikey;
		}
		$websitekey = scrybs_get_setting( 'websitekey' );
		if(!empty($websitekey)){
			$this->websitekey = $websitekey;
		}
		$l_source = scrybs_get_setting( 'source' );
		if(!empty($l_source)){
			$this->l_source = $l_source;
		}
		
		$l_target = scrybs_get_setting( 'languages' );
		if(!empty($l_target)){
			$this->l_target = $l_target;
		}
		
		$max_languages = scrybs_get_setting( 'maxlanguages' );
		if(!empty($max_languages)){
			$this->maxlanguages = $max_languages;
		}else{
			$this->maxlanguages = 1;
		}
	}
	
	public function scrybs_enqueue_styles() {
		wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/scrybs-admin.css', array(), $this->version, 'all' );
	}

	public function scrybs_enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/scrybs-admin.js', array( 'jquery' ), $this->version, false );
	}
			 
	public function add_scrybs_admin_menu() {

	    add_menu_page('Scrybs Translations', 'Translations', 'administrator', 'Scrybs', array($this, 'display_plugin_setup_page'),  SCRYBS_PLUGIN_DIRURL . '/res/scrybs.png');

	}
		
	public function add_action_links( $links ) {

	   $settings_link = array(
	    '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_name ) . '">' . __('Settings', $this->plugin_name) . '</a>',
	   );
	   return array_merge(  $settings_link, $links );
	
	}
	
	public function scrybs_js_actions() { ?>
		<script type="text/javascript" >
		jQuery(document).ready(function($) {
			var maxlanguages = <?php echo $this->maxlanguages; ?>;
			 $('.tlang input[type="checkbox"]').bind('change', function(e){
			 	if($('.tlang :checked').length > maxlanguages) {
			 		this.checked = false;
			 		alert("Your current Scrybs plan doesn't allow you to add more than "+maxlanguages+" language(s).");
			 	}
			});
			jQuery('#empty_cache').prop('disabled', false);
			jQuery('#empty_cache').click(function() {
				jQuery(this).append(' <i class="fa fa-spinner fa-spin"></i>');
				var data = {
					'action': 'scrybs_action',
					'todo': 'empty_cache'
				};
				var btn = jQuery(this);
				jQuery.post(ajaxurl, data, function(response) {
					if(response == '1'){
						btn.find('i').remove();
						btn.append(' <i class="fa fa-check"></i>');
						btn.prop('disabled', true);
					}else{
						btn.find('i').remove();
						btn.prop('disabled', false);
					}
				});
			});
			jQuery('#update_urls').prop('disabled', false);
			jQuery('#update_urls').click(function() {
				jQuery(this).append(' <i class="fa fa-spinner fa-spin"></i>');
				var data = {
					'action': 'scrybs_action',
					'todo': 'update_url_list'
				};
				var btn = jQuery(this);
				jQuery.post(ajaxurl, data, function(response) {
					if(response == '1'){
						btn.find('i').remove();
						btn.append(' <i class="fa fa-check"></i>');
						btn.prop('disabled', true);
					}else{
						btn.find('i').remove();
						btn.prop('disabled', false);
					}
				});
			});
		});
		</script> <?php
	}
	
	public function scrybs_action() {
		$todo = $_REQUEST['todo'];
		if($todo == 'empty_cache'){
			if(emptyDir(SCRYBS_CACHE_CONTENT_FOLDER)){
				echo '1';
			}else{
				echo '0';
			}
		}else if($todo == 'update_url_list'){
			if( $this->update_translated_url_list() ){
				echo '1';
			}else{
				echo '0';
			}
		}
		wp_die();
	}
	
	public function update_translated_url_list(){
		$action = 'get_translated_url';
		$parameters = array("api_key"=>$this->apikey, "website_key"=>$this->websitekey, "action"=>$action, "source"=>$this->l_source,"languages"=>$this->l_target);
		$results = doRequest(SCRYBS_API_URL,$parameters);
		$json = json_decode($results,true); 
		if(json_last_error() == JSON_ERROR_NONE){
			if(isset($json['success']) && ($json['success']==0 || $json['success']==1)) {
				if($json['success']==1) {
					if(isset($json['url_list'])) {
						$url_list = array();
						$url_list = $json['url_list'];
						
						$newjson = json_encode($url_list, JSON_UNESCAPED_UNICODE);
						file_put_contents(SCRYBS_PLUGIN_PATH.'/res/urllist.json', $newjson);
						return true;
					}else{
						return false;
					}
				}else{
					return false;
				}
			}
		}else{
			return false;
		}
	}
    
    public function scrybs_admin_notices_action(){
	    settings_errors( 'plugin_settings' );
    }
	    						
	public function display_plugin_setup_page() {
	    include_once( 'partials/scrybs-admin-display.php' );
	}
			
	public function scrybs_save_settings($input) {
		    	
	    $parameters['api_key'] 		= $input['api_key'];
	    $scrybs_source = isset( $_POST['scrybs']['source'] ) && !empty( $_POST['scrybs']['source'] ) ? TRUE : FALSE;
	    if($scrybs_source){
		   	$parameters['source']  		= $_POST['scrybs']['source'];
		   	$parameters['siteurl']	  	= get_option('home');
	   	}else{
		   	$parameters['website_key']  = $this->websitekey;
		   	$parameters['languages']  	= implode(',', $_POST['target_languages']);
		   	if(esc_attr(get_option('show_scrybs_box'))=="no") {
		   		update_option('show_scrybs_box','yes');
		   	}
	   	}
	   	$parameters['action']  		= 'save_settings';  
			
        $valid = array();
	   	
	    $msg = json_encode($parameters);
	    
	   	$results = doRequest(SCRYBS_API_URL,$parameters);
		$json = json_decode($results,true);
		if(json_last_error() == JSON_ERROR_NONE) 
		{	    										
			if($json['isvalid']=='yes'){
				if($json['error']==0){
					$valid['api_key']    = $json['api_key'];
					if(isset($json['source'])){
						$valid['source'] = $json['source'];
					}else{
						$valid['source'] = $this->l_source;
					}    
					
					if(isset($_POST['scrybs']['automatic_translation']) && $_POST['scrybs']['automatic_translation'] == 'yes'){
						$valid['automatic_translation'] = 'yes';
					}else{
						$valid['automatic_translation'] = 'no';
					}
					
					
					if(isset($_POST['scrybs']['browser_redirect']) && $_POST['scrybs']['browser_redirect'] == 'yes'){
						$valid['browser_redirect'] = 'yes';
					}else{
						$valid['browser_redirect'] = 'no';
					}
					
					if(isset($_POST['scrybs']['activate_lang_switcher']) && $_POST['scrybs']['activate_lang_switcher'] == 'yes'){
						$valid['activate_lang_switcher'] = 'yes';
					}else{
						$valid['activate_lang_switcher'] = 'no';
					}
					
					if(isset($_POST['scrybs']['sc_in_menu']) && $_POST['scrybs']['sc_in_menu'] == 'yes'){
						$valid['sc_in_menu'] = 'yes';
					}else{
						$valid['sc_in_menu'] = 'no';
					}
                    
					if(isset($_POST['scrybs']['in_menu']) && $_POST['scrybs']['in_menu'] == 'yes'){
						$valid['in_menu'] = 'yes';
					}else{
						$valid['in_menu'] = 'no';
					}
					
					if(isset($_POST['scrybs']['language_names'])){
						$valid['language_names'] = $_POST['scrybs']['language_names'];
					}else{
						$valid['language_names'] = 'full_names';
					}
					
					if(isset($_POST['scrybs']['is_dropdown']) && $_POST['scrybs']['is_dropdown'] == 'yes'){
						$valid['is_dropdown'] = 'yes';
					}else{
						$valid['is_dropdown'] = 'no';
					}
					
					
					if(isset($_POST['scrybs']['flag_style'])){
						$valid['flag_style'] = $_POST['scrybs']['flag_style'];
					}else{
						$valid['flag_style'] = 'flstyle1';
					}
					
					if(isset($_POST['scrybs']['icons'])){
						$valid['icons'] = $_POST['scrybs']['icons'];
					}else{
						$valid['icons'] = 'flags';
					}
					
					if(isset($_POST['scrybs']['en_flag'])){
						$valid['en_flag'] = $_POST['scrybs']['en_flag'];
					}else{
						$valid['en_flag'] = 'gb';
					}
					if(isset($_POST['scrybs']['es_flag'])){
						$valid['es_flag'] = $_POST['scrybs']['es_flag'];
					}else{
						$valid['es_flag'] = 'es';
					}
					if(isset($_POST['scrybs']['pt_flag'])){
						$valid['pt_flag'] = $_POST['scrybs']['pt_flag'];
					}else{
						$valid['pt_flag'] = 'br';
					}
					if(isset($_POST['scrybs']['fr_flag'])){
						$valid['fr_flag'] = $_POST['scrybs']['fr_flag'];
					}else{
						$valid['fr_flag'] = 'fr';
					}
					if(isset($_POST['scrybs']['de_flag'])){
						$valid['de_flag'] = $_POST['scrybs']['de_flag'];
					}else{
						$valid['de_flag'] = 'de';
					}
					
					if(isset($_POST['scrybs']['arrow_style'])){
						$valid['arrow_style'] = $_POST['scrybs']['arrow_style'];
					}else{
						$valid['arrow_style'] = 'arrow1';
					}
					
					if(isset($_POST['scrybs']['scrybs_url_exclusion'])){
						$exclusions = str_replace(' ', '', $_POST['scrybs']['scrybs_url_exclusion']);
						update_option('scrybs_url_exclusion', $exclusions);
					}
					
					$valid['websitekey'] 	= $json['websitekey'];
					$valid['languages']  	= $json['languages'];
					$valid['maxlanguages']  = $json['maxlanguages'];
					add_settings_error('plugin_settings', 'api_key_texterror', __('Your Scrybs settings were successfully saved.', $this->plugin_name), 'updated');
				}else{
					add_settings_error('plugin_settings', 'api_key_texterror', __('An error on Scrybs side occurred or your current plan doesn\'t allow these settings', $this->plugin_name), 'error');
				}
			}else{
				add_settings_error('plugin_settings', 'api_key_texterror', __('Invalid or expired API key.', $this->plugin_name), 'error');
			}
	   }else{
	   		add_settings_error('plugin_settings', 'api_key_texterror', __('An error occurred while trying to save your settings.', $this->plugin_name), 'error');
	   }
	   return $valid;
	   
	 }
	 
	 public function scrybs_options_update() {
	    register_setting($this->plugin_name, $this->plugin_name, array($this, 'scrybs_save_settings'));
	 }
	 
	 public function wpse_58613_comment_redirect( $location ) {
		//global $wpdb;
		//CACHE ISSUE with $location = $_SERVER["HTTP_REFERER"]."#comment-".$wpdb->insert_id;
		$location = $_SERVER["HTTP_REFERER"]."#comments";
		return $location;
	}
	
}
