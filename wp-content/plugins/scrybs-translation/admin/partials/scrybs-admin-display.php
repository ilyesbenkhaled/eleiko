<?php
/**
 * @link       https://scrybs.com/
 * @since      1.9.2
 *
 * @package    Scrybs
 * @subpackage Scrybs/admin/partials  
 * @author     Scrybs <info@scrybs.com>
 */
global $scrybs, $scrybs_settings, $wpdb;

$api_key = $scrybs->get_setting('api_key');
$website_key = $scrybs->get_setting('websitekey');
$default_language = $scrybs->get_source_language();
$selected_languages = $scrybs->get_active_languages();
$available_languages = $scrybs->get_available_languages();
$maxlanguages = $scrybs->get_setting('maxlanguages');

if (empty($api_key)) {
    $is_first_time = 'yes';
} else {
    $is_first_time = 'no';
}

$automatic_translation_ischecked = ' checked="checked"';
$automatic_translation = $scrybs->get_setting('automatic_translation');
if (isset($automatic_translation)) {
    if ($automatic_translation == 'no') {
        $automatic_translation_ischecked = '';
    }
}

$browser_redirect_checked = '';
$browser_redirect = $scrybs->get_setting('browser_redirect');
if (isset($browser_redirect)) {
    if ($browser_redirect == 'yes') {
        $browser_redirect_checked = ' checked="checked"';
    }
}

$activate_lang_switcher_checked = '';
$activate_lang_switcher = $scrybs->get_setting('activate_lang_switcher');
if (isset($activate_lang_switcher)) {
    if ($activate_lang_switcher == 'yes') {
        $activate_lang_switcher_checked = ' checked="checked"';
    }
}

$in_menu_checked = '';
$in_menu = $scrybs->get_setting('in_menu');
if (isset($in_menu)) {
    if ($in_menu == 'yes') {
        $in_menu_checked = ' checked="checked"';
    }
}  

$sc_in_menu_checked = '';
$sc_in_menu = $scrybs->get_setting('sc_in_menu');
if (isset($sc_in_menu)) {
    if ($sc_in_menu == 'yes') {
        $sc_in_menu_checked = ' checked="checked"';
    }
}

$arrow_style_checked = 'arrow1';
$arrow_style = $scrybs->get_setting('arrow_style');
if (isset($arrow_style)) {
    $arrow_style_checked = $arrow_style;
}

$flag_style_checked = 'flstyle1';
$flag_style = $scrybs->get_setting('flag_style');
if (isset($flag_style)) {
    $flag_style_checked = $flag_style;
}

$icons = 'flags';
$icon_style = $scrybs->get_setting('icons');
if (isset($icon_style)) {
    $icons = $icon_style;
}

$lnames = 'full_names';
$lnames_style = $scrybs->get_setting('language_names');
if (isset($lnames_style)) {
    $lnames = $lnames_style;
}

$is_dropdown_checked = ' checked="checked"';
$is_dropdown_btn = $scrybs->get_setting('is_dropdown');
if (isset($is_dropdown_btn)) {
    if ($is_dropdown_btn == 'no') {
        $is_dropdown_checked = '';
    }
}

$en_flag = 'gb';
$en_fl_opt = $scrybs->get_setting('en_flag');
if (isset($en_fl_opt)) {
    $en_flag = $en_fl_opt;
}

$fr_flag = 'fr';
$fr_fl_opt = $scrybs->get_setting('fr_flag');
if (isset($fr_fl_opt)) {
    $fr_flag = $fr_fl_opt;
}

$pt_flag = 'br';
$pt_fl_opt = $scrybs->get_setting('pt_flag');
if (isset($pt_fl_opt)) {
    $pt_flag = $pt_fl_opt;
}

$es_flag = 'es';
$es_fl_opt = $scrybs->get_setting('es_flag');
if (isset($es_fl_opt)) {
    $es_flag = $es_fl_opt;
}

$de_flag = 'de';
$de_fl_opt = $scrybs->get_setting('de_flag');
if (isset($de_fl_opt)) {
    $de_flag = $de_fl_opt;
}

$selected_languages_keys = array_keys($selected_languages);

if (empty($api_key)) {
    $detected_code = $scrybs->detect_wp_current_language();
    $suffix = ' (auto detect)';
    $ischecked = ' checked="checked"';
} else {
    $detected_code = $default_language['code'];
    $suffix = '';
}		
?>
<div class="wrap">
	<?php 
	/* This function was forked from Weglot */
	if(esc_attr(get_option('show_scrybs_box'))=="yes") { ?>
	<div class="scrybsbox-blur">
			<div class="scrybsbox">
				<div class="scrybsclose-btn"><?php _e('Close', $this->plugin_name); ?> <i class="fa fa-times"></i></div>
				<h3 class="scrybsbox-title"><?php _e('Tada! Your Wordpress site is now multilingual.', $this->plugin_name); ?></h3>
				<p class="scrybsbox-text"><?php _e('A language switcher has been added to your website. Try it now!', $this->plugin_name); ?></p>
				<a class="scrybsbox-button button button-primary" href="<?php get_option('site_url'); ?>/" target="_blank"><?php _e('Go to website homepage', $this->plugin_name); ?></a>
				<p class="scrybsbox-subtext"><?php _e('Next step, edit your translations in your Scrybs account.', $this->plugin_name); ?></p>
			</div>
	</div>
	<?php update_option('show_scrybs_box','already'); }  ?>
    <h2><?php echo esc_html(get_admin_page_title()); ?></h2>
    <?php if(!empty($api_key)){ ?>
    		<ul class="scrybs-navigation-links js-scrybs-navigation-links">
                    <li><a href="#lang-sec-1"><?php _e('Settings', $this->plugin_name); ?></a></li>
                    <li><a href="#lang-sec-2"><?php _e('Languages', $this->plugin_name); ?></a></li>
                    <li><a href="#lang-sec-3"><?php _e('Options', $this->plugin_name); ?></a></li>
                    <li><a href="#lang-sec-4"><?php _e('Language switcher', $this->plugin_name); ?></a></li>
			</ul>
			<ul class="scrybs-actions">
				<li><button class="button button-primary" id="update_urls"><?php _e('Update Translated URLs', $this->plugin_name); ?></button></li>
			  	<li><button class="button button-primary" id="empty_cache"><?php _e('Empty cache', $this->plugin_name); ?></button></li>
			</ul>
	<?php } ?>
    <form method="post" enctype="multipart/form-data" name="scrybs_options" action="options.php" class="apikeyform">
    <?php
    	settings_fields($this->plugin_name);
		do_settings_sections($this->plugin_name);
    ?>
    <?php if(!empty($api_key) && !empty($website_key)){ ?>
    	<div id="scrybs-info-box">
    	  <div class="logo-scrybs">
    	  	<img src="<?php echo SCRYBS_PLUGIN_DIRURL.'res/logo-dark.png'; ?>" alt="Scrybs">
    	  </div>
    	  <div class="sub-box">
			  <p><?php _e('View and edit your translations in your Scrybs account:', $this->plugin_name); ?></p>
			  <a class="gotoscrybs" href="https://scrybs.com/en/cloud/<?php echo $website_key; ?>/" target="_blank"><?php _e('Edit my translations', $this->plugin_name); ?></a>	  
			  <?php if($maxlanguages != '' && $maxlanguages==1 ){ ?>
			  <a class="gotoscrybs" href="https://scrybs.com/en/cloud/upgrade" target="_blank"><?php _e('Upgrade my plan', $this->plugin_name); ?></a>
			  <?php } ?>
    	  </div>
    	  <div class="sub-box">
			  <h3><?php _e('Language Switcher Preview', $this->plugin_name); ?></h3>
			  <div class="sub-box-preview">
			  	<div class="scrybs-switcher-preview"></div>
			  </div>
    	  </div>
		</div>
	<?php } ?>
    	<table class="form-table">
        	<tr id="lang-sec-1">
				<th scope="row">
					<label for="<?php echo $this->plugin_name; ?>[api_key]"><?php _e('Scrybs API Key', $this->plugin_name); ?></label>
				</th>
				<td>
					<input type="text" class="regular-text" aria-describedby="key-desc" id="<?php echo $this->plugin_name; ?>-api_key" name="<?php echo $this->plugin_name; ?>[api_key]" value="<?php if(!empty($api_key)) echo $api_key; ?>"<?php if(!empty($api_key)){ echo ' readonly'; }else{ echo ' placeholder="'.__('i.e.', $this->plugin_name).' 3c1747b92a6fa668ed66206974ef5e52"';} ?>/>					
					 <?php if(empty($api_key)){ ?><p id="key-desc" class="description"><?php _e('Find your API key in your <a href="https://scrybs.com/en/cloud/">Scrybs account</a>.', $this->plugin_name); ?></a> or get one <a href="https://scrybs.com/en/auth/registration/plugin">here</a>.</p><?php }else{ ?>
					 <button class="button button-primary" type="submit" id="update_plan">Update my plan</button>
					 <?php } ?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="source"><?php _e('Current language', $this->plugin_name); ?></label>
				</th>
				<td>
					<select id="<?php echo $this->plugin_name; ?>-source" name="<?php echo $this->plugin_name; ?>[source]" aria-describedby="source-desc"<?php if(!empty($default_language['code'])){ echo ' disabled="disabled"'; } ?>>
						<?php
							foreach( $available_languages as $code => $name ){
								if(!empty($default_language['code'])){
									if($code == $detected_code){
										echo '<option value="'.$code.'" selected="selected" disabled="disabled">'.$name.' ('.$code.')</option>';
									}else{
										echo '<option value="'.$code.'" disabled="disabled">'.$name.' ('.$code.')</option>';
									}
								}else{
									if($code == $detected_code){
										echo '<option value="'.$code.'" selected="selected">'.$name.' ('.$code.')'.$suffix.'</option>';
									}else{
										echo '<option value="'.$code.'">'.$name.' ('.$code.')</option>';
									}
								}
							}
						?>
					</select>
					<p id="source-desc" class="description"><?php _e('Select your website\'s current language.', $this->plugin_name); ?></a></p>
				</td>
			</tr>
			<?php if(!empty($api_key)){ ?>
			<tr id="lang-sec-2">
				<th scope="row">
					<label for="source"><?php _e('Target languages', $this->plugin_name); ?></label>
				</th>
				<td>
					<p id="desc" class="description"><?php _e('Select the language(s) you would like to add to your website:', $this->plugin_name); ?></a></p>
					<div class="tlanglist">
						<?php
							asort( $available_languages );
							foreach( $available_languages as $code => $name ){
								if(empty($default_language['code'])){
									echo '<div class="tlang">';
									if(in_array($code, $selected_languages_keys)){
										echo '<label><input type="checkbox" value="'.$code.'" name="target_languages[]" checked="checked">';
									}else{
										echo '<label><input type="checkbox" value="'.$code.'" name="target_languages[]">';
									}
									echo '<strong>'.$name.'</strong> ('.$code.')</label></div>';
								}else{
									if($code != $default_language['code']){
										echo '<div class="tlang">';
										if(in_array($code, $selected_languages_keys)){
											echo '<label><input type="checkbox" value="'.$code.'" name="target_languages[]" checked="checked">';
										}else{
											echo '<label><input type="checkbox" value="'.$code.'" name="target_languages[]">';
										}
										echo '<strong>'.$name.'</strong> ('.$code.')</label></div>';
									}
								}
							}
						?>
					</div>
				</td>
			</tr>
			<tr id="lang-sec-3">
				<th colspan="2">
					<h2><?php _e('Options', $this->plugin_name); ?></h2>
				</th>
			</tr>
			<tr>
				<th scope="row">
					<label for="automatic_translation"><?php _e('Automatic translation', $this->plugin_name); ?></label>
				</th>
				<td>
					<label><input type="checkbox" value="yes" id="automatic_translation" name="<?php echo $this->plugin_name; ?>[automatic_translation]"<?php echo $automatic_translation_ischecked; ?>> <?php _e('Check if you want your content to be automatically translated.', $this->plugin_name); ?></label>
					<p class="description"><?php _e('If your Scrybs word credits are sufficient.', $this->plugin_name); ?></a></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="browser_redirect"><?php _e('Browser language redirect', $this->plugin_name); ?></label>
				</th>
				<td>
					<label><input type="checkbox" value="yes" id="browser_redirect" name="<?php echo $this->plugin_name; ?>[browser_redirect]"<?php echo $browser_redirect_checked; ?>> <?php _e('Automatically redirect visitors according to their browser language.', $this->plugin_name); ?></label>
				</td>
			</tr>
			<tr id="lang-sec-4">
				<th colspan="2">
					<h2><?php _e('Language Switcher', $this->plugin_name); ?></h2>
				</th>
			</tr>
			<tr>
				<th scope="row">
					<label for="language_names"><?php _e('Language names', $this->plugin_name); ?></label>
				</th>
				<td>
					<div>
						<select id="<?php echo $this->plugin_name; ?>-language-names" name="<?php echo $this->plugin_name; ?>[language_names]">
							<option value="no_names"<?php if($lnames=='no_names'){echo ' selected="selected"';} ?>><?php _e('No language names', $this->plugin_name); ?></option>
							<option value="code_names"<?php if($lnames=='code_names'){echo ' selected="selected"';} ?>><?php _e('Language codes', $this->plugin_name); ?></option>
							<option value="full_names"<?php if($lnames=='full_names'){echo ' selected="selected"';} ?>><?php _e('Full language names', $this->plugin_name); ?></option>
						</select>
						<p class="description"><?php _e('Select how you want to display language names.', $this->plugin_name); ?></a></p>
					</div>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="is_dropdown"><?php _e('Dropdown button?', $this->plugin_name); ?></label>
				</th>
				<td>
					<label><input type="checkbox" value="yes" id="is_dropdown" name="<?php echo $this->plugin_name; ?>[is_dropdown]"<?php echo $is_dropdown_checked; ?>> <?php _e('Check if you want the button to be a dropdown list.', $this->plugin_name); ?></label>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="icons"><?php _e('Flags or globe', $this->plugin_name); ?></label>
				</th>
				<td>
					<select id="<?php echo $this->plugin_name; ?>-icons" name="<?php echo $this->plugin_name; ?>[icons]" aria-describedby="icons-desc">
						<option value="noicon"<?php if($icons=='noicon'){echo ' selected="selected"';} ?>><?php _e('Language names only', $this->plugin_name); ?></option>
						<option value="globe"<?php if($icons=='globe'){echo ' selected="selected"';} ?>><?php _e('Globe icon', $this->plugin_name); ?></option>
						<option value="flags"<?php if($icons=='flags'){echo ' selected="selected"';} ?>><?php _e('Flags', $this->plugin_name); ?></option>
					</select>
					<p id="icons-desc" class="description"><?php _e('Select whether you want to display flags, a globe icon or neither.', $this->plugin_name); ?></a></p>
				</td>
			</tr>
			<tr id="flags-style">
				<th scope="row">
					<label for="icons"><?php _e('Flag style', $this->plugin_name); ?></label>
				</th>
				<td>
					<ul class="ul-options">
						<li><span class="sc-flags flstyle1 gb"><a href="#"></a></span><input type="radio" name="<?php echo $this->plugin_name; ?>[flag_style]" value="flstyle1"<?php if($flag_style_checked=='flstyle1'){echo ' checked="checked"';} ?>></li>
						<li><span class="sc-flags flstyle2 gb"><a href="#"></a></span><input type="radio" name="<?php echo $this->plugin_name; ?>[flag_style]" value="flstyle2"<?php if($flag_style_checked=='flstyle2'){echo ' checked="checked"';} ?>></li>
						<li><span class="sc-flags flstyle3 gb"><a href="#"></a></span><input type="radio" name="<?php echo $this->plugin_name; ?>[flag_style]" value="flstyle3"<?php if($flag_style_checked=='flstyle3'){echo ' checked="checked"';} ?>></li>
					</ul>
				</td>
			</tr>
			<tr id="flags-countries">
				<th scope="row">
					<label for="icons"><?php _e('Choose country flags', $this->plugin_name); ?></label>
				</th>
				<td>
					<ul class="ul-options">
						<li class="enflag">
							<select name="<?php echo $this->plugin_name; ?>[en_flag]">
								<option value="gb" disabled><?php _e('English flag', $this->plugin_name); ?></option>
								<option value="gb"<?php if($en_flag=='gb'){echo ' selected="selected"';} ?>>United Kingdom (default)</option>
								<option value="us"<?php if($en_flag=='us'){echo ' selected="selected"';} ?>>United States</option>
								<option value="au"<?php if($en_flag=='au'){echo ' selected="selected"';} ?>>Australia</option>
								<option value="ca"<?php if($en_flag=='ca'){echo ' selected="selected"';} ?>>Canada</option>
								<option value="jm"<?php if($en_flag=='jm'){echo ' selected="selected"';} ?>>Jamaica</option>
								<option value="ie"<?php if($en_flag=='ie'){echo ' selected="selected"';} ?>>Ireland</option>
							</select>
						</li>
						<li class="esflag">
							<select name="<?php echo $this->plugin_name; ?>[es_flag]">
								<option value="es" disabled><?php _e('Spanish flag', $this->plugin_name); ?></option>
								<option value="es"<?php if($es_flag=='es'){echo ' selected="selected"';} ?>>Spain (default)</option>
								<option value="mx"<?php if($es_flag=='mx'){echo ' selected="selected"';} ?>>Mexico</option>
								<option value="ar"<?php if($es_flag=='ar'){echo ' selected="selected"';} ?>>Argentina</option>
								<option value="co"<?php if($es_flag=='co'){echo ' selected="selected"';} ?>>Colombia</option>
								<option value="pe"<?php if($es_flag=='pe'){echo ' selected="selected"';} ?>>Peru</option>
								<option value="bo"<?php if($es_flag=='bo'){echo ' selected="selected"';} ?>>Bolivia</option>
								<option value="ur"<?php if($es_flag=='ur'){echo ' selected="selected"';} ?>>Uruguay</option>
								<option value="ve"<?php if($es_flag=='ve'){echo ' selected="selected"';} ?>>Venezuela</option>
								<option value="cl"<?php if($es_flag=='cl'){echo ' selected="selected"';} ?>>Chile</option>
								<option value="gt"<?php if($es_flag=='gt'){echo ' selected="selected"';} ?>>Guatemala</option>
								<option value="hn"<?php if($es_flag=='hn'){echo ' selected="selected"';} ?>>Honduras</option>
								<option value="py"<?php if($es_flag=='py'){echo ' selected="selected"';} ?>>Paraguay</option>
								<option value="sv"<?php if($es_flag=='sv'){echo ' selected="selected"';} ?>>El Salvador</option>
								<option value="ni"<?php if($es_flag=='ni'){echo ' selected="selected"';} ?>>Nicaragua</option>
							</select>
						</li>
						<li class="ptflag brflag">
							<select name="<?php echo $this->plugin_name; ?>[pt_flag]">
								<option value="br" disabled><?php _e('Portuguese flag', $this->plugin_name); ?></option>
								<option value="br"<?php if($pt_flag=='br'){echo ' selected="selected"';} ?>>Brazil (default)</option>
								<option value="pt"<?php if($pt_flag=='pt'){echo ' selected="selected"';} ?>>Portugal</option>
							</select>
						</li>
						<li class="frflag">
							<select name="<?php echo $this->plugin_name; ?>[fr_flag]">
								<option value="fr" disabled><?php _e('French flag', $this->plugin_name); ?></option>
								<option value="fr"<?php if($fr_flag=='fr'){echo ' selected="selected"';} ?>>France (default)</option>
								<option value="be"<?php if($fr_flag=='be'){echo ' selected="selected"';} ?>>Belgium</option>
								<option value="ch"<?php if($fr_flag=='ch'){echo ' selected="selected"';} ?>>Switzerland</option>
								<option value="ca"<?php if($fr_flag=='ca'){echo ' selected="selected"';} ?>>Canada</option>
							</select>
						</li>
						<li class="deflag">
							<select name="<?php echo $this->plugin_name; ?>[de_flag]">
								<option value="de" disabled><?php _e('German flag', $this->plugin_name); ?></option>
								<option value="de"<?php if($de_flag=='de'){echo ' selected="selected"';} ?>>Germany (default)</option>
								<option value="at"<?php if($de_flag=='at'){echo ' selected="selected"';} ?>>Austria</option>
								<option value="ch"<?php if($de_flag=='ch'){echo ' selected="selected"';} ?>>Switzerland</option>
								<option value="be"<?php if($de_flag=='be'){echo ' selected="selected"';} ?>>Belgium</option>
							</select>
						</li>
					</ul>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="icons"><?php _e('Arrow style', $this->plugin_name); ?></label>
				</th>
				<td>
					<ul class="ul-options">
						<li><span><i class="fa fa-caret-right" aria-hidden="true"></i></span><input type="radio" name="<?php echo $this->plugin_name; ?>[arrow_style]" value="arrow1"<?php if($arrow_style_checked=='arrow1'){echo ' checked="checked"';} ?>></li>
						<li><span><i class="fa fa-chevron-right" aria-hidden="true"></i></span><input type="radio" name="<?php echo $this->plugin_name; ?>[arrow_style]" value="arrow2"<?php if($arrow_style_checked=='arrow2'){echo ' checked="checked"';} ?>></li>
						<li><span><i class="fa fa-angle-right" aria-hidden="true"></i></span><input type="radio" name="<?php echo $this->plugin_name; ?>[arrow_style]" value="arrow3"<?php if($arrow_style_checked=='arrow3'){echo ' checked="checked"';} ?>></li>
						<li><span><?php _e('No arrow', $this->plugin_name); ?></span><input type="radio" name="<?php echo $this->plugin_name; ?>[arrow_style]" value="noarrow"<?php if($arrow_style_checked=='noarrow'){echo ' checked="checked"';} ?>></li>
					</ul>
				</td>
			</tr>
			<tr id="lang-sec-5">
				<th colspan="2">
					<h2><?php _e('Language Switcher Integration', $this->plugin_name); ?></h2>
					<h4><?php _e('By default, the language switcher appears on the bottom right but you can choose to place it anywhere.', $this->plugin_name); ?></h4>
				</th>
			</tr>
			<tr>
				<th scope="row">
					<label for="activate_lang_switcher"><?php _e('Activate switcher', $this->plugin_name); ?></label>
				</th>
				<td>
					<label><input type="checkbox" value="yes" id="activate_lang_switcher" name="<?php echo $this->plugin_name; ?>[activate_lang_switcher]"<?php echo $activate_lang_switcher_checked; ?>> <?php _e('By checking this option, the language switcher will be activated.', $this->plugin_name); ?></label>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="sc_in_menu"><?php _e('Shortcode in menu', $this->plugin_name); ?></label>
				</th>
				<td>
					<label><input type="checkbox" value="yes" id="sc_in_menu" name="<?php echo $this->plugin_name; ?>[sc_in_menu]"<?php echo $sc_in_menu_checked; ?>> <?php _e('By checking this option, you can add the language switcher as a shortcode to your navigation menu. {{{scrybs_switcher}}}', $this->plugin_name); ?></label>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="in_menu"><?php _e('Automatic in menu', $this->plugin_name); ?></label>
				</th>
				<td>
					<label><input type="checkbox" value="yes" id="in_menu" name="<?php echo $this->plugin_name; ?>[in_menu]"<?php echo $in_menu_checked; ?>> <?php _e('By checking this option, the language switcher will be automatic added to your navigation menu.', $this->plugin_name); ?></label>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php _e('A widget', $this->plugin_name); ?>
				</th>
				<td>
					<?php _e('Find the "Scrybs Multilingual" widget in Appearance -> Widgets, drag and drop it wherever you want to display the language switcher.', $this->plugin_name); ?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php _e('A shortcode', $this->plugin_name); ?>
				</th>
				<td>
					<?php _e('Place [scrybs_switcher] shortcode wherever you want to display the language switcher.', $this->plugin_name); ?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php _e('In sourcecode', $this->plugin_name); ?>
				</th>
				<td>
					<?php 
						$str = htmlspecialchars('Use <div id="scrybs_here"></div> in your HTML sourcecode to display the language switcher.');
					_e($str, $this->plugin_name); ?>
				</td>
			</tr>
			<tr id="lang-sec-6">
				<th colspan="2">
					<h2><?php _e('Translation exclusions (Optional)', $this->plugin_name); ?></h2>
					<h4 style="margin-bottom:0"><?php _e('By default, every URL is translated. You can choose to exclude URLs and folders from translation by writing them below.', $this->plugin_name); ?></h4>
				</th>
			</tr>
			<tr>
				<th scope="row">
					<?php _e('URLs or Folders (comma separated)', $this->plugin_name); ?>
				</th>
				<td>
					<div>
						<textarea type="text" rows="3" style="width:100%" name="<?php echo $this->plugin_name; ?>[scrybs_url_exclusion]" placeholder="i.e. /blog/*,/welcome-world/"><?php echo esc_attr( get_option('scrybs_url_exclusion') ); ?></textarea>
						<p class="description"><?php _e('Write the urls or folders you want to exclude from translations (followed by a * for folders).', $this->plugin_name); ?></a></p>
					</div>
				</td>
			</tr>
			<?php } ?>
			<input type="hidden" name="<?php echo $this->plugin_name; ?>[is_first_time]" value="<?php echo $is_first_time; ?>"/>
        </table>
        <?php if(!empty($api_key)){ $class='is-api-key'; }else{ $class='no-api-key'; } ?>
        <div class="<?php echo $class; ?>">
        <?php 
        	submit_button(__('Save', $this->plugin_name), 'primary','submit', TRUE); 
        ?>
        </div>
    </form>
        
</div>