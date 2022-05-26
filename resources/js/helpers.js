export function initSelect2(selector, customConfig = null) {
	const $this = $(selector);
	const url = $this.data("url");
	const placeholder = $this.data("placeholder");
  
	let config = {
	  containerCssClass: ":all:",
	  placeholder: placeholder,
	  allowClear: true,
	  disabled: $this.prop("disabled"),
	  maximumSelectionLength: 5,
	};
  
	if (url) {
	  config = Object.assign(config, {
		ajax: {
		  url: url,
		  dataType: "json",
		  delay: 600,
		  processResults: function (data) {
			return {
			  results: data,
			};
		  },
		  cache: true,
		},
	  });
	}
  
	if (customConfig) {
	  config = Object.assign(config, customConfig);
	}
  
	return $this.select2(config);
  }

export function initDatatable(selector, config = {}) {
	const defaultConfig = {
		ordering: true,
		processing: true,
		serverSide: true,
		order: [[7, 'desc']],
		ajax: {
			url: $(selector).attr("data-url"),
			type: "POST",
		},
	};

	const mergedConfig = {
		...defaultConfig,
		...config
	};
	return $(selector).DataTable(mergedConfig);
}
