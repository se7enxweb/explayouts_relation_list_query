<?php
class expLayoutsRelationListQueryFactory
{
    public static function relation()
    {
        return new expLayoutsRelationListQuery();
    }

    public static function reverseRelation()
    {
        return new expLayoutsReverseRelationListQuery();
    }
}
