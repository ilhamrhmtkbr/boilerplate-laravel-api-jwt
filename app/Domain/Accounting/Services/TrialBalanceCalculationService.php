<?php

declare(strict_types=1);

namespace App\Domain\Accounting\Services;

use App\Domain\Accounting\ChartOfAccount\Repositories\ChartOfAccountRepositoryInterface;
use App\Domain\Accounting\GeneralLedger\Repositories\GeneralLedgerRepositoryInterface;
use DateTimeImmutable;
use LogicException;

/**
 * CONTOH Domain Service — TrialBalanceCalculationService.
 *
 * Ini adalah file REFERENSI/TEMPLATE, digenerate SEKALI (bukan per-entity
 * seperti CRUD skeleton lainnya), untuk menunjukkan seperti apa Domain
 * Service yang punya alasan architectural nyata untuk ada — bandingkan
 * dengan anti-pattern "{Entity}DomainService" yang isinya cuma forwarding
 * create/update/delete ke Repository (lihat catatan di gen_domain_exception).
 *
 * Domain Service ini pantas ada karena:
 *   1. Menghitung Trial Balance BUKAN tanggung jawab satu Entity —
 *      butuh mengagregasi banyak ChartOfAccount + GeneralLedger entries.
 *   2. Ada kebijakan/kalkulasi domain (bagaimana saldo per akun dihitung
 *      dari kumpulan jurnal) yang tidak dimiliki secara natural oleh
 *      satu aggregate manapun.
 *   3. Melibatkan lebih dari satu domain concept/repository sekaligus,
 *      sehingga tidak pas ditaruh di satu Entity maupun di satu Action.
 *
 * PERHATIAN — JANGAN LANGSUNG DIPAKAI DI PRODUCTION:
 * Method calculateForPeriod() di bawah ini SENGAJA belum diisi rumus
 * akuntansi sesungguhnya (kapan debit/kredit dianggap seimbang, saldo
 * normal per tipe akun, dst) karena itu adalah real business rule yang
 * harus kamu definisikan sendiri — bukan dikarang saat generate skeleton.
 * Method ini melempar LogicException supaya tidak bisa dipakai diam-diam
 * sebelum rule aslinya diisi.
 */
final class TrialBalanceCalculationService
{
    public function __construct(
        private readonly ChartOfAccountRepositoryInterface $chartOfAccountRepository,
        private readonly GeneralLedgerRepositoryInterface $generalLedgerRepository,
    ) {
    }

    /**
     * @return array<int, array{account_id: int, account_name: string, debit: float, credit: float}>
     */
    public function calculateForPeriod(DateTimeImmutable $periodStart, DateTimeImmutable $periodEnd): array
    {
        // TODO (contoh struktur, BUKAN rumus akuntansi final):
        //
        //   $balances = [];
        //   foreach ($this->chartOfAccountRepository->... as $account) {
        //       $entries = $this->generalLedgerRepository->findByAccountAndPeriod(
        //           accountId: $account->id(),
        //           start: $periodStart,
        //           end: $periodEnd,
        //       );
        //
        //       $balances[] = [
        //           'account_id'   => $account->id(),
        //           'account_name' => $account->name(),
        //           'debit'        => array_sum(array_map(fn ($e) => $e->debitAmount(), $entries)),
        //           'credit'       => array_sum(array_map(fn ($e) => $e->creditAmount(), $entries)),
        //       ];
        //   }
        //   return $balances;

        throw new LogicException(
            'TrialBalanceCalculationService::calculateForPeriod() belum diimplementasikan. '
            . 'Ini adalah contoh/template Domain Service — isi dengan business rule akuntansi '
            . 'yang sebenarnya sebelum dipakai, jangan dipanggil apa adanya.'
        );
    }
}
