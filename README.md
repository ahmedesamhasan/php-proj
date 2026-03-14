# PHP Online Shop - Admin Dashboard Version

This version keeps the project simple, but adds the next useful features that make it feel closer to a real admin panel.

## Added in this round

- Dashboard page with quick stats
- Change password flow for the admin account
- Edit and delete actions for categories
- Safe category delete rule, only empty categories can be removed
- Image resize and compression during upload when GD is available
- Lighter logic split into small service classes

## Service classes

- `services/AuthService.php`
- `services/CategoryService.php`
- `services/DashboardService.php`
- `services/ImageService.php`
- `services/ProductService.php`

## Pages

- `dashboard.php` -> overview page
- `change_password.php` -> password form
- `update_password.php` -> password action
- `edit_category.php` -> edit category form
- `update_category.php` -> update category action
- `delete_category.php` -> delete category action

## Default admin account

- Email: `admin@example.com`
- Password: `admin123`

## Setup

1. Import `schema.sql` into MySQL.
2. Update your database credentials in `config.php` if needed.
3. Open `login.php`.
4. After login, start from `dashboard.php`.

## Notes

- Uploaded images are resized to a more practical size when PHP GD is enabled.
- Image files are saved as `.jpg` after processing.
- Empty categories can be deleted, but categories with products are protected.
- The code is still intentionally lightweight, not a full MVC framework.
# php-proj
# php-proj
