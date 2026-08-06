# NutiPita Admin — File Inventory

Concrete file-by-file listing of `app/`, `routes/`, and `database/migrations/`. Excludes `vendor/`, `node_modules/`, framework-generated/unmodified defaults (`database/migrations/0001_01_01_000001_create_cache_table.php`, `..._000002_create_jobs_table.php`), `bootstrap/`, `storage/`, `public/`, and compiled/config boilerplate. Companion to `docs/refactor-analysis.md`, which covers the same code in analytical depth — this file is a flat reference index.

---

## app/Console/Commands

| Path | Purpose |
|---|---|
| `app/Console/Commands/CheckForStandingOrdersToBeActivated.php` | Scheduled daily (00:01, `routes/console.php`) command `app:check-standing-order-status`. Activates `StandingOrder` rows whose `start_from` date is today and that aren't manually force-overridden (`is_forced` null). |
| `app/Console/Commands/CreateOrderFromStanding.php` | Scheduled daily (00:02) command `app:create-order-from-standing`. Materializes a real `Order` (+ product lines) from each active `StandingOrder`'s pattern for today's weekday; independently reimplements order-creation logic rather than calling `OrderService::createOrder`. |
| `app/Console/Commands/IsDebug.php` | Dev utility command `app:is-debug` — prints whether the app is running in production/debug/local mode. No business logic. |
| `app/Console/Commands/test.php` | Scratch command `app:test` — logs `"test at {now}"`. Dev/debug leftover, not part of any workflow. |

## app/DataTransferObjects

| Path | Purpose |
|---|---|
| `app/DataTransferObjects/InvoiceDto.php` | Immutable DTO built via static `from()` factory; resolves customer/order, defaults dates, validates invoice-number uniqueness (queries DB inside the factory), used to carry invoice-creation data into `InvoiceService`. |
| `app/DataTransferObjects/InvoiceProductDto.php` | DTO for a single invoice line item (invoice, product, qty, unit price); factory validates qty ≥ 1. |
| `app/DataTransferObjects/OrderSummaryDto.php` | DTO summarizing a collection of orders — total price, per-product totals (via `ProductTotalDto`) — built from `Order::getTotalOfProduct()`. |
| `app/DataTransferObjects/ProductTotalDto.php` | Trivial value object: product id/name/total quantity, used inside `OrderSummaryDto`. |

## app/Enums

| Path | Purpose |
|---|---|
| `app/Enums/FinancialRecordType.php` | Backed enum: `EXPENSE`, `INCOME` — used only for `FinancialRecord` validation/typing. |
| `app/Enums/InvoiceStatus.php` | Backed enum: `paid`, `due`, `cancelled` — represents `invoices.invoice_status`, but the column isn't cast to it on the model. |
| `app/Enums/OrderStatus.php` | Backed enum: `Y_CONFIRMED`, `G_PAID`, `O_DELIVERED_UNPAID` (7 more cases commented out, suggesting a richer planned workflow). Has a custom `fromName()` lookup helper. |
| `app/Enums/settings/UserColorMode.php` | Backed int enum, 18 UI color options for user appearance settings; includes a `colorFromValue()` label helper. |
| `app/Enums/settings/UserFontSize.php` | Backed int enum: `_12,_14,_16,_18,_20` px font-size options for user settings. |
| `app/Enums/settings/UserThemeMode.php` | Backed int enum: `DARK`, `WHITE`; includes a `themeFromValue()` label helper. |

## app/Helpers

| Path | Purpose |
|---|---|
| `app/Helpers/Format.php` | Static formatting utilities: date parsing/formatting, unit-price trimming, number formatting, ceiling rounding, year extraction. |
| `app/Helpers/ModelResolver.php` | Static `resolve($id, $modelClass)` — returns the model as-is if already an instance, else `find()`s it by id. Used by services/DTOs to accept either a model or an id. |
| `app/Helpers/helpers.php` | Global function wrappers (`dayDate`, `moneyFormat`, `unitPriceFormat`, `numberFormat`, `amountFormat`, `resolveModel`, `getCurrentWeekNumber`) delegating to `Format`/`ModelResolver`; also registered as Blade directives in `AppServiceProvider`. |

## app/Http/Controllers

| Path | Purpose |
|---|---|
| `app/Http/Controllers/AuthController.php` | Login/logout — inline `User::whereUsername()` lookup + `Hash::check`, session auth via `auth()->login()`. |
| `app/Http/Controllers/Controller.php` | Empty abstract base controller. |
| `app/Http/Controllers/CreditNoteController.php` | Contains only `test()` — a hardcoded, non-persisting PDF-generation prototype for credit notes. Credit-note workflow is otherwise unimplemented. |
| `app/Http/Controllers/CustomerController.php` | Full customer CRUD + custom product-price management (`store`/`update`/`editCustomPrice`/`updateCustomPrice`); price-upsert logic is duplicated between two methods in this file. |
| `app/Http/Controllers/DashboardController.php` | Renders the dashboard view; `createOrderTotalPdf()` renders a PDF from a DTO stashed in `session('dto')` by a Livewire component. |
| `app/Http/Controllers/FinancialCategoryController.php` | CRUD for `FinancialCategory` (create/store/edit/update); has a dead import of a non-existent `StoreFinancialCategoryRequest`. |
| `app/Http/Controllers/FinancialRecordController.php` | CRUD for `FinancialRecord` (create/store/edit/update), each wrapped in its own DB transaction. |
| `app/Http/Controllers/InvoiceController.php` | `createSingleInvoice()` builds an `InvoiceDto` and drives `InvoiceService` to generate an invoice from one order (also sets the order to `O_DELIVERED_UNPAID` inline); plus `download()`/`viewInline()` for the stored PDF. |
| `app/Http/Controllers/MoneyController.php` | Single-action controller rendering the `money.index` view (data supplied entirely by the — currently dead — `MoneyFilter` Livewire component). |
| `app/Http/Controllers/OrderController.php` | Full order CRUD; `store()`/`update()` **independently reimplement** `OrderService::createOrder`/`updateOrder` with subtle behavioral differences (see refactor-analysis §5); `createSummaryPdf()` does its own order/product aggregation. |
| `app/Http/Controllers/ProductController.php` | CRUD for `Product`; has dead imports of non-existent `StoreProductRequest`/`UpdateProductRequest`. |
| `app/Http/Controllers/StandingOrderController.php` | Full standing-order CRUD including all day/product-pattern diffing and the activation-state rules — no service layer backs this domain at all. |
| `app/Http/Controllers/TestController.php` | Dev-only controller backing `routes/test.php`; `testLivewireOrderList()` returns a scratch view with empty test data. |
| `app/Http/Controllers/UserSettingController.php` | Renders the appearance-settings view; actual save logic lives in the `Setting/AppearanceUpdateForm` Livewire component, not here. |

## app/Http/Middleware

| Path | Purpose |
|---|---|
| `app/Http/Middleware/AuthMiddleware.php` | Single-tier session-auth gate — redirects to login (storing intended URL) if `auth()->check()` fails; no roles/permissions. |

## app/Http/Requests

| Path | Purpose |
|---|---|
| `app/Http/Requests/FinancialCategoryRequest.php` | Validates `fin_cat_name` (required/string/max:100). |
| `app/Http/Requests/FinancialRecordRequest.php` | Validates financial record fields; `fin_record_type` checked against `FinancialRecordType::cases()`. |
| `app/Http/Requests/LoginRequest.php` | Validates `username` (must exist) + `password` (required). |
| `app/Http/Requests/ProductRequest.php` | Validates product name/weight/qty-per-pack fields. |
| `app/Http/Requests/StandingOrderRequest.php` | Validates `customer_id`, `start_from`, `products` array for standing-order creation/update. |
| `app/Http/Requests/StoreCustomerRequest.php` | Validates new-customer fields, `customer_name` unique. |
| `app/Http/Requests/StoreOrderRequest.php` | Validates new-order fields: `customer_id`, `products.*` qty ≥ 0, `shift` in `day`/`night`. |
| `app/Http/Requests/UpdateCustomerRequest.php` | Same as `StoreCustomerRequest` but unique check ignores the current customer; adds custom-price validation. |
| `app/Http/Requests/UpdateOrderRequest.php` | Validates order updates incl. `order_status` against `OrderStatus::cases()`; validation key is `product.*` (singular) — inconsistent with `StoreOrderRequest`'s `products.*`, likely a typo. |

## app/Livewire

| Path | Purpose |
|---|---|
| `app/Livewire/Customer/CustomerList.php` | Sortable/mobile-aware customer list; preloads products with custom prices. |
| `app/Livewire/Customer/CustomerPopupCard.php` | Read-only customer detail card (modal). |
| `app/Livewire/Customer/MobileCustomerSort.php` | Mobile sort-option dispatcher → `CustomerList` (one of 5 near-identical Mobile*Sort components). |
| `app/Livewire/CustomerCustomPrices.php` | Manages a single customer's custom product prices (list + delete); has an inline `// TODO: optimise later` note. |
| `app/Livewire/FinCategories/FinancialCategoryList.php` | Lists and deletes financial categories, transaction-wrapped. |
| `app/Livewire/FinRecord/FileImport.php` | CSV bank-statement importer for `FinancialRecord` — parses headers/rows, infers income/expense, bulk-creates records row-by-row in transactions. |
| `app/Livewire/FinRecord/FinancialRecordFilter.php` | Filter widget (category + date range via `HasQuickDueFilter`) dispatching `update-filter` to `FinancialRecordList`. |
| `app/Livewire/FinRecord/FinancialRecordList.php` | Filtered/sorted financial-record list with inline delete and an in-component query builder. |
| `app/Livewire/FinRecord/FinancialRecordToggler.php` | Pure UI relay toggling between income/expense views of `FinancialRecordList`. |
| `app/Livewire/Homepage/DownloadSummary.php` | Builds a day/night order summary via `OrderService::createBasicSummary()` and redirects to the PDF download route. |
| `app/Livewire/Homepage/HomepageOrders.php` | Existence checks for whether day-shift/night-shift orders exist today (dashboard widget). |
| `app/Livewire/Homepage/HomepageToggler.php` | UI toggle dispatching day/night/both selection to `HomepageOrders`. |
| `app/Livewire/Invoice/CreateInvoice.php` | The most complex component in the app — creates invoices in "auto" (date-range of a customer's orders) or "manual" (freeform line items) mode, live-calculates totals, and drives `InvoiceService`. |
| `app/Livewire/Invoice/InvoiceFilter.php` | Filter widget (customer + invoice date range) dispatching `update-filter` to `InvoiceList`. |
| `app/Livewire/Invoice/InvoiceList.php` | Paginated/sortable invoice list; handles status updates (paid/due/delete), each with its own transaction + `Order` status side-effects. |
| `app/Livewire/Invoice/InvoicePopupCard.php` | Invoice detail card + delete (delegates deletion to `InvoiceService`). |
| `app/Livewire/Invoice/MobileInvoiceSort.php` | Mobile sort dispatcher → `InvoiceList`. |
| `app/Livewire/Modal/ModalContainer.php` | Global modal-stack manager, listens for `modal-open`/`modal-close`/`modal-clear` events. |
| `app/Livewire/Modal/OrderCreate.php` | Order-creation modal form; delegates creation to `OrderService::createOrder`. |
| `app/Livewire/Modal/OrderEdit.php` | Order-edit modal form; delegates update to `OrderService::updateOrder`. |
| `app/Livewire/MoneyFilter.php` | **Dead/unused** — an order-income filter widget not referenced by any view or route. |
| `app/Livewire/Order/MobileOrderSort.php` | Mobile sort dispatcher → `OrderList`. |
| `app/Livewire/Order/OrderFilter.php` | Order filter widget (customer/status/date-range/shift/month) dispatching `update-filter` to `OrderList`. |
| `app/Livewire/Order/OrderPopupCard.php` | Order detail card with delete/edit/create-invoice actions (delegates deletion to `OrderService`). |
| `app/Livewire/Order/OrderSummary.php` | Aggregate order summary (count/income/product totals) computed via `OrderQueryBuilder` — the cleanest example of query-class reuse in the app. |
| `app/Livewire/OrderList.php` | Main order list (desktop + infinite-scroll mobile), pagination/sort/filter, uses `OrderQueryBuilder` + `OrderListService`. |
| `app/Livewire/OrderSummaryDownload.php` | Relays a generated summary-PDF download link into the UI. |
| `app/Livewire/Product/MobileProductSort.php` | Mobile sort dispatcher → `ProductList`. |
| `app/Livewire/Product/ProductList.php` | Simple sorted product list, no filters. |
| `app/Livewire/Product/ProductSelector.php` | Product picker scoped to a chosen customer's custom prices. |
| `app/Livewire/Setting/AppearanceUpdateForm.php` | User appearance/theme/font-size settings form; saves directly to the `UserSetting` relation. |
| `app/Livewire/StandingOrder/CreateStandingOrder.php` | Standing-order creation form's product-list panel. |
| `app/Livewire/StandingOrder/MobileStandingOrderSort.php` | Mobile sort dispatcher → `StandingOrderList`. |
| `app/Livewire/StandingOrderList.php` | Standing-order list with status toggle and delete (manually cascades day/day-product deletion in nested loops). |
| `app/Livewire/_discard/CreateInvoiceFromOrder.php` | **Dead/unused** — an earlier draft of the invoice-creation flow, superseded by `Invoice/CreateInvoice`; folder name signals intentional discard. |

## app/Models

| Path | Purpose |
|---|---|
| `app/Models/CreditNote.php` | Maps `credit_notes`; `belongsTo` Invoice. No logic — the credit-note feature is otherwise unimplemented. |
| `app/Models/Customer.php` | Maps `customers`; `hasMany` Order/CustomerProductPrice/Invoice; one trivial address-concatenation accessor. |
| `app/Models/CustomerProductPrice.php` | Maps `customer_product_prices` (per-customer product price override); `belongsTo` Product/Customer. |
| `app/Models/FinancialCategory.php` | Maps `financial_categories`; no relationships or logic defined. |
| `app/Models/FinancialRecord.php` | Maps `financial_records`; `belongsTo` FinancialCategory. |
| `app/Models/Invoice.php` | Maps `invoices`; `belongsTo` Customer/Order, `hasMany` InvoiceProduct, `hasOne` CreditNote; `getNextInvoiceNumber()` static method generates the next zero-padded invoice number via a live query (not concurrency-safe). |
| `app/Models/InvoiceProduct.php` | Maps `invoice_products` (composite PK, no timestamps) — invoice line-item pivot; `belongsTo` Invoice/Product. |
| `app/Models/Order.php` | Maps `orders`; `belongsTo` Customer, `belongsToMany` Product, `hasOne` Invoice. Richest model in the app: pivot-based total accessors, presentation-formatting accessors, and status-mutating "scopes" (`scopeMarkPaid`/`markUnpaid`/`markConfirmed`) that perform bulk writes rather than filtering. |
| `app/Models/Product.php` | Maps `products`; `belongsToMany` Order, `hasMany` CustomerProductPrice. Global scope always orders by name; `setCurrentCustomer()`/`getPriceAttribute()` resolve customer-scoped pricing via request-scoped in-memory state (aborts 418 if no customer set). |
| `app/Models/Scopes/Product/SortByName.php` | Global `Scope` implementation applied to every `Product` query — `orderByDesc('product_name')`. |
| `app/Models/StandingOrder.php` | Maps `standing_orders`; `hasMany` StandingOrderDay, `belongsTo` Customer. No scheduling logic on the model itself (lives in controller + console commands instead). |
| `app/Models/StandingOrderDay.php` | Maps `standing_order_days`; `belongsTo` StandingOrder (method misleadingly named `order()`), `hasMany` StandingOrderDayProduct. |
| `app/Models/StandingOrderDayProduct.php` | Maps `standing_order_day_products`; `belongsTo` StandingOrderDay, and a `Product` relation modeled as `hasOne` where `belongsTo` would be semantically correct. |
| `app/Models/User.php` | Auth user model (custom `user_id` PK); `hasOne` UserSetting; hashes password via `casts()`. |
| `app/Models/UserSetting.php` | Maps `user_settings` (PK = FK on `user_id`); `belongsTo` User; the only model that properly casts enum-backed columns to PHP enums. |

## app/Providers

| Path | Purpose |
|---|---|
| `app/Providers/AppServiceProvider.php` | Boot-time setup: prohibits destructive DB commands in production, loads `helpers.php`, shares a `MobileDetect` instance to all views, registers Blade directives (`@dayDate`, `@moneyFormat`, `@unitPriceFormat`, `@numberFormat`, `@amountFormat`). |

## app/Queries

| Path | Purpose |
|---|---|
| `app/Queries/OrderQueryBuilder.php` | The one dedicated query class in the app — centralizes Order filtering by active period (today/shift shortcuts), daytime/nighttime, customer, status, and due-date range. Consumed only by `OrderList` and `Order/OrderSummary`; equivalent filter logic is reimplemented ad hoc in ~6 other places (see refactor-analysis §4). |

## app/Services

| Path | Purpose |
|---|---|
| `app/Services/InvoiceService.php` | Invoice domain service: generates an invoice + its product-line records + PDF from DTOs, and deletes an invoice (reverting its order's status). Bypassed by inline invoice-total calculation in `InvoiceController` and by independent order-eligibility querying in `Invoice/CreateInvoice`. |
| `app/Services/LivewireHelpers/OrderListService.php` | Order-list-specific helpers: custom sort closures (by customer/total_pita/total_price) and summary-PDF URL building. |
| `app/Services/OrderService.php` | Order domain service: `createBasicSummary()`, `createOrder()`, `updateOrder()`, `deleteOrder()`. Reached only from Livewire call sites — `OrderController` and `CreateOrderFromStanding` reimplement the same operations independently, with confirmed behavioral divergences. |

## app/Traits

| Path | Purpose |
|---|---|
| `app/Traits/HasQuickDueFilter.php` | Shared filter-state trait (today/yesterday/week/month/year quick-date shortcuts) used by 4+ Livewire filter components; contains a `setToday`/`setYesterday` date-labeling inconsistency. |
| `app/Traits/HasSort.php` | Shared sort-state trait (`initSort`/`setSort`/`applySort`, `set-mobile-sort` listener) used by all sortable list components — the shared half of the Mobile*Sort duplication pattern. |
| `app/Traits/TestableRenderComponent.php` | Unused testing-helper trait — no Livewire component imports it; dead code. |

## app/View/Components

| Path | Purpose |
|---|---|
| `app/View/Components/Form/CustomerSelect.php` | Blade component rendering a customer `<select>`, queries all customers itself. |
| `app/View/Components/Form/FormInput.php` | Generic labeled text/wire-bindable input Blade component. |
| `app/View/Components/Form/FormLabel.php` | Generic form `<label>` Blade component. |
| `app/View/Components/Form/FormSelect.php` | Generic wire-bindable `<select>` wrapper Blade component. |
| `app/View/Components/Nav/Breadcrumbs.php` | Breadcrumb-trail component, derives segments/section name from the current request path. |

---

## routes/

| Path | Purpose |
|---|---|
| `routes/web.php` | Top-level route file — public `auth` group (no middleware), then everything else behind `AuthMiddleware`, prefixing into each `route-groups/*.php` file per domain; also nests `errors.php` inside the authenticated group (so custom error pages currently require login). |
| `routes/console.php` | Schedules the two standing-order console commands daily (00:01 and 00:02). |
| `routes/test.php` | Dev-only `__test__` prefixed route group → `TestController::testLivewireOrderList`. |
| `routes/errors.php` | Custom `/error/{404,405,400}` routes rendering the custom error views. |
| `routes/route-groups/auth.php` | Login (GET/POST) + logout routes → `AuthController`. |
| `routes/route-groups/customers.php` | Customer CRUD + custom-price sub-routes; includes one route (`deleteCustomPrice`) pointing at a controller method that no longer exists. |
| `routes/route-groups/products.php` | Product CRUD routes (no delete route defined) → `ProductController`. |
| `routes/route-groups/orders.php` | Order CRUD + `create-summary-pdf` (no delete route; deletion is Livewire-only) → `OrderController`. |
| `routes/route-groups/invoices.php` | Invoice index/download/view-inline/create/create-single (no store/update/delete HTTP routes — all invoice mutation happens via Livewire) → `InvoiceController`. |
| `routes/route-groups/standing-orders.php` | Standing-order CRUD (no delete route) → `StandingOrderController`. |
| `routes/route-groups/financial-records.php` | Financial-record CRUD + nested `categories` CRUD sub-group (neither has a delete route) → `FinancialRecordController`/`FinancialCategoryController`. |
| `routes/route-groups/user-settings.php` | Single route: `GET /settings/appearance` → `UserSettingController`. |

---

## database/migrations/

Excludes the two unmodified framework-default migrations (`cache`, `jobs` tables).

| Path | Purpose |
|---|---|
| `database/migrations/0001_01_01_000000_create_users_table.php` | Creates `users` (customized: `user_id` PK, `username` instead of email) + `password_reset_tokens` + `sessions`. |
| `database/migrations/2025_05_17_090607_create_customers_table.php` | Creates `customers` table (PK `customer_id`, starts at 100). |
| `database/migrations/2025_05_17_090614_create_products_table.php` | Creates `products` table (PK `product_id`, starts at 500). |
| `database/migrations/2025_05_17_090620_create_orders_table.php` | Creates `orders` table (PK `order_id`, starts at 200; customer FK, status enum, placed/due dates). |
| `database/migrations/2025_05_17_093236_create_order_product_table.php` | Creates the `order_product` pivot table (qty + unit price per line). |
| `database/migrations/2025_05_17_094743_create_customer_product_prices_table.php` | Creates `customer_product_prices` (per-customer price override, unique on customer+product). |
| `database/migrations/2025_05_29_160546_create_invoices_table.php` | Creates `invoices` table (status enum default `due`, invoice number, issue/due dates, from/to billing window). |
| `database/migrations/2025_05_30_100544_create_standing_orders_table.php` | Creates `standing_orders` (customer FK, `is_active`, `start_from`, `is_forced`). |
| `database/migrations/2025_05_30_100855_create_standing_order_days_table.php` | Creates `standing_order_days` (day-of-week rows per standing order). |
| `database/migrations/2025_05_30_100921_create_standing_order_day_products_table.php` | Creates `standing_order_day_products` (product+qty per standing-order day). |
| `database/migrations/2025_06_07_203441_add_invoice_total_column_to_invoices.php` | Adds nullable `invoice_total` decimal column. |
| `database/migrations/2025_07_30_144429_create_invoice_products_table.php` | Creates `invoice_products` (composite-PK line items, cascade-deletes with invoice). |
| `database/migrations/2025_08_01_123254_add_owner_name_col_to_customers.php` | Adds `customer_business_owner_name`. |
| `database/migrations/2025_08_01_141206_add_daytime_column_to_orders.php` | Adds `is_daytime` boolean to `orders`. |
| `database/migrations/2025_08_02_150552_modify_order_status_column.php` | Re-asserts `order_status` enum values to match `OrderStatus` cases. |
| `database/migrations/2025_08_04_135210_update_order_foreign_key.php` | Changes `invoices.order_id` FK to `nullOnDelete()`. |
| `database/migrations/2025_08_04_135510_update_foreign_order_on_order_products.php` | Changes `order_product.order_id` FK to `cascadeOnDelete()`. |
| `database/migrations/2025_08_13_105529_add_customer_optional_name.php` | Adds `customer_optional_name`. |
| `database/migrations/2025_09_01_095932_create_financial_categories_table.php` | Creates `financial_categories` (no timestamps). |
| `database/migrations/2025_09_01_100247_create_financial_records_table.php` | Creates `financial_records` (amount, date, income/expense type, category FK). |
| `database/migrations/2025_09_01_133444_create_credit_notes_table.php` | Creates `credit_notes` (invoice FK, issue date, number). |
| `database/migrations/2025_09_01_152937_change_invoice_status.php` | Alters `invoice_status` enum to use `InvoiceStatus` case names (`paid`/`due`/`cancelled`) via raw DDL. |
| `database/migrations/2025_09_20_150641_add_trading_name_to_customers.php` | Adds `customer_trading_name`. |
| `database/migrations/2025_09_23_132142_add_delivery_address_to_customers.php` | Adds `customer_delivery_address`. |
| `database/migrations/2025_10_01_172538_create_user_settings_table.php` | Creates `user_settings` (PK = FK `user_id`; color/theme/font-size enum-int columns). |
| `database/migrations/2026_02_19_084803_add_delivery_charge_to_invoices.php` | Adds `invoice_delivery_charge` decimal column, default 0.00. |
| `database/migrations/2026_03_21_093722_add_credit_column_to_invoice.php` | Adds `invoice_credit` decimal(10,4) column. |
| `database/migrations/2026_06_12_131941_add_index_on_orders_table.php` | Adds index on `order_due_at` and composite `(customer_id, order_due_at)` index — supports the filter patterns in `OrderQueryBuilder`/`OrderListService`. |
| `database/migrations/2026_06_21_103142_add_index_to_invoice_table.php` | Adds index on `invoices.customer_id`. |
