#!/usr/bin/env sh

# Install WordPress.
wp core install \
  --title="Project" \
  --admin_user="wordpress" \
  --admin_password="wordpress" \
  --admin_email="admin@example.com" \
  --url="http://localhost" \
  --skip-email

# Activate plugin.
wp plugin activate wp-stats-manager
wp user create user contact@example.com --user_pass=user --role=subscriber