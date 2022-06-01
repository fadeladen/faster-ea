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
				<input type="text" class="d-none" name="current_lodging" id="current_lodging" value="<?= $current_lodging ?>">
				<input type="text" class="d-none" name="current_meals" id="current_meals" value="<?= $current_meals ?>">
				<div class="form-group">
					<label for="cost">Actual cost</label>
					<input value="<?= (isset($detail) ? $detail['clean_cost'] : '') ?>" type="text" class="form-control"
						name="cost" id="cost">
				</div>
				<div class="form-group">
					<label for="receipt">Receipt<small class="text-danger">*(pdf|jpg|png|jpeg)</small></label>
					<div class="custom-file">
						<input type="file" class="custom-file-input" name="receipt" id="receipt">
						<label class="custom-file-label" for="receipt">Choose file</label>
					</div>
				</div>
				<?php if ($item_type == 1): ?>
					<h6 class="text-dark mb-2">Max budget: <span
									class="total_current_budget badge badge-pill badge-secondary fw-bold ml-2"><?= number_format($max_lodging_budget,2,',','.')  ?></span>
					</h6>
				<?php endif; ?>
				<!-- <div class="form-group row border-top py-3 border-bottom">
					<div class="col-md-6">
						<label class="text-danger">Max lodging and meals budget</label>
						<input readonly class="form-control" type="text" value="<?= $max_budget ?>" name="max_budget"
							id="max_budget">
					</div>
					<div class="col-md-6">
						<label>Total lodging and meals</label>
						<input readonly class="form-control" type="text" value="<?= $current_budget ?>" name="current_budget"
							id="current_budget">
					</div>
				</div> -->
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

</script>
