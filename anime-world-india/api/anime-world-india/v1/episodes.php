<?php
header("Content-Type: application/json; charset=UTF-8");

// Get seasonId
$seasonId = isset($_GET['seasonId']) ? trim($_GET['seasonId']) : '';

if ($seasonId === '') {
    echo json_encode([
        "success" => false,
        "error" => "Missing seasonId parameter"
    ]);
    exit;
}

// Build URL
$targetUrl = "https://animeworld-india.me/season/" . $seasonId;
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
   SEASON / ANIME DETAILS
========================= */

$titleNode   = $xpath->query("//h1[contains(@class,'anime-title')]")->item(0);
$posterNode  = $xpath->query("//div[contains(@class,'season-poster')]//img")->item(0);
$metaNodes   = $xpath->query("//div[contains(@class,'meta-row')]//span[contains(@class,'meta-item')]");
$descNode    = $xpath->query("//p[contains(@class,'season-desc')]")->item(0);

$animeTitle = $titleNode ? trim(html_entity_decode($titleNode->textContent)) : null;
$poster     = $posterNode ? $posterNode->getAttribute("src") : null;
$description= $descNode ? trim(html_entity_decode($descNode->textContent)) : null;

$seasonName = null;
$seasonNumber = null;
$totalEpisodes = null;
$rating = null;
$duration = null;

foreach ($metaNodes as $meta) {
    $text = trim($meta->textContent);

    if (str_starts_with($text, "Season:")) {
        $seasonName = trim(str_replace("Season:", "", $text));
    } elseif (str_starts_with($text, "Episodes:")) {
        $totalEpisodes = trim(str_replace("Episodes:", "", $text));
    } elseif (str_starts_with($text, "Rating:")) {
        $rating = trim(str_replace("Rating:", "", $text));
    } elseif (str_starts_with($text, "Duration:")) {
        $duration = trim(str_replace("Duration:", "", $text));
    }
}

/* =========================
   EPISODES LIST
========================= */

$episodeLinks = $xpath->query("//section[contains(@class,'episodes-section')]//a[contains(@href,'/episode/')]");

$episodes = [];

foreach ($episodeLinks as $a) {

    $href = $a->getAttribute("href");

    $episodeId = null;
    if ($href && str_contains($href, "/episode/")) {
        $episodeId = trim(str_replace("/episode/", "", $href), "/");
    }

    $titleNode = $xpath->query(".//h2[contains(@class,'entry-title')]", $a)->item(0);
    $imgNode   = $xpath->query(".//img", $a)->item(0);
    $epNumNode = $xpath->query(".//span[contains(@class,'year')]", $a)->item(0);
    $dateNode  = $xpath->query(".//span[contains(@class,'number')]", $a)->item(0);
    $descNode  = $xpath->query(".//p[contains(@class,'ep-overview')]", $a)->item(0);

    $title = $titleNode ? trim(html_entity_decode($titleNode->textContent)) : null;
    $image = $imgNode ? $imgNode->getAttribute("src") : null;
    $episodeNumber = $epNumNode ? trim($epNumNode->textContent) : null;
    $airDate = $dateNode ? trim($dateNode->textContent) : null;
    $overview = $descNode ? trim(html_entity_decode($descNode->textContent)) : null;

    $episodes[] = [
        "episodeId" => $episodeId,
        "title" => $title,
        "episodeNumber" => $episodeNumber,
        "airDate" => $airDate,
        "image" => $image,
        "overview" => $overview
    ];
}

/* =========================
   OUTPUT
========================= */

echo json_encode([
    "success" => true,
    "source" => "animeworld-india.me/season",
    "season" => [
        "seasonId" => $seasonId,
        "animeTitle" => $animeTitle,
        "seasonName" => $seasonName,
        "totalEpisodes" => $totalEpisodes,
        "rating" => $rating,
        "duration" => $duration,
        "poster" => $poster,
        "description" => $description
    ],
    "episodes" => $episodes
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
