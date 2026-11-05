<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\MaintenanceRequest;
use App\Models\PortalNotification;
use App\Models\RentPayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    private function user(string $email): User
    {
        return User::query()->where('email', $email)->firstOrFail();
    }

    public function test_public_landing_page_loads(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('PropNest');
    }

    public function test_demo_users_are_seeded_with_expected_roles(): void
    {
        $this->assertSame(UserRole::Tenant, $this->user('alex.morgan@propnest.test')->role);
        $this->assertSame(UserRole::PropertyManager, $this->user('manager@propnest.test')->role);
        $this->assertSame(UserRole::Landlord, $this->user('landlord@propnest.test')->role);
        $this->assertSame(UserRole::Contractor, $this->user('contractor@propnest.test')->role);
    }

    public function test_login_redirects_each_role_to_its_dashboard(): void
    {
        $this->post('/login', [
            'email' => 'alex.morgan@propnest.test',
            'password' => 'password',
        ])->assertRedirect('/dashboard');

        $this->post('/logout');

        $this->post('/login', [
            'email' => 'manager@propnest.test',
            'password' => 'password',
        ])->assertRedirect('/admin');
    }

    public function test_tenant_can_browse_the_core_portal_pages(): void
    {
        $tenant = $this->user('alex.morgan@propnest.test');

        foreach (['/dashboard', '/tenancy', '/payments', '/maintenance', '/documents', '/requests', '/announcements', '/notifications'] as $path) {
            $this->actingAs($tenant)->get($path)->assertOk();
        }
    }

    public function test_tenant_cannot_access_the_manager_workspace(): void
    {
        $tenant = $this->user('alex.morgan@propnest.test');

        $this->actingAs($tenant)->get('/admin')->assertForbidden();
    }

    public function test_manager_can_access_the_workspace_and_contractor_cannot(): void
    {
        $this->actingAs($this->user('manager@propnest.test'))->get('/admin')->assertOk();
        $this->actingAs($this->user('contractor@propnest.test'))->get('/admin')->assertForbidden();
    }

    public function test_tenant_can_pay_an_outstanding_rent_instalment(): void
    {
        $tenant = $this->user('alex.morgan@propnest.test');

        $payment = RentPayment::query()
            ->where('tenancy_id', $tenant->activeTenancy()->value('id'))
            ->whereIn('status', ['pending', 'overdue'])
            ->firstOrFail();

        $this->actingAs($tenant)
            ->post("/payments/{$payment->id}/pay", ['confirm' => '1'])
            ->assertRedirect(route('tenant.payments.receipt', $payment));

        $payment->refresh();

        $this->assertSame('paid', $payment->status);
        $this->assertNotNull($payment->paid_at);
        $this->assertNotNull($payment->receipt_number);

        $this->actingAs($tenant)
            ->get(route('tenant.payments.receipt', $payment))
            ->assertOk()
            ->assertSee($payment->receipt_number);

        $this->assertDatabaseHas('portal_notifications', [
            'user_id' => $tenant->id,
            'type' => 'payment',
        ]);
    }

    public function test_tenant_can_submit_a_maintenance_request(): void
    {
        $tenant = $this->user('alex.morgan@propnest.test');

        $response = $this->actingAs($tenant)->post('/maintenance', [
            'title' => 'Leaking kitchen tap',
            'category' => 'Plumbing',
            'priority' => 'normal',
            'description' => 'The kitchen tap has been dripping constantly since yesterday morning.',
        ]);

        $maintenance = MaintenanceRequest::query()->latest('id')->firstOrFail();

        $response->assertRedirect(route('tenant.maintenance.show', $maintenance));

        $this->assertDatabaseHas('maintenance_requests', [
            'id' => $maintenance->id,
            'submitted_by' => $tenant->id,
            'status' => 'submitted',
        ]);

        $this->assertDatabaseHas('maintenance_updates', [
            'maintenance_request_id' => $maintenance->id,
        ]);
    }

    public function test_manager_update_notifies_the_submitting_tenant(): void
    {
        $manager = $this->user('manager@propnest.test');
        $maintenance = MaintenanceRequest::query()->where('status', '!=', 'completed')->firstOrFail();
        $before = PortalNotification::query()->count();

        $this->actingAs($manager)
            ->patch("/admin/maintenance/{$maintenance->id}", [
                'status' => 'in_progress',
                'priority' => 'high',
                'message' => 'A contractor has been assigned and will attend tomorrow.',
            ])
            ->assertRedirect();

        $this->assertSame('in_progress', $maintenance->refresh()->status);
        $this->assertGreaterThan($before, PortalNotification::query()->count());
        $this->assertDatabaseHas('portal_notifications', [
            'user_id' => $maintenance->submitted_by,
            'type' => 'maintenance',
        ]);
    }

    public function test_contractor_only_sees_jobs_assigned_to_them(): void
    {
        $contractor = $this->user('contractor@propnest.test');

        $assigned = MaintenanceRequest::query()->where('assigned_to', $contractor->id)->firstOrFail();
        $unassigned = MaintenanceRequest::query()->whereNull('assigned_to')->firstOrFail();

        $this->actingAs($contractor)->get('/contractor')->assertOk();
        $this->actingAs($contractor)->get("/contractor/jobs/{$assigned->id}")->assertOk();
        $this->actingAs($contractor)->get("/contractor/jobs/{$unassigned->id}")->assertNotFound();
    }

    public function test_registration_creates_a_tenant_account(): void
    {
        $this->post('/register', [
            'name' => 'New Tenant',
            'email' => 'new.tenant@propnest.test',
            'phone' => '07700 900999',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect('/dashboard');

        $this->assertDatabaseHas('users', [
            'email' => 'new.tenant@propnest.test',
            'role' => UserRole::Tenant->value,
        ]);
    }
}
