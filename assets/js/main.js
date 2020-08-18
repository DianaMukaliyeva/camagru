// set up work of navigation menu on mobile screen
const burger = document.querySelector('.navbar-toggler');
const nav = document.querySelector(burger.dataset.target);

burger.addEventListener('click', function () { nav.classList.toggle('collapse'); })

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
    // console.log(confirmation);
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
                setTimeout(function () { location.reload(); }, 1000);
                // location.reload();
            } else {
                showMessage(result['message'], 'alert');
                // alert(result['message']);
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
                document.getElementById('profile_followers_amount').innerHTML =
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
                button.childNodes[0].classList.add('user_act');
            } else {
                button.childNodes[0].classList.remove('user_act');
            }
            button.childNodes[0].classList.toggle('user_act');
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

const saveChanges = function (form) {
    event.preventDefault();
    // console.log(form.dataset.userId);
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
    // console.log(data);
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
            showMessage('Your information is successfully updated');
            document.getElementById('profile_login').innerHTML = data['login'];
            document.getElementById('profile_name').innerHTML = data['first_name'] + ' ' + data['last_name'];
            document.getElementById('profile_email').innerHTML = data['email'];
            closeModal();
            // button.childNodes[0].classList.toggle('user_act');
            // button.childNodes[1].innerHTML = ' ' + result['likes_amount'];
            // if (button.id == 'modal_like_button') {
            //     document.getElementById('like_button_' + imageId).childNodes[0].classList.toggle('user_act');
            //     document.getElementById('like_button_' + imageId).childNodes[1].innerHTML = ' ' + result['likes_amount'];
            // }
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
