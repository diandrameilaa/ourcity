<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class DiscussionController extends Controller
{
     // Halaman forum diskusi
     public function index()
     {
         $discussions = DB::table('discussions')
             ->join('users', 'discussions.user_id', '=', 'users.id')
             ->select('discussions.id', 'discussions.user_id', 'users.name', 'discussions.message', 'discussions.created_at', 'discussions.is_deleted')
             ->where('discussions.is_deleted', false)
             ->distinct() // Menghindari data duplikat
             ->orderBy('discussions.created_at', 'asc') // Mengurutkan secara ascending
             ->get();
     
         return view('discussions', compact('discussions'));
     }
     
     
 
     // Simpan pesan diskusi baru
     public function store(Request $request)
     {
         $request->validate([
             'message' => 'required|string|max:500',
         ]);
 
         DB::table('discussions')->insert([
             'user_id' => Auth::id(),
             'message' => $request->message,
             'created_at' => now(),
             'updated_at' => now(),
         ]);
 
         return redirect()->route('discussions.index')->with('success', 'Message posted successfully!');
     }
 
     // Hapus pesan diskusi
     public function destroy($id)
     {
         DB::table('discussions')->where('id', $id)->update(['is_deleted' => true]);
 
         return redirect()->route('discussions.index')->with('success', 'Message deleted successfully!');
     }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
}
