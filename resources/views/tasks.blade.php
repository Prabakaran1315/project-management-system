<x-app-layout>
    @section('title')
        Task
    @endsection

    <div class="bg-white p-4">
        @can('task_create')
        <div class="mb-2">
            <x-primary-button class="ms-3 float-right" id="addTaskBtn">
                {{ __('+ Add') }}
            </x-primary-button>
        </div>
        @endcan

        <div>
            <table id="task-data-table" class="table" cellspacing="0" width="100%" style="width:100%">
                <thead>
                    <tr>
                        <th>Project</th>
                        <th>Task</th>
                        <th>Deadline</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>  
            </table>
        </div>
    </div>

    <!-- Modal -->
    <x-modal name="taskModal" :show="false" maxWidth="2xl">
        <div class="modal-header">
            <h5 class="modal-title"># Task</h5>
            <button type="button" @click="show = false" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
       
        @php
            $readOnly = !auth()->user()->can('task_updates');
        @endphp

        <div class="modal-body">
            <form id="taskForm" method="POST">
                @csrf
                <div class="form-group">
                    <x-input-label for="title" :value="__('Title*')" />
                    <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" required autofocus autocomplete="false"  />
                    <input type="hidden" name="task_id" id="task_id">
                </div>
                <div class="form-group">
                    <x-input-label for="dead_line" :value="__('Deadline*')" />
                    <x-text-input id="dead_line" class="block mt-1 w-full" type="datetime-local" name="dead_line" required />
                </div>
                <div class="form-group">
                    <x-input-label for="project_id" :value="__('Project*')" />
                    <x-select
                        name="project_id"
                        id="project_id"
                        :options="$projects->pluck('name', 'id')->toArray()"
                        :selected="old('project_id', [])"
                        placeholder="Select a project"
                        
                    />
                </div>
                <div class="form-group">
                    <x-input-label for="assignee" :value="__('Assignee')" />
                    <x-select
                        name="assignee"
                        id="assignee"
                        :selected="old('assignee', [])"
                        placeholder="Select assignee"
                        
                    />
                </div>

                <div class="form-group">
                    <x-input-label for="description" :value="__('Description')" />
                    <x-text-area name="description" class="block mt-1 w-full" rows="3" id="description"></x-text-area>
                </div>
                <x-primary-button class="ms-1">{{ __('Save') }}</x-primary-button>
                <x-secondary-button class="ms-1" @click="show = false" aria-label="Close">{{ __('Cancel') }}</x-secondary-button>
            </form>
        </div>
    </x-modal>

    <x-modal name="taskStatusModal" :show="false" maxWidth="2xl">
        <div class="modal-header">
            <h5 class="modal-title"># Task Status</h5>
            <button type="button" @click="show = false" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        @php
            $canUpdateStatus = auth()->user()->can('task_status_update');
        @endphp

        <div class="modal-body">
            <form id="taskStatusForm" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <x-input-label for="title-info" :value="__('Title')" />
                    <p id="title-info" class="form-control-static"></p>
                    <input type="hidden" name="task_id_status" id="task_id_status">
                </div>
                <div class="form-group">
                    <x-input-label for="dead_line-info" :value="__('Deadline')" />
                    <p id="dead_line-info" class="form-control-static"></p>
                </div>
                <div class="form-group">
                    <x-input-label for="project-info" :value="__('Project')" />
                    <p id="project-info" class="form-control-static"></p>
                </div>
                <div class="form-group">
                    <x-input-label for="description-info" :value="__('Description')" />
                    <p id="description-info" class="form-control-static"></p>
                </div>
                
                @if($canUpdateStatus)
                    <div class="form-group">
                        <x-input-label for="status" :value="__('Status*')" />
                        <x-select
                            name="status"
                            id="status"
                            :options="[
                                1 => 'Created',
                                2 => 'In Progress',
                                3 => 'Completed'
                            ]"
                            :selected="old('status', [])"
                        />
                    </div>
                @else
                    <div class="form-group">
                        <x-input-label for="status" :value="__('Status*')" />
                        <p id="status-info" class="form-control-static"></p>
                    </div>
                @endif

                @if($canUpdateStatus)
                    <x-primary-button type="button" id="updateStatus">{{ __('Save') }}</x-primary-button>
                    <x-secondary-button class="ms-1" @click="show = false" aria-label="Close">{{ __('Cancel') }}</x-secondary-button>
                @endif
            </form>
        </div>
    </x-modal>
    
    @section('script')
    <script>
        const taskStoreUrl = @json(route('task.store'));
        const taskListUrl = @json(route('task.list'));
        const taskEditUrl = @json(route('task.edit', ':id'));
        const taskUpdateUrl = @json(route('task.update', ':id'));
        const taskDeleteUrl = @json(route('task.delete', ':id'));
        const assigneesUrl = @json(route('projects.team-members', ':id'));
        const updateStatus = {{$canUpdateStatus}};
        const taskStatusUpdateUrl = @json(route('task.status', ':id'));
        const STATUS_MAP = {
            1: 'Created',
            2: 'In Progress',
            3: 'Completed'
        };
        
    </script>

    <script src="{{ asset('js/task.js') }}"></script>
    @endsection
</x-app-layout>