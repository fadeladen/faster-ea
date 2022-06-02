<div class="modal-dialog" role="document">
	<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="exampleModalLabel"><?= $items[0]['item_name'] ?> expenses</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">
			<div class="kt-datatable kt-datatable--default kt-datatable--brand kt-datatable--loaded border-bottom">
				<table class="kt-datatable__table" id="html_table" width="100%" style="display: block;">
					<thead class="kt-datatable__head">
						<tr class="kt-datatable__row" style="left: 0px;">
							<th class="kt-datatable__cell kt-datatable__cell--sort"><span style="width: 20px;">#</span>
							</th>
							<th class="kt-datatable__cell kt-datatable__cell--sort">
								<span style="width: 90px;">Actual cost</span></th>
							<th class="kt-datatable__cell kt-datatable__cell--sort">
								<span style="width: 80px;">Receipt</span></th>
						</tr>
					</thead>
					<tbody class="kt-datatable__body">
                        <?php $i = 1; ?>
						<?php foreach ($items as $item): ?>
						<tr data-row="0" class="kt-datatable__row" style="left: 0px;">
							<td class="kt-datatable__cell fw-bold">
								<span style="width: 20px;">
									<?= $i++ ?>
								</span>
							</td>
							<td class="kt-datatable__cell">
								<span style="width: 90px;">
									<span class="badge badge-pill badge-secondary fw-bold lodging_meals_budget">
										<?= $item['d_cost'] ?>
									</span>
								</span>
							</td>
							<td class="kt-datatable__cell">
								<span style="width: 80px;">
									<a target="_blank" class="badge badge-warning text-light"
										href="<?= base_url('uploads/ea_items_receipt/') ?><?= $item['receipt']  ?>">
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
											fill="currentColor" class="bi bi-card-image" viewBox="0 0 16 16">
											<path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z" />
											<path
												d="M1.5 2A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13zm13 1a.5.5 0 0 1 .5.5v6l-3.775-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12v.54A.505.505 0 0 1 1 12.5v-9a.5.5 0 0 1 .5-.5h13z" />
										</svg>
									</a>
								</span>
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		</div>
	</div>
</div>
