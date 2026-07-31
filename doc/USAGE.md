# Using explayouts_relation_list_query

## Direct PHP usage

Both query classes expose a single method, `execute( $params = array() )`, returning `expLayoutsContentBrowserItem[]`. Use the factory for readability:

```php
<?php
$items = expLayoutsRelationListQueryFactory::relation()->execute( array(
    'content_id' => 42,
    'field_identifier' => 'related_items',
    'sort_type' => 'defined_by_field', // or date_published, date_modified, content_name, location_priority
    'sort_direction' => 'asc',
    'limit' => 10,
    'offset' => 0,
) );

foreach ( $items as $item )
{
    echo $item->name . ' (' . $item->classIdentifier . ")\n";
}
?>
```

Reverse relations:

```php
<?php
$items = expLayoutsRelationListQueryFactory::reverseRelation()->execute( array(
    'content_id' => 42,
    'field_identifier' => 'related_items', // optional: limit to relations made through this field
    'sort_type' => 'date_published',
    'sort_direction' => 'desc',
    'limit' => 10,
) );
?>
```

`new expLayoutsRelationListQuery()` / `new expLayoutsReverseRelationListQuery()` work identically without the factory.

## Parameters

| Parameter | Meaning |
|-----------|---------|
| `content_id` | Source content object ID |
| `location_id` | Source location/node ID (used when `content_id` is absent, or preferred when `use_current_location` is set) |
| `use_current_location` | Prefer `location_id` over `content_id` when both are given |
| `field_identifier` | Identifier of the `ezobjectrelationlist` field (required for the forward query; optional field filter for the reverse query) |
| `sort_type` | `defined_by_field` (forward query default), `date_published` (reverse query default), `date_modified`, `content_name`, `location_priority` |
| `sort_direction` | `asc` or `desc` (default `desc`) |
| `content_types` | Array of class identifiers to filter |
| `content_types_filter` | `include` (default) or `exclude` |
| `limit` / `offset` | Pagination; `0` means unlimited |

Items are built from each related object's main node; related objects without a main node are skipped. Note: `content_types` filtering and the non-default sort types currently hit a known gap in the item class — see [TODO.md](TODO.md).

## Scenario: source object by location instead of object ID

```php
<?php
$items = expLayoutsRelationListQueryFactory::relation()->execute( array(
    'location_id' => 152,
    'use_current_location' => true,
    'field_identifier' => 'related_items',
) );
?>
```

`use_current_location` makes the query resolve the source object from `location_id` even when a `content_id` is also present.

## Scenario: "related articles" box in a custom module or block handler

```php
<?php
$items = expLayoutsRelationListQueryFactory::relation()->execute( array(
    'content_id' => $object->attribute( 'id' ),
    'field_identifier' => 'related_items',
    'limit' => 5,
) );

$tpl->setVariable( 'related', array_map( function( $i ) { return $i->toArray(); }, $items ) );
?>
```

## Scenario: "who links here" listing (reverse relations)

```php
<?php
// All objects relating to object 42 through any field
$all = expLayoutsRelationListQueryFactory::reverseRelation()->execute( array(
    'content_id' => 42,
) );

// Only objects relating through their 'related_items' field
$viaField = expLayoutsRelationListQueryFactory::reverseRelation()->execute( array(
    'content_id' => 42,
    'field_identifier' => 'related_items',
) );
?>
```

The reverse query resolves `field_identifier` to a class attribute ID on the source object's class and passes it to `eZContentObject::reverseRelatedObjectList()`.

## Scenario: CLI usage

```bash
php bin/php/ezexec.php ai/bin/tmp/test_relation_list_query.php --allow-root-user
```

## Collection configuration in Exponential Layouts

The layout collection runtime uses query type identifiers registered in `extension/explayouts/settings/explayouts.ini.append.php`:

```ini
[QuerySettings]
AvailableQueries[]=exp_content_relation_list
AvailableQueries[]=exp_content_reverse_relation_list

[QueryType_exp_content_relation_list]
Name=Exp relation list
Handler=expLayoutsRelationListQueryHandler

[QueryType_exp_content_reverse_relation_list]
Name=Exp reverse relation list
Handler=expLayoutsReverseRelationListQueryHandler
```

Pick `exp_content_relation_list` or `exp_content_reverse_relation_list` as the query type of a dynamic collection in the layouts admin UI. Those handler classes ship with the `explayouts` extension and support `use_current_location` against the currently viewed page; the classes in this extension are the standalone port of the upstream package for direct PHP use (custom modules, block handlers, CLI scripts).

## Customization

### Settings layer (INI)

This extension ships only `settings/design.ini.append.php` (a `DesignExtensions[]` registration; no templates yet). The collection query types shown above live in `explayouts.ini`, so integrators can adjust them from outside through the standard cascade, lowest to highest priority:

1. `settings/*.ini` — kernel defaults
2. `extension/<ext>/settings/*.ini.append.php` — extension defaults (`extension/explayouts/settings/explayouts.ini.append.php` defines the query types)
3. `settings/siteaccess/<siteaccess>/*.ini.append.php` — siteaccess overrides
4. `extension/<ext>/settings/siteaccess/<siteaccess>/*.ini.append.php` — extension siteaccess overrides
5. `settings/override/*.ini.append.php` — global overrides (always win)

For example, `settings/override/explayouts.ini.append.php` can rename a query type's `Name=` or point its `Handler=` at your own class:

```ini
[QueryType_exp_content_relation_list]
Name=Related content
Handler=myRelationListQueryHandler
```

### Template layer (design overrides)

No templates are shipped. Collection items returned by these queries are rendered by the block templates of your layouts design; override those in your design extension, not here.

### PHP layer (extension points)

- `expLayoutsRelationListQuery` and `expLayoutsReverseRelationListQuery` keep every step in `protected` methods — `resolveContent()`, `relatedObjectIds()` / `reverseRelatedObjects()`, `filterByContentType()`, `sort()`, `applyLimitOffset()` — so subclasses can replace individual stages (e.g. support `ezobjectrelation` in `relatedObjectIds()`).
- `expLayoutsRelationListQueryFactory::relation()` / `reverseRelation()` instantiate the concrete classes directly; when subclassing, construct your subclass yourself instead of going through the factory.
- For collection-driven customization, implement your own handler class (following `expLayoutsRelationListQueryHandler` in `explayouts`, static `getParameters()` / `getValues()` / `getCount()` / `isContextual()`) and register it via the INI override shown above.
