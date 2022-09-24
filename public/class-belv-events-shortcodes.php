<?php

class Belv_Events_Shortcodes {

    /**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

    public function belv_calendar_shortcode(){

        // Load Javascript when shortcode is called
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/belv-events-public.js', array( 'jquery' ), $this->version, true );

        ob_start();
        ?>

        <table id="belv-desk-calendar">
            <thead>
                <tr class="belv-desk-month">
                    <th id="belv-desk-previous-month" class="belv-previous-month" colspan ="2" style="text-align: left"></th>
                    <th class="belv-current-month" colspan="3" style="font-weight: bold"></th>
                    <th id="belv-desk-next-month" class="belv-next-month" colspan="2" style="text-align: right"></th>
                </tr>
                <tr class="belv-desk-days">
                    <th>Sunday</th>
                    <th>Monday</th>
                    <th>Tuesday</th>
                    <th>Wednesday</th>
                    <th>Thursday</th>
                    <th>Friday</th>
                    <th>Saturday</th>
                </tr>
            </thead>
            <tbody id="belv-desk-body">
                <tr>
                    <td colspan="7" style="text-align: center;">
                        <img src="<?php echo plugin_dir_url( __FILE__ ) . "img/ajax-loader.gif"; ?>">
                    </td>
                </tr>
            </tbody>
        </table>
        <table id="belv-mobile-calendar" style="margin: 0;">
            <tr>
                <td class="belv-previous-month" id="belv-mobile-previous-month" style="text-align: left; border-color: #fff;" width="20%"><</td>
                <td class="belv-current-month" style="text-align: center; border-color: #fff;" width="60%">Month</td>
                <td class="belv-next-month" id="belv-mobile-next-month" style="text-align: right; border-color: #fff;" width="20%">></td>
            </tr>
        </table>
        <table id="belv-mobile-calendar">
            <tbody id="belv-mobile-body">
                <tr>
                    <td colspan="3" style="text-align: center;">
                        <img src="<?php echo plugin_dir_url( __FILE__ ) . "img/ajax-loader.gif"; ?>">
                    </td>
                </tr>
            </tbody>
        </table>
        
        <?php
        return ob_get_clean();
    }

}

?>