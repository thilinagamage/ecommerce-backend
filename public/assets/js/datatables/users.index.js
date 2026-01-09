
$(document).ready(function() {
    $('#users-table').DataTable({
        "paging": true,         // pagination
        "searching": true,      // search box
        "ordering": true,       // sortable columns
        "info": true,           // table info
        "lengthMenu": [5, 10, 25, 50], // page length options
        "pageLength": 10        // default page length
    });
});

