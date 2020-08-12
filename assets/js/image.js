if (document.getElementsByName('like')) {
    let likeIcons = document.getElementsByName('like');
    // console.log(likeIcons);
    for (let i = 0; i < likeIcons.length; i++) {
        // console.log('iterating');
        likeIcons[i].addEventListener('click', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            let imageId = this.dataset.imageId;

            let xhr = new XMLHttpRequest();
            xhr.open('GET', '/' + urlpath + '/likes/like/' + imageId, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    let result = JSON.parse(xhr.responseText);
                    if (result['message'] == 'liked') {
                        likeIcons[i].childNodes[0].classList.add('my_like');
                    } else if (result['message'] == 'unliked') {
                        likeIcons[i].childNodes[0].classList.remove('my_like');
                    } else {
                        alert(result['message']);
                        return;
                    }
                    likeIcons[i].childNodes[1].innerHTML = ' ' + result['likes_amount'];
                    if (likeIcons[i].id == 'modal_like_button') {
                        if (result['message'] == 'liked') {
                            document.getElementById('like_button_' + imageId).childNodes[0].classList.add('my_like');
                        } else if (result['message'] == 'unliked') {
                            document.getElementById('like_button_' + imageId).childNodes[0].classList.remove('my_like');
                        }
                        document.getElementById('like_button_' + imageId).childNodes[1].innerHTML = ' ' + result['likes_amount'];
                    }
                }
            };
            xhr.send();
        });
    }
}
