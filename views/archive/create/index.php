<h2>Archiving <?= $url ?>...</h2>

<p>Current position in queue: <span id="position">unavailable</span></p>

<script type="text/javascript">
// If our request is the one that starts the worker, it's output will be 0
// and it won't update the variable soon (or ever)
// But if it's not the worker, the variable will be updated (quickly)
var queuePos = 0;

function requestDownload(url) {
    var request = new XMLHttpRequest();
    request.onreadystatechange = function() {
        if (request.readyState < 4) return;

        console.log(request.responseText);
        queuePos = request.responseText;
    }
    request.open("POST", "#", true);
    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    request.withCredentials = true;
    request.send('async=true&url=' +
            url +
            '<?php if (array_key_exists("manual", $_POST)) { echo "&manual=" . $_POST["manual"]; } ?>');
}
requestDownload('<?= $url ?>');

const position = document.getElementById('position');
function updatePosition(url) {
    var request = new XMLHttpRequest();
    request.onreadystatechange = function() {
        if (request.readyState < 4) return;

        if (queuePos < request.responseText) {
            window.location.href = '/archive/?url=' + url;
            return;
        }

        position.innerText = queuePos - request.responseText;
        setTimeout(updatePosition, 1000, url);
    }
    request.open("POST", "/archive/create/status.php", true);
    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    request.send('url=' + url);
}

updatePosition('<?= $url ?>');
</script>
