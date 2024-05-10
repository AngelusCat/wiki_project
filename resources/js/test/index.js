let importTab = document.getElementById('importTab');
let searchTab = document.getElementById('searchTab');
let content = document.getElementById('content');

importTab.addEventListener('click', function() {
    let htmlCode = fetch('/importHTML').then(function(htmlCode) {
        htmlCode.text().then(function(text) {
            content.innerHTML = text;
        });
    });
});
searchTab.addEventListener('click', function() {
    let htmlCode = fetch('/searchHTML').then(function(htmlCode) {
        htmlCode.text().then(function(text) {
            content.innerHTML = text;
        });
    });
});
