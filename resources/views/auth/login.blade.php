<!DOCTYPE html>
<html>
<head>
    <title>Login MacBilling</title>
    <style>
        body{
            font-family:Arial;
            background:#f3f4f6;
            display:flex;
            height:100vh;
            align-items:center;
            justify-content:center;
        }
        .box{
            background:white;
            padding:30px;
            border-radius:10px;
            width:350px;
            box-shadow:0 5px 20px #ddd;
        }
        input{
            width:100%;
            padding:10px;
            margin-bottom:15px;
        }
        button{
            width:100%;
            padding:10px;
            background:#2563eb;
            color:white;
            border:0;
            border-radius:5px;
        }
    </style>
</head>
<body>

<div class="box">
<h2>MacBilling Login</h2>

@if($errors->any())
<p style="color:red">
{{ $errors->first() }}
</p>
@endif

<form method="POST" action="/login">
@csrf

<input type="text" name="username" placeholder="Username">

<input type="password" name="password" placeholder="Password">

<button type="submit">
Login
</button>

</form>
</div>

</body>
</html>
