<?php
/**
 * Add function to widgets_init that will load our widget.
 */
add_action( 'widgets_init', 'most_commented_load_widgets' );

/**
 * Register our widget.
 * 'Last_Comments_Widget' is the widget class used below.
 */
function most_commented_load_widgets() {
	register_widget( 'most_commented_widget' );
}

/**
 * most_commented Widget class.
 */
class most_commented_widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function most_commented_widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'Most Commented News', 'description' => 'The most commented news' );

		/* Widget control settings. */
		$control_ops = array( 'width' => 200, 'height' => 250, 'id_base' => 'most-commented-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'most-commented-widget', 'RoyalNews: Most Commented News', $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$number = $instance['number'];

		/* Before widget (defined by themes). */			
		echo $before_widget;		

		
		$output = '';		
		$output .= '
			<!-- // most commented start // -->
			<div class="side-block">
			  <div class="side-block-lbl">'.$title.'</div>
			  <div class="most-commented-row">
		';
		
		/* Display the widget title if one was input (before and after defined by themes). */
		//if ( $title ) $output .= $before_title . $title . $after_title;		
		
		global $wpdb;
		global $wlm_shortname;
		
		$sql = 'select DISTINCT * from '.$wpdb->posts.' 
			WHERE '.$wpdb->posts.'.post_status="publish" 
			AND '.$wpdb->posts.'.post_type="post" 
			ORDER BY '.$wpdb->posts.'.comment_count DESC
			LIMIT 0,'.$number;

		$blogposts = $wpdb->get_results($sql);

		$post_number = 0;
		foreach ($blogposts as $post) {
			$post_title = $post->post_title;
			/*if (strlen($post_title) > 60) {
				$post_title = substr($post_title,0,60).'...';
			}*/
			
			$post->post_date = date("d F, Y",strtotime($post->post_date));			
			//$get_custom_options = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), '', false );
			//$image_preview_url = $get_custom_options[0];
			//$image_thumb = '<img src="'.mr_image_resize($image_preview_url,45,45,'br','','').'" alt="'.$post_title.'" />';
			
			$output .= '
				<!-- // -->
				<div class="most-commented-item">
				  <div class="most-commented-l">
					<a href="'.get_permalink($post->ID).'">'.$post_title.'</a>
				  </div>
				  <div class="most-commented-r">'.get_comments_number($post->ID).' <span class="most-commented-c"></span></div>
				  <div class="clear"></div>
				</div>
				<!-- \\ -->
			';
		}

		$output .= '
				</div>
			</div>
		';

		echo $output;
		/* After widget (defined by themes). */
		//echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and comments count to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number'] = strip_tags( $new_instance['number'] );
		
		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => '', 'number' => '3', 'description' => 'The most commented news' );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo 'Title:'; ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php echo 'Count:'; ?></label>
			<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" value="<?php echo $instance['number']; ?>" style="width:100%;" />
		</p>

	<?php
	}
}

?>