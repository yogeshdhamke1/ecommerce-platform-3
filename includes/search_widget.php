<?php
// Quick Search Widget - can be included anywhere
$current_path = $_SERVER['REQUEST_URI'];
$is_in_pages = strpos($current_path, '/pages/') !== false;
$search_action = $is_in_pages ? 'search.php' : 'pages/search.php';
$api_path = $is_in_pages ? 'search_api.php' : 'pages/search_api.php';
?>

<div class="search-widget">
    <form action="<?php echo $search_action; ?>" method="GET" class="relative">
        <div class="flex">
            <input type="text" name="search" id="quickSearch" 
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                   placeholder="Quick search..." autocomplete="off">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-r-md hover:bg-blue-700 transition-colors">
                <i class="fas fa-search"></i>
            </button>
        </div>
        <div id="quickSearchSuggestions" class="absolute top-full left-0 right-0 bg-white border border-gray-300 rounded-b-md shadow-lg z-50 hidden max-h-64 overflow-y-auto"></div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const quickSearch = document.getElementById('quickSearch');
    const quickSuggestions = document.getElementById('quickSearchSuggestions');
    let quickTimeout;
    
    if (quickSearch) {
        quickSearch.addEventListener('input', function() {
            clearTimeout(quickTimeout);
            const query = this.value.trim();
            
            if (query.length < 2) {
                quickSuggestions.classList.add('hidden');
                return;
            }
            
            quickTimeout = setTimeout(() => {
                fetch(`<?php echo $api_path; ?>?q=${encodeURIComponent(query)}&limit=8`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            let html = '';
                            data.forEach(item => {
                                html += `
                                    <a href="<?php echo $is_in_pages ? '' : 'pages/'; ?>product.php?id=${item.id}" class="flex items-center p-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                                        <img src="<?php echo $is_in_pages ? '../' : ''; ?>assets/images/${item.image}" alt="${item.name}" class="w-12 h-12 object-cover rounded mr-3" onerror="this.src='<?php echo $is_in_pages ? '../' : ''; ?>assets/images/demo-product.jpg'">
                                        <div class="flex-1">
                                            <div class="font-medium text-gray-900 text-sm">${item.name}</div>
                                            <div class="text-xs text-gray-500">${item.category} • ${item.price}</div>
                                        </div>
                                        <i class="fas fa-arrow-right text-gray-400 text-sm"></i>
                                    </a>
                                `;
                            });
                            html += `
                                <div class="p-3 bg-gray-50 text-center">
                                    <a href="<?php echo $search_action; ?>?search=${encodeURIComponent(query)}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                        View all results for "${query}" <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </div>
                            `;
                            quickSuggestions.innerHTML = html;
                            quickSuggestions.classList.remove('hidden');
                        } else {
                            quickSuggestions.innerHTML = `
                                <div class="p-4 text-center text-gray-500">
                                    <i class="fas fa-search text-2xl mb-2"></i>
                                    <div>No products found for "${query}"</div>
                                </div>
                            `;
                            quickSuggestions.classList.remove('hidden');
                        }
                    })
                    .catch(() => {
                        quickSuggestions.classList.add('hidden');
                    });
            }, 300);
        });
        
        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!quickSearch.contains(e.target) && !quickSuggestions.contains(e.target)) {
                quickSuggestions.classList.add('hidden');
            }
        });
        
        // Show suggestions when focusing on input
        quickSearch.addEventListener('focus', function() {
            if (this.value.trim().length >= 2) {
                quickSuggestions.classList.remove('hidden');
            }
        });
    }
});
</script>