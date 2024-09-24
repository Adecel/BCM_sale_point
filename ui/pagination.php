<?php
function displayPagination($totalItems, $currentPage, $limit, $url) {
    $totalPages = ceil($totalItems / $limit);

    // Generate pagination HTML
    echo '<nav><ul class="pagination">';

    if ($currentPage > 1) {
        echo '<li class="page-item"><a class="page-link" href="'.$url.'?page='.($currentPage - 1).'">Précédent</a></li>';
    }

    for ($i = 1; $i <= $totalPages; $i++) {
        $activeClass = ($i == $currentPage) ? 'active' : '';
        echo '<li class="page-item '.$activeClass.'"><a class="page-link" href="'.$url.'?page='.$i.'">'.$i.'</a></li>';
    }

    if ($currentPage < $totalPages) {
        echo '<li class="page-item"><a class="page-link" href="'.$url.'?page='.($currentPage + 1).'">Suivant</a></li>';
    }

    echo '</ul></nav>';
}
