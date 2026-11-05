@extends('layouts.app')

@section('title', 'New announcement')
@section('eyebrow', 'Communications')

@section('content')
    <div class="page-heading">
        <div>
            <a href="{{ route('admin.announcements.index') }}" class="back-link"><x-icon name="arrow-left" :size="15" /> Back to announcements</a>
            <h2>Publish a notice.</h2>
            <p>It will appear in the resident portal immediately.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.announcements.store') }}" class="form-layout">
        @csrf
        <div class="stack">
            <section class="panel">
                <div class="panel-head"><div><span class="eyebrow">Content</span><h3>What do residents need to know?</h3></div></div>

                <div class="field">
                    <label for="title">Title</label>
                    <input id="title" name="title" type="text" value="{{ old('title') }}" required placeholder="Lift maintenance at Willow Court">
                    @error('title')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                <div class="field">
                    <label for="body">Message</label>
                    <textarea id="body" name="body" rows="8" required placeholder="Write the notice in plain language. Include dates, times and what residents need to do.">{{ old('body') }}</textarea>
                    @error('body')<span class="field-error">{{ $message }}</span>@enderror
                </div>
            </section>

            <section class="panel">
                <div class="panel-head"><div><span class="eyebrow">Audience</span><h3>Who should see it?</h3></div></div>

                <div class="form-grid-2">
                    <div class="field">
                        <label for="property_id">Property</label>
                        <select id="property_id" name="property_id">
                            <option value="">All buildings</option>
                            @foreach ($properties as $property)
                                <option value="{{ $property->id }}" @selected(old('property_id') == $property->id)>{{ $property->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="audience">Audience</label>
                        <select id="audience" name="audience" required>
                            <option value="tenants" @selected(old('audience', 'tenants') === 'tenants')>Tenants</option>
                            <option value="landlords" @selected(old('audience') === 'landlords')>Landlords</option>
                            <option value="all" @selected(old('audience') === 'all')>Everyone</option>
                        </select>
                    </div>
                </div>

                <label class="checkbox-row">
                    <input type="checkbox" name="pinned" value="1" @checked(old('pinned') == 1)>
                    <span>Pin to the top of the noticeboard</span>
                </label>
            </section>
        </div>

        <aside class="stack">
            <button type="submit" class="button button-dark button-block">Publish announcement <x-icon name="send" :size="16" /></button>
            <a href="{{ route('admin.announcements.index') }}" class="button button-ghost button-block">Cancel</a>
            <section class="panel panel-tinted">
                <span class="support-icon"><x-icon name="bell" :size="20" /></span>
                <h3>Keep it useful</h3>
                <p>Lead with what residents need to do, give clear dates and times, and avoid jargon. Short notices get read.</p>
            </section>
        </aside>
    </form>
@endsection
