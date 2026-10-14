/*
 * BoardNotes workspace controller for secure forms, ordering and disclosure controls.
 * Related test: tests/OutputSecurityContractTest.php.
 */
(function () {
  'use strict';

  function showMessage(root, message) {
    var output = root.querySelector('[data-boardnotes-message]');
    if (!output) {
      return;
    }

    output.textContent = message;
    output.hidden = message === '';
    if (message !== '') {
      output.focus();
    }
  }

  async function submitForm(root, form) {
    if (form.dataset.confirm && !window.confirm(form.dataset.confirm)) {
      return;
    }

    var button = form.querySelector('[type="submit"]');
    if (button) {
      button.disabled = true;
    }

    showMessage(root, '');

    try {
      var response = await window.fetch(form.action, {
        method: 'POST',
        body: new window.URLSearchParams(new window.FormData(form)),
        credentials: 'same-origin',
        headers: {'Accept': 'application/json'}
      });
      var payload = await response.json();

      if (!response.ok || !payload.success) {
        throw new Error(payload.message || root.dataset.requestError);
      }

      if (payload.task_url) {
        window.location.assign(payload.task_url);
        return;
      }

      window.location.reload();
    } catch (error) {
      showMessage(root, error.message || root.dataset.requestError);
      if (button) {
        button.disabled = false;
      }
    }
  }

  async function persistOrder(root) {
    var list = root.querySelector('[data-boardnotes-list]');
    if (!list || !root.dataset.reorderUrl) {
      return;
    }

    var noteIds = Array.from(list.querySelectorAll('[data-note-id]')).map(function (item) {
      return item.dataset.noteId;
    });
    var body = new window.URLSearchParams({
      csrf_token: root.dataset.csrfToken,
      order: noteIds.join(','),
      nrNotes: String(noteIds.length)
    });

    try {
      var response = await window.fetch(root.dataset.reorderUrl, {
        method: 'POST',
        body: body,
        credentials: 'same-origin',
        headers: {'Accept': 'application/json'}
      });
      var payload = await response.json();
      if (!response.ok || !payload.success) {
        throw new Error(root.dataset.orderError);
      }
    } catch (error) {
      showMessage(root, error.message || root.dataset.orderError);
    }
  }

  function moveItem(root, item, direction) {
    var sibling = direction < 0 ? item.previousElementSibling : item.nextElementSibling;
    if (!sibling) {
      return;
    }

    if (direction < 0) {
      item.parentNode.insertBefore(item, sibling);
    } else {
      item.parentNode.insertBefore(sibling, item);
    }
    persistOrder(root);
  }

  function initialiseRoot(root) {
    if (root.dataset.boardnotesReady === 'true') {
      return;
    }
    root.dataset.boardnotesReady = 'true';

    root.addEventListener('submit', function (event) {
      var form = event.target.closest('[data-boardnotes-form]');
      if (form) {
        event.preventDefault();
        submitForm(root, form);
      }
    });

    root.addEventListener('click', function (event) {
      var item = event.target.closest('[data-note-id]');
      if (event.target.closest('[data-boardnotes-up]') && item) {
        moveItem(root, item, -1);
      } else if (event.target.closest('[data-boardnotes-down]') && item) {
        moveItem(root, item, 1);
      } else if (event.target.closest('[data-boardnotes-expand]')) {
        root.querySelectorAll('details').forEach(function (details) { details.open = true; });
      } else if (event.target.closest('[data-boardnotes-collapse]')) {
        root.querySelectorAll('details').forEach(function (details) { details.open = false; });
      }
    });

    var draggedItem = null;
    root.addEventListener('dragstart', function (event) {
      draggedItem = event.target.closest('[data-note-id]');
      if (draggedItem) {
        event.dataTransfer.effectAllowed = 'move';
      }
    });
    root.addEventListener('dragover', function (event) {
      var target = event.target.closest('[data-note-id]');
      if (draggedItem && target && target !== draggedItem) {
        event.preventDefault();
        target.parentNode.insertBefore(draggedItem, target);
      }
    });
    root.addEventListener('dragend', function () {
      if (draggedItem) {
        persistOrder(root);
      }
      draggedItem = null;
    });
  }

  function initialiseAll() {
    document.querySelectorAll('[data-boardnotes-root]').forEach(initialiseRoot);
    document.querySelectorAll('[data-boardnotes-print]').forEach(function (button) {
      button.addEventListener('click', function () { window.print(); });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initialiseAll);
  } else {
    initialiseAll();
  }
}());
