import { setCookie, getCookie } from "./cookies";
import { setLinkEventListeners } from "./urls";

/**
 * Show notification.
 * 
 * @param {Object} notification The `showNotification` function requires an object with the following properties:
 * @param {string} notification.text Text to display in notification. Also accepts 'paypal-confirmation' to display PayPal confirmation text, or 'cookie' to display cookie consent text.
 * @param {string=} notification.cookieName Cookie name to set once notification is closed so that the notification does not display again for the user. Defaults to ''.
 * @param {string=} notification.className Class name to apply to notification element. Defaults to ''.
 */
export const showNotification = ({ text, cookieName = '', className = '' } : { text: string, cookieName?: string, className?: string }): void => {

	// Don't display notification if cookie is set.
	if (cookieName && getCookie({name: cookieName})) {
		return;
	}

	new Promise((resolve, reject) => {

		const notificationContainer = <HTMLDialogElement>document.querySelector('.notification');
		const notificationTextContainer = <HTMLElement>document.querySelector('.notification p');
		const notificationCloseContainer = <HTMLElement>document.querySelector('.notification .close');

		if (!notificationContainer) {
			reject('.notification element not found');
		}

		// Close any open notifications.
		closeNotification();

		// Set notification text.
		if (notificationTextContainer) {
			if (text === 'paypal-confirmation') {
				notificationTextContainer.innerHTML = `Transaction approved! Thank you so much! <span role='img' title='Heart' class='icon icon-heart'>♥</span>`;
			} else if (text === 'cookie') {
				notificationTextContainer.innerHTML = `Cookies and other tracking technologies are used on this website to improve your browsing experience, analyze website traffic, and show personalized content and targeted ads. By browsing this website, you consent to the use of cookies and other tracking technologies.`;
			} else {
				notificationTextContainer.innerHTML = text;
			}
		}

		// Set notification class.
		if (className) {
			notificationContainer.setAttribute('data-class', className);
			notificationContainer.classList.add(className);
		}

		// Set cookie data attribute, which will cause the cookie to get set when the notification is closed, so that it doesn't display again for the user.
		if (cookieName) {
			notificationContainer.setAttribute('data-cookie', cookieName);
		}

		// Set close functionality.
		if (notificationCloseContainer) {
			notificationCloseContainer.addEventListener('click', () => {
				closeNotification();
			});
		}

		// Display notification.
		notificationContainer.show();
		notificationContainer.setAttribute('aria-hidden', 'false');

		// Resolve promise.
		resolve(true);
	})
	.then((result) => {
		if (result) {
			// Set event listeners for any event links in notification.
			setLinkEventListeners();
		}
	})
	.catch((error) => {
		console.error(error);
	});
}

/**
 * Close notification.
 */
export const closeNotification = (): void => {

	const notificationContainer = <HTMLDialogElement>document.querySelector('.notification');

	if (!notificationContainer) {
		return;
	}

	notificationContainer.close();
	notificationContainer.setAttribute('aria-hidden', 'true');
	
	// If notification has class data attribute set, remove class after closing.
	if (notificationContainer.dataset.class) {
		notificationContainer.classList.remove(notificationContainer.dataset.class);
		notificationContainer.removeAttribute('data-class');
	}
	
	// If notification has cookie data attribute set, set cookie after closing.
	if (notificationContainer.dataset.cookie) {
		setCookie({ name: notificationContainer.dataset.cookie, value: true });
		notificationContainer.removeAttribute('data-cookie');
	}
}

/**
 * Show promo notification.
 * @param {Object} notification The `showPromo` function requires an object with the following properties:
 * @param {string} notification.text Text to display in notification.
 * @param {string=} notification.cookieName Cookie name to provide to showNotification(), so that the promo does not display again for the user. Defaults to the website URL with '-promo' appended.
 * @param {string=} notification.customIconSelector Selector for the promo icon, if one exists. If provided, the icon will be animated and a cookie will be set on click so that it only animates once.
 */
export const showPromo = ({ text, cookieName = `${window.baseUrl}-promo`, customIconSelector = 'nav .custom' }: { text: string, cookieName?: string, customIconSelector?: string }): void => {

	if (!text) {
		return;
	}

	// Animate custom icon and set click event listener so that it only animates once.
	if (customIconSelector) {
		setTimeout(() => {
			const element = document.querySelector(customIconSelector);

			if (!element) {
				return;
			}

			element.classList.add('animate');
			element.addEventListener('click', () => {
				setCookie({ name: `${cookieName}-icon`, value: true });
			});
		}, 20000);
	}

	// Show the notification for the promo.
	setTimeout(() => {
		showNotification({ text, cookieName });
	}, 60000);
}
