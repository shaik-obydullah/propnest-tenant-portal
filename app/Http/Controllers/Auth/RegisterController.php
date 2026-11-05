<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $organization = Organization::query()->orderBy('id')->first()
            ?? Organization::create([
                'name' => 'Independent Tenants',
                'slug' => 'independent-tenants',
                'plan' => 'starter',
            ]);

        $user = User::create([
            'organization_id' => $organization->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
            'role' => UserRole::Tenant,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('tenant.dashboard')
            ->with('status', 'Your PropNest account is ready. Welcome aboard.');
    }
}
