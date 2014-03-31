<?php

    $this->layout = 'interna';

    add_taxonoy_terms_to_js('area');
    add_taxonoy_terms_to_js('linguagem');
    add_entity_types_to_js('MapasCulturais\Entities\Space');
    add_entity_types_to_js('MapasCulturais\Entities\Agent');
    add_entity_types_to_js('MapasCulturais\Entities\Project');

//    $app = \MapasCulturais\App::i();

    $app->enqueueScript('vendor', 'angular', '/vendor/angular.min.js');
    $app->enqueueScript('vendor', 'angular-rison', '/vendor/angular-rison/angular-rison.min.js');
    $app->enqueueScript('vendor', 'ng-infinite-scroll', '/vendor/ng-infinite-scroll/ng-infinite-scroll.min.js');
    $app->enqueueScript('app', 'ng-mapasculturais', '/js/ng-mapasculturais.js');
    $app->enqueueScript('app', 'SearchService', '/js/SearchService.js');
    $app->enqueueScript('app', 'FindOneService', '/js/FindOneService.js');
    $app->enqueueScript('app', 'SearchMapController', '/js/SearchMap.js');
    $app->enqueueScript('app', 'SearchSpatial', '/js/SearchSpatial.js');
    $app->enqueueScript('app', 'Search', '/js/Search.js');

    $app->enqueueScript('vendor', 'spin.js', '/vendor/spin.min.js', array('angular'));
    $app->enqueueScript('vendor', 'angularSpinner', '/vendor/angular-spinner.min.js', array('spin.js'));


    $app->enqueueScript('vendor', 'jquery-ui-datepicker', '/vendor/jquery-ui.datepicker.js', array('jquery'));
    $app->enqueueScript('vendor', 'jquery-ui-datepicker-pt-BR', '/vendor/jquery-ui.datepicker-pt-BR.min.js', array('jquery'));

    $app->enqueueScript('vendor', 'angular-ui-date', '/vendor/ui-date-master/src/date.js', array('jquery-ui-datepicker-pt-BR'));

    $app->hook('mapasculturais.scripts', function() use($app){
        $def = $app->getRegisteredMetadataByMetakey('classificacaoEtaria', 'MapasCulturais\Entities\Event');

        ?>
<script type="text/javascript">
MapasCulturais.classificacoesEtarias = <?php echo json_encode(array_values($def->config['options'])); ?>;
</script>
        <?php
    });


?>


<?php add_map_assets(); ?>
    <div id="infobox" style="display:block" ng-show="data.global.openEntity.id>0 && data.global.viewMode==='map'">
        <a class="icone icon_close" ng-click="data.global.openEntity=null"></a>

        <article class="objeto agente clearfix" ng-if="openEntity.agent">
            <h1><a href="{{openEntity.agent.singleUrl}}">{{openEntity.agent.name}}</a></h1>
            <img class="objeto-thumb" ng-src="{{openEntity.agent['@files:avatar.avatarBig'].url||defaultImageURL}}">
            <p class="objeto-resumo">{{openEntity.agent.shortDescription}}</p>
            <div class="objeto-meta">
                <div><span class="label">Tipo:</span> <a href="#">{{openEntity.agent.type.name}}</a></div>
                <div>
                    <span class="label">Áreas de atuação:</span>
                        <span ng-repeat="area in openEntity.agent.terms.area">
                            <a href="#">{{area}}</a>{{$last ? '' : ', '}}
                        </span>
                </div>
            </div>
        </article>

        <article class="objeto espaco clearfix" ng-if="openEntity.space">
            <article class="objeto espaco clearfix">
                <h1><a href="{{openEntity.space.singleUrl}}">{{openEntity.space.name}}</a></h1>
                <div class="objeto-content clearfix">
                    <a href="{{openEntity.space.singleUrl}}" class="js-single-url">
                        <img class="objeto-thumb" ng-src="{{openEntity.space['@files:avatar.avatarBig'].url||defaultImageURL}}">
                    </a>
                    <p class="objeto-resumo">{{openEntity.space.shortDescription}}</p>
                    <div class="objeto-meta">
                        <div><span class="label">Tipo:</span> <a ng-click="data.space.types.push(openEntity.space.type.id)">{{openEntity.space.type.name}}</a></div>
                        <div>
                            <span class="label">Área de atuação:</span>
                            <span ng-repeat="area in openEntity.space.terms.area">
                                <a>{{area}}</a>{{$last ? '' : ', '}}
                            </span>
                        </div>
                        <div><span class="label">Local:</span>{{openEntity.space.metadata.endereco}}</div>
                        <div><span class="label">Acessibilidade:</span>{{openEntity.space.metadata.acessibilidade}}</div>
                    </div>
                </div>
            </article>
        </article>

        <div ng-if="openEntity.event">
            <p class="espaco-dos-eventos">Eventos encontrados em:<br>
                <a href="{{openEntity.event.space.singleUrl}}">{{openEntity.event.space.name}}<br>
                    {{openEntity.event.space.endereco}}</a></p>

            <article class="objeto evento clearfix" ng-repeat="event in openEntity.event.events">
                <h1><a href="{{event.singleUrl}}">{{event.name}}</a></h1>
                <div class="objeto-content clearfix">
                    <a href="{{event.singleUrl}}" class="js-single-url">
                        <img class="objeto-thumb" ng-src="{{event['@files:avatar.avatarBig'].url||defaultImageURL}}">
                    </a>
                    <p class="objeto-resumo">{{event.shortDescription}}</p>
                    <div class="objeto-meta">
                        <div>
                            <span class="label">Linguagem:</span>
                            <span ng-repeat="linguagem in event.terms.linguagem">
                                <a>{{linguagem}}</a>{{$last ? '' : ', '}}
                            </span>
                        </div>
                        <div ng-repeat="occ in event.readableOccurrences"><span class="label">Horário:</span> <time>{{occ}}</time></div>
                        <div><span class="label">Classificação:</span> livre</div>
                    </div>
                </div>
            </article>
        </div>
    </div>
    <div id="filtro-local" class="clearfix js-leaflet-control" data-leaflet-target=".leaflet-top.leaflet-left" ng-controller="SearchSpatialController" ng-show="data.global.viewMode ==='map'">
        <form id="form-local" method="post">
            <label for="proximo-a">Local: </label>
            <input id="endereco" ng-model="data.global.locationFilters.address.text" type="text" class="proximo-a" name="proximo-a" placeholder="Digite um endereço" />
            <!--<p class="mensagem-erro-proximo-a-mim mensagens">Não foi possível determinar sua localização. Digite seu endereço, bairro ou CEP </p>-->
            <input type="hidden" name="lat" />
            <input type="hidden" name="lng" />
        </form>
        <a id ="proximo-a-mim" class="control-infobox-open hltip botoes-do-mapa" ng-click="filterNeighborhood()" title="Buscar somente resultados próximos a mim."></a>
        <!--<a class="botao principal hltip" href="#" ng-click="drawCircle()" title="Buscar somente resultados em uma área delimitada">delimitar área</a>-->
    </div>
    <!--#filtro-local-->
    <div id="camadas-de-entidades" class="js-leaflet-control" data-leaflet-target=".leaflet-top.leaflet-right" ng-show="data.global.viewMode ==='map'">
        <a class="hltip botoes-do-mapa icone icon_calendar" ng-class="{active: data.global.enabled.event}" ng-click="data.global.enabled.event = !data.global.enabled.event" title="Mostrar eventos"></a>
        <a class="hltip botoes-do-mapa icone icon_profile"  ng-class="{active: data.global.enabled.agent}" ng-click="data.global.enabled.agent = !data.global.enabled.agent" title="Ocultar agentes"></a>
        <a class="hltip botoes-do-mapa icone icon_building" ng-class="{active: data.global.enabled.space}" ng-click="data.global.enabled.space = !data.global.enabled.space" title="Mostrar espaços"></a>
    </div>
    <div id="mapa" ng-controller="SearchMapController"  ng-show="data.global.viewMode!=='list'" ng-animate="{show:'animate-show', hide:'animate-hide'}" class="js-map" data-options='{"dragging":true, "zoomControl":true, "doubleClickZoom":true, "scrollWheelZoom":true }'>
    </div>
    <div id="lista" ng-show="data.global.viewMode==='list'" ng-animate="{show:'animate-show', hide:'animate-hide'}">
        <header id="header-dos-projetos" class="header-do-objeto clearfix" ng-if="data.global.filterEntity == 'project'">
            <div class="clearfix">
                <h1><span class="icone icon_document_alt"></span> Projetos</h1>
                <a class="botao adicionar" href="<?php echo $app->createUrl('project', 'create') ?>">Adicionar projeto</a>
                <a class="icone arrow_carrot-down" href="#"></a>
            </div>
        </header>
        <div id="lista-dos-projetos" class="lista" infinite-scroll="addMore('agent')" ng-if="data.global.filterEntity == 'project'">
            <article class="objeto projeto clearfix"  ng-repeat="project in projects" id="agent-result-{{project.id}}">
                <h1><a href="{{project.singleUrl}}">{{project.name}}</a></h1>
                <div class="objeto-content clearfix">
                    <a href="{{project.singleUrl}}" class="js-single-url">
                        <img class="objeto-thumb" ng-src="{{project['@files:avatar.avatarBig'].url||defaultImageURL}}">
                    </a>
                    <p class="objeto-resumo">
                        {{project.shortDescription}}
                    </p>
                    <div class="objeto-meta">
                        <div><span class="label">Tipo:</span> <a href="#">{{project.type.name}}</a></div>
                        <div>
                            <span class="label">Linguagens:</span>
                            <span ng-repeat="linguagem in project.terms.linguagens">
                                <a href="#">{{linguagem}}</a>{{$last ? '' : ', '}}
                            </span>
                            <div><span class="label">Inscrições:</span> 00/00/00 - 00/00/00</div>
                        </div>
                    </div>
                </div>
            </article>
            <!--.objeto-->
        </div>

        <header id="header-dos-agentes" class="header-do-objeto clearfix" ng-if="data.global.filterEntity == 'agent'">
            <h1><span class="icone icon_profile"></span> Agentes</h1>
            <a class="botao adicionar" href="<?php echo $app->createUrl('agent', 'create'); ?>">Adicionar agente</a>
            <a class="icone arrow_carrot-down" href="#"></a>
        </header>

        <div id="lista-dos-agentes" class="lista" infinite-scroll="addMore('agent')" ng-if="data.global.filterEntity == 'agent'">
            <article class="objeto agente clearfix" ng-repeat="agent in agents" id="agent-result-{{agent.id}}">
                <h1><a href="{{agent.singleUrl}}">{{agent.name}}</a></h1>
                <div class="objeto-content clearfix">
                    <a href="{{agent.singleUrl}}" class="js-single-url">
                        <img class="objeto-thumb" ng-src="{{agent['@files:avatar.avatarBig'].url||defaultImageURL}}">
                    </a>
                    <p class="objeto-resumo">{{agent.shortDescription}}</p>
                    <div class="objeto-meta">
                        <div><span class="label">Tipo:</span> <a href="#">{{agent.type.name}}</a></div>
                        <div>
                            <span class="label">Área de atuação:</span>
                            <span ng-repeat="area in agent.terms.area">
                                <a href="#">{{area}}</a>{{$last ? '' : ', '}}
                            </span>
                        </div>
                    </div>
                </div>
            </article>
        </div>
        <header id="header-dos-espacos" class="header-do-objeto clearfix" ng-if="data.global.filterEntity == 'space'">
            <h1><span class="icone icon_building"></span> Espaços</h1>
            <a class="botao adicionar" href="<?php echo $app->createUrl('space', 'create'); ?>">Adicionar espaço</a>
            <a class="icone arrow_carrot-down" href="#"></a>
        </header>
        <div id="lista-dos-espacos" class="lista" infinite-scroll="addMore('space')" ng-if="data.global.filterEntity == 'space'">
            <article class="objeto espaco clearfix" ng-repeat="space in spaces" id="space-result-{{space.id}}">
                <h1><a href="{{space.singleUrl}}">{{space.name}}</a></h1>
                <div class="objeto-content clearfix">
                    <a href="{{agent.singleUrl}}" class="js-single-url">
                        <img class="objeto-thumb" ng-src="{{space['@files:avatar.avatarBig'].url||defaultImageURL}}">
                    </a>
                    <p class="objeto-resumo">{{space.shortDescription}}</p>
                    <div class="objeto-meta">
                        <div><span class="label">Tipo:</span> <a href="#">{{space.type.name}}</a></div>
                        <div>
                            <span class="label">Área de atuação:</span>
                            <span ng-repeat="area in space.terms.area">
                                <a href="#">{{area}}</a>{{$last ? '' : ', '}}
                            </span>
                        </div>
                        <div><span class="label">Local:</span>{{space.metadata.endereco}}</div>
                        <div><span class="label">Acessibilidade:</span>{{space.metadata.acessibilidade}}</div>
                    </div>
                </div>
            </article>
        </div>
        <header id="header-dos-eventos" class="header-do-objeto clearfix"  ng-if="data.global.filterEntity == 'event'">
            <h1><span class="icone icon_calendar"></span> Eventos</h1>
            <a class="botao adicionar" href="<?php echo $app->createUrl('event', 'create'); ?>">Adicionar evento</a>
            <a class="icone arrow_carrot-down" href="#"></a>
        </header>
        
        <div id="lista-dos-eventos" class="lista" infinite-scroll="addMore('event')" ng-show="data.global.filterEntity == 'event'">
            <article class="objeto evento clearfix" ng-repeat="event in events">
                <h1><a href="{{event.singleUrl}}">{{event.name}}</a></h1>
                <div class="objeto-content clearfix">
                    <a href="{{event.singleUrl}}" class="js-single-url">
                        <img class="objeto-thumb" ng-src="{{event['@files:avatar.avatarBig'].url||defaultImageURL}}">
                    </a>
                    <p class="objeto-resumo">{{event.shortDescription}}</p>
                    <div class="objeto-meta">
                        <div>
                            <span class="label">Linguagem:</span>
                            <span ng-repeat="linguagem in event.terms.linguagem">
                                <a>{{linguagem}}</a>{{$last ? '' : ', '}}
                            </span>
                        </div>
                        <div ng-repeat="occ in event.readableOccurrences"><span class="label">Horário:</span> <time>{{occ}}</time></div>
                        <div><span class="label">Classificação:</span> livre</div>
                    </div>
                </div>
            </article>
        </div>
    </div>