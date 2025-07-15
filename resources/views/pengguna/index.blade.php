<!-- File: resources/views/admin/pengguna/index.blade.php -->
<x-app-layout>
    <div class="container mx-auto p-6 bg-white rounded shadow">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Daftar Pengguna</h1>
            <a href="{{ route('admin.pengguna.create') }}" class="px-4 py-2 bg-yellow-600 text-white rounded">
                Tambah Pengguna
            </a>
        </div>

        @if (session('success'))
            <div id="success-alert" class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div id="error-alert" class="mb-4 p-3 bg-red-100 text-red-800 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Alert untuk AJAX response -->
        <div id="ajax-alert" class="mb-4 p-3 rounded hidden"></div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-4 py-2">#</th>
                        <th class="px-4 py-2">Username</th>
                        <th class="px-4 py-2">Email</th>
                        <th class="px-4 py-2 text-center">Status</th>
                        <th class="px-4 py-2 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $i => $u)
                        <tr class="hover:bg-gray-50" data-user-id="{{ $u->id_pengguna }}">
                            <td class="px-4 py-2">{{ $i + 1 }}</td>
                            <td class="px-4 py-2">{{ $u->username }}</td>
                            <td class="px-4 py-2">{{ $u->email }}</td>

                            {{-- Status dengan Toggle Switch --}}
                            <td class="px-4 py-2 text-center">
                                <div class="inline-flex items-center space-x-2">
                                    <label class="relative inline-flex cursor-pointer">
                                        <input type="checkbox" data-id="{{ $u->id_pengguna }}"
                                            class="status-toggle sr-only peer"
                                            {{ $u->status === 'aktif' ? 'checked' : '' }} />
                                        <div
                                            class="w-10 h-6 bg-gray-300 rounded-full peer-checked:bg-green-500 transition duration-200">
                                        </div>
                                        <div
                                            class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full peer-checked:translate-x-4 transition duration-200">
                                        </div>
                                    </label>
                                    <span class="status-label text-xs font-medium">
                                        {{ $u->status === 'aktif' ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </div>
                            </td>

                            {{-- Aksi lainnya --}}
                            <td class="px-4 py-2 text-center">
                                <div class="flex justify-center space-x-2">
                                    <a href="{{ route('admin.pengguna.edit', $u->id_pengguna) }}"
                                        class="px-2 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.pengguna.destroy', $u->id_pengguna) }}"
                                        method="POST" class="inline-block"
                                        onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-2 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Loading Overlay --}}
    <div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-4 rounded">
            <div class="flex items-center space-x-2">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                <span>Mengubah status...</span>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Function untuk show alert
        function showAlert(message, type = 'success') {
            const alertDiv = document.getElementById('ajax-alert');
            alertDiv.className =
                `mb-4 p-3 rounded ${type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`;
            alertDiv.textContent = message;
            alertDiv.classList.remove('hidden');

            // Auto hide after 5 seconds
            setTimeout(() => {
                alertDiv.classList.add('hidden');
            }, 5000);
        }

        // Function untuk show/hide loading
        function toggleLoading(show = true) {
            const overlay = document.getElementById('loading-overlay');
            if (show) {
                overlay.classList.remove('hidden');
                overlay.classList.add('flex');
            } else {
                overlay.classList.add('hidden');
                overlay.classList.remove('flex');
            }
        }

        // Event listener untuk toggle switches
        document.querySelectorAll('.status-toggle').forEach(checkbox => {
            checkbox.addEventListener('change', async function() {
                const userId = this.dataset.id;
                const row = this.closest('tr');
                const statusLabel = row.querySelector('.status-label');
                const originalChecked = this.checked;

                // Konfirmasi
                const confirmed = confirm('Yakin ingin mengubah status pengguna ini?');
                if (!confirmed) {
                    this.checked = !this.checked; // Rollback
                    return;
                }

                try {
                    // Show loading
                    toggleLoading(true);

                    // AJAX Request menggunakan GET method
                    const response = await fetch(
                        `/auth/admin/pengguna/${userId}/toggle-status`, {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }

                    const data = await response.json();

                    if (data.success) {
                        // Update UI
                        statusLabel.textContent = data.label;

                        // Show success message
                        showAlert(data.message, 'success');

                        console.log('Status berhasil diubah:', data);
                    } else {
                        throw new Error(data.message || 'Gagal mengubah status');
                    }

                } catch (error) {
                    console.error('Error:', error);

                    // Rollback checkbox state
                    this.checked = !this.checked;

                    // Show error message
                    showAlert('Gagal mengubah status: ' + error.message, 'error');

                } finally {
                    // Hide loading
                    toggleLoading(false);
                }
            });
        });

        // Auto hide existing alerts
        const existingAlerts = document.querySelectorAll('#success-alert, #error-alert');
        existingAlerts.forEach(alert => {
            if (alert) {
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 5000);
            }
        });
    });
</script>
