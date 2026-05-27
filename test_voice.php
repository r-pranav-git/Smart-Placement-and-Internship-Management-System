<!DOCTYPE html>
<html>
<head>
    <title>AI Voice Assistant</title>
</head>
<body>

<h1>AI Voice Assistant</h1>

<input
    type="text"
    id="message"
    placeholder="Speak or type"
>

<button onclick="sendMessage()">
    Send
</button>

<button onclick="startVoice()">
    🎤 Speak
</button>

<p id="reply"></p>

<script>

async function sendMessage() {

    const message =
        document.getElementById("message").value;

    const response = await fetch(
        "voice_api.php",
        {
            method: "POST",

            headers: {
                "Content-Type":
                "application/x-www-form-urlencoded"
            },

            body:
                "message=" +
                encodeURIComponent(message)
        }
    );

    const data = await response.json();

    document.getElementById("reply")
        .innerHTML = data.reply;

    // SPEAK RESPONSE

    const speech =
        new SpeechSynthesisUtterance(
            data.reply
        );

    speechSynthesis.speak(speech);
}

function startVoice() {

    const recognition =
        new webkitSpeechRecognition();

    recognition.lang = "en-US";

    recognition.start();

    recognition.onresult =
        function(event) {

        const transcript =
            event.results[0][0].transcript;

        document.getElementById("message")
            .value = transcript;

        sendMessage();
    };
}

</script>

</body>
</html>