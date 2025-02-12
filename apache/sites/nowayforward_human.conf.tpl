<VirtualHost *:8000>
  ServerName localhost
  DocumentRoot "${REPOSITORY}/views"
  Alias /archives "${ARCHIVES_DIR}"

  <FilesMatch \.php$>
    <If "'${PHP_FPM_SOCKET}' != ''">
      SetHandler "proxy:unix:${PHP_FPM_SOCKET}|fcgi://localhost/"
    </If>
  </FilesMatch>

  # 5 minutes
  TimeOut 300

  RewriteEngine On

  RewriteCond %{HTTP:Authorization} ^(.*)
  RewriteRule .* - [e=HTTP_AUTHORIZATION:%1]

  RewriteCond %{REQUEST_URI} !/archives.*
  RewriteCond %{DOCUMENT_ROOT}%{REQUEST_FILENAME} !-f
  RewriteRule ^(.*)$ /global/router.php
</VirtualHost>
