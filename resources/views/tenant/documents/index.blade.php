@extends('layouts.app')

@section('title', 'Documents')
@section('eyebrow', 'Your document library')

@section('content')
    <div class="page-heading">
        <div>
            <h2>Your paperwork, in one place.</h2>
            <p>Tenancy agreements, safety certificates, statements and receipts for {{ $tenancy->property->name }}.</p>
        </div>
        <span class="panel-ref">{{ $documents->count() }} files</span>
    </div>

    <div class="filter-tabs">
        <a href="{{ route('tenant.documents.index') }}" class="{{ request('category') ? '' : 'is-active' }}">All documents <span>{{ $documents->count() }}</span></a>
        @foreach ($categories as $value => $label)
            <a href="{{ route('tenant.documents.index', ['category' => $value]) }}" class="{{ request('category') === $value ? 'is-active' : '' }}">{{ $label }}</a>
        @endforeach
    </div>

    @if ($documents->isEmpty())
        <div class="empty-state">
            <span class="empty-state-icon"><x-icon name="folder" :size="28" /></span>
            <h3>No documents in this category</h3>
            <p>Try another filter, or ask your property manager if you were expecting something.</p>
        </div>
    @else
        <div class="document-grid">
            @foreach ($documents as $document)
                <article class="document-card">
                    <div class="document-card-top">
                        <span class="document-icon document-icon-{{ $document->category }}"><x-icon name="file" :size="22" /></span>
                        <x-badge status="neutral" :label="$document->category_label" />
                    </div>
                    <h3>{{ $document->title }}</h3>
                    <p>Added {{ $document->created_at->format('j F Y') }} · Version {{ $document->version }}</p>
                    <div class="document-card-meta">
                        <span>PDF · {{ $document->human_size }}</span>
                        <span>{{ $document->tenancy ? 'Tenancy document' : 'Property document' }}</span>
                    </div>
                    <div class="document-card-actions">
                        <a href="{{ route('tenant.documents.download', $document) }}" class="button button-ghost button-sm"><x-icon name="download" :size="15" /> Download</a>
                        <a href="{{ route('tenant.documents.download', $document) }}" target="_blank" class="icon-btn" aria-label="Open document"><x-icon name="eye" :size="17" /></a>
                    </div>
                </article>
            @endforeach
        </div>
    @endif

    <div class="info-strip">
        <span class="info-strip-icon"><x-icon name="shield" :size="19" /></span>
        <div>
            <strong>Your documents are private</strong>
            <p>Only you, your landlord and your property team can access these files. Downloads are logged for security.</p>
        </div>
    </div>
@endsection
