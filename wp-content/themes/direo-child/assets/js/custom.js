$ = jQuery;

$(document).ready( function () {
	var scammerModal = $('#submit_scammer_modal');
	var dieroNotice = $('.direo-message');
	var scammerSubmit = $('.add-scammer-submit');
	var scammerForm = $("#add-scammer");
	var scammerTable = $('#scammer-table');
	var searchLocationForm = $('#directorist-search-form-home');
	var searchLocationSubmit = $('#search-location-submit');
	var userItemForm = $('#add-edit-item');
	var userItemFormSubmit = $('#add-new-item-submit');
	var userItemModal = $('#add_new_item_modal');
    scammerTable.DataTable();

    $( ".add-new-scammer" ).click(function() {
    	dieroNotice.removeClass('success');
    	dieroNotice.removeClass('failed');
    	dieroNotice.text('');
    	scammerForm.trigger('reset');
		scammerModal.modal('show');
	});

	$( "#open-manage-item-modal" ).click(function() {
		$('#add_new_item_modal .modal-title').html('Add New Product');
		$('#add-edit-item .add-new-item-submit').text('Add Product');
		$('#add-edit-item #upload-featured-image').text('Upload Featured Image');
		$('#add-edit-item #upload-gallery-images').css('display', 'block');
		$('#add-edit-item #upload-gallery-images').text('Upload Gallery Images');

		$('#add-edit-item input[name="action"]').val('add');
		userItemModal.modal('show');
		userItemModal.css('z-index', '9999');
	});

	$( ".edit-user-item" ).click(function() {
		var productId 		= $(this).data('id');
		var productTitle 	= $(this).data('title');
		var productDesc 	= $(this).data('desc');
		var productCategory = $(this).data('cat').toString();
		var productImage 	= $(this).data('featured-image');
		var productImages 	= $(this).data('gallery-images');

		$('#add_new_item_modal .modal-title').html('Update Product');
		$('#add-edit-item .add-new-item-submit').html('Update');

		$('#add-edit-item input[name="action"]').val('edit');
		$('#add-edit-item input[name="product_id"]').val(productId);
		$('#add-edit-item input[name="product_title"]').val(productTitle);
		$('#add-edit-item textarea[name="product_description"]').val(productDesc);

		if (productCategory.indexOf(',') > -1) {
			var productCarArr = productCategory.split(',');
			$.each(productCarArr, function(index, value) { 
				$(`#add-edit-item :checkbox[value=${value}]`).prop("checked",true);
			});
		} else {
			$(`#add-edit-item :checkbox[value=${productCategory}]`).attr("checked", true);
		}

		$('#add-edit-item input[name="featured_image"]').val(productImage);
		$('#add-edit-item input[name="gallery_images"]').val(productImages);

		userItemModal.modal('show');
		userItemModal.css('z-index', '9999');
	});

	scammerForm.submit(function(e) {
	    e.preventDefault();
	    var formData = $(this).serialize();
	    direoAjaxAddScammer($(this), 'POST', 'json', { action: 'add_scammer', formData: formData });
	});

	searchLocationForm.submit(function(e) {
	    e.preventDefault();
	    var location = $('#address').val();
	    direoAjaxSearchLocation(location);
	});

	userItemForm.submit(function(e) {
	    e.preventDefault();
	    var formData = $(this).serialize();
	    direoAjaxAddUserItem($(this), 'POST', 'json', { action: 'add_user_item', formData: formData });
	});

	function direoAjaxAddScammer($this, type = 'POST', dataType = 'json', data = {}) {
	    $.ajax({
	        type: type,
	        dataType: dataType,
	        url: directorist.ajaxurl,
	        data: data,
	        beforeSend: function() {
	            scammerSubmit.prop('disabled', true);
	            scammerSubmit.html('Processing...');
	        },
	        success: function(response) {
	            scammerSubmit.prop('disabled', false);
	            scammerSubmit.html('Add Scammer');
	            if(response.status == 'success') {
	            	dieroNotice.addClass(response.status);
	            	dieroNotice.text(response.msg);
	            	$this.trigger('reset');
	            	setTimeout(function() {
				        scammerModal.modal('hide');
				    }, 2000);
	            } else {
	            	dieroNotice.addClass(response.status);
	            	dieroNotice.text(response.msg);
	            }
	        },
	        error: function(jqXHR, textStatus, errorThrown){
	            console.log(jqXHR + " :: " + textStatus + " :: " + errorThrown);
	        }
	    });
	}

	function direoAjaxSearchLocation(location) {
		var displayName = '', lat = '', lon = '';
		$.ajax({
	        type: 'POST',
	        dataType: 'json',
	        url: 'https://nominatim.openstreetmap.org/?q='+ location +'&format=json',
	        success: function(response) {
	        	if(response != '') {
	        		displayName = response[0].display_name;
		        	lat = response[0].lat;
		        	lon = response[0].lon;

		        	if(displayName != '' && lat != '' && lon != '') {
		        		window.location.replace( customScriptObj.site_url + "/maps/?location=" + displayName + "&manual_lat=" + lat + "&manual_lng=" + lon);
		        	} else {
		        		alert("There was an error. We were unable to fetch the location you requested.");
		        	}
	        	} else {
	        		alert("There was an error. We were unable to fetch the location you requested.");
	        	}
	        },
	        error: function(jqXHR, textStatus, errorThrown){
	        	alert("There was an error. We were unable to fetch the location you requested.");
	            console.log(jqXHR + " :: " + textStatus + " :: " + errorThrown);
	        }
	    });
	}

	$(document).on("click",".delete-user-item",function() {
		var postId = $(this).data('id');
		if(postId) {
	       	$.ajax({
		        type: 'POST',
		        dataType: 'json',
		        url: directorist.ajaxurl,
		        data: { action: 'delete_user_item', post: postId },
		        success: function(response) {
		        	if(response.status != 'failed') {
			            alert(response.msg);
			            location.reload();
		        	} else {
		        		alert(response.msg);
		        	}
		        },
		        error: function(jqXHR, textStatus, errorThrown){
		            console.log(jqXHR + " :: " + textStatus + " :: " + errorThrown);
		        }
		    });
		} else {
			alert('Something went wrong. Please try again.');
		}
    });

    $(document).on("click",".directorist-res-btns #js-dlm-listings",function() {
    	$('.directorist-map-search-inner').css('display', 'block');
    });

    $(document).on("click",".directorist-res-btns #js-dlm-map",function() {
    	$('.directorist-map-search-inner').css('display', 'none');
    });

    function direoAjaxAddUserItem($this, type = 'POST', dataType = 'json', data = {}) {
	    $.ajax({
	        type: type,
	        dataType: dataType,
	        url: directorist.ajaxurl,
	        data: data,
	        beforeSend: function() {
	            userItemFormSubmit.prop('disabled', true);
	            userItemFormSubmit.text('Processing...');
	        },
	        success: function(response) {
	            userItemFormSubmit.prop('disabled', false);
	            userItemFormSubmit.text('Add Product');
	            if(response.status == 'success') {
	            	dieroNotice.addClass(response.status);
	            	dieroNotice.text(response.msg);
	            	$this.trigger('reset');
	            	setTimeout(function() {
				        userItemModal.modal('hide');
				        location.reload();
				    }, 2000);
	            } else {
	            	dieroNotice.addClass(response.status);
	            	dieroNotice.text(response.msg);
	            }
	        },
	        error: function(jqXHR, textStatus, errorThrown){
	            console.log(jqXHR + " :: " + textStatus + " :: " + errorThrown);
	        }
	    });
	}

	$(document).on("click","#upload-featured-image",function() {
    	var file_frame = '', attachmentIds = [];
        if ( file_frame ) { file_frame.open(); return; }

        file_frame = wp.media.frames.file_frame = wp.media({
            title: $( this ).data( 'Select Item Images' ),
            button: {
                text: $( this ).data( 'Select Image' ),
            },
            library: {
		       	type: ['image']
		    },
            multiple: false,
        });

        file_frame.on( 'select', function() {
            attachments = file_frame.state().get('selection').toJSON();
            $.each(attachments, function( index, value ) {
			  	attachmentIds.push(value.id);
			});

			$('input[name="featured_image"]').val(attachmentIds);
			$('#add-edit-item #upload-featured-image').html('Featured Image Selected');
        });

        file_frame.open();
   	});

   	$(document).on("click","#upload-gallery-images",function() {
    	var file_frame = '', attachmentIds = [];
        if ( file_frame ) { file_frame.open(); return; }

        file_frame = wp.media.frames.file_frame = wp.media({
            title: $( this ).data( 'Select Item Images' ),
            button: {
                text: $( this ).data( 'Select Image' ),
            },
            library: {
		       	type: ['image']
		    },
            multiple: true,
        });

        file_frame.on( 'select', function() {
            attachments = file_frame.state().get('selection').toJSON();
            $.each(attachments, function( index, value ) {
			  	attachmentIds.push(value.id);
			});

			$('input[name="gallery_images"]').val(attachmentIds);
			$('#add-edit-item #upload-gallery-images').html('Gallery Images Selected');
        });

        file_frame.open();
   	});

   	$(document).on("click",".image_wrapper_remove",function() {
		var attachmentId = $(this).data('id');
		var postid = $(this).data('postid');

		if(attachmentId && postid) {
	       	$.ajax({
		        type: 'POST',
		        dataType: 'json',
		        url: directorist.ajaxurl,
		        data: { action: 'delete_attachment', attachment: attachmentId, post: postid },
		        success: function(response) {
		        	if(response.status != 'failed') {
			            alert(response.msg);
		        	} else {
		        		alert(response.msg);
		        	}
		        	location.reload();
		        },
		        error: function(jqXHR, textStatus, errorThrown){
		            console.log(jqXHR + " :: " + textStatus + " :: " + errorThrown);
		        }
		    });
		} else {
			alert('Something went wrong. Please try again.');
		}
    });
});