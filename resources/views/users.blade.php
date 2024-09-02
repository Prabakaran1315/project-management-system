<x-app-layout>
    @section('title')
        Users
    @endsection

    <div class="bg-white p-4">
        <div>
            <table id="user-data-table" class="table" cellspacing="0" width="100%" style="width:100%">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>  
            </table>
        </div>
    </div>

    <x-modal name="userModal" :show="false" maxWidth="2xl">
        <div class="modal-header">
            <h5 class="modal-title"># User</h5>
            <button type="button" @click="show = false" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form id="userForm" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <x-input-label for="title-info" :value="__('Name')" />
                    <p id="name-info" class="form-control-static"></p>
                    <input type="hidden" name="user_id" id="user_id">
                </div>

                <div class="form-group">
                    <x-input-label for="role_id" :value="__('Role*')" />
                    <x-select
                        name="role_id"
                        id="role_id"
                        :options="$roles"
                        :selected="old('status', [])"
                        required
                    />
                </div>

                <x-primary-button type="button" id="updateUserRole">{{ __('Save') }}</x-primary-button>
                <x-secondary-button class="ms-1" @click="show = false" aria-label="Close">{{ __('Cancel') }}</x-secondary-button>
        </form>
        </div>
    </x-modal>

    @section('script')
        <script>
            const userListUrl = '{{ route('users.list') }}';
            const roleUpdateUrl = '{{ route("users.role", ":id") }}';
            const userDeleteUrl = '{{ route("users.delete", ":id") }}';
        </script>
        <script src="{{ asset('js/users.js') }}"></script>
    @endsection
</x-app-layout>