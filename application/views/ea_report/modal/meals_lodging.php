<style>
	.select2-search__field {
		padding-top: 1rem !important;
	}
</style>

<div class="modal-dialog" role="document">
	<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="exampleModalLabel">Reporting actual
				<?= ($item_type == 1 ? 'Lodging' : 'Meals') ?> cost</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<form enctype="multipart/form-data" method="POST"
			action="<?= base_url('ea_report/outgoing/insert_actual_costs') ?>" id="meals-lodging-form">
			<div class="modal-body">
				<input type="text" class="d-none" name="dest_id" id="dest_id" value="<?= $dest_id ?>">
				<input type="text" class="d-none" name="item_type" id="item_type" value="<?= $item_type ?>">
				<input type="text" class="d-none" name="night" id="night" value="<?= $night ?>">
				<?php if ($item_type == 1): ?>
					<div class="form-group">
						<label for="cost">Actual cost</label>
						<input value="<?= (isset($detail['clean_cost']) ? $detail['clean_cost'] : '') ?>" type="text" class="form-control"
							name="cost" id="cost">
					</div>
				<?php else : ?>
					<div class="form-group d-none">
						<label for="cost">Actual cost</label>
						<input value="<?= $max_meals_budget ?>" type="text" class="form-control"
							name="cost" id="cost">
					</div>
					<div class="form-group">
						<label for="cost">Meals</label>
						<select name="meals[]" multiple="multiple" id="meals" class="form-control meals">
							<option <?= (in_array('-', $meals) ? 'selected' : '') ?> value="-">None</option>
							<option <?= (in_array('B', $meals) ? 'selected' : '') ?> value="B">Breakfast</option>
							<option <?= (in_array('L', $meals) ? 'selected' : '') ?> value="L">Lunch</option>
							<option <?= (in_array('D', $meals) ? 'selected' : '') ?> value="D">Dinner</option>
						</select>
					</div>
				<?php endif; ?>
				<div class="form-group">
					<label for="receipt">Receipt<small class="text-danger">*(pdf|jpg|png|jpeg)</small></label>
					<div class="custom-file">
						<input type="file" class="custom-file-input" name="receipt" id="receipt">
						<label class="custom-file-label" for="receipt">Choose file</label>
					</div>
				</div>
				<?php if ($item_type == 1): ?>
					<h6 class="text-dark mb-2">Max budget: <span
									class="total_current_budget text-danger badge badge-pill badge-secondary fw-bold ml-2">
									<?= number_format($max_lodging_budget,2,',','.')  ?>
									<?= ($country == 1 ? '' : ' / USD ' .  number_format($max_lodging_budget_usd,0,',','.'))  ?>
								</span>
					</h6>
				<?php endif; ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<?php if (isset($detail)): ?>
				<input type="text" class="d-none" name="method_" id="method_" value="PUT">
				<input type="text" class="d-none" name="item_id" id="item_id" value="<?= $detail['id'] ?>">
				<button type="submit" class="btn btn-primary">Update</button>
				<?php else : ?>
				<button type="submit" class="btn btn-primary">Submit</button>
				<?php endif; ?>
			</div>
		</form>
	</div>
</div>

<script type="application/javascript">
	$('input[type="file"]').change(function (e) {
		var fileName = e.target.files[0].name;
		$('.custom-file-label').html(fileName);
	});

	$('#meals').change(function (e) {
		const values = $(this).val()
		if(values.includes('-')) {
			$("#meals option[value='B']").prop("selected", false);
			$("#meals option[value='L']").prop("selected", false);
			$("#meals option[value='D']").prop("selected", false);
		}
	});

	$('.meals').select2({
		placeholder: 'Select meal',
    });

</script>
