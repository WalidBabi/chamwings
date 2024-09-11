<!DOCTYPE html>
<html lang="en">
<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <meta http-equiv="X-UA-Compatible" content="ie=edge">
     <title>Answer Question</title>
     <style>
          .question{
               text-align: center;
               margin-top: 1%;
               font-weight: 800;
          }
          .employee{
               margin-left: 2%;
               margin-top: 3%;
               font-size: 25px;
               font-weight: 600;
               color: blue;
          }
          .answer{
               margin-left: 2%;
               margin-top: 3%;
               font-size: 23px;
          }
     </style>
</head>
<body>
     <h1 class="question">{{$fAQ->question}}</h1>
     <h2 class="employee">{{$fAQ->employee->name}}</h2>
     <div class="answer">{{$fAQ->answer}}</div>
</body>
</html>