
<!DOCTYPE html>
<html lang="en">
	<!--begin::Head-->
	<head>
		<meta charset="utf-8" />
		<title>Cargo Flare | Login</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<!--begin::Fonts-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
		<!--end::Fonts-->
		<!--begin::Page Custom Styles(used by this page)-->
		<link href="/styles/cargo_flare/login/login.js" rel="stylesheet" type="text/css" />
		<!--end::Page Custom Styles-->
		<!--begin::Global Theme Styles(used by all pages)-->
		<link href="/styles/cargo_flare/login/plugin.bundle.css" rel="stylesheet" type="text/css" />
		<link href="/styles/cargo_flare/login/prism.js.css" rel="stylesheet" type="text/css" />
		<link href="/styles/cargo_flare/login/style.bundle.css" rel="stylesheet" type="text/css" />
		<!--end::Global Theme Styles-->
	</head>
	<!--end::Head-->
	<!--begin::Body-->
	<body id="kt_body" class="header-fixed header-mobile-fixed header-bottom-enabled subheader-enabled page-loading">
		<!--begin::Main-->
		<div class="d-flex flex-column flex-root">
			<!--begin::Login-->
			<div class="login login-1 login-signin-on d-flex flex-column flex-lg-row flex-column-fluid bg-white" id="kt_login">
				<!--begin::Aside-->
				<div class="login-aside d-flex flex-column flex-row-auto" style="background-color: #F2C98A; width: 37%;">
					<!--begin::Aside Top-->
					<div class="d-flex flex-column-auto flex-column pt-lg-40 pt-15">
						<!--begin::Aside header-->
						<a href="#" class="text-center mb-10">
							<img alt="Cargo Flare" src="<?php echo SITE_IN ?>styles/cargo_flare/logo.png" class="max-h-70px">
						</a>
						<!--end::Aside header-->
						<!--begin::Aside title-->
						<h3 class="font-weight-bolder text-center font-size-h4 font-size-h1-lg" style="color: #986923;">Discover Amazing Metronic 
						<br />with great build tools</h3>
						<!--end::Aside title-->
					</div>
					<!--end::Aside Top-->
					<!--begin::Aside Bottom-->
					<div class="aside-img d-flex flex-row-fluid bgi-no-repeat bgi-position-y-bottom bgi-position-x-center" style="background-image: url(https://preview.keenthemes.com/metronic/theme/html/demo7/dist/assets/media/svg/illustrations/login-visual-1.svg)"></div>
					<!--end::Aside Bottom-->
				</div>
				<!--begin::Aside-->
				<!--begin::Content-->
				<div class="login-content flex-row-fluid d-flex flex-column justify-content-center position-relative overflow-hidden p-7 mx-auto">
					<!--begin::Content body-->
					<div class="d-flex flex-column-fluid flex-center">
						<!--begin::Signin-->
						<div class="" style="width: 40%;">
							<!--begin::Form-->
							@flash_message@
							<form action="<?=getLink("user", "signin")?>" method="post">
								<!--begin::Title-->
								<div class="pb-13 pt-lg-0 pt-5">
									<h3 class="font-weight-bolder text-dark text-center font-size-h4 font-size-h1-lg">Cargo Flare LLC</h3>
								</div>
								<!--begin::Title-->
								<!--begin::Form group-->
								<div class="form-group">
									<label class="font-size-h6 font-weight-bolder text-dark">Email</label>
									<input class="form-control form-control-solid h-auto py-6 px-6 rounded-lg" type="email" id="email" name="email" autocomplete="off" required/>
								</div>
								<!--end::Form group-->
								<!--begin::Form group-->
								<div class="form-group">
									<div class="d-flex justify-content-between mt-n5">
										<label class="font-size-h6 font-weight-bolder text-dark pt-5">Password</label>
										<a href="/user/forgot-password" class="text-primary font-size-h6 font-weight-bolder text-hover-primary pt-5">Forgot Password ?</a>
									</div>
									<input class="form-control form-control-solid h-auto py-6 px-6 rounded-lg" id="password" type="password" name="password" autocomplete="off" required/>
								</div>
								
								<!--end::Form group-->
								<!--begin::Action-->
								<div class="d-flex justify-content-between mt-n5">
									<div class="form-group text-right" style="margin-top: 0px;">
										@saveme@
									</div>
									<input type="hidden" class="m-input btn-primary btn-block" name="submit">
									<button type="submit" id="submit_button" class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-3">Sign In</button>
								</div>
								<!--end::Action-->
							</form>
							<!--end::Form-->
						</div>
						<!--end::Signin-->
					</div>
					<!--end::Content body-->
				</div>
				<!--end::Content-->
			</div>
			<!--end::Login-->
		</div>
		<!--end::Main-->
	</body>
	<!--end::Body-->
</html>