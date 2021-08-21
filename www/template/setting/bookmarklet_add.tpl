javascript:
showWindow = function(iconURL) {
    let parameters = {};
    if (typeof iconURL !== "undefined") {
        parameters["icon_url"] = iconURL;
    }
    parameters["url"] = location.href;
    let titles = document.getElementsByTagName("title");
    for (let index = 0; index < titles.length; index++) {
        parameters["name"] = titles[index].textContent;
        break;
    }
    let queryString = "";
    Object.keys(parameters).forEach(function (key) {
        if (queryString.length > 0) {
            queryString += "&";
        }
        queryString += key;
        queryString += "=";
        queryString += encodeURIComponent(parameters[key]);
    });
    window.open("<!--{$protocol}--><!--{$server}--><!--{$root}-->?" + queryString, "my-bookmarks-window-for-add", "width=700,height=800,toolbar=no,menubar=no");
};
image = new Image();
imageURL = "/favicon.ico";
image.addEventListener("load", function() {
    let iconURL = image.src;
    showWindow(iconURL);
});
image.addEventListener("error", function() {
    let iconURL;
    let links = document.getElementsByTagName("link");
    for (let index = 0; index < links.length; index++) {
        let link = links[index];
        let attribute = link.getAttribute("rel");
        if (typeof attribute !== "undefined" && typeof link.href !== "undefined") {
            if (attribute == "icon" || attribute == "shortcut icon") {
                iconURL = link.href;
                break;
            }
        }
    }
    showWindow(iconURL);
});
image.src = imageURL;
console.log("Open a page for bookmark.");
