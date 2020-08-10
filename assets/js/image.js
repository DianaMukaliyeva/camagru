// setting up video stream on different browsers
navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia ||
    navigator.mozGetUserMedia || navigator.msGetUserMedia || navigator.oGetUserMedia;

const video = document.getElementById('video');
const photoList = document.getElementById("photo_list");
const videoStreamButton = document.getElementById('video_stream');
const takePhotoButton = document.getElementById('take_photo');
const uploadImageButton = document.getElementById('upload');
const canvas = document.createElement("canvas");
const applied_filters = document.getElementById('filters');
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
            })
            .catch(function (err0r) {
                console.log("Something went wrong!");
            });
    }
    video.addEventListener('canplay', function (ev) {
        if (!streaming) {
            height = video.videoHeight / (video.videoWidth / width);

            // Some browser has a bug
            if (isNaN(height)) {
                height = width / (4 / 3);
            }
            video.setAttribute('width', width);
            video.setAttribute('height', height);
            canvas.setAttribute('width', width);
            canvas.setAttribute('height', height);
            streaming = true;
        }
    }, false);
}

// Sends taken photo with filters on server, receives combined image
const takePhoto = function () {
    let data = {};
    let filters = [];
    const imageToSend = imageUploaded ? document.getElementById('uploaded_photo') : video;
    canvas.getContext('2d').drawImage(imageToSend, 0, 0, width, height);
    data.img_data = canvas.toDataURL('image/png');
    data.width = width;
    data.height = height;
    for (let i = 0; i < applied_filters.options.length; i++)
        if (applied_filters.options[i].selected && applied_filters.options[i].value != "")
            filters.push(applied_filters.options[i].value);
    data.filters = filters;
    resetFilters();
    data.tags = document.getElementById('tags').value;
    document.getElementById('tags').value = "";

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
        resetFilters();
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
    div.innerHTML = "<img src='" + img['photo'] + "'></img>\
                    <a><i class='fas fa-times-circle'></i></a>\
                    <p>"+ tags + "</p>";
    div.childNodes[2].addEventListener('click', deleteImageContainer);
    return div;
}

const applyFilters = function () {
    let elements = camera.getElementsByTagName("img");
    let selected_filter = false;
    for (let i = elements.length - 1; i >= 0; i--) {
        if (elements[i].id != "uploaded_photo")
            elements[i].remove();
    }
    for (let i = 0; i < applied_filters.options.length; i++) {
        if (applied_filters[i].selected) {
            selected_filter = true;
            if (applied_filters[i].value != "") {
                const img = document.createElement('img');
                img.src = applied_filters[i].value;
                img.classList.add("video_overlay", "embed-responsive-item");
                camera.appendChild(img);
            }
        }
    }
}

const resetFilters = function () {
    let elements = camera.getElementsByTagName("img");
    let selected_filter = false;
    for (let i = elements.length - 1; i >= 0; i--) {
        if (elements[i].id != "uploaded_photo")
            elements[i].remove();
    }
    for (i = applied_filters.options.length - 1; i >= 0; i--) {
        applied_filters[i].selected = false;
    }
    applied_filters[0].selected = true;
}

// Upload/remove image
const toggleUploadImage = function () {
    if (imageUploaded) {
        imageUploaded = false;
        document.getElementById('uploaded_photo').remove();
        document.getElementById('upload_photo').value = '';
        uploadImageButton.value = "Upload photo";
        takePhotoButton.disabled = true;
        resetFilters();
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
applied_filters.addEventListener('change', applyFilters);
uploadImageButton.addEventListener('click', toggleUploadImage);
