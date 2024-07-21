FROM debian:bookworm-slim

# install httpd runtime dependencies
# https://httpd.apache.org/docs/2.4/install.html#requirements
RUN set -eux; \
	apt-get update; \
	apt-get install -y --no-install-recommends \
# https://github.com/docker-library/httpd/issues/214
		ca-certificates \
		libaprutil1-ldap \
# https://github.com/docker-library/httpd/issues/209
		libldap-common \
        apache2 \
        apache2-utils \
		php-cli \
	; \
	rm -rf /var/lib/apt/lists/* \
	; \
	apt clean; \
	a2enmod ext_filter rewrite include setenvif; \
	a2enconf localized-error-pages; \
	rm /var/www/html/index.html
#link logfiles see https://serverfault.com/questions/711168/writing-apache2-logs-to-stdout-stderr
RUN ln -sf /proc/self/fd/1 /var/log/apache2/access.log && \
    ln -sf /proc/self/fd/1 /var/log/apache2/error.log
COPY ./base/. /
#COPY ./errordoc/ /var/www/html/
#COPY ./errordoc/include/* /usr/share/apache2/error/include/
ARG SERVER_NAME_BUILD=""
ARG ENABLE_WHOAMI="true"

ENV DEBUG_ENV="false"
ENV SERVER_ADMIN=""
#set build args as env default
ENV ENABLE_WHOAMI=$ENABLE_WHOAMI
ENV DEFAULT_SERVER_NAME=$SERVER_NAME_BUILD

EXPOSE 80
#CMD ["apache2ctl", "-D", "FOREGROUND"]
CMD ["sh", "-c", "DEFAULT_SERVER_NAME=\"${DEFAULT_SERVER_NAME:-$(hostname -s)}\" SERVER_ADMIN=\"${SERVER_ADMIN:-webmaster@$DEFAULT_SERVER_NAME}\" && apache2ctl -D FOREGROUND"]