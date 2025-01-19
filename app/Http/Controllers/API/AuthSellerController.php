<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

// Mail
use App\Mail\GeneralMailer;

// Helper
use App\Helper\ResponseFormatter;
use App\Helper\InputValidationHelper;

// Models
use App\Models\Seller;

class AuthSellerController extends Controller
{
    public function registration_seller(Request $request){
        // validation
        $validation = [
            'name' => 'required|string',
            'email'=> 'required|email|unique:sellers,email',
            'store_name' => 'required|string|unique:sellers,store_name',
        ];
        $message = [
            'required' => ':attribute tidak boleh kosong',
            'email' => ':attribute harus berupa email',
            'unique' => ':attribute sudah terdaftar',
            'string' => ':attribute harus berupa string'
        ];
        $name = [
            'name' => 'Nama',
            'email'=> 'Email',
            'store_name' => 'Nama Toko',
        ];
        $validator = Validator::make($request->all(), $validation, $message, $name);
        if ($validator->fails()) {
            return ResponseFormatter::error(400, $validator->errors(), 'Data tidak valid, periksa kembali input Anda.');
        }

        // DB Transaction
        DB::beginTransaction();
        try {
            // VALIDATION
            $seller = new Seller();
            $name = InputValidationHelper::validate_input_text($request->name);
            if(!$name){
                return ResponseFormatter::error(400, 'Nama tidak valid');
            }
            $seller->name = $name;

            $email = InputValidationHelper::validate_input_email($request->email);
            if(!$email){
                return ResponseFormatter::error(400, 'Email tidak valid');
            }
            $seller->email = $email;

            $store_name = InputValidationHelper::validate_input_text($request->store_name);
            if(!$store_name){
                return ResponseFormatter::error(400, 'Nama Toko tidak valid');
            }
            $seller->store_name = $store_name;

            // create OTP code 6 digit
            $otp_register = rand(100000, 999999);

            // check if OTP code already exist. if exist, create new OTP code, do loop until OTP code not exist
            $otpCount = Seller::where('otp_register', $otp_register)->count();
            if($otpCount > 0){
                do {
                    $otp_register = rand(100000, 999999);
                    $otpCount = Seller::where('otp_register', $otp_register)->count();
                } while ($otpCount > 0);
            }
            $seller->otp_register = $otp_register;

            // otp expired based on env OTP_SELLER_EXPIRED
            $otp_expired = now()->addMinutes((int) env('OTP_SELLER_EXPIRED', 5));
            $seller->otp_expired = $otp_expired;

            // change otp_expired to string and localize timezone
            $timezone = 'Asia/Jakarta';
            $otp_expired_text = $otp_expired->setTimezone($timezone)->format('Y-m-d H:i:s');

            $seller->save();
            
            $seller->otp_expired_text = $otp_expired_text;

            DB::commit();

            if ($seller) {
                try {
                    // Use GeneralMailer to send the OTP email
                    Mail::to($seller->email)->send(new GeneralMailer(
                        subject: 'Kode OTP Pendaftaran Anda',
                        view: 'mails.register-seller-otp', // Replace with the Blade view for your OTP email
                        data: [
                            'name' => $seller->name,
                            'otp' => $seller->otp_register,
                            'store_name' => $seller->store_name,
                        ]
                    ));
                } catch (\Exception $e) {
                    return ResponseFormatter::error(500, 'Email gagal dikirim: ' . $e->getMessage());
                }
            }

            // return success
            return ResponseFormatter::success([
                'seller' => $seller,
            ], 'Registrasi berhasil. Kode OTP telah dikirim ke email Anda.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $message = $e->getMessage();
            return ResponseFormatter::error(500, 'Gagal membuat akun' . $e->getMessage());
        }
    }

    public function verify_otp(Request $request){
        // validation
        $validation = [
            'email' => 'required|email|exists:sellers,email',
            'otp' => 'required|exists:sellers,otp_register',
        ];
        $message = [
            'required' => ':attribute tidak boleh kosong',
            'email' => ':attribute harus berupa email',
            'exists' => ':attribute tidak terdaftar',
        ];
        $name = [
            'email' => 'Email',
            'otp' => 'Kode OTP',
        ];
        $validator = Validator::make($request->all(), $validation, $message, $name);
        if ($validator->fails()) {
            return ResponseFormatter::error(400, $validator->errors(), 'Data tidak valid, periksa kembali input Anda.');
        }

        // DB Transaction
        DB::beginTransaction();
        try {
            // VALIDATION
            $email = InputValidationHelper::validate_input_email($request->email);
            if (!$email) {
                return ResponseFormatter::error(400, 'Email tidak valid');
            }

            $otp = InputValidationHelper::validate_input_text($request->otp);
            if (!$otp) {
                return ResponseFormatter::error(400, 'OTP tidak valid');
            }

            $seller = Seller::where('email', $email)->where('otp_register', $otp)->first();
            if(!$seller){
                return ResponseFormatter::error(400, 'Seller Tidak ditemukan');
            }

            // check if the seller already verified and active
            if($seller->status == 'active' || $seller->verified_at){
                return ResponseFormatter::error(400, 'Akun Anda sudah terverifikasi');
            }

            // check if OTP code is expired
            $now = now();
            if($now > $seller->otp_expired){
                return ResponseFormatter::error(400, 'Kode OTP sudah kadaluarsa, silakan minta kode OTP yang baru.');
            }

            $seller->verified_at = $now;
            $seller->status = 'active';
            $seller->otp_register = null;
            $seller->otp_expired = null;
            $seller->save();

            DB::commit();

            // return success
            return ResponseFormatter::success([
                'seller' => $seller,
            ], 'Verifikasi berhasil. Akun Anda telah diverifikasi.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $message = $e->getMessage();
            return ResponseFormatter::error(500, 'Gagal verifikasi akun' . $message);
        }
    }

    public function resend_otp(Request $request){
        // validation
        $validation = [
            'email' => 'required|email|exists:sellers,email',
        ];
        $message = [
            'required' => ':attribute tidak boleh kosong',
            'email' => ':attribute harus berupa email',
            'exists' => ':attribute tidak terdaftar',
        ];
        $name = [
            'email' => 'Email',
        ];
        $validator = Validator::make($request->all(), $validation, $message, $name);
        if($validator->fails()){
            return ResponseFormatter::error(400, $validator->errors(), 'Data tidak valid, periksa kembali input Anda.');
        }

        // DB Transaction
        DB::beginTransaction();
        try {
            $email = InputValidationHelper::validate_input_email($request->email);
            if (!$email) {
                return ResponseFormatter::error(400, 'Email tidak valid');
            }

            // find seller by email
            $seller = Seller::where('email', $email)->first();
            if (!$seller) {
                return ResponseFormatter::error(400, 'Email tidak terdaftar');
            }

            // check if seller already verified and active
            if ($seller->status == 'active' || $seller->verified_at) {
                return ResponseFormatter::error(400, 'Akun Anda sudah terverifikasi');
            }

            // create OTP code 6 digit
            $otp_register = rand(100000, 999999);

            // check if OTP code already exist. if exist, create new OTP code, do loop until OTP code not exist
            $otpCount = Seller::where('otp_register', $otp_register)->count();
            if ($otpCount > 0) {
                do {
                    $otp_register = rand(100000, 999999);
                    $otpCount = Seller::where('otp_register', $otp_register)->count();
                } while ($otpCount > 0);
            }
            $seller->otp_register = $otp_register;

            // otp expired based on env OTP_SELLER_EXPIRED
            $otp_expired = now()->addMinutes((int) env('OTP_SELLER_EXPIRED', 5));
            $seller->otp_expired = $otp_expired;

            // change otp_expired to string and localize timezone
            $timezone = 'Asia/Jakarta';
            $otp_expired_text = $otp_expired->setTimezone($timezone)->format('Y-m-d H:i:s');
            
            $seller->save();

            $seller->otp_expired_text = $otp_expired_text;

            DB::commit();

            // return success
            return ResponseFormatter::success([
                'seller' => $seller,
            ], 'Registrasi berhasil. Kode OTP telah dikirim ke email Anda.');            

        } catch (\Exception $e) {
            DB::rollBack();
            $message = $e->getMessage();
            return ResponseFormatter::error(500, 'Gagal mengirim ulang kode OTP' . $message);
        }
    }

    public function register_step_2(Request $request){
        // Validation
        $validation = [
            'name' => 'nullable|string',
            'email' => 'required|email|exists:sellers,email',
            'store_name' => 'nullable|string',
            'phone'=> 'nullable',
            'gender'=> 'nullable',
            'birth_date'=> 'nullable',
            'photo'=> 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'password' => [
                'required',
                'confirmed',
                'string',
                'min:8',                                // must be at least 8 characters in length
                'regex:/[a-z]/',                        // must contain at least one lowercase letter
                'regex:/[A-Z]/',                        // must contain at least one uppercase letter
                'regex:/[0-9]/',                        // must contain at least one digit numeric
                'regex:/[?!@#$%^&*~`_+=:;.,"><\'-]/',   // must contain a special character
            ],
        ];
        $message = [
            'required' => ':attribute tidak boleh kosong',
            'email' => ':attribute harus berupa email',
            'exists' => ':attribute tidak terdaftar',
            'string' => ':attribute harus berupa string',
            'regex' => ':attribute harus mengandung huruf kecil, huruf besar, angka, dan karakter spesial',
            'min' => ':attribute minimal :min karakter',
            'confirmed' => 'Konfirmasi :attribute tidak cocok',
            'image' => ':attribute harus berupa gambar',
            'mimes' => ':attribute harus berformat jpg, jpeg, png',
            'max' => ':attribute tidak boleh lebih dari :max KB',
        ];
        $name = [
            'name' => 'Nama',
            'email' => 'Email',
            'store_name' => 'Nama Toko',
            'phone' => 'Nomor Telepon',
            'gender'=> 'Gender',
            'birth_date'=> 'Tanggal Lahir',
            'photo'=> 'Photo',
            'password' => 'Password',
        ];
        $validator = Validator::make($request->all(), $validation, $message, $name);
        if ($validator->fails()) {
            return ResponseFormatter::error(400, $validator->errors(), 'Data tidak valid, periksa kembali input Anda.');
        }

        // DB Transaction
        DB::beginTransaction();
        try{
            $email = InputValidationHelper::validate_input_email($request->email);
            if (!$email) {
                return ResponseFormatter::error(400, 'Email tidak valid');
            }

            // find seller by email
            $seller = Seller::where('email', $email)
                ->first();

            if (!$seller) {
                return ResponseFormatter::error(400, 'Email tidak terdaftar');
            }

            // check if seller not verified and active
            if ($seller->status != 'active' || !$seller->verified_at) {
                return ResponseFormatter::error(400, 'Akun Anda sudah terverifikasi');
            }

            $name = null;
            if($request->name){
                $name = InputValidationHelper::validate_input_text($request->name);
                if (!$name) {
                    return ResponseFormatter::error(400, 'Nama tidak valid');
                }
                $seller->name = $name;
            } else{
                $seller->name = $seller->name;
            }

            $store_name = null;
            if($request->store_name){
                $store_name = InputValidationHelper::validate_input_text($request->store_name);
                if (!$store_name) {
                    return ResponseFormatter::error(400, 'Nama tidak valid');
                }

                // check if the store name is the correct store name, if not, check if the store name already exists
                if ($store_name != $seller->store_name) {
                    $store_name_count = Seller::where('store_name', $store_name)->count();
                    if ($store_name_count > 0) {
                        return ResponseFormatter::error(400, 'Nama Toko sudah terdaftar');
                    }
                }
                $seller->store_name = $store_name;
            } else {
                $seller->store_name = $seller->store_name;
            }


            $phone = null;
            if($request->phone){
                $phone = InputValidationHelper::validate_input_text($request->phone);
                if (!$phone) {
                    return ResponseFormatter::error(400, 'Nama tidak valid');
                }
            }
            $seller->phone = $phone;

            $gender = null;
            if($request->gender){
                $gender = InputValidationHelper::validate_input_text($request->gender);
                if (!$gender) {
                    return ResponseFormatter::error(400, 'Nama tidak valid');
                }
            }
            $seller->gender = $gender;

            $birth_date = null;
            if($request->birth_date){
                $birth_date = InputValidationHelper::validate_input_text($request->birth_date);
                if (!$birth_date) {
                    return ResponseFormatter::error(400, 'Nama tidak valid');
                }
            }
            $seller->birth_date = $birth_date;

            // upload photo
            if($request->file('photo')){
                $photo = $request->file('photo')->store('assets/seller', 'public');
                $seller->photo = $photo;
            }

            $seller->password = Hash::make($request->password);
            $seller->save();

            DB::commit();

            // return success
            return ResponseFormatter::success([
                'seller' => $seller,
            ], 'Registrasi berhasil. Akun Anda sudah terdaftar dengan lengkap.');

        } catch (\Exception $e) {
            DB::rollBack();
            $message = $e->getMessage();
            return ResponseFormatter::error(500, 'Gagal membuat akun' . $message);
        }
    }

    public function login(Request $request){
        // validation
        $validation = [
            'email' => 'required|email',
            'password' => [
                'required',
                'string',
                'min:8',                                // must be at least 8 characters in length
                'regex:/[a-z]/',                        // must contain at least one lowercase letter
                'regex:/[A-Z]/',                        // must contain at least one uppercase letter
                'regex:/[0-9]/',                        // must contain at least one digit numeric
                'regex:/[?!@#$%^&*~`_+=:;.,"><\'-]/',   // must contain a special character
            ],
        ];
        $message = [
            'required' => ':attribute tidak boleh kosong',
            'email' => ':attribute harus berupa email',
            'string' => ':attribute harus berupa string',
            'regex' => ':attribute harus mengandung huruf kecil, huruf besar, angka, dan karakter spesial',
            'min' => ':attribute minimal :min karakter',
        ];
        $name = [
            'email' => 'Email',
            'password' => 'Password',
        ];
        $validator = Validator::make($request->all(), $validation, $message, $name);
        if ($validator->fails()) {
            return ResponseFormatter::error(400, $validator->errors(), 'Data tidak valid, periksa kembali input Anda.');
        }

        // DB Transaction
        DB::beginTransaction();
        try {
            $email = InputValidationHelper::validate_input_email($request->email);
            if (!$email) {
                return ResponseFormatter::error(400, 'Email tidak valid');
            }

            $password = InputValidationHelper::validate_input_text($request->password);
            if (!$password) {
                return ResponseFormatter::error(400, 'Password tidak valid');
            }

            $seller = Seller::where('email', $email)->first();
            if (!$seller) {
                return ResponseFormatter::error(400, 'Email tidak terdaftar');
            }

            if (!Hash::check($password, $seller->password)) {
                return ResponseFormatter::error(400, 'Password salah');
            }

            // check if seller already verified and active
            if ($seller->status != 'active' || !$seller->verified_at) {
                return ResponseFormatter::error(400, 'Akun Anda belum terverifikasi');
            }

            // if seller already verified and active, delete all token
            $seller->tokens()->delete();

            // create new token
            $token = $seller->createToken('seller_token')->plainTextToken;

            DB::commit();

            // return success
            return ResponseFormatter::success([
                'seller' => $seller,
                'token_type' => 'Bearer',
                'token' => $token,
            ], 'Login berhasil');

        } catch (\Exception $e) {
            DB::rollBack();
            $message = $e->getMessage();
            return ResponseFormatter::error(500, 'Gagal login' . $message);
        }
    }

    public function logout(Request $request){
        // DB Transaction
        DB::beginTransaction();
        try {
            $token = $request->user()->currentAccessToken();
            if(!$token){
                return ResponseFormatter::error(400, 'Token tidak valid');
            }

            $token->delete();

            DB::commit();

            // return success
            return ResponseFormatter::success([], 'Logout berhasil');

        } catch (\Exception $e) {
            DB::rollBack();
            $message = $e->getMessage();
            return ResponseFormatter::error(500, 'Gagal logout' . $message);
        }
    }

    // test get seller data
    public function get_seller($id, Request $request){
        $seller = Seller::find($id);
        if(!$seller){
            return ResponseFormatter::error(404, 'Seller tidak ditemukan');
        }

        return ResponseFormatter::success($seller, 'Data seller berhasil diambil');
    }
}
