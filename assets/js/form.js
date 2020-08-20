// Clean error messages in the form input
const emptySettingErrors = function (form) {
    let inputs = form.getElementsByTagName('input');
    for (let i = 0; i < inputs.length; i++) {
        inputs[i].classList.remove('is-invalid');
        inputs[i].nextElementSibling.innerHTML = '';
    }
}

// Update user's new information from the editing modal form
const saveChanges = function (form) {
    event.preventDefault();
    data = {
        'id': form.dataset.userId,
        'login': form.login.value,
        'first_name': form.first_name.value,
        'last_name': form.last_name.value,
        'email': form.email.value,
        'old_pswd': form.old_pswd.value,
        'new_pswd': form.new_pswd.value,
        'new_pswd_confirm': form.new_pswd_confirm.value,
        'notify': form.notifications.checked
    }
    emptySettingErrors(form);

    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            let result = JSON.parse(xhr.responseText);
            if (result['message']) {
                showMessage(result['message'], 'alert');
            } else if (result['errors']) {
                for (let key in result['errors']) {
                    let span = document.getElementById('modal_' + key);
                    span.previousElementSibling.classList.add('is-invalid');
                    span.innerHTML = result['errors'][key];
                }
            } else {
                showMessage('Your information is successfully updated');
                document.getElementById('profile_login').innerHTML = data['login'];
                document.getElementById('profile_name').innerHTML = data['first_name'] + ' ' + data['last_name'];
                document.getElementById('profile_email').innerHTML = data['email'];
                closeModal();
            }
        }
    };
    xhr.open('POST', urlpath + '/account/update/', true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send('data=' + JSON.stringify(data));
}

const login = function (form) {
    event.preventDefault();
    data = {
        'email': form.email.value,
        'password': form.password.value
    };
    emptySettingErrors(form);

    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            let result = JSON.parse(xhr.responseText);
            if (result['errors']) {
                for (let key in result['errors']) {
                    let span = document.getElementsByName(key)[0];
                    span.previousElementSibling.classList.add('is-invalid');
                    span.innerHTML = result['errors'][key];
                }
            } else {
                window.location.reload();
            }
        }
    };
    xhr.open('POST', urlpath + '/users/login/', true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send('data=' + JSON.stringify(data));
}

let processRegister = false;
let passwordValid = false;

const register = function (form) {
    event.preventDefault();
    if (!passwordValid) {
        return;
    }
    if (processRegister) {
        showMessage('Registration in process');
        return;
    }
    processRegister = true;
    document.getElementById('registerButton').disabled = true;

    data = {
        'login': form.login.value,
        'first_name': form.first_name.value,
        'last_name': form.last_name.value,
        'email': form.email.value,
        'password': form.password.value,
        'confirm_password': form.confirm_password.value
    };
    emptySettingErrors(form);

    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            let result = JSON.parse(xhr.responseText);
            processRegister = false;
            document.getElementById('registerButton').disabled = false;
            showMessage(result['message']);
            for (let key in result['errors']) {
                let span = document.getElementsByName(key)[0];
                span.previousElementSibling.classList.add('is-invalid');
                span.innerHTML = result['errors'][key];
            }
            if (Object.keys(result['errors']).length == 0) {
                setTimeout(function () { window.location = urlpath + '/users/login'; }, 1000);
            }
        }
    };
    xhr.open('POST', urlpath + '/users/register/', true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send('data=' + JSON.stringify(data));
}

const resetPassword = function (form) {
    event.preventDefault();
    if (!passwordValid) {
        return;
    }
    data = {
        'email': form.dataset.email,
        'password': form.password.value,
        'confirm_password': form.confirm_password.value
    }
    emptySettingErrors(form);

    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            let result = JSON.parse(xhr.responseText);
            showMessage(result['message']);
            for (let key in result['errors']) {
                let span = document.getElementsByName(key)[0];
                span.previousElementSibling.classList.add('is-invalid');
                span.innerHTML = result['errors'][key];
            }
            if (Object.keys(result['errors']).length == 0) {
                setTimeout(function () { window.location = urlpath + '/users/login'; }, 1000);
            }
        }
    }
    xhr.open('POST', urlpath + '/account/resetPassword/', true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send('data=' + JSON.stringify(data));
}

let sendingResetPassword = false;

const forgetPassword = function (form) {
    event.preventDefault();

    if (sendingResetPassword) {
        showMessage('Sending email');
        return;
    }

    data = { 'email': form.email.value }
    emptySettingErrors(form);

    sendingResetPassword = true;
    document.getElementById('forgetPasswordButton').disabled = true;

    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            let result = JSON.parse(xhr.responseText);
            sendingResetPassword = false;
            document.getElementById('forgetPasswordButton').disabled = false;
            showMessage(result['message']);
            for (let key in result['errors']) {
                let span = document.getElementsByName(key)[0];
                span.previousElementSibling.classList.add('is-invalid');
                span.innerHTML = result['errors'][key];
            }
            if (Object.keys(result['errors']).length == 0) {
                setTimeout(function () { window.location = urlpath + '/users/login'; }, 2000);
            }
        }
    }
    xhr.open('POST', urlpath + '/account/forgetPassword/', true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send('data=' + JSON.stringify(data));
}

// Trigger when user type in password field in the form
const checkPasswordStrength = function (input) {
    let mediumRegex = new RegExp("^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.{8,})");
    let numberRegex = new RegExp("^(?=.*[0-9])");
    let uppercaseRegex = new RegExp("^(?=.*[A-Z])");
    let lowercaseRegex = new RegExp("^(?=.*[a-z])");
    let value = input.value;

    let requirements = '';
    input.classList.remove('is-invalid');
    input.setAttribute("style", "border: 1px solid #ced4da");
    input.nextElementSibling.innerHTML = '';
    passwordValid = true;
    if (value != '' && !mediumRegex.test(value)) {
        passwordValid = false;
        input.setAttribute("style", "border: 2px solid red;");
        input.classList.add('is-invalid');
        if (value.length < 8)
            requirements += "Password must have at least 8 characters<br>";
        if (!uppercaseRegex.test(value))
            requirements += "Password must have at least 1 uppercase letter<br>";
        if (!lowercaseRegex.test(value))
            requirements += "Password must have at least 1 lowercase letter<br>";
        if (!numberRegex.test(value))
            requirements += "Password must have at least 1 number<br>";
        input.nextElementSibling.innerHTML = requirements.replace(/<br>\s*$/, "");
    }
}
