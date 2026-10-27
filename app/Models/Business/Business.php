<?php

namespace App\Models\Business;

use App\Models\Concerns\CascadesSoftDeletes;
use App\Models\Employee\Employee;
use App\Models\Marketing\MarketingAd;
use App\Models\Marketing\MarketingObjective;
use App\Models\Marketing\MarketingPlatform;
use App\Models\Partner\Partner;
use App\Models\ProductService\ProductServiceCategory;
use App\Models\ProductService\ProductServiceItem;
use App\Models\ProductService\ProductServiceQuestion;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Business extends Model
{
    use CascadesSoftDeletes, HasUuids, SoftDeletes;

    protected array $cascadeSoftDeletes = [
        'businessUsers',
        'businessBranches',
        'businessBankAccounts',
        'employees',
        'partners',
        'productServiceCategories',
        'productServiceItems',
        'productServiceQuestions',
        'marketingPlatforms',
        'marketingObjectives',
        'marketingAds',
    ];

    protected $fillable = [
        'business_type',
        'business_tax_no',
        'business_vat_status',
        'business_code',
        'business_name_th',
        'business_en_status',
        'business_name_en',
    ];

    public function businessUsers(): HasMany
    {
        return $this->hasMany(BusinessUser::class, 'business_id');
    }

    public function businessBranches(): HasMany
    {
        return $this->hasMany(BusinessBranch::class, 'business_id');
    }

    public function businessBankAccounts(): HasMany
    {
        return $this->hasMany(BusinessBankAccount::class, 'business_id');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'businesses_id');
    }

    public function partners(): HasMany
    {
        return $this->hasMany(Partner::class, 'business_id');
    }

    public function productServiceCategories(): HasMany
    {
        return $this->hasMany(ProductServiceCategory::class, 'business_id');
    }

    public function productServiceItems(): HasMany
    {
        return $this->hasMany(ProductServiceItem::class, 'business_id');
    }

    public function productServiceQuestions(): HasMany
    {
        return $this->hasMany(ProductServiceQuestion::class, 'business_id');
    }

    public function marketingPlatforms(): HasMany
    {
        return $this->hasMany(MarketingPlatform::class, 'business_id');
    }

    public function marketingObjectives(): HasMany
    {
        return $this->hasMany(MarketingObjective::class, 'business_id');
    }

    public function marketingAds(): HasMany
    {
        return $this->hasMany(MarketingAd::class, 'business_id');
    }
}
