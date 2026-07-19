<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $notifications = $user->notifications()
            ->where('notifiable_type', $user->getMorphClass())
            ->where('notifiable_id', $user->getKey())
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('notifications.index', compact('notifications', 'user'));
    }

    public function unread(): JsonResponse
    {
        $notifications = Auth::user()->unreadNotifications()
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn ($notification) => [
                'id' => $notification->id,
                'title' => $notification->data['title'] ?? 'Notification',
                'message' => $notification->data['message'] ?? '',
            ])
            ->values();

        return response()->json([
            'count' => Auth::user()->unreadNotifications()->count(),
            'notifications' => $notifications,
        ]);
    }

    public function read(string $id): RedirectResponse
    {
        $notification = Auth::user()->notifications()->whereKey($id)->firstOrFail();
        $notification->markAsRead();

        $url = $notification->data['url'] ?? null;

        if ($url && Auth::user()->role === 'admin') {
            $url = str_replace(
                ['/comptable/aliments', '/comptable/oeufs', '/comptable/matieres-premieres', '/comptable/poulets'],
                ['/admin/aliments', '/admin/oeufs', '/admin/matieres-premieres', '/admin/poulets'],
                $url
            );
        } elseif ($url && Auth::user()->role === 'comptable') {
            $url = str_replace(
                ['/admin/aliments', '/admin/oeufs', '/admin/matieres-premieres', '/admin/poulets'],
                ['/comptable/aliments', '/comptable/oeufs', '/comptable/matieres-premieres', '/comptable/poulets'],
                $url
            );
        }

        return $url ? redirect($url) : back();
    }

    public function readAll(): RedirectResponse
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }

    public function destroy(string $id): RedirectResponse
    {
        Auth::user()->notifications()->whereKey($id)->delete();

        return back();
    }
}
