/* ========================================================================
 * DOM-based Routing
 * Based on http://goo.gl/EUTi53 by Paul Irish
 *
 * Only fires on body classes that match. If a body class contains a dash,
 * replace the dash with an underscore when adding it to the object below.
 *
 * .noConflict()
 * The routing is enclosed within an anonymous function so that you can
 * always reference jQuery with $, even when in .noConflict() mode.
 *
 * Google CDN, Latest jQuery
 * To use the default WordPress version of jQuery, go to lib/config.php and
 * remove or comment out: add_theme_support('jquery-cdn');
 * ======================================================================== */

(function ($) {

	// Use this variable to set up the common and page specific functions. If you 
	// rename this variable, you will also need to rename the namespace below.
	var Control = {
		// All pages
		common: {
			init: function () {
				// JavaScript to be fired on all pages
				$(document).on('nervetask-new-task', nervetaskNewTaskHandler);
				$(document).on('nervetask-update-assignees', nervetaskUpdateAssigneesHandler);
				$(document).on('nervetask-update-status', nervetaskUpdateStatusHandler);
				$(document).on('nervetask-update-priority', nervetaskUpdatePriorityHandler);
				$(document).on('nervetask-update-category', nervetaskUpdateCategoryHandler);
				$(document).on('nervetask-update-tags', nervetaskUpdateTagsHandler);
				
				$('select.nervetask-update-tags').chosen({
					create_option: true,
					persistent_create_option: true,
					skip_no_results: true,
					width: '95%'
				});

				$('.chosen-select').chosen({
					width: '95%'
				});

			}
		},
		// My Tasks template
		page_template_templatesmy_tasks_php: {
			init: function () {
				var user = $('#userid').data('userid');
				
				// JavaScript to be fired on the home page
				$('.table-tasks').dataTable({
					'bProcessing': true,
					'bServerSide': true,
					'sAjaxSource': control.ajaxURL + '?action=get_user_tasks&user='+ user,
					'iColumns': 5,
					'aaSorting': [[5, "desc"]]
				});
			}
		},
		// Author template
		author: {
			init: function () {
				var user = $('#userid').data('userid');
				
				// JavaScript to be fired on the home page
				$('.table-tasks').dataTable({
					'bProcessing': true,
					'bServerSide': true,
					'sAjaxSource': control.ajaxURL + '?action=get_user_tasks&user='+ user,
					'iColumns': 5,
					'aaSorting': [[5, "desc"]]
				});
			}
		},
		// About us page, note the change from about-us to about_us.
		post_type_archive_nervetask: {
			init: function () {
				// JavaScript to be fired on the 'nervetask' archive
				$('.table-tasks').dataTable({
					'iDisplayLength': 25,
					'aLengthMenu': [[25, 50, 100], [25, 50, 100]],
					'bProcessing': true,
					'bServerSide': true,
					'sAjaxSource': control.ajaxURL + '?action=get_tasks',
					'iColumns': 5,
					'aaSorting': [[5, "desc"]]
				});
			}
		},
		// Singular task
		single_nervetask: {
			init: function () {
				$('#task-update-content').on('hide.bs.collapse', function () {
					$('.nervetask-update-content .static-content').show();
				});
				$('#task-update-content').on('show.bs.collapse', function () {
					$('.nervetask-update-content .static-content').hide();
				});
			}
		}
	};

	// The routing fires all common scripts, followed by the page specific scripts.
	// Add additional events for more control over timing e.g. a finalize event
	var UTIL = {
		fire: function (func, funcname, args) {
			var namespace = Control;
			funcname = (funcname === undefined) ? 'init' : funcname;
			if (func !== '' && namespace[func] && typeof namespace[func][funcname] === 'function') {
				namespace[func][funcname](args);
			}
		},
		loadEvents: function () {
			UTIL.fire('common');

			$.each(document.body.className.replace(/-/g, '_').split(/\s+/), function (i, classnm) {
				UTIL.fire(classnm);
			});
		}
	};

	$(document).ready(UTIL.loadEvents);

	function nervetaskNewTaskHandler(e) {

		$('#new-task-user-feedback').append(function () {
			console.log(e);
			var output;
			if( e.message.status === 'success' ) {
				output = '<div class="alert alert-success"><strong><a href="' + e.message.post.guid + '">' + e.message.post.post_title + '</a></strong> created successfully</div>';
			} else {
				output = '<div class="alert alert-warning">'+ e.message +'</div>';
			}
			return output;
		});

	}

	function nervetaskUpdateAssigneesHandler(e) {

		var output = '';
		$('.assigned').empty();

		output = $(e.message.users).map(function () {
			return '<a href="?author=' + this.data.ID + '">' + this.data.display_name + '</a>';
		}).get().join(',');
		
		$('#task-meta-assignees-options').collapse('hide');

		$('.assigned').html(output);
		
		nervetaskComment(e.message.comment);

	}

	function nervetaskUpdateStatusHandler(e) {
		console.log(e);

		var output, status = '';
		$('.task-status').empty();

		output = $(e.message.terms).map(function () {
			// TODO: this could be a lot more efficient and failproof
			$('.task-sidebar-status.nervetask-status').attr('class', '').addClass('task-sidebar-status nervetask-status nervetask-status-' + this.slug );
			return '<a href="?nervetask_status=' + this.slug + '">' + this.name + '</a>';
		}).get().join(',');

		status = $(e.message.terms).map(function () {
			return 'nervetask-status-' + this.slug;
		}).get().join(' ');
		
		$('#task-meta-status-options').collapse('hide');

		$('.task-status').html(output);

		nervetaskComment(e.message.comment, status);

	}

	function nervetaskUpdatePriorityHandler(e) {

		var output = '';
		$('.task-priority').empty();

		output = $(e.message.terms).map(function () {
			return '<a href="?nervetask_priority=' + this.slug + '">' + this.name + '</a>';
		}).get().join(',');
		
		$('#task-meta-priority-options').collapse('hide');

		$('.task-priority').html(output);
		
		nervetaskComment(e.message.comment);

	}

	function nervetaskUpdateCategoryHandler(e) {

		var output = '';
		$('.task-category').empty();

		output = $(e.message.terms).map(function () {
			return '<a href="?nervetask_category=' + this.slug + '">' + this.name + '</a>';
		}).get().join(',');
		
		$('#task-meta-category-options').collapse('hide');

		$('.task-category').html(output);
		
		nervetaskComment(e.message.comment);

	}
	
	function nervetaskUpdateTagsHandler(e) {

		var output = '';
		$('.task-tags').empty();

		output = $(e.message.terms).map(function () {
			return '<a href="?nervetask_tags=' + this.slug + '">' + this.name + '</a>';
		}).get().join(',');
		
		$('#task-meta-tags-options').collapse('hide');

		$('.task-tags').html(output);
		
		nervetaskComment(e.message.comment);

	}
	
	function nervetaskComment(data, status) {
		console.log(data);
		var output;
		
		output = '<li class="status '+ status + '">';
		output += '<div class="comment-body">';
		output += '<div class="comment-meta commentmetadata">'+ moment(data[0].comment_date).format('MMMM Do YYYY') +' at '+ moment(data[0].comment_date).format('h:mma') +'</div>';
		output += '<div class="comment-author vcard"><cite class="fn">'+ data[0].comment_author +'</cite></div>';
		output += '<div class="comment-text"><p>'+ data[0].comment_content +'</p></div>';
		output += '</div>';
		output += '</li>';
		
		$(output).hide().appendTo('.comment-list').show('fast');

	}

})(jQuery); // Fully reference jQuery after this point.