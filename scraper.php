<?php

require_once 'vendor/autoload.php';

function trimPartial($str) {
    return trim($str, ", ");
}

class scraper
{
    private $dbApi = "https://appext20.dos.ny.gov/pls/ucc_public/web_inhouse_search.print_ucc1_list";
    private $requestData = array(
        "p_name"=>"",
        "p_last"=>"",
        "p_first"=>"",
        "p_middle"=>"",
        "p_suffix"=>"",
        "p_city"=>"",
        "p_state"=>"",
        "p_lapsed"=>"1",
        "p_filetype"=>"ALL");

    function scrape() {
        $this->requestData = array_merge($this->requestData, (array) json_decode($_POST['json'])); //fold in our json values
        $this->requestData['p_last'] = $_POST['p_last']; // and manually add the required values
        $this->requestData['p_name'] = $_POST['p_name'];
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($this->requestData),
            ),
        );

        $context  = stream_context_create($options);
        $result = file_get_contents($this->dbApi, false, $context); // result of an api call

        if ($result === FALSE) {
            die("No response, check that you can contact " . $this->dbApi);
        }

        $html = \SimpleHtmlDom\str_get_html($result);
        $htmlForm = $html->find('form', 0);
        $data = array();

        if (!$htmlForm) { // we didn't get any hits, so we just want to echo an empty json object
            header('Content-Type: application/json');
            die(json_encode([]));
        }

        foreach ($htmlForm->find('table') as $entry) {
            if (count($entry->find('table')) > 1) { // this entry is an whole valid entry, not a sub-table
                $entryData = array();
                $currentType = '';

                foreach ($entry->find('table', 0)->find('tr') as $tableRow) { // hard codes bits to snag out the correct columns
                    $currentType = ($tableRow->find('td',1)->plaintext ? $tableRow->find('td',1)->plaintext : $currentType);
                    if (!$entryData[$currentType]) {
                        $entryData[$currentType] = '';
                    }
                    $entryData[$currentType] .= $tableRow->find('td', 2)->plaintext . ' ' . $tableRow->find('td', 3)->plaintext . ',';
                }
                $data[] = array_map(trimPartial, $entryData);
            }
        }
        header('Content-Type: application/json');
        echo json_encode($data);
    }
    function problem1() {
        $testData = ["michael", "DORIAN"];
        $data = array_map(strtolower, $testData);
        var_dump(array_map(ucfirst, $data));
    }
    function problem2() {
        $testData = ["(123) 456-7890", "1111)555 2345", "098) 123 4567"];
        // note that, in a production environment, you would not want to enforce a
        // specific phone number format on a user. I would start by stripping out all
        // non numeric chars and counting to make sure I have the right number of digits
        $testNumber = function($str) {
            return (boolean) preg_match("/^\([0-9]{3}\) [0-9]{3}-[0-9]{4}/", $str);
        };
        $data = array_map($testNumber, $testData);
        var_dump($data);
    }
    function problem3() {
        $testData = [3,3,3 ,7, 3, 3];
        $equalsOne = function ($num) {
            return $num == 1;
        };
        $data = array_count_values($testData);
        $data = array_filter($data, $equalsOne);
        $keys = array_keys($data);
        echo array_pop($keys);
    }
}