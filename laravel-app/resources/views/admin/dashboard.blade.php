@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Welcome Card -->
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-bold mb-4">Welcome, {{ auth()->user()->name }} 👋</h2>
            <p class="text-gray-600 mb-4">Use the sidebar to navigate to Ads Moderation or view Logs.</p>
            <div class="flex space-x-4">
                <a href="{{ route('admin.ads.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Go to Ads</a>
                <a href="{{ route('admin.logs') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">View Logs</a>
            </div>
        </div>

        <!-- Quick Stats (Placeholder) -->
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-bold mb-4">Admin Quick Tips</h2>
            <ul class="list-disc list-inside text-gray-600 space-y-2">
                <li>Only <strong>pending</strong> ads can be approved/rejected.</li>
                <li>Rejection requires a comment.</li>
                <li>All actions are logged securely.</li>
            </ul>
        </div>
    </div>
@endsection
