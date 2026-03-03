<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Staff') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-start mb-6">
                <a href="{{ route('owner.staff-management.index') }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                    &larr; Back to Staff List
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form method="POST" action="{{ route('owner.staff-management.update', $staff) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-label for="name" value="{{ __('Name') }}" />
                        <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $staff->name)" required autofocus autocomplete="name" />
                        <x-input-error for="name" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-label for="email" value="{{ __('Email') }}" />
                        <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $staff->email)" required autocomplete="username" />
                        <x-input-error for="email" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-label for="phone_number" value="{{ __('Phone Number') }}" />
                        <x-input id="phone_number" class="block mt-1 w-full" type="text" name="phone_number" :value="old('phone_number', $staff->phone_number)" />
                        <x-input-error for="phone_number" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-label for="password" value="{{ __('New Password (leave blank to keep current)') }}" />
                        <x-input id="password" class="block mt-1 w-full" type="password" name="password" autocomplete="new-password" />
                        <x-input-error for="password" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-label for="password_confirmation" value="{{ __('Confirm New Password') }}" />
                        <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" autocomplete="new-password" />
                    </div>
                    
                    <div class="mt-4">
                        <x-label for="photo" value="{{ __('Photo') }}" />
                        <div class="mt-2 mb-2">
                            <img src="{{ $staff->profile_photo_url }}" alt="{{ $staff->name }}" class="rounded-full h-20 w-20 object-cover">
                        </div>
                        <input id="photo" type="file" name="photo" class="block w-full text-sm text-gray-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-full file:border-0
                            file:text-sm file:font-semibold
                            file:bg-indigo-50 file:text-indigo-700
                            hover:file:bg-indigo-100" />
                        <x-input-error for="photo" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-button class="ml-4">
                            {{ __('Update Staff') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
