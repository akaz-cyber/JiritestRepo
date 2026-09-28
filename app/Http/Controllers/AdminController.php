<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Settings;
use App\User;
use App\Rules\MatchOldPassword;
use Hash;
use Carbon\Carbon;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
class AdminController extends Controller
{
    public function index(){
        $data = User::select(\DB::raw("COUNT(*) as count"), \DB::raw("DAYNAME(created_at) as day_name"), \DB::raw("DAY(created_at) as day"))
        ->where('created_at', '>', Carbon::today()->subDay(6))
        ->groupBy('day_name','day')
        ->orderBy('day')
        ->get();
     $array[] = ['Name', 'Number'];
     foreach($data as $key => $value)
     {
       $array[++$key] = [$value->day_name, $value->count];
     }
    //  return $data;
     return view('backend.index')->with('users', json_encode($array));
    }

    public function profile(){
        $profile=Auth()->user();
        // return $profile;
        return view('backend.users.profile')->with('profile',$profile);
    }

    public function profileUpdate(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->except('photo'); // Ambil semua data kecuali 'photo'

        // Cek jika ada file 'photo' yang di-upload
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');

            // Tentukan folder penyimpanan (misal: 'uploads/users')
            $path = $file->store('uploads/users', 'public');

            // Simpan path yang dapat diakses publik ke dalam array data
            $data['photo'] = '/storage/' . $path;

            // Opsional: Hapus foto lama jika ada
            // if($user->photo && file_exists(public_path($user->photo))){
            //     unlink(public_path($user->photo));
            // }
        }

        // Update data pengguna
        $status = $user->update($data);

        if ($status) {
            session()->flash('success', 'Successfully updated your profile');
        } else {
            session()->flash('error', 'Please try again!');
        }

        return redirect()->back();
    }

    public function settings(){
        $data=Settings::first();
        return view('backend.setting')->with('data',$data);
    }


    public function settingsUpdate(Request $request)
    {
        $this->validate($request, [
            'short_des'     => 'required|string',
            'description'   => 'required|string',
            'address'       => 'required|string',
            'email'         => 'required|email',
            'phone'         => 'required|string',
            'photo'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'logo'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $settings = Settings::first();
        $data = $request->except(['photo', 'logo']);

        // === Upload Photo ===
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $path = $file->store('uploads/settings', 'public');
            $data['photo'] = '/storage/' . $path;
        }

        // === Upload Logo ===
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $path = $file->store('uploads/settings', 'public');
            $data['logo'] = '/storage/' . $path;
        }

        $status = $settings->update($data);

        if ($status) {
            session()->flash('success','Setting successfully updated');
        } else {
            session()->flash('error','Please try again');
        }

        return redirect()->route('admin');
    }


    public function changePassword(){
        return view('backend.layouts.changePassword');
    }
    public function changPasswordStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', new MatchOldPassword],
            'new_password' => ['required', 'min:6'],
            'new_confirm_password' => ['same:new_password'],
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal harus 6 karakter.',
            'new_confirm_password.same' => 'Konfirmasi password baru harus sama dengan password baru.',
        ]);
        if ($validator->fails()) {
                return redirect()->route('change.password.form')
                             ->withErrors($validator)
                             ->withInput();
        }
        User::find(auth()->user()->id)->update(['password'=> Hash::make($request->new_password)]);

        return redirect()->route('admin')->with('success','Password berhasil diperbarui');
    }
    // Pie chart
    public function userPieChart(Request $request){
        // dd($request->all());
        $data = User::select(\DB::raw("COUNT(*) as count"), \DB::raw("DAYNAME(created_at) as day_name"), \DB::raw("DAY(created_at) as day"))
        ->where('created_at', '>', Carbon::today()->subDay(6))
        ->groupBy('day_name','day')
        ->orderBy('day')
        ->get();
     $array[] = ['Name', 'Number'];
     foreach($data as $key => $value)
     {
       $array[++$key] = [$value->day_name, $value->count];
     }
    //  return $data;
     return view('backend.index')->with('course', json_encode($array));
    }

    // public function activity(){
    //     return Activity::all();
    //     $activity= Activity::all();
    //     return view('backend.layouts.activity')->with('activities',$activity);
    // }

    public function storageLink(){
        // check if the storage folder already linked;
        if(File::exists(public_path('storage'))){
            // removed the existing symbolic link
            File::delete(public_path('storage'));

            //Regenerate the storage link folder
            try{
                Artisan::call('storage:link');
                session()->flash('success', 'Successfully storage linked.');
                return redirect()->back();
            }
            catch(\Exception $exception){
                session()->flash('error', $exception->getMessage());
                return redirect()->back();
            }
        }
        else{
            try{
                Artisan::call('storage:link');
              session()->flash('success', 'Successfully storage linked.');
                return redirect()->back();
            }
            catch(\Exception $exception){
               session()->flash('error', $exception->getMessage());
                return redirect()->back();
            }
        }
    }
}
