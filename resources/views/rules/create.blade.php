@extends('shopify-app::layouts.default')

@section('content')

    <main class="container">
        <span class="d-flex">
             <a href="{{ URL::tokenRoute('home') }}" style="color: grey"><i class="icon-prev icon-style"></i> <strong>Rules</strong></a>
        </span>

        <div class="nav-buttons">
            <strong>{{ strtoupper(config('shopify-app.app_name')) }}</strong>
            <div class="">
                <a href="#" class="">Get Support</a>
                <a href="#" class="mx-2"><button class="secondary"><i class="icon-question icon-style"></i> <strong> User Guide</strong></button></a>
                <a href="#"><button><i class="icon-gear icon-style"></i> <strong> General Setting</strong></button></a>
            </div>
        </div>

        <div class="sessionMessages">
        </div>

        <section class="full-width table-section">
            <div class="card listing-card">
                <div class="row">
                    <ul class="tabs">
                        <li class="active">
                            <a href="#"><i class="icon-post icon-style"></i> Rule</a>
                        </li>
                    </ul>
                </div>

                    {!! Form::open(['route' => 'store-rule']) !!}

                    <div class="row py-2">
                        <div class="input-group">
                            <div class="columns three">
                                {!! Form::label('title', 'Rule Title') !!}
                            </div>
                            <div class="columns nine">
                                {!! Form::text('title', null, ['id' => 'title', 'required' => 'required']) !!}
                            </div>
                        </div>
                    </div>


                    <div class="row py-2">
                        <div class="input-group">
                            <div class="columns three">
                                {!! Form::label('description', 'Rule Description (optional)') !!}
                            </div>
                            <div class="columns nine">
                                {!! Form::textarea('description', null, ['id' => 'description', 'placeholder' => 'This Rule ...','rows' => 3,]) !!}
                            </div>
                        </div>
                    </div>

                    <div class="row py-2">
                        <div class="input-group">
                            <div class="columns three">
                                {!! Form::label('variant_id', 'Select Variable Products Variants') !!}
                            </div>
                            <div class="columns nine">
                                <select class="multipleSelect" id="variant_id" name="variant_id[]" multiple required style="">
                                    <option>Select Variants</option>
                                    @foreach ($products as $product)
                                        <optgroup label="{{ @$product['title'] }}">
                                            @foreach ($product['variants'] as $variant)
                                                @php
                                                    $disabled = in_array($variant->id, $allRuleVariantIDs) ? 'disabled' : '';
                                                @endphp
                                                <option value="{{ $variant['id'] }}" {{ $disabled }}>
                                                    {{ $variant['title'] }} - ${{ $variant['price'] }}
                                                    {{ $disabled ? '(Another Rule Applied)' : '' }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row py-2">
                        <div class="input-group">
                            <div class="columns three">
                                {!! Form::label('is_enabled', 'Enable Hide Price Rule On Selected Product(s)') !!}
                            </div>
                            <div class="columns nine">
                                {!! Form::checkbox('is_enabled', 1, true, ['id' => 'is_enabled']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button class="" type="submit">Save</button>
                    </div>

                {!! Form::close() !!}
            </div>
        </section>

    </main>


    <script>
        $(document).ready(function() {
            $(".multipleSelect").select2({
                theme: "classic",
                // dropdownCssClass: "custom-select2-dropdown"
            });


            $('form').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();

                $.ajax({
                    url: '{{ route('store-rule') }}',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $('form')[0].reset();
                            var successMessage = '<div class="alert success"><a class="close" href=""></a><dl><dt>' + response.success + '</dt></dl></div>';
                            $('.sessionMessages').html(successMessage);
                        }
                    },
                    error: function(xhr) {
                        var errorList = [];
                        if (xhr.responseJSON && Array.isArray(xhr.responseJSON.errors)) {
                            errorList = xhr.responseJSON.errors;
                        } else if (xhr.responseJSON && typeof xhr.responseJSON.errors === 'string') {
                            errorList.push(xhr.responseJSON.errors);
                        } else {
                            console.error(xhr.responseText); // Log the full response for debugging
                            errorList.push('An unknown error occurred.');
                        }

                        var errorHtml = '<div class="alert error my-2"><ul>';
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
@endsection
