
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MP4/MP3 Converter</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background: linear-gradient(to right, #00b4db, #0083b0);
    color: #fff;
    height: 100vh;
    display: flex;
    flex-direction: column;
}

.container {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    padding: 20px;
    background: rgba(0, 0, 0, 0.5);
    border-radius: 10px;
}

header {
    text-align: center;
    margin-bottom: 20px;
}

header h1 {
    font-size: 2.5em;
    margin: 0;
    animation: fadeIn 2s ease-in-out;
}

header p {
    font-size: 1.2em;
    margin: 0;
    opacity: 0.8;
}

main {
    text-align: center;
    flex: 1;
}

.converter-form {
    margin-bottom: 20px;
}

input[type="text"] {
    width: 60%;
    padding: 10px;
    margin-bottom: 10px;
    border: none;
    border-radius: 5px;
    font-size: 1em;
}

select {
    padding: 10px;
    border: none;
    border-radius: 5px;
    font-size: 1em;
}

button {
    background: #00b4db;
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    font-size: 1em;
    cursor: pointer;
    transition: background 0.3s;
}

button:hover {
    background: #0083b0;
}

.status {
    font-size: 1.2em;
    margin-top: 20px;
}
.report-bug {
    text-align: center;
    margin: 30px 0;
    padding: 20px;
  
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.report-bug .bug {
    font-size: 1.5em;
    color: white;
    margin-bottom: 15px;
}

.report-bug .report-button {
    display: inline-block;
    padding: 10px 20px;
    font-size: 1.2em;
    color: #fff;
    background-color: #007bff;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s, box-shadow 0.3s;
}

.report-bug .report-button:hover {
    background-color: #0056b3;
    box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2);
}

.coming-soon, .trusted-by {
    text-align: center;
    margin-top: 20px;
    padding: 20px;
    background: rgba(0, 0, 0, 0.6);
    border-radius: 10px;
}

.coming-soon h2, .trusted-by h2 {
    font-size: 2em;
    margin: 0;
    animation: fadeIn 2s ease-in-out;
}

.coming-soon p {
    font-size: 1.2em;
    margin: 0;
}

.trusted-by .trust-logos {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
}

.trusted-by .trust-logos img {
    margin: 10px;
    max-width: 150px;
    height: auto;
}

footer {
    text-align: center;
    margin-top: 20px;
    font-size: 0.9em;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>YTOMPC - MP4/MP3 Converter</h1>
            <p>Convert YouTube videos to MP4 or MP3 quickly and easily.</p>
        </header>

        <main>
            <div class="converter-form">
                <input type="text" id="youtubeUrl" placeholder="enetr youtube url">
                
                <select id="format">
                    <option value="mp3" >Mp3</option>
                    <option value="mp4">Mp4</option>

                </select>
                <button id="convertBtn">Convert</button>
            </div>

            <div id="status" class="status"></div>
        </main>
        <section class="report-bug">
    <p class="bug">Kindly report any bug you find on this site.</p>
    <a class="report-button" href="https://www.surveymonkey.com/r/D5YPF3F" target="_blank" rel="noopener noreferrer">Report Bug</a>
   </section>
  
        <section class="coming-soon">
            <h2>New Features Coming Soon!</h2>
            <p>Stay tuned for exciting updates and new features that will enhance your experience.</p>
        </section>

        <section class="coming-soon">
            <h2>Why ytompc</h2>
            <p>Best way to download youtube video also convert to mp3 , no ad , no virus absolutely free with good quality videos and audios.</p>
        </section>
        <footer>
            <p>&copy; 2024 YTOMP. All rights reserved.</p>
        </footer>
    </div>

    <script>
     document.getElementById("convertBtn").addEventListener("click",function(){
        const youtubeUrl=document.getElementById("youtubeUrl").value
        const format=document.getElementById("format").value
        if(!youtubeUrl){
            document.getElementById("status").innerText= "provide valid url"
            return;
        }
        document.getElementById("status").innerText="converting...."
        fetch('converting.php',{
            method:'POST',
            body:JSON.stringify({url:youtubeUrl , format:format}),
            headers:{
                'Content-Type':'application/json'
            }
        }).then(response=> response.json())
        .then(data=>{
            if(data.success){
            const downloadUrl=encodeURI(data.downloadUrl);
            document.getElementById("status").innerHTML=`done: <a href="${downloadUrl}" download > download ${format.toUpperCase()} </a>`
            }else{
                document.getElementById("status").innerText=data.error
            }
        })
        .catch(error=>{
            document.getElementById("status").innerText="error occured:"+ error.message

        })
        
     })
    </script>
</body>
</html>