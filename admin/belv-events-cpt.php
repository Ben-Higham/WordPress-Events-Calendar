<?php

/**
 * Define the Events custom post type
 *
 *
 * @link       http://belvidere.org.uk
 * @since      1.0.0
 *
 * @package    Belv_Events
 * @subpackage Belv_Events/admin/partials
 */

class Belv_Custom_Post_Type {

	public function __construct(){
		add_action( 'cmb2_admin_init', array($this,'belv_event_metaboxes') );
	}

	/**
	 * Register the Custom Post Type.
	 *
	 * @since    1.0.0
	 */
	public function belv_register_post_type() {

		$singular = 'Calendar Event';
		$plural = 'Calendar Events';
	
		// Set custom post type labels
		$labels = array(
			'name' 					=> $plural,
			'singular_name' 		=> $singular,
			'add_name' 				=> 'Add New',
			'add_new_item' 			=> 'Add New ' . $singular,
			'edit' 					=> 'Edit',
			'edit_item'				=> 'Edit ' . $singular,
			'new_item' 				=> 'New ' . $singular,
			'view'					=> 'View ' . $singular,
			'view_item'				=> 'View ' . $singular,
			'search_term'			=> 'Search' . $plural,
			'parent'				=> 'Parent' . $singular,
			'not_found'				=> 'No ' . $plural . ' found',
			'not_found_in_trash' 	=> 'No ' . $plural . ' found'
		);

		$args = array(
			'labels' 				=> $labels,
			'public' 				=> false,
			'exclude_from_search' 	=> false,
			'publicly_queryable'	=> true,
			'show_ui'				=> true,
			'show_in_nav_menus'		=> false,
			'show_in_menu'			=> true,
			'show_in_admin_bar'		=> true,
			'menu_position' 		=> 21,
			'menu_icon'				=> 'dashicons-calendar-alt',
			'can_export'			=> true,
			'capability_type'		=> 'post',
			'delete_with_user'		=> false,
			'hierarchical'			=> false,
			'has_archive'			=> false,
			'map_meta_cap'			=> true,
			'supports'				=> array('title',)
		);

		register_post_type( 'belv-events', $args );
	}

	public function belv_event_metaboxes(){

		$cmb = new_cmb2_box( array(
			'id'            => 'test_metabox',
			'title'         => __( 'Event Details', 'cmb2' ),
			'object_types'  => array( 'belv-events', ), // Post type
			'context'       => 'normal',
			'priority'      => 'high',
			'show_names'    => true, // Show field names on the left
    	) );

		// Add event date
		$cmb->add_field( array(
			'name'       => __( 'Date', 'cmb2' ),
			'desc'       => __( 'Date of the event', 'cmb2' ),
			'id'         => 'event_date',
			'type'       => 'text_date',
			'show_on_cb' => 'cmb2_hide_if_no_cats',
			'date_format' => 'd-m-Y',

		) );

		// Add event time
		$cmb->add_field( array(
			'name'       => __( 'Time', 'cmb2' ),
			'desc'       => __( 'Time of the event', 'cmb2' ),
			'id'         => 'event_time',
			'type'       => 'text_time',
			'show_on_cb' => 'cmb2_hide_if_no_cats', // function should return a bool value
		) );

		// Add event link
		$cmb->add_field( array(
			'name'       => __( 'Link', 'cmb2' ),
			'desc'       => __( 'Link to event page', 'cmb2' ),
			'id'         => 'event_link',
			'type'       => 'text_url',
			'show_on_cb' => 'cmb2_hide_if_no_cats', // function should return a bool value
		) );

	}

	public function belv_event_columns_title($columns) {
		
		$new_columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __('Event Title'),
			'event_date' => __( 'Event Date' ),
			'event_time' => __( 'Event Time' ),
			'event_link' => __( 'Event Link' ),
			'date' => __('Date'),
		);
		
		return $new_columns;

	}

	public function belv_event_columns_content($columns) {
		
		global $post;
		switch( $columns ) {
			case 'event_date':
				echo get_post_meta($post->ID, 'event_date', true);
				break;
			case 'event_time':
				echo get_post_meta($post->ID, 'event_time', true);
				break;
			case 'event_link':
				$link = get_post_meta($post->ID, 'event_link', true);
				echo '<a href="' . $link . '">' . $link . '</a>';
				break;
		}

	}

	public function belv_event_sortable_columns($columns) {
		$columns['event_date'] = 'event_date';
		return $columns;
	}

	public function belv_event_date_sort(){
		
		if ( isset( $vars['post_type'] ) && 'belv-events' == $vars['post_type'] ) {
			if( isset($vars['orderby']) && 'event_date' == $vars['orderby']){
				$vars = array_merge(
					$vars,
					array(
						'meta_key' => 'event_date',
						'orderby' => 'meta_value_num'
					)
				);
			}
		}

	}

/****

	public function belv_event_meta_callback($post) {

		wp_nonce_field( basename( __FILE__ ), 'belv_events_nonce');
		$belv_stored_meta = get_post_meta( $post->ID );
		?>
			<div>
				<div class="meta-row">
					<div class="meta-th">
						<label class="belv-event-date">Date</label>
					</div>
					<div class="meta-td">
						<input type="text" class="belv-event-date datepicker" name="event_date" id="event-date" value="<?php
							if ( ! empty ($belv_stored_meta['event_date'])) { echo esc_attr($belv_stored_meta['event_date'][0]); }?>"/>
					</div>
				</div>
				<div class="meta-row">
					<div class="meta-th">
						<label class="event-time">Time</label>
					</div>
					<div class="meta-td">
						<select name="event_hour" id="event-hour">
							<?php 
								$option_values = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
			                    foreach($option_values as $key => $value) {
									if($value == $belv_stored_meta['event_hour'][0]) {
										?>
										<option selected><?php echo $value; ?></option>
										<?php 	
									} else { 
										?>
										<option><?php echo $value; ?></option>
										<?php
                        			}
                    			}
                			?>
						</select> : 
						<select name="event_minute" id="event-minute">';
							<?php
								$dropdown_values = array('00', '05', '10', '15', '20', '25', '30', '35', '40', '45', '50', '55', '60');
								foreach($dropdown_values as $key => $value) {
									if($value == $belv_stored_meta['event_minute'][0]) {
										?>
										<option selected><?php echo $value; ?></option>
										<?php 
									} else {
                						?>
                            			<option><?php echo $value; ?></option>
                            			<?php
            						}
       							}
                			?>
						</select>
						<select name="event_time" id="event-time">
							<?php 
								$option_values = array('am', 'pm');
			                    foreach($option_values as $key => $value) {
									if($value == $belv_stored_meta['event_time'][0]) {
										?>
										<option selected><?php echo $value; ?></option>
										<?php 	
									} else {
										?>
										<option><?php echo $value; ?></option>
										<?php
                        			}
                    			}
                			?>
						</select>
					</div>
				</div>
				<div class="meta-row">
					<div class="meta-th">
						<label for="event-id" class="event-link">Link</label>
					</div>
					<div class="meta-td">
						<input type="text" class="event-link regular-text" name="event_link" id="event-link" value="<?php
							if ( ! empty ($belv_stored_meta['event_link'])) { echo esc_attr($belv_stored_meta['event_link'][0]); }?>"/>
					</div>
				</div>
			</div>
		<?php

	}

	public function belv_meta_save($post_id) {

		// Check save status
		$is_autosave = wp_is_post_autosave($post_id);
		$is_revision = wp_is_post_revision($post_id);
		$is_valid_nonce = (isset ($_POST['belv_events_nonce']) && wp_verify_nonce($_POST['belv_events_nonce'], basename(__FILE__))) ? 'true' : 'false';

		// Exit script depending on save status
		if($is_autosave || $is_revision || !$is_valid_nonce){
			return;
		}

		if(isset ($_POST['event_date'])) {
			update_post_meta( $post_id, 'event_date', sanitize_text_field($_POST['event_date']));
		}
		if(isset ($_POST['event_link'])) {
			update_post_meta( $post_id, 'event_link', sanitize_text_field($_POST['event_link']));
		}
		if(isset ($_POST['event_hour'])) {
			update_post_meta( $post_id, 'event_hour', esc_attr($_POST['event_hour']));
		}
		if(isset ($_POST['event_minute'])) {
			update_post_meta( $post_id, 'event_minute', esc_attr($_POST['event_minute']));
		}
		if(isset ($_POST['event_time'])) {
			update_post_meta( $post_id, 'event_time', esc_attr($_POST['event_time']));
		}
		
	}

	***/

}

?>