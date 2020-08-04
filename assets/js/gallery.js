let images = null;
let request_in_progress = false;

const img_container = document.getElementById('article-list');
const pagination = document.getElementById('post-pagination');
const load_more = document.getElementById('load-more');
const urlpath = window.location.pathname.split('/')[1];

function showLoadMore() {
    load_more.style.display = 'inline';
}

function hideLoadMore() {
    load_more.style.display = 'none';
}

function getPageId(n) {
    return 'article-page-' + n;
}

function addPaginationPage(page) {
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

function sortImages(title) {
    document.querySelectorAll(".sort_images").forEach(function (item) {
        item.classList.remove('active');
        if (item.dataset.title == title) {
            item.classList.add('active');
        }
    });
    let xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            images = JSON.parse(this.responseText);
            if (images != '') {
                img_container.innerHTML = '';
                pagination.innerHTML = '';
                pagination.classList.add('article-list__pagination--inactive');
                load_more.setAttribute('data-page', 0);
                loadMore();
            } else {
                pagination.innerHTML = '';
                pagination.classList.add('article-list__pagination--inactive');
                hideLoadMore();
                img_container.innerHTML = '<div class="mt-5 text-center">No images yet</div>';
            }
        }
    }
    if (title == 'newest') {
        xmlhttp.open("GET", "/" + urlpath + "/images/gallery/newest", true);
    } else {
        xmlhttp.open("GET", "/" + urlpath + "/images/gallery/popular", true);
    }
    xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xmlhttp.send();
};

function appendToDiv(div, new_html, page) {
    // Put the new HTML into a temp div
    // This causes browser to parse it as elements.
    let page_number = document.createElement('div');
    let temp = document.createElement('div');
    page_number.id = getPageId(page);
    page_number.classList.add('article-list__page', 'row', 'row-cols-1', 'row-cols-md-3', 'pt-5', 'px-md-5');
    temp.innerHTML = new_html;

    // Then we can find and work with those elements.
    // Use firstElementChild b/c of how DOM treats whitespace.
    let class_name = temp.firstElementChild.className;
    let items = temp.getElementsByClassName(class_name);

    let len = items.length;
    for (i = 0; i < len; i++) {
        page_number.appendChild(items[0]);
    }
    div.appendChild(page_number);
}

function setCurrentPage(page) {
    // console.log('Incrementing page to: ' + page);
    load_more.setAttribute('data-page', page);
}

function loadMore() {

    if (request_in_progress) {
        return;
    }

    request_in_progress = true;
    let page = parseInt(load_more.getAttribute('data-page'));
    let size = images ? images.length : 0;
    let next_page = page + 1;
    // console.log('page = ' + page + ', size = ' + size + ', next page = ' + next_page);
    if (size > page * 9) {
        addPaginationPage(next_page);
        let xhr = new XMLHttpRequest();
        xhr.open('POST', '/' + urlpath + '/images/download', true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
        // xhr.setRequestHeader('Content-Type', 'application/json')
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                let result = xhr.responseText;
                // console.log('Result: ' + result);

                setCurrentPage(next_page);
                // append results to end of blog posts
                appendToDiv(img_container, result, next_page);
                if (next_page * 9 < size)
                    showLoadMore();
                else
                    hideLoadMore();
            }
        };
        xhr.send('images=' + JSON.stringify(images.slice(page * 9, next_page * 9)));
    }
    request_in_progress = false;
}

function scrollReaction() {
    let content_height = img_container.offsetHeight;
    let current_y = window.innerHeight + window.pageYOffset;
    if (current_y >= content_height) {
        loadMore();
        pagination.classList.remove('fixed');
    } else {
        pagination.classList.add('fixed');
    }
}

window.addEventListener('DOMContentLoaded', function (event) {
    sortImages('newest');

    document.querySelectorAll(".sort_images").forEach(function (item) {
        item.addEventListener('click', function () {
            sortImages(item.dataset.title);
        });
    });
    load_more.addEventListener("click", loadMore);
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
