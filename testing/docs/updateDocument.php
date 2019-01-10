<?php

header('Content-type: application/json');
$response = array();
$modules = scandir("../features");
$execludedMoudles = array("bootstrap", ".", "..");
$moduleDisplayData = array(
    "CollaborationCenter" => array("name" => "Collaboration Center","color" => "#815b3a"),
    "Events" => array("name" => "Events","color" => "#815b3a"),
    "Expert_Thoughts" => array("name" => "Expert Thoughts","color" => "#654982"),
    "FOSSMap" => array("name" => "FOSS Map","color" => "#8eb021"),
    "FOSSPedia" => array("name" => "FOSSPedia","color" => "#654982"),
    "Feedback" => array("name" => "Feedback","color" => "#4a6785"),
    "Market_Place" => array("name" => "Market Place","color" => "#d39c3f"),
    "News" => array("name" => "News","color" => "#d39c3f"),
    "Open_Datasets" => array("name" => "Open Datasets","color" => "#8eb021"),
    "Request_center" => array("name" => "Request Center","color" => "#4a6785"),
    "Success_Stories" => array("name" => "Success Stories","color" => "#ac707a"),
    "System_Notifications" => array("name" => "System Notifications","color" => "#f79232"),
    "products" => array("name" => "Products Portfolio","color" => "#3b7fc4"),
    "registration_and_profiling" => array("name" => "Registeration And Profiling","color" => "#f79232")
);
$defaultUrl = 'https://jira6.espace-technologies.com/browse/';
$modules = array_diff($modules, $execludedMoudles);
foreach ($modules as $moduleDir) {
    $module = array(
        "name" => $moduleDisplayData[$moduleDir]["name"],
        "color" => $moduleDisplayData[$moduleDir]["color"],
        "features" => array()
    );
    $features = scandir("../features/" . $moduleDir);
    $features = array_diff($features, $execludedMoudles);
    foreach ($features as $featureFile) {
        $filePath = "../features/" . $moduleDir . '/' . $featureFile;
        $myfile = fopen($filePath, "r") or die("Unable to open file!");
        
        $implementedScenarios = 0;
        $notImplementedScenarios = 0;

        $feature = array(
            "title" => "",
            "body" => "",
            "url"  => "",
            "hasBackground" => false,
            "background" => array(),
            "scenarios" => array()
        );
        $background = array(
            "steps" => array()
        );
        $scenario = null;
        $tags = array();
        $step = null;
        $currentStepsOwner = null;
        while (!feof($myfile)) {
            $line = fgets($myfile);

            if (trim($line) != "") {
                if(!preg_match("/^\s*\|.*\|\s*$/im", $line)) {
                    if ($step !== null && $currentStepsOwner == "background") {
                        $background["steps"][] = $step;
                        $step = null;
                    } else if ($step !== null) {
                        $scenario["steps"][] = $step;
                        $step = null;
                    }
                }
                if (strpos($line, 'Feature:') !== FALSE) {
                    $fileName = basename($filePath, ".feature");
                    $fileName = substr($fileName, 0, strpos($fileName, '-', strpos($fileName, '-')+1));
                    $feature["title"] = $fileName.': '.str_replace('Feature:', '', $line);

                    //set url
                    $feature["url"] = $defaultUrl.$fileName;
                } else if (strpos($line, 'Background:') !== FALSE) {
                    $feature["hasBackground"] = true;
                    $currentStepsOwner = "background";
                } else if (strpos($line, 'Scenario:') !== FALSE || strpos($line, 'Scenario Outline:') !== FALSE) {
                    if ($currentStepsOwner == "background") {
                        $feature["background"] = $background;
                        $currentStepsOwner = "scenario";
                    } else if ($scenario) {
                        $feature["scenarios"][] = $scenario;
                    }
                    $isImplemented = in_array("not_implemented", $tags) ? false : (in_array("WIP", $tags) ? false : true);
                    $implementedScenarios += $isImplemented ? 1 : 0;
                    $notImplementedScenarios += $isImplemented ? 0 : 1;
                    $scenario = array(
                        "steps" => array(),
                        "text" => $line,
                        "tags" => $tags,
                        "isImplemented" => $isImplemented,
                        "hasOutline" => false,
                        "outline" => array()
                    );
                } else if (strpos($line, 'Given ')) {
                    $step = array(
                        "type" => "Given",
                        "text" => substr($line, strpos($line, 'Given ')+6),
                        "hasOutline" => false,
                        "outline" => array()
                    );
                } else if (strpos($line, 'When ') > -1) {
                    $step = array(
                        "type" => "When",
                        "text" => substr($line, strpos($line, 'When ')+5),
                        "hasOutline" => false,
                        "outline" => array()
                    );
                } else if (strpos($line, 'Then ') > -1) {
                    $step = array(
                        "type" => "Then",
                        "text" => substr($line, strpos($line, 'Then ')+5),
                        "hasOutline" => false,
                        "outline" => array()
                    );
                } else if (strpos($line, 'And ') > -1) {
                    $step = array(
                        "type" => "And",
                        "text" => substr($line, strpos($line, 'And ')+4),
                        "hasOutline" => false,
                        "outline" => array()
                    );
                } else if (preg_match ("/^\s*\|.*\|\s*$/im", $line)) {
                    $textStart = strpos($line,'|');
                    $splittedLine = explode("|",trim(substr($line, $textStart)));
                    $line = array();
                    foreach ($splittedLine as $cell) {
                        if(trim($cell)) {
                            $line[] = htmlspecialchars(trim($cell));
                        }
                    }
                    if($step != null) {
                        $step["hasOutline"] = true;
                        $step["outline"][] = $line;
                    }
                } else if (strpos($line, '@') > -1) {
                    $line = ltrim(trim($line));
                    $tags = split('@', $line);
                    $tags = array_map('trim', $tags);
                    $tags = array_filter($tags);
                } else {
//                    var_dump($line);
//                    var_dump(preg_match ("/^\s*\|.*\|\s*$/im", $line));
                }
            }
        }
        if ($step !== null && $currentStepsOwner == "background") {
            $background["steps"][] = $step;
            $feature["background"] = $background;
            $step = null;
        } else if ($step !== null) {
            $scenario["steps"][] = $step;
            $feature["scenarios"][] = $scenario;
            $step = null;
        }
        $feature["implementedPercentage"] = $implementedScenarios * 100/ ($implementedScenarios + $notImplementedScenarios);
        fclose($myfile);
        
        $module["features"][] = $feature;
    }
    $response["modules"][] = $module;
}
echo json_encode($response);
