@extends('shopify-app::layouts.default')

@section('content')
    <section class="full-width">
        <div id="main" class="full-width">
            <section class="row"></section><div></div><section class="row"></section>

            <article>
                <div class="columns ten">
                    <h5>{{ strtoupper(config('shopify-app.app_name')) }}</h5>
                </div>
                <div class="columns two align-right">
                    <a href="{{ URL::tokenRoute('create-rule') }}" class="button">Add Rule</a>
                </div>

            </article>

            <section class="row"></section><div></div><section class="row"></section>

            <article>
                <div class="row full-width">
                    <div class="columns twelve">
                        <div class="card has-sections">
                            <div class="card-section">
                                <div class="card-section">
                                    <div class="sessionMessages"></div>
                                    <div class="row">
                                        <div class="columns twelve">
                                            <div class="">
                                                <input type="search" placeholder="Search ID, Rule Title, Status" id="search"/>
                                            </div>
                                            <div class="row"></div> <div class="row"></div>
                                            <div id="rulesPage">
                                                @include('partials.rules')
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </section>
    @include('partials.deleteConfirmationModal')

<script>


    // Home Route
    function home() {
        location.reload();
    }

    // Edit the Rule
    function editRule(ruleId) {
        $.ajax({
            url: "/edit-rule/" + ruleId,
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                success: function (response) {
                    $('#main').empty().html(response);
                },
                error: function (xhr) {
                    var errorList = [];
                    if (xhr.responseJSON && Array.isArray(xhr.responseJSON.errors)) {
                        errorList = xhr.responseJSON.errors;
                    } else if (xhr.responseJSON && typeof xhr.responseJSON.errors === 'string') {
                        errorList.push(xhr.responseJSON.errors);
                    } else {
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
    }

    // Delete the Rule
    function deleteRule(ruleId) {
        $('#deleteConfirmationModal').css('display', 'block');
        $('#confirmDeleteBtn').on('click', function () {
            $.ajax({
                url: '/delete-rule/' + ruleId,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                success: function (response) {
                    if (response.success) {
                        $('#ruleRow_' + ruleId).hide();
                        var successMessage = '<div class="alert success"><a class="close" href=""></a><dl><dt>' + response.success + '</dt></dl></div>';
                        $('.sessionMessages').html(successMessage);
                    }
                },
                error: function (xhr) {
                    var errorList = [];
                    if (xhr.responseJSON && Array.isArray(xhr.responseJSON.errors)) {
                        errorList = xhr.responseJSON.errors;
                    } else if (xhr.responseJSON && typeof xhr.responseJSON.errors === 'string') {
                        errorList.push(xhr.responseJSON.errors);
                    } else {
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
            $('#deleteConfirmationModal').css('display', 'none');
        });
    }

    // Search Logic
    $(document).on('input', '#search', function () {
        search(1);
    });

    // Pagination Logic
    $(document).on('click', '.pagination a', function (event) {
        event.preventDefault();
        let page = $(this).attr('href').split('page=')[1];
        search(page);
    });

    function search(page) {
        const query = $('#search').val();
        const url = '/search';

        $.ajax({
            url: url,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: { search: query, page: page },
            success: function (response) {
                $('#rulesPage').empty().html(response);
            },
            error: function (error) {
                //
            }
        });
    }

    $(document).on('click', '.close', function () {
        $('#deleteConfirmationModal').css('display', 'none');
    });

</script>

@endsection

