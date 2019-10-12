<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <title>Diary</title>
</head>
<body>
     
    <p>Hello! A new appointment has been scheduled.</p>
    <p>The information is the following:</p>
    <ul>
        <li>Webinar: {{ $distressCall->webinar->description }}</li>
        <li>Start date: {{ date('d M Y', strtotime($distressCall->date_begin)) }}</li>
        <li>Hour: {{ $distressCall->time }} {{ $distressCall->timezone }}</li>
    </ul>   
</body>
</html>

