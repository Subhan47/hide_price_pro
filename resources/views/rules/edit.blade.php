{{--@extends('shopify-app::layouts.default')--}}

{{--@section('content')--}}


<section class="full-width">
    <div id="main" class="full-width">
        <section class="row"></section>
        <div></div>
        <section class="row"></section>
        <article class="row" style="display: flex">
            <a onclick="home()" style="color: grey"><i class="icon-prev icon-style"></i> <strong>Rules</strong></a>
        </article>

        <article>
            <div class="columns nine">
                <h5>{{ strtoupper(config('shopify-app.app_name')) }}</h5>
            </div>
            <div class="columns three align-right">
                <div class="">
                    <a href="#" class="">Get Support</a> &nbsp;
                    <a href="#" class="">
                        <button class="secondary"><i class="icon-question icon-style"></i> <strong> User
                                Guide</strong></button>
                    </a>
                    <a href="#">
                        <button><i class="icon-gear icon-style"></i> <strong> General Setting</strong></button>
                    </a>
                </div>
            </div>
        </article>

        <article>
            <div class="row full-width">
                <div class="columns twelve">
                    <div class="sessionMessages"></div>
                    <div class="row">
                        <div class="columns twelve">
                            <section class="full-width table-section">
                                <div class="card">
                                    <div class="row">
                                        <ul class="tabs">
                                            <li class="active">
                                                <a href="#"><i class="icon-post icon-style"></i>
                                                    Rule</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <article>
                                        {!! Form::open(['route' => ['update-rule', $rule['id']] ,'class' => 'full-width']) !!}
                                        <div class="row"></div>
                                        <div class="row">
                                            <div class="input-group">
                                                <div class="columns two">
                                                    {!! Form::label('title', 'Rule Title') !!}
                                                </div>
                                                <div class="columns ten">
                                                    {!! Form::text('title', $rule['title'], ['id' => 'title', 'required' => 'required']) !!}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row"></div>
                                        <div class="row">
                                            <div class="input-group">
                                                <div class="columns two">
                                                    {!! Form::label('description', 'Rule Description (optional)') !!}
                                                </div>
                                                <div class="columns ten">
                                                    {!! Form::textarea('description', @$rule['description'], ['id' => 'description', 'placeholder' => 'This Rule ...','rows' => 3,]) !!}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row"></div>
                                        <div class="row">
                                            <div class="input-group">
                                                <div class="columns two">
                                                    <label for="variant_id">Select Variable Products Variants</label>
                                                </div>
                                                <div class="columns ten">
                                                    <select class="multipleSelect" id="variant_id" name="variant_id[]" multiple required style="">
                                                        @foreach ($products as $product)
                                                            @php
                                                                $variableProduct = false;
                                                            @endphp
                                                            @foreach ($product['variants'] as $variant)
                                                                @if($variant['title'] !== 'Default Title')
                                                                    @php
                                                                        $isSelected = in_array($variant->id, $ruleVariantIDs) ? 'selected' : '';
                                                                        $disabled = in_array($variant->id, $allRuleVariantIDs) ? 'disabled' : '';
                                                                        if (!$variableProduct) {
                                                                            echo '<optgroup label="' . @$product['title'] . '"></optgroup>';
                                                                            $variableProduct = true;
                                                                        }
                                                                    @endphp
                                                                    <option value="{{ $variant['id'] }}" {{ $disabled }} {{ $isSelected }}>
                                                                        {{ $product['title'] }} - {{ $variant['title'] }} - ${{ $variant['price'] }}
                                                                        {{ $disabled ? '(Another Rule Applied)' : '' }}
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row"></div>
                                        <div class="row">
                                            <div class="input-group">
                                                <div class="columns two">
                                                    {!! Form::label('is_enabled', 'Enable Hide Price Rule On Selected Product(s)') !!}
                                                </div>
                                                <div class="columns ten">
                                                    {!! Form::checkbox('is_enabled', 1, $rule['is_enabled'] ? true : false, ['id' => 'is_enabled']) !!}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="">Update</button>
                                        </div>

                                        {!! Form::close() !!}
                                    </article>
                                </div>
                            </section>

                        </div>
                    </div>

                </div>
            </div>
        </article>
    </div>
</section>



<script>
    $(document).ready(function() {
        $(".multipleSelect").select2({
        });

        $('form').on('submit', function(e) {
            e.preventDefault();
            $('#variant_id option:disabled').prop('disabled', false);
            var formData = $(this).serialize();
            $.ajax({
                url: '{{ route('update-rule', ['id' => $rule->id]) }}',
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        var disabledOptions = response.disabledOptions;
                        disabledOptions.forEach(function(disabledId) {
                            $('#variant_id option[value="' + disabledId + '"]').prop('disabled', true);
                        });
                        $('#variant_id').trigger('change');
                        var successMessage = '<div class="alert success"><a class="close" href="#"></a><dl><dt>' + response.success + '</dt></dl></div>';
                        $('.sessionMessages').html(successMessage);
                    }
                },
                error: function(xhr) {
                    var disabledOptions = xhr.responseJSON.disabledOptions;
                    if (Array.isArray(disabledOptions) && disabledOptions.length > 0) {
                        disabledOptions.forEach(function(disabledId) {
                            $('#variant_id option[value="' + disabledId + '"]').prop('disabled', true);
                        });
                        $('#variant_id').trigger('change');
                    } else {
                        console.error('Disabled options not provided or not in the expected format.');
                    }

                    var errorList = [];
                    if (xhr.responseJSON && Array.isArray(xhr.responseJSON.errors)) {
                        errorList = xhr.responseJSON.errors;
                    } else if (xhr.responseJSON && typeof xhr.responseJSON.errors === 'string') {
                        errorList.push(xhr.responseJSON.errors);
                    } else {
                        errorList.push('An unknown error occurred.');
                    }

                    var errorHtml = '<div class="alert error my-2"><a class="close" href="#"></a><ul>';
                    errorList.forEach(function(error) {
                        errorHtml += '<li>' + error + '</li>';
                    });
                    errorHtml += '</ul></div>';
                    $('.sessionMessages').html(errorHtml);
                }
            });
        });

    });
</script>
{{--@endsection--}}

