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
	var Roots = {
		// All pages
		common: {
			init: function () {
				// JavaScript to be fired on all pages
				$(document).on('nervetask-new-task', nervetaskNewTaskHandler);
				$(document).on('nervetask-update-assignees', nervetaskUpdateAssigneesHandler);
				$(document).on('nervetask-update-status', nervetaskUpdateStatusHandler);
				$(document).on('nervetask-update-priority', nervetaskUpdatePriorityHandler);
				$(document).on('nervetask-update-category', nervetaskUpdateCategoryHandler);

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
					'aaSorting': [[5, "asc"]]
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
					'aaSorting': [[5, "asc"]]
				});
			}
		},
		// About us page, note the change from about-us to about_us.
		post_type_archive_nervetask: {
			init: function () {
				// JavaScript to be fired on the 'nervetask' archive
				$('.table-tasks').dataTable({
					'bProcessing': true,
					'bServerSide': true,
					'sAjaxSource': control.ajaxURL + '?action=get_tasks',
					'iColumns': 5,
					"aaSorting": [[5, "asc"]]
				});
			}
		}
	};

	// The routing fires all common scripts, followed by the page specific scripts.
	// Add additional events for more control over timing e.g. a finalize event
	var UTIL = {
		fire: function (func, funcname, args) {
			var namespace = Roots;
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

		$('.new-task-list').append(function () {
			var output = '<li><a href="' + e.message.post.guid + '">' + e.message.post.post_title + '</a></li>';
			return output;
		});

	}

	function nervetaskUpdateAssigneesHandler(e) {

		var output = '';
		$('.assigned').empty();

		output = $(e.message.users).map(function () {
			return '<a href="?author=' + this.data.ID + '">' + this.data.display_name + '</a>';
		}).get().join(',');

		$('.assigned').html(output);

	}

	function nervetaskUpdateStatusHandler(e) {

		var output = '';
		$('.task-status').empty();

		output = $(e.message.terms).map(function () {
			return '<a href="?nervetask_status=' + this.slug + '">' + this.name + '</a>';
		}).get().join(',');

		$('.task-status').html(output);

	}

	function nervetaskUpdatePriorityHandler(e) {

		var output = '';
		$('.task-priority').empty();

		output = $(e.message.terms).map(function () {
			return '<a href="?nervetask_priority=' + this.slug + '">' + this.name + '</a>';
		}).get().join(',');

		$('.task-priority').html(output);

	}

	function nervetaskUpdateCategoryHandler(e) {

		var output = '';
		$('.task-category').empty();

		output = $(e.message.terms).map(function () {
			return '<a href="?nervetask_category=' + this.slug + '">' + this.name + '</a>';
		}).get().join(',');

		$('.task-category').html(output);

	}

})(jQuery); // Fully reference jQuery after this point.