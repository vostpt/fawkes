@extends('layouts.app')

@section('content')
<main role="main" class="inner cover">
    <div id="first">
        <h1 class="cover-heading">Vamos começar</h1>
        <p class="lead">Faz upload a uma foto tua!</p>
        <div id="input_alert" class="alert alert-danger" role="alert" style="display:none">
        </div>
        <div class="input-group">
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="inputGroup" aria-describedby="inputAddon">
                <label class="custom-file-label" for="inputGroup">Escolher Ficheiro</label>
            </div>
            <div class="input-group-append">
                <button class="btn btn-primary" type="button" id="inputAddon" onclick="uploadFile()">Upload</button>
            </div>
        </div>
    </div>
    <div id="second" style="display: none">
        <h1 class="cover-heading">Está quase</h1>
        <p class="lead">Não feches esta página, quando estiver completa iremos mostrar-te a tua foto</p>
        <p class="lead">Pode demorar até 30 minutos</p>
        <img src="/img/loading.gif"/>
    </div>
    <div id="third" style="display: none">
        <h1 class="cover-heading">Aqui tens a tua foto</h1>
        <p class="lead">Guarda-a enquanto podes, vai desaparecer em 60 minutos.</p>
        <img id="final_image" src="/img/logo.png"/>
    </div>
    <div id="forth" style="display: none">
        <h1 class="cover-heading">A tua foto foi apagada</h1>
        <p class="lead">Se não chegaste a receber a tua foto significa que o Fawkes não detetou nenhuma cara na tua foto.</p>
    </div>
</main>
@endsection

@section('scripts')
<script>
    let uuid = "";
    function uploadFile() {
        $("#input_alert").hide();
        var formData = new FormData();
        var imagefile = document.querySelector('#inputGroup');
        formData.append("image", imagefile.files[0]);
        axios.post('api/upload', formData, {
            headers: {
            'Content-Type': 'multipart/form-data'
            }
        }).then(function (response) {
            console.log(response);
            if ('data' in response) {
                if(response.data.status == "UPLOADED") {
                    uuid = response.data.uuid;
                    $("#first").hide();
                    $("#second").show();
                    setInterval(statusChecker, 10000);
                }
                else {
                    console.log("Unexpected error");
                    console.log(response);
                }
            }
        }).catch(function (error) {
            if ('response' in error) {
                if('data' in error.response) {                    
                    if(error.response.data.status == "ERROR") {
                        let text = "";
                        error.response.data.errors.image.forEach(single_error => {
                            switch (single_error) {
                                case "IMAGE_REQUIRED":
                                    text += "Não selecionaste nenhuma imagem<br/>";
                                    break;                            
                                case "IMAGE_BAD_FORMAT":
                                    text += "O ficheiro que fizeste upload não é uma imagem. Formatos aceites: jpg/jpeg, png, bmp, gif, svg, webp<br/>";
                                    break;    
                                case "IMAGE_SIZE":
                                    text += "A imagem tem mais de 1 MB. Só aceitamos imagens até 1 MB.<br/>";
                                    break;    
                            }
                        });
                        $("#input_alert").html(text);
                        $("#input_alert").show();
                    }
                }
            }
        })
    }
    function statusChecker() {
        axios.post('/api/get', {
            uuid: uuid,
        })
        .then(function (response) {
            if ('data' in response) {
                console.log(response.data.status);
                switch(response.data.status) {
                    case "NOT_PROCESSED":
                        break;
                    case "PROCESSED":
                        $("#second").hide();
                        $("#forth").hide();
                        let current_url = $("#final_image").attr("src");
                        if(current_url == "/img/logo.png") {
                            $("#final_image").attr("src",response.data.url);
                        }
                        $("#third").show();
                        break;
                    case "NOT_FOUND":
                        $("#second").hide();
                        $("#third").hide();
                        $("#forth").show();
                        break;
                    default:
                        console.log("Unexpected error");
                        console.log(response);
                        break;
                }
            }
        })
        .catch(function (error) {
            if ('response' in error) {
                if('data' in error.response) {                    
                    if(error.response.data.status == "ERROR") {
                        let text = "";
                        error.response.data.errors.image.forEach(single_error => {
                            switch (single_error) {
                                case "UUID_REQUIRED":
                                    console.log("UUID not defined");
                                    break;
                                case "UUID_NOT_UUID":
                                    console.log("Invalid UUID");
                                    break;
                            }
                        });
                    }
                }
            }
            console.log(error);
        });
    }
    $('#inputGroup').on('change',function(){
        let fileName = $(this).val();
        fileName = fileName.substr(fileName.search("fakepath") + 9);
        $(this).next('.custom-file-label').html(fileName);
    })
</script>
@endsection