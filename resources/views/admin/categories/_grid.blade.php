{{-- Grid Kategori --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    @forelse($categories as $cat)
    <div class="bg-white rounded-2xl shadow-sm border {{ $cat->is_active ? 'border-surface-200' : 'border-slate-200 opacity-60' }} p-5 hover:shadow-md transition-all group">
        <div class="flex items-start justify-between mb-3">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0
                {{ $cat->is_active ? 'bg-blue-100' : 'bg-slate-100' }}">
                <i class="{{ $cat->icon ?? 'fa-solid fa-tag' }} {{ $cat->is_active ? 'text-blue-600' : 'text-slate-400' }} text-lg"></i>
            </div>
            <div class="flex items-center gap-1">
                <a href="{{ route('admin.categories.edit', $cat) }}" title="Edit"
                    class="p-1.5 bg-blue-50 border border-blue-100 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors">
                    <i class="fa-solid fa-pen text-xs"></i>
                </a>
                <form action="{{ route('admin.categories.toggle', $cat) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit" title="{{ $cat->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                        class="p-1.5 {{ $cat->is_active ? 'bg-amber-50 border border-amber-100 text-amber-500 hover:bg-amber-100' : 'bg-emerald-50 border border-emerald-100 text-emerald-600 hover:bg-emerald-100' }} rounded-lg transition-colors">
                        <i class="fa-solid {{ $cat->is_active ? 'fa-eye-slash' : 'fa-eye' }} text-xs"></i>
                    </button>
                </form>
                @if($cat->products_count === 0)
                <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" id="del-cat-{{ $cat->id }}">
                    @csrf @method('DELETE')
                    <button type="button" onclick="confirmDelete('del-cat-{{ $cat->id }}', '{{ $cat->nama_kategori }}')"
                        class="p-1.5 bg-red-50 border border-red-100 text-red-500 hover:bg-red-100 rounded-lg transition-colors">
                        <i class="fa-solid fa-trash text-xs"></i>
                    </button>
                </form>
                @endif
            </div>
        </div>

        <h3 class="font-bold text-slate-800 text-sm mb-1">{{ $cat->nama_kategori }}</h3>
        <p class="text-xs text-slate-400 line-clamp-2 mb-3">{{ $cat->deskripsi ?? 'Tidak ada deskripsi' }}</p>

        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-slate-600">
                <i class="fa-solid fa-boxes-stacked text-slate-400 mr-1"></i>
                {{ $cat->products_count }} produk
            </span>
            <span class="text-xs px-2 py-0.5 rounded-full font-semibold
                {{ $cat->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                {{ $cat->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
        </div>
    </div>
    @empty
    <div class="col-span-full bg-white rounded-2xl border border-surface-200 px-6 py-16 text-center">
        <i class="fa-solid fa-tags text-slate-200 text-4xl mb-3"></i>
        <p class="text-slate-400 font-medium">Kategori tidak ditemukan</p>
        <p class="text-slate-300 text-xs mt-1">Coba masukkan kata kunci pencarian yang lain.</p>
    </div>
    @endforelse
</div>

@if($categories->hasPages())
<div class="mt-6 flex justify-end">
    {{ $categories->links() }}
</div>
@endif
