<?php
header("Content-Type: application/json; charset=UTF-8");

// Page param
$page = isset($_GET['p']) && is_numeric($_GET['p']) ? $_GET['p'] : 1;

// URLs
$targetUrl = "https://animeworld-india.me/series?page=" . $page;
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
    echo json_encode(["success" => false, "error" => curl_error($ch)]);
    exit;
}
curl_close($ch);

if (!$html) {
    echo json_encode(["success" => false, "error" => "Empty response"]);
    exit;
}

// Load DOM
libxml_use_internal_errors(true);
$dom = new DOMDocument();
$dom->loadHTML($html);
libxml_clear_errors();

$xpath = new DOMXPath($dom);

// =======================
// Series list
// =======================
$nodes = $xpath->query("//ul[contains(@class,'post-lst')]//li[contains(@class,'status-publish')]");

$series = [];

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

    // Extract seriesId
    $seriesId = null;
    if ($link && str_contains($link, "/series/")) {
        $seriesId = trim(str_replace("/series/", "", $link), "/");
    }

    $series[] = [
        "title"    => $title,
        "image"    => $image,
        "year"     => $year,
        "rating"   => $rating,
        "seriesId" => $seriesId
    ];
}

// =======================
// Pagination
// =======================
$pages = [];
$currentPage = (int)$page;
$totalPages = 1;
$hasNext = false;
$hasPrev = false;

$pageNodes = $xpath->query("//nav[contains(@class,'pagination')]//a[contains(@class,'page-link')]");

foreach ($pageNodes as $pNode) {
    $num = trim($pNode->textContent);

    if (is_numeric($num)) {
        $pages[] = (int)$num;

        if (str_contains($pNode->getAttribute("class"), "current")) {
            $currentPage = (int)$num;
        }
    }
}

if (!empty($pages)) {
    $totalPages = max($pages);
}

$hasNext = $currentPage < $totalPages;
$hasPrev = $currentPage > 1;

// =======================
// Output JSON
// =======================
echo json_encode([
    "success" => true,
    "source" => "animeworld-india.me/series",
    "current_page" => $currentPage,
    "total_pages" => $totalPages,
    "has_next" => $hasNext,
    "has_prev" => $hasPrev,
    "pages" => array_values(array_unique($pages)),
    "total_results" => count($series),
    "series" => $series
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
