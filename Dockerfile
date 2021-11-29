FROM webdevops/php-nginx:7.4

# Copy Composer binary from the Composer official Docker image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY --from=node:16.3 /usr/local/bin/node /usr/local/bin/node
RUN cd /tmp && wget https://npmjs.org/install.sh && chmod +x install.sh && sh install.sh
ENV WEB_DOCUMENT_ROOT /app/public
ENV APP_ENV production
WORKDIR /app
COPY ./frontend .

RUN composer install --no-interaction --optimize-autoloader --no-dev
# Optimizing Configuration loading
RUN php artisan config:cache
# Optimizing Route loading
RUN php artisan route:cache
# Optimizing View loading
RUN php artisan view:cache
RUN mkdir backend
COPY ./backend ./backend
RUN cd backend && npm install
RUN chown -R application:application .
# ENTRYPOINT [ "/entrypoint" ]
# COPY node-entry.sh /entrypoint
# COPY waapi.service /lib/systemd/system

# RUN ["/bin/bash", "-c", "service daemon-reload"]
# RUN /bin/bash -c "systemtl enable waapi"
# RUN service waapi enable
EXPOSE 8005
# CMD ["service","waapi","start"]
# RUN pm2-runtime backend/app.js
# RUN 
COPY conf/ /opt/docker/
# ENTRYPOINT ["/waapientry.sh"]
# COPY ./waapientry.sh /