import grapesjs from 'grapesjs';

function initTemplateBuilder() {
    const container = document.getElementById('gjs-app');

    if (!container || container.dataset.initialized) {
        return;
    }

    container.dataset.initialized = '1';

    const blocks = JSON.parse(container.dataset.blocks || '[]');
    const initialHtml = container.dataset.html || '';
    const initialCss = container.dataset.css || '';
    const wireId = container.dataset.wireId;

    let initialComponents;
    try {
        const data = JSON.parse(container.dataset.grapesjsData || '{}');
        initialComponents = data?.pages ? data : undefined;
    } catch (e) {
        initialComponents = undefined;
    }

    const editor = grapesjs.init({
        container: '#gjs',
        height: '100%',
        fromElement: false,
        storageManager: false,
        components: initialHtml || '<div class="template-canvas"></div>',
        style: initialCss || '',
        blockManager: {
            appendTo: '#gjs-blocks',
        },
        layerManager: {
            appendTo: '#gjs-layers',
        },
        selectorManager: {
            appendTo: '#gjs-selectors',
        },
        styleManager: {
            appendTo: '#gjs-styles',
        },
        panels: { defaults: [] },
    });

    if (initialComponents) {
        editor.loadProjectData(initialComponents);
    }

    blocks.forEach((block) => {
        editor.BlockManager.add(block.id, {
            label: block.label,
            content: block.content,
            category: 'Blocs',
        });
    });

    document.getElementById('gjs-save')?.addEventListener('click', () => {
        const html = editor.getHtml();
        const css = editor.getCss();
        const projectData = editor.getProjectData();

        if (window.Livewire && wireId) {
            window.Livewire.find(wireId).call('save', html, css, projectData);
        }
    });

    document.querySelectorAll('.gjs-device-btn').forEach((btn) => {
        btn.addEventListener('click', () => editor.setDevice(btn.dataset.device));
    });
}

document.addEventListener('DOMContentLoaded', initTemplateBuilder);
document.addEventListener('livewire:navigated', initTemplateBuilder);
