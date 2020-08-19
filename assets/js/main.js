// set up work of navigation menu on mobile screen
const burger = document.querySelector('.navbar-toggler');
const nav = document.querySelector(burger.dataset.target);
const search = document.getElementById('search');
let searchRequest = null;

document.addEventListener("DOMContentLoaded", function (event) {
    burger.addEventListener('click', function () { nav.classList.toggle('collapse'); })
    search.addEventListener('search', function () {
        console.log('close');
        document.getElementById('live_search_columns').classList.add('d-none');
    });
    search.addEventListener('keyup', function () {
        let value = this.value;
        let search = 'users';
        const searchResultContainer = document.getElementById('live_search_columns');
        searchResultContainer.innerHTML = '';
        if (searchRequest)
            searchRequest.abort();
        if (value == '') {
            searchResultContainer.classList.add('d-none');
            return;
        }
        if (value.substring(0, 1) == '#') {
            if (value.length == 1)
                return;
            value = value.substring(1);
            search = 'tags';
        }
        searchRequest = new XMLHttpRequest();
        searchRequest.onreadystatechange = function () {
            if (searchRequest.readyState == 4 && searchRequest.status == 200) {
                let result = JSON.parse(searchRequest.responseText);
                console.log(result);
                searchResultContainer.classList.remove('d-none');
                // console.log(searchResultContainer.classList);
                if (result['message']) {
                    searchResultContainer.innerHTML = 'No results';
                } else if (result['users']) {
                    fillSearchUsersResult(searchResultContainer, result['users']);
                } else if (result['tags']) {
                    console.log('search by tag');
                    result['tags'].forEach(tag => {
                        let html = `
                        <div role="button" class="row py-1 on-hover" onclick="showImagesByTag(this)">
                            <span>#${tag['tag']}</span>
                        </div>`;
                        searchResultContainer.innerHTML += html;
                    });
                }
            }
        };
        searchRequest.open('POST', urlpath + '/search/' + search, true);
        searchRequest.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        searchRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        searchRequest.send('data=' + value);
    });
});

const showImagesByTag = function(div) {
    let tag = div.firstElementChild.innerHTML;
    tag = tag.substring(1);

    window.location = urlpath + '/images/imagesByTag/' + tag;
};

const fillSearchUsersResult = function (div, users) {
    div.innerHTML = '';
    const innerDiv = document.createElement('div');
    users.forEach(element => {
        let html = `
            <div class="row on-hover">
                <a class="text-decoration-none" href="${urlpath}/account/profile/${element['id']}">
                    <div class="media my-3">
                        <img class="rounded-circle media-img mx-2" src="${urlpath}/${element['picture']}" alt="profile image">
                        <div class="media-body">
                            <div class="font-weight-bold">${element['login']}</div>
                            <div>${element['first_name']} ${element['last_name']}</div>
                        </div>
                    </div>
                </a>
            </div>`;
        innerDiv.innerHTML += html;
    });
    div.appendChild(innerDiv);
}

// the root location of our project
const urlpath = '/' + window.location.pathname.split('/')[1];

// add comment to database
const addComment = function (form) {
    event.preventDefault();

    const data = {};
    data['image_id'] = form.dataset.imageId;
    data['comment'] = form.getElementsByTagName('input')[0].value;

    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            let result = JSON.parse(xhr.responseText);
            if (result['message']) {
                showMessage(result['message'], 'alert');
                return;
            }
            form.getElementsByTagName('input')[0].value = '';
            document.getElementById('comments_' + data['image_id']).childNodes[1].innerHTML = ' ' + result['comments'].length;
            document.getElementById('comments_' + data['image_id']).classList.add('user_act');
            // console.log('form = ' + form.id);
            if (form.id && form.id == 'modal_comment_form') {
                // console.log('this is from modal');
                fillComments(result['comments'], result['logged_user_id']);
            }
            form.getElementsByTagName('input')[0].value = '';
            sendEmailAboutComment(form, data);
        }
    };
    xhr.open('POST', urlpath + '/comments/addComment', true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send('data=' + JSON.stringify(data));
}

// send email to user about commenting his photo
const sendEmailAboutComment = function (form, data) {
    let newxhr = new XMLHttpRequest();
    newxhr.onreadystatechange = function () {
        if (newxhr.readyState == 4 && newxhr.status == 200) {
            let result = JSON.parse(newxhr.responseText);
            if (result['message']) {
                showMessage(result['message'], 'alert');
                // alert(result['message']);
            }
            form.getElementsByTagName('input')[0].disabled = false;
            form.getElementsByTagName('input')[0].value = '';
            form.getElementsByTagName('button')[0].disabled = false;
            form.getElementsByTagName('button')[0].innerHTML = 'Send';
            // console.log('email send');
        }
    };
    newxhr.open('POST', urlpath + '/comments/sendCommentEmail', true);
    newxhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    newxhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    newxhr.send('data=' + JSON.stringify(data));
}

// delete comment from image
// data = commentId?imageId?userId
const deleteComment = function (data) {
    let ids = data.split('?');

    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            let result = JSON.parse(xhr.responseText);
            if (result['message']) {
                showMessage(result['message'], 'alert');
                return;
            }
            document.getElementById('comments_' + ids[1]).childNodes[1].innerHTML = ' ' + result['comments'].length;
            if (!result['user_commented']) {
                document.getElementById('comments_' + ids[1]).classList.remove('user_act');
            }
            fillComments(result['comments'], result['logged_user_id']);
        }
    };
    xhr.open('DELETE', urlpath + '/comments/delete/' + data, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
    xhr.send();
}

// delete image from db
const deleteImage = function (button) {
    imageId = button.dataset.imageId;
    let confirmation = confirm('Are you sure you want to delete this photo?');
    if (!confirmation) {
        return;
    }

    let xhr = new XMLHttpRequest();
    xhr.open('GET', urlpath + '/images/delete/' + imageId, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            let result = JSON.parse(xhr.responseText);
            if (result['message'] == 'success') {
                closeModal();
                showMessage('Image successfully deleted');
                setTimeout(function () { window.location.reload(); }, 1000);
            } else {
                showMessage(result['message'], 'alert');
            }
        }
    };
    xhr.send();
}

// triggers when user click follow/unfollow
const follow = function (button) {
    event.preventDefault();
    let userIdToFollow = button.dataset.userId;
    let imageId = button.dataset.imageId;

    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            let result = JSON.parse(xhr.responseText);
            // console.log(result);
            if (result['message']) {
                // alert(result['message']);
                showMessage(result['message'], 'alert');
                return;
            }
            if (result['success'] == 'Follow') {
                button.classList.remove('btn-outline-secondary');
                button.classList.add('btn-success');
            } else {
                button.classList.remove('btn-success');
                button.classList.add('btn-outline-secondary');
            }
            if (button.id && button.id == 'profile_follow') {
                document.getElementById('followers').innerHTML =
                    result['followers_amount'] + ' followers';
            }
            button.innerHTML = result['success'];
        }
    };
    xhr.open('GET', urlpath + '/followers/follow/' + userIdToFollow, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send();
}

// triggers when user likes/unlikes image
const like = function (button) {
    event.preventDefault;
    let imageId = button.dataset.imageId;

    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            let result = JSON.parse(xhr.responseText);
            // console.log(result);
            if (result['message']) {
                showMessage(result['message'], 'alert');
                // alert(result['message']);
                return;
            }
            if (result['success'] == 'liked') {
                document.getElementById('like_button_' + imageId).childNodes[0].classList.add('user_act');
            } else {
                document.getElementById('like_button_' + imageId).childNodes[0].classList.remove('user_act');
            }
            // button.childNodes[0].classList.toggle('user_act');
            button.childNodes[1].innerHTML = ' ' + result['likes_amount'];
            if (button.id == 'modal_like_button') {
                if (result['success'] == 'liked') {
                    button.childNodes[0].classList.add('user_act');
                } else {
                    button.childNodes[0].classList.remove('user_act');
                }
                document.getElementById('like_button_' + imageId).childNodes[1].innerHTML = ' ' + result['likes_amount'];
            }
        }
    };
    xhr.open('GET', urlpath + '/likes/like/' + imageId, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send();
}

const emptySettingErrors = function (form) {
    let inputs = form.getElementsByTagName('input');
    for (let i = 0; i < inputs.length; i++) {
        inputs[i].classList.remove('is-invalid');
        inputs[i].nextElementSibling.innerHTML = '';
    }
}

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

// change profile image
const changeProfilePicture = function (button) {
    // console.log(button);
    let data = {
        'user_id': button.dataset.userId,
        'image_path': button.dataset.imagePath
    }
    // console.log(data);
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            let result = JSON.parse(xhr.responseText);
            if (result['message']) {
                showMessage(result['message'], 'alert');
            } else {
                // change all visible profile picture on the page
                const profilePhoto = document.getElementsByName('picture_' + data['user_id']);
                // console.log(profilePhoto);
                profilePhoto.forEach(element => {
                    element.src = urlpath + '/' + result['path'];
                });
                showMessage('You successfully updated profile photo');
            }
        }
    };
    xhr.open('POST', urlpath + '/account/updatePicture/', true);
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

const register = function (form) {
    event.preventDefault();
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
    xhr.open('POST', urlpath + '/users/forgetPassword/', true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send('data=' + JSON.stringify(data));
}


