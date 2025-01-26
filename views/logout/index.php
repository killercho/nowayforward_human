<h2>Logging you out...</h2>

<script type="text/javascript">
    if (!sessionStorage.getItem('token')) {
        window.location.href = '/';
    }

    function deleteToken(response) {
        let token = sessionStorage.getItem('token');
        sessionStorage.removeItem('token');

        let request = new XMLHttpRequest();
        request.onreadystatechange = function() {
            if (request.readyState < 4) return;

            window.location.href = '/';
        }
        request.open("DELETE", "#", true);
        request.setRequestHeader("Authorization", token);
        request.send(null);
    }
    authenticated(deleteToken);
</script>
