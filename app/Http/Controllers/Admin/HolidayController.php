<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Holiday;

class HolidayController extends Controller
{
    public function index()
    {
        // Urutkan dari tanggal terbaru
        $holidays = Holiday::orderBy('date', 'desc')->paginate(10);
        return view('admin.holidays.index', compact('holidays'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'date'  => 'required|date|unique:holidays,date',
        ], [
            'date.unique' => 'Tanggal libur ini sudah terdaftar.',
        ]);

        Holiday::create([
            'title' => $request->title,
            'date'  => $request->date,
            'description' => $request->description
        ]);

        return back()->with('success', 'Hari libur berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        Holiday::findOrFail($id)->delete();
        return back()->with('success', 'Hari libur berhasil dihapus.');
    }
}
