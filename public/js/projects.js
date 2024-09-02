$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});


$(document).ready(function() {
    $('#addProjectBtn').on('click', function() {
        $('#name').val('');
        $('#description').val('');
        $('#start_date').val('');
        $('#end_date').val('');
        $('#project_id').val('');
        $('#team_members').val([]); 
        window.dispatchEvent(new CustomEvent('open-modal', { detail: 'projectModal' }));
    });

    $('form#projectForm').on('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission
        var formData = $(this).serialize();
        let id = $('#project_id').val();
        let url = id ? projectUpdateUrl.replace(':id', id) : projectStoreUrl;
        let method = id ? 'PUT' : 'POST';
        $.ajax({
            url: url,
            type: method,
            data: formData,
            success: function(response) {
                $('#project-data-table').DataTable().ajax.reload();
                successToast(response.message);
                window.dispatchEvent(new CustomEvent('close-modal', { detail: 'projectModal' }));
            },
            error: function(xhr) {
                warningToast('Please try again.');
            }
        });
    });
});

var projectTable = $('#project-data-table').DataTable({
    processing: true,
    serverSide: true,
    order: [],
    "ajax": {
        type: 'GET',
        url: projectListUrl,
        error: function (jqXHR, XMLHttpRequest, textStatus, errorThrown) {
            warningToast('Please try again.');
        }
    },
    columns: [
        { data: 'name', name: 'name' },
        {
            data: 'start_date',
            name: 'start_date',
            render: function(data, type, row) {
                return moment(data).format('DD/MM/YYYY');
            }
        },
        {
            data: 'end_date',
            name: 'end_date',
            render: function(data, type, row) {
                return moment(data).format('DD/MM/YYYY');
            }
        },
        {
            data: "action",
            name: "action",
            orderable: false,
            searchable: false,
        }
    ],
    responsive: true,
});

$(document).on('click', '#editProject', function() {
    var projectId = $(this).data('id');
    showLoader();
    $.ajax({
        url: projectEditUrl.replace(':id', projectId),
        type: 'GET',
        success: function(response) {
            let res = response.data;
            $('#name').val(res.name);
            $('#description').val(res.description);
            $('#start_date').val(res.start_date);
            $('#end_date').val(res.end_date);
            $('#project_id').val(res.id);

            let teamMemberIds = res.team_members.map(member => member.id);
            $('#team_members').val(teamMemberIds); 
            
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'projectModal' }));
            hideLoader(); 
        },
        error: function(xhr, status, error) {
            warningToast('Please try again.');
            hideLoader(); 
        }
    });
});

$(document).on('click', '#deleteProject', function() {
    var projectId = $(this).data('id');
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
                url: projectDeleteUrl.replace(':id', projectId),
                type: 'Delete',
                success: function(response) {
                    $('#project-data-table').DataTable().ajax.reload();
                    successToast(response.message);
                },
                error: function(xhr, status, error) {
                    warningToast('Please try again.');
                }
            });
        }
    });

});
