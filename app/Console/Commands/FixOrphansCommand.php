<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cashbox;
use App\Models\CashboxTransaction;
use App\Models\LedgerEntry;

class FixOrphansCommand extends Command
{
    protected $signature = 'db:fix-orphans';
    protected $description = 'حذف تراکنش‌های صندوق/دفتر کل یتیم و تنظیم مجدد موجودی صندوق‌ها';

    public function handle()
    {
        // ۱. پیدا کردن تراکنش‌های صندوق که reference آن‌ها حذف شده
        $orphanCashboxIds = CashboxTransaction::whereNotNull('reference_type')
            ->get()
            ->filter(function ($trx) {
                if (!class_exists($trx->reference_type)) {
                    return true;
                }
                return !app($trx->reference_type)->find($trx->reference_id);
            })
            ->pluck('id');

        $count1 = $orphanCashboxIds->count();
        if ($count1 > 0) {
            CashboxTransaction::whereIn('id', $orphanCashboxIds)->delete();
        }
        $this->info("{$count1} تراکنش صندوق یتیم حذف شد.");

        // ۲. پیدا کردن اسناد دفتر کل یتیم
        $orphanLedgerIds = LedgerEntry::whereNotNull('reference_type')
            ->get()
            ->filter(function ($entry) {
                if (!class_exists($entry->reference_type)) {
                    return true;
                }
                return !app($entry->reference_type)->find($entry->reference_id);
            })
            ->pluck('id');

        $count2 = $orphanLedgerIds->count();
        if ($count2 > 0) {
            LedgerEntry::whereIn('id', $orphanLedgerIds)->delete();
        }
        $this->info("{$count2} سند دفتر کل یتیم حذف شد.");

        // ۳. تنظیم مجدد موجودی صندوق‌ها بر اساس تراکنش‌های معتبر
        $cashboxes = Cashbox::all();
        foreach ($cashboxes as $box) {
            $deposits = CashboxTransaction::where('cashbox_id', $box->id)
                ->where('type', 'deposit')->sum('amount');
            $withdrawals = CashboxTransaction::where('cashbox_id', $box->id)
                ->where('type', 'withdrawal')->sum('amount');
            $box->update(['current_balance' => $deposits - $withdrawals]);
        }
        $this->info('موجودی تمام صندوق‌ها مجدداً محاسبه شد.');

        return 0;
    }
}
