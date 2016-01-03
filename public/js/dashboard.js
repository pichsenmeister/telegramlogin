function deleteApp(app) {
    swal({
        title: 'Delete '+app.name+' ('+app.client_id+')?',
        text: 'All related data will be deleted. You will not be able to recover your app!',
        type: 'warning',
        confirmButtonColor: '#DD6B55',
        confirmButtonText: 'Yes, delete it!',
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true
    }, function() {
        sendDeleteRequest(app.id)
    });
}

function sendDeleteRequest(id) {
    var xhttp = new XMLHttpRequest()
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            var element = document.getElementById('app-'+id)
            element.parentNode.removeChild(element)
        }
    }
    var token = document.getElementById('token').value
    xhttp.open('POST', '/app/'+id, true)
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded')
    xhttp.send('_method=delete&_token='+token)
}
