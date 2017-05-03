<?php

/**
 * Fired during plugin activation
 *
 * @link       http://belvidere.org.uk
 * @since      1.0.0
 *
 * @package    Belv_Events
 * @subpackage Belv_Events/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Belv_Events
 * @subpackage Belv_Events/includes
 * @author     Ben Higham <web@belvidere.org.uk>
 */
class Belv_Events_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		global $wpdb;
		global $belv_db_version;
				
		$table_name = $wpdb->prefix . 'belv_calendar';
		$charset_collate = $wpdb->get_charset_collate();
			
		$sql = "CREATE TABLE $table_name (
				id int(9) NOT NULL AUTO_INCREMENT,
				title varchar(45) NOT NULL,
				date date DEFAULT '0000-00-00' NOT NULL,
				time varchar(8) NOT NULL,
				link varchar(65) DEFAULT '' NOT NULL,
				UNIQUE KEY id (id)
				) $charset_collate;";
			
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	
		add_option('belv_db_version', $belv_db_version);

	}

}
