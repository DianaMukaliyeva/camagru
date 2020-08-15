// set up work of navigation menu on mobile screen
const burger = document.querySelector('.navbar-toggler');
const nav = document.querySelector(burger.dataset.target);

burger.addEventListener('click', function () { nav.classList.toggle('collapse'); })

// the root location of our project
const urlpath = window.location.pathname.split('/')[1];
let sendingComment = false;

// add comment to database
const addComment = function (form) {
    event.preventDefault();
    if (sendingComment) {
        return;
    }

    const data = {};
    data['image_id'] = form.dataset.imageId;
    data['comment'] = form.getElementsByTagName('input')[0].value;

    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            let result = JSON.parse(xhr.responseText);
            if (result['success']) {
                document.getElementById('comments_' + data['image_id']).childNodes[1].innerHTML = ' ' + result['comments'].length;
                document.getElementById('comments_' + data['image_id']).classList.add('user_act');
                // console.log('form = ' + form.id);
                if (form.id && form.id == 'modal_comment_form') {
                    // console.log('this is from modal');
                    fillComments(result['comments'], result['logged_user_id']);
                }
                sendingComment = true;
                form.getElementsByTagName('input')[0].disabled = true;
                form.getElementsByTagName('button')[0].disabled = true;
                form.getElementsByTagName('button')[0].innerHTML = 'Sending';
                sendEmailAboutComment(form, data);
            } else {
                form.getElementsByTagName('input')[0].value = '';
                alert(result['message']);
            }
            // console.log('comment inserted');
        }
    };
    xhr.open('POST', '/' + urlpath + '/comments/addComment', true);
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
                alert(result['message']);
            }
            sendingComment = false;
            form.getElementsByTagName('input')[0].disabled = false;
            form.getElementsByTagName('input')[0].value = '';
            form.getElementsByTagName('button')[0].disabled = false;
            form.getElementsByTagName('button')[0].innerHTML = 'Send';
            // console.log('email send');
        }
    };
    newxhr.open('POST', '/' + urlpath + '/comments/sendCommentEmail', true);
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
            if (result['message'] == 'success') {
                document.getElementById('comments_' + ids[1]).childNodes[1].innerHTML = ' ' + result['comments'].length;
                fillComments(result['comments'], result['logged_user_id']);
            } else {
                alert(result['message']);
            }
        }
    };
    xhr.open('DELETE', '/' + urlpath + '/comments/delete/' + data, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
    xhr.send();
}

// delete image from db
const deleteImage = function (button) {
    imageId = button.dataset.imageId;

    let xhr = new XMLHttpRequest();
    xhr.open('GET', '/' + urlpath + '/images/delete/' + imageId, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            let result = JSON.parse(xhr.responseText);
            if (result['message'] == 'success') {
                closeModal();
                location.reload();
            } else {
                alert(result['message']);
            }
        }
    };
    xhr.send();
}

// triggers when user likes/unlikes image
const like = function (button) {
    event.preventDefault;
    let imageId = button.dataset.imageId;

    let xhr = new XMLHttpRequest();
    xhr.open('GET', '/' + urlpath + '/likes/like/' + imageId, true);
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
            button.childNodes[0].classList.toggle('user_act');
            button.childNodes[1].innerHTML = ' ' + result['likes_amount'];
            if (button.id == 'modal_like_button') {
                document.getElementById('like_button_' + imageId).childNodes[0].classList.toggle('user_act');
                document.getElementById('like_button_' + imageId).childNodes[1].innerHTML = ' ' + result['likes_amount'];
            }
        }
    };
    xhr.send();
}
