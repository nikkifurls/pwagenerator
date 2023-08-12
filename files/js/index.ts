import { showNotification } from "./notifications";
import { setLinkEventListeners } from "./urls";

import '../scss/index.scss';
import '../scss/style.scss';

window.addEventListener('load', () => {

	// Display cookie notification.
	showNotification('cookie', 'notification-cookie');

	// If URL parameters are passed in, check for notification.
	if (window.location.search) {
		const query = window.location.search.replace('?', '').split('=');
		if (query.length > 1) {
			const [ param, value ] = query;

			if ('notification' === param && value) {
				showNotification(value);
			}
		}
	}

	// Set event listeners for all event links.
	setLinkEventListeners();
});