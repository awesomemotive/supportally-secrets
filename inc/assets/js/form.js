window.addEventListener(
	"load", function () {

		const alert = document.getElementById("alert");
		const alertDelete = document.getElementById("alert-delete");
		const alertUrl = document.getElementById("alert-url");
		const alertText = document.getElementById("alert-text");
		const displayUrl = document.getElementById("display-url");

		const deleteForm = document.getElementById("delete-form");
		const viewForm = document.getElementById("view-form");

		const deleteLink = document.querySelector(".view #secret-delete");

		if (deleteLink !== null) {
			deleteLink.value = window.location;
		}
		const form = document.querySelector("#share-secret");
		if (form !== null ) {
			form.addEventListener(
				"submit", function (event) {
					event.preventDefault();
					let fields = document.querySelectorAll(".share-secret .field") 
					let valid = true
					for (var i = 0; i < fields.length; i++) {
						fields[i].classList.remove("no-error") 
						if(fields[i].value === "") { 
							fields[i].classList.add("has-error")
							fields[i].nextElementSibling.style.display = "block"
							valid = false
						}
						else{
							fields[i].classList.remove("has-error")
							fields[i].classList.add("no-error")
						}
					}
					if(valid===true) {
						fetch(window.location.href, {
							method: "POST",
							body: new FormData(form), // Send the form data
						})    
						.then((response) => response.text())
						.then(
							(response) => {
								console.log(response);
								const responseText = JSON.parse(response);
								if(responseText.error) {    
									alertText.innerText = responseText.error
									alert.classList.add("error");
									alert.style.display = "block";
									displayUrl.style.display = "none";
									return
								}
								alertText.innerText = '';
								form.style.display = "none"
								displayUrl.style.display = "block";
								alert.classList.remove("error");
								alert.style.display = "block";
								alert.classList.add("success");
								displayUrl.value = responseText.secret_url;
								displayUrl.focus();
								displayUrl.select();
								deleteForm.style.display = "block";
								document.querySelector("#secret-delete").value = responseText.secret_url;
							}
						)
					}
				}
			);
		}

		if (deleteForm !== null ) {
			deleteForm.addEventListener(
				"submit", function (event) {
					event.preventDefault() 
					fetch(
						window.location.href, {
							method: "POST",
							body: new FormData(deleteForm), // Send the form data
						}
					)
					.then((response) => response.text())
					.then(
						(response) => {
							const deleteText = JSON.parse(response) // Get the response
							if(deleteText.error ) { // If there is an error    
								alertDelete.innerText = deleteText.error
								alertDelete.classList.add("error")
								alertDelete.style.display = "block";
								return
							}
							displayUrl.style.display = "none";
							if ( viewForm !== null ) {
								viewForm.style.display = "none";
							}
							deleteForm.style.display = "none";
							alertDelete.style.display = "block";
							alertDelete.innerText = deleteText.success;
							alertDelete.classList.add("success");
						}
					)
				}
			);
		}

	if (viewForm !== null ) {
		viewForm.addEventListener(
			"submit", function (event) {
				event.preventDefault();
				document.querySelector("#view-button").style.display = "none";
				if ( deleteForm !== null ) {
					deleteForm.style.display = "block";
				}
				alertUrl.style.display = "block";
				alertUrl.focus();
				alertUrl.select();
		});
	}
});