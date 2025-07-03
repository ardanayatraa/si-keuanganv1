<x-app-layout>
    <div class="container mx-auto p-6 bg-white rounded shadow">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Daftar Pengguna</h1>
            <a href="{{ route('admin.pengguna.create') }}" class="px-4 py-2 bg-yellow-600 text-white rounded">Tambah
                Pengguna</a>
        </div>

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-4 py-2">#</th>
                        <th class="px-4 py-2">Username</th>
                        <th class="px-4 py-2">Email</th>
                        <th class="px-4 py-2 text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $i => $u)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $i + 1 }}</td>
                            <td class="px-4 py-2">{{ $u->username }}</td>
                            <td class="px-4 py-2">{{ $u->email }}</td>

                            {{-- Toggle switch --}}
                            <td class="px-4 py-2 text-center">
                                <div class="inline-flex items-center space-x-2">
                                    {{-- Hidden fallback --}}
                                    <input type="hidden" name="status" value="nonaktif">
                                    {{-- Label wrapper perlu class relative --}}
                                    <label class="relative inline-flex cursor-pointer">
                                        <input type="checkbox" data-id="{{ $u->id_pengguna }}"
                                            class="status-toggle sr-only peer"
                                            {{ $u->status === 'aktif' ? 'checked' : '' }} />
                                        <div
                                            class="w-10 h-6 bg-gray-300 rounded-full peer-checked:bg-green-500 transition">
                                        </div>
                                        <div
                                            class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full
                                peer-checked:translate-x-4 transition">
                                        </div>
                                    </label>
                                    <span class="status-label text-xs font-medium">
                                        {{ $u->status === 'aktif' ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- CSRF token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
</x-app-layout>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.status-toggle').forEach(chk => {
            chk.addEventListener('change', async function() {
                const id = this.dataset.id;
                const confirmed = confirm('Yakin ingin mengubah status pengguna ini?');

                // jika user batal, kembalikan posisi awal
                if (!confirmed) {
                    this.checked = !this.checked;
                    return;
                }

                try {
                    const res = await fetch(
                        `/auth/admin/pengguna/${id}/toggle-status`, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                        }
                    );
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    const data = await res.json();

                    // update UI: checkbox sudah di-handle browser, cukup perbarui teks
                    const row = this.closest('tr');
                    const label = row.querySelector('.status-label');
                    label.textContent = data.label;

                } catch (err) {
                    alert('Gagal mengubah status. Silakan coba lagi.');
                    // rollback kalau error
                    this.checked = !this.checked;
                }
            });
        });
    });
</script>
