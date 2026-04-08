<?php

namespace App\Http\Controllers;

use App\Models\Emergency_contact;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmergencyContactController extends Controller
{
    /**
     * Method helper clear cache dihapus karena sudah tidak menggunakan caching.
     */

    public function list($id_User)
    {
        /**
         * Mengambil data langsung dari database tanpa Cache.
         */
        $kontaks = Emergency_contact::where('users_id', $id_User)->get();

        /**
         * Mengambil data user dari ProfileController.
         */
        $user = (new ProfileController)->based_user_data($id_User);

        return view('kelola_data.emergency_contact.list', compact('kontaks', 'user'));
    }

    public function new($id_User)
    {
        $user = (new ProfileController)->based_user_data($id_User);
        return view('kelola_data.emergency_contact.input', compact('user'));
    }

    public function new_data(Request $request, $id_User)
    {
        $validated = $this->validation($request);

        try {
            $validated['users_id'] = $id_User;

            // Memanggil method create di bawah
            $this->create($request);
            
            return redirect(route('manage.emergency-contact.list', ['id_User' => $id_User]))
                ->with('success', 'Emergency contact berhasil dibuat.');
        } catch (\Exception $e) {
            // Rollback jika terjadi error pada database di method create
            DB::rollBack();

            return redirect()->back()
                ->with('message', 'Emergency contact Gagal Dibuat, Berikut alannya: ' . $e->getMessage());
        }
    }

    public function create(Request $request)
    {
        $validated = $this->validation($request);

        try {
            $cek_hp = Emergency_contact::where('telepon', $request['telepon'])
                ->where('users_id', $request['users_id'])
                ->first();
            $cek_email = Emergency_contact::where('email', $request['email'])
                ->where('users_id', $request['users_id'])
                ->first();

            if ($cek_hp == null && $cek_email == null) {
                DB::beginTransaction();
                
                $validated['users_id'] = $request['users_id'];
                $emergency_contact_save = Emergency_contact::create($validated);

                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil membuat Kontak Darurat',
                    'error'   => $emergency_contact_save
                ], 200);
            } else {
                $message = [];

                if ($cek_hp != null) {
                    $message[] = 'Nomor Telepon: ' . $request['telepon'];
                }

                if ($cek_email != null) {
                    $message[] = 'Email: ' . $request['email'];
                }

                $finalMessage = implode("\n", $message);
                return response()->json([
                    'success' => false, 
                    'error' => 'Data Emergency ini sudah terdaftar atau terpakai. Berikut detailnya: ' . $finalMessage
                ], 422);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat Kontak Darurat',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function validation(Request $request)
    {
        return $request->validate(
            [
                'nama_lengkap' => [
                    'required',
                    'string',
                    'max:200',
                    "regex:/^(?=.*[A-Za-z])[A-Za-z' ]+$/"
                ],
                'status_hubungan' => 'required|string|max:255',
                'telepon'         => [
                    'required',
                    'string',
                    'max:15',
                    'regex:/^(?![ +-])(?!.*\+\+)(?!.*--)(?=.*\d)[0-9+\- ]+$/'
                ],
                'email'           => 'required|email|max:100',
                'alamat'          => 'required|string|max:300',
            ],
            [
                'required' => ':attribute Kontak Darurat wajib diisi.',
                'max'      => ':attribute Kontak Darurat maksimal :max karakter.',
                'string'   => ':attribute Kontak Darurat harus berupa text.',
                'email.email' => 'Format Email Kontak Darurat tidak valid.',

                'nama_lengkap.regex' => 'Nama Lengkap Kontak Darurat hanya boleh berisi huruf, spasi, dan tanda petik (\') serta harus mengandung minimal 1 huruf.',
                'telepon.regex'      => 'Telepon Kontak Darurat hanya boleh berisi angka, spasi, tanda + dan -, tidak boleh diawali spasi atau -, dan harus mengandung angka.',
            ]
        );
    }

    public function updateView($id_User, $id_emergency_contact)
    {
        $ec = Emergency_contact::where('id', $id_emergency_contact)->first();
        $user = (new ProfileController)->based_user_data($id_User);
        return view('kelola_data.emergency_contact.update', ['data' => $ec, 'user' => $user]);
    }

    public function updateData(Request $request, $id_User, $id_emergency_contact)
    {
        $validated = $this->validation($request); 

        $ec = Emergency_contact::where('id', $id_emergency_contact)->first();

        DB::beginTransaction(); 

        try {
            if (!$ec) {
                throw new \Exception('Data Emergency Contact tidak ditemukan.');
            }

            $ec->update($validated);

            DB::commit();

            // Bagian Cache Forget telah dihapus

            if (session('account')['is_admin'] && $id_User != session('account')['id']) {
                return redirect(route('manage.emergency-contact.list', ['id_User' => $id_User]))
                    ->with('success', 'Kontak darurat berhasil diperbarui.');
            } else {
                return redirect(route('profile.emergency-contacts.list', ['id_User' => session('account')['id']]))
                    ->with('success', 'Kontak darurat berhasil diperbarui.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error update emergency contact: ' . $e->getMessage());

            return redirect()->back()
                ->with('message', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}