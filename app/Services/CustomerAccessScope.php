<?php
namespace App\Services;

use App\Models\Area;
use App\Models\Router;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

class CustomerAccessScope
{
    public static function areaIds(): ?array
    {
        $user=auth()->user();
        abort_unless($user,403);
        return $user->isSuperAdmin() ? null : array_map('intval',$user->activeAreaIds()->all());
    }
    public static function areas(): Builder
    {
        $ids=self::areaIds();
        return Area::query()->where('active',true)
            ->when($ids!==null,fn($q)=>$q->whereIn('id',$ids));
    }
    public static function routers(): Builder
    {
        $query=Router::query()->where('active',true)->with('accessibleAreas');
        $ids=self::areaIds();
        if($ids!==null){
            if(!Schema::hasTable('area_router_access')) return $query->whereRaw('1 = 0');
            $query->whereHas('accessibleAreas',fn($q)=>$q->where('areas.active',true)->whereIn('areas.id',$ids));
        }
        return $query;
    }
    public static function routerSupportsArea(int $routerId,int $areaId): bool
    {
        if(!Schema::hasTable('area_router_access')) return false;
        if(!self::areas()->whereKey($areaId)->exists()) return false;
        return self::routers()->whereKey($routerId)
            ->whereHas('accessibleAreas',fn($q)=>$q->where('areas.id',$areaId)->where('areas.active',true))
            ->exists();
    }
    public static function customers(Builder $query): Builder
    {
        $ids=self::areaIds();
        return $query->when($ids!==null,fn($q)=>$q->whereIn('area_id',$ids));
    }
}
