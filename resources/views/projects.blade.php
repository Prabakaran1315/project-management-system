<x-app-layout>
    @section('title')
        Projects
    @endsection

    <div class="bg-white p-4">
        <div>
            @can('project_create')
            <x-primary-button class="ms-3 float-right" id="addProjectBtn">
                {{ __('+ Add') }}
            </x-primary-button>
            @endcan
        </div>

        <div class="pt-4">
            <table id="project-data-table" class="table" cellspacing="0" width="100%" style="width:100%">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Action</th>
                    </tr>
                </thead>  
            </table>
        </div>
    </div>

    <!-- Modal -->
    <x-modal name="projectModal" :show="false" maxWidth="2xl">
            <div class="modal-header">
                <h5 class="modal-title"># Project</h5>
                <button type="button" @click="show = false" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="projectForm" method="POST">
                    @csrf
                    <div class="form-group">
                        <x-input-label for="name" :value="__('Project Name*')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required autofocus autocomplete="false" />
                        <input type="hidden" name="project_id" id="project_id">
                    </div>
                    <div class="form-group">
                        <x-input-label for="start_date" :value="__('Start Date*')" />
                        <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" required autofocus autocomplete="false" />

                    </div>
                    <div class="form-group">
                        <x-input-label for="end_date" :value="__('End Date*')" />
                        <x-text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" required autofocus autocomplete="false" />
                    </div>
                    <div class="form-group">
                        <x-input-label for="team_members" :value="__('Assignee*')" />
                        <x-select
                            name="team_members"
                            id="team_members"
                            :options="$teamMembers->pluck('name', 'id')->toArray()"
                            :selected="old('team_members', [])"
                            multiple
                            placeholder="Select team members"
                        />
                    </div>
                    <div class="form-group">
                        <x-input-label for="description" :value="__('Description')" />
                        <x-text-area name="description" class="block mt-1 w-full"  rows="3" id="description"></x-text-area>
                    </div>
                    <x-primary-button class="ms-1">{{ __('Save') }}</x-primary-button>
                    <x-secondary-button class="ms-1" @click="show = false" aria-label="Close">{{ __('Cancel') }}</x-secondary-button>
                </form>
            </div>
        </x-modal>
    @section('script')
    <script>
        const projectStoreUrl = @json(route('projects.store'));
        const projectListUrl = @json(route('projects.list'));
        const projectEditUrl = @json(route('projects.edit', ['id' => ':id']));
        const projectUpdateUrl = @json(route('projects.update', ['id' => ':id']));
        const projectDeleteUrl = @json(route('projects.delete', ['id' => ':id']));
    </script>
    <script src="{{ asset('js/projects.js') }}"></script>
    @endsection
</x-app-layout>