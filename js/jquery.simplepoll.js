/**
 * SimplePoll jQuery plugin
 * 
 * Copyright © 2011 James Stine (leon.blade@gmail.com)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>. 
 */
 
(function($) {
	$.simplepoll = function(object, id) {
		
		// define the gateway file
		var gateway = "gateway.php";
		
		// grab the poll from the gateway
		$.getJSON(gateway, {action:'get', id:id}, function(poll) {
		
			// first we create a header for the poll name
			var $header = $('<h2>').text(poll.name).addClass('simplepoll-header');
			
			// next create a div that will hold the poll options
			var $options_container = $('<div>').addClass('simplepoll-optionscontainer');
			// add the total votes to the options container
			$options_container.attr('total-votes', poll.total_votes);
			
			// next we will loop over each poll option
			$(poll.choices).each(function(i, option) {
			
				// we create a div for each option
				var $option = $('<div>').attr('votes', option.votes).addClass('simplepoll-option');
				
				// first we can add the title of the option 
				var $title = $('<p>').text(option.option).addClass('simplepoll-optiontitle');
				$option.append($title);
				
				// if we have an image we will add one of those in there
				if (option.image != '') {
					var $image = $('<img>').attr('src', option.image).addClass('simplepoll-optionimage');
					$option.append($image);
				}
				
				// we can now add the div for the radio button/vote bar
				var $bottom = $('<div>').addClass('simplepoll-optionbottom');
				
				// add this option to the container
				$options_container.append($option);
			
			});
			
			// add everything to the object
			console.log(object);
			$(object).append($header).append($options_container);
			
		});
		
		// return this object
		return this;
	};
	
	// this will let things like $('element').simplepoll( … ) to work
	$.fn.simplepoll = function(id) {	
		this.each(function() {
			$.simplepoll(this, id);
		});
	};
}) (jQuery);
