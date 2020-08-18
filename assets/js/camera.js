// Older browsers might not implement mediaDevices at all, so we set an empty object first
if (navigator.mediaDevices === undefined) {
    navigator.mediaDevices = {};
}

// Some browsers partially implement mediaDevices. We can't just assign an object
// with getUserMedia as it would overwrite existing properties.
// Here, we will just add the getUserMedia property if it's missing.
if (navigator.mediaDevices.getUserMedia === undefined) {
    navigator.mediaDevices.getUserMedia = function (constraints) {

        // First get ahold of the legacy getUserMedia, if present
        var getUserMedia = navigator.webkitGetUserMedia || navigator.mozGetUserMedia;

        // Some browsers just don't implement it - return a rejected promise with an error
        // to keep a consistent interface
        if (!getUserMedia) {
            return Promise.reject(new Error('getUserMedia is not implemented in this browser'));
        }

        // Otherwise, wrap the call to the old navigator.getUserMedia with a Promise
        return new Promise(function (resolve, reject) {
            getUserMedia.call(navigator, constraints, resolve, reject);
        });
    }
}

const video = document.getElementById('video');
const photoList = document.getElementById("photo_list");
const videoStreamButton = document.getElementById('video_stream');
const takePhotoButton = document.getElementById('take_photo');

let streaming = false;
let imageUploaded = false;
let imagesInCapture = 0;

let width = 320; // We will scale the photo width to this
let height = 0; // This will be computed based on the input stream

// starts video stream
const startStream = function () {
    // ensure that our media-related code only works if getUserMedia is actually supported
    if (navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(function (stream) {
                video.srcObject = stream;
                video.play();
            })
            .catch(function (error) {
                console.log("Something went wrong with video!" + error);
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
        streaming = false;
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

// Sends taken photo with filters on server, receives combined image
const takePhoto = function () {
    let data = {};
    const canvas = document.createElement("canvas");
    const imageToSend = !streaming ? document.getElementById('uploaded_photo') : video;
    canvas.width = width;
    canvas.height = height;
    try {
        canvas.getContext('2d').drawImage(imageToSend, 0, 0, width, height);
    } catch {
        showMessage('Please, start video or upload an image', true);
        return;
    }
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
            let result = JSON.parse(xmlhttp.responseText);
            if (result['message']) {
                showMessage(result['message'], 'alert');
                if (result['message'] == 'You should be logged in') {
                    window.location.replace(urlpath);
                }
                return;
            }
            photoList.appendChild(createImageContainer(JSON.parse(this.responseText)));
            imagesInCapture++;
            changeImagesInPreview();
            showMessage('Photo added to preview window');
        }
    }
    xmlhttp.open("POST", urlpath + "/camera/combine", true);
    xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
    xmlhttp.send('data=' + JSON.stringify(data));
}

startStream();
videoStreamButton.addEventListener('click', toggleStream);
takePhotoButton.addEventListener('click', takePhoto);
