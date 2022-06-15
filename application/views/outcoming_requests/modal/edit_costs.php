<div class="modal-dialog" role="document">
	<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="exampleModalLabel">Edit cost</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<form enctype="multipart/form-data" method="POST"
			action="<?= base_url('ea_requests/outcoming_requests/update_costs/') ?><?= $detail['id'] ?>"
			id="update-costs">
			<div class="modal-body">
				<div class="form-group <?= ($detail['requestor_id'] != $this->user_data->userId) ? 'd-none' : '' ?>">
					<label for="actual_">Arrival date</label>
					<input value="<?= $detail['arrival_date'] ?>" type="date" class="form-control" name="arrival_date"
						id="arrival_date">
				</div>
				<div class="form-group <?= ($detail['requestor_id'] != $this->user_data->userId) ? 'd-none' : '' ?>">
					<label for="actual_">Departure</label>
					<input value="<?= $detail['departure_date'] ?>" type="date" class="form-control"
						name="departure_date" id="departure_date">
				</div>
				<div
					class="form-group <?= ($detail['requestor_id'] == $this->user_data->userId || $detail['country'] == 1) ? 'd-none' : '' ?>">
					<label for="konversi_usd">Exchange rate</label>
					<input value="<?= $detail['konversi_usd'] ?>" type="text" class="form-control" name="konversi_usd"
						id="konversi_usd">
				</div>
				<div class="form-group row">
					<div class="col-md-8">
						<label for="">Lodging</label>
						<input value="<?= ($detail['is_edited_by_ea'] == 0 ? $detail['lodging'] : $detail['max_lodging_budget']) ?>" type="text" class="form-control" name="lodging"
							id="lodging">
					</div>
					<div class="col-md-4">
						<label for="">USD</label>
						<input readonly value="<?= ($detail['is_edited_by_ea'] == 0 ? $detail['lodging_usd'] : $detail['max_lodging_budget_usd'])  ?>" type="text" class="form-control" name="lodging_usd"
							id="lodging_usd">
					</div>
				</div>
				<div class="form-group row">
					<div class="col-md-8">
						<label for="">Meals</label>
						<input value="<?= ($detail['is_edited_by_ea'] == 0 ? $detail['meals'] : $detail['max_meals_budget']) ?>" type="text" class="form-control" name="meals" id="meals">
					</div>
					<div class="col-md-4">
						<label for="">USD</label>
						<input readonly value="<?= ($detail['is_edited_by_ea'] == 0 ? $detail['meals_usd'] : $detail['max_meals_budget_usd'])  ?>" type="text" class="form-control" name="meals_usd"
							id="meals_usd">
					</div>

				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary">Update</button>
			</div>
		</form>
	</div>
</div>

<script>
	

</script>
