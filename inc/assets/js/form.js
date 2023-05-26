window.addEventListener(
	"load", function () {

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
						fetch("/../index.php", {
							method: "POST",
							body: new FormData(form), // Send the form data
						})    
						.then((response) => response.text())
						.then(
							(response) => {
								console.log(response);
								const responseText = JSON.parse(response);
								if(responseText.error) {    
									document.querySelector("#alert-text").innerText = responseText.error
									document.querySelector("#alert").classList.add("error");
									document.querySelector("#alert").style.display = "block";
									document.querySelector("#display-url").style.display = "none";
									return
								}
								document.querySelector("#alert-text").innerText = '';
								document.querySelector(".secret-form").style.display = "none"
								document.querySelector("#display-url").style.display = "block";
								document.querySelector("#alert").classList.remove("error");
								document.querySelector("#alert").style.display = "block";
								document.querySelector("#alert").classList.add("success");
								document.querySelector("#display-url").value = responseText.secret_url;
								document.querySelector("#display-url").focus();
								document.querySelector("#display-url").select();
								document.querySelector("#delete-form").style.display = "block";
								document.querySelector("#secret-delete").value = responseText.secret_url;
							}
						)
					}
				}
			);
		}

	const deleteForm = document.querySelector("#delete-form");
		if (deleteForm !== null ) {
			deleteForm.addEventListener(
				"submit", function (event) {
					event.preventDefault() 
					fetch(
						"/../index.php", {
							method: "POST",
							body: new FormData(deleteForm), // Send the form data
						}
					)
					.then((response) => response.text())
					.then(
						(response) => {
							const deleteText = JSON.parse(response) // Get the response
							if(deleteText.error ) { // If there is an error    
								document.querySelector("#alert-delete").innerText = deleteText.error
								document.querySelector("#alert-delete").classList.add("error")
								return
							}
							document.querySelector("#display-url").style.display = "none";
							if ( document.querySelector("#view-form") !== null ) {
								document.querySelector("#view-form").style.display = "none";
							}
							document.querySelector("#delete-form").style.display = "none";
							document.querySelector("#alert-delete").style.display = "block";
							document.querySelector("#alert-delete").innerText = deleteText.success;
							document.querySelector("#alert-delete").classList.add("success");
						}
					)
				}
			);
		}

	const viewForm = document.querySelector("#view-form");
	if (viewForm !== null ) {
		viewForm.addEventListener(
			"submit", function (event) {
				event.preventDefault();
				document.querySelector("#view-button").style.display = "none";
				document.querySelector("#delete-form").style.display = "block";
				document.querySelector("#alert-url").style.display = "block";
				document.querySelector("#alert-url").focus();
				document.querySelector("#alert-url").select();
		});
	}
});