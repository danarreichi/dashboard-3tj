<?php

namespace App\Traits;

use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait RelationShortcut
{
    private function getNestedRelation($relation, $excludeLast = false)
    {
        $splitted = explode('.', $relation);
        $length = count($splitted);
        if ($excludeLast && $length - 1 > 0) $length = $length - 1;

        $lastObj = null;
        for ($i = 0; $i < $length; $i++) {
            $rel = $splitted[$i];
            if ($lastObj && $lastObj instanceof MissingValue) return $lastObj;
            if (($lastObj && !$lastObj->relationLoaded($rel)) || (!$lastObj && !$this->relationLoaded($rel))) return null;
            if (!$lastObj) $lastObj = $this;
            if ($lastObj instanceof Collection) $lastObj = $lastObj->first();
            else $lastObj = $lastObj->{$rel};
        }

        return $lastObj;
    }

    public function nestedRelationLoaded($relation)
    {
        $nestedRel = $this->getNestedRelation($relation);
        return $nestedRel !== null && !($nestedRel instanceof MissingValue);
    }

    public function getPropWhenLoaded($relation, $propName, $def = null)
    {
        $obj = $this->getNestedRelation($relation);
        if ($obj && $obj instanceof MissingValue) return $obj;
        if (!$obj || ($obj instanceof Collection)) return $def;

        return optional($obj)->{$propName};
    }

    public function limitWhenLoaded($relation, $limit)
    {
        $obj = $this->getNestedRelation($relation);
        if ($obj && $obj instanceof MissingValue) return $obj;
        if (!$obj || !($obj instanceof Collection)) return new MissingValue;

        return optional($obj)->take($limit);
    }

    public function pluckPropWhenLoaded($relation, $propName)
    {
        $obj = $this->getNestedRelation($relation);
        if ($obj && $obj instanceof MissingValue) return $obj;
        if (!$obj || !($obj instanceof Collection)) return [];

        return optional($obj)->pluck($propName);
    }

    private function getAggregateRelation($relation)
    {
        if (str_contains($relation, '.')) {
            $obj = $this->getNestedRelation($relation, true);
            if (!$obj || ($obj instanceof Collection)) return new MissingValue;
            $splitted = explode('.', $relation);
            $lastRelation = array_pop($splitted);
        } else {
            $obj = $this;
            $lastRelation = $relation;
        }

        return [$obj, $lastRelation];
    }

    public function whenCountLoaded($relation)
    {
        [$obj, $lastRelation] = $this->getAggregateRelation($relation);

        if ($obj && $obj instanceof MissingValue) return $obj;
        if (!$obj || ($obj instanceof Collection)) return null;

        $count = $obj->{Str::snake($lastRelation) . '_count'};
        return $count === null ? new MissingValue() : $count;
    }

    public function whenSumLoaded($relation, $column)
    {
        [$obj, $lastRelation] = $this->getAggregateRelation($relation);

        if ($obj && $obj instanceof MissingValue) return $obj;
        if (!$obj || ($obj instanceof Collection)) return null;

        $sum = $obj->{Str::snake($lastRelation) . '_sum_' . $column};
        return $sum === null ? new MissingValue() : $sum;
    }
}
