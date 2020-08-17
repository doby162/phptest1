<?php
require_once 'vendor/autoload.php';
include 'scraper.php';

$request = $_SERVER['REQUEST_URI'];
$scraper = new Scraper();

switch ($request) {
    case '' :
    case '/' : //todo: make this endpoint a view file
        ?>
<script>
    function validateForm() {
        if (document.forms["ucc"]["p_name"].value || document.forms["ucc"]["p_last"].value) {
            return true;}
        else {
            alert("Please fill out either the last name or business name field");
            return false;
        }
    }
</script>
<form name='ucc' action='/api/scraping/ucc' method='post' onsubmit='return validateForm()'>
<input type='text' name='p_last' placeholder='Last name'>
<input type='text' name='p_name' placeholder='Business name'>
<input type='text' name='json' placeholder='Additional json'>
<input type='submit'>
</form>
<p>try a value of {"p_first":"john"} for json with a last name of acker</p>
    <?php
    break;
    case '/api/scraping/ucc' :
        $scraper->scrape();
        break;
    case '/problem1' : // view these endpoints along with their sourcecode to see the test data they're running on
        $scraper->problem1();
        break;
    case '/problem2' :
        $scraper->problem2();
        break;
    case '/problem3' :
        $scraper->problem3();
        break;
    default:
        http_response_code(404);
        echo "page not found";
        break;
}