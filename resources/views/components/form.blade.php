@props([
    'route',
    'method' => 'POST',
    'id' => 'this_form',
    'class' => null,
    'cancelRoute' => null,
    'base_route' => null,
])
<form action="{{ $route }}" @if ($base_route != null) base-route="{{ $base_route }}" @endif
    id="{{ $id }}" class="{{ $class }} flex flex-col gap-11 w-full max-w-100 mx-auto"
    method="{{ $method }}" enctype="multipart/form-data">
    @csrf

    @if ($errors->any())
        <div class="mb-4 rounded-md border border-red-300 bg-red-50 p-3 text-red-800">
            <div class="font-semibold mb-2">
                Terdapat {{ $errors->count() }} kesalahan pada input:
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{ $slot }}


    <div class="flex justify-end gap-3 mt-6">
        @if ($cancelRoute)
            <a href="{{ $cancelRoute }}"
                class="px-6 py-2 active:scale-95 bg-gray-300 text-gray-700 rounded-md font-medium hover:bg-gray-400 transition">
                Batal
            </a>
        @endif
        <button type="submit" id="button_{{ $id }}" onclick="form_loading(this)"
            class="px-6 py-2 active:scale-95 bg-black text-white rounded-md font-medium hover:bg-gray-800 transition flex items-center gap-2">

            <?xml version="1.0" encoding="utf-8"?>
            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" fill="white" viewBox="0 0 122.88 108.95"
                style="enable-background:new 0 0 122.88 108.95" xml:space="preserve">
                <g>
                    <path
                        d="M23.01,0h76.87c6.33,0,12.08,2.59,16.25,6.76c4.17,4.17,6.76,9.92,6.76,16.25v62.93c0,6.33-2.59,12.08-6.76,16.25 c-4.17,4.17-9.92,6.76-16.25,6.76H23.01c-6.33,0-12.08-2.59-16.25-6.76C2.59,98.02,0,92.27,0,85.94V23.01 c0-6.33,2.59-12.08,6.76-16.25C10.92,2.59,16.68,0,23.01,0L23.01,0z M4.95,77.41c0.86,3.23,2.57,6.12,4.86,8.41 c3.38,3.38,8.05,5.49,13.19,5.49h76.87c0.2,0,0.41,0,0.61-0.01l1.27-0.09c4.38-0.44,8.34-2.43,11.3-5.39c2.3-2.3,4-5.19,4.86-8.41 V23.01c0-4.96-2.03-9.47-5.31-12.75c-3.27-3.27-7.79-5.31-12.75-5.31H23.01c-4.96,0-9.48,2.03-12.75,5.3 c-3.27,3.27-5.3,7.79-5.3,12.75V77.41L4.95,77.41z M84.7,62.77H61.86c0.26-2.38,1.06-4.63,2.38-6.72c1.33-2.1,3.81-4.59,7.46-7.44 c2.23-1.75,3.66-3.08,4.29-3.99c0.63-0.91,0.94-1.77,0.94-2.59c0-0.88-0.31-1.64-0.92-2.27c-0.62-0.63-1.4-0.94-2.33-0.94 c-0.97,0-1.77,0.32-2.38,0.97c-0.62,0.65-1.03,1.79-1.24,3.44l-7.61-0.65c0.3-2.27,0.85-4.04,1.65-5.31 c0.8-1.27,1.92-2.25,3.37-2.93c1.45-0.68,3.46-1.02,6.03-1.02c2.68,0,4.76,0.32,6.25,0.97c1.49,0.64,2.66,1.63,3.51,2.97 c0.85,1.34,1.28,2.84,1.28,4.5c0,1.76-0.49,3.45-1.47,5.06c-0.98,1.6-2.75,3.37-5.34,5.29c-1.53,1.12-2.56,1.91-3.07,2.35 c-0.52,0.45-1.12,1.03-1.82,1.76H84.7V62.77L84.7,62.77z M38.18,33.8h20.93v6.23H46.67v5.06h10.64v5.87H46.67v11.81h-8.49V33.8 L38.18,33.8z" />
                </g>
            </svg>
            Simpan Data


        </button>
    </div>
</form>


<script>
    function form_loading(elemen) {
        console.log(elemen.checkValidity())
        if (!elemen.closest('form').checkValidity()) {
            console.log('masuk', 'cek')
            Pop_message('Validasi Data', 'Silakan periksa kembali dan lengkapi semua field yang bertanda *.', false,
                'warning');
            return;
        } {
            console.log('masuk', 'proses')
            Pop_message('Mohon Tunggu....', 'Sedang melakukan validasi data', true);
        }

    }

    document.addEventListener('keydown', function(e) {
        if (e.key === "F2" || e.keyCode === 114) {
            console.log('masuk f2')
            e.preventDefault(); // cegah fungsi default (kalau ada)
            document.getElementById('button_{{ $id }}').click();

        }
    });
</script>
