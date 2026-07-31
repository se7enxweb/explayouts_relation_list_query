<?php
class expLayoutsReverseRelationListQuery
{
    public function execute( $params = array() )
    {
        $content = $this->resolveContent( $params );
        if ( !$content instanceof eZContentObject )
            return array();

        $objects = $this->reverseRelatedObjects( $content, $params );
        if ( !is_array( $objects ) || empty( $objects ) )
            return array();

        $items = array();
        foreach ( $objects as $object )
        {
            if ( !$object instanceof eZContentObject )
                continue;

            $nodeId = (int)$object->attribute( 'main_node_id' );
            if ( $nodeId <= 0 )
                continue;

            $node = eZContentObjectTreeNode::fetch( $nodeId );
            if ( $node instanceof eZContentObjectTreeNode )
                $items[] = new expLayoutsContentBrowserItem( $node );
        }

        $items = $this->filterByContentType( $items, $params );
        $items = $this->sort( $items, $params );
        $items = $this->applyLimitOffset( $items, $params );

        return $items;
    }

    protected function resolveContent( $params )
    {
        if ( !empty( $params['use_current_location'] ) && !empty( $params['location_id'] ) )
        {
            $node = eZContentObjectTreeNode::fetch( (int)$params['location_id'] );
            if ( $node instanceof eZContentObjectTreeNode )
                return $node->attribute( 'object' );
        }

        if ( !empty( $params['content_id'] ) )
            return eZContentObject::fetch( (int)$params['content_id'] );

        if ( !empty( $params['location_id'] ) )
        {
            $node = eZContentObjectTreeNode::fetch( (int)$params['location_id'] );
            if ( $node instanceof eZContentObjectTreeNode )
                return $node->attribute( 'object' );
        }

        return null;
    }

    protected function reverseRelatedObjects( $content, $params )
    {
        $fieldIdentifier = isset( $params['field_identifier'] ) ? (string)$params['field_identifier'] : '';
        $attributeID = 0;

        if ( $fieldIdentifier !== '' )
        {
            $class = eZContentClass::fetch( (int)$content->attribute( 'contentclass_id' ) );
            if ( $class instanceof eZContentClass )
            {
                $classAttribute = $class->fetchAttributeByIdentifier( $fieldIdentifier );
                if ( $classAttribute instanceof eZContentClassAttribute )
                    $attributeID = (int)$classAttribute->attribute( 'id' );
            }
        }

        return $content->reverseRelatedObjectList( false, $attributeID, false, array( 'AsObject' => true ) );
    }

    protected function filterByContentType( $items, $params )
    {
        $types = isset( $params['content_types'] ) ? (array)$params['content_types'] : array();
        if ( empty( $types ) )
            return $items;

        $filter = isset( $params['content_types_filter'] ) ? (string)$params['content_types_filter'] : 'include';
        $filtered = array();

        foreach ( $items as $item )
        {
            $identifier = $item instanceof expLayoutsContentBrowserItem
                ? (string)$item->attribute( 'class_identifier' )
                : '';

            $matches = in_array( $identifier, $types, true );

            if ( $filter === 'exclude' )
            {
                if ( !$matches )
                    $filtered[] = $item;
            }
            else
            {
                if ( $matches )
                    $filtered[] = $item;
            }
        }

        return $filtered;
    }

    protected function sort( $items, $params )
    {
        $sortType = isset( $params['sort_type'] ) ? (string)$params['sort_type'] : 'date_published';
        $direction = isset( $params['sort_direction'] ) ? strtolower( (string)$params['sort_direction'] ) : 'desc';
        $ascending = $direction === 'asc';

        $map = array(
            'date_published' => 'object_published',
            'date_modified' => 'object_modified',
            'content_name' => 'name',
            'location_priority' => 'priority',
        );

        if ( !isset( $map[$sortType] ) )
            return $items;

        $field = $map[$sortType];

        usort(
            $items,
            function( $a, $b ) use ( $field, $ascending )
            {
                $valueA = $a->attribute( $field );
                $valueB = $b->attribute( $field );

                if ( is_numeric( $valueA ) && is_numeric( $valueB ) )
                {
                    return $ascending
                        ? ( $valueA <=> $valueB )
                        : ( $valueB <=> $valueA );
                }

                $cmp = strnatcasecmp( (string)$valueA, (string)$valueB );
                return $ascending ? $cmp : -$cmp;
            }
        );

        return $items;
    }

    protected function applyLimitOffset( $items, $params )
    {
        $limit = isset( $params['limit'] ) ? (int)$params['limit'] : 0;
        $offset = isset( $params['offset'] ) ? (int)$params['offset'] : 0;

        if ( $offset > 0 || $limit > 0 )
            $items = array_slice( $items, $offset, $limit > 0 ? $limit : null );

        return $items;
    }
}
