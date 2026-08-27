FROM richarvey/nginx-php-fpm:php81

# انسخي كل ملفات المشروع جوه الكونتينر
COPY . .

# إعدادات الصورة (متلمسيهاش دلوقتي)
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# إعدادات Laravel الأساسية (هتتحط القيم الحقيقية من Render Environment Variables)
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# صلاحيات المجلدات اللي Laravel محتاج يكتب فيها
RUN chmod -R 755 /var/www/html/storage
RUN chmod -R 755 /var/www/html/bootstrap/cache

# سكريبت البداية اللي بيشغل composer install والمايجريشن أوتوماتيك كل مرة الكونتينر يشتغل
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

CMD ["/entrypoint.sh"]