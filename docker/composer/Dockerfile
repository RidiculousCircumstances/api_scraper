FROM composer:latest
ARG APP_UID
ARG APP_GID

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/
RUN install-php-extensions redis
# ADD and set Group
RUN mkdir -p /var/www/ \
    && wget -O /usr/local/bin/dumb-init "https://github.com/Yelp/dumb-init/releases/download/v1.2.2/dumb-init_1.2.2_amd64" \
    && chmod +x /usr/local/bin/dumb-init \
#    && chown -R ${APP_UID}:${APP_GID} /var/www/ \

# Run in work space
WORKDIR /var/www/

ENTRYPOINT ["/usr/local/bin/dumb-init"]