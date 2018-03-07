var setUrl = function( ev ) {
	var el = ev.target;
	// Find the date picker.
	var datepicker = el.parentNode.parentNode.querySelectorAll( '.js-datepicker' );
	// Get the date to summerise.
	var summaryWeek = datepicker[0].value;

	if ( summaryWeek.length > 0 ) {
		// Get the URL.
		var url = el.getAttribute( 'data-url' );
		// Replace and continue.
		el.setAttribute( 'href', url + '&date=' + summaryWeek );
	}
};
// Set watchers for every URL action.
[].forEach.call( document.querySelectorAll( '.js-url-action' ), function( el ) {
	el.addEventListener( 'click', setUrl.bind() );
} );

var doDatepicker = function( ev ) {
	var el = ev.target;
	// Hide the stylised date.
	el.style.display = 'none';
	// Show the nearest date picker. We know it's the next element.
	el.nextElementSibling.style.display = 'inline';
};
// Set watchers for every date picker span.
[].forEach.call( document.querySelectorAll( 'span.js-trigger-datepicker' ), function( el ) {
	el.addEventListener( 'click', doDatepicker.bind() );
} );

var closeDatepicker = function( ev ) {
	var el = ev.target;

	if ( el.className.indexOf( 'js-trigger-datepicker' ) > -1 || el.className.indexOf( 'js-datepicker' ) > -1 ) {
		return;
	}

	// Close all the open date pickers.
	[].forEach.call( document.querySelectorAll( 'input.js-datepicker' ), function( el ) {
		if ( el.offsetParent === null ) {
			return;
		}

		if ( 0 !== el.value.length ){
			el.previousElementSibling.innerHTML = el.value;
		}

		el.style.display = 'none';
		el.previousElementSibling.style.display = 'inline';

	} );
};
// Set watchers for the body element for clearing the datepicker.
document.getElementsByTagName( 'body' )[0].addEventListener( 'click', closeDatepicker.bind() );
