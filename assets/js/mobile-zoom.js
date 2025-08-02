// Mobile Touch Zoom for Product Images
class MobileImageZoom {
    constructor(imageSelector) {
        this.image = document.querySelector(imageSelector);
        this.container = this.image.parentElement;
        this.scale = 1;
        this.minScale = 1;
        this.maxScale = 3;
        this.lastTouchDistance = 0;
        this.isZooming = false;
        
        this.init();
    }
    
    init() {
        // Add touch event listeners
        this.container.addEventListener('touchstart', this.handleTouchStart.bind(this), { passive: false });
        this.container.addEventListener('touchmove', this.handleTouchMove.bind(this), { passive: false });
        this.container.addEventListener('touchend', this.handleTouchEnd.bind(this), { passive: false });
        
        // Add double tap to zoom
        let lastTap = 0;
        this.container.addEventListener('touchend', (e) => {
            const currentTime = new Date().getTime();
            const tapLength = currentTime - lastTap;
            if (tapLength < 500 && tapLength > 0) {
                this.handleDoubleTap(e);
            }
            lastTap = currentTime;
        });
    }
    
    handleTouchStart(e) {
        if (e.touches.length === 2) {
            e.preventDefault();
            this.isZooming = true;
            this.lastTouchDistance = this.getTouchDistance(e.touches);
        }
    }
    
    handleTouchMove(e) {
        if (e.touches.length === 2 && this.isZooming) {
            e.preventDefault();
            const currentDistance = this.getTouchDistance(e.touches);
            const scaleChange = currentDistance / this.lastTouchDistance;
            
            this.scale = Math.min(Math.max(this.scale * scaleChange, this.minScale), this.maxScale);
            this.updateImageScale();
            
            this.lastTouchDistance = currentDistance;
        }
    }
    
    handleTouchEnd(e) {
        if (e.touches.length < 2) {
            this.isZooming = false;
        }
    }
    
    handleDoubleTap(e) {
        e.preventDefault();
        if (this.scale === 1) {
            this.scale = 2;
        } else {
            this.scale = 1;
        }
        this.updateImageScale();
    }
    
    getTouchDistance(touches) {
        const dx = touches[0].clientX - touches[1].clientX;
        const dy = touches[0].clientY - touches[1].clientY;
        return Math.sqrt(dx * dx + dy * dy);
    }
    
    updateImageScale() {
        this.image.style.transform = `scale(${this.scale})`;
        this.image.style.transition = this.isZooming ? 'none' : 'transform 0.3s ease';
        
        // Update cursor based on zoom level
        if (this.scale > 1) {
            this.container.style.cursor = 'zoom-out';
        } else {
            this.container.style.cursor = 'zoom-in';
        }
    }
}

// Initialize mobile zoom on touch devices
if ('ontouchstart' in window) {
    document.addEventListener('DOMContentLoaded', () => {
        new MobileImageZoom('#productImage');
    });
}