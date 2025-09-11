#!/bin/bash

# =============================================================================
# SIMRT GitHub Publishing Script
# =============================================================================
# Script untuk publish project SIMRT ke GitHub dengan semua persiapan
# production yang diperlukan

set -e  # Exit jika ada error

# Colors untuk output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Fungsi logging
log_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

log_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

log_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

log_error() {
    echo -e "${RED}❌ $1${NC}"
}

# Banner
echo -e "${BLUE}"
echo "=================================="
echo "🏠 SIMRT GitHub Publishing Script"
echo "=================================="
echo -e "${NC}"

# Validasi requirements
log_info "Checking requirements..."

# Check Git
if ! command -v git &> /dev/null; then
    log_error "Git tidak ditemukan. Install Git terlebih dahulu."
    exit 1
fi

# Check jika di root project
if [ ! -f "composer.json" ] || [ ! -f "artisan" ]; then
    log_error "Script harus dijalankan dari root directory Laravel project"
    exit 1
fi

log_success "Requirements check passed"

# 1. Cleanup dan persiapan files
log_info "Preparing project for GitHub..."

# Remove development files yang tidak perlu
rm -rf storage/logs/*.log 2>/dev/null || true
rm -rf bootstrap/cache/*.php 2>/dev/null || true
rm -rf storage/framework/cache/data/* 2>/dev/null || true
rm -rf storage/framework/sessions/* 2>/dev/null || true
rm -rf storage/framework/views/* 2>/dev/null || true
rm -rf node_modules 2>/dev/null || true

# Create production directories
mkdir -p storage/app/public
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p storage/logs

# Set permissions (untuk environment Unix)
if [[ "$OSTYPE" != "msys" && "$OSTYPE" != "win32" ]]; then
    chmod -R 755 storage bootstrap/cache
fi

log_success "Project cleaned and prepared"

# 2. Build production assets
log_info "Building production assets..."

if [ -f "package.json" ]; then
    # Install Node dependencies
    if command -v npm &> /dev/null; then
        npm install --production
        npm run build
        log_success "Assets built successfully"
    else
        log_warning "NPM tidak ditemukan. Build assets manual setelah deployment"
    fi
else
    log_warning "package.json tidak ditemukan"
fi

# 3. Composer optimization
log_info "Optimizing Composer..."

if command -v composer &> /dev/null; then
    composer install --optimize-autoloader --no-dev
    log_success "Composer optimized"
else
    log_warning "Composer tidak ditemukan. Run composer install manual setelah deployment"
fi

# 4. Laravel optimization
log_info "Optimizing Laravel..."

# Clear dan cache routes, config, views
php artisan config:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true

# Production optimizations (optional - comment out untuk development)
# php artisan config:cache
# php artisan route:cache
# php artisan view:cache

log_success "Laravel optimized"

# 5. Security check
log_info "Running security checks..."

# Check .env file tidak tercommit
if [ -f ".env" ]; then
    if ! grep -q "^\.env$" .gitignore 2>/dev/null; then
        echo ".env" >> .gitignore
        log_warning "Added .env to .gitignore"
    fi
fi

# Check sensitive files
SENSITIVE_FILES=(
    ".env"
    ".env.local"
    ".env.production"
    "storage/oauth-private.key"
    "storage/oauth-public.key"
)

for file in "${SENSITIVE_FILES[@]}"; do
    if [ -f "$file" ] && git ls-files --error-unmatch "$file" >/dev/null 2>&1; then
        log_error "CRITICAL: File sensitif '$file' ada di Git tracking!"
        log_error "Hapus file ini dari Git: git rm --cached $file"
        exit 1
    fi
done

log_success "Security checks passed"

# 6. Git setup
log_info "Setting up Git repository..."

# Initialize Git jika belum
if [ ! -d ".git" ]; then
    git init
    log_success "Git repository initialized"
fi

# Add all files
git add .

# Commit jika ada changes
if ! git diff --staged --quiet; then
    echo ""
    log_info "Files yang akan di-commit:"
    git diff --name-only --staged
    echo ""

    read -p "Masukkan commit message (default: 'Initial production-ready commit'): " commit_msg
    commit_msg=${commit_msg:-"Initial production-ready commit"}

    git commit -m "$commit_msg"
    log_success "Changes committed"
else
    log_info "No changes to commit"
fi

# 7. GitHub setup
echo ""
log_info "GitHub Repository Setup"
echo "Pilih opsi setup GitHub:"
echo "1. Repository baru (akan dibuat otomatis)"
echo "2. Repository existing (sudah ada di GitHub)"
echo "3. Skip GitHub setup (manual setup)"

read -p "Pilih opsi (1-3): " github_option

case $github_option in
    1)
        # New repository
        read -p "Masukkan nama repository: " repo_name
        read -p "Masukkan GitHub username: " github_user

        # Check GitHub CLI
        if command -v gh &> /dev/null; then
            # Create repo dengan GitHub CLI
            gh repo create "$repo_name" --public --description "SIMRT - Sistem Informasi Manajemen RT"
            git remote add origin "https://github.com/$github_user/$repo_name.git"
            log_success "GitHub repository created"
        else
            log_warning "GitHub CLI tidak ditemukan"
            log_info "Buat repository manual di GitHub: https://github.com/new"
            log_info "Kemudian jalankan:"
            echo "git remote add origin https://github.com/$github_user/$repo_name.git"
        fi
        ;;
    2)
        # Existing repository
        read -p "Masukkan GitHub repository URL (HTTPS): " repo_url
        git remote add origin "$repo_url" 2>/dev/null || git remote set-url origin "$repo_url"
        log_success "Remote origin set"
        ;;
    3)
        log_info "Skipping GitHub setup"
        ;;
    *)
        log_warning "Invalid option, skipping GitHub setup"
        ;;
esac

# 8. Push to GitHub
if git remote get-url origin >/dev/null 2>&1; then
    echo ""
    log_info "Pushing to GitHub..."

    # Set main branch sebagai default
    git branch -M main

    # Push
    if git push -u origin main; then
        log_success "Successfully pushed to GitHub!"

        # Get repository URL
        origin_url=$(git remote get-url origin)
        if [[ $origin_url == *".git" ]]; then
            web_url=${origin_url%.git}
        else
            web_url=$origin_url
        fi

        echo ""
        log_success "🎉 Repository berhasil di-publish!"
        log_info "Repository URL: $web_url"
        log_info "Live demo akan tersedia setelah deployment"

    else
        log_error "Failed to push to GitHub"
        log_info "Coba manual: git push -u origin main"
    fi
else
    log_warning "No GitHub remote configured"
fi

# 9. Deployment instructions
echo ""
log_info "=== NEXT STEPS - DEPLOYMENT ==="
echo ""
echo "1. 🌐 HOSTING PLATFORM:"
echo "   - DigitalOcean App Platform (Recommended)"
echo "   - Heroku"
echo "   - VPS/Cloud Server"
echo ""
echo "2. 📋 DEPLOYMENT CHECKLIST:"
echo "   ✅ Environment variables (.env production)"
echo "   ✅ Database setup (MySQL)"
echo "   ✅ Google OAuth credentials"
echo "   ✅ Domain & SSL certificate"
echo ""
echo "3. 📖 DOCUMENTATION:"
echo "   - Setup guide: DEPLOYMENT.md"
echo "   - OAuth setup: GOOGLE_OAUTH_SETUP.md"
echo "   - Project info: README.md"
echo ""

# 10. Post-deployment commands
log_info "Commands untuk server production:"
echo ""
echo "# Install dependencies"
echo "composer install --optimize-autoloader --no-dev"
echo "npm install && npm run build"
echo ""
echo "# Laravel setup"
echo "php artisan key:generate"
echo "php artisan migrate --force"
echo "php artisan storage:link"
echo "php artisan config:cache"
echo "php artisan route:cache"
echo "php artisan view:cache"
echo ""

log_success "🚀 GitHub publishing completed successfully!"
log_info "Lihat DEPLOYMENT.md untuk panduan deployment lengkap"

echo -e "${BLUE}"
echo "=================================="
echo "✨ Happy Deploying!"
echo "=================================="
echo -e "${NC}"
