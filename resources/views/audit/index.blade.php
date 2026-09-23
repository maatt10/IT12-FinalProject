@extends('layouts.app')

@section('title', 'Audit Trail')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">
        Audit Trail
    </h1>

    <p class="text-sm text-gray-600 mt-1">
        Review important changes made to the system.
    </p>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">

    @if($logs->isEmpty())

        <div class="p-6 text-center text-gray-500">
            No audit records found.
        </div>

    @else

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">

                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left">
                            Date / Time
                        </th>

                        <th class="px-4 py-3 text-left">
                            User
                        </th>

                        <th class="px-4 py-3 text-left">
                            Action
                        </th>

                        <th class="px-4 py-3 text-left">
                            Table
                        </th>

                        <th class="px-4 py-3 text-left">
                            Record
                        </th>

                        <th class="px-4 py-3 text-left">
                            Details
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y">

                    @foreach($logs as $log)

                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap">
                                {{ $log->action_timestamp?->format('M d, Y h:i A') }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $log->user?->full_name ?? 'Unknown' }}
                            </td>

                            <td class="px-4 py-3">
                                {{ ucfirst($log->action_type) }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $log->table_affected }}
                            </td>

                            <td class="px-4 py-3">
                                #{{ $log->record_id }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $log->details ?? '—' }}
                            </td>
                        </tr>

                    @endforeach

                </tbody>

            </table>
        </div>

    @endif

</div>

@endsection