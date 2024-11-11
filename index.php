<?php

$curl_handle = curl_init();
curl_setopt($curl_handle, CURLOPT_URL, "https://api.github.com/search/repositories?q=+stars:12..24+sort:updated&order:desc");
curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, value: true);
curl_setopt($curl_handle, CURLOPT_HTTPHEADER, [
    "Accept: application/vnd.github.v3+json",
    "User-Agent: Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/93.0.4577.82 Mobile Safari/537.36",
]);

$response = curl_exec($curl_handle);
curl_close($curl_handle);

if ($response === false) {
    die(curl_error($curl_handle));
}

$data = json_decode($response, true);
// var_dump($data);

$Repos = [];

foreach ($data["items"] as $repo) {
    array_push($Repos, [
        "name" => $repo["full_name"],
        "url" => $repo["html_url"],
        "desc" => $repo["description"],
        "lang" => $repo["language"],
    ]);
}

function generateReadme()
{
    $readme = "# Repos\n\n";
    $readme .= "| Language | Repo | Description |\n";
    $readme .= "| - | --------- | --------- |\n";
    foreach ($GLOBALS["Repos"] as $Repo) {
        $readme .= "| " . $Repo["lang"] . " | [" . $Repo["name"] . "](" . $Repo["url"] . ") | " . $Repo["desc"] . " |\n";
    }
    return $readme;
}

file_put_contents("README.md", generateReadme());
