@extends('layouts.admin')
@section('content')
<?php
$role_id = Auth::user()->role_id;
$role_1 = '';
$role_2 = '';
if ($role_id == 1) {
    $role_1 = "Parlours";
    $role_2 = "Users";
}

?>
<div class="page-content">
    <div class="container-fluid">
        <div class="d-lg-flex">
            <div class="chat-leftsidebar mr-lg-4">
                <div class="">
                    <div class="py-4 border-bottom">
                        <div class="media">
                            <div class="align-self-center mr-3">
                                <img src="{{ asset(Storage::url(Auth::user()->profile))}}" class="avatar-xs rounded-circle" alt="{{Auth::user()->name}}">
                            </div>
                            <div class="media-body">
                                <h5 class="font-size-15 mt-0 mb-1">{{Auth::user()->name}}</h5>
                                <p class="text-muted mb-0"><i class="mdi mdi-circle text-success align-middle mr-1"></i> Active</p>
                            </div>
                        </div>
                    </div>
                    <div class="chat-leftsidebar-nav">
                        <ul class="nav nav-pills nav-justified">
                            <li class="nav-item">
                                <a href="#barber" data-toggle="tab" aria-expanded="true" class="nav-link active">
                                    <i class="bx bx-chat font-size-20 d-sm-none"></i>
                                    <span class="d-none d-sm-block">{{$role_1}}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#user" data-toggle="tab" aria-expanded="false" class="nav-link">
                                    <i class="bx bx-group font-size-20 d-sm-none"></i>
                                    <span class="d-none d-sm-block">{{$role_2}}</span>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content py-4">
                            <div class="tab-pane show active" id="barber">

                            </div>
                            <div class="tab-pane show " id="user">
                                <ul class="list-unstyled " data-simplebar style="max-height: 410px;">
                                </ul>
                            </div>
                        </div>
                        <!-- <div class="inbox_chat">
                    {{-- Inbox will append here --}}
                </div> -->
                    </div>
                </div>
            </div>
            <div class="col user-chat">
                <div class="card">

                    <div class="p-4 border-bottom ">
                        <div id="serverMsg"></div>
                        <div class="row inbox-chat-box ">
                            <div class="col-md-4 col-9" style=" float: left;">

                                <h5 class="font-size-15 mb-1 receiver_name current_chat_box" data-group-id="" data-receiver-name="" data-receiver-id=""></h5>
                                {{-- <p class="text-muted mb-0"><i class="mdi mdi-circle text-success align-middle mr-1"></i> {{ trans('lanKey.chat_active_now') }}</p> --}}
                            </div>
                            <div class="col-md-8 col-3">

                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="chat-conversation p-3">
                            <ul class="list-unstyled  msg_history" style="height: 570px;overflow: auto;">
                                <div class="welcome_presenta_box" style="text-align: center;">
                                    <h3>Welcome Admin, Here you can chat</h3>
                                </div>

                            </ul>
                            <div class="typing_area_main">

                            </div>
                        </div>
                        <div class="p-3 chat-input-section" style="display: none">
                            <div class="row">
                                <div class="col">
                                    <div class="position-relative">
                                        <input type="text" class="form-control chat-input message" id="chatMessage" placeholder="Enter Message">
                                        <div class="chat-input-links">
                                            <ul class="list-inline mb-0">
                                                <li class="list-inline-item" id="emoji-button"><a href="#" title="Emoji"><i class="mdi mdi-emoticon-happy-outline"></i></a></li>
                                                {{-- <li class="list-inline-item" id="msg_send_btn" ><a href="#" data-toggle="tooltip" data-placement="top" title="Images"><i class="mdi mdi-file-image-outline"></i></a></li> --}}
                                                <li class="list-inline-item" id="uploadFile"><a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Add Files"><i class="mdi mdi-file-document-outline"></i></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-primary btn-rounded chat-send w-md waves-effect waves-light msg_send_btn"><span class="d-none d-sm-inline-block mr-2">Send</span> <i class="mdi mdi-send"></i></button>
                                </div>
                            </div>

                        </div>
                    </div>


                    <form method="post" enctype="multipart/form-data" id="uploadFileForm">
                        <input type="file" id="chat_image" name="chat_image" style="display: none;" onchange="previewFile()">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" class="auth_token" value="{{Auth::user()->createToken('glow')->accessToken}}">
<input type="hidden" class="login_user_id" value="{{Auth::user()->id}}">
<input type="hidden" class="socket_base_url" value="{{env('SOCKET_URL')}}">
<input type="hidden" class="socket_port" value="{{env('SOCKET_PORT')}}">
<input type="hidden" class="image_path" value="{{env('IMAGE_PATH')}}">


<!-- end row -->
@endsection
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.3.0/socket.io.js"></script>
<script type="text/javascript">
    var token = $(".auth_token").val();
    var login_user_id = $(".login_user_id").val();
    var image_path = $(".image_path").val();
    //var base_url = "http://localhost";

    var base_url = $(".socket_base_url").val();
    var port = $(".socket_port").val();
    var socket_port_url = base_url + ":" + port;
    var socket = io(socket_port_url);
    var typingTimer; //timer identifier
    var doneTypingInterval = 1000;
    var image_path = image_path;
    var current_role_id = +'{{$role_id}}';
    var curr_receiver_id;

    $(document).ready(function() {
        // socket.on('connect', function () {
        //     socket.emit('join', {email: user@example.com});
        // });

        /* Emoji picker start*/
        const button = document.querySelector('#emoji-button');

        const picker = new EmojiButton();
        picker.on('emoji', emoji => {
            document.querySelector('#chatMessage').value += emoji;
        });
        button.addEventListener('click', () => {
            picker.togglePicker(button);
        });
        /* Emoji picker end*/

        socket.emit("getInbox", {
            token: token
        });

        //socket.emit("setActive", {token:token});
        socket.on("setInbox", function(data) {
            if (token == data.token) {
                if (current_role_id == 1) {
                    data.barbers = data.barbers;
                    data.users = data.users;
                }

                data.barbers.sort(SortByUnreadCount);
                var barbers = '';
                barbers = '<ul class="list-unstyled chat-list"  data-simplebar style="height: calc(100vh - 150px);">';
                $.each(data.barbers, function(index, value) {
                    barbers += '                 <li class="chat_list " id="user_' + value.id + '" data-receiver-name="' + value.name + '" data-group-id="' + value.group_id + '" data-receiver-id=' + value.id + '>';
                    barbers += '                     <a href="javascript:void(0);">';
                    barbers += '                         <div class="media">';
                    barbers += '                             <div class="align-self-center mr-3">';
                    var profile_pic = "no-image.jpg";
                    if (value.profile != "" && value.profile != null) {
                        profile = value.profile;
                    }
                    barbers += '                                <img  src="' + image_path + profile + '" class="rounded-circle avatar-xs" alt="">';
                    barbers += '                             </div>';
                    barbers += '                             <div class="media-body overflow-hidden">';
                    barbers += '                                  <h5 class="text-truncate font-size-14 mb-1">' + value.name + '</h5>';
                    if (value.last_active_time == "1") {
                        barbers += '<i class="mdi mdi-circle text-success align-middle mr-1"></i> </span>Active Now</span>';
                    } else {
                        barbers += '</span>' + value.last_active_time + '</span>';
                    }
                    barbers += '                              </div>';
                    if (value.unread_count != 0) {
                        barbers += '                              <div class="badge badge-pill badge-primary font-size-12 px-2 unread_count">' + value.unread_count + '</div>';
                    }
                    barbers += '                          </div>';
                    barbers += '                       </a>';

                    barbers += '                   </li>';
                });
                barbers += '</ul>';


                data.users.sort(SortByUnreadCount);
                //SortByUnreadCount()
                var users = '';
                users = '<ul class="list-unstyled chat-list"  data-simplebar style="height: calc(100vh - 150px);">';

                $.each(data.users, function(index, value) {
                    users += '                <li class="chat_list" id="user_' + value.id + '" data-receiver-name="' + value.name + '" data-group-id="' + value.group_id + '" data-receiver-id=' + value.id + '>';
                    users += '                    <a href="javascript:void(0);">';
                    users += '                        <div class="media align-items-center">';
                    users += '                             <div class="align-self-center mr-3">';
                    var profile = "no-image.jpg";
                    if (value.profile != "" && value.profile != null) {
                        profile = value.profile;
                    }
                    users += '                                <img src="' + image_path + '/' + profile + '" class="rounded-circle avatar-xs" alt="">';
                    users += '                            </div>';
                    users += '                            <div class="media-body">';
                    users += '                                <h5 class="font-size-14 mb-0">' + value.name + '</h5>';
                    if (value.last_active_time == "1") {
                        users += '<i class="mdi mdi-circle text-success align-middle mr-1"></i> </span>Active Now</span>';
                    } else {
                        users += '</span>' + value.last_active_time + '</span>';
                    }
                    users += '                            </div>';
                    if (value.unread_count != 0) {
                        users += '                              <div class="badge badge-pill badge-primary font-size-12 px-2 unread_count">' + value.unread_count + '</div>';
                    }
                    users += '                        </div>';
                    users += '                    </a>';

                    users += '                </li>';
                });
                users += '</ul>';


                $("#barber").html(barbers);
                $("#user").html(users);
            }
        });
        socket.on("setMessages", function(data) {

            if (token == data.token) {
                var receiver_name = $('.current_chat_box').attr('data-receiver-name');
                //console.log(data.group_id);
                var html = '';
                html += '<div>';
                if (typeof data.messageData != "undefined" &&
                    data.messageData != null &&
                    data.messageData.length != null &&
                    data.messageData.length > 0) {
                    $('.no_data_found').hide();
                    $.each(data.messageData, function(index, value) {
                        html += '     <li class="';
                        if (parseInt(login_user_id) == parseInt(value.user_id)) {
                            html += '                 right';
                        }
                        html += '     ">';

                        html += '        <div class="conversation-list">';
                        html += '            <div class="ctext-wrap">';
                        if (parseInt(login_user_id) != parseInt(value.user_id)) {
                            html += '                 <div class="conversation-name">' + value.user.name + '</div>';
                        }
                        if (parseInt(value.type) == 1) {
                            html += '                  <p>' + value.message + '</p>';
                        } else if (parseInt(value.type) == 2) {
                            html += '<a href="' + value.filename + '" target="_blank"><img src="' + value.filename + '" alt="" class="img-fluid" style="max-height:250px;width:auto;"></a>';
                        }
                        html += '                  <p class="chat-time mb-0">';
                        html += '                      <i class="fa fa-check align-middle mr-1" style="color:green;"></i>';
                        html += value.chat_time;
                        if (parseInt(login_user_id) == parseInt(value.user_id) && value.message != 'This message is deleted') {
                            html += '<a href="javascript:{}" title="Delete" class="btn btn-sm btn delete_message" style="color:#ff0231;" entry-id="' + value.id + '"><i class="fa fa-trash-alt"></i></a>';
                        }
                        html += '                   </p>';
                        html += '             </div>';
                        html += '         </div>';

                        html += '       </li>';
                    });
                    $('.chat_list.active .unread_count').hide();
                } else {
                    var chat_not = "Start your first message";
                    $('.no_data_found').show();
                    html += '<li class="no_data_found">' + chat_not + '</li>';
                }
                html += '</div>';

                // $('.chat_list').each(function(index,el) => {
                //     console.log($(this).attr('data-receiver-id'));
                // });
                $('.chat_list').each(function(index, value) {
                    var current = $(this);
                    //console.log(data.group_id);
                    var receiver_id = current.attr('data-receiver-id');
                    if (parseInt(receiver_id) == parseInt(data.receiver_id)) {
                        current.attr('data-group-id', data.group_id);
                    }
                });
                $(".message").val("");
                $(".msg_history").html(html);
                window.setTimeout(function() {
                    $(".msg_history").animate({
                            scrollTop: $(".msg_history").prop("scrollHeight")
                        },
                        300
                    );
                }, 400);
            }
        });
        socket.on("getCurrentMessage", function(data) {
            //socket.emit("getInbox", {token:token});
            // if(token == data.token){
            var group_id = parseInt($(".current_chat_box").attr('data-group-id'));
            var receiver_name = $('.current_chat_box').attr('data-receiver-name');
            if (data.result) {
                if (parseInt(data.result.group_id) == parseInt(group_id)) {
                    $('.no_data_found').hide();

                    var html = '';
                    html += '     <li class="';
                    if (parseInt(login_user_id) == parseInt(data.result.user_id)) {
                        html += '                 right';
                    } else {
                        var msg_id = data.result.id;
                        socket.emit('setReadMessage1', {
                            token: token,
                            msg_id: msg_id
                        });
                    }
                    html += '     ">';
                    html += '        <div class="conversation-list">';
                    html += '            <div class="ctext-wrap">';
                    if (parseInt(login_user_id) != parseInt(data.result.user_id)) {

                        html += '                 <div class="conversation-name">' + data.result.user.name + '</div>';
                    }
                    if (parseInt(data.result.type) == 1) {
                        html += '                  <p>' + data.result.message + '</p>';
                    } else if (parseInt(data.result.type) == 2) {
                        html += '<a href="' + data.result.filename + '" target="_blank"><img src="' + data.result.filename + '" alt="" class="img-fluid" style="max-height:250px;width:auto;"></a>';
                    }
                    html += '                  <p class="chat-time mb-0">';
                    html += '                      <i class="fa fa-check align-middle mr-1" style="color:green;"></i>';
                    html += data.result.chat_time;
                    if (parseInt(login_user_id) == parseInt(data.result.user_id)) {
                        html += '<a href="javascript:{}" title="Delete" class="btn btn-sm btn delete_message" style="color:#ff0231;" entry-id="' + data.result.id + '"><i class="fa fa-trash-alt"></i></a>';
                    }
                    html += '                   </p>';
                    html += '             </div>';
                    html += '         </div>';
                    html += '       </li>';

                    $(".msg_history").append(html);
                    window.setTimeout(function() {
                        $(".msg_history").animate({
                                scrollTop: $(".msg_history").prop("scrollHeight")
                            },
                            300
                        );
                    }, 100);
                }
            }
            //}
        });
        socket.on('typing', function(data) {
            var group_id = $('.chat_list.active').attr('data-group-id');
            if (parseInt(data.group_id) == parseInt(group_id)) {
                if (parseInt(login_user_id) != parseInt(data.user_id)) {
                    $('.typing_area_main').html(data.receiver_name + " is typing..");
                }
            }
        });
        socket.on('stop typing', function(data) {
            $('.typing_area_main').text("");
        });
        $('body').on('click', '.chat_list', function() {
            $('.chat-input-section').show();
            $(".msg_history").html("");
            $('.chat_list').removeClass('active');
            $(this).addClass('active');

            var receiver_name = $(this).attr('data-receiver-name');
            var group_id = parseInt($(this).attr('data-group-id'));
            var receiver_id = parseInt($(this).attr('data-receiver-id'));
            $('.current_chat_box').text(receiver_name);
            $('.current_chat_box').attr('data-group-id', group_id);
            $('.current_chat_box').attr('data-receiver-id', receiver_id);
            //messageData = {group_id: group_id,opp_user_id: opp_user_id,msg: message, type: 1,auth_token:auth_token};
            //alert(receiver_id);
            socket.emit('getMessages', {
                group_id: group_id,
                token: token
            });
            curr_receiver_id = receiver_id;
        });
        $("body").on("click", ".msg_send_btn", function() {
            var message = $(".message").val();
            var group_id = parseInt($(".current_chat_box").attr('data-group-id'));
            var receiver_id = parseInt($(".current_chat_box").attr('data-receiver-id'));

            if (message != "") {
                socket.emit('sendMessage', {
                    group_id: group_id,
                    message: message,
                    token: token,
                    type: 1,
                    sender_id: login_user_id,
                    receiver_id: receiver_id
                });
                //socket.emit("getInbox", {token:token,login_user_id : receiver_id});
                $('.message').val('');
            }
        });
        $("body").on('keypress', '.message', function(event) {
            var group_id = parseInt($(".current_chat_box").attr('data-group-id'))
            var receiver_name = $('.current_chat_box').attr('data-receiver-name');

            socket.emit("typing", {
                group_id: group_id,
                user_id: login_user_id,
                receiver_name: receiver_name,
                token: token
            });
            clearTimeout(typingTimer);
            typingTimer = setTimeout(doneTyping, doneTypingInterval);
            if (event.which == 13 && event.shiftKey) {
                clearTimeout(typingTimer);
            } else if (event.which == 13) {
                event.preventDefault();
                $(".msg_send_btn").click();
            }
        });
        $('body').on('click', '#uploadFile', function() {
            $('#chat_image').click();
        });

        $('body').on('click', '.delete_message', async function() {
            var msg_id = $(this).attr('entry-id');
            var receiver_id = $(this).attr('receiver_id');
            await socket.emit('deleteMessage', {
                message_id: msg_id,
                token: token,
            });
            setTimeout(() => {
                $('#user_' + curr_receiver_id).click();
            }, 500);
        });

        const interval = setInterval(function() {
            socket.emit("getInbox", {
                token: token
            });
        }, 5000);
        var user_temp_id = '{{$user_temp_id}}';
        if (parseInt(user_temp_id) == "") {
            setTimeout(() => {
                $('#barber .chat_list:first-child').click();
            }, 500);
        } else {
            setTimeout(() => {
                $('#user_' + user_temp_id).click();
            }, 500);
        }


        function doneTyping() {
            var group_id = $(".chat_list.active").attr("data-group-id");
            socket.emit('stop typing', {
                group_id
            });
        }

        function SortByUnreadCount(a, b) {
            var aName = parseInt(a.unread_count);
            var bName = parseInt(b.unread_count);
            return ((aName > bName) ? -1 : ((aName < bName) ? 1 : 0));
        }


    });

    function previewFile() {
        const file = document.querySelector('input[type=file]').files[0];
        const reader = new FileReader();
        var group_id = parseInt($(".current_chat_box").attr('data-group-id'));
        var receiver_id = parseInt($(".current_chat_box").attr('data-receiver-id'));

        reader.addEventListener("load", function() {
            socket.emit('sendFile', {
                group_id: group_id,
                media: reader.result,
                token: token,
                type: 2,
                receiver_id: receiver_id
            });

        }, false);

        if (file) {
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection