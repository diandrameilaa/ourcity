<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('reports.index');
    }

    // Ambil Data untuk DataTables
    public function getData(Request $request)
    {
        $reports = DB::table('reports')
            ->join('users', 'reports.user_id', '=', 'users.id')
            ->select('reports.id', 'users.name as user_name', 'reports.description', 'reports.photo', 'reports.location', 'reports.status', 'reports.longitude', 'reports.latitude', 'reports.created_at');
    
        return DataTables::of($reports)
            ->editColumn('status', function ($report) {
                return ucfirst(str_replace('_', ' ', $report->status));
            })
            ->editColumn('photo', function ($report) {
                if ($report->photo) {
                    return '<img src="' . asset("storage/reports/{$report->photo}") . '" alt="Report Photo" class="img-thumbnail" style="width: 100px;">';
                }
                return 'No Photo';
            })
            ->addColumn('action', function ($report) {
                return '<button class="btn btn-danger btn-sm delete-btn" data-id="' . $report->id . '">Delete</button>';
            })
            ->rawColumns(['photo', 'action'])
            ->make(true);
    }
    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('reports.create');
    }

    public function store(Request $request)
    {
        // Validate the incoming data
        $validator = Validator::make($request->all(), [
            'description' => 'required|string',
            'photo' => 'nullable|image|max:2048', // Adjust max size as needed
            'location' => 'required|string',
            'longitude' => 'required|string',
            'latitude' => 'required|string',
        ]);
    
        // If validation fails, redirect back with errors and input data
        if ($validator->fails()) {
            return redirect()->route('reports.create')
                ->withErrors($validator)
                ->withInput();
        }
    
        // Handle file upload

        if ($request->hasFile('photo')) {
            // Simpan file di public/storage/reports
            $file = $request->file('photo');
            $filename = time() . '_' . $file->getClientOriginalName(); // Generate nama file unik
            $file->move('storage/reports', $filename); // Pindahkan file

        }
    
        // Prepare data for insertion
        $data = [
            'user_id' => 1,
            'description' => $request->input('description'),
            'photo' => $filename,
            'location' => $request->input('location'),
            'status' => 'diajukan',
            'longitude' => $request->input('longitude'),
            'latitude' => $request->input('latitude'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    
        // Insert data into 'reports' table using Query Builder
        DB::table('reports')->insert($data);
    
        // Redirect to the reports index page with a success message
        return redirect()->route('reports.index')->with('success', 'Report created successfully!');
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
    public function edit($id)
    {
        // Retrieve the report data using Query Builder
        $report = DB::table('reports')->where('id', $id)->first();

        // Check if the report exists
        if (!$report) {
            return redirect()->route('reports.index')->with('error', 'Report not found!');
        }

        return view('reports.edit', compact('report'));
    }

    // Update the report data
    public function update(Request $request, $id)
    {
        // Validate the input fields
        $request->validate([
            'description' => 'required|string|max:255',
            'status' => 'required|in:diajukan,diproses,selesai',
        ]);

        // Update the report using Query Builder
        $updateData = [
            'description' => $request->input('description'),
            'status' => $request->input('status'),
        ];

        // Check if the report exists
        $report = DB::table('reports')->where('id', $id)->first();
        if (!$report) {
            return redirect()->route('reports.index')->with('error', 'Report not found!');
        }


        // Update the report
        DB::table('reports')->where('id', $id)->update($updateData);

        // Return back to the index page with a success message
        return redirect()->route('reports.index')->with('success', 'Report updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $report = DB::table('reports')->where('id', $id)->first();
    
        // if ($report) {
        //     // Delete the report's photo if it exists
        //     if ($report->photo) {
        //         $photoPath = storage_path('app/public/reports/' . $report->photo);
        //         if (file_exists($photoPath)) {
        //             unlink($photoPath);
        //         }
        //     }
    
            // Delete the report from the database
            DB::table('reports')->where('id', $id)->delete();
    
            return response()->json(['success' => true]);
        // }
    
        // return response()->json(['success' => false, 'message' => 'Report not found.'], 404);
    }
    
}
