<script>
	$(document).ready(function () {
		Swal.fire({
			"title": "Sorry",
			"text": "This request has been expired",
			"type": "warning",
			"confirmButtonClass": "btn btn-dark"
		}).then((result) => {
			if (result.value) {
				window.close();
			}
		})
	});
</script>
