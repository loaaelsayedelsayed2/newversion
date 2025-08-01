@extends('adminmodule::layouts.master')

@section('title',translate('add_new_discount'))

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-wrap mb-3 d-flex justify-content-between">
                        <h2 class="page-title">{{translate('add_new_discount')}}</h2>

                        <div><i class="material-icons" data-bs-toggle="modal" data-bs-target="#alertModal">info</i></div>
                    </div>
                    <div class="card mb-30">
                        <div class="card-body p-30">
                            <form action="{{route('admin.discount.store')}}" method="POST">
                                @csrf
                                <div class="discount-type">
                                    <div class="mb-3">{{translate('discount_type')}}</div>
                                    <div class="d-flex flex-wrap align-items-center gap-4 mb-30">
                                        <div class="custom-radio">
                                            <input type="radio" id="category" name="discount_type" value="category" checked>
                                            <label for="category">{{translate('category_wise')}}</label>
                                        </div>
                                        <div class="custom-radio">
                                            <input type="radio" id="service" name="discount_type" value="service">
                                            <label for="service">{{translate('service_wise')}}</label>
                                        </div>
                                        <div class="custom-radio">
                                            <input type="radio" id="mixed" name="discount_type" value="mixed">
                                            <label for="mixed">{{translate('mixed')}}</label>
                                        </div>
                                    </div>

                                    <div class="mb-30">
                                        <div class="form-floating form-floating__icon">
                                            <input type="text" class="form-control" name="discount_title"
                                                   placeholder="{{translate('discount_title')}} *"
                                                   required="" maxlength="191">
                                            <label>{{translate('discount_title')}} *</label>
                                            <span class="material-icons">title</span>
                                        </div>
                                    </div>
                                    <div class="mb-30" id="category_selector">
                                        <select class="category-select theme-input-style w-100" name="category_ids[]"
                                                multiple="multiple" id="category_selector__select" required>
                                            <option value="0" disabled>{{translate('Select Category')}}</option>
                                            <option value="all">{{translate('Select All')}}</option>
                                            @foreach($categories as $category)
                                                <option value="{{$category->id}}">{{$category->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-30 service_selector" id="service_selector">
                                        <select class="service-select theme-input-style w-100" name="service_ids[]"
                                                multiple="multiple" id="service_selector__select">
                                            <option value="all">{{translate('Select All')}}</option>
                                            @foreach($services as $service)
                                                <option value="{{$service->id}}">{{$service->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-30">
                                        <select class="zone-select theme-input-style w-100" name="zone_ids[]"
                                                multiple="multiple" id="zone_selector__select" required>
                                            <option value="0" disabled>{{translate('Select Zone')}}</option>
                                            <option value="all">{{translate('Select All')}}</option>
                                            @foreach($zones as $zone)
                                                <option value="{{$zone->id}}">{{$zone->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="discount-amount-type">
                                    <div class="mb-3">{{translate('discount_amount_type')}} *</div>
                                    <div class="d-flex align-items-center gap-4 mb-30">
                                        <div class="custom-radio">
                                            <input type="radio" id="percentage" name="discount_amount_type" value="percent" checked>
                                            <label for="percentage">{{translate('percentage')}}</label>
                                        </div>
                                        <div class="custom-radio">
                                            <input type="radio" id="fixed_amount" name="discount_amount_type" value="amount">
                                            <label for="fixed_amount">{{translate('fixed_amount')}}</label>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="mb-30">
                                                <div class="form-floating form-floating__icon">
                                                    <input type="number" class="form-control" name="discount_amount"
                                                           placeholder="{{translate('amount')}}" id="discount_amount"
                                                           min="0" max="100" step="any" value="0">
                                                    <label id="discount_amount__label">{{translate('amount')}} (%) *</label>
                                                    <span class="material-icons">price_change</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-30">
                                                <div class="form-floating">
                                                    <input type="date" class="form-control" name="start_date" value="{{now()->format('Y-m-d')}}">
                                                    <label>{{translate('start_date')}} *</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-30">
                                                <div class="form-floating">
                                                    <input type="date" class="form-control" name="end_date" value="{{now()->addDays(2)->format('Y-m-d')}}">
                                                    <label>{{translate('end_date')}} *</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-30">
                                                <div class="form-floating form-floating__icon">
                                                    <input type="number" class="form-control"
                                                           name="min_purchase"
                                                           placeholder="{{translate('min_purchase')}} ({{currency_symbol()}}) *"
                                                           min="0" value="0" step="any">
                                                    <label>{{translate('min_purchase_amount')}} ({{currency_symbol()}}) *</label>
                                                    <span class="material-icons">price_change</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4" id="max_discount_amount">
                                            <div class="mb-30">
                                                <div class="form-floating form-floating__icon">
                                                    <input type="number" class="form-control" step="any"
                                                           name="max_discount_amount"
                                                           placeholder="{{translate('max_discount')}} ({{currency_symbol()}}) *"
                                                           min="0.01" value="0">
                                                    <label>{{translate('max_discount')}} ({{currency_symbol()}}) *</label>
                                                    <span class="material-icons">price_change</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn--primary">{{translate('submit')}}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header pb-0 border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pb-sm-5 px-sm-5">
                    <div class="d-flex flex-column align-items-center gap-2 text-center">
                        <h3>{{translate('Alert')}}!</h3>
                        <p class="fw-medium">
                            {{translate('This request is with customized instructions. Please read the customer description and instructions thoroughly and place your pricing according to this')}}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        "use Strict";

        $('#category_selector__select').on('change', function() {
            var selectedValues = $(this).val();
            if (selectedValues !== null && selectedValues.includes('all')) {
                $(this).find('option').not(':disabled').prop('selected', 'selected');
                $(this).find('option[value="all"]').prop('selected', false);
            }
        });

        $('#service_selector__select').on('change', function() {
            var selectedValues = $(this).val();
            if (selectedValues !== null && selectedValues.includes('all')) {
                $(this).find('option').not(':disabled').prop('selected', 'selected');
                $(this).find('option[value="all"]').prop('selected', false);
            }
        });

        $('#zone_selector__select').on('change', function() {
            var selectedValues = $(this).val();
            if (selectedValues !== null && selectedValues.includes('all')) {
                $(this).find('option').not(':disabled').prop('selected', 'selected');
                $(this).find('option[value="all"]').prop('selected', false);
            }
        });

        $('#category').on('click', function () {
            $('#category_selector').show();
            $('#service_selector').hide();

            $('#category_selector__select').prop('required',true);
            $('#service_selector__select').prop('required',false);
        });

        $('#service').on('click', function () {
            $('#category_selector').hide();
            $('#service_selector').show();

            $('#service_selector__select').prop('required',true);
            $('#category_selector__select').prop('required',false);
        });

        $('#mixed').on('click', function () {
            $('#category_selector').show();
            $('#service_selector').show();

            $('#service_selector__select').prop('required',true);
            $('#category_selector__select').prop('required',true);
        });

        $('#percentage').on('click', function () {
            $('#max_discount_amount').show();

            $('#discount_amount').attr({"max" : 100});
            $('#discount_amount__label').html('{{translate('amount')}} (%)');
        });

        $('#fixed_amount').on('click', function () {
            $('#max_discount_amount').hide();

            $('#discount_amount').removeAttr('max');
            $('#discount_amount__label').html('{{translate('amount')}} ({{currency_symbol()}})');
        });

        //Select 2
        $(".category-select").select2({
            placeholder: "{{translate('Select Category')}}",
        });
        $(".service-select").select2({
            placeholder: "{{translate('Select Service')}}",
        });
        $(".zone-select").select2({
            placeholder: "{{translate('Select Zone')}}",
        });
    </script>
@endpush
