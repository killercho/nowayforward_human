<VirtualHost *:8000>
  ServerName localhost
  DocumentRoot "${REPOSITORY}/views"
  Alias /controllers "${REPOSITORY}/controllers"

  <FilesMatch \.php$>
    SetHandler "proxy:unix:${PHP_FPM_SOCKET}|fcgi://localhost/"
  </FilesMatch>
</VirtualHost>
