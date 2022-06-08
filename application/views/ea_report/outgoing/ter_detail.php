<div class="details-container">
	<div class="kt-portlet">
		<div id="meals_lodging_table" class="kt-portlet__body">
			<div class="kt-infobox">
				<div class="kt-infobox__header border-bottom ml-4 pb-1">
					<h3 class="text-dark fw-600">TER detail <span
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
							<label class="col-5 mb-2 col-form-label fw-bold">Total actual costs</label>
							<div class="col-7">
								<span class="badge badge-pill badge-secondary fw-bold">IDR
									<?= $total_actual_costs ?>
								</span>
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
									<th class="kt-datatable__cell kt-datatable__cell--sort">
										<span style="width: 160px;">Actual cost</span></th>
									<th class="kt-datatable__cell kt-datatable__cell--sort">
										<span style="width: 90px;">Receipt</span></th>
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
										<span style="width: 160px;">
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
								</tr>
								<tr data-row="0" class="kt-datatable__row" style="left: 0px;">
									<td class="kt-datatable__cell fw-bold">
										<span style="width: 120px;">
											Meals
										</span>
									</td>
									<td class="kt-datatable__cell">
										<span style="width: 160px;">
											<span class="badge badge-pill badge-secondary fw-bold lodging_meals_budget">
												<?= (isset($dest['actual_meals_items'][$night-1]['cost']) == '' ? '-' : $dest['actual_meals_items'][$night-1]['d_cost']) ?>
											</span>
											-
											<span class="badge badge-pill badge-secondary fw-bold lodging_meals_budget">
												<?= $dest['meals_text'][$night - 1] ?>
											</span>
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
										<span style="width: 160px;">
											<span class="badge badge-pill badge-secondary fw-bold">
												<?= number_format($items['total_cost'],2,',','.') ?>
											</span>
										</span>
									</td>
									<td class="kt-datatable__cell">
										<span style="width: 90px;">
											<button data-night="<?= $night ?>" data-dest-id="<?= $dest['id'] ?>"
												data-id="<?= $items['id'] ?>"
												data-item-name="<?= $items['item_name'] ?>"
												class="btn btn-items-info btn-sm btn-info">
												<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
													fill="currentColor" class="bi bi-info-circle" viewBox="0 0 16 16">
													<path
														d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
													<path
														d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
												</svg>
												<span class="ml-1">Detail</span>
											</button>
										</span>
									</td>
								</tr>
								<?php endforeach; ?>
								<?php endif; ?>
								<tr style="background-color: #f8f9fa !important;" data-row="0" class="kt-datatable__row"
									style="left: 0px;">
									<td class="kt-datatable__cell fw-bold">
										<span style="width: 120px;">
											<h5 class="text-dark fw-800 m-0">
												Total
											</h5>
										</span>
									</td>
									<td class="kt-datatable__cell">
										<span style="width: 160px;">
											<span class="badge badge-pill badge-secondary fw-bold">
												<?= number_format($dest['total_costs_per_night'][$night-1],2,',','.') ?>
											</span>
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
				<div id="finished_btn" class="ml-3 pl-4">
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
				</div>
			</div>
		</div>
	</div>

	<div class="kt-portlet">
		<div class="kt-portlet__body">
			<div class="kt-infobox">
				<div class="kt-infobox__header border-bottom pb-1">
					<h4 class="text-dark fw-600">TER Status</h4>
				</div>
				<div class="kt-infobox__body">
					<div class="kt-datatable kt-datatable--default kt-datatable--brand kt-datatable--loaded">
						<table class="kt-datatable__table" id="html_table" width="100%" style="display: block;">
							<thead class="kt-datatable__head">
								<tr class="kt-datatable__row" style="left: 0px;">
									<th class="kt-datatable__cell kt-datatable__cell--sort"><span
											style="width: 150px;">Name</span></th>
									<th class="kt-datatable__cell kt-datatable__cell--sort"><span
											style="width: 110px;">Role</span></th>
									<th class="kt-datatable__cell kt-datatable__cell--sort">
										<span style="width: 110px;">Status</span></th>
									<th class="kt-datatable__cell kt-datatable__cell--sort">
										<span style="width: 110px;">Submitted on</span></th>
									<th class="kt-datatable__cell kt-datatable__cell--sort">
										<span style="width: 140px;">Action</span></th>
								</tr>
							</thead>
							<tbody class="kt-datatable__body">
								<tr data-row="0" class="kt-datatable__row" style="left: 0px;">
									<td data-field="Order ID" class="kt-datatable__cell fw-bold">
										<span style="width: 150px;">
											<?= $detail['head_of_units_name'] ?>
										</span>
									</td>
									<td data-field="Status" data-autohide-disabled="false" class="kt-datatable__cell">
										<span style="width: 110px;">
											<span class="kt-badge kt-badge--dark kt-badge--inline kt-badge--pill">
												Head Of Units
											</span>
										</span>
									<td data-field="Status" data-autohide-disabled="false" class="kt-datatable__cell">
										<span style="width: 110px;">
											<span
												class="kt-badge kt-badge--inline kt-badge--pill status-badge"><?= $detail['head_of_units_status_text'] ?></span>
										</span>
									</td>
									<td class="kt-datatable__cell">
										<span style="width: 110px;">
											<?= $detail['head_of_units_status_at'] ?>
										</span>
									</td>
									<td class="kt-datatable__cell">
										<div style="width: 140px;" class="d-flex <?= $head_of_units_btn ?>">
											<button data-level='head_of_units' data-id=<?= $detail['r_id'] ?>
												data-status="2" class="btn btn-status btn-success mr-1">
												<div class="d-flex align-items-center justify-content-center">
													<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
														fill="currentColor" class="bi bi-check" viewBox="0 0 16 16">
														<path
															d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z" />
													</svg>
													Approve
												</div>
											</button>
											<button data-level='head_of_units' data-id=<?= $detail['r_id'] ?>
												data-status="3" class="btn btn-status btn-danger">
												<div class="d-flex align-items-center justify-content-center">
													<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
														fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
														<path
															d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
													</svg>
													Reject
												</div>
											</button>
										</div>
									</td>
								</tr>
								<tr data-row="1" class="kt-datatable__row" style="left: 0px;">
									<td data-field="Order ID" class="kt-datatable__cell fw-bold">
										<span style="width: 150px;">
											<?= $detail['country_director_name'] ?>
										</span>
									</td>
									<td data-field="Status" data-autohide-disabled="false" class="kt-datatable__cell">
										<span style="width: 110px;"><span
												class="kt-badge kt-badge--dark kt-badge--inline kt-badge--pill">
												Country director
											</span>
										</span>
									<td data-field="Status" data-autohide-disabled="false" class="kt-datatable__cell">
										<span style="width: 110px;">
											<span
												class="kt-badge kt-badge--inline kt-badge--pill status-badge"><?= $detail['country_director_status_text'] ?></span>
										</span>
									</td>
									<td class="kt-datatable__cell">
										<span style="width: 110px;">
											<?= $detail['country_director_status_at'] ?>
										</span>
									</td>
									<td class="kt-datatable__cell">
										<div style="width: 140px;" class="d-flex <?= $country_director_btn ?>">
											<button data-level='country_director' data-id=<?= $detail['r_id'] ?>
												data-status="2" class="btn btn-status btn-success mr-1">
												<div class="d-flex align-items-center justify-content-center">
													<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
														fill="currentColor" class="bi bi-check" viewBox="0 0 16 16">
														<path
															d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z" />
													</svg>
													Approve
												</div>
											</button>
											<button data-level='country_director' data-id=<?= $detail['r_id'] ?>
												data-status="3" class="btn btn-status btn-danger">
												<div class="d-flex align-items-center justify-content-center">
													<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
														fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
														<path
															d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
													</svg>
													Reject
												</div>
											</button>
										</div>
									</td>
								</tr>
								<tr data-row="2" class="kt-datatable__row" style="left: 0px;">
									<td data-field="Order ID" class="kt-datatable__cell fw-bold"><span
											style="width: 150px;">
											<?= $detail['finance_name'] ?>
										</span></td>
									<td data-field="Status" data-autohide-disabled="false" class="kt-datatable__cell">
										<span style="width: 110px;"><span
												class="kt-badge kt-badge--dark kt-badge--inline kt-badge--pill">
												Finance teams
											</span>
									<td data-field="Status" data-autohide-disabled="false" class="kt-datatable__cell">
										<span style="width: 110px;">
											<span
												class="kt-badge kt-badge--inline kt-badge--pill status-badge"><?= $detail['finance_status_text'] ?></span>
										</span>
									</td>
									<td class="kt-datatable__cell">
										<span style="width: 110px;">
											<?= $detail['finance_status_at'] ?>
										</span>
									</td>
									<td class="kt-datatable__cell">
										<div style="width: 140px;" class="d-flex <?= $finance_btn ?>">
											<button style="padding: 0.3rem .6rem !important;
													font-size: 0.75rem !important;
													line-height: 1.5 !important;
													border-radius: 0.2rem !important;" data-level='finance' data-id=<?= $detail['r_id'] ?> data-status="2"
												class="btn btn-payment-finance btn-success mr-1">
												<div class="d-flex align-items-center justify-content-center">
													<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
														fill="currentColor" class="bi bi-check" viewBox="0 0 16 16">
														<path
															d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z" />
													</svg>
													Approve
												</div>
											</button>
											<button data-level='finance' data-id=<?= $detail['r_id'] ?> data-status="3"
												class="btn btn-status btn-danger">
												<div class="d-flex align-items-center justify-content-center">
													<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
														fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
														<path
															d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
													</svg>
													Reject
												</div>
											</button>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="ml-3 <?= $submit_btn ?>">
						<button
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
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="payment-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
	aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">TER Payment</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form enctype="multipart/form-data" method="POST" action="<?= base_url('ea_report/incoming/ter_payment') ?>"
				id="ter_payment">
				<div class="modal-body">
					<input type="text" class="d-none" name="req_id" id="req_id" value="<?= $detail['r_id'] ?>">
					<div class="form-group">
						<label for="date_of_transfer">Refund/reimburst</label>
						<select name="payment_type" class="form-control" id="payment_type">
							<option value="">Select refund/reimburst</option>
							<option value="1">Refund</option>
							<option value="2">Reimburst</option>
						</select>
					</div>
					<div class="form-group">
						<label for="date_of_transfer">Cost</label>
						<input type="text" class="form-control" name="total_payment" id="total_payment" value="">
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Submit payment</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	$(document).ready(function () {
		$('#total_payment').number(true, 0, '', '.');
		$('.status-badge').each(function () {
			const status = $(this).text()
			if (status == 'Pending') {
				$(this).addClass('kt-badge--brand')
			} else if (status == 'Approved' || status == 'Done') {
				$(this).addClass('kt-badge--success')
			} else {
				$(this).addClass('kt-badge--danger')
			}
		});

		$(document).on('click', '.btn-items-info', function (e) {
			e.preventDefault()
			const dest_id = $(this).attr('data-dest-id')
			const item_name = $(this).attr('data-item-name')
			const item_id = $(this).attr('data-item-id')
			const night = $(this).attr('data-night')
			$.get(base_url +
				`ea_report/outgoing/other_items_detail?dest_id=${dest_id}&item_name=${item_name}&night=${night}&item_id=${item_id}`,
				function (html) {
					$('#myModal').html(html)
					$('#myModal').modal('show')
				});
		});

		$(document).on('click', '.btn-payment-finance', function (e) {
			e.preventDefault()
			const loader = `<div style="width: 5rem; height: 5rem;" class="spinner-border mb-5" role="status"></div>
			<h5 class="mt-2">Please wait</h5>
			<p>Saving data and sending email to requestor ...</p>`
			$('#payment-modal').modal('show')
			$(document).on("submit", '#ter_payment', function (e) {
				e.preventDefault()
				const formData = new FormData(this);
				Swal.fire({
					title: `Approve TER?`,
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
		});

		$(document).on('click', '.btn-status', function (e) {
			e.preventDefault()
			const id = $(this).attr('data-id')
			const status = $(this).attr('data-status')
			const level = $(this).attr('data-level')
			let confirm_text = 'Approve'
			if (status == 3) {
				confirm_text = 'Reject'
			}
			const loader = `<div style="width: 5rem; height: 5rem;" class="spinner-border mb-5" role="status"></div>
			<h5 class="mt-2">Please wait</h5>
			<p>${confirm_text} TER and sending email ...</p>`
			if (status == 3) {
				$.get(base_url +
					`ea_requests/incoming_requests/get_rejected_modal?id=${id}&status=${status}&level=${level}`,
					function (html) {
						$('#myModal').html(html)
						$('#myModal').modal('show')
					});
				$(document).on("submit", '#reject-form', function (e) {
					e.preventDefault()
					const formData = new FormData(this);
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
								url: base_url +
									'ea_report/incoming/set_status',
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
			} else {
				Swal.fire({
					title: confirm_text + ' TER?',
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
							url: base_url + 'ea_report/incoming/set_status',
							data: {
								id,
								level,
								status
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
			}
		});
		$(document).on('click', '#btn_submit_report', function (e) {
			e.preventDefault()
			const req_id = $(this).attr('data-id')
			const loader = `<div style="width: 5rem; height: 5rem;" class="spinner-border mb-5" role="status"></div>
			<h5 class="mt-2">Please wait</h5>
			<p>Submit TER and sending email ...</p>`
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
	});

</script>
