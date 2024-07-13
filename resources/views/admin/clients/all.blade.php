<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Title</title>
    <!-- Font Awesome link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-NprRj5TLaU/Gp6BR8a5iia6h1F2hVkBdkF/CwT5T/6hkprjsaxkrkHqOzX1mgj/WnuLkp/zsRZ3cG4vdFIc53g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- jQuery script -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Password toggle script -->
    <script>
        $(document).ready(function() {
            $('.toggle-password').click(function() {
                $(this).toggleClass('active');
                var input = $($(this).attr('toggle'));
                if (input.attr('type') == 'password') {
                    input.attr('type', 'text');
                } else {
                    input.attr('type', 'password');
                }
            });
        });
    </script>
    <!-- Custom CSS for button positioning -->
    <style>
        .actions {
            display: flex;
            justify-content: flex-end;
        }
    </style>
</head>
<body>

@extends('templates.clients.all')

@section('modals')
<!-- new client modal -->
<div class="ui tiny modal" id="newClientModal">
    <div class="header">{{ trans('clients.modal.header.new') }}</div>
    <div class="scrolling content">
        <p>{{ trans('clients.modal.instruction.new') }}</p>
        <form action="{{ action('ClientController@add') }}" enctype="multipart/form-data" method="POST">
            {{ csrf_field() }}
            <div class="ui form">
                <div class="two fields">
                    <div class="field required">
                        <label>{{ trans('clients.input.label.first_name') }}</label>
                        <input type="text" name="first_name" placeholder="{{ trans('clients.input.placeholder.first_name') }}" required value="{{ old('first_name') }}"/>
                    </div>
                    <div class="field">
                        <label>{{ trans('clients.input.label.last_name') }}</label>
                        <input type="text" name="last_name" placeholder="{{ trans('clients.input.placeholder.last_name') }}" value="{{ old('last_name') }}"/>
                    </div>
                </div>
                <div class="two fields">
                    <div class="field required">
                        <label>{{ trans('clients.input.label.email') }}</label>
                        <input type="email" name="email" placeholder="{{ trans('clients.input.placeholder.email') }}" required value="{{ old('email') }}"/>
                    </div>
                    <div class="field">
                        <label>{{ trans('clients.input.label.phone') }}</label>
                        <input type="tel" placeholder="{{ trans('clients.input.placeholder.phone') }}" value="{{ old('phone') }}"/>
                    </div>
                </div>
                <div class="two fields">
                    <div class="field">
                        <label>{{ trans('clients.input.label.birthday') }}</label>
                        <input type="text" class="datepicker" name="birthday" placeholder="{{ trans('clients.input.placeholder.birthday') }}" value="{{ old('birthday') }}"/>
                    </div>
                    <div class="field">
                        <label>{{ trans('clients.input.label.address') }}</label>
                        <input type="text" name="address" placeholder="{{ trans('clients.input.placeholder.address') }}" value="{{ old('address') }}"/>
                    </div>
                </div>
                <div class="two fields">
                    <div class="field required">
                        <label>{{ trans('clients.input.label.password') }}</label>
                        <div class="ui action input">
                            <input type="password" name="password" placeholder="{{ trans('clients.input.placeholder.password') }}" required />
                            <button class="ui icon button toggle-password" tabindex="-1" toggle="password-field">
                                <i class="fas fa-eye-slash"></i> <!-- Font Awesome eye slash icon -->
                            </button>
                        </div>
                    </div>
                    <div class="field required">
                        <label>{{ trans('clients.input.label.confirm_password') }}</label>
                        <div class="ui action input">
                            <input type="password" name="password_confirmation" placeholder="{{ trans('clients.input.placeholder.confirm_password') }}" required />
                            <button class="ui icon button toggle-password" tabindex="-1" toggle="confirm-password-field">
                                <i class="fas fa-eye-slash"></i> <!-- Font Awesome eye slash icon -->
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Other fields... -->
                <div class="field">
                    <label>{{ trans('clients.input.label.profile_image') }}</label>
                    <input type="file" accept="image/*" data-allowed-file-extensions="bmp gif jpeg jpg png svg" class="file-upload" data-default-file="{{ asset('uploads/images/users/default-profile.jpg') }}" name="profile_image"/>
                </div>
            </div>
            <div class="actions">
                <div class="ui buttons">
                    <button class="ui cancel button">{{ trans('clients.modal.button.cancel.new') }}</button>
                    <div class="or" data-text="{{ trans('products.modal.button.or') }}"></div>
                    <button class="ui positive primary button">{{ trans('clients.modal.button.confirm.new') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

</body>
</html>
