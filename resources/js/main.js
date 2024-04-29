const buttonImport = document.getElementById('import');
const buttonSearch = document.getElementById('search');
const div = document.getElementById('content');

buttonImport.addEventListener('click', function () {
    fetch('/importHTMLCode').then(function (response) {
        response.text().then(function (text) {
            div.innerHTML = text;
        });
    });
});
buttonSearch.addEventListener('click', function () {
    fetch('/searchHTMLCode').then(function (response) {
        response.text().then(function (text) {
            div.innerHTML = text;
        });
    });
});
