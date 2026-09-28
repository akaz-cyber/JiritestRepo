<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class AddressController extends Controller
{
    public function index() {
        $addresses = Address::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('user.address.index', compact('addresses'));
    }

    public function create() {
        return view('user.address.create');
    }

    public function store(Request $request) {
        $data = $request->validate([
            'label'         => ['nullable','string','max:100'],
            // 'first_name'    => ['required','string'],
            // 'last_name'     => ['required','string'],
            'email'         => ['required','email'],
            'phone'         => ['required'],
            'province_id'   => ['required'],
            'province_name' => ['required'],
            'city_id'       => ['required'],
            'city_name'     => ['required'],
            'district_id'   => ['required'],
            'district_name' => ['required'],
            'post_code'     => ['required'],
            'sub_district_id'  => ['required'],
            'sub_district_name'=> ['required'],
            'address1'      => ['required','string'],
            'is_default'    => ['nullable','boolean'],
        ]);

        $data['user_id'] = auth()->id();
        $data['is_default'] = $request->boolean('set_default');

        $nameParts = explode(' ', auth()->user()->name, 2);
        $data['first_name'] = $nameParts[0];
        $data['last_name']  = isset($nameParts[1]) ? $nameParts[1] : '-';

        $address = Address::create($data);

        if ($address->is_default) {
            Address::where('user_id', auth()->id())
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        return redirect()->route('addresses.index')->with('success', 'Alamat berhasil ditambahkan!');
    }

    public function edit($encryptedId)
    {
        $id = Crypt::decryptString($encryptedId);

        $address = Address::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('user.address.edit', compact('address', 'encryptedId'));
    }

    public function update(Request $request, $encryptedId)
    {
        $id = Crypt::decryptString($encryptedId);

        $address = Address::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $data = $request->validate([
            'label'         => ['nullable','string','max:100'],
            // 'first_name'    => ['required','string'],
            // 'last_name'     => ['required','string'],
            'email'         => ['required','email'],
            'phone'         => ['required','string','max:30'],
            'province_id'   => ['required'],
            'province_name' => ['required'],
            'city_id'       => ['required'],
            'city_name'     => ['required'],
            'district_id'   => ['required'],
            'district_name' => ['required'],
            'post_code'     => ['required','string','max:20'],
            'sub_district_id'   => ['required'],
            'sub_district_name' => ['required'],
            'address1'      => ['required','string'],
            'set_default'   => ['nullable','boolean'],
        ]);

        $data['is_default'] = $request->boolean('set_default');
        $nameParts = explode(' ', auth()->user()->name, 2);
        $data['first_name'] = $nameParts[0];
        $data['last_name']  = isset($nameParts[1]) ? $nameParts[1] : '-';

        $address->update($data);

        if ($address->is_default) {
            Address::where('user_id', auth()->id())
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        return redirect()->route('addresses.index')->with('success', 'Alamat berhasil diperbarui!');
    }

    public function destroy($encryptedId)
    {
        $id = Crypt::decryptString($encryptedId);

        $address = Address::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $address->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Alamat dihapus'
        ]);
    }

    public function setDefault($encryptedId)
    {
        // decode ID
        $id = Crypt::decryptString($encryptedId);

        // ambil address
        $address = Address::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // matikan default lain
        Address::where('user_id', auth()->id())->update(['is_default' => false]);

        // set default
        $address->update(['is_default' => true]);

        return redirect()->route('addresses.index')->with('success', 'Alamat default diperbarui!');
    }

}
