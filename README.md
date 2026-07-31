# explayouts_relation_list_query

Relation list collection query handlers for Exponential Layouts on Exponential Legacy / Exponential 6. Given a source content object, the queries return its `ezobjectrelationlist` field relations (or its reverse relations) as `expLayoutsContentBrowserItem` objects, with content-class filtering, sorting and pagination — the data source pattern used by layout collection blocks.

Exponential Legacy port inspired by `netgen-layouts/layouts-ibexa-relation-list-query`.

## Key classes

| Class | File | Purpose |
|-------|------|---------|
| `expLayoutsRelationListQuery` | `classes/explayoutsrelationlistquery.php` | Reads an `ezobjectrelationlist` field and returns the related items |
| `expLayoutsReverseRelationListQuery` | `classes/explayoutsreverserelationlistquery.php` | Finds objects that relate back to the selected item, optionally per field |
| `expLayoutsRelationListQueryFactory` | `classes/explayoutsrelationlistqueryfactory.php` | `relation()` / `reverseRelation()` factory shortcuts |

## Quick example

```php
<?php
$items = expLayoutsRelationListQueryFactory::relation()->execute( array(
    'content_id' => 42,
    'field_identifier' => 'related_items',
    'sort_type' => 'defined_by_field',
    'limit' => 10,
) );

foreach ( $items as $item )
{
    echo $item->name . "\n"; // expLayoutsContentBrowserItem public property
}
?>
```

## Collection configuration

The Exponential Layouts collection runtime registers the relation list query types `exp_content_relation_list` and `exp_content_reverse_relation_list` in `extension/explayouts/settings/explayouts.ini.append.php`; see [doc/USAGE.md](doc/USAGE.md) for how the two layers relate.

## Documentation

- [INSTALL.md](INSTALL.md) — activation steps and dependencies
- [doc/USAGE.md](doc/USAGE.md) — parameters, scenarios, collection query types and customization
- [doc/FAQ.md](doc/FAQ.md) — frequently asked questions
- [doc/TODO.md](doc/TODO.md) — known gaps and planned work
- [doc/SUPPORT.md](doc/SUPPORT.md) — how to get help
