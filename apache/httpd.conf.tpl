ServerRoot ${SERVER_ROOT}
Listen 127.0.0.1:${SERVER_PORT}

LoadModule proxy_module modules/mod_proxy.so
LoadModule proxy_fcgi_module modules/mod_proxy_fcgi.so

LoadModule mpm_prefork_module modules/mod_mpm_prefork.so
# LoadModule mpm_worker_module modules/mod_mpm_worker.so
# LoadModule mpm_event_module /path/to/modules/mod_mpm_event.so


LoadModule dir_module modules/mod_dir.so
LoadModule authz_core_module modules/mod_authz_core.so
LoadModule unixd_module modules/mod_unixd.so
LoadModule mime_module modules/mod_mime.so
LoadModule alias_module modules/mod_alias.so
LoadModule env_module modules/mod_env.so
LoadModule rewrite_module modules/mod_rewrite.so
#LoadModule log_config_module modules/mod_log_config.so

Define ROOT ${ROOT_DIR}
Include ${ROOT_DIR}/sites/*.conf

ServerName localhost

ErrorLog "${ROOT_DIR}/error.log"
PidFile "${ROOT_DIR}/httpd.pid"

User ${USER}
Group users

AddType application/javascript .js
