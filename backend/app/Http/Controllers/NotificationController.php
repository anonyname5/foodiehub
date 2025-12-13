<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get all notifications for the authenticated user
     */
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(20);
        $unreadCount = Auth::user()->unreadNotifications()->count();
        
        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Get unread notifications count (for AJAX)
     */
    public function unreadCount()
    {
        $count = Auth::user()->unreadNotifications()->count();
        return response()->json(['count' => $count]);
    }

    /**
     * Get recent notifications (for dropdown)
     */
    public function recent()
    {
        $notifications = Auth::user()->notifications()->take(10)->get();
        
        // Format notifications for JSON response
        $formatted = $notifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'type' => $notification->data['type'] ?? 'general',
                'message' => $notification->data['message'] ?? 'New notification',
                'url' => $notification->data['url'] ?? null,
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at->toIso8601String(),
                'created_at_human' => $notification->created_at->diffForHumans(),
            ];
        });
        
        return response()->json([
            'notifications' => $formatted,
            'unread_count' => Auth::user()->unreadNotifications()->count()
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = Auth::user()->notifications()->find($id);
        
        if ($notification) {
            $notification->markAsRead();
            
            if ($request->expectsJson()) {
                return response()->json(['success' => true]);
            }
            
            return redirect()->route('notifications.index')
                ->with('success', 'Notification marked as read');
        }
        
        if ($request->expectsJson()) {
            return response()->json(['success' => false], 404);
        }
        
        return redirect()->route('notifications.index')
            ->with('error', 'Notification not found');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        Auth::user()->unreadNotifications->markAsRead();
        
        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->route('notifications.index')
            ->with('success', 'All notifications marked as read');
    }
}
