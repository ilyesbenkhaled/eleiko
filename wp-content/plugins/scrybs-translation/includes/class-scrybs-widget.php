<?php

/* This class and function were forked from Weglot */
class ScrybsWidget extends WP_Widget {

    public function ScrybsWidget() {
    	$widget_ops = array( 
			'classname' => 'scrybs',
			'description' => 'Add a language switcher.',
		);
		parent::__construct( 'scrybs', 'Scrybs Multilingual', $widget_ops );
    }

    public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}

    function widget($args, $instance) {
		
		$title = apply_filters( 'widget_title', $instance['title'] );
	
		$tt = ( ! empty( $title ) ) ? $args['before_title'] . $title . $args['after_title']:"";
		$button = '<div id="scrybs_here"></div>';
		echo $args['before_widget'].$tt.$button.$args['after_widget'];
    }
	
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = "";
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}
}