FROM webdevops/php-nginx:7.4


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY --from=node:16.3 /usr/local/bin/node /usr/local/bin/node
# COPY --from=node:16.3 /usr/local/bin/npm /usr/local/bin/npm
# RUN curl -s -L npmjs.org/install.sh | sh
RUN cd /tmp && wget https://npmjs.org/install.sh && chmod +x install.sh && sh install.sh
RUN node -v
RUN npm -v
ENV WEB_DOCUMENT_ROOT /app/public
ENV APP_ENV production
WORKDIR /app
COPY ./frontend .
RUN apt update
RUN cd /tmp && wget https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb
RUN cd /tmp && apt install -y ./google-chrome-stable_current_amd64.deb
RUN cd /tmp && dpkg -i google-chrome-stable_current_amd64.deb

RUN composer install --no-interaction --optimize-autoloader --no-dev
# Optimizing Configuration loading
RUN php artisan config:cache
# Optimizing Route loading
RUN php artisan route:cache
# Optimizing View loading
RUN php artisan view:cache

RUN chown -R application:application .

RUN mkdir backend
COPY ./backend ./backend
RUN npm i -g yarn
RUN cd backend &&  npm install
EXPOSE 80
WORKDIR /app/backend
CMD ["node", "app.js"]