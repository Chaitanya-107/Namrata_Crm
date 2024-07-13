<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Artisan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class SettingController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Setting Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the management of existing settings.
    |
    */

    /**
     * Create a new settings controller instance.
     * 
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('localize_auth');
    }
    
    /**
     * Re-cache the app's config
     * 
     * @return \Illuminate\Http\Response
     */
    public function cache() {
        Artisan::call('config:clear');
        Artisan::call('config:cache');
        // Give adequate time for the config cache to be output before continuing
        sleep(5);

        return redirect()->action('SettingController@load');
    }
    
    /**
     * Edit system settings
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request) {
        $request->session()->flash('tab', 'system');
        $this->validate($request, [
            'app_locale_default'        => 'max:5|min:5|required',
            'favicon'                   => 'image',
            'insura_currency_default'   => 'required|in:' . implode(',', config('insura.currencies.list.*.code')),
            'insura_name'               => 'required|min:3|max:64',
            'logo'                      => 'image',
            'mail_driver'               => 'required|in:mailgun,mandrill,sendmail,ses,smtp',
            'mail_encryption'           => 'required|in:none,ssl,tls',
            'mail_username'             => 'string|min:4|max:64',
            'mailgun_domain'            => 'string',
            'mailgun_secret'            => 'string',
            'mandrill_secret'           => 'string',
            'ses_key'                   => 'string',
            'ses_region'                => 'string',
            'ses_secret'                => 'string',
            'smtp_host'                 => 'string|min:3|max:64',
            'smtp_password'             => 'confirmed',
            'smtp_port'                 => 'integer',
        ]);

        $input = $request->only([
            'app_locale_default',
            'insura_currency_default',
            'insura_name',
            'mail_driver',
            'mail_encryption',
            'mail_username',
            'mailgun_domain',
            'mailgun_secret',
            'mandrill_secret',
            'ses_key',
            'ses_region',
            'ses_secret',
            'smtp_host',
            'smtp_password',
            'smtp_port',
        ]);

        $this->handleFileUpload($request, 'favicon', 'insura_favicon');
        $this->handleFileUpload($request, 'logo', 'insura_logo');

        $env = view('templates.env', ['env' => $input]);
        Storage::disk('base')->put('.env', $env);
        Artisan::call('config:clear');

        return redirect()->action('SettingController@cache');
    }

    /**
     * Handle file upload for a specific field
     *
     * @param \Illuminate\Http\Request $request
     * @param string $fieldName
     * @param string $configKey
     * @return void
     */
    private function handleFileUpload(Request $request, string $fieldName, string $configKey) {
        if ($request->hasFile($fieldName) && $request->file($fieldName)->isValid()) {
            $filename = $request->file($fieldName)->getClientOriginalName();
            try {
                $uploadedFile = $request->file($fieldName)->storeAs('images', $filename);
                $input[$configKey] = $filename;
            } catch (FileException $e) {
                return redirect()->back()->withErrors([
                    trans('settings.message.error.file', [
                        'filename' => $filename,
                        'type' => trans('settings.message.error.files.' . $fieldName),
                    ])
                ]);
            }
        }
    }

    /**
     * Get all settings
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function get(Request $request) {
        $user = $request->user();
        $view_data = [];
        if (in_array($user->role, ['admin', 'super'])) {
            $view_data['company'] = $user->company;
            $view_data['reminders'] = $user->company->reminders->all();
        }
        return view($user->role . '.settings', $view_data);
    }
    
    /**
     * Load the app with new settings
     * 
     * @return \Illuminate\Http\Response
     */
    public function load() {
        return redirect()->action('SettingController@get')->with('success', trans('settings.message.success.system.edit'))->with('tab', 'system');
    }
}
