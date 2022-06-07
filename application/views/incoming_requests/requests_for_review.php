<div class="kt-portlet kt-portlet--mobile">
	<div class="kt-portlet__head kt-portlet__head--lg">
		<div class="kt-portlet__head-label">
			<span class="kt-portlet__head-icon">
				<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
					class="bi bi-person-workspace" viewBox="0 0 16 16">
					<path d="M4 16s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H4Zm4-5.95a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" />
					<path
						d="M2 1a2 2 0 0 0-2 2v9.5A1.5 1.5 0 0 0 1.5 14h.653a5.373 5.373 0 0 1 1.066-2H1V3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v9h-2.219c.554.654.89 1.373 1.066 2h.653a1.5 1.5 0 0 0 1.5-1.5V3a2 2 0 0 0-2-2H2Z" />
				</svg>
			</span>
			<h3 class="kt-portlet__head-title">
				Requests for Review
				<small>All requests that you must review</small>
			</h3>
		</div>
		<div class="kt-portlet__head-toolbar">
			<div class="kt-portlet__head-wrapper">
				<div class="kt-portlet__head-actions">
					<a href="<?= base_url('ea_requests/outcoming-requests/create') ?>"
						class="btn btn-primary btn-icon-sm">
						<i class="la la-plus"></i>
						Create Request
					</a>
				</div>
			</div>
		</div>
	</div>

	<div class="kt-portlet__body">
		<table id="table-requests" class="table table-striped"
			data-url="<?= base_url('ea_requests/incoming-requests/datatable') ?>/<?= $status ?>">
			<thead>
				<tr>
					<th>EA Number</th>
					<th>Requestor</th>
					<th>Request base</th>
					<th>Employment</th>
					<th>Originating City</th>
					<th>Departure date</th>
					<th>Returning date</th>
					<th>Request date</th>
					<th class="action-col">Action</th>
				</tr>
			</thead>
			<tbody>

			</tbody>
		</table>
	</div>
</div>

<script>
	initDatatable('#table-requests', {
		order: [[8, 'desc']],
		columnDefs: [{
			targets: 'action-col',
			orderable: false,
			searchable: false,
			render: function (data) {
				return `
						<div class="d-flex align-items-center">
							<a href="${base_url}ea_requests/outcoming-requests/detail/${data}"
								 class="btn btn-sm btn-info mr-1">
								<div class="d-flex align-items-center justify-content-center">
								<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-info-circle" viewBox="0 0 16 16">
								<path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
								<path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
								</svg>
									<span class="ml-2">Details</span>
								</div>
							</a>
	                   </div>
	                `
			}
		}, ]
	})

</script>
