@extends('layouts.app')

@section('title', 'Edit property')
@section('eyebrow', $property->reference)

@section('content')
    <div class="page-heading">
        <div>
            <a href="{{ route('admin.properties.show', $property) }}" class="back-link"><x-icon name="arrow-left" :size="15" /> Back to property</a>
            <h2>Edit {{ $property->name }}.</h2>
            <p>Update the property record, ownership and presentation.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.properties.update', $property) }}" class="form-layout">
        @csrf
        @method('PATCH')

        <div class="stack">
            <section class="panel">
                <div class="panel-head">
                    <div><span class="eyebrow">The basics</span><h3>Property details</h3></div>
                </div>

                <div class="form-grid-2">
                    <div class="field">
                        <label for="name">Property name</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $property->name) }}" required>
                        @error('name')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="field">
                        <label for="reference">Internal reference</label>
                        <input id="reference" name="reference" type="text" value="{{ old('reference', $property->reference) }}" required>
                        @error('reference')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="field">
                        <label for="type">Property type</label>
                        <select id="type" name="type" required>
                            @foreach (['apartment' => 'Apartment', 'house' => 'House', 'studio' => 'Studio', 'maisonette' => 'Maisonette', 'commercial' => 'Commercial'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('type', $property->type) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            @foreach (['available' => 'Available', 'occupied' => 'Occupied', 'maintenance' => 'Under maintenance', 'off_market' => 'Off market'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', $property->status) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="field">
                    <label for="address_line_1">Address line 1</label>
                    <input id="address_line_1" name="address_line_1" type="text" value="{{ old('address_line_1', $property->address_line_1) }}" required>
                </div>
                <div class="field">
                    <label for="address_line_2">Address line 2</label>
                    <input id="address_line_2" name="address_line_2" type="text" value="{{ old('address_line_2', $property->address_line_2) }}">
                </div>

                <div class="form-grid-2">
                    <div class="field">
                        <label for="city">City</label>
                        <input id="city" name="city" type="text" value="{{ old('city', $property->city) }}" required>
                    </div>
                    <div class="field">
                        <label for="postcode">Postcode</label>
                        <input id="postcode" name="postcode" type="text" value="{{ old('postcode', $property->postcode) }}" required>
                    </div>
                </div>

                <div class="field">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4">{{ old('description', $property->description) }}</textarea>
                </div>
            </section>

            <section class="panel">
                <div class="panel-head">
                    <div><span class="eyebrow">Spaces</span><h3>Size &amp; facilities</h3></div>
                </div>

                <div class="form-grid-3">
                    <div class="field">
                        <label for="bedrooms">Bedrooms</label>
                        <input id="bedrooms" name="bedrooms" type="number" min="0" max="20" value="{{ old('bedrooms', $property->bedrooms) }}" required>
                    </div>
                    <div class="field">
                        <label for="bathrooms">Bathrooms</label>
                        <input id="bathrooms" name="bathrooms" type="number" min="0" max="20" value="{{ old('bathrooms', $property->bathrooms) }}" required>
                    </div>
                    <div class="field">
                        <label for="size_sqft">Size (sq ft)</label>
                        <input id="size_sqft" name="size_sqft" type="number" min="0" value="{{ old('size_sqft', $property->size_sqft) }}">
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="field">
                        <label for="landlord_id">Landlord</label>
                        <select id="landlord_id" name="landlord_id">
                            <option value="">Unassigned</option>
                            @foreach ($landlords as $landlord)
                                <option value="{{ $landlord->id }}" @selected(old('landlord_id', $property->landlord_id) == $landlord->id)>{{ $landlord->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="manager_id">Property manager</label>
                        <select id="manager_id" name="manager_id">
                            <option value="">Unassigned</option>
                            @foreach ($managers as $manager)
                                <option value="{{ $manager->id }}" @selected(old('manager_id', $property->manager_id) == $manager->id)>{{ $manager->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="field">
                    <label for="accent">Card colour</label>
                    <select id="accent" name="accent">
                        @foreach (['forest' => 'Forest green', 'clay' => 'Terracotta', 'ocean' => 'Ocean blue', 'sand' => 'Sand', 'plum' => 'Plum'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('accent', $property->accent) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </section>
        </div>

        <aside class="stack">
            <button type="submit" class="button button-dark button-block">Save changes <x-icon name="check" :size="16" /></button>
            <a href="{{ route('admin.properties.show', $property) }}" class="button button-ghost button-block">Cancel</a>
            <section class="panel panel-tinted">
                <span class="support-icon"><x-icon name="key" :size="20" /></span>
                <h3>{{ $property->units->count() }} units</h3>
                <p>Unit level details such as rent and occupancy are managed from the property overview.</p>
            </section>
        </aside>
    </form>
@endsection
