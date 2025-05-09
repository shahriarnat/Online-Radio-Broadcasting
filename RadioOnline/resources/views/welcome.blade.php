<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Stream Music Player</title>
    <style>
        body {
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-image: url('./bg.jpg');
            color: white;
            font-family: Arial, sans-serif;
            flex-direction: column;
            text-align: center;
            background-repeat: no-repeat;
            background-size: cover;
            backdrop-filter: blur(10em);

            /* Add animation */
            background-position: center;
            animation: moveBackground 3s infinite linear alternate;
        }

        /* Keyframes to animate the background */
        @keyframes moveBackground {
            0% {
                background-position: 0% 0%;
            }
            25% {
                background-position: 20% 10%;
            }
            50% {
                background-position: 40% 20%;
            }
            75% {
                background-position: 60% 30%;
            }
            100% {
                background-position: 80% 40%;
            }
        }
        canvas {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100%;
            height: 50%;
        }
        .player-container {
            position: relative;
            z-index: 1;
            background: rgba(0, 0, 0, 0.5);
            padding: 20px;
            border-radius: 10px;
        }
        audio::-webkit-media-controls-timeline,
        audio::-webkit-media-controls-seek-back-button,
        audio::-webkit-media-controls-seek-forward-button {
            display: none;
        }

    </style>
</head>
<body>
<canvas id="waveform"></canvas>
<div class="player-container">
    <h2>Live Stream Music Player</h2>
    <audio id="audio" autoplay controls controlslist="play volume" ></audio>
    <div id="metadata" style="color:#9b9b9b;text-shadow: 0px 0px 20px #ddd;">Loading metadata...</div>
</div>
<script>
    const channel_name = "music.mp3";
    const audio = document.getElementById("audio");
    const metadataDiv = document.getElementById("metadata");
    const canvas = document.getElementById("waveform");
    const ctx = canvas.getContext("2d");

    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight * 0.5;

    let audioCtx, analyser, source;
    let dataArray, bufferLength;

    function setupAudio() {
        if (!audioCtx) {
            audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            analyser = audioCtx.createAnalyser();
            analyser.fftSize = 1024;
            bufferLength = analyser.frequencyBinCount;
            dataArray = new Uint8Array(bufferLength);

            source = audioCtx.createMediaElementSource(audio);
            source.connect(analyser);
            analyser.connect(audioCtx.destination);

            drawWaveform();
        }
    }

    function getColorBasedOnVolume(volume) {
        const red = Math.min(255, volume * 5);
        const green = Math.max(50, 255 - volume * 4);
        const blue = Math.min(255, 100 + volume * 3);
        return `rgb(${red}, ${green}, ${blue})`;
    }

    function drawWaveform() {
        requestAnimationFrame(drawWaveform);
        analyser.getByteFrequencyData(dataArray);
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = "rgba(0, 0, 0, 0.2)";
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.lineWidth = 2;

        let avgVolume = dataArray.reduce((sum, value) => sum + value, 0) / bufferLength;
        ctx.strokeStyle = getColorBasedOnVolume(avgVolume);

        ctx.beginPath();
        let sliceWidth = canvas.width / bufferLength * 2;
        let x = 0;
        for (let i = 0; i < bufferLength; i++) {
            let v = dataArray[i] / 255.0;
            let y = canvas.height / 2 - (v * canvas.height / 2);
            if (i === 0) {
                ctx.moveTo(x, y);
            } else {
                ctx.lineTo(x, y);
            }
            x += sliceWidth;
        }
        ctx.stroke();
    }

    async function fetchMetadata() {
        return 0;
        try {
            const response = await fetch('https://stream.nodejs-lib.ir/status-json.xsl?mount=/' + channel_name + '&nocache=' + Math.random());
            const json = await response.json();
            if (response.ok && json.icestats.source) {
                const metaData = typeof json.icestats.source.title !== 'undefined' ? JSON.parse(json.icestats.source.title) : 'N/A';
                metadataDiv.innerHTML = "<div style=padding-top:5px;text-align:left;><b>On Air:</b> " + metaData.title + "<br><b>Artist:</b> " + metaData.artist + "</div>";
            } else {
                metadataDiv.innerText = "No metadata available";
            }
        } catch (error) {
            metadataDiv.innerText = "Error fetching metadata";
            console.error("Metadata fetch error:", error);
            startStream();
        }

        setTimeout(function(){
            fetchMetadata();
        }, 10000000);
    }

    function startStream() {
        audio.src = "https://play.livemst.com/" + channel_name + "?nocache=" + new Date().getTime();;
        audio.crossOrigin = "anonymous";
        audio.load();
        audio.play().then(() => {
            setupAudio();
            console.log("Stream started successfully");
        }).catch(err => {
            console.error("Audio play error:", err);
            document.addEventListener("click", () => {
                audio.play();
                if (audioCtx && audioCtx.state === "suspended") {
                    audioCtx.resume();
                }
            }, { once: true });
        });
    }

    audio.addEventListener("canplay", () => {
        audio.play();
        setupAudio();
    });

    window.addEventListener("click", () => {
        if (audioCtx && audioCtx.state === "suspended") {
            audioCtx.resume();
        }
    });

    // Example live stream URL (replace with actual stream link)
    startStream();
    fetchMetadata();
</script>
</body>
</html>
