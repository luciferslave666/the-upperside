<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Produk Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                        @csrf 

                        <div>
                            <label for="name" class="block font-medium text-sm text-gray-700">Nama Produk</label>
                            <input id="name" name="name" type="text" value="{{ old('name') }}" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required autofocus>
                            @error('name')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <label for="image" class="block font-medium text-sm text-gray-700">Gambar Produk (Opsional)</label>
                            <input id="image" name="image" type="file" class="block mt-1 w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-md file:border-0
                                file:text-sm file:font-semibold
                                file:bg-indigo-50 file:text-indigo-700
                                hover:file:bg-indigo-100
                            ">
                            @error('image')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <div class="mt-4" x-data="{ 
    open: false, 
    selectedId: '{{ old('category_id') }}', 
    selectedName: 'Pilih Kategori',
    categories: {{ $categories->toJson() }} 
}" x-init="
    // Set initial name jika old value ada
    if(selectedId) {
        const found = categories.find(c => c.id == selectedId);
        if(found) selectedName = found.name;
    }
">
    <label class="block font-medium text-sm text-gray-700">Kategori</label>
    
    <input type="hidden" name="category_id" x-model="selectedId">

    <div class="relative mt-1">
        <button type="button" @click="open = !open" 
                class="relative w-full cursor-default rounded-md border border-gray-300 bg-white py-2 pl-3 pr-10 text-left shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 sm:text-sm">
            <span class="block truncate" x-text="selectedName"></span>
            <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 3a.75.75 0 01.55.24l3.25 3.5a.75.75 0 11-1.1 1.02L10 4.852 7.3 7.76a.75.75 0 01-1.1-1.02l3.25-3.5A.75.75 0 0110 3zm-3.76 9.2a.75.75 0 011.06.04l2.7 2.908 2.7-2.908a.75.75 0 111.1 1.02l-3.25 3.5a.75.75 0 01-1.1 0l-3.25-3.5a.75.75 0 01.04-1.06z" clip-rule="evenodd" />
                </svg>
            </span>
        </button>

        <ul x-show="open" @click.away="open = false" class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm" style="display: none;">
            
            <template x-for="category in categories" :key="category.id">
                <li class="relative cursor-default select-none py-2 pl-3 pr-9 hover:bg-indigo-50 group flex justify-between items-center">
                    <div @click="selectedId = category.id; selectedName = category.name; open = false" class="flex-grow cursor-pointer">
                        <span class="block truncate font-normal" x-text="category.name"></span>
                    </div>

                    <button type="button" @click="$dispatch('open-modal', 'delete-category-' + category.id)" 
                            class="text-gray-400 hover:text-red-600 px-2 z-20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </li>
            </template>

            <li @click="$dispatch('open-modal', 'add-category-modal'); open = false" 
                class="relative cursor-pointer select-none py-3 pl-3 pr-9 text-indigo-600 hover:bg-indigo-50 border-t font-semibold">
                + Tambah Kategori Baru
            </li>
        </ul>
    </div>
    @error('category_id') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
</div>

<x-modal name="add-category-modal" focusable>
    <form method="POST" action="{{ route('admin.categories.store') }}" class="p-6">
        @csrf
        <h2 class="text-lg font-medium text-gray-900">Tambah Kategori Baru</h2>
        <div class="mt-4">
            <label class="block font-medium text-sm text-gray-700">Nama Kategori</label>
            <input type="text" name="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>
        <div class="mt-6 flex justify-end">
            <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-200 rounded-md mr-3">Batal</button>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Simpan</button>
        </div>
    </form>
</x-modal>

@foreach ($categories as $category)
    <x-modal name="delete-category-{{ $category->id }}" focusable>
        <form method="POST" action="{{ route('admin.categories.destroy', $category->id) }}" class="p-6">
            @csrf
            @method('DELETE')
            <h2 class="text-lg font-medium text-gray-900 text-red-600">Hapus Kategori?</h2>
            <p class="mt-1 text-sm text-gray-600">
                Anda yakin ingin menghapus kategori <strong>{{ $category->name }}</strong>?
            </p>
            <div class="mt-6 flex justify-end">
                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-200 rounded-md mr-3">Batal</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md">Ya, Hapus</button>
            </div>
        </form>
    </x-modal>
@endforeach
                            @error('category_id')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <label for="price" class="block font-medium text-sm text-gray-700">Harga (Rp)</label>
                            <input id="price" name="price" type="number" min="0" value="{{ old('price') }}" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                            @error('price')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <label for="description" class="block font-medium text-sm text-gray-700">Deskripsi (Opsional)</label>
                            <textarea id="description" name="description" rows="3" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mt-4">
                            <label for="is_available" class="block font-medium text-sm text-gray-700">Status Ketersediaan</label>
                            <select name="is_available" id="is_available" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="1" {{ old('is_available', 1) == 1 ? 'selected' : '' }}>Tersedia</option>
                                <option value="0" {{ old('is_available') == 0 ? 'selected' : '' }}>Habis</option>
                            </select>
                            @error('is_available')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                Batal
                            </a>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition duration-300">
                                Simpan Produk
                            </button>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>