<div class="modal-dialog" role="document">
	<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="exampleModalLabel"><?= (is_finance_teams() ? 'Edit' : 'Detail') ?> item</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<form enctype="multipart/form-data" method="POST"
			action="<?= base_url('ea_report/incoming/update_ter_item') ?>" id="ter-item-form">
			<div class="modal-body">
				<div class="form-group">
					<label for="cost">Cost</label>
					<input <?= (!is_finance_teams() ? 'readonly' : '') ?> value="<?= $detail['cost'] ?>" type="text" class="form-control" name="cost" id="cost">
				</div>
				<div class="form-group">
					<label for="cost">Comment</label>
                    <textarea <?= (!is_finance_teams() ? 'readonly' : '') ?> name="comment" class="form-control" id="comment" rows="3"><?= $detail['comment_by_finance'] ?></textarea>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<input type="text" class="d-none" name="method_" id="method_" value="PUT">
				<input type="text" class="d-none" name="id" id="id" value="<?= $detail['id'] ?>">
				<button type="submit" class="btn btn-primary">Update</button>
			</div>
		</form>
	</div>
</div>