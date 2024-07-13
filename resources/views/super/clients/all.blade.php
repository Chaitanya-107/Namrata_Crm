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
        <input type="password" name="password" placeholder="{{ trans('clients.input.placeholder.password') }}" required/>
    </div>
    <div class="field required">
        <label>{{ trans('clients.input.label.confirm_password') }}</label>
        <input type="password" name="password_confirmation" placeholder="{{ trans('clients.input.placeholder.confirm_password') }}" required/>
    </div>
</div>
                    <div class="field required">
                        <label>{{ trans('clients.input.label.company') }}</label>
                        <select class="dropdown fluid search ui" name="company_id" required>
                            @foreach($companies as $company)
                                <option{{ $user->company && !empty(old('company_id')) && old('company_id') === $company->id || empty(old('company_id')) && $company === $user->company ? ' selected' : '' }} value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field required">
                        <label>{{ trans('clients.input.label.inviter') }}</label>
                        <div class="ui selection fluid search dropdown">
                            <input type="hidden" name="inviter_id" value="{{ old('inviter_id') ?: ($user->company ? $user->id : '') }}"/>
                            <div class="default text">{{ trans('policies.input.placeholder.inviter') }}</div>
                            <i class="dropdown icon"></i>
                            <div class="menu" id="">
                                @foreach($companies as $company)
                                    <div class="company{{ $company->id }} header"{!! $company->id === ($user->company ? $user->company->id : null) ? '' : ' style="display:none;"' !!}>
                                        {{ trans('clients.input.option.header.company') }}
                                    </div>
                                    <div class="company{{ $company->id }} item{{ !empty(old('inviter_id')) && old('inviter_id') === ($company->admin->id ?? null) || empty(old('company_id')) && ($company->admin->id ?? null) === ($user->id ?? null) ? ' selected' : '' }}" data-value="{{ $company->admin->id ?? null }}"{!! $company->id === ($user->company ? $user->company->id : null) ? '' : ' style="display:none;"'  !!}>
                                        <i class="building icon"></i>
                                        {{ $company->name }} {{ ($company->admin->id ?? null) === ($user->id ?? null) ? trans('clients.input.option.you') : '' }}
                                    </div>
                                    <div class="company{{ $company->id }} divider"{!! $company->id === ($user->company ? $user->company->id : null) ? '' : ' style="display:none;"' !!}></div>
                                    <div class="company{{ $company->id }} header"{!! $company->id === ($user->company ? $user->company->id : null) ? '' : ' style="display:none;"' !!}>
                                        {{ trans('clients.input.option.header.staff') }}
                                    </div>
                                    @forelse($company->staff as $staff)
                                        <div class="company{{ $company->id }} item{{ !empty(old('inviter_id')) && old('inviter_id') === ($staff->id ?? null) || empty(old('inviter_id')) && ($staff->id ?? null) === ($user->id ?? null) ? ' selected' : '' }}" data-value="{{ $staff->id ?? null }}"{!! $company->id === ($user->company ? $user->company->id : null) ? '' : ' style="display:none;"'  !!}>
                                            <i class="address book icon"></i>
                                            {{ $staff->first_name ?? '' }} - {{ $staff->email ?? '' }} {{ ($staff->id ?? null) === ($user->id ?? null) ? trans('clients.input.option.you') : '' }}
                                        </div>
                                    @empty
                                        <div class="company{{ $company->id }} disabled item" data-value=""{!! $company->id === ($user->company ? $user->company->id : null) ? '' : ' style="display:none;"' !!}>
                                            {{ trans('clients.input.option.empty.brokers', array(
                                                'company_name'  => $company->name
                                            )) }}
                                        </div>
                                    @endforelse
                                    <div class="company{{ $company->id }} divider"{!! $company->id === ($user->company ? $user->company->id : null) ? '' : ' style="display:none;"' !!}></div>
                                    <div class="company{{ $company->id }} header"{!! $company->id === ($user->company ? $user->company->id : null) ? '' : ' style="display:none;"' !!}>
                                        {{ trans('clients.input.option.header.brokers') }}
                                    </div>
                                    @forelse($company->brokers as $broker)
                                        <div class="company{{ $company->id }} item{{ !empty(old('inviter_id')) && old('inviter_id') === ($broker->id ?? null) || empty(old('inviter_id')) && ($broker->id ?? null) === ($user->id ?? null) ? ' selected' : '' }}" data-value="{{ $broker->id ?? null }}"{!! $company->id === ($user->company ? $user->company->id : null) ? '' : ' style="display:none;"'  !!}>
                                            <i class="briefcase icon"></i>
                                            {{ $broker->first_name ?? '' }} - {{ $broker->email ?? '' }} {{ ($broker->id ?? null) === ($user->id ?? null) ? trans('clients.input.option.you') : '' }}
                                        </div>
                                    @empty
                                        <div class="company{{ $company->id }} disabled item" data-value=""{!! $company->id === ($user->company ? $user->company->id : null) ? '' : ' style="display:none;"' !!}>
                                            {{ trans('clients.input.option.empty.brokers', array(
                                                'company_name'  => $company->name
                                            )) }}
                                        </div>
                                    @endforelse
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="divider"></div>
                    <div class="divider" style="display:none;"></div>
                    <div class="field">
                        <label>{{ trans('clients.input.label.profile_image') }}</label>
                        <input type="file" accept="image/*" data-allowed-file-extensions="bmp gif jpeg jpg png svg" class="file-upload" data-default-file="{{ asset('uploads/images/users/default-profile.jpg') }}" name="profile_image"/>
                    </div>
                </div>
            </form>
            @foreach($companies as $company)
                <div id="company{{ $company->id }}CustomFields" style="display:none;">
                    @foreach ($company->custom_fields_metadata->where('model', 'client')->all() as $key => $field)
                        <input type="hidden" name="custom_fields[C{{ $company->id }}F{{ $key }}][label]" value="{{ $field->label }}"/>
                        <input type="hidden" name="custom_fields[C{{ $company->id }}F{{ $key }}][type]" value="{{ $field->type }}"/>
                        <input type="hidden" name="custom_fields[C{{ $company->id }}F{{ $key }}][uuid]" value="{{ $field->uuid }}"/>
                        @if ($field->type === 'checkbox')
                            <div class="field{{ isset($field->required) ? ' required' : '' }}">
                                <div class="ui checkbox">
                                    <input type="checkbox"{{ isset($field->default) ? ' checked' : '' }} name="custom_fields[C{{ $company->id }}F{{ $key }}][value]"{{ isset($field->required) ? ' required' : '' }}>
                                    <label>{{ $field->label }}</label>
                                </div>
                            </div>
                        @elseif ($field->type === 'date')
                            <div class="field{{ isset($field->required) ? ' required' : '' }}">
                                <label>{{ $field->label }}</label>
                                <input type="text" class="datepicker" name="custom_fields[C{{ $company->id }}F{{ $key }}][value]""{{ isset($field->required) ? ' required' : '' }} value="{{ $field->default }}">
                            </div>
                        <!-- Other field types go here -->
                        @endif
                    @endforeach
                </div>
            @endforeach
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

@section('extra_scripts')
    <script type="text/javascript">
        (function($insura, $) {
            $(document).ready(function() {
                //Watch for a company change
                $('select[name="company_id"]').change(function() {
                    var companyId = $(this).val();
                    var customFieldsDivider = $('div#newClientModal div.form > div.divider:first');
                    $('div[class^="company"]').hide();
                    $('div.company' + companyId).show();
                    $('input[name="inviter_id"]').val('').parent().find('div.text').text('');
                    while(!customFieldsDivider.next().hasClass('divider')) {
                        customFieldsDivider.next().remove();
                    }
                    customFieldsDivider.after($('div#company' + companyId + 'CustomFields').html());
                    (function semanticInit(div) {
                        if(div.length > 0) {
                            $insura.helpers.initDatepicker(div.find('input.datepicker'));
                            $insura.helpers.initDropdown(div.find('div.dropdown'));
                            $insura.helpers.initTelInput(div.find('input[type="tel"]'));
                            $insura.helpers.requireDropdownFields(div.find('div.required select, div.required div.dropdown input[type="hidden"]'));
                            semanticInit(div.next());
                        }
                    })(customFieldsDivider.next());
                }).change();
            });
        })(window.insura, window.jQuery);
    </script>
@endsection
