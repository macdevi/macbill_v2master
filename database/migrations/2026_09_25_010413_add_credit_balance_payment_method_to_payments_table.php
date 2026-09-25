<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');

            try {
                DB::transaction(function (): void {
                    DB::statement("
                        CREATE TABLE payments_new (
                            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                            invoice_id INTEGER NOT NULL,
                            method VARCHAR NOT NULL CHECK (method IN ('cash', 'bank_transfer', 'credit_balance')),
                            amount NUMERIC NOT NULL,
                            proof_path VARCHAR NULL,
                            status VARCHAR NOT NULL DEFAULT 'pending' CHECK (status IN ('pending', 'verified', 'rejected')),
                            notes TEXT NULL,
                            paid_at DATETIME NULL,
                            verified_at DATETIME NULL,
                            created_at DATETIME NULL,
                            updated_at DATETIME NULL,
                            FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE
                        )
                    ");

                    DB::statement("
                        INSERT INTO payments_new (
                            id, invoice_id, method, amount, proof_path, status, notes,
                            paid_at, verified_at, created_at, updated_at
                        )
                        SELECT
                            id, invoice_id, method, amount, proof_path, status, notes,
                            paid_at, verified_at, created_at, updated_at
                        FROM payments
                    ");

                    DB::statement('DROP TABLE payments');
                    DB::statement('ALTER TABLE payments_new RENAME TO payments');
                });
            } finally {
                DB::statement('PRAGMA foreign_keys = ON');
            }

            return;
        }

        if ($driver === 'mysql') {
            DB::statement(
                "ALTER TABLE payments MODIFY method ENUM('cash', 'bank_transfer', 'credit_balance') NOT NULL"
            );

            return;
        }

        throw new RuntimeException("Database driver [$driver] tidak didukung oleh migration ini.");
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        $hasCreditBalancePayments = DB::table('payments')
            ->where('method', 'credit_balance')
            ->exists();

        if ($hasCreditBalancePayments) {
            throw new RuntimeException(
                'Rollback diblokir: payments.method masih memiliki data credit_balance.'
            );
        }

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');

            try {
                DB::transaction(function (): void {
                    DB::statement("
                        CREATE TABLE payments_new (
                            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                            invoice_id INTEGER NOT NULL,
                            method VARCHAR NOT NULL CHECK (method IN ('cash', 'bank_transfer')),
                            amount NUMERIC NOT NULL,
                            proof_path VARCHAR NULL,
                            status VARCHAR NOT NULL DEFAULT 'pending' CHECK (status IN ('pending', 'verified', 'rejected')),
                            notes TEXT NULL,
                            paid_at DATETIME NULL,
                            verified_at DATETIME NULL,
                            created_at DATETIME NULL,
                            updated_at DATETIME NULL,
                            FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE
                        )
                    ");

                    DB::statement("
                        INSERT INTO payments_new (
                            id, invoice_id, method, amount, proof_path, status, notes,
                            paid_at, verified_at, created_at, updated_at
                        )
                        SELECT
                            id, invoice_id, method, amount, proof_path, status, notes,
                            paid_at, verified_at, created_at, updated_at
                        FROM payments
                    ");

                    DB::statement('DROP TABLE payments');
                    DB::statement('ALTER TABLE payments_new RENAME TO payments');
                });
            } finally {
                DB::statement('PRAGMA foreign_keys = ON');
            }

            return;
        }

        if ($driver === 'mysql') {
            DB::statement(
                "ALTER TABLE payments MODIFY method ENUM('cash', 'bank_transfer') NOT NULL"
            );

            return;
        }

        throw new RuntimeException("Database driver [$driver] tidak didukung oleh migration ini.");
    }
};
