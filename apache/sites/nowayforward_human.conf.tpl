<VirtualHost *:8000>
  ServerName localhost
  DocumentRoot "${REPOSITORY}/views"
  Alias /archives "${ARCHIVES_DIR}"

  <FilesMatch \.php$>
    <If "'${PHP_FPM_SOCKET}' != ''">
      SetHandler "proxy:unix:${PHP_FPM_SOCKET}|fcgi://localhost/"
    </If>
  </FilesMatch>

  # Database
  SetEnv SERVER ${SERVER}
  SetEnv PORT ${PORT}
  SetEnv USER ${USER}
  SetEnv PASSWORD ${PASSWORD}
  SetEnv MYSQL_UNIX_SOCKET ${MYSQL_UNIX_SOCKET}

  # Project
  SetEnv ARCHIVES_DIR ${ARCHIVES_DIR}

  RewriteEngine On

  RewriteCond %{HTTP:Authorization} ^(.*)
  RewriteRule .* - [e=HTTP_AUTHORIZATION:%1]

  RewriteCond %{REQUEST_URI} !/archives.*
  RewriteCond %{DOCUMENT_ROOT}%{REQUEST_FILENAME} !-f
  RewriteRule ^(.*)$ /global/router.php
</VirtualHost>
