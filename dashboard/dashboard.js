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
      if (selector === 'project') renumberProjects();
    });
  };

  addEditor('partner-list', 'partner-template', 'partner');
  addEditor('project-list', 'project-template', 'project');

  const projectList = document.getElementById('project-list');
  const renumberProjects = () => {
    if (!projectList) return;
    const projects = [...projectList.querySelectorAll('[data-project-editor]')];
    projects.forEach((project, index) => {
      const number = project.querySelector('.editor-number');
      if (number) number.textContent = String(index + 1).padStart(2, '0');
      project.querySelectorAll('[name]').forEach((input) => {
        input.name = input.name.replace(/(projects|project_image)\[[^\]]+\]/, `$1[${index}]`);
      });
      const moveUp = project.querySelector('[data-move-up]');
      const moveDown = project.querySelector('[data-move-down]');
      if (moveUp) moveUp.disabled = index === 0;
      if (moveDown) moveDown.disabled = index === projects.length - 1;
    });
  };
  renumberProjects();

  document.addEventListener('click', (event) => {
    const moveUp = event.target.closest('[data-move-up]');
    const moveDown = event.target.closest('[data-move-down]');
    if (projectList && (moveUp || moveDown)) {
      const project = (moveUp || moveDown).closest('[data-project-editor]');
      if (!project) return;
      if (moveUp && project.previousElementSibling) {
        projectList.insertBefore(project, project.previousElementSibling);
      } else if (moveDown && project.nextElementSibling) {
        projectList.insertBefore(project.nextElementSibling, project);
      }
      renumberProjects();
      return;
    }

    const removeButton = event.target.closest('[data-remove-editor]');
    if (removeButton) {
      removeButton.closest('[data-partner-editor], [data-project-editor]')?.remove();
      renumberProjects();
    }
  });
})();

