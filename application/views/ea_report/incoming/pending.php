<div class="kt-portlet kt-portlet--mobile">
	<div class="kt-portlet__head kt-portlet__head--lg">
		<div class="kt-portlet__head-label">
			<span class="kt-portlet__head-icon">
				<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="gray"
					class="bi bi-file-earmark-text" viewBox="0 0 16 16">
					<path
						d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5z" />
					<path
						d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5L9.5 0zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z" />
				</svg>
			</span>
			<h3 class="kt-portlet__head-title">
				Pending TER
				<small>TER in progress</small>
			</h3>
		</div>
	</div>

	<div class="kt-portlet__body">
		<table id="table-ter" class="table table-striped"
		data-url="<?= base_url('ea_report/incoming/datatable/') . 'pending'?>">
			<thead>
				<tr>
					<th style="width: 80px;">EA Number</th>
					<th style="min-width: 110px;">Request for</th>
					<th style="min-width: 100px;">Total advance</th>
					<th style="min-width: 100px;">Total expense</th>
					<th style="min-width: 100px;">Refund</th>
					<th style="min-width: 100px;">Reimburst</th>
					<th style="min-width: 100px;" class="action-col">Action</th>
				</tr>
			</thead>
			<tbody>

			</tbody>
		</table>
	</div>
</div>

<script>
	initDatatable('#table-ter', {
		order: [[6, 'desc']],
		columnDefs: [{
			targets: 'action-col',
			orderable: false,
			searchable: false,
			render: function (data) {
				return `
						<div class="d-flex flex-column align-items-start">
							<a href="${base_url}ea_report/outgoing/ter_detail/${data}"
								 class="btn btn-sm btn-primary mb-2">
								<div class="d-flex align-items-center justify-content-center">
								<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-info-circle" viewBox="0 0 16 16">
								<path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
								<path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
								</svg>
									<span class="ml-2">Detail</span>
								</div>
							</a>
	                   </div>
	                `
			}
		}, ]
	})

</script>
