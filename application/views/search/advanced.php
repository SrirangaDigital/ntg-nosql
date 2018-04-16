<div class="container gap-above-med">
	<div class="row">
		<div class="col-md-2"></div>
		<div class="col-md-8">
			<h2>Advanced Search</h2>
		</div>
		<div class="col-md-2"></div>
	</div>
	<div class="row gap-above-med">
		<div class="col-md-2"></div>
		<div class="col-md-8">
			<form method="GET" class="form-inline updateDataArchive" role="form" id="updateData" action="<?=BASE_URL?>search/field" onsubmit="return validate()">
				<div class="keyValuePair">	
					<div class="form-group">
						<select class="keySelect form-control">
							<option value="">Select Key</option>
							<?php 	foreach ($data as $key) {	?>
							<option value="<?=$key?>"><?=$key?></option>
							<?php } ?>
						</select>
						<input type="text" class="form-control edit value" placeholder="Enter Value" />
					</div>					
				</div>	
				<i class="fa fa-plus" title="Add new field" id="addKeyValue"></i>
				<input class="updateSubmit" type="submit" id="submit" value="Search" />
			</form>    
		</div>
		<div class="col-md-2"></div>
	</div>
</div>

<script>
$(document).ready(function() {

	$('body').on('click', '#addKeyValue', function(){

		$(".keyValuePair").append('<div class="form-group">' + $('.keyValuePair .form-group').first().html() + '</div>');
	});

	$('body').on('change', '.keySelect', function(){

		var key = $(this).val();
		$(this).next('input.edit.value').attr('name', key);
	});
});	
</script>
