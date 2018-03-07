var setUrl = function( ev ) {
	var el = ev.target;
	// Get the date to summerise.
	var summaryWeek = document.querySelector( '.js-summary-date' ).value;
	// Get the URL.
	var url = el.getAttribute( 'data-url' );
	// Replace and continue.
	el.setAttribute( 'href', url + '&date=' + summaryWeek );
};

// Set watchers for every URL action.
[].forEach.call( document.querySelectorAll( '.js-url-action' ), function(el) {
	el.addEventListener( 'click', setUrl.bind() );
} );
