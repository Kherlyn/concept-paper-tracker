<?php

use App\Models\User;
use App\Models\WorkflowStage;
use App\Policies\WorkflowStagePolicy;

beforeEach(function () {
  $this->policy = new WorkflowStagePolicy();
});

describe('complete method', function () {
  test('allows assigned user with matching role to complete stage', function () {
    $user = User::factory()->create([
      'role' => 'sps',
      'is_active' => true,
    ]);

    $stage = WorkflowStage::factory()->create([
      'assigned_role' => 'sps',
      'assigned_user_id' => $user->id,
      'status' => 'pending',
    ]);

    expect($this->policy->complete($user, $stage))->toBeTrue();
  });

  test('denies inactive user from completing stage', function () {
    $user = User::factory()->create([
      'role' => 'sps',
      'is_active' => false,
    ]);

    $stage = WorkflowStage::factory()->create([
      'assigned_role' => 'sps',
      'assigned_user_id' => $user->id,
      'status' => 'pending',
    ]);

    expect($this->policy->complete($user, $stage))->toBeFalse();
  });

  test('denies user with wrong role from completing stage', function () {
    $user = User::factory()->create([
      'role' => 'vp_acad',
      'is_active' => true,
    ]);

    $stage = WorkflowStage::factory()->create([
      'assigned_role' => 'sps',
      'status' => 'pending',
    ]);

    expect($this->policy->complete($user, $stage))->toBeFalse();
  });

  test('denies completing already completed stage', function () {
    $user = User::factory()->create([
      'role' => 'sps',
      'is_active' => true,
    ]);

    $stage = WorkflowStage::factory()->create([
      'assigned_role' => 'sps',
      'assigned_user_id' => $user->id,
      'status' => 'completed',
    ]);

    expect($this->policy->complete($user, $stage))->toBeFalse();
  });

  test('denies user when different user is specifically assigned', function () {
    $user1 = User::factory()->create([
      'role' => 'sps',
      'is_active' => true,
    ]);

    $user2 = User::factory()->create([
      'role' => 'sps',
      'is_active' => true,
    ]);

    $stage = WorkflowStage::factory()->create([
      'assigned_role' => 'sps',
      'assigned_user_id' => $user2->id,
      'status' => 'pending',
    ]);

    expect($this->policy->complete($user1, $stage))->toBeFalse();
  });
});

describe('return method', function () {
  test('allows assigned user to return stage to previous', function () {
    $user = User::factory()->create([
      'role' => 'sps',
      'is_active' => true,
    ]);

    $stage = WorkflowStage::factory()->create([
      'assigned_role' => 'sps',
      'assigned_user_id' => $user->id,
      'status' => 'pending',
      'stage_order' => 2,
    ]);

    expect($this->policy->return($user, $stage))->toBeTrue();
  });

  test('denies inactive user from returning stage', function () {
    $user = User::factory()->create([
      'role' => 'sps',
      'is_active' => false,
    ]);

    $stage = WorkflowStage::factory()->create([
      'assigned_role' => 'sps',
      'assigned_user_id' => $user->id,
      'status' => 'pending',
      'stage_order' => 2,
    ]);

    expect($this->policy->return($user, $stage))->toBeFalse();
  });

  test('denies returning first stage', function () {
    $user = User::factory()->create([
      'role' => 'sps',
      'is_active' => true,
    ]);

    $stage = WorkflowStage::factory()->create([
      'assigned_role' => 'sps',
      'assigned_user_id' => $user->id,
      'status' => 'pending',
      'stage_order' => 1,
    ]);

    expect($this->policy->return($user, $stage))->toBeFalse();
  });

  test('denies returning completed stage', function () {
    $user = User::factory()->create([
      'role' => 'sps',
      'is_active' => true,
    ]);

    $stage = WorkflowStage::factory()->create([
      'assigned_role' => 'sps',
      'assigned_user_id' => $user->id,
      'status' => 'completed',
      'stage_order' => 2,
    ]);

    expect($this->policy->return($user, $stage))->toBeFalse();
  });
});

describe('addAttachment method', function () {
  test('allows assigned user to add attachment', function () {
    $user = User::factory()->create([
      'role' => 'sps',
      'is_active' => true,
    ]);

    $stage = WorkflowStage::factory()->create([
      'assigned_role' => 'sps',
      'assigned_user_id' => $user->id,
      'status' => 'pending',
    ]);

    expect($this->policy->addAttachment($user, $stage))->toBeTrue();
  });

  test('denies inactive user from adding attachment', function () {
    $user = User::factory()->create([
      'role' => 'sps',
      'is_active' => false,
    ]);

    $stage = WorkflowStage::factory()->create([
      'assigned_role' => 'sps',
      'assigned_user_id' => $user->id,
      'status' => 'pending',
    ]);

    expect($this->policy->addAttachment($user, $stage))->toBeFalse();
  });

  test('denies adding attachment to completed stage', function () {
    $user = User::factory()->create([
      'role' => 'sps',
      'is_active' => true,
    ]);

    $stage = WorkflowStage::factory()->create([
      'assigned_role' => 'sps',
      'assigned_user_id' => $user->id,
      'status' => 'completed',
    ]);

    expect($this->policy->addAttachment($user, $stage))->toBeFalse();
  });

  test('denies user with wrong role from adding attachment', function () {
    $user = User::factory()->create([
      'role' => 'vp_acad',
      'is_active' => true,
    ]);

    $stage = WorkflowStage::factory()->create([
      'assigned_role' => 'sps',
      'status' => 'pending',
    ]);

    expect($this->policy->addAttachment($user, $stage))->toBeFalse();
  });
});
