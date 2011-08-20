<?php require_once('templates/header.php'); ?>

	<script type="text/javascript">
	$(function() {
		$('#poll_load').click(function() {
			var poll_id = $('#poll_id').val();
			$('#myPoll').simplepoll(poll_id);
		});
	});
	</script>
	
	<div class="round_box">
		<input type="text" id="poll_id" placeholder="poll id" /><input type="button" value="Load Poll" id="poll_load" />
		<div id="myPoll"></script>
	</div>

<?php require_once('templates/header.php'); ?>
