	<div class="details-container">
		<div class="kt-portlet">
			<div class="kt-portlet__body">
				<div class="kt-infobox">
					<div class="kt-infobox__header border-bottom pb-1">
						<h4 class="text-dark fw-600">Basic Information</h4>
					</div>
					<div class="kt-infobox__body">
						<div class="row">
							<label class="col-3 mb-2 col-form-label fw-bold">EA Number</label>
							<div class="col-9">
								<span style="font-size: 1rem;"
									class="badge badge-success fw-bold"><?= $detail['ea_number'] ?></span>
							</div>
						</div>
						<div class="row">
							<label class="col-3 mb-2 col-form-label fw-bold">EA Online Number</label>
							<div class="col-9">
								<span style="font-size: 1rem;"
									class="badge badge-success fw-bold"><?= $detail['ea_online_number'] ?></span>
							</div>
						</div>
						<div class="row">
							<label class="col-3 mb-2 col-form-label fw-bold">Status</label>
							<div class="col-9">
								<span style="font-size: 1rem;"
									class="badge badge-<?= $request_status['badge_color'] ?> fw-bold"><?= $request_status['text'] ?></span>
							</div>
						</div>
						<div class="row">
							<label class="col-3 mb-2 col-form-label fw-bold">Requestor name</label>
							<div class="col-9">
								<span style="font-size: 1rem;"
									class="badge badge-light fw-bold"><?= $requestor_data['username'] ?></span>
							</div>
						</div>
						<div class="row">
							<label class="col-3 mb-2 col-form-label fw-bold">Division</label>
							<div class="col-9">
								<span class="badge badge-dark fw-bold"><?= $requestor_data['project_name'] ?></span>
							</div>
						</div>
						<div class="row">
							<label class="col-3 mb-2 col-form-label fw-bold">Purpose</label>
							<div class="col-9">
								<span class="badge badge-info fw-bold"><?= $requestor_data['unit_name'] ?></span>
							</div>
						</div>
						<div class="p-2 mb-2 border-bottom"></div>
						<div class="row">
							<label class="col-3 mb-2 col-form-label fw-bold">Request base</label>
							<div class="col-9">
								<span class="badge badge-light fw-bold"><?= $detail['request_base'] ?></span>
							</div>
						</div>
						<?php if ($detail['request_base'] === 'Internal TOR'): ?>
						<div class="row">
							<label class="col-3 mb-2 col-form-label fw-bold">TOR number</label>
							<div class="col-9">
								<span class="badge badge-light fw-bold"><?= $detail['tor_number'] ?></span>
							</div>
						</div>
						<?php endif; ?>
						<div class="row">
							<label class="col-3 mb-2 col-form-label fw-bold">Request date</label>
							<div class="col-9">
								<span class="badge badge-light fw-bold"><?= $detail['request_date'] ?></span>
							</div>
						</div>
						<div class="row">
							<label class="col-3 mb-2 col-form-label fw-bold">Employment</label>
							<div class="col-9">
								<span class="badge badge-light fw-bold"><?= $detail['employment'] ?></span>
							</div>
						</div>
						<?php if ($detail['employment'] === 'On behalf'): ?>
						<div class="row">
							<label class="col-3 mb-2 col-form-label fw-bold">Employment status</label>
							<div class="col-9">
								<span class="badge badge-light fw-bold"><?= $detail['employment_status'] ?></span>
							</div>
						</div>

						<?php if ($detail['employment_status'] === 'Group'): ?>

						<!-- If employment status = Group -->
						<div class="group-info row my-2 pt-1 pb-3 border-bottom border-top">
							<label class="col-3 col-form-label fw-bold">Group Info</label>
							<div class="col-md-4 mt-2">
								<small class="col-form-label">
									Group name
								</small>
								<input readonly value="<?= $detail['participant_group_name'] ?>" class="form-control mt-2"
									type="text">
							</div>
							<div class="col-md-1">
							</div>
							<div class="col-md-4 mt-3 my-2">
								<small class="col-form-label">
									Email
								</small>
								<input readonly value="<?= $detail['participant_group_email'] ?>" class="form-control mt-2"
									type="text">
							</div>
							<label class="col-3 col-form-label fw-bold"></label>
							<div class="col-md-4 mt-3 mt-2">
								<small class="col-form-label">
									Contact Person
								</small>
								<input readonly value="<?= $detail['participant_group_contact_person'] ?>"
									class="form-control mt-2" type="text">
							</div>
							<div class="col-md-1">
							</div>
							<div class="col-md-4 my-2">
								<small class="col-form-label">
									Number of participants
								</small>
								<input readonly value="<?= $detail['number_of_participants'] ?>" class="form-control mt-2"
									type="text">
							</div>
						</div>

						<?php else: ?>

						<!-- If employment status = Consultant / other -->
						<div class="group-info row my-2 pt-1 pb-2 border-bottom border-top">
							<label class="col-3 col-form-label fw-bold mt-3">Participants</label>
							<div class="col-9">
								<div class="kt-datatable kt-datatable--default kt-datatable--brand kt-datatable--loaded">
									<table class="kt-datatable__table" width="100%" style="display: block;">
										<thead class="kt-datatable__head">
											<tr class="kt-datatable__row" style="left: 0px;">

												<th class="kt-datatable__cell kt-datatable__cell--sort"><span
														style="width: 30px;"></span>#</th>
												<th class="kt-datatable__cell kt-datatable__cell--sort"><span
														style="width: 180px;">Name</span></th>
												<th class="kt-datatable__cell kt-datatable__cell--sort"><span
														style="width: 180px;">Email</span></th>
												<th class="kt-datatable__cell kt-datatable__cell--sort"><span
														style="width: 110px;">Title</span></th>
											</tr>
										</thead>
										<tbody class="kt-datatable__body">
											<?php $no = 1 ?>
											<?php foreach ($detail['participants'] as $par): ?>
											<tr class="kt-datatable__row" style="left: 0px;">
												<td class="kt-datatable__cell"><span class="text-dark"
														style="width: 30px;"><?= $no++ ?></span></td>
												<td class="kt-datatable__cell">
													<span class="text-dark"
														style="width: 180px;"><?= $par['name'] ?></span>
												</td>
												<td class="kt-datatable__cell">
													<span class="text-dark"
														style="width: 180px;"><?= $par['email'] ?></span>
												<td class="kt-datatable__cell"><span class="text-dark"
														style="width: 110px;"><?= $par['title'] ?></span></td>
											</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>

						<?php endif; ?>

						<?php endif; ?>

						<div class="row">
							<label class="col-3 col-form-label fw-bold">Originating city</label>
							<div class="col-9">
								<span class="badge badge-light fw-bold"><?= $detail['originating_city'] ?></span>
							</div>
						</div>
						<div class="row mb-2">
							<label class="col-3 col-form-label fw-bold">Date</label>
							<div class="col-md-4 mt-2">
								<small for="departure_date" class="col-form-label">
									Departure date
								</small>
								<input readonly value="<?= $detail['d_date'] ?>" class="form-control mt-2" type="text"
									id="departure_date" name="departure_date">
							</div>
							<div class="col-md-1">
							</div>
							<div class="col-md-4 mt-2">
								<small for="departure_date" class="col-form-label">
									Return date
								</small>
								<input readonly value="<?= $detail['r_date'] ?>" class="form-control mt-2" type="text"
									id="return_date" name="return_date">
							</div>
						</div>
						<div class="row">
							<label class="col-3 col-form-label fw-bold">Country director notified</label>
							<div class="col-9">
								<span class="badge badge-light fw-bold"><?= $detail['country_director_notified'] ?></span>
							</div>
						</div>
						<div class="row">
							<label class="col-3 col-form-label fw-bold">Travel advance</label>
							<div class="col-9">
								<span class="badge badge-light fw-bold"><?= $detail['travel_advance'] ?></span>
							</div>
						</div>

						<form id="max-budget-form" class="mt-3" method="POST"
							action="<?= base_url('ea_requests/incoming-requests/update_budget') ?>">
							<input value="<?= $detail['r_id'] ?>" class="d-none" type="text" id="r_id" name="r_id">
							<div class="row mb-2">
								<label class="col-3 col-form-label fw-bold">Max budget</label>
								<div class="col-md-4 mt-2">
									<small for="max_budget_idr" class="col-form-label">
										IDR
									</small>
									<input <?= (!is_ea_assosiate() ? 'readonly' : '') ?>
										value="<?= $detail['clean_max_budget_idr'] ?>" class="form-control mt-2"
										type="text" id="max_budget_idr" name="max_budget_idr">
								</div>
								<div class="col-md-1">
								</div>
								<div class="col-md-4 mt-2">
									<small for="departure_date" class="col-form-label">
										USD
									</small>
									<input <?= (!is_ea_assosiate() ? 'readonly' : '') ?>
										value="<?= $detail['clean_max_budget_usd'] ?>" class="form-control mt-2"
										type="text" id="max_budget_usd" name="max_budget_usd">
								</div>
							</div>
							<div class="justify-content-end mt-3 <?= (!is_ea_assosiate() ? 'd-none' : 'd-flex') ?>">
								<button id="btn-update-budget" type="submit"
									class="btn btn-success btn-sm kt-font-bold kt-font-transform-u"
									data-ktwizard-type="action-submit">
									Update max budget
								</button>
							</div>
						</form>

					</div>
				</div>
			</div>
		</div>
		<div class="kt-portlet">
			<div class="kt-portlet__body">
				<div class="kt-infobox">
					<div class="kt-infobox__header border-bottom pb-1">
						<h4 class="text-dark fw-600">Special Requests</h4>
					</div>
					<div class="kt-infobox__body">
						<div class="details-special-requests mt-2">
							<div class="special-request-el">
								<div class="form-group mb-3 row border-bottom pb-3">
									<label class="col-md-7 col-form-label">Documents Needed more than 3 days prior
										to
										departure?</label>
									<div class="col-md-5">
										<div class="kt-radio-inline">
											<span class="badge badge-light fw-bold">
												<?= $detail['need_documents'] ?>
											</span>
										</div>
										<?php if ($detail['need_documents'] === 'Yes'): ?>
										<div class="form-group mt-4 documents-description-el">
											<label for="document_description">Document description</label>
											<textarea class="form-control" readonly name="document_description"
												id="document_description" rows="3"><?= $detail['document_description'] ?>
											</textarea>
										</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="form-group mb-3 row border-bottom pb-3">
									<label class="col-md-7 col-form-label">Car Rental? If yes, attach approved per
										diem
										variance memo to Travel Manager</label>
									<div class="col-md-5">
										<div class="kt-radio-inline">
											<span class="badge badge-light fw-bold">
												<?= $detail['car_rental'] ?>
											</span>
										</div>
									</div>
								</div>
								<div class="form-group mb-3 row border-bottom pb-3">
									<label class="col-md-7 col-form-label">Hotel Reservations? If yes, specify dates
										in/out
										and hotel(s) if known.</label>
									<div class="col-md-5">
										<div class="kt-radio-inline">
											<span class="badge badge-light fw-bold">
												<?= $detail['hotel_reservations'] ?>
											</span>
										</div>
										<?php if ($detail['hotel_reservations'] === 'Yes'): ?>
										<div class="form-group row mt-4 hotel-reservations-el">
											<div class="col-6">
												<label for="hotel_check_in" class="form-label">
													Check in
												</label>
												<input readonly class="form-control" type="text"
													value="<?= date("d M Y", strtotime($detail['hotel_check_in'])) ?>"
													name="hotel_check_in" id="hotel_check_in">
											</div>
											<div class="col-6">
												<label for="hotel_check_out" class="form-label">
													Check out
												</label>
												<input readonly class="form-control" type="text"
													value="<?= date("d M Y", strtotime($detail['hotel_check_out'])) ?>"
													name="hotel_check_out" id="hotel_check_out">
											</div>
											<div class="col-12 mt-3">
												<label for="preferred_hotel">Preferred Hotel</label>
												<textarea readonly class="form-control" name="preferred_hotel"
													id="preferred_hotel" rows="2"><?= $detail['preferred_hotel'] ?>
												</textarea>
											</div>
										</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="form-group mb-3 row border-bottom pb-3">
									<label class="col-md-7 col-form-label">Hotel transfer/taxi/other transportation
										needed
										(International Travel only)</label>
									<div class="col-md-5">
										<div class="kt-radio-inline">
											<span class="badge badge-light fw-bold">
												<?= $detail['other_transportation'] ?>
											</span>
										</div>
									</div>
								</div>
							</div>
							<div>
								<label class="mt-3 mb-2 fw-bold">Special instructions: </label>
								<p> <?= $detail['special_instructions'] ?> </p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="kt-portlet">
			<div class="kt-portlet__body">
				<div class="kt-infobox">
					<div class="kt-infobox__header border-bottom pb-1">
						<h4 class="text-dark fw-600">Destination details</h4>
					</div>
					<div class="kt-infobox__body">
						<div id="destinations-review-lists" class="row destinations-review-lists">
							<?php foreach ($detail['destinations'] as $dest): ?>
							<div class="col-md-6 destination-review border p-3">
								<h6 class="pb-2 border-bottom font-weight-bold"><?= $dest['order'] ?> destination </h6>
								<div class="text-dark">
									<p class="mb-1">City/country: <span
											class="destination-city-val"><?= $dest['city'] ?></span> </p>
									<div class="mb-1 d-flex">
										<p class="mb-0">Arrival date: <?= $dest['arriv_date'] ?></p>
										<p class="ml-3 mb-0">Departure date: <?= $dest['depar_date'] ?>,</p>
									</div>
									<p class="mb-1">Project number: <span
											class="destination-project-number-val"><?= $dest['project_number'] ?></span>
									</p>
									<p class="mb-1">Budget monitor: <span class="destination-project-number-val">
											<?= $dest['budget_monitor'] ?></span> </p>
									<p class="mb-1">Lodging: <span class="destination-lodging-val">Rp.
											<?= $dest['d_lodging'] ?></span>
									</p>
									<p class="mb-1">Meals: <span class="destination-meals-val">Rp.
											<?= $dest['d_meals'] ?></span> </p>
									<p class="mb-1">Total (lodging+meals): <span
											class="destination-meals-lodging-total-val">Rp.
											<?= $dest['d_total_lodging_and_meals'] ?></span> </p>
									<p class="mb-1">Number of nights: <span
											class="destination-night-val"><?= $dest['night'] ?></span> </p>
									<p class="mb-1">Total: <span class="destination-total-val">Rp.
											<?= $dest['d_total'] ?></span>
									</p>
								</div>
								<?php if (is_ea_assosiate()): ?>
								<div class="d-flex justify-content-end align-items-center p-2">
									<button data-dest-id="<?= $dest['id'] ?>"
										class="btn btn-sm btn-success btn-edit-costs">
										<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor"
											class="bi bi-pencil-square" viewBox="0 0 16 16">
											<path
												d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
											<path fill-rule="evenodd"
												d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z" />
										</svg>
										<span class="ml-1">Edit cost</span>
									</button>
								</div>
								<?php elseif ($detail['requestor_id'] == $this->user_data->userId && $request_status['text'] == 'Rejected'): ?>
								<div class="d-flex justify-content-end align-items-center p-2">
									<button data-dest-id="<?= $dest['id'] ?>"
										class="btn btn-sm btn-success btn-edit-costs">
										<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor"
											class="bi bi-pencil-square" viewBox="0 0 16 16">
											<path
												d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
											<path fill-rule="evenodd"
												d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z" />
										</svg>
										<span class="ml-1">Edit cost</span>
									</button>
								</div>
								<?php endif; ?>
							</div>
							<?php endforeach; ?>
						</div>
						<div class="row mt-3">
							<h5 class="col-md-2">Total all costs</h5>
							<h5 class="col-md-10">: Rp.
								<?= number_format($detail['total_destinations_cost'], 2, ",", ".") ?></h5>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="kt-portlet">
			<div class="kt-portlet__body">
				<div class="kt-infobox">
					<div class="kt-infobox__header border-bottom pb-1">
						<h4 class="text-dark fw-600">Documents</h4>
					</div>
					<div class="kt-infobox__body">
						<div class="row">
							<div class="div col-md-3 col-6">
								<div class="p-3">
									<div class="d-flex">
										<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor"
											class="bi bi-file-earmark-pdf" viewBox="0 0 16 16">
											<path
												d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z" />
											<path
												d="M4.603 14.087a.81.81 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.68 7.68 0 0 1 1.482-.645 19.697 19.697 0 0 0 1.062-2.227 7.269 7.269 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.188-.012.396-.047.614-.084.51-.27 1.134-.52 1.794a10.954 10.954 0 0 0 .98 1.686 5.753 5.753 0 0 1 1.334.05c.364.066.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.856.856 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.712 5.712 0 0 1-.911-.95 11.651 11.651 0 0 0-1.997.406 11.307 11.307 0 0 1-1.02 1.51c-.292.35-.609.656-.927.787a.793.793 0 0 1-.58.029zm1.379-1.901c-.166.076-.32.156-.459.238-.328.194-.541.383-.647.547-.094.145-.096.25-.04.361.01.022.02.036.026.044a.266.266 0 0 0 .035-.012c.137-.056.355-.235.635-.572a8.18 8.18 0 0 0 .45-.606zm1.64-1.33a12.71 12.71 0 0 1 1.01-.193 11.744 11.744 0 0 1-.51-.858 20.801 20.801 0 0 1-.5 1.05zm2.446.45c.15.163.296.3.435.41.24.19.407.253.498.256a.107.107 0 0 0 .07-.015.307.307 0 0 0 .094-.125.436.436 0 0 0 .059-.2.095.095 0 0 0-.026-.063c-.052-.062-.2-.152-.518-.209a3.876 3.876 0 0 0-.612-.053zM8.078 7.8a6.7 6.7 0 0 0 .2-.828c.031-.188.043-.343.038-.465a.613.613 0 0 0-.032-.198.517.517 0 0 0-.145.04c-.087.035-.158.106-.196.283-.04.192-.03.469.046.822.024.111.054.227.09.346z" />
										</svg>
									</div>
									<div class="mt-3">
										<h6 class="fw-bold">EA requests form</h6>
										<a target="_blank"
											href="<?= base_url('ea_requests/outcoming-requests/ea_form') ?>/<?= $detail['r_id'] ?>"
											class="badge badge-primary"><span class="mr-1"><svg
													xmlns="http://www.w3.org/2000/svg" width="10" height="10"
													fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
													<path
														d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z" />
													<path
														d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z" />
												</svg></span> Download</a>
									</div>
								</div>
							</div>
							<?php if ($detail['request_base'] === 'Exteral Invitation'): ?>
							<div class="div col-md-3 col-6">
								<div class="p-3">
									<div class="d-flex">
										<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor"
											class="bi bi-file-earmark-pdf" viewBox="0 0 16 16">
											<path
												d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z" />
											<path
												d="M4.603 14.087a.81.81 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.68 7.68 0 0 1 1.482-.645 19.697 19.697 0 0 0 1.062-2.227 7.269 7.269 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.188-.012.396-.047.614-.084.51-.27 1.134-.52 1.794a10.954 10.954 0 0 0 .98 1.686 5.753 5.753 0 0 1 1.334.05c.364.066.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.856.856 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.712 5.712 0 0 1-.911-.95 11.651 11.651 0 0 0-1.997.406 11.307 11.307 0 0 1-1.02 1.51c-.292.35-.609.656-.927.787a.793.793 0 0 1-.58.029zm1.379-1.901c-.166.076-.32.156-.459.238-.328.194-.541.383-.647.547-.094.145-.096.25-.04.361.01.022.02.036.026.044a.266.266 0 0 0 .035-.012c.137-.056.355-.235.635-.572a8.18 8.18 0 0 0 .45-.606zm1.64-1.33a12.71 12.71 0 0 1 1.01-.193 11.744 11.744 0 0 1-.51-.858 20.801 20.801 0 0 1-.5 1.05zm2.446.45c.15.163.296.3.435.41.24.19.407.253.498.256a.107.107 0 0 0 .07-.015.307.307 0 0 0 .094-.125.436.436 0 0 0 .059-.2.095.095 0 0 0-.026-.063c-.052-.062-.2-.152-.518-.209a3.876 3.876 0 0 0-.612-.053zM8.078 7.8a6.7 6.7 0 0 0 .2-.828c.031-.188.043-.343.038-.465a.613.613 0 0 0-.032-.198.517.517 0 0 0-.145.04c-.087.035-.158.106-.196.283-.04.192-.03.469.046.822.024.111.054.227.09.346z" />
										</svg>
									</div>
									<div class="mt-3">
										<h6 class="fw-bold">Exteral invitation</h6>
										<a target="_blank"
											href="<?= base_url('uploads/exteral_invitation/') ?><?= $detail['exteral_invitation_file'] ?>"
											class="badge badge-primary"><span class="mr-1"><svg
													xmlns="http://www.w3.org/2000/svg" width="10" height="10"
													fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
													<path
														d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z" />
													<path
														d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z" />
												</svg></span> Download</a>
									</div>
								</div>
							</div>
							<?php endif; ?>
							<?php if ($detail['car_rental'] === 'Yes'): ?>
							<div class="div col-md-3 col-6">
								<div class="p-3">
									<div class="d-flex">
										<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor"
											class="bi bi-file-earmark-pdf" viewBox="0 0 16 16">
											<path
												d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z" />
											<path
												d="M4.603 14.087a.81.81 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.68 7.68 0 0 1 1.482-.645 19.697 19.697 0 0 0 1.062-2.227 7.269 7.269 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.188-.012.396-.047.614-.084.51-.27 1.134-.52 1.794a10.954 10.954 0 0 0 .98 1.686 5.753 5.753 0 0 1 1.334.05c.364.066.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.856.856 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.712 5.712 0 0 1-.911-.95 11.651 11.651 0 0 0-1.997.406 11.307 11.307 0 0 1-1.02 1.51c-.292.35-.609.656-.927.787a.793.793 0 0 1-.58.029zm1.379-1.901c-.166.076-.32.156-.459.238-.328.194-.541.383-.647.547-.094.145-.096.25-.04.361.01.022.02.036.026.044a.266.266 0 0 0 .035-.012c.137-.056.355-.235.635-.572a8.18 8.18 0 0 0 .45-.606zm1.64-1.33a12.71 12.71 0 0 1 1.01-.193 11.744 11.744 0 0 1-.51-.858 20.801 20.801 0 0 1-.5 1.05zm2.446.45c.15.163.296.3.435.41.24.19.407.253.498.256a.107.107 0 0 0 .07-.015.307.307 0 0 0 .094-.125.436.436 0 0 0 .059-.2.095.095 0 0 0-.026-.063c-.052-.062-.2-.152-.518-.209a3.876 3.876 0 0 0-.612-.053zM8.078 7.8a6.7 6.7 0 0 0 .2-.828c.031-.188.043-.343.038-.465a.613.613 0 0 0-.032-.198.517.517 0 0 0-.145.04c-.087.035-.158.106-.196.283-.04.192-.03.469.046.822.024.111.054.227.09.346z" />
										</svg>
									</div>
									<div class="mt-3">
										<h6 class="fw-bold">Car rental memo</h6>
										<a target="_blank"
											href="<?= base_url('uploads/car_rental_memo/') ?><?= $detail['car_rental_memo'] ?>"
											class="badge badge-primary"><span class="mr-1"><svg
													xmlns="http://www.w3.org/2000/svg" width="10" height="10"
													fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
													<path
														d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z" />
													<path
														d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z" />
												</svg></span> Download</a>
									</div>
								</div>
							</div>
							<?php endif; ?>
							<?php if ($detail['fco_monitor_status'] == 2): ?>
							<div class="div col-md-3 col-6">
								<div class="p-3">
									<div class="d-flex">
										<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor"
											class="bi bi-file-earmark-pdf" viewBox="0 0 16 16">
											<path
												d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z" />
											<path
												d="M4.603 14.087a.81.81 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.68 7.68 0 0 1 1.482-.645 19.697 19.697 0 0 0 1.062-2.227 7.269 7.269 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.188-.012.396-.047.614-.084.51-.27 1.134-.52 1.794a10.954 10.954 0 0 0 .98 1.686 5.753 5.753 0 0 1 1.334.05c.364.066.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.856.856 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.712 5.712 0 0 1-.911-.95 11.651 11.651 0 0 0-1.997.406 11.307 11.307 0 0 1-1.02 1.51c-.292.35-.609.656-.927.787a.793.793 0 0 1-.58.029zm1.379-1.901c-.166.076-.32.156-.459.238-.328.194-.541.383-.647.547-.094.145-.096.25-.04.361.01.022.02.036.026.044a.266.266 0 0 0 .035-.012c.137-.056.355-.235.635-.572a8.18 8.18 0 0 0 .45-.606zm1.64-1.33a12.71 12.71 0 0 1 1.01-.193 11.744 11.744 0 0 1-.51-.858 20.801 20.801 0 0 1-.5 1.05zm2.446.45c.15.163.296.3.435.41.24.19.407.253.498.256a.107.107 0 0 0 .07-.015.307.307 0 0 0 .094-.125.436.436 0 0 0 .059-.2.095.095 0 0 0-.026-.063c-.052-.062-.2-.152-.518-.209a3.876 3.876 0 0 0-.612-.053zM8.078 7.8a6.7 6.7 0 0 0 .2-.828c.031-.188.043-.343.038-.465a.613.613 0 0 0-.032-.198.517.517 0 0 0-.145.04c-.087.035-.158.106-.196.283-.04.192-.03.469.046.822.024.111.054.227.09.346z" />
										</svg>
									</div>
									<div class="mt-3">
										<h6 class="fw-bold">Payment request form</h6>
										<a target="_blank"
											href="<?= base_url('ea_requests/incoming_requests/get_payment_form/') ?><?= $detail['r_id'] ?>"
											class="badge badge-primary"><span class="mr-1"><svg
													xmlns="http://www.w3.org/2000/svg" width="10" height="10"
													fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
													<path
														d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z" />
													<path
														d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z" />
												</svg></span> Download</a>
									</div>
								</div>
							</div>
							<?php endif; ?>
							<?php if ($detail['finance_status'] == 2 && $detail['payment_receipt'] != null): ?>
							<div class="div col-md-3 col-6">
								<div class="p-3">
									<div class="d-flex">
										<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor"
											class="bi bi-file-earmark-pdf" viewBox="0 0 16 16">
											<path
												d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z" />
											<path
												d="M4.603 14.087a.81.81 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.68 7.68 0 0 1 1.482-.645 19.697 19.697 0 0 0 1.062-2.227 7.269 7.269 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.188-.012.396-.047.614-.084.51-.27 1.134-.52 1.794a10.954 10.954 0 0 0 .98 1.686 5.753 5.753 0 0 1 1.334.05c.364.066.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.856.856 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.712 5.712 0 0 1-.911-.95 11.651 11.651 0 0 0-1.997.406 11.307 11.307 0 0 1-1.02 1.51c-.292.35-.609.656-.927.787a.793.793 0 0 1-.58.029zm1.379-1.901c-.166.076-.32.156-.459.238-.328.194-.541.383-.647.547-.094.145-.096.25-.04.361.01.022.02.036.026.044a.266.266 0 0 0 .035-.012c.137-.056.355-.235.635-.572a8.18 8.18 0 0 0 .45-.606zm1.64-1.33a12.71 12.71 0 0 1 1.01-.193 11.744 11.744 0 0 1-.51-.858 20.801 20.801 0 0 1-.5 1.05zm2.446.45c.15.163.296.3.435.41.24.19.407.253.498.256a.107.107 0 0 0 .07-.015.307.307 0 0 0 .094-.125.436.436 0 0 0 .059-.2.095.095 0 0 0-.026-.063c-.052-.062-.2-.152-.518-.209a3.876 3.876 0 0 0-.612-.053zM8.078 7.8a6.7 6.7 0 0 0 .2-.828c.031-.188.043-.343.038-.465a.613.613 0 0 0-.032-.198.517.517 0 0 0-.145.04c-.087.035-.158.106-.196.283-.04.192-.03.469.046.822.024.111.054.227.09.346z" />
										</svg>
									</div>
									<div class="mt-3">
										<h6 class="fw-bold">Payment Receipt</h6>
										<a target="_blank"
											href="<?= base_url('uploads/ea_payment_receipt/') ?><?= $detail['payment_receipt'] ?>"
											class="badge badge-primary"><span class="mr-1"><svg
													xmlns="http://www.w3.org/2000/svg" width="10" height="10"
													fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
													<path
														d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z" />
													<path
														d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z" />
												</svg></span> Download</a>
									</div>
								</div>
							</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="kt-portlet">
			<div class="kt-portlet__body">
				<div class="kt-infobox">
					<div class="kt-infobox__header border-bottom pb-1">
						<h4 class="text-dark fw-600">Signature Status</h4>
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
											<span style="width: 110px;"><span
													class="kt-badge kt-badge--inline kt-badge--pill status-badge"><?= $detail['head_of_units_status_text'] ?></span>
											</span>
										</td>
										<td data-field="Car Model" class="kt-datatable__cell"><span
												style="width: 110px;"><?= $detail['head_of_units_status_at'] ?></span></td>
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
												style="width: 150px;"><?= $detail['ea_assosiate_name'] ?></span></td>
										<td data-field="Status" data-autohide-disabled="false" class="kt-datatable__cell">
											<span style="width: 110px;"><span
													class="kt-badge kt-badge--dark kt-badge--inline kt-badge--pill">
													EA Assosiate</span></span>
										<td data-field="Status" data-autohide-disabled="false" class="kt-datatable__cell">
											<span style="width: 110px;">
												<span
													class="kt-badge kt-badge--inline kt-badge--pill status-badge"><?= $detail['ea_assosiate_status_text'] ?></span></span>
										</td>
										<td data-field="Car Model" class="kt-datatable__cell"><span
												style="width: 110px;"><?= $detail['ea_assosiate_status_at'] ?></span></td>
										<td class="kt-datatable__cell">
											<div style="width: 140px;" class="d-flex <?= $ea_assosiate_btn ?>">
												<?php if ($detail['max_budget_idr'] == null || $detail['max_budget_usd'] == null ): ?>
												<button id="btn-to-budget-form" class="btn btn-sm btn-dark">
													Max budget form
												</button>
												<?php else : ?>
												<button data-level='ea_assosiate' data-id=<?= $detail['r_id'] ?>
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
												<button data-level='ea_assosiate' data-id=<?= $detail['r_id'] ?>
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
												<?php endif; ?>
											</div>
										</td>
									</tr>
									<tr data-row="2" class="kt-datatable__row" style="left: 0px;">
										<td data-field="Order ID" class="kt-datatable__cell fw-bold"><span
												style="width: 150px;"><?= $detail['fco_monitor_name'] ?></span></td>
										<td data-field="Status" data-autohide-disabled="false" class="kt-datatable__cell">
											<span style="width: 110px;"><span
													class="kt-badge kt-badge--dark kt-badge--inline kt-badge--pill">FCO
													Monitor</span></span>
										<td data-field="Status" data-autohide-disabled="false" class="kt-datatable__cell">
											<span style="width: 110px;"><span
													class="kt-badge kt-badge--inline kt-badge--pill status-badge"><?= $detail['fco_monitor_status_text'] ?></span></span>
										</td>
										<td data-field="Car Model" class="kt-datatable__cell"><span
												style="width: 110px;"><?= $detail['fco_monitor_status_at'] ?></span></td>
										<td class="kt-datatable__cell">
											<div style="width: 140px;" class="d-flex <?= $fco_monitor_btn ?>">
												<button data-level='fco_monitor' data-id=<?= $detail['r_id'] ?>
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
												<button data-level='fco_monitor' data-id=<?= $detail['r_id'] ?>
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
									<tr data-row="3" class="kt-datatable__row" style="left: 0px;">
										<td data-field="Order ID" class="kt-datatable__cell fw-bold"><span
												style="width: 150px;"><?= $detail['finance_name'] ?></span></td>
										<td data-field="Status" data-autohide-disabled="false" class="kt-datatable__cell">
											<span style="width: 110px;"><span
													class="kt-badge kt-badge--dark kt-badge--inline kt-badge--pill">Finance
													teams</span></span>
										<td data-field="Status" data-autohide-disabled="false" class="kt-datatable__cell">
											<span style="width: 110px;">
												<span
													class="kt-badge kt-badge--brand kt-badge--inline kt-badge--pill status-badge"><?= $detail['finance_status_text'] ?></span>
											</span>
										</td>
										<td data-field="Car Model" class="kt-datatable__cell"><span
												style="width: 110px;"><?= $detail['finance_status_at'] ?></span></td>
										<td class="kt-datatable__cell">
											<div style="width: 140px;" class="d-flex flex-column <?= $finance_btn ?>">
												<button style="padding: 0.3rem .6rem !important;" data-level='finance'
													data-id=<?= $detail['r_id'] ?>
													class="btn btn-payment btn-sm btn-warning text-light">
													<div class="d-flex align-items-center justify-content-center">
														<svg xmlns="http://www.w3.org/2000/svg" width="11" height="11"
															fill="currentColor" class="bi bi-currency-dollar"
															viewBox="0 0 16 16">
															<path
																d="M4 10.781c.148 1.667 1.513 2.85 3.591 3.003V15h1.043v-1.216c2.27-.179 3.678-1.438 3.678-3.3 0-1.59-.947-2.51-2.956-3.028l-.722-.187V3.467c1.122.11 1.879.714 2.07 1.616h1.47c-.166-1.6-1.54-2.748-3.54-2.875V1H7.591v1.233c-1.939.23-3.27 1.472-3.27 3.156 0 1.454.966 2.483 2.661 2.917l.61.162v4.031c-1.149-.17-1.94-.8-2.131-1.718H4zm3.391-3.836c-1.043-.263-1.6-.825-1.6-1.616 0-.944.704-1.641 1.8-1.828v3.495l-.2-.05zm1.591 1.872c1.287.323 1.852.859 1.852 1.769 0 1.097-.826 1.828-2.2 1.939V8.73l.348.086z" />
														</svg>
														Payment
													</div>
												</button>
												<button data-level='finance' data-id=<?= $detail['r_id'] ?> data-status="3"
													class="btn btn-status btn-danger mt-2">
													<div class="d-flex align-items-center justify-content-center">
														<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
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
							<?php if ($request_status['text'] == 'Rejected'): ?>
							<div class="mt-4">
								<h5>Rejected reason :</h5>
								<textarea class="form-control" readonly id=""
									rows="3"><?= $detail['rejected_reason'] ?></textarea>
							</div>
								<?php if ($detail['requestor_id'] == $this->user_data->userId): ?>
								<div class="mt-3 d-flex justify-content-end align-items-center">
									<button id="btn-resubmit" data-id="<?= $detail['r_id'] ?>"
										class="btn btn btn-success">RESUBMIT REQUEST</button>
								</div>
								<?php endif; ?>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal -->
	<div class="modal fade" id="payment-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
		aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Payment request</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form enctype="multipart/form-data" method="POST"
					action="<?= base_url('ea_requests/incoming-requests/payment') ?>" id="payment-form">
					<div class="modal-body">
						<input type="text" class="d-none" name="r_id" id="r_id" value="<?= $detail['r_id'] ?>">
						<div class="form-group">
							<label for="date_of_transfer">Date of transfer</label>
							<input type="date" class="form-control" name="date_of_transfer" id="date_of_transfer"
								value="<?= date('Y-m-d') ?>">
						</div>
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
	</div>

	<script>
		$(document).ready(function () {
			$('#max_budget_idr, #max_budget_usd').number(true, 0, '', '.');
			$(document).on('click', '#btn-to-budget-form', function (e) {
				e.preventDefault()
				$('html, body').animate({
					scrollTop: $("#max-budget-form").offset().top - 180
				}, 500);
			})
			$(document).on('click', '.btn-edit-costs', function (e) {
				e.preventDefault()
				const dest_id = $(this).attr('data-dest-id')
				$.get(base_url + `ea_requests/outcoming_requests/edit_costs_modal?dest_id=${dest_id}`,
					function (html) {
						$('#myModal').html(html)
						$('#lodging, #meals').number(true, 0, '', '.');
						$('#myModal').modal('show')
					});
			})
			$(document).on('click', '#btn-resubmit', function (e) {
				e.preventDefault()
				const request_id = $(this).attr('data-id')
				const loader = `<div style="width: 5rem; height: 5rem;" class="spinner-border mb-5" role="status"></div>
					<h5 class="mt-2">Please wait</h5>
					<p>Resubmit request and sending email ...</p>`
				Swal.fire({
					title: 'Resubmit request?',
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
							url: base_url + 'ea_requests/outcoming_requests/resubmit_request',
							data: {
								request_id
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
			})
			$(document).on("submit", '#update-costs', function (e) {
				e.preventDefault()
				const formData = new FormData(this);
				const loader = `<div style="width: 5rem; height: 5rem;" class="spinner-border mb-5" role="status"></div>
					<h5 class="mt-2">Please wait</h5>
					<p>Updating costs ...</p>`
				Swal.fire({
					title: 'Update costs?',
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
			$(document).on('click', '.btn-status', function (e) {
				e.preventDefault()
				const id = $(this).attr('data-id')
				const status = $(this).attr('data-status')
				const level = $(this).attr('data-level')
				const loader = `<div style="width: 5rem; height: 5rem;" class="spinner-border mb-5" role="status"></div>
				<h5 class="mt-2">Please wait</h5>
				<p>Updating status and sending email...</p>	`
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
							title: `Reject request and send notification email?`,
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
				} else {
					Swal.fire({
						title: 'Are you sure?',
						text: `Approve request and send notification email?`,
						type: 'warning',
						showCancelButton: true,
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						confirmButtonText: `Yes, approve it!`
					}).then((result) => {
						const formData = {
							id: id,
							status: status,
							level: level
						}
						if (result.value) {
							$.ajax({
								type: 'POST',
								dataType: 'JSON',
								url: base_url + 'ea_requests/incoming_requests/set_status',
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
									console.log(response)
									Swal.fire({
										"title": "Something went wrong!",
										"text": response.message,
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
											// window.location.replace(base_url +
											// 	'ea_requests/incoming-requests/requests-for-review'
											// )
										}
									})
								},
							});
						}
					})
				}

			});
		});

		$(document).on('click', '.btn-payment', function (e) {
			e.preventDefault()
			const id = $(this).attr('data-id')
			const level = $(this).attr('data-level')
			const loader = `<div style="width: 5rem; height: 5rem;" class="spinner-border mb-5" role="status"></div>
				<h5 class="mt-2">Please wait</h5>
				<p>Process payment request and sending email to requestor...</p>				
				`
			$('#payment-modal').modal('show')
			$(document).on("submit", '#payment-form', function (e) {
				e.preventDefault()
				const loader = `<div style="width: 5rem; height: 5rem;" class="spinner-border mb-5" role="status"></div>
				<h5 class="mt-2">Please wait</h5>
				<p>Saving data and send email to requestor...</p>				
				`
				const formData = new FormData(this);
				Swal.fire({
					title: 'Process payment and send email to requestor?',
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
								for (const err in response.errors) {
									$(`#${err}`).parent().append(
										`<p class="error mt-1 mb-0">This field is required</p>`
									)
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

		$(document).on("submit", '#max-budget-form', function (e) {
			e.preventDefault()
			const loader = `<div style="width: 5rem; height: 5rem;" class="spinner-border mb-5" role="status"></div>
				<h5 class="mt-2">Please wait</h5>
				<p>Updating max budget...</p>				
				`
			Swal.fire({
				title: 'Update max budget?',
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
						dataType: 'JSON',
						url: $(this).attr("action"),
						data: $(this).serialize(),
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
							for (const err in response.errors) {
								$(`#${err}`).parent().append(
									'<p class="error mt-1 mb-0">This field is required</p>')
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
					});
				}
			})
		});

	</script>
