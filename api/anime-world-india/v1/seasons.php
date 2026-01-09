<?php
header("Content-Type: application/json; charset=UTF-8");

// Get seriesID
$seriesID = isset($_GET['seriesID']) ? trim($_GET['seriesID']) : '';

if ($seriesID === '') {
    echo json_encode([
        "success" => false,
        "error" => "Missing seriesID parameter"
    ]);
    exit;
}

// Build URL
$targetUrl = "https://animeworld-india.me/series/" . $seriesID;
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

/* =========================
   SERIES DETAILS
========================= */

$titleNode   = $xpath->query("//h1[contains(@class,'entry-title')]")->item(0);
$imgNode     = $xpath->query("//div[contains(@class,'post-thumbnail')]//img")->item(0);
$yearNode    = $xpath->query("//span[contains(@class,'year')]")->item(0);
$durationNode= $xpath->query("//span[contains(@class,'duration')]")->item(0);
$descNode    = $xpath->query("//div[contains(@class,'description')]//p")->item(0);
$ratingNode  = $xpath->query("//span[contains(@class,'vote')]//span[contains(@class,'num')]")->item(0);
$seasonCountNode = $xpath->query("//span[contains(@class,'seasons')]//span")->item(0);

$seriesTitle = $titleNode ? trim(html_entity_decode($titleNode->textContent)) : null;
$poster      = $imgNode ? $imgNode->getAttribute("src") : null;
$year        = $yearNode ? trim($yearNode->textContent) : null;
$duration    = $durationNode ? trim($durationNode->textContent) : null;
$description = $descNode ? trim(html_entity_decode($descNode->textContent)) : null;
$rating      = $ratingNode ? trim($ratingNode->textContent) : null;
$totalSeasons= $seasonCountNode ? trim($seasonCountNode->textContent) : null;

/* =========================
   SEASONS LIST
========================= */

$seasonNodes = $xpath->query("//div[contains(@class,'season-card')]");
$seasons = [];

foreach ($seasonNodes as $card) {

    $numberNode  = $xpath->query(".//div[contains(@class,'season-number')]", $card)->item(0);
    $nameNode    = $xpath->query(".//div[contains(@class,'season-name')]", $card)->item(0);
    $epCountNode = $xpath->query(".//span[contains(@class,'episode-count')]", $card)->item(0);
    $linkNode    = $xpath->query(".//a[contains(@class,'season-link')]", $card)->item(0);

    $seasonNumber = $numberNode ? trim($numberNode->textContent) : null;
    $seasonName   = $nameNode ? trim(html_entity_decode($nameNode->textContent)) : null;
    $episodes     = $epCountNode ? trim($epCountNode->textContent) : null;
    $link         = $linkNode ? $linkNode->getAttribute("href") : null;

    $seasonId = null;
    if ($link && str_contains($link, "/season/")) {
        $seasonId = trim(str_replace("/season/", "", $link), "/");
    }

    $seasons[] = [
        "seasonNumber" => $seasonNumber,
        "seasonName"   => $seasonName,
        "episodes"     => $episodes,
        "seasonId"     => $seasonId
    ];
}

/* =========================
   OUTPUT
========================= */

echo json_encode([
    "success" => true,
    "source" => "animeworld-india.me/series",
    "series" => [
        "seriesId" => $seriesID,
        "title" => $seriesTitle,
        "poster" => $poster,
        "year" => $year,
        "duration" => $duration,
        "rating" => $rating,
        "totalSeasons" => $totalSeasons,
        "description" => $description
    ],
    "seasons" => $seasons
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
