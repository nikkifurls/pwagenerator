/**
 * Set cookie.
 * @param {Object} cookie setCookie function requires an object with the following properties:
 * @param {string} cookie.name Cookie name.
 * @param {string|boolean=} cookie.value Cookie value. Defaults to boolean true.
 */
export const setCookie = ({ name, value = true } : { name: string, value?: string|boolean }): void => {
	const date = new Date();
	date.setTime(date.getTime() + (365 * 24 * 60 * 60 * 1000));
	document.cookie = name + '=' + value + '; expires=' + date.toUTCString() + '; path=/';
}

/**
 * Get cookie by name.
 * @param {Object} cookie getCookie function requires an object with the following properties:
 * @param {string} cookie.name Cookie name.
 * @returns {string} Cookie value.
 */
export const getCookie = ({ name }: { name: string }): string => {
	const cookieValue = document.cookie.split('; ')
		.map(cookie => {
			// Extract the cookie name and value from the cookie string.
			const [ cookieName, cookieValue ] = cookie.split('=');

			// If the cookie name matches the name provided, return the cookie.
			return cookieName === name ? cookieValue : null;
		})
		.filter(cookie => cookie); // Remove null values.

	return cookieValue[0] ?? '';
}
