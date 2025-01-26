<h2>Logging you out...</h2>

<script type="text/javascript">
    if (!cookieStorage.getItem('token')) {
        window.location.href = '/';
    }

    function deleteToken(response) {
        let request = new XMLHttpRequest();
        request.onreadystatechange = function() {
            if (request.readyState < 4) return;

            window.location.href = '/';
        }
        request.open("DELETE", "#", true);
        request.send(null);

        cookieStorage.removeItem('token');
    }
    authenticated(deleteToken);
</script>
