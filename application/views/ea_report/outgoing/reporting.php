<div class="details-container">
	<div class="kt-portlet">
		<div id="meals_lodging_table" class="kt-portlet__body">
			<div class="kt-infobox">
				<div class="kt-infobox__header border-bottom ml-4 pb-1">
					<h3 class="text-dark fw-600">Reporting EA request <span
							class="badge badge-success fw-bold ml-3">#<?= $detail['ea_number'] ?></span></h3>
				</div>

				<div class="kt-infobox">
					<div class="kt-infobox__header border-bottom pb-1">
						<h4 class="text-dark fw-600">Requestor information</h4>
					</div>
					<div class="kt-infobox__body">
						<div class="row">
							<label class="col-5 mb-2 col-form-label fw-bold">Name</label>
							<div class="col-7">
								<span style="font-size: 1rem;"
									class="badge badge-light fw-bold"><?= $requestor_data['username'] ?></span>
							</div>
						</div>
						<div class="row">
							<label class="col-5 mb-2 col-form-label fw-bold">Division</label>
							<div class="col-7">
								<span class="badge badge-dark fw-bold"><?= $requestor_data['unit_name'] ?></span>
							</div>
						</div>
						<div class="row mb-2">
							<label class="col-5 mb-2 col-form-label fw-bold">Purpose</label>
							<div class="col-7">
								<textarea readonly class="form-control" id=""
									rows="2"><?= $detail['purpose'] ?></textarea>
							</div>
						</div>
						<div class="row">
							<label class="col-5 mb-2 col-form-label fw-bold">Total cost (meals and lodging x
								night)</label>
							<div class="col-7">
								<span class="badge badge-pill badge-secondary fw-bold">IDR
									<?= number_format($detail['total_destinations_cost'],2,',','.') ?></span>
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
						<div class="p-2 mb-2 border-bottom"></div>
					</div>
					<?php $day = 0 ?>
					<?php for ($night = 1; $night <= $dest['night']; $night++): ?>
					<?php
						$total_cost_per_night = $dest['actual_lodging_items'][$night-1]['cost'] + $dest['actual_meals_items'][$night-1]['cost'];
					?>
					<div class="py-2 border-bottom ml-2">
						<h5 class="text-dark fw-bold">
							<?= ordinal($night) ?> night,
							<?= date('d M Y',strtotime($dest['d_arriv_date'] . "+" . $day++ ." days")) ?>
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
												<?= $dest['d_lodging'] ?>
											</span>
										</span>
									</td>
									<td class="kt-datatable__cell">
										<span style="width: 110px;">
											<span
												class="badge badge-pill badge-secondary fw-bold lodging_meals_budget"><?= (isset($dest['actual_lodging_items'][$night-1]['cost']) == '' ? '-' : $dest['actual_lodging_items'][$night-1]['d_cost']) ?></span>
										</span>
									</td>
									<td class="kt-datatable__cell">
										<span style="width: 90px;">
											<?php if (isset($dest['actual_lodging_items'][$night-1]['cost']) == null): ?>
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
										</span>
									</td>
									<td class="kt-datatable__cell">
										<span style="width: 110px;">
											<span class="badge badge-pill badge-secondary fw-bold">
												<?= $dest['d_meals'] ?>
											</span>
										</span>
									</td>
									<td class="kt-datatable__cell">
										<span style="width: 110px;">
											<span
												class="badge badge-pill badge-secondary fw-bold lodging_meals_budget"><?= (isset($dest['actual_meals_items'][$night-1]['cost']) == '' ? '-' : $dest['actual_meals_items'][$night-1]['d_cost']) ?></span>
										</span>
									</td>
									<td class="kt-datatable__cell">
										<span style="width: 90px;">
											<?php if (isset($dest['actual_meals_items'][$night-1]['receipt']) == null): ?>
											<span class="badge badge-pill badge-secondary fw-bold">
												-
											</span>
											<?php else : ?>
											<a target="_blank" class="badge badge-warning text-light"
												href="<?= base_url('uploads/ea_items_receipt/') ?><?= $dest['actual_meals_items'][$night-1]['receipt'] ?>">
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
								<?php if (!empty($dest['other_items'][$night-1])): ?>
								<?php foreach ($dest['other_items'][$night-1] as $items): ?>
								<tr data-row="0" class="kt-datatable__row" style="left: 0px;">
									<td class="kt-datatable__cell fw-bold">
										<span style="width: 120px;">
											<?= $items['item_name'] ?>
										</span>
									</td>
									<td class="kt-datatable__cell">
										<span style="width: 110px;">

										</span>
									</td>
									<td class="kt-datatable__cell">
										<span style="width: 110px;">
											<span class="badge badge-pill badge-secondary fw-bold">
												<?= $items['d_cost'] ?>
											</span>
										</span>
									</td>
									<td class="kt-datatable__cell">
										<span style="width: 90px;">
											<a target="_blank" class="badge badge-warning text-light"
												href="<?= base_url('uploads/ea_items_receipt/') ?><?= $items['receipt'] ?>">
												<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
													fill="currentColor" class="bi bi-card-image" viewBox="0 0 16 16">
													<path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z" />
													<path
														d="M1.5 2A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13zm13 1a.5.5 0 0 1 .5.5v6l-3.775-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12v.54A.505.505 0 0 1 1 12.5v-9a.5.5 0 0 1 .5-.5h13z" />
												</svg>
											</a>
										</span>
									</td>
									<td class="kt-datatable__cell">
										<span style="width: 90px;">
											<div class="d-flex flex-column">
												<button data-night="<?= $night ?>" data-dest-id="<?= $dest['id'] ?>"
													data-id="<?= $items['id'] ?>"
													class="btn btn-add-items btn-sm btn-info">
													<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
														fill="currentColor" class="bi bi-pencil-square"
														viewBox="0 0 16 16">
														<path
															d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
														<path fill-rule="evenodd"
															d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z" />
													</svg>
													<span class="ml-1">Edit</span>
												</button>
												<button data-id="<?= $items['id'] ?>"
													class="btn btn-delete-items btn-sm btn-danger mt-1">
													<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
														fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
														<path
															d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z" />
														<path fill-rule="evenodd"
															d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z" />
													</svg>
													<span class="ml-1">Delete</span>
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
							</tbody>
						</table>
						<div class="ml-2 py-3 border-bottom d-flex">
							<!-- <h6 class="text-danger mr-2">Max lodging and meals budget: <span
									class="badge badge-pill badge-secondary fw-bold"
									id="max-budget"><?= $dest['d_total_lodging_and_meals'] ?></span></h6> -->
							<h6 class="text-dark mb-2">Total actual lodging and meals: <span
									class="total_current_budget badge badge-pill badge-secondary fw-bold"><?= number_format($total_cost_per_night,2,',','.')  ?></span>
							</h6>
						</div>
					</div>
					<?php endfor; ?>
				</div>
				<?php endforeach; ?>
				<div id="finished_btn" class="ml-3 pl-4">
					<a target="_blank" href="<?= base_url('ea_report/outgoing/excel_report/') . $detail['r_id'] ?>"
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
					<button <?= ($detail['is_ter_submitted'] == 1 ? 'disabled' : '') ?> data-id="<?= $detail['r_id'] ?>" type="button" id="btn_submit_report"
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
				<div id="report_notes">
					<p class="pl-3 ml-3 text-danger">Please report all meals and lodging actual costs to
						download excel report</p>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function () {
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
									location.reload();
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

		const reportIsFinished = () => {
			let valid = true

			$('#meals_lodging_table .lodging_meals_budget').each(function () {
				const budget = $(this).text()
				if (budget == '-') {
					valid = false;
				}
			});
			if (!valid) {
				$('#finished_btn').addClass('d-none')
				$('#report_notes').removeClass('d-none')
			} else {
				$('#finished_btn').removeClass('d-none')
				$('#report_notes').addClass('d-none')
			}
		}
		reportIsFinished()
	});

</script>
