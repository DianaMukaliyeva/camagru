let images = null;
let request_in_progress = false;
let imagesOnPage = 9;

const imgContainer = document.getElementById('article-list');
const pagination = document.getElementById('post-pagination');
const loadMoreButton = document.getElementById('load-more');

const getPageId = function (n) { return 'article-page-' + n; }
const showLoadMore = function () { loadMoreButton.classList.remove('d-none'); }
const hideLoadMore = function () { loadMoreButton.classList.add('d-none'); }
const setCurrentPage = function (page) { loadMoreButton.setAttribute('data-page', page); }

// Add next page to pagination
const addPaginationPage = function (page) {
    const pageLink = document.createElement('a');
    pageLink.href = '#' + getPageId(page);
    pageLink.innerHTML = page;

    const listItem = document.createElement('li');
    listItem.className = 'article-list__pagination__item';
    listItem.appendChild(pageLink);

    pagination.appendChild(listItem);

    if (page === 2) {
        pagination.classList.remove('article-list__pagination--inactive');
    }
}

// Add given images into container
const appendToDiv = function (div, new_html, page) {
    const page_number = document.createElement('div');
    page_number.id = getPageId(page);
    page_number.classList.add('article-list__page', 'row', 'row-cols-1', 'row-cols-md-3', 'pt-5');

    const temp = document.createElement('div');
    temp.innerHTML = new_html;

    const class_name = temp.firstElementChild.className;
    const items = temp.getElementsByClassName(class_name);

    const len = items.length;
    for (i = 0; i < len; i++) {
        page_number.appendChild(items[0]);
    }
    div.appendChild(page_number);
}

// Send request to server to get list of images sorted by sorting parameter
const getSortedImages = function (sorting) {
    document.querySelectorAll(".sort_images").forEach(function (item) {
        item.classList.remove('active');
        if (item.dataset.title == sorting) {
            item.classList.add('active');
        }
    });

    let xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            images = JSON.parse(this.responseText);
            imgContainer.innerHTML = '';
            pagination.innerHTML = '';
            pagination.classList.add('article-list__pagination--inactive');
            loadMoreButton.setAttribute('data-page', 0);
            hideLoadMore();
            if (images != '') {
                loadMore();
            } else {
                imgContainer.innerHTML = '<div class="mt-5 text-center">No images yet</div>';
            }
        }
    }
    xmlhttp.open("GET", urlpath + "/images/getImages/" + sorting, true);
    xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xmlhttp.send();
};

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
        addPaginationPage(next_page);

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
        xhr.open('POST', urlpath + '/images/gallery', true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send('images=' + JSON.stringify(images.slice(page * imagesOnPage, next_page * imagesOnPage)));
    }
    request_in_progress = false;
}

// show next page on scroll
const scrollReaction = function () {
    let content_height = imgContainer.offsetHeight;
    let current_y = window.innerHeight + window.pageYOffset;
    // console.log('content heigh: ' + content_height);
    // console.log('current_y: ' + current_y);
    if (current_y >= content_height || images.length <= imagesOnPage) {
        loadMore();
    }
    pagination.classList.add('fixed');
    if (current_y >= content_height &&
        images.length <= imagesOnPage * parseInt(loadMoreButton.getAttribute('data-page'))) {
        pagination.classList.remove('fixed');
    }
}

window.addEventListener('DOMContentLoaded', function (event) {
    getSortedImages('newest');

    loadMoreButton.addEventListener('click', loadMore);
    hideLoadMore();
});

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
    imagesOnPage = 24;
}
