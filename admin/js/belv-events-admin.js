(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

})( jQuery );

var monthNames = ["January", "February", "March", "April", "May", "June",
  "July", "August", "September", "October", "November", "December"];
var currentMonth;
var currentYear;

jQuery(document).ready(function($) {

	$( function() {
		jQuery( ".datepicker" ).datepicker();
	});

	currentMonth = (new Date().getMonth());
	currentYear = new Date().getFullYear();

	// Sets month to the current month
	setCurrentMonth($, currentMonth, currentYear);

	// Initialise save button onClick event
	saveNewEvent($);

	// Get events from current month
	getEvent($, currentMonth, currentYear);

	$("#previous-month").click(function() {
		jQuery('#belv-calendar-events').html(spinnerHtml());
		// If month is January, set month to December and decrement year
        if(currentMonth == 0){
            currentMonth = 11;
			currentYear--;
        } else {
            currentMonth--;
        }
		setCurrentMonth($, currentMonth, currentYear);
        getEvent($, currentMonth, currentYear);
    });
    
    $("#next-month").click(function() {
		jQuery('#belv-calendar-events').html(spinnerHtml());
        // If month is December, set month to January and increment year
        if(currentMonth == 11){
            currentMonth = 0;
			currentYear++;
        } else {
            currentMonth++;
        }
		setCurrentMonth($, currentMonth, currentYear);
        getEvent($, currentMonth, currentYear);
    });

});

function spinnerHtml(){
	var content = '<tr>';
	content += '<td colspan="6" style="text-align: center;">';
	content += '<span class="spinner is-active" style="float:none;"></span>';
	content += '</td>';
	content += '</tr>';
	return content;
}

function setCurrentMonth($, month, year){
	$('#month-title').html(monthNames[month] + " " + year);
}

function saveNewEvent($) {
	$('#save-event').on('click', function() {

		// Get date from UI
		var dateArray = $('#event-date').val().split('/');
		var event_date = dateArray[2] + '-' + dateArray[1] + '-' + dateArray[0];
		
		// Get time from UI
		var hours = $('#event-hour').val();
		var minutes = $('#event-minute').val();
		var time = $('#event-time').val();
		var event_time = hours + ':' + minutes + time;

		// Get title from UI
		var event_title = $('#event-title').val();
		
		// Get link from UI
		var event_link = $('#event-link').val();

		$.post(belvajaxobject.ajax_url, {
			_ajax_nonce: belvajaxobject.nonce,
			action: 'belv_post_new_event',
			title: event_title,
			date: event_date,
			time: event_time,
			link: event_link,
		}, function(response){
			if(!response){
				alert('Error');
			} else {
				alert('Event has been added to the calendar');
				getEvent($, dateArray[1], dateArray[2]);
			}
		});
	});
}

// Loads the events from the database for the month and year arguments
function getEvent($, month, year) {
	var url = document.location.origin + "/wp-json/belv-events/v1/events/" + (month + 1) + "/" + year;
	jQuery.get( url, function( response ) {
			updateContent(response)
		}
	)

}

// Updates the table on the options page with the events from the response
function updateContent(response) {
	var bodyContent = '';
	if(response.length == 0) {
		bodyContent = '<tr><td colspan="6" style="text-align: center;">No Events for this month</td></tr>';
		jQuery('#belv-calendar-events').html(bodyContent);
	} else {
		
		sortEvents(response);
				
		for(var e = 0; e < response.length; e++){
			splitDate = response[e].date.split('-');
			date = splitDate[2] + '/' + splitDate[1] + '/' + splitDate[0];

			bodyContent += '<tr id="event-' + response[e].id + '">';
			bodyContent += '<td>' + response[e].title + '</td>';
			bodyContent += '<td style="text-align:center;">' + date + '</td>';
			bodyContent += '<td style="text-align:center;">' + response[e].time + '</td>';
			bodyContent += '<td>' + response[e].link + '</td>';
			bodyContent += '<td id="edit-' + response[e].id + '" style="text-align:center;">'
				+ '<a class="edit-link" style="cursor: pointer;">Edit</a></td>';
			bodyContent += '<td id="delete-' + response[e].id + '" style="text-align:center;">'
				+ '<a class="delete-link" style="cursor: pointer;">Delete</a></td>';
		}
		
		jQuery('#belv-calendar-events').html(bodyContent);

		editLink();
		
		deleteLink();
	}
}

function editLink(){
	jQuery('.edit-link').click( function() {
		var row = jQuery(this).closest("tr");
		var id = row.attr("id").split('-')[1];
		var title = row.find('td:eq(0)').html();
		var date = row.find('td:eq(1)').html();
		var hour = row.find('td:eq(2)').html().substring(0, 2);
		var minute = row.find('td:eq(2)').html().substring(3, 5);
		var time = row.find('td:eq(2)').html().substring(5, 7);
		var link = row.find('td:eq(3)').html();

		jQuery('#event-title').val(title);
		jQuery('#event-date').val(date);
		jQuery('#event-hour').val(hour);
		jQuery('#event-minute').val(minute);
		jQuery('#event-time').val(time);
		jQuery('#event-link').val(link);
		
		jQuery(window).scrollTop(0);
		jQuery('#edit-event-buttons').css('display', 'block');
		jQuery('#add-event-button').css('display', 'none');

		cancelButton();
		saveButton(id);
	});
}

function cancelButton(){
	// When cancel button clicked, hide editing buttons and set input values to default
	jQuery('#cancel-changes-event').click(function(){
		jQuery('#edit-event-buttons').css('display', 'none');
		jQuery('#add-event-button').css('display', 'block');
		jQuery('#event-title').val('');
		jQuery('#event-date').val('');
		jQuery('#event-hour').val('01');
		jQuery('#event-minute').val('00');
		jQuery('#event-time').val('am');
		jQuery('#event-link').val('');
	});
}

function saveButton(id){
	jQuery('#save-changes-event').click(function(){
		// Get date from UI
		var dateArray = jQuery('#event-date').val().split('/');
		var event_date = dateArray[2] + '-' + dateArray[1] + '-' + dateArray[0];
			
		// Get time from UI
		var hours = jQuery('#event-hour').val();
		var minutes = jQuery('#event-minute').val();
		var time = jQuery('#event-time').val();
		var event_time = hours + ':' + minutes + time;

		// Get title from UI
		var event_title = jQuery('#event-title').val();
			
		// Get link from UI
		var event_link = jQuery('#event-link').val();

		// When save changes button is clicked, save changes to the database
		jQuery.post(belvajaxobject.ajax_url, {
			_ajax_nonce: belvajaxobject.nonce,
			action: 'belv_update_event',
			id: id,
			title: event_title,
			date: event_date,
			time: event_time,
			link: event_link,
		}, function(response, status){
			if(status == 'success'){
				alert('Event has been updated');
				getEvent($, currentMonth, currentYear);
			} else {
				console.log('Error updating event');
			}
		});
		
	});
}

function deleteLink(){
	jQuery('.delete-link').click( function() {
		var row = jQuery(this).closest("tr");
		var id = row.attr("id").split('-')[1];
		var title = row.find('td:eq(0)').html();
		var date = row.find('td:eq(1)').html();
		var deleted = confirm("Are you sure you want to delete the event " + title + " on " + date);
		if(deleted == true) {
			jQuery.post(belvajaxobject.ajax_url, {
				_ajax_nonce: belvajaxobject.nonce,
				action: 'belv_remove_event',
				id: id,
			}, function(response, status) {
				if(status == "success"){
					alert('Event has been deleted');
					getEvent($, currentMonth, currentYear);
				} else {
					console.log('Error deleting event ' + id);
				}
			});
		}
	});	
}

function sortEvents(events){

   	events.sort(function(a,b){
		// Event date of month
		var aDate = new Date(a.date).getTime();
		var bDate = new Date(b.date).getTime();
		// Event hours
		var aHour = parseInt(a.time.slice(0,2), 10);
		var bHour = parseInt(b.time.slice(0,2), 10);
		// Event AM or PM
		var aTime = a.time.slice(5,7);
		var bTime = b.time.slice(5,7);
		// Event minutes
		var aMinute = parseInt(a.time.slice(3,5), 10);
		var bMinute = parseInt(b.time.slice(3,5), 10);
			
		// Compare date of month and return if they are different
		var result = aDate - bDate;
		if(result !== 0){
			return result;
		}

		// Add 12 hours on if time is PM
		if(aTime == 'pm')
			aHour += 12;
		if(bTime == 'pm')
			bHour += 12;
			
		result = aHour - bHour;
		// Compare hours and return if different
		if(result !== 0){
			return result;
		}
		// Return difference of minutes
		return aMinute - bMinute;
   	});
}