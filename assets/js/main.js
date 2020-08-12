// set up work of navigation menu on mobile screen
const burger = document.querySelector('.navbar-toggler');
const nav = document.querySelector(burger.dataset.target);

burger.addEventListener('click', function () { nav.classList.toggle('collapse'); })

// the root location of our project
const urlpath = window.location.pathname.split('/')[1];

const createComment = function (comment, div) {
    const comment_div = document.createElement('div');
    const p = document.createElement('p');
    p.innerHTML = "<a class='link' href=''>" + comment['login'] + "</a> (" + comment['created_at'] + ") :<br> <i>" + comment['comment'] + "</i>";
    comment_div.appendChild(p);
    div.appendChild(comment_div);
}

const fillComments = function (comments, div) {
    comments.forEach(comment => {
        createComment(comment, div);
    });
}

const fillModalImage = function (imageId) {
    let xhr = new XMLHttpRequest();
    xhr.open('GET', '/' + urlpath + '/images/imageInfo/' + imageId, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            let result = JSON.parse(xhr.responseText);
            console.log(result);
            if (result['message'] == 'liked') {
                document.getElementById('modal_like_button').childNodes[0].classList.add('my_like');
            } else if (result['message'] == 'unliked') {
                document.getElementById('modal_like_button').childNodes[0].classList.remove('my_like');
            } else {
                alert(result['message']);
                return;
            }
            document.getElementById('modal_profile_photo').src = result['profile_photo'];
            document.getElementById('modal_profile_login').innerHTML = result['user_login'];
            document.getElementById('modal_image').src = result['image_path'];
            document.getElementById('modal_image_date').innerHTML = result['created_at'];
            document.getElementById('modal_like_button').childNodes[1].innerHTML = ' ' + result['likes_amount'];
            document.getElementById('modal_like_button').dataset.imageId = imageId;
            document.getElementById('modal_comment_form').dataset.imageId = imageId;
            if (result['tags'].length > 0) {
                result['tags'].forEach(element => {
                    document.getElementById('modal_image_tags').innerHTML += '#' + element['tag'] + ' ';
                });
            } else {
                document.getElementById('modal_image_tags').innerHTML = '';
            }
            document.getElementById('modal_image_tags').dataset.imageId = imageId;
            if (result['comments'].length != 0)
                fillComments(result['comments'], document.getElementById('modal_image_comments'));
            else {
                document.getElementById('modal_image_comments').innerHTML = 'No comments yet';
                document.getElementById('modal_image_comments').classList.add('text-center');
            }
        }
    };
    xhr.send();
}

const openModal = function (imageId) {
    if (imageId) {
        fillModalImage(imageId);
    }

    document.getElementById("backdrop").classList.remove('d-none');
    document.getElementById("exampleModal").style.display = "block";
    document.getElementById("exampleModal").classList.add('show');
}

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