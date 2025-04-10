<script>
   $(document).ready(function() {
    // Initialize DataTable
    var table = $('#salesTable').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        order: [[0, 'desc']]
    });

    // Disable DataTable search when typing in the medicine search input
    $('#medicineSearchPage1').on('keyup', function(event) {
        event.stopPropagation(); // Prevent the event from bubbling
        // Disable the search for the DataTable
        table.search('').draw(); // This clears the DataTable search
    });

    // Your modal listeners
    window.addEventListener('openReturnSaleModal', () => $('#returnSaleModal').modal('show'));
    window.addEventListener('closeReturnSaleModal', () => $('#returnSaleModal').modal('hide'));
    window.addEventListener('openViewSaleModal', () => $('#viewSaleModal').modal('show'));
    window.addEventListener('closeViewSaleModal', () => $('#viewSaleModal').modal('hide'));
});

</script>
