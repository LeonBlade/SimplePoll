<?php session_start(); /* require_once the header */ require_once 'templates/header.php'; ?>

<script type="text/javascript">
$(function()
{
	$.getJSON('inc/get_polls.php', {}, function(data)
	{
		$('#polls').html('');
	
		// loop through each poll
		$.each(data, function(i, poll)
		{
			$poll_div = $('<div>').attr({'poll':poll['id'], 'class':'poll round_box', 'total_votes':poll['total_votes']});
			$poll_div.append("<h2>"+poll['name']+"</h2>");
			
			$choices_div = $('<div>').addClass('choices-container');
			
			$.each(poll['choices'], function(j, choice)
			{
				$c = $('<div>').addClass('choice').attr('votes', choice['votes']);
				$c.append('<p>'+choice['option']+'</p>');
				if (choice['image'] != '') 
					$c.append('<img src="'+choice['image']+'" />');
				$bdiv = $('<div>').css({'margin':'5px 0', 'height':'19px'});
				if ($.cookie('p'+poll['id']) == '1')
				{
					$v_b = $('<div>').addClass('vote-bar');						
					var vote_perc = (choice['votes']/poll['total_votes'])*100;
					$v_c = $('<div>').addClass('vote-count').css('width', vote_perc+'%');
					$v_b.append($v_c);
					$bdiv.html($v_b);
				}
				else
				{
					$bdiv.append('<input type="radio" name="p'+poll['id']+'" value="'+j+'" />');
				}
				
				$c.append($bdiv);
				$choices_div.append($c);
			}); 
			
			$poll_div.append($choices_div);
			$poll_div.append('<div style="clear:both;"></div>');
			
			if ($.cookie('p'+poll['id']) == null)
			{
				$vote_button = $('<input>').attr('type', 'button').addClass('poll-vote').val('Vote');
				$vote_button.css({'position':'relative', 'left':'50%', 'margin-left':'-27px'});
				
				$poll_div.append($vote_button);
			}
			
			$('#polls').append($poll_div);
		});
		
		$('.choice').live('click', function() 
		{
			$(this).parent().children('.choice').removeClass('option-selected');
			$(this).addClass('option-selected');
			$(this).children('div').children('input[type=radio]').attr('checked', 'true');
		});
		
		$('.poll-vote').live('click', function()
		{
			$poll_div = $(this).parent('.poll');
			$vdiv = $poll_div.children('.choices-container').children('.option-selected').children('div')
			var choice = $vdiv.children('input[type=radio]:checked').val();
			$.get('inc/vote_poll.php', {id:$poll_div.attr('poll'), choice:choice}, function(data)
			{
				data = $.parseJSON(data);
				if (data.success == 1)
				{
					var total_votes = Number($poll_div.attr('total_votes'));
					total_votes++;
					
					$.each($poll_div.children('.choices-container').children(), function(i, j)
					{
						$poll_div.attr('total_votes', total_votes);
						$v_b = $('<div>').addClass('vote-bar');
						
						var votes = Number($(j).attr('votes'));
						if ($(this).hasClass('choices-container')) votes++;
						
						var vote_perc = (votes/total_votes)*100;

						$v_c = $('<div>').addClass('vote-count').css('width', vote_perc+'%');
						$v_b.append($v_c);
						$(this).children('div').html($v_b);
					});
				}
			});						
		});
	});
});
</script>

<div id="polls"></div>

<?php /* require_once the footer */ require_once 'templates/footer.php'; ?>