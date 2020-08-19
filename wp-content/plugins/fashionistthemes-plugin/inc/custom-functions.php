<?php
/**
 * Recent_Posts widget class
 *
 * @since 1.0
 */

if( !function_exists( 'fashionist_checkPlugin' ) ) {
    function fashionist_checkPlugin($path = '')
    {
        if (strlen($path) == 0) return false;
        $_actived = apply_filters('active_plugins', get_option('active_plugins'));
        if (in_array(trim($path), $_actived)) return true;
        else return false;
    }
}

class WP_Widget_Recent_Posts_Images extends WP_Widget {

    function __construct() {
        $widget_ops = array('classname' => 'widget_recent_entries_with_images', 'description' => __( "The most recent posts on your site" ,'fashionist') );
        parent::__construct('recent-posts', __('Recent Posts With Images' ,'fashionist'), $widget_ops);
        $this->alt_option_name = 'widget_recent_entries_with_images';

        add_action( 'save_post', array($this, 'flush_widget_cache') );
        add_action( 'deleted_post', array($this, 'flush_widget_cache') );
        add_action( 'switch_theme', array($this, 'flush_widget_cache') );
    }

    function widget($args, $instance) {
        $cache = wp_cache_get('widget_recent_entries_with_images', 'widget');

        if ( !is_array($cache) )
            $cache = array();

        if ( ! isset( $args['widget_id'] ) )
            $args['widget_id'] = $this->id;

        if ( isset( $cache[ $args['widget_id'] ] ) ) {
            echo $cache[ $args['widget_id'] ];
            return;
        }

        ob_start();
        extract($args);

        $title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Recent Posts' ,'fashionist');
        $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
        $number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 10;
        if ( ! $number )
            $number = 10;
        $show_date = isset( $instance['show_date'] ) ? $instance['show_date'] : false;

        $r = new WP_Query( apply_filters( 'widget_posts_args', array( 'posts_per_page' => $number, 'no_found_rows' => true, 'post_status' => 'publish', 'ignore_sticky_posts' => true, 'tax_query' => array( 
                                            array(
                                                'taxonomy' => 'post_format',
                                                'field' => 'slug',
                                                'terms' => array('post-format-aside', 'post-format-image', 'post-format-video', 'post-format-quote', 'post-format-link'),
                                                'operator' => 'NOT IN'
                                            )
                                        ) ) ) );
        if ($r->have_posts()) :
?>
        <?php echo $before_widget; ?>
        <?php if ( $title ) echo $before_title . $title . $after_title; ?>
        <div class="recent-posts">        
        <?php while ( $r->have_posts() ) : $r->the_post(); ?>
	        <a href="<?php the_permalink() ?>">
	        	<div class="post">
                    <?php
                    if ( has_post_thumbnail() ) {
                    ?>
	        		<div class="img">
						<?php the_post_thumbnail( array(81, 82)); ?>
					</div>					
					<?php
                    }
                    ?>   
                    <div class="text">
						<span class="post-title"><?php echo get_the_title(); ?></span>
						<span class="desc"><?php echo excerpt(5); ?></span>					
					</div>
				</div>            
			</a>	
        <?php endwhile; ?>        
    	</div>
        <?php echo $after_widget; ?>
<?php
        // Reset the global $the_post as this query will have stomped on it
        wp_reset_postdata();

        endif;

        $cache[$args['widget_id']] = ob_get_flush();
        wp_cache_set('widget_recent_entries_with_images', $cache, 'widget');
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['number'] = (int) $new_instance['number'];
        $instance['show_date'] = (bool) $new_instance['show_date'];
        $this->flush_widget_cache();

        $alloptions = wp_cache_get( 'alloptions', 'options' );
        if ( isset($alloptions['widget_recent_entries']) )
            delete_option('widget_recent_entries');

        return $instance;
    }

    function flush_widget_cache() {
        wp_cache_delete('widget_recent_entries_with_images', 'widget');
    }

    function form( $instance ) {
        $title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        $number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
        //$show_date = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;
?>
        <p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ,'fashionist' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

        <p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show:' ,'fashionist'); ?></label>
        <input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>

<?php
    }
}
function Register_WP_Widget_Recent_Posts_Images() {
    register_widget( 'WP_Widget_Recent_Posts_Images' );
}
add_action( 'widgets_init', 'Register_WP_Widget_Recent_Posts_Images' );


// Creating the tag widget 
class fashionist_tag_widget_sidebar extends WP_Widget 
{
	function __construct() {
		parent::__construct(
		// Base ID of your widget
		'fashionist_tag_widget_sidebar', 

		// Widget name will appear in UI
		__('Fashionist Tag Widget', 'fashionist'), 

		// Widget description
		array( 'description' => __( 'Display List of Tags', 'fashionist' ), ) 
		);
	}

	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) 
	{
		$title = apply_filters( 'widget_title', $instance['title'] );
		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) )
		echo '<div class="title">'. $title .'</div>';
		$terms = get_tags();
		$term_array = array();
		$output = '<div class="popular-tags">';
		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
		    foreach ( $terms as $term ) {
		    	$tag_link = get_tag_link( $term->term_id );
		        $output .= '<a href="'.$tag_link.'" class="tag">'.$term->name.'</a>';
		    }
		}
		$output .= '</div>';		
		echo $output;
		echo $args['after_widget'];
	}
		
	// Widget Backend 
	public function form( $instance ) 
	{
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Fashionist title', 'fashionist' );
		}
		// Widget admin form
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ,'fashionist' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}
		
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) 
	{
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}
} // Class wpb_widget ends here

// Register and load the widget
function fashionist_tag_load_widget_sidebar() {
	register_widget( 'fashionist_tag_widget_sidebar' );
}
add_action( 'widgets_init', 'fashionist_tag_load_widget_sidebar' );

if (fashionist_checkPlugin('woocommerce/woocommerce.php') ) {
    // Creating the tag widget 
    class fashionist_tag_widget extends WP_Widget 
    {
    	function __construct() {
    		parent::__construct(
    		// Base ID of your widget
    		'fashionist_tag_widget', 

    		// Widget name will appear in UI
    		__('Fashionist Products Tag Widget', 'fashionist'), 

    		// Widget description
    		array( 'description' => __( 'Display List of Products Tags', 'fashionist' ), ) 
    		);
    	}

    	// Creating widget front-end
    	// This is where the action happens
    	public function widget( $args, $instance ) 
    	{
    		$title = apply_filters( 'widget_title', $instance['title'] );
    		// before and after widget arguments are defined by themes
    		echo $args['before_widget'];
    		if ( ! empty( $title ) )
    		echo $args['before_title'] . $title . $args['after_title'];
    		$terms = get_terms( 'product_tag' );
    		$term_array = array();
    		$output = '<ul class="tags">';
    		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
    		    foreach ( $terms as $term ) {
    		    	$tag_link = get_tag_link( $term->term_id );
    		        $output .= '<li><a href="'.$tag_link.'">'.$term->name.'</a></li>';
    		    }
    		}
    		$output .= '</ul>';		
    		echo $output;
    		echo $args['after_widget'];
    	}
    		
    	// Widget Backend 
    	public function form( $instance ) 
    	{
    		if ( isset( $instance[ 'title' ] ) ) {
    			$title = $instance[ 'title' ];
    		}
    		else {
    			$title = __( 'Fashionist title', 'fashionist' );
    		}
    		// Widget admin form
    		?>
    		<p>
    		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ,'fashionist' ); ?></label> 
    		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
    		</p>
    		<?php 
    	}
    		
    	// Updating widget replacing old instances with new
    	public function update( $new_instance, $old_instance ) 
    	{
    		$instance = array();
    		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
    		return $instance;
    	}
    } // Class wpb_widget ends here

    // Register and load the widget
    function fashionist_tag_load_widget() {
    	register_widget( 'fashionist_tag_widget' );
    }
    add_action( 'widgets_init', 'fashionist_tag_load_widget' );
}
if (fashionist_checkPlugin('woocommerce/woocommerce.php') ) {    
    // Creating the Popular Products widget 
    class fashionist_popular_products_widget extends WP_Widget 
    {
        function __construct() {
            parent::__construct(
            // Base ID of your widget
            'fashionist_popular_products_widget', 

            // Widget name will appear in UI
            __('Fashionist Popular Products Widget', 'fashionist'), 

            // Widget description
            array( 'description' => __( 'Display List of Popular Products', 'fashionist' ), ) 
            );
        }

        // Creating widget front-end
        // This is where the action happens
        public function widget( $args, $instance ) 
        {
            $title = apply_filters( 'widget_title', $instance['title'] );
            $number = apply_filters( 'widget_number', $instance['number'] );
            // before and after widget arguments are defined by themes
            echo $args['before_widget'];
            
            if ( ! empty( $title ) )
            echo '<div class="title">'. $title . '</div>';

            $arg = array(
    		'post_type' => 'product',
    		'posts_per_page' => $number,
    		'meta_key' => 'total_sales',
    		'orderby' => 'meta_value_num',
    		);
            $output = '';
            $output .= '<div class="related">';
    		$loop = new WP_Query( $arg);
    		if ( $loop->have_posts() ) {
    			while ( $loop->have_posts() ) : $loop->the_post();
    				global $product; 
    				$url = wp_get_attachment_url( get_post_thumbnail_id($loop->ID,'thumbnail') );
    				
    					$output .= '<div class="item">';
    						$output .= '<img class="img-responsive item-img" alt="Single product" src="'.$url.'">';
    						$output .= '<div class="item-details">';
    							$output .= '<span class="item-name">'.get_the_title().'</span>';
    							$output .= '<span class="item-price">'.$product->get_price_html().'</span>';
    							$output .= '<a rel="nofollow" href="'.site_url().'/?add-to-cart='.get_the_id().'" data-quantity="1" data-product_id="'.get_the_id().'" data-product_sku="" class="action add_to_cart_button ajax_add_to_cart added btn">Add to cart</a>';
    						$output .= '</div>';
    					$output .= '</div>';				

    			endwhile;
    			$output .= '</div>	';
    		} else {
    			echo __( 'No products found' ,'fashionist');
    		}
    		echo $output;
            echo $args['after_widget'];
            wp_reset_query();
        }
            
        // Widget Backend 
        public function form( $instance ) 
        {
            if ( isset( $instance[ 'title' ] ) ) {
                $title = $instance[ 'title' ];
            }
            else {
                $title = __( 'Fashionist title', 'fashionist' );
            }
            if ( isset( $instance[ 'number' ] ) ) {
                $number = $instance[ 'number' ];
            }
            else {
                $number = __( '2', 'fashionist' );
            }
            // Widget admin form
            ?>
            <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ,'fashionist' ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            </p>
            <p>
            <label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number Of Product:' ,'fashionist'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" />
            </p>
            <?php 
        }
            
        // Updating widget replacing old instances with new
        public function update( $new_instance, $old_instance ) 
        {
            $instance = array();
            $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
            $instance['number'] = ( ! empty( $new_instance['number'] ) ) ? strip_tags( $new_instance['number'] ) : '';
            return $instance;
        }
    } // Class wpb_widget ends here

    // Register and load the widget
    function fashionist_popular_products_load_widget() {
        register_widget( 'fashionist_popular_products_widget' );
    }
    add_action( 'widgets_init', 'fashionist_popular_products_load_widget' );
}

// Creating the Search widget 
class fashionist_searchbox extends WP_Widget 
{
    function __construct() {
        parent::__construct(
        // Base ID of your widget
        'fashionist_searchbox', 

        // Widget name will appear in UI
        __('Fashionist Search Widget', 'fashionist'), 

        // Widget description
        array( 'description' => __( 'A custom search form for fashionist site. ', 'fashionist' ), ) 
        );
    }

    // Creating widget front-end
    // This is where the action happens
    public function widget( $args, $instance ) 
    {
        $title = apply_filters( 'widget_title', $instance['title'] );
        // before and after widget arguments are defined by themes
        echo $args['before_widget'];
        if ( ! empty( $title ) )
        echo '<div class="title">'. $title .'</div>';
        
        $output = '<div class="searchform">';
        $output .= '<form action="'.site_url().'" class="searchform" method="get" role="search">';
            $output .= '<input type="text" name="s" value="" placeholder="search for something" class="search-field">';
            $output .= '<button type="submit"><span class="icon-right"></span></button>';
        $output .= '</form>';
        $output .= '</div>';

        echo $output;
        echo $args['after_widget'];
    }
        
    // Widget Backend 
    public function form( $instance ) 
    {
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        }
        else {
            $title = __( '', 'fashionist' );
        }
        // Widget admin form
        ?>
        <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ,'fashionist' ); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <?php 
    }
        
    // Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) 
    {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        return $instance;
    }
} // Class wpb_widget ends here

// Register and load the widget
function fashionist_searchbox_load() {
    register_widget( 'fashionist_searchbox' );
}
add_action( 'widgets_init', 'fashionist_searchbox_load' );

// Creating the Category widget 
class fashionist_category extends WP_Widget 
{
    function __construct() {
        parent::__construct(
        // Base ID of your widget
        'fashionist_category', 

        // Widget name will appear in UI
        __('Fashionist Category Widget', 'fashionist'), 

        // Widget description
        array( 'description' => __( 'A custom search form for fashionist site. ', 'fashionist' ), ) 
        );
    }

    // Creating widget front-end
    // This is where the action happens
    public function widget( $args, $instance ) 
    {
        $title = apply_filters( 'widget_title', $instance['title'] );              
        
        echo $args['before_widget'];
        if ( ! empty( $title ) )
        echo '<div class="title">'. $title .'</div>';
        
        $output = '<div class="categories">';

        $arg=array( 'orderby' => 'name', 'order' => 'ASC');
        $categories=get_categories($arg);
        foreach($categories as $category) {
            $output .= '<div class="cat-link"><a href="' . get_category_link( $category->term_id ) . '" title="' . sprintf( __( "View all posts in %s" ,'fashionist'), $category->name ) . '" ' . '>'. $category->name.'</a> <span class="quantity">('. $category->count . ')</span></div>';
            }
        $output .= '</div>';

        echo $output;  
        echo $args['after_widget'];      
    }
        
    // Widget Backend 
    public function form( $instance ) 
    {
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        }
        else {
            $title = __( '', 'fashionist' );
        }
        // Widget admin form
        ?>
        <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ,'fashionist' ); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <?php 
    }
        
    // Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) 
    {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        return $instance;
    }
} // Class wpb_widget ends here

// Register and load the widget
function fashionist_category_load() {
    register_widget( 'fashionist_category' );
}
add_action( 'widgets_init', 'fashionist_category_load' );

if (fashionist_checkPlugin('woocommerce/woocommerce.php') ) {  
    // Creating the Woocommerce Category widget 
    class fashionist_woo_category extends WP_Widget 
    {
        function __construct() {
            parent::__construct(
            // Base ID of your widget
            'fashionist_woo_category', 

            // Widget name will appear in UI
            __('Fashionist Woocommerce Category', 'fashionist'), 

            // Widget description
            array( 'description' => __( 'A category list for fashionist site. ', 'fashionist' ), ) 
            );
        }

        // Creating widget front-end
        // This is where the action happens
        public function widget( $args, $instance ) 
        {
            $title = apply_filters( 'widget_title', $instance['title'] );              
            echo $args['before_widget'];

            if ( ! empty( $title ) )
            echo '<div class="title">'. $title .'</div>';       
            $output = '<div class="categories">';

            	  $taxonomy     = 'product_cat';
    			  $orderby      = 'name';  
    			  $show_count   = 0;      // 1 for yes, 0 for no
    			  $pad_counts   = 0;      // 1 for yes, 0 for no
    			  $hierarchical = 1;      // 1 for yes, 0 for no  
    			  $title        = '';  
    			  $empty        = 0;

    			  $args1 = array( 'taxonomy'     => $taxonomy,'orderby'      => $orderby,'show_count'   => $show_count,'pad_counts'   => $pad_counts,'hierarchical' => $hierarchical,'title_li'     => $title,'hide_empty'   => $empty);
    			 $all_categories = get_categories( $args1 );
    			 foreach ($all_categories as $cat) {
    			    if($cat->category_parent == 0) {
    			        $category_id = $cat->term_id;
    			        $output .= '<div class="cat" onclick="open_cat(\''.$cat->slug.'\')"><a href="'. get_term_link($cat->slug, 'product_cat') .'">'.$cat->name.'</a> <span class="open">+</span></div>';       
    			        $args2 = array('taxonomy'     => $taxonomy,
    			                'child_of'     => 0,
    			                'parent'       => $category_id,
    			                'orderby'      => $orderby,
    			                'show_count'   => $show_count,
    			                'pad_counts'   => $pad_counts,
    			                'hierarchical' => $hierarchical,
    			                'title_li'     => $title,
    			                'hide_empty'   => $empty
    			        );
    			        $sub_cats = get_categories( $args2 );
    			        if($sub_cats) {
    			        	$output .= '<div class="cat-links" id="'.$cat->slug.'">';
    			            foreach($sub_cats as $sub_category) {
    			            	$output .= '<div class="cat-link"><a href="'. get_term_link($sub_category->slug, 'product_cat') .'">'.$sub_category->name.' </a><span class="quantity">('.$sub_category->count.')</span></div>';			                
    			            }
    			            $output .= '</div>';   
    			        }
    			    }       
    			}
    		$output .= '</div>';

            echo $output; 
            echo $args['after_widget'];       
        }
            
        // Widget Backend 
        public function form( $instance ) 
        {
            if ( isset( $instance[ 'title' ] ) ) {
                $title = $instance[ 'title' ];
            }
            else {
                $title = __( '', 'fashionist' );
            }
            // Widget admin format
            ?>
            <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ,'fashionist' ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            </p>
            <?php 
        }
            
        // Updating widget replacing old instances with new
        public function update( $new_instance, $old_instance ) 
        {
            $instance = array();
            $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
            return $instance;
        }
    } // Class wpb_widget ends here

    // Register and load the widget
    function fashionist_woo_category_load() {
        register_widget( 'fashionist_woo_category' );
    }
    add_action( 'widgets_init', 'fashionist_woo_category_load' );
}