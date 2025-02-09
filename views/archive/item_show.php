<script type="text/javascript">
    function showButtons() {
        for (buttonset of document.getElementsByName('itemButton')) {
            buttonset.hidden = false;
        }
    }
    authenticated(showButtons);

    function copyLink(url) {
        navigator.clipboard.writeText(url);
        alert("Copied link to clipboard!");
    }
</script>
