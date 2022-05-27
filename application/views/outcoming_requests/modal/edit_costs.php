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
				<div class="form-group">
					<label for="actual_">Lodging</label>
					<input value="<?= $detail['lodging'] ?>" type="text" class="form-control" name="lodging"
						id="lodging">
				</div>
				<div class="form-group">
					<label for="actual_">Meals</label>
					<input value="<?= $detail['meals'] ?>" type="text" class="form-control" name="meals" id="meals">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary">Update</button>
			</div>
		</form>
	</div>
</div>
