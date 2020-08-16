let images = null;
let request_in_progress = false;
let imagesOnPage = 9;

const imgContainer = document.getElementById('image-list');
const loadMoreButton = document.getElementById('load-more-image');
const setCurrentPage = function (page) { loadMoreButton.setAttribute('data-page', page); }
const getPageId = function (n) { return 'article-page-' + n; }

const showLoadMore = function () { loadMoreButton.classList.remove('d-none'); }
const hideLoadMore = function () { loadMoreButton.classList.add('d-none'); }

const getUsersImages = function (userId) {
    // console.log(userId);
    let xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            images = JSON.parse(this.responseText);
            // console.log(images)
            imgContainer.innerHTML = '';
            loadMoreButton.setAttribute('data-page', 0);
            hideLoadMore();
            if (images != '') {
                loadMore();
            } else {
                imgContainer.innerHTML = '<div class="mt-5 text-center">No images yet</div>';
            }
        }
    }
    xmlhttp.open("GET", urlpath + "/images/userGallery/" + userId, true);
    xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xmlhttp.send();
}

// Add given images into container
const appendToDiv = function (div, new_html, page) {
    const page_number = document.createElement('div');
    const temp = document.createElement('div');
    page_number.id = getPageId(page);
    page_number.classList.add('row', 'row-cols-1', 'row-cols-md-3', 'pt-5');
    temp.innerHTML = new_html;

    const class_name = temp.firstElementChild.className;
    const items = temp.getElementsByClassName(class_name);

    const len = items.length;
    for (i = 0; i < len; i++) {
        page_number.appendChild(items[0]);
    }
    div.appendChild(page_number);
}

// send to server and receive part of images for the next page
const loadMore = function () {

    if (request_in_progress) {
        return;
    }

    request_in_progress = true;
    let page = parseInt(loadMoreButton.getAttribute('data-page'));
    let size = images ? images.length : 0;
    let next_page = page + 1;
    // console.log('page = ' + page + ', size = ' + size + ', next page = ' + next_page);
    if (size > page * imagesOnPage) {
        let xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                let result = xhr.responseText;
                // console.log('Result: ' + result);

                setCurrentPage(next_page);
                // append results to the end of blog posts
                appendToDiv(imgContainer, result, next_page);
                if (next_page * imagesOnPage < size)
                    showLoadMore();
                else
                    hideLoadMore();
            }
        };
        xhr.open('POST', urlpath + '/images/loadUsersImage', true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send('images=' + JSON.stringify(images.slice(page * imagesOnPage, next_page * imagesOnPage)));
    }
    request_in_progress = false;
}


window.addEventListener('DOMContentLoaded', function (event) {
    if (document.getElementById('profile_login')) {
        let profileId = document.getElementById('profile_login').dataset.userId;
        getUsersImages(profileId);
        loadMoreButton.addEventListener('click', loadMore);
        hideLoadMore();
    }
});


// show next page on scroll
const scrollReaction = function () {
    let content_height = imgContainer.offsetHeight;
    let current_y = window.innerHeight + window.pageYOffset;
    // console.log('content heigh: ' + content_height);
    // console.log('current_y: ' + current_y);
    if (current_y >= content_height || images.length <= imagesOnPage) {
        loadMore();
    }
}

// for not triggering scroll twice
let timeout;

window.onscroll = function () {
    clearTimeout(timeout);
    timeout = setTimeout(function () {
        scrollReaction();
    }, 50);
}

let windowHeight = window.innerHeight + window.pageYOffset;

// calculate how many images show on the page
if (windowHeight < 1200) {
    imagesOnPage = 6;
} else if (windowHeight < 1800) {
    imagesOnPage = 9;
} else if (windowHeight < 2500) {
    imagesOnPage = 12;
} else {
    imagesOnPage = 20;
}

