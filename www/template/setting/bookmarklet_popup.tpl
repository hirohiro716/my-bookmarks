javascript:
popupWindow = window.open("<!--{$protocol}--><!--{$server}--><!--{$root}-->popup/", "my-bookmarks-window-for-popup", "width=500,height=700,toolbar=no,menubar=no");
window.addEventListener("message", function(event) {
    if (event.origin !== "<!--{$protocol}--><!--{$server}-->") {
        return;
    }
    let url = event.data;
    window.location.href = url;
    popupWindow.close();
}, false);
