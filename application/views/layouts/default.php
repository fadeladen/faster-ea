<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Faster | <?= (isset($page) ? $page : '') ?></title>
	<!--begin::Global Theme Styles(used by all pages) -->
	<!--end::Global Theme Styles -->
	<link rel="stylesheet" href="<?= site_url('assets/compiled/css/app.css?v=' . ASSETS_VERSION) ?>">
	<link href="<?= site_url('assets/css/demo1/style.bundle.css') ?>" rel="stylesheet" type="text/css" />
	<!--begin::Fonts -->
	<link rel="stylesheet"
		href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700|Roboto:300,400,500,600,700">
	<!--end::Fonts -->

	<!--begin::Page Custom Styles(used by this page) -->
	<!--end::Page Custom Styles -->

	<!--begin:: Global Mandatory Vendors -->
	<link href="<?= site_url('assets/vendors/custom/vendors/line-awesome/css/line-awesome.css') ?>" rel="stylesheet"
		type="text/css" />
	<link href="<?= site_url('assets/vendors/general/perfect-scrollbar/css/perfect-scrollbar.css') ?>" rel="stylesheet"
		type="text/css" />
	<link href="<?= site_url('assets/vendors/general/sweetalert2/dist/sweetalert2.min.css') ?>" rel="stylesheet"
		type="text/css" />
	<!--end:: Global Mandatory Vendors -->

	<script src="<?= site_url('assets/compiled/js/app.js?v=' . ASSETS_VERSION) ?>"></script>

	<?php
	if (isset($assets_css)) {
		foreach ($assets_css as $asset_css) {
	?>
	<link rel="stylesheet" href="<?= $asset_css ?>">
	<?php
		}
	}
	?>

	<?php
	if (isset($assets_js)) {
		foreach ($assets_js as $js) {
			echo "\r\t" . script_tag($js) . "\r\n";
		}
	}
	?>

	<script>
		const base_url = "<?= base_url() ?>"

	</script>
</head>

<body
	class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--enabled kt-subheader--fixed kt-subheader--solid kt-aside--enabled kt-aside--fixed kt-page--loading">


	<!-- begin:: Page -->
	<!-- begin:: Header Mobile -->
	<div id="kt_header_mobile" class="kt-header-mobile  kt-header-mobile--fixed ">
		<div class="kt-header-mobile__logo">
			<a href="<?= base_url('dashboard') ?>">
				<img class="mt-2 mr-1" style="height: 2.73rem; width: auto;" alt="Logo"
					src="<?= site_url('assets/images/logos/faster_v2.png') ?>">
				<!-- <h3 class="text-light">Faster</h3> -->
			</a>
		</div>
		<div class="kt-header-mobile__toolbar">
			<button class="kt-header-mobile__toggler kt-header-mobile__toggler--left"
				id="kt_aside_mobile_toggler"><span></span></button>
		</div>
	</div>
	<!-- end:: Header Mobile -->
	<div class="kt-grid kt-grid--hor kt-grid--root">
		<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">
			<!-- begin:: Aside -->
			<button class="kt-aside-close " id="kt_aside_close_btn"><i class="la la-close"></i></button>

			<div class="kt-aside kt-aside--fixed  kt-grid__item kt-grid kt-grid--desktop kt-grid--hor-desktop"
				id="kt_aside">
				<!-- begin:: Aside -->
				<div style="border-bottom: 1px solid #2d2d43; height: 74px;" class="kt-aside__brand kt-grid__item "
					id="kt_aside_brand">


					<div class="kt-aside__brand-logo hidden-aside">
						<a href="<?= base_url('dashboard') ?>">
							<img class="mt-1 mr-1" style="height: 2.75rem; width: auto;" alt="Logo"
								src="<?= site_url('assets/images/logos/faster_v2.png') ?>">
							<!-- <h3 class="text-light">Faster</h3> -->
						</a>
					</div>

					<div class="kt-aside__brand-tools">
						<button class="kt-aside__brand-aside-toggler" id="kt_aside_toggler">
							<span><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
									width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										<polygon id="Shape" points="0 0 24 0 24 24 0 24" />
										<path
											d="M5.29288961,6.70710318 C4.90236532,6.31657888 4.90236532,5.68341391 5.29288961,5.29288961 C5.68341391,4.90236532 6.31657888,4.90236532 6.70710318,5.29288961 L12.7071032,11.2928896 C13.0856821,11.6714686 13.0989277,12.281055 12.7371505,12.675721 L7.23715054,18.675721 C6.86395813,19.08284 6.23139076,19.1103429 5.82427177,18.7371505 C5.41715278,18.3639581 5.38964985,17.7313908 5.76284226,17.3242718 L10.6158586,12.0300721 L5.29288961,6.70710318 Z"
											id="Path-94" fill="#000000" fill-rule="nonzero"
											transform="translate(8.999997, 11.999999) scale(-1, 1) translate(-8.999997, -11.999999) " />
										<path
											d="M10.7071009,15.7071068 C10.3165766,16.0976311 9.68341162,16.0976311 9.29288733,15.7071068 C8.90236304,15.3165825 8.90236304,14.6834175 9.29288733,14.2928932 L15.2928873,8.29289322 C15.6714663,7.91431428 16.2810527,7.90106866 16.6757187,8.26284586 L22.6757187,13.7628459 C23.0828377,14.1360383 23.1103407,14.7686056 22.7371482,15.1757246 C22.3639558,15.5828436 21.7313885,15.6103465 21.3242695,15.2371541 L16.0300699,10.3841378 L10.7071009,15.7071068 Z"
											id="Path-94" fill="#000000" fill-rule="nonzero" opacity="0.3"
											transform="translate(15.999997, 11.999999) scale(-1, 1) rotate(-270.000000) translate(-15.999997, -11.999999) " />
									</g>
								</svg></span>
							<span><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
									width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										<polygon id="Shape" points="0 0 24 0 24 24 0 24" />
										<path
											d="M12.2928955,6.70710318 C11.9023712,6.31657888 11.9023712,5.68341391 12.2928955,5.29288961 C12.6834198,4.90236532 13.3165848,4.90236532 13.7071091,5.29288961 L19.7071091,11.2928896 C20.085688,11.6714686 20.0989336,12.281055 19.7371564,12.675721 L14.2371564,18.675721 C13.863964,19.08284 13.2313966,19.1103429 12.8242777,18.7371505 C12.4171587,18.3639581 12.3896557,17.7313908 12.7628481,17.3242718 L17.6158645,12.0300721 L12.2928955,6.70710318 Z"
											id="Path-94" fill="#000000" fill-rule="nonzero" />
										<path
											d="M3.70710678,15.7071068 C3.31658249,16.0976311 2.68341751,16.0976311 2.29289322,15.7071068 C1.90236893,15.3165825 1.90236893,14.6834175 2.29289322,14.2928932 L8.29289322,8.29289322 C8.67147216,7.91431428 9.28105859,7.90106866 9.67572463,8.26284586 L15.6757246,13.7628459 C16.0828436,14.1360383 16.1103465,14.7686056 15.7371541,15.1757246 C15.3639617,15.5828436 14.7313944,15.6103465 14.3242754,15.2371541 L9.03007575,10.3841378 L3.70710678,15.7071068 Z"
											id="Path-94" fill="#000000" fill-rule="nonzero" opacity="0.3"
											transform="translate(9.000003, 11.999999) rotate(-270.000000) translate(-9.000003, -11.999999) " />
									</g>
								</svg></span>
						</button>
					</div>
				</div>
				<!-- end:: Aside -->
				<!-- begin:: Aside Menu -->
				<div class="kt-aside-menu-wrapper kt-grid__item kt-grid__item--fluid" id="kt_aside_menu_wrapper">

					<div style="display: flex !important; flex-direction: column !important; justify-content: space-between !important; margin-top: 2px !important;"
						id="kt_aside_menu" class="kt-aside-menu " data-ktmenu-vertical="1" data-ktmenu-scroll="1"
						data-ktmenu-dropdown-timeout="500">
						<div>
							<div
								class="ml-4 aside-user d-flex align-items-sm-center justify-content-center hidden-aside">
								<div class="symbol symbol-50px">
									<div class="mr-2 container-img">
										<img class="avatar-img"
											src="<?= ($this->user_data->avatar == null) ? base_url('assets/images/no-avatar.png') : $_ENV['ASSETS_URL'] . $this->user_data->avatar .'?subfolder=avatars&token=' . $_ENV['ASSETS_TOKEN'] ?>"
											alt="">
									</div>
								</div>
								<div class="aside-user-info flex-row-fluid flex-wrap mt-2 pt-1 p-2">
									<div class="d-flex">
										<div class="flex-grow-1 pl-2 pt-1">
											<span class="text-white fw-bold username">
												<?= $this->user_data->fullName ?>
											</span>
											<small
												class="text-muted fw-bold d-block mb-1 user-email"><?= $this->user_data->username ?>
											</small>
											<small class="d-flex align-items-center text-success online-status">
												<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
													fill="currentColor" class="bi bi-dot" viewBox="0 0 16 16">
													<path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z" />
												</svg>
												<span>
													online
												</span>
											</small>
										</div>
										<div class="pb-2 pr-2 mt-1"><a href="#"
												class="btn btn-icon btn-sm btn-active-color-primary mt-n2"
												data-kt-menu-trigger="click" data-kt-menu-placement="bottom-start"
												data-kt-menu-overflow="true"><span class="pulse-ring"></span><span
													class="svg-icon svg-icon-muted svg-icon-1"><svg
														xmlns="http://www.w3.org/2000/svg" width="24" height="24"
														viewBox="0 0 24 24" fill="none">
														<path opacity="0.3"
															d="M22.1 11.5V12.6C22.1 13.2 21.7 13.6 21.2 13.7L19.9 13.9C19.7 14.7 19.4 15.5 18.9 16.2L19.7 17.2999C20 17.6999 20 18.3999 19.6 18.7999L18.8 19.6C18.4 20 17.8 20 17.3 19.7L16.2 18.9C15.5 19.3 14.7 19.7 13.9 19.9L13.7 21.2C13.6 21.7 13.1 22.1 12.6 22.1H11.5C10.9 22.1 10.5 21.7 10.4 21.2L10.2 19.9C9.4 19.7 8.6 19.4 7.9 18.9L6.8 19.7C6.4 20 5.7 20 5.3 19.6L4.5 18.7999C4.1 18.3999 4.1 17.7999 4.4 17.2999L5.2 16.2C4.8 15.5 4.4 14.7 4.2 13.9L2.9 13.7C2.4 13.6 2 13.1 2 12.6V11.5C2 10.9 2.4 10.5 2.9 10.4L4.2 10.2C4.4 9.39995 4.7 8.60002 5.2 7.90002L4.4 6.79993C4.1 6.39993 4.1 5.69993 4.5 5.29993L5.3 4.5C5.7 4.1 6.3 4.10002 6.8 4.40002L7.9 5.19995C8.6 4.79995 9.4 4.39995 10.2 4.19995L10.4 2.90002C10.5 2.40002 11 2 11.5 2H12.6C13.2 2 13.6 2.40002 13.7 2.90002L13.9 4.19995C14.7 4.39995 15.5 4.69995 16.2 5.19995L17.3 4.40002C17.7 4.10002 18.4 4.1 18.8 4.5L19.6 5.29993C20 5.69993 20 6.29993 19.7 6.79993L18.9 7.90002C19.3 8.60002 19.7 9.39995 19.9 10.2L21.2 10.4C21.7 10.5 22.1 11 22.1 11.5ZM12.1 8.59998C10.2 8.59998 8.6 10.2 8.6 12.1C8.6 14 10.2 15.6 12.1 15.6C14 15.6 15.6 14 15.6 12.1C15.6 10.2 14 8.59998 12.1 8.59998Z"
															fill="gray"></path>
														<path
															d="M17.1 12.1C17.1 14.9 14.9 17.1 12.1 17.1C9.30001 17.1 7.10001 14.9 7.10001 12.1C7.10001 9.29998 9.30001 7.09998 12.1 7.09998C14.9 7.09998 17.1 9.29998 17.1 12.1ZM12.1 10.1C11 10.1 10.1 11 10.1 12.1C10.1 13.2 11 14.1 12.1 14.1C13.2 14.1 14.1 13.2 14.1 12.1C14.1 11 13.2 10.1 12.1 10.1Z"
															fill="#a1a5b7"></path>
													</svg></span></a>
											<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold py-4 fs-6 w-275px"
												data-kt-menu="true"></div>
										</div>
									</div>
								</div>
							</div>
							<ul class="kt-menu__nav ">
								<li class="kt-menu__item " aria-haspopup="true"><a
										href="<?= $_ENV['STAGING_URL'] ?>dashboard" class="kt-menu__link "><span
											class="kt-menu__link-icon"><svg xmlns="http://www.w3.org/2000/svg"
												xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
												viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
												<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
													<polygon id="Bound" points="0 0 24 0 24 24 0 24" />
													<path
														d="M12.9336061,16.072447 L19.36,10.9564761 L19.5181585,10.8312381 C20.1676248,10.3169571 20.2772143,9.3735535 19.7629333,8.72408713 C19.6917232,8.63415859 19.6104327,8.55269514 19.5206557,8.48129411 L12.9336854,3.24257445 C12.3871201,2.80788259 11.6128799,2.80788259 11.0663146,3.24257445 L4.47482784,8.48488609 C3.82645598,9.00054628 3.71887192,9.94418071 4.23453211,10.5925526 C4.30500305,10.6811601 4.38527899,10.7615046 4.47382636,10.8320511 L4.63,10.9564761 L11.0659024,16.0730648 C11.6126744,16.5077525 12.3871218,16.5074963 12.9336061,16.072447 Z"
														id="Shape" fill="#000000" fill-rule="nonzero" />
													<path
														d="M11.0563554,18.6706981 L5.33593024,14.122919 C4.94553994,13.8125559 4.37746707,13.8774308 4.06710397,14.2678211 C4.06471678,14.2708238 4.06234874,14.2738418 4.06,14.2768747 L4.06,14.2768747 C3.75257288,14.6738539 3.82516916,15.244888 4.22214834,15.5523151 C4.22358765,15.5534297 4.2250303,15.55454 4.22647627,15.555646 L11.0872776,20.8031356 C11.6250734,21.2144692 12.371757,21.2145375 12.909628,20.8033023 L19.7677785,15.559828 C20.1693192,15.2528257 20.2459576,14.6784381 19.9389553,14.2768974 C19.9376429,14.2751809 19.9363245,14.2734691 19.935,14.2717619 L19.935,14.2717619 C19.6266937,13.8743807 19.0546209,13.8021712 18.6572397,14.1104775 C18.654352,14.112718 18.6514778,14.1149757 18.6486172,14.1172508 L12.9235044,18.6705218 C12.377022,19.1051477 11.6029199,19.1052208 11.0563554,18.6706981 Z"
														id="Path" fill="#000000" opacity="0.3" />
												</g>
											</svg></span><span class="kt-menu__link-text">Dashboard</span></a></li>
								<li class="kt-menu__section ">
									<h4 class="kt-menu__section-text">Expenses Authorization</h4>
									<i class="kt-menu__section-icon flaticon-more-v2"></i>
								</li>
								<?php if (is_ea_assosiate() || is_fco_monitor() || is_finance_teams() || is_head_of_units() || is_line_supervisor() ||$this->user_data->username == 'root'): ?>

								<li class="kt-menu__item  kt-menu__item--submenu <?= ($pageParent == 'Incoming Requests' ? 'kt-menu__item--open' : '') ?>"
									aria-haspopup="true" data-ktmenu-submenu-toggle="hover"><a href="javascript:;"
										class="kt-menu__link kt-menu__toggle">
										<span class="kt-menu__link-icon">
											<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="gray"
												class="bi bi-file-earmark-arrow-down" viewBox="0 0 16 16">
												<path
													d="M8.5 6.5a.5.5 0 0 0-1 0v3.793L6.354 9.146a.5.5 0 1 0-.708.708l2 2a.5.5 0 0 0 .708 0l2-2a.5.5 0 0 0-.708-.708L8.5 10.293V6.5z" />
												<path
													d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z" />
											</svg>
										</span>
										<span class="kt-menu__link-text">Incoming Requests</span><i
											class="kt-menu__ver-arrow la la-angle-right"></i></a>
									<div class="kt-menu__submenu "><span class="kt-menu__arrow"></span>
										<ul class="kt-menu__subnav">
											<li class="kt-menu__item  kt-menu__item--parent" aria-haspopup="true"><span
													class="kt-menu__link"><span
														class="kt-menu__link-text">Subheaders</span></span></li>
											<li class="kt-menu__item <?= ($page == 'Requests for review' ? 'kt-menu__item--active' : '') ?>"" aria-haspopup="
												true">
												<a href="<?= base_url('ea_requests/incoming-requests/requests-for-review') ?>"
													class="kt-menu__link"><i
														class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
														class="kt-menu__link-text">
														Requests For Review</span>
												</a>
											</li>
											<li class="kt-menu__item <?= ($page == 'Pending requests' ? 'kt-menu__item--active' : '') ?>"" aria-haspopup="
												true">
												<a href="<?= base_url('ea_requests/incoming-requests/pending') ?>"
													class="kt-menu__link"><i
														class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
														class="kt-menu__link-text">
														Pending</span>
												</a>
											</li>
											<li class="kt-menu__item <?= ($page == 'Rejected requests' ? 'kt-menu__item--active' : '') ?>"
												aria-haspopup="true">
												<a href="<?= base_url('ea_requests/incoming-requests/rejected') ?>"
													class="kt-menu__link"><i
														class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
														class="kt-menu__link-text">
														Rejected</span>
												</a>
											</li>
											<li class="kt-menu__item <?= ($page == 'Done requests' ? 'kt-menu__item--active' : '') ?>"
												aria-haspopup="true">
												<a href="<?= base_url('ea_requests/incoming-requests/done') ?>"
													class="kt-menu__link"><i
														class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
														class="kt-menu__link-text">
														Done</span>
												</a>
											</li>
										</ul>
									</div>
								</li>

								<?php endif; ?>
								<li class="kt-menu__item  kt-menu__item--submenu <?= ($pageParent == 'Outcoming Requests' ? 'kt-menu__item--open' : '') ?>"
									aria-haspopup="true" data-ktmenu-submenu-toggle="hover"><a href="javascript:;"
										class="kt-menu__link kt-menu__toggle">
										<span class="kt-menu__link-icon">
											<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="gray"
												class="bi bi-file-earmark-arrow-up" viewBox="0 0 16 16">
												<path
													d="M8.5 11.5a.5.5 0 0 1-1 0V7.707L6.354 8.854a.5.5 0 1 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 7.707V11.5z" />
												<path
													d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z" />
											</svg>
										</span>
										<span class="kt-menu__link-text">Outgoing Requests</span><i
											class="kt-menu__ver-arrow la la-angle-right"></i></a>
									<div class="kt-menu__submenu "><span class="kt-menu__arrow"></span>
										<ul class="kt-menu__subnav">
											<li class="kt-menu__item  kt-menu__item--parent" aria-haspopup="true"><span
													class="kt-menu__link"><span
														class="kt-menu__link-text">Subheaders</span></span></li>
											<li class="kt-menu__item <?= ($page == 'Create request' ? 'kt-menu__item--active' : '') ?>"
												aria-haspopup="true">
												<a href="<?= base_url('ea_requests/outcoming-requests/create') ?>"
													class="kt-menu__link"><i
														class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
														class="kt-menu__link-text">
														Create new request</span>
												</a>
											</li>
											<li class="kt-menu__item <?= ($page == 'Pending requests' ? 'kt-menu__item--active' : '') ?>"
												aria-haspopup="true">
												<a href="<?= base_url('ea_requests/outcoming-requests/pending') ?>"
													class="kt-menu__link"><i
														class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
														class="kt-menu__link-text">
														Pending</span>
												</a>
											</li>
											<li class="kt-menu__item <?= ($page == 'Rejected requests' ? 'kt-menu__item--active' : '') ?>"
												aria-haspopup="true">
												<a href="<?= base_url('ea_requests/outcoming-requests/rejected') ?>"
													class="kt-menu__link"><i
														class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
														class="kt-menu__link-text">
														Rejected</span>
												</a>
											</li>
											<li class="kt-menu__item <?= ($page == 'Done requests' ? 'kt-menu__item--active' : '') ?>"
												aria-haspopup="true">
												<a href="<?= base_url('ea_requests/outcoming-requests/done') ?>"
													class="kt-menu__link"><i
														class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
														class="kt-menu__link-text">
														Done</span>
												</a>
											</li>
										</ul>
									</div>
								</li>
								<li class="kt-menu__section ">
									<h4 class="kt-menu__section-text">Travel Expenses Report</h4>
									<i class="kt-menu__section-icon flaticon-more-v2"></i>
								</li>
								<?php if (is_head_of_units() || is_country_director() || is_finance_teams() ||$this->user_data->username == 'root'): ?>
								<li class="kt-menu__item  kt-menu__item--submenu <?= ($pageParent == 'Incoming report' ? 'kt-menu__item--open' : '') ?>"
									aria-haspopup="true" data-ktmenu-submenu-toggle="hover"><a href="javascript:;"
										class="kt-menu__link kt-menu__toggle">
										<span class="kt-menu__link-icon">
											<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="gray"
												class="bi bi-file-earmark-text" viewBox="0 0 16 16">
												<path
													d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5z" />
												<path
													d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5L9.5 0zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z" />
											</svg>
										</span>
										<span class="kt-menu__link-text">Incoming </span><i
											class="kt-menu__ver-arrow la la-angle-right"></i></a>
									<div class="kt-menu__submenu "><span class="kt-menu__arrow"></span>
										<ul class="kt-menu__subnav">
											<li class="kt-menu__item  kt-menu__item--parent" aria-haspopup="true"><span
													class="kt-menu__link"><span
														class="kt-menu__link-text">Subheaders</span></span></li>
											<li class="kt-menu__item <?= ($page == 'New report' ? 'kt-menu__item--active' : '') ?>"
												aria-haspopup="true">
												<a href="<?= base_url('ea_report/incoming') ?>" class="kt-menu__link"><i
														class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
														class="kt-menu__link-text">
														New Report</span>
												</a>
											</li>
											<li class="kt-menu__item <?= ($page == 'Pending report' ? 'kt-menu__item--active' : '') ?>"
												aria-haspopup="true">
												<a href="<?= base_url('ea_report/incoming/pending') ?>"
													class="kt-menu__link"><i
														class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
														class="kt-menu__link-text">
														Pending</span>
												</a>
											</li>
											<li class="kt-menu__item <?= ($page == 'Approved report' ? 'kt-menu__item--active' : '') ?>"
												aria-haspopup="true">
												<a href="<?= base_url('ea_report/incoming/approved') ?>"
													class="kt-menu__link"><i
														class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
														class="kt-menu__link-text">
														Approved</span>
												</a>
											</li>
											<li class="kt-menu__item <?= ($page == 'Rejected report' ? 'kt-menu__item--active' : '') ?>"
												aria-haspopup="true">
												<a href="<?= base_url('ea_report/incoming/rejected') ?>"
													class="kt-menu__link"><i
														class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
														class="kt-menu__link-text">
														Rejected</span>
												</a>
											</li>
											<li class="kt-menu__item <?= ($page == 'Paid report' ? 'kt-menu__item--active' : '') ?>"
												aria-haspopup="true">
												<a href="<?= base_url('ea_report/incoming/paid') ?>"
													class="kt-menu__link"><i
														class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
														class="kt-menu__link-text">
														Paid</span>
												</a>
											</li>
										</ul>
									</div>
								</li>
								<?php endif; ?>

								<li class="kt-menu__item  kt-menu__item--submenu <?= ($pageParent == 'Outgoing report' ? 'kt-menu__item--open' : '') ?>"
									aria-haspopup="true" data-ktmenu-submenu-toggle="hover"><a href="javascript:;"
										class="kt-menu__link kt-menu__toggle">
										<span class="kt-menu__link-icon">
											<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="gray"
												class="bi bi-file-earmark-text" viewBox="0 0 16 16">
												<path
													d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5z" />
												<path
													d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5L9.5 0zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z" />
											</svg>
										</span>
										<span class="kt-menu__link-text">Outgoing </span><i
											class="kt-menu__ver-arrow la la-angle-right"></i></a>
									<div class="kt-menu__submenu "><span class="kt-menu__arrow"></span>
										<ul class="kt-menu__subnav">
											<li class="kt-menu__item  kt-menu__item--parent" aria-haspopup="true"><span
													class="kt-menu__link"><span
														class="kt-menu__link-text">Subheaders</span></span></li>
											<li class="kt-menu__item <?= ($page == 'New report' ? 'kt-menu__item--active' : '') ?>"
												aria-haspopup="true">
												<a href="<?= base_url('ea_report/outgoing') ?>" class="kt-menu__link"><i
														class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
														class="kt-menu__link-text">
														New Report</span>
												</a>
											</li>
											<li class="kt-menu__item <?= ($page == 'Pending report' ? 'kt-menu__item--active' : '') ?>"
												aria-haspopup="true">
												<a href="<?= base_url('ea_report/outgoing/pending') ?>"
													class="kt-menu__link"><i
														class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
														class="kt-menu__link-text">
														Pending</span>
												</a>
											</li>
											<li class="kt-menu__item <?= ($page == 'Rejected report' ? 'kt-menu__item--active' : '') ?>"
												aria-haspopup="true">
												<a href="<?= base_url('ea_report/outgoing/rejected') ?>"
													class="kt-menu__link"><i
														class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
														class="kt-menu__link-text">
														Rejected</span>
												</a>
											</li>
											<li class="kt-menu__item <?= ($page == 'Approved report' ? 'kt-menu__item--active' : '') ?>"
												aria-haspopup="true">
												<a href="<?= base_url('ea_report/outgoing/approved') ?>"
													class="kt-menu__link"><i
														class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
														class="kt-menu__link-text">
														Approved</span>
												</a>
											</li>
											<li class="kt-menu__item <?= ($page == 'Paid report' ? 'kt-menu__item--active' : '') ?>"
												aria-haspopup="true">
												<a href="<?= base_url('ea_report/outgoing/paid') ?>"
													class="kt-menu__link"><i
														class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
														class="kt-menu__link-text">
														Paid</span>
												</a>
											</li>
										</ul>
									</div>
								</li>
							</ul>
						</div>
						<div class="px-3 hidden-aside">
							<a class="btn btn-dark w-100" href="<?= base_url('auth/logout') ?>">
								<span>
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
										fill="none">
										<rect opacity="0.3" x="8.5" y="11" width="12" height="2" rx="1" fill="white">
										</rect>
										<path
											d="M10.3687 11.6927L12.1244 10.2297C12.5946 9.83785 12.6268 9.12683 12.194 8.69401C11.8043 8.3043 11.1784 8.28591 10.7664 8.65206L7.84084 11.2526C7.39332 11.6504 7.39332 12.3496 7.84084 12.7474L10.7664 15.3479C11.1784 15.7141 11.8043 15.6957 12.194 15.306C12.6268 14.8732 12.5946 14.1621 12.1244 13.7703L10.3687 12.3073C10.1768 12.1474 10.1768 11.8526 10.3687 11.6927Z"
											fill="white"></path>
										<path opacity="0.5"
											d="M16 5V6C16 6.55228 15.5523 7 15 7C14.4477 7 14 6.55228 14 6C14 5.44772 13.5523 5 13 5H6C5.44771 5 5 5.44772 5 6V18C5 18.5523 5.44771 19 6 19H13C13.5523 19 14 18.5523 14 18C14 17.4477 14.4477 17 15 17C15.5523 17 16 17.4477 16 18V19C16 20.1046 15.1046 21 14 21H5C3.89543 21 3 20.1046 3 19V5C3 3.89543 3.89543 3 5 3H14C15.1046 3 16 3.89543 16 5Z"
											fill="white"></path>
									</svg>
								</span>
								<span style="font-size:  1.1rem !important;">
									Sign Out
								</span>
							</a>
						</div>
					</div>
				</div>
				<!-- end:: Aside Menu -->
			</div>
			<!-- end:: Aside -->
			<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper">
				<!-- begin:: Header -->
				<div id="kt_header" class="kt-header kt-grid__item  kt-header--fixed">

					<!-- begin:: Header Menu -->
					<button class="kt-header-menu-wrapper-close" id="kt_header_menu_mobile_close_btn"><i
							class="la la-close"></i></button>
					<div id="kt_header_menu"
						class="kt-header-menu kt-header-menu-mobile  kt-header-menu--layout-default d-flex flex-column">
						<h4 class="fw-bold mt-3 mb-1 text-dark"><?= (isset($pageParent) ? $pageParent : '') ?></h4>
						<span class="fw-bold text-muted"><?= (isset($page) ? $page : '') ?></span>
					</div>
					<!-- end:: Header Menu -->
					<!-- begin:: Header Topbar -->
					<div class="kt-header__topbar">
						<!--begin: User Bar -->

						<!--end: User Bar -->
					</div>
					<!-- end:: Header Topbar -->
				</div>
				<!-- end:: Header -->
				<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
					<!-- <div class="kt-subheader   kt-grid__item" id="kt_subheader">
						<div class="kt-container  kt-container--fluid ">
							<div class="kt-subheader__main">
								<h3 class="kt-subheader__title">
									<?= (isset($pageParent) ? $pageParent : '') ?>
								</h3>
								<span class="kt-subheader__separator kt-hidden"></span>
								<div class="kt-subheader__breadcrumbs">
									<svg xmlns="http://www.w3.org/2000/svg" width="16px" height="24" viewBox="0 0 24 24"
										fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
										stroke-linejoin="round" class="feather feather-chevrons-right text-muted">
										<polyline points="13 17 18 12 13 7"></polyline>
										<polyline points="6 17 11 12 6 7"></polyline>
									</svg>
									<a href="" class="kt-subheader__breadcrumbs-link ml-2">
										<?= (isset($page) ? $page : '') ?>
									</a>
								</div>
							</div>
							<div class="kt-subheader__toolbar">
								<div class="kt-subheader__wrapper">

								</div>
							</div>
						</div>
					</div> -->

					<!-- begin:: Content -->
					<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">

						<?= $content ?>

					</div>
					<!-- end:: Content -->
				</div>

				<!-- begin:: Footer -->
				<div class="py-3 px-3 bg-white" id="kt_footer">
					<div
						class="container-fluid d-flex px-4 flex-column flex-md-row align-items-center justify-content-between">
						<div class="text-dark order-2 order-md-1"><span class="text-muted fw-bold me-1">2022 © </span><a
								href="/dashboard" class="text-dark">Faster Version 2.0</a></div>
						<ul class="d-flex fw-bold order-1">
							<li class="px-2 text-muted list-unstyled">EpiC</li>
							<li class="px-2 text-muted list-unstyled">FHI360 - INDONESIA</li>
						</ul>
					</div>
				</div>
				<!-- end:: Footer -->
			</div>
		</div>
	</div>

	<!-- end:: Page -->

	<!-- begin::Scrolltop -->
	<div id="kt_scrolltop" class="kt-scrolltop text-white">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-arrow-up"
			viewBox="0 0 16 16">
			<path fill-rule="evenodd"
				d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5z" />
		</svg>
	</div>
	<!-- end::Scrolltop -->

	<!-- Modal -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">

	</div>
	<!-- begin::Global Config(global config for global JS sciprts) -->
	<script>
		var KTAppOptions = {
			"colors": {
				"state": {
					"brand": "#5d78ff",
					"dark": "#282a3c",
					"light": "#ffffff",
					"primary": "#5867dd",
					"success": "#34bfa3",
					"info": "#36a3f7",
					"warning": "#ffb822",
					"danger": "#fd3995"
				},
				"base": {
					"label": ["#c5cbe3", "#a1a8c3", "#3d4465", "#3e4466"],
					"shape": ["#f0f3ff", "#d9dffa", "#afb4d4", "#646c9a"]
				}
			}
		};

	</script>
	<!-- end::Global Config -->

	<!--begin:: Global Mandatory Vendors -->
	<script src="<?= site_url('assets/vendors/general/js-cookie/src/js.cookie.js') ?>" type="text/javascript"></script>
	<script src="<?= site_url('assets/vendors/general/jquery-number/jquery.number.min.js') ?>" type="text/javascript">
	</script>
	<script src="<?= site_url('assets/vendors/general/sweetalert2/dist/sweetalert2.min.js') ?>" type="text/javascript">
	</script>
	<script src="<?= site_url('assets/vendors/general/sticky-js/dist/sticky.min.js') ?>" type="text/javascript">
	</script>
	<script src="<?= site_url('assets/vendors/general/perfect-scrollbar/dist/perfect-scrollbar.js') ?>"
		type="text/javascript"></script>

	<!--end:: Global Mandatory Vendors -->

	<!--begin::Global Theme Bundle(used by all pages) -->
	<script src="<?= site_url('assets/js/demo1/scripts.bundle.js') ?>" type="text/javascript"></script>
	<!--end::Global Theme Bundle -->

	<script>
		var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
		var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
			return new bootstrap.Tooltip(tooltipTriggerEl)
		})

	</script>

</body>
