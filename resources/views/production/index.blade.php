@extends('layouts.app')

@section('title', 'Production')

@section('content')

<div class="space-y-6">

    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Production
            </h1>

            <p class="mt-1 text-sm text-gray-600">
                Record and review products made from their defined BOM components.
            </p>
        </div>

        <a
            href="{{ route('production.create') }}"
            class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-green-600 text-white font-medium hover:bg-green-700 transition"
        >
            Record Production
        </a>

    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">

        @if($productions->isEmpty())

            <div class="rounded-lg border border-dashed border-gray-300 p-8 text-center">
                <p class="text-sm text-gray-500">
                    No production records have been created yet.
                </p>
            </div>

        @else

            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead>
                        <tr class="border-b border-gray-200 text-left">

                            <th class="px-4 py-3 font-semibold text-gray-700">
                                Date & Time
                            </th>

                            <th class="px-4 py-3 font-semibold text-gray-700">
                                Product
                            </th>

                            <th class="px-4 py-3 font-semibold text-gray-700">
                                Quantity Produced
                            </th>

                            <th class="px-4 py-3 font-semibold text-gray-700">
                                Produced By
                            </th>

                        </tr>
                    </thead>

                    <tbody>

                        @foreach($productions as $production)

                            <tr class="border-b border-gray-100">

                                <td class="px-4 py-3 text-gray-600">
                                    {{ $production->production_date->format('M d, Y h:i A') }}
                                </td>

                                <td class="px-4 py-3 font-medium text-gray-800">
                                    {{ $production->product->display_name }}
                                </td>

                                <td class="px-4 py-3 text-gray-700">
                                    {{ number_format((float) $production->quantity_produced, 2) }}
                                    {{ $production->product->stock_unit }}
                                </td>

                                <td class="px-4 py-3 text-gray-600">
                                    {{ $production->producedBy->full_name }}
                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @endif

    </div>

</div>

@endsection