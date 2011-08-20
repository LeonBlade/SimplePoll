<?php session_start(); /* require_once the header */ require_once 'templates/header.php'; ?>

<script type="text/javascript">
$(function() {
	
	$.getJSON('gateway.php?action=get', {}, function(data) {
		$('#polls').html('');
	
		// loop through each poll
		$.each(data, function(i, poll) {
			$poll_div = $('<div>').attr({'poll':poll['id'], 'class':'poll round_box', 'total_votes':poll['total_votes']});
			$poll_div.append("<h2>"+poll['name']+"</h2>");
			
			$choices_div = $('<div>').addClass('choices-container');
			
			var nd = Date.parse(new Date());
			var ed = Date.parse(poll['end_date']);
			var tdelta = ed - nd;
			
			$.each(poll['choices'], function(j, choice) {
				$c = $('<div>').addClass('choice').attr('votes', choice['votes']);
				$c.append('<p>'+choice['option']+'</p>');
				if (choice['image'] != '') 
					$c.append('<img src="'+choice['image']+'" />');
				$bdiv = $('<div>').css({'margin':'5px 0', 'height':'19px'});
				
				if (tdelta <= 0) {
					var vote_perc = 0;
					if (poll['total_votes'] != 0 || choice['votes'] != 0) vote_perc = (choice['votes']/poll['total_votes'])*100;
					$v_b = $('<div>').addClass('vote-bar');						
					$v_c = $('<div>').addClass('vote-count').css('width', vote_perc+'%');
					$v_b.append($v_c);
					$bdiv.html($v_b);
				}
				else {
					if ($.cookie('p'+poll['id']) == null)
						$bdiv.append('<input type="radio" name="p'+poll['id']+'" value="'+j+'" />');
				}
				
				$c.append($bdiv);
				$choices_div.append($c);
			}); 
			
			$poll_div.append($choices_div);
			$poll_div.append('<div style="clear:both;"></div>');
			
			// time not up
			if (tdelta > 0) {
				if ($.cookie('p'+poll['id']) == null) {
					$vote_button = $('<input>').attr('type', 'button').addClass('poll-vote').val('Vote');
					$vote_button.css({'position':'relative', 'left':'50%', 'margin-left':'-27px'});
					$poll_div.append($vote_button);
				}
				else { 
					$poll_div.append("<div style='text-align:center;'>Thanks for voting!</div>");
				}
			}
			
			$('#polls').append($poll_div);
		});
		
		$('.choice').live('click', function() {
			$(this).parent().children('.choice').removeClass('option-selected');
			$(this).addClass('option-selected');
			$(this).children('div').children('input[type=radio]').attr('checked', 'true');
		});
		
		$('.poll-vote').live('click', function() {
			$poll_div = $(this).parent('.poll');
			$vdiv = $poll_div.children('.choices-container').children('.option-selected').children('div')
			var choice = $vdiv.children('input[type=radio]:checked').val();
			$.get('gateway.php?action=vote', {id:$poll_div.attr('poll'), choice:choice}, function(data) {
				data = $.parseJSON(data);
				if (data.success == 1) {
					var total_votes = Number($poll_div.attr('total_votes'));
					total_votes++;
					
					$.each($poll_div.children('.choices-container').children(), function(i, j) {
						$(this).children('div').html("");
					});
				}
			});	
			$(this).parent().append($('<div>').css('text-align', 'center').text("Thanks for voting!"));
			$(this).remove();
		});
	});
});
</script>

<div id="polls"></div>

<?php /* require_once the footer */ require_once 'templates/footer.php'; ?>