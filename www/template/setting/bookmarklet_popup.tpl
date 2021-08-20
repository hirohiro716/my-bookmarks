javascript:
popupWindow = window.open("<!--{$protocol}--><!--{$server}--><!--{$root}-->popup/", "my-bookmarks-window-for-popup", "width=500,height=700,toolbar=no,menubar=no");
window.addEventListener("message", function(event) {
    if (event.origin !== "<!--{$protocol}--><!--{$server}-->") {
        return;
    }
    let values = event.data;
    switch (values.mode) {
    case "move":
        window.location.href = values.url;
        break;
    case "edit":
        window.location.href = "<!--{$protocol}--><!--{$server}--><!--{$root}-->?scroll=" + values.id;
        break;
    }
    popupWindow.close();
}, false);
