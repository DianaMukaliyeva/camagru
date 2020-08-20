let searchRequest = null; // xmlhttprequest for search

// the root location of our project
const urlpath = '/' + window.location.pathname.split('/')[1];

// set up work of navigation menu on mobile screen
const burger = document.querySelector('.navbar-toggler');
const nav = document.querySelector(burger.dataset.target);
const searchInput = document.getElementById('search');
const searchResultContainer = document.getElementById('live_search_columns');

document.addEventListener("DOMContentLoaded", function (event) {
    burger.addEventListener('click', function () { nav.classList.toggle('collapse'); })
    searchInput.addEventListener('search', function () { searchResultContainer.classList.add('d-none') });
    searchInput.addEventListener('keyup', function () { search(this.value) });
});

// Send request to server to find users or tags
const search = function (value) {
    let search = 'users';
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
            searchResultContainer.classList.remove('d-none');
            if (result['message']) {
                searchResultContainer.innerHTML = 'No results';
            } else if (result['users']) {
                fillSearchUsersResult(searchResultContainer, result['users']);
            } else if (result['tags']) {
                result['tags'].forEach(tag => {
                    let html = `
                        <a role="button" class="row py-1 on-hover" href="${urlpath}/images/getImages/${tag['tag']}">
                            <span>#${tag['tag']}</span>
                        </a>`;
                    searchResultContainer.innerHTML += html;
                });
            }
        }
    };
    searchRequest.open('POST', urlpath + '/search/' + search, true);
    searchRequest.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    searchRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    searchRequest.send('data=' + value);
}

// Show search result in the pop down window
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

// Delete image from db
const deleteImage = function (button) {
    imageId = button.dataset.imageId;
    let confirmation = confirm('Are you sure you want to delete this photo?');
    if (!confirmation) {
        return;
    }

    let xhr = new XMLHttpRequest();
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
    xhr.open('GET', urlpath + '/images/delete/' + imageId, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.send();
}

// Triggers when user click follow/unfollow
const follow = function (button) {
    event.preventDefault();
    let userIdToFollow = button.dataset.userId;

    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            let result = JSON.parse(xhr.responseText);
            if (result['message']) {
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

// Triggers when user likes/unlikes image
const like = function (button) {
    event.preventDefault;
    let imageId = button.dataset.imageId;

    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            let result = JSON.parse(xhr.responseText);
            if (result['message']) {
                showMessage(result['message'], 'alert');
                return;
            }
            if (result['success'] == 'liked') {
                document.getElementById('like_button_' + imageId).childNodes[0].classList.add('user_act');
            } else {
                document.getElementById('like_button_' + imageId).childNodes[0].classList.remove('user_act');
            }
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
    xhr.send();
}

// Change profile image
const changeProfilePicture = function (button) {
    let data = {
        'user_id': button.dataset.userId,
        'image_path': button.dataset.imagePath
    }

    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            let result = JSON.parse(xhr.responseText);
            if (result['message']) {
                showMessage(result['message'], 'alert');
            } else {
                // change all visible profile picture on the page
                const profilePhoto = document.getElementsByName('picture_' + data['user_id']);
                profilePhoto.forEach(element => {
                    element.src = urlpath + '/' + result['path'];
                });
                showMessage('You successfully updated profile photo');
            }
        }
    };
    xhr.open('POST', urlpath + '/users/updatePicture/', true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send('data=' + JSON.stringify(data));
}
