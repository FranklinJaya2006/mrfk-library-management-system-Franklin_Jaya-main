<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // Menampilkan daftar librarian untuk View atau API
    // Menampilkan Librarian berdasarkan role
    public function showLibrarians(Request $request)
    {
        // Manually checking if user is logged in (for example, by checking a session or token)
        $user = $request->user();  // Retrieve the currently logged-in user from the request
    
        // Check if a user is logged in
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: No user authenticated'
            ], 403);
        }
    
        // Check user role for authorization
        if ($user->role === 'admin') {
            // Admin can see all librarians
            $librarians = User::where('role', 'librarian')->get();
    
            if ($request->wantsJson()) {
                if ($librarians->isEmpty()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No librarians found'
                    ], 404);
                }
    
                return response()->json([
                    'success' => true,
                    'data' => $librarians
                ], 200);
            }
    
            // Return view for browser request
            return view('admin', compact('librarians'));
        } elseif ($user->role === 'librarian') {
            // Librarian can only see their own data
            return response()->json([
                'success' => true,
                'data' => $user
            ], 200);
        }
    
        // Unauthorized user
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized'
        ], 403);
    }
    

// Menghapus akun librarian oleh admin
public function deleteLibrarian(Request $request, $id)
{
    $user = $request->user();  // Ambil data User yang sedang login
    
    // Pastikan hanya admin yang dapat menghapus librarian
    if ($user->role !== 'admin') {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized action'
        ], 403);
    }

    $librarian = User::find($id);

    if (!$librarian) {
        // Respons JSON untuk API
        return response()->json([
            'success' => false,
            'message' => 'Librarian tidak ditemukan'
        ], 404);
    }

    // Hapus librarian
    $librarian->delete();

    // Respons JSON untuk API
    return response()->json([
        'success' => true,
        'message' => 'Librarian berhasil dihapus'
    ], 200);
}



    public function approveRequest($id)
    {
        $request = BookLoan::findOrFail($id);
        $request->update(['approved_at' => now()]);

        return redirect()->back()->with('success', 'Request berhasil disetujui.');
    }
}