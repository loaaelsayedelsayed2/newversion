@extends('adminmodule::layouts.master')

<<<<<<< HEAD
@section('title', translate('provider_details'))

@push('css_or_js')
=======
@section('title',translate('provider_details'))

@push('css_or_js')

>>>>>>> newversion/main
@endpush

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-title-wrap mb-3">
<<<<<<< HEAD
                <h2 class="page-title">{{ translate('Provider_Details') }}</h2>
=======
                <h2 class="page-title">{{translate('Provider_Details')}}</h2>
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
                <div class="card-body p-30">
<<<<<<< HEAD
                    @if ($provider->is_approved == 1)
                        <div class="provider-details-overview mb-30">
                            <div class="provider-details-overview__collect-cash">
                                <div class="statistics-card statistics-card__collect-cash h-100">
                                    <h3>{{ translate('Collect_Cash_From_Provider') }}</h3>
                                    <h2>{{ with_currency_symbol($provider->owner->account->account_payable) }}</h2>
                                    @can('provider_update')
                                        <a href="{{ route('admin.provider.collect_cash.list', [$provider->id]) }}"
                                            class="btn btn--primary text-capitalize w-100 btn--lg mw-75">{{ translate('Collect_Cash') }}</a>
=======
                    @if($provider->is_approved == 1)
                        <div class="provider-details-overview mb-30">
                            <div class="provider-details-overview__collect-cash">
                                <div class="statistics-card statistics-card__collect-cash h-100">
                                    <h3>{{translate('Collect_Cash_From_Provider')}}</h3>
                                    <h2>{{with_currency_symbol($provider->owner->account->account_payable)}}</h2>
                                    @can('provider_update')
                                        <a href="{{route('admin.provider.collect_cash.list', [$provider->id])}}"
                                           class="btn btn--primary text-capitalize w-100 btn--lg mw-75">{{translate('Collect_Cash')}}</a>
>>>>>>> newversion/main
                                    @endcan
                                </div>
                            </div>
                            <div class="provider-details-overview__statistics">

<<<<<<< HEAD
                                <div class="statistics-card statistics-card__style2 statistics-card__pending-withdraw">
                                    <h2>{{ with_currency_symbol($provider->owner->account->balance_pending) }}</h2>
                                    <h3>{{ translate('Pending_Withdrawn') }}</h3>
                                </div>

                                <div class="statistics-card statistics-card__style2 statistics-card__already-withdraw">
                                    <h2>{{ with_currency_symbol($provider->owner->account->total_withdrawn) }}</h2>
                                    <h3>{{ translate('Already_Withdrawn') }}</h3>
                                </div>

                                <div class="statistics-card statistics-card__style2 statistics-card__withdrawable-amount">
                                    <h2>{{ with_currency_symbol($provider->owner->account->account_receivable) }}</h2>
                                    <h3>{{ translate('Withdrawable_Amount') }}</h3>
                                </div>

                                <div class="statistics-card statistics-card__style2 statistics-card__total-earning">
                                    <h2>{{ with_currency_symbol($provider->owner->account->received_balance + $provider->owner->account->total_withdrawn) }}
                                    </h2>
                                    <h3>{{ translate('Total_Earning') }}</h3>
=======
                                <div
                                    class="statistics-card statistics-card__style2 statistics-card__pending-withdraw">
                                    <h2>{{with_currency_symbol($provider->owner->account->balance_pending)}}</h2>
                                    <h3>{{translate('Pending_Withdrawn')}}</h3>
                                </div>

                                <div
                                    class="statistics-card statistics-card__style2 statistics-card__already-withdraw">
                                    <h2>{{with_currency_symbol($provider->owner->account->total_withdrawn)}}</h2>
                                    <h3>{{translate('Already_Withdrawn')}}</h3>
                                </div>

                                <div
                                    class="statistics-card statistics-card__style2 statistics-card__withdrawable-amount">
                                    <h2>{{with_currency_symbol($provider->owner->account->account_receivable)}}</h2>
                                    <h3>{{translate('Withdrawable_Amount')}}</h3>
                                </div>

                                <div
                                    class="statistics-card statistics-card__style2 statistics-card__total-earning">
                                    <h2>{{ with_currency_symbol($provider->owner->account->received_balance + $provider->owner->account->total_withdrawn) }}</h2>
                                    <h3>{{translate('Total_Earning')}}</h3>
>>>>>>> newversion/main
                                </div>
                            </div>
                            <div class="provider-details-overview__order-overview">
                                <div class="statistics-card statistics-card__order-overview h-100 pb-2">
<<<<<<< HEAD
                                    <h3 class="mb-0">{{ translate('Booking_Overview') }}</h3>
=======
                                    <h3 class="mb-0">{{translate('Booking_Overview')}}</h3>
>>>>>>> newversion/main
                                    <div id="apex-pie-chart" class="d-flex justify-content-center"></div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="d-flex align-items-center flex-wrap-reverse justify-content-between gap-3 mb-3">
<<<<<<< HEAD
                        <h2>{{ translate('Information_Details') }}</h2>
                        <div class="d-flex align-items-center flex-wrap gap-3">
                            @if ($provider->is_approved == 2)
                                <a type="button" class="btn btn-soft--danger text-capitalize provider_approval"
                                    id="button-deny-{{ $provider->id }}" data-approve="{{ $provider->id }}"
                                    data-status="deny">
                                    {{ translate('Deny') }}
                                </a>
                            @endif
                            @if ($provider->is_approved == 0 || $provider->is_approved == 2)
                                <a type="button" class="btn btn--success text-capitalize approval_provider"
                                    id="button-{{ $provider->id }}" data-approve="{{ $provider->id }}"
                                    data-approve="approve">
                                    {{ translate('Accept') }}
=======
                        <h2>{{translate('Information_Details')}}</h2>
                        <div class="d-flex align-items-center flex-wrap gap-3">
                            @if($provider->is_approved == 2)
                                <a type="button"
                                   class="btn btn-soft--danger text-capitalize provider_approval"
                                   id="button-deny-{{$provider->id}}" data-approve="{{$provider->id}}"
                                   data-status="deny">
                                    {{translate('Deny')}}
                                </a>
                            @endif
                            @if($provider->is_approved == 0 || $provider->is_approved == 2)
                                <a type="button" class="btn btn--success text-capitalize approval_provider"
                                   id="button-{{$provider->id}}" data-approve="{{$provider->id}}"
                                   data-approve="approve">
                                    {{translate('Accept')}}
>>>>>>> newversion/main
                                </a>
                            @endif

                            @can('provider_update')
<<<<<<< HEAD
                                <a href="{{ route('admin.provider.edit', [$provider->id]) }}" class="btn btn--primary">
                                    <span class="material-icons">border_color</span>
                                    {{ translate('Edit') }}
=======
                                <a href="{{route('admin.provider.edit',[$provider->id])}}" class="btn btn--primary">
                                    <span class="material-icons">border_color</span>
                                    {{translate('Edit')}}
>>>>>>> newversion/main
                                </a>
                            @endcan
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="information-details-box media flex-column flex-sm-row gap-20">
<<<<<<< HEAD
                                <img class="avatar-img radius-5" src="{{ $provider->logoFullPath }}"
                                    alt="{{ translate('logo') }}">
                                <div class="media-body ">
                                    <h2 class="information-details-box__title">
                                        {{ Str::limit($provider->company_name, 30) }}</h2>
=======
                                <img class="avatar-img radius-5" src="{{ $provider->logoFullPath }}" alt="{{ translate('logo') }}">
                                <div class="media-body ">
                                    <h2 class="information-details-box__title">{{Str::limit($provider->company_name, 30)}}</h2>
>>>>>>> newversion/main

                                    <ul class="contact-list">
                                        <li>
                                            <span class="material-symbols-outlined">phone_iphone</span>
<<<<<<< HEAD
                                            <a
                                                href="tel:{{ $provider->company_phone }}">{{ $provider->company_phone }}</a>
                                        </li>
                                        <li>
                                            <span class="material-symbols-outlined">mail</span>
                                            <a
                                                href="mailto:{{ $provider->company_email }}">{{ $provider->company_email }}</a>
                                        </li>
                                        <li>
                                            <span class="material-symbols-outlined">map</span>
                                            {{ Str::limit($provider->company_address, 100) }}
=======
                                            <a href="tel:{{$provider->company_phone}}">{{$provider->company_phone}}</a>
                                        </li>
                                        <li>
                                            <span class="material-symbols-outlined">mail</span>
                                            <a href="mailto:{{$provider->company_email}}">{{$provider->company_email}}</a>
                                        </li>
                                        <li>
                                            <span class="material-symbols-outlined">map</span>
                                            {{Str::limit($provider->company_address, 100)}}
>>>>>>> newversion/main
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="information-details-box h-100">
<<<<<<< HEAD
                                <h2 class="information-details-box__title c1">{{ translate('Contact_Person_Information') }}
                                </h2>
                                <h3 class="information-details-box__subtitle">
                                    {{ Str::limit($provider->contact_person_name, 30) }}</h3>
=======
                                <h2 class="information-details-box__title c1">{{translate('Contact_Person_Information')}}
                                </h2>
                                <h3 class="information-details-box__subtitle">{{Str::limit($provider->contact_person_name, 30)}}</h3>
>>>>>>> newversion/main

                                <ul class="contact-list">
                                    <li>
                                        <span class="material-symbols-outlined">phone_iphone</span>
<<<<<<< HEAD
                                        <a
                                            href="tel:{{ $provider->contact_person_phone }}">{{ $provider->contact_person_phone }}</a>
                                    </li>
                                    <li>
                                        <span class="material-symbols-outlined">mail</span>
                                        <a
                                            href="mailto:{{ $provider->contact_person_email }}">{{ $provider->contact_person_email }}</a>
=======
                                        <a href="tel:{{$provider->contact_person_phone}}">{{$provider->contact_person_phone}}</a>
                                    </li>
                                    <li>
                                        <span class="material-symbols-outlined">mail</span>
                                        <a href="mailto:{{$provider->contact_person_email}}">{{$provider->contact_person_email}}</a>
>>>>>>> newversion/main
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="information-details-box">
                                <div class="row g-4">
                                    <div class="col-lg-3">
<<<<<<< HEAD
                                        <h2 class="information-details-box__title c1 mb-3">{{ translate('Business_Info') }}
                                        </h2>
                                        <p><strong
                                                class="text-capitalize">{{ translate($provider->owner->identification_type) }}
                                                -</strong> {{ $provider->owner->identification_number }}</p>
                                    </div>
                                    <div class="col-lg-9">
                                        <div class="d-flex flex-wrap gap-3 justify-content-lg-end">
                                            @foreach ($provider->owner->identification_image_full_path as $image)
                                                <img class="max-w320" src="{{ $image }}"
                                                    alt="{{ translate('image') }}">
=======
                                        <h2 class="information-details-box__title c1 mb-3">{{translate('Business_Info')}}
                                        </h2>
                                        <p><strong
                                                class="text-capitalize">{{translate($provider->owner->identification_type)}}
                                                -</strong> {{$provider->owner->identification_number}}</p>
                                    </div>
                                    <div class="col-lg-9">
                                        <div class="d-flex flex-wrap gap-3 justify-content-lg-end">
                                            @foreach($provider->owner->identification_image_full_path as $image)
                                                <img class="max-w320" src="{{ $image }}" alt="{{translate('image')}}">
>>>>>>> newversion/main
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<<<<<<< HEAD
    <script src="{{ asset('public/assets/provider-module') }}/plugins/apex/apexcharts.min.js"></script>
=======

    <script src="{{asset('public/assets/provider-module')}}/plugins/apex/apexcharts.min.js"></script>
>>>>>>> newversion/main

    <script>
        "use strict";

        var options = {
            labels: ['accepted', 'ongoing', 'completed', 'canceled'],
<<<<<<< HEAD
            series: {{ json_encode($total) }},
=======
            series: {{json_encode($total)}},
>>>>>>> newversion/main
            chart: {
                width: 235,
                height: 160,
                type: 'donut',
            },
            dataLabels: {
                enabled: false
            },
            title: {
<<<<<<< HEAD
                text: "{{ $provider->bookings_count }} Bookings",
=======
                text: "{{$provider->bookings_count}} Bookings",
>>>>>>> newversion/main
                align: 'center',
                offsetX: 0,
                offsetY: 58,
                floating: true,
                style: {
                    fontSize: '12px',
                    fontWeight: 600,
                },
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    legend: {
                        show: true
                    }
                }
            }],
            legend: {
                position: 'bottom',
                offsetY: -5,
                height: 30,
            },
        };

        var chart = new ApexCharts(document.querySelector("#apex-pie-chart"), options);
        chart.render();

<<<<<<< HEAD
        $('.provider_approval').on('click', function() {
=======
        $('.provider_approval').on('click', function () {
>>>>>>> newversion/main
            let itemId = $(this).data('approve');
            let route = '{{ route('admin.provider.update-approval', ['id' => ':itemId', 'status' => 'deny']) }}';
            route = route.replace(':itemId', itemId);
            route_alert_reload(route, '{{ translate('want_to_deny_the_provider') }}');
        });

<<<<<<< HEAD
        $('.approval_provider').on('click', function() {
            let itemId = $(this).data('approve');
            let route =
            '{{ route('admin.provider.update-approval', ['id' => ':itemId', 'status' => 'approve']) }}';
=======
        $('.approval_provider').on('click', function () {
            let itemId = $(this).data('approve');
            let route = '{{ route('admin.provider.update-approval', ['id' => ':itemId', 'status' => 'approve']) }}';
>>>>>>> newversion/main
            route = route.replace(':itemId', itemId);
            route_alert_reload(route, '{{ translate('want_to_approve_the_provider') }}');
        });
    </script>
@endpush
