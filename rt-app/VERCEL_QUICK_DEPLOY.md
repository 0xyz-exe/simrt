# 🚀 Quick Vercel Deployment Guide for SIMRT

## Step-by-Step Deployment

### 1. **Database Setup** (Required First)
Since Vercel doesn't include a database, set up an external one:

**Recommended: PlanetScale (Free)**
1. Go to [PlanetScale](https://planetscale.com)
2. Create account and new database
3. Get connection details
4. Note down: host, username, password, database name

### 2. **Generate Application Key**
```bash
cd rt-app
php artisan key:generate --show
```
Copy the generated key (starts with `base64:`)

### 3. **Deploy to Vercel**

#### Option A: Via Vercel Dashboard
1. Go to [Vercel Dashboard](https://vercel.com/dashboard)
2. Click "New Project"
3. Import from GitHub: `wondrv/simrt`
4. **Framework Preset**: Other
5. **Root Directory**: `rt-app` (important!)
6. **Build Command**: `npm run vercel-build`
7. **Output Directory**: `public`

#### Option B: Via Vercel CLI
```bash
# Install Vercel CLI
npm i -g vercel

# In your rt-app directory
cd rt-app
vercel

# Follow prompts:
# - Link to existing project? N
# - Project name: simrt
# - Directory: ./  (current directory)
# - Build command: npm run vercel-build
# - Output directory: public
```

### 4. **Environment Variables**
In Vercel dashboard, add these environment variables:

```bash
# Essential
APP_NAME=SIMRT
APP_ENV=production
APP_KEY=base64:your-generated-key-here
APP_DEBUG=false
APP_URL=https://your-app.vercel.app

# Database (from PlanetScale)
DB_CONNECTION=mysql
DB_HOST=your-planetscale-host
DB_PORT=3306
DB_DATABASE=your-database-name
DB_USERNAME=your-username
DB_PASSWORD=your-password

# Session & Cache
SESSION_DRIVER=cookie
CACHE_DRIVER=array
QUEUE_CONNECTION=sync

# Logging
LOG_CHANNEL=stderr
LOG_LEVEL=error

# Storage
FILESYSTEM_DISK=public
VIEW_COMPILED_PATH=/tmp
SSR_TEMP_PATH=/tmp
```

### 5. **Run Database Migration**
After successful deployment:

```bash
# Install Vercel CLI if not done
npm i -g vercel

# Pull environment variables
vercel env pull .env.production

# Run migrations
php artisan migrate --force --env=production
```

Or use Vercel's serverless function (create this file):
```php
// api/migrate.php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->call('migrate', ['--force' => true]);
echo "Migration completed!";
```

Then visit: `https://your-app.vercel.app/api/migrate.php`

### 6. **Test Your Deployment**
- Visit your Vercel URL
- Check if homepage loads
- Try logging in (if you have user data)
- Test admin features

## 🔧 Troubleshooting Common Issues

### 404 Errors
- Check `vercel.json` is in root directory
- Ensure `api/index.php` exists
- Verify routes in `vercel.json`

### Database Connection Issues
- Use SSL connection: Add `?sslmode=require` to connection string
- Check if database is accessible publicly
- Verify credentials are correct

### Build Failures
- Check PHP version compatibility
- Ensure all Composer dependencies are installed
- Verify Node.js build process

### File Upload Issues
Vercel has no persistent storage. For file uploads:
1. Use **Cloudinary** for images
2. Use **AWS S3** for files
3. Use **Vercel Blob** for simple files

## 📦 Alternative Deployment Options

If Vercel proves difficult, consider:
1. **Railway** - Full Laravel support, database included
2. **DigitalOcean App Platform** - Laravel-friendly
3. **Heroku** - Classic option with add-ons
4. **Traditional VPS** - Full control

## 🆘 Need Help?

If you encounter issues:
1. Check Vercel function logs
2. Review build logs
3. Test locally first: `php artisan serve`
4. Join our [GitHub Discussions](https://github.com/wondrv/simrt/discussions)
