(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
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

var monthNames = ["January", "February", "March", "April", "May", "June", "July",
					"August", "September", "October", "November", "December"];
var weekDays = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
var monthDays = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
var todaysDate = new Date();
var currentMonth;
var currentYear;
var currentDay = 0;
var daysInMonth = 0;
var weeksInMonth = 0;
var previousMonth;
var previousYear;
var nextMonth;
var nextYear;

jQuery(document).ready(function($) {

	currentYear = todaysDate.getFullYear();
    currentMonth = todaysDate.getMonth();
	
	getEvents($, currentMonth, currentYear);

    $(".belv-previous-month").on('click', function(){
        $("#belv-desk-body").html(loadingIcon);
        $("#belv-mobile-body").html(loadingIcon);

        // If month is January, set month to December and decrement year
        if(currentMonth == 0){
            currentMonth = 11;
            currentYear--;
        } else {
            currentMonth--;
        }
        getEvents($, currentMonth, currentYear);
    });
    
    $(".belv-next-month").on('click', function(){
        $("#belv-desk-body").html(loadingIcon);
        $("#belv-mobile-body").html(loadingIcon);

        // If month is December, set month to January and increment year
        if(currentMonth == 11){
            currentMonth = 0;
            currentYear++;
        } else {
            currentMonth++;
        }
        getEvents($, currentMonth, currentYear);
    });

});

function getEvents($, month, year) {
	var url = document.location.origin + "/wp-json/belv-events/v1/events/" + (month + 1) + "/" + year;
	$.get( url, function( data, status ) {
        var days = new Date(year, month+1, 0).getDate();

        // Exclude these dates
        var excludeDates = [
            { date: "2022-09-25", time: "06:00pm" },
            { date: "2022-09-09", time: "10:15am" },
            { date: "2022-09-09", time: "08:00pm" },
        ];
        
        for(var day = 1; day <= days; day++) {
            var checkDate = year + '-' + String(month+1).padStart(2, '0') + '-' + String(day).padStart(2, '0');
            
            // Add Sunday services
            if(new Date(year, month, day).getDay() == 0) {
                var morningService = data.find(event => event.date === checkDate && event.time === '10:15am');
                var eveningService = data.find(event => event.date === checkDate && event.time === '06:00pm');
                if(!morningService) {
                    data.push({
                        date: checkDate,
                        link: "",
                        time: "10:15am",
                        title: "Morning Service"
                    });
                }
                if(!eveningService) {
                    data.push({
                        date: checkDate,
                        link: "",
                        time: "06:00pm",
                        title: "Evening Service"
                    });
                }
            }

            // Add Wednesday meetings
            if(new Date(year, month, day).getDay() == 3) {
                var wednesdayMeeting = data.find(event => event.date === checkDate && event.time === '08:00pm');
                if(!wednesdayMeeting) {
                    data.push({
                        date: checkDate,
                        link: "",
                        time: "08:00pm",
                        title: "Bible Study & Prayer"
                    });
                }
            }

            excludeDates.forEach(function(exc) {
                data = data.filter(function(obj) {
                    return obj.time !== exc.time || obj.date !== exc.date;
                });
            });
            
        }
        updateCalendar($, month, year, data);
	});
}

function updateCalendar($, month, year, events){

    getNextAndPreviousMonth();

    sortEvents(events);
    
    // Update desktop calendar month and year title
    $(".belv-current-month").html(monthNames[month] + " " + year);

    // Update desktop calendar with previous month
    $("#belv-desk-previous-month").html("< " + monthNames[this.previousMonth] + " " + this.previousYear);
    
    // Update desktop calendar with next month
    $("#belv-desk-next-month").html(monthNames[this.nextMonth] + " " + this.nextYear + " >");

    // Update desktop calendar table with the events
	$("#belv-desk-body").html(desktopCalendar(currentMonth, currentYear, events));

    // Update mobile calendar table with the events
	$("#belv-mobile-body").html(mobileCalendar(currentMonth, currentYear, events));
    
}

function getNextAndPreviousMonth(){
    // Get previous month and year and next month and year
    if(this.currentMonth == 0){
        this.previousMonth = 11;
        this.previousYear = this.currentYear - 1;
        this.nextMonth = this.currentMonth + 1;
        this.nextYear = this.currentYear;
    } else if(this.currentMonth == 11){
        this.nextMonth = 0;
        this.nextYear = this.currentYear + 1;
        this.previousMonth = this.currentMonth - 1;
        this.previousYear = this.currentYear;
    } else {
        this.previousMonth = this.currentMonth - 1;
        this.nextMonth = this.currentMonth + 1;
        this.previousYear = this.currentYear;
        this.nextYear = this.currentYear;
    }    
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

function desktopCalendar(month, year, events){
    var desktopContent = "";
    this.currentDay = 0;

    // Add extra day to February if this year is a leap year
    if (((year % 4 == 0) && !(year % 100 == 0)) || (year % 400 == 0)){
        monthDays[1] = 29;
    } else {
        monthDays[1] = 28;
    }
    
    this.weeksInMonth = getWeeksInMonth(month, year);
    this.daysInMonth = monthDays[month];
    
    this.firstDay = new Date(year, month, 1);

    this.firstDay = this.firstDay.getDay();
    
    for (var week = 0; week < this.weeksInMonth; week++){
        desktopContent += '<tr class="belv-desk-cell" id="belv-desk-week-' + week.toString() + '">';
        for (var day = 0; day < 7; day++) {
            desktopContent += showDay(week * 7 + day, events);
        }
        desktopContent += '</tr>'; 
    }


    return desktopContent;
}

function getWeeksInMonth(month, year) {
    // Get first day of selected month
    var firstOfMonth = new Date(year, month, 1);

    // Get last day of selected month
    var lastOfMonth = new Date(year, month + 1, 0);

    // Get the number of days needed
    var usedDays = firstOfMonth.getDay() + lastOfMonth.getDate();

    return Math.ceil(usedDays / 7);
}

function showDay(dayNumber, events) {
    if (this.currentDay == 0) {

        if(dayNumber == this.firstDay){
            this.currentDay = 1;
        }

    }
    
    var cellContent;
    
    if (this.currentDay != 0 && this.currentDay <= this.daysInMonth){
        cellContent = currentDay;
        if(events[0] != 'No Events') {
            if(events.length != 0) {
                for(var e = 0; e < events.length; e++){
                    if((events[e].date).slice(-2) == currentDay) {
                        cellContent += '<p><strong>' + events[e].time + '</strong> ';
                        
                        if(events[e].link != '') {
                            cellContent += '<a href=\"' + events[e].link + '\">'  + events[e].title + '</a>';
                        } else {
                            cellContent += events[e].title;
                        }
                        
                        cellContent += '</p>';
                    }
                }
            }
        }
        currentDay++;
    } else {
        currentDay = 0;
        cellContent = "&nbsp;";
    }

    return '<td ' +
        (cellContent == '&nbsp;' ? 'class="belv-desk-blank">' : 'id="belv-desk-day-' + currentDay + '">') +
        cellContent + '</td>';     
}

function mobileCalendar(month, year, events){
    var mobileContent = '';
    if(events.length != 0){
        for(var e = 0; e < events.length; e++){
            mobileContent += '<tr><td><div class="image">';
            mobileContent += '<img class="icon" src="' + wp_site.url + 'img/calendar_template.png">';
            mobileContent += '<h2 class="month">' + monthNames[month] + '</h2>';
            mobileContent += '<h2 class="date">' + parseInt((events[e].date).slice(-2), 10) + '</h2>';
            mobileContent += '<h2 class="day">' + weekDays[new Date(events[e].date).getDay()] + '</h2>';
            mobileContent += '</div></td>';
            mobileContent += '<td class="mobile-calendar-time">' + events[e].time + '</td>';
            mobileContent += '<td class="mobile-calendar-event">' + events[e].title + '</td>';
            mobileContent += '</tr>';
        }
    } else {
        mobileContent = '<tr><td colspan="7" style="text-align: center;">' +
                            'No Events Found' +
                        '</td></tr>';
    }

    return mobileContent;

}

function loadingIcon(){
    return '<tr><td colspan="7" style="text-align: center;">' +
                '<img src="' + wp_site.url + 'img/ajax-loader.gif">' +
            '</td></tr>';
}