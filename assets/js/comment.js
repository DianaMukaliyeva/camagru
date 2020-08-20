// Fill modal window with comments
const fillComments = function (comments, loggedUserId) {
    modalImageComments.innerHTML = "<h5 class='text-center py-2'>Comments</h5>";

    comments.forEach(comment => {
        const comment_div = document.createElement('div');
        const p = document.createElement('p');
        p.innerHTML = `
            <a href="${urlpath}/account/profile/${comment['user_id']}">${comment['login']}</a>
            (${comment['created_at']}) :
            <a role="button" onclick="deleteComment(this.dataset.dataId)" data-data-id="${comment['id']}?${comment['image_id']}?${comment['user_id']}">
                <i class='fas fa-times-circle'></i>
            </a>
            <br>
            <i>${comment['comment']}</i>`;
        p.getElementsByTagName('a')[1].classList.remove('d-none');

        if (comment['user_id'] != loggedUserId)
            p.getElementsByTagName('a')[1].classList.add('d-none');

        comment_div.appendChild(p);
        modalImageComments.appendChild(comment_div);
    });
}

// Delete comment from image
// data = commentId?imageId?userId
const deleteComment = function (data) {
    let ids = data.split('?');

    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            let result = JSON.parse(xhr.responseText);

            if (result['message']) {
                showMessage(result['message'], 'alert');
                return;
            }

            document.getElementById('comments_' + ids[1]).childNodes[1].innerHTML = ' ' + result['comments'].length;
            if (!result['user_commented']) {
                document.getElementById('comments_' + ids[1]).classList.remove('user_act');
            }
            fillComments(result['comments'], result['logged_user_id']);
        }
    };
    xhr.open('DELETE', urlpath + '/comments/delete/' + data, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
    xhr.send();
}

// Add comment to database
const addComment = function (form) {
    event.preventDefault();

    const data = {};
    data['image_id'] = form.dataset.imageId;
    data['comment'] = form.getElementsByTagName('input')[0].value;

    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            let result = JSON.parse(xhr.responseText);

            if (result['message']) {
                showMessage(result['message'], 'alert');
                return;
            }
            if (form.id && form.id == 'modal_comment_form') {
                fillComments(result['comments'], result['logged_user_id']);
            }

            form.getElementsByTagName('input')[0].value = '';
            document.getElementById('comments_' + data['image_id']).childNodes[1].innerHTML = ' ' + result['comments'].length;
            document.getElementById('comments_' + data['image_id']).classList.add('user_act');
            sendEmailAboutComment(form, data);
        }
    };
    xhr.open('POST', urlpath + '/comments/addComment', true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send('data=' + JSON.stringify(data));
}

// Send email to user about commenting his photo
const sendEmailAboutComment = function (form, data) {
    let newxhr = new XMLHttpRequest();
    newxhr.onreadystatechange = function () {
        if (newxhr.readyState == 4 && newxhr.status == 200) {
            let result = JSON.parse(newxhr.responseText);

            if (result['message']) {
                showMessage(result['message'], 'alert');
            }
        }
    };
    newxhr.open('POST', urlpath + '/comments/sendCommentEmail', true);
    newxhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    newxhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    newxhr.send('data=' + JSON.stringify(data));
}
