<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    public function store(Request $request)
    {
        Enquiry::create($request->validate([
            'service_id' => ['nullable', 'exists:services,id'],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:160'],
            'phone' => ['required', 'string', 'max:20'],
            'subject' => ['nullable', 'string', 'max:180'],
            'message' => ['required', 'string', 'max:2000'],
            'source' => ['nullable', 'string', 'max:120'],
        ]));

        return back()->with('success', 'Thanks! Our party planner will contact you shortly.');
    }
}
