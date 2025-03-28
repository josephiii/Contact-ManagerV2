document.addEventListener('DOMContentLoaded', function(){
    const form = document.getElementById('login-form');

    if(form){
        form.addEventListener('submit', formSubmit);
    }

    // sends login into to server for validating (php)
    function formSubmit(event){
        event.preventDefault();

        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;

        const userLogin = {
            username: username,
            password: password,
        }

        fetch('../LAMPAPI/user-endpts/login.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(userLogin)
        })
        .then(response => {
            if(response.ok){
                return response.json();
            } 
            throw new Error('Network Response Failed');
        })
        .then(data => {
            if(data.success){
                alert('Login Successful!');
                window.location.href = '../userHomepage.html'
            } else {
                alert(data.error); // may be secuirty concern
            }
        })
        .catch(error => {
            alert('There was an issue with Login, please try again.');
            console.error('Error:', error);
        });

    }
});