<?php

/**
 * The file that defines the REST API class
 *
 * A class definition that includes attributes and functions used
 * to create endpoints for the REST API
 *
 * @link       http://belvidere.org.uk
 * @since      1.0.0
 *
 * @package    Belv_Events
 * @subpackage Belv_Events/includes
 */

/**
 * The REST API class.
 *
 * This is used to define 
 * 
 *
 * @since      1.0.0
 * @package    Belv_Events
 * @subpackage Belv_Events/includes
 * @author     Ben Higham <web@belvidere.org.uk>
 */
class Belv_Events_Rest_Controller {

    public function __construct() {
        
	}

    /**
    * Register the /wp-json/belv-events/v1/events route
    */
    public function belv_events_register_routes() {
        $version = '1';
        $namespace = 'belv-events/v' . $version;
        $base = 'events';
        register_rest_route( $namespace, '/' . $base . '/(?P<month>[\w]+)/(?P<year>[\d]+)', array(
            'methods'  => WP_REST_Server::READABLE,
            'callback' => array( $this, 'get_months_events' ),
            'args' => array(
                /*'id' => array(
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_numeric( $param );
                    }
                )*/
            )
        ) );
    }

    function get_months_events( $request ) {
        
        $event_data = new Belv_Events_Data();
        $params = $request->get_params();
        $results = $event_data->belv_get_months_events( $params );

        // Return either a WP_REST_Response or WP_Error object
        return new WP_REST_Response( $results, 200 ); 

    }

}
?>