Form validation component for Elgg
==================================
![Elgg 7.x](https://img.shields.io/badge/Elgg-7.x-orange.svg?style=flat-square)

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

## Compatibility

| Plugin version | Elgg version |
|---|---|
| 7.0.0 | 7.x |
| 6.0.0 | 6.x |
| 5.0.0 | 5.x |
| 4.0.0 | 4.x |
| 3.0.0 | 3.x |
