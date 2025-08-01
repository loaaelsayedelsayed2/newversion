@extends('adminmodule::layouts.master')

@section('title',translate('customer_list'))

@push('css_or_js')
    <link rel="stylesheet" href="{{asset('public/assets/admin-module/plugins/dataTables/jquery.dataTables.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('public/assets/admin-module/plugins/dataTables/select.dataTables.min.css')}}"/>
@endpush

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div
                        class="page-title-wrap d-flex justify-content-between flex-wrap align-items-center gap-3 mb-3">
                        <h2 class="page-title">{{translate('customer_list')}}</h2>
                        @can('customer_add')
                            <div>
                                <a href="{{route('admin.customer.create')}}" class="btn btn--primary">
                                    <span class="material-icons">add</span>
                                    {{translate('add_customer')}}
                                </a>
                            </div>
                        @endcan
                    </div>

                    <div
                        class="d-flex flex-wrap justify-content-between align-items-center border-bottom mx-lg-4 mb-10 gap-3">
                        <ul class="nav nav--tabs">
                            <li class="nav-item">
                                <a class="nav-link {{$status=='all'?'active':''}}"
                                   href="{{url()->current()}}?status=all">
                                    {{translate('all')}}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{$status=='active'?'active':''}}"
                                   href="{{url()->current()}}?status=active">
                                    {{translate('active')}}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{$status=='inactive'?'active':''}}"
                                   href="{{url()->current()}}?status=inactive">
                                    {{translate('inactive')}}
                                </a>
                            </li>
                        </ul>

                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="mb-3 fz-16">{{translate('Search_Filter')}}</div>

                            <form action="{{ url()->current() }}" method="GET">
                                <div class="row">
                                    <input type="hidden" name="search" value="{{array_key_exists('search', $queryParam)?$queryParam['search']:''}}">
                                    <div class="col-lg-3 col-sm-6" id="from-filter__div">
                                        <div class="form-floating">
                                            <input type="date" class="form-control" id="from" name="from" value="{{array_key_exists('from', $queryParam)?$queryParam['from']:''}}">
                                            <label for="from">{{translate('start_date')}}</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6" id="to-filter__div">
                                        <div class="form-floating">
                                            <input type="date" class="form-control" id="to" name="to" value="{{array_key_exists('to', $queryParam)?$queryParam['to']:''}}">
                                            <label for="to">{{translate('end_date')}}</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <div class="form-floating">
                                            <select class="js-select" name="sort_by">
                                                <option value="" selected>{{translate('Select option')}}</option>
                                                <option value="latest" {{array_key_exists('sort_by', $queryParam) && $queryParam['sort_by'] == 'latest' ? 'selected' : ''}}>{{translate('latest')}}</option>
                                                <option value="oldest" {{array_key_exists('sort_by', $queryParam) && $queryParam['sort_by'] == 'oldest' ? 'selected' : ''}}>{{translate('oldest')}}</option>
                                                <option value="ascending" {{array_key_exists('sort_by', $queryParam) && $queryParam['sort_by'] == 'ascending' ? 'selected' : ''}}>{{translate('ascending')}}</option>
                                                <option value="descending" {{array_key_exists('sort_by', $queryParam) && $queryParam['sort_by'] == 'descending' ? 'selected' : ''}}>{{translate('descending')}}</option>
                                            </select>
                                            <label class="mb-2">{{translate('sort_by')}}</label>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-sm-6">
                                        <div class="form-floating">
                                            <input class="form-control" type="number" name="limit" value="{{array_key_exists('limit', $queryParam)?$queryParam['limit']:''}}">
                                            <label class="mb-2">{{translate('choose_first')}}</label>
                                        </div>
                                    </div>
                                    <input type="hidden" name="status" value="{{$status}}">

                                    <div class="col-12 d-flex justify-content-end mt-3">
                                        <button type="submit" class="btn btn--primary btn-sm">{{translate('filter')}}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom mx-lg-4 mb-10 gap-3">
                        @php
                            $baseQuery = $queryParam;
                        @endphp

                        <ul class="nav nav--tabs">
                            <li class="nav-item">
                                <a class="nav-link {{ $status == 'all' ? 'active' : '' }}"
                                   href="{{ url()->current() . '?' . http_build_query(array_merge($baseQuery, ['status' => 'all'])) }}">
                                    {{ translate('all') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $status == 'active' ? 'active' : '' }}"
                                   href="{{ url()->current() . '?' . http_build_query(array_merge($baseQuery, ['status' => 'active'])) }}">
                                    {{ translate('active') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $status == 'inactive' ? 'active' : '' }}"
                                   href="{{ url()->current() . '?' . http_build_query(array_merge($baseQuery, ['status' => 'inactive'])) }}">
                                    {{ translate('inactive') }}
                                </a>
                            </li>
                        </ul>
                        <div class="d-flex gap-2 fw-medium">
                            <span class="opacity-75">{{translate('Total_Customers')}}:</span>
                            <span class="title-color">{{$customers->total()}}</span>
                        </div>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="all-tab-pane">
                            <div class="card">
                                <div class="card-body">
                                    <div class="data-table-top d-flex flex-wrap gap-10 justify-content-between">
                                        <form action="{{url()->current()}}?status={{$status}}"
                                              class="search-form search-form_style-two"
                                              method="GET">
                                            <div class="input-group search-form__input_group">
                                            <span class="search-form__icon">
                                                <span class="material-icons">search</span>
                                            </span>
                                                <input type="search" class="theme-input-style search-form__input"
                                                       value="{{$search}}" name="search"
                                                       placeholder="{{translate('search_here')}}">
                                            </div>

                                            <!-- Preserve all other filters -->
                                            <input type="hidden" name="from" value="{{ $queryParam['from'] ?? '' }}">
                                            <input type="hidden" name="to" value="{{ $queryParam['to'] ?? '' }}">
                                            <input type="hidden" name="sort_by" value="{{ $queryParam['sort_by'] ?? '' }}">
                                            <input type="hidden" name="limit" value="{{ $queryParam['limit'] ?? '' }}">
                                            <input type="hidden" name="status" value="{{ $queryParam['status'] ?? '' }}">

                                            <button type="submit"
                                                    class="btn btn--primary">{{translate('search')}}</button>
                                        </form>
                                        @can('customer_export')
                                            <div class="d-flex flex-wrap align-items-center gap-3">
                                                <div class="dropdown">
                                                    <button type="button"
                                                            class="btn btn--secondary text-capitalize dropdown-toggle"
                                                            data-bs-toggle="dropdown">
                                                        <span class="material-icons">file_download</span> download
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                                        <li>
                                                            <a class="dropdown-item"
                                                               href="{{env('APP_ENV') !='demo' ?route('admin.customer.download', '?search='. ($queryParam['search'] ?? '') .
                                                                         '&from='. ($queryParam['from'] ?? '') .
                                                                         '&to='. ($queryParam['to'] ?? '') .
                                                                         '&limit='. ($queryParam['limit'] ?? '') .
                                                                         '&status='. ($queryParam['status'] ?? '') .
                                                                         '&sort_by='. ($queryParam['sort_by'] ?? '') ).'?search='.$search:'javascript:demo_mode()'}}">
                                                                {{translate('excel')}}
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        @endcan
                                    </div>

                                    <div class="table-responsive">
                                        <table id="example" class="table align-middle">
                                            <thead>
                                            <tr>
                                                <th>{{translate('Sl')}}</th>
                                                <th>{{translate('Customer_Name')}}</th>
                                                <th class="text-center">{{translate('Contact_Info')}}</th>
                                                <th class="text-center">{{translate('Total_Bookings')}}</th>
                                                <th class="text-center">{{translate('Joined')}}</th>
                                                @can('customer_manage_status')
                                                    <th class="text-center">{{translate('status')}}</th>
                                                @endcan
                                                <th class="text-center">{{translate('action')}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @php
                                                $count= 0;
                                            @endphp

                                            @foreach($customers as $key => $customer)
                                                <tr>
                                                    <td>{{ (request()->get('limit') ?  $count++ : $key  )+ $customers->firstItem() }}</td>
                                                    <td>
                                                        <a href="{{route('admin.customer.detail',[$customer->id, 'web_page'=>'overview'])}}">
                                                            {{$customer->first_name}} {{$customer->last_name}}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-column align-items-center gap-1">
                                                            @if(env('APP_ENV')=='demo')
                                                                <label class="badge badge-primary">
                                                                    {{translate('protected')}}
                                                                </label>
                                                            @else
                                                                <a href="mailto:{{$customer->email}}"
                                                                   class="fz-12 fw-medium">
                                                                    {{$customer->email}}
                                                                </a>
                                                                <a href="tel:{{$customer->phone}}"
                                                                   class="fz-12 fw-medium">
                                                                    {{$customer->phone}}
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="text-center">{{$customer->bookings_count}}</td>
                                                    <td class="text-center">{{date('d M, Y',strtotime($customer->created_at))}}</td>
                                                    @can('customer_manage_status')
                                                        <td>
                                                            <label class="switcher mx-auto" data-bs-toggle="modal"
                                                                   data-bs-target="#deactivateAlertModal">
                                                                <input class="switcher_input"
                                                                       type="checkbox"
                                                                       {{$customer->is_active?'checked':''}} data-status="{{$customer->id}}">
                                                                <span class="switcher_control"></span>
                                                            </label>
                                                        </td>
                                                    @endcan
                                                    <td>
                                                        <div class="d-flex gap-2 justify-content-center">
                                                            @can('customer_update')
                                                                <a href="{{env('APP_ENV') !='demo' ?route('admin.customer.edit',[$customer->id]):'javascript:demo_mode()'}}"
                                                                   class="action-btn btn--light-primary"
                                                                   style="--size: 30px">
                                                                    <span class="material-icons">edit</span>
                                                                </a>
                                                            @endcan
                                                            @can('customer_delete')
                                                                <button type="button" data-delete="{{$customer->id}}"
                                                                        data-id="delete-{{$customer->id}}"
                                                                        data-message="{{translate('want_to_delete_this_customer')}}?"
                                                                        class="action-btn btn--danger {{ env('APP_ENV') != 'demo' ? 'form-alert' : 'demo_check' }}"
                                                                        style="--size: 30px">
                                                                    <span
                                                                        class="material-symbols-outlined">delete</span>
                                                                </button>
                                                            @endcan
                                                            <a href="{{route('admin.customer.detail',[$customer->id, 'web_page'=>'overview'])}}"
                                                               class="action-btn btn--light-primary"
                                                               style="--size: 30px">
                                                                <span class="material-icons">visibility</span>
                                                            </a>
                                                        </div>
                                                        <form
                                                            action="{{route('admin.customer.delete',[$customer->id])}}"
                                                            method="post" id="delete-{{$customer->id}}" class="hidden">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        {!! $customers->links() !!}
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
    <script src="{{asset('public/assets/admin-module')}}/plugins/select2/select2.min.js"></script>
    <script>
        "use strict"
        $(document).ready(function () {
            $('.js-select').select2();
        });

        $('.switcher_input').on('click', function () {
            let itemId = $(this).data('status');
            let route = '{{ route('admin.customer.status-update', ['id' => ':itemId']) }}';
            route = route.replace(':itemId', itemId);
            route_alert(route, '{{ translate('want_to_update_status') }}');
        })
    </script>
    <script src="{{asset('public/assets/admin-module/plugins/dataTables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('public/assets/admin-module/plugins/dataTables/dataTables.select.min.js')}}"></script>
@endpush
