# Installing explayouts_relation_list_query

## Requirements

- Exponential Legacy / Exponential 6
- PHP 8.1, 8.2, 8.3 or 8.4

## Dependencies

- `explayouts_content_browser` — the queries return `expLayoutsContentBrowserItem` objects. Activate it first.
- `explayouts` — required only for the layout collection integration (the `exp_content_relation_list` / `exp_content_reverse_relation_list` query types and their handlers live there); the classes in this extension also work standalone.

## Steps

1. Place the extension in `extension/explayouts_relation_list_query`.

2. Activate it (after its dependencies) in `settings/override/site.ini.append.php`:

   ```ini
   [ExtensionSettings]
   ActiveExtensions[]=explayouts
   ActiveExtensions[]=explayouts_content_browser
   ActiveExtensions[]=explayouts_relation_list_query
   ```

   To activate it for a single siteaccess only, use `ActiveAccessExtensions[]` in `settings/siteaccess/<name>/site.ini.append.php` instead.

3. Regenerate autoloads:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   ```

4. Clear caches:

   ```bash
   php bin/php/ezcache.php --clear-all --purge --allow-root-user
   ```
