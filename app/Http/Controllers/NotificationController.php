<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
  /**
   * Display a listing of user notifications.
   *
   * @param Request $request
   * @return Response
   */
  public function index(Request $request): Response
  {
    /** @var \App\Models\User $user */
    $user = Auth::user();

    // Get all notifications for the user, paginated
    $notifications = $user->notifications()
      ->orderBy('created_at', 'desc')
      ->paginate(20);

    // Get unread count
    $unreadCount = $user->unreadNotifications()->count();

    return Inertia::render('Notifications/Index', [
      'notifications' => $notifications->through(function ($notification) {
        return [
          'id' => $notification->id,
          'type' => $notification->type,
          'data' => $notification->data,
          'read_at' => $notification->read_at,
          'created_at' => $notification->created_at,
        ];
      }),
      'unread_count' => $unreadCount,
    ]);
  }

  /**
   * Mark a single notification as read.
   *
   * @param Request $request
   * @param string $id
   * @return JsonResponse
   */
  public function markAsRead(Request $request, string $id): JsonResponse
  {
    /** @var \App\Models\User $user */
    $user = Auth::user();

    $notification = $user->notifications()->find($id);

    if (!$notification) {
      return response()->json([
        'success' => false,
        'message' => 'Notification not found',
      ], 404);
    }

    $notification->markAsRead();

    return response()->json([
      'success' => true,
      'message' => 'Notification marked as read',
    ]);
  }

  /**
   * Mark all notifications as read for the authenticated user.
   *
   * @param Request $request
   * @return JsonResponse
   */
  public function markAllAsRead(Request $request): JsonResponse
  {
    /** @var \App\Models\User $user */
    $user = Auth::user();

    $user->unreadNotifications->markAsRead();

    return response()->json([
      'success' => true,
      'message' => 'All notifications marked as read',
    ]);
  }

  /**
   * Delete a single notification.
   *
   * @param string $id
   * @return JsonResponse
   */
  public function destroy(string $id): JsonResponse
  {
    /** @var \App\Models\User $user */
    $user = Auth::user();

    $notification = $user->notifications()->find($id);

    if (!$notification) {
      return response()->json([
        'success' => false,
        'message' => 'Notification not found',
      ], 404);
    }

    $notification->delete();

    return response()->json([
      'success' => true,
      'message' => 'Notification deleted',
    ]);
  }

  /**
   * Delete all read notifications for the authenticated user.
   *
   * @return JsonResponse
   */
  public function deleteAllRead(): JsonResponse
  {
    /** @var \App\Models\User $user */
    $user = Auth::user();

    $user->readNotifications()->delete();

    return response()->json([
      'success' => true,
      'message' => 'All read notifications deleted',
    ]);
  }
}
