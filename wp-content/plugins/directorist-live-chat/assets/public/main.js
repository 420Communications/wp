jQuery(document).ready(function ($) {





    // user chat

    // $(function () {

    //     var socket = io("https://aazztech.herokuapp.com/");

    //     var reciver = "";

    //     var sender = "";

    //     var user = dlc_js_api.user.data.user_login;

    //     socket.emit("user_connected", user);

    //     sender = user;

    //     reciver = $('input[name="userId"]').val();

    //     var chatListing_id = $('input[name="chatListing_id"]').val();

    //     var chatSubmit = $('#ChatForm');

    //     $( chatSubmit ).on("submit", function (e) {

    //       e.preventDefault();

    //       var message = document.getElementById("txt").value;

    //       socket.emit("send_message", {

    //         sender: sender,

    //         image: dlc_js_api.admin_avatar,

    //         reciver: reciver,

    //         message: message,

    //         listing: chatListing_id,

    //       });

    //       // push to sender screen

    //       var html = "";

    //       html += `<li class="directorist-user-chat">

    //       <div class="directorist-chat-content-wrap">

    //             <div class="directorist-chat-un-time">

    //                 <span class="directorist-chat-user-name">${sender}</span><span class="directorist-chat-time">${timeNow()}</span>

    //             </div>

    //             <div class="directorist-listing-chat-content">

    //                 <p>${message}</p>

    //             </div>

    //         </div>

    //       </li>`;

    //       document.getElementById("directorist-user-message-box").innerHTML += html;

    //       scrollToButtom();

    //     });

    //   });





      // admin chat

      $(function () {

        var socket = io("https://directorist-live-chat.herokuapp.com/");

        var reciver = "";

        var sender = "";

        var user = dlc_js_api.user.data.user_login;

        socket.emit("user_connected", user);

        sender = user;

        var ChatForm = $('#ChatForm');

        $(ChatForm).on("submit", function (e) {

          e.preventDefault();

          var reciver = $('.directorist-message-list-user__name').html();

          if(!reciver){

            reciver = $('input[name="userId"]').val();

          }

          var message = $('input[name^="chatMsg"]').val();

          var chatListing_id = $('input[name="chatListing_id"]').val();



          socket.emit("send_message", {

            sender: sender,

            image: dlc_js_api.admin_avatar,

            reciver: reciver,

            message: message,

            listing: chatListing_id,

          });



          $('.directorist-atbdp-no-chat').remove();

         

          var calss = $('.directorist-message-list-user__name').length ? 'directorist-admin-chat' : 'directorist-user-chat';

          var html = "";



          html += `<li class="${calss}">

          <div class="directorist-chat-content-wrap">

                <div class="directorist-chat-un-time">

                    <span class="directorist-chat-user-name">${sender}</span><span class="directorist-chat-time">${timeNow()}</span>

                </div>

                <div class="directorist-listing-chat-content">

                    <p>${message}</p>          

                </div>

            </div>

          </li>`;

          document.getElementById("directorist-user-message-box").innerHTML += html;

          scrollToButtom();

        });



        socket.on("new_message", function (data) {

            //console.log(data);

            var reciver = $('.directorist-message-list-user__name');

            var chatListing_id = $('input[name="chatListing_id"]').val();

            //console.log(data);

            var html = "";

            if(reciver.length){

                if( data.listing == chatListing_id ){

                    html += `<li class="">${data.image} 

                            <div class="directorist-chat-content-wrap">

                                <div class="directorist-chat-un-time">

                                    <span class="directorist-chat-user-name">${data.sender}</span><span class="directorist-chat-time">${timeNow()}</span>

                                </div>

                                <div class="directorist-listing-chat-content">

                                    <p>${data.message}</p>          

                                </div>

                            </div>

                            </li>`;

                    document.getElementById("directorist-user-message-box").innerHTML += html;

                    scrollToButtom();

                }

            }else{

                if(data.listing == chatListing_id){

                    html += `<li class="directorist-admin-chat">${data.image} 

                            <div class="directorist-chat-content-wrap">

                                <div class="directorist-chat-un-time">

                                    <span class="directorist-chat-user-name">${data.sender}</span><span class="directorist-chat-time">${timeNow()}</span>

                                </div>

                                <div class="directorist-listing-chat-content">

                                    <p>${data.message}</p>          

                                </div>

                            </div>

                            </li>`;

                    document.getElementById("directorist-user-message-box").innerHTML += html;

                    scrollToButtom();

                }

            }



        });

      });





    /**

     * @package Directorist

     * @type {*|jQuery|HTMLElement}

     * Submit single chat

     */

    var public_chat_form = $('input[name="public_chat_form"]').length;    

    var chatSubmit = $('#ChatForm');

    if (chatSubmit.length && public_chat_form) {

        $(chatSubmit).on('submit', function (e) {

            e.preventDefault();

            var chatMsg = $('input[name="chatMsg"]');

            var chatListing_id = $('input[name="chatListing_id"]').val();

            var chatAuthor_id = $('input[name="chatAuthor_id"]').val();

            var formData = new FormData();

            formData.append('chatMsg', chatMsg.val());

            formData.append('chatListing_id', chatListing_id);

            formData.append('chatAuthor_id', chatAuthor_id);

            formData.append('action', 'atbdp_live_chat');

            $.ajax({

                url: dlc_js_api.ajaxurl,

                method: 'POST',

                data: formData,

                crossDomain: true,

                processData: false,

                contentType: false,

                success: function (response) {

                    //console.log(response);

                    chatMsg.val('');

                   // getChats();

                },

                error: function (error) {

                    console.log(error);

                }



            });

        });

    }

    var ChatForm = $('#ChatForm');

    if (ChatForm.length && !public_chat_form) {

        $(ChatForm).on('submit', function (e) {

            e.preventDefault();

                var chatMsg = $('input[name="chatMsg"]');

                var chatListing_id = $('input[name="chatListing_id"]').val();

                var chatAuthor_id = $('.directorist-mas-chat-user-id').val();

                var formData = new FormData();

                formData.append('chatMsg', chatMsg.val());

                formData.append('chatListing_id', chatListing_id);

                formData.append('chatAuthor_id', chatAuthor_id);

                formData.append('action', 'atbdp_live_chat');

                $.ajax({

                    url: dlc_js_api.ajaxurl,

                    method: 'POST',

                    data: formData,

                    crossDomain: true,

                    processData: false,

                    contentType: false,

                    success: function (response) {
                        if(response.status = 'fail' && response.reason == 'limit_over') {
                            chatMsg.prop('disabled', true);
                            chatMsg.attr('placeholder', response.message);
                            $('.directorist-chat-submit').prop('disabled', true);
                            $('.directorist-chat-submit').addClass('disabled');
                        }

                        $('.diero-message-limit').html('(' + response.remaining_messages + ')');

                        chatMsg.val('');

                        //getAdminChats(response['listing_id'], response['chat_author_id']);   

                    },

                    error: function (error) {

                        //console.log(error);

                    }

    

                });            

        });

    }



    /**

     * @package Directorist

     * @type {*|jQuery|HTMLElement}

     * @fires load chat automatically

     */



    var chatContainer_user = $('#directorist-user-message-container');

    // // load chats

     var chatContainer_admin = $('#directorist-admin-message-container');

    // var ChatForm = $('#ChatForm');

    // if (ChatForm) {

    //     setInterval(function () {

    //         var listingId = $('input[name^="chatListing_id"]').val();

    //         var chatAuthor_id = $('.directorist-mas-chat-user-id').val();

    //          getAdminChats(listingId, chatAuthor_id)

    //     }, 2000)

    // }



    $('.directorist-start-chat-btn').on('click', function () {

        if (chatContainer_user.length > 0) {

            setInterval(getChats, 1000);

        }



    });





    /**

     * @package Directorist

     * @type {*|jQuery|HTMLElement}

     * @fires load chat when filtered by listing

     */



    if (chatContainer_admin.length > 0) {

        $('.directorist-chatted-listing:first-child').addClass('lc-active');



        $('body').on('click', '.directorist-chatted-listing', function (e) {

            e.preventDefault();

            var listingID = $(this).attr('data-listing-id');

            var listingTitle = $(this).attr('data-listing-title');

            var listingLink = $(this).attr('data-listing-link');

            var listingImgSrc = $(this).attr('data-listing-image-src');

            var chatAuthorId = $(this).attr('data-chat-user-id');

            var chatAuthorName = $(this).attr('data-chat-user-name');

            var chatAuthorImg = $(this).attr('data-chat-user-img');

            $('input[name^="chatListing_id"]').val(listingID);

            $('.directorist-message-list-item__title').empty().append(listingTitle);

            $('.directorist-message-list-user__name').empty().append(chatAuthorName);

            $('.directorist-message-list-user__img').empty().append(chatAuthorImg);

            $('.directorist-mas-chat-user-id').val(chatAuthorId);

            $('.directorist-message-list-item__img').empty().attr('src', listingImgSrc);

            $('.directorist-message-list-item__link').attr('href', listingLink);

            getAdminChats(listingID, chatAuthorId);

            $(this).siblings().removeClass("lc-active");

            $(this).addClass('lc-active');

        })

    }



    /**

     * @package Directorist

     * @type {*|jQuery|HTMLElement}

     * @fires load chat when filtered by user

     */

    if (chatContainer_admin.length > 0) {

        $('body').on('click', '.directorist-all-chated-author', function (e) {

            $('.directorist-all-chated-author').removeClass('directorist-active');

            e.preventDefault();



            var authorId   = $(this).attr('data-chatAuthor');

            var authorNmae = $(this).attr('data-chatAuthorName');

            var authorImg  = $('.chatAuthorImg img');

            var listingId  = $('input[name^="chatListing_id"]').val();



            $('.directorist-message-list-user__name').empty().append(authorNmae);

            $('.directorist-message-list-user__img').empty().append(authorImg);

            $('.directorist-mas-chat-user-id').val(authorId);

            getAdminChats(listingId, authorId)

            $('.directorist-all-chated-author').removeClass('directorist-active');

            $(this).addClass('directorist-active');

        })

    }





    /**

     * @package Directorist

     * @type {*|jQuery|HTMLElement}

     *

     */



    /*    $('body').on('click', '.mas-delete-chat', function (e) {

            e.preventDefault();

            var chatListing_id = $('input[name^="chatListing_id"]').val();

            var chatAuthor_id = $('input[name="chatAuthor_id"]').val();

            var formData = new FormData();

            formData.append('chatListing_id', chatListing_id);

            formData.append('chatAuthor_id',  chatAuthor_id);

            formData.append('action', 'atbdp_delete_chat_history');

            $.ajax({

                url: dlc_js_api.ajaxurl,

                method: 'POST',

                data: formData,

                crossDomain: true,

                processData: false,

                contentType: false,

                success: function (response) {

                   console.log(response)

                },

                error: function (error) {

                    console.log(error);

                }



            });

        });*/





    /**

     * @package Directorist

     * @type {*|jQuery|HTMLElement}

     * @fires push elemens to bottom

     */



    function scrollToButtom(){

            //stick scrollbar always at bottom

            if ($('#directorist-user-message-box').length > 0) {

                $('#directorist-user-message-box').scrollTop($('#directorist-user-message-box')[0].scrollHeight);

            }

            if ($('#directorist-user-message-box').length > 0) {

                $('#directorist-user-message-box').scrollTop($('#directorist-user-message-box')[0].scrollHeight);

            }

    }



    /**

     * @package Directorist

     * @type {*|jQuery|HTMLElement}

     * @returns current time

     */



    function timeNow() {

        return new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});

      }







    /**

     * @package Directorist

     * @type {*|jQuery|HTMLElement}

     * @return HTML as Ajax response for single listing

     */

    function getChats() {



        var chatListing_id = $('input[name="chatListing_id"]').val();

        var chatAuthor_id = $('input[name="chatAuthor_id"]').val();

        var formData = new FormData();

        formData.append('chatListing_id', chatListing_id);

        formData.append('chatAuthor_id', chatAuthor_id);

        formData.append('action', 'atbdp_get_chat_history');



        $.ajax({

            url: dlc_js_api.ajaxurl,

            method: 'POST',

            data: formData,

            crossDomain: true,

            processData: false,

            contentType: false,

            success: function (response) {

                $('#directorist-user-message-box').empty().append(response);

                scrollToButtom();

            },

            error: function (error) {

                console.log(error);

            }



        });

    }





    /**

     * @package Directorist

     * @type {*|jQuery|HTMLElement}

     * @return HTML as Ajax response in user dashboard

     */

    function getAdminChats(listingId, chatAuthorId) {



        var chatListing_id = listingId;

        var chatAuthor_id = $('input[name="chatAuthor_id"]').val();

        var formData = new FormData();

        formData.append('chatListing_id', chatListing_id);

        formData.append('chatAuthor_id', chatAuthorId ? chatAuthorId : chatAuthor_id);

        formData.append('action', 'atbdp_get_admin_chat_history');

        $.ajax({

            url: dlc_js_api.ajaxurl,

            method: 'POST',

            data: formData,

            crossDomain: true,

            processData: false,

            contentType: false,

            success: function (response) {

                $('#directorist-user-message-box').empty().append(response);

                scrollToButtom();

            },

            error: function (error) {

                console.log(error);

            }



        });

    }



    $('.directorist-start-chat-btn').on('click', function (e) {

        e.preventDefault();

        var spText = $(".directorist-start-chat-btn span");

        $(".directorist-client-chat-content-area").toggleClass("atbd-show");

        spText.toggleClass("active");

        spText.hasClass("active") ? spText.text(dlc_js_api.hide_chat_button) : spText.text(dlc_js_api.show_chat_button);

        $(this).toggleClass("active");

        $(this).parents(".directorist-chat-wrapper").toggleClass("active");

        scrollToButtom();

    });



    if ($('#directorist-user-message-box').length > 0) {

        $('#directorist-user-message-box').scrollTop($('#directorist-user-message-box')[0].scrollHeight);

    }

    if ($('#directorist-user-message-box').length > 0) {

        $('#directorist-user-message-box').scrollTop($('#directorist-user-message-box')[0].scrollHeight);

    }



    //show login alert

    var dcln = $('.dcl_login_notice');

    dcln.hide();

    $('.dcl_login_alert ').on('click', function (e) {

        e.preventDefault();

        dcln.slideDown();

    });



    /* tab functionality */

    (function () {

        pureScriptTab2 = (selector1) => {

            var selector = document.querySelectorAll(selector1);

            selector.forEach((el, index) => {

                a = el.querySelectorAll('.atbdlc_tn_link');





                a.forEach((element, index) => {



                    element.style.cursor = 'pointer';

                    element.addEventListener('click', (event) => {

                        event.preventDefault();

                        event.stopPropagation();



                        var ul = event.target.closest('.atbdlc_tab_nav'),

                            main = ul.nextElementSibling,

                            item_a = ul.querySelectorAll('.atbdlc_tn_link'),

                            section = main.querySelectorAll('.directorist-message-tabs__inner');



                        item_a.forEach((ela, ind) => {

                            ela.classList.remove('lc_tabItemActive');

                        });

                        event.target.classList.add('lc_tabItemActive');





                        section.forEach((element1, index) => {

                            //console.log(element1);

                            element1.classList.remove('directorist-lc-tab-content-active');

                        });

                        var target = event.target.target;

                        document.getElementById(target).classList.add('directorist-lc-tab-content-active');

                    });

                });

            });

        };

    })();

    pureScriptTab2('.directorist-message-tabs');

});