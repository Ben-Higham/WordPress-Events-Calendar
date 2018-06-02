<?php

/**
 * The file that defines methods for the database
 *
 * A class definition that includes attributes and functions used
 * to Insert, Get, Update and Delete from the database
 *
 * @link       http://belvidere.org.uk
 * @since      1.0.0
 *
 * @package    Belv_Events
 * @subpackage Belv_Events/includes
 */

/**
 * The Data class.
 *
 * This is used to define 
 * 
 *
 * @since      1.0.0
 * @package    Belv_Events
 * @subpackage Belv_Events/includes
 * @author     Ben Higham <web@belvidere.org.uk>
 */
class Belv_Events_Data {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0.
	 */
	public function __construct() {

	}

	/**
	 * Method used to retrieve data from the database
	 *
	 * @since    1.0.0
	 */
    public function belv_get_months_events( $params ) {
        global $wpdb;
        
        $year = $params['year'];
		$month = $params['month'];
        
        $table = $wpdb->prefix . 'belv_calendar';
		$sql_query = "SELECT * FROM $table WHERE month(date) = $month AND year(date) = $year";
            
        $events =  $wpdb->get_results($sql_query);
		return $events;

		die();
        
    }

    public function belv_post_new_event() {

		check_ajax_referer('calendar_nonce');
		
		global $wpdb;

		$title = $_POST['title'];
        $date = $_POST['date'];
        $time = $_POST['time'];
        $link = $_POST['link'];
		
		$table = $wpdb->prefix . 'belv_calendar';

		$wpdb->insert($table,
			array(
                'title' => $title,
                'date' => $date,
                'time' => $time,
                'link' => $link
            )
		);

	}

	public function belv_update_event(){

		check_ajax_referer('calendar_nonce');

		global $wpdb;
		
		$id = $_POST['id'];
		$title = $_POST['title'];
        $date = $_POST['date'];
        $time = $_POST['time'];
		$link = $_POST['link'];
		
		$table = $wpdb->prefix . 'belv_calendar';

		$wpdb->update( $table,
			array(
                'title' => $title,
                'date' => $date,
                'time' => $time,
                'link' => $link
            ),
			array( 'id' => $id )
		);

	}

	public function belv_remove_event(){
		
		check_ajax_referer('calendar_nonce');

		global $wpdb;

		$id = $_POST['id'];

		$table = $wpdb->prefix . 'belv_calendar';

		$wpdb->delete( $table, array( 'id' => $id )	);

		die();

	}
}