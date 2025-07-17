@extends('adminmodule::layouts.master')

<<<<<<< HEAD
@section('title', translate('provider_details'))
=======
@section('title',translate('provider_details'))
>>>>>>> newversion/main

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-title-wrap mb-3">
<<<<<<< HEAD
                <h2 class="page-title">{{ translate('Bank_Information') }}</h2>
=======
                <h2 class="page-title">{{translate('Bank_Information')}}</h2>
>>>>>>> newversion/main
            </div>

            <div class="mb-3">
                <ul class="nav nav--tabs nav--tabs__style2">
                    <li class="nav-item">
<<<<<<< HEAD
                        <a class="nav-link {{ $webPage == 'overview' ? 'active' : '' }}"
                            href="{{ url()->current() }}?web_page=overview">{{ translate('Overview') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $webPage == 'subscribed_services' ? 'active' : '' }}"
                            href="{{ url()->current() }}?web_page=subscribed_services">{{ translate('Subscribed_Services') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $webPage == 'bookings' ? 'active' : '' }}"
                            href="{{ url()->current() }}?web_page=bookings">{{ translate('Bookings') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $webPage == 'serviceman_list' ? 'active' : '' }}"
                            href="{{ url()->current() }}?web_page=serviceman_list">{{ translate('Service_Man_List') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $webPage == 'settings' ? 'active' : '' }}"
                            href="{{ url()->current() }}?web_page=settings">{{ translate('Settings') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $webPage == 'bank_information' ? 'active' : '' }}"
                            href="{{ url()->current() }}?web_page=bank_information">{{ translate('Bank_Information') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $webPage == 'reviews' ? 'active' : '' }}"
                            href="{{ url()->current() }}?web_page=reviews">{{ translate('Reviews') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $webPage == 'subscription' ? 'active' : '' }}"
                            href="{{ url()->current() }}?web_page=subscription&provider_id={{ request()->id }}">{{ translate('Business Plan') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $webPage == 'provider_dashboard' ? 'active' : '' }}"
                            href="{{ url()->current() }}?web_page=provider_dashboard&provider_id={{ request()->id }}">{{ translate('Provider_DashBoard') }}</a>
=======
                        <a class="nav-link {{$webPage=='overview'?'active':''}}"
                           href="{{url()->current()}}?web_page=overview">{{translate('Overview')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{$webPage=='subscribed_services'?'active':''}}"
                           href="{{url()->current()}}?web_page=subscribed_services">{{translate('Subscribed_Services')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{$webPage=='bookings'?'active':''}}"
                           href="{{url()->current()}}?web_page=bookings">{{translate('Bookings')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{$webPage=='serviceman_list'?'active':''}}"
                           href="{{url()->current()}}?web_page=serviceman_list">{{translate('Service_Man_List')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{$webPage=='settings'?'active':''}}"
                           href="{{url()->current()}}?web_page=settings">{{translate('Settings')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{$webPage=='bank_information'?'active':''}}"
                           href="{{url()->current()}}?web_page=bank_information">{{translate('Bank_Information')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{$webPage=='reviews'?'active':''}}"
                           href="{{url()->current()}}?web_page=reviews">{{translate('Reviews')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{$webPage=='subscription'?'active':''}}"
                           href="{{url()->current()}}?web_page=subscription&provider_id={{ request()->id }}">{{translate('Business Plan')}}</a>
>>>>>>> newversion/main
                    </li>
                </ul>
            </div>

            <div class="card">
                <div class="border-bottom d-flex gap-3 flex-wrap justify-content-between align-items-center px-4 py-3">
                    <div class="d-flex gap-2 align-items-center">
                        <span class="material-symbols-outlined">account_balance</span>

<<<<<<< HEAD
                        <h3>{{ translate('Bank_Information') }}</h3>
=======
                        <h3>{{translate('Bank_Information')}}</h3>
>>>>>>> newversion/main
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                    </div>
                </div>
                <div class="card-body p-30">
                    <div class="row justify-content-center">
                        <div class="col-sm-6 col-md-8 col-lg-6 col-xl-5">
                            <div class="card bank-info-card bg-bottom bg-contain bg-img"
<<<<<<< HEAD
                                style="background-image: url('{{ asset('public/assets/admin-module') }}/img/media/bank-info-card-bg.png');">
                                <div class="border-bottom p-3">
                                    <h4 class="fw-semibold">{{ translate('Holder_Name') }}:
                                        <strong>{{ Str::limit($provider->bank_detail->acc_holder_name ?? translate('Unavailable'), 50) }}</strong>
=======
                                 style="background-image: url('{{asset('public/assets/admin-module')}}/img/media/bank-info-card-bg.png');">
                                <div class="border-bottom p-3">
                                    <h4 class="fw-semibold">{{translate('Holder_Name')}}:
                                        <strong>{{Str::limit($provider->bank_detail->acc_holder_name ?? translate('Unavailable'), 50)}}</strong>
>>>>>>> newversion/main
                                    </h4>
                                </div>
                                <div class="card-body position-relative">
                                    <img class="bank-card-img"
<<<<<<< HEAD
                                        src="{{ asset('public/assets/admin-module') }}/img/media/bank-card.png"
                                        alt="">
                                    <ul class="list-unstyled d-flex flex-column gap-4">
                                        <li>
                                            <h3 class="mb-2">{{ translate('Bank_Name') }}:</h3>
                                            <div>{{ $provider->bank_detail->bank_name ?? translate('Unavailable') }}</div>
                                        </li>
                                        <li>
                                            <h3 class="mb-2">{{ translate('Branch_Name') }}:</h3>
                                            <div>{{ $provider->bank_detail->branch_name ?? translate('Unavailable') }}
                                            </div>
                                        </li>
                                        <li>
                                            <h3 class="mb-2">{{ translate('Account_Number') }}:</h3>
=======
                                         src="{{asset('public/assets/admin-module')}}/img/media/bank-card.png" alt="">
                                    <ul class="list-unstyled d-flex flex-column gap-4">
                                        <li>
                                            <h3 class="mb-2">{{translate('Bank_Name')}}:</h3>
                                            <div>{{ $provider->bank_detail->bank_name ?? translate('Unavailable') }}</div>
                                        </li>
                                        <li>
                                            <h3 class="mb-2">{{translate('Branch_Name')}}:</h3>
                                            <div>{{ $provider->bank_detail->branch_name ?? translate('Unavailable') }}</div>
                                        </li>
                                        <li>
                                            <h3 class="mb-2">{{translate('Account_Number')}}:</h3>
>>>>>>> newversion/main
                                            <div>{{ $provider->bank_detail->acc_no ?? translate('Unavailable') }}</div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updateBankInfo" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
<<<<<<< HEAD
                <form action="{{ route('admin.provider.account.update', [$provider->id]) }}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="changeScheduleModalLabel">{{ translate('Update_Account_Information') }}
                        </h5>
=======
                <form action="{{route('admin.provider.account.update',[$provider->id])}}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"
                            id="changeScheduleModalLabel">{{translate('Update_Account_Information')}}</h5>
>>>>>>> newversion/main
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-floating mb-30">
                            <input type="text" class="form-control" name="bank_name"
<<<<<<< HEAD
                                value="{{ $provider->bank_detail->bank_name ?? '' }}"
                                placeholder="{{ translate('Bank_Name') }}" required>
                            <label>{{ translate('Bank_Name') }}</label>
                        </div>
                        <div class="form-floating mb-30">
                            <input type="text" class="form-control" name="branch_name"
                                value="{{ $provider->bank_detail->branch_name ?? '' }}"
                                placeholder="{{ translate('Branch_Name') }}" required>
                            <label>{{ translate('Branch_Name') }}</label>
                        </div>
                        <div class="form-floating mb-30">
                            <input type="text" class="form-control" name="acc_no"
                                value="{{ $provider->bank_detail->acc_no ?? '' }}" placeholder="{{ translate('Acc_No') }}"
                                required>
                            <label>{{ translate('Acc._No.') }}</label>
                        </div>
                        <div class="form-floating mb-30">
                            <input type="text" class="form-control" name="acc_holder_name"
                                value="{{ $provider->bank_detail->acc_holder_name ?? '' }}"
                                placeholder="{{ translate('Acc._Holder_Name') }}" required>
                            <label>{{ translate('Acc._Holder_Name') }}</label>
=======
                                   value="{{$provider->bank_detail->bank_name??''}}"
                                   placeholder="{{translate('Bank_Name')}}" required>
                            <label>{{translate('Bank_Name')}}</label>
                        </div>
                        <div class="form-floating mb-30">
                            <input type="text" class="form-control" name="branch_name"
                                   value="{{$provider->bank_detail->branch_name??''}}"
                                   placeholder="{{translate('Branch_Name')}}" required>
                            <label>{{translate('Branch_Name')}}</label>
                        </div>
                        <div class="form-floating mb-30">
                            <input type="text" class="form-control" name="acc_no"
                                   value="{{$provider->bank_detail->acc_no??''}}"
                                   placeholder="{{translate('Acc_No')}}" required>
                            <label>{{translate('Acc._No.')}}</label>
                        </div>
                        <div class="form-floating mb-30">
                            <input type="text" class="form-control" name="acc_holder_name"
                                   value="{{$provider->bank_detail->acc_holder_name??''}}"
                                   placeholder="{{translate('Acc._Holder_Name')}}" required>
                            <label>{{translate('Acc._Holder_Name')}}</label>
>>>>>>> newversion/main
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--secondary"
<<<<<<< HEAD
                            data-bs-dismiss="modal">{{ translate('Close') }}</button>
                        <button type="submit" class="btn btn--primary">{{ translate('Submit') }}</button>
=======
                                data-bs-dismiss="modal">{{translate('Close')}}</button>
                        <button type="submit" class="btn btn--primary">{{translate('Submit')}}</button>
>>>>>>> newversion/main
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
