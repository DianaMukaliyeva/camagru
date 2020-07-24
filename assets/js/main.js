(function () {
    var burger = document.querySelector('.navbar-toggler');
    var nav = document.querySelector(burger.dataset.target);

    burger.addEventListener('click', function () { nav.classList.toggle('collapse'); })
})();
// var loginModalButton = document.querySelector('#loginModalButton');
// var login = document.querySelector('#' + loginModalButton.dataset.target);

// loginModalButton.addEventListener('click', function () { login.classList.toggle('modal'); })

function sortImages(e) {
    const targetLink = e.target.dataset.title;
    document.querySelectorAll(".sort_images").forEach(function (item) {
        item.classList.remove('active');
        if (item.dataset.title == targetLink) {
            item.classList.add('active');
        }
    });
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("txtHint").innerHTML = this.responseText;
        }
    }
    if (targetLink == 'newest') {
        xmlhttp.open("GET", "images/gallery/newest", true);
    } else {
        xmlhttp.open("GET", "images/gallery/popular", true);
    }
    xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xmlhttp.send();
};

document.querySelectorAll(".sort_images").forEach(function (item) {
    item.addEventListener('click', sortImages);
    if (item.dataset.title == 'newest') {
        item.click();
    }
});