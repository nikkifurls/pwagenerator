/**
 * Normalize a text string.
 * 
 * @param {string} text Text string to normalize.
 * @param {string} type Type of normalization to perform. Defaults to 'text'. Other possible value is 'url'.
 * @returns {string} Normalized text string.
 */
const normalizeText = (text: string, type: string = 'text'): string => {
	
	// Remove leading and trailing slashes.
	let decodedText = text.replace(/^\/|\/$/g, '');

	// Transform accented characters to their non-accented version.
	decodedText = decodedText.normalize('NFD').replace(/[\u0300-\u036f]/g, '');

	// Remove .html extension.
	decodedText = decodedText.replace(/.html/g, ' ');

	// Remove +.
	decodedText = decodedText.replace(/\+/g, ' ');

	// Remove %20.
	decodedText = decodedText.replace(/%20/g, ' ');

	// Change dashes to spaces, unless type=url.
	if (type === 'url') {
		decodedText = decodedText.replace(/ /g, '-');
	} else {
		decodedText = decodedText.replace(/-/g, ' ');
	}

	// Transform to lowercase.
	decodedText = decodedText.toLowerCase();

	// Trim whitespace.
	decodedText = decodedText.trim();

	return decodedText;
}

export default normalizeText;