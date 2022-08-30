<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AuthRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'nama' => 'required|string|max:50',
            'nik' => 'required|numeric|digits:16|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'tanggal_lahir' => 'required|date_format:Y-m-d',
            'jenis_kelamin' => 'required|string|max:1',
            'nomor_tlp' => 'required|regex:/(0)[0-9]{11}/',
            'alamat' => 'required|string',
            'pekerjaan' => 'required|string',
            'kewarganegaraan' => 'required|string',
            'role' => 'required|in:petugas,masyarakat'
        ];
    }
}
