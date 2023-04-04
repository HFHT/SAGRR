
[ ] Big list - Age not working
[x] Medical - Date plus one month
[ ] dbPrep, saveMedia, saveApplContact, saveAdopt - FILTER_SANITIZE_STRING is deprecated
[ ] Test saveAdopt, saveAdoption, memSpawn, ListGroup, filterApp
[ ] functions - $password not defined 

$oneMonthOn = new DateTime();
$oneMonthOn->add(new DateInterval('P30D'));
$oneMonthOn->format('Y-m-d');