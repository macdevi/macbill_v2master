<?php
namespace App\Http\Controllers;

use App\Models\Installation;
use App\Services\InstallationBillingCorrectionService;
use Illuminate\Http\Request;

class InstallationBillingCorrectionController extends Controller
{
    private function data(Request $request): array
    {
        $request->merge(['reason' => trim((string) $request->input('reason', ''))]);
        return $request->validate([
            'billing_day' => ['required', 'integer', 'min:1', 'max:28'],
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);
    }

    public function edit(Request $request, Installation $installation,
        InstallationBillingCorrectionService $service)
    {
        $service->authorizeNote($installation, $request->user());
        $installation->load('customer');
        return view('installations.billing-correction', [
            'installation' => $installation, 'preview' => null,
        ]);
    }

    public function preview(Request $request, Installation $installation,
        InstallationBillingCorrectionService $service)
    {
        $service->authorizeNote($installation, $request->user());
        $data = $this->data($request);
        $preview = $service->preview($installation->id, $request->user(),
            (int) $data['billing_day'], $data['reason']);
        $installation->load('customer');
        return view('installations.billing-correction', compact('installation', 'preview'));
    }

    public function update(Request $request, Installation $installation,
        InstallationBillingCorrectionService $service)
    {
        $service->authorizeNote($installation, $request->user());
        $data = $this->data($request);
        $request->validate([
            'preview_token' => ['required', 'string', 'max:10000'],
            'confirmed' => ['accepted'],
        ]);
        $result = $service->apply($installation->id, $request->user(),
            (int) $data['billing_day'], $data['reason'],
            $request->string('preview_token')->toString(), $request->ip());
        return redirect()->route('installations.show', $installation)->with('success',
            $result['invoice_id']
                ? 'Jadwal PSB dan pelanggan diselaraskan. Invoice '.$result['invoice_number'].' berhasil dibuat.'
                : 'Jadwal PSB dan pelanggan diselaraskan. Tagihan pertama dijadwalkan '.$result['first_billing_on'].'.');
    }
}
