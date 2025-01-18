<VirtualHost *:8000>
  ServerName localhost
  DocumentRoot "${REPOSITORY}/views"

  <FilesMatch \.php$>
    SetHandler "proxy:unix:${PHP_FPM_SOCKET}|fcgi://localhost/"
  </FilesMatch>
</VirtualHost>
