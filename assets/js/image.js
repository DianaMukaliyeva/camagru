let isVideoLoaded = false;
let imagesInCapture = 0;

const urlpath = window.location.pathname.split('/')[1];
navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia ||
    navigator.mozGetUserMedia || navigator.msGetUserMedia || navigator.oGetUserMedia;

const video = document.getElementById('video');
const photo_list = document.getElementById("photo_list");
const video_stream_button = document.getElementById('video_stream');
const takePhotoButton = document.getElementById('take_photo');

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
}

const takePhoto = function () {
    let applied_filters = document.getElementById('filters');
    let data = {};
    let filters = [];
    canvas = document.createElement("canvas");
    canvas.width = 270;
    canvas.height = 200;
    data.width = 270;
    data.height = 200;
    if (isVideoLoaded || document.getElementById('uploaded_photo')) {
        // if (document.getElementById('uploaded_photo') && isVideoLoaded) {
        //     var uploaded_photo = document.getElementById('uploaded_photo');
        //     canvas.getContext('2d').drawImage(uploaded_photo, 0, 0, canvas.width, canvas.height);
        //     uploaded_photo.remove();
        // } else if (!isVideoLoaded && document.getElementById('inner_container').getElementsByTagName('img')) {
        //     var uploaded_photo = document.getElementById('inner_container').getElementsByTagName('img')[0];
        //     canvas.getContext('2d').drawImage(uploaded_photo, 0, 0, canvas.width, canvas.height);
        //     uploaded_photo.remove();
        // }
        // else
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
        data.img_data = canvas.toDataURL('image/png');
        for (var i = 0; i < applied_filters.options.length; i++)
            if (applied_filters.options[i].selected && applied_filters.options[i].value != "")
                filters.push(applied_filters.options[i].value);
        data.filters = filters;
        // data.description = document.getElementById('description').value;
        canvas.remove();
        document.getElementById('video').style.opacity = 1;
        // console.log(data);
        let xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                photo_list.appendChild(createImageContainer(JSON.parse(this.responseText)));
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
const toggleStream = function () {
    let stream = video.srcObject;
    if (stream) {
        let tracks = stream.getTracks();
        for (let i = 0; i < tracks.length; i++) {
            let track = tracks[i];
            track.stop();
        }
        video.srcObject = null;
        video_stream_button.innerHTML = "Start video";
        takePhotoButton.disabled = true;
    } else {
        if (video.srcObject === null) {
            startStream();
        }
        video_stream_button.innerHTML = "Stop video";
        takePhotoButton.disabled = false;
    }
}

const createImageContainer = function (img) {
    var div = document.createElement("div");
    div.innerHTML = "<img src='" + img['photo'] + "'></img>\
                    <a class='delete'></a>\
                    <p>"+ img['description'] + "</p>";
    div.childNodes[2].addEventListener('click', deleteImageContainer);
    return div;
}

startStream();
video_stream_button.addEventListener('click', toggleStream);
takePhotoButton.addEventListener('click', takePhoto);
