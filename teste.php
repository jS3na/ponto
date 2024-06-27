<!DOCTYPE html>
<html>
<head>
    <title>Geolocalização com PHP</title>
</head>
<body>
    <h1>Obter Localização Atual</h1>
    <button onclick="getLocation()">Obter Localização</button>
    <p id="location"></p>

    <script>
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition, showError);
            } else {
                document.getElementById("location").innerHTML = "Geolocalização não é suportada por este navegador.";
            }
        }

        function showPosition(position) {
            var latitude = position.coords.latitude;
            var longitude = position.coords.longitude;
            document.getElementById("location").innerHTML = "Latitude: " + latitude + "<br>Longitude: " + longitude;

            // Enviar a localização para o servidor PHP
            var xhttp = new XMLHttpRequest();
            xhttp.open("POST", "save_location.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("latitude=" + latitude + "&longitude=" + longitude);
        }

        function showError(error) {
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    document.getElementById("location").innerHTML = "Usuário negou a solicitação de Geolocalização.";
                    break;
                case error.POSITION_UNAVAILABLE:
                    document.getElementById("location").innerHTML = "As informações de localização não estão disponíveis.";
                    break;
                case error.TIMEOUT:
                    document.getElementById("location").innerHTML = "A solicitação para obter a localização do usuário expirou.";
                    break;
                case error.UNKNOWN_ERROR:
                    document.getElementById("location").innerHTML = "Ocorreu um erro desconhecido.";
                    break;
            }
        }
    </script>
</body>
</html>
