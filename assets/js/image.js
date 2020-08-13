if (document.getElementsByName('like')) {
    let likeIcons = document.getElementsByName('like');
    for (let i = 0; i < likeIcons.length; i++) {
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

if (document.getElementsByName('send_comment')) {
    let commentForm = document.getElementsByName('send_comment');
    for (let i = 0; i < commentForm.length; i++) {
        commentForm[i].addEventListener('submit', function (e) {
            e.preventDefault();
            const data = {};
            data['image_id'] = commentForm[i].dataset.imageId;
            data['comment'] = commentForm[i].getElementsByTagName('input')[0].value;
            let xhr = new XMLHttpRequest();
            xhr.open('POST', '/' + urlpath + '/images/addComment', true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    let result = JSON.parse(xhr.responseText);
                    if (result['success']) {
                        document.getElementById('comments_' + data['image_id']).childNodes[1].innerHTML = ' ' + result['comments_amount'];
                        document.getElementById('comments_' + data['image_id']).classList.add('my_like');
                        commentForm[i].getElementsByTagName('input')[0].value = '';
                        if (commentForm[i].id && commentForm[i].id == 'modal_comment_form') {
                            $firstComment = result['comments_amount'] == 1 ? true : false;
                            createComment(result, document.getElementById('modal_image_comments'), $firstComment);
                        }
                    } else {
                        alert(result['message']);
                    }
                }
            };
            xhr.send('data=' + JSON.stringify(data));
        });
    }
}
