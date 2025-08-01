@extends('adminmodule::layouts.master')

@section('title',translate('Send Notification'))

@push('css_or_js')
    <link rel="stylesheet" href="{{asset('public/assets/admin-module')}}/plugins/select2/select2.min.css"/>
    <link rel="stylesheet" href="{{asset('public/assets/admin-module')}}/plugins/dataTables/jquery.dataTables.min.css"/>
    <link rel="stylesheet" href="{{asset('public/assets/admin-module')}}/plugins/dataTables/select.dataTables.min.css"/>
@endpush

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-wrap mb-3">
                        <h2 class="page-title">{{translate('push_notification')}}</h2>
                    </div>

                    @can('push_notification_add')
                        <div class="card mb-30">
                            <div class="card-body p-30">
                                <form action="{{route('admin.push-notification.store')}}" method="POST"
                                      enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-6 mb-4 mb-lg-0">
                                            <div class="form-floating form-floating__icon mb-30">
                                                <input type="text" class="form-control" id="floatingInput" name="title"
                                                       placeholder="Title" maxlength="191" required="">
                                                <label for="floatingInput">{{translate('title')}}</label>
                                                <span class="material-icons">title</span>
                                            </div>
                                            <div class="form-floating mb-30">
                                            <textarea class="form-control resize-none" id="floatingInput2"
                                                      placeholder="{{translate('description')}}"
                                                      name="description"></textarea>
                                                <label for="floatingInput2">{{translate('description')}}</label>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="mb-30">
                                                        <select class="select-zone theme-input-style w-100"
                                                                name="zone_ids[]" id="zone_selector__select"
                                                                multiple="multiple">
                                                            <option value="all">{{translate('Select All')}}</option>
                                                            @foreach($zones as $zone)
                                                                <option value="{{$zone->id}}">{{$zone->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="mb-30">
                                                        <select class="select-user theme-input-style w-100"
                                                                name="to_users[]" id="user_selector__select"
                                                                multiple="multiple">
                                                            <option value="all">{{translate('all')}}</option>
                                                            <option value="customer">{{translate('customer')}}</option>
                                                            <option value="provider-admin">
                                                                {{translate('provider')}}
                                                            </option>
                                                            <option value="provider-serviceman">
                                                                {{translate('serviceman')}}
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="d-flex flex-column align-items-center gap-3">
                                                <p class="title-color mb-0">{{translate('upload_cover_image')}}</p>

                                                <div class="upload-file">
                                                    <input type="file" class="upload-file__input" name="cover_image"
                                                           accept=".{{ implode(',.', array_column(IMAGEEXTENSION, 'key')) }}, |image/*">
                                                    <div class="upload-file__img upload-file__img_banner">
                                                        <img
                                                            src="{{asset('public/assets/admin-module/img/media/banner-upload-file.png')}}"
                                                            alt="{{ translate('notification') }}">
                                                    </div>
                                                    <span class="upload-file__edit">
                                                    <span class="material-icons">edit</span>
                                                </span>
                                                </div>

                                                <p class="opacity-75 max-w220 mx-auto">{{translate('Image format - jpg,
                                                png, jpeg, gif Image Size - maximum size 2 MB Image
                                                Ratio - 2:1')}}</p>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="d-flex justify-content-end gap-20 mt-30">
                                                <button class="btn btn--secondary"
                                                        type="reset">{{translate('reset')}}</button>
                                                <button class="btn btn--primary demo_check"
                                                        type="submit">{{translate('submit')}}</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endcan

                    <div
                        class="d-flex flex-wrap justify-content-between align-items-center border-bottom mx-lg-4 mb-10 gap-3">
                        <ul class="nav nav--tabs">
                            <li class="nav-item">
                                <a class="nav-link {{$toUserType=='all'?'active':''}}"
                                   href="{{url()->current()}}?to_user_type=all">
                                    {{translate('all')}}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{$toUserType=='customer'?'active':''}}"
                                   href="{{url()->current()}}?to_user_type=customer">
                                    {{translate('customer')}}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{$toUserType=='provider-admin'?'active':''}}"
                                   href="{{url()->current()}}?to_user_type=provider-admin">
                                    {{translate('provider')}}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{$toUserType=='provider-serviceman'?'active':''}}"
                                   href="{{url()->current()}}?to_user_type=provider-serviceman">
                                    {{translate('serviceman')}}
                                </a>
                            </li>
                        </ul>

                        <div class="d-flex gap-2 fw-medium">
                            <span class="opacity-75">{{translate('Push_Notifications')}}:</span>
                            <span class="title-color">{{$pushNotification->total()}}</span>
                        </div>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="all-tab-pane">
                            <div class="card">
                                <div class="card-body">
                                    <div class="data-table-top d-flex flex-wrap gap-10 justify-content-between">
                                        <form action="{{url()->current()}}?to_user_type={{$toUserType}}"
                                              class="search-form search-form_style-two"
                                              method="POST">
                                            @csrf
                                            <div class="input-group search-form__input_group">
                                            <span class="search-form__icon">
                                                <span class="material-icons">search</span>
                                            </span>
                                                <input type="search" class="theme-input-style search-form__input"
                                                       value="{{$search}}" name="search"
                                                       placeholder="{{translate('search_here')}}">
                                            </div>
                                            <button type="submit"
                                                    class="btn btn--primary">{{translate('search')}}</button>
                                        </form>

                                        <div class="d-flex flex-wrap align-items-center gap-3">
                                            @can('push_notification_export')
                                                <div class="dropdown">
                                                    <button type="button"
                                                            class="btn btn--secondary text-capitalize dropdown-toggle"
                                                            data-bs-toggle="dropdown">
                                                    <span
                                                        class="material-icons">file_download</span> {{translate('download')}}
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                                        <a class="dropdown-item"
                                                           href="{{route('admin.push-notification.download')}}?search={{$search}}">
                                                            {{translate('excel')}}
                                                        </a>
                                                    </ul>
                                                </div>
                                            @endcan
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="example" class="table align-middle">
                                            <thead class="text-nowrap">
                                            <tr>
                                                <th>{{translate('Sl')}}</th>
                                                <th>{{translate('title')}}</th>
                                                <th>{{translate('cover_image')}}</th>
                                                <th>{{translate('send_to')}}</th>
                                                <th>{{translate('zones')}}</th>
                                                @can('push_notification_manage_status')
                                                    <th>{{translate('status')}}</th>
                                                @endcan
                                                @canany(['push_notification_delete', 'push_notification_update'])
                                                    <th>{{translate('action')}}</th>
                                                @endcan
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($pushNotification as $key => $item)
                                                <tr>
                                                    <td>{{$key+$pushNotification->firstItem()}}</td>
                                                    <td>{{$item->title}}</td>
                                                    <td>
                                                        <img src="{{$item->cover_image_full_path}}" class="table-cover-img" alt="">
                                                    </td>
                                                    <td>
                                                        @foreach($item->to_users as $key=>$user)
                                                            {{$user}}{{$key+1==count($item->to_users)?'':','}}
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        @foreach($item->zone_ids as $key=>$zone)
                                                            {{$zone['name']}}{{$key+1==count($item->zone_ids)?'':','}}
                                                        @endforeach
                                                    </td>
                                                    @can('push_notification_manage_status')
                                                        <td>
                                                            <label class="switcher">
                                                                <input class="switcher_input"
                                                                       data-status="{{$item->id}}"
                                                                       type="checkbox" {{$item->is_active?'checked':''}}>
                                                                <span class="switcher_control"></span>
                                                            </label>
                                                        </td>
                                                    @endcan
                                                    @canany(['push_notification_delete', 'push_notification_update'])
                                                        <td>
                                                            <div class="d-flex gap-2">
                                                                @can('push_notification_update')
                                                                    <a href="{{route('admin.push-notification.edit',[$item->id])}}"
                                                                       class="action-btn btn--light-primary">
                                                                        <span class="material-icons">edit</span>
                                                                    </a>
                                                                @endcan
                                                                @can('push_notification_delete')
                                                                    <button type="button"
                                                                            data-id="{{$item->id}}"
                                                                            class="action-btn btn--danger delete_section">
                                                                        <span class="material-icons">delete</span>
                                                                    </button>
                                                                    <form
                                                                        action="{{route('admin.push-notification.delete',[$item->id])}}"
                                                                        method="post" id="delete-{{$item->id}}"
                                                                        class="hidden">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                    </form>
                                                                @endcan
                                                            </div>
                                                        </td>
                                                    @endcan
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        {!! $pushNotification->links() !!}
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
        "use Strict";

        $('#user_selector__select').on('change', function () {
            var selectedValues = $(this).val();
            if (selectedValues !== null && selectedValues.includes('all')) {
                $(this).find('option').not(':disabled').prop('selected', 'selected');
                $(this).find('option[value="all"]').prop('selected', false);
            }
        });

        $('#zone_selector__select').on('change', function () {
            var selectedValues = $(this).val();
            if (selectedValues !== null && selectedValues.includes('all')) {
                $(this).find('option').not(':disabled').prop('selected', 'selected');
                $(this).find('option[value="all"]').prop('selected', false);
            }
        });

        $('.switcher_input').on('click', function () {
            let itemId = $(this).data('status');
            let route = '{{ route('admin.push-notification.status-update', ['id' => ':itemId']) }}';
            route = route.replace(':itemId', itemId);
            route_alert(route, '{{ translate('want_to_update_status') }}');
        })

        $('.delete_section').on('click', function () {
            let itemId = $(this).data('id');
            form_alert('delete-' + itemId, '{{ translate('want_to_delete_this') }}');
        })

        $(document).ready(function () {
            $('.js-select').select2();
        });

        $(document).ready(function () {
            $('.js-select').select2({
                placeholder: "{{translate('select_items')}}",
            });
            $('.select-zone').select2({
                placeholder: "{{translate('select_zones')}}",
            });
            $('.select-user').select2({
                placeholder: "{{translate('select_users')}}",
            });
        });

    </script>
    <script src="{{asset('public/assets/admin-module')}}/plugins/dataTables/jquery.dataTables.min.js"></script>
    <script src="{{asset('public/assets/admin-module')}}/plugins/dataTables/dataTables.select.min.js"></script>
@endpush

