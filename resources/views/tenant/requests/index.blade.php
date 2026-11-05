@extends('layouts.app')

@section('title', 'Requests')
@section('eyebrow', 'Tenant services')

@section('content')
    <div class="page-heading">
        <div>
            <h2>Ask, request, arrange.</h2>
            <p>Renewals, pets, occupant changes or a general question — start it here and track the response.</p>
        </div>
        <a href="{{ route('tenant.requests.create') }}" class="button button-dark"><x-icon name="plus" :size="16" /> New request</a>
    </div>

    <div class="request-grid">
        @forelse ($requests as $request)
            <a href="{{ route('tenant.requests.show', $request) }}" class="request-card">
                <div class="request-card-top">
                    <span class="request-icon request-icon-{{ $request->type }}"><x-icon name="{{ match ($request->type) { 'renewal' => 'refresh', 'notice' => 'logout', 'details' => 'user', 'occupant' => 'users', 'pet' => 'star', 'concern' => 'alert', default => 'message' } }}" :size="20" /></span>
                    <x-badge :status="$request->status" :label="$request->status_label" />
                </div>
                <span class="request-type">{{ $request->type_label }}</span>
                <h3>{{ $request->subject }}</h3>
                <p>{{ Str::limit($request->details, 110) }}</p>
                <div class="request-card-foot">
                    <span>{{ $request->reference }} · {{ $request->created_at->format('j M Y') }}</span>
                    <span class="table-link">View <x-icon name="arrow-right" :size="13" /></span>
                </div>
                @if ($request->response)
                    <div class="request-response-preview">
                        <span class="response-label">Response</span>
                        <p>{{ Str::limit($request->response, 90) }}</p>
                    </div>
                @endif
            </a>
        @empty
            <div class="empty-state">
                <span class="empty-state-icon"><x-icon name="clipboard" :size="28" /></span>
                <h3>No requests yet</h3>
                <p>Need something from your property team? Start a request and we’ll keep the conversation in one place.</p>
                <a href="{{ route('tenant.requests.create') }}" class="button button-dark button-sm">Make a request</a>
            </div>
        @endforelse
    </div>
@endsection
