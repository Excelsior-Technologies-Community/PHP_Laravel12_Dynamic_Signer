<?php

namespace App\Http\Controllers;

use App\Models\SignedUrl;
use App\Models\SignedUrlAccess;
use App\Services\SignedUrlService;
use Illuminate\Http\Request;

class SignedUrlController extends Controller
{
    /**
     * Show the URL generator form.
     */
    public function form()
    {
        return view('generate');
    }

    /**
     * Generate a signed URL.
     */
    public function generate(
        Request $request,
        SignedUrlService $signer
    ) {
        $request->validate([
            'url' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);

        $data = $signer->create(
            $request->url,
            5
        );

        return view('generate', [
            'signedUrl' => $data['url'],
            'expiresAt' => $data['expires'],
        ]);
    }

    /**
     * Secure page.
     *
     * Access is allowed only when the signed URL
     * passes the signature and revocation checks.
     */
    public function secure()
    {
        return view('secure');
    }

    /**
     * Analytics dashboard.
     */
    public function analytics()
    {
        $totalUrls = SignedUrl::count();

        $activeUrls = SignedUrl::whereNull('revoked_at')
            ->where('expires_at', '>', now())
            ->count();

        $expiredUrls = SignedUrl::whereNull('revoked_at')
            ->where('expires_at', '<=', now())
            ->count();

        $revokedUrls = SignedUrl::whereNotNull('revoked_at')
            ->count();

        $totalAccesses = SignedUrlAccess::count();

        $todayAccesses = SignedUrlAccess::whereDate(
            'accessed_at',
            today()
        )->count();

        $recentUrls = SignedUrl::latest()
            ->take(10)
            ->get();

        $recentAccesses = SignedUrlAccess::with('signedUrl')
            ->latest('accessed_at')
            ->take(10)
            ->get();

        return view('analytics', compact(
            'totalUrls',
            'activeUrls',
            'expiredUrls',
            'revokedUrls',
            'totalAccesses',
            'todayAccesses',
            'recentUrls',
            'recentAccesses'
        ));
    }

    /**
     * Signed URL history with search,
     * status filtering and pagination.
     */
    public function history(Request $request)
    {
        $query = SignedUrl::query();

        // Search target URL or signed URL.
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('target_url', 'like', "%{$search}%")
                    ->orWhere('signed_url', 'like', "%{$search}%")
                    ->orWhere('signature', 'like', "%{$search}%");
            });
        }

        // Status filter.
        if ($request->status === 'active') {
            $query->whereNull('revoked_at')
                ->where('expires_at', '>', now());
        }

        if ($request->status === 'expired') {
            $query->whereNull('revoked_at')
                ->where('expires_at', '<=', now());
        }

        if ($request->status === 'revoked') {
            $query->whereNotNull('revoked_at');
        }

        $signedUrls = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('history', compact('signedUrls'));
    }

    /**
     * Revoke a signed URL.
     */
    public function revoke(SignedUrl $signedUrl)
    {
        if ($signedUrl->isRevoked()) {
            return back()->with(
                'error',
                'This signed URL is already revoked.'
            );
        }

        $signedUrl->update([
            'revoked_at' => now(),
        ]);

        return back()->with(
            'success',
            'Signed URL revoked successfully.'
        );
    }

    /**
     * Display access history for a signed URL.
     */
    public function accessHistory(SignedUrl $signedUrl)
    {
        $accesses = $signedUrl->accesses()
            ->latest('accessed_at')
            ->paginate(15);

        return view(
            'access-history',
            compact('signedUrl', 'accesses')
        );
    }
}