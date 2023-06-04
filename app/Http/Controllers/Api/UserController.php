<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user) {
            if (password_verify($request->password, $user->password)) {
                return response()->json([
                    'succes'    => 1,
                    'message'   => 'Selamat datang ' . $user->name,
                    'user'      => $user
                ]);
            } else {
                return $this->error('Password salah');
            }
        }

        return $this->error('Email tidak terdaftar');
    }

    public function register(Request $request)
    {
        $validasi = Validator::make($request->all(), [
            'name'      => 'required',
            'email'     => 'required|unique:users',
            'password'  => 'required|min:6'
        ]);

        if ($validasi->fails()) {
            $val = $validasi->errors()->all();

            return $this->error($val[0]);
        } else {

            $user = User::create(array_merge($request->all(), [
                'password' => bcrypt($request->password)
            ]));
           

            if ($user) {
                return response()->json([
                    'succes'    => 1,
                    'message'   => 'Pendafataran Berhasil',
                    'user'      => $user
                ]); 
            } else {
                return $this->error('Pendaftaran Gagal');
            }
        }
    }

    public function error($pesan)
    {
        return response()->json([
            'succes'    => 0,
            'message'   => $pesan
        ]);
    }
}
