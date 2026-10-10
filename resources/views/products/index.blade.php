@extends('layouts.app')

@section('title', 'Inventory')

@php
    $hasFilter = fn($key) => in_array($key, $filters ?? []);
    $buildUrl = function ($newFilters) use ($itemType) {
        return route('products.index', array_filter([
            'item_type' => $itemType,
            'filters' => $newFilters,
        ]));
    };
    $toggleFilter = function ($key) use ($filters, $buildUrl) {
        $current = $filters ?? [];
        if (in_array($key, $current)) {
            $current = array_values(array_diff($current, [$key]));
        } else {
            if ($key === 'archived') {
                $current = ['archived'];
            } else {
                $current = array_values(array_diff($current, ['archived']));
                $current[] = $key;
            }
        }
        return $buildUrl($current);
    };
    $isOwner = auth()->user()->role === 'owner';

    $fmtQty = function ($val) {
        $formatted = number_format((float) $val, 2, '.', ',');
        $trimmed = rtrim(rtrim($formatted, '0'), '.');
        return $trimmed === '' ? '0' : $trimmed;
    };
@endphp

@section('content')

<div class="page-header">
    <div>
        <h1>Inventory</h1>
        <p>Manage products and materials used by the shop.</p>
    </div>
</div>

{{-- TABS --}}
<div class="item-tabs">
    <a href="{{ route('products.index', ['item_type' => 'product']) }}"
       class="item-tab {{ $itemType === 'product' ? 'active' : '' }}">
        Products
    </a>
    <a href="{{ route('products.index', ['item_type' => 'material']) }}"
       class="item-tab {{ $itemType === 'material' ? 'active' : '' }}">
        Materials
    </a>
</div>

{{-- SEARCH --}}
<div class="pos-search">
    <div class="pos-search-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
    </div>
    <input type="text" id="item-search" class="pos-search-input"
           placeholder="Search by name, variation, or reference code..."
           autocomplete="off">
</div>

{{-- ACTION BUTTONS --}}
<div class="item-actions">
    @if($itemType === 'product')
        <a href="{{ route('production.create') }}" class="action-primary">Make Product</a>
        <a href="{{ route('products.create', ['item_type' => 'product']) }}" class="action-secondary">+ Add Product</a>
    @else
        @if($isOwner)
            <a href="{{ route('purchases.create') }}" class="action-primary">Restock</a>
            <a href="{{ route('products.create', ['item_type' => 'material']) }}" class="action-secondary">+ Add Material</a>
        @endif
    @endif
</div>

{{-- FILTERS --}}
<div class="filter-chips">
    <span class="filter-label">Filters</span>

    <a href="{{ $buildUrl([]) }}" class="filter-chip {{ empty($filters) ? 'active' : '' }}">All</a>

    <a href="{{ $toggleFilter('low_stock') }}" class="filter-chip {{ $hasFilter('low_stock') ? 'active' : '' }}">
        Low Stock
    </a>

    <a href="{{ $toggleFilter('sellable') }}" class="filter-chip {{ $hasFilter('sellable') ? 'active' : '' }}">
        Sellable
    </a>

    <a href="{{ $toggleFilter('production') }}" class="filter-chip {{ $hasFilter('production') ? 'active' : '' }}">
        Production
    </a>

    @if($isOwner)
        <a href="{{ $toggleFilter('archived') }}" class="filter-chip archive {{ $hasFilter('archived') ? 'active' : '' }}">
            Archived
        </a>
    @endif
</div>

{{-- TABLE --}}
<div class="card">
    @if($items->count() > 0)
        <div style="overflow-x: auto;">
            <table id="items-table">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>{{ $itemType === 'material' ? 'Material Name' : 'Product Name' }}</th>
                        <th>Variation</th>
                        <th class="num">Retail Stock</th>
                        <th class="num">Production Stock</th>
                        <th class="num">Total</th>
                        <th>Unit</th>
                        <th class="num">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        <tr class="clickable-row" onclick="window.location='{{ route('products.show', $item) }}';">
                            <td style="font-family: 'SF Mono', Consolas, monospace; font-size: 12px; color: #64748B; font-weight: 600;">
                                {{ $item->reference_code }}
                            </td>

                            <td style="font-weight: 600; color: #212121;">
                                {{ $item->name }}
                                @if(!$item->is_active)
                                    <span class="archived-tag">Archived</span>
                                @endif
                            </td>

                            <td style="color: #64748B;">{{ $item->variation ?? '—' }}</td>

                            {{-- RETAIL STOCK --}}
                            <td class="num">
                                @if($item->retail_stock === null)
                                    <span style="color: #CBD5E1;">—</span>
                                @else
                                    <span class="stock-inline {{ $item->retail_low ? 'low' : 'ok' }}">
                                        {{ $fmtQty($item->retail_stock) }}
                                        @if($item->retail_low)
                                            <span class="low-tag">LOW</span>
                                        @endif
                                    </span>
                                @endif
                            </td>

                            {{-- PRODUCTION STOCK --}}
                            <td class="num">
                                @if($item->production_stock === null)
                                    <span style="color: #CBD5E1;">—</span>
                                @else
                                    <span class="stock-inline {{ $item->production_low ? 'low' : 'ok' }}">
                                        {{ $fmtQty($item->production_stock) }}
                                        @if($item->production_low)
                                            <span class="low-tag">LOW</span>
                                        @endif
                                    </span>
                                @endif
                            </td>

                            {{-- TOTAL --}}
                            <td class="num" style="font-weight: 700; color: #212121;">
                                {{ $fmtQty($item->total_stock) }}
                            </td>

                            <td style="color: #64748B;">{{ $item->stock_unit }}</td>

                            <td class="num" onclick="event.stopPropagation();">
                                <div class="action-buttons">
                                    @if($item->is_active)
                                        @if($item->inventory->isEmpty())
                                            <a href="{{ route('inventory.initial-stock', $item) }}" class="action-btn adjust">Set Stock</a>
                                        @else
                                            <a href="{{ route('inventory.adjustment', $item) }}" class="action-btn adjust">Adjust</a>
                                        @endif

                                        @if($isOwner)
                                            <form action="{{ route('products.destroy', $item) }}"
                                                  method="POST"
                                                  style="display: inline;"
                                                  onsubmit="return confirm('Archive this item?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-btn archive">Archive</button>
                                            </form>
                                        @endif
                                    @else
                                        @if($isOwner)
                                            <form action="{{ route('products.unarchive', $item) }}"
                                                  method="POST"
                                                  style="display: inline;"
                                                  onsubmit="return confirm('Restore this item?');">
                                                @csrf
                                                <button type="submit" class="action-btn unarchive">Unarchive</button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div id="search-empty" style="display: none; text-align: center; padding: 40px 20px; color: #94A3B8;">
                <p style="font-size: 14px;">No items match your search.</p>
            </div>
        </div>
    @else
        <div class="empty-state">
            @if($hasFilter('archived'))
                <h3>No archived items</h3>
                <p>Archived items will appear here.</p>
            @elseif(!empty($filters))
                <h3>No items match the current filters</h3>
                <p>Try removing some filters or switching tabs.</p>
                <a href="{{ route('products.index', ['item_type' => $itemType]) }}" class="action-secondary">Clear Filters</a>
            @else
                <h3>No items yet</h3>
                <p>Add a new {{ $itemType === 'material' ? 'material' : 'product' }} to get started.</p>
            @endif
        </div>
    @endif
</div>

<style>
    /* TABS */
    .item-tabs {
        display: flex;
        gap: 28px;
        margin-bottom: 20px;
        border-bottom: 1.5px solid #F0E6DD;
    }
    .item-tab {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 2px; text-decoration: none;
        font-size: 15px; font-weight: 600; color: #94A3B8;
        border-bottom: 2px solid transparent;
        margin-bottom: -1.5px; transition: all 0.15s ease;
    }
    .item-tab:hover { color: #6B5B95; }
    .item-tab.active {
        color: #6B5B95;
        border-bottom-color: #6B5B95;
    }

    /* SEARCH */
    .pos-search { position: relative; margin-bottom: 18px; }
    .pos-search-icon {
        position: absolute; left: 8px; top: 50%; transform: translateY(-50%);
        width: 38px; height: 38px; border-radius: 50%;
        background: #6B5B95; color: #FFFFFF;
        display: flex; align-items: center; justify-content: center;
        pointer-events: none;
    }
    .pos-search-input {
        width: 100%; height: 54px;
        padding: 0 20px 0 60px;
        border: 1.5px solid #F0E6DD; border-radius: 27px;
        font-size: 15px; background: #FFFFFF; color: #212121;
        transition: all 0.2s ease; font-family: inherit;
    }
    .pos-search-input:focus {
        outline: none; border-color: #6B5B95;
        box-shadow: 0 0 0 4px rgba(107, 91, 149, 0.1);
    }
    .pos-search-input::placeholder { color: #B0A99F; }

    /* =========================================
       ACTION BUTTONS — hard-locked identical
       ========================================= */
    .item-actions {
        display: flex;
        gap: 10px;
        margin-bottom: 18px;
        flex-wrap: wrap;
        align-items: center;
    }

    .action-primary,
    .action-secondary {
        /* Reset everything */
        all: unset;

        /* Force identical box model */
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;

        /* Force identical dimensions */
        height: 42px;
        min-height: 42px;
        min-width: 140px;
        padding: 0 20px;

        /* Force identical typography */
        font-family: inherit;
        font-size: 14px;
        font-weight: 600;
        line-height: 1;
        white-space: nowrap;
        text-decoration: none;

        /* Force identical shape */
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .action-primary {
        background: #6B5B95;
        color: #FFFFFF;
        border: none;
        box-shadow: 0 2px 6px rgba(107, 91, 149, 0.3);
    }
    .action-primary:hover {
        background: #594B7D;
        box-shadow: 0 4px 10px rgba(107, 91, 149, 0.4);
    }

    .action-secondary {
        background: #FFFFFF;
        color: #212121;
        border: 1.5px solid #F0E6DD;
    }
    .action-secondary:hover {
        background: #FEFCF9;
        border-color: #6B5B95;
        color: #6B5B95;
    }

    /* FILTER CHIPS */
    .filter-chips {
        display: flex; gap: 8px; margin-bottom: 20px;
        flex-wrap: wrap; align-items: center;
    }
    .filter-label {
        font-size: 11px; color: #94A3B8; font-weight: 700;
        text-transform: uppercase; letter-spacing: 1.5px; margin-right: 6px;
    }
    .filter-chip {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 14px; border-radius: 20px;
        text-decoration: none; font-size: 12px; font-weight: 600;
        color: #64748B; background: #FFFFFF;
        border: 1.5px solid #F0E6DD;
        transition: all 0.15s ease;
    }
    .filter-chip:hover {
        border-color: #D5C9E8; background: #FEFCF9; color: #6B5B95;
    }
    .filter-chip.active {
        background: #6B5B95; color: #FFFFFF; border-color: #6B5B95;
    }
    .filter-chip.archive.active {
        background: #A16207; border-color: #A16207;
    }

    /* ROWS */
    .clickable-row { cursor: pointer; transition: background 0.15s ease; }
    .clickable-row:hover { background: #FDFBFF; }

    table td.num, table th.num {
        text-align: right;
        white-space: nowrap;
    }

    /* STOCK CELL */
    .stock-inline {
        display: inline-flex;
        align-items: center;
        justify-content: flex-end;
        gap: 6px;
        font-variant-numeric: tabular-nums;
        font-weight: 600;
        white-space: nowrap;
    }
    .stock-inline.ok { color: #2E5A3B; }
    .stock-inline.low { color: #DC3545; }

    .low-tag {
        display: inline-block;
        padding: 1px 6px;
        font-size: 9px;
        font-weight: 700;
        background: #FDECEA;
        color: #DC3545;
        border-radius: 3px;
        letter-spacing: 0.5px;
        line-height: 1.4;
    }

    .archived-tag {
        display: inline-block; margin-left: 6px;
        padding: 2px 8px; border-radius: 4px;
        background: #F1F5F9; color: #64748B;
        font-size: 10px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.5px;
    }

    /* ACTION BUTTONS (in table) */
    .action-buttons { display: flex; gap: 6px; justify-content: flex-end; }
    .action-btn {
        display: inline-block; padding: 5px 12px;
        border-radius: 6px; font-size: 12px; font-weight: 600;
        text-decoration: none; cursor: pointer; border: none;
        transition: all 0.15s ease; white-space: nowrap; font-family: inherit;
    }
    .action-btn.adjust { background: #EEF5F0; color: #2E5A3B; }
    .action-btn.adjust:hover { background: #D8E9DF; color: #1E3D28; }
    .action-btn.archive { background: #FFF4E5; color: #A16207; }
    .action-btn.archive:hover { background: #FDE9C7; color: #854D0E; }
    .action-btn.unarchive { background: #E8F5E9; color: #2E5A3B; }
    .action-btn.unarchive:hover { background: #D4EBD6; color: #1E3D28; }

    /* EMPTY */
    .empty-state {
        text-align: center; padding: 50px 20px; color: #64748B;
    }
    .empty-state h3 {
        font-size: 18px; color: #212121; margin-bottom: 6px; font-weight: 700;
    }
    .empty-state p { font-size: 14px; margin-bottom: 20px; }

    @media (max-width: 640px) {
        .item-tabs { gap: 20px; }
        .action-primary,
        .action-secondary {
            flex: 1;
            min-width: 0;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('item-search');
        const table = document.getElementById('items-table');
        const emptyState = document.getElementById('search-empty');
        if (!searchInput || !table) return;

        const rows = table.querySelectorAll('.clickable-row');

        searchInput.addEventListener('input', function () {
            const term = this.value.toLowerCase().trim();
            let visible = 0;

            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                const matches = term === '' || text.includes(term);

                row.style.display = matches ? '' : 'none';
                if (matches) visible++;
            });

            if (emptyState) {
                emptyState.style.display = (visible === 0 && term !== '') ? 'block' : 'none';
            }
        });
    });
</script>

@endsection