@extends('layouts.app')
@section('content')
<h1 class="cover-heading">Aqui tens mais alguma informação de como podes usar o sistema</h1>
<p class="lead">Também temos uma API disponivel</p>
<div class="container" style="text-align: left">
    <h2>Como funciona o sistema?</h2>
    <p>Assim que o progama recebe uma foto, a foto recebe um UUID (versão 4).</p>
    <p>De minuto a minuto, o nosso programa corre o <a href="https://github.com/Shawn-Shan/fawkes">Fawkes</a>, após o
        programa terminar, movemos as imagens processadas e apagamos as originais. O nosso progrma, também apaga todas
        as fotos com mais de {{env('MINUTES_TO_STORE_FILES')}} minutos.</p>
    <p>Se o <a href="https://github.com/Shawn-Shan/fawkes">Fawkes</a> não deteta nenhuma cara na foto, não a vai
        processar, sendo que a mesma é logo apagada após execução.</p>
    <h2>API</h2>
    <p>O pedidos à API são limitados a 20 por minuto.</p>
    <div id="accordion">
        <div class="card">
            <div class="card-header" id="headingOne">
                <h5 class="mb-0">
                    <button class="btn collapsed" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false"
                        aria-controls="collapseOne">
                        POST /api/upload
                    </button>
                </h5>
            </div>

            <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                <div class="card-body" style="color:black; text-shadow: 0 0 !important;">
                    <h3>Parametros Necessários</h3>
                    <ul>
                        <li>image - Max: 1024 KB, Formatos Aceites: jpg/jpeg, png, bmp, gif, svg, webp</li>
                    </ul>
                    <h3>Formato da Resposta - Sucesso</h3>
                    <samp>[<br/>
                        &nbsp;&nbsp;&nbsp;&nbsp;'status' => 'UPLOADED',<br/>
                        &nbsp;&nbsp;&nbsp;&nbsp;'uuid' => "generated_image_uuid"<br/>
                    ]</samp><br/>                    
                    <p>O generated_image_uuid simboliza um UUID v4 que irá ser a referência interna no sistema</p>
                    <h3>Formato da Resposta - Erros</h3>
                    <samp>[<br/>
                        &nbsp;&nbsp;&nbsp;&nbsp;'status' => 'ERROR',<br/>
                        &nbsp;&nbsp;&nbsp;&nbsp;'errors' => [<br/>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'image' => [<br/>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'TIPO_DE_ERRO'<br/>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;]<br/>
                        &nbsp;&nbsp;&nbsp;&nbsp;]<br/>
                    ]</samp>
                    <h4>Tipos de Erros</h4>
                    <ul>
                        <li>IMAGE_REQUIRED - Parametro Necessário <samp>image</samp> não foi encontrado</li>
                        <li>IMAGE_BAD_FORMAT - Formato de imagem não suportado</li>
                        <li>IMAGE_SIZE - Imagem com tamanho superior ao aceite</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header" id="headingTwo">
                <h5 class="mb-0">
                    <button class="btn collapsed" data-toggle="collapse" data-target="#collapseTwo"
                        aria-expanded="false" aria-controls="collapseTwo">
                        POST /api/get
                    </button>
                </h5>
            </div>
            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                <div class="card-body" style="color:black; text-shadow: 0 0 !important;">
                    <h3>Parametros Necessários</h3>
                    <ul>
                        <li>uuid - UUID v4</li>
                    </ul>
                    <h3>Formato da Resposta - Sucesso</h3>
                    <samp>[<br/>
                        &nbsp;&nbsp;&nbsp;&nbsp;'status' => 'TYPE_OF_STATUS',<br/>
                        &nbsp;&nbsp;&nbsp;&nbsp;'url' => "image_url"<br/>
                    ]</samp>                    
                    <h4>Tipos de Estados</h4>
                    <ul>
                        <li>NOT_PROCESSED - A imagem não se encontra processada pelo Fawkes. Pode demorar até 30 minutos.</li>
                        <li>NOT_FOUND - Nenhuma imagem associada a este UUID foi encontra</li>
                        <li>PROCESSED - A imagem encontra-se processada e o <samp>url</samp> simboliza o caminho relativo para o seu destino</li>
                    </ul>
                    <p>O <samp>url</samp> só está definido no <samp>status</samp> <samp>PROCESSED</samp></p>
                    <h3>Formato da Resposta - Erros</h3>
                    <samp>[<br/>
                        &nbsp;&nbsp;&nbsp;&nbsp;'status' => 'ERROR',<br/>
                        &nbsp;&nbsp;&nbsp;&nbsp;'errors' => [<br/>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'uuid' => [<br/>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'TIPO_DE_ERRO'<br/>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;]<br/>
                        &nbsp;&nbsp;&nbsp;&nbsp;]<br/>
                    ]</samp>
                    <h4>Tipos de Erros</h4>
                    <ul>
                        <li>UUID_REQUIRED - Parametro Necessário <samp>uuid</samp> não foi encontrado</li>
                        <li>UUID_NOT_UUID - UUID não está no formato esperado</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection