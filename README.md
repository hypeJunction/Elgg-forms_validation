Form validation component for Elgg
==================================
![Elgg 2.0](https://img.shields.io/badge/Elgg-2.0.x-orange.svg?style=flat-square)

## Screenshots ##
![Invalid input](https://raw.github.com/hypeJunction/Elgg-forms_validation/master/screenshots/validation.png "Invalid input")

## Features

* Client-side validation with Parsley.js
* Customizable error messages
* Easy to integrate into existing forms
* Extendable with custom validation rules

## Usage

* Validation rules and other options are described in Parsley.js documentation http://parsleyjs.org/
* To enable client-side form validation, pass 'validate' parameter with form vars:

```php

echo elgg_view_form('my/form', array(
    'validate' => true,
), array());

// or

echo elgg_view('input/form', array(
    'action' => 'action/my/action',
    'validate' => true,
));
```

