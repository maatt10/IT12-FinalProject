@extends('layouts.app')

@section('title', 'Reports')

@section('content')

<div class="page-header">
    <div>
        <h1>Reports</h1>
        <p>View summarized reports for the shop.</p>
    </div>
</div>

{{-- TABS --}}
<div class="report-tabs">

    <a href="{{ route('reports.sales') }}" class="report-tab active">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
        </svg>
        Sales Report
    </a>

    {{-- Future tabs can be added here --}}

</div>

<div class="card" style="margin-top: 20px;">
    <p style="color: #64748B; text-align: center; padding: 30px 20px;">
        Select a report type above to view it.
    </p>
</div>

<style>
    .report-tabs {
        display: flex;
        gap: 6px;
        background: #FFFFFF;
        padding: 6px;
        border-radius: 12px;
        border: 1px solid #F0E6DD;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        width: fit-content;
    }

    .report-tab {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        color: #64748B;
        transition: all 0.15s ease;
    }

    .report-tab:hover {
        background: #FEFCF9;
        color: #E85D75;
    }

    .report-tab.active {
        background: #E85D75;
        color: #FFFFFF;
        box-shadow: 0 2px 8px rgba(232, 93, 117, 0.3);
    }
</style>

@endsection