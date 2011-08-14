<?php 

// require our little polls class
require_once 'inc/littlepoll.php';
if (!$little_poll->checkAdminToken()) {
	header('Location: login.php');
}

/* require_once the header */ require_once 'templates/header.php'; 

?>

<script type="text/javascript">
$(function() 
{			
	// adding a new option for a new poll
	$('#add_option').click(function()
	{
		// create a new option div starting off with display none
		$option_div = $('<div>').addClass('option').css('display', 'none');
		
		// set up the inputs for option and image and also the remove button
		$o_name = $('<input>').attr({'type':'text', 'placeholder':'option'}).addClass('option-name');
		$o_image = $('<input>').attr({'type':'text', 'placeholder':'image'}).addClass('option-image');
		$o_remove = $('<input>').attr({'type':'button', 'value':'Remove'}).addClass('option-remove'); 
		
		// append the inputs to the option div
		$option_div.append($o_name);
		$option_div.append($o_image);
		$option_div.append($o_remove);
		
		// add the option div to the new poll options list
		$('#new_poll_options').append($option_div);
		
		// fade in the option so it looks cool
		$option_div.fadeIn(300);
	});	
	
	// live click for new options to remove them
	$('.option-remove').live('click', function() 
	{
		// grab the parent element option div
		$option_div = $(this).parent('.option');
		// fade it out and afterwards remove it
		$option_div.fadeOut(300, function(){$(this).remove();});
	});
	
	// this handles any errors to make them go away once they are clicked
	$('.error').live('click', function()
	{
		$(this).removeClass('error');
	});
	
	// creating the poll
	$('#create_poll').click(function() 
	{
		// create an options array
		var options = new Array();
		
		// adds error if poll name isn't filled out
		if ($('#new_poll_name').val() == '') $('#new_poll_name').addClass('error');
		
		// loop through each option
		$.each($('#new_poll_options .option'), function(i, val)
		{				
			// grab the values
			$option_name = $(val).children('.option-name');
			$option_image = $(val).children('.option-image');
			
			// adds an error to the name if there is none image can be left blank
			if ($option_name.val() == '') $option_name.addClass('error');
			
			// set up the choice array
			var choice = new Object();
			choice.option = $option_name.val();
			choice.image = $option_image.val();
			
			// add the new choice to the options array
			options.push(choice);
		});
		
		// adds an error to the add option button if there are less than 2 options
		if (options.length < 2) $('#add_option').addClass('error');
		
		// if there are no errors
		if ($('.error').size() == 0)
		{
			// create option json
			var option_json = JSON.stringify(options);

			// create the new poll array
			$.post('inc/create_poll.php', {name:$('#new_poll_name').val(), options:option_json}, function(data)
			{
				// turn to JSON
				data = $.parseJSON(data);
			
				// a simple success return is called and we remove all options and clear the name
				if (data.success == 1) 
				{
					// calling the click on all the option-remove items will remove all the options
					$('#new_poll_options .option-remove').click();
					// set the value to nothing
					$('#new_poll_name').val('');
					
					// call the get polls function
					getPolls();
				}
				else // otherwise we flash the create poll with an error 
					$('#create_poll').addClass('error');
			});
		}
	});
	
	// get the polls
	getPolls();
	
	// deleting polls
	$('.poll-delete').live('click', function()
	{
		var poll_id = $(this).parent('.poll').attr('poll');
		
		$.get('inc/delete_poll.php', {id:poll_id}, function(data)
		{
			data = $.parseJSON(data);
			if (data.success == 1)
				getPolls();
		});
	});
	
	// resetting polls
	$('.poll-reset').live('click', function()
	{
		var poll_id = $(this).parent('.poll').attr('poll');
		
		$.get('inc/reset_poll.php', {id:poll_id}, function(data)
		{
			data = $.parseJSON(data);
			if (data.success == 1)
				getPolls();
		});
	});		
	
	$('.poll-add-option').live('click', function()
	{
		$options = $('<div>').addClass('option');
						
		// set up the inputs for option and image and also the remove button
		$o_name = $('<input>').attr({'type':'text', 'placeholder':'option'}).addClass('option-name');
		$o_image = $('<input>').attr({'type':'text', 'placeholder':'image'}).addClass('option-image');
		$o_remove = $('<input>').attr({'type':'button', 'value':'Remove'}).addClass('option-remove'); 
							
		// append the inputs to the option div
		$options.append($o_name).append($o_image).append($o_remove);
		
		// get vote percent
		$vote_bar = $('<div>').addClass('vote-bar');
		$vote_count = $('<div>').addClass('vote-count').css('width', '0%');
		$vote_bar.append($vote_count);
		
		$options.append($vote_bar);
		
		$(this).parent('.poll').children('.poll-options').append($options);		
	});
	
	$('.poll-update').live('click', function()
	{
		// store this button
		$poll_update_btn = $(this);
		$poll = $(this).parent('.poll');
		
		// get poll id
		var poll_id = $(this).parent('.poll').attr('poll');
		
		// create an options array
		var options = new Array();
		
		// adds error if poll name isn't filled out
		if ($poll.children('.poll-name').val() == '') $poll.children('.poll-name').addClass('error');
		
		// loop through each option
		$.each($poll.children('.poll-options').children('.option'), function(i, val)
		{				
			// grab the values
			$option_name = $(val).children('.option-name');
			$option_image = $(val).children('.option-image');
			
			// adds an error to the name if there is none image can be left blank
			if ($option_name.val() == '') $option_name.addClass('error');
			
			// set up the choice array
			var choice = new Object();
			choice.option = $option_name.val();
			choice.image = $option_image.val();
			
			// add the new choice to the options array
			options.push(choice);
		});
		
		// adds an error to the add option button if there are less than 2 options
		if (options.length < 2) $poll_update_btn.parent('.poll').children('.poll-add-option').addClass('error');
		
		// if there are no errors
		if ($('.error').size() == 0)
		{
			// create option json
			var option_json = JSON.stringify(options);

			// create the new poll array
			$.post('inc/update_poll.php', {id:poll_id, name:$poll.children('.poll-name').val(), options:option_json}, function(data)
			{
				// turn to JSON
				data = $.parseJSON(data);
			
				// a simple success return is called and we remove all options and clear the name
				if (data.success == 1) 
					getPolls();
				else // otherwise we flash the create poll with an error 
					$poll_update_btn.addClass('error');
			});
		}
	});
});

function getPolls()
{
	// clear out the polls div
	$('#polls').html('');

	// get the polls JSON
	$.getJSON('inc/get_polls.php', {}, function(data)
	{
		// loop through each poll
		$.each(data, function(i, val)
		{
			// create a div for each poll
			$poll_div = $('<div>').attr('poll', val['id']).addClass('round_box').addClass('poll');
			
			// create an input for poll name
			$poll_name = $('<input>').attr({'type':'text', 'placeholder':'poll name'}).addClass('poll-name').val(val['name']);
			// create an add option button
			$poll_add = $('<input>').attr('type', 'button').val('Add option').addClass('poll-add-option');
			// create an update button
			$poll_update = $('<input>').attr('type', 'button').val('Update').addClass('poll-update');
			// create an reset button
			$poll_reset = $('<input>').attr('type', 'button').val('Reset').addClass('poll-reset');
			// create a delete button
			$poll_delete = $('<input>').attr('type', 'button').val('Delete').addClass('poll-delete');
			// create a div to store the options
			$poll_options = $('<div>').addClass('poll-options');
			
			var vote_total = 0;
			$.each(val['choices'],function(j,c){vote_total+=c['votes'];});
			
			$.each(val['choices'], function(j, c)
			{				
				$options = $('<div>').addClass('option');
			
				// set up the inputs for option and image and also the remove button
				$o_name = $('<input>').attr({'type':'text', 'placeholder':'option'}).addClass('option-name');
				$o_image = $('<input>').attr({'type':'text', 'placeholder':'image'}).addClass('option-image');
				$o_remove = $('<input>').attr({'type':'button', 'value':'Remove'}).addClass('option-remove'); 
				
				$o_name.val(c['option']);
				$o_image.val(c['image']);
									
				// append the inputs to the option div
				$options.append($o_name).append($o_image).append($o_remove);
				
				// get vote percent
				var perc = 0;
				if (vote_total != 0) perc = (c['votes'] / vote_total) * 100;

				$vote_bar = $('<div>').addClass('vote-bar');
				$vote_count = $('<div>').addClass('vote-count').css('width', perc+'%');
				$vote_bar.append($vote_count);
				
				$options.append($vote_bar);
				
				$poll_options.append($options);
			});
			
			// add everything to the div
			$poll_div.append($poll_name).append($poll_add);
			$poll_div.append($poll_options);
			$poll_div.append($poll_update).append($poll_reset).append($poll_delete);
			
			// add the poll div to the polls div
			$('#polls').append($poll_div);
		});
	});
}
</script>

<h3>Create poll</h3>

<div id="new_poll" class="round_box">	
	<input type="text" id="new_poll_name" placeholder="poll name" /><input type="button" id="add_option" value="Add option" />
	<div id="new_poll_options"></div>
	<input type="button" id="create_poll" value="Create poll" />	
</div>

<h3>Edit polls</h3>

<div id="polls"></div>

<?php /* require_once the footer */ require_once 'templates/footer.php'; ?>
