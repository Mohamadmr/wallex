# دستگاه فروش خودکار (Vending Machine)

این پروژه یک دستگاه فروش خودکار است که با استفاده از الگوی State و SAGA(orchestration) پیاده‌سازی شده است.

## پیش‌نیازها

- PHP 8.0 یا بالاتر
- Composer
- Docker و Docker Compose
- PHPUnit

## نصب

### روش ۱: نصب مستقیم

1. کلون کردن پروژه:
```bash
git clone https://github.com/Mohamadmr/wallex.git
cd wallex
```

2. نصب وابستگی‌ها:
```bash
composer install
```

### روش ۲: استفاده از Docker

1. کلون کردن پروژه:
```bash
git clone https://github.com/Mohamadmr/wallex.git
cd wallex
```

2. ساخت و اجرای کانتینرها:
```bash
docker-compose up -d --build
```

پس از اجرا، پروژه روی پورت 8080 در دسترس خواهد بود:
http://localhost:8080

## ساختار پروژه

```
wallex/
├── src/
│   ├── contracts/
│   │   ├── TransactionInterface.php
│   │   └── VendingMachineState.php
│   ├── exceptions/
│   │   ├── NotEnoughBalance.php
│   │   ├── NotEnoughCount.php
│   │   └── ProductNotFound.php
│   ├── states/
│   │   ├── DispensingState.php
│   │   ├── IDLEState.php
│   │   └── ProductSelectedState.php
│   ├── SAGAWorker.php
│   ├── Transaction.php
│   └── VendingMachine.php
├── tests/
│   ├── states/
│   │   └── IDLEStateTest.php
│   ├── SAGAWorkerTest.php
│   ├── TransactionTest.php
│   └── VendingMachineTest.php
├── public/
│   └── index.php
├── deployment/
│   └── nginx/
│       └── app.conf
├── doc/
│   └── design.md
├── docker-compose.yml
├── Dockerfile
├── composer.json
└── README.md
```

## اجرای تست‌ها


```bash
./vendor/bin/phpunit tests
```

### یا در Docker:
```bash
docker-compose exec app ./vendor/bin/phpunit tests
```

## مستندات طراحی

مستندات طراحی پروژه در پوشه `doc` قرار دارد. برای مشاهده نمودارها، می‌توانید از ابزارهای پشتیبانی کننده Mermaid استفاده کنید.

## نحوه استفاده

### استفاده از API

پس از راه‌اندازی پروژه، می‌توانید از API زیر استفاده کنید:

```bash
curl http://localhost:8080
```

### استفاده در کد

```php
$worker = new SAGAWorker();
$vendingMachine = $worker->getFirstIDLEVendingMachine();

try {
    
    $vendingMachine->insertCoin(10);
    
    $vendingMachine->selectProduct('soda_uid');
    
    $vendingMachine->dispense();
} catch (ProductNotFound $exception) {
    echo $exception->getMessage() . PHP_EOL;
    echo 'Please get new product uid' . PHP_EOL;
} catch (NotEnoughCount|NotEnoughBalance $exception) {
    echo $exception->getMessage() . PHP_EOL;
    $vendingMachine->ejectCoin();
} finally {
    echo 'Transaction completed' . PHP_EOL;
    $vendingMachine->ejectCoin();
}
```

## خطاهای احتمالی

- `ProductNotFound`: زمانی که محصول مورد نظر یافت نشود
- `NotEnoughCount`: زمانی که موجودی محصول کافی نباشد
- `NotEnoughBalance`: زمانی که موجودی مالی کافی نباشد

## امنیت

- استفاده از قفل برای جلوگیری از تراکنش‌های همزمان
- بررسی موجودی قبل از قفل کردن محصول
- آزاد کردن قفل در صورت خطا یا لغو تراکنش
