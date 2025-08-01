rm -rf package
rm storage/install.zip
rm storage/update.zip
rsync -av --exclude '.env' --exclude 'auto_builder.sh' --exclude 'vendor/' --exclude 'storage/app/public/' --exclude '.git/' --exclude '.idea/' ./ ./package

cd package
php composer.phar update
cp .env.example .env
php artisan key:generate
php artisan passport:keys --force

mkdir -p storage/framework/{sessions,views,cache}
chmod -R 775 framework

php artisan prepare:installable
chmod -R 755 .
zip -r ../storage/install.zip .

php artisan prepare:updatable
rm -rf installation
rm -rf storage
rm .env
zip -r ../storage/update.zip .

pwd

cd ../
rm -rf package

