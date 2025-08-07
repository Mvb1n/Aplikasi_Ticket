<?php

namespace App\Http\Controllers;

use App\Models\Problem;
use App\Models\Incident;
use Illuminate\Http\Request;

class ProblemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $problems = Problem::withCount('incidents')->latest()->paginate(10);
        return view('problems.index', compact('problems'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $incidents = Incident::where('status', '!=', 'Closed')->get();
        return view('problems.create', compact('incidents'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'incident_ids' => 'required|array',
            'incident_ids.*' => 'exists:incidents,id',
        ]);

        $problem = Problem::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
        ]);

        $problem->incidents()->attach($validatedData['incident_ids']);

        return redirect()->route('problems.index')->with('success', 'Tiket Problem berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Problem $problem)
    {
        $problem->load('incidents.user');
        return view('problems.show', compact('problem'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Problem $problem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Problem $problem)
    {
        $validatedData = $request->validate([
            'root_cause_analysis' => 'nullable|string',
            'permanent_solution' => 'nullable|string',
            'status' => 'required|in:Analysis,Solution Implemented,Closed',
        ]);

        $problem->update($validatedData);

        return redirect()->route('problems.show', $problem->id)->with('success', 'Detail problem berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Problem $problem)
    {
        //
    }
}