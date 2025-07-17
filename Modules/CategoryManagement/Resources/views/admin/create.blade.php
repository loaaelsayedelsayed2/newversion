@extends('adminmodule::layouts.master')

@section('title',translate('category_setup'))

@push('css_or_js')
    <link rel="stylesheet" href="{{asset('public/assets/admin-module/plugins/select2/select2.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('public/assets/admin-module/plugins/dataTables/jquery.dataTables.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('public/assets/admin-module/plugins/dataTables/select.dataTables.min.css')}}"/>
@endpush

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-wrap mb-3">
                        <h2 class="page-title">{{translate('category_setup')}}</h2>
                    </div>

                    @can('category_add')
                        <div class="card category-setup mb-30">
                            <div class="card-body p-30">
                                <form action="{{route('admin.category.store')}}" method="post"
                                      enctype="multipart/form-data">
                                    @csrf
                                    @php($language= Modules\BusinessSettingsModule\Entities\BusinessSettings::where('key_name','system_language')->first())
                                    @php($default_lang = str_replace('_', '-', app()->getLocale()))
                                    @if($language)
                                        <ul class="nav nav--tabs border-color-primary mb-4">
                                            <li class="nav-item">
                                                <a class="nav-link lang_link active"
                                                   href="#"
                                                   id="default-link">{{translate('default')}}</a>
                                            </li>
                                            @foreach ($language?->live_values as $lang)
                                                <li class="nav-item">
                                                    <a class="nav-link lang_link"
                                                       href="#"
                                                       id="{{ $lang['code'] }}-link">{{ get_language_name($lang['code']) }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    <div class="row">
                                        <div class="col-lg-8 mb-5 mb-lg-0">
                                            <div class="d-flex flex-column">
                                                @if ($language)
                                                    <div class="form-floating form-floating__icon mb-30 lang-form"
                                                         id="default-form">
                                                        <input type="text" name="name[]" class="form-control" required
<<<<<<< HEAD
                                                               placeholder="{{translate('category_name')}}">
                                                        <label>{{translate('category_name')}}
                                                            ({{ translate('default') }}
                                                            )</label>
                                                        <span class="material-icons">subtitles</span>
                                                    </div>
                                                    <input type="hidden" name="lang[]" value="default">
                                                    @foreach ($language?->live_values as $lang)
=======
                                                               placeholder="{{translate('category_name')}}"
                                                               value="{{ old('name.0') }}">
                                                        <label>{{translate('category_name')}}({{ translate('default') }})</label>
                                                        <span class="material-icons">subtitles</span>
                                                    </div>
                                                    <input type="hidden" name="lang[]" value="default">
                                                    @foreach ($language?->live_values as $index => $lang)
>>>>>>> newversion/main
                                                        <div
                                                            class="form-floating form-floating__icon mb-30 d-none lang-form"
                                                            id="{{$lang['code']}}-form">
                                                            <input type="text" name="name[]" class="form-control"
<<<<<<< HEAD
                                                                   placeholder="{{translate('category_name')}}">
                                                            <label>{{translate('category_name')}}
                                                                ({{strtoupper($lang['code'])}})</label>
=======
                                                                   placeholder="{{translate('category_name')}}"
                                                                   value="{{ old('name.' . ($index + 1)) }}">
                                                            <label>{{translate('category_name')}}({{strtoupper($lang['code'])}})</label>
>>>>>>> newversion/main
                                                            <span class="material-icons">subtitles</span>
                                                        </div>
                                                        <input type="hidden" name="lang[]" value="{{$lang['code']}}">
                                                    @endforeach
                                                @else
                                                    <div class="form-floating form-floating__icon mb-30">
                                                        <input type="text" name="name[]" class="form-control"
<<<<<<< HEAD
                                                               placeholder="{{translate('category_name')}}" required>
=======
                                                               placeholder="{{translate('category_name')}}" required
                                                               value="{{ old('name.0') }}">
>>>>>>> newversion/main
                                                        <label>{{translate('category_name')}}</label>
                                                        <span class="material-icons">subtitles</span>
                                                    </div>
                                                    <input type="hidden" name="lang[]" value="default">
                                                @endif

<<<<<<< HEAD
                                                <select class="select-zone theme-input-style w-100" name="zone_ids[]"
                                                        multiple="multiple" id="zone_selector__select">
                                                    <option value="all">{{translate('Select All')}}</option>
                                                    @foreach($zones as $zone)
                                                        <option value="{{$zone['id']}}">{{$zone->name}}</option>
=======
                                                <select class="select-zone theme-input-style w-100" name="zone_ids[]" multiple="multiple" id="zone_selector__select" required>
                                                    <option value="all"  {{ (old('zone_ids') && in_array('all', old('zone_ids'))) ? 'selected' : '' }}>{{translate('Select All')}}</option>
                                                    @foreach($zones as $zone)
                                                        <option value="{{$zone['id']}}" {{ (old('zone_ids') && in_array($zone['id'], old('zone_ids'))) ? 'selected' : '' }}>{{$zone->name}}</option>
>>>>>>> newversion/main
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="d-flex  gap-3 gap-xl-5">
                                                <p class="opacity-75 max-w220">{{translate('image_format_-_jpg,_png,_jpeg,_gif_image
                                                size_-_
                                                maximum_size_2_MB_Image_Ratio_-_1:1')}}</p>
                                                <div class="d-flex align-items-center flex-column">
                                                    <div class="upload-file">
<<<<<<< HEAD
                                                        <input type="file" class="upload-file__input" name="image"
=======
                                                        <input type="file" class="upload-file__input" name="image" required
>>>>>>> newversion/main
                                                               accept=".{{ implode(',.', array_column(IMAGEEXTENSION, 'key')) }}, |image/*">
                                                        <div class="upload-file__img">
                                                            <img
                                                                src="{{asset('public/assets/admin-module/img/media/upload-file.png')}}"
                                                                alt="{{translate('image')}}">
                                                        </div>
                                                        <span class="upload-file__edit">
                                                        <span class="material-icons">edit</span>
                                                    </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="d-flex justify-content-end gap-20 mt-30">
<<<<<<< HEAD
                                                <button class="btn btn--secondary"
                                                        type="reset">{{translate('reset')}}</button>
=======
                                                <button class="btn btn--secondary" type="reset">{{translate('reset')}}</button>
>>>>>>> newversion/main
                                                <button class="btn btn--primary" type="submit">{{translate('submit')}}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endcan

<<<<<<< HEAD
                    <div
                        class="d-flex flex-wrap justify-content-between align-items-center border-bottom mx-lg-4 mb-10 gap-3">
=======
                    <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom mx-lg-4 mb-10 gap-3">
>>>>>>> newversion/main
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

                        <div class="d-flex gap-2 fw-medium">
                            <span class="opacity-75">{{translate('Total_Categories')}}:</span>
<<<<<<< HEAD
                            <span class="title-color">{{$categories->total()}}</span>
=======
                            <span class="title-color" id="totalListCount">{{$categories->total()}}</span>
>>>>>>> newversion/main
                        </div>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="all-tab-pane">
                            <div class="card">
                                <div class="card-body">
                                    <div class="data-table-top d-flex flex-wrap gap-10 justify-content-between">
                                        <form action="{{url()->current()}}?status={{$status}}"
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

                                        @can('category_export')
                                            <div class="d-flex flex-wrap align-items-center gap-3">
                                                <div class="dropdown">
                                                    <button type="button"
                                                            class="btn btn--secondary text-capitalize dropdown-toggle"
                                                            data-bs-toggle="dropdown">
                                                        <span class="material-icons">file_download</span> download
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                                        <li><a class="dropdown-item"
                                                               href="{{route('admin.category.download')}}?search={{$search}}">{{translate('excel')}}</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        @endcan
                                    </div>

<<<<<<< HEAD
                                    <div class="table-responsive">
                                        <table id="example" class="table align-middle">
                                            <thead class="align-middle">
                                            <tr>
                                                <th>{{translate('SL')}}</th>
                                                <th>{{translate('category_name')}}</th>
                                                <th>{{translate('sub_category_count')}}</th>
                                                <th>{{translate('zone_count')}}</th>
                                                @can('category_manage_status')
                                                    <th>{{translate('status')}}</th>
                                                @endcan
                                                @can('category_manage_status')
                                                    <th>{{translate('Is_Featured')}}</th>
                                                @endcan
                                                @canany(['category_delete', 'category_update'])
                                                    <th>{{translate('action')}}</th>
                                                @endcan
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($categories as $key=>$category)
                                                <tr>
                                                    <td>{{$categories->firstitem()+$key}}</td>
                                                    <td>{{$category->name}}</td>
                                                    <td>{{$category->children_count}}</td>
                                                    <td>{{$category->zones_count}}</td>
                                                    @can('category_manage_status')
                                                        <td>
                                                            <label class="switcher" data-bs-toggle="modal"
                                                                   data-bs-target="#deactivateAlertModal">
                                                                <input class="switcher_input status-update"
                                                                       type="checkbox"
                                                                       {{$category->is_active?'checked':''}} data-status="{{$category->id}}">
                                                                <span class="switcher_control"></span>
                                                            </label>
                                                        </td>
                                                    @endcan
                                                    @can('category_manage_status')
                                                        <td>
                                                            <label class="switcher">
                                                                <input class="switcher_input feature-update"
                                                                       type="checkbox"
                                                                       {{$category->is_featured?'checked':''}} data-featured="{{$category->id}}">
                                                                <span class="switcher_control"></span>
                                                            </label>
                                                        </td>
                                                    @endcan
                                                    @canany(['category_delete', 'category_update'])
                                                        <td>
                                                            <div class="d-flex gap-2">
                                                                @can('category_update')
                                                                    <a href="{{route('admin.category.edit',[$category->id])}}"
                                                                       class="action-btn btn--light-primary demo_check"
                                                                       style="--size: 30px">
                                                                        <span class="material-icons">edit</span>
                                                                    </a>
                                                                @endcan
                                                                @can('category_delete')
                                                                    <button type="button"
                                                                            data-id="delete-{{$category->id}}"
                                                                            data-message="{{translate('want_to_delete_this_category')}}?"
                                                                            class="action-btn btn--danger {{ env('APP_ENV') != 'demo' ? 'form-alert' : 'demo_check' }}"
                                                                            style="--size: 30px">
                                                                    <span
                                                                        class="material-symbols-outlined">delete</span>
                                                                    </button>
                                                                    <form
                                                                        action="{{route('admin.category.delete',[$category->id])}}"
                                                                        method="post" id="delete-{{$category->id}}"
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
                                        {!! $categories->links() !!}
                                    </div>
=======
                                    <div id="ListTableContainer">
                                        @include('categorymanagement::admin.partials._table')
                                    </div>

>>>>>>> newversion/main
                                </div>
                            </div>
                        </div>
                    </div>
<<<<<<< HEAD
=======


>>>>>>> newversion/main
                </div>
            </div>
        </div>
    </div>
<<<<<<< HEAD
=======

    <input type="hidden" id="offset" value="{{ request()->page }}">
>>>>>>> newversion/main
@endsection

@push('script')
    <script src="{{asset('public/assets/admin-module')}}/plugins/select2/select2.min.js"></script>
    <script src="{{asset('public/assets/category-module')}}/js/category/create.js"></script>
    <script src="{{asset('public/assets/admin-module')}}/plugins/dataTables/jquery.dataTables.min.js"></script>
    <script src="{{asset('public/assets/admin-module')}}/plugins/dataTables/dataTables.select.min.js"></script>

    <script>
        "use strict"

        $('#zone_selector__select').on('change', function () {
            var selectedValues = $(this).val();
            if (selectedValues !== null && selectedValues.includes('all')) {
                $(this).find('option').not(':disabled').prop('selected', 'selected');
                $(this).find('option[value="all"]').prop('selected', false);
            }
        });

<<<<<<< HEAD
        $('.status-update').on('click', function () {
            let itemId = $(this).data('status');
            let route = '{{route('admin.category.status-update',['id' => ':itemId'])}}';
            route = route.replace(':itemId', itemId);
            route_alert(route, '{{ translate('want_to_update_status') }}');
        })

        $('.feature-update').on('click', function () {
            let itemId = $(this).data('featured');
            let route = '{{route('admin.category.featured-update',['id' => ':itemId'])}}';
            route = route.replace(':itemId', itemId);
            route_alert(route, '{{ translate('want_to_update_status') }}');
        })
=======
        $('.feature-update').on('change', function (event) {
            event.preventDefault();
            let $this = $(this);
            let initialState = $this.prop('checked'); // Save initial state
            let itemId = $(this).data('featured');
            let route = '{{route('admin.category.featured-update',['id' => ':itemId'])}}';
            route = route.replace(':itemId', itemId);
            route_alert(route, '{{ translate('want_to_update_feature_status') }}', $this, initialState);
        })

        $('button[type="reset"]').on('click', function () {
            $('#zone_selector__select option').prop('selected', false).trigger('change');
        });

        let selectedItem;
        let selectedRoute;
        let initialState;
        let currentStatus = "{{ request('status', 'all') }}"; // Keep the current tab status

        $('.nav-link').on('click', function () {
            const urlParams = new URLSearchParams($(this).attr('href').split('?')[1]);
            currentStatus = urlParams.get('status') || 'all';
        });

        // Attach event listener for status change with event delegation
        $(document).on('change', '.status-update', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            selectedItem = $(this);
            initialState = selectedItem.prop('checked');

            // Revert checkbox visual state until confirmation
            selectedItem.prop('checked', !initialState);

            let itemId = selectedItem.data('id');
            selectedRoute = '{{ route('admin.category.status-update', ['id' => ':itemId']) }}'.replace(':itemId', itemId);

            let confirmationTitleText = initialState
                ? '{{ translate('Are you sure to Turn On the Category Status') }}?'
                : '{{ translate('Are you sure to Turn Off the Category Status') }}?';

            $('.confirmation-title-text').text(confirmationTitleText);

            let confirmationDescriptionText = initialState
                ? '{{ translate('Once you turn on the Category Status, the user can find the Category and its service for selection') }}.'
                : '{{ translate('Once you turn off the Category Status, the Provider can’t subscribe to the services of that category and the Customer can’t find the category & its service when they want to book') }}.';

            $('.confirmation-description-text').text(confirmationDescriptionText);

            let imgSrc = initialState
                ? "{{ asset('public/assets/admin-module/img/icons/status-on.png') }}"
                : "{{ asset('public/assets/admin-module/img/icons/status-off.png') }}";

            $('#confirmChangeModal img').attr('src', imgSrc);

            showModal();
        });

        $('#confirmChange').on('click', function () {
            updateStatus(selectedRoute);
        });

        //  Cancel and reset checkbox state
        $('.cancel-change').on('click', function () {
            resetCheckboxState();
            hideModal();
        });

        $('#confirmChangeModal').on('hidden.bs.modal', function () {
            resetCheckboxState();
        });

        //  Show/hide modal functions
        function showModal() {
            $('#confirmChangeModal').modal('show');
        }
        function hideModal() {
            $('#confirmChangeModal').modal('hide');
        }

        //  Reset the checkbox if change canceled
        function resetCheckboxState() {
            if (selectedItem) {
                selectedItem.prop('checked', !initialState);
            }
        }

        //  Submit the status change with AJAX
        function updateStatus(route) {
            let page = $('#offset').val();
            $.ajax({
                url: route,
                type: 'POST',
                data: {_token: '{{ csrf_token() }}'},
                dataType: 'json',
                success: function (data) {
                    toastr.success(data.message, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                    reloadTable(currentStatus, page);
                    hideModal();
                },
                error: function () {
                    resetCheckboxState();
                    toastr.error('Something went wrong! Please try again.');
                }
            });
        }

        // Reload the table after status update
        function reloadTable(status, page) {
            let search = $('input[name="search"]').val();

            $.ajax({
                url: "{{ route('admin.category.table') }}",
                type: "GET",
                data: {
                    status: status,
                    search: search,
                    page: page
                },
                success: function (response) {
                    if (response.page != page) {
                        updateBrowserUrl(status, search, response.page);
                        $('#offset').val((response.page - 1) * {{ pagination_limit() }});
                    } else {
                        $('#offset').val(response.offset);
                        updateBrowserUrl(status, search, page);
                    }

                    $('#totalListCount').html(response.totalCategory)
                    $('#ListTableContainer').empty().html(response.view);
                },
                error: function () {
                    toastr.error('Failed to update table. Please reload the page.', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        }

        // ✅ Update browser URL
        function updateBrowserUrl(status, search, page) {
            const params = new URLSearchParams();
            if (search) params.set('search', search);
            if (status) params.set('status', status);
            if (page > 1) params.set('page', page);

            const newUrl = `${window.location.pathname}?${params.toString()}`;
            window.history.replaceState({}, '', newUrl);
        }

>>>>>>> newversion/main
    </script>

@endpush
