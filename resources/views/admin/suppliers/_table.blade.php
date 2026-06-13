<div class="overflow-x-auto w-full">
    <table class="w-full text-sm min-w-[800px]">
        <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
                <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama Supplier</th>
                <th class="text-left px-4 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Kontak</th>
                <th class="text-left px-4 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Alamat</th>
                <th class="text-center px-4 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Transaksi</th>
                <th class="text-center px-4 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                <th class="text-center px-4 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @forelse($suppliers as $sup)
            <tr class="hover:bg-slate-50/50 transition-colors">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-violet-100 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-truck text-violet-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-800">{{ $sup->nama_supplier }}</p>
                            @if($sup->email)
                            <p class="text-xs text-slate-400">{{ $sup->email }}</p>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-4 py-4">
                    @if($sup->kontak)
                    <a href="tel:{{ $sup->kontak }}" class="text-blue-600 hover:underline text-sm flex items-center gap-1">
                        <i class="fa-solid fa-phone text-xs"></i> {{ $sup->kontak }}
                    </a>
                    @else
                    <span class="text-slate-300 text-sm">—</span>
                    @endif
                </td>
                <td class="px-4 py-4 text-xs text-slate-500 max-w-xs">
                    <p class="line-clamp-2">{{ $sup->alamat ?? '—' }}</p>
                </td>
                <td class="px-4 py-4 text-center">
                    <span class="text-sm font-semibold text-slate-700">{{ number_format($sup->stock_mutations_count) }}</span>
                    <p class="text-xs text-slate-400">mutasi</p>
                </td>
                <td class="px-4 py-4 text-center">
                    <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full
                        {{ $sup->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                        <i class="fa-solid fa-circle text-xs"></i>
                        {{ $sup->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
                <td class="px-4 py-4">
                    <div class="flex items-center justify-center gap-1.5">
                        <a href="{{ route('admin.suppliers.edit', $sup) }}" title="Edit"
                            class="p-2 bg-blue-50 border border-blue-100 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors">
                            <i class="fa-solid fa-pen text-sm"></i>
                        </a>
                        <form action="{{ route('admin.suppliers.toggle', $sup) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" title="{{ $sup->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                class="p-2 {{ $sup->is_active ? 'bg-amber-50 border border-amber-100 text-amber-600 hover:bg-amber-100' : 'bg-emerald-50 border border-emerald-100 text-emerald-600 hover:bg-emerald-100' }} rounded-lg transition-colors">
                                <i class="fa-solid {{ $sup->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }} text-sm"></i>
                            </button>
                        </form>
                        @if($sup->stock_mutations_count === 0)
                        <form action="{{ route('admin.suppliers.destroy', $sup) }}" method="POST" id="del-sup-{{ $sup->id }}">
                            @csrf @method('DELETE')
                            <button type="button" onclick="confirmDelete('del-sup-{{ $sup->id }}', '{{ $sup->nama_supplier }}')"
                                class="p-2 bg-red-50 border border-red-100 text-red-500 hover:bg-red-100 rounded-lg transition-colors">
                                <i class="fa-solid fa-trash text-sm"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-16 text-center">
                    <i class="fa-solid fa-truck text-slate-200 text-4xl mb-3"></i>
                    <p class="text-slate-400 font-medium">Supplier tidak ditemukan</p>
                    <p class="text-slate-300 text-xs mt-1">Coba masukkan kata kunci pencarian yang lain.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($suppliers->hasPages())
<div class="px-6 py-4 border-t border-slate-100">
    {{ $suppliers->links() }}
</div>
@endif
