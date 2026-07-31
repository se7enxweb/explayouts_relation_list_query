Exponential Layouts Relation List Query
=======================================

General description
-------------------

Exponential Layouts Relation List Query (`explayouts_relation_list_query`)
provides relation list collection query handlers for Exponential Layouts on
Exponential Legacy / Exponential 6. Given a source content object, the
queries return its `ezobjectrelationlist` field relations (or its reverse
relations) as `expLayoutsContentBrowserItem` objects, with content-class
filtering, sorting and pagination — the data source pattern used by layout
collection blocks.

This extension is an Exponential Legacy port inspired by
`netgen-layouts/layouts-ibexa-relation-list-query` and provides the
following capabilities:

- Forward relation queries - Use this feature to read an
  `ezobjectrelationlist` field and return the related items in editor-defined
  or attribute-based order.
- Reverse relation queries - Use this feature to find the objects that
  relate back to a given item, optionally restricted to relations made
  through a specific field ("who links here").
- Filtering, sorting and pagination - Use this feature to include/exclude
  content classes, choose sort type and direction, and page the results.
- Layout collection integration - Use the `exp_content_relation_list` and
  `exp_content_reverse_relation_list` query types to drive dynamic
  collections from the layouts admin UI.

Features
--------

The following features are provided by the Exponential Layouts Relation
List Query extension:

- A forward relation query, `expLayoutsRelationListQuery`, that resolves a
  source content object (by `content_id`, or by `location_id` when
  `use_current_location` prefers it), reads the named
  `ezobjectrelationlist` field and returns the related items as
  `expLayoutsContentBrowserItem[]`. Items are built from each related
  object's main node; related objects without a main node are skipped.
- A reverse relation query, `expLayoutsReverseRelationListQuery`, that finds
  objects relating back to the selected item. When a `field_identifier` is
  given, it is resolved to a class attribute ID on the source object's class
  and passed to `eZContentObject::reverseRelatedObjectList()`, so results
  can be limited to relations made through one specific field.
- A convenience factory, `expLayoutsRelationListQueryFactory`, with
  `relation()` / `reverseRelation()` shortcuts; direct construction
  (`new expLayoutsRelationListQuery()`) works identically.
- A single, uniform entry point — `execute( $params = array() )` — on both
  query classes, with a rich parameter set:
  - `content_id` — source content object ID.
  - `location_id` — source location/node ID (used when `content_id` is
    absent, or preferred when `use_current_location` is set).
  - `use_current_location` — prefer `location_id` over `content_id` when
    both are given.
  - `field_identifier` — identifier of the `ezobjectrelationlist` field
    (required for the forward query; optional field filter for the reverse
    query).
  - `sort_type` — `defined_by_field` (forward default, i.e. the order
    editors arranged the relations in), `date_published` (reverse default),
    `date_modified`, `content_name`, `location_priority`.
  - `sort_direction` — `asc` or `desc` (default `desc`).
  - `content_types` / `content_types_filter` — class identifier list with
    `include` (default) or `exclude` semantics.
  - `limit` / `offset` — pagination; `0` means unlimited.
- Layout collection integration through the query type identifiers
  `exp_content_relation_list` and `exp_content_reverse_relation_list`,
  registered in `extension/explayouts/settings/explayouts.ini.append.php`
  and selectable as the query type of a dynamic collection in the layouts
  admin UI.
- Stage-by-stage `protected` internals — `resolveContent()`,
  `relatedObjectIds()` / `reverseRelatedObjects()`, `filterByContentType()`,
  `sort()`, `applyLimitOffset()` — so subclasses can replace individual
  steps (for example, adding `ezobjectrelation` support in
  `relatedObjectIds()`).
- Standalone by design: the classes work in custom modules, block handlers
  and CLI scripts, independent of the layout collection runtime.

Version
-------

- The current version of Exponential Layouts Relation List Query is 1.0.0
- Last Major update: July 30, 2026

Copyright
---------

- Exponential Layouts Relation List Query is copyright 1998 - 2026 7x
- See: [LICENSE.md](LICENSE.md) for more information on the terms of the
  copyright and license

License
-------

Exponential Layouts Relation List Query is licensed under the GNU General
Public License.

The complete license agreement is included in the [LICENSE.md](LICENSE.md)
file.

Exponential Layouts Relation List Query is free software: you can
redistribute it and/or modify it under the terms of the GNU General Public
License as published by the Free Software Foundation, either version 2 of
the License or at your option a later version.

Exponential Layouts Relation List Query is distributed in the hope that it
will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General
Public License for more details.

The GNU GPL gives you the right to use, modify and redistribute
Exponential Layouts Relation List Query under certain conditions. The GNU
GPL license is distributed with the software, see the file
[LICENSE.md](LICENSE.md).

It is also available at http://www.gnu.org/licenses/gpl.txt

You should have received a copy of the GNU General Public License along with
Exponential Layouts Relation List Query in [LICENSE.md](LICENSE.md). If
not, see http://www.gnu.org/licenses/.

Using Exponential Layouts Relation List Query under the terms of the GNU
GPL is free (as in freedom).

For more information or questions please contact info@se7enx.com

Requirements
------------

The following requirements exist for using the Exponential Layouts Relation
List Query extension:

Exponential version
- Make sure you use Exponential 6 / Exponential Legacy.

PHP version
- Make sure you have PHP 8.1, 8.2, 8.3 or 8.4.

Extension dependencies
- `explayouts_content_browser` — the queries return
  `expLayoutsContentBrowserItem` objects. Activate it first.
- `explayouts` — required only for the layout collection integration (the
  `exp_content_relation_list` / `exp_content_reverse_relation_list` query
  types and their handlers live there); the classes in this extension also
  work standalone.

Installation
------------

Installation is the standard extension procedure: place the extension in
`extension/explayouts_relation_list_query`, activate it after its
dependencies via `ActiveExtensions[]` (or `ActiveAccessExtensions[]` for a
single siteaccess), regenerate autoloads with
`php bin/php/ezpgenerateautoloads.php -e` and clear caches with
`php bin/php/ezcache.php --clear-all --purge --allow-root-user`.

See [INSTALL.md](INSTALL.md) for the complete step-by-step installation
instructions.

Usage
-----

The extension ships three classes:

| Class | File | Purpose |
|-------|------|---------|
| `expLayoutsRelationListQuery` | `classes/explayoutsrelationlistquery.php` | Reads an `ezobjectrelationlist` field and returns the related items |
| `expLayoutsReverseRelationListQuery` | `classes/explayoutsreverserelationlistquery.php` | Finds objects that relate back to the selected item, optionally per field |
| `expLayoutsRelationListQueryFactory` | `classes/explayoutsrelationlistqueryfactory.php` | `relation()` / `reverseRelation()` factory shortcuts |

A quick example:

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

Reverse relations ("who links here"):

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

For layout collections, the Exponential Layouts collection runtime
registers the relation list query types in
`extension/explayouts/settings/explayouts.ini.append.php`:

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

Pick `exp_content_relation_list` or `exp_content_reverse_relation_list` as
the query type of a dynamic collection in the layouts admin UI. Those
handler classes ship with the `explayouts` extension and support
`use_current_location` against the currently viewed page; the classes in
this extension are the standalone port of the upstream package for direct
PHP use (custom modules, block handlers, CLI scripts).

The full usage guide in [doc/USAGE.md](doc/USAGE.md) covers the complete
parameter reference, usage scenarios ("related articles" boxes, "who links
here" listings, location-based source resolution, CLI usage), how the two
layers relate, and the three customization layers of this stack: the
settings layer (overriding the query type `Name=` / `Handler=` through the
INI configuration cascade), the template layer (collection items are
rendered by your layouts block templates) and the PHP layer (the
`protected` stage methods and custom handler classes).

Documentation
-------------

| Document | Description |
|----------|-------------|
| [INSTALL.md](INSTALL.md) | Requirements, dependencies and step-by-step activation instructions |
| [doc/USAGE.md](doc/USAGE.md) | Parameter reference, scenarios, collection query types and the settings/template/PHP customization layers |
| [doc/FAQ.md](doc/FAQ.md) | Frequently asked questions and answers |
| [doc/TODO.md](doc/TODO.md) | Known gaps and planned improvements |
| [doc/SUPPORT.md](doc/SUPPORT.md) | Where and how to get help |
| [LICENSE.md](LICENSE.md) | The complete GNU General Public License agreement |

Troubleshooting
---------------

Read the FAQ
- Some problems are more common than others. The most common ones are listed
  in [doc/FAQ.md](doc/FAQ.md).

Use our support systems
- If you find a bug or defect, please report it to the
  [Exponential Layouts Relation List Query: Issue Tracker](https://github.com/se7enxweb/explayouts_relation_list_query/issues)
- For commercial support and custom development please visit
  [se7enx.com](https://se7enx.com)
