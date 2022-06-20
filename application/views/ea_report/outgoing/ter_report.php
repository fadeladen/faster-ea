<style>
	.details-container span {
		margin-top: 2px !important;
	}

</style>

<div class="details-container">
	<div class="kt-portlet">
		<div id="meals_lodging_table" class="kt-portlet__body">
			<div class="kt-infobox">
				<div class="kt-infobox__header border-bottom ml-4 pb-1">
					<h3 class="text-dark fw-600">TER <span
							class="badge badge-success fw-bold ml-3">#<?= $detail['ea_number'] ?></span></h3>
				</div>

				<div class="kt-infobox">
					<div class="kt-infobox__header border-bottom pb-1">
						<h4 class="text-dark fw-600">Requestor information</h4>
					</div>
					<div class="kt-infobox__body">
						<div class="row">
							<label class="col-5 mb-3 col-form-label fw-bold">Requestor (prepared by)</label>
							<div class="col-7">
								<span style="font-size: 1rem;"
									class="badge badge-light fw-bold"><?= $requestor_data['username'] ?></span>
							</div>
						</div>
						<div class="row">
							<label class="col-5 mb-3 col-form-label fw-bold">Request for</label>
							<div class="col-7">
								<span style="font-size: 1rem;"
									class="badge badge-light fw-bold"><?= $participants ?></span>
							</div>
						</div>
						<div class="row">
							<label class="col-5 mb-3 col-form-label fw-bold">Originating city</label>
							<div class="col-7">
								<span style="font-size: 1rem;"
									class="badge badge-light fw-bold"><?= $detail['originating_city'] ?></span>

							</div>
						</div>
						<div class="row">
							<label class="col-5 mb-3 col-form-label fw-bold">Total advance
								<?= $detail['employment'] == 'Just for me' ? ' (for 1 person)' : ' (for ' . $detail['number_of_participants'] . ' persons)' ?></label>
							<div class="col-7">
								<span class="badge badge-pill badge-secondary fw-bold">IDR
									<?=  $detail['d_total_advance'] ?></span>
							</div>
						</div>
						<div class="row">
							<label class="col-5 mb-3 col-form-label fw-bold">Request date</label>
							<div class="col-7">
								<span class="badge badge-light fw-bold"><?= $detail['payment_date'] ?></span>
							</div>
						</div>
						<div class="p-2 mb-5 border-bottom"></div>
						<div class="kt-datatable kt-datatable--default kt-datatable--brand kt-datatable--loaded">
							<table class="kt-datatable__table mb-5 pb-3" id="html_table" width="100%"
								style="display: block;">
								<thead class="kt-datatable__head">
									<tr class="kt-datatable__row" style="left: 0px;">
										<th class="kt-datatable__cell kt-datatable__cell--sort"><span
												style="width: 110px;">Name</span></th>
										<th class="kt-datatable__cell kt-datatable__cell--sort">
											<span style="width: 140px;">Total Expense</span>
										</th>
										<th class="kt-datatable__cell kt-datatable__cell--sort">
											<span style="width: 90px;">Status</span></th>
										<th class="kt-datatable__cell kt-datatable__cell--sort">
											<span style="width: 180px;">Action</span>
										</th>
									</tr>
								</thead>
								<tbody class="kt-datatable__body">
									<?php foreach ($participants_data as $part): ?>
									<tr data-row="0" class="kt-datatable__row" style="left: 0px;">
										<td data-field="Status" data-autohide-disabled="false"
											class="kt-datatable__cell">
											<span class="fw-bold" style="width: 110px;">
												<?= $part['name'] ?>
											</span>
										</td>
										<td class="kt-datatable__cell">
											<span style="width: 140px;">
												<span class="badge badge-pill badge-secondary fw-bold">
													<?= get_total_expense_by_ter($detail['r_id'], $part['id']) ?>
												</span>
											</span>
										</td>
										<td class="kt-datatable__cell">
											<span style="width: 90px;">
												<?php if (is_ter_report_finished($detail['r_id'], $part['id'])): ?>
												<span
													class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill">
													Complete
												</span>
												<?php else: ?>
												<span class="kt-badge kt-badge--brand kt-badge--inline kt-badge--pill">
													Pending
												</span>
												<?php endif; ?>
											</span>
										</td>
										<td class="kt-datatable__cell">
											<div style="width: 180px;" class="d-flex">
												<a target="_blank"
													href="<?= base_url('ea_report/outgoing/ter_form_by_ter_id?ter_id=') . $part['id'] . '&req_id=' . $detail['r_id']?>"
													class="btn btn-sm btn btn-success">
													<div class="d-flex align-items-center justify-content-center">
														<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
															fill="currentColor" class="bi bi-file-earmark-spreadsheet"
															viewBox="0 0 16 16">
															<path
																d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V9H3V2a1 1 0 0 1 1-1h5.5v2zM3 12v-2h2v2H3zm0 1h2v2H4a1 1 0 0 1-1-1v-1zm3 2v-2h3v2H6zm4 0v-2h3v1a1 1 0 0 1-1 1h-2zm3-3h-3v-2h3v2zm-7 0v-2h3v2H6z" />
														</svg>
														<span class="ml-1">
															Excel
														</span>
													</div>
												</a>
												<a href="<?= base_url('ea_report/outgoing/reporting/') . encrypt($part['id']) ?>"
													class="btn btn-sm btn-danger ml-2">
													<div class="d-flex align-items-center justify-content-center">
														<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
															fill="currentColor" class="bi bi-pencil-square"
															viewBox="0 0 16 16">
															<path
																d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
															<path fill-rule="evenodd"
																d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z" />
														</svg>
														<span class="ml-2">Report</span>
													</div>
												</a>
											</div>
										</td>
									</tr>
									<?php endforeach; ?>
									<tr data-row="0" class="kt-datatable__row" style="left: 0px;">
										<td data-field="Status" data-autohide-disabled="false"
											class="kt-datatable__cell">
											<h5 class="fw-bold mt-3 text-dark" style="width: 110px;">
												Total:
											</h5>
										</td>
										<td class="kt-datatable__cell">
											<span style="width: 140px;">
												<span class="badge badge-pill badge-secondary fw-bold">
													<?= get_total_all_ter_expense($detail['r_id']) ?>
												</span>
											</span>
										</td>
										<td class="kt-datatable__cell">
											<span style="width: 90px;">

											</span>
										</td>
										<td class="kt-datatable__cell">
											<div style="width: 180px;" class="d-flex">

											</div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<?php if ($is_report_finished): ?>
						<div id="finished_btn" class="ml-3 pl-2 mt-3">
							<a target="_blank" href="<?= base_url('ea_report/outgoing/ter_form/') . $detail['r_id'] ?>"
								class="btn btn btn-success">
								<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
									class="bi bi-file-earmark-spreadsheet" viewBox="0 0 16 16">
									<path
										d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V9H3V2a1 1 0 0 1 1-1h5.5v2zM3 12v-2h2v2H3zm0 1h2v2H4a1 1 0 0 1-1-1v-1zm3 2v-2h3v2H6zm4 0v-2h3v1a1 1 0 0 1-1 1h-2zm3-3h-3v-2h3v2zm-7 0v-2h3v2H6z" />
								</svg>
								<span class="ml-1">
									Download Excel
								</span>
							</a>
							<button <?= ($detail['is_ter_submitted'] == 1 ? 'disabled' : '') ?>
								data-id="<?= $detail['r_id'] ?>" type="button" id="btn_submit_report"
								class="btn btn-primary ml-2">
								<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
									class="bi bi-box-arrow-in-right" viewBox="0 0 16 16">
									<path fill-rule="evenodd"
										d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0v-2z" />
									<path fill-rule="evenodd"
										d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z" />
								</svg>
								<span class="ml-1">
									Submit report
								</span>
							</button>
						</div>
						<?php else : ?>
						<div id="report_notes mt-5">
							<p class="ml-3 text-danger">Please complete at least one TER to download excel report</p>
						</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- <div class="kt-portlet">
		<div class="kt-portlet__body">
			<div class="kt-infobox">
				<div class="kt-infobox__header border-bottom pb-1 ml-4">
					<h4 class="text-dark fw-600">Reporting TER</h4>
				</div>
				<div class="kt-infobox__body ml-4">
					<div class="kt-datatable kt-datatable--default kt-datatable--brand kt-datatable--loaded">
						<table class="kt-datatable__table mb-5 pb-3" id="html_table" width="100%"
							style="display: block;">
							<thead class="kt-datatable__head">
								<tr class="kt-datatable__row" style="left: 0px;">
									<th class="kt-datatable__cell kt-datatable__cell--sort"><span
											style="width: 110px;">Name</span></th>
									<th class="kt-datatable__cell kt-datatable__cell--sort">
										<span style="width: 140px;">Total Expense</span>
									</th>
									<th class="kt-datatable__cell kt-datatable__cell--sort">
										<span style="width: 90px;">Status</span></th>
									<th class="kt-datatable__cell kt-datatable__cell--sort">
										<span style="width: 90px;">Action</span>
									</th>
								</tr>
							</thead>
							<tbody class="kt-datatable__body">
								<?php foreach ($participants_data as $part): ?>
								<tr data-row="0" class="kt-datatable__row" style="left: 0px;">
									<td data-field="Status" data-autohide-disabled="false" class="kt-datatable__cell">
										<span class="fw-bold" style="width: 110px;">
											<?= $part['name'] ?>
										</span>
									<td class="kt-datatable__cell">
										<span style="width: 140px;">
											<span class="badge badge-pill badge-secondary fw-bold">
												<?= $part['d_total_expense'] ?>
											</span>
										</span>
									</td>
									<td class="kt-datatable__cell">
										<span style="width: 90px;">
											<span class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill">

											</span>
										</span>
									</td>
									<td class="kt-datatable__cell">
										<div style="width: 90px;" class="d-flex">
											<a href="<?= base_url('ea_report/outgoing/reporting/') . encrypt($detail['r_id']) ?>"
												class="btn btn-sm btn-danger mb-2">
												<div class="d-flex align-items-center justify-content-center">
													<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
														fill="currentColor" class="bi bi-pencil-square"
														viewBox="0 0 16 16">
														<path
															d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
														<path fill-rule="evenodd"
															d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z" />
													</svg>
													<span class="ml-2">Report</span>
												</div>
											</a>
										</div>
									</td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
					<?php if ($is_report_finished): ?>
					<div id="finished_btn" class="ml-3 pl-2 mt-3">
						<a target="_blank" href="<?= base_url('ea_report/outgoing/ter_form/') . $detail['r_id'] ?>"
							class="btn btn btn-success">
							<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
								class="bi bi-file-earmark-spreadsheet" viewBox="0 0 16 16">
								<path
									d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V9H3V2a1 1 0 0 1 1-1h5.5v2zM3 12v-2h2v2H3zm0 1h2v2H4a1 1 0 0 1-1-1v-1zm3 2v-2h3v2H6zm4 0v-2h3v1a1 1 0 0 1-1 1h-2zm3-3h-3v-2h3v2zm-7 0v-2h3v2H6z" />
							</svg>
							<span class="ml-1">
								Download Excel
							</span>
						</a>
						<button <?= ($detail['is_ter_submitted'] == 1 ? 'disabled' : '') ?>
							data-id="<?= $detail['r_id'] ?>" type="button" id="btn_submit_report"
							class="btn btn-primary ml-2">
							<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
								class="bi bi-box-arrow-in-right" viewBox="0 0 16 16">
								<path fill-rule="evenodd"
									d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0v-2z" />
								<path fill-rule="evenodd"
									d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z" />
							</svg>
							<span class="ml-1">
								Submit report
							</span>
						</button>
					</div>
					<?php else : ?>
					<div id="report_notes mt-5">
						<p class="ml-3 text-danger">Please complete all TER to download excel report</p>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div> -->

	<div class="kt-portlet">
		<div class="kt-portlet__body">
			<div class="kt-infobox">
				<div class="kt-infobox__header border-bottom pb-1 ml-4">
					<h4 class="text-dark fw-600">Travel time</h4>
				</div>
				<div class="kt-infobox__body">
					<?php foreach ($detail['destinations'] as $dest): ?>
					<div class="kt-infobox">
						<div class="kt-infobox__header border-bottom pb-1">
							<h4 class="text-dark fw-600"><?= $dest['order'] ?> destination
								<span>(<?= ($dest['country'] == 1 ? $dest['city'] .'/Indonesia' : $dest['city']) ?>,
									<?= $dest['night'] ?> night)</span></h4>
						</div>
						<div class="kt-infobox__body">
							<div class="row mb-2">
								<div class="col-md-6 mt-2">
									<small for="arriv_date" class="col-form-label">
										Arrival date
									</small>
									<input readonly value="<?= $dest['arriv_date'] ?>" class="form-control mt-2"
										type="text" id="arriv_date" name="arriv_date">
								</div>
								<div class="col-md-6 mt-2">
									<small for="departure_date" class="col-form-label">
										Departure date
									</small>
									<input readonly value="<?= $dest['depar_date'] ?>" class="form-control mt-2"
										type="text" id="depar_date" name="depar_date">
								</div>
							</div>
							<div class="my-2 py-2">
								<form class="update-dest-time-form"
									action="<?= base_url('ea_report/outgoing/update_destination_time/') . $dest['id'] ?>">
									<div class="row mt-3">
										<div class="col-md-6">
											<div class="form-group">
												<small class="col-form-label">
													First destination/meeting poin
												</small>
												<input readonly value="<?= $dest['city'] ?>" class="form-control mt-2"
													type="text">
											</div>
											<div class="form-group">
												<small for="first_depar_time" class="col-form-label">
													Departure time
												</small>
												<input value="<?= $dest['first_depar_time'] ?>"
													class="form-control mt-2" type="time" name="first_depar_time">
											</div>
											<div class="form-group">
												<small for="first_arriv_time" class="col-form-label">
													Arrival time
												</small>
												<input value="<?= $dest['first_arriv_time'] ?>"
													class="form-control mt-2" type="time" name="first_arriv_time">
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<small for="arriv_date" class="col-form-label">
													Second destination/meeting poin
												</small>
												<select placeholder="Select city" class="form-control second_city"
													name="second_city">
													<option value="">Select city</option>
													<?php foreach ($cities as $city): ?>
													<option
														<?= ($dest['second_city'] == $city['nama'] ? 'selected' : '') ?>
														value="<?= $city['nama'] ?>"><?= $city['nama'] ?></option>
													<?php endforeach; ?>
												</select>
											</div>
											<div class="form-group">
												<small for="second_depar_time" class="col-form-label">
													Departure time
												</small>
												<input value="<?= $dest['second_depar_time'] ?>"
													class="form-control mt-2" type="time" name="second_depar_time">
											</div>
											<div class="form-group">
												<small for="second_arriv_time" class="col-form-label">
													Arrival time
												</small>
												<input value="<?= $dest['second_arriv_time'] ?>"
													class="form-control mt-2" type="time" name="second_arriv_time">
											</div>
										</div>
									</div>
									<div class="d-flex justify-content-end my-2">
										<button type="submit" class="btn btn-dark">Update</button>
									</div>
								</form>
							</div>
							<div class="p-2 mb-2 border-bottom"></div>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function () {

		$('.second_city').select2({
			placeholder: 'Select city'
		})

		
		$(document).on('click', '#btn_submit_report', function (e) {
			e.preventDefault()
			const req_id = $(this).attr('data-id')
			const loader = `<div style="width: 5rem; height: 5rem;" class="spinner-border mb-5" role="status"></div>
				<h5 class="mt-2">Please wait</h5>
				<p>Submit report and sending email ...</p>`
			Swal.fire({
				title: 'Submit report?',
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
						url: base_url + 'ea_report/outgoing/submit_report',
						data: {
							req_id
						},
						beforeSend: function () {
							Swal.fire({
								html: loader,
								showConfirmButton: false,
								allowEscapeKey: false,
								allowOutsideClick: false,
							});
						},
						error: function (xhr) {
							const response = xhr.responseJSON;
							Swal.fire({
								"title": response.message,
								"text": '',
								"type": "error",
								"confirmButtonClass": "btn btn-dark"
							});
						},
						success: function (response) {
							Swal.fire({
								"title": "Success!",
								"text": response.message,
								"type": "success",
								"confirmButtonClass": "btn btn-dark"
							}).then((result) => {
								console.log(response)
								if (result.value) {
									window.location = base_url +
										'ea_report/outgoing'
								}
							})
						},
					});
				}
			})
		});

		$(document).on("submit", '.update-dest-time-form', function (e) {
			e.preventDefault()
			const formData = new FormData(this);
			const loader = `<div style="width: 5rem; height: 5rem;" class="spinner-border mb-5" role="status"></div>
			<h5 class="mt-2">Please wait</h5>
			<p>Updating data ...</p>`
			Swal.fire({
				title: 'Update destination time?',
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
							Swal.fire({
								html: loader,
								showConfirmButton: false,
								allowEscapeKey: false,
								allowOutsideClick: false,
							});
						},
						error: function (xhr) {
							const response = xhr.responseJSON;
							Swal.fire({
								"title": response.message,
								"text": 'All cost and receipt are required!',
								"type": "error",
								"confirmButtonClass": "btn btn-dark"
							});
						},
						success: function (response) {
							Swal.fire({
								"title": "Success!",
								"text": response.message,
								"type": "success",
								"confirmButtonClass": "btn btn-dark"
							}).then((result) => {
								console.log(response)
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
