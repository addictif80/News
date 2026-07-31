<?php

namespace Tests\Feature;

use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SupportTicketTest extends TestCase
{
    use RefreshDatabase;

    private function member(): User
    {
        Role::firstOrCreate(['name' => 'gratuit', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->assignRole('gratuit');

        return $user;
    }

    private function moderator(): User
    {
        Role::firstOrCreate(['name' => 'moderateur', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->assignRole('moderateur');

        return $user;
    }

    public function test_user_can_create_a_ticket(): void
    {
        $user = $this->member();

        $response = $this->actingAs($user)->post('/compte/tickets', [
            'subject' => 'Problème de connexion',
            'body' => "Je n'arrive pas à me connecter.",
        ]);

        $ticket = SupportTicket::first();
        $response->assertRedirect(route('tickets.show', $ticket));
        $this->assertSame($user->id, $ticket->user_id);
        $this->assertSame('open', $ticket->status);
        $this->assertCount(1, $ticket->messages);
    }

    public function test_user_cannot_view_someone_elses_ticket(): void
    {
        $owner = $this->member();
        $intruder = $this->member();

        $ticket = SupportTicket::create([
            'user_id' => $owner->id,
            'subject' => 'Sujet privé',
            'status' => 'open',
            'last_activity_at' => now(),
        ]);

        $response = $this->actingAs($intruder)->get(route('tickets.show', $ticket));

        $response->assertForbidden();
    }

    public function test_moderateur_can_view_and_reply_to_any_ticket(): void
    {
        $owner = $this->member();
        $moderator = $this->moderator();

        $ticket = SupportTicket::create([
            'user_id' => $owner->id,
            'subject' => 'Besoin d\'aide',
            'status' => 'open',
            'last_activity_at' => now(),
        ]);

        $this->actingAs($moderator)->get(route('tickets.show', $ticket))->assertOk();

        $response = $this->actingAs($moderator)->post(route('tickets.reply', $ticket), [
            'body' => 'Nous regardons ça.',
        ]);

        $response->assertRedirect();
        $ticket->refresh();
        $this->assertSame('pending', $ticket->status);
        $this->assertTrue($ticket->messages()->latest('id')->first()->is_staff_reply);
    }

    public function test_user_reply_does_not_flip_status_to_pending(): void
    {
        $owner = $this->member();

        $ticket = SupportTicket::create([
            'user_id' => $owner->id,
            'subject' => 'Suivi',
            'status' => 'open',
            'last_activity_at' => now(),
        ]);

        $this->actingAs($owner)->post(route('tickets.reply', $ticket), [
            'body' => 'Toujours pas résolu.',
        ]);

        $this->assertSame('open', $ticket->fresh()->status);
    }
}
