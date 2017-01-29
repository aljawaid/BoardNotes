Kanboard Plugin for notes
==========================

This plugin adds a GUI for notes.

Plugin for [Kanboard](https://github.com/fguillot/kanboard)

Author
------

- [TTJ](https://gitlab.com/u/ThomasTJ)
- License MIT

Installation
------------

- Decompress the archive in the `plugins` folder

or

- Create a folder **plugins/Boardnotes**
- Copy all files under this directory

or

- Clone folder with git

# AND #

- Add your api (settings -> api) in the post.php.config file
- Add your kanboard url in the post.php.config file
- Rename the post.php.config to post.php (mv post.php.config post.php)

Requirements
------------

All of the links are pointing to kanboard/ which means, that your Kanboard installations need to be here: 127.0.0.1/kanboard.

Use
---

Take notes on the fly. Developed to easily take project specific notes. The purpose of the notes is not to be tasks, but for keeping information - a flexible alternative to metadata.
I'm using Kanboard as projectmanagement tool for managing construction projects, where I often need to take notes regarding specific installations, during site-visits or phonemeetings.

The notes is accessible from the project dropdown, where only the project specific notes will be shown. On the dashboard there's a link in the sidebar to view all notes, the notes will be separated in tabs.

Features
--------

- Take notes quickly. Write the note title and press ENTER to save.
- Press TAB in the new note title to show the detailed menu
- Add detailed description to new notes
- Add a category to notes. The category is the same as the projects categories. (Please see the section for bugs)
- Get pie analytic on open and done notes
- Delete all done notes
- One-click for editing notes status (open/done)
- Edit note title. Click on title, edit and press ENTER
- Press the show more button on a note to see the note details
- Edit an existing notes description. Click on the description, type, press TAB to save
- Change category on existing notes. If you want to remove the category, just choose option 2 (the blank)
- Free sorting. Move the notes around. The sorting is saved.
- Export note to task. (Please see the secton for bugs)
- Generate report for printing notes.
- Filter report on category

Todo
----

- Implement fault procedures (verify it is number, etc.)
- Adding possibility to attach image from mobile
- Finish exporting notes to task in specific swimlane and with category
- Update styling for a more simplicity view
- Better overview of multiple projects with tabs

Issues
------------------------

- Focus on description textarea when pressing TAB on new notes title is not working
- Category is saved as text in database and does not have foreing key to the projects real category table
- Category not updating in title after manually changing the category
- Analytic chart on categories not developed
- Margin bottom not added
- The only folder in the `Template` folder is `boardnotes`, and not specified out on `dashboard` etc.
- There is no description of shortcuts (ENTER and TAB key)
- Delete directly on trash button on single note - to fast?
- If note has empty title, it's not possible to change it afterwards
- Analytic is breaking when viewing all projects (js not reloading correctly)
- Exporting note to task: Swimlanes not working. Category not working.
- Div modal for "Delete all done" and "Analytic" is repeated on every reload
- Should disabled projects show on all boardnotes page?
- Functions in controller (BoardnotesController) missing variables in () - needed?
- Markups as Kanboard

Tested on
---------

- Application version: 1.0.30 / 1.0.32
- PHP version: 5.5.9-1ubuntu4.17
- PHP SAPI: apache2handler
- OS version: Linux 3.13.0-74-generic
- Database driver: sqlite
- Database version: 3.8.2
- Browser: Chromium 51.0.2704.106 (64-bit)
