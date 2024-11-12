<?php

function curl(string $url): array|bool
{
    $curl_handle = curl_init();
    curl_setopt($curl_handle, CURLOPT_URL, $url);
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
    return json_decode($response, true);
}

$data = curl("https://api.github.com/search/repositories?q=+stars:12..24+sort:updated&order:desc");

$json_path = "repos.json";
$Repos = json_decode(file_get_contents($json_path), true);

foreach ($data["items"] as $repo) {
    if ($repo["language"]) {
        $Repos[$repo["id"]] = [
            "name" => $repo["full_name"],
            "url" => $repo["html_url"],
            "desc" => $repo["description"],
            "langs" => implode(", ", array_keys(curl($repo["languages_url"]))),
            "update" => $repo["updated_at"]
        ];
    }
}

function sort_repos($a, $b)
{
    return ($a["update"] > $b["update"]) ? -1 : 1;
}
uasort($Repos, "sort_repos");

function generateReadme()
{
    $readme = "# Repos\n\n";
    $readme .= "| Language | Repo | Description |\n";
    $readme .= "| - | --------- | --------- |\n";
    foreach ($GLOBALS["Repos"] as $Repo) {
        $readme .= "| " . $Repo["langs"] . " | [" . $Repo["name"] . "](" . $Repo["url"] . ") | " . $Repo["desc"] . " |\n";
    }
    return $readme;
}

file_put_contents($json_path, json_encode($Repos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
file_put_contents("README.md", generateReadme());
