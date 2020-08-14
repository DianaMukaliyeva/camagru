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
                        likeIcons[i].childNodes[0].classList.add('user_act');
                    } else if (result['message'] == 'unliked') {
                        likeIcons[i].childNodes[0].classList.remove('user_act');
                    } else {
                        alert(result['message']);
                        return;
                    }
                    likeIcons[i].childNodes[1].innerHTML = ' ' + result['likes_amount'];
                    if (likeIcons[i].id == 'modal_like_button') {
                        if (result['message'] == 'liked') {
                            document.getElementById('like_button_' + imageId).childNodes[0].classList.add('user_act');
                        } else if (result['message'] == 'unliked') {
                            document.getElementById('like_button_' + imageId).childNodes[0].classList.remove('user_act');
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
            // console.log('send comment');
            e.preventDefault();
            e.stopImmediatePropagation();
            const data = {};
            data['image_id'] = commentForm[i].dataset.imageId;
            data['comment'] = commentForm[i].getElementsByTagName('input')[0].value;
            let xhr = new XMLHttpRequest();
            xhr.open('POST', '/' + urlpath + '/comments/addComment', true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    let result = JSON.parse(xhr.responseText);
                    if (result['success']) {
                        document.getElementById('comments_' + data['image_id']).childNodes[1].innerHTML = ' ' + result['comments_amount'];
                        document.getElementById('comments_' + data['image_id']).classList.add('user_act');
                        commentForm[i].getElementsByTagName('input')[0].value = '';
                        // console.log('form = ' + commentForm[i].id);
                        if (commentForm[i].id && commentForm[i].id == 'modal_comment_form') {
                            // console.log('this is from modal');
                            $firstComment = result['comments_amount'] == 1 ? true : false;
                            createComment(result, document.getElementById('modal_image_comments'), $firstComment);
                        }
                    } else {
                        alert(result['message']);
                    }
                    // console.log('comment inserted');
                }
            };
            // console.log('send data 1');
            xhr.send('data=' + JSON.stringify(data));
            let newxhr = new XMLHttpRequest();
            newxhr.open('POST', '/' + urlpath + '/comments/sendCommentEmail', true);
            newxhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            newxhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
            newxhr.onreadystatechange = function () {
                if (newxhr.readyState == 4 && newxhr.status == 200) {
                    let result = JSON.parse(newxhr.responseText);
                    if (result['message']) {
                        alert(result['message']);
                    }
                    // console.log('email send');
                }
            };
            // console.log('send data 2');
            newxhr.send('data=' + JSON.stringify(data));
        });
    }
}
