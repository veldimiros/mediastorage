$(document).ready(function () {
    $('#example').DataTable();
    $("#upload").uploadprogress({redirect_url: '/result'});
});