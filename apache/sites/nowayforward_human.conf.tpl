<VirtualHost *:8000>
  ServerName localhost
  DocumentRoot "${REPOSITORY}/views"
  Alias /archives "${ARCHIVES_DIR}"

  <FilesMatch \.php$>
    SetHandler "proxy:unix:${PHP_FPM_SOCKET}|fcgi://localhost/"
  </FilesMatch>

  RewriteEngine On

  RewriteCond %{HTTP:Authorization} ^(.*)
  RewriteRule .* - [e=HTTP_AUTHORIZATION:%1]

  RewriteCond %{REQUEST_URI} !/archives.*
  RewriteCond %{DOCUMENT_ROOT}%{REQUEST_FILENAME} !-f
  RewriteCond %{DOCUMENT_ROOT}%{REQUEST_FILENAME} !-d
  RewriteRule ^(.*)$ /global/router.php
</VirtualHost>
