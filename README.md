````markdown name=README.md
# Simple User Manager (WordPress plugin)

A small, class-based admin plugin that adds a management screen to list users, create users (AJAX), change roles (AJAX), and delete users (AJAX) with pagination. Demo / learning plugin — review and harden before using in production.

Installation:
1. Create a folder named `simple-user-manager` in `wp-content/plugins/`.
2. Save the files from this package into that folder (keep directories: includes, assets).
3. Activate the plugin from the WordPress admin (Plugins screen).
4. Go to Users → Simple User Manager.

Features:
- List users with pagination.
- Create new users via AJAX (username, email, password, role).
- Change a user's role via AJAX.
- Delete users via AJAX.
- Action capability checks:
  - Viewing the page: list_users
  - Creating users: create_users
  - Changing roles: promote_users OR edit_users
  - Deleting users: delete_users

Security & Notes:
- Uses WordPress nonces for AJAX actions and capability checks for each action.
- Basic sanitization and error handling included.
- This is a simple demo: add stronger validation (password strength), rate limiting, server-side logging, and more granular capability checks before using on a live site.
- For large sites: consider adding more efficient pagination and caching.

If you'd like, I can:
- Push this to a GitHub repository for you (create new repo, push files, open main branch).
- Add richer UI (modals, inline editing without page reload).
- Add logging/audit trails and role-based UI constraints for non-super-admins.
````
