<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = trim((string) $request->query('tanggal', ''));

        $rows = Pembayaran::query()
            ->with('rekamMedis.pasien')
            ->when($tanggal !== '', fn ($query) => $query->where('tanggal', $tanggal))
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();

        return view('pembayaran.index', [
            'rows' => $rows,
            'tanggal' => $tanggal,
        ]);
    }

    public function updateStatus(Request $request, Pembayaran $pembayaran)
    {
        $data = $request->validate([
            'status' => ['required', 'in:lunas,belum_lunas'],
        ]);

        $pembayaran->update(['status' => $data['status']]);

        $params = [];
        if (trim((string) $request->input('tanggal_filter', '')) !== '') {
            $params['tanggal'] = $request->input('tanggal_filter');
        }

        return redirect()->route('pembayaran.index', $params)
            ->with('success', 'Status pembayaran diubah menjadi '.status_bayar_label($data['status']).'.');
    }
}
