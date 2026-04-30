<div 
    x-data="imageGalleryInit()" 
    x-init="init()"
    wire:ignore.self
>
</div>

<template x-teleport="body">
    <div 
        x-data="imageGalleryModal()"
        x-show="$store.imageGallery.show"
        x-cloak
        @keydown.escape.window="closeModal()"
        class="gallery-modal-fixed"
        style="display: none;"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="gallery-backdrop" @click="closeModal()"></div>

        <button @click="closeModal()" type="button" class="gallery-close-btn" title="Close (ESC)">
            <svg xmlns="http://www.w3.org/2000/svg" style="width: 24px; height: 24px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <div class="gallery-zoom-controls">
            <button @click="zoomOut()" type="button" class="gallery-zoom-btn" title="Zoom Out">
                <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7" />
                </svg>
            </button>

            <span class="gallery-zoom-text">
                <span x-text="Math.round($store.imageGallery.scale * 100) + '%'"></span>
            </span>

            <button @click="zoomIn()" type="button" class="gallery-zoom-btn" title="Zoom In">
                <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                </svg>
            </button>

            <div class="gallery-divider"></div>

            <button @click="resetZoom()" type="button" class="gallery-zoom-btn" title="Reset Zoom">
                <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </button>
        </div>

        <div x-show="$store.imageGallery.title" class="gallery-title">
            <p class="text-white text-sm font-medium" x-text="$store.imageGallery.title"></p>
        </div>

        <div class="gallery-image-container">
            <img 
                :src="$store.imageGallery.url" 
                :alt="$store.imageGallery.title"
                class="gallery-image"
                :style="'transform: scale(' + $store.imageGallery.scale + ');'"
                @dblclick="$store.imageGallery.scale === 1 ? zoomIn() : resetZoom()"
                :class="$store.imageGallery.scale > 1 ? 'cursor-zoom-out' : 'cursor-zoom-in'"
                draggable="false"
            />
        </div>

        <div class="gallery-hint">
            <p class="gallery-hint-text">Double click to zoom • ESC to close</p>
        </div>
    </div>
</template>

@script
<script>
Alpine.store('imageGallery', {
    show: false,
    url: '',
    title: '',
    scale: 1
});

window.openImageGallery = function(url, title) {
    Alpine.store('imageGallery', {
        show: true,
        url: url,
        title: title || 'Image Preview',
        scale: 1
    });
    document.body.style.overflow = 'hidden';
};

window.setupImageWrapper = function(img) {
    if (img.hasAttribute('data-gallery-wrapped')) return;
    if (!img.src || !img.src.includes('storage')) return;

    img.setAttribute('data-gallery-wrapped', 'true');

    const parent = img.parentNode;
    if (parent.classList.contains('image-hover-wrapper')) return;

    const wrapper = document.createElement('div');
    wrapper.className = 'image-hover-wrapper';
    wrapper.style.cssText = 'position: relative; display: inline-block; width: fit-content; max-width: 100%;';

    const overlay = document.createElement('div');
    overlay.className = 'image-hover-overlay';
    overlay.style.cssText = 'position: absolute; top: 8px; right: 8px; display: flex; gap: 8px; opacity: 0; transition: opacity 0.3s ease; z-index: 10; pointer-events: none;';

    const viewBtn = document.createElement('button');
    viewBtn.type = 'button';
    viewBtn.title = 'View Image';
    viewBtn.className = 'gallery-view-btn';
    viewBtn.innerHTML = `<svg xmlns='http://www.w3.org/2000/svg' style='width: 20px; height: 20px;' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15 12a3 3 0 11-6 0 3 3 0 016 0z' /><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z' /></svg>`;
    viewBtn.style.cssText = 'padding: 8px; background: rgba(0,0,0,0.7); color: white; border: none; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease; backdrop-filter: blur(4px); pointer-events: auto;';

    viewBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        window.openImageGallery(img.src, img.alt || 'Image Preview');
    });
    viewBtn.addEventListener('mouseenter', () => { viewBtn.style.background = 'rgba(59,130,246,0.9)'; viewBtn.style.transform = 'scale(1.1)'; });
    viewBtn.addEventListener('mouseleave', () => { viewBtn.style.background = 'rgba(0,0,0,0.7)'; viewBtn.style.transform = 'scale(1)'; });

    const downloadBtn = document.createElement('a');
    downloadBtn.href = img.src;
    downloadBtn.download = img.alt || 'image';
    downloadBtn.title = 'Download Image';
    downloadBtn.className = 'gallery-download-btn';
    downloadBtn.innerHTML = `<svg xmlns='http://www.w3.org/2000/svg' style='width: 20px; height: 20px;' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4' /></svg>`;
    downloadBtn.style.cssText = 'padding: 8px; background: rgba(0,0,0,0.7); color: white; border: none; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease; backdrop-filter: blur(4px); text-decoration: none; pointer-events: auto;';

    downloadBtn.addEventListener('click', (e) => e.stopPropagation());
    downloadBtn.addEventListener('mouseenter', () => { downloadBtn.style.background = 'rgba(34,197,94,0.9)'; downloadBtn.style.transform = 'scale(1.1)'; });
    downloadBtn.addEventListener('mouseleave', () => { downloadBtn.style.background = 'rgba(0,0,0,0.7)'; downloadBtn.style.transform = 'scale(1)'; });

    overlay.appendChild(viewBtn);
    overlay.appendChild(downloadBtn);

    parent.insertBefore(wrapper, img);
    wrapper.appendChild(img);
    wrapper.appendChild(overlay);

    const originalDisplay = window.getComputedStyle(img).display;
    img.style.cssText += `transition: opacity 0.2s ease, filter 0.3s ease; display: ${originalDisplay === 'inline' ? 'block' : originalDisplay}; max-width: 100%; height: auto;`;

    wrapper.addEventListener('mouseenter', () => { overlay.style.opacity = '1'; img.style.opacity = '0.95'; img.style.filter = 'blur(3px)'; });
    wrapper.addEventListener('mouseleave', () => { overlay.style.opacity = '0'; img.style.opacity = '1'; img.style.filter = 'blur(0px)'; });
};

window.initAllImages = function() {
    document.querySelectorAll('img[src*="storage"]:not([data-gallery-wrapped])').forEach(img => {
        window.setupImageWrapper(img);
    });
};

window.imageGalleryInit = function() {
    return {
        observer: null,

        init() {
            this.$nextTick(() => window.initAllImages());

            this.observer = new MutationObserver((mutations) => {
                const shouldReinit = mutations.some(mutation =>
                    [...mutation.addedNodes].some(node =>
                        node.nodeType === 1 && (
                            (node.tagName === 'IMG' && node.src?.includes('storage')) ||
                            node.querySelector?.('img[src*="storage"]')
                        )
                    )
                );

                if (shouldReinit) {
                    setTimeout(() => window.initAllImages(), 100);
                }
            });

            this.observer.observe(document.body, { childList: true, subtree: true });

            if (window.Livewire) {
                Livewire.hook('commit', ({ succeed }) => {
                    succeed(() => setTimeout(() => window.initAllImages(), 150));
                });
            }
        },

        destroy() {
            this.observer?.disconnect();
        }
    };
};

window.imageGalleryModal = function() {
    return {
        closeModal() {
            Alpine.store('imageGallery').show = false;
            Alpine.store('imageGallery').scale = 1;
            document.body.style.overflow = '';
        },
        zoomIn() {
            const store = Alpine.store('imageGallery');
            if (store.scale < 3) store.scale += 0.25;
        },
        zoomOut() {
            const store = Alpine.store('imageGallery');
            if (store.scale > 0.5) store.scale -= 0.25;
        },
        resetZoom() {
            Alpine.store('imageGallery').scale = 1;
        }
    };
};
</script>
@endscript

<style>
[x-cloak] { display: none !important; }

.image-hover-wrapper {
    position: relative;
    display: inline-block;
    max-width: 100%;
}

.image-hover-wrapper img {
    transition: opacity 0.2s ease, filter 0.3s ease;
    display: block;
    max-width: 100%;
    height: auto;
}

.image-hover-wrapper:hover img {
    filter: blur(3px);
    opacity: 0.95;
}

.image-hover-overlay { pointer-events: none; }
.image-hover-overlay button,
.image-hover-overlay a { pointer-events: auto; }

.gallery-modal-fixed {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    z-index: 60;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.gallery-backdrop {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0, 0, 0, 0.98);
    z-index: 60;
    cursor: pointer;
}

.gallery-close-btn {
    position: fixed;
    top: 1rem; right: 1rem;
    z-index: 65;
    padding: 0.75rem;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(12px);
    color: white;
    border: none;
    border-radius: 9999px;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
}

.gallery-close-btn:hover {
    background: rgba(239, 68, 68, 0.9);
    transform: scale(1.1);
}

.gallery-image-container {
    position: fixed;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    z-index: 62;
    max-width: 90vw;
    max-height: 90vh;
    display: flex;
    align-items: center;
    justify-content: center;
    will-change: transform;
    backface-visibility: hidden;
}

.gallery-image {
    max-width: 90vw;
    max-height: 90vh;
    width: auto;
    height: auto;
    object-fit: contain;
    transition: transform 0.3s ease-out;
    transform-origin: center center;
    user-select: none;
    will-change: transform;
    backface-visibility: hidden;
    animation: fadeInStatic 0.15s ease-out;
}

@keyframes fadeInStatic {
    from { opacity: 0; }
    to { opacity: 1; }
}

.gallery-zoom-controls {
    position: fixed;
    top: 1rem; left: 50%;
    transform: translateX(-50%);
    z-index: 63;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(12px);
    border-radius: 9999px;
    padding: 0.5rem 1rem;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
}

.gallery-zoom-btn {
    padding: 0.5rem;
    color: white;
    background: transparent;
    border: none;
    border-radius: 9999px;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.gallery-zoom-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: scale(1.1);
}

.gallery-zoom-text {
    color: white;
    font-weight: 600;
    font-size: 0.875rem;
    min-width: 60px;
    text-align: center;
}

.gallery-divider {
    width: 1px;
    height: 1.5rem;
    background: rgba(255, 255, 255, 0.3);
    margin: 0 0.25rem;
}

.gallery-title {
    position: fixed;
    bottom: 4rem; left: 50%;
    transform: translateX(-50%);
    z-index: 63;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(12px);
    padding: 0.75rem 1.5rem;
    border-radius: 9999px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
    max-width: 80%;
    text-align: center;
}

.gallery-hint {
    position: fixed;
    bottom: 1rem; left: 50%;
    transform: translateX(-50%);
    z-index: 63;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(12px);
    padding: 0.5rem 1rem;
    border-radius: 9999px;
}

.gallery-hint-text {
    color: white !important;
    font-size: 0.75rem;
    margin: 0;
}

.cursor-zoom-in { cursor: zoom-in; }
.cursor-zoom-out { cursor: zoom-out; }

@media (max-width: 640px) {
    .gallery-image,
    .gallery-image-container {
        max-width: 95vw;
        max-height: 85vh;
    }

    .gallery-zoom-controls {
        top: 0.75rem;
        padding: 0.375rem 0.75rem;
        gap: 0.25rem;
    }

    .gallery-zoom-btn { padding: 0.375rem; }

    .gallery-close-btn {
        top: 0.75rem;
        right: 0.75rem;
        padding: 0.5rem;
    }

    .gallery-title {
        bottom: 3rem;
        max-width: 90%;
        padding: 0.5rem 1rem;
    }
}
</style>