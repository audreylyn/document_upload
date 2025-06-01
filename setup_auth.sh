#!/bin/bash

# Commands to run the migrations and seed the database
echo "Running migrations..."
php artisan migrate

echo "Seeding the database..."
php artisan db:seed

echo "Authentication system setup complete!"
echo "Admin login: admin@example.com / password"
echo "Applicant login: applicant@example.com / password"
