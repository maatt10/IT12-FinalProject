@extends('layouts.app')

@section('title', 'Backup & Recovery')

@section('content')

<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-gray-800">
            Backup & Recovery
        </h1>

        <p class="mt-1 text-sm text-gray-600">
            Create and download database backups for the Lara's Flowershop system.
        </p>
    </div>

    {{-- Create Backup --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">

        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

            <div>
                <h2 class="text-lg font-semibold text-gray-800">
                    Database Backup
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    Create a complete backup of the current MySQL database.
                </p>
            </div>

            <form
                action="{{ route('backup.create') }}"
                method="POST"
            >
                @csrf

                <button
                    type="submit"
                    class="w-full md:w-auto px-5 py-2.5 rounded-lg bg-green-600 text-white font-medium hover:bg-green-700 transition"
                >
                    Create Database Backup
                </button>
            </form>

        </div>

    </div>

    {{-- Existing Backups --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">

        <div class="mb-4">
            <h2 class="text-lg font-semibold text-gray-800">
                Existing Backups
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Previously created database backup files are listed below.
            </p>
        </div>

        @if($backups->isEmpty())

            <div class="rounded-lg border border-dashed border-gray-300 p-8 text-center">
                <p class="text-sm text-gray-500">
                    No database backups have been created yet.
                </p>
            </div>

        @else

            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead>
                        <tr class="border-b border-gray-200 text-left">
                            <th class="px-4 py-3 font-semibold text-gray-700">
                                Filename
                            </th>

                            <th class="px-4 py-3 font-semibold text-gray-700">
                                Date & Time
                            </th>

                            <th class="px-4 py-3 font-semibold text-gray-700">
                                Size
                            </th>

                            <th class="px-4 py-3 font-semibold text-gray-700">
                                Action
                            </th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($backups as $backup)

                            <tr class="border-b border-gray-100">

                                <td class="px-4 py-3 text-gray-800">
                                    {{ $backup->getFilename() }}
                                </td>

                                <td class="px-4 py-3 text-gray-600">
                                    {{ date('M d, Y h:i A', $backup->getMTime()) }}
                                </td>

                                <td class="px-4 py-3 text-gray-600">
                                    {{ number_format($backup->getSize() / 1024, 2) }} KB
                                </td>

                                <td class="px-4 py-3">

                                    <a
                                        href="{{ route('backup.download', ['filename' => $backup->getFilename()]) }}"
                                        class="inline-flex items-center px-3 py-2 rounded-lg bg-gray-100 text-gray-700 font-medium hover:bg-gray-200 transition"
                                    >
                                        Download
                                    </a>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @endif

    </div>

    {{-- Recovery Instructions --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">

        <div class="mb-4">
            <h2 class="text-lg font-semibold text-gray-800">
                Recovery Instructions
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Database restoration is currently performed manually to prevent accidental replacement of the active database.
            </p>
        </div>

        <div class="space-y-4 text-sm text-gray-700">

            <div>
                <h3 class="font-semibold text-gray-800">
                    1. Stop the application
                </h3>

                <p class="mt-1">
                    Close the Laravel application or stop the development server before performing database restoration.
                </p>
            </div>

            <div>
                <h3 class="font-semibold text-gray-800">
                    2. Open phpMyAdmin
                </h3>

                <p class="mt-1">
                    Open phpMyAdmin through XAMPP and select the Lara's Flowershop database.
                </p>
            </div>

            <div>
                <h3 class="font-semibold text-gray-800">
                    3. Import the backup
                </h3>

                <p class="mt-1">
                    Use phpMyAdmin's Import function and select the downloaded
                    <strong>.sql</strong> backup file.
                </p>
            </div>

            <div>
                <h3 class="font-semibold text-gray-800">
                    4. Verify the database
                </h3>

                <p class="mt-1">
                    After importing the backup, verify that the database tables and records have been restored correctly before using the system again.
                </p>
            </div>

        </div>

    </div>

</div>

@endsection
