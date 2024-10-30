<script>
(function (window, document) {
    var loader = function () {
        var script = document.createElement("script"), tag = document.getElementsByTagName("script")[0];
    script.src = "https://app.convertbar.com/embed/<?= get_option("convbar_embed_id", "")?>/convertbar.js";
    script.id = "app-convertbar-script";
    tag.parentNode.insertBefore(script, tag);
  };
    window.addEventListener ? window.addEventListener("load", loader, false) : window.attachEvent("onload", loader);
})(window, document);
</script>