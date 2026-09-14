(() => {
  const sidebar = document.querySelector('.dashboard-sidebar');
  const toggle = document.querySelector('.sidebar-toggle');
  if (sidebar && toggle) {
    toggle.addEventListener('click', () => {
      const open = sidebar.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', String(open));
    });
  }

  const addEditor = (listId, templateId, selector) => {
    const list = document.getElementById(listId);
    const template = document.getElementById(templateId);
    if (!list || !template) return;
    document.querySelector(`[data-add-${selector}]`)?.addEventListener('click', () => {
      const index = Date.now();
      const fragment = template.content.cloneNode(true);
      fragment.querySelectorAll('[name]').forEach((input) => {
        input.name = input.name.replaceAll('__INDEX__', String(index));
      });
      list.appendChild(fragment);
      list.lastElementChild?.querySelector('input')?.focus();
    });
  };

  addEditor('partner-list', 'partner-template', 'partner');
  addEditor('project-list', 'project-template', 'project');

  document.addEventListener('click', (event) => {
    const removeButton = event.target.closest('[data-remove-editor]');
    if (removeButton) {
      removeButton.closest('[data-partner-editor], [data-project-editor]')?.remove();
    }
  });
})();

