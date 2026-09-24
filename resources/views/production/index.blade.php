@extends('layouts.app')

@section('title', 'Production')

@section('content')

<div class="page-header">
    <div>
        <h1>Production</h1>
        <p>Record and review products made from their defined BOM components.</p>
    </div>

    <a href="{{ route('production.create') }}" class="btn btn-primary">
        + Record Production
    </a>
</div>

<div class="card">

    @if($productions->isEmpty())

        <div class="empty-state">
            <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
            </svg>
            <h3>No production records yet</h3>
            <p>Start by recording your first production run.</p>
            <a href="{{ route('production.create') }}" class="btn btn-primary">
                + Record First Production
            </a>
        </div>

    @else

        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Date &amp; Time</th>
                        <th>Product</th>
                        <th style="text-align: right;">Quantity Produced</th>
                        <th>Produced By</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($productions as $production)
                        <tr>
                            <td style="color: #64748B; white-space: nowrap;">
                                {{ $production->production_date->format('M d, Y h:i A') }}
                            </td>

                            <td style="font-weight: 600; color: #212121;">
                                {{ $production->product->display_name }}
                            </td>

                            <td style="text-align: right; font-weight: 600; color: #2E5A3B;">
                                {{ (float) $production->quantity_produced }} {{ $production->product->stock_unit }}
                            </td>

                            <td style="color: #64748B;">
                                {{ $production->producedBy->full_name }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    @endif

</div>

<style>
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #64748B;
    }
    .empty-state svg {
        color: #D4AF37;
        margin-bottom: 16px;
        opacity: 0.6;
    }
    .empty-state h3 {
        font-size: 18px;
        color: #212121;
        margin-bottom: 6px;
        font-weight: 700;
    }
    .empty-state p {
        font-size: 14px;
        margin-bottom: 20px;
    }
</style>

@endsection