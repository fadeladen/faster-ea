<div class="modal-dialog" role="document">
	<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="exampleModalLabel">Submit Payment</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<form enctype="multipart/form-data" method="POST" action="<?= base_url('ea_report/incoming/ter_payment') ?>"
			id="ter_payment">
			<div class="modal-body">
				<input type="text" class="d-none" name="req_id" id="req_id" value="<?= $req_id ?>">
				<input type="text" class="d-none" name="payment_type" id="payment_type" value="<?= ($payment_type == 'Reimburst' ? '2' : '1') ?>">
				<input type="text" class="d-none" name="total_payment" id="total_payment" value="<?= $total_payment ?>">
				<div class="form-group">
					<label for="payment_receipt">Payment receipt <small
							class="text-danger">*(pdf|jpg|png|jpeg)</small></label>
					<div class="custom-file">
						<input type="file" class="custom-file-input" name="payment_receipt" id="payment_receipt">
						<label class="custom-file-label" for="payment_receipt">Choose file</label>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary">Submit payment</button>
			</div>
		</form>
	</div>
</div>

<script>
    $('input[type="file"]').change(function (e) {
		var fileName = e.target.files[0].name;
		$('.custom-file-label').html(fileName);
	});
</script>
