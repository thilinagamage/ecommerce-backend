
    $(document).ready(function () {
        $('#categories-table').DataTable({
            pageLength: 10,
            lengthChange: true,
            ordering: true,
            searching: true,
            responsive: true,
            columnDefs: [
                { orderable: false, targets: [0, 5] } // Image & Actions not sortable
            ]
        });
    });

