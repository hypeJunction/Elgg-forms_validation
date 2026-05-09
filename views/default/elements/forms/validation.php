<?php
if (!elgg_extract('data-parsley-validate', $vars)) {
	return;
}
?>
<script type="module">
	import 'elements/forms/validation';
	$('[data-parsley-validate]').parsley();
</script>
