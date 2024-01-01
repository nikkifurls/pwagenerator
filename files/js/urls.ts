import { showNotification } from "./notifications";

declare global {
	interface Window {
		baseUrl: string;
		baseTitle: string;
		baseDescription: string;
		gtag: any;
	}
}

/**
 * Copies provided URL to clipboard if navigator.clipboard is available, otherwise displays a notification with the URL.
 * @param {Object} url The `copyUrl` function requires an object with the following properties:
 * @param {string=} url.url Url to copy.
 */
export const copyUrl = ({ url = window.location.href } : { url?: string }): void => {
	if (!url) {
		return;
	}

	if (navigator.clipboard) {
		navigator.clipboard.writeText(url)
			.then(() => {
				showNotification({ text: `<span><strong>Success! <span role='img' title='Party' class='icon icon-partyface'>🥳</span></strong> URL copied to clipboard: <span class='url'>${url}</span>` });
			})
			.catch(() => {
				showNotification({ text: `<span><strong>Copy URL:</strong></span> <span class='url'>${url}</span>` });
			});

	} else {
		showNotification({ text: `<span><strong>Copy URL:</strong></span> <span class='url'>${url}</span>` });
	}

	// Send copy event to GA.
	window.gtag('event', 'select_content', {
		item_id: url,
	});
}

/**
 * Displays the native sharing mechanism for the device if navigator.share is available, otherwise displays a notification with the share data.
 * @param {Object} url The `shareUrl` function requires an object with the following properties:
 * @param {string=} url.url URL to share. Defaults to window.location.href.
 * @param {string=} url.title Title of website. Defaults to window.baseTitle. 
 * @param {string=} url.text Text to share. Defaults to window.baseDescription.
 */
export const shareUrl = ({ url = window.location.href, title = window.baseTitle, text = window.baseDescription } : { url?: string, title?: string, text?: string }): void => {
	if (navigator.share) {
		navigator.share({ title, url }).catch(error => console.warn(error));
	} else {
		const textEncoded = encodeURIComponent(text + ' ' + url);
		showNotification({
      text: 			`<a class='button facebook' href='https://www.facebook.com/sharer/sharer.php?u=${url}' title='Share on Facebook'><i class='fas fa-share-alt'></i>&nbsp;&nbsp;Facebook</a>` +
			`<a class='button twitter' href='https://twitter.com/intent/tweet?text=${textEncoded}' title='Share on Twitter'><i class='fas fa-share-alt'></i>&nbsp;&nbsp;Twitter</a>` +
			`<a class='button email' href='mailto:?subject=Check+out+${title}!&body=${textEncoded}' title='Share on Email'><i class='fas fa-share-alt'></i>&nbsp;&nbsp;Email</a>` +
			`<a class='button copy' href='#' title='Copy' data-url='${url}'><i class='fas fa-copy'></i>&nbsp;&nbsp;Copy</button>`,
      cookieName: '',
      className: 'notification-share'
    });
	}

	// Send share event to GA.
	window.gtag('event', 'share', {
		content_id: url,
	});
}

/**
 * Adds event listeners to share and copy links.
 */
export const setLinkEventListeners = (): void => {

	const shareLinks = document.querySelectorAll('.share');

	if (shareLinks) {
		shareLinks.forEach(link => {
			link.addEventListener('click', event => {
				event.preventDefault();
	
				const targetElement = <HTMLElement>event.target;
	
				if (!targetElement) {
					return;
				}
	
				let element = <HTMLElement>targetElement.closest('.share');
	
				if (!element) {
					return;
				}
	
				shareUrl({
					url: element.dataset.url ?? window.baseUrl,
					title: element.dataset.title ?? window.baseTitle,
					text: element.dataset.text ?? window.baseDescription
        });
			});
		});
	}

	const copyLinks = document.querySelectorAll('.copy');

	if (copyLinks) {
		copyLinks.forEach(link => {
			link.addEventListener('click', event => {
				event.preventDefault();
	
				const targetElement = <HTMLElement>event.target;
	
				if (!targetElement) {
					return;
				}
	
				let element = <HTMLElement>targetElement.closest('.copy');
	
				if (!element) {
					return;
				}
	
				copyUrl({ url: element.dataset.url ?? window.baseUrl });
			});
		});
	}
}
