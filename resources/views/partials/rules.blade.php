<table class="listing-table">
    <thead>
    <tr>
        <th>ID</th>
        <th>Rule Title</th>
        <th>Description</th>
        <th>Rule On Product's Variant(s)</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    @forelse($rules as $rule)
        <tr id="ruleRow_{{ $rule['id'] }}">
            <td class="red">{{ $rule['id'] }}</td>
            <td>{{ $rule['title'] }}</td>
            <td>{{ @$rule['description']?: 'N/A' }}</td>
            <td>
                @foreach ($rule->variants as $ruleVariant)
                    @foreach ($products as $product)
                        @foreach ($product['variants'] as $variant)
                            @if ($ruleVariant->variant_id == $variant['id'])
                                {{ $product['title'] }}'s - {{ $variant['title'] }} - ${{ $variant['price'] }}<br>

                            @endif
                        @endforeach
                    @endforeach
                @endforeach
            </td>
            <td><span class="tag {{ $rule['is_enabled'] == true ? 'green' : 'orange' }}">{{ $rule['is_enabled'] == true ? 'Enabled' : 'Disabled' }}</span></td>
            <td>
{{--                <a href="{{ URL::tokenRoute('edit-rule', ['id' => $rule['id']]) }}">--}}
                <button class="secondary icon-edit" onclick="editRule({{ $rule['id'] }})"></button>
                <button class="secondary icon-trash" onclick="deleteRule({{ $rule['id'] }})"></button>
            </td>
        </tr>

    @empty
        <tr>
            <td>No Rules Available</td>
        </tr>
    @endforelse

    </tbody>
</table>

@if ($rules->hasPages())
    <nav>
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($rules->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="page-link icon-prev" aria-hidden="true"></span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link icon-prev" href="{{ $rules->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')"></a>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($rules->hasMorePages())
                <li class="page-item">
                    <a class="page-link icon-next" href="{{ $rules->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')"></a>
                        </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="page-link icon-next" aria-hidden="true"></span>
                </li>
            @endif
        </ul>
    </nav>
@endif



{{--{{ $rules->links('pagination::slider') }}--}}
