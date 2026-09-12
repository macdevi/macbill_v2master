<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Router extends Model
{
    protected $fillable = ['name','host','port','username','password','ssl','active','isolation_profile'];
    protected $casts = ['ssl'=>'boolean','active'=>'boolean','port'=>'integer'];
    protected $hidden = ['password'];
    public function customers(): HasMany { return $this->hasMany(Customer::class); }
}