<?php

namespace hypeJunction\FormsValidation;

use Elgg\DefaultPluginBootstrap;

class Bootstrap extends DefaultPluginBootstrap {

	public function init(): void {
		// Make the vendored Parsley library resolvable from the ES module importmap,
		// so elements/forms/validation.mjs can `import 'parsley.js'`. Point straight
		// at the web-accessible vendored file: a 'views' simplecache entry named
		// `parsley.js` (.js, not .mjs) is NOT added to the Elgg 7 importmap, so the
		// previous elgg_get_simplecache_url('parsley.js') route never resolved.
		\elgg_register_esm('parsley.js', \elgg_normalize_url('mod/forms_validation/vendors/parsleyjs/parsley.min.js'));
	}
}
