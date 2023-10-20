$(document).ready(function() {
    $('.sidebar a').on('click', function() {
        $('.sidebar a').removeClass('active'); // Remove active class from all links
        $(this).addClass('active'); // Add active class to the clicked link
    });
});

$(document).ready(function() {
    setTimeout(function() {
        $('.view-blanks-table').DataTable();
    }, 500);
});
