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

  let draggedProject = null;
  const clearDragState = () => {
    projectList?.querySelectorAll('[data-project-editor]').forEach((project) => {
      project.classList.remove('is-dragging', 'is-drag-over');
    });
    draggedProject = null;
  };

  projectList?.addEventListener('dragstart', (event) => {
    const handle = event.target.closest('[data-drag-handle]');
    if (!handle) return;
    draggedProject = handle.closest('[data-project-editor]');
    if (!draggedProject) return;
    draggedProject.classList.add('is-dragging');
    if (event.dataTransfer) {
      event.dataTransfer.effectAllowed = 'move';
      event.dataTransfer.setData('text/plain', 'portfolio');
    }
  });

  projectList?.addEventListener('dragover', (event) => {
    const target = event.target.closest('[data-project-editor]');
    if (!draggedProject || !target || target === draggedProject) return;
    event.preventDefault();
    projectList.querySelectorAll('[data-project-editor]').forEach((project) => {
      project.classList.toggle('is-drag-over', project === target);
    });
    if (event.dataTransfer) event.dataTransfer.dropEffect = 'move';
  });

  projectList?.addEventListener('dragleave', (event) => {
    const target = event.target.closest('[data-project-editor]');
    if (!target || (event.relatedTarget && target.contains(event.relatedTarget))) return;
    target.classList.remove('is-drag-over');
  });

  projectList?.addEventListener('drop', (event) => {
    const target = event.target.closest('[data-project-editor]');
    if (!draggedProject || !target || target === draggedProject) return;
    event.preventDefault();
    const targetRect = target.getBoundingClientRect();
    const insertAfter = event.clientY > targetRect.top + targetRect.height / 2;
    if (insertAfter) {
      const next = target.nextElementSibling;
      if (next && next !== draggedProject) projectList.insertBefore(draggedProject, next);
      else if (!next) projectList.appendChild(draggedProject);
    } else {
      projectList.insertBefore(draggedProject, target);
    }
    renumberProjects();
    clearDragState();
  });

  projectList?.addEventListener('dragend', clearDragState);

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

