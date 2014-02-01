function assigned_user(item) {
	$('.assigned-users').html('<a href="/author/' + item.data.user_login + '">' + item.data.display_name + '</a>');
}
function edit_assigned_users() {
	$('.assigned-users').prepend('<a href="#" class="btn btn-secondary">Edit</a>');
}

jQuery(document).ready(function ($) {

	var update = '.assigned-users';
	$(update).html('<img src="/wp-admin/images/wpspin_light.gif">');
	var post_id = $('body').data('id');

	jQuery.ajax({

		type: 'POST',

		url: control.ajaxurl,

		data: {
			action: 'get_current_user'
		},

		dataType: 'json',

		success: function(data, textStatus, XMLHttpRequest) {
			if( data.current_user.allcaps.edit_posts === true ) {
				edit_assigned_users();
			}
		},

		error: function (MLHttpRequest, textStatus, errorThrown) {
			console.log(MLHttpRequest);
			console.log(textStatus);
			console.log(errorThrown);
		}
	});

	jQuery.ajax({

		type: 'POST',

		url: control.ajaxurl,

		data: {
			action: 'get_assigned_users',
			post_id: post_id
		},

		dataType: 'json',

		success: function(data, textStatus, XMLHttpRequest) {
			$.each(data.users, function(i, item) {
				assigned_user(item);
			});
		},

		error: function (MLHttpRequest, textStatus, errorThrown) {
			console.log(MLHttpRequest);
			console.log(textStatus);
			console.log(errorThrown);
		}
	});

});