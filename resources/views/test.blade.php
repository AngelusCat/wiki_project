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
    const button = document.getElementById('myButton');
    const div = document.getElementById('myDiv');
    function changeContent()
    {
        div.innerHTML = '<p>New content</p>';
    }
    button.addEventListener('click', function () {
        let promise = fetch('/test2');
        promise.then(response => {
            let promise2 = response.json();
            alert(promise2);
        });
    });
</script>
</body>
</html>
