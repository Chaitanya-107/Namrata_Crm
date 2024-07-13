<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Storage;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class UserController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | User Profile Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the management and update of existing users Why
    | don't you explore it?
    |
    */

    /**
     * Create a new user controller instance.
     * 
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('localize_auth');
    }

/**
     * Update a user's profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, User $user) {
        $this->validate($request, [
            'address'       => 'max:256|string',
            'birthday'      => 'date',
            'email'         => "email|required|unique:users,email,{$user->id}",
            'first_name'    => 'max:32|min:3|string|required|alpha', // Ensure only alphabetic characters are allowed
            'last_name'     => 'max:32|min:3|string|alpha', // Ensure only alphabetic characters are allowed
            'locale'        => 'in:' . collect(config('insura.languages'))->pluck('locale')->implode(',') . '|required',
            'phone'         => 'max:16|string',
            'profile_image' => 'image'
        ]);

        if ($request->hasFile('profile_image') && $request->file('profile_image')->isValid()) {
            $profile_image_filename = str_random(7). '-profile.' . $request->file('profile_image')->getClientOriginalExtension();
            try {
                $request->file('profile_image')->move(storage_path('app/images/users/'), $profile_image_filename);
                $profile_image_storage_path = 'images/users/' . $user->profile_image_filename;
                if ($user->profile_image_filename !== 'default-profile.jpg' && Storage::exists($profile_image_storage_path)) {
                    Storage::delete($profile_image_storage_path);
                }
                $user->profile_image_filename = $profile_image_filename;
            } catch (FileException $e) {
                return redirect()->back()->withErrors([
                    trans('settings.message.error.file', [
                        'filename' => $request->file('profile_image')->getClientOriginalName(),
                        'type' => trans('settings.message.error.files.profile_image')
                    ])
                ]);
            }
        }

        $user->address = $request->address;
        $user->birthday = $request->birthday;
        $user->email = $request->email;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->locale = $request->locale;
        $user->phone = $request->phone;
        $user->save();

        return redirect()->back()->with('success', trans('settings.message.success.profile.edit'));
    }
}