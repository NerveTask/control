(function ( $ ) {
	"use strict";

	$(function () {

		$(document).on('nervetask-new-task', nervetaskNewTaskHandler );
		$(document).on('nervetask-update-assignees', nervetaskUpdateAssigneesHandler );
		$(document).on('nervetask-update-status', nervetaskUpdateStatusHandler );
		$(document).on('nervetask-update-priority', nervetaskUpdatePriorityHandler );
		$(document).on('nervetask-update-category', nervetaskUpdateCategoryHandler );

	});

	function nervetaskNewTaskHandler(e) {

		$('.new-task-list').append( function() {
			var output = '<li><a href="' + e.message.post.guid + '">' + e.message.post.post_title + '</a></li>';
			return output;
		});

	}

	function nervetaskUpdateAssigneesHandler(e) {

		var output = '';
		$('.assigned').empty();

		output = $(e.message.users).map(function(){
			return '<a href="?author='+ this.data.ID +'">'+ this.data.display_name +'</a>';
		}).get().join(',');

		$('.assigned').html( output );

	}

	function nervetaskUpdateStatusHandler(e) {

		var output = '';
		$('.task-status').empty();

		output = $(e.message.terms).map(function(){
			return '<a href="?nervetask_status='+ this.slug +'">'+ this.name +'</a>';
		}).get().join(',');

		$('.task-status').html( output );

	}

	function nervetaskUpdatePriorityHandler(e) {

		var output = '';
		$('.task-priority').empty();

		output = $(e.message.terms).map(function(){
			return '<a href="?nervetask_priority='+ this.slug +'">'+ this.name +'</a>';
		}).get().join(',');

		$('.task-priority').html( output );

	}

	function nervetaskUpdateCategoryHandler(e) {

		var output = '';
		$('.task-category').empty();

		output = $(e.message.terms).map(function(){
			return '<a href="?nervetask_category='+ this.slug +'">'+ this.name +'</a>';
		}).get().join(',');

		$('.task-category').html( output );

	}

}(jQuery));