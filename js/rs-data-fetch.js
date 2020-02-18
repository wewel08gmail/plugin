jQuery(function($){
		 
	
		 var $varselect = $(".js-multiple-category").select2({
		   templateResult: formatSelect
		 });
		 var $eventSelect = $(".js-multiple-category");
		  //select event
		  $eventSelect.on("select2:select", function (e) { 
			    $varselect.select2("open");
			    //$('.select2-selection__rendered').find('[title='+e.params.data.text+']').css('background','#'+e.params.data.element.getAttribute('data-color'));
				$('.select2-results__option').each(function(event){
					$optionval = $(this);
					$('.select2-selection__choice').each(function(event){					    
						if($($optionval).text()==$(this).attr('title')){													
							console.log($($optionval).find('span').attr("data-color"));
							$(this).css({ 'background':'#'+$($optionval).find('span').attr("data-color"), 'color':'white', 'margin-bottom':'0px' });						 
						}
					});		
					//$(this).css('background','#'+e.params.data.element.getAttribute('data-color'));							
				});	
					
				if($(".page-viewed-tick").hasClass('wct-tickbox-ticked')){
					viewstat = 'unviewed';
				}else{
					viewstat = 'viewed';
				}
				$.ajax({
				  url: siteUrl+"/wp-content/plugins/rs_user_notes/rs_ajax_result.php",
				  data: {select: $(".js-multiple-category").val(), pid: $('.js-multiple-category').find('option').attr("data-page-id"), startdate: $("#start_date").val(), enddate: $("#end_date").val(), viewstatus: viewstat },				  
				  context: $("#rs-data-list"),
				}).done(function(data) {				 				  
				  $( this ).html( data );
				});
		  });

		  //unselect event
		  $eventSelect.on("select2:unselect", function (e) { 
			    $varselect.select2("open");
			    //$('.select2-selection__rendered').find('[title='+e.params.data.text+']').css('background','#'+e.params.data.element.getAttribute('data-color'));
				$('.select2-results__option').each(function(event){
					$optionval = $(this);
					$('.select2-selection__choice').each(function(event){					    
						if($($optionval).text()==$(this).attr('title')){													
							console.log($($optionval).find('span').attr("data-color"));
							$(this).css({ 'background':'#'+$($optionval).find('span').attr("data-color"), 'color':'white' });						 
						}

					});		
					//$(this).css('background','#'+e.params.data.element.getAttribute('data-color'));							
				});		
				
				if($(".page-viewed-tick").hasClass('wct-tickbox-ticked')){
					viewstat = 'unviewed';
				}else{
					viewstat = 'viewed';
				}
				$.ajax({
				  url: siteUrl+"/wp-content/plugins/rs_user_notes/rs_ajax_result.php",
				  data: {select: $(".js-multiple-category").val(), pid: $('.js-multiple-category').find('option').attr("data-page-id"), startdate: $("#start_date").val(), enddate: $("#end_date").val(), viewstatus: viewstat },				  
				  context: $("#rs-data-list"),
				}).done(function(data) {				 				  
				  $( this ).html( data );
				});
		  });
          
		  $(".start_date_wrapper, .end_date_wrapper").on("change", function() { 
				
				if($(".page-viewed-tick").hasClass('wct-tickbox-ticked')){
					viewstat = 'unviewed';
				}else{
					viewstat = 'viewed';
				}				
				$.ajax({
				  url: siteUrl+"/wp-content/plugins/rs_user_notes/rs_ajax_result.php",
				  data: {select: $(".js-multiple-category").val(), pid: $('.js-multiple-category').find('option').attr("data-page-id"), startdate: $("#start_date").val(), enddate: $("#end_date").val(), viewstatus: viewstat },				  
				  context: $("#rs-data-list"),
				}).done(function(data) {				 				  
				  $( this ).html( data );
				});
				
		  });
		  $(".page-viewed-tick").on("click", function(){
			    
				if($(this).hasClass('wct-tickbox-ticked')){
					viewstat = 'unviewed';
				}else{
					viewstat = 'viewed';
				}				
				$.ajax({
				  url: siteUrl+"/wp-content/plugins/rs_user_notes/rs_ajax_result.php",
				  data: {select: $(".js-multiple-category").val(), pid: $('.js-multiple-category').find('option').attr("data-page-id"), startdate: $("#start_date").val(), enddate: $("#end_date").val(), viewstatus: viewstat },				  
				  context: $("#rs-data-list"),
				}).done(function(data) {				 				  
				  $( this ).html( data );
				});
				
		  });
		  
	      function formatSelect (state) {
			  if (!state.id) { return state.text; }

			  var $state = $("<span data-color='"+state.element.getAttribute('data-color')+"' style='background:#"+state.element.getAttribute('data-color')+"; padding:5px; color:white; border-radius: 5px;'>"+state.text+"</span>");
			  return $state;

		  };

	
	
});

