<x-guest-layout>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-2xl font-semibold text-gray-800 mb-6">Projects</h1>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            @forelse($projects as $project)
                <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
                    @if($project['image'])
                        <img src="{{ $project['image'] }}" alt="{{ $project['title'] }}" class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gray-100 flex items-center justify-center text-gray-400">
                            No Image
                        </div>
                    @endif
                    <div class="p-4">
                        <div class="text-sm uppercase tracking-wide text-indigo-600 mb-1">{{ $project['type'] }}</div>
                        <h3 class="text-lg font-medium text-gray-900">{{ $project['title'] }}</h3>
                    </div>
                </div>
            @empty
                <p class="text-gray-600">No projects found.</p>
            @endforelse
        </div>
        <div class="mt-8">
            {{ $projects->links() }}
        </div>
    </div>
</x-guest-layout>

