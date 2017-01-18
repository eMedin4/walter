
$( document ).ready(function() {

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});


/************************************************************************************************
    SEARCH
************************************************************************************************/

$('.search-launch').on('click', function() {
	$('.search').addClass('visible');
	$('.input-search').focus();
});

$('.search .close').on('click', function() {
	$('.search').removeClass('visible');
});

$('.input-search').focusout(function() {
    $('.search-results, .search-results-wrap').fadeOut(300);
});			

$('.input-search').focusin(function() {
    //para que solo aparezcan si hay resultados
    if ($('.search-item').length && $(this).length) {
        $('.search-results, .search-results-wrap').fadeIn(300);
    }
});

$('.input-search').bind('paste keyup', function() {
    var t = $(this);
    var string = t.val();
    var ilength = string.length;
    var url = t.data('url'); 
    var path = t.data('path');
    if (ilength > 3) {  
        $.ajax({
            url: url,
            type: 'POST',
            data: { 'string': string }
        }).done(function(data) {
            if (data.response == true) { /*si hay resultados*/
                $('.search-results, .search-results-wrap').fadeIn(300).html('');
                $.each(data.result, function(key,val) {
                    if (val.check_poster) {
                        var fullImgPath = path + `assets/posters/small` + val.poster;
                    } else {
                        var fullImgPath = path + `assets/images/no-poster-small.png`;
                    }
                    var html = 
                    `<a class="search-item" href="` + path + val.slug + `"><img src="` + fullImgPath + `" width="30" height="45"> 
                        <p><span>` + val.title + `</span> ` + val.year + ` · ` + val.country + `</p>
                    </a>`;
                    $('.search-results').append(html);
                });
            } else {
                $('.search-results').html('');
            }
        }).fail(function() {
            console.log('fail');
        });
    } else { //si tiene menos de 3 carácteres
        $('.search-results').html('');
    }
});


/************************************************************************************************
    MODAL GENERIC
************************************************************************************************/

$('.modal-wrap').on('click', function () {
    $(this).fadeOut(500);
});

$('.modal .inner').on('click', function (e) {
    //hacemos que con el boton cancel si se cierre el modal
    if(!$(e.target).is('.propagation')){
      e.stopPropagation();
    }
});

$('.modal').on('change', '#check-description', function() {
    if($('#check-description').is(":checked"))   
        $(".modal textarea").fadeIn(300);
    else
        $(".modal textarea").hide();
});


/************************************************************************************************
    MODAL LOGIN
************************************************************************************************/

$('.js-launch-login').on('click', function() {
    var facebook = $('.single-wrap').data('facebook');
    var google = $('.single-wrap').data('google');
    var info = $(this).parent().parent().data('info');
    var html = 
        `<div class="login-modal panel-modal">
            <h3>Entra</h3>
            <h4>en Indicecine</h4>
            <a class="social-btn facebook" href="{{route('authsocial', ['provider' => 'facebook'])}}">
               <i class="fa fa-facebook-fa" aria-hidden="true"></i>
               <span>Entra con Facebook</span>
            </a>
            <a class="social-btn google" href="{{route('authsocial', ['provider' => 'google'])}}">
               <i class="fa fa-google" aria-hidden="true"></i>
               <span>Entra con Google</span>
            </a>
            <div class="oval-shape"></div>
            <p>` + info + `</p>
        </div>`;
    $('.modal .inner').html(html);
    $('.modal-wrap').fadeIn(500).css('display','table');
    $('.modal input').focus();
});


/************************************************************************************************
    MODAL NEW LIST
************************************************************************************************/

$('.js-new-list').on('click', function(){
    var t = $(this);
    var csrf = t.data('csrf');
    var movie = t.data('movie');
    var url = t.data('url');
    var html = 
        `<form method="POST" action="` + url + `" class="modal-new-list" data-movie="` + movie + `">
            <input type="hidden" name="_token" value="` + csrf + `">
            <h3>Nueva lista</h3>
            <div class="errors"></div>
            <input type="text" name="name" maxlength="24" placeholder="Nombre">           
            <textarea name="description" rows="3" maxlength="200" placeholder="Descripción"></textarea>
            <div class="btn-group">
                <button type="submit" class="btn">Crear</button>
                <button type="button" class="btn btn-cancel propagation">Cancelar</button>
            </div>
            <div class="checkbox">
                <input id="check-description" type="checkbox" name="check-description">
                <label class="lbl-check" for="check-description">Añadir descripción</label>
            </div>
            <div class="checkbox">
                <input id="check-ordered" type="checkbox" name="check-ordered">
                <label class="lbl-check" for="check-ordered">Lista numerada</label>
            </div>
        </form>`;
    $('.modal .inner').html(html);
    $('.modal-wrap').fadeIn(500).css('display','table');
    $('.modal input').focus();
});


/************************************************************************************************
    CREATE NEW LIST
************************************************************************************************/

$('.modal').on('submit', '.modal-new-list', function(e){
    e.preventDefault(e);
    var t = $(this);
    var url = t.attr('action');
    var movie = t.data('movie');
    var name = t.find('input[type="text"]').val();
    var description = t.find('textarea').val();
    var ordered = t.find('#check-ordered').is(":checked") ? 1 : 0;
    $.ajax({
        url: url,
        type: 'POST',
        data: { 'name': name, 'movie': movie, 'description': description, 'ordered': ordered }
    })
    .done(function(data) {
        if (!data.state) {
            t.find('.errors').text(data.message);
        } else {
            $('.modal-wrap').fadeOut(500);
            if (movie) { /*Si estamos añadiendo desde single movie será el id de la película, si añadimos desde user-lists movie será 0*/
                var html = '<li><span class="disable-add-list recent-list">' + data.name + '<i class="icon-check-list fa fa-check"></i></span></li>';  
                //para que funcione el efecto lo cargamos previamente
                var new_item = $(html).hide();
                $('.my-lists').append(new_item);
                new_item.show('slow');
            } else {
                //que hacer cuando añadimos desde user-lists
                var html= `<article class="new-grid"><a class="list" href="` + $('.js-new-list').data('path') + `lista/` + data.name + `/` + data.id + `">
                    <div class="meta"><span><span>No hay nada </span><i class="separator">·</i><span class="no-wrap"> Ahora</span></span></div>
                    <div class="list-image relative"><div class="loop-no-image"></div></div>
                    <div class="loop-title"><h3>` + data.name + `</h3></div></a></article>`;
                $('.loop').prepend(html);
            }
        }
    })
    .fail(function(data) {
        var parsed = $.parseJSON(data.responseText).name;
        t.find('.errors').text(parsed);
    });
});

/************************************************************************************************
    ADD MOVIE TO LIST
************************************************************************************************/

$('.js-add-list').one('click', function(){
    var t = $(this);
    var list = t.data('id');
    var name = t.data('name');
    var ordered = t.data('ordered');
    var movie= t.parent().parent().data('movie');
    var url = t.parent().parent().data('url');
    $.ajax({
        url: url,
        type: 'POST',
        data: { 'list': list, 'movie': movie, 'ordered': ordered }
    })
    .done(function(data) {
        t.removeClass('js-add-list').addClass('flash-disable-add-list');
        window.setTimeout(function(){t.addClass('disable-add-list');}, 300);
        t.html(name + '<i class="icon-check-list fa fa-check"></i>');
    })
    .fail(function(data) {
        console.log('error');
    });        
});


if($('body').is('.list-page')){

/************************************************************************************************
    SETTINGS RUBAXA SORTABLE
************************************************************************************************/

    var rubitems = document.getElementById('js-loop');
    var sortable = Sortable.create(rubitems, {
        disabled: true,
        animation: 150,
        handle: ".medium-image",
        chosenClass: "js-chosen",  // Clase mientras arrastramos
        filter: ".js-ignore-edit",
        onUpdate: function () { //reordenamos en la etiqueta order
            var i = 1;
            $('.order').each(function (index) {
                var t = $(this);
                var old = t.data('current');
                if (old > i) {
                    t.html(i + '<i class="icon-order-up fa fa-arrow-up-fi"></i>');
                } else if (old < i) {
                    t.html(i + '<i class="icon-order-down fa fa-arrow-down"></i>');
                } else {
                    t.html(i);
                }
                i++;
            });
        },
    });

/************************************************************************************************
    ACTIVAR MODO EDICIÓN
************************************************************************************************/

    $('.js-on-edit').on('click', function() {
        sortable.option('disabled', false);
        $('.info-default').fadeOut(300).promise().done(function(){
            $('.info-edit').fadeIn(300);
        });
        $('.loop article').addClass('article-edit');
        $('.medium-image').append('<i class="delete-movie fa fa-times"></i>')
        $('.movie').on('click', function(e) { //la película no es clickable en modo edit
            e.preventDefault();
        })
    });

/************************************************************************************************
    SALIR MODO EDICIÓN
************************************************************************************************/
    var offedit = function() {
        sortable.option('disabled', true);
        $('.info-edit').fadeOut(300).promise().done(function(){
            $('.info-default').fadeIn(300);
        });
        $('.loop article').removeClass('article-edit');
        $('.delete-movie').remove();
        $('.movie').unbind('click'); /*Recupera su acción por defecto (inhabilita preventdefault)*/            
    }
    $('.js-off-edit').on('click', function() {
        offedit();
    });

/************************************************************************************************
    EDITAR INFO
************************************************************************************************/

$('.js-edit-list').on('click', function(){
    var t = $(this);
    var name = $('.info-edit .name').text();
    var description = $('.info-edit .description').text();
    var order = t.data('order');
    var html = 
        `<form class="modal-edit-list">
            <h3>Editar lista</h3>
            <div class="errors"></div>
            <input type="text" name="name" maxlength="24" value="` + name + `">           
            <textarea name="description" rows="3" maxlength="200" placeholder="Descripción" ` + (description ? "style='display:block;'" : "")  + `>` + description + `</textarea>
            <div class="btn-group">
                <button type="submit" class="btn">Actualizar</button>
                <button type="button" class="btn btn-cancel">Cancelar</button>
            </div>
            <div class="checkbox">
                <input id="check-description" type="checkbox" name="check-description" ` + (description ? "checked" : "")  + `>
                <label class="lbl-check" for="check-description">Añadir descripción</label>
            </div>
            <div class="checkbox">
                <input id="check-ordered" type="checkbox" name="check-ordered" ` + (order == 1 ? "checked" : "")  + `>
                <label class="lbl-check" for="check-ordered">Lista numerada</label>
            </div>
        </form>`;
    $('.modal .inner').html(html);
    $('.modal-wrap').fadeIn(500).css('display','table');
    $('.modal input').focus();
});

$('.modal').on('submit', '.modal-edit-list', function(e){
    e.preventDefault(e);
    var name = $('input[name=name]').val();
    if ($('#check-description').is(":checked"))   
        var description = $('textarea[name=description]').val();
    else
        var description = "";
    $('.info-edit .name').text(name);
    $('.info-edit .description').text(description);
    $('.modal-wrap').fadeOut(500);
});

/************************************************************************************************
    VALIDAR EDICIÓN
************************************************************************************************/

$('.edit-submit').on('click', function() {
    var t = $(this);
    var url = t.data('url');
    var list = t.data('id');
    var title = $('.info-edit .name').text();
    var description = $('.info-edit .description').text();
    var movies = [];
        $('.movie').each(function (index) {
            var id = $(this).data('id');
            movies[index] = id;
        });
    $.ajax({
        url: url,
        type: 'POST',
        data: { 'list': list, 'movies': movies, 'title': title, 'description': description }
    })
    .done(function(data) {
        offedit();
        $('.original-name').text(title);
        //existia <h2>description</h2>? SI
        if ($('.original-description').length) {
            //existe ahora description?
            if (description) {
                $('.original-description').text(description);
            } else {
                $('.original-description').remove();
            }
        //existia <h2>description</h2>? NO
        } else {
            //existe ahora description?
            if (description) {
                $('.original-name').after('<h2 class="original-description">' + description + '</h2>');
            }
        }    
        $('.time').text('Actualizada ahora');
    })
    .fail(function(data) {
        console.log('error');
    });      

});

/************************************************************************************************
    BORRAR LISTA
************************************************************************************************/

$('.edit-delete').on('click', function() {
    var name = $(this).data('name');
    var html=`<div>Vas a borrar la lista</div>
        <h3>`+ name + `</h3>
        <div class="btn-group-alt">
            <span class="btn btn-cancel propagation">Cancelar</span>
            <span class="btn btn-alert edit-delete-confirm propagation">Borrar definitivamente</span>
        </div>`;
    $('.modal .inner').html(html);
    $('.modal-wrap').fadeIn(500).css('display','table');
});


$('.modal').on('click', '.edit-delete-confirm', function() {
    var id = $('.edit-delete').data('id');
    var url = $('.edit-delete').data('url');
    var redirect_url = $('.edit-delete').data('redirect');
    $.ajax({
        url: url,
        type: 'POST',
        data: { 'id': id }
    })
    .done(function(data) {
        console.log(data);
        window.location.href = redirect_url;
    })
    .fail(function(data) {
        console.log('error');
    });      
});

/************************************************************************************************
    BORRAR PELICULA
************************************************************************************************/

$('.medium-image').on('click', '.delete-movie', function() {
    var t = $(this);
    var name = t.siblings('.loop-image').attr('alt');
    var movieid = t.parent().parent().data('id');
    var html=`<div>Vas a eliminar de la lista la película</div>
        <h3>`+ name + `</h3>
        <div class="btn-group-alt">
            <span class="btn btn-cancel propagation">Cancelar</span>
            <span class="btn btn-alert edit-delete-movie-confirm propagation" data-movieid="` + movieid + `">Borrar</span>
        </div>`;
    $('.modal .inner').html(html);
    $('.modal-wrap').fadeIn(500).css('display','table');
});

$('.modal').on('click', '.edit-delete-movie-confirm', function() {
    var list = $('.edit-delete').data('id');
    var url = $('.edit-delete').data('url-movielist');
    var movie = $(this).data('movieid');
    $.ajax({
        url: url,
        type: 'POST',
        data: { 'list': list, 'movie': movie }
    })
    .done(function(data) {
        var selector = '.movie[data-id="' + movie + '"]';
        console.log(selector);
        $(selector).parent().remove();
    })
    .fail(function(data) {
        console.log('error');
    });      
});


/************************************************************************************************
    GUARDAR LISTA EN MIS LISTAS
************************************************************************************************/

$('.info').on('click', '.js-add-to-mylists', function(){
    var t = $(this);
    var list = t.data('id');
    var url = t.data('url');
    $.ajax({
        url: url,
        type: 'POST',
        data: { 'list': list }
    })
    .done(function(data) {
        t.removeClass('js-add-to-mylists').addClass('btn-success btn-single').html('¡Guardada en mis listas!');
    })
    .fail(function(data) {
        console.log('error');
    });        
});


/************************************************************************************************
    BORRAR LISTA DE MIS LISTAS
************************************************************************************************/

$('.info').on('click', '.js-del-from-mylists', function(){
    var t = $(this);
    var list = t.data('id');
    var url = t.data('url');
    $.ajax({
        url: url,
        type: 'POST',
        data: { 'list': list }
    })
    .done(function(data) {
        t.siblings('.btn-success').remove();
        t.removeClass('js-del-from-mylists btn-double').addClass('btn-success btn-single').text('¡Borrada de mis listas!');
    })
    .fail(function(data) {
        console.log('error');
    });        
});


}/*endif is .list-page*/

/************************************************************************************************
    SUMMARY
************************************************************************************************/

/*OCULTAR LISTAS DE REPARTO DEMASIADO LARGAS*/
/*$('.js-characters a:lt(10)').show();
$('.more').on('click', function() {
	$('.js-characters a').fadeIn();
	$(this).fadeOut();
});*/

/*MENU SUMMARY MOBILE*/
$('.summary-menu').on('click', '.launch-menu', function() {
    var t = $(this);
    var selector = '.' + t.data('launch');
    $('.summary-part').fadeOut(200);
    $(selector).fadeIn(200);
    $('.summary-menu .active').removeClass('active').addClass('launch-menu');
    t.removeClass('launch.menu').addClass('active');
});

});
