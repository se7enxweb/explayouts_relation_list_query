# FAQ — explayouts_relation_list_query

## Which datatypes are supported?

Only `ezobjectrelationlist`. The forward query checks the field's `data_type_string` and returns an empty array for anything else, including the single-value `ezobjectrelation` datatype.

## What does `sort_type = defined_by_field` do?

It preserves the order in which the relations are stored in the field (the order editors arranged them in). `sort_direction = desc` reverses that order. This is the forward query's default; the reverse query defaults to `date_published`.

## Is this the class my layout collection uses?

Not directly. Dynamic collections configured with the `exp_content_relation_list` / `exp_content_reverse_relation_list` query types are executed by `expLayoutsRelationListQueryHandler` / `expLayoutsReverseRelationListQueryHandler`, which ship with the `explayouts` extension. This extension is the standalone port of the upstream package for direct PHP use.

## Why does `execute()` fail when I pass `content_types` or a date sort?

Known gap: `filterByContentType()` and `sort()` call `$item->attribute( ... )` on `expLayoutsContentBrowserItem`, which does not define an `attribute()` method yet. See [TODO.md](TODO.md). Until fixed, use the default `defined_by_field` sorting without a class filter, or filter/sort the returned items yourself using their public properties.

## Does `use_current_location` detect the currently viewed page?

No. It only tells the query to prefer an explicitly passed `location_id` over `content_id`. Automatic current-page detection is implemented in the `explayouts` collection handlers, not here.

## What do the items look like?

`expLayoutsContentBrowserItem` objects (from `explayouts_content_browser`) with public properties such as `$item->name`, `$item->objectId`, `$item->classIdentifier`, and a `toArray()` method.
