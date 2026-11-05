<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PropertyController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->toString();

        $properties = Property::query()
            ->where('organization_id', auth()->user()->organization_id)
            ->with(['units', 'landlord', 'manager'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('reference', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhere('postcode', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view('admin.properties.index', [
            'properties' => $properties,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.properties.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateProperty($request);
        $unitData = $request->validate([
            'unit_number' => ['required', 'string', 'max:30'],
            'rent_amount' => ['required', 'numeric', 'min:0', 'max:100000'],
            'deposit_amount' => ['required', 'numeric', 'min:0', 'max:100000'],
        ]);

        $property = DB::transaction(function () use ($data, $unitData) {
            $property = Property::create([
                ...$data,
                'organization_id' => auth()->user()->organization_id,
            ]);

            Unit::create([
                'property_id' => $property->id,
                'unit_number' => $unitData['unit_number'],
                'rent_amount' => $unitData['rent_amount'],
                'deposit_amount' => $unitData['deposit_amount'],
                'bedrooms' => $data['bedrooms'],
                'bathrooms' => $data['bathrooms'],
                'status' => 'available',
            ]);

            return $property;
        });

        return redirect()
            ->route('admin.properties.show', $property)
            ->with('status', 'Property added to your portfolio.');
    }

    public function show(Property $property): View
    {
        $this->authorizeProperty($property);

        return view('admin.properties.show', [
            'property' => $property->load([
                'units.tenancies.tenant',
                'landlord',
                'manager',
                'tenancies.tenant',
                'maintenanceRequests.tenancy.tenant',
                'documents',
            ]),
        ]);
    }

    public function edit(Property $property): View
    {
        $this->authorizeProperty($property);

        return view('admin.properties.edit', [
            'property' => $property->load('units'),
            ...$this->formData(),
        ]);
    }

    public function update(Request $request, Property $property): RedirectResponse
    {
        $this->authorizeProperty($property);
        $data = $this->validateProperty($request, $property);

        $property->update($data);

        return redirect()
            ->route('admin.properties.show', $property)
            ->with('status', 'Property details updated.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateProperty(Request $request, ?Property $property = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'reference' => ['required', 'string', 'max:30', Rule::unique('properties', 'reference')->ignore($property?->id)],
            'type' => ['required', 'in:apartment,house,studio,maisonette,commercial'],
            'address_line_1' => ['required', 'string', 'max:160'],
            'address_line_2' => ['nullable', 'string', 'max:160'],
            'city' => ['required', 'string', 'max:80'],
            'postcode' => ['required', 'string', 'max:12'],
            'description' => ['nullable', 'string', 'max:3000'],
            'bedrooms' => ['required', 'integer', 'min:0', 'max:20'],
            'bathrooms' => ['required', 'integer', 'min:0', 'max:20'],
            'size_sqft' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'status' => ['required', 'in:available,occupied,maintenance,off_market'],
            'landlord_id' => ['nullable', 'exists:users,id'],
            'manager_id' => ['nullable', 'exists:users,id'],
            'accent' => ['required', 'in:forest,clay,ocean,sand,plum'],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(): array
    {
        $organizationId = auth()->user()->organization_id;

        return [
            'landlords' => User::query()
                ->where('organization_id', $organizationId)
                ->where('role', UserRole::Landlord)
                ->orderBy('name')
                ->get(),
            'managers' => User::query()
                ->where('organization_id', $organizationId)
                ->whereIn('role', [UserRole::PropertyManager, UserRole::AgencyAdmin])
                ->orderBy('name')
                ->get(),
        ];
    }

    private function authorizeProperty(Property $property): void
    {
        abort_unless($property->organization_id === auth()->user()->organization_id, 404);
    }
}
