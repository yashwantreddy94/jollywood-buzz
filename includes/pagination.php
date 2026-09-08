<?php
/**
 * Pagination Helper Functions
 * 
 * Handles pagination logic and HTML generation
 */

/**
 * Calculate total pages
 */
function getTotalPages($totalItems, $itemsPerPage) {
    return ceil($totalItems / $itemsPerPage);
}

/**
 * Get current page number
 */
function getCurrentPage() {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    return max(1, $page); // Ensure page is at least 1
}

/**
 * Get offset for database query
 */
function getOffset($page, $itemsPerPage) {
    return ($page - 1) * $itemsPerPage;
}

/**
 * Generate pagination HTML
 */
function generatePagination($currentPage, $totalPages, $baseUrl = '') {
    if ($totalPages <= 1) {
        return ''; // No pagination needed
    }
    
    $html = '<div class="pagination">';
    
    // Previous button
    if ($currentPage > 1) {
        $prevPage = $currentPage - 1;
        $html .= '<a href="' . $baseUrl . '?page=1" class="page-link">&laquo; First</a>';
        $html .= '<a href="' . $baseUrl . '?page=' . $prevPage . '" class="page-link">&lsaquo; Previous</a>';
    } else {
        $html .= '<span class="page-link disabled">&laquo; First</span>';
        $html .= '<span class="page-link disabled">&lsaquo; Previous</span>';
    }
    
    // Page numbers
    $startPage = max(1, $currentPage - 2);
    $endPage = min($totalPages, $currentPage + 2);
    
    if ($startPage > 1) {
        $html .= '<a href="' . $baseUrl . '?page=1" class="page-link">1</a>';
        if ($startPage > 2) {
            $html .= '<span class="page-link disabled">...</span>';
        }
    }
    
    for ($i = $startPage; $i <= $endPage; $i++) {
        if ($i == $currentPage) {
            $html .= '<span class="page-link active">' . $i . '</span>';
        } else {
            $html .= '<a href="' . $baseUrl . '?page=' . $i . '" class="page-link">' . $i . '</a>';
        }
    }
    
    if ($endPage < $totalPages) {
        if ($endPage < $totalPages - 1) {
            $html .= '<span class="page-link disabled">...</span>';
        }
        $html .= '<a href="' . $baseUrl . '?page=' . $totalPages . '" class="page-link">' . $totalPages . '</a>';
    }
    
    // Next button
    if ($currentPage < $totalPages) {
        $nextPage = $currentPage + 1;
        $html .= '<a href="' . $baseUrl . '?page=' . $nextPage . '" class="page-link">Next &rsaquo;</a>';
        $html .= '<a href="' . $baseUrl . '?page=' . $totalPages . '" class="page-link">Last &raquo;</a>';
    } else {
        $html .= '<span class="page-link disabled">Next &rsaquo;</span>';
        $html .= '<span class="page-link disabled">Last &raquo;</span>';
    }
    
    $html .= '</div>';
    
    return $html;
}

/**
 * Generate simple pagination (Previous | Next)
 */
function generateSimplePagination($currentPage, $totalPages, $baseUrl = '') {
    if ($totalPages <= 1) {
        return '';
    }
    
    $html = '<div class="pagination-simple">';
    
    if ($currentPage > 1) {
        $prevPage = $currentPage - 1;
        $html .= '<a href="' . $baseUrl . '?page=' . $prevPage . '" class="btn-prev">&lsaquo; Previous</a>';
    }
    
    $html .= '<span class="page-info">Page ' . $currentPage . ' of ' . $totalPages . '</span>';
    
    if ($currentPage < $totalPages) {
        $nextPage = $currentPage + 1;
        $html .= '<a href="' . $baseUrl . '?page=' . $nextPage . '" class="btn-next">Next &rsaquo;</a>';
    }
    
    $html .= '</div>';
    
    return $html;
}

/**
 * Generate numeric pagination
 */
function generateNumericPagination($currentPage, $totalPages, $baseUrl = '') {
    if ($totalPages <= 1) {
        return '';
    }
    
    $html = '<div class="pagination-numeric">';
    
    for ($i = 1; $i <= $totalPages; $i++) {
        if ($i == $currentPage) {
            $html .= '<span class="page-number active">' . $i . '</span>';
        } else {
            $html .= '<a href="' . $baseUrl . '?page=' . $i . '" class="page-number">' . $i . '</a>';
        }
    }
    
    $html .= '</div>';
    
    return $html;
}

/**
 * Generate pagination info (e.g., "Showing 1-10 of 50 items")
 */
function generatePaginationInfo($currentPage, $itemsPerPage, $totalItems) {
    if ($totalItems == 0) {
        return 'No items found';
    }
    
    $startItem = ($currentPage - 1) * $itemsPerPage + 1;
    $endItem = min($currentPage * $itemsPerPage, $totalItems);
    
    return 'Showing ' . $startItem . '-' . $endItem . ' of ' . $totalItems . ' items';
}

?>
