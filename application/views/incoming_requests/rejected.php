<div class="kt-portlet kt-portlet--mobile">
	<div class="kt-portlet__head kt-portlet__head--lg">
		<div class="kt-portlet__head-label">
			<span class="kt-portlet__head-icon">
				<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
					class="bi bi-file-earmark-x" viewBox="0 0 16 16">
					<path
						d="M6.854 7.146a.5.5 0 1 0-.708.708L7.293 9l-1.147 1.146a.5.5 0 0 0 .708.708L8 9.707l1.146 1.147a.5.5 0 0 0 .708-.708L8.707 9l1.147-1.146a.5.5 0 0 0-.708-.708L8 8.293 6.854 7.146z" />
					<path
						d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z" />
				</svg>
			</span>
			<h3 class="kt-portlet__head-title">
				Rejected Requests
				<small>All requests have been rejected</small>
			</h3>
		</div>
		<div class="kt-portlet__head-toolbar">
			<div class="kt-portlet__head-wrapper">
				<div class="kt-portlet__head-actions">
					<a href="<?= base_url('ea_requests/outcoming-requests/create') ?>" class="btn btn-primary btn-icon-sm">
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
		order: [[7, 'desc']],
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
