/**
 * Set cookie.
 * 
 * @param {string} name Cookie name.
 * @param {string|boolean} value Cookie value. Defaults to boolean true.
 */
export const setCookie = (name: string, value: string|boolean = true): void => {
	const date = new Date();
	date.setTime(date.getTime() + (365 * 24 * 60 * 60 * 1000));
	document.cookie = name + '=' + value + '; expires=' + date.toUTCString() + '; path=/';
}

/**
 * Get cookie by name.
 * 
 * @param {string} name Cookie name.
 * @returns {string} Cookie value.
 */
export const getCookie = (name: string): string => {
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