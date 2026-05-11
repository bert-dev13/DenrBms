<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use App\Models\ProtectedArea;
use App\Models\User;
use App\Support\UserAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        [$sortColumn, $sortDirection] = $this->resolveSort($request);

        $users = $this->filteredUsersQuery($request)
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(20)
            ->withQueryString();

        $protectedAreas = ProtectedArea::query()->orderBy('name')->get(['id', 'name']);

        return view('pages.users.index', compact('users', 'protectedAreas'));
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        [$sortColumn, $sortDirection] = $this->resolveSort($request);
        $users = $this->filteredUsersQuery($request)
            ->orderBy($sortColumn, $sortDirection)
            ->get();

        $filename = 'users-'.date('Y-m-d-H-i-s').'.csv';
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($users): void {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, ['Name', 'Email', 'Username', 'Role', 'Protected Area', 'Status', 'Created At']);

            foreach ($users as $user) {
                fputcsv($file, [
                    $user->name,
                    $user->email,
                    $user->username,
                    $user->role === UserAccess::ROLE_ADMIN ? 'Administrator' : 'Protected Area User',
                    $user->protectedArea?->name ?? '',
                    $user->is_active ? 'Active' : 'Inactive',
                    optional($user->created_at)?->format('Y-m-d H:i:s') ?? '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        [$sortColumn, $sortDirection] = $this->resolveSort($request);
        $users = $this->filteredUsersQuery($request)
            ->orderBy($sortColumn, $sortDirection)
            ->get();

        $pdf = Pdf::setOptions([
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
        ])->loadView('pages.users.export.pdf', [
            'users' => $users,
            'exportedAt' => now(),
        ]);

        return $pdf->download('users-'.date('Y-m-d-H-i-s').'.pdf');
    }

    public function exportPrint(Request $request): View
    {
        [$sortColumn, $sortDirection] = $this->resolveSort($request);
        $users = $this->filteredUsersQuery($request)
            ->orderBy($sortColumn, $sortDirection)
            ->get();

        return view('pages.users.export.print', [
            'users' => $users,
            'printedAt' => now(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePayload($request);
        $validated['is_active'] = $request->boolean('is_active', true);

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'User account created successfully.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $this->validatePayload($request, $user);
        if ($request->filled('password')) {
            $validated['password'] = $request->string('password')->toString();
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->boolean('is_active');
        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'User account updated successfully.');
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        if (Auth::id() === $user->id) {
            return redirect()->route('users.index')->with('error', 'You cannot deactivate your own account.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        return redirect()->route('users.index')->with('success', 'User account status updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if (Auth::id() === $user->id) {
            return redirect()->route('users.index')->with('error', 'You cannot delete your own account.');
        }

        $user->forceDelete();

        return redirect()->route('users.index')->with('success', 'User account deleted successfully.');
    }

    private function validatePayload(Request $request, ?User $user = null): array
    {
        $userId = $user?->id;

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'username' => ['required', 'string', 'max:100', Rule::unique('users', 'username')->ignore($userId)],
            'role' => ['required', Rule::in([UserAccess::ROLE_ADMIN, UserAccess::ROLE_PA_USER])],
            'protected_area_id' => ['nullable', 'exists:protected_areas,id'],
            'password' => $user
                ? ['nullable', 'string', 'min:8']
                : ['required', 'string', 'min:8'],
        ];

        $validated = $request->validate($rules, [
            'protected_area_id.required' => 'Protected Area is required for PA User role.',
        ]);

        if (($validated['role'] ?? null) === UserAccess::ROLE_PA_USER && empty($validated['protected_area_id'])) {
            $request->validate([
                'protected_area_id' => ['required', 'exists:protected_areas,id'],
            ]);
        }

        if (($validated['role'] ?? null) === UserAccess::ROLE_ADMIN) {
            $validated['protected_area_id'] = null;
        }

        return $validated;
    }

    private function filteredUsersQuery(Request $request): Builder
    {
        return User::query()
            ->with('protectedArea')
            ->when($request->filled('search'), function ($builder) use ($request) {
                $search = trim((string) $request->query('search'));
                $builder->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('role'), fn ($builder) => $builder->where('role', $request->query('role')))
            ->when($request->filled('status'), function ($builder) use ($request) {
                $isActive = $request->query('status') === 'active';
                $builder->where('is_active', $isActive);
            })
            ->when($request->filled('protected_area_id'), fn ($builder) => $builder->where('protected_area_id', $request->integer('protected_area_id')));
    }

    /**
     * @return array{0:string,1:'asc'|'desc'}
     */
    private function resolveSort(Request $request): array
    {
        $sortBy = $request->query('sort_by', 'name');
        $sortDirection = $request->query('sort_direction', 'asc') === 'desc' ? 'desc' : 'asc';
        $sortColumn = in_array($sortBy, ['name', 'email', 'username', 'role', 'is_active', 'created_at'], true) ? $sortBy : 'name';

        return [$sortColumn, $sortDirection];
    }
}
