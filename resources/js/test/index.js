let importTab = document.getElementById('importTab');
let searchTab = document.getElementById('searchTab');
let content = document.getElementById('content');

let children = content.children;
let childrenArray = Array.prototype.slice.call(children);
export let flag = false;

for (let elem of childrenArray) {
    let id = elem.id;
    if (id === 'table') {
        flag = true;
    }
}

console.log(flag);

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
