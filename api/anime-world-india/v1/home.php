<?php
header("Content-Type: application/json; charset=UTF-8");

// Target site
$targetUrl = "https://animeworld-india.me/";
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

// Load HTML into DOM
libxml_use_internal_errors(true);
$dom = new DOMDocument();
$dom->loadHTML($html);
libxml_clear_errors();

$xpath = new DOMXPath($dom);

// Helper function to extract list data
function extractItems($xpath, $sectionId, $type = "series") {
    $items = [];

    $nodes = $xpath->query("//section[@id='$sectionId']//li[contains(@class,'status-publish')]");

    foreach ($nodes as $li) {
        $titleNode = $xpath->query(".//h2[@class='entry-title']", $li)->item(0);
        $imgNode   = $xpath->query(".//img", $li)->item(0);
        $yearNode  = $xpath->query(".//span[@class='year']", $li)->item(0);
        $linkNode  = $xpath->query(".//a[contains(@class,'lnk-blk')]", $li)->item(0);
        $ratingNode= $xpath->query(".//span[@class='vote']", $li)->item(0);

        $title = $titleNode ? trim($titleNode->textContent) : null;
        $image = $imgNode ? $imgNode->getAttribute("src") : null;
        $year  = $yearNode ? trim($yearNode->textContent) : null;
        $link  = $linkNode ? $linkNode->getAttribute("href") : null;
        $rating= $ratingNode ? trim($ratingNode->textContent) : null;

        // Determine ID instead of full URL
        $id = null;
        if ($link) {
            if ($type === "series" && str_contains($link, "/series/")) {
                $id = trim(str_replace("/series/", "", $link), "/");
            } elseif ($type === "movie" && str_contains($link, "/movie/")) {
                $id = trim(str_replace("/movie/", "", $link), "/");
            }
        }

        $items[] = [
            "title"  => $title,
            "image"  => $image,
            "year"   => $year,
            "rating" => $rating,
            $type."Id" => $id
        ];
    }

    return $items;
}

// Extract data
$latestSeries = extractItems($xpath, "widget_list_movies_series-2", "series");
$latestMovies = extractItems($xpath, "widget_list_movies_series-3", "movie");

// Final JSON response
echo json_encode([
    "success" => true,
    "source" => "animeworld-india.me",
    "latest_series" => $latestSeries,
    "latest_movies" => $latestMovies
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
