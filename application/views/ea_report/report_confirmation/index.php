<script>
	$(document).ready(function () {
		Swal.fire({
			"title": "Success!",
			"text": "<?= $message ?>",
			"type": "success",
			"confirmButtonClass": "btn btn-dark"
		}).then((result) => {
			if (result.value) {
				window.close();
			}
		})
	});
</script>
