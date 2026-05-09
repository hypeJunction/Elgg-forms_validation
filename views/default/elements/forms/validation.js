import i18n from 'elgg/i18n';
import $ from 'jquery';
import 'parsley.js';

window.Parsley.addCatalog('_', {
	defaultMessage: 'validation:error:default',
	type: {
		email: 'validation:error:type:email',
		url: 'validation:error:type:url',
		number: 'validation:error:type:number',
		integer: 'validation:error:type:integer',
		digits: 'validation:error:type:digits',
		alphanum: 'validation:error:type:alphanum'
	},
	notblank: 'validation:error:notblank',
	required: 'validation:error:required',
	pattern: 'validation:error:pattern',
	min: 'validation:error:min',
	max: 'validation:error:max',
	range: 'validation:error:range',
	minlength: 'validation:error:minlength',
	maxlength: 'validation:error:maxlength',
	length: 'validation:error:length',
	mincheck: 'validation:error:mincheck',
	maxcheck: 'validation:error:maxcheck',
	check: 'validation:error:check',
	equalto: 'validation:error:equalto'
}, true);

window.Parsley.getErrorMessage = function (constraint) {
	let message;
	if ('type' === constraint.name) {
		// Type constraints are a bit different, we have to match their requirements too to find right error message
		const typeMessages = this.catalog['_'][constraint.name] || {};
		message = typeMessages[constraint.requirements];
	} else {
		message = this.catalog['_'][constraint.name];
	}

	message = message || this.catalog['_'].defaultMessage;
	return message ? i18n.echo(message, constraint.requirementList) : this.catalog.en.defaultMessage;
}.bind(window.Parsley._validatorRegistry);

window.Parsley.on('field:init', function () {
	this.options.errorsMessagesDisabled = true;
});

window.Parsley.on('field:error', function () {
	const errors = window.ParsleyUI.getErrorsMessages(this) || [];
	const uniqueErrors = $.uniqueSort(errors);

	let $row = this.$element.closest('.elgg-field');
	if (!$row.length) {
		$row = this.$element.parent();
	}

	$row.addClass('elgg-field-has-errors').removeClass('elgg-field-no-errors');
	let $errors = $row.find('.elgg-field-feedback');
	if (!$errors.length) {
		$errors = $('<ul class="elgg-field-feedback" />');
		$row.append($errors);
	}

	$errors.html('');
	$.each(uniqueErrors, function (index, value) {
		$errors.append($('<li class="elgg-field-error" />').text(value));
	});
});

window.Parsley.on('field:success', function () {
	let $row = this.$element.closest('.elgg-field');
	if (!$row.length) {
		$row = this.$element.parent();
	}

	$row.removeClass('elgg-field-has-errors').addClass('elgg-field-no-errors');
	$row.find('.elgg-field-feedback').html('');
});
