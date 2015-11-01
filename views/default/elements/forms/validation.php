<?php
if (!elgg_extract('data-parsley-validate', $vars)) {
	return;
}
?>
<script>
	require(['elements/forms/validation'], function() {
		$('[data-parsley-validate]').parsley();
	});
</script>