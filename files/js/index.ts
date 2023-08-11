import { showNotification } from "./notifications";
import { setLinkEventListeners } from "./urls";

import '../scss/index.scss';
import '../scss/style.scss';

window.addEventListener('load', () => {

	// Display cookie notification.
	showNotification('cookie', 'notification-cookie');

	// If URL parameters are passed in, check for notification.
	if (window.location.search) {
		const param = window.location.search.substring(1);
		if (param) {
			const paramParts = param.split('=');

			// If ?notification=text is set in URL, open notification.
			if (
					(typeof paramParts !== 'undefined') && 
					(paramParts !== null) && 
					(typeof paramParts[0] !== 'undefined') && 
					(paramParts[0] !== null) &&
					(paramParts[0] === 'notification') &&
					(typeof paramParts[1] !== 'undefined') && 
					(paramParts[1] !== null)
				) {
					// @todo: add functionality to show cookie notification after notification passed in via URL.
					showNotification(paramParts[1]);
			}
		}
	}

	// Set event listeners for all event links.
	setLinkEventListeners();
});