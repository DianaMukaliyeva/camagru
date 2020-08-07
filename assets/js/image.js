
const urlpath = window.location.pathname.split('/')[1];
navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia ||
    navigator.mozGetUserMedia || navigator.msGetUserMedia || navigator.oGetUserMedia;

const video = document.getElementById('video');
const photoList = document.getElementById("photo_list");
const videoStreamButton = document.getElementById('video_stream');
const takePhotoButton = document.getElementById('take_photo');
const uploadImageButton = document.getElementById('upload_photo');
const canvas = document.createElement("canvas");
const applied_filters = document.getElementById('filters');

let isVideoLoaded = false;
let streaming = false;
let imagesInCapture = 0;

let width = 320; // We will scale the photo width to this
let height = 0; // This will be computed based on the input stream

const deleteImageContainer = function (div) { this.parentElement.remove(); }

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

const takePhoto = function () {
    // var applied_filters = document.getElementsByName('filters[]');
    const uploadedImage = document.getElementById('uploaded_photo');

    let data = {};
    let filters = [];
    canvas.width = width;
    canvas.height = height;
    data.width = width;
    data.height = height;
    if (isVideoLoaded || uploadedImage) {
        if (uploadedImage) {
            canvas.getContext('2d').drawImage(uploadedImage, 0, 0, canvas.width, canvas.height);
            data.img_data = canvas.toDataURL('image/png');
        } else {
            canvas.getContext('2d').drawImage(video, 0, 0, width, height);
            data.img_data = canvas.toDataURL('image/png');
        }
        for (let i = 0; i < applied_filters.options.length; i++)
            if (applied_filters.options[i].selected && applied_filters.options[i].value != "")
                filters.push(applied_filters.options[i].value);
        data.filters = filters;
        data.tags = document.getElementById('tags').value;
        document.getElementById('tags').value = "";
        resetFilters();
        canvas.remove();
        video.style.opacity = 1;
        // console.log(data);
        let xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                photoList.appendChild(createImageContainer(JSON.parse(this.responseText)));
                document.getElementById('display_list').style.display = "block";
                imagesInCapture++;
                document.getElementById('images_header').innerHTML = "Preview (" + imagesInCapture + ")";
                // console.log(this.responseText + ' response');
            }
        }
        xmlhttp.open("POST", "/" + urlpath + "/gallery/add", true);
        xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
        xmlhttp.send('data=' + JSON.stringify(data));
    }
}

// stop or start webcam stream
const toggleStream = function (start = true) {
    const uploadedImage = document.getElementById('uploaded_photo');
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
    } else {
        if (uploadedImage) {
            uploadedImage.remove();
        }
        if (video.srcObject === null && start) {
            startStream();
            videoStreamButton.innerHTML = "Stop video";
            takePhotoButton.disabled = false;
        }
    }
}

const createImageContainer = function (img) {
    let div = document.createElement("div");
    div.classList.add("mx-3");
    let tags = '';
    img['tags'].forEach(element => {
        tags += "#" + element + " ";
    });
    if (tags == '')
        tags = 'No tags';
    div.innerHTML = "<img src='" + img['photo'] + "'></img>\
                    <a class='delete'></a>\
                    <p>"+ tags + "</p>";
    div.childNodes[2].addEventListener('click', deleteImageContainer);
    return div;
}

const applyFilters = function () {
    const inner_container = document.getElementById('video_container');
    let elements = inner_container.getElementsByTagName("img");
    let selected_filter = false;
    for (let i = elements.length - 1; i >= 0; i--) {
        if (elements[i].id != "uploaded_photo")
            elements[i].remove();
    }
    for (i = applied_filters.options.length - 1; i >= 0; i--) {
        if (applied_filters[i].selected) {
            selected_filter = true;
            if (applied_filters[i].value != "") {
                const img = document.createElement('img');
                img.src = applied_filters[i].value;
                img.classList.add("video_overlay");
                inner_container.insertBefore(img, inner_container.firstChild);
            }
        }
    }
}

const resetFilters = function () {
    const inner_container = document.getElementById('video_container');
    let elements = inner_container.getElementsByTagName("img");
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

const createUploadedImage = function () {
    const img = new Image();
    const uploadedImage = document.getElementById('uploaded_photo');

    if (!isVideoLoaded) {
        video.style.width = "680px";
        video.style.height = "480px";
    }
    img.onload = function () {
        // console.log('here');
        if (uploadedImage)
            uploadedImage.remove();
        const img = document.createElement('img');
        // console.log("this.src" + this.src);
        img.src = this.src;
        img.classList.add("video_overlay");
        img.style.zIndex = 1;
        img.classList.add("embed-responsive-item");
        img.id = "uploaded_photo";
        const camera = document.getElementById('video_container');
        // console.log("camera div: " + document.getElementById('video_container'));
        // console.log("img" + img);
        camera.insertBefore(img, camera.firstChild);
    };
    img.src = this.result;
    // console.log('img src') + img.src;
}

const uploadImage = function () {
    // console.log('here');
    const img = uploadImageButton.files[0];
    const reader = new FileReader();
    reader.onload = createUploadedImage;
    // console.log(uploadImageButton.files[0]);
    reader.readAsDataURL(img);
    toggleStream(false);
    takePhotoButton.disabled = false;
    video.style.opacity = 0;
}

startStream();
videoStreamButton.addEventListener('click', toggleStream);
takePhotoButton.addEventListener('click', takePhoto);
applied_filters.addEventListener('change', applyFilters);
uploadImageButton.addEventListener('change', uploadImage);
