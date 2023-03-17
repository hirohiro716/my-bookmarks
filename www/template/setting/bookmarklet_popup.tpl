javascript:
popupWindow = window.open("<!--{$protocol}--><!--{$server}--><!--{$root}-->popup/", "my-bookmarks-window-for-popup", "width=400,height=600,top=" + (window.screenY + 50) + ",left=" + (window.screenX - 50));
window.addEventListener("message", function(event) {
    if (event.origin !== "<!--{$protocol}--><!--{$server}-->") {
        return;
    }
    let values = event.data;
    switch (values.mode) {
    case "move":
        window.location.href = values.url;
        break;
    }
    popupWindow.close();
}, false);
