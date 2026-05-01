{{--
  Reusable search bar for data tables.
  Props: id (input id), clearId (clear button id), name (input name, default 'search'),
         placeholder, value (current search value), wrapClass (optional)
--}}
@props([
    'id' => 'table-search',
    'clearId' => 'search-clear',
    'searchBtnId' => null,
    'name' => 'search',
    'placeholder' => 'Search...',
    'value' => '',
    'wrapClass' => '',
])
<div class="action-bar__search-wrap {{ $wrapClass }}" data-search-wrap>
    <div class="action-bar__search">
        <button
            type="button"
            @if($searchBtnId) id="{{ $searchBtnId }}" @endif
            class="action-bar__search-icon-btn"
            aria-label="Search"
        >
            <i data-lucide="search" class="lucide-icon"></i>
        </button>
        <input
            type="text"
            id="{{ $id }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            class="action-bar__search-input"
            placeholder="{{ $placeholder }}"
            autocomplete="off"
            aria-label="{{ $placeholder }}"
        />
        <span class="action-bar__search-loading hidden" data-search-loading aria-hidden="true">
            <i data-lucide="loader-2" class="lucide-icon spin"></i>
        </span>
        <button type="button" id="{{ $clearId }}" class="action-bar__search-clear hidden" data-search-clear aria-label="Clear search">
            <i data-lucide="x" class="lucide-icon"></i>
        </button>
    </div>
</div>
