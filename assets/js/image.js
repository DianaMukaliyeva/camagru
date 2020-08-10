// setting up video stream on different browsers
navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia ||
    navigator.mozGetUserMedia || navigator.msGetUserMedia || navigator.oGetUserMedia;

const video = document.getElementById('video');
const photoList = document.getElementById("photo_list");
const videoStreamButton = document.getElementById('video_stream');
const takePhotoButton = document.getElementById('take_photo');
const uploadImageButton = document.getElementById('upload');
const camera = document.getElementById('video_container');

let isVideoLoaded = false;
let streaming = false;
let imageUploaded = false;
let imagesInCapture = 0;
let appliedFilters = [];

let width = 320; // We will scale the photo width to this
let height = 0; // This will be computed based on the input stream

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

// starts video stream
const startStream = function () {
    // ensure that our media-related code only works if getUserMedia is actually supported
    if (navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(function (stream) {
                video.srcObject = stream;
                isVideoLoaded = true;
                video.play();
            })
            .catch(function (error) {
                console.log("Something went wrong!" + error);
            });
    }
    video.addEventListener('canplay', function (ev) {
        if (!streaming) {
            height = video.videoHeight / (video.videoWidth / width);

            // Firefox currently has a bug where the height can't be read from
            // the video, so we will make assumptions if this happens.
            if (isNaN(height)) {
                height = width / (4 / 3);
            }
            video.setAttribute('width', width);
            video.setAttribute('height', height);
            streaming = true;
        }
    }, false);
}

// Sends taken photo with filters on server, receives combined image
const takePhoto = function () {
    let data = {};
    let filters = [];
    const canvas = document.createElement("canvas");
    const imageToSend = imageUploaded ? document.getElementById('uploaded_photo') : video;
    canvas.width = width;
    canvas.height = height;
    canvas.getContext('2d').drawImage(imageToSend, 0, 0, width, height);
    data.img_data = canvas.toDataURL('image/png');
    data.width = width;
    data.height = height;
    data.filters = appliedFilters;
    data.tags = document.getElementById('tags').value;
    document.getElementById('tags').value = "";
    canvas.remove();

    let xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            photoList.appendChild(createImageContainer(JSON.parse(this.responseText)));
            imagesInCapture++;
            changeImagesInPreview();
        }
    }
    xmlhttp.open("POST", "/" + urlpath + "/gallery/add", true);
    xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
    xmlhttp.send('data=' + JSON.stringify(data));
}

// Stop/start webcam stream
const toggleStream = function (confirmStart = true) {
    let stream = video.srcObject;
    if (stream) {
        let tracks = stream.getTracks();
        for (let i = 0; i < tracks.length; i++) {
            let track = tracks[i];
            track.stop();
        }
        video.srcObject = null;
        videoStreamButton.innerHTML = "Start video";
        takePhotoButton.disabled = true;
    } else if (video.srcObject === null && confirmStart) {
        // Check if we have uploaded image, delete it if have
        if (imageUploaded) {
            toggleUploadImage();
        }
        startStream();
        videoStreamButton.innerHTML = "Stop video";
        takePhotoButton.disabled = false;
    }
}

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
            camera.appendChild(img);
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
                    camera.insertBefore(img, camera.firstChild);
                };
                img.src = this.result;
            };
            reader.readAsDataURL(img);
        }
    }
}

startStream();
videoStreamButton.addEventListener('click', toggleStream);
takePhotoButton.addEventListener('click', takePhoto);
uploadImageButton.addEventListener('click', toggleUploadImage);
