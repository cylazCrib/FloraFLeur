<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Shop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ShopRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_shop_registration_works()
    {
        Storage::fake('public');

        $response = $this->post('/register-shop', [
            'owner_name' => 'John Doe',
            'email' => 'vendor2@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'owner_phone' => '1234567890',
            'shop_name' => 'My Flower Shop',
            'shop_description' => 'A beautiful shop',
            'shop_phone' => '0987654321',
            'address' => '123 Street',
            'zip_code' => '12345',
            'delivery_coverage' => 'City wide',
            'permit_file' => UploadedFile::fake()->image('permit.jpg'),
        ]);

        $response->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'vendor2@example.com',
            'role' => 'vendor',
        ]);

        $user = User::where('email', 'vendor2@example.com')->first();
        $this->assertDatabaseHas('shops', [
            'user_id' => $user->id,
            'name' => 'My Flower Shop',
            'status' => 'pending',
        ]);
    }
}
