<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Policies\UserAdminPolicy;
use App\Policies\PlanPolicy;
use App\Policies\VeterinaryPolicy;
use App\Policies\ConfigurationPolicy;
use App\Policies\AddressPolicy;
use App\Policies\SchedulePolicy;
use App\Policies\ClientPolicy;
use App\Models\User;
use App\Models\Veterinary;
use App\Models\Plan;
use App\Models\Configuration;
use App\Models\Address;
use App\Models\Schedule;
use App\Models\Client;
use App\Models\TypePet;
use App\Models\Race;
use App\Policies\TypePetPolicy;
use App\Policies\RacePolicy;
use App\Models\Pet;
use App\Policies\PetPolicy;
use App\Models\Vaccine;
use App\Policies\VaccinePolicy;
use App\Models\Consultation;
use App\Policies\ConsultationPolicy;
use App\Models\Advertising;
use App\Policies\AdvertisingPolicy;
use App\Models\Promotion;
use App\Policies\PromotionPolicy;
use App\Models\Product;
use App\Policies\ProductPolicy;
use App\Models\Sale;
use App\Policies\SalePolicy;
use App\Models\Shopping;
use App\Policies\ShoppingPolicy;
use App\Models\Movement;
use App\Policies\MovementPolicy;
use App\Policies\ReportPolicy;
use App\Models\Advertisement;
use App\Policies\AdvertisementPolicy;
use App\Models\ContentVeterinary;
use App\Policies\ContentVeterinaryPolicy;
use App\Models\ImageVeterinary;
use App\Policies\ImageVeterinaryPolicy;


class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Veterinary::class => VeterinaryPolicy::class,
        Plan::class => PlanPolicy::class,
        User::class => UserAdminPolicy::class,
        Configuration::class => ConfigurationPolicy::class,
        Address::class => AddressPolicy::class,
        Schedule::class => SchedulePolicy::class,
        Client::class => ClientPolicy::class,
        TypePet::class => TypePetPolicy::class,
        Race::class => RacePolicy::class,
        Pet::class => PetPolicy::class,
        Vaccine::class => VaccinePolicy::class,
        Consultation::class => ConsultationPolicy::class,
        Advertising::class => AdvertisingPolicy::class,
        Promotion::class => PromotionPolicy::class,
        Product::class => ProductPolicy::class,
        Sale::class => SalePolicy::class,
        Shopping::class => ShoppingPolicy::class,
        Movement::class => MovementPolicy::class,
        Advertisement::class => AdvertisementPolicy::class,
        ContentVeterinary::class => ContentVeterinaryPolicy::class,
        ImageVeterinary::class => ImageVeterinaryPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();

        // Register custom gates for reports
        Gate::define('viewSalesAnalytics', [ReportPolicy::class, 'viewSalesAnalytics']);
        Gate::define('viewPurchasesAnalytics', [ReportPolicy::class, 'viewPurchasesAnalytics']);
        Gate::define('viewConsultationsAnalytics', [ReportPolicy::class, 'viewConsultationsAnalytics']);
        Gate::define('viewDashboard', [ReportPolicy::class, 'viewDashboard']);
        Gate::define('viewLowStock', [ReportPolicy::class, 'viewLowStock']);
    }

}