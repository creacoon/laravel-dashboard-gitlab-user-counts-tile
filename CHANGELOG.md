# Changelog

All notable changes to ` creacoon/laravel-dashboard-gitlab-user-counts-tile ` will be documented in this file

## [Unreleased]

### Added
- When `specific_users` is empty, show all active users that have to-dos, assigned merge requests or review requested merge requests

### Changed
- Require `spatie/laravel-dashboard` ^4.0 (Livewire 4, Tailwind CSS 4)
- Replace `flex-grow` with `grow` for Tailwind CSS 4
- Only show active users; blocked or deactivated users are hidden, even when listed in `specific_users`
- Ignore empty entries in `specific_users`

## 1.0.0 - 202X-XX-XX

- initial release
