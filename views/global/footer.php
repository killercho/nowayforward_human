    </article>
</body>
<script type="text/javascript">
    function eval_callbacks() {
        if (authentication_response === null) {
            setTimeout(eval_callbacks, 50);
        }
        else if (authentication_response !== "") {
            for (callback of authentication_callbacks) {
                callback(authentication_response);
            }
        }
    }
    eval_callbacks();
</script>
</html>
