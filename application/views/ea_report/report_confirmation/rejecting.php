<!-- Modal -->
<div class="modal fade" id="reject-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Rejecting TER</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="POST" action="<?= base_url('ea_report/report_confirmation/rejecting') ?>" id="reject-form">
				<input type="text" class="d-none" name="id" id="id" value="<?= $req_id ?>">
				<input type="text" class="d-none" name="approver_id" id="approver_id" value="<?= $approver_id ?>">
				<input type="text" class="d-none" name="status" id="status" value="<?= $status ?>">
				<input type="text" class="d-none" name="level" id="level" value="<?= $level ?>">

				<div class="modal-body">
					<div class="form-group">
						<label for="actual_">Reason</label>
						<textarea class="form-control" name="rejected_reason" id="rejected_reason" rows="2"></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Submit</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	$(document).ready(function () {
		$('#reject-modal').modal('show')
		$(document).on("submit", '#reject-form', function (e) {
			e.preventDefault()
			const formData = new FormData(this);
            const loader = `<div style="width: 5rem; height: 5rem;" class="spinner-border mb-5" role="status"></div>
				<h5 class="mt-2">Please wait</h5>
				<p>Rejecting request and sending email...</p>	`
			Swal.fire({
				title: `Reject TER and send notification email?`,
				text: "",
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: `Yes!`
			}).then((result) => {
				if (result.value) {
					$.ajax({
						type: 'POST',
						url: $(this).attr("action"),
						data: formData,
						beforeSend: function () {
							$('p.error').remove();
							Swal.fire({
								html: loader,
								showConfirmButton: false,
								allowEscapeKey: false,
								allowOutsideClick: false,
							});
						},
						error: function (xhr) {
							const response = xhr.responseJSON;
							if (response.errors) {
								for (const err in response.errors) {
									$(`#${err}`).parent().append(
										`<p class="error mt-1 mb-0">This field is required</p>`
									)
								}
							}
							Swal.fire({
								"title": response.message,
								"text": '',
								"type": "error",
								"confirmButtonClass": "btn btn-dark"
							}).then((result) => {
								if (result.value) {
									window.close();
								}
							});
						},
						success: function (response) {
							Swal.fire({
								"title": "Success!",
								"text": response.message,
								"type": "success",
								"confirmButtonClass": "btn btn-dark"
							}).then((result) => {
								if (result.value) {
									window.close();
								}
							})
						},
						cache: false,
						contentType: false,
						processData: false
					});
				}
			})
		});
	});

</script>
