<?php

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create and authenticate a user for each test
    $this->user = User::factory()->admin()->create();
    $this->actingAs($this->user);
});

describe('Client CRUD Operations', function () {

    describe('Creating Clients', function () {

        it('can create a client with valid data', function () {
            // Arrange: Prepare valid client data
            $clientData = [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'phone' => '81989274343',
                'address' => '123 Main St, City, State 12345',
                'document' => '1234567890',
                'zip_code' => '12345'
            ];

            // Act: Send POST request to create client
            $response = $this->post(route('clients.store'), $clientData);

            unset($clientData['phone']);
            // Assert: Check response and database
            $response->assertStatus(201)
                ->assertJsonFragment($clientData);

            // Verify client was saved to database
            $this->assertDatabaseHas('clients', $clientData);
        });

        it('validates required name field', function () {
            $this->withoutExceptionHandling();

            // Arrange: Client data without required name
            $clientData = [
                'email' => 'john.doe@example.com',
                'phone' => '+1234567890',
                // 'name' ausente
            ];

            $this->expectException(QueryException::class);

            // Act: Attempt to create client without name
            $this->post(route('clients.store'), $clientData);

            // Verify no client was created
            $this->assertDatabaseMissing('clients', [
                'email' => 'john.doe@example.com'
            ]);
        });

        // it('validates unique email constraint', function () {
        //     // Arrange: Create existing client
        //     Client::factory()->create(['email' => 'existing@example.com']);

        //     $clientData = [
        //         'name' => 'John Doe',
        //         'email' => 'existing@example.com', // Duplicate email
        //         'phone' => '+1234567890',
        //     ];

        //     // Act: Attempt to create client with duplicate email
        //     $response = $this->post(route('clients.store'), $clientData);

        //     // Assert: Should return validation error
        //     $response->assertSessionHasErrors(['email'])
        //         ->assertRedirect();
        // });

        // it('validates required phone field', function () {
        //     // Arrange: Client data without required phone
        //     $clientData = [
        //         'name' => 'John Doe',
        //         'email' => 'john.doe@example.com',
        //     ];

        //     // Act: Attempt to create client without phone
        //     $response = $this->post(route('clients.store'), $clientData);

        //     // Assert: Should return validation error
        //     $response->assertSessionHasErrors(['phone'])
        //         ->assertRedirect();
        // });

        // it('can access create client form', function () {
        //     // Act: Request create client form
        //     $response = $this->get(route('clients.create'));

        //     // Assert: Should render create form
        //     $response->assertOk()
        //         ->assertInertia(
        //             fn(Assert $page) =>
        //             $page->component('Clients/Create')
        //         );
        // });
    });

    // describe('Reading Clients', function () {

    //     it('can show a specific client', function () {
    //         // Arrange: Create a client
    //         $client = Client::factory()->create([
    //             'name' => 'Jane Smith',
    //             'email' => 'jane.smith@example.com',
    //             'phone' => '+0987654321',
    //             'company' => 'Tech Solutions Inc',
    //         ]);

    //         // Act: Request specific client details
    //         $response = $this->get(route('clients.show', $client));

    //         // Assert: Should render client details
    //         $response->assertOk()
    //             ->assertInertia(
    //                 fn(Assert $page) =>
    //                 $page->component('Clients/Show')
    //                     ->has('client')
    //                     ->where('client.id', $client->id)
    //                     ->where('client.name', 'Jane Smith')
    //                     ->where('client.email', 'jane.smith@example.com')
    //                     ->where('client.phone', '+0987654321')
    //                     ->where('client.company', 'Tech Solutions Inc')
    //             );
    //     });

    //     it('returns 404 for non-existent client', function () {
    //         // Act: Request non-existent client
    //         $response = $this->get(route('clients.show', 99999));

    //         // Assert: Should return 404 error
    //         $response->assertNotFound();
    //     });

    //     it('can list all clients with pagination', function () {
    //         // Arrange: Create multiple clients
    //         $clients = Client::factory()->count(15)->create();

    //         // Act: Request clients list
    //         $response = $this->get(route('clients.index'));

    //         // Assert: Should render clients index with pagination
    //         $response->assertOk()
    //             ->assertInertia(
    //                 fn(Assert $page) =>
    //                 $page->component('Clients/Index')
    //                     ->has('clients')
    //                     ->has('clients.data', 10) // Default pagination
    //                     ->has('clients.links')
    //                     ->where('clients.total', 15)
    //                     ->where('clients.last_page', 2)
    //             );
    //     });

    //     it('can filter clients by search query', function () {
    //         // Arrange: Create clients with different names
    //         Client::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);
    //         Client::factory()->create(['name' => 'Jane Smith', 'email' => 'jane@example.com']);
    //         Client::factory()->create(['name' => 'Bob Johnson', 'email' => 'bob@example.com']);

    //         // Act: Search for clients with "John" in name
    //         $response = $this->get(route('clients.index', ['search' => 'John']));

    //         // Assert: Should return only matching clients
    //         $response->assertOk()
    //             ->assertInertia(
    //                 fn(Assert $page) =>
    //                 $page->component('Clients/Index')
    //                     ->has('clients')
    //                     ->has('clients.data', 2) // John Doe and Bob Johnson
    //                     ->where('filters.search', 'John')
    //             );
    //     });

    //     it('can access edit client form', function () {
    //         // Arrange: Create a client
    //         $client = Client::factory()->create();

    //         // Act: Request edit client form
    //         $response = $this->get(route('clients.edit', $client));

    //         // Assert: Should render edit form with client data
    //         $response->assertOk()
    //             ->assertInertia(
    //                 fn(Assert $page) =>
    //                 $page->component('Clients/Edit')
    //                     ->has('client')
    //                     ->where('client.id', $client->id)
    //             );
    //     });
    // });

    // describe('Updating Clients', function () {

    //     it('can update a client with valid data', function () {
    //         // Arrange: Create a client
    //         $client = Client::factory()->create([
    //             'name' => 'Original Name',
    //             'email' => 'original@example.com',
    //             'phone' => '+1111111111',
    //         ]);

    //         $updateData = [
    //             'name' => 'Updated Name',
    //             'email' => 'updated@example.com',
    //             'phone' => '+2222222222',
    //             'company' => 'Updated Company',
    //         ];

    //         // Act: Update the client
    //         $response = $this->put(route('clients.update', $client), $updateData);

    //         // Assert: Should redirect with success message
    //         $response->assertRedirect(route('clients.show', $client))
    //             ->assertSessionHas('success', 'Client updated successfully');

    //         // Verify database was updated
    //         $this->assertDatabaseHas('clients', [
    //             'id' => $client->id,
    //             'name' => 'Updated Name',
    //             'email' => 'updated@example.com',
    //             'phone' => '+2222222222',
    //             'company' => 'Updated Company',
    //         ]);

    //         // Verify old data is no longer in database
    //         $this->assertDatabaseMissing('clients', [
    //             'id' => $client->id,
    //             'name' => 'Original Name',
    //             'email' => 'original@example.com',
    //         ]);
    //     });

    //     it('validates required fields when updating', function () {
    //         // Arrange: Create a client
    //         $client = Client::factory()->create();

    //         $invalidUpdateData = [
    //             'name' => '', // Empty name should fail validation
    //             'email' => 'invalid-email',
    //             'phone' => '',
    //         ];

    //         // Act: Attempt to update with invalid data
    //         $response = $this->put(route('clients.update', $client), $invalidUpdateData);

    //         // Assert: Should return validation errors
    //         $response->assertSessionHasErrors(['name', 'email', 'phone'])
    //             ->assertRedirect();
    //     });

    //     it('validates unique email when updating different client', function () {
    //         // Arrange: Create two clients
    //         $client1 = Client::factory()->create(['email' => 'client1@example.com']);
    //         $client2 = Client::factory()->create(['email' => 'client2@example.com']);

    //         // Act: Try to update client2 with client1's email
    //         $response = $this->put(route('clients.update', $client2), [
    //             'name' => 'Updated Name',
    //             'email' => 'client1@example.com', // This should fail
    //             'phone' => '+1234567890',
    //         ]);

    //         // Assert: Should return validation error
    //         $response->assertSessionHasErrors(['email'])
    //             ->assertRedirect();
    //     });

    //     it('allows keeping same email when updating same client', function () {
    //         // Arrange: Create a client
    //         $client = Client::factory()->create([
    //             'name' => 'John Doe',
    //             'email' => 'john@example.com',
    //             'phone' => '+1234567890',
    //         ]);

    //         // Act: Update client keeping same email
    //         $response = $this->put(route('clients.update', $client), [
    //             'name' => 'John Updated',
    //             'email' => 'john@example.com', // Same email should be allowed
    //             'phone' => '+0987654321',
    //         ]);

    //         // Assert: Should succeed
    //         $response->assertRedirect(route('clients.show', $client))
    //             ->assertSessionHas('success');

    //         $this->assertDatabaseHas('clients', [
    //             'id' => $client->id,
    //             'name' => 'John Updated',
    //             'email' => 'john@example.com',
    //             'phone' => '+0987654321',
    //         ]);
    //     });

    //     it('returns 404 when updating non-existent client', function () {
    //         // Act: Try to update non-existent client
    //         $response = $this->put(route('clients.update', 99999), [
    //             'name' => 'Test Name',
    //             'email' => 'test@example.com',
    //             'phone' => '+1234567890',
    //         ]);

    //         // Assert: Should return 404
    //         $response->assertNotFound();
    //     });
    // });

    // describe('Deleting Clients', function () {

    //     it('can delete an existing client', function () {
    //         // Arrange: Create a client
    //         $client = Client::factory()->create([
    //             'name' => 'Client to Delete',
    //             'email' => 'delete@example.com',
    //         ]);

    //         // Verify client exists before deletion
    //         $this->assertDatabaseHas('clients', [
    //             'id' => $client->id,
    //             'email' => 'delete@example.com',
    //         ]);

    //         // Act: Delete the client
    //         $response = $this->delete(route('clients.destroy', $client));

    //         // Assert: Should redirect with success message
    //         $response->assertRedirect(route('clients.index'))
    //             ->assertSessionHas('success', 'Client deleted successfully');

    //         // Verify client was soft deleted (if using soft deletes)
    //         $this->assertSoftDeleted('clients', [
    //             'id' => $client->id,
    //             'email' => 'delete@example.com',
    //         ]);
    //     });

    //     it('returns 404 when deleting non-existent client', function () {
    //         // Act: Try to delete non-existent client
    //         $response = $this->delete(route('clients.destroy', 99999));

    //         // Assert: Should return 404
    //         $response->assertNotFound();
    //     });

    //     it('can soft delete a client', function () {
    //         // Arrange: Create a client
    //         $client = Client::factory()->create([
    //             'name' => 'Soft Delete Test',
    //             'email' => 'softdelete@example.com',
    //         ]);

    //         // Act: Soft delete the client
    //         $response = $this->delete(route('clients.destroy', $client));

    //         // Assert: Should redirect with success
    //         $response->assertRedirect(route('clients.index'))
    //             ->assertSessionHas('success');

    //         // Verify the client is soft deleted
    //         $this->assertSoftDeleted('clients', [
    //             'id' => $client->id,
    //             'email' => 'softdelete@example.com',
    //         ]);

    //         // Verify the client still exists in database but with deleted_at timestamp
    //         $deletedClient = Client::withTrashed()->find($client->id);
    //         expect($deletedClient)->not->toBeNull();
    //         expect($deletedClient->deleted_at)->not->toBeNull();
    //     });
    // });

    // describe('Authorization and Security', function () {

    //     it('requires authentication for all client operations', function () {
    //         // Arrange: Create a client and logout user
    //         $client = Client::factory()->create();
    //         auth()->logout();

    //         // Act & Assert: All operations should require authentication
    //         $this->get(route('clients.index'))->assertRedirect(route('login'));
    //         $this->get(route('clients.show', $client))->assertRedirect(route('login'));
    //         $this->get(route('clients.create'))->assertRedirect(route('login'));
    //         $this->get(route('clients.edit', $client))->assertRedirect(route('login'));
    //         $this->post(route('clients.store'), [])->assertRedirect(route('login'));
    //         $this->put(route('clients.update', $client), [])->assertRedirect(route('login'));
    //         $this->delete(route('clients.destroy', $client))->assertRedirect(route('login'));
    //     });

    //     it('prevents SQL injection in search queries', function () {
    //         // Arrange: Create some clients
    //         Client::factory()->count(3)->create();

    //         // Act: Attempt SQL injection through search parameter
    //         $maliciousQuery = "'; DROP TABLE clients; --";
    //         $response = $this->get(route('clients.index', ['search' => $maliciousQuery]));

    //         // Assert: Should handle safely without errors
    //         $response->assertOk();

    //         // Verify clients table still exists and has data
    //         expect(Client::count())->toBe(3);
    //     });

    //     it('sanitizes input data properly', function () {
    //         // Arrange: Client data with potentially malicious content
    //         $clientData = [
    //             'name' => '<script>alert("xss")</script>John Doe',
    //             'email' => 'john@example.com',
    //             'phone' => '+1234567890',
    //             'address' => '<img src="x" onerror="alert(1)">123 Main St',
    //         ];

    //         // Act: Create client with potentially malicious data
    //         $response = $this->post(route('clients.store'), $clientData);

    //         // Assert: Should succeed and store sanitized data
    //         $response->assertRedirect(route('clients.index'));

    //         // Verify data is stored (Laravel automatically escapes when displaying)
    //         $this->assertDatabaseHas('clients', [
    //             'name' => '<script>alert("xss")</script>John Doe',
    //             'email' => 'john@example.com',
    //         ]);
    //     });
    // });

    // describe('Edge Cases and Error Handling', function () {

    //     it('handles very long input gracefully', function () {
    //         // Arrange: Create data with very long strings
    //         $longString = str_repeat('a', 1000);
    //         $clientData = [
    //             'name' => $longString,
    //             'email' => 'test@example.com',
    //             'phone' => '+1234567890',
    //         ];

    //         // Act: Attempt to create client with long data
    //         $response = $this->post(route('clients.store'), $clientData);

    //         // Assert: Should handle according to validation rules
    //         // This should return validation error if name has max length constraint
    //         $response->assertSessionHasErrors(['name']);
    //     });

    //     it('handles special characters in client data', function () {
    //         // Arrange: Client data with special characters
    //         $clientData = [
    //             'name' => 'José María Ñoño',
    //             'email' => 'jose.maria@example.com',
    //             'phone' => '+34-123-456-789',
    //             'address' => '123 Main St. Apt #4B, São Paulo',
    //         ];

    //         // Act: Create client with special characters
    //         $response = $this->post(route('clients.store'), $clientData);

    //         // Assert: Should handle special characters correctly
    //         $response->assertRedirect(route('clients.index'))
    //             ->assertSessionHas('success');

    //         $this->assertDatabaseHas('clients', [
    //             'name' => 'José María Ñoño',
    //             'email' => 'jose.maria@example.com',
    //         ]);
    //     });

    //     it('handles concurrent updates gracefully', function () {
    //         // Arrange: Create a client
    //         $client = Client::factory()->create(['name' => 'Original']);

    //         // Act: Simulate concurrent updates
    //         $updateData1 = ['name' => 'Update 1', 'email' => $client->email, 'phone' => $client->phone];
    //         $updateData2 = ['name' => 'Update 2', 'email' => $client->email, 'phone' => $client->phone];

    //         $response1 = $this->put(route('clients.update', $client), $updateData1);
    //         $response2 = $this->put(route('clients.update', $client), $updateData2);

    //         // Assert: Both should succeed (last one wins)
    //         $response1->assertRedirect();
    //         $response2->assertRedirect();

    //         // Verify final state
    //         $updatedClient = Client::find($client->id);
    //         expect($updatedClient->name)->toBe('Update 2');
    //     });

    //     it('handles pagination edge cases', function () {
    //         // Arrange: Create exactly one client
    //         Client::factory()->create();

    //         // Act: Request page that doesn't exist
    //         $response = $this->get(route('clients.index', ['page' => 999]));

    //         // Assert: Should handle gracefully (Laravel typically redirects to last valid page)
    //         $response->assertOk()
    //             ->assertInertia(
    //                 fn(Assert $page) =>
    //                 $page->component('Clients/Index')
    //                     ->has('clients')
    //             );
    //     });

    //     it('handles empty search results', function () {
    //         // Arrange: Create some clients
    //         Client::factory()->count(3)->create();

    //         // Act: Search for non-existent term
    //         $response = $this->get(route('clients.index', ['search' => 'nonexistentclient']));

    //         // Assert: Should return empty results gracefully
    //         $response->assertOk()
    //             ->assertInertia(
    //                 fn(Assert $page) =>
    //                 $page->component('Clients/Index')
    //                     ->has('clients')
    //                     ->has('clients.data', 0)
    //                     ->where('clients.total', 0)
    //             );
    //     });
    // });
});
