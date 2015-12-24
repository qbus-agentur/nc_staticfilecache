Nginx configuration
^^^^^^^^^^^^^^^^^^^

We introduce a named location_ (@sfc) and replace the invocation of TYPO3's index.php will a redirect to @sfc.
@sfc includes all checks whether the current request can be handled with a static file or needs to be directed to TYPO3.
The error_page_ functionality is used to perform an internal redirect. Thus the if-blocks only need one return statement, which is save according to `If Is Evil`_. 
If all checks pass, the try_files_ directive is used to find the files in typo3temp/tx_ncstaticfilecache and if not found redirect to TYPO3's index.php
Note that no explicit file existence is needed since that's performed by try_files_ by using the first found file (or redirects to the uri given in the last parameter)

In your nginx configuration you would replace your / location, which probably looks like the following:

.. code-block:: nginx

   location / {
       try_files $uri $uri/ /index.php$is_args$args;
   }

By the following configuration:

.. code-block:: nginx

   location /typo3temp/tx_ncstaticfilecache {
       deny all;
   }

   location / {
       try_files $uri $uri/ @sfc;
   }

   # Special root site case. prevent "try_files" + "index" from skipping the cache
   # by accessing /index.php directly
   location =/ {
       recursive_error_pages on;
       error_page 405 = @sfc;
       return 405;
   }

   location @sfc {
       # Perform an internal redirect to TYPO3 if any of the required
       # conditions for static file cache don't match
       error_page 405 = /index.php$is_args$args;

       # Query String needs to be empty
       if ($args != '') {
           return 405;
       }

       # We can't serve static files for logged-in BE/FE users
       if ($cookie_nc_staticfilecache = 'fe_typo_user_logged_in') {
           return 405;
       }
       if ($cookie_be_typo_user != '') {
           return 405;
       }

       # Ensure we redirect to TYPO3 for non GET/HEAD requests
       if ($request_method !~ ^(GET|HEAD)$ ) {
           return 405;
       }

       charset utf8;
       try_files /typo3temp/tx_ncstaticfilecache/${scheme}/${host}${uri}/index.html
             /typo3temp/tx_ncstaticfilecache/${scheme}/${host}${uri}
             =405;
   }

.. _location: http://nginx.org/en/docs/http/ngx_http_core_module.html#location
.. _error_page: http://nginx.org/en/docs/http/ngx_http_core_module.html#error_page
.. _`If Is Evil`: https://www.nginx.com/resources/wiki/start/topics/depth/ifisevil/
.. _try_files: http://nginx.org/en/docs/http/ngx_http_core_module.html#try_files
