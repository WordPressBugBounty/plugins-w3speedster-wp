<section id="import" class="tab-pane fade<?php echo $tab == 'import' ? ' active in' : ''; ?>">
	<div class="header w3d-flex gap20">
		<div class="heading_container">
			<h4 class="w3heading">
				<?php $admin->translate('Import / Export'); ?>
			</h4>
			<span class="info"><a
					href="https://w3speedster.com/w3speedster-documentation/"><?php $admin->translate('More info'); ?>?
				</a></span>
		</div>
		<div class="icon_container"> <img
				src="<?php echo W3SPEEDSTER_URL; ?>assets/images/import-export-icon.webp"></div>
	</div>
	<hr>
	<form id="import_form" method="post">
		<div class="import_form">
			<label><?php $admin->translate('Import Settings'); ?><span class="info"></span><span
					class="info-display"><?php $admin->translate('Enter exported json code from W3speedster plugin import/export page'); ?></span></label>
			<textarea id="import_text" name="import_text" rows="10" cols="16"
				placeholder="<?php $admin->translate('Enter json code'); ?>"></textarea>
			<input type="hidden" name="_wpnonce"
				value="<?php echo $admin->createSecureKey('w3_settings'); ?>">
			<button id="import_button" class="btn"
				type="button"><?php $admin->translate('Import'); ?></button>
		</div>
	</form>
	<?php
	$export_setting = $result;
	$export_setting['license_key'] = '';
	$export_setting['is_activated'] = '';
	?>

	<hr>
	<div class="import_form">
		<label><?php $admin->translate('Export Settings'); ?><span class="info"></span><span
				class="info-display"><?php $admin->translate('Copy the code and save it in a file for future use'); ?></span></label>
		<textarea rows="10" cols="16"><?php if (!empty($export_setting))
			echo $admin->w3JsonEncode($export_setting); ?></textarea>
	</div>
</section>
