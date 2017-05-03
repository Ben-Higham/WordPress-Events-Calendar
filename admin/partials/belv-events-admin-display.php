<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 * It should primarily consist of HTML with a little bit of PHP.
 *
 * @link       http://belvidere.org.uk
 * @since      1.0.0
 *
 * @package    Belv_Events
 * @subpackage Belv_Events/admin/partials
 */

?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title());?></h1>

    <h2>Add New Event</h2>
    <div id="add-event">
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Title:</th>
                <td><input type="text" name="event_title" id="event-title" class="regular-text"/></td>
            </tr> 
            <tr valign="top">
                <th scope="row">Date:</th>
                <td><input type="text" name="event_date" id="event-date" class="custom_date datepicker"/></td>
            </tr>
            <tr valign="top">
                <th scope="row">Time:</th>
                <td>
                    <select name="event_hour" id="event-hour">
				    <?php 
					    $option_values = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
			            foreach($option_values as $key => $value) {
						    ?><option value="<?php echo $value; ?>"><?php echo $value; ?></option><?php
                    	}
                		?>
				    </select> <strong>:</strong> 
                    <select name="event_minute" id="event-minute">';
						<?php
							$option_values = array('00', '05', '10', '15', '20', '25', '30', '35', '40', '45', '50', '55');
							foreach($option_values as $key => $value) {
								?><option value="<?php echo $value; ?>"><?php echo $value; ?></option><?php 
       						}
                		?>
					</select>
                    <select name="event_time" id="event-time">
						<?php 
							$option_values = array('am', 'pm');
			                foreach($option_values as $key => $value) {
								?><option value="<?php echo $value; ?>"><?php echo $value; ?></option><?php
                    		}
                		?>
					</select>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Link:</th>
                <td><input type="text" name="event_link" id="event-link" class="regular-text"/></td>
            </tr>
        </table>
        <?php submit_button('Add New Event', 'primary', 'save-event'); ?>
    </div>
    <hr>
    <h2>Existing Events</h2>
    <table class="widefat">
    <tr width="100%">
    <td id="previous-month" width="25%" style="text-align:left"><h3><a style="cursor: pointer;">< Previous month</a></h3></td>
    <td id="current-month" width="50%" style="text-align:center"><h2 id="month-title"></h2></td>
    <td id="next-month" width="25%" style="text-align:right"><h3><a style="cursor: pointer;"> Next Month ></a></h3></td>
    </tr>
    </table>
    <table id="current-events" class="widefat striped">
        <thead>
            <tr>
                <th class="row-title"><strong>Title</strong></th>
                <th class="row-title" style="text-align:center;"><strong>Date</strong></th>
                <th class="row-title" style="text-align:center;"><strong>Time</strong></th>
                <th class="row-title" style="text-align:center;"><strong>Link</strong></th>
                <th class="row-title" style="text-align:center;"><strong>Edit</strong></th>
                <th class="row-title" style="text-align:center;"><strong>Delete</strong></th>
            </tr>
        </thead>
        <tbody id="belv-calendar-events">
            <tr>
                <td colspan="6" style="text-align: center;">
                    <span class="spinner is-active" style="float:none;"></span>
                </td>
            </tr>
        </tbody>
    </table>
</div>