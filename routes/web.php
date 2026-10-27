<?php

use App\Http\Controllers\BillingNoteController;
use App\Http\Controllers\CreditNoteController;
use App\Http\Controllers\DebitNoteController;
use App\Http\Controllers\DeliveryNoteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OpportunityController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\PurchaseRequisitionController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\Settings\LeadAssignmentRuleController;
use App\Http\Controllers\Settings\LeadChannelController;
use App\Http\Controllers\Settings\LeadClosedReasonController;
use App\Http\Controllers\Settings\LeadPipelineSettingController;
use App\Http\Controllers\Settings\LeadProjectTypeController;
use App\Http\Controllers\Settings\MarketingAdController;
use App\Http\Controllers\Settings\MarketingAdUnitController;
use App\Http\Controllers\Settings\MarketingArtworkController;
use App\Http\Controllers\Settings\MarketingObjectiveController;
use App\Http\Controllers\Settings\MarketingPlatformController;
use App\Http\Controllers\Settings\ProductServiceCategoryController;
use App\Http\Controllers\Settings\ProductServiceItemController;
use App\Http\Controllers\Settings\ProductServiceQuestionController;
use App\Http\Controllers\TaxInvoiceController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware('auth');

Route::get('/', function () {
    return Auth::check() ? redirect()->route('home') : redirect()->route('login');
});

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('forgot-password', [LoginController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('forgot-password', [LoginController::class, 'sendResetLinkEmail'])->name('password.email');

Route::get('locations/amphures', [LocationController::class, 'amphures'])->name('locations.amphures');
Route::get('locations/tambons', [LocationController::class, 'tambons'])->name('locations.tambons');
Route::get('locations/thai-location-inter', [LocationController::class, 'thaiLocationInter'])->name('locations.thaiLocationInter');

Route::get('register', [LoginController::class, 'showRegisterForm'])->name('register');
Route::post('register/step1', [LoginController::class, 'processStep1'])->name('register.step1');
Route::get('register/create-business', [LoginController::class, 'showCreateBusinessForm'])->name('register.step2');
Route::post('register', [LoginController::class, 'register'])->name('register.store');
Route::get('app/{branch}', [HomeController::class, 'openAppContext'])
    ->name('app.home')
    ->middleware(['auth', 'app.context']);
Route::get('office', [HomeController::class, 'redirectToOfficeContext'])
    ->name('office.redirect')
    ->middleware('auth');

Route::prefix('app/{branch}')->middleware(['auth', 'app.context'])->group(function () {
    Route::prefix('office')->name('office.')->group(function () {
        Route::view('/shortcut', 'office.shortcut')->name('shortcut');
        Route::view('/', 'office.shortcut')->name('index');
        Route::view('/profile', 'office.profile.index')->name('profile');
        Route::view('/pages-profile', 'office.profile.index')->name('pages-profile');
        Route::view('/pages-profile-settings', 'office.profile.index')->name('pages-profile-settings');

        Route::view('/advance-ui-animation', 'office.advance-ui-animation')->name('advance-ui-animation');
        Route::view('/advance-ui-highlight', 'office.advance-ui-highlight')->name('advance-ui-highlight');
        Route::view('/advance-ui-nestable', 'office.advance-ui-nestable')->name('advance-ui-nestable');
        Route::view('/advance-ui-ratings', 'office.advance-ui-ratings')->name('advance-ui-ratings');
        Route::view('/advance-ui-scrollbar', 'office.advance-ui-scrollbar')->name('advance-ui-scrollbar');
        Route::view('/advance-ui-scrollspy', 'office.advance-ui-scrollspy')->name('advance-ui-scrollspy');
        Route::view('/advance-ui-sweetalerts', 'office.advance-ui-sweetalerts')->name('advance-ui-sweetalerts');
        Route::view('/advance-ui-swiper', 'office.advance-ui-swiper')->name('advance-ui-swiper');
        Route::view('/advance-ui-tour', 'office.advance-ui-tour')->name('advance-ui-tour');
        Route::view('/apps-api-key', 'office.apps-api-key')->name('apps-api-key');
        Route::view('/apps-calendar', 'office.apps-calendar')->name('apps-calendar');
        Route::view('/apps-calendar-month-grid', 'office.apps-calendar-month-grid')->name('apps-calendar-month-grid');
        Route::view('/apps-chat', 'office.apps-chat')->name('apps-chat');
        Route::view('/apps-crm-companies', 'office.apps-crm-companies')->name('apps-crm-companies');
        Route::view('/apps-crm-contacts', 'office.apps-crm-contacts')->name('apps-crm-contacts');
        Route::view('/apps-crm-deals', 'office.apps-crm-deals')->name('apps-crm-deals');
        Route::view('/apps-crm-leads-demo', 'office.apps-crm-leads')->name('apps-crm-leads-demo');
        Route::view('/apps-crypto-buy-sell', 'office.apps-crypto-buy-sell')->name('apps-crypto-buy-sell');
        Route::view('/apps-crypto-ico', 'office.apps-crypto-ico')->name('apps-crypto-ico');
        Route::view('/apps-crypto-kyc', 'office.apps-crypto-kyc')->name('apps-crypto-kyc');
        Route::view('/apps-crypto-orders', 'office.apps-crypto-orders')->name('apps-crypto-orders');
        Route::view('/apps-crypto-transactions', 'office.apps-crypto-transactions')->name('apps-crypto-transactions');
        Route::view('/apps-crypto-wallet', 'office.apps-crypto-wallet')->name('apps-crypto-wallet');
        Route::view('/apps-ecommerce-add-product', 'office.apps-ecommerce-add-product')->name('apps-ecommerce-add-product');
        Route::view('/apps-ecommerce-cart', 'office.apps-ecommerce-cart')->name('apps-ecommerce-cart');
        Route::view('/apps-ecommerce-checkout', 'office.apps-ecommerce-checkout')->name('apps-ecommerce-checkout');
        Route::view('/apps-ecommerce-customers', 'office.apps-ecommerce-customers')->name('apps-ecommerce-customers');
        Route::view('/apps-ecommerce-order-details', 'office.apps-ecommerce-order-details')->name('apps-ecommerce-order-details');
        Route::view('/apps-ecommerce-orders', 'office.apps-ecommerce-orders')->name('apps-ecommerce-orders');
        Route::view('/apps-ecommerce-product-details', 'office.apps-ecommerce-product-details')->name('apps-ecommerce-product-details');
        Route::view('/apps-ecommerce-products', 'office.apps-ecommerce-products')->name('apps-ecommerce-products');
        Route::view('/apps-ecommerce-seller-details', 'office.apps-ecommerce-seller-details')->name('apps-ecommerce-seller-details');
        Route::view('/apps-ecommerce-sellers', 'office.apps-ecommerce-sellers')->name('apps-ecommerce-sellers');
        Route::view('/apps-email-basic', 'office.apps-email-basic')->name('apps-email-basic');
        Route::view('/apps-email-ecommerce', 'office.apps-email-ecommerce')->name('apps-email-ecommerce');
        Route::view('/apps-file-manager', 'office.apps-file-manager')->name('apps-file-manager');
        Route::view('/apps-invoices-create', 'office.apps-invoices-create')->name('apps-invoices-create');
        Route::view('/apps-invoices-details', 'office.apps-invoices-details')->name('apps-invoices-details');
        Route::view('/apps-invoices-list', 'office.apps-invoices-list')->name('apps-invoices-list');
        Route::view('/apps-job-application', 'office.apps-job-application')->name('apps-job-application');
        Route::view('/apps-job-candidate-grid', 'office.apps-job-candidate-grid')->name('apps-job-candidate-grid');
        Route::view('/apps-job-candidate-lists', 'office.apps-job-candidate-lists')->name('apps-job-candidate-lists');
        Route::view('/apps-job-categories', 'office.apps-job-categories')->name('apps-job-categories');
        Route::view('/apps-job-companies-lists', 'office.apps-job-companies-lists')->name('apps-job-companies-lists');
        Route::view('/apps-job-details', 'office.apps-job-details')->name('apps-job-details');
        Route::view('/apps-job-grid-lists', 'office.apps-job-grid-lists')->name('apps-job-grid-lists');
        Route::view('/apps-job-lists', 'office.apps-job-lists')->name('apps-job-lists');
        Route::view('/apps-job-new', 'office.apps-job-new')->name('apps-job-new');
        Route::view('/apps-job-statistics', 'office.apps-job-statistics')->name('apps-job-statistics');
        Route::view('/apps-mailbox', 'office.apps-mailbox')->name('apps-mailbox');
        Route::view('/apps-nft-auction', 'office.apps-nft-auction')->name('apps-nft-auction');
        Route::view('/apps-nft-collections', 'office.apps-nft-collections')->name('apps-nft-collections');
        Route::view('/apps-nft-create', 'office.apps-nft-create')->name('apps-nft-create');
        Route::view('/apps-nft-creators', 'office.apps-nft-creators')->name('apps-nft-creators');
        Route::view('/apps-nft-explore', 'office.apps-nft-explore')->name('apps-nft-explore');
        Route::view('/apps-nft-item-details', 'office.apps-nft-item-details')->name('apps-nft-item-details');
        Route::view('/apps-nft-marketplace', 'office.apps-nft-marketplace')->name('apps-nft-marketplace');
        Route::view('/apps-nft-ranking', 'office.apps-nft-ranking')->name('apps-nft-ranking');
        Route::view('/apps-nft-wallet', 'office.apps-nft-wallet')->name('apps-nft-wallet');
        Route::view('/apps-projects-create', 'office.apps-projects-create')->name('apps-projects-create');
        Route::view('/apps-projects-list', 'office.apps-projects-list')->name('apps-projects-list');
        Route::view('/apps-projects-overview', 'office.apps-projects-overview')->name('apps-projects-overview');
        Route::view('/apps-tasks-details', 'office.apps-tasks-details')->name('apps-tasks-details');
        Route::view('/apps-tasks-kanban', 'office.apps-tasks-kanban')->name('apps-tasks-kanban');
        Route::view('/apps-tasks-list-view', 'office.apps-tasks-list-view')->name('apps-tasks-list-view');
        Route::view('/apps-tickets-details', 'office.apps-tickets-details')->name('apps-tickets-details');
        Route::view('/apps-tickets-list', 'office.apps-tickets-list')->name('apps-tickets-list');
        Route::view('/apps-todo', 'office.apps-todo')->name('apps-todo');
        Route::view('/auth-404-alt', 'office.auth-404-alt')->name('auth-404-alt');
        Route::view('/auth-404-basic', 'office.auth-404-basic')->name('auth-404-basic');
        Route::view('/auth-404-cover', 'office.auth-404-cover')->name('auth-404-cover');
        Route::view('/auth-500', 'office.auth-500')->name('auth-500');
        Route::view('/auth-lockscreen-basic', 'office.auth-lockscreen-basic')->name('auth-lockscreen-basic');
        Route::view('/auth-lockscreen-cover', 'office.auth-lockscreen-cover')->name('auth-lockscreen-cover');
        Route::view('/auth-logout-basic', 'office.auth-logout-basic')->name('auth-logout-basic');
        Route::view('/auth-logout-cover', 'office.auth-logout-cover')->name('auth-logout-cover');
        Route::view('/auth-offline', 'office.auth-offline')->name('auth-offline');
        Route::view('/auth-pass-change-basic', 'office.auth-pass-change-basic')->name('auth-pass-change-basic');
        Route::view('/auth-pass-change-cover', 'office.auth-pass-change-cover')->name('auth-pass-change-cover');
        Route::view('/auth-pass-reset-basic', 'office.auth-pass-reset-basic')->name('auth-pass-reset-basic');
        Route::view('/auth-pass-reset-cover', 'office.auth-pass-reset-cover')->name('auth-pass-reset-cover');
        Route::view('/auth-signin-basic', 'office.auth-signin-basic')->name('auth-signin-basic');
        Route::view('/auth-signin-cover', 'office.auth-signin-cover')->name('auth-signin-cover');
        Route::view('/auth-signup-basic', 'office.auth-signup-basic')->name('auth-signup-basic');
        Route::view('/auth-signup-cover', 'office.auth-signup-cover')->name('auth-signup-cover');
        Route::view('/auth-success-msg-basic', 'office.auth-success-msg-basic')->name('auth-success-msg-basic');
        Route::view('/auth-success-msg-cover', 'office.auth-success-msg-cover')->name('auth-success-msg-cover');
        Route::view('/auth-twostep-basic', 'office.auth-twostep-basic')->name('auth-twostep-basic');
        Route::view('/auth-twostep-cover', 'office.auth-twostep-cover')->name('auth-twostep-cover');
        Route::view('/charts-apex-area', 'office.charts-apex-area')->name('charts-apex-area');
        Route::view('/charts-apex-bar', 'office.charts-apex-bar')->name('charts-apex-bar');
        Route::view('/charts-apex-boxplot', 'office.charts-apex-boxplot')->name('charts-apex-boxplot');
        Route::view('/charts-apex-bubble', 'office.charts-apex-bubble')->name('charts-apex-bubble');
        Route::view('/charts-apex-candlestick', 'office.charts-apex-candlestick')->name('charts-apex-candlestick');
        Route::view('/charts-apex-column', 'office.charts-apex-column')->name('charts-apex-column');
        Route::view('/charts-apex-funnel', 'office.charts-apex-funnel')->name('charts-apex-funnel');
        Route::view('/charts-apex-heatmap', 'office.charts-apex-heatmap')->name('charts-apex-heatmap');
        Route::view('/charts-apex-line', 'office.charts-apex-line')->name('charts-apex-line');
        Route::view('/charts-apex-mixed', 'office.charts-apex-mixed')->name('charts-apex-mixed');
        Route::view('/charts-apex-pie', 'office.charts-apex-pie')->name('charts-apex-pie');
        Route::view('/charts-apex-polar', 'office.charts-apex-polar')->name('charts-apex-polar');
        Route::view('/charts-apex-radar', 'office.charts-apex-radar')->name('charts-apex-radar');
        Route::view('/charts-apex-radialbar', 'office.charts-apex-radialbar')->name('charts-apex-radialbar');
        Route::view('/charts-apex-range-area', 'office.charts-apex-range-area')->name('charts-apex-range-area');
        Route::view('/charts-apex-scatter', 'office.charts-apex-scatter')->name('charts-apex-scatter');
        Route::view('/charts-apex-timeline', 'office.charts-apex-timeline')->name('charts-apex-timeline');
        Route::view('/charts-apex-treemap', 'office.charts-apex-treemap')->name('charts-apex-treemap');
        Route::view('/charts-chartjs', 'office.charts-chartjs')->name('charts-chartjs');
        Route::view('/charts-echarts', 'office.charts-echarts')->name('charts-echarts');
        Route::view('/dashboard-analytics', 'office.dashboard-analytics')->name('dashboard-analytics');
        Route::view('/dashboard-crm', 'office.dashboard-crm')->name('dashboard-crm');
        Route::view('/dashboard-crypto', 'office.dashboard-crypto')->name('dashboard-crypto');
        Route::view('/dashboard-job', 'office.dashboard-job')->name('dashboard-job');
        Route::view('/dashboard-nft', 'office.dashboard-nft')->name('dashboard-nft');
        Route::view('/dashboard-projects', 'office.dashboard-projects')->name('dashboard-projects');
        Route::view('/forms-advanced', 'office.forms-advanced')->name('forms-advanced');
        Route::view('/forms-checkboxs-radios', 'office.forms-checkboxs-radios')->name('forms-checkboxs-radios');
        Route::view('/forms-editors', 'office.forms-editors')->name('forms-editors');
        Route::view('/forms-elements', 'office.forms-elements')->name('forms-elements');
        Route::view('/forms-file-uploads', 'office.forms-file-uploads')->name('forms-file-uploads');
        Route::view('/forms-layouts', 'office.forms-layouts')->name('forms-layouts');
        Route::view('/forms-masks', 'office.forms-masks')->name('forms-masks');
        Route::view('/forms-pickers', 'office.forms-pickers')->name('forms-pickers');
        Route::view('/forms-range-sliders', 'office.forms-range-sliders')->name('forms-range-sliders');
        Route::view('/forms-select', 'office.forms-select')->name('forms-select');
        Route::view('/forms-select2', 'office.forms-select2')->name('forms-select2');
        Route::view('/forms-validation', 'office.forms-validation')->name('forms-validation');
        Route::view('/forms-wizard', 'office.forms-wizard')->name('forms-wizard');
        Route::view('/icons-boxicons', 'office.icons-boxicons')->name('icons-boxicons');
        Route::view('/icons-crypto', 'office.icons-crypto')->name('icons-crypto');
        Route::view('/icons-feather', 'office.icons-feather')->name('icons-feather');
        Route::view('/icons-lineawesome', 'office.icons-lineawesome')->name('icons-lineawesome');
        Route::view('/icons-materialdesign', 'office.icons-materialdesign')->name('icons-materialdesign');
        Route::view('/icons-remix', 'office.icons-remix')->name('icons-remix');
        Route::view('/job-landing', 'office.job-landing')->name('job-landing');
        Route::view('/landing', 'office.landing')->name('landing');
        Route::view('/layouts-detached', 'office.layouts-detached')->name('layouts-detached');
        Route::view('/layouts-horizontal', 'office.layouts-horizontal')->name('layouts-horizontal');
        Route::view('/layouts-two-column', 'office.layouts-two-column')->name('layouts-two-column');
        Route::view('/layouts-vertical-hovered', 'office.layouts-vertical-hovered')->name('layouts-vertical-hovered');
        Route::view('/maps-google', 'office.maps-google')->name('maps-google');
        Route::view('/maps-leaflet', 'office.maps-leaflet')->name('maps-leaflet');
        Route::view('/maps-vector', 'office.maps-vector')->name('maps-vector');
        Route::view('/nft-landing', 'office.nft-landing')->name('nft-landing');
        Route::view('/pages-coming-soon', 'office.pages-coming-soon')->name('pages-coming-soon');
        Route::view('/pages-faqs', 'office.pages-faqs')->name('pages-faqs');
        Route::view('/pages-gallery', 'office.pages-gallery')->name('pages-gallery');
        Route::view('/pages-maintenance', 'office.pages-maintenance')->name('pages-maintenance');
        Route::view('/pages-pricing', 'office.pages-pricing')->name('pages-pricing');
        Route::view('/pages-privacy-policy', 'office.pages-privacy-policy')->name('pages-privacy-policy');
        Route::view('/pages-search-results', 'office.pages-search-results')->name('pages-search-results');
        Route::view('/pages-sitemap', 'office.pages-sitemap')->name('pages-sitemap');
        Route::view('/pages-starter', 'office.pages-starter')->name('pages-starter');
        Route::view('/pages-team', 'office.pages-team')->name('pages-team');
        Route::view('/pages-term-conditions', 'office.pages-term-conditions')->name('pages-term-conditions');
        Route::view('/pages-timeline', 'office.pages-timeline')->name('pages-timeline');
        Route::view('/tables-basic', 'office.tables-basic')->name('tables-basic');
        Route::view('/tables-datatables', 'office.tables-datatables')->name('tables-datatables');
        Route::view('/tables-gridjs', 'office.tables-gridjs')->name('tables-gridjs');
        Route::view('/tables-listjs', 'office.tables-listjs')->name('tables-listjs');
        Route::view('/ui-accordions', 'office.ui-accordions')->name('ui-accordions');
        Route::view('/ui-alerts', 'office.ui-alerts')->name('ui-alerts');
        Route::view('/ui-badges', 'office.ui-badges')->name('ui-badges');
        Route::view('/ui-buttons', 'office.ui-buttons')->name('ui-buttons');
        Route::view('/ui-cards', 'office.ui-cards')->name('ui-cards');
        Route::view('/ui-carousel', 'office.ui-carousel')->name('ui-carousel');
        Route::view('/ui-colors', 'office.ui-colors')->name('ui-colors');
        Route::view('/ui-dropdowns', 'office.ui-dropdowns')->name('ui-dropdowns');
        Route::view('/ui-embed-video', 'office.ui-embed-video')->name('ui-embed-video');
        Route::view('/ui-general', 'office.ui-general')->name('ui-general');
        Route::view('/ui-grid', 'office.ui-grid')->name('ui-grid');
        Route::view('/ui-images', 'office.ui-images')->name('ui-images');
        Route::view('/ui-links', 'office.ui-links')->name('ui-links');
        Route::view('/ui-lists', 'office.ui-lists')->name('ui-lists');
        Route::view('/ui-media', 'office.ui-media')->name('ui-media');
        Route::view('/ui-modals', 'office.ui-modals')->name('ui-modals');
        Route::view('/ui-notifications', 'office.ui-notifications')->name('ui-notifications');
        Route::view('/ui-offcanvas', 'office.ui-offcanvas')->name('ui-offcanvas');
        Route::view('/ui-placeholders', 'office.ui-placeholders')->name('ui-placeholders');
        Route::view('/ui-progress', 'office.ui-progress')->name('ui-progress');
        Route::view('/ui-ribbons', 'office.ui-ribbons')->name('ui-ribbons');
        Route::view('/ui-tabs', 'office.ui-tabs')->name('ui-tabs');
        Route::view('/ui-typography', 'office.ui-typography')->name('ui-typography');
        Route::view('/ui-utilities', 'office.ui-utilities')->name('ui-utilities');
        Route::view('/widgets', 'office.widgets')->name('widgets');

        Route::view('/business', 'office.business.index')->name('business.index');
        Route::view('/mybusiness', 'office.business.index')->name('mybusiness');
        Route::view('/business-show/{id?}', 'office.business.show')->name('business-show');

        Route::get('/partners', [PartnerController::class, 'index'])->name('partners.index');
        Route::get('/partners/create', [PartnerController::class, 'create'])->name('partners.create');
        Route::post('/partners', [PartnerController::class, 'store'])->name('partners.store');
        Route::get('/partners/{partner}', [PartnerController::class, 'show'])->name('partners.show');
        Route::put('/partners/{partner}', [PartnerController::class, 'update'])->name('partners.update');
        Route::put('/partners/{partner}/branches/{branch}', [PartnerController::class, 'updateBranch'])->name('partners.branches.update');
        Route::patch('/partners/{partner}/status', [PartnerController::class, 'toggleStatus'])->name('partners.status');
        Route::get('/partner-create', [PartnerController::class, 'create']);
        Route::get('/partner-show/{partner}', [PartnerController::class, 'show'])->name('partner-show');

        Route::resource('leads', LeadController::class);
        Route::post('leads/{lead}/status', [LeadController::class, 'changeStatus'])->name('leads.status');
        Route::post('leads/{lead}/assign', [LeadController::class, 'assign'])->name('leads.assign');
        Route::post('leads/{lead}/contacts', [LeadController::class, 'storeContact'])->name('leads.contacts.store');
        Route::post('leads/{lead}/activities', [LeadController::class, 'storeActivity'])->name('leads.activities.store');
        Route::put('leads/{lead}/activities/{activity}', [LeadController::class, 'updateActivity'])->name('leads.activities.update');
        Route::delete('leads/{lead}/activities/{activity}', [LeadController::class, 'destroyActivity'])->name('leads.activities.destroy');
        Route::post('leads/{lead}/convert', [LeadController::class, 'convert'])->name('leads.convert');

        Route::get('locations/amphures', [LocationController::class, 'amphures'])->name('locations.amphures');
        Route::get('locations/tambons', [LocationController::class, 'tambons'])->name('locations.tambons');

        Route::resource('opportunities', OpportunityController::class);
        Route::get('quotations', [QuotationController::class, 'index'])->name('quotations.index');
        Route::get('quotations/create', [QuotationController::class, 'createDirect'])->name('quotations.create');
        Route::get('quotations/create/standalone', [QuotationController::class, 'createStandalone'])->name('quotations.create.standalone');
        Route::post('quotations/standalone', [QuotationController::class, 'storeStandalone'])->name('quotations.store.standalone');
        Route::get('opportunities/{opportunity}/quotations/create', [QuotationController::class, 'create'])
            ->name('opportunities.quotations.create');
        Route::post('opportunities/{opportunity}/quotations', [QuotationController::class, 'store'])
            ->name('opportunities.quotations.store');
        Route::get('quotations/{quotation}/edit', [QuotationController::class, 'edit'])->name('quotations.edit');
        Route::put('quotations/{quotation}', [QuotationController::class, 'update'])->name('quotations.update');
        Route::delete('quotations/{quotation}', [QuotationController::class, 'destroy'])->name('quotations.destroy');
        Route::get('quotations/{quotation}', [QuotationController::class, 'show'])->name('quotations.show');
        Route::post('quotations/{quotation}/send', [QuotationController::class, 'send'])->name('quotations.send');
        Route::post('quotations/{quotation}/accept', [QuotationController::class, 'accept'])->name('quotations.accept');
        Route::post('quotations/{quotation}/reject', [QuotationController::class, 'reject'])->name('quotations.reject');
        Route::post('quotations/{quotation}/cancel', [QuotationController::class, 'cancel'])->name('quotations.cancel');

        Route::get('sales-orders', [SalesOrderController::class, 'index'])->name('sales-orders.index');
        Route::get('sales-orders/create', [SalesOrderController::class, 'createDirect'])->name('sales-orders.create');
        Route::post('sales-orders/standalone', [SalesOrderController::class, 'storeStandalone'])->name('sales-orders.store.standalone');
        Route::get('quotations/{quotation}/sales-orders/create', [SalesOrderController::class, 'create'])
            ->name('quotations.sales-orders.create');
        Route::post('quotations/{quotation}/sales-orders', [SalesOrderController::class, 'store'])
            ->name('quotations.sales-orders.store');
        Route::get('sales-orders/{salesOrder}/edit', [SalesOrderController::class, 'edit'])->name('sales-orders.edit');
        Route::put('sales-orders/{salesOrder}', [SalesOrderController::class, 'update'])->name('sales-orders.update');
        Route::delete('sales-orders/{salesOrder}', [SalesOrderController::class, 'destroy'])->name('sales-orders.destroy');
        Route::get('sales-orders/{salesOrder}', [SalesOrderController::class, 'show'])->name('sales-orders.show');
        Route::post('sales-orders/{salesOrder}/confirm', [SalesOrderController::class, 'confirm'])->name('sales-orders.confirm');
        Route::post('sales-orders/{salesOrder}/amend', [SalesOrderController::class, 'amend'])->name('sales-orders.amend');
        Route::post('sales-orders/{salesOrder}/cancel', [SalesOrderController::class, 'cancel'])->name('sales-orders.cancel');

        Route::get('purchase-requisitions', [PurchaseRequisitionController::class, 'index'])->name('purchase-requisitions.index');
        Route::get('purchase-requisitions/create', [PurchaseRequisitionController::class, 'createDirect'])->name('purchase-requisitions.create');
        Route::post('purchase-requisitions/standalone', [PurchaseRequisitionController::class, 'storeStandalone'])->name('purchase-requisitions.store.standalone');
        Route::get('purchase-requisitions/{purchaseRequisition}/edit', [PurchaseRequisitionController::class, 'edit'])->name('purchase-requisitions.edit');
        Route::put('purchase-requisitions/{purchaseRequisition}', [PurchaseRequisitionController::class, 'update'])->name('purchase-requisitions.update');
        Route::delete('purchase-requisitions/{purchaseRequisition}', [PurchaseRequisitionController::class, 'destroy'])->name('purchase-requisitions.destroy');
        Route::get('purchase-requisitions/{purchaseRequisition}', [PurchaseRequisitionController::class, 'show'])->name('purchase-requisitions.show');
        Route::post('purchase-requisitions/{purchaseRequisition}/submit', [PurchaseRequisitionController::class, 'submit'])->name('purchase-requisitions.submit');
        Route::post('purchase-requisitions/{purchaseRequisition}/approve', [PurchaseRequisitionController::class, 'approve'])->name('purchase-requisitions.approve');
        Route::post('purchase-requisitions/{purchaseRequisition}/reject', [PurchaseRequisitionController::class, 'reject'])->name('purchase-requisitions.reject');
        Route::post('purchase-requisitions/{purchaseRequisition}/cancel', [PurchaseRequisitionController::class, 'cancel'])->name('purchase-requisitions.cancel');

        Route::get('purchase-orders', [PurchaseOrderController::class, 'index'])->name('purchase-orders.index');
        Route::get('purchase-orders/create', [PurchaseOrderController::class, 'createDirect'])->name('purchase-orders.create');
        Route::post('purchase-orders/standalone', [PurchaseOrderController::class, 'storeStandalone'])->name('purchase-orders.store.standalone');
        Route::get('purchase-requisitions/{purchaseRequisition}/purchase-orders/create', [PurchaseOrderController::class, 'create'])
            ->name('purchase-requisitions.purchase-orders.create');
        Route::post('purchase-requisitions/{purchaseRequisition}/purchase-orders', [PurchaseOrderController::class, 'store'])
            ->name('purchase-requisitions.purchase-orders.store');
        Route::get('purchase-orders/{purchaseOrder}/edit', [PurchaseOrderController::class, 'edit'])->name('purchase-orders.edit');
        Route::put('purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'update'])->name('purchase-orders.update');
        Route::delete('purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'destroy'])->name('purchase-orders.destroy');
        Route::get('purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('purchase-orders.show');
        Route::post('purchase-orders/{purchaseOrder}/confirm', [PurchaseOrderController::class, 'confirm'])->name('purchase-orders.confirm');
        Route::post('purchase-orders/{purchaseOrder}/amend', [PurchaseOrderController::class, 'amend'])->name('purchase-orders.amend');
        Route::post('purchase-orders/{purchaseOrder}/cancel', [PurchaseOrderController::class, 'cancel'])->name('purchase-orders.cancel');

        Route::get('billing-notes', [BillingNoteController::class, 'index'])->name('billing-notes.index');
        Route::get('billing-notes/create', [BillingNoteController::class, 'createDirect'])->name('billing-notes.create');
        Route::post('billing-notes/standalone', [BillingNoteController::class, 'storeStandalone'])->name('billing-notes.store.standalone');
        Route::get('sales-orders/{salesOrder}/billing-notes/create', [BillingNoteController::class, 'create'])
            ->name('sales-orders.billing-notes.create');
        Route::post('sales-orders/{salesOrder}/billing-notes', [BillingNoteController::class, 'store'])
            ->name('sales-orders.billing-notes.store');
        Route::get('billing-notes/{billingNote}/edit', [BillingNoteController::class, 'edit'])->name('billing-notes.edit');
        Route::put('billing-notes/{billingNote}', [BillingNoteController::class, 'update'])->name('billing-notes.update');
        Route::delete('billing-notes/{billingNote}', [BillingNoteController::class, 'destroy'])->name('billing-notes.destroy');
        Route::get('billing-notes/{billingNote}', [BillingNoteController::class, 'show'])->name('billing-notes.show');
        Route::post('billing-notes/{billingNote}/issue', [BillingNoteController::class, 'issue'])->name('billing-notes.issue');
        Route::post('billing-notes/{billingNote}/cancel', [BillingNoteController::class, 'cancel'])->name('billing-notes.cancel');

        Route::get('delivery-notes', [DeliveryNoteController::class, 'index'])->name('delivery-notes.index');
        Route::get('delivery-notes/create', [DeliveryNoteController::class, 'createDirect'])->name('delivery-notes.create');
        Route::post('delivery-notes/standalone', [DeliveryNoteController::class, 'storeStandalone'])->name('delivery-notes.store.standalone');
        Route::get('sales-orders/{salesOrder}/delivery-notes/create', [DeliveryNoteController::class, 'create'])
            ->name('sales-orders.delivery-notes.create');
        Route::post('sales-orders/{salesOrder}/delivery-notes', [DeliveryNoteController::class, 'store'])
            ->name('sales-orders.delivery-notes.store');
        Route::get('delivery-notes/{deliveryNote}/edit', [DeliveryNoteController::class, 'edit'])->name('delivery-notes.edit');
        Route::put('delivery-notes/{deliveryNote}', [DeliveryNoteController::class, 'update'])->name('delivery-notes.update');
        Route::delete('delivery-notes/{deliveryNote}', [DeliveryNoteController::class, 'destroy'])->name('delivery-notes.destroy');
        Route::get('delivery-notes/{deliveryNote}', [DeliveryNoteController::class, 'show'])->name('delivery-notes.show');
        Route::post('delivery-notes/{deliveryNote}/issue', [DeliveryNoteController::class, 'issue'])->name('delivery-notes.issue');
        Route::post('delivery-notes/{deliveryNote}/cancel', [DeliveryNoteController::class, 'cancel'])->name('delivery-notes.cancel');

        Route::get('tax-invoices', [TaxInvoiceController::class, 'index'])->name('tax-invoices.index');
        Route::get('tax-invoices/create', [TaxInvoiceController::class, 'createDirect'])->name('tax-invoices.create');
        Route::post('tax-invoices/standalone', [TaxInvoiceController::class, 'storeStandalone'])->name('tax-invoices.store.standalone');
        Route::get('sales-orders/{salesOrder}/tax-invoices/create', [TaxInvoiceController::class, 'create'])
            ->name('sales-orders.tax-invoices.create');
        Route::post('sales-orders/{salesOrder}/tax-invoices', [TaxInvoiceController::class, 'store'])
            ->name('sales-orders.tax-invoices.store');
        Route::get('tax-invoices/{taxInvoice}/edit', [TaxInvoiceController::class, 'edit'])->name('tax-invoices.edit');
        Route::put('tax-invoices/{taxInvoice}', [TaxInvoiceController::class, 'update'])->name('tax-invoices.update');
        Route::delete('tax-invoices/{taxInvoice}', [TaxInvoiceController::class, 'destroy'])->name('tax-invoices.destroy');
        Route::get('tax-invoices/{taxInvoice}', [TaxInvoiceController::class, 'show'])->name('tax-invoices.show');
        Route::post('tax-invoices/{taxInvoice}/issue', [TaxInvoiceController::class, 'issue'])->name('tax-invoices.issue');
        Route::post('tax-invoices/{taxInvoice}/cancel', [TaxInvoiceController::class, 'cancel'])->name('tax-invoices.cancel');

        Route::get('receipts', [ReceiptController::class, 'index'])->name('receipts.index');
        Route::get('receipts/create', [ReceiptController::class, 'createDirect'])->name('receipts.create');
        Route::post('receipts/standalone', [ReceiptController::class, 'storeStandalone'])->name('receipts.store.standalone');
        Route::get('tax-invoices/{taxInvoice}/receipts/create', [ReceiptController::class, 'create'])
            ->name('tax-invoices.receipts.create');
        Route::post('tax-invoices/{taxInvoice}/receipts', [ReceiptController::class, 'store'])
            ->name('tax-invoices.receipts.store');
        Route::get('receipts/{receipt}/edit', [ReceiptController::class, 'edit'])->name('receipts.edit');
        Route::put('receipts/{receipt}', [ReceiptController::class, 'update'])->name('receipts.update');
        Route::delete('receipts/{receipt}', [ReceiptController::class, 'destroy'])->name('receipts.destroy');
        Route::get('receipts/{receipt}', [ReceiptController::class, 'show'])->name('receipts.show');
        Route::post('receipts/{receipt}/issue', [ReceiptController::class, 'issue'])->name('receipts.issue');
        Route::post('receipts/{receipt}/cancel', [ReceiptController::class, 'cancel'])->name('receipts.cancel');

        Route::get('credit-notes', [CreditNoteController::class, 'index'])->name('credit-notes.index');
        Route::get('credit-notes/create', [CreditNoteController::class, 'createDirect'])->name('credit-notes.create');
        Route::post('credit-notes/standalone', [CreditNoteController::class, 'storeStandalone'])->name('credit-notes.store.standalone');
        Route::get('tax-invoices/{taxInvoice}/credit-notes/create', [CreditNoteController::class, 'create'])
            ->name('tax-invoices.credit-notes.create');
        Route::post('tax-invoices/{taxInvoice}/credit-notes', [CreditNoteController::class, 'store'])
            ->name('tax-invoices.credit-notes.store');
        Route::get('credit-notes/{creditNote}/edit', [CreditNoteController::class, 'edit'])->name('credit-notes.edit');
        Route::put('credit-notes/{creditNote}', [CreditNoteController::class, 'update'])->name('credit-notes.update');
        Route::delete('credit-notes/{creditNote}', [CreditNoteController::class, 'destroy'])->name('credit-notes.destroy');
        Route::get('credit-notes/{creditNote}', [CreditNoteController::class, 'show'])->name('credit-notes.show');
        Route::post('credit-notes/{creditNote}/issue', [CreditNoteController::class, 'issue'])->name('credit-notes.issue');
        Route::post('credit-notes/{creditNote}/cancel', [CreditNoteController::class, 'cancel'])->name('credit-notes.cancel');

        Route::get('debit-notes', [DebitNoteController::class, 'index'])->name('debit-notes.index');
        Route::get('debit-notes/create', [DebitNoteController::class, 'createDirect'])->name('debit-notes.create');
        Route::post('debit-notes/standalone', [DebitNoteController::class, 'storeStandalone'])->name('debit-notes.store.standalone');
        Route::get('tax-invoices/{taxInvoice}/debit-notes/create', [DebitNoteController::class, 'create'])
            ->name('tax-invoices.debit-notes.create');
        Route::post('tax-invoices/{taxInvoice}/debit-notes', [DebitNoteController::class, 'store'])
            ->name('tax-invoices.debit-notes.store');
        Route::get('debit-notes/{debitNote}/edit', [DebitNoteController::class, 'edit'])->name('debit-notes.edit');
        Route::put('debit-notes/{debitNote}', [DebitNoteController::class, 'update'])->name('debit-notes.update');
        Route::delete('debit-notes/{debitNote}', [DebitNoteController::class, 'destroy'])->name('debit-notes.destroy');
        Route::get('debit-notes/{debitNote}', [DebitNoteController::class, 'show'])->name('debit-notes.show');
        Route::post('debit-notes/{debitNote}/issue', [DebitNoteController::class, 'issue'])->name('debit-notes.issue');
        Route::post('debit-notes/{debitNote}/cancel', [DebitNoteController::class, 'cancel'])->name('debit-notes.cancel');

        Route::prefix('settings')->name('settings.')->group(function () {
            Route::resource('product-service-categories', ProductServiceCategoryController::class)
                ->except(['create', 'show', 'edit']);
            Route::resource('product-service-items', ProductServiceItemController::class)
                ->except(['show']);
            Route::resource('product-service-questions', ProductServiceQuestionController::class)
                ->except(['create', 'show', 'edit']);
            Route::resource('marketing-platforms', MarketingPlatformController::class)
                ->except(['create', 'show', 'edit']);
            Route::resource('marketing-objectives', MarketingObjectiveController::class)
                ->except(['create', 'show', 'edit']);
            Route::resource('marketing-ads', MarketingAdController::class);

            Route::resource('lead-channels', LeadChannelController::class)
                ->except(['create', 'show', 'edit']);
            Route::resource('lead-closed-reasons', LeadClosedReasonController::class)
                ->except(['create', 'show', 'edit']);
            Route::resource('lead-project-types', LeadProjectTypeController::class)
                ->except(['create', 'show', 'edit']);
            Route::resource('lead-assignment-rules', LeadAssignmentRuleController::class)
                ->except(['create', 'show', 'edit']);
            Route::get('lead-pipeline', [LeadPipelineSettingController::class, 'edit'])
                ->name('lead-pipeline.edit');
            Route::put('lead-pipeline/tier', [LeadPipelineSettingController::class, 'updateTier'])
                ->name('lead-pipeline.tier');
            Route::put('lead-pipeline/sla', [LeadPipelineSettingController::class, 'updateSla'])
                ->name('lead-pipeline.sla');
            Route::put('lead-pipeline/goal', [LeadPipelineSettingController::class, 'updateGoal'])
                ->name('lead-pipeline.goal');

            Route::post('marketing-ads/{marketing_ad}/units', [MarketingAdUnitController::class, 'store'])
                ->name('marketing-ads.units.store');
            Route::put('marketing-ads/{marketing_ad}/units/{unit}', [MarketingAdUnitController::class, 'update'])
                ->name('marketing-ads.units.update');
            Route::delete('marketing-ads/{marketing_ad}/units/{unit}', [MarketingAdUnitController::class, 'destroy'])
                ->name('marketing-ads.units.destroy');

            Route::post('marketing-ads/{marketing_ad}/artworks', [MarketingArtworkController::class, 'store'])
                ->name('marketing-ads.artworks.store');
            Route::delete('marketing-ads/{marketing_ad}/artworks/{artwork}', [MarketingArtworkController::class, 'destroy'])
                ->name('marketing-ads.artworks.destroy');
        });
    });
});

Route::prefix('template')->name('template.')->group(function () {
    Route::view('/advance-ui-animation', 'template.advance-ui-animation')->name('advance-ui-animation');
    Route::view('/advance-ui-highlight', 'template.advance-ui-highlight')->name('advance-ui-highlight');
    Route::view('/advance-ui-nestable', 'template.advance-ui-nestable')->name('advance-ui-nestable');
    Route::view('/advance-ui-ratings', 'template.advance-ui-ratings')->name('advance-ui-ratings');
    Route::view('/advance-ui-scrollbar', 'template.advance-ui-scrollbar')->name('advance-ui-scrollbar');
    Route::view('/advance-ui-scrollspy', 'template.advance-ui-scrollspy')->name('advance-ui-scrollspy');
    Route::view('/advance-ui-sweetalerts', 'template.advance-ui-sweetalerts')->name('advance-ui-sweetalerts');
    Route::view('/advance-ui-swiper', 'template.advance-ui-swiper')->name('advance-ui-swiper');
    Route::view('/advance-ui-tour', 'template.advance-ui-tour')->name('advance-ui-tour');
    Route::view('/apps-api-key', 'template.apps-api-key')->name('apps-api-key');
    Route::view('/apps-calendar', 'template.apps-calendar')->name('apps-calendar');
    Route::view('/apps-calendar-month-grid', 'template.apps-calendar-month-grid')->name('apps-calendar-month-grid');
    Route::view('/apps-chat', 'template.apps-chat')->name('apps-chat');
    Route::view('/apps-crm-companies', 'template.apps-crm-companies')->name('apps-crm-companies');
    Route::view('/apps-crm-contacts', 'template.apps-crm-contacts')->name('apps-crm-contacts');
    Route::view('/apps-crm-deals', 'template.apps-crm-deals')->name('apps-crm-deals');
    Route::view('/apps-crm-leads', 'template.apps-crm-leads')->name('apps-crm-leads');
    Route::view('/apps-crypto-buy-sell', 'template.apps-crypto-buy-sell')->name('apps-crypto-buy-sell');
    Route::view('/apps-crypto-ico', 'template.apps-crypto-ico')->name('apps-crypto-ico');
    Route::view('/apps-crypto-kyc', 'template.apps-crypto-kyc')->name('apps-crypto-kyc');
    Route::view('/apps-crypto-orders', 'template.apps-crypto-orders')->name('apps-crypto-orders');
    Route::view('/apps-crypto-transactions', 'template.apps-crypto-transactions')->name('apps-crypto-transactions');
    Route::view('/apps-crypto-wallet', 'template.apps-crypto-wallet')->name('apps-crypto-wallet');
    Route::view('/apps-ecommerce-add-product', 'template.apps-ecommerce-add-product')->name('apps-ecommerce-add-product');
    Route::view('/apps-ecommerce-cart', 'template.apps-ecommerce-cart')->name('apps-ecommerce-cart');
    Route::view('/apps-ecommerce-checkout', 'template.apps-ecommerce-checkout')->name('apps-ecommerce-checkout');
    Route::view('/apps-ecommerce-customers', 'template.apps-ecommerce-customers')->name('apps-ecommerce-customers');
    Route::view('/apps-ecommerce-order-details', 'template.apps-ecommerce-order-details')->name('apps-ecommerce-order-details');
    Route::view('/apps-ecommerce-orders', 'template.apps-ecommerce-orders')->name('apps-ecommerce-orders');
    Route::view('/apps-ecommerce-product-details', 'template.apps-ecommerce-product-details')->name('apps-ecommerce-product-details');
    Route::view('/apps-ecommerce-products', 'template.apps-ecommerce-products')->name('apps-ecommerce-products');
    Route::view('/apps-ecommerce-seller-details', 'template.apps-ecommerce-seller-details')->name('apps-ecommerce-seller-details');
    Route::view('/apps-ecommerce-sellers', 'template.apps-ecommerce-sellers')->name('apps-ecommerce-sellers');
    Route::view('/apps-email-basic', 'template.apps-email-basic')->name('apps-email-basic');
    Route::view('/apps-email-ecommerce', 'template.apps-email-ecommerce')->name('apps-email-ecommerce');
    Route::view('/apps-file-manager', 'template.apps-file-manager')->name('apps-file-manager');
    Route::view('/apps-invoices-create', 'template.apps-invoices-create')->name('apps-invoices-create');
    Route::view('/apps-invoices-details', 'template.apps-invoices-details')->name('apps-invoices-details');
    Route::view('/apps-invoices-list', 'template.apps-invoices-list')->name('apps-invoices-list');
    Route::view('/apps-job-application', 'template.apps-job-application')->name('apps-job-application');
    Route::view('/apps-job-candidate-grid', 'template.apps-job-candidate-grid')->name('apps-job-candidate-grid');
    Route::view('/apps-job-candidate-lists', 'template.apps-job-candidate-lists')->name('apps-job-candidate-lists');
    Route::view('/apps-job-categories', 'template.apps-job-categories')->name('apps-job-categories');
    Route::view('/apps-job-companies-lists', 'template.apps-job-companies-lists')->name('apps-job-companies-lists');
    Route::view('/apps-job-details', 'template.apps-job-details')->name('apps-job-details');
    Route::view('/apps-job-grid-lists', 'template.apps-job-grid-lists')->name('apps-job-grid-lists');
    Route::view('/apps-job-lists', 'template.apps-job-lists')->name('apps-job-lists');
    Route::view('/apps-job-new', 'template.apps-job-new')->name('apps-job-new');
    Route::view('/apps-job-statistics', 'template.apps-job-statistics')->name('apps-job-statistics');
    Route::view('/apps-mailbox', 'template.apps-mailbox')->name('apps-mailbox');
    Route::view('/apps-nft-auction', 'template.apps-nft-auction')->name('apps-nft-auction');
    Route::view('/apps-nft-collections', 'template.apps-nft-collections')->name('apps-nft-collections');
    Route::view('/apps-nft-create', 'template.apps-nft-create')->name('apps-nft-create');
    Route::view('/apps-nft-creators', 'template.apps-nft-creators')->name('apps-nft-creators');
    Route::view('/apps-nft-explore', 'template.apps-nft-explore')->name('apps-nft-explore');
    Route::view('/apps-nft-item-details', 'template.apps-nft-item-details')->name('apps-nft-item-details');
    Route::view('/apps-nft-marketplace', 'template.apps-nft-marketplace')->name('apps-nft-marketplace');
    Route::view('/apps-nft-ranking', 'template.apps-nft-ranking')->name('apps-nft-ranking');
    Route::view('/apps-nft-wallet', 'template.apps-nft-wallet')->name('apps-nft-wallet');
    Route::view('/apps-projects-create', 'template.apps-projects-create')->name('apps-projects-create');
    Route::view('/apps-projects-list', 'template.apps-projects-list')->name('apps-projects-list');
    Route::view('/apps-projects-overview', 'template.apps-projects-overview')->name('apps-projects-overview');
    Route::view('/apps-tasks-details', 'template.apps-tasks-details')->name('apps-tasks-details');
    Route::view('/apps-tasks-kanban', 'template.apps-tasks-kanban')->name('apps-tasks-kanban');
    Route::view('/apps-tasks-list-view', 'template.apps-tasks-list-view')->name('apps-tasks-list-view');
    Route::view('/apps-tickets-details', 'template.apps-tickets-details')->name('apps-tickets-details');
    Route::view('/apps-tickets-list', 'template.apps-tickets-list')->name('apps-tickets-list');
    Route::view('/apps-todo', 'template.apps-todo')->name('apps-todo');
    Route::view('/auth-404-alt', 'template.auth-404-alt')->name('auth-404-alt');
    Route::view('/auth-404-basic', 'template.auth-404-basic')->name('auth-404-basic');
    Route::view('/auth-404-cover', 'template.auth-404-cover')->name('auth-404-cover');
    Route::view('/auth-500', 'template.auth-500')->name('auth-500');
    Route::view('/auth-lockscreen-basic', 'template.auth-lockscreen-basic')->name('auth-lockscreen-basic');
    Route::view('/auth-lockscreen-cover', 'template.auth-lockscreen-cover')->name('auth-lockscreen-cover');
    Route::view('/auth-logout-basic', 'template.auth-logout-basic')->name('auth-logout-basic');
    Route::view('/auth-logout-cover', 'template.auth-logout-cover')->name('auth-logout-cover');
    Route::view('/auth-offline', 'template.auth-offline')->name('auth-offline');
    Route::view('/auth-pass-change-basic', 'template.auth-pass-change-basic')->name('auth-pass-change-basic');
    Route::view('/auth-pass-change-cover', 'template.auth-pass-change-cover')->name('auth-pass-change-cover');
    Route::view('/auth-pass-reset-basic', 'template.auth-pass-reset-basic')->name('auth-pass-reset-basic');
    Route::view('/auth-pass-reset-cover', 'template.auth-pass-reset-cover')->name('auth-pass-reset-cover');
    Route::view('/auth-signin-basic', 'template.auth-signin-basic')->name('auth-signin-basic');
    Route::view('/auth-signin-cover', 'template.auth-signin-cover')->name('auth-signin-cover');
    Route::view('/auth-signup-basic', 'template.auth-signup-basic')->name('auth-signup-basic');
    Route::view('/auth-signup-cover', 'template.auth-signup-cover')->name('auth-signup-cover');
    Route::view('/auth-success-msg-basic', 'template.auth-success-msg-basic')->name('auth-success-msg-basic');
    Route::view('/auth-success-msg-cover', 'template.auth-success-msg-cover')->name('auth-success-msg-cover');
    Route::view('/auth-twostep-basic', 'template.auth-twostep-basic')->name('auth-twostep-basic');
    Route::view('/auth-twostep-cover', 'template.auth-twostep-cover')->name('auth-twostep-cover');
    Route::view('/charts-apex-area', 'template.charts-apex-area')->name('charts-apex-area');
    Route::view('/charts-apex-bar', 'template.charts-apex-bar')->name('charts-apex-bar');
    Route::view('/charts-apex-boxplot', 'template.charts-apex-boxplot')->name('charts-apex-boxplot');
    Route::view('/charts-apex-bubble', 'template.charts-apex-bubble')->name('charts-apex-bubble');
    Route::view('/charts-apex-candlestick', 'template.charts-apex-candlestick')->name('charts-apex-candlestick');
    Route::view('/charts-apex-column', 'template.charts-apex-column')->name('charts-apex-column');
    Route::view('/charts-apex-funnel', 'template.charts-apex-funnel')->name('charts-apex-funnel');
    Route::view('/charts-apex-heatmap', 'template.charts-apex-heatmap')->name('charts-apex-heatmap');
    Route::view('/charts-apex-line', 'template.charts-apex-line')->name('charts-apex-line');
    Route::view('/charts-apex-mixed', 'template.charts-apex-mixed')->name('charts-apex-mixed');
    Route::view('/charts-apex-pie', 'template.charts-apex-pie')->name('charts-apex-pie');
    Route::view('/charts-apex-polar', 'template.charts-apex-polar')->name('charts-apex-polar');
    Route::view('/charts-apex-radar', 'template.charts-apex-radar')->name('charts-apex-radar');
    Route::view('/charts-apex-radialbar', 'template.charts-apex-radialbar')->name('charts-apex-radialbar');
    Route::view('/charts-apex-range-area', 'template.charts-apex-range-area')->name('charts-apex-range-area');
    Route::view('/charts-apex-scatter', 'template.charts-apex-scatter')->name('charts-apex-scatter');
    Route::view('/charts-apex-timeline', 'template.charts-apex-timeline')->name('charts-apex-timeline');
    Route::view('/charts-apex-treemap', 'template.charts-apex-treemap')->name('charts-apex-treemap');
    Route::view('/charts-chartjs', 'template.charts-chartjs')->name('charts-chartjs');
    Route::view('/charts-echarts', 'template.charts-echarts')->name('charts-echarts');
    Route::view('/dashboard-analytics', 'template.dashboard-analytics')->name('dashboard-analytics');
    Route::view('/dashboard-crm', 'template.dashboard-crm')->name('dashboard-crm');
    Route::view('/dashboard-crypto', 'template.dashboard-crypto')->name('dashboard-crypto');
    Route::view('/dashboard-job', 'template.dashboard-job')->name('dashboard-job');
    Route::view('/dashboard-nft', 'template.dashboard-nft')->name('dashboard-nft');
    Route::view('/dashboard-projects', 'template.dashboard-projects')->name('dashboard-projects');
    Route::view('/forms-advanced', 'template.forms-advanced')->name('forms-advanced');
    Route::view('/forms-checkboxs-radios', 'template.forms-checkboxs-radios')->name('forms-checkboxs-radios');
    Route::view('/forms-editors', 'template.forms-editors')->name('forms-editors');
    Route::view('/forms-elements', 'template.forms-elements')->name('forms-elements');
    Route::view('/forms-file-uploads', 'template.forms-file-uploads')->name('forms-file-uploads');
    Route::view('/forms-layouts', 'template.forms-layouts')->name('forms-layouts');
    Route::view('/forms-masks', 'template.forms-masks')->name('forms-masks');
    Route::view('/forms-pickers', 'template.forms-pickers')->name('forms-pickers');
    Route::view('/forms-range-sliders', 'template.forms-range-sliders')->name('forms-range-sliders');
    Route::view('/forms-select', 'template.forms-select')->name('forms-select');
    Route::view('/forms-select2', 'template.forms-select2')->name('forms-select2');
    Route::view('/forms-validation', 'template.forms-validation')->name('forms-validation');
    Route::view('/forms-wizard', 'template.forms-wizard')->name('forms-wizard');
    Route::view('/icons-boxicons', 'template.icons-boxicons')->name('icons-boxicons');
    Route::view('/icons-crypto', 'template.icons-crypto')->name('icons-crypto');
    Route::view('/icons-feather', 'template.icons-feather')->name('icons-feather');
    Route::view('/icons-lineawesome', 'template.icons-lineawesome')->name('icons-lineawesome');
    Route::view('/icons-materialdesign', 'template.icons-materialdesign')->name('icons-materialdesign');
    Route::view('/icons-remix', 'template.icons-remix')->name('icons-remix');
    Route::view('/', 'template.index')->name('index');
    Route::view('/job-landing', 'template.job-landing')->name('job-landing');
    Route::view('/landing', 'template.landing')->name('landing');
    Route::view('/layouts-detached', 'template.layouts-detached')->name('layouts-detached');
    Route::view('/layouts-horizontal', 'template.layouts-horizontal')->name('layouts-horizontal');
    Route::view('/layouts-two-column', 'template.layouts-two-column')->name('layouts-two-column');
    Route::view('/layouts-vertical-hovered', 'template.layouts-vertical-hovered')->name('layouts-vertical-hovered');
    Route::view('/maps-google', 'template.maps-google')->name('maps-google');
    Route::view('/maps-leaflet', 'template.maps-leaflet')->name('maps-leaflet');
    Route::view('/maps-vector', 'template.maps-vector')->name('maps-vector');
    Route::view('/nft-landing', 'template.nft-landing')->name('nft-landing');
    Route::view('/pages-coming-soon', 'template.pages-coming-soon')->name('pages-coming-soon');
    Route::view('/pages-faqs', 'template.pages-faqs')->name('pages-faqs');
    Route::view('/pages-gallery', 'template.pages-gallery')->name('pages-gallery');
    Route::view('/pages-maintenance', 'template.pages-maintenance')->name('pages-maintenance');
    Route::view('/pages-pricing', 'template.pages-pricing')->name('pages-pricing');
    Route::view('/pages-privacy-policy', 'template.pages-privacy-policy')->name('pages-privacy-policy');
    Route::view('/pages-profile', 'template.pages-profile')->name('pages-profile');
    Route::view('/pages-profile-settings', 'template.pages-profile-settings')->name('pages-profile-settings');
    Route::view('/pages-search-results', 'template.pages-search-results')->name('pages-search-results');
    Route::view('/pages-sitemap', 'template.pages-sitemap')->name('pages-sitemap');
    Route::view('/pages-starter', 'template.pages-starter')->name('pages-starter');
    Route::view('/pages-team', 'template.pages-team')->name('pages-team');
    Route::view('/pages-term-conditions', 'template.pages-term-conditions')->name('pages-term-conditions');
    Route::view('/pages-timeline', 'template.pages-timeline')->name('pages-timeline');
    Route::view('/tables-basic', 'template.tables-basic')->name('tables-basic');
    Route::view('/tables-datatables', 'template.tables-datatables')->name('tables-datatables');
    Route::view('/tables-gridjs', 'template.tables-gridjs')->name('tables-gridjs');
    Route::view('/tables-listjs', 'template.tables-listjs')->name('tables-listjs');
    Route::view('/ui-accordions', 'template.ui-accordions')->name('ui-accordions');
    Route::view('/ui-alerts', 'template.ui-alerts')->name('ui-alerts');
    Route::view('/ui-badges', 'template.ui-badges')->name('ui-badges');
    Route::view('/ui-buttons', 'template.ui-buttons')->name('ui-buttons');
    Route::view('/ui-cards', 'template.ui-cards')->name('ui-cards');
    Route::view('/ui-carousel', 'template.ui-carousel')->name('ui-carousel');
    Route::view('/ui-colors', 'template.ui-colors')->name('ui-colors');
    Route::view('/ui-dropdowns', 'template.ui-dropdowns')->name('ui-dropdowns');
    Route::view('/ui-embed-video', 'template.ui-embed-video')->name('ui-embed-video');
    Route::view('/ui-general', 'template.ui-general')->name('ui-general');
    Route::view('/ui-grid', 'template.ui-grid')->name('ui-grid');
    Route::view('/ui-images', 'template.ui-images')->name('ui-images');
    Route::view('/ui-links', 'template.ui-links')->name('ui-links');
    Route::view('/ui-lists', 'template.ui-lists')->name('ui-lists');
    Route::view('/ui-media', 'template.ui-media')->name('ui-media');
    Route::view('/ui-modals', 'template.ui-modals')->name('ui-modals');
    Route::view('/ui-notifications', 'template.ui-notifications')->name('ui-notifications');
    Route::view('/ui-offcanvas', 'template.ui-offcanvas')->name('ui-offcanvas');
    Route::view('/ui-placeholders', 'template.ui-placeholders')->name('ui-placeholders');
    Route::view('/ui-progress', 'template.ui-progress')->name('ui-progress');
    Route::view('/ui-ribbons', 'template.ui-ribbons')->name('ui-ribbons');
    Route::view('/ui-tabs', 'template.ui-tabs')->name('ui-tabs');
    Route::view('/ui-typography', 'template.ui-typography')->name('ui-typography');
    Route::view('/ui-utilities', 'template.ui-utilities')->name('ui-utilities');
    Route::view('/widgets', 'template.widgets')->name('widgets');
});
