
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('register-form');

    if(form){
        form.addEventListener('submit', formSubmit);
    }

    // submits user registration form to server (PHP)
    function formSubmit(event){
        event.preventDefault();

        // grabs each input from the form
        const firstName = document.getElementById('first-name').value;
        const username = document.getElementById('username').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm-password').value;

        if(password !== confirmPassword){
            alert('Passwords do not match, Please try again.');
            return;
        }

        // creates an obj of user info
        const userInfo = {
            firstName: firstName,
            username: username,
            email: email,
            password: password,
        };

        // send info to server (PHP)
        fetch('register.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(userInfo) 
        })
        // check for valid status code (200-299)
        .then(response => {
            if(response.ok){
                return response.json();
            }
            throw new Error('Network Response Failed')
        })
        // checks for API success response
        .then(data => {
            if(data.success){
                alert('Registration Successful!');
            } else {
                alert(data.error);
            }
        })
        .catch(error => {
            alert('There was an issue with registration, please try again.');
            console.error('Error:', error);
        })
    }


});