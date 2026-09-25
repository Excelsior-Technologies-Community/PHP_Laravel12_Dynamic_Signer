<?php

namespace App\Http\Controllers;

use App\Models\SignedUrl;
use App\Models\SignedUrlAccess;
use App\Services\SignedUrlService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SignedUrlController extends Controller
{
    /**
     * Signed URL generator form.
     */
    public function form()
    {
        return view('signed-url');
    }

    /**
     * Generate a new signed URL.
     */
    public function generate(
        Request $request,
        SignedUrlService $signer
    ) {
        $validated = $request->validate([
            'target_url' => [
                'required',
                'url',
                'max:2048',
            ],

            'name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'expires_in' => [
                'required',
                'integer',
                'min:1',
                'max:60',
            ],

            'max_accesses' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'one_time' => [
                'nullable',
                'boolean',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | One-Time URL
        |--------------------------------------------------------------------------
        |
        | A one-time URL always allows exactly one access.
        |
        */

        $oneTime = $request->boolean('one_time');

        $maxAccesses = $oneTime
            ? 1
            : ($validated['max_accesses'] ?? null);

        /*
        |--------------------------------------------------------------------------
        | Create Signed URL
        |--------------------------------------------------------------------------
        */

        $result = $signer->create(
            $validated['target_url'],
            (int) $validated['expires_in'],
            [],
            [
                'name' => $validated['name'] ?? null,
                'max_accesses' => $maxAccesses,
                'one_time' => $oneTime,
                'notes' => $validated['notes'] ?? null,
                'created_ip' => $request->ip(),
            ]
        );

        return redirect()
            ->route('home')
            ->with('success', 'Signed URL generated successfully.')
            ->with('signedUrlResult', $result);
    }

    /**
     * Secure page accessed through signed URL.
     */
    public function secure(Request $request)
    {
        return view('secure-page');
    }

    /**
     * Analytics dashboard.
     */
 /**
 * Analytics dashboard.
 */
public function analytics()
{
    $now = now();

    /*
    |--------------------------------------------------------------------------
    | Total Signed URLs
    |--------------------------------------------------------------------------
    */

    $totalUrls = SignedUrl::count();

    /*
    |--------------------------------------------------------------------------
    | Active URLs
    |--------------------------------------------------------------------------
    */

    $activeUrls = SignedUrl::query()
        ->whereNull('revoked_at')
        ->where('expires_at', '>', $now)
        ->where(function ($query) {
            $query->whereNull('max_accesses')
                ->orWhere(function ($q) {
                    $q->where('one_time', false)
                        ->whereColumn(
                            'access_count',
                            '<',
                            'max_accesses'
                        );
                });
        })
        ->where(function ($query) {
            $query->where('one_time', false)
                ->orWhere('access_count', '<', 1);
        })
        ->count();

    /*
    |--------------------------------------------------------------------------
    | Expired URLs
    |--------------------------------------------------------------------------
    */

    $expiredUrls = SignedUrl::query()
        ->whereNull('revoked_at')
        ->where('expires_at', '<=', $now)
        ->count();

    /*
    |--------------------------------------------------------------------------
    | Revoked URLs
    |--------------------------------------------------------------------------
    */

    $revokedUrls = SignedUrl::query()
        ->whereNotNull('revoked_at')
        ->count();

    /*
    |--------------------------------------------------------------------------
    | Access Limit Reached
    |--------------------------------------------------------------------------
    */

    $limitReachedUrls = SignedUrl::query()
        ->whereNull('revoked_at')
        ->where('expires_at', '>', $now)
        ->where(function ($query) {
            $query->where(function ($q) {
                // One-time URL already used
                $q->where('one_time', true)
                    ->where(
                        'access_count',
                        '>=',
                        1
                    );
            })
            ->orWhere(function ($q) {
                // Normal URL reached max accesses
                $q->where('one_time', false)
                    ->whereNotNull('max_accesses')
                    ->whereColumn(
                        'access_count',
                        '>=',
                        'max_accesses'
                    );
            });
        })
        ->count();

    /*
    |--------------------------------------------------------------------------
    | One-Time URLs
    |--------------------------------------------------------------------------
    */

    $oneTimeUrls = SignedUrl::query()
        ->where('one_time', true)
        ->count();

    /*
    |--------------------------------------------------------------------------
    | Used One-Time URLs
    |--------------------------------------------------------------------------
    */

    $usedOneTimeUrls = SignedUrl::query()
        ->where('one_time', true)
        ->where('access_count', '>=', 1)
        ->count();

    /*
    |--------------------------------------------------------------------------
    | Total Accesses
    |--------------------------------------------------------------------------
    */

    $totalAccesses = SignedUrlAccess::count();

    /*
    |--------------------------------------------------------------------------
    | Today's Accesses
    |--------------------------------------------------------------------------
    */

    $todayAccesses = SignedUrlAccess::query()
        ->whereDate(
            'accessed_at',
            today()
        )
        ->count();

    /*
    |--------------------------------------------------------------------------
    | Recent Signed URLs
    |--------------------------------------------------------------------------
    */

    $recentUrls = SignedUrl::query()
        ->latest()
        ->take(5)
        ->get();

    /*
    |--------------------------------------------------------------------------
    | Recent Access Activity
    |--------------------------------------------------------------------------
    */

    $recentAccesses = SignedUrlAccess::query()
        ->with('signedUrl')
        ->latest('accessed_at')
        ->take(5)
        ->get();

    /*
    |--------------------------------------------------------------------------
    | Return Analytics View
    |--------------------------------------------------------------------------
    */

    return view('analytics', compact(
        'totalUrls',
        'activeUrls',
        'expiredUrls',
        'revokedUrls',
        'limitReachedUrls',
        'oneTimeUrls',
        'usedOneTimeUrls',
        'totalAccesses',
        'todayAccesses',
        'recentUrls',
        'recentAccesses'
    ));
}

    /**
     * Signed URL history.
     */
    public function history(Request $request)
    {
        $query = $this->buildHistoryQuery($request);

        $signedUrls = $query
            ->paginate(5)
            ->withQueryString();

        return view(
            'history',
            compact('signedUrls')
        );
    }

    /**
     * Build signed URL history query.
     */
    protected function buildHistoryQuery(Request $request)
    {
        $query = SignedUrl::query();

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere(
                        'target_url',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'signed_url',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'signature',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Status filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {
            $status = $request->status;
            $now = now();

            if ($status === 'active') {
                $query
                    ->whereNull('revoked_at')
                    ->where('expires_at', '>', $now)
                    ->where(function ($q) {
                        $q->whereNull('max_accesses')
                            ->orWhere(function ($q2) {
                                $q2->where('one_time', false)
                                    ->whereColumn(
                                        'access_count',
                                        '<',
                                        'max_accesses'
                                    );
                            });
                    })
                    ->where(function ($q) {
                        $q->where('one_time', false)
                            ->orWhere('access_count', '<', 1);
                    });
            }

            if ($status === 'expired') {
                $query
                    ->whereNull('revoked_at')
                    ->where(
                        'expires_at',
                        '<=',
                        $now
                    );
            }

            if ($status === 'revoked') {
                $query->whereNotNull('revoked_at');
            }

            if (
                $status === 'limit' ||
                $status === 'limit_reached'
            ) {
                $query
                    ->whereNull('revoked_at')
                    ->where('expires_at', '>', $now)
                    ->where(function ($q) {
                        $q->where(function ($q2) {
                            $q2->where(
                                'one_time',
                                true
                            )
                            ->where(
                                'access_count',
                                '>=',
                                1
                            );
                        })
                        ->orWhere(function ($q2) {
                            $q2->where(
                                'one_time',
                                false
                            )
                            ->whereNotNull(
                                'max_accesses'
                            )
                            ->whereColumn(
                                'access_count',
                                '>=',
                                'max_accesses'
                            );
                        });
                    });
            }

            if ($status === 'one_time') {
                $query->where('one_time', true);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Date filters
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date_from')) {
            $query->whereDate(
                'created_at',
                '>=',
                $request->date_from
            );
        }

        if ($request->filled('date_to')) {
            $query->whereDate(
                'created_at',
                '<=',
                $request->date_to
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        $sort = $request->get('sort', 'oldest');

        switch ($sort) {
            case 'oldest':
                $query->orderBy(
                    'created_at',
                    'asc'
                );
                break;

            case 'most_accessed':
                $query->orderBy(
                    'access_count',
                    'desc'
                );
                break;

            case 'least_accessed':
                $query->orderBy(
                    'access_count',
                    'asc'
                );
                break;

            case 'expiry':
                $query->orderBy(
                    'expires_at',
                    'asc'
                );
                break;

            case 'latest':
            case 'newest':
            default:
                $query->orderBy(
                    'created_at',
                    'desc'
                );
                break;
        }

        return $query;
    }

    /**
     * Revoke one signed URL.
     */
    public function revoke(
        SignedUrl $signedUrl
    ) {
        if (!$signedUrl->revoked_at) {
            $signedUrl->update([
                'revoked_at' => now(),
            ]);
        }

        return redirect()
            ->route('signed-url.history')
            ->with(
                'success',
                'Signed URL revoked successfully.'
            );
    }

    /**
     * Bulk revoke signed URLs.
     */
    public function bulkRevoke(
        Request $request
    ) {
        $validated = $request->validate([
            'ids' => [
                'required',
                'array',
                'min:1',
            ],

            'ids.*' => [
                'integer',
                'exists:signed_urls,id',
            ],
        ]);

        $count = SignedUrl::query()
            ->whereIn('id', $validated['ids'])
            ->whereNull('revoked_at')
            ->update([
                'revoked_at' => now(),
            ]);

        return redirect()
            ->route('signed-url.history')
            ->with(
                'success',
                $count . ' signed URL(s) revoked successfully.'
            );
    }

    /**
     * Export signed URL history as CSV.
     */
    public function export(
        Request $request
    ): StreamedResponse {
        $signedUrls = $this
            ->buildHistoryQuery($request)
            ->get();

        $filename =
            'signed-url-history-' .
            now()->format('Y-m-d-H-i-s') .
            '.csv';

        return response()->streamDownload(
            function () use ($signedUrls) {

                $handle = fopen(
                    'php://output',
                    'w'
                );

                fputcsv($handle, [
                    'ID',
                    'Name',
                    'Target URL',
                    'Signature',
                    'Expires At',
                    'Max Accesses',
                    'One Time',
                    'Access Count',
                    'Remaining Accesses',
                    'Status',
                    'Revoked At',
                    'Created At',
                ]);

                foreach ($signedUrls as $signedUrl) {
                    fputcsv($handle, [
                        $signedUrl->id,
                        $signedUrl->name,
                        $signedUrl->target_url,
                        $signedUrl->signature,
                        optional(
                            $signedUrl->expires_at
                        )->format(
                            'Y-m-d H:i:s'
                        ),
                        $signedUrl->max_accesses,
                        $signedUrl->one_time
                            ? 'Yes'
                            : 'No',
                        $signedUrl->access_count,
                        $signedUrl->remainingAccesses()
                            ?? 'Unlimited',
                        $signedUrl->status,
                        optional(
                            $signedUrl->revoked_at
                        )->format(
                            'Y-m-d H:i:s'
                        ),
                        optional(
                            $signedUrl->created_at
                        )->format(
                            'Y-m-d H:i:s'
                        ),
                    ]);
                }

                fclose($handle);
            },
            $filename,
            [
                'Content-Type' =>
                    'text/csv; charset=UTF-8',
            ]
        );
    }

    /**
     * Access history for one signed URL.
     */
    public function accessHistory(
        SignedUrl $signedUrl
    ) {
        /*
        |--------------------------------------------------------------------------
        | Access records
        |--------------------------------------------------------------------------
        |
        | Paginate the access records so the Blade file can use:
        |
        | $accesses->currentPage()
        | $accesses->lastPage()
        | $accesses->url($page)
        |
        */

        $accesses = $signedUrl
            ->accesses()
            ->latest('accessed_at')
            ->paginate(10);

        /*
        |--------------------------------------------------------------------------
        | Unique IP addresses
        |--------------------------------------------------------------------------
        */

        $uniqueIps = $signedUrl
            ->accesses()
            ->whereNotNull('ip_address')
            ->where('ip_address', '!=', '')
            ->distinct('ip_address')
            ->count('ip_address');

        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */

        return view(
            'access-history',
            compact(
                'signedUrl',
                'accesses',
                'uniqueIps'
            )
        );
    }
}