<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BillingSettingController extends Controller
{
    private const DEFAULT_WHATSAPP_INVOICE_TEMPLATE = <<<'TEXT'
Halo Bapak/Ibu {customer_name},

Berikut tagihan internet dari {business_name}.

No. Invoice: {invoice_number}
{credit_summary}Sisa Tagihan: {amount}
Jatuh Tempo: {due_date}

Invoice: {invoice_url}

Terima kasih,
{business_name}
WhatsApp: {business_phone}
TEXT;

    public function edit(): View
    {
        return view('settings.billing', [
            'businessName' => Setting::value('business_name', 'MACBILLING'),
            'businessOwner' => Setting::value('business_owner', ''),
            'businessPhone' => Setting::value('business_phone', ''),
            'businessEmail' => Setting::value('business_email', ''),
            'businessAddress' => Setting::value('business_address', ''),
            'businessCity' => Setting::value('business_city', ''),
            'invoiceFooter' => Setting::value('invoice_footer', 'Terima kasih telah menggunakan layanan kami.'),
            'whatsappInvoiceTemplate' => Setting::value('whatsapp_invoice_template', self::DEFAULT_WHATSAPP_INVOICE_TEMPLATE),
            'taxEnabled' => filter_var(Setting::value('billing_tax_enabled', '1'), FILTER_VALIDATE_BOOLEAN),
            'taxName' => Setting::value('billing_tax_name', 'PPN'),
            'taxRate' => Setting::value('billing_tax_rate', '11.00'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'business_name' => ['required', 'string', 'max:100'],
            'business_owner' => ['nullable', 'string', 'max:100'],
            'business_phone' => ['nullable', 'string', 'max:30'],
            'business_email' => ['nullable', 'email', 'max:100'],
            'business_address' => ['nullable', 'string', 'max:500'],
            'business_city' => ['nullable', 'string', 'max:100'],
            'invoice_footer' => ['nullable', 'string', 'max:500'],
            'whatsapp_invoice_template' => ['required', 'string', 'max:3000'],
            'billing_tax_enabled' => ['nullable', 'boolean'],
            'billing_tax_name' => ['nullable', 'string', 'max:50'],
            'billing_tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
        ], [
            'business_name.required' => 'Nama usaha wajib diisi.',
            'business_email.email' => 'Format email usaha belum benar.',
            'whatsapp_invoice_template.required' => 'Template pesan WhatsApp wajib diisi.',
            'billing_tax_rate.required' => 'Tarif pajak wajib diisi.',
            'billing_tax_rate.numeric' => 'Tarif pajak harus berupa angka.',
            'billing_tax_rate.min' => 'Tarif pajak tidak boleh kurang dari 0.',
            'billing_tax_rate.max' => 'Tarif pajak tidak boleh lebih dari 100%.',
        ]);

        $values = [
            'business_name' => trim((string) $data['business_name']),
            'business_owner' => trim((string) ($data['business_owner'] ?? '')),
            'business_phone' => trim((string) ($data['business_phone'] ?? '')),
            'business_email' => trim((string) ($data['business_email'] ?? '')),
            'business_address' => trim((string) ($data['business_address'] ?? '')),
            'business_city' => trim((string) ($data['business_city'] ?? '')),
            'invoice_footer' => trim((string) ($data['invoice_footer'] ?? '')),
            'whatsapp_invoice_template' => trim((string) $data['whatsapp_invoice_template']),
            'billing_tax_enabled' => $request->boolean('billing_tax_enabled') ? '1' : '0',
            'billing_tax_name' => trim((string) ($data['billing_tax_name'] ?? '')) ?: 'PPN',
            'billing_tax_rate' => number_format((float) $data['billing_tax_rate'], 4, '.', ''),
        ];

        foreach ($values as $key => $value) {
            Setting::put($key, $value);
        }

        return redirect()
            ->route('settings.billing.edit')
            ->with('success', 'Pengaturan usaha berhasil disimpan. Perubahan pajak hanya berlaku untuk invoice baru.');
    }
}
