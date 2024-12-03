$(document).ready(function() {
    const firstUserInList = $('.message-user-item').find('.user-item')[0]
    if(firstUserInList) {
        const userId = firstUserInList.getAttribute('data-id')
        $.ajax({
            type: "GET",
            url: `/messages/${userId}`,
            dataType: 'json',
            beforeSend: function (){
                $('.messagesUl').html('<div class="loading-container"><img src="img/loading-buffer.gif" width="30" height="30"></div>');
            },
            success: function (response) {
                $('.messagesUl').html(response.messages)
            }
        });
    }
    let user = sessionStorage.getItem('selectedUser');
    if (user) {
        user = JSON.parse(user);

        // Añadir el usuario al contenedor de mensajes
        $('.message-user-item').append(`
            <div class="user-item" data-id="${user.userId}" data-sender-id="${user.senderId}">
                <img src="${user.photo}" alt="Photo" />
                <div>
                    <p id="usernameMessageItem">${user.username}</p>
                </div>
            </div>
        `);

        // Opcional: Limpiar los datos del sessionStorage
        sessionStorage.removeItem('selectedUser');
    }

    $('.user-item').on('click',function(){

        let userId = $(this).attr('data-id');
        let senderId = $(this).attr('data-sender-id')
        $('.message-item-user-info').find('img').remove();
        $('.message-item-user-info').find('p').remove();
        $('.message').remove();
        $('.message-item-user-info').attr('data-id',userId);
        $('.message-item-user-info').attr('data-sender-id',senderId);
        sessionStorage.setItem('data-sender-id',senderId);
        $('.message-item-user-info').append(
            `
            <img src="${$(this).find('img').attr('src')}"/>
            <p>${$(this).find('#usernameMessageItem').text()}</p>
            `
        );

        $.ajax({
            type: "GET",
            url: `/messages/${userId}`,
            dataType: 'json',
            beforeSend: function (){
                $('.messagesUl').html('<div class="loading-container"><img src="img/loading-buffer.gif" width="30" height="30"></div>');
            },
            success: function (response) {
                $('.messagesUl').html(response.messages)
            }
        });
    });

    $('.send-messages').find('img').on('click',function(){
        let input = $(this).parent().find('input');
        let value =  $(this).parent().find('input').val();
        let receiverId = $(this).parent().parent().find('.message-item-user-info').attr('data-id');

        $.ajax({
            type: "GET",
            url: `/message/new/${receiverId}/${value}`,
            success: function (response) {
                $('.message-item').find('ul').append(
                    `
                    <li class="message owner">
                        <p>${value}</p>
                    </li>
                    `
                )
                $(input).val("")
            }
        });
    })

    const supabaseUrl = 'https://fnofdrpcrthobxniqwkw.supabase.co';

    const cliente = supabase.createClient(supabaseUrl, supabaseKey);

    // Configuración para Realtime
    cliente
        .channel('mensajes')
        .on(
            'postgres_changes',
            { event: 'insert', schema: 'public', table: 'message' },
            (payload) => {
                let {sender_id, receiver_id, content} = payload.new;
                let logedId = sessionStorage.getItem('data-sender-id');

                if(receiver_id == logedId){
                    $('.message-item').find('ul').append(
                        `
                            <li class="message other">
                            <p>${content}</p>
                            </li>
                           `
                    )
                }

            })
        .subscribe();
})