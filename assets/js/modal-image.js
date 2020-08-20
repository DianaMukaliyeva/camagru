const modalImageContainer = document.getElementById('modal_image');
const modalImageDelete = document.getElementById('modal_delete_button');
const modalImageTags = document.getElementById('modal_image_tags');
const modalChangePicture = document.getElementById('modal_change_picture');
const modalFollowButton = document.getElementById('modal_follow_button');
const modalLikeButton = document.getElementById('modal_like_button');
const modalImageComments = document.getElementById('modal_image_comments');
const modalProfilePhoto = document.getElementById('modal_profile_photo');
const modalProfileLogin = document.getElementById('modal_profile_login');

// Clean Modal image before filling with result
const cleanModalImage = function (imageId) {
    modalImageContainer.src = urlpath + '/assets/img/images/loading.png';
    modalImageDelete.classList.add('d-none');
    modalImageDelete.dataset.imageId = imageId;
    modalImageTags.innerHTML = '';
    modalImageTags.dataset.imageId = imageId;
    modalChangePicture.classList.add('d-none');
    modalFollowButton.classList.add('d-none', 'btn-outline-secondary');
    modalFollowButton.innerHTML = "Unfollow";
    modalLikeButton.dataset.imageId = imageId;
    modalLikeButton.childNodes[0].classList.remove('user_act');
    modalImageComments.innerHTML = '';
    document.getElementById('modal_comment_form').dataset.imageId = imageId;
}

// Fill modal image with results from the server
const fillModalImageResult = function (imageId, result) {
    if (result['user_liked'] != 0) {
        modalLikeButton.childNodes[0].classList.add('user_act');
    }
    if (result['tags'].length > 0) {
        result['tags'].forEach(element => {
            modalImageTags.innerHTML += '#' + element['tag'] + ' ';
        });
    }
    if (result['login'] == result['logged_in_user']) {
        modalImageDelete.classList.remove('d-none');
        modalChangePicture.classList.remove('d-none');
    } else {
        if (result['user_follow'] == 0) {
            modalFollowButton.classList.add('btn-success');
            modalFollowButton.innerHTML = "Follow";
        }
        modalFollowButton.classList.remove('d-none');
    }
    document.getElementById('modal_image_date').innerHTML = result['created_at'];
    modalProfilePhoto.src = urlpath + '/' + result['picture'];
    modalProfilePhoto.name = 'picture_' + result['user_id'];
    modalProfileLogin.parentElement.href = urlpath + '/account/profile/' + result['user_id'];
    modalProfileLogin.innerHTML = result['login'];
    modalLikeButton.childNodes[1].innerHTML = ' ' + result['like_amount'];
    modalFollowButton.setAttribute('data-user-id', result['user_id']);
    modalChangePicture.setAttribute('data-user-id', result['user_id']);
    modalChangePicture.setAttribute('data-image-path', result['image_path']);
    modalImageContainer.src = urlpath + '/' + result['image_path'];
    fillComments(result['comments'], result['logged_user_id']);
}

// Get info about image and fill modal window with it
const fillModalImage = function (imageId) {
    cleanModalImage(imageId);

    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            let result = JSON.parse(xhr.responseText);
            if (result['message']) {
                showMessage(result['message'], true);
                return;
            }
            fillModalImageResult(imageId, result);
        }
    };
    xhr.open('GET', urlpath + '/images/imageInfo/' + imageId, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.send();
}
