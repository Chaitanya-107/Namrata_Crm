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
                            <input type="password" name="password" placeholder="{{ trans('clients.input.placeholder.password') }}" required />
                        </div>
                        <div class="field required">
                            <label>{{ trans('clients.input.label.confirm_password') }}</label>
                            <input type="password" name="password_confirmation" placeholder="{{ trans('clients.input.placeholder.confirm_password') }}" required />
                        </div>
                    </div>
                    <div class="field required">
                        <label>{{ trans('clients.input.label.inviter') }}</label>
                        <div class="ui selection dropdown">
                            <!-- Dropdown options -->
                        </div>
                    </div>
                    <div class="divider"></div>
                    <!-- Additional fields -->
                    <!-- ... -->
                    <div class="field">
                        <label>{{ trans('clients.input.label.profile_image') }}</label>
                        <input type="file" accept="image/*" data-allowed-file-extensions="bmp gif jpeg jpg png svg" class="file-upload" data-default-file="{{ asset('uploads/images/users/default-profile.jpg') }}" name="profile_image"/>
                    </div>
                </div>
            </form>
        </div>
        <div class="actions">
            <div class="ui buttons">
                <button class="ui cancel button">{{ trans('clients.modal.button.cancel.new') }}</button>
                <div class="or" data-text="{{ trans('products.modal.button.or') }}"></div>
                <button class="ui positive primary button">{{ trans('clients.modal.button.confirm.new') }}</button>
            </div>
        </div>
    </div>
@endsection
