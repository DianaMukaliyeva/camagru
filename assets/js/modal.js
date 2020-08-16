const fillModalProfile = function () {
    // console.log(userId);
    let xhr = new XMLHttpRequest();
    xhr.open('GET', urlpath + '/account/userInfo/', true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    // xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            let result = JSON.parse(xhr.responseText);
            // console.log(result);
            if (result['message']) {
                alert(result['message']);
                return;
            }
            let form = document.getElementById('editForm');
            form.setAttribute('data-user-id', result['id']);
            form.login.value = result['login'];
            form.first_name.value = result['first_name'];
            form.last_name.value = result['last_name'];
            form.email.value = result['email'];
            form.notifications.checked = result['notify'] ? true : false;
        }
    };
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
            // console.log(result);
            if (result['message']) {
                alert(result['message']);
                return;
            }
            if (result['user_liked'] != 0) {
                document.getElementById('modal_like_button').childNodes[0].classList.add('user_act');
            } else {
                document.getElementById('modal_like_button').childNodes[0].classList.remove('user_act');
            }
            document.getElementById('modal_profile_photo').src = urlpath + '/' + result['picture'];
            document.getElementById('modal_profile_login').parentElement.href = urlpath + '/account/profile/' + result['user_id'];
            document.getElementById('modal_profile_login').innerHTML = result['login'];
            document.getElementById('modal_image').src = urlpath + '/' + result['image_path'];
            document.getElementById('modal_image_date').innerHTML = result['created_at'];
            document.getElementById('modal_like_button').childNodes[1].innerHTML = ' ' + result['like_amount'];
            document.getElementById('modal_like_button').dataset.imageId = imageId;
            document.getElementById('modal_comment_form').dataset.imageId = imageId;
            document.getElementById('modal_image_tags').innerHTML = '';
            document.getElementById('modal_follow_button').setAttribute('data-user-id', result['user_id']);
            if (result['login'] == result['logged_in_user']) {
                document.getElementById('modal_follow_button').classList.add('d-none');
                document.getElementById('modal_delete_button').classList.remove('d-none');
                document.getElementById('modal_delete_button').setAttribute('data-image-id', imageId);
            } else {
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
            fillComments(result['comments'], result['logged_user_id']);
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
        // console.log(comment);
        p.innerHTML = `<a href="${urlpath}/account/profile/${comment['user_id']}">${comment['login']}</a> (${comment['created_at']}) :
            <a role="button" onclick="deleteComment(this.dataset.dataId)" data-data-id="${comment['id']}?${comment['image_id']}?${comment['user_id']}'">
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
    } else {
        fillModalImage(param);
    }

    document.getElementById("backdrop").classList.remove('d-none');
    document.getElementById("exampleModal").style.display = "block";
    document.getElementById("exampleModal").classList.add('show');
}

// close modal window with image
const closeModal = function () {
    document.getElementById("backdrop").classList.add('d-none')
    document.getElementById("exampleModal").style.display = "none";
    document.getElementById("exampleModal").classList.remove("show");
}

const modal = document.getElementById('exampleModal');

// When the user clicks anywhere outside of the modal, close it
window.onclick = function (event) {
    if (event.target === modal) {
        closeModal();
    }
}
