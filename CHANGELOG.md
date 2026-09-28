# Changelog

All notable changes to ` creacoon/laravel-dashboard-gitlab-user-counts-tile ` will be documented in this file

## [Unreleased]

## [2.1.0] 2026-09-28

### Changed
- Modernised the tile: a card per user with equal font sizes, right-aligned counts and dimmed zeros
- Removed the duplicate `wire:poll`; the tile wrapper already polls

## [2.0.2] 2026-09-23

### Added
- When `specific_users` is empty, show all active users that have to-dos, assigned merge requests or review requested merge requests

### Changed
- Only show active users; blocked or deactivated users are hidden, even when listed in `specific_users`
- Ignore empty entries in `specific_users`

## [2.0.0] 2026-09-23

### Changed
- Require `spatie/laravel-dashboard` ^4.0 (Livewire 4, Tailwind CSS 4)
- Replace `flex-grow` with `grow` for Tailwind CSS 4

## 1.0.0 - 202X-XX-XX

- initial release
