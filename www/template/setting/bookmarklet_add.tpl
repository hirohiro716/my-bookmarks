javascript:
let parameters = {};
parameters["url"] = location.href;
let titles = document.getElementsByTagName("title");
for (let index = 0; index < titles.length; index++) {
    parameters["name"] = titles[index].innerText;
    break;
}
let links = document.getElementsByTagName("link");
for (let index = 0; index < links.length; index++) {
    let link = links[index];
    let attribute = link.getAttribute("rel");
    if (attribute == "icon" || attribute == "shortcut icon") {
        parameters["icon_url"] = link.href;
        break;
    }
}
let queryString = "";
Object.keys(parameters).forEach(function (key) {
    if (queryString.length > 0) {
        queryString += "&";
    }
    queryString += key;
    queryString += "=";
    queryString += parameters[key];
});
window.open("<!--{$protocol}--><!--{$server}--><!--{$root}-->?" + queryString, null, "width=700,height=800,toolbar=no,menubar=no");
