<?php

use App\Models\User;

// ============================================================================
// Requirement 1.1: Unauthenticated Access
// ============================================================================

test('unauthenticated users can access landing page', function () {
  $response = $this->get('/');

  $response->assertStatus(200);
  $response->assertInertia(
    fn($page) => $page
      ->component('Landing')
      ->has('canLogin')
      ->has('canRegister')
  );
});

test('landing page displays login and register options', function () {
  $response = $this->get('/');

  $response->assertStatus(200);
  $response->assertInertia(
    fn($page) => $page
      ->where('canLogin', true)
      ->where('canRegister', true)
  );
});

test('landing page is accessible at root URL', function () {
  $response = $this->get('/');

  $response->assertStatus(200);
  $response->assertInertia(fn($page) => $page->component('Landing'));
});

// ============================================================================
// Requirement 1.5: Authenticated User Redirect
// ============================================================================

test('authenticated users are redirected to dashboard', function () {
  $user = User::factory()->create();

  $response = $this->actingAs($user)->get('/');

  $response->assertRedirect(route('dashboard'));
});

test('authenticated users cannot view landing page directly', function () {
  $user = User::factory()->create(['role' => 'requisitioner']);

  $response = $this->actingAs($user)->get('/');

  $response->assertRedirect(route('dashboard'));
  $response->assertStatus(302);
});

test('authenticated admin users are also redirected to dashboard', function () {
  $admin = User::factory()->create(['role' => 'admin']);

  $response = $this->actingAs($admin)->get('/');

  $response->assertRedirect(route('dashboard'));
});

// ============================================================================
// Requirement 1.2, 1.3, 1.4: Navigation Links
// ============================================================================

test('landing page route is named correctly', function () {
  expect(route('landing'))->toBe(url('/'));
});

test('landing page provides correct props for navigation', function () {
  $response = $this->get('/');

  $response->assertInertia(
    fn($page) => $page
      ->component('Landing')
      ->where('canLogin', true)
      ->where('canRegister', true)
  );
});

test('landing page has login route available', function () {
  expect(route('login'))->toBeString();
  expect(route('login'))->toContain('/login');
});

test('landing page has register route available', function () {
  expect(route('register'))->toBeString();
  expect(route('register'))->toContain('/register');
});

// ============================================================================
// Additional Navigation and Functionality Tests
// ============================================================================

test('landing page returns correct content type', function () {
  $response = $this->get('/');

  $response->assertStatus(200);
  $response->assertHeader('Content-Type', 'text/html; charset=UTF-8');
});

test('landing page does not require authentication', function () {
  // Ensure no auth middleware is blocking access
  $response = $this->get('/');

  $response->assertStatus(200);
  $response->assertDontSee('Login Required');
  $response->assertDontSee('Unauthorized');
});

test('landing page can be accessed multiple times without issues', function () {
  // Test that landing page is stateless and can handle multiple requests
  $response1 = $this->get('/');
  $response2 = $this->get('/');
  $response3 = $this->get('/');

  $response1->assertStatus(200);
  $response2->assertStatus(200);
  $response3->assertStatus(200);
});

// ============================================================================
// Responsive Design and Component Tests
// ============================================================================

test('landing page renders with all required components', function () {
  $response = $this->get('/');

  $response->assertStatus(200);
  $response->assertInertia(
    fn($page) => $page
      ->component('Landing')
      ->has('canLogin')
      ->has('canRegister')
  );
});

test('landing page provides boolean values for navigation flags', function () {
  $response = $this->get('/');

  $response->assertInertia(
    fn($page) => $page
      ->where('canLogin', fn($value) => is_bool($value))
      ->where('canRegister', fn($value) => is_bool($value))
  );
});
