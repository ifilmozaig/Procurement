(function() {
    'use strict';

    const SAFE_IMAGE_SELECTOR = [
        'img[src*="storage"]:not([data-gallery-wrapped])',
        ':not(.fi-ta-actions *)',           
        ':not(.fi-bulk-actions *)',         
        ':not(.fi-checkbox *)',             
        ':not([wire\\:model] *)',          
        ':not(.fi-fo-file-upload *)',    
    ].join('');

    
    function isSafeImage(img) {
        if (img.hasAttribute('data-gallery-wrapped')) return false;
        if (img.closest('.image-hover-wrapper')) return false;
        if (img.closest('.fi-ta-actions')) return false;          
        if (img.closest('.fi-bulk-actions')) return false;          
        if (img.closest('.fi-checkbox')) return false;            
        if (img.closest('[wire\\:model]')) return false;          
        if (img.closest('.fi-fo-file-upload')) return false;      
        if (img.closest('.fi-input-wrapper')) return false;       
        if (img.closest('label')) return false;                   
        if (img.closest('.fi-ta-cell:has(input)')) return false;  
        if (img.closest('thead')) return false;                   
        if (img.closest('.fi-sidebar')) return false;             
        if (img.closest('.fi-topbar')) return false;              
        if (img.closest('.fi-breadcrumbs')) return false;         
        if (img.closest('nav')) return false;                     

        const naturalW = img.naturalWidth || img.width;
        const naturalH = img.naturalHeight || img.height;
        const renderedW = img.offsetWidth;
        const renderedH = img.offsetHeight;
        if (renderedW > 0 && renderedW < 60) return false;
        if (renderedH > 0 && renderedH < 60) return false;

        return true;
    }
    
    function createImageGalleryModal() {
        if (document.getElementById('image-gallery-modal')) return;
        
        const modalHTML = `
        <div id="image-gallery-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 99999; background: rgba(0, 0, 0, 0.98);">
            <button 
                onclick="window.closeImageGallery()"
                style="position: absolute; top: 20px; right: 20px; z-index: 100; background: transparent; border: none; color: white; cursor: pointer; padding: 8px;"
                title="Close (ESC)"
            >
                <svg xmlns="http://www.w3.org/2000/svg" style="width: 40px; height: 40px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div style="position: absolute; top: 20px; left: 50%; transform: translateX(-50%); z-index: 100; display: flex; align-items: center; gap: 12px; background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(10px); border-radius: 50px; padding: 12px 20px;">
                <button onclick="window.zoomOut()" style="padding: 8px; background: transparent; border: none; color: white; cursor: pointer; border-radius: 50%;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='transparent'" title="Zoom Out">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7" /></svg>
                </button>
                <span id="zoom-level" style="color: white; font-weight: 600; font-size: 14px; min-width: 60px; text-align: center;">100%</span>
                <button onclick="window.zoomIn()" style="padding: 8px; background: transparent; border: none; color: white; cursor: pointer; border-radius: 50%;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='transparent'" title="Zoom In">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" /></svg>
                </button>
                <div style="width: 1px; height: 24px; background: rgba(255,255,255,0.3); margin: 0 4px;"></div>
                <button onclick="window.resetZoom()" style="padding: 8px; background: transparent; border: none; color: white; cursor: pointer; border-radius: 50%;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='transparent'" title="Reset Zoom">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                </button>
            </div>

            <a id="download-image-btn" href="#" download style="position: absolute; top: 20px; left: 20px; z-index: 100; padding: 12px; background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(10px); color: white; border-radius: 50%; text-decoration: none; display: flex; align-items: center; justify-content: center;" onmouseover="this.style.background='rgba(34, 197, 94, 0.8)'" onmouseout="this.style.background='rgba(0, 0, 0, 0.6)'" title="Download Image">
                <svg xmlns="http://www.w3.org/2000/svg" style="width: 24px; height: 24px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
            </a>

            <div id="image-title-display" style="position: absolute; bottom: 60px; left: 50%; transform: translateX(-50%); z-index: 100; background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(10px); padding: 12px 24px; border-radius: 50px; display: none;">
                <p style="color: white; font-size: 14px; font-weight: 500; margin: 0;"></p>
            </div>

            <div onclick="window.closeImageGallery()" style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; padding: 80px 40px;">
                <div onclick="event.stopPropagation()" style="display: flex; align-items: center; justify-content: center;">
                    <img id="gallery-image" src="" alt="" style="max-width: 90vw; max-height: 80vh; object-fit: contain; transition: transform 0.3s ease; cursor: zoom-in; user-select: none;" draggable="false" ondblclick="window.toggleZoom()" />
                </div>
            </div>

            <div style="position: absolute; bottom: 20px; right: 20px; z-index: 100; background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(10px); padding: 8px 16px; border-radius: 50px;">
                <p style="color: white; font-size: 12px; opacity: 0.75; margin: 0;">Double click to zoom • ESC to close</p>
            </div>
        </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('image-gallery-modal');
                if (modal && modal.style.display === 'flex') {
                    window.closeImageGallery();
                }
            }
        });
    }
    
    window.currentImageScale = 1;
    
    window.openImageGallery = function(url, title) {
        const modal = document.getElementById('image-gallery-modal');
        const img = document.getElementById('gallery-image');
        const downloadBtn = document.getElementById('download-image-btn');
        const titleDisplay = document.getElementById('image-title-display');
        
        if (!modal || !img) return;
        
        img.src = url;
        img.alt = title || 'Image Preview';
        downloadBtn.href = url;
        
        if (title) {
            titleDisplay.querySelector('p').textContent = title;
            titleDisplay.style.display = 'block';
        } else {
            titleDisplay.style.display = 'none';
        }
        
        window.currentImageScale = 1;
        updateImageZoom();
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        modal.style.opacity = '0';
        setTimeout(() => {
            modal.style.transition = 'opacity 0.2s ease';
            modal.style.opacity = '1';
        }, 10);
    };
    
    window.closeImageGallery = function() {
        const modal = document.getElementById('image-gallery-modal');
        if (!modal) return;
        
        modal.style.opacity = '0';
        setTimeout(() => {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }, 200);
    };
    
    window.zoomIn = function() {
        if (window.currentImageScale < 3) { window.currentImageScale += 0.25; updateImageZoom(); }
    };
    window.zoomOut = function() {
        if (window.currentImageScale > 0.5) { window.currentImageScale -= 0.25; updateImageZoom(); }
    };
    window.resetZoom = function() {
        window.currentImageScale = 1; updateImageZoom();
    };
    window.toggleZoom = function() {
        window.currentImageScale = window.currentImageScale === 1 ? 2 : 1; updateImageZoom();
    };
    
    function updateImageZoom() {
        const img = document.getElementById('gallery-image');
        const zoomLevel = document.getElementById('zoom-level');
        if (img) {
            img.style.transform = `scale(${window.currentImageScale})`;
            img.style.cursor = window.currentImageScale > 1 ? 'zoom-out' : 'zoom-in';
        }
        if (zoomLevel) zoomLevel.textContent = Math.round(window.currentImageScale * 100) + '%';
    }
    
    function setupImageButtons() {
        document.querySelectorAll('img[src*="storage"]:not([data-gallery-wrapped])').forEach(img => {
            if (!isSafeImage(img)) return;

            img.setAttribute('data-gallery-wrapped', 'true');
            
            const wrapper = document.createElement('div');
            wrapper.className = 'image-hover-wrapper';
            wrapper.style.cssText = 'position: relative; display: inline-block; width: fit-content; max-width: 100%;';
            
            const overlay = document.createElement('div');
            overlay.style.cssText = 'position: absolute; top: 8px; right: 8px; display: flex; gap: 8px; opacity: 0; transition: opacity 0.3s ease; z-index: 10; pointer-events: none;';
            
            const viewBtn = document.createElement('button');
            viewBtn.type = 'button';
            viewBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>';
            viewBtn.style.cssText = 'padding: 8px; background: rgba(0,0,0,0.7); color: white; border: none; border-radius: 6px; cursor: pointer; display: flex; align-items: center; transition: all 0.2s; backdrop-filter: blur(4px); pointer-events: auto;';
            viewBtn.title = 'View Image';
            viewBtn.onmouseenter = () => { viewBtn.style.background = 'rgba(59,130,246,0.9)'; viewBtn.style.transform = 'scale(1.1)'; };
            viewBtn.onmouseleave = () => { viewBtn.style.background = 'rgba(0,0,0,0.7)'; viewBtn.style.transform = 'scale(1)'; };
            viewBtn.onclick = (e) => { e.preventDefault(); e.stopPropagation(); window.openImageGallery(img.src, img.alt || 'Image Preview'); };
            
            const downloadBtn = document.createElement('a');
            downloadBtn.href = img.src;
            downloadBtn.download = img.alt || 'image';
            downloadBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>';
            downloadBtn.style.cssText = 'padding: 8px; background: rgba(0,0,0,0.7); color: white; border-radius: 6px; cursor: pointer; display: flex; align-items: center; transition: all 0.2s; backdrop-filter: blur(4px); text-decoration: none; pointer-events: auto;';
            downloadBtn.title = 'Download Image';
            downloadBtn.onmouseenter = () => { downloadBtn.style.background = 'rgba(34,197,94,0.9)'; downloadBtn.style.transform = 'scale(1.1)'; };
            downloadBtn.onmouseleave = () => { downloadBtn.style.background = 'rgba(0,0,0,0.7)'; downloadBtn.style.transform = 'scale(1)'; };
            downloadBtn.onclick = (e) => e.stopPropagation();
            
            overlay.appendChild(viewBtn);
            overlay.appendChild(downloadBtn);
            
            img.parentNode.insertBefore(wrapper, img);
            wrapper.appendChild(img);
            wrapper.appendChild(overlay);
            
            img.style.cssText += 'transition: opacity 0.2s ease, filter 0.3s ease; display: block; max-width: 100%; height: auto;';
            
            wrapper.onmouseenter = () => { overlay.style.opacity = '1'; img.style.opacity = '0.95'; img.style.filter = 'blur(3px)'; };
            wrapper.onmouseleave = () => { overlay.style.opacity = '0'; img.style.opacity = '1'; img.style.filter = 'blur(0px)'; };
        });
    }
    
    function init() {
        createImageGalleryModal();
        setTimeout(setupImageButtons, 500);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    document.addEventListener('livewire:navigated', () => setTimeout(setupImageButtons, 500));
    document.addEventListener('livewire:updated', () => setTimeout(setupImageButtons, 300));
    const observer = new MutationObserver((mutations) => {
        const hasNewImages = mutations.some(m =>
            [...m.addedNodes].some(n =>
                n.nodeType === 1 && (
                    (n.tagName === 'IMG' && n.src?.includes('storage')) ||
                    n.querySelector?.('img[src*="storage"]')
                )
            )
        );
        if (hasNewImages) setTimeout(setupImageButtons, 200);
    });

    observer.observe(document.body, { childList: true, subtree: true });
    
})();