<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Services\ClockingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;
use Inertia\Inertia;
use Inertia\Response;

class FormController extends Controller
{
    protected ClockingService $clocking;

    public function __construct(ClockingService $clocking)
    {
        $this->clocking = $clocking;
    }

    /**
     * Display the form creation page
     */
    public function create(): Response
    {
        return Inertia::render('Forms/Create');
    }

    /**
     * Store a new form submission
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate form input data
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'file' => ['nullable', File::types(['xlsx','xls','csv'])->max(10240)], // 10MB max
        ]);

        // Handle file upload if present
        $filePath = null;
        $originalName = null;
        
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $filePath = $file->store('form-uploads', 'public');
        }

        // Create form record in database
        $form = Form::create([
            'name' => $validated['name'],
            'date' => $validated['date'],
            'file_path' => $filePath,
            'file_original_name' => $originalName,
            'user_id' => $request->user()->id,
        ]);

        // Get the auto-created ID
        $formId = $form->id;

        // create the clocking entries
        $this->clocking->index(public_path('storage/' . $filePath), $formId);

        return redirect()->route('forms.index')->with('success', 'Form submitted successfully!');
    }

    /**
     * Display the form edit page
     */
    public function edit(Form $form): Response
    {
        // Ensure user can only edit their own forms
        if ($form->user_id !== auth()->id()) {
            abort(403);
        }

        return Inertia::render('Forms/Edit', [
            'form' => $form,
        ]);
    }

    /**
     * Update an existing form submission
     */
    public function update(Request $request, Form $form): RedirectResponse
    {
        // Ensure user can only update their own forms
        if ($form->user_id !== auth()->id()) {
            abort(403);
        }

        // Validate form input data
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'file' => ['nullable', File::types(['xlsx','xls','csv'])->max(10240)], // 10MB max
        ]);

        // Handle file upload if present
        $filePath = $form->file_path; // Keep existing file by default
        $originalName = $form->file_original_name;
        
        if ($request->hasFile('file')) {
            // Delete old file if it exists
            if ($form->file_path) {
                Storage::disk('public')->delete($form->file_path);
            }
            
            // Store new file
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $filePath = $file->store('form-uploads', 'public');
        }

        // Update form record in database
        $form->update([
            'name' => $validated['name'],
            'date' => $validated['date'],
            'file_path' => $filePath,
            'file_original_name' => $originalName,
        ]);

        // If file was updated, recreate clocking entries
        if ($request->hasFile('file')) {
            $this->clocking->index(public_path('storage/' . $filePath), $form->id);
        }

        return redirect()->route('forms.index')->with('success', 'Form updated successfully!');
    }

    /**
     * Display all form submissions
     */
    public function index(): Response
    {
        // Get all forms for the authenticated user
        $forms = Form::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return Inertia::render('Forms/Index', [
            'forms' => $forms,
        ]);
    }

    /**
     * Delete a form submission
     */
    public function destroy(Form $form): RedirectResponse
    {
        // Ensure user can only delete their own forms
        if ($form->user_id !== auth()->id()) {
            abort(403);
        }

        // Delete file from storage if exists
        if ($form->file_path) {
            Storage::disk('public')->delete($form->file_path);
        }

        // Delete form record
        $form->delete();

        return redirect()->route('forms.index')->with('success', 'Form deleted successfully!');
    }
}
