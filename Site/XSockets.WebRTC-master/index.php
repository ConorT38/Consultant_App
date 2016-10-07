<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Grayscale - Start Bootstrap Theme</title>

    <!-- Bootstrap Core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">

    <!-- Theme CSS -->
    <link href="css/grayscale.min.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
<body id="page-top" data-spy="scroll" data-target=".navbar-fixed-top">
    <!-- Intro Header -->
    <header class="intro">
        <div class="intro-body">
            <div class="container">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <h1 class="brand-heading">Grayscale</h1>
                        <p class="intro-text">A free, responsive, one page Bootstrap theme.
                            <br>Created by Start Bootstrap.</p> <div class="localvideo">
        <video autoplay></video>
    </div>

    <h2>Remote videos</h2>
    <div class="remotevideos"><div id="immediate"></div>
                        <a href="#about" class="btn btn-circle page-scroll">
                            <i class="fa fa-angle-double-down animated"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

   

    <!-- jQuery -->	
	    <script src="lib/XSockets.latest.js"></script>
    <script src="xsockets/js/XSockets.WebRTC.latest.js"></script>
    <script>
        var $ = function (selector, el) {
            if (!el) el = document;
            return el.querySelector(selector);
        }
        var trace = function (what, obj) {
            var pre = document.createElement("pre");
            pre.textContent = JSON.stringify(what) + " - " + JSON.stringify(obj || "");
            $("#immediate").appendChild(pre);
        };
        var main = (function () {
            var broker;
            var rtc;
            trace("Ready");
            trace("Try connect the connectionBroker");
            var ws = new XSockets.WebSocket("wss://rtcplaygrouund.azurewebsites.net:443", ["connectionbroker"], {
                ctx: '23fbc61c-541a-4c0d-b46e-1a1f6473720a'
            });
            var onError = function (err) {
                trace("error", arguments);
            };
            var recordMediaStream = function (stream) {
                if ("MediaRecorder" in window === false) {
                    trace("Recorder not started MediaRecorder not available in this browser. ");
                    return;
                }
                var recorder = new XSockets.MediaRecorder(stream);
                recorder.start();
                trace("Recorder started.. ");
                recorder.oncompleted = function (blob, blobUrl) {
                    trace("Recorder completed.. ");
                    var li = document.createElement("li");
                    var download = document.createElement("a");
                    download.textContent = new Date();
                    download.setAttribute("download", XSockets.Utils.randomString(8) + ".webm");
                    download.setAttribute("href", blobUrl);
                    li.appendChild(download);
                    $("ul").appendChild(li);
                };
            };
            var addRemoteVideo = function (peerId, mediaStream) {
                var remoteVideo = document.createElement("video");
                remoteVideo.setAttribute("autoplay", "autoplay");
                remoteVideo.setAttribute("rel", peerId);
                attachMediaStream(remoteVideo, mediaStream);
                $(".remotevideos").appendChild(remoteVideo);
            };
            var onConnectionLost = function (remotePeer) {
                trace("onconnectionlost", arguments);
                var peerId = remotePeer.PeerId;
                var videoToRemove = $("video[rel='" + peerId + "']");
                $(".remotevideos").removeChild(videoToRemove);
            };
            var oncConnectionCreated = function () {
                console.log(arguments, rtc);
                trace("oncconnectioncreated", arguments);
            };
            var onGetUerMedia = function (stream) {
                trace("Successfully got some userMedia , hopefully a goat will appear..");
                rtc.connectToContext(); // connect to the current context?
            };
            var onRemoteStream = function (remotePeer) {
                addRemoteVideo(remotePeer.PeerId, remotePeer.stream);
                trace("Opps, we got a remote stream. lets see if its a goat..");
            };
            var onLocalStream = function (mediaStream) {
                trace("Got a localStream", mediaStream.id);
                attachMediaStream($(".localvideo video "), mediaStream);
                // if user click, video , call the recorder
                $(".localvideo video ").addEventListener("click", function () {
                    recordMediaStream(rtc.getLocalStreams()[0]);
                });
            };
            var onContextCreated = function (ctx) {
                trace("RTC object created, and a context is created - ", ctx);
                rtc.getUserMedia(rtc.userMediaConstraints.hd(false), onGetUerMedia, onError);
            };
            var onOpen = function () {
                trace("Connected to the brokerController - 'connectionBroker'");
                rtc = new XSockets.WebRTC(this);
                rtc.onlocalstream = onLocalStream;
                rtc.oncontextcreated = onContextCreated;
                rtc.onconnectioncreated = oncConnectionCreated;
                rtc.onconnectionlost = onConnectionLost;
                rtc.onremotestream = onRemoteStream;
                rtc.onanswer = function (event) {
                };
                rtc.onoffer = function (event) {
                };
            };
            var onConnected = function () {
                trace("connection to the 'broker' server is established");
                trace("Try get the broker controller form server..");
                broker = ws.controller("connectionbroker");
                broker.onopen = onOpen;
            };
            ws.onconnected = onConnected;
        });
        document.addEventListener("DOMContentLoaded", main);
    </script>

</body>
</html>