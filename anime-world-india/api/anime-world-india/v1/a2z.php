<?php
header("Content-Type: application/json");

$letter = $_GET['letter'] ?? '';
$page   = $_GET['page'] ?? 1;

$letter = trim($letter);
$page   = (int)$page;

if ($letter === '') {
    echo json_encode([
        "success" => false,
        "error" => "letter parameter is required"
    ]);
    exit;
}

$url = "https://animeworld-india.me/letter/" . urlencode($letter) . "?page=" . $page;

/* -------- Fetch HTML -------- */
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_USERAGENT => "Mozilla/5.0"
]);
$html = curl_exec($ch);
curl_close($ch);

if (!$html) {
    echo json_encode([
        "success" => false,
        "error" => "Failed to fetch page"
    ]);
    exit;
}

/* -------- Parse HTML -------- */
libxml_use_internal_errors(true);
$dom = new DOMDocument();
$dom->loadHTML($html);
$xpath = new DOMXPath($dom);

/* -------- Extract Items -------- */
$items = [];
$nodes = $xpath->query("//ul[contains(@class,'post-lst')]/li/article");

foreach ($nodes as $article) {

    $titleNode = $xpath->query(".//h2[@class='entry-title']", $article)->item(0);
    $title = $titleNode ? trim($titleNode->textContent) : null;

    $ratingNode = $xpath->query(".//span[@class='vote']", $article)->item(0);
    $rating = $ratingNode ? trim(preg_replace('/\s+/', ' ', $ratingNode->textContent)) : null;

    $yearNode = $xpath->query(".//span[@class='year']", $article)->item(0);
    $year = $yearNode ? trim($yearNode->textContent) : null;

    $imgNode = $xpath->query(".//img", $article)->item(0);
    $poster = $imgNode ? $imgNode->getAttribute("src") : null;

    $linkNode = $xpath->query(".//a[contains(@class,'lnk-blk')]", $article)->item(0);
    $link = $linkNode ? $linkNode->getAttribute("href") : null;
    $id = $link ? ltrim($link, "/") : null;

    $items[] = [
        "title" => $title,
        "rating" => $rating,
        "year" => $year,
        "poster" => $poster,
        "url" => $link ? "https://animeworld-india.me" . $link : null,
        "id" => $id,
        "type" => ($id && str_starts_with($id, "movie/")) ? "movie" : "series"
    ];
}

/* -------- Pagination -------- */
$pages = [];
$currentPage = 1;
$totalPages = 1;
$hasNext = false;
$hasPrev = false;

$pageNodes = $xpath->query("//nav[contains(@class,'pagination')]//a[contains(@class,'page-link')]");

foreach ($pageNodes as $p) {
    $num = trim($p->textContent);
    if (is_numeric($num)) {
        $pages[] = (int)$num;

        if (strpos($p->getAttribute("class"), "current") !== false) {
            $currentPage = (int)$num;
        }
    }
}

if (!empty($pages)) {
    $totalPages = max($pages);
}

$hasNext = $currentPage < $totalPages;
$hasPrev = $currentPage > 1;

/* -------- Output -------- */
echo json_encode([
    "success" => true,
    "letter" => $letter,
    "current_page" => $currentPage,
    "total_pages" => $totalPages,
    "has_next" => $hasNext,
    "has_prev" => $hasPrev,
    "total_results" => count($items),
    "results" => $items
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
