<x-app-layout>

<x-slot name="header">
    <h2 class="text-2xl font-bold tracking-wide text-slate-800 dark:text-white">
        📄 Manajemen Request Dokumen
    </h2>
</x-slot>

<div class="p-8">

    <!-- Premium Card -->
    <div class="bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl shadow-2xl rounded-2xl p-6 border border-slate-200 dark:border-slate-700">

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">

                <thead>
                    <tr class="text-left text-slate-600 dark:text-slate-300 border-b border-slate-200 dark:border-slate-700">
                        <th class="p-4">Mahasiswa</th>
                        <th class="p-4">Tipe</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody id="tableBody" class="text-slate-700 dark:text-slate-200">

                    @foreach($requests as $req)
                    <tr id="row-{{ $req->id }}"
                        class="border-b border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition duration-300">

                        <td class="p-4 font-medium">
                            {{ $req->mahasiswa->nama ?? '-' }}
                        </td>

                        <td class="p-4">
                            {{ $req->tipeDkmn }}
                        </td>

                        <td class="p-4">
                            <span id="status-{{ $req->id }}"
                                class="px-3 py-1 rounded-full text-xs font-semibold
                                {{ $req->status->value == 'approved' ?  'bg-emerald-100 text-emerald-700'
                                : ($req->status == 'rejected' ? 'bg-red-100 text-red-700'
                                : 'bg-amber-100 text-amber-700') }}">
                                {{ ucfirst($req->status?->value ?? '-') }}
                            </span>
                        </td>

                        <td class="p-4 text-center space-x-2">
                            <button onclick="showDetail({{ $req->id }})"
                                class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold transition">
                                Detail
                            </button>

                            <button onclick="deleteReq({{ $req->id }})"
                                class="px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold transition">
                                Hapus
                            </button>
                        </td>

                    </tr>
                    @endforeach

                </tbody>

            </table>
        </div>
    </div>
</div>

<!-- PREMIUM MODAL -->
<div id="modal"
    class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50">

    <div
        class="bg-white dark:bg-slate-900 w-[600px] rounded-2xl shadow-2xl p-8 border border-slate-200 dark:border-slate-700 animate-fadeIn">

        <h3 class="text-xl font-bold mb-6 text-slate-800 dark:text-white">
            Detail Request
        </h3>

        <div id="modalContent" class="space-y-2 text-sm text-slate-600 dark:text-slate-300"></div>

        <form id="uploadForm" class="mt-6 space-y-4">
            @csrf
            <input type="file" name="file" accept="application/pdf"
                class="w-full border border-slate-300 dark:border-slate-700 rounded-lg p-2 text-sm bg-transparent">

            <button type="submit"
                class="w-full py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold transition">
                Upload PDF & Approve
            </button>
        </form>

        <button onclick="closeModal()"
            class="mt-4 text-slate-500 hover:text-slate-700 text-sm">
            Tutup
        </button>
    </div>
</div>

<!-- Toast -->
<div id="toast"
    class="fixed bottom-6 right-6 bg-slate-900 text-white px-6 py-3 rounded-xl shadow-xl hidden">
</div>

<script>

let currentId = null;

/* ================= DETAIL ================= */

function showDetail(id)
{
    currentId = id;

    fetch('/kln/dokumen/' + id)
    .then(res => res.json())
    .then(data => {

        document.getElementById('modalContent').innerHTML = `
            <p><b>Mahasiswa:</b> ${data.mahasiswa?.nama ?? '-'}</p>
            <p><b>Tipe:</b> ${data.tipeDkmn}</p>
            <p><b>Status:</b> ${data.status}</p>
            <p><b>Keterangan:</b> ${data.message ?? '-'}</p>
        `;

        document.getElementById('modal').classList.remove('hidden');
        document.getElementById('modal').classList.add('flex');
    });
}

function closeModal()
{
    document.getElementById('modal').classList.add('hidden');
    document.getElementById('modal').classList.remove('flex');
}

/* ================= DELETE TANPA RELOAD ================= */

function deleteReq(id)
{
    if(!confirm('Yakin hapus request ini?')) return;

    fetch('/kln/dokumen/' + id, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(() => {
        document.getElementById('row-' + id).remove();
        showToast('Request berhasil dihapus');
    });
}

/* ================= UPLOAD TANPA RELOAD ================= */

document.getElementById('uploadForm').addEventListener('submit', function(e){

    e.preventDefault();

    let formData = new FormData(this);

    fetch('/kln/dokumen/' + currentId + '/upload', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(() => {

        document.getElementById('status-' + currentId).innerHTML = 'Approved';
        document.getElementById('status-' + currentId).className =
            "px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700";

        closeModal();
        showToast('Dokumen berhasil di-approve 🎉');
    });
});

/* ================= TOAST ================= */

function showToast(message)
{
    let toast = document.getElementById('toast');
    toast.innerText = message;
    toast.classList.remove('hidden');

    setTimeout(() => {
        toast.classList.add('hidden');
    }, 3000);
}

</script>

<style>
@keyframes fadeIn {
    from {opacity:0; transform:scale(.95);}
    to {opacity:1; transform:scale(1);}
}
.animate-fadeIn {
    animation: fadeIn .2s ease-out;
}
</style>

</x-app-layout>