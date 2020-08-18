// show toast message
const showMessage = function (message = '', alert = false) {
    if (message == '') {
        return;
    }
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

// close toast message
const closeMessage = function (button) {
    button.parentElement.classList.remove('show');
    button.parentElement.classList.add('d-none');
}

// fill modal profile settings
const fillModalFollow = function (param) {
    let userId = document.getElementById('profile_login').dataset.userId;
    document.getElementById('follow-title').innerHTML = param;
    let div = document.getElementById('list-user');
    div.innerHTML = '';
    let xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            let result = JSON.parse(this.responseText);
            // console.log(result)
            if (param == 'followers') {
                // followed.classList.remove('d-none');
                appendToContainer(div, result['followers']);
            } else {
                appendToContainer(div, result['followed']);
                // followers.classList.remove('d-none');
                // appendToContainer(followers.children[1], result['followers']);
            }
        }
    }
    xmlhttp.open("GET", urlpath + "/followers/getFollow/" + userId, true);
    xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xmlhttp.send();
}

// fill modal profile settings
const fillModalProfile = function () {
    emptySettingErrors(document.getElementById('editForm'));

    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            let result = JSON.parse(xhr.responseText);
            if (result['message']) {
                showMessage(result['message'], true);
                return;
            }
            let form = document.getElementById('editForm');
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
    xhr.open('GET', urlpath + '/account/userInfo/', true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.send();
}

// get info about image and fill modal window with it
const fillModalImage = function (imageId) {
    let xhr = new XMLHttpRequest();
    xhr.open('GET', urlpath + '/images/imageInfo/' + imageId, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            let result = JSON.parse(xhr.responseText);
            if (result['message']) {
                showMessage(result['message'], true);
                return;
            }
            document.getElementById('modal_image').src = urlpath + '/' + result['image_path'];
            fillComments(result['comments'], result['logged_user_id']);
            document.getElementById('modal_profile_photo').src = urlpath + '/' + result['picture'];
            document.getElementById('modal_profile_photo').name = 'picture_' + result['user_id'];
            if (result['user_liked'] != 0) {
                document.getElementById('modal_like_button').childNodes[0].classList.add('user_act');
            } else {
                document.getElementById('modal_like_button').childNodes[0].classList.remove('user_act');
            }
            document.getElementById('modal_profile_login').parentElement.href = urlpath + '/account/profile/' + result['user_id'];
            document.getElementById('modal_profile_login').innerHTML = result['login'];
            document.getElementById('modal_image_date').innerHTML = result['created_at'];
            document.getElementById('modal_like_button').childNodes[1].innerHTML = ' ' + result['like_amount'];
            document.getElementById('modal_like_button').dataset.imageId = imageId;
            document.getElementById('modal_comment_form').dataset.imageId = imageId;
            document.getElementById('modal_image_tags').innerHTML = '';
            document.getElementById('modal_follow_button').setAttribute('data-user-id', result['user_id']);
            document.getElementById('modal_change_picture').setAttribute('data-user-id', result['user_id']);
            document.getElementById('modal_delete_button').dataset.imageId = imageId;
            if (result['login'] == result['logged_in_user']) {
                document.getElementById('modal_follow_button').classList.add('d-none');
                document.getElementById('modal_delete_button').classList.remove('d-none');
                document.getElementById('modal_change_picture').classList.remove('d-none');
                document.getElementById('modal_change_picture').setAttribute('data-image-path', result['image_path']);
            } else {
                document.getElementById('modal_change_picture').classList.add('d-none');
                if (result['user_follow'] == 0) {
                    document.getElementById('modal_follow_button').classList.add('btn-success');
                    document.getElementById('modal_follow_button').innerHTML = "Follow";
                } else {
                    document.getElementById('modal_follow_button').classList.add('btn-outline-secondary');
                    document.getElementById('modal_follow_button').innerHTML = "Unfollow";
                }
                document.getElementById('modal_follow_button').classList.remove('d-none');
                document.getElementById('modal_delete_button').classList.add('d-none');
            }
            if (result['tags'].length > 0) {
                result['tags'].forEach(element => {
                    document.getElementById('modal_image_tags').innerHTML += '#' + element['tag'] + ' ';
                });
            }
            document.getElementById('modal_image_tags').dataset.imageId = imageId;
        }
    };
    xhr.send();
}

// fill modal window with comments
const fillComments = function (comments, loggedUserId) {
    const div = document.getElementById('modal_image_comments');
    div.innerHTML = "<h5 class='text-center py-2'>Comments</h5>";
    comments.forEach(comment => {
        const comment_div = document.createElement('div');
        const p = document.createElement('p');
        p.innerHTML = `<a href="${urlpath}/account/profile/${comment['user_id']}">${comment['login']}</a> (${comment['created_at']}) :
            <a role="button" onclick="deleteComment(this.dataset.dataId)" data-data-id="${comment['id']}?${comment['image_id']}?${comment['user_id']}">
            <i class='fas fa-times-circle'></i></a>
            <br><i>${comment['comment']}</i>`;
        if (comment['user_id'] != loggedUserId)
            p.getElementsByTagName('a')[1].classList.add('d-none');
        else
            p.getElementsByTagName('a')[1].classList.remove('d-none');
        comment_div.appendChild(p);
        div.appendChild(comment_div);
    });
}

// open modal window for image
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
        document.getElementById("exampleModal").style.display = "block";
        document.getElementById("exampleModal").classList.add('show');
    }

    document.getElementById("backdrop").classList.remove('d-none');
}

const modalImage = document.getElementById('exampleModal');
const modalSettings = document.getElementById('settings');
const modalFollow = document.getElementById('follow');

// close modal window with image
const closeModal = function () {
    document.getElementById("backdrop").classList.add('d-none')
    modalImage.style.display = "none";
    modalImage.classList.remove("show");
    modalSettings.style.display = "none";
    modalSettings.classList.remove("show");
    modalFollow.style.display = "none";
    modalFollow.classList.remove("show");
}


// When the user clicks anywhere outside of the modal, close it
window.onclick = function (event) {
    if (event.target === modalImage || event.target === modalSettings ||
        event.target === modalFollow) {
        closeModal();
    }
}
