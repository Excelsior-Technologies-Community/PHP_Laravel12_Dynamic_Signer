<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SignedUrlService;

class SignedUrlController extends Controller
{
    // Show the URL generator form page
    public function form()
    {
        return view('generate'); // Load generate blade view
    }

    // Handle form submission and generate signed URL
    public function generate(Request $request, SignedUrlService $signer)
    {
        $request->validate([
            'url' => 'required|string'
        ]);

        // Create signed URL valid for 5 minutes
        $data = $signer->create($request->url, 5);

        // Return view with signed URL and expiry timestamp
        return view('generate', [
            'signedUrl' => $data['url'],
            'expiresAt' => $data['expires']
        ]);
    }

    // Secure page accessible only via signed URL
    public function secure()
    {
        return view('secure'); 
    }
}