jQuery( document ).ready(function( $ ) {
	if(jQuery('.hasTooltip').length) {
		jQuery('.hasTooltip').tooltip({"html": true,"container": "body"});
	}
	
	//$(this).find('.btn').toggleClass('active');
	// if ($(this).find('.btn-primary').size()>0) {
		// $(this).find('.btn').toggleClass('btn-primary');
	// }
	$(this).find('.btn').toggleClass('btn-default');
	
	$("input[name='new_site']").blur(function() {
		var input = $(this);
		var val = input.val();
		if (val && !val.match(/^http([s]?):\/\/.*/)) {
			input.val('http://' + val);
		}
	});
	
	$( "#chronoform-mainPage" ).submit(function( event ) {
		event.preventDefault(); // Stop form from submitting normally
		
		var form = $( this ),
		term = form.find( "input[name='new_site']" ).val(),
		url = '/add-new-site';
		if(term.length) {
			form.find( "input[name='new_site']" ).removeAttr('required');
			showLoading();
			
			$.post(url, { new_site: term })
				.done(function( data ) {
					// var content = $( data ).find( "#tryit" );
					// $( "#tryit" ).append( data );
					// console.log(data);
				})
				.fail(function(err) {
					console.log(err);
				})
				.always(function() {
					hideLoading();
					// console.log('show always');
				}
			);
		} else if(!term.length) {
			form.find( "input[name='new_site']" ).attr('required', '');
			form.find( "input[name='new_site']" ).focus();
			return false;
		}
	});
	
	function showLoading() {
		if(!$('#appsloading').length) {
			$('body').append('<div id="appsloading"><p>Процесс обработки данных может длиться до 5 минут.<br />Для успешного завершения просим не обновлять страницу.</p></div>');
			$('#appsloading').css('height', $( window ).height()).css('width', $( window ).width()).show();
		} else {
			$('#appsloading').css('height', $( window ).height()).css('width', $( window ).width()).show();
		}
	}
	function hideLoading() {
		$('#appsloading').hide();
	}
	
	$('a[href="#login-formhidden"]').fancybox();
	$('a[href="#chronoform-addSite"]').fancybox();
	
	
	var site = {
		remove: function (siteId) {
			var acceptRemoving = confirm('Вы действительно хотите удалить этот сайт?');
			if(acceptRemoving) {
				showLoading();
				var posting = $.post( "/hide-from-list", { site_id: siteId } );
				posting.done(function(data) {
					console.log('success');
					console.log(data);
					location.reload(true)
				});
				posting.fail(function(error) {
					console.log(error);
				});
				posting.always(function() {
					hideLoading();
				});
			}
		},
		duplicate: function (siteId) {
			showLoading();
			var posting = $.post( "/duplicate", { site_id: siteId } );
			posting.done(function(data) {
				console.log('success');
				location.reload(true);
			});
			posting.fail(function(error) {
				console.log(error);
			});
			posting.always(function() {
				hideLoading();
			});
		},
		publish: function(siteId, publish) {
			showLoading();
			var posting = $.post( "/publish", { site_id: siteId, published: publish } );
			posting.done(function(data) {
				console.log('success');
				console.log(data);
			});
			posting.fail(function(error) {
				console.log(error);
			});
			posting.always(function() {
				hideLoading();
			});
		}
	};
	
	$('a[href="#duplicate"]').click(function() {
		site.duplicate($(this).attr('data-id'));
		return false;
	});
	
	$('a[href="#remove"]').click(function() {
		site.remove($(this).attr('data-id'));
		return false;
	});
	
	$('a[href="#publish"]').click(function() {
		site.publish($(this).attr('data-id'), $(this).attr('data-cond'));
		return false;
	});
	
	$('#model a').click(function() {
		$('#site_view').removeAttr('style').removeAttr('class').addClass($(this).attr('data-model'));
		$('#site_view > div').removeAttr('style').removeAttr('id').attr('id', ($(this).attr('data-model')));
		$(this).parent().find('.active').removeClass('active');
		$(this).addClass('active');
		
		return false;
	});
	$('#rotate a').click(function() {
		if($(this).parent().find('.active').length) {
			$(this).removeClass('active');
		} else {
			$(this).addClass('active');
		}
		w = $('#site_view > div').css('width');
		h = $('#site_view > div').css('height');
		$('#site_view > div').css('width', h);
		$('#site_view > div').css('height', w);
		w = $('#site_view').css('width');
		h = $('#site_view').css('height');
		$('#site_view').css('width', h);
		$('#site_view').css('height', w);
		
		
		return false;
	});
});