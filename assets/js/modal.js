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
