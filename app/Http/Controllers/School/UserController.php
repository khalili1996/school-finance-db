<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * لیست کاربران همان مکتب
     */
    public function index()
    {
        $schoolId = session('active_school_id', auth()->user()->school_id);

        $users = User::where('school_id', $schoolId)
            ->with('roles')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('school.users.index', compact('users'));
    }

    /**
     * فرم ایجاد کاربر جدید
     */
    public function create()
    {
        // فقط نقش‌های غیر از Super Admin قابل انتخاب هستند
        $roles = Role::where('name', '!=', 'Super Admin')->get();

        return view('school.users.create', compact('roles'));
    }

    /**
     * ذخیره‌ی کاربر جدید
     */
    public function store(Request $request)
    {
        $schoolId = session('active_school_id', auth()->user()->school_id);

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'phone'    => 'nullable|string|max:20',
            'role'     => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'school_id' => $schoolId,
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'phone'     => $data['phone'] ?? null,
            'is_active' => true,
        ]);

        $user->assignRole($data['role']);

        return redirect()->route('school.users.index')
            ->with('success', 'کاربر جدید با موفقیت ثبت شد.');
    }

    /**
     * فرم ویرایش کاربر
     */
    public function edit(User $user)
    {
        $schoolId = session('active_school_id', auth()->user()->school_id);
        if ($user->school_id !== $schoolId) {
            abort(403, 'دسترسی غیرمجاز');
        }

        $roles = Role::where('name', '!=', 'Super Admin')->get();

        return view('school.users.edit', compact('user', 'roles'));
    }

    /**
     * به‌روزرسانی کاربر
     */
    public function update(Request $request, User $user)
    {
        $schoolId = session('active_school_id', auth()->user()->school_id);
        if ($user->school_id !== $schoolId) {
            abort(403, 'دسترسی غیرمجاز');
        }

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'phone'    => 'nullable|string|max:20',
            'role'     => 'required|exists:roles,name',
            'is_active'=> 'nullable|boolean',
        ]);

        $user->update([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'] ?? null,
            'is_active'=> $request->boolean('is_active', true),
        ]);

        if (!empty($data['password'])) {
            $user->update(['password' => Hash::make($data['password'])]);
        }

        $user->syncRoles([$data['role']]);

        return redirect()->route('school.users.index')
            ->with('success', 'اطلاعات کاربر به‌روزرسانی شد.');
    }

    /**
     * حذف کاربر
     */
    public function destroy(User $user)
    {
        $schoolId = session('active_school_id', auth()->user()->school_id);
        if ($user->school_id !== $schoolId) {
            abort(403, 'دسترسی غیرمجاز');
        }

        $user->delete();

        return redirect()->route('school.users.index')
            ->with('success', 'کاربر با موفقیت حذف شد.');
    }
}
