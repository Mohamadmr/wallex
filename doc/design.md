# شماتیک طراحی دستگاه فروش خودکار

## ۱. معماری کلی

این پروژه از الگوی State برای مدیریت وضعیت‌های مختلف دستگاه فروش خودکار استفاده می‌کند. همچنین از الگوی SAGA برای مدیریت تراکنش‌ها استفاده شده است.

### ۱.۱ کلاس‌های اصلی

- `SAGAWorker`: مدیریت دستگاه‌های فروش خودکار
- `VendingMachine`: دستگاه فروش خودکار
- `Transaction`: مدیریت تراکنش‌ها و موجودی محصولات
- `VendingMachineState`: کلاس پایه برای وضعیت‌های دستگاه

### ۱.۲ وضعیت‌های دستگاه

- `IDLEState`: وضعیت آماده به کار
- `ProductSelectedState`: وضعیت انتخاب محصول
- `DispensingState`: وضعیت تحویل محصول

## ۲. نمودار کلاس‌ها

```mermaid
classDiagram
    class SAGAWorker {
        -array $vendingMachines
        -TransactionInterface $transaction
        +__construct()
        +addVendingMachine() VendingMachine
        +dropIDLEVendingMachines() void
        +getFirstIDLEVendingMachine() VendingMachine
        +getVendingMachines() array
    }

    class VendingMachine {
        -float $balance
        -array $product
        -string $productUid
        -VendingMachineState $currentState
        -TransactionInterface $transaction
        +__construct(TransactionInterface)
        +insertCoin(float) void
        +ejectCoin() void
        +selectProduct(string) void
        +dispense() void
        +updateProductCount() void
        +unlockProduct() void
        +setState(VendingMachineState) void
        +getCurrentState() VendingMachineState
    }

    class Transaction {
        -array $products
        +getCount(string) int
        +getProducts() array
        +getProduct(string) array
        +setProduct(string, array) void
        +lockProduct(string) void
        +unlockProduct(string) void
        +decreaseProductCount(string) void
    }

    class VendingMachineState {
        #VendingMachine $vendingMachine
        +__construct(VendingMachine)
        +insertCoin() void
        +ejectCoin() void
        +selectProduct() void
        +dispense() void
    }

    class IDLEState {
        +insertCoin() void
        +ejectCoin() void
        +selectProduct() void
        +dispense() void
    }

    class ProductSelectedState {
        +insertCoin() void
        +ejectCoin() void
        +selectProduct() void
        +dispense() void
    }

    class DispensingState {
        +insertCoin() void
        +ejectCoin() void
        +selectProduct() void
        +dispense() void
    }

    SAGAWorker --> VendingMachine
    VendingMachine --> Transaction
    VendingMachine --> VendingMachineState
    VendingMachineState <|-- IDLEState
    VendingMachineState <|-- ProductSelectedState
    VendingMachineState <|-- DispensingState
```

## ۳. نمودار حالت‌ها

```mermaid
stateDiagram-v2
    [*] --> IDLE
    IDLE --> ProductSelected: selectProduct
    ProductSelected --> IDLE: ejectCoin
    ProductSelected --> Dispensing: dispense
    Dispensing --> IDLE: complete
```

## ۴. جریان تراکنش

1. کاربر سکه وارد می‌کند
2. کاربر محصول را انتخاب می‌کند
3. سیستم موجودی و قیمت را بررسی می‌کند
4. در صورت کافی بودن موجودی و قیمت، محصول قفل می‌شود
5. کاربر درخواست تحویل می‌دهد
6. محصول تحویل داده می‌شود و موجودی به‌روز می‌شود
7. قفل محصول آزاد می‌شود

## ۵. مدیریت خطاها

- `ProductNotFound`: محصول یافت نشد
- `NotEnoughCount`: موجودی کافی نیست
- `NotEnoughBalance`: موجودی مالی کافی نیست

## ۶. امنیت

- استفاده از قفل برای جلوگیری از تراکنش‌های همزمان
- بررسی موجودی قبل از قفل کردن محصول
- آزاد کردن قفل در صورت خطا یا لغو تراکنش 