# Laravel Shop

A modern e-commerce application built with Laravel, featuring a clean and intuitive user interface, secure payment processing with Stripe, and comprehensive order management.

## Features

- 🛍️ Product catalog with categories
- 🛒 Shopping cart functionality
- 💳 Secure payment processing with Stripe
- 📦 Order management system
- 👤 User authentication and profiles
- 📱 Responsive design
- 🔍 Product search and filtering
- 📊 Admin dashboard

## Requirements

- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL/PostgreSQL/SQLite
- Stripe account for payment processing

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/laravel-shop.git
cd laravel-shop
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install NPM dependencies:
```bash
npm install
```

4. Create environment file:
```bash
cp .env.example .env
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Configure your database in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_shop
DB_USERNAME=root
DB_PASSWORD=
```

7. Configure Stripe in `.env`:
```env
STRIPE_KEY=your_stripe_publishable_key
STRIPE_SECRET=your_stripe_secret_key
```

8. Run migrations and seed the database:
```bash
php artisan migrate --seed
```

9. Start the development server:
```bash
php artisan serve
```

10. In a separate terminal, start Vite:
```bash
npm run dev
```

## Testing

Run the test suite:
```bash
php artisan test
```

## Payment Testing

For testing payments, use these Stripe test card numbers:

- Success: 4242 4242 4242 4242
- Decline: 4000 0000 0000 0002

Use any future expiration date, any 3-digit CVC, and any postal code.

## Project Structure

```
laravel-shop/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Middleware/
│   ├── Models/
│   └── Services/
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── js/
│   └── views/
├── routes/
│   ├── web.php
│   └── api.php
└── tests/
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## Security

If you discover any security-related issues, please email security@yourdomain.com instead of using the issue tracker.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Acknowledgments

- [Laravel](https://laravel.com)
- [Stripe](https://stripe.com)
- [Tailwind CSS](https://tailwindcss.com)
- [Alpine.js](https://alpinejs.dev)
