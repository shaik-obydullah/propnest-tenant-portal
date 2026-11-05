@extends('layouts.app')

@section('title', 'Report an issue')
@section('eyebrow', 'Maintenance')

@section('content')
    <div class="page-heading">
        <div>
            <a href="{{ route('tenant.maintenance.index') }}" class="back-link"><x-icon name="arrow-left" :size="15" /> Back to maintenance</a>
            <h2>Tell us what needs fixing.</h2>
            <p>{{ $tenancy->property->name }}, Unit {{ $tenancy->unit->unit_number }} · a little detail now helps us fix it faster.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('tenant.maintenance.store') }}" enctype="multipart/form-data" class="form-layout">
        @csrf

        <div class="stack">
            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">The issue</span>
                        <h3>What’s going on?</h3>
                    </div>
                </div>

                <div class="field">
                    <label for="title">Short title</label>
                    <input id="title" name="title" type="text" value="{{ old('title') }}" maxlength="120" required placeholder="e.g. Kitchen tap is dripping">
                    @error('title')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-grid-2">
                    <div class="field">
                        <label for="category">Category</label>
                        <select id="category" name="category" required>
                            <option value="">Choose a category</option>
                            @foreach (config('portal.maintenance.categories') as $value => $label)
                                <option value="{{ $value }}" @selected(old('category') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('category')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="field">
                        <label for="priority">How urgent is it?</label>
                        <select id="priority" name="priority" required>
                            @foreach (config('portal.maintenance.priorities') as $value => $label)
                                <option value="{{ $value }}" @selected(old('priority', 'normal') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('priority')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="field">
                    <label for="description">Describe the problem</label>
                    <textarea id="description" name="description" rows="6" required placeholder="When did it start? Where is it? Anything we should know before a visit?">{{ old('description') }}</textarea>
                    <span class="field-hint">Please include anything that helps our engineer — noises, leaks, warning lights, access notes.</span>
                    @error('description')<span class="field-error">{{ $message }}</span>@enderror
                </div>
            </section>

            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Optional</span>
                        <h3>Add a photo</h3>
                    </div>
                </div>

                <label class="upload-zone" for="photo">
                    <span class="upload-icon"><x-icon name="paperclip" :size="22" /></span>
                    <strong>Drop a photo here or browse</strong>
                    <span>JPG, PNG or WebP · up to 5 MB</span>
                    <span class="upload-filename" data-upload-name></span>
                    <input id="photo" name="photo" type="file" accept="image/*" data-upload-input>
                </label>
                @error('photo')<span class="field-error">{{ $message }}</span>@enderror
            </section>
        </div>

        <aside class="stack">
            <section class="panel panel-tinted">
                <span class="support-icon"><x-icon name="clock" :size="20" /></span>
                <h3>What happens next?</h3>
                <ol class="mini-steps">
                    <li><span>1</span>We log your request and send a reference.</li>
                    <li><span>2</span>The property team triages it and books a visit.</li>
                    <li><span>3</span>You get updates and can message us any time.</li>
                </ol>
            </section>

            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Your home</span>
                        <h3>Visit address</h3>
                    </div>
                </div>
                <div class="detail-list">
                    <div><span>Property</span><strong>{{ $tenancy->property->name }}</strong></div>
                    <div><span>Unit</span><strong>{{ $tenancy->unit->unit_number }}</strong></div>
                    <div><span>Address</span><strong>{{ $tenancy->property->address }}</strong></div>
                    <div><span>Tenancy</span><strong>{{ $tenancy->reference }}</strong></div>
                </div>
            </section>

            <button type="submit" class="button button-dark button-block">Submit maintenance request <x-icon name="arrow-right" :size="16" /></button>
            <p class="form-note">You’ll receive a reference and can track progress from your dashboard.</p>
        </aside>
    </form>
@endsection
