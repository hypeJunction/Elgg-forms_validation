<?php

if (!is_callable('elgg_view_input')) {
	return;
}

echo elgg_view_input('plaintext', array(
	'required' => true,
	'minlength' => 50,
	'maxlength' => 100,
	'rows' => 2,
	'label' => 'About me',
	'help' => 'Write a paragraph between 50 and 100 characters',
));

$states = array(
	'Alabama' => 'AL',
	'Alaska' => 'AK',
	'Arizona' => 'AZ',
	'Arkansas' => 'AR',
	'California' => 'CA',
	'Colorado' => 'CO',
	'Connecticut' => 'CT',
	'Delaware' => 'DE',
	'Florida' => 'FL',
	'Georgia' => 'GA',
	'Hawaii' => 'HI',
	'Idaho' => 'ID',
	'Illinois' => 'IL',
	'Indiana' => 'IN',
	'Iowa' => 'IA',
	'Kansas' => 'KS',
	'Kentucky' => 'KY',
	'Louisiana' => 'LA',
	'Maine' => 'ME',
	'Maryland' => 'MD',
	'Massachusetts' => 'MA',
	'Michigan' => 'MI',
	'Minnesota' => 'MN',
	'Mississippi' => 'MS',
	'Missouri' => 'MO',
	'Montana' => 'MT',
	'Nebraska' => 'NE',
	'Nevada' => 'NV',
	'New Hampshire' => 'NH',
	'New Jersey' => 'NJ',
	'New Mexico' => 'NM',
	'New York' => 'NY',
	'North Carolina' => 'NC',
	'North Dakota' => 'ND',
	'Ohio' => 'OH',
	'Oklahoma' => 'OK',
	'Oregon' => 'OR',
	'Pennsylvania' => 'PA',
	'Rhode Island' => 'RI',
	'South Carolina' => 'SC',
	'South Dakota' => 'SD',
	'Tennessee' => 'TN',
	'Texas' => 'TX',
	'Utah' => 'UT',
	'Vermont' => 'VT',
	'Virginia' => 'VA',
	'Washington' => 'WA',
	'West Virginia' => 'WV',
	'Wisconsin' => 'WI',
	'Wyoming' => 'WY'
);

echo elgg_view_input('checkboxes', array(
	'required' => true,
	'data-parsley-mincheck' => '5',
	'name' => 'state',
	'options' => $states,
	'label' => 'States',
	'help' => 'Select minimum 5 states',
	'align' => 'horizontal',
));
