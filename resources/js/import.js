var form = document.getElementById('form');
var params = new FormData(form);
fetch('/', {
    method: 'POST',
    body: params
});
