<script>
	var custom_css_cd = 0;
	var custom_js_cd = 0;
	jQuery(document).ready(function () {
		jQuery('.w3_custom_code').click(function () {
			console.log("custom code click");
			if (!custom_css_cd) {
				custom_css_cd = 1;
				setTimeout(function () { wp.codeEditor.initialize(jQuery('[name="custom_css"]'), cm_settings.codeCss); }, 300);
			}
			if (!custom_js_cd) {
				custom_js_cd = 1;
				setTimeout(function () {
					wp.codeEditor.initialize(jQuery('[name="custom_javascript"]'), cm_settings.codeJs);
					wp.codeEditor.initialize(jQuery('[name="custom_js"]'), cm_settings.codeJs);
				}, 300);
			}
		});
		var $textareas = jQuery('.hook_before_start');
		var customEditorSettings = wp.codeEditor.defaultSettings ? _.clone(wp.codeEditor.defaultSettings) : {};

		customEditorSettings.codemirror.mode = "text/x-php";
		customEditorSettings.codemirror.lineNumbers = false;
		customEditorSettings.codemirror.autoRefresh = true;

		$textareas.each(function () {
			var textareaId = jQuery(this).attr('id');
			var editor = wp.codeEditor.initialize(textareaId, customEditorSettings);

		});
	});
</script>