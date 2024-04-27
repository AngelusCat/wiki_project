<!doctype html>
<html>
<head>
    <title>Document</title>
</head>
<body>
    <button id="myButton">
        Click me!
    </button>
    <div id="myDiv">
        <p>
            Old content
        </p>
    </div>
<script>
/*    const button = document.getElementById('myButton');
    const div = document.getElementById('myDiv');
    function changeContent()
    {
        div.innerHTML = '<p>New content</p>';
    }
    button.addEventListener('click', function () {
        let promise = fetch('/test2');
        promise.then(response, function() {
            div.innerHTML = response;
        });
        });*/

const button = document.getElementById('myButton');
const div = document.getElementById('myDiv');

button.addEventListener('click', function () {
    fetch('/test2').then(function (response) {
        response.text().then(function (text) {
            div.innerHTML = text;
        });
    });
});

/*    fetch(url).then(function (response) {
        response.text().then(function (text) {
            poemDisplay.textContent = text;
        });
    });*/

</script>
</body>
</html>
