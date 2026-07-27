<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CheckJalaliDates extends Command
{
    protected $signature = 'db:check-jalali';
    protected $description = 'بررسی واقعی تبدیلات تاریخ شمسی در کنترلرها و Viewها';

    protected $dateColumns = [
        'Student' => [
            'controller' => 'SchoolStudentController',
            'columns'    => ['birth_date', 'enrollment_date'],
        ],
        'Employee' => [
            'controller' => 'SchoolEmployeeController',
            'columns'    => ['birth_date', 'hire_date', 'termination_date'],
        ],
        'Expense' => [
            'controller' => 'SchoolExpenseController',
            'columns'    => ['expense_date'],
        ],
        'Income' => [
            'controller' => 'SchoolIncomeController',
            'columns'    => ['income_date'],
        ],
        'Salary' => [
            'controller' => 'SchoolSalaryController',  // اگر SalaryPaymentController هم تاریخ دارد می‌توان اضافه کرد
            'columns'    => ['payment_date'],           // معمولاً paid_date یا payment_date در salary_payments
        ],
        'Loan' => [
            'controller' => 'LoanController',            // از namespace کامل استفاده نشده ولی فایل همین است
            'columns'    => ['start_date', 'borrower_birth_date'],
        ],
        'Asset' => [
            'controller' => 'AssetController',
            'columns'    => ['purchase_date'],
        ],
    ];

    public function handle()
    {
        $this->info("🔍 بررسی واقعی تبدیلات تاریخ جلالی...\n");

        foreach ($this->dateColumns as $model => $config) {
            $controllerName = $config['controller'];
            $columns = $config['columns'];

            foreach ($columns as $column) {
                $this->output->write("🔹 {$model} → {$column} ... ");

                // مسیر کنترلر
                $controllerPath = app_path("Http/Controllers/School/{$controllerName}.php");
                $controllerOk = false;
                if (File::exists($controllerPath)) {
                    $content = File::get($controllerPath);
                    // الگوی تبدیل شمسی → میلادی (ورود داده)
                    if (preg_match("/JalaliHelper\s*::\s*toGregorian\s*\(.*{$column}/i", $content)) {
                        $controllerOk = true;
                    }
                }

                // بررسی Viewها – نام پوشه را از مدل حدس می‌زنیم (مثلاً Student → students)
                $viewFolder = strtolower($model) . 's'; // students, employees, expenses, incomes, salaries, loans, assets
                $viewPath = resource_path("views/school/{$viewFolder}");
                $viewOk = false;
                if (File::exists($viewPath)) {
                    $files = File::allFiles($viewPath);
                    foreach ($files as $file) {
                        $content = File::get($file);
                        // الگوی تبدیل میلادی → شمسی (نمایش)
                        if (preg_match("/JalaliHelper\s*::\s*toJalali\s*\(.*{$column}/i", $content)) {
                            $viewOk = true;
                            break;
                        }
                    }
                }

                // نمایش وضعیت
                if ($controllerOk && $viewOk) {
                    $this->info("✅ کامل (هم ورود هم نمایش)");
                } elseif ($controllerOk && !$viewOk) {
                    $this->warn("⚠️ فقط ورود تبدیل دارد (نمایش بررسی نشد / ممکن است در View نباشد)");
                } elseif (!$controllerOk && $viewOk) {
                    $this->warn("⚠️ فقط نمایش تبدیل دارد (ورود تبدیل ندارد)");
                } else {
                    $this->error("❌ تبدیل جلالی پیدا نشد");
                }
            }
        }

        $this->newLine();
        $this->info("پایان بررسی.");
    }
}
