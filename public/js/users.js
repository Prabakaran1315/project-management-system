$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

var taskTable = $('#user-data-table').DataTable({
    processing: true,
    serverSide: true,
    order: [],
    "ajax": {
        type: 'GET',
        url: userListUrl,
        error: function (jqXHR, XMLHttpRequest, textStatus, errorThrown) {
            // custom_err(jqXHR, XMLHttpRequest, textStatus, errorThrown);
        }
    },
    columns: [
        { data: 'name', name: 'name' },
        { data: 'role_name', name: 'role_name' },
        {
            data: "action",
            name: "action",
            orderable: false,
            searchable: false,
        }
    ],
    responsive: true,
});

$(document).on('click', '#viewUser', function() {
    var userId = $(this).data('id');
    var userName = $(this).data('name');
    var userRole = $(this).data('role');
    $('#name-info').text(userName);
    $('#user_id').val(userId);
    $('#role_id').val(userRole);
    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'userModal' }));
});

$(document).on('click', '#updateUserRole', async function() {
    var userId = $('#user_id').val();
    var role_id = $('#role_id').val();
    
    let url = roleUpdateUrl.replace(':id', userId)
    $.ajax({
        url: url,
        type: 'patch',
        data: {role_id:role_id},
        success: function(response) {
            $('#user-data-table').DataTable().ajax.reload();
            successToast(response.message);
            window.dispatchEvent(new CustomEvent('close-modal', { detail: 'userModal' }));
        },
        error: function(xhr) {
            warningToast('Please try again.');
        }
    });
});

$(document).on('click', '#deleteUser', function() {
    var taskId = $(this).data('id');
    Swal.fire({
        title: 'Are you sure?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: userDeleteUrl.replace(':id', taskId),
                type: 'Delete',
                success: function(response) {
                    $('#user-data-table').DataTable().ajax.reload();
                    successToast(response.message);
                },
                error: function(xhr, status, error) {
                    warningToast('Please try again.');
                }
            });
        }
    });
});