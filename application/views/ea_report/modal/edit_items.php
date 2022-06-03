<div class="modal-dialog" role="document">
	<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="exampleModalLabel">Edit items</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<form enctype="multipart/form-data" method="POST"
			action="<?= base_url('ea_report/outgoing/insert_actual_costs') ?>" id="meals-lodging-form">
			<div class="modal-body">
				<input type="text" class="d-none" name="dest_id" id="dest_id" value="<?= $dest_id ?>">
				<input type="text" class="d-none" name="item_type" id="item_type" value="3">
				<input type="text" class="d-none" name="night" id="night" value="<?= $night ?>">
				<div class="form-group">
					<label for="cost" class="d-block">Item</label>
					<select name="item_name" id="item_name">
						<option value="">Select items</option>
						<option <?= (isset($detail['item_name']) && $detail['item_name'] == 'Ticket Cost' ? 'selected' : '') ?> value="Ticket Cost">Ticket Cost (if purchased by traveler</option>
						<option <?= (isset($detail['item_name']) && $detail['item_name'] == 'Mileage' ? 'selected' : '') ?> value="Mileage">Mileage (# of miles)</option>
						<option <?= (isset($detail['item_name']) && $detail['item_name'] == 'Parking' ? 'selected' : '') ?> value="Parking">Parking</option>
						<option <?= (isset($detail['item_name']) && $detail['item_name'] == 'Airport Tax' ? 'selected' : '') ?> value="Airport Tax">Airport Tax</option>
						<option <?= (isset($detail['item_name']) && $detail['item_name'] == 'Communication' ? 'selected' : '') ?> value="Communication">Communication (phone)</option>
						<option <?= (isset($detail['item_name']) && $detail['item_name'] == 'Registration' ? 'selected' : '') ?> value="Registration">Registration (if paid by traveler)</option>
						<option <?= (isset($detail['item_name']) && $detail['item_name'] == 'Visa Fee' ? 'selected' : '') ?> value="Visa Fee">Visa Fee</option>
						<option <?= (isset($detail['item_name']) && $detail['item_name'] == 'Auto Rental' ? 'selected' : '') ?> value="Auto Rental">Auto Rental</option>
						<option <?= (isset($detail['item_name']) && $detail['item_name'] == 'Internet Charges' ? 'selected' : '') ?> value="Internet Charges">Internet Charges</option>
						<option <?= (isset($detail['item_name']) && $detail['item_name'] == 'Taxi (Home to hotel)' ? 'selected' : '') ?> value="Taxi (Home to hotel)">Taxi (Home to hotel)</option>
						<option <?= (isset($detail['item_name']) && $detail['item_name'] == 'Taxi (Hotel to home)' ? 'selected' : '') ?> value="Taxi (Hotel to home)">Taxi (Hotel to home)</option>
						<option <?= (isset($detail['item_name']) && $detail['item_name'] == 'Other' ? 'selected' : '') ?> value="Other">Other</option>
					</select>
				</div>
				<div class="form-group">
					<label for="cost">Cost</label>
					<input value="<?= (isset($detail) ? $detail['clean_cost'] : '') ?>" type="text" class="form-control" name="cost" id="cost">
				</div>
				<div class="form-group">
					<label for="receipt">Receipt<small class="text-danger">*(pdf|jpg|png|jpeg)</small></label>
					<div class="custom-file">
						<input type="file" class="custom-file-input" name="receipt" id="receipt">
						<label class="custom-file-label" for="receipt">Choose file</label>
					</div>
				</div>
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

    $('#item_name').select2({
			placeholder: 'Select items',
		})

</script>