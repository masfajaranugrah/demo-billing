<?php

namespace App\Http\Controllers;

use App\Models\Iklan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class IklanController extends Controller
{
    public function index()
    {
        $iklans = Iklan::with('creator')
            ->latest()
            ->get();

        return view('content.apps.Iklan.iklan', compact('iklans'));
    }

    public function create()
    {
        return view('content.apps.Iklan.add-iklan');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'message' => 'required|string|min:10|max:1000',
                'type' => 'required|in:informasi,maintenance,iklan',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $imagePath = $request->hasFile('image')
                ? $request->file('image')->store('iklan', 'public')
                : null;

            $iklan = Iklan::create([
                'id' => (string) Str::uuid(),
                'title' => $validated['title'],
                'message' => $validated['message'],
                'type' => $validated['type'],
                'image' => $imagePath,
                'status' => 'active',
                'total_sent' => 0,
                'created_by' => Auth::id()
            ]);

            // ?? KIRIM VIA QUEUE (AMAN 10.000+)
            dispatch(new \App\Jobs\SendIklanPushJob($iklan->id));

            return redirect()
                ->route('iklan.index')
                ->with('success', 'Iklan berhasil dibuat. Notifikasi sedang dikirim di background.');

        } catch (\Throwable $e) {
            Log::error('Store iklan error', ['error' => $e->getMessage()]);

            return back()
                ->with('error', 'Gagal membuat iklan')
                ->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $iklan = Iklan::findOrFail($id);

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'message' => 'required|string|max:1000',
                'type' => 'required|in:informasi,maintenance,iklan',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($request->hasFile('image')) {
                if ($iklan->image) {
                    Storage::disk('public')->delete($iklan->image);
                }
                $validated['image'] = $request->file('image')->store('iklan', 'public');
            }

            $iklan->update($validated);

            return redirect()
                ->route('iklan.index')
                ->with('success', 'Iklan berhasil diupdate');

        } catch (\Throwable $e) {
            Log::error('Update iklan error', ['error' => $e->getMessage()]);

            return back()
                ->with('error', 'Gagal update iklan')
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            Iklan::findOrFail($id)->delete();

            return redirect()
                ->route('iklan.index')
                ->with('success', 'Iklan berhasil dihapus');

        } catch (\Throwable $e) {
            Log::error('Delete iklan error', ['error' => $e->getMessage()]);

            return back()->with('error', 'Gagal hapus iklan');
        }
    }
}
