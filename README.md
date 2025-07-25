# TastySwift

<p align="center">
  <img src="public/images/logo.png" alt="TastySwift Logo" width="200">
  <h2 align="center">Delicious Food Delivery at Your Fingertips</h2>
  <p align="center">
    <a href="#features">Features</a> •
    <a href="#requirements">Requirements</a> •
    <a href="#installation">Installation</a> •
    <a href="#configuration">Configuration</a> •
    <a href="#usage">Usage</a> •
    <a href="#api-documentation">API</a> •
    <a href="#contributing">Contributing</a> •
    <a href="#license">License</a>
  </p>
  
  [![Laravel Version](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=flat&logo=laravel)](https://laravel.com/)
  [![PHP Version](https://img.shields.io/badge/PHP-8.1+-777BB4?style=flat&logo=php&logoColor=white)](https://www.php.net/)
  [![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
  [![Build Status](https://github.com/AronnoDIU/TastySwift/actions/workflows/tests.yml/badge.svg)](https://github.com/AronnoDIU/TastySwift/actions)
</p>

## 🚀 About TastySwift

TastySwift is a modern food delivery platform built with Laravel, designed to connect hungry customers with their favorite restaurants. Our mission is to provide a seamless food ordering experience with a focus on speed, reliability, and user satisfaction.

## ✨ Features

- 🍽️ Browse restaurants and menus
- ⚡ Fast and secure checkout
- 📱 Responsive design for all devices
- 🔍 Advanced search and filtering
- 📊 Real-time order tracking
- 💬 Live chat support
- ⭐ Ratings and reviews
- 🎯 Personalized recommendations
- 📱 PWA Support
- 🔄 Real-time updates with Laravel Echo

## 🛠 Requirements

- PHP 8.1 or higher
- Composer
- Node.js & NPM
- MySQL 5.7+
- Web Server (Apache/Nginx)
- Redis (for caching and queues)

## 🚀 Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/AronnoDIU/TastySwift.git
   cd TastySwift
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**
   ```bash
   npm install
   ```

4. **Create environment file**
   ```bash
   cp .env.example .env
   ```

5. **Generate application key**
   ```bash
   php artisan key:generate
   ```

6. **Configure database**
   Update your `.env` file with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=tastyswift
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

7. **Run migrations and seeders**
   ```bash
   php artisan migrate --seed
   ```

8. **Compile assets**
   ```bash
   npm run build
   ```

9. **Start the development server**
   ```bash
   php artisan serve
   ```

10. **Access the application**
    Open your browser and visit: [http://localhost:8000](http://localhost:8000)

## 🔧 Configuration

### Environment Variables

Key environment variables to configure:

- `APP_ENV`: Application environment (local, production, etc.)
- `APP_DEBUG`: Enable/disable debug mode
- `APP_URL`: Application URL
- `DB_*`: Database configuration
- `MAIL_*`: Email configuration
- `STRIPE_*`: Stripe payment configuration
- `GOOGLE_MAPS_API_KEY`: For location services

### Storage

Ensure the storage directory is writable:
```bash
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

## 🚦 Usage

### Development

To start the development server:
```bash
php artisan serve
npm run dev
```

### Production

For production deployment:
1. Set `APP_ENV=production`
2. Set `APP_DEBUG=false`
3. Run `php artisan config:cache`
4. Run `php artisan route:cache`
5. Run `php artisan view:cache`

### Testing

Run the test suite:
```bash
php artisan test
```

## 📚 API Documentation

API documentation is available at `/api/documentation` after setting up the application.

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- [Laravel](https://laravel.com/)
- [Vue.js](https://vuejs.org/)
- [Bootstrap](https://getbootstrap.com/)
- [Font Awesome](https://fontawesome.com/)

---

<p align="center">
  Made with ❤️ by <a href="https://github.com/AronnoDIU" target="_blank">Yeasir Arafat Aronno</a>
</p>"
