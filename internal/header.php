<?php
function renderPage($htmlFile) {
    // Aggiunge se manca / all'inizio del percorso
    if ($htmlFile[0] !== '/') {
        $htmlFile = __DIR__ . '/' . $htmlFile;
    }
    $content = file_get_contents($htmlFile);

    renderFromHtml($content);
}

function renderFromHtml($htmlContent) {
    $menuFile = isset($_SESSION['user_id'])
        ? __DIR__ . "/home/menu-user.html"
        : __DIR__ . "/home/menu-guest.html";
    
    $menuContent = file_get_contents($menuFile);
    $currentPage = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
    $currentPath = pathinfo($_SERVER['PHP_SELF'], PATHINFO_DIRNAME);

    $currentPath = htmlspecialchars($currentPath, ENT_QUOTES);
    $currentPath = str_replace("/error", "", $currentPath);
    $menuContent = str_replace("[CWD]", $currentPath, $menuContent);
        
    // Regex che serve ad individuare l'intero elemento <li> contenente il link attivo, nel template del menu:
    // cerca un tag <a> il cui attributo 'data-page' corrisponda esattamente alla variabile $currentPage (escapata con preg_quote),
    // catturando nel primo gruppo tutti gli attributi del tag e nel secondo il testo visibile del link, gestendo eventuali spaziature variabili.
    $pattern = '/<li>\s*<a ([^>]*data-page="' . preg_quote($currentPage, '/') . '"[^>]*)>(.*?)<\/a>\s*<\/li>/';
    
    if (preg_match($pattern, $menuContent, $matches)) {
        $attributes = $matches[1];
        $contentInside = $matches[2];
        
        $keepLang = '';
        if (in_array($currentPage, ['home', 'login', 'logout'])) {
            $keepLang = ' lang="en"';
        }
        
        // Assicura che la pagina corrente abbia gli attributi corretti
        $span = '<li><span' . $keepLang . ' aria-current="page" tabindex="0">' . $contentInside . '</span></li>';
        $menuContent = preg_replace($pattern, $span, $menuContent);
    }
    
    $htmlContent = str_replace('[Menu]', $menuContent, $htmlContent);
    
    echo $htmlContent;
}

?>
