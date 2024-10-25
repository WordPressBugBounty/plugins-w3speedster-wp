<ul class="nav nav-tabs w3speedsternav">
	<?php if (empty($result['manage_site_separately'])) { ?>
		<li class="w3_html_cache<?php echo $tab == 'htmlCache' ? ' active' : ''; ?>"><a data-toggle="tab"
				data-section="htmlCache" href="avascript:void(0)">
				<?php $admin->translate('HTML Cache'); ?>
			</a></li>
	<?php } ?>
	<li class="w3_general<?php echo empty($tab) || $tab == 'general' ? ' active' : ''; ?>"><a
			data-toggle="tab" data-section="general" href="javascript:void(0)">
			<?php $admin->translate('General'); ?>
		</a></li>
	<?php if (empty($result['manage_site_separately'])) { ?>
		<li class="w3_cdn<?php echo $tab == 'w3-cdn' ? ' active' : ''; ?>"><a data-toggle="tab"
				data-section="w3-cdn" href="javascript:void(0)">
				<?php $admin->translate('CDN'); ?>
			</a></li>
		<li class="w3_opt_img<?php echo $tab == 'opt_img' ? ' active' : ''; ?>"><a data-toggle="tab"
				data-section="opt_img" href="javascript:void(0)">
				<?php $admin->translate('Image Optimization'); ?>
			</a></li>
		<li class="w3_css<?php echo $tab == 'css' ? ' active' : ''; ?>"><a data-toggle="tab" data-section="css"
				href="javascript:void(0)">
				<?php $admin->translate('Css'); ?>
			</a></li>
		<li class="w3_js<?php echo $tab == 'js' ? ' active' : ''; ?>"><a data-toggle="tab" data-section="js"
				href="javascript:void(0)">
				<?php $admin->translate('Javascript'); ?>
			</a></li>
		<li class="w3_exclusions<?php echo $tab == 'exclusions' ? ' active' : ''; ?>"><a data-toggle="tab"
				data-section="exclusions" href="javascript:void(0)">
				<?php $admin->translate('Exclusions'); ?>
			</a></li>
		<li class="w3_custom_code<?php echo $tab == 'w3_custom_code' ? ' active' : ''; ?>"><a data-toggle="tab"
				data-section="w3_custom_code" href="javascript:void(0)">
				<?php $admin->translate('Custom Code'); ?>
			</a></li>
		<li class="w3_cache<?php echo $tab == 'cache' ? ' active' : ''; ?>"><a data-toggle="tab"
				data-section="cache" href="javascript:void(0)">
				<?php $admin->translate('Clear Cache'); ?>
			</a></li>
		<li class="w3_webvitals_log<?php echo $tab == 'webvitalslogs' ? ' active' : ''; ?>"><a data-toggle="tab"
				data-section="webvitalslogs" href="javascript:void(0)">
				<?php $admin->translate('Web Vitals Logs'); ?>
			</a></li>
		<li class="w3_import<?php echo $tab == 'import' ? ' active' : ''; ?>"><a data-toggle="tab"
				data-section="import" href="javascript:void(0)">
				<?php $admin->translate('Import/Export'); ?>
			</a></li>
	<?php } ?>
</ul>
			