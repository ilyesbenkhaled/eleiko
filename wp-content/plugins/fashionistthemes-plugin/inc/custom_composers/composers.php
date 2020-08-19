<?php
if( !function_exists( 'excerpt' ) ) {
    function excerpt($limit) {
     $excerpt = explode(' ', get_the_excerpt(), $limit);
        array_pop($excerpt);
        $excerpt = implode(" ",$excerpt).'...';
     $excerpt = preg_replace('`[[^]]*]`','',$excerpt);
     return $excerpt;
    }
}
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
                echo $page;
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

// Register Team Post Type
function fashionist_team_post_type() {

    $labels = array(
        'name'                  => _x( 'Teams', 'Post Type General Name', 'fashionist' ),
        'singular_name'         => _x( 'Team', 'Post Type Singular Name', 'fashionist' ),
        'menu_name'             => __( 'Team', 'fashionist' ),
        'name_admin_bar'        => __( 'Team', 'fashionist' ),
        'archives'              => __( 'Item Archives', 'fashionist' ),
        'parent_item_colon'     => __( 'Parent Item:', 'fashionist' ),
        'all_items'             => __( 'All Items', 'fashionist' ),
        'add_new_item'          => __( 'Add New Item', 'fashionist' ),
        'add_new'               => __( 'Add New', 'fashionist' ),
        'new_item'              => __( 'New Item', 'fashionist' ),
        'edit_item'             => __( 'Edit Item', 'fashionist' ),
        'update_item'           => __( 'Update Item', 'fashionist' ),
        'view_item'             => __( 'View Item', 'fashionist' ),
        'search_items'          => __( 'Search Item', 'fashionist' ),
        'not_found'             => __( 'Not found', 'fashionist' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'fashionist' ),
        'featured_image'        => __( 'Featured Image', 'fashionist' ),
        'set_featured_image'    => __( 'Set featured image', 'fashionist' ),
        'remove_featured_image' => __( 'Remove featured image', 'fashionist' ),
        'use_featured_image'    => __( 'Use as featured image', 'fashionist' ),
        'insert_into_item'      => __( 'Insert into item', 'fashionist' ),
        'uploaded_to_this_item' => __( 'Uploaded to this item', 'fashionist' ),
        'items_list'            => __( 'Items list', 'fashionist' ),
        'items_list_navigation' => __( 'Items list navigation', 'fashionist' ),
        'filter_items_list'     => __( 'Filter items list', 'fashionist' ),
    );
    $args = array(
        'label'                 => __( 'Team', 'fashionist' ),
        'description'           => __( 'Team Description', 'fashionist' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'thumbnail', ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'page',
    );
    register_post_type( 'team', $args );

}
add_action( 'init', 'fashionist_team_post_type', 0 );

add_action( 'add_meta_boxes', 'add_fashionist_team_metaboxes' );
// Add the Client Logo Meta Boxes
function add_fashionist_team_metaboxes() {
    add_meta_box('wpt_fashionist_team_location', 'Designation', 'wpt_fashionist_team_location', 'team', 'side', 'default');
}
function wpt_fashionist_team_location() {
    global $post;
    // Noncename needed to verify where the data originated
    echo '<input type="hidden" name="teammeta_noncename" id="teammeta_noncename" value="' .
    wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
    $location = get_post_meta($post->ID, 'team_designation', true) ? get_post_meta($post->ID, 'team_designation', true) : 'Manager';
    echo '<input type="text" name="team_designation" value="' . $location  . '" class="widefat" />';

}
// Save the Metabox Data
function wpt_save_fashionist_team_meta($post_id, $post) {

    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times
    $nonce = isset($_POST['teammeta_noncename']) ? $_POST['teammeta_noncename'] : '';
    if ( !wp_verify_nonce( $nonce, plugin_basename(__FILE__) )) {
    return $post->ID;
    }

    // Is the user allowed to edit the post or page?
    if ( !current_user_can( 'edit_post', $post->ID ))
        return $post->ID;

    // OK, we're authenticated: we need to find and save the data
    // We'll put it into an array to make it easier to loop though.

    $fashionist_team_meta['team_designation'] = $_POST['team_designation'];

    // Add values of $fashionist_team_meta as custom fields

    foreach ($fashionist_team_meta as $key => $value) { // Cycle through the $fashionist_team_meta array!
        if( $post->post_type == 'revision' ) return; // Don't store custom data twice
        $value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
        if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
            update_post_meta($post->ID, $key, $value);
        } else { // If the custom field doesn't have a value
            add_post_meta($post->ID, $key, $value);
        }
        if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
    }

}
add_action('save_post', 'wpt_save_fashionist_team_meta', 1, 2); // save the custom fields

// Register Client Post Type
function fashionist_client_post_type() {

    $labels = array(
        'name'                  => _x( 'Clients', 'Post Type General Name', 'fashionist' ),
        'singular_name'         => _x( 'Client', 'Post Type Singular Name', 'fashionist' ),
        'menu_name'             => __( 'Client', 'fashionist' ),
        'name_admin_bar'        => __( 'Client', 'fashionist' ),
        'archives'              => __( 'Item Archives', 'fashionist' ),
        'parent_item_colon'     => __( 'Parent Item:', 'fashionist' ),
        'all_items'             => __( 'All Items', 'fashionist' ),
        'add_new_item'          => __( 'Add New Item', 'fashionist' ),
        'add_new'               => __( 'Add New', 'fashionist' ),
        'new_item'              => __( 'New Item', 'fashionist' ),
        'edit_item'             => __( 'Edit Item', 'fashionist' ),
        'update_item'           => __( 'Update Item', 'fashionist' ),
        'view_item'             => __( 'View Item', 'fashionist' ),
        'search_items'          => __( 'Search Item', 'fashionist' ),
        'not_found'             => __( 'Not found', 'fashionist' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'fashionist' ),
        'featured_image'        => __( 'Featured Image', 'fashionist' ),
        'set_featured_image'    => __( 'Set featured image', 'fashionist' ),
        'remove_featured_image' => __( 'Remove featured image', 'fashionist' ),
        'use_featured_image'    => __( 'Use as featured image', 'fashionist' ),
        'insert_into_item'      => __( 'Insert into item', 'fashionist' ),
        'uploaded_to_this_item' => __( 'Uploaded to this item', 'fashionist' ),
        'items_list'            => __( 'Items list', 'fashionist' ),
        'items_list_navigation' => __( 'Items list navigation', 'fashionist' ),
        'filter_items_list'     => __( 'Filter items list', 'fashionist' ),
    );
    $args = array(
        'label'                 => __( 'Client', 'fashionist' ),
        'description'           => __( 'Client Description', 'fashionist' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'thumbnail', 'editor' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'page',
    );
    register_post_type( 'client', $args );

}
add_action( 'init', 'fashionist_client_post_type', 0 );

add_action( 'add_meta_boxes', 'add_fashionist_client_metaboxes' );
// Add the Client Logo Meta Boxes
function add_fashionist_client_metaboxes() {
    add_meta_box('fashionist_client_location', 'Designation', 'fashionist_client_location', 'client', 'side', 'default');
}
function fashionist_client_location() {
    global $post;
    // Noncename needed to verify where the data originated
    echo '<input type="hidden" name="clientmeta_noncename" id="clientmeta_noncename" value="' .
    wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
    $location = get_post_meta($post->ID, 'client_designation', true) ? get_post_meta($post->ID, 'client_designation', true) : 'Manager';
    echo '<input type="text" name="client_designation" value="' . $location  . '" class="widefat" />';

}
// Save the Metabox Data
function save_fashionist_client_meta($post_id, $post) {

    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times
    $nonce = isset($_POST['clientmeta_noncename']) ? $_POST['clientmeta_noncename'] : '';
    if ( !wp_verify_nonce( $nonce, plugin_basename(__FILE__) )) {
    return $post->ID;
    }

    // Is the user allowed to edit the post or page?
    if ( !current_user_can( 'edit_post', $post->ID ))
        return $post->ID;

    // OK, we're authenticated: we need to find and save the data
    // We'll put it into an array to make it easier to loop though.

    $fashionist_client_meta['client_designation'] = $_POST['client_designation'];

    // Add values of $fashionist_client_meta as custom fields

    foreach ($fashionist_client_meta as $key => $value) { // Cycle through the $fashionist_client_meta array!
        if( $post->post_type == 'revision' ) return; // Don't store custom data twice
        $value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
        if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
            update_post_meta($post->ID, $key, $value);
        } else { // If the custom field doesn't have a value
            add_post_meta($post->ID, $key, $value);
        }
        if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
    }

}
add_action('save_post', 'save_fashionist_client_meta', 1, 2); // save the custom fields


/*--------------- Custom Visual Composer Component's -----------------*/

/*------------- Fashionist Latest Product Collections ----------- */

if(! function_exists('fashionist_latest_product_collection') && fashionist_checkPlugin('woocommerce/woocommerce.php') && fashionist_checkPlugin('js_composer/js_composer.php')){
function fashionist_latest_product_collection() {
    vc_map(
        array(
            'name' => __( 'Fashionist Latest Product Collections','fashionist'),
            'base' => 'fashionist_latest_product_collection',
            'category' => __( 'Fashionist' ,'fashionist'),
            'params' => array(
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Title' ,'fashionist'),
                    'param_name' => 'title',
                    'value' => __( 'Latest' ,'fashionist'),
                    'description' => __( 'Add Title' ,'fashionist'),
                ),
                array(
                    'type' => 'textarea_html',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Description' ,'fashionist'),
                    'param_name' => 'content',
                    "value" => __( "<p>I am test text block. Click edit button to change this text.</p>", "fashionist" ),
                    "description" => __( "Enter your content.", "fashionist" )
                ),
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Display Product Limit','fashionist' ),
                    'param_name' => 'limit',
                    'value' => __( '3' ,'fashionist'),
                    'description' => __( 'Add Number Of Products','fashionist' ),
                ),
                array(
                    'type' => 'css_editor',
                    'heading' => __( 'CSS box', 'fashionist' ),
                    'param_name' => 'css',
                    'group' => __( 'Design Options', 'fashionist' ),
                ),
            )
        )
    );
}
add_action( 'vc_before_init', 'fashionist_latest_product_collection' );
function fashionist_latest_product_collection_function( $atts, $content ) {
    $atts = shortcode_atts(
        array(
            'limit' => __( '3' ,'fashionist'),
            'title' => __('Latest','fashionist'),
            'content' => wpb_js_remove_wpautop($content, true),
            'css' => __('css','fashionist'),
        ), $atts, 'fashionist_latest_product_collection'
    );


$html = '<section id="latest" class="hidden-xs hidden-sm hidden-md">';
$html .= '<div class="white"></div>';
$html .= '<div class="color"></div>';

    $html .= '<div class="container">';
        $html .= '<div class="col-lg-4">';
            $html .= '<div class="row">';
                $html .= '<div id="mini-slider">';
                    $html .= wp_reset_query();
                    $args = array( 'post_type' => 'product', 'posts_per_page' => $atts['limit']);
                    $loop = new WP_Query( $args );
                    while ( $loop->have_posts() ) : $loop->the_post();
                        global $product;
                    /*-------- Slide's --------*/
                    $url = wp_get_attachment_image_src( get_post_thumbnail_id($loop->ID) ,array(240,240));
                    $html .= '<div class="ms-slide active">';
                        $html .= '<div class="ms-product">';
                            $html .= '<div class="share">';
                               $html .= '<a href="https://www.facebook.com/sharer.php?u='.get_permalink().'" target="_blank"><span class="icon-facebook"></span></a>';
                                $html .= '<a href="https://twitter.com/share?url='.get_permalink().'" target="_blank"><span class="icon-twitter"></span></a>';
                                $html .= '<a href="https://www.instagram.com/create/button/?url='.get_permalink().'&media='.$url[0].'&description='.urlencode(get_the_title()).'"><span class="icon-instagram"></span></a>';
                                $html .= '<span class="icon-rss"></span>';
                            $html .= '</div>';

                            $html .= '<img src="'.$url[0].'" alt="" />';
                            $html .= '<span class="name"><a href="'.get_permalink().'">'.get_the_title().'</a></span>';
                            $html .= '<span class="price">'.$product->get_price_html().'</span>';
                        $html .= '</div>';
                    $html .= '</div>';
                    /*---------- Slide's --------*/
                    endwhile;
                    wp_reset_query();
                    $html .= '<div id="ms-bullets"></div>';
                $html .= '</div>';

            $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="col-lg-7 col-lg-offset-1">';
            $html .= '<h2>'.$atts['title'].'</h2>';
            $html .= $atts['content'] ;
        $html .= '</div>';
    $html .= '</div>';
$html .= '</section>';
    return $html;
}
add_shortcode( 'fashionist_latest_product_collection', 'fashionist_latest_product_collection_function' );
}

/*------------- Fashionist Product Category ----------- */
if( !function_exists('fashionist_product_category') && fashionist_checkPlugin('woocommerce/woocommerce.php') && fashionist_checkPlugin('js_composer/js_composer.php')){
function fashionist_product_category() {
    vc_map(
        array(
            'name' => __( 'Fashionist Category' ,'fashionist'),
            'base' => 'fashionist_product_category',
            'category' => __( 'Fashionist' ,'fashionist'),
            'params' => array(
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Title' ,'fashionist'),
                    'param_name' => 'title',
                    'value' => __( 'Latest' ,'fashionist'),
                    'description' => __( 'Add Title' ,'fashionist'),
                ),
                array(
                    'type' => 'textarea_html',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Description' ,'fashionist'),
                    'param_name' => 'content',
                    "value" => __( "<p>I am test text block. Click edit button to change this text.</p>", "fashionist" ),
                    "description" => __( "Enter your content.", "fashionist" )
                ),
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Display Category Slug' ,'fashionist'),
                    'param_name' => 'cat_ids',
                    'value' => __( 'blog,foo' ,'fashionist'),
                    'description' => __( 'Add Category Id seprated by coma(,)' ,'fashionist'),
                ),
                array(
                    'type' => 'dropdown',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Set Style','fashionist' ),
                    'param_name' => 'style',
                    'value' => array(
                        __( 'Style 1', 'fashionist' ) => 'style1',
                        __( 'Style 2', 'fashionist' ) => 'style2',
                        ),
                    'description' => __( 'Add category slug.','fashionist' ),
                ),
                array(
                    'type' => 'css_editor',
                    'heading' => __( 'CSS box', 'fashionist' ),
                    'param_name' => 'css',
                    'group' => __( 'Design Options', 'fashionist' ),
                ),
            )
        )
    );
}
add_action( 'vc_before_init', 'fashionist_product_category' );
function fashionist_product_category_function( $atts, $content ) {
    $atts = shortcode_atts(
        array(
            'cat_ids' => __('','fashionist'),
            'title' => __('','fashionist'),
            'content' => wpb_js_remove_wpautop($content, true),
            'style' => __('','fashionist'),
            'css' => __('css','fashionist'),
        ), $atts, 'fashionist_product_category'
    );

        $html = '<div id="welcome">';
            if($atts['title']){
                $html .= '<div style="text-align: center;">';
                    $html .= '<h2 class="welcome">'.$atts['title'].'</h2>';
                    $html .= $atts['content'];
                $html .= '</div>';
            }

            $html .= '<div class="container">';
                $tem_id = explode(',', $atts['cat_ids']);
                $counter = 1;
                if($atts['style'] != 'style2'){
                    foreach ($tem_id as $key) {
                        $a = get_term_by( 'slug', $key , 'product_cat' );
                        $thumbnail_id = get_woocommerce_term_meta( $a->term_id, 'thumbnail_id', true );
                        $image = wp_get_attachment_url( $thumbnail_id );
                        $img = get_field('secondary_image','product_cat_'.$a->term_id);
                        if($counter == 1){ $bottomleft = 'bottomleft'; }
                        if($counter == 2){ $bottomleft = 'bottomcenter';}
                        if($counter == 3){ $bottomleft = 'bottomright';}
                        $html .= '<div class="col-xs-12 col-sm-4">';
                            if($counter % 2 == 0){
                                $html .= '<div class="row">';
                                    $html .= '<div class="img">';
                                        $html .= '<a href="'.get_term_link($a->term_id,'product_cat').'"><img src="'.$img.'" alt="" /></a>';
                                        $html .= '<span class="title">'.$a->name.'</span>';
                                    $html .= '</div>';
                                $html .= '</div>';
                            }
                            $html .= '<div class="row  hidden-xs">';
                                $html .= '<div class="img color">';
                                    $html .= '<span class="'.$bottomleft.' num">0'.$counter.'</span>';
                                    $html .= '<a href="'.get_term_link($a->term_id,'product_cat').'"><img src="'.$image.'" alt="" /></a>';
                                $html .= '</div>';
                            $html .= '</div>';

                            if($counter % 2 == 1){
                                $html .= '<div class="row">';
                                    $html .= '<div class="img">';
                                        $html .= '<a href="'.get_term_link($a->term_id,'product_cat').'"><img src="'.$img.'" alt="" /></a>';
                                        $html .= '<span class="title">'.$a->name.'</span>';
                                    $html .= '</div>';
                                $html .= '</div>';
                            }
                        $html .= '</div>';
                        $counter++;
                    }
                }else{
                    $html .= '<div class="row">';
                    foreach ($tem_id as $key) {
                        $a = get_term_by( 'slug', $key , 'product_cat' );
                        $img = get_field('secondary_image','product_cat_'.$a->term_id);
                        $html .= '<div class="col-xs-12 col-sm-4">';
                            $html .= '<div class="img">';
                                $html .= '<img src="'.$img.'" alt="" />';
                                $html .= '<span class="title">'.$a->name.'</span>';
                            $html .= '</div>';
                        $html .= '</div>';
                    }
                    $html .= '</div>';
                }
            $html .= '</div>';
        $html .= '</div>';
    return $html;
}
add_shortcode( 'fashionist_product_category', 'fashionist_product_category_function' );
}
/*------------- Fashionist Category Products ----------- */
if( !function_exists('fashionist_categories_product') && fashionist_checkPlugin('woocommerce/woocommerce.php') && fashionist_checkPlugin('js_composer/js_composer.php')){
function fashionist_categories_product() {
    vc_map(
        array(
            'name' => __( 'Fashionist Category Products' ,'fashionist'),
            'base' => 'fashionist_categories_product',
            'category' => __( 'Fashionist' ,'fashionist'),
            'params' => array(
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Title' ,'fashionist'),
                    'param_name' => 'title',
                    'value' => __( 'Title' ,'fashionist'),
                    'description' => __( 'Add Title' ,'fashionist'),
                ),
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Per Category Limit' ,'fashionist'),
                    'param_name' => 'limit',
                    'value' => __( '6' ,'fashionist'),
                    'description' => __( 'Add Number Of Posts' ,'fashionist'),
                ),
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Category' ,'fashionist'),
                    'param_name' => 'cat_name',
                    'value' => __( 'foo,bar' ,'fashionist'),
                    'description' => __( 'Add category slug seperated by coma(,)' ,'fashionist'),
                ),
                array(
                    'type' => 'css_editor',
                    'heading' => __( 'CSS box', 'fashionist' ),
                    'param_name' => 'css',
                    'group' => __( 'Design Options', 'fashionist' ),
                ),
            )
        )
    );
}
add_action( 'vc_before_init', 'fashionist_categories_product' );
function fashionist_categories_product_function( $atts, $content ) {
    $atts = shortcode_atts(
        array(
            'title' => __('','fashionist'),
            'limit' => __('6','fashionist'),
            'cat_name' => __('','fashionist'),
            'style' => __('','fashionist'),
            'css' => __('css','fashionist'),
        ), $atts, 'fashionist_categories_product'
    );

    $temp_cat = explode(",",$atts['cat_name']);
    $counter  = 0;

    $html = '<section id="featured" class="variation_product">';
        $html .= '<div class="container">';

            $html .= '<div class="top">';

                if(isset($_GET['add-to-cart'])){
                    if(ctype_digit($_GET['add-to-cart'])){
                        $html .= '<div class="woocommerce-message">“'.get_the_title($_GET['add-to-cart']).'” has been added to your cart.</div>';
                    }else{
                        $html .= '<div class="woocommerce-error">“Must Need to select Color” has been added to your cart.</div>';
                    }

                }
                if(isset($_GET['add_to_wishlist'])){
                    $html .= '<div class="woocommerce-message">“'.get_the_title($_GET['add_to_wishlist']).'” has been added to your wishlist.</div>';
                }

                if($atts['title']){
                    $html .= '<h2 class="wline">'.$atts['title'].'</h2>';
                }
                $html .= '<div class="choose">';
                    $html .= '<ul class="nav-tabs">';
                    foreach ($temp_cat as $value) {
                        $term = get_term_by('slug', $value, 'product_cat'); $name = $term->name;
                        if($counter == 0){
                            $html .= '<li class="active"><a href="#'.$value.'">'.$name.'</a></li>';
                        }else{
                            $html .= '<li><a href="#'.$value.'">'.$name.'</a></li>';
                        }
                        $counter++;
                    }
                    $html .= '</ul>';
                    $html .= '</div>';
            $html .= '</div>';

            $html .= '<div class="tab-content">';
            $counter  = 0;
            foreach ($temp_cat as $value)
            {
                if($counter == 0){
                    $html .= '<div id="'.$value.'" class="tab-pane fade in active">';
                }else{
                    $html .= '<div id="'.$value.'" class="tab-pane fade">';
                }
                $html .= '<div class="row">';
                    $html .= wp_reset_query();
                    $args = array( 'post_type' => 'product', 'posts_per_page' => $atts['limit'], 'product_cat' => $value );
                    $loop = new WP_Query( $args );
                    while ( $loop->have_posts() ) : $loop->the_post();
                        global $product;
                        $url = wp_get_attachment_url( get_post_thumbnail_id($loop->get_id(),'large') );
                        $html .= '<div class="col-xs-12 col-sm-6 col-md-4">';
                            $html .= '<div class="product" id="p'.get_the_id().$value.'" data-id="'.get_the_id().'">';
                                $html .= '<div class="share">';
                                    $html .= '<a href="https://www.facebook.com/sharer.php?u='.get_permalink().'" target="_blank"><span class="icon-facebook"></span></a>';
                                    /*$html .= '<a href="https://twitter.com/share?url='.get_permalink().'" target="_blank"><span class="icon-twitter"></span></a>';*/
                                    $html .= '<a href="https://www.instagram.com/eleikotunisie/" target="_blank"><span class="fa fa-instagram"></span></a>';
                                    $html .= '<span class=""></span>';
                                $html .= '</div>';
                                $html .= '<a href="'.get_permalink().'"><img src="'.$url.'" alt="" /></a>';
                                $html .= '<span class="name"><a href="'.get_permalink().'">'.get_the_title().'</a></span>';
                                $html .= '<span class="price">'.$product->get_price_html().'</span>';

                                $regular_price = get_post_meta( get_the_ID(), '_regular_price', true);
                                $html .= '<div class="actions">';
                                    $html .= '<a class="action" href="'.$url.'" data-lightbox="'.rand().'"><span class="icon-maximize-plus"></span></a>';
                                    /*$html .= '<a class="action" href="?add_to_wishlist='.get_the_id().'"><span class="icon-heart"></span></a>';*/

                                    if ($regular_price == ""){
                                        $html .= '<a href="?add-to-cart=#" class="action add_to_cart_button ajax_add_to_cart added"><span class="icon-shoppingcart"></span></a>';
                                    }else{
                                        $html .= '<a href="?add-to-cart='.get_the_id().'" class="action add_to_cart_button ajax_add_to_cart added"><span class="icon-shoppingcart"></span></a>';
                                    }

                                $html .= '</div>';

                                if ($regular_price == "" && $product->get_type() == 'variable'){
                                    $available_variations = $product->get_available_variations();

                                    if($available_variations)
                                    {
                                        $variation_id = $available_variations[0]['variation_id'];
                                    }else{
                                        $variation_id = null;
                                    }
                                    if($variation_id && $variation_id != null ){
                                        $variable_product1= new WC_Product_Variation( $variation_id );
                                        $regular_price = $variable_product1->get_regular_price();
                                        $html .= '<div class="colors" data-product_variations="'.htmlspecialchars( json_encode( $available_variations ) ).'">';
                                            $pa_colour_values = get_the_terms( get_the_id(), 'pa_color');
                                            if($pa_colour_values && ! is_wp_error( $pa_colour_values ))
                                            {
                                                foreach ( $pa_colour_values as $pa_colour_value )
                                                {
                                                    $color = get_field('color_picker','pa_color_'.$pa_colour_value->term_id);
                                                    $html .= '<div class="circle" style="background:'.$color.'" data-id="'.$pa_colour_value->slug.'"></div>';
                                                }
                                            }
                                        $html .= '</div>';
                                    }
                                }

                                $html .= '<div class="add-to-cart">';
                                    $html .= '<span class="icon-plus" onclick="show_actions(\'p'.get_the_id().$value.'\')"></span>';
                                $html .= '</div>';
                                $html .= '<div class="add-to-cart close">';
                                    $html .= '<span class="icon-plus" onclick="hide_actions(\'p'.get_the_id().$value.'\')"></span>';
                                $html .= '</div>';
                            $html .= '</div>';
                        $html .= '</div>';
                    $counter++;
                    endwhile;
                    wp_reset_query();
                $html .= '</div>';
                $html .= '</div>';
            }
            $html .= '</div>'; // tab-content
        $html .= '</div>';
    $html .= '</section>';

    return $html;
}
add_shortcode( 'fashionist_categories_product', 'fashionist_categories_product_function' );
}
/*------------- Fashionist Products Portfolio ----------- */
if(! function_exists('fashionist_product_portfolio') && fashionist_checkPlugin('woocommerce/woocommerce.php') && fashionist_checkPlugin('js_composer/js_composer.php')){
function fashionist_product_portfolio() {
    vc_map(
        array(
            'name' => __( 'Fashionist Products Portfolio' ,'fashionist'),
            'base' => 'fashionist_product_portfolio',
            'category' => __( 'Fashionist' ,'fashionist'),
            'params' => array(
               array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Per Category Limit' ,'fashionist'),
                    'param_name' => 'limit',
                    'value' => __( '6' ,'fashionist'),
                    'description' => __( 'Add Number Of Posts','fashionist' ),
                ),
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Number Of columns','fashionist' ),
                    'param_name' => 'columnno',
                    'value' => __( '3' ,'fashionist'),
                    'description' => __( 'Add Number Of Columns' ,'fashionist'),
                ),
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Category' ,'fashionist'),
                    'param_name' => 'cat_name',
                    'value' => __( 'foo,bar' ,'fashionist'),
                    'description' => __( 'Add category slug seperated by coma(,)' ,'fashionist'),
                ),
                array(
                    'type' => 'css_editor',
                    'heading' => __( 'CSS box', 'fashionist' ),
                    'param_name' => 'css',
                    'group' => __( 'Design Options', 'fashionist' ),
                ),
            )
        )
    );
}
add_action( 'vc_before_init', 'fashionist_product_portfolio' );
function fashionist_unction_portfolio( $atts, $content ) {
    $atts = shortcode_atts(
        array(
            'limit' => __('6','fashionist'),
            'columnno' => __('3','fashionist'),
            'cat_name' => __('','fashionist'),
            'style' => __('','fashionist'),
            'css' => __('css','fashionist'),
        ), $atts, 'fashionist_product_portfolio'
    );

    $temp_cat = explode(",",$atts['cat_name']);
    $counter  = 0;

    $coulmnCal = 12 / $atts['columnno'];

    $html = '<div class="content variation_product" id="portfolio">';
        $html .= '<div class="container">';
            $html .= '<div class="choose-cat">';

                    $html .= '<ul class="nav-tabs">';
                    foreach ($temp_cat as $value) {
                        $term = get_term_by('slug', $value, 'product_cat'); $name = $term->name;
                        if($counter == 0){
                            $html .= '<li class="active"><a href="#'.$value.'">'.$name.'</a></li>';
                        }else{
                            $html .= '<li><a href="#'.$value.'">'.$name.'</a></li>';
                        }
                        $counter++;
                    }
                    $html .= '</ul>';

            $html .= '</div>';

            $html .= '<div class="tab-content">';
            $counter  = 0;
            foreach ($temp_cat as $value)
            {
                if($counter == 0){
                    $html .= '<div id="'.$value.'" class="tab-pane fade in active">';
                }else{
                    $html .= '<div id="'.$value.'" class="tab-pane fade">';
                }
                $html .= '<div class="row">';
                    if(isset($_GET['add-to-cart'])){
                        if(ctype_digit($_GET['add-to-cart'])){
                            $html .= '<div class="woocommerce-message">“'.get_the_title($_GET['add-to-cart']).'” has been added to your cart.</div>';
                        }else{
                            $html .= '<div class="woocommerce-error">“Must Need to select Color” has been added to your cart.</div>';
                        }

                    }
                    if(isset($_GET['add_to_wishlist'])){
                        $html .= '<div class="woocommerce-message">“'.get_the_title($_GET['add_to_wishlist']).'” has been added to your wishlist.</div>';
                    }


                    $html .= wp_reset_query();
                    $args = array( 'post_type' => 'product', 'posts_per_page' => $atts['limit'], 'product_cat' => $value );
                    $loop = new WP_Query( $args );
                    while ( $loop->have_posts() ) : $loop->the_post();
                        global $product;
                        $url = wp_get_attachment_url( get_post_thumbnail_id($loop->ID,'large') );
                        $html .= '<div class="col-xs-12 col-sm-6 col-md-'.$coulmnCal.'">';
                            $html .= '<div class="product" id="p'.get_the_id().$value.'" data-id="'.get_the_id().'">';
                                $html .= '<div class="share">';
                                    $html .= '<a href="https://www.facebook.com/sharer.php?u='.get_permalink().'" target="_blank"><span class="icon-facebook"></span></a>';
                                    $html .= '<a href="https://twitter.com/share?url='.get_permalink().'" target="_blank"><span class="icon-twitter"></span></a>';
                                    $html .= '<a href="https://www.instagram.com/pin/create/button/?url='.get_permalink().'&media='.$url.'&description='.urlencode(get_the_title()).'"><span class="icon-instagram"></span></a>';
                                    $html .= '<span class="icon-rss"></span>';
                                $html .= '</div>';
                                $html .= '<a href="'.get_permalink().'"><img src="'.$url.'" alt="" /></a>';
                                $html .= '<span class="name"><a href="'.get_permalink().'">'.get_the_title().'</a></span>';
                                $html .= '<span class="price">'.$product->get_price_html().'</span>';
                                $regular_price = get_post_meta( get_the_ID(), '_regular_price', true);
                                $html .= '<div class="actions">';
                                    $html .= '<a class="action" href="'.$url.'" data-lightbox="'.rand().'"><span class="icon-maximize-plus"></span></a>';
                                    $html .= '<a class="action" href="?add_to_wishlist='.get_the_id().'"><span class="icon-heart"></span></a>';

                                    if ($regular_price == ""){
                                        $html .= '<a href="?add-to-cart=#" class="action add_to_cart_button ajax_add_to_cart added"><span class="icon-shoppingcart"></span></a>';
                                    }else{
                                        $html .= '<a href="?add-to-cart='.get_the_id().'" class="action add_to_cart_button ajax_add_to_cart added"><span class="icon-shoppingcart"></span></a>';
                                    }

                                $html .= '</div>';


                                if ($regular_price == "" && $product->get_type() == 'variable'){
                                    $available_variations = $product->get_available_variations();
                                    if($available_variations)
                                    {
                                        $variation_id = $available_variations[0]['variation_id'];
                                    }else{
                                        $variation_id = null;
                                    }
                                    if($variation_id)
                                    {
                                        $variable_product1= new WC_Product_Variation( $variation_id );
                                        $regular_price = $variable_product1->get_regular_price();
                                        $html .= '<div class="colors" data-product_variations="'.htmlspecialchars( json_encode( $available_variations ) ).'">';
                                            $pa_colour_values = get_the_terms( get_the_id(), 'pa_color');
                                            if($pa_colour_values && ! is_wp_error( $pa_colour_values ))
                                            {
                                                foreach ( $pa_colour_values as $pa_colour_value )
                                                {
                                                    $color = get_field('color_picker','pa_color_'.$pa_colour_value->term_id);
                                                    $html .= '<div class="circle" style="background:'.$color.'" data-id="'.$pa_colour_value->slug.'"></div>';
                                                }
                                            }
                                        $html .= '</div>';
                                    }
                                }

                                $html .= '<div class="add-to-cart">';
                                    $html .= '<span class="icon-plus" onclick="show_actions(\'p'.get_the_id().$value.'\')"></span>';
                                $html .= '</div>';
                                $html .= '<div class="add-to-cart close">';
                                    $html .= '<span class="icon-plus" onclick="hide_actions(\'p'.get_the_id().$value.'\')"></span>';
                                $html .= '</div>';
                            $html .= '</div>';
                        $html .= '</div>';
                    $counter++;
                    endwhile;
                    wp_reset_query();
                $html .= '</div>';
                $html .= '</div>';
            }
            $html .= '</div>'; // tab-content
        $html .= '</div>';
    $html .= '</div>';

    return $html;
}
add_shortcode( 'fashionist_product_portfolio', 'fashionist_unction_portfolio' );
}

/*------------- Fashionist Top Product Collections ----------- */
if(! function_exists('fashionist_top_product_collection') && fashionist_checkPlugin('woocommerce/woocommerce.php') && fashionist_checkPlugin('js_composer/js_composer.php')){
function fashionist_top_product_collection() {
    vc_map(
        array(
            'name' => __( 'Fashionist Top Product Collections' ,'fashionist'),
            'base' => 'fashionist_top_product_collection',
            'category' => __( 'Fashionist' ,'fashionist'),
            'params' => array(
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Title' ,'fashionist'),
                    'param_name' => 'title',
                    'value' => __( 'Top' ,'fashionist'),
                    'description' => __( 'Add Title','fashionist' ),
                ),
                array(
                    'type' => 'textarea_html',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Description' ,'fashionist'),
                    'param_name' => 'content',
                    "value" => __( "<p>I am test text block. Click edit button to change this text.</p>", "fashionist" ),
                    "description" => __( "Enter your content.", "fashionist" )
                ),
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Display Product Limit' ,'fashionist'),
                    'param_name' => 'limit',
                    'value' => __( '4' ,'fashionist'),
                    'description' => __( 'Add Number Of Products' ,'fashionist'),
                ),
                array(
                    'type' => 'css_editor',
                    'heading' => __( 'CSS box', 'fashionist' ),
                    'param_name' => 'css',
                    'group' => __( 'Design Options', 'fashionist' ),
                ),
            )
        )
    );
}
add_action( 'vc_before_init', 'fashionist_top_product_collection' );
function fashionist_top_product_collection_function( $atts, $content ) {
    $atts = shortcode_atts(
        array(
            'limit' => __( '4' ,'fashionist'),
            'title' => __('Top','fashionist'),
            'content' => wpb_js_remove_wpautop($content, true),
            'css' => __('css','fashionist'),
        ), $atts, 'fashionist_top_product_collection'
    );

    $html = '<section id="top-products">';
        $html .= '<div style="text-align: center;">';
            $html .= '<h2 class="top">'.$atts['title'].'</h2>';
            $html .= '<p class="welcometext">'.$content.'</p>';
        $html .= '</div>';

        $html .= '<div class="container">';
            $html .= '<div class="row">';

                $args =
                array(
                    'post_type'      => 'product',
                    'posts_per_page' => $atts['limit'],
                    'meta_key'       => 'total_sales',
                    'orderby'        => 'meta_value',
                    'meta_query'     => array(
                        array(
                            'key'     => '_visibility',
                            'value'   => array( 'catalog', 'visible' ),
                            'compare' => 'IN'
                        )
                    ));
                    $html .= wp_reset_query();
                    $loop = new WP_Query( $args );
                    while ( $loop->have_posts() ) : $loop->the_post();
                        global $product;
                        $url = wp_get_attachment_url( get_post_thumbnail_id($loop->ID,'large') );
                        $html .= '<div class="col-xs-12 col-sm-6 col-lg-3">';
                            $html .= '<div class="product" id="p_'.get_the_id().'">';
                                $html .= '<div class="product-img">';

                                    $html .= '<img src="'.$url.'" alt="" />';
                                    $html .= '<div class="actions">';
                                        $html .= '<a class="action" href="'.$url.'" data-lightbox="'.rand().'"><span class="icon-maximize-plus"></span></a>';
                                        $html .= '<a class="action" href="?add_to_wishlist='.get_the_id().'"><span class="icon-heart"></span></a>';
                                       $html .= '<a rel="nofollow" href="/?add-to-cart='.get_the_id().'" data-quantity="1" data-product_id="'.get_the_id().'" data-product_sku="" class="action add_to_cart_button ajax_add_to_cart added"><span class="icon-shoppingcart"></span></a>';
                                    $html .= '</div>';

                                    $html .= '<div class="add-to-cart">';
                                        $html .= '<span class="icon-plus" onclick="show_actions(\'p_'.get_the_id().'\')"></span>';
                                    $html .= '</div>';

                                    $html .= '<div class="add-to-cart close">';
                                        $html .= '<span class="icon-plus" onclick="hide_actions(\'p_'.get_the_id().'\')"></span>';
                                    $html .= '</div>';

                                $html .= '</div>';
                                $html .= '<span class="name"><a href="'.get_the_permalink().'">'.get_the_title().'</a></span>';
                                $html .= '<span class="price">'.$product->get_price_html().'</span>';
                            $html .= '</div>';
                        $html .= '</div>';
                    endwhile;
                    wp_reset_query();

            $html .= '</div>';
        $html .= '</div>';
    $html .= '</section>';
    return $html;
}
add_shortcode( 'fashionist_top_product_collection', 'fashionist_top_product_collection_function' );
}
/*------------- Fashionist Featured Product Slider ----------- */
if(! function_exists('fashionist_featured_product_slider') && fashionist_checkPlugin('woocommerce/woocommerce.php') && fashionist_checkPlugin('js_composer/js_composer.php')){
function fashionist_featured_product_slider() {
    vc_map(
        array(
            'name' => __( 'Fashionist Featured Product Slider' ,'fashionist'),
            'base' => 'fashionist_featured_product_slider',
            'category' => __( 'Fashionist' ,'fashionist'),
            'params' => array(
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Title' ,'fashionist'),
                    'param_name' => 'title',
                    'value' => __( 'Top' ,'fashionist'),
                    'description' => __( 'Add Title' ,'fashionist'),
                ),
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Display Product Limit' ,'fashionist'),
                    'param_name' => 'limit',
                    'value' => __( '2' ,'fashionist'),
                    'description' => __( 'Add Number Of Products' ,'fashionist'),
                ),
                array(
                    'type' => 'css_editor',
                    'heading' => __( 'CSS box', 'fashionist' ),
                    'param_name' => 'css',
                    'group' => __( 'Design Options', 'fashionist' ),
                ),
            )
        )
    );
}
add_action( 'vc_before_init', 'fashionist_featured_product_slider' );
function fashionist_featured_product_slider_function( $atts, $content ) {
    $atts = shortcode_atts(
        array(
            'limit' => __('2','fashionist'),
            'title' => __('Top','fashionist'),
            'css' => __('css','fashionist'),
        ), $atts, 'fashionist_featured_product_slider'
    );

    $html = '<section id="featured-product-wslider">';
        $html .= '<div class="container">';
            $html .= '<div class="row">';
                $html .= '<div class="col-xs-12">';
                    $html .= '<h2>'.$atts['title'].'</h2>';
                $html .= '</div>';
                $html .= '<div id="ps">';

                    $args =
                    array(
                        'post_type'      => 'product',
                        'posts_per_page' => $atts['limit'],
                        'meta_query' => array(
                            array(
                                'key' => '_visibility',
                                'value' => array('catalog', 'visible'),
                                'compare' => 'IN'
                            ),
                            array(
                                'key' => '_featured',
                                'value' => 'yes'
                            )
                        )
                    );
                    $html .= wp_reset_query();
                    $loop = new WP_Query( $args );
                    while ( $loop->have_posts() ) : $loop->the_post();
                        global $product;
                        $url = wp_get_attachment_url( get_post_thumbnail_id($loop->ID),'large');
                        $html .= '<div class="slide">';
                            $html .= '<div class="col-xs-12 col-md-7">';
                                $html .= '<div class="ps-textbox">';
                                    $html .= '<div class="share hidden-xs">';
                                        $html .= '<a href="https://www.facebook.com/sharer.php?u='.get_permalink().'" target="_blank"><span class="icon-facebook"></span></a>';
                                        $html .= '<a href="https://twitter.com/share?url='.get_permalink().'" target="_blank"><span class="icon-twitter"></span></a>';
                                        $html .= '<a href="https://pinterest.com/pin/create/button/?url='.get_permalink().'&media='.$url.'&description='.urlencode(get_the_title()).'"><span class="icon-pinterest"></span></a>';
                                        $html .= '<span class="icon-rss"></span>';
                                    $html .= '</div>';

                                    $html .= '<span class="ps-product-name">'.get_the_title().'</span>';
                                    $html .= '<span class="ps-product-price">'.$product->get_price_html().'</span>';
                                    $html .= '<p class="ps-product-desc">'.excerpt(50).'</p>';

                                    $html .= '<a class="ps-shopnow" href="'.get_permalink().'">Shop now</a>';
                                $html .= '</div>';
                            $html .= '</div>';

                            $html .= '<div class="col-md-5">';
                                $html .= '<div class="ps-imagebox">';
                                    $html .= '<img class="ps_image active" src="'.$url.'" alt="" />';
                                $html .= '</div>';
                            $html .= '</div>';
                        $html .= '</div>';
                    endwhile;
                    wp_reset_query();

                $html .= '</div>';
            $html .= '</div>';
        $html .= '</div>';
    $html .= '</section>';
    $html .= '<script> jQuery(function($) { $("#ps").sss({ slideShow : true, showNav : true }); }); </script>';

    return $html;
}
add_shortcode( 'fashionist_featured_product_slider', 'fashionist_featured_product_slider_function' );
}
/*------------- Fashionist Latest Blogs ----------- */
if( !function_exists('fashionist_latest_blogs')  && fashionist_checkPlugin('js_composer/js_composer.php')){
function fashionist_latest_blogs() {
    vc_map(
        array(
            'name' => __( 'Fashionist Latest Blogs' ,'fashionist'),
            'base' => 'fashionist_latest_blogs',
            'category' => __( 'Fashionist' ,'fashionist'),
            'params' => array(
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Title' ,'fashionist'),
                    'param_name' => 'title',
                    'value' => __( 'Latest' ,'fashionist'),
                    'description' => __( 'Add Title' ,'fashionist'),
                ),
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Display Blog Limit' ,'fashionist'),
                    'param_name' => 'limit',
                    'value' => __( '3' ,'fashionist'),
                    'description' => __( 'Add Number Of Blogs' ,'fashionist'),
                ),
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Blog Category Slug' ,'fashionist'),
                    'param_name' => 'blog_cat',
                    'value' => __( '1','fashionist' ),
                    'description' => __( 'Add Blog Category Slug' ,'fashionist'),
                ),
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Link For view all posts' ,'fashionist'),
                    'param_name' => 'link',
                    'value' => __( '#' ,'fashionist'),
                    'description' => __( 'Add Url of Detail page' ,'fashionist'),
                ),
                array(
                    'type' => 'css_editor',
                    'heading' => __( 'CSS box', 'fashionist' ),
                    'param_name' => 'css',
                    'group' => __( 'Design Options', 'fashionist' ),
                ),
            )
        )
    );
}
add_action( 'vc_before_init', 'fashionist_latest_blogs' );

function fashionist_latest_blogs_function( $atts, $content ) {
    $atts = shortcode_atts(
        array(
            'limit' => __( '3' ,'fashionist'),
            'title' => __('Latest','fashionist'),
            'blog_cat' => __('','fashionist'),
            'link' => __('#','fashionist'),
            'css' => __('css','fashionist'),
        ), $atts, 'fashionist_latest_blogs'
    );

    $args = array (
        'post_type'              => array( 'post' ),
        'post_status'            => array( 'publish' ),
        'posts_per_page'         => $atts['limit'],
        'category_name' => $atts['blog_cat'],
    );


    $html ='<section id="our-journal">';
        $html .='<div style="text-align: center;">';
            $html .='<h2 class="journal">'.$atts['title'].'</h2>';
        $html .='</div>';

        $html .='<div class="container">';
            $html .='<div class="row">';
                 $query = new WP_Query( $args );
                if ( $query->have_posts() ) {
                    while ( $query->have_posts() ) {
                        $query->the_post();
                        $image_url = wp_get_attachment_image_src( get_post_thumbnail_id() ,'larg');
                        $html .='<div class="col-xs-12 col-md-4">';
                            $html .='<div class="article" style="background-image: url('.$image_url[0].');">';
                                $html .='<div class="details">';
                                    $html .='<div class="triangle-separator"></div>';
                                    $html .='<p class="short-description">'.excerpt(15).'</p>';
                                    $html .='<a href="'.get_the_permalink().'" class="readmore">Lire la suite ...</a>';
                                $html .='</div>';

                                $html .='<span class="title">'.get_the_title().'</span>';
                                $html .='<div class="separator"></div>';
                                $html .='<span class="tagged"><i>tagged in</i>'.get_the_tag_list(' ',', ','').'</span>';
                            $html .='</div>';
                        $html .='</div>';
                    }
                } else {
                    $html .= 'no Blogs found';
                }
                wp_reset_postdata();

                $html .='<a href="'.$atts['link'].'" class="view-all">View all posts</a>';

            $html .='</div>';
        $html .='</div>';
    $html .='</section>';


    return $html;
}
add_shortcode( 'fashionist_latest_blogs', 'fashionist_latest_blogs_function' );
}

/*-------------- Call To Action Buttons ------------*/
if( !function_exists('fashionist_call_to_action1')  && fashionist_checkPlugin('js_composer/js_composer.php')){
function fashionist_call_to_action1() {
    vc_map(
        array(
            'name' => __( 'Fashionist Call To Action 1' ,'fashionist'),
            'base' => 'fashionist_call_to_action1',
            'category' => __( 'Fashionist' ,'fashionist'),
            'params' => array(
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Top Title' ,'fashionist'),
                    'param_name' => 'top_title',
                    'value' => __( 'Top Title' ,'fashionist'),
                    'description' => __( 'Top Title' ,'fashionist'),
                ),
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Main Title','fashionist'),
                    'param_name' => 'main_title',
                    'value' => __( 'Main Title' ,'fashionist'),
                    'description' => __( 'Main Title' ,'fashionist'),
                ),
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Description' ,'fashionist'),
                    'param_name' => 'desc',
                    'value' => __( 'Description' ,'fashionist'),
                ),
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Button Text' ,'fashionist'),
                    'param_name' => 'button_title',
                    'value' => __( 'Send' ,'fashionist'),
                ),
                 array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Button Url' ,'fashionist'),
                    'param_name' => 'button_url',
                    'value' => __( 'http://www.example.com' ,'fashionist'),
                ),
                array(
                    'type' => 'dropdown',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Set Style' ,'fashionist'),
                    'param_name' => 'style',
                    'value' => array(
                        __( 'With background', 'fashionist' ) => 'style1',
                        __( 'Without background', 'fashionist' ) => 'style2',
                        ),
                    'description' => __( 'Add category slug.' ,'fashionist'),
                ),
                array(
                    'type' => 'css_editor',
                    'heading' => __( 'Left box', 'fashionist' ),
                    'param_name' => 'css_left',
                    'group' => __( 'Left box', 'fashionist' ),
                ),
                array(
                    'type' => 'css_editor',
                    'heading' => __( 'Right box', 'fashionist' ),
                    'param_name' => 'css_right',
                    'group' => __( 'Right box', 'fashionist' ),
                ),
            )
        )
    );
}
add_action( 'vc_before_init', 'fashionist_call_to_action1' );

/**
* Function for displaying Title functionality
*/
function fashionist_call_to_action1_function( $atts, $content ) {
    $atts = shortcode_atts(
        array(
            'top_title' => __( 'This is the custom shortcode' ,'fashionist'),
            'main_title' => __('This is the main title demo','fashionist'),
            'desc' => __('This is the demo Description','fashionist'),
            'button_title' => __('Submit','fashionist'),'fashionist',
            'button_url' => 'http://www.example.com',
            'style' => __('','fashionist'),
            'css_left' => __('css_left','fashionist'),
            'css_right' => __('css_right','fashionist'),
        ), $atts, 'fashionist_call_to_action1'
    );

    if($atts['style'] != 'style2'){
        $html = '<section id="bigimgs">';
            $html .= '<div class="container-fluid">';
                $html .= '<div class="row">';
                    $html .= '<div class="col-lg-5 hidden-xs hidden-sm hidden-md">';
                        $html .= '<div class="row">';
                            $html .= '<div class="img '.vc_shortcode_custom_css_class( $atts['css_left'] ).'"></div>';
                        $html .= '</div>';
                    $html .= '</div>';

                    $html .= '<div class="col-xs-12 col-lg-4">';
                        $html .= '<div class="row">';
                            $html .= '<div class="banner">';
                                $html .= '<h2 class="title">'. $atts['top_title'].'</h2>';
                                $html .= '<span class="subtitle">'.$atts['main_title'].'</span>';
                                $html .= '<p class="description">'.$atts['desc'].'</p>';
                                $html .= '<a href="'.$atts['button_url'].'" class="button">'. $atts['button_title'].'</a>';
                            $html .= '</div>';
                        $html .= '</div>';
                    $html .= '</div>';

                    $html .= '<div class="col-lg-3 hidden-xs hidden-sm hidden-md">';
                        $html .= '<div class="row">';
                            $html .= '<div class="img '.vc_shortcode_custom_css_class( $atts['css_right'] ).'"></div>';
                        $html .= '</div>';
                    $html .= '</div>';

                $html .= '</div>';
            $html .= '</div>';
        $html .= '</section>';
    }else{
        $html = '<div class="box nopadding fashionist_callBox">';
            $html .= '<h2>'. $atts['top_title'].'</h2>';
            $html .= '<span class="subtitle">'.$atts['main_title'].'</span>';
            $html .= '<p>'.$atts['desc'].'</p>';
            $html .= '<a href="'.$atts['button_url'].'" class="start-shopping">'.$atts['button_title'].'</a>';
        $html .= '</div>';
    }
    return $html;
}
add_shortcode( 'fashionist_call_to_action1', 'fashionist_call_to_action1_function' );
}
// Call To action 2
if( !function_exists('fashionist_call_to_action2')  && fashionist_checkPlugin('js_composer/js_composer.php')){
function fashionist_call_to_action2() {
    vc_map(
        array(
            'name' => __( 'Fashionist Call To Action 2' ,'fashionist'),
            'base' => 'fashionist_call_to_action2',
            'category' => __( 'Fashionist' ,'fashionist'),
            'params' => array(
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Top Title' ,'fashionist'),
                    'param_name' => 'top_title',
                    'value' => __( 'Top Title' ,'fashionist'),
                    'description' => __( 'Top Title' ,'fashionist'),
                ),
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Main Title' ,'fashionist'),
                    'param_name' => 'main_title',
                    'value' => __( 'Main Title' ,'fashionist'),
                    'description' => __( 'Main Title' ,'fashionist'),
                ),
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Button Text' ,'fashionist'),
                    'param_name' => 'button_title',
                    'value' => __( 'Send' ,'fashionist'),
                ),
                 array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Button Url' ,'fashionist'),
                    'param_name' => 'button_url',
                    'value' => __( 'http://www.example.com' ,'fashionist'),
                ),
                array(
                    'type' => 'dropdown',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Set align' ,'fashionist'),
                    'param_name' => 'align',
                    'value' => array(
                        __( 'Left', 'fashionist' ) => 'left',
                        __( 'Right', 'fashionist' ) => 'right',
                        __( 'Center', 'fashionist' ) => 'center',
                        ),
                    'description' => __( 'Select display style.' ,'fashionist'),
                ),
                array(
                    'type' => 'dropdown',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Set Color' ,'fashionist'),
                    'param_name' => 'color',
                    'value' => array(
                        __( 'Dark', 'fashionist' ) => 'dark',
                        __( 'Light', 'fashionist' ) => 'light',
                        ),
                    'description' => __( 'Select background style.' ,'fashionist'),
                ),
                array(
                    "type" => "attach_image",
                    "heading" => __("Image", "fashionist"),
                    "holder" => "div",
                    "class" => "",
                    "param_name" => "image_url",
                    "description" => __("Your desc", "fashionist")
                ),
                array(
                    'type' => 'dropdown',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Show button','fashionist' ),
                    'param_name' => 'dbutton',
                    'value' => array(
                        __( 'Show', 'fashionist' ) => 'show',
                        __( 'Hide', 'fashionist' ) => 'hide',
                        ),
                    'description' => __( 'Select background style.' ,'fashionist'),
                ),
                array(
                    'type' => 'css_editor',
                    'heading' => __( 'CSS box', 'fashionist' ),
                    'param_name' => 'css',
                    'group' => __( 'Design Options', 'fashionist' ),
                ),
            )
        )
    );
}
add_action( 'vc_before_init', 'fashionist_call_to_action2' );
}
/**
* Function for displaying Title functionality
*/
if( !function_exists('fashionist_call_to_action2_function')  && fashionist_checkPlugin('js_composer/js_composer.php')){
function fashionist_call_to_action2_function( $atts, $content ) {
    $atts = shortcode_atts(
        array(
            'top_title' => __( 'This is the custom shortcode' ,'fashionist'),
            'main_title' => __('This is the main title demo','fashionist'),
            'desc' => __('This is the demo Description','fashionist'),
            'button_title' => __('Submit','fashionist'),
            'button_url' => 'http://www.example.com',
            'image_url' => __('','fashionist'),
            'align' => __('left','fashionist'),
            'color' => __('dark','fashionist'),
            'dbutton' => __('show','fashionist'),
            'css' => __('css','fashionist'),
        ), $atts, 'fashionist_call_to_action2'
    );

    $html = '<div class="box fashionist_callBox blue text-'.$atts['color'].' '.vc_shortcode_custom_css_class( $atts['css'] ).'">';
        if($atts['image_url'])
        {
            $img = wp_get_attachment_image_src($atts['image_url'], "large");
            $imgSrc = $img[0];
            $imgClass = ($atts['align'] == 'left' ? 'right' : 'left');
            $html .= '<img src="'.$imgSrc.'" alt="" class="'.$imgClass.'" />';
        }
        $html .= '<div class="text-'.$atts['align'].'">';
            $html .= '<h2>'.$atts['top_title'].'</h2>';
            $html .= '<div class="separator-'.$atts['align'].'"></div>';
            $html .= '<span class="subtitle">'.$atts['main_title'].'</span>';
            if($atts['dbutton'] != 'hide')
            {
                $html .= '<a href="'.$atts['button_url'].'" class="view '.$atts['color'].'" style="float:'.$atts['align'].'">'.$atts['button_title'].'</a>';
            }
        $html .= '</div>';
    $html .= '</div>';

    return $html;
}
add_shortcode( 'fashionist_call_to_action2', 'fashionist_call_to_action2_function' );
}
/*------------- Fashionist Progress Bar ----------- */

if(! function_exists('fashionist_progress_bar') && fashionist_checkPlugin('js_composer/js_composer.php')){
function fashionist_progress_bar() {
    vc_map(
        array(
            'name' => __( 'Fashionist Progress Bar' ,'fashionist'),
            'base' => 'fashionist_progress_bar',
            'category' => __( 'Fashionist' ,'fashionist'),
            'params' => array(
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Title' ,'fashionist'),
                    'param_name' => 'title',
                    'value' => __( 'Top' ,'fashionist'),
                    'description' => __( 'Add Title' ,'fashionist'),
                ),
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Sub Title' ,'fashionist'),
                    'param_name' => 'subtitle',
                    'value' => __( 'Top' ,'fashionist'),
                    'description' => __( 'Add Title' ,'fashionist'),
                ),
                array(
                    'type' => 'param_group',
                    'heading' => __( 'Values', 'fashionist' ),
                    'param_name' => 'values',
                    'description' => __( 'Enter values for graph - value, title and color.', 'fashionist' ),
                    'params' => array(
                        array(
                            'type' => 'textfield',
                            'heading' => __( 'Label', 'fashionist' ),
                            'param_name' => 'label',
                            'description' => __( 'Enter text used as title of bar.', 'fashionist' ),
                            'admin_label' => true,
                        ),
                        array(
                            'type' => 'textarea',
                            'holder' => 'div',
                            'class' => '',
                            'heading' => __( 'Description' ,'fashionist'),
                            'param_name' => 'content',
                            "value" => __( "<p>I am test text block. Click edit button to change this text.</p>", "fashionist" ),
                            "description" => __( "Add Address & Opening Hours Detail" ,'fashionist')
                        ),
                        array(
                            'type' => 'textfield',
                            'heading' => __( 'Value', 'fashionist' ),
                            'param_name' => 'value',
                            'description' => __( 'Enter value of bar.', 'fashionist' ),
                            'admin_label' => true,
                        ),
                    ),
                ),
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Button Text','fashionist' ),
                    'param_name' => 'button_title',
                    'value' => __( 'Send','fashionist' ),
                ),
                 array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Button Url' ,'fashionist'),
                    'param_name' => 'button_url',
                    'value' => __( 'http://www.example.com','fashionist' ),
                ),
                array(
                    'type' => 'css_editor',
                    'heading' => __( 'CSS box', 'fashionist' ),
                    'param_name' => 'css',
                    'group' => __( 'Design Options', 'fashionist' ),
                ),
            )
        )
    );
}
add_action( 'vc_before_init', 'fashionist_progress_bar' );
function fashionist_progress_bar_function( $atts, $content ) {
    $atts = shortcode_atts(
        array(
            'title' => __('Top1','fashionist'),
            'subtitle' => __('Top2','fashionist'),
            'values' => array('values','fashionist'),
            'button_title' => __('send','fashionist'),
            'button_url' => __('http://www.example.com','fashionist'),
            'content' => __('content','fashionist'),
            'css' => __('css','fashionist'),
        ), $atts, 'fashionist_progress_bar'
    );

    $temp_values = vc_param_group_parse_atts( $atts['values']);

    $html = '<section id="our-skills">';
        $html .= '<div class="container-fluid ourskills">';
                $html .= '<div class="row">';
                    $html .= '<div class="container">';
                        $html .= '<div class="row">';
                            $html .= '<div class="col-xs-12">';
                                $html .= '<div style="text-align: center;">';
                                    $html .= '<h2>'.$atts['title'].'</h2>';
                                    $html .= '<p class="subtitle">'.$atts['subtitle'].'</p>';
                                $html .= '</div>';
                            $html .= '</div>';
                            foreach ($temp_values as $value)
                            {
                                $label = ( isset($value['label']) ) ? $value['label'] : '';
                                $content = ( isset($value['content']) ) ? $value['content'] : '';
                                $val = ( isset($value['value']) ) ? $value['value'] : '0' ;

                                $html .= '<div class="col-xs-12 col-md-6">';
                                    $html .= '<div class="feature">';
                                        $html .= '<span class="feature-name">'.$label.'</span>';
                                        $html .= '<p class="feature-description">'.$content.'</p>';
                                        $html .= '<div class="percentage" style="width: '.$val.'%;"><div class="text"><span>'.$val.'%</span></div></div>';
                                    $html .= '</div>';
                                $html .= '</div>';
                            }
                    $html .= '</div>';
                    $html .= '<a href="'.$atts['button_url'].'" class="button">'.$atts['button_title'].'</a>';
                $html .= '</div>';
            $html .= '</div>';
        $html .= '</section>';

    return $html;
}
add_shortcode( 'fashionist_progress_bar', 'fashionist_progress_bar_function' );
}
/*-------------- Fashionist Info Box ------------*/
if( !function_exists('fashionist_info_box')  && fashionist_checkPlugin('js_composer/js_composer.php')){
function fashionist_info_box() {
    vc_map(
        array(
            'name' => __( 'Fashionist Information Box' ,'fashionist'),
            'base' => 'fashionist_info_box',
            'category' => __( 'Fashionist' ,'fashionist'),
            'params' => array(
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Icon class' ,'fashionist'),
                    'param_name' => 'icon',
                    'value' => __('phone','fashionist'),
                    'description' => __( 'Add Font Awesome class','fashionist' ),
                ),
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Team Title' ,'fashionist'),
                    'param_name' => 'title',
                    'value' => __( 'Fashionist Team','fashionist' )
                ),
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Description' ,'fashionist'),
                    'param_name' => 'sub_title',
                    'value' => __( 'Meet Fashionist Team','fashionist' )
                ),
                array(
                    'type' => 'css_editor',
                    'heading' => __( 'CSS box', 'fashionist' ),
                    'param_name' => 'css',
                    'group' => __( 'Design Options', 'fashionist' ),
                ),
            )
        )
    );
}
add_action( 'vc_before_init', 'fashionist_info_box' );
function fashionist_info_box_function( $atts, $content ) {
    $atts = shortcode_atts(
        array(
            'title' => __('title','fashionist'),
            'sub_title' => __('sub_title','fashionist'),
            'icon' => __('phone','fashionist'),
            'css' => __('css','fashionist'),
        ), $atts, 'fashionist_info_box'
    );

    $temp_class = (vc_shortcode_custom_css_class( $atts['css'] ))? 'brown' : '';

    $html = '<div class="box fashionist_info_box '.vc_shortcode_custom_css_class( $atts['css'] ).' '.$temp_class.'">';
        $html .= '<span class="bigicon icon-'.$atts['icon'].'"></span>';
        $html .= '<div class="contact">';
            $html .= '<span class="contact-type">'.$atts['title'].'</span>';
            $html .= '<span class="contact-details">'.$atts['sub_title'].'</span>';
        $html .= '</div>';
    $html .= '</div>';

    return $html;
}
add_shortcode( 'fashionist_info_box', 'fashionist_info_box_function' );
}
/*------------- Fashionist Fancy Box ----------- */
if( !function_exists('fashionist_fancy_box')  && fashionist_checkPlugin('js_composer/js_composer.php')){
function fashionist_fancy_box() {
    // Title
    vc_map(
        array(
            'name' => __( 'Fashionist Fancy Box' ,'fashionist'),
            'base' => 'fashionist_fancy_box',
            'category' => __( 'Fashionist' ,'fashionist'),
            'params' => array(
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Title' ,'fashionist'),
                    'param_name' => 'title',
                    'value' => __( 'History' ,'fashionist'),
                    'description' => __( 'Add Title' ,'fashionist'),
                ),
                array(
                    'type' => 'textarea_html',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Description' ,'fashionist'),
                    'param_name' => 'content',
                    "value" => __( "<p>I am test text block. Click edit button to change this text.</p>", "fashionist" ),
                    "description" => __( "Enter your content.", "fashionist" )
                ),
                array(
                    "type" => "attach_image",
                    "heading" => __("Image", "fashionist"),
                    "holder" => "div",
                    "class" => "",
                    "param_name" => "image_url",
                    "description" => __("Your desc", "fashionist")
                ),
                array(
                    'type' => 'dropdown',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Set Style' ,'fashionist'),
                    'param_name' => 'style',
                    'value' => array(
                        __( 'Full', 'fashionist' ) => 'full',
                        __( 'Half', 'fashionist' ) => 'half',
                        ),
                    'description' => __( 'Add category slug.' ,'fashionist'),
                ),
                array(
                    'type' => 'css_editor',
                    'heading' => __( 'CSS box', 'fashionist' ),
                    'param_name' => 'css',
                    'group' => __( 'Design Options', 'fashionist' ),
                ),
            )
        )
    );
}
add_action( 'vc_before_init', 'fashionist_fancy_box' );
function fashionist_fancy_box_function( $atts, $content ) {
    $atts = shortcode_atts(
        array(
            'title' => __( 'History' ,'fashionist'),
            'content' => wpb_js_remove_wpautop($content, true),
            'style' => __('full','fashionist'),
            'image_url' => __('','fashionist'),
            'css' => __('css','fashionist'),
        ), $atts, 'fashionist_fancy_box'
    );

    $img = wp_get_attachment_image_src($atts['image_url'], "large");
    $imgSrc = $img[0];

    $html = '<section id="our-story" class="fashionist_fancy_box '.vc_shortcode_custom_css_class( $atts['css'] ).'">';
        $html .= '<div class="container">';
            $html .= '<div class="row">';
                if($atts['style'] != 'half')
                {
                    $html .= '<div class="col-xs-12">';
                        $html .= '<h2 class="about-us-title">'.$atts['title'].'</h2>';
                        $html .= '<p class="ourstory">'.wpb_js_remove_wpautop($content, true).'</p>';
                    $html .= '</div>';

                    $html .= '<div class="col-xs-12">';
                        $html .= '<div class="about-us-img fullw" style="background: url('.$imgSrc.');"></div>';
                    $html .= '</div>';
                }else {
                    $html .= '<div class="col-lg-5">';
                        $html .= '<h2 class="about-us-title">'.$atts['title'].'</h2>';
                        $html .= '<p class="ourstory">'.wpb_js_remove_wpautop($content, true).'</p>';
                    $html .= '</div>';

                    $html .= '<div class="col-lg-7">';
                        $html .= '<img class="about-us-img" src="'.$imgSrc.'" />';
                    $html .= '</div>';
                }
            $html .= '</div>';
        $html .= '</div>';
    $html .= '</section>';
    return $html;
}
add_shortcode( 'fashionist_fancy_box', 'fashionist_fancy_box_function' );
}

/*------------- Fashionist Fancy Box 2 ----------- */
if( !function_exists('fashionist_fancy_box_2')  && fashionist_checkPlugin('js_composer/js_composer.php')){
function fashionist_fancy_box_2() {
    // Title
    vc_map(
        array(
            'name' => __( 'Fashionist Fancy Box 2' ,'fashionist'),
            'base' => 'fashionist_fancy_box_2',
            'category' => __( 'Fashionist' ,'fashionist'),
            'params' => array(
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Title' ,'fashionist'),
                    'param_name' => 'title',
                    'value' => __( 'History' ,'fashionist'),
                    'description' => __( 'Add Title' ,'fashionist'),
                ),
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Sub Title' ,'fashionist'),
                    'param_name' => 'sub_title',
                    'value' => __( 'History' ,'fashionist'),
                    'description' => __( 'Add Title' ,'fashionist'),
                ),
                array(
                    "type" => "attach_image",
                    "heading" => __("Image", "fashionist"),
                    "holder" => "div",
                    "class" => "",
                    "param_name" => "image_url",
                    "description" => __("Your desc", "fashionist")
                ),
                array(
                    'type' => 'css_editor',
                    'heading' => __( 'CSS box', 'fashionist' ),
                    'param_name' => 'css',
                    'group' => __( 'Design Options', 'fashionist' ),
                ),
            )
        )
    );
}
add_action( 'vc_before_init', 'fashionist_fancy_box_2' );
function fashionist_fancy_box_2_function( $atts, $content ) {
    $atts = shortcode_atts(
        array(
            'title' => __('','fashionist'),
            'sub_title' => __('','fashionist'),
            'image_url' => __('','fashionist'),
            'css' => __('css','fashionist'),
        ), $atts, 'fashionist_fancy_box_2'
    );

    $img = wp_get_attachment_image_src($atts['image_url'], "large");
    $imgSrc = $img[0];

    $html = '<div class="shop-grid"><div class="cover '.vc_shortcode_custom_css_class( $atts['css'] ).'">';
        $html .= '<img src="'.$imgSrc.'" style="bottom: -30%;" alt=""/>';
        $html .= '<div class="col-xs-12 col-sm-4 col-md-5 col-lg-4 cover-title">';
            $html .= '<span class="main">'.$atts['title'].'</span>';
            $html .= '<span class="sub">'.$atts['sub_title'].'</span>';
        $html .= '</div>';
    $html .= '</div></div>';
    return $html;
}
add_shortcode( 'fashionist_fancy_box_2', 'fashionist_fancy_box_2_function' );
}
/*------------- Fashionist News Letter ----------- */
if( !function_exists('fashionist_news_letter')  && fashionist_checkPlugin('js_composer/js_composer.php')){
function fashionist_news_letter() {
    // Title
    vc_map(
        array(
            'name' => __( 'Fashionist News Letter' ,'fashionist'),
            'base' => 'fashionist_news_letter',
            'category' => __( 'Fashionist' ,'fashionist'),
            'params' => array(
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Title' ,'fashionist'),
                    'param_name' => 'title',
                    'value' => __( 'History' ,'fashionist'),
                    'description' => __( 'Add Title' ,'fashionist'),
                ),
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Shortcode' ,'fashionist'),
                    'param_name' => 'shortcode',
                    'value' => __( '' ,'fashionist'),
                    'description' => __( 'Add Mailchimp Form id','fashionist' ),
                ),
                array(
                    'type' => 'colorpicker',
                    'heading' => __( 'News Letter text color', 'fashionist' ),
                    'param_name' => 'customtxtcolor',
                    'description' => __( 'Select custom text color for bars.', 'fashionist' ),
                    'value' => '#FF0000', //Default Red color
                ),
                array(
                    'type' => 'colorpicker',
                    'heading' => __( 'News Letter text box background color', 'fashionist' ),
                    'param_name' => 'boxcolor',
                    'description' => __( 'Select custom text color for bars.', 'fashionist' ),
                    'value' => '#E5F1F5', //Default Red color
                ),
                array(
                    'type' => 'css_editor',
                    'heading' => __( 'CSS box', 'fashionist' ),
                    'param_name' => 'css',
                    'group' => __( 'Design Options', 'fashionist' ),
                ),
            )
        )
    );
}
add_action( 'vc_before_init', 'fashionist_news_letter' );

function fashionist_news_letter_function( $atts, $content ) {
    $atts = shortcode_atts(
        array(
            'title' => __( 'History' ,'fashionist'),
            'shortcode' => __('','fashionist'),
            'customtxtcolor' => __('','fashionist'),
            'boxcolor' => __('#E5F1F5','fashionist'),
            'css' => __('css','fashionist'),
        ), $atts, 'fashionist_news_letter'
    );

    $html = '<style type="text/css">#newsletter.pink::before{color:'.$atts['customtxtcolor'].' !important;} #newsletter.pink .newsletter-form form input[type="email"]{ background: '.$atts['boxcolor'].' !important;}</style><div id="newsletter" class="pink '.vc_shortcode_custom_css_class( $atts['css'] ).'">';
        $html .= '<div class="container-fluid">';
            $html .= '<div class="row">';
                $html .= '<div class="container">';
                    $html .= '<div class="row">';
                        $html .= '<div class="newsletter-form">';
                            $html .= '<div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">';
                                $html .= '<div class="row">';
                                    $html .= '<span class="newsletter-title">'.$atts['title'].'</span>';
                                $html .= '</div>';
                            $html .= '</div>';

                            $html .= '<div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">';
                                $html .= '<div class="row">';
                                    $html .= do_shortcode('[mc4wp_form id="'.$atts['shortcode'].'"]');
                                $html .= '</div>';
                            $html .= '</div>';

                        $html .= '</div>';
                    $html .= '</div>';
                $html .= '</div>';
            $html .= '</div>';
        $html .= '</div>';
    $html .= '</div>';

    return $html;
}
add_shortcode( 'fashionist_news_letter', 'fashionist_news_letter_function' );

}
/*-------------- Fashionist Team ------------*/
if( !function_exists('fashionist_team')  && fashionist_checkPlugin('js_composer/js_composer.php')){
function fashionist_team() {
    // Title
    vc_map(
        array(
            'name' => __( 'Fashionist Team' ,'fashionist'),
            'base' => 'fashionist_team',
            'category' => __( 'Fashionist' ,'fashionist'),
            'params' => array(
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'No of Team Member' ,'fashionist'),
                    'param_name' => 'no_client',
                    'value' => __( '-1' ,'fashionist'),
                    'description' => __( '-1 Display All Team Member' ,'fashionist'),
                ),
                array(
                    'type' => 'css_editor',
                    'heading' => __( 'CSS box', 'fashionist' ),
                    'param_name' => 'css',
                    'group' => __( 'Design Options', 'fashionist' ),
                ),
            )
        )
    );
}
add_action( 'vc_before_init', 'fashionist_team' );
function fashionist_team_function( $atts, $content ) {
    $atts = shortcode_atts(
        array(
            'no_client' => __(-1,'fashionist'),
            'css' => __('css','fashionist'),
        ), $atts, 'fashionist_team'
    );
    wp_reset_postdata();
    // WP_Query arguments
    $args = array (
        'post_type'              => array( 'team' ),
        'post_status'            => array( 'publish' ),
        'posts_per_page'         => $atts['no_client'],
    );

    $html = '<section id="our-team" class="about_our_team_area '.vc_shortcode_custom_css_class( $atts['css'] ).'">';
        $html .='<div class="container">';
            $html .='<div class="row">';

                $query = new WP_Query( $args );
                if ( $query->have_posts() )
                {
                    while ( $query->have_posts() )
                    {
                        $query->the_post();
                        $html .='<div class="col-xs-12 col-sm-6 col-md-3">';
                            $html .='<div class="member">';
                                $html .='<div class="member-img">';
                                    $html .='<img src="'.get_the_post_thumbnail_url().'" alt="" />';
                                    $html .='<div class="more"><span class="icon-plus"></span></div>';
                                $html .='</div>';

                                $html .='<div class="member-info">';
                                    $html .='<span class="member-name">'.get_the_title().'</span>';
                                    $html .='<span class="member-role">'.get_post_meta(get_the_id(), 'team_designation', true).'</span>';
                                $html .='</div>';
                            $html .='</div>';
                        $html .='</div>';
                    }
                } else {
                    $html .= 'no Members found';
                }
                wp_reset_postdata();

            $html .='</div>';
        $html .='</div>';
    $html .='</section>';
    return $html;
}
add_shortcode( 'fashionist_team', 'fashionist_team_function' );
}
/*-------------- Fashionist Client ------------*/
if( !function_exists('fashionist_client')  && fashionist_checkPlugin('js_composer/js_composer.php')){
function fashionist_client() {
    // Title
    vc_map(
        array(
            'name' => __( 'Fashionist Client' ,'fashionist'),
            'base' => 'fashionist_client',
            'category' => __( 'Fashionist' ,'fashionist'),
            'params' => array(
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Title' ,'fashionist'),
                    'param_name' => 'title',
                    'value' => __( 'Title' ,'fashionist')
                ),
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'No of Client Member' ,'fashionist'),
                    'param_name' => 'no_client',
                    'value' => __( '3' ,'fashionist'),
                    'description' => __( '-1 Display All Client Member','fashionist' ),
                ),
                array(
                    'type' => 'css_editor',
                    'heading' => __( 'CSS box', 'fashionist' ),
                    'param_name' => 'css',
                    'group' => __( 'Design Options', 'fashionist' ),
                ),
            )
        )
    );
}
add_action( 'vc_before_init', 'fashionist_client' );
function fashionist_client_function( $atts, $content ) {
    $atts = shortcode_atts(
        array(
            'no_client' => __(3,'fashionist'),
            'title' => __('','fashionist'),
            'css' => __('css','fashionist'),
        ), $atts, 'fashionist_client'
    );
    wp_reset_postdata();
    // WP_Query arguments
    $args = array (
        'post_type'              => array( 'client' ),
        'post_status'            => array( 'publish' ),
        'posts_per_page'         => $atts['no_client'],
    );

    $html = '<section id="our-clients-say" class="'.vc_shortcode_custom_css_class( $atts['css'] ).'">';
        $html .='<div class="container">';
            $html .='<div class="row">';

                $html .='<div class="col-xs-12">';
                    $html .='<div style="text-align: center;">';
                        $html .='<h2>'.$atts['title'].'</h2>';
                    $html .='</div>';
                $html .='</div>';

                $query = new WP_Query( $args );
                if ( $query->have_posts() )
                {
                    while ( $query->have_posts() )
                    {
                        $query->the_post();
                        $html .='<div class="col-xs-12 col-sm-4">';
                            $html .='<div class="client-story">';
                                $html .='<img src="'.get_the_post_thumbnail_url().'" alt="" />';
                                $html .='<span class="client-name">'.get_the_title().'</span>';
                                $html .='<span class="client-job">'.get_post_meta(get_the_id(), 'client_designation', true).'</span>';
                                $html .= '<p> "'.get_the_content().'"</p>';
                            $html .='</div>';
                        $html .='</div>';
                    }
                } else {
                    $html .= 'no Members found';
                }
                wp_reset_postdata();

            $html .='</div>';
        $html .='</div>';
    $html .='</section>';
    return $html;
}
add_shortcode( 'fashionist_client', 'fashionist_client_function' );
}
/*------------- Fashionist Why us slider ----------- */

if( !function_exists('fashionist_why_us') && fashionist_checkPlugin('js_composer/js_composer.php')){
function fashionist_why_us() {
    vc_map(
        array(
            'name' => __( 'Fashionist Why us slider' ,'fashionist'),
            'base' => 'fashionist_why_us',
            'category' => __( 'Fashionist' ,'fashionist'),
            'params' => array(
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __( 'Title' ,'fashionist'),
                    'param_name' => 'title',
                    'value' => __( 'Top' ,'fashionist'),
                    'description' => __( 'Add Title' ,'fashionist'),
                ),
                array(
                    'type' => 'param_group',
                    'heading' => __( 'Values', 'fashionist' ),
                    'param_name' => 'values',
                    'description' => __( 'Enter values for graph - value, title and color.', 'fashionist' ),
                    'params' => array(
                        array(
                            "type" => "attach_image",
                            "heading" => __("Image", "fashionist"),
                            "holder" => "div",
                            "class" => "",
                            "param_name" => "image_url",
                            "description" => __("Your desc", "fashionist")
                        ),
                        array(
                            'type' => 'textarea',
                            'holder' => 'div',
                            'class' => '',
                            'heading' => __( 'Description' ,'fashionist'),
                            'param_name' => 'content',
                            "value" => __( "<p>I am test text block. Click edit button to change this text.</p>", "fashionist" ),
                            "description" => __( "Add Address & Opening Hours Detail" ,'fashionist')
                        ),
                    ),
                ),
                array(
                    'type' => 'css_editor',
                    'heading' => __( 'CSS box', 'fashionist' ),
                    'param_name' => 'css',
                    'group' => __( 'Design Options', 'fashionist' ),
                ),
            )
        )
    );
}
add_action( 'vc_before_init', 'fashionist_why_us' );
function fashionist_why_us_function( $atts, $content ) {
    $atts = shortcode_atts(
        array(
            'title' => __('Top1','fashionist'),
            'values' => array('values'),
            'css' => __('css','fashionist'),
        ), $atts, 'fashionist_why_us'
    );

    $temp_values = vc_param_group_parse_atts( $atts['values']);

    $html = '<section id="why-choose-us">';
        $html .= '<div class="container">';
            $html .= '<div class="row">';
                $html .= '<div id="wcu">';

                        foreach ($temp_values as $value)
                        {
                            $img = wp_get_attachment_image_src($value['image_url'], "large");
                            $imgSrc = $img[0];

                            $html .= '<div class="slide">';
                                $html .= '<div class="col-xs-12 col-md-8">';
                                    $html .= '<div class="wcus-imagebox">';
                                        $html .= '<img src="'.$imgSrc.'" alt="">';
                                    $html .= '</div>';
                                $html .= '</div>';

                                $html .= '<div class="col-xs-12 col-md-4">';
                                    $html .= '<div class="wcus-textbox">';
                                        $html .= '<h3>'.$atts['title'].'</h3>';
                                         $html .= $value['content'];

                                    $html .= '</div>';
                                $html .= '</div>';
                            $html .= '</div>';

                        }

                $html .= '</div>';
            $html .= '</div>';
        $html .= '</div>';
    $html .= '</section>';
    $html .='<script> jQuery(function() { jQuery("#wcu").sss({ slideShow : true, showNav : true }); }); </script>';

    return $html;
}
add_shortcode( 'fashionist_why_us', 'fashionist_why_us_function' );
}