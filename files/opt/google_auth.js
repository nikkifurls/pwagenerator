const apiClientID = document.currentScript.getAttribute("api_client_id");
const apiKey = document.currentScript.getAttribute("api_key");
const apiScope = document.currentScript.getAttribute("api_scope");
const apiUrl = document.currentScript.getAttribute("api_url");
const apiCallback = document.currentScript.getAttribute("api_callback");

let GoogleAuth;
let GoogleClient;

window.addEventListener("load", () => {

});

const handleClientLoad = callback => {
	return gapi.load("client:auth2", () => {
		gapi.auth2.init({
			client_id: apiClientID
		}).then(() => {
			GoogleAuth = gapi.auth2.getAuthInstance();
			GoogleAuth.isSignedIn.listen(updateLoginButtons);
			GoogleAuth.isSignedIn.listen(callback);
			gapi.client.setApiKey(apiKey);
			
			GoogleClient = gapi.client.load(apiUrl)
				.then(
					() => {
						callback();
					},
					error => {
						console.error("handleClientLoad()", error);
					}
				);
		});
	});
}

const login = () => {
	const isLoggedIn = getLoginStatus();

	if (isLoggedIn === false) {
		return gapi.auth2.getAuthInstance()
			.signIn({
				scope: apiScope
			})
			.then(() => {
					// TO DO - show different notifications depending on user
					showNotification("Log-in successful!");
				},
				error => {
					console.error("login()", error);
				}
			);
	} else {
		console.error("login()", "Already logged in");
	}
}

const logout = () => {
	const isLoggedIn = getLoginStatus();

	if (isLoggedIn === true) {
		if (GoogleAuth !== undefined) {
			GoogleAuth.signOut()
				.then(() => {
					showNotification("Log-out successful!");
				});
		} else {
			console.error("logout()", "Cannot log out, GoogleAuth is null");
		}
	} else {
		console.error("logout()", "Already logged out");
	}
}

const getLoginStatus = () => {
	let isLoggedIn = null;

	if (GoogleAuth !== undefined) {
		isLoggedIn = GoogleAuth.isSignedIn.get();
	}

	return isLoggedIn;
}

const updateLoginButtons = (isLoggedIn = null) => {
	const loginButtons = document.querySelector(".login_buttons");

	if (!isLoggedIn) {
		isLoggedIn = getLoginStatus();
	}

	if (isLoggedIn === true) {
		loginButtons.innerHTML = `<button id="logout">Log out</button>`;
	} else {
		loginButtons.innerHTML = `<button id="login">Log in</button>`;
	}

	const loginButton = document.querySelector("button#login");
	const logoutButton = document.querySelector("button#logout");

	if (loginButton) {
		loginButton.addEventListener("click", event => {
			event.preventDefault();
			login();
		});
	}

	if (logoutButton) {
		logoutButton.addEventListener("click", event => {
			event.preventDefault();
			logout();
		});
	}
}