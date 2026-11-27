<?php

namespace App\Http\Controllers;

use App\Models\Installation;
use App\Services\InstallationCancellationService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class InstallationCancellationController extends Controller
{
    public function destroy(
        Request $request,
        Installation $installation,
        InstallationCancellationService $service
    ) {
        $user = $request->user();

        abort_unless(
            $user && in_array($user->role, ['super_admin', 'admin'], true),
            403
        );
        abort_unless(
            $user->isSuperAdmin() ||
            $user->activeAreaIds()->contains((int) $installation->area_id),
            403
        );

        $data = $request->validate([
            'cancel_target' => ['required', 'in:billing,billing_mikrotik'],
            'cancel_reason' => ['required', 'string', 'min:5', 'max:1000'],
            'confirm_receipt' => ['required', 'string', 'max:100'],
            'refund_amount' => ['nullable', 'numeric', 'min:0', 'max:999999999', 'decimal:0,2'],
            'refund_method' => ['nullable', 'in:cash,bank_transfer'],
            'refund_reference' => ['nullable', 'string', 'max:255'],
            'refund_confirmed' => ['sometimes', 'boolean'],
            'cancel_is_test' => ['sometimes', 'boolean'],
        ]);

        try {
            $service->cancel(
                $installation->id,
                $user,
                $data['cancel_target'],
                $data['cancel_reason'],
                $data['confirm_receipt'],
                $request->ip(),
                [
                    'amount' => $data['refund_amount'] ?? '0',
                    'method' => $data['refund_method'] ?? null,
                    'reference' => $data['refund_reference'] ?? null,
                    'confirmed' => $request->boolean('refund_confirmed'),
                    'is_test_data' => $request->boolean('cancel_is_test'),
                ]
            );
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $exception) {
            throw $exception;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()->route('installations.show', $installation)
                ->withErrors([
                    'cancellation' =>
                        'Pembatalan gagal; transaksi database dibatalkan. '.
                        ($data['cancel_target'] === 'billing_mikrotik'
                            ? 'Perubahan MikroTik mungkin sudah terjadi sebagian. Periksa router sebelum mencoba ulang.'
                            : 'Periksa log aplikasi sebelum mencoba ulang.'),
                ]);
        }

        return redirect()->route('installations.index')->with(
            'success',
            'Pemasangan dibatalkan. Arsip tersimpan; refund nyata, jika ada, dicatat di Pengeluaran. '.'Pelanggan, nota, dan invoice tanpa pembayaran dihapus. '.
            ($data['cancel_target'] === 'billing'
                ? 'MikroTik tidak diubah.'
                : 'Akun dan sesi PPPoE telah diperiksa dan dibersihkan.')
        );
    }
}
