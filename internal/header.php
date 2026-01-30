<?php
function renderPage($htmlFile) {

    // Aggiunge se manca / all'inizio del percorso
    if ($htmlFile[0] !== '/') {
        $htmlFile = __DIR__ . '/' . $htmlFile;
    }
    $content = file_get_contents($htmlFile);
    
    $menuFile = isset($_SESSION['user_id'])
        ? __DIR__ . "/home/menu-user.html"
        : __DIR__ . "/home/menu-guest.html";

    $menuContent = file_get_contents($menuFile);
    $currentPage = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
    
    
    $pattern = '/<li>\s*<a ([^>]*data-page="' . preg_quote($currentPage, '/') . '"[^>]*)>(.*?)<\/a>\s*<\/li>/';
    
    if (preg_match($pattern, $menuContent, $matches)) {
        $attributes = $matches[1];
        $contentInside = $matches[2];
        
        $keepLang = '';
        if (in_array($currentPage, ['home', 'login', 'logout'])) {
            $keepLang = ' lang="en"';
        }
        
        $span = '<li><span' . $keepLang . ' aria-current="page">' . $contentInside . '</span></li>';
        
        $menuContent = preg_replace($pattern, $span, $menuContent);
    }
    
    $content = str_replace('[Menu]', $menuContent, $content);
    
    echo $content;
}

function renderFromHtml($htmlContent) {
    $menuFile = isset($_SESSION['user_id'])
        ? __DIR__ . "/home/menu-user.html"
        : __DIR__ . "/home/menu-guest.html";
    
    $menuContent = file_get_contents($menuFile);
    $currentPage = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
    
    $pattern = '/<li>\s*<a ([^>]*data-page="' . preg_quote($currentPage, '/') . '"[^>]*)>(.*?)<\/a>\s*<\/li>/';
    
    if (preg_match($pattern, $menuContent, $matches)) {
        $attributes = $matches[1];
        $contentInside = $matches[2];
        
        $keepLang = '';
        if (in_array($currentPage, ['home', 'login', 'logout'])) {
            $keepLang = ' lang="en"';
        }
        
        $span = '<li><span' . $keepLang . ' aria-current="page">' . $contentInside . '</span></li>';
        $menuContent = preg_replace($pattern, $span, $menuContent);
    }
    
    $htmlContent = str_replace('[Menu]', $menuContent, $htmlContent);
    
    echo $htmlContent;
}

?>