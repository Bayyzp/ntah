<?php
$domain = 'http://cuisibest.runv1975.odns.fr/wp-includes/th/';
$sitemap_name = 'play';
// Load the keywords from the keywords.txt file
$keywords = file('hajar.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Set the maximum number of links per sitemap file
$max_links_per_sitemap = 10000;

// Initialize the sitemap index
$sitemap_index = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$sitemap_index .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// Initialize the sitemap files
$sitemap_files = array();

// Iterate over the keywords and generate the sitemap files
foreach ($keywords as $i => $keyword) {
    // Calculate the sitemap file number
    $sitemap_file_number = ceil(($i + 1) / $max_links_per_sitemap);

    // Create a new sitemap file if necessary
    if (!isset($sitemap_files[$sitemap_file_number])) {
        $sitemap_files[$sitemap_file_number] = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $sitemap_files[$sitemap_file_number] .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    }

    // Create a new URL element
    $sitemap_files[$sitemap_file_number] .= '  <url>' . "\n";
    $sitemap_files[$sitemap_file_number] .= '    <loc>' .$domain. urlencode($keyword) . '</loc>' . "\n";
    $sitemap_files[$sitemap_file_number] .= '  </url>' . "\n";
}

// Close the sitemap files
foreach ($sitemap_files as $sitemap_file_number => &$sitemap_file) {
    $sitemap_file .= '</urlset>' . "\n";
    file_put_contents("$sitemap_name-$sitemap_file_number.xml", $sitemap_file);
}

// Create the sitemap index file
foreach ($sitemap_files as $sitemap_file_number => $sitemap_file) {
    $sitemap_index .= '  <sitemap>' . "\n";
    $sitemap_index .= '    <loc>'.$domain.$sitemap_name.'-' . $sitemap_file_number . '.xml</loc>' . "\n";
    $sitemap_index .= '  </sitemap>' . "\n";
}
$sitemap_index .= '</sitemapindex>' . "\n";

// Save the sitemap index file to sitemap.xml
file_put_contents($sitemap_name.'.xml', $sitemap_index);

// Output a notification that the script has generated the sitemap.xml file
echo "Sitemap.xml sudah selesai dibuat cuk!";

?>