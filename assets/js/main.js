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
    sendingComment = true;
    form.getElementsByTagName('input')[0].disabled = true;
    form.getElementsByTagName('button')[0].disabled = true;
    form.getElementsByTagName('button')[0].innerHTML = 'Sending';

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
            } else {
                alert(result['message']);
            }
            // console.log('comment inserted');
        }
    };
    xhr.open('POST', '/' + urlpath + '/comments/addComment', true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send('data=' + JSON.stringify(data));

    // send email to user about commentin his photo
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
    newxhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
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

// fill modal window with comments
const fillComments = function (comments, loggedUserId) {
    const div = document.getElementById('modal_image_comments');
    div.innerHTML = "<h5 class='text-center'>Comments</h5>";
    comments.forEach(comment => {
        const comment_div = document.createElement('div');
        const p = document.createElement('p');
        p.innerHTML = `<a href=''>${comment['login']}</a> (${comment['created_at']}) :
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


// get info about image and fill modal window with it
const fillModalImage = function (imageId) {
    let xhr = new XMLHttpRequest();
    xhr.open('GET', '/' + urlpath + '/images/imageInfo/' + imageId, true);
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
            if (result['user_liked']) {
                document.getElementById('modal_like_button').childNodes[0].classList.add('user_act');
            } else {
                document.getElementById('modal_like_button').childNodes[0].classList.remove('user_act');
            }
            document.getElementById('modal_profile_photo').src = result['profile_photo'];
            document.getElementById('modal_profile_login').innerHTML = result['user_login'];
            document.getElementById('modal_image').src = result['image_path'];
            document.getElementById('modal_image_date').innerHTML = result['created_at'];
            document.getElementById('modal_like_button').childNodes[1].innerHTML = ' ' + result['likes_amount'];
            document.getElementById('modal_like_button').dataset.imageId = imageId;
            document.getElementById('modal_comment_form').dataset.imageId = imageId;
            document.getElementById('modal_image_tags').innerHTML = '';
            if (result['user_login'] == result['logged_in_user']) {
                document.getElementById('modal_delete_button').classList.remove('d-none');
                document.getElementById('modal_delete_button').setAttribute('data-image-id', imageId);
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

// open modal window for image
const openModal = function (imageId) {
    event.preventDefault();
    if (imageId) {
        fillModalImage(imageId);
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