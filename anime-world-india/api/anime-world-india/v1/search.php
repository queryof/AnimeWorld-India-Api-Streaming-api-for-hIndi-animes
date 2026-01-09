<?php
header("Content-Type: application/json; charset=UTF-8");

// Get params
$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$page  = isset($_GET['p']) && is_numeric($_GET['p']) ? (int)$_GET['p'] : 1;

if ($query === '') {
    echo json_encode([
        "success" => false,
        "error" => "Missing query parameter"
    ]);
    exit;
}

// Build URL
$targetUrl = "https://animeworld-india.me/search?q=" . urlencode($query) . "&page=" . $page;
$proxyUrl  = "https://corsproxy.io/?" . urlencode($targetUrl);

// Fetch HTML
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $proxyUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_USERAGENT => "Mozilla/5.0",
    CURLOPT_TIMEOUT => 20,
]);

$html = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode([
        "success" => false,
        "error" => curl_error($ch)
    ]);
    exit;
}

curl_close($ch);

if (!$html) {
    echo json_encode([
        "success" => false,
        "error" => "Failed to load HTML"
    ]);
    exit;
}

// Load DOM
libxml_use_internal_errors(true);
$dom = new DOMDocument();
$dom->loadHTML($html);
libxml_clear_errors();

$xpath = new DOMXPath($dom);

// Extract items
$nodes = $xpath->query("//ul[contains(@class,'post-lst')]//li[contains(@class,'status-publish')]");

$results = [];

foreach ($nodes as $li) {

    $titleNode  = $xpath->query(".//h2[contains(@class,'entry-title')]", $li)->item(0);
    $imgNode    = $xpath->query(".//img", $li)->item(0);
    $yearNode   = $xpath->query(".//span[contains(@class,'year')]", $li)->item(0);
    $linkNode   = $xpath->query(".//a[contains(@class,'lnk-blk')]", $li)->item(0);
    $ratingNode = $xpath->query(".//span[contains(@class,'vote')]", $li)->item(0);

    $title  = $titleNode ? trim(html_entity_decode($titleNode->textContent)) : null;
    $image  = $imgNode ? $imgNode->getAttribute("src") : null;
    $year   = $yearNode ? trim($yearNode->textContent) : null;
    $link   = $linkNode ? $linkNode->getAttribute("href") : null;
    $rating = $ratingNode ? trim($ratingNode->textContent) : null;

    $type = null;
    $seriesId = null;
    $movieId  = null;

    if ($link) {
        if (str_contains($link, "/series/")) {
            $type = "series";
            $seriesId = trim(str_replace("/series/", "", $link), "/");
        } elseif (str_contains($link, "/movie/")) {
            $type = "movie";
            $movieId = trim(str_replace("/movie/", "", $link), "/");
        }
    }

    $item = [
        "title"  => $title,
        "image"  => $image,
        "year"   => $year,
        "rating" => $rating,
        "type"   => $type
    ];

    if ($type === "series") {
        $item["seriesId"] = $seriesId;
    } elseif ($type === "movie") {
        $item["movieId"] = $movieId;
    }

    $results[] = $item;
}

// Pagination
$totalPages = null;
$hasNextPage = false;

$pageLinks = $xpath->query("//nav[contains(@class,'pagination')]//a[contains(@class,'page-link')]");

if ($pageLinks->length > 0) {
    $pages = [];

    foreach ($pageLinks as $a) {
        $text = trim($a->textContent);
        if (is_numeric($text)) {
            $pages[] = (int)$text;
        }
    }

    if (!empty($pages)) {
        $totalPages = max($pages);
        $hasNextPage = $page < $totalPages;
    }
}

// Output JSON
echo json_encode([
    "success" => true,
    "query" => $query,
    "currentPage" => $page,
    "totalPages" => $totalPages,
    "hasNextPage" => $hasNextPage,
    "source" => "animeworld-india.me/search",
    "results" => $results
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
