const modalImage = document.getElementById('exampleModal');
const modalSettings = document.getElementById('settings');
const modalFollow = document.getElementById('follow');

// When the user clicks anywhere outside of the modal, close it
window.addEventListener('click', function (event) {
    if (event.target === modalImage || event.target === modalSettings || event.target === modalFollow)
        closeModal();
});

// Show toast message
const showMessage = function (message = '', alert = false) {
    let toast = document.getElementById('message');
    let messages = message.split("\n");
    if (alert) {
        toast.className = "alert";
        setTimeout(function () { toast.className = toast.className.replace("alert", ""); }, 4000);
    } else {
        toast.className = "show";
        setTimeout(function () { toast.className = toast.className.replace("show", ""); }, 3000);
    }
    toast.children[1].innerHTML = '';
    messages.forEach(element => {
        if (element != '') {
            toast.children[1].innerHTML += element + "<br>";
        }
    });
}

// Close toast message
const closeMessage = function (button) {
    button.parentElement.classList.remove('show');
    button.parentElement.classList.add('d-none');
}

// Append following/follower users to modal
const appendUsersToDiv = function (div, users) {
    div.innerHTML = '';
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
        div.innerHTML += html;
    });
}

// Fill modal profile settings
const fillModalFollow = function (param) {
    let userId = document.getElementById('profile_login').dataset.userId;
    let div = document.getElementById('list-user');
    div.innerHTML = '';
    document.getElementById('follow-title').innerHTML = param;

    let xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            let result = JSON.parse(this.responseText);
            appendUsersToDiv(div, result[param]);
        }
    }
    xmlhttp.open("GET", urlpath + "/followers/getFollow/" + userId, true);
    xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xmlhttp.send();
}

// Fill modal profile settings
const fillModalProfile = function () {
    let form = document.getElementById('editForm');
    emptySettingErrors(form);

    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            let result = JSON.parse(xhr.responseText);
            if (result['message']) {
                showMessage(result['message'], true);
                return;
            }
            form.setAttribute('data-user-id', result['id']);
            form.login.value = result['login'];
            form.first_name.value = result['first_name'];
            form.last_name.value = result['last_name'];
            form.email.value = result['email'];
            form.notifications.checked = result['notify'] ? true : false;
            form.old_pswd.value = '';
            form.new_pswd.value = '';
            form.new_pswd_confirm.value = '';
        }
    };
    xhr.open('GET', urlpath + '/users/userInfo/', true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.send();
}

// Get info about image and fill modal window with it
const fillModalImage = function (imageId) {
    document.getElementById('modal_image').src = urlpath + '/assets/img/images/loading.png';
    document.getElementById('modal_delete_button').classList.add('d-none');
    document.getElementById('modal_image_tags').innerHTML = '';
    document.getElementById('modal_change_picture').classList.add('d-none');
    document.getElementById('modal_follow_button').classList.add('d-none');
    document.getElementById('modal_like_button').childNodes[0].classList.remove('user_act');
    document.getElementById('modal_image_comments').innerHTML = '';
    document.getElementById('modal_follow_button').classList.add('btn-outline-secondary');
    document.getElementById('modal_follow_button').innerHTML = "Unfollow";

    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            let result = JSON.parse(xhr.responseText);
            if (result['message']) {
                showMessage(result['message'], true);
                return;
            }
            if (result['user_liked'] != 0) {
                document.getElementById('modal_like_button').childNodes[0].classList.add('user_act');
            }
            if (result['tags'].length > 0) {
                result['tags'].forEach(element => {
                    document.getElementById('modal_image_tags').innerHTML += '#' + element['tag'] + ' ';
                });
            }
            if (result['login'] == result['logged_in_user']) {
                document.getElementById('modal_delete_button').classList.remove('d-none');
                document.getElementById('modal_change_picture').classList.remove('d-none');
            } else {
                if (result['user_follow'] == 0) {
                    document.getElementById('modal_follow_button').classList.add('btn-success');
                    document.getElementById('modal_follow_button').innerHTML = "Follow";
                }
                document.getElementById('modal_follow_button').classList.remove('d-none');
            }
            document.getElementById('modal_profile_photo').src = urlpath + '/' + result['picture'];
            document.getElementById('modal_profile_photo').name = 'picture_' + result['user_id'];
            document.getElementById('modal_profile_login').parentElement.href = urlpath + '/account/profile/' + result['user_id'];
            document.getElementById('modal_profile_login').innerHTML = result['login'];
            document.getElementById('modal_image_date').innerHTML = result['created_at'];
            document.getElementById('modal_like_button').childNodes[1].innerHTML = ' ' + result['like_amount'];
            document.getElementById('modal_like_button').dataset.imageId = imageId;
            document.getElementById('modal_comment_form').dataset.imageId = imageId;
            document.getElementById('modal_follow_button').setAttribute('data-user-id', result['user_id']);
            document.getElementById('modal_change_picture').setAttribute('data-user-id', result['user_id']);
            document.getElementById('modal_change_picture').setAttribute('data-image-path', result['image_path']);
            document.getElementById('modal_image_tags').dataset.imageId = imageId;
            document.getElementById('modal_image').src = urlpath + '/' + result['image_path'];
            document.getElementById('modal_delete_button').dataset.imageId = imageId;
            fillComments(result['comments'], result['logged_user_id']);
        }
    };
    xhr.open('GET', urlpath + '/images/imageInfo/' + imageId, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.send();
}

// Open modal window for image
const openModal = function (param) {
    event.preventDefault();
    if (param == 'editProfile') {
        fillModalProfile();
        modalSettings.style.display = "block";
        modalSettings.classList.add('show');
    } else if (param == 'followers' || param == 'following') {
        fillModalFollow(param);
        modalFollow.style.display = "block";
        modalFollow.classList.add('show');
    } else {
        fillModalImage(param);
        modalImage.style.display = "block";
        modalImage.classList.add('show');
    }
    document.getElementById("backdrop").classList.remove('d-none');
}

// Close modal window with image
const closeModal = function () {
    document.getElementById("backdrop").classList.add('d-none')
    if (modalImage)
        modalImage.style.display = "none";
    if (modalSettings)
        modalSettings.style.display = "none";
    if (modalFollow)
        modalFollow.style.display = "none";
}
