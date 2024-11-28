<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('projects.index');
    }

    // Ambil Data untuk DataTables
    public function getData(Request $request)
    {
        $projects = DB::table('projects')
            ->select('id', 'name', 'description', 'location', 'status', 'longitude', 'latitude', 'start_date', 'end_date');
        
        return DataTables::of($projects)
            ->editColumn('status', function ($project) {
                return ucfirst(str_replace('_', ' ', $project->status));
            })
            ->make(true);
    }
    
    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        // Validate the input using Validator (optional but recommended)
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string',
            'status' => 'required|in:planned,in_progress,completed',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
        ]);

        // If validation fails, return the error messages
        if ($validator->fails()) {
            return redirect()->route('projects.create')
                ->withErrors($validator)
                ->withInput();
        }

        // Insert project data using Query Builder
        DB::table('projects')->insert([
            'name' => $request->name,
            'description' => $request->description,
            'location' => $request->location,
            'status' => $request->status,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Redirect to the projects index page with a success message
        return redirect()->route('projects.index')->with('success', 'Project created successfully!');
    }

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
    // ProjectController.php

    public function edit(string $id)
    {
        // Fetch the project from the database
        $project = DB::table('projects')->where('id', $id)->first();

        // Check if the project exists
        if (!$project) {
            return redirect()->route('projects.index')->with('error', 'Project not found.');
        }

        // Pass the project data to the view
        return view('projects.edit', compact('project'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
{
    // Validate the input
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'description' => 'required|string',
        'status' => 'required|in:planned,in_progress,completed',
        'start_date' => 'required|date',
        'end_date' => 'required|date',
    ]);

    if ($validator->fails()) {
        return redirect()->route('projects.edit', $id)
            ->withErrors($validator)
            ->withInput();
    }

    // Update the project
    DB::table('projects')->where('id', $id)->update([
        'name' => $request->name,
        'description' => $request->description,
        'status' => $request->status,
        'start_date' => $request->start_date,
        'end_date' => $request->end_date,
        'updated_at' => now(),
    ]);

    return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Use Query Builder to find and delete the project
        $project = DB::table('projects')->where('id', $id)->first();
    
        if ($project) {
            // Use Query Builder to delete the project
            DB::table('projects')->where('id', $id)->delete();
    
            // Flash success message to session
            session()->flash('success', 'Project deleted successfully.');
    
            return response()->json(['success' => 'Project deleted successfully.']);
        } else {
            return response()->json(['error' => 'Project not found.'], 404);
        }
    }
    
    
}
