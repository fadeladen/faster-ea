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
							<label class="col-5 mb-2 col-form-label fw-bold">Requestor (prepared by)</label>
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
							<label class="col-5 mb-2 col-form-label fw-bold">Request for</label>
							<div class="col-7">
								<span style="font-size: 1rem;"
									class="badge badge-light fw-bold"><?= $participants ?></span>
							</div>
						</div>
						<div class="row">
							<label class="col-5 mb-2 col-form-label fw-bold">Total advance</label>
							<div class="col-7">
								<span class="badge badge-pill badge-secondary fw-bold">IDR
									<?= $detail['d_total_advance'] ?>
								</span>
							</div>
						</div>
						<div class="row">
							<label class="col-5 mb-2 col-form-label fw-bold">Actual expense</label>
							<div class="col-7">
								<span class="badge badge-pill badge-secondary fw-bold">IDR
									<?= get_total_all_ter_expense($detail['r_id']) ?>
								</span>
							</div>
						</div>
						<div class="p-2 mb-2 border-bottom"></div>
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
											<span style="width: 180px;">
												<div class="d-flex">
													<a target="_blank"
														href="<?= base_url('ea_report/outgoing/ter_form_by_ter_id?ter_id=') . $part['id'] . '&req_id=' . $detail['r_id']?>"
														class="btn btn-sm btn btn-success">
														<div class="d-flex align-items-center justify-content-center">
															<svg xmlns="http://www.w3.org/2000/svg" width="14"
																height="14" fill="currentColor"
																class="bi bi-file-earmark-spreadsheet"
																viewBox="0 0 16 16">
																<path
																	d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V9H3V2a1 1 0 0 1 1-1h5.5v2zM3 12v-2h2v2H3zm0 1h2v2H4a1 1 0 0 1-1-1v-1zm3 2v-2h3v2H6zm4 0v-2h3v1a1 1 0 0 1-1 1h-2zm3-3h-3v-2h3v2zm-7 0v-2h3v2H6z" />
															</svg>
															<small class="ml-1">
																Excel
															</small>
														</div>
													</a>
												</div>
											</span>
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
							<div id="finished_btn" class="ml-3 pl-2 mt-3">
								<a target="_blank"
									href="<?= base_url('ea_report/outgoing/ter_form/') . $detail['r_id'] ?>"
									class="btn btn btn-success">
									<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
										class="bi bi-file-earmark-spreadsheet" viewBox="0 0 16 16">
										<path
											d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V9H3V2a1 1 0 0 1 1-1h5.5v2zM3 12v-2h2v2H3zm0 1h2v2H4a1 1 0 0 1-1-1v-1zm3 2v-2h3v2H6zm4 0v-2h3v1a1 1 0 0 1-1 1h-2zm3-3h-3v-2h3v2zm-7 0v-2h3v2H6z" />
									</svg>
									<span class="ml-1">
										Preview trip report
									</span>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php foreach ($detail['reports'] as $report): ?>
	<div class="kt-portlet">
		<div class="kt-portlet__body">
			<div class="kt-infobox">
				<div class="kt-infobox__header border-bottom pb-1 ml-4">
					<h3 class="text-dark fw-800">Review TER for: <span
							class="fw-bolder ml-2"><?= $report['report_for'] ?></span> </h3>
				</div>
				<div class="kt-infobox__body">
					<?php foreach ($report['destinations'] as $dest): ?>
					<div class="kt-infobox">
						<div class="kt-infobox__header border-bottom pb-1">
							<h4 class="text-dark fw-600"><?= $dest['order'] ?> destination
								<span>(<?= ($dest['country'] == 1 ? $dest['city'] .'/Indonesia' : $dest['city']) ?>)
									, <?= $dest['night'] ?> night)
								</span>
							</h4>
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
							<div class="p-2 mb-2 border-bottom"></div>
						</div>
						<?php $day = 0 ?>
						<?php for ($night = 1; $night <= $dest['night']; $night++): ?>
						<div
							class="py-2 border-bottom ml-2 <?= ($dest['actual_lodging_items'][$night-1]['cost'] == 0.00 && $dest['meals_text'][$night-1]['cost'] == 0.00) ? '' : '' ?>">
							<h5 class="text-dark fw-bold">
								<?= ordinal($night) ?> night:
							</h5>
						</div>
						<div class="kt-datatable kt-datatable--default kt-datatable--brand kt-datatable--loaded">
							<table
								class="kt-datatable__table <?= ($dest['actual_lodging_items'][$night-1]['cost'] == 0.00 && $dest['meals_text'][$night-1]['cost'] == 0.00) ? '' : '' ?>"
								id="html_table" width="100%" style="display: block;">
								<thead class="kt-datatable__head">
									<tr class="kt-datatable__row" style="left: 0px;">
										<th class="kt-datatable__cell kt-datatable__cell--sort"><span
												style="width: 120px;">Item</span></th>
										<th class="kt-datatable__cell kt-datatable__cell--sort">
											<span style="width: 160px;">Actual cost</span></th>
										<th class="kt-datatable__cell kt-datatable__cell--sort">
											<span style="width: 90px;">Receipt</span></th>
										<th class="kt-datatable__cell kt-datatable__cell--sort">
											<span style="width: 200px;">Action</span></th>
										<th class="kt-datatable__cell kt-datatable__cell--sort">
											<span style="width: 140px;">Status</span></th>
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
												<?php if (isset($dest['actual_lodging_items'][$night-1]['receipt']) == null): ?>
												<span class="badge badge-pill badge-secondary fw-bold">
													-
												</span>
												<?php else : ?>
												<a target="_blank" class="badge badge-warning text-light"
													href="<?= base_url('uploads/ea_items_receipt/') ?><?= $dest['actual_lodging_items'][$night-1]['receipt']  ?>">
													<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
														fill="currentColor" class="bi bi-card-image"
														viewBox="0 0 16 16">
														<path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z" />
														<path
															d="M1.5 2A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13zm13 1a.5.5 0 0 1 .5.5v6l-3.775-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12v.54A.505.505 0 0 1 1 12.5v-9a.5.5 0 0 1 .5-.5h13z" />
													</svg>
												</a>
												<?php endif; ?>
											</span>
										</td>
										<td class="kt-datatable__cell">
											<span style="width: 200px;">
												<div class="d-flex">
													<?php if (is_finance_teams()): ?>
													<button
														<?= ($dest['actual_lodging_items'][$night-1]['is_approved_by_finance'] == 1 || $dest['actual_lodging_items'][$night-1]['cost'] == 0.00 && $dest['meals_text'][$night-1]['cost'] == 0.00) ? 'disabled' : '' ?>
														data-id="<?= $dest['actual_lodging_items'][$night-1]['id'] ?>"
														class="btn btn-approve-item btn-sm btn-success mr-1">
														<span class="ml-1">Approve</span>
													</button>
													<button
														data-id="<?= $dest['actual_lodging_items'][$night-1]['id'] ?>"
														class="btn btn-edit-item btn-sm btn-info">
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
													<?php else: ?>
													<button
														data-id="<?= $dest['actual_lodging_items'][$night-1]['id'] ?>"
														class="btn btn-edit-item btn-sm btn-info">
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
													<?php endif; ?>

												</div>
											</span>
										</td>
										<td class="kt-datatable__cell">
											<span style="width: 140px;">
												<?php if ($dest['actual_lodging_items'][$night-1]['is_approved_by_finance'] == 1 || $dest['actual_lodging_items'][$night-1]['cost'] == 0.00 && $dest['meals_text'][$night-1]['cost'] == 0.00): ?>
												<span
													class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill">
													Approved
												</span>
												<?php else: ?>

												<?php endif; ?>
											</span>
										</td>
									</tr>
									<tr data-row="0" class="kt-datatable__row" style="left: 0px;">
										<td class="kt-datatable__cell fw-bold">
											<span style="width: 120px;">
												Meals
												<?= ($dest['meals_text'][$night-1]['is_first_day'] == 1 || $dest['meals_text'][$night-1]['is_last_day'] == 1  ? '(75%)' : '') ?>
											</span>
										</td>
										<td class="kt-datatable__cell">
											<span style="width: 160px;">
												<span
													class="badge badge-pill badge-secondary fw-bold lodging_meals_budget">
													<?= $dest['meals_text'][$night-1]['d_cost']?>
												</span>
												-
												<span
													class="badge badge-pill badge-secondary fw-bold lodging_meals_budget">
													<?= $dest['meals_text'][$night-1]['meals_text']?>
												</span>
											</span>
										</td>
										<td class="kt-datatable__cell">
											<span style="width: 90px;">

											</span>
										</td>
										<td class="kt-datatable__cell">
											<span style="width: 200px;">
												<div class="d-flex">
													<?php if (is_finance_teams()): ?>
													<button
														<?= ($dest['meals_text'][$night-1]['is_approved_by_finance'] == 1 || $dest['actual_lodging_items'][$night-1]['cost'] == 0.00 && $dest['meals_text'][$night-1]['cost'] == 0.00) ? 'disabled' : '' ?>
														data-id="<?= $dest['meals_text'][$night-1]['id'] ?>"
														class="btn btn-approve-item btn-sm btn-success mr-1">
														<span class="ml-1">Approve</span>
													</button>
													<button data-id="<?= $dest['meals_text'][$night-1]['id'] ?>"
														class="btn btn-edit-item btn-sm btn-info">
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
													<?php else: ?>
													<button data-id="<?= $dest['meals_text'][$night-1]['id'] ?>"
														class="btn btn-edit-item btn-sm btn-info">
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
													<?php endif; ?>
												</div>
											</span>
										</td>
										<td class="kt-datatable__cell">
											<span style="width: 140px;">
												<?php if ($dest['meals_text'][$night-1]['is_approved_by_finance'] == 1 || $dest['actual_lodging_items'][$night-1]['cost'] == 0.00 && $dest['meals_text'][$night-1]['cost'] == 0.00): ?>
												<span
													class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill">
													Approved
												</span>
												<?php else: ?>

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

											</span>
										</td>
										<td class="kt-datatable__cell">
											<span style="width: 200px;">
												<button data-req-id="<?= $detail['r_id'] ?>" data-night="<?= $night ?>"
													data-dest-id="<?= $dest['id'] ?>" data-id="<?= $items['id'] ?>"
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
											</span>
										</td>
										<td class="kt-datatable__cell">
											<span style="width: 140px;">

											</span>
										</td>
									</tr>
									<?php endforeach; ?>
									<?php endif; ?>
									<tr style="background-color: #f8f9fa !important;" data-row="0"
										class="kt-datatable__row" style="left: 0px;">
										<td class="kt-datatable__cell fw-bold">
											<span style="width: 120px;">
												<h5 class="text-dark fw-800 m-0">
													Sub total
												</h5>
											</span>
										</td>
										<td class="kt-datatable__cell">
											<span style="width: 160px;">
												<span class="badge badge-pill badge-secondary fw-bold">
													<?= number_format($dest['total_approved_expenses_by_night'][$night-1],2,',','.') ?>
												</span>
											</span>
										</td>
										<td class="kt-datatable__cell">
											<span style="width: 90px;">

											</span>
										</td>
										<td class="kt-datatable__cell">
											<span style="width: 200px;">

											</span>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<?php endfor; ?>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
	<?php endforeach; ?>
	<div class="kt-portlet">
		<div class="kt-portlet__body">
			<div class="kt-infobox">
				<div class="kt-infobox__header border-bottom pb-1">
					<h4 class="text-dark fw-600">Summarized</h4>
				</div>
				<div class="kt-infobox__body">
					<div class="pb-2">
						<p class="fw-bold">
							Total travel advance :
							<span class="badge badge-pill badge-secondary fw-bold">
								<?= $detail['d_total_advance'] ?>
							</span>
						</p>
						<p class="fw-bold">
							Total approved expense :
							<span class="badge badge-pill badge-secondary fw-bold">
								<?= $total_approved_expense ?>
							</span>
						</p>
						<p class="fw-bold">
							<?= $refund_or_reimburst['status'] ?> :
							<span class="badge badge-pill badge-secondary fw-bold">
								<?= number_format($refund_or_reimburst['total'],2,',','.') ?>
							</span>
						</p>
						<div class="my-2 fw-bold">
							<p>Status:
								<span class="badge badge-pill badge-secondary fw-bold">
									<?= ($is_clear || $detail['country_director_status'] == 2 ? 'Clear' : 'Need confirmation') ?>
								</span>
							</p>
						</div>
					</div>
					<div class="mt-3">
						<a target="_blank" href="<?= base_url('ea_report/outgoing/ter_form/') . $detail['r_id'] ?>"
							class="btn btn btn-success">
							<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
								class="bi bi-file-earmark-spreadsheet" viewBox="0 0 16 16">
								<path
									d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V9H3V2a1 1 0 0 1 1-1h5.5v2zM3 12v-2h2v2H3zm0 1h2v2H4a1 1 0 0 1-1-1v-1zm3 2v-2h3v2H6zm4 0v-2h3v1a1 1 0 0 1-1 1h-2zm3-3h-3v-2h3v2zm-7 0v-2h3v2H6z" />
							</svg>
							<span class="ml-1">
								Download excel
							</span>
						</a>
					</div>
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
										<span style="width: 140px;">Status</span></th>
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
												Supervisor
											</span>
										</span>
									<td data-field="Status" data-autohide-disabled="false" class="kt-datatable__cell">
										<span style="width: 140px;">
											<span
												class="kt-badge kt-badge--inline kt-badge--pill status-badge"><?= $detail['head_of_units_status_text'] ?></span>
										</span>
									</td>
									<td class="kt-datatable__cell">
										<span style="width: 110px;">
											<?= $detail['submitted_at'] ?>
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
										<?php if ($detail['is_need_confirmation'] == 1): ?>
										<span style="width: 140px;">
											<span class="fw-bold text-dark" style="width: 140px;">
												Waiting for requestor confirmation
											</span>
										</span>
										<?php else : ?>
										<span style="width: 140px;">
											<span
												class="kt-badge kt-badge--inline kt-badge--pill status-badge"><?= $detail['finance_status_text'] ?></span>
										</span>
										<?php endif; ?>

									</td>
									<td class="kt-datatable__cell">
										<span style="width: 110px;">
											<?= ($detail['head_of_units_status'] == 2 ? $detail['head_of_units_status_at'] : '') ?>
										</span>
									</td>
									<td class="kt-datatable__cell">
										<div style="width: 140px;" class="d-flex <?= $finance_btn ?>">
											<?php if ($is_clear): ?>
											<button data-level='finance' data-id=<?= $detail['r_id'] ?> data-status="2"
												class="btn btn-status btn-success mr-1">
												<div class="d-flex align-items-center justify-content-center">
													<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
														fill="currentColor" class="bi bi-check" viewBox="0 0 16 16">
														<path
															d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z" />
													</svg>
													Approve
												</div>
											</button>
											<?php else: ?>
											<button style="padding: 0.3rem .6rem !important;
														font-size: 0.75rem !important;
														line-height: 1.5 !important;
														border-radius: 0.2rem !important;" data-level='finance' data-id=<?= $detail['r_id'] ?> data-status="3"
												class="btn btn-confirmation btn-primary">
												<div class="d-flex align-items-center justify-content-center">
													<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
														fill="currentColor" class="bi bi-question" viewBox="0 0 16 16">
														<path
															d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z" />
													</svg>
													Confirm
												</div>
											</button>
											<?php endif; ?>
										</div>
									</td>
								</tr>
								<tr data-row="2" class="kt-datatable__row" style="left: 0px;">
									<td data-field="Order ID" class="kt-datatable__cell fw-bold">
										<span style="width: 150px;">
											<?= $detail['country_director_name'] ?>
										</span>
									</td>
									<td data-field="Status" data-autohide-disabled="false" class="kt-datatable__cell">
										<span style="width: 110px;"><span
												class="kt-badge kt-badge--dark kt-badge--inline kt-badge--pill">
												FCO Monitor
											</span>
										</span>
									<td data-field="Status" data-autohide-disabled="false" class="kt-datatable__cell">
										<span style="width: 140px;">
											<span
												class="kt-badge kt-badge--inline kt-badge--pill status-badge"><?= $detail['country_director_status_text'] ?></span>
										</span>
									</td>
									<td class="kt-datatable__cell">
										<span style="width: 110px;">
											<?= $detail['finance_status_at'] ?>
										</span>
									</td>
									<td class="kt-datatable__cell">
										<div style="width: 140px;" class="d-flex <?= $country_director_btn ?>">
											<!-- <button data-level='country_director' data-id=<?= $detail['r_id'] ?>
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
											</button> -->
										</div>
									</td>
								</tr>

							</tbody>
						</table>
					</div>

					<div class="ml-3 <?= $submit_btn ?>">
						<button data-id="<?= $detail['r_id'] ?>" type="button" id="btn_submit_report"
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
			const req_id = $(this).attr('data-req-id')
			const item_name = $(this).attr('data-item-name')
			const item_id = $(this).attr('data-item-id')
			const night = $(this).attr('data-night')
			$.get(base_url +
				`ea_report/outgoing/other_items_detail?dest_id=${dest_id}&item_name=${item_name}&night=${night}&item_id=${item_id}&req_id=${req_id}`,
				function (html) {
					$('#myModal').html(html)
					$('#myModal').modal('show')
				});
		});

		$(document).on('click', '.btn-approve-item', function (e) {
			e.preventDefault()
			const item_id = $(this).attr('data-id')
			const loader = `<div style="width: 5rem; height: 5rem;" class="spinner-border mb-5" role="status"></div>
			<h5 class="mt-2">Please wait</h5>
			<p>Updating data ...</p>`
			Swal.fire({
				title: `Approve item?`,
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
						url: base_url + 'ea_report/incoming/approve_ter_item/' + item_id,
						beforeSend: function () {
							Swal.fire({
								html: loader,
								showConfirmButton: false,
								allowEscapeKey: false,
								allowOutsideClick: false,
							});
						},
						error: function (xhr) {
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
						}
					});
				}
			})
		});

		$(document).on('click', '.btn-edit-item', function (e) {
			e.preventDefault()
			const item_id = $(this).attr('data-id')
			const loader = `<div style="width: 5rem; height: 5rem;" class="spinner-border mb-5" role="status"></div>
			<h5 class="mt-2">Please wait</h5>
			<p>Saving data ...</p>`
			$.get(base_url +
				`ea_report/incoming/edit_ter_item?item_id=${item_id}`,
				function (html) {
					$('#myModal').html(html)
					$('#myModal').modal('show')
					$('#cost').number(true, 0, '', '.');
				});
			$(document).on("submit", '#ter-item-form', function (e) {
				e.preventDefault()
				const formData = new FormData(this);
				Swal.fire({
					title: `Update item?`,
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
		$(document).on('click', '.btn-confirmation', function (e) {
			e.preventDefault()
			const req_id = $(this).attr('data-id')
			const loader = `<div style="width: 5rem; height: 5rem;" class="spinner-border mb-5" role="status"></div>
			<h5 class="mt-2">Please wait</h5>
			<p>Sending email to requestor ...</p>`
			Swal.fire({
				title: 'Send report confirmation?',
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
						url: base_url + 'ea_report/incoming/report_confirmation',
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
