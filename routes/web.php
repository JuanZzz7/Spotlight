<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Admin\{DashboardController as AdminDash, UserController as AdminUser, CompanyController as AdminCompany, ProductController as AdminProduct, ReportController, FiscalVerificationController as AdminFiscalVerification, AssistedPurchaseController as AdminAssistedPurchase};
use App\Http\Controllers\Business\{DashboardController as BizDash, CompanyController as BizCompany, ProductController as BizProduct, ExpenseController, ShipmentController as BizShipment, AssistedPurchaseController as BizAssistedPurchase, FormalizationGuideController as BizFormalizationGuide};
use App\Http\Controllers\Business\Billing\{BillingDashboardController as BizBillingDash, FiscalProfileController as BizFiscalProfile, BillingConfigurationController as BizBillingConfig, InvoiceController as BizInvoice, DocumentController as BizDocument, BillingReportController as BizBillingReport};
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Api\{WompiWebhookController, MercadoPagoWebhookController};
use App\Http\Controllers\User\{DashboardController as UserDash, ProfileController, CartController, OrderController, AssistedPurchaseController as UserAssistedPurchase};

// Página pública
Route::get('/', [HomeController::class, 'index'])->name('home');

// Páginas públicas de empresa, productos y rastreo en tiempo real
Route::get('/empresas/{company}',         [PublicController::class, 'company'])->name('public.company');
Route::get('/empresas/{company}/tooltip', [PublicController::class, 'companyTooltip'])->name('public.company.tooltip');
Route::get('/productos/{product}',        [PublicController::class, 'product'])->name('public.product');
Route::post('/productos/{product}/reseñas', [PublicController::class, 'storeReview'])->name('public.product.review')->middleware('auth');
Route::get('/rastreo',                    [PublicController::class, 'trackingPortal'])->name('public.tracking');

// Auth
Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',   [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register',[AuthController::class, 'register']);
Route::post('/logout',  [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Recuperación de Contraseña
Route::get('/forgot-password',        [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password',       [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password',        [PasswordResetController::class, 'reset'])->name('password.update');

// Google OAuth
Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

// Verificación de Email (Google nuevos usuarios)
Route::get('/verify-email',           [EmailVerificationController::class, 'showNotice'])->name('verify.notice');
Route::get('/verify-email/codigo',    [EmailVerificationController::class, 'showForm'])->name('verify.form');
Route::post('/verify-email/codigo',   [EmailVerificationController::class, 'verify'])->name('verify.submit');
Route::post('/verify-email/reenviar', [EmailVerificationController::class, 'resend'])->name('verify.resend');

// Admin
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', fn() => redirect()->route('admin.dashboard'));
    Route::get('/dashboard', [AdminDash::class, 'index'])->name('dashboard');

    Route::resource('users', AdminUser::class);
    Route::patch('users/{user}/toggle', [AdminUser::class, 'toggleActive'])->name('users.toggle');

    Route::get('companies', [AdminCompany::class, 'index'])->name('companies.index');
    Route::patch('companies/{company}/toggle', [AdminCompany::class, 'toggleStatus'])->name('companies.toggle');
    Route::patch('companies/{company}/document-update-status', [AdminCompany::class, 'documentUpdateStatus'])->name('companies.document_update_status');
    Route::delete('companies/{company}', [AdminCompany::class, 'destroy'])->name('companies.destroy');

    // Habilitación y Verificación Fiscal DIAN (Admin)
    Route::get('fiscal-verifications', [AdminFiscalVerification::class, 'index'])->name('fiscal_verifications.index');
    Route::get('fiscal-verifications/{profile}', [AdminFiscalVerification::class, 'show'])->name('fiscal_verifications.show');
    Route::post('fiscal-verifications/{profile}/verify', [AdminFiscalVerification::class, 'verify'])->name('fiscal_verifications.verify');
    Route::post('fiscal-verifications/{profile}/reject', [AdminFiscalVerification::class, 'reject'])->name('fiscal_verifications.reject');
    Route::post('fiscal-verifications/{profile}/suspend', [AdminFiscalVerification::class, 'suspend'])->name('fiscal_verifications.suspend');
    Route::get('fiscal-verifications/{profile}/download/{type}', [AdminFiscalVerification::class, 'downloadDocument'])->name('fiscal_verifications.download');

    // Rutas de productos y reportes financieros restringidas para Administrador (privacidad comercial)
    Route::redirect('products', '/admin/dashboard')->name('products.index');
    Route::redirect('reports', '/admin/dashboard')->name('reports.index');

    // Solicitudes Internacionales Asistidas (Admin)
    Route::get('assisted-purchases', [AdminAssistedPurchase::class, 'index'])->name('assisted_purchases.index');
    Route::get('assisted-purchases/{assistedPurchase}', [AdminAssistedPurchase::class, 'show'])->name('assisted_purchases.show');
});

// Empresario
Route::prefix('business')->name('business.')->middleware(['auth', 'role:business'])->group(function () {
    Route::get('/', [BizDash::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [BizDash::class, 'index']);
    Route::get('sales', [BizInvoice::class, 'sales'])->name('sales')->middleware('colombian.billing');

    Route::get('company/edit',   [BizCompany::class, 'edit'])->name('company.edit');
    Route::post('company/update', [BizCompany::class, 'update'])->name('company.update');
    Route::post('company/request-document-update', [BizCompany::class, 'requestDocumentUpdate'])->name('company.request_document_update');

    Route::get('products/export/pdf', [BizProduct::class, 'exportPdf'])->name('products.export_pdf');
    Route::resource('products', BizProduct::class);

    Route::get('expenses/export/pdf',   [ExpenseController::class, 'exportPdf'])->name('expenses.export_pdf');
    Route::get('expenses',              [ExpenseController::class, 'index'])->name('expenses.index');
    Route::get('expenses/create',       [ExpenseController::class, 'create'])->name('expenses.create');
    Route::post('expenses',             [ExpenseController::class, 'store'])->name('expenses.store');
    Route::get('expenses/{expense}/edit', [ExpenseController::class, 'edit'])->name('expenses.edit');
    Route::put('expenses/{expense}',    [ExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

    // Gestión y despacho de envíos logísticos
    Route::get('shipments',                      [BizShipment::class, 'index'])->name('shipments.index');
    Route::post('shipments/{shipment}/dispatch', [BizShipment::class, 'dispatchOrder'])->name('shipments.dispatch');
    Route::post('shipments/{shipment}/status',   [BizShipment::class, 'updateStatus'])->name('shipments.status');

    // Compras Internacionales Asistidas (Comercio)
    Route::get('assisted-purchases', [BizAssistedPurchase::class, 'index'])->name('assisted_purchases.index');
    Route::get('assisted-purchases/{assistedPurchase}', [BizAssistedPurchase::class, 'show'])->name('assisted_purchases.show');
    Route::put('assisted-purchases/{assistedPurchase}', [BizAssistedPurchase::class, 'updateConditions'])->name('assisted_purchases.update');
    Route::post('assisted-purchases/{assistedPurchase}/conditions', [BizAssistedPurchase::class, 'updateConditions'])->name('assisted_purchases.update_conditions');
    Route::post('assisted-purchases/{assistedPurchase}/confirm-payment', [BizAssistedPurchase::class, 'confirmPayment'])->name('assisted_purchases.confirm_payment');

    // Centro de Tutoriales de Formalización (Comercios Informales)
    Route::get('formalization/guide', [BizFormalizationGuide::class, 'index'])->name('formalization.guide');

    // Módulo de Facturación Electrónica DIAN (Exclusivo comercios formalizados Colombia)
    Route::prefix('billing')->name('billing.')->middleware('colombian.billing')->group(function () {
        Route::get('/', [BizBillingDash::class, 'index'])->name('index');
        Route::get('profile', [BizFiscalProfile::class, 'show'])->name('profile');
        Route::post('profile', [BizFiscalProfile::class, 'update'])->name('profile.update');

        Route::get('configuration', [BizBillingConfig::class, 'edit'])->name('configuration');
        Route::post('configuration', [BizBillingConfig::class, 'update'])->name('configuration.update');
        Route::post('configuration/test', [BizBillingConfig::class, 'testConnection'])->name('configuration.test');

        Route::get('sales', [BizInvoice::class, 'sales'])->name('sales');
        Route::get('invoices/create-from-order/{order}', [BizInvoice::class, 'createFromOrder'])->name('invoices.create_from_order');
        Route::post('invoices/store-from-order/{order}', [BizInvoice::class, 'storeFromOrder'])->name('invoices.store_from_order');
        Route::get('invoices/create', [BizInvoice::class, 'create'])->name('invoices.create');
        Route::post('invoices', [BizInvoice::class, 'store'])->name('invoices.store');

        Route::get('documents', [BizDocument::class, 'index'])->name('documents.index');
        Route::get('documents/{document}', [BizDocument::class, 'show'])->name('documents.show');
        Route::get('documents/{document}/download-xml', [BizDocument::class, 'downloadXml'])->name('documents.download_xml');
        Route::get('documents/{document}/download-pdf', [BizDocument::class, 'downloadPdf'])->name('documents.download_pdf');
        Route::post('documents/{document}/sync', [BizDocument::class, 'syncStatus'])->name('documents.sync');
        Route::post('documents/{document}/credit-note', [BizDocument::class, 'createCreditNote'])->name('documents.credit_note');

        Route::get('reports', [BizBillingReport::class, 'index'])->name('reports.index');
    });
});

// Usuario
Route::prefix('user')->name('user.')->middleware(['auth', 'role:user'])->group(function () {
    Route::get('/', fn() => redirect()->route('user.dashboard'));
    Route::get('/dashboard', [UserDash::class, 'index'])->name('dashboard');
    Route::get('/panel',     [UserDash::class, 'panel'])->name('panel');
    Route::get('/map',       [UserDash::class, 'map'])->name('map');

    Route::get('profile/edit',   [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile/update', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('cart',                     [CartController::class, 'index'])->name('cart.index');
    Route::get('cart/checkout',            [CheckoutController::class, 'index'])->name('cart.checkout');
    Route::post('cart/checkout/process',    [CartController::class, 'processPayment'])->name('cart.payment.process');
    
    // Proceso oficial Checkout Marketplace Wompi / Mercado Pago
    Route::get('/checkout',                     [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process',            [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/result/{reference}',  [CheckoutController::class, 'result'])->name('checkout.result');

    Route::post('cart/{product}',          [CartController::class, 'add'])->name('cart.add');
    Route::delete('cart/{product}',        [CartController::class, 'remove'])->name('cart.remove');
    Route::patch('cart/{product}',         [CartController::class, 'update'])->name('cart.update');

    Route::get('orders',                  [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}/tracking', [OrderController::class, 'tracking'])->name('orders.tracking');
    Route::get('orders/{order}/invoice',  [OrderController::class, 'invoice'])->name('orders.invoice');

    // Solicitudes Internacionales Asistidas (Comprador)
    Route::get('assisted-purchases', [UserAssistedPurchase::class, 'index'])->name('assisted_purchases.index');
    Route::get('assisted-purchases/crear/{product}', [UserAssistedPurchase::class, 'create'])->name('assisted_purchases.create');
    Route::post('assisted-purchases/crear/{product}', [UserAssistedPurchase::class, 'store'])->name('assisted_purchases.store');
    Route::get('assisted-purchases/{assistedPurchase}', [UserAssistedPurchase::class, 'show'])->name('assisted_purchases.show');
    Route::post('assisted-purchases/{assistedPurchase}/payment', [UserAssistedPurchase::class, 'recordPayment'])->name('assisted_purchases.payment');
    Route::post('assisted-purchases/{assistedPurchase}/cancel', [UserAssistedPurchase::class, 'cancel'])->name('assisted_purchases.cancel');
});

// Proceso oficial Checkout Marketplace (Rutas principales)
Route::middleware(['auth'])->group(function () {
    Route::get('/checkout',                     [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process',            [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/result/{reference}',  [CheckoutController::class, 'result'])->name('checkout.result');

    // PSE Flujo de Autorización
    Route::get('/checkout/pse/authorize/{reference}', [CheckoutController::class, 'showPseAuthorize'])->name('checkout.pse.authorize');
    Route::post('/checkout/pse/approve/{reference}',   [CheckoutController::class, 'approvePse'])->name('checkout.pse.approve');
    Route::post('/checkout/pse/cancel/{reference}',    [CheckoutController::class, 'cancelPse'])->name('checkout.pse.cancel');

    // Código QR Dinámico (Bancolombia / Nequi)
    Route::get('/checkout/qr/{reference}',             [CheckoutController::class, 'showQr'])->name('checkout.qr.show');
    Route::post('/checkout/qr/confirm/{reference}',    [CheckoutController::class, 'confirmQr'])->name('checkout.qr.confirm');
    Route::get('/checkout/qr/status/{reference}',      [CheckoutController::class, 'checkQrStatus'])->name('checkout.qr.status');

    // Solicitud de Compra Internacional Asistida
    Route::get('/productos/{product}/compra-asistida', [UserAssistedPurchase::class, 'create'])->name('assisted_purchases.create');
    Route::post('/productos/{product}/compra-asistida', [UserAssistedPurchase::class, 'store'])->name('assisted_purchases.store');
});

// Rutas de Webhook Wompi Colombia (públicas, protegidas por firma SHA-256)
Route::post('/api/payments/wompi/webhook', [WompiWebhookController::class, 'handle'])->name('api.payments.wompi.webhook');
Route::post('/payments/wompi/webhook',     [WompiWebhookController::class, 'handle'])->name('payments.wompi.webhook');

// Rutas de Webhook Mercado Pago México (públicas, protegidas por firma x-signature)
Route::post('/api/payments/mercadopago/webhook', [MercadoPagoWebhookController::class, 'handle'])->name('api.payments.mercadopago.webhook');
Route::post('/payments/mercadopago/webhook',     [MercadoPagoWebhookController::class, 'handle'])->name('payments.mercadopago.webhook');


// Chat Global
Route::middleware(['auth'])->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/api/unread-count', [ChatController::class, 'unreadCount'])->name('chat.unread.count');
    Route::get('/chat/{receiver}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{receiver}', [ChatController::class, 'send'])->name('chat.send');
    Route::get('/chat/api/{receiver}', [ChatController::class, 'getMessages'])->name('chat.messages');
});

// AI Chatbot Widget (available to all authenticated users)
Route::middleware(['auth'])->group(function () {
    Route::get('/chatbot', [ChatbotController::class, 'index'])->name('chatbot.index');
    Route::post('/chatbot/message',     [ChatbotController::class, 'message'])->name('chatbot.message');
    Route::post('/chatbot/detect-lang', [ChatbotController::class, 'detectLang'])->name('chatbot.detect-lang');
});
