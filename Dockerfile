# Plain Alpine Linux base; PHP comes from the distro packages.
# PHP 8.5 is required by composer.json (ext-uri ships with 8.5 core).
FROM alpine:3.24

# pdo, uri are in core php85; gd, pdo_sqlite and fileinfo are split packages.
RUN apk add --no-cache php85 php85-gd php85-pdo_sqlite php85-fileinfo composer

# upload limits (image uploads go through POST)
RUN printf "upload_max_filesize=8M\npost_max_size=10M\n" > /etc/php85/conf.d/uploads.ini

WORKDIR /app

COPY . .
RUN composer update \
    && composer install \
    && chmod +x docker/entrypoint.sh bin/db bin/generate-seed

EXPOSE 5173

ENTRYPOINT ["docker/entrypoint.sh"]
CMD ["composer", "serve:lan"]
