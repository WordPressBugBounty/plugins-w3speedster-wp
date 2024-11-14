<?php
$admin = $w3admin;
$result = $admin->settings;
$tab = !empty($_GET['tab']) ? $_GET['tab'] : '';
list($img_to_opt,$img_remaining) = $admin->getImageOptimizationDetails();
$admin->scheduleImageOptimizationCron($img_remaining);
list($preload_total, $preload_created) = $admin->w3CriticalCssDetails();
$networkAdmin = $admin->addSettings['is_multisite_networkadmin'] ? 1 : 0;
$hidden_class = !empty($result['manage_site_separately']) && $admin->addSettings['is_multisite_networkadmin'] ? 'tr-hidden' : '';
?>
<script>
var adminUrl = "<?php echo $admin->getAjaxUrl(); ?>";
var secureKey = "<?php echo $admin->createSecureKey("hook_callback"); ?>";
</script>
<main class="admin-speedster">
	<div class="top_panel_container">
		<div class="top_panel d-none">
			<div class="logo_container">
				<img class="logo" src="<?php echo W3SPEEDSTER_URL; ?>assets/images/w3-logo.png">
			</div>

			<div class="support_section">
				<div class="right_section">
					<div class="doc w3d-flex gap10">
						<p class="m-0"><i class="fa fa-file-text" aria-hidden="true"></i></p>
						<p class="m-0 text-center w3text-white">
							<?php $admin->translate('Need help or have question'); ?><br><a
								href="https://w3speedster.com/w3speedster-documentation/"
								target="_blank"><?php $admin->translate('Check our documentation'); ?></a>
						</p>

					</div>

				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="tab-panel col-md-2">
			<div class="mobile_toggle d-none">
				<button type="button">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512">
						<path fill="#fff"
							d="M246.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-128-128c-9.2-9.2-22.9-11.9-34.9-6.9S63.9 115 63.9 128v256c0 12.9 7.8 24.6 19.8 29.6s25.7 2.2 34.9-6.9l128-128z" />
					</svg>
				</button>
			</div>
			<div class="logo_container">
				<img class="logo" src="<?php echo W3SPEEDSTER_URL; ?>assets/images/w3-logo.png">
			</div>
			<?php include 'includes/tabs.php'; ?>
			<div class="support_section">
				<a class="doc btn" href="https://w3speedster.com/w3speedster-documentation/"
					target="_blank"><?php $admin->translate('Documentation'); ?> <i
						class="fa fa-long-arrow-right" aria-hidden="true"></i></a>
				<a class="contact btn"
					href="https://w3speedster.com/contact-us/"><?php $admin->translate('Contact Us'); ?> <i
						class="fa fa-long-arrow-right" aria-hidden="true"></i></a>
			</div>
		</div>

		<form method="post" class="main-form">
			<div class="tab-content col-md-10">
				<?php include 'includes/general.php'; ?>
				<?php include 'includes/cdn.php'; ?>
				<?php include 'includes/css.php'; ?>
				<?php include 'includes/js.php'; ?>
				<?php include 'includes/exclusions.php'; ?>
				<?php include 'includes/w3-custom-code.php'; ?>
				<?php include 'includes/cache.php'; ?>
				<?php include 'includes/hooks.php'; ?>
				<?php include 'includes/webvital-logs.php'; ?>
				<?php include 'includes/html-cache.php'; ?>
				<?php include 'includes/opt-img.php'; ?>
				<?php include 'includes/import-export.php'; ?>
			</div>
		</form>



	</div>
</main>
<?php include 'includes/code-editor.php';