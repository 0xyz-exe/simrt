# 🏠 SIMRT - Sistem Informasi Manajemen RT

<div align="center">
  
  [![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
  [![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
  [![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
  [![License](https://img.shields.io/badge/License-MIT-green.svg?style=for-the-badge)](LICENSE)
  
  **Modern Digital Management System for Indonesian Neighborhood Communities (RT)**
  
  [Demo](https://demo-simrt.your-domain.com) · [Documentation](https://github.com/your-username/simrt/wiki) · [Report Bug](https://github.com/your-username/simrt/issues) · [Request Feature](https://github.com/your-username/simrt/issues)
  
</div>

## 🌟 Overview

**SIMRT (Sistem Informasi Manajemen RT)** is a comprehensive web-based management system designed specifically for Indonesian neighborhood communities (Rukun Tetangga). This modern platform digitalizes traditional RT administration processes, providing transparency, efficiency, and community engagement tools.

### 🎯 Why SIMRT?

- **🏘️ Community-Focused**: Built specifically for Indonesian RT/RW management needs
- **📱 Modern UX**: Responsive design optimized for mobile and desktop
- **🔒 Secure**: Advanced security features with multi-layer protection
- **📊 Data-Driven**: Real-time analytics and comprehensive reporting
- **🚀 Production-Ready**: Battle-tested with proper deployment scripts

## ✨ Features

### 🏘️ **Community Management**
- **📊 Real-time Dashboard** with live statistics and analytics
- **💰 Financial Transparency** with automated dues tracking and payment records
- **📢 Digital Announcements** for community notifications and updates
- **👥 Resident Database** with comprehensive household and individual data
- **📋 Document Management** with digital letter approval system

### 🏪 **UMKM (Small Business) Support**
- **🛍️ Business Directory** showcasing local enterprises
- **📝 Business Profiles** with detailed information and contact integration
- **⭐ Featured Listings** to promote community businesses
- **📱 WhatsApp Integration** for direct customer communication
- **📸 Photo Gallery** for business showcasing

### 📊 **Analytics & Reporting**
- **📈 Demographics Analysis** (age groups, occupations, education levels)
- **💹 Financial Reports** with collection rates and payment trends
- **📉 Interactive Charts** powered by Chart.js
- **📊 Real-time Statistics** with auto-updating data
- **📄 Export Capabilities** for administrative purposes

### 🗺️ **Geographic Features**
- **🗺️ Interactive Maps** with Leaflet.js integration
- **📍 Landmark Mapping** (mosques, schools, health centers)
- **🚌 Transportation Info** and accessibility details
- **📍 Boundary Visualization** of RT coverage areas

### 🔐 **Security & Authentication**
- **🔑 Multi-factor Authentication** (Email + Google OAuth)
- **👤 Role-based Access Control** (Admin, RT Officials, Residents)
- **🛡️ Advanced Security Middleware** with XSS/SQL injection protection
- **🔒 Session Management** with secure token handling
- **⚡ DDoS Protection** and rate limiting

### 📱 **User Experience**
- **📱 Mobile-First Design** optimized for all devices
- **🌙 Dark/Light Theme** support
- **⚡ Progressive Web App** capabilities
- **🎨 Modern UI/UX** with Tailwind CSS
- **♿ Accessibility Features** following WCAG guidelines
## �️ Tech Stack

<div align="center">

| Category | Technologies |
|----------|-------------|
| **Backend** | ![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?logo=laravel&logoColor=white) ![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php&logoColor=white) |
| **Database** | ![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?logo=mysql&logoColor=white) ![SQLite](https://img.shields.io/badge/SQLite-Development-003B57?logo=sqlite&logoColor=white) |
| **Frontend** | ![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?logo=tailwind-css&logoColor=white) ![Alpine.js](https://img.shields.io/badge/Alpine.js-8BC0D0?logo=alpine.js&logoColor=black) |
| **Maps & Charts** | ![Leaflet](https://img.shields.io/badge/Leaflet.js-199900?logo=leaflet&logoColor=white) ![Chart.js](https://img.shields.io/badge/Chart.js-FF6384?logo=chart.js&logoColor=white) |
| **Authentication** | ![Google OAuth](https://img.shields.io/badge/Google_OAuth-4285F4?logo=google&logoColor=white) ![Laravel Socialite](https://img.shields.io/badge/Laravel_Socialite-FF2D20?logo=laravel&logoColor=white) |
| **Development** | ![Vite](https://img.shields.io/badge/Vite-646CFF?logo=vite&logoColor=white) ![Composer](https://img.shields.io/badge/Composer-885630?logo=composer&logoColor=white) ![NPM](https://img.shields.io/badge/NPM-CB3837?logo=npm&logoColor=white) |

</div>

### Core Dependencies
```json
{
  "laravel/framework": "^11.0",
  "laravel/socialite": "^5.0",
  "tailwindcss": "^3.4",
  "alpinejs": "^3.0",
  "chart.js": "^4.0",
  "leaflet": "^1.9"
}
```

## � Quick Start

### System Requirements
- **PHP** 8.2 or higher
- **Composer** 2.0+
- **Node.js** 18.0+ & NPM
- **MySQL** 8.0+ or **SQLite** 3.x
- **Git** for version control

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/simrt.git
   cd simrt
   ```

2. **Install dependencies**
   ```bash
   # Backend dependencies
   composer install
   
   # Frontend dependencies
   npm install
   ```

3. **Environment setup**
   ```bash
   # Copy environment file
   cp .env.example .env
   
   # Generate application key
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   # Run migrations
   php artisan migrate
   
   # Seed with sample data (optional)
   php artisan db:seed
   ```

5. **Build frontend assets**
   ```bash
   # Development build
   npm run dev
   
   # Production build
   npm run build
   ```

6. **Start development server**
   ```bash
   php artisan serve
   ```

   Visit `http://localhost:8000` to access the application.

### Default Credentials
```
Admin Account:
Email: admin@rt.com
Password: password

Demo Account:
Email: demo@rt.com  
Password: password
```

## ⚙️ Configuration

### Database Configuration
Edit your `.env` file with your database credentials:

```env
# For MySQL (Production)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simrt_production
DB_USERNAME=your_username
DB_PASSWORD=your_secure_password

# For SQLite (Development)
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

### Google OAuth Setup
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing one
3. Enable Google+ API
4. Create OAuth 2.0 credentials
5. Add authorized redirect URIs
6. Update your `.env` file:

```env
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

### Mail Configuration
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="RT Management System"
```

### Application Settings
```env
APP_NAME="SIMRT"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
APP_TIMEZONE=Asia/Jakarta
```

## 🌐 Deployment

### Production Deployment

#### 1. Server Requirements
- **OS**: Ubuntu 20.04+ or CentOS 8+
- **Web Server**: Nginx or Apache 2.4+
- **PHP**: 8.2+ with required extensions
- **Database**: MySQL 8.0+ or PostgreSQL 13+
- **SSL**: Let's Encrypt or commercial certificate

#### 2. Deployment Steps
```bash
# 1. Clone and setup
git clone https://github.com/your-username/simrt.git
cd simrt

# 2. Install dependencies
composer install --optimize-autoloader --no-dev
npm install && npm run build

# 3. Configure environment
cp .env.example .env
php artisan key:generate

# 4. Setup database
php artisan migrate --force
php artisan db:seed --force

# 5. Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link

# 6. Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 755 storage bootstrap/cache
```

#### 3. Recommended Hosting Platforms
- **🌊 DigitalOcean App Platform** - Recommended for ease of use
- **☁️ AWS EC2** - For scalability and advanced features  
- **🚀 Heroku** - Quick deployment with add-ons
- **🔧 VPS** - Full control with manual setup

#### 4. Environment-Specific Configurations

**Production `.env` example:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

LOG_CHANNEL=daily
LOG_LEVEL=error

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Docker Deployment
A complete Docker setup is available in the `docker/` directory:

```bash
# Development
docker-compose -f docker-compose.dev.yml up -d

# Production  
docker-compose -f docker-compose.prod.yml up -d
```

### Automated Deployment
Use the included deployment scripts for one-click deployment:

```bash
# Run deployment script
./deployment/deploy.sh production

# Setup monitoring
./deployment/monitoring-setup.sh

# SSL certificate setup
./deployment/ssl-setup.sh your-domain.com
```

## 🤝 Contributing

We welcome contributions from the community! Here's how you can help improve SIMRT:

### Getting Started
1. **Fork** the repository
2. **Clone** your fork: `git clone https://github.com/your-username/simrt.git`
3. **Create** a feature branch: `git checkout -b feature/amazing-feature`
4. **Make** your changes
5. **Test** your changes thoroughly
6. **Commit** your changes: `git commit -m 'Add amazing feature'`
7. **Push** to the branch: `git push origin feature/amazing-feature`
8. **Open** a Pull Request

### Development Guidelines
- ✅ Follow **PSR-12** coding standards
- ✅ Write **comprehensive tests** for new features
- ✅ Update **documentation** for any changes
- ✅ Use **conventional commits** for clear history
- ✅ Ensure **mobile responsiveness** for UI changes
- ✅ Test with **multiple PHP versions** (8.2, 8.3)

### Code Style
```bash
# Run code style checks
composer run-script cs-check

# Auto-fix code style issues  
composer run-script cs-fix

# Run tests
php artisan test

# Run static analysis
composer run-script analyse
```

### Types of Contributions
- 🐛 **Bug Fixes** - Help us squash bugs
- ✨ **New Features** - Propose and implement new functionality
- 📚 **Documentation** - Improve docs and examples
- 🎨 **UI/UX** - Enhance user interface and experience
- 🌍 **Translations** - Add support for more languages
- 🧪 **Testing** - Improve test coverage

### Reporting Issues
Found a bug? Please create an issue with:
- Clear description of the problem
- Steps to reproduce
- Expected vs actual behavior
- Environment details (PHP version, OS, etc.)
- Screenshots if applicable

## � Screenshots

<details>
<summary>🖼️ View Application Screenshots</summary>

### Dashboard Overview
![Dashboard](docs/screenshots/dashboard.png)
*Real-time analytics and community overview*

### UMKM Management
![UMKM Directory](docs/screenshots/umkm-directory.png)
*Business directory with integrated contact features*

### Financial Transparency
![Financial Reports](docs/screenshots/financial-transparency.png)
*Transparent dues tracking and payment history*

### Mobile Experience
![Mobile Interface](docs/screenshots/mobile-responsive.png)
*Fully responsive mobile-first design*

</details>

## 🗺️ Roadmap

### Version 2.0 (Q1 2026)
- [ ] **🌐 Multi-language Support** (English, Bahasa Indonesia)
- [ ] **📊 Advanced Analytics** with predictive insights
- [ ] **💬 Real-time Chat** for community communication
- [ ] **📱 Mobile App** (React Native/Flutter)
- [ ] **🔗 API Integrations** with government systems

### Version 2.1 (Q2 2026)
- [ ] **🤖 AI-powered Insights** for demographic analysis
- [ ] **📧 Email Newsletter** automation
- [ ] **💳 Online Payment** gateway integration
- [ ] **📋 Digital Voting** system for RT decisions
- [ ] **🎯 Event Management** with RSVP tracking

### Community Requests
- [ ] **📊 Export to PDF/Excel** for all reports
- [ ] **🔔 Push Notifications** for mobile devices
- [ ] **🗓️ Calendar Integration** for community events
- [ ] **📱 WhatsApp Bot** integration
- [ ] **🌙 Enhanced Dark Mode** with custom themes

## 📄 License

This project is licensed under the **MIT License** - see the [LICENSE](LICENSE) file for details.

### MIT License Summary
- ✅ **Commercial Use** - Use in commercial projects
- ✅ **Modification** - Modify and distribute
- ✅ **Distribution** - Share with others
- ✅ **Private Use** - Use privately
- ❌ **Liability** - No warranty provided
- ❌ **Warranty** - Use at your own risk

## 📞 Support & Community

### 📚 Documentation
- **📖 [Full Documentation](https://github.com/your-username/simrt/wiki)** - Comprehensive guides
- **🚀 [Deployment Guide](deployment/README.md)** - Production setup
- **🔧 [API Documentation](docs/api.md)** - Developer reference
- **❓ [FAQ](docs/faq.md)** - Common questions

### 💬 Community & Support
- **🐛 [GitHub Issues](https://github.com/your-username/simrt/issues)** - Bug reports & feature requests
- **💭 [GitHub Discussions](https://github.com/your-username/simrt/discussions)** - Community discussions
- **📧 [Email Support](mailto:support@simrt.dev)** - Direct support
- **💬 [Telegram Group](https://t.me/simrt_community)** - Community chat

### 🆘 Need Help?
1. **Check the [FAQ](docs/faq.md)** for common issues
2. **Search [existing issues](https://github.com/your-username/simrt/issues)** for similar problems
3. **Create a [new issue](https://github.com/your-username/simrt/issues/new)** with detailed information
4. **Join our [community discussions](https://github.com/your-username/simrt/discussions)** for general questions

### 🔄 Updates & News
- **⭐ Star this repository** to get notified of updates
- **👀 Watch releases** for new versions
- **🐦 Follow us on Twitter** [@SimrtDev](https://twitter.com/SimrtDev)

---

<div align="center">

**Made with ❤️ for Indonesian Communities**

*Bringing traditional neighborhood management into the digital age*

[![GitHub stars](https://img.shields.io/github/stars/your-username/simrt?style=social)](https://github.com/your-username/simrt/stargazers)
[![GitHub forks](https://img.shields.io/github/forks/your-username/simrt?style=social)](https://github.com/your-username/simrt/network/members)
[![GitHub watchers](https://img.shields.io/github/watchers/your-username/simrt?style=social)](https://github.com/your-username/simrt/watchers)

</div>
