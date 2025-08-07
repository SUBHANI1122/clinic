$(document).ready(function () {
    const el = document.getElementById('medicineApp');
    const fetchUrl = el.dataset.fetchUrl;
    const storeUrl = el.dataset.storeUrl;
    const csrfToken = el.dataset.csrfToken;

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': csrfToken }
    });

    var table = $('#medicines-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: fetchUrl,
        order: [[0, 'desc']],
        columns: [
            { data: 'id', name: 'id', searchable: true },
            {
                data: 'null',
                name: 'name',
                searchable: true,
                render: (data, type, row) => `${row.name} (${row.size})`
            },
            {
                data: null,
                name: 'box_quantity',
                searchable: true,
                render: (data, type, row) => `${row.box_quantity} (${row.units_per_box})`
            },
            { data: 'total_units', name: 'total_units', searchable: true },
            { data: 'minimum_quantity', name: 'minimum_quantity', searchable: true },
            { data: 'expiry_date', name: 'expiry_date', searchable: true },
            { data: 'price', name: 'price', searchable: true },
            { data: 'sale_price', name: 'sale_price', searchable: true },
            { data: 'price_per_unit', name: 'price_per_unit', searchable: true },
            { data: 'sale_price_per_unit', name: 'sale_price_per_unit', searchable: true },
            {
                data: 'id',
                render: (data, type, row) => `
                    <button class="btn btn-info btn-sm editMedicine" 
                        data-id="${data}" 
                        data-name="${row.name}" 
                        data-size="${row.size}" 
                        data-box_quantity="${row.box_quantity}" 
                        data-units_per_box="${row.units_per_box}"
                        data-price="${row.price}"
                        data-expiry_date="${row.expiry_date}"
                        data-minimum_quantity="${row.minimum_quantity}"
                        data-total_units="${row.total_units}" 
                        data-sale_price="${row.sale_price}">
                        Edit
                    </button>
                    <button class="btn btn-danger btn-sm deleteMedicine" data-id="${data}">Delete</button>
                `,
                orderable: false,
                searchable: false
            }
        ],
        rowCallback: (row, data, index) => {
            const minQty = parseInt(data.minimum_quantity);
            const totalUnits = parseInt(data.total_units);
            const expiryDate = new Date(data.expiry_date);
            const today = new Date();
            const diffInDays = Math.ceil((expiryDate - today) / (1000 * 60 * 60 * 24));

            $(row).removeClass('table-danger table-warning');

            if (!isNaN(minQty) && totalUnits <= minQty) {
                $(row).addClass('table-danger');
            } else if (!isNaN(diffInDays) && diffInDays <= 2) {
                $(row).addClass('table-warning');
            }
        }
    });

    $('#newMedicineBtn').on('click', () => {
        $('#medicineModal').modal('show');
        $('#medicineModalTitle').text('Add New Medicine');
        $('#medicineForm')[0].reset();
        $('#medicineId').val('');
    });

    $(document).on('click', '.editMedicine', function () {
        $('#medicineModal').modal('show');
        $('#medicineModalTitle').text('Edit Medicine');
        $('#medicineId').val($(this).data('id'));
        $('#medicineName').val($(this).data('name'));
        $('#medicineSize').val($(this).data('size'));
        $('#boxQuantity').val($(this).data('box_quantity'));
        $('#unitsPerBox').val($(this).data('units_per_box'));
        $('#price').val($(this).data('price'));
        $('#sale_price').val($(this).data('sale_price'));
        $('#minimum_quantity').val($(this).data('minimum_quantity'));
        $('#total_units').val($(this).data('total_units'));
        $('#expiry_date').val($(this).data('expiry_date'));
    });

    $('#saveMedicine').on('click', () => {
        const medicineId = $('#medicineId').val();
        const formData = {
            name: $('#medicineName').val(),
            size: $('#medicineSize').val(),
            box_quantity: $('#boxQuantity').val(),
            units_per_box: $('#unitsPerBox').val(),
            price: $('#price').val(),
            sale_price: $('#sale_price').val(),
            total_units: $('#total_units').val(),
            minimum_quantity: $('#minimum_quantity').val(),
            expiry_date: $('#expiry_date').val(),
            _token: csrfToken
        };

        let url = storeUrl;
        if (medicineId) {
            url = `/medicines/${medicineId}`;
            formData._method = 'PATCH';
        }

        $.post(url, formData)
            .done(response => {
                if (response.success) {
                    $('#medicineModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Medicine',
                        text: 'Medicine saved successfully!',
                        customClass: { confirmButton: 'btn btn-success' }
                    });
                    table.ajax.reload();
                    $('#medicineForm')[0].reset();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Medicine',
                        text: 'Failed to save medicine!',
                        customClass: { confirmButton: 'btn btn-danger' }
                    });
                }
            })
            .fail(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Medicine',
                    text: 'An error occurred. Please try again.',
                    customClass: { confirmButton: 'btn btn-danger' }
                });
            });
    });

    $(document).on('click', '.deleteMedicine', function () {
        const id = $(this).data('id');
        if (confirm('Are you sure you want to delete this medicine?')) {
            $.ajax({
                url: `/medicines/${id}`,
                method: 'DELETE',
                data: { _token: csrfToken },
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Medicine',
                            text: 'Medicine Deleted.',
                            customClass: { confirmButton: 'btn btn-success' }
                        });
                        table.ajax.reload();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Medicine',
                            text: 'An error occurred. Please try again.',
                            customClass: { confirmButton: 'btn btn-danger' }
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Medicine',
                        text: 'An error occurred. Please try again.',
                        customClass: { confirmButton: 'btn btn-danger' }
                    });
                }
            });
        }
    });
});
