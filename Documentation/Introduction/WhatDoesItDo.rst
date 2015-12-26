What does it do?
^^^^^^^^^^^^^^^^

It slows down the warming of the earth.

Tests with apachebench can show an increase in performance (requests served per second) by a factor of 230!

This extension generates static html files from static pages. If a static page exists, mod_rewrite will redirect the visitor to the static page. This means that TYPO3 will not be loaded at all. Your server will have less work to do and will use less power. This helps to keep our earth cool ;-)

This extension works transparently together with the TYPO3 cache. Static files will be generated for all pages that TYPO3 caches in the cache_pages table. It uses the same decision making logic the TypoScriptFrontendController::sendCacheHeaders function uses to decide if a static file will be generated. It integrates into the TYPO3 caching framework, allowing cache flushes from DataHandler::clear_cache to remove static files.

Static files are created by default (following the sendCacheHeaders logic) but only when the uri contains no '?' (no query string).
