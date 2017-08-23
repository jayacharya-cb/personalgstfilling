/**
Core script to handle the entire theme and core functions
**/
var QuickSidebar = function () {

    // Handles quick sidebar toggler
    var handleQuickSidebarToggler = function () {
        // quick sidebar toggler
        $('.dropdown-quick-sidebar-toggler a, .page-quick-sidebar-toggler, .quick-sidebar-toggler').click(function (e) {
            $('body').toggleClass('page-quick-sidebar-open'); 
        });
    };

    // Handles quick sidebar chats
    var handleQuickSidebarChat = function () {
        var wrapper = $('.page-quick-sidebar-wrapper');
        var wrapperChat = wrapper.find('.page-quick-sidebar-chat');

        var initChatSlimScroll = function () {
            var chatUsers = wrapper.find('.page-quick-sidebar-chat-users');
            var chatUsersHeight;

            chatUsersHeight = wrapper.height() - wrapper.find('.nav-tabs').outerHeight(true);

            // chat user list 
            App.destroySlimScroll(chatUsers);
            chatUsers.attr("data-height", chatUsersHeight);
            App.initSlimScroll(chatUsers);

            var chatMessages = wrapperChat.find('.page-quick-sidebar-chat-user-messages');
            var chatMessagesHeight = chatUsersHeight - wrapperChat.find('.page-quick-sidebar-chat-user-form').outerHeight(true);
            chatMessagesHeight = chatMessagesHeight - wrapperChat.find('.page-quick-sidebar-nav').outerHeight(true);

            // user chat messages 
            App.destroySlimScroll(chatMessages);
            chatMessages.attr("data-height", chatMessagesHeight);
            App.initSlimScroll(chatMessages);
        };

       // initChatSlimScroll();
        App.addResizeHandler(initChatSlimScroll); // reinitialize on window resize

        wrapper.find('.page-quick-sidebar-chat-users .media-list > .media').click(function () {
           
            var actual_content =$(".page-quick-sidebar-item");
            actual_content.removeClass('hidden');
            actual_content.addClass('hidden');

            var current_container=$(this).data("chat-container");
            current_container=$(current_container);
            current_container.removeClass('hidden');
            if(current_container.length==0)
            {
                // create New Container
                var chat_room_id=$(this).data('cr-id');
                var chat_user_id=$(this).data('id');
                alert($("#page-quick-sidebar-item"+chat_user_id).length);
                if($("#page-quick-sidebar-item"+chat_user_id).length==0){

                	var create_new_conversation='<div class="page-quick-sidebar-item" id="page-quick-sidebar-item'+chat_user_id+'">'+
                                        '<div class="page-quick-sidebar-chat-user">'+
                                        '<div class="page-quick-sidebar-nav">'+
                                            '<a href="javascript:;" class="page-quick-sidebar-back-to-list">'+
                                                '<i class="icon-arrow-left"></i>Back</a>'+
                                        '</div>'+
                                        '<div class="page-quick-sidebar-chat-user-messages">'+
                                          
                                        '</div>'+
                                        '<div class="page-quick-sidebar-chat-user-form">'+
                                            '<div class="input-group">'+
                                                '<input type="text" class="form-control" placeholder="Type a message here...">'+
                                                '<div class="input-group-btn">'+
                                                    '<button data-id="'+chat_user_id+'" data-cr-id="'+chat_room_id+'" type="button" class="btn green">'+
                                                        '<i class="icon-paper-clip"></i>'+
                                                    '</button>'+
                                                '</div>'+
                                            '</div>'+
                                        '</div>'+
                                        '</div>'+
                                        '</div>';
                	wrapperChat.append(create_new_conversation);
                }
                
                var current_container=$(this).data("chat-container");
                current_container=$(current_container);              
               handleQuickSidebarChat(); // handles quick sidebar's chats
               //initChatSlimScroll();
            }
            
            wrapperChat.addClass("page-quick-sidebar-content-item-shown");

           
        });

        wrapper.find('.page-quick-sidebar-chat-user .page-quick-sidebar-back-to-list').click(function () {            
            wrapperChat.removeClass("page-quick-sidebar-content-item-shown");
        });

        var handleChatMessagePost = function (e) {
            e.preventDefault();

            var chatContainer = wrapperChat.find(".page-quick-sidebar-chat-user-messages");
            var input = wrapperChat.find('.page-quick-sidebar-chat-user-form .form-control');

            var text = input.val();
            if (text.length === 0) {
                return;
            }

            var preparePost = function(dir, time, name, avatar, message) {                
                var tpl = '';
                tpl += '<div class="post '+ dir +'">';
                tpl += '<img class="avatar" alt="" src="'+avatar +'"/>';
                tpl += '<div class="message">';
                tpl += '<span class="arrow"></span>';
                tpl += '<a href="#" class="name">'+name+'</a>&nbsp;';
                tpl += '<span class="datetime">' + time + '</span>';
                tpl += '<span class="body">';
                tpl += message;
                tpl += '</span>';
                tpl += '</div>';
                tpl += '</div>';

                return tpl;
            };

            var user=User.user;
            var sent_to_id=$(this).data("id");
            var cr_id=$(this).data("cr-id");
            
            // handle post
            var time = new Date();
            var message = preparePost('out', (time.getHours() + ':' + time.getMinutes()),user.name, user.profile_picture_path, text);
            message = $(message);

            var message_send_to_user = $.grep(User.chat_users, function(e){ return e.id == sent_to_id; });
            $.ajax({
              url: "ajax_function/ajax_function_chat.php",
              method: "POST",
              data: { uid :user.id,u_type:user.type,content:text,cr_id:cr_id,op:"a_n_m" },
              dataType: "json",
              success:function(result){
                if(result.ack==1)
                {
                 // toastr.success(result.ack_msg,"Success!!");
                  //chatContainer.append(message);
                   chatContainer.slimScroll({
                    scrollTo: '1000000px'
                    });

                    input.val("");
                }
                else
                toastr.error(result.ack_msg,"Sorry!!");   
              },
              error:function(error){
                toastr.error("Internal Error!!","Sorry!!");
              }
            });
            /*if(message_send_to_user.length>=1)
            {
                message_send_to_user=message_send_to_user[0];


                chatContainer.append(message);

                chatContainer.slimScroll({
                    scrollTo: '1000000px'
                });

                input.val("");

                // simulate reply
                setTimeout(function(){
                    var time = new Date();
                    var message = preparePost('in', (time.getHours() + ':' + time.getMinutes()), message_send_to_user.name, message_send_to_user.profile_picture_path, 'Lorem ipsum doloriam nibh...');
                    message = $(message);
                    chatContainer.append(message);

                    chatContainer.slimScroll({
                        scrollTo: '1000000px'
                    });
                }, 3000);
            }*/
            
        };

        wrapperChat.find('.page-quick-sidebar-chat-user-form .btn').click(handleChatMessagePost);
        wrapperChat.find('.page-quick-sidebar-chat-user-form .form-control').keypress(function (e) {
            if (e.which == 13) {
                handleChatMessagePost(e);
                return false;
            }
        });
    };

    // Handles quick sidebar tasks
    var handleQuickSidebarAlerts = function () {
        var wrapper = $('.page-quick-sidebar-wrapper');

        var initAlertsSlimScroll = function () {
            var alertList = wrapper.find('.page-quick-sidebar-alerts-list');
            var alertListHeight;

            alertListHeight = wrapper.height() - wrapper.find('.nav-justified > .nav-tabs').outerHeight();

            // alerts list 
            App.destroySlimScroll(alertList);
            alertList.attr("data-height", alertListHeight);
            App.initSlimScroll(alertList);
        };

        initAlertsSlimScroll();
        App.addResizeHandler(initAlertsSlimScroll); // reinitialize on window resize
    };

    // Handles quick sidebar settings
    var handleQuickSidebarSettings = function () {
        var wrapper = $('.page-quick-sidebar-wrapper');

        var initSettingsSlimScroll = function () {
            var settingsList = wrapper.find('.page-quick-sidebar-settings-list');
            var settingsListHeight;

            settingsListHeight = wrapper.height() - 80 - wrapper.find('.nav-justified > .nav-tabs').outerHeight();
           
            // alerts list 
            App.destroySlimScroll(settingsList);
            settingsList.attr("data-height", settingsListHeight);
            App.initSlimScroll(settingsList);
        };

        initSettingsSlimScroll();
        App.addResizeHandler(initSettingsSlimScroll); // reinitialize on window resize
    };

    return {

        init: function () {
            //layout handlers
            handleQuickSidebarToggler(); // handles quick sidebar's toggler
            handleQuickSidebarChat(); // handles quick sidebar's chats
            handleQuickSidebarAlerts(); // handles quick sidebar's alerts
            handleQuickSidebarSettings(); // handles quick sidebar's setting
        }
    };

}();

if (App.isAngularJsApp() === false) { 
    jQuery(document).ready(function() {    
       QuickSidebar.init(); // init metronic core componets
    });
}