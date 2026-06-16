<?php

namespace hypeJunction\FormsValidation;

use Elgg\DefaultPluginBootstrap;

class Bootstrap extends DefaultPluginBootstrap {

	public function init(): void {
		// Make the vendored Parsley library resolvable from the ES module importmap,
		// so elements/forms/validation.mjs can `import 'parsley.js'`.
		\elgg_register_esm('parsley.js', \elgg_get_simplecache_url('parsley.js'));
	}
}
