<?php

use App\Models\User;

test('authenticated users can access user guide index', function () {
  $user = User::factory()->create();

  $response = $this
    ->actingAs($user)
    ->get('/user-guide');

  $response->assertStatus(200);
  $response->assertInertia(
    fn($page) => $page
      ->component('UserGuide/Index')
      ->has('sections')
      ->has('tableOfContents')
  );
});

test('unauthenticated users cannot access user guide', function () {
  $response = $this->get('/user-guide');

  $response->assertRedirect(route('login'));
});

test('authenticated users can access user guide sections', function () {
  $user = User::factory()->create();

  $sections = ['getting-started', 'requisitioner', 'approver', 'admin', 'workflow', 'faq'];

  foreach ($sections as $section) {
    $response = $this
      ->actingAs($user)
      ->get("/user-guide/{$section}");

    $response->assertStatus(200);
    $response->assertInertia(
      fn($page) => $page
        ->component('UserGuide/Section')
        ->where('section', $section)
        ->has('content')
        ->has('navigation')
    );
  }
});

test('user guide section displays markdown content', function () {
  $user = User::factory()->create();

  $response = $this
    ->actingAs($user)
    ->get('/user-guide/getting-started');

  $response->assertStatus(200);
  $response->assertInertia(
    fn($page) => $page
      ->has('content.markdown')
      ->has('content.title')
      ->has('content.lastUpdated')
  );
});

test('user guide navigation includes previous and next links', function () {
  $user = User::factory()->create();

  // Test middle section (should have both previous and next)
  $response = $this
    ->actingAs($user)
    ->get('/user-guide/requisitioner');

  $response->assertStatus(200);
  $response->assertInertia(
    fn($page) => $page
      ->has('navigation.previous')
      ->has('navigation.next')
  );
});

test('user guide first section has no previous link', function () {
  $user = User::factory()->create();

  $response = $this
    ->actingAs($user)
    ->get('/user-guide/getting-started');

  $response->assertStatus(200);
  $response->assertInertia(
    fn($page) => $page
      ->where('navigation.previous', null)
      ->has('navigation.next')
  );
});

test('user guide last section has no next link', function () {
  $user = User::factory()->create();

  $response = $this
    ->actingAs($user)
    ->get('/user-guide/faq');

  $response->assertStatus(200);
  $response->assertInertia(
    fn($page) => $page
      ->has('navigation.previous')
      ->where('navigation.next', null)
  );
});

test('user guide returns 404 for non-existent section', function () {
  $user = User::factory()->create();

  $response = $this
    ->actingAs($user)
    ->get('/user-guide/non-existent-section');

  $response->assertStatus(404);
});

test('unauthenticated users cannot access user guide sections', function () {
  $response = $this->get('/user-guide/getting-started');

  $response->assertRedirect(route('login'));
});

test('user guide index displays all sections', function () {
  $user = User::factory()->create();

  $response = $this
    ->actingAs($user)
    ->get('/user-guide');

  $response->assertStatus(200);
  $response->assertInertia(
    fn($page) => $page
      ->has('tableOfContents', 6) // Should have 6 main sections
  );
});

test('user guide sections have proper structure', function () {
  $user = User::factory()->create();

  $response = $this
    ->actingAs($user)
    ->get('/user-guide');

  $response->assertStatus(200);
  $response->assertInertia(
    fn($page) => $page
      ->has('sections.getting-started')
      ->has('sections.requisitioner')
      ->has('sections.approver')
      ->has('sections.admin')
      ->has('sections.workflow')
      ->has('sections.faq')
  );
});
