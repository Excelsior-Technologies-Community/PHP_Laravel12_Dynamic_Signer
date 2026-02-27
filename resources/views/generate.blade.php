<!DOCTYPE html>
<html>
<head>
<title>Dynamic URL Signer</title>

<style>
body{
    font-family:Arial;
    background:linear-gradient(135deg,#4facfe,#00f2fe);
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    margin:0;
}

.card{
    background:white;
    padding:40px;
    border-radius:15px;
    width:550px;
    text-align:center;
    box-shadow:0 10px 25px rgba(0,0,0,.2);
}

input{
    width:100%;
    padding:12px;
    margin-top:10px;
    border-radius:8px;
    border:1px solid #ccc;
}

button{
    margin-top:15px;
    padding:12px 25px;
    background:#4facfe;
    border:none;
    color:white;
    border-radius:8px;
    cursor:pointer;
}

button:hover{
    background:#007bff;
}

#timer{
    margin-top:15px;
    font-weight:bold;
}
</style>
</head>

<body>

<div class="card">

<h2> Dynamic Signed URL Generator</h2>

<form method="POST" action="{{ route('generate') }}">
@csrf

<input type="text" name="url"
placeholder="Enter URL or Route Name"
required>

<button type="submit">Generate Signed URL</button>

</form>

@if(isset($signedUrl))

<hr>

<input id="link" value="{{ $signedUrl }}" readonly>

<button onclick="copyLink()">Copy Link</button>

<a href="{{ $signedUrl }}">
<button type="button">Open Secure Page</button>
</a>

<p id="timer"></p>

<script>
function copyLink(){
    let input=document.getElementById("link");
    input.select();
    document.execCommand("copy");
    alert("Copied!");
}

let expiry={{ $expiresAt ?? 0 }}*1000;

let timer=setInterval(function(){
    let now=new Date().getTime();
    let distance=expiry-now;

    let m=Math.floor((distance%(1000*60*60))/(1000*60));
    let s=Math.floor((distance%(1000*60))/1000);

    document.getElementById("timer").innerHTML=
        "Expires in: "+m+"m "+s+"s";

    if(distance<0){
        clearInterval(timer);
        document.getElementById("timer").innerHTML="Expired";
    }
},1000);
</script>

@endif

</div>

</body>
</html>