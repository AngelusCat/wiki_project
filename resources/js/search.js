const elem = document.querySelectorAll('a');
const preArticleContent = document.getElementById('articleContent');
for ( let i = 0; i < elem.length; i++ ) {
    elem[i].addEventListener( 'click', function () {
        let title = elem[i].name;
        let url = '/getArticleContent/' + title;
        fetch(url).then(function (response) {
            response.text().then(function (text) {
                preArticleContent.innerHTML = text;
            });
        });
    });
}
