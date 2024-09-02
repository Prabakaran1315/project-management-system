$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function() {
    $('#addTaskBtn').on('click', function() {
        $('#title').val('');
        $('#description').val('');
        $('#dead_line').val('');
        $('#project_id').val('');
        $('#assignee').val(''); 
        window.dispatchEvent(new CustomEvent('open-modal', { detail: 'taskModal' }));
    });

    $('#project_id').on('change', async function() {
        const projectId = $(this).val();
        await loadAssignees(projectId);
    });

    $('form#taskForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        let id = $('#task_id').val();
        let url = id ? taskUpdateUrl.replace(':id', id) : taskStoreUrl;
        let method = id ? 'PUT' : 'POST';
        $.ajax({
            url: url,
            type: method,
            data: formData,
            success: function(response) {
                $('#task-data-table').DataTable().ajax.reload();
                successToast(response.message);
                window.dispatchEvent(new CustomEvent('close-modal', { detail: 'taskModal' }));
            },
            error: function(xhr) {
                warningToast('Please try again.');
            }
        });
    });

    
});

var taskTable = $('#task-data-table').DataTable({
    processing: true,
    serverSide: true,
    order: [],
    "ajax": {
        type: 'GET',
        url: taskListUrl,
        error: function (jqXHR, XMLHttpRequest, textStatus, errorThrown) {
            warningToast('Please try again.');
        }
    },
    columns: [
        { data: 'project_name', name: 'project_name' },
        { data: 'title', name: 'title' },
        {
            data: 'dead_line',
            name: 'dead_line',
            render: function(data, type, row) {
                return moment(data).format('DD/MM/YYYY');
            }
        },
        {
            data: 'status',
            name: 'status',
            render: function(data, type, row) {
                return STATUS_MAP[data] || '-';
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

$(document).on('click', '#editTask', async function() {
    const taskId = $(this).data('id');
    showLoader();
    try {
        const response = await $.ajax({
            url: taskEditUrl.replace(':id', taskId),
            type: 'GET'
        });
        
        const res = response.data;
        $('#task_id').val(res.id);
        $('#title').val(res.title);
        $('#description').val(res.description);
        $('#dead_line').val(res.dead_line);
        $('#project_id').val(res.project_id);
        await loadAssignees(res.project_id);
        $('#assignee').val(res.user_id);
        window.dispatchEvent(new CustomEvent('open-modal', { detail: 'taskModal' }));
    } catch (error) {
        warningToast('Please try again.');
    } finally {
        hideLoader(); 
    }
});

$(document).on('click', '#deleteTask', function() {
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
                url: taskDeleteUrl.replace(':id', taskId),
                type: 'Delete',
                success: function(response) {
                    $('#task-data-table').DataTable().ajax.reload();
                    successToast(response.message);
                },
                error: function(xhr, status, error) {
                    warningToast('Please try again.');
                }
            });
        }
    });
});

async function loadAssignees(projectId) {
    try {
        
        const response = await $.ajax({
            url: assigneesUrl.replace(':id', projectId),
            type: 'GET'
        });
        
        $('#assignee').empty();
        $('#assignee').append('<option value="">Select</option>');
        response.data.forEach(member => {
            $('#assignee').append('<option value="' + member.id + '">' + member.name + '</option>');
        });
    } catch (error) {
        warningToast('Please try again.');
    } 
}

$(document).on('click', '#viewTask', async function() {
    var taskId = $(this).data('id');

    try {
        // Show loader
        showLoader();

        let response = await fetch(taskEditUrl.replace(':id', taskId));
        let data = await response.json();

        if (response.ok) {
            let task = data.data;
            
            // Populate labels with task data
            $('#title-info').text(task.title);
            $('#dead_line-info').text(task.dead_line);
            $('#project-info').text(task.project_name);
            $('#description-info').text(task.description);
            $('#task_id_status').val(task.id);
            // Populate status only if the user has permission
            if(updateStatus){
                $('#status').val(task.status);
            }
            else{
                $('#status-info').text(task.status);
            }

            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'taskStatusModal' }));
        } else {
            warningToast('Failed to load task details.');
        }
    } catch (error) {
        console.error('Error:', error);
        warningToast('Please try again.');
    } finally {
        hideLoader(); 
    }
});

$(document).on('click', '#updateStatus', async function() {
    var taskId = $('#task_id_status').val();
    var status = $('#status').val();
    
    let url = taskStatusUpdateUrl.replace(':id', taskId)
    $.ajax({
        url: url,
        type: 'patch',
        data: {status:status},
        success: function(response) {
            $('#task-data-table').DataTable().ajax.reload();
            successToast(response.message);
            window.dispatchEvent(new CustomEvent('close-modal', { detail: 'taskStatusModal' }));
        },
        error: function(xhr) {
            warningToast('Please try again.');
        }
    });
});

