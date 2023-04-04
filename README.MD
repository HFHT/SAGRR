
[ ] Big list - Age not working
[x] Medical - Date plus one month


$oneMonthOn = new DateTime();
$oneMonthOn->add(new DateInterval('P30D'));
$oneMonthOn->format('Y-m-d');