
[ ] Big list - Age not working
[x] Medical - Date plus one month
[ ] dbPrep, saveMedia, saveApplContact, saveAdopt - FILTER_SANITIZE_STRING is deprecated
[x] Test saveAdopt, saveAdoption, memSpawn, ListGroup, filterApp
[ ] functions - $password not defined 
[x] Dog - click on Family and it hangs
[ ] Make sure to remove quotes that are in the text boxes

$oneMonthOn = new DateTime();
$oneMonthOn->add(new DateInterval('P30D'));
$oneMonthOn->format('Y-m-d');