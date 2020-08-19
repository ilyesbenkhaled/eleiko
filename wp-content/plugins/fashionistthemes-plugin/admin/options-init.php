<?php

    /**
     * For full documentation, please visit: http://docs.reduxframework.com/
     * For a more extensive sample-config file, you may look at:
     * https://github.com/reduxframework/redux-framework/blob/master/sample/sample-config.php
     */

    if ( ! class_exists( 'Redux' ) ) {
        return;
    }

    // This is your option name where all the Redux data is stored.
    $opt_name = "fashionist_options";

    /**
     * ---> SET ARGUMENTS
     * All the possible arguments for Redux.
     * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
     * */

    $theme = wp_get_theme(); // For use with some settings. Not necessary.

    $args = array(
        'opt_name' => 'fashionist_options',
        'use_cdn' => TRUE,
        'display_name' => 'Fashionist Options',
        'display_version' => '1.0.0',
        'page_slug' => 'fashionist_options',
        'page_title' => 'Fashionist Theme Options',
        'update_notice' => TRUE,
        'admin_bar' => TRUE,
        'menu_type' => 'menu',
        'menu_title' => 'Fashionist Options',
        'allow_sub_menu' => TRUE,
        'page_parent_post_type' => 'your_post_type',
        'customizer' => TRUE,
        'default_mark' => '*',
        'hints' => array(
            'icon' => 'el el-question-sign',
            'icon_position' => 'right',
            'icon_size' => 'normal',
            'tip_style' => array(
                'color' => 'light',
            ),
            'tip_position' => array(
                'my' => 'top left',
                'at' => 'bottom right',
            ),
            'tip_effect' => array(
                'show' => array(
                    'duration' => '500',
                    'event' => 'mouseover',
                ),
                'hide' => array(
                    'duration' => '500',
                    'event' => 'mouseleave unfocus',
                ),
            ),
        ),
        'output' => TRUE,
        'output_tag' => TRUE,
        'settings_api' => TRUE,
        'cdn_check_time' => '1440',
        'compiler' => TRUE,
        'page_permissions' => 'manage_options',
        'save_defaults' => TRUE,
        'show_import_export' => TRUE,
        'database' => 'options',
        'transient_time' => '3600',
        'network_sites' => TRUE,
    );
    /*
     * ---> START HELP TABS
     */

    $tabs = array(
        array(
            'id'      => 'redux-help-tab-1',
            'title'   => __( 'Theme Information 1', 'fashionist' ),
            'content' => __( '<p>This is the tab content, HTML is allowed.</p>', 'fashionist' )
        ),
        array(
            'id'      => 'redux-help-tab-2',
            'title'   => __( 'Theme Information 2', 'fashionist' ),
            'content' => __( '<p>This is the tab content, HTML is allowed.</p>', 'fashionist' )
        )
    );
    Redux::setHelpTab( $opt_name, $tabs );

    // Set the help sidebar
    $content = __( '<p>This is the sidebar content, HTML is allowed.</p>', 'fashionist' );
    Redux::setHelpSidebar( $opt_name, $content );


    /*
     * <--- END HELP TABS
     */


    /*
     *
     * ---> START SECTIONS
     *
     */
    Redux::setSection( $opt_name, array(
        'title' => __( 'Header Setting', 'fashionist' ),
        'id'    => 'header',
        'icon'  => 'el el-cogs',
        'fields' => array(
            array(
                'id'       => 'logo_text',
                'type'     => 'text',
                'title'    => __('Fashionist', 'fashionist'),
                'default'  => 'Fashionist'
            ),
            array(
                'id'       => 'welcome_text',
                'type'     => 'text',
                'title'    => __('Welcome Text', 'fashionist'),
                'default'  => 'Welcome to Fashionist'
            ),
            array(
                'id'       => 'call_text',
                'type'     => 'text',
                'title'    => __('Call Us', 'fashionist'),
                'default'  => '+49 1234 5678 9'
            )                    
        )
    ) );

     Redux::setSection( $opt_name, array(
        'title' => __( 'Blog Setting', 'fashionist' ),
        'id'    => 'blog',
        'icon'  => 'el el-cogs',
        'fields' => array(
            array(
                'id'       => 'blog_single',
                'type'     => 'select',
                'title'    => __('Select Single page Style', 'fashionist'),            
                'options'  => array(
                    '1' => 'Without Sidebar',
                    '2' => 'With Sidebar'                    
                ),
                'default'  => '1',
            )
        )
    ) );

    Redux::setSection( $opt_name, array(
        'title' => __( 'Footer Setting', 'fashionist' ),
        'id'    => 'basic2',
        'icon'  => 'el el-cogs',
        'fields' => array(
            array(
                'id'       => 'footer_v',
                'type'     => 'select',
                'title'    => __('Select Footer Style', 'fashionist'),            
                // Must provide key => value pairs for select options
                'options'  => array(
                    '1' => 'Without Border',
                    '2' => 'With Border'                    
                ),
                'default'  => '1',
            ),
            array(
                'id'       => 'facebook_link',
                'type'     => 'text',
                'title'    => __('Facebook', 'fashionist'),
            ),
            array(
                'id'       => 'twitter_link',
                'type'     => 'text',
                'title'    => __('Twitter', 'fashionist'),
            ),
            array(
                'id'       => 'pinterest_link',
                'type'     => 'text',
                'title'    => __('Pinterest', 'fashionist'),
            ),
            array(
                'id'       => 'instagram_link',
                'type'     => 'text',
                'title'    => __('Instagram', 'fashionist'),
            ),
            array(
                'id'       => 'copyright_text',
                'type'     => 'text',
                'title'    => __('Copyright Text', 'fashionist'),
            ),
        )
    ) );

    Redux::setSection( $opt_name, array(
        'title' => __( 'Font Settings', 'fashionist' ),
        'id'    => 'fonts',
        'icon'  => 'el el-font',
        
        'fields' => array(            
            array(
                'id'          => 'heading_fonts',
                'type'        => 'typography', 
                'title'       => __('Heading Fonts', 'fashionist'),
                'google'      => true, 
                'font-backup' => false,
                'subsets' => false,
                'text-align' => false,
                'color' => false,
                'font-size' => false,
                'line-height' => false,
                'subtitle'    => __('This will apply to h1,h2,h3,h4,h5,h6', 'fashionist'),
                'output'      => array('h1,h2,h3,h4,h5,h6'),
            ), 
            
            array(
                'id'          => 'body_fonts',
                'type'        => 'typography', 
                'title'       => __('Body Fonts', 'fashionist'),
                'google'      => true, 
                'font-backup' => false,
                'subsets' => false,
                'text-align' => false,
                'color' => false,
                'font-size' => false,
                'line-height' => false,
                'subtitle'    => __('This will apply to body tag', 'fashionist'),
                'output'      => array('body'),
            ),               
        ),
            
    ) );


    Redux::setSection( $opt_name, array(
        'title' => __( 'Woocommerce Setting', 'fashionist' ),
        'id'    => 'woocommerce_setting',
        'icon'  => 'el el-cogs',
        'fields' => array(
            array(
                'id'       => 'shop_layout',
                'type'     => 'select',
                'title'    => __('Select Shop Page Layout', 'fashionist'),            
                // Must provide key => value pairs for select options
                'options'  => array(
                    '1' => 'Full width with list',
                    '2' => 'Full width with grid',
                    '3' => 'Sidebar with list',
                    '4' => 'Sidebar with grid' 
                    ),                   
                'default'  => '4',
            ),
            array(
                'id'       => 'product_layout',
                'type'     => 'select',
                'title'    => __('Select Product Page Layout', 'fashionist'),            
                // Must provide key => value pairs for select options
                'options'  => array(
                    '1' => 'with sidebar',
                    '2' => 'with out sidebar'                
                ),
                'default'  => '1',
            ),
        )
    ) );

    /*
     * <--- END SECTIONS
     */
