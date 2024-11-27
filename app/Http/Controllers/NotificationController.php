<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;  // Impor Mail
use App\Mail\NotificationMail;  // Impor NotificationMail dari namespace yang benar

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Fetch notifications using Query Builder
            $notifications = DB::table('notifications')->get();

            return DataTables::of($notifications)
                ->addColumn('status', function ($notification) {
                    return $notification->is_sent ? 'Sent' : 'Pending';
                })
                ->addColumn('action', function ($notification) {
                    return '<button type="button" class="btn btn-info btn-sm" onclick="viewNotification(' . $notification->id . ')">View</button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('emails.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Insert the new notification using Query Builder
        $notificationId = DB::table('notifications')->insertGetId([
            'title' => $request->title,
            'message' => $request->message,
            'is_sent' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Fetch users with 'warga' role
        $users = DB::table('users')->where('role', 'warga')->get();

        // Send email to users with 'warga' role
        foreach ($users as $user) {
            Mail::to($user->email)->send(new NotificationMail($notificationId));  // Kirim email menggunakan NotificationMail
        }

        // Update notification as sent
        DB::table('notifications')->where('id', $notificationId)->update(['is_sent' => true]);

        return redirect()->route('notifications.index')->with('success', 'Notification sent to all users.');
    }
}
