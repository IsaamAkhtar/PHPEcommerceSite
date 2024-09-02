# PHP E-Commerce Website

## Screenshots

![Homepage](img/screenshots/homepage.png)

## Overview

This project is a basic e-commerce website developed using PHP and MySQL. It provides a platform where users can browse and purchase products online. The site includes features like product categories, a shopping cart, and a simple admin panel for managing products and categories.

## Features

- **Product Listings**: Users can view products categorized by type (e.g., iPhone, iPad).
- **Product Details**: Each product has a detailed page with a description and an option to add it to the cart.
- **Shopping Cart**: Users can add products to their cart and proceed to checkout.
- **Admin Panel**: Admins can manage products and categories through a dedicated interface.

## How It Works

1. **Database Setup**:
   - The project uses a MySQL database to store product and category information. The database includes tables for products, categories, orders, and more.

2. **Product Display**:
   - Products are displayed on the store page, with images, names, and prices pulled from the database.

3. **Category Filtering**:
   - Users can filter products by category using links on the homepage.

4. **Admin Functions**:
   - The admin panel allows for adding, updating, and deleting products and categories.

## Getting Started

1. **Clone the Repository**:
   ```sh
   git clone https://github.com/yourusername/your-repository.git

**Setup the Database**:
- Import the provided SQL files to set up the database structure and initial data.

**Configuration**:
- Update the `config.php` file with your database credentials.

**Run the Project**:
- Start a local server (e.g., using XAMPP, MAMP, or the built-in PHP server).
- Access the project via your web browser at `http://localhost:8000`.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.