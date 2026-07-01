<?php
if (!elgg_extract('data-parsley-validate', $vars)) {
	return;
}

elgg_import_esm('elements/forms/validation');
