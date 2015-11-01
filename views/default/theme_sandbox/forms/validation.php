<?php

$body = elgg_view('theme_sandbox/forms/validation/form');
$body .= elgg_format_element('div', ['class' => 'elgg-foot'], elgg_view('input/submit'));

$form = elgg_view('input/form', array(
	'action' => '#',
	'method' => 'GET',
	'body' => $body,
	'validate' => true,
));

echo elgg_view_module('aside', 'Form Validation', $form);