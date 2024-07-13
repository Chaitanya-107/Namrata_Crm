<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Email;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the management of existing emails.
    |
    */

    /**
     * Create a new email controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('localize_auth');
    }

    /**
     * Send an email using PHPMailer.
     *
     * @param  \App\Models\Email  $email
     * @param  string  $view
     * @return void
     */
    protected function sendEmail(Email $email, string $view)
{
    $mail = new PHPMailer(true); // Passing `true` enables exceptions

    try {
        // Server settings
        $mail->SMTPDebug = 2; // Enable verbose debug output (change to 0 for no debug output)
        $mail->isSMTP(); // Set mailer to use SMTP
        $mail->Host = 'mail.revsol.co.in'; // Specify main and backup SMTP servers
        $mail->SMTPAuth = true; // Enable SMTP authentication
        $mail->Username = 'noreply@revsol.co.in'; // SMTP username
        $mail->Password = 'Qbetabipl32'; // SMTP password
        $mail->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 465; // TCP port to connect to

        // Recipients
        $mail->setFrom('noreply@revsol.co.in', 'Mailer');
        $mail->addAddress($email->recipient_email, $email->recipient_name); // Use client's email address and name

        // Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = $email->subject;
        $mail->Body = view($view, ['email' => $email])->render(); // Render the email view

        $mail->send();
        $email->status = 1; // Mark email as sent
        $email->save();
    } catch (Exception $e) {
        // Log any exceptions
        \Log::error('Message could not be sent. Mailer Error: ' . $mail->ErrorInfo);
    }
}


    /**
     * Add an email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request)
    {
        $user = $request->user();

        $this->validate($request, [
            'message' => 'required|string',
            'recipient' => 'in:admins,brokers,clients,staff,' . $user->company->users->keyBy('id')->keys()->implode(',') . '|required',
            'subject' => 'required|string'
        ]);

        switch ($request->recipient) {
            case 'admins':
                foreach (User::admin()->get() as $admin) {
                    $email = new Email([
                        'message' => $request->message,
                        'status' => 0,
                        'subject' => $request->subject,
                        'recipient_email' => $admin->email, // Use client's email address
                        'recipient_name' => $admin->name // Use client's name
                    ]);
                    $email->recipient()->associate($admin);
                    $email->sender()->associate($user);
                    $email->save();
                    $this->sendEmail($email, 'emails.regular');
                }
                break;
            // Add other cases for different recipient types if needed
        }

        return redirect()->back()->with('status', trans('communication.message.info.sent', ['type' => 'email']));
    }

    /**
     * Delete an email.
     *
     * @param  \App\Models\Email  $email
     * @return \Illuminate\Http\Response
     */
    public function delete(Email $email)
    {
        $email->delete();
        return redirect()->back()->with('status', trans('communication.message.info.deleted', ['type' => 'email']));
    }

    /**
     * Get all emails related to the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $recipient
     * @return \Illuminate\Http\Response
     */
    public function getAll(Request $request, User $recipient)
    {
        $user = User::withStatus()->find($request->user()->id);
        $view_data = [
            'emails' => $recipient->incomingEmails->where('sender_id', $user->id)->merge($recipient->outgoingEmails->where('recipient_id', $user->id))->sortByDesc('created_at'),
            'recipient' => $recipient
        ];
        if ($user->role === 'super') {
            $view_data['admins'] = Company::all()->map(function ($company) {
                return $company->admin;
            });
        }

        return view('global.emails', $view_data);
    }
}

