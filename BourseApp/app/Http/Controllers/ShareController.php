<?php

namespace App\Http\Controllers;

use App\Share;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShareController extends Controller
{
    
    public function index()
    {
        $shares = Share::all()->sortBy('name');
        foreach($shares as $share) {
            if ($share->type == "share") $share->type = "Action";
            if ($share->type == "tracker") $share->type = "Tracker";
            if ($share->type == "fund") $share->type = "FCP";
        }
        return view('share.index', compact('shares'));
    }

    
    public function create()
    {
        return view('share.create');
    }

    
    public function store(Request $request)
    {
        $share = Share::create($this->validateNewShare());
        return redirect()->route('shareIndex')->with('success', 'action créée');
    }

    
    public function show(Share $share)
    {
        return view('share.show', compact('share'));
    }

    
    public function edit(Share $share)
    {
        return view('share.create', compact('share'));
    }

    
    public function update(Request $request, Share $share)
    {
        $share->update($this->validateUpdateShare($share));
        return redirect()->route('shareIndex')->with('success', 'action mise à jour');
    }

    
    public function destroy(Share $share)
    {
        $share->delete();
        return redirect()->route('shareIndex')->with('success', 'action supprimée');
    }

    public function validateNewShare()
    {
        $validatedAttributes = request()->validate([
            'name' => 'required|unique:shares,name',
            'codeISIN' => 'required|unique:shares,codeISIN',
            'type' => 'required',
        ]);

        return $validatedAttributes;
    }

    public function validateUpdateShare(Share $share)
    {
        $validatedAttributes = request()->validate([
            'name' => ['required', Rule::unique('shares')->ignore($share->id),],
            'codeISIN' => ['required', Rule::unique('shares')->ignore($share->id),],
            'type' => 'required',
        ]);

        return $validatedAttributes;
    }
}
