(function () {
    let burger = document.querySelector('.navbar-toggler');
    let nav = document.querySelector(burger.dataset.target);

    burger.addEventListener('click', function () { nav.classList.toggle('collapse'); })
})();

// var loginModalButton = document.querySelector('#loginModalButton');
// var login = document.querySelector('#' + loginModalButton.dataset.target);

// loginModalButton.addEventListener('click', function () { login.classList.toggle('modal'); })
/*
let images = null;

const container = document.getElementById('article-list');
const articleListPagination = document.getElementById('post-pagination');
const load_more = document.getElementById('load-more');
let request_in_progress = false;


function showLoadMore() {
    load_more.style.display = 'inline';
}

function hideLoadMore() {
    load_more.style.display = 'none';
}

function addPaginationPage(page) {
    const pageLink = document.createElement('a');
    pageLink.href = '#' + getPageId(page);
    pageLink.innerHTML = page;

    const listItem = document.createElement('li');
    listItem.className = 'article-list__pagination__item';
    listItem.appendChild(pageLink);

    articleListPagination.appendChild(listItem);

    if (page === 2) {
        articleListPagination.classList.remove('article-list__pagination--inactive');
    }
}

// function getArticle() {
//     const div_col = document.createElement('div');
//     div_col.classList.add('col', 'mb-4');
//     const div_card = document.createElement('div');
//     div_card.classList.add('card', 'h-100', 'bg-light');
//     const div_media = document.createElement('div');
//     div_media.classList.add('media', 'mt-3');
//     const img_logo = document.createElement('img');
//     img_logo.classList.add('media-img', 'rounded-circle', 'mx-3');
//     img_logo.setAttribute('alt', 'profile image');
//     img_logo.setAttribute('src', 'http://localhost:8080/camagru_mine/assets/img/images/default.png');
//     const div_media_body = document.createElement('div');
//     div_media_body.classList.add('media-body');
//     const h5 = document.createElement('h5');
//     h5.classList.add('pt-3');
//     h5.innerHTML = 'Login';
//     div_media_body.appendChild(h5);
//     div_media.appendChild(img_logo);
//     div_media.appendChild(div_media_body);
//     div_card.appendChild(div_media);
//     div_col.appendChild(div_card);
//     return div_col;

//     const articleImage = getArticleImage();
//     const article = document.createElement('article');
//     article.className = 'article-list__item';
//     article.appendChild(articleImage);

//     return article;
// }

// function getDocumentHeight() {
//     const body = document.body;
//     const html = document.documentElement;

//     return Math.max(
//         body.scrollHeight, body.offsetHeight,
//         html.clientHeight, html.scrollHeight, html.offsetHeight
//     );
// };

// function getScrollTop() {
//     return (window.pageYOffset !== undefined) ? window.pageYOffset : (document.documentElement || document.body.parentNode || document.body).scrollTop;
// }

function getPageId(n) {
    return 'article-page-' + n;
}

// function getArticleImage() {
//     const hash = Math.floor(Math.random() * Number.MAX_SAFE_INTEGER);
//     const image = new Image;
//     image.className = 'article-list__item__image article-list__item__image--loading';
//     image.src = 'http://api.adorable.io/avatars/250/' + hash;

//     image.onload = function () {
//         image.classList.remove('article-list__item__image--loading');
//     };

//     return image;
// }

// function getArticlePage(page, articlesPerPage = 6) {
//     const pageElement = document.createElement('div');
//     pageElement.id = getPageId(page);
//     pageElement.className = 'article-list__page';

//     while (articlesPerPage--) {
//         pageElement.appendChild(getArticle());
//     }

//     return pageElement;
// }

// function fetchPage(page) {
//     articleList.appendChild(getArticlePage(page));
// }

// function addPage(page) {
//     if (page <= 3) {
//         fetchPage(page);
//         addPaginationPage(page);
//     }
// }

function sortImages(title) {
    document.querySelectorAll(".sort_images").forEach(function (item) {
        item.classList.remove('active');
        if (item.dataset.title == title) {
            item.classList.add('active');
        }
    });
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            // parser = new DOMParser();
            // doc = parser.parseFromString(this.responseText, "text/html");
            // var elements = doc.querySelectorAll( 'body *' );
            // let element = document.getElementById("txtHint");
            // for (el of elements) {
            //     element.parentNode.insertBefore(el, element);
            // }
            images = JSON.parse(this.responseText);
            // console.log('images before sending' + images);
            // for (image of images) {
            //     console.log(image);
            // }
            if (images) {
                container.innerHTML = '';
                articleListPagination.innerHTML = '';
                articleListPagination.classList.add('article-list__pagination--inactive');
                load_more.setAttribute('data-page', 0);
                loadMore();
            } else {
                articleListPagination.innerHTML = '';
                articleListPagination.classList.add('article-list__pagination--inactive');
                hideLoadMore();
                // console.log('here');
                container.innerHTML = '<div class="mx-auto text-center">No images yet</div>';
            }
            // console.log(window.innerHeight + ' y: ' + window.pageYOffset + ' doc ' + document.body.scrollHeight);
            // console.log(JSON.parse(this.responseText));
            // var content_height = container.offsetHeight;
            // var current_y = window.innerHeight + window.pageYOffset;
            // console.log('in button ' + current_y + '/' + content_height);
        }
    }
    if (title == 'newest') {
        xmlhttp.open("GET", "images/gallery/newest", true);
    } else {
        xmlhttp.open("GET", "images/gallery/popular", true);
    }
    xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xmlhttp.send();
};

function appendToDiv(div, new_html) {
    // Put the new HTML into a temp div
    // This causes browser to parse it as elements.
    var temp = document.createElement('div');
    temp.innerHTML = new_html;

    // Then we can find and work with those elements.
    // Use firstElementChild b/c of how DOM treats whitespace.
    var class_name = temp.firstElementChild.className;
    var items = temp.getElementsByClassName(class_name);

    var len = items.length;
    for (i = 0; i < len; i++) {
        div.appendChild(items[0]);
    }
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

    var page = parseInt(load_more.getAttribute('data-page'));
    var size = images ? images.length : 0;
    var next_page = page + 1;
    // console.log('page = ' + page + ', size = ' + size + ', next page = ' + next_page);
    if (size > page * 9) {
        addPaginationPage(next_page);
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'images/download', true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
        // xhr.setRequestHeader('Content-Type', 'application/json')
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var result = xhr.responseText;
                // console.log('Result: ' + result);

                setCurrentPage(next_page);
                // append results to end of blog posts
                appendToDiv(container, result);
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
    var content_height = container.offsetHeight;
    var current_y = window.innerHeight + window.pageYOffset;
    // console.log(current_y + '/' + content_height);
    if (current_y >= content_height) {
        loadMore();
        articleListPagination.classList.remove('fixed');
    } else {
        articleListPagination.classList.add('fixed');
    }
}

document.querySelectorAll(".sort_images").forEach(function (item) {
    item.addEventListener('click', function () {
        sortImages(item.dataset.title);
    });
});

window.addEventListener('load', (event) => {
    sortImages('newest');
});

load_more.addEventListener("click", loadMore);

window.onscroll = function () {
    scrollReaction();
}

hideLoadMore();

// let page = 0;

// addPage(++page);

// window.onscroll = function () {
//     if (getScrollTop() < getDocumentHeight() - window.innerHeight) return;
//     addPage(++page);
// };
*/