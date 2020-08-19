=== Plugin Name ===
Contributors: gscrybs
Tags: scrybs,translate,translation,translations,traduction,traductions,multilingual,bilingual,localization,multilanguage,language,translator,traduccion,traduzione,übersetzung,multilanguage,multilingue,traducteur,vertaling,tradução,tradutor
Requires at least: 4.0
Tested up to: 4.9
Stable tag: 1.3.3.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Make your Wordpress site multilingual in a few clicks with automatic, manual or professional translation. SEO Ready and compatible with any plugin.

== Description ==

Scrybs Multilingual & Translation is the easiest and fastest way to make your Wordpress site multilingual and translate it in no time. Save 95% of time and efforts spent on translating your website.

The solution is a WordPress plugin that effortlessly syncs with your source content. Once linked, all of your multilingual content is provided on the Scrybs dashboard. Here, you can manage your translations and select to translate the content yourself, use automatic translation tools or our community of professional translators. 

Given the importance of Google rankings, the Scrybs Multilingual Plugin follows the best practices for international SEO. All your translations are read by search engines. The Scrybs plugin also provides for URL translation, a key Google indicator.

[youtube https://www.youtube.com/watch?v=qrg1RI7oObM]

- 64 Languages to translate into 

- Fully compatible with all of your favorite WordPress themes and plugins.

- Content is automatically parsed from the HTML source code and pushed to your Scrybs cloud account. 

- Manage all of your translations on your Scrybs Dashboard. Here you can order automatic or professional translations as you please. 

- Make use of Scrybs approved professional translators within our community. 

- 100% SEO ready. The Scrybs multilingual plugin follows the best practices for international Search Engines. Your translated content is provided in HTML so it is easily read and indexed.

- URL translation for more effective user experience and SEO

- Language switcher provided with a number of different customization options

- Continual Support straight from a member of the Scrybs team

Is Scrybs Translation free?

Scrybs is free for small websites with less than 10 pages and one extra language. But you can upgrade anytime in your Scrybs dashboard: https://scrybs.com/wordpress-multilingual-plugin/

/* This plugin was forked from Weglot and improved in many ways such as URL translation managed remotely, content cache system, translation cache system, ability to use the plugin if Wordpress is installed in a folder, possibility to exclude folders from translation. */

== Installation ==

= Minimum Requirements for Scrybs Multilingual & Translation =
* WordPress 3.0 or greater
* PHP version 5.6 or greater
* URL rewriting activated

1. Upload the plugin files to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Get your Scrybs API key by registering through https://scrybs.com/en/auth/registration/plugin
4. Use the Translations menu to configure the plugin
5. Get your Scrybs API key by registering through https://scrybs.com/en/auth/registration/plugin
6. Enter your API key and select your source language.
7. Select your target languages and configure your language switcher.
8. Tada! Your website is now multilingual, you can edit your translations in your Scrybs dashboard.

== Frequently Asked Questions ==

= Is Scrybs plugin free? =

Scrybs is free for small websites with less than 10 pages and one extra language. But you can upgrade anytime in your Scrybs dashboard: https://scrybs.com/wordpress-multilingual-plugin/

= Can I position the language switcher wherever I want? =

Yes, you can use the default position (bottom right of the screen), add it to your menu, use it as widget, a shortcode or hard coded. All explanations are on the Settings page.

= Which plugins Scrybs is compatible with? =

Scrybs is compatible with any plugin such as Yoast, WooCommerce, W3 Total Cache, etc…

= Can I translate my permalinks? =

Absolutely, permalink translation is available in your Scrybs dashboard.

= Can I exclude URLs from translation? =

Yes, you can exclude URLs or Folders that you don’t want to translate.

= Can I exclude some words that I never want to translate? =

Yes, you can exclude words by adding notranslate to their HTML tag.

= Support =

Feel free to send us an email on info@scrybs.com, visit our website https://scrybs.com/ or you can read our Plugin documentation here: https://scrybs.com/en/support/categories/10-wordpress-plugin.

== Screenshots ==

1. Language switcher on front end
2. Scrybs translation dashboard
3. Scrybs translation settings page
4. Scrybs translation language switcher customizer

== Upgrade Notice ==
See changelog for upgrade changes.

== Changelog ==
= 1.3.3.2 =   
Added filter hooks to manipulate the Language Switcher.  See example in changelog for howto.
hook #1: scrybs_languages_mainmenu_css
hook #2: scrybs_languages_submenu_css

example:
/* Top level */
add_filter( 'scrybs_languages_submenu_css', 'scrybs_languages_submenu_css', 10, 1 ); 
/* Submenu */
add_filter( 'scrybs_languages_mainmenu_css', 'scrybs_languages_mainmenu_css', 10, 1 );   

function scrybs_languages_mainmenu_css( $css ) { return sprintf( "%s%s", $css, ' add-main-menu-class' ); }  
function scrybs_languages_submenu_css( $css ) { return sprintf( "%s%s", $css, ' add-sub-menu-class' ); }

= 1.3.3.2 =   
Added jQuery in own namespace due some strange behavior with other themes
fixed "Fatal error: Can't use method return value in write context"

= 1.3.3 =   
* Plan update in settings 
* Added link to upgrade plan in settings 
* Now displaying a 404 page if no translation is returned
* Added Settings link in plugin list

= 1.3.2 =   
* fixed typo in language switcher css 
* fixed native hreflang link 
* Removed empty "res/hashcontent.json" file this would overwrite an already translated website
* Add file creation check "res/hashcontent.json" in the activate_plugin hook.

= 1.3.1 =
* Fixed typo in "Shortcode in menu"
* Added Flag of Thailand in the defaults flag list

= 1.3 =   
* 4 Letter Country code bug on the Homepage fixed

= 1.2.1 =   
* Added a new Language replacement for the navigation menu. Some Themes do not allow injection in the navigation, please use {{{scrybs_switcher}}}
* Moved the scraping technology in to another action; wp_loaded

= 1.1.9.2 =   
* Checkbox admin option "Automatic translation" couldnt be unchecked 

= 1.1.9.1 =   
* Checkbox admin option "Dropdown button?" couldnt be unchecked 

= 1.1.9 =   
* Ad-hoc Fix White Screens

= 1.1.8 =   
* Add option to disable the Language Switcher
* Fixed mallformed javascript link

= 1.1.7 =   
* Fixed Admin jQuery script
* In the post edit screens, users couldn't add tags, images or edit publish settings. 

= 1.1.6 =   
* Added dependecy check for php-intl
* renamed endWith function so it doesnt collide with other plugins and cause fatal error while installing Scrybs Plug-in
* 
= 1.1.5 =   
* Refactor some code

= 1.1.4 =
* Fixed Minor Bug

= 1.1.3 =
* Fixed minor bug

= 1.1.2 =
* WP Compliance

= 1.1.1 =
* Fixed links for wordpress installed in subfolder
* Fixed settings box opening everytime
* Code improvement

= 1.1.0 =
* Fixed 404 status on translated pages
* Added Empty cache button
* Added Update/load translated URLs button
* Added Explainer box after installation
* Added Urls/Folders translation exclusions option

= 1.0.3 =
* Fixed Arabic flag and RTL direction in cloud
* Not displaying original content in case of no translation to avoid duplicate content
