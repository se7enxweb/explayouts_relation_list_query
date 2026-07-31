# TODO — explayouts_relation_list_query

- `filterByContentType()` and `sort()` in both query classes call `$item->attribute( 'class_identifier' )`, `$item->attribute( 'object_id' )` and `$item->attribute( $field )` on `expLayoutsContentBrowserItem`, which has no `attribute()` method — content-type filtering and every non-`defined_by_field` sort fail when reached. Either add an eZ-style `attribute()` accessor to the item class or switch to its public properties.
- The sort map targets `object_published`, `object_modified` and `priority`, none of which exist on the item (`published`/`modified` are formatted strings, and node priority is not captured at all). Sorting needs real timestamp/priority data on the item.
- `use_current_location` requires an explicit `location_id` parameter; there is no automatic detection of the currently viewed page (unlike `expLayoutsRelationListQueryHandler` in `explayouts`, which uses `expLayoutsExpSiteApi::currentObject()`).
- `settings/design.ini.append.php` registers `explayouts_relation_list_query` as a design extension, but the extension has no `design/` directory.
- The classes are not wired into any `[QueryType_*]` INI block; the collection query types with the same purpose are handled by separate classes inside `explayouts`. Decide whether these ports should become the INI handlers or stay a direct-call API.
- No support for the single-value `ezobjectrelation` datatype.
