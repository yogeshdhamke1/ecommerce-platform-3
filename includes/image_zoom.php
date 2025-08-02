<?php
/**
 * Reusable Image Zoom Component
 * Usage: include this file and call renderImageZoom($imageSrc, $altText, $options)
 */

function renderImageZoom($imageSrc, $altText = '', $options = []) {
    $defaults = [
        'width' => 'w-full',
        'height' => 'h-96',
        'containerClass' => 'relative',
        'showFullscreen' => true,
        'zoomLevel' => 2,
        'lensSize' => 100
    ];
    
    $options = array_merge($defaults, $options);
    $uniqueId = uniqid('zoom_');
    
    echo "
    <div class=\"{$options['containerClass']}\">
        <div id=\"{$uniqueId}_container\" class=\"relative overflow-hidden rounded-lg shadow-md cursor-zoom-in zoom-container\">
            <img id=\"{$uniqueId}_image\" 
                 src=\"{$imageSrc}\" 
                 alt=\"{$altText}\"
                 class=\"{$options['width']} {$options['height']} object-cover transition-transform duration-300\"
                 onerror=\"this.src='../assets/images/demo-product.jpg'\">
            <div id=\"{$uniqueId}_lens\" class=\"zoom-lens absolute border-2 border-white shadow-lg pointer-events-none opacity-0 transition-opacity duration-200\"></div>
        </div>
        <div id=\"{$uniqueId}_result\" class=\"zoom-result absolute top-0 left-full ml-4 w-96 h-96 border border-gray-300 rounded-lg shadow-lg bg-white overflow-hidden opacity-0 transition-opacity duration-200 z-10\"></div>";
    
    if ($options['showFullscreen']) {
        echo "
        <button id=\"{$uniqueId}_fullscreen\" class=\"absolute top-4 right-4 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70 transition\">
            <i class=\"fas fa-expand text-sm\"></i>
        </button>";
    }
    
    echo "
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('{$uniqueId}_container');
        const image = document.getElementById('{$uniqueId}_image');
        const lens = document.getElementById('{$uniqueId}_lens');
        const result = document.getElementById('{$uniqueId}_result');
        const fullscreenBtn = document.getElementById('{$uniqueId}_fullscreen');
        
        let isZooming = false;
        
        container.addEventListener('mouseenter', function() {
            lens.style.opacity = '1';
            result.style.opacity = '1';
            result.style.backgroundImage = 'url(' + image.src + ')';
            result.style.backgroundSize = (image.width * {$options['zoomLevel']}) + 'px ' + (image.height * {$options['zoomLevel']}) + 'px';
            isZooming = true;
        });
        
        container.addEventListener('mouseleave', function() {
            lens.style.opacity = '0';
            result.style.opacity = '0';
            isZooming = false;
        });
        
        container.addEventListener('mousemove', function(e) {
            if (!isZooming) return;
            
            const rect = container.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const lensWidth = {$options['lensSize']};
            const lensHeight = {$options['lensSize']};
            
            let lensX = x - lensWidth / 2;
            let lensY = y - lensHeight / 2;
            
            if (lensX < 0) lensX = 0;
            if (lensY < 0) lensY = 0;
            if (lensX > rect.width - lensWidth) lensX = rect.width - lensWidth;
            if (lensY > rect.height - lensHeight) lensY = rect.height - lensHeight;
            
            lens.style.left = lensX + 'px';
            lens.style.top = lensY + 'px';
            lens.style.width = lensWidth + 'px';
            lens.style.height = lensHeight + 'px';
            
            const fx = result.offsetWidth / lensWidth;
            const fy = result.offsetHeight / lensHeight;
            
            result.style.backgroundPosition = '-' + (lensX * fx) + 'px -' + (lensY * fy) + 'px';
        });
        
        if (fullscreenBtn) {
            fullscreenBtn.addEventListener('click', function() {
                // Trigger existing fullscreen modal if available
                if (typeof window.openImageFullscreen === 'function') {
                    window.openImageFullscreen(image.src, '{$altText}');
                }
            });
        }
    });
    </script>";
}
?>