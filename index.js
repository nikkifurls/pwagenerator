window.addEventListener("load", () => {

	document.querySelectorAll("input").forEach(field => {
		field.addEventListener("input", event => {
			event.preventDefault();

			const project = event.target.closest("fieldset").name;
			const fieldName = field.name;
			const fieldValue = (event.target.type == "checkbox") ? event.target.checked : event.target.value;

			updateProject(project, fieldName, fieldValue);
		});
	});

	document.querySelectorAll("textarea, select").forEach(field => {
		field.addEventListener("change", event => {
			event.preventDefault();

			const project = event.target.closest("fieldset").name;
			const fieldName = field.name;
			const fieldValue = event.target.value;

			updateProject(project, fieldName, fieldValue);
		});
	});

	document.querySelectorAll("button").forEach(button => {
		button.addEventListener("click", event => {
			event.preventDefault();

			const project = event.target.closest("fieldset") ? event.target.closest("fieldset").name : null;
			const buttonType = button.className;

			if (project) {
				generateProject(project, (buttonType == "deploy"));
			} else {
				generateProjects((buttonType == "deploy"));
			}
		});
	});
});

const updateProject = (project, field, value) => {

	let data = new FormData();
	data.append("project", project);
	data.append("field", field);
	data.append("value", value);

	fetch("/api/update_project.php", {
			method: "POST",
			body: data
		})
		.then(response => response.json())
		.then(result => {
			if (result) {
				showNotification(`Updated ${project} ${field} to "${value}"`);
			} else {
				showNotification(`ERROR: Updating ${project} ${field} to "${value}" failed`);
			}
		})
		.catch(error => console.warn(error));
}

const generateProject = (project, deploy = false) => {

	showNotification(deploy ? `Deploying ${project}...` : `Building ${project}...`);

	let data = new FormData();
	data.append("project", project);

	if (deploy) {
		data.append("deploy", true);
	}

	fetch("/api/generate_project.php", {
			method: "POST",
			body: data
		})
		.then(response => response.json())
		.then(result => {
			if (result) {
				showNotification(deploy ? `Deploying ${project}... DONE!` : `Building ${project}... DONE!`);
			} else {
				showNotification(deploy ? `Deploying ${project}... ERROR!` : `Building ${project}... ERROR!`);
			}
		})
		.catch(error => console.warn(error))
}

const generateProjects = (deploy = false) => {

	let notificationText = deploy ? `Deploying all projects...` : `Building all projects...`;
	showNotification(notificationText);

	const numProjects = document.querySelectorAll("fieldset").length;
	let projectsTotal = 0;

	document.querySelectorAll("fieldset").forEach((fieldset, index) => {

		const project = fieldset.name;

		notificationText += deploy ? `<br/>Deploying ${project}...` : `<br/>Building ${project}...`;
		showNotification(notificationText);

		let data = new FormData();
		data.append("project", project);

		if (deploy) {
			data.append("deploy", true);
		}

		fetch("/api/generate_project.php", {
				method: "POST",
				body: data
			})
			.then(response => response.json())
			.then(result => {
				if (result) {
					notificationText = deploy ?
						notificationText.replace(`Deploying ${project}...`, `Deploying ${project}... DONE! <span role="img" aria-label="check">✔</span>`) :
						notificationText.replace(`Building ${project}...`, `Building ${project}... DONE! <span role="img" aria-label="check">✔</span>`);
					
					projectsTotal++;

					if (projectsTotal != numProjects) {
						showNotification(notificationText);
					} else {
						notificationText = deploy ?
							notificationText.replace(`Deploying all projects...`, `Deploying all projects... DONE! <span role="img" aria-label="check">✅</span>`) :
							notificationText.replace(`Building all projects...`, `Building all projects... DONE! <span role="img" aria-label="check">✅</span>`);
						showNotification(notificationText);
					}
				} else {
					notificationText = deploy ?
						notificationText.replace(`Deploying ${project}...`, `Deploying ${project}... ERROR! <span role="img" aria-label="check">✖</span>`) :
						notificationText.replace(`Building ${project}...`, `Building ${project}... ERROR! <span role="img" aria-label="check">✖</span>`);
				}
			})
			.catch(error => console.warn(error))
	});
}