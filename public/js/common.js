$(document).ready(function() {

    var isWider = $( '.wider' );
    isWider.next( '.container' ).addClass( 'push-down' );

    if(isWider.length) {
        $( window ).scroll(function() {

            var tp = $( 'html' ).scrollTop();

            if(tp > 50) {

                $( '.navbar' ).removeClass( 'wider') ;
            }
            else if(tp < 50) {
        
                $( '.navbar' ).addClass( 'wider') ;
            }
        }); 
    }
    
    var hloc = window.location.href;
    if(hloc.match('#')){

        var jumpLoc = $( '#' + hloc.split("#")[1] ).offset().top - 105;

        $("html, body").animate({scrollTop: jumpLoc}, 1000);
    }

    $( '.navbar-nav li a').on('click', function(event){

        // event.preventDefault();

        var jumpLoc = $( '#' + $( this ).attr( "href" ).split('#')[1] ).offset().top - 105;

        $("html, body").animate({scrollTop: jumpLoc}, 1000);
    });

    $(".TOCtoggle").click(function(){

        var divID = "#toc-" + $(this).attr('data-name'); 
        $(divID).slideToggle(1, function(){

            buildMasonry();
           
        });
    });  

    $('#toggleSearchType input').on('change', function(){

			var actionUrl = $('#searchForm form').attr('action');

           actionUrl = ($(this).is(":checked")) ? actionUrl.replace('field', 'fulltext') : actionUrl.replace('fulltext', 'field');

           $('#searchForm form').attr('action', actionUrl);
    });

    // $( '.email-submit' ).on('click', function(event){

    //     event.preventDefault();
    //     alert('This facility will be made available shortly. Till then please write to us as heritage@iitm.ac.in');
    // });
});


// Masonry layout

jQuery(window).load(function () {



    // Takes the gutter width from the bottom margin of .post

    var gutter = parseInt(jQuery('.post').css('marginBottom'));
    var container = jQuery('#posts');

    // Creates an instance of Masonry on #posts
	if(container.length > 0){
		
		container.masonry({
			gutter: gutter,
			itemSelector: '.post',
			columnWidth: '.post'
		});
    }
    
    // This code fires every time a user resizes the screen and only affects .post elements
    // whose parent class isn't .container. Triggers resize first so nothing looks weird.
    
    jQuery(window).bind('resize', buildMasonry()).trigger('resize');

    if(document.getElementById('viewletterimages')){
        
        var viewer = new Viewer(document.getElementById('viewletterimages'), {url: 'data-original'});
    }

    if(document.getElementById('viewCardImages')){
        
        var viewer = new Viewer(document.getElementById('viewCardImages'), {url: 'data-original'});
    }

    if(document.getElementById('transcribeimages')){
        
        var viewer = new Viewer(document.getElementById('transcribeimages'), {url: 'data-original', inline: true, minHeight: $( window ).height()});
    }
});

function buildMasonry(){

    var gutter = parseInt(jQuery('.post').css('marginBottom'));
    var container = jQuery('#posts');


    // Creates an instance of Masonry on #posts

    container.masonry({
        gutter: gutter,
        itemSelector: '.post',
        columnWidth: '.post',
        fitWidth: true
    });

    if (!jQuery('#posts').parent().hasClass('container')) {
    
        // Resets all widths to 'auto' to sterilize calculations
        
        post_width = jQuery('.post').width() + gutter;
        jQuery('#posts, body > #grid').css('width', 'auto');
        
        // Calculates how many .post elements will actually fit per row. Could this code be cleaner?
        
        posts_per_row = jQuery('#posts').innerWidth() / post_width;

        floor_posts_width = (Math.floor(posts_per_row) * post_width) - gutter;
        ceil_posts_width = (Math.ceil(posts_per_row) * post_width) - gutter;
        posts_width = (ceil_posts_width > jQuery('#posts').innerWidth()) ? floor_posts_width : ceil_posts_width;
        if (posts_width == jQuery('.post').width()) {
            posts_width = '100%';
        }
        

        
        // Ensures that all top-level elements have equal width and stay centered
        
        jQuery('#posts, #grid').css('width', '1325px');
        // jQuery('#posts').css({'margin-left': '-20px'});  
    }
}

function getresult(url) {

    $('#grid').attr('data-go', '0');
    $.ajax({
        url: url,
        type: "GET",
        
        beforeSend: function(){

            $('#loader-icon').show();
        },
        success: function(data){
            
            $('#grid').attr('data-go', '0');

            if(data == "\"noData\"") {

                $('#grid').append('<div id="no-more-icon">No more<br />items<br />to show</div>');
                $('#loader-icon').hide();
                return;
            }

            buildMasonryFromJson(data);
        },
        error: function(){console.log("Fail");}
  });
}

function buildMasonryFromJson(json){

    // This requires the following properties in the json file
    // albumID, randomImagePath, leafCount, field

    var gutter = parseInt(jQuery('.post').css('marginBottom'));
    var $grid = $('#posts').masonry({
        gutter: gutter,
        // specify itemSelector so stamps do get laid out
        itemSelector: '.post',
        columnWidth: '.post',
    });

    var obj = JSON.parse(json);
    var displayString = "";
    
    var aux = (obj.auxiliary === undefined) ? '' : obj.auxiliary;
    var filterString = (aux.filterString === undefined) ? '' : aux.filterString;

    for(i = 0; i < Object.keys(obj).length - 1; i++) {

        if (obj[i].id === undefined) {
            
            // This snippet id for listing of categories

            displayString += '<div class="post">';
            displayString += '<a href="' + obj[i].nextURL + '" title="View Album" target="_blank">';
            displayString += '<div class="fixOverlayDiv">';
            displayString += '<img class="img-responsive" src="' + obj[i].thumbnailPath + '">';
            displayString += '<div class="OverlayText">' + obj[i].leafCount + ' ' + obj[i].parentType;
            // Due to lack of better methods, plural is handled here itself
            if(obj[i].leafCount > 1) displayString += 's'; 
            displayString +=  '<br /><span class="link"><i class="fa fa-link"></i></span></div>';
            displayString += '</div>';
            displayString += '<p class="image-desc"><strong>' + obj[i].name + '</strong></p>';
            displayString += '</a>';
            displayString += '</div>';
        }
        else{

            // This snippet is for listing of artefacts

            displayString += '<div class="post">';
            displayString += '<a href="' + base_url + 'describe/artefact/' + obj[i].idURL + '?' + aux.filterString + '" title="View Details" target="_blank">';
            displayString += '<img class="img-responsive" src="' +  obj[i].thumbnailPath + '">';
            displayString += '</a>';
            displayString += '<p class="image-desc">' + obj[i].cardName + '</p>';
            displayString += '</div>';
        }
    }

    var $content = $(displayString);
    $content.css('display','none');

    $grid.append($content).imagesLoaded().done(
        function(){
            $content.fadeIn(500);
            $grid.masonry('appended', $content);
            $('#loader-icon').hide();

            $('#grid').attr('data-go', '1');
        }
    );

    displayString = "";
}
