#!/bin/bash

# Color codes for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}Starting deployment...${NC}"

# Navigate to project directory
cd ~/public_html/lar || exit

# Put application in maintenance mode
echo -e "${YELLOW}Putting application in maintenance mode...${NC}"
php artisan down --retry=15 --refresh=15

# Pull latest changes from GitHub
echo -e "${YELLOW}Pulling latest code from GitHub...${NC}"
git pull origin master
if [ $? -ne 0 ]; then
    echo -e "${RED}Git pull failed! Aborting deployment.${NC}"
    php artisan up
    exit 1
fi

# Install/update composer dependencies
echo -e "${YELLOW}Installing Composer dependencies...${NC}"
composer install --no-dev --optimize-autoloader --no-interaction
if [ $? -ne 0 ]; then
    echo -e "${RED}Composer install failed! Aborting deployment.${NC}"
    php artisan up
    exit 1
fi

# Install/update npm dependencies
echo -e "${YELLOW}Installing NPM dependencies...${NC}"
npm install --production=false
if [ $? -ne 0 ]; then
    echo -e "${RED}NPM install failed! Aborting deployment.${NC}"
    php artisan up
    exit 1
fi

# Build assets
echo -e "${YELLOW}Building frontend assets...${NC}"
npm run build
if [ $? -ne 0 ]; then
    echo -e "${RED}NPM build failed! Aborting deployment.${NC}"
    php artisan up
    exit 1
fi

# Run database migrations
echo -e "${YELLOW}Running database migrations...${NC}"
php artisan migrate --force
if [ $? -ne 0 ]; then
    echo -e "${RED}Migration failed! Aborting deployment.${NC}"
    php artisan up
    exit 1
fi

# Clear all caches
echo -e "${YELLOW}Clearing caches...${NC}"
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild caches
echo -e "${YELLOW}Rebuilding caches...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set correct permissions
echo -e "${YELLOW}Setting permissions...${NC}"
chmod -R 755 storage bootstrap/cache

# Bring application back online
echo -e "${YELLOW}Bringing application back online...${NC}"
php artisan up

echo -e "${GREEN}Deployment completed successfully!${NC}"
