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
					<h3 class="text-dark fw-600">Reporting TER <span
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
							<label class="col-5 mb-3 col-form-label fw-bold">Requestor for</label>
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
									<?= $total_advance ?></span>
							</div>
						</div>
						<div class="row">
							<label class="col-5 mb-3 col-form-label fw-bold">Request date</label>
							<div class="col-7">
								<span class="badge badge-light fw-bold"><?= $detail['payment_date'] ?></span>
							</div>
						</div>
						<div class="p-2 mb-2 border-bottom"></div>
					</div>
				</div>

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
								<input readonly value="<?= $dest['arriv_date'] ?>" class="form-control mt-2" type="text"
									id="arriv_date" name="arriv_date">
							</div>
							<div class="col-md-6 mt-2">
								<small for="departure_date" class="col-form-label">
									Departure date
								</small>
								<input readonly value="<?= $dest['depar_date'] ?>" class="form-control mt-2" type="text"
									id="depar_date" name="depar_date">
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
											<input value="<?= $dest['first_depar_time'] ?>" class="form-control mt-2"
												type="time" name="first_depar_time">
										</div>
										<div class="form-group">
											<small for="first_arriv_time" class="col-form-label">
												Arrival time
											</small>
											<input value="<?= $dest['first_arriv_time'] ?>" class="form-control mt-2"
												type="time" name="first_arriv_time">
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
												<option <?= ($dest['second_city'] == $city['nama'] ? 'selected' : '') ?>
													value="<?= $city['nama'] ?>"><?= $city['nama'] ?></option>
												<?php endforeach; ?>
											</select>
										</div>
										<div class="form-group">
											<small for="second_depar_time" class="col-form-label">
												Departure time
											</small>
											<input value="<?= $dest['second_depar_time'] ?>" class="form-control mt-2"
												type="time" name="second_depar_time">
										</div>
										<div class="form-group">
											<small for="second_arriv_time" class="col-form-label">
												Arrival time
											</small>
											<input value="<?= $dest['second_arriv_time'] ?>" class="form-control mt-2"
												type="time" name="second_arriv_time">
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
					<?php $day = 0 ?>
					<?php for ($night = 1; $night <= $dest['night']; $night++): ?>
					<?php
						$total_cost_per_night = $dest['actual_lodging_items'][$night-1]['cost'] + $dest['actual_meals_items'][$night-1]['cost'];
					?>
					<div class="py-2 border-bottom ml-2">
						<h5 class="text-dark fw-bold">
							<?= ordinal($night) ?> night:
						</h5>
					</div>
					<div
						class="kt-datatable kt-datatable--default kt-datatable--brand kt-datatable--loaded border-bottom">
						<table class="kt-datatable__table" id="html_table" width="100%" style="display: block;">
							<thead class="kt-datatable__head">
								<tr class="kt-datatable__row" style="left: 0px;">
									<th class="kt-datatable__cell kt-datatable__cell--sort"><span
											style="width: 120px;">Item</span></th>
									<th class="kt-datatable__cell kt-datatable__cell--sort"><span
											style="width: 110px;">Cost</span></th>
									<th class="kt-datatable__cell kt-datatable__cell--sort">
										<span style="width: 110px;">Actual cost</span></th>
									<th class="kt-datatable__cell kt-datatable__cell--sort">
										<span style="width: 90px;">Receipt</span></th>
									<th class="kt-datatable__cell kt-datatable__cell--sort">
										<span style="width: 90px;">Action</span></th>
								</tr>
							</thead>
							<tbody class="kt-datatable__body">
								<tr data-row="0" class="kt-datatable__row" style="left: 0px;">
									<td class="kt-datatable__cell fw-bold">
										<span style="width: 120px;">
											Lodging
										</span>
									</td>
									<td class="kt-datatable__cell">
										<span style="width: 110px;">
											<span class="badge badge-pill badge-secondary fw-bold">
												<?= $dest['max_lodging_cost']?>
											</span>
										</span>
									</td>
									<td class="kt-datatable__cell">
										<span style="width: 110px;">
											<span
												class="badge badge-pill badge-secondary fw-bold lodging_meals_budget <?= $dest['actual_lodging_items'][$night-1]['d_cost'] == '' ? 'text-danger' : '' ?>">
												<?= (isset($dest['actual_lodging_items'][$night-1]['cost']) == '' ? 'null' : $dest['actual_lodging_items'][$night-1]['d_cost']) ?>
											</span>
										</span>
									</td>
									<td class="kt-datatable__cell">
										<span style="width: 90px;">
											<?php if (isset($dest['actual_lodging_items'][$night-1]['receipt']) == null): ?>
											<span class="badge badge-pill badge-secondary fw-bold">
												-
											</span>
											<?php else : ?>
											<a target="_blank" class="badge badge-warning text-light"
												href="<?= base_url('uploads/ea_items_receipt/') ?><?= $dest['actual_lodging_items'][$night-1]['receipt']  ?>">
												<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
													fill="currentColor" class="bi bi-card-image" viewBox="0 0 16 16">
													<path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z" />
													<path
														d="M1.5 2A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13zm13 1a.5.5 0 0 1 .5.5v6l-3.775-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12v.54A.505.505 0 0 1 1 12.5v-9a.5.5 0 0 1 .5-.5h13z" />
												</svg>
											</a>
											<?php endif; ?>
										</span>
									</td>
									<td class="kt-datatable__cell">
										<span style="width: 90px;">
											<button
												data-item-id="<?= (isset($dest['actual_lodging_items'][$night-1]['id']) ? $dest['actual_lodging_items'][$night-1]['id'] : 0) ?>"
												data-item-type="1" data-night="<?= $night ?>"
												data-dest-id="<?= $dest['id'] ?>"
												class="btn btn-meals-lodging btn-sm btn-info">
												<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
													fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
													<path
														d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
													<path fill-rule="evenodd"
														d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z" />
												</svg>
												<span class="ml-1">Edit</span>
											</button>
										</span>
									</td>
								</tr>
								<tr data-row="0" class="kt-datatable__row" style="left: 0px;">
									<td class="kt-datatable__cell fw-bold">
										<span style="width: 120px;">
											Meals
											<?= ($night == 1 || $dest['meals_text'][$night-1]['is_first_day'] == 1 || $dest['meals_text'][$night-1]['is_last_day'] == 1  ? '(75%)' : '') ?>
										</span>
									</td>
									<td class="kt-datatable__cell">
										<span style="width: 110px;">
											<span class="badge badge-pill badge-secondary fw-bold">
												<?= $dest['max_meals_cost']?>
											</span>
										</span>
									</td>
									<td class="kt-datatable__cell">
										<span style="width: 110px;">
											<span class="badge badge-pill badge-secondary fw-bold lodging_meals_budget <?= $dest['meals_text'][$night-1]['d_cost'] == '' ? 'text-danger' : '' ?>">
												<?= ($dest['meals_text'][$night-1]['d_cost'] == '' ? 'null' : $dest['meals_text'][$night-1]['d_cost']) ?>
											</span>
										</span>
									</td>
									<td class="kt-datatable__cell">
										<span style="width: 90px;">
											<span class="badge badge-pill badge-secondary fw-bold lodging_meals_budget">
												<?= $dest['meals_text'][$night - 1]['meals_text'] ?>
											</span>
										</span>
									</td>
									<td class="kt-datatable__cell">
										<span style="width: 90px;">
											<button
												data-item-id="<?= (isset($dest['actual_meals_items'][$night-1]['id']) ? $dest['actual_meals_items'][$night-1]['id'] : 0) ?>"
												data-item-type="2" data-night="<?= $night ?>"
												data-dest-id="<?= $dest['id'] ?>"
												class="btn btn-meals-lodging btn-sm btn-info">
												<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
													fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
													<path
														d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
													<path fill-rule="evenodd"
														d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z" />
												</svg>
												<span class="ml-1">Edit</span>
											</button>
										</span>
									</td>
								</tr>
								<?php if (!empty($dest['other_items_by_name'][$night-1])): ?>
								<?php foreach ($dest['other_items_by_name'][$night-1] as $items): ?>
								<tr data-row="0" class="kt-datatable__row" style="left: 0px;">
									<td class="kt-datatable__cell fw-bold">
										<span style="width: 120px;">
											<?= (isset($items['item_name']) ? $items['item_name'] : '')  ?>
										</span>
									</td>
									<td class="kt-datatable__cell">
										<span style="width: 110px;">

										</span>
									</td>
									<td class="kt-datatable__cell">
										<span style="width: 110px;">
											<span class="badge badge-pill badge-secondary fw-bold">
												<?= (isset($items['total_cost']) ? number_format($items['total_cost'],2,',','.') : '')  ?>
											</span>
										</span>
									</td>
									<td class="kt-datatable__cell">
										<span style="width: 90px;">
											-
										</span>
									</td>
									<td class="kt-datatable__cell">
										<span style="width: 90px;">
											<div class="d-flex flex-column">
												<button data-night="<?= $night ?>" data-dest-id="<?= $dest['id'] ?>"
													data-id="<?= $items['id'] ?>"
													data-item-name="<?= $items['item_name'] ?>"
													class="btn btn-items-info btn-sm btn-info">
													<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
														fill="currentColor" class="bi bi-info-circle"
														viewBox="0 0 16 16">
														<path
															d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
														<path
															d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
													</svg>
													<span class="ml-1">Detail</span>
												</button>
											</div>
										</span>
									</td>
								</tr>
								<?php endforeach; ?>
								<?php endif; ?>
								<tr data-row="0" class="kt-datatable__row" style="left: 0px;">
									<td class="kt-datatable__cell fw-bold">
										<span style="width: 120px;">

										</span>
									</td>
									<td class="kt-datatable__cell">
										<span style="width: 110px;">
										</span>
									</td>
									<td class="kt-datatable__cell">
										<span style="width: 110px;">
										</span>
									</td>
									<td class="kt-datatable__cell">
										<span style="width: 90px;">

										</span>
									</td>
									<td class="kt-datatable__cell">
										<span style="width: 90px;">
											<button data-id="0" data-night="<?= $night ?>"
												data-dest-id="<?= $dest['id'] ?>"
												class="btn btn-add-items btn-sm btn-success">
												Add items
											</button>
										</span>
									</td>
								</tr>
								<tr data-row="0" style="background-color: #f8f9fa !important;"
									class="kt-datatable__row border-top" style="left: 0px;">
									<td class="kt-datatable__cell fw-bold">
										<span style="width: 120px;">
											<h5 class="text-dark fw-800 m-0">
												Sub total
											</h5>
										</span>
									</td>
									<td class="kt-datatable__cell">
										<span style="width: 110px;">
										</span>
									</td>
									<td class="kt-datatable__cell">
										<span style="width: 110px;">
											<span class="badge badge-pill badge-secondary fw-bold">
												<?= number_format($dest['total_costs_per_night'][$night-1],2,',','.') ?>
											</span>
										</span>
									</td>
									<td class="kt-datatable__cell">
										<span style="width: 90px;">

										</span>
									</td>
									<td class="kt-datatable__cell">
										<span style="width: 90px;">
										</span>
									</td>
								</tr>

							</tbody>
						</table>
					</div>
					<?php endfor; ?>
				</div>
				<?php endforeach; ?>
				<div class="p-2 ml-3 pb-2 pt-3 border-bottom border-top">
					<h4>Summarized:</h4>
					<p class="fw-bold">
						Total advance receive :
						<span class="badge badge-pill badge-secondary fw-bold">
							<?= $total_advance ?>
						</span>
					</p>
					<p class="fw-bold">
						Total travel expense :
						<span class="badge badge-pill badge-secondary fw-bold">
							<?= $total_expense ?>
						</span>
					</p>
					<p class="fw-bold">
						<?= $refund_or_reimburst['status'] ?> :
						<span class="badge badge-pill badge-secondary fw-bold">
							<?= number_format($refund_or_reimburst['total'],2,',','.') ?>
						</span>
					</p>
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
					<button <?= ($detail['is_ter_submitted'] == 1 ? 'disabled' : '') ?> data-id="<?= $detail['r_id'] ?>"
						type="button" id="btn_submit_report" class="btn btn-primary ml-2">
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
				<div id="report_notes">
					<p class="pl-3 ml-3 text-danger">Please report all meals and lodging actual costs to
						download excel report</p>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function () {

		$('.second_city').select2({
			placeholder: 'Select city'
		})

		$(document).on('click', '.btn-meals-lodging', function (e) {
			e.preventDefault()
			const dest_id = $(this).attr('data-dest-id')
			const item_type = $(this).attr('data-item-type')
			const item_id = $(this).attr('data-item-id')
			const night = $(this).attr('data-night')
			const max_budget = $(this).attr('data-max-budget')
			const current_lodging_budget = $(this).attr('data-current-lodging')
			const current_meals_budget = $(this).attr('data-current-meals')
			$.get(base_url +
				`ea_report/outgoing/meals_lodging_modal?dest_id=${dest_id}&item_type=${item_type}&night=${night}&item_id=${item_id}&max_budget=${max_budget}&current_meals_budget=${current_meals_budget}&current_lodging_budget=${current_lodging_budget}`,
				function (html) {
					$('#myModal').html(html)
					$('#cost').number(true, 0, '', '.');
					$('#max_budget').number(true, 0, '', '.');
					$('#current_budget').number(true, 0, '', '.');
					$('#myModal').modal('show')
				});
		});
		$(document).on('click', '.btn-add-items', function (e) {
			e.preventDefault()
			const dest_id = $(this).attr('data-dest-id')
			const night = $(this).attr('data-night')
			const item_id = $(this).attr('data-id')
			$.get(base_url +
				`ea_report/outgoing/add_items_modal?dest_id=${dest_id}&night=${night}&item_id=${item_id}`,
				function (html) {
					$('#myModal').html(html)
					$('#cost').number(true, 0, '', '.');
					$('#myModal').modal('show')
				});
		});
		$(document).on('click', '.btn-edit-items', function (e) {
			e.preventDefault()
			const dest_id = $(this).attr('data-dest-id')
			const night = $(this).attr('data-night')
			const item_id = $(this).attr('data-id')
			$.get(base_url +
				`ea_report/outgoing/edit_items_modal?dest_id=${dest_id}&night=${night}&item_id=${item_id}`,
				function (html) {
					$('#myModal').html(html)
					$('#cost').number(true, 0, '', '.');
					$('#myModal').modal('show')
				});
		});
		$(document).on('click', '.btn-delete-items', function (e) {
			e.preventDefault()
			const id = $(this).attr('data-id')
			Swal.fire({
				title: 'Delete item?',
				text: "",
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: `Yes!`
			}).then((result) => {
				if (result.value) {
					$.get(base_url + `ea_report/outgoing/delete_other_items/${id}`,
						function (response) {
							if (response.success) {
								Swal.fire({
									"title": "Success!",
									"text": response.message,
									"type": "success",
									"confirmButtonClass": "btn btn-dark"
								}).then((result) => {
									if (result.value) {
										location.reload();
									}
								})
							} else {
								Swal.fire({
									"title": response.message,
									"text": '',
									"type": "error",
									"confirmButtonClass": "btn btn-dark"
								});
							}
						});
				}
			})
		});

		const loader = `<div style="width: 5rem; height: 5rem;" class="spinner-border mb-5" role="status"></div>
			<h5 class="mt-2">Please wait</h5>
			<p>Saving data ...</p>`

		$(document).on('click', '#btn_submit_report', function (e) {
			e.preventDefault()
			const req_id = $(this).attr('data-id')
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

		$(document).on("submit", '#meals-lodging-form', function (e) {
			e.preventDefault()
			const formData = new FormData(this);
			Swal.fire({
				title: 'Reporting actual costs?',
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
							$('p.error').remove()
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
									let eId = err
									if (err == 'meals[]') {
										eId = 'meals'
									}
									$(`#${eId}`).parent().append(
										`<p class="error mt-1 mb-0">This field is required</p>`
									)
								}
							}
							if (response.max_budget_error) {
								Swal.fire({
									"title": response.message,
									"text": response.max_budget_message,
									"type": "error",
									"confirmButtonClass": "btn btn-dark"
								});
							} else {
								Swal.fire({
									"title": response.message,
									"text": '',
									"type": "error",
									"confirmButtonClass": "btn btn-dark"
								});
							}
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
									location.reload();
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

		$(document).on('click', '#btn-add-more-items', function (e) {
			e.preventDefault()
			$('#items-lists').append(`<div class="row">
						<div class="col-md-6 form-group">
							<label for="cost">Cost</label>
							<input value="" type="text" class="form-control cost"
								name="cost[]">
						</div>
						<div class="col-md-5 form-group">
							<label for="receipt">Receipt<small class="text-danger">*(pdf|jpg|png|jpeg)</small></label>
							<div class="custom-file receipt">
								<input type="file" class="custom-file-input" name="receipt[]">
								<label class="custom-file-label" for="receipt">Choose file</label>
							</div>
						</div>
						<div class="col-1 d-flex align-items-center">
							<button class="btn btn-sm btn-danger btn-delete-other-item">
								<svg xmlns="http://www.w3.org/2000/svg" width="16"
									height="16" fill="currentColor" class="bi bi-trash"
									viewBox="0 0 16 16">
									<path
										d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z" />
									<path fill-rule="evenodd"
										d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z" />
								</svg>
							</button>
						</div>
					</div>`)
			$('.cost').number(true, 0, '', '.');
			$('input[type="file"]').change(function (e) {
				var fileName = e.target.files[0].name;
				$(this).next('.custom-file-label').html(fileName);
			});
		})

		$(document).on('click', '.btn-delete-other-item', function () {
			$(this).parent().parent().remove()
		});

		$(document).on("submit", '#other-items-form', function (e) {
			e.preventDefault()
			const formData = new FormData(this);
			Swal.fire({
				title: 'Reporting actual costs?',
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
								if (result.value) {
									location.reload();
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

		$(document).on("submit", '.update-dest-time-form', function (e) {
			e.preventDefault()
			const formData = new FormData(this);
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

		$(document).on('click', '.btn-items-info', function (e) {
			e.preventDefault()
			const dest_id = $(this).attr('data-dest-id')
			const item_name = $(this).attr('data-item-name')
			const item_id = $(this).attr('data-item-id')
			const night = $(this).attr('data-night')
			$.get(base_url +
				`ea_report/outgoing/edit_other_items_modal?dest_id=${dest_id}&item_name=${item_name}&night=${night}&item_id=${item_id}`,
				function (html) {
					$('#myModal').html(html)
					$('#myModal').modal('show')
				});
		});
	});

</script>
