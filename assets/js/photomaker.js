const videoContainer = document.getElementById('video_container');
const uploadImageButton = document.getElementById('upload');
let appliedFilters = [];

// show how many Images in preview
const changeImagesInPreview = function () {
    document.getElementById('images_header').innerHTML = "Preview (" + imagesInCapture + ")";
    document.getElementById('display_list').style.display = imagesInCapture == 0 ? "none" : "block";
}

// delete Image from preview
const deleteImageContainer = function (div) {
    this.parentElement.remove();
    imagesInCapture--;
    changeImagesInPreview();
}

// delete all images in preview
const deletePreview = function () {
    while (photoList.firstChild) {
        photoList.removeChild(photoList.firstChild);
        imagesInCapture--;
    }
    changeImagesInPreview();
}

// Send images to server and save them
const saveImages = function () {
    let imagesTab = {};
    let images = photoList.getElementsByTagName('img');
    let tags = photoList.getElementsByTagName('p');
    let j = 0;
    for (let i = 0; i < images.length; i++) {
        let imageInfo = {};
        imageInfo.src = images[i].src;
        imagesTab[j] = imageInfo;
        j++;
    }
    j = 0;
    for (i = 0; i < tags.length; i++) {
        imagesTab[j].description = tags[i].innerHTML;
        j++;
    }
    console.log(imagesTab);
    console.log(images);
    console.log(tags);
};

// Create container for image in preview
const createImageContainer = function (img) {
    let div = document.createElement("div");
    div.classList.add("mx-3", "delete_img");
    let tags = '';
    img['tags'].forEach(element => {
        tags += "#" + element;
    });
    tags = tags == '' ? 'No tags' : tags;
    div.innerHTML = "<img class='border' src='" + img['photo'] + "'></img>\
                    <a><i class='fas fa-times-circle'></i></a>\
                    <p>"+ tags + "</p>";
    div.childNodes[2].addEventListener('click', deleteImageContainer);
    return div;
}

// Add/delete filter
const toggleFilter = function (id) {
    if (id == 'filter_0') {
        appliedFilters = [];
        let filters = document.getElementById('filters').getElementsByTagName('input');
        for (var i = 0; i < filters.length; i++) {
            if (filters[i].type == 'checkbox') {
                if (document.getElementById('applied_' + filters[i].id))
                    document.getElementById('applied_' + filters[i].id).remove();
                filters[i].checked = false;
            }
        }
        filters[0].checked = true;
    } else {
        document.getElementById('filter_0').checked = false;
        let filter = document.getElementById(id);
        if (appliedFilters.includes(filter.dataset.path)) {
            appliedFilters = appliedFilters.filter(item => item !== filter.dataset.path);
            document.getElementById('applied_' + id).remove();
        } else {
            const img = document.createElement('img');
            img.id = "applied_" + id;
            img.src = filter.dataset.path;
            img.classList.add("video_overlay", "embed-responsive-item");
            videoContainer.appendChild(img);
            appliedFilters.push(filter.dataset.path);
        }
    }
}

// Upload/remove image
const toggleUploadImage = function () {
    if (imageUploaded) {
        imageUploaded = false;
        document.getElementById('uploaded_photo').remove();
        document.getElementById('upload_photo').value = '';
        uploadImageButton.value = "Upload photo";
        takePhotoButton.disabled = true;
    } else {
        document.getElementById('upload_photo').click();
        document.getElementById('upload_photo').onchange = function () {
            imageUploaded = true;
            toggleStream(false);
            uploadImageButton.value = "Delete photo";
            takePhotoButton.disabled = false;
            const img = document.getElementById('upload_photo').files[0];
            const reader = new FileReader();
            reader.onload = function () {
                const img = new Image();
                img.onload = function () {
                    const img = document.createElement('img');
                    img.src = this.src;
                    img.classList.add("video_overlay", "embed-responsive-item");
                    img.id = "uploaded_photo";
                    videoContainer.insertBefore(img, videoContainer.firstChild);
                };
                img.src = this.result;
            };
            reader.readAsDataURL(img);
        }
    }
}

uploadImageButton.addEventListener('click', toggleUploadImage);