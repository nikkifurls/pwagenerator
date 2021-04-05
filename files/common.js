window.addEventListener("load", event => {

	// Display cookie notification
	showNotification("cookie", null, "notification-cookie");

	// If URL parameters are passed in, check for notification
	if (window.location.search) {
		const param = window.location.search.substring(1);
		if (param) {
			const paramParts = param.split("=");

			// If ?notification=text is set in URL, open notification
			if (
					(typeof paramParts !== "undefined") && 
					(paramParts !== null) && 
					(typeof paramParts[0] !== "undefined") && 
					(paramParts[0] !== null) &&
					(paramParts[0] == "notification") &&
					(typeof paramParts[1] !== "undefined") && 
					(paramParts[1] !== null)
				) {
					// TO DO: add functionality to show cookie notification after notification passed in via URL
					showNotification(paramParts[1]);
			}
		}
	}

	// Set event listeners for all event links
	setLinkEventListeners();
});

// Set notification text
// If className provided, set class and data-class
// If cookieName provided, check whether cookie is set prior to showing notification, don't show if set
// Show notification
// Set event listeners for any event links in notification
const showNotification = (text, className = null, cookieName = null) => {

	const notificationContainer = document.querySelector(".notification");
	const notificationText = document.querySelector(".notification p");
	const notificationClose = document.querySelector(".notification .close");

	new Promise((resolve, reject) => {

		if (notificationContainer) {

			// Display notification if cookieName isn't provided, or cookie isn't set
			if (!cookieName || (cookieName && !getCookie(cookieName))) {

				// Close any open notifications
				if (notificationContainer.style.display != "none") {
					closeNotification();
				}

				// Set text
				if (notificationText) {
					if (text == "paypal-confirmation") {
						text = `Transaction approved! Thank you so much! <span role="img" title="Heart" class="icon icon-heart">♥</span>`;
					} else if (text == "cookie") {
						text = `Cookies and other tracking technologies are used on this website to improve your browsing experience, analyze website traffic, and show personalized content and targeted ads. By browsing this website, you consent to the use of cookies and other tracking technologies.`;
					}
				
					if (text) {
						notificationText.innerHTML = text;
					}
				}
		
				// Set class (for styling)
				notificationContainer.dataset.class = className !== null ? className : "";
		
				if (notificationContainer.dataset.class) {
					notificationContainer.classList = `notification ${className}`;
				} else {
					notificationContainer.classList = `notification`;
				}
		
				// Set cookie (prevents notification from displaying again)
				notificationContainer.dataset.cookie = cookieName !== null ? cookieName : "";
		
				// Set close functionality
				if (notificationClose) {
					notificationClose.addEventListener("click", event => {
						closeNotification();
					});
				}
		
				notificationContainer.style.display = "block";
				resolve(true);
			}

		} else {
			reject(".notification element not found");
		}
	})
	.then(result => {
		if (result) {
			// Set event listeners for any event links in notification
			setLinkEventListeners();
		}
	})
	.catch(error => {
		console.error(error);
	});
}

// Close notification
// If notification has data-class set, remove class after closing
// If notification has data-cookie set, set cookie after closing
const closeNotification = () => {

	const notificationContainer = document.querySelector(".notification");

	if (notificationContainer) {

		const className = (typeof notificationContainer.dataset.class !== "undefined" && notificationContainer.dataset.class !== null) ? notificationContainer.dataset.class : null;
		const cookieName = (typeof notificationContainer.dataset.cookie !== "undefined" && notificationContainer.dataset.cookie !== null) ? notificationContainer.dataset.cookie : null;

		notificationContainer.style.display = "none";
		
		if (className) {
			notificationContainer.classList.remove(className);
		}
		
		if (cookieName) {
			setCookie(cookieName, true);
		}

		notificationContainer.dataset.class = "";
		notificationContainer.dataset.cookie = "";
	}
}

const setCookie = (name, value = "") => {
	let date = new Date();
	date.setTime(date.getTime() + (365 * 24 * 60 * 60 * 1000));
	document.cookie = name + "=" + value + "; expires=" + date.toUTCString() + "; path=/";
}

const getCookie = name => {
	const cookieSet = document.cookie.split(";").filter(cookie => {
		return (cookie.trim().substring(0, name.length) === name);
	});

	return (cookieSet[0] === undefined) ? false : true;
}

const shareUrl = (url = null, title = null, text = null) => {
	if (!url) {
		url = window.location.href;
	}

	if (!title && baseTitle) {
		title = baseTitle;
	}

	if (url && title) {
		if (navigator.share) {
			navigator.share({
				title: title,
				url: url,
			})
			.catch(error => console.warn(error));
		} else {

			if (!text) {
				if (baseDescription) {
					text = baseDescription;
				} else {
					text = `Check out ${title}!`;
				}
			}

			text = encodeURIComponent(text + " " + url);
		
			showNotification(
				`<a class="button facebook" href="https://www.facebook.com/sharer/sharer.php?u=${url}" target="_blank" rel="noopener" title="Share on Facebook"><i class="fas fa-share-alt"></i>&nbsp;&nbsp;Facebook</a>` +
				`<a class="button twitter" href="https://twitter.com/intent/tweet?text=${text}" target="_blank" rel="noopener" title="Share on Twitter"><i class="fas fa-share-alt"></i>&nbsp;&nbsp;Twitter</a>` +
				`<a class="button email" href="mailto:?subject=Check+out+${title}!&body=${text}" target="_blank" rel="noopener" title="Share on Email"><i class="fas fa-share-alt"></i>&nbsp;&nbsp;Email</a>` +
				`<a class="button copy" href="#" target="_blank" rel="noopener" title="Copy" data-url="${url}"><i class="fas fa-copy"></i>&nbsp;&nbsp;Copy</button>`,
				"notification-share"
			);
		}

		// Send share event to GA
		gtag("event", "share", {
			content_id: url,
		});
	}
}

const copyUrl = (url = null) => {
	if (!url) {
		url = window.location.href;
	}

	if (navigator.clipboard) {
		navigator.clipboard.writeText(url)
			.then(() => {
				showNotification(`<span class="bold">Success! <span role="img" title="Party" class="icon icon-partyface">🥳</span></span> URL copied to clipboard: <span class="url">${url}</span>`);
			})
			.catch(() => {
				showNotification(`<span class="bold">Copy URL:</span> <span class="url">${url}</span>`);
			});

	} else {
		showNotification(`<span class="bold">Copy URL:</span> <span class="url">${url}</span>`);
	}

	// Send copy event to GA
	gtag("event", "select_content", {
		item_id: url,
	});
}

const showPromo = (cookieName, notificationText = null, customIcon = null) => {

	if (!cookieName) {
		if (baseTitle) {
			cookieName = `cookie-${baseTitle.toLowerCase()}`;
		} else {
			cookieName = `cookie-default`;
		}
	}

	if (customIcon) {
		setTimeout(() => {
			const element = document.querySelector("nav .custom");
			element.classList.add("animate");
			element.addEventListener("click", event => {
				setCookie(cookieName, true);
			});
		}, 20000);
	}

	if (notificationText) {
		setTimeout(() => {
			showNotification(notificationText, null, cookieName);
		}, 60000);
	}
}

const setLinkEventListeners = () => {

	const shareLinks = document.querySelectorAll(".share");
	const copyLinks = document.querySelectorAll(".copy");

	shareLinks.forEach(link => {
		link.addEventListener("click", event => {
			event.preventDefault();

			let element = event.target.closest(".share");

			const url = element.dataset.url ? element.dataset.url : (baseUrl ? baseUrl : window.location.href);
			const title = element.dataset.title ? element.dataset.title : (baseTitle ? baseTitle : null);
			const text = element.dataset.text ? element.dataset.text : (baseDescription ? baseDescription : (title ? `Check out ${title}` : null));

			shareUrl(url, title, text);
		});
	});

	copyLinks.forEach(link => {
		link.addEventListener("click", event => {
			event.preventDefault();

			let element = event.target.closest(".copy");

			const url = element.dataset.url ? element.dataset.url : (baseUrl ? baseUrl : window.location.href);

			copyUrl(url);
		});
	});
}

const decodeText = (text, type = "text") => {
	
	if (typeof text == "string") {
		// Remove leading and trailing slashes
		text = text.replace(/^\/|\/$/g, "");
	
		// Transform accented characters to their non-accented version
		text = text.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
	
		// Remove .html extension
		text = text.replace(/.html/g, " ");
	
		// Remove +
		text = text.replace(/\+/g, " ");
	
		// Remove %20
		text = text.replace(/%20/g, " ");
	
		// Change dashes to spaces, unless type=url
		if (type == "url") {
			text = text.replace(/ /g, "-");
		} else {
			text = text.replace(/-/g, " ");
		}
	
		// Transform to lowercase
		text = text.toLowerCase();
	
		// Trim whitespace
		text = text.trim();

	} else {
		
		console.error("decodeText()", "text should be string, " + typeof text + " provided")
	}
	
	return text;
}