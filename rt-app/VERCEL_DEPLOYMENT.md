# Vercel Deployment Guide for SIMRT

## Overview
SIMRT can be deployed to Vercel with some configuration adjustments for Laravel compatibility.

## Prerequisites
- Vercel account
- GitHub repository
- Environment variables configured

## Deployment Steps

### 1. Database Setup
Since Vercel doesn't support MySQL directly, you'll need an external database:

**Recommended Options:**
- **PlanetScale** (MySQL-compatible, free tier)
- **Railway** (PostgreSQL/MySQL)
- **Supabase** (PostgreSQL)
- **Amazon RDS** (Production)

### 2. Environment Variables
Add these to your Vercel project dashboard:

```bash
# Application
APP_NAME=SIMRT
APP_ENV=production
APP_KEY=base64:your-app-key-here
APP_DEBUG=false
APP_URL=https://your-app.vercel.app

# Database (Example for PlanetScale)
DB_CONNECTION=mysql
DB_HOST=your-planetscale-host
DB_PORT=3306
DB_DATABASE=your-database-name
DB_USERNAME=your-username
DB_PASSWORD=your-password

# Cache & Session
CACHE_DRIVER=array
SESSION_DRIVER=cookie
QUEUE_CONNECTION=sync

# Logging
LOG_CHANNEL=stderr
LOG_STDERR_FORMATTER=Monolog\Formatter\JsonFormatter

# Storage
FILESYSTEM_DISK=public

# Mail (Optional)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls

# Google OAuth (Optional)
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI=https://your-app.vercel.app/auth/google/callback
```

### 3. Build Configuration
The `vercel.json` file handles routing and build configuration.

### 4. Database Migration
Since Vercel functions are stateless, run migrations manually:

```bash
# Install Vercel CLI
npm i -g vercel

# Deploy first
vercel --prod

# Run migrations via Vercel function
vercel env pull .env.production
php artisan migrate --force --env=production
```

### 5. File Storage
For file uploads, integrate with:
- **Cloudinary** (images)
- **AWS S3** (files)
- **Vercel Blob** (simple files)

## Common Issues

### 404 Errors
- Ensure `vercel.json` routes are correct
- Check that `api/index.php` exists
- Verify your app key is set

### Database Connection
- Use SSL connections for external databases
- Check database credentials
- Ensure database is accessible from Vercel's IP ranges

### Performance
- Enable OPcache in production
- Use CDN for static assets
- Optimize images and assets

## Limitations
- No persistent file storage
- Cold starts on free tier
- Function timeout limits
- No background jobs (use external queue)

## Alternatives
If Vercel limitations are restrictive, consider:
- **Railway** (full Laravel support)
- **DigitalOcean App Platform**
- **AWS Elastic Beanstalk**
- **Traditional VPS**
