Version 0.1.4
-------------

* Keep full-width note controls inside narrow screens by applying scoped border-box sizing.

Version 0.1.3
-------------

* Fix task-conversion redirects so the created task opens without HTML-escaped query separators.

Version 0.1.2
-------------

* Make default custom projects and indexes idempotent so interrupted database migrations can safely retry.
* Replace privileged database triggers with portable orphan-note cleanup after user or project deletion.

Version 0.1.1
-------------

* Standardise release documentation and package validation for current Kanboard installations.

Version 0.1.0
-------------

* Enforce authenticated note ownership, project access and CSRF protection.
* Replace legacy jQuery fragments, inline code and fixed Kanboard paths with generated routes and native browser controls.
* Preserve notes until optional task conversion has completed successfully.
* Add empty-safe analytics, escaped reports, responsive theme-aware styling and English translation catalogues.
* Add schema migration 2 for indexes, portable seed data and user/project lifecycle cleanup.
* Add PHP 8.1-8.5 contract tests and update Super-Linter to immutable v9.0.0.
* Complete and close the structure and naming work tracked in issue #2.

Version 0.0.5
-------------

* Filtering options for report generation

Version 0.0.4
-------------

* Create report over task (todo: add filter)
* JS encodeURIComponent() added to title and description on export note
* Minor testing with changes in style.css


Version 0.0.3
-------------

* Export notes to task (categories is not exported)


Version 0.0.2
-------------

* Un-doing note (note done, click again, undone) - wrong variable in sql call
* Font size made smaller on desktop (13px) and mobile (11px)


Version 0.0.1
-------------

* Initial release
