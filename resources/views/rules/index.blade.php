@extends('shopify-app::layouts.default')

@section('content')
    <main class="container" id="main">
        <div class="navigationTop">
            <strong>{{ strtoupper(config('shopify-app.app_name')) }}</strong>
            <a href="{{ URL::tokenRoute('create-rule') }}" class="button align-right">Add Rule</a>
        </div>

        <section class="full-width table-section">
            <div class="card listing-card">

                <div class="sessionMessages"></div>

                <label>
                    <input type="search" placeholder="Search ID, Rule Title, Status" id="search"/>
                </label>
                <div id="rulesPage">
                    @include('partials.rules')
                </div>
            </div>
        </section>

    </main>

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
        if (confirm('Are you sure you want to delete this Rule?')) {
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
        }
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
</script>

@endsection

