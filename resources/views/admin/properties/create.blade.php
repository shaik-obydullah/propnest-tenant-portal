@extends('layouts.app')

@section('title', 'Add property')
@section('eyebrow', 'Portfolio')

@section('content')
    <div class="page-heading">
        <div>
            <a href="{{ route('admin.properties.index') }}" class="back-link"><x-icon name="arrow-left" :size="15" /> Back to properties</a>
            <h2>Add a property.</h2>
            <p>Set up the building and its first lettable unit.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.properties.store') }}" class="form-layout">
        @csrf
        <div class="stack">
            <section class="panel">
                <div class="panel-head">
                    <div><span class="eyebrow">The basics</span><h3>Property details</h3></div>
                </div>

                <div class="form-grid-2">
                    <div class="field">
                        <label for="name">Property name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required placeholder="14 Willow Court">
                        @error('name')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="field">
                        <label for="reference">Internal reference</label>
                        <input id="reference" name="reference" type="text" value="{{ old('reference') }}" required placeholder="PN-WILLOW-14">
                        @error('reference')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="field">
                        <label for="type">Property type</label>
                        <select id="type" name="type" required>
                            @foreach (['apartment' => 'Apartment', 'house' => 'House', 'studio' => 'Studio', 'maisonette' => 'Maisonette', 'commercial' => 'Commercial'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('type') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            @foreach (['available' => 'Available', 'occupied' => 'Occupied', 'maintenance' => 'Under maintenance', 'off_market' => 'Off market'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('status') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="field">
                    <label for="address_line_1">Address line 1</label>
                    <input id="address_line_1" name="address_line_1" type="text" value="{{ old('address_line_1') }}" required placeholder="14 Willow Court">
                    @error('address_line_1')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                <div class="field">
                    <label for="address_line_2">Address line 2 <span class="label-optional">Optional</span></label>
                    <input id="address_line_2" name="address_line_2" type="text" value="{{ old('address_line_2') }}" placeholder="Wharf Road">
                </div>

                <div class="form-grid-2">
                    <div class="field">
                        <label for="city">City</label>
                        <input id="city" name="city" type="text" value="{{ old('city', 'London') }}" required>
                        @error('city')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="field">
                        <label for="postcode">Postcode</label>
                        <input id="postcode" name="postcode" type="text" value="{{ old('postcode') }}" required placeholder="N1 7UX">
                        @error('postcode')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="field">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4" placeholder="A short, resident-friendly description of the property.">{{ old('description') }}</textarea>
                </div>
            </section>

            <section class="panel">
                <div class="panel-head">
                    <div><span class="eyebrow">Spaces</span><h3>Size &amp; facilities</h3></div>
                </div>

                <div class="form-grid-3">
                    <div class="field">
                        <label for="bedrooms">Bedrooms</label>
                        <input id="bedrooms" name="bedrooms" type="number" min="0" max="20" value="{{ old('bedrooms', 1) }}" required>
                    </div>
                    <div class="field">
                        <label for="bathrooms">Bathrooms</label>
                        <input id="bathrooms" name="bathrooms" type="number" min="0" max="20" value="{{ old('bathrooms', 1) }}" required>
                    </div>
                    <div class="field">
                        <label for="size_sqft">Size (sq ft) <span class="label-optional">Optional</span></label>
                        <input id="size_sqft" name="size_sqft" type="number" min="0" value="{{ old('size_sqft') }}">
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="field">
                        <label for="landlord_id">Landlord</label>
                        <select id="landlord_id" name="landlord_id">
                            <option value="">Unassigned</option>
                            @foreach ($landlords as $landlord)
                                <option value="{{ $landlord->id }}" @selected(old('landlord_id') == $landlord->id)>{{ $landlord->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="manager_id">Property manager</label>
                        <select id="manager_id" name="manager_id">
                            <option value="">Unassigned</option>
                            @foreach ($managers as $manager)
                                <option value="{{ $manager->id }}" @selected(old('manager_id') == $manager->id)>{{ $manager->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="field">
                    <label for="accent">Card colour</label>
                    <select id="accent" name="accent">
                        @foreach (['forest' => 'Forest green', 'clay' => 'Terracotta', 'ocean' => 'Ocean blue', 'sand' => 'Sand', 'plum' => 'Plum'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('accent') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </section>

            <section class="panel">
                <div class="panel-head">
                    <div><span class="eyebrow">First unit</span><h3>Lettable unit</h3></div>
                </div>

                <div class="form-grid-2">
                    <div class="field">
                        <label for="unit_number">Unit number</label>
                        <input id="unit_number" name="unit_number" type="text" value="{{ old('unit_number') }}" required placeholder="4B">
                        @error('unit_number')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="field">
                        <label for="rent_amount">Monthly rent</label>
                        <input id="rent_amount" name="rent_amount" type="number" step="0.01" min="0" value="{{ old('rent_amount') }}" required placeholder="1850.00">
                        @error('rent_amount')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="field">
                    <label for="deposit_amount">Deposit amount</label>
                    <input id="deposit_amount" name="deposit_amount" type="number" step="0.01" min="0" value="{{ old('deposit_amount') }}" required placeholder="2135.00">
                    @error('deposit_amount')<span class="field-error">{{ $message }}</span>@enderror
                </div>
            </section>
        </div>

        <aside class="stack">
            <section class="panel panel-tinted">
                <span class="support-icon"><x-icon name="building" :size="20" /></span>
                <h3>Add the first unit now</h3>
                <p>A property needs at least one unit to be let. You can add more units and full tenancies after saving.</p>
            </section>
            <button type="submit" class="button button-dark button-block">Create property <x-icon name="arrow-right" :size="16" /></button>
            <a href="{{ route('admin.properties.index') }}" class="button button-ghost button-block">Cancel</a>
        </aside>
    </form>
@endsection
