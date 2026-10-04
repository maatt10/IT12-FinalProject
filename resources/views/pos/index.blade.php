@extends('layouts.app')

@section('title', 'POS')

@section('content')

<div class="page-header">
    <div>
        <h1>POS</h1>
        <p>Select the type of transaction to record.</p>
    </div>
</div>

<div class="pos-hub">

    <a href="{{ route('sales.create') }}" class="pos-hub-card">
        <div class="pos-hub-icon" style="background: #FCE4EC; color: #E85D75;">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        </div>
        <div>
            <h3>Walk-in Sale</h3>
            <p>Record a purchase made in the shop.</p>
        </div>
        <div class="pos-hub-arrow">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </div>
    </a>

    <a href="{{ route('orders.create') }}" class="pos-hub-card">
        <div class="pos-hub-icon" style="background: #E8F5E9; color: #2E5A3B;">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        </div>
        <div>
            <h3>Online Order</h3>
            <p>Record a bouquet order received through Messenger.</p>
        </div>
        <div class="pos-hub-arrow">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </div>
    </a>

</div>

<style>
    .pos-hub {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 18px;
    }

    .pos-hub-card {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 24px;
        background: #FFFFFF;
        border: 1.5px solid #F0E6DD;
        border-radius: 14px;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .pos-hub-card:hover {
        border-color: #E85D75;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(232, 93, 117, 0.12);
    }

    .pos-hub-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .pos-hub-card h3 {
        font-size: 17px;
        font-weight: 700;
        color: #212121;
        margin-bottom: 4px;
    }

    .pos-hub-card p {
        font-size: 13px;
        color: #64748B;
        line-height: 1.4;
    }

    .pos-hub-arrow {
        color: #94A3B8;
        margin-left: auto;
        flex-shrink: 0;
    }

    .pos-hub-card:hover .pos-hub-arrow {
        color: #E85D75;
    }
</style>

@endsection