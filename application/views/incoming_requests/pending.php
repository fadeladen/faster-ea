<div class="kt-portlet kt-portlet--mobile">
	<div class="kt-portlet__head kt-portlet__head--lg">
		<div class="kt-portlet__head-label">
			<span class="kt-portlet__head-icon">
				<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
					class="bi bi-hourglass-split" viewBox="0 0 16 16">
					<path
						d="M2.5 15a.5.5 0 1 1 0-1h1v-1a4.5 4.5 0 0 1 2.557-4.06c.29-.139.443-.377.443-.59v-.7c0-.213-.154-.451-.443-.59A4.5 4.5 0 0 1 3.5 3V2h-1a.5.5 0 0 1 0-1h11a.5.5 0 0 1 0 1h-1v1a4.5 4.5 0 0 1-2.557 4.06c-.29.139-.443.377-.443.59v.7c0 .213.154.451.443.59A4.5 4.5 0 0 1 12.5 13v1h1a.5.5 0 0 1 0 1h-11zm2-13v1c0 .537.12 1.045.337 1.5h6.326c.216-.455.337-.963.337-1.5V2h-7zm3 6.35c0 .701-.478 1.236-1.011 1.492A3.5 3.5 0 0 0 4.5 13s.866-1.299 3-1.48V8.35zm1 0v3.17c2.134.181 3 1.48 3 1.48a3.5 3.5 0 0 0-1.989-3.158C8.978 9.586 8.5 9.052 8.5 8.351z" />
				</svg>
			</span>
			<h3 class="kt-portlet__head-title">
				Pending Requests
				<small>Requests in progress</small>
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
		}]
	})

</script>
